<?php

/**
 * Meta box functions
 *
 * @package     QUADS
 * @subpackage  Functions/Meta Boxes
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.4
 */

function quads_load_meta_box () {
	new Quads_Meta_Box();
}

add_action( 'load-post.php', 		'quads_load_meta_box' );
add_action( 'load-post-new.php', 	'quads_load_meta_box' );

/**
 * Config for all quads related options
 */
class Quads_Meta_Box {
	private $config_key;
	private $meta_key_visibility;

	public function __construct() {
		$this->config_key 			= 'quads_config';
		$this->meta_key_visibility 	= '_quads_config_visibility';

		$this->setup_hooks();
	}

	public function setup_hooks() {
		add_action( 'add_meta_boxes', 	array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', 		array( $this, 'save' ) );

		// @todo move to taget location
		add_filter( 'content_edit_pre', '' ); // usuwa z wp edytora jak jest config visibility w bazie
		add_filter( 'content_save_pre', '' ); // usuwa fizycznie z contentu quicktags na save-ie, jak jest config visibility w bazie
	}

	public function get_allowed_post_types () {
		return apply_filters( 'quads_meta_box_post_types', array( 'post', 'page' ) );
	}

	public function add_meta_boxes ( $post_type ) {
		if ( !in_array( $post_type, $this->get_allowed_post_types() ) ) {
			return;
		}

		add_meta_box(
			'quads_meta_box', 		    // id
			__( 'Hide Ads', 'g1_simple_ads' ),       // title
			array( $this, 'render_meta_box' ),      // render function callback
			$post_type, 							// post_type
			'normal', 							    // context
			'default'  								// priority
		);
	}

	public function render_meta_box ( $post, $meta_box ) {
		// Secure the form with nonce field
		$nonce = wp_nonce_field(
			'quads_config',
			'quads_config_nonce',
			true,
			false
		);

		// process visibility options
		$visibility_value = get_post_meta( $post->ID, $this->meta_key_visibility, true );

		$visibility_value = wp_parse_args( $visibility_value, $this->get_qtags_from_content( $post->post_content ) );
		$qtags = $this->get_qtags_to_hide_ads();

		echo $nonce;

		foreach ( $qtags as $qtag_id => $qtag_label ) {
			$checkbox_name = sprintf( '%s[visibility][%s]', $this->config_key, $qtag_id );
			?>
			<p>
				<label>
					<input id="<?php echo esc_attr( $checkbox_name ) ?>" type="checkbox" name="<?php echo esc_attr( $checkbox_name ) ?>" value="1" <?php checked( isset( $visibility_value[ $qtag_id ] ), true ); ?> />
					<?php echo esc_html( $qtag_label ); ?>
				</label>
			</p>
			<?php
		}
	}

	public function get_qtags_to_hide_ads () {
		return apply_filters( 'quads_qtags_to_hide_ads', array(
			'NoAds' 		=> __( '<!--NoAds-->', 'quick-adsense-reloaded' ),
			'OffDef'		=> __( '<!--OffDef-->', 'quick-adsense-reloaded' ),
			'OffWidget'		=> __( '<!--OffWidget-->', 'quick-adsense-reloaded' ),
			'OffBegin'		=> __( '<!--OffBegin-->', 'quick-adsense-reloaded' ),
			'OffMiddle'		=> __( '<!--OffMiddle-->', 'quick-adsense-reloaded' ),
			'OffEnd'		=> __( '<!--OffEnd-->', 'quick-adsense-reloaded' ),
			'OffAfMore'		=> __( '<!--OffAfMore-->', 'quick-adsense-reloaded' ),
			'OffBfLastPara'	=> __( '<!--OffBfLastPara-->', 'quick-adsense-reloaded' ),
		) );
	}

	public function get_qtags_from_content ( $content ) {
		$found = array();
		$qtags = $this->get_qtags_to_hide_ads();

		// we can use preg_match instead of multiple calls of strpos(),
		// but strpos is much faster and for such a small array should still be faster than preg_match()
		foreach ( $qtags as $qtag_id => $qtag_label ) {
			if ( false !== strpos( $content, '<!--' . $qtag_id . '-->' ) ) {
				$found[ $qtag_id ] = 1;
			}
		}

		return $found;
	}

	public function save ( $post_id ) {
		// Don't save data automatically via autosave feature
		if ( $this->is_doing_autosave() ) {
			return $post_id;
		}

		// Don't save data when doing preview
		if ( $this->is_doing_preview() ) {
			return $post_id;
		}

		// Don't save data when using Quick Edit
		if ($this->is_inline_edit() ) {
			return $post_id;
		}

		$post_type = isset( $_POST['post_type'] ) ? $_POST['post_type'] : null;

		// Update options only if they are appliable
		if( !in_array( $post_type, $this->get_allowed_post_types() ) ) {
			return $post_id;
		}

		// Check permissions
		$post_type_obj = get_post_type_object( $post_type );
		if ( !current_user_can( $post_type_obj->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		// Verify nonce
		if ( !check_admin_referer( 'quads_config', 'quads_config_nonce' ) ) {
			wp_die( __( 'Nonce incorrect!', 'g1_simple_ads' ) );
		}

		if ( ! isset( $_POST[ $this->config_key ] ) ) {
			return;
		}

		$config = $_POST[ $this->config_key ];

		// process visibility config
		// store it in separate meta key
		if ( isset( $config['visibility'] ) ) {
			$checked_qtags = array();
			$allowed_fields = $this->get_qtags_to_hide_ads();

			foreach ( $allowed_fields as $qtag_id => $qtag_label ) {
				if ( isset( $config['visibility'][ $qtag_id ] ) ) {
					$checked_qtags[ $qtag_id ] = 1;
				}
			}

			// strip all forbidden values
			foreach ( $config['visibility'] as $qtag_id => $qtag_label ) {
				if ( isset( $allowed_fields[ $qtag_id ] ) ) {
					$checked_qtags[ $qtag_id ] = 1;
				}
			}

			update_post_meta( $post_id, $this->meta_key_visibility, $checked_qtags );
		}
	}

	// @todo remove this code
//	protected function remove_unchecked_qtags_from_content ( $post_id, $allowed_qtags, $all_qtags ) {
//		$post = get_post( $post_id );
//		$post_content = $post->post_content;
//
//		foreach ( $all_qtags as $qtag_id => $qtag_label ) {
//			if ( ! isset( $allowed_qtags[ $qtag_id ] ) ) {
//				// remove qtag from content
//				$post_content = str_replace( '<!--'. $qtag_id .'-->', '', $post_content );
//			}
//		}
//
//		$new_data = array(
//			'ID'           => $post_id,
//			'post_content' => $post_content,
//		);
//
//		wp_update_post( $new_data );
//	}

	protected function is_doing_autosave() {
		return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ? true : false;
	}

	protected function is_inline_edit() {
		return isset( $_POST['_inline_edit'] ) ?  true : false;
	}

	protected function is_doing_preview () {
		return !empty( $_POST['wp-preview'] );
	}
}
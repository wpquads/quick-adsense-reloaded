<?php
/**
 * Meta box functions
 *
 * @package     QUADS
 * @subpackage  Functions/Meta Boxes
 * @copyright   Copyright (c) 2015, René Hermenau, Lukasz Wesolowski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.6
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

new Quads_Meta_Box();
/**
 * Ads options for a single post
 */
class Quads_Meta_Box {
	private $config_key;
	private $meta_key_visibility;
	public function __construct() {
		$this->config_key               = 'quads_config';
		$this->meta_key_visibility 	= '_quads_config_visibility';
		$this->setup_hooks();
	}
	public function setup_hooks() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}
	public function get_allowed_post_types () {
		return apply_filters( 'quads_meta_box_post_types', array( 'post', 'page' ) );
	}
	public function add_meta_boxes ( $post_type ) {
		if ( !in_array( $post_type, $this->get_allowed_post_types() ) ) {
			return;
		}
		add_meta_box(
                'quads_meta_box',                       // id
                __('WP QUADS - Hide Ads', 'quick-adsense-reloaded'),     // title
                array($this, 'render_meta_box'),        // render function callback
                $post_type,                             // post_type
                'advanced',                               // context
                'default'                               // priority
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
		// on first load, when post meta value is empty, we set defaults based on quicktags in content
		$visibility_value = wp_parse_args( $visibility_value, quads_get_quicktags_from_content( $post->post_content ) );
		$quicktags = quads_quicktag_list();
		echo $nonce; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: nonce field is escaped
		foreach ( $quicktags as $quicktag_id => $quicktag_label ) {
			$checkbox_name = sprintf( '%s[visibility][%s]', $this->config_key, $quicktag_id );
			?>
			<p>
                                <?php //echo 'id: ' . $quicktag_id . ' vid:' .$visibility_value[$quicktag_id];?>
				<label>
					<input id="<?php echo esc_attr( $checkbox_name ) ?>" type="checkbox" name="<?php echo esc_attr( $checkbox_name ) ?>" value="1" <?php checked( isset( $visibility_value[ $quicktag_id ] ), true ); ?> />
					<?php echo esc_html( $quicktag_label ); ?>
				</label>
			</p>
			<?php
		}
	}
        public function save($post_id) {
        // Don't save data automatically via autosave feature
        if ($this->is_doing_autosave()) {
            return $post_id;
        }
        // Don't save data when doing preview
        if ($this->is_doing_preview()) {
            return $post_id;
        }
        // Don't save data when using Quick Edit
        if ($this->is_inline_edit()) {
            return $post_id;
        }
        // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: Nonce verification is done below
        $post_type = isset($_POST['post_type']) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : null;
        // Update options only if they are appliable
        if (!in_array($post_type, $this->get_allowed_post_types())) {
            return $post_id;
        }
        // Check permissions
        $post_type_obj = get_post_type_object($post_type);
        if (!current_user_can($post_type_obj->cap->edit_post, $post_id)) {
            return $post_id;
        }
        // Verify nonce
        if (!empty($_POST['quads_config']) && !check_admin_referer('quads_config', 'quads_config_nonce')) {
            wp_die( esc_html__( 'Nonce incorrect!', 'quick-adsense-reloaded' ) );
        }
        $config = isset($_POST[$this->config_key]) ? $_POST[$this->config_key] : array();
        $visibility_config = isset($config['visibility']) ? $config['visibility'] : array();
        // process visibility config
        // store it in separate meta key
        $checked_qtags = array();
        $allowed_fields = quads_quicktag_list();
        foreach ($allowed_fields as $qtag_id => $qtag_label) {
            if (isset($visibility_config[$qtag_id])) {
                $checked_qtags[$qtag_id] = 1;
            }
        }
        // strip all forbidden values
        foreach ($visibility_config as $qtag_id => $qtag_label) {
            if (isset($allowed_fields[$qtag_id])) {
                $checked_qtags[$qtag_id] = 1;
            }
        }
        update_post_meta($post_id, $this->meta_key_visibility, $checked_qtags);
    }

    protected function is_doing_autosave() {
		return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ? true : false;
	}
	protected function is_inline_edit() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: This is a dependant function  
		return isset( $_POST['_inline_edit'] ) ?  true : false;
	}
	protected function is_doing_preview () {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: This is a dependant function 
		return !empty( $_POST['wp-preview'] );
	}
}
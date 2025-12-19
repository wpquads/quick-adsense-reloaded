<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Widget Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Widgets
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

function quads_register_new_widget(){
    register_widget('quads_ads_widget');
}

add_action( 'widgets_init', 'quads_register_new_widget', 1 );

class Quads_Ads_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
              
		parent::__construct(
			'quads_ads_widget', // Base ID
			esc_html__( 'WP QUADS ADS', 'quick-adsense-reloaded' ), // Name
			array( 'description' => esc_html__( 'Widget to display Ads', 'quick-adsense-reloaded' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
        global $quads_options;        
        
        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        $cont = quads_post_settings_to_quicktags( get_the_content() );
        $ads = isset($quads_options['ads'][$instance['ads']])?$quads_options['ads'][$instance['ads']]:'';
		$ads_fixed = isset($instance['ads_fixed'])?$instance['ads_fixed']:'';

		if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_is_visibility_on($ads)) {
            echo wp_kses_post($args['before_widget']);
            
            $code = quads_render_ad( $instance['ads'], $ads['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if($ads_fixed) {
				$this->fixed_widget();
	            echo '<div id="quads-ad' . esc_attr($instance['ads']) . '_widget" class="quads_widget_fixed">';
            }else{
	            echo '<div id="quads-ad' . esc_attr($instance['ads']) . '_widget">';
            }
            echo wp_kses_post($code);
            echo '</div>';
            echo wp_kses_post($args['after_widget']);	
        }
        	
	}
	public function fixed_widget(){
            $suffix = ( quadsIsDebugMode() ) ? '' : '.min'; 
            wp_enqueue_script( 'quads-fixed-widget', QUADS_PLUGIN_URL . 'assets/js/fixed_widget' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
        
        global $quads_options;

		if ( isset( $instance['ads_fixed'] ) ) {
			$check_fixed = $instance['ads_fixed'];
		} else {
			$check_fixed = 0;
		}
        
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Ad title or group title', 'quick-adsense-reloaded' );
        $ads = ! empty( $instance['ads'] ) ? $instance['ads'] : esc_html__( 'ads list to be display', 'quick-adsense-reloaded' );?>

        <p><label for="<?php echo esc_attr( $this->get_field_id( 'ads' ) ); ?>"><?php esc_attr_e( 'Ads:', 'quick-adsense-reloaded' ); ?></label><?php 
		
		if(isset($quads_options['ads'])){

            echo '<select id="'.esc_attr( $this->get_field_id( 'ads' )).'" name="'.esc_attr( $this->get_field_name( 'ads' )).'">';

            foreach($quads_options['ads'] as $key => $ad){
				$title = $key;
				if( is_array($ad) && array_key_exists( 'label', $ad ) ){ 
					$title = $ad['label'];
				}
             echo '<option '. esc_attr(selected( $ads, $key, false)).' value="'.esc_attr($key).'">'.esc_html( $title ).'</option>';
            }

            echo '</select>';

        }

		?></p><p><?php
		echo '<input type="checkbox" name="' . esc_attr( $this->get_field_name( 'ads_fixed' ) ) . '" value="1" ' . checked( $check_fixed, 1, false ) . '/>' . PHP_EOL;

            echo '<label for="'.esc_attr( $this->get_field_name( 'ads_fixed' )).'" >Fixed widget</label>'; ?></p><?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
        $instance['ads'] = ( ! empty( $new_instance['ads'] ) ) ? sanitize_text_field( $new_instance['ads'] ) : '';
		$instance['ads_fixed'] = ( ! empty( $new_instance['ads_fixed'] ) ) ? sanitize_text_field( $new_instance['ads_fixed'] ) : '';
		return $instance;
	}

} // class quads_Ads_Widget
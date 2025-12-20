<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Widget Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Widgets
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.1
 */

function quads_get_inline_widget_ad_style( $id ) {
    global $quads_options;

    if( empty($id) ) {
        return '';
    }

    // Basic style
    $styleArray = array(
        'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
        'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
        'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
        'float:none;margin:%1$dpx;');
    
    // Alignment
    $adsalign = ( int )$quads_options['ads']['ad' . esc_attr($id) . '_widget']['align'];
    
    // Margin
    $adsmargin = '0';
    $padding = 'padding:';
     if(isset( $quads_options['ads']['ad' . esc_attr($id) . '_widget']['margin'] )){ 
       $adsmargin = $quads_options['ads']['ad' . esc_attr($id) . '_widget']['margin'] ;
        $margin = sprintf( $styleArray[$adsalign], $adsmargin );
     }else{
          $margin = 'margin:';
          if(isset( $quads_options['ads']['ad' . esc_attr($id) . '_widget']['margintop'] )){ 
           $margin .=$quads_options['ads']['ad' . esc_attr($id) . '_widget']['margintop'] ."px " ;
         }else{
            $margin .= "0px ";
         }
          if(isset( $quads_options['ads']['ad' . esc_attr($id) . '_widget']['marginright'] )){ 
           $margin .=$quads_options['ads']['ad' . esc_attr($id) . '_widget']['marginright'] ."px " ;
         }else{
            $margin .= "0px ";
         }
          if(isset( $quads_options['ads']['ad' . esc_attr($id) . '_widget']['marginbottom'] )){ 
          $margin .=$quads_options['ads']['ad' . esc_attr($id) . '_widget']['marginbottom'] ."px " ;
         }else{
            $margin .= "0px ";
         }
          if(isset( $quads_options['ads']['ad' . esc_attr($id) . '_widget']['marginleft'] )){ 
          $margin .=$quads_options['ads']['ad' . esc_attr($id) . '_widget']['marginleft'] ."px" ;
         }else{
            $margin .= "0px ";
         }
     }
        if(isset( $quads_options['ads']['ad' . esc_attr($id) . '_widget']['paddingtop'] )){ 
           $padding .=$quads_options['ads']['ad' . esc_attr($id) . '_widget']['paddingtop'] ."px " ;
         }else{
            $padding .= "0px ";
         }
          if(isset( $quads_options['ads']['ad' . esc_attr($id ). '_widget']['paddingright'] )){ 
           $padding .=$quads_options['ads']['ad' . esc_attr($id) . '_widget']['paddingright'] ."px " ;
         }else{
            $padding .= "0px ";
         }
          if(isset( $quads_options['ads']['ad' . esc_attr($id) . '_widget']['paddingbottom'] )){ 
          $padding .=$quads_options['ads']['ad' . esc_attr($id) . '_widget']['paddingbottom'] ."px " ;
         }else{
            $padding .= "0px ";
         }
          if(isset( $quads_options['ads']['ad' . esc_attr($id) . '_widget']['paddingleft'] )){ 
          $padding .=$quads_options['ads']['ad' . esc_attr($id) . '_widget']['paddingleft'] ."px" ;
         }else{
            $padding .= "0px ";
         }

        $css =$margin.'; '.$padding .'; ';

    // Do not create any inline style on AMP site
    $style =  !quads_is_amp_endpoint() ? apply_filters( 'quads_filter_widget_margins', $css, 'ad' . esc_attr($id) . '_widget') : '';
    
    return $style;
}

/**
 * Register Widgets
 * 
 * @return void
 * @since 0.9.2
 */

function quads_register_widgets() {
    global $quads_options;
    
    $amountWidgets = 10;
    for ( $i = 1; $i <= $amountWidgets; $i++ ) {
        if( !empty( $quads_options['ads']['ad' . $i . '_widget']['code']) || !empty( $quads_options['ads']['ad' . $i . '_widget']['g_data_ad_slot']) ) {
            register_widget( 'quads_widgets_' . $i );
        }
    }
}
add_action( 'widgets_init', 'quads_register_widgets', 1 );



class quads_widgets_1 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $this->adsID = '1';
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    /**
     * Create widget
     * 
     * @global array $quads_options
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        global $quads_options, $quads_ad_count_widget;
        
        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        
        extract( $args );

        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            $style = quads_get_inline_widget_ad_style($this->adsID);
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget" style="'.sanitize_html_class($style).'">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function 
            echo quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}

// class My_Widget1

class quads_widgets_2 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $this->adsID = '2';

        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    public function widget( $args, $instance ) {
        global $quads_options;
        
        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        extract( $args );

        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function
            echo  quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}

// class My_Widget2
class quads_widgets_3 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        $this->adsID = '3';

        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    public function widget( $args, $instance ) {
        global $quads_options;
        
        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        extract( $args );
        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function
            echo quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}

// class My_Widget3

class quads_widgets_4 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        $this->adsID = '4';
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    public function widget( $args, $instance ) {
        global $quads_options;
        
        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        
        extract( $args );
        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function
            echo  quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}

// class My_Widget4

class quads_widgets_5 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        $this->adsID = '5';
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    public function widget( $args, $instance ) {
        global $quads_options;

        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        extract( $args );
        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function
            echo  quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}

// class My_Widget5

class quads_widgets_6 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        $this->adsID = '6';
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    public function widget( $args, $instance ) {
        global $quads_options;

        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        
        extract( $args );
        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function
            echo  quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}

// class My_Widget6

class quads_widgets_7 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        $this->adsID = '7';
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    public function widget( $args, $instance ) {
        global $quads_options;

        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        
        extract( $args );
        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function
            echo  quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}

// class My_Widget7

class quads_widgets_8 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        $this->adsID = '8';
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    public function widget( $args, $instance ) {
        global $quads_options;
        
        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        
        extract( $args );
        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function
            echo  quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}

// class My_Widget8

class quads_widgets_9 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        $this->adsID = '9';
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    public function widget( $args, $instance ) {
        global $quads_options;
        
        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        
        extract( $args );
        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function
            echo  quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}

// class My_Widget9

class quads_widgets_10 extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        $this->adsID = '10';
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', esc_attr($this->adsID) );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', sanitize_title($this->AdsWidName) ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', sanitize_title($this->AdsWidName)) , // Name
                array(
                    'description' => esc_html__( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.sanitize_html_class($this->adsID).'_widget'
                    ) // Args
        );
    }

    public function widget( $args, $instance ) {
        global $quads_options;
        
        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        
        extract( $args );
        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage()) {

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo wp_kses($args['before_widget'], wp_kses_allowed_html('post'));
            echo '<div id="quads-ad' . esc_attr($this->adsID) . '_widget">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad function
            echo  quads_render_ad( 'ad' . esc_attr($this->adsID) . '_widget', $quads_options['ads']['ad' . esc_attr($this->adsID) . '_widget']['code'] );
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo wp_kses($args['after_widget'], wp_kses_allowed_html('post'));
        };
    }

}// class My_Widget10
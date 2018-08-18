<?php

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
    $adsalign = ( int )$quads_options['ads']['ad' . $id . '_widget']['align'];
    
    // Margin
    $adsmargin = isset( $quads_options['ads']['ad' . $id . '_widget']['margin'] ) ? $quads_options['ads']['ad' . $id . '_widget']['margin'] : '0';
    $margin = sprintf( $styleArray[$adsalign], $adsmargin );

    // Do not create any inline style on AMP site
    $style =  !quads_is_amp_endpoint() ? apply_filters( 'quads_filter_widget_margins', $margin, 'ad' . $id . '_widget') : '';
    
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
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        
        
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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
        global $quads_options, $ad_count_widget;
        
        // All widget ads are deactivated via post meta settings
        if( quads_check_meta_setting( 'NoAds' ) === '1' ){
            return false;
        }
        
        extract( $args );

        $cont = quads_post_settings_to_quicktags( get_the_content() );
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            //quads_set_ad_count_widget();
            //$codetxt = $quads_options['ad' . $this->adsID . '_widget'];
            $style = quads_get_inline_widget_ad_style($this->adsID);
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget" style="'.$style.'">';
            echo $code;
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo $args['after_widget'];
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

        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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
        //if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_ad_reach_max_count() ) {
        if( strpos( $cont, "<!--OffAds-->" ) === false && strpos( $cont, "<!--OffWidget-->" ) === false && quads_widget_ad_is_allowed() && !quads_hide_ad_widget_on_homepage() ) {

            //quads_set_ad_count_widget();
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            //if (array_key_exists('before_widget', $args))
            echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget">';
            echo $code;
            echo '</div>';
            //if (array_key_exists('after_widget', $args))
            echo $args['after_widget'];
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

        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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

            //quads_set_ad_count_widget();
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget">';
            echo $code;
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo $args['after_widget'];
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
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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

            //quads_set_ad_count_widget();
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget">';
            echo $code;
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo $args['after_widget'];
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
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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

            //quads_set_ad_count_widget();
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget">';
            echo $code;
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo $args['after_widget'];
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
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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

            //quads_set_ad_count_widget();
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget">';
            echo $code;
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo $args['after_widget'];
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
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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

            //quads_set_ad_count_widget();
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget">';
            echo $code;
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo $args['after_widget'];
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
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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

            //quads_set_ad_count_widget();
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget">';
            echo $code;
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo $args['after_widget'];
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
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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

            //quads_set_ad_count_widget();
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget">';
            echo $code;
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo $args['after_widget'];
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
        $this->AdsWidName = sprintf( 'AdsWidget%d (Quick Adsense Reloaded)', $this->adsID );
        $this->AdsWidID = sanitize_title( str_replace( array('(', ')'), '', $this->AdsWidName ) );
        parent::__construct(
                $this->AdsWidID, // Base ID
                str_replace('Quick Adsense Reloaded', 'WP QUADS', $this->AdsWidName) , // Name
                array(
                    'description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),
                    'classname' => 'quads-ad'.$this->adsID.'_widget'
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

            //quads_set_ad_count_widget();
            $code = quads_render_ad( 'ad' . $this->adsID . '_widget', $quads_options['ads']['ad' . $this->adsID . '_widget']['code'] );
            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if( array_key_exists( 'before_widget', $args ) )
                echo $args['before_widget'];
            echo '<div id="quads-ad' . $this->adsID . '_widget">';
            echo $code;
            echo '</div>';
            if( array_key_exists( 'after_widget', $args ) )
                echo $args['after_widget'];
        };
    }

}

// class My_Widget10
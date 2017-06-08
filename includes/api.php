<?php

/**
 * API Functions allow creation of custom ad positions
 *
 * @package     QUADS
 * @subpackage  Functions/API
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.9
 */

/*
 * Sample function for creating custom ad positions in your template files 
 * and option setting in WPQUADS admin settings.
 * 
 * Use the code below in your functions.php to register custom ad positions:
 
 
      <?php if (function_exists('quads_register_ad')){
            quads_register_ad( array('location' => 'header', 'description' => 'Header position') );
            quads_register_ad( array('location' => 'footer', 'description' => 'Footer position') );
            quads_register_ad( array('location' => 'custom', 'description' => 'Custom position') );
            }
       ?> 
  
  Use this in your template files whereever you want to show a custom WPQUADS ad position on your site
  
      <?php if (function_exists('quads_ad'))
            echo quads_ad( array('location' => 'header') );
       ?> 
 * 
 */


/**
 * Return ad locations HTML based on new API in general settings
 *
 * @param $html
 * @return string   Locations HTML
 */
function quads_render_ad_locations( $html ) {
    global $quads_options, $_quads_registered_ad_locations, $quads;

    if( isset( $_quads_registered_ad_locations ) && is_array( $_quads_registered_ad_locations ) ) {
        foreach ( $_quads_registered_ad_locations as $location => $location_args ) {

            $location_settings = quads_get_ad_location_settings( $location );

            $html .= $quads->html->checkbox( array(
                'name' => 'quads_settings[location_settings][' . $location . '][status]',
                'current' => !empty( $location_settings['status'] ) ? $location_settings['status'] : null,
                'class' => 'quads-checkbox quads-assign'
                    ) );
            $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';

            $html .= $quads->html->select( array(
                'options' => quads_get_ads(),
                'name' => 'quads_settings[location_settings][' . $location . '][ad]',
                'selected' => !empty( $location_settings['ad'] ) ? $location_settings['ad'] : null,
                'show_option_all' => false,
                'show_option_none' => false
                    ) );
            $html .= ' ' . $location_args['description'] . '</br>';
        }
    }

    return $html;
}

/**
 * This hook should be removed and the hook function should replace entire "quads_ad_position_callback" function.
 */
add_filter( 'quads_ad_position_callback', 'quads_render_ad_locations' );


/**
 * Register an ad position.
 *
 * @param array $args   Location settings
 */
function quads_register_ad( $args ) {
    global $_quads_registered_ad_locations;
    $defaults = array(
        'location'      => '',
        'description'   => ''
    );
    $args = wp_parse_args( $args, $defaults );
    if ( empty( $args['location'] ) ) {
        return;
    }
    if ( ! isset( $_quads_registered_ad_locations  ) ) {
        $_quads_registered_ad_locations  = array();
    }
    $_quads_registered_ad_locations [ $args['location'] ] = $args;
}
/**
 * Whether a registered ad location has an ad assigned to it.
 *
 * @param string $location      Location id
 * @return bool
 */
function quads_has_ad( $location ) {
    global $quads_options;
    $result = false;

    $location_settings = quads_get_ad_location_settings( $location );
    
    if ( $location_settings['status'] && ! empty( $location_settings['ad'] ) ) {
      $result = true;
    }
    
    if ( ! quads_ad_is_allowed() || quads_ad_reach_max_count() ) {
        $result = false;
    }
    
    /**
     * Filter whether an ad is assigned to the specified location.
     */
    return apply_filters( 'quads_has_ad', $result, $location );
}
/**
 * Display a custom ad
 *
 * @param array $args       Displaying options
 * @return string|void      Ad code or none if echo set to true
 */
function quads_ad( $args ) {
    global $post;
    
    $defaults = array(
        'location'  => '',
        'echo'      => true,
    );
    $args = wp_parse_args( $args, $defaults );
    $code = '';
    
    // All ads are deactivated via post meta settings
    if( quads_check_meta_setting( 'NoAds' ) === '1' ){
        return false;
    }
    
    if ( quads_has_ad( $args['location'] ) ) {
        global $quads_options;
        
        quads_set_ad_count_custom(); // increase amount of Custom ads
        
        $location_settings = quads_get_ad_location_settings( $args['location'] ); 
        $code .= "\n".'<!-- WP QUADS Custom Ad v. ' . QUADS_VERSION .' -->'."\n";
        $code .= '<div class="quads-location quads-ad' .$location_settings['ad']. '" id="quads-ad' .$location_settings['ad']. '" style="'.  quads_get_inline_ad_style( $location_settings['ad'] ).'">'."\n";
        $code .= quads_render_ad( 'ad' . $location_settings['ad'], $quads_options['ads'][ 'ad' . $location_settings['ad'] ]['code'] );
        $code .= '</div>';
    }
    
    if ( $args['echo'] ) {
        echo $code;
    } else {
        return $code;
    }
}
/**
 * Return location settings.
 *
 * @param string $location      Location id
 * @return array
 */
function quads_get_ad_location_settings( $location ) {
    global $_quads_registered_ad_locations, $quads_options;

    $result = array(
        'status'    => false,
        'ad'        => '',
    );
    
    $location_registered     = isset( $_quads_registered_ad_locations ) && isset( $_quads_registered_ad_locations[ $location ] );
    $location_settings_exist = isset( $quads_options['location_settings'] ) && isset( $quads_options['location_settings'][ $location ] );
    
    if ( $location_registered && $location_settings_exist ) {
        $result = wp_parse_args( $quads_options['location_settings'][ $location ], $result );
    }
    
    return $result;
}

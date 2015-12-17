<?php

/**
 * shortcode functions
 *
 * @package     QUADS
 * @subpackage  Functions/shortcodes
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.4
 */


// add short codes
add_shortcode( 'quads_ad', 'quads_shortcode_display_ad', 1); // Important use a very early priority to be able to count total ads accurate


/**
 * shortcode to include ads in frontend
 *
 * @since 0.9.4
 * @param array $atts
 */
function quads_shortcode_display_ad($atts) {
    if ( !quads_ad_is_allowed() )
        return;
    
    $id = isset($atts['id']) ? (int) $atts['id'] : 0;
    return quads_get_ad($id);
}



/**
 * return ad content
 *
 * @since 0.9.4
 * @param int $id id of the ad
 * @return string
 */
function quads_get_ad($id = 0) {
    global $quads_options, $ad_count_shortcode;

    if ( quads_ad_reach_max_count() )
        return;
    
    if ( isset($quads_options['ad' . $id]['code']) ){
        // Count how often the shortcode is used - Important
        quads_set_ad_count_shortcode();
        return $quads_options['ad' . $id]['code'];
    }
}


/**
 * Set ad count and returns value for the_content
 * 
 * @global int $ad_count
 * @param type $ad_count
 * @return int amount of active ads in the_content
 */
function quads_set_ad_count_content(){
    global $ad_count_content;
       
    $ad_count_content++;
    return $ad_count_content;
}

/**
 * Set ad count and returns value for shortcodes
 * 
 * @global int $ShownAds
 * @param type $ShownAds
 * @return int amount of active ads in the_content
 */
function quads_set_ad_count_shortcode(){
    global $ad_count_shortcode;
       
    $ad_count_shortcode++;
    return $ad_count_shortcode;
}

/**
 * Check if the maximum amount of ads are reached
 * 
 * @global arr $quads_options settings
 * @var int amount of ads to activate 

 * @return bool true if max is reached
 */

function quads_ad_reach_max_count(){
    global $quads_options;
    if ( quads_get_total_ad_count() >= $quads_options['maxads'] ){
        return true;
    }
}


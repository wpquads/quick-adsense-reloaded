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
//@deprecated since 0.9.5
add_shortcode( 'quads_ad', 'quads_shortcode_display_ad', 1); // Important use a very early priority to be able to count total ads accurate
// new shortcode since 0.9.5
add_shortcode( 'quads', 'quads_shortcode_display_ad', 1); // Important use a very early priority to be able to count total ads accurate


/**
 * shortcode to include ads in frontend
 *
 * @since 0.9.4
 * @param array $atts
 */
function quads_shortcode_display_ad($atts) {
    if ( !quads_ad_is_allowed() )
        return;
    
    //return quads_check_meta_setting('NoAds');
    //return quads_check_meta_setting('NoAds');
    if ( quads_check_meta_setting('NoAds') === '1')
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

/**
 * Return value of quads meta box settings
 * 
 * @param type $id id of meta settings
 * @return mixed string | bool value if setting is active. False if there is no setting
 */
function quads_check_meta_setting($key){
    global $post;
    
    if ( !isset($post->ID ) )
    return false;
    
    $meta_key = '_quads_config_visibility';

    $value_arr = get_post_meta ( $post->ID, $meta_key, true );
    $value_key = isset($value_arr[$key]) ? $value_arr[$key] : null;
               
    if (!empty($value_key))
    return (string)$value_key;
    
    return false;
}


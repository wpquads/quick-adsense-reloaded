<?php
if ( ! defined( 'ABSPATH' ) ) exit;
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
function quads_shortcode_display_ad( $atts ) {
    global $quads_options,$quads_shortcode_ids,$quads_mode;

    // Display Condition is false and ignoreShortcodeCond is empty or not true
    if( !apply_filters('quads_show_ads',quads_ad_is_allowed()) && !isset($quads_options['ignoreShortcodeCond']) )
        return;


    //return quads_check_meta_setting('NoAds');
    if( quads_check_meta_setting( 'NoAds' ) === '1' ){
        return;
    }

    // The ad id
    $id = isset( $atts['id'] ) ? ( int ) $atts['id'] : 0;
    $ad_id = isset($quads_options['ads']['ad'.$id.'']) && $quads_options['ads']['ad'.$id.'']!==NULL ? (isset($quads_options['ads']['ad'.$id.'']['ad_id'])?$quads_options['ads']['ad'.$id.'']['ad_id']:NULL ): NULL ;

    if(isset($quads_mode) && $quads_mode == 'old'){
        if(isset($quads_options['ads']['ad'.$id]['phone']) || isset($quads_options['ads']['ad'.$id]['tablet_landscape']) || isset($quads_options['ads']['ad'.$id]['desktop'])){
            $get_device = function_exists('quads_check_my_device') ? quads_check_my_device() : '';
            if(isset($quads_options['ads']['ad'.$id][$get_device]) && $quads_options['ads']['ad'.$id][$get_device] == 1){
                return;
            }
        }
    }

    $arr = array(
        'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
        'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
        'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
        'float:none;margin:%1$dpx;');

    $adsalign = isset($quads_options['ads']['ad' . $id]['align']) ? $quads_options['ads']['ad' . $id]['align'] : 3; // default
    $adsmargin = isset( $quads_options['ads']['ad' . $id]['margin'] ) ? $quads_options['ads']['ad' . $id]['margin'] : '3'; // default
    $margin = sprintf( $arr[( int ) $adsalign], $adsmargin );

            $ad_checker = '';
            // Removing duplicate db calls by saving function output in variable passing 
            $quads_ad_var = quads_get_ad( $id ); 
            $ad_checker = $quads_ad_var ? $quads_ad_var : '' ;
            if ( isset($ad_checker) ) {
                if ( strpos( $ad_checker, 'quads-rotatorad')!==false) { 
                    $margin = 'text-align: center';
                }
            }
            else{
                $margin = sprintf( $arr[( int ) $adsalign], $adsmargin );
            }
            if(!empty($quads_shortcode_ids)){
                if(is_array($quads_shortcode_ids))
                {
                    $quads_shortcode_ids=array_push($quads_shortcode_ids,$ad_id);
                }
                else if($quads_shortcode_ids>0 && $quads_shortcode_ids!=$ad_id){
                    $quads_shortcode_ids=array($quads_shortcode_ids,$ad_id);
                }
                else{
                    $quads_shortcode_ids=array($ad_id);
                }
                
            }else{ 
                $quads_shortcode_ids=array($ad_id);
            }
    // Do not create any inline style on AMP site
    $style = !quads_is_amp_endpoint() ? apply_filters( 'quads_filter_margins', $margin, 'ad' . $id ) : '';
    if(function_exists('quads_hide_markup') && quads_hide_markup()) {
        $code = "\n" . '<div style="' . $style . '">' . "\n";
        $code .= do_shortcode( $quads_ad_var );
        $code .= '</div>' . "\n";
    }else{
        $idof_ad_id = '';
        $idof_ad_id = $ad_id;
        $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  Shortcode Ad -->' . "\n" .
            '<div class="quads-location quads-ad' . $idof_ad_id . '" id="quads-ad' . $idof_ad_id . '" style="' . $style . '">' . "\n";
        $code .= do_shortcode( $quads_ad_var );
        $code .= '</div>' . "\n";
    }

    return $code;
}

/**
 * return ad content
 *
 * @since 0.9.4
 * @param int $id id of the ad
 * @return string
 */
function quads_get_ad($id = 0) {
    global $quads_options,$quads_mode;

    if ( quads_ad_reach_max_count() ){
        return;
    }

    if ( isset($quads_options['ads']['ad' . $id]['code']) ){

        if($quads_mode == 'new'){
            $content_post = get_post($quads_options['ads']['ad' . $id]['ad_id']);
            if( isset($content_post->post_status) && $content_post->post_status == 'draft'){

                return '';
            }
        }
        $ads =$quads_options['ads']['ad' . $id];

//        $is_on         = quads_is_visibility_on($ads);
        $is_visitor_on = quads_is_visitor_on($ads);
        if($quads_mode == 'new' ) {
            if($is_visitor_on ) {
            if($ads['ad_type'] == 'random_ads') {
                if ( function_exists( 'quads_parse_random_ads' ) ) {
                    $html  ='<!--CusRnd'.$ads['ad_id'].'-->';
                    return quads_parse_random_ads_new($html);
                }else{
                    return '';
                }
            }else if($ads['ad_type'] == 'rotator_ads') {
                if ( function_exists( 'quads_parse_rotator_ads' ) ) {
                    $html  ='<!--CusRot'.$ads['ad_id'].'-->';
                    return quads_parse_rotator_ads($html);
                }else{
                    return '';
                }
            }else if($ads['ad_type'] == 'group_insertion') {
                if ( function_exists( 'quads_parse_group_insert_ads' ) ) {
                    $html  ='<!--CusGI'.$ads['ad_id'].'-->';
                    return quads_parse_group_insert_ads($html);
                }else{
                    return '';
                }
            }else{
                // Count how often the shortcode is used - Important
                quads_set_ad_count_shortcode();
                return quads_render_ad('ad' . $id, $quads_options['ads']['ad' . $id]['code']);
            }

            }else{

                return '';
            }
        }else{

            // Count how often the shortcode is used - Important
            quads_set_ad_count_shortcode();
            return quads_render_ad('ad' . $id, $quads_options['ads']['ad' . $id]['code']);
        }
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

    if ( !isset($post->ID ) ){
        return false;
    }

    $meta_key = '_quads_config_visibility';

    $value_arr = get_post_meta ( $post->ID, $meta_key, true );
    $value_key = isset($value_arr[$key]) ? $value_arr[$key] : null;

    if (!empty($value_key))
    return (string)$value_key;

    return false;
}

/*
 * Return string through shortcode function and strip out specific shortcode from it to
 * prevents infinte loops if shortcode contains same shortcode
 *
 * @since 1.3.6
 * @param1 string shortcode e.g. quads
 * @param1 string content to return via shortcode
 * @return string / shortcodes parsed
 */

function quadsCleanShortcode( $code, $content ) {
    global $shortcode_tags;
    $stack = $shortcode_tags;
    $shortcode_tags = array($code => 1);
    $content = strip_shortcodes( $content );
    $shortcode_tags = $stack;

    return do_shortcode( $content );
}
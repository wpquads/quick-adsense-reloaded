<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Post Types
 *
 * @package     QUADS
 * @subpackage  Functions/post_types
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.8
 */



/**
 * Get list of available post_types
 * 
 * @return array list of post_types
 */
function quads_get_post_types(){    
    $post_types = array('post'=>'post', 'page'=>'page');
    return apply_filters('quads_post_types',$post_types);
}

/**
 * Check if ad is allowed on specific post_type
 * 
 * @global array $quads_options
 * @global array $post
 * @return boolean true if post_type is allowed
 */
function quads_post_type_allowed(){
    global $quads_options, $post;
    
    $return = false;
    
    if (!isset($post)){
        $return = false;
        return apply_filters('quads_post_type_allowed',$return);
    }
    
    if (!isset($quads_options['post_types']) || !is_array($quads_options['post_types']) || empty($quads_options['post_types'])){
        $return = false;
        return apply_filters('quads_post_type_allowed',$return);
    }

    $current_post_type = get_post_type($post->ID);
    if ( in_array( $current_post_type, $quads_options['post_types'] )){
        $return = true;
    }
    return apply_filters('quads_post_type_allowed',$return);
}
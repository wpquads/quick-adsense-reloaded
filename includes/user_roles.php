<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * User Roles
 *
 * @package     QUADS
 * @subpackage  Functions/user_roles
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.8
 */

/**
 * Check if ad is hidden from current user role
 * 
 * @global array $quads_options
 * @return boolean true if the current user role is allowed to see ads
 */
function quads_user_roles_permission(){
    global $quads_options;
    
    // No restriction. Show ads to all user_roles including public visitors without user role
    if (!isset($quads_options['user_roles'])){
        return true;
    }
    $roles = wp_get_current_user()->roles;
    if ( isset ($quads_options['user_roles']) && count(array_intersect( $quads_options['user_roles'], $roles )) >= 1){
        return false;
    }
    
    return true;
}
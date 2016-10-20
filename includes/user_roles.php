<?php

/**
 * API Functions allow creation of custom ad positions
 *
 * @package     QUADS
 * @subpackage  Functions/user_roles
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.7
 */

/**
 * Get user role of current user
 * 
 * @return mixed string | boolean
 */
function quads_get_user_role(){
     $user_info = get_userdata($id);

     if (isset($user_info->roles) ){
         return $user_info->roles;
     } 
     return false;
}


/**
 * Get all available user roles
 * 
 * @return array
 */
function quads_get_all_user_roles(){    
    
    if ( !function_exists('get_editable_roles') ) {
        require_once( ABSPATH . '/wp-admin/includes/user.php' );
    }
    
    $user_roles = array();
    
    $roles = get_editable_roles();
    
    foreach ($roles as $key => $value){
        $user_roles[] = $key; 
    }
    var_dump($user_roles);
    return $user_roles;
    
}


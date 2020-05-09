<?php

/**
 * Conditions
 *
 * @package     QUADS
 * @subpackage  Functions/conditions
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.8
 */



/**
 * Global! Determine if ads are visible
 * 
 * @global arr $quads_options
 * @param string $content 
 * @since 0.9.4
 * @return boolean true when ads are shown
 */
function quads_ad_is_allowed( $content = null ) {
    global $quads_options, $quads_mode;
                
    // Never show ads in ajax calls
    if ( isset($quads_options['is_ajax']) && (defined('DOING_AJAX') && DOING_AJAX) || 
         (! empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) == 'xmlhttprequest' )
        )
        { 
        /* it's an AJAX call */ 
        return false;
    }

    if(isset($quads_mode) && $quads_mode == 'new'){
        
       return true; 

    }
    
    $hide_ads = apply_filters('quads_hide_ads', false);
    
    // User Roles check
    if(!quads_user_roles_permission()){
       return false;
    }
    
    // Frontpage check
    if (is_front_page() && isset( $quads_options['visibility']['AppHome'] ) ){
       return true;
    }

    if(
            (is_feed()) ||
            (is_search()) ||
            (is_404() ) ||
            (strpos( $content, '<!--NoAds-->' ) !== false) ||
            (strpos( $content, '<!--OffAds-->' ) !== false) ||
            (is_front_page() && !isset( $quads_options['visibility']['AppHome'] ) ) ||
            (is_category() && !(isset( $quads_options['visibility']['AppCate'] ) ) ) ||
            (is_archive() && !( isset( $quads_options['visibility']['AppArch'] ) ) ) ||
            (is_tag() && !( isset( $quads_options['visibility']['AppTags'] ) ) ) ||
            (!quads_post_type_allowed()) ||
            (is_user_logged_in() && ( isset( $quads_options['visibility']['AppLogg'] ) ) ) ||
            true === $hide_ads
    ) {
        return false;
    }
    // else
    return true;
}
/**
 * Global! Determine if widget ads are visible
 * 
 * @global arr $quads_options
 * @param string $content 
 * @since 0.9.4
 * @return boolean true when ads are shown
 */
function quads_widget_ad_is_allowed( $content = null ) {
    global $quads_options;
    
        
    // Never show ads in ajax calls
    if ( isset($quads_options['is_ajax']) && (defined('DOING_AJAX') && DOING_AJAX) || 
         (! empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) == 'xmlhttprequest' )
        )
        { 
        /* it's an AJAX call */ 
        return false;
    }
    
    $hide_ads = apply_filters('quads_hide_ads', false);
    
    // User Roles check
    if(!quads_user_roles_permission()){
       return false;
    }
    
    // Frontpage check
    if (is_front_page() && isset( $quads_options['visibility']['AppHome'] ) ){
       return true;
    }

    if(
            (is_feed()) ||
            (is_search()) ||
            (is_404() ) ||
            (strpos( $content, '<!--NoAds-->' ) !== false) ||
            (strpos( $content, '<!--OffAds-->' ) !== false) ||
            (is_category() && !(isset( $quads_options['visibility']['AppCate'] ) ) ) ||
            (is_archive() && !( isset( $quads_options['visibility']['AppArch'] ) ) ) ||
            (is_tag() && !( isset( $quads_options['visibility']['AppTags'] ) ) ) ||
            (!quads_post_type_allowed()) ||
            (is_user_logged_in() && ( isset( $quads_options['visibility']['AppLogg'] ) ) ) ||
            true === $hide_ads
    ) {
        return false;
    }
    // else
    return true;
}


/**
 * Check if Ad widgets are visible on homepage
 * 
 * @since 0.9.7
 * return true when ad widgets are not visible on frontpage else false
 */
function quads_hide_ad_widget_on_homepage(){
 global $quads_options;
 
 $is_active = isset($quads_options["visibility"]["AppSide"]) ? true : false;
 
 if( is_front_page() && $is_active ){
     return true;
 }
 
 return false;
 
}


/**
 * Get the total number of active ads
 * 
 * @global int $visibleShortcodeAds
 * @global int $visibleContentAdsGlobal
 * @global int $ad_count_custom
 * @global int $ad_count_widget
 * @return int number of active ads
 */
function quads_get_total_ad_count(){
    global $visibleShortcodeAds, $visibleContentAdsGlobal, $ad_count_custom, $ad_count_widget;
    
    $shortcode = isset($visibleShortcodeAds) ? (int)$visibleShortcodeAds : 0;
    $content = isset($visibleContentAdsGlobal) ? (int)$visibleContentAdsGlobal : 0;
    $custom = isset($ad_count_custom) ? (int)$ad_count_custom : 0;
    //$widget = isset($ad_count_widget) ? (int)$ad_count_widget : 0;
    $widget = quads_get_number_widget_ads();
    
    //wp_die($widget);
    //wp_die( $shortcode + $content + $custom + $widget);
    return $shortcode + $content + $custom + $widget;
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
    
    $maxads = isset($quads_options['maxads']) ? $quads_options['maxads'] : 100;
    $maxads = $maxads - quads_get_number_widget_ads();
    
    //echo 'Total ads: '.  quads_get_total_ad_count() . ' maxads: '. $maxads . '<br>';
    //wp_die('Total ads: '.  quads_get_total_ad_count() . ' maxads: '. $maxads . '<br>');  
    if ( quads_get_total_ad_count() >= $maxads ){
        return true;
    }
}

/**
 * Increment count of active ads generated in the_content
 * 
 * @global int $ad_count
 * @param type $ad_count
 * @return int amount of active ads in the_content
 */
function quads_set_ad_count_content(){
    global $visibleContentAdsGlobal;
       
    $visibleContentAdsGlobal++;
    return $visibleContentAdsGlobal;
}

/**
 * Increment count of active ads generated with shortcodes
 * 
 * @return int amount of active shortcode ads in the_content
 */
function quads_set_ad_count_shortcode(){
    global $visibleShortcodeAds;
       
    $visibleShortcodeAds++;
    return $visibleShortcodeAds;
}

/**
 * Increment count of custom active ads 
 * 
 * @return int amount of active custom ads
 */
function quads_set_ad_count_custom(){
    global $ad_count_custom;
       
    $ad_count_custom++;
    return $ad_count_custom;
}

/**
 * Increment count of active ads generated on widgets
 * 
 * @return int amount of active widget ads 
 * @deprecated since 1.4.1
 */
function quads_set_ad_count_widget(){
    global $ad_count_widget;

    $ad_count_widget++;
    return $ad_count_widget;
}

/**
 * Check if AMP ads are disabled on a post via the post meta box settings
 * 
 * @global obj $post
 * @return boolean true if its disabled
 */
function quads_is_disabled_post_amp() {
    global $post;
    
    if(!is_singular()){
        return true;
    }
    
    $ad_settings = get_post_meta( $post->ID, '_quads_config_visibility', true );

    if( !empty( $ad_settings['OffAMP'] ) ) {
        return true;
    }
    return false;
}
//New Functions in 2.0 starts here

function quads_is_visitor_on($ads){

    $include  = array();
    $exclude  = array();
    $response = false;
    
    $visibility_include = isset($ads['targeting_include']) ? $ads['targeting_include'] : ''; 
    
    $visibility_exclude = isset($ads['targeting_exclude']) ? $ads['targeting_exclude'] : ''; 
        
    
    if($visibility_include){ 
        
        foreach ($visibility_include as $visibility){
  
           $include[] = quads_visitor_comparison_logic_checker($visibility);     
           
        }   
      
      }else{
          $response = true;
      }

      if($visibility_exclude){ 
        
        foreach ($visibility_exclude as $visibility){
  
            $exclude[] = quads_visitor_comparison_logic_checker($visibility);     
           
        }   
      
      }else{
          $response = true;
      }
      
      if(!empty($include)){

        $include =   array_filter(array_unique($include));

        if(isset($include[0])){
            $response = true;
        }
        
      }
      
      if(!empty($exclude)){
        $exclude =   array_filter(array_unique($exclude));
        
        if(isset($exclude[0])){
            $response = false;
        }

      }

      return $response;

}

function quads_is_visibility_on($ads){

    $include  = array();
    $exclude  = array();
    $response = false;
    
    $visibility_include = isset($ads['visibility_include']) ? $ads['visibility_include'] : ''; 
    
    $visibility_exclude = isset($ads['visibility_exclude']) ? $ads['visibility_exclude'] : '';     
    
    if($visibility_include){ 
        
        foreach ($visibility_include as $visibility){
  
           $include[] = quads_comparison_logic_checker($visibility);                
        }   
      
      }

      if($visibility_exclude){ 
        
        foreach ($visibility_exclude as $visibility){
  
            $exclude[] = quads_comparison_logic_checker($visibility);     
           
        }   
      
      }
      
      if(!empty($include)){

        $include =   array_filter(array_unique($include));

        if(isset($include[0])){
            $response = true;
        }
        
      }
      
      if(!empty($exclude)){
        $exclude =   array_filter(array_unique($exclude));
        
        if(isset($exclude[0])){
            $response = false;
        }

      }      
      return $response;

}


function quads_visitor_comparison_logic_checker($visibility){
  
    global $post;             
    $v_type       = $visibility['type']['value'];
    $v_id         = $visibility['value']['value'];    
    $result       = false; 
    
    // Get all the users registered
    $user       = wp_get_current_user();

    switch ($v_type) {
                   
        case 'device_type':
            require_once QUADS_PLUGIN_DIR . '/admin/includes/mobile-detect.php'; 
            
            $mobile_detect = $isTablet = '';            
            $mobile_detect = new Quads_Mobile_Detect;
            $isMobile = $mobile_detect->isMobile();
            $isTablet = $mobile_detect->isTablet();

            $device_name  = 'desktop';
            if( $isMobile && $isTablet ){ //Only For tablet
              $device_name  = 'mobile';
            }else if($isMobile && !$isTablet){ // Only for mobile
              $device_name  = 'mobile';
            }
            
             if($v_id == $device_name){
                $result     = true; 
             }             
        break;
        case 'referrer_url3':    
                                      
                  $referrer_url  = esc_url($_SERVER['HTTP_REFERER']);                      
                  if(isset($input['key_4']) && $input['key_3']=='url_custom'){
                  $data = $input['key_4'];   
                  }
                  
                  if ( $comparison == 'equal' ) {
                      if ( $referrer_url == $data ) {
                        $result = true;
                      }
                  }
                  if ( $comparison == 'not_equal') {              
                      if ( $referrer_url != $data ) {
                        $result = true;
                      }
                  }    
                  
                  
                            
        break;
        case 'browser_width3':
          $result = true;
        break;         
        case 'browser_language3':                      
                 $browser_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);                                                                     
                if ( $comparison == 'equal' ) {
                      if ( $browser_language == $data ) {
                        $result = true;
                      }
                }
                  if ( $comparison == 'not_equal') {              
                      if ( $browser_language != $data ) {
                        $result = true;
                      }
                  }            
        break;
        
        case 'geo_locationd':     
                 $country_code =  ''; 
                 $saved_ip     =  '';
                 $user_ip      =  $this->adsforwp_get_client_ip();    
                 
                 $saved_ip_list = array();
                 
                  if(isset($_COOKIE['adsforwp-user-info'])){
                      
                       $saved_ip_list = $_COOKIE['adsforwp-user-info']; 
                       $saved_ip = trim(base64_decode($saved_ip_list[0]));	
                       
                  }						
                 
                 if(!empty($saved_ip_list) && $saved_ip == $user_ip){  
              
                  if(isset($saved_ip_list[1])){
                      
                      $country_code = trim(base64_decode($saved_ip_list[1])); 
                      
                  }   
                                      
                 }                                                                                             
                  if ( $comparison == 'equal' ) {
                        if ( $country_code == $data ) {
                          $result = true;
                        }
                  }
                  if ( $comparison == 'not_equal') {              
                      if ( $country_code != $data ) {
                        $result = true;
                      }
                  }            
        break;
        
        case 'url_parameter3':                      
                 $url = esc_url($_SERVER['REQUEST_URI']);                       
                if ( $comparison == 'equal' ) {                                            
                      if ( strpos($url,$data) !== false ) {                           
                        $result = true;
                      }
                }
                  if ( $comparison == 'not_equal') {              
                      if ( strpos($url,$data) == false ) {
                        $result = true;
                      }
                  }            
        break;
        
        case 'cookie3':          
            
            $cookie_arr = $_COOKIE;              
            if($data ==''){
                
             if ( $comparison == 'equal' ) {
              if ( isset($cookie_arr) ) {
                $result = true;
              }
              }
              if ( $comparison == 'not_equal') {              
                  if ( !isset($cookie_arr) ) {
                    $result = true;
                  }
              }   
                
            }else{
                
              if ( $comparison == 'equal' ) {
              
                  if($cookie_arr){
                  
                      foreach($cookie_arr as $arr){
                      
                      if($arr == $data){
                          $result = true;
                           break;
                      }
                    }
                      
                  }
                                                          
              }
              if ( $comparison == 'not_equal') {
                  
                  if(isset($cookie_arr)){
                  
                      foreach($cookie_arr as $arr){
                      
                      if($arr != $data){
                          $result = true;
                           break;
                      }
                      
                    }
                      
                  }
                                                         
              } 
                                                    
            }
            
            
            
        break;
        
         case 'logged_in_visitor3': 
          
           if ( is_user_logged_in() ) {
              $status = 'true';
           } else {
              $status = 'false';
           }
                        
          if ( $comparison == 'equal' ) {
              if ( $status == $data ) {
                $result = true;
              }
          }
          if ( $comparison == 'not_equal') {              
              if ( $status != $data ) {
                $result = true;
              }
          }

      break;
      
      case 'user_agent3':     
          
              $user_agent_name = $this->adsforwp_detect_user_agent();  
          
              if(isset($input['key_5']) && $input['key_3']=='user_agent_custom'){                                        
                  if(stripos($_SERVER['HTTP_USER_AGENT'], $input['key_5'])){
                   $user_agent_name = $input['key_5'];   
                  }
                  $data = $input['key_5'];
              }               
              if ( $comparison == 'equal' ) {
              if ( $user_agent_name == $data ) {
                $result = true;
              }
             }
              if ( $comparison == 'not_equal') {              
              if ( $user_agent_name != $data ) {
                $result = true;
              }
             }                         
      break;
      
      case 'user_typed':            
          if ( $comparison == 'equal') {
              if ( in_array( $data, (array) $user->roles ) ) {
                  $result = true;
              }
          }

          if ( $comparison == 'not_equal') {
              require_once ABSPATH . 'wp-admin/includes/user.php';
              // Get all the registered user roles
              $roles = get_editable_roles();                
              $all_user_types = array();
              foreach ($roles as $key => $value) {
                $all_user_types[] = $key;
              }
              // Flip the array so we can remove the user that is selected from the dropdown
              $all_user_types = array_flip( $all_user_types );
              // User Removed
              unset( $all_user_types[$data] );
              // Check and make the result true that user is not found 
              if ( in_array( $data, (array) $all_user_types ) ) {
                  $result = true;
              }
          }
          
         break;
      case 'membership_leveld':
        if( $comparison == 'equal') {
          if(isset($user->membership_level) && function_exists('pmpro_getAllLevels')){
            if ( in_array( $data, (array) $user->membership_level->id ) ) {
                  $result = true;
            }
          }
        }
        if ( $comparison == 'not_equal') {
            require_once ABSPATH . 'wp-admin/includes/user.php';
            // Get all the registered user roles
            if(function_exists('pmpro_getAllLevels')){
              $all_pmpro_levels = pmpro_getAllLevels(false, true);
              $all_level_types = array();
              foreach ($all_pmpro_levels as $key => $value) {
                $all_level_types[] = $value->id;
              }
              // Flip the array so we can remove the user that is selected from the dropdown
              $all_level_types = array_flip( $all_level_types );
              // User Removed
              unset( $all_level_types[$data] );
              // Check and make the result true that user is not found
              if ( in_array( $data, (array) $all_level_types ) ) {
                  $result = true;
              }  
            }
        } 
      break;
    default:
      $result = false;
      break;
  }

return $result;
}


function quads_comparison_logic_checker($visibility){
  
    global $post;             
    
    $v_type       = $visibility['type']['value'];
    $v_id         = isset($visibility['value']['value']) ? $visibility['value']['value'] :''; 
    $result       = false; 
   
    // Get all the users registered
    $user       = wp_get_current_user();

    switch ($v_type) {
     
    // Basic Controls ------------ 
      // Posts Type
    case 'post_type':   
          
             $post_type  = get_post_type($post->ID); 

             if($v_id == $post_type){
                $result     = true; 
             }
                     
      break;
      
      
      // Posts
    case 'general':    
                        
         if( ($v_id == 'homepage') && is_home() || is_front_page() || ( function_exists('ampforwp_is_home') && ampforwp_is_home()) ){
            $result     = true; 
         }

         if($v_id == 'show_globally'){
            $result     = true; 
         }

    break;

  // Logged in User Type
    case 'user_type':            
        
            if ( in_array( $v_id, (array) $user->roles ) ) {
                $result = true;
            }

       break; 

// Post Controls  ------------ 
  // Posts
    case 'post':    
      
        if($v_id == $post->ID){
            $result = true;
        }
        

    break;

  // Post Category
    case 'post_category':
     
      $current_category = '';
      
      if(is_object($post)){
      
          $postcat = get_the_category( $post->ID );
            if(!empty($postcat)){
                if(is_object($postcat[0])){                 
                  $current_category = $postcat[0]->cat_ID;                   
                }               
            }
                        
      }      
      if($v_id == $current_category){
          $result = true;
      }

    break;
  // Post Format
    case 'post_format':
      
      $current_post_format = get_post_format( $post->ID );
          
      if ( $current_post_format === false ) {
          $current_post_format = 'standard';
      }
      if($v_id == $current_post_format){
        $result = true;
      }
    break;

    case 'page': 
    
        if($v_id == $post->ID){
            $result = true;
        }

    break;

    case 'tags': 
    
        if ( term_exists( $v_id) ) {
            $result = true;
        } 

    break;


    case 'ef_taxonomy':
        
    $taxonomy_names = get_post_taxonomies( $post->ID );
    
    $post_terms = '';

      if ( $v_id != 'all') {

        $post_terms = wp_get_post_terms($post->ID, $v_id);           
        
        if ( $post_terms ) {
            $result = true;
        }                

      } else {
        
          if ( $taxonomy_names ) {                
              $result = true;
          }        
      }
    break;
  
  default:
    $result = false;
    break;
}

return $result;
}


//New Functions in 2.0 ends here
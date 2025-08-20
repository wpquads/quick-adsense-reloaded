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
    // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    
    if ( isset($quads_options['is_ajax']) && (defined('DOING_AJAX') && DOING_AJAX) ||
         ( isset($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) && ! empty( wp_unslash( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ) && strtolower( wp_unslash( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) ) == 'xmlhttprequest' ))
        {
          $theme = wp_get_theme();
          if(is_object($theme) && $theme->name == 'Bimber'){
              $bimber_theme_settings = get_option( 'bimber_theme' );
              if(isset($bimber_theme_settings['posts_auto_load_enable']) && $bimber_theme_settings['posts_auto_load_enable']){
                return true;
              }
          }
        /* it's an AJAX call */
        return false;
    }
    $hide_ads = apply_filters('quads_hide_ads', false);

    if(isset($quads_mode) && $quads_mode == 'new'){
      $content_condition = !empty($content)?$content:'';
         if(
            (is_feed()) ||
            (is_search()) ||
            (is_404() ) ||
            (strpos( $content_condition, '<!--NoAds-->' ) !== false) ||
            (strpos( $content_condition, '<!--OffAds-->' ) !== false) ||
            true === $hide_ads
    ) {
        return false;
    }
       return true;

    }


    // User Roles check
    if(!quads_user_roles_permission()){
       return false;
    }

    // Frontpage check
    if (is_front_page() && isset( $quads_options['visibility']['AppHome'] ) ){
       return true;
    }

    $content_condition = !empty($content)?$content:'';

    if(
            (is_feed()) ||
            (is_search()) ||
            (is_404() ) ||
            (strpos( $content_condition, '<!--NoAds-->' ) !== false) ||
            (strpos( $content_condition, '<!--OffAds-->' ) !== false) ||
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
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    if ( isset($quads_options['is_ajax']) && (defined('DOING_AJAX') && DOING_AJAX) ||
         (! empty( wp_unslash( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ) && strtolower( wp_unslash( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) ) == 'xmlhttprequest' ))
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

    $content_condition = !empty($content)?$content:'';

    if(
            (is_feed()) ||
            (is_search()) ||
            (is_404() ) ||
            (strpos( $content_condition, '<!--NoAds-->' ) !== false) ||
            (strpos( $content_condition, '<!--OffAds-->' ) !== false) ||
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

function quadsGetIPAddress() {  
$ip = array();
 if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
   $new_ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
  }
  //whether ip is from the proxy  
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $new_ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
  }
  //whether ip is from the remote address  
  else{
    if( isset( $_SERVER['REMOTE_ADDR'] ) ){
      $new_ip =   sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
    }
  }
   $ip =  get_option('add_blocked_ip') ? get_option('add_blocked_ip') : array() ;
   array_push( $ip, array( 'ip'=>$new_ip,'time' => gmdate('l d-m-Y H:i:s') ) );
   $ip = array_values(array_column( $ip , null, 'ip' ));
  return $ip;
}

function quads_click_fraud_on(){
  global $quads_options;
  $cookie_check = true;

  if (isset($quads_options['click_fraud_protection']) && !empty($quads_options['click_fraud_protection']) && $quads_options['click_fraud_protection']  && isset( $_COOKIE['quads_ad_clicks'] ) ) {
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized	
    $quads_ad_click = json_decode( stripslashes( $_COOKIE['quads_ad_clicks'] ), true );
    $current_time = time();
    if (isset($quads_options['allowed_click']) && isset($quads_options['ban_duration']) && $quads_ad_click['count']  >= $quads_options['allowed_click'] ) {
    if(function_exists('quadsGetIPAddress') ){
      $ips = quadsGetIPAddress();
    }
    $final_ip = $ips ;
    update_option( 'add_blocked_ip', $final_ip  );
  }
  
    if (isset($quads_options['allowed_click']) && isset($quads_options['ban_duration']) && $quads_options['allowed_click'] <= $quads_ad_click['count'] ) {
      $cookie_check = false;
      if($current_time >= strtotime( $quads_ad_click['exp']. ' +'.$quads_options['ban_duration'].' day') ){
        $cookie_check = true;
      }else {
        if ($current_time <= strtotime( $quads_ad_click['exp']. ' +'.$quads_options['click_limit'].' hours') ) {
             $cookie_check = false;
        }
    }
  }
}
return $cookie_check;
}
//New Functions in 2.0 starts here =272;

function quads_is_visitor_on($ads){
    global $quads_options;
    $include  = array();
    $exclude  = array();
    $response = false;

    $visibility_include = isset($ads['targeting_include']) ? $ads['targeting_include'] : array();

    $visibility_exclude = isset($ads['targeting_exclude']) ? $ads['targeting_exclude'] : array();
    $check_condition_include = array_column($visibility_include, 'condition');
    $check_condition_exclude = array_column($visibility_exclude, 'condition');

  if((is_array($check_condition_include) && !empty($check_condition_include)) || (is_array($check_condition_exclude) && !empty($check_condition_exclude))){
   
    $include_value_old = true;
    if($visibility_include){
        $condition_old = '';
        $include_value_old = true;
        foreach ($visibility_include as $visibility){
            $condition         = isset($visibility['condition']) ? $visibility['condition'] :'AND';
            $include_value_new = quads_visitor_comparison_logic_checker($visibility);
            switch ($condition_old){
                case 'AND':
                    $include_value_old = $include_value_old &&  $include_value_new;
                    $condition_old = $condition;
                    break;
                case 'OR':
                    $include_value_old = $include_value_old ||  $include_value_new;
                    $condition_old = $condition;
                    break;
                default:
                    $condition_old = $condition;
                    $include_value_old =$include_value_new;
                    break;
            }
        }
    }

    $response = $include_value_old;
    if($visibility_exclude){
        $exclude_value_old = false;
        $condition_old = '';
        foreach ($visibility_exclude as $visibility){
            $condition         = isset($visibility['condition']) ? $visibility['condition'] :'AND';
            $exclude_value_new = quads_visitor_comparison_logic_checker($visibility);
            switch ($condition_old){
                case 'AND':
                    $exclude_value_old = $exclude_value_old &&  $exclude_value_new;
                    $condition_old = $condition;
                    break;
                case 'OR':
                    $exclude_value_old = $exclude_value_old ||  $exclude_value_new;
                    $condition_old = $condition;
                    break;
                default:
                    $condition_old = $condition;
                    $exclude_value_old =$exclude_value_new;
                    break;
            }
        }
        if($exclude_value_old){
            $response =false;
        }
    }
  }else{
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
      if(empty($include)){
        $response = true;
      }
    }
    if(!empty($include)){
      if(in_array( false ,$include )){
        $response = false;
      }else{
        $response = true;
      }
    }
    if(!empty($exclude)){
      $exclude =   array_filter(array_unique($exclude));
      if(isset($exclude[0])){
          $response = false;
      }
    }
  }
      return $response;

}

function quads_is_visibility_on($ads){
    $include  = array();
    $exclude  = array();
    $response = false;
    $visibility_include = isset($ads['visibility_include']) ? $ads['visibility_include'] : array();
    $visibility_exclude = isset($ads['visibility_exclude']) ? $ads['visibility_exclude'] : array();
    $check_condition_include = array_column($visibility_include, 'condition');
    $check_condition_exclude = array_column($visibility_exclude, 'condition');

  if((is_array($check_condition_include) && !empty($check_condition_include)) || (is_array($check_condition_exclude) && !empty($check_condition_exclude))){
    
    $include_value_old = true;
    if($visibility_include){
        $condition_old = '';
        $include_value_old = true;
        foreach ($visibility_include as $visibility){
            $condition         = isset($visibility['condition']) ? $visibility['condition'] :'AND';
            $include_value_new = quads_comparison_logic_checker($visibility);
            switch ($condition_old){
                case 'AND':
                    $include_value_old = $include_value_old &&  $include_value_new;
                    $condition_old = $condition;
                    break;
                case 'OR':
                    $include_value_old = $include_value_old ||  $include_value_new;
                    $condition_old = $condition;
                    break;
                default:
                    $condition_old = $condition;
                    $include_value_old =$include_value_new;
                    break;
            }
        }
    }
    $response = $include_value_old;
    if($visibility_exclude){
        $exclude_value_old = false;
        $condition_old = '';
        foreach ($visibility_exclude as $visibility){
            $condition         = isset($visibility['condition']) ? $visibility['condition'] :'AND';
            $exclude_value_new = quads_comparison_logic_checker($visibility);
            switch ($condition_old){
                case 'AND':
                    $exclude_value_old = $exclude_value_old &&  $exclude_value_new;
                    $condition_old = $condition;
                    break;
                case 'OR':
                    $exclude_value_old = $exclude_value_old ||  $exclude_value_new;
                    $condition_old = $condition;
                    break;
                default:
                    $condition_old = $condition;
                    $exclude_value_old =$exclude_value_new;
                    break;
            }
        }
        if($exclude_value_old){
            $response =false;
        }
    }
  }else{
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
      $include =   array_values(array_filter(array_unique($include)));
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
  }
      return $response;
}
add_action('wp_head', 'quads_set_browser_width_script');
function quads_set_browser_width_script(){
  if(!is_admin() && !quads_is_amp_endpoint()){
    echo "<script>document.cookie = 'quads_browser_width='+screen.width;</script>";
  }
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
        case 'referrer_url':
            $referrer_url  = (isset($_SERVER['HTTP_REFERER'])) ? esc_url( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) )):'';
            if ( $referrer_url == $v_id ) {
              $result = true;
            }

        break;
        case 'browser_language':
          if( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ){
            $browser_language = substr(sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ), 0, 2);
            if ( $browser_language == $v_id ) {
              $result = true;
            }
          }
        break;
        case 'multilingual_language':
          if( class_exists('SitePress') ){
          $multilingual_language = apply_filters( 'wpml_current_language', NULL );  
          if ( $multilingual_language == $v_id ) {
            $result = true;
          }
        }
        break;

        case 'url_parameter':
          if( isset( $_SERVER['REQUEST_URI'] ) ){
              $url = esc_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
              if ( strpos($url, $v_id) !== false ) {
                $result = true;
              }       
          }       
        break;

        case 'cookie':

            $cookie_arr = $_COOKIE;

            if($v_id ==''){
              if ( isset($cookie_arr) ) {
                $result = true;
              }
            }else{

            if($cookie_arr){
              foreach($cookie_arr as $key=>$value){
                if($key == $v_id){
                    $result = true;
                    break;
                }
              }
            }
          }
          break;

         case 'logged_in_visitor':
        case 'logged_in':
        if ( is_user_logged_in() ) {
              $status = 'true';
           } else {
              $status = 'false';
           }


          if ( $status == $v_id ) {
            $result = true;
          }


      break;

      case 'user_agent':
            $user_agent_name =quads_detect_user_agent();
            if ( $user_agent_name == $v_id ) {
              $result = true;
            }
      break;
      case 'user_type':
        if ( in_array( $v_id, (array) $user->roles ) ) {
            $result = true;
        }
        break;
      case 'browser_width':
       if(isset($_COOKIE['quads_browser_width']) && $_COOKIE['quads_browser_width'] == $v_id){
          $result = true;
        }
        break;
    default:
      $result = false;
      break;
  }

 $result  = apply_filters( 'quads_visitor_comparison_logic_checker', $result ,$visibility);

return $result;
}

function quads_check_my_device(){
    $is_device = 'desktop';
    if( isset( $_SERVER['HTTP_USER_AGENT'] ) ){
      if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|palm|phone|pie|up\.browser|up\.link|webos|wos)/i", strtolower(sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) )))){
          $is_device = 'phone';        
      }elseif (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower(sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ))) {
          $is_device = 'tablet_landscape';
      }else{
          $is_device = 'desktop';
      }
    }
    return $is_device;
}

function quads_detect_user_agent( ){
        $user_agent_name ='others';
        if( isset( $_SERVER['HTTP_USER_AGENT'] ) ){
            if(strpos(sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'Opera') || strpos($user_agent_name, 'OPR/')) $user_agent_name = 'opera';
            elseif (strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'Edge'))    $user_agent_name = 'edge';
            elseif (strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'Firefox')) $user_agent_name ='firefox';
            elseif (strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'MSIE') || strpos($user_agent_name, 'Trident/7')) $user_agent_name = 'internet_explorer';
            elseif (stripos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'iPod')) $user_agent_name = 'ipod';
            elseif (stripos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'iPhone')) $user_agent_name = 'iphone';
            elseif (stripos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'iPad')) $user_agent_name = 'ipad';
            elseif (stripos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'Android')) $user_agent_name = 'android';
            elseif (stripos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'webOS')) $user_agent_name = 'webos';
            elseif (strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'Chrome'))  $user_agent_name = 'chrome';
            elseif (strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 'Safari'))  $user_agent_name = 'safari';
        }
        return $user_agent_name;
}
function quads_get_ip_address() {
    $ip_address = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ip_address = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ip_address = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ip_address = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ip_address = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ip_address = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ip_address = getenv('REMOTE_ADDR');
    else
        $ip_address = '';
    return $ip_address;
}

function quads_comparison_logic_checker($visibility){

    global $post;

    $v_type       = $visibility['type']['value'];
    $v_id         = isset($visibility['value']['value']) ? $visibility['value']['value'] :'';
    $result       = false;
    if(!is_object($post)){
      return false;
    }
    // Get all the users registered
    $user       = wp_get_current_user();

    switch ($v_type) {

    // Basic Controls ------------
      // Posts Type
    case 'post_type':

             $post_type  = get_post_type($post->ID);

             if($v_id == $post_type && is_singular()){
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

        if($v_id == $post->ID && is_singular()){
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
        global $wp_query;
        $page_id = $wp_query->get_queried_object_id();
        if($v_id == $page_id){
            $result = true;
        }

    break;

    case 'tags':

        if ( has_tag( $v_id) ) {
            $result = true;
        }

    break;


    case 'ef_taxonomy':
    case 'taxonomy':

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
    case 'page_template':
          $object = get_queried_object();
          $template = get_page_template_slug($object);
          if($v_id == $template){
            $result = true;
          }
      break;

  default:
    $result = false;
    break;
}

return $result;
}//New Functions in 2.0 ends here
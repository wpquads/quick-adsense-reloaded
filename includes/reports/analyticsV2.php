<?php
class quads_admin_analytics{
  
  public function __construct() {                           
      
  }
    
/**
 * This is the list of hooks used in this class
 */
public function quads_admin_analytics_hooks(){
  
    add_action( 'wp_enqueue_scripts', array($this,'quads_frontend_enqueue'));

      add_action('wp_ajax_nopriv_quads_insert_ad_impression', array($this, 'quads_insert_ad_impression'));      
      add_action('wp_ajax_quads_insert_ad_impression', array($this, 'quads_insert_ad_impression'));

      add_action('wp_ajax_nopriv_quads_insert_ad_impression_amp', array($this, 'quads_insert_ad_impression_amp'));      
      add_action('wp_ajax_quads_insert_ad_impression_amp', array($this, 'quads_insert_ad_impression_amp'));

      add_action('wp_ajax_nopriv_quads_insert_ad_clicks', array($this, 'quads_insert_ad_clicks'));      
      add_action('wp_ajax_quads_insert_ad_clicks', array($this, 'quads_insert_ad_clicks'));
      
      add_action('wp_ajax_nopriv_quads_insert_ad_clicks_amp', array($this, 'quads_insert_ad_clicks_amp'));      
      add_action('wp_ajax_quads_insert_ad_clicks_amp', array($this, 'quads_insert_ad_clicks_amp'));
      
      add_filter('amp_post_template_data',array($this, 'quads_enque_analytics_amp_script'));

      add_filter('amp_post_template_footer', array($this, 'quads_add_analytics_amp_tags'));                             
}


public function quads_frontend_enqueue(){
  if(quads_is_amp_endpoint()){
    return;
  }
  $object_name = array(
      'ajax_url'               => admin_url( 'admin-ajax.php' ), 
      'quads_front_nonce'   => wp_create_nonce('quads_ajax_check_front_nonce')
  );
  $suffix = ( quadsIsDebugMode() ) ? '' : '.min'; 
  global $quads_options;
  $quads_options = quads_get_settings();
  if(isset($quads_options['ad_performance_tracking'])  && $quads_options['ad_performance_tracking'] == true ){
  wp_enqueue_script( 'quads_ads_front', QUADS_PLUGIN_URL . 'assets/js/performance_tracking' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
}
  wp_localize_script('quads_ads_front', 'quads_analytics', $object_name);
}  
  
/**
* Ajax handler to get ad impression in NON AMP
* @return type void
*/
public function quads_insert_ad_impression(){  

  if ( ! isset( $_POST['quads_front_nonce'] ) ){
      return; 
   }
  if ( !wp_verify_nonce( $_POST['quads_front_nonce'], 'quads_ajax_check_front_nonce' ) ){
     return;  
  }  
  $ad_ids = array_map('sanitize_text_field', $_POST['ad_ids']);
  if($ad_ids){
      
      foreach ($ad_ids as $ad_id){
          
       if($ad_id){

          $this->quads_insert_impression($ad_id);  
          
        }
      }//Foreach closed     
  }        
 wp_die();           
}

/**
 * Ajax handler to get ad impression in AMP
 * @return type void
 */
public function quads_insert_ad_impression_amp(){  
        
  if ( ! isset( $_GET['quads_front_nonce'] ) ){
      return; 
    }
  if ( !wp_verify_nonce( $_GET['quads_front_nonce'], 'quads_ajax_check_front_nonce' ) ){
      return;  
  }  
                      
  $ad_id       = sanitize_text_field($_GET['event']);
  if($ad_id){
      $this->quads_insert_impression($ad_id);
  }
                            
  wp_die();           
}  

/**
   * Ajax handler to get ad clicks in NON AMP
   * @return type void
   */
  public function quads_insert_ad_clicks(){  
          
    if ( ! isset( $_POST['quads_front_nonce'] ) ){
        return; 
    }
    if ( !wp_verify_nonce( $_POST['quads_front_nonce'], 'quads_ajax_check_front_nonce' ) ){
        return;  
    }      
  
    $ad_id = sanitize_text_field($_POST['ad_id']);
    if($ad_id){     
      $this->quads_insert_clicks($ad_id);
                      
    }                           
    wp_die();           
}

/**
 * Ajax handler to get ad clicks in AMP
 * @return type void
 */
public function quads_insert_ad_clicks_amp(){        
    
  if ( ! isset( $_GET['quads_front_nonce'] ) ){
      return; 
    }
  if ( !wp_verify_nonce( $_GET['quads_front_nonce'], 'quads_ajax_check_front_nonce' ) ){
      return;  
  }  
  
  $ad_id = sanitize_text_field($_GET['event']);
  if($ad_id){     
    $this->quads_insert_clicks($ad_id );
  }                           
  wp_die();           
}

/**
 * Here, We are enquing amp scripts.
 * @param type $data
 * @return string
 */
public function quads_enque_analytics_amp_script($data){
  if ( empty( $data['amp_component_scripts']['amp-analytics'] ) ) {
          $data['amp_component_scripts']['amp-analytics'] = 'https://cdn.ampproject.org/v0/amp-analytics-latest.js';
  }
  if ( empty( $data['amp_component_scripts']['amp-bind'] ) ) {
          $data['amp_component_scripts']['amp-bind'] = 'https://cdn.ampproject.org/v0/amp-bind-0.1.js';
  }
  if ( empty( $data['amp_component_scripts']['amp-user-notification'] ) ) {
          $data['amp_component_scripts']['amp-user-notification'] = 'https://cdn.ampproject.org/v0/amp-user-notification-0.1.js';
  }
  if ( empty( $data['amp_component_scripts']['amp-ad'] ) ) {
          $data['amp_component_scripts']['amp-ad'] = 'https://cdn.ampproject.org/v0/amp-ad-latest.js';
  }
  if ( empty( $data['amp_component_scripts']['amp-iframe'] ) ) {
          $data['amp_component_scripts']['amp-iframe'] = 'https://cdn.ampproject.org/v0/amp-iframe-latest.js';
  }
  return $data;         
}

/**
 * Here, We are adding amp analytics tag for every ad serve on page
 */
public function quads_add_analytics_amp_tags(){
  global $quads_shortcode_ids;
                      
    if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
        
    $amp_ads_id = json_decode(get_transient('quads_transient_amp_ids'), true);    
    
    if(!empty($amp_ads_id)){
        
      $amp_ads_id = array_unique($amp_ads_id);  
      
    }   
    
    
    $nonce                = wp_create_nonce('quads_ajax_check_front_nonce');        
    $ad_impression_url    = admin_url('admin-ajax.php?action=quads_insert_ad_impression_amp&quads_front_nonce='.$nonce);                              
    $ad_clicks_url        = admin_url('admin-ajax.php?action=quads_insert_ad_clicks_amp&quads_front_nonce='.$nonce);                              

        require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
        $api_service = new QUADS_Ad_Setup_Api_Service();
        $quads_ads = $api_service->getAdDataByParam('quads-ads');
        if(isset($quads_ads['posts_data'])){        
          foreach($quads_ads['posts_data'] as $key => $value){
            
            $ads =$value['post_meta'];
            if($value['post']['post_status']== 'draft'){
              continue;
            }
            if(isset($ads['enabled_on_amp']) && !$ads['enabled_on_amp']){
              continue;
            }
            if(!isset($ads['position'])){
              continue;
            }
            if((isset($ads['position']) && $ads['position']=='ad_shortcode') && is_array($quads_shortcode_ids) && !in_array($ads['ad_id'],$quads_shortcode_ids))
            {
              continue;
            }
            if(isset($ads['ad_id'])){
              $post_status = get_post_status($ads['ad_id']); 
            }
              else{
                $post_status =  'publish';
              }
              if(isset($ads['random_ads_list'])){
              $ads['random_ads_list'] = unserialize($ads['random_ads_list']);
              }
            if(isset($ads['visibility_include'])){
                $ads['visibility_include'] = unserialize($ads['visibility_include']);
            }
            if(isset($ads['visibility_exclude'])){
                $ads['visibility_exclude'] = unserialize($ads['visibility_exclude']);
            }
            if(isset($ads['targeting_include']))
                $ads['targeting_include'] = unserialize($ads['targeting_include']);
  
            if(isset($ads['targeting_exclude'])){
                $ads['targeting_exclude'] = unserialize($ads['targeting_exclude']);
            }
              $is_on         = quads_is_visibility_on($ads);
              $is_visitor_on = quads_is_visitor_on($ads);
              $is_click_fraud_on = quads_click_fraud_on();
              if($is_on && $is_visitor_on && $is_click_fraud_on && $post_status=='publish'){

                echo '<amp-analytics><script type="application/json">
                {
                  "requests": {
                    "event": "'.esc_url($ad_impression_url).'&event=${eventId}"
                  },
                  "triggers": {
                    "trackPageview": {
                      "on": "visible",
                      "request": "event",
                      "visibilitySpec": {
                        "selector": ".quads-ad'.esc_attr($ads['ad_id']).'",
                        "visiblePercentageMin": 20,
                        "totalTimeMin": 500,
                        "continuousTimeMin": 200
                      },                                  
                      "vars": {
                        "eventId":".quads-ad'.esc_attr($ads['ad_id']).'"
                      }
                    }
                  }
                }</script></amp-analytics>                                  
              ';     
          
          echo '<amp-analytics>
                              <script type="application/json">
                                {
                                  "requests": {
                                    "event": "'.esc_url_raw($ad_clicks_url).'&event=${eventId}"
                                  },
                                  "triggers": {
                                    "trackAnchorClicks": {
                                      "on": "click",
                                      "selector": ".quads-ad'.esc_attr($ads['ad_id']).'",
                                      "request": "event",
                                      "vars": {
                                        "eventId": ".quads-ad'.esc_attr($ads['ad_id']).'"
                                      }
                                    }
                                  }
                                }
                              </script>
                            </amp-analytics>';


              }

          }
        }
                      
      }
}

/**
* Function to insert ad impressions for both (AMP and NON AMP)
* @return void
*/
private function quads_insert_impression($ad_id){ 
  global $wpdb,$quads_options;
  $performance_tracking = isset($quads_options['ad_performance_tracking'])?$quads_options['ad_performance_tracking']:false;
  $exclude_admin = isset($quads_options['exclude_admin_tracking'])?$quads_options['exclude_admin_tracking']:false;
  if($exclude_admin && current_user_can('administrator')){
    return ;
  }
  $todays_date = gmdate('Y-m-d');
  $todays_date = strtotime($todays_date);
  $year = intval(gmdate('Y'));
  $id_array = explode('quads-ad', $ad_id );
  $ad_id = $id_array[1];

  require_once QUADS_PLUGIN_DIR . '/admin/includes/mobile-detect.php';
  $device_name ='';
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

if($performance_tracking){
        $ad_stats = $wpdb->get_row($wpdb->prepare("SELECT id,stats_impressions FROM  {$wpdb->prefix}quads_impressions_{$device_name}  WHERE ad_id = %d AND stats_date = %d",array($ad_id, $todays_date)),ARRAY_A);
        if(isset($ad_stats['id']) && !empty($ad_stats['id'])){
            $updated_impression=$ad_stats['stats_impressions']+1;
            $result =  $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}quads_impressions_{$device_name}  SET stats_impressions = %d WHERE id = %d", array($updated_impression,$ad_stats['id'])));
        }
        else{
            $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}quads_impressions_{$device_name} (ad_id,stats_date,stats_impressions,stats_year) VALUES (%d,%d,%d,%d);",array($ad_id,$todays_date,1,$year)));
        }
}
}

/**
 * Function to insert ad clicks for both (AMP and NON AMP)
 * @return void
 */
private  function quads_insert_clicks($ad_id){

  global $wpdb,$quads_options;
  $log_enabled = isset($quads_options['ad_log'])?$quads_options['ad_log']:false;
  $performance_tracking = isset($quads_options['ad_performance_tracking'])?$quads_options['ad_performance_tracking']:false;
  $exclude_admin = isset($quads_options['exclude_admin_tracking'])?$quads_options['exclude_admin_tracking']:false;
  if($exclude_admin && current_user_can('administrator')){
    return ;
  }
  $todays_date = gmdate('Y-m-d');
  $todays_date = strtotime($todays_date);
  $year = intval( gmdate('Y') );
  $id_array = explode('quads-ad', $ad_id );
  $ad_id = $id_array[1];

  require_once QUADS_PLUGIN_DIR . '/admin/includes/mobile-detect.php';
  $device_name ='';
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

if($performance_tracking){
  $ad_stats = $wpdb->get_row($wpdb->prepare("SELECT id,stats_clicks FROM  {$wpdb->prefix}quads_clicks_{$device_name}  WHERE ad_id = %d AND stats_date = %d",array($ad_id, $todays_date)),ARRAY_A);
  if(isset($ad_stats['id']) && !empty($ad_stats['id'])){
      $updated_clicks=$ad_stats['stats_clicks']+1;
      $result =  $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}quads_clicks_{$device_name}  SET stats_clicks = %d WHERE id = %d", array($updated_clicks,$ad_stats['id'])));
  }
  else{
      $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}quads_clicks_{$device_name} (ad_id,stats_date,stats_clicks,stats_year) VALUES (%d,%d,%d,%d);",array($ad_id,$todays_date,1,$year)));
  }
}

if($log_enabled){
      $referrer_url  = wp_get_referer(); 
      // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: This is the dependant function, nonce verification is done from where this call has been made to this function
      $actual_link  = (isset($_POST['currentLocation'])) ? esc_url($_POST['currentLocation']):'';
      if(empty($actual_link) && isset($_SERVER['HTTP_HOST'])){
        $actual_link = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      }
      $user_ip      =  $this->quads_get_client_ip();
      $browser = $this->quads_get_browser();

      $ad_logs = $wpdb->get_row($wpdb->prepare("SELECT id,log_clicks FROM  {$wpdb->prefix}quads_logs WHERE ad_id = %d AND log_date = %s AND ip_address = %s AND log_url = %d AND browser= %s",array($ad_id, $todays_date,trim($user_ip),trim($actual_link),$browser)),ARRAY_A);
      if(isset($ad_logs['id']) && !empty($ad_logs['id'])){
        $updated_clicks = $ad_logs['log_clicks']+1;
        $result =  $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}quads_logs  SET log_clicks = %d  WHERE id = %d", array($updated_clicks,$ad_logs['id'])));
      }
      else{
        $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}quads_logs (ad_id,log_date,log_clicks,ip_address,log_url,browser,referrer) VALUES (%d,%d,%d,%s,%s,%s,%s);",array( $ad_id, $todays_date, 1, trim($user_ip), $actual_link,trim($browser),trim($referrer_url) )));
      }
  }
}

public function quads_get_client_ip() {
  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if(isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = $_SERVER['REMOTE_ADDR'];
  else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else
      $ipaddress = 'UNKNOWN';
  return $ipaddress;
}

public function quads_get_browser()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Other";

    $browsers = [
        '/msie/i' => 'Internet explorer',
        '/Edg/i' => 'Edge',
        '/YaBrowser/'=>'Yandex',
        '/firefox/i' => 'Firefox',
        '/chrome/i' => 'Chrome',
        '/safari/i' => 'Safari',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/mobile/i' => 'Mobile browser',
    ];

    foreach ($browsers as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
            break;
        }
    }

    return $browser;
}
  
}
if (class_exists('quads_admin_analytics')) {
	$quads_analytics_hooks_obj =new quads_admin_analytics;
  $quads_analytics_hooks_obj->quads_admin_analytics_hooks();        
}
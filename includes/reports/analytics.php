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
     * Function to insert ad impression for both (AMP and NON AMP)
     * @global type $wpdb
     * @param type $ad_id
     * @param type $device_name
     */
    public function quads_insert_impression($ad_id, $device_name='',$referrer_url='',$user_ip='',$actual_link='', $browser=''){
                  
      global $wpdb;
      
      $today = quads_get_date('day');
      $id_array = explode('quads-ad', $ad_id );
      $ad_id = $id_array[1]; 
      
      $referrer_url  = (isset($_POST['referrer'])) ? esc_url($_POST['referrer']):'';
      if(empty($referrer_url) && isset($_SERVER['HTTP_REFERER'])){
        $referrer_url  =  esc_url($_SERVER['HTTP_REFERER']);
      }
      $user_ip      =  $this->quads_get_client_ip();
      $actual_link  = (isset($_POST['currentLocation'])) ? esc_url($_POST['currentLocation']):'';
      if(empty($actual_link) && isset($_SERVER['HTTP_HOST'])){
        $actual_link = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      }

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

      $stats = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d AND `ad_device_name` = %d AND `ad_thetime` = %d AND `referrer` = %d AND `ip_address` = %d AND `url` = %d AND `browser` = %d ", $ad_id, trim($device_name), $today, trim($referrer_url),trim($user_ip),trim($actual_link),trim($browser)));
      if($stats > 0) {
              $wpdb->query("UPDATE `{$wpdb->prefix}quads_stats` SET `ad_impressions` = `ad_impressions` + 1 WHERE `id` = {$stats};");
      } else {
              $wpdb->insert($wpdb->prefix.'quads_stats', array('ad_id' => $ad_id, 'ad_thetime' => $today, 'ad_clicks' => 0, 'ad_impressions' => 1, 'ad_device_name' => trim($device_name),'referrer' => trim($referrer_url),'ip_address' => trim($user_ip),'browser' => trim($browser), 'url'=>$actual_link ));
    
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
      $referrer_url  = (isset($_POST['referrer'])) ? esc_url($_POST['referrer']):'';         
    if(empty($referrer_url) && isset($_SERVER['HTTP_REFERER'])){
      $referrer_url  =  esc_url($_SERVER['HTTP_REFERER']);

    }
      $user_ip       =  $this->quads_get_client_ip();
      $actual_link  = (isset($_POST['currentLocation'])) ? esc_url($_POST['currentLocation']):'';      
      if(empty($actual_link) && isset($_SERVER['HTTP_HOST'])){
        $actual_link = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

      }

      $browser = $_SERVER['HTTP_USER_AGENT'];
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
      if($ad_id){     
        $this->quads_insert_clicks($ad_id,$device_name,$referrer_url,$user_ip,$actual_link, $browser );
                        
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
     $device_name = 'amp';           
     
     if($ad_id){
         $this->quads_insert_impression($ad_id, $device_name);
     }
                                
     wp_die();           
}        



    public function quads_frontend_enqueue(){

      $object_name = array(
          'ajax_url'               => admin_url( 'admin-ajax.php' ), 
          'quads_front_nonce'   => wp_create_nonce('quads_ajax_check_front_nonce')
      );
      $suffix = ( quadsIsDebugMode() ) ? '' : '.min'; 
    //  if ( (function_exists( 'ampforwp_is_amp_endpoint' ) && !ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && !is_amp_endpoint()) {
      wp_enqueue_script( 'quads_ads_front', QUADS_PLUGIN_URL . 'assets/js/performance_tracking' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
    //  }
      wp_localize_script('quads_ads_front', 'quads_analytics', $object_name);


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
                         
        if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            
        $amp_ads_id = json_decode(get_transient('quads_transient_amp_ids'), true);    
        
        if(!empty($amp_ads_id)){
            
          $amp_ads_id = array_unique($amp_ads_id);  
          
        }   
        
        $ad_impression_script = ''; 
        $ad_clicks_script     = '';
        
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
      
                if(isset($ads['ad_id']))
                  $post_status = get_post_status($ads['ad_id']); 
                  else
                    $post_status =  'publish';
      
                  if(isset($ads['random_ads_list']))
                  $ads['random_ads_list'] = unserialize($ads['random_ads_list']);
               if(isset($ads['visibility_include']))
                   $ads['visibility_include'] = unserialize($ads['visibility_include']);
               if(isset($ads['visibility_exclude']))
                   $ads['visibility_exclude'] = unserialize($ads['visibility_exclude']);
      
               if(isset($ads['targeting_include']))
                   $ads['targeting_include'] = unserialize($ads['targeting_include']);
      
               if(isset($ads['targeting_exclude']))
                   $ads['targeting_exclude'] = unserialize($ads['targeting_exclude']);
                  $is_on         = quads_is_visibility_on($ads);
                  $is_visitor_on = quads_is_visitor_on($ads);
                  if($is_on && $is_visitor_on && $post_status=='publish'){
                    $ad_impression_script .= '<amp-analytics><script type="application/json">
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
              
              $ad_clicks_script .='<amp-analytics>
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
                         
          echo $ad_impression_script; 
          echo $ad_clicks_script;
         }
    }
    
    
    /**
     * Function to insert ad clicks for both (AMP and NON AMP)
     * @global type $wpdb
     * @param type $ad_id
     * @param type $device_name
     */
    public function quads_insert_clicks($ad_id, $device_name='',$referrer_url='',$user_ip='',$actual_link='', $browser=''){
      global $wpdb;

      $today = quads_get_date('day');
      $id_array = explode('quads-ad', $ad_id );

      $ad_id = $id_array[1]; 

      $stats = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d AND `ad_device_name` = %d AND `ad_thetime` = %d AND `referrer` = %d AND `ip_address` = %d AND `url` = %d AND `browser` = %d ", $ad_id, trim($device_name), $today, trim($referrer_url),trim($user_ip),trim($actual_link),trim($browser)));
      if( $stats == NULL ){
        $stats =  $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d AND `ad_device_name` = %d", $ad_id, $device_name ) )  ;
      }
      if($stats > 0) {
              $wpdb->query("UPDATE `{$wpdb->prefix}quads_stats` SET `ad_clicks` = `ad_clicks` + 1 WHERE `id` = {$stats};");
              $row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = `{$wpdb->prefix}quads_stats` AND column_name = 'Beginning_of_post'"  );
         if(empty($row)){
             $wpdb->query("ALTER TABLE `{$wpdb->prefix}quads_stats` ADD Beginning_of_post INT(1) NOT NULL DEFAULT 0, ADD End_of_post INT(1) NOT NULL DEFAULT 0, ADD Middle_of_post INT(1) NOT NULL DEFAULT 0, ADD After_more_tag INT(1) NOT NULL DEFAULT 0 ");
         }
      } else {
        
              $wpdb->insert($wpdb->prefix.'quads_stats', array('ad_id' => $ad_id, 'ad_thetime' => $today, 'ad_clicks' => 0, 'ad_impressions' => 1, 'ad_device_name' => trim($device_name),'referrer' => trim($referrer_url),'ip_address' => trim($user_ip),'browser' => trim($browser),'url' => trim($actual_link) ));
           

            }        
        
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
            $device_name = 'amp';            
            
            $referrer_url  = (isset($_SERVER['HTTP_REFERER'])) ? esc_url($_SERVER['HTTP_REFERER']):'';
            $user_ip       =  $this->quads_get_client_ip();
            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $browser = $_SERVER['HTTP_USER_AGENT'];
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
            if($ad_id){     
              
                $this->quads_insert_clicks($ad_id,$device_name,$referrer_url,$user_ip,$actual_link, $browser );
                
            }                           
           wp_die();           
    }

}
if (class_exists('quads_admin_analytics')) {
	$quads_analytics_hooks_obj =new quads_admin_analytics;
        $quads_analytics_hooks_obj->quads_admin_analytics_hooks();        
}

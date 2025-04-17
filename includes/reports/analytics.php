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
  if ( !wp_verify_nonce(  sanitize_text_field( wp_unslash( $_POST['quads_front_nonce'] ) ), 'quads_ajax_check_front_nonce' ) ){
     return;  
  }  
  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
  $ad_ids = ( ! empty( $_POST['ad_ids'] )) ? array_map('sanitize_text_field', $_POST['ad_ids']) : false;  
  
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
                  
      global $wpdb,$quads_options;

      $exclude_admin = isset($quads_options['exclude_admin_tracking'])?$quads_options['exclude_admin_tracking']:false;
      if($exclude_admin && current_user_can('administrator')){
        return ;
      }
      
      $today = quads_get_date('day');
      $id_array = explode('quads-ad', $ad_id );
      $ad_id = $id_array[1]; 
      
      $referrer_url  = wp_get_referer();      
      $todays_date = '';
      $todays_date = gmdate('Y-m-d');
      $year = gmdate("Y");
      $user_ip      =  $this->quads_get_client_ip();
      // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: This is the dependant function, nonce verification is done from where this call has been made to this function
      $actual_link  = (isset($_POST['currentLocation'])) ?  sanitize_text_field( wp_unslash( $_POST['currentLocation'] ) ) :'';
      if(empty($actual_link) && isset($_SERVER['HTTP_HOST'])){
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized
        $actual_link = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      }
      
      $browser =  ( isset( $_SERVER['HTTP_USER_AGENT'] ))? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) :'';
      
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
      // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
     $current_ad_stat = $wpdb->get_row($wpdb->prepare("SELECT id,ad_impressions FROM  {$wpdb->prefix}quads_stats  WHERE ad_id = %d AND ad_device_name = %s AND ad_thetime = %d AND referrer = %s AND ip_address = %s AND url = %s",array($ad_id, trim($device_name), $today, trim($referrer_url),trim($user_ip),trim($actual_link))),ARRAY_A);
     if(isset($current_ad_stat['id']) && !empty($current_ad_stat['id']))
     {
      $updated_impression=$current_ad_stat['ad_impressions']+1;
      // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
      $result =  $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}quads_stats SET ad_impressions = %d  WHERE id = %d", array($updated_impression,$current_ad_stat['id'])));
     }
     else{
      // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
      $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}quads_stats (ad_id,ad_thetime,ad_clicks,ad_impressions,ad_device_name,ip_address,referrer,browser,url) VALUES (%d,%d,%d,%d,%s,%s,%s,%s,%s);",array( $ad_id, $today, 0, 1,  trim($device_name), trim($user_ip) ,trim($referrer_url),trim($browser), $actual_link )));
     }
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
     $current_adstat_single = $wpdb->get_row($wpdb->prepare("SELECT id,ad_impressions,date_impression FROM  {$wpdb->prefix}quads_single_stats_  WHERE ad_id = %d AND ad_date = %s",array($ad_id, $todays_date)),ARRAY_A);
     if(isset($current_adstat_single['id']) && !empty($current_adstat_single['id']))
     {
      $updated_ad_impression=$current_adstat_single['ad_impressions']+1;
      $updated_date_impression=$current_adstat_single['date_impression']+1;
      // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
      $result =  $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}quads_single_stats_ SET ad_impressions = %d , date_impression = %d  WHERE id = %d", array($updated_ad_impression,$updated_date_impression,$current_adstat_single['id'])));
     }
     else{
      // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
      $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}quads_single_stats_ (ad_id,ad_thetime,ad_clicks,ad_impressions,ad_date,date_click,ad_year,date_impression) VALUES (%d,%d,%d,%d,%s,%s,%s,%s);",array($ad_id,0,0,1,$todays_date,0,$year,1)));
     }
}

public function quads_get_client_ip() {
  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
  else if(isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
  else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
  else if(isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED'] ) );
  else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED_FOR'] ) );
  else if(isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED'] ) );
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
      // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized
      if ( ! isset( $_POST['quads_front_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['quads_front_nonce'] ) ), 'quads_ajax_check_front_nonce' ) ){
         return;  
      }      
      
      $ad_id = ( isset( $_POST['ad_id'] ) )? sanitize_text_field( wp_unslash( $_POST['ad_id'] ) ) : false; 
      $referrer_url  = wp_get_referer();        
      $user_ip       =  $this->quads_get_client_ip();
      // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized
      $actual_link  = (isset($_POST['currentLocation'])) ? sanitize_text_field( wp_unslash( $_POST['currentLocation'] ) ) :'';      
      if(empty($actual_link) && isset($_SERVER['HTTP_HOST'])){
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized
        $actual_link = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

      }
      // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
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
      if ( !wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['quads_front_nonce'] ) ), 'quads_ajax_check_front_nonce' ) ){
         return;  
      }  
                         
     $ad_id       =  ( isset( $_GET['event'] ))? sanitize_text_field( wp_unslash( $_GET['event'] ) ) : false;           
     $device_name = 'amp';           
     
     if($ad_id){
         $this->quads_insert_impression($ad_id, $device_name);
     }
                                
     wp_die();           
}        



    public function quads_frontend_enqueue(){
      if(quads_is_amp_endpoint())
      {
        return;
      }
      $object_name = array(
          'ajax_url'               => admin_url( 'admin-ajax.php' ), 
          'quads_front_nonce'   => wp_create_nonce('quads_ajax_check_front_nonce')
      );
      $suffix = ( quadsIsDebugMode() ) ? '' : '.min'; 
    //  if ( (function_exists( 'ampforwp_is_amp_endpoint' ) && !ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && !is_amp_endpoint()) {
      global $quads_options;
      $quads_options = quads_get_settings();
      if(isset($quads_options['ad_performance_tracking'])  && $quads_options['ad_performance_tracking'] == true ){
      wp_enqueue_script( 'quads_ads_front', QUADS_PLUGIN_URL . 'assets/js/performance_tracking' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
    //  }
    }
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
     * Function to insert ad clicks for both (AMP and NON AMP)
     * @global type $wpdb
     * @param type $ad_id
     * @param type $device_name
     */
    public function quads_insert_clicks($ad_id, $device_name='',$referrer_url='',$user_ip='',$actual_link='', $browser=''){
      global $wpdb ,$quads_options;
      $exclude_admin = isset($quads_options['exclude_admin_tracking'])?$quads_options['exclude_admin_tracking']:false;
      if($exclude_admin && current_user_can('administrator')){
        return ;
      }

      $today = quads_get_date('day');
      $id_array = explode('quads-ad', $ad_id );

      $ad_id = $id_array[1]; 
      $todays_date = gmdate('Y-m-d');
      $year = gmdate("Y"); 
      $device_name=substr($device_name, 0, 20);
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
      $current_click_stat = $wpdb->get_row($wpdb->prepare("SELECT id,ad_clicks FROM  {$wpdb->prefix}quads_stats  WHERE ad_id = %d AND ad_device_name = %s AND ad_thetime = %d AND referrer = %s AND ip_address = %s AND url = %s",array($ad_id, trim($device_name), $today, trim($referrer_url),trim($user_ip),trim($actual_link))),ARRAY_A);
      if(isset($current_click_stat['id']) && !empty($current_click_stat['id']))
      {
       $updated_clicks=$current_click_stat['ad_clicks']+1;
       // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
       $result =  $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}quads_stats SET ad_clicks = %d  WHERE id = %d", array($updated_clicks,$current_click_stat['id'])));
      }
      else{
        $ad_thetime = $today; //%s
        $ad_clicks = 1; //%d
        $ad_impressions = 1; //%d
        $ad_device_name = trim($device_name); //%s
        $referrer = trim($referrer_url);
        $ip_address = trim($user_ip); //%s
        $browser = trim($browser); //%s
        $url = trim($actual_link); //%s
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}quads_stats (ad_id,ad_thetime,ad_clicks,ad_impressions,ad_device_name,ip_address,referrer,browser,url) VALUES (%d,%d,%d,%d,%s,%s,%s,%s,%s);",array( $ad_id, $ad_thetime,  $ad_clicks,  $ad_impressions,  $ad_device_name, $ip_address , $referrer, $browser, $url )));
      }

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
      $current_clicks_single = $wpdb->get_row($wpdb->prepare("SELECT id,ad_clicks,date_click FROM  {$wpdb->prefix}quads_single_stats_  WHERE ad_id = %d AND ad_date = %s",array($ad_id, $todays_date)),ARRAY_A);
      if(isset($current_clicks_single['id']) && !empty($current_clicks_single['id']))
      {
       $updated_ad_clicks=$current_clicks_single['ad_clicks']+1;
       $updated_date_clicks=$current_clicks_single['date_click']+1;
       // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
       $result =  $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}quads_single_stats_ SET ad_clicks = %d , date_click = %d  WHERE id = %d", array($updated_ad_clicks,$updated_date_clicks,$current_clicks_single['id'])));
      }
      else{
              $ad_thetime = 0; //%s
              $ad_clicks = 1; //%d
              $ad_impressions = 1; //%d
              $ad_date = $todays_date; //%s
              $date_click = 1; //%d
              $ad_year = $year; //%s
              $date_impression = 1; //%s
              // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
              $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}quads_single_stats_ (ad_id,ad_thetime,ad_clicks,ad_impressions,ad_date,date_click,ad_year,date_impression) VALUES (%d,%d,%d,%d,%s,%s,%s,%s);",array($ad_id,$ad_thetime,$ad_clicks,$ad_impressions,$ad_date,$date_click,$ad_year,$date_impression)));
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
            if ( !wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['quads_front_nonce'] ) ), 'quads_ajax_check_front_nonce' ) ){
               return;  
            }  
            
            $ad_id =  ( isset( $_GET['event'] ) )? sanitize_text_field( wp_unslash( $_GET['event'] ) ) : false;
            $device_name = 'amp';            
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $referrer_url  = (isset($_SERVER['HTTP_REFERER'])) ? esc_url($_SERVER['HTTP_REFERER']):'';
            $user_ip       =  $this->quads_get_client_ip();
             // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized
            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
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
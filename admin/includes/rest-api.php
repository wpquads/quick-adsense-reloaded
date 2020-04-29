<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class QUADS_Ad_Setup_Api {
                
        private static $instance;   
        private $api_service = null;
        private $migration_service = null;

        private function __construct() {
            
            if($this->api_service == null){
                require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
                $this->api_service = new QUADS_Ad_Setup_Api_Service();
            }
            if($this->migration_service == null){
                require_once QUADS_PLUGIN_DIR . '/admin/includes/migration-service.php';
                $this->migration_service = new QUADS_Ad_Migration();
            }   
            
            add_action( 'rest_api_init', array($this, 'registerRoute'));
                                 
        }
                
        public static function getInstance() {
            
            if ( null == self::$instance ) {
                self::$instance = new self;
            }
		    return self::$instance;
        }
        
        public function registerRoute(){
            
            register_rest_route( 'quads-route', 'get-ads-list', array(
                    'methods'    => 'GET',
                    'callback'   => array($this, 'getAdList'),
                    'permission_callback' => function(){
                        return current_user_can( 'manage_options' );
                    }
            ));
            register_rest_route( 'quads-route', 'change-mode', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'changeMode'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'ad-more-action', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'adMoreAction'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'update-ad', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'updateAd'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'update-settings', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'updateSettings'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'validate-ads-txt', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'validateAdsTxt'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'send-customer-query', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'sendCustomerQuery'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'get-ad-by-id', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getAdById'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'get-settings', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getSettings'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'get-condition-list', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getConditionList'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'export-settings', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'exportSettings')                
            ));
            register_rest_route( 'quads-route', 'get-quads-info', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getQuadsInfo'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'get-user-role', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getUserRole'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'get-tags', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getTags'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'quads-route', 'get-plugins', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getPlugins'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));   
             register_rest_route( 'quads-route', 'get-add-next-id', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'getAddNextId'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
             register_rest_route( 'quads-route', 'quads_subscribe_newsletter', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'quadsSubscribeNewsletter'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
               register_rest_route( 'quads-route', 'import-ampforwp-ads', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'importampforwpads'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
                      
        }  


        public function importampforwpads(){
          return  $this->migration_service->quadsImportadsforwp(); 
        }           
        public function quadsSubscribeNewsletter($request){
            $parameters = $request->get_params();
            $api_url = 'http://magazine3.company/wp-json/api/central/email/subscribe';
            $api_params = array(
            'name' => sanitize_text_field($parameters['name']),
            'email'=> sanitize_text_field($parameters['email']),
            'website'=> sanitize_text_field($parameters['website']),
            'type'=> 'quads'
            );
            $response = wp_remote_post( $api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
            $response = wp_remote_retrieve_body( $response );
            echo $response;
            die;
        }

        public function changeMode($request){
            
            $parameters = $request->get_params();
            $mode       = '';
            
            if(isset($parameters['mode'])){
                $mode   = sanitize_text_field($parameters['mode']);
            }

            $response = update_option('quads-mode', $mode);            

            return array('status' => 't');                        

        }
        public function getPlugins($request){

            $response = array();
            $search   = '';

            $parameters = $request->get_params();

            if(isset($parameters['search'])){
                $search   = $parameters['search'];
            }

            $response = $this->api_service->getPlugins($search);
            if($response){
                return array('status' => 't', 'data' => $response);
            }else{
                return array('status' => 'f', 'data' => 'data not found');
            }
            
            return $response;

        }

        public function getTags($request){

            $response = array();
            $search   = '';

            $parameters = $request->get_params();

            if(isset($parameters['search'])){
                $search   = $parameters['search'];
            }

            $response = $this->api_service->getConditionList('tags', $search, 'diff');
            if($response){
                return array('status' => 't', 'data' => $response);
            }else{
                return array('status' => 'f', 'data' => 'data not found');
            }
            
            return $response;

        }

        public function getUserRole($request){

            $response = array();
            $search   = '';

            $parameters = $request->get_params();

            if(isset($parameters['search'])){
                $search   = $parameters['search'];
            }

            $result = $this->api_service->getConditionList('user_type', $search);

            if($result){                
                return array('status' => 't', 'data' => $result);
            }else{
                return array('status' => 'f', 'data' => array());
            }
            
            return $response;
        }
        public function getQuadsInfo(){
            require_once QUADS_PLUGIN_DIR . 'includes/admin/tools.php';
            $info = quads_tools_sysinfo_get();
            return array('info' => $info);
        }
        public function exportSettings(){

            $settings = array();
	        $settings = get_option( 'quads_settings' );
            header( 'Content-Type: application/json; charset=utf-8' );
	        header( 'Content-Disposition: attachment; filename=' . apply_filters( 'quads_settings_export_filename', 'quads-settings-export-' . date( 'm-d-Y' ) ) . '.json' );
            header( "Expires: 0" );
            return   $settings ;	                   
        }
        public function adMoreAction($request){

            $response   = array();
            $parameters = $request->get_params();
            $action     = $parameters['action'];
            $ad_id      = $parameters['ad_id'];
            $result     = null;
            
            if($action){

                switch ($action) {

                    case 'publish':
                        $result = $this->api_service->changeAdStatus($ad_id, 'publish');
                        if($result){
                            $response = array('status'=> 't', 'msg' => 'Changed Successfully', 'data' => array());
                        }
                        break;
                    case 'draft':
                        $result = $this->api_service->changeAdStatus($ad_id, 'draft');
                        if($result){
                            $response = array('status'=> 't', 'msg' => 'Changed Successfully', 'data' => array());
                        }    
                        break;
                    case 'duplicate':
                        $new_ad_id = $this->api_service->duplicateAd($ad_id);
                        if($new_ad_id){
                            $data     = $this->api_service->getAdById($new_ad_id);                            
                            $response = array('status'=> 't', 'msg' => 'Duplicated Successfully', 'data' => $data);
                        }
                        break;
                    case 'delete':
                        $result = $this->api_service->deleteAd($ad_id);
                        if($result){
                            $response = array('status'=> 't', 'msg' => 'Deleted Successfully', 'data' => array());
                        }
                        break;        
                    
                    default:
                        # code...
                        break;
                }

            }

            return $response;
        }
        public function sendCustomerQuery($request){

             $parameters = $request->get_params();
             
               
             $customer_type  = 'Are you a premium customer ? No';
             $message        = sanitize_textarea_field($parameters['message']); 
             $email          = sanitize_text_field($parameters['email']); 
             $premium_cus    = sanitize_text_field($parameters['type']);                
             
             if($premium_cus == 'yes'){
                $customer_type  = 'Are you a premium customer ? Yes';
             }
             
             $message = '<p>'.$message.'</p><br><br>'
                     . $customer_type
                     . '<br><br>'.'Query from WP Quads plugin support tab <br> User Website URL: '.site_url();
             
             if($email && $message){
                           
                 //php mailer variables        
                 $sendto    = 'team@magazine3.com';
                 $subject   = "WP Quads Customer Query";
                 
                 $headers[] = 'Content-Type: text/html; charset=UTF-8';
                 $headers[] = 'From: '. esc_attr($email);            
                 $headers[] = 'Reply-To: ' . esc_attr($email);
                 // Load WP components, no themes.                      
                 $sent = wp_mail($sendto, $subject, $message, $headers); 
     
                 if($sent){
     
                    return array('status'=>'t');
     
                 }else{
     
                    return array('status'=>'f');
     
                 }
                 
             }else{
                return array('status'=>'f', 'msg' => 'Please provide message and email');
             }
        }
        public function validateAdsTxt($request){

            $response = array();

            $parameters = $request->get_params();

            if($parameters[0]){
                $result = $this->api_service->validateAdsTxt($parameters[0]);
                if($result['errors']){
                    $response['errors'] = $result['errors'];
                }else{
                    $response['valid'] = true;
                }
            }
            return $response;
           
        }        
        public function getSettings($request){

            $quads_settings = get_option('quads_settings');            
            $quads_settings['QckTags'] = isset($quads_settings['quicktags']['QckTags']) ? $quads_settings['quicktags']['QckTags'] : false;
            $quads_settings['license'] = get_option( 'quads_wp_quads_pro_license_active' );
            $post_types = get_post_types();
            $add = array('none' => 'Exclude nothing');
            $quads_settings['auto_ads_get_post_types'] =  $add + $post_types;
            $quads_settings['autoads_excl_user_roles'] =  array_merge(array('none' => 'Exclude nothing'), $this->quads_get_user_roles_api());
            return $quads_settings;
        }
        public function getConditionList($request_data){

            $response = array();
            $search   = '';

            $parameters = $request_data->get_params();

            if(isset($parameters['search'])){
                $search   = $parameters['search'];
            }

            if(isset($parameters['condition'])){
                $response = $this->api_service->getConditionList($parameters['condition'], $search);
            }else{
                $response =  array('status' => '404', 'message' => 'property type is required');
            }
            return $response;

            
        }
        public function getAdById($request_data){

            $response = array();

            $parameters = $request_data->get_params();

            if(isset($parameters['ad-id'])){
                $response = $this->api_service->getAdById($parameters['ad-id']);
            }else{
                $response =  array('status' => '404', 'message' => 'Ad id is required');
            }
            return $response;
           
        }
        public function getAddNextId($request_data){
        global $quads_options;
        $response = array();

        $parameters = $request_data->get_params();


        $postCount = !empty($_POST['count']) ? $_POST['count'] : 1;


        $count = isset($quads_options['ads']) ? count ($quads_options['ads']) + $postCount : 10 + $postCount;


        $args = array();
        // subtract 10 widget ads
        //$args['id'] = $count-10;
        $args['id'] = $count-getTotalWidgets();
        $args['name'] = 'Ad ' . $args['id'];


        return $args;

        }
        public function getAdList(){
            
            $search_param = '';
            $rvcount      = 10;
            $attr         = array();
            $paged        =  1;
            $offset       =  0;
            $post_type    = 'quads-ads';

            if(isset($_GET['page'])){
                $paged    = sanitize_text_field($_GET['page']);
            }
            if(isset($_GET['posts_per_page'])){
                $rvcount = sanitize_text_field($_GET['posts_per_page']);
            }            
            $result = $this->api_service->getAdDataByParam($post_type, $attr, $rvcount, $paged, $offset, $search_param);                       
            return $result;
                        
        }
        public function updateSettings($request_data){
            
            $response        = array();
            $parameters      = $request_data->get_params();
            $file            = $request_data->get_file_params();
            
            if(isset($file['file'])){

                $parts = explode( '.',$file['file']['name'] );                
                if( end($parts) != 'json' ) {
                    $response = array('status' => 'f', 'msg' =>  __( 'Please upload a valid .json file', 'quick-adsense-reloaded' ));                   
                }
              
                $import_file = $file['file']['tmp_name'];
                if( empty( $import_file ) ) {
                    $response = array('status' => 'f', 'msg' =>  __( 'Please upload a file to import', 'quick-adsense-reloaded' ));                                       
                }
                
                $settings = json_decode( file_get_contents( $import_file ), true);
                update_option( 'quads_settings', $settings );
                $response = array('file_status' => 't','status' => 't', 'msg' =>  __( 'file uploaded successfully', 'quick-adsense-reloaded' ));                                       

            }else{
                if(isset($parameters['settings'])){
                    $result      = $this->api_service->updateSettings(json_decode($parameters['settings'], true));
                    if($result){
                        $response = array('status' => 't', 'msg' =>  __( 'Settings has been saved successfully', 'quick-adsense-reloaded' ));                                               
                    }
                }
            }
            
            return $response;    
        }
        public function updateAd($request_data){

            $parameters = $request_data->get_params();                                   
            $ad_id      = $this->api_service->updateAdData($parameters);            
            if($ad_id){
                return array('status' => 't', 'ad_id' => $ad_id);
            }else{
                return array('status' => 'f', 'ad_id' => $ad_id);
            }     
        } 
    /**
 * 
 * Get all user roles
 * 
 * @global array $wp_roles
 * @return array
 */
public function quads_get_user_roles_api() {
   global $wp_roles;
   $roles = array();

   foreach ( $wp_roles->roles as $role ) {
      $value = str_replace( ' ', null, strtolower( $role["name"] ) );
      $roles[$value] = $role["name"];
   }
   return $roles;
}
     
       
}
if(class_exists('QUADS_Ad_Setup_Api')){
    QUADS_Ad_Setup_Api::getInstance();
}

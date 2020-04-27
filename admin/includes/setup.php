<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class QUADS_Ad_Setup {
                
        private static $instance;      
        private $migration_service = null;
        private $api_service = null;

        private function __construct() {
            
            if($this->migration_service == null){
                require_once QUADS_PLUGIN_DIR . '/admin/includes/migration-service.php';
                $this->migration_service = new QUADS_Ad_Migration();
            }   
            if($this->api_service == null){
                require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
                $this->api_service = new QUADS_Ad_Setup_Api_Service();
            }                    
                                 
        }
        public function quadsAdSetupHooks(){
            
            add_action( 'init', array($this, 'quadsAdminInit'));  
            add_action( 'upgrader_process_complete', array($this, 'quadsUpgradeToNewDesign') ,10, 2);            
            add_action( 'wp_ajax_quads_sync_ads_in_new_design', array($this, 'quadsSyncAdsInNewDesign') );

        }
                
        public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		    return self::$instance;
        }
        public function quadsAdminInit(){            
            $this->migration_service->quadsSaveAllAdToNewDesign();           
        }
        
        public function quadsSyncAdsInNewDesign(){
               
            check_ajax_referer( 'quads_ajax_nonce', 'nonce' );

            if( ! current_user_can( 'manage_options' ) )
                return;

                $quads_settings = get_option('quads_settings');

                if(isset($quads_settings['ads'])){               
                    
                    $i=1;
                    foreach($quads_settings['ads'] as $key => $value){                            
    
                        if($key === 'ad'.$i){
                            
                            $post_id = quadsGetPostIdByMetaKeyValue('quads_ad_old_id', $key); 
                            
                            if($post_id){                            
                                $value['ad_id']                      = $post_id;                                                                                                                                            
                            }else{
                                $value['quads_ad_old_id']            = $key;                                  
                            }    
                            
                             $parameters['quads_post_meta']       = $value;                                                                                            
                             $this->api_service->updateAdData($parameters, 'old_mode');                            

                        } 
                        
                        $i++;                       
                                            
                    }
    
               }
                   wp_die();         
        }                        

}
if(class_exists('QUADS_Ad_Setup')){
    $quadsAdSetup = QUADS_Ad_Setup::getInstance();
    $quadsAdSetup->quadsAdSetupHooks();
}
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
            
            add_action( 'wp_ajax_quads_sync_ads_in_new_design', array($this, 'quadsSyncAdsInNewDesign') );
             add_action( 'wp_ajax_quads_sync_random_ads_in_new_design', array($this, 'quadsSyncRandomAdsInNewDesign') );
             $this->quads_database_install();

        }
                
        public static function getInstance() {
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
            return self::$instance;
        }

        
        public function quadsSyncAdsInNewDesign(){
               
            check_ajax_referer( 'quads_ajax_nonce', 'nonce' );

            if( ! current_user_can( 'manage_options' ) )
                return;
            if(isset($_REQUEST['status']) && $_REQUEST['status'] == 'no'){
                update_option('quads_import_classic_ads_popup', 'no'); 
                return;
            }

                $quads_settings = get_option('quads_settings_backup');
                $flag_adddefault = true;
                 $flag_key_used = false;
                 $ad_count = 1;
                if(isset($quads_settings['ads'])){   
                  foreach($quads_settings['ads'] as $key2 => $value2){  
                        if($key2 === 'ad'.$ad_count){
                            $post_id = quadsGetPostIdByMetaKeyValue('quads_ad_old_id', $key2); 
                            if($post_id){       
                                          
                                array_push($random_ads_slno, $post_id);                                                
                           }
                           $ad_count++;
                        } 
                    }             
                    // exit(print_r($quads_settings['ads']));
                     $ads_total_count = count($quads_settings['ads']); 
                    foreach($quads_settings['ads'] as $key => $value){                            
                        // $i=1;
                        for ($i=1; $i < $ads_total_count ; $i++) { 
                        if($key == 'ad'.$i){

                              if(empty($value['code']) && empty($value['g_data_ad_slot'])){
                                    continue;           
                                  }
                            $post_id = quadsGetPostIdByMetaKeyValue('quads_ad_old_id', $key); 
                            
                            if($post_id){                            
                                $value['ad_id']                      = $post_id;                                                                                                                                    
                            }else{
                                $value['quads_ad_old_id']            = $key;                                  
                            }
                            $visibility_include[0]['type']['label'] = 'Post Type';
                            $visibility_include[0]['type']['value'] = 'post_type';
                            $visibility_include[0]['value']['label'] = 'post';
                            $visibility_include[0]['value']['value'] = 'post';
                            $value['visibility_include'] = $visibility_include;

                            // add position start
                            $position = '';
                            $paragraph_number  = '';
                            $enable_on_end_of_post = ''; 
                            if(isset($quads_settings['pos1']['BegnAds']) && $quads_settings['pos1']['BegnAds'] ){
                                if(isset($quads_settings['pos1']['BegnRnd']) && $quads_settings['pos1']['BegnRnd']== $i){
                                    $position           .= ',beginning_of_post';
                                }
                            }
                            if(isset($quads_settings['pos2']['MiddAds']) && $quads_settings['pos2']['MiddAds'] ){
                                if(isset($quads_settings['pos2']['MiddRnd']) && $quads_settings['pos2']['MiddRnd']== $i){
                                    $position           .= ',middle_of_post';
                                }
                            }
                            if(isset($quads_settings['pos3']['EndiAds']) && $quads_settings['pos3']['EndiAds'] ){
                                if(isset($quads_settings['pos3']['EndiRnd']) && $quads_settings['pos3']['EndiRnd']== $i){
                                    $position           .= ',end_of_post';
                                }
                            }
                            if(isset($quads_settings['pos4']['MoreAds']) && $quads_settings['pos4']['MoreAds'] ){
                                if(isset($quads_settings['pos4']['MoreRnd']) && $quads_settings['pos4']['MoreRnd']== $i){
                                    $position           .= ',after_more_tag';
                                }
                            }
                            if(isset($quads_settings['pos5']['LapaAds']) && $quads_settings['pos5']['LapaAds']) {
                                if(isset($quads_settings['pos5']['LapaRnd']) && $quads_settings['pos5']['LapaRnd']== $i){
                                    $position           .= ',before_last_paragraph';
                                }
                            }
                            if(isset($quads_settings['pos6']['Par1Ads']) && $quads_settings['pos6']['Par1Ads'] ){
                                if(isset($quads_settings['pos6']['Par1Rnd']) && $quads_settings['pos6']['Par1Rnd']== $i){
                                    $value2 =array();
                                    $value2 = $value;
                                     $flag_adddefault = false;
                                    if($post_id){                            
                                        $value2['ad_id']                      = $post_id;                                
                                    }else{
                                        $flag_key_used = true;
                                        $value2['quads_ad_old_id']            =  $key;  
                                        $ad_count++;     
                                    }
                                    $value2['paragraph_number']      = $quads_settings['pos6']['Par1Nup'];
                                    $value2['enable_on_end_of_post'] = $quads_settings['pos6']['Par1Con'];
                                    $value2['visibility_include'] = $visibility_include;
                                    $value2['position']              = 'after_paragraph';
                                    $parameters['quads_post_meta']   = $value2;                                        
                                    $this->api_service->updateAdData($parameters);
                                }
                            }
                            if(isset($quads_settings['pos7']['Par2Ads']) && $quads_settings['pos7']['Par2Ads'] ){
                                if(isset($quads_settings['pos7']['Par2Rnd']) && $quads_settings['pos7']['Par2Rnd']== $i){
                                    $value2 =array();
                                    $value2 = $value;
                                    $flag_adddefault = false;
                                    if($post_id){                            
                                        $value2['ad_id']                      = $post_id;                                
                                    }else{
                                        if($flag_key_used){
                                            $value2['quads_ad_old_id']            =  'ad'.$ad_count; 
                                            $ad_count++;  
                                        }else{
                                            $flag_key_used = true;
                                            $value2['quads_ad_old_id']            =  $key;
                                        }                                    
                                    }
                                    $value2['paragraph_number']      = $quads_settings['pos7']['Par2Nup'];
                                    $value2['enable_on_end_of_post'] = $quads_settings['pos7']['Par2Con'];
                                    $value2['visibility_include'] = $visibility_include;
                                    $value2['position']              = 'after_paragraph';
                                    $parameters['quads_post_meta']   = $value2;                                        
                                    $this->api_service->updateAdData($parameters);
                                }
                            }
                            if(isset($quads_settings['pos8']['Par3Ads']) && $quads_settings['pos8']['Par3Ads'] ){
                                if(isset($quads_settings['pos8']['Par3Rnd']) && $quads_settings['pos8']['Par3Rnd']== $i){
                                    $value2 =array();
                                    $value2 = $value;
                                    $flag_adddefault = false;
                                    if($post_id){                            
                                        $value2['ad_id']                      = $post_id;                                
                                    }else{
                                         if($flag_key_used){
                                            $value2['quads_ad_old_id']            =  'ad'.$ad_count; 
                                            $ad_count++;  
                                        }else{
                                            $flag_key_used = true;
                                            $value2['quads_ad_old_id']            =  $key;
                                        }                                    
                                    }
                                    $value2['paragraph_number']      = $quads_settings['pos8']['Par3Nup'];
                                    $value2['enable_on_end_of_post'] = $quads_settings['pos8']['Par3Con'];
                                    $value2['visibility_include'] = $visibility_include;
                                    $value2['position']              = 'after_paragraph';
                                    $parameters['quads_post_meta']   = $value2;                                        
                                    $this->api_service->updateAdData($parameters);
                                }
                            }

                            if(isset($quads_settings['pos9']['Img1Ads']) &&  $quads_settings['pos9']['Img1Ads']){
                                if(isset($quads_settings['pos9']['Img1Rnd']) && $quads_settings['pos9']['Img1Rnd']== $i){

                                        $value2 =array();
                                        $value2 = $value;
                                        $flag_adddefault = false;
                                        if($post_id){                            
                                            $value2['ad_id']                      = $post_id;                             
                                        }else{
                                            if($flag_key_used){
                                                $value2['quads_ad_old_id']            =  'ad'.$ad_count; 
                                                $ad_count++;  
                                            }else{
                                                $flag_key_used = true;
                                                $value2['quads_ad_old_id']            =  $key;
                                            }                                   
                                        }
                                        $value2['paragraph_number']      = $quads_settings['pos9']['Img1Nup'];
                                        $value2['enable_on_end_of_post'] = $quads_settings['pos9']['Img1Con'];
                                        $value2['visibility_include'] = $visibility_include;
                                        $value2['position']              = 'after_paragraph';
                                        $parameters['quads_post_meta']   = $value2;                                        
                                        $this->api_service->updateAdData($parameters);
                                }

                            }
                            for ($extra_ads=1; $extra_ads < 9; $extra_ads++) { 

                                if(isset($quads_settings['extra'.$extra_ads]['ParAds']) &&  $quads_settings['extra'.$extra_ads]['ParAds']){

                                    if(isset($quads_settings['extra'.$extra_ads]['ParRnd']) && $quads_settings['extra'.$extra_ads]['ParRnd']== $i){
                                            $value2 =array();
                                            $value2 = $value;
                                            $flag_adddefault = false;
                                            if($post_id){                            
                                                $value2['ad_id']             = $post_id;                             
                                            }else{
                                                 if($flag_key_used){
                                                    $value2['quads_ad_old_id']            =  'ad'.$ad_count; 
                                                    $ad_count++;  
                                                }else{
                                                    $flag_key_used = true;
                                                    $value2['quads_ad_old_id']            =  $key;
                                                }                                     
                                            }
                                            $value2['paragraph_number']      = $quads_settings['extra'.$extra_ads]['ParNup'];
                                            $value2['enable_on_end_of_post'] = $quads_settings['extra'.$extra_ads]['ParCon'];
                                            $value2['visibility_include'] = $visibility_include;
                                            $value2['position']              = 'after_paragraph';
                                            $parameters['quads_post_meta']   = $value2;                                        
                                            $this->api_service->updateAdData($parameters);
                                    }
                                }
                            }
    
                            // add position end
                            $position =trim($position,',');
                            if(empty($position) && $flag_adddefault){
                                $value['position']            = 'ad_shortcode';
                                $parameters['quads_post_meta']       = $value;                                        
                                $this->api_service->updateAdData($parameters); 
                            }else if(!empty($position)) {
                                $position_array = explode(',', $position);
                                foreach ($position_array  as $position) {
                                    if(isset($value['quads_ad_old_id'] )){
                                         if($flag_key_used){
                                            $value2['quads_ad_old_id']            =  'ad'.$ad_count; 
                                            $ad_count++;  
                                        }else{
                                            $flag_key_used = true;
                                            $value2['quads_ad_old_id']            =  $key;
                                        }  
                                    }
                                     if(isset($value['ad_id'] )){
                                        if($flag_key_used ){
                                            $value['quads_ad_old_id']      =  'ad'.$ad_count; 
                                            $ad_count++; 
                                        }else{
                                            $value['ad_id']                = $post_id; 
                                        }
                                    }
                                     
                                    $value['position']              = $position;
                                    $parameters['quads_post_meta']  = $value;     
                                    $this->api_service->updateAdData($parameters);
                                }
                            }
                        } 
                       }             
                    }
               }

               $this->quadsSyncRandomAdsInNewDesign();

               return  array('status' => 't', 'data' => 'Ads have been successfully imported'); 
                   wp_die();         
        }                        

/**
 * Here, We create our own database and tables 
 * @global type $wpdb
 */
public function quads_database_install() {
    
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
	global $wpdb;                
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$charset_collate = $engine = '';	
	
	if(!empty($wpdb->charset)) {
		$charset_collate .= " DEFAULT CHARACTER SET {$wpdb->charset}";
	} 
	if($wpdb->has_cap('collation') AND !empty($wpdb->collate)) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$found_engine = $wpdb->get_var("SELECT ENGINE FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".DB_NAME."' AND `TABLE_NAME` = '{$wpdb->prefix}posts';");
        
	if(strtolower($found_engine) == 'innodb') {
		$engine = ' ENGINE=InnoDB';
	}

	$found_tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}quads%';");	
        
	if(!in_array("{$wpdb->prefix}quads_stats", $found_tables)) {
            
		dbDelta("CREATE TABLE `{$wpdb->prefix}quads_stats` (
			`id` bigint(9) unsigned NOT NULL auto_increment,
			`ad_id` int(50) unsigned NOT NULL default '0',			
			`ad_thetime` int(15) unsigned NOT NULL default '0',
			`ad_clicks` int(15) unsigned NOT NULL default '0',
			`ad_impressions` int(15) unsigned NOT NULL default '0',
                        `ad_device_name` varchar(20) NOT NULL default '',
                        `ip_address` varchar(20) NOT NULL default '',
                        `URL` varchar(255) NOT NULL default '',
                        `browser` varchar(20) NOT NULL default '',
                        `referrer` varchar(255) NOT NULL default '',
			PRIMARY KEY  (`id`),
			INDEX `ad_id` (`ad_id`),
			INDEX `ad_thetime` (`ad_thetime`)
		) ".$charset_collate.$engine.";");
                
	}

}

public function quadsSyncRandomAdsInNewDesign(){
    $quads_settings = get_option('quads_settings_backup');
    $random_beginning_of_post = true;
    $random_middle_of_post = true;
    $random_end_of_post = true;
    $random_after_more_tag = true;
    $random_before_last_paragraph = true;
    $random_after_paragraph1 = true;
    $random_after_paragraph2 = true;
    $random_after_paragraph3 = true;
    $random_after_image = true;  
    $quads_ads = $this->api_service->getAdDataByParam('quads-ads');
   if(isset($quads_ads['posts_data'])){
   $random_ads_list =array();
   $random_ads_slno =array();
   $ad_count =1;
foreach($quads_settings['ads'] as $key2 => $value2){  
    if($key2 === 'ad'.$ad_count){
        $post_id = quadsGetPostIdByMetaKeyValue('quads_ad_old_id', $key2); 
        if($post_id){       
                      
            array_push($random_ads_slno, $post_id);                                                
       }
    } 
    $ad_count++;
} 

        foreach($quads_ads['posts_data'] as $key => $value){   

            if(!in_array($value['post_meta']['ad_id'], $random_ads_slno)){
                $missing_data =array();
                    $missing_data['post']  = $value['post'];
                     $missing_data['post_meta']  = $value['post_meta'];
                    $missing_data['visibility_include'] = unserialize($value['post_meta']['visibility_include']);
                    $missing_data['ad_type']       =  $value['post_meta']['ad_type'];
                    $missing_data['random_ads_list']   = unserialize($value['post_meta']['random_ads_list']);
                    $missing_data['position']      =  $value['post_meta']['position'];
                    $missing_data['label']         = $value['post_meta']['label'];
                    $missing_data['ad_id']         = $value['post_meta']['ad_id'];
                      $quads_optionsadd = get_option( 'quads_settings' );
                      if(!isset($quads_optionsadd['ads'][$value['post_meta']['quads_ad_old_id']])){
                        $quads_optionsadd['ads'][$value['post_meta']['quads_ad_old_id']] = $missing_data;

                         update_option( 'quads_settings', $quads_optionsadd );
                      }

            }
                  
       
            if($value['post']['post_status']=='draft'){
            // break;
            continue;
            }
             if($value['post_meta']['code']  || $value['post_meta']['g_data_ad_slot']){
                $random_ads_list[] = array('value'=>$value['post_meta']['ad_id'],'label'=>$value['post_meta']['quads_ad_old_id']);
            }

            if($value['post_meta']['position'] == 'beginning_of_post' && $value['post_meta']['ad_type'] == 'random_ads'){
                $random_beginning_of_post = false;
            }
            if($value['post_meta']['position'] =='middle_of_post' && $value['post_meta']['ad_type'] == 'random_ads'){
                $random_middle_of_post = false;
            }
            if($value['post_meta']['position'] == 'end_of_post' ){
                $random_end_of_post = false;
            }
            if($value['post_meta']['position'] == 'after_more_tag' && $value['post_meta']['ad_type'] == 'random_ads'){
                $random_after_more_tag = false;
            }
             if($value['post_meta']['position'] == 'before_last_paragraph' && $value['post_meta']['ad_type'] == 'random_ads'){
                $random_before_last_paragraph = false;
            }
            if($value['post_meta']['position'] == 'after_paragraph' && $value['post_meta']['ad_type'] == 'random_ads' && $value['post_meta']['label'] =='Random ads after paragraph 1'){
                $random_after_paragraph1 = false;
            }
            if($value['post_meta']['position'] == 'after_paragraph' && $value['post_meta']['ad_type'] == 'random_ads' && $value['post_meta']['label'] =='Random ads after paragraph 2'){
                $random_after_paragraph2 = false;
            }
            if($value['post_meta']['position'] == 'after_paragraph' && $value['post_meta']['ad_type'] == 'random_ads' && $value['post_meta']['label'] =='Random ads after paragraph 3'){
                $random_after_paragraph3 = false;
            }
            if($value['post_meta']['position'] == 'after_image' && $value['post_meta']['ad_type'] == 'random_ads'){
                $random_after_image = false;
            }                    

        }



        if(isset($quads_settings['pos1'])){ 
            if(isset($quads_settings['pos1']['BegnAds']) && $quads_settings['pos1']['BegnAds'] && $random_beginning_of_post){
                if(isset($quads_settings['pos1']['BegnRnd']) && $quads_settings['pos1']['BegnRnd']== 0){ 
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = 'post';
                    $visibility_include[0]['value']['value'] = 'post';
                    $value['visibility_include'] = $visibility_include;
                    $value['ad_type']       = 'random_ads';
                    $value['random_ads_list']   = $random_ads_list;
                    $value['position']      = 'beginning_of_post';  
                    $value['label']         = 'Random ads beginning';         
                    $value['quads_ad_old_id']         = 'ad'.$ad_count;
                     $ad_count++;
                    $parameters['quads_post_meta']  = $value;
                    $this->api_service->updateAdData($parameters);   
                }
            }
        } 
        if(isset($quads_settings['pos2'])){ 
            if(isset($quads_settings['pos2']['MiddAds']) && $quads_settings['pos2']['MiddAds']  && $random_middle_of_post){
                if(isset($quads_settings['pos2']['MiddRnd']) && $quads_settings['pos2']['MiddRnd']== 0){ 
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = 'post';
                    $visibility_include[0]['value']['value'] = 'post';
                    $value['visibility_include'] = $visibility_include;
                    $value['ad_type']       = 'random_ads';
                    $value['random_ads_list']   = $random_ads_list;
                    $value['position']      = 'middle_of_post';  
                    $value['label']         = 'Random ads middle'; 
                    $value['random']        = true;  
                      $value['quads_ad_old_id']         = 'ad'.$ad_count;
                     $ad_count++;
                    $parameters['quads_post_meta']  = $value;
                    $this->api_service->updateAdData($parameters);  
                }
            }
        }
        if(isset($quads_settings['pos3'])){ 

            if(isset($quads_settings['pos3']['EndiAds']) && $quads_settings['pos3']['EndiAds'] && $random_end_of_post){

                if(isset($quads_settings['pos3']['EndiRnd']) && $quads_settings['pos3']['EndiRnd']== 0){ 
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = 'post';
                    $visibility_include[0]['value']['value'] = 'post';
                    $value['visibility_include'] = $visibility_include;
                    $value['ad_type']       = 'random_ads';
                    $value['random_ads_list']   = $random_ads_list;
                    $value['position']      = 'end_of_post';  
                    $value['label']         = 'Random ads end';  
                    $value['random']        = true; 
                      $value['quads_ad_old_id']         = 'ad'.$ad_count;
                     $ad_count++;
                    $parameters['quads_post_meta']  = $value;
                    $this->api_service->updateAdData($parameters);  
                }
            }
        }

        if(isset($quads_settings['pos4'])){ 
            if(isset($quads_settings['pos4']['MoreAds']) && $quads_settings['pos4']['MoreAds'] && $random_after_more_tag){
                if(isset($quads_settings['pos4']['MoreRnd']) && $quads_settings['pos4']['MoreRnd']== 0){ 
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = 'post';
                    $visibility_include[0]['value']['value'] = 'post';
                    $value['visibility_include'] = $visibility_include;
                    $value['ad_type']       = 'random_ads';
                    $value['random_ads_list']   = $random_ads_list;
                    $value['position']      = 'after_more_tag';  
                    $value['label']         = 'Random ads after more';
                    $value['random']        = true;   
                      $value['quads_ad_old_id']         = 'ad'.$ad_count;
                     $ad_count++;
                    $parameters['quads_post_meta']  = $value;
                    $this->api_service->updateAdData($parameters);  
                }
            }
        }
        if(isset($quads_settings['pos5'])){ 
            if(isset($quads_settings['pos5']['LapaAds']) &&  $quads_settings['pos5']['LapaAds'] == 1 && $random_before_last_paragraph){
                if(isset($quads_settings['pos5']['LapaRnd']) && $quads_settings['pos5']['LapaRnd']== 0){ 
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = 'post';
                    $visibility_include[0]['value']['value'] = 'post';
                    $value['visibility_include'] = $visibility_include;
                    $value['ad_type']       = 'random_ads';
                    $value['random_ads_list']   = $random_ads_list;
                    $value['position']      = 'before_last_paragraph';  
                    $value['label']         = 'Random ads before last paragraph'; 
                    $value['random']        = true;  
                      $value['quads_ad_old_id']         = 'ad'.$ad_count;
                     $ad_count++;
                    $parameters['quads_post_meta']  = $value;
                    $this->api_service->updateAdData($parameters);  
                }
            }
        }
        if(isset($quads_settings['pos6'])){ 
            if(isset($quads_settings['pos6']['Par1Ads']) &&  $quads_settings['pos6']['Par1Ads'] && $random_after_paragraph1){
                if(isset($quads_settings['pos6']['Par1Rnd']) && $quads_settings['pos6']['Par1Rnd']== 0){ 
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = 'post';
                    $visibility_include[0]['value']['value'] = 'post';
                    $value['visibility_include'] = $visibility_include;
                    $value['ad_type']       = 'random_ads';
                    $value['random_ads_list']   = $random_ads_list;
                    $value['position']      = 'after_paragraph';
                    $value['paragraph_number']  = $quads_settings['pos6']['Par1Nup'];
                    $value['enable_on_end_of_post'] = $quads_settings['pos6']['Par1Con'];  
                    $value['label']         = 'Random ads after paragraph 1';  
                    $value['random']        = true; 
                    $value['quads_ad_old_id']         = 'ad'.$ad_count;
                     $ad_count++;
                    $parameters['quads_post_meta']  = $value;
                    $this->api_service->updateAdData($parameters);  
                }
            }
        }
        if(isset($quads_settings['pos7'])){ 
            if(isset($quads_settings['pos7']['Par2Ads']) &&  $quads_settings['pos7']['Par2Ads'] && $random_after_paragraph2){
                if(isset($quads_settings['pos7']['Par2Rnd']) && $quads_settings['pos7']['Par2Rnd']== 0){ 
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = 'post';
                    $visibility_include[0]['value']['value'] = 'post';
                    $value['visibility_include'] = $visibility_include;
                    $value['ad_type']       = 'random_ads';
                    $value['random_ads_list']   = $random_ads_list;
                    $value['position']      = 'after_paragraph';  
                    $value['paragraph_number']  = $quads_settings['pos7']['Par2Nup'];
                    $value['enable_on_end_of_post'] = $quads_settings['pos7']['Par2Con'];
                    $value['label']         = 'Random ads after paragraph 2'; 
                    $value['random']        = true; 
                    $value['quads_ad_old_id']         = 'ad'.$ad_count;
                     $ad_count++; 
                    $parameters['quads_post_meta']  = $value;
                    $this->api_service->updateAdData($parameters);  
                }
            }
        }
        if(isset($quads_settings['pos8'])){ 
            if(isset($quads_settings['pos8']['Par3Ads']) &&  $quads_settings['pos8']['Par3Ads'] && $random_after_paragraph3){
                if(isset($quads_settings['pos8']['Par3Rnd']) && $quads_settings['pos8']['Par3Rnd']== 0){ 
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = 'post';
                    $visibility_include[0]['value']['value'] = 'post';
                    $value['visibility_include'] = $visibility_include;
                    $value['ad_type']       = 'random_ads';
                    $value['random_ads_list']   = $random_ads_list;
                    $value['position']      = 'after_paragraph'; 
                    $value['paragraph_number']              = $quads_settings['pos8']['Par3Nup'];
                    $value['enable_on_end_of_post']         = $quads_settings['pos8']['Par3Con']; 
                    $value['label']         = 'Random ads after paragraph 3';  
                    $value['random']        = true; 
                    $value['quads_ad_old_id']         = 'ad'.$ad_count;
                     $ad_count++;
                    $parameters['quads_post_meta']  = $value;
                    $this->api_service->updateAdData($parameters);  
                }
            }
        }
        if(isset($quads_settings['pos9'])){ 
            if(isset($quads_settings['pos9']['Img1Ads']) &&  $quads_settings['pos9']['Img1Ads'] && $random_after_image){
                if(isset($quads_settings['pos9']['Img1Rnd']) && $quads_settings['pos9']['Img1Rnd']== 0){ 
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = 'post';
                    $visibility_include[0]['value']['value'] = 'post';
                    $value['visibility_include'] = $visibility_include;
                    $value['ad_type']       = 'random_ads';
                    $value['random_ads_list']   = $random_ads_list;
                    $value['position']      = 'after_image'; 
                    $value['paragraph_number']  = $quads_settings['pos9']['Img1Nup'];
                    $value['image_number']   = $quads_settings['pos9']['Img1Con']; 
                    $value['label']         = 'Random ads after image';  
                    $value['random']        = true; 
                    $value['quads_ad_old_id']         = 'ad'.$ad_count;
                     $ad_count++;
                    $parameters['quads_post_meta']  = $value;
                    $this->api_service->updateAdData($parameters);  
                }
            }
        }

    }
        update_option('quads_import_classic_ads_popup', 'no'); 

      return  array('status' => 't', 'data' => 'Ads have been successfully imported'); 
    wp_die();         
}  
}
if(class_exists('QUADS_Ad_Setup')){
    $quadsAdSetup = QUADS_Ad_Setup::getInstance();
    $quadsAdSetup->quadsAdSetupHooks();
}
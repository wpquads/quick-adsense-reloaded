<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class QUADS_Ad_Setup_Api {
                
        private static $instance;   
        private $api_service = null;

        private function __construct() {
            
            if($this->api_service == null){
                require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
                $this->api_service = new QUADS_Ad_Setup_Api_Service();
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
                'callback'   => array($this, 'exportSettings') ,
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }               
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
                'callback'   => array($this, 'importampforwp_ads'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
               register_rest_route( 'quads-route', 'import-adsforwp-ads', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'importadsforwp_ads'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
                      
        } 

        public function importadsforwp_ads(){
            global $quads_settings;
             $ad_count = 1;
                if(isset($quads_settings['ads'])){   
                  foreach($quads_settings['ads'] as $key2 => $value2){  
                        if($key2 === 'ad'.$ad_count){
                           $ad_count++;
                        } 
                    }  
                }
            $args = array(
                'post_type' => 'adsforwp'
            );
            $all_ads_post = get_posts( $args );

            if($all_ads_post){
                foreach($all_ads_post as $ads){ 
                  
                    $post_meta = get_post_meta($ads->ID, $key='', true );
                    $ads_post = array(
                                'post_author' => $ads->post_author,
                                'post_title'  => $ads->post_title,                    
                                'post_status' => $ads->post_status,
                                'post_name'   => $ads->post_title,                    
                                'post_type'   => 'quads-ads',
                            );  
                    $post_id          = wp_insert_post($ads_post);    
                    $adsense_type     = 'display_ads';
                    if(isset($post_meta['adsense_type'][0])){
                        if($post_meta['adsense_type'][0] =='in_feed_ads' || $post_meta['adsense_type'][0] =='in_article_ads' || $post_meta['adsense_type'][0] =='adsense_auto_ads'){
                            $adsense_type     = $post_meta['adsense_type'][0];
                        }
                    }  
                    if($post_meta['adsforwp_ad_align'][0]){    
                            switch ($post_meta['adsforwp_ad_align'][0]) {    
                                case 'left':    
                                $align = 0; 
                                break;  
                                case 'center':  
                                $align = 1; 
                                break;  
                                case 'right':   
                                $align = 2; 
                                break;  

                                default:    
                                $align = 3; 
                                break;  
                            }
                            update_post_meta( $post_id, 'align',$align); //xss ok
                        }
                    if(isset($post_meta['adsforwp_ad_margin'][0])){ 
                            update_post_meta( $post_id, 'margin',$post_meta['adsforwp_ad_margin'][0]); 
                        }

                    $visibility_include = array();  
                    $visibility_exclude = array();  
                    $data_group_array = unserialize($post_meta['data_group_array'][0]); 
                    $i =0;  $j =0;  

                    foreach ($data_group_array as $key => $value) { 
                        foreach ($value['data_array'] as $keys => $values) { 
                            $label = '';    
                            switch ($values['key_1']) { 
                                case 'post_type':   
                                $label = 'Post Type';   
                                break;  
                                case 'post_format': 
                                $label = 'Post Format'; 
                                break;  
                                case 'page':    
                                $label = 'Page';    
                                break;  
                            }   
                            if($values['key_2'] == 'equal'){    
                                $visibility_include[$i]['type']['label'] = $label;  
                                $visibility_include[$i]['type']['value'] = 'post_type'; 
                                $visibility_include[$i]['value']['label'] = $label; 
                                $visibility_include[$i]['value']['value'] = esc_html($values['key_3']); 
                                $i++;  

                            }else{  
                                $visibility_exclude[$j]['type']['label'] = $label;  
                                $visibility_exclude[$j]['type']['value'] = 'post_type'; 
                                $visibility_exclude[$j]['value']['label'] = $label; 
                                $visibility_exclude[$j]['value']['value'] = esc_html($values['key_3']); 
                                $j++;   

                            }   
                            update_post_meta( $post_id, 'visibility_include', $visibility_include); 
                            update_post_meta( $post_id, 'visibility_exclude', $visibility_exclude); 
                        }
                    }

                    $visibility_include = array();  
                    $visibility_exclude = array();  
                    $data_group_array = unserialize($post_meta['visitor_conditions_array'][0]); 
                    $i =0;  $j =0;  

                    foreach ($data_group_array as $key => $value) { 
                        foreach ($value['visitor_conditions'] as $keys => $values) { 
                            $label = '';    
                            switch ($values['key_1']) { 
                                case 'device':   
                                $label = 'Device Type';   
                                break;  
                                case 'browser_language': 
                                $label = 'Browser Language'; 
                                break;  
                                case 'url_parameter':    
                                $label = ' URL Parameter';    
                                break;  
                            }   
       
                            if($values['key_2'] == 'equal'){    
                                $targeting_include[$i]['type']['label'] = $label;  
                                $targeting_include[$i]['type']['value'] = esc_html($values['key_1']);  
                                $targeting_include[$i]['value']['label'] =  esc_html($values['key_3']); 
                                $targeting_include[$i]['value']['value'] = esc_html($values['key_3']); 
                                $i++;  

                            }else{  
                                $targeting_exclude[$j]['type']['label'] = $label;  
                                $targeting_exclude[$j]['type']['value'] =  esc_html($values['key_1']);  
                                $targeting_exclude[$j]['value']['label'] =  esc_html($values['key_3']); 
                                $targeting_exclude[$j]['value']['value'] = esc_html($values['key_3']); 
                                $j++;   

                            }   
                            update_post_meta( $post_id, 'targeting_include', $targeting_include); 
                            update_post_meta( $post_id, 'targeting_exclude', $targeting_exclude); 
                        }
                    }

                   $banner_size = 'responsive';
                    $g_data_ad_width = '300';
                    $g_data_ad_height = '240';
                    if(isset($post_meta['adsforwp_ad_responsive'][0]) && !$post_meta['adsforwp_ad_responsive'][0]){
                        //not responsive
                        $ad_size = explode( 'x',$post_meta['banner_size'][0]);  
                        $banner_size = 'normal';
                        $g_data_ad_width = $ad_size[0];
                        $g_data_ad_height = $ad_size[1];
                    } 
                    $position = 'ad_shortcode';
                    $paragraph_number = '';
                    $repeat_paragraph = '';
                    $ads_loop_number  = '';
                    $after_the_percentage_value = '';
                    if(isset($post_meta['wheretodisplay'][0])){
                        if($post_meta['wheretodisplay'][0] == 'between_the_content'){
                            if($post_meta['adposition'][0] == 'number_of_paragraph'){
                                $position = 'ad_after_html_tag';
                                $count_as_per = $post_meta['display_tag_name'][0];
                                $paragraph_number = $post_meta['paragraph_number'][0];
                                $repeat_paragraph = $post_meta['ads_on_every_paragraphs_number'][0];
                            }else{
                                $position = 'after_the_percentage';
                                $after_the_percentage_value = $post_meta['percent_content'][0];
                            }
                        }else if($post_meta['wheretodisplay'][0] == 'after_the_content'){
                            $position = 'end_of_post';
                        }else if($post_meta['wheretodisplay'][0] == 'before_the_content'){
                            $position = 'beginning_of_post';
                        }else if($post_meta['wheretodisplay'][0] == 'adsforwp_after_featured_images'){
                            $position = 'amp_after_featured_image';
                        }else if($post_meta['wheretodisplay'][0] == 'adsforwp_below_the_header'){
                            $position = 'amp_below_the_header';
                        }else if($post_meta['wheretodisplay'][0] == 'adsforwp_below_the_footer'){
                            $position = 'amp_below_the_footer';
                        }
                        else if($post_meta['wheretodisplay'][0] == 'adsforwp_above_the_footer'){
                            $position = 'amp_above_the_footer';
                        }
                        else if($post_meta['wheretodisplay'][0] == 'adsforwp_above_the_post_content'){
                            $position = 'amp_above_the_post_content';
                        }
                        else if($post_meta['wheretodisplay'][0] == 'adsforwp_below_the_post_content'){
                            $position = 'amp_below_the_post_content';
                        }
                        else if($post_meta['wheretodisplay'][0] == 'adsforwp_below_the_title'){
                            $position = 'amp_below_the_title';
                        }
                        else if($post_meta['wheretodisplay'][0] == 'adsforwp_above_related_post'){
                            $position = 'amp_above_related_post';
                        }
                        else if($post_meta['wheretodisplay'][0] == 'adsforwp_below_author_box'){
                            $position = 'amp_below_author_box';
                        }
                        else if($post_meta['wheretodisplay'][0] == 'adsforwp_ads_in_loops'){
                            $position = 'amp_ads_in_loops';
                            $ads_loop_number = $post_meta['adsforwp_after_how_many_post'][0];
                        }
                    } 
  
                    switch ($post_meta['select_adtype'][0]) {
                        case 'custom':
                           $select_adtype = 'plain_text';
                            break;
                        case 'doubleclick':
                           $select_adtype = 'double_click';
                            break;
                        case 'ad_background':
                           $select_adtype = 'background_ad';
                            break;    
                        default:
                            $select_adtype = $post_meta['select_adtype'][0];
                            break;
                    }

                    $adforwp_meta_key = array(
                        'label'                         => $ads->post_title ,
                        'ad_type'                       => $select_adtype , 
                        'adsense_ad_type'               => $adsense_type,     
                        'g_data_ad_client'              => $post_meta['data_client_id'][0], 
                        'g_data_ad_slot'                => $post_meta['data_ad_slot'][0],  
                        'g_data_ad_width'               => $g_data_ad_width,  
                        'g_data_ad_height'              => $g_data_ad_height,                      
                        'adsense_type'                  => $banner_size,
                        'position'                      => $position, 
                        'after_the_percentage_value'    => $after_the_percentage_value, 
                        'paragraph_number'              => $paragraph_number, 
                        'repeat_paragraph'              => $repeat_paragraph,  
                        'ads_loop_number'               => $ads_loop_number,
                        'count_as_per'                  => $count_as_per, 
                        'imported_from'                 => 'adsforwp_ads',
                        'adsforwp_ads_id'               => $ads->ID,
                        'data_publisher'                => $post_meta['adsforwp_mgid_data_publisher'][0], 
                        'data_widget'                   => $post_meta['adsforwp_mgid_data_widget'][0],  
                        'data_container'                => $post_meta['adsforwp_mgid_data_container'][0], 
                        'data_js_src'                   => $post_meta['data_js_src'][0],   
                        'code'                          => $post_meta['custom_code'][0],  
                        'network_code'                  => $post_meta['dfp_slot_id'][0], 
                        'ad_unit_name'                  => $post_meta['dfp_div_gpt_ad'][0], 
                        'taboola_publisher_id'          => $post_meta['taboola_publisher_id'][0], 
                        'data_cid'                      => $post_meta['data_cid'][0], 
                        'data_crid'                     => $post_meta['data_crid'][0], 
                        'mediavine_site_id'             => $post_meta['mediavine_site_id'][0], 
                        'outbrain_widget_ids'           => $post_meta['outbrain_widget_ids'][0], 
                        'image_src'                     => $post_meta['adsforwp_ad_image'][0], 
                        'image_redirect_url'            => $post_meta['adsforwp_ad_redirect_url'][0], 
                        'image_src'                     => $post_meta['ad_background_image'][0], 
                        'image_redirect_url'            => $post_meta['ad_background_redirect_url'][0], 
                        'enabled_on_amp'                => 1,
                        'ad_id'                         => $post_id,
                        'enable_one_end_of_post'        =>'',
                        'quads_ad_old_id'               => 'ad'.$ad_count,
                    );
                            
                    foreach ($adforwp_meta_key as $key => $val){    
                        update_post_meta($post_id, $key, $val);  
                    } 
                    require_once QUADS_PLUGIN_DIR . '/admin/includes/migration-service.php';
                        $this->migration_service = new QUADS_Ad_Migration();
                        $this->migration_service->quadsUpdateOldAd('ad'.$ad_count, $adforwp_meta_key);     
                        $ad_count++;  
                }
                update_option('adsforwp_to_quads', 'imported');      
            }
            return  array('status' => 't', 'data' => 'Ads have been successfully imported'); 
        } 
     /** Here we are importing AMP for WP and advance Amp ads to Quads**/
        public function importampforwp_ads(){
            global $redux_builder_amp;
            $args = array(
                      'post_type' => 'quads-ads'
                    );
            $the_query = new WP_Query( $args );
            $ad_count = $the_query->found_posts;
            $post_status = 'publish';            
            $amp_options       = get_option('redux_builder_amp');   
            $user_id          = get_current_user_id();
            $after_the_percentage_value = '';

            for($i=1; $i<=6; $i++){ 
               if($amp_options['enable-amp-ads-'.$i] != 1){ 
                    continue;
               } 
               $ad_type    =  $amp_options['enable-amp-ads-type-'.$i]; 
               if(($ad_type== 'adsense' && (empty($amp_options['enable-amp-ads-text-feild-client-'.$i]) || empty($amp_options['enable-amp-ads-text-feild-slot-'.$i]))) || ($ad_type== 'mgid' && (empty($amp_options['enable-amp-ads-mgid-field-data-pub-'.$i]) || empty($amp_options['enable-amp-ads-mgid-field-data-widget-'.$i])))){
                continue;
               }   
               $ad_count++;                              
               switch ($i) {
                        case 1:
                                $position   =   'amp_below_the_header'; 
                                break;
                        case 2:
                                $position   =   'amp_below_the_footer'; 
                                break;
                        case 3:
                                $position   =   'amp_above_the_post_content'; 
                                break;
                        case 4:
                                $position   =   'amp_below_the_post_content'; 
                                break;
                        case 5:
                                $position   =   'amp_below_the_title'; 
                                break;
                        case 6:
                                $position   =   'amp_above_related_post'; 
                                break;
                    }     
                switch ($amp_options['enable-amp-ads-select-'.$i]) {
                    case '1':
                        $g_data_ad_width    = '300';
                        $g_data_ad_height   = '250';  
                        break;
                    case '2':
                        $g_data_ad_width    = '336';
                        $g_data_ad_height   = '280';    
                        break;
                    case '3':
                        $g_data_ad_width    = '728';
                        $g_data_ad_height   = '90';
                        break;
                    case '4':
                        $g_data_ad_width    = '300';
                        $g_data_ad_height   = '600';    
                        break;
                    case '5':
                        $g_data_ad_width    = '320';
                        $g_data_ad_height   = '100';    
                        break;
                    case '6':
                        $g_data_ad_width    = '200';
                        $g_data_ad_height   = '50';
                        break;
                    case '7':
                        $g_data_ad_width    = '320';
                        $g_data_ad_height   = '50';
                        break;
                    default:
                        $g_data_ad_width = '300';
                        $g_data_ad_height= '250'; 
                        break;
                }
                if($ad_type== 'mgid'){
                    if($i == 2){
                        $position   =   'ad_shortcode'; 
                    }
                    $post_title ='MGID Ad '.$i.' (Migrated from AMP)';
                    $g_data_ad_width = $amp_options['enable-amp-ads-mgid-width-'.$i];
                    $g_data_ad_height= $amp_options['enable-amp-ads-mgid-height-'.$i]; 
                }else{
                    $post_title ='Adsense Ad '.$i.' (Migrated from AMP)';
                }
                $ads_post = array(
                            'post_author' => $user_id,                                                            
                            'post_title'  => $post_title,                    
                            'post_status' => $post_status,                                                            
                            'post_name'   => $post_title,                    
                            'post_type'   => 'quads-ads',
                            
                        );  
                if($amp_options['enable-amp-ads-resp-'.$i]){
                    $adsense_type = 'responsive';
                }else{
                     $adsense_type = 'normal';
                }
                $post_id          = wp_insert_post($ads_post);
                $visibility_include =array();
                if($i == 3){
                 $display_on =  $amp_options['made-amp-ad-3-global'];
                 $j =0;
                 foreach ($display_on as $display_on_data) {
                    switch ($display_on_data) {
                        case '1':
                            $visibility_include[$j]['type']['label'] = 'Post Type';
                            $visibility_include[$j]['type']['value'] = 'post_type';
                            $visibility_include[$j]['value']['label'] = "post";
                            $visibility_include[$j]['value']['value'] = "post"; 
                            $j++; 
                            break;
                        case '2':
                            $visibility_include[$j]['type']['label'] = 'Post Type';
                            $visibility_include[$j]['type']['value'] = 'post_type';
                            $visibility_include[$j]['value']['label'] = "page";
                            $visibility_include[$j]['value']['value'] = "page";  
                            $j++;    
                            break;
                        case '4':
                            $visibility_include[$j]['type']['label'] = 'General';
                            $visibility_include[$j]['type']['value'] = 'general';
                            $visibility_include[$j]['value']['label'] = "Show Globally";
                            $visibility_include[$j]['value']['value'] = "show_globally";  
                            $j++;
                            break;
                    }
                 }
                }else{
                        $visibility_include[0]['type']['label'] = 'General';
                        $visibility_include[0]['type']['value'] = 'general';
                        $visibility_include[0]['value']['label'] = "Show Globally";
                        $visibility_include[0]['value']['value'] = "show_globally";
                }

                $adforwp_meta_key = array(
                    'ad_type'                       => $ad_type ,  
                    'g_data_ad_client'              => $amp_options['enable-amp-ads-text-feild-client-'.$i], 
                    'g_data_ad_slot'                => $amp_options['enable-amp-ads-text-feild-slot-'.$i],  
                    'data_publisher'                => $amp_options['enable-amp-ads-mgid-field-data-pub-'.$i], 
                    'data_widget'                   => $amp_options['enable-amp-ads-mgid-field-data-widget-'.$i],
                    'data_container'                => $amp_options['enable-amp-ads-mgid-field-data-con-'.$i], 
                    'g_data_ad_width'               => $g_data_ad_width,  
                    'g_data_ad_height'              => $g_data_ad_height,                      
                    'adsense_type'                  => $adsense_type,
                    'enabled_on_amp'                => 1,
                    'visibility_include'            => $visibility_include,
                    'position'                      => $position,    
                    'imported_from'                 => 'ampforwp_ads',
                    'label'                         =>  $post_title,
                    'ad_id'                         => $post_id,
                    'code'                          => '',
                    'enable_one_end_of_post'        =>'',
                    'quads_ad_old_id'               => 'ad'.$ad_count,
                    'ad_label_check'                => $amp_options['ampforwp-ads-sponsorship'],
                    'ad_label_text'                 => $amp_options['ampforwp-ads-sponsorship-label'],
                );
                        
                foreach ($adforwp_meta_key as $key => $val){    
                    update_post_meta($post_id, $key, $val);  
                } 
            }
            if ( defined( 'ADVANCED_AMP_ADS_VERSION' ) ) {
                // Incontent Ads
                for($i=1; $i<=6; $i++){ 
                    if($redux_builder_amp['ampforwp-incontent-ad-'.$i] != 1){ 
                        continue;
                   } 
                   $ad_type    =  $redux_builder_amp['ampforwp-advertisement-type-incontent-ad-'.$i]; 
                   $ad_type_label   = '';
                   if($ad_type== '4'){
                    continue;
                   }
                   if(($ad_type== '1' && (empty($redux_builder_amp['ampforwp-adsense-ad-data-ad-client-incontent-ad-'.$i]) || empty($redux_builder_amp['ampforwp-adsense-ad-data-ad-slot-incontent-ad-'.$i]))) || ($ad_type== '5' && (empty($redux_builder_amp['ampforwp-mgid-ad-Data-Publisher-incontent-ad-'.$i]) || empty($redux_builder_amp['ampforwp-mgid-ad-Data-Widget-incontent-ad-'.$i])))){
                    continue;
                   }               
                    $ad_count++;
                    $g_data_ad_width = '';
                    $g_data_ad_height= ''; 
                    if($ad_type == '1'){
                        $ad_type_label      = 'adsense';
                        $post_title         = 'Adsense Ad '.$i.' Incontent Ad (Migrated from AMP)';
                        $g_data_ad_width    = $redux_builder_amp['ampforwp-adsense-ad-width-incontent-ad-'.$i];
                        $g_data_ad_height   = $redux_builder_amp['ampforwp-adsense-ad-height-incontent-ad-'.$i];
                        $position = $redux_builder_amp['ampforwp-adsense-ad-position-incontent-ad-'.$i];
                    }else if($ad_type == '2'){
                        $ad_type_label      = 'double_click';
                        $post_title         = 'DoubleClick Ad '.$i.' Incontent Ad (Migrated from AMP)';
                        $g_data_ad_width    = $redux_builder_amp['ampforwp-doubleclick-ad-width-incontent-ad-'.$i];
                        $g_data_ad_height   = $redux_builder_amp['ampforwp-doubleclick-ad-height-incontent-ad-'.$i];
                        $position = $redux_builder_amp['ampforwp-doubleclick-ad-position-incontent-ad-'.$i];
                    }else if($ad_type == '3'){
                        $ad_type_label      = 'plain_text';
                        $post_title         = 'Plain Text Ad '.$i.' Incontent Ad (Migrated from AMP)';
                        $position = $redux_builder_amp['ampforwp-custom-ads-ad-position-incontent-ad-'.$i];
                    }else if($ad_type == '5'){
                        $ad_type_label      = 'mgid';
                        $post_title         ='MGID Ad '.$i.' Incontent Ad (Migrated from AMP)';
                        $g_data_ad_width    = $redux_builder_amp['ampforwp-mgid-ad-width-incontent-ad-'.$i];
                        $g_data_ad_height   = $redux_builder_amp['ampforwp-mgid-ad-height-incontent-ad-'.$i]; 
                        $position = $redux_builder_amp['ampforwp-mgid-ad-position-incontent-ad-'.$i];
                    }
                    if($redux_builder_amp['adsense-rspv-ad-incontent-'.$i]){
                        $adsense_type = 'responsive';
                    }else{
                         $adsense_type = 'normal';
                    } 
                    $ads_post = array(
                                'post_author' => $user_id,                                                            
                                'post_title'  => $post_title,                    
                                'post_status' => $post_status,                                                            
                                'post_name'   => $post_title,                    
                                'post_type'   => 'quads-ads',
                            );  
                    $post_id          = wp_insert_post($ads_post);
                    $visibility_include =array();

                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = "post";
                    $visibility_include[0]['value']['value'] = "post"; 
                    $doubleclick_ad_data_slot = explode('/', $redux_builder_amp['ampforwp-doubleclick-ad-data-slot-incontent-ad-'.$i]);
                    $adlabel =  'above';
                    if($redux_builder_amp['ampforwp-ad-sponsorship-location'] == '2'){
                        $adlabel =  'below';
                    }
                    $paragraph_number = '1';
                    
                              switch ($position) {
                            case '20-percent':
                                    $position                     =   'after_the_percentage'; 
                                    $after_the_percentage_value   =   '20'; 
                                    break;
                            case '40-percent':
                                    $position                     =   'after_the_percentage'; 
                                    $after_the_percentage_value   =   '40'; 
                                    break;
                            case '50-percent':
                                    $position                     =   'after_the_percentage'; 
                                    $after_the_percentage_value   =   '50';  
                                    break;
                           case '60-percent':
                                    $position                     =   'after_the_percentage'; 
                                    $after_the_percentage_value   =   '60'; 
                                    break;
                            case '80-percent':
                                    $position                     =   'after_the_percentage'; 
                                    $after_the_percentage_value   =   '80';  
                                    break;
                            case 'custom':
                                    $position   =   'code'; 
                                    break;
                            default:
                                    if(is_numeric($position)){
                                        $paragraph_number = $position;
                                        $position = 'after_paragraph';
                                    }
                            break;
                        } 
                        $network_code = '';
                        $doubleclick_flag = 2;
                        if(isset($doubleclick_ad_data_slot[0]) && !empty($doubleclick_ad_data_slot[0])){
                               $doubleclick_flag = 3;
                            $network_code = $doubleclick_ad_data_slot[0];
                        }
                        if(isset($doubleclick_ad_data_slot[1]) && !empty($doubleclick_ad_data_slot[1])){
                            if($doubleclick_flag == 3){
                                $ad_unit_name = $doubleclick_ad_data_slot[1];
                            }else{
                                $network_code = $doubleclick_ad_data_slot[1]; 
                                if(isset($doubleclick_ad_data_slot[2]) && !empty($doubleclick_ad_data_slot[2])){
                                    $ad_unit_name = $doubleclick_ad_data_slot[2];
                                }
                            }
                        }

                    $adforwp_meta_key = array(
                        'ad_type'                       => $ad_type_label ,  
                        'g_data_ad_client'              => $redux_builder_amp['ampforwp-adsense-ad-data-ad-client-incontent-ad-'.$i], 
                        'g_data_ad_slot'                => $redux_builder_amp['ampforwp-adsense-ad-data-ad-slot-incontent-ad-'.$i],  
                        'data_publisher'                => $redux_builder_amp['ampforwp-mgid-ad-Data-Publisher-incontent-ad-'.$i], 
                        'data_widget'                   => $redux_builder_amp['ampforwp-mgid-ad-Data-Widget-incontent-ad-'.$i],
                        'data_container'                => $redux_builder_amp['ampforwp-mgid-ad-Data-Container-incontent-ad-'.$i], 
                        'network_code'                  => $network_code, 
                        'ad_unit_name'                  => $ad_unit_name, 
                        'code'                          => $redux_builder_amp['ampforwp-custom-advertisement-incontent-ad-'.$i],
                        'g_data_ad_width'               => $g_data_ad_width,  
                        'g_data_ad_height'              => $g_data_ad_height,                      
                        'adsense_type'                  => $adsense_type,
                        'enabled_on_amp'                => 1,
                        'visibility_include'            => $visibility_include,
                        'position'                      => $position, 
                        'after_the_percentage_value'    => $after_the_percentage_value, 
                        'paragraph_number'              => $paragraph_number,   
                        'imported_from'                 => 'ampforwp_ads',
                        'label'                         =>  $post_title,
                        'ad_id'                         => $post_id,
                        'enable_one_end_of_post'        =>'',
                        'quads_ad_old_id'               => 'ad'.$ad_count,
                        'ad_label_check'                => $redux_builder_amp['ampforwp-ad-sponsorship'],
                        'adlabel'                       => $adlabel,
                        'ad_label_text'                 => $redux_builder_amp['ampforwp-ad-sponsorship-label'],
                    );
                            
                    foreach ($adforwp_meta_key as $key => $val){    
                        update_post_meta($post_id, $key, $val);  
                    } 

                        require_once QUADS_PLUGIN_DIR . '/admin/includes/migration-service.php';
                        $this->migration_service = new QUADS_Ad_Migration();
                        $this->migration_service->quadsUpdateOldAd('ad'.$ad_count, $adforwp_meta_key);

                } 
                // General Ads
                for($i=1; $i<=10; $i++){ 
                   if($amp_options['ampforwp-standard-ads-'.$i] != 1){ 
                        continue;
                   } 
                   $ad_type    =  $amp_options['ampforwp-advertisement-type-standard-'.$i]; 
                    if(($ad_type== '1' && (empty($redux_builder_amp['ampforwp-adsense-ad-data-ad-client-standard-'.$i]) || empty($redux_builder_amp['ampforwp-adsense-ad-data-ad-slot-standard-'.$i])))|| ($ad_type== '2' && empty($redux_builder_amp['ampforwp-doubleclick-ad-data-slot-standard-'.$i])) || ($ad_type== '5' && (empty($redux_builder_amp['ampforwp-mgid-data-ad-data-publisher-standard-'.$i]) || empty($redux_builder_amp['ampforwp-mgid-data-ad-data-widget-standard-'.$i])))){
                    continue;
                   }   
                    $ad_count++;                              
                   switch ($i) {
                            case 1:
                                    $position   =   'amp_below_the_header'; 
                                    break;
                            case 2:
                                    $position   =   'amp_below_the_footer'; 
                                    break;
                            case 3:
                                    $position   =   'amp_above_the_footer'; 
                                    break;
                            case 4:
                                    $position   =   'amp_above_the_post_content'; 
                                    break;
                            case 5:
                                    $position   =   'amp_below_the_post_content'; 
                                    break;
                            case 6:
                                    $position   =   'amp_below_the_title'; 
                                    break;
                            case 7:
                                    $position   =   'amp_above_related_post'; 
                                    break;
                            case 8:
                                    $position   =   'amp_below_author_box'; 
                                    break;
                            case 9:
                                    $position   =   'amp_ads_in_loops'; 
                                    break;
                        }     

                                    $g_data_ad_width = '';
                    $g_data_ad_height= ''; 
                     $adsense_type = 'normal';
                    if($ad_type == '1'){
                        $ad_type_label      = 'adsense';
                        $post_title         = 'Adsense Ad '.$i.' General Options (Migrated from AMP)';
                        $g_data_ad_width    = $redux_builder_amp['ampforwp-adsense-ad-width-standard-'.$i];
                        $g_data_ad_height   = $redux_builder_amp['ampforwp-adsense-ad-height-standard-'.$i];
                        if($amp_options['adsense-rspv-ad-type-standard-'.$i]){
                            $adsense_type = 'responsive';
                        }else{
                             $adsense_type = 'normal';
                        }
                    }else if($ad_type == '2'){
                        $ad_type_label      = 'double_click';
                        $post_title         = 'DoubleClick Ad '.$i.' General Options (Migrated from AMP)';
                        $g_data_ad_width    = $redux_builder_amp['ampforwp-doubleclick-ad-width-standard-'.$i];
                        $g_data_ad_height   = $redux_builder_amp['ampforwp-doubleclick-ad-height-standard-'.$i];
                        $adsense_type = 'normal';
                    }else if($ad_type == '3'){
                        $ad_type_label      = 'plain_text';
                        $post_title         = 'Ad '.$i.' General Options (Migrated from AMP)';
                    }else if($ad_type == '5'){
                        $ad_type_label      = 'mgid';
                        $post_title         ='MGID Ad '.$i.' General Options (Migrated from AMP)';
                        $g_data_ad_width    = $redux_builder_amp['ampforwp-mgid-ad-width-standard-'.$i];
                        $g_data_ad_height   = $redux_builder_amp['ampforwp-mgid-ad-height-standard-'.$i]; 
                        $adsense_type = 'normal';
                    }
                    $ads_post = array(
                                'post_author' => $user_id,                                                            
                                'post_title'  => $post_title,                    
                                'post_status' => $post_status,                                                            
                                'post_name'   => $post_title,                    
                                'post_type'   => 'quads-ads',
                                
                            );  
                    $post_id          = wp_insert_post($ads_post);
                    $visibility_include =array();
                    $visibility_include[0]['type']['label'] = 'Post Type';
                    $visibility_include[0]['type']['value'] = 'post_type';
                    $visibility_include[0]['value']['label'] = "post";
                    $visibility_include[0]['value']['value'] = "post"; 

                        $network_code = '';
                        $ad_unit_name = '';
                        $doubleclick_flag = 2;
                        $doubleclick_ad_data_slot = explode('/', $redux_builder_amp['ampforwp-doubleclick-ad-data-slot-standard-'.$i]);
                        if(isset($doubleclick_ad_data_slot[0]) && !empty($doubleclick_ad_data_slot[0])){
                               $doubleclick_flag = 3;
                            $network_code = $doubleclick_ad_data_slot[0];
                        }
                        if(isset($doubleclick_ad_data_slot[1]) && !empty($doubleclick_ad_data_slot[1])){
                            if($doubleclick_flag == 3){
                                $ad_unit_name = $doubleclick_ad_data_slot[1];
                            }else{
                                $network_code = $doubleclick_ad_data_slot[1]; 
                                if(isset($doubleclick_ad_data_slot[2]) && !empty($doubleclick_ad_data_slot[2])){
                                    $ad_unit_name = $doubleclick_ad_data_slot[2];
                                }
                            }
                        }

                    $adforwp_meta_key = array(
                        'ad_type'                       => $ad_type_label ,  
                        'g_data_ad_client'              => $redux_builder_amp['ampforwp-adsense-ad-data-ad-client-standard-'.$i], 
                        'g_data_ad_slot'                => $redux_builder_amp['ampforwp-adsense-ad-data-ad-slot-standard-'.$i],  
                        'data_publisher'                => $redux_builder_amp['ampforwp-mgid-ad-Data-Publisher-standard-'.$i], 
                        'data_widget'                   => $redux_builder_amp['ampforwp-mgid-ad-Data-Widget-standard-'.$i],
                        'data_container'                => $redux_builder_amp['ampforwp-mgid-ad-Data-Container-standard-'.$i], 
                        'network_code'                  => $network_code, 
                        'ad_unit_name'                  => $ad_unit_name, 
                        'code'                          => $redux_builder_amp['ampforwp-custom-advertisement-standard-'.$i],
                        'g_data_ad_width'               => $g_data_ad_width,  
                        'g_data_ad_height'              => $g_data_ad_height,                      
                        'adsense_type'                  => $adsense_type,
                        'enabled_on_amp'                => 1,
                        'visibility_include'            => $visibility_include,
                        'position'                      => $position,    
                        'imported_from'                 => 'ampforwp_ads',
                        'label'                         =>  $post_title,
                        'ad_id'                         => $post_id,
                        'enable_one_end_of_post'        => '',
                        'quads_ad_old_id'               => 'ad'.$ad_count,
                        'ad_label_check'                => $redux_builder_amp['ampforwp-ad-sponsorship'],
                        'adlabel'                       => $adlabel,
                        'ad_label_text'                 => $redux_builder_amp['ampforwp-ad-sponsorship-label'],
                    );
                            
                    foreach ($adforwp_meta_key as $key => $val){    
                        update_post_meta($post_id, $key, $val);  
                    } 
                    require_once QUADS_PLUGIN_DIR . '/admin/includes/migration-service.php';
                        $this->migration_service = new QUADS_Ad_Migration();
                        $this->migration_service->quadsUpdateOldAd('ad'.$ad_count, $adforwp_meta_key);
                }
                
                if($amp_options['ampforwp-after-featured-image-ad']){ 
                    $ad_count++;
                    $ad_type    =  $amp_options['ampforwp-after-featured-image-ad-type']; 
                    $g_data_ad_width        = '';
                    $g_data_ad_height       = ''; 
                    $adsense_type = 'normal';
                    if($ad_type == '1'){
                        $ad_type_label      = 'adsense';
                        $post_title         = 'Adsense Ad '.$ad_count.' (Migrated from AMP)';
                        $g_data_ad_width    = $redux_builder_amp['ampforwp-after-featured-image-ad-type-1-width'];
                        $g_data_ad_height   = $redux_builder_amp['ampforwp-after-featured-image-ad-type-1-height'];
                        if($redux_builder_amp['adsense-rspv-ad-after-featured-img']){
                            $adsense_type = 'responsive';
                        }else{
                             $adsense_type = 'normal';
                        }
                    }else if($ad_type == '2'){
                        $ad_type_label      = 'double_click';
                        $post_title         = 'DoubleClick Ad '.$ad_count.' (Migrated from AMP)';
                        $g_data_ad_width    = $redux_builder_amp['ampforwp-after-featured-image-ad-type-2-width'];
                        $g_data_ad_height   = $redux_builder_amp['ampforwp-after-featured-image-ad-type-2-height'];
                    }else if($ad_type == '3'){
                        $ad_type_label      = 'plain_text';
                        $post_title         = 'Adsense Ad '.$ad_count.' (Migrated from AMP)';
                    }else if($ad_type == '5'){
                        $ad_type_label      = 'mgid';
                        $post_title         = 'MGID Ad '.$ad_count.' (Migrated from AMP)';
                        $g_data_ad_width    = $redux_builder_amp['ampforwp-after-featured-image-ad-type-5-width'];
                        $g_data_ad_height   = $redux_builder_amp['ampforwp-after-featured-image-ad-type-5-height'];
                    }
                    $network_code = '';
                        $ad_unit_name = '';
                        $doubleclick_flag = 2;
                        $doubleclick_ad_data_slot = explode('/', $redux_builder_amp['ampforwp-after-featured-image-ad-type-2-ad-data-slot']);
                        if(isset($doubleclick_ad_data_slot[0]) && !empty($doubleclick_ad_data_slot[0])){
                               $doubleclick_flag = 3;
                            $network_code = $doubleclick_ad_data_slot[0];
                        }
                        if(isset($doubleclick_ad_data_slot[1]) && !empty($doubleclick_ad_data_slot[1])){
                            if($doubleclick_flag == 3){
                                $ad_unit_name = $doubleclick_ad_data_slot[1];
                            }else{
                                $network_code = $doubleclick_ad_data_slot[1]; 
                                if(isset($doubleclick_ad_data_slot[2]) && !empty($doubleclick_ad_data_slot[2])){
                                    $ad_unit_name = $doubleclick_ad_data_slot[2];
                                }
                            }
                        }

                        $visibility_include =array();
                        $visibility_include[0]['type']['label'] = 'Post Type';
                        $visibility_include[0]['type']['value'] = 'post_type';
                        $visibility_include[0]['value']['label'] = "post";
                        $visibility_include[0]['value']['value'] = "post"; 
                        $ads_post = array(
                                'post_author' => $user_id,                                                            
                                'post_title'  => $post_title,                    
                                'post_status' => $post_status,                                                            
                                'post_name'   => $post_title,                    
                                'post_type'   => 'quads-ads',
                                
                            );  
                        $post_id          = wp_insert_post($ads_post);

                     $adforwp_meta_key = array(
                        'ad_type'                       => $ad_type_label ,  
                        'g_data_ad_client'              => $redux_builder_amp['ampforwp-after-featured-image-ad-type-1-data-ad-client'], 
                        'g_data_ad_slot'                => $redux_builder_amp['ampforwp-after-featured-image-ad-type-1-data-ad-slot'],  
                        'data_publisher'                => $redux_builder_amp['ampforwp-after-featured-image-ad-type-5-Data-publisher'], 
                        'data_widget'                   => $redux_builder_amp['ampforwp-after-featured-image-ad-type-5-Data-widget'],
                        'data_container'                => $redux_builder_amp['ampforwp-after-featured-image-ad-type-5-Data-Container'], 
                        'network_code'                  => $network_code, 
                        'ad_unit_name'                  => $ad_unit_name, 
                        'code'                          => $redux_builder_amp['ampforwp-after-featured-image-ad-custom-advertisement'],
                        'g_data_ad_width'               => $g_data_ad_width,  
                        'g_data_ad_height'              => $g_data_ad_height,                      
                        'adsense_type'                  => $adsense_type,
                        'enabled_on_amp'                => 1,
                        'visibility_include'            => $visibility_include,
                        'position'                      => 'amp_after_featured_image',    
                        'imported_from'                 => 'ampforwp_ads',
                        'label'                         =>  $post_title,
                        'ad_id'                         => $post_id,
                        'enable_one_end_of_post'        => '',
                        'quads_ad_old_id'               => 'ad'.$ad_count,
                        'ad_label_check'                => $redux_builder_amp['ampforwp-ad-sponsorship'],
                        'adlabel'                       => $adlabel,
                        'ad_label_text'                 => $redux_builder_amp['ampforwp-ad-sponsorship-label'],
                    );
                            
                    foreach ($adforwp_meta_key as $key => $val){    
                        update_post_meta($post_id, $key, $val);  
                    } 
                    require_once QUADS_PLUGIN_DIR . '/admin/includes/migration-service.php';
                        $this->migration_service = new QUADS_Ad_Migration();
                        $this->migration_service->quadsUpdateOldAd('ad'.$ad_count, $adforwp_meta_key);
                } 
            }
            return  array('status' => 't', 'data' => 'Ads have been successfully imported'); 

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
                 $sendto    = 'team@ampforwp.com';
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
            $quads_settings['adsforwp_to_quads'] = get_option( 'adsforwp_to_quads' );
            $post_types = get_post_types();
            $add = array('none' => 'Exclude nothing');
            $quads_settings['auto_ads_get_post_types'] =  $add + $post_types;
            $quads_settings['autoads_excl_user_roles'] =  array_merge(array('none' => 'Exclude nothing'), $this->quads_get_user_roles_api());
            if(file_exists(ABSPATH . 'ads.txt')){
                $quads_settings['adsTxtText'] = trim(file_get_contents(ABSPATH . 'ads.txt'));
            }
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
                $paged    = sanitize_text_field($_GET['pageno']);
            }
            if(isset($_GET['posts_per_page'])){
                $rvcount = sanitize_text_field($_GET['posts_per_page']);
            }  
             if(isset($_GET['search_param'])){
                $search_param = sanitize_text_field($_GET['search_param']);
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

<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class QUADS_Ad_Setup_Api {

        private static $instance;
        private $api_service = null;
    const CID = '92665529714-u1epglunqmdl9s2nne6cttkfc138smb7.apps.googleusercontent.com';

    const CS = 'dHjumDqZSshxPitfVJErRTcX';

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

        protected function quads_current_user_can() {
            global $quads_options;
            if(function_exists('current_user_can') && current_user_can( 'manage_options' )){
                return true;
            }
            $roles = wp_get_current_user()->roles;
            $get_roles = isset($quads_options['RoleBasedAccess']) ? $quads_options['RoleBasedAccess'] : array();
            if(is_array($get_roles)){    
                foreach ($get_roles as $get_role => $role_val) {
                    if ( count(array_intersect( $role_val, $roles )) >= 1 ){
                        return true;
                    }
                }
            }
            return false;
        }

        public function registerRoute(){

            register_rest_route( 'quads-route', 'get-ads-list', array(
                    'methods'    => 'GET',
                    'callback'   => array($this, 'getAdList'),
                    'permission_callback' => function(){
                        return $this->quads_current_user_can();
                    }
            ));
            register_rest_route( 'quads-route', 'change-mode', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'changeMode'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'ad-more-action', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'adMoreAction'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'update-ad', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'updateAd'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'update-settings', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'updateSettings'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'validate-ads-txt', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'validateAdsTxt'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'send-customer-query', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'sendCustomerQuery'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'get-ad-by-id', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getAdById'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'get-settings', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getSettings'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'get-condition-list', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getConditionList'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'export-settings', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'exportSettings') ,
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'import-settings', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'importSettings') ,
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'get-quads-info', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getQuadsInfo'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'get-user-role', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getUserRole'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'get-tags', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getTags'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'get-plugins', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getPlugins'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
             register_rest_route( 'quads-route', 'get-add-next-id', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'getAddNextId'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
             register_rest_route( 'quads-route', 'quads_subscribe_newsletter', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'quadsSubscribeNewsletter'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
               register_rest_route( 'quads-route', 'import-ampforwp-ads', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'importampforwp_ads'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
               register_rest_route( 'quads-route', 'import-advance-ads', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'importadvance_ads'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
               register_rest_route( 'quads-route', 'import-adsforwp-ads', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'importadsforwp_ads'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'reports-adsense-confcode', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'reportsAdsenseConfcode'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
	        register_rest_route( 'quads-route', 'quads_register_ad', array(
		        'methods'    => 'POST',
		        'callback'   => array($this, 'quads_register_ad'),
		        'permission_callback' => function(){
			        return $this->quads_current_user_can();
		        }
	        ));
            register_rest_route( 'quads-route', 'check_plugin_exist', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'check_plugin_exist'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'get-current-user', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getCurrentUser'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'getAdloggingData', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'getAdloggingData'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
            ));
            register_rest_route( 'quads-route', 'get-ads-analytics', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getAdAnalytics'),
                'permission_callback' => function(){
                    return $this->quads_current_user_can();
                }
        ));
        register_rest_route( 'quads-route', 'get-ad-types', array(
            'methods'    => 'GET',
            'callback'   => array($this, 'getAdTypes'),
            'permission_callback' => function(){
                return $this->quads_current_user_can();
            }
        ));
        register_rest_route( 'quads-route', 'list-adsell-records', array(
            'methods'    => 'GET',
            'callback'   => array($this, 'getAdSellList'),
            'permission_callback' => function(){
                return $this->quads_current_user_can();
            }
        ));
        register_rest_route( 'quads-route', 'list-disabledad-records', array(
            'methods'    => 'GET',
            'callback'   => array($this, 'getDisabledAdsList'),
            'permission_callback' => function(){
                return $this->quads_current_user_can();
            }
        ));
        register_rest_route('quads-route', '/adsell/(?P<id>\d+)/(?P<status>approved|disapproved)', [
            'methods'  => 'POST',
            'callback' => array($this, 'updateAdsellStatus'),
            'permission_callback' => function() {
                return $this->quads_current_user_can();
            }
        ]);
        register_rest_route('quads-route', '/disabledads/(?P<id>\d+)/(?P<status>paid|unsubscribe)', [
            'methods'  => 'POST',
            'callback' => array($this, 'updateDisableAdStatus'),
            'permission_callback' => function() {
                return $this->quads_current_user_can();
            }
        ]);

        register_rest_route( 'quads-route', 'get-pages', array(
            'methods'    => 'GET',
            'callback'   => array($this, 'getPages'),
            'permission_callback' => function(){
                return $this->quads_current_user_can();
            }
        ));
        }
        public function quads_register_ad(){
	        global $_quads_registered_ad_locations;
	        return $_quads_registered_ad_locations;
        }

    public static function log( $task = 'No task provided' ) {

        $message = date_i18n( '[Y-m-d H:i:s]' ) . ' ' . $task . "\n";
         // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        error_log( $message, 3, WP_CONTENT_DIR . '/Quads-ads-google-api-requests.log' );
    }
    public function reportsAdsenseConfcode() {

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
                'post_type' => 'adsforwp',
                'numberposts' => -1,
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
            return  array('status' => 't', 'data' => esc_html__( 'Ads have been successfully imported', 'quick-adsense-reloaded' ) );
        }

 /** Here we are importing Advance ads to Quads**/
        public function getgroup(){
            $group = get_terms([
                'taxonomy' => 'advanced_ads_groups',
                'hide_empty' => false,
            ]);
            return $group;
        }
 
        public function get_single_adsGroup( $group ) {
            $group = get_terms([
                'taxonomy' => 'advanced_ads_groups',
                'hide_empty' => false,
            ]);

            return get_posts( array(
                'post_type'      => "advanced_ads" ,
                'post_status'    => array( 'publish', 'pending', 'future', 'private' ),
                'taxonomy'       => $group[0]->taxonomy,
                'term'           => $group[0]->slug,
                'posts_per_page' => - 1,
            ) );
        }

        public function importadvance_ads(){

            $placements      = Advanced_Ads::get_ad_placements_array();
            $get_Advanced_Ads      = Advanced_Ads::get_ads(array('post_status'=>array( 'publish', 'pending', 'future', 'private' )));
            foreach ($get_Advanced_Ads  as $advanced_Ad) {
                $name = 'shortcode_'.$advanced_Ad->ID;
                $placements[$name] = array('item' => 'ad_'.$advanced_Ad->ID,'advanced_ads'=>true);
            }
            $get_Advanced_Ads_group_ads      = Advanced_Ads::get_ad_groups();
            foreach ($get_Advanced_Ads_group_ads  as $group_ad) {
                $name = 'shortcode_'.$group_ad->term_id;
                $placements[$name] = array('item' => 'ad_'.$group_ad->term_id,'advanced_ads_groups'=>true);
            }
            foreach ($placements  as $placement) {

                $idArray =    explode('ad_', $placement['item']);
                $id = $idArray['1'];
                $post = get_post( $id ,'ARRAY_A' );
                // if ( null === $data ) {
                //     return false;
                // }
                $term_name = isset( get_term( $id )->name) ? get_term( $id )->name : NULL ;
                $post_meta = get_post_meta($id,'advanced_ads_ad_options');
                if(isset($post_meta['0']['type'])  ||  isset($get_Advanced_Ads_group_ads[0]->taxonomy ) && $get_Advanced_Ads_group_ads[0]->taxonomy == "advanced_ads_groups"   ){
                    $advanced_ads_options       = get_option('advanced-ads');
                    $quads_settings = get_option( 'quads_settings' );
                    $ads_post = array(
                        'post_author' => $post['post_author'],
                        'post_title'  => $post['post_title'],
                        'post_status' => $post['post_status'],
                        'post_name'   => $post['post_name'],
                        'post_type'   => 'quads-ads'
                    );
                    if(in_array($ads_post['post_status'],array('pending', 'future', 'private' )))
                    {
                        $ads_post['post_status']="draft";
                    }
                    $post_id          = wp_insert_post($ads_post);

                    $adsense_type = 'responsive';
                    $ad_type_label = 'plain_text';
                    $code = '';
                    $network_code = '';
                    $ad_unit_name = '';
                    $count_as_per = '';
                    $paragraph_number = '';
                    $repeat_paragraph = '';
                    $g_data_ad_client = '';
                    $g_data_ad_slot = '';
                    $adsense_ad_type = '';
                    $image_ads_url = '';
                    $image_ad_width = '';
                    $image_ad_height = '';
                    $data_layout_key = '';
                    $post_meta_data =$post_meta['0'];
                    $posttitle = '';
                    // Start Get Groups Ads Data
                    if( $get_Advanced_Ads_group_ads[0]->taxonomy == "advanced_ads_groups" ){
                        $ad_type_label ='rotator_ads';
                        $posttitle = $get_Advanced_Ads_group_ads[0]->name;
                        $group = get_terms([
                            'taxonomy' => 'advanced_ads_groups',
                            'hide_empty' => false,
                        ]);
                        // $get_single_ads_from_Groupad get_safg
                    $get_safg = $this->get_single_adsGroup($group);
                    $ads_list_arr = array() ;
                    foreach ($get_safg as $key => $get_safg_value) {
                        $ads_list_arr[$key]['value'] = $get_safg_value->ID;
                        $ads_list_arr[$key]['label'] = $get_safg_value->post_title;
                }
                    }
                    // End Get Groups Ads Data
                    if($post_meta_data['type'] == 'plain' || $post_meta_data['type'] == 'content'){
                        $ad_type_label ='plain_text';
                        $code =$post['post_content'];
                        $posttitle = $post['post_title'];

                    }else if($post_meta_data['type'] == 'adsense'){
                        $ad_type_label ='adsense';
                        $posttitle = $post['post_title'];
                        $post_content_json = json_decode( $post['post_content'],true );
                        $g_data_ad_slot = $post_content_json['slotId'];
                        $g_data_ad_client = $post_content_json['pubId'];
                        if($post_content_json['unitType'] == 'in-article'){
                            $adsense_ad_type = 'in_article_ads';
                        }else if($post_content_json['unitType'] == 'in-feed'){
                            $adsense_ad_type = 'in_feed_ads';
                            $data_layout_key = $post_content_json['layout_key'];
                        }
                        $adsense_ad_type = $post_content_json['unitType'];

                    }
                    else if($post_meta_data['type'] == 'image'){
                        $ad_type_label ='ad_image';
                        $code = $post['post_content'];
                        $posttitle = $post['post_title'];
                        $image_ads_url = $post_meta_data["url"];
                        $image_ad_width = $post_meta_data["width"];
                        $image_ad_height = $post_meta_data["height"];
                        $post_meta = get_post_meta($id,'advanced_ads_ad_options');
                        $post_meta_image_id = $post_meta[0]["output"]["image_id"];
                        $post_meta_image_value = get_post_meta($post_meta_image_id,'_wp_attached_file',true);
                        $wp_upload_dir = wp_upload_dir();
                        $post_meta_image_value_final = $wp_upload_dir["baseurl"].'/'.$post_meta_image_value;
                        //  Added Visibility support
                        $conditions = $post_meta_data["conditions"];
                        $visibility_include = array();
                        $visibility_exclude = array();
                        $i=0;
                        $j=0;
                    foreach ($conditions as $display) {
                        if($display['type'] == 'posttypes'){
                        if($display['operator'] == 'is'){
                            $visibility_include[$i]['type']['label'] = 'Post Type';
                            $visibility_include[$i]['type']['value'] = 'post_type';
                            $visibility_include[$i]['value']['label'] = $display['value'][0];
                            $visibility_include[$i]['value']['value'] = $display['value'][0];
                            $i++;
                        }else{
                            $visibility_exclude[$j]['type']['label'] = 'Post Type';
                            $visibility_exclude[$j]['type']['value'] = 'post_type';
                            $visibility_exclude[$j]['value']['label'] = $display['value'][0];
                            $visibility_exclude[$j]['value']['value'] = $display['value'][0];
                            $j++;
                        }
                    }elseif($display['type'] == 'archive_category' || $display['type'] == 'taxonomy_category'){
                        if($display['operator'] == 'is'){
                            foreach ($display['value'] as $key => $value) {
                                $visibility_include[$i]['type']['label'] = 'Post Category';
                                $visibility_include[$i]['type']['value'] = 'post_category';
                                $visibility_include[$i]['value']['label'] = get_the_category( $value )[0]->name;
                                $visibility_include[$i]['value']['value'] = $value;
                                $i++;
                            }
                        }else{
                            foreach ($display['value'] as $key => $value) {
                                $visibility_exclude[$j]['type']['label'] = 'Post Category';
                                $visibility_exclude[$j]['type']['value'] = 'post_category';
                                $visibility_exclude[$j]['value']['label'] = get_the_category( $value )[0]->name;
                                $visibility_exclude[$j]['value']['value'] = $value;
                                $i++;
                            }
                        }
                    }elseif($display['type'] == 'archive_post_tag' || $display['type'] == 'taxonomy_post_tag'){
                        if($display['operator'] == 'is'){
                            foreach ($display['value'] as $key => $value) {
                                $visibility_include[$i]['type']['label'] = 'Tags';
                                $visibility_include[$i]['type']['value'] = 'tags';
                                $visibility_include[$i]['value']['label'] = get_tag( $value )->name;
                                $visibility_include[$i]['value']['value'] = $value;
                                $i++;
                            }
                        }else{
                            foreach ($display['value'] as $key => $value) {
                                $visibility_exclude[$j]['type']['label'] = 'Tags';
                                $visibility_exclude[$j]['type']['value'] = 'tags';
                                $visibility_exclude[$j]['value']['label'] = get_tag( $value )->name;
                                $visibility_exclude[$j]['value']['value'] = $value;
                                $i++;
                            }
                        }
                    }
                        }
                        
                    }
                    
                    $position = 'ad_shortcode';
                    switch ($placement['type']) {
                        case 'post_top':
                           $position = 'beginning_of_post';
                          break;
                        case 'post_bottom':
                           $position = 'end_of_post';
                          break;
                        case 'post_content':
                           $position = 'ad_after_html_tag';
                           if(isset($placement['options'])){
                            $count_as_per = $placement['options']['tag'];
                                if($placement['options']['tag'] == 'p' ||$placement['options']['tag'] == 'pwithoutimg'){
                                    $count_as_per = 'p_tag';
                                }elseif($placement['options']['tag'] == 'img'){
                                    $count_as_per = 'img_tag';
                                }elseif($placement['options']['tag'] == 'div'){
                                    $count_as_per = 'div_tag';
                                }

                            $paragraph_number = $placement['options']['index'];
                            $repeat_paragraph = (isset($placement['options']['repeat'])&& !empty($placement['options']['repeat']))?$placement['options']['repeat']: '';
                           }
                          break;
                        case 'sidebar_widget': // no there
                           $position = 'ad_shortcode';
                          break;
                        case 'background': //  we have only image
                           $position = 'ad_shortcode';
                          break;
                        case 'post_content_random':
                           $position = 'ad_shortcode';
                          break;
                        case 'post_above_headline': // no there
                           $position = 'beginning_of_post';
                          break;
                        case 'post_content_middle':
                           $position = 'middle_of_post';
                          break;
                        case 'custom_position': // no there
                           $position = 'ad_shortcode';
                          break;
                        default:
                           $position = 'ad_shortcode';
                          break;
                      }
                      $ad_label_check ='';
                      if($placement['options']['ad_label'] == 'default'){
                        if(isset($advanced_ads_options['custom-label']['enabled']) && $advanced_ads_options['custom-label']['enabled'])
                        {
                            $ad_label_check =true;
                        }
                      }else if($placement['options']['ad_label'] == 'enabled'){
                        $ad_label_check = true;
                      }
                    switch ($post_meta['0']['output']['position']) {
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
                    foreach ($placement['options']['placement_conditions']['display'] as $display) {
                        if($display['type'] == 'posttypes'){
                            if($display['operator'] == 'is'){
                                $visibility_include[$i]['type']['label'] = 'Post Type';
                                $visibility_include[$i]['type']['value'] = 'post_type';
                                $visibility_include[$i]['value']['label'] = $display['value'][0];
                                $visibility_include[$i]['value']['value'] = $display['value'][0];
                                $i++;
                            }else{
                                $visibility_exclude[$j]['type']['label'] = 'Post Type';
                                $visibility_exclude[$j]['type']['value'] = 'post_type';
                                $visibility_exclude[$j]['value']['label'] = $display['value'][0];
                                $visibility_exclude[$j]['value']['value'] = $display['value'][0];
                                $j++;
                            }
                        }elseif($display['type'] == 'archive_category' || $display['type'] == 'taxonomy_category'){
                            if($display['operator'] == 'is'){
                                foreach ($display['value'] as $key => $value) {
                                    $visibility_include[$i]['type']['label'] = 'Post Category';
                                    $visibility_include[$i]['type']['value'] = 'post_category';
                                    $visibility_include[$i]['value']['label'] = get_the_category( $value )[0]->name;
                                    $visibility_include[$i]['value']['value'] = $value;
                                    $i++;
                                }
                            }else{
                                foreach ($display['value'] as $key => $value) {
                                    $visibility_exclude[$j]['type']['label'] = 'Post Category';
                                    $visibility_exclude[$j]['type']['value'] = 'post_category';
                                    $visibility_exclude[$j]['value']['label'] = get_the_category( $value )[0]->name;
                                    $visibility_exclude[$j]['value']['value'] = $value;
                                    $i++;
                                }
                            }
                        }elseif($display['type'] == 'archive_post_tag' || $display['type'] == 'taxonomy_post_tag'){
                            if($display['operator'] == 'is'){
                                foreach ($display['value'] as $key => $value) {
                                    $visibility_include[$i]['type']['label'] = 'Tags';
                                    $visibility_include[$i]['type']['value'] = 'tags';
                                    $visibility_include[$i]['value']['label'] = get_tag( $value )->name;
                                    $visibility_include[$i]['value']['value'] = $value;
                                    $i++;
                                }
                            }else{
                                foreach ($display['value'] as $key => $value) {
                                    $visibility_exclude[$j]['type']['label'] = 'Tags';
                                    $visibility_exclude[$j]['type']['value'] = 'tags';
                                    $visibility_exclude[$j]['value']['label'] = get_tag( $value )->name;
                                    $visibility_exclude[$j]['value']['value'] = $value;
                                    $i++;
                                }
                            }
                        }
                    }
                    $targeting_include = array();
                    $targeting_exclude = array();
                    $i=0;
                    $j=0;
                    foreach ($placement['options']['placement_conditions']['visitors'] as $visitors) {
                        if($visitors['type'] == 'mobile'){
                            if($visitors['operator'] == 'is'){
                                $targeting_include[$i]['type']['label'] = 'Device Type';
                                $targeting_include[$i]['type']['value'] = 'device_type';
                                $targeting_include[$i]['value']['label'] = 'mobile';
                                $targeting_include[$i]['value']['value'] = 'mobile';
                                $i++;
                            }else{
                                $targeting_exclude[$j]['type']['label'] = 'Device Type';
                                $targeting_exclude[$j]['type']['value'] = 'device_type';
                                $targeting_exclude[$j]['value']['label'] = 'mobile';
                                $targeting_exclude[$j]['value']['value'] = 'mobile';
                                $j++;
                            }
                        }elseif($visitors['type'] == 'loggedin'){
                            if($visitors['operator'] == 'is'){
                                $targeting_include[$i]['type']['label'] = 'Logged In';
                                $targeting_include[$i]['type']['value'] = 'logged_in';
                                $targeting_include[$i]['value']['label'] = 'True';
                                $targeting_include[$i]['value']['value'] = 'true';
                                $i++;
                            }else{
                                $targeting_exclude[$j]['type']['label'] = 'Logged In';
                                $targeting_exclude[$j]['type']['value'] = 'logged_in';
                                $targeting_exclude[$j]['value']['label'] = 'False';
                                $targeting_exclude[$j]['value']['value'] = 'false';
                                $j++;
                            }
                        }
                    }

                      $adlabel = $placement['options']['ad_label'];
                     $advance_ads_meta_key =array(
                        'ad_type'                       => $ad_type_label ,
                        'ads_list'                       => $ads_list_arr ,
                        'code'                          => $code,
                        'position'                      => $position,
                        'count_as_per'                  => $count_as_per,
                        'paragraph_number'              => $paragraph_number,
                        'repeat_paragraph'              => $repeat_paragraph,
                        'imported_from'                 => 'advance_ads',
                        'g_data_ad_client'              => $g_data_ad_client,
                        'g_data_ad_slot'                => $g_data_ad_slot,
                        'adsense_ad_type'               => $adsense_ad_type,
                        'image_redirect_url'            => $image_ads_url,
                        'image_src'                     => $post_meta_image_value_final,
                        'data_layout_key'               => $data_layout_key,
                        'label'                         => $posttitle,
                        'ad_label_check'                => $ad_label_check,
                        'adlabel'                       => 'above',
                        'ad_label_text'                 => $advanced_ads_options['custom-label']['text'],
                        'align'                         => $align,
                        'advance_ads_id'                => $id,
                        'ad_id'                         => $post_id,
                        'visibility_include'            => $visibility_include,
                        'visibility_exclude'            => $visibility_exclude,
                        'targeting_include'             => $targeting_include,
                        'targeting_exclude'             => $targeting_exclude,
                     );


                    foreach ($advance_ads_meta_key as $key => $val){
                        update_post_meta($post_id, $key, $val);
                    }
                    require_once QUADS_PLUGIN_DIR . '/admin/includes/migration-service.php';
                    $this->migration_service = new QUADS_Ad_Migration();
                    $this->migration_service->quadsUpdateOldAd($post_id, $advance_ads_meta_key);
                }
            }
            return  array('status' => 't', 'data' => esc_html__( 'Ads have been successfully imported', 'quick-adsense-reloaded' ) );
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
                            case 10:
                                    $position   =   'amp_doubleclick_sticky_ad';
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
            return  array('status' => 't', 'data' => esc_html__( 'Ads have been successfully imported', 'quick-adsense-reloaded' ) );

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
            echo esc_html($response);
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
                return array('status' => 'f', 'data' => esc_html__( 'data not found', 'quick-adsense-reloaded' ) );
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
                return array('status' => 'f', 'data' => esc_html__( 'data not found', 'quick-adsense-reloaded' ) );
            }

            return $response;

        }
        
        public function check_plugin_exist($request){

            $response = array();
            $parameters = $request->get_params();
            $plugin_name   = isset($parameters['plugin_name'])?$parameters['plugin_name']:'';
            if($plugin_name == 'amp_story'){
                if (defined('AMPFORWP_STORIES_PLUGIN_DIR')) {
                    return array('status' => 't');
                }
                if (defined('WEBSTORIES_VERSION')) {
                    return array('status' => 't');
                }     
            }
            return array('status' => 'f');
        }

        public function getCurrentUser($request){
            if(function_exists('current_user_can') && current_user_can( 'administrator' )){
                return array('status' => 't');
            }
            return array('status' => 'f');
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
            
            // Get only published ads from the posts table
            $published_ads = array();
            require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
            $api_service = new QUADS_Ad_Setup_Api_Service();
            $quads_ads = $api_service->getAdDataByParam('quads-ads');

            
            if(isset($settings['ads']) && is_array($settings['ads'])) {
                $filtered_ads = array();
                foreach($settings['ads'] as $ad_id => $ad_data) {
                    $ad_exists = false;
                    if(isset($quads_ads['posts_data'])) {
                        foreach($quads_ads['posts_data'] as $post_data) {
                            if(isset($post_data['post_meta']['quads_ad_old_id']) && $post_data['post_meta']['quads_ad_old_id'] == $ad_id) {
                                $ad_exists = true;
                                $ad_data['label'] = $post_data['post_meta']['label'];
                                break;
                            }
                        }
                    }
                    // Only keep ads that exist in post data
                    if($ad_exists) {
                        $filtered_ads[$ad_id] = $ad_data;
                    }
                }
                $settings['ads'] = $filtered_ads;
            }
            
            // Add published ads to settings export
            header( 'Content-Type: application/json; charset=utf-8' );
	        header( 'Content-Disposition: attachment; filename=' . apply_filters( 'quads_settings_export_filename', 'quads-settings-export-' . gmdate( 'm-d-Y' ) ) . '.json' );
            header( "Expires: 0" );
            return   $settings ;
        }
        public function importSettings($request){
            $files = $request->get_file_params();

            if ( ! empty( $files ) ) {
                $file_data = quads_local_file_get_contents($files['myFile']['tmp_name']);
                $file_data = json_decode($file_data,true);


                if ( ! empty( $file_data )) {

                    $settings = get_option( 'quads_settings' );
                    if(!$settings){
                        update_option('quads_settings',$file_data);
                        foreach($file_data['ads'] as $ad){
                            $temp_array = array();
                            $temp_array['quads_post_meta'] =$ad;
                        $ad_id      = $this->api_service->updateAdData($ad);
                        }
                    }else{
                        foreach($file_data['ads'] as $ad){
                            if($ad['ad_type']=='plain_text' && empty($ad['code'])){
                            }else{
                                $temp_array = array();
                            $temp_array['quads_post_meta'] =$ad;
                            $ad_id      = $this->api_service->updateAdData($temp_array);
                            }
                        }

                    }
                  
                }
            } 
return array('status' => 't');
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
                            $response = array('status'=> 't', 'msg' => esc_html__( 'Changed Successfully', 'quick-adsense-reloaded' ), 'data' => array());
                        }
                        break;
                    case 'draft':
                        $result = $this->api_service->changeAdStatus($ad_id, 'draft');
                        if($result){
                            $response = array('status'=> 't', 'msg' => esc_html__( 'Changed Successfully', 'quick-adsense-reloaded' ), 'data' => array());
                        }
                        break;
                    case 'duplicate':
                        $new_ad_id = $this->api_service->duplicateAd($ad_id);
                        $this->quads_clear_all_cache();
                        if($new_ad_id){
                            $data     = $this->api_service->getAdById($new_ad_id);
                            $response = array('status'=> 't', 'msg' => esc_html__( 'Duplicated Successfully', 'quick-adsense-reloaded' ), 'data' => $data);
                        }
                        break;
                    case 'delete':
                        $result = $this->api_service->deleteAd($ad_id);
                        if($result){
                            $response = array('status'=> 't', 'msg' => esc_html__( 'Deleted Successfully', 'quick-adsense-reloaded' ), 'data' => array());
                        }
                        break;
                    case 'clear_impression':
                        $result = $this->api_service->resetImpressionAndClick($ad_id);
                        if($result){
                            $response = array('status'=> 't', 'msg' => esc_html__( 'Reset Successfully', 'quick-adsense-reloaded' ), 'data' => array());
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
			 $nonce      =  $request->get_header('X-WP-Nonce');
			 if (isset($nonce) && !empty($nonce) && wp_verify_nonce($nonce,'wp_rest'))
			 {
             $customer_type  = esc_html__( 'Are you a premium customer ? No', 'quick-adsense-reloaded' );
             $message        = sanitize_textarea_field($parameters['message']);
             $email          = sanitize_text_field($parameters['email']);
             $premium_cus    = sanitize_text_field($parameters['type']);
			 

             if($premium_cus == 'yes'){
                $customer_type  = esc_html__( 'Are you a premium customer ? Yes', 'quick-adsense-reloaded');
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
                return array('status'=>'f', 'msg' => esc_html__( 'Please provide message and email' ,'quick-adsense-reloaded') );
             }
        }else{
		return array('status'=>'f', 'msg' => esc_html__( 'Invalid Request', 'quick-adsense-reloaded') );
		}}
        public function validateAdsTxt($request){

            $response = array();

            $parameters = $request->get_params();

            if($parameters[0]){
                $result = $this->api_service->validateAdsTxt($parameters[0]);
                if($result['errors']){
                    $response['errors'] = $result['errors'];
                }else{
                    $settings = quads_defaultSettings();
                    if($settings['adsTxtEnabled']){
                        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
                        if (false !== file_put_contents(ABSPATH . 'ads.txt', $parameters[0])) {
                            // show notice that ads.txt has been created
                            set_transient('quads_vi_ads_txt_notice', true, 300);
                        }else{
                          set_transient('quads_vi_ads_txt_error', true, 300);
                        }
                      }
                    $response['valid'] = true;
                }
            }
            return $response;

        }
        public function getSettings($request){

            $quads_settings = get_option('quads_settings');           
            $b_license = isset($quads_settings['quads_wp_quads_pro_license_key'])?$quads_settings['quads_wp_quads_pro_license_key']:'';
            $transient =  'quads_trans';
            $value =  $b_license;
            $expiration =  '' ;
            set_transient( $transient, $value, $expiration );
            // $b_license
        $strlen = strlen($b_license);
        $show_key = "";
        for($i=1;$i<$strlen;$i++){
            if($i<$strlen-4){
                $show_key .= "*";
            }else{
                $show_key .= $b_license[$i];
            }
        }
                   $quads_settings['quads_wp_quads_pro_license_key']  = $show_key ;

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
            $payment_page = (isset($quads_settings['payment_page']))?$quads_settings['payment_page']:'';
            $page_status = '';
            if($payment_page>0){
                $page_info = get_post($payment_page);
                if(!empty($page_info) && isset($page_info->post_status)){
                    $page_status = $page_info->post_status;
                }
            }
            $quads_settings['payment_page_status'] = $page_status;
            return $quads_settings;
        }

        public function getPages($request){

            global $wpdb;
            $results = wp_cache_get( 'quads_pages_list_api', 'quick-adsense-reloaded' );
            if ( false === $results ) {
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                $query = "SELECT ID, post_title,post_status FROM $wpdb->posts WHERE post_type = 'page' AND post_status in( 'publish','draft' ) ORDER BY post_title ASC LIMIT 0, 100";
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
                $results = $wpdb->get_results($query, ARRAY_A);
                wp_cache_set( 'quads_pages_list_api', $results, 'quick-adsense-reloaded' );
            }

             foreach ($results as $key => $value) {
                if($value['post_status']=='draft'){
                    $results[$key]['post_title'] = $value['post_title'].' - Draft';
                }
            }
            
            return $results;

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
                $response =  array('status' => '404', 'message' => esc_html__( 'property type is required', 'quick-adsense-reloaded' ));
            }
            return $response;


        }
        public function getAdById($request_data){

            $response = array();

            $parameters = $request_data->get_params();

            if(isset($parameters['ad-id'])){
                $response = $this->api_service->getAdById($parameters['ad-id']);
            }else{
                $response =  array('status' => '404', 'message' => esc_html__( 'Ad id is required', 'quick-adsense-reloaded' ));
            }
            return $response;

        }
    public function getAddNextId(){
        global $quads_options;
        $args = array();
        $ad_count = 1;
        if(isset($quads_options['ads']) && !empty($quads_options['ads'])){
            end($quads_options['ads']);
            $numberOfPosts = $post_Types = $post_args = '';
            $post_args = array ( 'post_type'=>'quads-ads' );
            $post_Types = new WP_Query($post_args);
            $numberOfPosts = $post_Types->found_posts;
            $key = key($quads_options['ads']);
            if(!empty($key)){
                if (strpos($key, 'ads_wp_qu') !== false || strpos($key, 'ads_excl_user_roles') !== false) {
                    $key = "ad".$numberOfPosts;
                    $key_array =   explode("ad",$key);
                    if( $key_array ){
                    $ad_count = (isset($key_array[1]) && !empty($key_array[1]))?$key_array[1]+1:1;
                }
                }
                if (strpos($key, 'ad') !== false) {
                    $key_array =   explode("ad",$key);
                    if( is_array($key_array) ){
                    $ad_count = (isset($key_array[1]) && !empty($key_array[1]))?$key_array[1]+1:1;
                }
                }
                else{
                    $key = "ad".$numberOfPosts;
                    $key_array =   explode("ad",$key);
                    if( $key_array ){
                    $ad_count = (isset($key_array[1]) && !empty($key_array[1]))?$key_array[1]+1:1;
                }
            }    
        }
    }
        $args['id'] = $ad_count;
        $args['name'] = 'Ad ' . $ad_count;

        return $args;
    }
        public function getAdList(){
            global $quads_options;
            $search_param = '';
            $rvcount      = 10;
            $attr         = array();
            $paged        =  1;
            $offset       =  0;
            $post_type    = 'quads-ads';
            $sort_by      = null;
            $filter_by    = null;
            $filter_not_by    = null;

            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
            if(isset($_GET['pageno'])){
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
                $paged    = absint( $_GET['pageno'] );
            }
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
            if(isset($_GET['posts_per_page'])){
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
                $rvcount = absint( $_GET['posts_per_page'] );
            }
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
            if(isset($_GET['search_param'])){
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
                $search_param = sanitize_text_field( wp_unslash( $_GET['search_param'] ) );
            }
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
            if(isset($_GET['sort_by'])){
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
                $sort_by = sanitize_text_field( wp_unslash( $_GET['sort_by'] ) );
            }
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
            if(isset($_GET['filter_by'])){
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
                $filter_by = sanitize_text_field( wp_unslash( $_GET['filter_by'] ) );
            }
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if(isset($_GET['filter_not_by'])){
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading the ads list on Ads page.
                $filter_not_by = sanitize_text_field( wp_unslash( $_GET['filter_not_by'] ) );
            }
            $result = $this->api_service->getAdDataByParam($post_type, $attr, $rvcount, $paged, $offset, $search_param , $filter_by , $sort_by, $filter_not_by);
           
            return $result;

        }
        public function getAdAnalytics(){
            $default_return =['impressions'=>0,'clicks'=>0];
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading ads analytics.
            if(isset($_GET['ad_id'])){
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended  -- Reason: We are not processing form information but only loading ads analytics.
                $ad_id    = absint( $_GET['ad_id'] );
                $ad_analytics= quads_get_ad_stats('sumofstats',$ad_id);
                return $ad_analytics;
            }
            return $default_return;
        }
        public function getAdTypes(){
            $ad_types = array(
                'adsense' => 'Adsense',
                'double_click' => 'Double Click',
                'plain_text' => 'Plain Text',
                'ad_image' => 'Ad Image',
            );
            
            // get all the ad types from the database in post meta for which ads are created
            $args = array(
                'post_type'      => 'quads-ads',
                'posts_per_page' => -1, // Get all posts
                'fields'         => 'ids' // Only retrieve post IDs
            );
            $query = new WP_Query($args);
            $post_ids = $query->posts;
            if (!empty($post_ids)) {
                $ad_types = array();
            
                foreach ($post_ids as $post_id) {
                    $ad_type = get_post_meta($post_id, 'ad_type', true);
                    if ($ad_type && !in_array($ad_type, $ad_types)) {
                        $ad_types[] = $ad_type; // Add distinct ad_types
                    }
                }
            }

            return $ad_types;
        }
        
        public function getAdSellList( $request ){
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'quads_adbuy_data'; 
            $page = (int) $request->get_param('page') ?: 1;
            $per_page = 10;
            $offset = ($page - 1) * $per_page;
            
            $results = wp_cache_get( 'wpquads_ad_sell_list_' . $page, 'quick-adsense-reloaded' );
            if ( false === $results ) {
                // Query the records
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is fixed and safe
                $results = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM `$table_name` WHERE payment_status = %s ORDER BY id DESC LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is fixed and safe
                    'paid',
                    $per_page,
                    $offset
                ));
                wp_cache_set( 'quads_ad_sell_list_' . $page, $results, 'quick-adsense-reloaded' );
            }
            $total = wp_cache_get( 'quads_ad_total_' . $page, 'quick-adsense-reloaded' );
            if( false === $total ) {
                /* phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is fixed and safe */
                $total = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE payment_status = %s", 'paid' ) );
                wp_cache_set( 'quads_ad_total_' . $page, $total, 'quick-adsense-reloaded' );
            }

            foreach ($results as $key => $result) {
                $ad_id = $result->ad_id;
                $ad_name = get_the_title($ad_id);

                $start_date = gmdate('Y-m-d');
                $end_date = $result->end_date;
                $st_date = new DateTime($start_date);

                $en_date = new DateTime($end_date);

                $difference = $st_date->diff($en_date);
                $days = $difference->days;
                
                $expiring_in = 'Expiring in '. intval($days). ' Days';
                if($en_date>$end_date){
                    $expiring_in = '';
                }
                $results[$key]->expiring_in = '';
                if($result->ad_status=='approved'){
                    $results[$key]->expiring_in = $expiring_in;
                }
              
                $results[$key]->start_date = gmdate('Y-m-d', strtotime($result->start_date));
                $results[$key]->end_date = gmdate('Y-m-d', strtotime($result->end_date));
            
                $date_display = gmdate('d M Y', strtotime($result->start_date)).' to '.gmdate('d M Y', strtotime($result->end_date));
                $results[$key]->date_display = $date_display;

                $results[$key]->ad_name = $ad_name;
            }
        
            return ['records'=>$results,'total'=> $total ];
        } 
        public function getDisabledAdsList( $request ){
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'quads_disabledad_data'; 
            $page = (int) $request->get_param('page') ?: 1;
            $per_page = 10;
            $offset = ($page - 1) * $per_page;

            $results = wp_cache_get( 'quads_disabled_ad_list_' . $page, 'quick-adsense-reloaded' );
            if ( false === $results ) {
                // Query the records
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is fixed and safe
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `$table_name` WHERE payment_status in('paid','unsubscribe') ORDER BY disable_ad_id DESC LIMIT %d OFFSET %d",$per_page,$offset));
                wp_cache_set( 'quads_disabled_ad_list_' . $page, $results, 'quick-adsense-reloaded' );
            }

            $total = wp_cache_get( 'quads_disabled_ad_total_' . $page, 'quick-adsense-reloaded' );
            if( false === $total ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is fixed and safe
                $total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*)  FROM `$table_name` WHERE payment_status in('paid','unsubscribe')"));
                wp_cache_set( 'quads_disabled_ad_total_' . $page, $total, 'quick-adsense-reloaded' );
            }

            $resp = array();
            foreach ($results as $key => $result) {
                $disable_duration = $result->disable_duration;
                $result->start_date = '';
                $result->end_date = '';
                $result->color = '#ef3400';
                if($result->payment_status=='unsubscribe'){
                    $result->color = '#005aef';
                }

                if( $result->payment_response !="" ){
                    $payment_response = json_decode( $result->payment_response, true );
                    if( isset( $payment_response['payment_date'] ) ){
                        $payment_date = $payment_response['payment_date'];
                        $futureDate= gmdate('Y-m-d');
                        $currentDate= gmdate('Y-m-d');
                        if( $disable_duration=='yearly' ){
                            $futureDate=gmdate('Y-m-d', strtotime('+1 year', strtotime($payment_date)) );
                        }else if( $disable_duration=='monthly' ){
                            $futureDate=gmdate('Y-m-d', strtotime('+1 month', strtotime($payment_date)) );
                        }
                        $result->start_date = gmdate( 'd M Y', strtotime( $payment_date ) );
                        $result->end_date = gmdate( 'd M Y', strtotime( $futureDate ) );
                    }
                }
                $resp[] = $result;
            }
        
            return ['records'=>$resp,'total'=> $total ];
        } 
        public function updateDisableAdStatus($request) {
            // Retrieve parameters from the request
            $id = (int) $request['id'];
            $status = sanitize_text_field($request['status']);
        
            $new_status = ($status === 'paid') ? 'unsubscribe' : 'paid';
    
            global $wpdb;
            $table_name = $wpdb->prefix . 'quads_disabledad_data';
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
            $status = $wpdb->update(
                $table_name,
                ['payment_status' => $new_status],
                ['disable_ad_id' => $id]
            );
            
            return ['success' => true];
        }
        public function updateAdsellStatus($request) {
            // Retrieve parameters from the request
            $id = (int) $request['id'];
            $status = sanitize_text_field($request['status']);
        
            $new_status = ($status === 'approved') ? 'approved' : 'disapproved';
    
            global $wpdb;
            $table_name = $wpdb->prefix . 'quads_adbuy_data';
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
            $status = $wpdb->update(
                $table_name,
                ['ad_status' => $new_status],
                ['id' => $id]
            );
            
            return ['success' => true];
        }
        
        public function getAdloggingData($request){
            $parameters = $request->get_params();
            $search_param = array();
            $rvcount      = 10;
            $attr         = array();
            $paged        =  1;
            $post_type    = 'quads-ads';
            $result       =  '';
            // if(isset($parameters['posts_per_page'])){
            //     $rvcount = sanitize_text_field($parameters['posts_per_page']);
            // }
             if(isset($parameters['report_period'])){

                $search_param['report_period'] = esc_html($parameters['report_period']);

                if(isset($parameters['cust_fromdate'])){
                    $search_param['cust_fromdate'] = esc_html($parameters['cust_fromdate']);
                }

                if(isset($parameters['cust_todate'])){
                    $search_param['cust_todate'] = esc_html($parameters['cust_todate']);
                }

            }
            if(isset($parameters['search_param'])){
                $search_param['search_param'] = esc_html($parameters['search_param']);
            }
            if(isset($parameters['page'])){
                $search_param['page'] = sanitize_text_field($parameters['page']);
            }

            $result =  quads_get_ad_stats('search','','',$search_param);
            return $result;

        }
        public function updateSettings($request_data){

            $response        = array();
            $parameters      = $request_data->get_params();
            $file            = $request_data->get_file_params();

            if(isset($file['file'])){

                $parts = explode( '.',$file['file']['name'] );
                if( end($parts) != 'json' ) {
                    $response = array('status' => 'f', 'msg' =>  esc_html__( 'Please upload a valid .json file', 'quick-adsense-reloaded' ));
                }

                $import_file = $file['file']['tmp_name'];
                if( empty( $import_file ) ) {
                    $response = array('status' => 'f', 'msg' =>  esc_html__( 'Please upload a file to import', 'quick-adsense-reloaded' ));
                }

                $settings = json_decode( quads_local_file_get_contents( $import_file ), true);
                update_option( 'quads_settings', $settings );
                $response = array('file_status' => 't','status' => 't', 'msg' =>  esc_html__( 'file uploaded successfully', 'quick-adsense-reloaded' ));

            }else{
                if(isset($parameters['settings'])){
                    $param_array=json_decode($parameters['settings'], true);
                    $param_array['refresh_license']=false;
                    if(isset($parameters['settings']) && isset($parameters['refresh_license']) && $parameters['refresh_license']==true)
                    {
                        $param_array['refresh_license']=true;
                    }
                    if(isset($param_array['QckTags'])){
                        $param_array['quicktags']['QckTags']=$param_array['QckTags']?1:0;
                    }
                    $result      = $this->api_service->updateSettings($param_array);
                    if($result){
                        $response = array('status' => 'tp', 'msg' =>  esc_html__( 'Settings has been saved successfully', 'quick-adsense-reloaded' ));
                         // when sellable is disabled then make buy-adspace slug page to draft
                        if(isset($param_array['sellable_ads']) && $param_array['sellable_ads'] == 0){
                            $page = get_page_by_path('buy-adspace');
                            if($page && $page->post_status == 'publish'){
                                wp_update_post(array('ID' => $page->ID, 'post_status' => 'draft'));
                            }
                        }
                        if(is_array($result)){
                            if ($result['license'] == "invalid") {
                                $response = array('status' => 'lic_not_valid','license'=>$result['license'], 'msgINV' =>  esc_html__( 'Settings has been saved successfully', 'quick-adsense-reloaded' ));

                            }
                            else
                                {
                                    if ($result['license'] == "valid") {
                                        $response = array('status' => 'license_validated','license'=>$result['license'], 'msgV' =>  esc_html__( 'Settings has been saved successfully', 'quick-adsense-reloaded' ));
                                    }
                                }
                            }
                        }
                    }
                }

            return $response;
        }
        public function updateAd($request_data){

            $parameters = $request_data->get_params();
            $ad_id      = $this->api_service->updateAdData($parameters);
            if($ad_id){
                $this->quads_clear_all_cache();

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
      $value = str_replace( ' ', '', strtolower( $role["name"] ) );
      $roles[$value] = $role["name"];
   }
   return $roles;
}
 public function quads_clear_all_cache(){
     if (function_exists('w3tc_flush_all')){
         w3tc_flush_all();
     }
     if ( function_exists( 'rocket_clean_domain' ) ) {
         rocket_clean_domain();
     }
     if ( defined( 'WPCACHEHOME' ) ) {
         global  $file_prefix;
         wp_cache_clean_cache( $file_prefix, true );
     }
 }

}
if(class_exists('QUADS_Ad_Setup_Api')){
    QUADS_Ad_Setup_Api::getInstance();
}
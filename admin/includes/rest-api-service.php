<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class QUADS_Ad_Setup_Api_Service {

    private $migration_service = null;
    private $amp_front_loop = array();

    public function __construct() {

        if($this->migration_service == null){
          require_once QUADS_PLUGIN_DIR . '/admin/includes/migration-service.php';
          $this->migration_service = new QUADS_Ad_Migration();
        }

    }

    public function getConditionList($condition, $search, $diff = null){

        $choices = array();

        switch($condition){

          case "post_type":

              $post_type = array();
              $args['public'] = true;

              if(!empty($search) && $search != null){
                $args['name'] = $search;
              }
              $choices = get_post_types( $args, 'names', 'and' );
              unset($choices['attachment'], $choices['amp_acf'], $choices['quads-ads']);

              if($choices){
                foreach($choices as $key =>$value){
                  $post_type[] = array('label' => $value, 'value' => $key);
                }
              }

              $choices = $post_type;

            break;

          case "page_template" :

            $choices[] = array('label' => 'Default Template', 'value' => 'default');

            $templates = wp_get_theme()->get_page_templates();

            if($templates){

                foreach($templates as $k => $v){

                     $choices[] = array('label' => $v, 'value' => $k);

                }

            }

            break;

          case "post" :
          case "page" :

            if($condition == 'page'){

              $post_types['page'] = 'page';

            }else{

              $post_types = get_post_types();
              unset( $post_types['page'], $post_types['attachment'], $post_types['revision'] , $post_types['nav_menu_item'], $post_types['acf'] , $post_types['amp_acf'],$post_types['saswp']  );

            }

            if( $post_types )
            {
	            foreach( $post_types as $post_type ){

                $arg['post_type']      = $post_type;
                $arg['posts_per_page'] = 10;
                $arg['post_status']    = 'any';

                if(!empty($search)){
                  $arg['s']              = $search;
                }

                $posts = $this->getPostsByArg($arg);

                if(isset($posts['posts_data'])){

                  foreach($posts['posts_data'] as $post){

                    $choices[] = array('value' => $post['post']['post_id'], 'label' => $post['post']['post_title']);

                  }

                }


              }
	            $choices = array_map("unserialize", array_unique(array_map("serialize", $choices)));


            }

            break;

          case "post_category" :

            $args = array(
                        'hide_empty' => false,
                        'number'     => 10,
                      );

            if(!empty($search)){
              $args['name__like'] = $search;
            }
            $args['taxonomy']   = 'category'; 
            $terms = get_terms( $args );

            if( !empty($terms) ) {

              foreach( $terms as $term ) {

                $choices[] = array('value' => $term->term_id, 'label' => $term->name);

              }

            }

            break;

          case "user_type" :
          case "post_format" :
          case "taxonomy" :
          case "general":
              $general_arr = array();
            if($condition == 'post_format'){
              $choices = get_post_format_strings();
            }else if($condition == 'user_type'){
              global $wp_roles;

              $choices = $wp_roles->get_names();
            }else if($condition == 'taxonomy'){

              $choices    = array('all' => esc_html__('All','quick-adsense-reloaded'));
              $taxonomies = $this->quads_post_taxonomy_generator();
              $choices    = array_merge($choices, $taxonomies);

            }else{
                $choices = array(
                  'homepage'      => 'HomePage',
                  'show_globally' => 'Show Globally',
                );
            }

            if(!empty($search) && $search != null){

                $search_user = array();

                foreach($choices as $key => $val){
                  if((strpos($key, $search) !== false) || strpos($key, $val) !== false){
                    $search_user[$key] = $val;
                  }
                }

                $choices = $search_user;
            }

            if($choices){
              foreach($choices as $key =>$value){
                $general_arr[] = array('label' => $value, 'value' => $key);
              }
            }

            $choices = $general_arr;

            break;

          case "tags" :

            $args = array(
              'hide_empty' => false,
              'number'     => 10,
            );

            if(!empty($search)){
              $args['name__like'] = $search;
            }

            $taxonomies = $this->quads_post_taxonomy_generator();

            foreach($taxonomies as $key => $val){

              if(strpos($key, 'tag') !== false){
                $args['taxonomy']   = $key;
                $terms = get_terms( $args );

                if( !empty($terms) ) {

                  foreach( $terms as $term ) {

                   $choices[] = array('value' => $term->slug, 'label' => $term->name);

                  }

                }

              }

            }

            break;
        }

     return $choices;
    }

    public function quads_post_taxonomy_generator(){

        $taxonomies = '';
        $choices    = array();

        $taxonomies = get_taxonomies( array('public' => true), 'objects' );

        if($taxonomies){

          foreach($taxonomies as $taxonomy) {

            $choices[ $taxonomy->name ] = $taxonomy->labels->name;

          }

        }

          // unset post_format (why is this a public taxonomy?)
          if( isset($choices['post_format']) ) {

            unset( $choices['post_format']) ;

          }

        return $choices;
    }

    public function getAdById($ad_id){

        $response  = array();
        $meta_data = array();

        if($ad_id){

            $response['post']      = get_post($ad_id, ARRAY_A);
            $post_meta             = get_post_meta($ad_id, '', true);

            if($post_meta){

                foreach($post_meta as $key => $meta){
                    if(is_serialized($meta[0])){
                      $meta_data[$key] = unserialize($meta[0]);
                    }else{
                      $meta_data[$key] = $meta[0];
                    }

                }
            }
            if(isset($meta_data['enabled_on_amp'])){
            if( $meta_data['enabled_on_amp'] == 1 ){
              $meta_data['enabled_on_amp'] = true;
            }else{
              $meta_data['enabled_on_amp'] = false;
            }
          }
            $response['post_meta'] = $meta_data;

        }
        return $response;

    }

    public function getAdDataByParam($post_type, $attr = null, $rvcount = null, $paged = null, $offset = null, $search_param=null , $filter_by = null , $sort_by = null,$filter_not_by = null){

        $response   = array();
        $arg        = array();
        $meta_query = array();
        $posts_data = array();

        $arg['post_type']      = $post_type;
        $arg['posts_per_page'] = -1;
        $arg['post_status']    = array('publish', 'draft');

        if(isset($attr['in'])){
          $arg['post__in']    = $attr['in'];
        }
        if(isset($attr['id'])){
          $arg['attachment_id']    = $attr['id'];
        }
        if(isset($attr['title'])){
          $arg['title']    = $attr['title'];
        }

        if($rvcount){
            $arg['posts_per_page']    = $rvcount;
        }
        if($paged){
            $arg['paged']    = $paged;
        }
        if($offset){
            $arg['offset']    = $offset;
        }
        
        if($search_param){
                if($filter_by){
                  $meta_query_args = array(
                    'relation' => 'AND',
                    array(
                      'key'     =>   'ad_type',
                      'value'   =>   $filter_by
                    ),
                    array(
                      'relation' => 'OR',
                      array(
                        'key'     =>   'label',
                        'value'   => $search_param,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key'     =>   'ad_type',
                        'value'   => $search_param,
                        'compare' => 'LIKE'
                    ),
                      array(
                      'key'     => 'ID', 
                      'value'   => intval($search_param),
                      'compare' => '='
                    )
                      )
                    );
                }else if($filter_not_by){
                  $meta_query_args = array(
                    'relation' => 'AND',
                    array(
                      'key'     =>   'ad_type',
                      'value'   =>   $filter_not_by,
                      'compare' =>   '!='
                    )
                    );
                }else{
                  $meta_query_args = array(
                    'relation' => 'OR',
                    array(
                        'key'     =>   'label',
                        'value'   => $search_param,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key'     =>   'ad_type',
                        'value'   => $search_param,
                        'compare' => 'LIKE'
                    ),
                      array(
                      'key'     => 'ID', 
                      'value'   => intval($search_param),
                      'compare' => '='
                    )
                    );
                }
                $arg['meta_query']          = $meta_query_args;
                $arg['paged']               = 1;
        }else{

          if($filter_by){
            $meta_query_args = array(
              array(
                'key'     =>   'ad_type',
                'value'   =>   $filter_by
              )
              );
              $arg['meta_query']          = $meta_query_args;
          }else if($filter_not_by){
            $meta_query_args = array(
              array(
                'key'     =>   'ad_type',
                'value'   =>   $filter_not_by,
                'compare' =>  '!='
              )
              );
              $arg['meta_query']          = $meta_query_args;
          }
         
        }

        if($sort_by){
          global $wpdb,$quads_options;
          $array_ids = [];
          $array_ids_result = [];
          if($sort_by =='impression'){
              if(isset($quads_options['report_logging']) && $quads_options['report_logging'] = 'improved_v2'){

                $array_ids_result = $wpdb->get_results("SELECT posts.ID as ID,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
                FROM {$wpdb->prefix}posts as posts
                LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id
                LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id
                WHERE posts.post_type='quads-ads'AND posts.post_status='publish'
                GROUP BY posts.ID
                ORDER BY total_impression DESC;");

              }else{
                $array_ids_result = $wpdb->get_results("SELECT `{$wpdb->prefix}posts`.ID,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression from `{$wpdb->prefix}quads_single_stats_`
                 INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id
                 GROUP BY `{$wpdb->prefix}posts`.ID 
                 ORDER BY total_impression DESC;");
              }
          }

          if($sort_by =='click'){
            if(isset($quads_options['report_logging']) && $quads_options['report_logging'] = 'improved_v2'){

              $array_ids_result = $wpdb->get_results("SELECT posts.ID as ID,IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click
              FROM {$wpdb->prefix}posts as posts
              LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id
              LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id
              WHERE posts.post_type='quads-ads'AND posts.post_status='publish'
              GROUP BY posts.ID
              ORDER BY total_click DESC;");

            }else{
              $array_ids_result = $wpdb->get_results("SELECT `{$wpdb->prefix}posts`.ID,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_`
               INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id
               GROUP BY `{$wpdb->prefix}posts`.ID 
               ORDER BY total_click DESC;");
            }

          }

          if(count($array_ids_result)>0){
            foreach($array_ids_result as $ids){
              $array_ids[]=$ids->ID;
            }
          }
          if(!empty($array_ids)){
            $arg['post__in']    = $array_ids;
            $arg['orderby']    = 'post__in';
            
          }

        }
        
        $response = $this->getPostsByArg($arg);
        return $response;
    }

    public function getPostsByArg($arg){

      $response = array();
      if(count($this->amp_front_loop)==0){
        $query_data =  get_posts($arg);
        $post_meta = array();
        $posts_data = array();
        foreach ($query_data as $key => $value) {
          $data = array();
          $data['post_id']       =  $value->ID;
          $data['post_title']    =  $value->post_title;
          $data['post_status']   =  $value->post_status;
          $data['post_modified'] =  $value->post_modified;
          $post_meta             = get_post_meta($data['post_id'], '', true);
          if($post_meta){
            foreach($post_meta as $key => $val ){
                if($key == 'ads_list'){
                  //$post_meta[$key] = $val[0];
                  $post_meta[$key] = unserialize($val[0]);
                }else{
                  $post_meta[$key] = $val[0];
                }
            }
          }
          $posts_data[] = array(
                  'post'        => (array) $data,
                  'post_meta'   => $post_meta
                  );
        }
        $response['posts_data']  = $posts_data;
        // if($posts_data[0]['post']['post_status'] == 'publish'){
          $response['posts_found'] = $this->getTotalAds();
        // }
         
        $this->amp_front_loop = $response;
      }else{
        $response = $this->amp_front_loop;
      }

        return $response;

    }

    public function updateSettings($parameters){

        $response = false;
        $license_info =array();
        if($parameters){
          $quads_options = get_option('quads_settings');

          foreach($parameters as $key => $val){

             if($key == 'QckTags'){
              $quads_options['quicktags'] = array($key => $val);
             }else if($key == 'adsTxtText' ){
              if($parameters['adsTxtEnabled']){
                /* phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents */
                if (false !== file_put_contents(ABSPATH . 'ads.txt', $val)) {
                    // show notice that ads.txt has been created
                    set_transient('quads_vi_ads_txt_notice', true, 300);
                }else{
                  set_transient('quads_vi_ads_txt_error', true, 300);
                }
              }
            } else{
              if($key == 'ad_blocker_support' && $val){
                $upload_dir = wp_upload_dir();
                $content_url = $upload_dir['basedir'].'/wpquads/tinymce_shortcode.js';
                  wp_mkdir_p($upload_dir['basedir'].'/wpquads', 755, true);
                  $sourc = QUADS_PLUGIN_URL . 'assets/js/tinymce_shortcode_uploads.js';
                  if (!file_exists($content_url)) {
                    copy($sourc,$content_url);
                  }
                  $sourc = QUADS_PLUGIN_URL . 'admin/assets/js/src/images/wpquads_classic_icon.png';
                  $content_url = $upload_dir['basedir'].'/wpquads/wpquads_classic_icon.png';
                  if (!file_exists($content_url)) {
                    copy($sourc,$content_url);
                  }
              }else if($key == 'quads_wp_quads_pro_license_key' && !empty($val) && (strpos($val, '****************') === false || $parameters['refresh_license']==true)){
                        $item_shortname='quads_wp_quads_pro';
                        $item_name ='WP QUADS PRO';
                        $license = sanitize_text_field($val );
                        // Data to send to the API
                        $api_params = array(
                          'edd_action' => 'activate_license',
                          'license'    => $license,
                          'item_name'  => urlencode( $item_name ),
                          'url'        => home_url()
                        );
                        if($parameters['refresh_license']==true)
                        {
                          $api_params['edd_action']='check_license';
                          $api_params['license'] = $quads_options['quads_wp_quads_pro_license_key'];
                        }
                        //check_license 
                        // Call the API
                        $response = wp_remote_post(
                          'http://wpquads.com/edd-sl-api/',
                          array(
                            'timeout'   => 15,
                            'sslverify' => false,
                            'body'      => $api_params
                          )
                        );

                        // Make sure there are no errors
                        if ( is_wp_error( $response ) ) {    
                          $response = array('status' => 't','license'=>$response, 'msg' =>  __( 'Settings has been saved successfully', 'quick-adsense-reloaded' ));
                        }
                        // Decode license data
                        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                        if($license_data){
                            $license_info = array('license'=>$license_data->license);
                            if($parameters['refresh_license']==true)
                            {
                              update_option( 'quads_wp_quads_pro_license_active', $license_data );
                            }
                        }
              }
              if ($key != 'quads_wp_quads_pro_license_key' || ($key == 'quads_wp_quads_pro_license_key'  && !empty($val) &&  strpos($val, '****************') === false)) {
                $quads_options[$key] = $val;
              }
            }

          }
         $response =  update_option( 'quads_settings', $quads_options );
         
        }
if($license_info){
  $response =$license_info;
}


        return $response;
    }

    public function validateAdsTxt($content){

        $sanitized = array();
        $errors    = array();

        if($content){

          $lines     = preg_split( '/\r\n|\r|\n/', $content );

            foreach ( $lines as $i => $line ) {
              $line_number = $i + 1;
              $result      = quads_validate_ads_txt_line( $line, $line_number );

              $sanitized[] = $result['sanitized'];
              if ( ! empty( $result['errors'] ) ) {
                $errors = array_merge( $errors, $result['errors'] );
              }
            }
            $sanitized = implode( PHP_EOL, $sanitized );
        }
        return array('errors' => $errors, 'sanitized_content' => $sanitized);

    }
    public function updateAdData($parameters, $mode = null){

            $post_meta      = $parameters['quads_post_meta'];
            $ad_id          = isset($post_meta['ad_id']) ? $post_meta['ad_id'] : '';
            $post_status    = 'publish';


            if(isset($parameters['quads_ad_status'])){
              $post_status    = $parameters['quads_ad_status'];
            }
           
            if(isset($post_meta['publish_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $post_meta['publish_date']) && $post_meta['publish_date'] >= gmdate("Y-m-d")){
              $post_status    = 'draft';
            }

            if($mode){

              $post_status    = 'draft';

              if($post_meta['code']  || $post_meta['g_data_ad_slot']){
                $post_status    = 'publish';
              }

            }

            $arg = array(
                'post_title'   => wp_strip_all_tags( $post_meta['label']),
                'post_status'  => sanitize_text_field($post_status),
                'post_type'    => 'quads-ads',
            );

            if($ad_id && !is_null(get_post($ad_id))){

                $arg['ID'] = $ad_id;

                @wp_update_post( $arg );

            }else{
                  $ad_id =   wp_insert_post( $arg );
            }
            if($post_meta){

                $post_meta['ad_id'] = $ad_id;

                foreach($post_meta as $key => $val){
                  

                    $filterd_meta = quads_sanitize_post_meta($key, $val);
                    if($key == 'ad_blindness'){
                      $filterd_meta =$val;
                    }
                    if($key == 'ab_testing'){
                      $filterd_meta =$val;
                    }
                    if($key == 'popup_ads'){
                      $filterd_meta =$val;
                    }
                    if($key == 'video_ads'){
                      $filterd_meta =$val;
                    }
                    if($key == 'parallax_ads'){
                      $filterd_meta =$val;
                    }
                    if($key == 'half_page_ads'){
                      $filterd_meta =$val;
                    }
                    if($key == 'floating_slides'){
                      $filterd_meta =$val;
                    }
                    if($key == 'set_spec_day'){
                      $filterd_meta =$val;
                    }
                    if($key == 'mob_code'){
                      $filterd_meta =$val;
                    }

                    update_post_meta($ad_id, $key, $filterd_meta);
                }
            }

            //Saving post data to quads settings
            if( $mode == null ){

              $this->migration_service->quadsUpdateOldAd($ad_id, $post_meta);

            }

            return  $ad_id;
    }

    public function changeAdStatus($ad_id, $action){

      $adid_array = is_array($ad_id)?$ad_id:explode(',',$ad_id);
      if($adid_array && !empty($adid_array)){
        foreach($adid_array as $adid){
          $response = wp_update_post(array(
            'ID'            =>  $adid,
            'post_status'   =>  $action
          ));
        }
        return $response;
      }
    }
    public function duplicateAd($ad_id){

      $response = null;

      global $wpdb;
      $post = get_post( $ad_id);

      if ( isset( $post ) && $post != null ) {
         // args for new post
          $args = array(
            'post_title'       => $post->post_title,
            'post_status'      => $post->post_status,
            'post_type'        => $post->post_type,
            );

          $new_post_id = wp_insert_post( $args );

          $post_metas = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=%d",$ad_id));

          if ( count( $post_metas )!=0 ) {

            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare
            $sql_query = $wpdb->prepare( "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) ");

            foreach ( $post_metas as $post_meta ) {

             $meta_key = esc_sql($post_meta->meta_key);

             if( $meta_key == '_wp_old_slug' ) continue;

                if($meta_key == 'ad_id'){
                  $meta_value = esc_sql( $new_post_id);
                }else{
                  $meta_value = esc_sql( $post_meta->meta_value);
                }
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $sql_query_sel[]= $wpdb->prepare( "SELECT $new_post_id, '$meta_key', '$meta_value' " );
             }

             $sql_query.= implode(" UNION ALL ", $sql_query_sel);
             /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */
             $wpdb->query( $sql_query );
             $post_meta= $this->getAdById($new_post_id);
             $this->migration_service->quadsUpdateOldAd($new_post_id, $post_meta['post_meta'],'update_old');
            }
            $response = $new_post_id;
      }

      return $response;
    }

	public function deleteAd($ad_id){
//  current_user_can already checked in class QUADS_Ad_Setup_Api
    $quads_settings = get_option('quads_settings');
    $ads_ids = is_array($ad_id)?$ad_id:explode(',',$ad_id);
    if(!empty($ads_ids)){
      foreach($ads_ids as $adid){
        $old_ad_id      = get_post_meta($adid, 'quads_ad_old_id', true);
        unset($quads_settings['ads'][$adid]);
        $response = wp_delete_post($adid, true);
      }
      return $response;
      update_option('quads_settings', $quads_settings);
    }	
	}

    public function getPlugins($search){

      $response = array();
      $response[] = array('value' => 'woocommerce', 'label' => 'woocommerce');
      $response[] = array('value' => 'buddypress', 'label' => 'buddypress');
      return $response;

    }

    private function getTotalAds()
    {
      global $wpdb;
      if(defined('quads_add_count')){
        return quads_add_count;
      }else{
        $total_result = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(ID) as total_posts FROM $wpdb->posts Where post_type=%s AND (post_status=%s OR post_status=%s) ",'quads-ads','publish','draft'), 0, 0 );
        
        if($total_result)
        {
          $total_result = (int) $total_result;
        }else{
          $total_result = 0;
        }
        // setcookie("quads_add_count", $total_result, time() + (60 * 5));
        define('quads_add_count', $total_result);
        return $total_result;
      }
    }
}
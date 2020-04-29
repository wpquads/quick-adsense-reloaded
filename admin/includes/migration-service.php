<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$quads_migration_flag;

class QUADS_Ad_Migration {   
                
     public function quadsSaveAllAdToNewDesign(){

            global $quads_migration_flag;
            $upgrade_option = get_option('quads_ad_migrated_to_new_desing_70');

            if(!$upgrade_option && ! isset( $quads_migration_flag )){
              
                $ads = array();

                $quads_settings = get_option( 'quads_settings' );   

                if(isset($quads_settings['ads'])){
                    $ads            = $quads_settings['ads'];
                }
               
                if($ads){
                                        
                    $i=1;
                    foreach($ads as $key => $value){
                                                
                        if($key === 'ad'.$i){
                                                                                            
                                $post_title = sanitize_text_field($value['label']);
                           
                                $post_status = 'draft';

                                if($value['code'] || $value['g_data_ad_slot'] || $value['g_data_ad_client']){
                                    $post_status = 'publish';
                                }

                                $args = array(
                                    'post_title'   => $post_title,                                                            
                                    'post_status'  => $post_status,
                                    'post_type'    => 'quads-ads',
                                );

                                $old_post_id = quadsGetPostIdByMetaKeyValue('quads_ad_old_id', $key);

                                if(!$old_post_id){
                                    
                                    $ad_id = wp_insert_post( $args );

                                    if($ad_id){
                                                                         
                                        $value['quads_ad_old_id']        = sanitize_text_field($key);
                                        $value['ad_id']                  = $ad_id;                                                     
            
                                        if($quads_settings['pos1']['BegnRnd'] == $i){
                                            $value['position']                  = 'beginning_of_post';
                                        }
                                        if($quads_settings['pos2']['MiddRnd'] == $i){
                                            $value['position']                  = 'middle_of_post';
                                        }
                                        if($quads_settings['pos3']['EndiRnd'] == $i){
                                            $value['position']                  = 'end_of_post';
                                        }
                                        if($quads_settings['pos4']['MoreRnd'] == $i){
                                            $value['position']                  = 'after_more_tag';
                                        }
                                        if($quads_settings['pos5']['LapaRnd'] == $i){
                                            $value['position']                  = 'before_last_paragraph';
                                        }
                                        if($quads_settings['pos6']['Par1Rnd'] == $i){
                                            $value['position']                  = 'after_paragraph';
                                            $value['paragraph_number']          = $quads_settings['pos6']['Par1Nup'];

                                            if(isset($quads_settings['pos6']['Par1Con'])){
                                                $value['enable_on_end_of_post']                  = 1;
                                            }

                                        }
                                        if($quads_settings['pos7']['Par2Rnd'] == $i){
                                            $value['position']                  = 'after_paragraph';
                                            $value['paragraph_number']          = $quads_settings['pos7']['Par2Nup'];

                                            if(isset($quads_settings['pos7']['Par2Con'])){
                                                $value['enable_on_end_of_post']                  = 1;
                                            }
                                        }
                                        if($quads_settings['pos8']['Par3Rnd'] == $i){
                                            $value['position']                  = 'after_paragraph';
                                            $value['paragraph_number']          = $quads_settings['pos8']['Par3Nup'];

                                            if(isset($quads_settings['pos8']['Par3Con'])){
                                                $value['enable_on_end_of_post']                  = 1;
                                            }
                                        }
                                        if($quads_settings['pos9']['Img1Rnd'] == $i){
                                            $value['position']                  = 'after_image';
                                            $value['image_number']              = $quads_settings['pos9']['Img1Nup'];
                                            if(isset($quads_settings['pos9']['Img1Con'])){
                                                $value['image_caption']                  = 1;
                                            }
                                        }

                                        $x = 1;
                                        while (isset($quads_settings['extra'.$x])){ 
                                            
                                            if($quads_settings['extra'.$x]['ParRnd'] == $i){

                                                $value['position']                  = 'after_paragraph';
                                                $value['paragraph_number']          = $quads_settings['extra'.$x]['ParNup'];
    
                                                if(isset($quads_settings['extra'.$x]['ParCon'])){
                                                    $value['enable_on_end_of_post']                  = 1;
                                                }
    
                                            }
                                            
                                            $x++;
                                        }
                                        $visibility = array();
                                        if(isset($quads_settings['AppHome'])){
                                            $visibility[] = array(
                                                'type'  => array('label' => 'General', 'value' => 'general'),
                                                'value' => array('label' => 'Homepage', 'value' => 'homePage')
                                            );                                            
                                        }
                                        if(isset($quads_settings['AppCate'])){
                                            $visibility[] = array(
                                                'type'  => array('label' => 'Taxonomy', 'value' => 'taxonomy'),
                                                'value' => array('label' => 'Categories', 'value' => 'category')
                                            );                                            
                                        }
                                        if(isset($quads_settings['AppArch'])){
                                            $visibility[] = array(
                                                'type'  => array('label' => 'Taxonomy', 'value' => 'taxonomy'),
                                                'value' => array('label' => 'All', 'value' => 'all')
                                            );                                                                                        
                                        }
                                        if(isset($quads_settings['AppTags'])){
                                            $visibility[] = array(
                                                'type'  => array('label' => 'Taxonomy', 'value' => 'taxonomy'),
                                                'value' => array('label' => 'Tags', 'value' => 'post_tag')
                                            );                                            
                                        }
                                        if(isset($quads_settings['post_types'])){

                                            foreach($quads_settings['post_types'] as $type_val){                                                
                                                
                                                $visibility[] = array(
                                                    'type'  => array('label' => 'Post Type', 'value' => 'post_type'),
                                                    'value' => array('label' => $type_val, 'value' => $type_val)
                                                );

                                            }    
                                            
                                        }

                                        $value['visibility_include'] =  $visibility;                                        
                                        foreach($value as $key => $val){
            
                                            update_post_meta($ad_id, $key, $val);
                        
                                        } 
        
                                    }  
                                }                                                      

                        }
                        
                        $i++;
                        wp_reset_query();
                        wp_reset_postdata();   
                    }                        
                                                                                           
                }

                $quads_migration_flag = true;
                update_option('quads_ad_migrated_to_new_desing_70', date("Y-m-d"));  

            }
         
     }

     public function quadsUpdateOldAd($ad_id, $post_meta){

            $new_data = array();

            $new_data = $post_meta;

            $old_ad_id      = get_post_meta($ad_id, 'quads_ad_old_id', true);
            
            $quads_settings = get_option( 'quads_settings' );

            if($old_ad_id){                                 
                $quads_settings['ads'][$old_ad_id] = $new_data;                
            }else{
                
            $old_ads = array();    
            $ad_count = 1;

            foreach($quads_settings['ads'] as $key => $ads){

                if($key == 'ad'.$ad_count){
                    $ad_count++;
                    $old_ads[$key] = $ads;
                }

            }            
            $old_ads['ad'.$ad_count] = $new_data;
            $quads_settings['ads'] = $old_ads;            
            update_post_meta($ad_id, 'quads_ad_old_id', 'ad'.$ad_count);

            }                                                       
            update_option('quads_settings', $quads_settings);

            return $ad_id;
     } 
    public function quadsImportadsforwp(){

        $export_ad       = array();                 
            //ads
        $all_ads_post = get_posts(
            array(
                'post_type'        => 'adsforwp',                                                                                   
                'posts_per_page'   => -1,   
                'post_status'      => 'any',
            )
        );    
        if($all_ads_post){
            foreach($all_ads_post as $ads){  
              $post_title = (isset($ads->post_title) && !empty($ads->post_title))?$ads->post_title: 'ads for wp';
              $post_data = array(
                'post_title'   => wp_strip_all_tags( $post_title),                                                            
                'post_status'  => sanitize_text_field($ads->post_status),
                'post_type'    => 'quads-ads',
            );
                    $post_id = wp_insert_post($post_data); // insert to post and RETURN POST id
                    $post_meta = get_post_meta($ads->ID, $key='', true );// import meta data from adsforwp  
                    update_post_meta( $post_id, 'label', $post_title);
                    update_post_meta( $post_id, 'ad_id', $post_id);
                    update_post_meta( $post_id, 'importfrom','amp-for-wp');
                    
                    foreach ($post_meta as $key => $value) {
                        if($key == 'custom_code'){
                            update_post_meta( $post_id, 'code', $value[0]);// need to all speical all
                        }else if($key == 'select_adtype'){
                            if($value[0]=='custom'){
                                update_post_meta( $post_id, 'ad_type', 'plain_text');
                            }else if($value[0]=='adsense'){
                                 update_post_meta( $post_id, 'ad_type', 'adsense');
                            }
                        }else if($key == 'data_client_id'){
                            update_post_meta( $post_id, 'g_data_ad_slot', esc_html($value[0]));
                        }else if($key == 'data_ad_slot'){
                            update_post_meta( $post_id, 'g_data_ad_client', esc_html($value[0]));
                        }else if($key == 'wheretodisplay'){
                            if(is_string($value) && strpos($value, 'adsforwp_') !== false){
                                update_post_meta( $post_id, 'enabled_on_amp', 1);
                            }else{
                                update_post_meta( $post_id, 'enabled_on_amp', 0);
                            }
                            if($key == 'after_the_content' || $key = 'adsforwp_above_the_post_content'){
                                update_post_meta( $post_id, 'position', 'beginning_of_post');
                            }else if($key == 'between_the_content'){
                                update_post_meta( $post_id, 'position', 'middle_of_post');
                            }else if($key == 'adsforwp_below_the_post_content'){
                                update_post_meta( $post_id, 'position', 'end_of_post');
                            }else{
                             update_post_meta( $post_id, 'position', 'beginning_of_post');// need to check it once
                         }                            
                        }else if($key == 'adsforwp_ad_align'){
                            switch ($value[0]) {
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
                        }else if($key == 'adsforwp_ad_margin'){
                            update_post_meta( $post_id, 'margin', esc_html($value[0]));
                        }if($key == 'data_group_array'){
                            $visibility_include = array();
                            $visibility_exclude = array();
                            $data_group_array = unserialize($value[0]);
                            $i =0;  $j =0;

                            foreach ($data_group_array as $key => $value) {
                                $label = '';
                                switch ($value['data_array'][0]['key_1']) {
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

                                if($value['data_array'][0]['key_2'] == 'equal'){
                                    $visibility_include[$i]['type']['label'] = $label;
                                    $visibility_include[$i]['type']['value'] = 'post_type';
                                    $visibility_include[$i]['value']['label'] = $label;
                                    $visibility_include[$i]['value']['value'] = esc_html($value['data_array'][0]['key_3']);
                                    $i++;
                                }else{
                                    $visibility_exclude[$j]['type']['label'] = $label;
                                    $visibility_exclude[$j]['type']['value'] = 'post_type';
                                    $visibility_exclude[$j]['value']['label'] = $label;
                                    $visibility_exclude[$j]['value']['value'] = esc_html($value['data_array'][0]['key_3']);
                                    $j++;
                                }
                                update_post_meta( $post_id, 'visibility_include', $visibility_include);
                                update_post_meta( $post_id, 'visibility_exclude', $visibility_exclude);
                            }
                        }
                    }   
            }  
           
        } 
          return  array('status' => 't', 'data' => 'Data Imported Success');  
 
    }
}

<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$quads_migration_flag;

class QUADS_Ad_Migration {   
                
     public function quadsSaveAllAdToNewDesign(){

            global $quads_migration_flag;
            $upgrade_option = get_option('quads_ad_migrated_to_new_desing_70');

            if(!$upgrade_option && ! isset( $quads_migration_flag )){
                 update_option('quads_ad_migrated_to_new_desing_70', date("Y-m-d"));  
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
                                    if ( ! function_exists( 'post_exists' ) ) {
                                        require_once( ABSPATH . 'wp-admin/includes/post.php' );
                                    }
                                     if(!post_exists( $arg['post_title'] )){
                                        $ad_id =   wp_insert_post( $arg ); 
                                    }else{
                                        $ad_id = post_exists( $arg['post_title'] );
                                    }

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
                
}

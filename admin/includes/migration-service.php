<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$quads_migration_flag;

class QUADS_Ad_Migration {   
                
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

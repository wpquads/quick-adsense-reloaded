<?php
/**
 * This class handles displaying ads according to amp display conditions
 */
class quads_output_amp_condition_display{            
      private $api_service = null;
    function __construct() {
      if($this->api_service == null){
        require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
        $this->api_service = new QUADS_Ad_Setup_Api_Service();
      }
  	}
    /**
     * List of all hooks which are used in this class
     */
    public function quads_amp_condition_hooks(){
      global $quads_mode;
      if($quads_mode != 'new'){
        return ;
      }
       // Below the Header 
        //Amp custom theme
        add_action( 'ampforwp_add_loop_class', array($this, 'ampforwp_add_loop_class_above_ad') );
        
        add_action( 'ampforwp_after_header', array($this, 'quads_display_ads_below_the_header') );
        add_action( 'ampforwp_before_head', array($this, 'quads_display_ads_before_head') );
        add_action( 'ampforwp_design_1_after_header', array($this, 'quads_display_ads_below_the_header') ); 
        
        //Below the Footer
        add_action( 'amp_post_template_footer', array($this, 'quads_display_ads_below_the_footer') );
        
        //Below Footer Sticky AD
        add_action( 'amp_post_template_footer', array($this, 'quads_display_ads_sticky_doubleclick') );
        
        //ABove the Footer
        add_action( 'amp_post_template_above_footer', array($this, 'quads_display_ads_above_the_footer') );
        
        //Above the Post Content
        add_action( 'ampforwp_before_post_content', array($this, 'quads_display_ads_above_the_post_content') );
        add_action( 'ampforwp_inside_post_content_before', array($this, 'quads_display_ads_above_the_post_content') );
        
        // Below the Post Content
        add_action( 'ampforwp_after_post_content', array($this, 'quads_display_ads_below_the_post_content') );
        add_action( 'ampforwp_inside_post_content_after', array($this, 'quads_display_ads_below_the_post_content') );
        
        //Below The Title
        add_action('ampforwp_below_the_title',array($this, 'quads_display_ads_below_the_title'));
        
        //Above the Related Post
        add_action('ampforwp_above_related_post',array($this, 'quads_display_ads_above_related_post'));
        
        // Below the Author Box
        add_action( 'ampforwp_below_author_box', array($this, 'quads_display_ads_below_author_box') );
        // In loops
        add_action('ampforwp_between_loop', array($this, 'quads_display_ads_between_loop'),10,1);        
        // Ad After Featured Image #42
        add_action('ampforwp_after_featured_image_hook',array($this, 'quads_display_ads_after_featured_image'));
	    add_filter('amp_story_auto_ads_configuration',array($this,'quads_amp_story_ads'));


    }
	public function quads_amp_story_ads($data){

	return	$this->quads_amp_condition_ad_code('quads_amp_story_ads',$data);

	}

    public function quads_display_ads_after_featured_image(){   
        
            $this->quads_amp_condition_ad_code('quads_after_featured_image');   
            
    }
    
    public function quads_display_ads_between_loop($count){                     
                        
      $this->quads_amp_condition_ad_code('quads_ads_in_loops', $count);                
                        
    }    
    
    public function quads_display_ads_below_author_box(){    
        
            $this->quads_amp_condition_ad_code('quads_below_author_box');      
            
    }
    public function quads_display_ads_above_related_post(){ 
        
            $this->quads_amp_condition_ad_code('quads_above_related_post');    
            
    }
    public function quads_display_ads_below_the_title(){ 
        
            $this->quads_amp_condition_ad_code('quads_below_the_title');  
            
    }
    public function quads_display_ads_below_the_post_content(){  
        
            $this->quads_amp_condition_ad_code('quads_below_the_post_content');  
            
    }
    public function quads_display_ads_above_the_post_content(){  
        
            $this->quads_amp_condition_ad_code('quads_above_the_post_content');  
            
    }    
    public function quads_display_ads_above_the_footer(){     
        
            $this->quads_amp_condition_ad_code('quads_above_the_footer');    
            
    }
    
    public function quads_display_ads_below_the_footer(){  
        
            $this->quads_amp_condition_ad_code('quads_below_the_footer');    
            
    }
    
    public function quads_display_ads_sticky_doubleclick(){  
        
            $this->quads_amp_condition_ad_code('quads_sticky_ad_doubleclick');    
            
    }
    
    public function quads_display_ads_below_the_header(){  
        
            $this->quads_amp_condition_ad_code('quads_below_the_header');
            
    }
    public function quads_display_ads_before_head(){  
        
      $this->quads_amp_condition_ad_code('quads_before_head');
      
}
    
    /**
     * Here, We are displaying ads or group ads according to amp where to display condition
     * @param type $condition
     * @param type $count
     */
    public function quads_amp_condition_ad_code($condition, $count=null){               
                
        $result = $this->quads_amp_condition_get_ad_code($condition, $count);
        if($condition == 'quads_amp_story_ads')
            return $result;
        else
	        echo $result;
    } 
    public function quads_amp_condition_get_ad_code($condition, $count=null){
      // if (quads_is_amp_endpoint()){
      // return ;
      // }

      global $quads_options;
      $quads_ads = $this->api_service->getAdDataByParam('quads-ads');
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

          if(isset($ads['ad_id']))
            $post_status = get_post_status($ads['ad_id']); 
            else
              $post_status =  'publish';

            if(isset($ads['random_ads_list']))
            $ads['random_ads_list'] = unserialize($ads['random_ads_list']);
         if(isset($ads['visibility_include']))
             $ads['visibility_include'] = unserialize($ads['visibility_include']);
         if(isset($ads['visibility_exclude']))
             $ads['visibility_exclude'] = unserialize($ads['visibility_exclude']);

         if(isset($ads['targeting_include']))
             $ads['targeting_include'] = unserialize($ads['targeting_include']);

         if(isset($ads['targeting_exclude']))
             $ads['targeting_exclude'] = unserialize($ads['targeting_exclude']);
            $is_on         = quads_is_visibility_on($ads);
            $is_visitor_on = quads_is_visitor_on($ads);

             if($is_on && $is_visitor_on && $post_status=='publish' ||$condition == 'quads_amp_story_ads'){
	             if($ads['position'] =='amp_after_featured_image' && $condition == 'quads_after_featured_image'){
              $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
              echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if(($ads['position'] =='amp_below_the_header' || $ads['position'] == 'after_header') && $condition == 'quads_below_the_header'){
            $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
            echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if($ads['position'] =='amp_doubleclick_sticky_ad' && $condition == 'quads_sticky_ad_doubleclick'){
                global $quads_options;
                $ads_id = $ads['quads_ad_old_id'];
                $width        = ( isset($quads_options['ads'][$ads_id]['g_data_ad_width']) ) ? $quads_options['ads'][$ads_id]['g_data_ad_width'] : '300';
                $height        = ( isset($quads_options['ads'][$ads_id]['g_data_ad_height']) ) ? $quads_options['ads'][$ads_id]['g_data_ad_height'] : '250';
                $network_code  = $quads_options['ads'][$ads_id]['network_code'];
                $ad_unit_name  = $quads_options['ads'][$ads_id]['ad_unit_name'];
		             add_filter('amp_post_template_data',array($this, 'quads_enque_ads_specific_amp_script'));
		             $output = '<amp-sticky-ad layout="nodisplay"><amp-ad class="amp-sticky-ads" width='.$width.' height='.$height.' type="doubleclick" data-slot="'.$network_code.'" ></amp-ad></amp-sticky-ad>';
                 echo $output;
          }else if($ads['position'] =='amp_below_the_footer' && $condition == 'quads_below_the_footer'){
            $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
            echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if($ads['position'] =='amp_above_the_footer' && $condition == 'quads_above_the_footer'){
            $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
            echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if($ads['position'] =='amp_above_the_post_content' && $condition == 'quads_above_the_post_content'){
            $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
            echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if($ads['position'] =='amp_below_the_post_content' && $condition == 'quads_below_the_post_content'){
            $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
            echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if($ads['position'] =='amp_below_the_title' && $condition == 'quads_below_the_title'){
            $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
            echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if($ads['position'] =='amp_above_related_post' && $condition == 'quads_above_related_post'){
            $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
            echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if($ads['position'] =='amp_below_author_box' && $condition == 'quads_below_author_box'){
            $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
            echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if($ads['position'] =='amp_ads_in_loops' && $condition == 'quads_ads_in_loops'){
            $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
            $ads_loop_number = (isset($ads['ads_loop_number']) && !empty($ads['ads_loop_number']))? $ads['ads_loop_number'] : 1;
               $ad_code ='';
            $displayed_posts        = get_option('posts_per_page');        
            if(intval($ads_loop_number) == $count){            
                echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
            }
         }else if($ads['position'] =='quads_before_head' && $condition == 'before_header'){
          $tag= '<!--CusAds'.$ads['ad_id'].'-->'; 
          echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
       }else if($ads['position'] =='amp_story_ads' && $condition == 'quads_amp_story_ads'){

		             $data = array();
	             if($ads['ad_type'] == 'adsense') {
		             $data[] = array(
			             "ad-attributes" => array(
				             "type"      => "adsense",
				             "data-ad-client" =>esc_attr($ads['g_data_ad_client']),
				             "data-ad-slot" => esc_attr($ads['g_data_ad_slot']),
			             ),
		             );
	             }elseif($ads['ad_type'] == 'double_click') {
		             $data[] = array(
			             "ad-attributes" => array(
				             "type"      => "doubleclick",
				             "data-slot" => esc_attr($ads['network_code']).'/'.esc_attr($ads['ad_unit_name']),
			             ),
		             );
	             }
             return $data;
         } else if( $condition == 'quads_below_the_footer' && $ads['adsense_ad_type'] == 'adsense_sticky_ads' ){
		             $width        = (isset($ads['g_data_ad_width']) && !empty($ads['g_data_ad_width'])) ? $ads['g_data_ad_width'] : '300';
		             $height        = (isset($ads['g_data_ad_height']) && !empty($ads['g_data_ad_height'])) ? $ads['g_data_ad_height'] : '250';

		             add_filter('amp_post_template_data',array($this, 'quads_enque_ads_specific_amp_script'));
		             $output  = '<amp-sticky-ad layout="nodisplay">';
		             $output .= '<amp-ad class="amp-sticky-ads quads'.esc_attr($ads["quads_ad_old_id"]).'"
                                                     type="adsense"
                                                     width='. esc_attr($width) .'
                                                     height='. esc_attr($height) . '
                                                     data-ad-client="'. esc_attr($ads["g_data_ad_client"]) .'"
                                                     data-ad-slot="'.  esc_attr($ads["g_data_ad_slot"]) .'"
                                                     data-enable-refresh="10">';
		             $output	.=	'</amp-ad>';
		             $output	.= '</amp-sticky-ad>';
		             echo $output;
             }
        }
        }
      }
    }
    public function quads_enque_ads_specific_amp_script($data){
	    if ( empty( $data['amp_component_scripts']['amp-sticky-ad'] ) ) {
		    $data['amp_component_scripts']['amp-sticky-ad'] = 'https://cdn.ampproject.org/v0/amp-sticky-ad-latest.js';
	    }
	    return $data;

    }
        
  }
if (class_exists('quads_output_amp_condition_display')) {   
    
        add_action('amp_init', 'quads_amp_hooks_call');
        
        function quads_amp_hooks_call(){
            
            $quads_condition_obj = new quads_output_amp_condition_display;
            $quads_condition_obj->quads_amp_condition_hooks();   
        
        }        	
};

<?php
/**
 * Template Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Templates
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// we need to hook into the_content on lower than default priority (that's why we use separate hook)
add_filter('the_content', 'quads_post_settings_to_quicktags', quads_get_load_priority());
add_filter('the_content', 'quads_process_content', quads_get_load_priority());
add_filter('rest_prepare_post', 'quads_classic_to_gutenberg', 10, 1);
add_filter('the_content', 'quads_change_adsbygoogle_to_amp',11);
add_action('wp_head',  'quads_common_head_code');
add_action( 'the_post', 'quads_in_between_loop' , 20, 2 );
add_action( 'init', 'quads_background_ad' );
add_action( 'loop_start', 'quads_search_and_archive_ads' );
add_action('amp_post_template_head','quads_adsense_auto_ads_amp_script',1);
add_action('amp_post_template_footer','quads_adsense_auto_ads_amp_tag');
add_action( 'plugins_loaded', 'quads_plugins_loaded_bbpress', 20 );
add_action( 'wp_footer', 'quads_parse_floating_cubes_ads' );
add_action( 'init', 'quads_remove_ads_for_wp_shortcodes',999 );

function quads_get_complete_html( $content_buffer ) {
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound --Reason ALready prefixed.
    $content_buffer = apply_filters('wp_quads_content_html_last_filter', $content_buffer);
    return  $content_buffer;
  }
  add_action('wp', function(){ ob_start('quads_get_complete_html'); }, 999);
function quads_plugins_loaded_bbpress(){
  global $quads_mode;
      if($quads_mode != 'new' || !class_exists( 'bbPress' )){
        return ;
      }
  add_action( 'bbp_template_after_replies_loop', 'quads_bbp_template_after_Ads' );
  add_action( 'bbp_template_before_replies_loop', 'quads_bbp_template_before_Ads' );
  add_action( 'bbp_theme_after_reply_content', 'quads_bbp_template_after_replies_loop' );
  add_action( 'bbp_theme_before_reply_content', 'quads_bbp_template_before_replies_loop' );
}
add_filter('wp_quads_content_html_last_filter','quads_content_modifier');
function quads_content_modifier( $content_buffer ){
    $data =    quads_load_ads_common('newspaper_theme',$content_buffer);
    if(empty($data)){
        return $content_buffer;
    }
    return $data;
}

function quads_bbp_template_after_Ads(){
  quads_load_ads_common('bbpress_after_ad');
}

function quads_bbp_template_before_Ads(){
  quads_load_ads_common('bbpress_before_ad');
}
function quads_bbp_template_after_replies_loop(){
  quads_load_ads_common('bbpress_after_reply');
}

function quads_bbp_template_before_replies_loop(){
  quads_load_ads_common('bbpress_before_reply');
}

function quads_api_services_cllbck()
{
       // Global $quads_ads variable to reduce db calls #631
        require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
        $api_service = new QUADS_Ad_Setup_Api_Service();
        $quads_ads = $api_service->getAdDataByParam('quads-ads');
        return $quads_ads;
}
function quads_load_ads_common($user_position,$html=''){
    $quads_ads = quads_api_services_cllbck();
    if(isset($quads_ads['posts_data'])){        
        foreach($quads_ads['posts_data'] as $key => $value){
          $ads =$value['post_meta'];
          if($value['post']['post_status']== 'draft'){
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
             if($is_on && $is_visitor_on && $post_status == 'publish'){
          if(($ads['position'] == 'bbpress_after_ad' && $user_position == 'bbpress_after_ad' )|| ($ads['position'] == 'bbpress_before_ad' && $user_position == 'bbpress_before_ad')){
              $tag= '<!--CusAds'.esc_html($ads['ad_id']).'-->';
              // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_replace_ads_new() function
              echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
          }else if(($ads['position'] == 'bbpress_before_reply' && $user_position == 'bbpress_before_reply' )|| ($ads['position'] == 'bbpress_after_reply' && $user_position == 'bbpress_after_reply')){
            if((did_action( 'bbp_theme_before_reply_content' ) % $ads['paragraph_number'] == 0  && $user_position == 'bbpress_before_reply' )|| (did_action( 'bbp_theme_after_reply_content' ) % $ads['paragraph_number'] == 0 && $user_position == 'bbpress_after_reply')){
                  $tag= '<!--CusAds'.esc_html($ads['ad_id']).'-->';
                  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_replace_ads_new() function
                  echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
            }
          }elseif( $ads['position'] == 'before_header' && $user_position == 'newspaper_theme'){
            $tag= '<!--CusAds'.esc_html($ads['ad_id']).'-->';
            $html = preg_replace('/<div\sclass=\"td-header-menu-wrap-full td-container-wrap(.*?)\">(.*?)<div class=\"td-main-content-wrap /s', '<div class="td-header-menu-wrap-full td-container-wrap$1"> '.quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] ).'$2<div class="td-main-content-wrap' , $html);
           
          }elseif( $ads['position'] == 'after_header' && $user_position == 'newspaper_theme'){
            $tag= '<!--CusAds'.esc_html($ads['ad_id']).'-->';
            $html = preg_replace('/<div\sclass=\"td-header-menu-wrap-full td-container-wrap(.*?)<div class=\"td-main-content-wrap/s', '<div class="td-header-menu-wrap-full td-container-wrap$1 '.quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] ).'<div class="td-main-content-wrap' , $html);
           
          }
        }  
       }
     }
     if($user_position == 'newspaper_theme'){
     return $html;
    }
}
function quads_remove_ads_for_wp_shortcodes() {
    $quads_settings = get_option( 'quads_settings' );
    if(isset($quads_settings['adsforwp_quads_shortcode']) && $quads_settings['adsforwp_quads_shortcode']){
        remove_shortcode( 'adsforwp' );
        add_shortcode('adsforwp', 'quads_from_adsforwp_manual_ads',1);
    }
    if(isset($quads_settings['advance_ads_to_quads']) && $quads_settings['advance_ads_to_quads']){
        remove_shortcode( 'the_ad_placement' );
        remove_shortcode( 'the_ad' );
        add_shortcode('the_ad_placement', 'quads_from_advance_manual_ads',1);
        add_shortcode( 'the_ad', 'quads_from_advance_manual_ads',1);
    
    }
  }
//Ad blocker
add_action('wp_head', 'quads_adblocker_detector');
add_action('wp_footer', 'quads_adblocker_popup_notice');
add_action('wp_footer', 'quads_adblocker_notice_jsondata');
add_action('wp_body_open', 'quads_adblocker_notice_bar');
add_action('wp_footer', 'quads_adblocker_ad_block');

function quads_from_advance_manual_ads($atts ){
    global $quads_options;
   
   // Display Condition is false and ignoreShortcodeCond is empty or not true
   if( !quads_ad_is_allowed() && !isset($quads_options['ignoreShortcodeCond']) )
       return;


   //return quads_check_meta_setting('NoAds');
   if( quads_check_meta_setting( 'NoAds' ) === '1' ){
       return;
   }
   $id = '';
   // The ad id
   // $advance_ads_id = isset( $atts['id'] ) ? ( int ) $atts['id'] : 0;
$atts = is_array( $atts ) ? $atts : array();
   $advance_ads_id   = isset( $atts['id'] ) ? (string) $atts['id'] : '';
     $advanced_ads_placements       = get_option('advads-ads-placements'); 
     $args = array(
       'post_type' => 'advanced_ads',
       'post_status' => 'publish'
     );
        $get_Advanced_Ads = get_posts($args);

       foreach ($get_Advanced_Ads  as $advanced_Ad) {
           
           $name = 'shortcode_'.$advanced_Ad->ID;
           $advanced_ads_placements[$name] = array('item' => 'ad_'.$advanced_Ad->ID,'advanced_ads'=>true);
       }
     foreach ($advanced_ads_placements as $key => $value) {
       $idArray =  (isset($value['item']) && !empty($value['item'])) ?  explode('ad_', $value['item']) : array('1'=>'');

       if($idArray['1'] == $advance_ads_id){

         $id = $idArray['1'];

       }
     }
     if(empty($id)){
        return '';
      }
      $args = array(
        'post_type'      => 'quads-ads',
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        'meta_key'       => 'advance_ads_id', 
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
        'meta_value'     => $id
      );
   
      $event_query = new WP_Query( $args );

   if(!isset($event_query->post->ID)){
     return '';
   }
   $quads_post_id =$event_query->post->ID;
  $id_name = get_post_meta ( $quads_post_id, 'quads_ad_old_id', true );
  $id_array = explode('ad', $id_name );
  $id = $id_array[1]; 
   $arr = array(
       'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
       'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
       'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
       'float:none;margin:%1$dpx;');
   
   $adsalign = isset($quads_options['ads']['ad' . $id]['align']) ? $quads_options['ads']['ad' . $id]['align'] : 3; // default
   $adsmargin = isset( $quads_options['ads']['ad' . $id]['margin'] ) ? $quads_options['ads']['ad' . $id]['margin'] : '3'; // default
   $margin = sprintf( $arr[( int ) $adsalign], $adsmargin );

 
   // Do not create any inline style on AMP site
   $style = !quads_is_amp_endpoint() ? apply_filters( 'quads_filter_margins', $margin, 'ad' . $id ) : '';

   $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  Shortcode Ad -->' . "\n" .
           '<div class="quads-location quads-ad' . $id . '" id="quads-ad' . $id . '" style="' . $style . '">' . "\n";
   $code .= do_shortcode( quads_get_ad( $id ) );
   $code .= '</div>' . "\n";

   return $code;
}

function quads_from_adsforwp_manual_ads($atts ){
     global $quads_options;
    
    // Display Condition is false and ignoreShortcodeCond is empty or not true
    if( !quads_ad_is_allowed() && !isset($quads_options['ignoreShortcodeCond']) )
        return;


    //return quads_check_meta_setting('NoAds');
    if( quads_check_meta_setting( 'NoAds' ) === '1' ){
        return;
    }
    
    // The ad id
    $adsforwpid = isset( $atts['id'] ) ? ( int ) $atts['id'] : 0;

    $args = array(
      'post_type'      => 'quads-ads',
      // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
      'meta_key'   => 'adsforwp_ads_id', 
      // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query,WordPress.DB.SlowDBQuery.slow_db_query_meta_value
      'meta_value' => $adsforwpid
    );

    $event_query = new WP_Query( $args );
    if(!isset($event_query->post->ID)){
      return '';
    }
    $quads_post_id =$event_query->post->ID;
   $id_name = get_post_meta ( $quads_post_id, 'quads_ad_old_id', true );
   $id_array = explode('ad', $id_name );
   $id = $id_array[1]; 
    $arr = array(
        'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
        'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
        'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
        'float:none;margin:%1$dpx;');
    
    $adsalign = isset($quads_options['ads']['ad' . $id]['align']) ? $quads_options['ads']['ad' . $id]['align'] : 3; // default
    $adsmargin = isset( $quads_options['ads']['ad' . $id]['margin'] ) ? $quads_options['ads']['ad' . $id]['margin'] : '3'; // default
    $margin = sprintf( $arr[( int ) $adsalign], $adsmargin );

    
    // Do not create any inline style on AMP site
    $style = !quads_is_amp_endpoint() ? apply_filters( 'quads_filter_margins', $margin, 'ad' . $id ) : '';
    if(function_exists('quads_hide_markup') && quads_hide_markup()  ) {
        $adscode =
            "\n".'<div style="'.esc_html($style).'">'."\n".
            do_shortcode(quads_get_ad($id)).
            '</div>'. "\n";
    }else {
        $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  Shortcode Ad -->' . "\n" .
            '<div class="quads-location quads-ad' . esc_html($id) . '" id="quads-ad' . esc_html($id) . '" style="' . esc_html($style) . '">' . "\n";
        $code .= do_shortcode(quads_get_ad($id));
        $code .= '</div>' . "\n";
    }

    return $code;
}
function quads_adblocker_detector(){
    if(!quads_is_amp_endpoint())
    {
        $js_dir  = QUADS_PLUGIN_URL . 'assets/js/';
        wp_enqueue_script( 'quads-ads', $js_dir . 'ads.js', array(), QUADS_VERSION, false );
    }

}
/**
 * It is default settings value, if value is not set for any option in setting section 
 * @return type
 */
function quads_defaultSettings(){
    
    $defaults = array(
    'app_blog_name'       => get_bloginfo( 'name' ),
    'advnc_ads_import_check'  => 1,
    'ad_blocker_support'      => 1,
    'notice_type'    => 'bar',
    'page_redirect'  => 0,
    'allow_cookies'    => 2,
    'notice_title'    => 'Adblock Detected!',
    'notice_description'    => 'Our website is made possible by displaying online advertisements to our visitors. Please consider supporting us by whitelisting our website.',
    'notice_close_btn' => 1,
    'btn_txt' => 'X',
    'notice_txt_color' => '#ffffff',
    'notice_bg_color' => '#1e73be',
    'notice_btn_txt_color' => '#ffffff',
    'notice_btn_bg_color' => '#f44336',
    'ad_sponsorship_label' => 0,
    'ad_sponsorship_label_text' => 'Advertisement',
    'ad_label_postion' => 'above',
    'ad_label_txt_color' => '#cccccc'
    );  
        
    $settings = get_option( 'quads_settings', $defaults );
        
    return $settings;
}
function quads_adblocker_popup_notice(){
  
  $settings = quads_defaultSettings();

  if( isset($settings['ad_blocker_support']) && $settings['ad_blocker_support']){

    if($settings['notice_type'] == 'popup'){
        

      $content_color = $settings['notice_txt_color'];
      $notice_title = $settings['notice_title'];
      $notice_description = $settings['notice_description'];
      $button_txt = $settings['btn_txt'];
      $background_color = $settings['notice_bg_color'];
      $btn_txt_color = $settings['notice_btn_txt_color'];
      $btn_background_color = $settings['notice_btn_bg_color'];
      
  ?>
    <div id="quads-myModal_" class="quads-modal" style="display:none">
      <!-- Modal content -->
      <div class="quads-modal-content">
    <?php if( isset($settings['notice_close_btn']) && $settings['notice_close_btn'] && empty($button_txt) ){
          ?>
          <span class="quads-close quads-cls-notice">&times;</span>  
          <?php
        }
        ?>
        <h2 style="text-align: center;padding-top:0;color: <?php echo sanitize_hex_color( $content_color );?>;"><?php echo esc_attr( $notice_title );?></h2>
        <p style="margin:0 0 1.5em;padding: 0;text-align: center;color: <?php echo sanitize_hex_color( $content_color );?>;"><?php echo esc_attr( $notice_description );?></p>
        <?php if( isset($settings['notice_close_btn']) && $settings['notice_close_btn'] &&  !empty($button_txt) ){
          ?>
          <button class="quads-button quads-closebtn quads-cls-notice"><?php echo esc_attr( $button_txt );?></button>
        <?php
        }
        ?>
      </div>
    </div>
    <style>
    .quads-modal {
      display: block; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 999; /* Sit on top */
      padding-top: 200px; /* Location of the box */
      left: 0;
      right:0;
      top: 50%; 
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgb(0,0,0); /* Fallback color */
      background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
      -webkit-transform:translateY(-50%);
       -moz-transform:translateY(-50%);
       -ms-transform:translateY(-50%);
       -o-transform:translateY(-50%);
       transform:translateY(-50%);
    }

    /* Modal Content */
    .quads-modal-content {
      background-color: <?php echo sanitize_hex_color( $background_color );?>;
      margin: auto;
      padding: 20px;
      border: 1px solid #888;
      width: 40%;
      border-radius: 10px;
      text-align: center;
    }

    /* The Close Button */
    .quads-close{
      color: <?php echo sanitize_hex_color( $btn_txt_color );?>;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .quads-close:hover,
    .quads-close:focus {
      color: #000;
      text-decoration: none;
      cursor: pointer;
    }
    .quads-button {
      background-color: <?php echo sanitize_hex_color( $btn_background_color );?>; /* Green */
      border: none;
      color: <?php echo sanitize_hex_color( $btn_txt_color );?>;
      padding: 10px 15px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin: 4px 2px;
      cursor: pointer;
    }
    @media screen and (max-width: 1024px) {
      .quads-modal-content {
        width: 80%;
        font-size: 14px;
      }
      .quads-modal {
        padding-top: 100px;
      }
    }
  </style>
  <?php
    }
  }
}
function quads_adblocker_notice_jsondata(){
    if(!quads_is_amp_endpoint())
    {
    $settings = quads_defaultSettings();
    $output = '';
    $quads_mode = get_option('quads-mode');
    if( isset($settings['ad_blocker_support']) && $settings['ad_blocker_support'] && !empty($settings['notice_type']) || ($quads_mode && $quads_mode == 'old' && isset($settings['ad_blocker_message'])  && $settings['ad_blocker_message'])){
      $output    .= '<script>';
      $output    .= '/* <![CDATA[ */';
      $output    .= 'var quadsOptions =' .
        json_encode(
          array(
            'quadsChoice'          => esc_attr($settings['notice_type']),
            'page_redirect'          => (isset($settings['page_redirect_path']['value']) && !empty($settings['page_redirect_path']['value'])) ? get_permalink($settings['page_redirect_path']['value'] ):'',
            'allow_cookies'         => esc_attr($settings['notice_behaviour'])
          )
        );
      $output    .= '/* ]]> */';
      $output    .= '</script>';
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
      echo $output;
    }
    }
}
function quads_adblocker_notice_bar(){
    $settings = quads_defaultSettings();
    
  if( isset($settings['ad_blocker_support']) && $settings['ad_blocker_support']){
    if($settings['notice_type'] == 'bar' ){
      
      $notice_description = $settings['notice_description'];
      $button_txt = $settings['btn_txt'];
      $content_color = $settings['notice_txt_color'];
      $background_color = $settings['notice_bg_color'];
      $btn_txt_color = $settings['notice_btn_txt_color'];
      $btn_background_color = $settings['notice_btn_bg_color'];
      $notice_bar = !empty($settings['notice_bar']) ? $settings['notice_bar'] : '';
      $notice_bar_sticky = !empty($settings['notice_bar_sticky']) ? $settings['notice_bar_sticky'] : '';

  ?>
  
  <div id="quads-myModal" class="quads-adblocker-notice-bar">
    <div class="enb-textcenter">
      <?php if( isset($settings['notice_close_btn'])&& $settings['notice_close_btn'] && empty($button_txt)){?>
      <span class="quads-close quads-cls-notice">&times;</span>  
    <?php } ?>
      <div class="quads-adblocker-message">
      <?php echo esc_attr( $notice_description );?>
      </div>
      <?php if( isset($settings['notice_close_btn'])&& $settings['notice_close_btn'] && !empty($button_txt)){?>
      <button class="quads-button quads-closebtn quads-cls-notice"><?php echo esc_attr( $button_txt );?></button>  
    <?php } ?>
    </div>
    <input type="hidden" id="quads_notice_bar" value="<?php echo esc_attr( $notice_bar );?>">
    <input type="hidden" id="quads_notice_bar_sticky" value="<?php echo esc_attr( $notice_bar_sticky );?>">
  </div>
  <style>
    .quads-adblocker-message{
      display: inline-block;
    }
    .quads-adblocker-notice-bar {
      display: none;
      width: 100%;
      background: <?php echo sanitize_hex_color( $background_color );?>;
      color: <?php echo sanitize_hex_color( $content_color );?>;
      padding: 0.5em 0px;
      font-size: 16px;
      line-height: 1.8;
      position: fixed;
      z-index: 999999;
    }
    .quads-adblocker-notice-bar strong {
      color: inherit; /* some themes change strong tag to make it darker */
    }
    /* Alignments */
    .quads-adblocker-notice-bar .enb-textcenter {
      text-align: center;
    }
    .quads-close{
      color: <?php echo sanitize_hex_color( $btn_txt_color );?>;
      float: right;
      font-size: 20px;
      font-weight: bold;
    }
    .quads-close:hover,
    .quads-close:focus {
      color: #000;
      text-decoration: none;
      cursor: pointer;
    }
    .quads-button {
      background-color: <?php echo sanitize_hex_color( $btn_background_color );?>; /* Green */
      border: none;
      color: <?php echo sanitize_hex_color( $btn_txt_color );?>;
      padding: 5px 10px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 14px;
      margin-right: 5px;
      cursor: pointer;
      float: right;
    }
    @media screen and (max-width: 1024px) {
      .quads-modal-content {
        font-size: 14px;
      }
      .quads-button{
        padding:5px 10px;
        font-size: 14px;
        float:none;
      }
    }
  </style>
  <?php 
    }
  }
}
function quads_adblocker_ad_block(){
    if(!quads_is_amp_endpoint())
    {
    $settings = quads_defaultSettings();
    $quads_mode = get_option('quads-mode');
    if( isset($settings['ad_blocker_support']) && $settings['ad_blocker_support'] && !empty($settings['notice_type']) || ($quads_mode && $quads_mode == 'old' && isset($settings['ad_blocker_message'])  && $settings['ad_blocker_message'])){

        ?>
<script>

   if(typeof quadsOptions !== 'undefined' && typeof quads_adblocker_check_2 
  === 'undefined' && quadsOptions.quadsChoice == 'ad_blocker_message'){
  var addEvent1 = function (obj, type, fn) {
      if (obj.addEventListener)
          obj.addEventListener(type, fn, false);
      else if (obj.attachEvent)
          obj.attachEvent('on' + type, function () {
              return fn.call(obj, window.event);
          });
  };
   addEvent1(window, 'load', function () {
      if (typeof quads_adblocker_check_2 === "undefined" || quads_adblocker_check_2 === false) {

          quads_highlight_adblocked_ads();
      }
  });

   function quads_highlight_adblocked_ads() {
      try {
          var ad_wrappers = document.querySelectorAll('div[id^="quads-ad"]')
      } catch (e) {
          return;
      }

      for (i = 0; i < ad_wrappers.length; i++) {
          ad_wrappers[i].className += ' quads-highlight-adblocked';
          ad_wrappers[i].className = ad_wrappers[i].className.replace('quads-location', '');
          ad_wrappers[i].setAttribute('style', 'display:block !important');
      }
  }
 }

(function() {
//Adblocker Notice Script Starts Here
var curr_url = window.location.href;
var red_ulr = localStorage.getItem('curr');
var modal = document.getElementById("quads-myModal");
var quadsClosebtn = document.getElementsByClassName("quads-closebtn")[0];
var quadsModalContent = document.getElementsByClassName("quads-modal-content")[0];
var quads_model_  = document.getElementById("quads-myModal_");
var quadsAllowedCookie =  quadsgetCookie('quadsAllowedCookie');
var quadsNotice_bar = document.getElementById("quads_notice_bar");
var quadsNotice_bar_sticky = document.getElementById("quads_notice_bar_sticky");

if (quadsClosebtn) {
    quadsClosebtn.addEventListener("click", function(){
        if( quadsClosebtn ){
            quads_model_.style.display = "none";
        }
    })
} 

window.onclick = function(event) {
  if (event.target == quads_model_) {
    quads_model_.style.display = "none";
    document.cookie = "quads_prompt_close="+new Date();
    quadssetCookie('quadsCookie', 'true', 1, '/');
  }
}

if(quadsOptions.quadsChoice == 'bar'){
    if(quadsNotice_bar.value == 2){
        modal.style.top = "0";
    } else {
        modal.style.bottom = "0";
    }
    var prevScrollpos = window.pageYOffset;
    window.onscroll = function() {
        var currentScrollPos = window.pageYOffset;
        if(prevScrollpos > currentScrollPos){
            if(quadsNotice_bar.value == 2){
                modal.style.top = "0px";
            }
            if(quadsNotice_bar.value == 1){
                modal.style.bottom = "0px";
            }
        } else{
            if(quadsNotice_bar_sticky.value != 1 && quadsNotice_bar.value == 2){
                modal.style.top = "-90px";
            }
            if(quadsNotice_bar_sticky.value != 1 && quadsNotice_bar.value == 1){
                modal.style.bottom = "-90px";
            }
        }
        prevScrollpos = currentScrollPos;
    }
}

if(typeof quadsOptions !== 'undefined'){
    var quads_model_  = document.getElementById("quads-myModal_");
    if(quads_model_){ quads_model_.style.display = "block"; }

  if(quadsAllowedCookie!=quadsOptions.allow_cookies){
    quadssetCookie('quadsCookie', '', -1, '/');
    quadssetCookie('quadsAllowedCookie', quadsOptions.allow_cookies, 1, '/');
  }

  if(quadsOptions.allow_cookies == 2){
    if( quadsOptions.quadsChoice == 'bar' || quadsOptions.quadsChoice == 'popup'){
        modal.style.display = "block";
        quadssetCookie('quadsCookie', '', -1, '/');
    }
    
    if(quadsOptions.quadsChoice == 'page_redirect' && quadsOptions.page_redirect !="undefined"){
        if(red_ulr==null || curr_url!=quadsOptions.page_redirect){
        window.location = quadsOptions.page_redirect;
        localStorage.setItem('curr',quadsOptions.page_redirect);
      }
    }
  }else{
    var adsCookie = quadsgetCookie('quadsCookie');
    if(adsCookie==false) {
      if( quadsOptions.quadsChoice == 'bar' || quadsOptions.quadsChoice == 'popup'){
          modal.style.display = "block";
      }
      if(quadsOptions.quadsChoice == 'page_redirect' && quadsOptions.page_redirect !="undefined"){
        window.location = quadsOptions.page_redirect;
        quadssetCookie('quadsCookie', true, 1, '/');
      }
    }else{
      modal.style.display = "none";
    }
  }

}

var span = document.getElementsByClassName("quads-cls-notice")[0];
if(span){
  span.onclick = function() {
    modal.style.display = "none";
    document.cookie = "quads_prompt_close="+new Date();
    quadssetCookie('quadsCookie', 'true', 1, '/');
  }
}

})();
function quadsgetCookie(cname){
    var name = cname + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return false;
}
function quadssetCookie(cname, cvalue, exdays, path){
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
//Adblocker Notice Script Ends Here
</script>

<?php
    }
}
}

function quads_classic_to_gutenberg($data)
{
    if (isset($data->data['content']['raw'])) {
        $data->data['content']['raw'] =  preg_replace('/<!--Ads(\d+)-->/','[quads id=$1]', $data->data['content']['raw']);  
        $data->data['content']['raw'] =  str_replace('<!--RndAds-->', '[quads id=RndAds]', $data->data['content']['raw']);
    }
    return $data;
}
function quads_change_adsbygoogle_to_amp($content){
    if (quads_is_amp_endpoint()){
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            if( function_exists( 'mb_convert_encoding' ) ){
                $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');     
            }else{
                $content =  preg_replace( '/&.*?;/', 'x', $content ); // multi-byte characters converted to X
            }
        }else{
            $content = htmlentities($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        if(empty($content)){
            return $content;
          }
        libxml_use_internal_errors(true);
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $nodes = $dom->getElementsByTagName( 'ins' );

        $num_nodes  = $nodes->length;
        for ( $i = $num_nodes - 1; $i >= 0; $i-- ) {
            $url = $width = $height = '';
            $node   = $nodes->item( $i );
            if($node->getAttribute('class') == 'adsbygoogle'){
                $adclient= $node->getAttribute('data-ad-client');
                $adslot= $node->getAttribute('data-ad-slot');
                $adformat= $node->getAttribute('data-ad-format');
                $adfullwidth= $node->getAttribute('data-full-width-responsive');
    
                $new_node= $dom->createElement('amp-ad');
                $new_node->setAttribute('type', 'adsense');
                $new_node->setAttribute('data-ad-client', $adclient);
                $new_node->setAttribute('data-ad-slot', $adslot);
                if($node->getAttribute('data-full-width-responsive')){
                            $new_node->setAttribute('data-ad-format', $adformat);
                            $new_node->setAttribute('data-full-width-responsive', $adfullwidth);
                }
                $styletag= $node->getAttribute('style');
                $widthreg = "/width:(?<width>\\d+)/";
                $heightreg = "/height:(?<height>\\d+)/";
                preg_match($widthreg, $styletag, $width);
                preg_match($heightreg, $styletag, $height);
                if(isset($width['width'])){
                    $new_node->setAttribute('width', $width['width']);
                }else{
                    $new_node->setAttribute('width', '100vw');
                }
                if(isset($height['height'])){
                    $new_node->setAttribute('height', $height['height']);
                }else{
                    $new_node->setAttribute('height', '320');
                }
                $child_element= $dom->createElement('div');
                $child_element->setAttribute('overflow', '');
                $new_node->appendChild( $child_element );
    
                $node->parentNode->replaceChild($new_node, $node);
            }
        }
        $content = $dom->saveHTML();
    }
    return $content;
}

/**
 * Adds quicktags, defined via post meta options, to content.
 *
 * @param $content Post content
 *
 * @return string
 */
function quads_post_settings_to_quicktags ( $content ) {
    
        // Return original content if QUADS is not allowed
        if ( !quads_ad_is_allowed($content)){
            return $content;
        }
    
    $quicktags_str = quads_get_visibility_quicktags_str();

        return $content . $quicktags_str;
}
/**
 * Returns quicktags based on post meta options.
 * These quicktags define which ads should be hidden on current page.
 *
 * @param null $post_id Post id
 *
 * @return string
 */
function quads_get_visibility_quicktags_str( $post_id = null ) {

   if( !$post_id ) {
      $post_id = get_the_ID();
   }

   $str = '';
   if( false === $post_id ) {
      return $str;
   }

   $config = get_post_meta( $post_id, '_quads_config_visibility', true );

   if( !empty( $config ) && is_array($config) ) {
      foreach ( $config as $qtag_id => $qtag_label ) {
         $str .= '<!--' . $qtag_id . '-->';
      }
   }

   return $str;
}

/**
 * Get load priority
 * 
 * @global arr $quads_options
 * @return int
 */
function quads_get_load_priority(){
    global $quads_options;
    
    if (!empty($quads_options['priority'])){
        return intval($quads_options['priority']);
    }
    $priority = function_exists('Sensei') ? 10 : 20;
    return $priority;
}

/**
 *
 * @global arr $quads_options
 * @global type $quads_adsArray
 * @param type $content
 * @return type
 */
function quads_disable_ads(){
    if( ! is_user_logged_in() ){
        return false;
    }
    $current_user = wp_get_current_user();
    $email = sanitize_email( $current_user->user_email );
    $user_ID = absint( $current_user->ID );
   
    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_disabledad_data'; 
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
    $ad_details = wp_cache_get('quads_ad_details_'.$email.'_'.$user_ID, 'quick-adsense-reloaded');
    if(false === $ad_details){
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $ad_details = $wpdb->get_row($wpdb->prepare( "SELECT * FROM `{$table_name}` WHERE user_email = %s AND payment_status = %s and user_id=%d order by disable_ad_id desc limit 1", $email, 'paid', $user_ID ));
        wp_cache_set('quads_ad_details_'.$email.'_'.$user_ID, $ad_details, 'quick-adsense-reloaded', 3600);
    }
    $is_disable_ad = false;
    if(!empty($ad_details)){
        $disable_duration = $ad_details->disable_duration;
        if( $ad_details->payment_response !="" ){
            $payment_response = json_decode( $ad_details->payment_response, true );
            if( isset( $payment_response['payment_date'] ) ){
                $payment_date = $payment_response['payment_date'];
                $futureDate= gmdate('Y-m-d');
                $currentDate= gmdate('Y-m-d');
                if( $disable_duration=='yearly' ){
                    $futureDate=gmdate('Y-m-d', strtotime('+1 year', strtotime($payment_date)) );
                }else if( $disable_duration=='monthly' ){
                    $futureDate=gmdate('Y-m-d', strtotime('+1 month', strtotime($payment_date)) );
                }
                if($currentDate<=$futureDate){
                    $is_disable_ad = true;
                }
            }
        }
    }
    return $is_disable_ad;
}
function quads_process_content( $content ) {
    global $quads_mode, $quads_options, $quads_adsArray, $quads_adsArrayCus, $quads_visibleContentAds, $quads_ad_count_widget, $quads_visibleShortcodeAds;        
    
    // Array of ad codes ids
    $quads_adsArray = quads_get_active_ads();
    
    // Return is no ads are defined
    if ($quads_adsArray === 0 && $quads_mode != 'new'){
        return $content;
    }

    // Do nothing if maximum ads are reached in post content
    if( $quads_visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
        $content = quads_clean_tags( $content );
        return $content;
    }
    if(quads_disable_ads()){
        return $content;
    }
    $quads_ad_is_allowed=apply_filters('quads_show_ads',quads_ad_is_allowed( $content ));
    // Do not do anything if ads are not allowed or process is not in the main query
    if( !$quads_ad_is_allowed || !is_main_query() || !is_singular() ) {
        $content = quads_clean_tags( $content );
        return $content;
    }

    $content = quads_sanitize_content( $content );
    if($quads_mode == 'new'){
        $content = quads_filter_default_ads_new( $content );
        $content = '<!--EmptyClear-->' . $content . "\n";
        $content = quads_clean_tags( $content, true );
        $content = quads_parse_default_ads_new( $content );
        $content = quads_parse_quicktags( $content );
        $content = quads_parse_random_quicktag_ads($content);
        $content = quads_parse_random_ads_new( $content );
        $content = quads_clean_tags( $content );
        $content = quads_parse_popup_ads( $content );
        $content = quads_parse_video_ads( $content );       
        $content = quads_parse_parallax_ads( $content ); 
        $content = quads_parse_half_page_ads( $content );        
        return do_shortcode( $content );   
    }else{
        $content = quads_filter_default_ads( $content );    
        $content = '<!--EmptyClear-->' . $content . "\n";
        $content = quads_clean_tags( $content, true );
        $content = quads_parse_default_ads( $content );    
        $content = quads_parse_quicktags( $content );
        $content = quads_parse_random_quicktag_ads($content);
        $content = quads_parse_random_ads( $content );    
        $content = quads_clean_tags( $content );
        return do_shortcode( $content );
    }    
}

/**
 * Return number of active widget ads
 * @param string the_content
 * @return int amount of widget ads
 */
function quads_get_number_widget_ads() {
    $number_widgets = 0;
    $quads_maxWidgets = 10;
    // count active widget ads
        for ( $i = 1; $i <= $quads_maxWidgets; $i++ ) {
            $AdsWidName = 'AdsWidget%d (Quick Adsense Reloaded)';
            $wadsid = sanitize_title( str_replace( array('(', ')'), '', sprintf( $AdsWidName, $i ) ) );
            $number_widgets += (is_active_widget( '', '', $wadsid )) ? 1 : 0;
        }
    
    return $number_widgets;
}

/**
 * Get list of valid ad ids's where either the plain text code field or the adsense ad slot and the ad client id is populated.
 * @global arr $quads_options
 */
function quads_get_active_ads() {
    global $quads_options;

    
    // Return early
    if (empty($quads_options['ads'])){
       return 0;
    }
   
    // count valid ads
    $i = 1;
    foreach ( $quads_options['ads'] as $ads) {
        $tmp = isset( $quads_options['ads']['ad' . $i]['code'] ) ? trim( $quads_options['ads']['ad' . $i]['code'] ) : '';
        // id is valid if there is either the plain text field populated or the adsense ad slot and the ad client id
        if( !empty( $tmp ) || (!empty( $quads_options['ads']['ad' . $i]['g_data_ad_slot'] ) && !empty( $quads_options['ads']['ad' . $i]['g_data_ad_client'] ) ) ) {
            $quads_adsArray[] = $i;
        }
        $i++;
    }
    return (isset($quads_adsArray) && count($quads_adsArray) > 0) ? $quads_adsArray : 0;
}

function quads_get_active_ads_ids() {
    global $quads_options;
    if (empty($quads_options['ads'])){
       return 0;
    }
    foreach ( $quads_options['ads'] as $key =>$single_ad_options) {
		if((!empty( $quads_options['ads'][$key]['ad_id'] ) && !empty( $quads_options['ads'][$key]['ad_id'] ) ) ) {
			$adsArrayActive[] = $quads_options['ads'][$key]['ad_id'];
        }
    }
    return (isset($adsArrayActive) && count($adsArrayActive) > 0) ? $adsArrayActive : 0;
}

/**
 * Get list of valid ad ids's where either the plain text code field or the adsense ad slot and the ad client id is populated.
 * @global arr $quads_options
 */
function quads_get_active_ads_backup() {

    $quads_settings_backup = get_option( 'quads_settings_backup' );

    
    // Return early
    if (empty($quads_settings_backup['ads'])){
       return 0;
    }
   
    // count valid ads
    $i = 1;
    foreach ( $quads_settings_backup['ads'] as $ads) {
        $tmp = isset( $quads_settings_backup['ads']['ad' . $i]['code'] ) ? trim( $quads_settings_backup['ads']['ad' . $i]['code'] ) : '';
        // id is valid if there is either the plain text field populated or the adsense ad slot and the ad client id
        if( !empty( $tmp ) || (!empty( $quads_settings_backup['ads']['ad' . $i]['g_data_ad_slot'] ) && !empty( $quads_settings_backup['ads']['ad' . $i]['g_data_ad_client'] ) ) ) {
            $quads_adsArray[] = $i;
        }
        $i++;
    }
    return (isset($quads_adsArray) && count($quads_adsArray) > 0) ? $quads_adsArray : 0;
}

/**
 * Get max allowed numbers of ads
 * 
 * @param string $content
 * @return int maximum number of ads
 */
function quads_get_max_allowed_post_ads( $content ) {
    global $quads_options;

    // Maximum allowed general number of ads 
    $maxAds = isset( $quads_options['maxads'] ) ? $quads_options['maxads'] : 10;
    
    $numberWidgets = 10;
    
    $AdsWidName = 'AdsWidget%d (Quick Adsense Reloaded)';
    
    // count number of active widgets and subtract them 
    if( strpos( $content, '<!--OffWidget-->' ) === false &&  !quads_is_amp_endpoint() ) {
        for ( $i = 1; $i <= $numberWidgets; $i++ ) {
            $wadsid = sanitize_title( str_replace( array('(', ')'), '', sprintf( $AdsWidName, $i ) ) );
            $maxAds -= (is_active_widget( '', '', $wadsid )) ? 1 : 0;
        }
    }

    return $maxAds;
}


/**
 * Filter default ads
 * 
 * @global array $quads_options global settings
 * @global array $quads_adsArrayCus List of ad id'S
 * @param string $content
 * @return string content
 */
function quads_check_parent_by_dynamic_block($targetFragment, $content, $selectorsString) {
    // Extract paragraph
    if (preg_match('/<p[^>]*>(.*?)<\/p>/i', $targetFragment, $paragraphMatch)) {
        $targetParagraph = $paragraphMatch[0];
    } else {
        $targetParagraph = $targetFragment;
    }
    
    
    // Load content into DOM
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $paragraphs = $doc->getElementsByTagName('p');
    
    $selectors = explode(" ", $selectorsString);
    
    
    // Find matching paragraph
    foreach ($paragraphs as $p) {
        $pHtml = $doc->saveHTML($p);
        $normalizedP = preg_replace('/\s+/', ' ', trim($pHtml));
        $normalizedTarget = preg_replace('/\s+/', ' ', trim($targetParagraph));
        
        
        if ($normalizedP === $normalizedTarget) {
            $parent = $p->parentNode;
            $parentClasses = $parent->getAttribute('class');
            $parentId = $parent->getAttribute('id');
            
            
            foreach ($selectors as $selector) {
                if (strpos($selector, '#') === 0) {
                    if ($parentId === ltrim($selector, '#')) {
                        return true;
                    }
                } else {
                    $selector = ltrim($selector, '.');
                    if (strpos($parentClasses, $selector) !== false) {
                        
                        return true;
                    }
                }
            }
            
            return false;
        }
    }
    
    return false;
}
function quads_filter_default_ads_new( $content ) {
    
 
    global $quads_options, $quads_adsArrayCus;   
    
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);

    if( $off_default_ads ) { // If default ads are disabled 
        return $content;
    }   


    $quads_ads = quads_api_services_cllbck(); 


    // Default Ads
    $quads_adsArrayCus = array();
    if(isset($quads_ads['posts_data'])){        
        $i = 1;
        foreach($quads_ads['posts_data'] as $key => $value){
            $ads =$value['post_meta'];
            if($value['post']['post_status']== 'draft'){
                continue;
            }
            $quads_visibilty = apply_filters('quads_ad_conditional_visibility', $value['post_meta']);
            if(!$quads_visibilty){
                continue;
            }
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
            $is_click_fraud_on = quads_click_fraud_on();
            if(isset($ads['ad_id']))
            $post_status = get_post_status($ads['ad_id']); 
            else
              $post_status =  'publish';
            if($is_on && $is_visitor_on && $is_click_fraud_on && $post_status=='publish'){
                $ads  = apply_filters( 'quads_default_filter_position_data', $ads);
                $ads  = apply_filters( 'quads_default_filter_position_data_ab_testing', $ads);

                $position     = (isset($ads['position']) && $ads['position'] !='') ? $ads['position'] : '';
                $cls_btn     = (isset($ads['cls_btn']) && $ads['cls_btn'] !='') ? $ads['cls_btn'] : '';
                $sticky_show_hide     = (isset($ads['sticky_ad_show_hide']) && $ads['sticky_ad_show_hide'] !='') ? $ads['sticky_ad_show_hide'] : '';
                $sticky_show_hide_txt     = (isset($ads['sticky_show_hide_txt']) && $ads['sticky_show_hide_txt'] !='') ? $ads['sticky_show_hide_txt'] : 'Slide Up';
                $sticky_ad_anim = (isset($ads['sticky_ad_anim']) && $ads['sticky_ad_anim'] !='') ? $ads['sticky_ad_anim'] : '';
                $sticky_ad_anim_text = (isset($ads['sticky_ad_anim_txt']) && $ads['sticky_ad_anim_txt'] !='') ? $ads['sticky_ad_anim_txt'] : '1000';
                $paragraph_no = (isset($ads['paragraph_number']) && $ads['paragraph_number'] !='') ? $ads['paragraph_number'] : 1;
                $word_count_number = (isset($ads['word_count_number']) && $ads['word_count_number'] !='') ? $ads['word_count_number'] : 1;
                $imageNo      = (isset($ads['image_number']) && $ads['image_number'] !='') ? $ads['image_number'] : 1;
                $imageCaption = isset($ads['image_caption']) ? $ads['image_caption'] : false;
                $end_of_post  = isset($ads['enable_on_end_of_post']) ? $ads['enable_on_end_of_post'] : false;
 
                // placeholder string for custom ad spots
                if(isset($ads['random_ads_list']) && !empty($ads['random_ads_list'])){
                    $cusads = '<!--CusRnd'.esc_html($ads['ad_id']).'-->';
                }else if($ads['ad_type']== 'rotator_ads' &&isset($ads['ads_list']) && !empty($ads['ads_list'])){
                    $cusads = '<!--CusRot'.esc_html($ads['ad_id']).'-->';
                }else if($ads['ad_type']== 'popup_ads' &&isset($ads['ads_list']) && !empty($ads['ads_list'])){
                    $cusads = '<!--pop_up_ads'.esc_html($ads['ad_id']).'-->';
                }
                else if($ads['ad_type']== 'video_ads'){
                    $cusads = '<!--video_ad'.esc_html($ads['ad_id']).'-->';
                }
                else if($ads['ad_type']== 'parallax_ads'){
                    $cusads = '<!--parallax_ad'.esc_html($ads['ad_id']).'-->';
                }
                else if($ads['ad_type']== 'half_page_ads'){
                    $cusads = '<!--half_page_ad'.esc_html($ads['ad_id']).'-->';
                }
                else if($ads['ad_type']== 'floating_cubes'){
                    $cusads = '<!--floating_cubes_ad'.esc_html($ads['ad_id']).'-->';
                }
                else{                   
                    $cusads = '<!--CusAds'.esc_html($ads['ad_id']).'-->';
                }
               
                switch ($position) {
                    case 'beginning_of_post':                          
                        if(strpos( $content, '<!--OffBegin-->' ) === false ) {
                           $content = $cusads.$content;   
                        }                    
                    break;

                    case 'middle_of_post':
                        // Check if ad is middle one
                        if(strpos( $content, '<!--OffMiddle-->' ) === false ) {
                            $closing_p        = '</p>';
                            $paragraphs       = explode( $closing_p, $content );       
                            $total_paragraphs = count($paragraphs);                          
                            $paragraph_id     = floor($total_paragraphs /2);  
                            if( strpos($content, "</blockquote>") || strpos($content, "</table>")){
                                $ads_data['after_the_percentage_value'] = 50;
                               $content =  quads_remove_ad_from_content($content,$cusads,$ads_data);

                              }else{                            
                                    foreach ($paragraphs as $index => $paragraph) {
                                        if ( trim( $paragraph ) ) {
                                            $paragraphs[$index] .= $closing_p;
                                        }
                                        if ( $paragraph_id == $index + 1 ) {
                                            $paragraphs[$index] .= $cusads;
                                        }
                                    }
                                    $content = implode('', $paragraphs ); 
                                }
                        }

                    break;                            
                    case 'end_of_post':           
                        if(strpos( $content, '<!--OffEnd-->' ) === false ) {
                           $content = $content.$cusads;   
                        }                     
                        # code...
                    break;
/* 
                    case 'ad_sticky_ad':
                        $sticky_cookie =   (isset( $_COOKIE['quads_sticky'] ) && $_COOKIE['quads_sticky']!== NULL ) ? $_COOKIE['quads_sticky'] : '' ;
                        if( $sticky_cookie !== "sticky_ad" ){
                            if(strpos( $content, '<!--OffEnd-->' ) === false ) {
                                $a_tag = $btn_tag = '';
                                if( isset($cls_btn) && $cls_btn == 1 ){
                                    $a_tag = '<a class="quads-sticky-ad-close">x</a>';
                                }
                                if( isset($sticky_show_hide) && $sticky_show_hide == 1 ){
                                    $btn_tag = '<div class="quads-sticky-show-btn">'.esc_attr($sticky_show_hide_txt).'</div>';
                                }
                                $anim_attr = ( isset($sticky_ad_anim) && $sticky_ad_anim == 1 ) ? ' data-anim-tod='.$sticky_ad_anim_text.'' : '';
                                $q_main_open = ''.$btn_tag.'<div class="quads-sticky"'.$anim_attr.'>'.$a_tag.'';
                                $q_close = '</div>';
                                $content = $content.$q_main_open.$cusads.$q_close;}                                 
                            }
                    break;    */                             
                    case 'after_more_tag':
                        // Check if ad is after "More Tag"
                        if(strpos( $content, '<!--OffAfMore-->' ) === false ) {                           
                            $postid  = get_the_ID();
                            $content = str_replace( '<span id="more-' . $postid . '"></span>', $cusads, $content );
                        }
                    break;
                    case 'before_last_paragraph':

                        if(strpos( $content, '<!--OffBfLastPara-->' ) === false ) {
                            $closing_p        = '</p>';
                            $paragraphs       = explode( $closing_p, $content );
                            $p_count          = count($paragraphs);                                                             
                            $paragraph_no     = ($p_count - 2);
                            if($paragraph_no <= $p_count){

                                foreach ($paragraphs as $index => $paragraph) {
                                    if ( trim( $paragraph ) ) {
                                        $paragraphs[$index] .= $closing_p;
                                    }
                                    if ( $paragraph_no == $index + 1 ) {
                                        $paragraphs[$index] .= $cusads;
                                    }
                                }
                                $content = implode( '', $paragraphs ); 
                            }                                                        
                        }                                                

                    break;
                    case 'after_word_count':
                        
                        if(strpos( $content, '<!--OffBfLastPara-->' ) === false ) {
                           

                            $paragraphs       =  explode( ' ', $content );
                            $p_count          = count($paragraphs);
                            $original_paragraph_no = $paragraph_no;  
                             ;   
                           
                            $flag= false;                                   
                            if($word_count_number <= $p_count){
                                if( strpos($content, "</blockquote>") || strpos($content, "</table>")){
                                    $content =  quads_remove_ad_from_content($content,$cusads,'',$paragraph_no);
                                  }else{

                                foreach ($paragraphs as $index => $paragraph) {
        
                                    if ( $word_count_number == $index + 1 ) {
                                        $flag= true; 
                                    }
                                     if($flag && preg_match("/<[^<]+>/",$paragraphs[$index])){
                                        $pattern = "#<\s*?li\b[^>]*>(.*?)#s"; //  to find the tag name
                                        preg_match($pattern, $paragraphs[$index], $matches);
                                        if(empty($matches)){
                                            preg_match("#<\s*?(p|span|div|a)\b[^>]*(.*?)#s", $paragraphs[$index], $matches);
                                        }
                                     if(isset($matches[0])){
                                        $tagname =$matches[0];
                                        $stringarray= explode($tagname,$paragraphs[$index]);
                                        if(isset($stringarray[0])){
                                            $stringarray[0]=$stringarray[0].$cusads;
                                            $paragraphs[$index] =   implode($tagname,$stringarray);
                                        }
                                     }else{
                                        $paragraphs[$index] .= $cusads;
                                     }
                                        $flag= false;  
                                    }
                                }
                                $content = implode( ' ', $paragraphs ); 
                            }  
                            
                        }
                        }

                    break;
                    case 'after_paragraph':

                        if(strpos( $content, '<!--OffBfLastPara-->' ) === false ) {
                          $repeat_paragraph = (isset($ads['repeat_paragraph']) && !empty($ads['repeat_paragraph'])) ? $ads['repeat_paragraph'] : false;
                          
                          $paragraph_limit         = isset($ads['paragraph_limit']) ? $ads['paragraph_limit'] : '';
                          $pnumber         = isset($ads['paragraph_number']) ? $ads['paragraph_number'] : '';
                          
                          $exclude_from_class_id         = isset($ads['exclude_from_class_id']) ? $ads['exclude_from_class_id'] : '';

                          $insert_after         = isset($ads['insert_after']) ? $ads['insert_after'] : 1;

                          $closing_p        = '</p>';
                          $paragraphs       = array_filter(explode( $closing_p, $content ));
                          $p_count          = count($paragraphs);
                          $original_paragraph_no = $paragraph_no;                                                             
                          
                          if($paragraph_no <= $p_count){
                            if($ads['ad_type']== 'group_insertion'){
                                $p_count =$p_count -1;
                                $cusads = '<!--CusGI'.$ads['ad_id'].'-->';
                              $next_insert_val = $insert_after;
                              $displayed_ad =1;
                                foreach ($paragraphs as $index => $paragraph) {
                                    $addstart = false;
                                    if ( trim( $paragraph ) ) {
                                        $paragraphs[$index] .= $closing_p;
                                    }

                                    if((!empty($paragraph_limit) && $paragraph_limit < $displayed_ad) || ($index == $p_count )){
                                        break;
                                    }
                                        if($index+1 == $next_insert_val){
                                            $displayed_ad +=1;
                                          $next_insert_val = $next_insert_val+$insert_after;
                                          $addstart = true;
                                      }
                                        if($addstart){
                                            $paragraphs[$index] .= $cusads;
                                        }
                                }
                            }else if($ads['ad_type']== 'sticky_scroll'){
                                $p_count =$p_count -1;
                                $cusads = '<!--CusSS'.$ads['ad_id'].'-->';
                              $next_insert_val = $insert_after;
                              $displayed_ad =1;
                                foreach ($paragraphs as $index => $paragraph) {
                                    $addstart = false;
                                    if ( trim( $paragraph ) ) {
                                        $paragraphs[$index] .= $closing_p;
                                    }

                                    if((!empty($paragraph_limit) && $paragraph_limit < $displayed_ad) || ($index == $p_count )){
                                        break;
                                    }
                                        if($index+1 == $next_insert_val){
                                            $displayed_ad +=1;
                                          $next_insert_val = $next_insert_val+$insert_after;
                                          $addstart = true;
                                      }
                                        if($addstart){
                                            $paragraphs[$index] .= $cusads;
                                        }
                                        if(!$repeat_paragraph)
                                        {
                                            break;
                                        }
                                }
                            }else{
                                $displayed_ad = 1;
                                $index = 0;
                                while ($index < count($paragraphs)) {
                                    if (!isset($paragraphs[$index])) {
                                        $index++;
                                        continue;
                                    }
                                    $paragraph = $paragraphs[$index];
                                    
                                    if (trim($paragraph)) {
                                        $paragraphs[$index] .= $closing_p;
                                    }
                                    
                                    if ($paragraph_no == $index + 1) {
                                        if(!empty($exclude_from_class_id)){
                                            $new_para = $paragraphs[$index] . $cusads;
                                            $is_found_parent = quads_check_parent_by_dynamic_block($new_para, $content, $exclude_from_class_id);
                                            if ($is_found_parent == true) {
                                                $index++; // Increment index before continuing
                                                $paragraph_no++;
                                                continue; // Skip to next iteration
                                            }
                                        }
                                        
                                        // Only execute this if $is_found_parent is false
                                        $paragraphs[$index] .= $cusads;
                                        
                                        // Check display limit and break if exceeded
                                        if (!empty($paragraph_limit) && $paragraph_limit < ($displayed_ad + 1)) {
                                            break;
                                        }
                                        
                                        // Handle paragraph repetition
                                        if ($repeat_paragraph && ($paragraph_no < $p_count - $original_paragraph_no)) {
                                            $displayed_ad++;
                                            $paragraph_no = $original_paragraph_no + $paragraph_no;
                                        }
                                    }
                                  
                                    $index++; // Always increment index to ensure progress
                                }
                            }
                              $content = implode( '', $paragraphs ); 
                          }else{                        
                              if($end_of_post){
                                  $content = $content.$cusads;   
                              }                                
                          }
                         
                      }
                      
                        break;
                    
                    case 'after_image':

                        // Sanitation
                        $imgtag = "<img";
                        $delimiter = ">";
                        $caption = "[/caption]";
                        $atag = "</a>";
                        $content = str_replace( "<IMG", $imgtag, $content );
                        $content = str_replace( "</A>", $atag, $content );

                        // Get all images in content
                        $imagesArray = explode( $imgtag, $content );
                        // Modify Image ad
                        if( ( int ) $imageNo < count( $imagesArray ) ) {
                            //Get all tags
                            $tagsArray = explode( $delimiter, $imagesArray[$imageNo] );
                            if( count( $tagsArray ) > 1 ) {
                                $captionArray = explode( $caption, $imagesArray[$imageNo] );
                                $ccp = ( count( $captionArray ) > 1 ) ? strpos( strtolower( $captionArray[0] ), '[caption ' ) === false : false;
                                $imagesArrayAtag = explode( $atag, $imagesArray[$imageNo] );
                                $cdu = ( count( $imagesArrayAtag ) > 1 ) ? strpos( strtolower( $imagesArrayAtag[0] ), '<a href' ) === false : false;
                                // Show ad after caption
                                if( $imageCaption && $ccp ) {
                                    $imagesArray[$imageNo] = implode( $caption, array_slice( $captionArray, 0, 1 ) ) . $caption . "\r\n" .$cusads. "\r\n" . implode( $caption, array_slice( $captionArray, 1 ) );
                                } else if( $cdu ) {
                                    $imagesArray[$imageNo] = implode( $atag, array_slice( $imagesArrayAtag, 0, 1 ) ) . $atag . "\r\n" . $cusads . "\r\n" . implode( $atag, array_slice( $imagesArrayAtag, 1 ) );
                                } else {
                                    $imagesArray[$imageNo] = implode( $delimiter, array_slice( $tagsArray, 0, 1 ) ) . $delimiter . "\r\n" .$cusads . "\r\n" . implode( $delimiter, array_slice( $tagsArray, 1 ) );
                                }
                            }
                            $content = implode( $imgtag, $imagesArray );
                        }

                    break; 
                    
                    case 'before_image':
                        // Sanitation
                        $imgtag = "<img";
                        $delimiter = ">";
                        $caption = "[/caption]";
                        $atag = "</a>";
                        $content = str_replace( "<IMG", $imgtag, $content );
                        $content = str_replace( "</A>", $atag, $content );
                        // Get all images in content
                        $imagesArray = explode( $imgtag, $content );
                        // Modify Image ad
                        if( ( int ) $imageNo < count( $imagesArray ) ) {
                            //Get all tags
                            $tagsArray = explode( $delimiter, $imagesArray[$imageNo] );
                            if( count( $tagsArray ) > 1 ) {
                                $captionArray = explode( $caption, $imagesArray[$imageNo] );
                                $ccp = ( count( $captionArray ) > 1 ) ? strpos( strtolower( $captionArray[0] ), '[caption ' ) === false : false;
                                $imagesArrayAtag = explode( $atag, $imagesArray[$imageNo] );
                                $cdu = ( count( $imagesArrayAtag ) > 1 ) ? strpos( strtolower( $imagesArrayAtag[0] ), '<a href' ) === false : false;
                                // Show ad before caption
                                if( $imageCaption && $ccp ) {
                                    $imagesArray[$imageNo] = $caption . "\r\n" .$cusads. "\r\n" .implode( $caption, array_slice( $captionArray, 0, 1 ) ) . '<img '.implode( $caption, array_slice( $captionArray, 1 ) );
                                } else if( $cdu ) {
                                    $imagesArray[$imageNo] = $atag . "\r\n" . $cusads . "\r\n" . implode( $atag, array_slice( $imagesArrayAtag, 0, 1 ) ) .'<img '.implode( $atag, array_slice( $imagesArrayAtag, 1 ) );
                                } else {
                                    $imagesArray[$imageNo] = $delimiter . "\r\n" .$cusads . "\r\n" . implode( $delimiter, array_slice( $tagsArray, 1 ) ).'<img '.implode( $delimiter, array_slice( $tagsArray, 0, 1 ) );
                                }
                            }
                            $content = implode( $imgtag, $imagesArray);
                        }

                    break;

                    case 'after_the_percentage':
                    
                        $content =  quads_remove_ad_from_content($content,$cusads,$ads);

                    break;
                    case 'ad_after_customq':
                        $type_name = 'custom';
                        $custom_name = isset($ads['after_customq_name']) ? $ads['after_customq_name'] : '';
                        $repeat_paragraph = (isset($ads['repeat_paragraph']) && !empty($ads['repeat_paragraph'])) ? $ads['repeat_paragraph'] : false;
                        if( strpos($content, "</blockquote>") || strpos($content, "</table>")){
                            $content =  quads_remove_ad_from_content($content,$cusads,'',$paragraph_no,$repeat_paragraph);
                        }else{
                            if(!empty($custom_name)){
                                $custom_name = '//*'.$custom_name;
                                $content = quads_after_id_class_ad_creator($content,$custom_name,$type_name);
                                $content = str_replace('afterCustomQAd', $cusads, $content);
                            }
                        }
                    break; 
                    case 'ad_after_html_tag':
                        $tag = 'p';
                        switch ( $ads['count_as_per']) {
                            case 'p_tag':
                                 $tag = 'p';
                                 break;
                            case 'div_tag':
                                 $tag = 'div';
                                 break;
                            case 'img_tag':
                                 $tag = 'img';
                                 break;
                            case 'custom_tag':
                                 $tag = $ads['enter_your_tag'];
                                 break;
                             
                             default:
                                 $tag = $ads['count_as_per'];
                                 break;
                        }
                            
                                                                                       
                            $repeat_paragraph = (isset($ads['repeat_paragraph']) && !empty($ads['repeat_paragraph'])) ? $ads['repeat_paragraph'] : false;
                            if( strpos($content, "</blockquote>") || strpos($content, "</table>")){
                          $content =  quads_remove_ad_from_content($content,$cusads,'',$paragraph_no,$repeat_paragraph);
                        }else{
                                $closing_p        = '</'.$tag.'>';
                            $paragraphs       = explode( $closing_p, $content );
                            $p_count          = count($paragraphs);
                            $original_paragraph_no = $paragraph_no;
                            if($paragraph_no <= $p_count){

                                foreach ($paragraphs as $index => $paragraph) {
                                    if($p_count==($index+1)){ 
                                        continue;
                                    }
                                    if ( trim( $paragraph ) ) {
                                        $paragraphs[$index] .= $closing_p;
                                    }
                                    if ( $paragraph_no == $index + 1 ) {
                                        $paragraphs[$index] .= $cusads;
                                        if($repeat_paragraph){
                                         $paragraph_no =  $original_paragraph_no+$paragraph_no; 
                                        }
                                    }
                                }
                                $content = implode( '', $paragraphs ); 
                            }else{
                                if($end_of_post){
                                    $content = $content.$cusads;   
                                }                                
                            }  
                            }                                                      
                        break; 
                        
                        case 'ad_before_html_tag':
                            $tag = 'p';
                            switch ( $ads['count_as_per']) {
                                case 'p_tag':
                                     $tag = 'p';
                                     break;
                                case 'div_tag':
                                     $tag = 'div';
                                     break;
                                case 'img_tag':
                                     $tag = 'img';
                                     break;
                                case 'custom_tag':
                                     $tag = $ads['enter_your_tag'];
                                     break;
                                 
                                 default:
                                     $tag = $ads['count_as_per'];
                                     break;
                            }
                                
                                                                                           
                                $repeat_paragraph = (isset($ads['repeat_paragraph']) && !empty($ads['repeat_paragraph'])) ? $ads['repeat_paragraph'] : false;
                                if( strpos($content, "</blockquote>") || strpos($content, "</table>")){
                              $content =  quads_remove_ad_from_content($content,$cusads,'',$paragraph_no,$repeat_paragraph);
                            }else{
                                $string_data = $content;
                                $pattern_ = "/<".$tag."(.*?)>/i";
                                if($pattern_){
                                    if(preg_match_all($pattern_, $string_data, $matches)) {
                                        $p_reg_match = $matches;
                                       $finalmatch = $p_reg_match;
                                       if(isset($finalmatch[0]) && !empty($finalmatch[0]))
                                       {
                                        foreach ($finalmatch[0] as $key => $value) {
                                            $openingtag =   $value;
                                        }
                                       $opening_p        = $openingtag;
                                       $paragraphs       = explode( $opening_p, $content );
                                       $p_count          = count($paragraphs);
                                       $original_paragraph_no = $paragraph_no;
                                       if($paragraph_no <= $p_count){
                                           foreach ($paragraphs as $index => $paragraph) {
                                               $opening_p        = isset($finalmatch[0][$index]) ? $finalmatch[0][$index] : null;
                                               if ( trim( $paragraph ) || $index==0) {
                                                   $paragraphs[$index] .= '<'.$tag.'>';
                                               }
                                               if ( $paragraph_no == $index+1  ) {
                                                //$index = ($index>0) ? $index-1 : $index;
                                                if( strpos( $paragraphs[$index] , $opening_p ) > -1 ) {
                                                    $ad_c = $cusads.$opening_p;
                                                    $paragraphs[$index] = str_replace($opening_p,$ad_c,$paragraphs[$index]);
                                                   }else{
                                                    $paragraphs[$index] .= $opening_p;
                                                   }
                                                   if($repeat_paragraph){
                                                    $paragraph_no =  $original_paragraph_no+$paragraph_no; 
                                                   }
                                               }
                                           }
                                           $content = implode( '', $paragraphs );
                                        }
                                        }
                                        }}
                                        else{
                                           if($end_of_post){
                                               $content = $content.$cusads;   
                                           }                                
                                       }
                                
                            }                                                     
                        break;
                        case 'amp_after_paragraph':
                        if( function_exists('quads_is_amp_endpoint') && quads_is_amp_endpoint()){
                            if(strpos( $content, '<!--OffBfLastPara-->' ) === false ) {
                                $repeat_paragraph = (isset($ads['repeat_paragraph']) && !empty($ads['repeat_paragraph'])) ? $ads['repeat_paragraph'] : false;
                                $paragraph_limit         = isset($ads['paragraph_limit']) ? $ads['paragraph_limit'] : '';
                                $insert_after         = isset($ads['insert_after']) ? $ads['insert_after'] : 1;
      
                                $closing_p        = '</p>';
                                $paragraphs       = explode( $closing_p, $content );
                                $p_count          = count($paragraphs);
                                $original_paragraph_no = $paragraph_no;                                                             
                                
                                if($paragraph_no <= $p_count){
                                  if($ads['ad_type']== 'group_insertion'){
                                      $p_count =$p_count -1;
                                      $cusads = '<!--CusGI'.$ads['ad_id'].'-->';
                                    $next_insert_val = $insert_after;
                                    $displayed_ad =1;
                                      foreach ($paragraphs as $index => $paragraph) {
                                          $addstart = false;
                                          if ( trim( $paragraph ) ) {
                                              $paragraphs[$index] .= $closing_p;
                                          }
      
                                          if((!empty($paragraph_limit) && $paragraph_limit < $displayed_ad) || ($index == $p_count )){
                                              break;
                                          }
                                              if($index+1 == $next_insert_val){
                                                  $displayed_ad +=1;
                                                $next_insert_val = $next_insert_val+$insert_after;
                                                $addstart = true;
                                            }
                                              if($addstart){
                                                  $paragraphs[$index] .= $cusads;
                                              }
                                      }
                                  }else{
      
                                    foreach ($paragraphs as $index => $paragraph) {
                                        if ( trim( $paragraph ) ) {
                                            $paragraphs[$index] .= $closing_p;
                                        }
                                        if ( $paragraph_no == $index + 1 ) {
                                            $paragraphs[$index] .= $cusads;
                                            if($repeat_paragraph){
                                             $paragraph_no =  $original_paragraph_no+$paragraph_no; 
                                            }
                                        }
                                    }
                                  }
                                    $content = implode( '', $paragraphs ); 
                                }else{
                                    if($end_of_post){
                                        $content = $content.$cusads;   
                                    }                                
                                }
                            }                    
                        }
                        break;
                        case 'random_ad_placement':

                            if(strpos( $content, '<!--OffBfLastPara-->' ) === false ) {
                              $repeat_paragraph = true;
                              $paragraph_limit  = isset($ads['paragraph_limit']) ? $ads['paragraph_limit'] : '';
                              $closing_p        = '</p>';
                              $paragraphs       = array_filter(explode( $closing_p, $content ));
                              $p_count          = count($paragraphs);
                              $original_paragraph_no = $paragraph_no; 
                              $max_p_after = intval($original_paragraph_no/2);
                              if($max_p_after<=2){
                                $max_p_after += 2; 
                              }     
                              // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand                                                       
                              $insert_after     = mt_rand(2,$max_p_after);
                              if($paragraph_no <= $p_count){
                                if($ads['ad_type']== 'group_insertion'){
                                    $p_count =$p_count -1;
                                    $cusads = '<!--CusGI'.$ads['ad_id'].'-->';
                                  $next_insert_val = $insert_after;
                                  $displayed_ad =1;
                                    foreach ($paragraphs as $index => $paragraph) {
                                        $addstart = false;
                                        if ( trim( $paragraph ) ) {
                                            $paragraphs[$index] .= $closing_p;
                                        }
    
                                        if((!empty($paragraph_limit) && $paragraph_limit < $displayed_ad) || ($index == $p_count )){
                                            break;
                                        }
                                            if($index+1 == $next_insert_val){
                                                $displayed_ad +=1;
                                              $next_insert_val = $next_insert_val+$insert_after;
                                              $addstart = true;
                                          }
                                            if($addstart){
                                                $paragraphs[$index] .= $cusads;
                                            }
                                    }
                                }else if($ads['ad_type']== 'sticky_scroll'){
                                    $p_count =$p_count -1;
                                    $cusads = '<!--CusSS'.$ads['ad_id'].'-->';
                                  $next_insert_val = $insert_after;
                                  $displayed_ad =1;
                                    foreach ($paragraphs as $index => $paragraph) {
                                        $addstart = false;
                                        if ( trim( $paragraph ) ) {
                                            $paragraphs[$index] .= $closing_p;
                                        }
    
                                        if((!empty($paragraph_limit) && $paragraph_limit < $displayed_ad) || ($index == $p_count )){
                                            break;
                                        }
                                            if($index+1 == $next_insert_val){
                                                $displayed_ad +=1;
                                              $next_insert_val = $next_insert_val+$insert_after;
                                              $addstart = true;
                                          }
                                            if($addstart){
                                                $paragraphs[$index] .= $cusads;
                                            }
                                            if(!$repeat_paragraph)
                                            {
                                                break;
                                            }
                                    }
                                }else{
                                    $next_insert_val = $insert_after;
                                    $displayed_ad =1;
                                  foreach ($paragraphs as $index => $paragraph) {
                                    $addstart = false;
                                      if ( trim( $paragraph ) ) {
                                          $paragraphs[$index] .= $closing_p;
                                      }
                                      if ( $next_insert_val == $index + 1 ) {
                                        $displayed_ad +=1;
                                        $next_insert_val = $next_insert_val+$insert_after;
                                        $addstart = true;
                                      }
                                      if($addstart){
                                        $paragraphs[$index] .= $cusads;
                                    }
                                  }
                                }
                                  $content = implode( '', $paragraphs ); 
                              }else{                        
                                  if($end_of_post){
                                      $content = $content.$cusads;   
                                  }                                
                              }
                          }
                            break;
                }

                $quads_adsArrayCus[] = $i;   
            }
            $i++;
        }
        
    }
    return $content;
}

add_action( 'wp_enqueue_scripts', 'quads_add_sticky_script' );

function quads_add_sticky_script(){

    $quads_ads = quads_api_services_cllbck(); 
    if(isset($quads_ads['posts_data'])){        
        foreach($quads_ads['posts_data'] as $key => $value){
            $ads =$value['post_meta'];
            if($value['post']['post_status']== 'draft'){continue;}
            $quads_visibilty = apply_filters('quads_ad_conditional_visibility', $value['post_meta']);
            if(!$quads_visibilty){continue;}
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
            $is_click_fraud_on = quads_click_fraud_on();
            if(isset($ads['ad_id']))
            $post_status = get_post_status($ads['ad_id']); 
            else
              $post_status =  'publish';
            if($is_on && $is_visitor_on && $is_click_fraud_on && $post_status=='publish'){
                $ads  = apply_filters( 'quads_default_filter_position_data', $ads);
                $ads  = apply_filters( 'quads_default_filter_position_data_ab_testing', $ads);

                $position     = (isset($ads['position']) && $ads['position'] !='') ? $ads['position'] : '';
                if($position == 'ad_sticky_ad')
                {
                    $js_dir = QUADS_PLUGIN_URL . 'assets/js/';
                    $suffix = ( quadsIsDebugMode() ) ? '' : '.min';
                    wp_enqueue_script('quads-wp-qds-sticky', $js_dir . 'wp_qds_sticky'.$suffix .'.js', array('jquery'), QUADS_VERSION, false );
                    break;
                }
                else{
                    continue;
                }
            }
        }
        
    }

       
}

/**
 * Filter default ads
 * 
 * @global array $quads_options global settings
 * @global array $quads_adsArrayCus List of ad id'S
 * @param string $content
 * @return string content
 */
function quads_filter_default_ads( $content ) {

    global $quads_options, $quads_adsArrayCus;
    
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);

    if( $off_default_ads ) { // If default ads are disabled 
        return $content;
    }    
    // Default Ads
    $quads_adsArrayCus = array();

    // placeholder string for random ad
    $cusrnd = 'CusRnd';
    
    // placeholder string for custom ad spots
    $cusads = 'CusAds';
    
    // Beginning of Post
    $beginning_position_status = isset( $quads_options['pos1']['BegnAds'] ) ? true : false;
    $beginning_position_ad_id = isset( $quads_options['pos1']['BegnRnd'] ) ? $quads_options['pos1']['BegnRnd'] : 0;

    // Middle of Post
    $middle_position_status = isset( $quads_options['pos2']['MiddAds'] ) ? true : false;
    $middle_position_ad_id = isset( $quads_options['pos2']['MiddRnd'] ) ? $quads_options['pos2']['MiddRnd'] : 0;

    // End of Post
    $end_position_status = isset( $quads_options['pos3']['EndiAds'] ) ? true : false;
    $end_position_ad_id = isset( $quads_options['pos3']['EndiRnd'] ) ? $quads_options['pos3']['EndiRnd'] : 0;

    // After the more tag
    $more_position_status = isset( $quads_options['pos4']['MoreAds'] ) ? true : false;
    $more_position_ad_id = isset( $quads_options['pos4']['MoreRnd'] ) ? $quads_options['pos4']['MoreRnd'] : 0;

    // Right before the last paragraph
    $last_paragraph_position_status = isset( $quads_options['pos5']['LapaAds'] ) ? true : false;
    $last_paragraph_position_ad_id = isset( $quads_options['pos5']['LapaRnd'] ) ? $quads_options['pos5']['LapaRnd'] : 0;

    // After Paragraph option 1 - 3
    $number = 3; // number of paragraph ads | default value 3. 
    $default = 5; // Position. Let's start with id 5
    for ( $i = 1; $i <= $number; $i++ ) {
        $key = $default + $i; // 6,7,8

        $paragraph['status'][$i] = isset( $quads_options['pos' . $key]['Par' . $i . 'Ads'] ) ? $quads_options['pos' . $key]['Par' . $i . 'Ads'] : 0; // Status - active | inactive
        $paragraph['id'][$i] = isset( $quads_options['pos' . $key]['Par' . $i . 'Rnd'] ) ? $quads_options['pos' . $key]['Par' . $i . 'Rnd'] : 0; // Ad id   
        $paragraph['position'][$i] = isset( $quads_options['pos' . $key]['Par' . $i . 'Nup'] ) ? $quads_options['pos' . $key]['Par' . $i . 'Nup'] : 0; // Paragraph No  
        $paragraph['end_post'][$i] = isset( $quads_options['pos' . $key]['Par' . $i . 'Con'] ) ? $quads_options['pos' . $key]['Par' . $i . 'Con'] : 0; // End of post - yes | no        
    }
    // After Image ad
    $imageActive = isset( $quads_options['pos9']['Img1Ads'] ) ? $quads_options['pos9']['Img1Ads'] : false;
    $imageAdNo = isset( $quads_options['pos9']['Img1Rnd'] ) ? $quads_options['pos9']['Img1Rnd'] : false;
    $imageNo = isset( $quads_options['pos9']['Img1Nup'] ) ? $quads_options['pos9']['Img1Nup'] : false;
    $imageCaption = isset( $quads_options['pos9']['Img1Con'] ) ? $quads_options['pos9']['Img1Con'] : false;


    if( $beginning_position_ad_id == 0 ) {
        $b1 = $cusrnd;
    } else {
        $b1 = $cusads . $beginning_position_ad_id;
        array_push( $quads_adsArrayCus, $beginning_position_ad_id );
    };
    
    if( $more_position_ad_id == 0 ) {
        $r1 = $cusrnd;
    } else {
        $r1 = $cusads . $more_position_ad_id;
        array_push( $quads_adsArrayCus, $more_position_ad_id );
    };
    
    if( $middle_position_ad_id == 0 ) {
        $m1 = $cusrnd;
    } else {
        $m1 = $cusads . $middle_position_ad_id;
        array_push( $quads_adsArrayCus, $middle_position_ad_id );
    };
    if( $last_paragraph_position_ad_id == 0 ) {
        $g1 = $cusrnd;
    } else {
        $g1 = $cusads . $last_paragraph_position_ad_id;
        array_push( $quads_adsArrayCus, $last_paragraph_position_ad_id );
    };
    if( $end_position_ad_id == 0 ) {
        $b2 = $cusrnd;
    } else {
        $b2 = $cusads . $end_position_ad_id;
        array_push( $quads_adsArrayCus, $end_position_ad_id );
    };
    for ( $i = 1; $i <= $number; $i++ ) {
        if( $paragraph['id'][$i] == 0 ) {
            $paragraph[$i] = $cusrnd;
        } else {
            $paragraph[$i] = $cusads . $paragraph['id'][$i];
            array_push( $quads_adsArrayCus, $paragraph['id'][$i] );
        };
    }
    //wp_die(print_r($quads_adsArrayCus));

    // Create the arguments for filter quads_filter_paragraphs
    $quads_args = array(
        'paragraph' => $paragraph,
        'cusads' => $cusads,
        'cusrnd' => $cusrnd,
        'AdsIdCus' => $quads_adsArrayCus,
    );

    // Execute filter to add more paragraph ad spots
    $quads_filtered = apply_filters( 'quads_filter_paragraphs', $quads_args );

    // The filtered arguments
    $paragraph = $quads_filtered['paragraph'];

    // filtered list of ad spots
    $quads_adsArrayCus = $quads_filtered['AdsIdCus'];

    // Create paragraph ads
    $number = 11;

    for ( $i = $number; $i >= 1; $i-- ) {
        if( !empty( $paragraph['status'][$i] ) ) {
            $sch = "</p>";
            $content = str_replace( "</P>", $sch, $content );
            
                        
            /**
             * Get all blockquote if there are any
             */
            
            preg_match_all("/<blockquote>(.*?)<\/blockquote>/s", $content, $blockquotes);
            
            /**
             * Replace blockquotes with placeholder
             */
            if(!empty($blockquotes)){
               $bId = 0;
               foreach($blockquotes[0] as $blockquote){
                  $replace = "#QUADSBLOCKQUOTE" . $bId . '#';
                  $content = str_replace(trim($blockquote), $replace, $content);
                  $bId++;
               }
            }
            
            // Get paragraph tags
            $paragraphsArray = explode( $sch, $content );
          
           /**
            * Check if last element is empty and remove it
            */
            if(trim($paragraphsArray[count($paragraphsArray)-1]) == "") array_pop($paragraphsArray);
            
            if( ( int ) $paragraph['position'][$i] <= count( $paragraphsArray ) ) {
                  $content = implode( $sch, array_slice( $paragraphsArray, 0, $paragraph['position'][$i] ) ) . $sch . '<!--' . $paragraph[$i] . '-->' . implode( $sch, array_slice( $paragraphsArray, $paragraph['position'][$i] ) );
            } elseif( $paragraph['end_post'][$i] ) {
                $content = implode( $sch, $paragraphsArray ) . '<!--' . $paragraph[$i] . '-->';
            }
            
            /**
             * Put back blockquotes into content
             */
            
            if(!empty($blockquotes)){
               $bId = 0;
               foreach($blockquotes[0] as $blockquote){
                  $search = '#QUADSBLOCKQUOTE' . $bId . '#'; 
                  $content = str_replace($search, trim($blockquote), $content);
                  $bId++;
               }
            }
        }
    }

    // Check if image ad is random one
    if( $imageAdNo == 0 ) {
        $imageAd = $cusrnd;
    } else {
        $imageAd = $cusads . $imageAdNo;
        array_push( $quads_adsArrayCus, $imageAdNo );
    };


    // Beginning of post ad
    if( $beginning_position_status && strpos( $content, '<!--OffBegin-->' ) === false ) {
        $content = '<!--' . $b1 . '-->' . $content;
    }
    
        // Check if ad is middle one
    if( $middle_position_status && strpos( $content, '<!--OffMiddle-->' ) === false ) {
        if( substr_count( strtolower( $content ), '</p>' ) >= 2 ) {
            $closingTagP = "</p>";
            $content = str_replace( "</P>", $closingTagP, $content );
            $paragraphsArray = explode( $closingTagP, $content );
            
            /**
            * Check if last element is empty and remove it
            */
            if(trim($paragraphsArray[count($paragraphsArray)-1]) == "") array_pop($paragraphsArray);
            
            $nn = 0;
            $mm = strlen( $content ) / 2;
            for ( $i = 0; $i < count( $paragraphsArray ); $i++ ) {
                $nn += strlen( $paragraphsArray[$i] ) + 4;
                if( $nn > $mm ) {
                    if( ($mm - ($nn - strlen( $paragraphsArray[$i] ))) > ($nn - $mm) && $i + 1 < count( $paragraphsArray ) ) {
                        $paragraphsArray[$i + 1] = '<!--' . $m1 . '-->' . $paragraphsArray[$i + 1];
                    } else {
                        $paragraphsArray[$i] = '<!--' . $m1 . '-->' . $paragraphsArray[$i];
                    }
                    break;
                }
            }
           
            $content = implode( $closingTagP, $paragraphsArray );
        }
    }
    
    // End of Post ad
    if( $end_position_status && strpos( $content, '<!--OffEnd-->' ) === false ) {
        $content = $content . '<!--' . $b2 . '-->';
    }
    
    

    // Check if ad is after "More Tag"
    if( $more_position_status && strpos( $content, '<!--OffAfMore-->' ) === false ) {
        $mmr = '<!--' . $r1 . '-->';
        $postid = get_the_ID();
        $content = str_replace( '<span id="more-' . $postid . '"></span>', $mmr, $content );
    }
    
    // Right before last paragraph ad
    if( $last_paragraph_position_status && strpos( $content, '<!--OffBfLastPara-->' ) === false ) {
        $closingTagP = "</p>";
        $content = str_replace( "</P>", $closingTagP, $content );
        $paragraphsArray = explode( $closingTagP, $content );
        
        
            /**
            * Check if last element is empty and remove it
            */
            if(trim($paragraphsArray[count($paragraphsArray)-1]) == "") array_pop($paragraphsArray);
        
        
        //if( count( $paragraphsArray ) > 2 && !strpos($paragraphsArray[count( $paragraphsArray ) - 1], '</blockquote>')) {
        if( count( $paragraphsArray ) > 2) {
            $content = implode( $closingTagP, array_slice( $paragraphsArray, 0, count( $paragraphsArray ) - 1 ) ) . '<!--' . $g1 . '-->' . $closingTagP . $paragraphsArray[count( $paragraphsArray ) - 1];
        }

    }

    // After Image ad
    if( $imageActive ) {

        // Sanitation
        $imgtag = "<img";
        $delimiter = ">";
        $caption = "[/caption]";
        $atag = "</a>";
        $content = str_replace( "<IMG", $imgtag, $content );
        $content = str_replace( "</A>", $atag, $content );

        // Get all images in content
        $imagesArray = explode( $imgtag, $content );
        // Modify Image ad
        if( ( int ) $imageNo < count( $imagesArray ) ) {
            //Get all tags
            $tagsArray = explode( $delimiter, $imagesArray[$imageNo] );
            if( count( $tagsArray ) > 1 ) {
                $captionArray = explode( $caption, $imagesArray[$imageNo] );
                $ccp = ( count( $captionArray ) > 1 ) ? strpos( strtolower( $captionArray[0] ), '[caption ' ) === false : false;
                $imagesArrayAtag = explode( $atag, $imagesArray[$imageNo] );
                $cdu = ( count( $imagesArrayAtag ) > 1 ) ? strpos( strtolower( $imagesArrayAtag[0] ), '<a href' ) === false : false;
                // Show ad after caption
                if( $imageCaption && $ccp ) {
                    $imagesArray[$imageNo] = implode( $caption, array_slice( $captionArray, 0, 1 ) ) . $caption . "\r\n" . '<!--' . $imageAd . '-->' . "\r\n" . implode( $caption, array_slice( $captionArray, 1 ) );
                } else if( $cdu ) {
                    $imagesArray[$imageNo] = implode( $atag, array_slice( $imagesArrayAtag, 0, 1 ) ) . $atag . "\r\n" . '<!--' . $imageAd . '-->' . "\r\n" . implode( $atag, array_slice( $imagesArrayAtag, 1 ) );
                } else {
                    $imagesArray[$imageNo] = implode( $delimiter, array_slice( $tagsArray, 0, 1 ) ) . $delimiter . "\r\n" . '<!--' . $imageAd . '-->' . "\r\n" . implode( $delimiter, array_slice( $tagsArray, 1 ) );
                }
            }
            $content = implode( $imgtag, $imagesArray );
        }
    }

    return $content;
}
/**
 * Sanitize content and return it cleaned
 * 
 * @param string $content
 * @return string
 */
function quads_sanitize_content($content){
    
    /* ... Tidy up content ... */
    // Replace all <p></p> tags with placeholder ##QA-TP1##
    $content = str_replace( "<p></p>", "##QA-TP1##", $content );

    // Replace all <p>&nbsp;</p> tags with placeholder ##QA-TP2##
    $content = str_replace( "<p>&nbsp;</p>", "##QA-TP2##", $content );
    
    return $content;
}



/**
 * Parse random ads which are created from quicktag <!--RndAds-->
 * 
 * @global array $adsArray
 * @global int $quads_visibleContentAds
 * @return content
 */
function quads_parse_random_quicktag_ads($content){
    global $quads_adsArray, $quads_visibleContentAds, $quads_options;
    $maxAds = isset($quads_options['maxads']) ? $quads_options['maxads'] : 10;
    /*
     * Replace RndAds Random Ads
     */
    if(!is_array($quads_adsArray)) { $quads_adsArray = [];}
    $content=  str_replace('[quads id=RndAds]', '<!--RndAds-->', $content);
    if( strpos( $content, '<!--RndAds-->' ) !== false && is_singular() ) {
        $adsArrayTmp = array();
        shuffle( $quads_adsArray );
        for ( $i = 1; $i <= $maxAds - $quads_visibleContentAds; $i++ ) {
            if( $i <= count( $quads_adsArray ) ) {
                array_push( $adsArrayTmp, $adsArray[$i - 1] );
            }
        }
        $tcx = count( $adsArrayTmp );
        $tcy = substr_count( $content, '<!--RndAds-->' );
        for ( $i = $tcx; $i <= $tcy - 1; $i++ ) {
            array_push( $adsArrayTmp, -1 );
        }
        shuffle( $adsArrayTmp );
        for ( $i = 1; $i <= $tcy; $i++ ) {
            $tmp = $adsArrayTmp[0];
            $content = quads_replace_ads( $content, 'RndAds', $adsArrayTmp[0] );
            $adsArrayTmp = quads_del_element( $adsArrayTmp, 0 );
            if( $tmp != -1 ) {
                $quads_visibleContentAds += 1;
            };
            //quads_set_ad_count_content();
            //if( quads_ad_reach_max_count() ) {
            if( $quads_visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
                $content = quads_clean_tags( $content );
                return $content;
            }
        }
    }
    
    return $content;
}

/**
 * Parse random default ads which can be enabled from general settings
 * 
 * @global array $adsArray
 * @global int $quads_visibleContentAds
 * @return string
 */
 function quads_parse_random_ads_new($content) {
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);
    if( $off_default_ads ) {
        return $content;
    }
    $selected_ads =array();
    $random_ads_list_after =array();

    $number_rand_ads = substr_count( $content, '<!--CusRnd' );
    for ( $i = 0; $i <= $number_rand_ads - 1; $i++ ) {
        preg_match("#<!--CusRnd(.+?)-->#si", $content, $match);
        $ad_id = $match['1'];
        if(!empty($ad_id)){
            $ad_meta = get_post_meta($ad_id, '',true);
        }
        $random_ads_list = unserialize($ad_meta['random_ads_list']['0']);
        if (!is_array($random_ads_list)) return $content; 
        $temp_array =array();
        foreach ($random_ads_list as $radom_ad ) {
            if (isset($radom_ad['value']) && get_post_status($radom_ad['value']) == 'publish'){
                $temp_array[] = $radom_ad['value'];
            }
        }
        $used_for_this_ad_id = isset($selected_ads[$ad_id]) ? $selected_ads[$ad_id] : array();
        $random_ads_list_after =  array_diff($temp_array, $used_for_this_ad_id);
        
        // If all ads have been used for this ad_id, reset the used list to allow repetition
        if(empty($random_ads_list_after)) {
            $random_ads_list_after = $temp_array;
            $used_for_this_ad_id = array();
        }
        
        $keys = array_keys($random_ads_list_after); 
        if(is_array($keys) && isset($keys[0])){
            shuffle($keys); 
            $randomid = $random_ads_list_after[$keys[0]]; 
            $used_for_this_ad_id[] = $randomid;
            $selected_ads[$ad_id] = $used_for_this_ad_id;
            $enabled_on_amp = (isset($ad_meta['enabled_on_amp'][0]))? $ad_meta['enabled_on_amp'][0]: '';
            $content = quads_replace_ads_new( $content, 'CusRnd' . $ad_id, $randomid,$enabled_on_amp);
        }
    }
    return $content;

}



/**
 * Parse random default ads which can be enabled from general settings
 * 
 * @global array $adsArray
 * @global int $quads_visibleContentAds
 * @return string
 */
function quads_parse_random_ads($content) {
    global $quads_adsRandom, $quads_visibleContentAds;
    
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);
    if( $off_default_ads ) { // disabled default ads
        return $content;
    }

    if( strpos( $content, '<!--CusRnd-->' ) !== false && is_singular() ) {

        $tcx = count( $quads_adsRandom );
        // How often is a random ad appearing in content
        $number_rand_ads = substr_count( $content, '<!--CusRnd-->' );

        for ( $i = $tcx; $i <= $number_rand_ads - 1; $i++ ) {
            array_push( $quads_adsRandom, -1 );
        }
        shuffle( $quads_adsRandom );
        //wp_die(print_r($quads_adsRandom));
        //wp_die($quads_adsRandom[0]);
        for ( $i = 1; $i <= $number_rand_ads; $i++ ) {
            $content = quads_replace_ads( $content, 'CusRnd', $quads_adsRandom[0] );
            $quads_adsRandom = quads_del_element( $quads_adsRandom, 0 );
            $quads_visibleContentAds += 1;
            //quads_set_ad_count_content();
            //if( quads_ad_reach_max_count() ) {
            if( $quads_visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
                $content = quads_clean_tags( $content );
                return $content;
            }
        }
    }

    return $content;
}

/**
 * Parse Quicktags
 * 
 * @global array $adsArray
 * @param string $content
 * @return string
 */
function quads_parse_quicktags($content){
    global $quads_adsArray, $quads_visibleContentAds;
    //print_r(count($quads_adsArray));
    if (!is_array($quads_adsArray)){
        return $content;
    }
    $idx = 0;
    for ( $i = 1; $i <= count( $quads_adsArray ); $i++ ) {
        if( strpos( $content, '<!--Ads' . $quads_adsArray[$idx] . '-->' ) !== false ) {
            $content = quads_replace_ads( $content, 'Ads' . $quads_adsArray[$idx], $quads_adsArray[$idx] );
            //$quads_adsArray = quads_del_element( $quads_adsArray, $idx );
            $quads_visibleContentAds += 1;
            $idx +=1;
            //quads_set_ad_count_content();
            if( $quads_visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
                $content = quads_clean_tags( $content );
                return $content;
            }
        } else {
            $idx += 1;
        }
    }
    
    return $content;
}

/**
 * Parse default ads Beginning/Middle/End/Paragraph Ads1-10
 * 
 * @param string $content
 * @return string
 */
function quads_parse_default_ads( $content ) {
    global $quads_adsArrayCus, $quads_adsRandom, $quads_adsArray, $quads_visibleContentAds;
     
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);

    if( $off_default_ads ) { // disabled default ads
        return $content;
    }
    // Create the array which contains the random ads
    $quads_adsRandom = $quads_adsArray;

    for ( $i = 0; $i <= count( $quads_adsArrayCus ); $i++ ) {
        
        if( isset( $quads_adsArrayCus[$i] ) && strpos( $content, '<!--CusAds' . $quads_adsArrayCus[$i] . '-->' ) !== false && in_array( $quads_adsArrayCus[$i], $quads_adsArray ) ) {
             
            $content = quads_replace_ads( $content, 'CusAds' . $quads_adsArrayCus[$i], $quads_adsArrayCus[$i] );

            // Create array $quads_adsRandom for quads_parse_random_ads() parsing functions to make sure that the random function 
            // is never using ads that are already used on static ad spots which are generated with quads_parse_default_ads()
            if ($i == 0){
                $quads_adsRandom = quads_del_element($quads_adsRandom, array_search($quads_adsArrayCus[$i], $quads_adsRandom));
            }else{
                $quads_adsRandom = quads_del_element($quads_adsRandom, array_search($quads_adsArrayCus[$i-1], $quads_adsRandom));
            }
            
            $quads_visibleContentAds += 1;

            if( $quads_visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
             
                $content = quads_clean_tags( $content );
            }
        }
    }
    return $content;
}
function quads_parse_popup_ads($content) {
    if(!isset($_COOKIE['quads_popup'])){
    global $quads_options;
    preg_match("#<!--pop_up_ads(.+?)-->#si", $content, $match);
    if (!isset($match['1'])) {
        return $content;
    }
    $ad_id = $match['1'];
    if(!empty($ad_id)){
        $ad_meta = get_post_meta($ad_id, '',true);
    }
    $ads_list = !empty($ad_meta['ads_list']['0']) ? unserialize($ad_meta['ads_list']['0']) : "" ;

    if (!is_array($ads_list)) return $content;
    $temp_array =array();
    foreach ($ads_list as $ad ) {
        if (isset($ad['value'])){
            $temp_array[] = $ad['value'];
        }
    }

    $ad_code = array_rand($temp_array);

    $popup_type                    =  isset($ad_meta['popup_type'][0]) ? $ad_meta['popup_type'][0] : '';
    $everytime_popup       =  (isset($ad_meta['everytime_popup'][0]) && !empty($ad_meta['everytime_popup'][0])) ? $ad_meta['everytime_popup'][0] : 0;
    $specific_time_interval_sec       =  (isset($ad_meta['specific_time_interval_sec'][0]) && !empty($ad_meta['specific_time_interval_sec'][0])) ? $ad_meta['specific_time_interval_sec'][0] : 0;
    $on_scroll_popup_percentage       =  (isset($ad_meta['on_scroll_popup_percentage'][0]) && !empty($ad_meta['on_scroll_popup_percentage'][0])) ? $ad_meta['on_scroll_popup_percentage'][0] : 0;
    
    //cookie
    $popup_set_cookie_type       =  (isset($ad_meta['popup_set_cookie_type'][0]) && !empty($ad_meta['popup_set_cookie_type'][0])) ? $ad_meta['popup_set_cookie_type'][0] : 'withcookieexp';
    $pop_set_cookie_indays       =  (isset($ad_meta['pop_set_cookie_indays'][0]) && !empty($ad_meta['pop_set_cookie_indays'][0])) ? $ad_meta['pop_set_cookie_indays'][0] : 1;
    $data_cke = $popup_set_cookie_type == 'withcookieexp' ? $pop_set_cookie_indays : 'no';
    
    $adsresultset = array();
    if( $ads_list ){
        foreach ($temp_array as $post_ad_id){
            $ad_meta_group = get_post_meta($post_ad_id, '',true);
            $adsresultset[] = array(
                'ad_id'                     => $post_ad_id,
                'ad_type'                   => $ad_meta_group['ad_type'],
                'ad_adsense_type'           => $ad_meta_group['adsense_type'],
                'ad_data_client_id'         => $ad_meta_group['g_data_ad_client'][0],
                'ad_data_ad_slot'           => $ad_meta_group['g_data_ad_slot'][0],
                // 'ad_custom_code'            => $ad_meta_group['custom_code'],
                'width'                     => $ad_meta_group['g_data_ad_width'],
                'height'                    => $ad_meta_group['g_data_ad_height'],
                'code'                      => $ad_meta_group['code'],
                'network_code'              => $ad_meta_group['network_code'],
                'ad_unit_name'              => $ad_meta_group['ad_unit_name'],
                // 'block_id'                  => $ad_meta_group['block_id'],
                'data_container'            => $ad_meta_group['data_container'],
                'data_js_src'               => $ad_meta_group['data_js_src'],
                'data_cid'                  => $ad_meta_group['data_cid'],
                'data_crid'                 => $ad_meta_group['data_crid'],
                'taboola_publisher_id'      => $ad_meta_group['taboola_publisher_id'],
                'mediavine_site_id'         => $ad_meta_group['mediavine_site_id'],
                'outbrain_widget_ids'       => $ad_meta_group['outbrain_widget_ids'],
                'image_redirect_url'        => $ad_meta_group['image_redirect_url'],
                'ad_image'                  => $ad_meta_group['image_src'],
                'mobile_ad_image'           => $ad_meta_group['image_mobile_src'],
                
            ) ;
        }
        $response['quads_group_id'] = $ad_id;
        $response['quads_popup_type']           = 'popupads';
        $response['specific_time_popup']           = $specific_time_interval_sec;
        $response['on_scroll_popup']           = $on_scroll_popup_percentage;
        $response['ads'] = $adsresultset;

        $arr = array(
            'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
            'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
            'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
            'float:none;margin:%1$dpx;');

        $adsalign = isset($quads_options['ads']['ad' . $ad_id]['align']) ? $quads_options['ads']['ad' . $ad_id]['align'] : 0; // default
        $adsmargin = isset( $quads_options['ads']['ad' . $ad_id]['margin'] ) ? $quads_options['ads']['ad' . $ad_id]['margin'] : '0'; // default
        $margin = sprintf( $arr[( int ) $adsalign], $adsmargin );
        $enabled_on_amp =  isset( $quads_options['ads']['ad' . $ad_id]['enabled_on_amp'] ) ? $quads_options['ads']['ad' . $ad_id]['enabled_on_amp'] : 0; // default

        // Do not create any inline style on AMP site
        $style = '' ;
        $popups_data = '';
        $addl_class='';
        if( $popup_type == "everytime_popup" ){
            $style = "display:block";
            $popups_data = '';
        }
        if( $popup_type == "specific_time_popup" || $popup_type == "load_on_top" || $popup_type == "load_on_bottom"){
            $style = "display:none";
            $popups_data = "data-timer=".$specific_time_interval_sec."";
        }
        if( $popup_type == "on_scroll_popup" ){
            $style = "display:none";
            $popups_data = "data-percent=".$on_scroll_popup_percentage."";
        }

        if(  $popup_type == "load_on_top" || $popup_type == "load_on_bottom"){
            $addl_class='quads_'.$popup_type;
        }

        $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  popup Ad -->' . "\n" .
            '<div class="quads-location quads-popupad ad_' . esc_attr($ad_id) . ' '.esc_attr($addl_class).'" id="quads-ad'. esc_attr($ad_id) .'" '.$popups_data.' data-popuptype="'.$popup_type.'" data-cke="'.$data_cke.'" style="' . $style . '">' . "\n";
        $code .='<div class="quads-groups-ads-json"  data-json="'. esc_attr(json_encode($response)).'">';
        $code .='</div>';

        $code .='<div style="display:none;" class="quads_ad_container__pre"></div><div data-id="'.esc_attr($ad_id).'" class="quads quads_ad_container_">
        
        </div>';

        $code .= '</div>' . "\n";

        $cont = explode('<!--CusRot'.$ad_id.'-->', $content, 2);

        $content =  $cont[0].$code;
        $js_dir = QUADS_PLUGIN_URL . 'assets/js/';

        // Use minified libraries if SCRIPT_DEBUG is turned off
        $suffix = ( quadsIsDebugMode() ) ? '' : '.min';

        // These have to be global
        wp_enqueue_script( 'quads-wp-qds-popup', $js_dir . 'wp_qds_popup' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );

    }else{
        $content = quads_replace_ads_new( $content, 'CusRot' . $ad_id, $temp_array[$ad_code],$enabled_on_amp);
    }
}
    return  $content ;
}
function quads_parse_video_ads($content) {
    if(!isset($_COOKIE['quads_video'])){
        
        preg_match("#<!--video_ad(.+?)-->#si", $content, $match);
        if (!isset($match['1'])) {
            return $content;
        }
        $ad_id = $match['1'];

        if(!empty($ad_id)){
            $ad_meta = get_post_meta($ad_id, '',true);
        }
        $video_ad_type                    =  isset($ad_meta['video_ad_type'][0]) ? $ad_meta['video_ad_type'][0] : '';
    $specific_time_interval_sec_video       =  (isset($ad_meta['specific_time_interval_sec_video'][0]) && !empty($ad_meta['specific_time_interval_sec_video'][0])) ? $ad_meta['specific_time_interval_sec_video'][0] : 0;
    $position =  (isset($ad_meta['video_ad_type_position'][0]) && !empty($ad_meta['video_ad_type_position'][0])) ? $ad_meta['video_ad_type_position'][0] : 0;
    $on_scroll_video_percentage       =  (isset($ad_meta['on_scroll_video_percentage'][0]) && !empty($ad_meta['on_scroll_video_percentage'][0])) ? $ad_meta['on_scroll_video_percentage'][0] : 0;
    $V_image_src       =  (isset($ad_meta['image_src'][0]) && !empty($ad_meta['image_src'][0])) ? $ad_meta['image_src'][0] : 0;
    $V_redirect       =  (isset($ad_meta['image_redirect_url'][0]) && !empty($ad_meta['image_redirect_url'][0])) ? $ad_meta['image_redirect_url'][0] : '';
    $V_image_width       =  (isset($ad_meta['video_width'][0]) && !empty($ad_meta['video_width'][0])) ? $ad_meta['video_width'][0] : '350';
    $V_image_height       =  (isset($ad_meta['video_height'][0]) && !empty($ad_meta['video_height'][0])) ? $ad_meta['video_height'][0] :'auto';

    if(quads_is_amp_endpoint())
    {
        $position_array =['v_left'=>'position:fixed;bottom:10px;left:10px;','v_right'=>'position:fixed;bottom:10px;right:10px;'];
        $amp_code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  popup Ad -->' . "\n" .
                    '<div class="quads-video ad_' . esc_attr($ad_id) . ' quads-ad'. esc_attr($ad_id) .'" id="quads-ad'. esc_attr($ad_id) .'" style="z-index:99999;width:'.esc_attr($V_image_width).'px;height:'.esc_attr($V_image_width*0.75).'px;'.esc_attr($position_array[$position]).'">
                    <button style="float:right" id="video_close_'.esc_attr($ad_id).'" on="tap:video_close_'.esc_attr($ad_id).'.hide,video_amp_'.esc_attr($ad_id).'.hide">X</button>';
    
        $amp_code .= '<amp-video layout="intrinsic" id="video_amp_'.esc_attr($ad_id).'" width="'.esc_attr($V_image_width).'" height="'.esc_attr($V_image_width*0.75).'" src="'.esc_url($V_image_src).'" autoplay></amp-video></div>';
        $cont = explode('<!--CusRot'.$ad_id.'-->', $content, 2);
        $content =  $cont[0].$amp_code;
        return $content;
    }
    
    $adsresultset = array();
    if( $ad_meta ){
        foreach ($ad_meta as $post_ad_id){
            $ad_meta_group = get_post_meta($post_ad_id, '',true);
            
            if($post_ad_id[0] == 'video_ads'){
                $adsresultset[] = array(
                    'ad_id'                     => $post_ad_id,
                    'ad_type'                   => 'video_ads',
                );
            }
        }
        $response['quads_group_id'] = $ad_id;
        $response['quads_video_type']           = 'videoads';
        $response['specific_time_interval_sec_video']           = $specific_time_interval_sec_video;
        $response['on_scroll_video_percentage']           = $on_scroll_video_percentage;
        $response['viedo_url']           = $V_image_src;
        //$response['viedo_height']           = $V_image_height;
        $response['viedo_width']           = $V_image_width;
        $response['viedo_position']           = $position;
        $response['ads'] = $adsresultset;

        $arr = array(
            'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
            'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
            'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
            'float:none;margin:%1$dpx;');

        $adsalign = isset($quads_options['ads']['ad' . $ad_id]['align']) ? $quads_options['ads']['ad' . $ad_id]['align'] : 0; // default
        $adsmargin = isset( $quads_options['ads']['ad' . $ad_id]['margin'] ) ? $quads_options['ads']['ad' . $ad_id]['margin'] : '0'; // default
        $margin = sprintf( $arr[( int ) $adsalign], $adsmargin );

        // Do not create any inline style on AMP site
        $style = '' ;
        $videoad_data = '';
        if( $video_ad_type == "specific_time_video" ){
            $style = "display:none";
            $videoad_data = "data-position=".$position." data-timer=".$specific_time_interval_sec_video."";
        }
        if( $video_ad_type == "after_scroll_video" ){
            $style = "display:none";
            $videoad_data = "data-position=".$position." data-percent=".$on_scroll_video_percentage."";
        }

        $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  popup Ad -->' . "\n" .
            '<div class="video_main"><div class="quads-location quads-video ad_' . esc_attr($ad_id) . '" id="quads-ad'. esc_attr($ad_id) .'" '.$videoad_data.' data-videotype="'.$video_ad_type.'" data-redirect="'.esc_url($V_redirect).'" style="' . $style . '">' . "\n";

        $code .='<div class="quads-video-ads-json"  data-json="'. esc_attr(json_encode($response)).'">';
        $code .='</div>';

        $code .='<div data-id="'.esc_attr($ad_id).'" class="quads quads_ad_container_video">
        
        </div>';

        $code .= '</div>' . "\n";
        $code .= '</div>' . "\n";

        $cont = explode('<!--CusRot'.$ad_id.'-->', $content, 2);
        $content =  $cont[0].$code;
        $js_dir = QUADS_PLUGIN_URL . 'assets/js/';

        // Use minified libraries if SCRIPT_DEBUG is turned off
        $suffix = ( quadsIsDebugMode() ) ? '' : '.min';

        // These have to be global
        wp_enqueue_script( 'quads-wp-qds-video', $js_dir . 'wp_qds_video' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );

    }
}
    return  $content ;
}
function quads_parse_parallax_ads($content) {
    if(!isset($_COOKIE['quads_parallax'])){
        
        preg_match("#<!--parallax_ad(.+?)-->#si", $content, $match);
        if (!isset($match['1'])) {           
            return $content;
        }
        $ad_id = $match['1'];
        if(!empty($ad_id)){
            $ad_meta = get_post_meta($ad_id, '',true);
        }
        $parallax_ad_type                    =  'after_scroll_parallax_ads';
    $on_scroll_parallax_ads_percentage       =  (isset($ad_meta['on_scroll_parallax_ads_percentage'][0]) && !empty($ad_meta['on_scroll_parallax_ads_percentage'][0])) ? $ad_meta['on_scroll_parallax_ads_percentage'][0] : 20;
    $parallax_image_src       =  (isset($ad_meta['image_src'][0]) && !empty($ad_meta['image_src'][0])) ? $ad_meta['image_src'][0] : 0;
    $parallax_btn_url       =  (isset($ad_meta['parallax_btn_url'][0]) && !empty($ad_meta['parallax_btn_url'][0])) ? $ad_meta['parallax_btn_url'][0] : '';
    $parallax_image_width       =  (isset($ad_meta['parallax_ads_width'][0]) && !empty($ad_meta['parallax_ads_width'][0])) ? $ad_meta['parallax_ads_width'][0] : '800';
    $parallax_image_height       =  (isset($ad_meta['parallax_ads_height'][0]) && !empty($ad_meta['parallax_ads_height'][0])) ? $ad_meta['parallax_ads_height'][0] :'auto';

    
    $adsresultset = array();
    if( $ad_meta ){
        foreach ($ad_meta as $post_ad_id){
            $ad_meta_group = get_post_meta($post_ad_id, '',true);
            if($post_ad_id[0] == 'parallax_ads'){
                $adsresultset[] = array(
                    'ad_id'                     => $post_ad_id,
                    'ad_type'                   => 'parallax_ads',
                ) ;
            }
        }
        $response['quads_group_id'] = $ad_id;
        $response['quads_parallax_ads_type']           = 'parallax_ads';
        $response['on_scroll_parallax_ads_percentage']           = $on_scroll_parallax_ads_percentage;
        $response['parallax_btn_url']           = $parallax_btn_url;
        $response['parallax_image_src']           = $parallax_image_src;
        $response['parallax_ads_width']           = $parallax_image_width;
        $response['ads'] = $adsresultset;

        $arr = array(
            'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
            'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
            'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
            'float:none;margin:%1$dpx;');

        $adsalign = isset($quads_options['ads']['ad' . $ad_id]['align']) ? $quads_options['ads']['ad' . $ad_id]['align'] : 0; // default
        $adsmargin = isset( $quads_options['ads']['ad' . $ad_id]['margin'] ) ? $quads_options['ads']['ad' . $ad_id]['margin'] : '0'; // default
        $margin = sprintf( $arr[( int ) $adsalign], $adsmargin );

        // Do not create any inline style on AMP site
        $style = '' ;
        $parallax_ads_data = '';
        if( !empty($on_scroll_parallax_ads_percentage)){
            $style = "display:none";
            $parallax_ads_data = "data-percent=".$on_scroll_parallax_ads_percentage."";
        }

        $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  popup Ad -->' . "\n" .
            '<div class="parallax_main"><div class="quads_parallax_scroll_text">Keep Scrolling</div><div class="quads-parallax-spacer"></div><div class="quads-location quads-parallax ad_' . esc_attr($ad_id) . '" id="quads-ad'. esc_attr($ad_id) .'" '.$parallax_ads_data.' data-parallaxtype="'.$parallax_ad_type.'" data-position="'.$parallax_ad_type.'" data-redirect="'.esc_url($parallax_btn_url).'" style="' . $style . '">' . "\n";
        $code .='<div class="quads-parallax-ads-json"  data-json="'. esc_attr(json_encode($response)).'">';
        $code .='</div>';

        $code .='<div data-id="'.esc_attr($ad_id).'" class="quads quads_ad_container_parallax">
        
        </div>';
        $code .= '</div><div class="quads-parallax-spacer"></div>' . "\n";
        $code .= '</div>' . "\n";

        $cont = explode('<!--CusRot'.$ad_id.'-->', $content, 2);

        $content =  $cont[0].$code;
        $js_dir = QUADS_PLUGIN_URL . 'assets/js/';

        // Use minified libraries if SCRIPT_DEBUG is turned off
        $suffix = ( quadsIsDebugMode() ) ? '' : '.min';

        // These have to be global
        wp_enqueue_script( 'quads-wp-qds-parallax', $js_dir . 'wp_qds_parallax' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );

    }
}
    return  $content ;
}

function quads_parse_half_page_ads($content) {
    if(!isset($_COOKIE['quads_half_page_ads'])){
        preg_match("#<!--half_page_ad(.+?)-->#si", $content, $match);
        if (!isset($match['1'])) {           
            return $content;
        }
        $ad_id = $match['1'];
        if(!empty($ad_id)){
            $ad_meta = get_post_meta($ad_id, '',true);
        }
        $half_page_ads_type =  isset($ad_meta['half_page_ads_type'][0]) ? $ad_meta['half_page_ads_type'][0] : '';
        $half_page_ads_type_position =  isset($ad_meta['half_page_ads_type_position'][0]) ? $ad_meta['half_page_ads_type_position'][0] : '';
        $half_page_ads_type_specific_time_num =  isset($ad_meta['half_page_ads_type_specific_time_num'][0]) ? $ad_meta['half_page_ads_type_specific_time_num'][0] : '5000';
        $half_page_ads_image_src =  (isset($ad_meta['image_src'][0]) && !empty($ad_meta['image_src'][0])) ? $ad_meta['image_src'][0] : 0;
        $half_page_ads_btn_url =  (isset($ad_meta['half_page_ads_btn_url'][0]) && !empty($ad_meta['half_page_ads_btn_url'][0])) ? $ad_meta['half_page_ads_btn_url'][0] : '';
        $half_page_ads_page_vertical_text =  isset($ad_meta['half_page_ads_page_vertical_text'][0]) ? $ad_meta['half_page_ads_page_vertical_text'][0] : 'Click Here To Open/Close';
    $adsresultset = array();
    if( $ad_meta ){
        foreach ($ad_meta as $post_ad_id){
            if($post_ad_id[0] == 'half_page_ads'){
                $adsresultset[] = array(
                    'ad_id'                     => $post_ad_id,
                    'ad_type'                   => 'half_page_ads',
                ) ;
            }
        }
        $response['quads_group_id'] = $ad_id;
        $response['half_page_ads_type']           = 'half_page_ads';
        $response['half_page_ads_btn_url']            = $half_page_ads_btn_url;
        $response['half_page_ads_image_src']          = $half_page_ads_image_src;
        $response['ads'] = $adsresultset;

        // Do not create any inline style on AMP site
        $half_page_ads_data = '';
        if( $half_page_ads_type == "half_page_ads_type_specific_time_sec" ){
            if($half_page_ads_type_specific_time_num){
                $half_page_ads_data = "data-timer=".$half_page_ads_type_specific_time_num."";
            }
        }

        $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  popup Ad -->' . "\n" .
            '<div class="post_half_page_ad">
            <div class="post_half_page_openClose_box">
            <div id="post_half_page_openClose">
            <div id="post_half_pageVertical-text">'.esc_html( $half_page_ads_page_vertical_text).'</div>
            </div>
            <div class="half-page-arrow-left" id="half-page-arrow-left"></div>
            <div class="half-page-arrow-right" id="half-page-arrow-right"></div>
            </div>
            <div class="quads-location quads-half_page ad_' . esc_attr($ad_id) . '" id="quads-ad'. esc_attr($ad_id) .'" '.$half_page_ads_data.' data-half-page-type="'.$half_page_ads_type.'" data-position="'.$half_page_ads_type_position.'" data-redirect="'.esc_url($half_page_ads_btn_url).'">' . "\n";
        $code .='<div class="quads-half-page-ads-json"  data-json="'. esc_attr(json_encode($response)).'">';
        $code .='</div>';

        $code .='<div data-id="'.esc_attr($ad_id).'" class="quads quads_ad_container_half_page">
        
        </div>';
        $code .= '</div>' . "\n";
        $code .= '</div>' . "\n";

        $cont = explode('<!--CusRot'.$ad_id.'-->', $content, 2);

        $content =  $cont[0].$code;
        $js_dir = QUADS_PLUGIN_URL . 'assets/js/';

        // Use minified libraries if SCRIPT_DEBUG is turned off
        $suffix = ( quadsIsDebugMode() ) ? '' : '.min';

        // These have to be global
        wp_enqueue_script( 'quads-wp-qds-onload-ads', $js_dir . 'wp_qds_onload_ads' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );

    }
}
    return  $content ;
}

function quads_parse_default_ads_new( $content ) {
    global $quads_adsArrayCus, $quads_adsRandom, $quads_adsArray;
     
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);

    if( $off_default_ads ) { // disabled default ads
        return $content;
    }

    $number_rand_ads = substr_count( $content, '<!--CusAds' );
    for ( $i = 0; $i <= $number_rand_ads - 1; $i++ ) {
         preg_match("#<!--CusAds(.+?)-->#si", $content, $match);
         $ad_id = isset($match['1'])?$match['1']:'';
        if( strpos( $content, '<!--CusAds' . $ad_id . '-->' ) !== false )  {
            $content = quads_replace_ads_new( $content, 'CusAds' . $ad_id, $ad_id );
        }
    }
    return $content;
}

/**
 * Replace ad code in content
 * 
 * @global type $quads_options
 * @param string $content
 * @param string $quicktag Quicktag
 * @param string $id id of the ad
 * @return type
 */
function quads_replace_ads($content, $quicktag, $id) {
    global $quads_options, $quads_mode;

    if( strpos($content,'<!--'.$quicktag.'-->')===false ) { 
            return $content; 
        }

    if(isset($quads_mode) && $quads_mode == 'old'){
        if(isset($quads_options['ads']['ad'.$id]['phone']) || isset($quads_options['ads']['ad'.$id]['desktop'])){
            $get_device = function_exists('quads_check_my_device') ? quads_check_my_device() : '';
            if(isset($quads_options['ads']['ad'.$id][$get_device]) && $quads_options['ads']['ad'.$id][$get_device] == 1){
                return $content;
            }
        }
    }
        
    if ($id != -1) {
                
                $code = !empty($quads_options['ads']['ad' . $id ]['code']) ? $quads_options['ads']['ad' . $id ]['code'] : '';
                $style = quads_get_inline_ad_style($id);


        if(function_exists('quads_hide_markup') && quads_hide_markup()  ) {
            $adscode =
                "\n".'<div style="'.esc_attr($style).'">'."\n".
                quads_render_ad('ad'.$id, $code)."\n".
                '</div>'. "\n";
        }else{
            $adscode =
                "\n".'<!-- WP QUADS Content Ad Plugin v. ' . QUADS_VERSION .' -->'."\n".
                '<div class="quads-location quads-ad' .esc_html($id). '" id="quads-ad' .esc_html($id). '" style="'.esc_attr($style).'">'."\n".
                quads_render_ad('ad'.$id, $code)."\n".
                '</div>'. "\n";
        }

              
    } else {
        $adscode ='';
    }   
    $cont = explode('<!--'.$quicktag.'-->', $content, 2);
        
    return $cont[0].$adscode.$cont[1];
}

/**
 * Replace ad code in content
 * 
 * @global type $quads_options
 * @param string $content
 * @param string $quicktag Quicktag
 * @param string $id id of the ad
 * @return type
 */
function quads_replace_ads_new($content, $quicktag, $id,$ampsupport='') {
        global $quads_options;

    if( strpos($content,'<!--'.$quicktag.'-->')===false ) { 
        return $content; 
    }
    $flag = true;
    
    $ad_meta = get_post_meta($id, '',true);
    // if it was sticky ad return empty
    if (isset($ad_meta['adsense_ad_type'][0]) && $ad_meta['adsense_ad_type'][0] == 'adsense_sticky_ads' ){
        $flag = false;
    }

    if (isset($ad_meta['code'][0])&& $flag) {
        if(!empty($ad_meta['code'][0])){

            $code = '';
                if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true && strpos($ad_meta['code'][0], 'class="adsbygoogle"') !== false) {
                    $id_name = "quads-".esc_attr($id)."-place";
                    $code .= '<div id="'.esc_attr($id_name).'" class="quads-ll">' ;
                }
                if(isset($ad_meta['mobile_html_check'][0]) && $ad_meta['mobile_html_check'][0] == true && isset($ad_meta['mob_code'][0])){
                    $get_device = function_exists('quads_check_my_device') ? quads_check_my_device() : '';
                    $code .= $get_device == 'phone' ? $ad_meta['mob_code'][0] : $ad_meta['code'][0];
                }else{
                    $code .=   $ad_meta['code'][0];
                }
                if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true && strpos($ad_meta['code'][0], 'class="adsbygoogle"') !== false) {
                    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
                    $check_script_tag =    preg_match('#<script(.*?)src=(.*?)>(.*?)</script>#is', $code);
                    if($check_script_tag){
                        // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
                        $code = preg_replace('#<script(.*?)src=(.*?)>(.*?)</script>#is', '', $code);
                    }
                    $code = str_replace( 'class="adsbygoogle"', '', $code );
                    $code = str_replace( '></ins>', '><span>Loading...</span></ins></div>', $code );
                    $code1 = 'instant= new adsenseLoader( \'#quads-' . esc_attr($id) . '-place\', {
                    onLoad: function( ad ){
                        if (ad.classList.contains("quads-ll")) {
                            ad.classList.remove("quads-ll");
                        }
                      }   
                    });';
                    $code = str_replace( '(adsbygoogle = window.adsbygoogle || []).push({});', $code1, $code );
                }
        }else{
            $code ='';
        }
                $style = quads_get_inline_ad_style_new($id);
	    
        if(function_exists('quads_hide_markup') && quads_hide_markup()  ) {
            $adscode =
                "\n".'<div style="'.esc_attr($style).'">'."\n".
                quads_render_ad($ad_meta['quads_ad_old_id'][0], $code,'',$ampsupport)."\n".
                '</div>'. "\n";
        }

        elseif (isset($ad_meta['adsense_ad_type'][0]) && $ad_meta['adsense_ad_type'][0] == 'adsense_sticky_ads' ){
            $adscode = '';
        }

        elseif (isset($ad_meta['adsense_ad_type'][0]) && $ad_meta['adsense_ad_type'][0] == 'adsense_auto_ads' ){
            $adscode = '';
        }
        
        else{
            $image_banner_device_detect = $useragent = $dev_name = $output = $wpimage_quads = '';
            $wpimage_quads = "quads";
            $output = quads_render_ad( isset( $ad_meta['quads_ad_old_id'][0] ) ? $ad_meta['quads_ad_old_id'][0] : '', $code , '', $ampsupport );
            if (strpos($output, 'imagebanner') !== false) {
                $image_banner_device_detect = true;
            }
            if( $image_banner_device_detect == true ){
                $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '' ;
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
        $dev_name = 'quads-mobile';
    }else{
        $dev_name = 'quads-desktop';
    }
}

    $settings = quads_defaultSettings();
    if( isset($settings['ad_blocker_support']) && $settings['ad_blocker_support'] ){
        if( $image_banner_device_detect == true ){
            $wpimage_quads = "quads";
        }else{
            $wpimage_quads = "quads";
        }
    }

            $adscode =
                "\n".'<!-- WP QUADS Content Ad Plugin v. '.QUADS_VERSION .' -->'."\n".
                '<div class="'.$wpimage_quads.'-location quads-ad' .esc_attr($id). ' '.$dev_name.'" id="quads-ad' .esc_attr($id). '" style="'.esc_attr($style).'" data-lazydelay="'.esc_attr(quads_lazyload_delay_template($ad_meta)).'">'."\n".
                $output."\n".
                '</div>'. "\n";
        }
              
    } else {
        $adscode ='';
    }   
    $cont = explode('<!--'.$quicktag.'-->', $content, 2);
    if(isset($quads_options['tcf_2_integration']) && !empty($quads_options['tcf_2_integration']) && $quads_options['tcf_2_integration'] && function_exists( 'run_qc_choice' ) ){
        $adscode= sprintf(
              '<script type="text/plain" data-tcf="waiting-for-consent" data-id="%d">%s</script>',
              $id,
              base64_encode( $adscode )
      );
    }
    $content =  $cont[0].$adscode.$cont[1];
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    $adscode = apply_filters("wp_quads_final_ad_data", $adscode);

    return  $content;
}

/**
 * Get ad inline style
 * 
 * @global arr $quads_options
 * @param int $id id of the ad
 * @return string
 */
function quads_get_inline_ad_style( $id ) {
    global $quads_options;

    if( empty($id) ) {
        return '';
    }

    // Basic style
    $styleArray = array(
        'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
        'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
        'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
        'float:none;margin:%1$dpx;');
    
    // Alignment
    $adsalign = ( int )$quads_options['ads']['ad' . $id]['align'];
    
    // Margin
    $adsmargin = isset( $quads_options['ads']['ad' . $id]['margin'] ) ? $quads_options['ads']['ad' . $id]['margin'] : '3'; // default option = 3
    $margin = sprintf( $styleArray[$adsalign], $adsmargin );
    
    //wp_die($quads_options['ads']['ad' . $id]['margin']);
    //wp_die('ad'.$id);

    // Do not create any inline style on AMP site
    $style =   apply_filters( 'quads_filter_margins', $margin, 'ad' . $id );
    
    return $style;
}

function quads_get_inline_ad_style_new( $id ) {
    global $quads_options;

    if( empty($id) ) {
        return '';
    }
 $ad_meta = get_post_meta($id, '',true);

    // Basic style
    $styleArray = array(
        'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
        'float:none;text-align:center;',
        'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
        'float:none;margin:%1$dpx %2$dpx %3$dpx %4$dpx;');
        
    $padding_styleArray = array(
        'padding:%1$dpx %1$dpx %1$dpx 0;',
        'padding:%1$dpx 0 %1$dpx 0;',
        'padding:%1$dpx 0 %1$dpx %1$dpx;',
        'padding:%1$dpx %2$dpx %3$dpx %4$dpx;');
    
    // Alignment
    $adsalign = ( int )$ad_meta['align'][0];
    
    
    // Margin
    $adsmargin = isset( $ad_meta['margin'][0] ) ? $ad_meta['margin'][0] : '3'; // default option = 3
    $adsmargin_right = isset( $ad_meta['margin_right'][0] ) ? $ad_meta['margin_right'][0] : '3'; // default option = 3
    $adsmargin_bottom = isset( $ad_meta['margin_bottom'][0] ) ? $ad_meta['margin_bottom'][0] : '3'; // default option = 3
    $adsmargin_left = isset( $ad_meta['margin_left'][0] ) ? $ad_meta['margin_left'][0] : '3'; // default option = 3
    $margin = sprintf( $styleArray[$adsalign], $adsmargin, $adsmargin_right, $adsmargin_bottom, $adsmargin_left );

    // Padding
    $adspadding = isset( $ad_meta['padding'][0] ) ? $ad_meta['padding'][0] : '0'; // default option = 0
    $adspadding_right = isset( $ad_meta['padding_right'][0] ) ? $ad_meta['padding_right'][0] : '0'; // default option = 0
    $adspadding_bottom = isset( $ad_meta['padding_bottom'][0] ) ? $ad_meta['padding_bottom'][0] : '0'; // default option = 0
    $adspadding_left = isset( $ad_meta['padding_left'][0] ) ? $ad_meta['padding_left'][0] : '0'; // default option = 0
    $padding = sprintf( $padding_styleArray[$adsalign], $adspadding, $adspadding_right, $adspadding_bottom, $adspadding_left );
    
    // Do not create any inline style on AMP site
    $style =  apply_filters( 'quads_filter_margins', $margin, 'ad' . $id ) ;
    
    return $style.$padding;
}

/**
 * Revert content to original content any remove any processing helper strings
 * 
 * @global int $quads_visibleContentAds
 * @global array $adsArray
 * @global array $quads_options
 * @global int $ad_count
 * @param string $content
 * @param boolean $trimonly
 * 
 * @return string content
 */
function quads_clean_tags($content, $trimonly = false) {
    global $quads_visibleContentAds;
    global $quads_adsArray;
        global $quads_options;
        global $ad_count;
        
    $tagnames = array('EmptyClear','RndAds','NoAds','OffDef','OffAds','OffWidget','OffBegin','OffMiddle','OffEnd','OffBfMore','OffAfLastPara','CusRnd');

        for($i=1;$i<=10;$i++) { 
            array_push($tagnames, 'CusAds'.$i); 
            array_push($tagnames, 'Ads'.$i); 
        };
        
        
    foreach ($tagnames as $tags) {
        if(strpos($content,'<!--'.$tags.'-->')!==false || $tags=='EmptyClear') {
            if($trimonly) {
                $content = str_replace('<p><!--'.$tags.'--></p>', '<!--'.$tags.'-->', $content);    
            }else{
                $content = str_replace(array('<p><!--'.$tags.'--></p>','<!--'.$tags.'-->'), '', $content);  
                $content = str_replace("##QA-TP1##", "<p></p>", $content);
                $content = str_replace("##QA-TP2##", "<p>&nbsp;</p>", $content);
            }
        }
    }
    if(!$trimonly && (is_single() || is_page()) ) {
        $quads_visibleContentAds = 0;
        $quads_adsArray = array();
    }   
    return $content;
}



/**
 * Remove element from array
 * 
 * @param array $paragraphsArrayay
 * @param int $idx key to remove from array
 * @return array
 */
function quads_del_element($array, $idx) {
  $copy = array();
    for( $i=0; $i<count($array) ;$i++) {
        if ( $idx != $i ) {
            array_push($copy, $array[$i]);
        }
    }   
  return $copy;
}

/**
     * echo ad before/after posts in loops on archive pages
     *
     * @since 1.2.1
     * @param arr $post post object
     * @param WP_Query $wp_query query object
     */
     function quads_in_between_loop( $post, $wp_query = null ) {
       global $quads_new_interface_ads;

        $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

        if ( ! $wp_query instanceof WP_Query || is_feed() || ( is_admin() && ! $is_ajax )  ) {
            return;
        }

        if( ! isset( $wp_query->current_post )) {
            return;
        };

        // don’t inject into main query on single pages.
        if( $wp_query->is_main_query() && is_single() ){
            return;
        }
        if ( $wp_query->is_singular() || ! $wp_query->in_the_loop   ) {
                return;
        }

        // check if the loop is outside of wp_head, but only on non-AJAX calls.
        if  ( ! is_admin() && ! did_action( 'wp_head' ) ) {
            return;
        }


        $curr_index = $wp_query->current_post ; // normalize index
        static $handled_indexes = array();
        if ( $wp_query->is_main_query() ) {
            if ( in_array( $curr_index, $handled_indexes ) ) {
                return;
            }
            $handled_indexes[] = $curr_index;
        }
        if(empty($quads_new_interface_ads)){
          if(isset($quads_ads) && !empty($quads_ads))
          {
            $quads_new_interface_ads = $quads_ads;
          }
          else
          {
            $quads_ads = quads_api_services_cllbck();
          }
          
        }else{
          $quads_ads = $quads_new_interface_ads;
        }

        if(isset($quads_ads['posts_data'])){        
            foreach($quads_ads['posts_data'] as $key => $value){
                $ads =$value['post_meta'];
                if($value['post']['post_status']== 'draft'){
                    continue;
                }
                 $display_after_every = (isset($ads['display_after_every']) && !empty($ads['display_after_every'])) ? $ads['display_after_every'] : false;
                if( isset($ads['position'] ) && $ads['position'] == 'amp_ads_in_loops' && (isset($ads['ads_loop_number']) && ($ads['ads_loop_number'] == $curr_index || ($display_after_every && $curr_index!== 0 && ($curr_index % $ads['ads_loop_number'] == 0))))){
                    $tag= '<!--CusAds'.$ads['ad_id'].'-->';
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
                    if($is_on && $is_visitor_on ){
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done is quads_replace_ads_new() function
                        echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );

                    }
                }

            }
        }
    }

     function quads_background_ad(){
        if(!is_admin()){   
          ob_start( "quads_background_ad_last");  
        }

    }


     function quads_background_ad_last($content){

        $quads_ads = quads_api_services_cllbck();
        if(isset($quads_ads['posts_data'])){        
            foreach($quads_ads['posts_data'] as $key => $value){
                $ads =$value['post_meta'];
                if($value['post']['post_status']== 'draft'){
                    continue;
                }

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
            if(isset($ads['ad_id']))
            $post_status = get_post_status($ads['ad_id']); 
            else
              $post_status =  'publish';

            if(!isset($ads['position']) || isset($ads['ad_type']) && $ads['ad_type']== 'random_ads'){
                
                $is_on = true;
            }           
            if( isset($ads['position']) && $ads['position'] == 'ad_sticky_ad' ){
                $is_on = false;
            }
            if($is_on && $is_visitor_on && $post_status=='publish'){
                if(isset($ads['ad_type']) && $ads['ad_type'] == 'background_ad'){

                      $after_body='<div class="quads-bg-wrapper">
                   <a style="background-image: url('.esc_url($ads['image_src']).')" class="quads-bg-ad" target="_blank" href="'.esc_url($ads['image_redirect_url']).'">'
                . '</a>'                               
                . '<div class="quads-bg-content">';   
                $style=' <style>     .quads-bg-ad{                             
                                      position: absolute;
                                      top: 0;
                                      left: 0;
                                      height: 100%;
                                      width: 100%;
                                      background-position: center;
                                      background-repeat: no-repeat;
                                      background-size: cover;
                               }
                              .quads-bg-content{
                                margin: auto;
                                position: inherit;
                                top: 0;
                                left: 0;
                                bottom: 0;
                                right: 0;
                               }
                               .h_m{
                                 z-index: 1;
                                 position: relative;
                               }
                               .content-wrapper{
                                   position: relative;
                                   z-index: 0;
                                   margin: 0 16%
                               }
                               .cntr, .amp-wp-article{
                                  background:#ffffff;
                               }
                               .footer{
                                  background:#ffffff;
                               }
                              @media(max-width:768px){
                                 .quads-bg-ad{
                                   position:relative;
                                 }
                                 .content-wrapper{
                                   margin:0;
                                 }
                               }</style>';
                  $before_body = $style.'</div></div>';
                  $content = preg_replace("/(\<body.*\>)/", $before_body."$1".$after_body, $content);
                } else if(isset($ads['ad_type']) && $ads['ad_type'] == 'skip_ads'){
                
                    if(!isset($_COOKIE['skip_ads_delay'])) {
                        setcookie('skip_ads_delay', esc_attr($ads['freq_page_view']),-1, "/"); // 86400 = 1 day
                    }else{
                        if($_COOKIE['skip_ads_delay'] != 0){
                            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                            $delay = absint( wp_unslash( $_COOKIE['skip_ads_delay'] ) );
                            setcookie( 'skip_ads_delay', esc_attr( $delay - 1 ), -1, "/" ); // 86400 = 1 day
                            return $content;
                        }
    
                        }
                        global $quads_options;
                    
                    $html = '<div style="bottom: 0px; height: 8px; background: rgb(210, 210, 210);" id="progressContainer" class="progressContainer">
                    <div id="progressAd" class="progressAd" style="background-color: rgb(221, 51, 51); width: 0%; height: 8px;"></div>
                    </div>
                    
                    <div style="background-color:#212121;" id="progressModal" class="progressModal">
                            <span class="pClose" style="right:0.8rem;bottom:1.2rem;background-color:#000;color:#ffffff" id="progressSkipper">Please wait..</span>
                            <div class="progresContentArea">';
                    if(isset($ads['skip_ads_type'])  && $ads['skip_ads_type'] == 'image_banner' ){

                        $add_nofollow = (isset($ads['add_url_nofollow']) && $ads['add_url_nofollow'])?true:false;
                        if(isset($ads['image_redirect_url'])  && !empty($ads['image_redirect_url'])){
                            $html .= '
                            <a target="_blank" href="'.esc_url($ads['image_redirect_url']). '" '.($add_nofollow?'rel=nofollow':'').'>
                            <img class="aligncenter" '.(quads_is_lazyload_template($quads_options,$ads) ? 'src="data:image/svg+xml,%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20viewBox=\'0%200%20480%20270\'%3E%3C/svg%3E" data-src' : 'src').'="'.esc_url($ads['image_src']). '" data-lazydelay="'.esc_attr(quads_lazyload_delay_template($ads)).'"> 
                            </a>';
                        }else{
                            $html .= '<img class="aligncenter" '.(quads_is_lazyload_template($quads_options,$ads) ? 'src="data:image/svg+xml,%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20viewBox=\'0%200%20480%20270\'%3E%3C/svg%3E" data-src' : 'src').'="'.esc_url($ads['image_src']). '" data-lazydelay="'.esc_attr(quads_lazyload_delay_template($ads)).'">';
                        }
                    }else{
                        $html .= $ads['code'];
                    }
                  
                    $html .= '</div>
                    </div>
                    <script>
                    
                    if (typeof quadsgetCookie !== "function"){

                        function quadsgetCookie(cname) {
                            var name = cname + "=";
                            var ca = document.cookie.split(";");
                            for (var i = 0; i < ca.length; i++) {
                                var c = ca[i].trim();
                                if (c.indexOf(name) === 0) {
                                    return c.substring(name.length, c.length);
                                }
                            }
                            return false;
                        }
                    }
                    if (typeof quadssetCookie !== "function") {
                    
                        function quadssetCookie(cName, cValue, exdays, path) {
                            var d = new Date();
                            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                            var expires = "expires=" + d.toUTCString();
                            document.cookie = cName + "=" + cValue + "; " + expires + "; path=/";
                        }
                    }



                    function updateDCPAProgress(top, bottom, col1, col2, range, time, skip, remaining, type, modal, adstime, afterads) {
                          var selectRange = range;
                          var selectRange2 = selectRange + 30;
                          
                          var percent = Math.ceil(top / bottom * 100) + "%";
                          var normal = Math.ceil(top / bottom * 100);
                          
                          document.getElementById("progressAd").style.width = percent;
                              
                              if (normal >= selectRange && normal <= selectRange2) { //if in range
                    
                                    const element = document.querySelector("#progressModal");
                                    if(element.classList.contains("active") == false && element.classList.contains("clicked") == false){
                                        //if in ads
                                        element.classList.add("active");
                                        document.body.style.overflow = "hidden";
                                        document.querySelector("#progressAd").style.backgroundColor = col2;
                                    
                                        if (type == 2 ) {
                                            //if youtube style ad button
                                            var timeleft = time;
                                            var downloadTimer = setInterval(function(){
                                              document.getElementById("progressSkipper").innerHTML = timeleft + " " + remaining;
                                              timeleft--;
                                              if(timeleft == -2){
                                                clearInterval(downloadTimer);
                                                document.getElementById("progressSkipper").innerHTML = skip;
                                                    document.querySelector(".pClose").onclick = function() {
                                                        var count_skip =quadsgetCookie("skip_ads_delay");
                                                        quadssetCookie("skip_ads_delay",count_skip -1, 30, "/");
                    
                                                        document.querySelector("#progressContainer").style.display = "none";
                                                        element.classList.remove("active");
                                                        element.classList.add("clicked");
                                                        document.body.style.overflow = "visible";
                                                        document.querySelector("#progressAd").style.backgroundColor = col1;
                                                    }
                                              }
                                            }, 1000);
                                        }
                                    }
                                }
                    }
                    window.addEventListener("scroll", function () {
                        var top = window.scrollY;
                        var height = document.body.getBoundingClientRect().height - window.innerHeight;
                        var color1 = "#dd3333";
                        var color2 = "#eff700";
                        var type = 2;
                        var range = 30;
                        var modal = 1;
                        var time = '.esc_attr(isset($ads['ad_wt_time'])?$ads['ad_wt_time'] : 5). ';
                        var skip = "Skip Ad >";
                        var remaining = "seconds remaining";
                        var freq = 0;
                        var afterads = 1;
                        updateDCPAProgress(top, height, color1, color2, range, time, skip, remaining, type, modal, freq, afterads);
                      });</script>
                    <style>
                    #progressCloser{z-index:999999;font-family:Arial;font-size:21px;position:absolute;cursor:pointer;padding:4px 11px;text-align:center;border-radius:100%}#progressSkipper{z-index:999999;font-family:Arial;font-size:21px;position:absolute;cursor:pointer;padding:8px 12px 8px;border:1px solid #484848;text-align:center;}.progressModal{z-index:999998;padding:2rem 4rem 2rem;background-color:#000;visibility:hidden;opacity:0;transition:opacity .5s,visibility 0s .5s}@media (max-width :768px){.progressModal{padding:2rem 1rem 1rem}}.progressModal.active{opacity:1;overflow-y:scroll!important;visibility:visible;transition:opacity .5s}.progresContentArea{padding:.4rem}.progressContainer{z-index:999999;position:fixed;left:0;width:100%}.progressAd{z-index:999999;transition:width .5s}.progressAdcontent,.progressModal{position:fixed;max-height:100%;overflow-y:auto;overflow:hidden;top:0;left:0;height:100%;width:100%}.progressAdcontent{z-index:999998}@keyframes progMove{from{background-position:0 0}to{background-position:220px 0}}.progressAd2{z-index:999999;float:left;box-sizing:border-box;background-size:40px 40px;border-radius:10px 0 0 10px;background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.2) 30%,rgba(0,0,0,.1) 30%,rgba(0,0,0,.1) 33%,transparent 33%,transparent 46%,rgba(0,0,0,.1) 46%,rgba(0,0,0,.1) 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 80%,rgba(0,0,0,.1) 80%,rgba(0,0,0,.1) 83%,transparent 83%,transparent 97%,rgba(0,0,0,.1) 97%,rgba(0,0,0,.1));background-image:linear-gradient(45deg,rgba(255,255,255,.2) 30%,rgba(0,0,0,.1) 30%,rgba(0,0,0,.1) 34%,transparent 34%,transparent 46%,rgba(0,0,0,.1) 46%,rgba(0,0,0,.1) 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 80%,rgba(0,0,0,.1) 80%,rgba(0,0,0,.1) 84%,transparent 84%,transparent 96%,rgba(0,0,0,.1) 96%,rgba(0,0,0,.1));-webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,.1);-moz-box-shadow:inset 0 -1px 0 rgba(0,0,0,.1);box-shadow:inset 0 -1px 0 rgba(0,0,0,.1);-webkit-transition:width .2s ease;-moz-transition:width .2s ease;-o-transition:width .2s ease;transition:width .2s ease}.progressAd3{z-index:999999;-webkit-border-radius:3px;-moz-border-radius:3px;-ms-border-radius:3px;-o-border-radius:3px;border-radius:3px;-webkit-box-shadow:inset 0 3px 5px 0 rgba(0,0,0,.2);-moz-box-shadow:inset 0 3px 5px 0 rgba(0,0,0,.2);box-shadow:inset 0 3px 5px 0 rgba(0,0,0,.2);background-image:-webkit-gradient(linear,0 0,100% 100%,color-stop(.25,rgba(255,255,255,.2)),color-stop(.25,transparent),color-stop(.5,transparent),color-stop(.5,rgba(255,255,255,.2)),color-stop(.75,rgba(255,255,255,.2)),color-stop(.75,transparent),to(transparent));background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);background-image:-moz-linear-gradient(45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);background-image:-ms-linear-gradient(45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);-webkit-background-size:45px 45px;-moz-background-size:45px 45px;-o-background-size:45px 45px;background-size:45px 45px}.progressAd4{z-index:999999;animation:progMove 4s linear infinite;-moz-animation:progMove 4s linear infinite;-webkit-animation:progMove 4s linear infinite;-o-animation:progMove 4s linear infinite;-webkit-border-radius:3px;-moz-border-radius:3px;-ms-border-radius:3px;-o-border-radius:3px;border-radius:3px;-webkit-box-shadow:inset 0 3px 5px 0 rgba(0,0,0,.2);-moz-box-shadow:inset 0 3px 5px 0 rgba(0,0,0,.2);box-shadow:inset 0 3px 5px 0 rgba(0,0,0,.2);background-image:-webkit-gradient(linear,0 0,100% 100%,color-stop(.25,rgba(255,255,255,.2)),color-stop(.25,transparent),color-stop(.5,transparent),color-stop(.5,rgba(255,255,255,.2)),color-stop(.75,rgba(255,255,255,.2)),color-stop(.75,transparent),to(transparent));background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);background-image:-moz-linear-gradient(45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);background-image:-ms-linear-gradient(45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);-webkit-background-size:45px 45px;-moz-background-size:45px 45px;-o-background-size:45px 45px;background-size:45px 45px}.progressAd5{z-index:999999;background-image:-webkit-linear-gradient(-45deg,transparent 33%,rgba(0,0,0,.1) 33%,rgba(0,0,0,.1) 55%,transparent 55%),-webkit-linear-gradient(top,rgba(255,255,255,.25),rgba(0,0,0,.25)),-webkit-linear-gradient(left,#09c,#f44);border-radius:2px;background-size:35px 20px,100% 100%,100% 100%}.progressAd6{z-index:999999;background-color:#fff;background-image:url("data:image/svg+xml,%3Csvg width="40" height="12" viewBox="0 0 40 12" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M0 6.172L6.172 0h5.656L0 11.828V6.172zm40 5.656L28.172 0h5.656L40 6.172v5.656zM6.172 12l12-12h3.656l12 12h-5.656L20 3.828 11.828 12H6.172zm12 0L20 10.172 21.828 12h-3.656z" fill="%23008386" fill-opacity="0.7" fill-rule="evenodd"/%3E%3C/svg%3E")!important}.progressAd7{z-index:999999;background-color:#383838;background-image:url("data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23e6afff" fill-opacity="1" fill-rule="evenodd"/%3E%3C/svg%3E")!important}.progressAd8{z-index:999999;background-color:#72deff;background-image:url("data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 80 80"%3E%3Cg fill="%2392278f" fill-opacity="0.71"%3E%3Cpath fill-rule="evenodd" d="M0 0h40v40H0V0zm40 40h40v40H40V40zm0-40h2l-2 2V0zm0 4l4-4h2l-6 6V4zm0 4l8-8h2L40 10V8zm0 4L52 0h2L40 14v-2zm0 4L56 0h2L40 18v-2zm0 4L60 0h2L40 22v-2zm0 4L64 0h2L40 26v-2zm0 4L68 0h2L40 30v-2zm0 4L72 0h2L40 34v-2zm0 4L76 0h2L40 38v-2zm0 4L80 0v2L42 40h-2zm4 0L80 4v2L46 40h-2zm4 0L80 8v2L50 40h-2zm4 0l28-28v2L54 40h-2zm4 0l24-24v2L58 40h-2zm4 0l20-20v2L62 40h-2zm4 0l16-16v2L66 40h-2zm4 0l12-12v2L70 40h-2zm4 0l8-8v2l-6 6h-2zm4 0l4-4v2l-2 2h-2z"/%3E%3C/g%3E%3C/svg%3E")!important}.progressAd9{z-index:999999;background-color:#585858;background-image:url("data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 56 28" width="56" height="28"%3E%3Cpath fill="%23f0d519" fill-opacity="0.89" d="M56 26v2h-7.75c2.3-1.27 4.94-2 7.75-2zm-26 2a2 2 0 1 0-4 0h-4.09A25.98 25.98 0 0 0 0 16v-2c.67 0 1.34.02 2 .07V14a2 2 0 0 0-2-2v-2a4 4 0 0 1 3.98 3.6 28.09 28.09 0 0 1 2.8-3.86A8 8 0 0 0 0 6V4a9.99 9.99 0 0 1 8.17 4.23c.94-.95 1.96-1.83 3.03-2.63A13.98 13.98 0 0 0 0 0h7.75c2 1.1 3.73 2.63 5.1 4.45 1.12-.72 2.3-1.37 3.53-1.93A20.1 20.1 0 0 0 14.28 0h2.7c.45.56.88 1.14 1.29 1.74 1.3-.48 2.63-.87 4-1.15-.11-.2-.23-.4-.36-.59H26v.07a28.4 28.4 0 0 1 4 0V0h4.09l-.37.59c1.38.28 2.72.67 4.01 1.15.4-.6.84-1.18 1.3-1.74h2.69a20.1 20.1 0 0 0-2.1 2.52c1.23.56 2.41 1.2 3.54 1.93A16.08 16.08 0 0 1 48.25 0H56c-4.58 0-8.65 2.2-11.2 5.6 1.07.8 2.09 1.68 3.03 2.63A9.99 9.99 0 0 1 56 4v2a8 8 0 0 0-6.77 3.74c1.03 1.2 1.97 2.5 2.79 3.86A4 4 0 0 1 56 10v2a2 2 0 0 0-2 2.07 28.4 28.4 0 0 1 2-.07v2c-9.2 0-17.3 4.78-21.91 12H30zM7.75 28H0v-2c2.81 0 5.46.73 7.75 2zM56 20v2c-5.6 0-10.65 2.3-14.28 6h-2.7c4.04-4.89 10.15-8 16.98-8zm-39.03 8h-2.69C10.65 24.3 5.6 22 0 22v-2c6.83 0 12.94 3.11 16.97 8zm15.01-.4a28.09 28.09 0 0 1 2.8-3.86 8 8 0 0 0-13.55 0c1.03 1.2 1.97 2.5 2.79 3.86a4 4 0 0 1 7.96 0zm14.29-11.86c1.3-.48 2.63-.87 4-1.15a25.99 25.99 0 0 0-44.55 0c1.38.28 2.72.67 4.01 1.15a21.98 21.98 0 0 1 36.54 0zm-5.43 2.71c1.13-.72 2.3-1.37 3.54-1.93a19.98 19.98 0 0 0-32.76 0c1.23.56 2.41 1.2 3.54 1.93a15.98 15.98 0 0 1 25.68 0zm-4.67 3.78c.94-.95 1.96-1.83 3.03-2.63a13.98 13.98 0 0 0-22.4 0c1.07.8 2.09 1.68 3.03 2.63a9.99 9.99 0 0 1 16.34 0z"%3E%3C/path%3E%3C/svg%3E")!important}.progressAd10{z-index:999999;background-color:#f36b6b;background-image:url("data:image/svg+xml,%3Csvg width="100" height="20" viewBox="0 0 100 20" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M21.184 20c.357-.13.72-.264 1.088-.402l1.768-.661C33.64 15.347 39.647 14 50 14c10.271 0 15.362 1.222 24.629 4.928.955.383 1.869.74 2.75 1.072h6.225c-2.51-.73-5.139-1.691-8.233-2.928C65.888 13.278 60.562 12 50 12c-10.626 0-16.855 1.397-26.66 5.063l-1.767.662c-2.475.923-4.66 1.674-6.724 2.275h6.335zm0-20C13.258 2.892 8.077 4 0 4V2c5.744 0 9.951-.574 14.85-2h6.334zM77.38 0C85.239 2.966 90.502 4 100 4V2c-6.842 0-11.386-.542-16.396-2h-6.225zM0 14c8.44 0 13.718-1.21 22.272-4.402l1.768-.661C33.64 5.347 39.647 4 50 4c10.271 0 15.362 1.222 24.629 4.928C84.112 12.722 89.438 14 100 14v-2c-10.271 0-15.362-1.222-24.629-4.928C65.888 3.278 60.562 2 50 2 39.374 2 33.145 3.397 23.34 7.063l-1.767.662C13.223 10.84 8.163 12 0 12v2z" fill="%230d37c2" fill-opacity="0.4" fill-rule="evenodd"/%3E%3C/svg%3E")!important}.progressAd11{z-index:999999;background-color:#f3e092;background-image:url("data:image/svg+xml,%3Csvg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%238fe1e7" fill-opacity="1" fill-rule="evenodd"%3E%3Cpath d="M0 40L40 0H20L0 20M40 40V20L20 40"/%3E%3C/g%3E%3C/svg%3E")!important}.progresContentArea .alignnone { margin: 5px 20px 20px 0; } .progresContentArea .aligncenter, .progresContentArea div.aligncenter { display: block; margin: 5px auto 5px auto; } .progresContentArea .alignright { float:right; margin: 5px 0 20px 20px; } .progresContentArea .alignleft { float: left; margin: 5px 20px 20px 0; } .progresContentArea a img.alignright { float: right; margin: 5px 0 20px 20px; } .progresContentArea a img.alignnone { margin: 5px 20px 20px 0; } .progresContentArea a img.alignleft { float: left; margin: 5px 20px 20px 0; } .progresContentArea a img.aligncenter { display: block; margin-left: auto; margin-right: auto; } .progresContentArea .wp-caption { background: #fff; border: 1px solid #f0f0f0; max-width: 96%; padding: 5px 3px 10px; text-align: center; } .progresContentArea .wp-caption.alignnone { margin: 5px 20px 20px 0; } .progresContentArea .wp-caption.alignleft { margin: 5px 20px 20px 0; } .progresContentArea .wp-caption.alignright { margin: 5px 0 20px 20px; } .progresContentArea .wp-caption img { border: 0 none; height: auto; margin: 0; max-width: 98.5%; padding: 0; width: auto; } .progresContentArea .wp-caption p.wp-caption-text { font-size: 11px; line-height: 17px; margin: 0; padding: 0 4px 5px; }
                    </style>';
                    $content = preg_replace("/(\<body.*\>)/", $html."$1".$after_body, $content);

                }

              }

            }
        }
           return $content;
        }

        function quads_search_and_archive_ads(){
            if(!(is_search() || is_archive())){
                return '';
            }
            $quads_ads = quads_api_services_cllbck();
            if(isset($quads_ads['posts_data'])){        
                foreach($quads_ads['posts_data'] as $key => $value){
                    $ads =$value['post_meta'];
                    if($value['post']['post_status']== 'draft'){
                        continue;
                    }
                    if(isset($ads['position']) && $ads['position'] == 'ad_shortcode'){
                        continue;
                    }
    
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
                if(isset($ads['ad_id']))
                $post_status = get_post_status($ads['ad_id']); 
                else
                  $post_status =  'publish';
    
                if(!isset($ads['position']) || isset($ads['ad_type']) && $ads['ad_type']== 'random_ads'){
                    
                    $is_on = true;
                } 
                if($is_on && $is_visitor_on && $post_status=='publish' && isset($ads['visibility_include'][0]['value']['value']) && $ads['visibility_include'][0]['value']['value']=='show_globally'){
                    if(in_array($ads['ad_type'],array('ad_image','adsense','plain_text'))){
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo "\n".'<!-- WP QUADS Content Ad Plugin v. ' . QUADS_VERSION .' -->'."\n"
                        .'<div class="quads-location quads-ad' .esc_html($ads['ad_id']). '" id="quads-ad' .esc_html($ads['ad_id']). '">'."\n"
                        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad() function
                        .quads_render_ad($ads['quads_ad_old_id'], $ads['code'])."\n"
                        .'</div>'. "\n";
                    } 
                  }
    
                }
            }
            }
          
            
            
            
            
            
function quads_remove_ad_from_content($content,$ads,$ads_data='',$position='',$repeat_paragraph=false){

    $wp_charset = get_bloginfo( 'charset' );
    $tag = '//p[not(parent::blockquote) and not(parent::table)]';
    $offsets = array();
    $paragraphs = array();
    $doc =  new DOMDocument( '1.0', $wp_charset );
    libxml_use_internal_errors( true );
     if($content)
     {
        // Wrap all <!--shortcodes-->  in div to prevent them from being moved
        if (preg_match_all('/<!--(\w+)(.*?)-->/', $content, $matches)) {
            foreach ($matches[0] as $key => $shortcode) {
                $wrapped_shortcode = '<div> ' . $shortcode . ' </div>';
                $content = str_replace($shortcode, $wrapped_shortcode, $content);
            }
        }
        if (strpos($content, 'ewd-ufaq-faq-list') !== false) {
            $content = preg_replace('/<html\b[^>]*>|<\/html>|<body\b[^>]*>|<\/body>/i', '', $content);
            $content = '<div>' . $content . '</div>';
        }
        $doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
     }
     else
     {
        return '';
     }
     libxml_clear_errors();
      $xpath = new DOMXPath( $doc );
      
      $items = $xpath->query( $tag );
      
      if($items->length == 0) {
          $items = $xpath->query( '//p' );
          
          if($items->length == 0) {
              $items = $xpath->query( '/html/body/p' );
          }
          
          if($items->length == 0) {
              $items = $xpath->query( '//body//p' );
          }
      }
      
      $whitespaces = json_decode( '"\t\n\r \u00A0"' );
      foreach ( $items  as $item) {
        if (  ( isset( $item->textContent ) && trim( $item->textContent, $whitespaces ) !== '' ) ) { 
          $paragraphs[] = $item;
        }
      }
      $total_paragraphs = count($paragraphs);
      
      $position = 0; // Initialize position
      if(isset($ads_data['after_the_percentage_value']) && !empty($ads_data['after_the_percentage_value'])){
        $percentage = intval($ads_data['after_the_percentage_value']);
        $position = floor(($percentage / 100) * $total_paragraphs);
        
        // Ensure position is within bounds
        if($position < 0) {
            $position = 0;
        }
        if($position >= $total_paragraphs) {
            $position = max(0, $total_paragraphs - 1);
        }
      }
     
if($repeat_paragraph && isset($ads_data['after_the_percentage_value']) && !empty($ads_data['after_the_percentage_value'])){
      for ( $i = $position -1; $i < $total_paragraphs; $i++ ) {
        // Select every X number.
        if ( $position > 0 && ( $i + 1 ) % $position === 0 )  {
          $offsets[] = $i;
        }
      }
                               foreach ( $offsets as $offset ) {

                        $ref_node  = $paragraphs[$offset]->nextSibling;
                        $quick_tags_pattern = '/<!--CusAds\d+-->/';
                        
                        // Handle placeholder comments differently from actual HTML
                        if (preg_match($quick_tags_pattern, $ads)) {
                            // For placeholder comments, create a comment node directly
                            $commentNode = $doc->createComment(str_replace(array('<!--', '-->'), '', $ads));
                            if ($ref_node) {
                                $ref_node->parentNode->insertBefore($commentNode, $ref_node);
                            } else {
                                $paragraphs[$offset]->parentNode->appendChild($commentNode);
                            }
                        } else {
                            // For actual HTML (like carousel ads), wrap and extract properly
                            $ad_dom =  new DOMDocument( '1.0', $wp_charset );
                            libxml_use_internal_errors( true );
                            // Wrap in html/body to preserve nested div structure
                            $ad_dom->loadHTML(mb_convert_encoding('<!DOCTYPE html><html><body>' . $ads . '</body></html>', 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                            libxml_clear_errors();
                            
                            // Extract nodes from body element to preserve nested structure
                            $body = $ad_dom->getElementsByTagName('body')->item(0);
                            $importedNodes = [];
                            if ($body) {
                                // Import all child nodes from body (preserves nested divs)
                                foreach ($body->childNodes as $importedNode) {
                                    if ($importedNode->nodeType === XML_ELEMENT_NODE) {
                                        $importedNodes[] = $doc->importNode($importedNode, true);
                                    }
                                }
                            } else {
                                // Fallback: import direct child nodes
                                foreach ($ad_dom->childNodes as $importedNode) {
                                    if ($importedNode->nodeType === XML_ELEMENT_NODE) {
                                        $importedNodes[] = $doc->importNode($importedNode, true);
                                    }
                                }
                            }
                        
                            foreach ($importedNodes as $importedNode) {
                                if ($ref_node) {
                                    $ref_node->parentNode->insertBefore($importedNode, $ref_node);
                                } else {
                                    // If no nextSibling, append to parent
                                    $paragraphs[$offset]->parentNode->appendChild($importedNode);
                                }
                            }
                        }
}
    }else{
        // Check if position is valid and percentage value exists
        if (isset($ads_data['after_the_percentage_value']) && !empty($ads_data['after_the_percentage_value']) && isset($paragraphs[$position]) && $position >= 0) {
            // Insert AFTER the paragraph, not before
            $ref_node = $paragraphs[$position]->nextSibling;
            
            $quick_tags_pattern = '/<!--CusAds\d+-->/';
            
            // Handle placeholder comments differently from actual HTML
            if (preg_match($quick_tags_pattern, $ads)) {
                // For placeholder comments, create a comment node directly
                $commentNode = $doc->createComment(str_replace(array('<!--', '-->'), '', $ads));
                if ($ref_node) {
                    $ref_node->parentNode->insertBefore($commentNode, $ref_node);
                } else {
                    $paragraphs[$position]->parentNode->appendChild($commentNode);
                }
            } else {
                // For actual HTML (like carousel ads), wrap and extract properly
                $ad_dom = new DOMDocument('1.0', $wp_charset);
                libxml_use_internal_errors(true);
                // Wrap in html/body to preserve nested div structure
                $ad_dom->loadHTML(mb_convert_encoding('<!DOCTYPE html><html><body>' . $ads . '</body></html>', 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                libxml_clear_errors();
                
                // Extract nodes from body element to preserve nested structure
                $body = $ad_dom->getElementsByTagName('body')->item(0);
                $importedNodes = [];
                if ($body) {
                    // Import all child nodes from body (preserves nested divs)
                    foreach ($body->childNodes as $importedNode) {
                        if ($importedNode->nodeType === XML_ELEMENT_NODE) {
                            $importedNodes[] = $doc->importNode($importedNode, true);
                        }
                    }
                } else {
                    // Fallback: import direct child nodes
                    foreach ($ad_dom->childNodes as $importedNode) {
                        if ($importedNode->nodeType === XML_ELEMENT_NODE) {
                            $importedNodes[] = $doc->importNode($importedNode, true);
                        }
                    }
                }
            
                foreach ($importedNodes as $importedNode) {
                    if ($ref_node) {
                        $ref_node->parentNode->insertBefore($importedNode, $ref_node);
                    } else {
                        // If no nextSibling, append to parent
                        $paragraphs[$position]->parentNode->appendChild($importedNode);
                    }
                }
            }
        }
        
    }
    $content =$doc->saveHTML();
    return $content;  
}

function quads_after_id_class_ad_creator($content,$srch_name,$type_name){

    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);
    if(function_exists('mb_convert_encoding')){
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    }else{
        $dom->loadHTML( $content , LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    }
    libxml_clear_errors();
    $finder = new DomXPath($dom);
    if($type_name == 'id'){
        $after_ad = 'afterIdAd';
        $sel_data = $finder->query("//*[contains(@id, $srch_name)]");
    }
    if($type_name == 'class'){
        $after_ad = 'afterClassAd';
        $sel_data = $finder->query("//*[contains(@class, $srch_name)]");
    }
    if($type_name == 'custom'){
        $after_ad = 'afterCustomQAd';
        $sel_data = $finder->query($srch_name);
    }
    for ($i=0; $i < $sel_data->length ; $i++) {
        if($sel_data){
            $p = $sel_data->item($i);
            $div = $dom->createTextNode($after_ad);
            if($p->nextSibling === null) {
                $p->parentNode->appendChild($div);
            } else {
                $p->parentNode->insertBefore($div, $p->nextSibling);
            }
        }
    }

    $content = $dom->saveHTML();

    return $content;

} 

function quads_parse_floating_cubes_ads() {
    $quads_ads = quads_api_services_cllbck();
    if(isset($quads_ads['posts_data'])){        
        foreach($quads_ads['posts_data'] as $key => $value){
            $ads =$value['post_meta'];
            if($value['post']['post_status']== 'draft'){
                continue;
            }
            if((isset($ads['position']) && $ads['position'] == 'ad_shortcode') || (isset($ads['ad_type']) && $ads['ad_type'] !='floating_cubes')){
                continue;
            }

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
        if(isset($ads['ad_id']))
        $post_status = get_post_status($ads['ad_id']); 
        else
          $post_status =  'publish';

        if( $is_on && $is_visitor_on && $post_status=='publish' ){
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo "\n".'<!-- WP QUADS Content Ad Plugin v. ' . QUADS_VERSION .' -->'."\n"
                .'<div class="quads-location quads-ad' .esc_html( $ads['ad_id'] ). '" id="quads-ad' .esc_html( $ads['ad_id'] ). '">'."\n"
                //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_render_ad() function
                .quads_render_ad( $ads['quads_ad_old_id'], $ads['code'] )."\n"
                .'</div>'. "\n";
          }

        }
    }
}
function quads_is_lazyload_template($options, $ads){
    if((function_exists('quads_delay_ad_sec') && quads_delay_ad_sec()) || (isset($ads['check_lazy_load'] ) && $ads['check_lazy_load'])){
        return true;
    }
    return false;
  }

  function quads_lazyload_delay_template($ads){
    if(isset($ads['check_lazy_load'] ) && $ads['check_lazy_load'] && isset($ads['check_lazy_load_delay']) && $ads['check_lazy_load_delay'] > 0 ){
        if(is_array($ads['check_lazy_load_delay']) && isset($ads['check_lazy_load_delay'][0])){
            return (intval($ads['check_lazy_load_delay'][0])*1000); 
        }else{
            return (intval($ads['check_lazy_load_delay'])*1000);
        }
        
    }
    if((function_exists('quads_delay_ad_sec') && quads_delay_ad_sec())){
        return 3000;
    }
    return 0;
  }

  add_filter('wp_quads_content_html_last_filter','quads_position_insert_advance',11,1);
  function quads_position_insert_advance($content){
    $quads_ads = quads_api_services_cllbck();
    if(isset($quads_ads['posts_data'])){        
        foreach($quads_ads['posts_data'] as $key => $value){
            $ads =$value['post_meta'];
            if($value['post']['post_status']== 'draft'){
                continue;
            }

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
        $is_click_fraud_on = quads_click_fraud_on();

        if(isset($ads['ad_id'])){
            $post_status = get_post_status($ads['ad_id']); 
        }else{
            $post_status =  'publish';
        }
        $quads_enabled_position = array('ad_after_class','ad_after_id');
            if($is_on && $is_visitor_on && $is_click_fraud_on && $post_status=='publish' && isset($ads['position']) && in_array($ads['position'],$quads_enabled_position)){
                $paragraph_no = (isset($ads['paragraph_number']) && $ads['paragraph_number'] !='') ? $ads['paragraph_number'] : 1;
                // placeholder string for custom ad spots
                if(isset($ads['random_ads_list']) && !empty($ads['random_ads_list'])){
                    $cusads = '<!--CusRnd'.esc_html($ads['ad_id']).'-->';
                }else if($ads['ad_type']== 'rotator_ads' &&isset($ads['ads_list']) && !empty($ads['ads_list'])){
                    $cusads = '<!--CusRot'.esc_html($ads['ad_id']).'-->';
                }else if($ads['ad_type']== 'popup_ads' &&isset($ads['ads_list']) && !empty($ads['ads_list'])){
                    $cusads = '<!--pop_up_ads'.esc_html($ads['ad_id']).'-->';
                }
                else if($ads['ad_type']== 'video_ads'){
                    $cusads = '<!--video_ad'.esc_html($ads['ad_id']).'-->';
                }
                else if($ads['ad_type']== 'parallax_ads'){
                    $cusads = '<!--parallax_ad'.esc_html($ads['ad_id']).'-->';
                }
                else if($ads['ad_type']== 'half_page_ads'){
                    $cusads = '<!--half_page_ad'.esc_html($ads['ad_id']).'-->';
                }
                else if($ads['ad_type']== 'floating_cubes'){
                    $cusads = '<!--floating_cubes_ad'.esc_html($ads['ad_id']).'-->';
                }
                else{                   
                    $cusads = '<!--CusAds'.esc_html($ads['ad_id']).'-->';
                }

                $quads_ad_style = quads_get_inline_ad_style_new($ads['ad_id']);
                $datalazydelay = quads_lazyload_delay_template($ads);
                $quad_parsed_ads = '';
              
                $repeat_paragraph = (isset($ads['repeat_paragraph']) && !empty($ads['repeat_paragraph'])) ? $ads['repeat_paragraph'] : false;
                if(isset($ads['position'])){
                    switch($ads['position']){
                        case 'ad_after_class':
                            $type_name = 'class';
                            $class_name = isset($ads['after_class_name']) ? $ads['after_class_name'] : '';  
                            if( strpos($content, "</blockquote>") || strpos($content, "</table>")){
                                $content =  quads_remove_ad_from_content($content,$cusads,'',$paragraph_no,$repeat_paragraph);
                            }else{
                                if(!empty($class_name)){
                                    if($ads['ad_type'] == 'random_ads') {
                                        if ( function_exists( 'quads_parse_random_ads' ) ) {
                                            $quad_parsed_ads  ='<!--CusRnd'.$ads['ad_id'].'-->';
                                            $quad_parsed_ads = quads_parse_random_ads_new($quad_parsed_ads);
                                        }
                                    }else if($ads['ad_type'] == 'rotator_ads') {
                                        if ( function_exists( 'quads_parse_rotator_ads' ) ) {
                                            $quad_parsed_ads  ='<!--CusRot'.$ads['ad_id'].'-->';
                                            $quad_parsed_ads = quads_parse_rotator_ads($quad_parsed_ads);
                                        }
                                        
                                    }else if($ads['ad_type'] == 'group_insertion') {
                                        if ( function_exists( 'quads_parse_group_insert_ads' ) ) {
                                            $quad_parsed_ads  ='<!--CusGI'.$ads['ad_id'].'-->';
                                            $quad_parsed_ads = quads_parse_group_insert_ads($quad_parsed_ads);
                                        }
                                    }else{
                                        $quad_parsed_ads = quads_render_ad($ads['quads_ad_old_id'],$ads['code']);
                                        $quad_parsed_ads = quads_parse_id_class_ads( $quad_parsed_ads,  $ads['ad_id'] );
                                    }

                                    $class_name = '"'.$class_name.'"';
                                    $content = quads_after_id_class_ad_creator($content,$class_name,$type_name);
                                    $content = str_replace('afterClassAd', $quad_parsed_ads, $content);
                            }

                    }
                            
                        break;
                        case 'ad_after_id':
                            
                            $type_name = 'id';
                            $id_name = isset($ads['after_id_name']) ? $ads['after_id_name'] : '';
                            if( strpos($content, "</blockquote>") || strpos($content, "</table>")){
                                $content =  quads_remove_ad_from_content($content,$cusads,'',$paragraph_no,$repeat_paragraph);
                            }else{
                                if(!empty($id_name)){
                                    if($ads['ad_type'] == 'random_ads') {
                                        if ( function_exists( 'quads_parse_random_ads' ) ) {
                                            $quad_parsed_ads  ='<!--CusRnd'.$ads['ad_id'].'-->';
                                            $quad_parsed_ads = quads_parse_random_ads_new($quad_parsed_ads);
                                        }
                                    }else if($ads['ad_type'] == 'rotator_ads') {
                                        if ( function_exists( 'quads_parse_rotator_ads' ) ) {
                                            $quad_parsed_ads  ='<!--CusRot'.$ads['ad_id'].'-->';
                                            $quad_parsed_ads = quads_parse_rotator_ads($quad_parsed_ads);
                                        }
                                        
                                    }else if($ads['ad_type'] == 'group_insertion') {
                                        if ( function_exists( 'quads_parse_group_insert_ads' ) ) {
                                            $quad_parsed_ads  ='<!--CusGI'.$ads['ad_id'].'-->';
                                            $quad_parsed_ads = quads_parse_group_insert_ads($quad_parsed_ads);
                                        }
                                    }else{
                                        $quad_parsed_ads = quads_render_ad($ads['quads_ad_old_id'],$ads['code']);
                                        $quad_parsed_ads = quads_parse_id_class_ads( $quad_parsed_ads,  $ads['ad_id'] );
                                    }
                                    $id_name = '"'.$id_name.'"';
                                    $content = quads_after_id_class_ad_creator($content,$id_name,$type_name);
                                    $content = str_replace('afterIdAd', $quad_parsed_ads, $content);
                                }
                            }
                        break;
                        case 'after_paragraph':
                            $exclude_from_class_id         = isset($ads['exclude_from_class_id']) ? $ads['exclude_from_class_id'] : '';
                    }
            
                }

            }
        }

    }
    return $content;
  }
function quads_display_sticky_ads(){
    $quads_ads = quads_api_services_cllbck(); 
    $quads_adsArrayCus = array();
    if(isset($quads_ads['posts_data'])){        
       
        foreach($quads_ads['posts_data'] as $key => $value){
            $ads =$value['post_meta'];
            if($value['post']['post_status']== 'draft'){
                continue;
            }
            $quads_visibilty = apply_filters('quads_ad_conditional_visibility', $value['post_meta']);
            if(!$quads_visibilty){
                continue;
            }
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
            $is_click_fraud_on = quads_click_fraud_on();
            if(isset($ads['ad_id']))
            $post_status = get_post_status($ads['ad_id']); 
            else
              $post_status =  'publish';
            if($is_on && $is_visitor_on && $is_click_fraud_on && $post_status=='publish'){
                $ads  = apply_filters( 'quads_default_filter_position_data', $ads);
                $ads  = apply_filters( 'quads_default_filter_position_data_ab_testing', $ads);

                $position     = (isset($ads['position']) && $ads['position'] !='') ? $ads['position'] : '';
                $cls_btn     = (isset($ads['cls_btn']) && $ads['cls_btn'] !='') ? $ads['cls_btn'] : '';
                $sticky_show_hide     = (isset($ads['sticky_ad_show_hide']) && $ads['sticky_ad_show_hide'] !='') ? $ads['sticky_ad_show_hide'] : '';
                $sticky_show_hide_txt     = (isset($ads['sticky_show_hide_txt']) && $ads['sticky_show_hide_txt'] !='') ? $ads['sticky_show_hide_txt'] : 'Slide Up';
                $sticky_ad_anim = (isset($ads['sticky_ad_anim']) && $ads['sticky_ad_anim'] !='') ? $ads['sticky_ad_anim'] : '';
                $sticky_ad_anim_text = (isset($ads['sticky_ad_anim_txt']) && $ads['sticky_ad_anim_txt'] !='') ? $ads['sticky_ad_anim_txt'] : '1000';
              
 
                                   
                $cusads = '<!--CusAds'.esc_html($ads['ad_id']).'-->';
                
               
                switch ($position) {
                    case 'ad_sticky_ad':
                        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                        $sticky_cookie =   (isset( $_COOKIE['quads_sticky'] ) && $_COOKIE['quads_sticky']!== NULL ) ? wp_unslash( $_COOKIE['quads_sticky'] ) : '' ;
                        if( $sticky_cookie !== "sticky_ad" ){
                            if(strpos( $content, '<!--OffEnd-->' ) === false ) {
                                $a_tag = $btn_tag = '';
                                if( isset($cls_btn) && $cls_btn == 1 ){
                                    $a_tag = '<a class="quads-sticky-ad-close">x</a>';
                                }
                                if( isset($sticky_show_hide) && $sticky_show_hide == 1 ){
                                    $btn_tag = '<div class="quads-sticky-show-btn">'.esc_attr($sticky_show_hide_txt).'</div>';
                                }
                                $anim_attr = ( isset($sticky_ad_anim) && $sticky_ad_anim == 1 ) ? ' data-anim-tod='.$sticky_ad_anim_text.'' : '';
                                $q_main_open = ''.$btn_tag.'<div class="quads-sticky"'.$anim_attr.'>'.$a_tag.'';
                                $q_close = '</div>';
                                $content = $q_main_open.$cusads.$q_close;
                                /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
                                echo quads_replace_ads_new( $content, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
                            }                                 
                        }
                        break;    
                }
            }
        }
    }
}

add_action( 'wp_footer', 'quads_display_sticky_ads'  );


function quads_add_inmobi_cmp_config() {
    if( quads_is_amp_endpoint() ){
        return false;
    }
    $quads_settings = get_option( 'quads_settings' );
    $in_mobi_integration = isset($quads_settings['in_mobi_integration']) ? $quads_settings['in_mobi_integration'] : false;
    $in_mobi_site_id = isset($quads_settings['in_mobi_site_id']) ? $quads_settings['in_mobi_site_id'] : false;
    $in_mobi_hash_code = isset($quads_settings['in_mobi_hash_code']) ? $quads_settings['in_mobi_hash_code'] : '';
    $in_mobi_publisher_country_code = isset($quads_settings['in_mobi_publisher_country_code']) ? $quads_settings['in_mobi_publisher_country_code'] : '';
    $in_mobi_privacy_mode = isset($quads_settings['in_mobi_privacy_mode']) ? $quads_settings['in_mobi_privacy_mode'] : '';
    $in_mobi_banner_text = isset($quads_settings['in_mobi_banner_text']) ? $quads_settings['in_mobi_banner_text'] : '';
    
    // Display Condition is false and ignoreShortcodeCond is empty or not true
    if( $in_mobi_integration==true  && $in_mobi_site_id != "" && $in_mobi_banner_text != ''){
        $privacy = array();
        if (empty($in_mobi_privacy_mode)) {
            $privacy[] = "GDPR";
        }else{
            $exp = explode(',',$in_mobi_privacy_mode);
            $pri_arr = array();
            for ($i=0; $i < count($exp); $i++) { 
                $pr ='"'.$exp[$i].'"';
                $pri_arr[] = $pr;
            }
            $privacy = $pri_arr;
        }
        $new_priv = implode( ',', $privacy);
        echo '<script>
        window.__cmpConfig = {
            consentConfig: {
            siteId: '.esc_attr( $in_mobi_site_id ).',
            regulations: ['.esc_attr( $new_priv ).'],
            gdprAppliesGlobally: false,
            customization: {
                bannerText: '.esc_html( $in_mobi_banner_text ).',
                acceptAllText: "Accept",
                rejectAllText: "Reject",
                managePreferencesText: "Preferences",
                theme: "light"
            }
            }
        };
        </script>';
        // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
        echo '<script src="https://consent.inmobi.com/choice/'.esc_attr( $in_mobi_site_id ).'.js" async></script>';
    }
}
add_action('wp_head', 'quads_add_inmobi_cmp_config');
add_action('amp_post_template_footer','quads_ampforwp_footer_html_output',11);
function quads_ampforwp_footer_html_output() {
    if( ! quads_is_amp_endpoint() ){
        return false;
    }
    $quads_settings = get_option( 'quads_settings' );
    $in_mobi_integration = isset($quads_settings['in_mobi_integration']) ? $quads_settings['in_mobi_integration'] : false;
    $in_mobi_site_id = isset($quads_settings['in_mobi_site_id']) ? $quads_settings['in_mobi_site_id'] : false;
    $in_mobi_hash_code = isset($quads_settings['in_mobi_hash_code']) ? $quads_settings['in_mobi_hash_code'] : '';
    $in_mobi_publisher_country_code = isset($quads_settings['in_mobi_publisher_country_code']) ? $quads_settings['in_mobi_publisher_country_code'] : '';
    $in_mobi_privacy_mode = isset($quads_settings['in_mobi_privacy_mode']) ? $quads_settings['in_mobi_privacy_mode'] : '';
    $in_mobi_banner_text = isset($quads_settings['in_mobi_banner_text']) ? $quads_settings['in_mobi_banner_text'] : '';
    if ($in_mobi_integration == true) {
		$id = $in_mobi_site_id;
		$hashcode = $in_mobi_hash_code;
		$country = $in_mobi_publisher_country_code;
		$privacy = $in_mobi_privacy_mode;
		$lang = 'eng';
		if (empty($privacy)) {
			$privacy = array("GDPR");
		}else{
			$exp = explode(',',$privacy);
			$pri_arr = array();
			for ($i=0; $i < count($exp); $i++) { 
				$pr ='"'.$exp[$i].'"';
				$pri_arr[] = $pr;
			}
			$privacy = $pri_arr;
		}
		if (empty($lang)) {
			$lang = 'en';
		}
		if (!empty($id) && !empty($hashcode) && !empty($country) ) {?>
			<amp-consent id="inmobi" layout="nodisplay">
			<script type="application/json">
				{
				"consentInstanceId": "inmobi",
				"checkConsentHref": "https://api.cmp.inmobi.com/amp/check-consent",
				"consentRequired": "remote",
				"promptUISrc": "https://cmp.inmobi.com/tcfv2/amp.html",
				"clientConfig": {
					"coreConfig": {
					"vendorPurposeLegitimateInterestIds": [
						2,
						7,
						8,
						10,
						9,
						11
					],
					"publisherPurposeIds": [],
					"publisherSpecialPurposesIds": [],
					"publisherFeaturesIds": [],
					"stacks": [
						1,
						42
					],
					"publisherLIRestrictionIds": [],
					"inmobiAccountId": "<?php echo esc_attr( $id )?>",
					"vendorSpecialPurposesIds": [
						1,
						2
					],
					"initScreenBodyTextOption": 1,
					"publisherConsentRestrictionIds": [],
					"vendorPurposeIds": [2,4,6,7,9,10,1,3,5,8,11],
					"totalVendors": 1420,
					"lang_": "en",
					"privacyMode": [<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ echo implode( ',', $privacy);?>],
					"publisherPurposeLegitimateInterestIds": [],
					"hashCode": "<?php echo esc_attr($hashcode)?>",
					"vendorSpecialFeaturesIds": [
						1,
						2
					],
					"displayUi": "always",
					"publisherSpecialFeaturesIds": [],
					"googleEnabled": false,
					"vendorListUpdateFreq": 30,
					"publisherCountryCode": "<?php echo esc_attr( $country );?>",
					"vendorFeaturesIds": [
						1,
						2,
						3
					],
					"gvlVersion": 3
					},
					"coreUiLabels": { 
                                        "message": "<?php echo esc_attr( $in_mobi_banner_text );?>",
                                        "acceptButtonLabel": "Accept All",
                                        "rejectButtonLabel": "Reject All"
                                    },
					"theme": {},
					"tagVersion": "V3"
				}
				}
			</script>
			</amp-consent>
		<?php
		}
	}
}

/**
 * @param   $ad
 * @since   2.0.95
 * */
function quads_parse_id_class_ads( $add_content, $id ) {
    
    $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . ' -->' . "\n" .
        '<div class="quads-location quads-ad' . esc_html( $id ) . '" id="quads-ad' . esc_html( $id ) . '">' . "\n";
    $code .= $add_content;
    $code .= '</div>' . "\n";

    return $code;

}
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
add_action('amp_post_template_head','quads_adsense_auto_ads_amp_script',1);
add_action('amp_post_template_footer','quads_adsense_auto_ads_amp_tag');
 
add_action( 'init', 'remove_ads_for_wp_shortcodes',999 );

function remove_ads_for_wp_shortcodes() {
  $quads_settings = get_option( 'quads_settings' );
  if(isset($quads_settings['adsforwp_quads_shortcode']) && $quads_settings['adsforwp_quads_shortcode']){
      remove_shortcode( 'adsforwp' );
      add_shortcode('adsforwp', 'quads_from_adsforwp_manual_ads',1);
  }
}

//Ad blocker
add_action('wp_head', 'quads_adblocker_detector');
add_action('wp_footer', 'quads_adblocker_popup_notice');
add_action('wp_footer', 'quads_adblocker_notice_jsondata');
add_action('wp_body_open', 'quads_adblocker_notice_bar');
add_action('wp_footer', 'quads_adblocker_ad_block');


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
      'meta_key'   => 'adsforwp_ads_id', 
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

    $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  Shortcode Ad -->' . "\n" .
            '<div class="quads-location quads-ad' . $id . '" id="quads-ad' . $id . '" style="' . $style . '">' . "\n";
    $code .= do_shortcode( quads_get_ad( $id ) );
    $code .= '</div>' . "\n";

    return $code;
}
function quads_adblocker_detector(){
     echo '<script type="text/javascript" src="'.QUADS_PLUGIN_URL . 'assets/js/ads.js"></script>';

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
        

      $content_color = sanitize_hex_color($settings['notice_txt_color']);
      $notice_title = esc_attr($settings['notice_title']);
      $notice_description = esc_attr($settings['notice_description']);
      $button_txt = esc_attr($settings['btn_txt']);
      $background_color = sanitize_hex_color($settings['notice_bg_color']);
      $btn_txt_color = sanitize_hex_color($settings['notice_btn_txt_color']);
      $btn_background_color = sanitize_hex_color($settings['notice_btn_bg_color']);
      
  ?>
    <div id="quads-myModal" class="quads-modal">
      <!-- Modal content -->
      <div class="quads-modal-content">
    <?php if( isset($settings['notice_close_btn']) && $settings['notice_close_btn'] && empty($button_txt) ){
          ?>
          <span class="quads-close quads-cls-notice">&times;</span>  
          <?php
        }
        ?>
        <h2 style="text-align: center;padding-top:0;color: <?php echo $content_color;?>;"><?php echo $notice_title;?></h2>
        <p style="margin:0 0 1.5em;padding: 0;text-align: center;color: <?php echo $content_color;?>;"><?php echo $notice_description;?></p>
        <?php if( isset($settings['notice_close_btn']) && $settings['notice_close_btn'] &&  !empty($button_txt) ){
          ?>
          <button class="quads-button quads-closebtn quads-cls-notice"><?php echo $button_txt;?></button>
        <?php
        }
        ?>
      </div>
    </div>
    <style type="text/css">
    .quads-modal {
      display: none; /* Hidden by default */
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
      background-color: <?php echo $background_color;?>;
      margin: auto;
      padding: 20px;
      border: 1px solid #888;
      width: 40%;
      border-radius: 10px;
      text-align: center;
    }

    /* The Close Button */
    .quads-close{
      color: <?php echo $btn_txt_color;?>;
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
      background-color: <?php echo $btn_background_color;?>; /* Green */
      border: none;
      color: <?php echo $btn_txt_color;?>;
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
    $settings = quads_defaultSettings();
    $output = '';

    if( isset($settings['ad_blocker_support']) && $settings['ad_blocker_support'] && !empty($settings['notice_type'])){
      
      $output    .= '<script type="text/javascript">';
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
      echo $output;
    }
}
function quads_adblocker_notice_bar(){
    $settings = quads_defaultSettings();
    
  if( isset($settings['ad_blocker_support']) && $settings['ad_blocker_support']){
    if($settings['notice_type'] == 'bar' ){
      
      $notice_description = esc_attr($settings['notice_description']);
      $button_txt = esc_attr($settings['btn_txt']);
      $content_color = sanitize_hex_color($settings['notice_txt_color']);
      $background_color = sanitize_hex_color($settings['notice_bg_color']);
      $btn_txt_color = sanitize_hex_color($settings['notice_btn_txt_color']);
      $btn_background_color = sanitize_hex_color($settings['notice_btn_bg_color']);
  ?>
  
  <div id="quads-myModal" class="quads-adblocker-notice-bar">
    <div class="enb-textcenter">
      <?php if( isset($settings['notice_close_btn'])&& $settings['notice_close_btn'] && empty($button_txt)){?>
      <span class="quads-close quads-cls-notice">&times;</span>  
    <?php } ?>
      <div class="quads-adblocker-message">
      <?php echo $notice_description;?>
      </div>
      <?php if( isset($settings['notice_close_btn'])&& $settings['notice_close_btn'] && !empty($button_txt)){?>
      <button class="quads-button quads-closebtn quads-cls-notice"><?php echo $button_txt;?></button>  
    <?php } ?>
    </div>
  </div>
  <style type="text/css">
    .quads-adblocker-message{
      display: inline-block;
    }
    .quads-adblocker-notice-bar {
      display: none;
      width: 100%;
      background: <?php echo $background_color;?>;
      color: <?php echo $content_color;?>;
      padding: 0.5em 1em;
      font-size: 16px;
      line-height: 1.8;
      position: relative;
      z-index: 99;
    }
    .quads-adblocker-notice-bar strong {
      color: inherit; /* some themes change strong tag to make it darker */
    }
    /* Alignments */
    .quads-adblocker-notice-bar .enb-textcenter {
      text-align: center;
    }
    .quads-close{
      color: <?php echo $btn_txt_color;?>;
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
      background-color: <?php echo $btn_background_color;?>; /* Green */
      border: none;
      color: <?php echo $btn_txt_color;?>;
      padding: 5px 10px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 14px;
      margin: 0px 2px;
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
  ?>
<script type="text/javascript">
   if(typeof quadsOptions !== 'undefined' && typeof wpquads_adblocker_check_2 
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
      if (typeof wpquads_adblocker_check_2 === "undefined" || wpquads_adblocker_check_2 === false) {
          highlight_adblocked_ads();
      }
  });

   function highlight_adblocked_ads() {
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
var quadsAllowedCookie =  quadsgetCookie('quadsAllowedCookie');

if(typeof quadsOptions !== 'undefined' && typeof wpquads_adblocker_check_2 
  === 'undefined' ){

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

window.onclick = function(event) {
  if (event.target == modal) {
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
/**
 * Show ads before posts
 * @not used at the moment
 */
//add_action('loop_start', 'quads_inject_ad');

//function quads_inject_ad() {
//   global $quads_options, $post;
//   
//   // Ads are deactivated via post meta settings
//    if( quads_check_meta_setting( 'NoAds' ) === '1' || quads_check_meta_setting( 'OffBegin' ) === '1'){
//        return false;
//    }
//   
//   if( !quads_ad_is_allowed( '' ) || !is_main_query() ) {
//      return false;
//   }
//   // Array of ad codes ids
//   $adsArray = quads_get_active_ads();
//
//   // Return no ads are defined
//   if( count($adsArray) === 0 ) {
//      return false;
//   }
//   
//   $id = 1;
//   
//   $code = !empty($quads_options['ads']['ad' . $id ]['code']) ? $quads_options['ads']['ad' . $id ]['code'] : '';
//   echo quads_render_ad(1, $code, false);
//   
//}

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
        $dom = new DOMDocument();
         if( function_exists( 'mb_convert_encoding' ) ){
          $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');     
        }
        else{
          $content =  preg_replace( '/&.*?;/', 'x', $content ); // multi-byte characters converted to X
        }
        @$dom->loadHTML($content);
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
    return 20;
}

/**
 * 
 * @global arr $quads_options
 * @global type $adsArray
 * @param type $content
 * @return type
 */
function quads_process_content( $content ) {
    global $quads_mode, $quads_options, $adsArray, $adsArrayCus, $visibleContentAds, $ad_count_widget, $visibleShortcodeAds;        
    
    // Array of ad codes ids
    $adsArray = quads_get_active_ads();
    
    // Return is no ads are defined
    if ($adsArray === 0 && $quads_mode != 'new'){
        return $content;
    }

    // Do nothing if maximum ads are reached in post content
    if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
        $content = quads_clean_tags( $content );
        return $content;
    }

    // Do not do anything if ads are not allowed or process is not in the main query
    if( !quads_ad_is_allowed( $content ) || !is_main_query()) {
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
    $maxWidgets = 10;
    // count active widget ads
        for ( $i = 1; $i <= $maxWidgets; $i++ ) {
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
            $adsArray[] = $i;
        }
        $i++;
    }
    return (isset($adsArray) && count($adsArray) > 0) ? $adsArray : 0;
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
            $adsArray[] = $i;
        }
        $i++;
    }
    return (isset($adsArray) && count($adsArray) > 0) ? $adsArray : 0;
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
 * @global array $adsArrayCus List of ad id'S
 * @param string $content
 * @return string content
 */
function quads_filter_default_ads_new( $content ) {

    global $quads_options, $adsArrayCus;   
    
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);

    if( $off_default_ads ) { // If default ads are disabled 
        return $content;
    }    
     require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
                $api_service = new QUADS_Ad_Setup_Api_Service();
                    $quads_ads = $api_service->getAdDataByParam('quads-ads');
    // Default Ads
    $adsArrayCus = array();
    if(isset($quads_ads['posts_data'])){        

        $i = 1;
        foreach($quads_ads['posts_data'] as $key => $value){
            $ads =$value['post_meta'];
            if($value['post']['post_status']== 'draft'){
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

            if(!isset($ads['position']) || isset($ads['ad_type']) && $ads['ad_type']== 'random_ads'){
                if($ads['ad_id']){

                    if(isset($ads['position'])){

                        $ads['position']               = isset($ads['position']) ? $ads['position'] : '';;
                        $ads['paragraph_number']       = isset($ads['paragraph_number']) ? $ads['paragraph_number'] : '';
                        $ads['image_number']           = isset($ads['image_number']) ? $ads['image_number'] : '';
                        $ads['image_caption']          = isset($ads['image_caption']) ? $ads['image_caption'] : '';
                        $ads['enable_on_end_of_post']  = isset($ads['enable_on_end_of_post']) ? $ads['enable_on_end_of_post'] : '';
                        $quads_options[$key]['position']              = isset($ads['position']) ? $ads['position'] : '';
                        $quads_options[$key]['paragraph_number']      = isset($ads['paragraph_number']) ? $ads['paragraph_number'] : '';
                        $quads_options[$key]['image_number']          = isset($ads['image_number']) ? $ads['image_number'] : '';
                        $quads_options[$key]['image_caption']         = isset($ads['image_caption']) ? $ads['image_caption'] : '';
                        $quads_options[$key]['enable_on_end_of_post'] = isset($ads['enable_on_end_of_post']) ? $ads['enable_on_end_of_post'] : '';
                    }
                }
                
                $is_on = true;
            }           
            
            if($is_on && $is_visitor_on && $is_click_fraud_on && $post_status=='publish'){
                    
                $position     = (isset($ads['position']) && $ads['position'] !='') ? $ads['position'] : '';
                $paragraph_no = (isset($ads['paragraph_number']) && $ads['paragraph_number'] !='') ? $ads['paragraph_number'] : 1;
                $word_count_number = (isset($ads['word_count_number']) && $ads['word_count_number'] !='') ? $ads['word_count_number'] : 1;
                $imageNo      = (isset($ads['image_number']) && $ads['image_number'] !='') ? $ads['image_number'] : 1;
                $imageCaption = isset($ads['image_caption']) ? $ads['image_caption'] : false;
                $end_of_post  = isset($ads['enable_on_end_of_post']) ? $ads['enable_on_end_of_post'] : false;
 
                // placeholder string for custom ad spots
                if(isset($ads['random_ads_list']) && !empty($ads['random_ads_list'])){
                    $cusads = '<!--CusRnd'.$ads['ad_id'].'-->'; 
                }else{
                       $cusads = '<!--CusAds'.$ads['ad_id'].'-->'; 
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

                        break;                            
                    case 'end_of_post':           
                        if(strpos( $content, '<!--OffEnd-->' ) === false ) {
                           $content = $content.$cusads;   
                        }                     
                        # code...
                        break;                                
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
                            if($word_count_number <= $p_count){

                                foreach ($paragraphs as $index => $paragraph) {
        
                                    if ( $word_count_number == $index + 1 ) {
                                        $paragraphs[$index] .= $cusads;
                                    }
                                }
                                $content = implode( ' ', $paragraphs ); 
                            }                                                       
                        }

                        break;
                                        case 'after_paragraph':
                        
                        if(strpos( $content, '<!--OffBfLastPara-->' ) === false ) {
                          $repeat_paragraph = (isset($ads['repeat_paragraph']) && !empty($ads['repeat_paragraph'])) ? $ads['repeat_paragraph'] : false;
                            if( strpos($content, "</blockquote>")){
                          $content =  remove_ad_from_content($content,$cusads,'',$paragraph_no,$repeat_paragraph);
                        }else{
                            $closing_p        = '</p>';
                            $paragraphs       = explode( $closing_p, $content );
                            $p_count          = count($paragraphs);
                            $original_paragraph_no = $paragraph_no;                                                             
                            
                            if($paragraph_no <= $p_count){

                                foreach ($paragraphs as $index => $paragraph) {
                                    if ( trim( $paragraph ) ) {
                                        $paragraphs[$index] .= $closing_p;
                                    }
                                    if ( $paragraph_no == $index + 1 ) {
                                        // exit(var_dump($index));
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
                     case 'after_the_percentage':
                    
                        $content =  remove_ad_from_content($content,$cusads,$ads);

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
                          if($tag == 'p' && strpos($content, "</blockquote>")){
                          $content =  remove_ad_from_content($content,$cusads,'',$paragraph_no,$repeat_paragraph);
                        }else{
                                $closing_p        = '</'.$tag.'>';
                            $paragraphs       = explode( $closing_p, $content );
                            $p_count          = count($paragraphs);
                            $original_paragraph_no = $paragraph_no;
                            if($paragraph_no <= $p_count){

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
                                $content = implode( '', $paragraphs ); 
                            }else{
                                if($end_of_post){
                                    $content = $content.$cusads;   
                                }                                
                            }  
                            }                                                      
                        break; 
                }

                $adsArrayCus[] = $i;   
            }
            $i++;
        }
        
    }
    
    return $content;
}

/**
 * Filter default ads
 * 
 * @global array $quads_options global settings
 * @global array $adsArrayCus List of ad id'S
 * @param string $content
 * @return string content
 */
function quads_filter_default_ads( $content ) {

    global $quads_options, $adsArrayCus;
    
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);

    if( $off_default_ads ) { // If default ads are disabled 
        return $content;
    }    
    // Default Ads
    $adsArrayCus = array();

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
        array_push( $adsArrayCus, $beginning_position_ad_id );
    };
    
    if( $more_position_ad_id == 0 ) {
        $r1 = $cusrnd;
    } else {
        $r1 = $cusads . $more_position_ad_id;
        array_push( $adsArrayCus, $more_position_ad_id );
    };
    
    if( $middle_position_ad_id == 0 ) {
        $m1 = $cusrnd;
    } else {
        $m1 = $cusads . $middle_position_ad_id;
        array_push( $adsArrayCus, $middle_position_ad_id );
    };
    if( $last_paragraph_position_ad_id == 0 ) {
        $g1 = $cusrnd;
    } else {
        $g1 = $cusads . $last_paragraph_position_ad_id;
        array_push( $adsArrayCus, $last_paragraph_position_ad_id );
    };
    if( $end_position_ad_id == 0 ) {
        $b2 = $cusrnd;
    } else {
        $b2 = $cusads . $end_position_ad_id;
        array_push( $adsArrayCus, $end_position_ad_id );
    };
    for ( $i = 1; $i <= $number; $i++ ) {
        if( $paragraph['id'][$i] == 0 ) {
            $paragraph[$i] = $cusrnd;
        } else {
            $paragraph[$i] = $cusads . $paragraph['id'][$i];
            array_push( $adsArrayCus, $paragraph['id'][$i] );
        };
    }
    //wp_die(print_r($adsArrayCus));

    // Create the arguments for filter quads_filter_paragraphs
    $quads_args = array(
        'paragraph' => $paragraph,
        'cusads' => $cusads,
        'cusrnd' => $cusrnd,
        'AdsIdCus' => $adsArrayCus,
    );

    // Execute filter to add more paragraph ad spots
    $quads_filtered = apply_filters( 'quads_filter_paragraphs', $quads_args );

    // The filtered arguments
    $paragraph = $quads_filtered['paragraph'];

    // filtered list of ad spots
    $adsArrayCus = $quads_filtered['AdsIdCus'];

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
        array_push( $adsArrayCus, $imageAdNo );
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
 * @global int $visibleContentAds
 * @return content
 */
function quads_parse_random_quicktag_ads($content){
    global $adsArray, $visibleContentAds, $quads_options;
    
    $maxAds = isset($quads_options['maxads']) ? $quads_options['maxads'] : 10;
    /*
     * Replace RndAds Random Ads
     */
    $content=  str_replace('[quads id=RndAds]', '<!--RndAds-->', $content);
    if( strpos( $content, '<!--RndAds-->' ) !== false && is_singular() ) {
        $adsArrayTmp = array();
        shuffle( $adsArray );
        for ( $i = 1; $i <= $maxAds - $visibleContentAds; $i++ ) {
            if( $i <= count( $adsArray ) ) {
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
                $visibleContentAds += 1;
            };
            //quads_set_ad_count_content();
            //if( quads_ad_reach_max_count() ) {
            if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
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
 * @global int $visibleContentAds
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
            if (isset($radom_ad['value'])){
                $temp_array[] = $radom_ad['value'];
            }
        }
        $random_ads_list_after =  array_diff($temp_array, $selected_ads);
        $keys = array_keys($random_ads_list_after); 
        shuffle($keys); 
        $randomid = $random_ads_list_after[$keys[0]]; 
        $selected_ads[] = $randomid;
        $enabled_on_amp = (isset($ad_meta['enabled_on_amp'][0]))? $ad_meta['enabled_on_amp'][0]: '';
        $content = quads_replace_ads_new( $content, 'CusRnd' . $ad_id, $randomid,$enabled_on_amp);
    }
    return $content;

}

/**
 * Parse random default ads which can be enabled from general settings
 * 
 * @global array $adsArray
 * @global int $visibleContentAds
 * @return string
 */
function quads_parse_random_ads($content) {
    global $adsRandom, $visibleContentAds;
    
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);
    if( $off_default_ads ) { // disabled default ads
        return $content;
    }

    if( strpos( $content, '<!--CusRnd-->' ) !== false && is_singular() ) {

        $tcx = count( $adsRandom );
        // How often is a random ad appearing in content
        $number_rand_ads = substr_count( $content, '<!--CusRnd-->' );

        for ( $i = $tcx; $i <= $number_rand_ads - 1; $i++ ) {
            array_push( $adsRandom, -1 );
        }
        shuffle( $adsRandom );
        //wp_die(print_r($adsRandom));
        //wp_die($adsRandom[0]);
        for ( $i = 1; $i <= $number_rand_ads; $i++ ) {
            $content = quads_replace_ads( $content, 'CusRnd', $adsRandom[0] );
            $adsRandom = quads_del_element( $adsRandom, 0 );
            $visibleContentAds += 1;
            //quads_set_ad_count_content();
            //if( quads_ad_reach_max_count() ) {
            if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
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
    global $adsArray, $visibleContentAds;
    //print_r(count($adsArray));
    if (!is_array($adsArray)){
        return $content;
    }
    $idx = 0;
    for ( $i = 1; $i <= count( $adsArray ); $i++ ) {
        if( strpos( $content, '<!--Ads' . $adsArray[$idx] . '-->' ) !== false ) {
            $content = quads_replace_ads( $content, 'Ads' . $adsArray[$idx], $adsArray[$idx] );
            //$adsArray = quads_del_element( $adsArray, $idx );
            $visibleContentAds += 1;
            $idx +=1;
            //quads_set_ad_count_content();
            if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
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
    global $adsArrayCus, $adsRandom, $adsArray, $visibleContentAds;
     
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);

    if( $off_default_ads ) { // disabled default ads
        return $content;
    }
    // Create the array which contains the random ads
    $adsRandom = $adsArray;

//        echo '<pre>';
//        echo 'adsArrayCus: ';
//        print_r($adsArrayCus);
//        echo 'adsArray: ';
//        print_r( $adsArray );
//        echo '</pre>';

    for ( $i = 0; $i <= count( $adsArrayCus ); $i++ ) {
        
        if( isset( $adsArrayCus[$i] ) && strpos( $content, '<!--CusAds' . $adsArrayCus[$i] . '-->' ) !== false && in_array( $adsArrayCus[$i], $adsArray ) ) {
             
            $content = quads_replace_ads( $content, 'CusAds' . $adsArrayCus[$i], $adsArrayCus[$i] );

            // Create array $adsRandom for quads_parse_random_ads() parsing functions to make sure that the random function 
            // is never using ads that are already used on static ad spots which are generated with quads_parse_default_ads()
            if ($i == 0){
                $adsRandom = quads_del_element($adsRandom, array_search($adsArrayCus[$i], $adsRandom));
            }else{
                $adsRandom = quads_del_element($adsRandom, array_search($adsArrayCus[$i-1], $adsRandom));
            }
            
            $visibleContentAds += 1;
            //quads_set_ad_count_content();
            //if( quads_ad_reach_max_count() || $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
            //wp_die(quads_get_max_allowed_post_ads( $content ));

            if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
             
                $content = quads_clean_tags( $content );
            }
        }
    }
    return $content;
}
function quads_parse_default_ads_new( $content ) {
    global $adsArrayCus, $adsRandom, $adsArray;
     
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
        global $quads_options;
   

    if( strpos($content,'<!--'.$quicktag.'-->')===false ) { 
            return $content; 
        }

        
    if ($id != -1) {
                
                $code = !empty($quads_options['ads']['ad' . $id ]['code']) ? $quads_options['ads']['ad' . $id ]['code'] : '';
                $style = quads_get_inline_ad_style($id);
                $adscode =
            "\n".'<!-- WP QUADS Content Ad Plugin v. ' . QUADS_VERSION .' -->'."\n".
            '<div class="quads-location quads-ad' .$id. '" id="quads-ad' .$id. '" style="'.$style.'">'."\n".
            quads_render_ad('ad'.$id, $code)."\n".
            '</div>'. "\n";
              
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
    $ad_meta = get_post_meta($id, '',true);
    if (isset($ad_meta['code'][0])) {
        if(!empty($ad_meta['code'][0])){

            $code = '';
                if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true && strpos($ad_meta['code'][0], 'class="adsbygoogle"') !== false) {
                    $id_name = "quads-".esc_attr($id)."-place";
                    $code .= '<div id="'.esc_attr($id_name).'" class="quads-ll">' ;
                }
                $code .=   $ad_meta['code'][0];
                if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true && strpos($ad_meta['code'][0], 'class="adsbygoogle"') !== false) {
                    $check_script_tag =    preg_match('#<script(.*?)src=(.*?)>(.*?)</script>#is', $code);
                    if($check_script_tag){
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
                $adscode =
            "\n".'<!-- WP QUADS Content Ad Plugin v. ' . QUADS_VERSION .' -->'."\n".
            '<div class="quads-location quads-ad' .$id. '" id="quads-ad' .$id. '" style="'.$style.'">'."\n".
            quads_render_ad($ad_meta['quads_ad_old_id'][0], $code,'',$ampsupport)."\n".
            '</div>'. "\n";
              
    } else {
        $adscode ='';
    }   
    $cont = explode('<!--'.$quicktag.'-->', $content, 2);
        
    return $cont[0].$adscode.$cont[1];
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
        'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
        'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
        'float:none;margin:%1$dpx;');
    
    // Alignment
    $adsalign = ( int )$ad_meta['align'][0];
    
    // Margin
    $adsmargin = isset( $ad_meta['margin'][0] ) ? $ad_meta['margin'][0] : '3'; // default option = 3
    $margin = sprintf( $styleArray[$adsalign], $adsmargin );
    
    // Do not create any inline style on AMP site
    $style =  apply_filters( 'quads_filter_margins', $margin, 'ad' . $id ) ;
    
    return $style;
}

/**
 * Revert content to original content any remove any processing helper strings
 * 
 * @global int $visibleContentAds
 * @global array $adsArray
 * @global array $quads_options
 * @global int $ad_count
 * @param string $content
 * @param boolean $trimonly
 * 
 * @return string content
 */
function quads_clean_tags($content, $trimonly = false) {
    global $visibleContentAds;
    global $adsArray;
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
        $visibleContentAds = 0;
        $adsArray = array();
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
          require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
          $api_service = new QUADS_Ad_Setup_Api_Service();
          $quads_ads = $api_service->getAdDataByParam('quads-ads');
          $quads_new_interface_ads = $quads_ads;
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
                  echo   quads_replace_ads_new( $tag, 'CusAds' . $ads['ad_id'], $ads['ad_id'] );
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

      require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
        $api_service = new QUADS_Ad_Setup_Api_Service();
        $quads_ads = $api_service->getAdDataByParam('quads-ads');

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
            
            if($is_on && $is_visitor_on && $post_status=='publish'){
                if($ads['ad_type'] == 'background_ad'){

                      $after_body=''
                . '<div class="quads-bg-wrapper">
                   <a style="background-image: url('.esc_attr($ads['image_src']).')" class="quads-bg-ad" target="_blank" href="'.esc_attr($ads['image_redirect_url']).'">'
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
                }
              }

            }
        }
           return $content;
        }


function remove_ad_from_content($content,$ads,$ads_data='',$position='',$repeat_paragraph=false){

    $wp_charset = get_bloginfo( 'charset' );
     $tag = 'p[not(parent::blockquote)]';
     $doc =  new DOMDocument( '1.0', $wp_charset );
     libxml_use_internal_errors( true );
     $doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
      $xpath = new DOMXPath( $doc );
      $items = $xpath->query( '/html/body/' . $tag );
      $whitespaces = json_decode( '"\t\n\r \u00A0"' );
      foreach ( $items  as $item) {
        if (  ( isset( $item->textContent ) && trim( $item->textContent, $whitespaces ) !== '' ) ) { 
          $paragraphs[] = $item;
        }
      }
      $total_paragraphs = count($paragraphs);    
      if(isset($ads_data['after_the_percentage_value'])){
        $percentage       = intval($ads_data['after_the_percentage_value']);
        $position     = floor(($percentage / 100) * $total_paragraphs);
      }
if($repeat_paragraph){
      for ( $i = $position -1; $i < $total_paragraphs; $i++ ) {
        // Select every X number.
        if ( ( $i + 1 ) % $position === 0 )  {
          $offsets[] = $i;
        }
      }
                               foreach ( $offsets as $offset ) {

                        $ref_node  = $paragraphs[$offset]->nextSibling;
                        $ad_dom =  new DOMDocument( '1.0', $wp_charset );
                        libxml_use_internal_errors( true );
                        $ad_dom->loadHTML(mb_convert_encoding('<!DOCTYPE html><html><meta http-equiv="Content-Type" content="text/html; charset=' . $wp_charset . '" /><body>' . $ads, 'HTML-ENTITIES', 'UTF-8'));

                        foreach ( $ad_dom->getElementsByTagName( 'body' )->item( 0 )->childNodes as $importedNode ) {
                          $importedNode = $doc->importNode( $importedNode, true );
                          if($ref_node){
                            $ref_node->parentNode->insertBefore( $importedNode, $ref_node );
                          }
                        }
}
    }else{
      $ref_node  = $paragraphs[$position];

      $ad_dom =  new DOMDocument( '1.0', $wp_charset );
      libxml_use_internal_errors( true );
 $ad_dom->loadHTML(mb_convert_encoding('<!DOCTYPE html><html><meta http-equiv="Content-Type" content="text/html; charset=' . $wp_charset . '" /><body>' . $ads, 'HTML-ENTITIES', 'UTF-8'));

      foreach ( $ad_dom->getElementsByTagName( 'body' )->item( 0 )->childNodes as $importedNode ) {
        $importedNode = $doc->importNode( $importedNode, true );
          if($ref_node){
                $ref_node->parentNode->insertBefore( $importedNode, $ref_node );
              }
      }
    }
    $content =$doc->saveHTML();
    return $content;  
}
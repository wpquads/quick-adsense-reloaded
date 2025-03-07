<?php
/**
 * Scripts
 *
 * @package     QUADS
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;
add_action( 'wp_print_styles', 'quads_inline_styles', 9999 );
add_action('amp_post_template_css','quads_inline_styles_amp', 11);

add_action( 'admin_enqueue_scripts', 'quads_load_admin_scripts', 100 );
add_action( 'admin_enqueue_scripts', 'quads_load_plugins_admin_scripts', 100 );
add_action( 'admin_enqueue_scripts', 'quads_load_all_admin_scripts', 100 );

if( function_exists( quads_is_pro_active() ) ) {
    add_action( 'admin_enqueue_scripts', 'quads_load_admin_fonts', 100 );
}

add_action( 'admin_print_footer_scripts', 'quads_check_ad_blocker' );
add_action( 'wp_enqueue_scripts', 'click_fraud_protection' );
add_action( 'wp_enqueue_scripts', 'quads_tcf_2_integration' );

function quads_tcf_2_integration(){
    if(quads_is_amp_endpoint()){
        return;
    }
    global $quads_options;
    if(isset($quads_options['tcf_2_integration']) && !empty($quads_options['tcf_2_integration']) && $quads_options['tcf_2_integration']){



        $suffix = ( quadsIsDebugMode() ) ? '' : '.min'; 
        wp_enqueue_script( 'quads-tcf-2-scripts', QUADS_PLUGIN_URL . 'assets/js/tcf_2_integration' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );

        wp_localize_script( 'quads-tcf-2-scripts', 'quads_tcf_2',array( ) );

    }

}


function click_fraud_protection(){

    global $quads_options,$quads_mode;
    if($quads_mode == 'new'){
        $allowed_click = isset( $quads_options['allowed_click'] )? $quads_options['allowed_click'] : 3;
        $ban_duration = isset( $quads_options['ban_duration'] )? $quads_options['ban_duration'] : 7;
        $click_limit = isset( $quads_options['click_limit'] )? absint( $quads_options['click_limit'] ) : 3;

           if (isset($quads_options['click_fraud_protection']) && !empty($quads_options['click_fraud_protection']) && $quads_options['click_fraud_protection']  ) {  
                $suffix = ( quadsIsDebugMode() ) ? '' : '.min'; 
                wp_register_script('quads-scripts', QUADS_PLUGIN_URL . 'assets/js/fraud_protection' . $suffix . '.js', array ('jquery'),QUADS_VERSION, false );
                wp_localize_script( 'quads-scripts', 'quads', array(
                    'version'               => QUADS_VERSION,
                    'allowed_click'         => esc_attr($allowed_click),
                    'quads_click_limit'     => esc_attr($click_limit),
                    'quads_ban_duration'    => esc_attr($ban_duration),
                ) );

                if ( (!function_exists( 'ampforwp_is_amp_endpoint' ) || !function_exists( 'is_amp_endpoint' )) || (function_exists( 'ampforwp_is_amp_endpoint' ) && !ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && !is_amp_endpoint() ) {
                    wp_enqueue_script( 'quads-scripts');
                }
         
            } 
               
    }
}

/**
 *  Determines whether the current admin page is an QUADS admin page.
 *
 *  Only works after the `wp_loaded` hook, & most effective
 *  starting on `admin_menu` hook.
 *
 *  @since 1.9.6
 *  @return bool True if QUADS admin page.
 */
if(!function_exists('quads_is_admin_page')){
    function quads_is_admin_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is the dependant function to check if current admin page is an QUADS admin page
        $currentpage = isset($_GET['page']) ? $_GET['page'] : '';
        if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
            return false;
        }
        if ( 'quads-settings' == $currentpage ) {
            return true;
        }
    }
}
/**
 * Create ad blocker admin script
 * 
 * @return mixed boolean | string
 */
function quads_check_ad_blocker() {
    if( !quads_is_admin_page() ) {
        return false;
    }
    ?>
    <script>
        window.onload = function(){
        if (typeof wpquads_adblocker_check === 'undefined' || false === wpquads_adblocker_check) {
        if (document.getElementById('wpquads-adblock-notice')){
        document.getElementById('wpquads-adblock-notice').style.display = 'block';
                console.log('adblocker detected');
        }
        }
        }
    </script>
    <?php
}

function quads_show_adpushup_notice(){
     
        if( false !== get_option( 'quads_hide_adpushup_notice' ) ) {
            return false;
        }

        if( function_exists('quads_is_pro_active') && quads_is_pro_active() ){
        return false;
        }
    
        $message  = esc_html__( 'Get 30+ ad networks to compete for your ad inventory with Google Certified Publishing partner AdPushup.', 'quick-adsense-reloaded' );
        $message .= '  <a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_adpushup_notice" class="close_adpushup" target="_self" title="'.esc_html__( 'Close Notice', 'quick-adsense-reloaded' ).'" style="font-weight:bold;"><span class="screen-reader-text">'.esc_html__( 'Dismiss this notice', 'quick-adsense-reloaded').'</span></a>';
        $message .= '<br><br><a target="_blank" href="https://www.adpushup.com/publisher/wp-quads/" class="button-primary thankyou" target="_self" title="'.esc_html__( 'Close Notice', 'quick-adsense-reloaded' ).'" style="font-weight:bold;">'.esc_html__( 'Know More', 'quick-adsense-reloaded').'</a>';
        $message .= '  <a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_adpushup_notice" class="button-primary thankyou" target="_self" title="'.esc_html__( 'Close Notice', 'quick-adsense-reloaded' ).'" style="font-weight:bold;">'.esc_html__( 'Close Notice', 'quick-adsense-reloaded').'</a>';
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p><?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above */ echo $message; ?></p>
        </div>
        <?php

}

/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 1.0
 * @global $post
 * @param string $hook Page hook
 * @return void
 */
function quads_load_admin_scripts( $hook ) {

$quads_mode = get_option('quads-mode');
$screens = get_current_screen();

$currentScreen = '';
if(is_object($screens)){
    $currentScreen = $screens->base;    

    if($currentScreen == 'toplevel_page_quads-settings'){
        remove_all_actions('admin_notices');
        if($quads_mode == 'new'){
             add_action( 'admin_notices', 'quads_show_rate_div' );
             add_action( 'admin_notices', 'quads_admin_messages_new' );
             add_action( 'admin_notices', 'quads_admin_newdb_upgrade' );               
        }
        wp_enqueue_media();
        //To add page
        if ( ! class_exists( '_WP_Editors', false ) ) {
            require( ABSPATH . WPINC . '/class-wp-editor.php' );
        }
    }

    if($currentScreen == 'plugins' || $currentScreen == 'toplevel_page_quads-settings'){
        // add_action( 'admin_notices', 'quads_show_adpushup_notice' );
    }

}
      
       
          if($quads_mode != 'new'){
            add_action( 'admin_notices', 'quads_admin_messages' );
          }    

    global $current_user,$wp_version, $quads;
    $dismissed = explode (',', get_user_meta (wp_get_current_user ()->ID, 'dismissed_wp_pointers', true));                            
    $do_tour   = !in_array ('wpquads_subscribe_pointer', $dismissed);

    if ($do_tour) {
        wp_enqueue_style ('wp-pointer');
        wp_enqueue_script ('wp-pointer');                       
        $js_dir  = QUADS_PLUGIN_URL . 'assets/js/';
        wp_register_script( 'quads-newsletter', $js_dir . 'quads-newsletter.js', array('jquery'), QUADS_VERSION, false );
        wp_localize_script( 'quads-newsletter', 'quadsnewsletter', array(
        'current_user_email' => $current_user->user_email,
        'current_user_name' => $current_user->display_name,
        'do_tour'           => $do_tour,
        'path'           => get_site_url()

        ) );
        wp_enqueue_script('quads-newsletter');
    }
    $js_dir  = QUADS_PLUGIN_URL . 'assets/js/';
    $css_dir = QUADS_PLUGIN_URL . 'assets/css/';
        // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( quadsIsDebugMode() ) ? '' : '.min';
    wp_enqueue_script( 'quads-admin-scripts', $js_dir . 'quads-admin' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
         $quads_import_classic_ads_popup = false;
        $classic_ads_status = get_option( 'quads_import_classic_ads_popup' );
        if($classic_ads_status === false && $quads_mode === false){
            update_option('quads_import_classic_ads_popup', 'yes'); 
            $quads_import_classic_ads_popup = true;
        }elseif($classic_ads_status == 'yes'){
            $quads_import_classic_ads_popup = true;
        }
    wp_localize_script( 'quads-admin-scripts', 'quads', array(
        'nonce'         => wp_create_nonce( 'quads_ajax_nonce' ),
        'error'         => __( "error", 'quick-adsense-reloaded' ),
        'path'          => get_option( 'siteurl' ),
        'plugin_url'    => QUADS_PLUGIN_URL,
        'email'         => get_option( 'admin_email' ),
        'aid'           => 'WP_Quads',
        'quads_import_classic_ads_popup' => $quads_import_classic_ads_popup,
        'quads_get_active_ads' => quads_get_active_ads_backup()
    ) );
    if( !apply_filters( 'quads_load_admin_scripts', quads_is_admin_page(), $hook ) ) {
        return;
    }
    
    // These have to be global
    if(is_admin()){
        wp_enqueue_script( 'quads-admin-ads', $js_dir . 'ads.js', array('jquery'), QUADS_VERSION, false );
    }
    wp_enqueue_script( 'quads-jscolor', $js_dir . 'jscolor' . $suffix . '.js', array(), QUADS_VERSION, false );
    wp_enqueue_script( 'jquery-chosen', $js_dir . 'chosen.jquery' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
    wp_enqueue_script( 'jquery-form' );
    wp_enqueue_style( 'quads-admin', $css_dir . 'quads-admin' . $suffix . '.css',array(), QUADS_VERSION );
    wp_enqueue_style( 'jquery-chosen', $css_dir . 'chosen' . $suffix . '.css',array(), QUADS_VERSION );


}

/**
 * Load Admin Scripts available on plugins page 
 *
 * Enqueues the required admin scripts.
 *
 * @since 1.0
 * @global $post
 * @param string $hook Page hook
 * @return void
 */
function quads_load_plugins_admin_scripts( $hook ) {
    if( !apply_filters( 'quads_load_plugins_admin_scripts', quads_is_plugins_page(), $hook ) ) {
        return;
    }

    $js_dir  = QUADS_PLUGIN_URL . 'assets/js/';
    $css_dir = QUADS_PLUGIN_URL . 'assets/css/';

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( quadsIsDebugMode() ) ? '' : '.min';

    wp_enqueue_script( 'quads-plugins-admin-scripts', $js_dir . 'quads-plugins-admin' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
    wp_enqueue_style( 'quads-plugins-admin', $css_dir . 'quads-plugins-admin' . $suffix . '.css', array(),QUADS_VERSION );
}

/**
 * Load Admin Scripts available on all admin pages 
 *
 * Enqueues the required admin scripts.
 *
 * @since 1.6.1
 * @global $post
 * @param string $hook Page hook
 * @return void
 */
function quads_load_all_admin_scripts( $hook ) {


    $css_dir = QUADS_PLUGIN_URL . 'assets/css/';

    wp_enqueue_style( 'quads-admin-all', $css_dir . 'quads-admin-all.css',array(), QUADS_VERSION );
}

function quads_load_admin_fonts( $hook ) {
    
    $font_url = QUADS_PLUGIN_URL.'admin/assets/js';
    $font_styles= '<style>
    @font-face {
    font-family: "quads-icomoon";
    src: url("../fonts/icomoon.eot");
    src: url("../fonts/icomoon.eot?#iefix") format("embedded-opentype"), url("'.$font_url.'/fonts/icomoon.woff") format("woff"), url("../fonts/icomoon.ttf") format("truetype"), url("../fonts/icomoon.svg#icomoon") format("svg");
    font-weight: normal;
    font-style: normal;
}
[class^="quads-icon-"]:before,
[class*=" quads-icon-"]:after,
[class^="quads-icon-"]:after,
[class*=" quads-icon-"]:before,
[id^="quads-nav-"]:before,
[id*=" quads-nav-"]:after,
[id^="quads-nav-"]:after,
[id*=" quads-nav-"]:before {
    font-family: "quads-icomoon";
    speak: none;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
[class^="quads-icon-"]:before, [class*=" quads-icon-"]:after, [class^="quads-icon-"]:after, [class*=" quads-icon-"]:before, [id^="quads-nav-"]:before, [id*=" quads-nav-"]:after, [id^="quads-nav-"]:after, [id*=" quads-nav-"]:before {
    font-family: \'quads-icomoon\';
    speak: none;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

    </style>';

    if( isset($hook) && $hook == "admin.php" ) {
        //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $font_styles ;
    } 
}

/**
 * Add dynamic CSS to write media queries for removing unwanted ads without the need to use any cache busting method
 * (Cache busting could affect performance and lead to lot of support tickets so lets follow the css approach)
 *
 * @since 1.0
 * @global1 array options
 * @global2 $quads_css dynamic build css
 * 
 * @return string
 */
function quads_inline_styles() {
    $quads_ads = quads_api_services_cllbck();
    $ad_loaded = $is_sticky_loaded = $sticky_show_hide = $is_sticky_anim = false;
    $ads_types = [];
    $css = '';
    $is_sticky_pos = 'bottom';
    $is_sticky_cls_pos = 'top';

    if( isset( $quads_ads['posts_data'] ) ) {
        foreach ( $quads_ads['posts_data'] as $key => $value ) {
            $css .= quads_render_media_query( $key, $value );
            $ads =$value['post_meta'];
            if($value['post']['post_status']== 'draft'){
                continue;
            }
            if(isset($ads['visibility_include']) && is_string($ads['visibility_include'])){$ads['visibility_include'] = unserialize($ads['visibility_include']);}
            if(isset($ads['visibility_exclude']) && is_string($ads['visibility_exclude'])){$ads['visibility_exclude'] = unserialize($ads['visibility_exclude']);}
            if(isset($ads['targeting_include']) && is_string($ads['targeting_include'])){$ads['targeting_include'] = unserialize($ads['targeting_include']);}
            if(isset($ads['targeting_exclude']) && is_string($ads['targeting_exclude'])){$ads['targeting_exclude'] = unserialize($ads['targeting_exclude']);}
            $is_on         = quads_is_visibility_on($ads);
            $is_visitor_on = quads_is_visitor_on($ads);
           if(isset($ads['ad_id'])){$post_status = get_post_status($ads['ad_id']);}else{$post_status =  'publish';}
           if(!isset($ads['position']) || isset($ads['ad_type']) && $ads['ad_type']== 'random_ads'){
               $is_on = true;
           } 
           $is_on=apply_filters('quads_show_ads',quads_ad_is_allowed());
           if($is_on && $is_visitor_on && $post_status=='publish'){
            $ad_loaded = true;
            if(isset($ads['ad_type'])){
                $ads_types[]=$ads['ad_type'];
            }
            if(isset($ads['position']) && $ads['position'] == 'ad_sticky_ad'){
                $is_sticky_loaded = true;
                if(isset($ads['sticky_slide_ad']) && $ads['sticky_slide_ad'] == 'sticky_ad_top'){
                    $is_sticky_pos = 'top';
                    $is_sticky_cls_pos = 'bottom';
                }
                if(isset($ads['sticky_ad_show_hide']) && $ads['sticky_ad_show_hide'] == 1){
                    $sticky_show_hide = true;
                }
                if(isset($ads['sticky_ad_anim']) && $ads['sticky_ad_anim'] == 1){
                    $is_sticky_anim = true;
                }
            }
           }
            
        }
        if(!$ad_loaded){
            return ''; 
        }
    }
    else{
        return '';
    }
    if(is_array($ads_types) && !empty($ads_types)){
        $ads_types=array_unique($ads_types);
    }
    if ((function_exists('quads_delay_ad_sec') && quads_delay_ad_sec()) && !quads_is_amp_endpoint()){
        $css .="
        .quads-location {
            visibility: hidden;
        }";
    }

    $css .="
    .quads-location ins.adsbygoogle {
        background: transparent !important;
    }.quads-location .quads_rotator_img{ opacity:1 !important;}
    .quads.quads_ad_container { display: grid; grid-template-columns: auto; grid-gap: 10px; padding: 10px; }
    .grid_image{animation: fadeIn 0.5s;-webkit-animation: fadeIn 0.5s;-moz-animation: fadeIn 0.5s;
        -o-animation: fadeIn 0.5s;-ms-animation: fadeIn 0.5s;}
    .quads-ad-label { font-size: 12px; text-align: center; color: #333;}
    .quads_click_impression { display: none;} .quads-location, .quads-ads-space{max-width:100%;} @media only screen and (max-width: 480px) { .quads-ads-space, .penci-builder-element .quads-ads-space{max-width:340px;}}";
    if(in_array("popup_ads", $ads_types)){
            $css .=".quads-popupad {
                position: fixed;
                top: 0px;
                left:0px;
                width: 100%;
                height: 100em;
                background-color: rgba(0,0,0,0.6);
                z-index: 999;
                max-width: 100em !important;
                margin: 0 auto;
            }
            .quads.quads_ad_container_ {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            .quads_load_on_top .quads_ad_container_{
                position: fixed;
                top: 10px;
                left: 50%;
                transform: translate(-50%, 0);

            }
            .quads_load_on_bottom .quads_ad_container_{
                position: fixed;
                bottom: 5px;
                left: 50%;
                transform: translate(-50%, 0);

            }
            .quads_load_on_top .quads_ad_container_{
                animation:animatetop 0.5s
            }
            @keyframes animatetop{from{top:-300px;opacity:0} to{top:0;opacity:1}}
            .quads_load_on_bottom .quads_ad_container_{
                animation:animatebottom 0.5s
            }

            .quads_load_on_top , .quads_load_on_bottom{
                height:auto;
            }
            @keyframes animatebottom{from{bottom:-300px;opacity:1} to{bottom:0;opacity:1}}

            #btn_close{
                background-color: #fff;
                width: 25px;
                height: 25px;
                text-align: center;
                line-height: 22px;
                position: absolute;
                right: -10px;
                top: -10px;
                cursor: pointer;
                transition: all 0.5s ease;
                border-radius: 50%;
            } @media screen and (max-width: 480px) {
                .quads.quads_ad_container_ {
                    left: 10px;
                }
            }"; 
    }

    if(in_array("video_ads", $ads_types)){
            $css .="#btn_close_video{
                background-color: #fff;
                width: 25px;
                height: 25px;
                text-align: center;
                line-height: 22px;
                position: absolute;
                right: -10px;
                top: -10px;
                cursor: pointer;
                transition: all 0.5s ease;
                border-radius: 50%;
                z-index:100;
            }
            .quads-video {
                position: fixed;
                bottom: 0px;
                z-index: 9999999;
            }
            .quads_ad_container_video{
                max-width:220px;
            }";
    }
    if(in_array("half_page_ads", $ads_types)){ 
            $css .=".post_half_page_ad{
                visibility: visible;
                position: fixed;
                top: 0;
                right: -200vw;
            }
            #post_half_page_openClose {
                -webkit-transform: rotate(90deg);
                -webkit-transform-origin: left top;
                -moz-transform: rotate(90deg);
                -moz-transform-origin: left top;
                -o-transform: rotate(90deg);
                -o-transform-origin: left top;
                -ms-transform: rotate(90deg);
                -ms-transform-origin: left top;
                -transform: rotate(90deg);
                -transform-origin: left top;
                position: absolute;
                left: 4px;
                top: 0%;
                cursor: pointer;
                z-index: 999999;
                display: none;
            }
            #post_half_pageVertical-text {
                background: #000000;
                text-align: center;
                z-index: 999999;
                cursor: pointer;
                color: #FFFFFF;
                float: left;
                font-size: 13pt;
                padding: 5px;
                font-weight: bold;
                width: 85vh;
                font-family: verdana;
                text-transform: uppercase;
            }
            .half-page-arrow-left {
                position: absolute;
                cursor: pointer;
                width: 0;
                height: 0;
                border-right: 15px solid #FFFFFF;
                border-top: 15px solid transparent;
                border-bottom: 15px solid transparent;
                left: -27px;
                z-index: 9999999;
                top: 8vh;
            }
            .half-page-arrow-right {
                position: absolute;
                cursor: pointer;
                width: 0;
                height: 0;
                border-left: 15px solid #FFFFFF;
                border-top: 15px solid transparent;
                border-bottom: 15px solid transparent;
                left: -25px;
                z-index: 9999999;
                bottom: 30vh;
            }
            @media screen and (max-width: 520px) {
                .post_half_page_ad {
                    display: none;
                }
                #post_half_pageVertical-text {
                    width: 100%;
                    font-size: 14px;
                }
                .half-page-arrow-left{
                    left: 12px;
                    bottom: 8px;
                    top: 12px;
                    border-left: 10px solid #ffffff00;
                    border-top: none;
                    border-bottom: 10px solid white;
                    border-right: 10px solid #ffffff00;
                }
                .half-page-arrow-right {
                    border-left: 10px solid #ffffff00;
                    border-top: 10px solid white;
                    border-bottom: none;
                    border-right: 10px solid #ffffff00;
                    right: 12px;
                    left: unset;
                    top: 13px;
                    bottom: 8px;
                }
            }";
    }
    if(in_array("floating_cubes", $ads_types)){ 
            $css .=".wpquads-3d-container {
                border-radius:3px;
                position:relative;
                -webkit-perspective:1000px;
                -moz-perspective:1000px;
                -ms-perspective:1000px;
                -o-perspective:1000px;
                perspective:1000px;
                z-index:999999;
            }
            .wpquads-3d-cube{
                width:100%;
                height:100%;
                position:absolute;
                -webkit-transition:-webkit-transform 1s;
                -moz-transition:-moz-transform 1s;
                -o-transition:-o-transform 1s;
                transition:transform 1s;
                -webkit-transform-style:preserve-3d;
                -moz-transform-style:preserve-3d;
                -ms-transform-style:preserve-3d;
                -o-transform-style:preserve-3d;
                transform-style:preserve-3d;
            }
            .wpquads-3d-cube .wpquads-3d-item{
                position:absolute;
                border:3px inset;
                border-style:outset
            }
            .wpquads-3d-close{
                text-align:right;
            }
            #wpquads-close-btn{
                text-decoration:none !important;
                cursor:pointer;
            }
            #wpquads-close-btn svg{
                padding: 5px;
            }
            .wpquads-3d-cube .wpquads-3d-item, .wpquads-3d-cube .wpquads-3d-item img{
                display:block;
                margin:0;
                width:100%;
                height:100%;
                background:#fff;
            }
            .ewd-ufaq-faqs .wpquads-3d-container {
                display: none;
            }  ";
    } 
    if(in_array("parallax_ads", $ads_types)){ 
            $css .=".parallax_main {
                padding-left: 3px;
                padding-right: 3px;
            }
            .parallax_main {
                display:none;
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
                background: #00000070;
                overflow-y: auto;
                background-attachment: fixed;
                background-position: center;
                -moz-transition: all 0.3s ease-in-out;
                -o-transition: all 0.3s ease-in-out;
                -ms-transition: all 0.3s ease-in-out;
                -webkit-transition: all 0.3s ease-in-out;
                transition: all 03s ease-in-out;
            }
            .parallax_main .quads-parallax-spacer {
                display: block;
                padding-top: 100vh;
                position: relative;
                pointer-events: none;
            }
            .quads-parallax {
                bottom: 0px;
                z-index: 9999999;
                bottom: 0;
                left: 0;
                right: 0; 
                margin: 0 auto;
                position:relative;
                -moz-transition: all 0.3s ease;
                -o-transition: all 0.3s ease;
                -ms-transition: all 0.3s ease;
                -webkit-transition: all 0.3s ease;
                transition: all 0.3s ease;
            }
            .parallax_popup_img {
                text-align: center;
                margin: 0 auto;
            }
            .quads_parallax_scroll_text{
                display: none;
                position: fixed;
                left: 0;
                z-index: 1;
                color: #989898;
                right: 0;
                text-align: center;
                font-weight: 600;
                font-size: 15px;
                background: #fff;
                padding: 6px;
                top: 5px;
            }";
    }
    if(in_array("sticky_scroll", $ads_types) || $is_sticky_loaded){ 
            $css .=".quads-sticky {
                width: 100% !important;
                position: fixed;
                max-width: 100%!important;
                ".$is_sticky_pos.":0;
                margin:0;
                left:0;
                text-align: center;
                opacity: 0;
                z-index:999;
                background-color: rgb(0 0 0 / 70%);
            }
            .quads-sticky.active {
                opacity: 1;
            }
            .quads-sticky.active .quads-ad-label-new {
                color:#fff
            }
            .quads-sticky .quads-location {
                text-align: center;
                background-color: unset !important;
            }.quads-sticky .wp_quads_dfp {
                display: contents;
            }
            a.quads-sticky-ad-close {
                background-color: #fff;
                width: 25px;
                height: 25px;
                text-align: center;
                line-height: 22px;
                position: absolute;
                right: 35px;
                ".$is_sticky_cls_pos.": -15px;
                cursor: pointer;
                transition: all 0.5s ease;
                border-radius: 50%;
            }";
        if($is_sticky_anim){
            $per_anim = '100%';
            if($is_sticky_pos == 'top'){
                $per_anim = '-50%';
            }
            $css .=".quads-sticky {
                transition: .5s;
                -webkit-transform: translate(0,".$per_anim.");
                -moz-transform: translate(0,".$per_anim.");
                -ms-transform: translate(0,".$per_anim.");
                -o-transform: translate(0,".$per_anim.");
                transform: translate(0,".$per_anim.");
            }
            .quads-sticky.active {
                -webkit-transform: translate(0,0);
                -moz-transform: translate(0,0);
                -ms-transform: translate(0,0);
                -o-transform: translate(0,0);
                transform: translate(0,0);
            }";
        }
        if($sticky_show_hide){
            $css .= '.quads-sticky-show-btn {
                visibility: hidden;
            }
            .quads-sticky-show-btn.active {
                font-size: 10px;
                position: fixed;
                text-align: center;
                cursor: pointer;
                border-radius: 50%;
                background-color: #000;
                color: #fff;
                right: 20px;
                '.$is_sticky_pos.': -8px;
                width: 50px;
                padding: 15px;
                line-height: 12px;
                text-transform: uppercase;
                opacity: 0.7;
                z-index: 99;
                visibility: visible;
            }';
        }
    }
    if(in_array("carousel_ads", $ads_types)){ 
        $css .="@media only screen and (max-width: 480px) {
            .quads_carousel_img {
                 width:100%
            }
        }
         .quads_carousel_img {
             width:auto;
        }
         .quads-slides{
            display:none
        }
         .quads-container:after,.quads-container:before {
            content:'';
            display:table;
            clear:both
        }
         .quads-container{
            padding:.01em 16px
        }
         .quads-content{
            margin-left:auto;
            margin-right:auto;
            max-width:100%
        }
         .quads-section{
            margin-top:16px!important;
            margin-bottom:16px!important
        }
         .quads-animate-right{
            position:relative;
            animation: animateright 0.5s
        }
        .quads-animate-left{
            position:relative;
            animation: animateleft 0.5s
        }
        .quads_carousel_back {
            background: #000;
            color: #fff;
            padding: 0 8px;
            border-radius: 50%;
            position: absolute;
            z-index: 999;
            top: 48%;
            left:5px;
            cursor: pointer;
            font-weight:bold;
        }
        .quads_carousel_close {
            background: #000;
            color: #fff;
            padding: 0 8px;
            border-radius: 50%;
            position: absolute;
            z-index: 999;
            top: 0;
            right:5px;
            cursor: pointer;
            font-weight:bold;
        }
        .quads-carousel-container{
            position:relative;
        }
       .quads_carousel_next{
            background: #000;
            color: #fff;
            padding: 0 8px;
            border-radius: 50%;
            position: absolute;
            z-index: 999;
            top: 48%;
            right:5px;
            cursor: pointer;
            font-weight:bold;
        }
        .quads-slides:first-of-type{
            display:block;
        }
         @keyframes animateright{
            from{
                right:-300px;
                opacity:0
            }
            to{
                right:0;
                opacity:1
            }
        }
        @keyframes animateleft{
            from{
                left:-300px;
                opacity:0
            }
            to{
                left:0;
                opacity:1
            }
        }
        ";
}
    // Register empty style so we do not need an external css file
    wp_register_style( 'quads-styles', false );
    // Enque empty style
    wp_enqueue_style( 'quads-styles' );
    // Add inline css to that style
    wp_add_inline_style( 'quads-styles', $css );
}
function quads_inline_styles_amp() {
    global $quads_options;

    $css = '';

    if( isset( $quads_options['ads'] ) ) {
        foreach ( $quads_options['ads'] as $key => $value ) {
            $css .= quads_render_media_query( $key, $value );
        }
    }
    $css .=".quads-ad-label { font-size: 12px; text-align: center; color: #333;}";

    if (quads_is_amp_endpoint()){
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $css;
    }
}


/**
 * Render Media Queries
 * 
 * @param string $key
 * @param string $value
 * @return string
 */
function quads_render_media_query( $key, $value ) {
    $lic = get_option( 'quads_wp_quads_pro_license_active' );
    if( !$lic || (is_object( $lic ) && isset($lic->success) && $lic->success !== true) ) {
        return '';
    }

    $html = '';

    if( isset( $value['desktop'] ) ) {
        //$html .= '/* Hide on desktop */'; 
        $html .= '@media only screen and (min-width:1140px){#quads-' . $key . ', .quads-' . $key . ' {display:none;}}' . "\n";
    }
    if( isset( $value['tablet_landscape'] ) ) {
        //$html .= '/* Hide on tablet landscape */'; 
        $html .= '@media only screen and (min-width:1024px) and (max-width:1140px) {#quads-' . $key . ', .quads-' . $key . ' {display:none;}}' . "\n";
    }
    if( isset( $value['tablet_portrait'] ) ) {
        //$html .= '/* Hide on tablet portrait */'; 
        $html .= '@media only screen and (min-width:768px) and (max-width:1023px){#quads-' . $key . ', .quads-' . $key . ' {display:none;}}' . "\n";
    }
    if( isset( $value['phone'] ) ) {
        //$html .= '/* Hide on mobile device */'; 
        $html .= '@media only screen and (max-width:767px){#quads-' . $key . ', .quads-' . $key . ' {display:none;}}' . "\n";
    }

    return $html;
}

/*
 * Check if debug mode is enabled
 * 
 * @since 0.9.0
 * @return bool true if Mashshare debug mode is on
 */

function quadsIsDebugMode() {
    global $quads_options;

    $debug_mode = (isset( $quads_options['debug_mode'] ) && $quads_options['debug_mode'] ) ? true : false;
    return $debug_mode;
}

function quads_delay_ad_sec() {
    global $quads_options;
    $delay_ad_sec = (isset( $quads_options['delay_ad_sec'] ) && $quads_options['delay_ad_sec'] ) ? true : false;
    return apply_filters('quads_delay_ad_sec_filter', $delay_ad_sec);
}

/**
 * Create ad buttons for editor
 * 
 * @author Tedd Garland, René Hermenau
 * @since 0.9.0
 */
$wpvcomp = ( bool ) (version_compare( get_bloginfo( 'version' ), '3.1', '>=' ));

function quads_ads_head_script() {
    global $quads_options, $wpvcomp;

    if( isset( $quads_options['quicktags']['QckTags'] ) ) {
        ?>
        <script>
        wpvcomp = <?php echo (($wpvcomp == 1) ? "true " : "false"); ?>;
        edaddID = new Array();
        edaddNm = new Array();
        if (typeof (edButtons) != 'undefined') {         edadd = edButtons.length;
        var dynads = {"all":[
        <?php
        for ( $i = 1; $i <= count( quads_get_ads() ) - 1; $i++ ) {
            if( isset( $quads_options['ads']['ad' . $i]['code'] ) && $quads_options['ads']['ad' . $i]['code'] != '' ) {
                echo('"1",');
            } else {
                echo('"0",');
            };
        }
        ?>
        "0"] };
        for (i = 1; i <=<?php echo count( quads_get_ads() ) - 1; ?>; i++) {
        if (dynads.all[ i - 1 ] == "1") {
        edButtons [edButtons.length] = new edButton("ads" + i.toString(), " Ads" + i.toString(), "\n<!--Ads"+i.toString()+"-->\n", "", "", - 1);
        edaddID[edaddID.length] = " ads" + i.toString();
        edaddNm[edaddNm.length] = "Ads" + i.toString();
        }
        }
        <?php if( !isset( $quads_options['quicktags']['QckRnds'] ) ) { ?>
            edButtons[edButtons.length] = new edButton("random_ads", " RndAds", "\n<!--RndAds-->\n", "", "", - 1);
            edaddID[edaddID.length] = "random_ads";
            edaddNm[edaddNm.length] = "RndAds";
        <?php } ?>
        edButtons[edButtons.length] = new edButton("no_ads", "NoAds", "\n<!--NoAds-->\n","","",-1);
            edaddID[edaddID.length] = "no_ads";
                            edaddNm[edaddNm.length] = "NoAds";			
        };
        (function(){
        if(typeof(edButtons)!='undefined' && typeof(jQuery)!='undefined' && wpvcomp){
            jQuery(document).ready(function(){
                    for(i=0;i<edaddID.length;i++) {
                            jQuery("#ed_toolbar").append('<input type="button" value="' + edaddNm[i] +'" id="' + edaddID[i] +'" class="ed_button" onclick="edInsertTag(edCanvas, ' + (edadd+i) + ');" title="' + edaddNm[i] +'" />');
                    }
            });
        }
        }());	
        </script> 
        <?php
    }
}

if( $wpvcomp ) {
    add_action( 'admin_print_footer_scripts', 'quads_ads_head_script' );
} else {
    add_action( 'admin_head', 'quads_ads_head_javascript_script' );
}
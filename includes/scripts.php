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

//add_action( 'wp_enqueue_scripts', 'quads_register_styles', 10 );
add_action( 'wp_print_styles', 'quads_inline_styles', 9999 );
add_action('amp_post_template_css','quads_inline_styles_amp', 11);

add_action( 'admin_enqueue_scripts', 'quads_load_admin_scripts', 100 );
add_action( 'admin_enqueue_scripts', 'quads_load_plugins_admin_scripts', 100 );
add_action( 'admin_enqueue_scripts', 'quads_load_all_admin_scripts', 100 );
add_action( 'admin_enqueue_scripts', 'quads_load_admin_fonts', 100 );
add_action( 'admin_print_footer_scripts', 'quads_check_ad_blocker' );
add_action( 'wp_enqueue_scripts', 'click_fraud_protection' );
add_action( 'wp_enqueue_scripts', 'tcf_2_integration' );

function tcf_2_integration(){
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
                if ( (function_exists( 'ampforwp_is_amp_endpoint' ) && !ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && !is_amp_endpoint() ) {
              wp_enqueue_script( 'quads-scripts', QUADS_PLUGIN_URL . 'assets/js/fraud_protection' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
                }
         
            } 
                wp_localize_script( 'quads-scripts', 'quads', array(
                    'version'               => QUADS_VERSION,
                    'allowed_click'         => esc_attr($allowed_click),
                    'quads_click_limit'     => esc_attr($click_limit),
                    'quads_ban_duration'    => esc_attr($ban_duration),
                ) );
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
    <script type="text/javascript">
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
    
        $message  = __( 'Get 30+ ad networks to compete for your ad inventory with Google Certified Publishing partner AdPushup.', 'quick-adsense-reloaded' );
        $message .= '  <a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_adpushup_notice" class="close_adpushup" target="_self" title="Close Notice" style="font-weight:bold;"><span class="screen-reader-text">Dismiss this notice.</span></a>';
        $message .= '<br><br><a target="_blank" href="https://www.adpushup.com/publisher/wp-quads/" class="button-primary thankyou" target="_self" title="Close Notice" style="font-weight:bold;">Know More</a>';
        $message .= '  <a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_adpushup_notice" class="button-primary thankyou" target="_self" title="Close Notice" style="font-weight:bold;">Close Notice</a>';
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p><?php echo $message; ?></p>
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
        }
        wp_enqueue_media();
        //To add page
        if ( ! class_exists( '_WP_Editors', false ) ) {
            require( ABSPATH . WPINC . '/class-wp-editor.php' );
        }
    }

    if($currentScreen == 'plugins' || $currentScreen == 'toplevel_page_quads-settings'){
        add_action( 'admin_notices', 'quads_show_adpushup_notice' );
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
    $signupURL = $quads->vi->getSettings()->data->signupURL;
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
        'vi_revenue'    => !empty( $quads->vi->getRevenue()->mtdReport ) ? $quads->vi->getRevenue()->mtdReport : '',
        'vi_login_url'  => $quads->vi->getLoginURL(),
        'vi_signup_url' => !empty( $signupURL ) ? $signupURL : '',
        'domain'        => $quads->vi->getDomain(),
        'email'         => get_option( 'admin_email' ),
        'aid'           => 'WP_Quads',
        'quads_import_classic_ads_popup' => $quads_import_classic_ads_popup,
        'quads_get_active_ads' => quads_get_active_ads_backup()
    ) );
    if( !apply_filters( 'quads_load_admin_scripts', quads_is_admin_page(), $hook ) ) {
        return;
    }
    
    // These have to be global
    wp_enqueue_script( 'quads-admin-ads', $js_dir . 'ads.js', array('jquery'), QUADS_VERSION, false );
    wp_enqueue_script( 'quads-jscolor', $js_dir . 'jscolor' . $suffix . '.js', array(), QUADS_VERSION, false );
    wp_enqueue_script( 'jquery-chosen', $js_dir . 'chosen.jquery' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
    wp_enqueue_script( 'jquery-form' );

    $vi_dir = QUADS_PLUGIN_URL . 'includes/vendor/vi/public/js/';
    wp_enqueue_script( 'quads-vi', $vi_dir . 'vi.js', array(), QUADS_VERSION, false );
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

function quads_load_admin_fonts( ) {
    $font_url = QUADS_PLUGIN_URL.'admin/assets/js';
    echo '<style>
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

}


/**
 * Register CSS Styles
 *
 * Checks the styles option and hooks the required filter.
 *
 * @since 1.0
 * @global $mashsb_options
 * @return void
 */
//function quads_register_styles( $hook ) {
//    global $quads_options;
//
//    // Register empty quads.css to be able to register quads_inline_styles()
//    //$url = QUADS_PLUGIN_URL . 'assets/css/quads.css';
//
//    //wp_enqueue_style( 'quads-styles', $url, array(), QUADS_VERSION );
//    wp_enqueue_style( 'quads-styles', false );
//}

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
    global $quads_options;

    $css = '';

    if( isset( $quads_options['ads'] ) ) {
        foreach ( $quads_options['ads'] as $key => $value ) {
            $css .= quads_render_media_query( $key, $value );
        }
    }
    $css .="
    .quads-location ins.adsbygoogle {
        background: transparent !important;
    }
    
    .quads.quads_ad_container { display: grid; grid-template-columns: auto; grid-gap: 10px; padding: 10px; }
    .grid_image{animation: fadeIn 0.5s;-webkit-animation: fadeIn 0.5s;-moz-animation: fadeIn 0.5s;
        -o-animation: fadeIn 0.5s;-ms-animation: fadeIn 0.5s;}
    .quads-ad-label { font-size: 12px; text-align: center; color: #333;}
    .quads_click_impression { display: none;}";
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
    if( !$lic || (is_object( $lic ) && $lic->success !== true) ) {
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
        <script type="text/javascript">
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

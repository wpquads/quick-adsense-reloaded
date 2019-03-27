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

add_action( 'admin_enqueue_scripts', 'quads_load_admin_scripts', 100 );
add_action( 'admin_enqueue_scripts', 'quads_load_plugins_admin_scripts', 100 );
add_action( 'admin_enqueue_scripts', 'quads_load_all_admin_scripts', 100 );
add_action( 'admin_print_footer_scripts', 'quads_check_ad_blocker' );

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
    if( !apply_filters( 'quads_load_admin_scripts', quads_is_admin_page(), $hook ) ) {
        return;
    }
    global $wp_version, $quads;

    $js_dir  = QUADS_PLUGIN_URL . 'assets/js/';
    $css_dir = QUADS_PLUGIN_URL . 'assets/css/';

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( quadsIsDebugMode() ) ? '' : '.min';


    // These have to be global
    wp_enqueue_script( 'quads-admin-ads', $js_dir . 'ads.js', array('jquery'), QUADS_VERSION, false );
    wp_enqueue_script( 'quads-admin-scripts', $js_dir . 'quads-admin' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
    wp_enqueue_script( 'quads-jscolor', $js_dir . 'jscolor' . $suffix . '.js', array(), QUADS_VERSION, false );
    wp_enqueue_script( 'jquery-chosen', $js_dir . 'chosen.jquery' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
    wp_enqueue_script( 'jquery-form' );

    $vi_dir = QUADS_PLUGIN_URL . 'includes/vendor/vi/public/js/';
    wp_enqueue_script( 'quads-vi', $vi_dir . 'vi.js', array(), QUADS_VERSION, false );


    wp_enqueue_style( 'quads-admin', $css_dir . 'quads-admin' . $suffix . '.css', QUADS_VERSION );
    wp_enqueue_style( 'jquery-chosen', $css_dir . 'chosen' . $suffix . '.css', QUADS_VERSION );

    $signupURL = $quads->vi->getSettings()->data->signupURL;

    wp_localize_script( 'quads-admin-scripts', 'quads', array(
        'nonce'         => wp_create_nonce( 'quads_ajax_nonce' ),
        'error'         => __( "error", 'quick-adsense-reloaded' ),
        'path'          => get_option( 'siteurl' ),
        'vi_revenue'    => !empty( $quads->vi->getRevenue()->mtdReport ) ? $quads->vi->getRevenue()->mtdReport : '',
        'vi_login_url'  => $quads->vi->getLoginURL(),
        'vi_signup_url' => !empty( $signupURL ) ? $signupURL : '',
        'domain'        => $quads->vi->getDomain(),
        'email'         => get_option( 'admin_email' ),
        'aid'           => 'WP_Quads'
    ) );
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
    wp_enqueue_style( 'quads-plugins-admin', $css_dir . 'quads-plugins-admin' . $suffix . '.css', QUADS_VERSION );
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

    wp_enqueue_style( 'quads-admin-all', $css_dir . 'quads-admin-all.css', QUADS_VERSION );
}

/**
 * Create Gutenberg block
 */
//function quads_load_blocks() {
//    $js_dir = QUADS_PLUGIN_URL . 'assets/js/';
//
//    wp_register_script( 'wpquads', $js_dir . 'blocks.js', array('wp-blocks', 'wp-element', 'wp-editor') );
//
//    register_block_type( 'wpquads/blocks', array(
//        'editor_script' => 'wpquads',
//    ) );
//}
//
//add_action( 'init', 'quads_load_blocks' );


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
    // Register empty style so we do not need an external css file
    wp_register_style( 'quads-styles', false );
    // Enque empty style
    wp_enqueue_style( 'quads-styles' );
    // Add inline css to that style
    wp_add_inline_style( 'quads-styles', $css );
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

    $debug_mode = isset( $quads_options['debug_mode'] ) ? true : false;
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

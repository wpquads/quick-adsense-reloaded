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

add_action( 'wp_enqueue_scripts', 'quads_register_styles', 10 );
add_action( 'wp_print_styles', 'quads_inline_styles', 9999 );
add_action( 'admin_enqueue_scripts', 'quads_load_admin_scripts', 100 );

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
    global $wp_version;

    $js_dir = QUADS_PLUGIN_URL . 'assets/js/';
    $css_dir = QUADS_PLUGIN_URL . 'assets/css/';

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( quadsIsDebugMode() ) ? '' : '.min';

    // These have to be global
    wp_enqueue_script( 'quads-admin-scripts', $js_dir . 'quads-admin' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
    wp_enqueue_script( 'jquery-form' );
    wp_enqueue_style( 'quads-admin', $css_dir . 'quads-admin' . $suffix . '.css', QUADS_VERSION );

    wp_localize_script( 'quads-admin-scripts', 'quads', array(
        'nonce' => wp_create_nonce( 'quads_ajax_nonce' ),
        'error' => __( "error", 'wpstg' ),
        'path' => get_option( 'siteurl' ),
    ) );
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
function quads_register_styles( $hook ) {
    global $quads_options;

    // Register empty quads.css to be able to register quads_inline_styles()
    $url = QUADS_PLUGIN_URL . 'assets/css/quads.css';

    wp_enqueue_style( 'quads-styles', $url, array(), QUADS_VERSION );
}

/**
 * Add dynamic CSS to write media queries for removing unwanted ads without the need to use any cache busting method
 * that could affect performance and lead to lot of support tickets
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
    
    foreach ($quads_options as $key => $value){
        $css .= quads_render_media_query($key, $value);
    }
    
    wp_add_inline_style( 'quads-styles', $css );
}

/**
 * Render Media Queries
 * 
 * @param string $key
 * @param string $value
 * @return string
 */
function quads_render_media_query($key, $value){
 
        $html = '';
        
    if (isset($value['desktop']) ){
        $html .= '@media only screen and (min-width:1140px){.quads-'.$key.' {display:none;}}'. "\n";
    }
    if (isset($value['tablet_landscape']) ){
        $html .= '@media only screen and (min-width:1019px) and (max:width:1140px) {.quads-'.$key.' {display:none;}}' . "\n";
    }
    if (isset($value['tablet_portrait']) ){
        $html .= '@media only screen and (min-width:768px) and (max-width:1019px){.quads-'.$key.' {display:none;}}' . "\n";
    }
    if (isset($value['phone']) ){
        $html .= '@media only screen and (max-width:768px){.quads-'.$key.' {display:none;}}' . "\n";
    }
    
    return $html;
}

/*
 * Check if debug mode is enabled
 * 
 * @since 0.9.0
 * @return bool true if Mashshare debug mode is on
 */
function quadsIsDebugMode(){
    global $quads_options;

    $debug_mode = isset($quads_options['debug_mode']) ? true : false;
    return $debug_mode;
}

/**
 * Create ad buttons for editor
 * 
 * @author Tedd Garland, René Hermenau
 * @since 0.9.0
 */



$wpvcomp = (bool)(version_compare(get_bloginfo('version'), '3.1', '>='));
function quads_ads_head_script() {
    global $quads_options, $wpvcomp;

	if ( isset($quads_options['quicktags']['QckTags'] ) ) { ?>
        <script type="text/javascript">
		wpvcomp = <?php echo(($wpvcomp==1)?"true":"false"); ?>;
                    edaddID = new Array();
                    edaddNm = new Array();
		if(typeof(edButtons)!='undefined') {
            edadd = edButtons.length;
			var dynads={"all":[
				<?php for ($i=1;$i<=count( quads_get_ads() )-1;$i++) { if( isset($quads_options['ad'.$i]['code']) && $quads_options['ad'.$i]['code'] !='' ){echo('"1",');}else{echo('"0",');}; } ?>
                    "0"]};
			for(i=1;i<=<?php echo count( quads_get_ads() ) -1; ?>;i++) {
				if(dynads.all[i-1]=="1") {
					edButtons[edButtons.length]=new edButton("ads"+i.toString(),"Ads"+i.toString(),"\n<!--Ads"+i.toString()+"-->\n","","",-1);
					edaddID[edaddID.length] = "ads"+i.toString();
					edaddNm[edaddNm.length] = "Ads"+i.toString();
                                 }
                                }
			<?php if( !isset($quads_options['quicktags']['QckRnds'] ) ){ ?>
				edButtons[edButtons.length]=new edButton("random_ads","RndAds","\n<!--RndAds-->\n","","",-1);
				edaddID[edaddID.length] = "random_ads";
				edaddNm[edaddNm.length] = "RndAds";
        <?php } ?>
                                edButtons[edButtons.length]=new edButton("no_ads","NoAds","\n<!--NoAds-->\n","","",-1);
				edaddID[edaddID.length] = "no_ads";
                                                edaddNm[edaddNm.length] = "NoAds";
			<?php //if( !isset( $quads_options['quicktags']['QckOffs'] ) ){ ?>
                                        //edButtons[edButtons.length]=new edButton("no_ads","NoAds","\n<!--NoAds-->\n","","",-1);
                                //edaddID[edaddID.length] = "no_ads";
                                //edaddNm[edaddNm.length] = "NoAds";
                                //edButtons[edButtons.length]=new edButton("off_def","OffDef","\n<!--OffDef-->\n","","",-1);	
                                //edaddID[edaddID.length] = "off_def";
                                //edaddNm[edaddNm.length] = "OffDef";
                                //edButtons[edButtons.length]=new edButton("off_wid","OffWidget","\n<!--OffWidget-->\n","","",-1);	
                                //edaddID[edaddID.length] = "off_wid";
                                //edaddNm[edaddNm.length] = "OffWidget";				
			<?php //} ?>
			<?php //if( !isset( $quads_options['quicktags']['QckOfPs'] ) ){ ?>
                                //edButtons[edButtons.length]=new edButton("off_bgn","OffBegin","\n<!--OffBegin-->\n","","",-1);
                                //edaddID[edaddID.length] = "off_bgn";
                                //edaddNm[edaddNm.length] = "OffBegin";
                                //edButtons[edButtons.length]=new edButton("off_mid","OffMiddle","\n<!--OffMiddle-->\n","","",-1);
                                //edaddID[edaddID.length] = "off_mid";
                                //edaddNm[edaddNm.length] = "OffMiddle";
                                //edButtons[edButtons.length]=new edButton("off_end","OffEnd","\n<!--OffEnd-->\n","","",-1);
                                //edaddID[edaddID.length] = "off_end";
                                //edaddNm[edaddNm.length] = "OffEnd";				
                                //edButtons[edButtons.length]=new edButton("off_more","OffAfMore","\n<!--OffAfMore-->\n","","",-1);
                                //edaddID[edaddID.length] = "off_more";
                                //edaddNm[edaddNm.length] = "OffAfMore";				
                                //edButtons[edButtons.length]=new edButton("off_last","OffBfLastPara","\n<!--OffBfLastPara-->\n","","",-1);
                                //edaddID[edaddID.length] = "off_last";
                                //edaddNm[edaddNm.length] = "OffBfLastPara";								
			<?php //} ?>			
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
	<?php	}
    }
if ($wpvcomp) {
	add_action('admin_print_footer_scripts', 'quads_ads_head_script');
}else{
	add_action('admin_head', 'quads_ads_head_javascript_script');
}

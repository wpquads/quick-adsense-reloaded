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
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load Scripts
 *
 * Enqueues the required scripts.
 *
 * @since 1.0
 * @global $quads_options
 * @global $post
 * @return void
 * @param string $hook Page hook
 */
function quads_load_scripts($hook) {
    global $wp;
        /*if ( ! apply_filters( 'quads_load_scripts', quadsGetActiveStatus(), $hook ) ) {
            quadsdebug()->info("quads_load_script not active");
            return;
	}*/
    
	global $quads_options;

            
	$js_dir = QUADS_PLUGIN_URL . 'assets/js/';
	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( quadsIsDebugMode() ) ? '' : '.min';
        
        isset($quads_options['load_scripts_footer']) ? $in_footer = true : $in_footer = false;       
	wp_enqueue_script( 'quads', $js_dir . 'quads' . $suffix . '.js', array( 'jquery' ), QUADS_VERSION, $in_footer );

                wp_localize_script( 'quads', 'quads', array(
			'sample' => 1
                    ));
                        
}
add_action( 'wp_enqueue_scripts', 'quads_load_scripts' );

/**
 * Register Styles
 *
 * Checks the styles option and hooks the required filter.
 *
 * @since 1.0
 * @global $quads_options
 * @return void
 */
function quads_register_styles($hook) {
        /*if ( ! apply_filters( 'quads_register_styles', quadsGetActiveStatus(), $hook ) ) {
            return;
	}*/
	global $quads_options;

	if ( isset( $quads_options['disable_styles'] ) ) {
		return;
	}

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( quadsIsDebugMode() ) ? '' : '.min';
	$file          = 'quads' . $suffix . '.css';

        $url = QUADS_PLUGIN_URL . 'templates/' .   $file;
	wp_enqueue_style( 'quads-styles', $url, array(), QUADS_VERSION );
}
//add_action( 'wp_enqueue_scripts', 'quads_register_styles' );

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
	if ( ! apply_filters( 'quads_load_admin_scripts', quads_is_admin_page(), $hook ) ) {
		return;
	}
	global $wp_version;

	$js_dir  = QUADS_PLUGIN_URL . 'assets/js/';
	$css_dir = QUADS_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( quadsIsDebugMode() ) ? '' : '.min';

	// These have to be global
	wp_enqueue_script( 'quads-admin-scripts', $js_dir . 'quads-admin' . $suffix . '.js', array( 'jquery' ), QUADS_VERSION, false );
	wp_enqueue_style( 'quads-admin', $css_dir . 'quads-admin' . $suffix . '.css', QUADS_VERSION );
        
        wp_localize_script( 'quads-admin-scripts', 'quads', array(
        'nonce' => wp_create_nonce( 'quads_ajax_nonce' ),
        'error' => __( "error", 'wpstg' ),
	));
}
add_action( 'admin_enqueue_scripts', 'quads_load_admin_scripts', 100 );

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
				<?php for ($i=1;$i<=count( quads_get_ads() )-1;$i++) { if( $quads_options['ad'.$i]['code'] !='' ){echo('"1",');}else{echo('"0",');}; } ?>
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
			<?php if( !isset( $quads_options['quicktags']['QckOffs'] ) ){ ?>
				edButtons[edButtons.length]=new edButton("no_ads","NoAds","\n<!--NoAds-->\n","","",-1);
				edaddID[edaddID.length] = "no_ads";
				edaddNm[edaddNm.length] = "NoAds";
				edButtons[edButtons.length]=new edButton("off_def","OffDef","\n<!--OffDef-->\n","","",-1);	
				edaddID[edaddID.length] = "off_def";
				edaddNm[edaddNm.length] = "OffDef";
				edButtons[edButtons.length]=new edButton("off_wid","OffWidget","\n<!--OffWidget-->\n","","",-1);	
				edaddID[edaddID.length] = "off_wid";
				edaddNm[edaddNm.length] = "OffWidget";				
			<?php } ?>
			<?php if( !isset( $quads_options['quicktags']['QckOfPs'] ) ){ ?>
				edButtons[edButtons.length]=new edButton("off_bgn","OffBegin","\n<!--OffBegin-->\n","","",-1);
				edaddID[edaddID.length] = "off_bgn";
				edaddNm[edaddNm.length] = "OffBegin";
				edButtons[edButtons.length]=new edButton("off_mid","OffMiddle","\n<!--OffMiddle-->\n","","",-1);
				edaddID[edaddID.length] = "off_mid";
				edaddNm[edaddNm.length] = "OffMiddle";
				edButtons[edButtons.length]=new edButton("off_end","OffEnd","\n<!--OffEnd-->\n","","",-1);
				edaddID[edaddID.length] = "off_end";
				edaddNm[edaddNm.length] = "OffEnd";				
				edButtons[edButtons.length]=new edButton("off_more","OffAfMore","\n<!--OffAfMore-->\n","","",-1);
				edaddID[edaddID.length] = "off_more";
				edaddNm[edaddNm.length] = "OffAfMore";				
				edButtons[edButtons.length]=new edButton("off_last","OffBfLastPara","\n<!--OffBfLastPara-->\n","","",-1);
				edaddID[edaddID.length] = "off_last";
				edaddNm[edaddNm.length] = "OffBfLastPara";								
			<?php } ?>			
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

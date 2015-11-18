<?php
/**
 * Scripts
 *
 * @package     QUADS
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
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
        if ( ! apply_filters( 'quads_load_scripts', quadssbGetActiveStatus(), $hook ) ) {
            quadsdebug()->info("quads_load_script not active");
            return;
	}
    
	global $quads_options, $post;

        $url = get_permalink($post->ID);
        $title = urlencode(html_entity_decode(the_title_attribute('echo=0'), ENT_COMPAT, 'UTF-8'));
        $title = str_replace('#' , '%23', $title); 
        $titleclean = esc_html($title);
        $image = quads_get_image($post->ID);
        $desc = quads_get_excerpt_by_id($post->ID);
        
        /* Load hashshags */   
        $hashtag = !empty($quads_options['quickads_hashtag']) ? $quads_options['quickads_hashtag'] : '';
            
	$js_dir = quads_PLUGIN_URL . 'assets/js/';
	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
        
        isset($quads_options['load_scripts_footer']) ? $in_footer = true : $in_footer = false;       
	wp_enqueue_script( 'quads', $js_dir . 'quads' . $suffix . '.js', array( 'jquery' ), quads_VERSION, $in_footer );
        !isset($quads_options['disable_sharecount']) ? $shareresult = getSharedcount($url) : $shareresult = 0;
                wp_localize_script( 'quads', 'quads', array(
			'shares'        => $shareresult,
                        'round_shares'  => isset($quads_options['quickads_round']),
                        /* Do not animate shares on blog posts. The share count would be wrong there and performance bad */
                        'animate_shares' => isset($quads_options['animate_shares']) && is_singular() ? 1 : 0,
                        'share_url' => $url,
                        'title' => $titleclean,
                        'image' => $image,
                        'desc' => $desc,
                        'hashtag' => $hashtag,
                        'subscribe' => !empty($quads_options['subscribe_behavior']) && $quads_options['subscribe_behavior'] === 'content' ? 'content' : 'link',
                        'subscribe_url' => isset($quads_options['subscribe_link']) ? $quads_options['subscribe_link'] : '',
                        'activestatus' => quadssbGetActiveStatus(),
                        'singular' => is_singular() ? 1 : 0,
                        'twitter_popup' => isset($quads_options['twitter_popup']) ? 0 : 1,
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
        if ( ! apply_filters( 'quads_register_styles', quadssbGetActiveStatus(), $hook ) ) {
            return;
	}
	global $quads_options;

	if ( isset( $quads_options['disable_styles'] ) ) {
		return;
	}

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$file          = 'quads' . $suffix . '.css';

	//$url = trailingslashit( plugins_url(). '/quadssharer/templates/'    ) . $file;
        $url = quads_PLUGIN_URL . 'templates/' .   $file;
	wp_enqueue_style( 'quads-styles', $url, array(), quads_VERSION );
}
add_action( 'wp_enqueue_scripts', 'quads_register_styles' );

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

	$js_dir  = quads_PLUGIN_URL . 'assets/js/';
	$css_dir = quads_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
        //echo $css_dir . 'quads-admin' . $suffix . '.css', quads_VERSION;
	// These have to be global
	wp_enqueue_script( 'quads-admin-scripts', $js_dir . 'quads-admin' . $suffix . '.js', array( 'jquery' ), quads_VERSION, false );
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('media-upload'); //Provides all the functions needed to upload, validate and give format to files.
        wp_enqueue_script('thickbox'); //Responsible for managing the modal window.
        wp_enqueue_style('thickbox'); //Provides the styles needed for this window.
	wp_enqueue_style( 'quads-admin', $css_dir . 'quads-admin' . $suffix . '.css', quads_VERSION );
}
add_action( 'admin_enqueue_scripts', 'quads_load_admin_scripts', 100 );
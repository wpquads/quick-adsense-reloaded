<?php
/**
 * Plugin Name: WP QUADS Shortcode Remover
 * Plugin URI: https://wpquads.com/
 * Description: Remove WP QUADS shortcode when the plugin AdSense Integration WP QUADS has deactive/uninstall
 * Author: WP Quads
 * Author URI: https://wpquads.com/
 * Version: 0.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
	exit;

if(!defined( 'QUADS_NAME' ) && !class_exists( 'QuickAdsenseReloaded' )){
	add_shortcode( 'quads_ad', 'quads_remove_unsed_shortcode', 1); 
	add_shortcode( 'quads', 'quads_remove_unsed_shortcode', 1);
	function quads_remove_unsed_shortcode( $atts ) {
		return '';
	}
}
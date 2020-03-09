<?php
/**
 * Plugin Name: WP QUADS
 * Plugin URI: https://wordpress.org/plugins/quick-adsense-reloaded/
 * Description: Remove WP QUADS shortcode when the plugin AdSense Integration WP QUADS has deactive/uninstall
 * Author: WP Quads
 * Author URI: https://wordpress.org/plugins/quick-adsense-reloaded/
 * Version: 0.1
 * Credits: WP QUADS - Quick AdSense Reloaded is a fork of Quick AdSense
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
	exit;
// Plugin version
if( !defined( 'WP_QUADS_MU_PLUGIN_VERSION' ) ) {
 define( 'WP_QUADS_MU_PLUGIN_VERSION', '0.1' );
}

if(!defined( 'QUADS_NAME' ) && !class_exists( 'QuickAdsenseReloaded' )){
	add_shortcode( 'quads_ad', 'quads_shortcode_remove_ad', 1); 
	add_shortcode( 'quads', 'wpquads_remove_unsed_shortcode', 1);
	function wpquads_remove_unsed_shortcode( $atts ) {
		return '';
	}
}
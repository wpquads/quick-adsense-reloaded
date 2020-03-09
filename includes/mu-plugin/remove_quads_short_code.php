<?php
/*plugin name: WP QUADS
Description:Remove WP QUADS shortcode
Author:WP Quads
*/
if(!defined( 'QUADS_NAME' ) && !class_exists( 'QuickAdsenseReloaded' )){
	add_shortcode( 'quads_ad', 'quads_shortcode_remove_ad', 1); 
	add_shortcode( 'quads', 'quads_shortcode_remove_ad', 1);
	function quads_shortcode_remove_ad( $atts ) {
		return '';
	}

}
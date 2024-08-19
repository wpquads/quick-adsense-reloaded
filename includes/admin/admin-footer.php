<?php
/**
 * Admin Footer
 *
 * @package     QUADS
 * @subpackage  Admin/Footer
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add rating links to the admin dashboard
 *
 * @since	1.0.0
 * @global	string $typenow
 * @param       string $footer_text The existing footer text
 * @return      string
 */
function quads_admin_rate_us( $footer_text ) {
	global $typenow;

	if ( quads_is_admin_page() ) {
		$footer_text = '<strong>' . esc_html__( 'Please do us a BIG favor and give us a 5 star rating' , 'quick-adsense-reloaded' );
		$footer_text .= ' <a href="'.esc_url('https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/?filter=5#new-post').'" target="_blank">' . esc_html__( 'here', 'quick-adsense-reloaded' ) . '</a>. ';
		$footer_text .= esc_html__( 'If you have issues, open a', 'quick-adsense-reloaded' );
		$footer_text .= ' <a href="'.esc_url('http://wpquads.com/support/').'" target="_blank">' . esc_html__( 'support ticket', 'quick-adsense-reloaded' ) . '</a>, ';
		$footer_text .= esc_html__( 'so that we can sort it out. Thank you!', 'quick-adsense-reloaded' ) . '</strong>';
	} 

	return $footer_text;
}
add_filter( 'admin_footer_text', 'quads_admin_rate_us' );
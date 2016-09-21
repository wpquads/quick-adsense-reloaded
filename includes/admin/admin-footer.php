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
		$rate_text = sprintf( __( 'Please do us a BIG favor and give us a 5 star rating <a href="%1$s" target="_blank">here</a> . If you`re not happy, please get in touch with us at support@wpquads.com, so that we can sort it out. Thank you!', 'quick-adsense-reloaded' ),
			'https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/?filter=5#new-post'
		);
	} else {
		return $footer_text;
	}
}
add_filter( 'admin_footer_text', 'quads_admin_rate_us' );
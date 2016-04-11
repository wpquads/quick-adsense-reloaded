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
		$rate_text = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">WP QUADS</a>! Please <a href="%2$s" target="_blank">rate it</a> on <a href="%2$s" target="_blank">WordPress.org</a> and help to support this project.<br>Something not working as expected or need help for customizing Quick AdSense Reloaded? Visit the Quick AdSense Reloaded <a href="https://wordpress.org/support/plugin/quick-adsense-reloaded" target="blank">Support Forum</a>.', 'quick-adsense-reloaded' ),
			'http://wordpress.org/support/view/plugin-reviews/quick-adsense-reloaded',
			'http://wordpress.org/support/view/plugin-reviews/quick-adsense-reloaded?filter=5#postform'
		);

		//return str_replace( '</span>', '', '' ) . $rate_text . '</span>';
                return $rate_text;
	} else {
		return $footer_text;
	}
}
add_filter( 'admin_footer_text', 'quads_admin_rate_us' );
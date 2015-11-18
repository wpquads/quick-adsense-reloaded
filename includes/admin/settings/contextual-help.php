<?php
/**
 * Contextual Help
 *
 * @package     QUADS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Settings contextual help.
 *
 * @access      private
 * @since       1.0
 * @return      void
 */
function quads_settings_contextual_help() {
	$screen = get_current_screen();

	/*if ( $screen->id != 'quads-settings' )
		return;
*/
	$screen->set_help_sidebar(
		'<p><strong>' . $screen->id . sprintf( __( 'For more information:', 'quads' ) . '</strong></p>' .
		'<p>' . sprintf( __( 'Visit the <a href="%s">documentation</a> on the Quick AdSense Reloaded website.', 'quads' ), esc_url( 'https://www.quadsshare.net/' ) ) ) . '</p>' .
		'<p>' . sprintf(
					__( '<a href="%s">Post an issue</a> on <a href="%s">Quick AdSense Reloaded</a>. View <a href="%s">extensions</a>.', 'quads' ),
					esc_url( 'https://www.quadsshare.net/contact-support/' ),
					esc_url( 'https://www.quadsshare.net' ),
					esc_url( 'https://www.quadsshare.net/downloads' )
				) . '</p>'
	);

	$screen->add_help_tab( array(
		'id'	    => 'quads-settings-general',
		'title'	    => __( 'General', 'quads' ),
		'content'	=> '<p>' . __( 'This screen provides the most basic settings for configuring Quick AdSense Reloaded.', 'quads' ) . '</p>'
	) );


	

	do_action( 'quads_settings_contextual_help', $screen );
}
add_action( 'load-quads-settings', 'quads_settings_contextual_help' );

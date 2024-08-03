<?php
/**
 * Contextual Help
 *
 * @package     QUADS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
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
		'<p><strong>' . $screen->id . sprintf( __( 'For more information:', 'quick-adsense-reloaded' ) . '</strong></p>' .
		'<p>' . sprintf( /* translators: %s: Quick AdSense Reloaded documentation URL */
			__( 'Visit the <a href="%s">documentation</a> on the Quick AdSense Reloaded website.', 'quick-adsense-reloaded' ), esc_url( 'https://wordpress.org/plugins/quick-adsense-reloaded' ) ) ) . '</p>' .
		'<p>' . sprintf(
					/* translators: %s: Quick AdSense Reloaded support forum URL */
					__( '<a href="%1$s">Post an issue</a> on <a href="%2$s">Quick AdSense Reloaded</a>. View <a href="%3$s">extensions</a>.', 'quick-adsense-reloaded' ),
					esc_url( 'https://wordpress.org/plugins/quick-adsense-reloaded' ),
					esc_url( 'https://wordpress.org/plugins/quick-adsense-reloaded' ),
					esc_url( 'https://wordpress.org/plugins/quick-adsense-reloaded' )
				) . '</p>'
	);

	$screen->add_help_tab( array(
		'id'	    => 'quads-settings-general',
		'title'	    => __( 'General', 'quick-adsense-reloaded' ),
		'content'	=> '<p>' . __( 'This screen provides the most basic settings for configuring Quick AdSense Reloaded.', 'quick-adsense-reloaded' ) . '</p>'
	) );


	

	do_action( 'quads_settings_contextual_help', $screen );
}
add_action( 'load-quads-settings', 'quads_settings_contextual_help' );
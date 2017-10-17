<?php
/**
 * Welcome Page Class
 *
 * @package     QUADS
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * quads_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.4
 */
class quads_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	

	/**
	 * Sends user to the Settings page on first activation of QUADS as well as each
	 * time QUADS is upgraded to a new version
	 *
	 * @access public
	 * @since 1.0.1
	 * @return void
	 */
	public function welcome() {
		// Bail if no activation redirect
		if ( false === get_transient( 'quads_activation_redirect' ) ){
			return;
                }

		// Delete the redirect transient
		delete_transient( 'quads_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ){
			return;
                }

		$upgrade = get_option( 'quads_version_upgraded_from' );

                // Redirect to settings page if wp quads pro is not installed
		if( quads_is_extra() ) { // First time install
			wp_safe_redirect( admin_url( 'admin.php?page=quads-settings' ) ); exit;
		} else { // Update
			wp_safe_redirect( admin_url( 'admin.php?page=quads-addons' ) ); exit;
		}
	}
}
new quads_Welcome();

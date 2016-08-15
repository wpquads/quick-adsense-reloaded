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
	 * @global $quads_options Array of all the QUADS Options
	 * @return void
	 */
	public function welcome() {
		global $quads_options;

		// Bail if no activation redirect
		if ( ! get_transient( '_quads_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_quads_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		//$upgrade = get_option( 'quads_version_upgraded_from' );
                
                
                
                //@since 2.0.3
		if( quads_is_installed_clickfraud() ) { // clickfraud plugin already installed
			// no redirect
		} else { 
			wp_safe_redirect( admin_url( 'admin.php?page=quads-addons' ) ); exit;
		}
//                //@since 2.0.3
//		if( ! $upgrade ) { // First time install
//			wp_safe_redirect( admin_url( 'admin.php?page=quads-addons' ) ); exit;
//		} else { // Update
//			wp_safe_redirect( admin_url( 'admin.php?page=quads-addons' ) ); exit;
//		}
	}
}
new quads_Welcome();

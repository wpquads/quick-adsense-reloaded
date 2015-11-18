<?php
/**
 * Install Function
 *
 * @package     QUADS
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Install Multisite
 * check first if multisite is enabled
 * @since 2.1.1
 * 
 */

register_activation_hook( quads_PLUGIN_FILE, 'quads_install_multisite' );

function quads_install_multisite($networkwide) {
    global $wpdb;
                 
    if (function_exists('is_multisite') && is_multisite()) {
        // check if it is a network activation - if so, run the activation function for each blog id
        if ($networkwide) {
                    $old_blog = $wpdb->blogid;
            // Get all blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                quads_install();
            }
            switch_to_blog($old_blog);
            return;
        }   
    } 
    quads_install();      
}

/**
 * Install
 *
 * Runs on plugin install to populates the settings fields for those plugin
 * pages. After successful install, the user is redirected to the QUADS Welcome
 * screen.
 *
 * @since 2.0
 * @global $wpdb
 * @global $quads_options
 * @global $wp_version
 * @return void
 */



function quads_install() {
	global $wpdb, $quads_options, $wp_version;

	// Add Upgraded From Option
	$current_version = get_option( 'quads_version' );
	if ( $current_version ) {
		update_option( 'quads_version_upgraded_from', $current_version );
	}

        // Update the current version
        update_option( 'quads_version', quads_VERSION );
        // Add plugin installation date and variable for rating div
        add_option('quads_installDate',date('Y-m-d h:i:s'));
        add_option('quads_RatingDiv','no');
        if ( !get_option('quads_update_notice') )
            add_option('quads_update_notice','no');
	
                
    /* Setup some default options
     * Store our initial social networks in separate option row.
     * For easier modification and to prevent some trouble
     */
    $networks = array(
        'Facebook',
        'Twitter',
        'Subscribe'
    );
    
    if (is_plugin_inactive('quadsshare-networks/quadsshare-networks.php')) {
        update_option('quads_networks', $networks);
    }
    
    // Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;	
        }
        
        // Add the transient to redirect / not for multisites
	set_transient( '_quads_activation_redirect', true, 30 );
        


}

/**
 * Post-installation
 *
 * Runs just after plugin installation and exposes the
 * quads_after_install hook.
 *
 * @since 2.0
 * @return void
 */
function quads_after_install() {

	if ( ! is_admin() ) {
		return;
	}

	$activation_pages = get_transient( '_quads_activation_pages' );

	// Exit if not in admin or the transient doesn't exist
	if ( false === $activation_pages ) {
		return;
	}

	// Delete the transient
	delete_transient( '_quads_activation_pages' );

	do_action( 'quads_after_install', $activation_pages );
}
add_action( 'admin_init', 'quads_after_install' );
<?php
/**
 * Uninstall Quick adsense reloaded
 *
 * @package     quads
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load QUADS file
include_once( 'quick-adsense-reloaded.php' );

if( quads_get_option( 'uninstall_on_delete' ) ) {
	/** Delete all the Plugin Options */
	delete_option( 'quads_settings' );
        delete_option( 'quads_install_date');
        delete_option( 'quads_rating_div'); 
        delete_option( 'quads_version');
        delete_option( 'quads_version_upgraded_from');
        

        /* Delete all post meta options */
        delete_post_meta_by_key( 'quads_timestamp' );
        delete_post_meta_by_key( 'quads_shares' );
        delete_post_meta_by_key( 'quads_jsonshares' );
        
}

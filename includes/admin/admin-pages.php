<?php
/**
 * Admin Pages
 *
 * @package     QUADS
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the admin submenu pages under the Quick AdSense Reloaded menu and assigns their
 * links to global variables
 *
 * @since 1.0
 * @global $quads_settings_page
 * @global $quads_add_ons_page
 * @global $quads_tools_page
 * @return void
 */
function quads_add_options_link() {
	global $quads_options, $quads_parent_page, $quads_add_ons_page, $quads_add_ons_page2, $quads_settings_page, $quads_tools_page;

        $create_settings = isset($quads_options['create_settings']) ? true : false;
        if ($create_settings){
            $quads_settings_page = add_submenu_page( 'options-general.php', __( 'Quick AdSense Reloaded Settings', 'quick-adsense-reloaded' ), __( 'Quick AdSense Reloaded', 'quick-adsense-reloaded' ), 'manage_options', 'quads-settings', 'quads_options_page' );
        }else{
            $quads_parent_page   = add_menu_page( 'Quick AdSense Reloaded Settings', __( 'Quick AdSense Reloaded', 'quick-adsense-reloaded' ), 'manage_options', 'quads-settings', 'quads_options_page' );
        }
        //$quads_parent_page = add_menu_page( 'Quick AdSense Reloaded Welcome Screen' , 'Quick AdSense Reloaded' , 'manage_options' , 'quadsshare-welcome' , 'quadsshare_welcome_conf');   
        //$quads_settings_page = add_submenu_page( 'quads-settings', __( 'Quick AdSense Reloaded Settings', 'quick-adsense-reloaded' ), __( 'Settings', 'quick-adsense-reloaded' ), 'manage_options', 'quads-settings', 'quads_options_page' );
        //$quads_add_ons_page  = add_submenu_page( 'quads-settings', __( 'Quick AdSense Reloaded Add Ons', 'quick-adsense-reloaded' ), __( 'Add Ons', 'quick-adsense-reloaded' ), 'manage_options', 'quads-addons', 'quads_add_ons_page' ); 
        //$quads_tools_page = add_submenu_page( 'quads-settings', __( 'Quick AdSense Reloaded Tools', 'quick-adsense-reloaded' ), __( 'Tools', 'quick-adsense-reloaded' ), 'manage_options', 'quads-tools', 'quads_tools_page' );

}
add_action( 'admin_menu', 'quads_add_options_link', 10 );

/**
 *  Determines whether the current admin page is an QUADS admin page.
 *  
 *  Only works after the `wp_loaded` hook, & most effective 
 *  starting on `admin_menu` hook.
 *  
 *  @since 1.9.6
 *  @return bool True if QUADS admin page.
 */
function quads_is_admin_page() {
        $currentpage = isset($_GET['page']) ? $_GET['page'] : '';
	if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
		return false;
	}
	
	global $quads_parent_page, $pagenow, $typenow, $quads_settings_page, $quads_add_ons_page, $quads_tools_page;

	if ( 'quads-settings' == $currentpage || 'quads-addons' == $currentpage || 'quads-tools' == $currentpage) {
                quadsdebug()->info("quads_is_admin_page() = true");
		return true;      
	}
	
         
}

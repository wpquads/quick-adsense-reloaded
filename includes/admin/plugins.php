<?php
/**
 * Admin Plugins
 *
 * @package     QUADS
 * @subpackage  Admin/Plugins
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Plugins row action links
 *
 * @author Michael Cannon <mc@aihr.us>
 * @since 2.0
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 * @return array $links
 */
function quads_plugin_action_links( $links, $file ) {
	$settings_link = '<a href="' . admin_url( 'admin.php?page=quads-settings' ) . '">' . esc_html__( 'General Settings', 'quick-adsense-reloaded' ) . '</a>';
        
	if ( $file == 'quick-adsense-reloaded/quick-adsense-reloaded.php' || $file == 'quads-pro/wpquads-pro.php' ){
		array_unshift( $links, $settings_link );
        }

	return $links;
}
add_filter( 'plugin_action_links', 'quads_plugin_action_links', 10, 2 );

function quads_premium_plugin_action_links( $links, $file ){

		$settings_link = array( 'settings'=> '<a href="https://wpquads.com/">' . esc_html__( 'Premium Features', 'quick-adsense-reloaded' ) . '</a> | <a href="https://wpquads.com/support/">' . esc_html__( 'Support', 'quick-adsense-reloaded' ) . '</a>' );
		if ( $file == 'quick-adsense-reloaded/quick-adsense-reloaded.php' ){
			$links = array_merge( $links, $settings_link );
		}
		
		return $links;
	
	}

add_filter('plugin_action_links', 'quads_premium_plugin_action_links', 10, 2);

/**
 * Function to fix the issue with the Jannah theme which is causing every rest api request redirect to homepage 
 * 
 * @see https://github.com/wpquads/quick-adsense-reloaded/issues/890
 * @since 2.0.86
 */
function quads_override_custom_rewrite_rules($rules) {
    // Check if the unwanted rule exists, then remove it
	if( function_exists( 'wpse_custom_rewrite_rules' ) ){
		foreach ($rules as $rule => $rewrite) {
			if (strpos($rule, '(.+?)/(.+?)/?$') !== false) {
				unset($rules[$rule]); // Unset the problematic rule
			}
		}
	}
    return $rules;
}
add_filter('rewrite_rules_array', 'quads_override_custom_rewrite_rules', 20);

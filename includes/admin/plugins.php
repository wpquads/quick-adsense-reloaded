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
	$settings_link = '<a href="' . admin_url( 'options-general.php?page=quads-settings' ) . '">' . esc_html__( 'General Settings', 'quick-adsense-reloaded' ) . '</a>';
        
	if ( $file == 'quick-adsense-reloaded/quick-adsense-reloaded.php' || $file == 'quads-pro/wpquads-pro.php' ){
		array_unshift( $links, $settings_link );
        }

	return $links;
}
add_filter( 'plugin_action_links', 'quads_plugin_action_links', 10, 2 );



/**
 * Plugin row meta links
 *
 * @author Michael Cannon <mc@aihr.us>
 * @since 0.0.9
 * @param array $input already defined meta links
 * @param string $file plugin file path and name being processed
 * @return array $input
 */
//function quads_plugin_row_meta( $input, $file ) {
//	if ( $file != 'quick-adsense-reloaded/quick-adsense-reloaded.php' && $file != 'quads-pro/wpquads-pro.php' ){
//		return $input;
//        }
//
//	$links = array(
//		'<a href="' . admin_url( 'options-general.php?page=quads-settings' ) . '">' . esc_html__( 'Getting Started', 'quick-adsense-reloaded' ) . '</a>',
//	);
//        
//
//	$input = array_merge( $input, $links );
//
//	return $input;
//}
//add_filter( 'plugin_row_meta', 'quads_plugin_row_meta', 10, 2 );
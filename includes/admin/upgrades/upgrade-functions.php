<?php

/**
 * Upgrade Functions
 *
 * @package     QUADS
 * @subpackage  Admin/Upgrades
 * @copyright   Copyright (c) 2016, Ren´é Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Perform automatic upgrades when necessary
 *
 * @since 1.2.3
 * @return void
 */
function quads_do_automatic_upgrades() {

    $did_upgrade = false;
    $quads_version = preg_replace( '/[^0-9.].*/', '', get_option( 'quads_version' ) );

    if( version_compare( $quads_version, '1.2.3', '<' ) ) {
        quads_check_theme();
        //quads_redirect_after_update();
    }

    // Check if version number in DB is lower than version number in current plugin
    if( version_compare( $quads_version, QUADS_VERSION, '<' ) ) {
        //wp_die( 'upgrade' );
        // Let us know that an upgrade has happened
        $did_upgrade = true;
    }

    // Update Version number
    if( $did_upgrade ) {
        update_option( 'quads_version', preg_replace( '/[^0-9.].*/', '', QUADS_VERSION ) );
    }
}

add_action( 'admin_init', 'quads_do_automatic_upgrades' );

/**
 * Check if theme is a commercial one. Than write the option
 */
function quads_check_theme() {
    update_option ('quads_show_theme_notice', quads_is_commercial_theme() );
}

/**
 * 
 * @return mixed string | bool false name of the theme if theme is a known commercial theme
 */
function quads_is_commercial_theme() {

    // Get current theme name
    $my_theme = wp_get_theme();

    // Known commercial themes which are using WP QUADS
    $themes = array('Bunchy', 'Bimber');

    if( is_object( $my_theme ) && in_array( $my_theme->get( 'Name' ), $themes ) ) {
        return $my_theme->get( 'Name' );
    }

    return false;
}


/**
 * Set a transient which is responsible for redirecting after updating the plugin
 */
//function quads_redirect_after_update() {
//    // Add the transient to redirect (not for multisites)
//    set_transient( 'quads_activation_redirect', true, 10 );
//}

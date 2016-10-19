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

    if( version_compare( $quads_version, '1.2.5', '<' ) ) {
        quads_store_adsense_args();
        quads_check_theme();
    }
    if( version_compare( $quads_version, '1.2.7', '<' ) ) {
        quads_change_widget_values();
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
 * Change array quads_settings['ad1_widget'] to quads_settings[ad1_widget][code]
 * 
 * return mixed bool|void false when settings are empty
 */
function quads_change_widget_values(){
    $settings = get_option('quads_settings');

    if (empty($settings)){
        return false;
    }
    
    foreach ($settings as $key => $value){
        if ($key === 'ad1_widget' && is_string($settings['ad1_widget']) )
            $new['ad1_widget']['code'] = $value;
        
        else if ($key === 'ad2_widget' && is_string($settings['ad2_widget']) )
            $new['ad2_widget']['code'] = $value;
        
        else if ($key === 'ad3_widget' && is_string($settings['ad3_widget']) )
            $new['ad3_widget']['code'] = $value;
        
        else if ($key === 'ad4_widget' && is_string($settings['ad4_widget']) )
            $new['ad4_widget']['code'] = $value;
        
        else if ($key === 'ad5_widget' && is_string($settings['ad5_widget']) )
            $new['ad5_widget']['code'] = $value;
        
        else if ($key === 'ad6_widget' && is_string($settings['ad6_widget']) )
            $new['ad6_widget']['code'] = $value;
        
        else if ($key === 'ad7_widget' && is_string($settings['ad7_widget']) )
            $new['ad7_widget']['code'] = $value;
        
        else if ($key === 'ad8_widget' && is_string($settings['ad8_widget']) )
            $new['ad8_widget']['code'] = $value;
        
        else if ($key === 'ad9_widget' && is_string($settings['ad9_widget']) )
            $new['ad9_widget']['code'] = $value;
        
        else if ($key === 'ad10_widget' && is_string($settings['ad10_widget']) )
            $new['ad10_widget']['code'] = $value;
        else
            $new[$key] = $value;
    }
    
    update_option('quads_settings', $new);
    //wp_die('<pre>' . var_dump($new));
}


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
    $themes = array('Bunchy', 'Bimber', 'boombox', 'Boombox');

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

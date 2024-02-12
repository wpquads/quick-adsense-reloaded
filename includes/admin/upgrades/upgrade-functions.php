<?php

/**
 * Upgrade Functions
 *
 * @package     QUADS
 * @subpackage  Admin/Upgrades
 * @copyright   Copyright (c) 2016, René Hermenau
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
    // Get current installed version
    $quads_version = preg_replace( '/[^0-9.].*/', '', get_option( 'quads_version' ) );
    
    // Previous version
    $previous_version = get_option( 'quads_version_upgraded_from' );
    

//    if( version_compare( $quads_version, '1.2.5', '<' ) ) {
//        quads_store_adsense_args();
//    }
    if( version_compare( $quads_version, '1.2.7', '<' ) ) {
        quads_change_widget_values();
    }
    if( version_compare( $quads_version, '1.4.0', '<' ) ) {
        quads_import_post_type_settings();
        quads_is_commercial_theme();
    }
    
    // Update settings - Try to do this after any update
    if( version_compare( $quads_version, QUADS_VERSION, '<=' )) {
       quads_update_settings_1_5_3();
       quads_is_commercial_theme();
    }
    

    // Check if version number in DB is lower than version number of plugin
    if( version_compare( $quads_version, QUADS_VERSION, '<' ) ) {
        // Let us know that an upgrade has happened
        $did_upgrade = true;
    }

      // Update Current Version number
    if( $did_upgrade ) {    
      update_option( 'quads_version', preg_replace( '/[^0-9.].*/', '', QUADS_VERSION ) );
    }
    
}
add_action( 'admin_init', 'quads_do_automatic_upgrades' );

/**
 * Update Settings for version 1.5.3 and higher
 * Add new index $settings['ads']
 */
function quads_update_settings_1_5_3(){
   $settings = get_option( 'quads_settings' );

   // Do not update - we already did it
   if (isset($settings['ads'])){
      return false;
   }
   // Do not update - no data available
   if (false == $settings){
      return false;
   }
   
   

   foreach ( $settings as $key => $value ) {
      
      if( $key === 'ad1_widget'){
            $new['ads']['ad1_widget'] = $value;
      }else if( $key === 'ad2_widget' )
            $new['ads']['ad2_widget'] = $value;
        else if( $key === 'ad3_widget' )
            $new['ads']['ad3_widget'] = $value;

        else if( $key === 'ad4_widget' )
            $new['ads']['ad4_widget'] = $value;

        else if( $key === 'ad5_widget' )
            $new['ads']['ad5_widget'] = $value;

        else if( $key === 'ad6_widget' )
            $new['ads']['ad6_widget'] = $value;

        else if( $key === 'ad7_widget' )
            $new['ads']['ad7_widget'] = $value;

        else if( $key === 'ad8_widget' )
            $new['ads']['ad8_widget'] = $value;

        else if( $key === 'ad9_widget' )
            $new['ads']['ad9_widget'] = $value;

        else if( $key === 'ad10_widget' )
            $new['ads']['ad10_widget'] = $value;
        
        else if ( $key === 'ad1' )
            $new['ads']['ad1'] = $value;

        else if( $key === 'ad2' )
            $new['ads']['ad2'] = $value;

        else if( $key === 'ad3' )
            $new['ads']['ad3'] = $value;

        else if( $key === 'ad4' )
            $new['ads']['ad4'] = $value;

        else if( $key === 'ad5' )
            $new['ads']['ad5'] = $value;

        else if( $key === 'ad6' )
            $new['ads']['ad6'] = $value;

        else if( $key === 'ad7' )
            $new['ads']['ad7'] = $value;

        else if( $key === 'ad8' )
            $new['ads']['ad8'] = $value;

        else if( $key === 'ad9' )
            $new['ads']['ad9'] = $value;

        else if( $key === 'ad10' )
            $new['ads']['ad10'] = $value;
        else
            $new[$key] = $value;
   }
   // Backup old settings just in case. Do this only one time!
   if (false === get_option('quads_settings_1_5_2')){
      update_option('quads_settings_1_5_2', $settings);
   }
   update_option('quads_settings', $new);
}

/**
 * Change array quads_settings['ad1_widget'] to quads_settings[ad1_widget][code]
 * 
 * return mixed bool|void false when settings are empty
 */
function quads_change_widget_values() {
    $settings = get_option( 'quads_settings' );

    if( empty( $settings ) ) {
        return false;
    }

    foreach ( $settings as $key => $value ) {
        if( $key === 'ad1_widget' && is_string( $settings['ad1_widget'] ) )
            $new['ad1_widget']['code'] = $value;

        else if( $key === 'ad2_widget' && is_string( $settings['ad2_widget'] ) )
            $new['ad2_widget']['code'] = $value;

        else if( $key === 'ad3_widget' && is_string( $settings['ad3_widget'] ) )
            $new['ad3_widget']['code'] = $value;

        else if( $key === 'ad4_widget' && is_string( $settings['ad4_widget'] ) )
            $new['ad4_widget']['code'] = $value;

        else if( $key === 'ad5_widget' && is_string( $settings['ad5_widget'] ) )
            $new['ad5_widget']['code'] = $value;

        else if( $key === 'ad6_widget' && is_string( $settings['ad6_widget'] ) )
            $new['ad6_widget']['code'] = $value;

        else if( $key === 'ad7_widget' && is_string( $settings['ad7_widget'] ) )
            $new['ad7_widget']['code'] = $value;

        else if( $key === 'ad8_widget' && is_string( $settings['ad8_widget'] ) )
            $new['ad8_widget']['code'] = $value;

        else if( $key === 'ad9_widget' && is_string( $settings['ad9_widget'] ) )
            $new['ad9_widget']['code'] = $value;

        else if( $key === 'ad10_widget' && is_string( $settings['ad10_widget'] ) )
            $new['ad10_widget']['code'] = $value;
        else
            $new[$key] = $value;
    }

    update_option( 'quads_settings', $new );
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
        //update_option( 'quads_show_theme_notice', $my_theme->get( 'Name' ) );
        return $my_theme->get( 'Name' );
    }

    return false;
}


/**
 * Check if WP QUADS PRO is installed and version number is higher or equal 1.2.7
 * @deprecated since version 1.5.6
 * @return boolean
 */
function quads_is_advanced_1_2_7() {
    if( quads_is_advanced() && version_compare( QUADS_PRO_VERSION, '1.2.7', '>=' ) ) {
        return true;
    }
    return false;
}

/**
 * Convert all previous post/page settings to the new post_type global options array which has been introduced in 1.4.0
 * 
 * @global array $quads_options
 * @return true if success
 */
function quads_import_post_type_settings(){
    global $quads_options;
    
    // Get previous settings
    $post_setting_old = isset($quads_options['visibility']['AppPost']) ? true : false;
    $page_setting_old = isset($quads_options['visibility']['AppPage']) ? true : false;
    
    // Store them in new array post_types
    if (true === $post_setting_old && true === $page_setting_old) {
        $quads_options['post_types'] = array('post', 'page');
    } else if (true === $post_setting_old && false === $page_setting_old) {
        $quads_options['post_types'] = array('post');
    } else if (false === $post_setting_old && true === $page_setting_old) {
        $quads_options['post_types'] = array('page');
    } else {
        // do nothing
    }
    update_option('quads_settings', $quads_options);
}
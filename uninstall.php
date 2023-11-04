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
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
   exit;

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0.0
 * @return mixed
 */
function quads_get_option_uninstall( $key = '', $default = false ) {
   $quads_options = get_option( 'quads_settings' );
   $value = !empty( $quads_options[$key] ) ? $quads_options[$key] : $default;
   $value = apply_filters( 'quads_get_option', $value, $key, $default );
   return apply_filters( 'quads_get_option_' . $key, $value, $key, $default );
}

if( quads_get_option_uninstall( 'uninstall_on_delete' ) ) {
   /** Delete all the Plugin Options */
   delete_option( 'quads_settings' );
   delete_option( 'quads_install_date' );
   delete_option( 'quads_install_date_flag' );
   delete_option( 'quads_rating_div' );
   delete_option( 'quads_version' );
   delete_option( 'quads_version_upgraded_from' );
   delete_option( 'quads_show_theme_notice' );
   delete_option( 'quads_show_update_notice' );
   delete_option( 'quads_settings_1_5_2' );
   delete_option( 'quads_show_update_notice_1_5_2' );
   delete_option( 'quads_v2_db_no_import' );
   
   /**
    * Delete all vi settings
    */
    delete_option( 'quads_close_vi_welcome_notice' );
    delete_option( 'quads_close_vi_notice' );
    delete_option( 'quads_vi_ads' );
    delete_option( 'quads_vi_settings' );
    delete_option( 'quads_vi_revenue' );
    delete_option( 'quads_vi_variant' );
    delete_option( 'quads_vi_token' );

   /* Delete all post meta options */
   delete_post_meta_by_key( 'quads_timestamp' );
   delete_post_meta_by_key( 'quads_shares' );
   delete_post_meta_by_key( 'quads_jsonshares' );

   // Delete transients
   delete_transient( 'quads_check_theme' );
   delete_transient( 'quads_activation_redirect' );
   delete_option( 'quads-mode' );
   delete_option( 'quads_version' );
   delete_option( 'quads_wp_quads_pro_license_active' );
   delete_option( 'widget_quads_ads_widget' );
   delete_option( 'quads_vi_variant' );

  $arg  = array();
  $arg['post_type']      = 'quads-ads';
  $arg['posts_per_page'] = -1;  
  $arg['post_status']    = array('publish', 'draft');   
  $allposts= get_posts( $arg );
  foreach ($allposts as $eachpost) {
  wp_delete_post( $eachpost->ID, true );
  }
}
<?php

/**
 * Admin Actions
 *
 * @package     QUADS
 * @subpackage  Admin/Actions
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Processes all QUADS actions sent via POST and GET by looking for the 'quads-action'
 * request and running do_action() to call the function
 *
 * @since 1.0
 * @return void
 */
function quads_process_actions() {
    // phpcs:ignore WordPress.Security.NonceVerification.Missing
    if (isset($_POST['quads-action'])) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        do_action('quads_' . sanitize_text_field( wp_unslash( $_POST['quads-action'] ) ), $_POST);
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if (isset($_GET['quads-action'])) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        do_action('quads_' . sanitize_text_field( wp_unslash( $_GET['quads-action'] ) ), $_GET);
    }
}

add_action('admin_init', 'quads_process_actions');

/**
 * Update option quads_show_theme_notice
 * "no" means no further upgrade notices are shown
 */
function quads_close_upgrade_notice() {
    update_option('quads_show_theme_notice', 'no');
}

add_action('quads_close_upgrade_notice', 'quads_close_upgrade_notice');



/**
 * Save vi token
 */
function quads_save_vi_token() {
    echo json_encode(array("status" => "success", "token" => ''));
    wp_die();
}

add_action('wp_ajax_quads_save_vi_token', 'quads_save_vi_token');

add_action('wp_ajax_quads_id_delete', 'quads_id_delete');
function quads_id_delete(){
    check_ajax_referer( 'quads_ajax_nonce', 'nonce' );
   
	if( ! current_user_can( 'manage_options' ) ) { return false; }
    delete_option('add_blocked_ip');
    if (isset($_COOKIE['quads_ad_clicks'])) {
        unset($_COOKIE['quads_ad_clicks']);
        setcookie('quads_ad_clicks', '', time() - 3600, '/'); // empty value and old timestamp
    }
    wp_send_json( array('status'=>esc_html__( 'Operation success', 'quick-adsense-reloaded' ) ));
}

add_action('wp_ajax_quads_remove_old_tracked_data', 'quads_remove_old_tracked_data');
function quads_remove_old_tracked_data() {
    // Check for required parameters
    if ( ! isset( $_POST['nonce'], $_POST['duration'] ) ) {
        wp_send_json_error( array( 'error' => esc_html__( 'Missing required parameters.', 'quick-adsense-reloaded' ) ), 400 );
    }

    // Verify nonce
    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'quads_ajax_nonce' ) ) {
        wp_send_json_error( array( 'error' => esc_html__( 'Invalid nonce.', 'quick-adsense-reloaded' ) ), 403 );
    }

    // Check user capability
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'error' => esc_html__( 'Unauthorized access.', 'quick-adsense-reloaded' ) ), 403 );
    }

    global $wpdb;

    $duration = sanitize_text_field( wp_unslash( $_POST['duration'] ) );
    $allowed_durations = array( 'all', 'everything_before_thisyear', 'first6month' );

    if ( ! in_array( $duration, $allowed_durations, true ) ) {
        wp_send_json_error( array( 'error' => esc_html__( 'Invalid duration value.', 'quick-adsense-reloaded' ) ), 400 );
    }

    $tables = array(
        'quads_impressions_desktop',
        'quads_impressions_mobile',
        'quads_clicks_desktop',
        'quads_clicks_mobile'
    );

    foreach ( $tables as $table ) {
        $table_name = $wpdb->prefix . $table;

        if ( $duration === 'all' ) {
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->query( "TRUNCATE TABLE $table_name" );

        } elseif ( $duration === 'everything_before_thisyear' ) {
             // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->query(
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $wpdb->prepare( "DELETE FROM $table_name WHERE stats_year < %d", gmdate( 'Y' ) )
            );

        } elseif ( $duration === 'first6month' ) {
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $min_date = $wpdb->get_var( "SELECT MIN(stats_date) FROM $table_name" );
            if ( $min_date ) {
                 $six_months_seconds = 6 * 30 * 24 * 60 * 60;
                 $cutoff = $min_date + $six_months_seconds;
                 // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->query(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $wpdb->prepare( "DELETE FROM $table_name WHERE stats_date < %d", $cutoff_date )
                );
            }
        }
    }

    wp_send_json_success( array( 'status' =>  esc_html__( 'Operation successful.', 'quick-adsense-reloaded' ) ) );
}


/**
 * Hide ads txt error notice
 */
function quads_close_ads_txt_error() {
    delete_transient('quads_ads_txt_error');
}
add_action('quads_close_ads_txt_error', 'quads_close_ads_txt_error');
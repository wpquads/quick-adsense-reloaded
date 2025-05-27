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
    wp_send_json( array('status'=>'Operation success'));
}

add_action('wp_ajax_quads_remove_old_tracked_data', 'quads_remove_old_tracked_data');
function quads_remove_old_tracked_data(){
    if ( !wp_verify_nonce(  sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'quads_ajax_nonce' ) ){
        return;  
    }  
	if( ! current_user_can( 'manage_options' ) ) { return false; }
    global $wpdb;
    $duration = sanitize_text_field( wp_unslash( $_POST['duration'] ) );
    if($duration=='all'){
        $quads_impressions_desktop = $wpdb->prefix . 'quads_impressions_desktop';
        $query = "TRUNCATE TABLE $quads_impressions_desktop";
        $wpdb->query($query);

        $quads_impressions_mobile = $wpdb->prefix . 'quads_impressions_mobile';
        $query = "TRUNCATE TABLE $quads_impressions_mobile";
        $wpdb->query($query);

        $quads_clicks_desktop = $wpdb->prefix . 'quads_clicks_desktop';
        $query = "TRUNCATE TABLE $quads_clicks_desktop";
        $wpdb->query($query);

        $quads_clicks_mobile = $wpdb->prefix . 'quads_clicks_mobile';
        $query = "TRUNCATE TABLE $quads_clicks_mobile";
        $wpdb->query($query);
    }else if($duration=='everything_before_thisyear'){
        $quads_impressions_desktop = $wpdb->prefix . 'quads_impressions_desktop';
        $query = "DELETE FROM $quads_impressions_desktop WHERE stats_year < YEAR(CURDATE())";
        $wpdb->query($query);

        $quads_impressions_mobile = $wpdb->prefix . 'quads_impressions_mobile';
        $query = "DELETE FROM $quads_impressions_mobile WHERE stats_year < YEAR(CURDATE())";
        $wpdb->query($query);

        $quads_clicks_desktop = $wpdb->prefix . 'quads_clicks_desktop';
        $query = "DELETE FROM $quads_clicks_desktop WHERE stats_year < YEAR(CURDATE())";
        $wpdb->query($query);

        $quads_clicks_mobile = $wpdb->prefix . 'quads_clicks_mobile';
        $query = "DELETE FROM $quads_clicks_mobile WHERE stats_year < YEAR(CURDATE())";
        $wpdb->query($query);
    }else if($duration=='first6month'){
        global $wpdb;

        $quads_impressions_desktop = $wpdb->prefix . 'quads_impressions_desktop';

        $min_date = $wpdb->get_var("SELECT MIN(stats_date) FROM $quads_impressions_desktop");
        $six_months_seconds = 6 * 30 * 24 * 60 * 60;
        $cutoff = $min_date + $six_months_seconds;

        $wpdb->query(
            $wpdb->prepare("DELETE FROM $quads_impressions_desktop WHERE stats_date < %d", $cutoff)
        );
        $quads_impressions_mobile = $wpdb->prefix . 'quads_impressions_mobile';

        $min_date = $wpdb->get_var("SELECT MIN(stats_date) FROM $quads_impressions_mobile");
        $six_months_seconds = 6 * 30 * 24 * 60 * 60;
        $cutoff = $min_date + $six_months_seconds;

        $wpdb->query(
            $wpdb->prepare("DELETE FROM $quads_impressions_mobile WHERE stats_date < %d", $cutoff)
        );

        $quads_clicks_desktop = $wpdb->prefix . 'quads_clicks_desktop';

        $min_date = $wpdb->get_var("SELECT MIN(stats_date) FROM $quads_clicks_desktop");
        $six_months_seconds = 6 * 30 * 24 * 60 * 60;
        $cutoff = $min_date + $six_months_seconds;

        $wpdb->query(
            $wpdb->prepare("DELETE FROM $quads_clicks_desktop WHERE stats_date < %d", $cutoff)
        );
        $quads_clicks_mobile = $wpdb->prefix . 'quads_clicks_mobile';

        $min_date = $wpdb->get_var("SELECT MIN(stats_date) FROM $quads_clicks_mobile");
        $six_months_seconds = 6 * 30 * 24 * 60 * 60;
        $cutoff = $min_date + $six_months_seconds;

        $wpdb->query(
            $wpdb->prepare("DELETE FROM $quads_clicks_mobile WHERE stats_date < %d", $cutoff)
        );   
    }
    wp_send_json( array('status'=>'Operation success'));
}

/**
 * Hide ads txt error notice
 */
function quads_close_ads_txt_error() {
    delete_transient('quads_ads_txt_error');
}
add_action('quads_close_ads_txt_error', 'quads_close_ads_txt_error');
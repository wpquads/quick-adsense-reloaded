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
        do_action('quads_' . $_POST['quads-action'], $_POST);
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if (isset($_GET['quads-action'])) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        do_action('quads_' . $_GET['quads-action'], $_GET);
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


/**
 * Hide ads txt error notice
 */
function quads_close_ads_txt_error() {
    delete_transient('quads_ads_txt_error');
}
add_action('quads_close_ads_txt_error', 'quads_close_ads_txt_error');
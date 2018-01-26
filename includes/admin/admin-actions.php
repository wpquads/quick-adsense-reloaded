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
    if (isset($_POST['quads-action'])) {
        do_action('quads_' . $_POST['quads-action'], $_POST);
    }

    if (isset($_GET['quads-action'])) {
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
 * Close vi welcome notice and do not show again
 */
function quads_close_vi_welcome_notice() {
    update_option('quads_close_vi_welcome_notice', 'yes');
}

add_action('quads_close_vi_welcome_notice', 'quads_close_vi_welcome_notice');

/**
 * Close vi update notice and show it one week later again
 */
function quads_show_vi_notice_later() {
    $nextweek = time() + (7 * 24 * 60 * 60);
    $human_date = date('Y-m-d h:i:s', $nextweek);
    update_option('quads_show_vi_notice_later', $human_date);
    update_option('quads_close_vi_notice', 'yes');
}

add_action('quads_show_vi_notice_later', 'quads_show_vi_notice_later');

/**
 * Save vi token
 */
function quads_save_vi_token() {
    global $quads_options;

    if (empty($_POST['token'])) {
        echo json_encode(array("status" => "failed"));
        wp_die();
    }

    // Save token before trying to create ads.txt
    update_option('quads_vi_token', $_POST['token']);

    if (!isset($quads_options['adsTxtEnabled'])) {
        set_transient('quads_vi_ads_txt_disabled', true, 300);
        delete_transient('quads_vi_ads_txt_error');
        delete_transient('quads_vi_ads_txt_notice');
        echo json_encode(array("status" => "success", "token" => $_POST['token'], "adsTxt" => 'disabled'));
        wp_die();
    }

    $vi = new wpquads\vi();

    if ($vi->createAdsTxt()) {
        set_transient('quads_vi_ads_txt_notice', true, 300);
        delete_transient('quads_vi_ads_txt_error');
    } else {
        set_transient('quads_vi_ads_txt_error', true, 300);
        delete_transient('quads_vi_ads_txt_notice');
    }


    // Create AdSense ads.txt entries
    $adsense = new \wpquads\adsense($quads_options);
    $adsense->writeAdsTxt();

    //sleep(5);
    echo json_encode(array("status" => "success", "token" => $_POST['token']));
    wp_die();
}

add_action('wp_ajax_quads_save_vi_token', 'quads_save_vi_token');

/**
 * Save vi ad settings and create ad code
 */
function quads_save_vi_ads() {
    global $quads;

    $return = $quads->vi->setAdCode();

    if ($return) {
        wp_die($return);
    } else {
        wp_die(array('status' => 'error', 'message' => 'Unknown API Error. Can not get vi ad code'));
    }
}
add_action('wp_ajax_quads_save_vi_ads', 'quads_save_vi_ads');

/**
 * Logout of vi
 */
function quads_logout_vi() {
    delete_option('quads_vi_token');
}
add_action('quads_logout_vi', 'quads_logout_vi');

/**
 * Hide ads txt information notice
 */
function quads_close_ads_txt_notice() {
    delete_transient('quads_ads_txt_notice');
}
add_action('quads_close_ads_txt_notice', 'quads_close_ads_txt_notice');

/**
 * Hide ads txt error notice
 */
function quads_close_ads_txt_error() {
    delete_transient('quads_ads_txt_error');
}
add_action('quads_close_ads_txt_error', 'quads_close_ads_txt_error');



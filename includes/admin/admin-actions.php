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
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Processes all QUADS actions sent via POST and GET by looking for the 'quads-action'
 * request and running do_action() to call the function
 *
 * @since 1.0
 * @return void
 */
function quads_process_actions() {
	if ( isset( $_POST['quads-action'] ) ) {
		do_action( 'quads_' . $_POST['quads-action'], $_POST );
	}

	if ( isset( $_GET['quads-action'] ) ) {
		do_action( 'quads_' . $_GET['quads-action'], $_GET );
	}
}
add_action( 'admin_init', 'quads_process_actions' );

/**
 * Update option quads_show_theme_notice
 * "no" means no further upgrade notices are shown
 */
function quads_close_upgrade_notice(){
    update_option ('quads_show_theme_notice', 'no');
}
add_action('quads_close_upgrade_notice', 'quads_close_upgrade_notice');


/**
 * Close vi notice and do not show again
 */
function quads_close_vi_notice(){
    update_option ('quads_close_vi_notice', 'yes');
    delete_option('quads_show_vi_notice_later');
}
add_action('quads_close_vi_notice', 'quads_close_vi_notice');

/**
 * Close vi update notice and show it one week later again
 */
function quads_show_vi_notice_later(){
    $nextweek = time() + (7 * 24 * 60 * 60);
    $human_date = date( 'Y-m-d h:i:s', $nextweek );
    update_option( 'quads_show_vi_notice_later', $human_date );
    update_option( 'quads_close_vi_notice', 'yes' );
    
}
add_action('quads_show_vi_notice_later', 'quads_show_vi_notice_later');

/**
 * Save vi token
 */
function quads_save_vi_token(){  
    if (empty($_POST['token'])){
        echo json_encode( array("status" => "failed") );
        exit;
    }
    
    update_option('quads_vi_token', $_POST['token']);
    echo json_encode( array("status" => "success") );
    exit;
}
add_action( 'wp_ajax_quads_save_vi_token', 'quads_save_vi_token' );

/**
 * Logout of vi
 */
function quads_logout_vi(){
    delete_option('quads_vi_token');
}
add_action('quads_logout_vi', 'quads_logout_vi');


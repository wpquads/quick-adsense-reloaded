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
global $quads_options;

    if (empty($_POST['token'])){
        echo json_encode( array("status" => "failed") );
        exit;
    }
    
    // Save token before trying to create ads.txt
    update_option('quads_vi_token', $_POST['token']);
    //sleep(5);
    echo json_encode( array("status" => "success", "token" => $_POST['token']) );
    
    $vi = new wpquads\vi();
    
    if($vi->createAdsTxt()){
       set_transient('quads_vi_ads_txt_notice', true, 300);
       delete_transient('quads_vi_ads_txt_error');
    } else {
       set_transient('quads_vi_ads_txt_error', true, 300);
       delete_transient('quads_vi_ads_txt_notice');
    }
    
    // create default ad code
    //$vi->setAdCode();
    
    // Create AdSense ads.txt entries
    $adsense = new \wpquads\adsense($quads_options);
    $adsense->writeAdsTxt();
    

    exit;
}
add_action( 'wp_ajax_quads_save_vi_token', 'quads_save_vi_token' );

/**
 * Save vi ad settings and create ad code
 */
function quads_save_vi_ads(){  
    global $quads;
    
    //wp_die('tester' . $quads->vi->setAdCode());
    
    if ($quads->vi->setAdCode()) {
        //wp_die(json_encode(array('status' => 'success', 'data' => $quads->vi->getAdCode())));
        wp_die($quads->vi->setAdCode());
    } else {
        wp_die(array('status' => 'error', 'message' => 'Unknown API Error. Can not get vi ad code'));
    }
}
add_action( 'wp_ajax_quads_save_vi_ads', 'quads_save_vi_ads' );



/**
 * Logout of vi
 */
function quads_logout_vi(){
    delete_option('quads_vi_token');
}
add_action('quads_logout_vi', 'quads_logout_vi');


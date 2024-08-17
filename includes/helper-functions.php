<?php

/**
 * Helper Functions
 *
 * @package     QUADS
 * @subpackage  Helper/Templates
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Helper method to check if user is in the plugins page.
 *
 * @author René Hermenau
 * @since  1.4.0
 *
 * @return bool
 */
function quads_is_plugins_page() {
    if(function_exists('get_current_screen')){
        $screen = get_current_screen();
            if(is_object($screen)){
                if($screen->id == 'plugins' || $screen->id == 'plugins-network'){
                    return true;
                }
            }
    }
    return false;
}

/**
 * display deactivation logic on plugins page
 * 
 * @since 1.4.0
 */
function quads_add_deactivation_feedback_modal() {

    $screen = get_current_screen();
    if( !is_admin() && !quads_is_plugins_page()) {
        return;
    }

    $current_user = wp_get_current_user();
    if( !($current_user instanceof WP_User) ) {
        $email = '';
    } else {
        $email = trim( $current_user->user_email );
    }

    include QUADS_PLUGIN_DIR . 'includes/admin/views/deactivate-feedback.php';
}

/**
 * send feedback via email
 * 
 * @since 1.4.0
 */
function quads_send_feedback() {

    if( function_exists('current_user_can') && ! current_user_can( 'manage_options' ) ) {
        die( esc_html__( 'You are not allowed to perform this action', 'quick-adsense-reloaded' ) );
    }

    if( isset( $_POST['data'] ) ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: Nince verification si done below
        parse_str( $_POST['data'], $form );
    }
    if ( ! wp_verify_nonce( $form['quads_feedback_nonce'] , 'quads_feedback_nonce' ) ) {
        die( esc_html__( 'Invalid nonce', 'quick-adsense-reloaded' ) ); 
    }
    
    $text = '';
    if( isset( $form['quads_disable_text'] ) ) {
        $text = implode( "\n\r", $form['quads_disable_text'] );
    }

    $headers = array();

    $from = isset( $form['quads_disable_from'] ) ? $form['quads_disable_from'] : '';
    if( $from ) {
        $headers[] = "From: $from";
        $headers[] = "Reply-To: $from";
    }

    $subject = "WP Quads";

    $subject .= isset( $form['quads_disable_reason'] ) ? ' - '.$form['quads_disable_reason'] : '(no reason given)';

    $success = wp_mail( 'team@magazine3.in', $subject, $text, $headers );

   // error_log(print_r($success, true));
    die();
}
add_action( 'wp_ajax_quads_send_feedback', 'quads_send_feedback' );
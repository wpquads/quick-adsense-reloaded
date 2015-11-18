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



function quads_save_order(){
        global $quads_options;
        // Get all settings
        
        $current_list = get_option('quads_networks');
        $new_order = $_POST['quads_list'];
        $new_list = array();
   
        /* First write the sort order */
        foreach ($new_order as $n){
            if (isset($current_list[$n])){
                $new_list[$n] = $current_list[$n];
                
            }
        }
        //print_r($_POST);
        /* Update sort order of networks */
        update_option('quads_networks', $new_list);
        die();
}
add_action ('wp_ajax_quads_update_order', 'quads_save_order');
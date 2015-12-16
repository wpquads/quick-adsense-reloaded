<?php

/**
 * Meta box functions
 *
 * @package     QUADS
 * @subpackage  Functions/Meta Boxes
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.4
 */

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function quads_add_meta_box() {

	$screens = array( 'post', 'page' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'quads_sectionid',
			__( 'Quick AdSense Reloaded', 'quick-adsense-reloaded' ),
			'quads_meta_box_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'quads_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function quads_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'quads_save_meta_box_data', 'quads_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $post->ID, 'quads_disable_all_ads', true );

	echo '<label for="quads_new_field">';
	_e( 'Disable all ads', 'quads_textdomain' );
	echo '</label> ';
	echo '<input type="text" id="quads_disable_all_ads" name="quads_disable_all_ads" value="' . esc_attr( $value ) . '" size="25" />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function quads_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['quads_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['quads_meta_box_nonce'], 'quads_save_meta_box_data' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['quads_disable_all_ads'] ) ) {
		return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['quads_disable_all_ads'] );

	// Update the meta field in the database.
	update_post_meta( $post_id, 'quads_disable_all_ads', $my_data );
}
add_action( 'save_post', 'quads_save_meta_box_data' );
<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function quads_sanitize_post_meta($key, $meta){

    $response = null;

    switch ($key) {

      case 'visibility_include':
      case 'visibility_exclude':
			$response = wp_unslash($meta);         
        break;

		case 'targeting_include':
		case 'targeting_exclude':
        $response = wp_unslash($meta);          
		break;
		case 'code':
			$response = wp_unslash($meta);
		break;
    case 'random_ads_list':
			$response = wp_unslash($meta); 
		break; 
		case 'ads_list':
			$response = wp_unslash($meta);          
		break;
      default:
        $response = sanitize_text_field(wp_unslash($meta));
        break;
    }

    return $response;
    
  }

function quadsGetPostIdByMetaKeyValue( $meta_key, $meta_value ) {
    global $wpdb;

    // Use direct database query for better performance than meta_query
    // This leverages indexes on postmeta table more efficiently
	
	$post_id = wp_cache_get('quads_post_id_by_meta_key_value_'.$meta_key.'_'.$meta_value, 'quick-adsense-reloaded');
	if(false === $post_id){
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is fixed and safe
		$post_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM `$wpdb->postmeta` WHERE meta_key = %s AND meta_value = %s LIMIT 1",$meta_key,$meta_value));
		wp_cache_set('quads_post_id_by_meta_key_value_'.$meta_key.'_'.$meta_value, $post_id, 'quick-adsense-reloaded', 3600);
	}
    return ! empty( $post_id ) ? (int) $post_id : null;
}

 /**
 * since v2.0
 * Validate a single line.
 *
 * @param string $line        The line to validate.
 * @param string $line_number The line number being evaluated.
 *
 * @return array {
 *     @type string $sanitized Sanitized version of the original line.
 *     @type array  $errors    Array of errors associated with the line.
 * }
 */
function quads_validate_ads_txt_line( $line, $line_number ) {
    
	$domain_regex = '/^((?=[a-z0-9-]{1,63}\.)(xn--)?[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}$/i';
	$errors       = array();

	if ( empty( $line ) ) {
		$sanitized = '';
	} elseif ( 0 === strpos( $line, '#' ) ) { // This is a full-line comment.
		$sanitized = wp_strip_all_tags( $line );
	} elseif ( 1 < strpos( $line, '=' ) ) { // This is a variable declaration.
		// The spec currently supports CONTACT and SUBDOMAIN.
		if ( ! preg_match( '/^(CONTACT|SUBDOMAIN)=/i', $line ) ) {
			$errors[] = array(
				'line' => $line_number,
				'type' => 'invalid_variable',
			);
		} elseif ( 0 === stripos( $line, 'subdomain=' ) ) { // Subdomains should be, well, subdomains.
			// Disregard any comments.
			$subdomain = explode( '#', $line );
			$subdomain = $subdomain[0];

			$subdomain = explode( '=', $subdomain );
			array_shift( $subdomain );

			// If there's anything other than one piece left something's not right.
			if ( 1 !== count( $subdomain ) || ! preg_match( $domain_regex, $subdomain[0] ) ) {
				$subdomain = implode( '', $subdomain );
				$errors[]  = array(
					'line'  => $line_number,
					'type'  => 'invalid_subdomain',
					'value' => $subdomain,
				);
			}
		}

		$sanitized = wp_strip_all_tags( $line );

		unset( $subdomain );
	} else { // Data records: the most common.
		// Disregard any comments.
		$record = explode( '#', $line );
		$record = $record[0];

		// Record format: example.exchange.com,pub-id123456789,RESELLER|DIRECT,tagidhash123(optional).
		$fields = explode( ',', $record );

		if ( 3 <= count( $fields ) ) {
			$exchange     = trim( $fields[0] );
			$pub_id       = trim( $fields[1] );
			$account_type = trim( $fields[2] );

			if ( ! preg_match( $domain_regex, $exchange ) ) {
				$errors[] = array(
					'line'  => $line_number,
					'type'  => 'invalid_exchange',
					'value' => $exchange,
				);
			}

			if ( ! preg_match( '/^(RESELLER|DIRECT)$/i', $account_type ) ) {
				$errors[] = array(
					'line' => $line_number,
					'type' => 'invalid_account_type',
				);
			}

			if ( isset( $fields[3] ) ) {
				$tag_id = trim( $fields[3] );

				// TAG-IDs appear to be 16 character hashes.
				// TAG-IDs are meant to be checked against their DB - perhaps good for a service or the future.
				if ( ! empty( $tag_id ) && ! preg_match( '/^[a-f0-9]{16}$/', $tag_id ) ) {
					$errors[] = array(
						'line'  => $line_number,
						'type'  => 'invalid_tagid',
						'value' => $fields[3],
					);
				}
			}

			$sanitized = wp_strip_all_tags( $line );
		} else {
			// Not a comment, variable declaration, or data record; therefore, invalid.
			// Early on we commented the line out for safety but it's kind of a weird thing to do with a JS AYS.
			$sanitized = wp_strip_all_tags( $line );

			$errors[] = array(
				'line' => $line_number,
				'type' => 'invalid_record',
			);
		}

		unset( $record, $fields );
	}

	return array(
		'sanitized' => $sanitized,
		'errors'    => $errors,
	);
}


////

function quads_change_mode() {

	check_ajax_referer( 'quads_ajax_nonce', 'nonce' );

	if( ! current_user_can( 'manage_options' ) )
	return;

	ignore_user_abort( true );
	$quads_settings_backup = get_option( 'quads_settings_backup' );
	$quads_settings = get_option( 'quads_settings' );
	if($quads_settings_backup){
		update_option('quads_settings', $quads_settings_backup);
	}else{
		update_option('quads_settings', $quads_settings);
	}
    
	update_option('quads_settings_backup', $quads_settings);
	if( isset( $_REQUEST['mode'] )){
		update_option('quads-mode',sanitize_text_field(wp_unslash( $_REQUEST['mode'] )));
	}
	
	wp_send_json ( array('status' => 't') );

}
add_action('wp_ajax_quads_change_mode', 'quads_change_mode');
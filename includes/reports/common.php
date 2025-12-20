<?php
if( !defined( 'ABSPATH' ) )
	exit;
add_action( 'admin_enqueue_scripts', 'quads_load_adsnese_scripts', 100 );
define('client_id','434993230199-hk6lg7d10mi9lja7euvqckef6ji2i4n0.apps.googleusercontent.com');
define('client_secret','LTUkw1OpRaL4S-kvDaS7tMU_');
add_action( 'rest_api_init', 'quads_registerRoute');

function quads_registerRoute($hook){
	register_rest_route( 'quads-adsense', 'quads_confirm_code', array(
		'methods'    => 'POST',
		'callback'   => 'quads_confirm_code',
		'permission_callback' => function(){
			return current_user_can( 'manage_options' );
		}
	));
	register_rest_route( 'quads-adsense', 'quads_adsense_get_details', array(
		'methods'    => 'POST',
		'callback'   => 'quads_adsense_get_details',
		'permission_callback' => function(){
			return current_user_can( 'manage_options' );
		}
	));
	register_rest_route( 'quads-adsense', 'get_report_status', array(
		'methods'    => 'POST',
		'callback'   => 'quads_adsense_get_report_status',
		'permission_callback' => function(){
			return current_user_can( 'manage_options' );
		}
	));
	register_rest_route( 'quads-adsense', 'get_report_adsense', array(
		'methods'    => 'POST',
		'callback'   => 'quads_adsense_get_report_data',
		'permission_callback' => function(){
			return current_user_can( 'manage_options' );
		}
	));
	register_rest_route( 'quads-adsense', 'get_report_stats', array(
		'methods'    => 'POST',
		'callback'   => 'quads_ads_stats_get_report_data',
		'permission_callback' => function(){
			return current_user_can( 'manage_options' );
		}
	));
	register_rest_route( 'quads-adsense', 'get_report_abtesting', array(
		'methods'    => 'POST',
		'callback'   => 'quads_adsense_get_report_abtesting_data',
		'permission_callback' => function(){
			return current_user_can( 'manage_options' );
		}
	));
	register_rest_route( 'quads-adsense', 'revoke_adsense_link', array(
		'methods'    => 'POST',
		'callback'   => 'quads_adsense_revoke_adsense_link',
		'permission_callback' => function(){
			return current_user_can( 'manage_options' );
		}
	));
}
function quads_adsense_revoke_adsense_link($request_data){
	$parameters = $request_data->get_params();
	$options = quads_get_option_adsense();
	$account_id = $parameters['account_id'];
	$token = $options['accounts'][ $account_id ]['refresh_token'];

	$url  = 'https://accounts.google.com/o/oauth2/revoke?token=' . esc_html($token);
	$args = array(
		'timeout' => 5,
		'header'  => array( 'Content-type' => 'application/x-www-form-urlencoded' ),
	);

	$response = wp_remote_post( $url, $args );
	if ( is_wp_error( $response ) ) {
		echo json_encode( array( 'status' => false ) );
	} else {
		//  remove all the adsense stats
		delete_option("quads_adsense_api_data");
		echo json_encode( array( 'status' => true ) );
	}
}
function quads_adsense_get_report_status($request_data){
	$parameters = $request_data->get_params();
	$reportlist =array();
	$quads_get_option_adsense =quads_get_option_adsense();
	$account_id ='';
	$status = false;
	if(isset($quads_get_option_adsense['accounts']) && !empty($quads_get_option_adsense['accounts'])){
		//$account_id =implode(',',array_keys($quads_get_option_adsense['accounts']));
		$account_id = $quads_get_option_adsense['accounts'][""]["details"];
		$status = true;
	}
	$reportlist['adsense'] =  array("status"=>$status,"account_id" =>$account_id);
	return $reportlist;
}
function quads_confirm_code($request_data){

	$parameters = $request_data->get_params();
	$code = isset($parameters['report']['adsense_code']) ?urldecode( $parameters['report']['adsense_code'] ) :'';
	$cid  = client_id;
	$cs   = client_secret;
	$code_url     = 'https://www.googleapis.com/oauth2/v4/token';
	$redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
	$grant_type   = 'authorization_code';

	$args = array(
		'timeout' => 10,
		'body'    => array(
			'code'          => $code,
			'client_id'     => $cid,
			'client_secret' => $cs,
			'redirect_uri'  => $redirect_uri,
			'grant_type'    => $grant_type,
		),
	);
	$response = wp_remote_post( $code_url, $args );

	if ( is_wp_error( $response ) ) {
		return json_encode(
			array(
				'status' => false,
				'msg'    => esc_html__( 'error while submitting code', 'quick-adsense-reloaded' ),
				'raw'    => $response->get_error_message(),
			)
		);
	} else {
		$token      = json_decode( $response['body'], true );

		if ( null !== $token && isset( $token['refresh_token'] ) ) {
			$expires          = time() + absint( $token['expires_in'] );
			$token['expires'] = $expires;
			echo json_encode(
				array(
					'status'     => true,
					'token_data' => $token,
				)
			);

		} else {
			echo json_encode(
				array(
					'status'        => false,
					'response_body' => $response['body'],
				)
			);
		}
	}

	die;
}
function quads_adsense_get_details($request_data){
	$parameters = $request_data->get_params();

	$url        = 'https://adsense.googleapis.com/v2/accounts';
	$token_data = wp_unslash( $parameters );

	if ( ! is_array( $token_data ) ) {

		echo json_encode(
			array(
				'status'    => false,
				'error_msg' => esc_html__( 'No token provided. Token data needed to get account details.', 'quick-adsense-reloaded' ),
			)
		);
		die;

	}

	$headers = array( 'Authorization' => 'Bearer ' . $token_data['access_token'] );
	$response = wp_remote_get( $url, array( 'headers' => $headers ) );

	if ( is_wp_error( $response ) ) {

		echo json_encode(
			array(
				'status'    => false,
				'error_msg' => $response->get_error_message(),
			)
		);

	} else {

		$accounts = json_decode( $response['body'], true );
		if ( isset( $accounts ) ) {
			$options = quads_get_option_adsense();
			$options['connect_error'] = array();
			update_option( 'quads_adsense_api_data', $options );

			// $adsense_id = $accounts['items'][0]['id'];
			// $name = $accounts['items'][0]['name'];
			$adsense_id = $accounts["accounts"][0]['displayName'];
			$name = $accounts["accounts"][0]['name'];

			// quads_save_token_from_data( $token_data, $accounts['items'][0]);
			quads_save_token_from_data( $token_data, $accounts["accounts"][0]);
			echo json_encode(
				array(
					'status'     => true,
					'adsense_id' => $adsense_id,
					'name' => $name,
				)
			);

		} else {
			if ( isset( $accounts['error'] ) ) {
				$msg = esc_html__( 'An error occurred while requesting account details.', 'quick-adsense-reloaded' );
				if ( isset( $accounts['error']['message'] ) ) {
					$msg = $accounts['error']['message'];
				}

				$options = get_option('quads_adsense_api_data');
				$options['connect_error'] = array(
					'message' => $msg,
				);

				if ( isset( $accounts['error']['errors'][0]['reason'] ) ) {
					$options['connect_error']['reason'] = $accounts['error']['errors'][0]['reason'];
				}

				update_option( 'quads_adsense_api_data', $options );
				echo json_encode(
					array(
						'status'    => false,
						'error_msg' => $msg,
						'raw'       => $accounts['error'],
					)
				);
			}
		}

	}
	die;
}
function quads_get_option_adsense(){
	$default_options =array(
		'accounts'          => array(),
		'ad_codes'          => array(),
		'unsupported_units' => array(),
		'quota'             => array(
			'count' => 20,
			'ts'    => 0,
		),
		'connect_error' => array(),
	);
	$options = get_option( 'quads_adsense_api_data', array() );
	if ( ! is_array( $options ) ) {
		$options = array();
	}
	return $options + $default_options;
}

function quads_save_token_from_data( $token, $details, $args = array() ) {
	$empty_account_data = array(
		'access_token'  => '',
		'refresh_token' => '',
		'expires'       => 0,
		'token_type'    => '',
		'ad_units'    => array(),
		'details'     => array(),
	);
	$options    = quads_get_option_adsense();
	$adsense_id = $details ['id'];

	if ( ! isset( $options['accounts'][ $adsense_id ] ) ) {
		$options['accounts'][ $adsense_id ] = $empty_account_data;
	}
	$options['accounts'][ $adsense_id ] = array(
		'access_token'  => $token['access_token'],
		'refresh_token' => $token['refresh_token'],
		'expires'       => $token['expires'],
		'token_type'    => $token['token_type'],
	);
	$options['accounts'][ $adsense_id ]['details'] = $details;
	update_option( 'quads_adsense_api_data', $options );
}

function quads_has_token( $adsense_id = '' ) {
	if ( empty( $adsense_id ) ) {
		return false;
	}
	$has_token = false;
	$options   = get_option();
	if ( isset( $options['accounts'][ $adsense_id ] ) && ! empty( $options['accounts'][ $adsense_id ]['refresh_token'] ) ) {
		$has_token = true;
	}
	return $has_token;
}

/**
 * Get A/B testing report data for REST API
 *
 * Retrieves A/B testing statistics from the database and returns them
 * as formatted HTML table for display in the admin interface.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request REST API request object
 * @return WP_REST_Response|WP_Error REST API response with table HTML or error
 */
function quads_adsense_get_report_abtesting_data( $request ) {
	global $wpdb;

	$cache_key = 'quads_abtesting_stats';
	$results   = wp_cache_get( $cache_key, 'quick-adsense-reloaded' );

	if ( false === $results ) {
		$table_name = $wpdb->prefix . 'quads_stats';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is fixed and safe
		$results = $wpdb->get_results( "SELECT * FROM `$table_name`" );
		wp_cache_set( $cache_key, $results, 'quick-adsense-reloaded', 3600 );
	}

	$quads_table = '';

	if ( ! empty( $results ) ) {
		$quads_table = '<table id="blocked_id_table">';
		$quads_table .= '<tbody>';
		$quads_table .= '<tr class="b_in_" style="font-weight: bold;">';
		$quads_table .= '<td class="b_in_">' . esc_html__( 'ID', 'quick-adsense-reloaded' ) . '</td>';
		$quads_table .= '<td class="b_in_">' . esc_html__( 'Beginning Of Post', 'quick-adsense-reloaded' ) . '</td>';
		$quads_table .= '<td class="b_in_">' . esc_html__( 'End Of Post', 'quick-adsense-reloaded' ) . '</td>';
		$quads_table .= '<td class="b_in_">' . esc_html__( 'Middle Of Post', 'quick-adsense-reloaded' ) . '</td>';
		$quads_table .= '<td class="b_in_">' . esc_html__( 'After more Tag', 'quick-adsense-reloaded' ) . '</td>';
		$quads_table .= '</tr>';

		foreach ( $results as $row ) {
			$quads_table .= '<tr class="b_in">';
			$quads_table .= '<td class="b_in">' . esc_html( $row->id ) . '</td>';
			$quads_table .= '<td class="b_in">' . esc_html( isset( $row->Beginning_of_post ) ? $row->Beginning_of_post : '' ) . '</td>';
			$quads_table .= '<td class="b_in">' . esc_html( isset( $row->End_of_post ) ? $row->End_of_post : '' ) . '</td>';
			$quads_table .= '<td class="b_in">' . esc_html( isset( $row->Middle_of_post ) ? $row->Middle_of_post : '' ) . '</td>';
			$quads_table .= '<td class="b_in">' . esc_html( isset( $row->After_more_tag ) ? $row->After_more_tag : '' ) . '</td>';
			$quads_table .= '</tr>';
		}

		$quads_table .= '</tbody>';
		$quads_table .= '</table>';
	}

	return rest_ensure_response(
		array(
			'status'     => 'success',
			'success_msg' => $quads_table,
		)
	);
}

function quads_adsense_get_report_data($request_data){

	$parameters = $request_data->get_params();
	$report_period = (isset($parameters['report_period'])&& !empty($parameters['report_period']))?$parameters['report_period'] :'';
	$report_type = (isset($parameters['report_type'])&& !empty($parameters['report_type']))?$parameters['report_type'] :'';
	$input_based = (isset($parameters['input_based'])&& !empty($parameters['input_based']))?$parameters['input_based'] :'';
	$report_view_type = (isset($parameters['report_view_type'])&& !empty($parameters['report_view_type']))?$parameters['report_view_type'] :'';

	$forcast_date_count = 0;

	$endDate = (isset($parameters['endDate'])&& $parameters['endDate'])?$parameters['endDate'] :gmdate('Y-m-d');

	switch ($report_period) {
		case 'last_7_days':
			$startDate = strtotime(" -6 day");
			$forcast_date_count = 7;
			break;
		case 'last_15_days':
			$startDate = strtotime(" -14 day");
			$forcast_date_count = 15;
			break;
		case 'last_30_days':
			$startDate = strtotime(" -29 day");
			$forcast_date_count = 30;
			break;
		case 'last_6_months':
			$startDate = strtotime("-6 month");
			$forcast_date_count = 180;
			break;
		case 'last_1_year':

			$startDate = strtotime('-1 year');
			$forcast_date_count = 365;
			break;
		case 'custom':

			$startDate = ( isset( $parameters['cust_fromdate'] ) && ! empty( $parameters['cust_fromdate'] ) ) ? strtotime( str_replace( "/", "-", $parameters['cust_fromdate'] ) ) : strtotime("now");
			$endDate = ( isset( $parameters['cust_todate'] ) && ! empty( $parameters['cust_todate'] ) ) ? gmdate( "Y-m-d", strtotime(str_replace( "/", "-", $parameters['cust_todate'] ) ) ) : gmdate( "Y-m-d" );
			$forcast_date_count = 365;
			break;
		case 'all_time':
			$startDate = strtotime('-3 year');
			$forcast_date_count = 900;
			break;
		default:
			$forcast_date_count = 7;
			$startDate = strtotime(" -6 day");
			break;
	}

	$account_id = $parameters['account_id'];
	$startDate = gmdate( "Y-m-d", $startDate );//date('Y-m-d',$startDate);
	$token_data    = quads_adsense_get_access_token($account_id);

	switch ($report_type){
		case 'earning_forcast':
			$url        = 'https://adsense.googleapis.com/v2/accounts/'.esc_html($account_id).'/reports:generate?dateRange='.esc_html($report_period).'&dimensions=MONTH&metrics=ESTIMATED_EARNINGS';
			break;
		case 'top_device_type':
			$report_type = 'PLATFORM_TYPE_CODE';
			$url        = 'https://adsense.googleapis.com/v2/accounts/'.esc_html($account_id).'/reports:generate?dateRange='.esc_html($report_period).'&dimensions=PLATFORM_TYPE_CODE&metrics=TOTAL_EARNINGS';
			break;
		case 'earning':
		default:
			$report_type = 'EARNINGS';
			// $url        = 'https://www.googleapis.com/adsense/v1.4/accounts/'.esc_html($account_id).'/reports?startDate='.esc_html($startDate).'&endDate='.esc_html($endDate).'&dimension=DATE&dimension=EARNINGS&metric=EARNINGS&useTimezoneReporting=true';
			$url        = 'https://adsense.googleapis.com/v2/accounts/'.esc_html($account_id).'/reports:generate?dateRange='.esc_html($report_period).'&dimensions=MONTH&metrics=TOTAL_EARNINGS';
			break;
	}
	$token_data = wp_unslash( $token_data);

	$headers = array( 'Authorization' => 'Bearer ' . $token_data );
	$response = wp_remote_get( $url, array( 'headers' => $headers ) );

	if ( is_wp_error( $response ) ) {

		echo json_encode(
			array(
				'status'    => false,
				'error_msg' => $response->get_error_message(),
			)
		);

	} else {
		// $adsense_data_response = json_decode( $response['body'], true );
		return json_decode( $response['body'], true );

		// return $adsense_data_response['rows'];
	}
	die;
}

function quads_ads_stats_get_report_data($request_data, $ad_id=''){
	
	global $wpdb;
    $ad_stats = array();
	
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
	if( isset( $_GET['id'] ) ) {
	    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	    $ad_id = absint( $_GET['id'] );
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
	if( isset( $_GET['day'] ) ) {
	    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	    $day = sanitize_text_field( $_GET['day'] );
	}
	$todays_date = gmdate("Y-m-d");
	$individual_ad_dates = '';
	$array_top5=array();
	
		if( $day == "last_7_days" ){

			$loop = 7 ;
			$month= gmdate("m");
			$date_= gmdate("d");
			$year= gmdate("Y");
			$dates_i = array() ;
			$dates_c = '';
			for( $i=0; $i<=$loop; $i++ ){
				$dates_i[] = ''.gmdate('Y-m-d', mktime(0,0,0,$month,( $date_-$i ) , $year ) );
			}
			
			sort($dates_i);
			$from_date = $todays_date;
			$to_date = $dates_i[0];
			if($ad_id=='all') {
			//old db results
			$results_impresn_S = wp_cache_get('quads_stats_impressions_all_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_S) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_S = $wpdb->get_results($wpdb->prepare("SELECT date_impression,ad_date FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ", array($to_date, $from_date)));
				wp_cache_set('quads_stats_impressions_all_' . $from_date . '_' . $to_date, $results_impresn_S, 'quick-adsense-reloaded', 3600);
			}

			$results_impresn_S_2 = wp_cache_get('quads_stats_impressions_all_group_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_S_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_S_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, IFNULL(SUM(ad_impressions),0) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_impressions_all_group_' . $from_date . '_' . $to_date, $results_impresn_S_2, 'quick-adsense-reloaded', 3600);
			}
			//new db results
			$results_impresn_desk_1 = wp_cache_get('quads_stats_impressions_desktop_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_impressions_desktop_' . $from_date . '_' . $to_date, $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
			}
			if ($results_impresn_desk_1) {
				$results_impresn_S = array_merge($results_impresn_desk_1, $results_impresn_S);
			}
			$results_impresn_mob_1 = wp_cache_get('quads_stats_impressions_mobile_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_mob_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date))  as ad_date  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_impressions_mobile_' . $from_date . '_' . $to_date, $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
			}
			if ($results_impresn_mob_1) {
				$results_impresn_S = array_merge($results_impresn_mob_1, $results_impresn_S);
			}

			$results_impresn_desk_2 = wp_cache_get('quads_stats_impressions_desktop_group_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_desk_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_id,IFNULL(SUM(stats_impressions),0) as impressions,stats_date as ad_thetime,'desktop' as  ad_device_name  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d GROUP BY stats_date", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_impressions_desktop_group_' . $from_date . '_' . $to_date, $results_impresn_desk_2, 'quick-adsense-reloaded', 3600);
			}
			if ($results_impresn_desk_2) {
				$results_impresn_S_2 = array_merge($results_impresn_desk_2, $results_impresn_S_2);
			}

			$results_impresn_mob_2 = wp_cache_get('quads_stats_impressions_mobile_group_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_mob_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_id,IFNULL(SUM(stats_impressions),0) as impressions,stats_date as ad_thetime,'mobile' as  ad_device_name  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d GROUP BY stats_date", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_impressions_mobile_group_' . $from_date . '_' . $to_date, $results_impresn_mob_2, 'quick-adsense-reloaded', 3600);
			}
			if ($results_impresn_mob_2) {
				$results_impresn_S_2 = array_merge($results_impresn_mob_2, $results_impresn_S_2);
			}
		} else {
			$results_impresn_S = wp_cache_get('quads_stats_impressions_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_S) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_S = $wpdb->get_results($wpdb->prepare("SELECT date_impression,ad_date FROM 
			`{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ", array($to_date, $from_date, $ad_id)));
				wp_cache_set('quads_stats_impressions_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_impresn_S, 'quick-adsense-reloaded', 3600);
			}
			$results_impresn_desk_1 = wp_cache_get('quads_stats_impressions_desktop_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d AND `ad_id` = %d", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_impressions_desktop_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
			}
			if ($results_impresn_desk_1) {
				$results_impresn_S = array_merge($results_impresn_desk_1, $results_impresn_S);
			}
			$results_impresn_mob_1 = wp_cache_get('quads_stats_impressions_mobile_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_mob_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date))  as ad_date  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d AND `ad_id` = %d", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_impressions_mobile_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
			}
			if ($results_impresn_mob_1) {
				$results_impresn_S = array_merge($results_impresn_mob_1, $results_impresn_S);
			}
			$results_impresn_S_2 = wp_cache_get('quads_stats_impressions_group_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_S_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_S_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, IFNULL(SUM(ad_impressions),0) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_impressions_group_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_impresn_S_2, 'quick-adsense-reloaded', 3600);
			}
			$results_impresn_desk_2 = wp_cache_get('quads_stats_impressions_desktop_group_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_desk_2) {
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_id,IFNULL(SUM(stats_impressions),0) as impressions,stats_date as ad_thetime,'desktop' as  ad_device_name  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND `ad_id` = %d GROUP BY stats_date", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_impressions_desktop_group_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_impresn_desk_2, 'quick-adsense-reloaded', 3600);
			}
			if ($results_impresn_desk_2) {
				$results_impresn_S_2 = array_merge($results_impresn_desk_2, $results_impresn_S_2);
			}

			$results_impresn_mob_2 = wp_cache_get('quads_stats_impressions_mobile_group_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_impresn_mob_2) {
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber,  WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_id,IFNULL(SUM(stats_impressions),0) as impressions,stats_date as ad_thetime,'mobile' as  ad_device_name  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND `ad_id` = %d GROUP BY stats_date", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_impressions_mobile_group_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_impresn_mob_2, 'quick-adsense-reloaded', 3600);
			}
			if($results_impresn_mob_2) { $results_impresn_S_2 = array_merge($results_impresn_mob_2,$results_impresn_S_2);}
			
			}
			
				foreach ($results_impresn_S as $key => $value) {
					foreach ($results_impresn_S_2 as $key2 => $value2) {
						if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
							$value->desk_imprsn = $value2->impressions;
							if(!isset($value->mob_imprsn)){
								$value->mob_imprsn = 0;
							}
						}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
							$value->mob_imprsn = $value2->impressions;
							if(!isset($value->desk_imprsn)){
								$value->desk_imprsn = 0;
							}
						}else{
							$value->mob_imprsn = 0;
							$value->desk_imprsn = 0;
						}
					}
				}
			$array = array_values($results_impresn_S);
			$ad_mob_imprsn = 0;
			$ad_desk_imprsn = 0;
			$ad_imprsn = 0;
			$ad_mob_imprsn_values = array(0,0,0,0,0,0,0) ;
			$ad_desk_imprsn_values = array(0,0,0,0,0,0,0) ;
			$ad_imprsn_values = array(0,0,0,0,0,0,0) ;
			
				$dates_i_chart = array() ;
		for( $i=0; $i<=$loop; $i++ ){
			$dates_i_chart[] = ''.gmdate( 'Y-m-d', mktime(0, 0, 0, $month, ( $date_-$i ) , $year ) );
		}
		sort($dates_i_chart);
		$get_impressions_specific_dates = str_replace('-','/',$dates_i_chart);
			
			foreach ($array as $key => $value) {
				$ad_mob_imprsn += $value->mob_imprsn;
				$ad_desk_imprsn += $value->desk_imprsn;
				$ad_imprsn += $value->date_impression;
				$key_ = array_search($value->ad_date, $dates_i_chart);
				$ad_mob_imprsn_values[$key_] += $value->mob_imprsn;
				$ad_desk_imprsn_values[$key_] += $value->desk_imprsn;
				$ad_imprsn_values[$key_] += $value->date_impression;
			}
		
		$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
		$desk_indi_impr_day_counts = $ad_desk_imprsn_values;	
		$individual_impr_day_counts = $ad_imprsn_values;


		if ($ad_id == 'all') {
			$results_click_S = wp_cache_get('quads_stats_clicks_all_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_click_S) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_S = $wpdb->get_results($wpdb->prepare("SELECT date_click,ad_date FROM 
			`{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ", array($to_date, $from_date)));
				wp_cache_set('quads_stats_clicks_all_' . $from_date . '_' . $to_date, $results_click_S, 'quick-adsense-reloaded', 3600);
			}
			$results_click_S_2 = wp_cache_get('quads_stats_clicks_all_group_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_click_S_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_S_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, IFNULL(SUM(ad_clicks),0) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_clicks_all_group_' . $from_date . '_' . $to_date, $results_click_S_2, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_desk_1 = wp_cache_get('quads_stats_clicks_desktop_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_clicks_desktop_' . $from_date . '_' . $to_date, $results_clicks_desk_1, 'quick-adsense-reloaded', 3600);
			}
			if ($results_clicks_desk_1) {
				$results_click_S = array_merge($results_clicks_desk_1, $results_click_S);
			}

			$results_clicks_mob_1 = wp_cache_get('quads_stats_clicks_mobile_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_clicks_mobile_' . $from_date . '_' . $to_date, $results_clicks_mob_1, 'quick-adsense-reloaded', 3600);
			}
			if ($results_clicks_mob_1) {
				$results_click_S = array_merge($results_clicks_mob_1, $results_click_S);
			}
			$results_clicks_desk_2 = wp_cache_get('quads_stats_clicks_desktop_group_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_id,IFNULL(SUM(stats_clicks),0) as clicks,stats_date as ad_thetime,'desktop' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d GROUP BY stats_date", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_clicks_desktop_group_' . $from_date . '_' . $to_date, $results_clicks_desk_2, 'quick-adsense-reloaded', 3600);
			}
			if ($results_clicks_desk_2) {
				$results_click_S_2 = array_merge($results_clicks_desk_2, $results_click_S_2);
			}
			$results_clicks_mob_2 = wp_cache_get('quads_stats_clicks_mobile_group_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_id,IFNULL(SUM(stats_clicks),0) as clicks,stats_date as ad_thetime,'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d GROUP BY stats_date", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_clicks_mobile_group_' . $from_date . '_' . $to_date, $results_clicks_mob_2, 'quick-adsense-reloaded', 3600);
			}
			if ($results_clicks_mob_2) {
				$results_click_S_2 = array_merge($results_clicks_mob_2, $results_click_S_2);
			}

		} else {
			$results_click_S = wp_cache_get('quads_stats_clicks_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_click_S) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_S = $wpdb->get_results($wpdb->prepare("SELECT date_click,ad_date FROM 
			`{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ", array($to_date, $from_date)));
				wp_cache_set('quads_stats_clicks_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_click_S, 'quick-adsense-reloaded', 3600);
			}
			$results_click_S_2 = wp_cache_get('quads_stats_clicks_group_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_click_S_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_S_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, IFNULL(SUM(ad_clicks),0) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND ad_id =%d GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_clicks_group_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_click_S_2, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_desk_1 = wp_cache_get('quads_stats_clicks_desktop_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id =%d", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_clicks_desktop_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_clicks_desk_1, 'quick-adsense-reloaded', 3600);
			}
			if ($results_clicks_desk_1) {
				$results_click_S = array_merge($results_clicks_desk_1, $results_click_S);
			}
			$results_clicks_mob_1 = wp_cache_get('quads_stats_clicks_mobile_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id =%d", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_clicks_mobile_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_clicks_mob_1, 'quick-adsense-reloaded', 3600);
			}
			if ($results_clicks_mob_1) {
				$results_click_S = array_merge($results_clicks_mob_1, $results_click_S);
			}
			$results_clicks_desk_2 = wp_cache_get('quads_stats_clicks_desktop_group_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_id,IFNULL(SUM(stats_clicks),0) as clicks,stats_date as ad_thetime,'desktop' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id =%d GROUP BY stats_date", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_clicks_desktop_group_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_clicks_desk_2, 'quick-adsense-reloaded', 3600);
			}
			if ($results_clicks_desk_2) {
				$results_click_S_2 = array_merge($results_clicks_desk_2, $results_click_S_2);
			}
			$results_clicks_mob_2 = wp_cache_get('quads_stats_clicks_mobile_group_' . $ad_id . '_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_id,IFNULL(SUM(stats_clicks),0) as clicks,stats_date as ad_thetime,'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id =%d GROUP BY stats_date", array(strtotime($to_date), strtotime($from_date), $ad_id)));
				wp_cache_set('quads_stats_clicks_mobile_group_' . $ad_id . '_' . $from_date . '_' . $to_date, $results_clicks_mob_2, 'quick-adsense-reloaded', 3600);
			}
			if ($results_clicks_mob_2) {
				$results_click_S_2 = array_merge($results_clicks_mob_2, $results_click_S_2);
			}



		}

				foreach ($results_click_S as $key => $value) {
						foreach ($results_click_S_2 as $key2 => $value2) {
							if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
								$value->desk_click = $value2->clicks;
								if(!isset($value->mob_click)){
									$value->mob_click = 0;
								}

							}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
								$value->mob_click = $value2->clicks;
							}else{
								$value->mob_click = 0;
								$value->desk_click = 0;
							}
						}
				}
			
			$array_c = array_values($results_click_S);
		
			$ad_mob_clicks = 0;
			$ad_desk_clicks = 0;
			$ad_clicks = 0;
			$ad_mob_click_values = array(0,0,0,0,0,0,0) ;
			$ad_desk_click_values = array(0,0,0,0,0,0,0) ;
			$ad_click_values = array(0,0,0,0,0,0,0) ;

			foreach ($array_c as $key => $value) {
				$ad_mob_clicks += $value->mob_click;
				$ad_desk_clicks += $value->desk_click;
				$ad_clicks += $value->date_click;
				$key_ = array_search($value->ad_date, $dates_i_chart);
				$ad_mob_click_values[$key_] += $value->mob_click;
				$ad_desk_click_values[$key_] += $value->desk_click;
				$ad_click_values[$key_] += $value->date_click;
			}
			
			$mob_indi_click_day_counts = $ad_mob_click_values;
			$desk_indi_click_day_counts = $ad_desk_click_values;
			$individual_click_day_counts = $ad_click_values;
			
			if($ad_id=="all")
			{
			$results_top5 = wp_cache_get('quads_stats_top5_clicks_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			$results_top5_2 = wp_cache_get('quads_stats_top5_clicks_group_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
			if (false === $results_top5) {
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber,  WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id WHERE `{$wpdb->prefix}quads_single_stats_`.ad_date BETWEEN %s AND %s   GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d", array($to_date, $from_date, 5)));
				wp_cache_set('quads_stats_top5_clicks_' . $from_date . '_' . $to_date, $results_top5, 'quick-adsense-reloaded', 3600);
			}
			if (false === $results_top5_2) {
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber,  WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC", array(strtotime($to_date), strtotime($from_date))));
				wp_cache_set('quads_stats_top5_clicks_group_' . $from_date . '_' . $to_date, $results_top5_2, 'quick-adsense-reloaded', 3600);
			}

			$total_click=[0,0,0,0,0,0,0];
				$total_impression=[0,0,0,0,0,0,0];
				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key] + $value2->impressions;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key] + $value2->impressions;
							}
						}
						$results_top5[$key]->total_click = $total_click[$key];
						$results_top5[$key]->total_impression = $total_impression[$key];

				}

				$array_top5 = array_values($results_top5);
				$array_top_clicks = wp_cache_get('quads_stats_top_clicks_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
				$array_top_imprs = wp_cache_get('quads_stats_top_imprs_' . $from_date . '_' . $to_date, 'quick-adsense-reloaded');
				if (false === $array_top_clicks) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$array_top_clicks = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks ,IFNULL(SUM(click_mob.stats_clicks),0)as mob_clicks , SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date BETWEEN %d AND %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_desk.stats_date BETWEEN %d AND %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC
					LIMIT 5;", array(strtotime($to_date), strtotime($from_date), strtotime($to_date), strtotime($from_date))));
					wp_cache_set('quads_stats_top_clicks_' . $from_date . '_' . $to_date, $array_top_clicks, 'quick-adsense-reloaded', 3600);
				}
				if (false === $array_top_imprs) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$array_top_imprs = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(impr_desk.stats_impressions),0)as desk_imprsn ,IFNULL(SUM(impr_mob.stats_impressions),0)as mob_imprsn , SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date BETWEEN %d AND %d
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date BETWEEN %d AND %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_impression DESC
					LIMIT 5;", array(strtotime($to_date), strtotime($from_date), strtotime($to_date), strtotime($from_date))));
					wp_cache_set('quads_stats_top_imprs_' . $from_date . '_' . $to_date, $array_top_imprs, 'quick-adsense-reloaded', 3600);
				}
				$array_top_imprs_ = $array_top_imprs;
				foreach($array_top_clicks as $key=>$value){
					foreach($array_top_imprs as $key2=>$value2){
					  if($value->ID == $value2->ID){
						$value->desk_imprsn = $value2->desk_imprsn;
						$value->total_impression = $value2->total_impression;
						$value->mob_imprsn = $value2->mob_imprsn;
						unset($array_top_imprs_[$key]);
					  }
					}
				}

				foreach($array_top_imprs_ as $key=>$value){
						$value->desk_clicks = 0;
						$value->total_click = 0;
						$value->mob_click = 0;
				}
				$array_top_clicks =  array_merge($array_top_clicks, $array_top_imprs_);
				$array_top5_ =$array_top_clicks = array_slice($array_top_clicks, 0, 5);
	
		
				if(!empty($array_top_clicks)){
					foreach($array_top5 as $key => $value){
						foreach($array_top_clicks as $key2=>$value2){
							if($value->ID == $value2->ID){

								$array_top5[$key]->total_click = $value->total_click+$value2->total_click;
								$array_top5[$key]->desk_clicks = $value->desk_clicks+$value2->desk_clicks;
								$array_top5[$key]->desk_imprsn = $value->desk_imprsn+$value2->desk_imprsn;
								$array_top5[$key]->mob_clicks = $value->mob_clicks+$value2->mob_clicks;
								$array_top5[$key]->mob_imprsn = $value->mob_imprsn+$value2->mob_imprsn;
								$array_top5[$key]->total_impression = $value->total_impression+$value2->total_impression;
								unset($array_top5_[$key2]);
							}
						}
					}
					$array_top5 =  array_merge($array_top5,$array_top5_);
					$array_top5= array_slice($array_top5, 0, 5);
			 }
			}
		}
		else if( $day == "this_month" ){
			
			$loop = 30 ;
			$month= gmdate("m");
			$date_= gmdate("d");
			$year= gmdate("Y");
			$first_date_ = gmdate('Y-m-d',strtotime('first day of this month'));
			$current_date_month_ = gmdate('Y-m-d');
			if($ad_id=='all') {
				$results_impresn_F = wp_cache_get('quads_stats_impressions_all_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				if (false === $results_impresn_F) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT date_impression,ad_date FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s", array($first_date_, $current_date_month_)));
					wp_cache_set('quads_stats_impressions_all_' . $first_date_ . '_' . $current_date_month_, $results_impresn_F, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_F_2 = wp_cache_get('quads_stats_impressions_all_group_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				if (false === $results_impresn_F_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s  GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", array(strtotime($first_date_), strtotime($current_date_month_))));
					wp_cache_set('quads_stats_impressions_all_group_' . $first_date_ . '_' . $current_date_month_, $results_impresn_F_2, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_desk_1 = wp_cache_get('quads_stats_impressions_desktop_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d", array(strtotime($first_date_), strtotime($current_date_month_))));
					wp_cache_set('quads_stats_impressions_desktop_' . $first_date_ . '_' . $current_date_month_, $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
				}
				if ($results_impresn_desk_1) {
					$results_impresn_F = array_merge($results_impresn_F, $results_impresn_desk_1);
				}
				$results_impresn_mob_1 = wp_cache_get('quads_stats_impressions_mobile_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d", array(strtotime($first_date_), strtotime($current_date_month_))));
					wp_cache_set('quads_stats_impressions_mobile_' . $first_date_ . '_' . $current_date_month_, $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
				}
				if ($results_impresn_mob_1) {
					$results_impresn_F = array_merge($results_impresn_F, $results_impresn_mob_1);
				}
				$results_impresn_desk_2 = wp_cache_get('quads_stats_impressions_desktop_group_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as impressions,stats_date as ad_thetime , 'desktop' as ad_device_name FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d", array(strtotime($first_date_), strtotime($current_date_month_))));
					wp_cache_set('quads_stats_impressions_desktop_group_' . $first_date_ . '_' . $current_date_month_, $results_impresn_desk_2, 'quick-adsense-reloaded', 3600);
				}
				if ($results_impresn_desk_2) {
					$results_impresn_F_2 = array_merge($results_impresn_F_2, $results_impresn_desk_2);
				}
				
				$results_impresn_mob_2 = wp_cache_get('quads_stats_impressions_mobile_group_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as impressions,stats_date as ad_thetime ,'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d", array(strtotime($first_date_), strtotime($current_date_month_))));
					wp_cache_set('quads_stats_impressions_mobile_group_' . $first_date_ . '_' . $current_date_month_, $results_impresn_mob_2, 'quick-adsense-reloaded', 3600);
				}
				if ($results_impresn_mob_2) {
					$results_impresn_F_2 = array_merge($results_impresn_F_2, $results_impresn_mob_2);
				}
			} else {
			$results_impresn_F = wp_cache_get('quads_stats_impressions_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_impresn_F) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT date_impression,ad_date FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ", array($first_date_, $current_date_month_, $ad_id)));
				wp_cache_set('quads_stats_impressions_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_impresn_F, 'quick-adsense-reloaded', 3600);
			}
			$results_impresn_F_2 = wp_cache_get('quads_stats_impressions_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_impresn_F_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_impressions_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_impresn_F_2, 'quick-adsense-reloaded', 3600);
			}
			$results_impresn_desk_1 = wp_cache_get('quads_stats_impressions_desktop_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_impresn_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_impressions_desktop_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
			}

			if ($results_impresn_desk_1) {
				$results_impresn_F = array_merge($results_impresn_F, $results_impresn_desk_1);
			}
			$results_impresn_mob_1 = wp_cache_get('quads_stats_impressions_mobile_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_impresn_mob_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_impressions_mobile_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
			}

			if ($results_impresn_mob_1) {
				$results_impresn_F = array_merge($results_impresn_F, $results_impresn_mob_1);
			}
			$results_impresn_desk_2 = wp_cache_get('quads_stats_impressions_desktop_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_impresn_desk_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as impressions,stats_date as ad_thetime , 'desktop' as ad_device_name FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_impressions_desktop_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_impresn_desk_2, 'quick-adsense-reloaded', 3600);
			}

			if ($results_impresn_desk_2) {
				$results_impresn_F_2 = array_merge($results_impresn_F_2, $results_impresn_desk_2);
			}
			$results_impresn_mob_2 = wp_cache_get('quads_stats_impressions_mobile_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_impresn_mob_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as impressions,stats_date as ad_thetime ,'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_impressions_mobile_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_impresn_mob_2, 'quick-adsense-reloaded', 3600);
			}
			if ($results_impresn_mob_2) {
				$results_impresn_F_2 = array_merge($results_impresn_F_2, $results_impresn_mob_2);
			}


		}
			
			foreach ($results_impresn_F as $key => $value) {
				foreach ($results_impresn_F_2 as $key2 => $value2) {
					if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
						
						$value->desk_imprsn = $value2->impressions;
					}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
						$value->mob_imprsn = $value2->impressions;
					}
				}
		}

			$array = array_values($results_impresn_F);
			$ad_mob_imprsn = $ad_desk_imprsn = $ad_imprsn = 0;
			$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = array() ;
			
				
			$dates_i_chart = array();
		$first_date = gmdate('Y-m-d',strtotime('first day of this month'));
		
		$first__date = $first_date; 
		$last_date_month = gmdate("Y-m-t", strtotime($first__date));
		$begin = new DateTime( $first__date );
		$end   = new DateTime( $last_date_month );
		
		for($i = $begin; $i <= $end; $i->modify('+1 day')){
			$dates_i_chart[] =  $i->format("Y-m-d");
			$ad_mob_imprsn_values[]=0;
			$ad_desk_imprsn_values[]=0;
			$ad_imprsn_values[]=0;
			$ad_mob_click_values[] =0;
			$ad_desk_click_values[] =0;
			$ad_click_values[] =0;
		}

			foreach ($array as $key => $value) {
				$ad_mob_imprsn += $value->mob_imprsn;
				$ad_desk_imprsn += $value->desk_imprsn;
				$ad_imprsn += $value->date_impression;
				$key_ = array_search($value->ad_date, $dates_i_chart);
				$ad_mob_imprsn_values[$key_] += $value->mob_imprsn;
				$ad_desk_imprsn_values[$key_] += $value->desk_imprsn;
				$ad_imprsn_values[$key_] += $value->date_impression;
			}
		
		$mob_indi_impr_day_counts = array_map('quadsRemoveNullElement',$ad_mob_imprsn_values);
		$desk_indi_impr_day_counts = array_map('quadsRemoveNullElement',$ad_desk_imprsn_values);
		$individual_impr_day_counts = array_map('quadsRemoveNullElement',$ad_imprsn_values);

		$_to_slash = $dates_i_chart;
		$get_impressions_specific_dates = str_replace('-','/',$_to_slash);
		if($ad_id=='all') {
			$results_click_F = wp_cache_get('quads_stats_clicks_all_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_click_F) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT date_click,ad_date FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ", array($first_date_, $current_date_month_)));
				wp_cache_set('quads_stats_clicks_all_' . $first_date_ . '_' . $current_date_month_, $results_click_F, 'quick-adsense-reloaded', 3600);
			}
			$results_click_F_2 = wp_cache_get('quads_stats_clicks_all_group_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_click_F_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, IFNULL(SUM(ad_clicks),0) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s  GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", array(strtotime($first_date_), strtotime($current_date_month_))));
				wp_cache_set('quads_stats_clicks_all_group_' . $first_date_ . '_' . $current_date_month_, $results_click_F_2, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_desk_1 = wp_cache_get('quads_stats_clicks_desktop_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d", array(strtotime($first_date_), strtotime($current_date_month_))));
				wp_cache_set('quads_stats_clicks_desktop_' . $first_date_ . '_' . $current_date_month_, $results_clicks_desk_1, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_desk_1){ $results_click_F = array_merge($results_click_F ,$results_clicks_desk_1); }
			$results_clicks_mob_1 = wp_cache_get('quads_stats_clicks_mobile_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d", array(strtotime($first_date_), strtotime($current_date_month_))));
				wp_cache_set('quads_stats_clicks_mobile_' . $first_date_ . '_' . $current_date_month_, $results_clicks_mob_1, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_mob_1){ $results_click_F = array_merge($results_click_F ,$results_clicks_mob_1); }
			$results_clicks_desk_2 = wp_cache_get('quads_stats_clicks_desktop_group_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as clicks,stats_date as ad_thetime , 'desktop' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d", array(strtotime($first_date_), strtotime($current_date_month_))));
				wp_cache_set('quads_stats_clicks_desktop_group_' . $first_date_ . '_' . $current_date_month_, $results_clicks_desk_2, 'quick-adsense-reloaded', 3600);
			}
            if($results_clicks_desk_2){ $results_click_F = array_merge($results_click_F ,$results_clicks_desk_2); }
			$results_clicks_mob_2 = wp_cache_get('quads_stats_clicks_mobile_group_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as clicks,stats_date as ad_thetime , 'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d", array(strtotime($first_date_), strtotime($current_date_month_))));
				wp_cache_set('quads_stats_clicks_mobile_group_' . $first_date_ . '_' . $current_date_month_, $results_clicks_mob_2, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_mob_2){ $results_click_F = array_merge($results_click_F ,$results_clicks_mob_2); }
		}
		else
		{
			$results_click_F = wp_cache_get('quads_stats_clicks_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_click_F) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT date_click,ad_date FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ", array($first_date_, $current_date_month_, $ad_id)));
				wp_cache_set('quads_stats_clicks_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_click_F, 'quick-adsense-reloaded', 3600);
			}
			$results_click_F_2 = wp_cache_get('quads_stats_clicks_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_click_F_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, IFNULL(SUM(ad_clicks),0) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_clicks_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_click_F_2, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_desk_1 = wp_cache_get('quads_stats_clicks_desktop_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d AND `ad_id` = %d", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_clicks_desktop_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_clicks_desk_1, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_desk_1){ $results_click_F = array_merge($results_click_F ,$results_clicks_desk_1); }
			$results_clicks_mob_1 = wp_cache_get('quads_stats_clicks_mobile_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d AND `ad_id` = %d", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_clicks_mobile_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_clicks_mob_1, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_mob_1){ $results_click_F = array_merge($results_click_F ,$results_clicks_mob_1); }
			$results_clicks_desk_2 = wp_cache_get('quads_stats_clicks_desktop_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as clicks,stats_date as ad_thetime , 'desktop' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d AND `ad_id` = %d", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_clicks_desktop_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_clicks_desk_2, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_desk_2){ $results_click_F = array_merge($results_click_F ,$results_clicks_desk_2); }
			$results_clicks_mob_2 = wp_cache_get('quads_stats_clicks_mobile_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as clicks,stats_date as ad_thetime , 'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d AND `ad_id` = %d", array(strtotime($first_date_), strtotime($current_date_month_), $ad_id)));
				wp_cache_set('quads_stats_clicks_mobile_group_' . $ad_id . '_' . $first_date_ . '_' . $current_date_month_, $results_clicks_mob_2, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_mob_2){ $results_click_F = array_merge($results_click_F ,$results_clicks_mob_2); }
		}
			foreach ($results_click_F as $key => $value) {
					foreach ($results_click_F_2 as $key2 => $value2) {
						if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
							$value->desk_click = $value2->clicks;
						}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
							$value->mob_click = $value2->clicks;
						}
					}
			}

		
		
		$array_c = array_values($results_click_F);
		$ad_mob_clicks = $ad_desk_clicks = $ad_clicks = 0;
		

		foreach ($array_c as $key => $value) {
			$ad_mob_clicks += $value->mob_click;
			$ad_desk_clicks += $value->desk_click;
			$ad_clicks += $value->date_click;
			$key_ = array_search($value->ad_date, $dates_i_chart);
			$ad_mob_click_values[$key_] += $value->mob_click;
			$ad_desk_click_values[$key_] += $value->desk_click;
			$ad_click_values[$key_] += $value->date_click;
		}
		
		$mob_indi_click_day_counts = array_map('quadsRemoveNullElement',$ad_mob_click_values);
		$desk_indi_click_day_counts = array_map('quadsRemoveNullElement',$ad_desk_click_values);
		$individual_click_day_counts = array_map('quadsRemoveNullElement',$ad_click_values);
		if($ad_id=="all")
			{
				$results_top5 = wp_cache_get('quads_stats_top5_ads_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				if (false === $results_top5) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id WHERE `{$wpdb->prefix}quads_single_stats_`.ad_date BETWEEN %s AND %s  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($first_date_,$current_date_month_,5)));
					wp_cache_set('quads_stats_top5_ads_' . $first_date_ . '_' . $current_date_month_, $results_top5, 'quick-adsense-reloaded', 3600);
				}
				$results_top5_2 = wp_cache_get('quads_stats_top5_ads_group_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				if (false === $results_top5_2) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, IFNULL(SUM(ad_impressions),0) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",array(strtotime($first_date_),strtotime($current_date_month_))));
					wp_cache_set('quads_stats_top5_ads_group_' . $first_date_ . '_' . $current_date_month_, $results_top5_2, 'quick-adsense-reloaded', 3600);
				}
				$total_click=[0,0,0,0,0];
				$total_impression=[0,0,0,0,0];
				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key] + $value2->impressions;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key] + $value2->impressions;
							}
						}
						$results_top5[$key]->total_click = $total_click[$key];
						$results_top5[$key]->total_impression = $total_impression[$key];
				}

				$array_top5 = array_values($results_top5);
				$array_top_clicks = wp_cache_get('quads_stats_top_ads_clicks_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				$array_top_imprs  = wp_cache_get('quads_stats_top_ads_impressions_' . $first_date_ . '_' . $current_date_month_, 'quick-adsense-reloaded');
				if (false === $array_top_clicks) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$array_top_clicks = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks ,IFNULL(SUM(click_mob.stats_clicks),0)as mob_clicks , SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date BETWEEN %d AND %d
	                LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_desk.stats_date BETWEEN %d AND %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC
				LIMIT 5;",array(strtotime($first_date_,),strtotime($current_date_month_),strtotime($first_date_,),strtotime($current_date_month_))));
					wp_cache_set('quads_stats_top_ads_clicks_' . $first_date_ . '_' . $current_date_month_, $array_top_clicks, 'quick-adsense-reloaded', 3600);
				}

				if (false === $array_top_imprs) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$array_top_imprs = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(impr_desk.stats_impressions),0)as desk_imprsn ,IFNULL(SUM(impr_mob.stats_impressions),0)as mob_imprsn , SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date BETWEEN %d AND %d
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date BETWEEN %d AND %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_impression DESC
					LIMIT 5;",array(strtotime($first_date_,),strtotime($current_date_month_),strtotime($first_date_,),strtotime($current_date_month_))));
					wp_cache_set('quads_stats_top_ads_impressions_' . $first_date_ . '_' . $current_date_month_, $array_top_imprs, 'quick-adsense-reloaded', 3600);
				}
				$array_top_imprs_ = $array_top_imprs;

				foreach($array_top_clicks as $key=>$value){
					foreach($array_top_imprs as $key2=>$value2){
					  if($value->ID == $value2->ID){
						$value->desk_imprsn = $value2->desk_imprsn;
						$value->total_impression = $value2->total_impression;
						$value->mob_imprsn = $value2->mob_imprsn;
						unset($array_top_imprs_[$key]);
					  }
					}
				}

				foreach($array_top_imprs_ as $key=>$value){
						$value->desk_clicks = 0;
						$value->total_click = 0;
						$value->mob_click = 0;
				}
				$array_top_clicks =  array_merge($array_top_clicks, $array_top_imprs_);
				$array_top5_ =$array_top_clicks = array_slice($array_top_clicks, 0, 5);
	
		
				if(!empty($array_top_clicks)){
					foreach($array_top5 as $key => $value){
						foreach($array_top_clicks as $key2=>$value2){
							if($value->ID == $value2->ID){

								$array_top5[$key]->total_click = $value->total_click+$value2->total_click;
								$array_top5[$key]->desk_clicks = $value->desk_clicks+$value2->desk_clicks;
								$array_top5[$key]->desk_imprsn = $value->desk_imprsn+$value2->desk_imprsn;
								$array_top5[$key]->mob_clicks = $value->mob_clicks+$value2->mob_clicks;
								$array_top5[$key]->mob_imprsn = $value->mob_imprsn+$value2->mob_imprsn;
								$array_top5[$key]->total_impression = $value->total_impression+$value2->total_impression;
								unset($array_top5_[$key2]);
							}
						}
					}
					$array_top5 =  array_merge($array_top5,$array_top5_);
					$array_top5= array_slice($array_top5, 0, 5);
			 }		
			}
	}
		else if( $day == "last_month" ){
			
			$loop = 30 ;
			$year = gmdate("Y");
			if($ad_id=='all') {
				$results_impresn_F = wp_cache_get('quads_stats_impressions_all_last_month_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_F) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT date_impression,ad_date from `{$wpdb->prefix}quads_single_stats_` WHERE month(ad_date)=month(now())-1 AND year(ad_date) = %s ",array($year)));
					wp_cache_set('quads_stats_impressions_all_last_month_' . $year, $results_impresn_F, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_F_2 = wp_cache_get('quads_stats_impressions_all_last_month_group_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_F_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, IFNULL(SUM(ad_impressions),0) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE MONTH(FROM_UNIXTIME(ad_thetime)) = month(now())-1 AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime;",array($year)));
					wp_cache_set('quads_stats_impressions_all_last_month_group_' . $year, $results_impresn_F_2, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_desk_1 = wp_cache_get('quads_stats_impressions_desktop_last_month_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE   MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
					wp_cache_set('quads_stats_impressions_desktop_last_month_' . $year, $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_desk_1){ $results_impresn_F = array_merge($results_impresn_F,$results_impresn_desk_1 ); }
				$results_impresn_mob_1 = wp_cache_get('quads_stats_impressions_mobile_last_month_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
					wp_cache_set('quads_stats_impressions_mobile_last_month_' . $year, $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_mob_1){ $results_impresn_F = array_merge($results_impresn_F,$results_impresn_mob_1 ); }
				$results_impresn_desk_2 = wp_cache_get('quads_stats_impressions_desktop_last_month_group_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as impressions,stats_date as ad_thetime , 'desktop' as ad_device_name  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE   MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
					wp_cache_set('quads_stats_impressions_desktop_last_month_group_' . $year, $results_impresn_desk_2, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_desk_2){ $results_impresn_F = array_merge($results_impresn_F,$results_impresn_desk_2 ); }
				$results_impresn_mob_2 = wp_cache_get('quads_stats_impressions_mobile_last_month_group_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as impressions,stats_date as ad_thetime , 'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
					wp_cache_set('quads_stats_impressions_mobile_last_month_group_' . $year, $results_impresn_mob_2, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_mob_2){ $results_impresn_F = array_merge($results_impresn_F,$results_impresn_mob_2 ); }
			}
			else
			{
				$results_impresn_F = wp_cache_get('quads_stats_impressions_' . $ad_id . '_last_month_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_F) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT date_impression,ad_date from `{$wpdb->prefix}quads_single_stats_` WHERE month(ad_date)=month(now())-1 AND year(ad_date) = %s AND `ad_id`=%d ",array($year,$ad_id)));
					wp_cache_set('quads_stats_impressions_' . $ad_id . '_last_month_' . $year, $results_impresn_F, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_F_2 = wp_cache_get('quads_stats_impressions_group_' . $ad_id . '_last_month_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_F_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, IFNULL(SUM(ad_impressions),0) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE MONTH(FROM_UNIXTIME(ad_thetime)) = month(now())-1 AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime;",array($year,$ad_id)));
					wp_cache_set('quads_stats_impressions_group_' . $ad_id . '_last_month_' . $year, $results_impresn_F_2, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_desk_1 = wp_cache_get('quads_stats_impressions_desktop_last_month_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE   MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND `ad_id`=%d",array($year,$ad_id)));
					wp_cache_set('quads_stats_impressions_desktop_last_month_' . $ad_id . '_' . $year, $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_desk_1){ $results_impresn_F = array_merge($results_impresn_F,$results_impresn_desk_1 ); }
				$results_impresn_mob_1 = wp_cache_get('quads_stats_impressions_mobile_last_month_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND `ad_id`=%d",array($year,$ad_id)));
					wp_cache_set('quads_stats_impressions_mobile_last_month_' . $ad_id . '_' . $year, $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_mob_1){ $results_impresn_F = array_merge($results_impresn_F,$results_impresn_mob_1 ); }
				$results_impresn_desk_2 = wp_cache_get('quads_stats_impressions_desktop_last_month_group_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as impressions,stats_date as ad_thetime , 'desktop' as ad_device_name  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE   MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND `ad_id`=%d",array($year,$ad_id)));
					wp_cache_set('quads_stats_impressions_desktop_last_month_group_' . $ad_id . '_' . $year, $results_impresn_desk_2, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_desk_2){ $results_impresn_F = array_merge($results_impresn_F,$results_impresn_desk_2 ); }
				$results_impresn_mob_2 = wp_cache_get('quads_stats_impressions_mobile_last_month_group_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as impressions,stats_date as ad_thetime , 'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND `ad_id`=%d",array($year,$ad_id)));
					wp_cache_set('quads_stats_impressions_mobile_last_month_group_' . $ad_id . '_' . $year, $results_impresn_mob_2, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_mob_2){ $results_impresn_F = array_merge($results_impresn_F,$results_impresn_mob_2 ); }

			}
			
			foreach ($results_impresn_F as $key => $value) {
				foreach ($results_impresn_F_2 as $key2 => $value2) {
					if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
						$value->desk_imprsn = $value2->impressions;
					}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
						$value->mob_imprsn = $value2->impressions;
					}
				}
		}

			$array = array_values($results_impresn_F);
			$ad_mob_imprsn = $ad_desk_imprsn = $ad_imprsn = 0;
			$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = array() ;
			$ad_mob_click_values = $ad_desk_click_values = $ad_click_values = array();
			
			$dates_i_chart= array();
			$year = gmdate("Y",strtotime("-1 month"));
			$month = gmdate("m",strtotime("-1 month"));
			
			for($d=1; $d<=31; $d++){
				$time=mktime(12, 0, 0, $month, $d, $year);          
				if (gmdate('m', $time)==$month)       
					$dates_i_chart[] =gmdate('Y-m-d', $time);
					$ad_mob_imprsn_values[]=0;
					$ad_desk_imprsn_values[]=0;
					$ad_imprsn_values[]=0;
					$ad_mob_click_values[]=0;
					$ad_desk_click_values[]=0;
					$ad_click_values[]=0;
			}

			foreach ($array as $key => $value) {
				$ad_mob_imprsn += $value->mob_imprsn;
				$ad_desk_imprsn += $value->desk_imprsn;
				$ad_imprsn += $value->date_impression;
				$key_ = array_search($value->ad_date, $dates_i_chart);
				$ad_mob_imprsn_values[$key_] += $value->mob_imprsn;
				$ad_desk_imprsn_values[$key_] += $value->desk_imprsn;
				$ad_imprsn_values[$key_] += $value->date_impression;
			}
			$mob_indi_impr_day_counts = array_map('quadsRemoveNullElement',$ad_mob_imprsn_values);
			$desk_indi_impr_day_counts = array_map('quadsRemoveNullElement',$ad_desk_imprsn_values);
			$individual_impr_day_counts = array_map('quadsRemoveNullElement',$ad_imprsn_values);

	

		$_to_slash = $dates_i_chart;
		$get_impressions_specific_dates = str_replace('-','/',$_to_slash);
		if($ad_id=='all') {
			 $results_click_F = wp_cache_get('quads_stats_clicks_all_last_month_' . $year, 'quick-adsense-reloaded');
			 if (false === $results_click_F) {
				 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				 $results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT date_click,ad_date from `{$wpdb->prefix}quads_single_stats_` WHERE month(ad_date)=month(now())-1 AND year(ad_date) = %s ",array($year)));
				 wp_cache_set('quads_stats_clicks_all_last_month_' . $year, $results_click_F, 'quick-adsense-reloaded', 3600);
			 }
			$results_clicks_desk_1 = wp_cache_get('quads_stats_clicks_desktop_last_month_' . $year, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE   MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
				wp_cache_set('quads_stats_clicks_desktop_last_month_' . $year, $results_clicks_desk_1, 'quick-adsense-reloaded', 3600);
			}
			 
		 if($results_clicks_desk_1 ) { $results_click_F  = array_merge($results_click_F ,$results_clicks_desk_1); }
		 $results_clicks_mob_1 = wp_cache_get('quads_stats_clicks_mobile_last_month_' . $year, 'quick-adsense-reloaded');
		 if (false === $results_clicks_mob_1) {
			 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			 $results_clicks_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
			 wp_cache_set('quads_stats_clicks_mobile_last_month_' . $year, $results_clicks_mob_1, 'quick-adsense-reloaded', 3600);
		 }
		 if($results_clicks_mob_1 ) { $results_click_F  = array_merge($results_click_F ,$results_clicks_mob_1); }
		}
		else
		{
			$results_click_F = wp_cache_get('quads_stats_clicks_' . $ad_id . '_last_month_' . $year, 'quick-adsense-reloaded');
			if (false === $results_click_F) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT date_click,ad_date from `{$wpdb->prefix}quads_single_stats_` WHERE month(ad_date)=month(now())-1 AND year(ad_date) = %s AND `ad_id`=%d ",array($year,$ad_id)));
				wp_cache_set('quads_stats_clicks_' . $ad_id . '_last_month_' . $year, $results_click_F, 'quick-adsense-reloaded', 3600);
			}
			$results_click_F_2 = wp_cache_get('quads_stats_clicks_group_' . $ad_id . '_last_month_' . $year, 'quick-adsense-reloaded');
			if (false === $results_click_F_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, IFNULL(SUM(ad_clicks),0) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE MONTH(FROM_UNIXTIME(ad_thetime)) = month(now())-1 AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime;",array($year,$ad_id)));
				wp_cache_set('quads_stats_clicks_group_' . $ad_id . '_last_month_' . $year, $results_click_F_2, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_desk_1 = wp_cache_get('quads_stats_clicks_desktop_last_month_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE   MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND `ad_id`=%d",array($year,$ad_id)));
				wp_cache_set('quads_stats_clicks_desktop_last_month_' . $ad_id . '_' . $year, $results_clicks_desk_1, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_desk_1 ) { $results_click_F  = array_merge($results_click_F ,$results_clicks_desk_1); }
			$results_clicks_mob_1 = wp_cache_get('quads_stats_clicks_mobile_last_month_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND `ad_id`=%d",array($year,$ad_id)));
				wp_cache_set('quads_stats_clicks_mobile_last_month_' . $ad_id . '_' . $year, $results_clicks_mob_1, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_mob_1 ) { $results_click_F  = array_merge($results_click_F ,$results_clicks_mob_1); }
			$results_clicks_desk_2 = wp_cache_get('quads_stats_clicks_desktop_last_month_group_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as clicks,stats_date as ad_thetime , 'desktop' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE   MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND `ad_id`=%d",array($year,$ad_id)));
				wp_cache_set('quads_stats_clicks_desktop_last_month_group_' . $ad_id . '_' . $year, $results_clicks_desk_2, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_desk_2 ) { $results_click_F_2  = array_merge($results_click_F ,$results_click_F_2); }
			$results_clicks_mob_2 = wp_cache_get('quads_stats_clicks_mobile_last_month_group_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
			if (false === $results_clicks_mob_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as clicks,stats_date as ad_thetime , 'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND `ad_id`=%d",array($year,$ad_id)));
				wp_cache_set('quads_stats_clicks_mobile_last_month_group_' . $ad_id . '_' . $year, $results_clicks_mob_2, 'quick-adsense-reloaded', 3600);
			}
			if($results_clicks_mob_2 ) { $results_click_F_2  = array_merge($results_click_F ,$results_click_F_2); }
		
		foreach ($results_click_F as $key => $value) {
				foreach ($results_click_F_2 as $key2 => $value2) {
					if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
						$value->desk_click = $value2->clicks;
					}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
						$value->mob_click = $value2->clicks;
					}
				}
		}

		}
		
		$array_c = array_values($results_click_F);
		$ad_mob_clicks = $ad_desk_clicks = $ad_clicks = 0;
		

		foreach ($array_c as $key => $value) {
			$ad_mob_clicks += $value->mob_click;
			$ad_desk_clicks += $value->desk_click;
			$ad_clicks += $value->date_click;
			$key_ = array_search($value->ad_date, $dates_i_chart);
			$ad_mob_click_values[$key_] += $value->mob_click;
			$ad_desk_click_values[$key_] += $value->desk_click;
			$ad_click_values[$key_] += $value->date_click;
		}

		$mob_indi_click_day_counts = array_map('quadsRemoveNullElement',$ad_mob_click_values);
		$desk_indi_click_day_counts = array_map('quadsRemoveNullElement',$ad_desk_click_values);
		$individual_click_day_counts = array_map('quadsRemoveNullElement',$ad_click_values);
		
		if($ad_id=="all")
			{
				$results_top5 = wp_cache_get('quads_stats_top5_last_month_group_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
				if (false === $results_top5) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id WHERE month(`{$wpdb->prefix}quads_single_stats_`.ad_date)=month(now())-1 AND year(`{$wpdb->prefix}quads_single_stats_`.ad_date) = %d  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($year,5)));
					wp_cache_set('quads_stats_top5_last_month_group_' . $ad_id . '_' . $year, $results_top5, 'quick-adsense-reloaded', 3600);
				}
				$results_top5_2 = wp_cache_get('quads_stats_top5_last_month_stats_' . $ad_id . '_' . $year, 'quick-adsense-reloaded');
				if (false === $results_top5_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, IFNULL(SUM(ad_impressions),0) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE MONTH(FROM_UNIXTIME(ad_thetime)) = month(now())-1 AND YEAR(FROM_UNIXTIME(ad_thetime)) = %d GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime;",array($year)));
					wp_cache_set('quads_stats_top5_last_month_stats_' . $ad_id . '_' . $year, $results_top5_2, 'quick-adsense-reloaded', 3600);
				}
				$total_click=[0,0,0,0,0];
				$total_impression=[0,0,0,0,0];
				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key] + $value2->impressions;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key] + $value2->impressions;
							}
						}
						$results_top5[$key]->total_click = $total_click[$key];
						$results_top5[$key]->total_impression = $total_impression[$key];
				}
				$array_top5 = array_values($results_top5);
				$array_top_clicks = wp_cache_get('quads_stats_top_clicks_last_month_' . $year, 'quick-adsense-reloaded');
				if (false === $array_top_clicks) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$array_top_clicks = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks ,IFNULL(SUM(click_mob.stats_clicks),0)as mob_clicks , SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND  MONTH(FROM_UNIXTIME(click_desk.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(click_desk.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND  MONTH(FROM_UNIXTIME(click_mob.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(click_mob.stats_date)) = %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC
					LIMIT 5;",array($year,$year)));
					wp_cache_set('quads_stats_top_clicks_last_month_' . $year, $array_top_clicks, 'quick-adsense-reloaded', 3600);
				}
				
				$array_top_imprs = wp_cache_get('quads_stats_top_impressions_last_month_' . $year, 'quick-adsense-reloaded');
				if (false === $array_top_imprs) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$array_top_imprs = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(impr_desk.stats_impressions),0)as desk_imprsn ,IFNULL(SUM(impr_mob.stats_impressions),0)as mob_imprsn , SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND  MONTH(FROM_UNIXTIME(impr_mob.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(impr_mob.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND  MONTH(FROM_UNIXTIME(impr_desk.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(impr_desk.stats_date)) = %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_impression DESC
					LIMIT 5;",array($year,$year)));
					wp_cache_set('quads_stats_top_impressions_last_month_' . $year, $array_top_imprs, 'quick-adsense-reloaded', 3600);
				}

				$array_top_imprs_=$array_top_imprs;
				
				foreach($array_top_clicks as $key=>$value){
					foreach($array_top_imprs as $key2=>$value2){
					  if($value->ID == $value2->ID){
						$value->desk_imprsn = $value2->desk_imprsn;
						$value->total_impression = $value2->total_impression;
						$value->mob_imprsn = $value2->mob_imprsn;
						unset($array_top_imprs_[$key]);
					  }
					}
				}

				foreach($array_top_imprs_ as $key=>$value){
						$value->desk_clicks = 0;
						$value->total_click = 0;
						$value->mob_click = 0;
				}
				$array_top_clicks =  array_merge($array_top_clicks, $array_top_imprs_);
				$array_top5_ =$array_top_clicks = array_slice($array_top_clicks, 0, 5);
	
		
				if(!empty($array_top_clicks)){
					foreach($array_top5 as $key => $value){
						foreach($array_top_clicks as $key2=>$value2){
							if($value->ID == $value2->ID){

								$array_top5[$key]->total_click = $value->total_click+$value2->total_click;
								$array_top5[$key]->desk_clicks = $value->desk_clicks+$value2->desk_clicks;
								$array_top5[$key]->desk_imprsn = $value->desk_imprsn+$value2->desk_imprsn;
								$array_top5[$key]->mob_clicks = $value->mob_clicks+$value2->mob_clicks;
								$array_top5[$key]->mob_imprsn = $value->mob_imprsn+$value2->mob_imprsn;
								$array_top5[$key]->total_impression = $value->total_impression+$value2->total_impression;
								unset($array_top5_[$key2]);
							}
						}
					}
					$array_top5 =  array_merge($array_top5,$array_top5_);
					$array_top5 = array_slice($array_top5, 0, 5);
			 }
			}
	}
		else if( $day == "all_time" ){
			
			$loop = 30 ;
			$month = gmdate("m");
			$date_ = gmdate("d");
			$year = gmdate("Y");
			$first_date_ = gmdate('Y-m-d',strtotime('first day of this month'));
			$current_date_month_ = gmdate('Y-m-d');
			if($ad_id=="all"){
				$results_impresn_F = wp_cache_get('quads_stats_impressions_all_time_all_' , 'quick-adsense-reloaded');
				if (false === $results_impresn_F) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare, WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT IFNULL(sum(date_impression),0) as date_impression, ad_year FROM `{$wpdb->prefix}quads_single_stats_`  group by ad_year ;"));
					wp_cache_set('quads_stats_impressions_all_time_all_' , $results_impresn_F, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_desk_1 = wp_cache_get('quads_stats_impressions_all_time_desktop_' , 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_1) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare, WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as date_impression, stats_year as ad_year  FROM `{$wpdb->prefix}quads_impressions_desktop` GROUP BY stats_year"));
					wp_cache_set('quads_stats_impressions_all_time_desktop_' , $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_desk_1) { 
					foreach($results_impresn_desk_1 as $key => $value) {
						foreach($results_impresn_F as $key2=>$value2){
							if($value->ad_year == $value2->ad_year){
								$results_impresn_F[$key2]->date_impression = $value2->date_impression+$value->date_impression;
								unset($results_impresn_desk_1[$key]);
							}
						}
					}
					$results_impresn_F = array_merge($results_impresn_F,$results_impresn_desk_1);
				}
				$results_impresn_mob_1 = wp_cache_get('quads_stats_impressions_all_time_mobile_' , 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_1) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare, WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as date_impression, stats_year as ad_year FROM `{$wpdb->prefix}quads_impressions_mobile`  GROUP BY stats_year"));
					wp_cache_set('quads_stats_impressions_all_time_mobile_' , $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
				}
				
				if($results_impresn_mob_1) { 
					foreach($results_impresn_mob_1 as $key => $value) {
						foreach($results_impresn_F as $key2=>$value2){
							if($value->ad_year == $value2->ad_year){
								$results_impresn_F[$key2]->date_impression = $value2->date_impression+$value->date_impression;
								unset($results_impresn_mob_1[$key]);
							}
						}
					}
					$results_impresn_F = array_merge($results_impresn_F,$results_impresn_mob_1);
				}
			}
			else
			{
			 $results_impresn_F = wp_cache_get('quads_stats_impressions_all_time_' . $ad_id , 'quick-adsense-reloaded');
			 if (false === $results_impresn_F) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT IFNULL(sum(date_impression),0) as date_impression, ad_year FROM `{$wpdb->prefix}quads_single_stats_` where ad_id = %d group by ad_year ;",$ad_id));
					wp_cache_set('quads_stats_impressions_all_time_' . $ad_id , $results_impresn_F, 'quick-adsense-reloaded', 3600);
			 }
			 $results_impresn_F_2 = wp_cache_get('quads_stats_impressions_all_time_group_' . $ad_id , 'quick-adsense-reloaded');
			 if (false === $results_impresn_F_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, IFNULL(SUM(ad_impressions),0) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE ad_id = %d GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", $ad_id));
					wp_cache_set('quads_stats_impressions_all_time_group_' . $ad_id , $results_impresn_F_2, 'quick-adsense-reloaded', 3600);
			 }
			 
			 $results_impresn_desk_1 = wp_cache_get('quads_stats_impressions_all_time_desktop_' . $ad_id , 'quick-adsense-reloaded');
			 if (false === $results_impresn_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as date_impression, stats_year as ad_year  FROM `{$wpdb->prefix}quads_impressions_desktop` where ad_id = %d GROUP BY stats_year",$ad_id));
				wp_cache_set('quads_stats_impressions_all_time_desktop_' . $ad_id , $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
			 }
			 if($results_impresn_desk_1) { 
				 foreach($results_impresn_desk_1 as $key => $value) {
					 foreach($results_impresn_F as $key2=>$value2){
						 if($value->ad_year == $value2->ad_year){
							 $results_impresn_F[$key2]->date_impression = $value2->date_impression+$value->date_impression;
							 unset($results_impresn_desk_1[$key]);
						 }
					 }
				 }
				 $results_impresn_F = array_merge($results_impresn_F,$results_impresn_desk_1);
			 }
			 $results_impresn_mob_1 = wp_cache_get('quads_stats_impressions_all_time_mobile_' . $ad_id , 'quick-adsense-reloaded');
			 if (false === $results_impresn_mob_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as date_impression, stats_year as ad_year FROM `{$wpdb->prefix}quads_impressions_mobile`  where ad_id = %d GROUP BY stats_year",$ad_id));
				wp_cache_set('quads_stats_impressions_all_time_mobile_' . $ad_id , $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
			 }
			
			 if($results_impresn_mob_1) { 
				 foreach($results_impresn_mob_1 as $key => $value) {
					 foreach($results_impresn_F as $key2=>$value2){
						 if($value->ad_year == $value2->ad_year){
							 $results_impresn_F[$key2]->date_impression = $value2->date_impression+$value->date_impression;
							 unset($results_impresn_mob_1[$key]);
						 }
					 }
				 }
				 $results_impresn_F = array_merge($results_impresn_F,$results_impresn_mob_1);
			 }

			  $results_impresn_desk_2 = wp_cache_get('quads_stats_impressions_all_time_desktop_group_' . $ad_id , 'quick-adsense-reloaded');
			 if (false === $results_impresn_desk_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as impression, stats_date as ad_thetime , 'desktop' as ad_device_name  FROM `{$wpdb->prefix}quads_impressions_desktop` where ad_id = %d GROUP BY stats_year",$ad_id));
				wp_cache_set('quads_stats_impressions_all_time_desktop_group_' . $ad_id , $results_impresn_desk_2, 'quick-adsense-reloaded', 3600);
			 }
			 if($results_impresn_desk_2) { 
				 foreach($results_impresn_desk_2 as $key => $value) {
					 foreach($results_impresn_F as $key2=>$value2){
						 if($value->ad_year == $value2->ad_year){
							 $results_impresn_F[$key2]->date_impression = $value2->date_impression+$value->date_impression;
							 unset($results_impresn_desk_2[$key]);
						 }
					 }
				 }
				 $results_impresn_F_2 = array_merge($results_impresn_F_2,$results_impresn_desk_2);
			 }
			 $results_impresn_mob_2 = wp_cache_get('quads_stats_impressions_all_time_mobile_group_' . $ad_id , 'quick-adsense-reloaded');
			 if (false === $results_impresn_mob_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as impression, stats_date as ad_thetime , 'mobile' as ad_device_name  FROM `{$wpdb->prefix}quads_impressions_mobile`  where ad_id = %d GROUP BY stats_year",$ad_id));
				wp_cache_set('quads_stats_impressions_all_time_mobile_group_' . $ad_id , $results_impresn_mob_2, 'quick-adsense-reloaded', 3600);
			 }
			
			 if($results_impresn_mob_2) { 
				 foreach($results_impresn_mob_2 as $key => $value) {
					 foreach($results_impresn_F as $key2=>$value2){
						 if($value->ad_year == $value2->ad_year){
							 $results_impresn_F[$key2]->date_impression = $value2->date_impression+$value->date_impression;
							 unset($results_impresn_mob_2[$key]);
						 }
					 }
				 }
				 $results_impresn_F_2 = array_merge($results_impresn_F_2,$results_impresn_mob_2);
			 }

			 
			 foreach ( $results_impresn_F as $key => $value ) {
			 		foreach ( $results_impresn_F_2 as $key2 => $value2) {
			 			if( gmdate("Y", $value2->ad_thetime ) == $value->ad_year && $value2->ad_device_name == 'desktop' ) {
			 				$value->desk_imprsn += $value2->impressions;
			 			}elseif( gmdate("Y", $value2->ad_thetime) == $value->ad_year && $value2->ad_device_name == 'mobile'){
			 				$value->mob_imprsn += $value2->impressions;
			 			}
			 		}
			 }
			 

			}
			$array = array_values($results_impresn_F);
			$ad_mob_imprsn = $ad_desk_imprsn = $ad_imprsn = 0;
			$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = array() ;
			$ad_mob_imprsn_values_ = $ad_desk_imprsn_values_ = $ad_imprsn_values_ = array() ;
			foreach ($array as $key => $value) {
				$ad_mob_imprsn += $value->mob_imprsn;
				$ad_desk_imprsn += $value->desk_imprsn;
				$ad_imprsn += $value->date_impression;
				$ad_mob_imprsn_values[] = $value->ad_year;
				$ad_desk_imprsn_values[] = $value->ad_year;
				$ad_imprsn_values[] = $value->ad_year;
				$ad_mob_imprsn_values_[] = $value->mob_imprsn;
				$ad_desk_imprsn_values_[] = $value->desk_imprsn;
				$ad_imprsn_values_[] = $value->date_impression;
			}
			$mob_indi_impr_day_counts = array_map('quadsRemoveNullElement',$ad_mob_imprsn_values_);
			$desk_indi_impr_day_counts = array_map('quadsRemoveNullElement',$ad_desk_imprsn_values_);
			$individual_impr_day_counts = array_map('quadsRemoveNullElement',$ad_imprsn_values_);
			// individual_impr_day_counts
			$get_mob_impr_specific_dates = $ad_mob_imprsn_values;
			$get_desk_impr_specific_dates = $ad_desk_imprsn_values;
			$get_impressions_specific_dates = $ad_imprsn_values;

			if($ad_id=="all"){
				$results_click_F = wp_cache_get('quads_stats_clicks_all_time_all_' , 'quick-adsense-reloaded');
				if (false === $results_click_F) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare, WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT IFNULL(sum(date_click),0) as date_click, ad_year FROM `{$wpdb->prefix}quads_single_stats_`  group by ad_year; "));
					wp_cache_set('quads_stats_clicks_all_time_all_' , $results_click_F, 'quick-adsense-reloaded', 3600);
				}
				$results_click_desk_1 = wp_cache_get('quads_stats_clicks_all_time_desktop_' , 'quick-adsense-reloaded');
				if (false === $results_click_desk_1) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare, WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_click_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as date_click, stats_year as ad_year  FROM `{$wpdb->prefix}quads_impressions_desktop` GROUP BY stats_year"));
					wp_cache_set('quads_stats_clicks_all_time_desktop_' , $results_click_desk_1, 'quick-adsense-reloaded', 3600);
				}	
				if($results_click_desk_1) { 
					foreach($results_click_desk_1 as $key => $value) {
						foreach($results_click_F as $key2=>$value2){
							if($value->ad_year == $value2->ad_year){
								$results_click_F[$key2]->date_click = $value2->date_click+$value->date_click;
								unset($results_click_desk_1[$key]);
							}
						}
					}
					$results_click_F = array_merge($results_click_F,$results_click_desk_1);
				}
				$results_click_mob_1 = wp_cache_get('quads_stats_clicks_all_time_mobile_' , 'quick-adsense-reloaded');
				if (false === $results_click_mob_1) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare, WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_click_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as date_click, stats_year as ad_year FROM `{$wpdb->prefix}quads_impressions_mobile`  GROUP BY stats_year"));
					wp_cache_set('quads_stats_clicks_all_time_mobile_' , $results_click_mob_1, 'quick-adsense-reloaded', 3600);
				}
				
				if($results_click_mob_1) { 
					foreach($results_click_mob_1 as $key => $value) {
						foreach($results_click_F as $key2=>$value2){
							if($value->ad_year == $value2->ad_year){
								$results_click_F[$key2]->date_click = $value2->date_click+$value->date_click;
								unset($results_click_mob_1[$key]);
							}
						}
					}
					$results_click_F = array_merge($results_click_F,$results_click_mob_1);
				}
			}
			else{
				$results_click_F = wp_cache_get('quads_stats_clicks_all_time_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $results_click_F) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT IFNULL(sum(date_click),0) as date_click, ad_year FROM `{$wpdb->prefix}quads_single_stats_` where ad_id = %d group by ad_year ;",$ad_id));
					wp_cache_set('quads_stats_clicks_all_time_' . $ad_id , $results_click_F, 'quick-adsense-reloaded', 3600);
				}
				$results_click_F_2 = wp_cache_get('quads_stats_clicks_all_time_group_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $results_click_F_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_click_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, IFNULL(SUM(ad_clicks),0) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE ad_id = %d GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name", $ad_id));
					wp_cache_set('quads_stats_clicks_all_time_group_' . $ad_id , $results_click_F_2, 'quick-adsense-reloaded', 3600);
				}
				$results_click_desk_1 = wp_cache_get('quads_stats_clicks_all_time_desktop_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $results_click_desk_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_click_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as date_click, stats_year as ad_year  FROM `{$wpdb->prefix}quads_impressions_desktop` where ad_id = %d GROUP BY stats_year",$ad_id));
					wp_cache_set('quads_stats_clicks_all_time_desktop_' . $ad_id , $results_click_desk_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_click_desk_1) { 
					foreach($results_click_desk_1 as $key => $value) {
						foreach($results_click_F as $key2=>$value2){
							if($value->ad_year == $value2->ad_year){
								$results_click_F[$key2]->date_click = $value2->date_click+$value->date_click;
								unset($results_click_desk_1[$key]);
							}
						}
					}
					$results_click_F = array_merge($results_click_F,$results_click_desk_1);
				}
				$results_click_mob_1 = wp_cache_get('quads_stats_clicks_all_time_mobile_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $results_click_mob_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_click_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as date_click, stats_year as ad_year FROM `{$wpdb->prefix}quads_impressions_mobile`  where ad_id = %d GROUP BY stats_year",$ad_id));
					wp_cache_set('quads_stats_clicks_all_time_mobile_' . $ad_id , $results_click_mob_1, 'quick-adsense-reloaded', 3600);
				}
				
				if($results_click_mob_1) { 
					foreach($results_click_mob_1 as $key => $value) {
						foreach($results_click_F as $key2=>$value2){
							if($value->ad_year == $value2->ad_year){
								$results_click_F[$key2]->date_click = $value2->date_click+$value->date_click;
								unset($results_click_mob_1[$key]);
							}
						}
					}
					$results_click_F = array_merge($results_click_F,$results_click_mob_1);
				}
				$results_click_desk_2 = wp_cache_get('quads_stats_clicks_all_time_desktop_group_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $results_click_desk_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_click_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as clicks, stats_date as ad_thetime , 'desktop' as ad_device_name  FROM `{$wpdb->prefix}quads_impressions_desktop` where ad_id = %d GROUP BY stats_year",$ad_id));
					wp_cache_set('quads_stats_clicks_all_time_desktop_group_' . $ad_id , $results_click_desk_2, 'quick-adsense-reloaded', 3600);
				}
				if($results_click_desk_2) { 
					foreach($results_click_desk_2 as $key => $value) {
						foreach($results_click_F_2 as $key2=>$value2){
							if($value->ad_year == $value2->ad_year){
								$results_click_F[$key2]->date_click = $value2->date_click+$value->date_click;
								unset($results_click_desk_2[$key]);
							}
						}
					}
					$results_click_F_2 = array_merge($results_click_F_2,$results_click_desk_2);
				}
				$results_click_mob_2 = wp_cache_get('quads_stats_clicks_all_time_mobile_group_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $results_click_mob_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_click_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as clicks, stats_date as ad_thetime , 'mobile' as ad_device_name  FROM `{$wpdb->prefix}quads_impressions_mobile`  where ad_id = %d GROUP BY stats_year",$ad_id));
					wp_cache_set('quads_stats_clicks_all_time_mobile_group_' . $ad_id , $results_click_mob_2, 'quick-adsense-reloaded', 3600);
				}
				
				if($results_click_mob_2) { 
					foreach($results_click_mob_2 as $key => $value) {
						foreach($results_click_F_2 as $key2=>$value2){
							if($value->ad_year == $value2->ad_year){
								$results_click_F_2[$key2]->date_click = $value2->date_click+$value->date_click;
								unset($results_click_mob_2[$key]);
							}
						}
					}
					$results_click_F_2 = array_merge($results_click_F_2,$results_click_mob_2);
				}

				foreach ($results_click_F as $key => $value) {
						foreach ($results_click_F_2 as $key2 => $value2) {
							if( gmdate("Y", $value2->ad_thetime) == $value->ad_year && $value2->ad_device_name == 'desktop' ) {
								$value->desk_click += $value2->clicks;
							}elseif( gmdate("Y", $value2->ad_thetime) == $value->ad_year && $value2->ad_device_name == 'mobile' ) {
								$value->mob_click += $value2->clicks;
							}
						}
				}

			}
			
		$array_c = array_values($results_click_F);
		$ad_mob_clicks = $ad_desk_clicks = $ad_clicks = 0;
		$ad_mob_click_values = $ad_desk_click_values = $ad_click_values = array();
		$ad_mob_click_values_ = $ad_desk_click_values_ = $ad_click_values_ = array();

		foreach ($array_c as $key => $value) {
			$ad_mob_clicks += $value->mob_click;
			$ad_desk_clicks += $value->desk_click;
			$ad_clicks += $value->desk_click+$value->mob_click;
			$ad_mob_click_values[] = $value->ad_year;
			$ad_desk_click_values[] = $value->ad_year;
			$ad_click_values[] = $value->ad_year;
			$ad_mob_click_values_[] = $value->mob_click;
			$ad_desk_click_values_[] = $value->desk_click;
			$ad_click_values_[] = $value->desk_click+$value->mob_click;
		}
			$mob_indi_click_day_counts = array_map('quadsRemoveNullElement',$ad_mob_click_values_);
			$desk_indi_click_day_counts = array_map('quadsRemoveNullElement',$ad_desk_click_values_);
			$individual_click_day_counts = array_map('quadsRemoveNullElement',$ad_click_values_);
			
			if($ad_id=="all")
			{
				$results_top5 = wp_cache_get('quads_stats_top5_all_time_all_' , 'quick-adsense-reloaded');
				if (false === $results_top5) {
					 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id   GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",5));
					wp_cache_set('quads_stats_top5_all_time_all_' , $results_top5, 'quick-adsense-reloaded', 3600);
				}
				$results_top5_2 = wp_cache_get('quads_stats_top5_all_time_all_group_' , 'quick-adsense-reloaded');
				if (false === $results_top5_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare
					$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, IFNULL(SUM(ad_impressions),0) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC"));
					wp_cache_set('quads_stats_top5_all_time_all_group_' , $results_top5_2, 'quick-adsense-reloaded', 3600);
				}
				$total_click=[0,0,0,0,0];
				$total_impression=[0,0,0,0,0];
				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key] + $value2->impressions;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key] + $value2->impressions;
							}
						}

						$results_top5[$key]->total_click = $total_click[$key];
						$results_top5[$key]->total_impression = $total_impression[$key];
				}

				$array_top5 = array_values($results_top5);	

				$array_top_clicks = wp_cache_get('quads_stats_top5_all_time_clicks_' , 'quick-adsense-reloaded');
				$array_top_imprs = wp_cache_get('quads_stats_top5_all_time_imprs_' , 'quick-adsense-reloaded');
				if (false === $array_top_clicks) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare
					$array_top_clicks = $wpdb->get_results(
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare
					$wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks ,IFNULL(SUM(click_mob.stats_clicks),0)as mob_clicks , SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click FROM {$wpdb->prefix}posts as posts LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id WHERE posts.post_type='quads-ads' AND posts.post_status='publish' GROUP BY posts.ID ORDER BY total_click DESC LIMIT 5;")
					);
					wp_cache_set('quads_stats_top5_all_time_clicks_' , $array_top_clicks, 'quick-adsense-reloaded', 3600);
				}
				if (false === $array_top_imprs) {
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare
					$array_top_imprs = $wpdb->get_results(
						// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnnecessaryPrepare
						$wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(impr_desk.stats_impressions),0)as desk_imprsn ,IFNULL(SUM(impr_mob.stats_impressions),0)as mob_imprsn , SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression FROM {$wpdb->prefix}posts as posts LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id  LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id WHERE posts.post_type='quads-ads' AND posts.post_status='publish' GROUP BY posts.ID ORDER BY total_impression DESCLIMIT 5;")
					);
					wp_cache_set('quads_stats_top5_all_time_imprs_' , $array_top_imprs, 'quick-adsense-reloaded', 3600);
				}
				$array_top_imprs_=$array_top_imprs;
				
				foreach($array_top_clicks as $key=>$value){
					foreach($array_top_imprs as $key2=>$value2){
					  if($value->ID == $value2->ID){
						$value->desk_imprsn = $value2->desk_imprsn;
						$value->total_impression = $value2->total_impression;
						$value->mob_imprsn = $value2->mob_imprsn;
						unset($array_top_imprs_[$key]);
					  }
					}
				}

				foreach($array_top_imprs_ as $key=>$value){
						$value->desk_clicks = 0;
						$value->total_click = 0;
						$value->mob_click = 0;
				}
				$array_top_clicks =  array_merge($array_top_clicks, $array_top_imprs_);
				$array_top5_ =$array_top_clicks = array_slice($array_top_clicks, 0, 5);
	
		
				if(!empty($array_top_clicks)){
					foreach($array_top5 as $key => $value){
						foreach($array_top_clicks as $key2=>$value2){
							if($value->ID == $value2->ID){

								$array_top5[$key]->total_click = $value->total_click+$value2->total_click;
								$array_top5[$key]->desk_clicks = $value->desk_clicks+$value2->desk_clicks;
								$array_top5[$key]->desk_imprsn = $value->desk_imprsn+$value2->desk_imprsn;
								$array_top5[$key]->mob_clicks = $value->mob_clicks+$value2->mob_clicks;
								$array_top5[$key]->mob_imprsn = $value->mob_imprsn+$value2->mob_imprsn;
								$array_top5[$key]->total_impression = $value->total_impression+$value2->total_impression;
								unset($array_top5_[$key2]);
							}
						}
					}
					$array_top5 =  array_merge($array_top5,$array_top5_);
					$array_top5 = array_slice($array_top5, 0, 5);
			 }
			}
		
		}

		else if( $day == "this_year" ){
			
			$loop = 30 ;
			$month= gmdate("m");
			$date_= gmdate("d");
			$year= gmdate("Y");
			$first_date_ = gmdate('Y-m-d',strtotime('first day of this month'));
			$current_date_month_ = gmdate('Y-m-d');
			if($ad_id=="all"){
				$yearly_mob_impressions = wp_cache_get('quads_stats_yearly_impressions_all_mobile_' , 'quick-adsense-reloaded');
				if (false === $yearly_mob_impressions) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_mob_impressions = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 1 THEN ad_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 2 THEN ad_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 3 THEN ad_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 4 THEN ad_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 5 THEN ad_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 6 THEN ad_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 7 THEN ad_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 8 THEN ad_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 9 THEN ad_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 10 THEN ad_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 11 THEN ad_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 12 THEN ad_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = %s AND  YEAR(FROM_UNIXTIME(ad_thetime)) = %s ; ",array('mobile',$year)));
					wp_cache_set('quads_stats_yearly_impressions_all_mobile_' , $yearly_mob_impressions, 'quick-adsense-reloaded', 3600);
				}
				$yearly_desk_impressions = wp_cache_get('quads_stats_yearly_impressions_all_desktop_' , 'quick-adsense-reloaded');
				if (false === $yearly_desk_impressions) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_desk_impressions = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 1 THEN ad_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 2 THEN ad_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 3 THEN ad_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 4 THEN ad_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 5 THEN ad_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 6 THEN ad_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 7 THEN ad_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 8 THEN ad_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 9 THEN ad_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 10 THEN ad_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 11 THEN ad_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 12 THEN ad_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = %s AND  YEAR(FROM_UNIXTIME(ad_thetime)) = %s ; ",array('desktop',$year)));
					wp_cache_set('quads_stats_yearly_impressions_all_desktop_' , $yearly_desk_impressions, 'quick-adsense-reloaded', 3600);
				}
				$yearly_mob_impressions_1 = wp_cache_get('quads_stats_yearly_impressions_all_mobile_1_' , 'quick-adsense-reloaded');
				if (false === $yearly_mob_impressions_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_mob_impressions_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s ; ",array($year)));
					wp_cache_set('quads_stats_yearly_impressions_all_mobile_1_' , $yearly_mob_impressions_1, 'quick-adsense-reloaded', 3600);
				}
				$yearly_desk_impressions_1 = wp_cache_get('quads_stats_yearly_impressions_all_desktop_1_' , 'quick-adsense-reloaded');
				if (false === $yearly_desk_impressions_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_desk_impressions_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s ; ",array($year)));
					wp_cache_set('quads_stats_yearly_impressions_all_desktop_1_' , $yearly_desk_impressions_1, 'quick-adsense-reloaded', 3600);	
				}
			}else{
				$yearly_mob_impressions = wp_cache_get('quads_stats_yearly_impressions_mobile_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $yearly_mob_impressions) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_mob_impressions = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 1 THEN ad_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 2 THEN ad_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 3 THEN ad_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 4 THEN ad_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 5 THEN ad_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 6 THEN ad_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 7 THEN ad_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 8 THEN ad_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 9 THEN ad_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 10 THEN ad_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 11 THEN ad_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 12 THEN ad_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = %s AND  YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND ad_id = %d; ",array('mobile',$year,$ad_id)));
					wp_cache_set('quads_stats_yearly_impressions_mobile_' . $ad_id , $yearly_mob_impressions, 'quick-adsense-reloaded', 3600);
				}
				$yearly_desk_impressions = wp_cache_get('quads_stats_yearly_impressions_desktop_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $yearly_desk_impressions) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$yearly_desk_impressions = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 1 THEN ad_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 2 THEN ad_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 3 THEN ad_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 4 THEN ad_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 5 THEN ad_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 6 THEN ad_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 7 THEN ad_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 8 THEN ad_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 9 THEN ad_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 10 THEN ad_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 11 THEN ad_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 12 THEN ad_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = %s AND  YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND ad_id = %d; ",array('desktop',$year,$ad_id)));
					wp_cache_set('quads_stats_yearly_impressions_desktop_' . $ad_id , $yearly_desk_impressions, 'quick-adsense-reloaded', 3600);
				}
				$yearly_mob_impressions_1 = wp_cache_get('quads_stats_yearly_impressions_mobile_1_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $yearly_mob_impressions_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_mob_impressions_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s AND ad_id = %d; ",array($year,$ad_id)));
					wp_cache_set('quads_stats_yearly_impressions_mobile_1_' . $ad_id , $yearly_mob_impressions_1, 'quick-adsense-reloaded', 3600);
				}
				$yearly_desk_impressions_1 = wp_cache_get('quads_stats_yearly_impressions_desktop_1_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $yearly_desk_impressions_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_desk_impressions_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s AND ad_id = %d; ",array($year,$ad_id)));
					wp_cache_set('quads_stats_yearly_impressions_desktop_1_' . $ad_id , $yearly_desk_impressions_1, 'quick-adsense-reloaded', 3600);
				}
			}
			if($yearly_mob_impressions_1){
			foreach($yearly_mob_impressions as $key => $value){
				foreach($yearly_mob_impressions_1 as $key2 => $value2){
					if(isset($value2->jan_impr)){
						$yearly_mob_impressions[$key]->jan_impr = $value2->jan_impr+$value->jan_impr;
						$yearly_mob_impressions[$key]->feb_impr = $value2->feb_impr+$value->feb_impr;
						$yearly_mob_impressions[$key]->mar_impr = $value2->jan_impr+$value->mar_impr;
						$yearly_mob_impressions[$key]->apr_impr = $value2->apr_impr+$value->apr_impr;
						$yearly_mob_impressions[$key]->may_impr = $value2->may_impr+$value->may_impr;
						$yearly_mob_impressions[$key]->jun_impr = $value2->jun_impr+$value->jun_impr;
						$yearly_mob_impressions[$key]->aug_impr = $value2->aug_impr+$value->aug_impr;
						$yearly_mob_impressions[$key]->sep_impr = $value2->sep_impr+$value->sep_impr;
						$yearly_mob_impressions[$key]->oct_impr = $value2->oct_impr+$value->oct_impr;
						$yearly_mob_impressions[$key]->nov_impr = $value2->nov_impr+$value->nov_impr;
						$yearly_mob_impressions[$key]->dec_impr = $value2->dec_impr+$value->dec_impr;

					}
				}
				
			}
		}
		if($yearly_desk_impressions_1){
			foreach($yearly_desk_impressions as $key => $value){
				foreach($yearly_desk_impressions_1 as $key2 => $value2){
					if(isset($value2->jan_impr)){
						$yearly_desk_impressions[$key]->jan_impr = $value2->jan_impr+$value->jan_impr;
						$yearly_desk_impressions[$key]->feb_impr = $value2->feb_impr+$value->feb_impr;
						$yearly_desk_impressions[$key]->mar_impr = $value2->jan_impr+$value->mar_impr;
						$yearly_desk_impressions[$key]->apr_impr = $value2->apr_impr+$value->apr_impr;
						$yearly_desk_impressions[$key]->may_impr = $value2->may_impr+$value->may_impr;
						$yearly_desk_impressions[$key]->jun_impr = $value2->jun_impr+$value->jun_impr;
						$yearly_desk_impressions[$key]->aug_impr = $value2->aug_impr+$value->aug_impr;
						$yearly_desk_impressions[$key]->sep_impr = $value2->sep_impr+$value->sep_impr;
						$yearly_desk_impressions[$key]->oct_impr = $value2->oct_impr+$value->oct_impr;
						$yearly_desk_impressions[$key]->nov_impr = $value2->nov_impr+$value->nov_impr;
						$yearly_desk_impressions[$key]->dec_impr = $value2->dec_impr+$value->dec_impr;

					}
				}
			}
			}

			$mob_imp=reset($yearly_mob_impressions);
			$desk_imp=reset($yearly_desk_impressions);

			$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = array();
		
			$ad_mob_imprsn_values = [$mob_imp->jan_impr,$mob_imp->feb_impr,$mob_imp->mar_impr,$mob_imp->apr_impr,$mob_imp->may_impr,$mob_imp->jun_impr,$mob_imp->jul_impr,$mob_imp->aug_impr,$mob_imp->sep_impr,$mob_imp->oct_impr,$mob_imp->nov_impr,$mob_imp->dec_impr];
			$ad_mob_imprsn = $mob_imp->jan_impr+$mob_imp->feb_impr+$mob_imp->mar_impr+$mob_imp->apr_impr+$mob_imp->may_impr+$mob_imp->jun_impr+$mob_imp->jul_impr+$mob_imp->aug_impr+$mob_imp->sep_impr+$mob_imp->oct_impr+$mob_imp->nov_impr+$mob_imp->dec_impr;
			
			$ad_desk_imprsn_values = [$desk_imp->jan_impr,$desk_imp->feb_impr,$desk_imp->mar_impr,$desk_imp->apr_impr,$desk_imp->may_impr,$desk_imp->jun_impr,$desk_imp->jul_impr,$desk_imp->aug_impr,$desk_imp->sep_impr,$desk_imp->oct_impr,$desk_imp->nov_impr,$desk_imp->dec_impr];
			$ad_desk_imprsn =$desk_imp->jan_impr+$desk_imp->feb_impr+$desk_imp->mar_impr+$desk_imp->apr_impr+$desk_imp->may_impr+$desk_imp->jun_impr+$desk_imp->jul_impr+$desk_imp->aug_impr+$desk_imp->sep_impr+$desk_imp->oct_impr+$desk_imp->nov_impr+$desk_imp->dec_impr;
			$ad_imprsn_values = quadsSumArrays($ad_mob_imprsn_values,$ad_desk_imprsn_values);
			$ad_imprsn = $ad_mob_imprsn+$ad_desk_imprsn;

			$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
			$desk_indi_impr_day_counts = $ad_desk_imprsn_values;
			$individual_impr_day_counts = $ad_imprsn_values;
			$individual_ad_dates = [1];

			if($ad_id=="all"){
				$yearly_mob_clicks = wp_cache_get('quads_stats_yearly_clicks_all_mobile_' , 'quick-adsense-reloaded');
				if (false === $yearly_mob_clicks) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_mob_clicks = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 1 THEN ad_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 2 THEN ad_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 3 THEN ad_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 4 THEN ad_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 5 THEN ad_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 6 THEN ad_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 7 THEN ad_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 8 THEN ad_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 9 THEN ad_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 10 THEN ad_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 11 THEN ad_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 12 THEN ad_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = %s AND  YEAR(FROM_UNIXTIME(ad_thetime)) = %s ; ",array('mobile',$year)));
					wp_cache_set('quads_stats_yearly_clicks_all_mobile_' , $yearly_mob_clicks, 'quick-adsense-reloaded', 3600);
				}
				$yearly_desk_clicks = wp_cache_get('quads_stats_yearly_clicks_all_desktop_' , 'quick-adsense-reloaded');
				if (false === $yearly_desk_clicks) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_desk_clicks = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 1 THEN ad_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 2 THEN ad_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 3 THEN ad_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 4 THEN ad_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 5 THEN ad_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 6 THEN ad_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 7 THEN ad_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 8 THEN ad_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 9 THEN ad_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 10 THEN ad_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 11 THEN ad_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 12 THEN ad_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = %s AND  YEAR(FROM_UNIXTIME(ad_thetime)) = %s ; ",array('desktop',$year)));
					wp_cache_set('quads_stats_yearly_clicks_all_desktop_' , $yearly_desk_clicks, 'quick-adsense-reloaded', 3600);
				}
				$yearly_mob_clicks_1 = wp_cache_get('quads_stats_yearly_clicks_all_mobile_1_' , 'quick-adsense-reloaded');
				if (false === $yearly_mob_clicks_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_mob_clicks_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s ; ",array($year)));
					wp_cache_set('quads_stats_yearly_clicks_all_mobile_1_' , $yearly_mob_clicks_1, 'quick-adsense-reloaded', 3600);
				}
				$yearly_desk_clicks_1 = wp_cache_get('quads_stats_yearly_clicks_all_desktop_1_' , 'quick-adsense-reloaded');
				if (false === $yearly_desk_clicks_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_desk_clicks_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s ; ",array($year)));
					wp_cache_set('quads_stats_yearly_clicks_all_desktop_1_' , $yearly_desk_clicks_1, 'quick-adsense-reloaded', 3600);
				}
			}else{
				$yearly_mob_clicks = wp_cache_get('quads_stats_yearly_clicks_mobile_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $yearly_mob_clicks) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_mob_clicks = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 1 THEN ad_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 2 THEN ad_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 3 THEN ad_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 4 THEN ad_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 5 THEN ad_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 6 THEN ad_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 7 THEN ad_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 8 THEN ad_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 9 THEN ad_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 10 THEN ad_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 11 THEN ad_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 12 THEN ad_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = %s AND  YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND ad_id = %d; ",array('mobile',$year,$ad_id)));
					wp_cache_set('quads_stats_yearly_clicks_mobile_' . $ad_id , $yearly_mob_clicks, 'quick-adsense-reloaded', 3600);
				}
				$yearly_desk_clicks = wp_cache_get('quads_stats_yearly_clicks_desktop_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $yearly_desk_clicks) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_desk_clicks = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 1 THEN ad_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 2 THEN ad_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 3 THEN ad_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 4 THEN ad_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 5 THEN ad_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 6 THEN ad_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 7 THEN ad_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 8 THEN ad_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 9 THEN ad_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 10 THEN ad_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 11 THEN ad_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(ad_thetime)) = 12 THEN ad_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = %s AND  YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND ad_id = %d; ",array('desktop',$year,$ad_id)));
					wp_cache_set('quads_stats_yearly_clicks_desktop_' . $ad_id , $yearly_desk_clicks, 'quick-adsense-reloaded', 3600);
				}
				$yearly_mob_clicks_1 = wp_cache_get('quads_stats_yearly_clicks_mobile_1_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $yearly_mob_clicks_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_mob_clicks_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE YEAR(FROM_UNIXTIME(stats_date)) = %s AND ad_id = %d; ",array($year,$ad_id)));
					wp_cache_set('quads_stats_yearly_clicks_mobile_1_' . $ad_id , $yearly_mob_clicks_1, 'quick-adsense-reloaded', 3600);
				}
				$yearly_desk_clicks_1 = wp_cache_get('quads_stats_yearly_clicks_desktop_1_' . $ad_id , 'quick-adsense-reloaded');
				if (false === $yearly_desk_clicks_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$yearly_desk_clicks_1 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE YEAR(FROM_UNIXTIME(stats_date)) = %s AND ad_id = %d; ",array($year,$ad_id)));
					wp_cache_set('quads_stats_yearly_clicks_desktop_1_' . $ad_id , $yearly_desk_clicks_1, 'quick-adsense-reloaded', 3600);
				}
			}

			if($yearly_mob_clicks_1){
				foreach($yearly_mob_clicks as $key => $value){
					foreach($yearly_mob_clicks_1 as $key2 => $value2){
							$yearly_mob_clicks[$key]->jan_clk = $value2->jan_clk+$value->jan_clk;
							$yearly_mob_clicks[$key]->feb_clk = $value2->feb_clk+$value->feb_clk;
							$yearly_mob_clicks[$key]->mar_clk = $value2->jan_clk+$value->mar_clk;
							$yearly_mob_clicks[$key]->apr_clk = $value2->apr_clk+$value->apr_clk;
							$yearly_mob_clicks[$key]->may_clk = $value2->may_clk+$value->may_clk;
							$yearly_mob_clicks[$key]->jun_clk = $value2->jun_clk+$value->jun_clk;
							$yearly_mob_clicks[$key]->aug_clk = $value2->aug_clk+$value->aug_clk;
							$yearly_mob_clicks[$key]->sep_clk = $value2->sep_clk+$value->sep_clk;
							$yearly_mob_clicks[$key]->oct_clk = $value2->oct_clk+$value->oct_clk;
							$yearly_mob_clicks[$key]->nov_clk = $value2->nov_clk+$value->nov_clk;
							$yearly_mob_clicks[$key]->dec_clk = $value2->dec_clk+$value->dec_clk;
	
						
					}
					
				}
			}
			if($yearly_desk_clicks_1){
				foreach($yearly_desk_clicks as $key => $value){
					foreach($yearly_desk_clicks_1 as $key2 => $value2){
							$yearly_desk_clicks[$key]->jan_clk = $value2->jan_clk+$value->jan_clk;
							$yearly_desk_clicks[$key]->feb_clk = $value2->feb_clk+$value->feb_clk;
							$yearly_desk_clicks[$key]->mar_clk = $value2->jan_clk+$value->mar_clk;
							$yearly_desk_clicks[$key]->apr_clk = $value2->apr_clk+$value->apr_clk;
							$yearly_desk_clicks[$key]->may_clk = $value2->may_clk+$value->may_clk;
							$yearly_desk_clicks[$key]->jun_clk = $value2->jun_clk+$value->jun_clk;
							$yearly_desk_clicks[$key]->aug_clk = $value2->aug_clk+$value->aug_clk;
							$yearly_desk_clicks[$key]->sep_clk = $value2->sep_clk+$value->sep_clk;
							$yearly_desk_clicks[$key]->oct_clk = $value2->oct_clk+$value->oct_clk;
							$yearly_desk_clicks[$key]->nov_clk = $value2->nov_clk+$value->nov_clk;
							$yearly_desk_clicks[$key]->dec_clk = $value2->dec_clk+$value->dec_clk;
					}
				}
				}

			$mob_clk=reset($yearly_mob_clicks);
			$desk_clk=reset($yearly_desk_clicks);			
			
			$ad_mob_click_values = $ad_desk_click_values = $ad_click_values = array();

			$ad_mob_clicks = $mob_clk->jan_impr+$mob_clk->feb_impr+$mob_clk->mar_impr+$mob_clk->apr_impr+$mob_clk->may_impr+$mob_clk->jun_impr+$mob_clk->jul_impr+$mob_clk->aug_impr+$mob_clk->sep_impr+$mob_clk->oct_impr+$mob_clk->nov_impr+$mob_clk->dec_impr;
			$ad_mob_click_values = [$mob_clk->jan_impr,$mob_clk->feb_impr,$mob_clk->mar_impr,$mob_clk->apr_impr,$mob_clk->may_impr,$mob_clk->jun_impr,$mob_clk->jul_impr,$mob_clk->aug_impr,$mob_clk->sep_impr,$mob_clk->oct_impr,$mob_clk->nov_impr,$mob_clk->dec_impr];
			
			$ad_desk_clicks = $desk_clk->jan_impr+$desk_clk->feb_impr+$desk_clk->mar_impr+$desk_clk->apr_impr+$desk_clk->may_impr+$desk_clk->jun_impr+$desk_clk->jul_impr+$desk_clk->aug_impr+$desk_clk->sep_impr+$desk_clk->oct_impr+$desk_clk->nov_impr+$desk_clk->dec_impr;
			$ad_desk_click_values = [$desk_clk->jan_impr,$desk_clk->feb_impr,$desk_clk->mar_impr,$desk_clk->apr_impr,$desk_clk->may_impr,$desk_clk->jun_impr,$desk_clk->jul_impr,$desk_clk->aug_impr,$desk_clk->sep_impr,$desk_clk->oct_impr,$desk_clk->nov_impr,$desk_clk->dec_impr];

			$ad_clicks =  $ad_mob_clicks+$ad_desk_clicks;
			$ad_click_values = quadsSumArrays($ad_mob_click_values,$ad_desk_click_values);

			$mob_indi_click_day_counts = $ad_mob_click_values;
			$desk_indi_click_day_counts = $ad_desk_click_values;
			$individual_click_day_counts = $ad_click_values;

			
			if($ad_id=="all")
			{
				$results_top5 = wp_cache_get('quads_stats_top5_ads_yearly_' . $year , 'quick-adsense-reloaded');
				if (false === $results_top5) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id   WHERE year(`{$wpdb->prefix}quads_single_stats_`.ad_date) = %d GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($year,5)));
					wp_cache_set('quads_stats_top5_ads_yearly_' . $year , $results_top5, 'quick-adsense-reloaded', 3600);
				}
				$results_top5_2  = wp_cache_get('quads_stats_top5_ads_yearly_2_' . $year , 'quick-adsense-reloaded');
				if (false === $results_top5_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE YEAR(FROM_UNIXTIME(`{$wpdb->prefix}quads_stats`.ad_thetime)) = %d GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",array($year)));
					wp_cache_set('quads_stats_top5_ads_yearly_2_' . $year , $results_top5_2, 'quick-adsense-reloaded', 3600);
				}
				$total_click=[0,0,0,0,0];
				$total_impression=[0,0,0,0,0];
				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key] + $value2->impressions;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_clicks = $value2->clicks;
								$total_click[$key] =  $total_click[$key] + $value2->clicks;
								$total_impression[$key] =  $total_impression[$key]  + $value2->impressions;
							}
						}
						$results_top5[$key]->total_click = $total_click[$key];
						$results_top5[$key]->total_impression = $total_impression[$key];

				}
				$array_top5 = array_values($results_top5);	
			    $array_top_clicks = wp_cache_get('quads_stats_yearly_top_clicks_all_' . $year , 'quick-adsense-reloaded');
				if (false === $array_top_clicks) {
					// phpcs:ignore  WordPress.DB.DirectDatabaseQuery.DirectQuery
					$array_top_clicks = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks ,IFNULL(SUM(click_mob.stats_clicks),0)as mob_clicks , SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id  AND YEAR(FROM_UNIXTIME(impr_mob.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND YEAR(FROM_UNIXTIME(impr_mob.stats_date)) = %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC
					LIMIT 5;",array($year,$year)));
					wp_cache_set('quads_stats_yearly_top_clicks_all_' . $year , $array_top_clicks, 'quick-adsense-reloaded', 3600);
				}
				$array_top_imprs = wp_cache_get('quads_stats_yearly_top_impressions_all_' . $year , 'quick-adsense-reloaded');;
				if (false === $array_top_imprs) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$array_top_imprs = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(impr_desk.stats_impressions),0)as desk_imprsn ,IFNULL(SUM(impr_mob.stats_impressions),0)as mob_imprsn , SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id  AND YEAR(FROM_UNIXTIME(impr_mob.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND YEAR(FROM_UNIXTIME(impr_mob.stats_date)) = %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_impression DESC
					LIMIT 5;",array($year,$year)));
					wp_cache_set('quads_stats_yearly_top_impressions_all_' . $year , $array_top_imprs, 'quick-adsense-reloaded', 3600);
				}
				$array_top_imprs_ = $array_top_imprs;
				foreach($array_top_clicks as $key=>$value){
					foreach($array_top_imprs as $key2=>$value2){
					  if($value->ID == $value2->ID){
						$value->desk_imprsn = $value2->desk_imprsn;
						$value->total_impression = $value2->total_impression;
						$value->mob_imprsn = $value2->mob_imprsn;
						unset($array_top_imprs_[$key]);
					  }
					}
				}

				foreach($array_top_imprs_ as $key=>$value){
						$value->desk_clicks = 0;
						$value->total_click = 0;
						$value->mob_click = 0;
				}
				$array_top_clicks =  array_merge($array_top_clicks, $array_top_imprs_);
				$array_top5_ =$array_top_clicks = array_slice($array_top_clicks, 0, 5);
	
		
				if(!empty($array_top_clicks)){
					foreach($array_top5 as $key => $value){
						foreach($array_top_clicks as $key2=>$value2){
							if($value->ID == $value2->ID){

								$array_top5[$key]->total_click = $value->total_click+$value2->total_click;
								$array_top5[$key]->desk_clicks = $value->desk_clicks+$value2->desk_clicks;
								$array_top5[$key]->desk_imprsn = $value->desk_imprsn+$value2->desk_imprsn;
								$array_top5[$key]->mob_clicks = $value->mob_clicks+$value2->mob_clicks;
								$array_top5[$key]->mob_imprsn = $value->mob_imprsn+$value2->mob_imprsn;
								$array_top5[$key]->total_impression = $value->total_impression+$value2->total_impression;
								unset($array_top5_[$key2]);
							}
						}
					}
					$array_top5 =  array_merge($array_top5,$array_top5_);
					$array_top5 = array_slice($array_top5, 0, 5);
			 }

			}
	}
	else if( $day == "yesterday" ){
		
		$yesterday_date = gmdate('Y-m-d',strtotime("-1 days"));
		$get_impressions_specific_dates = str_replace('-','/',$yesterday_date);
		if($ad_id=="all")
		{
			$results_top5 = wp_cache_get('quads_stats_top5_ads_yesterday_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_top5) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id  WHERE `{$wpdb->prefix}quads_single_stats_`.`ad_date` = %s  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($yesterday_date,5)));
				wp_cache_set('quads_stats_top5_ads_yesterday_' . $yesterday_date , $results_top5, 'quick-adsense-reloaded', 3600);
			}
			
			$unix_todays_date = "'".intval(strtotime($yesterday_date))."'";
			$results_top5_2 = wp_cache_get('quads_stats_top5_ads_yesterday_2_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_top5_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is fixed and safe
				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.`ad_thetime` = %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",$unix_todays_date));
				wp_cache_set('quads_stats_top5_ads_yesterday_2_' . $yesterday_date , $results_top5_2, 'quick-adsense-reloaded', 3600);
			}
			
			foreach ($results_top5 as $key => $value) {
				$temp_var = $value->ID;
					foreach ($results_top5_2 as $key2 => $value2) {
						if($value2->ad_id == $temp_var && $value2->ad_device_name == 'desktop'){
							$value->desk_imprsn = $value2->impressions;
							$value->desk_clicks = $value2->clicks;
						}elseif($value2->ad_id == $temp_var && $value2->ad_device_name == 'mobile'){
							$value->mob_imprsn = $value2->impressions;
							$value->mob_clicks = $value2->clicks;
						}
					}
			}

			$array_top5 = array_values($results_top5);	
			$array_top_clicks = wp_cache_get('quads_stats_yesterday_top_clicks_all_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $array_top_clicks) {
				// phpcs:ignore  WordPress.DB.DirectDatabaseQuery.DirectQuery
				$array_top_clicks = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks ,IFNULL(SUM(click_mob.stats_clicks),0)as mob_clicks , SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id  AND click_desk.stats_date = %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date = %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC
				LIMIT 5;",array(strtotime($yesterday_date),strtotime($yesterday_date))));
				wp_cache_set('quads_stats_yesterday_top_clicks_all_' . $yesterday_date , $array_top_clicks, 'quick-adsense-reloaded', 3600);
			}
			$array_top_imprs = wp_cache_get('quads_stats_yesterday_top_impressions_all_' . $yesterday_date , 'quick-adsense-reloaded');;
			if (false === $array_top_imprs) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$array_top_imprs = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(impr_desk.stats_impressions),0)as desk_imprsn ,IFNULL(SUM(impr_mob.stats_impressions),0)as mob_imprsn , SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id  AND impr_mob.stats_date = %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date = %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_impression DESC
				LIMIT 5;",array(strtotime($yesterday_date),strtotime($yesterday_date))));
				wp_cache_set('quads_stats_yesterday_top_impressions_all_' . $yesterday_date , $array_top_imprs, 'quick-adsense-reloaded', 3600);
			}
			$array_top_imprs_ = $array_top_imprs;
			foreach($array_top_clicks as $key=>$value){
				foreach($array_top_imprs as $key2=>$value2){
				  if($value->ID == $value2->ID){
					$value->desk_imprsn = $value2->desk_imprsn;
					$value->total_impression = $value2->total_impression;
					$value->mob_imprsn = $value2->mob_imprsn;
					unset($array_top_imprs_[$key]);
				  }
				}
			}

			foreach($array_top_imprs_ as $key=>$value){
					$value->desk_clicks = 0;
					$value->total_click = 0;
					$value->mob_click = 0;
			}
			$array_top_clicks =  array_merge($array_top_clicks, $array_top_imprs_);
			$array_top5_ =$array_top_clicks = array_slice($array_top_clicks, 0, 5);

	
			if(!empty($array_top_clicks)){
				foreach($array_top5 as $key => $value){
					foreach($array_top_clicks as $key2=>$value2){
						if($value->ID == $value2->ID){

							$array_top5[$key]->total_click = $value->total_click+$value2->total_click;
							$array_top5[$key]->desk_clicks = $value->desk_clicks+$value2->desk_clicks;
							$array_top5[$key]->desk_imprsn = $value->desk_imprsn+$value2->desk_imprsn;
							$array_top5[$key]->mob_clicks = $value->mob_clicks+$value2->mob_clicks;
							$array_top5[$key]->mob_imprsn = $value->mob_imprsn+$value2->mob_imprsn;
							$array_top5[$key]->total_impression = $value->total_impression+$value2->total_impression;
							unset($array_top5_[$key2]);
						}
					}
				}
				$array_top5 =  array_merge($array_top5,$array_top5_);
				$array_top5 = array_slice($array_top5, 0, 5);
		 }
		}

		
		if($ad_id=="all"){
			$results_impresn_t_2 = wp_cache_get('quads_stats_yesterday_imprsn_clicks_all_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_impresn_t_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_t_2 = $wpdb->get_results($wpdb->prepare("SELECT  IFNULL(SUM(CASE ad_device_name WHEN 'mobile' THEN ad_impressions END),0) as mob_imprsn, IFNULL(SUM(CASE ad_device_name WHEN 'desktop' THEN ad_impressions END),0) as desk_imprsn ,IFNULL(SUM(CASE ad_device_name WHEN 'mobile' THEN ad_clicks END),0) as mob_click, IFNULL(SUM(CASE ad_device_name WHEN 'desktop' THEN ad_clicks END),0) as desk_click  FROM `{$wpdb->prefix}quads_stats` WHERE `ad_thetime` = %s",array(strtotime($yesterday_date))));
				wp_cache_set('quads_stats_yesterday_imprsn_clicks_all_' . $yesterday_date , $results_impresn_t_2, 'quick-adsense-reloaded', 3600);
			}
			$results_impresn_desk = wp_cache_get('quads_stats_yesterday_imprsn_desktop_all_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_impresn_desk) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  desk_imprsn  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date = %d",array(strtotime($yesterday_date))));
				wp_cache_set('quads_stats_yesterday_imprsn_desktop_all_' . $yesterday_date , $results_impresn_desk, 'quick-adsense-reloaded', 3600);
			}
			$results_impresn_mob = wp_cache_get('quads_stats_yesterday_imprsn_mobile_all_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_impresn_mob) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  mob_imprsn FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date = %d",array(strtotime($yesterday_date))));
				wp_cache_set('quads_stats_yesterday_imprsn_mobile_all_' . $yesterday_date , $results_impresn_mob, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_desk = wp_cache_get('quads_stats_yesterday_clicks_desktop_all_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_clicks_desk) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  desk_click FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date = %d",array(strtotime($yesterday_date))));
				wp_cache_set('quads_stats_yesterday_clicks_desktop_all_' . $yesterday_date , $results_clicks_desk, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_mob = wp_cache_get('quads_stats_yesterday_clicks_mobile_all_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_clicks_mob) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0)as   mob_click FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date = %d",array(strtotime($yesterday_date))));
				wp_cache_set('quads_stats_yesterday_clicks_mobile_all_' . $yesterday_date , $results_clicks_mob, 'quick-adsense-reloaded', 3600);
			}
			foreach($results_impresn_t_2 as $key=>$value){
				if($results_impresn_desk){
					$results_impresn_t_2[$key]->desk_imprsn = $value->desk_imprsn+$results_impresn_desk;
				}

				if($results_impresn_mob){
					$results_impresn_t_2[$key]->mob_imprsn = $value->mob_imprsn+$results_impresn_mob;
				}

				if($results_clicks_desk){
					$results_impresn_t_2[$key]->desk_click = $value->desk_click+$results_clicks_desk;
				}
				if($results_clicks_mob){
					$results_impresn_t_2[$key]->mob_click = $value->mob_click+$results_clicks_mob;
				}
			}
			}
		else{
			$results_impresn_t_2 = wp_cache_get('quads_stats_yesterday_imprsn_clicks_' . $ad_id . '_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_impresn_t_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_t_2 = $wpdb->get_results($wpdb->prepare("SELECT  IFNULL(SUM(CASE ad_device_name WHEN 'mobile' THEN ad_impressions END),0) as mob_imprsn, IFNULL(SUM(CASE ad_device_name WHEN 'desktop' THEN ad_impressions END),0) as desk_imprsn ,IFNULL(SUM(CASE ad_device_name WHEN 'mobile' THEN ad_clicks END),0) as mob_click, IFNULL(SUM(CASE ad_device_name WHEN 'desktop' THEN ad_clicks END),0) as desk_click  FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d AND `ad_thetime` = %s",array( $ad_id, strtotime($yesterday_date))));
				wp_cache_set('quads_stats_yesterday_imprsn_clicks_' . $ad_id . '_' . $yesterday_date , $results_impresn_t_2, 'quick-adsense-reloaded', 3600);
			}
			$results_impresn_desk = wp_cache_get('quads_stats_yesterday_imprsn_desktop_' . $ad_id . '_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_impresn_desk) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  desk_imprsn  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date = %d AND ad_id =%d",array(strtotime($yesterday_date),$ad_id)));
				wp_cache_set('quads_stats_yesterday_imprsn_desktop_' . $ad_id . '_' . $yesterday_date , $results_impresn_desk, 'quick-adsense-reloaded', 3600);
			}
			$results_impresn_mob = wp_cache_get('quads_stats_yesterday_imprsn_mobile_' . $ad_id . '_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_impresn_mob) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_impresn_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  mob_imprsn FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date = %d AND ad_id =%d",array(strtotime($yesterday_date),$ad_id)));
				wp_cache_set('quads_stats_yesterday_imprsn_mobile_' . $ad_id . '_' . $yesterday_date , $results_impresn_mob, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_desk = wp_cache_get('quads_stats_yesterday_clicks_desktop_' . $ad_id . '_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_clicks_desk) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  desk_click FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date = %d AND ad_id =%d",array(strtotime($yesterday_date),$ad_id)));
				wp_cache_set('quads_stats_yesterday_clicks_desktop_' . $ad_id . '_' . $yesterday_date , $results_clicks_desk, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_mob = wp_cache_get('quads_stats_yesterday_clicks_mobile_' . $ad_id . '_' . $yesterday_date , 'quick-adsense-reloaded');
			if (false === $results_clicks_mob) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0)as  mob_click FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date = %d AND ad_id =%d",array(strtotime($yesterday_date),$ad_id)));
				wp_cache_set('quads_stats_yesterday_clicks_mobile_' . $ad_id . '_' . $yesterday_date , $results_clicks_mob, 'quick-adsense-reloaded', 3600);
			}
			foreach($results_impresn_t_2 as $key=>$value){
				if($results_impresn_desk){
					$results_impresn_t_2[$key]->desk_imprsn = $value->desk_imprsn+$results_impresn_desk;
				}
				if($results_impresn_mob){
					$results_impresn_t_2[$key]->mob_imprsn = $value->mob_imprsn+$results_impresn_mob;
				}

				if($results_clicks_desk){
					$results_impresn_t_2[$key]->desk_click = $value->desk_click+$results_clicks_desk;
				}
				if($results_clicks_mob){
					$results_impresn_t_2[$key]->mob_click = $value->mob_click+$results_clicks_mob;
				}
			}
		}
			$array_i_t = array_values($results_impresn_t_2);
			$array_i_t = reset($array_i_t);
			
			$ad_mob_imprsn = isset($array_i_t->mob_imprsn)?intval($array_i_t->mob_imprsn):0;		
			$ad_desk_imprsn = isset($array_i_t->desk_imprsn)?intval($array_i_t->desk_imprsn):0;		
			$ad_imprsn = $ad_mob_imprsn + $ad_desk_imprsn;		
			$ad_desk_clicks = isset($array_i_t->desk_click)?intval($array_i_t->desk_click):0;
			$ad_mob_clicks = isset($array_i_t->mob_click)?intval($array_i_t->mob_click):0;
			$ad_clicks = $ad_mob_clicks+$ad_desk_clicks;

			$mob_indi_impr_day_counts=$ad_mob_imprsn;
			$desk_indi_impr_day_counts=$ad_desk_imprsn;
			$individual_impr_day_counts=$ad_imprsn;
			$ad_mob_imp_individual_dates=$yesterday_date;
			$ad_desk_imp_individual_dates=$yesterday_date;
			$mob_indi_click_day_counts=$ad_mob_clicks;
			$desk_indi_click_day_counts=$ad_desk_clicks;
			$individual_click_day_counts=$ad_clicks;
			$individual_ad_dates=$yesterday_date;

	}
	else if( $day == "today" ) {
		$get_impressions_specific_dates = str_replace('-','/',$todays_date);
		if($ad_id=="all")
			{
				$results_top5 = wp_cache_get('quads_stats_top5_ads_today_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_top5) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id  WHERE `{$wpdb->prefix}quads_single_stats_`.`ad_date` = %s  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($todays_date,5)));
					wp_cache_set('quads_stats_top5_ads_today_' . $todays_date , $results_top5, 'quick-adsense-reloaded', 3600);
				}
				
				$unix_todays_date = "'".intval(strtotime($todays_date))."'";
				$results_top5_2 = wp_cache_get('quads_stats_top5_ads_today_2_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_top5_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is fixed and safe
					$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.`ad_thetime` = %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",$unix_todays_date));
					wp_cache_set('quads_stats_top5_ads_today_2_' . $todays_date , $results_top5_2, 'quick-adsense-reloaded', 3600);
				}
				foreach ($results_top5 as $key => $value) {
					$temp_var = $value->ID;
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $temp_var && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_clicks = $value2->clicks;
							}elseif($value2->ad_id == $temp_var && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_clicks = $value2->clicks;
							}
						}
				}

				$array_top5 = array_values($results_top5);	

				$array_top_clicks = wp_cache_get('quads_stats_today_top_clicks_all_' . $todays_date , 'quick-adsense-reloaded');;
				if (false === $array_top_clicks) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$array_top_clicks = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks ,IFNULL(SUM(click_mob.stats_clicks),0)as mob_clicks , SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id  AND click_desk.stats_date = %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date = %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC
				LIMIT 5;",array(strtotime($todays_date),strtotime($todays_date))));
				wp_cache_set('quads_stats_today_top_clicks_all_' . $todays_date , $array_top_clicks, 'quick-adsense-reloaded', 3600);
			}
			$array_top_imprs = wp_cache_get('quads_stats_today_top_impressions_all_' . $todays_date , 'quick-adsense-reloaded');;
			if (false === $array_top_imprs) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$array_top_imprs_=$array_top_imprs = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(impr_desk.stats_impressions),0)as desk_imprsn ,IFNULL(SUM(impr_mob.stats_impressions),0)as mob_imprsn , SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id  AND impr_mob.stats_date = %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date = %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_impression DESC
				LIMIT 5;",array(strtotime($todays_date),strtotime($todays_date))));
				wp_cache_set('quads_stats_today_top_impressions_all_' . $todays_date , $array_top_imprs, 'quick-adsense-reloaded', 3600);
			}
			$array_top_imprs_ = $array_top_imprs;
			foreach($array_top_clicks as $key=>$value){
				foreach($array_top_imprs as $key2=>$value2){
				  if($value->ID == $value2->ID){
					$value->desk_imprsn = $value2->desk_imprsn;
					$value->total_impression = $value2->total_impression;
					$value->mob_imprsn = $value2->mob_imprsn;
					unset($array_top_imprs_[$key]);
				  }
				}
			}

			foreach($array_top_imprs_ as $key=>$value){
					$value->desk_clicks = 0;
					$value->total_click = 0;
					$value->mob_click = 0;
			}
			$array_top_clicks =  array_merge($array_top_clicks, $array_top_imprs_);
			$array_top5_ =$array_top_clicks = array_slice($array_top_clicks, 0, 5);

	
			if(!empty($array_top_clicks)){
				foreach($array_top5 as $key => $value){
					foreach($array_top_clicks as $key2=>$value2){
						if($value->ID == $value2->ID){

							$array_top5[$key]->total_click = $value->total_click+$value2->total_click;
							$array_top5[$key]->desk_clicks = $value->desk_clicks+$value2->desk_clicks;
							$array_top5[$key]->desk_imprsn = $value->desk_imprsn+$value2->desk_imprsn;
							$array_top5[$key]->mob_clicks = $value->mob_clicks+$value2->mob_clicks;
							$array_top5[$key]->mob_imprsn = $value->mob_imprsn+$value2->mob_imprsn;
							$array_top5[$key]->total_impression = $value->total_impression+$value2->total_impression;
							unset($array_top5_[$key2]);
						}
					}
				}
				$array_top5 =  array_merge($array_top5,$array_top5_);
				$array_top5 = array_slice($array_top5, 0, 5);
		 }

			}

			
			if($ad_id=="all"){
				$results_impresn_t_2 = wp_cache_get('quads_stats_today_imprsn_clicks_all_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_impresn_t_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_t_2 = $wpdb->get_results($wpdb->prepare("SELECT  SUM(CASE ad_device_name WHEN 'mobile' THEN ad_impressions END) as mob_imprsn, SUM(CASE ad_device_name WHEN 'desktop' THEN ad_impressions END) as desk_imprsn ,SUM(CASE ad_device_name WHEN 'mobile' THEN ad_clicks END) as mob_click, SUM(CASE ad_device_name WHEN 'desktop' THEN ad_clicks END) as desk_click  FROM `{$wpdb->prefix}quads_stats` WHERE `ad_thetime` = %s",array(strtotime($todays_date))));
					wp_cache_set('quads_stats_today_imprsn_clicks_all_' . $todays_date , $results_impresn_t_2, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_desk = wp_cache_get('quads_stats_today_imprsn_desktop_all_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_impresn_desk) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date = %d",array(strtotime($todays_date))));
					wp_cache_set('quads_stats_today_imprsn_desktop_all_' . $todays_date , $results_impresn_desk, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_mob = wp_cache_get('quads_stats_today_imprsn_mobile_all_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_impresn_mob) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  desk_imprsn FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date = %d",array(strtotime($todays_date))));
					wp_cache_set('quads_stats_today_imprsn_mobile_all_' . $todays_date , $results_impresn_mob, 'quick-adsense-reloaded', 3600);
				}
				$results_clicks_desk = wp_cache_get('quads_stats_today_clicks_desktop_all_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_clicks_desk) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_clicks_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  desk_click FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date = %d",array(strtotime($todays_date))));
					wp_cache_set('quads_stats_today_clicks_desktop_all_' . $todays_date , $results_clicks_desk, 'quick-adsense-reloaded', 3600);
				}
				$results_clicks_mob = wp_cache_get('quads_stats_today_clicks_mobile_all_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_clicks_mob) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_clicks_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0)as   mob_click FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date = %d",array(strtotime($todays_date))));
					wp_cache_set('quads_stats_today_clicks_mobile_all_' . $todays_date , $results_clicks_mob, 'quick-adsense-reloaded', 3600);
				}
				foreach($results_impresn_t_2 as $key=>$value){
					if($results_impresn_desk){
						$results_impresn_t_2[$key]->desk_impr = $value->desk_impr+$results_impresn_desk;
					}
	
					if($results_impresn_mob){
						$results_impresn_t_2[$key]->mob_impr = $value->mob_impr+$results_impresn_mob;
					}
	
					if($results_clicks_desk){
						$results_impresn_t_2[$key]->desk_click = $value->desk_click+$results_clicks_desk;
					}
					if($results_clicks_mob){
						$results_impresn_t_2[$key]->mob_click = $value->mob_click+$results_clicks_mob;
					}
				}
			}
			else{
				$results_impresn_t_2 = wp_cache_get('quads_stats_today_imprsn_clicks_' . $ad_id . '_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_impresn_t_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_t_2 = $wpdb->get_results($wpdb->prepare("SELECT  SUM(CASE ad_device_name WHEN 'mobile' THEN ad_impressions END) as mob_imprsn, SUM(CASE ad_device_name WHEN 'desktop' THEN ad_impressions END) as desk_imprsn ,SUM(CASE ad_device_name WHEN 'mobile' THEN ad_clicks END) as mob_click, SUM(CASE ad_device_name WHEN 'desktop' THEN ad_clicks END) as desk_click  FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d AND `ad_thetime` = %s",array( $ad_id, strtotime($todays_date))));
					wp_cache_set('quads_stats_today_imprsn_clicks_' . $ad_id . '_' . $todays_date , $results_impresn_t_2, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_desk = wp_cache_get('quads_stats_today_imprsn_desktop_' . $ad_id . '_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_impresn_desk) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date = %d AND ad_id =%d",array(strtotime($todays_date),$ad_id)));
					wp_cache_set('quads_stats_today_imprsn_desktop_' . $ad_id . '_' . $todays_date , $results_impresn_desk, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_mob = wp_cache_get('quads_stats_today_imprsn_mobile_' . $ad_id . '_' . $todays_date , 'quick-adsense-reloaded');
				if (false === $results_impresn_mob) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  desk_imprsn FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date = %d AND ad_id =%d",array(strtotime($todays_date),$ad_id)));
					wp_cache_set('quads_stats_today_imprsn_mobile_' . $ad_id . '_' . $todays_date , $results_impresn_mob, 'quick-adsense-reloaded', 3600);
				}
			$results_clicks_desk = wp_cache_get('quads_stats_today_clicks_desktop_' . $ad_id . '_' . $todays_date , 'quick-adsense-reloaded');
			if (false === $results_clicks_desk) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  desk_click FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date = %d AND ad_id =%d",array(strtotime($todays_date),$ad_id)));
				wp_cache_set('quads_stats_today_clicks_desktop_' . $ad_id . '_' . $todays_date , $results_clicks_desk, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_mob = wp_cache_get('quads_stats_today_clicks_mobile_' . $ad_id . '_' . $todays_date , 'quick-adsense-reloaded');
			if (false === $results_clicks_mob) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0)as  mob_click FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date = %d AND ad_id =%d",array(strtotime($todays_date),$ad_id)));
				wp_cache_set('quads_stats_today_clicks_mobile_' . $ad_id . '_' . $todays_date , $results_clicks_mob, 'quick-adsense-reloaded', 3600);
			}
			foreach($results_impresn_t_2 as $key=>$value){
				if($results_impresn_desk){
					$results_impresn_t_2[$key]->desk_impr = $value->desk_impr+$results_impresn_desk;
				}

				if($results_impresn_mob){
					$results_impresn_t_2[$key]->mob_impr = $value->mob_impr+$results_impresn_mob;
				}

				if($results_clicks_desk){
					$results_impresn_t_2[$key]->desk_click = $value->desk_click+$results_clicks_desk;
				}
				if($results_clicks_mob){
					$results_impresn_t_2[$key]->mob_click = $value->mob_click+$results_clicks_mob;
				}
			}
			}
				$array_i_t = array_values($results_impresn_t_2);
				$array_i_t = reset($array_i_t);
				
				$ad_mob_imprsn = isset($array_i_t->mob_imprsn)?intval($array_i_t->mob_imprsn):0;		
				$ad_desk_imprsn = isset($array_i_t->desk_imprsn)?intval($array_i_t->desk_imprsn):0;		
				$ad_imprsn = $ad_mob_imprsn + $ad_desk_imprsn;		
				$ad_desk_clicks = isset($array_i_t->desk_click)?intval($array_i_t->desk_click):0;
				$ad_mob_clicks = isset($array_i_t->mob_click)?intval($array_i_t->mob_click):0;
				$ad_clicks = $ad_mob_clicks+$ad_desk_clicks;
			

			$mob_indi_impr_day_counts=$ad_mob_imprsn;
			$desk_indi_impr_day_counts=$ad_desk_imprsn;
			$individual_impr_day_counts=$ad_imprsn;
			$ad_mob_imp_individual_dates=$get_impressions_specific_dates;
			$ad_desk_imp_individual_dates=$get_impressions_specific_dates;
			$mob_indi_click_day_counts=$ad_mob_clicks;
			$desk_indi_click_day_counts=$ad_desk_clicks;
			$individual_click_day_counts=$ad_clicks;
			$individual_ad_dates=$get_impressions_specific_dates;

	}
	else if( $day == "custom" ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
		if( isset($_GET['fromdate'] ) ) {
		    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		    $fromdate = sanitize_text_field( $_GET['fromdate'] );
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
		if(isset($_GET['todate'])){
		    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		    $todate = sanitize_text_field($_GET['todate']);
		}
		$get_from = preg_replace('/(.*?)-(.*?)-(.*?)T(.*)/', '$1-$2-$3', $fromdate);
		$get_to = preg_replace('/(.*?)-(.*?)-(.*?)T(.*)/', '$1-$2-$3', $todate);
		if($ad_id=="all")
			{
				$results_impresn_C_ = wp_cache_get('quads_stats_custom_imprsn_all_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_C_) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_C_ = $wpdb->get_results($wpdb->prepare(" SELECT ad_date, date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ",array($get_from,$get_to)));
					wp_cache_set('quads_stats_custom_imprsn_all_' . $get_from . '_' . $get_to , $results_impresn_C_, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_desk_1 = wp_cache_get('quads_stats_custom_imprsn_desktop_all_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %s AND %s GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to))));
					wp_cache_set('quads_stats_custom_imprsn_desktop_all_' . $get_from . '_' . $get_to , $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_desk_1){ $results_impresn_C_ = array_merge($results_impresn_C_,$results_impresn_desk_1 ); }
				$results_impresn_mob_1 = wp_cache_get('quads_stats_custom_imprsn_mobile_all_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %s AND %s GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to))));
					wp_cache_set('quads_stats_custom_imprsn_mobile_all_' . $get_from . '_' . $get_to , $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_mob_1){ $results_impresn_C_ = array_merge($results_impresn_C_,$results_impresn_mob_1 ); }
				$results_impresn_C_2 = wp_cache_get('quads_stats_custom_imprsn_all_2_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_C_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_C_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_thetime, IFNULL(SUM(ad_impressions),0) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name",array(strtotime($get_from),strtotime($get_to))));
					wp_cache_set('quads_stats_custom_imprsn_all_2_' . $get_from . '_' . $get_to , $results_impresn_C_2, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_desk_2 = wp_cache_get('quads_stats_custom_imprsn_desktop_all_2_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as impressions,stats_date as ad_thetime , 'desktop' as ad_device_name  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %s AND %s GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to))));
					wp_cache_set('quads_stats_custom_imprsn_desktop_all_2_' . $get_from . '_' . $get_to , $results_impresn_desk_2, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_desk_2){ $results_impresn_C_2 = array_merge($results_impresn_C_2,$results_impresn_desk_2 ); }
				$results_impresn_mob_2 = wp_cache_get('quads_stats_custom_imprsn_mobile_all_2_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as impressions,stats_date as ad_thetime , 'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %s AND %s GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to))));
					wp_cache_set('quads_stats_custom_imprsn_mobile_all_2_' . $get_from . '_' . $get_to , $results_impresn_mob_2, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_mob_2){ $results_impresn_C_2 = array_merge($results_impresn_C_2,$results_impresn_mob_2 ); }

				foreach ($results_impresn_C_ as $key => $value) {
						foreach ($results_impresn_C_2 as $key2 => $value2) {
							if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								if(!isset($value->mob_imprsn)){
									$value->mob_imprsn = 0;
								}
							}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
							}
						}
				}

			}
			else
			{
				$results_impresn_C_ = wp_cache_get('quads_stats_custom_imprsn_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_C_) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_C_ = $wpdb->get_results($wpdb->prepare(" SELECT ad_date, date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ",array($get_from,$get_to,$ad_id)));
					wp_cache_set('quads_stats_custom_imprsn_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_impresn_C_, 'quick-adsense-reloaded', 3600);
				}
				$results_impresn_desk_1 = wp_cache_get('quads_stats_custom_imprsn_desktop_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %s AND %s AND `ad_id` = %d GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to),$ad_id)));
					wp_cache_set('quads_stats_custom_imprsn_desktop_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_impresn_desk_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_desk_1){ $results_impresn_C_ = array_merge($results_impresn_C_,$results_impresn_desk_1 ); }
				$results_impresn_mob_1 = wp_cache_get('quads_stats_custom_imprsn_mobile_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_1) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as date_impression,DATE(FROM_UNIXTIME(stats_date)) as ad_date  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %s AND %s AND `ad_id` = %d GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to),$ad_id)));
					wp_cache_set('quads_stats_custom_imprsn_mobile_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_impresn_mob_1, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_mob_1){ $results_impresn_C_ = array_merge($results_impresn_C_,$results_impresn_mob_1 ); }
				$results_impresn_C_2 = wp_cache_get('quads_stats_custom_imprsn_2_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_C_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_C_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_thetime, IFNULL(SUM(ad_impressions),0) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name",array(strtotime($get_from),strtotime($get_to),$ad_id)));
					wp_cache_set('quads_stats_custom_imprsn_2_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_impresn_C_2, 'quick-adsense-reloaded', 3600);
				
				}
				$results_impresn_desk_2 = wp_cache_get('quads_stats_custom_imprsn_desk_2_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_desk_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as impressions,stats_date as ad_thetime , 'desktop' as ad_device_name  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %s AND %s AND `ad_id` = %d GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to),$ad_id)));
					wp_cache_set('quads_stats_custom_imprsn_desk_2_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_impresn_desk_2, 'quick-adsense-reloaded', 3600);
				}
				if($results_impresn_desk_2){ $results_impresn_C_2 = array_merge($results_impresn_C_2,$results_impresn_desk_2 ); }
				
				$results_impresn_mob_2 = wp_cache_get('quads_stats_custom_imprsn_mob_2_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_impresn_mob_2) {
					//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_impresn_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as impressions,stats_date as ad_thetime , 'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %s AND %s AND `ad_id` = %d  GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to),$ad_id)));
					wp_cache_set('quads_stats_custom_imprsn_mob_2_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_impresn_mob_2, 'quick-adsense-reloaded', 3600);
				}	
				if($results_impresn_mob_2){ $results_impresn_C_2 = array_merge($results_impresn_C_2,$results_impresn_mob_2 ); }


				foreach ($results_impresn_C_ as $key => $value) {
						foreach ($results_impresn_C_2 as $key2 => $value2) {
							if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								if(!isset($value->mob_imprsn)){
									$value->mob_imprsn = 0;
								}
								
							}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
							}
						}
				}
			}
		
		$array_i_c = array_values($results_impresn_C_);
		$ad_mob_imprsn = $ad_desk_imprsn = $ad_imprsn = 0;		
		$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = '';		
		foreach ($array_i_c as $key => $value) {
			$ad_mob_imprsn += $value->mob_imprsn;
			$ad_desk_imprsn += $value->desk_imprsn;
			$ad_imprsn += $value->date_impression;
				$ad_mob_imprsn_values .= $value->mob_imprsn.',';
				$ad_desk_imprsn_values .= $value->desk_imprsn.',';
				$ad_imprsn_values .= ($value->mob_imprsn+$value->desk_imprsn).',';
			}
			
		$remove_mob_comma = substr($ad_mob_imprsn_values, 0, -1);
		$remove_desk_comma = substr($ad_desk_imprsn_values, 0, -1);
		$remove_comma = substr($ad_imprsn_values, 0, -1);
		$mob_indi_impr_day_counts = explode(",",$remove_mob_comma);
		$desk_indi_impr_day_counts = explode(",",$remove_desk_comma);
		$individual_impr_day_counts = explode(",",$remove_comma);

		$period = new DatePeriod(new DateTime(''.$get_from.''), new DateInterval('P1D'), new DateTime(''.$get_to.''.' +1 day'));
		$dates_i_chart = '';
    foreach ($period as $date) {
        $dates_i_chart .= $date->format("Y-m-d").',';
    }
	
	$remove_comma_d = substr($dates_i_chart, 0, -1);
		$_to_slash = explode(",",$remove_comma_d);
		$get_impressions_specific_dates = str_replace('-','/',$_to_slash);
		if($ad_id=="all")
		{
			$results_click_S = wp_cache_get('quads_stats_custom_clicks_all_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
			if (false === $results_click_S) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_S = $wpdb->get_results($wpdb->prepare(" SELECT ad_date, date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ",array($get_from,$get_to)));
				wp_cache_set('quads_stats_custom_clicks_all_' . $get_from . '_' . $get_to , $results_click_S, 'quick-adsense-reloaded', 3600);
			}
			$results_click_S_2 = wp_cache_get('quads_stats_custom_clicks_all_2_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
			if (false === $results_click_S_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_S_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_thetime, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name",array(strtotime($get_from),strtotime($get_to))));
				wp_cache_set('quads_stats_custom_clicks_all_2_' . $get_from . '_' . $get_to , $results_click_S_2, 'quick-adsense-reloaded', 3600);
			}
			
			$results_clicks_desk_1 = wp_cache_get('quads_stats_custom_clicks_desktop_all_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %s AND %s GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to))));
				wp_cache_set('quads_stats_custom_clicks_desktop_all_' . $get_from . '_' . $get_to , $results_clicks_desk_1, 'quick-adsense-reloaded', 3600);
			}
		if($results_clicks_desk_1 ) { $results_click_S  = array_merge($results_click_S ,$results_clicks_desk_1); }
		$results_clicks_mob_1 = wp_cache_get('quads_stats_custom_clicks_mobile_all_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
		if (false === $results_clicks_mob_1) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$results_clicks_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %s AND %s GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to))));
			wp_cache_set('quads_stats_custom_clicks_mobile_all_' . $get_from . '_' . $get_to , $results_clicks_mob_1, 'quick-adsense-reloaded', 3600);
		}
		if($results_clicks_mob_1 ) { $results_click_S  = array_merge($results_click_S ,$results_clicks_mob_1); }
		$results_click_S_2 = wp_cache_get('quads_stats_custom_clicks_all_2_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
		if (false === $results_click_S_2) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$results_click_S_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_thetime, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name",array(strtotime($get_from),strtotime($get_to))));
			wp_cache_set('quads_stats_custom_clicks_all_2_' . $get_from . '_' . $get_to , $results_click_S_2, 'quick-adsense-reloaded', 3600);
		}
		$results_clicks_desk_2 = wp_cache_get('quads_stats_custom_clicks_desktop_all_2_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
		if (false === $results_clicks_desk_2) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$results_clicks_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as clicks,stats_date as ad_thetime , 'desktop' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %s AND %s GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to))));
			wp_cache_set('quads_stats_custom_clicks_desktop_all_2_' . $get_from . '_' . $get_to , $results_clicks_desk_2, 'quick-adsense-reloaded', 3600);
		}
		if($results_clicks_desk_2 ) { $results_click_S_2  = array_merge($results_click_S_2 ,$results_clicks_desk_2); }
		$results_clicks_mob_2 = wp_cache_get('quads_stats_custom_clicks_mobile_all_2_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
		if (false === $results_clicks_mob_2) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$results_clicks_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as clicks,stats_date as ad_thetime , 'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %s AND %s GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to))));
			wp_cache_set('quads_stats_custom_clicks_mobile_all_2_' . $get_from . '_' . $get_to , $results_clicks_mob_2, 'quick-adsense-reloaded', 3600);
		}
		if($results_clicks_mob_2 ) { $results_click_S_2  = array_merge($results_click_S_2 ,$results_clicks_mob_2); }

			foreach ($results_click_S as $key => $value) {
					foreach ($results_click_S_2 as $key2 => $value2) {
						if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
							$value->desk_click = $value2->clicks;
							if(!isset($value->mob_click)){
								$value->mob_click = 0;
							}
						}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
							$value->mob_click = $value2->clicks;
						}
					}
			}

		}
		else
		{
			$results_click_S = wp_cache_get('quads_stats_custom_clicks_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
			if (false === $results_click_S) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_S = $wpdb->get_results($wpdb->prepare(" SELECT ad_date, date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ",array($get_from,$get_to,$ad_id)));
				wp_cache_set('quads_stats_custom_clicks_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_click_S, 'quick-adsense-reloaded', 3600);
			}
			$results_click_S_2 = wp_cache_get('quads_stats_custom_clicks_2_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
			if (false === $results_click_S_2) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_click_S_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_thetime, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name",array(strtotime($get_from),strtotime($get_to),$ad_id)));
				wp_cache_set('quads_stats_custom_clicks_2_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_click_S_2, 'quick-adsense-reloaded', 3600);
			}
			$results_clicks_desk_1 = wp_cache_get('quads_stats_custom_clicks_desktop_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
			if (false === $results_clicks_desk_1) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results_clicks_desk_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %s AND %s AND `ad_id`=%d GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to),$ad_id)));
				wp_cache_set('quads_stats_custom_clicks_desktop_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_clicks_desk_1, 'quick-adsense-reloaded', 3600);
			}
		 if($results_clicks_desk_1 ) { $results_click_S  = array_merge($results_click_S ,$results_clicks_desk_1); }
		$results_clicks_mob_1 = wp_cache_get('quads_stats_custom_clicks_mobile_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
		if (false === $results_clicks_mob_1) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$results_clicks_mob_1 = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as date_click,DATE(FROM_UNIXTIME(stats_date)) as ad_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %s AND %s AND `ad_id`=%d GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to),$ad_id)));
			wp_cache_set('quads_stats_custom_clicks_mobile_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_clicks_mob_1, 'quick-adsense-reloaded', 3600);
		}
		if($results_clicks_mob_1 ) { $results_click_S  = array_merge($results_click_S ,$results_clicks_mob_1); }
		$results_clicks_desk_2 = wp_cache_get('quads_stats_custom_clicks_desktop_2_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
		if (false === $results_clicks_desk_2) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$results_clicks_desk_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as clicks,stats_date as ad_thetime , 'desktop' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %s AND %s AND `ad_id`=%d GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to),$ad_id)));
			wp_cache_set('quads_stats_custom_clicks_desktop_2_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_clicks_desk_2, 'quick-adsense-reloaded', 3600);
		}
		if($results_clicks_desk_2 ) { $results_click_S_2  = array_merge($results_click_S_2 ,$results_clicks_desk_2); }
		$results_clicks_mob_2 = wp_cache_get('quads_stats_custom_clicks_mobile_2_' . $ad_id . '_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
		if (false === $results_clicks_mob_2) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$results_clicks_mob_2 = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as clicks,stats_date as ad_thetime , 'mobile' as ad_device_name FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %s AND %s AND `ad_id`=%d GROUP BY stats_date",array(strtotime($get_from),strtotime($get_to),$ad_id)));
			wp_cache_set('quads_stats_custom_clicks_mobile_2_' . $ad_id . '_' . $get_from . '_' . $get_to , $results_clicks_mob_2, 'quick-adsense-reloaded', 3600);
		}
		if($results_clicks_mob_2 ) { $results_click_S_2  = array_merge($results_click_S_2 ,$results_clicks_mob_2); }


		 foreach ($results_click_S as $key => $value) {
			foreach ($results_click_S_2 as $key2 => $value2) {
				if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
					$value->desk_click = $value2->clicks;
						if(!isset($value->mob_click)){
						$value->mob_click = 0;
					}
				}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
					$value->mob_click = $value2->clicks;
				}
			}
		 }

		}
		
		$array_c = array_values($results_click_S);
		$ad_mob_clicks = $ad_desk_clicks = $ad_clicks = 0;
		$ad_mob_click_values = $ad_desk_click_values = $ad_click_values = '';

		foreach ($array_c as $key => $value) {
			$ad_mob_clicks += $value->mob_click;
			$ad_desk_clicks += $value->desk_click;
			$ad_clicks += $value->date_click;
			$ad_mob_click_values .= $value->mob_click.',';
			$ad_desk_click_values .= $value->desk_click.',';
			$ad_click_values .= ($value->mob_click+$value->desk_click).',';
		}
		$remove_mob_comma_click = substr($ad_mob_click_values, 0, -1);
		$remove_desk_comma_click = substr($ad_desk_click_values, 0, -1);
		$remove_comma_click = substr($ad_click_values, 0, -1);
		$mob_indi_click_day_counts = explode(",",$remove_mob_comma_click);
		$desk_indi_click_day_counts = explode(",",$remove_desk_comma_click);
		$individual_click_day_counts = explode(",",$remove_comma_click);
		
		if($ad_id=="all")
			{
				$results_top5 = wp_cache_get('quads_stats_top5_ads_all_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				$results_top5_2 = wp_cache_get('quads_stats_top5_ads_all_2_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $results_top5) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id   WHERE `{$wpdb->prefix}quads_single_stats_`.`ad_date` BETWEEN %s AND %s  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($get_from,$get_to,5)));
					wp_cache_set('quads_stats_top5_ads_all_' . $get_from . '_' . $get_to , $results_top5, 'quick-adsense-reloaded', 3600);
				}
				if (false === $results_top5_2) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, IFNULL(SUM(CASE WHEN ad_device_name = 'mobile' THEN ad_impressions END),0) AS mob_impr , IFNULL(SUM(CASE WHEN ad_device_name = 'desktop' THEN ad_impressions END),0) AS desk_impr,IFNULL(SUM(CASE WHEN ad_device_name = 'mobile' THEN ad_clicks END),0) AS mob_clks , IFNULL(SUM(CASE WHEN ad_device_name = 'desktop' THEN ad_clicks END),0) AS desk_clks FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",array(strtotime($get_from),strtotime($get_to))));
					wp_cache_set('quads_stats_top5_ads_all_2_' . $get_from . '_' . $get_to , $results_top5_2, 'quick-adsense-reloaded', 3600);
				}
				$total_click=[0,0,0,0,0];
				$total_impression=[0,0,0,0,0];
				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID){
								$value->desk_imprsn = $value2->desk_impr;
								$value->desk_clicks = $value2->desk_clks;
								$value->mob_imprsn = $value2->mob_impr;
								$value->mob_clicks = $value2->mob_clks;
								$total_click[$key] = $total_click[$key]+$value2->desk_clks+$value2->mob_clks;
								$total_impression[$key] = $total_impression[$key]+$value2->desk_impr+$value2->mob_impr;

							}
						}

						$results_top5[$key]->total_click = $total_click[$key];
						$results_top5[$key]->total_impression = $total_impression[$key];
				}
				
				$array_top5 = array_values($results_top5);	

				$array_top_clicks = wp_cache_get('quads_stats_top_clicks_all_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				$array_top_imprs = wp_cache_get('quads_stats_top_imprs_all_' . $get_from . '_' . $get_to , 'quick-adsense-reloaded');
				if (false === $array_top_clicks) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$array_top_clicks = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks ,IFNULL(SUM(click_mob.stats_clicks),0)as mob_clicks , SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date BETWEEN %d AND %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_desk.stats_date BETWEEN %d AND %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC
					LIMIT 5;",array(strtotime($get_from),strtotime($get_to),strtotime($get_from),strtotime($get_to))));
					wp_cache_set('quads_stats_top_clicks_all_' . $get_from . '_' . $get_to , $array_top_clicks, 'quick-adsense-reloaded', 3600);
				}
				if (false === $array_top_imprs) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$array_top_imprs = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(impr_desk.stats_impressions),0)as desk_imprsn ,IFNULL(SUM(impr_mob.stats_impressions),0)as mob_imprsn , SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date BETWEEN %d AND %d
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date BETWEEN %d AND %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_impression DESC
					LIMIT 5;",array(strtotime($get_from),strtotime($get_to),strtotime($get_from),strtotime($get_to))));
					wp_cache_set('quads_stats_top_imprs_all_' . $get_from . '_' . $get_to , $array_top_imprs, 'quick-adsense-reloaded', 3600);
				}
				$array_top_imprs_ = $array_top_imprs;
				foreach($array_top_clicks as $key=>$value){
					foreach($array_top_imprs as $key2=>$value2){
					  if($value->ID == $value2->ID){
						$value->desk_imprsn = $value2->desk_imprsn;
						$value->total_impression = $value2->total_impression;
						$value->mob_imprsn = $value2->mob_imprsn;
						unset($array_top_imprs_[$key]);
					  }
					}
				}

				foreach($array_top_imprs_ as $key=>$value){
						$value->desk_clicks = 0;
						$value->total_click = 0;
						$value->mob_click = 0;
				}
				$array_top_clicks =  array_merge($array_top_clicks, $array_top_imprs_);
				$array_top5_ =$array_top_clicks = array_slice($array_top_clicks, 0, 5);
	
		
				if(!empty($array_top_clicks)){
					foreach($array_top5 as $key => $value){
						foreach($array_top_clicks as $key2=>$value2){
							if($value->ID == $value2->ID){

								$array_top5[$key]->total_click = $value->total_click+$value2->total_click;
								$array_top5[$key]->desk_clicks = $value->desk_clicks+$value2->desk_clicks;
								$array_top5[$key]->desk_imprsn = $value->desk_imprsn+$value2->desk_imprsn;
								$array_top5[$key]->mob_clicks = $value->mob_clicks+$value2->mob_clicks;
								$array_top5[$key]->mob_imprsn = $value->mob_imprsn+$value2->mob_imprsn;
								$array_top5[$key]->total_impression = $value->total_impression+$value2->total_impression;
								unset($array_top5_[$key2]);
							}
						}
					}
					$array_top5 =  array_merge($array_top5,$array_top5_);
					$array_top5= array_slice($array_top5, 0, 5);
			 }
			}
		
	}
			
	  $ad_stats['mob_impressions'] = $ad_mob_imprsn;
	  $ad_stats['desk_impressions'] = $ad_desk_imprsn;
	  $ad_stats['impressions'] = $ad_imprsn;
      $ad_stats['mob_clicks']  = $ad_mob_clicks;
      $ad_stats['desk_clicks'] = $ad_desk_clicks;
      $ad_stats['clicks']      = $ad_clicks;
      $ad_stats['ad_day']      = $day;
      $ad_stats['mob_indi_impr_day_counts']  = $mob_indi_impr_day_counts;
      $ad_stats['desk_indi_impr_day_counts']  = $desk_indi_impr_day_counts;
      $ad_stats['individual_impr_day_counts']  = $individual_impr_day_counts;
      $ad_stats['ad_mob_imp_individual_dates']  = $get_mob_impr_specific_dates;
      $ad_stats['ad_desk_imp_individual_dates']  = $get_desk_impr_specific_dates;
      $ad_stats['ad_imp_individual_dates']  = $get_impressions_specific_dates;
      $ad_stats['mob_indi_click_day_counts']  = $mob_indi_click_day_counts;
      $ad_stats['desk_indi_click_day_counts']  = $desk_indi_click_day_counts;
      $ad_stats['individual_click_day_counts']  = $individual_click_day_counts;
      $ad_stats['individual_ad_dates']  = $individual_ad_dates;
	  $ad_stats['top5_ads']  = $array_top5;
	  return $ad_stats;
                                    
}

function quads_adsense_get_access_token($account){
	$options = quads_get_option_adsense();

		if ( isset( $options['accounts'][""] ) ) {
			if ( time() > $options['accounts'][""]['expires'] ) {
			$new_tokens = quads_adsense_renew_access_token( $account );
			if ( $new_tokens['status'] ) {
				return $new_tokens['access_token'];
			} else {
				// return all error info [arr]
				return $new_tokens;
			}
		} else {
			return $options['accounts'][""]['access_token'];
		}

	} else {
		// Account does not exists.
		if ( ! empty( $options['accounts'] ) ) {
			// There is another account connected.
			return array(
				'status' => false,
				'msg' => esc_html__( 'It seems that some changes have been made in the Quads Ads settings. Please refresh this page.', 'quick-adsense-reloaded' ),
				'reload' => true,
			);
		} else {
			// No account at all.
			return array(
				'status' => false,
				'msg' => wp_kses( sprintf( /* translators: %s: account name */  __( 'Advanced Ads does not have access to your account (<code>%s</code>) anymore.', 'quick-adsense-reloaded' ), $account ), array( 'code' => true ) ),
				'reload' => true,
			);
		}
	}
}
function quads_adsense_renew_access_token( $account ) {

	$options       = quads_get_option_adsense();
	$access_token  = $options['accounts'][""]['access_token'];
	$refresh_token = $options['accounts'][""]['refresh_token'];

	$url  = 'https://www.googleapis.com/oauth2/v4/token';
	$args = array(
		'body' => array(
			'refresh_token' => $refresh_token,
			'client_id'     => client_id,
			'client_secret' => client_secret,
			'grant_type'    => 'refresh_token',
		),
	);

	$response = wp_remote_post( $url, $args );

	if ( is_wp_error( $response ) ) {
		return array(
			'status' => false,
			'msg'    => sprintf( /* translators: %s: account name */ esc_html__( 'error while renewing access token for "%s"', 'quick-adsense-reloaded' ), $account ),
			'raw'    => $response->get_error_message(),
		);
	} else {
		$tokens = json_decode( $response['body'], true );
		//  checking for the $tokens is not enough. it can be empty.
		//  monitored this, when the access token is revoked from the outside
		//  this can happen, when the user connects from another domain.
		if ( null !== $tokens && isset($tokens['expires_in']) ) {
			$expires = time() + absint( $tokens['expires_in'] );

			$options['accounts'][ $account ]['access_token'] = $tokens['access_token'];
			$options['accounts'][ $account ]['expires']      = $expires;

			update_option( 'quads_adsense_api_data', $options );
			return array(
				'status'       => true,
				'access_token' => $tokens['access_token'],
			);
		} else {
			return array(
				'status' => false,
				'msg'    => sprintf( /* translators: %s: account name */ esc_html__( 'invalid response received while renewing access token for "%s"', 'quick-adsense-reloaded' ),  $account ),
				'raw'    => $response['body'],
			);
		}
	}
}
function quads_load_adsnese_scripts($hook){
	if($hook!=='toplevel_page_quads-settings'){ return ; }

	$js_dir  = QUADS_PLUGIN_URL . 'assets/js/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( quadsIsDebugMode() ) ? '' : '.min';
	wp_enqueue_script( 'quads-admin-adsense', $js_dir . 'connect-adsense' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );

	$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?scope=' .
	            urlencode( 'https://www.googleapis.com/auth/adsense.readonly' ) .
	            '&client_id=' . client_id .
	            '&redirect_uri=' . urlencode( 'urn:ietf:wg:oauth:2.0:oob' ) .
	            '&access_type=offline&include_granted_scopes=true&prompt=select_account&response_type=code';

	wp_localize_script( 'quads-admin-adsense', 'quads_adsense', array(
		'auth_url' => $auth_url
	) );

	// ab testing js
	wp_enqueue_script( 'quads-admin-abtesting', $js_dir . 'abtesting-reports' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
	// chart js
	wp_enqueue_script( 'quads_charts_js', $js_dir . 'Chart' . $suffix . '.js', array('jquery'), QUADS_VERSION, false );
	wp_enqueue_script( 'quads_charts_js' );
}

/**
 * Here, We get date in unix format as per condition
 * @param type $type
 * @return type string
 */
function quads_get_date($type) {
    	
	switch($type) {
		
		case 'day' :
			$timezone = get_option('timezone_string');
			if($timezone) {
				$server_timezone = gmdate('e');
				// phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
				date_default_timezone_set($timezone);
				$result = strtotime('00:00:00') + (get_option('gmt_offset') * 3600);
				// phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
				date_default_timezone_set($server_timezone);
			} else {
				$result = gmdate('U', gmmktime(0, 0, 0, gmdate('n'), gmdate('j')));
			}
		break;
				
	}

	return $result;
}

/**
 * Here, We fetch ads stats from database table as per condition in query
 * @global type $wpdb
 * @param type $condition
 * @param type $ad_id
 * @param type $date
 * @return type array
 */
function quads_get_ad_stats($condition, $ad_id='', $date=null,$parameters ='') {
    
    global $wpdb;
    $ad_stats = array();
    switch ($condition) {
        
        case 'sumofstats':
			$result = wp_cache_get('quads_ad_stats_sumofstats_' . $ad_id , 'quick-adsense-reloaded');
			if (false === $result) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$result = $wpdb->get_results($wpdb->prepare("SELECT SUM(`ad_clicks`) as `clicks`, SUM(`ad_impressions`) as `impressions` FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d;", $ad_id), ARRAY_A);
				wp_cache_set('quads_ad_stats_sumofstats_' . $ad_id , $result, 'quick-adsense-reloaded', 3600);
			}
			$desk_clicks = wp_cache_get('quads_ad_stats_sumofstats_desktop_clicks_' . $ad_id , 'quick-adsense-reloaded');
			if (false === $desk_clicks) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$desk_clicks = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(`stats_clicks`),0) as `clicks` FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE `ad_id` = %d;", $ad_id));
				wp_cache_set('quads_ad_stats_sumofstats_desktop_clicks_' . $ad_id , $desk_clicks, 'quick-adsense-reloaded', 3600);
			}
			$mob_clicks = wp_cache_get('quads_ad_stats_sumofstats_mobile_clicks_' . $ad_id , 'quick-adsense-reloaded');
			if (false === $mob_clicks) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$mob_clicks = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(`stats_clicks`),0) as `clicks` FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE `ad_id` = %d;", $ad_id));
				wp_cache_set('quads_ad_stats_sumofstats_mobile_clicks_' . $ad_id , $mob_clicks, 'quick-adsense-reloaded', 3600);
			}
			 $desk_impres = wp_cache_get('quads_ad_stats_sumofstats_desktop_impressions_' . $ad_id , 'quick-adsense-reloaded');
			if (false === $desk_impres) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$desk_impres = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(`stats_impressions`),0) as `impressions` FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE `ad_id` = %d;", $ad_id));
				wp_cache_set('quads_ad_stats_sumofstats_desktop_impressions_' . $ad_id , $desk_impres, 'quick-adsense-reloaded', 3600);
			}
			$mob_impres = wp_cache_get('quads_ad_stats_sumofstats_mobile_impressions_' . $ad_id , 'quick-adsense-reloaded');
			if (false === $mob_impres) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$mob_impres = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(`stats_impressions`),0) as `impressions` FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE `ad_id` = %d;", $ad_id));
				wp_cache_set('quads_ad_stats_sumofstats_mobile_impressions_' . $ad_id , $mob_impres, 'quick-adsense-reloaded', 3600);
			}
           
            $ad_stats['impressions'] = $result[0]['impressions']+ $desk_impres+$mob_impres;
            $ad_stats['clicks']      = $result[0]['clicks']+$desk_clicks+$mob_clicks ;
                                    
            break;
        
        case 'fetchAllBy':

            
            if($ad_id){
				$result = wp_cache_get('quads_ad_stats_fetchAllBy_' . $ad_id . '_' . $date , 'quick-adsense-reloaded');
				if (false === $result) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$result = $wpdb->get_results($wpdb->prepare("SELECT *FROM `{$wpdb->prefix}quads_stats` WHERE `ad_thetime` = %d AND `ad_id` = %d;", $date, $ad_id), ARRAY_A);
					wp_cache_set('quads_ad_stats_fetchAllBy_' . $ad_id . '_' . $date , $result, 'quick-adsense-reloaded', 3600);
				}
            }else{
				$result = wp_cache_get('quads_ad_stats_fetchAllBy_all_' . $date , 'quick-adsense-reloaded');
				if (false === $result) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$result = $wpdb->get_results($wpdb->prepare("SELECT *FROM `{$wpdb->prefix}quads_stats` WHERE `ad_thetime` = %d;", $date), ARRAY_A);
					wp_cache_set('quads_ad_stats_fetchAllBy_all_' . $date , $result, 'quick-adsense-reloaded', 3600);
				}
            }                        
                
            if($result){
                                               
                foreach($result as $row){
                     
                    if($row['ad_device_name'] =='desktop'){
                        
                        if(isset($ad_stats['desktop']['click'])){
                            $ad_stats['desktop']['click']      +=  $row['ad_clicks'];
                        }else{
                            $ad_stats['desktop']['click']       =  $row['ad_clicks'];
                        }
                        
                        if(isset($ad_stats['desktop']['impression'])){
                            $ad_stats['desktop']['impression'] +=  $row['ad_impressions'];
                        }else{
                            $ad_stats['desktop']['impression'] =  $row['ad_impressions'];
                        }
                                                
                    }
                    if($row['ad_device_name'] =='mobile'){
                       
                        if(isset($ad_stats['mobile']['click'])){
                            $ad_stats['mobile']['click']      +=  $row['ad_clicks'];
                        }else{
                            $ad_stats['mobile']['click']       =  $row['ad_clicks'];
                        }
                        
                        if(isset($ad_stats['mobile']['impression'])){
                            $ad_stats['mobile']['impression'] +=  $row['ad_impressions'];   
                        }else{
                            $ad_stats['mobile']['impression']  =  $row['ad_impressions'];   
                        }
                                                
                    }
                    if($row['ad_device_name'] =='amp'){
                        
                        if(isset($ad_stats['amp']['click'])){
                            $ad_stats['amp']['click']         +=  $row['ad_clicks'];
                        }else{
                            $ad_stats['amp']['click']          =  $row['ad_clicks'];
                        }
                        
                        if(isset($ad_stats['amp']['impression'])){
                            $ad_stats['amp']['impression']    +=  $row['ad_impressions'];
                        }else{
                           $ad_stats['amp']['impression']      =  $row['ad_impressions']; 
                        }
                                                                     
                    }
                                        
                }
                
            }
            
            break;
			case 'search':
				$ad_thetime = '';
				$items_per_page = 20;
				$page = (isset($parameters['page'])&& !empty($parameters['page']))?$parameters['page'] :1;

				$offset = ($page - 1) * $items_per_page;

				if($parameters){
					if(isset($parameters['report_period'])){

						$endDate = strtotime('now');
						switch ($parameters['report_period']) {
							case 'last_15days':
								$startDate = strtotime("-14 day");
								break;
							case 'last_30days':
								$startDate = strtotime("-29 day");
								break;
							case 'last_6months':
								$startDate = strtotime("-6 month");
								break;
							case 'last_1year':
					
								$startDate = strtotime('-1 year');
								break;
							case 'custom':  
								$startDate = (isset($parameters['cust_fromdate'])&& !empty($parameters['cust_fromdate']))?strtotime($parameters['cust_fromdate']) :strtotime("now");
								$endDate = (isset($parameters['cust_todate'])&& !empty($parameters['cust_todate']))?strtotime($parameters['cust_todate']) :strtotime('now');
								break;
							case 'all_time':
								$startDate = strtotime('-3 year');
								break;
							default:
								$startDate = strtotime(" -6 day");
								break;
						}
						// phpcs:ignore  WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
						$ad_thetime = $wpdb->prepare('where ad_thetime BETWEEN %d AND %d',array($startDate,$endDate)); 
					
					}
				}
				$search_param = '';
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery
				if(isset($parameters['search_param']) && !empty($parameters['search_param'])){
					if(empty($ad_thetime)){
						// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery, WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder, WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnescapedLiteral, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
						$search_param = $wpdb->prepare("where ad_id  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or ad_device_name  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or ip_address  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or url  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or browser  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or referrer  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%'   "); 
					}else {
						// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery, WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder, WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnescapedLiteral, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
						$search_param = $wpdb->prepare("and ( ad_id  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or ad_device_name  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or ip_address  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or url  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or browser  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or referrer  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' )  "); 
					}

				}
				$results = wp_cache_get('quads_ad_stats_search_' . md5(serialize($parameters)), 'quick-adsense-reloaded');
				$result_total = wp_cache_get('quads_ad_stats_search_total_' . md5(serialize($parameters)) , 'quick-adsense-reloaded');
				if (false === $results) {
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter --Reason Escaping is already done above
					$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}quads_stats` ".$ad_thetime ." ".$search_param ." LIMIT %d, %d",array($offset,$items_per_page)), ARRAY_A);
					wp_cache_set('quads_ad_stats_search_' . md5(serialize($parameters)), $results, 'quick-adsense-reloaded', 3600);
				}
				if (false === $result_total) {
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter --Reason Escaping is already done above
					$result_total = $wpdb->get_row($wpdb->prepare("SELECT count(*) as total FROM `{$wpdb->prefix}quads_stats` ". $ad_thetime ." ".$search_param), ARRAY_A);
					wp_cache_set('quads_ad_stats_search_total_' . md5(serialize($parameters)) , $result_total, 'quick-adsense-reloaded', 3600);
				}
			
					$ad_stats = $results;	
					$log_array = array();


					if($parameters){
						if(isset($parameters['report_period'])){
		
							$endDate = strtotime('now');
							switch ($parameters['report_period']) {
								case 'last_15days':
									$startDate = strtotime("-14 day");
									break;
								case 'last_30days':
									$startDate = strtotime("-29 day");
									break;
								default:
									$startDate = strtotime(" -6 day");
									break;
							}
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$ad_thetime2 = $wpdb->prepare('WHERE log_date BETWEEN %d AND %d',array($startDate,$endDate)); 
						
						}
					}
					$search_param2 = '';
					if(isset($parameters['search_param']) && !empty($parameters['search_param'])){
						if(empty($ad_thetime2)){
							// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder, WordPress.DB.PreparedSQLPlaceholders.UnescapedLiteral
							$search_param = $wpdb->prepare("where ad_id  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or ip_address  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or log_url  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or browser  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or referrer  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%'   "); 
						}else {
						// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder,WordPress.DB.PreparedSQLPlaceholders.UnescapedLiteral
							$search_param = $wpdb->prepare("and ( ad_id  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or ip_address  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or log_url  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or browser  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or referrer  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' )  "); 
						}
					}
					$results2 = wp_cache_get('quads_ad_stats_search_logs_' . md5(serialize($parameters)), 'quick-adsense-reloaded');
					$result_total2 = wp_cache_get('quads_ad_stats_search_logs_total_' . md5(serialize($parameters)) , 'quick-adsense-reloaded');
					if (false === $results2) {
						// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter --Reason Escaping is already done above
						$results2 = $wpdb->get_results($wpdb->prepare("SELECT ad_id , log_date as ad_thetime,log_clicks ,ip_address,log_url as url,browser,referrer FROM `{$wpdb->prefix}quads_logs` ". $ad_thetime2 ." ".$search_param2." LIMIT %d, %d",array($offset,$items_per_page)), ARRAY_A);
						wp_cache_set('quads_ad_stats_search_logs_' . md5(serialize($parameters)), $results2, 'quick-adsense-reloaded', 3600);
					}
					if (false === $result_total2) {
						// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter --Reason Escaping is already done above
						$result_total2 = $wpdb->get_row($wpdb->prepare("SELECT count(*) as total FROM `{$wpdb->prefix}quads_logs` ". $ad_thetime2 ." ".$search_param2), ARRAY_A);
						wp_cache_set('quads_ad_stats_search_logs_total_' . md5(serialize($parameters)) , $result_total2, 'quick-adsense-reloaded', 3600);
					}
						
					$ad_stats2 = $results;
					$results = array_merge($results2,$results );
					$ad_stats = array_merge($ad_stats2,$ad_stats );
					$result_total['total'] = $result_total['total']+$result_total2['total'];
					foreach($results as $result){
						$ad_id = $result['ad_id'];
						
		$post_type      = get_post_meta($ad_id, 'ad_type', true);
		$post_label      = get_post_meta($ad_id, 'label', true);
		$result['ad_type'] = $post_type;

		$result['label'] = $post_label;
		$log_array[] = $result;

					}
					$response['posts_data']  = $log_array;
					$response['posts_found'] = ($result_total['total']);

					return $response;

				break;
        default:
            break;
    }            
    return $ad_stats;
}


function quadsSumArrays(array $a = [], array $b = []) {
	foreach ($a as $k => $v) {
		if(array_key_exists($k, $b)){

			if(is_array($v)){
				$b[$k] = sumArrays($b[$k],$v);
			}else{

				$b[$k]+=$v;
			}
		}else{
	
			$b[$k]=$v;
		}
	}
	return $b;
}

function quadsRemoveNullElement($val){
	$val = $val?$val:0;
	return $val;
}
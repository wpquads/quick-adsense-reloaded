<?php
if( !defined( 'ABSPATH' ) ) exit;
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
	register_rest_route( 'quads-adsense', 'import_old_db', array(
		'methods'    => 'POST',
		'callback'   => 'quads_adsense_import_old_db',
		'permission_callback' => '__return_true'
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
				'msg'    => 'error while submitting code',
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

function quads_adsense_get_report_abtesting_data(){
	global $wpdb;
	$results = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}quads_stats` ");
	if(!empty($results)) {    
    $quads_table = "<table id=\"blocked_id_table\">"; 
    $quads_table.= "<tbody>";
	$quads_table.= '<tr class="b_in_" style="font-weight: bold;">
	<td class="b_in_">ID</td>
	<td class="b_in_">Beginning Of Post</td>
	<td class="b_in_">End Of Post</td>
	<td class="b_in_">Middle Of Post</td>
	<td class="b_in_">After more Tag</td>
  </tr>';
    foreach($results as $row){   
    $userip = $row->ad_clicks;               
    $quads_table.= '
	<tr class="b_in">
                              <td class="b_in" >'.$row->id.'</td>
                              <td class="b_in">'.(isset($row->Beginning_of_post)?$row->Beginning_of_post:'').'</td>
                              <td class="b_in">'.(isset($row->End_of_post)?$row->End_of_post:'').'</td>
                              <td class="b_in">'.(isset($row->Middle_of_post)?$row->Middle_of_post:'').'</td>
                              <td class="b_in">'.(isset($row->After_more_tag)?$row->After_more_tag:'').'</td>
	</tr>
	'; 
    }
    $quads_table.= "</tbody>";
    $quads_table.= "</table>"; 
}
echo json_encode(
	array(
		'status'    => 'success',
		'success_msg' => $quads_table,
	));
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

			$startDate = (isset($parameters['cust_fromdate'])&& !empty($parameters['cust_fromdate']))?strtotime(str_replace("/", "-",$parameters['cust_fromdate'])) :strtotime("now");
			$endDate = (isset($parameters['cust_todate'])&& !empty($parameters['cust_todate']))?gmdate("Y-m-d", strtotime(str_replace("/", "-",$parameters['cust_todate']))) :gmdate("Y-m-d");
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
	$startDate = gmdate("Y-m-d", $startDate);//date('Y-m-d',$startDate);
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
	if(isset($_GET['id'])){
	    // phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
	    $ad_id = sanitize_text_field($_GET['id']);
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
	if(isset($_GET['day'])){
	    // phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
	    $day = sanitize_text_field($_GET['day']);
	}
	$todays_date = gmdate("Y-m-d");
	$individual_ad_dates = '';
	$get_desk_impr_specific_dates =[];
	$get_mob_impr_specific_dates =[];
	$array_top5=array();
	
		if( $day == "last_7_days" ){

			$loop = 7 ;
			$month= gmdate("m");
			$date_= gmdate("d");
			$year= gmdate("Y");
			$dates_i_chart = array() ;
			for( $i=0; $i<$loop; $i++ ){
				$dates_i_chart[] = ''.gmdate('Y-m-d', mktime(0,0,0,$month,( $date_-$i ) , $year ) );
			}
			sort($dates_i_chart);
			$from_date = strtotime($todays_date);
			$to_date =  strtotime($dates_i_chart[0]);


			if($ad_id=='all' || $ad_id == 'top_five_ads') {
				$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d",array($to_date,$from_date)));
				$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date))  as stats_date  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d",array($to_date,$from_date)));
			}
			else{
				$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array($to_date,$from_date,$ad_id)));
				$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array($to_date,$from_date,$ad_id)));
			}
			
			$ad_mob_imprsn = 0;
			$ad_desk_imprsn = 0;
			$ad_imprsn = 0;
			$ad_mob_imprsn_values = array(0,0,0,0,0,0,0) ;
			$ad_desk_imprsn_values = array(0,0,0,0,0,0,0) ;
			$ad_imprsn_values = array(0,0,0,0,0,0,0) ;
			
		    $get_impressions_specific_dates =$dates_i_chart;

			foreach($results_impresn_desk as $key =>$value){
				$ad_desk_imprsn += $value->stats_impressions;
				$ad_imprsn += $value->stats_impressions;
				$date_key = array_search($value->stats_date, $dates_i_chart);
				$ad_desk_imprsn_values[$date_key] += $value->stats_impressions;
				$ad_imprsn_values[$date_key] += $value->stats_impressions;
			}

			foreach($results_impresn_mob as $key =>$value){
				$ad_mob_imprsn += $value->stats_impressions;
				$ad_imprsn += $value->stats_impressions;
				$date_key = array_search($value->stats_date, $dates_i_chart);
				$ad_mob_imprsn_values[$date_key] += $value->stats_impressions;
				$ad_imprsn_values[$date_key] += $value->stats_impressions;
			}
		
			$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
			$desk_indi_impr_day_counts = $ad_desk_imprsn_values;	
			$individual_impr_day_counts = $ad_imprsn_values;

			if($ad_id=='all' || $ad_id == 'top_five_ads') {
				$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date  FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d",array($to_date,$from_date)));
				$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d",array($to_date,$from_date)));
			}
			else{
				$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array($to_date,$from_date,$ad_id)));
				$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array($to_date,$from_date,$ad_id)));
			}
	
			$ad_mob_clicks = 0;
			$ad_desk_clicks = 0;
			$ad_clicks = 0;
			$ad_mob_clicks_values = array(0,0,0,0,0,0,0) ;
			$ad_desk_clicks_values = array(0,0,0,0,0,0,0) ;
			$ad_clicks_values = array(0,0,0,0,0,0,0) ;
			
			foreach($results_clicks_desk as $key =>$value){
				$ad_desk_clicks += $value->stats_clicks;
				$ad_clicks += $value->stats_clicks;
				$date_key = array_search($value->stats_date, $dates_i_chart);
				$ad_desk_clicks_values[$date_key] += $value->stats_clicks;
				$ad_clicks_values[$date_key] += $value->stats_clicks;
			}

			foreach($results_clicks_mob as $key =>$value){
				$ad_mob_clicks += $value->stats_clicks;
				$ad_clicks += $value->stats_clicks;
				$date_key = array_search($value->stats_date, $dates_i_chart);
				$ad_mob_clicks_values[$date_key] += $value->stats_clicks;
				$ad_clicks_values[$date_key] += $value->stats_clicks;
			}
		
			$mob_indi_click_day_counts = $ad_mob_clicks_values;
			$desk_indi_click_day_counts = $ad_desk_clicks_values;	
			$individual_click_day_counts = $ad_clicks_values;
					
			if($ad_id=="all")
			{
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0) + IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date BETWEEN %d AND %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC , total_impression DESC;",array($to_date,$from_date,$to_date,$from_date,$to_date,$from_date,$to_date,$from_date)));			
			}else if($ad_id == 'top_five_ads'){
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0) + IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date BETWEEN %d AND %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC , total_impression DESC
				LIMIT %d;",array($to_date,$from_date,$to_date,$from_date,$to_date,$from_date,$to_date,$from_date,5)));			
			}
			

		}
		else if( $day == "this_month" ){
			
			$loop = 30 ;
			$month= gmdate("m");
			$date_= gmdate("d");
			$year= gmdate("Y");
			$first_date_ = gmdate('Y-m-d',strtotime('first day of this month'));
			$first_date_ = strtotime($first_date_);
			$current_date_month_ = gmdate('Y-m-d');
			$current_date_month_ = strtotime($current_date_month_);

			if($ad_id=='all') {
				$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d",array($first_date_,$current_date_month_)));
				$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d",array($first_date_,$current_date_month_)));
			}
			else{
				$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array($first_date_,$current_date_month_,$ad_id)));
				$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array($first_date_,$current_date_month_,$ad_id)));
			}
			
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
				$ad_mob_clicks_values[] =0;
				$ad_desk_clicks_values[] =0;
				$ad_clicks_values[] =0;
			}

			foreach($results_impresn_desk as $key =>$value){
				$ad_desk_imprsn += $value->stats_impressions;
				$ad_imprsn += $value->stats_impressions;
				$date_key = array_search($value->stats_date, $dates_i_chart);
				$ad_desk_imprsn_values[$date_key] += $value->stats_impressions;
				$ad_imprsn_values[$date_key] += $value->stats_impressions;
			}

			foreach($results_impresn_mob as $key =>$value){
				$ad_mob_imprsn += $value->stats_impressions;
				$ad_imprsn += $value->stats_impressions;
				$date_key = array_search($value->stats_date, $dates_i_chart);
				$ad_mob_imprsn_values[$date_key] += $value->stats_impressions;
				$ad_imprsn_values[$date_key] += $value->stats_impressions;
			}
		
			$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
			$desk_indi_impr_day_counts = $ad_desk_imprsn_values;	
			$individual_impr_day_counts = $ad_imprsn_values;


		$get_impressions_specific_dates =$dates_i_chart;


		if($ad_id=='all' || $ad_id == 'top_five_ads') {
			$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d",array($first_date_,$current_date_month_)));
			$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date  FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d",array($first_date_,$current_date_month_)));
		}
		else{
			$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array($first_date_,$current_date_month_,$ad_id)));
			$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array($first_date_,$current_date_month_,$ad_id)));
		}

		$ad_mob_clicks = $ad_desk_clicks = $ad_clicks = 0;
	
		foreach($results_clicks_desk as $key =>$value){
			$ad_desk_clicks += $value->stats_clicks;
			$ad_clicks += $value->stats_clicks;
			$date_key = array_search($value->stats_date, $dates_i_chart);
			$ad_desk_clicks_values[$date_key] += $value->stats_clicks;
			$ad_clicks_values[$date_key] += $value->stats_clicks;
		}

		foreach($results_clicks_mob as $key =>$value){
			$ad_mob_clicks += $value->stats_clicks;
			$ad_clicks += $value->stats_clicks;
			$date_key = array_search($value->stats_date, $dates_i_chart);
			$ad_mob_clicks_values[$date_key] += $value->stats_clicks;
			$ad_clicks_values[$date_key] += $value->stats_clicks;
		}
		
		$mob_indi_click_day_counts = $ad_mob_clicks_values;
		$desk_indi_click_day_counts = $ad_desk_clicks_values;
		$individual_click_day_counts = $ad_clicks_values;


		if($ad_id=="all")
		{
			$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0) + IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date BETWEEN %d AND %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC , total_impression DESC",array($first_date_,$current_date_month_,$first_date_,$current_date_month_,$first_date_,$current_date_month_,$first_date_,$current_date_month_)));			
		}else if($ad_id == 'top_five_ads'){
			$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0) + IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date BETWEEN %d AND %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC , total_impression DESC
				LIMIT %d;",array($first_date_,$current_date_month_,$first_date_,$current_date_month_,$first_date_,$current_date_month_,$first_date_,$current_date_month_,5)));
		}
	}
		else if( $day == "last_month" ){
			
			$loop = 30 ;
			$year = intval(gmdate("Y",strtotime("-1 month")));
			if($ad_id=='all') {
				$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE   MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
				$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date  FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
			}
			else{
				$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND ad_id = %d",array($year,$ad_id)));
				$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND ad_id = %d",array($year,$ad_id)));
			}	

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
					$ad_mob_clicks_values[]=0;
					$ad_desk_clicks_values[]=0;
					$ad_clicks_values[]=0;
			}

			foreach($results_impresn_desk as $key =>$value){
				$ad_desk_imprsn += $value->stats_impressions;
				$ad_imprsn += $value->stats_impressions;
				$date_key = array_search($value->stats_date, $dates_i_chart);
				$ad_desk_imprsn_values[$date_key] += $value->stats_impressions;
				$ad_imprsn_values[$date_key] += $value->stats_impressions;
			}

			foreach($results_impresn_mob as $key =>$value){
				$ad_mob_imprsn += $value->stats_impressions;
				$ad_imprsn += $value->stats_impressions;
				$date_key = array_search($value->stats_date, $dates_i_chart);
				$ad_mob_imprsn_values[$date_key] += $value->stats_impressions;
				$ad_imprsn_values[$date_key] += $value->stats_impressions;
			}
			$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
			$desk_indi_impr_day_counts = $ad_desk_imprsn_values;
			$individual_impr_day_counts = $ad_imprsn_values;

		$get_impressions_specific_dates = $dates_i_chart;
		if($ad_id=='all' || $ad_id=='top_five_ads') {
			$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE   MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
			$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d",array($year)));
		}
		else{
			$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND ad_id = %d",array($year,$ad_id)));
			$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE  MONTH(FROM_UNIXTIME(stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(stats_date)) = %d AND ad_id = %d",array($year,$ad_id)));
		}

		$ad_mob_clicks = $ad_desk_clicks = $ad_clicks = 0;
	
		foreach($results_clicks_desk as $key =>$value){
			$ad_desk_clicks += $value->stats_clicks;
			$ad_clicks += $value->stats_clicks;
			$date_key = array_search($value->stats_date, $dates_i_chart);
			$ad_desk_clicks_values[$date_key] += $value->stats_clicks;
			$ad_clicks_values[$date_key] += $value->stats_clicks;
		}

		foreach($results_clicks_mob as $key =>$value){
			$ad_mob_clicks += $value->stats_clicks;
			$ad_clicks += $value->stats_clicks;
			$date_key = array_search($value->stats_date, $dates_i_chart);
			$ad_mob_clicks_values[$date_key] += $value->stats_clicks;
			$ad_clicks_values[$date_key] += $value->stats_clicks;
		}
		

		$mob_indi_click_day_counts = $ad_mob_clicks_values;
		$desk_indi_click_day_counts = $ad_desk_clicks_values;
		$individual_click_day_counts = $ad_clicks_values;
		

		if($ad_id=="all")
		{
			$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0) + IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND  MONTH(FROM_UNIXTIME(impr_mob.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(impr_mob.stats_date)) = %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND  MONTH(FROM_UNIXTIME(impr_desk.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(impr_desk.stats_date)) = %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND  MONTH(FROM_UNIXTIME(click_mob.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(click_mob.stats_date)) = %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND  MONTH(FROM_UNIXTIME(click_desk.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(click_desk.stats_date)) = %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC , total_impression DESC",array($year,$year,$year,$year)));			
		}else if($ad_id == 'top_five_ads'){
			$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0) + IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND  MONTH(FROM_UNIXTIME(impr_mob.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(impr_mob.stats_date)) = %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND  MONTH(FROM_UNIXTIME(impr_desk.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(impr_desk.stats_date)) = %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND  MONTH(FROM_UNIXTIME(click_mob.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(click_mob.stats_date)) = %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND  MONTH(FROM_UNIXTIME(click_desk.stats_date))=MONTH(now())-1 AND YEAR(FROM_UNIXTIME(click_desk.stats_date)) = %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC , total_impression DESC
				LIMIT %d;",array($year,$year,$year,$year,5)));			
		}
	}
		else if( $day == "all_time" ){
			
			$loop = 30 ;
			$month= gmdate("m");
			$date_= gmdate("d");
			$year= gmdate("Y");
			$first_date_ = gmdate('Y-m-d',strtotime('first day of this month'));
			$current_date_month_ = gmdate('Y-m-d');

			if($ad_id=='all' || $ad_id == 'top_five_ads') {
				$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as desk_impressions, stats_year  FROM `{$wpdb->prefix}quads_impressions_desktop` GROUP BY stats_year"));
				$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as mob_impressions, stats_year  FROM `{$wpdb->prefix}quads_impressions_mobile`  GROUP BY stats_year"));
			}
			else{
				$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as desk_impressions, stats_year  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE ad_id = %d GROUP BY stats_year",array($ad_id)));
				$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as mob_impressions, stats_year  FROM `{$wpdb->prefix}quads_impressions_mobile`  WHERE ad_id = %d  GROUP BY stats_year",array($ad_id)));
			
			}	

			$ad_mob_imprsn = $ad_desk_imprsn = $ad_imprsn = 0;
			$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = array() ;
			$ad_mob_imprsn_values_ = $ad_desk_imprsn_values_ = $ad_imprsn_values_ = array() ;

			$combinedData = [];
			$results_impresn_desk = is_array($results_impresn_desk) ? $results_impresn_desk : array();
			$results_impresn_mob = is_array($results_impresn_mob) ? $results_impresn_mob : array();
			$merge_array = array_merge($results_impresn_desk, $results_impresn_mob);
			foreach ($merge_array as $item) {
				$stats_year = $item->stats_year;
				
				if (!isset($combinedData[$stats_year])) {
					$combinedData[$stats_year] = ["stats_year" => $stats_year, "desk_impressions" => 0, "mob_impressions" => 0];
				}
				
				if (isset($item->desk_impressions)) {
					$combinedData[$stats_year]["desk_impressions"] += $item->desk_impressions;
				}
				
				if (isset($item->mob_impressions)) {
					$combinedData[$stats_year]["mob_impressions"] += $item->mob_impressions;
				}
			}

			$array_com = array_values($combinedData);

			foreach($array_com as $value){
				$ad_desk_imprsn += $value['desk_impressions'];
				$ad_imprsn += ($value['desk_impressions']+$value['mob_impressions']);
				$ad_desk_imprsn_values[] = $value['stats_year'];
				$ad_imprsn_values[] = $value['stats_year'];
				$ad_desk_imprsn_values_[] = $value['desk_impressions'];
				$ad_imprsn_values_[] = ($value['desk_impressions']+$value['mob_impressions']); 
				$ad_mob_imprsn += $value['mob_impressions'];
				$ad_mob_imprsn_values[] = $value['mob_impressions'];
				$ad_mob_imprsn_values_[] = $value['mob_impressions'];
			}

			$mob_indi_impr_day_counts = $ad_mob_imprsn_values_;
			$desk_indi_impr_day_counts = $ad_desk_imprsn_values_;
			$individual_impr_day_counts = $ad_imprsn_values_;
			// individual_impr_day_counts
			$get_mob_impr_specific_dates = $ad_mob_imprsn_values;
			$get_desk_impr_specific_dates = $ad_desk_imprsn_values;
			$get_impressions_specific_dates = $ad_imprsn_values;


			if($ad_id=='all' || $ad_id == 'top_five_ads') {
				$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as desk_clicks, stats_year  FROM `{$wpdb->prefix}quads_clicks_desktop` GROUP BY stats_year"));
				$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as mob_clicks, stats_year  FROM `{$wpdb->prefix}quads_clicks_mobile`  GROUP BY stats_year"));
			}
			else{
				$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as desk_clicks, stats_year  FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE ad_id = %d GROUP BY stats_year",array($ad_id)));
				$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as mob_clicks, stats_year  FROM `{$wpdb->prefix}quads_clicks_mobile`  WHERE ad_id = %d  GROUP BY stats_year",array($ad_id)));
			
			}	
			
		$ad_mob_clicks = $ad_desk_clicks = $ad_clicks = 0;
		$ad_mob_clicks_values = $ad_desk_clicks_values = $ad_clicks_values = array();
		$ad_mob_clicks_values_ = $ad_desk_clicks_values_ = $ad_clicks_values_ = array();
		$combinedData = [];
		$results_clicks_desk = is_array($results_clicks_desk) ? $results_clicks_desk : array();
		$results_clicks_mob = is_array($results_clicks_mob) ? $results_clicks_mob : array();
		$merge_array_c = array_merge($results_clicks_desk, $results_clicks_mob);

		foreach ($merge_array_c as $item) {
			$stats_year = $item->stats_year;
			
			if (!isset($combinedData[$stats_year])) {
				$combinedData[$stats_year] = ["stats_year" => $stats_year, "desk_clicks" => 0, "mob_clicks" => 0];
			}
			if (isset($item->desk_clicks)) {
				$combinedData[$stats_year]["desk_clicks"] += $item->desk_clicks;
			}
			if (isset($item->mob_clicks)) {
				$combinedData[$stats_year]["mob_clicks"] += $item->mob_clicks;
			}
		}
		$array_com_c = array_values($combinedData);
		foreach($array_com_c as $key =>$value){
			$ad_desk_clicks += $value['desk_clicks'];
			$ad_clicks += ($value['desk_clicks']+$value['mob_clicks']);
			$ad_desk_clicks_values[] = $value['stats_year'];
			$ad_clicks_values[] = $value['stats_year'];
			$ad_desk_clicks_values_[] = $value['desk_clicks'];
			$ad_clicks_values_[] = ($value['desk_clicks']+$value['mob_clicks']); 
			$ad_mob_clicks += $value['mob_clicks'];
			$ad_mob_clicks_values[] = $value['mob_clicks'];
			$ad_mob_clicks_values_[] = $value['mob_clicks'];
		}

		$mob_indi_click_day_counts = $ad_mob_clicks_values_;
		$desk_indi_click_day_counts = $ad_desk_clicks_values_;
		$individual_click_day_counts = $ad_clicks_values_;
		
			if($ad_id=="all")
			{
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id
					WHERE posts.post_type='quads-ads'AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC , total_impression DESC;"));			
			}else if($ad_id == 'top_five_ads'){
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id
					WHERE posts.post_type='quads-ads'AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC , total_impression DESC
					LIMIT %d;",array(5)));			
			}
		}

		else if( $day == "this_year" ){
			
			$loop = 30 ;
			$month= gmdate("m");
			$date_= gmdate("d");
			$year= gmdate("Y");
			$first_date_ = gmdate('Y-m-d',strtotime('first day of this month'));
			$current_date_month_ = gmdate('Y-m-d');
			if($ad_id=="all" || $ad_id == 'top_five_ads'){
				$yearly_mob_impressions = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s ; ",array($year)));
				$yearly_desk_impressions = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s ; ",array($year)));
			}else{
				$yearly_mob_impressions = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s AND ad_id = %d; ",array($year,$ad_id)));
				$yearly_desk_impressions = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_impressions END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_impressions END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_impressions END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_impressions END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_impressions END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_impressions END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_impressions END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_impressions END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_impressions END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_impressions END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_impressions END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_impressions END),0) as dec_impr FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s AND ad_id = %d; ",array($year,$ad_id)));
			}
			$mob_imp=reset($yearly_mob_impressions);
			$desk_imp=reset($yearly_desk_impressions);

			$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = array();
		
			$ad_mob_imprsn_values = [$mob_imp->jan_impr,$mob_imp->feb_impr,$mob_imp->mar_impr,$mob_imp->apr_impr,$mob_imp->may_impr,$mob_imp->jun_impr,$mob_imp->jul_impr,$mob_imp->aug_impr,$mob_imp->sep_impr,$mob_imp->oct_impr,$mob_imp->nov_impr,$mob_imp->dec_impr];
			$ad_mob_imprsn = $mob_imp->jan_impr+$mob_imp->feb_impr+$mob_imp->mar_impr+$mob_imp->apr_impr+$mob_imp->may_impr+$mob_imp->jun_impr+$mob_imp->jul_impr+$mob_imp->aug_impr+$mob_imp->sep_impr+$mob_imp->oct_impr+$mob_imp->nov_impr+$mob_imp->dec_impr;
			
			$ad_desk_imprsn_values = [$desk_imp->jan_impr,$desk_imp->feb_impr,$desk_imp->mar_impr,$desk_imp->apr_impr,$desk_imp->may_impr,$desk_imp->jun_impr,$desk_imp->jul_impr,$desk_imp->aug_impr,$desk_imp->sep_impr,$desk_imp->oct_impr,$desk_imp->nov_impr,$desk_imp->dec_impr];
			$ad_desk_imprsn =$desk_imp->jan_impr+$desk_imp->feb_impr+$desk_imp->mar_impr+$desk_imp->apr_impr+$desk_imp->may_impr+$desk_imp->jun_impr+$desk_imp->jul_impr+$desk_imp->aug_impr+$desk_imp->sep_impr+$desk_imp->oct_impr+$desk_imp->nov_impr+$desk_imp->dec_impr;
			$ad_imprsn_values = wpquadsSumArrays($ad_mob_imprsn_values,$ad_desk_imprsn_values);
			$ad_imprsn = $ad_mob_imprsn+$ad_desk_imprsn;

			$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
			$desk_indi_impr_day_counts = $ad_desk_imprsn_values;
			$individual_impr_day_counts = $ad_imprsn_values;
			$individual_ad_dates = [1];

			if($ad_id=="all" || $ad_id == 'top_five_ads'){
				$yearly_mob_clicks = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE  YEAR(FROM_UNIXTIME(stats_date)) = %s ; ",array($year)));
				$yearly_desk_clicks = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE YEAR(FROM_UNIXTIME(stats_date)) = %s ; ",array($year)));
			}else{
				$yearly_mob_clicks = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE YEAR(FROM_UNIXTIME(stats_date)) = %s AND ad_id = %d; ",array($year,$ad_id)));
				$yearly_desk_clicks = $wpdb->get_results($wpdb->prepare("SELECT IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 1 THEN stats_clicks END),0) AS jan_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 2 THEN stats_clicks END),0) AS feb_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 3 THEN stats_clicks END),0) AS mar_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 4 THEN stats_clicks END),0) AS apr_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 5 THEN stats_clicks END),0) AS may_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 6 THEN stats_clicks END),0) AS jun_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 7 THEN stats_clicks END),0) AS jul_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 8 THEN stats_clicks END),0) AS aug_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 9 THEN stats_clicks END),0) AS sep_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 10 THEN stats_clicks END),0) AS oct_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 11 THEN stats_clicks END),0) AS nov_impr,IFNULL(SUM(CASE WHEN MONTH(FROM_UNIXTIME(stats_date)) = 12 THEN stats_clicks END),0) as dec_impr FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE YEAR(FROM_UNIXTIME(stats_date)) = %s AND ad_id = %d; ",array($year,$ad_id)));
			}

			$mob_clk=reset($yearly_mob_clicks);
			$desk_clk=reset($yearly_desk_clicks);			
			
			$ad_mob_click_values = $ad_desk_click_values = $ad_click_values = array();

			$ad_mob_clicks = $mob_clk->jan_impr+$mob_clk->feb_impr+$mob_clk->mar_impr+$mob_clk->apr_impr+$mob_clk->may_impr+$mob_clk->jun_impr+$mob_clk->jul_impr+$mob_clk->aug_impr+$mob_clk->sep_impr+$mob_clk->oct_impr+$mob_clk->nov_impr+$mob_clk->dec_impr;
			$ad_mob_click_values = [$mob_clk->jan_impr,$mob_clk->feb_impr,$mob_clk->mar_impr,$mob_clk->apr_impr,$mob_clk->may_impr,$mob_clk->jun_impr,$mob_clk->jul_impr,$mob_clk->aug_impr,$mob_clk->sep_impr,$mob_clk->oct_impr,$mob_clk->nov_impr,$mob_clk->dec_impr];
			
			$ad_desk_clicks = $desk_clk->jan_impr+$desk_clk->feb_impr+$desk_clk->mar_impr+$desk_clk->apr_impr+$desk_clk->may_impr+$desk_clk->jun_impr+$desk_clk->jul_impr+$desk_clk->aug_impr+$desk_clk->sep_impr+$desk_clk->oct_impr+$desk_clk->nov_impr+$desk_clk->dec_impr;
			$ad_desk_click_values = [$desk_clk->jan_impr,$desk_clk->feb_impr,$desk_clk->mar_impr,$desk_clk->apr_impr,$desk_clk->may_impr,$desk_clk->jun_impr,$desk_clk->jul_impr,$desk_clk->aug_impr,$desk_clk->sep_impr,$desk_clk->oct_impr,$desk_clk->nov_impr,$desk_clk->dec_impr];

			$ad_clicks =  $ad_mob_clicks+$ad_desk_clicks;
			$ad_click_values = wpquadsSumArrays($ad_mob_click_values,$ad_desk_click_values);

			$mob_indi_click_day_counts = $ad_mob_click_values;
			$desk_indi_click_day_counts = $ad_desk_click_values;
			$individual_click_day_counts = $ad_click_values;

			
			if($ad_id=="all")
			{
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND YEAR(FROM_UNIXTIME(impr_mob.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND YEAR(FROM_UNIXTIME(impr_desk.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND YEAR(FROM_UNIXTIME(click_mob.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND YEAR(FROM_UNIXTIME(click_desk.stats_date)) = %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC , total_impression DESC;",array($year,$year,$year,$year)));			
			}else if($ad_id == 'top_five_ads'){
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND YEAR(FROM_UNIXTIME(impr_mob.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND YEAR(FROM_UNIXTIME(impr_desk.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND YEAR(FROM_UNIXTIME(click_mob.stats_date)) = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND YEAR(FROM_UNIXTIME(click_desk.stats_date)) = %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC , total_impression DESC
					LIMIT %d;",array($year,$year,$year,$year,5)));			
			}

	}
	else if( $day == "yesterday" ){
		
		$yesterday_date = gmdate('Y-m-d',strtotime("-1 days"));

		if($ad_id=='all' || $ad_id == 'top_five_ads') {
			$results_impresn_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date = %d",array(strtotime($yesterday_date))));
			$results_impresn_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date = %d",array(strtotime($yesterday_date))));
		}
		else{
			$results_impresn_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date = %d AND ad_id = %d",array(strtotime($yesterday_date),$ad_id)));
			$results_impresn_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date = %d AND ad_id = %d",array(strtotime($yesterday_date),$ad_id)));
		}
		
		if($ad_id=='all' || $ad_id == 'top_five_ads') {
			$results_clicks_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  stats_clicks  FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date = %d",array(strtotime($yesterday_date))));
			$results_clicks_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0)as  stats_clicks FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date = %d",array(strtotime($yesterday_date))));
		}
		else{
			$results_clicks_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  stats_clicks FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date = %d AND ad_id = %d",array(strtotime($yesterday_date),$ad_id)));
			$results_clicks_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  stats_clicks FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date = %d AND ad_id = %d",array(strtotime($yesterday_date),$ad_id)));
		}	
		
			$ad_mob_imprsn = $results_impresn_mob? $results_impresn_mob:0;	
			$ad_desk_imprsn = $results_impresn_desk? $results_impresn_desk:0;		
			$ad_imprsn = $ad_mob_imprsn + $ad_desk_imprsn;		
			$ad_desk_clicks = $results_clicks_desk? $results_clicks_desk:0;
			$ad_mob_clicks = $results_clicks_mob? $results_clicks_mob:0;
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
			$get_impressions_specific_dates = $yesterday_date;

			if($ad_id=="all")
			{
	
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
						FROM {$wpdb->prefix}posts as posts
						LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date = %d
						LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date = %d
						LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date = %d
						LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date = %d
						WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
						GROUP BY posts.ID
						ORDER BY total_click DESC , total_impression DESC;",array(strtotime($yesterday_date),strtotime($yesterday_date),strtotime($yesterday_date),strtotime($yesterday_date))));	
			}else if($ad_id == 'top_five_ads'){
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
						FROM {$wpdb->prefix}posts as posts
						LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date = %d
						LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date = %d
						LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date = %d
						LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date = %d
						WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
						GROUP BY posts.ID
						ORDER BY total_click DESC , total_impression DESC
						LIMIT %d;",array(strtotime($yesterday_date),strtotime($yesterday_date),strtotime($yesterday_date),strtotime($yesterday_date),5)));	
			}

	}
	else if( $day == "today" ) {
		$get_impressions_specific_dates = str_replace('-','/',$todays_date);
			
		if($ad_id=='all' || $ad_id == 'top_five_ads') {
			$results_impresn_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date = %d",array(strtotime($todays_date))));
			$results_impresn_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date = %d",array(strtotime($todays_date))));
		}
		else{
			$results_impresn_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date = %d AND ad_id = %d",array(strtotime($todays_date),$ad_id)));
			$results_impresn_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_impressions),0) as  stats_impressions FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date = %d AND ad_id = %d",array(strtotime($todays_date),$ad_id)));
		}
		
		if($ad_id=='all' || $ad_id == 'top_five_ads') {
			$results_clicks_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  stats_clicks  FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date = %d",array(strtotime($todays_date))));
			$results_clicks_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0)as  stats_clicks FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date = %d",array(strtotime($todays_date))));
		}
		else{
			$results_clicks_desk = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  stats_clicks FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date = %d AND ad_id = %d",array(strtotime($todays_date),$ad_id)));
			$results_clicks_mob = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(stats_clicks),0) as  stats_clicks FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date = %d AND ad_id = %d",array(strtotime($todays_date),$ad_id)));
		}	


			
			$ad_mob_imprsn = $results_impresn_mob? $results_impresn_mob:0;	
			$ad_desk_imprsn = $results_impresn_desk? $results_impresn_desk:0;;		
			$ad_imprsn = $ad_mob_imprsn + $ad_desk_imprsn;		
			$ad_desk_clicks = $results_clicks_desk? $results_clicks_desk:0;
			$ad_mob_clicks = $results_clicks_mob? $results_clicks_mob:0;
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

			if($ad_id=="all")
			{

				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date = %d
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date = %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC , total_impression DESC;",array(strtotime($todays_date),strtotime($todays_date),strtotime($todays_date),strtotime($todays_date))));	
			}else if($ad_id == 'top_five_ads'){
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0)+IFNULL(impr_mob.stats_impressions,0)) as total_impression
					FROM {$wpdb->prefix}posts as posts
					LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date = %d
					LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date = %d
					LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date = %d
					WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
					GROUP BY posts.ID
					ORDER BY total_click DESC , total_impression DESC
					LIMIT %d;",array(strtotime($todays_date),strtotime($todays_date),strtotime($todays_date),strtotime($todays_date),5)));	
			}

	}
	else if( $day == "custom" ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
		if(isset($_GET['fromdate'])){
		    // phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
		    $fromdate = sanitize_text_field($_GET['fromdate']);
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
		if(isset($_GET['todate'])){
		    // phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called
		    $todate = sanitize_text_field($_GET['todate']);
		}
		$get_from = preg_replace('/(.*?)-(.*?)-(.*?)T(.*)/', '$1-$2-$3', $fromdate);
		$get_to = preg_replace('/(.*?)-(.*?)-(.*?)T(.*)/', '$1-$2-$3', $todate);

		
		if($ad_id=='all' || $ad_id == 'top_five_ads') {
			$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as desk_imprsn,DATE(FROM_UNIXTIME(stats_date)) as stats_date  FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d",array(strtotime($get_from),strtotime($get_to))));
			$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as mob_imprsn,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d",array(strtotime($get_from),strtotime($get_to))));
		}
		else{
			$results_impresn_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as desk_imprsn,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array(strtotime($get_from),strtotime($get_to),$ad_id)));
			$results_impresn_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_impressions as mob_imprsn,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array(strtotime($get_from),strtotime($get_to),$ad_id)));
		}

		$ad_mob_imprsn = $ad_desk_imprsn = $ad_imprsn = 0;		
		$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = [];
		$ad_mob_clicks = $ad_desk_clicks = $ad_clicks = 0;
		$ad_mob_clicks_values = $ad_desk_clicks_values = $ad_clicks_values = [];

		$period = new DatePeriod(new DateTime(''.$get_from.''), new DateInterval('P1D'), new DateTime(''.$get_to.''.' +1 day'));
		$dates_i_chart = [];
		foreach ($period as $date) {
			$dates_i_chart[]= $date->format("Y-m-d");
		}
		$get_impressions_specific_dates =$dates_i_chart;

		$combinedData = [];
		$results_impresn_desk = is_array($results_impresn_desk) ? $results_impresn_desk : array();
		$results_impresn_mob = is_array($results_impresn_mob) ? $results_impresn_mob : array();
		$merge_array = array_merge($results_impresn_desk, $results_impresn_mob);

		

		
		foreach ($merge_array as $item) {
			$stats_date = $item->stats_date;
			
			if (!isset($combinedData[$stats_date])) {
				$combinedData[$stats_date] = ["stats_date" => $stats_date, "desk_imprsn" => 0, "mob_imprsn" => 0];
			}
			if (isset($item->desk_imprsn)) {
				$combinedData[$stats_date]["desk_imprsn"] += $item->desk_imprsn;
			}
			if (isset($item->mob_imprsn)) {
				$combinedData[$stats_date]["mob_imprsn"] += $item->mob_imprsn;
			}
		}
		
		$array_com_c = array_values($combinedData);
		
		foreach($dates_i_chart as $single){
			$ad_mob_imprsn_values[] = 0;
			$ad_desk_imprsn_values[] = 0;
			$ad_imprsn_values[] = 0;
			$ad_mob_clicks_values []= 0;
			$ad_desk_clicks_values []= 0;
			$ad_clicks_values []= 0;
		}

		
		foreach ($array_com_c as $key => $value) {
			$ad_mob_imprsn += $value['mob_imprsn'];
			$ad_desk_imprsn +=$value['desk_imprsn'];
			$ad_imprsn += $value['mob_imprsn']+$value['desk_imprsn'];
			$date_key = array_search($value['stats_date'], $dates_i_chart);
			if($date_key){
				$ad_mob_imprsn_values[$date_key] = $value['mob_imprsn'];
				$ad_desk_imprsn_values[$date_key] = $value['desk_imprsn'];
				$ad_imprsn_values[$date_key] = ($value['mob_imprsn']+$value['desk_imprsn']);
			}

		}

	
		$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
		$desk_indi_impr_day_counts = $ad_desk_imprsn_values;
		$individual_impr_day_counts = $ad_imprsn_values;

		if($ad_id=='all' || $ad_id == 'top_five_ads') {
			$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as desk_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date  FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d",array(strtotime($get_from),strtotime($get_to))));
			$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as mob_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d",array(strtotime($get_from),strtotime($get_to))));
		}
		else{
			$results_clicks_desk = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as desk_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array(strtotime($get_from),strtotime($get_to),$ad_id)));
			$results_clicks_mob = $wpdb->get_results($wpdb->prepare("SELECT stats_clicks as mob_clicks,DATE(FROM_UNIXTIME(stats_date)) as stats_date FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE stats_date BETWEEN %d AND %d AND ad_id = %d",array(strtotime($get_from),strtotime($get_to),$ad_id)));
		}

		$combinedData = [];
		$results_clicks_desk = is_array($results_clicks_desk) ? $results_clicks_desk : array();
		$results_clicks_mob = is_array($results_clicks_mob) ? $results_clicks_mob : array();
		$merge_array = array_merge($results_clicks_desk, $results_clicks_mob);

		foreach ($merge_array as $item) {
			$stats_date = $item->stats_date;
			
			if (!isset($combinedData[$stats_date])) {
				$combinedData[$stats_date] = ["stats_date" => $stats_date, "desk_clicks" => 0, "mob_clicks" => 0];
			}
			if (isset($item->desk_clicks)) {
				$combinedData[$stats_date]["desk_clicks"] += $item->desk_clicks;
			}
			if (isset($item->mob_clicks)) {
				$combinedData[$stats_date]["mob_clicks"] += $item->mob_clicks;
			}
		}
		$array_com_c = array_values($combinedData);

		foreach ($array_com_c as $key => $value) {
			$ad_mob_clicks += $value['mob_clicks'];
			$ad_desk_clicks +=$value['desk_clicks'];
			$ad_clicks += $value['mob_clicks']+$value['desk_clicks'];
			$date_key = array_search($value['stats_date'], $dates_i_chart);
			if($date_key){
				$ad_mob_clicks_values[$date_key] = $value['mob_clicks'];
				$ad_desk_clicks_values[$date_key] = $value['desk_clicks'];
				$ad_clicks_values[$date_key] = ($value['mob_clicks']+$value['desk_clicks']);
			}
			}

		$mob_indi_click_day_counts = $ad_mob_clicks_values;
		$desk_indi_click_day_counts = $ad_desk_clicks_values;
		$individual_click_day_counts = $ad_clicks_values;
		$get_mob_impr_specific_dates= $dates_i_chart;
		$get_desk_impr_specific_dates= $dates_i_chart;

		$get_from_=strtotime($get_from);
		$get_to_=strtotime($get_to);

		if($ad_id=="all")
			{
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0) + IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date BETWEEN %d AND %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC , total_impression DESC;",array($get_from_,$get_to_,$get_from_,$get_to_,$get_from_,$get_to_,$get_from_,$get_to_)));
			}else if($ad_id == 'top_five_ads'){
				$array_top5= $wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, posts.post_title as post_title, IFNULL(SUM(click_desk.stats_clicks),0)as desk_clicks,IFNULL(SUM(click_mob.stats_clicks),0) as mob_clicks,IFNULL(SUM(impr_mob.stats_impressions),0) as mob_imprsn ,IFNULL(SUM(impr_desk.stats_impressions),0) as desk_imprsn,SUM(IFNULL(click_desk.stats_clicks,0)+IFNULL(click_mob.stats_clicks,0)) as total_click,SUM(IFNULL(impr_desk.stats_impressions,0) + IFNULL(impr_mob.stats_impressions,0)) as total_impression
				FROM {$wpdb->prefix}posts as posts
				LEFT JOIN {$wpdb->prefix}quads_impressions_mobile as impr_mob ON posts.ID=impr_mob.ad_id AND impr_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_impressions_desktop as impr_desk ON posts.ID=impr_desk.ad_id AND impr_desk.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_mobile as click_mob ON posts.ID=click_mob.ad_id AND click_mob.stats_date BETWEEN %d AND %d
				LEFT JOIN {$wpdb->prefix}quads_clicks_desktop as click_desk ON posts.ID=click_desk.ad_id AND click_desk.stats_date BETWEEN %d AND %d
				WHERE posts.post_type='quads-ads' AND posts.post_status='publish'
				GROUP BY posts.ID
				ORDER BY total_click DESC , total_impression DESC
				LIMIT %d;",array($get_from_,$get_to_,$get_from_,$get_to_,$get_from_,$get_to_,$get_from_,$get_to_,5)));
			}
		
	}
	
	/**
	 * Fetch only rotator ads if ad_id is selected as all
	 * @since 2.0.84
	 * */
	$rotator_ads = array();
	$re_arrange_top5 = array();
	if($ad_id == "all" && $day == "all_time"){

		$rotator_ads = 	$wpdb->get_results($wpdb->prepare("SELECT posts.ID as ID, postmeta.meta_key, postmeta.meta_value  
																												FROM {$wpdb->prefix}posts as posts 
																												LEFT JOIN {$wpdb->prefix}postmeta as postmeta ON posts.ID = postmeta.post_id
																												WHERE posts.post_type = 'quads-ads' AND posts.post_status='publish' AND postmeta.meta_value = 'rotator_ads'" ), ARRAY_A);
		$rotate_sub_ads = array();
		$rcnt = 0;
		if(!empty($rotator_ads) && is_array($rotator_ads)){

			foreach ($rotator_ads as $ra_key => $ra_value) {

				$rotate_sub_ads[$ra_value['ID']][$rcnt]['ID'] = 	$ra_value['ID'];
				$rotate_sub_ads[$ra_value['ID']][$rcnt]['is_parent'] = 	'yes';
				$ads_list = get_post_meta($ra_value['ID'], 'ads_list', true);

				if(!empty($ads_list) && is_array($ads_list)){

					foreach ($ads_list as $al_key => $al_value) {

						if(is_array($al_value) && isset($al_value['value'])){
							$rotate_sub_ads[$ra_value['ID']][$al_value['value']]['ID'] = $al_value['value'];
							$rotate_sub_ads[$ra_value['ID']][$al_value['value']]['is_parent'] = 'no';
						}
						
					}

				}
				$rcnt++;

			}

		}

		if(!empty($rotate_sub_ads) && is_array($rotate_sub_ads) && !empty($array_top5) && is_array($array_top5)){

			$re_arrange_top5 = array();
			$ad_cnt = 0;

			foreach ($rotate_sub_ads as $rsa_key1 => $rsa_value1) {

				if(!empty($rsa_value1) && is_array($rsa_value1)){

					foreach ($rsa_value1 as $rsa_key => $rsa_value) {

						foreach ($array_top5 as $at_key => $at_value) {

							if($rsa_value['ID'] == $at_value->ID && $rsa_value['is_parent'] == 'yes'){

								$re_arrange_top5[$ad_cnt] = $at_value;	
								$re_arrange_top5[$ad_cnt]->is_parent = $rsa_value['is_parent'];	

							}else if($rsa_value['ID'] == $at_value->ID && $rsa_value['is_parent'] == 'no'){

								$re_arrange_top5[$ad_cnt] = $at_value;	
								$re_arrange_top5[$ad_cnt]->is_parent = $rsa_value['is_parent'];

							}
							$ad_cnt++;	

						}	// array_top5 each end

					} // rsa_value1 each end

				} // rsa_value1 if end

			} // rotate_sub_ads each end

			if(!empty($re_arrange_top5) && is_array($re_arrange_top5)){

				$re_arrange_top5 = array_values($re_arrange_top5);

				foreach ($re_arrange_top5 as $rat_key => $rat_value) {

					foreach ($array_top5 as $at_key => $at_value) {

						if($rat_value->ID == $at_value->ID){
							unset($array_top5[$at_key]);
						}

					}

				}
				$array_top5 = array_merge($re_arrange_top5, $array_top5);	

			} // re_arrange_top5 if end

		} // rotate_sub_ads if end

	} // ad_id if end

			
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
				'msg' => wp_kses( /* translators: %s: account name */ sprintf( __( 'Advanced Ads does not have access to your account (<code>%s</code>) anymore.', 'quick-adsense-reloaded' ), $account ), array( 'code' => true ) ),
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
				date_default_timezone_set($timezone);
				$result = strtotime('00:00:00') + (get_option('gmt_offset') * 3600);
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
			$desk_clicks = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(`stats_clicks`),0) as `clicks` FROM `{$wpdb->prefix}quads_clicks_desktop` WHERE `ad_id` = %d;", $ad_id));
			$mob_clicks = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(`stats_clicks`),0) as `clicks` FROM `{$wpdb->prefix}quads_clicks_mobile` WHERE `ad_id` = %d;", $ad_id));
            $desk_impres = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(`stats_impressions`),0) as `impressions` FROM `{$wpdb->prefix}quads_impressions_desktop` WHERE `ad_id` = %d;", $ad_id));
			$mob_impres = $wpdb->get_var($wpdb->prepare("SELECT IFNULL(SUM(`stats_impressions`),0) as `impressions` FROM `{$wpdb->prefix}quads_impressions_mobile` WHERE `ad_id` = %d;", $ad_id));
           
            $ad_stats['impressions'] = $desk_impres+$mob_impres;
            $ad_stats['clicks']      = $desk_clicks+$mob_clicks;
                                    
            break;
        
		case 'search':
			$ad_thetime = '';
			$items_per_page = 20;
			$page = (isset($parameters['page'])&& !empty($parameters['page']))?$parameters['page'] :1;
			$page = intval($page);
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
						default:
							$startDate = strtotime(" -6 day");
							break;
					}
					$ad_thetime = $wpdb->prepare('WHERE log_date BETWEEN %d AND %d',array($startDate,$endDate)); 
				
				}
			}
			$search_param = '';
			if(isset($parameters['search_param']) && !empty($parameters['search_param'])){
				if(empty($ad_thetime)){
					$search_param = $wpdb->prepare("where ad_id  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
					ip_address  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
					log_url  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
					browser  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
					referrer  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%'   "); 
				}else {
				
					$search_param = $wpdb->prepare("and ( ad_id  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
					ip_address  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
					log_url  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
					browser  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
					referrer  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' )  "); 
				}

			}
		
				$results = $wpdb->get_results($wpdb->prepare("SELECT ad_id , log_date as ad_thetime,log_clicks ,ip_address,log_url as url,browser,referrer FROM `{$wpdb->prefix}quads_logs` ". $ad_thetime ." ".$search_param." LIMIT %d, %d",array($offset,$items_per_page)), ARRAY_A);
				$ad_stats = $results;
				$result_total = $wpdb->get_row($wpdb->prepare("SELECT count(*) as total FROM `{$wpdb->prefix}quads_logs` ". $ad_thetime ." ".$search_param), ARRAY_A);
				$log_array = array();
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
				$response['search_param'] = $search_param;

				return $response;

			break;
        default:
            break;
    }            
    return $ad_stats;
}


function wpquadsSumArrays(array $a = [], array $b = []) {
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

/*
* Clear log entries older than 30 days to keep DB size small
*/
function wpquads_logs_weekly_clear( $schedules ) {
	// add a 'weekly' schedule to the existing set
	$schedules['wpquads_logs_weekly'] = array(
		'interval' => 604800,
		'display' => __('Clear Wpquads Logs Weekly', 'quick-adsense-reloaded')
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'wpquads_logs_weekly_clear' );

if ( ! wp_next_scheduled( 'wpquads_logs_weekly_clear' ) ) {
	wp_schedule_event( time(), 'wpquads_logs_weekly', 'wpquads_logs_weekly_clear' );
}

function wpquads_cron_import_schedule( $schedules ) {
	// add a 'weekly' schedule to the existing set
	$schedules['wpquads_cron_import'] = array(
		'interval' => 1000,
		'display' => __('Cron Import Wpquads', 'quick-adsense-reloaded' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'wpquads_cron_import_schedule' );

if ( ! wp_next_scheduled( 'wpquads_cron_import' ) ) {
	$import_details = get_option('quads_import_data');
	if(isset($import_details['status']) && $import_details['status'] == 'active'){
		wp_schedule_event( time(), 'wpquads_cron_import', 'wpquads_cron_import_action' );
	}
	
}

add_action( 'wpquads_cron_import_action', 'wpquads_cron_import_action_cb' );
function wpquads_cron_import_action_cb() {
	quads_adsense_import_old_db();
}


function quads_adsense_import_old_db(){
ignore_user_abort(true);
set_time_limit(900);
$default  = array('status' => 'inactive','current_table'=>'quads_stats','sub_table'=>'','offset'=> 50,'imported' => 0,'total' => 0);
$import_details = get_option('quads_import_data',$default);
error_log(json_encode($import_details));
$import_done = get_option('quads_db_import',false);
global $wpdb;
if($import_details['status'] == 'active' && !$import_done){
	if($import_details['current_table'] == 'quads_stats'){

		   $old_db = $wpdb->prefix.'quads_stats';
			$new_db = $wpdb->prefix.'quads_logs';
			$since = strtotime("-30 day");
			$offset = isset($import_details['offset'])?intval($import_details['offset']):50;
			$imported = isset($import_details['imported'])?intval($import_details['imported']):0;
			$total_records = isset($import_details['total'])?intval($import_details['total']):0;
			if(!$total_records){
				$total_records = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE  ad_thetime > %d AND ad_clicks > 0",array($old_db,$since)));
			}
			$loop_no = ceil($total_records/$offset);
			for($i=0;$i<$loop_no;$i++){
			$old_db_results = $wpdb->get_results($wpdb->prepare("SELECT ad_id,ad_thetime,ad_clicks,ip_address,URL,browser,referrer FROM %i WHERE  ad_thetime > %d AND ad_clicks > 0 LIMIT %d,%d",array($old_db,$since,$imported,$offset)));
			$wpdb->flush();
			if(is_array($old_db_results ) && count($old_db_results) > 0){
				$insertQuery = "INSERT INTO %i (ad_id,log_date,log_clicks,ip_address,log_url,browser,referrer) VALUES";
				$insertQueryValues = array();
				foreach($old_db_results as $odb_res){
					if($odb_res->ad_clicks >0 ){
						array_push( $insertQueryValues, "(" . $odb_res->ad_id .",".$odb_res->ad_thetime.",".$odb_res->ad_clicks.",'".$odb_res->ip_address."','".$odb_res->URL."','".$odb_res->browser."','".$odb_res->referrer. "')" );
					}
				}
				if(is_array($insertQueryValues) && count($insertQueryValues)>0){
					$insertQuery .= implode( ",", $insertQueryValues );
					$status = $wpdb->query($wpdb->prepare($insertQuery,array($new_db)));
				}
				if(isset($status) && $status !== false){
					error_log(json_encode($import_details));
					$imported = $imported+$offset;
					$import_details['imported'] = $imported;
					$import_details['total'] = $total_records;
					update_option('quads_import_data',$import_details);	
				}
			}
			sleep(1);
			}
			if($imported >= $total_records){
				$import_details['current_table'] = 'quads_single_stats_';
				$import_details['sub_table'] = 'impressions_mobile';
				$import_details['imported'] = 0;
				$import_details['total'] = 0;
				update_option('quads_import_data',$import_details);
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				die(quads_adsense_import_old_db());
			}


	}else if($import_details['current_table'] == 'quads_single_stats_' && $import_details['sub_table']){
		
		$device = 'mobile'; 
		$evnt_type = 'impressions'; 
		if(isset($import_details['sub_table']) && !empty($import_details['sub_table']))
		{
			$tmp =  explode('_', $import_details['sub_table']);
			$device = isset($tmp[1])?$tmp[1]:'mobile';
			$evnt_type = isset($tmp[0])?$tmp[0]:'impressions';
		}

		global $wpdb;
		$old_db = $wpdb->prefix.'quads_stats';
		$new_db = $wpdb->prefix.'quads_'.$evnt_type.'_'.$device;

		$offset = isset($import_details['offset'])?intval($import_details['offset']):50;
		$imported = isset($import_details['imported'])?intval($import_details['imported']):0;
		$total_records = isset($import_details['total'])?intval($import_details['total']):0;
	
		if(!$total_records){
		  $total_records = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE ad_device_name = %s AND ad_impressions > 0 ",array($old_db,$device)));
		}
		$loop_no = ceil($total_records/$offset);
		
		for($i=0;$i<$loop_no;$i++){
	
			$old_db_results = $wpdb->get_results($wpdb->prepare("SELECT ad_id,%i as counts ,ad_thetime FROM %i WHERE ad_device_name = %s LIMIT %d,%d;",array('ad_'.$evnt_type,$old_db,$device,$imported,$offset)));
			$wpdb->flush();
			if(is_array($old_db_results ) && count($old_db_results) > 0){
				foreach($old_db_results as $result){
					if(isset($res_array[$result->ad_id.'|'.$result->ad_thetime][$evnt_type]) && $res_array[$result->ad_id.'|'.$result->ad_thetime][$evnt_type] > 0 &&  $res_array[$result->ad_id.'|'.$result->ad_thetime]['ad_thetime'] == $result->ad_thetime && $res_array[$result->ad_id.'|'.$result->ad_thetime]['ad_id'] == $result->ad_id){
						$res_array[$result->ad_id.'|'.$result->ad_thetime][$evnt_type] += $result->counts;
					}else{
						$res_array[$result->ad_id.'|'.$result->ad_thetime][$evnt_type] = $result->counts;
						$res_array[$result->ad_id.'|'.$result->ad_thetime]['ad_id'] = $result->ad_id;
						$res_array[$result->ad_id.'|'.$result->ad_thetime]['ad_thetime'] = $result->ad_thetime;
					}
				}
				$insertQuery = "INSERT INTO %i (ad_id,%i,stats_date,stats_year) VALUES";
				$insertQueryValues = array();
				foreach($res_array as $r){
					if($r[$evnt_type] > 0) {
						array_push( $insertQueryValues, "(" . $r['ad_id'] .",".$r[$evnt_type].",".$r['ad_thetime'].",".gmdate('Y',$r['ad_thetime']). ")" );
					}
				}
				$insertQuery .= implode( ",", $insertQueryValues );
				$status = $wpdb->query($wpdb->prepare($insertQuery,array($new_db,'stats_'.$evnt_type)));
				error_log($status);
				if($status !== false){
					$imported = $imported+$offset;
					$import_details['imported'] = $imported;
					$import_details['total'] = $total_records;
					update_option('quads_import_data',$import_details);	
				}

			}
		 sleep(1);
		}

		if($imported >= $total_records){
			$import_details['current_table'] = 'quads_single_stats_';
			$import_details['sub_table'] = quads_getnext_table($import_details['sub_table']);
			$import_details['imported'] = 0;
			$import_details['total'] = 0;
			update_option('quads_import_data',$import_details);
			quads_adsense_import_old_db();
		}
	}else if(!$import_details['sub_table']){
		$reset  = array('status' => 'inactive','current_table'=>'quads_stats','sub_table'=>'','offset'=> 50,'imported' => 0,'total' => 0);
		update_option('quads_import_data' ,$reset);
		update_option('quads_db_import' ,true);
	}
}

}

function quads_import_log_table($data){
	if($data && isset($data['status']) && $data['status'] == 'active')
	{
		if(isset($data['current_table']) && $data['current_table'] == 'quads_stats'){
			global $wpdb;
			$old_db = $wpdb->prefix.'quads_stats';
			$new_db = $wpdb->prefix.'quads_logs';
			$since = strtotime("-30 day");
			$offset = isset($data['offset'])?intval($data['offset']):50;
			$imported = isset($data['imported'])?intval($data['imported']):0;
			$total_records = isset($data['total'])?intval($data['total']):0;
			if(!$total_records){
				$total_records = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE  ad_thetime > %d AND ad_clicks > 0",array($old_db,$since)));
			}
			$loop_no = ceil($total_records/$offset);
			for($i=0;$i<$loop_no;$i++){
			$old_db_results = $wpdb->get_results($wpdb->prepare("SELECT ad_id,ad_thetime,ad_clicks,ip_address,URL,browser,referrer FROM %i WHERE  ad_thetime > %d AND ad_clicks > 0 LIMIT %d,%d",array($old_db,$since,$data['imported'],$offset)));
			$wpdb->flush();
			if(is_array($old_db_results ) && count($old_db_results) > 0){
				$insertQuery = "INSERT INTO %i (ad_id,log_date,log_clicks,ip_address,log_url,browser,referrer) VALUES";
				$insertQueryValues = array();
				foreach($old_db_results as $odb_res){
					if($odb_res->ad_clicks >0 ){
						array_push( $insertQueryValues, "(" . $odb_res->ad_id .",".$odb_res->ad_thetime.",".$odb_res->ad_clicks.",'".$odb_res->ip_address."','".$odb_res->URL."','".$odb_res->browser."','".$odb_res->referrer. "')" );
					}
				}
				if(is_array($insertQueryValues) && count($insertQueryValues)>0){
					$insertQuery .= implode( ",", $insertQueryValues );
					$status = $wpdb->query($wpdb->prepare($insertQuery,array($new_db)));
				}
				if(isset($status) && $status !== false){
					error_log(json_encode($data));
					$imported = $imported+$offset;
					$data['imported'] = $imported;
					$data['total'] = $total_records;
					update_option('quads_import_data',$data);	
				}
			}
			sleep(1);
			}
			if($imported >= $total_records){
				$data['current_table'] = 'quads_single_stats_';
				$data['sub_table'] = 'impressions_mobile';
				$data['imported'] = 0;
				$data['total'] = 0;
				update_option('quads_import_data',$data);
				quads_adsense_import_old_db();
			}
		  }
	}
}


function quads_import_reports($data = null){
	if($data && isset($data['status']) && $data['status'] == 'active')
	{
		if(isset($data['current_table']) && $data['current_table'] == 'quads_single_stats_'){
			$params=[];
			$params['device']=$device = 'mobile'; 
			$params['evnt_type']=$evnt_type = 'impressions'; 
			if(isset($data['sub_table']) && !empty($data['sub_table']))
			{
				$tmp =  explode('_', $data['sub_table']);
				$params['device']=$device = isset($tmp[1])?$tmp[1]:'mobile';
				$params['evnt_type']=$evnt_type = isset($tmp[0])?$tmp[0]:'impressions';
			}

			global $wpdb;
			$params['old_db']=$old_db = $wpdb->prefix.'quads_stats';
			$params['new_db']=$new_db = $wpdb->prefix.'quads_'.$evnt_type.'_'.$device;

			$params['offset']=$offset = isset($data['offset'])?intval($data['offset']):50;
			$params['imported']=$imported = isset($data['imported'])?intval($data['imported']):0;
			$params['total_records']=$total_records = isset($data['total'])?intval($data['total']):0;
		
			if(!$total_records){
				$params['total_records']=$total_records = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE ad_device_name = %s AND ad_impressions > 0 ",array($old_db,$device)));
			}
			$loop_no = ceil($total_records/$offset);
			
			for($i=0;$i<$loop_no;$i++){
				$default  = array('status' => 'inactive','current_table'=>'quads_stats','sub_table'=>'','offset'=> 50,'imported' => 0,'total' => 0);
				$data = get_option('quads_import_data',$default);
				$old_db_results = $wpdb->get_results($wpdb->prepare("SELECT ad_id,%i as counts ,ad_thetime FROM %i WHERE ad_device_name = %s LIMIT %d,%d;",array('ad_'.$evnt_type,$params['old_db'],$params['device'],$params['imported'],$params['offset'])));
				$wpdb->flush();
				if(is_array($old_db_results ) && count($old_db_results) > 0){
					foreach($old_db_results as $result){
						if(isset($res_array[$result->ad_id.'|'.$result->ad_thetime][$evnt_type]) && $res_array[$result->ad_id.'|'.$result->ad_thetime][$evnt_type] > 0 &&  $res_array[$result->ad_id.'|'.$result->ad_thetime]['ad_thetime'] == $result->ad_thetime && $res_array[$result->ad_id.'|'.$result->ad_thetime]['ad_id'] == $result->ad_id){
							$res_array[$result->ad_id.'|'.$result->ad_thetime][$evnt_type] += $result->counts;
						}else{
							$res_array[$result->ad_id.'|'.$result->ad_thetime][$evnt_type] = $result->counts;
							$res_array[$result->ad_id.'|'.$result->ad_thetime]['ad_id'] = $result->ad_id;
							$res_array[$result->ad_id.'|'.$result->ad_thetime]['ad_thetime'] = $result->ad_thetime;
						}
					}
					$insertQuery = "INSERT INTO %i (ad_id,%i,stats_date,stats_year) VALUES";
					$insertQueryValues = array();
					foreach($res_array as $r){
							array_push( $insertQueryValues, "(" . $r['ad_id'] .",".$r[$evnt_type].",".$r['ad_thetime'].",".gmdate('Y',$r['ad_thetime']). ")" );
					}
					$insertQuery .= implode( ",", $insertQueryValues );
					$status = $wpdb->query($wpdb->prepare($insertQuery,array($params['new_db'],'stats_'.$evnt_type)));
					error_log($status);
					if($status !== false){
						$imported = $params['imported']+$params['offset'];
						$data['imported'] = $imported;
						$data['total'] = $params['total_records'];
						update_option('quads_import_data',$data);	
					}

				}
			 sleep(1);
			}

			if($imported >= $total_records){
				$data['current_table'] = 'quads_single_stats_';
				$data['sub_table'] = quads_getnext_table($data['sub_table']);
				$data['imported'] = 0;
				$data['total'] = 0;
				update_option('quads_import_data',$data);
				quads_adsense_import_old_db();
			}
		
		}

	}
}

function quads_getnext_table($current = null){
	if($current)
	{
		if($current == 'impressions_mobile'){
			return 'impressions_desktop';
		}else if($current == 'impressions_desktop'){
			return 'clicks_mobile';
		}else if($current == 'clicks_mobile'){
			return 'clicks_desktop';
		}else {
			return false;
		}
	}
	return false;
}

function quads_insert_reports_newdb($params){
				global $wpdb;
				$res_array=[];
				$evnt_type = $params['evnt_type'];
				error_log($evnt_type);

}


/************************************************
 * Adding ajax call to start DB migration
 ************************************************/

 add_action('wp_ajax_quads_start_newdb_migration', 'quads_start_newdb_migration');

 function quads_start_newdb_migration(){
	 $quads_cron_manual=['status'=>'fail','msg'=>'Invalid Action'];
	 if(current_user_can('manage_options') && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'quads_newdb_nonce')){
	 $quads_cron_manual=['status'=>'success','msg'=>'Cron Started'];
	 $rest_route = get_rest_url(null,'quads-adsense/import_old_db/');
	 $default  = array('status' => 'inactive','current_table'=>'quads_stats','sub_table'=>'','offset'=> 50,'imported' => 0,'total' => 0);
	$import_details = get_option('quads_import_data',$default);
	$import_details['status']  = 'active';
	update_option('quads_import_data',$import_details);
	   $response = wp_remote_get(esc_url($rest_route),
			 array(
				 'timeout'     => 3,
				 'httpversion' => '1.1',
			 )
		 );
	 }
	 echo json_encode($quads_cron_manual);
	 wp_die();
 
 }

 /************************************************
 * Adding ajax call to start DB migration
 ************************************************/

 add_action('wp_ajax_quads_hide_newdb_migration', 'quads_hide_newdb_migration');

 function quads_hide_newdb_migration(){
	 $quads_res=['status'=>'fail'];
	 if(current_user_can('manage_options') && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'quads_newdb_nonce')){
	 
		if(update_option('quads_v2_db_no_import',true)){
			$quads_res['status'] = 'success';
		}
	 }
	 echo json_encode($quads_res);
	 wp_die();
 
 }
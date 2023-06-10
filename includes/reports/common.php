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
				$msg = esc_html__( 'An error occurred while requesting account details.', 'advanced-ads' );
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

	$endDate = (isset($parameters['endDate'])&& $parameters['endDate'])?$parameters['endDate'] :date('Y-m-d');

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
			$endDate = (isset($parameters['cust_todate'])&& !empty($parameters['cust_todate']))?date("Y-m-d", strtotime(str_replace("/", "-",$parameters['cust_todate']))) :date("Y-m-d");
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
	$startDate = date("Y-m-d", $startDate);//date('Y-m-d',$startDate);
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
	
	$ad_id = $_GET['id'];
	$mydate = $_GET['date'];
	$day = $_GET['day'];
	$date_timestamp = strtotime($mydate);
	$new_date = date('d_m_Y', $date_timestamp);
	$todays_date = date("Y-m-d");
	$individual_ad_dates = '';
	$array_top5=array();


		$col_name_imprsn = 'impressions_'.$new_date;
		$col_name_clicks = 'clicks_'.$new_date;
	
		if( $day == "last_7_days" ){

			$loop = 7 ;
			$month= date("m");
			$date_= date("d");
			$year= date("Y");
			$dates_i = array() ;
			$dates_c = '';
			for( $i=0; $i<=$loop; $i++ ){
			$dates_i[] = ''.date('Y-m-d', mktime(0,0,0,$month,( $date_-$i ) , $year ) );
			}
			
			sort($dates_i);
			$from_date = $todays_date;
			$to_date = $dates_i[0];
			if($ad_id=='all') {
				$results_impresn_S = $wpdb->get_results($wpdb->prepare("SELECT date_impression,ad_date FROM 
			`{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ",array($to_date,$from_date)));
			}
			else
			{
				$results_impresn_S = $wpdb->get_results($wpdb->prepare("SELECT date_impression,ad_date FROM 
			`{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ",array($to_date,$from_date,$ad_id)));

				$results_impresn_S_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name",array(strtotime($to_date),strtotime($from_date),$ad_id)));

				foreach ($results_impresn_S as $key => $value) {
						foreach ($results_impresn_S_2 as $key2 => $value2) {
							if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
							}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
							}
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
			$dates_i_chart[] = ''.date('Y-m-d', mktime(0,0,0,$month,( $date_-$i ) , $year ) );
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
		
	
			if($ad_id=='all') {
				$results_click_S = $wpdb->get_results($wpdb->prepare("SELECT date_click,ad_date FROM 
			`{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ",array($to_date,$from_date)));
			}
			else
			{
				$results_click_S = $wpdb->get_results($wpdb->prepare("SELECT date_click,ad_date FROM 
			`{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ",array($to_date,$from_date,$ad_id)));

				$results_click_S_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name",array(strtotime($to_date),strtotime($from_date),$ad_id)));

				foreach ($results_click_S as $key => $value) {
						foreach ($results_click_S_2 as $key2 => $value2) {
							if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
								$value->desk_click = $value2->clicks;
							}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
								$value->mob_click = $value2->clicks;
							}
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
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id WHERE `{$wpdb->prefix}quads_single_stats_`.ad_date BETWEEN %s AND %s   GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($to_date,$from_date,5)));

				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",array(strtotime($to_date),strtotime($from_date),5)));

				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_click = $value2->clicks;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_click = $value2->clicks;
							}
						}
				}

				$array_top5 = array_values($results_top5);
				
			}
			

		}
		else if( $day == "this_month" ){
			
			$loop = 30 ;
			$month= date("m");
			$date_= date("d");
			$year= date("Y");
			$first_date_ = date('Y-m-d',strtotime('first day of this month'));
			$current_date_month_ = date('Y-m-d');
			if($ad_id=='all') {
				$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT date_impression,ad_date FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND '%s", array($first_date_,$current_date_month_)));
			}
			else
			{
				$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT date_impression,ad_date FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ",array($first_date_,$current_date_month_,$ad_id)));
			
				$results_impresn_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name",array(strtotime($first_date_),strtotime($current_date_month_),$ad_id)));

				foreach ($results_impresn_F as $key => $value) {
						foreach ($results_impresn_F_2 as $key2 => $value2) {
							if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
							}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
							}
						}
				}

			}
			$array = array_values($results_impresn_F);
			$ad_mob_imprsn = $ad_desk_imprsn = $ad_imprsn = 0;
			$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = array() ;
			
				
			$dates_i_chart = array();
		$first_date = date('Y-m-d',strtotime('first day of this month'));
		
		$first__date = $first_date; 
		$last_date_month = date("Y-m-t", strtotime($first__date));
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
		
		$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
		$desk_indi_impr_day_counts = $ad_desk_imprsn_values;
		$individual_impr_day_counts = $ad_imprsn_values;

		$_to_slash = $dates_i_chart;
		$get_impressions_specific_dates = str_replace('-','/',$_to_slash);
		if($ad_id=='all') {
			$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT date_click,ad_date FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ",array($first_date_,$current_date_month_)));
		}
		else
		{
			$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT date_click,ad_date FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ",array($first_date_,$current_date_month_,$ad_id)));

			$results_click_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name",array(strtotime($first_date_),strtotime($current_date_month_),$ad_id)));

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
		
		$mob_indi_click_day_counts = $ad_mob_click_values;
		$desk_indi_click_day_counts = $ad_desk_click_values;
		$individual_click_day_counts = $ad_click_values;
		if($ad_id=="all")
			{
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id WHERE `{$wpdb->prefix}quads_single_stats_`.ad_date BETWEEN %s AND %s  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($first_date_,$current_date_month_,5)));

				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",array(strtotime($first_date_),strtotime($current_date_month_),5)));

				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_click = $value2->clicks;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_click = $value2->clicks;
							}
						}
				}

				$array_top5 = array_values($results_top5);
				
			}
	}
		else if( $day == "last_month" ){
			
			$loop = 30 ;
			$year = date("Y");
			if($ad_id=='all') {
				$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT date_impression,ad_date from `{$wpdb->prefix}quads_single_stats_` WHERE month(ad_date)=month(now())-1 AND year(ad_date) = %s ",array($year)));
			}
			else
			{
				$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT date_impression,ad_date from `{$wpdb->prefix}quads_single_stats_` WHERE month(ad_date)=month(now())-1 AND year(ad_date) = %s AND `ad_id`=%d ",array($year,$ad_id)));

				$results_impresn_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE MONTH(FROM_UNIXTIME(ad_thetime)) = month(now())-1 AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime;",array($year,$ad_id)));
				
				foreach ($results_impresn_F as $key => $value) {
						foreach ($results_impresn_F_2 as $key2 => $value2) {
							if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
							}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
							}
						}
				}

			}
			
			$array = array_values($results_impresn_F);
			$ad_mob_imprsn = $ad_desk_imprsn = $ad_imprsn = 0;
			$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = array() ;
			$ad_mob_click_values = $ad_desk_click_values = $ad_click_values = array();
			
			$dates_i_chart= array();
			$year = date("Y",strtotime("-1 month"));
			$month = date("m",strtotime("-1 month"));
			
			for($d=1; $d<=31; $d++){
				$time=mktime(12, 0, 0, $month, $d, $year);          
				if (date('m', $time)==$month)       
					$dates_i_chart[] =date('Y-m-d', $time);
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
			$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
			$desk_indi_impr_day_counts = $ad_desk_imprsn_values;
			$individual_impr_day_counts = $ad_imprsn_values;

	

		$_to_slash = $dates_i_chart;
		$get_impressions_specific_dates = str_replace('-','/',$_to_slash);
		if($ad_id=='all') {
		$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT date_click,ad_date from `{$wpdb->prefix}quads_single_stats_` WHERE month(ad_date)=month(now())-1 AND year(ad_date) = %s ",array($year)));
		}
		else
		{
		$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT date_click,ad_date from `{$wpdb->prefix}quads_single_stats_` WHERE month(ad_date)=month(now())-1 AND year(ad_date) = %s AND `ad_id`=%d ",array($year,$ad_id)));

		$results_click_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE MONTH(FROM_UNIXTIME(ad_thetime)) = month(now())-1 AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime;",array($year,$ad_id)));
		
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

		$mob_indi_click_day_counts = $ad_mob_click_values;
		$desk_indi_click_day_counts = $ad_desk_click_values;
		$individual_click_day_counts = $ad_click_values;
		
		if($ad_id=="all")
			{
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id WHERE month(`{$wpdb->prefix}quads_single_stats_`.ad_date)=month(now())-1 AND year(`{$wpdb->prefix}quads_single_stats_`.ad_date) = %d  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($year,5)));

				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE MONTH(FROM_UNIXTIME(ad_thetime)) = month(now())-1 AND YEAR(FROM_UNIXTIME(ad_thetime)) = %d GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime;",array($year)));

				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_click = $value2->clicks;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_click = $value2->clicks;
							}
						}
				}
				$array_top5 = array_values($results_top5);	
			}
	}
		else if( $day == "all_time" ){
			
			$loop = 30 ;
			$month= date("m");
			$date_= date("d");
			$year= date("Y");
			$first_date_ = date('Y-m-d',strtotime('first day of this month'));
			$current_date_month_ = date('Y-m-d');
			if($ad_id=="all"){
				$results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) as date_impression, ad_year FROM `{$wpdb->prefix}quads_single_stats_`  group by ad_year ;"));
			}
			else
			{
			 $results_impresn_F = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) as date_impression, ad_year FROM `{$wpdb->prefix}quads_single_stats_` where ad_id = %d group by ad_year ;",$ad_id));	
			
			 $results_impresn_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE ad_id = %d GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name", $ad_id));

			 foreach ($results_impresn_F as $key => $value) {
			 		foreach ($results_impresn_F_2 as $key2 => $value2) {
			 			if(date("Y", $value2->ad_thetime) == $value->ad_year && $value2->ad_device_name == 'desktop'){
			 				$value->desk_imprsn += $value2->impressions;
			 			}elseif(date("Y", $value2->ad_thetime) == $value->ad_year && $value2->ad_device_name == 'mobile'){
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
			$mob_indi_impr_day_counts = $ad_mob_imprsn_values_;
			$desk_indi_impr_day_counts = $ad_desk_imprsn_values_;
			$individual_impr_day_counts = $ad_imprsn_values_;
			// individual_impr_day_counts
			$get_mob_impr_specific_dates = $ad_mob_imprsn_values;
			$get_desk_impr_specific_dates = $ad_desk_imprsn_values;
			$get_impressions_specific_dates = $ad_imprsn_values;
			if($ad_id=="all"){
				$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) as date_click, ad_year FROM `{$wpdb->prefix}quads_single_stats_`  group by ad_year; "));
			}
			else{
				$results_click_F = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) as date_click, ad_year FROM `{$wpdb->prefix}quads_single_stats_` where ad_id = %d group by ad_year ;",$ad_id));

				$results_click_F_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_thetime`, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE ad_id = %d GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name", $ad_id));

				foreach ($results_click_F as $key => $value) {
						foreach ($results_click_F_2 as $key2 => $value2) {
							if(date("Y", $value2->ad_thetime) == $value->ad_year && $value2->ad_device_name == 'desktop'){
								$value->mob_click += $value2->clicks;
							}elseif(date("Y", $value2->ad_thetime) == $value->ad_year && $value2->ad_device_name == 'mobile'){
								$value->desk_click += $value2->clicks;
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
			$ad_clicks += $value->date_click;
			$ad_mob_click_values[] = $value->ad_year;
			$ad_desk_click_values[] = $value->ad_year;
			$ad_click_values[] = $value->ad_year;
			$ad_mob_click_values_[] = $value->mob_click;
			$ad_desk_click_values_[] = $value->desk_click;
			$ad_click_values_[] = $value->date_click;
		}
			$mob_indi_click_day_counts = $ad_mob_click_values_;
			$desk_indi_click_day_counts = $ad_desk_click_values_;
			$individual_click_day_counts = $ad_click_values_;
			
			if($ad_id=="all")
			{
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id   GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",5));

				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",5));

				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_click = $value2->clicks;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_click = $value2->clicks;
							}
						}
				}

				$array_top5 = array_values($results_top5);	
			}
		
		}

		else if( $day == "this_year" ){
			
			$loop = 30 ;
			$month= date("m");
			$date_= date("d");
			$year= date("Y");
			$first_date_ = date('Y-m-d',strtotime('first day of this month'));
			$current_date_month_ = date('Y-m-d');
			if($ad_id=="all")
			{
			$jan = $wpdb->get_results($wpdb->prepare(" SELECT SUM(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ; ",array(1,$year)));
			$feb = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(2,$year)));
			$mar = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s  ;",array(3,$year)));
			$apr = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(4,$year)));
			$may = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(5,$year)));
			$jun = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(6,$year)));
			$july = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(7,$year)));
			$aug = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(8,$year)));
			$sep = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(9,$year)));
			$oct = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(10,$year)));
			$nov = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(11,$year)));
			$dec = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(12,$year)));
			}
			
			else
			{
			$jan = $wpdb->get_results($wpdb->prepare(" SELECT SUM(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ; ",array(1,$year,$ad_id)));
			$feb = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(2,$year,$ad_id)));
			$mar = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(3,$year,$ad_id)));
			$apr = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(4,$year,$ad_id)));
			$may = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(5,$year,$ad_id)));
			$jun = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(6,$year,$ad_id)));
			$july = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(7,$year,$ad_id)));
			$aug = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(8,$year,$ad_id)));
			$sep = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(9,$year,$ad_id)));
			$oct = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(10,$year,$ad_id)));
			$nov = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(11,$year,$ad_id)));
			$dec = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_impression) AS date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=%d ;",array(12,$year,$ad_id)));	
			
			//Mobile	
			$jan_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(1,$year,$ad_id)));
			$feb_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(2,$year,$ad_id)));
			$mar_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(3,$year,$ad_id)));
			$apr_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(4,$year,$ad_id)));
			$may_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(5,$year,$ad_id)));
			$jun_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(6,$year,$ad_id)));
			$jul_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(7,$year,$ad_id)));
			$aug_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(8,$year,$ad_id)));
			$sep_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(9,$year,$ad_id)));
			$oct_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(10,$year,$ad_id)));
			$nov_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(11,$year,$ad_id)));
			$dec_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(12,$year,$ad_id)));
			
			//Desktop
			$jan_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(1,$year,$ad_id)));
			$feb_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(2,$year,$ad_id)));
			$mar_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(3,$year,$ad_id)));
			$apr_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(4,$year,$ad_id)));
			$may_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(5,$year,$ad_id)));
			$jun_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(6,$year,$ad_id)));
			$jul_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(7,$year,$ad_id)));
			$aug_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(8,$year,$ad_id)));
			$sep_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(9,$year,$ad_id)));
			$oct_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(10,$year,$ad_id)));
			$nov_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(11,$year,$ad_id)));
			$dec_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_impressions) AS impressions FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(12,$year,$ad_id)));

			}

			$jan = $jan[0]->date_impression;
			$feb = $feb[0]->date_impression;
			$mar = $mar[0]->date_impression;
			$apr = $apr[0]->date_impression;
			$may = $may[0]->date_impression;
			$jun = $jun[0]->date_impression;
			$july = $july[0]->date_impression;
			$aug = $aug[0]->date_impression;
			$sep = $sep[0]->date_impression;
			$oct = $oct[0]->date_impression;
			$nov = $nov[0]->date_impression;
			$dec = $dec[0]->date_impression;

			//Mobile
			$jan_mob = $jan_mob[0]->impressions;
			$feb_mob = $feb_mob[0]->impressions;
			$mar_mob = $mar_mob[0]->impressions;
			$apr_mob = $apr_mob[0]->impressions;
			$may_mob = $may_mob[0]->impressions;
			$jun_mob = $jun_mob[0]->impressions;
			$jul_mob = $jul_mob[0]->impressions;
			$aug_mob = $aug_mob[0]->impressions;
			$sep_mob = $sep_mob[0]->impressions;
			$oct_mob = $oct_mob[0]->impressions;
			$nov_mob = $nov_mob[0]->impressions;
			$dec_mob = $dec_mob[0]->impressions;

			//Desktop
			$jan_desk = $jan_desk[0]->impressions;
			$feb_desk = $feb_desk[0]->impressions;
			$mar_desk = $mar_desk[0]->impressions;
			$apr_desk = $apr_desk[0]->impressions;
			$may_desk = $may_desk[0]->impressions;
			$jun_desk = $jun_desk[0]->impressions;
			$jul_desk = $jul_desk[0]->impressions;
			$aug_desk = $aug_desk[0]->impressions;
			$sep_desk = $sep_desk[0]->impressions;
			$oct_desk = $oct_desk[0]->impressions;
			$nov_desk = $nov_desk[0]->impressions;
			$dec_desk = $dec_desk[0]->impressions;

			if($jan== NULL){$jan = 0;}if($feb== NULL){$feb = 0;}if($mar== NULL){$mar = 0;}
			if($apr== NULL){$apr = 0;}if($may== NULL){$may = 0;}if($jun== NULL){$jun = 0;}
			if($july== NULL){$july = 0;}if($aug== NULL){$aug = 0;}if($sep== NULL){$sep = 0;}
			if($oct== NULL){$oct = 0;}if($nov== NULL){$nov = 0;}if($dec== NULL){$dec = 0;}

			//Mobile
			if($jan_mob== NULL){$jan_mob = 0;}if($feb_mob== NULL){$feb_mob = 0;}if($mar_mob== NULL){$mar_mob = 0;}
			if($apr_mob== NULL){$apr_mob = 0;}if($may_mob== NULL){$may_mob = 0;}if($jun_mob== NULL){$jun_mob = 0;}
			if($jul_mob== NULL){$jul_mob = 0;}if($aug_mob== NULL){$aug_mob = 0;}if($sep_mob== NULL){$sep_mob = 0;}
			if($oct_mob== NULL){$oct_mob = 0;}if($nov_mob== NULL){$nov_mob = 0;}if($dec_mob== NULL){$dec_mob = 0;}

			//Desktop
			if($jan_desk== NULL){$jan_desk = 0;}if($feb_desk== NULL){$feb_desk = 0;}if($mar_desk== NULL){$mar_desk = 0;}
			if($apr_desk== NULL){$apr_desk = 0;}if($may_desk== NULL){$may_desk = 0;}if($jun_desk== NULL){$jun_desk = 0;}
			if($jul_desk== NULL){$jul_desk = 0;}if($aug_desk== NULL){$aug_desk = 0;}if($sep_desk== NULL){$sep_desk = 0;}
			if($oct_desk== NULL){$oct_desk = 0;}if($nov_desk== NULL){$nov_desk = 0;}if($dec_desk== NULL){$dec_desk = 0;}

			$ad_mob_imprsn_values = $ad_desk_imprsn_values = $ad_imprsn_values = array();

			$ad_imprsn_values = [$jan,$feb, $mar,$apr,$may,$jun,$july,$aug,$sep,$oct,$nov,$dec];
			$ad_imprsn = $jan+$feb+ $mar+$apr+$may+$jun+$july+$aug+$sep+$oct+$nov+$dec;
			
			$ad_mob_imprsn_values = [$jan_mob,$feb_mob,$mar_mob,$apr_mob,$may_mob,$jun_mob,$jul_mob,$aug_mob,$sep_mob,$oct_mob,$nov_mob,$dec_mob];
			$ad_mob_imprsn = $jan_mob+$feb_mob+$mar_mob+$apr_mob+$may_mob+$jun_mob+$jul_mob+$aug_mob+$sep_mob+$oct_mob+$nov_mob+$dec_mob;
			
			$ad_desk_imprsn_values = [$jan_desk,$feb_desk,$mar_desk,$apr_desk,$may_desk,$jun_desk,$jul_desk,$aug_desk,$sep_desk,$oct_desk,$nov_desk,$dec_desk];
			$ad_desk_imprsn = $jan_desk+$feb_desk+$mar_desk+$apr_desk+$may_desk+$jun_desk+$jul_desk+$aug_desk+$sep_desk+$oct_desk+$nov_desk+$dec_desk;

			$mob_indi_impr_day_counts = $ad_mob_imprsn_values;
			$desk_indi_impr_day_counts = $ad_desk_imprsn_values;
			$individual_impr_day_counts = $ad_imprsn_values;
			$individual_ad_dates = [1];
			if($ad_id=="all")
			{
				$jan = $wpdb->get_results($wpdb->prepare(" SELECT SUM(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s  ;",array(1,$year)));
			$feb = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(2,$year)));
			$mar = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(3,$year)));
			$apr = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(4,$year)));
			$may = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(5,$year)));
			$jun = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(6,$year)));
			$july = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(7,$year)));
			$aug = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(8,$year)));
			$sep = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(9,$year)));
			$oct = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(10,$year)));
			$nov = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(11,$year)));
			$dec = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s ;",array(12,$year)));
			}
			else
			{
			$jan = $wpdb->get_results($wpdb->prepare(" SELECT SUM(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND `ad_id`=$ad_id ;",array(1,$year,$ad_id)));
			$feb = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(2,$year,$ad_id)));
			$mar = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(3,$year,$ad_id)));
			$apr = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(4,$year,$ad_id)));
			$may = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(5,$year,$ad_id)));
			$jun = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(6,$year,$ad_id)));
			$july = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(7,$year,$ad_id)));
			$aug = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(8,$year,$ad_id)));
			$sep = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(9,$year,$ad_id)));
			$oct = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(10,$year,$ad_id)));
			$nov = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(11,$year,$ad_id)));
			$dec = $wpdb->get_results($wpdb->prepare(" SELECT sum(date_click) AS date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE MONTH(ad_date) = %d AND year(ad_date) = %s AND ad_id=$ad_id ;",array(12,$year,$ad_id)));	

			//Mobile	
			$jan_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(1,$year,$ad_id)));
			$feb_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(2,$year,$ad_id)));
			$mar_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(3,$year,$ad_id)));
			$apr_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(4,$year,$ad_id)));
			$may_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(5,$year,$ad_id)));
			$jun_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(6,$year,$ad_id)));
			$jul_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(7,$year,$ad_id)));
			$aug_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(8,$year,$ad_id)));
			$sep_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(9,$year,$ad_id)));
			$oct_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(10,$year,$ad_id)));
			$nov_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(11,$year,$ad_id)));
			$dec_mob = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'mobile' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(12,$year,$ad_id)));
			
			//Desktop
			$jan_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(1,$year,$ad_id)));
			$feb_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(2,$year,$ad_id)));
			$mar_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(3,$year,$ad_id)));
			$apr_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(4,$year,$ad_id)));
			$may_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(5,$year,$ad_id)));
			$jun_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(6,$year,$ad_id)));
			$jul_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(7,$year,$ad_id)));
			$aug_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(8,$year,$ad_id)));
			$sep_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(9,$year,$ad_id)));
			$oct_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(10,$year,$ad_id)));
			$nov_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(11,$year,$ad_id)));
			$dec_desk = $wpdb->get_results($wpdb->prepare(" SELECT SUM(ad_clicks) AS clicks FROM `{$wpdb->prefix}quads_stats` WHERE `ad_device_name` = 'desktop' AND MONTH(FROM_UNIXTIME(ad_thetime)) = %d AND YEAR(FROM_UNIXTIME(ad_thetime)) = %s AND `ad_id`=%d ; ",array(12,$year,$ad_id)));

			}
			
			
			$jan = $jan[0]->date_click;$feb = $feb[0]->date_click;$mar = $mar[0]->date_click;$apr = $apr[0]->date_click;$may = $may[0]->date_click;$jun = $jun[0]->date_click;$july = $july[0]->date_click;$aug = $aug[0]->date_click;$sep = $sep[0]->date_click;$oct = $oct[0]->date_click;$nov = $nov[0]->date_click;$dec = $dec[0]->date_click;

			//Mobile
			$jan_mob = $jan_mob[0]->clicks;$feb_mob = $feb_mob[0]->clicks;$mar_mob = $mar_mob[0]->clicks;$apr_mob = $apr_mob[0]->clicks;$may_mob = $may_mob[0]->clicks;$jun_mob = $jun_mob[0]->clicks;$jul_mob = $jul_mob[0]->clicks;$aug_mob = $aug_mob[0]->clicks;$sep_mob = $sep_mob[0]->clicks;$oct_mob = $oct_mob[0]->clicks;$nov_mob = $nov_mob[0]->clicks;$dec_mob = $dec_mob[0]->clicks;

			//Desktop
			$jan_desk = $jan_desk[0]->clicks;$feb_desk = $feb_desk[0]->clicks;$mar_desk = $mar_desk[0]->clicks;$apr_desk = $apr_desk[0]->clicks;$may_desk = $may_desk[0]->clicks;$jun_desk = $jun_desk[0]->clicks;$jul_desk = $jul_desk[0]->clicks;$aug_desk = $aug_desk[0]->clicks;$sep_desk = $sep_desk[0]->clicks;$oct_desk = $oct_desk[0]->clicks;$nov_desk = $nov_desk[0]->clicks;$dec_desk = $dec_desk[0]->clicks;

			if($jan== NULL){$jan = 0;}if($feb== NULL){$feb = 0;}if($mar== NULL){$mar = 0;}
			if($apr== NULL){$apr = 0;}if($may== NULL){$may = 0;}if($jun== NULL){$jun = 0;}
			if($july== NULL){$july = 0;}if($aug== NULL){$aug = 0;}if($sep== NULL){$sep = 0;}
			if($oct== NULL){$oct = 0;}if($nov== NULL){$nov = 0;}if($dec== NULL){$dec = 0;}

			//Mobile
			if($jan_mob== NULL){$jan_mob = 0;}if($feb_mob== NULL){$feb_mob = 0;}if($mar_mob== NULL){$mar_mob = 0;}
			if($apr_mob== NULL){$apr_mob = 0;}if($may_mob== NULL){$may_mob = 0;}if($jun_mob== NULL){$jun_mob = 0;}
			if($jul_mob== NULL){$jul_mob = 0;}if($aug_mob== NULL){$aug_mob = 0;}if($sep_mob== NULL){$sep_mob = 0;}
			if($oct_mob== NULL){$oct_mob = 0;}if($nov_mob== NULL){$nov_mob = 0;}if($dec_mob== NULL){$dec_mob = 0;}

			//Desktop
			if($jan_desk== NULL){$jan_desk = 0;}if($feb_desk== NULL){$feb_desk = 0;}if($mar_desk== NULL){$mar_desk = 0;}
			if($apr_desk== NULL){$apr_desk = 0;}if($may_desk== NULL){$may_desk = 0;}if($jun_desk== NULL){$jun_desk = 0;}
			if($jul_desk== NULL){$jul_desk = 0;}if($aug_desk== NULL){$aug_desk = 0;}if($sep_desk== NULL){$sep_desk = 0;}
			if($oct_desk== NULL){$oct_desk = 0;}if($nov_desk== NULL){$nov_desk = 0;}if($dec_desk== NULL){$dec_desk = 0;}
			
			$ad_mob_click_values = $ad_desk_click_values = $ad_click_values = array();

			$ad_clicks = $jan+$feb+ $mar+$apr+$may+$jun+$july+$aug+$sep+$oct+$nov+$dec;
			$ad_click_values = [$jan,$feb, $mar,$apr,$may,$jun,$july,$aug,$sep,$oct,$nov,$dec];

			$ad_mob_clicks = $jan_mob+$feb_mob+$mar_mob+$apr_mob+$may_mob+$jun_mob+$jul_mob+$aug_mob+$sep_mob+$oct_mob+$nov_mob+$dec_mob;
			$ad_mob_click_values = [$jan_mob,$feb_mob,$mar_mob,$apr_mob,$may_mob,$jun_mob,$jul_mob,$aug_mob,$sep_mob,$oct_mob,$nov_mob,$dec_mob];
			
			$ad_desk_clicks = $jan_desk+$feb_desk+$mar_desk+$apr_desk+$may_desk+$jun_desk+$jul_desk+$aug_desk+$sep_desk+$oct_desk+$nov_desk+$dec_desk;
			$ad_desk_click_values = [$jan_desk,$feb_desk,$mar_desk,$apr_desk,$may_desk,$jun_desk,$jul_desk,$aug_desk,$sep_desk,$oct_desk,$nov_desk,$dec_desk];

			$mob_indi_click_day_counts = $ad_mob_click_values;
			$desk_indi_click_day_counts = $ad_desk_click_values;
			$individual_click_day_counts = $ad_click_values;
			
			if($ad_id=="all")
			{
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id   WHERE year(`{$wpdb->prefix}quads_single_stats_`.ad_date) = %d GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($year,5)));

				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE YEAR(FROM_UNIXTIME(`{$wpdb->prefix}quads_stats`.ad_thetime)) = %d GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",array($year)));

				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_click = $value2->clicks;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_click = $value2->clicks;
							}
						}
				}

				$array_top5 = array_values($results_top5);	
			}
	}
	else if( $day == "yesterday" ){
		
		$yesterday_date = date('Y-m-d',strtotime("-1 days"));
		if($ad_id=="all")
		{
			$results_impresn = $wpdb->get_results($wpdb->prepare("SELECT date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE `ad_date` = %s ",$yesterday_date ));
		}
		else
		{
			$results_impresn = $wpdb->get_results($wpdb->prepare("SELECT date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE `ad_id` = %d AND `ad_date` = %s " ,array($ad_id,$yesterday_date)));

			$results_impresn_2 = $wpdb->get_results($wpdb->prepare("SELECT SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d AND `ad_thetime` = %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name",array( $ad_id, strtotime($yesterday_date))));

			foreach ($results_impresn as $key => $value) {
					foreach ($results_impresn_2 as $key2 => $value2) {
						if($value2->ad_device_name == 'desktop'){
							$value->desk_imprsn = $value2->impressions;
						}elseif($value2->ad_device_name == 'mobile'){
							$value->mob_imprsn = $value2->impressions;
						}
					}
			}

		}
		
		$array = array_values($results_impresn);
		$ad_mob_imprsn = 0;		
		$ad_desk_imprsn = 0;		
		$ad_imprsn = 0;		
		
		foreach ($array as $key => $value) {
			$ad_mob_imprsn += $value->mob_imprsn;
			$ad_desk_imprsn += $value->desk_imprsn;
			$ad_imprsn += $value->date_impression;
		}
		$get_impressions_specific_dates = str_replace('-','/',$yesterday_date);
		if($ad_id=="all")
		{	
			$results_click = $wpdb->get_results($wpdb->prepare("SELECT date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE `ad_date` = %s ",$yesterday_date));
		}
		else
		{
			$results_click = $wpdb->get_results($wpdb->prepare("SELECT date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE `ad_id` = %d AND `ad_date` = %s " ,array($ad_id,$yesterday_date) ));

			$results_click_2 = $wpdb->get_results($wpdb->prepare("SELECT SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d AND `ad_thetime` = %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name",array( $ad_id, strtotime($yesterday_date))));

			foreach ($results_click as $key => $value) {
					foreach ($results_click_2 as $key2 => $value2) {
						if($value2->ad_device_name == 'desktop'){
							$value->desk_click = $value2->clicks;
						}elseif($value2->ad_device_name == 'mobile'){
							$value->mob_click = $value2->clicks;
						}
					}
			}

		}
		
		$array_c = array_values($results_click);
		$ad_mob_clicks = 0;
		$ad_desk_clicks = 0;
		$ad_clicks = 0;
		foreach ($array_c as $key => $value) {
			$ad_mob_clicks += $value->mob_click;
			$ad_desk_clicks += $value->desk_click;
			$ad_clicks += $value->date_click;
		}
		
		if($ad_id=="all")
			{
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id   WHERE `{$wpdb->prefix}quads_single_stats_`.`ad_date` = %s  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($yesterday_date,5)));
				$unix_yesterday_date = "'".intval(strtotime($yesterday_date))."'";
				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.`ad_thetime` = $unix_yesterday_date GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",5));

				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_click = $value2->clicks;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_click = $value2->clicks;
							}
						}
				}

				$array_top5 = array_values($results_top5);	
			}
	}
	else if( $day == "today" ) {
		if($ad_id=="all")
			{
			$results_impresn_t = $wpdb->get_results($wpdb->prepare("SELECT date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE `ad_date` = %s " ,$todays_date));	
			}
			else
			{
			$results_impresn_t = $wpdb->get_results($wpdb->prepare("SELECT date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE `ad_id` = %d AND `ad_date` = %s " ,array( $ad_id,$todays_date)));	

			$results_impresn_t_2 = $wpdb->get_results($wpdb->prepare("SELECT SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d AND `ad_thetime` = %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name",array( $ad_id, strtotime($todays_date))));

			foreach ($results_impresn_t as $key => $value) {
					foreach ($results_impresn_t_2 as $key2 => $value2) {
						if($value2->ad_device_name == 'desktop'){
							$value->desk_imprsn = $value2->impressions;
						}elseif($value2->ad_device_name == 'mobile'){
							$value->mob_imprsn = $value2->impressions;
						}
					}
			}

			}
		
		$array_i_t = array_values($results_impresn_t);
		$ad_mob_imprsn = 0;		
		$ad_desk_imprsn = 0;		
		$ad_imprsn = 0;		
		foreach ($array_i_t as $key => $value) {
			$ad_mob_imprsn += $value->mob_imprsn;
			$ad_desk_imprsn += $value->desk_imprsn;
			$ad_imprsn += $value->date_impression;
		}
		$get_impressions_specific_dates = str_replace('-','/',$todays_date);		
		if($ad_id=="all")
		{
			$results_click_t = $wpdb->get_results($wpdb->prepare("SELECT date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE `ad_date` = %s " ,$todays_date ));
		}
		else
		{
			$results_click_t = $wpdb->get_results($wpdb->prepare("SELECT date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE `ad_id` = %d AND `ad_date` = %s " ,array( $ad_id,$todays_date) ));

			$results_click_t_2 = $wpdb->get_results($wpdb->prepare("SELECT SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d AND `ad_thetime` = %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `{$wpdb->prefix}quads_stats`.ad_device_name",array( $ad_id, strtotime($todays_date))));

			foreach ($results_click_t as $key => $value) {
					foreach ($results_click_t_2 as $key2 => $value2) {
						if($value2->ad_device_name == 'desktop'){
							$value->desk_click = $value2->clicks;
						}elseif($value2->ad_device_name == 'mobile'){
							$value->mob_click = $value2->clicks;
						}
					}
			}

		}
		
		$array_c_t = array_values($results_click_t);
		$ad_clicks = 0;
		foreach ($array_c_t as $key => $value) {
			$ad_mob_clicks += $value->mob_click;
			$ad_desk_clicks += $value->desk_click;
			$ad_clicks += $value->date_click;
		}
		
		if($ad_id=="all")
			{
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id  WHERE `{$wpdb->prefix}quads_single_stats_`.`ad_date` = %s  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($todays_date,5)));

				$unix_todays_date = "'".intval(strtotime($todays_date))."'";

				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.`ad_thetime` = $unix_todays_date GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",5));
				
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
			}

	}
	else if( $day == "custom" ) {
		$fromdate = $_GET["fromdate"];
		$todate = $_GET["todate"];
		$get_from = preg_replace('/(.*?)-(.*?)-(.*?)T(.*)/', '$1-$2-$3', $fromdate);
		$get_to = preg_replace('/(.*?)-(.*?)-(.*?)T(.*)/', '$1-$2-$3', $todate);
		if($ad_id=="all")
			{
				$results_impresn_C_ = $wpdb->get_results($wpdb->prepare(" SELECT ad_date, date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ",array($get_from,$get_to)));

				$results_impresn_C_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_thetime, SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name",array(strtotime($get_from),strtotime($get_to))));

				foreach ($results_impresn_C_ as $key => $value) {
						foreach ($results_impresn_C_2 as $key2 => $value2) {
							if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
							}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
							}
						}
				}

			}
			else
			{
				$results_impresn_C_ = $wpdb->get_results($wpdb->prepare(" SELECT ad_date, date_impression FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ",array($get_from,$get_to,$ad_id)));

				$results_impresn_C_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_thetime, SUM(ad_impressions) AS impressions, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name",array(strtotime($get_from),strtotime($get_to),$ad_id)));

				foreach ($results_impresn_C_ as $key => $value) {
						foreach ($results_impresn_C_2 as $key2 => $value2) {
							if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
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
				$ad_imprsn_values .= $value->date_impression.',';
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
			$results_click_S = $wpdb->get_results($wpdb->prepare(" SELECT ad_date, date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s ",array($get_from,$get_to)));

			$results_click_S_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_thetime, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name",array(strtotime($get_from),strtotime($get_to),$ad_id)));

			foreach ($results_click_S as $key => $value) {
					foreach ($results_click_S_2 as $key2 => $value2) {
						if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
							$value->desk_click = $value2->clicks;
						}elseif($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'mobile'){
							$value->mob_click = $value2->clicks;
						}
					}
			}

		}
		else
		{
		 $results_click_S = $wpdb->get_results($wpdb->prepare(" SELECT ad_date, date_click FROM `{$wpdb->prefix}quads_single_stats_` WHERE ad_date BETWEEN %s AND %s AND `ad_id` = %d ",array($get_from,$get_to,$ad_id)));	
		
		 $results_click_S_2 = $wpdb->get_results($wpdb->prepare("SELECT ad_thetime, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s AND `ad_id` = %d GROUP BY `wp_quads_stats`.ad_id, `wp_quads_stats`.ad_thetime, `wp_quads_stats`.ad_device_name",array(strtotime($get_from),strtotime($get_to),$ad_id)));

		 foreach ($results_click_S as $key => $value) {
		 		foreach ($results_click_S_2 as $key2 => $value2) {
		 			if($value2->ad_thetime == strtotime($value->ad_date) && $value2->ad_device_name == 'desktop'){
		 				$value->desk_click = $value2->clicks;
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
			$ad_click_values .= $value->date_click.',';
		}
		$remove_mob_comma_click = substr($ad_mob_click_values, 0, -1);
		$remove_desk_comma_click = substr($ad_desk_click_values, 0, -1);
		$remove_comma_click = substr($ad_click_values, 0, -1);
		$mob_indi_click_day_counts = explode(",",$remove_mob_comma_click);
		$desk_indi_click_day_counts = explode(",",$remove_desk_comma_click);
		$individual_click_day_counts = explode(",",$remove_comma_click);
		
		if($ad_id=="all")
			{
				$results_top5 = $wpdb->get_results($wpdb->prepare("SELECT `{$wpdb->prefix}posts`.ID,`{$wpdb->prefix}posts`.post_title,SUM(`{$wpdb->prefix}quads_single_stats_`.date_impression) as total_impression ,SUM(`{$wpdb->prefix}quads_single_stats_`.date_click)as total_click from `{$wpdb->prefix}quads_single_stats_` INNER JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}posts`.ID=`{$wpdb->prefix}quads_single_stats_`.ad_id   WHERE `{$wpdb->prefix}quads_single_stats_`.`ad_date` BETWEEN %s AND %s  GROUP BY `{$wpdb->prefix}posts`.post_title ORDER BY `{$wpdb->prefix}quads_single_stats_`.date_click DESC  LIMIT %d",array($get_from,$get_to,5)));
				$results_top5_2 = $wpdb->get_results($wpdb->prepare("SELECT `ad_id`, `ad_thetime`, SUM(ad_impressions) AS impressions, SUM(ad_clicks) AS clicks, ad_device_name FROM `{$wpdb->prefix}quads_stats` WHERE `{$wpdb->prefix}quads_stats`.ad_thetime BETWEEN %s AND %s GROUP By `{$wpdb->prefix}quads_stats`.ad_id, `{$wpdb->prefix}quads_stats`.ad_device_name ORDER BY `{$wpdb->prefix}quads_stats`.ad_thetime DESC",array(strtotime($get_from),strtotime($get_to),5)));

				foreach ($results_top5 as $key => $value) {
						foreach ($results_top5_2 as $key2 => $value2) {
							if($value2->ad_id == $value->ID && $value2->ad_device_name == 'desktop'){
								$value->desk_imprsn = $value2->impressions;
								$value->desk_click = $value2->clicks;
							}elseif($value2->ad_id == $value->ID && $value2->ad_device_name == 'mobile'){
								$value->mob_imprsn = $value2->impressions;
								$value->mob_click = $value2->clicks;
							}
						}
				}
				
				$array_top5 = array_values($results_top5);	
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
	// if ( isset( $options['accounts'][ $account ] ) ) {

	// 	if ( time() > $options['accounts'][ $account ]['expires'] ) {
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
				'msg' => esc_html__( 'It seems that some changes have been made in the Quads Ads settings. Please refresh this page.', 'advanced-ads' ),
				'reload' => true,
			);
		} else {
			// No account at all.
			return array(
				'status' => false,
				'msg' => wp_kses( sprintf( __( 'Advanced Ads does not have access to your account (<code>%s</code>) anymore.', 'advanced-ads' ), $account ), array( 'code' => true ) ),
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
			'msg'    => sprintf( esc_html__( 'error while renewing access token for "%s"', 'advanced-ads' ), $account ),
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
				'msg'    => sprintf( esc_html__( 'invalid response received while renewing access token for "%s"', 'advanced-ads' ),  $account ),
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
				$server_timezone = date('e');
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
            $result = $wpdb->get_results($wpdb->prepare("SELECT SUM(`ad_clicks`) as `clicks`, SUM(`ad_impressions`) as `impressions` FROM `{$wpdb->prefix}quads_stats` WHERE `ad_id` = %d;", $ad_id), ARRAY_A);
           
            $ad_stats['impressions'] = $result[0]['impressions'];
            $ad_stats['clicks']      = $result[0]['clicks'];
                                    
            break;
        
        case 'fetchAllBy':

            
            if($ad_id){
                
                $result = $wpdb->get_results($wpdb->prepare("SELECT *FROM `{$wpdb->prefix}quads_stats` WHERE `ad_thetime` = %d AND `ad_id` = %d;", $date, $ad_id), ARRAY_A);
                
            }else{
            
                $result = $wpdb->get_results($wpdb->prepare("SELECT *FROM `{$wpdb->prefix}quads_stats` WHERE `ad_thetime` = %d;", $date), ARRAY_A);

                
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
								$endDate = (isset($parameters['cust_todate'])&& !empty($parameters['cust_todate']))?strtotime($parameters['cust_todate']) :strtotime('now');;
								break;
							case 'all_time':
								$startDate = strtotime('-3 year');
								break;
							default:
								$startDate = strtotime(" -6 day");
								break;
						}
						$ad_thetime = $wpdb->prepare('where ad_thetime BETWEEN %d AND %d',array($startDate,$endDate)); 
					
					}
				}
				$search_param = '';
				if(isset($parameters['search_param']) && !empty($parameters['search_param'])){
					if(empty($ad_thetime)){
						$search_param = $wpdb->prepare("where ad_id  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						ad_device_name  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						ip_address  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						url  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						browser  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						referrer  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%'   "); 
					}else {
					
						$search_param = $wpdb->prepare("and ( ad_id  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						ad_device_name  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						ip_address  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						url  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						browser  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' or
						referrer  LIKE '%".$wpdb->esc_like($parameters['search_param'])."%' )  "); 
					}

				}
			$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}quads_stats` ". $ad_thetime ." ".$search_param ." LIMIT %d, %d",array($offset,$items_per_page)), ARRAY_A);
					$ad_stats = $results;	
					$result_total = $wpdb->get_row($wpdb->prepare("SELECT count(*) as total FROM `{$wpdb->prefix}quads_stats` ". $ad_thetime ." ".$search_param), ARRAY_A);
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

					return $response;

				break;
        default:
            break;
    }            
    return $ad_stats;
}
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

function has_token( $adsense_id = '' ) {
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
	$results = $wpdb->get_results( "SELECT * FROM `wp_quads_stats` ");
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
                              <td class="b_in">'.$row->Beginning_of_post.'</td>
                              <td class="b_in">'.$row->End_of_post.'</td>
                              <td class="b_in">'.$row->Middle_of_post.'</td>
                              <td class="b_in">'.$row->After_more_tag.'</td>
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
	//    $css_dir = QUADS_PLUGIN_URL . 'assets/css/';

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
	//    wp_localize_script( 'quads-charts-js' ,'');
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
						$ad_thetime = $wpdb->prepare('where ad_thetime BETWEEN '.$startDate.' AND '.$endDate); 
					
					}
				}
				$search_param = '';
				if(isset($parameters['search_param']) && !empty($parameters['search_param'])){
					if(empty($ad_thetime)){
						$search_param = $wpdb->prepare("where ad_id  LIKE '%".$parameters['search_param']."%' or
						ad_device_name  LIKE '%".$parameters['search_param']."%' or
						ip_address  LIKE '%".$parameters['search_param']."%' or
						url  LIKE '%".$parameters['search_param']."%' or
						browser  LIKE '%".$parameters['search_param']."%' or
						referrer  LIKE '%".$parameters['search_param']."%'   "); 
					}else {
					
						$search_param = $wpdb->prepare("and ( ad_id  LIKE '%".$parameters['search_param']."%' or
						ad_device_name  LIKE '%".$parameters['search_param']."%' or
						ip_address  LIKE '%".$parameters['search_param']."%' or
						url  LIKE '%".$parameters['search_param']."%' or
						browser  LIKE '%".$parameters['search_param']."%' or
						referrer  LIKE '%".$parameters['search_param']."%' )  "); 
					}

				}
			$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}quads_stats` ". $ad_thetime ." ".$search_param ." LIMIT " . $offset . "," . $items_per_page), ARRAY_A);
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
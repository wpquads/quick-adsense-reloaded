<?php 
/**
 * Helper Functions for Selling Ads
 * @since       2.0.86
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
    exit;
}
/*
    * Create a new page on plugin activation
    * @since 2.0.86
*/
function quads_create_sellpage_on_activation() {
    // Check if the page already exists
    $existing_page = get_page_by_path( 'buy-adspace' );
    $quads_sell_page = get_option( 'quads_sellpage' , false );
    $quads_settings = get_option( 'quads_settings' , []);

    if ( $existing_page && ! $quads_sell_page ) {
        $quads_settings['payment_page'] = $existing_page->ID;
        update_option( 'quads_settings', $quads_settings , false);
        update_option( 'quads_sellpage', true , false);
        return;
    }
    // If the page doesn't exist, create a new page
    if ( ! $existing_page && ! $quads_sell_page ) {
        $page_data = array(
            'post_title'     => esc_html__( 'Buy Adspace', 'quick-adsense-reloaded' ),
            'post_content'   => '[quads_buy_form]',
            'post_status'    => 'draft',
            'post_type'      => 'page',
            'post_author'    => get_current_user_id(),
            'post_name'      => 'buy-adspace', // Custom slug
        );

        $page_id = wp_insert_post( $page_data ); // Create the page

        if ( $page_id && ! is_wp_error( $page_id ) ) {
            // Save the page slug or ID in the options table
            $quads_settings['payment_page'] = $page_id;
            update_option( 'quads_settings', $quads_settings , false);
            update_option( 'quads_sellpage', true , false);
        }
    }
}
add_action( 'admin_init', 'quads_create_sellpage_on_activation' );

add_action( 'upgrader_process_complete', 'quads_adsell_upgrade_handler', 10, 2 );

add_action( 'init', 'quads_authorize_payment_success' );
function quads_authorize_payment_success(){
    
    if ( !is_user_logged_in() ) {
        return false;
    }
    if( !isset( $_GET[ 'security' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET[ 'security' ] ) ), 'security' )){ 
        return false;
    }
    if( isset( $_GET['status'] ) && $_GET['status']=='success' && isset( $_GET['ad_slot_id'] ) && $_GET['ad_slot_id'] > 0 && isset( $_GET['refId'] ) && $_GET['refId'] != "" && isset( $_GET['user_id'] ) && intval( $_GET['user_id'] ) >0 && !isset( $_GET['target'] )){
        $slot_id = sanitize_text_field( wp_unslash( $_GET['ad_slot_id'] ) );
        $slot_id = intval($slot_id);
        $order_id = sanitize_text_field( wp_unslash( $_GET['refId'] ) );
        $order_id = intval($order_id);
        $user_id = sanitize_text_field( wp_unslash( $_GET['user_id'] ) );
        $user_id = intval($user_id);
        $price = get_post_meta( $slot_id, 'ad_cost' );
        if(!empty($price)){
            $price = $price[0];
        }else{
            $price = '';
        }
        $user = get_user_by( 'id', $user_id );
        if($user){
            global $wpdb;
            $table_name = $wpdb->prefix . 'quads_adbuy_data';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $ad_details = $wpdb->get_row($wpdb->prepare( "SELECT * FROM %s WHERE id = %d AND user_id = %d",$table_name, $order_id, $user->ID ));
           
            if (!$ad_details) {
                return false;
                
            }
            $payment_status = 'paid';
            if ($ad_details->payment_status === 'paid') {
                return false;
            }
            $params = array();
            $params['payment_date'] = gmdate('Y-m-d H:i:s');
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->update(
                $table_name,
                array('payment_status' => 'paid' , 'payment_response'=> wp_json_encode($params)), // Data to update
                array('id' => $order_id , 'user_id'=>$user->ID) 
            );

            //get the ad details from db
            $setting= get_option('quads_settings',[]);
            $currency = isset($setting['currency']) ? $setting['currency'] :'USD';
            $payer_email = $user->user_email;
            $ad_details_html = "";
            //send email to  user and admin
            $to = $payer_email;
            $subject = esc_html__( 'Ad Payment Confirmation', 'quick-adsense-reloaded' );
            $message = esc_html__( 'Your ad payment has been confirmed. Your ad will be live soon.', 'quick-adsense-reloaded' ).PHP_EOL;

            $start_date = $ad_details->start_date;
            $end_date = $ad_details->end_date;
            $days = ( strtotime( $end_date ) - strtotime( $start_date ) ) / ( 60 * 60 * 24 ) + 1;
            $total_cost = $price * $days;
            //also add the ad details in the email
            $ad_details_html .= esc_html__( 'Ad Details: ', 'quick-adsense-reloaded' ).PHP_EOL;
            $ad_details_html .= esc_html__( 'Ad Slot: ', 'quick-adsense-reloaded' ) . get_the_title($ad_details->ad_id ) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Start Date: ', 'quick-adsense-reloaded' ) . esc_html($ad_details->start_date) . PHP_EOL;
            $ad_details_html .= esc_html__( 'End Date: ', 'quick-adsense-reloaded' ) .  esc_html($ad_details->end_date) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Ad Link: ', 'quick-adsense-reloaded' ) .  esc_html($ad_details->ad_link) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Ad Image: ', 'quick-adsense-reloaded' ) .  esc_html($ad_details->ad_image) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Total Cost: ', 'quick-adsense-reloaded' ) . esc_html($currency . $total_cost) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Payment Status: ', 'quick-adsense-reloaded' ) . esc_html($payment_status) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Payer Email: ', 'quick-adsense-reloaded' ) . esc_html($payer_email) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Order ID: ', 'quick-adsense-reloaded' ) . esc_html($order_id) . PHP_EOL;
            $message .= $ad_details_html;
            

            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $to, $subject, $message, $headers );

            $to = get_option('admin_email');
            $subject = esc_html__( 'Ad Payment Confirmation', 'quick-adsense-reloaded' );
            $message = esc_html__( 'Ad payment has been confirmed for user: ', 'quick-adsense-reloaded' ) . $payer_email. PHP_EOL;
            $message = esc_html__( 'Please  review the AD so that it can go live ', 'quick-adsense-reloaded' ). PHP_EOL;
            //also add reminder to review the ad

            $message .= $ad_details_html;
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $to, $subject, $message, $headers );
        }
    }else if( isset( $_GET['status'] ) && $_GET['status'] == 'success' && isset( $_GET['refId'] ) && $_GET['refId'] != "" && isset( $_GET['user_id'] ) && intval( $_GET['user_id'] ) > 0 && isset( $_GET['target'] ) && $_GET['target'] == 'disablead' ){
       
        $order_id = sanitize_text_field( wp_unslash( $_GET['refId'] ) );
        $user_id = sanitize_text_field( wp_unslash( $_GET['user_id'] ) );
        
        $user = get_user_by( 'id', $user_id );
        if($user){
            global $wpdb;
            $table_name = $wpdb->prefix . 'quads_disabledad_data';
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $ad_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE disable_ad_id = %d AND user_id = %d", $order_id,$user->ID ) );
           
            if (!$ad_details) {
                return false;
            }
            $payment_status = 'paid';
            if ($ad_details->payment_status === 'paid') {
                return false;
            }
            $duration = $ad_details->disable_duration;
            $params = array();
            $params['payment_date'] = gmdate('Y-m-d H:i:s');
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->update(
                $table_name,
                array('payment_status' => 'paid' , 'payment_response'=> wp_json_encode($params)), // Data to update
                array('disable_ad_id' => $order_id , 'user_id'=>$user->ID) 
            );

            //get the ad details from db
            $setting= get_option('quads_settings',[]);
            $currency = isset($setting['_dacurrency']) ? $setting['_dacurrency'] :'USD';
            $price = isset($setting['_dacost']) ? $setting['_dacurrency'] :'USD';
            $payer_email = $user->user_email;
            $ad_details_html = "";
            //send email to  user and admin
            $to = $payer_email;
            $subject = esc_html__( 'Payment Confirmation', 'quick-adsense-reloaded' );
            $message = esc_html__( 'Your payment has been confirmed', 'quick-adsense-reloaded' ).PHP_EOL;

            $total_cost = $price;
            //also add the ad details in the email
            $ad_details_html .= esc_html__( 'Ad Details: ', 'quick-adsense-reloaded' ).PHP_EOL;
            $ad_details_html .= esc_html__( 'Total Cost: ', 'quick-adsense-reloaded' ) . esc_html($currency . $total_cost) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Duration: ', 'quick-adsense-reloaded' ) . esc_html($duration) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Payment Status: ', 'quick-adsense-reloaded' ) . esc_html($payment_status) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Payer Email: ', 'quick-adsense-reloaded' ) . esc_html($payer_email) . PHP_EOL;
            $ad_details_html .= esc_html__( 'Order ID: ', 'quick-adsense-reloaded' ) . esc_html($order_id) . PHP_EOL;
            $message .= $ad_details_html;
            

            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $to, $subject, $message, $headers );

            $to = get_option('admin_email');
            $subject = esc_html__( 'Ad Payment Confirmation', 'quick-adsense-reloaded' );
            $message = esc_html__( 'Ad payment has been confirmed for user: ', 'quick-adsense-reloaded' ) . $payer_email. PHP_EOL;
           
            //also add reminder to review the ad

            $message .= $ad_details_html;
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $to, $subject, $message, $headers );
        }
    }
}

/**
 * Create a new page on plugin upgrade
 * @param mixed $upgrader_object
 * @param mixed $options
 * @return void
 */
function quads_adsell_upgrade_handler( $upgrader_object, $options ) {
    if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
        // Check if the current plugin is being updated
        if ( isset( $options['plugins'] ) && is_array( $options['plugins'] ) ) {
            foreach ( $options['plugins'] as $plugin ) {
                if ( strpos( $plugin, 'quick-adsense-reloaded/quick-adsense-reloaded.php' ) !== false ) {
                    quads_create_sellpage_on_activation(); 
                }
            }
        }
    }
}

/**
 * Create a custom form for buying ads
 * @since 2.0.86
 */

function quads_ads_buy_form() {
    $ad_list = array();
    
    $args = array(
        'post_type'      => 'quads-ads',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
        'meta_query'     => array(
            array(
                'key'     => 'ad_type',
                'value'   => 'ads_space',
                'compare' => '='
            ),
        ),
    );
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $selected_ad_slot = isset($_GET['ad_slot_id']) ? sanitize_text_field( wp_unslash( $_GET['ad_slot_id'] ) ) : ''; // Get and sanitize ad_slot_id from GET
    $ads = get_posts( $args );

    if ( $ads ) {
        $booked_ads = quads_get_active_sellads_ids();
        $quads_settings = get_option( 'quads_settings' );
        $currency = isset($quads_settings['currency']) ? $quads_settings['currency'] :'USD';
        foreach ( $ads as $ad ) {
            if( in_array( $ad, array_column( $booked_ads, 'ad_id' ) ) ){
                continue;
            }
            $ad_list[ $ad ] [ 'name' ]= get_the_title( $ad );
            $ad_list[ $ad ] [ 'price' ]= get_post_meta( $ad , 'ad_cost', true )? get_post_meta( $ad , 'ad_cost', true ) : 999;
            $ad_list[ $ad ] [ 'ad_minimum_days' ]= get_post_meta( $ad , 'ad_minimum_days', true )? get_post_meta( $ad , 'ad_minimum_days', true ) : '';
            $ad_list[ $ad ] [ 'ad_minimum_selection' ]= get_post_meta( $ad , 'ad_minimum_selection', true )? get_post_meta( $ad , 'ad_minimum_selection', true ) : 'day';
            $ad_list[ $ad ] [ 'currency' ]= $currency ? $currency : 'USD';
            $ad_list[ $ad ] [ 'type' ]= get_post_meta( $ad , 'ad_cost_type', true ) ? get_post_meta( $ad , 'ad_cost_type', true ) : 'per day';
        }
    }
    global $wp;
    
    $redirect_link =  ( isset( $_SERVER['QUERY_STRING'] ) )?add_query_arg( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), '', home_url( $wp->request ) ):'';
    
   if( $selected_ad_slot != "" && intval($selected_ad_slot)>0 && isset($ad_list[ $selected_ad_slot ])){
        $selected_ad_slot = intval($selected_ad_slot);
        $selected_ad_list = $ad_list[ $selected_ad_slot ];
        $end_min_selection = '';
        $end_min_value = '';
        $ad_selection_info = '';
        if(isset($selected_ad_list[ 'ad_minimum_days' ])){
            $ad_minimum_days = $selected_ad_list[ 'ad_minimum_days' ];
            $ad_minimum_selection = $selected_ad_list[ 'ad_minimum_selection' ];
            if($ad_minimum_days!="" && $ad_minimum_days>0){
                if($ad_minimum_selection=='month'){
                    $st_date = gmdate('Y-m-d');
                    $end_date = gmdate('Y-m-d', strtotime('+'.$ad_minimum_days.' month'));
                    $end_min_selection = 'min='.$end_date;
                    $end_min_value = 'value='.$end_date;
                    $ad_selection_info = 'Minimum '.$ad_minimum_days.' month(s) selection is possible for the selected Ad Slot';
                }else if($ad_minimum_selection=='day'){
                    $st_date = gmdate('Y-m-d');
                    $end_date = gmdate('Y-m-d', strtotime('+'.$ad_minimum_days.' day'));
                    $end_min_selection = 'min='.$end_date;
                    $end_min_value = 'value='.$end_date;
                    $ad_selection_info = 'Minimum '.$ad_minimum_days.' day(s) selection is possible for the selected Ad Slot';
                }
            }
        }
    }
    if ( empty( $ad_list ) ) {
        return '<h2>'.esc_html__('No ad slots available for purchase','quick-adsense-reloaded').'</h2>';
    }


    // get  my ads from table wp_quads_adbuy_data
    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data';
    $user_id = get_current_user_id();
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $my_ads = $wpdb->get_results(  $wpdb->prepare( "SELECT * FROM $table_name Where user_id = %d", $user_id ) );


    // Start output buffering
    ob_start();
    ?>
    <style>
        /* General Form Styles */
#quads-adbuy-form {
    max-width: 700px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-family: Arial, sans-serif;
}

#quads-adbuy-form h2 {
    margin-bottom: 15px;
    font-size: 20px;
    color: #333;
}

/* Form Sections */
#quads-adbuy-form .form-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

#quads-adbuy-form .form-section:last-child {
    border-bottom: none;
}

/* Form Fields */
#quads-adbuy-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

#quads-adbuy-form input[type="text"],
#quads-adbuy-form input[type="email"],
#quads-adbuy-form input[type="password"],
#quads-adbuy-form input[type="url"],
#quads-adbuy-form input[type="date"],
#quads-adbuy-form select,
#quads-adbuy-form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    box-sizing: border-box;
}

#quads-adbuy-form input[type="file"] {
    margin-bottom: 15px;
}

#quads-adbuy-form input[type="submit"],
#quads-adbuy-form button {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

#quads-adbuy-form input[type="submit"]:hover,
#quads-adbuy-form button:hover {
    background-color: #0056b3;
}

/* Summary Section */
#quads-adbuy-form #summary-section {
    background-color: #e9ecef;
    padding: 15px;
    border-radius: 8px;
}

#quads-adbuy-form #summary-section p {
    margin: 10px 0;
    font-size: 16px;
}

#quads-adbuy-form #summary-section strong {
    font-weight: bold;
}

#quads-adbuy-form #total-cost {
    font-size: 18px;
    color: #d9534f;
}

#quads-adbuy-form #paypal-button-container {
    margin-top: 20px;
}
#quads-adbuy-form .notice-success {
    margin: 20px 0; 
    padding: 15px; 
    border: 1px solid #4caf50;
    background-color: #dff0d8; 
    color: #3c763d; 
    border-radius: 4px; 
    position: relative; 
}

#quads-adbuy-form .notice-success p {
    margin: 0;
}

#quads-adbuy-form .notice-error {
    margin: 20px 0; 
    padding: 15px; 
    border: 1px solid #d9534f; 
    background-color: #f2dede; 
    color: #a94442; 
    border-radius: 4px; 
    position: relative; 
}

#quads-adbuy-form .notice-error p {
    margin: 0;
}

#quads-adbuy-form .notice-dismiss {
    cursor: pointer; 
    position: absolute; 
    top: 15px; 
    right: 15px;
    background: none; 
    border: none; 
    font-size: 20px;
    line-height: 1; 
    color: #a94442;
}



    </style>
    <?php 
        $quads_settings = get_option( 'quads_settings' );
        $payment_gateway = isset($quads_settings['payment_gateway']) ? $quads_settings['payment_gateway'] : 'paypal';
        $stripe_publishable_key = '';
        $stripe_secret_key = '';
        if($payment_gateway=='stripe'){
            $stripe_publishable_key =  isset($quads_settings['stripe_publishable_key']) ? $quads_settings['stripe_publishable_key'] : '';
        
            $stripe_secret_key =  isset($quads_settings['stripe_secret_key']) ? $quads_settings['stripe_secret_key'] : '';
        }
        $paysatck_public_key = '';
        $paystack_secret_key = '';
        if($payment_gateway=='paystack'){
            $paysatck_public_key =  isset($quads_settings['paysatck_public_key']) ? $quads_settings['paysatck_public_key'] : '';
        
            $paystack_secret_key =  isset($quads_settings['paystack_secret_key']) ? $quads_settings['paystack_secret_key'] : '';
        }
    ?>
    <form id="quads-adbuy-form" method="POST" action="<?php echo ($payment_gateway!='stripe')?esc_url(admin_url('admin-ajax.php')):'/process-payment'; ?>" enctype="multipart/form-data">
    <?php
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if (isset( $_GET['status'] ) && $_GET['status'] == 'success') {
        echo '<div class="notice notice-success is-dismissible">
                <p>'.esc_html__('AD Successfully Submitted. You will get a confirmation email when your payment is confirmed.','quick-adsense-reloaded').'</p></div>';
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    } elseif (isset( $_GET['status'] ) && $_GET['status'] == 'cancelled') {
        echo '<div class="notice notice-error is-dismissible">
    <p>'.esc_html__('AD Payment Cancelled. Please try again.','quick-adsense-reloaded').'</p>
</div>';
    }
    ?>
        <!-- Step 1: User Info Section -->
        <?php if ( ! $user_id ) : ?>
        <div id="user-info-section" class="form-section">
            <h2><?php echo esc_html__('User Information','quick-adsense-reloaded');?></h2>
            <label for="full_name"><?php echo esc_html__('Full Name','quick-adsense-reloaded');?></label>
            <input type="text" name="full_name" id="full_name" required />

            <label for="email"><?php echo esc_html__('Email','quick-adsense-reloaded');?></label>
            <input type="email" name="email" id="email" required />

            <label for="password"><?php echo esc_html__('Password','quick-adsense-reloaded');?></label>
            <input type="password" name="password" id="password" required />
        </div>
        <?php endif; ?>
       
        <!-- Step 2: Campaign Details Section -->
        <div id="campaign-section" class="form-section">
            <h2><?php echo esc_html__('Campaign Details','quick-adsense-reloaded');?></h2>
            <label for="ad_slot_id"><?php echo esc_html__('Select Ad Slot','quick-adsense-reloaded');?></label>

            <select name="ad_slot_id" id="ad_slot_id" required>
                <option value=""><?php echo esc_html__('Select Ad Slot', 'quick-adsense-reloaded'); ?></option>
                <?php foreach ( $ad_list as $key => $value ) : ?>
                    <option value="<?php echo esc_attr( $key ); ?>" data-price="<?php echo esc_attr( $value['price'] ); ?>"  data-days="<?php echo esc_attr( $value['ad_minimum_days'] ); ?>"  data-minimum-selection="<?php echo esc_attr( $value['ad_minimum_selection'] ); ?>"
                        <?php selected( $selected_ad_slot, $key ); // Check if this is the selected option ?>>
                        <?php echo esc_html( $value['name'] ); ?> (<?php echo esc_html( $value['currency'] ); ?> <?php echo esc_html( $value['price'] ); ?><?php echo esc_html( strtoupper( str_replace('per_','/',$value['type']) ) ); ?>)
                    </option>
                <?php endforeach; ?>
            </select>


            <label for="start_date"><?php echo esc_html__('Start Date','quick-adsense-reloaded');?></label>
            <input type="date" name="start_date" id="start_date" required value="<?php echo esc_attr( gmdate('Y-m-d') );?>" min="<?php echo esc_attr( gmdate('Y-m-d') );?>" onblur="handleChangeDate('blur',this,'start')"/>

            <label for="end_date"><?php echo esc_html__('End Date','quick-adsense-reloaded');?></label>
            <input type="date" name="end_date" id="end_date" required <?php echo isset($end_min_value)? esc_attr($end_min_value) : ''?> <?php echo isset($end_min_selection)?esc_attr($end_min_selection):''?> />
            <p id="ad_selection_info" style="color:gray;font-size:14px;margin-top:-10px"><?php echo (isset($ad_selection_info))?esc_attr($ad_selection_info):''?></p>
            <label for="ad_link"><?php echo esc_html__('Ad Link','quick-adsense-reloaded');?></label>
            <input type="url" name="ad_link" id="ad_link" required placeholder="Ad Link"/>
            <input type="hidden" name="redirect_link" id="redirect_link" value=<?php echo esc_url_raw( $redirect_link )?>/>
          

            <label for="ad_content"><?php echo esc_html__('Ad Content','quick-adsense-reloaded');?> <small>(This will be ignored if Ad image is present)</small></label>
            <textarea name="ad_content" id="ad_content" rows="4"> Your ad text here</textarea>

            <label for="ad_image"><?php echo esc_html__('Upload Ad Image','quick-adsense-reloaded');?> (optional) </label>
            <input type="file" name="ad_image" id="ad_image" accept="image/*" />
        </div>

        <!-- Step 3: Summary and Payment Section -->
        <div id="summary-section" class="form-section">
            <h2><?php echo esc_html__('Summary','quick-adsense-reloaded');?></h2>
            <p><strong><?php echo esc_html__('Selected Slot:','quick-adsense-reloaded');?></strong> <span id="summary-slot"></span></p>
            <p><strong><?php echo esc_html__('Start Date:','quick-adsense-reloaded');?></strong> <span id="summary-start-date"></span></p>
            <p><strong><?php echo esc_html__('End Date:','quick-adsense-reloaded');?></strong> <span id="summary-end-date"></span></p>

            <input type="text" name="coupon_code" id="coupon_code" class="input" value="" size="20" autocapitalize="off" autocomplete="coupon_code" placeholder="Redeem a coupon (if any)" style="width:200px;margin:0px" onchange="handleRedeemCouponCode(event)">
            <input type="hidden" name="coupon_discount_amount" id="coupon_discount_amount" class="input">
            <p style="color:red;margin:0px" id="coupon_error"></p>
            <p><strong><?php echo esc_html__('Total Cost:','quick-adsense-reloaded');?></strong> <?php echo esc_html($currency); ?> <span id="total-cost">0</span></p>

            <input type="hidden" name="action" value="submit_ad_buy_form" />
            <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce( 'submit_ad_buy_form' ));?>" />
                
            <!-- PayPal Payment Button -->
            <div id="paypal-button-container"></div>
            <?php if($payment_gateway=='stripe'){?>
                <div>
                    <label>Card Info</label>
                    <div id="card-element"style="padding:10px"></div>
                </div>
            <?php }?>
        </div>
        <button type="submit"><?php echo esc_html__('Submit','quick-adsense-reloaded');?></button>
    </form>
   
    <?php if($payment_gateway=='stripe'){ // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion, WordPress.WP.EnqueuedResources.NonEnqueuedScript?>
    <script src="https://js.stripe.com/v3/"></script>
    <?php }?>
    <?php if($payment_gateway=='paystack'){ // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion, WordPress.WP.EnqueuedResources.NonEnqueuedScript?>
        <script src="https://js.paystack.co/v1/inline.js"></script>
    <?php }?>
    <script>
   
    let ad_lists = <?php echo wp_json_encode($ad_list)?>;
    
    let selected_id = '';
    <?php if($selected_ad_slot!=""){?>
     selected_id = <?php echo esc_attr($selected_ad_slot)?>;
     calculateTotalCost(selected_id);  
    <?php }?>
    function handleRedeemCouponCode(event){
        let coupon = event.target.value;
        let nonce = '<?php echo esc_attr(wp_create_nonce( 'redeem_coupon_code' ));?>';
        let total_cost = document.getElementById('total-cost').innerHTML;
        jQuery.ajax({
            url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
            type: 'post',
            data: {slot_id:selected_id,coupon:coupon,nonce:nonce,action:'quads_redeem_coupon',total_cost:total_cost},
            success: function (response, status, XHR) {
               let data = response.data;
               if( data.success == 2 ){
                    let message = data.message;
                    document.getElementById('coupon_error').innerHTML = message;
                    setTimeout(() => {
                        document.getElementById('coupon_error').innerHTML = '';
                    }, 5000);
               }else{
                   let message = data.message;
                    document.getElementById('coupon_error').innerHTML = 'You are eligible for discount of '+message;
                    total_cost = total_cost - message;
                    document.getElementById('total-cost').innerText = total_cost;
                    document.getElementById('coupon_discount_amount').value = message;
                    document.getElementById('coupon_error').style.color = 'green';
               }
            },
            error: function (request, status, error) {
            },
        });
    }
    function handleConvertFormat(newDate){
        newDate = new Date(newDate);
        let nday = newDate.getDate();
        nday = nday.toString().padStart(2, '0');
        let nmonth = newDate.getMonth() + 1;
        nmonth = nmonth.toString().padStart(2, '0');
        let nyear = newDate.getFullYear();
        const formattedDate = `${nday}/${nmonth}/${nyear}`;
        return formattedDate;
    }            
    function calculateTotalCost(selected_id){
        let price = ad_lists[selected_id].price;
        let ad_minimum_days = ad_lists[selected_id].ad_minimum_days;
        let minimumSelection = ad_lists[selected_id].ad_minimum_selection;

        let start_date = new Date();
        let selectedDate = new Date();
        
        if(ad_minimum_days!==undefined && ad_minimum_days!=""){
            var numberOfDaysToAdd = parseInt(ad_minimum_days);
            let newDate = '';
            if(minimumSelection=='day'){
                newDate = selectedDate.setDate(selectedDate.getDate() + numberOfDaysToAdd);
            }else if(minimumSelection=='month'){
                newDate = selectedDate.setMonth(selectedDate.getMonth() + numberOfDaysToAdd);
            }
            const startDate = handleConvertFormat(start_date);
            const endDate = handleConvertFormat(newDate);
             document.getElementById('summary-start-date').innerText = startDate;
            document.getElementById('summary-end-date').innerText = endDate;
            newDate = new Date(newDate);
            if(newDate!==""){
                const days = calculateDays(start_date, newDate);
                let totalCost = price * days;
                totalCost = totalCost.toFixed(2);
                document.getElementById('total-cost').innerText = totalCost;
            }
        }
    }
    function handleChangeDate( ev, object,type ) {
        let ad_slot_id = document.getElementById("ad_slot_id").value;
        let ad_info = ad_lists[ ad_slot_id ];
        var numberOfDaysToAdd = ad_info.ad_minimum_days;
        numberOfDaysToAdd = parseInt( numberOfDaysToAdd );
        var minimumSelection = ad_info.ad_minimum_selection;
       
        let thisdate = object.value;
        var selectedDate = new Date(thisdate);
        if(ad_info.ad_minimum_days!==undefined && ad_info.ad_minimum_days!=""){
            let newDate = '';
            let ad_selection_info = '';
            if(minimumSelection=='day'){
                selectedDate.setDate(selectedDate.getDate() + numberOfDaysToAdd); // Add specified days
                newDate = selectedDate.toISOString().split('T')[0];
                ad_selection_info = 'Minimum '+numberOfDaysToAdd+' day(s) selection is possible for the selected Ad Slot';
            }else if(minimumSelection=='month'){
                newDate = selectedDate.setMonth( selectedDate.getMonth() + numberOfDaysToAdd );
                ad_selection_info = 'Minimum '+numberOfDaysToAdd+' month(s) selection is possible for the selected Ad Slot';
            }
            document.getElementById("ad_selection_info").innerHTML = ad_selection_info;
          
            if(newDate!=""){
                newDate = new Date( newDate );
                let nday = newDate.getDate();
                nday = nday.toString().padStart(2, '0');
                let nmonth = newDate.getMonth() + 1;
                nmonth = nmonth.toString().padStart(2, '0');
                let nyear = newDate.getFullYear();
                const formattedDate = handleConvertFormat( newDate );
                
                let new_date = nyear+'-'+nmonth+'-'+nday;
                
                if(type=="start"){
                    document.getElementById('end_date').value=new_date;
                    document.getElementById('end_date').setAttribute('min', new_date);
                }
                updateSummary();
            }
        }
    }

    document.getElementById('ad_slot_id').addEventListener('change', function() {
        let slotid =  this.options[this.selectedIndex].value;
        window.location.href = '?ad_slot_id='+slotid;
        /* const pricePerDay = this.options[this.selectedIndex].getAttribute('data-price');
        const dataDays = this.options[this.selectedIndex].getAttribute('data-days');
        const dataDaysSelectionType = this.options[this.selectedIndex].getAttribute('data-minimum-selection');
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (startDate && endDate && isValidDateRange(startDate, endDate)) {
            const days = calculateDays(startDate, endDate);
            let totalCost = pricePerDay * days;
            totalCost = days.toFixed(2);
            document.getElementById('total-cost').innerText = totalCost;
        }

        document.getElementById('summary-slot').innerText = this.options[this.selectedIndex].text; */
    });

function handleAdSlotChange(){
    const ad_slot_val = document.getElementById('ad_slot_id');

    if (ad_slot_val && ad_slot_val.value) {
        document.getElementById('summary-slot').innerText = ad_slot_val.options[ad_slot_val.selectedIndex].text; 
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', handleAdSlotChange);




document.getElementById('start_date').addEventListener('change', function() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    // Automatically adjust end date if it's earlier than start date
    if (startDate && endDate && !isValidDateRange(startDate, endDate)) {
        document.getElementById('end_date').value = startDate; // Set end date to the start date
    }

    updateSummary();
});

document.getElementById('end_date').addEventListener('change', function() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    // Prevent end date from being earlier than start date
    if (startDate && endDate && !isValidDateRange(startDate, endDate)) {
        alert('End date must be greater than or equal to start date.');
        document.getElementById('end_date').value = startDate; // Reset end date to start date
    }

    updateSummary();
});

function updateSummary() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const pricePerDay = document.getElementById('ad_slot_id').selectedOptions[0].getAttribute('data-price');

    if (startDate && endDate && isValidDateRange(startDate, endDate)) {
        document.getElementById('summary-start-date').innerText = startDate;
        document.getElementById('summary-end-date').innerText = endDate;
        const days = calculateDays(startDate, endDate);
        document.getElementById('total-cost').innerText = pricePerDay * days;
    }
}

function calculateDays(start, end) {
    const startDate = new Date(start);
    const endDate = new Date(end);
    const timeDiff = endDate - startDate;
    return Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; 
}

function isValidDateRange(start, end) {
    const startDate = new Date(start);
    const endDate = new Date(end);
    return endDate >= startDate; 
}

    document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('quads-adbuy-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting normally

    var form = this;
    var formData = new FormData(form);

    // Disable the submit button and change its text
    var submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = 'Submitting...';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo esc_url(admin_url('admin-ajax.php')); ?>', true);


    // Handle the success and error responses
    xhr.onload = function() {

        // Re-enable the submit button and reset its text
        submitButton.disabled = false;

        if (xhr.status >= 200 && xhr.status < 400) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                var paypalFormContainer = document.createElement('div');
                if(response.data.paypal_form){
                    paypalFormContainer.innerHTML = response.data.paypal_form;
                    document.body.appendChild(paypalFormContainer);
                }
                // Automatically submit the PayPal form
                var paypalForm = paypalFormContainer.querySelector('form');
                if (paypalForm) {
                    paypalForm.submit();
                }else{
                    
                    <?php if($payment_gateway=='paystack'){?>
                        payWithPaystack(response.data);
                    <?php }?>
                    <?php if($payment_gateway=='stripe'){?>
                    if(response.data.id){
                        processStripePaymentSuccess(response.data);
                    }
                    <?php }?>
                }
            } else {
                alert('Error: ' + response.data.message);
            }
        } else {
            alert('An error occurred: ' + xhr.statusText);
        }
    };

    // Handle network errors
    xhr.onerror = function() {

        // Re-enable the submit button and reset its text
        submitButton.disabled = false;
        submitButton.textContent = 'Submit';

        alert('An error occurred during the request.');
    };

    // Send the form data
    xhr.send(formData);
});

});
<?php if($payment_gateway=='stripe'){?>
    var stripe = Stripe('<?php echo esc_attr($stripe_publishable_key)?>'); // Replace with your key
    var elements = stripe.elements();
    var card = elements.create('card');
    card.mount('#card-element');
<?php }?>
function payWithPaystack(data) {
    let success_link = data.success_link;
    var handler = PaystackPop.setup({
        key: data.public_key, // Replace with your Public Key
        email: data.email,
        amount: data.amount, //  * 100 Convert to kobo
        currency: data.currency,
        callback: function(response) {
            window.location.href = "verify_payment.php?reference=" + response.reference;
        },
        onClose: function() {
            alert('Payment window closed.');
        }
    });
    handler.openIframe();
}
function verifyPaystackPayment(reference,success_link){
    let nonce = '<?php echo esc_attr(wp_create_nonce( 'submit_ad_buy_form' ));?>';
    $.ajax({
        url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
        type: 'post',
        data: {reference:reference,nonce:nonce,action:'quads_verify_paystack_payment'},
        success: function (response, status, XHR) {
            if(response.data==1){
                window.location.href = success_link;
            }
        },
        error: function (request, status, error) {
        },
    });
}
async function processStripePaymentSuccess( data ){
    let client_secret = data.id;
    let success_link = data.success_link;
    let cancel_url = data.cancel_url;
    const {error, paymentIntent} = await stripe.confirmCardPayment(client_secret, {
        payment_method: {card: card}
    });

    if (error) {
        window.location.href = cancel_url;
    } else if (paymentIntent.status === 'succeeded') {
        window.location.href = success_link;
    } 
}

    </script>
    <?php
    // Return the buffered content
    return ob_get_clean();
}

$quads_settings = get_option( 'quads_settings' );
$sellable_ads = isset($quads_settings['sellable_ads']) ? $quads_settings['sellable_ads'] : true;
if ( $sellable_ads ) {
    add_shortcode( 'quads_buy_form', 'quads_ads_buy_form' );
    add_shortcode( 'sellable_premium_member_page', 'quads_sellable_premium_member_page' );
}
$disable_ads = isset($quads_settings['disableads']) ? $quads_settings['disableads'] : false;
if ( $disable_ads ) {
    add_shortcode( 'quads_disable_ads_form', 'quads_ads_disable_form' );
}
function quads_custom_premimum_memeber_login() {
    
    if ( isset($_POST['username']) && isset($_POST['password']) && isset($_POST['nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'member_login_form' ) ) {
        global $wp;
        $redirect_url = home_url( $wp->request );
        $creds = array(
            'user_login'    => sanitize_text_field( wp_unslash( $_POST['username'] ) ),
            'user_password' => sanitize_text_field( wp_unslash( $_POST['password'] ) ),
            'remember'      => true
        );
        $user = wp_signon($creds, false);
        if (!is_wp_error($user)) {
            wp_redirect($redirect_url);
            exit;
        } else {
            echo '<p>Login failed! Please try again.</p>';
        }
    }
}
function quads_update_member_subscription() {
    if (isset($_POST['id']) && isset($_POST['ad_link']) && isset($_POST['ad_content']) && isset($_POST['submit-update-member-ad-space']) && isset($_POST['nonce'])  && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'member_subscription' )) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'quads_adbuy_data'; 
        $update_data = array();
        $update_data['ad_link'] =   sanitize_text_field( wp_unslash( $_POST['ad_link']) ) ;
        $update_data['ad_content'] =  sanitize_text_field( wp_unslash( $_POST['ad_content'] ) );
        if ( ! empty( $_FILES['ad_image']['name'] ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            $uploaded_file = wp_handle_upload( $_FILES['ad_image'], array( 'test_form' => false ) );
            if ( isset( $uploaded_file['url'] ) ) {
                $ad_image = esc_url_raw( $uploaded_file['url'] );
                $update_data['ad_image'] =  $ad_image;
            }
        }
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $status = $wpdb->update(
            $table_name,
            $update_data,
            ['id' => intval($_POST['id'])]
        );
        global $wp;
        $redirect_url = home_url( $wp->request );
        wp_redirect($redirect_url);
            exit;
    }
}
function quads_get_premimum_member_ad_space($user_id){
    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data'; 
   
    // Query the records
    /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching */
    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM $table_name WHERE payment_status = %s and user_id = %d ORDER BY id DESC", 'paid', $user_id ) );
   
    foreach ($results as $key => $result) {
        $ad_id = $result->ad_id;
        $ad_name = get_the_title($ad_id);
        $start_date = $result->start_date;
        $end_date = $result->end_date;
        $display_date = gmdate('d M Y', strtotime($start_date)).' to '.gmdate('d M Y', strtotime($end_date));
        $results[$key]->ad_name = $ad_name;
        $results[$key]->display_date = $display_date;

        $today = gmdate('Y-m-d');
        $date1 = new DateTime($end_date);
        $date2 = new DateTime($today);

        $interval = $date1->diff($date2);
        $days = $interval->days;
        $expire_message = '';
        if($today>$end_date){
            $expire_message = 'Expired '.$days.' days ago';
        }else{
            $expire_message = 'Expring in '.$days.' days';
        }
        $results[$key]->expire_message = $expire_message;
    }
    return $results;
} 
function quads_get_premimum_member_ad_space_on_id($id){
    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data'; 
   
    // Query the records
    /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching */
    $results = $wpdb->get_results($wpdb->prepare(
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        "SELECT * FROM $table_name WHERE payment_status = %s and id = %d ORDER BY id DESC", 'paid', $id
    ));
   
    foreach ($results as $key => $result) {
        $ad_id = $result->ad_id;
        $ad_name = get_the_title($ad_id);
        $start_date = $result->start_date;
        $end_date = $result->end_date;
        $display_date = gmdate('d M Y', strtotime($start_date)).' to '.gmdate('d M Y', strtotime($end_date));
        $results[$key]->ad_name = $ad_name;
        $results[$key]->display_date = $display_date;

        $today = gmdate('Y-m-d');
        $date1 = new DateTime($end_date);
        $date2 = new DateTime($today);

        $interval = $date1->diff($date2);
        $days = $interval->days;
        $expire_message = '';
        if($today>$end_date){
            $expire_message = 'Expired '.$days.' ago';
        }else{
            $expire_message = 'Expring in '.$days.' days';
        }
        $results[$key]->expire_message = $expire_message;
    }
    return $results;
} 
function quads_sellable_premium_member_page(){
    ob_start();
    
?>
<style>
    /*! This file is auto-generated */
    #login form p.submit,.login *,body,html{margin:0;padding:0}.login form,.login h1 a{font-weight:400;overflow:hidden}.login form,.login form .input,.login form input[type=checkbox],.login input[type=text]{background:#fff}body,html{height:100%}body{background:#f0f0f1;min-width:0;color:#3c434a;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;font-size:13px;line-height:1.4}.login label,p{line-height:1.5}a{cursor:pointer;color:#2271b1;transition-property:border,background,color;transition-duration:.05s;transition-timing-function:ease-in-out;outline:0}a:active,a:hover{color:#135e96}a:focus{color:#043959;box-shadow:0 0 0 2px #2271b1;outline:transparent solid 2px}#loginform p.submit{border:none;margin:-10px 0 20px}.login .input::-ms-clear{display:none}.login .wp-pwd{position:relative}.login form{margin-top:20px;margin-left:0;padding:26px 24px;border:1px solid #c3c4c7;box-shadow:0 1px 3px rgba(0,0,0,.04)}.login .button-primary{float:right;background: #2271b1;
    cursor: pointer;
    border-color: #2271b1;
    color: #fff;
    text-decoration: none;
    text-shadow: none;min-height: 32px;
    line-height: 2.30769231;
    padding: 0 12px;}#login form p{margin-bottom:0}.login label{font-size:14px;display:inline-block;margin-bottom:3px}.login h1{text-align:center}.login h1 a{background-image:url(../images/w-logo-blue.png?ver=20131202);background-image:none,url(../images/wordpress-logo.svg?ver=20131107);background-size:84px;background-position:center top;background-repeat:no-repeat;color:#3c434a;height:84px;font-size:20px;line-height:1.3;margin:0 auto 25px;padding:0;text-decoration:none;width:84px;text-indent:-9999px;outline:0;display:block}#login{width:320px;padding:5% 0 0;margin:auto}.login form .input,.login input[type=password],.login input[type=text],.login input[type=url],.login input[type=file],.login textarea{font-size:24px;line-height:1.33333333;width:95%;border-width:.0625rem;padding:.1875rem .3125rem;margin:0 6px 16px 0;min-height:40px;max-height:none}.login input.password-input{font-family:Consolas,Monaco,monospace}.wp-login-logo{color:#3c434a;font-size:20px}.preview-ad-space{background: #fff}.preview-ad-space h3{margin:0px;text-align:center}
</style>
<?php 
$user_id = is_user_logged_in() ? get_current_user_id() : 0;
if($user_id==0){?>
<div class="login">
    <div id="login">
        <h1 class="wp-login-logo">Member Login</h1>
        <form name="loginform" id="loginform" method="post">
            <p>
                <label for="user_login"> <?php echo esc_html__( 'Username or Email Address','quick-adsense-reloaded' ); ?></label>
                <input type="text" name="username" id="user_login" class="input" value="" size="20" autocapitalize="off" autocomplete="username" required="required">
            </p>

            <div class="user-pass-wrap">
                <label for="user_pass"><?php echo esc_html__( 'Password','quick-adsense-reloaded' ); ?></label>
                <div class="wp-pwd">
                    <input type="password" name="password" id="user_pass" class="input password-input" value="" size="20" autocomplete="current-password" spellcheck="false" required="required">
                </div>
            </div> 
            <p class="submit">
                <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce( 'member_login_form' ));?>" />
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In">
            </p>
        </form>
    </div>
</div>
<?php 
quads_custom_premimum_memeber_login();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
}else if(!isset($_GET['modify_id'])){
    $ad_space_list = quads_get_premimum_member_ad_space($user_id);
    $quads_settings = get_option( 'quads_settings' );
    $da_page_id = isset($quads_settings['payment_page']) ? $quads_settings['payment_page'] : 0;
    $payment_page = get_permalink( $da_page_id );
?>
<style>
    #prem-member-subs-table {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#prem-member-subs-table td, #prem-member-subs-table th {
  border: 1px solid #ddd;
  padding: 8px;
}

#prem-member-subs-table tr:nth-child(even){background-color: #f2f2f2;}

#prem-member-subs-table th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #b9b9b9;
  color: white;
  white-space: nowrap;
}
</style>
<div class="login preview-ad-space">
    
    
    <table id="prem-member-subs-table">
        <thead>
            <th><?php echo esc_html__('Ad Space Name','quick-adsense-reloaded');?></th>
            <th><?php echo esc_html__('Add Content','quick-adsense-reloaded');?></th>
            <th><?php echo esc_html__('Add Link','quick-adsense-reloaded');?></th>
            <th><?php echo esc_html__('Duration','quick-adsense-reloaded');?></th>
            <th><?php echo esc_html__('Status','quick-adsense-reloaded');?></th>
            <th></th>
        </thead>
        <tbody>
        <?php
        foreach ($ad_space_list as $key => $value) {
    ?>
    <tr>
        <td><?php echo esc_attr($value->ad_name)?></td>
        <td><?php echo esc_attr($value->ad_content)?></td>
        <td><?php echo esc_url($value->ad_link)?></td>
        <td>
            <p style="white-space:nowrap"><?php echo esc_attr($value->display_date)?></p>
            <p style="color:red"><?php echo esc_attr($value->expire_message)?></p>
        </td>
        <td><?php echo esc_attr($value->payment_status)?></td>
        <td style="display:flex">
            <a href="?modify_id=<?php echo intval($value->id)?>&renew_id=<?php echo intval($value->ad_id)?>"><input type="button" class="button button-primary button-large" value="Modify"></a>
            <a style="margin-left:5px" href="<?php echo esc_url($payment_page).'?ad_slot_id='.intval($value->ad_id)?>"><input type="button" class="button button-primary button-large" value="Renew"></a>
        </td>
    </tr>
<?php } 
if ( count( $ad_space_list )==0 ) {
?>
        <tr>
            <td colspan="6" style="text-align:center">
                <p>
                    <?php echo esc_html__( 'No Ad Space purchase yet, Please click the below button to purchase ad space ','quick-adsense-reloaded' ); ?>
                </p>
                
                <a href="<?php echo esc_url($payment_page)?>"><input type="button" class="button button-primary button-large" style="float:none" value="<?php echo esc_html__( 'Purchase Ad Space','quick-adsense-reloaded' );?>"></a>
            </td>
        </tr>
<?php
}
?>
    </tbody>
    </table>
    
    </div>
<?php
}
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if( isset( $_GET['modify_id'] ) && !empty( $_GET['modify_id'] ) && isset( $_GET['renew_id'] )  && !empty( $_GET['renew_id'] ) ){
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $modify_id = sanitize_text_field( wp_unslash( $_GET['modify_id'] ) );
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $renew_id = sanitize_text_field( wp_unslash( $_GET['renew_id'] ) );
    $ad_space_list = quads_get_premimum_member_ad_space_on_id( $modify_id );
    $asdata = $ad_space_list[0];
    $quads_settings = get_option( 'quads_settings' );
    $da_page_id = isset($quads_settings['payment_page']) ? $quads_settings['payment_page'] : 0;
    $payment_page = get_permalink( $da_page_id );
?>
    <div class="login preview-ad-space">
    <form name="loginform" id="loginform" method="post" enctype="multipart/form-data">
        <h3><?php echo esc_html__( 'Preview Ad Space','quick-adsense-reloaded' ); ?></h3> 
        <div>
            <label for="ad_link"><?php echo esc_html__( 'Ad Link','quick-adsense-reloaded' ); ?></label> 
            <input type="url" name="ad_link" id="ad_link" required placeholder="Ad Link" value=<?php echo esc_url($asdata->ad_link)?>>
            <input type="hidden" name="id" id="id"  value=<?php echo intval($asdata->id)?>>
        </div>
        <div>
            <label for="ad_content"><?php echo esc_html__( 'Ad Content','quick-adsense-reloaded' ); ?></label>
            <textarea name="ad_content" id="ad_content" rows="4"><?php echo esc_attr($asdata->ad_content)?></textarea>
        </div>
        <div><label for="ad_image"><?php echo esc_html__( 'Ad Image','quick-adsense-reloaded' ); ?></label></div>
        <div>
            <?php if($asdata->ad_image!=""){ // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
                <img id="ad_image_src" src="<?php echo esc_url($asdata->ad_image)?>"/>
            <?php }?>
            <input type="file" name="ad_image" id="ad_image" accept="image/*"  onchange="onFileSelected(event)">
        </div>
        <p class="submit">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce( 'member_subscription' ));?>" />

            <input type="submit" name="submit-update-member-ad-space" class="button button-primary button-large" value="<?php echo esc_html__( 'Update Information','quick-adsense-reloaded' );?>" style="cursor:pointer">
 
            <a href="<?php echo esc_url($payment_page);?>?ad_slot_id=<?php echo intval($renew_id)?>"><input type="button"  class="button button-primary button-large" value="<?php echo esc_html__( 'Renew','quick-adsense-reloaded' );?>"></a>
        </p>
    </form>
    </div>
    
    <script>
        function onFileSelected(event) {
            
            var selectedFile = event.target.files[0];
            let url = window.URL.createObjectURL(selectedFile);
            
            document.getElementById('ad_image_src').setAttribute('src',url);
            
        }
    </script>
<?php 
quads_update_member_subscription();
}?>
<?php
return ob_get_clean();
}
function quads_ads_disable_form(){
    ob_start();
    ?>
<style>
.da-payment-box{
    align-items: center;
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-top: 43px;
}
.da-payment-box2{
    align-items: center;
    box-sizing: border-box;
    display: flex;
    gap: 16px;
    justify-content: center;
    margin: auto;
    width: 100%;
}
.da-payment-box3{
    padding: 0px 16px 24px 32px;background-color: #fff;
    border: 1px solid #bbb;
    border-radius: 4px;
    width: 100%;align-items: center;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
}
.da-title1{
    border-bottom: 1px solid #e3e3e3;
    font: 600 22px 'Work Sans', sans-serif;
    line-height: 22px;
    margin-bottom: 32px;
    padding-bottom: 8px;
    text-align: center;
    text-transform: uppercase;
    width: 100%;
}
.da-content-box{
    align-items: center;display: flex;flex-direction: column;
}
.da-sub-content{
    font: 700 55px 'Merriweather', 'GeorgiaCustom';
    gap: 4px;
    padding-bottom: 8px;
    margin:0px;
}
.da-sub-content2{
    font: 400 18px 'Work Sans', sans-serif;
    padding-bottom: 16px;
    line-height : 1.5;
    text-align:center;
}
.da-subcribe-btn{
    background-color: #dc0000;height: 48px;border: none;
    border-radius: 8px;
    color: #fff;
    font: 600 14px 'Work Sans', sans-serif;
    width: 100%;
    cursor:pointer
}
._da-open-button {
  background-color: #555;
  color: white;
  padding: 16px 20px;
  border: none;
  cursor: pointer;
  opacity: 0.8;
  position: fixed;
  bottom: 23px;
  right: 28px;
  width: 280px;
}

._da-modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50%;
  
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
._da-modal-content {
  background-color: #fefefe;
  margin: 15% auto; /* 15% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 80%; /* Could be more or less, depending on screen size */
}

/* The Close Button */
._da-close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

._da-close:hover,
._da-close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
#quads-adbuy-form {
    font-family: Arial, sans-serif;
}

#quads-adbuy-form h2 {
    margin-bottom: 15px;
    font-size: 20px;
    color: #333;
}

/* Form Sections */
#quads-adbuy-form .form-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

#quads-adbuy-form .form-section:last-child {
    border-bottom: none;
}

/* Form Fields */
#quads-adbuy-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

#quads-adbuy-form input[type="text"],
#quads-adbuy-form input[type="email"],
#quads-adbuy-form input[type="password"],
#quads-adbuy-form input[type="url"],
#quads-adbuy-form input[type="date"],
#quads-adbuy-form select,
#quads-adbuy-form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    box-sizing: border-box;
}

#quads-adbuy-form input[type="file"] {
    margin-bottom: 15px;
}

#quads-adbuy-form input[type="submit"],
#quads-adbuy-form button {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

#quads-adbuy-form input[type="submit"]:hover,
#quads-adbuy-form button:hover {
    background-color: #0056b3;
}

/* Summary Section */
#quads-adbuy-form #summary-section {
    background-color: #e9ecef;
    padding: 15px;
    border-radius: 8px;
}

#quads-adbuy-form #summary-section p {
    margin: 10px 0;
    font-size: 16px;
}

#quads-adbuy-form #summary-section strong {
    font-weight: bold;
}

#quads-adbuy-form #total-cost {
    font-size: 18px;
    color: #d9534f;
}

#quads-adbuy-form #paypal-button-container {
    margin-top: 20px;
}
._danotice-success {
    margin: 20px 0; 
    padding: 15px; 
    border: 1px solid #4caf50;
    background-color: #dff0d8; 
    color: #3c763d; 
    border-radius: 4px; 
    position: relative; 
}

._danotice-success p {
    margin: 0;
}

._danotice-error {
    margin: 20px 0; 
    padding: 15px; 
    border: 1px solid #d9534f; 
    background-color: #f2dede; 
    color: #a94442; 
    border-radius: 4px; 
    position: relative; 
}

._danotice-error p {
    margin: 0;
}

._danotice-dismiss {
    cursor: pointer; 
    position: absolute; 
    top: 15px; 
    right: 15px;
    background: none; 
    border: none; 
    font-size: 20px;
    line-height: 1; 
    color: #a94442;
}

</style>
<?php
    $quads_settings = get_option( 'quads_settings' );
    $currency = isset($quads_settings['_dacurrency']) ? $quads_settings['_dacurrency'] :'USD';
    $_dacost = isset($quads_settings['_dacost']) ? $quads_settings['_dacost'] :'';
    $_daduration = isset($quads_settings['_daduration']) ? $quads_settings['_daduration'] :'Monthly';
    $payment_gateway = isset($quads_settings['_dapayment_gateway']) ? $quads_settings['_dapayment_gateway'] : 'paypal';
    $stripe_publishable_key = '';
    $stripe_secret_key = '';
    if($payment_gateway=='stripe'){
        $stripe_publishable_key =  isset($quads_settings['_dastripe_publishable_key']) ? $quads_settings['_dastripe_publishable_key'] : '';
    
        $stripe_secret_key =  isset($quads_settings['_dastripe_secret_key']) ? $quads_settings['_dastripe_secret_key'] : '';
    }
    $user_id = get_current_user_id();
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if (isset($_GET['status']) && $_GET['status'] == 'success') {
        echo '<div class="_danotice _danotice-success _dais-dismissible">
        <p>'. esc_html__( 'Successfully Submitted. You will get a confirmation email when your payment is confirmed.','quick-adsense-reloaded' ).'</p></div>';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        } elseif (isset($_GET['status']) && $_GET['status'] == 'cancelled') {
            echo '<div class="_danotice _danotice-error _dais-dismissible">
        <p>'.esc_html__( 'Payment Cancelled. Please try again.','quick-adsense-reloaded').'</p></div>';
        }
?>
<div class="da-payment-box2">
   <div class="da-payment-box3">
       <p class="da-title1"><?php echo esc_attr($_daduration);?></p>
       <div class="da-content-box">
           <p class="da-sub-content">$<?php echo esc_attr($_dacost)?></p>
           <p class="da-sub-content2"><?php echo esc_html__('Take your browsing to the next level by upgrading to our premium plan, where you can enjoy an uninterrupted, completely ad-free experience, ensuring faster loading times, a cleaner interface, and seamless access to all your favorite content without any distractions','quick-adsense-reloaded');?></p>
       </div>
       <button type="button" class="da-subcribe-btn" onclick="openAdsBlockForm()">Subscribe</button>
   </div>
</div>
<div id="adsBlockForm" class="_da-modal">
  <!-- Modal content -->
  <form id="quads-adbuy-form" method="POST" action="<?php echo ($payment_gateway!='stripe')?esc_url(admin_url('admin-ajax.php')):'/process-payment'; ?>" enctype="multipart/form-data">
    <div class="_da-modal-content">
        <span class="_da-close" onclick="closeAdsBlockForm()">&times;</span>
        <?php if ( ! $user_id ) : ?>
            <div id="user-info-section" class="form-section">
                <h2><?php echo esc_html__('User Information','quick-adsense-reloaded');?></h2>
                <label for="full_name"><?php echo esc_html__('Full Name','quick-adsense-reloaded');?></label>
                <input type="text" name="full_name" id="full_name" required />

                <label for="email"><?php echo esc_html__('Email','quick-adsense-reloaded');?></label>
                <input type="email" name="email" id="email" required />

                <label for="password"><?php echo esc_html__('Password','quick-adsense-reloaded');?></label>
                <input type="password" name="password" id="password" required />
            </div>
        <?php endif; ?>
        <input type="hidden" name="action" value="submit_disablead_form" />
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce( 'submit_disablead_form' ));?>" />
        <!-- PayPal Payment Button -->
        <div id="paypal-button-container"></div>
        <?php if($payment_gateway=='stripe'){?>
            <div>
                <label>Card Info</label>
                <div id="card-element"style="padding:10px"></div>
            </div>
        <?php }?>
        <button type="submit"><?php echo esc_html__('Proceed for Payment','quick-adsense-reloaded');?></button>
    </div>
</form>
<?php if($payment_gateway=='stripe'){ // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion, WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
    <script src="https://js.stripe.com/v3/"></script>
<?php }?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('quads-adbuy-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting normally

            var form = this;
            var formData = new FormData(form);

            // Disable the submit button and change its text
            var submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo esc_url(admin_url('admin-ajax.php')); ?>', true);


            // Handle the success and error responses
            xhr.onload = function() {

                // Re-enable the submit button and reset its text
                submitButton.disabled = false;

                if (xhr.status >= 200 && xhr.status < 400) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        var paypalFormContainer = document.createElement('div');
                        if(response.data.paypal_form){
                            paypalFormContainer.innerHTML = response.data.paypal_form;
                            document.body.appendChild(paypalFormContainer);
                        }
                        // Automatically submit the PayPal form
                        var paypalForm = paypalFormContainer.querySelector('form');
                        if (paypalForm) {
                            paypalForm.submit();
                        }else{
                            console.log(response.data.id);
                            
                            if(response.data.id){
                                processStripePaymentSuccess(response.data);
                            }
                        }
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                } else {
                    alert('An error occurred: ' + xhr.statusText);
                }
            };

            // Handle network errors
            xhr.onerror = function() {

                // Re-enable the submit button and reset its text
                submitButton.disabled = false;
                submitButton.textContent = 'Submit';

                alert('An error occurred during the request.');
            };

            // Send the form data
            xhr.send(formData);
        });
    });
<?php if($payment_gateway=='stripe'){?>
    var stripe = Stripe('<?php echo esc_attr($stripe_publishable_key)?>'); // Replace with your key
    var elements = stripe.elements();
    var card = elements.create('card');
    card.mount('#card-element');
<?php }?>
async function processStripePaymentSuccess( data ){
    let client_secret = data.id;
    let success_link = data.success_link;
    let cancel_url = data.cancel_url;
    const {error, paymentIntent} = await stripe.confirmCardPayment(client_secret, {
        payment_method: {card: card}
    });

    if (error) {
        window.location.href = cancel_url;
    } else if (paymentIntent.status === 'succeeded') {
        window.location.href = success_link;
    } 
}
    function openAdsBlockForm() {
        document.getElementById("adsBlockForm").style.display = "block";
    }

    function closeAdsBlockForm() {
        document.getElementById("adsBlockForm").style.display = "none";
    }
</script>
<?php
return ob_get_clean();
}

function handle_ad_buy_form_submission() {
   
    if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'submit_ad_buy_form' ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Invalid request.', 'quick-adsense-reloaded' ) ) );
    }

    if ( ! check_ajax_referer( 'submit_ad_buy_form', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Invalid request.', 'quick-adsense-reloaded' ) ) );
    }
  
    // Handle form fields, sanitize input, validate, and process accordingly
    $user_id = is_user_logged_in() ? get_current_user_id() : 0;

    // If user is not logged in, register them using the provided info
    if ( ! $user_id ) {
        $full_name = ( isset( $_POST['full_name'] ) )?sanitize_text_field( wp_unslash( $_POST['full_name']  ) ) :'';
        $email = ( isset( $_POST['email'] ) )? sanitize_email( wp_unslash( $_POST['email'] ) ):'';
        $password = ( isset( $_POST['password'] ) )? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';

        if ( empty( $full_name ) || empty( $email ) || empty( $password ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Please fill in all fields.', 'quick-adsense-reloaded' ) ) );
        }

        // Create the new user
        $user_id = wp_create_user( $email, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Failed to create account. ', 'quick-adsense-reloaded' ) . $user_id->get_error_message() ) );
        }else{
            $creds = array(
                'user_login'    => $email,
                'user_password' => $password,
                'remember'      => true
            );
            $user = wp_signon($creds, false);
            if ( is_wp_error($user)) {
                wp_send_json_error( array( 'message' => esc_html__( 'Failed to Login. ', 'quick-adsense-reloaded' ) ) );
            }
        }
    }

    // Sanitize and validate the remaining fields
    $redirect_link  = isset( $_POST['redirect_link'] )? esc_url_raw( wp_unslash( $_POST['redirect_link'] ) ) : '';
    $cancel_link  = isset( $_POST['cancel_link'] )? intval( wp_unslash($_POST['cancel_link'] ) ) : '';
    $ad_slot_id  = isset( $_POST['ad_slot_id'] )? intval( wp_unslash($_POST['ad_slot_id'] ) ) : '';
    $start_date  = isset( $_POST['start_date'] )? sanitize_text_field( wp_unslash($_POST['start_date'] ) ) : '';
    $end_date    = isset( $_POST['end_date'] )? sanitize_text_field( wp_unslash($_POST['end_date'] ) ) : '';
    $ad_link     = isset( $_POST['ad_link'] )? esc_url_raw( wp_unslash( $_POST['ad_link'] ) ) : '';
    $ad_content  = isset($_POST['ad_content']) ? sanitize_textarea_field( wp_unslash ($_POST['ad_content'] ) ):'';

    $coupon_code  = isset($_POST['coupon_code']) ? sanitize_textarea_field( wp_unslash ($_POST['coupon_code'] ) ):'';

    $coupon_discount_amount  = isset($_POST['coupon_discount_amount']) ? sanitize_textarea_field( wp_unslash ($_POST['coupon_discount_amount'] ) ):'';

    $ad_image    = ''; // Initialize the ad image URL

    // Handle file upload if provided
    if ( ! empty( $_FILES['ad_image']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $uploaded_file = wp_handle_upload( $_FILES['ad_image'], array( 'test_form' => false ) );

        if ( isset( $uploaded_file['url'] ) ) {
            $ad_image = esc_url_raw( $uploaded_file['url'] );
        } else {
            wp_send_json_error( array( 'message' => esc_html__( 'Image upload failed.', 'quick-adsense-reloaded' ) ) );
        }
    }

    // Insert the ad buy record in the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $result = $wpdb->insert( $table_name, array(
        'user_id'        => $user_id,
        'ad_id'          => $ad_slot_id,
        'ad_content'     => $ad_content,
        'ad_link'        => $ad_link,
        'ad_image'       => $ad_image,
        'start_date'     => $start_date,
        'end_date'       => $end_date,
        'payment_status' => 'pending', // Update after payment
        'ad_status'      => 'pending', // Set to pending until approved
    ) );

    $price = get_post_meta( $ad_slot_id, 'ad_cost', true );
    $currency = "USD";
    $days = ( strtotime( $end_date ) - strtotime( $start_date ) ) / ( 60 * 60 * 24 ) + 1;
    $total_cost = $price * $days;
   
    $name = get_the_title( $ad_slot_id );


    if ( $result ) {
        $order_id = $wpdb->insert_id;
        if( $coupon_discount_amount !='' && $coupon_discount_amount>0){
            $total_cost = $total_cost - $coupon_discount_amount;
        }
        $quads_settings = get_option( 'quads_settings' );
        $payment_gateway = isset($quads_settings['payment_gateway']) ? $quads_settings['payment_gateway'] : 'paypal';
        if($payment_gateway=='paypal'){
            $paypal_email =  isset($quads_settings['paypal_email']) ? $quads_settings['paypal_email'] : '';

            if ( empty( $paypal_email ) ) {
                wp_send_json_error( array( 'message' => esc_html__(  'PayPal email not set.Please inform Siteadmin', 'quick-adsense-reloaded' ) ) );
            }

            $currency = isset($quads_settings['currency']) ? $quads_settings['currency'] : 'USD';

            // Prepare the PayPal form
            $paypal_form = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">';
            $paypal_form .= '<input type="hidden" name="cmd" value="_xclick">';
            $paypal_form .= '<input type="hidden" name="business" value="'.sanitize_email( $paypal_email ).'">'; // Your PayPal email
            $paypal_form .= '<input type="hidden" name="item_name" value="'.esc_attr( $name).'">';
            $paypal_form .= '<input type="hidden" name="amount" value="'.esc_attr($total_cost).'">';
            $paypal_form .= '<input type="hidden" name="currency_code" value="'.esc_attr($currency).'">';
            $paypal_form .= '<input type="hidden" name="return" value="' . esc_url( $redirect_link.'?status=success' ) . '">';
            $paypal_form .= '<input type="hidden" name="cancel_return" value="' . esc_url( $redirect_link.'?status=cancelled' ) . '">';
            $paypal_form .= '<input type="hidden" name="notify_url" value="' . esc_url( rest_url('wpquads/v1/paypal_notify_url') ) . '">';
            $paypal_form .= '<input type="hidden" name="item_number" value="' . esc_attr($order_id) . '">';
            $paypal_form .= '<input type="hidden" name="custom" value="' . esc_attr($user_id) . '">';

            wp_send_json_success( array( 'message' => esc_html__( 'Ad submission successful.', 'quick-adsense-reloaded' ) , 'paypal_form'=>$paypal_form) );
        }else if($payment_gateway=='authorize'){
            $authorize_name =  isset($quads_settings['authorize_name']) ? $quads_settings['authorize_name'] : '';
            $authorize_transactionKey =  isset($quads_settings['authorize_transactionKey']) ? $quads_settings['authorize_transactionKey'] : '';
            $authorize_merchant_name =  isset($quads_settings['authorize_merchant_name']) ? $quads_settings['authorize_merchant_name'] : '';

            if ( empty( $authorize_name ) || empty( $authorize_transactionKey ) ) {
                wp_send_json_error( array( 'message' => esc_html__( 'Authorize Credentials are not set. Please inform Siteadmin', 'quick-adsense-reloaded' ) ) );
            }
            $currency = isset($quads_settings['currency']) ? $quads_settings['currency'] : 'USD';

            
            //$authorize_url ='https://apitest.authorize.net/xml/v1/request.api';
            $authorize_url ='https://api.authorize.net/xml/v1/request.api';
            $redirect_link = rtrim($redirect_link,'/');
            $success_nonce = wp_create_nonce( 'submit_ad_buy_form_success' );
            $success_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&status=success&user_id='.$user_id.'&ad_slot_id='.esc_attr( $ad_slot_id ).'&security='.esc_attr($success_nonce);
            $cancel_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&cancel=true&user_id='.$user_id.'&ad_slot_id='.esc_attr( $ad_slot_id );
        
         $send_data = [
                        "getHostedPaymentPageRequest" => [
                            "merchantAuthentication" => [
                                "name" => esc_attr($authorize_name),
                                "transactionKey" => esc_attr($authorize_transactionKey)
                            ],
                            "refId" => esc_attr($order_id),
                            "transactionRequest" => [
                                "transactionType" => "authCaptureTransaction",
                                "amount" => esc_attr($total_cost),
                                "profile" => [
                                    "customerProfileId" => esc_attr($user_id)
                                ],
                                "customer" => [
                                    "email" => ""
                                ]
                            ],
                            "hostedPaymentSettings" => [
                                "setting" => [
                                    [
                                        "settingName" => "hostedPaymentReturnOptions",
                                        "settingValue" => wp_json_encode([
                                            "showReceipt" => true,
                                            "url" => esc_url($success_link),
                                            "urlText" => "Continue",
                                            "cancelUrl" => esc_url($cancel_link),
                                            "cancelUrlText" => "Cancel"
                                        ])
                                    ],
                                    [
                                        "settingName" => "hostedPaymentButtonOptions",
                                        "settingValue" => wp_json_encode(["text" => "Pay"])
                                    ],
                                    [
                                        "settingName" => "hostedPaymentStyleOptions",
                                        "settingValue" => wp_json_encode(["bgColor" => "blue"])
                                    ],
                                    [
                                        "settingName" => "hostedPaymentPaymentOptions",
                                        "settingValue" => wp_json_encode([
                                            "cardCodeRequired" => false,
                                            "showCreditCard" => true,
                                            "showBankAccount" => true
                                        ])
                                    ],
                                    [
                                        "settingName" => "hostedPaymentSecurityOptions",
                                        "settingValue" => wp_json_encode(["captcha" => false])
                                    ],
                                    [
                                        "settingName" => "hostedPaymentShippingAddressOptions",
                                        "settingValue" => wp_json_encode(["show" => false, "required" => false])
                                    ],
                                    [
                                        "settingName" => "hostedPaymentBillingAddressOptions",
                                        "settingValue" => wp_json_encode(["show" => true, "required" => false])
                                    ],
                                    [
                                        "settingName" => "hostedPaymentCustomerOptions",
                                        "settingValue" => wp_json_encode([
                                            "showEmail" => false,
                                            "requiredEmail" => false,
                                            "addPaymentProfile" => true
                                        ])
                                    ],
                                    [
                                        "settingName" => "hostedPaymentOrderOptions",
                                        "settingValue" => wp_json_encode([
                                            "show" => true,
                                            "merchantName" => esc_attr($authorize_merchant_name)
                                        ])
                                    ],
                                    [
                                        "settingName" => "hostedPaymentIFrameCommunicatorUrl",
                                        "settingValue" => wp_json_encode(["url" => esc_url($success_link)])
                                    ]
                                ]
                            ]
                        ]
                    ];
             // echo $send_data;
             // die;
            $response = wp_remote_post($authorize_url, array(
                'headers'   => array('content-type' => 'application/json'),
                'body'      => wp_json_encode($send_data),
                'method'    => 'POST'
            ));
            
             // Make sure there are no errors
              if ( is_wp_error( $response ) ) {    
                wp_send_json_error( array( 'message' => esc_html__( 'Processing failed.', 'quick-adsense-reloaded' ) ) );
                die;
              }
              
              $resp_data = wp_remote_retrieve_body( $response );
              $re = str_replace( '﻿', '', $resp_data );

              $re = wp_json_encode( $resp_data );
              $re = str_replace( '\ufeff', '', $re);
              $re = json_decode( $re );
              $re = json_decode( $re,true );

            if( isset( $re['token'] ) && $re['token']!="" ){
                $token = $re['token'];
                //$form_url = 'https://test.authorize.net/payment/payment';
                $form_url = 'https://accept.authorize.net/payment/payment';
                $auth_form ='<!doctype html>
                            <html lang="en">
                            <head>
                                <meta charset="utf-8">
                                <title>Hosted Accept.js Payment Form</title>
                            </head>
                            <body>
                                <form id="paymentForm" method="POST" action="'.esc_url( $form_url ).'">
                                    <input type="hidden" name="token" id="token" value="'.esc_attr( $token ).'" />
                                </form>
                            </body>
                            </html>';
            wp_send_json_success( array( 'message' => esc_html__( 'Ad submission successful.', 'quick-adsense-reloaded' ) , 'paypal_form'=>$auth_form) );
            }else {
                wp_send_json_error( array( 'message' => esc_html__('Failed to process payment.', 'quick-adsense-reloaded' ) ) );
            }
        }else if($payment_gateway=='stripe'){
            $stripe_publishable_key =  isset($quads_settings['stripe_publishable_key']) ? $quads_settings['stripe_publishable_key'] : '';
        
            $stripe_secret_key =  isset($quads_settings['stripe_secret_key']) ? $quads_settings['stripe_secret_key'] : '';
            if ( empty( $stripe_secret_key ) || empty( $stripe_publishable_key ) ) {
                wp_send_json_error( array( 'message' => esc_html__( 'Stripe Credentials are not set. Please inform Siteadmin', 'quick-adsense-reloaded' ) ) );
            }
            $currency = isset($quads_settings['currency']) ? $quads_settings['currency'] : 'USD';

            
            $redirect_link = rtrim($redirect_link,'/');
            $success_nonce = wp_create_nonce( 'submit_ad_buy_form_success' );
            $success_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&status=success&user_id='.$user_id.'&ad_slot_id='.esc_attr( $ad_slot_id ).'&security='.esc_attr($success_nonce);
            $cancel_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&cancel=true&user_id='.$user_id.'&ad_slot_id='.esc_attr( $ad_slot_id );
            require_once('stripe/vendor/autoload.php'); // Get this from Stripe's PHP SDK
            \Stripe\Stripe::setApiKey($stripe_secret_key);
            try {
                $total_cost = $total_cost*100;
                // Create a PaymentIntent
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => esc_attr( $total_cost ), // Amount in cents
                    'currency' => esc_attr( strtolower($currency) ),
                    'payment_method_types' => ['card'], // Use 'card' as the payment method
                ]);
            
                $output = [
                    'clientSecret' => $paymentIntent->client_secret,
                ];
            
                wp_send_json_success( array( 'message' => esc_html__( 'Ad submission successful.', 'quick-adsense-reloaded' ) , 'id' => $paymentIntent->client_secret,'success_link'=>$success_link,'cancel_url'=>$cancel_link) );
                die;
            } catch (\Stripe\Exception\ApiErrorException $e) {
                wp_send_json_error( array( 'message' => esc_html__( 'Failed to submit ad.', 'quick-adsense-reloaded' ) ) );
                die;
            }
        }else if($payment_gateway=='paystack'){
            $paystack_public_key =  isset($quads_settings['paystack_public_key']) ? $quads_settings['paystack_public_key'] : '';
        
            $paystack_secret_key =  isset($quads_settings['paystack_secret_key']) ? $quads_settings['paystack_secret_key'] : '';
            if ( empty( $paystack_secret_key ) || empty( $paystack_public_key ) ) {
                wp_send_json_error( array( 'message' => esc_html__( 'Stripe Credentials are not set. Please inform Siteadmin', 'quick-adsense-reloaded' ) ) );
            }
            $currency = isset($quads_settings['currency']) ? $quads_settings['currency'] : 'USD';

            $redirect_link = rtrim($redirect_link,'/');
            $success_nonce = wp_create_nonce( 'submit_ad_buy_form_success' );
            $success_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&status=success&user_id='.$user_id.'&ad_slot_id='.esc_attr( $ad_slot_id ).'&security='.esc_attr($success_nonce);
            $cancel_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&cancel=true&user_id='.$user_id.'&ad_slot_id='.esc_attr( $ad_slot_id );
            $total_cost = round($total_cost);
            $user = get_user_by('id', $user_id);
            $email = $user->user_email;
            wp_send_json_success( array( 'message' => esc_html__( 'Ad submission successful.', 'quick-adsense-reloaded' ) , 'public_key' =>$paystack_public_key,'secret_key'=>$paystack_secret_key,'email'=>$user->user_email,'amount'=>$total_cost,'currency'=>$currency,'success_link'=>$success_link,'cancel_url'=>$cancel_link) );
            die;
        }
    } else {
        wp_send_json_error( array( 'message' => esc_html__( 'Failed to submit ad.', 'quick-adsense-reloaded' ) ) );
        die;
    }
}
add_action( 'wp_ajax_quads_verify_paystack_payment', 'quads_verify_paystack_payment' );
add_action( 'wp_ajax_nopriv_quads_verify_paystack_payment', 'quads_verify_paystack_payment' );

function quads_verify_paystack_payment(){
    if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'submit_ad_buy_form' ) {
        wp_send_json_error( array( 'message' => esc_html__('Invalid request.', 'quick-adsense-reloaded' ) ) );
    } 
    if ( ! check_ajax_referer( 'submit_ad_buy_form', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => esc_html__('Invalid request.', 'quick-adsense-reloaded' ) ) );
    }
    if(isset($_POST['reference'])) {
        $reference = ( isset( $_POST['reference'] ) ) ? sanitize_text_field( wp_unslash( $_POST['reference'] ) ) : '';
        $secretKey = ( isset( $_POST['secret_key'] ) ) ? sanitize_text_field( wp_unslash( $_POST['secret_key'] ) ) : ''; // Replace with your Secret Key
    
        $url = "https://api.paystack.co/transaction/verify/" . esc_attr($reference);
        $args = [
            'method'    => 'GET',
            'headers'   => [
                'Authorization' => 'Bearer ' . esc_attr($secretKey),
                'Content-Type'  => 'application/json',
            ],
            'timeout'   => 45, // Set timeout to avoid delays
        ];
    
        // Send the request
        $response = wp_remote_get($url, $args);
    
        if (is_wp_error($response)) {
            echo 3;
            die;
        }
    
        // Get the response body
        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);
    
        if (!isset($result['data']) || $result['status'] !== true) {
            echo 2;
            die;
        }
    
        echo 1; 
        die;
       
    } else {
        echo 3; 
        die;
    }
}
add_action( 'wp_ajax_quads_redeem_coupon', 'quads_redeem_coupon' );
add_action( 'wp_ajax_nopriv_quads_redeem_coupon', 'quads_redeem_coupon' );

function quads_redeem_coupon(){ 
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if( !isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ), 'redeem_coupon_code' )){ 
        return false;
    }
    if(isset($_POST['coupon'])) {
        $coupon = ( isset( $_POST['coupon'] ) ) ? sanitize_text_field( wp_unslash( $_POST['coupon'] ) ) : '';

        $slot_id = ( isset( $_POST['slot_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['slot_id'] ) ) : '';

        $total_cost = ( isset( $_POST['total_cost'] ) ) ? sanitize_text_field( wp_unslash( $_POST['total_cost'] ) ) : '';
        if( $total_cost == '' ||  $total_cost ==0){
            wp_send_json_error( array( 'success'=>2, 'message' => esc_html__('Can not apply coupon code', 'quick-adsense-reloaded' ) ) );
            die;
        }
        if( $coupon != "" && $slot_id != ""){
            $quads_settings = get_option( 'quads_settings' );
            $post_data = get_post_meta($slot_id);
            $discount_name = '';
            $coupon_code = '';
            $coupon_start_date = '';
            $coupon_type_selection = '';
            $coupon_expire_date = '';
            $coupon_amount = '';
            
            if( isset( $post_data[ 'coupon_code' ] ) && $post_data[ 'coupon_code' ] != "" && trim( $coupon )== trim( $post_data[ 'coupon_code' ] ) ){
                $discount_name = ( isset( $post_data[ 'discount_name' ] ) ) ? $post_data[ 'discount_name' ][0] : '';

                $coupon_code = ( isset( $post_data[ 'coupon_code' ] ) && isset( $post_data[ 'coupon_code' ][0] ) ) ? $post_data[ 'coupon_code' ][0] : '';

                $coupon_start_date = ( isset( $post_data[ 'coupon_start_date' ] ) && isset( $post_data[ 'coupon_start_date' ][0] ) ) ? $post_data[ 'coupon_start_date' ][0] : '';

                $coupon_type_selection = ( isset( $post_data[ 'coupon_type_selection' ] ) && isset( $post_data[ 'coupon_type_selection' ][0] ) ) ? $post_data[ 'coupon_type_selection' ][0] : 'percent';

                $coupon_expire_date = ( isset( $post_data[ 'coupon_expire_date' ] ) && isset( $post_data[ 'coupon_expire_date' ][0] ) ) ? $post_data[ 'coupon_expire_date' ][0] : '';

                $coupon_amount = ( isset( $post_data[ 'coupon_amount' ] ) && isset( $post_data[ 'coupon_amount' ][0] ) ) ? $post_data[ 'coupon_amount' ][0] : 10;
            }else if( isset( $quads_settings[ 'coupon_code' ] ) && !empty( $quads_settings[ 'coupon_code' ] )){
                $discount_name = ( isset( $quads_settings[ 'discount_name' ] ) ) ? $quads_settings[ 'discount_name' ] : '';

                $coupon_code = ( isset( $quads_settings[ 'coupon_code' ] ) && isset( $quads_settings[ 'coupon_code' ] ) ) ? $quads_settings[ 'coupon_code' ] : '';

                $coupon_start_date = ( isset( $quads_settings[ 'coupon_start_date' ] ) && isset( $quads_settings[ 'coupon_start_date' ] ) ) ? $quads_settings[ 'coupon_start_date' ] : '';

                $coupon_type_selection = ( isset( $quads_settings[ 'coupon_type_selection' ] ) && isset( $quads_settings[ 'coupon_type_selection' ] ) ) ? $quads_settings[ 'coupon_type_selection' ] : 'percent';

                $coupon_expire_date = ( isset( $quads_settings[ 'coupon_expire_date' ] ) && isset( $quads_settings[ 'coupon_expire_date' ] ) ) ? $quads_settings[ 'coupon_expire_date' ] : '';

                $coupon_amount = ( isset( $quads_settings[ 'coupon_amount' ] ) && isset( $quads_settings[ 'coupon_amount' ] ) ) ? $quads_settings[ 'coupon_amount' ] : 10;
            }
            if( trim( $coupon_code ) != trim( $coupon ) ){
                wp_send_json_error( array( 'success'=>2, 'message' => esc_html__('Invalid coupon, please try another one.', 'quick-adsense-reloaded' ) ) );
                die;
            }
            $today = gmdate('Y-m-d');
            $is_expired = false;
            if( $coupon_expire_date && $coupon_expire_date !='' && $coupon_expire_date < $today ){
                $is_expired = true;
            }
            $resp = array();
            if( $is_expired ){
                wp_send_json_error( array( 'success'=>2, 'message' => esc_html__('Coupon expired, please try another one.', 'quick-adsense-reloaded' ) ) );
                die;
            }
            
            $cal_amount = 0;
            if( $coupon_type_selection=='percent' ){
                $cal_amount = ( $total_cost * $coupon_amount ) / 100;
            }else if( $coupon_type_selection=='fixed_amount' ){
                $cal_amount =$coupon_amount;
            }

            wp_send_json_error( array( 'success'=>1, 'message' => esc_attr( $cal_amount ) ) );
            die;
        }
    } else {
        echo 3; 
        die;
    }
}

add_action( 'wp_ajax_submit_ad_buy_form', 'handle_ad_buy_form_submission' );
add_action( 'wp_ajax_nopriv_submit_ad_buy_form', 'handle_ad_buy_form_submission' );
function quads_handle_submit_disablead_form() {
   
    if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'submit_disablead_form' ) {
        wp_send_json_error( array( 'message' => esc_html__('Invalid request.', 'quick-adsense-reloaded' ) ) );
    }

    if ( ! check_ajax_referer( 'submit_disablead_form', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => esc_html__('Invalid request.', 'quick-adsense-reloaded' ) ) );
    }
  
    // Handle form fields, sanitize input, validate, and process accordingly
    $user_id = is_user_logged_in() ? get_current_user_id() : 0;

    // If user is not logged in, register them using the provided info
    if ( ! $user_id ) {
        $full_name = ( isset( $_POST['full_name'] ) )?sanitize_text_field( wp_unslash( $_POST['full_name']  ) ) : '';
        $email = ( isset( $_POST['email'] ) )?sanitize_email( wp_unslash( $_POST['email'] )  ) : '';
        $password = ( isset( $_POST['password'] ) )?sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';

        if ( empty( $full_name ) || empty( $email ) || empty( $password ) ) {
            wp_send_json_error( array( 'message' => esc_html__('Please fill in all fields.', 'quick-adsense-reloaded' ) ) );
        }

        // Create the new user
        $user_id = wp_create_user( $email, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( array( 'message' => esc_html__('Failed to create account. ', 'quick-adsense-reloaded' ) . $user_id->get_error_message() ) );
        }else{
            $creds = array(
                'user_login'    => $email,
                'user_password' => $password,
                'remember'      => true
            );
            $user = wp_signon($creds, false);
            if ( is_wp_error($user)) {
                wp_send_json_error( array( 'message' => esc_html__('Failed to Login. ', 'quick-adsense-reloaded' )) );
            }
        }
    }

    // Sanitize and validate the remaining fields
    $redirect_link  = ( isset( $_POST['redirect_link'] ) )?esc_url_raw( wp_unslash( $_POST['redirect_link'] ) ): '';
    $cancel_link  = ( isset( $_POST['cancel_link'] ) )?intval( wp_unslash($_POST['cancel_link'] ) ) : '';
    

    // Insert the ad buy record in the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_disabledad_data';
    
    $quads_settings = get_option( 'quads_settings' );
    $currency = isset($quads_settings['_dacurrency']) ? $quads_settings['_dacurrency'] :'USD';
    $price = isset($quads_settings['_dacost']) ? $quads_settings['_dacost'] :0;
    $_daduration = isset($quads_settings['_daduration']) ? $quads_settings['_daduration'] :'Monthly';
    $da_page_id = isset($quads_settings['dapayment_page']) ? $quads_settings['dapayment_page'] : 0;
    $payment_page = get_permalink( $da_page_id );

    $user_info = get_userdata($user_id);
    $user_data = $user_info->data;
    $user_name =  $user_data->display_name;
    $user_email =  $user_data->user_email;
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $result = $wpdb->insert( $table_name, array(
        'user_id'        => $user_id,
        'disable_cost' =>$price,
        'disable_duration' =>$_daduration,
        'username' =>$user_name,
        'user_email' =>$user_email,
        'disable_date' =>gmdate('Y-m-d'),
        'payment_status' => 'pending', // Update after payment
        'disable_status'      => 'pending', // Set to pending until approved
    ) );
    
    if ( $result ) { 
        $redirect_link = rtrim($redirect_link,'/');
        $payment_gateway = isset($quads_settings['_dapayment_gateway']) ? $quads_settings['_dapayment_gateway'] : 'paypal';
        if($payment_gateway=='paypal'){
            $paypal_email =  isset($quads_settings['_dapaypal_email']) ? $quads_settings['_dapaypal_email'] : '';

            if ( empty( $paypal_email ) ) {
                wp_send_json_error( array( 'message' => esc_html__( 'PayPal email not set.Please inform Siteadmin', 'quick-adsense-reloaded' ) ) );
            }

            $currency = isset($quads_settings['_dacurrency']) ? $quads_settings['_dacurrency'] : 'USD';

            $order_id = $wpdb->insert_id;
            // Prepare the PayPal form
            $paypal_form = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">';
            $paypal_form .= '<input type="hidden" name="cmd" value="_xclick">';
            $paypal_form .= '<input type="hidden" name="business" value="'.sanitize_email( $paypal_email ).'">'; // Your PayPal email
            $paypal_form .= '<input type="hidden" name="item_name" value="'.esc_attr( $name).'">';
            $paypal_form .= '<input type="hidden" name="amount" value="'.esc_attr($price).'">';
            $paypal_form .= '<input type="hidden" name="currency_code" value="'.esc_attr($currency).'">';
            $paypal_form .= '<input type="hidden" name="return" value="' . esc_url( $redirect_link.'?status=success&target=disablead' ) . '">';
            $paypal_form .= '<input type="hidden" name="cancel_return" value="' . esc_url( $redirect_link.'?status=cancelled&target=disablead' ) . '">';
            $paypal_form .= '<input type="hidden" name="notify_url" value="' . esc_url( rest_url('wpquads/v1/paypal_disable_ad_notify_url') ) . '">';
            $paypal_form .= '<input type="hidden" name="item_number" value="' . esc_attr($order_id) . '">';
            $paypal_form .= '<input type="hidden" name="custom" value="' . esc_attr($user_id) . '">';

            wp_send_json_success( array( 'message' => esc_html__( 'Ad submission successful.', 'quick-adsense-reloaded' ) , 'paypal_form'=>$paypal_form) );
        }else if($payment_gateway=='authorize'){
            $authorize_name =  isset($quads_settings['_daauthorize_name']) ? $quads_settings['_daauthorize_name'] : '';
            $authorize_transactionKey =  isset($quads_settings['_daauthorize_transactionKey']) ? $quads_settings['_daauthorize_transactionKey'] : '';
            $authorize_merchant_name =  isset($quads_settings['_daauthorize_merchant_name']) ? $quads_settings['_daauthorize_merchant_name'] : '';

            if ( empty( $authorize_name ) || empty( $authorize_transactionKey ) ) {
                wp_send_json_error( array( 'message' => esc_html__('Authorize Credentials are not set. Please inform Siteadmin', 'quick-adsense-reloaded' ) ) );
            }
            $currency = isset($quads_settings['_dacurrency']) ? $quads_settings['_dacurrency'] : 'USD';

            $order_id = $wpdb->insert_id;
            //$authorize_url ='https://apitest.authorize.net/xml/v1/request.api';
            $authorize_url ='https://api.authorize.net/xml/v1/request.api';
            $redirect_link = rtrim($redirect_link,'/');
            $success_nonce = wp_create_nonce( 'submit_ad_buy_form_success' );
            $success_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&target=disablead&status=success&user_id='.$user_id.'&security='.esc_attr($success_nonce);
            $cancel_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&target=disablead&cancel=true&user_id='.$user_id;
        
         $send_data = '{
                "getHostedPaymentPageRequest": {
                  "merchantAuthentication": {
                    "name": "'.esc_attr( $authorize_name ).'",
                    "transactionKey": "'.esc_attr( $authorize_transactionKey ).'"
                  },
                  "refId": "'.esc_attr( $order_id ).'",
                  "transactionRequest": {
                    "transactionType": "authCaptureTransaction",
                    "amount": "'.esc_attr( $price ).'",
                    "profile": {
                      "customerProfileId": "'.esc_attr( $user_id ).'"
                    },
                    "customer": {
                      "email": ""
                    }
                  },
                  "hostedPaymentSettings": {
                    "setting": [{
                      "settingName": "hostedPaymentReturnOptions",
                      "settingValue": "{\"showReceipt\": true, \"url\": \"'.esc_url( $success_link ).'\", \"urlText\": \"Continue\", \"cancelUrl\": \"'.esc_url( $cancel_link ).'\", \"cancelUrlText\": \"Cancel\"}"
                    }, {
                      "settingName": "hostedPaymentButtonOptions",
                      "settingValue": "{\"text\": \"Pay\"}"
                    }, {
                      "settingName": "hostedPaymentStyleOptions",
                      "settingValue": "{\"bgColor\": \"blue\"}"
                    }, {
                      "settingName": "hostedPaymentPaymentOptions",
                      "settingValue": "{\"cardCodeRequired\": false, \"showCreditCard\": true, \"showBankAccount\": true}"
                    }, {
                      "settingName": "hostedPaymentSecurityOptions",
                      "settingValue": "{\"captcha\": false}"
                    }, {
                      "settingName": "hostedPaymentShippingAddressOptions",
                      "settingValue": "{\"show\": false, \"required\": false}"
                    }, {
                      "settingName": "hostedPaymentBillingAddressOptions",
                      "settingValue": "{\"show\": true, \"required\": false}"
                    }, {
                      "settingName": "hostedPaymentCustomerOptions",
                      "settingValue": "{\"showEmail\": false, \"requiredEmail\": false, \"addPaymentProfile\": true}"
                    }, {
                      "settingName": "hostedPaymentOrderOptions",
                      "settingValue": "{\"show\": true, \"merchantName\": \"'.esc_attr( $authorize_merchant_name ).'\"}"
                    }, {
                      "settingName": "hostedPaymentIFrameCommunicatorUrl",
                      "settingValue": "{\"url\": \"'.esc_url( $success_link ).'\"}"
                    }]
                  }
                }
              }';
             // echo $send_data;
             // die;
            $response = wp_remote_post($authorize_url, array(
                'headers'   => array('content-type' => 'application/json'),
                'body'      => $send_data,
                'method'    => 'POST'
            ));
            
             // Make sure there are no errors
              if ( is_wp_error( $response ) ) {    
                wp_send_json_error( array( 'message' => esc_html__( 'Processing failed.', 'quick-adsense-reloaded' ) ) );
                die;
              }
              
              $resp_data = wp_remote_retrieve_body( $response );
              $re = str_replace( '﻿', '', $resp_data );

              $re = wp_json_encode( $resp_data );
              $re = str_replace( '\ufeff', '', $re);
              $re = json_decode( $re );
              $re = json_decode( $re,true );
            if( isset( $re['token'] ) && $re['token']!="" ){
                $token = $re['token'];
                //$form_url = 'https://test.authorize.net/payment/payment';
                $form_url = 'https://accept.authorize.net/payment/payment';
                $auth_form ='<!doctype html>
                            <html lang="en">
                            <head>
                                <meta charset="utf-8">
                                <title>Hosted Accept.js Payment Form</title>
                            </head>
                            <body>
                                <form id="paymentForm" method="POST" action="'.esc_url( $form_url ).'">
                                    <input type="hidden" name="token" id="token" value="'.esc_attr( $token ).'" />
                                </form>
                            </body>
                            </html>';
            wp_send_json_success( array( 'message' => esc_html__( 'Ad submission successful.', 'quick-adsense-reloaded' ) , 'paypal_form'=>$auth_form) );
            }else {
                wp_send_json_error( array( 'message' => esc_html__('Failed to process payment.', 'quick-adsense-reloaded' ) ) );
            }
        }else if($payment_gateway=='stripe'){
            $stripe_publishable_key =  isset($quads_settings['_dastripe_publishable_key']) ? $quads_settings['_dastripe_publishable_key'] : '';
        
            $stripe_secret_key =  isset($quads_settings['_dastripe_secret_key']) ? $quads_settings['_dastripe_secret_key'] : '';
            if ( empty( $stripe_secret_key ) || empty( $stripe_publishable_key ) ) {
                wp_send_json_error( array( 'message' => esc_html__('Stripe Credentials are not set. Please inform Siteadmin', 'quick-adsense-reloaded' ) ) );
            }
            $currency = isset($quads_settings['_dacurrency']) ? $quads_settings['_dacurrency'] : 'USD';

            $order_id = $wpdb->insert_id;
            $redirect_link = rtrim($redirect_link,'/');
            $success_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&target=disablead&status=success&user_id='.$user_id;
            $cancel_link = $redirect_link.'?refId='.esc_attr( $order_id ).'&target=disablead&cancel=true&user_id='.$user_id;
            require_once('stripe/vendor/autoload.php'); // Get this from Stripe's PHP SDK
            \Stripe\Stripe::setApiKey($stripe_secret_key);
            try {
                $total_cost = $price*100;
                // Create a PaymentIntent
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => esc_attr( $total_cost ), // Amount in cents
                    'currency' => esc_attr( strtolower($currency) ),
                    'payment_method_types' => ['card'], // Use 'card' as the payment method
                ]);
            
                $output = [
                    'clientSecret' => $paymentIntent->client_secret,
                ];
            
                wp_send_json_success( array( 'message' => esc_html__( 'Ad submission successful.', 'quick-adsense-reloaded' ) , 'id' => $paymentIntent->client_secret,'success_link'=>$success_link,'cancel_url'=>$cancel_link) );
                die;
            } catch (\Stripe\Exception\ApiErrorException $e) {
                wp_send_json_error( array( 'message' => esc_html__('Failed to submit ad.', 'quick-adsense-reloaded' ) ) );
                die;
            }
        }
    } else {
        wp_send_json_error( array( 'message' => esc_html__('Failed to submit ad.', 'quick-adsense-reloaded' ) ) );
        die;
    }
}
add_action( 'wp_ajax_submit_disablead_form', 'quads_handle_submit_disablead_form' );
add_action( 'wp_ajax_nopriv_submit_disablead_form', 'quads_handle_submit_disablead_form' );

add_action('rest_api_init', function () {
    register_rest_route('wpquads/v1', '/paypal_notify_url', array(
        'methods'  => 'POST',
        'callback' => 'wpquads_handle_paypal_notify',
        'permission_callback' => '__return_true',
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('wpquads/v1', '/paypal_disable_ad_notify_url', array(
        'methods'  => 'POST',
        'callback' => 'wpquads_handle_paypal_disable_ad_notify',
        'permission_callback' => '__return_true',
    ));
});

function wpquads_handle_paypal_notify(WP_REST_Request $request) {
    $params = $request->get_params();
    $payment_status = isset($params['payment_status']) ? sanitize_text_field($params['payment_status']) : '';
    $order_id     = isset($params['item_number']) ? intval($params['item_number']) : 0;
    $payer_email    = isset($params['payer_email']) ? sanitize_email($params['payer_email']) : '';
    $user_id = isset($params['custom']) ? intval($params['custom']) : 0;
    $total_cost = isset($params['mc_gross']) ? floatval($params['mc_gross']) : 0;
    $test_ipn = isset($params['test_ipn']) ? floatval($params['test_ipn']) : 0;
    $user = get_user_by('id', $user_id);
    $post_data = wp_unslash($params);

    // Prepare PayPal verification request
    $req = 'cmd=_notify-validate';
    foreach ($post_data as $key => $value) {
        $req .= "&$key=" . urlencode($value);
    }

    // Validate with PayPal
    //$paypal_url = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr";
    $paypal_url = "https://ipnpb.paypal.com/cgi-bin/webscr";
    $response = wp_remote_post($paypal_url, array(
        'body'      => $req,
        'timeout'   => 30,
        'sslverify' => true,
    ));
    if (is_wp_error($response) || wp_remote_retrieve_body($response) !== 'VERIFIED') {
        return new WP_REST_Response(array('status' => 'error', 'message' => esc_html__( 'PayPal IPN verification failed.', 'quick-adsense-reloaded' ) ), 404);
    }
    $params['re'] = wp_remote_retrieve_body($response);
    // Check if the payment is complete
    if ($user && $payment_status === 'Completed') {
        // Update your database, set ad status to 'active', etc.
        // Example: Mark the ad as paid in your custom table
        global $wpdb;
        $table_name = $wpdb->prefix . 'quads_adbuy_data';
       
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,  WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $ad_details = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND user_id = %d",$order_id,$user->ID) );
        if (!$ad_details) {
            return new WP_REST_Response( array( 'status' => 'error', 'message' => esc_html__( 'Ad not found', 'quick-adsense-reloaded' ) ), 404);
        }

        if ($ad_details->payment_status === 'paid') {
            return new WP_REST_Response( array('status' => 'error', 'message' => esc_html__( 'Ad already paid', 'quick-adsense-reloaded' ) ), 400);
        }
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update(
            $table_name,
            array('payment_status' => 'paid' , 'payment_response'=> wp_json_encode($params)), // Data to update
            array('id' => $order_id , 'user_id'=>$user->ID) 
        );

        //get the ad details from db
        $setting= get_option('quads_settings',[]);
        $currency = isset($setting['currency']) ? $setting['currency'] :'USD';

        $ad_details_html = "";
        //send email to  user and admin
        $to = $payer_email;
        $subject = esc_html__( 'Ad Payment Confirmation', 'quick-adsense-reloaded' );
        $message = esc_html__( 'Your ad payment has been confirmed. Your ad will be live soon.', 'quick-adsense-reloaded' ).PHP_EOL;
        //also add the ad details in the email
        $ad_details_html .= esc_html__( 'Ad Details: ', 'quick-adsense-reloaded' ) . PHP_EOL;
        $ad_details_html .= esc_html__('Ad Slot: ', 'quick-adsense-reloaded' ) . esc_attr(get_the_title($ad_details->ad_id )) . PHP_EOL;
        $ad_details_html .= esc_html__('Start Date: ', 'quick-adsense-reloaded' ) . esc_html($ad_details->start_date) . PHP_EOL;
        $ad_details_html .= esc_html__('End Date: ', 'quick-adsense-reloaded' ) .  esc_html($ad_details->end_date) . PHP_EOL;
        $ad_details_html .= esc_html__('Ad Link: ', 'quick-adsense-reloaded' ) .  esc_html($ad_details->ad_link) . PHP_EOL;
        $ad_details_html .= esc_html__('Ad Image: ', 'quick-adsense-reloaded' ) .  esc_html($ad_details->ad_image) . PHP_EOL;
        $ad_details_html .= esc_html__('Total Cost: ', 'quick-adsense-reloaded' ) . esc_html($currency . $total_cost) . PHP_EOL;
        $ad_details_html .= esc_html__('Payment Status: ', 'quick-adsense-reloaded' ) . esc_html($payment_status) . PHP_EOL;
        $ad_details_html .= esc_html__('Payer Email: ', 'quick-adsense-reloaded' ) . esc_html($payer_email) . PHP_EOL;
        $ad_details_html .= esc_html__('Order ID: ', 'quick-adsense-reloaded' ) . esc_html($order_id) . PHP_EOL;
        $ad_details_html .= esc_html__('Order ID: ', 'quick-adsense-reloaded' ) . esc_html($order_id) . PHP_EOL;
        $message .= $ad_details_html;
        

        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $message, $headers );

        $to = get_option('admin_email');
        $subject = esc_html__( 'Ad Payment Confirmation', 'quick-adsense-reloaded' );
        $message = esc_html__( 'Ad payment has been confirmed for user: ', 'quick-adsense-reloaded' ) . $payer_email. PHP_EOL;
        $message = esc_html__( 'Please  review the AD so that it can go live ', 'quick-adsense-reloaded' ). PHP_EOL;
        //also add reminder to review the ad

        $message .= $ad_details_html;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $message, $headers );

    }

    // Respond back with a success message
    return new WP_REST_Response(array('status' => esc_html__( 'success', 'quick-adsense-reloaded' )), 200);
}
function wpquads_handle_paypal_disable_ad_notify(WP_REST_Request $request) {
    $params = $request->get_params();

    $payment_status = isset($params['payment_status']) ? sanitize_text_field($params['payment_status']) : '';
    $order_id     = isset($params['item_number']) ? intval($params['item_number']) : 0;
    $payer_email    = isset($params['payer_email']) ? sanitize_email($params['payer_email']) : '';
    $user_id = isset($params['custom']) ? intval($params['custom']) : 0;
    $total_cost = isset($params['mc_gross']) ? floatval($params['mc_gross']) : 0;
    $test_ipn = isset($params['test_ipn']) ? floatval($params['test_ipn']) : 0;
    $user = get_user_by('id', $user_id);
    // Prepare PayPal verification request
    $post_data = wp_unslash($params);
    $req = 'cmd=_notify-validate';
    foreach ($post_data as $key => $value) {
        $req .= "&$key=" . urlencode($value);
    }

    // Validate with PayPal
    //$paypal_url = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr";
    $paypal_url = "https://ipnpb.paypal.com/cgi-bin/webscr";
    $response = wp_remote_post($paypal_url, array(
        'body'      => $req,
        'timeout'   => 30,
        'sslverify' => true,
    ));
    if (is_wp_error($response) || wp_remote_retrieve_body($response) !== 'VERIFIED') {
        return new WP_REST_Response(array('status' => 'error', 'message' => esc_html__('PayPal IPN verification failed.', 'quick-adsense-reloaded' )), 404);
    }
    $params['re'] = wp_remote_retrieve_body($response);
    // Check if the payment is complete
    if ($user && $payment_status === 'Completed') {
        // Update your database, set ad status to 'active', etc.
        // Example: Mark the ad as paid in your custom table
        global $wpdb;
        $table_name = $wpdb->prefix . 'quads_disabledad_data';
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,  WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $ad_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE disable_ad_id = %d AND user_id = %d",$order_id,$user->ID ) );
        if (!$ad_details) {
            return false;
        }
        $payment_status = 'paid';
        if ($ad_details->payment_status === 'paid') {
            return false;
        }
        $duration = $ad_details->disable_duration;
        $params = array();
        $params['payment_date'] = gmdate('Y-m-d H:i:s');
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update(
            $table_name,
            array('payment_status' => 'paid' , 'payment_response'=> wp_json_encode($params)), // Data to update
            array('disable_ad_id' => $order_id , 'user_id'=>$user->ID) 
        );

        //get the ad details from db
        $setting= get_option('quads_settings',[]);
        $currency = isset($setting['_dacurrency']) ? $setting['_dacurrency'] :'USD';
        $price = isset($setting['_dacost']) ? $setting['_dacurrency'] :'USD';
        $payer_email = $user->user_email;
        $ad_details_html = "";
        //send email to  user and admin
        $to = $payer_email;
        $subject = esc_html__( 'Payment Confirmation', 'quick-adsense-reloaded' );
        $message = esc_html__( 'Your payment has been confirmed', 'quick-adsense-reloaded' ).PHP_EOL;

        $total_cost = $price;
        //also add the ad details in the email
        $ad_details_html .=  esc_html__( 'Ad Details: ', 'quick-adsense-reloaded' ).PHP_EOL;
        $ad_details_html .=  esc_html__( 'Total Cost: ', 'quick-adsense-reloaded' ) . esc_html($currency . $total_cost) . PHP_EOL;
        $ad_details_html .=  esc_html__( 'Duration: ', 'quick-adsense-reloaded' ) . esc_html($duration) . PHP_EOL;
        $ad_details_html .=  esc_html__( 'Payment Status: ', 'quick-adsense-reloaded' ) . esc_html($payment_status) . PHP_EOL;
        $ad_details_html .= esc_html__( 'Payer Email: ', 'quick-adsense-reloaded' ) . esc_html($payer_email) . PHP_EOL;
        $ad_details_html .= esc_html__( 'Order ID: ', 'quick-adsense-reloaded' ) . esc_html($order_id) . PHP_EOL;
        $message .= $ad_details_html;
        

        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $message, $headers );

        $to = get_option('admin_email');
        $subject = esc_html__( 'Ad Payment Confirmation', 'quick-adsense-reloaded' );
        $message = esc_html__( 'Ad payment has been confirmed for user: ', 'quick-adsense-reloaded' ) . $payer_email. PHP_EOL;
        
        //also add reminder to review the ad

        $message .= $ad_details_html;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $message, $headers );
    }

    // Respond back with a success message
    return new WP_REST_Response(array('status' => esc_html__( 'success', 'quick-adsense-reloaded' )), 200);
}

function quads_get_active_ads_by_slot( $slot_id = null ){
    if( ! $slot_id ){
        return false;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data';
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,  WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $active_ads = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name Where  ad_id = %d and payment_status = 'paid' and ad_status = 'approved' and end_date >= CURDATE() and start_date <= CURDATE()", $slot_id ) );
    return $active_ads;
}

function quads_get_active_sellads_ids( ){

    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data';
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $active_ads = $wpdb->get_results( "SELECT ad_id FROM $table_name Where  payment_status = 'paid' and ad_status = 'approved' and end_date >= CURDATE() and start_date <= CURDATE()" );
    return $active_ads;
}

/**
 * Send Expired Ad Notification Email
 * @param mixed $ad_id
 * @param mixed $user_id
 * @param string $type Notification type (reminder or expiry)
 * @return void
 */
function quads_send_ad_expiry_email( $ad_id, $user_id, $type = 'expiry' ){
    $ad = get_post( $ad_id );
    $user = get_user_by('id', $user_id);
    $to = $user->user_email;
    $subject = ($type === 'reminder') ? esc_html__( 'Ad Expiry Reminder', 'quick-adsense-reloaded' ) : esc_html__( 'Ad Expiry Notification', 'quick-adsense-reloaded' );
    
    if ( $type === 'reminder' ) {
        $message = esc_html__( 'Your ad is set to expire in 2 days. Please renew your ad to continue showing it on the site. Renew your ad here: ', 'quick-adsense-reloaded' )
            . '<a href="' . esc_url( site_url('buy-adspace') ) . '" target="_blank">' . esc_url( site_url('buy-adspace') ) . '</a>' . PHP_EOL;
    } else {
        $message = esc_html__( 'Your ad has expired and is no longer showing on the site. Please renew your ad to continue showing it on the site. Renew your ad here: ', 'quick-adsense-reloaded' )
            . '<a href="' . esc_url( site_url('buy-adspace') ) . '" target="_blank">' . esc_url( site_url('buy-adspace') ) . '</a>' . PHP_EOL;
    }

    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $to, $subject, $message, $headers );
}


// Schedule the cron job if it’s not already scheduled
if ( ! wp_next_scheduled( 'quads_daily_check_expired_sellads' ) ) {
    $quads_settings = get_option( 'quads_settings' );
    if( (isset($quads_settings['email_notification_adsell_expiry']) && $quads_settings['email_notification_adsell_expiry']) || empty( $quads_settings['email_notification_adsell_expiry'] ) ){
        wp_schedule_event( time(), 'daily', 'quads_daily_check_expired_sellads' );
    } else {
        wp_clear_scheduled_hook( 'quads_daily_check_expired_sellads' );
    }
}
add_action( 'quads_daily_check_expired_sellads', 'quads_check_expired_sellads' );

/**
 * Checks for ads that expired yesterday and ads expiring in two days, sending notification emails accordingly.
 */
function quads_check_expired_sellads() {
    $yesterday = gmdate( 'Y-m-d', strtotime( '-1 day', current_time( 'timestamp' ) ) );
    $two_days_ahead = gmdate( 'Y-m-d', strtotime( '+2 days', current_time( 'timestamp' ) ) );

    $query_args = [
        'post_type'      => 'quads-ads',
        'posts_per_page' => -1,
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
        'meta_query'     => [
            'relation' => 'AND',
            [
                'key'     => 'ad_type',
                'value'   => 'ads_space',
                'compare' => '=',
            ],
            [
                'relation' => 'OR',
                [
                    'key'     => 'end_date',
                    'value'   => $yesterday,
                    'compare' => '=',
                    'type'    => 'DATE',
                ],
                [
                    'key'     => 'end_date',
                    'value'   => $two_days_ahead,
                    'compare' => '=',
                    'type'    => 'DATE',
                ],
            ],
        ],
    ];

    // Get relevant ads using WP_Query
    $ads = new WP_Query( $query_args );

    if ( $ads->have_posts() ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'quads_adbuy_data';

        // Collect ad IDs and determine email type based on expiration date
        $ad_ids = [];
        $ad_email_types = [];
        while ( $ads->have_posts() ) {
            $ads->the_post();
            $ad_id = get_the_ID();
            $end_date = get_post_meta( $ad_id, 'end_date', true );

            if ( $end_date === $yesterday ) {
                $ad_email_types[$ad_id] = 'expiry';
            } elseif ( $end_date === $two_days_ahead ) {
                $ad_email_types[$ad_id] = 'reminder';
            }
            $ad_ids[] = $ad_id;
        }
        wp_reset_postdata();

        // Only proceed if there are ad IDs to check
        if ( ! empty( $ad_ids ) ) {
            $placeholders = implode( ',', array_fill( 0, count( $ad_ids ), '%d' ) );
            

            // Execute the query and get results
            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $users = $wpdb->get_results(  $wpdb->prepare(
               // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare	
                "SELECT user_id, ad_id FROM $table_name WHERE ad_id IN ($placeholders)",
                ...$ad_ids
            ) );

            // Process each user and ad combination
            foreach ( $users as $user ) {
                $user_id = $user->user_id;
                $ad_id   = $user->ad_id;

                // Determine email type and send the notification
                if ( isset( $ad_email_types[$ad_id] ) ) {
                    quads_send_ad_expiry_email( $ad_id, $user_id, $ad_email_types[$ad_id] );
                }
            }
        }
    }
}

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
    $existing_page = get_page_by_path( 'buy-adspace' ); // You can change the slug

    // If the page doesn't exist, create a new page
    if ( ! $existing_page ) {
        $page_data = array(
            'post_title'     => esc_html__( 'Buy Adspace', 'quick-adsense-reloaded' ),
            'post_content'   => '[quads_buy_form]',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_author'    => get_current_user_id(),
            'post_name'      => 'buy-adspace', // Custom slug
        );

        $page_id = wp_insert_post( $page_data ); // Create the page

        if ( $page_id && ! is_wp_error( $page_id ) ) {
            // Save the page slug or ID in the options table
            update_option( 'quads_sell_page', 'buy-adspace' ); // You can store the page slug or ID
        }
    }
}
add_action( 'admin_init', 'quads_create_sellpage_on_activation' );

add_action( 'upgrader_process_complete', 'quads_adsell_upgrade_handler', 10, 2 );

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
        'meta_query'     => array(
            array(
                'key'     => 'ad_type',
                'value'   => 'ads_space',
                'compare' => '='
            ),
        ),
    );
    
    
    $ads = get_posts( $args );

    if ( $ads ) {
        $booked_ads = quads_get_active_sellads_ids();
        foreach ( $ads as $ad ) {
            if( in_array( $ad, array_column( $booked_ads, 'ad_id' ) ) ){
                continue;
            }
            $ad_list[ $ad ] [ 'name' ]= get_the_title( $ad );
            $ad_list[ $ad ] [ 'price' ]= get_post_meta( $ad , 'ad_cost', true )? get_post_meta( $ad , 'ad_cost', true ) : 999;
            $ad_list[ $ad ] [ 'currency' ]= get_post_meta( $ad , 'USD', true )? get_post_meta( $ad , 'USD', true ) : 'USD';
            $ad_list[ $ad ] [ 'type' ]= get_post_meta( $ad , 'ad_cost_type', true ) ? get_post_meta( $ad , 'ad_cost_type', true ) : 'per day';
        }
    }

    if ( empty( $ad_list ) ) {
        return '<h2>'.esc_html__('No ad slots available for purchase','quick-adsense-reloaded').'</h2>';
    }


    // get  my ads from table wp_quads_adbuy_data
    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data';
    $user_id = get_current_user_id();
    $my_ads = $wpdb->get_results( "SELECT * FROM $table_name Where user_id = $user_id" );


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
   
    <form id="quads-adbuy-form" method="POST" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">
    <?php

    if (isset($_GET['status']) && $_GET['status'] == 'success') {
        echo '<div class="notice notice-success is-dismissible">
    <p>AD Successfully Submitted. You will get a confirmation email when your payment is confirmed.</p>
</div>';
    } elseif (isset($_GET['status']) && $_GET['status'] == 'cancelled') {
        echo '<div class="notice notice-error is-dismissible">
    <p>AD Payment Cancelled. Please try again.</p>
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
        <?php
        $selected_ad_slot = isset($_GET['ad_slot_id']) ? sanitize_text_field( wp_unslash( $_GET['ad_slot_id'] ) ) : ''; // Get and sanitize ad_slot_id from GET
        ?>
        <!-- Step 2: Campaign Details Section -->
        <div id="campaign-section" class="form-section">
            <h2><?php echo esc_html__('Campaign Details','quick-adsense-reloaded');?></h2>
            <label for="ad_slot_id"><?php echo esc_html__('Select Ad Slot','quick-adsense-reloaded');?></label>

<select name="ad_slot_id" id="ad_slot_id" required>
    <option value=""><?php echo esc_html__('Select Ad Slot', 'quick-adsense-reloaded'); ?></option>
    <?php foreach ( $ad_list as $key => $value ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>" data-price="<?php echo esc_attr( $value['price'] ); ?>"
            <?php selected( $selected_ad_slot, $key ); // Check if this is the selected option ?>>
            <?php echo esc_html( $value['name'] ); ?> (<?php echo esc_html( $value['currency'] ); ?> <?php echo esc_html( $value['price'] ); ?> <?php echo esc_html( $value['type'] ); ?>)
        </option>
    <?php endforeach; ?>
</select>


            <label for="start_date"><?php echo esc_html__('Start Date','quick-adsense-reloaded');?></label>
            <input type="date" name="start_date" id="start_date" required />

            <label for="end_date"><?php echo esc_html__('End Date','quick-adsense-reloaded');?></label>
            <input type="date" name="end_date" id="end_date" required />

            <label for="ad_link"><?php echo esc_html__('Ad Link','quick-adsense-reloaded');?></label>
            <input type="url" name="ad_link" id="ad_link" required placeholder="Ad Link"/>

            <label for="ad_content"><?php echo esc_html__('Ad Content','quick-adsense-reloaded');?> <small>(This will be ignored if Ad image is present)</small></label>
            <textarea name="ad_content" id="ad_content" rows="4"> You ad text here</textarea>

            <label for="ad_image"><?php echo esc_html__('Upload Ad Image','quick-adsense-reloaded');?> (optional) </label>
            <input type="file" name="ad_image" id="ad_image" accept="image/*" />
        </div>

        <!-- Step 3: Summary and Payment Section -->
        <div id="summary-section" class="form-section">
            <h2><?php echo esc_html__('Summary','quick-adsense-reloaded');?></h2>
            <p><strong><?php echo esc_html__('Selected Slot:','quick-adsense-reloaded');?></strong> <span id="summary-slot"></span></p>
            <p><strong><?php echo esc_html__('Start Date:','quick-adsense-reloaded');?></strong> <span id="summary-start-date"></span></p>
            <p><strong><?php echo esc_html__('End Date:','quick-adsense-reloaded');?></strong> <span id="summary-end-date"></span></p>
            <p><strong><?php echo esc_html__('Total Cost:','quick-adsense-reloaded');?></strong> $<span id="total-cost">0</span></p>

            <input type="hidden" name="action" value="submit_ad_buy_form" />
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'submit_ad_buy_form' )?>" />

            <!-- PayPal Payment Button -->
            <div id="paypal-button-container"></div>
        </div>

        <button type="submit"><?php echo esc_html__('Submit','quick-adsense-reloaded');?></button>
    </form>

    <script>


       document.getElementById('ad_slot_id').addEventListener('change', function() {
    const pricePerDay = this.options[this.selectedIndex].getAttribute('data-price');
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (startDate && endDate && isValidDateRange(startDate, endDate)) {
        const days = calculateDays(startDate, endDate);
        document.getElementById('total-cost').innerText = pricePerDay * days;
    }

    document.getElementById('summary-slot').innerText = this.options[this.selectedIndex].text;
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
                paypalFormContainer.innerHTML = response.data.paypal_form;
                document.body.appendChild(paypalFormContainer);

                // Automatically submit the PayPal form
                var paypalForm = paypalFormContainer.querySelector('form');
                if (paypalForm) {
                    paypalForm.submit();
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


    </script>
    <?php
    // Return the buffered content
    return ob_get_clean();
}

add_shortcode( 'quads_buy_form', 'quads_ads_buy_form' );


function handle_ad_buy_form_submission() {
   
    if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'submit_ad_buy_form' ) {
        wp_send_json_error( array( 'message' => 'Invalid request.' ) );
    }

    if ( ! check_ajax_referer( 'submit_ad_buy_form', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => 'Invalid request.' ) );
    }

    // Handle form fields, sanitize input, validate, and process accordingly
    $user_id = is_user_logged_in() ? get_current_user_id() : 0;

    // If user is not logged in, register them using the provided info
    if ( ! $user_id ) {
        $full_name = sanitize_text_field( wp_unslash( $_POST['full_name']  ) );
        $email = sanitize_email( wp_unslash( $_POST['email'] )  );
        $password = sanitize_text_field( wp_unslash( $_POST['password'] ) );

        if ( empty( $full_name ) || empty( $email ) || empty( $password ) ) {
            wp_send_json_error( array( 'message' => 'Please fill in all fields.' ) );
        }

        // Create the new user
        $user_id = wp_create_user( $email, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( array( 'message' => 'Failed to create account. ' . $user_id->get_error_message() ) );
        }
    }

    // Sanitize and validate the remaining fields
    $ad_slot_id  = intval( wp_unslash($_POST['ad_slot_id'] ) );
    $start_date  = sanitize_text_field( wp_unslash($_POST['start_date'] ) );
    $end_date    = sanitize_text_field( wp_unslash($_POST['end_date'] ) );
    $ad_link     = esc_url_raw( wp_unslash( $_POST['ad_link'] ) );
    $ad_content  = isset($_POST['ad_content']) ? sanitize_textarea_field( wp_unslash ($_POST['ad_content'] ) ):'';
    $ad_image    = ''; // Initialize the ad image URL

    // Handle file upload if provided
    if ( ! empty( $_FILES['ad_image']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $uploaded_file = wp_handle_upload( $_FILES['ad_image'], array( 'test_form' => false ) );

        if ( isset( $uploaded_file['url'] ) ) {
            $ad_image = esc_url_raw( $uploaded_file['url'] );
        } else {
            wp_send_json_error( array( 'message' => 'Image upload failed.' ) );
        }
    }

    // Insert the ad buy record in the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data';

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
        $quads_settings = get_option( 'quads_settings' );
        $paypal_email =  isset($quads_settings['paypal_email']) ? $quads_settings['paypal_email'] : '';

        if ( empty( $paypal_email ) ) {
            wp_send_json_error( array( 'message' => 'PayPal email not set.Please inform Siteadmin' ) );
        }

        $currency = isset($quads_settings['currency']) ? $quads_settings['currency'] : 'USD';

        $order_id = $wpdb->insert_id;
        // Prepare the PayPal form
        $paypal_form = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">';
        $paypal_form .= '<input type="hidden" name="cmd" value="_xclick">';
        $paypal_form .= '<input type="hidden" name="business" value="'.sanitize_email( $paypal_email ).'">'; // Your PayPal email
        $paypal_form .= '<input type="hidden" name="item_name" value="'.esc_attr( $name).'">';
        $paypal_form .= '<input type="hidden" name="amount" value="'.esc_attr($total_cost).'">';
        $paypal_form .= '<input type="hidden" name="currency_code" value="'.esc_attr($currency).'">';
        $paypal_form .= '<input type="hidden" name="return" value="' . esc_url( site_url( 'buy-adspace' ).'?status=success' ) . '">';
        $paypal_form .= '<input type="hidden" name="cancel_return" value="' . esc_url( site_url( 'buy-adspace' ).'?status=cancelled' ) . '">';
        $paypal_form .= '<input type="hidden" name="notify_url" value="' . esc_url( rest_url('wpquads/v1/paypal_notify_url') ) . '">';
        $paypal_form .= '<input type="hidden" name="item_number" value="' . esc_attr($order_id) . '">';
        $paypal_form .= '<input type="hidden" name="custom" value="' . esc_attr($user_id) . '">';

        wp_send_json_success( array( 'message' => 'Ad submission successful.' , 'paypal_form'=>$paypal_form) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to submit ad.' ) );
    }
}

add_action( 'wp_ajax_submit_ad_buy_form', 'handle_ad_buy_form_submission' );
add_action( 'wp_ajax_nopriv_submit_ad_buy_form', 'handle_ad_buy_form_submission' );


add_action('rest_api_init', function () {
    register_rest_route('wpquads/v1', '/paypal_notify_url', array(
        'methods'  => 'POST',
        'callback' => 'wpquads_handle_paypal_notify',
        'permission_callback' => '__return_true', // You can define your own permissions check
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

    // Check if the payment is complete
    if ($user && $payment_status === 'Completed') {
        // Update your database, set ad status to 'active', etc.
        // Example: Mark the ad as paid in your custom table
        global $wpdb;
        $table_name = $wpdb->prefix . 'quads_adbuy_data';

        $ad_details = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $order_id AND user_id = $user->ID");
        if (!$ad_details) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Ad not found'), 404);
        }

        if ($ad_details->payment_status === 'paid') {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Ad already paid'), 400);
        }
        
        $wpdb->update(
            $table_name,
            array('payment_status' => 'paid' , 'payment_response'=> json_encode($params)), // Data to update
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
        $ad_details_html .= 'Ad Details: '.PHP_EOL;
        $ad_details_html .= 'Ad Slot: ' . get_the_title($ad_details->ad_id ) . PHP_EOL;
        $ad_details_html .= 'Start Date: ' . esc_html($ad_details->start_date) . PHP_EOL;
        $ad_details_html .= 'End Date: ' .  esc_html($ad_details->end_date) . PHP_EOL;
        $ad_details_html .= 'Ad Link: ' .  esc_html($ad_details->ad_link) . PHP_EOL;
        $ad_details_html .= 'Ad Image: ' .  esc_html($ad_details->ad_image) . PHP_EOL;
        $ad_details_html .= 'Total Cost: ' . esc_html($currency . $total_cost) . PHP_EOL;
        $ad_details_html .= 'Payment Status: ' . esc_html($payment_status) . PHP_EOL;
        $ad_details_html .= 'Payer Email: ' . esc_html($payer_email) . PHP_EOL;
        $ad_details_html .= 'Order ID: ' . esc_html($order_id) . PHP_EOL;
        $ad_details_html .= 'Order ID: ' . esc_html($order_id) . PHP_EOL;
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
    return new WP_REST_Response(array('status' => 'success'), 200);
}

function quads_get_active_ads_by_slot( $slot_id = null ){
    if( ! $slot_id ){
        return false;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data';
    $active_ads = $wpdb->get_results( "SELECT * FROM $table_name Where  ad_id = $slot_id and payment_status = 'paid' and ad_status = 'approved' and end_date >= CURDATE() and start_date <= CURDATE()" );
    return $active_ads;
}

function quads_get_active_sellads_ids( ){

    global $wpdb;
    $table_name = $wpdb->prefix . 'quads_adbuy_data';
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
    $yesterday = date( 'Y-m-d', strtotime( '-1 day', current_time( 'timestamp' ) ) );
    $two_days_ahead = date( 'Y-m-d', strtotime( '+2 days', current_time( 'timestamp' ) ) );

    $query_args = [
        'post_type'      => 'quads-ads',
        'posts_per_page' => -1,
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
            $query        = $wpdb->prepare(
                "SELECT user_id, ad_id FROM $table_name WHERE ad_id IN ($placeholders)",
                ...$ad_ids
            );

            // Execute the query and get results
            $users = $wpdb->get_results( $query );

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

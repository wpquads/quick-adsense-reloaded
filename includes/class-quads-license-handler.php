<?php
/**
 * License handler for WP QUADS
 *
 * This class should simplify the process of adding license information
 * to WP QUADSs.
 *
 * @version 1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'QUADS_License' ) ) :

/**
 * QUADS_License Class
 */
class QUADS_License {
	private $file;
	private $license;
	private $item_name;
	private $item_shortname;
	private $version;
	private $author;
	private $api_url = 'http://wpquads.com/edd-sl-api/'; // production
        private $api_url_debug = 'http://src.wordpress-develop.dev/edd-sl-api/'; // development
	/**
	 * Class constructor
	 *
	 * @global  array $quads_options
	 * @param string  $_file
	 * @param string  $_item_name
	 * @param string  $_version
	 * @param string  $_author
	 * @param string  $_optname
	 * @param string  $_api_url
	 */
	function __construct( $_file, $_item_name, $_version, $_author, $_optname = null, $_api_url = null ) {
		global $quads_options;

		$this->file           = $_file;
		$this->item_name      = $_item_name;
		$this->item_shortname = 'quads_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
		$this->version        = $_version;
		$this->license        = isset( $quads_options[ $this->item_shortname . '_license_key' ] ) ? trim( $quads_options[ $this->item_shortname . '_license_key' ] ) : '';
                $this->author         = $_author;
                if (QUADS_DEBUG){
                $this->api_url        = is_null( $_api_url ) ? $this->api_url_debug : $_api_url;
                }else{
                $this->api_url        = is_null( $_api_url ) ? $this->api_url : $_api_url;   
                }



		/**
		 * Allows for backwards compatibility with old license options,
		 * i.e. if the plugins had license key fields previously, the license
		 * handler will automatically pick these up and use those in lieu of the
		 * user having to reactive their license.
		 */
		if ( ! empty( $_optname ) ) {
			$opt = quads_get_option( $_optname, false );

			if( isset( $opt ) && empty( $this->license ) ) {
				$this->license = trim( $opt );
                    }
		}
                 
		// Setup hooks
		$this->includes();
		$this->hooks();

	}

	/**
	 * Include the updater class
	 *
	 * @access  private
	 * @return  void
	 */
	private function includes() {
		if ( ! class_exists( 'QUADS_SL_Plugin_Updater' ) ) {
                    require_once 'QUADS_SL_Plugin_Updater.php';    
                }
	}

	/**
	 * Setup hooks
	 *
	 * @access  private
	 * @return  void
	 */
	private function hooks() {

		// Register settings
		add_filter( 'quads_settings_licenses', array( $this, 'settings' ), 1 );

		// Display help text at the top of the Licenses tab
		add_action( 'quads_settings_tab_top', array( $this, 'license_help_text' ) );

		// Activate license key on settings save
		add_action( 'admin_init', array( $this, 'activate_license' ) );

		// Deactivate license key
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );

		// Check that license is valid once per week
		// add_action( 'quads_weekly_event', array( $this, 'weekly_license_check' ) );

		// For testing license notices, uncomment this line to force checks on every page load
		//add_action( 'admin_init', array( $this, 'weekly_license_check' ) );

		// Updater
		add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

		add_action( 'admin_init', array( $this, 'auto_checker' ), 0 );

		// Display notices to admins
		//add_action( 'admin_notices', array( $this, 'notices' ) );

		add_action( 'in_plugin_update_message-' . plugin_basename( $this->file ), array( $this, 'plugin_row_license_missing' ), 10, 2 );
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: This is a dependent function
		if( isset( $_POST['requestfrom'] ) && $_POST['requestfrom'] == 'wpquads2' ) {
			$this->activate_license2();
			$this->deactivate_license2();
			$this->auto_updater();

		}

	}

	/**
	 * Auto updater
	 *
	 * @access  private
	 * @return  void
	 */
	public function auto_updater() {
            
		$args = array(
			'version'   => $this->version,
			'license'   => $this->license,
			'author'    => $this->author
		);

  $license_data = get_option( $this->item_shortname . '_license_active' );

  			if (isset($license_data->expires)) {
        $license_exp = gmdate('Y-m-d', strtotime($license_data->expires));
        $license_exp_d = gmdate('d F Y', strtotime($license_data->expires));
      
        if (isset($license_data->expires)) {
        	$license_data->expires = $license_exp_d;
        }
        $license_info_lifetime = $license_data->expires;
		$today = gmdate('Y-m-d');
		$exp_date = $license_exp;
		$date1 = date_create($today);
			$date2 = date_create($exp_date);
			$diff = date_diff($date1,$date2);
			$days = $diff->format("%a");
			if( $license_info_lifetime == 'lifetime' ){
				$days = 'Lifetime';
				if ($days == 'Lifetime') {
				$expire_msg = " Your License is Valid for Lifetime ";
				}
			}
			elseif($today > $exp_date){
				$days = -$days;
			}
    }

    if ( isset( $days ) ) {
    if ( $days >= 0 && $days <= 30   ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function
    if( isset( $_GET["path"] ) && ! empty( $_GET ) ) {
    	// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function
    	if( $_GET['path'] == 'settings_licenses' ) {
    		// $this->weekly_license_check();
    	};
    }
  }
}

 
  if( ! empty( $this->item_id ) ) {
			$args['item_id']   = $this->item_id;
		} else {
			$args['item_name'] = $this->item_name;
		}
            
		// Setup the updater
		$quads_updater = new QUADS_SL_Plugin_Updater(
			$this->api_url,
			$this->file,
			$args
		);
	}

	public function auto_checker(){
		$details = get_option( 'quads_wp_quads_pro_license_active' );
		if (isset($details->expires)) {
		$license_exp = gmdate('Y-m-d', strtotime($details->expires));
		$license_info_lifetime = $details->expires;
		$today = gmdate('Y-m-d');
		$exp_date = $license_exp;
		$date1 = date_create($today);
			$date2 = date_create($exp_date);
			$diff = date_diff($date1,$date2);
			$days = $diff->format("%a");
			if( $license_info_lifetime == 'lifetime' ){
				$days = 'Lifetime';
				if ($days == 'Lifetime') {
				$expire_msg = " Your License is Valid for Lifetime ";
				}
			}
			elseif($today > $exp_date){
				$days = -$days;
			}
		}
	
		if ( $details ) {
		if ( isset( $days ) && $days <= 7 ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: There is no form submission and this is a dependent function 
			if( isset( $_GET["page"] ) && !empty( $_GET ) ) {
				$quads_mode = get_option('quads-mode');
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: There is no form submission and this is a dependent function
				if( $quads_mode !== "old" && $_GET['page'] == 'quads-settings' ){
					$trans_check = get_transient( 'quads_adsense_r_t' );
					if ( $days <= 7 && $trans_check !== 'quads_adsense_r_tvalue' ) {
						$this->weekly_license_check();
					}
					$transient =  'quads_adsense_r_t';
					$value =  'quads_adsense_r_tvalue';
					$expiration =  86400 ;
					set_transient( $transient, $value, $expiration );
				}
			}
		}
		}
	}


	/**
	 * Add license field to settings
	 *
	 * @access  public
	 * @param array   $settings
	 * @return  array
	 */
	public function settings( $settings ) {
		$quads_license_settings = array(
			array(
				'id'      => $this->item_shortname . '_license_key',
				'name'    => sprintf( /* translators: %s: Quick AdSense Reloaded */ __( '%1$s License Key', 'quick-adsense-reloaded' ), $this->item_name ),
				'desc'    => '',
				'type'    => 'license_key',
				'options' => array( 'is_valid_license_option' => $this->item_shortname . '_license_active' ),
				'size'    => 'regular'
			)
		);

		return array_merge( $settings, $quads_license_settings );
	}


	/**
	 * Display help text at the top of the Licenses tag
	 *
	 * @access  public
	 * @since   2.5
	 * @param   string   $active_tab
	 * @return  void
	 */
	public function license_help_text( $active_tab = '' ) {

		static $has_ran;

		if( 'licenses' !== $active_tab ) {
			return;
		}

		if( ! empty( $has_ran ) ) {
			return;
		}

		echo '<p>' . esc_html( sprintf( /* translators: %s: Quick AdSense Reloaded documentation URL */
			__( 'Enter your extension license keys here to receive updates for purchased extensions. If your license key has expired, please <a href="%s" target="_blank" title="License renewal FAQ">renew your license</a>.', 'quick-adsense-reloaded' ) ),
			'http://wpquads.com/renew-my-license/'
		) . '</p>';

		$has_ran = true;

	}

	/**
	 * Activate the license key
	 *
	 * @access  public
	 * @return  void
	 */
	public function activate_license2() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$post_setting  =json_decode($_POST['settings'], true);

               if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $post_setting['quads_wp_quads_pro_license_key'] ) ) {
		
			delete_option( $this->item_shortname . '_license_active' );
                
			return;

		}
                
		$details = get_option( $this->item_shortname . '_license_active' );

		if ( is_object( $details ) && 'valid' === $details->license ) {
			return;
		}

		$license = sanitize_text_field( $post_setting['quads_wp_quads_pro_license_key'] );

		if( empty( $license ) ) {
			return;
		}

		// Data to send to the API
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url()
		);

		// Call the API
		$response = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		// Make sure there are no errors
		if ( is_wp_error( $response ) ) {
			return;
		}

		// Tell WordPress to look for updates
		set_site_transient( 'update_plugins', null );

		// Decode license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( $this->item_shortname . '_license_active', $license_data );
      $response = array('status' => 't','license' => 'actived', 'msg' =>  __( 'Settings has been saved successfully', 'quick-adsense-reloaded' ), 'expiring_days' => $license_data->expires); 

      $license_exp = gmdate('Y-m-d', strtotime($details->expires));
			$license_exp_d = gmdate('d F Y', strtotime($details->expires));
			$license_info_lifetime = $details->expires;
		$today = gmdate('Y-m-d');
		$exp_date = $license_exp;
		$date1 = date_create($today);
			$date2 = date_create($exp_date);
			$diff = date_diff($date1,$date2);
			$days = $diff->format("%a");
			if( $license_info_lifetime == 'lifetime' ){
				$days = 'Lifetime';
				if ($days == 'Lifetime') {
				$expire_msg = " Your License is Valid for Lifetime ";
				}
			}
			elseif($today > $exp_date){
				$days = -$days;
			}
      return $response;
  }
	/**
	 * Activate the license key
	 *
	 * @access  public
	 * @return  void
	 */
	public function activate_license() {
		$details = get_option( $this->item_shortname . '_license_active' );
		if (isset($license_data->expires)) {
		$license_exp = gmdate('Y-m-d', strtotime($details->expires));
		$license_exp_d = gmdate('d F Y', strtotime($details->expires));
		$license_info_lifetime = $details->expires;
		$today = gmdate('Y-m-d');
		$exp_date = $license_exp;
		$date1 = date_create($today);
			$date2 = date_create($exp_date);
			$diff = date_diff($date1,$date2);
			$days = $diff->format("%a");
			if( $license_info_lifetime == 'lifetime' ){
				$days = 'Lifetime';
				if ($days == 'Lifetime') {
				$expire_msg = " Your License is Valid for Lifetime ";
				}
			}
			elseif($today > $exp_date){
				$days = -$days;
			}
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! isset( $_REQUEST[ $this->item_shortname . '_license_key-nonce'] ) || ! wp_verify_nonce( $_REQUEST[ $this->item_shortname . '_license_key-nonce'], $this->item_shortname . '_license_key-nonce' ) ) {

			return;

		}                
		
               if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		if ( empty( $_POST['quads_settings'][ $this->item_shortname . '_license_key'] ) ) {
		
			delete_option( $this->item_shortname . '_license_active' );
                
			return;

		}
		 	   
		foreach ( $_POST as $key => $value ) {
			if( false !== strpos( $key, 'license_key_deactivate' ) ) {
				// Don't activate a key when deactivating a different key
				return;
			}
		}
		
		$details = get_option( $this->item_shortname . '_license_active' );
		$refresh = isset($_POST[$this->item_shortname . '_license_key_refresh'])?true:false;

		if ( (is_object( $details ) && 'valid' === $details->license) && !$refresh) {
			return;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$license = sanitize_text_field( $_POST['quads_settings'][ $this->item_shortname . '_license_key'] );
		
		if( empty( $license ) ) {
			return;
		}
			$license_exp = gmdate('Y-m-d', strtotime($details->expires));
			$license_exp_d = gmdate('d F Y', strtotime($details->expires));
			$license_info_lifetime = $details->expires;
			$today = gmdate('Y-m-d');
			$exp_date = $license_exp;
			$date1 = date_create($today);
			$date2 = date_create($exp_date);
			$diff = date_diff($date1,$date2);
			$days = $diff->format("%a");
			if( $license_info_lifetime == 'lifetime' ){
				$days = 'Lifetime';
				if ($days == 'Lifetime') {
				$expire_msg = " Your License is Valid for Lifetime ";
				}
			}
			elseif($today > $exp_date){
				$days = -$days;
			}
		// Data to send to the API
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url(),
			'key_duration'        => $license_exp_d
		);

		// Call the API
		$response = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		// Make sure there are no errors
		if ( is_wp_error( $response ) ) {
			return;
		}

		// Tell WordPress to look for updates
		set_site_transient( 'update_plugins', null );

		// Decode license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( $this->item_shortname . '_license_active', $license_data );

                }


	/**
	 * Deactivate the license key
	 *
	 * @access  public
	 * @return  void
	 */
	public function deactivate_license() {

		if ( ! isset( $_POST['quads_settings'] ) )
			return;

		if ( ! isset( $_POST['quads_settings'][ $this->item_shortname . '_license_key'] ) )
			return;
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		if( ! wp_verify_nonce( $_REQUEST[ $this->item_shortname . '_license_key-nonce'], $this->item_shortname . '_license_key-nonce' ) ) {
		
			wp_die( esc_html__( 'Nonce verification failed', 'quick-adsense-reloaded' ), esc_html__( 'Error', 'quick-adsense-reloaded' ), array( 'response' => 403 ) );
                
		}
                
                if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Run on deactivate button press
		if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate'] ) ) {

			// Data to send to the API
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $this->license,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url()
			);

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params
				)
			);

			// Make sure there are no errors
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			delete_option( $this->item_shortname . '_license_active' );

		}
	}

		/**
	 * Deactivate the license key
	 *
	 * @access  public
	 * @return  void
	 */
	public function deactivate_license2() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$post_setting  =json_decode($_POST['settings'], true);

               if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $post_setting['quads_wp_quads_pro_license_key'] ) ) {
			return;
		}

		// Run on deactivate button press
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: There is no form submission and this is a dependent function
		if ( isset( $_POST['quads_wp_quads_pro_license_key_deactivate'] ) ) {
			// Data to send to the API
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $this->license,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url()
			);

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params
				)
			);

			// Make sure there are no errors
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			delete_option( $this->item_shortname . '_license_active' );

		}
	}
        
        
        /**
	 * Check if license key is valid once per week
	 *
	 * @access  public
	 * @since   2.5
	 * @return  void
	 */
	public function weekly_license_check() {

		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: There is no form submission and this is a dependent function
		if( ! empty( $_POST['quads_settings'] ) ) {
			return; // Don't fire when saving settings
		}

		if( empty( $this->license ) ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'check_license',
			'license' 	=> $this->license,
			'item_name' => urlencode( $this->item_name ),
			'url'       => home_url()
		);

		// Call the API
		$response = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		if ($license_data->license == "expired") {
  			if (isset($license_data->expires)) {
        $license_exp = gmdate('Y-m-d', strtotime($license_data->expires));
        $license_exp_d = gmdate('d F Y', strtotime($license_data->expires));
      
        if (isset($license_data->expires)) {
        	$license_data->expires = $license_exp_d;
        }
        $license_info_lifetime = $license_data->expires;
		$today = gmdate('Y-m-d');
		$exp_date = $license_exp;
		$date1 = date_create($today);
			$date2 = date_create($exp_date);
			$diff = date_diff($date1,$date2);
			$days = $diff->format("%a");
			if( $license_info_lifetime == 'lifetime' ){
				$days = 'Lifetime';
				if ($days == 'Lifetime') {
				$expire_msg = " Your License is Valid for Lifetime ";
				}
			}
			elseif($today > $exp_date){
				$days = -$days;
			}
    }
    update_option( $this->item_shortname . '_license_active', $license_data );
}
		if ($license_data->license !== "expired") {
		update_option( $this->item_shortname . '_license_active', $license_data );
		}

		if ($response['response']['code'] == 200 && $response['response']['message'] == 'OK') {
			if ( QUADS_VERSION == '2.0.33' && function_exists('quads_is_pro_active') && quads_is_pro_active() ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: There is no form submission and this is a dependent function
				if( isset( $_GET["page"] ) && !empty( $_GET ) && $_GET["page"] == 'quads-settings' && isset($_GET["tab"]) && $_GET["tab"] == 'licenses' ){
			$transient_load =  'quads_adsense_license_auto_check';
            $value_load =  'quads_adsense_license_auto_check_value';
            $expiration_load =  0 ;
            set_transient( $transient_load, $value_load, $expiration_load );
        }
    }
}

	}
       /**
	 * Displays message inline on plugin row that the license key is missing
	 *
	 * @access  public
	 * @since   2.5
	 * @return  void
	 */
	public function plugin_row_license_missing( $plugin_data, $version_info ) {

		static $showed_imissing_key_message;

		$license = get_option( $this->item_shortname . '_license_active' );

		if( ( ! is_object( $license ) || 'valid' !== $license->license ) && empty( $showed_imissing_key_message[ $this->item_shortname ] ) ) {

			echo '&nbsp;<strong><a href="' . esc_url( admin_url( 'admin.php?page=quads-settings&tab=licenses' ) ) . '">' . esc_html__( 'Enter valid license key for automatic updates.', 'quick-adsense-reloaded' ) . '</a></strong>';
			$showed_imissing_key_message[ $this->item_shortname ] = true;
		}

	} 
}

endif; // end class_exists check
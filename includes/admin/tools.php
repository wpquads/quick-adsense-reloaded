<?php
/**
 * Tools
 *
 * These are functions used for displaying QUADS tools such as the import/export system.
 *
 * @package     QUADS
 * @subpackage  Admin/Tools
 * @copyright   Copyright (c) 2015, Pippin Williamson, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Tools
 *
 * Shows the tools panel which contains QUADS-specific tools including the
 * built-in import/export system.
 *
 * @since       0.9.0
 * @author      Daniel J Griffiths, René Hermenau
 * @return      void
 */
function quads_tools_page() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended --Reason: This is a dependent function being called to load the tools page where all security measurament is done.
	$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ): 'import_export';
?>
	<div class="wrap">
		<h2 class="quads-nav-tab-wrapper">
			<?php
			foreach( quads_get_tools_tabs() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'tab' => $tab_id
				) );

				$tab_url = remove_query_arg( array(
					'quads-message'
				), $tab_url );

				$active = $active_tab == $tab_id ? ' quads-nav-tab-active' : '';
				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="quads-nav-tab' . esc_attr( $active ) . '">' . esc_html( $tab_name ) . '</a>';

			}
			?>
		</h2>
		<div class="metabox-holder">
			<?php
			do_action( 'quads_tools_tab_' . $active_tab );
			?>
		</div><!-- .metabox-holder -->
	</div><!-- .wrap -->
<?php
}


/**
 * Retrieve tools tabs
 *
 * @since       2.1.6
 * @return      array
 */
function quads_get_tools_tabs() {

	$tabs                  = array();
	$tabs['import_export'] = __( 'Import/Export', 'quick-adsense-reloaded' );
       $tabs['system_info'] = __( 'System Info', 'quick-adsense-reloaded' );

	return apply_filters( 'quads_tools_tabs', $tabs );
}



/**
 * Display the tools import/export tab
 *
 * @since       2.1.6
 * @return      void
 */
function quads_tools_import_export_display() {
    
        if( ! current_user_can( 'update_plugins' ) ) {
		return;
	}
    
	do_action( 'quads_tools_import_export_before' );
?>
        <!-- We have to close the old form first//-->

	<div class="quads-postbox">
		<h3><span><?php esc_html_e( 'Export Settings', 'quick-adsense-reloaded' ); ?></span></h3>
		<div class="inside">
			<p><?php esc_html_e( 'Export the Quick AdSense Reloaded settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'quick-adsense-reloaded' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=quads-settings&tab=imexport' ) ); ?>" id="quads-export-settings">
				<p><input type="hidden" name="quads-action" value="export_settings" /></p>
				<p>
					<?php wp_nonce_field( 'quads_export_nonce', 'quads_export_nonce' ); ?>
					<?php submit_button( esc_html__( 'Export', 'quick-adsense-reloaded' ), 'primary', 'submit', false ); ?>
				</p>
			</form>
		</div><!-- .inside -->
	</div><!-- .postbox -->

	<div class="quads-postbox">
		<h3><span><?php esc_html_e( 'Import Settings', 'quick-adsense-reloaded' ); ?></span></h3>
		<div class="inside">
			<p><?php esc_html_e( 'Import the Quick AdSense Reloaded settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'quick-adsense-reloaded' ); ?></p>
			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin.php?page=quads-settings&tab=imexport' ) ); ?>">
				<p>
					<input type="file" name="import_file"/>
				</p>
				<p>
					<input type="hidden" name="quads-action" value="import_settings" />
					<?php wp_nonce_field( 'quads_import_nonce', 'quads_import_nonce' ); ?>
					<?php submit_button( esc_html__( 'Import', 'quick-adsense-reloaded' ), 'secondary', 'submit', false ); ?>
				</p>
			</form>
		</div><!-- .inside -->
	</div><!-- .postbox -->
<?php
	do_action( 'quads_tools_import_export_after' );
}
add_action( 'quads_tools_tab_import_export', 'quads_tools_import_export_display' );



/* check if function is disabled or not
 * 
 * @returns bool
 * @since 2.1.6
 */
function quads_is_func_disabled( $function ) {
  $disabled = explode( ',',  ini_get( 'disable_functions' ) );
  return in_array( $function, $disabled );
}

/**
 * Process a settings export that generates a .json file of the Quick AdSense Reloaded settings
 *
 * @since       2.1.6
 * @return      void
 */
function quads_tools_import_export_process_export() {
	if( empty( $_POST['quads_export_nonce'] ) )
		return;

	if( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['quads_export_nonce'] ) ), 'quads_export_nonce' ) )
		return;

	if( ! current_user_can( 'manage_options' ) )
		return;

	$settings = array();
	$settings = get_option( 'quads_settings' );

	ignore_user_abort( true );

	if ( ! quads_is_func_disabled( 'set_time_limit' ) ){
		/* phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged */
		@set_time_limit( 0 );
	}

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=' . apply_filters( 'quads_settings_export_filename', 'quads-settings-export-' . gmdate( 'm-d-Y' ) ) . '.json' );
	header( "Expires: 0" );

	echo json_encode( $settings );
	exit;
}
add_action( 'quads_export_settings', 'quads_tools_import_export_process_export' );

/**
 * Get File Extension
 *
 * Returns the file extension of a filename.
 *
 * @since 1.0
 * @param unknown $str File name
 * @return mixed File extension
 */
 function quads_get_file_extension( $str ) {
     $parts = explode( '.', $str );
     return end( $parts );
}

/* Convert an object to an associative array.
 * Can handle multidimensional arrays
 * 
 * @returns array
 * @since 2.1.6
 */
function quads_object_to_array( $data ) {
  if ( is_array( $data ) || is_object( $data ) ) {
    $result = array();
    foreach ( $data as $key => $value ) {
      $result[ $key ] = quads_object_to_array( $value );
    }
    return $result;
  }
  return $data;
}

/**
 * Process a settings import from a json file
 *
 * @since 2.1.6
 * @return void
 */
function quads_tools_import_export_process_import() {
	if( empty( $_POST['quads_import_nonce'] ) )
		return;
	/* phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash */
	if( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['quads_import_nonce'] ) ), 'quads_import_nonce' ) )
		return;

	if( ! current_user_can( 'update_plugins' ) )
		return;

    if( isset($_FILES['import_file']['name'] ) && quads_get_file_extension( sanitize_text_field( $_FILES['import_file']['name'] ) ) != 'json' ) {
        wp_die( esc_html__( 'Please upload a valid .json file', 'quick-adsense-reloaded' ) );
    }

	$import_file = ( isset( $_FILES['import_file']['tmp_name'] ) )? sanitize_text_field( $_FILES['import_file']['tmp_name'] ) : '';

	if( empty( $import_file ) ) {
		wp_die( esc_html__( 'Please upload a file to import', 'quick-adsense-reloaded' ) );
	}

	// Retrieve the settings from the file and convert the json object to an array
	$settings = quads_object_to_array( json_decode( quads_local_file_get_contents( $import_file ) ) );

	update_option( 'quads_settings', $settings );

	wp_safe_redirect( admin_url( 'admin.php?page=quads-settings&quads-message=settings-imported&tab=imexport' ) ); exit;

}
add_action( 'quads_import_settings', 'quads_tools_import_export_process_import' );


/**
 * Display the system info tab
 *
 * @since       2.1.6
 * @return      void
 * @change      2.3.1
 */
function quads_tools_sysinfo_display() {
    
    if( ! current_user_can( 'update_plugins' ) ) {
		return;
	}
        
?>
	<!--<form action="<?php //echo esc_url( admin_url( 'admin.php?page=quads-settings&tab=system_info' ) ); ?>" method="post" dir="ltr">//-->
		<textarea readonly="readonly" onclick="this.focus(); this.select()" id="system-info-textarea" name="quads-sysinfo" title="To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac)."><?php /* phpcs:ignore 	WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_tools_sysinfo_get() function */ echo quads_tools_sysinfo_get(); ?></textarea>
		<!--
                <p class="submit">
			<input type="hidden" name="quads-action" value="download_sysinfo" />-->
			<?php //submit_button( 'Download System Info File', 'primary', 'quads-download-sysinfo', false ); ?>
		<!--</p>//-->
	<!--</form>//-->
<?php
    // phpcs:ignore 	WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_tools_sysinfo_get() function
    echo '<br>' . quads_render_backup_settings(); 

}
add_action( 'quads_tools_tab_system_info', 'quads_tools_sysinfo_display' );

/**
 * Render textarea with backup settings from previous version 1.5.2
 * @return string
 */
function quads_render_backup_settings(){
       if( ! current_user_can( 'update_plugins' ) ) {
		return;
	}
       
       $settings = json_encode(get_option('quads_settings_1_5_2'));
       echo '<h3>' . esc_html__('Backup data from WP QUADS 1.5.2', 'quick-adsense-reloaded') .  '</h3>' . esc_html__('Copy and paste this data into an empty text file with extension *.json', 'quick-adsense-reloaded');       
       ?>

       <textarea readonly="readonly" onclick="this.focus(); this.select()" id="backup-settings-textarea" name="quads-backupsettings" title="To copy the backup settings info, click below then press Ctrl + C (PC) or Cmd + C (Mac)."><?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ echo $settings; ?></textarea>
<?php
}


/**
 * Get system info
 *
 * @since       2.1.6
 * @access      public
 * @global      object $wpdb Used to query the database using the WordPress Database API
 * @global      array $quads_options Array of all QUADS options
 * @return      string $return A string containing the info to output
 */
function quads_tools_sysinfo_get() {
	global $wpdb, $quads_options;

	if( !class_exists( 'Browser' ) )
		require_once QUADS_PLUGIN_DIR . 'includes/libraries/browser.php';

	$browser = new Browser();

	// Get theme info
	if( get_bloginfo( 'version' ) < '3.4' ) {
		// phpcs:ignore WordPress.WP.DeprecatedFunctions.get_theme_dataFound
		$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
		$theme      = $theme_data['Name'] . ' ' . $theme_data['Version'];
	} else {
		$theme_data = wp_get_theme();
		$theme      = $theme_data->Name . ' ' . $theme_data->Version;
	}


	$return  = '### Begin System Info ###' . "\n\n";

	// Start with the basics...
	$return .= '-- Site Info' . "\n\n";
	$return .= 'Site URL:                 ' . site_url() . "\n";
	$return .= 'Home URL:                 ' . home_url() . "\n";
	$return .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";

	$return  = apply_filters( 'quads_sysinfo_after_site_info', $return );


	// The local users' browser information, handled by the Browser class
	$return .= "\n" . '-- User Browser' . "\n\n";
	$return .= $browser;

	$return  = apply_filters( 'quads_sysinfo_after_user_browser', $return );

	// WordPress configuration
	$return .= "\n" . '-- WordPress Configuration' . "\n\n";
	$return .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
	$return .= 'Language:                 ' . ( defined( 'WPLANG' ) && WPLANG ? WPLANG : 'en_US' ) . "\n";
	$return .= 'Permalink Structure:      ' . ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' ) . "\n";
	$return .= 'Active Theme:             ' . $theme . "\n";
	$return .= 'Show On Front:            ' . get_option( 'show_on_front' ) . "\n";

	// Only show page specs if frontpage is set to 'page'
	if( get_option( 'show_on_front' ) == 'page' ) {
		$front_page_id = get_option( 'page_on_front' );
		$blog_page_id = get_option( 'page_for_posts' );

		$return .= 'Page On Front:            ' . ( $front_page_id != 0 ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset' ) . "\n";
		$return .= 'Page For Posts:           ' . ( $blog_page_id != 0 ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset' ) . "\n";
	}

	// Make sure wp_remote_post() is working
	$request['cmd'] = '_notify-validate';

	$params = array(
		'sslverify'     => false,
		'timeout'       => 60,
		'user-agent'    => 'QUADS/' . QUADS_VERSION,
		'body'          => $request
	);

	$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

	if( !is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
		$WP_REMOTE_POST = 'wp_remote_post() works';
	} else {
		$WP_REMOTE_POST = 'wp_remote_post() does not work';
	}

	$return .= 'Remote Post:              ' . $WP_REMOTE_POST . "\n";
	$return .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . '   Status: ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' ) . "\n";
	$return .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
	$return .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";
	$return .= 'Registered Post Stati:    ' . implode( ', ', get_post_stati() ) . "\n";

	$return  = apply_filters( 'quads_sysinfo_after_wordpress_config', $return );

	// QUADS configuration
	$return .= "\n" . '-- QUADS Configuration' . "\n\n";
	$return .= 'Version:                  ' . QUADS_VERSION . "\n";
	$return .= 'Upgraded From:            ' . get_option( 'quads_version_upgraded_from', 'None' ) . "\n";

	$return  = apply_filters( 'quads_sysinfo_after_quads_config', $return );


	// WordPress active plugins
	$return .= "\n" . '-- WordPress Active Plugins' . "\n\n";
	 if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
	$plugins = get_plugins();
	$active_plugins = get_option( 'active_plugins', array() );

	foreach( $plugins as $plugin_path => $plugin ) {
		if( !in_array( $plugin_path, $active_plugins ) )
			continue;

		$return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
	}

	$return  = apply_filters( 'quads_sysinfo_after_wordpress_plugins', $return );

	// WordPress inactive plugins
	$return .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";

	foreach( $plugins as $plugin_path => $plugin ) {
		if( in_array( $plugin_path, $active_plugins ) )
			continue;

		$return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
	}

	$return  = apply_filters( 'quads_sysinfo_after_wordpress_plugins_inactive', $return );

	if( is_multisite() ) {
		// WordPress Multisite active plugins
		$return .= "\n" . '-- Network Active Plugins' . "\n\n";

		$plugins = wp_get_active_network_plugins();
		$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

		foreach( $plugins as $plugin_path ) {
			$plugin_base = plugin_basename( $plugin_path );

			if( !array_key_exists( $plugin_base, $active_plugins ) )
				continue;

			$plugin  = get_plugin_data( $plugin_path );
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
		}

		$return  = apply_filters( 'quads_sysinfo_after_wordpress_ms_plugins', $return );
	}

	// Server configuration (really just versioning)
	$return .= "\n" . '-- Webserver Configuration' . "\n\n";
	$return .= 'PHP Version:              ' . PHP_VERSION . "\n";
	$return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
	if( isset( $_SERVER['SERVER_SOFTWARE'] )){
		$return .= 'Webserver Info:           ' . sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) . "\n";
	}

	$return  = apply_filters( 'quads_sysinfo_after_webserver_config', $return );

	// PHP configs... now we're getting to the important stuff
	$return .= "\n" . '-- PHP Configuration' . "\n\n";
	$return .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
	$return .= 'Upload Max Size:          ' . ini_get( 'upload_max_filesize' ) . "\n";
	$return .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
	$return .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
	$return .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
	$return .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
	$return .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

	$return  = apply_filters( 'quads_sysinfo_after_php_config', $return );

	// PHP extensions and such
	$return .= "\n" . '-- PHP Extensions' . "\n\n";
	$return .= 'cURL:                     ' . ( function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported' ) . "\n";
	$return .= 'fsockopen:                ' . ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ) . "\n";
	$return .= 'SOAP Client:              ' . ( class_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed' ) . "\n";
	$return .= 'Suhosin:                  ' . ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ) . "\n";

	$return  = apply_filters( 'quads_sysinfo_after_php_ext', $return );

	$return .= "\n" . '### End System Info ###';

	return $return;
}


/**
 * Generates a System Info download file
 *
 * @since       2.0
 * @return      void
 */
function quads_tools_sysinfo_download() {
    
    if( ! current_user_can( 'update_plugins' ) ) return;
    // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: This is a dependent function
    if( ! isset( $_POST['quads-sysinfo'] ) ) return;
    
	nocache_headers();

	header( 'Content-Type: text/plain' );
	header( 'Content-Disposition: attachment; filename="quads-system-info.txt"' );

	// phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: This is a dependent function
	echo esc_html( wp_strip_all_tags( wp_unslash( $_POST['quads-sysinfo'] ) ) );
	wp_die();
}
add_action( 'quads_download_sysinfo', 'quads_tools_sysinfo_download' );

/*
 * Import settings from Quick AdSense reloaded  v. 1.9.2
 */

function quads_import_quick_adsense_settings(){
    // Check first if Quick AdSense is installed and version matches
    if (!quads_check_quick_adsense_version())
        return;
   
    
        if( ! current_user_can( 'update_plugins' ) ) {
		return;
	}
    
	do_action( 'quads_import_quick_adsense_settings_before' );
?>
	<div class="quads-postbox" id="quads-import-settings">
		<h3><span><?php esc_html_e( 'Import from Quick AdSense', 'quick-adsense-reloaded' ); ?></span></h3>
		<div class="inside">
			<p><?php esc_html_e( 'Import the settings for Quick AdSense Reloaded from Quick AdSense v. 1.9.2.', 'quick-adsense-reloaded' ); ?></p>
			
			<!--
                        <form id="quads_quick_adsense_input" method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=quads-settings&tab=imexport' ) ); ?>" onsubmit="return confirm('Importing the settings from Quick AdSense will overwrite all your current settings. Are you sure?');">
                        -->
				<p><input type="hidden" name="quads-action" value="import_quick_adsense" /></p>
				<p>
					<?php wp_nonce_field( 'quads_quick_adsense_nonce', 'quads_quick_adsense_nonce' ); ?>
					<?php submit_button( __( 'Start Import process', 'quick-adsense-reloaded' ), 'primary quads-import-settings', 'submit', false ); ?>
				</p>
			<!--</form>-->
                        <div id="quads-error-details"></div>
		</div><!-- .inside -->
	</div><!-- .postbox -->
<?php
	do_action( 'quads_import_quick_adsense_settings_after' );
}
add_action( 'quads_import_quick_adsense_settings', 'quads_import_quick_adsense_settings' );

/**
 * Ajax process a settings import from Quick AdSense
 *
 * @since       0.9.0
 * @return      string json
 */
function quads_import_quick_adsense_process() {

        check_ajax_referer( 'quads_ajax_nonce', 'nonce' );

	if( ! current_user_can( 'manage_options' ) )
		return;

	ignore_user_abort( true );

	if ( ! quads_is_func_disabled( 'set_time_limit' ) ){
		// phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
		set_time_limit( 0 );
	}
        
        
        $quads_settings          = get_quick_adsense_setting();
        $quads_reloaded_settings = get_option('quads_settings');
        

        if (update_option('quads_settings', $quads_settings ) ){
            $message = __('Most of the settings have been sucessfully imported from Quick AdSense <br> but due to some inconsistencies there are still some options which needs your attention and manual adjusting.','quick-adsense-reloaded');
            wp_send_json ( $message );
        }

        $message = __('Most of settings have been already imported successfully! (If not we probably have an unknown issue here)', 'quick-adsense-reloaded');
        //$message = get_quick_adsense_setting();
        wp_send_json ( $message );

}
//add_action( 'quads_import_quick_adsense', 'quads_import_quick_adsense_process' );
add_action('wp_ajax_quads_import_quick_adsense', 'quads_import_quick_adsense_process');

/**
 * Clear all the cache to apply latest changes
 * 
 * @return boolean true when it is installed and version matches
 */
function quads_clear_cache(){

	check_ajax_referer( 'quads_ajax_nonce', 'nonce' );

	if( ! current_user_can( 'manage_options' ) ) { return false; }

	if ( function_exists( 'rocket_clean_domain' ) ) {
		rocket_clean_domain();
	}
	if ( defined( 'WPCACHEHOME' ) ) {
		global  $file_prefix;
		wp_cache_clean_cache( $file_prefix, true );
	}

}
add_action('wp_ajax_quads_clear_cache', 'quads_clear_cache');


/**
 * Check if Quick AdSense is installed and if version is 1.9.2
 * 
 * @return boolean true when it is installed and version matches
 */
function quads_check_quick_adsense_version(){
    $plugin_file = 'quick-adsense/quick-adsense.php';
    $plugin_abs_path = get_home_path() . '/wp-content/plugins/quick-adsense/quick-adsense.php';
    $checkVersion = '1.9.2';
    
    if ( is_plugin_active( $plugin_file ) ) {
        $plugin_data = get_plugin_data( $plugin_abs_path, $markup = true, $translate = true );
        
        if ($plugin_data['Version'] === $checkVersion)
            return true;   
    }
    
    if ( file_exists( $plugin_abs_path ) && is_plugin_inactive( $plugin_file ) ) {
        $plugin_data = get_plugin_data( $plugin_abs_path, $markup = true, $translate = true );
        
        if ($plugin_data['Version'] === $checkVersion)
            return true;   
    }
   
}

/**
 * Get all Quick AdSense settings and convert them to a Quick AdSense reloaded compatible array
 * 
 * @since 0.9.0
 * @return array
 */
function get_quick_adsense_setting() {
    $amountAds = 10;
    $amountWidgets = 10;
    $settings = array();
    $new_align = '';


    for ( $i = 1; $i <= $amountAds; $i++ ) {
        if( get_option( 'AdsCode' . $i ) != '' ) {
            $settings['ad' . $i]['code'] = get_option( 'AdsCode' . $i );
            $settings['ad' . $i]['margin'] = get_option( 'AdsMargin' . $i );
            //$settings['ad' . $i]['align'] = get_option( 'AdsAlign' . $i );
            // convert the old margin values into the new ones
            $old_align = get_option( 'AdsAlign' . $i );
            if (isset($old_align) && $old_align === '1'){ // right
                $new_align = '0';
            } else if(isset($old_align) && $old_align === '2'){ // center
                $new_align = '1';
            } else if(isset($old_align) &&$old_align === '3'){ // right
                $new_align = '2';
            } else if(isset($old_align) &&$old_align === '4'){ // none
                $new_align = '3';
            }
            $settings['ad' . $i]['align'] = $new_align;
        }
    }
    for ( $i = 1; $i <= $amountWidgets; $i++ ) {
        if( get_option( 'WidCode' . $i ) != '' ) {
            $settings['ad' . $i . '_widget'] = get_option( 'WidCode' . $i );
        }
    }
    $settings['maxads'] = get_option( 'AdsDisp' );
    $settings['pos1']['BegnAds'] = get_option( 'BegnAds' );
    $settings['pos1']['BegnRnd'] = get_option( 'BegnRnd' );
    $settings['pos2']['MiddAds'] = get_option( 'MiddAds' );
    $settings['pos2']['MiddRnd'] = get_option( 'MiddRnd' );
    $settings['pos3']['EndiAds'] = get_option( 'EndiAds' );
    $settings['pos3']['EndiRnd'] = get_option( 'EndiRnd' );
    $settings['pos4']['MoreAds'] = get_option( 'MoreAds' );
    $settings['pos4']['MoreRnd'] = get_option( 'MoreRnd' );
    $settings['pos5']['LapaAds'] = get_option( 'LapaAds' );
    $settings['pos5']['LapaRnd'] = get_option( 'LapaRnd' );
    $rc = 3;
    $value = 5;
    for ( $j = 1; $j <= $rc; $j++ ) {
        $key = $value + $j;
        $settings['pos' . $key]['Par' . $j . 'Ads'] = get_option( 'Par' . $j . 'Ads' );
        $settings['pos' . $key]['Par' . $j . 'Rnd'] = get_option( 'Par' . $j . 'Rnd' );
        $settings['pos' . $key]['Par' . $j . 'Nup'] = get_option( 'Par' . $j . 'Nup' );
        $settings['pos' . $key]['Par' . $j . 'Con'] = get_option( 'Par' . $j . 'Con' );
    }
    $settings['pos9']['Img1Ads'] = get_option( 'Img1Ads' );
    $settings['pos9']['Img1Rnd'] = get_option( 'Img1Rnd' );
    $settings['pos9']['Img1Nup'] = get_option( 'Img1Nup' );
    $settings['pos9']['Img1Con'] = get_option( 'Img1Con' );
    //$settings['visibility']['AppPost'] = get_option( 'AppPost' );
    //$settings['visibility']['AppPage'] = get_option( 'AppPage' );
    $settings['visibility']['AppHome'] = get_option( 'AppHome' );
    $settings['visibility']['AppCate'] = get_option( 'AppCate' );
    $settings['visibility']['AppArch'] = get_option( 'AppArch' );
    $settings['visibility']['AppTags'] = get_option( 'AppTags' );
    $settings['visibility']['AppMaxA'] = get_option( 'AppMaxA' );
    $settings['visibility']['AppSide'] = get_option( 'AppSide' );
    $settings['visibility']['AppLogg'] = get_option( 'AppLogg' );
    $settings['quicktags']['QckTags'] = get_option( 'QckTags' );
    $settings['quicktags']['QckRnds'] = get_option( 'QckRnds' );
    $settings['quicktags']['QckOffs'] = get_option( 'QckOffs' );
    $settings['quicktags']['QckOfPs'] = get_option( 'QckOfPs' );
    
    // Get previous settings for AppPost and AppPage
    $post_setting_old = (false !== get_option( 'AppPost' )) ? true : false;
    $page_setting_old = (false !== get_option( 'AppPage' )) ? true : false;
    // Store them in new array post_types
    if (true === $post_setting_old && true === $page_setting_old) {
        $settings['post_types'] = array('post', 'page');
    } else if (true === $post_setting_old && false === $page_setting_old) {
        $settings['post_types'] = array('post');
    } else if (false === $post_setting_old && true === $page_setting_old) {
        $settings['post_types'] = array('page');
    }

    $settings1 = quads_str_replace_json( "true", "1", $settings );
    return $settings1;
}

/**
 * A faster way to replace the strings in multidimensional array is to json_encode() it, 
 * do the str_replace() and then json_decode() it
 * 
 * @param string $search
 * @param string $replace
 * @param array $subject
 * @return array
 */
function quads_str_replace_json($search, $replace, $subject){
     $stdClass = json_decode(str_replace($search, $replace, json_encode($subject)));
     
     return quads_object_to_array($stdClass);
}
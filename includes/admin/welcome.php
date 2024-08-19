<?php
/**
 * Welcome Page Class
 *
 * @package     QUADS
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * quads_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.4
 */
class quads_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'welcome'    ) );
		add_filter( 'mce_buttons', array( $this, 'quads_register_buttons' ) );
		add_filter( 'tiny_mce_plugins', array( $this, 'tiny_mce_plugins' ) );
		add_filter( 'wp_tiny_mce_init', array( $this, 'print_shortcode_plugin' ) );
		add_action( 'print_default_editor_scripts', array( $this, 'print_shortcode_plugin' ) );


	}

	
	private function hooks_exist() {
		global $quads_options;
		if (
			( isset( $quads_options['quicktags']['QckTags'] ) && $quads_options['quicktags']['QckTags'] ) && 
			(
				has_action( 'wp_tiny_mce_init', array( $this, 'print_shortcode_plugin' ) )
				|| add_action( 'print_default_editor_scripts', array( $this, 'print_shortcode_plugin' ) )
			)
			&& has_filter( 'mce_buttons', array( $this, 'quads_register_buttons' ) )
			&& has_filter( 'tiny_mce_plugins', array( $this, 'tiny_mce_plugins' ) ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Sends user to the Settings page on first activation of QUADS as well as each
	 * time QUADS is upgraded to a new version
	 *
	 * @access public
	 * @since 1.0.1
	 * @return void
	 */
	public function welcome() {
		// Bail if no activation redirect
		if ( false === get_transient( 'quads_activation_redirect' ) ){
			return;
                }

		// Delete the redirect transient
		delete_transient( 'quads_activation_redirect' );

		// Bail if activating from network, or bulk
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ){
			return;
                }

		$upgrade = get_option( 'quads_version_upgraded_from' );
		wp_safe_redirect( admin_url( 'admin.php?page=quads-settings' ) ); exit;

	}
	
	public function tiny_mce_plugins( $plugins ) {
		if ( ! $this->hooks_exist() ) {
			return $plugins;
		}

		$plugins[] = 'quads_shortcode';
		return $plugins;
	}
	

	/**
	 * Add the plugin to array of external TinyMCE plugins
	 *
	 * @param array $plugin_array array with TinyMCE plugins.
	 *
	 * @return array
	 */
	public function print_shortcode_plugin() {
		static $printed = null;
	
		if ($printed !== null) {
			return;
		}
	
		$printed = true;
	
		if (!$this->hooks_exist()) {
			return;
		}
	
		// Include the WordPress Filesystem API and initialize it.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	
		// Get the global $wp_filesystem object.
		global $wp_filesystem;
	
		// Define the file path.
		$file_path = QUADS_PLUGIN_DIR . 'assets/js/tinymce_shortcode.js';
	
		// Get the file contents using the Filesystem API.
		$script_content = $wp_filesystem->get_contents($file_path);
	
		if ($script_content !== false) {
			echo "<script>\n"
				. $script_content . "\n" // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: The content is a static script file.
				. "</script>\n";
		} else {
			echo esc_html__("<!-- Error: Unable to read the script file. -->\n",'quick-adsense-reloaded');
		}
	}
	


	/**
	 * Add button to tinyMCE window.
	 *
	 * @param array $buttons array with existing buttons.
	 *
	 * @return array
	 */
	public function quads_register_buttons( $buttons ) {
		if ( ! is_array( $buttons ) ) {
			$buttons = array();
		}
		$buttons[] = 'quads_shortcode_button';
		return $buttons;
	}
}
new quads_Welcome();
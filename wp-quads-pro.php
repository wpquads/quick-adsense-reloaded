<?php

/**
 * Plugin Name: AdSense Integration WP QUADS PRO
 * Plugin URI: https://wordpress.org/plugins/quick-adsense-reloaded/
 * Description: Insert Google AdSense or any Ads code into your website. A fork of Quick AdSense
 * Author: Rene Hermenau
 * Author URI: https://wordpress.org/plugins/quick-adsense-reloaded/
 * Version: {{ version }}
 * Text Domain: quick-adsense-reloaded
 * Domain Path: languages
 *
 *
 * @package QUADS
 * @category Core
 * @author René Hermenau
 * @version 0.9.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

// Plugin version
if( !defined( 'QUADS_VERSION' ) ) {
    define( 'QUADS_VERSION', '{{ version }}' );
}

// Plugin name
if( !defined( 'QUADS_NAME' ) ) {
    define( 'QUADS_NAME', 'WP QUADS PRO' );
}

// Files that needs to be loaded early
if( !class_exists( 'QUADS_Utils' ) ) {
    require dirname( __FILE__ ) . '/includes/quads-utils.php';
}


// Debug
if( !defined( 'QUADS_DEBUG' ) ) {
    define( 'QUADS_DEBUG', false );
}

// Define some globals
$ShownAds = 0; // Amount of ads which are shown
$ad_count_shortcode = 0; // Number of active ads which are shown via shortcodes
$ad_count_content = 0; // Number of active ads which are shown in the_content
$ad_count_custom = 0; // Number of active custom ads which are shown on the site
$ad_count_widget = 0; // Number of active ads in widgets
$AdsId = array(); // Array of active ad id's
$adWidgets = 10; // number of widgets
$numberAds = 10; // number of regular ads
$AdsWidName = 'AdsWidget%d (WP QUADS)';


if( !class_exists( 'QuickAdsenseReloaded' ) ) :

    /**
     * Main QuickAdsenseReloaded Class
     *
     * @since 1.0.0
     */
    final class QuickAdsenseReloaded {
        /** Singleton ************************************************************ */

        /**
         * @var QuickAdsenseReloaded The one and only QuickAdsenseReloaded
         * @since 1.0
         */
        private static $instance;

        /**
         * QUADS HTML Element Helper Object
         *
         * @var object
         * @since 2.0.0
         */
        public $html;

        /* QUADS LOGGER Class
         * 
         */
        public $logger;

        /**
         * Main QuickAdsenseReloaded Instance
         *
         * Insures that only one instance of QuickAdsenseReloaded exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.0
         * @static
         * @static var array $instance
         * @uses QuickAdsenseReloaded::setup_constants() Setup the constants needed
         * @uses QuickAdsenseReloaded::includes() Include the required files
         * @uses QuickAdsenseReloaded::load_textdomain() load the language files
         * @see QUADS()
         * @return The one true QuickAdsenseReloaded
         */
        public static function instance() {
            if( !isset( self::$instance ) && !( self::$instance instanceof QuickAdsenseReloaded ) ) {
                self::$instance = new QuickAdsenseReloaded;
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->logger = new quadsLogger( "quick_adsense_log_" . date( "Y-m-d" ) . ".log", quadsLogger::INFO );
                self::$instance->html = new QUADS_HTML_Elements();
                self::$instance->hooks();

            }
            return self::$instance;
        }

        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @since 1.0
         * @access protected
         * @return void
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'QUADS' ), '1.0' );
        }

        /**
         * Disable unserializing of the class
         *
         * @since 1.0
         * @access protected
         * @return void
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'QUADS' ), '1.0' );
        }

        /**
         * Setup plugin constants
         *
         * @access private
         * @since 1.0
         * @return void
         */
        private function setup_constants() {
            global $wpdb;

            // Plugin Folder Path
            if( !defined( 'QUADS_PLUGIN_DIR' ) ) {
                define( 'QUADS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }

            // Plugin Folder URL
            if( !defined( 'QUADS_PLUGIN_URL' ) ) {
                define( 'QUADS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }

            // Plugin Root File
            if( !defined( 'QUADS_PLUGIN_FILE' ) ) {
                define( 'QUADS_PLUGIN_FILE', __FILE__ );
            }
        }

        /**
         * Include required files
         *
         * @access private
         * @since 1.0
         * @return void
         */
        private function includes() {
            global $quads_options;

            require_once QUADS_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
            $quads_options = quads_get_settings();
            require_once QUADS_PLUGIN_DIR . 'includes/scripts.php';
            require_once QUADS_PLUGIN_DIR . 'includes/template-functions.php';
            require_once QUADS_PLUGIN_DIR . 'includes/class-quads-license-handler.php';
            require_once QUADS_PLUGIN_DIR . 'includes/debug/classes/QuadsDebug.interface.php';
            require_once QUADS_PLUGIN_DIR . 'includes/debug/classes/QuadsDebug.class.php';
            require_once QUADS_PLUGIN_DIR . 'includes/logger.php';
            require_once QUADS_PLUGIN_DIR . 'includes/class-quads-html-elements.php';
            require_once QUADS_PLUGIN_DIR . 'includes/widgets.php';
            require_once QUADS_PLUGIN_DIR . 'includes/shortcodes.php';
            require_once QUADS_PLUGIN_DIR . 'includes/api.php';
            require_once QUADS_PLUGIN_DIR . 'includes/render-ad-functions.php';
            require_once QUADS_PLUGIN_DIR . 'includes/class-quads-license-handler.php';

            if( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
                require_once QUADS_PLUGIN_DIR . 'includes/admin/add-ons.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/admin-actions.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/admin-footer.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/admin-pages.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/plugins.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/welcome.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/settings/contextual-help.php';
                require_once QUADS_PLUGIN_DIR . 'includes/install.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/tools.php';
                require_once QUADS_PLUGIN_DIR . 'includes/meta-boxes.php';
                require_once QUADS_PLUGIN_DIR . 'includes/quicktags.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/admin-notices.php';
                require_once QUADS_PLUGIN_DIR . 'includes/admin/settings/advanced-settings.php';
            }
        }

        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         *
         */
        private function hooks() {

            /* Instantiate class QUADS_licence 
             * Create 
             * @since 1.0.0
             * @return apply_filter mashsb_settings_licenses and create licence key input field in core mashsb
             */
            if( class_exists( 'QUADS_License' ) ) {
                $quads_sl_license = new QUADS_License( __FILE__, 'WP QUADS PRO', QUADS_VERSION, 'Rene Hermenau', 'edd_sl_license_key' );
            }
        }

        /**
         * Loads the plugin language files
         *
         * @access public
         * @since 1.0
         * @return void
         */
        public function load_textdomain() {
            // Set filter for plugin's languages directory
            $quads_lang_dir = dirname( plugin_basename( QUADS_PLUGIN_FILE ) ) . '/languages/';
            $quads_lang_dir = apply_filters( 'quads_languages_directory', $quads_lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'quick-adsense-reloaded' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'quick-adsense-reloaded', $locale );

            // Setup paths to current locale file
            $mofile_local = $quads_lang_dir . $mofile;
            $mofile_global = WP_LANG_DIR . '/quads/' . $mofile;
            //echo $mofile_local;
            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/quads folder
                load_textdomain( 'quick-adsense-reloaded', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/quick-adsense-reloaded/languages/ folder
                load_textdomain( 'quick-adsense-reloaded', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'quick-adsense-reloaded', false, $quads_lang_dir );
            }
        }

    }

    endif; // End if class_exists check

/**
 * The main function responsible for returning the one true QuickAdsenseReloaded
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: $QUADS = QUADS();
 *
 * @since 2.0.0
 * @return object The one true QuickAdsenseReloaded Instance
 */
//function QUADS() {
//    return QuickAdsenseReloaded::instance();
//}
//
//// Get QUADS Running
//QUADS();

/**
 * Populate the $quads global with an instance of the QuickAdsenseReloaded class and return it.
 *
 * @return $quads a global instance class of the QuickAdsenseReloaded class.
 */
function wp_quads_pro_loaded() {

    global $quads;

    if( !is_null( $quads ) ) {
        return $quads;
    }

    $quads_instance = new QuickAdsenseReloaded;
    $quads = $quads_instance->instance();
    return $quads;
}
add_action( 'plugins_loaded', 'wp_quads_pro_loaded' );

// This hook is run immediately after any plugin is activated, and may be used to detect the activation of plugins.
add_action( 'activated_plugin', array('QUADS_Utils', 'deactivate_other_instances') );



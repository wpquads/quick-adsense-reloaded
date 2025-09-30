<?php
/**
 * Plugin Name: AdSense Integration WP QUADS
 * Plugin URI: https://wordpress.org/plugins/quick-adsense-reloaded/
 * Description: Insert Google AdSense and other ad formats fully automatic into your website
 * Author: Sanjeev Kumar
 * Author URI: https://wordpress.org/plugins/quick-adsense-reloaded/
 * Version: 2.0.94.1
 * Text Domain: quick-adsense-reloaded
 * Domain Path: languages
 * Credits: WP QUADS - Quick AdSense Reloaded is a fork of Quick AdSense
 * License: GPL2
 *
 * WP QUADS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP QUADS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with plugin. If not, see <http://www.gnu.org/licenses/>.
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
  define( 'QUADS_VERSION', '2.0.94.1' );
}

// Plugin name
if( !defined( 'QUADS_NAME' ) ) {
   define( 'QUADS_NAME', 'WP QUADS - Quick AdSense Reloaded' );
}

// Debug
if( !defined( 'QUADS_DEBUG' ) ) {
   define( 'QUADS_DEBUG', false );
}

// Files that needs to be loaded early
if( !class_exists( 'QUADS_Utils' ) ) {
   require dirname( __FILE__ ) . '/includes/quads-utils.php';
}

// Define some globals
$visibleContentAds = 0; // Amount of ads which are shown
$visibleShortcodeAds = 0; // Number of active ads which are shown via shortcodes
$visibleContentAdsGlobal = 0; // Number of active ads which are shown in the_content
$ad_count_custom = 0; // Number of active custom ads which are shown on the site
$ad_count_widget = 0; // Number of active ads in widgets
$AdsId = array(); // Array of active ad id's
$maxWidgets = 10; // number of widgets
$quads_shortcode_ids=array(); // array of active shortcode ids (new mode)
$quads_total_ads=0; // Total ads to display (new mode)


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
       * Public vi class
       */
      public $vi;
      
      public $adsense;

      public function __construct() {
        
        
        
      }

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
            self::$instance->load_hooks();
            self::$instance->logger = new quadsLogger( "quick_adsense_log_" . gmdate( "Y-m-d" ) . ".log", quadsLogger::INFO );
            self::$instance->html = new QUADS_HTML_Elements();
            self::$instance->adsense = new wpquads\adsense(get_option('quads_settings'));
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
         _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'quick-adsense-reloaded' ), '1.0' );
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
         _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'quick-adsense-reloaded' ), '1.0' );
      }

      /**
       * Setup plugin constants
       *
       * @access private
       * @since 1.0
       * @return void
       */
      private function setup_constants() {
         //global $wpdb;

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
         global $quads_options, $quads_mode,$quads_permissions;

         $quads_mode = get_option('quads-mode');

         require_once QUADS_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
         $quads_options = quads_get_settings();

         
         $permissions = "manage_options";
         if(isset($quads_options['RoleBasedAccess'])){
            $user = wp_get_current_user();
            $rolename = $quads_options['RoleBasedAccess'];
            $rolename= array_map(function($x){ return $x['value']; }, $rolename);
            if( in_array( 'administrator', $user->roles ) ) {
               $permissions = "manage_options";
            }elseif( in_array( 'editor', $user->roles ) && in_array('editor', $rolename) ){
               $permissions = 'edit_pages';
            }elseif( in_array( 'author', $user->roles ) && in_array('author', $rolename)){
               $permissions = 'edit_posts';
            }
            if (class_exists('WPSEO_Options') && in_array( 'wpseo_manager', $user->roles ) && in_array('wpseo_manager', $rolename)) {
               $permissions = 'edit_pages'; 
            }
         }
         $quads_permissions =$permissions;
         require_once QUADS_PLUGIN_DIR . 'includes/post_types.php';
         require_once QUADS_PLUGIN_DIR . 'includes/user_roles.php';
         require_once QUADS_PLUGIN_DIR . 'includes/widgets.php';
         require_once QUADS_PLUGIN_DIR . 'includes/template-functions.php';
         require_once QUADS_PLUGIN_DIR . 'includes/class-quads-license-handler.php';
         require_once QUADS_PLUGIN_DIR . 'includes/logger.php';
         require_once QUADS_PLUGIN_DIR . 'includes/class-quads-html-elements.php';
         require_once QUADS_PLUGIN_DIR . 'includes/shortcodes.php';
         require_once QUADS_PLUGIN_DIR . 'includes/api.php';
         require_once QUADS_PLUGIN_DIR . 'includes/render-ad-functions.php';
         require_once QUADS_PLUGIN_DIR . 'includes/scripts.php';
         require_once QUADS_PLUGIN_DIR . 'includes/automattic-amp-ad.php';
         require_once QUADS_PLUGIN_DIR . 'includes/helper-functions.php';
         require_once QUADS_PLUGIN_DIR . 'includes/conditions.php';
         require_once QUADS_PLUGIN_DIR . 'includes/frontend-checks.php';
         require_once QUADS_PLUGIN_DIR . 'includes/Cron/Cron.php';
         require_once QUADS_PLUGIN_DIR . 'includes/vendor/google/adsense.php';
         require_once QUADS_PLUGIN_DIR . 'includes/class-template.php';
         require_once QUADS_PLUGIN_DIR . 'includes/admin/adsTxt.php';
        require_once QUADS_PLUGIN_DIR . 'includes/elementor/widget.php';
        require_once QUADS_PLUGIN_DIR . 'includes/amp-condition-display.php';
         require_once QUADS_PLUGIN_DIR . 'includes/ad-selling-helper.php';
        
      quads_check_for_newinstall();

      if(isset($quads_options['report_logging']) && $quads_options['report_logging'] == 'improved_v2'){
         require_once QUADS_PLUGIN_DIR . 'includes/reports/commonV2.php'; 
      }else{
         require_once QUADS_PLUGIN_DIR . 'includes/reports/common.php';
      }        
      //Add reports
         if((isset($quads_options['ad_performance_tracking']) && $quads_options['ad_performance_tracking']) || isset($quads_options['ad_log']) && $quads_options['ad_log'] ){
               require_once QUADS_PLUGIN_DIR . 'includes/reports/analyticsV2.php';
         }
        if ( function_exists('has_blocks')) {
            require_once QUADS_PLUGIN_DIR . 'includes/gutenberg/src/init.php';
        }

         if( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
            require_once QUADS_PLUGIN_DIR . 'includes/admin/add-ons.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/admin-actions.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/admin-footer.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/admin-pages.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/plugins.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/welcome.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/settings/contextual-help.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/tools.php';
            require_once QUADS_PLUGIN_DIR . 'includes/meta-boxes.php';
            require_once QUADS_PLUGIN_DIR . 'includes/quicktags.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/admin-notices.php';
            require_once QUADS_PLUGIN_DIR . 'includes/admin/upgrades/upgrade-functions.php';
            require_once QUADS_PLUGIN_DIR . 'includes/Forms/Form.php';
            require_once QUADS_PLUGIN_DIR . 'includes/Autoloader.php';
            $this->registerNamespaces();

         }

         //Includes new files
         require_once QUADS_PLUGIN_DIR . '/admin/includes/setup.php';
         require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api.php';
         require_once QUADS_PLUGIN_DIR . '/admin/includes/common-functions.php';
         require_once QUADS_PLUGIN_DIR . '/admin/includes/widget.php';

      }

   /**
    * Register used namespaces
    */
   private function registerNamespaces() {
      $autoloader = new wpquads\Autoloader();

      // Autoloader
      $autoloader->registerNamespaces( array(
          "wpquads" => array(
              QUADS_PLUGIN_DIR,
              QUADS_PLUGIN_DIR . 'includes' . DIRECTORY_SEPARATOR . 'Forms',
              QUADS_PLUGIN_DIR . 'includes' . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . 'Elements',
              QUADS_PLUGIN_DIR . 'includes' . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . 'Elements' . DIRECTORY_SEPARATOR . 'Interfaces',
              )
      ) );


      // Register namespaces
      $autoloader->register();
   }



      public function load_hooks() {
            add_filter( 'admin_footer', 'quads_add_deactivation_feedback_modal' );
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

      /*
       * Activation function fires when the plugin is activated.
       * Checks first if multisite is enabled
       * @since 1.0.0
       *
       */

      public static function activation( $networkwide ) {
         global $wpdb;

         if( function_exists( 'is_multisite' ) && is_multisite() ) {
            // check if it is a network activation - if so, run the activation function for each blog id
            if( $networkwide ) {
               $old_blog = $wpdb->blogid;
               // Get all blog ids
               // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
               $blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
               foreach ( $blogids as $blog_id ) {
                  switch_to_blog( $blog_id );
                  QuickAdsenseReloaded::during_activation();
               }
               switch_to_blog( $old_blog );
               return;
            }
         }
         QuickAdsenseReloaded::during_activation();
      }

      /**
       * This function is fired from the activation method.
       *
       * @since 2.1.1
       * @access public
       *
       * @return void
       */
      public static function during_activation() {

         // Add cron event
         require_once plugin_dir_path( __FILE__ ) . 'includes/Cron/Cron.php';
         $cron = new quadsCron();
         $cron->schedule_event();

         // Add Upgraded From Option
         $current_version = get_option( 'quads_version' );
         if( $current_version ) {
            update_option( 'quads_version_upgraded_from', $current_version );
         }
         // First time installation
         // Get all settings and update them only if they are empty
         $quads_options = get_option( 'quads_settings' );
         if( !$quads_options ) {
            $quads_options['post_types'] = array('post', 'page');
            $quads_options['visibility']['AppHome'] = "1";
            $quads_options['visibility']['AppCate'] = "1";
            $quads_options['visibility']['AppArch'] = "1";
            $quads_options['visibility']['AppTags'] = "1";
            $quads_options['quicktags']['QckTags'] = "1";
            $quads_options['reports_settings'] = "1";
            add_option('quads-mode','new');
            update_option( 'quads_settings', $quads_options );
         }

         // Update the current version
         //update_option( 'quads_version', QUADS_VERSION );
         // Add plugin installation date and variable for rating div
         add_option( 'quads_install_date', gmdate( 'Y-m-d h:i:s' ) );
         add_option( 'quads_rating_div', 'no' );
         add_option( 'quads_show_theme_notice', 'yes' );

         // Add the transient to redirect (not for multisites)
         set_transient( 'quads_activation_redirect', true, 3600 );
      }

      /**
       * Get all wp quads settings
       * @return array
       */
      private function startAdsense(){
          new wpquads\adsense(get_option( 'quads_settings' ));
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

/**
 * Populate the $quads global with an instance of the QuickAdsenseReloaded class and return it.
 *
 * @return $quads a global instance class of the QuickAdsenseReloaded class.
 */
function quads_loaded() {

   global $quads;

   if( !is_null( $quads ) ) {
      return $quads;
   }

   $quads_instance = new QuickAdsenseReloaded;
   $quads = $quads_instance->instance();
   return $quads;
}

add_action( 'plugins_loaded', 'quads_loaded' );

/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class hence, needs to be called outside and the
 * function also needs to be static.
 */
register_activation_hook( __FILE__, array('QuickAdsenseReloaded', 'activation') );

/**
 * Check if pro version is installed and active
 */
function quads_is_pro_active() {
   $needle = 'wp-quads-pro';
   $plugins = get_option( 'active_plugins', array() );
   foreach ( $plugins as $key => $value ) {
      if( strpos( $value, $needle ) !== false  ) {
         return true;
      }
   }
   return false;
}


/**
 * Check if advanced settings are available
 *
 * @return boolean
 */
function quads_is_advanced() {
   if( function_exists( 'quads_is_active_pro' ) ) {
      return quads_is_active_pro();
   } else {
      return quads_is_active_deprecated();
   }
   return false;
}

/**
 * Check if wp quads pro is active and installed
 *
 * @deprecated since version 1.3.0
 * @return boolean
 */
function quads_is_active_deprecated() {

   include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
   $plugin = 'wp-quads-pro/wp-quads-pro.php';

   if( is_plugin_active( $plugin ) ) {
      return true;
   }
 }

/**
 * Create a MU plugin to remove unused shortcode when plugin is removed.
 *
 * @since 1.8.12
 */
add_action('update_option_quads_settings', 'wpquads_remove_shortcode',10,3);
function wpquads_remove_shortcode($old_value,$new_value,$option){
  $content_url =WPMU_PLUGIN_DIR.'/wpquads_remove_shortcode.php';
  if(isset($new_value['hide_add_on_disableplugin'])){
    wp_mkdir_p(WPMU_PLUGIN_DIR, 755, true);
    $sourc =plugin_dir_path( __FILE__ ) . 'includes/mu-plugin/wpquads_remove_shortcode.php';
    if (!file_exists($content_url)) {
      copy($sourc,$content_url);
    }
  }else{
    wp_delete_file($content_url);
  }
}

if (QUADS_VERSION >= '2.0.28' && quads_is_pro_active() ) {
         $quads_settings = get_option('quads_settings');    
         if (isset($quads_settings['quads_wp_quads_pro_license_key']) && strpos($quads_settings['quads_wp_quads_pro_license_key'], '****************') !== false) {
       $quads_settings['quads_wp_quads_pro_license_key'] = '';
       update_option( 'quads_settings', $quads_settings );
       delete_option( 'quads_wp_quads_pro_license_active' );
    }
 }
      
add_action( 'wp_loaded','quads_checker_license' );
function quads_checker_license(){
  if ( QUADS_VERSION == '2.0.33' && function_exists('quads_is_pro_active') && quads_is_pro_active() ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if( isset( $_GET["page"] ) && !empty( $_GET ) && $_GET["page"] == 'quads-settings' && isset($_GET["tab"]) && $_GET["tab"] == 'licenses' ){
      $quads_license_obj = new QUADS_License( __FILE__, 'WP QUADS PRO', QUADS_PRO_VERSION, 'Rene Hermenau', 'edd_sl_license_key' );
      $trans_check = get_transient( 'quads_adsense_license_auto_check' );
      if ( $trans_check !== 'quads_adsense_license_auto_check_value' ) {
        $quads_license_obj->weekly_license_check();
      }
    }
  }
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if( function_exists('quads_is_pro_active') && quads_is_pro_active() && isset( $_GET["page"] ) && !empty( $_GET ) && $_GET["page"] == 'quads-settings' && isset($_GET["tab"]) && $_GET["tab"] == 'licenses' ){
    $license = get_option( 'quads_wp_quads_pro_license_active' );
    if( !empty( $license ) && is_object( $license ) && $license->license == 'valid' ) {
      add_action('plugins_loaded','quads_settings_update_title');
    }
  }

function quads_settings_update_title(){
  add_filter('quads_settings_licenses','quads_settings_update_license_t_name',99,1);
}

function quads_settings_update_license_t_name($q_array){
  $q_array['licenses_header']['name'] = '';
  return $q_array;
}

function quads_check_for_newinstall(){
   global $quads_options,$wpdb;            
   $quads_install_date = get_option('quads_install_date',false);
   $quads_install_date_flag = get_option('quads_install_date_flag',false);
   if($quads_install_date && !$quads_install_date_flag){
      $quads_today = gmdate('Y-m-d');
      if($quads_install_date){
         $quads_install_date = gmdate('Y-m-d',strtotime($quads_install_date));
      }
      if($quads_install_date == $quads_today){
         update_option('quads_install_date_flag',true);
         $quads_options['report_logging'] = 'improved_v2';
         $quads_options['logging_toggle'] = 'off';
         update_option('quads_settings',$quads_options);
         update_option('quads_v2_db_no_import',true);
         
      }
   }
}

/**
 * Load local files and return file contents
 * @param $file_path String
 * @return file contents
 * @since 2.0.85
 * */
function quads_local_file_get_contents( $file_path ){

   $file_safe =   '';

    // Include WordPress Filesystem API
    if ( ! function_exists( 'WP_Filesystem' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    // Initialize the API
    global $wp_filesystem;
    if ( ! WP_Filesystem() ) {
        return $file_safe;
    }
    // Check if the file exists
    if ( $wp_filesystem->exists( $file_path ) ) {
        // Read the file content
        $file_safe = $wp_filesystem->get_contents( $file_path );
        return $file_safe;
    }
}
<?php
/**
 * Register Settings
 *
 * @package     QUADS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
   exit;

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0.0
 * @return mixed
 */
function quads_get_option( $key = '', $default = false ) {
   global $quads_options;
   $value = !empty( $quads_options[$key] ) ? $quads_options[$key] : $default;
   $value = apply_filters( 'quads_get_option', $value, $key, $default );
   return apply_filters( 'quads_get_option_' . $key, $value, $key, $default );
}



add_action( 'admin_init', 'quads_check_licene_upgrade_modified',10);
 
function quads_check_licene_upgrade_modified() {
 
   $quads_license_bug_fixed =   get_transient('quads_license_bug_fixed');
   $quads_mode = get_option('quads-mode');
      if($quads_license_bug_fixed != 'value' && QUADS_VERSION >= '2.0.28' &&  $quads_mode == 'new'){
         $quadsAdResetDeleted = get_option('quadsAdResetDeleted');
         if(isset($quadsAdResetDeleted['quads_wp_quads_pro_license_key']) && !empty($quadsAdResetDeleted['quads_wp_quads_pro_license_key'])){

            $license_key = $quadsAdResetDeleted['quads_wp_quads_pro_license_key'];
            $quads_settings = get_option('quads_settings');

            if( strpos($quads_settings['quads_wp_quads_pro_license_key'] , '******') !== false){
               $quads_settings['quads_wp_quads_pro_license_key'] = $license_key;
               $response =  update_option( 'quads_settings', $quads_settings );
            }
         }
         set_transient('quads_license_bug_fixed', 'value', '');
   }
}




/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array QUADS settings
 */
function quads_get_settings() {
   $settings = get_option( 'quads_settings' );

   if( empty( $settings ) ) {
      // Update old settings with new single option
      $general_settings = is_array( get_option( 'quads_settings_general' ) ) ? get_option( 'quads_settings_general' ) : array();
      $ext_settings = is_array( get_option( 'quads_settings_extensions' ) ) ? get_option( 'quads_settings_extensions' ) : array();
      $license_settings = is_array( get_option( 'quads_settings_licenses' ) ) ? get_option( 'quads_settings_licenses' ) : array();
      $addons_settings = is_array( get_option( 'quads_settings_addons' ) ) ? get_option( 'quads_settings_addons' ) : array();
      $imexport_settings = is_array( get_option( 'quads_settings_imexport' ) ) ? get_option( 'quads_settings_imexport' ) : array();
      $help_settings = is_array( get_option( 'quads_settings_help' ) ) ? get_option( 'quads_settings_help' ) : array();

      $settings = array_merge( $general_settings, $ext_settings, $imexport_settings, $help_settings );

      update_option( 'quads_settings', $settings );


   }
   return apply_filters( 'quads_get_settings', $settings );
}

function wpquads_support_page_callback(){
    ?>
     <div class="wpquads_support_div">
          <?php echo esc_html__('If you have any query, please write the query in below box or email us at', 'quick-adsense-reloaded') ?> <a href="mailto:team@wpquads.com">team@wpquads.com</a>. <?php echo esc_html__('We will reply to your email address shortly', 'quick-adsense-reloaded') ?><br><br>
            <span class="wpquads-query-success wpquads_hide"><?php echo esc_html__('Message sent successfully, Please wait we will get back to you shortly', 'quick-adsense-reloaded'); ?></span>
                    <span class="wpquads-query-error wpquads_hide"><?php echo esc_html__('Message not sent. please check your network connection', 'quick-adsense-reloaded'); ?></span>
            <ul>
                <li>
                   <input type="text" id="wpquads_query_email" name="wpquads_query_email" placeholder="Your Email">
                </li>
                <li>
                    <div><textarea rows="5" cols="60" id="wpquads_query_message" name="wpquads_query_message" placeholder="Write your query"></textarea></div>
                </li>
                <li>
                    <strong><?php echo esc_html__('Are you a premium customer ?', 'quick-adsense-reloaded'); ?></strong>
                    <select id="wpquads_query_premium_cus" name="wpquads_query_premium_cus">
                        <option value=""><?php echo esc_html__('Select', 'quick-adsense-reloaded'); ?></option>
                        <option value="yes"><?php echo esc_html__('Yes', 'quick-adsense-reloaded'); ?></option>
                        <option value="no"><?php echo esc_html__('No', 'quick-adsense-reloaded'); ?></option>
                    </select>
                </li>
                <li><button class="button wpquads-send-query"><?php echo esc_html__('Send Message', 'quick-adsense-reloaded'); ?></button></li>
            </ul>
        </div>
    <?php
}
/**
 * Add all settings sections and fields
 *
 * @since 1.0
 * @return void
 */
function quads_register_settings() {

   if( false == get_option( 'quads_settings' ) ) {
      add_option( 'quads_settings' );
   }

   foreach ( quads_get_registered_settings() as $tab => $settings ) {

      add_settings_section(
              'quads_settings_' . $tab, __return_null(), '__return_false', 'quads_settings_' . $tab
      );

      foreach ( $settings as $option ) {

         $name = isset( $option['name'] ) ? $option['name'] : '';
        if($tab=='help' && $option['id'] == 'wpquads_support'){

         add_settings_field(
                     'quads_settings[' . $option['id'] . ']', $name, 'wpquads_support_page_callback', 'quads_settings_' . $tab, 'quads_settings_' . $tab
             );
        }else{
             add_settings_field(
                     'quads_settings[' . $option['id'] . ']', $name, function_exists( 'quads_' . $option['type'] . '_callback' ) ? 'quads_' . $option['type'] . '_callback' : 'quads_missing_callback', 'quads_settings_' . $tab, 'quads_settings_' . $tab, array(
                 'id' => isset( $option['id'] ) ? $option['id'] : null,
                 'desc' => !empty( $option['desc'] ) ? $option['desc'] : '',
                 'desc2' => !empty( $option['desc2'] ) ? $option['desc2'] : '',
                 'helper-desc' => !empty( $option['helper-desc'] ) ? $option['helper-desc'] : '',
                 'name' => isset( $option['name'] ) ? $option['name'] : null,
                 'section' => $tab,
                 'size' => isset( $option['size'] ) ? $option['size'] : null,
                 'options' => isset( $option['options'] ) ? $option['options'] : '',
                 'std' => isset( $option['std'] ) ? $option['std'] : '',
                 'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
                 'textarea_rows' => isset( $option['textarea_rows'] ) ? $option['textarea_rows'] : ''
                     )
             );
        }
      }
   }

   // Store adsense values
   quads_store_adsense_args();

   // Store AdSense value
   //quads_fix_ad_not_shown();
   // Creates our settings in the options table
   register_setting( 'quads_settings', 'quads_settings', 'quads_settings_sanitize' );
}
add_action( 'admin_init', 'quads_register_settings' );

/**
 * Retrieve the array of plugin settings
 *
 * @since 1.8
 * @return array
 */
function quads_get_registered_settings() {

   /**
    * 'Whitelisted' QUADS settings, filters are provided for each settings
    * section to allow extensions and other plugins to add their own settings
    */
    global $quads, $quads_options;
   
   $quads_settings = array(
       /** General Settings */
       'general' => apply_filters( 'quads_settings_general', array(
           array(
               'id' => 'general_header',
               'name' =>  __( 'General & Position', 'quick-adsense-reloaded' ) ,
               'desc' => '',
               'type' => 'header'
           ),
           'maxads' => array(
               'id' => 'maxads',
               'name' => __( 'Limit Amount of ads:', 'quick-adsense-reloaded' ),
               'desc' => __( ' ads on a page.', 'quick-adsense-reloaded' ),
               'desc2' => sprintf( /* translators: %s: adsense guide url */ __( '<a href="%s" target="_blank">Read here</a> to learn how many AdSense ads are allowed. If you are unsure set the value to unlimited.', 'quick-adsense-reloaded' ), 'http://wpquads.com/google-adsense-allowed-number-ads/' ),
               'type' => 'select',
               'std' => 100,
               'options' => array(
                   1 => '1',
                   2 => '2',
                   3 => '3',
                   4 => '4',
                   5 => '5',
                   6 => '6',
                   7 => '7',
                   8 => '8',
                   9 => '9',
                   10 => '10',
                   11 => '11',
                   12 => '12',
                   13 => '13',
                   14 => '14',
                   15 => '15',
                   16 => '16',
                   17 => '17',
                   18 => '18',
                   19 => '19',
                   20 => '20',
                   100 => 'Unlimited',
               ),
           ),
           array(
               'id' => 'ad_position',
               'name' => __( 'Position - Default Ads', 'quick-adsense-reloaded' ),
               'desc' => __( 'Assign and activate ads on specific ad places', 'quick-adsense-reloaded' ),
               'type' => 'ad_position'
           ),
           array(
               'id' => 'visibility',
               'name' => __( 'Visibility', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'visibility'
           ),
           array(
               "id" => "post_types",
               "name" => __( "Post Types", "quick-adsense-reloaded" ),
               "desc" => __( "Select post types where ads are visible.", "quick-adsense-reloaded" ),
               "helper-desc" => __( "Select post types where ads are visible.", "quick-adsense-reloaded" ),
               "type" => "multiselect",
               "options" => quads_get_post_types(),
               "placeholder" => __( "Select Post Type", "quick-adsense-reloaded" )
           ),
           array(
               'id' => 'hide_ajax',
               'name' => __( 'Hide Ads From Ajax Requests', 'quick-adsense-reloaded' ),
               'desc' => __( 'If your site is using ajax based infinite loading it might happen that ads are loaded without any further post content. Disable this here.', 'quick-adsense-reloaded' ),
               'type' => 'checkbox'
           ),
           array(
               'id' => 'quicktags',
               'name' => __( 'Quicktags', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'quicktags'
           ),
           array(
               'id' => 'adsTxtEnabled',
               'name' => __( 'ads.txt - Automatic Creation', 'quick-adsense-reloaded' ),
               'desc' => __( 'Create an ads.txt file', 'quick-adsense-reloaded' ),
               "helper-desc" => sprintf( /* translators: 1: ads.txt file url ,2: ads.txt doc url */
                  __( 'Allow WP QUADS to generate automatically the ads.txt file in root of your website domain. After enabling and saving settings,
                        check if your ads.txt is correct by opening: <a href="%1$s" target="_blank">%1$s</a> <br><a href="%2$s" target="_blank">Read here</a> to learn more about ads.txt', 'quick-adsense-reloaded' ),
                        get_site_url() . '/ads.txt',
                       'https://wpquads.com/make-more-revenue-by-using-an-ads-txt-in-your-website-root-domain/'
                       ),
               'type' => 'checkbox'
           ),
            array(
               'id' => 'lazy_load_global',
               'name' => __( 'Lazy Loading for Adsense', 'quick-adsense-reloaded' ),
               // 'desc' => __( 'Lazy Loading for Adsense', 'quick-adsense-reloaded' ),
               'type' => 'checkbox'
           ),
           array(
               'id' => 'quicktags',
               'name' => __( 'Quicktags', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'quicktags'
           ),
           'adsense_header' => array(
               'id' => 'adsense_header',
               'name' =>  __( 'Ads', 'quick-adsense-reloaded' ) ,
               'desc' => '<div class="adsense_admin_header">' . __( 'Enter your ads below:</div>
                               <ul style="margin-top:10px;">
                               <li style="font-weight:600;">- <i>AdSense</i> for using <span style="font-weight:600;">AdSense Text & display Ads</span>!</li>
                               <li style="font-weight:600;">- <i>Plain Text / HTML / JS</i> for all other ads! <br><strong>Caution:</strong> Adding AdSense code into <i>Plain Text</i> option can result in non-displayed ads!</li></ul>', 'quick-adsense-reloaded' )
               . '</ul>'
               . '<div style="clear:both;">' . sprintf( /* translators: %s: troubleshooting guide url */  __( '<strong>Ads are not showing? Read the <a href="%s" target="_blank">troubleshooting guide</a> to find out how to resolve it.', 'quick-adsense-reloaded' ), 'http://wpquads.com/docs/adsense-ads-are-not-showing/?utm_source=plugin&utm_campaign=wpquads-settings&utm_medium=website&utm_term=toplink' ) . ''
               . '<br><a href="http://wpquads.com/effective-adsense-banner-size-formats/?utm_campaign=plugin&utm_source=general_tab&utm_medium=admin&utm_content=best_banner_sizes" target="_blank">Read this</a> to find out the most effective AdSense banner sizes. </div>'
               . '<div id="quads-open-toggle" class="button">' . __( 'Open All Ads', 'quick-adsense-reloaded' ) . '</div>',
               'type' => 'header'
           ),
           array(
               'id' => 'quads_ads',
               'name' => '',
               'type' => 'ad_code'
           ),
           array(
               'id' => 'new_ad',
               'name' => '',
               'type' => 'new_ad',
           ),
           'widget_header' => array(
               'id' => 'widget_header',
               'name' =>  __( 'Widget Ads', 'quick-adsense-reloaded' ) ,
               'desc' => sprintf( /* translators: %s: widger url*/
                  __( 'After creating your ads here go to <a href="%s" target="_self">Appearance->Widgets</a> and drag the WP QUADS widget into place.', 'quick-adsense-reloaded' ), admin_url() . 'widgets.php' ),
               'type' => 'header'
           ),
           'ad1_widget' => array(
               'id' => 'ad1_widget',
               'name' => __( 'Ad widget 1', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           'ad2_widget' => array(
               'id' => 'ad2_widget',
               'name' => __( 'Ad widget 2', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           'ad3_widget' => array(
               'id' => 'ad3_widget',
               'name' => __( 'Ad widget 3', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           'ad4_widget' => array(
               'id' => 'ad4_widget',
               'name' => __( 'Ad widget 4', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           'ad5_widget' => array(
               'id' => 'ad5_widget',
               'name' => __( 'Ad widget 5', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           'ad6_widget' => array(
               'id' => 'ad6_widget',
               'name' => __( 'Ad widget 6', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           'ad7_widget' => array(
               'id' => 'ad7_widget',
               'name' => __( 'Ad widget 7', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           'ad8_widget' => array(
               'id' => 'ad8_widget',
               'name' => __( 'Ad widget 8', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           'ad9_widget' => array(
               'id' => 'ad9_widget',
               'name' => __( 'Ad widget 9', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           'ad10_widget' => array(
               'id' => 'ad10_widget',
               'name' => __( 'Ad widget 10', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'adsense_widget',
               'size' => 4
           ),
           array(
               'id' => 'plugin_header',
               'name' =>  __( 'Plugin Settings', 'quick-adsense-reloaded' ) ,
               'desc' => '',
               'type' => 'header'
           ),
           'priority' => array(
               'id' => 'priority',
               'name' => __( 'Load Priority', 'quick-adsense-reloaded' ),
               //'desc' => __( 'Do not change this until you know what you are doing. Usually the default value 20 is working fine. Changing this value can lead to unexpected results like ads not showing or loaded on wrong order. <strong>Default:</strong> 20', 'quick-adsense-reloaded' ),
               'helper-desc' => __( 'Do not change this until you know what you are doing. Usually the default value 20 is working fine. Changing this value can lead to unexpected results like ads not showing or loaded on wrong order. <strong>Default:</strong> 20', 'quick-adsense-reloaded' ),
               'type' => 'number',
               'size' => 'small',
               'std' => 10
           ),
           'create_settings' => array(
               'id' => 'create_settings',
               'name' => __( 'Remove menu button', 'quick-adsense-reloaded' ),
               //'desc' => __( 'Make the WPQUADS settings available from <strong>Settings->WPQUADS</strong>. This will remove the primary menu button from the admin sidebar', 'quick-adsense-reloaded' ),
               'desc' => __( 'Remove it', 'quick-adsense-reloaded' ),
               'helper-desc' => __( 'Make the WPQUADS settings available from <strong>Settings->WPQUADS</strong>. This will remove the primary menu button from the admin sidebar', 'quick-adsense-reloaded' ),
               'type' => 'checkbox',
           ),
           'disableAmpScript' => array(
               'id' => 'disableAmpScript',
               'name' => __( 'Disable AMP script', 'quick-adsense-reloaded' ),
               //'desc' => __( 'Make the WPQUADS settings available from <strong>Settings->WPQUADS</strong>. This will remove the primary menu button from the admin sidebar', 'quick-adsense-reloaded' ),
               'desc' => __( 'Disable AMP Scripts', 'quick-adsense-reloaded' ),
               'helper-desc' => __( 'Disable duplicate AMP ad script integration if your AMP plugin is already loading the script https://cdn.ampproject.org/v0/amp-ad-0.1.js into your site', 'quick-adsense-reloaded' ),
               'type' => 'checkbox',
           ),
           'uninstall_on_delete' => array(
               'id' => 'uninstall_on_delete',
               'name' => __( 'Delete Data on Uninstall?', 'quick-adsense-reloaded' ),
               //'desc' => __( 'Check this box if you would like <strong>Settings->WPQUADS</strong> to completely remove all of its data when the plugin is deleted.', 'quick-adsense-reloaded' ),
                'helper-desc' => __( 'Check this box if you would like <strong>Settings->WPQUADS</strong> to completely remove all of its data when the plugin is deleted.', 'quick-adsense-reloaded' ),
               'desc' => 'Delete data',
               'type' => 'checkbox'
           ),
           'hide_add_on_disableplugin' => array(
               'id' => 'hide_add_on_disableplugin',
               'name' => __( 'Hide Shortcode after Deactivate', 'quick-adsense-reloaded' ),
               //'desc' => __( 'Check this box if you would like <strong>Settings->WPQUADS</strong> to completely remove all of its data when the plugin is deleted.', 'quick-adsense-reloaded' ),
               'helper-desc' => __( 'Check this box if you would like to Hide [quads] shortcode from the content after deactivating the plugin.', 'quick-adsense-reloaded' ),
               'desc' => 'Hides [quads] shortcode from the content',
               'type' => 'checkbox'
           ),
           'debug_mode' => array(
               'id' => 'debug_mode',
               'name' => __( 'Debug mode', 'quick-adsense-reloaded' ),
               'desc' => __( 'Check this to not minify JavaScript and CSS files. This makes debugging much easier and is recommended setting for inspecting issues on your site', 'quick-adsense-reloaded' ),
               'type' => 'checkbox'
           )
               )
       ),
       'licenses' => apply_filters( 'quads_settings_licenses', array('licenses_header' => array(
               'id' => 'licenses_header',
               'name' => __( 'Activate Your License', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'header'
           ),)
       ),
       'extensions' => apply_filters( 'quads_settings_extension', array()
       ),
       'addons' => apply_filters( 'quads_settings_addons', array(
           'addons' => array(
               'id' => 'addons',
               'name' => '',
               'desc' => '',
               'type' => 'addons'
           ),
               )
       ),
       'imexport' => apply_filters( 'quads_settings_imexport', array(
           'imexport' => array(
               'id' => 'imexport',
               'name' => '',
               'desc' => '',
               'type' => 'imexport'
           )
               )
       ),
       'help' => apply_filters( 'quads_settings_help', array(

            'support' => array(
               'id' => 'wpquads_support',
               'name' => __( 'Get help from our development team', 'quick-adsense-reloaded' ),
                'desc' => '',
               'type' => 'header'
           ),
           'systeminfo' => array(
               'id' => 'systeminfo',
               'name' => __( 'Systeminfo', 'quick-adsense-reloaded' ),
               'desc' => '',
               'type' => 'systeminfo'
           )
               )
       )
   );

   return $quads_settings;
}

function quads_get_active_ads_data() {
   global $quads_options;

   // Return early
   if (empty($quads_options['ads'])){
      return 0;
   }
   // count valid ads
   $i = 1;
   foreach ( $quads_options['ads'] as $ads) {
      $tmp = isset( $quads_options['ads']['ad' . $i]['code'] ) ? trim( $quads_options['ads']['ad' . $i]['code'] ) : '';
       // id is valid if there is either the plain text field populated or the adsense ad slot and the ad client id
       if( !empty( $tmp ) || (!empty( $quads_options['ads']['ad' . $i]['g_data_ad_slot'] ) && !empty( $quads_options['ads']['ad' . $i]['g_data_ad_client'] ) ) ) {
           $adsArray[] = 'ad'.$i;
       }
       $i++;
   }
   return (isset($adsArray) && count($adsArray) > 0) ? $adsArray : 0;
}

add_action('wp_ajax_wpquads_ads_for_shortcode_data', 'wpquads_ads_for_shortcode_data');
function wpquads_ads_for_shortcode_data(){
   if ( ! isset( $_POST['wpquads_security_nonce'] ) ){
         wp_die('Invalid Request');
   }
   if ( !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpquads_security_nonce'] ) ), 'quads_ajax_nonce' ) && !current_user_can( 'manage_options' )){
         wp_die('Unauthorized Request');
   }
      $html = quads_get_active_ads_data();
      echo json_encode($html);
      wp_die();
      
}

add_action('wp_ajax_wpquads_ads_for_shortcode', 'wpquads_ads_for_shortcode');
function wpquads_ads_for_shortcode(){
      if ( ! isset( $_POST['wpquads_security_nonce'] ) ){
        return;
    }
    if ( !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpquads_security_nonce'] ) ), 'quads_ajax_nonce' ) ){
        return;
    }
     global $quads_options;
      echo '<select id="quads-select-for-shortcode">';
      foreach ($quads_options['ads'] as $key => $value){
        echo '<option value="'.esc_attr($key).'"> '.esc_attr($key).'</option>';
      }
    echo '</select>';
   wp_die();

}

add_action('wp_ajax_wpquads_send_query_message', 'wpquads_send_query_message');
function wpquads_send_query_message(){

    if ( ! isset( $_POST['wpquads_security_nonce'] ) ){
        return;
    }
    if ( !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpquads_security_nonce'] ) ), 'quads_ajax_nonce' ) ){
        return;
    }
    $customer_type  = 'Are you a premium customer ? No';
    $message = $email = $premium_cus = '';
    if(isset($_POST['message'])){
        $message = sanitize_textarea_field( wp_unslash( $_POST['message'] ) );
    }
    if(isset($_POST['email'])){
        $email = sanitize_email( wp_unslash( $_POST['email'] ) );
    }
    if(isset($_POST['premium_cus'])){
        $premium_cus =  sanitize_text_field( wp_unslash( $_POST['premium_cus'] ) );
    }
    $user           = wp_get_current_user();

    if($premium_cus == 'yes'){
        $customer_type  = 'Are you a premium customer ? Yes';
    }

    $message = '<p>'.esc_html($message).'</p><br><br>'. esc_attr($customer_type). '<br><br> query from WPQuads support tab <br> User Website URL: '.esc_url(site_url());

    if($user){
        $user_data  = $user->data;
        $user_email = $user_data->user_email;
        if($email){
            $user_email = $email;
        }
        //php mailer variables
        $sendto    = 'team@ampforwp.com';
        $subject   = "WPQuads Support ticket";
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: '. esc_attr($user_email);
        $headers[] = 'Reply-To: ' . esc_attr($user_email);
        // Load WP components, no themes.
        $sent = wp_mail($sendto, $subject, $message, $headers);
        if($sent){
            echo json_encode(array('status'=>'t'));
        }else{
            echo json_encode(array('status'=>'f'));
        }
    }
    wp_die();
}

/**
 * return empty settings
 * @return string empty one
 */
function quads_empty_callback() {
   return '';
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since 0.9.0
 *
 * @param array $input The value input in the field
 *
 * @return string $input Sanitized value
 */
function quads_settings_sanitize( $input = array() ) {

   global $quads_options;


   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information but sanitizing the settings fields
   if( empty( $_POST['_wp_http_referer'] ) ) {
      return $input;
   }

   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information but sanitizing the settings fields
   parse_str(  sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) ), $referrer );

   $settings = quads_get_registered_settings();
   $tab = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';


   $input = $input ? $input : array();
   $input = apply_filters( 'quads_settings_' . $tab . '_sanitize', $input );
   // Loop through each setting being saved and pass it through a sanitization filter
   foreach ( $input as $key => $value ) {

      // Get the setting type (checkbox, select, etc)
      $type = isset( $settings[$tab][$key]['type'] ) ? $settings[$tab][$key]['type'] : false;
      if( $type ) {
         // Field type specific filter
         $input[$key] = apply_filters( 'quads_settings_sanitize_' . $type, $value, $key );
      }

      // General filter
      $input[$key] = apply_filters( 'quads_settings_sanitize', $value, $key );
   }


   // Loop through the whitelist and unset any that are empty for the tab being saved
   if( !empty( $settings[$tab] ) ) {
      foreach ( $settings[$tab] as $key => $value ) {
         // settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
         if( is_numeric( $key ) ) {
            $key = $value['id'];
         }

         if( empty( $input[$key] ) ) {
            unset( $quads_options[$key] );
         }
      }
   }


   // Merge our new settings with the existing
   $output = array_merge( $quads_options, $input );


   add_settings_error( 'quads-notices', '', __( 'Settings updated.', 'quick-adsense-reloaded' ), 'updated' );

   return $output;
}

/**
 * Sanitize all fields and remove whitespaces
 *
 * @since 1.5.3
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function quads_sanitize_general_field( $input ){
   if (!is_array( $input )){
      return trim($input);
   }
   return array_map('quads_sanitize_general_field', $input);
}
add_filter( 'quads_settings_sanitize', 'quads_sanitize_general_field' );

/**
 * Sanitize text fields
 *
 * @since 1.8
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function quads_sanitize_text_field( $input ) {
   return trim( $input );
}
add_filter( 'quads_settings_sanitize_text', 'quads_sanitize_text_field' );

/**
 * Retrieve settings tabs
 *
 * @since 1.8
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function quads_get_settings_tabs() {

   $settings = quads_get_registered_settings();

   $tabs = array();
   $tabs['general'] = __( 'General', 'quick-adsense-reloaded' );

   if( !empty( $settings['visual'] ) ) {
      $tabs['visual'] = __( 'Visual', 'quick-adsense-reloaded' );
   }

   if( !empty( $settings['networks'] ) ) {
      //$tabs['networks'] = __( 'Social Networks', 'quick-adsense-reloaded' );
   }

   if( !empty( $settings['extensions'] ) ) {
      $tabs['extensions'] = __( 'Add-On Setting', 'quick-adsense-reloaded' );
   }


   if( !empty( $settings['licenses'] ) && quads_is_extra() || quads_is_advanced() ) {
      $tabs['licenses'] = __( 'License', 'quick-adsense-reloaded' );
   }

   //$tabs['addons'] = __( 'Add-Ons', 'quick-adsense-reloaded' );

   $tabs['imexport'] = __( 'Import/Export', 'quick-adsense-reloaded' );

   $tabs['help'] = __( 'Help', 'quick-adsense-reloaded' );

   //$tabs['misc']      = __( 'Misc', 'quick-adsense-reloaded' );

   return apply_filters( 'quads_settings_tabs', $tabs );
}

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function quads_header_callback( $args ) {
   if( !empty( $args['desc'] ) ) {
      //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Escaping has been done in quads_get_registered_settings() function
      echo $args['desc'];
   } else {
      echo '&nbsp';
   }
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_checkbox_callback( $args ) {
   global $quads_options;

   $checked = isset( $quads_options[$args['id']] ) ? checked( 1, $quads_options[$args['id']], false ) : '';
   echo '<input type="checkbox" id="quads_settings[' . esc_attr($args['id']) . ']" name="quads_settings[' . esc_attr($args['id']) . ']" value="1" ' . esc_attr($checked) . '/>';
   echo '<label class="quads_hidden" for="quads_settings[' . esc_attr($args['id']) . ']"> ' . esc_html($args['desc']) . '</label>';
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_checkbox_adsense_callback( $args ) {
   global $quads_options;

   $checked = isset( $quads_options[$args['id']] ) ? checked( 1, $quads_options[$args['id']], false ) : '';
   echo '<input type="checkbox" id="quads_settings[' . esc_attr($args['id']) . ']" name="quads_settings[' . esc_attr($args['id']) . ']" value="1" ' . esc_attr( $checked ) . '/>';
   echo '<label class="quads_hidden" for="quads_settings[' . esc_attr($args['id']) . ']"> ' . esc_html($args['desc']) . '</label>';
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_multicheck_callback( $args ) {
   global $quads_options;

   if( !empty( $args['options'] ) ) {
      foreach ( $args['options'] as $key => $option ):
         if( isset( $quads_options[$args['id']][$key] ) ) {
            $enabled = $option;
         } else {
            $enabled = NULL;
         }
         echo '<input name="quads_settings[' . esc_attr($args['id']) . '][' . esc_attr($key) . ']" id="quads_settings[' . esc_attr($args['id']) . '][' . esc_attr($key). ']" type="checkbox" value="' . esc_attr($option) . '" ' . esc_attr(checked( $option, $enabled, false )) . '/>&nbsp;';
         echo '<label for="quads_settings[' . esc_attr($args['id']) . '][' . esc_attr($key) . ']">' . esc_html($option) . '</label><br/>';
      endforeach;
      echo '<p class="description quads_hidden">' . esc_attr($args['desc']) . '</p>';
   }
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 1.3.3
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_radio_callback( $args ) {
   global $quads_options;

   foreach ( $args['options'] as $key => $option ) :
      $checked = false;

      if( isset( $quads_options[$args['id']] ) && $quads_options[$args['id']] == $key )
         $checked = true;
      elseif( isset( $args['std'] ) && $args['std'] == $key && !isset( $quads_options[$args['id']] ) )
         $checked = true;

      echo '<input name="quads_settings[' . esc_attr($args['id']) . ']"" id="quads_settings[' . esc_attr($args['id']) . '][' . esc_attr($key) . ']" type="radio" value="' . esc_attr($key) . '" ' . esc_attr(checked( true, $checked, false )) . '/>&nbsp;';
      echo '<label for="quads_settings[' . esc_attr($args['id']) . '][' . esc_attr($key) . ']">' . esc_html($option) . '</label><br/>';
   endforeach;

   echo '<p class="description quads_hidden">' . esc_attr($args['desc']) . '</p>';
}

/**
 * Radio Callback for ad types
 *
 * Renders radio boxes for specific ads
 *
 * @since 1.2.7
 * @param1 array $args Arguments passed by the setting
 * @param2 id int ID of the ad
 *
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_adtype_callback( $id, $args ) {
   global $quads_options;

   foreach ( $args['options'] as $key => $option ) :
      $checked = false;

      if( isset( $quads_options['ads'][$id]['ad_type'] ) && $quads_options['ads'][$id]['ad_type'] == $key )
         $checked = true;
      elseif( isset( $args['std'] ) && $args['std'] == $key && !isset( $quads_options['ads'][$id]['ad_type'] ) )
         $checked = true;

      echo '<input name="quads_settings[ads][' . esc_attr($id) . '][ad_type]" class="quads_adsense_type" id="quads_settings[ads][' . esc_attr($id) . '][ad_type_' . esc_attr($key) . ']" type="radio" value="' . esc_attr($key) . '" ' . esc_attr(checked( true, $checked, false )) . '/>&nbsp;';
      echo '<label for="quads_settings[ads][' . esc_attr($id) . '][ad_type_' . esc_attr($key) . ']">' . esc_html($option) . '</label>&nbsp;';
   endforeach;

   echo '<p class="description quads_hidden">' . esc_html(esc_attr($args['desc'])) . '</p>';
}

/**
 * Radio Callback for ad positions
 *
 * Renders radio boxes for left center right alignment
 *
 * @since 1.2.7
 * @param1 array $args Arguments passed by the setting
 * @param2 id int ID of the ad
 *
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_adposition_callback( $id, $args ) {
   global $quads_options;

   foreach ( $args['options'] as $key => $option ) :
      $checked = false;

      if( isset( $quads_options['ads'][$id]['align'] ) && $quads_options['ads'][$id]['align'] == $key )
         $checked = true;
      elseif( isset( $args['std'] ) && $args['std'] == $key && !isset( $quads_options['ads'][$id]['align'] ) )
         $checked = true;

      if( $key == '3' ) {
         echo '<input name="quads_settings[ads][' . esc_attr($id) . '][align]" class="quads_adsense_align" id="quads_settings[ads][' . esc_attr($id) . '][align_' . esc_attr($key) . ']" type="radio" value="' . esc_attr($key) . '" ' . esc_attr(checked( true, $checked, false )) . '/>&nbsp;';
         echo '<label for="quads_settings[ads][' . esc_attr($id) . '][align_' . esc_attr($key) . ']">Default</label>&nbsp;';
      } else {
         echo '<input name="quads_settings[ads][' . esc_attr($id) . '][align]" class="quads_adsense_positon" id="quads_settings[ads][' . esc_attr($id) . '][align_' . esc_attr($key) . ']" type="radio" value="' . esc_attr($key) . '" ' . esc_attr(checked( true, $checked, false )) . '/>&nbsp;';
         echo '<label for="quads_settings[ads][' . esc_attr($id) . '][align_' . esc_attr($key) . ']"><img src="' . esc_url(QUADS_PLUGIN_URL) . 'assets/images/align_' . esc_attr($key) . '.png" width="75" height="56"></label>&nbsp;';
      }

   endforeach;
}


/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_text_callback( $args ) {
   global $quads_options;

   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
   echo '<input type="text" class="' . esc_attr($size) . '-text" id="quads_settings[' . esc_attr($args['id']) . ']" name="quads_settings[' . esc_attr($args['id']) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
   echo '<label class="quads_hidden" class="quads_hidden" for="quads_settings[' . esc_attr($args['id']) . ']"> ' . esc_html($args['desc']) . '</label>';
}

/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since 1.9
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_number_callback( $args ) {
   global $quads_options;

   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   $max = isset( $args['max'] ) ? $args['max'] : 999999;
   $min = isset( $args['min'] ) ? $args['min'] : 0;
   $step = isset( $args['step'] ) ? $args['step'] : 1;

   $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
   echo '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . esc_attr($size) . '-text" id="quads_settings[' .  esc_attr($args['id']) . ']" name="quads_settings[' .  esc_attr($args['id']) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
   echo '<label class="quads_hidden" for="quads_settings[' . esc_attr($args['id']) . ']"> ' . esc_html($args['desc']) . '</label>';
}

/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_textarea_callback( $args ) {
   global $quads_options;

   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : '40';
   echo '<textarea class="large-text quads-textarea" cols="50" rows="' .  esc_attr($size) . '" id="quads_settings[' .  esc_attr($args['id']) . ']" name="quads_settings[' .  esc_attr($args['id']) . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
   //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
   echo '<label class="quads_hidden" for="quads_settings[' .  esc_attr($args['id']) . ']"> ' .  $args['desc'] . '</label>';
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since 1.3
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_password_callback( $args ) {
   global $quads_options;

   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
   echo  '<input type="password" class="' . esc_attr($size) . '-text" id="quads_settings[' . esc_attr($args['id']) . ']" name="quads_settings[' . esc_attr($args['id']) . ']" value="' . esc_attr( $value ) . '"/>';
   echo  '<label for="quads_settings[' . esc_attr($args['id']) . ']"> ' . esc_html($args['desc']) . '</label>';;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.3.1
 * @param array $args Arguments passed by the setting
 * @return void
 */
function quads_missing_callback( $args ) {
    ?>
    <div class="callback_data">
        <?php echo esc_html( 'The callback function used for the', 'quick-adsense-reloaded' ); ?> 
        <strong> <?php echo esc_html($args['id']); ?> </strong> 
        <?php echo esc_html( 'setting is missing.', 'quick-adsense-reloaded' ); ?> 
    </div>
    <?php
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_select_callback( $args ) {
   global $quads_options;

   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   echo '<select id="quads_settings[' . esc_attr($args['id']) . ']" name="quads_settings[' . esc_attr($args['id']) . ']">';

   foreach ( $args['options'] as $option => $name ) :
      $selected = selected( $option, $value, false );
      echo '<option value="' . esc_attr($option) . '" ' . esc_attr($selected) . '>' . esc_html($name) . '</option>';
   endforeach;

   echo  '</select>';
   //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
   echo  '<label class="quads_hidden" for="quads_settings[' . esc_attr($args['id']) . ']"> ' . $args['desc'] . '</label>';
   //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
   echo  '<br>' . $args['desc2'];

}

/**
 * AdSense Type Select Callback
 *
 * Renders Adsense adsense type fields.
 *
 * @since 1.0
 * @param1 array $args Arguments passed by the setting
 * @param2 int $id if od the ad
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_adense_select_callback( $id, $args ) {
   global $quads_options;

   if( isset( $quads_options['ads'][$id][$args['id']] ) )
      $value = $quads_options['ads'][$id][$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';


   $size = !empty( $args['size'] ) ? $args['size'] : 'quads-medium-size';

   echo '<label class="quads_hidden" id="'.esc_attr('quads-label-' . $args['desc'] ). '" for="'.esc_attr('quads_settings[ads][' . $id . '][' . $args['id'].']').'"> ' . esc_html($args['desc']) . ' </label>';
   echo '<select class="'.esc_attr('quads-select-' . $args['desc'] . ' ' . $size ).'" id="'.esc_attr('quads_settings[ads][' . $id . '][' . $args['id'] . ']').'" name="'.esc_attr('quads_settings[ads][' . $id . '][' . $args['id'] . ']').'" >';

   foreach ( $args['options'] as $option => $name ) {
      $selected = selected( $option, $value, false );
      echo  '<option value="' . esc_attr($option) . '" ' . esc_attr($selected) . '>' . esc_html($name) . '</option>';
   }

   echo  '</select>';
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since 2.1.2
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_color_select_callback( $args ) {
   global $quads_options;

   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   echo '<strong>#:</strong><input type="text" style="max-width:80px;border:1px solid #' . esc_attr( stripslashes( $value ) ) . ';border-right:20px solid #' . esc_attr( stripslashes( $value ) ) . ';" id="quads_settings[' .  esc_attr( $args['id']) . ']" class="medium-text ' .  esc_attr( $args['id']) . '" name="quads_settings[' .  esc_attr( $args['id']) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';

   echo '</select>';
   echo '<label class="quads_hidden" for="quads_settings[' . esc_attr($args['id']) . ']"> ' . esc_html($args['desc']) . '</label>';
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @global $wp_version WordPress Version
 */
function quads_rich_editor_callback( $args ) {
   global $quads_options, $wp_version;
   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   if( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
      ob_start();
      wp_editor( stripslashes( $value ), 'quads_settings_' . esc_attr($args['id']), array('textarea_name' => 'quads_settings[' . esc_attr($args['id']) . ']', 'textarea_rows' => esc_attr( $args['textarea_rows'] ) ) );
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
      echo ob_get_clean();
   } else {
      echo '<textarea class="large-text quads-richeditor" rows="10" id="quads_settings[' .  esc_attr($args['id']) . ']" name="quads_settings[' .  esc_attr( $args['id']) . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
   }

   echo '<br/><label class="quads_hidden" for="quads_settings[' .  esc_attr( $args['id']) . ']"> ' . esc_html($args['desc']) . '</label>';

}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_upload_callback( $args ) {
   global $quads_options;

   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
   ?>
   <input type="text" class="<?php echo esc_attr($size); ?>-text quads_upload_field" id="quads_settings[<?php echo esc_attr( $args['id'] );?>]" name="quads_settings[<?php echo esc_attr($args['id']);?>]" value="<?php echo esc_attr( stripslashes( $value ) ); ?>" />
   <span>&nbsp;<input type="button" class="quads_settings_upload_button button-secondary" value="<?php echo esc_html__( 'Upload File', 'quick-adsense-reloaded' ); ?>"/></span>
   <label class="quads_hidden" for="quads_settings[<?php echo esc_attr( $args['id'] );?>]"><?php echo esc_html($args['desc']); ?></label>';
   <?php
}


/**
 * Check if extra settings are available and activated
 *
 * @return boolean
 */
function quads_is_extra() {

   if( !function_exists( 'quads_extra' ) ) {
      return false;
   }

   $lic = get_option( 'quads_wp_quads_pro_license_active' );

   if (!$lic){
     return false;
   }

   if (isset($lic->error) && $lic->error === 'expired'){
       return true;
   }

   if (isset($lic->license) && $lic->license === 'valid'){
       return true;
   }

   if (isset($lic->license) && $lic->license === 'inactive'){
       return false;
   }


//   if( !$lic || (is_object( $lic ) && $lic->success !== true) ) {
//      return false;
//   }

   return false;
}

/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since 1.6
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_color_callback( $args ) {
   global $quads_options;

   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   $default = isset( $args['std'] ) ? $args['std'] : '';

   $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
   ?>
   <input type="text" class="quads-color-picker" id="quads_settings[<?php echo esc_attr($args['id']); ?>]" name="quads_settings[<?php echo esc_attr($args['id']); ?>]" value="<?php echo esc_attr( $value ); ?>" data-default-color="<?php echo esc_attr( $default );?>" />
   <label class="quads_hidden" for="quads_settings[<?php echo esc_attr( $args['id']);?>]"><?php echo esc_html($args['desc']); ?></label>
   <?php
}

/**
 * Registers the license field callback
 *
 * @since 3.0.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS options
 * @return void
 */
if( !function_exists( 'quads_license_key_callback' ) ) {

   function quads_license_key_callback( $args ) {
      global $quads_options;

      $class = '';

      $messages = array();
      $license = get_option( $args['options']['is_valid_license_option'] );


      if( isset( $quads_options[$args['id']] ) ) {
         $value = $quads_options[$args['id']];
      } else {
         $value = isset( $args['std'] ) ? $args['std'] : '';
      }

      if( !empty( $license ) && is_object( $license ) ) {


         // activate_license 'invalid' on anything other than valid, so if there was an error capture it
         if( false === $license->success ) {

            switch ( $license->error ) {

               case 'expired' :

                  $class = 'error';
                  $messages[] = sprintf( /* translators: %1$s: license expiration date, %2$s: renew license url */
                          __( 'Your license key expired on %1$s. Please <a href="%2$s" target="_blank" title="Renew your license key">renew your license key</a>.', 'quick-adsense-reloaded' ), date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ), 'http://wpquads.com/checkout/?edd_license_key=' . $value . '&utm_campaign=notice&utm_source=license_tab&utm_medium=admin&utm_content=license-expired'
                  );

                  $license_status = 'quads-license-' . $class . '-notice';

                  break;

               case 'missing' :

                  $class = 'error';
                  $messages[] = sprintf( /* translators: %s: Account url */ 
                          __( 'Invalid license. Please <a href="%s" target="_blank" title="Visit account page">visit your account page</a> and verify it.', 'quick-adsense-reloaded' ), 'http://wpquads.com/your-account?utm_source=licenses-tab&utm_medium=admin&utm_content=invalid-license&utm_campaign=notice'
                  );

                  $license_status = 'quads-license-' . $class . '-notice';

                  break;

               case 'invalid' :
               case 'site_inactive' :

                  $class = 'error';
                  $messages[] = sprintf( /* translators: %1$s: plugin name, %2$s: account page url */
                          __( 'Your %1$s is not active for this URL. Please <a href="%2$s" target="_blank" title="Visit account page">visit your account page</a> to manage your license key URLs.', 'quick-adsense-reloaded' ), $args['name'], 'http://wpquads.com/your-account?utm_campaign=notice&utm_source=licenses-tab&utm_medium=admin&utm_content=invalid-license'
                  );

                  $license_status = 'quads-license-' . $class . '-notice';

                  break;

               case 'item_name_mismatch' :

                  $class = 'error';
                  $messages[] = sprintf( /* translators: %s: item name */ __( 'This is not a %s.', 'quick-adsense-reloaded' ), $args['name'] );

                  $license_status = 'quads-license-' . $class . '-notice';

                  break;

               case 'no_activations_left':

                  $class = 'error';
                  $messages[] = sprintf( /* translators: %s: upgrades url */  __( 'Your license key has reached its activation limit. <a href="%s" target="_blank">View possible upgrades</a> now.', 'quick-adsense-reloaded' ), 'http://wpquads.com/your-account?utm_campaign=notice&utm_source=licenses-tab&utm_medium=admin&utm_content=invalid-license' );

                  $license_status = 'quads-license-' . $class . '-notice';

                  break;
            }

         } else {

            switch ( $license->license ) {

               case 'valid' :
               default:

                  $class = 'valid';

                  $now = current_time( 'timestamp' );
                  $expiration = strtotime( $license->expires, current_time( 'timestamp' ) );

                  if( 'lifetime' === $license->expires ) {

                     $messages[] = __( ' Your <span class="lifetime">License key is Valid for Lifetime</span> ', 'quick-adsense-reloaded' );

                     $license_status = 'quads-license-lifetime-notice';
                  } elseif( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

                     $messages[] = sprintf( /* translators: 1: license expiration date , 2:Renew license url */
                             __( 'Your license key expires soon! It expires on %1$s. <a class="license_expiring" href="%2$s" target="_blank" title="Renew license">Renew your license key</a>.', 'quick-adsense-reloaded' ), date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ), 'http://wpquads.com/checkout/?edd_license_key=' . $value . '&utm_campaign=notice&utm_source=licenses-tab&utm_medium=admin'
                     );

                     $license_status = 'quads-license-expires-soon-notice';
                  } else {

                     $messages[] = sprintf( /* translators: %s: license expiration date */
                             __( 'Your license key expires on %s.', 'quick-adsense-reloaded' ), date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
                     );

                     $license_status = 'quads-license-expiration-date-notice';
                  }
               break;

               case 'expired' :

               if (isset($license->expires)) {
                $license_exp = gmdate('Y-m-d', strtotime($license->expires));
                $license_exp_d = gmdate('d F Y', strtotime($license->expires));
                if (isset($license->expires)) {
                $license->expires = $license_exp_d;
              }
              $license_info_lifetime = $license->expires;
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
          
                $messages[] = sprintf( /* translators: %s: Renew license url */
                             __( '<span class="expired_license_main">Your <span class="expired_license">License key has been Expired.</span></span> <a class="lic_is_expired" href="%s" target="_blank" title="Renew license">Renew Now</a>', 'quick-adsense-reloaded' ), 'http://wpquads.com/checkout/?edd_license_key=' . $value . '&utm_campaign=notice&utm_source=licenses-tab&utm_medium=admin'
                     );
                     $license_status = 'quads-license-error-notice';
                     }
                break;
                

               case 'inactive' :
                    $messages[] = sprintf( /* translators: %s: Renew license url */
                             __( 'Your license key has been disabled! <a href="%s" target="_blank" title="Renew license">Renew your license key</a>.', 'quick-adsense-reloaded' ), 'http://wpquads.com/checkout/?edd_license_key=' . $value . '&utm_campaign=notice&utm_source=licenses-tab&utm_medium=admin'
                     );
                     $license_status = 'quads-license-error-notice';
                break;
            }
         }

//         switch ( $license->license ) {
//             case 'invalid' :
//                    $messages[] = sprintf(
//                             __( 'Your license key has been disabled! <a href="%s" target="_blank" title="Renew license">Renew your license key</a>.', 'quick-adsense-reloaded' ), 'http://wpquads.com/checkout/?edd_license_key=' . $value . '&utm_campaign=notice&utm_source=licenses-tab&utm_medium=admin'
//                     );
//             break;
//         }

      } else {
         $license_status = null;

      }

      $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
      $html="";

    if( isset( $license_status ) ) {
    ?> <div class="' . $license_status . '">' <?php  
    }else{
    ?> <div class="quads-license-null"> <?php  
    }
        if( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) {
        ?>
            <div class="quads-after-actv"><span class="after_activation"><?php echo esc_html__( 'Congratulations!', 'quick-adsense-reloaded' ); ?></span><span class="after_activation_in"> <?php echo esc_html__( 'WP QUADS PRO is now activated and working for you. This enables the Advanced Settings and High Performance for your ADS!', 'quick-adsense-reloaded' ); ?></span></div>
        <?php    
        }
        ?>
        <?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_sanitize_key() function  ?>
        <input type="text" class="<?php echo sanitize_html_class( $size );?>-text" id="quads_settings[<?php echo quads_sanitize_key( $args['id'] );?>]" name="quads_settings[<?php echo quads_sanitize_key( $args['id'] );?>]" value="<?php echo esc_attr( $value );?>" />
        <?php 
        if( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) {
        ?>
            &nbsp;<input type="submit" class="button-secondary" name="<?php echo esc_attr( $args['id'] );?>_deactivate" value="<?php echo esc_html__( 'Deactivate License', 'quick-adsense-reloaded' );?> "/>
            &nbsp;<input type="submit" class="button-secondary" name="<?php echo esc_attr($args['id']);?>_refresh" value="<?php esc_html__( 'Refresh Info', 'quick-adsense-reloaded' );?>"/>
        <?php
        }
        ?>
        <?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_sanitize_key() function  ?>
        <label for="quads_settings[<?php echo quads_sanitize_key( $args['id'] ); ?>]"><?php echo wp_kses_post( $args['desc'] ); ?></label>
        <?php
        if( !empty( $messages ) ) {
            foreach ( $messages as $message ) {
        ?>
                <div class="quads-license-data quads-license-<?php echo sanitize_html_class($class); ?>">
                <p><?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above */ echo $message;?></p>
                </div>
        <?php
            }
        }
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_sanitize_key() function
        wp_nonce_field( quads_sanitize_key( $args['id'] ) . '-nonce', quads_sanitize_key( $args['id'] ) . '-nonce' );
        ?>
    </div>
    <?php
   }

}

/**
 * Registers the Add-Ons field callback for WPQUADS Add-Ons
 *
 * @since 2.0.5
 * @param array $args Arguments passed by the setting
 * @return html
 */
function quads_addons_callback( $args ) {
   $html = quads_add_ons_page();
   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping has already been done in quads_add_ons_page() function
   echo $html;
}

/**
 * Registers the im/export callback for WPQUADS
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @return html
 */
function quads_imexport_callback( $args ) {
   $html = quads_tools_import_export_display();
   $html .= quads_import_quick_adsense_settings();
   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping has already been done in above functions
   echo $html;
}

/**
 * Registers the system info for WPQUADS
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @return html
 */
function quads_systeminfo_callback( $args ) {
   $html = quads_tools_sysinfo_display();
   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping has already been done in quads_tools_sysinfo_display() function
   echo $html;
}

/**
 * Registers the image upload field
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_upload_image_callback( $args ) {
   global $quads_options;

   if( isset( $quads_options[$args['id']] ) )
      $value = $quads_options[$args['id']];
   else
      $value = isset( $args['std'] ) ? $args['std'] : '';

   $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
   echo '<input type="text" class="' . esc_attr($size) . '-text ' . esc_attr($args['id']) . '" id="quads_settings[' . esc_attr($args['id']) . ']" name="quads_settings[' . esc_attr($args['id']) . ']" value="' . esc_attr( $value ) . '"/>';

   echo '<input type="submit" class="button-secondary quads_upload_image" name="' . esc_attr($args['id']) . '_upload" value="' . esc_html__( 'Select Image', 'quick-adsense-reloaded' ) . '"/>';

   echo '<label class="quads_hidden" for="quads_settings[' . esc_attr($args['id']) . ']"> ' . wp_kses_post($args['desc']) . '</label>';

}

/*
 * Note Callback
 *
 * Show a note
 *
 * @since 2.2.8
 * @param array $args Arguments passed by the setting
 * @return void
 *
 */

function quads_note_callback( $args ) {
   global $quads_options;

}

/**
 * Additional content Callback
 * Adds several content text boxes selectable via jQuery easytabs()
 *
 * @param array $args
 * @return string $html
 * @scince 2.3.2
 */
function quads_add_content_callback( $args ) {
   global $quads_options;

   echo '<div id="quadstabcontainer" class="tabcontent_container"><ul class="quadstabs" style="width:99%;max-width:500px;">';
   foreach ( $args['options'] as $option => $name ) :
      echo '<li class="quadstab" style="float:left;margin-right:4px;"><a href="#' . esc_attr($name['id']) . '">' . wp_kses_post($name['name']) . '</a></li>';
   endforeach;
   echo '</ul>';
   echo '<div class="quadstab-container">';
   foreach ( $args['options'] as $option => $name ) :
      $value = isset( $quads_options[$name['id']] ) ? $quads_options[$name['id']] : '';
      $textarea = '<textarea class="large-text quads-textarea" cols="50" rows="15" id="quads_settings[' . esc_attr($name['id']) . ']" name="quads_settings[' . esc_attr($name['id']) . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
      echo '<div id="' . esc_attr($name['id']) . '" style="max-width:500px;"><span style="padding-top:60px;display:block;">' . wp_kses_post($name['desc']) . ':</span><br>' . wp_kses_post($textarea) . '</div>';
   endforeach;
   echo '</div>';
   echo '</div>';
}

/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since 1.0.8.2
 * @param array $args Arguments passed by the setting
 * @return void
 */
function quads_hook_callback( $args ) {
   do_action( 'quads_' . $args['id'] );
}

/**
 * Set manage_options as the cap required to save QUADS settings pages
 *
 * @since 1.9
 * @return string capability required
 */
function quads_set_settings_cap() {
   return 'manage_options';
}

add_filter( 'option_page_capability_quads_settings', 'quads_set_settings_cap' );




/* returns Cache Status if enabled or disabled
 *
 * @since 2.0.4
 * @return string
 */

function quads_cache_status() {
   global $quads_options;
   if( isset( $quads_options['disable_cache'] ) ) {
      return ' <strong style="color:red;">' . __( 'Transient Cache disabled! Enable it for performance increase.', 'quick-adsense-reloaded' ) . '</strong> ';
   }
}

/* Permission check if logfile is writable
 *
 * @since 2.0.6
 * @return string
 */

function quads_log_permissions() {
   global $quads_options;
   if( !$quads->logger->checkDir() ) {
      return '<br><strong style="color:red;">' . __( 'Log file directory not writable! Set FTP permission to 755 or 777 for /wp-content/plugins/quadssharer/logs/', 'quick-adsense-reloaded' ) . '</strong> <br> Read here more about <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">file permissions</a> ';
   }
}

/**
 * Get number of available ads
 *
 * @global $quads_options $quads_options
 * @return array
 */
function quads_get_ads() {
   global $quads_options;
   $quads_options['ads'] = (isset($quads_options['ads']) && count( $quads_options['ads'] ) !== 0 )?(array)$quads_options['ads']: array();
   if (empty($quads_options['ads'])) {
            $ads = array(
          0 => __( 'Random Ads', 'quick-adsense-reloaded' ),
          1 => isset( $quads_options['ads']['ad1']['label'] ) ? $quads_options['ads']['ad1']['label'] : 'ad1',
          2 => isset( $quads_options['ads']['ad2']['label'] ) ? $quads_options['ads']['ad2']['label'] : 'ad2',
          3 => isset( $quads_options['ads']['ad3']['label'] ) ? $quads_options['ads']['ad3']['label'] : 'ad3',
          4 => isset( $quads_options['ads']['ad4']['label'] ) ? $quads_options['ads']['ad4']['label'] : 'ad4',
          5 => isset( $quads_options['ads']['ad5']['label'] ) ? $quads_options['ads']['ad5']['label'] : 'ad5',
          6 => isset( $quads_options['ads']['ad6']['label'] ) ? $quads_options['ads']['ad6']['label'] : 'ad6',
          7 => isset( $quads_options['ads']['ad7']['label'] ) ? $quads_options['ads']['ad7']['label'] : 'ad7',
          8 => isset( $quads_options['ads']['ad8']['label'] ) ? $quads_options['ads']['ad8']['label'] : 'ad8',
          9 => isset( $quads_options['ads']['ad9']['label'] ) ? $quads_options['ads']['ad9']['label'] : 'ad9',
          10 => isset( $quads_option['ads']['ad10']['label'] ) ? $quads_options['ads']['ad10']['label'] : 'ad10',
      );
      return $ads;
   }

   // Start array with
   $arrHeader = array ( 0 => __( 'Random Ads', 'quick-adsense-reloaded' ) );

   $ads = array();

   foreach ( $quads_options['ads'] as $key => $value ){
      // Skip all widget ads
      if ( false !== strpos($key, '_widget') ){
         continue;
      }
      // Create array
      if (!empty( $value['label'] ) ) {
         $ads[] = $value['label'];
      } else {
          $ads[] = $key;
      }

   }

   return array_merge($arrHeader, $ads);
}

/**
 * Get array of 1 to 50 for image and paragraph dropdown values
 *
 * @global $quads_options $quads_options
 * @return array
 */
function quads_get_values() {

   $array = array(1);
   for ( $i = 1; $i <= 50; $i++ ) {
      $array[] = $i;
   }
   unset( $array[0] ); // remove the 0 and start the array with 1
   return $array;
}

/**
 * Visibility Callback
 *
 * Renders fields for ad visibility
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_visibility_callback( $args ) {
   global $quads_options, $quads;

   $html = $quads->html->checkbox( array('name' => 'quads_settings[visibility][AppHome]', 'current' => !empty( $quads_options['visibility']['AppHome'] ) ? $quads_options['visibility']['AppHome'] : null, 'class' => 'quads-checkbox') ) . esc_html__( 'Homepage ', 'quick-adsense-reloaded' );
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[visibility][AppCate]', 'current' => !empty( $quads_options['visibility']['AppCate'] ) ? $quads_options['visibility']['AppCate'] : null, 'class' => 'quads-checkbox') ) . esc_html__( 'Categories ', 'quick-adsense-reloaded' );
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[visibility][AppArch]', 'current' => !empty( $quads_options['visibility']['AppArch'] ) ? $quads_options['visibility']['AppArch'] : null, 'class' => 'quads-checkbox') ) . esc_html__( 'Archives ', 'quick-adsense-reloaded' );
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[visibility][AppTags]', 'current' => !empty( $quads_options['visibility']['AppTags'] ) ? $quads_options['visibility']['AppTags'] : null, 'class' => 'quads-checkbox') ) . esc_html__( 'Tags', 'quick-adsense-reloaded' ) . '<br>';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[visibility][AppSide]', 'current' => !empty( $quads_options['visibility']['AppSide'] ) ? $quads_options['visibility']['AppSide'] : null, 'class' => 'quads-checkbox') ) . esc_html__( 'Hide Ad Widgets on Homepage', 'quick-adsense-reloaded' ) . '<br>';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[visibility][AppLogg]', 'current' => !empty( $quads_options['visibility']['AppLogg'] ) ? $quads_options['visibility']['AppLogg'] : null, 'class' => 'quads-checkbox') ) . esc_html__( 'Hide Ads when user is logged in.', 'quick-adsense-reloaded' ) . '<br>';

   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
   echo $html;
}

/**
 * Ad position Callback
 *
 * Renders multioptions fields for ad position
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_ad_position_callback( $args ) {
   global $quads_options, $quads;


   // Pos 1
   $html = $quads->html->checkbox( array('name' => 'quads_settings[pos1][BegnAds]', 'current' => !empty( $quads_options['pos1']['BegnAds'] ) ? $quads_options['pos1']['BegnAds'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[pos1][BegnRnd]', 'name' => 'quads_settings[pos1][BegnRnd]', 'selected' => !empty( $quads_options['pos1']['BegnRnd'] ) ? esc_attr( $quads_options['pos1']['BegnRnd'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( 'to <strong>Beginning of Post</strong>', 'quick-adsense-reloaded' ) . '</br>';

   // Pos 2
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos2][MiddAds]', 'current' => !empty( $quads_options['pos2']['MiddAds'] ) ? esc_attr( $quads_options['pos2']['MiddAds'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[pos2][MiddRnd]', 'name' => 'quads_settings[pos2][MiddRnd]', 'selected' => !empty( $quads_options['pos2']['MiddRnd'] ) ? esc_attr( $quads_options['pos2']['MiddRnd'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( 'to <strong>Middle of Post</strong>', 'quick-adsense-reloaded' ) . '</br>';

   // Pos 3
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos3][EndiAds]', 'current' => !empty( $quads_options['pos3']['EndiAds'] ) ? esc_attr( $quads_options['pos3']['EndiAds'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[pos3][EndiRnd]', 'name' => 'quads_settings[pos3][EndiRnd]', 'selected' => !empty( $quads_options['pos3']['EndiRnd'] ) ? esc_attr( $quads_options['pos3']['EndiRnd'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( 'to <strong>End of Post</strong>', 'quick-adsense-reloaded' ) . '</br>';

   // Pos 4
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos4][MoreAds]', 'current' => !empty( $quads_options['pos4']['MoreAds'] ) ? esc_attr( $quads_options['pos4']['MoreAds'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[pos4][MoreRnd]', 'name' => 'quads_settings[pos4][MoreRnd]', 'selected' => !empty( $quads_options['pos4']['MoreRnd'] ) ? esc_attr( $quads_options['pos4']['MoreRnd'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( 'right after <strong>the <span style="font-family:Courier New,Courier,Fixed;">&lt;!--more--&gt;</span> tag</strong>', 'quick-adsense-reloaded' ) . '</br>';

   // Pos 5
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos5][LapaAds]', 'current' => !empty( $quads_options['pos5']['LapaAds'] ) ? esc_attr( $quads_options['pos5']['LapaAds'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[pos5][LapaRnd]', 'name' => 'quads_settings[pos5][LapaRnd]', 'selected' => !empty( $quads_options['pos5']['LapaRnd'] ) ? esc_attr( $quads_options['pos5']['LapaRnd'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( 'right before <strong>the last Paragraph</strong>', 'quick-adsense-reloaded' ) . ' </br>';

   // Pos 6
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos6][Par1Ads]', 'current' => !empty( $quads_options['pos6']['Par1Ads'] ) ? esc_attr( $quads_options['pos6']['Par1Ads'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[pos6][Par1Rnd]', 'name' => 'quads_settings[pos6][Par1Rnd]', 'selected' => !empty( $quads_options['pos6']['Par1Rnd'] ) ? esc_attr( $quads_options['pos6']['Par1Rnd'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' <strong>' . __( 'After Paragraph', 'quick-adsense-reloaded' ) . '</strong> ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'class' => 'quads-paragraph', 'id' => 'quads_settings[pos6][Par1Nup]', 'name' => 'quads_settings[pos6][Par1Nup]', 'selected' => !empty( $quads_options['pos6']['Par1Nup'] ) ? esc_attr( $quads_options['pos6']['Par1Nup'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos6][Par1Con]', 'current' => !empty( $quads_options['pos6']['Par1Con'] ) ? esc_attr( $quads_options['pos6']['Par1Con'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';

   // Pos 7
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos7][Par2Ads]', 'current' => !empty( $quads_options['pos7']['Par2Ads'] ) ? esc_attr( $quads_options['pos7']['Par2Ads'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[pos7][Par2Rnd]', 'name' => 'quads_settings[pos7][Par2Rnd]', 'selected' => !empty( $quads_options['pos7']['Par2Rnd'] ) ? esc_attr( $quads_options['pos7']['Par2Rnd'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' <strong>' . __( 'After Paragraph', 'quick-adsense-reloaded' ) . '</strong> ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[pos7][Par2Nup]', 'name' => 'quads_settings[pos7][Par2Nup]', 'selected' => !empty( $quads_options['pos7']['Par2Nup'] ) ? esc_attr( $quads_options['pos7']['Par2Nup'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos7][Par2Con]', 'current' => !empty( $quads_options['pos7']['Par2Con'] ) ? esc_attr( $quads_options['pos7']['Par2Con'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';

   // Pos 8
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos8][Par3Ads]', 'current' => !empty( $quads_options['pos8']['Par3Ads'] ) ? esc_attr( $quads_options['pos8']['Par3Ads'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[pos8][Par3Rnd]', 'name' => 'quads_settings[pos8][Par3Rnd]', 'selected' => !empty( $quads_options['pos8']['Par3Rnd'] ) ? esc_attr( $quads_options['pos8']['Par3Rnd'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' <strong>' . __( 'After Paragraph', 'quick-adsense-reloaded' ) . '</strong> ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[pos8][Par3Nup]', 'name' => 'quads_settings[pos8][Par3Nup]', 'selected' => !empty( $quads_options['pos8']['Par3Nup'] ) ? esc_attr( $quads_options['pos8']['Par3Nup'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos8][Par3Con]', 'current' => !empty( $quads_options['pos8']['Par3Con'] ) ? esc_attr( $quads_options['pos8']['Par3Con'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';

   $html .= apply_filters( 'quads_extra_paragraph', '' );

   // Pos 9
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos9][Img1Ads]', 'current' => !empty( $quads_options['pos9']['Img1Ads'] ) ? esc_attr( $quads_options['pos9']['Img1Ads'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'name' => 'quads_settings[pos9][Img1Rnd]', 'selected' => !empty( $quads_options['pos9']['Img1Rnd'] ) ? esc_attr( $quads_options['pos9']['Img1Rnd'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' <strong>' . __( 'After Image', 'quick-adsense-reloaded' ) . ' </strong>';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[pos9][Img1Nup]', 'name' => 'quads_settings[pos9][Img1Nup]', 'selected' => !empty( $quads_options['pos9']['Img1Nup'] ) ? esc_attr( $quads_options['pos9']['Img1Nup'] ) : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[pos9][Img1Con]', 'current' => !empty( $quads_options['pos9']['Img1Con'] ) ? esc_attr( $quads_options['pos9']['Img1Con'] ) : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'after <b>Image\'s outer</b><b><span style="font-family:Courier New,Courier,Fixed;"> &lt;div&gt; wp-caption</span></b> if any.', 'quick-adsense-reloaded' ) . ' </br>';

   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
   echo apply_filters( 'quads_ad_position_callback', $html );
}

/**
 * Quicktags Callback
 *
 * Renders quicktags fields
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_quicktags_callback( $args ) {
   global $quads_options, $quads;

   // Quicktags info
   $html = '<div style="margin-bottom:5px;"><strong>Optional: </strong><a href="#" id="quads_insert_ads_action">' . __( ' Insert Ads into a post, on-the-fly', 'quick-adsense-reloaded' ) . '</a></br>' .
           '<ol style="margin-top:5px;display:none;" id="quads_insert_ads_box">
                <li>' . __( 'Insert <span class="quads-quote-docs">&lt;!--Ads1--&gt;</span>, <span class="quads-quote-docs">&lt;!--Ads2--&gt;</span>, etc. into a post to show the <b>Particular Ads</b> at specific location.', 'quick-adsense-reloaded' ) . '</li>
                <li>' . __( 'Insert <span class="quads-quote-docs">&lt;!--RndAds--&gt;</span> into a post to show the <b>Random Ads</b> at specific location', 'quick-adsense-reloaded' ) . '</li>
                </ol></div>';

   $html .= $quads->html->checkbox( array('name' => 'quads_settings[quicktags][QckTags]', 'current' => !empty( $quads_options['quicktags']['QckTags'] ) ? $quads_options['quicktags']['QckTags'] : null, 'class' => 'quads-checkbox') );
   $html .= esc_html__( 'Show Quicktag Buttons on the HTML Post Editor', 'quick-adsense-reloaded' ) . '</br>';
   $html .= '<span class="quads-desc">' . esc_html__( 'Tags can be inserted into a post via the additional Quicktag Buttons at the HTML Edit Post SubPanel.', 'quick-adsense-reloaded' ) . '</span>';
   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
   echo $html;
}

/**
 * Add new ad
 * @global array $quads_options
 */
function quads_ajax_add_ads(){

   check_ajax_referer( 'quads_ajax_nonce', 'nonce' );
   
	if( ! current_user_can( 'manage_options' ) ) { return false; }

   global $quads_options;

   $postCount = !empty($_POST['count']) ? sanitize_text_field( wp_unslash( $_POST['count'] ) ) : 1;


   $count = isset($quads_options['ads']) ? count ($quads_options['ads']) + $postCount : 10 + $postCount;


   $args = array();
   // subtract 10 widget ads
   //$args['id'] = $count-10;
   $args['id'] = $count-quadsGetTotalWidgets();
   $args['name'] = 'Ad ' . $args['id'];

   quads_ajax_add_ads_new($args);

   ob_start();
   // ... get the content ...
   quads_adsense_code_callback( $args );
   $content = ob_get_contents();
   ob_end_clean();

   echo '<tr><td>';
   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
   echo $content;
   echo '</td></tr>';
   die();
}
add_action( 'wp_ajax_quads_ajax_add_ads', 'quads_ajax_add_ads' );

/**
 * Get the total amount of widget ads
 * @global $quads_options $quads_options
 * @return int
 */
function quadsGetTotalWidgets(){
      global $quads_options;

      $i = 0;

      foreach ($quads_options['ads'] as $key => $value){
         if (false !== strpos($key, 'widget')){
            $i++;
         }
      }
      return $i;
}

/**
 * Count normal ads. Do not count widget ads
 *
 * @global array $quads_options
 * @return int
 */
function quads_count_normal_ads() {
   global $quads_options;

   if(!isset($quads_options['ads'])){
      return 0;
   }

   // Count normal ads - not widget ads
   $adsCount = 0;
   $id = 1;
   foreach ( $quads_options['ads'] as $ads => $value ) {
      // Skip if its a widget ad
      if( strpos( $ads, 'ad' . $id ) === 0 && false === strpos( $ads, 'ad' . $id . '_widget' ) ) {
         $adsCount++;
      }
      $id++;
   }
   return $adsCount;
}

function quads_new_ad_callback(){
      if (quads_is_extra()) {
       echo '<a href="#" id="quads-add-new-ad">' . esc_html__('Add New Ad','quick-adsense-reloaded') . '</a>';
      }
}

/**
 * Render all ad relevant settings (ADSENSE CODE tab)
 * No widget ads
 * @global $quads_options $quads_options
 */
function quads_ad_code_callback(){
   global $quads_options;

//   echo '<tr><td>';
//   echo 'test2';
//   echo '</td></tr>';

   $args = array();

   $i = 1;
   // Render 10 default ads if there are less than 10 ads stored or none at all
   if( quads_count_normal_ads() < 10 || !quads_is_extra()) {
      //wp_die('t2');
      while ( $i <= 10 ) {

         $id = $i++;

         $args['id'] = $id;

         $args['desc'] = '';

         $args['name'] = !empty( $quads_options['ads']['ad' . $id]['label'] ) ? $quads_options['ads']['ad' . $id]['label'] : 'Ad ' . $id;

         echo '<tr><td>';
         // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_adsense_code_callback() function
         echo quads_adsense_code_callback( $args );
         echo '</td></tr>';

      }

      // Stop here early
      return true;
   }

   // Else render 10 + n ads
   $i = 1;
   foreach ($quads_options['ads'] as $ads => $value ){

      $id = $i++;

      $args['id'] = $id;

      $args['desc'] = '';

      $args['name'] = !empty($quads_options['ads']['ad' . $id]['label']) ? $quads_options['ads']['ad' . $id]['label'] : 'Ad ' . $id;

      // Skip if its a widget ad
      if ( (strpos($ads, 'ad' . $id) === 0) && (false === strpos($ads, 'ad' . $id . '_widget') ) ){
      echo '<tr><td>';
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_adsense_code_callback() function
      echo quads_adsense_code_callback( $args );
      echo '</td></tr>';
      }

   }
}

/**
 * AdSense Code Callback
 *
 * Renders adsense code fields
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_adsense_code_callback( $args ) {
   global $quads_options;

   $new_label = isset( $quads_options['ads']['ad'.$args['id']]['label'] ) ? $quads_options['ads']['ad'.$args['id']]['label'] : '';

   $label = !empty( $new_label ) ? $new_label : $args['name'];

   $code = isset( $quads_options['ads']['ad'.$args['id']]['code'] ) ? $quads_options['ads']['ad'.$args['id']]['code'] : '';

   $margin = isset( $quads_options['ads']['ad'.$args['id']]['margin'] ) ? esc_attr( stripslashes( $quads_options['ads']['ad'.$args['id']]['margin'] ) ) : 0;

   $g_data_ad_client = isset( $quads_options['ads']['ad'. $args['id']]['g_data_ad_client'] ) ? $quads_options['ads']['ad'. $args['id']]['g_data_ad_client'] : '';

   $g_data_ad_slot = isset( $quads_options['ads']['ad'. $args['id']]['g_data_ad_slot'] ) ? $quads_options['ads']['ad'. $args['id']]['g_data_ad_slot'] : '';

   $g_data_ad_width = isset( $quads_options['ads']['ad'. $args['id']]['g_data_ad_width'] ) ? $quads_options['ads']['ad'. $args['id']]['g_data_ad_width'] : '';

   $g_data_ad_height = isset( $quads_options['ads']['ad'. $args['id']]['g_data_ad_height'] ) ? $quads_options['ads']['ad'. $args['id']]['g_data_ad_height'] : '';

   $id = 'ad' . $args['id'];
   ?>
   <div class="quads-ad-toggle-header quads-box-close" data-box-id="quads-toggle<?php echo esc_attr($id); ?>">
       <div class="quads-toogle-title"><span contenteditable="true" id="quads-ad-label-<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></span><input type="hidden" class="quads-input-label" name="quads_settings[ads][<?php echo esc_attr($id); ?>][label]" value="<?php echo esc_attr($new_label); ?>"></div>
       <a class="quads-toggle" data-box-id="quads-toggle<?php echo esc_attr($id); ?>" href="#"><div class="quads-close-open-icon"></div></a>
   </div>
   <div class="quads-ad-toggle-container" id="quads-toggle<?php echo esc_attr($id); ?>" style="display:none;">
       <div>
   <?php
   $args_ad_type = array(
       'id' => 'ad_type',
       'name' => 'Type',
       'desc' => '',
       'std' => 'plain_text',
       'options' => array(
           'adsense' => 'AdSense',
           'plain_text' => 'Plain Text / HTML / JS'
       )
   );
   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_adtype_callback() function
   echo quads_adtype_callback( $id, $args_ad_type );
   ?>
       </div>
       <textarea style="vertical-align:top;margin-right:20px;" class="large-text quads-textarea" cols="50" rows="10" id="quads_settings[ads][<?php echo esc_attr($id); ?>][code]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][code]"><?php echo esc_textarea( stripslashes( $code ) ); ?></textarea>
       <!--<label for="quads_settings[ads][ <?php //echo $id; ?> ][code]"> <?php //echo $args['desc']; ?></label><br>//-->
       <label for="quads_shortcode_<?php echo esc_attr($args['id']);?>">Post Shortcode:</label><input readonly id="quads_shortcode_<?php echo esc_attr($args['id']);?>" type="text" onclick="this.focus(); this.select()" value='[quads id=<?php echo esc_attr($args['id']);?>]' title="Optional: Copy and paste the shortcode into the post editor, click below then press Ctrl + C (PC) or Cmd + C (Mac).">
       <label for="quads_php_shortcode_<?php echo esc_attr($args['id']);?>">PHP:</label><input readonly id="quads_php_shortcode_<?php echo esc_attr($args['id']);?>" type="text" onclick="this.focus(); this.select()" style="width:290px;" value="&lt;?php echo do_shortcode('[quads id=<?php echo esc_attr($args['id']); ?>]'); ?&gt;" title="<?php echo esc_html('Optional: Copy and paste the PHP code into your theme files, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'quick-adsense-reloaded');?>">
       <br>
       <div class="quads_adsense_code">
           <input type="button" style="vertical-align:inherit;" class="button button-primary quads-add-adsense" value="Copy / Paste AdSense Code"> <span>or add Ad Slot ID & Publisher ID manually below:</span>
           <br />
   <?php //echo __('Generate Ad Slot & Publisher ID automatically from your adsense code', 'quick-adsense-reloaded') ?>
           <label class="quads-label-left" for="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_slot]">Ad Slot ID </label><input type="text" class="quads-medium-size quads-bggrey" id="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_slot]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_slot]" value="<?php echo esc_attr($g_data_ad_slot); ?>">
           <label for="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_client]">Publisher ID</label><input type="text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_client]" class="medium-text quads-bggrey" name="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_client]" value="<?php echo esc_attr($g_data_ad_client); ?>">
           <br />
   <?php
   $args = array(
       'id' => 'adsense_type',
       'name' => 'Type',
       'desc' => 'Type',
       'options' => array(
           'normal' => 'Fixed Size',
           'responsive' => 'Responsive'
       )
   );
   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done in quads_adense_select_callback() function
   echo quads_adense_select_callback( $id, $args );
   ?>
           <?php if( !quads_is_extra() ) { ?>
            <span class="quads-pro-notice" style="display:block;margin-top:20px;">
              <?php echo esc_html__( 'Install', 'quick-adsense-reloaded' ); ?> 
              <a href="http://wpquads.com/?utm_campaign=overlay&utm_source=free-plugin&utm_medium=admin" target="_blank"><?php echo esc_html__( 'WP QUADS PRO','quick-adsense-reloaded' ); ?>
              </a> 
              <?php echo esc_html__( 'to fully support AdSense Responsive ads.', 'quick-adsense-reloaded' ); ?>
            </span>
           <?php } ?>
           <br />
           <label class="quads-label-left quads-type-normal" for="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_width]"><?php echo esc_html__( 'Width', 'quick-adsense-reloaded' )?> </label><input type="number" step="1" id="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_width]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_width]" class="small-text quads-type-normal" value="<?php echo esc_attr($g_data_ad_width); ?>">
           <label class="quads-type-normal" for="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_height]"><?php echo esc_html__( 'Height', 'quick-adsense-reloaded' )?> </label><input type="number" step="1" id="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_height]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_height]" class="small-text quads-type-normal" value="<?php echo esc_attr($g_data_ad_height); ?>">
       </div>
       <div class="quads-style">
           <h3> <?php echo esc_html__( 'Layout', 'quick-adsense-reloaded' )?></h3>
   <?php
   $args_ad_position = array(
       'id' => 'align',
       'name' => 'align',
       'desc' => 'align',
       'std' => '3',
       'options' => array(
           '3' => 'Default',
           '0' => 'Left',
           '1' => 'Center',
           '2' => 'Right'
       )
   );
   echo quads_adposition_callback( $id, $args_ad_position ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself
   // if WP QUADS PRO is installed and version number is higher or equal 1.2.7 show the new margin settings
   if( !quads_is_extra() ) {
      ?>

              <br />
              <label class="quads-label-left" for="quads_settings[ads][<?php echo esc_attr($id); ?>][margin]"><?php esc_html_e( 'Margin', 'quick-adsense-reloaded' ); ?></label>
              <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][margin]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][margin]" value="<?php echo esc_attr( stripslashes( $margin ) ); ?>"/><?php echo esc_html__( 'px', 'quick-adsense-reloaded' );?>
   <?php } echo apply_filters( 'quads_render_margin', '', esc_attr($id) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself ?>
       </div>
           <?php
           if (quads_is_extra()){
               echo apply_filters( 'quads_advanced_settings', '', esc_attr($id) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself
           }
           echo quads_pro_overlay(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself
           ?>
   </div>
       <?php
    }

    /**
     * AdSense Code Widget Callback
     *
     * Renders adsense code fields
     *
     * @since 0.9.0
     * @param array $args Arguments passed by the setting
     * @global $quads_options Array of all the QUADS Options
     * @return void
     */
    function quads_adsense_widget_callback( $args ) {
       global $quads_options;

       $label = !empty( $args['name'] ) ? $args['name'] : '';

       $code = isset( $quads_options['ads'][$args['id']]['code'] ) ? $quads_options['ads'][$args['id']]['code'] : '';

       $margin = isset( $quads_options['ads'][$args['id']]['margin'] ) ? esc_attr( stripslashes( $quads_options['ads'][$args['id']]['margin'] ) ) : 0;
       $margintop   = 0;
      $marginright  = 0;
      $marginbottom = 0;
      $marginleft   = 0;
      if(isset( $quads_options['ads'][$args['id']]['margin'] )){
         $margintop     = esc_attr( stripslashes($quads_options['ads'][$args['id']]['margin']));
         $marginright   = esc_attr( stripslashes($quads_options['ads'][$args['id']]['margin']));
         $marginbottom  = esc_attr( stripslashes($quads_options['ads'][$args['id']]['margin']));
         $marginleft    = esc_attr( stripslashes($quads_options['ads'][$args['id']]['margin']));
       }
       if(isset( $quads_options['ads'][$args['id']]['margintop'] )){
         $margintop = esc_attr( stripslashes($quads_options['ads'][$args['id']]['margintop']));
       }
       if(isset( $quads_options['ads'][$args['id']]['marginright'] )){
         $marginright = esc_attr( stripslashes($quads_options['ads'][$args['id']]['marginright']));
       }
       if(isset( $quads_options['ads'][$args['id']]['marginbottom'] )){
         $marginbottom = esc_attr( stripslashes($quads_options['ads'][$args['id']]['marginbottom']));
       }
       if(isset( $quads_options['ads'][$args['id']]['marginleft'] )){
         $marginleft = esc_attr( stripslashes($quads_options['ads'][$args['id']]['marginleft']));
       }
       // padding
      $paddingtop   = 0;
      $paddingright  = 0;
      $paddingbottom = 0;
      $paddingleft   = 0;

       if(isset( $quads_options['ads'][$args['id']]['paddingtop'] )){
         $paddingtop = esc_attr( stripslashes($quads_options['ads'][$args['id']]['paddingtop']));
       }
       if(isset( $quads_options['ads'][$args['id']]['paddingright'] )){
         $paddingright = esc_attr( stripslashes($quads_options['ads'][$args['id']]['paddingright']));
       }
       if(isset( $quads_options['ads'][$args['id']]['paddingbottom'] )){
         $paddingbottom = esc_attr( stripslashes($quads_options['ads'][$args['id']]['paddingbottom']));
       }
       if(isset( $quads_options['ads'][$args['id']]['paddingleft'] )){
         $paddingleft = esc_attr( stripslashes($quads_options['ads'][$args['id']]['paddingleft']));
       }

       $g_data_ad_client = isset( $quads_options['ads'][$args['id']]['g_data_ad_client'] ) ? $quads_options['ads'][$args['id']]['g_data_ad_client'] : '';

       $g_data_ad_slot = isset( $quads_options['ads'][$args['id']]['g_data_ad_slot'] ) ? $quads_options['ads'][$args['id']]['g_data_ad_slot'] : '';

       $g_data_ad_width = isset( $quads_options['ads'][$args['id']]['g_data_ad_width'] ) ? $quads_options['ads'][$args['id']]['g_data_ad_width'] : '';

       $g_data_ad_height = isset( $quads_options['ads'][$args['id']]['g_data_ad_height'] ) ? $quads_options['ads'][$args['id']]['g_data_ad_height'] : '';

       // Create a shorter var to make HTML cleaner
       $id = $args['id']; //xss ok
       ?>
   <div class="quads-ad-toggle-header quads-box-close" data-box-id="quads-toggle<?php echo esc_attr($id); ?>">
       <div class="quads-toogle-title"><?php echo esc_html($label); ?></div>
       <a class="quads-toggle" data-box-id="quads-toggle<?php echo esc_attr($id); ?>" href="#"><div class="quads-close-open-icon"></div></a>
   </div>
   <div class="quads-ad-toggle-container" id="quads-toggle<?php echo esc_attr($id); ?>" style="display:none;">
       <div>
   <?php
   $args_ad_type = array(
       'id' => 'ad_type',
       'name' => 'Type',
       'desc' => '',
       'std' => 'plain_text',
       'options' => array(
           'adsense' => 'AdSense',
           'plain_text' => 'Plain Text / HTML / JS'
       )
   );
   echo quads_adtype_callback( $id, $args_ad_type ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself
   ?>
       </div>
       <textarea style="vertical-align:top;margin-right:20px;" class="large-text quads-textarea" cols="50" rows="10" id="quads_settings[ads][<?php echo esc_attr($id); ?>][code]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][code]"><?php echo esc_textarea( stripslashes( $code ) ); ?></textarea><label for="quads_settings[ads][ <?php echo esc_attr($id); ?> ][code]"> <?php echo wp_kses_post($args['desc']); ?>></label>
       <br>
       <div class="quads_adsense_code">
           <input type="button" style="vertical-align:inherit;" class="button button-primary quads-add-adsense" value="<?php echo esc_html__( 'Copy / Paste AdSense Code', 'quick-adsense-reloaded' ); ?>"> <span><?php echo esc_html__( '_or add Ad Slot ID & Publisher ID manually below:', 'quick-adsense-reloaded'); ?></span>
           <br />
   <?php //echo __('Generate Ad Slot & Publisher ID automatically from your adsense code', 'quick-adsense-reloaded') ?>
           <label class="quads-label-left" for="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_slot]">Ad Slot ID </label><input type="text" class="quads-medium-size quads-bggrey" id="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_slot]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_slot]" value="<?php echo esc_attr($g_data_ad_slot); ?>">
           <label for="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_client]">Publisher ID</label><input type="text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_client]" class="medium-text quads-bggrey" name="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_client]" value="<?php echo esc_attr($g_data_ad_client); ?>">
           <br />
   <?php
   $args_adsense_type = array(
       'id' => 'adsense_type',
       'name' => 'Type',
       'desc' => 'Type',
       'options' => array(
           'normal' => 'Fixed Size',
           'responsive' => 'Responsive'
       )
   );
   echo quads_adense_select_callback( $id, $args_adsense_type ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself
   ?>
           <?php if( !quads_is_extra() ) { ?>
            <span class="quads-pro-notice" style="display:block;margin-top:20px;"><?php echo esc_html__( 'Install WP QUADS PRO to fully support AdSense Responsive ads.', 'quick-adsense-reloaded' ) . '<a href="' . esc_url( 'http://wpquads.com/?utm_campaign=overlay&utm_source=free-plugin&utm_medium=admin' ) . '" target="_blank">' .esc_html__( 'WP QUADS PRO', 'quick-adsense-reloaded' ) . '</a>';?></span>
           <?php } ?>
           <br />
           <label class="quads-label-left quads-type-normal" for="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_width]">Width </label><input type="number" step="1" id="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_width]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_width]" class="small-text quads-type-normal" value="<?php echo esc_attr($g_data_ad_width); ?>">
           <label class="quads-type-normal" for="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_height]">Height </label><input type="number" step="1" id="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_height]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][g_data_ad_height]" class="small-text quads-type-normal" value="<?php echo esc_attr($g_data_ad_height); ?>">
       </div>
       <div class="quads-style">
           <h3><?php esc_html__('Layout' ,'quick-adsense-reloaded')?></h3>
   <?php
   $args_ad_position = array(
       'id' => 'align',
       'name' => 'align',
       'desc' => 'align',
       'std' => '3',
       'options' => array(
           '3' => 'Default',
           '0' => 'Left',
           '1' => 'Center',
           '2' => 'Right'
       )
   );
   echo quads_adposition_callback( $id, $args_ad_position ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself
   // if WP QUADS PRO is installed and version number is higher or equal 1.2.7 show the new margin settings
   if( !quads_is_extra() ) {
      ?>
              <br />
              <label class="quads-label-left" ><?php esc_html_e( 'Margin', 'quick-adsense-reloaded' ); ?></label>
              <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][margintop]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][margintop]" value="<?php echo esc_attr( stripslashes( $margintop ) ); ?>"/>
              <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][marginright]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][marginright]" value="<?php echo esc_attr( stripslashes( $marginright ) ); ?>"/>
              <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][marginbottom]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][marginbottom]" value="<?php echo esc_attr( stripslashes( $marginbottom ) ); ?>"/>
              <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][marginleft]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][marginleft]" value="<?php echo esc_attr( stripslashes( $marginleft ) ); ?>"/>px
              <br />
              <label class="quads-label-left" ><?php esc_html_e( 'Padding', 'quick-adsense-reloaded' ); ?></label>
              <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][paddingtop]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][paddingtop]" value="<?php echo esc_attr( stripslashes( $paddingtop ) ); ?>"/>
              <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][paddingright]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][paddingright]" value="<?php echo esc_attr( stripslashes( $paddingright ) ); ?>"/>
              <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][paddingbottom]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][paddingbottom]" value="<?php echo esc_attr( stripslashes( $paddingbottom ) ); ?>"/>
              <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo esc_attr($id); ?>][paddingleft]" name="quads_settings[ads][<?php echo esc_attr($id); ?>][paddingleft]" value="<?php echo esc_attr( stripslashes( $paddingleft ) ); ?>"/>px

   <?php } echo apply_filters( 'quads_render_margin', '', esc_attr($id) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself ?>
       </div>
           <?php
           if (quads_is_extra()){
           echo apply_filters( 'quads_advanced_settings', '', esc_attr($id) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself
           }
           echo quads_pro_overlay(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Output is escaped in the function itself
           ?>
   </div>
       <?php
    }

    /**
     * If advanced settings are not available load overlay image
     * @return string
     */
    function quads_pro_overlay() {
       if( quads_is_extra() ) {
          return '';
       }

       $html = '<div class="quads-advanced-ad-box quads-pro-overlay"><a href="'.esc_url('http://wpquads.com/?utm_campaign=overlay&utm_source=free-plugin&utm_medium=admin').'" target="_blank"><img src="' . esc_url(QUADS_PLUGIN_URL.'/assets/images/get_pro_overlay.png') . '"></a></div>';

       return $html;
    }

    /**
     *
     * Return array of alignment options
     *
     * @return array
     */
    function quads_get_alignment() {
       // Do not change the key => value order for compatibility reasons
       return array(
           3 => 'none',
           0 => 'left',
           1 => 'center',
           2 => 'right',
       );
    }

    /**
     * Check if plugin Clickfraud Monitoring is installed
     *
     * @return boolean true when it is installed and active
     */
    function quads_is_installed_clickfraud() {
       $plugin_file = 'cfmonitor/cfmonitor.php';
       $plugin_file2 = 'clickfraud-monitoring/cfmonitor.php';

       if( is_plugin_active( $plugin_file ) || is_plugin_active( $plugin_file2 ) ) {
          return true;
       }

       return false;
    }

    /**
     *
     * @param array $args array(
     * 'id' => 'string),
     * 'type' => desktop, tablet_landscape, tablet_portrait, phone
     * @return string

     */
    function quads_render_size_option( $args ) {
       global $quads_options;

       if( !isset( $args['id'] ) ) {
          return '';
       }

       $checked = isset( $quads_options['ads'][$args['id']][$args['type']] ) ? $quads_options['ads'][$args['id']][$args['type']] : '';
       $html = '<div class="quads-select-style-overwrite">';
       $html .= '<select class="quads-size-input" id="quads_settings[ads][' . esc_attr($args['id']) . '][' . esc_attr($args['type']) . ']" name="quads_settings[ads][' . esc_attr($args['id']) . '][' . esc_attr($args['type']) . ']">';
       foreach ( quads_get_adsense_sizes() as $key => $value ) :
          $selected = selected( $key, $checked, false );
          $html .= '<option value="' . esc_attr($key) . '" ' . esc_attr($selected) . '>' . wp_kses_post($value) . '</option>';
       endforeach;
       $html .= '</select>';
       $html .= '</div>';

       return $html;
    }

    /**
     * Get all AdSense Sizes
     * @return array
     */
    function quads_get_adsense_sizes() {
       $sizes = array(
           'Auto' => 'Auto',
           '120 x 90' => '120 x 90',
           '120 x 240' => '120 x 240',
           '120 x 600' => '120 x 600',
           '125 x 125' => '125 x 125',
           '160 x 90' => '160 x 90',
           '160 x 600' => '160 x 600',
           '180 x 90' => '180 x 90',
           '180 x 150' => '180 x 150',
           '200 x 90' => '200 x 90',
           '200 x 200' => '200 x 200',
           '234 x 60' => '234 x 60',
           '250 x 250' => '250 x 250',
           '320 x 100' => '320 x 100',
           '300 x 250' => '300 x 250',
           '300 x 600' => '300 x 600',
           '300 x 1050' => '300 x 1050',
           '320 x 50' => '320 x 50',
           '336 x 280' => '336 x 280',
           '360 x 300' => '360 x 300',
           '435 x 300' => '435 x 300',
           '468 x 15' => '468 x 15',
           '468 x 60' => '468 x 60',
           '640 x 165' => '640 x 165',
           '640 x 190' => '640 x 190',
           '640 x 300' => '640 x 300',
           '728 x 15' => '728 x 15',
           '728 x 90' => '728 x 90',
           '970 x 90' => '970 x 90',
           '970 x 250' => '970 x 250',
           '240 x 400' => '240 x 400 - Regional ad sizes',
           '250 x 360' => '250 x 360 - Regional ad sizes',
           '580 x 400' => '580 x 400 - Regional ad sizes',
           '750 x 100' => '750 x 100 - Regional ad sizes',
           '750 x 200' => '750 x 200 - Regional ad sizes',
           '750 x 300' => '750 x 300 - Regional ad sizes',
           '980 x 120' => '980 x 120 - Regional ad sizes',
           '930 x 180' => '930 x 180 - Regional ad sizes',
       );

       return apply_filters( 'quads_adsense_size_formats', $sizes );
    }

    /**
     * Store AdSense parameters
     *
     * @return boolean
     */
   function quads_store_adsense_args() {
   global $quads_options;

   foreach ( $quads_options as $id => $ads ) {
      if (!is_array($ads)){
         continue;
      }
      foreach ($ads as $key => $value) {
         if( is_array( $value ) && array_key_exists( 'code', $value ) && !empty( $value['code'] ) ) {

            //check to see if it is google ad
            if( preg_match( '/googlesyndication.com/', $value['code'] ) ) {

               // Test if its google asyncron ad
               if( preg_match( '/data-ad-client=/', $value['code'] ) ) {
                  //*** GOOGLE ASYNCRON *************
                  $quads_options['ads'][$key]['current_ad_type'] = 'google_async';
                  //get g_data_ad_client
                  $explode_ad_code = explode( 'data-ad-client', $value['code'] );
                  if(isset($explode_ad_code[1])){
                    preg_match( '#"([a-zA-Z0-9-\s]+)"#', $explode_ad_code[1], $matches_add_client );
                  }
                  if(isset($matches_add_client[1])){
                    $quads_options['ads'][$key]['g_data_ad_client'] = str_replace( array('"', ' '), array(''), $matches_add_client[1] );
                  }
                  //get g_data_ad_slot
                  $explode_ad_code = explode( 'data-ad-slot', $value['code'] );
                  if(isset($explode_ad_code[1])){
                    preg_match( '#"([a-zA-Z0-9/\s]+)"#', $explode_ad_code[1], $matches_add_slot );
                  }
                  if (isset($matches_add_slot[1])){
                  $quads_options['ads'][$key]['g_data_ad_slot'] = str_replace( array('"', ' '), array(''), $matches_add_slot[1] );
                  }
               } else {
                  //*** GOOGLE SYNCRON *************
                  $quads_options['ads'][$key]['current_ad_type'] = 'google_sync';
                  //get g_data_ad_client
                  $explode_ad_code = explode( 'google_ad_client', $value['code'] );
                  preg_match( '#"([a-zA-Z0-9-\s]+)"#', $explode_ad_code[1], $matches_add_client );
                  $quads_options['ads'][$key]['g_data_ad_client'] = str_replace( array('"', ' '), array(''), $matches_add_client[1] );

                  //get g_data_ad_slot
                  $explode_ad_code = explode( 'google_ad_slot', $value['code'] );
                  preg_match( '#"([a-zA-Z0-9/\s]+)"#', isset($explode_ad_code[1]) ? $explode_ad_code[1] : null, $matches_add_slot );
                  $quads_options['ads'][$key]['g_data_ad_slot'] = str_replace( array('"', ' '), array(''), isset($matches_add_slot[1]) ? $matches_add_slot[1] : null  );
               }
            }
         }
      }
   }
   update_option( 'quads_settings', $quads_options );
}
    /**
     * Sanitizes a string key for QUADS Settings
     *
     * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are allowed
     *
     * @since  2.0.0
     * @param  string $key String key
     * @return string Sanitized key
     */
    function quads_sanitize_key( $key ) {
       $raw_key = $key;
       $key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
       /**
        * Filter a sanitized key string.
        *
        * @since 2.5.8
        * @param string $key     Sanitized key.
        * @param string $raw_key The key prior to sanitization.
        */
       return apply_filters( 'quads_sanitize_key', $key, $raw_key );
    }

    /**
     * Multi Select Callback
     *
     * @since 1.3.8
     * @param array $args Arguments passed by the settings
     * @global $quads_options Array of all the QUADS Options
     * @return string $output dropdown
     */
    function quads_multiselect_callback( $args = array() ) {
       global $quads_options;

       $placeholder = !empty( $args['placeholder'] ) ? $args['placeholder'] : '';
       $selected = isset( $quads_options[$args['id']] ) ? $quads_options[$args['id']] : '';
       $checked = '';

       echo '<select id="quads_select_'. esc_attr($args['id']) .'" name="quads_settings[' . esc_attr($args['id']) . '][]" data-placeholder="' . esc_attr($placeholder) . '" style="width:550px;" multiple tabindex="4" class="quads-select quads-chosen-select">';
       $i = 0;
       foreach ( $args['options'] as $key => $value ) :
          if( is_array( $selected ) ) {
             $checked = selected( true, in_array( $key, $selected ), false );
          }
          echo '<option value="' . esc_attr($key) . '" ' . esc_attr($checked) . '>' . esc_attr($value) . '</option>';
       endforeach;
       echo '</select>';
    }
    /**
     * Multi Select Ajax Callback
     * This adds only active elements to the array. Useful if there are a lot of elements like tags to increase performance
     *
     * @since 1.3.8
     * @param array $args Arguments passed by the settings
     * @global $quads_options Array of all the QUADS Options
     * @return string $output dropdown
     */
    function quads_multiselect_ajax_callback( $args = array() ) {
       global $quads_options;

       $placeholder = !empty( $args['placeholder'] ) ? $args['placeholder'] : '';
       $selected = isset( $quads_options[$args['id']] ) ? $quads_options[$args['id']] : '';
       $checked = '';

       echo '<select id="quads_select_'. esc_attr($args['id']) .'" name="quads_settings[' . esc_attr($args['id']) . '][]" data-placeholder="' . esc_attr($placeholder) . '" style="width:550px;" multiple tabindex="4" class="quads-select quads-chosen-select">';
       $i = 0;

       if (!isset($quads_options[$args['id']]) || !is_array( $quads_options[$args['id']] ) || count($quads_options[$args['id']]) == 0){
            echo '</select>';
            return;
       }

       foreach ( $quads_options[$args['id']] as $key => $value ) {
          echo '<option value="' . esc_attr($key) . '" selected="selected">' . esc_attr($value) . '</option>';
       }
       echo '</select>';
    }

/**
 * Create ads.txt for Google AdSense when saving settings
 * @return boolean
 */
    function quads_write_adsense_ads_txt() {
        // Get the current recently updated settings
        $quads_options = get_option('quads_settings');

        // ads.txt is disabled
        if (!isset($quads_options['adsTxtEnabled'])) {
            set_transient('quads_ads_txt_disabled', true, 100);
            delete_transient('quads_ads_txt_error');
            return false;
        }

        // Create AdSense ads.txt entries
        $adsense = new \wpquads\adsense($quads_options);
        if ($adsense->writeAdsTxt()){
            return true;
        } else {
            // Make sure an error message is shown when ads.txt is available but can not be modified
            // Otherwise google adsense ads are not shown
            if (is_file(ABSPATH . 'ads.txt')) {
                set_transient('quads_ads_txt_error', 'true', 3000);
            }
            return false;
        }
    }
    add_action('update_option_quads_settings', 'quads_write_adsense_ads_txt');


    /**
     * Periodically update ads.txt once a day for vi and adsense
     * This is to ensure that the file is recreated in case it was deleted
     * @return boolean
     */
   function updateAdsTxt(){
       global $quads, $quads_options;
        if(is_file('ads.txt') || !isset($quads_options['adsTxtEnabled'])){
            return false;
        }
        $adsense = new wpquads\adsense($quads_options);
        $adsense->writeAdsTxt();
    }
 add_action('quads_daily_event', 'updateAdsTxt');

 // Start 2.0 code from here //

 function quads_ajax_add_ads_new($args){

   if($args['id']){

      $parameters = array();
      $parameters['quads_post_meta']['ad_type'] = 'adsense';
      $parameters['quads_post_meta']['label']   = $args['name'];
      $parameters['quads_post_meta']['quads_ad_old_id'] = 'ad'.$args['id'];

      $api_service =   new QUADS_Ad_Setup_Api_Service();
      $api_service->updateAdData($parameters);

   }

 }

 // 2.0 code end here

/**
 * We are adding extra fields for user profile
 * @param type $user
 */
function quads_extra_user_profile_fields( $user ) {
    ?>
    <h3><?php esc_html_e("WPQuads Revenue Sharing", 'quick-adsense-reloaded'); ?></h3>

    <table class="form-table">
    <tr>
        <th><label for="quads-data-client-id"><?php esc_html_e("AdSense Publisher ID",'quick-adsense-reloaded'); ?></label></th>
        <td>
            <input placeholder="ca-pub-2005XXXXXXXXX342" type="text" name="quads_adsense_pub_id" id="quads_adsense_pub_id" value="<?php echo esc_attr( get_the_author_meta( 'quads_adsense_pub_id', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php esc_html_e("Please enter your pub ID.", 'quick-adsense-reloaded'); ?></span>
        </td>
    </tr>
    </table>
<?php
}
add_action( 'show_user_profile', 'quads_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'quads_extra_user_profile_fields' );

/**
 * we are saving user extra fields data in database
 * @param type $user_id
 * @return boolean
 */
function quads_save_extra_user_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason: We are not processing form information just updating the user meta
    $adsense_pub_id = isset($_POST['quads_adsense_pub_id']) ? sanitize_text_field( wp_unslash( $_POST['quads_adsense_pub_id'] ) ) : '';
    update_user_meta( $user_id, 'quads_adsense_pub_id', $adsense_pub_id );
}

add_action( 'personal_options_update', 'quads_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'quads_save_extra_user_profile_fields' );

function wp_quads_quick_tag() {
   if ( wp_script_is( 'quicktags' ) ) {
   ?>
<script language="javascript" type="text/javascript">
      ( function() {
         jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				'action': 'wpquads_ads_for_shortcode_data',
				'wpquads_security_nonce' :quads.nonce
			}
         
		}, 'json' )
		.done( function( data, textStatus, jqXHR ) {
         dataObj = JSON.parse(data);
         dataObj.forEach( ad_data => {
         var ad_id = ad_data.replace('[',' ').replace(']','').replace('"','').replace('"','').replace('ad','')
         //QTags.addButton( ad_data , ad_data, "[quads id="+ad_id+"]", '', '' );
         QTags.addButton( ad_data , ad_data, "<!--Ads"+ad_id+"-->", '', '' );
         });
		} )
} )();
	</script>
<?php
   }
}
$quads_mode = get_option('quads-mode') ? get_option('quads-mode') : '' ;
if( isset($quads_mode) && $quads_mode == "old" ) {
   add_action( 'admin_print_footer_scripts', 'wp_quads_quick_tag', 100 );
}
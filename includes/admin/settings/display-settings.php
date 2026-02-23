<?php
/**
 * Admin Options Page
 *
 * @package     QUADS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Returns list elements for jQuery tab navigation 
 * based on header callback
 * 
 * @since 2.1.2
 * @todo Use sprintf to sanitize  $field['id'] instead using str_replace() Should be much faster? 
 * @return string
 */

function quads_get_tab_header($page, $section){
    global $quads_options;
    global $wp_settings_fields;
    
    if (!isset($wp_settings_fields[$page][$section]))
        return;
    
    echo '<ul>';
    foreach ((array) $wp_settings_fields[$page][$section] as $field) {  
    $sanitizedID = str_replace('[', '', $field['id'] );
    $sanitizedID = str_replace(']', '', $sanitizedID );     
     if ( strpos($field['callback'],'header') !== false && !quads_is_excluded(array('help', 'licenses') ) ) { 
         echo '<li class="quads-tabs"><a href="#' . esc_attr($sanitizedID) . '">' . esc_attr($field['title']) .'</a></li>';
     }
    }
    echo '</ul>';
}

/**
 * Check if current page is excluded
 * 
 * @param array $pages
 * @return boolean
 */
function quads_is_excluded($pages){
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but checking if current page is excluded
    if (isset($_GET['tab'])){
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but checking if current page is excluded
        $currentpage = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
        if (isset($currentpage) && in_array($currentpage, $pages))
                return true;
    }
}

/**
 * Print out the settings fields for a particular settings section
 *
 * Part of the Settings API. Use this in a settings page to output
 * a specific section. Should normally be called by do_settings_sections()
 * rather than directly.
 *
 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
 * @return string
 *
 * @since 2.1.2
 *
 * @param string $page Slug title of the admin page who's settings fields you want to show.
 * @param section $section Slug title of the settings section who's fields you want to show.
 * 
 * Copied from WP Core 4.0 /wp-admin/includes/template.php do_settings_fields()
 * We use our own function to be able to create jQuery tabs with easytabs()
 * 
*  We dont use tables here any longer. Are we stuck in the nineties?
 * @todo Use sprintf to sanitize  $field['id'] instead using str_replace() Should be faster?
 * @todo some media queries for better responisbility
 */
function quads_do_settings_fields($page, $section) {
    global $wp_settings_fields;
    $header = false;
    $firstHeader = false;
    
    if (!isset($wp_settings_fields[$page][$section]))
        return;
    
    // Check first if any callback header registered
    foreach ((array) $wp_settings_fields[$page][$section] as $field) {
       strpos($field['callback'],'header') !== false ? $header = true : $header = false; 
       
       if ($header === true)
               break;
    }
    
    foreach ((array) $wp_settings_fields[$page][$section] as $field) {
       $sanitizedID = str_replace('[', '', $field['id'] );
       $sanitizedID = str_replace(']', '', $sanitizedID );
       
       // Check if header has been created previously
       if (strpos($field['callback'],'header') !== false && $firstHeader === false) { 
           
           echo '<div id="' .esc_attr($sanitizedID)  . '">'; 
           echo '<table class="quads-form-table"><tbody>';
           $firstHeader = true;
           
       } elseif (strpos($field['callback'],'header') !== false && $firstHeader === true) { 
       // Header has been created previously so we have to close the first opened div
           echo '</table></div><div id="' . esc_attr($sanitizedID) . '">'; 
           echo '<table class="quads-form-table"><tbody>';  
       }  
        
        if (!empty($field['args']['label_for']) && !quads_is_excluded_title( $field['args']['id'] )){
            echo '<tr class="quads-row">';
            echo '<td class="quads-row th">';
            echo '<label for="' . esc_attr($field['args']['label_for']) . '">' . esc_attr($field['title']) . '</label>';
            echo '</td></tr>';
        }else if (!empty($field['title']) && !empty($field['args']['helper-desc']) && !quads_is_excluded_title( $field['args']['id'] ) ){
            echo '<tr class="quads-row">';
            echo '<td class="quads-row th">';//xss ok
            echo '<div class="col-title">' . esc_attr($field['title']) . '<a class="quads-general-helper" href="#"></a><div class="quads-message">' . esc_html($field['args']['helper-desc']). '</div></div>';
            echo '</td></tr>';
        }else if (!empty($field['title']) && !empty($field['args']['id']) && !quads_is_excluded_title( $field['args']['id'] ) ){
            echo '<tr class="quads-row">';
            echo '<td class="quads-row th">'; //xss ok
            echo '<div class="col-title" id="'.esc_attr($field['args']['id']).'">' . esc_attr($field['title']) . '</div>';
            echo '</td></tr>';
        }
        
        else {
            echo '';
        }

        
        echo '<tr><td>';
            call_user_func($field['callback'], $field['args']);
        echo '</td></tr>';  
    }
    echo '</tbody></table>';
    if ($header === true){
    echo '</div>';
    }
}

/**
 * If title is one of these entries do not show it
 */
function quads_is_excluded_title($string){
    $haystack = array('ad1','ad2','ad3','ad4','ad5','ad6','ad7','ad8','ad9','ad10', 
        'ad1_widget',
        'ad2_widget',
        'ad3_widget',
        'ad4_widget',
        'ad5_widget',
        'ad6_widget',
        'ad7_widget',
        'ad8_widget',
        'ad9_widget',
        'ad10_widget',
        'vi_header',
        'vi_signup'
        );

    if (in_array($string, $haystack)){
            return true;
    }
    return false;
}



/**
 * Options Page New
 *
 * Renders the options page contents.
 *
 * @since 2.0
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_options_page_new() {

        global $quads_options;    
        // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
        wp_enqueue_style('quads-admin-ad-style', QUADS_PLUGIN_URL.'admin/assets/js/dist/style.css');
        // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
        wp_enqueue_style('quads-material-ui-font', 'https://fonts.googleapis.com/icon?family=Material+Icons');
        
        $licenses = get_option( 'quads_wp_quads_pro_license_active' );
        if (!$licenses) {
            $quads_settings = get_option('quads_settings');
            if (isset($quads_settings['quads_wp_quads_pro_license_key']) && !empty($quads_settings['quads_wp_quads_pro_license_key']) && strpos($quads_settings['quads_wp_quads_pro_license_key'], '****************') === false) {
                // Call API to check license
                $item_shortname = 'quads_wp_quads_pro';
                $item_name = 'WP QUADS PRO';
                $license = sanitize_text_field($quads_settings['quads_wp_quads_pro_license_key']);
                $api_params = array(
                    'edd_action' => 'check_license',
                    'license'    => $license,
                    'item_name'  => urlencode($item_name),
                    'url'        => home_url()
                );
                $response = wp_remote_post(
                    'http://wpquads.com/edd-sl-api/',
                    array(
                        'timeout'   => 15,
                        'sslverify' => false,
                        'body'      => $api_params
                    )
                );
                if (is_wp_error($response)) {
                    $licenses = (object) array('license' => 'invalid', 'error' => 'connection_error');
                } else {
                    $license_data = json_decode(wp_remote_retrieve_body($response));
                    if ($license_data) {
                        update_option('quads_wp_quads_pro_license_active', $license_data);
                        $licenses = $license_data;
                    } else {
                        $licenses = (object) array('license' => 'invalid', 'error' => 'invalid_response');
                    }
                }
            } else {
                $licenses = (object) array(
                    'license' => 'invalid',
                    'error' => '',
                    'expires' => '',
                    'price_id' => '',
                    'activations_left' => '',
                    'checksum' => '',
                    'customer_email' => '',
                    'customer_name' => '',
                    'item_name' => '',
                    'license_limit' => '',
                    'payment_id' => '',
                    'site_count' => '',
                    'success' => false
                );
            }
        }
        if (isset($licenses->expires)) {
        $license_exp = gmdate('Y-m-d', strtotime($licenses->expires));
        $license_exp_d = gmdate('d F Y', strtotime($licenses->expires));
        $license_info_lifetime = $licenses->expires;

        // if (isset($licenses->expires)) {
        // $licenses->expires = $license_exp_d;
        // }

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

        if(isset($licenses->price_id)){
            $licenses->price_id = $days;
        }        
        if(isset($licenses->expires)){
            $licenses->expires = $days;
        }        
        // print_r($licenses->price_id);die;       
        }
        $get_ip =  get_option('add_blocked_ip') ?  get_option('add_blocked_ip')  : 0 ;
        $get_e_p_p_p = '20';
        if(is_user_logged_in()){
            $current_user = get_current_user_id();
        }
        if( is_user_logged_in() && $current_user){
        $user_info = get_user_meta($current_user);
        if( isset($user_info['edit_post_per_page']) ){
            $get_specific_user_meta = $user_info['edit_post_per_page'];
            // get edit_post_per_page
            $get_e_p_p_p = implode("",$get_specific_user_meta);
        }
        else{
            $get_specific_user_meta = '20' ;
        }
    }
        $ajax_call = admin_url( 'admin-ajax.php' );
        $get_admin_url = admin_url('admin.php');
        $get_activated_data = is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? is_plugin_active('sitepress-multilingual-cms/sitepress.php') : 0 ;
        $quads_settings = get_option('quads_settings');
        $currency = isset($quads_settings['currency']) ? $quads_settings['currency'] : 'USD';
        $sellable_ads = isset($quads_settings['sellable_ads']) ? $quads_settings['sellable_ads'] : 1;
        $disableads = isset($quads_settings['disableads']) ? $quads_settings['disableads'] : 0;
        $is_polylang_activated = is_plugin_active('polylang/polylang.php') ? is_plugin_active('polylang/polylang.php') : 0 ;
        $pll_languages = [];

        // Add active languages added in polylang plugin
        if ( function_exists( 'pll_languages_list' ) ) {
            $languages = PLL()->model->get_languages_list();
            if ( ! empty( $languages ) && is_array( $languages ) ) {
                foreach ( $languages as $language ) {
                    if ( is_object( $language ) && ! empty( $language->name ) ) {
                        $pll_languages[]     =   array(
                                                    "value" => $language->slug, 
                                                     "label" => $language->name  
                                                );
                    }
                }
            }

        }

        $data = array(
            'quads_plugin_url'     => QUADS_PLUGIN_URL,
            'rest_url'             => esc_url_raw( rest_url() ),
            'nonce'                => wp_create_nonce( 'wp_rest' ),
            'licenses'             => $licenses,
            'is_amp_enable'        => function_exists('is_amp_endpoint') ? true : false,
            'is_bbpress_exist'     => class_exists( 'bbPress' )? true : false,
            'is_newsPapertheme_exist'     => class_exists( 'tagdiv_config' )? true : false,
            'quads_get_ips'     => $get_ip,
            'ajax_url' => $ajax_call,
            'num_of_ads_to_display' => $get_e_p_p_p,
            'get_admin_url' => $get_admin_url,
            'wpml_activation' => $get_activated_data,
            'user_roles'=>quads_get_current_user_roles(),
            'currency' => $currency,
            'sellable_ads' => $sellable_ads,
            'disableads' => $disableads,
            'is_polylang_activated' => $is_polylang_activated,
            'pll_languages' => $pll_languages,
        );
        $data = apply_filters('quads_localize_filter',$data,'quads_localize_data');
        // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
        wp_register_script( 'quads-admin-ad-script', QUADS_PLUGIN_URL . 'admin/assets/js/dist/adminscript.js', array( 'wp-i18n' ), QUADS_VERSION );

        wp_localize_script( 'quads-admin-ad-script', 'quads_localize_data', $data );
        
        wp_enqueue_script('quads-admin-ad-script');

        wp_set_script_translations( 'quads-admin-ad-script','quick-adsense-reloaded', QUADS_PLUGIN_DIR . 'languages' );

        echo '<div id="quads-ad-content"></div>';

        echo '<div class="quads-admin-debug">'.quads_get_debug_messages().'</div>';  //phpcs:ignore --Reason: Duming options for debug mode
            	
}

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @return void
 */
function quads_version_switch(){
    $quads_mode = get_option('quads-mode');
    if($quads_mode == 'new'){
        update_option('quads-mode','old');
    }else{
         update_option('quads-mode','new');
    }
    echo '<script>window.location="'.esc_url(admin_url("admin.php?page=quads-settings")).'";</script>';
  
}

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_options_page() {
	global $quads_options;

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information just rendering the options page contents
    $active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), quads_get_settings_tabs() ) ? sanitize_text_field( wp_unslash( $_GET[ 'tab' ] ) ) : 'general';
	?>
	<div class="wrap quads_admin">
             <h1 style="text-align:center;"> <?php echo esc_html(QUADS_NAME . ' ' . QUADS_VERSION); ?></h1>
		<h2 class="quads-nav-tab-wrapper">
			<?php
			foreach( quads_get_settings_tabs() as $tab_id => $tab_name ) {

				$tab_url = esc_url(add_query_arg( array(
					'settings-updated' => false,
					'tab' => $tab_id
				) ));

				$active = $active_tab == $tab_id ? ' quads-nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="quads-nav-tab' . esc_attr($active) . '">';
					echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h2>
		<div id="quads_tab_container" class="quads_tab_container">
                        <?php quads_get_tab_header( 'quads_settings_' . $active_tab, 'quads_settings_' . $active_tab ); ?>   
                    <div class="quads-panel-container"> <!-- new //-->
			<form method="post" action="options.php" id="quads_settings">
                            
				<?php
				settings_fields( 'quads_settings' );
				quads_do_settings_fields( 'quads_settings_' . $active_tab, 'quads_settings_' . $active_tab );
				?>                                
                                <?php  settings_errors(); ?>
				<?php 
                                // do not show save button on add-on page
                                if ($active_tab !== 'addons' && $active_tab !== 'imexport' && $active_tab !== 'help'){
                                    $other_attributes = array( 'id' => 'quads-submit-button' );
                                    submit_button(null, 'primary', 'quads-save-settings' , true, $other_attributes ); 
                                    if ($active_tab !== 'licenses'){
                                    ?>
                                    <!--<a href="<?php //echo admin_url() . '/admin.php?page=quads-settings&quads-action=validate'; ?> " id="quads-validate"><?php //esc_html_e('Validate Ad Settings','quick-adsense-reloaded')?></a>//-->
                                <?php
                                }
                                
                                    }
                                ?>
			</form>
                        <div id="quads-footer">
                        <?php
                        
                        if ($active_tab !== 'addons' && $active_tab !== 'licenses'){
                        echo '<strong>'. esc_html__( 'If you like this plugin please do us a BIG favor and give us a 5 star rating ', 'quick-adsense-reloaded' ) .
                        '<a href="' . esc_url( 'https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/#new-post' ) . '" target="_blank">' .
                        esc_html__( 'here', 'quick-adsense-reloaded' ) . '</a>' .
                        esc_html__( '. If you have issues, open a ', 'quick-adsense-reloaded' ) .
                        '<a href="' . esc_url( 'http://wpquads.com/support/' ) . '" target="_blank">' .
                        esc_html__( 'support ticket', 'quick-adsense-reloaded' ) . '</a>' .
                        esc_html__( ', so that we can sort it out. Thank you!', 'quick-adsense-reloaded' ).'</strong>';
                        echo '<br/><br/><strong>' . esc_html__( 'Ads are not showing? Read the troubleshooting guide to find out how to resolve it', 'quick-adsense-reloaded' ) . '<a href="' . esc_url( 'http://example.com/troubleshooting-guide' ) . '" target="_blank">' . esc_html__( 'troubleshooting guide', 'quick-adsense-reloaded' ) . '</a></strong>';
                        }
                        ?>
                        </div>
                    </div> 
                    <div style="display: inline-block;width: 242px;">
                    <div class="switch_to_v2">
                    <h3><?php echo esc_html__('Quads 2.0 has the better User interface','quick-adsense-reloaded');?></h3> 
                    <p><?php echo esc_html__('We have improved the Quads and made it better than ever! Step into the future with one-click!','quick-adsense-reloaded');?></p>
                    <div onclick="quads_switch_version('new',this);" class="switch_to_v2_btn"><a  href="#"><?php echo esc_html__('Switch to New Panel','quick-adsense-reloaded');?></a></div>
                    </div>
                    <?php quads_get_advertising(); ?>
                </div>
		</div><!-- #tab_container-->
                <div id="quads-save-result"></div>
                <div class="quads-admin-debug"><?php echo quads_get_debug_messages(); //phpcs:ignore --Reason: Dumping options for debug mode ?></div>
                <?php echo quads_render_adsense_form(); //phpcs:ignore --Reason: Already escaped ?>
	</div><!-- .wrap -->
	<?php
}

function quads_get_debug_messages(){
    global $quads_options;
    
    if (isset($quads_options['debug_mode']) && $quads_options['debug_mode'] == 1){        
        echo '<pre style="clear:both;">';
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
        var_dump($quads_options);
        echo '</pre>';
    }
}

/**
 * Render ad and return it when plugin is not pro version
 * @return string
 */
function quads_get_advertising() {
    
    if (  quads_is_extra() ){
        return '';
    }
            ?>
    <div class="quads-panel-sidebar" style="float:left;min-width: 301px;margin-left: 1px;margin-top:0px;">
        <a href="http://wpquads.com/?utm_source=wpquads&utm_medium=banner&utm_term=click-quads&utm_campaign=wpquads" target="_blank">
            <img src="<?php echo esc_url(QUADS_PLUGIN_URL . '/assets/images/quads_banner_250x521_buy.png'); ?>">
        </a>
        <br>
    </div>
    <?php
}

/**
 * Render social buttons
 * 
 * @return void
 */
function quads_render_social(){
         ?>
        <div class='quads-share-button-container'>
                        <div class='quads-share-button quads-share-button-twitter' data-share-url="https://wordpress.org/plugins/quick-adsense-reloaded">
                            <div clas='box'>
                                <a href="https://twitter.com/share?url=https://wordpress.org/plugins/quick-adsense-reloaded&text=Quick%20AdSense%20reloaded%20-%20a%20brand%20new%20fork%20of%20the%20popular%20AdSense%20Plugin%20Quick%20Adsense!" target='_blank'>
                                    <span class='quads-share'><?php echo esc_html__('Tweet', 'quick-adsense-reloaded'); ?></span>
                                </a>
                            </div>
                        </div>

                        <div class="quads-share-button quads-share-button-facebook" data-share-url="https://wordpress.org/plugins/quick-adsense-reloaded">
                            <div class="box">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=https://wordpress.org/plugins/quick-adsense-reloaded" target="_blank">
                                    <span class='quads-share'><?php echo esc_html__('Share', 'quick-adsense-reloaded'); ?></span>
                                </a>
                            </div>
                        </div>
            </div>
        
        <?php
}


/**
 * Render AdSense Form
 */
function quads_render_adsense_form(){
    
?>
<div id="quads-adsense-bg-div" style="display: none;">
    <div id="quads-adsense-container">
        <h3><?php esc_html_e( 'Enter <a ahref="https://wpquads.com/docs/how-to-create-and-where-to-get-adsense-code/" target="_blank">AdSense text & display ad code</a> here', 'quick-adsense-reloaded' ); ?></h3>
        <?php esc_html_e('Do not enter <a href="https://wpquads.com/docs/integrate-page-level-ads-wordpress/" target="_blank">AdSense page level ads</a> or <a href="https://wpquads.com/introducing-new-adsense-auto-ads/" target="_blank">Auto ads!</a> <br> <a href="https://wpquads.com/docs/how-to-create-and-where-to-get-adsense-code/" target="_blank">Learn how to create AdSense ad code</a>', 'quick-adsense-reloaded'); ?>
        <textarea rows="15" cols="55" id="quads-adsense-form"></textarea><hr />
        <button class="button button-primary" id="quads-paste-button"><?php esc_html_e( 'Get Code', 'quick-adsense-reloaded' ); ?></button>&nbsp;&nbsp;
        <button class="button button-secondary" id="quads-close-button"><?php esc_html_e( 'Close', 'quick-adsense-reloaded' ); ?></button>
        <div id="quads-msg"></div>
        <input type="hidden" id="quads-adsense-id" value="">
    </div>
</div>
<?php
}
function quads_get_current_user_roles() {
 
  if( is_user_logged_in() ) {
 
    $user = wp_get_current_user();
 
    $roles = ( array ) $user->roles;
 
    return $roles; // This will returns an array
 
  } else {
 
    return array();
 
  }
 
}
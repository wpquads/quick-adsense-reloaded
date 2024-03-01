<?php
/**
 * Admin Notices
 *
 * @package     QUADS
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.9
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Admin Messages
 *
 * @since 2.2.3
 * @global $mashsb_options Array of all the WP QUADS Options
 * @return void
 */
function quads_admin_messages() {
    global $quads_options;

    if( !current_user_can( 'update_plugins' ) || quads_is_addon_page() ) {
        return;
    }

    $screen = get_current_screen();
    if( $screen->parent_base == 'edit' ) {
        return;
    }
    if (!quads_is_advanced()) {
        // quads_show_update_auto_ads();
    }else{
        if(!quads_is_extra())
        quads_licene_acivation_notice();
    }
    

    quads_theme_notice();

    // quads_update_notice();

    quads_update_notice_v2();

    quads_update_notice_1_5_3();

    quads_show_ads_txt_notice();

    quads_show_license_expired();


    if( quads_is_admin_page() ) {
        echo '<div class="notice notice-error" style="background-color:#ffebeb;display:none;" id="wpquads-adblock-notice">' . sprintf( __( '<strong><p>Please disable your browser AdBlocker to resolve problems with WP QUADS ad setup</strong></p>', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings#quads_settingsgeneral_header' ) . '</div>';
    }

    if( isset( $_GET['quads-action'] ) && $_GET['quads-action'] === 'validate' && quads_is_admin_page() && quads_is_any_ad_activated() && quads_is_post_type_activated() && quads_get_active_ads() > 0 ) {
        echo '<div class="notice notice-success">' . sprintf( __( '<strong>No errors detected in WP QUADS settings.</strong> If ads are still not shown read the <a href="%s" target="_blank">troubleshooting guide</a>' ), 'http://wpquads.com/docs/adsense-ads-are-not-showing/?utm_source=plugin&utm_campaign=wpquads-settings&utm_medium=website&utm_term=toplink' ) . '</div>';
    }
quads_show_rate_div();

}
function quads_admin_messages_new(){
       if( quads_is_admin_page() ) {
        echo '<div class="notice notice-error" style="background-color:#ffebeb;display:none;" id="wpquads-adblock-notice">' . sprintf( __( '<strong><p>Please disable your browser AdBlocker to resolve problems with WP QUADS ad setup</strong></p>', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings#quads_settingsgeneral_header' ) . '</div>';
    }
}

function quads_admin_newdb_upgrade(){
    if( quads_is_admin_page() ) {
        global $quads_options;
        $import_details = get_option('quads_import_data');
        $import_status = (isset($import_details['status']) && $import_details['status'] == 'active')?true:false;
        $import_nonce = wp_create_nonce( 'quads_newdb_nonce' );
        $import_done = get_option('quads_db_import',false);
        $tb_style = $ul_style = '';
        $upgrade_percent = 2;
        
        $mode_check = (isset($quads_options['report_logging']) && $quads_options['report_logging'] == 'improved_v2')?false:true;
        $new_check = get_option('quads_v2_db_no_import',false);
        if($new_check){
            return '';
        }
        if($import_done || $mode_check){
            return '';
        }
        if($import_status ){
            if(isset($import_details['current_table']) && isset($import_details['sub_table']) && $import_details['current_table'] == 'quads_stats' && $import_details['sub_table'] == ''){
                $upgrade_percent = 10;
            }else if(isset($import_details['current_table']) && isset($import_details['sub_table']) && $import_details['current_table'] == 'quads_single_stats_' && $import_details['sub_table'] == 'impressions_mobile'){
                $upgrade_percent = 25;
            }else if(isset($import_details['current_table']) && isset($import_details['sub_table']) && $import_details['current_table'] == 'quads_single_stats_' && $import_details['sub_table'] == 'impressions_desktop'){
                $upgrade_percent = 50;
            }else if(isset($import_details['current_table']) && isset($import_details['sub_table']) && $import_details['current_table'] == 'quads_single_stats_' && $import_details['sub_table'] == 'clicks_mobile'){
                $upgrade_percent = 75;
            }else if(isset($import_details['current_table']) && isset($import_details['sub_table']) && $import_details['current_table'] == 'quads_single_stats_' && $import_details['sub_table'] == 'clicks_desktop'){
                $upgrade_percent = 100;
                update_option('quads_v2_db_no_import',true);
            }
            $ul_style = 'display:none';
        }else{
            $tb_style = 'display:none';
        }
        


        echo '<div class="quads_db_upgrade updated " style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);background-color:white;font-size:16px;"> 
        <p style="font-size:18px;">'.esc_html('You have selected').' <b>'.esc_html('Report Logging Method').'</b>  to <b>'.esc_html('"Separate Data (Improved V2)"').'</b>.'.esc_html('To import your old tracking data click on import. It may take sometime depending upon size of your website. ').'<b>'.esc_html('Once you have imported the old data we recommend that you do not  select ').'<u>'.esc_html('Combined Data (Legacy)').'</u> '.esc_html('to avoid duplication of data.').'</b>
        <ul class="dbupgrade_link" style="'.esc_attr($ul_style).'">
            <li><a href="javascript:void(0);" class="quads_db_upgrade_button" title="Upgrade Performance Tracking" style="font-weight:bold;">Import Tracking Data</a> &nbsp;<a href="javascript:void(0);" style="color:#000" class="quads_db_not_upgrade" title="Do Not import Data" style="font-weight:bold;">Do Not import Data</a></li>
            <li class="spinner" style="float:none;display:list-item;margin:0px;"></li>        
        </ul>
        <table class="dbupgrade_infotable" style="padding: 10px;'.esc_attr($tb_style).'">
        <tr>
        <th>'.esc_html('Upgrade Status').'</th>
        <td> '.esc_attr($upgrade_percent).'% </td>
        </tr></table>

    </div>
    <div id="quads-conform-dialog" class="hidden" style="max-width:800px; position: fixed;top: 35%;left: 25%;background: #fff;padding: 40px;z-index: 999;border: 1px solid;">
  <h3>'.esc_html('Are you sure you want to continue ?').'</h3>
  <h4>'.esc_html('Are you sure that you want to continue without old tracking data and start with fresh tracking?').'</h4>
  <button id="quads_db_confirm" class="quads-btn quads-btn-primary">'.esc_html('Yes, Continue').'</button> &nbsp; <button class="quads-btn quads_db_cancel quads-btn-default">'.esc_html('No,Take me back').'</button>
</div>
    <script>
    jQuery( document ).ready(function( $ ) {

    jQuery(\'.quads_db_upgrade_button\').click(function(){
    jQuery(".spinner").addClass("is-active");
        var data={\'start\':\'true\',
                 \'action\':\'quads_start_newdb_migration\',
                 \'nonce\':\''.esc_attr($import_nonce ).'\'}
                jQuery.ajax({
                    url: "' . admin_url( 'admin-ajax.php').'",
                    type: "post",
                    data: data,
                    dataType: "json",
                    async: !0,
                    success: function(e) {
                        
                        jQuery(".spinner").removeClass("is-active");
                        jQuery(\'.dbupgrade_infotable\').show();
                        jQuery(\'.dbupgrade_link\').hide();
                        
                    },
                    error: function(e) {
                        jQuery(".spinner").removeClass("is-active");
                        jQuery(\'.dbupgrade_infotable\').hide();
                        jQuery(\'.dbupgrade_link\').show();
                        alert(\'Error Occured, please refresh the page and try again\');
                    }
                });
        }) 
        
        jQuery(\'#quads_db_confirm\').click(function(){
            jQuery(".spinner").addClass("is-active");
                var data={\'start\':\'true\',
                         \'action\':\'quads_hide_newdb_migration\',
                         \'nonce\':\''.esc_attr($import_nonce ).'\'}
                        jQuery.ajax({
                            url: "' . admin_url( 'admin-ajax.php').'",
                            type: "post",
                            data: data,
                            dataType: "json",
                            async: !0,
                            success: function(e) {
                                if(e.status == "success"){
                                jQuery(".spinner").removeClass("is-active");
                                jQuery(\'.quads_db_upgrade\').hide();
                                jQuery(\'.dbupgrade_link\').hide();
                                jQuery(\'#quads-conform-dialog\').hide();
                                }
                                else{
                                    alert(\'Error Occured, please refresh the page and try again\');
                                }
                                
                            },
                            error: function(e) {
                                jQuery(".spinner").removeClass("is-active");
                                jQuery(\'.dbupgrade_infotable\').hide();
                                jQuery(\'.dbupgrade_link\').show();
                                jQuery(\'#quads-conform-dialog\').hide();
                                alert(\'Error Occured, please refresh the page and try again\');

                            }
                        });
                })    

                jQuery(\'.quads_db_cancel\').click(function(){
                    jQuery(".spinner").removeClass("is-active");
                    jQuery(\'.quads_db_upgrade\').show();
                    jQuery(\'.dbupgrade_link\').show();
                    jQuery(\'#quads-conform-dialog\').hide();
                    
                        }); 
                        
                jQuery(\'.quads_db_not_upgrade\').click(function(){
                    jQuery(\'#quads-conform-dialog\').show();
                    
                }); 

    });
    </script>';
    }  
}
function quads_show_rate_div(){


    $install_date = get_option( 'quads_install_date' );
    $display_date = date( 'Y-m-d h:i:s' );
    $datetime1    = new DateTime( $install_date );
    $datetime2    = new DateTime( $display_date );
    $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );

    $rate = get_option( 'quads_rating_div', false );
    if( $diff_intrval >= 7 && ($rate === "no" || false === $rate || quads_rate_again() ) ) {
        echo '<div class="quads_fivestar updated " style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);background-color:white;">
        <p>Awesome, you\'ve been using <strong>WP QUADS</strong> for more than 1 week. <br> May i ask you to give it a <strong>5-star rating</strong> on Wordpress? </br>
        This will help to spread its popularity and to make this plugin a better one.
        <br><br>Your help is much appreciated. Thank you very much
        <ul>
            <li><a href="https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/?filter=5#new-post" class="thankyou" target="_new" title="Ok, you deserved it" style="font-weight:bold;">Ok, you deserved it</a></li>
            <li><a href="javascript:void(0);" class="quadsHideRating" title="I already did" style="font-weight:bold;">I already did</a></li>
            <li><a href="javascript:void(0);" class="quadsHideRating" title="No, not good enough" style="font-weight:bold;">No, not good enough</a></li>
            <br>
            <li><a href="javascript:void(0);" class="quadsHideRatingWeek" title="No, not good enough" style="font-weight:bold;">I want to rate it later. Ask me again in a week!</a></li>
            <li class="spinner" style="float:none;display:list-item;margin:0px;"></li>        
</ul>

    </div>
    <script>
    jQuery( document ).ready(function( $ ) {

    jQuery(\'.quadsHideRating\').click(function(){
    jQuery(".spinner").addClass("is-active");
        var data={\'action\':\'quads_hide_rating\'}
             jQuery.ajax({
        
        url: "' . admin_url( 'admin-ajax.php' ) . '",
        type: "post",
        data: data,
        dataType: "json",
        async: !0,
        success: function(e) {
            if (e=="success") {
               jQuery(".spinner").removeClass("is-active");
               jQuery(\'.quads_fivestar\').slideUp(\'fast\');
               
            }
        }
         });
        })
    
        jQuery(\'.quadsHideRatingWeek\').click(function(){
        jQuery(".spinner").addClass("is-active");
        var data={\'action\':\'quads_hide_rating_week\'}
             jQuery.ajax({
        
        url: "' . admin_url( 'admin-ajax.php' ) . '",
        type: "post",
        data: data,
        dataType: "json",
        async: !0,
        success: function(e) {
            if (e=="success") {
               jQuery(".spinner").removeClass("is-active");
               jQuery(\'.quads_fivestar\').slideUp(\'fast\');
               
            }
        }
         });
        })
    
    });
    </script>
    ';
    }
}

// add_action( 'admin_notices', 'quads_admin_messages' );


/* Hide the rating div
 * 
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.9
 * 
 * @return json string
 * 
 */

function quads_hide_rating_div() {
    if( ! current_user_can( 'manage_options' ) ) { return; }
    update_option( 'quads_rating_div', 'yes' );
    delete_option( 'quads_date_next_notice' );
    echo json_encode( array("success") );
    exit;
}

add_action( 'wp_ajax_quads_hide_rating', 'quads_hide_rating_div' );

/**
 * Write the timestamp when rating notice will be opened again
 */
function quads_hide_rating_notice_week() {
    if( ! current_user_can( 'manage_options' ) ) { return; }
    $nextweek   = time() + (7 * 24 * 60 * 60);
    $human_date = date( 'Y-m-d h:i:s', $nextweek );
    update_option( 'quads_date_next_notice', $human_date );
    update_option( 'quads_rating_div', 'yes' );
    echo json_encode( array("success") );
    exit;
}

add_action( 'wp_ajax_quads_hide_rating_week', 'quads_hide_rating_notice_week' );

/**
 * Check if admin notice will open again after one week of closing
 * @return boolean
 */
function quads_rate_again() {
    $rate_again_date = get_option( 'quads_date_next_notice' );

    if( false === $rate_again_date ) {
        return false;
    }

    $current_date = date( 'Y-m-d h:i:s' );
    $datetime1    = new DateTime( $rate_again_date );
    $datetime2    = new DateTime( $current_date );
    $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );

    if( $diff_intrval >= 0 ) {
        return true;
    }
}

/**
 * Show a message when pro or free plugin gets disabled
 * 
 * @return void
 * @not used
 */
function quads_plugin_deactivated_notice() {
    if( false !== ( $deactivated_notice_id = get_transient( 'quads_deactivated_notice_id' ) ) ) {
        if( '1' === $deactivated_notice_id ) {
            $message = __( "WP QUADS and WP QUADS Pro cannot be activated both. We've automatically deactivated WP QUADS.", 'wpstg' );
        } else {
            $message = __( "WP QUADS and WP QUADS Pro cannot be activated both. We've automatically deactivated WP QUADS Pro.", 'wpstg' );
        }
        ?>
        <div class="updated notice is-dismissible" style="border-left: 4px solid #ffba00;">
            <p><?php echo esc_html( $message ); ?></p>
        </div> <?php
        delete_transient( 'quads_deactivated_notice_id' );
    }
}

/**
 * This notice is shown for user of the bimber and bunchy theme
 * 
 * Not used at the moment
 */
function quads_theme_notice() {

    $show_notice = get_option( 'quads_show_theme_notice' );

    if( false !== $show_notice && 'no' !== $show_notice && quads_is_commercial_theme() ) {
        $message = __( '<strong>Extend the' . quads_is_commercial_theme() . '</strong> theme with <strong>WP QUADS PRO!</strong><br>Save time and earn more - Bring your AdSense earnings to next level. <a href="http://wpquads.com?utm_campaign=adminnotice&utm_source=admin_notice&utm_medium=admin&utm_content=bimber_upgrade_notice" target="_blank"> Purchase Now</a> or <a href="http://wpquads.com?utm_campaign=free_plugin&utm_source=admin_notice&utm_medium=admin&utm_content=bimber_upgrade_notice" target="_blank">Get Details</a> <p> <a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=close_upgrade_notice" class="button">Close Notice</a>', 'quick-adsense-reloaded' );
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p><?php echo $message; ?></p>
        </div> <?php
        //update_option ('quads_show_theme_notice', 'no');
    }
}

/**
 * This notice is shown after updating to 1.3.9
 * 
 */
function quads_update_notice() {

    $show_notice = get_option( 'quads_show_update_notice' );

    // do not do anything
    if( false !== $show_notice ) {
        return false;
    }

    if( (version_compare( QUADS_VERSION, '1.3.9', '>=' ) ) && quads_is_pro_active() && (version_compare( QUADS_PRO_VERSION, '1.3.0', '<' ) ) ) {
        $message = sprintf( __( '<strong>WP QUADS ' . QUADS_VERSION . ': </strong> Update WP QUADS PRO to get custom post type support from <a href="%s">General Settings</a>.', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings' );
        $message .= '<br><br><a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_update_notice" class="button-primary thankyou" target="_self" title="Close Notice" style="font-weight:bold;">Close Notice</a>';
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p><?php echo $message; ?></p>
        </div> <?php
        //update_option ('quads_show_update_notice', 'no');
    } else
    if( !quads_is_advanced() ) {
        $message = sprintf( __( '<strong>WP QUADS ' . QUADS_VERSION . ': </strong> Install <a href="%1s" target="_blank">WP QUADS PRO</a> to get custom post type support in <a href="%2s">General Settings</a>.', 'quick-adsense-reloaded' ), 'http://wpquads.com?utm_campaign=admin_notice&utm_source=admin_notice&utm_medium=admin&utm_content=custom_post_type', admin_url() . 'admin.php?page=quads-settings' );
        $message .= '<br><br><a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_update_notice" class="button-primary thankyou" target="_self" title="Close Notice" style="font-weight:bold;">Close Notice</a>';
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p><?php echo $message; ?></p>
        </div>
        <?php
    }
}

/**
 * Show upgrade notice if wp quads pro is lower than 1.3.6
 * @return boolean
 */
function quads_update_notice_v2() {

    if( quads_is_pro_active() && (version_compare( QUADS_PRO_VERSION, '1.3.6', '<' ) ) && quads_is_admin_page() ) {
        $message = sprintf( __( 'You need to update <strong>WP QUADS PRO to version 1.3.6</strong> or higher. Your version of <strong>WP QUADS Pro</strong> is ' . QUADS_PRO_VERSION . '.<br>WP QUADS Pro ' . QUADS_PRO_VERSION . ' supports unlimited amount of ads. <br>Updating requires a valid <a href="%s" target="_new">license key</a>.', 'quick-adsense-reloaded' ), 'https://wpquads.com/#buy-wpquads?utm_source=plugin_notice&utm_medium=admin&utm_campaign=activate_license' );
        ?>
        <div class="notice notice-error">
            <p><?php echo $message; ?></p>
        </div> <?php
    }
}

/**
 * Show upgrade notice after updating from 1.5.2 to 1.5.3 and higher
 * @return boolean
 */
function quads_update_notice_1_5_3() {

    // do not show anything
    if( false !== get_option( 'quads_hide_update_notice_1_5_3' ) ) {
        return false;
    }

    $previous_version = get_option( 'quads_version_upgraded_from' );

    //wp_die(QUADS_VERSION);
    // Show update message if previous version was lower than 1.7 - This makes sure that the message is shown for future updates without complicated version number conditions
    if( !empty( $previous_version ) && version_compare( QUADS_VERSION, '1.7.0', '<=' ) ) {

        $message = sprintf( __( 'This is a huge update! The data structure of WP QUADS has been modified and improved for better performance and great new features. <br> For the case you\'d experience issues, we made a <a href="%1s" target="_self">backup of previous WP QUADS data</a>. So you can <a href="%2s" target="_new">switch back to the previous version</a> anytime. <br><br>Please <a href="%3s" target="_new">open first a support ticket</a> if you experience any issue.', 'quick-adsense-reloaded' ), admin_url() . '?page=quads-settings&tab=help', 'https://wpquads.com/docs/install-older-plugin-version/?utm_source=plugin_notice&utm_medium=admin&utm_campaign=install_older_version', 'https://wordpress.org/support/plugin/quick-adsense-reloaded' );
        ?>
        <div class="notice notice-error">
            <p><?php echo $message; ?></p>
            <?php
            echo '<p><a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_update_notice_1_5_3" class="button-primary" target="_self" title="Close Notice" style="font-weight:bold;">' . __( 'Close Notice', 'quick-adsense-reloaded' ) . '</a>';
            ?>
        </div> <?php
    }
}

/**
 * Hide Notice and update db option quads_hide_notice
 */
function quads_hide_notice() {
    update_option( 'quads_show_update_notice', 'no' );
}

add_action( 'quads_hide_update_notice', 'quads_hide_notice', 10 );

/**
 * Set option to hide admin notice 1.5.3
 * @return boolean
 */
function quads_hide_notice_1_5_3() {
    update_option( 'quads_hide_update_notice_1_5_3', '1' );
}

add_action( 'quads_hide_update_notice_1_5_3', 'quads_hide_notice_1_5_3' );

function quads_hide_adpushup_notice_box() {
    update_option( 'quads_hide_adpushup_notice', '1' );
}

add_action( 'quads_hide_adpushup_notice', 'quads_hide_adpushup_notice_box' );

/**
 * Check if any ad is activated and assigned in general settings
 * 
 * @global array $quads_options
 * @return boolean
 */
function quads_is_any_ad_activated() {
    global $quads_options;

    // Check if custom positions location_settings is empty or does not exists
    $check = array();
    if( isset( $quads_options['location_settings'] ) ) {
        foreach ( $quads_options['location_settings'] as $location_array ) {
            if( isset( $location_array['status'] ) ) {
                $check[] = $location_array['status'];
            }
        }
    }

    // ad activated with api (custom position)
    if( count( $check ) > 0 ) {
        return true;
    }
    // check if any other ad is assigned and activated
    if( isset( $quads_options['pos1']['BegnAds'] ) ||
            isset( $quads_options['pos2']['MiddAds'] ) ||
            isset( $quads_options['pos3']['EndiAds'] ) ||
            isset( $quads_options['pos4']['MoreAds'] ) ||
            isset( $quads_options['pos5']['LapaAds'] ) ||
            isset( $quads_options['pos6']['Par1Ads'] ) ||
            isset( $quads_options['pos7']['Par2Ads'] ) ||
            isset( $quads_options['pos8']['Par3Ads'] ) ||
            isset( $quads_options['pos9']['Img1Ads'] )
    ) {
        return true;
    }
    // no ad is activated
    return false;
}

/**
 * Check if any post type is enabled
 * 
 * @global array $quads_options
 * @return boolean
 */
function quads_is_post_type_activated() {
    global $quads_options;

    if( empty( $quads_options['post_types'] ) ) {
        return false;
    }
    return true;
}

/**
 * Check if ad codes are populated
 * 
 * @global array $quads_options
 * @return booleantrue if ads are empty
 */
function quads_ads_empty() {
    global $quads_options;

    $check = array();

    for ( $i = 1; $i <= 10; $i++ ) {
        if( !empty( $quads_options['ads']['ad' . $i]['code'] ) ) {
            $check[] = 'true';
        }
    }
    if( count( $check ) === 0 ) {
        return true;
    }
    return false;
}

/**
 * Show a ads.txt notices if WP QUADS has permission to update or create an ads.txt
 */
function quads_show_ads_txt_notice() {
    global $quads, $quads_options;

    if( !quads_is_admin_page() )
        return false;


    // show ads.txt error notice
    if( get_transient( 'close_ads_txt_error' ) && isset( $quads_options['adsTxtEnabled'] ) ) {

        // Check if adsense is used and add the adsense publisherId to ads.txt blurb as well
        $adsense             = new wpquads\adsense( $quads_options );
        $adsensePublisherIds = $adsense->getPublisherIds();


        $adsenseAdsTxtText = '';
        if( !empty( $adsensePublisherIds ) ) {
            foreach ( $adsensePublisherIds as $adsensePublisherId ) {
                $adsenseAdsTxtText .= "google.com, " . str_replace( 'ca-', '', $adsensePublisherId ) . ", DIRECT, f08c47fec0942fa0\n\r";
            }
        }

        $viAdsTxtText = '';
        if( $quads->vi->getPublisherId() ) {
            $viAdsTxtText = $quads->vi->getAdsTxtContent();
        }

        // ads.txt content
        $notice['message'] = "<p><strong>ADS.TXT couldn't be updated automatically</strong><br><br>Important note: WP QUADS hasn't been able to update your ads.txt file automatically. Please make sure to enter the following line manually into <strong>" . get_home_path() . "ads.txt</strong>:"
                . "<p>"
                . "<pre>" . $viAdsTxtText . "<br>"
                . $adsenseAdsTxtText
                . "</pre></p>"
                . "Only by doing so AdSense ads are shown on your site.</p>";
        $notice['type']    = 'error';
        $notice['action']  = 'quads_ads_txt_error';

        // render blurb
        $adsTxtError = new wpquads\template( '/includes/admin/views/notices', $notice );
        echo $adsTxtError->render();
    }
}

/**
 * Show global notice WP QUADS Pro license expired
 * @return mixed boolean | string
 */
function quads_show_license_expired() {
    global $quads_options, $wp_version;

    $licKey = isset( $quads_options['quads_wp_quads_pro_license_key'] ) ? $quads_options['quads_wp_quads_pro_license_key'] : '';

    $lic = get_option( 'quads_wp_quads_pro_license_active' );

    // Do not show if no license at all or if there is a valid license key
    if( !$lic || (isset( $lic->license ) && $lic->license !== 'invalid') ) {
        return false;
    }

    // Do not show notice for another 30 days
    if( get_transient( 'quads_notice_lic_expired' ) ) {
        return false;
    }

    
    echo '<div class="notice notice-error">';
    echo sprintf(
            __( '<p>Oh No! <strong>WP Quads Pro</strong> license key is not activated or has been expired. It expires on %s. Renew or activate your license key to make sure that your (AdSense) ads are shown properly with your WordPress, version ' . $wp_version . '<br>'
                    . '<a href="%s" target="_blank" title="Renew your license key" class="button"><strong>Renew Your License Key Now</strong></a> | <a href="%s" title="Renew your license key">I am aware of possible issues and want to hide this reminder</a>'
                    , 'quick-adsense-reloaded' ), date_i18n( get_option( 'date_format' ), strtotime( isset($lic->expires)?$lic->expires:null, current_time( 'timestamp' ) ) ), 'http://wpquads.com/checkout/?edd_license_key=' . $licKey . '&utm_campaign=adminnotic123e&utm_source=adminnotice123&utm_medium=admin&utm_content=license-expired', admin_url() . 'admin.php?page=quads-settings&tab=licenses&quads-action=hide_license_expired_notice'
    );
    echo '</p></div>';
}

/**
 * Store the transient for 30 days
 */
function quads_hide_license_expired_notice() {
    set_transient( 'quads_notice_lic_expired', 'hide', 60 * 60 * 24 * 30 );
}

add_action( 'quads_hide_license_expired_notice', 'quads_hide_license_expired_notice' );

/**
 * Return update notice for Google Auto Ads
 * @since 3.5.3.0
 */
function quads_show_update_auto_ads() {


    $message = sprintf( __( '<h2 style="color:white;">WP QUADS & Google Auto Ads</h2>'
                    . 'WP QUADS Pro adds support for Google Auto Ads<br><br> Get the Pro plugin from <a href="https://wpquads.com/?utm_source=wp-admin&utm_medium=autoads-notice&utm_campaign=autoads-notice" target="_blank" style="color:#87c131;font-weight:500;">wpquads.com</a>'
                    , 'mashsb' ), admin_url() . 'admin.php?page=quads-settings'
    );

    if( get_option( 'quads_show_notice_auto_ads' ) === 'no' ) {
        return false;
    }

    // admin notice after updating wp quads
    echo '<div class="quads-notice-gdpr update-nag" style="background-color: black;color: #87c131;padding: 20px;margin-top: 20px;border: 3px solid #87c131;display:block;">' . $message .
    '<p><a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_auto_ads_notice" class="quads_hide_gdpr" title="I got it" style="text-decoration:none;color:white;">- I Understand! Do Not Show This Hint Again -</a></a>' .
    '</div>';
}
function quads_licene_acivation_notice(){
    $quads_mode = get_option('quads-mode');
    if($quads_mode == 'new'){
        $message = __( 'Activate the License of <a href="'.admin_url('admin.php?page=quads-settings&path=settings_licenses').'" target="_blank"> <strong>WP QUADS PRO!</strong></a><br><p>', 'quick-adsense-reloaded' );
    }else{
        $message = __( 'Activate the License of <a href="'.admin_url('admin.php?page=quads-settings&tab=licenses').'" target="_blank"> <strong>WP QUADS PRO!</strong></a><br><p>', 'quick-adsense-reloaded' );
    }
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p><?php echo $message; ?></p>
        </div><?php
}

/**
 * Hide GDPR notice
 * 
 * @global array $quads_options
 */
function quads_hide_auto_ads_notice() {
    global $quads_options;
    // Get all settings
    update_option( 'quads_show_notice_auto_ads', 'no' );
}

add_action( 'quads_hide_auto_ads_notice', 'quads_hide_auto_ads_notice' );
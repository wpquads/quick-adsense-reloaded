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
        quads_license_activation_notice();
    }
    

    quads_theme_notice();

    // quads_update_notice();

    quads_update_notice_v2();

    quads_update_notice_1_5_3();

    quads_show_ads_txt_notice();

    quads_show_license_expired();


    if( quads_is_admin_page() ) {

        echo '<div class="notice notice-error" style="background-color:#ffebeb;display:none;" id="quads-adblock-notice">' .
            '<strong>' . esc_html__('Please disable your browser AdBlocker to resolve problems with WP QUADS ad setup', 'quick-adsense-reloaded') . '</strong><br><br>' .
            '<a href="' . esc_url(admin_url('admin.php?page=quads-settings#quads_settingsgeneral_header')) . '">' . esc_html__('Go to WP QUADS Settings', 'quick-adsense-reloaded') . '</a>' .
            '</div>';

    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only displaying the admin notices
    if( isset( $_GET['quads-action'] ) && $_GET['quads-action'] === 'validate' && quads_is_admin_page() && quads_is_any_ad_activated() && quads_is_post_type_activated() && quads_get_active_ads() > 0 ) {
        echo '<div class="notice notice-success"><strong>' .esc_html__('No errors detected in WP QUADS settings.','quick-adsense-reloaded'). '</strong>'. esc_html__('If ads are still not shown read the ','quick-adsense-reloaded'). '<a href="'.esc_url('http://wpquads.com/docs/adsense-ads-are-not-showing/?utm_source=plugin&utm_campaign=wpquads-settings&utm_medium=website&utm_term=toplink').'" target="_blank">'.esc_html__('troubleshooting guide','quick-adsense-reloaded') . '</a> </div>';
    }
quads_show_rate_div();

}
function quads_license_activation_notice(){
    $quads_mode = get_option('quads-mode');
    $message = '';
    if($quads_mode == 'new'){
        //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
        $message  = esc_html__( 'Activate the License of', 'quick-adsense-reloaded' ) . ' ';
        $message .= '<a href="' . esc_url( admin_url( 'admin.php?page=quads-settings&path=settings_licenses' ) ) . '" target="_blank">';
        $message .= '<strong> WP QUADS PRO!</strong></a>';
    }else{
        //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
        $message  = esc_html__( 'Activate the License of', 'quick-adsense-reloaded' ) . ' ';
        $message .= '<a href="' . esc_url( admin_url( 'admin.php?page=quads-settings&tab=licenses' ) ) . '" target="_blank">';
        $message .= '<strong> WP QUADS PRO!</strong></a>';
    }
    ?>
    <div class="updated notice" style="border-left: 4px solid #ffba00;">
        <p><?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ echo $message; ?></p>
    </div><?php
}
function quads_admin_messages_new(){
       if( quads_is_admin_page() ) {
        echo '<div class="notice notice-error" style="background-color:#ffebeb;display:none;" id="quads-adblock-notice">' . 
        '<strong>' . esc_html__('Please disable your browser AdBlocker to resolve problems with WP QUADS ad setup', 'quick-adsense-reloaded') . '</strong>' . 
        '</p>' . 
        '<p><a href="' . esc_url(admin_url('admin.php?page=quads-settings#quads_settingsgeneral_header')) . '">' . 
        esc_html__('Go to WP QUADS Settings', 'quick-adsense-reloaded') . '</a></p>' . 
        '</div>';    
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
        <p style="font-size:18px;">'.esc_html__('You have selected', 'quick-adsense-reloaded').' <b>'.esc_html__('Report Logging Method', 'quick-adsense-reloaded').'</b>  to <b>'.esc_html__('"Separate Data (Improved V2)"', 'quick-adsense-reloaded').'</b>.'.esc_html__('To import your old tracking data click on import. It may take sometime depending upon size of your website. ', 'quick-adsense-reloaded').'<b>'.esc_html__('Once you have imported the old data we recommend that you do not  select ', 'quick-adsense-reloaded').'<u>'.esc_html__('Combined Data (Legacy)', 'quick-adsense-reloaded').'</u> '.esc_html__('to avoid duplication of data.', 'quick-adsense-reloaded').'</b>
        <ul class="dbupgrade_link" style="'.esc_attr($ul_style).'">
            <li><a href="javascript:void(0);" class="quads_db_upgrade_button" title="' . esc_attr__( 'Upgrade Performance Tracking', 'quick-adsense-reloaded' ) . '" style="font-weight:bold;">' . esc_html__( 'Import Tracking Data', 'quick-adsense-reloaded' ) . '</a> &nbsp;<a href="javascript:void(0);" style="color:#000" class="quads_db_not_upgrade" title="' . esc_attr__( 'Do Not import Data', 'quick-adsense-reloaded' ) . '" style="font-weight:bold;">' . esc_html__( 'Do Not import Data', 'quick-adsense-reloaded' ) . '</a></li>
            <li class="spinner" style="float:none;display:list-item;margin:0px;"></li>        
        </ul>
        <table class="dbupgrade_infotable" style="padding: 10px;'.esc_attr($tb_style).'">
        <tr>
        <th>'.esc_html__('Upgrade Status', 'quick-adsense-reloaded').'</th>
        <td> '.esc_attr($upgrade_percent).'% </td>
        </tr></table>

    </div>
    <div id="quads-conform-dialog" class="hidden" style="max-width:800px; position: fixed;top: 35%;left: 25%;background: #fff;padding: 40px;z-index: 999;border: 1px solid;">
  <h3>'.esc_html__('Are you sure you want to continue ?', 'quick-adsense-reloaded').'</h3>
  <h4>'.esc_html__('Are you sure that you want to continue without old tracking data and start with fresh tracking?', 'quick-adsense-reloaded').'</h4>
  <button id="quads_db_confirm" class="quads-btn quads-btn-primary">'.esc_html__('Yes, Continue', 'quick-adsense-reloaded').'</button> &nbsp; <button class="quads-btn quads_db_cancel quads-btn-default">'.esc_html__('No,Take me back', 'quick-adsense-reloaded').'</button>
</div>
    <script>
    jQuery( document ).ready(function( $ ) {

    jQuery(\'.quads_db_upgrade_button\').click(function(){
    jQuery(".spinner").addClass("is-active");
        var data={\'start\':\'true\',
                 \'action\':\'quads_start_newdb_migration\',
                 \'nonce\':\''.esc_attr($import_nonce ).'\'}
                jQuery.ajax({
                    url: "' . esc_url(admin_url( 'admin-ajax.php')).'",
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
                            url: "' . esc_url(admin_url( 'admin-ajax.php')).'",
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
/**
 * Display rating notice after plugin has been used for more than 7 days
 *
 * Shows a notice asking users to rate the plugin on WordPress.org after
 * they've been using it for at least a week.
 *
 * @since 1.0.9
 * @return void
 */
function quads_show_rate_div() {

    $install_date = get_option( 'quads_install_date' );
    $display_date = gmdate( 'Y-m-d h:i:s' );
    $datetime1    = new DateTime( $install_date );
    $datetime2    = new DateTime( $display_date );
    $diff_intrval = round( ( $datetime2->format( 'U' ) - $datetime1->format( 'U' ) ) / ( 60 * 60 * 24 ) );

    $rate = get_option( 'quads_rating_div', false );
    
    // Show notice if 7+ days have passed and rating hasn't been dismissed
    if ( $diff_intrval >= 7 && ( $rate === 'no' || false === $rate || quads_rate_again() ) ) {
        $review_url = 'https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/';
        $ajax_url   = admin_url( 'admin-ajax.php' );
        $nonce      = wp_create_nonce( 'quads_hide_rating_nonce' );
        ?>
        <div class="quads_fivestar updated" style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);background-color:white;">
            <p>
                <?php
                echo esc_html__( 'Awesome, you\'ve been using', 'quick-adsense-reloaded' ) . ' ';
                echo '<strong>' . esc_html__( 'WP QUADS', 'quick-adsense-reloaded' ) . '</strong> ';
                echo esc_html__( 'for more than 1 week.', 'quick-adsense-reloaded' );
                ?>
                <br>
                <?php echo esc_html__( 'May i ask you to give it a', 'quick-adsense-reloaded' ); ?>
                <strong><?php echo esc_html__( '5-star rating', 'quick-adsense-reloaded' ); ?></strong>
                <?php echo esc_html__( 'on WordPress?', 'quick-adsense-reloaded' ); ?>
                <br>
                <?php echo esc_html__( 'This will help to spread its popularity and to make this plugin a better one.', 'quick-adsense-reloaded' ); ?>
                <br><br>
                <?php echo esc_html__( 'Your help is much appreciated. Thank you very much', 'quick-adsense-reloaded' ); ?>
            </p>
            <ul>
                <li>
                    <a href="<?php echo esc_url( $review_url ); ?>" class="thankyou" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr__( 'Ok, you deserved it', 'quick-adsense-reloaded' ); ?>" style="font-weight:bold;">
                        <?php echo esc_html__( 'Ok, you deserved it', 'quick-adsense-reloaded' ); ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="quadsHideRating" title="<?php echo esc_attr__( 'I already did', 'quick-adsense-reloaded' ); ?>" style="font-weight:bold;">
                        <?php echo esc_html__( 'I already did', 'quick-adsense-reloaded' ); ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="quadsHideRating" title="<?php echo esc_attr__( 'No, not good enough', 'quick-adsense-reloaded' ); ?>" style="font-weight:bold;">
                        <?php echo esc_html__( 'No, not good enough', 'quick-adsense-reloaded' ); ?>
                    </a>
                </li>
                <br>
                <li>
                    <a href="javascript:void(0);" class="quadsHideRatingWeek" title="<?php echo esc_attr__( 'I want to rate it later. Ask me again in a week!', 'quick-adsense-reloaded' ); ?>" style="font-weight:bold;">
                        <?php echo esc_html__( 'I want to rate it later. Ask me again in a week!', 'quick-adsense-reloaded' ); ?>
                    </a>
                </li>
                <li class="spinner" style="float:none;display:list-item;margin:0px;"></li>
            </ul>
        </div>
        <script type="text/javascript">
        jQuery( document ).ready(function( $ ) {
            $( '.quadsHideRating' ).click(function() {
                $( '.spinner' ).addClass( 'is-active' );
                var data = {
                    'action': 'quads_hide_rating',
                    'nonce': <?php echo wp_json_encode( $nonce ); ?>
                };
                jQuery.ajax({
                    url: <?php echo wp_json_encode( $ajax_url ); ?>,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    async: true,
                    success: function( response ) {
                        if ( response && response.success ) {
                            $( '.spinner' ).removeClass( 'is-active' );
                            $( '.quads_fivestar' ).slideUp( 'fast' );
                        }
                    },
                    error: function() {
                        $( '.spinner' ).removeClass( 'is-active' );
                    }
                });
            });

            $( '.quadsHideRatingWeek' ).click(function() {
                $( '.spinner' ).addClass( 'is-active' );
                var data = {
                    'action': 'quads_hide_rating_week',
                    'nonce': <?php echo wp_json_encode( $nonce ); ?>
                };
                jQuery.ajax({
                    url: <?php echo wp_json_encode( $ajax_url ); ?>,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    async: true,
                    success: function( response ) {
                        if ( response && response.success ) {
                            $( '.spinner' ).removeClass( 'is-active' );
                            $( '.quads_fivestar' ).slideUp( 'fast' );
                        }
                    },
                    error: function() {
                        $( '.spinner' ).removeClass( 'is-active' );
                    }
                });
            });
        });
        </script>
        <?php
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
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'quads_hide_rating_nonce' ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Security check failed.', 'quick-adsense-reloaded' ) ) );
        return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'quick-adsense-reloaded' ) ) );
        return;
    }

    update_option( 'quads_rating_div', 'yes' );
    delete_option( 'quads_date_next_notice' );
    
    wp_send_json_success();
}

add_action( 'wp_ajax_quads_hide_rating', 'quads_hide_rating_div' );

/**
 * Hide rating notice and schedule it to show again after one week
 *
 * AJAX handler that dismisses the rating notice and sets a timestamp
 * to show it again after 7 days.
 *
 * @since 1.0.9
 * @package QUADS
 * @subpackage Admin/Notices
 * @return void
 */
function quads_hide_rating_notice_week() {
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'quads_hide_rating_nonce' ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Security check failed.', 'quick-adsense-reloaded' ) ) );
        return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'quick-adsense-reloaded' ) ) );
        return;
    }

    $nextweek   = time() + WEEK_IN_SECONDS;
    $human_date = gmdate( 'Y-m-d h:i:s', $nextweek );
    
    update_option( 'quads_date_next_notice', $human_date );
    update_option( 'quads_rating_div', 'yes' );
    
    wp_send_json_success();
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

    $current_date = gmdate( 'Y-m-d h:i:s' );
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
            $message = esc_html__( "WP QUADS and WP QUADS Pro cannot be activated both. We've automatically deactivated WP QUADS.", 'quick-adsense-reloaded' );
        } else {
            //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
            $message = esc_html__( "WP QUADS and WP QUADS Pro cannot be activated both. We've automatically deactivated WP QUADS Pro.", 'quick-adsense-reloaded' );
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
 */function quads_theme_notice() {
    $show_notice = get_option( 'quads_show_theme_notice' );

    if ( false !== $show_notice && 'no' !== $show_notice && quads_is_commercial_theme() ) {
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p>
                <?php
                echo sprintf(
                    /* translators: 1: theme name, 2: plugin name, 3: purchase link, 4: details link, 5: close link */
                    esc_html__( 'Extend the %1$s theme with %2$s! Save time and earn more - Bring your AdSense earnings to the next level. %3$s or %4$s. %5$s', 'quick-adsense-reloaded' ),
                    '<strong>' . esc_html( quads_is_commercial_theme() ) . '</strong>',
                    '<strong>WP QUADS PRO</strong>',
                    '<a href="' . esc_url( 'http://wpquads.com?utm_campaign=adminnotice&utm_source=admin_notice&utm_medium=admin&utm_content=bimber_upgrade_notice' ) . '" target="_blank">' . esc_html__( 'Purchase Now', 'quick-adsense-reloaded' ) . '</a>',
                    '<a href="' . esc_url( 'http://wpquads.com?utm_campaign=free_plugin&utm_source=admin_notice&utm_medium=admin&utm_content=bimber_upgrade_notice' ) . '" target="_blank">' . esc_html__( 'Get Details', 'quick-adsense-reloaded' ) . '</a>',
                    '<p><a href="' . esc_url( admin_url( 'admin.php?page=quads-settings&quads-action=close_upgrade_notice' ) ) . '" class="button">' . esc_html__( 'Close Notice', 'quick-adsense-reloaded' ) . '</a></p>'
                );
                ?>
            </p>
        </div>
        <?php
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
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p><?php echo  '<p><strong>' . esc_html( /* translators: %s: plugin version */  sprintf( __( 'WP QUADS %1$s:', 'quick-adsense-reloaded' ), esc_attr(QUADS_VERSION) ) ) . '</strong> ' . 
                        esc_html__('Update WP QUADS PRO to get custom post type support from', 'quick-adsense-reloaded') . 
                        ' <a href="' . esc_url(admin_url('admin.php?page=quads-settings')) . '">' . esc_html__('General Settings', 'quick-adsense-reloaded') . 
                        '</a>.' . 
                        '<br><br>' . 
                        '<a href="' . esc_url(admin_url('admin.php?page=quads-settings&quads-action=hide_update_notice')) . 
                        '" class="button-primary thankyou" target="_self" title="' . esc_attr__('Close Notice', 'quick-adsense-reloaded') . 
                        '" style="font-weight:bold;">' . esc_html__('Close Notice', 'quick-adsense-reloaded') . 
                        '</a></p>';
 
     ?></p>
        </div> <?php
    } else
    if( !quads_is_advanced() ) {
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p><?php echo '<p><strong>' . esc_html( sprintf( /* translators: %s: plugin version */ __('WP QUADS %1$s:', 'quick-adsense-reloaded'), QUADS_VERSION ) ) . '</strong> ' . 
                        esc_html__('Install', 'quick-adsense-reloaded') . 
                        ' <a href="' . esc_url('http://wpquads.com?utm_campaign=admin_notice&utm_source=admin_notice&utm_medium=admin&utm_content=custom_post_type') . 
                        '" target="_blank">' . esc_html__('WP QUADS PRO', 'quick-adsense-reloaded') . 
                        '</a> ' . esc_html__('to get custom post type support in', 'quick-adsense-reloaded') . 
                        ' <a href="' . esc_url(admin_url('admin.php?page=quads-settings')) . '">' . esc_html__('General Settings', 'quick-adsense-reloaded') . 
                        '</a>.' . 
                        '<br><br>' . 
                        '<a href="' . esc_url(admin_url('admin.php?page=quads-settings&quads-action=hide_update_notice')) . 
                        '" class="button-primary thankyou" target="_self" title="' . esc_attr__('Close Notice', 'quick-adsense-reloaded') . 
                        '" style="font-weight:bold;">' . esc_html__('Close Notice', 'quick-adsense-reloaded') . 
                        '</a></p>';
        ?></p>
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
        ?>
        <div class="notice notice-error">
            <p><?php 
            echo '<p>' . 
            esc_html__('You need to update', 'quick-adsense-reloaded') . 
            ' <strong>' . esc_html__('WP QUADS PRO to version 1.3.6', 'quick-adsense-reloaded') . 
            '</strong> ' . esc_html__('or higher. Your version of', 'quick-adsense-reloaded') . 
            ' <strong>' . esc_html__('WP QUADS Pro', 'quick-adsense-reloaded') . '</strong> ' . 
            esc_html__('is', 'quick-adsense-reloaded') . ' ' . esc_html(QUADS_PRO_VERSION) . 
            '.<br>' . esc_html__('WP QUADS Pro', 'quick-adsense-reloaded') . ' ' . 
            esc_html(QUADS_PRO_VERSION) . ' ' . esc_html__('supports unlimited amount of ads.', 'quick-adsense-reloaded') . 
            '<br>' . esc_html__('Updating requires a valid', 'quick-adsense-reloaded') . 
            ' <a href="' . esc_url('https://wpquads.com/#buy-wpquads?utm_source=plugin_notice&utm_medium=admin&utm_campaign=activate_license') . 
            '" target="_new">' . esc_html__('license key', 'quick-adsense-reloaded') . '</a>.' . 
            '</p>';
        
             ?></p>
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

    // Show update message if previous version was lower than 1.7 - This makes sure that the message is shown for future updates without complicated version number conditions
    if( !empty( $previous_version ) && version_compare( QUADS_VERSION, '1.7.0', '<=' ) ) {
        ?>
        <div class="notice notice-error">
            <p><?php echo '<p>' . esc_html__('This is a huge update! The data structure of WP QUADS has been modified and improved for better performance and great new features.', 'quick-adsense-reloaded') . '<br>' .
                                esc_html__('For the case you\'d experience issues, we made a', 'quick-adsense-reloaded') . ' <a href="' . esc_url(admin_url('?page=quads-settings&tab=help')) . '" target="_self">' . esc_html__('backup of previous WP QUADS data', 'quick-adsense-reloaded') . '</a>. ' .
                                esc_html__('So you can', 'quick-adsense-reloaded') . ' <a href="' . esc_url('https://wpquads.com/docs/install-older-plugin-version/?utm_source=plugin_notice&utm_medium=admin&utm_campaign=install_older_version') . '" target="_new">' . esc_html__('switch back to the previous version', 'quick-adsense-reloaded') . '</a> ' .
                                esc_html__('anytime.', 'quick-adsense-reloaded') . '<br><br>' .
                                esc_html__('Please', 'quick-adsense-reloaded') . ' <a href="' . esc_url('https://wordpress.org/support/plugin/quick-adsense-reloaded') . '" target="_new">' . esc_html__('open first a support ticket', 'quick-adsense-reloaded') . '</a> ' .
                                esc_html__('if you experience any issue.', 'quick-adsense-reloaded') . '</p>';
            ?></p>
            <?php
            echo '<p><a href="' . esc_url(admin_url('admin.php?page=quads-settings&quads-action=hide_update_notice_1_5_3')) . '" class="button-primary" target="_self" title="Close Notice" style="font-weight:bold;">' . esc_html__( 'Close Notice', 'quick-adsense-reloaded' ) . '</a>';
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
        $adsense             = new quads\adsense( $quads_options );
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
        $notice['message'] = "<p><strong>" . esc_html__( 'ADS.TXT couldnt be updated automatically', 'quick-adsense-reloaded') . "</strong><br><br>" . esc_html__( 'Important note: WP QUADS hasnt been able to update your ads.txt file automatically. Please make sure to enter the following line manually into', 'quick-adsense-reloaded' ) . "<strong>" . get_home_path() . "ads.txt</strong>:"
                . "<p>"
                . "<pre>" . esc_html( $viAdsTxtText ) . "<br>"
                . esc_html( $adsenseAdsTxtText )
                . "</pre></p>"
                . esc_html__( "Only by doing so AdSense ads are shown on your site.", 'quick-adsense-reloaded' ) . "</p>";
        $notice['type']    = 'error';
        $notice['action']  = 'quads_ads_txt_error';

        // render blurb
        $adsTxtError = new quads\QUADS_Template( '/includes/admin/views/notices', $notice );
        echo wp_kses_post($adsTxtError->render());
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
    echo '<p>' . esc_html__('Oh No! ', 'quick-adsense-reloaded') . '<strong>' . esc_html__('WP Quads Pro', 'quick-adsense-reloaded') . '</strong> ' . esc_html__('license key is not activated or has been expired. It expires on ', 'quick-adsense-reloaded') . esc_html(date_i18n(get_option('date_format'), strtotime(isset($lic->expires) ? $lic->expires : null, current_time('timestamp')))) . '. ' . esc_html__('Renew or activate your license key to make sure that your (AdSense) ads are shown properly with your WordPress, version ', 'quick-adsense-reloaded') . esc_html($wp_version) . '<br>
    <a href="' . esc_url('http://wpquads.com/checkout/?edd_license_key=' . $licKey . '&utm_campaign=adminnotic123e&utm_source=adminnotice123&utm_medium=admin&utm_content=license-expired') . '" target="_blank" title="' . esc_attr__('Renew your license key', 'quick-adsense-reloaded') . '" class="button"><strong>' . esc_html__('Renew Your License Key Now', 'quick-adsense-reloaded') . '</strong></a> | <a href="' . esc_url(admin_url() . 'admin.php?page=quads-settings&tab=licenses&quads-action=hide_license_expired_notice') . '" title="' . esc_attr__('Renew your license key', 'quick-adsense-reloaded') . '">' . esc_html__('I am aware of possible issues and want to hide this reminder', 'quick-adsense-reloaded') . '</a>';
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

    if ( get_option( 'quads_show_notice_auto_ads' ) === 'no' ) {
        return false;
    }

    // Admin notice after updating WP QUADS
    echo '<div class="quads-notice-gdpr update-nag" style="background-color: black;color: #87c131;padding: 20px;margin-top: 20px;border: 3px solid #87c131;display:block;">';
    echo '<h2 style="color:white;">' . esc_html__('WP QUADS & Google Auto Ads', 'quick-adsense-reloaded') . '</h2>';
    echo '<p>' . esc_html__('WP QUADS Pro adds support for Google Auto Ads', 'quick-adsense-reloaded') . '</p><br>';
    echo '<p>' . esc_html__('Get the Pro plugin from', 'quick-adsense-reloaded') . '<a href="'.esc_url('https://wpquads.com/?utm_source=wp-admin&utm_medium=autoads-notice&utm_campaign=autoads-notice').'" target="_blank" style="color:#87c131;font-weight:500;">wpquads.com</a></p><br>';
    echo '<p><a href="' . esc_url(admin_url('admin.php?page=quads-settings&quads-action=hide_auto_ads_notice')) . '" class="quads_hide_gdpr" title="' . esc_attr__('I got it', 'quick-adsense-reloaded') . '" style="text-decoration:none;color:white;">- ' . esc_html__('I Understand! Do Not Show This Hint Again', 'quick-adsense-reloaded') . ' -</a></p>';
    echo '</div>';
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
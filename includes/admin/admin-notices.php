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
 * @global $mashsb_options Array of all the MASHSB Options
 * @return void
 */
function quads_admin_messages() {
    global $quads_options;

    if( !current_user_can( 'update_plugins' ) ) {
        return;
    }

    quads_theme_notice();

    quads_update_notice();
    
    quads_update_notice_v2();
    
    quads_update_notice_1_5_3();

    if( quads_is_admin_page() ) {
        echo '<div class="notice notice-error" id="wpquads-adblock-notice" style="display:none;">' . sprintf( __( '<strong><p>You need to deactivate your ad blocker to use WP QUADS settings.</strong> Your ad blocker browser extension is removing WP QUADS css ressources and is breaking the settings screen! Deactivating the ad blocker will resolve it. WP QUADS is used on 60.000 websites and is into focus of the big adblocking companies. That\'s the downside of our success but nothing you need to worry about.</p>', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings#quads_settingsgeneral_header' ) . '</div>';
    }
    
    if( !quads_is_any_ad_activated() && quads_is_admin_page() ) {
        echo '<div class="notice notice-warning">' . sprintf( __( '<strong>No ads are activated!</strong> You need to assign at least 1 ad to an ad spot. Fix this in <a href="%s">General Settings</a>! Alternatively you need to use a shortcode in your posts or no ads are shown at all.', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings#quads_settingsgeneral_header' ) . '</div>';
    }

    if( quads_get_active_ads() === 0 && quads_is_admin_page() ) {
        echo '<div class="notice notice-warning">' . sprintf( __( '<strong>No ads defined!</strong> You need to create at least one ad code. Fix this in <a href="%s">ADSENSE CODE</a>.', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings#quads_settingsadsense_header' ) . '</div>';
    }

    if( !quads_is_post_type_activated() && quads_is_admin_page() ) {
        echo '<div class="notice notice-warning">' . sprintf( __( '<strong>No ads are shown - No post type chosen!</strong> You need to select at least 1 post type like <i>blog</i> or <i>page</i>. Fix this in <a href="%s">General Settings</a> or no ads are shown at all.', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings#quads_settingsgeneral_header' ) . '</div>';
    }

    if( isset( $_GET['quads-action'] ) && $_GET['quads-action'] === 'validate' && quads_is_admin_page() && quads_is_any_ad_activated() && quads_is_post_type_activated() && quads_get_active_ads() > 0 ) {
        echo '<div class="notice notice-success">' . sprintf( __( '<strong>No errors detected in WP QUADS settings.</strong> If ads are still not shown read the <a href="%s" target="_blank">troubleshooting guide</a>' ), 'http://wpquads.com/docs/adsense-ads-are-not-showing/?utm_source=plugin&utm_campaign=wpquads-settings&utm_medium=website&utm_term=toplink' ) . '</div>';
    }


    $install_date = get_option( 'quads_install_date' );
    $display_date = date( 'Y-m-d h:i:s' );
    $datetime1 = new DateTime( $install_date );
    $datetime2 = new DateTime( $display_date );
    $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );

   $rate = get_option( 'quads_rating_div', false);
    if( $diff_intrval >= 7 && ($rate === "no" || false === $rate || quads_rate_again() ) ) {
        echo '<div class="quads_fivestar updated " style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);background-color:white;">
    	<p>Awesome, you\'ve been using <strong>WP QUADS</strong> for more than 1 week. <br> May i ask you to give it a <strong>5-star rating</strong> on Wordpress? </br>
        This will help to spread its popularity and to make this plugin a better one.
        <br><br>Your help is much appreciated. Thank you very much,<br> ~René Hermenau
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

add_action( 'admin_notices', 'quads_admin_messages' );


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
    $nextweek = time() + (7 * 24 * 60 * 60);
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
    $datetime1 = new DateTime( $rate_again_date );
    $datetime2 = new DateTime( $current_date );
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
    if( !quads_is_extra() ) {
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
function quads_update_notice_v2(){

    if( quads_is_pro_active() && (version_compare( QUADS_PRO_VERSION, '1.3.6', '<' ) ) ) {
        $message = sprintf( __( 'You need to update <strong>WP QUADS PRO to version 1.3.6</strong> or higher. Your version of <strong>WP QUADS Pro</strong> is '.QUADS_PRO_VERSION. '.<br>WP QUADS Pro '.QUADS_PRO_VERSION.' supports unlimited amount of ads. <br>Updating requires a valid <a href="%s" target="_new">license key</a>.', 'quick-adsense-reloaded' ), 'https://wpquads.com/#buy-wpquads?utm_source=plugin_notice&utm_medium=admin&utm_campaign=activate_license' );
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
function quads_update_notice_1_5_3(){

    // do not show anything
    if( false !== get_option( 'quads_hide_update_notice_1_5_3' )) {
        return false;
    }
    
    $previous_version = get_option('quads_version_upgraded_from');
    
    //wp_die(QUADS_VERSION);

    // Show update message if previous version was lower than 1.7 - This makes sure that the message is shown for future updates without complicated version number conditions
    if( !empty($previous_version) && version_compare( QUADS_VERSION, '1.7.0', '<=' ) ) {

        $message = sprintf( __( 'This is a huge update! The data structure of WP QUADS has been modified and improved for better performance and great new features. <br> For the case you\'d experience issues, we made a <a href="%1s" target="_self">backup of previous WP QUADS data</a>. So you can <a href="%2s" target="_new">switch back to the previous version</a> anytime. <br><br>Please <a href="%3s" target="_new">open first a support ticket</a> if you experience any issue.', 'quick-adsense-reloaded' ), admin_url() . '?page=quads-settings&tab=help', 'https://wpquads.com/docs/install-older-plugin-version/?utm_source=plugin_notice&utm_medium=admin&utm_campaign=install_older_version', 'https://wordpress.org/support/plugin/quick-adsense-reloaded' );
        ?>
        <div class="notice notice-error">
            <p><?php echo $message; ?></p>
            <?php
            echo '<p><a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_update_notice_1_5_3" class="button-primary" target="_self" title="Close Notice" style="font-weight:bold;">' . __('Close Notice','quick-adsense-reloaded') . '</a>';
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
function quads_hide_notice_1_5_3(){
      update_option('quads_hide_update_notice_1_5_3', '1');
}
add_action('quads_hide_update_notice_1_5_3', 'quads_hide_notice_1_5_3');

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
        //wp_die(print_r($check));
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
        //wp_die('test');
        return true;
    }
     //wp_die('test1');
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

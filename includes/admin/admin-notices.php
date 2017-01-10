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

    if( !current_user_can( 'update_plugins' ) ){
        return;
    }
    
    quads_theme_notice();
    
    quads_update_notice();

    //quads_plugin_deactivated_notice();
    
    $install_date = get_option( 'quads_install_date' );
    $display_date = date( 'Y-m-d h:i:s' );
    $datetime1 = new DateTime( $install_date );
    $datetime2 = new DateTime( $display_date );
    $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );
    
    
    if( $diff_intrval >= 7 && get_option( 'quads_rating_div' ) == "no" || false === get_option( 'quads_rating_div' ) || quads_rate_again() ) {
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
    update_option( 'quads_date_next_notice', $human_date  );
    update_option( 'quads_rating_div', 'yes'  );
    echo json_encode( array("success") );
    exit;
}
add_action( 'wp_ajax_quads_hide_rating_week', 'quads_hide_rating_notice_week' );

/**
 * Check if admin notice will open again after one week of closing
 * @return boolean
 */
function quads_rate_again(){
        
    $rate_again_date = get_option( 'quads_date_next_notice' );

    if (false === $rate_again_date){
        return false;
    }

    $current_date = date( 'Y-m-d h:i:s' );
    $datetime1 = new DateTime( $rate_again_date );
    $datetime2 = new DateTime( $current_date );
    $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );

    if ($diff_intrval >= 0){
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
function quads_theme_notice(){
    
    $show_notice = get_option('quads_show_theme_notice');
    
        if( false !== $show_notice && 'no' !== $show_notice )  {
            $message = __( '<strong>Extend the <strong>' . quads_is_commercial_theme(). '</strong> theme with <strong>WP QUADS PRO!</strong> Save time and earn more - Bring your AdSense earnings to next level. <a href="http://wpquads.com?utm_campaign=adminnotice&utm_source=admin_notice&utm_medium=admin&utm_content=bimber_upgrade_notice" target="_blank"> Purchase Now</a> or <a href="http://wpquads.com?utm_campaign=free_plugin&utm_source=admin_notice&utm_medium=admin&utm_content=bimber_upgrade_notice" target="_blank">Get Details</a></strong>', 'quick-adsense-reloaded' );
        ?>
        <div class="updated notice is-dismissible" style="border-left: 4px solid #ffba00;">
            <p><?php echo $message; ?></p>
        </div> <?php
        update_option ('quads_show_theme_notice', 'no');
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

    if( (version_compare( QUADS_VERSION, '1.3.9', '>=' ) ) && quads_is_advanced() && (version_compare( QUADS_PRO_VERSION, '1.3.0', '<' ) ) ) {
        $message = sprintf( __( '<strong>WP QUADS ' . QUADS_VERSION . ': <strong> Update WP QUADS PRO to get custom post type support from <a href="%s">General Settings</a>.', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings' );
        $message .= '<br><br><a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_update_notice" class="button-primary thankyou" target="_self" title="Close Notice" style="font-weight:bold;">Close Notice</a>';
?>
                        <div class="updated notice" style="border-left: 4px solid #ffba00;">
                            <p><?php echo $message; ?></p>
                        </div> <?php
        //update_option ('quads_show_update_notice', 'no');
    } else
        if( !quads_is_advanced() ) {
        $message = sprintf( __( '<strong>WP QUADS ' . QUADS_VERSION . ': <strong> Install <a href="%1s" target="_blank">WP QUADS PRO</a> to get custom post type support from <a href="%2s">General Settings</a>.', 'quick-adsense-reloaded' ), 'http://wpquads.com?utm_campaign=admin_notice&utm_source=admin_notice&utm_medium=admin&utm_content=custom_post_type', admin_url() . 'admin.php?page=quads-settings' );
        $message .= '<br><br><a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_update_notice" class="button-primary thankyou" target="_self" title="Close Notice" style="font-weight:bold;">Close Notice</a>';
?>
                        <div class="updated notice" style="border-left: 4px solid #ffba00;">
                            <p><?php echo $message; ?></p>
                        </div>
        <?php
    }
}

function quads_hide_notice(){
    update_option ('quads_show_update_notice', 'no');
}
add_action('quads_hide_update_notice', 'quads_hide_notice', 10);

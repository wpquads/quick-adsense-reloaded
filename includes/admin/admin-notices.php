<?php
/**
 * Admin Notices
 *
 * @package     QUADS
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin Messages
 *
 * @since 0.9.0
 * @global $quads_options array of all the QUADS Options
 * @return void
 */
function quads_admin_messages() {
	global $quads_options;
        
        if ( isset( $_GET['quads-message'] ) && 'settings-imported' == $_GET['quads-message'] && current_user_can( 'update_plugins' ) ) {
		 add_settings_error( 'quads-notices', 'quads-settings-imported', __( 'The settings have been imported.', 'quick-adsense-reloaded' ), 'updated' );
	}
        if ( quads_check_quick_adsense_status() && current_user_can( 'update_plugins' ) ) {

                echo '<div class="error">';
			echo '<p>' . __( 'You have to disable <strong> Quick AdSense plugin</strong> first to be able to use Quick AdSense Reloaded without issues. <br> You should not use both plugins enabled! Try if you can migrate the settings from Quick AdSense via "Import/Export"', 'quick-adsense-reloaded' ) . '</p>';
		echo '</div>';
	}
     
        
        // Show rating notice only to administrator
        if (!current_user_can('update_plugins'))
        return;
        
        $install_date = get_option('quads_install_date');
        $display_date = date('Y-m-d h:i:s');
	$datetime1 = new DateTime($install_date);
	$datetime2 = new DateTime($display_date);
	$diff_intrval = round(($datetime2->format('U') - $datetime1->format('U')) / (60*60*24));
        //if($diff_intrval >= 7 && get_option('quads_rating_div')=="no")
        if($diff_intrval >= 6 && get_option('quads_rating_div')== "no")
    {
	 echo '<div class="quads_fivestar updated settings-error notice style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);">
    	<p>Awesome, you\'ve been using <strong>Quick AdSense Reloaded</strong> for more than 1 week. <br> May i ask you to give it a rating on Wordpress? <br>
        This will help to spread its popularity and to make this plugin a better one.
        <br><br>Your help is much appreciated. Thank you very much,<br> ~René Hermenau
        <ul>
            <li><a href="https://wordpress.org/support/view/plugin-reviews/quick-adsense-reloaded" class="thankyou" target="_new" title="Ok, you deserved it" style="font-weight:bold;">Ok, you deserved it</a></li>
            <li><a href="javascript:void(0);" class="quadsHideRating" title="I already did" style="font-weight:bold;">I already did</a></li>
            <li><a href="javascript:void(0);" class="quadsHideRating" title="No, not good enough" style="font-weight:bold;">No, not good enough</a></li>
        </ul>
    </div>
    <script>
    jQuery( document ).ready(function( $ ) {

    jQuery(\'.quadsHideRating\').click(function(){
        var data={\'action\':\'hide_rating\'}
             jQuery.ajax({
        
        url: "'.admin_url( 'admin-ajax.php' ).'",
        type: "post",
        data: data,
        dataType: "json",
        async: !0,
        success: function(e) {
            if (e=="success") {
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
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
 * 
 * @return json string
 * 
 */

function quads_hide_rating_div(){
    update_option('quads_rating_div','yes');
    echo json_encode(array("success")); exit;
}
add_action('wp_ajax_hide_rating','quads_hide_rating_div');

/* Hide the update notice div
 * 
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
 * 
 * @return json string
 * 
 */
function quads_hide_update_div(){
    update_option('quads_update_notice','yes');
    echo json_encode(array("success")); exit;
}
add_action('wp_ajax_hide_update','quads_hide_update_div');

/**
 * Admin Add-ons Notices
 *
 * @since 0.9.0
 * @return void
*/
function quads_admin_addons_notices() {
	add_settings_error( 'quads-notices', 'quads-addons-feed-error', __( 'There seems to be an issue with the server. Please try again in a few minutes.', 'quick-adsense-reloaded' ), 'error' );
	settings_errors( 'quads-notices' );
}

/**
 * Dismisses admin notices when Dismiss links are clicked
 *
 * @since 0.9.0
 * @return void
*/
function quads_dismiss_notices() {

	$notice = isset( $_GET['quads_notice'] ) ? $_GET['quads_notice'] : false;
	if( ! $notice )
		return; // No notice, so get out of here

	update_user_meta( get_current_user_id(), '_quads_' . $notice . '_dismissed', 1 );
      
	wp_redirect( esc_url(remove_query_arg( array( 'quads_action', 'quads_notice' ) ) ) ); exit;

}
add_action( 'quads_dismiss_notices', 'quads_dismiss_notices' );

/*
 * Show big colored update information below the official update notification in /wp-admin/plugins
 * @since 0.9.0
 * @return void
 * 
 */

function quads_plugin_update_message( $args ) {
    $transient_name = 'quads_upgrade_notice_' . $args['Version'];

    if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {

      $response = wp_remote_get( 'https://plugins.svn.wordpress.org/quick-adsense-reloaded/trunk/readme.txt' );

      if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {

        // Output Upgrade Notice
        $matches        = null;
        $regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( quads_VERSION ) . '\s*=|$)~Uis';
        $upgrade_notice = '';

        if ( preg_match( $regexp, $response['body'], $matches ) ) {
          $version        = trim( $matches[1] );
          $notices        = (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );
          
          if ( version_compare( quads_VERSION, $version, '<' ) ) {

            $upgrade_notice .= '<div class="quads_plugin_upgrade_notice" style="padding:10px;background-color:#58C1FF;color: #FFF;">';

            foreach ( $notices as $index => $line ) {
              $upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}" style="text-decoration:underline;color:#ffffff;">${1}</a>', $line ) );
            }

            $upgrade_notice .= '</div> ';
          }
        }

        set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
      }
    }

    echo wp_kses_post( $upgrade_notice );
  }
 add_action ( "in_plugin_update_message-quick-adsense-reloaded/quick-adsense-reloaded.php", 'quads_plugin_update_message'  );

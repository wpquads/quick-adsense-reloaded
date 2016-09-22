<?php
/**
 * Admin Add-ons
 *
 * @package     QUADS
 * @subpackage  Admin/Add-ons
 * @copyright   Copyright (c) 2015, Rene Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add-ons
 *
 * Renders the add-ons content.
 *
 * @since 1.1.8
 * @return void
 */
function quads_add_ons_page() {
	ob_start(); ?>
	<div class="wrap_" id="quads-add-ons">
            <h2 class="quads-h1">WP QUADS Premium Integrations: </h2>
            <div style="border: 2px solid white;padding: 20px;margin-bottom:20px;">
		<h2 class="quads-h2">
			<span class="quads-heading-pro"><?php _e( 'WP QUADS PRO', 'quick-adsense-reloaded' );  ?></span><?php _e( ', save time and earn more with next level AdSense integration!', 'quick-adsense-reloaded' );  ?> 
		</h2>
		<h2 style="display:none;"><?php _e( 'Mobile and Responsive AdSense Support ', 'quick-adsense-reloaded' ); ?></h2>  
                <li><strong>Responsive Ads</strong> - <?php _e('individual AdSense sizes for Desktop, Phone and Tablet devices.','quick-adsense-reloaded' ); ?></li>
                <li><strong>Visibility Conditionals</strong> - <?php _e('select if AdSense is visible on mobile, tablet or desktop', 'quick-adsense-reloaded' ); ?></li>
                <li><strong>Automatic Mode</strong> - <?php _e('let the plugin detect optimal ad size on all devices.', 'quick-adsense-reloaded' ); ?></li>
                <li><strong>High Performance</strong> - <?php _e('this plugin keeps the speed of your site', 'quick-adsense-reloaded' ); ?></li>
                <a href="http://wpquads.com/?utm_source=wpquads&utm_medium=addon_page&utm_term=click-quads-pro&utm_campaign=wpquads" target="_blank" class="quads-button green">Buy WP QUADS Pro</a>
                <a href="<?php echo admin_url(); ?>admin.php?page=quads-settings" target="_self" style="margin-left:30px;">Skip - Go to Settings</a>
                <div class="quads-footer"> <?php _e('Comes with our 30-day no questions asked money back guarantee','quick-adsense-reloaded'); ?></div>
            </div>
            <div style="float:left;width:50%;border: 2px solid white;padding: 20px;display:none;">
		<h2>
			<?php _e( 'Clickfraud Monitor Integration', 'quick-adsense-reloaded' ); ?>
			<!--&nbsp;&mdash;&nbsp;<a href="https://www.quadsshare.net" class="button-primary" title="<?php _e( 'Visit Website', 'quick-adsense-reloaded' ); ?>" target="_blank"><?php _e( 'See Details', 'quick-adsense-reloaded' ); ?></a>-->
		</h2>
		<h2><?php _e( 'Protect your AdSense Account ', 'quick-adsense-reloaded' ); ?></h2>  
                <p><?php _e('Monitor and protect all your advertisements on your site.<br> Click protection for Google AdSense and other pay per click vendors.','quick-adsense-reloaded' ); ?></p>
                <p><?php _e('Fully integrated in WP<strong>QUADS</strong> or completely independant and compatible with <br>any other AdSense Plugin, even with manual inserted ads.', 'quick-adsense-reloaded' ); ?></p>
                <a href="http://demo.clickfraud-monitoring.com/pricing/?utm_source=wpquads&utm_medium=addon_page&utm_term=click-clickfraud&utm_campaign=wpquads" target="_blank" class="button button-primary">See Demo</a>
                <a href="<?php echo admin_url(); ?>admin.php?page=quads-settings" target="_blank" class="button">Maybe later - Go to Settings</a>

            </div>
	</div>
	<?php
	echo ob_get_clean();
}

/**
 * Add-ons Get Feed
 *
 * Gets the add-ons page feed.
 *
 * @since 0.9.0
 * @return void
 */
function quads_add_ons_get_feed() {
	if ( false === ( $cache = get_transient( 'quadsshare_add_ons_feed' ) ) ) {
		$feed = wp_remote_get( 'https://www.quadsshare.net/?feed=addons', array( 'sslverify' => false ) );
		if ( ! is_wp_error( $feed ) ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( 'quadsshare_add_ons_feed', $cache, 3600 );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the Quick AdSense Reloaded addon list from the server. Please try again later.', 'quick-adsense-reloaded' ) . '
                                   <br>Visit instead the Quick AdSense Reloaded Addon Website <a href="https://www.quadsshare.net" class="button-primary" title="Quick AdSense Reloaded Add ons" target="_blank"> Get Add-Ons  </a></div>';
		}
	}
	return $cache;
}
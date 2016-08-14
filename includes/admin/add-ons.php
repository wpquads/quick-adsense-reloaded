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
	<div class="wrap" id="quads-add-ons">
            <p>Whats New in v.<?php echo QUADS_VERSION;?>?</p>
		<h1>
			<?php _e( 'Clickfraud Monitor Integration', 'quick-adsense-reloaded' ); ?>
			<!--&nbsp;&mdash;&nbsp;<a href="https://www.quadsshare.net" class="button-primary" title="<?php _e( 'Visit Website', 'quick-adsense-reloaded' ); ?>" target="_blank"><?php _e( 'See Details', 'quick-adsense-reloaded' ); ?></a>-->
		</h1>
		<h2><?php _e( 'Protect your AdSense Account from being banned! ', 'quick-adsense-reloaded' ); ?></h2>  
                <p><?php _e('Monitor and protect all your advertisements on your site.<br> Click protection for Google AdSense and other pay per click vendors.','quick-adsense-reloaded' ); ?></p>
                <p><?php _e('Fully integrated in WP<strong>QUADS</strong> or completely independant and compatible with <br>any other AdSense Plugin, even with manual inserted ads.', 'quick-adsense-reloaded' ); ?></p>
                <a href="http://demo.clickfraud-monitoring.com/pricing/?utm_source=wpquads&utm_medium=addon_page&utm_term=click&utm_campaign=wpquads" target="_blank" class="button button-primary">Get Add-On</a>
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
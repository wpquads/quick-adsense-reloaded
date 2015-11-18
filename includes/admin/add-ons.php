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
		<h2>
			<?php _e( 'Add Ons for Quick AdSense Reloaded', 'quads' ); ?>
			<!--&nbsp;&mdash;&nbsp;<a href="https://www.quadsshare.net" class="button-primary" title="<?php _e( 'Visit Website', 'quads' ); ?>" target="_blank"><?php _e( 'See Details', 'quads' ); ?></a>-->
		</h2>
		<p><?php _e( 'These add-ons extend the functionality of Quick AdSense Reloaded.', 'quads' ); ?></p>
		<?php //echo quads_add_ons_get_feed(); ?>
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
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the Quick AdSense Reloaded addon list from the server. Please try again later.', 'quads' ) . '
                                   <br>Visit instead the Quick AdSense Reloaded Addon Website <a href="https://www.quadsshare.net" class="button-primary" title="Quick AdSense Reloaded Add ons" target="_blank"> Get Add-Ons  </a></div>';
		}
	}
	return $cache;
}
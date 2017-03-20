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

add_action('admin_head', 'quads_admin_inline_css');

/**
 * Create admin inline css to bypass adblock plugin which is blocking wp quads css ressources
 */
function quads_admin_inline_css() {
    if (!quads_is_addon_page()){
        return false;
    }
  echo '<style>
.quads-button.green {
    display: inline-block;
    background-color: #83c11f;
    padding: 10px;
    min-width: 170px;
    color: white;
    font-size: 16px;
    text-decoration: none;
    text-align: center;
    margin-top: 20px;
}
#quads-add-ons li {
    font-size: 18px;
    line-height: 29px;
    position: relative;
    padding-left: 23px;
    list-style: none!important;
}
.quads-heading-pro {
    color: #83c11f;
    font-weight: bold;
}
.quads-h2 {
    margin-top: 0px;
    margin-bottom: 1.2rem;
    font-size: 30px;
    line-height: 2.5rem;
}
#quads-add-ons li:before {
    width: 1em;
    height: 100%;
    background: url(data:image/svg+xml;charset=utf8,%3Csvg%20width%3D%221792%22%20height%3D%221792%22%20viewBox%3D%220%200%201792%201792%22%20xmlns%3D%22http%3A%2F%2Fwww%2Ew3%2Eorg%2F2000%2Fsvg%22%3E%3Cpath%20fill%3D%22%2377B227%22%20d%3D%22M1671%20566q0%2040%2D28%2068l%2D724%20724%2D136%20136q%2D28%2028%2D68%2028t%2D68%2D28l%2D136%2D136%2D362%2D362q%2D28%2D28%2D28%2D68t28%2D68l136%2D136q28%2D28%2068%2D28t68%2028l294%20295%20656%2D657q28%2D28%2068%2D28t68%2028l136%20136q28%2028%2028%2068z%22%2F%3E%3C%2Fsvg%3E) left .4em no-repeat;
    background-size: contain;
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    color: #77b227;
}
.quads-h1 {
    font-size: 2.75em;
    margin-bottom: 1.35rem;
    font-size: 2.5em;
    line-height: 3.68rem;
    letter-spacing: normal;
}
#quads-add-ons h2 {
    margin: 0 0 15px;
}
#quads-add-ons .quads-footer {
    clear: both;
    margin-top: 20px;
    font-style: italic;
}
  </style>';
}

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
                <li><strong>Visibility Conditions</strong> - <?php _e('decide if AdSense is visible on mobile, tablet or desktop', 'quick-adsense-reloaded' ); ?></li>
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
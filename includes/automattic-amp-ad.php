<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Automattic AMP Functions
 *
 * @package     QUADS
 * @subpackage  Includes/automattic-amp-ad
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.9
 */

add_action( 'amp_post_template_head', 'quads_amp_add_amp_ad_js' );
function quads_amp_add_amp_ad_js( $amp_template ) {
   global $quads_options;
   
   if (isset($quads_options['disableAmpScript'])){
      return false;
   } // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion, WordPress.WP.EnqueuedResources.NonEnqueuedScript
    wp_enqueue_script( 'quads-ampproject-js', 'https://cdn.ampproject.org/v0/amp-ad-0.1.js', array(), QUADS_VERSION, false );
}
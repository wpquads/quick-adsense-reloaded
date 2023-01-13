<?php
/**
 * Quicktags functions
 *
 * @package     QUADS
 * @subpackage  Functions/Quicktags
 * @copyright   Copyright (c) 2015, René Hermenau, Lukasz Wesolowksi
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.6
 */
add_filter( 'content_edit_pre', 'quads_strip_quicktags_from_content' );
add_filter( 'content_save_pre', 'quads_strip_quicktags_from_content' );
/**
 * Removes all quicktags from content, but only if their config is already stored in post meta
 *
 * @param string $content
 * @return string Filtered content
 */
function quads_strip_quicktags_from_content ( $content ) {
	$ads_visibility_config = get_post_meta( get_the_ID(), '_quads_config_visibility', true );
	// if config exists, quicktags are handled via metabox
	// so we don't need them anymore in the content
	if ( $ads_visibility_config ) {
		$content = quads_strip_quicktags( $content );
	}
	return $content;
}
/**
 * Returns an array of all quicktags found in content
 *
 * @param string $content
 * @return array List of quicktags
 */
function quads_get_quicktags_from_content ( $content ) {
	$found = array();
	$quicktags = quads_quicktag_list();
	// we can use preg_match instead of multiple calls of strpos(),
	// but strpos is much faster and for such a small array should still be faster than preg_match()
	foreach ( $quicktags as $id => $label ) {
		if ( false !== strpos( $content, '<!--' . $id . '-->' ) ) {
			$found[ $id ] = 1;
		}
	}
	return $found;
}


/**
 * Removes all quicktags from content
 *
 * @param string $content
 * @return string Filtered content
 */
function quads_strip_quicktags ( $content ) {
	$quicktags = quads_quicktag_list();
	foreach ( $quicktags as $id => $label ) {
		$content = str_replace( '<!--'. $id .'-->', '', $content );
	}
	return $content;
}
/**
 * Returns list of all allowed quicktags
 *
 * @return array List of quicktags
 */
function quads_quicktag_list () {
	return apply_filters( 'quads_quicktag_list', array(
		'NoAds' 		=> __( 'Hide all ads on page', 'quick-adsense-reloaded' ),
		'OffDef'		=> __( 'Hide default ads, use manually placed ads', 'quick-adsense-reloaded' ),
		'OffWidget'		=> __( 'Hide all ads in sidebar', 'quick-adsense-reloaded' ),
		'OffBegin'		=> __( 'Hide ad on beginning', 'quick-adsense-reloaded' ),
		'OffMiddle'		=> __( 'Hide ad in middle', 'quick-adsense-reloaded' ),
		'OffEnd'		=> __( 'Hide ad on end', 'quick-adsense-reloaded' ),
		'OffAfMore'		=> __( 'Hide ad after MoreTag', 'quick-adsense-reloaded' ),
		'OffBfLastPara'         => __( 'Hide ad before last paragraph', 'quick-adsense-reloaded' ),
	) );
}
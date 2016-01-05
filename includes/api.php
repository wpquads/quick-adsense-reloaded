<?php

/**
 * API Functions allow creation of custom ad positions
 *
 * @package     QUADS
 * @subpackage  Functions/API
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.4
 */

/**
 * Register an ad position.
 *
 * @param array $args   Location settings
 */
function quads_register_ad( $args ) {
    global $quads_options;

    $defaults = array(
        'location'      => '',
        'description'   => ''
    );

    $args = wp_parse_args( $args, $defaults );

    if ( empty( $args['location'] ) ) {
        return;
    }

    if ( ! isset( $quads_options['locations'] ) ) {
        $quads_options['locations'] = array();
    }

    $quads_options['locations'][ $args['location'] ] = $args;
}

/**
 * Whether a registered ad location has an ad assigned to it.
 *
 * @param string $location      Location id
 * @return bool
 */
function quads_has_ad( $location ) {
    global $quads_options;

    $result = false;

    if ( isset( $quads_options['locations'] ) && isset( $quads_options['locations'][ $location ] ) ) {
        $location_settings = $quads_options['location_settings'][ $location ];

        if ( isset( $location_settings['status'] ) && $location_settings['status'] && ! empty( $location_settings['ad'] ) ) {
            $result = true;
        }
    }

    if (
        is_feed() ||
        ( is_single()           && ! isset( $quads_options['visibility']['AppPost'] ) )     ||
        ( is_page()             && ! isset($quads_options['visibility']['AppPage'] ) )      ||
        ( is_home()             && ! isset( $quads_options['visibility']['AppHome'] ) )     ||
        ( is_category()         && ! isset( $quads_options['visibility']['AppCate'] ) )     ||
        ( is_archive()          && ! isset($quads_options['visibility']['AppArch'] ) )      ||
        ( is_tag()              && ! isset($quads_options['visibility']['AppTags'] ) )      ||
        ( is_user_logged_in()   &&   isset($quads_options['visibility']['AppLogg'] ) )
    ) {
        $result = false;
    }

    /**
     * Filter whether an ad is assigned to the specified location.
     */
    return apply_filters( 'quads_has_ad', $result, $location );
}

/**
 * Display an ad
 *
 * @param array $args       Displaying options
 * @return string|void      Ad code or none if echo set to true
 */
function quads_ad( $args ) {
    $defaults = array(
        'location'  => '',
        'echo'      => true,
    );

    $args = wp_parse_args( $args, $defaults );

    if ( ! quads_has_ad( $args['location'] ) ) {
        return '';
    }

    global $quads_options;

    $location_settings = quads_get_ad_location_settings( $args['location'] );

    $code = $quads_options['ad' . $location_settings['ad'] ]['code'];

    if ( $args['echo'] ) {
        echo $code;
    } else {
        return $code;
    }
}

/**
 * Return location settings.
 *
 * @param string $location      Location id
 * @return array
 */
function quads_get_ad_location_settings( $location ) {
    global $quads_options;

    $result = array(
        'status'    => false,
        'ad'        => '',
    );

    if ( isset( $quads_options['locations'] ) && isset( $quads_options['locations'][ $location ] ) ) {
        $result = wp_parse_args( $quads_options['location_settings'][ $location ], $result );
    }

    return $result;
}


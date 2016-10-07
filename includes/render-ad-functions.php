<?php

/**
 * Render Ad Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Render Ad Functions
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Render the adsense code
 * 
 * @param1 int the ad id 
 * @param2 string $string The adsense code
 * @param3 bool True when function is called from widget
 * 
 * @todo create support for widgets
 * @return string HTML js adsense code
 */
function quads_render_ad( $id, $string, $widget = false ) {
    global $quads_options;

    $string = apply_filters('quads_render_ad', $string);
    
    // Create the global id
    $id = 'ad' . $id;

    // Return empty string
    if( empty( $id ) || empty( $string ) ) {
        return '';
    }

    // Return the original ad code if its no adsense code
    if( !quads_is_adsense( $string ) ) {
        return quads_render_normal_ad($id, $string);
    }

    // Return the original ad code if its called from widget
    if( $widget === true ) {
        return quads_render_normal_ad($string);
    }

    // Return ad code as it is when we have no advanced settings
    if( !quads_is_advanced() ) {
        return quads_render_normal_ad($string);
    }

    
    // Render Google AdSense async ads
    return quads_render_google_async($id);


}


/**
 * Render non adsense ads
 * 
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_normal_ad($id, $string ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Ad --> \n\n";

    $html .= '<script type="text/javascript" data-cfasync="false">' . "\n";
    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";


    if( !isset( $quads_options[$id]['desktop'] ) ) {
$html .= 'if ( quads_screen_width >= 1140 ) {
/* desktop monitors */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . $string . '\');
}';
    }


    if( !isset( $quads_options[$id]['tablet_landscape'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_height'] ) ) {
$html .= 'if ( quads_screen_width >= 1019  && quads_screen_width < 1140 ) {
/* landscape tablets */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . $string . '\');
}';
    }


    if( !isset( $quads_options[$id]['tablet_portrait'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_height'] ) ) {
$html .= 'if ( quads_screen_width >= 768  && quads_screen_width < 1019 ) {
/* portrait tablets */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . $string . '\');
}';
    }

    if( !isset( $quads_options[$id]['phone'] ) and ! empty( $default_ad_sizes[$id]['phone_width'] ) and ! empty( $default_ad_sizes[$id]['phone_height'] ) ) {
$html .= 'if ( quads_screen_width < 768 ) {
/* Phones */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . $string . '\');
}';
    }

    $html .= '</script>' . "\n";

    $html .= "\n <!-- end WP QUADS --> \n\n";
    
    return apply_filters('quads_render_normal_ad', $html);
}
 

/**
 * Render Google async ad
 * 
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_google_async( $id ) {
    global $quads_options;
    
    
    // Create CSS
    $bgcolor = 'background-color:#ffffff;';
    
    // Default ad sizes - Option: Auto
    $default_ad_sizes[$id] = array(
        'desktop_width' => '300',
        'desktop_height' => '250',
        'tbl_landscape_width' => '300',
        'tbl_landscape_height' => '250',
        'tbl_portrait_width' => '300',
        'tbl_portrait_height' => '250',
        'phone_width' => '300',
        'phone_height' => '250'
    );

    // Overwrite default values if there are ones
    // Desktop big ad
    if( !empty( $quads_options[$id]['desktop_size'] ) && $quads_options[$id]['desktop_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['desktop_size'] );
        $default_ad_sizes[$id]['desktop_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['desktop_height'] = $ad_size_parts[1];
    }


    //tablet landscape
    if( !empty( $quads_options[$id]['tbl_lands_size'] ) && $quads_options[$id]['tbl_lands_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_lands_size'] );
        $default_ad_sizes[$id]['tbl_landscape_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['tbl_landscape_height'] = $ad_size_parts[1];
    }


    //tablet portrait
    if( !empty( $quads_options[$id]['tbl_portr_size'] ) && $quads_options[$id]['tbl_portr_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_portr_size'] );
        $default_ad_sizes[$id]['tbl_portrait_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['tbl_portrait_height'] = $ad_size_parts[1];
    }


    //phone
    if( !empty( $quads_options[$id]['phone_size'] ) && $quads_options[$id]['phone_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['phone_size'] );
        $default_ad_sizes[$id]['phone_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['phone_height'] = $ad_size_parts[1];
    }


    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";

    //google async script
    $html .= '<script async data-cfasync="false" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';

    $html .= '<script type="text/javascript" data-cfasync="false">' . "\n";
    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";


    if( !isset( $quads_options[$id]['desktop'] ) and ! empty( $default_ad_sizes[$id]['desktop_width'] ) and ! empty( $default_ad_sizes[$id]['desktop_height'] ) ) {
$html .= 'if ( quads_screen_width >= 1140 ) {
/* desktop monitors */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['desktop_width'] . 'px;height:' . $default_ad_sizes[$id]['desktop_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
    }


    if( !isset( $quads_options[$id]['tablet_landscape'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_height'] ) ) {
$html .= 'if ( quads_screen_width >= 1019  && quads_screen_width < 1140 ) {
/* landscape tablets */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['tbl_landscape_width'] . 'px;height:' . $default_ad_sizes[$id]['tbl_landscape_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
    }


    if( !isset( $quads_options[$id]['tablet_portrait'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_height'] ) ) {
$html .= 'if ( quads_screen_width >= 768  && quads_screen_width < 1019 ) {
/* portrait tablets */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['tbl_portrait_width'] . 'px;height:' . $default_ad_sizes[$id]['tbl_portrait_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
    }

    if( !isset( $quads_options[$id]['phone'] ) and ! empty( $default_ad_sizes[$id]['phone_width'] ) and ! empty( $default_ad_sizes[$id]['phone_height'] ) ) {
$html .= 'if ( quads_screen_width < 768 ) {
/* Phones */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['phone_width'] . 'px;height:' . $default_ad_sizes[$id]['phone_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
    }

    $html .= '</script>' . "\n";

    $html .= "\n <!-- end WP QUADS --> \n\n";
    
    return apply_filters('quads_render_adsense_async', $html);
}

/**
 * Render Google normal ad
 * 
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_google_normal( $id ) {
    global $quads_options;
    
    // Default ad sizes
    $default_ad_sizes[$id] = array(
        'desktop_width' => '300',
        'desktop_height' => '250',
        'tbl_landscape_width' => '300',
        'tbl_landscape_height' => '250',
        'tbl_portrait_width' => '300',
        'tbl_portrait_height' => '250',
        'phone_width' => '300',
        'phone_height' => '250'
    );

    // Overwrite default values if there are ones
    // Desktop big ad
    if( !empty( $quads_options[$id]['desktop_size'] ) && $quads_options[$id]['desktop_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['desktop_size'] );
        $default_ad_sizes[$id]['desktop_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['desktop_height'] = $ad_size_parts[1];
    }


    //tablet landscape
    if( !empty( $quads_options[$id]['tbl_lands_size'] ) && $quads_options[$id]['tbl_lands_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_lands_size'] );
        $default_ad_sizes[$id]['tbl_landscape_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['tbl_landscape_height'] = $ad_size_parts[1];
    }


    //tablet portrait
    if( !empty( $quads_options[$id]['tbl_portr_size'] ) && $quads_options[$id]['tbl_portr_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_portr_size'] );
        $default_ad_sizes[$id]['tbl_portrait_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['tbl_portrait_height'] = $ad_size_parts[1];
    }


    //phone
    if( !empty( $quads_options[$id]['phone_size'] ) && $quads_options[$id]['phone_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['phone_size'] );
        $default_ad_sizes[$id]['phone_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['phone_height'] = $ad_size_parts[1];
    }


    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";

    
    //google async script
    $html .= '<script async data-cfasync="false" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';

    $html .= '<script type="text/javascript" data-cfasync="false">' . "\n";
    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";


    if( !isset( $quads_options[$id]['desktop'] ) and ! empty( $default_ad_sizes[$id]['desktop_width'] ) and ! empty( $default_ad_sizes[$id]['desktop_height'] ) ) {
$html .= 'if ( quads_screen_width >= 1140 ) {
/* desktop monitors */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['desktop_width'] . 'px;height:' . $default_ad_sizes[$id]['desktop_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
    }


    if( !isset( $quads_options[$id]['tablet_landscape'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_height'] ) ) {
$html .= 'if ( quads_screen_width >= 1019  && quads_screen_width < 1140 ) {
/* landscape tablets */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['tbl_landscape_width'] . 'px;height:' . $default_ad_sizes[$id]['tbl_landscape_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
    }


    if( !isset( $quads_options[$id]['tablet_portrait'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_height'] ) ) {
$html .= 'if ( quads_screen_width >= 768  && quads_screen_width < 1019 ) {
/* portrait tablets */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['tbl_portrait_width'] . 'px;height:' . $default_ad_sizes[$id]['tbl_portrait_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
    }

    if( !isset( $quads_options[$id]['phone'] ) and ! empty( $default_ad_sizes[$id]['phone_width'] ) and ! empty( $default_ad_sizes[$id]['phone_height'] ) ) {
$html .= 'if ( quads_screen_width < 768 ) {
/* Phones */
document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['phone_width'] . 'px;height:' . $default_ad_sizes[$id]['phone_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
    }

    $html .= '</script>' . "\n";

    $html .= "\n <!-- end WP QUADS --> \n\n";
    
    return apply_filters('quads_render_adsense_normal', $html);
}

//
//function quads_get_ad_type(){
//    
//}
//
//function quads_render_desktop(){
//    
//}
//
//function quads_render_tablet_horizontal(){
//    
//}
//
//function quads_render_tablet_vertical(){
//    
//}
//
//function quads_render_phone(){
//    
//}


/**
 * Check if ad code is adsense or not
 * 
 * @param string $string ad code
 * @return boolean
 */
function quads_is_adsense( $string ) {
    if( strpos( $string, 'googlesyndication.com' ) !== false ) {
        return true;
    }
    return false;
}

//
//function quads_is_google_async(){
//    if( preg_match( '/googlesyndication.com/', $values['code'] ) ) {
//        return true;
//    }
//    return false;
//}




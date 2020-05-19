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
 * @param1 string the ad id  => ad1, ad2, ad3 etc
 * @param2 string $string The adsense code
 * @param3 bool True when function is called from widget
 * 
 * @todo create support for widgets
 * @return string HTML js adsense code
 */
function quads_render_ad( $id, $string, $widget = false ) {
    
    // Return empty string
    if( empty( $id ) ) {
        return '';
    }
    
    
    if (quads_is_amp_endpoint()){
        return quads_render_amp($id);
    }
    

    // Return the original ad code if it's no adsense code
    if( false === quads_is_adsense( $id, $string ) && !empty( $string ) ) {
        // allow use of shortcodes in ad plain text content
        $string = quadsCleanShortcode('quads', $string);
        //wp_die('t1');
        return apply_filters( 'quads_render_ad', $string );
    }

    // Return the adsense ad code
    if( true === quads_is_adsense( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_google_async( $id ) );
    }

    // Return empty string
    return '';
}

/**
 * Render Google async ad
 * 
 * @global array $quads_options
 * @param int $id
 * @return html
 */
$loaded_lazy_load = '';
function quads_render_google_async( $id ) {
    global $quads_options,$loaded_lazy_load;
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
    if( !empty( $quads_options['ads'][$id]['desktop_size'] ) && $quads_options['ads'][$id]['desktop_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options['ads'][$id]['desktop_size'] );
        $default_ad_sizes[$id]['desktop_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['desktop_height'] = $ad_size_parts[1];
    }


    //tablet landscape
    if( !empty( $quads_options['ads'][$id]['tbl_lands_size'] ) && $quads_options['ads'][$id]['tbl_lands_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options['ads'][$id]['tbl_lands_size'] );
        $default_ad_sizes[$id]['tbl_landscape_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['tbl_landscape_height'] = $ad_size_parts[1];
    }


    //tablet portrait
    if( !empty( $quads_options['ads'][$id]['tbl_portr_size'] ) && $quads_options['ads'][$id]['tbl_portr_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options['ads'][$id]['tbl_portr_size'] );
        $default_ad_sizes[$id]['tbl_portrait_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['tbl_portrait_height'] = $ad_size_parts[1];
    }


    //phone
    if( !empty( $quads_options['ads'][$id]['phone_size'] ) && $quads_options['ads'][$id]['phone_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options['ads'][$id]['phone_size'] );
        $default_ad_sizes[$id]['phone_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['phone_height'] = $ad_size_parts[1];
    }

    $id_name = "quads-".esc_attr($id)."-place";
    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";
    if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
        $html .= '<div id="'.esc_attr($id_name).'"></div>';
    }
    //google async script
    $html .=   "\n".'<script async data-cfasync="false" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
    if($loaded_lazy_load==''){
        $loaded_lazy_load = 'yes';
        $html .= quads_load_loading_script();
    }
    $html .= "\n".'<script type="text/javascript" data-cfasync="false">' . "\n";
    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";
    
if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
    $html .= quads_render_desktop_js( $id, $default_ad_sizes,$id_name );
    $html .= quads_render_tablet_landscape_js( $id, $default_ad_sizes,$id_name );
    $html .= quads_render_tablet_portrait_js( $id, $default_ad_sizes,$id_name );
    $html .= quads_render_phone_js( $id, $default_ad_sizes,$id_name );

    $html = str_replace( '<div id="'.esc_attr($id_name).'">', '<div id="'.esc_attr($id_name).'" class="quads-ll">', $html );
    $html = str_replace( 'class="adsbygoogle"', '', $html );
    $html = str_replace( '></ins>', '><span>Loading...</span></ins>', $html );
    $code = 'instant= new adsenseLoader( \'#quads-' . esc_attr($id) . '-place\', {
    onLoad: function( ad ){
        if (ad.classList.contains(\'quads-ll\')) {
            ad.classList.remove(\'quads-ll\');
        }
      }   
    });';

    $html = str_replace( '(adsbygoogle = window.adsbygoogle || []).push({});', $code, $html );

}else{
    $html .= quads_render_desktop_js( $id, $default_ad_sizes );
    $html .= quads_render_tablet_landscape_js( $id, $default_ad_sizes );
    $html .= quads_render_tablet_portrait_js( $id, $default_ad_sizes );
    $html .= quads_render_phone_js( $id, $default_ad_sizes );
}
    $html .=   "\n".'</script>' . "\n";

    $html .= "\n <!-- end WP QUADS --> \n\n";


    return apply_filters( 'quads_render_adsense_async', $html );
}

function quads_load_loading_script(){
    global $quads_options;
    $script = '';
    if ($quads_options['lazy_load_global']===true) {
    $script .=  "\n".'<script>!function(e,n){"function"==typeof define&&define.amd?define([],n("adsenseLoader")):"object"==typeof exports?module.exports=n("adsenseLoader"):e.adsenseLoader=n("adsenseLoader")}(this,function(e){"use strict";function n(e,n){"string"==typeof e?e=document.querySelectorAll(e):void 0===e.length&&(e=[e]),n=r(o,n),[].forEach.call(e,function(e){e=h(e,n),d.push(e)}),this.elements=e,l()}var t=250,o={laziness:1,onLoad:!1},r=function(e,n){var t,o={};for(t in e)Object.prototype.hasOwnProperty.call(e,t)&&(o[t]=e[t]);for(t in n)Object.prototype.hasOwnProperty.call(n,t)&&(o[t]=n[t]);return o},a=function(e,n){e.classList?e.classList.add(n):e.className+=" "+n},i=function(e){var n=e.getBoundingClientRect();return{top:n.top+document.body.scrollTop,left:n.left+document.body.scrollLeft}},s=function(e,n){var t,o;return function(){var r=this,a=arguments,i=+new Date;t&&i<t+e?(clearTimeout(o),o=setTimeout(function(){t=i,n.apply(r,a)},e)):(t=i,n.apply(r,a))}},d=[],f=[],u=[],c=function(e){(adsbygoogle=window.adsbygoogle||[]).push({});var n=e._adsenseLoaderData.options.onLoad;"function"==typeof n&&e.querySelector("iframe").addEventListener("load",function(){n(e)})},l=function(){if(!d.length)return!0;var e=window.pageYOffset,n=window.innerHeight;d.forEach(function(t){var o=i(t).top,r=t._adsenseLoaderData.options.laziness+1;if(o-e>n*r||e-o-t.offsetHeight-n*r>0)return!0;d=L(d,t),t._adsenseLoaderData.width=p(t),a(t.children[0],"adsbygoogle"),f.push(t),"undefined"!=typeof adsbygoogle?c(t):u.push(t)})},p=function(e){return parseInt(window.getComputedStyle(e,":before").getPropertyValue("content").slice(1,-1)||9999)},L=function(e,n){return e.filter(function(e){return e!==n})},h=function(e,n){return e._adsenseLoaderData={originalHTML:e.innerHTML,options:n},e.adsenseLoader=function(n){"destroy"==n&&(d=L(d,e),f=L(f,e),u=L(f,e),e.innerHTML=e._adsenseLoaderData.originalHTML)},e};return window.addEventListener("scroll",s(t,l)),window.addEventListener("resize",s(t,l)),window.addEventListener("resize",s(t,function(){if(!f.length)return!0;var e=!1;f.forEach(function(n){n._adsenseLoaderData.width!=p(n)&&(e=!0,f=L(f,n),n.innerHTML=n._adsenseLoaderData.originalHTML,d.push(n))}),e&&l()})),n.prototype={destroy:function(){this.elements.forEach(function(e){e.adsenseLoader("destroy")})}},window.adsenseLoaderConfig=function(e){void 0!==e.throttle&&(t=e.throttle)},n});</script>' . "\n";
        }
    return $script;

}

/**
 * Render Google Ad Code Java Script for desktop devices
 * 
 * @global array $quads_options
 * @param string $id
 * @param array $default_ad_sizes
 * @return string
 */
function quads_render_desktop_js( $id, $default_ad_sizes,$id_name='' ) {
    global $quads_options;
    
    $adtype = 'desktop';

    $backgroundcolor = '';

    $responsive_style = 'display:block;' . $backgroundcolor;
    
    if( quads_is_extra() && isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' ) {
        $width = $default_ad_sizes[$id][$adtype.'_width'];

        $height = $default_ad_sizes[$id][$adtype.'_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' && (isset( $quads_options['ads'][$id][$adtype.'_size'] ) && $quads_options['ads'][$id][$adtype.'_size'] === 'Auto') ? $responsive_style : $normal_style;
    } else {
        $width = empty( $quads_options['ads'][$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id][$adtype.'_width'] : $quads_options['ads'][$id]['g_data_ad_width'];

        $height = empty( $quads_options['ads'][$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id][$adtype.'_height'] : $quads_options['ads'][$id]['g_data_ad_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' ? $responsive_style : $normal_style;
    }

    $ad_format = (isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive') && (isset( $quads_options['ads'][$id][$adtype.'_size'] ) && $quads_options['ads'][$id][$adtype.'_size'] === 'Auto') ? 'data-ad-format="auto"' : '';

    $html = '<ins class="adsbygoogle" style="' . $style . '"';
    $html .= ' data-ad-client="' . $quads_options['ads'][$id]['g_data_ad_client'] . '"';
    $html .= ' data-ad-slot="' . $quads_options['ads'][$id]['g_data_ad_slot'] . '" ' . $ad_format . '></ins>';
    
    if (!quads_is_extra() && !empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'])){
$js = 'if ( quads_screen_width >= 1140 ) {';
        if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
            $js.='document.getElementById("'.$id_name.'").innerHTML='."'".$html."'".';
            (adsbygoogle = window.adsbygoogle || []).push({}); }';
        }else{
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        }
        return $js;   
    }
    
    if( !isset( $quads_options['ads'][$id][$adtype] ) and !empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
$js = 'if ( quads_screen_width >= 1140 ) {';
 if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
            $js.='document.getElementById("'.$id_name.'").innerHTML='."'".$html."'".';
            (adsbygoogle = window.adsbygoogle || []).push({});}';
        }else{
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        }
        return $js;
    }
}

/**
 * Render Google Ad Code Java Script for tablet landscape devices
 * 
 * @global array $quads_options
 * @param string $id
 * @param array $default_ad_sizes
 * @return string
 */
function quads_render_tablet_landscape_js( $id, $default_ad_sizes,$id_name='' ) {
    global $quads_options;
    
    $adtype = 'tbl_landscape';
    $adtype_short = 'tbl_lands';

    //$backgroundcolor = 'background-color:white;'; // Pro Version
    $backgroundcolor = '';

    $responsive_style = 'display:block;' . $backgroundcolor;

    if( quads_is_extra() && isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' ) {
        $width = $default_ad_sizes[$id][$adtype.'_width'];

        $height = $default_ad_sizes[$id][$adtype.'_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' && (isset( $quads_options['ads'][$id][$adtype_short.'_size'] ) && $quads_options['ads'][$id][$adtype_short.'_size'] === 'Auto') ? $responsive_style : $normal_style;
    } else {
        $width = empty( $quads_options['ads'][$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id][$adtype.'_width'] : $quads_options['ads'][$id]['g_data_ad_width'];

        $height = empty( $quads_options['ads'][$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id][$adtype.'_height'] : $quads_options['ads'][$id]['g_data_ad_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' ? $responsive_style : $normal_style;
    }

    $ad_format = (isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive') && (isset( $quads_options['ads'][$id][$adtype_short.'_size'] ) && $quads_options['ads'][$id][$adtype_short.'_size'] === 'Auto') ? 'data-ad-format="auto"' : '';


    $html = '<ins class="adsbygoogle" style="' . $style . '"';
    $html .= ' data-ad-client="' . $quads_options['ads'][$id]['g_data_ad_client'] . '"';
    $html .= ' data-ad-slot="' . $quads_options['ads'][$id]['g_data_ad_slot'] . '" ' . $ad_format . '></ins>';

        if( !quads_is_extra() && ! empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
$js = 'if ( quads_screen_width >= 1024  && quads_screen_width < 1140 ) {';
        if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
            $js.='document.getElementById("'.$id_name.'").innerHTML='."'".$html."'".';
            (adsbygoogle = window.adsbygoogle || []).push({});}';
        }else{
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        }
        return $js;
    }
    
    if( !isset( $quads_options['ads'][$id]['tablet_landscape'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width >= 1024  && quads_screen_width < 1140 ) {';
       if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
            $js.='document.getElementById("'.$id_name.'").innerHTML='."'".$html."'".';
            (adsbygoogle = window.adsbygoogle || []).push({}); }';
        }else{
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        }
        return $js;
    }
}

/**
 * Render Google Ad Code Java Script for tablet portrait devices
 * 
 * @global array $quads_options
 * @param string $id
 * @param array $default_ad_sizes
 * @return string
 */
function quads_render_tablet_portrait_js( $id, $default_ad_sizes,$id_name='' ) {
    global $quads_options;
  
    $adtype = 'tbl_portrait';
    
    $adtype_short = 'tbl_portr';

    $backgroundcolor = '';

    $responsive_style = 'display:block;' . $backgroundcolor;

    if( quads_is_extra() && isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' ) {
        $width = $default_ad_sizes[$id][$adtype.'_width'];

        $height = $default_ad_sizes[$id][$adtype.'_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' && (isset( $quads_options['ads'][$id][$adtype_short.'_size'] ) && $quads_options['ads'][$id][$adtype_short.'_size'] === 'Auto') ? $responsive_style : $normal_style;
    } else {
        $width = empty( $quads_options['ads'][$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id][$adtype.'_width'] : $quads_options['ads'][$id]['g_data_ad_width'];

        $height = empty( $quads_options['ads'][$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id][$adtype.'_height'] : $quads_options['ads'][$id]['g_data_ad_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' ? $responsive_style : $normal_style;
    }

    $ad_format = (isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive') && (isset( $quads_options['ads'][$id][$adtype_short.'_size'] ) && $quads_options['ads'][$id][$adtype_short.'_size'] === 'Auto') ? 'data-ad-format="auto"' : '';

    $html = '<ins class="adsbygoogle" style="' . $style . '"';
    $html .= ' data-ad-client="' . $quads_options['ads'][$id]['g_data_ad_client'] . '"';
    $html .= ' data-ad-slot="' . $quads_options['ads'][$id]['g_data_ad_slot'] . '" ' . $ad_format . '></ins>';

        if( !quads_is_extra() and !empty( $default_ad_sizes[$id]['tbl_portrait_width'] ) and !empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
$js = 'if ( quads_screen_width >= 768  && quads_screen_width < 1024 ) {';
        if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
            $js.='document.getElementById("'.$id_name.'").innerHTML='."'".$html."'".';
            (adsbygoogle = window.adsbygoogle || []).push({}); }';
        }else{
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        }
        return $js;
    }
    
    if( !isset( $quads_options['ads'][$id]['tablet_portrait'] ) and !empty( $default_ad_sizes[$id]['tbl_portrait_width'] ) and !empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
$js = 'if ( quads_screen_width >= 768  && quads_screen_width < 1024 ) {';
 if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
    $js.='document.getElementById("'.$id_name.'").innerHTML='."'".$html."'".';
            (adsbygoogle = window.adsbygoogle || []).push({}); }';
}else{
    $js.= 'document.write(\'' . $html . '\');
    (adsbygoogle = window.adsbygoogle || []).push({});
    }';
}
return $js;
    }
}

/**
 * Render Google Ad Code Java Script for phone devices
 * 
 * @global array $quads_options
 * @param string $id
 * @param array $default_ad_sizes
 * @return string
 */
function quads_render_phone_js( $id, $default_ad_sizes,$id_name='' ) {
    global $quads_options;
    
    $adtype = 'phone';

    $backgroundcolor = '';

    $responsive_style = 'display:block;' . $backgroundcolor;

    if( quads_is_extra() && isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' ) {
        $width = $default_ad_sizes[$id][$adtype.'_width'];

        $height = $default_ad_sizes[$id][$adtype.'_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' && (isset( $quads_options['ads'][$id][$adtype.'_size'] ) && $quads_options['ads'][$id][$adtype.'_size'] === 'Auto') ? $responsive_style : $normal_style;
    } else {
        $width = empty( $quads_options['ads'][$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id][$adtype.'_width'] : $quads_options['ads'][$id]['g_data_ad_width'];

        $height = empty( $quads_options['ads'][$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id][$adtype.'_height'] : $quads_options['ads'][$id]['g_data_ad_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive' ? $responsive_style : $normal_style;
    }

    $ad_format = (isset( $quads_options['ads'][$id]['adsense_type'] ) && $quads_options['ads'][$id]['adsense_type'] === 'responsive') && (isset( $quads_options['ads'][$id][$adtype.'_size'] ) && $quads_options['ads'][$id][$adtype.'_size'] === 'Auto') ? 'data-ad-format="auto"' : '';

    $html = '<ins class="adsbygoogle" style="' . $style . '"';
    $html .= ' data-ad-client="' . $quads_options['ads'][$id]['g_data_ad_client'] . '"';
    $html .= ' data-ad-slot="' . $quads_options['ads'][$id]['g_data_ad_slot'] . '" ' . $ad_format . '></ins>';

        if( !quads_is_extra() and ! empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width < 768 ) {';
 if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
            $js.='document.getElementById("'.$id_name.'").innerHTML='."'".$html."'".';
            (adsbygoogle = window.adsbygoogle || []).push({}); }';
        }else{
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        }
        return $js;
    }
    
    
    if( !isset( $quads_options['ads'][$id][$adtype] ) and ! empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width < 768 ) {';
 if ( $quads_options['ads'][$id]['lazy_load_ads'] == 'yes' || ($quads_options['ads'][$id]['lazy_load_ads']=='inherit' && $quads_options['lazy_load_global']===true)) {
            $js.='document.getElementById("'.$id_name.'").innerHTML='."'".$html."'".';
            (adsbygoogle = window.adsbygoogle || []).push({}); }';
        }else{
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        }
        return $js;
    }
}


/**
 * Check if ad code is adsense or other ad code
 * 
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_adsense( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'adsense') {
        return true;
    }
    return false;
}


/**
 * Render advert on amp pages
 * 
 * @global array $quads_options
 * @param int $id
 * @return string
 */
function quads_render_amp($id){
    global $quads_options;
    
    // quads pro not installed and activated
    if ( !quads_is_extra() ){
       return '';
    }
    if(isset($quads_options['ads'][$id]['enabled_on_amp']) && isset($quads_options['ads'][$id]['code']) && !empty($quads_options['ads'][$id]['code'])){
            return $quads_options['ads'][$id]['code'];
        }
    // if amp is not activated return empty
    if (!isset($quads_options['ads'][$id]['amp']) || quads_is_disabled_post_amp() ){
        return '';
    }
    
    if (!empty($quads_options['ads'][$id]['amp_code'])){
        $html = $quads_options['ads'][$id]['amp_code'];
    } else {
        // Return default adsense code
        $html = '<amp-ad layout="responsive" width=300 height=250 type="adsense" data-ad-client="'. $quads_options['ads'][$id]['g_data_ad_client'] . '" data-ad-slot="'.$quads_options['ads'][$id]['g_data_ad_slot'].'"></amp-ad>';
    }

    return $html;
}

/**
 * Check if page is AMP one
 * 
 * @return boolean
 */
function quads_is_amp_endpoint(){
   
   // General AMP query
   if (false !== get_query_var( 'amp', false )){
      return true;
   }
   
    // Automattic AMP plugin
    if (  function_exists( 'is_amp_endpoint' )){
        if ( is_amp_endpoint()){
            return true;
        }
    }
    return false;
}
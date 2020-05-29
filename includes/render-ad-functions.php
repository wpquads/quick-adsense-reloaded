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
function quads_render_ad( $id, $string, $widget = false,$ampsupport='' ) {
    
    // Return empty string
    if( empty( $id ) ) {
        return '';
    }
    
    
    if (quads_is_amp_endpoint()){
        return apply_filters( 'quads_render_ad', quads_render_amp($id,$ampsupport),$id );
    }
    

    // Return the original ad code if it's no adsense code
    if( false === quads_is_adsense( $id, $string ) && !empty( $string ) ) {
        // allow use of shortcodes in ad plain text content
        $string = quadsCleanShortcode('quads', $string);
        //wp_die('t1');
        return apply_filters( 'quads_render_ad', $string,$id );
    }

    // Return the adsense ad code
    if( true === quads_is_adsense( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_google_async( $id ),$id );
    }
    if( true === quads_is_double_click( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_double_click_async( $id ),$id );
    }
    if( true === quads_is_yandex( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_yandex_async( $id ),$id );
    }

    // Return empty string
    return '';
}
function quads_doubleclick_head_code(){

    $data_slot  = '';   
    $adsense     = false;   
    require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
    $api_service = new QUADS_Ad_Setup_Api_Service();
    $quads_ads = $api_service->getAdDataByParam('quads-ads');               

    if(isset($quads_ads['posts_data'])){  

        foreach($quads_ads['posts_data'] as $key => $value){
            if($value['post']['post_status']== 'draft'){
                continue;
            }
            $ads =$value['post_meta'];
            if(isset($ads['random_ads_list']))
                $ads['random_ads_list'] = unserialize($ads['random_ads_list']);
            if(isset($ads['visibility_include']))
                $ads['visibility_include'] = unserialize($ads['visibility_include']);
            if(isset($ads['visibility_exclude']))
                $ads['visibility_exclude'] = unserialize($ads['visibility_exclude']);

            if(isset($ads['targeting_include']))
                $ads['targeting_include'] = unserialize($ads['targeting_include']);

            if(isset($ads['targeting_exclude']))
                $ads['targeting_exclude'] = unserialize($ads['targeting_exclude']);
            $is_on =quads_is_visibility_on($ads);
           if(!$is_on){
             continue;
           }
            if($ads['ad_type']== 'double_click'){
                $network_code  = $ads['network_code'];                          
                $ad_unit_name  = $ads['ad_unit_name'];

                $width        = (isset($ads['g_data_ad_width']) && !empty($ads['g_data_ad_width'])) ? $ads['g_data_ad_width'] : '300';  
                 $height        = (isset($ads['g_data_ad_height']) && !empty($ads['g_data_ad_height'])) ? $ads['g_data_ad_height'] : '250';                                                                                                            
                $data_slot .="googletag.defineSlot('/".esc_attr($network_code)."/".esc_attr($ad_unit_name)."/', [".esc_attr($width).", ".esc_attr($height)."], 'wp_quads_dfp_".esc_attr($ads['ad_id'])."')
             .addService(googletag.pubads());";
            }else if($ads['ad_type'] == 'adsense'){
                $adsense= true;

            }   

        }
        if( $data_slot !=''){

            echo "<script async src='https://securepubads.g.doubleclick.net/tag/js/gpt.js'></script>
                    <script>
                 window.googletag = window.googletag || {cmd: []};
  googletag.cmd.push(function() {
  ".$data_slot." 
    googletag.pubads().enableSingleRequest();
    googletag.enableServices();
  });
                </script>";   

        }     
        if($adsense){
            echo '<script src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';

        }                       

    }                                                    

}  
/**
 * Render Google async ad
 * 
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_double_click_async( $id ) {
    global $quads_options;
      $width        = (isset($quads_options['ads'][$id]['g_data_ad_width']) && !empty($quads_options['ads'][$id]['g_data_ad_width'])) ? $quads_options['ads'][$id]['g_data_ad_width'] : '300';  
        $height        = (isset($quads_options['ads'][$id]['g_data_ad_height']) && !empty($quads_options['ads'][$id]['g_data_ad_height'])) ? $quads_options['ads'][$id]['g_data_ad_height'] : '250';  

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Doubleclick async --> \n\n";
    $html .= '<div id="wp_quads_dfp_'.esc_attr($quads_options['ads'][$id]['ad_id']). '" style="height:'.esc_attr($height). 'px; width:'.esc_attr($width). 'px;">
                        <script>
                        googletag.cmd.push(function() { googletag.display("wp_quads_dfp_'.esc_attr($quads_options['ads'][$id]['ad_id']).'"); });
                        </script>
                        </div>';
    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_double_click_async', $html );
}
/**
 * Render Google async ad
 * 
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_yandex_async( $id ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Yandex async --> \n\n";
    $html .= '<div id="yandex_rtb_'.esc_attr($quads_options['ads'][$id]['block_id']). '" ></div>
                       <script type="text/javascript">
    (function(w, d, n, s, t) {
        w[n] = w[n] || [];
        w[n].push(function() {
            Ya.Context.AdvManager.render({
                blockId: "'.esc_attr($quads_options['ads'][$id]['block_id']). '",
                renderTo: "yandex_rtb_'.esc_attr($quads_options['ads'][$id]['block_id']). '",
                async: true
            });
        });
        t = d.getElementsByTagName("script")[0];
        s = d.createElement("script");
        s.type = "text/javascript";
        s.src = "//an.yandex.ru/system/context.js";
        s.async = true;
        t.parentNode.insertBefore(s, t);
    })(this, this.document, "yandexContextAsyncCallbacks");
</script>';
    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_yandex_async', $html );
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
    if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {
        $html .= '<div id="'.esc_attr($id_name).'"></div>';
    }
    //google async script
    if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {
        if($loaded_lazy_load==''){
            $loaded_lazy_load = 'yes';
            $html .= quads_load_loading_script();
        }
    }
    $html .= "\n".'<script type="text/javascript" >' . "\n";
    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";
    
if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {
    $html .= quads_render_desktop_js( $id, $default_ad_sizes,$id_name );
    $html .= quads_render_tablet_landscape_js( $id, $default_ad_sizes,$id_name );
    $html .= quads_render_tablet_portrait_js( $id, $default_ad_sizes,$id_name );
    $html .= quads_render_phone_js( $id, $default_ad_sizes,$id_name );

    $html = str_replace( '<div id="'.esc_attr($id_name).'">', '<div id="'.esc_attr($id_name).'" class="quads-ll">', $html );
    $html = str_replace( 'class="adsbygoogle"', '', $html );
    $html = str_replace( '></ins>', '><span>Loading...</span></ins>', $html );
    $code = 'instant= new adsenseLoader( \'#quads-' . esc_attr($id) . '-place\', {
    onLoad: function( ad ){
        if (ad.classList.contains("quads-ll")) {
            ad.classList.remove("quads-ll");
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
    $script .=  "\n".'<script>';

    $script .= file_get_contents(QUADS_PLUGIN_DIR.'assets/js/lazyload.js');

    $script .='</script>' . "\n";
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
        if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {

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
        if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {
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
        if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {
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
        if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {

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
 * Render Google Ad Code Java Script for tablet landscape devices
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
        if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {

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
        if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {
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
        if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {
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
        if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']===true) {
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
 * Check if ad code is double click or other ad code
 * 
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_double_click( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'double_click') {
        return true;
    }
    return false;
}

/**
 * Check if ad code is double click or other ad code
 * 
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_yandex( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'yandex') {
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
function quads_render_amp($id,$ampsupport=''){
    global $quads_options,$quads_mode;

    if($quads_mode != 'new'){
        // quads pro not installed and activated
        if ( !quads_is_extra() ){
           return '';
        }
        if(isset($quads_options['ads'][$id]['amp']) && isset($quads_options['ads'][$id]['code']) && !empty($quads_options['ads'][$id]['code'])){
                return $quads_options['ads'][$id]['code'];
            }
        // if amp is not activated return empty
        if (!isset($quads_options['ads'][$id]['amp']) || quads_is_disabled_post_amp() ){
            return '';
        }
    }else{

         if((isset($quads_options['ads'][$id]['enabled_on_amp']) && isset($quads_options['ads'][$id]['code']) && !empty($quads_options['ads'][$id]['code']))|| (!empty($ampsupport) && $ampsupport)){
                if((isset($quads_options['ads'][$id]['enabled_on_amp']) && $quads_options['ads'][$id]['enabled_on_amp']) || (!empty($ampsupport) && $ampsupport)){
                    if(isset($quads_options['ads'][$id]['code'])){
                      return  $quads_options['ads'][$id]['code'];
                    }else if(isset($quads_options['ads'][$id]['post_meta'])){
                      return $quads_options['ads'][$id]['post_meta']['code'];
                    }else{
                       return '';
                    }
                }else{
                    return '';
                }
            }
        // if amp is not activated return empty
        if (!isset($quads_options['ads'][$id]['enabled_on_amp']) || quads_is_disabled_post_amp() ){
            return '';
        }
    }
    if (!empty($quads_options['ads'][$id]['amp_code'])){
        $html = $quads_options['ads'][$id]['amp_code'];
    } else {
            if($quads_options['ads'][$id]['ad_type'] == 'double_click'){
                $width        = (isset($quads_options['ads'][$id]['g_data_ad_width']) && !empty($quads_options['ads'][$id]['g_data_ad_width'])) ? $quads_options['ads'][$id]['g_data_ad_width'] : '300';  
                $height        = (isset($quads_options['ads'][$id]['g_data_ad_height']) && !empty($quads_options['ads'][$id]['g_data_ad_height'])) ? $quads_options['ads'][$id]['g_data_ad_height'] : '250';  

                $network_code  = $quads_options['ads'][$id]['network_code'];                          
                $ad_unit_name  = $quads_options['ads'][$id]['ad_unit_name']; 
               // Return default Double click code
        $html = '<amp-ad width='.esc_attr($width).' height='.esc_attr($height).' type="doubleclick" data-ad-slot="/'.esc_attr($network_code)."/".esc_attr($ad_unit_name). '/" data-multi-size="468x60,300x250"></amp-ad>';
            }else if($quads_options['ads'][$id]['ad_type'] == 'yandex'){

                $width        = (isset($quads_options['ads'][$id]['g_data_ad_width']) && !empty($quads_options['ads'][$id]['g_data_ad_width'])) ? $quads_options['ads'][$id]['g_data_ad_width'] : '300';  
                $height        = (isset($quads_options['ads'][$id]['g_data_ad_height']) && !empty($quads_options['ads'][$id]['g_data_ad_height'])) ? $quads_options['ads'][$id]['g_data_ad_height'] : '250';  
                
                  $html = '<amp-ad width='.esc_attr($width).' height='.esc_attr($height).' type="yandex" data-block-id="'.esc_attr($quads_options['ads'][$id]['block_id']).'" data-html-access-allowed="true"></amp-ad>';
            }else{
                   // Return default adsense code
             $html = '<amp-ad layout="responsive" width=300 height=250 type="adsense" data-ad-client="'. esc_attr($quads_options['ads'][$id]['g_data_ad_client']) . '" data-ad-slot="'.esc_attr($quads_options['ads'][$id]['g_data_ad_slot']).'"></amp-ad>';
            }
     
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



function quads_render_ad_label_new( $adcode,$id='') {
   global $quads_options,$quads_mode;

   $post_id= quadsGetPostIdByMetaKeyValue('quads_ad_old_id', $id);
    $ad_meta = get_post_meta($post_id, '',true);
    if (quads_is_amp_endpoint()){
        if(!isset($ad_meta['enabled_on_amp'][0]) || (isset($ad_meta['enabled_on_amp'][0]) && (empty($ad_meta['enabled_on_amp'][0])|| !$ad_meta['enabled_on_amp'][0]) )){
            return $adcode;
        }
    }
    $ad_label_check  = isset($ad_meta['ad_label_check'][0]) ? $ad_meta['ad_label_check'][0] : false;
    if($quads_mode =='new' && $ad_label_check){
        $position =  (isset($ad_meta['adlabel'][0]) && !empty($ad_meta['adlabel'][0]) )? $ad_meta['adlabel'][0] : 'above';
        $ad_label_text =  (isset($ad_meta['ad_label_text'][0]) && !empty($ad_meta['ad_label_text'][0])) ? $ad_meta['ad_label_text'][0] : 'Advertisements';
         $label = apply_filters( 'quads_ad_label', $ad_label_text );

       $html = '<div class="quads-ad-label quads-ad-label-new">' . sanitize_text_field($label) . '</div>';
       if (defined('QUADS_PRO_VERSION') && QUADS_PRO_VERSION >= '2.0') {
            $css = '.quads-ad-label{display:none}  .quads-ad-label.quads-ad-label-new{display:block}';
            wp_dequeue_style('quads-ad-label');
            wp_deregister_style('quads-ad-label');
            wp_register_style( 'quads-ad-label', false );
            wp_enqueue_style( 'quads-ad-label' );
            wp_add_inline_style( 'quads-ad-label', $css );
        }

       if( $position == 'above' ) {
          return $html . $adcode;
       }
       if( $position == 'below' ) {
          return $adcode . $html;
       }
    }
    return $adcode;
}

add_filter( 'quads_render_ad', 'quads_render_ad_label_new',99,2 );
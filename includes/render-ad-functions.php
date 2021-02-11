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
    global $quads_mode;
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
        if($quads_mode == 'new'){

            return apply_filters( 'quads_render_ad', quads_render_google_async_new( $id ),$id );

        }else{
            return apply_filters( 'quads_render_ad', quads_render_google_async( $id ),$id );
          }
    }
    if( true === quads_is_double_click( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_double_click_async( $id ),$id );
    }
    if( true === quads_is_yandex( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_yandex_async( $id ),$id );
    }
    if( true === quads_is_mgid( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_mgid_async( $id ),$id );
    }
    if( true === quads_is_ad_image( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_ad_image_async( $id ),$id );
    }
    if( true === quads_is_taboola( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_taboola_async( $id ),$id );
    }
    if( true === quads_is_media_net( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_media_net_async( $id ),$id );
    }
    if( true === quads_is_outbrain( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_outbrain_async( $id ),$id );
    }
    if( true === quads_is_infolinks( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_infolinks_async( $id ),$id );
    }
    // Return empty string
    return '';
}
function quads_common_head_code(){
    global $quads_options;
    if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']== true) {
        echo quads_load_loading_script();
    }
    $data_slot  = '';
    $adsense     = false;
        if(isset($quads_options['ads'])){
        foreach ($quads_options['ads'] as $key => $value) {
            if($value['ad_type'] == 'adsense'){
                $adsense  = true;
                break;
            }
        }
    }
    require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
    $api_service = new QUADS_Ad_Setup_Api_Service();
    $quads_ads = $api_service->getAdDataByParam('quads-ads');
    if(isset($quads_ads['posts_data'])){
        $revenue_sharing = quads_get_pub_id_on_revenue_percentage();
        foreach($quads_ads['posts_data'] as $key => $value){
            if($value['post']['post_status']== 'draft'){
                continue;
            }

            $ads =$value['post_meta'];
            if($revenue_sharing){
                if(isset($revenue_sharing['author_pub_id']) && !empty($revenue_sharing['author_pub_id'])){
                    $ads['g_data_ad_client'] = $revenue_sharing['author_pub_id'];
                }
            }
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
            $is_visitor_on = quads_is_visitor_on($ads);
           if(!$is_on || !$is_visitor_on){
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

                if(isset($ads['adsense_ad_type']) && $ads['adsense_ad_type'] == 'adsense_auto_ads'){
                    echo ' <script>
                  (adsbygoogle = window.adsbygoogle || []).push({
                  google_ad_client: "'.esc_attr($ads['g_data_ad_client']).'",
                  enable_page_level_ads: true
                  }); 
                 </script>';
                }
                $adsense= true;

            }
            if($ads['ad_type']== 'taboola'){
               echo '<script type="text/javascript">window._taboola = window._taboola || [];
              _taboola.push({article:"auto"});
              !function (e, f, u) {
                e.async = 1;
                e.src = u;
                f.parentNode.insertBefore(e, f);
              }(document.createElement("script"), document.getElementsByTagName("script")[0], "//cdn.taboola.com/libtrc/'.esc_attr($ads['taboola_publisher_id']).'/loader.js");
              </script>';
            }else if($ads['ad_type']== 'mediavine'){
               echo '<link rel="dns-prefetch" href="//scripts.mediavine.com" />
                  <script type="text/javascript" async="async" data-noptimize="1" data-cfasync="false" src="//scripts.mediavine.com/tags/'.esc_attr($ads['mediavine_site_id']).'.js?ver=5.2.3"></script>';
            }else if($ads['ad_type']== 'outbrain'){
               echo '<script type="text/javascript" async="async" src="http://widgets.outbrain.com/outbrain.js "></script>';
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
 * Render Double Click ad
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
 * Render Yandex ad
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
 * Render ad banner
 *
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_ad_image_async( $id ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Yandex async --> \n\n";
    if(isset($quads_options['ads'][$id]['image_redirect_url'])  && !empty($quads_options['ads'][$id]['image_redirect_url'])){
        $html .= '
        <a target="_blank" href="'.esc_attr($quads_options['ads'][$id]['image_redirect_url']). '" rel="nofollow">
        <img  src="'.esc_attr($quads_options['ads'][$id]['image_src']). '" > 
        </a>';
    }else{
        $html .= '<img src="'.esc_attr($quads_options['ads'][$id]['image_src']). '" >';
    }

    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_ad_image_async', $html );
}

/**
 * Render Taboola
 *
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_taboola_async( $id ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Taboola --> \n\n";

        $html .= '<div id="quads_taboola_'.$id.'"></div>';

          $html .= '<script type="text/javascript">
                        window._taboola = window._taboola || [];
                        _taboola.push({
                            mode:"thumbnails-a", 
                            container:"quads_taboola_'.$id.'", 
                            placement:"quads_taboola_'.$id.'", 
                            target_type: "mix"
                        });</script>';


    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_taboola_async', $html );
}

/**
 * Render Media.net
 *
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_media_net_async( $id ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Media.net --> \n\n";

    $width = (isset($quads_options['ads'][$id]['g_data_ad_width']) && (!empty($quads_options['ads'][$id]['g_data_ad_width']))) ? $quads_options['ads'][$id]['g_data_ad_width']:300;
    $height = (isset($quads_options['ads'][$id]['g_data_ad_height']) && (!empty($quads_options['ads'][$id]['g_data_ad_height']))) ? $quads_options['ads'][$id]['g_data_ad_height']:250;
    $html .= '<script id="mNCC" language="javascript">
                medianet_width = "'.esc_attr($width).'";
                medianet_height = "'.esc_attr($height).'";
                medianet_crid = "'.esc_attr($quads_options['ads'][$id]['data_crid']).'"
                medianet_versionId ="3111299"
               </script>
               <script src="//contextual.media.net/nmedianet.js?cid='.esc_attr($quads_options['ads'][$id]['data_cid']).'"></script>';


    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_media_net_async', $html );
}
/**
 * Render Outbrain
 *
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_outbrain_async( $id ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Outbrain --> \n\n";


    $html .= '<div class="quads_ad_amp_outbrain" data-widget-id="'.esc_attr($quads_options['ads'][$id]['outbrain_widget_ids']).'"></div>
';


    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_outbrain_async', $html );
}
/**
 * Render Infolinks
 *
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_infolinks_async( $id ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Infolinks --> \n\n";
    $html .= ' <script type="text/javascript">
                                    var infolinks_pid = '.esc_attr($quads_options['ads'][$id]['infolinks_pid']).';
                                    var infolinks_wsid = '.esc_attr($quads_options['ads'][$id]['infolinks_wsid']).';
                                  </script>
                                <script type="text/javascript" src="http://resources.infolinks.com/js/infolinks_main.js"></script>';

    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_infolinks_async', $html );
}
/**
 * Render MGID ad
 *
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_mgid_async( $id ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content MGID --> \n\n";
    $html .= '                             
                <div id="'.esc_attr($quads_options['ads'][$id]['data_container']).'">
                </div>
                <script src="'.esc_attr($quads_options['ads'][$id]['data_js_src']).'" async>
                </script>
            ';
    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_mgid_async', $html );
}
function quads_adsense_auto_ads_amp_script(){
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

            if($ads['ad_type'] == 'adsense' && isset($ads['adsense_ad_type']) && $ads['adsense_ad_type'] == 'adsense_auto_ads'){
             echo '<meta name="amp-script-src" content="sha384-X8xW7VFd-a-kgeKjsR4wgFSUlffP7x8zpVmqC6lm2DPadWUnwfdCBJ2KbwQn6ADE sha384-nNFaDRiLzgQEgiC5kP28pgiJVfNLVuw-nP3VBV-e2s3fOh0grENnhllLfygAuU_M sha384-u7NPnrcs7p4vsbGLhlYHsId_iDJbcOWxmBd9bhVuPoA_gM_he4vyK6GsuvFvr2ym">';

            }
        }
   }
}

function quads_adsense_auto_ads_amp_tag(){
        require_once QUADS_PLUGIN_DIR . '/admin/includes/rest-api-service.php';
    $api_service = new QUADS_Ad_Setup_Api_Service();
    $quads_ads = $api_service->getAdDataByParam('quads-ads');
     $revenue_sharing = quads_get_pub_id_on_revenue_percentage();
    if(isset($quads_ads['posts_data'])){

        foreach($quads_ads['posts_data'] as $key => $value){
            if($value['post']['post_status']== 'draft'){
                continue;
            }

            $ads =$value['post_meta'];
            if($revenue_sharing){
                if(isset($revenue_sharing['author_pub_id']) && !empty($revenue_sharing['author_pub_id'])){
                    $ads['g_data_ad_client'] = $revenue_sharing['author_pub_id'];
                }
            }
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

            if($ads['ad_type'] == 'adsense' && isset($ads['adsense_ad_type']) && $ads['adsense_ad_type'] == 'adsense_auto_ads'){
             echo '<amp-auto-ads 
                                type="adsense"
                                data-ad-client="'.esc_attr($ads['g_data_ad_client'] ).'">
                            </amp-auto-ads>';;

            }
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
function quads_render_google_async_new( $id ) {
    global $quads_options,$loaded_lazy_load;
    $revenue_sharing = quads_get_pub_id_on_revenue_percentage();
    if($revenue_sharing){
        if(isset($revenue_sharing['author_pub_id']) && !empty($revenue_sharing['author_pub_id'])){
            $quads_options['ads'][$id]['g_data_ad_client'] = $revenue_sharing['author_pub_id'];
        }
    }
     if (isset($quads_options['ads'][$id]['adsense_ad_type']) && $quads_options['ads'][$id]['adsense_ad_type'] == 'adsense_auto_ads'){
        return '';
        }
    $id_name = "quads-".esc_attr($id)."-place";
    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";
    if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global'] == true) {

        $html .= '<div id="'.esc_attr($id_name).'" class="quads-ll">' ;
    }
    $ad_data = '';
    if (isset($quads_options['ads'][$id]['adsense_ad_type']) && $quads_options['ads'][$id]['adsense_ad_type'] == 'in_feed_ads'){
            $ad_data = ' style="display:block;min-height:50px"
                         data-ad-format="fluid"
                         data-ad-layout-key="'.esc_attr($quads_options['ads'][$id]['data_layout_key']).'"';
    }else if (isset($quads_options['ads'][$id]['adsense_ad_type']) && $quads_options['ads'][$id]['adsense_ad_type'] == 'in_article_ads'){
            $ad_data = 'style="display:block; text-align:center;"
                         data-ad-layout="in-article"
                         data-ad-format="fluid"';

    }else if (isset($quads_options['ads'][$id]['adsense_ad_type']) && $quads_options['ads'][$id]['adsense_ad_type'] == 'matched_content'){
            $ad_data = 'style="display:block; text-align:center;"
                         data-ad-layout="in-article"
                         data-ad-format="fluid"';

    }else{
            $ad_data = ' style="display:block;"
                          data-ad-format="auto"';

    }

    if (isset($quads_options['ads'][$id]['adsense_type']) && $quads_options['ads'][$id]['adsense_type'] != 'responsive' && ((isset($quads_options['ads'][$id]['adsense_ad_type']) && ($quads_options['ads'][$id]['adsense_ad_type'] == 'display_ads' || $quads_options['ads'][$id]['adsense_ad_type'] == 'matched_content')) || !isset($quads_options['ads'][$id]['adsense_ad_type']))) {
        $width = (isset($quads_options['ads'][$id]['g_data_ad_width']) && (!empty($quads_options['ads'][$id]['g_data_ad_width']))) ? $quads_options['ads'][$id]['g_data_ad_width']:300;
        $height = (isset($quads_options['ads'][$id]['g_data_ad_height']) && (!empty($quads_options['ads'][$id]['g_data_ad_height']))) ? $quads_options['ads'][$id]['g_data_ad_height']:250;
        $style = 'display:inline-block;width:' . esc_attr($width) . 'px;height:' . esc_attr($height) . 'px;' ;

        $html .= '<ins class="adsbygoogle" style="' . $style . '"';
        $html .= ' data-ad-client="' . esc_attr($quads_options['ads'][$id]['g_data_ad_client'] ). '"';
        $html .= ' data-ad-slot="' . esc_attr($quads_options['ads'][$id]['g_data_ad_slot']) . '"></ins>
                    <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});</script>';
    }else{

        $html .= '
            <ins class="adsbygoogle"
                 '.$ad_data.'
                 data-ad-client="'. esc_attr($quads_options['ads'][$id]['g_data_ad_client'] ).'"
                 data-ad-slot="'. esc_attr($quads_options['ads'][$id]['g_data_ad_slot']) .'"></ins>
                 <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});</script>';

    }

    if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']== true) {
        $html = str_replace( 'class="adsbygoogle"', '', $html );
        $html = str_replace( '></ins>', '><span>Loading...</span></ins></div>', $html );
        $code = 'instant= new adsenseLoader( \'#quads-' . esc_attr($id) . '-place\', {
        onLoad: function( ad ){
            if (ad.classList.contains("quads-ll")) {
                ad.classList.remove("quads-ll");
            }
          }   
        });';
        $html = str_replace( '(adsbygoogle = window.adsbygoogle || []).push({});', $code, $html );
    }
    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_adsense_async', $html );
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


    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";
      if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global'] == true) {
             $id_name = "quads-".esc_attr($id)."-place";
        $html .= '<div id="'.esc_attr($id_name).'" class="quads-ll">' ;
    }

    //google async script
    $html .= "\n".'<script type="text/javascript" >' . "\n";
    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";


        $html .= quads_render_desktop_js( $id, $default_ad_sizes );
        $html .= quads_render_tablet_landscape_js( $id, $default_ad_sizes );
        $html .= quads_render_tablet_portrait_js( $id, $default_ad_sizes );
        $html .= quads_render_phone_js( $id, $default_ad_sizes );
       if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global'] == true) {
            $html = str_replace( 'class="adsbygoogle"', '', $html );
            $html = str_replace( '></ins>', '><span>Loading...</span></ins></div>', $html );
            $code = 'instant= new adsenseLoader( \'#quads-' . esc_attr($id) . '-place\', {
            onLoad: function( ad ){
                if (ad.classList.contains("quads-ll")) {
                    ad.classList.remove("quads-ll");
                }
              }   
            });';
            $html = str_replace( '(adsbygoogle = window.adsbygoogle || []).push({});', $code, $html );
        }
        $html .=   "\n".'</script>' . "\n";

        $html .= "\n <!-- end WP QUADS --> \n\n";


        return apply_filters( 'quads_render_adsense_async', $html );
}
function quads_load_loading_script(){
    global $quads_options;
    $script = '';
    if ($quads_options['lazy_load_global']== true) {
    $script .=  "\n".'<script>';
    $suffix = ( quadsIsDebugMode() ) ? '' : '.min';
    $script .= file_get_contents(QUADS_PLUGIN_DIR.'assets/js/lazyload' . $suffix .'.js');

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
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        return $js;
    }

    if( !isset( $quads_options['ads'][$id][$adtype] ) and !empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
            $js = 'if ( quads_screen_width >= 1140 ) {';
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
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
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        return $js;
    }

    if( !isset( $quads_options['ads'][$id]['tablet_landscape'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width >= 1024  && quads_screen_width < 1140 ) {';
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
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
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        return $js;
    }

    if( !isset( $quads_options['ads'][$id]['tablet_portrait'] ) and !empty( $default_ad_sizes[$id]['tbl_portrait_width'] ) and !empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width >= 768  && quads_screen_width < 1024 ) {';
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
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
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
        return $js;
    }


    if( !isset( $quads_options['ads'][$id][$adtype] ) and ! empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width < 768 ) {';
            $js.= 'document.write(\'' . $html . '\');
            (adsbygoogle = window.adsbygoogle || []).push({});
            }';
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
 * Check if ad code is MGID or other ad code
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_mgid( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'mgid') {
        return true;
    }
    return false;
}

/**
 * Check if ad code is Ad Banner
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_ad_image( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'ad_image') {
        return true;
    }
    return false;
}
/**
 * Check if ad code is Taboola
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_taboola( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'taboola') {
        return true;
    }
    return false;
}
/**
 * Check if ad code is Media.net
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_media_net( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'media_net') {
        return true;
    }
    return false;
}
/**
 * Check if ad code is Outbrain
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_outbrain( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'outbrain') {
        return true;
    }
    return false;
}
/**
 * Check if ad code is Infolinks
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_infolinks( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'infolinks') {
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
        // if amp is not activated return empty
        if (!isset($quads_options['ads'][$id]['amp']) || quads_is_disabled_post_amp() ){
            return '';
        }
        // if having amp code
        if(!empty($quads_options['ads'][$id]['amp_code'])){
            return $quads_options['ads'][$id]['amp_code'];
        }
        if($quads_options['ads'][$id]['ad_type']=='plain_text'){
             return $quads_options['ads'][$id]['code'];
        }else{
            return '<amp-ad layout="responsive" width=300 height=250 type="adsense" data-ad-client="'. esc_attr($quads_options['ads'][$id]['g_data_ad_client']) . '" data-ad-slot="'.esc_attr($quads_options['ads'][$id]['g_data_ad_slot']).'"></amp-ad>';
        }

    }else{
        $revenue_sharing = quads_get_pub_id_on_revenue_percentage();
        if($revenue_sharing){
            if(isset($revenue_sharing['author_pub_id']) && !empty($revenue_sharing['author_pub_id'])){
                $quads_options['ads'][$id]['g_data_ad_client'] = $revenue_sharing['author_pub_id'];
            }
        }

         if($quads_options['ads'][$id]['ad_type'] == 'plain_text' && (isset($quads_options['ads'][$id]['enabled_on_amp']) && isset($quads_options['ads'][$id]['code']) && !empty($quads_options['ads'][$id]['code']))|| (!empty($ampsupport) && $ampsupport)){
                if((isset($quads_options['ads'][$id]['enabled_on_amp']) && $quads_options['ads'][$id]['enabled_on_amp']) || (!empty($ampsupport) && $ampsupport)){
                    if(isset($quads_options['ads'][$id]['code'])){
                        return $quads_options['ads'][$id]['code'];
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
        if (!isset($quads_options['ads'][$id]['enabled_on_amp']) || (isset($quads_options['ads'][$id]['enabled_on_amp']) && $quads_options['ads'][$id]['enabled_on_amp'] === false) ){
            return '';
        }

            if($quads_options['ads'][$id]['ad_type'] == 'double_click'){
                $width        = (isset($quads_options['ads'][$id]['g_data_ad_width']) && !empty($quads_options['ads'][$id]['g_data_ad_width'])) ? $quads_options['ads'][$id]['g_data_ad_width'] : '300';
                $height        = (isset($quads_options['ads'][$id]['g_data_ad_height']) && !empty($quads_options['ads'][$id]['g_data_ad_height'])) ? $quads_options['ads'][$id]['g_data_ad_height'] : '250';

                $network_code  = $quads_options['ads'][$id]['network_code'];
                $ad_unit_name  = $quads_options['ads'][$id]['ad_unit_name'];
               // Return default Double click code
        $html = '<amp-ad width='.esc_attr($width).' height='.esc_attr($height).' type="doubleclick" data-ad-slot="/'.esc_attr($network_code)."/".esc_attr($ad_unit_name). '/" data-multi-size="468x60,300x250"></amp-ad>';
            }else if($quads_options['ads'][$id]['ad_type'] == 'yandex'){

                  $html = '<amp-ad width='.esc_attr($width).' height='.esc_attr($height).' type="yandex" data-block-id="'.esc_attr($quads_options['ads'][$id]['block_id']).'" data-html-access-allowed="true"></amp-ad>';
            }else if($quads_options['ads'][$id]['ad_type'] == 'mgid'){

                    preg_match('/\/([a-z.]+)\.([0-9]+)\.js/', $quads_options['ads'][$id]['data_js_src'], $matches);
                  $html = '<amp-ad  width="600" height="600"
                                  type="mgid"
                                  data-publisher="'.esc_attr($matches[1]).'"
                                  data-widget="'.esc_attr($matches[2]).'"
                                  data-container="'.esc_attr($quads_options['ads'][$id]['data_container']).'"
                                >
                                </amp-ad>';
            }else if($quads_options['ads'][$id]['ad_type'] == 'ad_image'){
                    $html = '';
                if(isset($quads_options['ads'][$id]['image_redirect_url'])  && !empty($quads_options['ads'][$id]['image_redirect_url'])){
                        $html .= '
                        <a target="_blank" href="'.esc_attr($quads_options['ads'][$id]['image_redirect_url']). '" rel="nofollow">
                        <img  src="'.esc_attr($quads_options['ads'][$id]['image_src']). '" > 
                        </a>';
                    }else{
                        $html .= '<img src="'.esc_attr($quads_options['ads'][$id]['image_src']). '" >';
                    }
            }else if($quads_options['ads'][$id]['ad_type'] == 'taboola'){
                        $html = '<div id="quads_taboola_'.$id.'"></div>';
                          $html .= ' <amp-embed width="100" height="283"
                                         type=taboola
                                         layout=responsive
                                         heights="(min-width:1907px) 39%, (min-width:1200px) 46%, (min-width:780px) 64%, (min-width:480px) 98%, (min-width:460px) 167%, 196%"
                                         data-publisher="'.esc_attr($quads_options['ads'][$id]['taboola_publisher_id']).'"
                                         data-mode="thumbnails-a"
                                         data-placement="quads_taboola_'.$id.'"
                                         data-article="auto">
                                    </amp-embed>
                                  </div>
                                  </div>';

            }else if($quads_options['ads'][$id]['ad_type'] == 'media_net'){
                    $width = (isset($quads_options['ads'][$id]['g_data_ad_width']) && (!empty($quads_options['ads'][$id]['g_data_ad_width']))) ? $quads_options['ads'][$id]['g_data_ad_width']:300;
                    $height = (isset($quads_options['ads'][$id]['g_data_ad_height']) && (!empty($quads_options['ads'][$id]['g_data_ad_height']))) ? $quads_options['ads'][$id]['g_data_ad_height']:250;

                        $html = '<amp-ad 
                                    type="medianet"
                                    width="'. esc_attr($width) .'"
                                    height="'. esc_attr($height) .'"
                                                    data-tagtype="cm"    
                                    data-cid="'.esc_attr($quads_options['ads'][$id]['data_cid']).'"
                                    data-crid="'.esc_attr($quads_options['ads'][$id]['data_crid']).'"
                                    data-enable-refresh="10">
                                </amp-ad> ';

            }else if($quads_options['ads'][$id]['ad_type'] == 'mediavine'){
                    $width = (isset($quads_options['ads'][$id]['g_data_ad_width']) && (!empty($quads_options['ads'][$id]['g_data_ad_width']))) ? $quads_options['ads'][$id]['g_data_ad_width']:300;
                    $height = (isset($quads_options['ads'][$id]['g_data_ad_height']) && (!empty($quads_options['ads'][$id]['g_data_ad_height']))) ? $quads_options['ads'][$id]['g_data_ad_height']:250;

                        $html = ' <amp-ad width="'. esc_attr($width) .'"
                                          height="'. esc_attr($height) .'"
                                          type="mediavine"
                                          data-site="'.esc_attr($quads_options['ads'][$id]['mediavine_site_id']).'">
                                    </amp-ad>';

            }else if($quads_options['ads'][$id]['ad_type'] == 'outbrain'){
                    $width = (isset($quads_options['ads'][$id]['g_data_ad_width']) && (!empty($quads_options['ads'][$id]['g_data_ad_width']))) ? $quads_options['ads'][$id]['g_data_ad_width']:300;
                    $height = (isset($quads_options['ads'][$id]['g_data_ad_height']) && (!empty($quads_options['ads'][$id]['g_data_ad_height']))) ? $quads_options['ads'][$id]['g_data_ad_height']:250;

                        $html = '<amp-embed type="outbrain"
                                  width='. esc_attr($width) .'
                                  height='. esc_attr($height) . '
                                  data-widgetids='.esc_attr($quads_options['ads'][$id]['outbrain_widget_ids']).'
                                  data-enable-refresh="10">
                                </amp-sticky-ad>';

            }else{
                   // Return default adsense code

                if (isset($quads_options['ads'][$id]['adsense_type']) && $quads_options['ads'][$id]['adsense_type'] == 'normal') {
                    $width = (isset($quads_options['ads'][$id]['g_data_ad_width']) && (!empty($quads_options['ads'][$id]['g_data_ad_width']))) ? $quads_options['ads'][$id]['g_data_ad_width']:300;
                    $height = (isset($quads_options['ads'][$id]['g_data_ad_height']) && (!empty($quads_options['ads'][$id]['g_data_ad_height']))) ? $quads_options['ads'][$id]['g_data_ad_height']:250;

                    $html = '<amp-ad layout="fixed" width='.esc_attr($width).' height='.esc_attr($height).' type="adsense" data-ad-client="'. esc_attr($quads_options['ads'][$id]['g_data_ad_client']) . '" data-ad-slot="'.esc_attr($quads_options['ads'][$id]['g_data_ad_slot']).'"></amp-ad>';
                }else{

                    $data_auto_format ="rspv";
                    if( $quads_options['ads'][$id]['adsense_ad_type'] == 'matched_content'){
                      $data_auto_format ="mcrspv";
                    }
                    $html = '<amp-ad
                                  width="100vw"
                                  height="320"
                                  type="adsense"
                                  data-ad-client="'. esc_attr($quads_options['ads'][$id]['g_data_ad_client']) . '"
                                  data-ad-slot="'. esc_attr($quads_options['ads'][$id]['g_data_ad_slot']) . '"
                                  data-auto-format="'.$data_auto_format.'"
                                  data-full-width
                                >
                                  <div overflow></div>
                                </amp-ad>';
                }

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

    /**
     * This function returns publisher id or data ad client id for adsense ads
     * @return type
     */
    function quads_get_pub_id_on_revenue_percentage(){
          global $quads_options;
        $ad_owner_revenue_per       = '';
        $display_per_in_minute      = '';
        $author_adsense_ids         = array();

        if(isset($quads_options['ad_owner_revenue_per']) && $quads_options['ad_owner_revenue_per']){
            $ad_owner_revenue_per         =  isset( $quads_options['ad_owner_revenue_per'] ) ? $quads_options['ad_owner_revenue_per'] : 0;
            $display_per_in_minute      = (60*$ad_owner_revenue_per)/100;
            $current_second = date("s");

            if(!($current_second <= $display_per_in_minute)) {
             $author_adsense_ids['author_pub_id']     =  get_the_author_meta( 'quads_adsense_pub_id' );
            }
            return $author_adsense_ids;
        }
    }

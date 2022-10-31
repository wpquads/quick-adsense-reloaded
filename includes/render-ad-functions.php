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
    if( true === quads_is_propeller( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_propeller_async( $id ),$id );
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
    if( true === quads_is_loopad( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_loopad_async( $id ),$id );
    }
    if( true === quads_is_carousel_ads( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_carousel_ads_async( $id ),$id );
    }
    if( true === quads_is_floating_ads( $id, $string ) ) {
        return apply_filters( 'quads_render_ad', quads_render_floating_ads_async( $id ),$id );
    }
    // Return empty string
    return '';
}
function quads_common_head_code(){
    if(quads_is_amp_endpoint()){
        return;
    }
    global $quads_options;
    if ( isset($quads_options['lazy_load_global']) && $quads_options['lazy_load_global']== true) {
        echo quads_load_loading_script();
    }
    $data_slot  = '';
    $adsense     = false;
        if(isset($quads_options['ads'])){
        foreach ($quads_options['ads'] as $key => $value) {
            if(isset($value['ad_type']) && $value['ad_type'] == 'adsense'){
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
            }else if($ads['ad_type']== 'adpushup'){
                echo '<script data-cfasync="false" type="text/javascript">
                (function(w, d) {
                    var s = d.createElement("script");
                    s.src = "//cdn.adpushup.com/'.esc_attr($ads['adpushup_site_id']).'/adpushup.js";
                    s.crossOrigin="anonymous"; 
                    s.type = "text/javascript"; s.async = true;
                    (d.getElementsByTagName("head")[0] || d.getElementsByTagName("body")[0]).appendChild(s);
                    w.adpushup = w.adpushup || {que:[]};
                })(window, document);                
                </script>';
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
    global $quads_options,$quads_mode;
    $t_css = "" ;
      $width        = (isset($quads_options['ads'][$id]['g_data_ad_width']) && !empty($quads_options['ads'][$id]['g_data_ad_width'])) ? $quads_options['ads'][$id]['g_data_ad_width'] : '300';
        $height        = (isset($quads_options['ads'][$id]['g_data_ad_height']) && !empty($quads_options['ads'][$id]['g_data_ad_height'])) ? $quads_options['ads'][$id]['g_data_ad_height'] : '250';

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Doubleclick async --> \n\n";
    $post_id= quadsGetPostIdByMetaKeyValue('quads_ad_old_id', $id);
     $ad_meta = get_post_meta($post_id, '',true);

     $text_around_ad_check  = isset($ad_meta['text_around_ad_check'][0]) ? $ad_meta['text_around_ad_check'][0] : false;
     if($quads_mode =='new' && $text_around_ad_check){
        $position =  (isset($ad_meta['text_around_ad_text_label'][0]) && !empty($ad_meta['text_around_ad_text_label'][0]) )? $ad_meta['text_around_ad_text_label'][0] : 'above';
     }
     if( isset($position) && $position == "text_around_right" ){
         $t_css = "float: left;";
     }
     if( isset($position) && $position == "text_around_left" ){
         $t_css = "float: right;";
     }
     
    $html .= '<div class="wp_quads_dfp" id="wp_quads_dfp_'.esc_attr($quads_options['ads'][$id]['ad_id']). '" style="height:'.esc_attr($height). 'px; width:'.esc_attr($width). 'px; '.$t_css.' ">
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

    $image_render_src = $useragent = '';
    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content ImageBanner AD --> \n\n";
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' ;
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    {
        if(isset($quads_options['ads'][$id]['image_redirect_url'])  && !empty($quads_options['ads'][$id]['image_redirect_url'])){
            if( isset( $quads_options['ads'][$id]['image_mobile_src'] ) && !empty($quads_options['ads'][$id]['image_mobile_src'] ) && isset( $quads_options['ads'][$id]['mobile_image_check'] ) && $quads_options['ads'][$id]['mobile_image_check']!==false ) {
                $image_render_src = $quads_options['ads'][$id]['image_mobile_src'];
            }
            else{
                $image_render_src = $quads_options['ads'][$id]['image_src'];
            }
            if(isset($quads_options['ads'][$id]['parallax_ads_check']) && $quads_options['ads'][$id]['parallax_ads_check']){
                $parallax_height=$quads_options['ads'][$id]['parallax_height']?$quads_options['ads'][$id]['parallax_height']:300;
                $html .=' <a imagebanner target="_blank" href="'.esc_attr($quads_options['ads'][$id]['image_redirect_url']). '" rel="nofollow">
                 <div class="quads_parallax parallax_'.$id.'"></div>
                 </a>
                <style> .quads-ad'.$quads_options['ads'][$id]['ad_id'].' { margin:0 auto !important;} .parallax_'.$id.' {background-image: url("'.esc_attr($image_render_src).'");height:'.$parallax_height.'px;background-attachment: fixed;background-position: center;background-repeat: no-repeat;background-size: auto;}</style>';
            }
            else {
            $html .= '
            <a imagebanner target="_blank" href="'.esc_attr($quads_options['ads'][$id]['image_redirect_url']). '" rel="nofollow">
            <img  src="'.esc_attr($image_render_src).'" > 
            </a>';
            }
        }
    }
    else if (isset($quads_options['ads'][$id]['image_redirect_url'])  && !empty($quads_options['ads'][$id]['image_redirect_url'])){
        
        if(isset($quads_options['ads'][$id]['parallax_ads_check']) && $quads_options['ads'][$id]['parallax_ads_check']){
            $parallax_height=$quads_options['ads'][$id]['parallax_height']?$quads_options['ads'][$id]['parallax_height']:300;
            $html .='<a  imagebanner target="_blank" href="'.esc_attr($quads_options['ads'][$id]['image_redirect_url']). '" rel="nofollow">
             <div class="quads_parallax parallax_'.$id.'"></div>
             </a>
             <style> .quads-ad'.$quads_options['ads'][$id]['ad_id'].' { margin:0 auto !important;} .parallax_'.$id.' {background-image: url("'.esc_attr($quads_options['ads'][$id]['image_src']).'");height:'.$parallax_height.'px;background-attachment: fixed;background-position: center;background-repeat: no-repeat;background-size: auto;}</style>';
            
        }
        else {
            $html .= '
        <a imagebanner target="_blank" href="'.esc_attr($quads_options['ads'][$id]['image_redirect_url']). '" rel="nofollow">
        <img  src="'.esc_attr($quads_options['ads'][$id]['image_src']). '" > 
        </a>';
        }
        
    }else{
        if(isset($quads_options['ads'][$id]['parallax_ads_check']) && $quads_options['ads'][$id]['parallax_ads_check']){
            
            $parallax_height=$quads_options['ads'][$id]['parallax_height']?$quads_options['ads'][$id]['parallax_height']:300;
            $html .='<div class="quads_parallax parallax_'.$id.'"></div>
            <style>  .quads-ad'.$quads_options['ads'][$id]['ad_id'].' { margin:0 auto !important;} .parallax_'.$id.' {background-image: url("'.esc_attr($image_render_src).'");height:'.$parallax_height.'px;background-attachment: fixed;background-position: center;background-repeat: no-repeat;background-size: auto;}</style>';
        
    }
        else{
            $html .= '<img src="'.esc_attr($quads_options['ads'][$id]['image_src']). '" >';
        }
        
    }

    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_ad_image_async', $html );
}

function quads_render_ad_video_async( $id ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Yandex async --> \n\n";
    $vid_width = isset($quads_options['ads'][$id]['image_width']) ? $quads_options['ads'][$id]['image_width'] : '' ;
    $vid_height = isset($quads_options['ads'][$id]['image_height']) ? $quads_options['ads'][$id]['image_height'] : '' ;
    if(isset($quads_options['ads'][$id]['image_src'])  && !empty($quads_options['ads'][$id]['image_src'])){
        $html .= '
        <iframe width="'.$vid_width.'" height="'.$vid_height.'" src="'.$quads_options['ads'][$id]['image_src'].'" 
            frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen></iframe>
        ';
    }
    else{
        $html .= '<iframe width="560" height="315" src="'.$quads_options['ads'][$id]['image_src'].'" 
        frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
        allowfullscreen></iframe>';
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
                                    var infolinks_adid = '.esc_attr($quads_options['ads'][$id]['ad_id']).';
                                  </script>
                                <script type="text/javascript" src="//resources.infolinks.com/js/infolinks_main.js"></script>';

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

function quads_render_propeller_async( $id ) {
    global $quads_options;

    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content propeller --> \n\n";
    $html .= '                             
                <div id="'.$id.' propeller-ad">
                <script type="text/javascript"> '.esc_attr($quads_options['ads'][$id]['propeller_js']).'"
                </script>
                </div>
            ';
    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_propeller_async', $html );
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
    if(function_exists('quads_hide_markup') && quads_hide_markup()  ) {
        $html = "";
    }else{
        $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";
    }
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
        $html = str_replace( '></ins>', '><span></span></ins></div>', $html );
        $code = 'instant= new adsenseLoader( \'#quads-' . esc_attr($id) . '-place\', {
        onLoad: function( ad ){
            if (ad.classList.contains("quads-ll")) {
                ad.classList.remove("quads-ll");
            }
          }   
        });';
        $html = str_replace( '(adsbygoogle = window.adsbygoogle || []).push({});', $code, $html );
    }
    if(function_exists('quads_hide_markup') && quads_hide_markup()  ) {
    }else{
        $html .= "\n <!-- end WP QUADS --> \n\n";
    }
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

    if(function_exists('quads_hide_markup') && quads_hide_markup()  ) {
        $html = "";
    }else{
        $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";
    }
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
    if(function_exists('quads_hide_markup') && quads_hide_markup()  ) {
    }else{
        $html .= "\n <!-- end WP QUADS --> \n\n";
    }


        return apply_filters( 'quads_render_adsense_async', $html );
}
/**
 * Render Loop ad
 *
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_loopad_async( $id ) {
    global $quads_options;
    $image_render_src = $useragent = '';
    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Loop AD --> \n\n";
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' ;
    
        $html.='<article class="post type-post status-publish format-standard has-post-thumbnail">
	<div class="post-item">';
       
        $html .='<div class="entry-container">';    
        if(isset($quads_options['ads'][$id]['loop_add_link']) && isset($quads_options['ads'][$id]['loop_add_title'])){    
	$html .='<header class="entry-header">
				<h2 class="entry-title default-max-width"><a href="'.$quads_options['ads'][$id]['loop_add_link'].'" rel="sponsored">'.$quads_options['ads'][$id]['loop_add_title'].'</a></h2>
            </header><!-- .entry-header -->';
        }
        if(isset($quads_options['ads'][$id]['loop_add_link'])  && !empty($quads_options['ads'][$id]['loop_add_link']) && isset($quads_options['ads'][$id]['image_src'])  && !empty($quads_options['ads'][$id]['image_src'])){
            $html .='<div class="featured-image">
                        <a href="'.$quads_options['ads'][$id]['loop_add_link'].'" class="post-thumbnail-link"  rel="sponsored">
                        <img src="'.$quads_options['ads'][$id]['image_src'].'" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="" loading="lazy" style="width:100%;height:66.57%;max-width:350px;">
                        </a>
                </div><!-- .featured-image -->';
                }
        if(isset($quads_options['ads'][$id]['loop_add_description'])){    
            $html .='<div class="entry-content">
                        <p>'.$quads_options['ads'][$id]['loop_add_description'].'
                        <a class="more-link" href="'.$quads_options['ads'][$id]['loop_add_link'].'">Learn More <span class="screen-reader-text">'.$quads_options['ads'][$id]['loop_add_title'].'</span></a></p>
                     </div><!-- .entry-content -->';
                }
    $html.='</div><!-- .entry-container -->
    </div><!-- .post-item -->
</article>';
    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_loopad_async', $html );
}

/**
 * Carousel ads which can be enabled from general settings
 *
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_carousel_ads_async($id) {
    
    global $quads_options;
    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Carousel AD --> \n\n";
    $ads_list = $quads_options['ads'][$id]['ads_list'];
    $org_ad_id = $quads_options['ads'][$id]['ad_id'];
    $carousel_type = isset($quads_options['ads'][$id]['carousel_type'])?$quads_options['ads'][$id]['carousel_type']:'slider';
    $carousel_width = isset($quads_options['ads'][$id]['carousel_width'])?$quads_options['ads'][$id]['carousel_width']:450;
    $carousel_height = isset($quads_options['ads'][$id]['carousel_height'])?$quads_options['ads'][$id]['carousel_height']:350;
    $carousel_speed = isset($quads_options['ads'][$id]['carousel_speed'])?$quads_options['ads'][$id]['carousel_speed']:1;
    if($carousel_type=="slider")
    {
        $html.='<div class="quads-content quads-section" style="max-width:100%;overflow:hidden;">';
    }
   
    $total_slides=count($ads_list);
    foreach($ads_list as $ad)
    {
        if(isset($ad['value']))
        {   
            if($carousel_type=="slider")
            {
                $html.='<div class="quads-location quads-slides-'.$org_ad_id.' quads-animate-right" id="quads-ad'.$ad['value'].'" style="width:100%">';
            }
           

            $ad_id="ad".$ad['value'];
            $ad_meta=get_post_meta($ad['value']);
         
            if(isset($ad_meta['ad_type']) && isset($ad_meta['ad_type'][0]) && $ad_meta['ad_type'][0]=='ad_image' && isset($ad_meta['image_src'][0]) && isset($ad_meta['image_redirect_url'][0]) )
            {	$image=$ad_meta['image_src'][0];
		        $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' ;
				 if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)) && isset( $ad_meta['image_mobile_src'] ) && !empty($ad_meta['image_mobile_src'] )){
					 $image=$ad_meta['image_mobile_src'][0];
				 }
                $html .='<a imagebanner class="im-'.$org_ad_id.'" target="_blank" href="'.esc_attr($ad_meta['image_redirect_url'][0]). '" rel="nofollow"><img  class="quads_carousel_img" src="'.esc_attr($image).'" > </a>';  
            }
        
            if(isset($ad_meta['ad_type']) && isset($ad_meta['ad_type'][0]) && $ad_meta['ad_type'][0]=='plain_text')
            {
                if(isset($ad_meta['code']) && isset($ad_meta['code'][0]))
                {
                    $html .=$ad_meta['code'][0];
                }   
            }
                $html.='</div>';
        }
    }

    if($carousel_type=="slider")
    {
        $html.='</div><style>@media only screen and (max-width: 480px) {.quads_carousel_img { width:100%}}.quads_carousel_img { width:auto;}.quads-slides-'.$org_ad_id.'{display:none}.quads-container:after,.quads-container:before{content:"";display:table;clear:both}.quads-container{padding:.01em 16px}.quads-content{margin-left:auto;margin-right:auto;max-width:100%}.quads-section{margin-top:16px!important;margin-bottom:16px!important}.quads-animate-right{position:relative;animation: animateright 0.5s}@keyframes animateright{from{right:-300px;opacity:0}to{right:0;opacity:1}}</style>
        <script>var myIndex_'.$org_ad_id.' = 0;setTimeout(quads_carousel_'.$org_ad_id.', 1000);function quads_carousel_'.$org_ad_id.'() {var i;var x = document.getElementsByClassName("quads-slides-'.$org_ad_id.'");for (i = 0; i < x.length; i++) {x[i].style.display = "none";}myIndex_'.$org_ad_id.'++;if (myIndex_'.$org_ad_id.' > x.length) {myIndex_'.$org_ad_id.' = 1} x[myIndex_'.$org_ad_id.'-1].style.display = "block"; var nid= x[myIndex_'.$org_ad_id.'-1].id;    if(x.length>1) { setTimeout(quads_carousel_'.$org_ad_id.', '.($carousel_speed*1000).');} }</script>';
    }
   
    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_carousel_ads_async', $html );

}


/**
 * Floating ads which can be enabled from general settings
 *
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_floating_ads_async($id) {
    
    global $quads_options;
    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Floating AD --> \n\n";
    $ads_list = $quads_options['ads'][$id]['floating_slides'];
    $org_ad_id = $quads_options['ads'][$id]['ad_id'];
    $floating_type = isset($quads_options['ads'][$id]['floating_cubes_type'])?$quads_options['ads'][$id]['floating_cubes_type']:'flip';
    $floating_size = isset($quads_options['ads'][$id]['floating_cubes_size'])?$quads_options['ads'][$id]['floating_cubes_size']:'200';
    $floating_position = isset($quads_options['ads'][$id]['floating_position'])?$quads_options['ads'][$id]['floating_position']:'bottom-right';
    $position_array =['top-left'=>'position:fixed !important;top:0px;left:10px;','top-right'=>'position:fixed !important;top:0px;right:10px;','bottom-left'=>'position:fixed !important;bottom:40px;left:10px;','bottom-right'=>'position:fixed !important;bottom:40px;right:10px;'];
    
        $html.='<section class="wpquads-3d-container" id="con-'.esc_attr($id).'">
        <div class="wpquads-3d-close"><a href="javascript:void(0);" id="wpquads-close-btn">&times;</a></div>
        <div class="wpquads-3d-cube" id="wpquads-3d-cube">';
   
   
    $total_slides=count($ads_list);
    foreach($ads_list as $ad)
    {
        if(isset($ad['slide']))
        { 
            
            
            if(isset($ad['slide']) && isset($ad['link']))
            {
            $html.='	<figure class="wpquads-3d-item ">
            <a href="'.esc_attr($ad['link']).'" target="_blank" rel="nofollow">
                <img src="'.esc_attr($ad['slide']).'" >
                </a>
        </figure>';
            }
        }
    }

    if($total_slides<6)
    {
        $fill_loop=6-$total_slides;
        for($i=0;$i<$fill_loop;$i++)
        {
            $new=(int) $i/$total_slides;
            if(isset($ads_list[$new]['slide']))
        {
            $html.='	<figure class="wpquads-3d-item ">
            <a href="'.esc_attr($ads_list[$new]['link']).'" target="_blank" rel="nofollow">
                <img src="'.esc_attr($ads_list[$new]['slide']).'" >
                </a>
        </figure>';
        }
        }
    }

 
        $html.='</div></section><style>.wpquads-3d-close{text-align:right;}#wpquads-close-btn{text-decoration:none !important;cursor:pointer;}.wpquads-3d-cube .wpquads-3d-item,.wpquads-3d-cube .wpquads-3d-item img{display:block;margin:0;width:100%;height:100%}.wpquads-3d-container{'.esc_attr($position_array[$floating_position]).'width:'.esc_attr($floating_size).'px;height:'.esc_attr($floating_size).'px;border-radius:3px;position:relative;-webkit-perspective:1000px;-moz-perspective:1000px;-ms-perspective:1000px;-o-perspective:1000px;perspective:1000px;z-index:999999;}.wpquads-3d-cube{width:100%;height:100%;position:absolute;-webkit-transition:-webkit-transform 1s;-moz-transition:-moz-transform 1s;-o-transition:-o-transform 1s;transition:transform 1s;-webkit-transform-style:preserve-3d;-moz-transform-style:preserve-3d;-ms-transform-style:preserve-3d;-o-transform-style:preserve-3d;transform-style:preserve-3d;-webkit-transform:translateZ(-'.esc_attr($floating_size/2).'px);-moz-transform:translateZ(-'.esc_attr($floating_size/2).'px);-ms-transform:translateZ(-'.esc_attr($floating_size/2).'px);-o-transform:translateZ(-'.esc_attr($floating_size/2).'px);transform:translateZ(-'.esc_attr($floating_size/2).'px)}.wpquads-3d-cube .wpquads-3d-item{position:absolute;border:3px inset;border-style:outset}.wpquads-3d-item:first-child{-webkit-transform:rotateY(0) translateZ('.esc_attr($floating_size/2).'px);-moz-transform:rotateY(0) translateZ('.esc_attr($floating_size/2).'px);-ms-transform:rotateY(0) translateZ('.esc_attr($floating_size/2).'px);-o-transform:rotateY(0) translateZ('.esc_attr($floating_size/2).'px);transform:rotateY(0) translateZ('.esc_attr($floating_size/2).'px)}.wpquads-3d-item:nth-child(2){-webkit-transform:rotateX(180deg) translateZ('.esc_attr($floating_size/2).'px);-moz-transform:rotateX(180deg) translateZ('.esc_attr($floating_size/2).'px);-ms-transform:rotateX(180deg) translateZ('.esc_attr($floating_size/2).'px);-o-transform:rotateX(180deg) translateZ(150px);transform:rotateX(180deg) translateZ('.esc_attr($floating_size/2).'px)}.wpquads-3d-item:nth-child(3){-webkit-transform:rotateY(90deg) translateZ('.esc_attr($floating_size/2).'px);-moz-transform:rotateY(90deg) translateZ('.esc_attr($floating_size/2).'px);-ms-transform:rotateY(90deg) translateZ('.esc_attr($floating_size/2).'px);-o-transform:rotateY(90deg) translateZ('.esc_attr($floating_size/2).'px);transform:rotateY(90deg) translateZ('.esc_attr($floating_size/2).'px)}.wpquads-3d-item:nth-child(4){-webkit-transform:rotateY(-90deg) translateZ('.esc_attr($floating_size/2).'px);-moz-transform:rotateY(-90deg) translateZ('.esc_attr($floating_size/2).'px);-ms-transform:rotateY(-90deg) translateZ('.esc_attr($floating_size/2).'px);-o-transform:rotateY(-90deg) translateZ('.esc_attr($floating_size/2).'px);transform:rotateY(-90deg) translateZ('.esc_attr($floating_size/2).'px)}.wpquads-3d-item:nth-child(5){-webkit-transform:rotateX(90deg) translateZ('.esc_attr($floating_size/2).'px);-moz-transform:rotateX(90deg) translateZ('.esc_attr($floating_size/2).'px);-ms-transform:rotateX(90deg) translateZ('.esc_attr($floating_size/2).'px);-o-transform:rotateX(90deg) translateZ('.esc_attr($floating_size/2).'px);transform:rotateX(90deg) translateZ('.esc_attr($floating_size/2).'px)}.wpquads-3d-item:nth-child(6){-webkit-transform:rotateX(-90deg) translateZ('.esc_attr($floating_size/2).'px);-moz-transform:rotateX(-90deg) translateZ('.esc_attr($floating_size/2).'px);-ms-transform:rotateX(-90deg) translateZ('.esc_attr($floating_size/2).'px);-o-transform:rotateX(-90deg) translateZ('.esc_attr($floating_size/2).'px);transform:rotateX(-90deg) translateZ('.esc_attr($floating_size/2).'px)}.wpquads-slide0-active{-webkit-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(0);-moz-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(0);-ms-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(0);-o-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(0);transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(0)}.wpquads-slide1-active{-webkit-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-180deg);-moz-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-180deg);-ms-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-180deg);-o-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-180deg);transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-180deg)}.wpquads-slide2-active{-webkit-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(-90deg);-moz-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(-90deg);-ms-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(-90deg);-o-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(-90deg);transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(-90deg)}.wpquads-slide3-active{-webkit-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(90deg);-moz-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(90deg);-ms-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(90deg);-o-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(90deg);transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateY(90deg)}.wpquads-slide4-active{-webkit-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-90deg);-moz-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-90deg);-ms-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-90deg);-o-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-90deg);transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(-90deg)}.wpquads-slide5-active{-webkit-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(90deg);-moz-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(90deg);-ms-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(90deg);-o-transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(90deg);transform:translateZ(-'.esc_attr($floating_size/2).'px) rotateX(90deg)}</style>
        <script>const wpquads_3d_slides=document.querySelectorAll(".wpquads-3d-item"),wpquads_3d_slider=document.getElementById("wpquads-3d-cube");let activeSlide=0;function changeSlide(){wpquads_3d_slider.classList.remove("wpquads-slide"+activeSlide+"-active"),++activeSlide>=wpquads_3d_slides.length&&(activeSlide=0),wpquads_3d_slider.classList.add("wpquads-slide"+activeSlide+"-active")}setInterval(changeSlide,5e3);const close_element = document.getElementById("wpquads-close-btn");close_element.addEventListener("click", function() {document.getElementById("con-'.esc_attr($id).'").style.display = "none";});</script>';
   
    $html .= "\n <!-- end WP QUADS --> \n\n";
    return apply_filters( 'quads_render_floating__ads_async', $html );

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

function quads_is_popup_ad( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'popup_ads') {
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
 * Check if ad code is Propeller or other ad code
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_propeller( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'propeller') {
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
function quads_is_ad_video( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'video_ads') {
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
 * Check if ad code is Loop ad
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_loopad( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'loop_ads') {
        return true;
    }
    return false;
}
/**
 * Check if ad code is Carousel ad
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_carousel_ads( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'carousel_ads') {
        return true;
    }
    return false;
}
/**
 * Check if ad code is Floating ad
 *
 * @param1 id int id of the ad
 * @param string $string ad code
 * @return boolean
 */
function quads_is_floating_ads( $id, $string ) {
    global $quads_options;

    if( isset($quads_options['ads'][$id]['ad_type']) && $quads_options['ads'][$id]['ad_type'] === 'floating_cubes') {
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
        $html = '<amp-ad width='.esc_attr($width).' height='.esc_attr($height).' type="doubleclick" data-slot="/'.esc_attr($network_code)."/".esc_attr($ad_unit_name). '/" data-multi-size="468x60,300x250"></amp-ad>';
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
                $html = '';$parallax = false;$parallax_height=300;
                
                list($width, $height) = getimagesize($quads_options['ads'][$id]['image_src']);
                if(isset($quads_options['ads'][$id]['parallax_ads_check'])  && $quads_options['ads'][$id]['parallax_ads_check']){
                    $parallax=true;
                    $parallax_height=($quads_options['ads'][$id]['parallax_height']>1)?$quads_options['ads'][$id]['parallax_height']:300;
                }
            if(isset($quads_options['ads'][$id]['image_redirect_url'])  && !empty($quads_options['ads'][$id]['image_redirect_url'])){
               
                $html .= '
                    <a target="_blank" href="'.esc_attr($quads_options['ads'][$id]['image_redirect_url']). '" rel="nofollow">';
                    if($parallax){
                        $html .=' <amp-fx-flying-carpet height="'.$parallax_height.'px">';       
                    }
                    $html .=' <amp-img
src="'.esc_attr($quads_options['ads'][$id]['image_src']). '"
width="'.esc_attr($width).'"
height="'.esc_attr($height).'"
layout="responsive"
>
</amp-img>';
        if($parallax){
            $html .='</amp-fx-flying-carpet>';
        }            
            $html .='</a>';
                }else{
                    if($parallax){
                        $html .=' <amp-fx-flying-carpet height="'.$parallax_height.'px">';       
                    }
                    $html .= '                        <amp-img
                    src="'.esc_attr($quads_options['ads'][$id]['image_src']). '"
                    width="'.esc_attr($width).'"
                    height="'.esc_attr($height).'"
                    layout="responsive"
                  >
                  </amp-img>';
                  if($parallax){
                    $html .='</amp-fx-flying-carpet>';
                }
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

            } else if($quads_options['ads'][$id]['ad_type'] == 'adpushup'){
                
                $width = (isset($quads_options['ads'][$id]['g_data_ad_width']) && (!empty($quads_options['ads'][$id]['g_data_ad_width']))) ? $quads_options['ads'][$id]['g_data_ad_width']:300;
                $height = (isset($quads_options['ads'][$id]['g_data_ad_height']) && (!empty($quads_options['ads'][$id]['g_data_ad_height']))) ? $quads_options['ads'][$id]['g_data_ad_height']:250;

                $html = '<amp-ad width="'.esc_attr($width).'" height="'.esc_attr($height).'"
                type="adpushup"
                data-siteid="'.esc_attr($quads_options['ads'][$id]['adpushup_site_id']).'"
                data-slotpath="'.esc_attr($quads_options['ads'][$id]['adpushup_slot_id']).'"
                data-totalAmpSlots="1">
                </amp-ad>';

            } else if($quads_options['ads'][$id]['ad_type'] == 'loop_ads'){
                
                if(isset($quads_options['ads'][$id]['loop_add_link']) && isset($quads_options['ads'][$id]['loop_add_title']) && isset($quads_options['ads'][$id]['loop_add_description']))
                {    
                $html = '<div class="fsp">'; 
                if(isset($quads_options['ads'][$id]['image_src']) && !empty($quads_options['ads'][$id]['image_src'])){
                    list($width, $height) = getimagesize($quads_options['ads'][$id]['image_src']);
                $html .='<div class="fsp-img">
                            <div class="loop-img image-container">
                                <a href="'.$quads_options['ads'][$id]['loop_add_link'].'" title="'.esc_attr($quads_options['ads'][$id]['loop_add_title']).'">
                                <amp-img
                                        alt="'.esc_attr($quads_options['ads'][$id]['loop_add_title']).'"
                                        src="'.esc_attr($quads_options['ads'][$id]['image_src']).'"
                                        width="'.esc_attr($width).'"
                                        height="'.esc_attr($height).'"
                                        layout="responsive"
                                        >
                                        </amp-img>    
                                </a></div> </div>';
                }
                $html .='     <div class="fsp-cnt">
                                <h2 class="loop-title"><a href="'.$quads_options['ads'][$id]['loop_add_link'].'">'.$quads_options['ads'][$id]['loop_add_title'].'</a></h2>
                                <p class="loop-excerpt">'.$quads_options['ads'][$id]['loop_add_description'].'</p> 
                                <div class="pt-dt"> &nbsp; </div> 
                            </div>
                         </div>
                         <style>.quads-ad'.$quads_options['ads'][$id]['ad_id'].' { flex-basis: calc(33.33% - 30px); } @media (max-width: 425px){.quads-ad'.$quads_options['ads'][$id]['ad_id'].' {flex-basis: calc(100% - 0px);margin: 15px 0px;}</style>';
                }
        

            }else if($quads_options['ads'][$id]['ad_type'] == 'carousel_ads'){
                
                if(isset($quads_options['ads'][$id]['ads_list']) && !empty($quads_options['ads'][$id]['ads_list']))
                {   
                    $carousel_type = isset($quads_options['ads'][$id]['carousel_type'])?$quads_options['ads'][$id]['carousel_type']:'slider';
                    $carousel_speed = isset($quads_options['ads'][$id]['carousel_speed'])?$quads_options['ads'][$id]['carousel_speed']:1;    
                    $carousel_width = isset($quads_options['ads'][$id]['carousel_width'])?$quads_options['ads'][$id]['carousel_width']:450;
                    $carousel_height = isset($quads_options['ads'][$id]['carousel_height'])?$quads_options['ads'][$id]['carousel_height']:350;
                $html = '<amp-carousel '.($carousel_type=='slider'?'width='.$carousel_width:'').'  height="'.$carousel_height.'"     layout="'.($carousel_type=='slider'?'responsive':'fixed-height').'"      type="'.($carousel_type=='slider'?'slides':'carousel').'" '.($carousel_type=='slider'?'autoplay delay="'.($carousel_speed*1000).'"':'').' role="region" aria-label="Carousel Ads">'; 
                if(isset($quads_options['ads'][$id]['image_src']) && !empty($quads_options['ads'][$id]['image_src'])){
                    list($carousel_width, $carousel_height) = getimagesize($quads_options['ads'][$id]['image_src']);
                }
                $ads_list = $quads_options['ads'][$id]['ads_list'];
                 
                foreach($ads_list as $ad)
                {
                    if(isset($ad['value']))
                    { 
                        $ad_meta=get_post_meta($ad['value']);
                        if(isset($ad_meta['ad_type']) && isset($ad_meta['ad_type'][0]) && $ad_meta['ad_type'][0]=='ad_image' && isset($ad_meta['image_src'][0]) && isset($ad_meta['image_redirect_url'][0]))
                        {
                            $html .='
                            <a  href="'.$ad_meta['image_redirect_url'][0].'" target="_blank">
                            <amp-img
                                    alt="'.esc_attr($ad_meta['quads_ad_old_id'][0]).'"
                                    src="'.esc_attr($ad_meta['image_src'][0]).'"
                                    width="'.esc_attr($carousel_width).'"
                                    height="'.esc_attr($carousel_height).'"
                                    layout="responsive"
                                    >
                                    </amp-img>    
                            </a>';
                        }

                        else
                        {
                            $html.="<div>".$ad_meta['code'][0]."</div>";
                        }
                    
                    }
                    
                }
            
                
                $html .='</amp-carousel>';
                }
        

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
add_filter( 'quads_render_ad', 'quads_render_ad_text_around_ad_new',99,2 );
function quads_render_ad_text_around_ad_new( $adcode,$id='') {
    global $quads_options,$quads_mode;
 
    $post_id= quadsGetPostIdByMetaKeyValue('quads_ad_old_id', $id);
     $ad_meta = get_post_meta($post_id, '',true);
     if (quads_is_amp_endpoint()){
         if(!isset($ad_meta['enabled_on_amp'][0]) || (isset($ad_meta['enabled_on_amp'][0]) && (empty($ad_meta['enabled_on_amp'][0])|| !$ad_meta['enabled_on_amp'][0]) )){
             return $adcode;
         }
     }
     $text_around_ad_check  = isset($ad_meta['text_around_ad_check'][0]) ? $ad_meta['text_around_ad_check'][0] : false;

     if($quads_mode =='new' && $text_around_ad_check){
         $position =  (isset($ad_meta['text_around_ad_text_label'][0]) && !empty($ad_meta['text_around_ad_text_label'][0]) )? $ad_meta['text_around_ad_text_label'][0] : 'above';
         $text_around_ad_text =  (isset($ad_meta['text_around_ad_text'][0]) && !empty($ad_meta['text_around_ad_text'][0])) ? $ad_meta['text_around_ad_text'][0] : 'Advertisements';
          $label = apply_filters( 'quads_ad_label', $text_around_ad_text );
        $html = '<div class="quads-text-around-ad-label-'.$position.' quads-text-around-label-new">' . sanitize_text_field($label) . '</div>';
        if (defined('QUADS_PRO_VERSION') && QUADS_PRO_VERSION >= '2.0') {
             $css = '.quads-ad-label{display:none}  .quads-ad-label.quads-ad-label-new{display:block}';
             wp_dequeue_style('quads-ad-label');
             wp_deregister_style('quads-ad-label');
             wp_register_style( 'quads-ad-label', false );
             wp_enqueue_style( 'quads-ad-label' );
             wp_add_inline_style( 'quads-ad-label', $css );
         }
 
        if( $position == 'text_around_above' ) {
           return $html . $adcode;
        }
        if( $position == 'text_around_below' ) {
           return $adcode . $html;
        }
        if( $position == 'text_around_left' ) {
           return $adcode . $html;
        }
        if( $position == 'text_around_right' ) {
           return $adcode . $html;
        }
     }
     return $adcode;
    
}

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


    

jQuery( document ).ready(function($) {

    // setting cookie when button is closed
    function set_quads_Cookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function quads_show_popup(cname) {
        if (cname) {
            let name = cname + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for(let i = 0; i <ca.length; i++) {
              let c = ca[i];
              while (c.charAt(0) == ' ') {
                c = c.substring(1);
              }
              if (c.indexOf(name) == 0) {
                return false;
              }
            }
            
        }
        return true;
    }
    
    setTimeout(() => {
        $("#btn_close").click(function() {  
            $(".quads-popupad").css("display", "none");
            let cketimer = $(".quads-popupad").attr('data-cke');
            if (cketimer == 'no') {
                cketimer = 365;
            }
            set_quads_Cookie('quads_popup','popup_ad',cketimer);
          });
    }, 500);
    // showing popup after respective time as per settings
    var data_popuptype = $(".quads-popupad").attr('data-popuptype');
    var data_timer = $(".quads-popupad").attr('data-timer');
    
    if( data_popuptype && (data_popuptype == "specific_time_popup" || data_popuptype == "load_on_top" || data_popuptype == "load_on_bottom")){
        setTimeout(() => {
            $(".quads-popupad").css("display", "block");
        }, data_timer);
    }
    // showing popup after respective scroll as per settings
    var data_popuptype = $(".quads-popupad").attr('data-popuptype');
    var data_percent = $(".quads-popupad").attr('data-percent');
    if( data_popuptype == "on_scroll_popup"  ){
        window.addEventListener("scroll", () => {
            if(quads_show_popup('quads_popup')){
                let scrollTop = window.scrollY;
                let docHeight = document.body.offsetHeight;
                let winHeight = window.innerHeight;
                let scrollPercent = scrollTop / (docHeight - winHeight);
                let scrollPercentRounded = Math.round(scrollPercent * 100);
                if( scrollPercentRounded>=data_percent  ) {
                    $(".quads-popupad").css("display", "block");
                }
                else{
                    $(".quads-popupad").css("display", "none");
                }
            }
          
          });
    }
     

    /**
     * we are here iterating on each group div to display all ads
     * randomly or ordered on interval or on reload
     */
    $(".quads-groups-ads-json").each(function(){
        var ad_data_json = $(this).attr('data-json');
        var obj = JSON.parse(ad_data_json);
        var ads_group_id = obj.quads_group_id;
        var ads_group_refresh_type = obj.quads_popup_type;
        var ads_group_ref_interval_sec = obj.quads_group_ref_interval_sec;
        var ad_ids = obj.ads;
        var ad_ids_length = Object.keys(ad_ids).length;
        var i=0;

        if(ads_group_refresh_type ==='popupads'){
            quadsShowAdsById(ads_group_id, ad_ids[i]);
            var quads_ad_popupads = function () {
                if(i >= ad_ids_length){
                    i = 0;
                }
                var adbyindex ='';
                adbyindex = ad_ids[i];
                quadsShowAdsById(ads_group_id, adbyindex);
                i++;
                setTimeout(quads_ad_popupads, ads_group_ref_interval_sec);
            };
          
        }
    });
});

function quadsShowAdsById(ads_group_id, adbyindex){
    var container = jQuery(".quads_ad_container_[data-id='"+ads_group_id+"']");
    var container_pre = jQuery(".quads_ad_container__pre[data-id='"+ads_group_id+"']");
    var content ='';
    var image_src='';
    switch(adbyindex.ad_type[0]){
        case "plain_text":
            content +=adbyindex.code[0];
            container.html(content);
            break;
        case "adsense":
            // var bannersize =(adbyindex.ad_banner_size).split("x");
            var width = adbyindex.width[0];
            var height = adbyindex.height[0];
            if(adbyindex.ad_adsense_type[0] == "normal"){
                content +='<script async="" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
                content +='<ins class="adsbygoogle" style="display:inline-block;width:'+width+'px;height:'+height+'px" data-ad-client="'+adbyindex.ad_data_client_id+'" data-ad-slot="'+adbyindex.ad_data_ad_slot+'"></ins>';
            }
            container.html(content);
            break;
        case "double_click":
            var width = adbyindex.width[0];
            var height = adbyindex.height[0];
            var data_slot ="googletag.defineSlot('"+adbyindex.network_code+"/"+adbyindex.ad_unit_name+"/', ["+width+", "+height+"], 'wp_quads_dfp_"+ads_group_id+"');";
            content +="<script async src='https://securepubads.g.doubleclick.net/tag/js/gpt.js'></script><script>window.googletag = window.googletag || {cmd: []}; googletag.cmd.push(function() { "+data_slot+"googletag.pubads().enableSingleRequest();googletag.enableServices(); });  </script>";

            content +='<div id="wp_quads_dfp_'+ads_group_id+'" style="height:'+height+'px; width:'+width+'px;"><script>googletag.cmd.push(function() { googletag.display("wp_quads_dfp_'+ads_group_id+'"); });</script></div>';
            container.html(content);
            break;
        case "yandex":
            var width = adbyindex.width[0];
            var height = adbyindex.height[0];
            var data_slot ="googletag.defineSlot('"+adbyindex.network_code+"/"+adbyindex.ad_unit_name+"/', ["+width+", "+height+"], 'wp_quads_dfp_"+ads_group_id+"');";

            content +='<div id="yandex_rtb_'+adbyindex.block_id+'" ></div>\n' +
                '                       <script>\n' +
                '    (function(w, d, n, s, t) {\n' +
                '        w[n] = w[n] || [];\n' +
                '        w[n].push(function() {\n' +
                '            Ya.Context.AdvManager.render({\n' +
                '                blockId: "'+adbyindex.block_id+ '",\n' +
                '                renderTo: "yandex_rtb_'+adbyindex.block_id+'",\n' +
                '                async: true\n' +
                '            });\n' +
                '        });\n' +
                '        t = d.getElementsByTagName("script")[0];\n' +
                '        s = d.createElement("script");\n' +
                '        s.type = "text/javascript";\n' +
                '        s.src = "//an.yandex.ru/system/context.js";\n' +
                '        s.async = true;\n' +
                '        t.parentNode.insertBefore(s, t);\n' +
                '    })(this, this.document, "yandexContextAsyncCallbacks");\n' +
                '</script>';
            container.html(content);
            break;
        case "mgid":
            content +=' <div id="'+adbyindex.data_container+'"></div> <script src="'+adbyindex.data_js_src+'" async></script>';
            container.html(content);
            break;
        case "taboola":
            content +='<script>window._taboola = window._taboola || [];\n' +
                '              _taboola.push({article:"auto"});\n' +
                '              !function (e, f, u) {\n' +
                '                e.async = 1;\n' +
                '                e.src = u;\n' +
                '                f.parentNode.insertBefore(e, f);\n' +
                '              }(document.createElement("script"), document.getElementsByTagName("script")[0], "//cdn.taboola.com/libtrc/'+adbyindex.taboola_publisher_id+'/loader.js");\n' +
                '              </script>';
            container.html(content);
            break;
        case "media_net":
            var width = adbyindex.width[0];
            var height = adbyindex.height[0];
            var image_src='';
            content +='<script id="mNCC" language="javascript">';
            content +='medianet_width = '+width+';';
            content +='medianet_height = '+height+';';
            content +='medianet_crid ='+adbyindex.data_crid;
            content +='medianet_versionId ="3111299";';
            content +='</script>';
            content +='<script src="//contextual.media.net/nmedianet.js?cid='+adbyindex.data_cid+'"></script>';
            container.html(content);
            break;
        case "mediavine":
            content += '<link rel="dns-prefetch" href="//scripts.mediavine.com" />\n' +
                '                  <script async="async" data-noptimize="1" data-cfasync="false" src="//scripts.mediavine.com/tags/'+adbyindex.mediavine_site_id+'.js?ver=5.2.3"></script>';
            container.html(content);
            break;
        case "outbrain":
            content += '<script async="async" src="http://widgets.outbrain.com/outbrain.js "></script>' +
                '<div class="quads_ad_amp_outbrain" data-widget-id="'+adbyindex.outbrain_widget_ids+'"></div>';
            container.html(content);
            break;
        case "ad_image":
            if (window.matchMedia("(max-width: 640px)").matches && adbyindex.mobile_ad_image) 
            {
                image_src=adbyindex.mobile_ad_image;
            } else {
                image_src=adbyindex.ad_image;
            }
            content +='<div id="btn_close">x</div><a target="_blank" href="'+adbyindex.image_redirect_url+'"><img src="'+image_src+'"></a>';
            container.html(content);
            break;

    }
}

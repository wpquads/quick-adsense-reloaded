
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
    
 
    // showing video after respective time as per settings
    var data_videotype = $(".quads-video").attr('data-videotype');
    var data_videoposition = $(".quads-video").attr('data-position');
    var data_timer = $(".quads-video").attr('data-timer');
    if( data_videoposition == "v_right" ){
        $(".quads-video").css("bottom", "0px");
        $(".quads-video").css("float", "right");
        $(".quads-video").css("position", "sticky");
        $(".quads-video").css("top", "0%");
    }
    if( data_videotype && data_videotype == "specific_time_video" ){
        setTimeout(() => {
            $(".quads-video").css("display", "block");
        }, data_timer);
    }
    // showing video after respective scroll as per settings
    var data_percent = $(".quads-video").attr('data-percent');
    console.log(data_percent);
    if( data_videotype == "after_scroll_video"  ){
        window.addEventListener("scroll", () => {
            let scrollTop = window.scrollY;
            let docHeight = document.body.offsetHeight;
            let winHeight = window.innerHeight;
            let scrollPercent = scrollTop / (docHeight - winHeight);
            let scrollPercentRounded = Math.round(scrollPercent * 100);
            if( scrollPercentRounded>=data_percent  ) {
                $(".quads-video").css("display", "block");
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
        var lol = obj.quads_group_id;
        var videourl = obj.viedo_url;
        var videowidth = obj.viedo_width;
        var videoheight = obj.viedo_height;
        var ads_group_refresh_type = obj.quads_video_type;
        var ads_group_ref_interval_sec = obj.quads_group_ref_interval_sec;
        var ad_ids = obj.ads;
        var ad_ids_length = Object.keys(ad_ids).length;

        var i=0;
        var j = 0;
        if(ads_group_refresh_type ==='videoads'){
            j = 1;

            quadsShowAdsById(lol,videourl,videowidth, videoheight, ad_ids[i], j);
            i++;

            j++;
            var quads_ad_videoads = function () {
                if(i >= ad_ids_length){
                    i = 0;
                }
                var adbyindex ='';
                adbyindex = ad_ids[i];
                quadsShowAdsById(lol,videourl,videowidth, videoheight, adbyindex, j);
                i++;

                j++;
                setTimeout(quads_ad_videoads, ads_group_ref_interval_sec);
            };
            // quads_ad_videoads();
        }
    });
});

function quadsShowAdsById(lol, videourl, viedo_width, viedo_height, adbyindex, j){
    var container = jQuery(".quads_ad_containerrr[data-id='"+lol+"']");
    var container_pre = jQuery(".quads_ad_containerrr_pre[data-id='"+lol+"']");
    var content ='';
    switch(adbyindex.ad_type[0]){
        case "v":
            content +='<iframe width="'+viedo_width+'" height="'+viedo_height+'" src='+videourl+' frameborder="0" allow="accelerometer; autoplay="true"; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            container.html(content);
            break;

    }
}

window.addEventListener("load", function(){
    jQuery( document ).ready(function($) {
        var isFlagShow = true;
        // showing parallax after respective time as per settings
        var data_parallaxtype = $(".quads-parallax").attr('data-parallaxtype');
        // showing parallax after respective scroll as per settings
        var data_percent = $(".quads-parallax").attr('data-percent');
        if( data_parallaxtype == "after_scroll_parallax_ads"){
            $(".parallax_main").scroll(function(){
                $(".quads-parallax").focus();
                let popupWrapInnerHeight = $(this).innerHeight();
                let windHeight = $(window).innerHeight();
                if($(this).scrollTop() +
                $(this).innerHeight() >= 
                ($(this)[0].scrollHeight - 100)){
                    $(".parallax_main").scrollTop(windHeight - popupWrapInnerHeight);
                    $(".parallax_main").css("display", "none");
                    $(".quads-parallax").css("display", "none");
                    $('body').css({overflow:"auto"});
                    isFlagShow = false;
                }
            });
            let lastScrollTop;
            window.addEventListener("scroll", () => {
                var st = $(this).scrollTop();
                let scrollTop = window.scrollY;
                let docHeight = document.body.offsetHeight;
                let winHeight = window.innerHeight;
                let scrollPercent = scrollTop / (docHeight - winHeight);
                let scrollPercentRounded = Math.round(scrollPercent * 100); 
                if (st > lastScrollTop){
                    if(isFlagShow == true){                       
                        if( scrollPercentRounded>=data_percent  ) {
                                $(".parallax_main").css("display", "block");
                                $(".quads_parallax_scroll_text").css("display", "block");
                                $(".quads-parallax").css("display", "block");
                                $('body').css({overflow:"hidden"});
                        }
                    }
                } else {
                    if (scrollPercentRounded < data_percent) {
                        isFlagShow = true;
                    }
                }
                lastScrollTop = st;
              });
        }

        /**
         * we are here iterating on each group div to display all ads
         * randomly or ordered on interval or on reload
         */
        $(".quads-parallax-ads-json").each(function(){
            var ad_data_json = $(this).attr('data-json');
            var obj = JSON.parse(ad_data_json);
            var group__id = obj.quads_group_id;
            var parallax_image_src = obj.parallax_image_src;
            var parallax_btn_url = obj.parallax_btn_url;
            var parallax_ads_width = obj.parallax_ads_width;
            var parallaxheight = obj.parallax_height?obj.parallax_height:'auto';
            var ads_group_refresh_type = obj.quads_parallax_ads_type;
            var ads_group_ref_interval_sec = obj.quads_group_ref_interval_sec;
            var ad_ids = obj.ads;
            var ad_ids_length = Object.keys(ad_ids).length;
            var i=0;
            var j = 0;
            if(ads_group_refresh_type ==='parallax_ads'){
                j = 1;
                quadsShowAdsById(group__id,parallax_image_src, parallax_btn_url,  parallax_ads_width, parallaxheight, ad_ids[i], j);
                i++;
                j++;
                var quads_ad_parallaxads = function () {
                    if(i >= ad_ids_length){
                        i = 0;
                    }
                    var adbyindex ='';
                    adbyindex = ad_ids[i];
                    quadsShowAdsById(group__id,parallax_image_src, parallax_btn_url, parallax_ads_width, parallaxheight, adbyindex, j);
                    i++;
                    j++;
                    setTimeout(quads_ad_parallaxads, ads_group_ref_interval_sec);
                };
            }
        });
    });
    
    function quadsShowAdsById(group__id, parallax_image_src, parallax_btn_url, parallax_ads_width, parallaxheight, adbyindex, j){
        var container = jQuery(".quads_ad_container_parallax[data-id='"+group__id+"']");
        var data_redirect = jQuery(".quads-parallax").attr('data-redirect')?jQuery(".quads-parallax").attr('data-redirect'):false;
        var content ='';
        if(adbyindex.ad_type == "parallax_ads"){
            if(data_redirect){
                content +='<div class="parallax_popup"><div class="parallax_popup_img"><a target="_blank" href="'+parallax_btn_url+'"><img src="'+parallax_image_src+'" alt="Parallax Ad Pic"></a></div></div>';
            }
            container.html(content);
        }
        }
    });
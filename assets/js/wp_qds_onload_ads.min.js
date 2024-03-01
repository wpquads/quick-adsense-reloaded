window.addEventListener("load", function(){
    jQuery( document ).ready(function($) {
        setTimeout(()=>{
            var post_half_page_adWidth = $(".post_half_page_ad a img");
            var post_half_page_openClose = $("#post_half_page_openClose");
            var post_half_page_arrow_left = $(".half-page-arrow-left");
            var post_half_page_arrow_right = $(".half-page-arrow-right");
            var adWidth = post_half_page_adWidth.width();
            var post_half_page_ad = $(".post_half_page_ad");
            var post_body = $("body");
            var data_timer = $(".quads-half_page").attr('data-timer');
            var data_position = $(".quads-half_page").attr('data-position');
            var ad_position;
            if(data_position == 'half_page_ads_type_position_right'){
                ad_position = "right";
            } else {
                ad_position = "left";
            }
            var isSlide = true;
            if(screen.availWidth > 768){
                if(ad_position == "left"){
                    post_half_page_openClose.css("-webkit-transform", "rotate(270deg)");
                    post_half_page_openClose.css("-webkit-transform-origin", "right top");
                    post_half_page_openClose.css("right", "4px");
                    post_half_page_openClose.css("left", "unset");
                    post_half_page_ad.css("right", "unset");
                    post_half_page_ad.css("left", "-100vw");
                    post_half_page_arrow_left.css("right", "-27px");
                    post_half_page_arrow_left.css("left", "unset");
                    post_half_page_arrow_right.css("right", "-25px");
                    post_half_page_arrow_right.css("left", "unset");
                }
            
                post_half_page_ad.animate({[ad_position]: 0},700);
                post_half_page_openClose.css("display", "block");
                
                post_body.css("position", "relative");
                post_body.animate({[ad_position]: adWidth},700);
                if(data_timer){
                    setTimeout(()=>{
                        post_body.animate({[ad_position]: "0"},700);
                        post_half_page_ad.animate({[ad_position]: -adWidth},700);
                    }, data_timer);
                }
                
                $("#post_half_page_openClose").click(function() {  
                    if(isSlide){
                        post_body.animate({[ad_position]: adWidth},700);
                        post_half_page_ad.animate({[ad_position]:0},700);
                        isSlide = false;
                    } else {
                        post_body.animate({[ad_position]:0},700);
                        post_half_page_ad.animate({[ad_position]: -adWidth},700);
                        isSlide = true;
                    }
                });
            } 
            if(screen.availWidth < 769){
                post_half_page_ad.css("display", "block");
                post_half_page_ad.css("left", "0");
                post_half_page_ad.css("right", "0");
                post_half_page_ad.css("top", "unset");
                post_half_page_ad.animate({bottom: 0},700);
                post_half_page_openClose.css("display", "block");
                post_half_page_openClose.css("-webkit-transform", "unset");
                post_half_page_openClose.css("top", "unset");
                // post_half_page_openClose.css("bottom", "0");
                post_half_page_openClose.css("left", "0");
                post_half_page_openClose.css("right", "0");
                // post_half_page_openClose.css("position", "fixed");
                setTimeout(() => {
                    var adHeight = post_half_page_adWidth.height();
                    var adArrowRight = $('.half-page-arrow-right');
                    var adArrowLeft = $('.half-page-arrow-left');
                    $("#post_half_page_openClose").click(function() {  
                        if(isSlide){
                            post_half_page_ad.animate({bottom: "0"},700);
                            post_half_page_openClose.css("position", "absolute");
                            post_half_page_openClose.css("bottom", "unset");
                            adArrowRight.css("position", "absolute");
                            adArrowLeft.css("position", "absolute");
                            adArrowLeft.css("top", "13px");
                            adArrowLeft.css("bottom", "unset");
                            adArrowRight.css("top", "13px");
                            adArrowRight.css("bottom", "unset");
                            isSlide = false;
                        } else {
                            post_half_page_ad.animate({bottom: -adHeight},700);
                            post_half_page_openClose.css("bottom", "0");
                            post_half_page_openClose.css("position", "fixed");
                            post_half_page_openClose.css("bottom", "0");
                            adArrowRight.css("position", "fixed");
                            adArrowRight.css("top", "unset");
                            adArrowRight.css("bottom", "13px");
                            adArrowLeft.css("position", "fixed");
                            adArrowLeft.css("top", "unset");
                            adArrowLeft.css("bottom", "13px");
                            isSlide = true;
                        }
                    });
                }, 1000);
            }
        }, 3000);
        
        /**
         * we are here iterating on each group div to display all ads
         * randomly or ordered on interval or on reload
         */
         $(".quads-half-page-ads-json").each(function(){
            var ad_data_json = $(this).attr('data-json');
            var obj = JSON.parse(ad_data_json);
            var group__id = obj.quads_group_id;
            var half_page_ads_image_src = obj.half_page_ads_image_src;
            var half_page_ads_btn_url = obj.half_page_ads_btn_url;
            var half_page_adsheight = obj.half_page_ads_height?obj.half_page_ads_height:'auto';
            var ads_group_refresh_type = obj.half_page_ads_type;
            var ads_group_ref_interval_sec = obj.quads_group_ref_interval_sec;
            var ad_ids = obj.ads;
            var ad_ids_length = Object.keys(ad_ids).length;
            var i=0;
            var j = 0;
            if(ads_group_refresh_type ==='half_page_ads'){
                j = 1;
                quadsShowAdsById(group__id,half_page_ads_image_src, half_page_ads_btn_url, half_page_adsheight, ad_ids[i], j);
                i++;
                j++;
                var quads_ad_halfpage = function () {
                    if(i >= ad_ids_length){
                        i = 0;
                    }
                    var adbyindex ='';
                    adbyindex = ad_ids[i];
                    quadsShowAdsById(group__id,half_page_ads_image_src, half_page_ads_btn_url, half_page_adsheight, adbyindex, j);
                    i++;
                    j++;
                    setTimeout(quads_ad_halfpage, ads_group_ref_interval_sec);
                };
            }
        });
    });
    
    function quadsShowAdsById(group__id, half_page_ads_image_src, half_page_ads_btn_url, half_page_adsheight, adbyindex, j){
        var container = jQuery(".quads_ad_container_half_page[data-id='"+group__id+"']");
        var data_redirect = jQuery(".quads-half_page").attr('data-redirect')?jQuery(".quads-half_page").attr('data-redirect'):false;
        var content ='';
        if(adbyindex.ad_type == "half_page_ads"){
            if(data_redirect){
                content +='<a target="_blank" href="'+half_page_ads_btn_url+'"><img src="'+half_page_ads_image_src+'" alt="Half Page Slider Ad Pic"></a>';
            }
            container.html(content);
        }
    }
})

window.addEventListener("load", function(){
    jQuery( document ).ready(function($) {
        var isFlagShow = true;
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
        
        setTimeout(() => {data_parallaxtype
            $("#btn_close_parallax").click(function() {  
                $(".quads-parallax").css("display", "none");
                isFlagShow = false;
                set_quads_Cookie('quads-parallax','parallax_ad',1);
              });
        }, 500);
        
     
        // showing parallax after respective time as per settings
        var data_parallaxtype = $(".quads-parallax").attr('data-parallaxtype');
        var data_parallaxposition = $(".quads-parallax").attr('data-position');
        var data_timer = $(".quads-parallax").attr('data-timer');
        if( data_parallaxposition == "v_right" ){
            $(".quads-parallax").css("float", "right");
            $(".quads-parallax").css("right", "10px");
        }
        if( data_parallaxposition == "v_left" ){
            $(".quads-parallax").css("left", "0px");
        }
        if( data_parallaxtype && data_parallaxtype == "specific_time_parallax_ads" ){
            setTimeout(() => {
                $(".quads-parallax").css("display", "block");
            }, data_timer);
        }
        // showing parallax after respective scroll as per settings
        var data_percent = $(".quads-parallax").attr('data-percent');
        if( data_parallaxtype == "after_scroll_parallax_ads"  ){
            window.addEventListener("scroll", () => {
                if(isFlagShow == true){
                    let scrollTop = window.scrollY;
                    let docHeight = document.body.offsetHeight;
                    let winHeight = window.innerHeight;
                    let scrollPercent = scrollTop / (docHeight - winHeight);
                    let scrollPercentRounded = Math.round(scrollPercent * 100);
                    if( scrollPercentRounded>=data_percent  ) {
                        $(".quads-parallax").css("display", "block");
                    }
                }
              });
        }
         
    
        /**
         * we are here iterating on each group div to display all ads
         * randomly or ordered on interval or on reload
         */
        $(".quads-parallax-ads-json").each(function(){
            var ad_data_json = $(this).attr('data-json');
            var obj = JSON.parse(ad_data_json);
            console.log(obj);
            var group__id = obj.quads_group_id;
            var parallax_ad_title = obj.parallax_ad_title;
            var parallax_image_src = obj.parallax_image_src;
            var parallax_btn_url = obj.parallax_btn_url;
            var parallax_ad_desc = obj.parallax_ad_desc;
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
    
                quadsShowAdsById(group__id,parallax_ad_title,parallax_image_src, parallax_btn_url, parallax_ad_desc, parallax_ads_width, parallaxheight, ad_ids[i], j);
                i++;
    
                j++;
                var quads_ad_parallaxads = function () {
                    if(i >= ad_ids_length){
                        i = 0;
                    }
                    var adbyindex ='';
                    adbyindex = ad_ids[i];
                    quadsShowAdsById(group__id,parallax_ad_title,parallax_image_src, parallax_btn_url, parallax_ad_desc, parallax_ads_width, parallaxheight, adbyindex, j);
                    i++;
    
                    j++;
                    setTimeout(quads_ad_parallaxads, ads_group_ref_interval_sec);
                };
            }
        });
    });
    
    function quadsShowAdsById(group__id, parallax_ad_title,parallax_image_src, parallax_btn_url, parallax_ad_desc, parallax_ads_width, parallaxheight, adbyindex, j){
        var container = jQuery(".quads_ad_container_parallax[data-id='"+group__id+"']");
        var data_redirect = jQuery(".quads-parallax").attr('data-redirect')?jQuery(".quads-parallax").attr('data-redirect'):false;
        var content ='';
        if(adbyindex.ad_type == "parallax_ads"){
            if(data_redirect)
            {
                content +='<div class="parallax_popup"><div id="btn_close_parallax">x</div><h5 class="parallax_popup_title" id="parallax_popup_title">'+parallax_ad_title+'</h5><div class="parallax_popup_img"><img src="'+parallax_image_src+'" alt="Parallax Ad Pic"></div><div class="parallax_popup_desc"><blockquote>'+parallax_ad_desc+'</blockquote></div><div class="parallax_popup_button"><a target="_blank" href="'+parallax_btn_url+'">Learn More</a></div></div>';
            }
            
            container.html(content);
        }
        }
    });
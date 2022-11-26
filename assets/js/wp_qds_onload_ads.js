
window.addEventListener("load", function(){
    jQuery( document ).ready(function($) {
        console.log("On Load I am loading");

        setTimeout(()=>{
            var post_onLoad_adWidth = $(".post_onLoad_ad a img");
            var post_onLoad_openClose = $("#post_onLoad_openClose");
            var adWidth = post_onLoad_adWidth.width();
            var post_onLoad_ad = $(".post_onLoad_ad");
            post_onLoad_ad.css("visibility", "visible");
            post_onLoad_openClose.css("display", "block");
            var post_body = $("body");
            post_body.css("position", "relative");
            post_body.css("transition", "all 0.5s");
            post_body.css("right", adWidth);
            var isSlide = false;
            setTimeout(()=>{
                post_body.css("right", "0");
                post_onLoad_ad.css({right: -adWidth});
            }, 15000);
            $("#post_onLoad_openClose").click(function() {  
                if(!isSlide){
                    post_body.css("right", "0");
                    post_onLoad_ad.css("right", -adWidth);
                    isSlide = true;
                } else {
                    post_body.css("right", adWidth);
                    post_onLoad_ad.css("right", "0");
                    isSlide = false;
                }
            });
        }, 5000);
        
    })
})
var wpquads_adblocker_check = true;

var wpquads_adblocker_check_2 = true;

window.addEventListener("load", function(){
        var wpquads_sticky = document.querySelector(".quads-sticky .quads-location");
        var wpquads_location_ad = document.querySelectorAll(".quads-location");
        if(wpquads_sticky){
            wpquads_sticky.style.backgroundColor = 'hsla(0,0%,100%,.7)';
        }
        Array.from(wpquads_location_ad).forEach(elm=>{
            var img_elements = elm.querySelectorAll("img");
            var wpquads_lazy_delay = 3000;
            if(img_elements.length){
                img_elements.forEach(img=>{
                    
                    if(img.dataset && img.dataset.lazydelay > 0){
                     wpquads_lazy_delay = img.dataset.lazydelay;
                    }
                    setTimeout(()=>{
                     if(img.dataset && img.dataset.src){
                         img.src = img.dataset.src;
                     }
                     elm.style.visibility = "visible";
                 }, wpquads_lazy_delay);
                 }); 
            }else{
                if(elm.dataset.lazydelay && elm.dataset.lazydelay > 0){
                    wpquads_lazy_delay=elm.dataset.lazydelay;
                }
                setTimeout(()=>{
                    elm.style.visibility = "visible";
                }, wpquads_lazy_delay);
            }
        })
});
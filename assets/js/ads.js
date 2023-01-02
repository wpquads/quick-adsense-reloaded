var wpquads_adblocker_check = true;

var wpquads_adblocker_check_2 = true;

window.addEventListener("load", function(){
    setTimeout(()=>{
        var wpquads_sticky = document.querySelector(".quads-sticky .quads-location");
        var wpquads_location_ad = document.querySelectorAll(".quads-location");
        if(wpquads_sticky){
            wpquads_sticky.style.backgroundColor = 'hsla(0,0%,100%,.7)';
        }
        Array.from(wpquads_location_ad).forEach(elm=>{
            elm.style.visibility = "visible";
        })
    }, 2000);
});

var wpquads_adblocker_check = true;

var wpquads_adblocker_check_2 = true;

window.addEventListener("load", function(){
    var wpquads_sticky = document.querySelector(".quads-sticky .quads-location");
    wpquads_sticky.style.backgroundColor = 'hsla(0,0%,100%,.7)';
    var wpquads_location_ad = document.querySelectorAll(".quads-location");
    setTimeout(()=>{
        Array.from(wpquads_location_ad).forEach(elm=>{
            elm.style.display = "block";
        })
    }, 2000);
});

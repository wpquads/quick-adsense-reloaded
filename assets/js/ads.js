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

jQuery(document).ready(function($){
var animTime = $('.quads-sticky').attr("data-anim-tod");
setTimeout(() =>
{
  let animate = document.querySelectorAll('.quads-sticky');
  if(typeof animate != undefined){
    animate.forEach(item => item.classList.add('active'));
  }
}
,animTime);

var stickyHide = document.querySelector(".quads-sticky-ad-close");
stickyHide.addEventListener( 'click', function() {  
    let close = document.querySelectorAll('.quads-sticky');
    if(typeof close != undefined){
        close.forEach(item => item.classList.remove('active'));
    }
    let shbtn = document.querySelectorAll('.quads-sticky-show-btn');
    if(typeof shbtn != undefined){
        shbtn.forEach(item => item.classList.add('active'));
    }
});
var stickyShow = document.querySelector(".quads-sticky-show-btn");
stickyShow.addEventListener( 'click', function() {  
    let close = document.querySelectorAll('.quads-sticky');
    if(typeof close != undefined){
        close.forEach(item => item.classList.add('active'));
    }
    let shbtn = document.querySelectorAll('.quads-sticky-show-btn');
    if(typeof shbtn != undefined){
        shbtn.forEach(item => item.classList.remove('active'));
    }
});
});
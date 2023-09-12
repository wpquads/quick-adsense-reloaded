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
if(stickyHide !== null){
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
}
var stickyShow = document.querySelector(".quads-sticky-show-btn");
if(stickyShow !== null){
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
}
});
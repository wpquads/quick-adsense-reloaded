window.addEventListener("load", function(){
    const wpquads_3d_slides = document.querySelectorAll(".wpquads-3d-item"),
        wpquads_3d_slider = document.getElementById("wpquads-3d-cube");
    var wpquads_3d_container = document.querySelector(".wpquads-3d-container");
    var wpquadsAdId = wpquads_3d_container.getAttribute('id');
    var floatingSizeValue = document.getElementById("floatingSizeValue").value;

    wpquads_3d_slides[0].setAttribute('style', 'transform:rotateY(0) translateZ('+ (floatingSizeValue / 2) +'px); -webkit-transform:rotateY(0) translateZ('+ (floatingSizeValue / 2) +'px); -ms-transform:rotateY(0) translateZ('+ (floatingSizeValue / 2) +'px); -o-transform:rotateY(0) translateZ('+ (floatingSizeValue / 2) +'px);');

    wpquads_3d_slides[1].setAttribute('style', 'transform:rotateX(180deg) translateZ('+ (floatingSizeValue / 2) +'px); -webkit-transform:rotateX(180deg) translateZ('+ (floatingSizeValue / 2) +'px); -ms-transform:rotateX(180deg) translateZ('+ (floatingSizeValue / 2) +'px); -o-transform:rotateX(180deg) translateZ('+ (floatingSizeValue / 2) +'px);');

    wpquads_3d_slides[2].setAttribute('style', 'transform:rotateY(90deg) translateZ('+ (floatingSizeValue / 2) +'px); -webkit-transform:rotateY(90deg) translateZ('+ (floatingSizeValue / 2) +'px); -ms-transform:rotateY(90deg) translateZ('+ (floatingSizeValue / 2) +'px); -o-transform:rotateY(90deg) translateZ('+ (floatingSizeValue / 2) +'px);');

    wpquads_3d_slides[3].setAttribute('style', 'transform:rotateY(-90deg) translateZ('+ (floatingSizeValue / 2) +'px); -webkit-transform:rotateY(-90deg) translateZ('+ (floatingSizeValue / 2) +'px); -ms-transform:rotateY(-90deg) translateZ('+ (floatingSizeValue / 2) +'px); -o-transform:rotateY(-90deg) translateZ('+ (floatingSizeValue / 2) +'px);');

    wpquads_3d_slides[4].setAttribute('style', 'transform:rotateX(90deg) translateZ('+ (floatingSizeValue / 2) +'px); -webkit-transform:rotateX(90deg) translateZ('+ (floatingSizeValue / 2) +'px); -ms-transform:rotateX(90deg) translateZ('+ (floatingSizeValue / 2) +'px); -o-transform:rotateX(90deg) translateZ('+ (floatingSizeValue / 2) +'px);');

    wpquads_3d_slides[5].setAttribute('style', 'transform:rotateX(-90deg) translateZ('+ (floatingSizeValue / 2) +'px); -webkit-transform:rotateX(-90deg) translateZ('+ (floatingSizeValue / 2) +'px); -ms-transform:rotateX(-90deg) translateZ('+ (floatingSizeValue / 2) +'px); -o-transform:rotateX(-90deg) translateZ('+ (floatingSizeValue / 2) +'px);');


    let activeSlide=0;
    function changeSlide(){
        wpquads_3d_slider.classList.remove("wpquads-slide"+activeSlide+"-active"),++activeSlide>=wpquads_3d_slides.length&&(activeSlide=0),
        wpquads_3d_slider.classList.add("wpquads-slide"+activeSlide+"-active");

        var wpquads_slide_active = document.querySelector(".wpquads-3d-cube");

        console.log(wpquads_slide_active);
        console.log(activeSlide);
        switch (activeSlide) {
            case 0:
                wpquads_slide_active.removeAttribute('style');
                wpquads_slide_active.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);transform(-'+ (floatingSizeValue / 2) +'px) rotateY(0);');
                break;

            case 1:
                wpquads_slide_active.removeAttribute('style');
                wpquads_slide_active.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);');
                break;

            case 2:
                wpquads_slide_active.removeAttribute('style');
                wpquads_slide_active.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);');
                break;

            case 3:
                wpquads_slide_active.removeAttribute('style');
                wpquads_slide_active.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);');
                break;

            case 4:
                wpquads_slide_active.removeAttribute('style');
                wpquads_slide_active.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);');
                break;

            case 5:
                wpquads_slide_active.removeAttribute('style');
                wpquads_slide_active.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);');
                break;

            default:
                wpquads_slide_active.removeAttribute('style');
                wpquads_slide_active.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px);transform:translateZ(-'+ (floatingSizeValue / 2) +'px);');
                break;
        }
    }
    setInterval(changeSlide,5e3);
    const close_element = document.getElementById("wpquads-close-btn");
    close_element.addEventListener("click", function() {
        document.getElementById(wpquadsAdId).style.display = "none";
    });

});


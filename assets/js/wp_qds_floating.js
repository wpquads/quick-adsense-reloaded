window.addEventListener("load", function(){
    const wpquads_3d_cubes = document.querySelectorAll(".wpquads-3d-container");
    wpquads_3d_cubes.forEach(s_cube => {
        const wpquads_3d_slider = s_cube.querySelector(".wpquads-3d-cube");
        const wpquads_3d_slides = s_cube.querySelectorAll(".wpquads-3d-item");
        var wpquadsAdId = s_cube.getAttribute('id');
        var floatingSize = s_cube.querySelector(".quadsFloatingSizeValue") ;
        var floatingSizeValue = floatingSize.value;
        let activeSlide=0;
    
        function changeSlide(){
            wpquads_3d_slider.classList.remove("wpquads-slide"+activeSlide+"-active");
            activeSlide=activeSlide+1;
            if(activeSlide>=wpquads_3d_slides.length){
                activeSlide=0;
            }
            wpquads_3d_slider.classList.add("wpquads-slide"+activeSlide+"-active");
            switch (activeSlide) {
                case 0:
                    wpquads_3d_slider.removeAttribute('style');
                    wpquads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);transform(-'+ (floatingSizeValue / 2) +'px) rotateY(0);');
                    break;
    
                case 1:
                    wpquads_3d_slider.removeAttribute('style');
                    wpquads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);');
                    break;
    
                case 2:
                    wpquads_3d_slider.removeAttribute('style');
                    wpquads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);');
                    break;
    
                case 3:
                    wpquads_3d_slider.removeAttribute('style');
                    wpquads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);');
                    break;
    
                case 4:
                    wpquads_3d_slider.removeAttribute('style');
                    wpquads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);');
                    break;
    
                case 5:
                    wpquads_3d_slider.removeAttribute('style');
                    wpquads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);');
                    break;
    
                default:
                    wpquads_3d_slider.removeAttribute('style');
                    wpquads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);transform(-'+ (floatingSizeValue / 2) +'px) rotateY(0);');
                    break;
            }
        }
        setInterval(changeSlide,5e3);
        const close_element = s_cube.querySelector(".wpquads-close-btn");
        close_element.addEventListener("click", function() {
            document.getElementById(wpquadsAdId).style.display = "none";
        });
    
    });
});

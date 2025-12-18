window.addEventListener("load", function(){
    const quads_3d_cubes = document.querySelectorAll(".quads-3d-container");
    quads_3d_cubes.forEach(s_cube => {
        const quads_3d_slider = s_cube.querySelector(".quads-3d-cube");
        const quads_3d_slides = s_cube.querySelectorAll(".quads-3d-item");
        var quadsAdId = s_cube.getAttribute('id');
        var floatingSize = s_cube.querySelector(".quadsFloatingSizeValue") ;
        var floatingSizeValue = floatingSize.value;
        let activeSlide=0;
    
        function changeSlide(){
            quads_3d_slider.classList.remove("quads-slide"+activeSlide+"-active");
            activeSlide=activeSlide+1;
            if(activeSlide>=quads_3d_slides.length){
                activeSlide=0;
            }
            quads_3d_slider.classList.add("quads-slide"+activeSlide+"-active");
            switch (activeSlide) {
                case 0:
                    quads_3d_slider.removeAttribute('style');
                    quads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);transform(-'+ (floatingSizeValue / 2) +'px) rotateY(0);');
                    break;
    
                case 1:
                    quads_3d_slider.removeAttribute('style');
                    quads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-180deg);');
                    break;
    
                case 2:
                    quads_3d_slider.removeAttribute('style');
                    quads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(-90deg);');
                    break;
    
                case 3:
                    quads_3d_slider.removeAttribute('style');
                    quads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(90deg);');
                    break;
    
                case 4:
                    quads_3d_slider.removeAttribute('style');
                    quads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(-90deg);');
                    break;
    
                case 5:
                    quads_3d_slider.removeAttribute('style');
                    quads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateX(90deg);');
                    break;
    
                default:
                    quads_3d_slider.removeAttribute('style');
                    quads_3d_slider.setAttribute('style', '-webkit-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-moz-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-ms-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);-o-transform:translateZ(-'+ (floatingSizeValue / 2) +'px) rotateY(0);transform(-'+ (floatingSizeValue / 2) +'px) rotateY(0);');
                    break;
            }
        }
        setInterval(changeSlide,5e3);
        const close_element = s_cube.querySelector(".quads-close-btn");
        close_element.addEventListener("click", function() {
            document.getElementById(quadsAdId).style.display = "none";
        });
    
    });
});

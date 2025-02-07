window.addEventListener("load", function(){
    const wpquads_carousel = document.querySelectorAll(".quads-carousel-container");
    let quads_carousel_intervals=[];
    let current_carousel_id=0;
    let quads_move = 'next';
    wpquads_carousel.forEach(carousel => {
        const wpquads_slider_speed = carousel.getAttribute("data-speed")?carousel.getAttribute("data-speed"):3000;
        const wpquads_adid = carousel.getAttribute("data-adid")?carousel.getAttribute("data-adid"):0;
        current_carousel_id=wpquads_adid;
        quads_move = 'next';
        quads_carousel_intervals[wpquads_adid]= quadsCarouselInterval(current_carousel_id,quads_move,wpquads_slider_speed);   
    });

    function quadsCarouselInterval(id,move,delay){
        setInterval(() => {
            quadsCarousel(id,move);
          }, delay);
    }

    function quadsCarousel(current_carousel_id,quads_move){
        if(!current_carousel_id){
            return;
        }
        let car_con = document.getElementById('carousel-container-'+current_carousel_id);
        if(!car_con){
            return;
        }
        var cur_slide = car_con.getAttribute('data-slide')?car_con.getAttribute('data-slide'):1;
        var x = document.getElementsByClassName("quads-slides-"+current_carousel_id);
        for (i = 0; i < x.length; i++) {x[i].style.display = "none";}
        if(quads_move=='back'){
            cur_slide--;
            if (cur_slide < 1) {cur_slide = x.length;}
        }
        else{
            cur_slide++;
            if (cur_slide > x.length) {cur_slide = 1;}
        }
        if(x[cur_slide-1]) {  x[cur_slide-1].style.display = "block"; }
        car_con.setAttribute('data-slide',cur_slide);

    }

    const wpquads_carousel_back_btns = document.querySelectorAll(".quads_carousel_back");
    wpquads_carousel_back_btns.forEach(element => {
        element.addEventListener('click',function(){
            var temp_id = element.parentNode.getAttribute('data-adid')?element.parentNode.getAttribute('data-adid'):0;
            clearInterval(quads_carousel_intervals[temp_id]);
            if(temp_id){
                
                let car_slides = document.querySelectorAll("#carousel-container-"+temp_id+" .quads-slides");
                
                car_slides.forEach(element => {
                    console.log(element.className);
                    element.className=element.className.replace('quads-animate-right','quads-animate-left')
                });
                quads_move ='back';
                quadsCarousel(temp_id,quads_move);
            }
        });
    });
    const wpquads_carousel_next_btns = document.querySelectorAll(".quads_carousel_next");
    wpquads_carousel_next_btns.forEach(element => {
        element.addEventListener('click',function(){
            var temp_id = element.parentNode.getAttribute('data-adid')?element.parentNode.getAttribute('data-adid'):0;
            clearInterval(quads_carousel_intervals[temp_id]);
            if(temp_id){
                let car_slides = document.querySelectorAll("#carousel-container-"+temp_id+" .quads-slides");
                
                car_slides.forEach(element => {
                    element.className=element.className.replace('quads-animate-left','quads-animate-right')
                });
                quads_move ='next';
                quadsCarousel(temp_id,quads_move);
            }
        });
    });
    
    const wpquads_carousel_close_btns = document.querySelectorAll(".quads_carousel_close");
    wpquads_carousel_close_btns.forEach(element => {
        element.addEventListener('click',function(){
            var temp_id = element.parentNode.getAttribute('data-adid')?element.parentNode.getAttribute('data-adid'):0;
            clearInterval(quads_carousel_intervals[temp_id]);
            if(temp_id){
                let car_con = document.getElementById('carousel-container-'+temp_id);
                if(car_con){
                    car_con.remove();
                }
            }
        });
    });
});

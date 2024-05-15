 ;
(function($) {
 function quads_ad_tracker(){
        
        setTimeout(function(){   
            
        var ad_ids ={}; 
        let adIndex = 0;   
        $(".quads-location").each(function(index){
            if($(this).attr('id') != 'quads-rotate'){
               ad_ids[adIndex]= ($(this).attr('id'));
               adIndex++;
            }
        });  
        
        if($('.quads-rotatorad').length > 0){
            $(".quads-rotatorad").each(function(qrindex){
                let childAds = $(this).children('.quads-groups-ads-json');
                if(childAds.length > 0){
                    let rotatorChildAds = childAds.attr('data-json');
                    if(rotatorChildAds.length > 0){
                        let parseRotator = JSON.parse(rotatorChildAds);
                        if(!parseRotator.quads_repeat_impressions){
                            if(parseRotator.ads){
                                $.each(parseRotator.ads, function(rindex, relement){
                                    if(relement.ad_id){
                                        let addRotateId = 'quads-ad'+relement.ad_id;
                                        ad_ids[adIndex] = addRotateId;
                                        adIndex++;
                                    }
                                });
                            }
                        }
                    }
                }
            });
        }  
        
        if($.isEmptyObject( ad_ids ) == false){      
         var currentLocation = window.location.href;         
        $.ajax({
                    type: "POST",    
                    url:quads_analytics.ajax_url,                    
                    dataType: "json",
                    data:{action:"quads_insert_ad_impression", ad_ids:ad_ids, quads_front_nonce:quads_analytics.quads_front_nonce,currentLocation:currentLocation},                    
                    error: function(response){                    
                    }
                });     
        } 
             $(document).on('click', '.quads-location, .quads-child-ads', function(e){
                        var ad_id = $(this).attr('id');
                        var currentLocation = window.location.href;                        
                  if(ad_id){
                     $.post(quads_analytics.ajax_url, 
                           { action:"quads_insert_ad_clicks", ad_id:ad_id, quads_front_nonce:quads_analytics.quads_front_nonce,currentLocation:currentLocation},
                           function(response){
                           console.log(response);                
               });  
             }         
        });                  
        }, 1000);
        
   
                
        //Detecting click event on iframe based ads
         window.addEventListener('blur',function(){   
      if (document.activeElement instanceof HTMLIFrameElement) {
                var data = $(this);                   
                var el = data.context.activeElement;
                 while (el.parentElement) {
                     el = el.parentElement;     
                       if(el.attributes[0].name =='data-ad-id'){
                       var ad_id = el.attributes[0].value;
                       if(ad_id){
                          $.post(quads_analytics.ajax_url, 
                             { action:"quads_insert_ad_clicks", ad_id:ad_id},
                                function(response){
                                console.log(response);                
                              });  
                          }
                       }
                   }
         }
    });
        
      }  
      quads_ad_tracker();
})(window.jQuery);

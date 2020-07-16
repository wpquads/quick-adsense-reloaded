var wpquads_adblocker_check = true;
jQuery('document').ready(function(){
//Adblocker Notice Script Starts Here
var curr_url = window.location.href;
var red_ulr = localStorage.getItem('curr');
var modal = document.getElementById("quads-myModal");
var quadsAllowedCookie =  quadsgetCookie('quadsAllowedCookie');
if(typeof quadsOptions !== 'undefined'){
  if(quadsAllowedCookie!=quadsOptions.allow_cookies){
    quadssetCookie('quadsCookie', '', -1, '/');
    quadssetCookie('quadsAllowedCookie', quadsOptions.allow_cookies, 1, '/');
  }

  if(quadsOptions.allow_cookies == 2){
    if( quadsOptions.quadsChoice == 'bar' || quadsOptions.quadsChoice == 'popup'){
        modal.style.display = "block";
        quadssetCookie('quadsCookie', '', -1, '/');
    }
    
    if(quadsOptions.quadsChoice == 'page_redirect' && quadsOptions.page_redirect !="undefined"){
        if(red_ulr==null || curr_url!=quadsOptions.page_redirect){
        window.location = quadsOptions.page_redirect;
        localStorage.setItem('curr',quadsOptions.page_redirect);
      }
    }
  }else{
    var adsCookie = quadsgetCookie('quadsCookie');
    if(adsCookie==false) {
      if( quadsOptions.quadsChoice == 'bar' || quadsOptions.quadsChoice == 'popup'){
          modal.style.display = "block";
      }
      if(quadsOptions.quadsChoice == 'page_redirect' && quadsOptions.page_redirect !="undefined"){
        window.location = quadsOptions.page_redirect;
        quadssetCookie('quadsCookie', true, 1, '/');
      }
    }else{
      modal.style.display = "none";
    }
  }
}

function quadsgetCookie(cname){
    var name = cname + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return false;
}
function quadssetCookie(cname, cvalue, exdays, path){
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

var span = document.getElementsByClassName("quads-cls-notice")[0];
if(span){
  span.onclick = function() {
    modal.style.display = "none";
    document.cookie = "quads_prompt_close="+new Date();
    quadssetCookie('quadsCookie', 'true', 1, '/');
  }
}

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
    document.cookie = "quads_prompt_close="+new Date();
    quadssetCookie('quadsCookie', 'true', 1, '/');
  }
}
});
//Adblocker Notice Script Ends Here
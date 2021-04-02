;
(function($) {
    var quads_ad_click = 'quads_ad_clicks';
    var quads_click_fraud_protection = function() {
        this.$elements = {};
        this.currentIFrame = false;
        this.focusLost = false;
        this.init();
    }

    quads_click_fraud_protection.prototype = {
        constructor: quads_click_fraud_protection,
        init: function() {
            var that = this;
            $(document).on('click', '.quads-location', function() {
                var currentid = $(this).attr('id');
                currentid = currentid.replace(/[^0-9]/g, '');
                that.onClick(parseInt(currentid));
            });
            $(window).on('blur', function() {
                if (false !== that.currentIFrame) {
                    that.onClick(that.currentIFrame);
                    that.currentIFrame = false;
                    that.focusLost = true;
                }
            });
        },
        onClick: function(ID) {
            var cookie_val = {};
            var C = false,
                C_vc = false;
            if ($('#quads-ad' + ID)) {
                var cookie = quadsgetCookie(quads_ad_click);
                if (cookie) {
                    try {
                        C_vc = JSON.parse( cookie );
                    } catch( Ex ) {
                        C_vc= false;
                    }
                }
            }
            var d = new Date();
            var now = new Date();
            var expires =  d.toUTCString();
            cookie_val['exp'] = expires;

            if (C_vc) {
                var old_date = new Date(C_vc['exp']);
                var click_limit_time = old_date.setHours(old_date.getHours() +quads.quads_click_limit );
                if(click_limit_time < now ){
                    cookie_val['count'] = 0;
                }else{
                    cookie_val['count'] = C_vc['count']+1;
                }
                cookie_val['exp'] = expires;
                quadssetCookie(quads_ad_click, JSON.stringify( cookie_val, 'false', false ), quads.quads_ban_duration);
            } else {
                cookie_val['count'] = 0;
                quadssetCookie(quads_ad_click, JSON.stringify( cookie_val, 'false', false ), quads.quads_ban_duration);
            }
        }
    }
    $(document).on('mouseenter', '.quads-location', function() {
        var ID = $(this).attr('id');
        var currentid = ID.replace(/[^0-9]/g, '');
        quads_click_fraud.currentIFrame = currentid;
    }).on('mouseleave', '.quads-location', function() {
        quads_click_fraud.currentIFrame = false;
        if (quads_click_fraud.focusLost) {
            quads_click_fraud.focusLost = false;
            $(window).focus();
        }
    });
    $(function() {

        window.quads_click_fraud = new quads_click_fraud_protection();

    });

})(window.jQuery);


if (typeof quadsgetCookie !== "function"){

    function quadsgetCookie(cname) {
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
}
if (typeof quadssetCookie !== "function") {

    function quadssetCookie(cname, cvalue, exdays, path) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
}
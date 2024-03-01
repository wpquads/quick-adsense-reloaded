var strict;
function quads_switch_version(toversion,selector){
jQuery(selector).attr('onClick', "");
    var data = {
        action: 'quads_change_mode',
        mode: toversion,
        nonce: quads.nonce,
    };        
    jQuery.post(ajaxurl, data, function (resp, status, xhr) {            
        window.location.href = quads.path + '/wp-admin/admin.php?page=quads-settings';  
    }).fail(function (xhr) { // Will be executed when $.post() fails
        quads_show_message('Ajax Error: ' + xhr.status + ' ' + xhr.statusText);            
    });
}

jQuery(document).ready(function ($) {

$('a[href$="quads_switch_to_new"]').removeAttr("href").attr('onClick', "quads_switch_version('new',this);");
$('a[href$="quads_switch_to_old"]').removeAttr("href").attr('onClick', "quads_switch_version('old',this);");
if($('a[href$="admin.php?page=quads-addons"]')){
    $('a[href$="admin.php?page=quads-addons"]').css({"color": "#eb3349", "font-size": "bold"});
}

    $(".wpquads-send-query").on("click", function(e){
        e.preventDefault();   
        var message     = $("#wpquads_query_message").val();  
        var email       = $("#wpquads_query_email").val();  
        var premium_cus = $("#wpquads_query_premium_cus").val(); 
        var wpnonce = quads.nonce;
        if($.trim(message) !='' && premium_cus && $.trim(email) !='' && wpquadsIsEmail(email) == true){
            $.ajax({
                type: "POST",    
                url:ajaxurl,                    
                dataType: "json",
                data:{action:"wpquads_send_query_message", premium_cus:premium_cus,message:message,email:email, wpquads_security_nonce:wpnonce},
                success:function(response){  
                    $(".wpquads_support_div ul").hide();                       
                    if(response['status'] =='t'){
                        $(".wpquads-query-success").show();
                        $(".wpquads-query-error").hide();
                    }else{                                  
                        $(".wpquads-query-success").hide();  
                        $(".wpquads-query-error").show();
                    }
                },
                error: function(response){                    
                    console.log(response);
                }
            });   
        }else{
            if($.trim(message) =='' && premium_cus =='' && $.trim(email) ==''){
                alert('Please enter the message, email and select customer type');
            }else{
                if(premium_cus ==''){
                    alert('Select Customer type');
                }
                if($.trim(message) == ''){
                    alert('Please enter the message');
                }
                if($.trim(email) == ''){
                    alert('Please enter the email');
                }
                if(wpquadsIsEmail(email) == false){
                    alert('Please enter a valid email');
                }
            }     
        }                        
    });
    function wpquadsIsEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
    // show / hide helper description
    $('.quads-tooltip').click(function (e) {
        var icon = $(this),
                bubble = $(this).next();
        if(bubble.html() === undefined){
            return ;
        }else{
            e.preventDefault();
        }

        // Close any that are already open
        $('.quads-tooltip-message').not(bubble).hide();

        var position = icon.position();
        if (bubble.hasClass('bottom')) {
            bubble.css({
                'left': (position.left - bubble.width() / 2) + 'px',
                'top': (position.top + icon.height() + 9) + 'px'
            });
        } else {
            bubble.css({
                'left': (position.left + icon.width() + 9) + 'px',
                'top': (position.top + icon.height() / 2 - 18) + 'px'
            });
        }

        bubble.toggle();
        e.stopPropagation();
    });

    $('body').click(function () {
        $('.quads-tooltip-message').hide();
    });

    $('.quads-tooltip-message').click(function (e) {
        e.stopPropagation();
    });
                        

    
    // Remove several unused elements from vi page
    if (document.location.href.indexOf('vi_header') > - 1) {
            $('#quads-submit-button').hide();
            $('#quads-validate').hide();
            $('#quads-footer').hide();
    } else {
            $('#quads-submit-button').show();
            $('#quads-validate').show();
            $('#quads-footer').show();
    }
    $(window).bind('easytabs:after', function(){
            if (document.location.href.indexOf('vi_header') > - 1) {
            $('#quads-submit-button').hide();
            $('#quads-validate').hide();
            $('#quads-footer').hide();
            } else {

            $('#quads-submit-button').show();
            $('#quads-validate').show();
            $('#quads-footer').show();
            }
    });

     
    /**
     * General Tab
     */
    // Inactive select fields are greyed out
    $('.quads-assign').each(function(e){
        if (!$(this).prop('checked')){
            $(this).next('select').css('background-color', 'whitesmoke').css('color', '#939393');
        }else {
            $(this).next('select').css('background-color', 'white').css('color', 'black');
        }
    });
    
    $('.quads-assign').click(function(){
        if (!$(this).prop('checked')){
            $(this).next('select').css('background-color', 'whitesmoke').css('color', '#939393');
        } else {
            $(this).next('select').css('background-color', 'white').css('color', 'black');
        }
    });      
    
    /**
     * AdSense Code Tab
     */
    // Check if submit button is visible than stick it to the bottom of the page
    $(window).scroll(function() {
        if(!$('#quads_settings').length){
            return true;
        }
        var elem = '#quads_tab_container .submit';
        var $myElement = $('#quads_settings'),
        canUserSeeIt = inViewport($myElement);

        if ($(elem).length < 1){
            return;
        }
        
        var top_of_element = $(elem).offset().top;
        var bottom_of_element = $(elem).offset().top + $(elem).outerHeight(false);
        var bottom_of_screen = $(window).scrollTop() + $(window).height();
        if (!canUserSeeIt){
            // The element is visible, do something
            $('#quads-submit-button').css('position', 'relative').css('bottom', '20px');
        } else {
            // The element is NOT visible, do something else
            $('#quads-submit-button').css('position', 'fixed').css('bottom', '20px');
            }
    });
    function inViewport($ele) {
    var lBound = $(window).scrollTop(),
        uBound = lBound + $(window).height(),
        top = $ele.offset().top,
        bottom = top + $ele.outerHeight(true);

    return (top > lBound && top < uBound)
        || (bottom > lBound && bottom < uBound)
        || (lBound >= top && lBound <= bottom)
        || (uBound >= top && uBound <= bottom);
}
    
    // Activate chosen select boxes
//    $(".quads-chosen-select").chosen({
//        inherit_select_classes: true
//    });
    

    
    // Hid or show AMP code form on click on amp checkbox
    $('.quads-activate-amp').click(function(){
    var parent = $(this).parents('.quads-ad-toggle-container').attr('id');
        if ($(this).attr('checked') === 'checked') {
            $('#' + parent).find('.quads-amp-code').show();
        } else {
            $('#' + parent).find('.quads-amp-code').hide();
        }
    });
    
    
    // Hide or show AMP code form on loading
    $('.quads-ad-toggle-container').find('.quads-activate-amp').each(function (index, value) {
        var parentContainerID = $(this).parents('.quads-ad-toggle-container').attr('id');
        if ($(this).attr( 'checked') === 'checked' ) {
            $('#' + parentContainerID).find('.quads-amp-code').show();
        }else {
            $('#' + parentContainerID).find('.quads-amp-code').hide();
        }
    });
    

    
    /**
     * Toggle Button | Open All Ads
     */
    $('#quads-open-toggle').click(function(){        
            if ($('#quads-open-toggle').text() === 'Open All Ads' ){
                $('.quads-ad-toggle-container').show();
                $('#quads-open-toggle').html('Close Ads');
            }else{
                $('.quads-ad-toggle-container').hide();
                $('#quads-open-toggle').html('Open All Ads');
            }
    });
    
    // show / hide helper description
    $('.quads-helper').click(function (e) {
        e.preventDefault();
        var icon = $(this),
                bubble = $(this).next();

        // Close any that are already open
        $('.quads-message').not(bubble).hide();

        var position = icon.position();
        if (bubble.hasClass('bottom')) {
            bubble.css({
                'left': (position.left - bubble.width() / 2) + 'px',
                'top': (position.top + icon.height() + 9) + 'px'
            });
        } else {
            bubble.css({
                'left': (position.left + icon.width() + 9) + 'px',
                'top': (position.top + icon.height() / 2 - 18) + 'px'
            });
        }

        bubble.toggle();
        e.stopPropagation();
    });
        $(document).on('mouseover', '.quads-general-helper',function (e) {
        e.preventDefault();
        var icon = $(this),
                bubble = $(this).next();

        // Close any that are already open
        $('.quads-message').not(bubble).hide();

        var position = icon.position();
        if (bubble.hasClass('bottom')) {
            bubble.css({
                'left': (position.left - bubble.width() / 2) + 'px',
                'top': (position.top + icon.height() + 9) + 'px'
            });
        } else {
            bubble.css({
                'left': (position.left + icon.width() + 9) + 'px',
                'top': (position.top + icon.height() / 2 - 18) + 'px'
            });
        }

        bubble.toggle();
        e.stopPropagation();
    });
           $(document).on('mouseout', '.quads-general-helper',function (e) {
            $('.quads-message').hide();
            });
    $(document).on('click', '.quads-general-helper',function (e) {
        var icon = $(this),
                bubble = $(this).next();
        if(bubble.html() === undefined){
            return ;
        }else{
            e.preventDefault();
        }

        // Close any that are already open
        $('.quads-message').not(bubble).hide();

        var position = icon.position();
        if (bubble.hasClass('bottom')) {
            bubble.css({
                'left': (position.left - bubble.width() / 2) + 'px',
                'top': (position.top + icon.height() + 9) + 'px'
            });
        } else {
            bubble.css({
                'left': (position.left + icon.width() + 9) + 'px',
                'top': (position.top + icon.height() / 2 - 18) + 'px'
            });
        }

        bubble.toggle();
        e.stopPropagation();
    });

    $('body').click(function () {
        $('.quads-message').hide();
    });

    $('.quads-message').click(function (e) {
        e.stopPropagation();
    });
 
    jQuery('#quads_settings').submit(function() {
        quads_clear_cache();
        // Check if ad name has been changed and prepare new data for saving
        $('.quads-toogle-title').each(function(){
            $(this).children('input').val($(this).text());
        });
        
        
        // Check current url / settings page
        var url = window.location.search;
        var tab_imexport = url.search("page=quads-settings&tab=imexport");
        var tab_licenses = url.search("page=quads-settings&tab=licenses");
        
        jQuery('#quads-save-result').html("<div id='quads-loader' class='quads-loader'></div>");
        jQuery('#quads-loader').append('<p><img src="'+quads.path+'/wp-content/plugins/quick-adsense-reloaded/assets/images/loader1.gif"></p>').show();
        jQuery(this).ajaxSubmit({
            
            success: function(){
                jQuery('#quads-save-result').html("<div id='quads-save-message' class='quads-success-modal'></div>");
                jQuery('#quads-save-message').append('<p><img src="'+quads.path+'/wp-content/plugins/quick-adsense-reloaded/assets/images/saved.gif"></p>').show();
                quads_hide_success_message();
            },
            //,timeout: 0,
            
            error: function(){
                //Do not show alert on import/export tab
                if ( tab_imexport === -1 && tab_licenses === -1  ){
                    alert ('Error: Can not save settings. Try again'); 
                }
                    jQuery('#quads-save-result').hide('fast');
            }
        });
        // Do not use ajax saving on import/export and licenses tab
        if ( tab_imexport === -1 && tab_licenses ===-1 ){
            return false;
        }
    });
      function quads_clear_cache(){
         var data = {
            action: 'quads_clear_cache',
            nonce: quads.nonce,
        };
        $.post(ajaxurl, data, function (resp, status, xhr) {
            console.log('success:' + resp + status + xhr);
        }).fail(function (xhr) { // Will be executed when $.post() fails
            console.log('error: ' + xhr.statusText);
        });
    }
    
    function quads_hide_success_message(){
        setTimeout("jQuery('#quads-save-message').hide()", 1000);
    }
    
    
    /**
     * Paste AdSense Code form
     */ 
    $(document).on('click', '#quads-paste-button', function () {

        var content = $('#quads-adsense-form').val();
        var parseResult = quadsParseAdSenseCode(content);
        if (false !== parseResult) {
            console.log(parseResult);
            setDetailsFromAdCode(parseResult);
        }else{
            $('#quads-msg').html('Can not parse AdSense Code. Is the code valid?');
            $('#quads-msg').show();
        }
    });
   

    /**
     * Populate AdSense Date Fields
     * 
     * @param object adsense
     * @param2 string id of the parent container
     * @returns false
     */
    function setDetailsFromAdCode(GoogleAd) {

        var containerID = $('#quads-adsense-id').val();

        var id = containerID.replace("quads-toggle", "");

        $('#quads_settings\\[ads\\]\\[' + id + '\\]\\[g_data_ad_slot\\]').val(GoogleAd.slotId);
        $('#quads_settings\\[ads\\]\\[' + id + '\\]\\[g_data_ad_client\\]').val(GoogleAd.pubId);
        if ('normal' == GoogleAd.type) {
            console.log($('#quads_settings\\[ads\\]\\[' + id + '\\]\\[adsense_type\\]'));
            $('#quads_settings\\[ads\\]\\[' + id + '\\]\\[adsense_type\\]').val('normal');
            $('#quads_settings\\[ads\\]\\[' + id + '\\]\\[g_data_ad_width\\]').val(GoogleAd.width);
            $('#quads_settings\\[ads\\]\\[' + id + '\\]\\[g_data_ad_height\\]').val(GoogleAd.height);
        }
        if ('responsive' == GoogleAd.type) {
            $('#quads_settings\\[ads\\]\\[' + id + '\\]\\[adsense_type\\]').val('responsive');
            //$('#ad-resize-type').val('auto');
            $('#quads_settings\\[ads\\]\\[' + id + '\\]\\[g_data_ad_width\\]').val('');
            $('#quads_settings\\[ads\\]\\[' + id + '\\]\\[g_data_ad_height\\]').val('');
        }
        // Trigger the ad type select
        $('.quads-select-Type').trigger('change');
        // Hide the overlay
        $('#quads-adsense-bg-div').hide();
        // Ad code input form must not be empty!
        if ($('#' + containerID).children('textarea').val().length === 0){
            $('#' + containerID).children('textarea').val('adsense');
        }
        
        
    }

    /**
     * Parse the adsense ad content
     * @param {type} content
     * @returns {quads-admin.min_L3.parseAdContent.GoogleAd|Boolean}
     */
    function quadsParseAdSenseCode(content) {

        var rawContent = ('undefined' != typeof (content)) ? content.trim() : '';
        var GoogleAd = {};
        var theContent = $('<div />').html(rawContent);
        var asyncGoogleAd = theContent.find('ins');
        //var syncGoogleAd = theContent.search('google_ad_client');

        // Its a async adsense ad
        if (asyncGoogleAd.length > 0) {
            console.log('async ad');

            // Ad Slot ID
            GoogleAd.slotId = asyncGoogleAd.attr('data-ad-slot');

            if ('undefined' != typeof (asyncGoogleAd.attr('data-ad-client'))) {
                // Ad Publisher ID
                GoogleAd.pubId = asyncGoogleAd.attr('data-ad-client').substr(3);
            }

            if (undefined !== GoogleAd.slotId && '' != GoogleAd.pubId) {
                GoogleAd.display = asyncGoogleAd.css('display');
                GoogleAd.format = asyncGoogleAd.attr('data-ad-format');
                GoogleAd.style = asyncGoogleAd.attr('style');

                if ('undefined' == typeof (GoogleAd.format) && -1 != GoogleAd.style.indexOf('width')) {
                    /* normal ad */
                    GoogleAd.type = 'normal';
                    GoogleAd.width = asyncGoogleAd.css('width').replace('px', '');
                    GoogleAd.height = asyncGoogleAd.css('height').replace('px', '');
                    return GoogleAd;
                }

                if ('undefined' != typeof (GoogleAd.format) && 'auto' == GoogleAd.format) {
                    /* Responsive ad, auto resize */
                    GoogleAd.type = 'responsive';
                    return GoogleAd;
                }
                return GoogleAd;
            }

            return false;
        }

        // Google syncronous ad
        if (rawContent.search('google_ad_client') > 0) {
            console.log('syncronous code');

            // Ad Slot ID
            GoogleAd.slotId = get_google_ad_slot(content);
            
            console.log(get_google_ad_slot(content));
            console.log(get_google_ad_client(content));
            console.log(get_google_ad_height(content));
            console.log(get_google_ad_width(content));

            if (!quadsIsEmpty(get_google_ad_client(content))) {
                // Ad Publisher ID
                GoogleAd.pubId = 'ca-pub-' + get_google_ad_client(content);
            }else{
                return false;
            }

            if (!quadsIsEmpty(GoogleAd.slotId) && !quadsIsEmpty(GoogleAd.pubId)) {

                if (!quadsIsEmpty(get_google_ad_width(content))) {
                    GoogleAd.type = 'normal';
                    GoogleAd.width = get_google_ad_width(content);
                    GoogleAd.height = get_google_ad_height(content);
                    return GoogleAd;
                }
            }
            return false;
        }

        return false;
    }

    function get_google_ad_slot(content) {
        const regex = /google_ad_slot\s*=\s*"(\d*)";/g;
        const str = content;
        var m;
        var result = {};
        
        while ((m = regex.exec(str)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === regex.lastIndex) {
                regex.lastIndex++;
            }

            // The result can be accessed through the `m`-variable.
            m.forEach(function(match, index){
                //console.log(`Found match, group ${groupIndex}: ${match}`);
                console.log(match);
                result = match;
            });
        }
        return result;
    }
    function get_google_ad_client(content) {
        const regex = /google_ad_client\s*=\s*"ca-pub-(\d*)";/g;
        const str = content;
        var m;
        var result = {};
        
        while ((m = regex.exec(str)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === regex.lastIndex) {
                regex.lastIndex++;
            }

            // The result can be accessed through the `m`-variable.
            m.forEach(function(match, index){
                //console.log(`Found match, group ${groupIndex}: ${match}`);
                console.log(match);
                result = match;
            });
        }
        return result;
    }
    function get_google_ad_width(content) {
        const regex = /google_ad_width\s*=\s*(\d*);/g;
        const str = content;
        var m;
        var result = {};
        
        while ((m = regex.exec(str)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === regex.lastIndex) {
                regex.lastIndex++;
            }

            // The result can be accessed through the `m`-variable.
            m.forEach(function(match, index){
                //console.log(`Found match, group ${groupIndex}: ${match}`);
                console.log(match);
                result = match;
            });
        }
        return result;
    }
    function get_google_ad_height(content) {
        const regex = /google_ad_height\s*=\s*(\d*);/g;
        const str = content;
        var m;
        var result = {};
        
        while ((m = regex.exec(str)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === regex.lastIndex) {
                regex.lastIndex++;
            }

            // The result can be accessed through the `m`-variable.
            m.forEach(function(match, index){
                //console.log(`Found match, group ${groupIndex}: ${match}`);
                console.log(match);
                result = match;
            });
        }
        return result;
    }
   
    /**
     * Check if return value is empty or not
     * @param {type} str
     * @returns {Boolean}
     */
    function quadsIsEmpty(str) {
        return (!str || 0 === str.length);
    }

    // AdSense Code Input Form
    $(document).on('click', '.quads-add-adsense', function (e) {
        e.preventDefault();
        var parentContainerID = $(this).parents('.quads-ad-toggle-container').attr('id');
        // Empty the ad plain text form
        $('#quads-adsense-form').val('');
        $('#quads-adsense-id').val(parentContainerID);
        $('#quads-adsense-bg-div').show();
    });
    $(document).on('click', '#quads-close-button', function (e) {
        e.preventDefault();
        $('#quads-adsense-bg-div').hide();
    });


    // Toggle between AdSense or Plain Text option
    $(document).on('click', '.quads_adsense_type', function () {

        var parentContainerID = $(this).parents('.quads-ad-toggle-container').attr('id');

        if ($(this).val() === 'adsense') {
            $('#' + parentContainerID).children('textarea').hide();
            $('#' + parentContainerID).find('div.quads_adsense_code').show();
            $('#' + parentContainerID).find('.quads-sizes').show();
            $('#' + parentContainerID).find('.quads-sizes-container').css('clear',''); 

        }
        if ($(this).val() === 'plain_text') {
            $('#' + parentContainerID).children('textarea').show();
            $('#' + parentContainerID).children('div.quads_adsense_code').hide();
            $('#' + parentContainerID).find('.quads-sizes').hide();
            $('#' + parentContainerID).find('.quads-sizes-container').css('clear','both'); 
        }
    });


    // Hide or show AdSense elements on loading
    $('.quads-ad-toggle-container').find('.quads_adsense_type').each(function (index, value) {

        var parentContainerID = $(this).parents('.quads-ad-toggle-container').attr('id');

        if ($(this).attr('checked') === 'checked' && $(this).val() === 'adsense') {
            $('#' + parentContainerID).children('textarea').fadeOut();
            $('#' + parentContainerID).find('div.quads_adsense_code').show();
        }
        if ($(this).attr('checked') === 'checked' && $(this).val() === 'plain_text') {
            $('#' + parentContainerID).children('textarea').fadeIn();
            $('#' + parentContainerID).children('div.quads_adsense_code').hide();
        }
    });
    
        
    // Hide or show AdSense width and height on loading
    $('.quads-ad-toggle-container').find('.quads-select-Type').each(function (index, value) {

        var parentContainerID = $(this).parents('.quads-ad-toggle-container').attr('id');

        if ($(this).val() === 'responsive') {
            $('#' + parentContainerID).find('.quads-type-normal').hide();
            $('#' + parentContainerID).find('.quads-pro-notice').show();
            $('#' + parentContainerID).find('.quads-sizes').show();
            $('#' + parentContainerID).find('.quads-sizes-container').css('clear',''); 
        }
        if ($(this).val() === 'normal') {
            $('#' + parentContainerID).find('.quads-type-normal').show();
            $('#' + parentContainerID).find('.quads-pro-notice').hide();
            $('#' + parentContainerID).find('.quads-sizes').hide();
            $('#' + parentContainerID).find('.quads-sizes-container').css('clear','both');   
        }
    });
    
    
    // Toggle between Fixed Size or Responsive ad type
    $(document).on('change', '.quads-select-Type', function () {
        var parentContainerID = $(this).parents('.quads-ad-toggle-container').attr('id');

        if ($(this).val() === 'responsive') {
            $('#' + parentContainerID).find('.quads-type-normal').hide();
            $('#' + parentContainerID).find('.quads-pro-notice').show();
            $('#' + parentContainerID).find('.quads-sizes').show();
            $('#' + parentContainerID).find('.quads-sizes-container').css('clear',''); 
        }
        if ($(this).val() === 'normal') {
            $('#' + parentContainerID).find('.quads-type-normal').show();
            $('#' + parentContainerID).find('.quads-pro-notice').hide();           
            $('#' + parentContainerID).find('.quads-sizes').hide();
            $('#' + parentContainerID).find('.quads-sizes-container').css('clear','both');     
        }
    });

//*[@id="quads-togglead1"]/div[2]
    /**
     * Toggle the adsense container
     */
    //$('.quads-ad-toggle-header').click(function (e) {
    $('.quads-form-table').on('click', '.quads-ad-toggle-header', function(e) {
        e.preventDefault();

        var container = $('#' + $(this).data('box-id'));
        //console.log(container);
        container.toggle(0, function () {
            if (container.parents('.quads-ad-toggle-header').hasClass('quads-box-close')) {
                // open the box
                container.parents('.quads-ad-toggle-header').removeClass('quads-box-close');
            } else {
                container.parents('.quads-ad-toggle-header').addClass('quads-box-close');
            }
        });
    });

    /*
     * Quick Adsense import process
     */

    jQuery('.quads-import-settings').click(function (e) {
        e.preventDefault();

        if (!confirm('Importing settings from Quick AdSense will overwrite all your current settings. Are you sure?'))
            return;

        jQuery('#quads-import-settings').addClass('loading');
        var data = {
            action: 'quads_import_quick_adsense',
            nonce: quads.nonce,
        };
        $.post(ajaxurl, data, function (resp, status, xhr) {

            //console.log('success:' + resp + status + xhr);
            quads_show_message(resp);

        }).fail(function (xhr) { // Will be executed when $.post() fails
            quads_show_message('Ajax Error: ' + xhr.status + ' ' + xhr.statusText);
            //console.log('error: ' + xhr.statusText);
        });
    });

    jQuery('#quads_insert_ads_action').click(function (e) {
        e.preventDefault();
        jQuery('#quads_insert_ads_box').toggle();
    });

    jQuery('#quads_disable_ads_action').click(function (e) {
        e.preventDefault();
        jQuery('#quads_disable_ads_box').toggle();
    });


    /**
     * Show error message and die()
     * Writes error message into log file
     * 
     * @param {string} $error notice
     * @returns void
     */
    function quads_show_message(error) {
        $('#quads-error-details').show();
        $('#quads-error-details').html(error);
        console.log(error);
    }


    /**
     * Start easytabs()
     */
    if ($(".quads-tabs").length) {
        $('#quads_tab_container').easytabs({
            animate: true,
            updateHash: true,
            animationSpeed: 'fast'
        });
    }
    



}); // document ready

/*
 * jQuery hashchange event - v1.3 - 7/21/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function ($, e, b) {
    var c = "hashchange", h = document, f, g = $.event.special, i = h.documentMode, d = "on" + c in e && (i === b || i > 7);
    function a(j) {
        j = j || location.href;
        return"#" + j.replace(/^[^#]*#?(.*)$/, "$1")
    }
    $.fn[c] = function (j) {
        return j ? this.bind(c, j) : this.trigger(c)
    };
    $.fn[c].delay = 50;
    g[c] = $.extend(g[c], {setup: function () {
            if (d) {
                return false
            }
            $(f.start)
        }, teardown: function () {
            if (d) {
                return false
            }
            $(f.stop)
        }});
    f = (function () {
        var j = {}, p, m = a(), k = function (q) {
            return q
        }, l = k, o = k;
        j.start = function () {
            p || n()
        };
        j.stop = function () {
            p && clearTimeout(p);
            p = b
        };
        function n() {
            var r = a(), q = o(m);
            if (r !== m) {
                l(m = r, q);
                $(e).trigger(c)
            } else {
                if (q !== m) {
                    location.href = location.href.replace(/#.*/, "") + q
                }
            }
            p = setTimeout(n, $.fn[c].delay)
        }
        !d && (function () {
            var q, r;
            j.start = function () {
                if (!q) {
                    r = $.fn[c].src;
                    r = r && r + a();
                    q = $('<iframe tabindex="-1" title="empty"/>').hide().one("load", function () {
                        r || l(a());
                        n()
                    }).attr("src", r || "javascript:0").insertAfter("body")[0].contentWindow;
                    h.onpropertychange = function () {
                        try {
                            if (event.propertyName === "title") {
                                q.document.title = h.title
                            }
                        } catch (s) {
                        }
                    }
                }
            };
            j.stop = k;
            o = function () {
                return a(q.location.href)
            };
            l = function (v, s) {
                var u = q.document, t = $.fn[c].domain;
                if (v !== s) {
                    u.title = h.title;
                    u.open();
                    t && u.write('<script>document.domain="' + t + '"<\/script>');
                    u.close();
                    q.location.hash = v
                }
            }
        })();
        return j
    })()
})(jQuery, this);

/*
 * jQuery EasyTabs plugin 3.2.0
 *
 * Copyright (c) 2010-2011 Steve Schwartz (JangoSteve)
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Date: Thu May 09 17:30:00 2013 -0500
 */
(function (a) {
    a.easytabs = function (j, e) {
        var f = this, q = a(j), i = {animate: true, panelActiveClass: "active", tabActiveClass: "active", defaultTab: "li:first-child", animationSpeed: "fast", tabs: "> ul > li", updateHash: true, cycle: false, collapsible: false, collapsedClass: "collapsed", collapsedByDefault: true, uiTabs: false, transitionIn: "fadeIn", transitionOut: "fadeOut", transitionInEasing: "swing", transitionOutEasing: "swing", transitionCollapse: "slideUp", transitionUncollapse: "slideDown", transitionCollapseEasing: "swing", transitionUncollapseEasing: "swing", containerClass: "", tabsClass: "", tabClass: "", panelClass: "", cache: true, event: "click", panelContext: q}, h, l, v, m, d, t = {fast: 200, normal: 400, slow: 600}, r;
        f.init = function () {
            f.settings = r = a.extend({}, i, e);
            r.bind_str = r.event + ".easytabs";
            if (r.uiTabs) {
                r.tabActiveClass = "ui-tabs-selected";
                r.containerClass = "ui-tabs ui-widget ui-widget-content ui-corner-all";
                r.tabsClass = "ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all";
                r.tabClass = "ui-state-default ui-corner-top";
                r.panelClass = "ui-tabs-panel ui-widget-content ui-corner-bottom"
            }
            if (r.collapsible && e.defaultTab !== undefined && e.collpasedByDefault === undefined) {
                r.collapsedByDefault = false
            }
            if (typeof (r.animationSpeed) === "string") {
                r.animationSpeed = t[r.animationSpeed]
            }
            a("a.anchor").remove().prependTo("body");
            q.data("easytabs", {});
            f.setTransitions();
            f.getTabs();
            b();
            g();
            // w();
            n();
            c();
            q.attr("data-easytabs", true)
        };
        f.setTransitions = function () {
            v = (r.animate) ? {show: r.transitionIn, hide: r.transitionOut, speed: r.animationSpeed, collapse: r.transitionCollapse, uncollapse: r.transitionUncollapse, halfSpeed: r.animationSpeed / 2} : {show: "show", hide: "hide", speed: 0, collapse: "hide", uncollapse: "show", halfSpeed: 0}
        };
        f.getTabs = function () {
            var x;
            f.tabs = q.find(r.tabs), f.panels = a(), f.tabs.each(function () {
                var A = a(this), z = A.children("a"), y = A.children("a").data("target");
                A.data("easytabs", {});
                if (y !== undefined && y !== null) {
                    A.data("easytabs").ajax = z.attr("href")
                } else {
                    y = z.attr("href")
                }
                y = y.match(/#([^\?]+)/)[1];
                x = r.panelContext.find("#" + y);
                if (x.length) {
                    x.data("easytabs", {position: x.css("position"), visibility: x.css("visibility")});
                    x.not(r.panelActiveClass).hide();
                    f.panels = f.panels.add(x);
                    A.data("easytabs").panel = x
                } else {
                    f.tabs = f.tabs.not(A);
                    if ("console" in window) {
                        console.warn("Warning: tab without matching panel for selector '#" + y + "' removed from set")
                    }
                }
            })
        };
        f.selectTab = function (x, C) {
            var y = window.location, B = y.hash.match(/^[^\?]*/)[0], z = x.parent().data("easytabs").panel, A = x.parent().data("easytabs").ajax;
            if (r.collapsible && !d && (x.hasClass(r.tabActiveClass) || x.hasClass(r.collapsedClass))) {
                f.toggleTabCollapse(x, z, A, C)
            } else {
                if (!x.hasClass(r.tabActiveClass) || !z.hasClass(r.panelActiveClass)) {
                    o(x, z, A, C)
                } else {
                    if (!r.cache) {
                        o(x, z, A, C)
                    }
                }
            }
        };
        f.toggleTabCollapse = function (x, y, z, A) {
            f.panels.stop(true, true);
            if (u(q, "easytabs:before", [x, y, r])) {
                f.tabs.filter("." + r.tabActiveClass).removeClass(r.tabActiveClass).children().removeClass(r.tabActiveClass);
                if (x.hasClass(r.collapsedClass)) {
                    if (z && (!r.cache || !x.parent().data("easytabs").cached)) {
                        q.trigger("easytabs:ajax:beforeSend", [x, y]);
                        y.load(z, function (C, B, D) {
                            x.parent().data("easytabs").cached = true;
                            q.trigger("easytabs:ajax:complete", [x, y, C, B, D])
                        })
                    }
                    x.parent().removeClass(r.collapsedClass).addClass(r.tabActiveClass).children().removeClass(r.collapsedClass).addClass(r.tabActiveClass);
                    y.addClass(r.panelActiveClass)[v.uncollapse](v.speed, r.transitionUncollapseEasing, function () {
                        q.trigger("easytabs:midTransition", [x, y, r]);
                        if (typeof A == "function") {
                            A()
                        }
                    })
                } else {
                    x.addClass(r.collapsedClass).parent().addClass(r.collapsedClass);
                    y.removeClass(r.panelActiveClass)[v.collapse](v.speed, r.transitionCollapseEasing, function () {
                        q.trigger("easytabs:midTransition", [x, y, r]);
                        if (typeof A == "function") {
                            A()
                        }
                    })
                }
            }
        };
        f.matchTab = function (x) {
            return f.tabs.find("[href='" + x + "'],[data-target='" + x + "']").first()
        };
        f.matchInPanel = function (x) {
            return(x && f.validId(x) ? f.panels.filter(":has(" + x + ")").first() : [])
        };
        f.validId = function (x) {
            return x.substr(1).match(/^[A-Za-z]+[A-Za-z0-9\-_:\.].$/)
        };
        f.selectTabFromHashChange = function () {
            var y = window.location.hash.match(/^[^\?]*/)[0], x = f.matchTab(y), z;
            if (r.updateHash) {
                if (x.length) {
                    d = true;
                    f.selectTab(x)
                } else {
                    z = f.matchInPanel(y);
                    if (z.length) {
                        y = "#" + z.attr("id");
                        x = f.matchTab(y);
                        d = true;
                        f.selectTab(x)
                    } else {
                        if (!h.hasClass(r.tabActiveClass) && !r.cycle) {
                            if (y === "" || f.matchTab(m).length || q.closest(y).length) {
                                d = true;
                                f.selectTab(l)
                            }
                        }
                    }
                }
            }
        };
        f.cycleTabs = function (x) {
            if (r.cycle) {
                x = x % f.tabs.length;
                $tab = a(f.tabs[x]).children("a").first();
                d = true;
                f.selectTab($tab, function () {
                    setTimeout(function () {
                        f.cycleTabs(x + 1)
                    }, r.cycle)
                })
            }
        };
        f.publicMethods = {select: function (x) {
                var y;
                if ((y = f.tabs.filter(x)).length === 0) {
                    if ((y = f.tabs.find("a[href='" + x + "']")).length === 0) {
                        if ((y = f.tabs.find("a" + x)).length === 0) {
                            if ((y = f.tabs.find("[data-target='" + x + "']")).length === 0) {
                                if ((y = f.tabs.find("a[href$='" + x + "']")).length === 0) {
                                    a.error("Tab '" + x + "' does not exist in tab set")
                                }
                            }
                        }
                    }
                } else {
                    y = y.children("a").first()
                }
                f.selectTab(y)
            }};
        var u = function (A, x, z) {
            var y = a.Event(x);
            A.trigger(y, z);
            return y.result !== false
        };
        var b = function () {
            q.addClass(r.containerClass);
            f.tabs.parent().addClass(r.tabsClass);
            f.tabs.addClass(r.tabClass);
            f.panels.addClass(r.panelClass)
        };
        var g = function () {
            var y = window.location.hash.match(/^[^\?]*/)[0], x = f.matchTab(y).parent(), z;
            if (x.length === 1) {
                h = x;
                r.cycle = false
            } else {
                z = f.matchInPanel(y);
                if (z.length) {
                    y = "#" + z.attr("id");
                    h = f.matchTab(y).parent()
                } else {
                    h = f.tabs.parent().find(r.defaultTab);
                    if (h.length === 0) {
                        a.error("The specified default tab ('" + r.defaultTab + "') could not be found in the tab set ('" + r.tabs + "') out of " + f.tabs.length + " tabs.")
                    }
                }
            }
            l = h.children("a").first();
            p(x)
        };
        var p = function (z) {
            var y, x;
            if (r.collapsible && z.length === 0 && r.collapsedByDefault) {
                h.addClass(r.collapsedClass).children().addClass(r.collapsedClass)
            } else {
                y = a(h.data("easytabs").panel);
                x = h.data("easytabs").ajax;
                if (x && (!r.cache || !h.data("easytabs").cached)) {
                    q.trigger("easytabs:ajax:beforeSend", [l, y]);
                    y.load(x, function (B, A, C) {
                        h.data("easytabs").cached = true;
                        q.trigger("easytabs:ajax:complete", [l, y, B, A, C])
                    })
                }
                h.data("easytabs").panel.show().addClass(r.panelActiveClass);
                h.addClass(r.tabActiveClass).children().addClass(r.tabActiveClass)
            }
            q.trigger("easytabs:initialised", [l, y])
        };
        var w = function () {
            f.tabs.children("a").bind(r.bind_str, function (x) {
                r.cycle = false;
                d = false;
                f.selectTab(a(this));
                x.preventDefault ? x.preventDefault() : x.returnValue = false
            })
        };
        var o = function (z, D, E, H) {
            f.panels.stop(true, true);
            if (u(q, "easytabs:before", [z, D, r])) {
                var A = f.panels.filter(":visible"), y = D.parent(), F, x, C, G, B = window.location.hash.match(/^[^\?]*/)[0];
                if (r.animate) {
                    F = s(D);
                    x = A.length ? k(A) : 0;
                    C = F - x
                }
                m = B;
                G = function () {
                    q.trigger("easytabs:midTransition", [z, D, r]);
                    if (r.animate && r.transitionIn == "fadeIn") {
                        if (C < 0) {
                            y.animate({height: y.height() + C}, v.halfSpeed).css({"min-height": ""})
                        }
                    }
                    if (r.updateHash && !d) {
                        window.location.hash = "#" + D.attr("id")
                    } else {
                        d = false
                    }
                    D[v.show](v.speed, r.transitionInEasing, function () {
                        y.css({height: "", "min-height": ""});
                        q.trigger("easytabs:after", [z, D, r]);
                        if (typeof H == "function") {
                            H()
                        }
                    })
                };
                if (E && (!r.cache || !z.parent().data("easytabs").cached)) {
                    q.trigger("easytabs:ajax:beforeSend", [z, D]);
                    D.load(E, function (J, I, K) {
                        z.parent().data("easytabs").cached = true;
                        q.trigger("easytabs:ajax:complete", [z, D, J, I, K])
                    })
                }
                if (r.animate && r.transitionOut == "fadeOut") {
                    if (C > 0) {
                        y.animate({height: (y.height() + C)}, v.halfSpeed)
                    } else {
                        y.css({"min-height": y.height()})
                    }
                }
                f.tabs.filter("." + r.tabActiveClass).removeClass(r.tabActiveClass).children().removeClass(r.tabActiveClass);
                f.tabs.filter("." + r.collapsedClass).removeClass(r.collapsedClass).children().removeClass(r.collapsedClass);
                z.parent().addClass(r.tabActiveClass).children().addClass(r.tabActiveClass);
                f.panels.filter("." + r.panelActiveClass).removeClass(r.panelActiveClass);
                D.addClass(r.panelActiveClass);
                if (A.length) {
                    A[v.hide](v.speed, r.transitionOutEasing, G)
                } else {
                    D[v.uncollapse](v.speed, r.transitionUncollapseEasing, G)
                }
            }
        };
        var s = function (z) {
            if (z.data("easytabs") && z.data("easytabs").lastHeight) {
                return z.data("easytabs").lastHeight
            }
            var B = z.css("display"), y, x;
            try {
                y = a("<div></div>", {position: "absolute", visibility: "hidden", overflow: "hidden"})
            } catch (A) {
                y = a("<div></div>", {visibility: "hidden", overflow: "hidden"})
            }
            x = z.wrap(y).css({position: "relative", visibility: "hidden", display: "block"}).outerHeight();
            z.unwrap();
            z.css({position: z.data("easytabs").position, visibility: z.data("easytabs").visibility, display: B});
            z.data("easytabs").lastHeight = x;
            return x
        };
        var k = function (y) {
            var x = y.outerHeight();
            if (y.data("easytabs")) {
                y.data("easytabs").lastHeight = x
            } else {
                y.data("easytabs", {lastHeight: x})
            }
            return x
        };
        var n = function () {
            if (typeof a(window).hashchange === "function") {
                a(window).hashchange(function () {
                    f.selectTabFromHashChange()
                })
            } else {
                if (a.address && typeof a.address.change === "function") {
                    a.address.change(function () {
                        f.selectTabFromHashChange()
                    })
                }
            }
        };
        var c = function () {
            var x;
            if (r.cycle) {
                x = f.tabs.index(h);
                setTimeout(function () {
                    f.cycleTabs(x + 1)
                }, r.cycle)
            }
        };
        f.init()
    };
    a.fn.easytabs = function (c) {
        var b = arguments;
        return this.each(function () {
            var e = a(this), d = e.data("easytabs");
            if (undefined === d) {
                d = new a.easytabs(this, c);
                e.data("easytabs", d)
            }
            if (d.publicMethods[c]) {
                return d.publicMethods[c](Array.prototype.slice.call(b, 1))
            }
        })
    }
})(jQuery);


/*
 colpick Color Picker
 Copyright 2013 Jose Vargas. Licensed under GPL license. Based on Stefan Petre's Color Picker www.eyecon.ro, dual licensed under the MIT and GPL licenses
 
 For usage and examples: colpick.com/plugin
 */

(function ($) {
    var colpick = function () {
        var
                tpl = '<div class="colpick"><div class="colpick_color"><div class="colpick_color_overlay1"><div class="colpick_color_overlay2"><div class="colpick_selector_outer"><div class="colpick_selector_inner"></div></div></div></div></div><div class="colpick_hue"><div class="colpick_hue_arrs"><div class="colpick_hue_larr"></div><div class="colpick_hue_rarr"></div></div></div><div class="colpick_new_color"></div><div class="colpick_current_color"></div><div class="colpick_hex_field"><div class="colpick_field_letter">#</div><input type="text" maxlength="6" size="6" /></div><div class="colpick_rgb_r colpick_field"><div class="colpick_field_letter">R</div><input type="text" maxlength="3" size="3" /><div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div></div><div class="colpick_rgb_g colpick_field"><div class="colpick_field_letter">G</div><input type="text" maxlength="3" size="3" /><div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div></div><div class="colpick_rgb_b colpick_field"><div class="colpick_field_letter">B</div><input type="text" maxlength="3" size="3" /><div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div></div><div class="colpick_hsb_h colpick_field"><div class="colpick_field_letter">H</div><input type="text" maxlength="3" size="3" /><div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div></div><div class="colpick_hsb_s colpick_field"><div class="colpick_field_letter">S</div><input type="text" maxlength="3" size="3" /><div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div></div><div class="colpick_hsb_b colpick_field"><div class="colpick_field_letter">B</div><input type="text" maxlength="3" size="3" /><div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div></div><div class="colpick_submit"></div></div>',
                defaults = {
                    showEvent: 'click',
                    onShow: function () {
                    },
                    onBeforeShow: function () {
                    },
                    onHide: function () {
                    },
                    onChange: function () {
                    },
                    onSubmit: function () {
                    },
                    colorScheme: 'light',
                    color: '3289c7',
                    livePreview: true,
                    flat: false,
                    layout: 'full',
                    submit: 1,
                    submitText: 'OK',
                    height: 156
                },
                //Fill the inputs of the plugin
                fillRGBFields = function (hsb, cal) {
                    var rgb = hsbToRgb(hsb);
                    $(cal).data('colpick').fields
                            .eq(1).val(rgb.r).end()
                            .eq(2).val(rgb.g).end()
                            .eq(3).val(rgb.b).end();
                },
                fillHSBFields = function (hsb, cal) {
                    $(cal).data('colpick').fields
                            .eq(4).val(Math.round(hsb.h)).end()
                            .eq(5).val(Math.round(hsb.s)).end()
                            .eq(6).val(Math.round(hsb.b)).end();
                },
                fillHexFields = function (hsb, cal) {
                    $(cal).data('colpick').fields.eq(0).val(hsbToHex(hsb));
                },
                //Set the round selector position
                setSelector = function (hsb, cal) {
                    $(cal).data('colpick').selector.css('backgroundColor', '#' + hsbToHex({h: hsb.h, s: 100, b: 100}));
                    $(cal).data('colpick').selectorIndic.css({
                        left: parseInt($(cal).data('colpick').height * hsb.s / 100, 10),
                        top: parseInt($(cal).data('colpick').height * (100 - hsb.b) / 100, 10)
                    });
                },
                //Set the hue selector position
                setHue = function (hsb, cal) {
                    $(cal).data('colpick').hue.css('top', parseInt($(cal).data('colpick').height - $(cal).data('colpick').height * hsb.h / 360, 10));
                },
                //Set current and new colors
                setCurrentColor = function (hsb, cal) {
                    $(cal).data('colpick').currentColor.css('backgroundColor', '#' + hsbToHex(hsb));
                },
                setNewColor = function (hsb, cal) {
                    $(cal).data('colpick').newColor.css('backgroundColor', '#' + hsbToHex(hsb));
                },
                //Called when the new color is changed
                change = function (ev) {
                    var cal = $(this).parent().parent(), col;
                    if (this.parentNode.className.indexOf('_hex') > 0) {
                        cal.data('colpick').color = col = hexToHsb(fixHex(this.value));
                        fillRGBFields(col, cal.get(0));
                        fillHSBFields(col, cal.get(0));
                    } else if (this.parentNode.className.indexOf('_hsb') > 0) {
                        cal.data('colpick').color = col = fixHSB({
                            h: parseInt(cal.data('colpick').fields.eq(4).val(), 10),
                            s: parseInt(cal.data('colpick').fields.eq(5).val(), 10),
                            b: parseInt(cal.data('colpick').fields.eq(6).val(), 10)
                        });
                        fillRGBFields(col, cal.get(0));
                        fillHexFields(col, cal.get(0));
                    } else {
                        cal.data('colpick').color = col = rgbToHsb(fixRGB({
                            r: parseInt(cal.data('colpick').fields.eq(1).val(), 10),
                            g: parseInt(cal.data('colpick').fields.eq(2).val(), 10),
                            b: parseInt(cal.data('colpick').fields.eq(3).val(), 10)
                        }));
                        fillHexFields(col, cal.get(0));
                        fillHSBFields(col, cal.get(0));
                    }
                    setSelector(col, cal.get(0));
                    setHue(col, cal.get(0));
                    setNewColor(col, cal.get(0));
                    cal.data('colpick').onChange.apply(cal.parent(), [col, hsbToHex(col), hsbToRgb(col), cal.data('colpick').el, 0]);
                },
                //Change style on blur and on focus of inputs
                blur = function (ev) {
                    $(this).parent().removeClass('colpick_focus');
                },
                focus = function () {
                    $(this).parent().parent().data('colpick').fields.parent().removeClass('colpick_focus');
                    $(this).parent().addClass('colpick_focus');
                },
                //Increment/decrement arrows functions
                downIncrement = function (ev) {
                    ev.preventDefault ? ev.preventDefault() : ev.returnValue = false;
                    var field = $(this).parent().find('input').focus();
                    var current = {
                        el: $(this).parent().addClass('colpick_slider'),
                        max: this.parentNode.className.indexOf('_hsb_h') > 0 ? 360 : (this.parentNode.className.indexOf('_hsb') > 0 ? 100 : 255),
                        y: ev.pageY,
                        field: field,
                        val: parseInt(field.val(), 10),
                        preview: $(this).parent().parent().data('colpick').livePreview
                    };
                    $(document).mouseup(current, upIncrement);
                    $(document).mousemove(current, moveIncrement);
                },
                moveIncrement = function (ev) {
                    ev.data.field.val(Math.max(0, Math.min(ev.data.max, parseInt(ev.data.val - ev.pageY + ev.data.y, 10))));
                    if (ev.data.preview) {
                        change.apply(ev.data.field.get(0), [true]);
                    }
                    return false;
                },
                upIncrement = function (ev) {
                    change.apply(ev.data.field.get(0), [true]);
                    ev.data.el.removeClass('colpick_slider').find('input').focus();
                    $(document).off('mouseup', upIncrement);
                    $(document).off('mousemove', moveIncrement);
                    return false;
                },
                //Hue slider functions
                downHue = function (ev) {
                    ev.preventDefault ? ev.preventDefault() : ev.returnValue = false;
                    var current = {
                        cal: $(this).parent(),
                        y: $(this).offset().top
                    };
                    $(document).on('mouseup touchend', current, upHue);
                    $(document).on('mousemove touchmove', current, moveHue);

                    var pageY = ((ev.type == 'touchstart') ? ev.originalEvent.changedTouches[0].pageY : ev.pageY);
                    change.apply(
                            current.cal.data('colpick')
                            .fields.eq(4).val(parseInt(360 * (current.cal.data('colpick').height - (pageY - current.y)) / current.cal.data('colpick').height, 10))
                            .get(0),
                            [current.cal.data('colpick').livePreview]
                            );
                    return false;
                },
                moveHue = function (ev) {
                    var pageY = ((ev.type == 'touchmove') ? ev.originalEvent.changedTouches[0].pageY : ev.pageY);
                    change.apply(
                            ev.data.cal.data('colpick')
                            .fields.eq(4).val(parseInt(360 * (ev.data.cal.data('colpick').height - Math.max(0, Math.min(ev.data.cal.data('colpick').height, (pageY - ev.data.y)))) / ev.data.cal.data('colpick').height, 10))
                            .get(0),
                            [ev.data.preview]
                            );
                    return false;
                },
                upHue = function (ev) {
                    fillRGBFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
                    fillHexFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
                    $(document).off('mouseup touchend', upHue);
                    $(document).off('mousemove touchmove', moveHue);
                    return false;
                },
                //Color selector functions
                downSelector = function (ev) {
                    ev.preventDefault ? ev.preventDefault() : ev.returnValue = false;
                    var current = {
                        cal: $(this).parent(),
                        pos: $(this).offset()
                    };
                    current.preview = current.cal.data('colpick').livePreview;

                    $(document).on('mouseup touchend', current, upSelector);
                    $(document).on('mousemove touchmove', current, moveSelector);

                    var payeX, pageY;
                    if (ev.type == 'touchstart') {
                        pageX = ev.originalEvent.changedTouches[0].pageX,
                                pageY = ev.originalEvent.changedTouches[0].pageY;
                    } else {
                        pageX = ev.pageX;
                        pageY = ev.pageY;
                    }

                    change.apply(
                            current.cal.data('colpick').fields
                            .eq(6).val(parseInt(100 * (current.cal.data('colpick').height - (pageY - current.pos.top)) / current.cal.data('colpick').height, 10)).end()
                            .eq(5).val(parseInt(100 * (pageX - current.pos.left) / current.cal.data('colpick').height, 10))
                            .get(0),
                            [current.preview]
                            );
                    return false;
                },
                moveSelector = function (ev) {
                    var payeX, pageY;
                    if (ev.type == 'touchmove') {
                        pageX = ev.originalEvent.changedTouches[0].pageX,
                                pageY = ev.originalEvent.changedTouches[0].pageY;
                    } else {
                        pageX = ev.pageX;
                        pageY = ev.pageY;
                    }

                    change.apply(
                            ev.data.cal.data('colpick').fields
                            .eq(6).val(parseInt(100 * (ev.data.cal.data('colpick').height - Math.max(0, Math.min(ev.data.cal.data('colpick').height, (pageY - ev.data.pos.top)))) / ev.data.cal.data('colpick').height, 10)).end()
                            .eq(5).val(parseInt(100 * (Math.max(0, Math.min(ev.data.cal.data('colpick').height, (pageX - ev.data.pos.left)))) / ev.data.cal.data('colpick').height, 10))
                            .get(0),
                            [ev.data.preview]
                            );
                    return false;
                },
                upSelector = function (ev) {
                    fillRGBFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
                    fillHexFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
                    $(document).off('mouseup touchend', upSelector);
                    $(document).off('mousemove touchmove', moveSelector);
                    return false;
                },
                //Submit button
                clickSubmit = function (ev) {
                    var cal = $(this).parent();
                    var col = cal.data('colpick').color;
                    cal.data('colpick').origColor = col;
                    setCurrentColor(col, cal.get(0));
                    cal.data('colpick').onSubmit(col, hsbToHex(col), hsbToRgb(col), cal.data('colpick').el);
                },
                //Show/hide the color picker
                show = function (ev) {
                    // Prevent the trigger of any direct parent
                    ev.stopPropagation();
                    var cal = $('#' + $(this).data('colpickId'));
                    cal.data('colpick').onBeforeShow.apply(this, [cal.get(0)]);
                    var pos = $(this).offset();
                    var top = pos.top + this.offsetHeight;
                    var left = pos.left;
                    var viewPort = getViewport();
                    var calW = cal.width();
                    if (left + calW > viewPort.l + viewPort.w) {
                        left -= calW;
                    }
                    cal.css({left: left + 'px', top: top + 'px'});
                    if (cal.data('colpick').onShow.apply(this, [cal.get(0)]) != false) {
                        cal.show();
                    }
                    //Hide when user clicks outside
                    $('html').mousedown({cal: cal}, hide);
                    cal.mousedown(function (ev) {
                        ev.stopPropagation();
                    })
                },
                hide = function (ev) {
                    if (ev.data.cal.data('colpick').onHide.apply(this, [ev.data.cal.get(0)]) != false) {
                        ev.data.cal.hide();
                    }
                    $('html').off('mousedown', hide);
                },
                getViewport = function () {
                    var m = document.compatMode == 'CSS1Compat';
                    return {
                        l: window.pageXOffset || (m ? document.documentElement.scrollLeft : document.body.scrollLeft),
                        w: window.innerWidth || (m ? document.documentElement.clientWidth : document.body.clientWidth)
                    };
                },
                //Fix the values if the user enters a negative or high value
                fixHSB = function (hsb) {
                    return {
                        h: Math.min(360, Math.max(0, hsb.h)),
                        s: Math.min(100, Math.max(0, hsb.s)),
                        b: Math.min(100, Math.max(0, hsb.b))
                    };
                },
                fixRGB = function (rgb) {
                    return {
                        r: Math.min(255, Math.max(0, rgb.r)),
                        g: Math.min(255, Math.max(0, rgb.g)),
                        b: Math.min(255, Math.max(0, rgb.b))
                    };
                },
                fixHex = function (hex) {
                    var len = 6 - hex.length;
                    if (len > 0) {
                        var o = [];
                        for (var i = 0; i < len; i++) {
                            o.push('0');
                        }
                        o.push(hex);
                        hex = o.join('');
                    }
                    return hex;
                },
                restoreOriginal = function () {
                    var cal = $(this).parent();
                    var col = cal.data('colpick').origColor;
                    cal.data('colpick').color = col;
                    fillRGBFields(col, cal.get(0));
                    fillHexFields(col, cal.get(0));
                    fillHSBFields(col, cal.get(0));
                    setSelector(col, cal.get(0));
                    setHue(col, cal.get(0));
                    setNewColor(col, cal.get(0));
                };
        return {
            init: function (opt) {
                opt = $.extend({}, defaults, opt || {});
                //Set color
                if (typeof opt.color == 'string') {
                    opt.color = hexToHsb(opt.color);
                } else if (opt.color.r != undefined && opt.color.g != undefined && opt.color.b != undefined) {
                    opt.color = rgbToHsb(opt.color);
                } else if (opt.color.h != undefined && opt.color.s != undefined && opt.color.b != undefined) {
                    opt.color = fixHSB(opt.color);
                } else {
                    return this;
                }

                //For each selected DOM element
                return this.each(function () {
                    //If the element does not have an ID
                    if (!$(this).data('colpickId')) {
                        var options = $.extend({}, opt);
                        options.origColor = opt.color;
                        //Generate and assign a random ID
                        var id = 'collorpicker_' + parseInt(Math.random() * 1000);
                        $(this).data('colpickId', id);
                        //Set the tpl's ID and get the HTML
                        var cal = $(tpl).attr('id', id);
                        //Add class according to layout
                        cal.addClass('colpick_' + options.layout + (options.submit ? '' : ' colpick_' + options.layout + '_ns'));
                        //Add class if the color scheme is not default
                        if (options.colorScheme != 'light') {
                            cal.addClass('colpick_' + options.colorScheme);
                        }
                        //Setup submit button
                        cal.find('div.colpick_submit').html(options.submitText).click(clickSubmit);
                        //Setup input fields
                        options.fields = cal.find('input').change(change).blur(blur).focus(focus);
                        cal.find('div.colpick_field_arrs').mousedown(downIncrement).end().find('div.colpick_current_color').click(restoreOriginal);
                        //Setup hue selector
                        options.selector = cal.find('div.colpick_color').on('mousedown touchstart', downSelector);
                        options.selectorIndic = options.selector.find('div.colpick_selector_outer');
                        //Store parts of the plugin
                        options.el = this;
                        options.hue = cal.find('div.colpick_hue_arrs');
                        huebar = options.hue.parent();
                        //Paint the hue bar
                        var UA = navigator.userAgent.toLowerCase();
                        var isIE = navigator.appName === 'Microsoft Internet Explorer';
                        var IEver = isIE ? parseFloat(UA.match(/msie ([0-9]{1,}[\.0-9]{0,})/)[1]) : 0;
                        var ngIE = (isIE && IEver < 10);
                        var stops = ['#ff0000', '#ff0080', '#ff00ff', '#8000ff', '#0000ff', '#0080ff', '#00ffff', '#00ff80', '#00ff00', '#80ff00', '#ffff00', '#ff8000', '#ff0000'];
                        if (ngIE) {
                            var i, div;
                            for (i = 0; i <= 11; i++) {
                                div = $('<div></div>').attr('style', 'height:8.333333%; filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=' + stops[i] + ', endColorstr=' + stops[i + 1] + '); -ms-filter: "progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=' + stops[i] + ', endColorstr=' + stops[i + 1] + ')";');
                                huebar.append(div);
                            }
                        } else {
                            stopList = stops.join(',');
                            huebar.attr('style', 'background:-webkit-linear-gradient(top,' + stopList + '); background: -o-linear-gradient(top,' + stopList + '); background: -ms-linear-gradient(top,' + stopList + '); background:-moz-linear-gradient(top,' + stopList + '); -webkit-linear-gradient(top,' + stopList + '); background:linear-gradient(to bottom,' + stopList + '); ');
                        }
                        cal.find('div.colpick_hue').on('mousedown touchstart', downHue);
                        options.newColor = cal.find('div.colpick_new_color');
                        options.currentColor = cal.find('div.colpick_current_color');
                        //Store options and fill with default color
                        cal.data('colpick', options);
                        fillRGBFields(options.color, cal.get(0));
                        fillHSBFields(options.color, cal.get(0));
                        fillHexFields(options.color, cal.get(0));
                        setHue(options.color, cal.get(0));
                        setSelector(options.color, cal.get(0));
                        setCurrentColor(options.color, cal.get(0));
                        setNewColor(options.color, cal.get(0));
                        //Append to body if flat=false, else show in place
                        if (options.flat) {
                            cal.appendTo(this).show();
                            cal.css({
                                position: 'relative',
                                display: 'block'
                            });
                        } else {
                            cal.appendTo(document.body);
                            $(this).on(options.showEvent, show);
                            cal.css({
                                position: 'absolute'
                            });
                        }
                    }
                });
            },
            //Shows the picker
            showPicker: function () {
                return this.each(function () {
                    if ($(this).data('colpickId')) {
                        show.apply(this);
                    }
                });
            },
            //Hides the picker
            hidePicker: function () {
                return this.each(function () {
                    if ($(this).data('colpickId')) {
                        $('#' + $(this).data('colpickId')).hide();
                    }
                });
            },
            //Sets a color as new and current (default)
            setColor: function (col, setCurrent) {
                setCurrent = (typeof setCurrent === "undefined") ? 1 : setCurrent;
                if (typeof col == 'string') {
                    col = hexToHsb(col);
                } else if (col.r != undefined && col.g != undefined && col.b != undefined) {
                    col = rgbToHsb(col);
                } else if (col.h != undefined && col.s != undefined && col.b != undefined) {
                    col = fixHSB(col);
                } else {
                    return this;
                }
                return this.each(function () {
                    if ($(this).data('colpickId')) {
                        var cal = $('#' + $(this).data('colpickId'));
                        cal.data('colpick').color = col;
                        cal.data('colpick').origColor = col;
                        fillRGBFields(col, cal.get(0));
                        fillHSBFields(col, cal.get(0));
                        fillHexFields(col, cal.get(0));
                        setHue(col, cal.get(0));
                        setSelector(col, cal.get(0));

                        setNewColor(col, cal.get(0));
                        cal.data('colpick').onChange.apply(cal.parent(), [col, hsbToHex(col), hsbToRgb(col), cal.data('colpick').el, 1]);
                        if (setCurrent) {
                            setCurrentColor(col, cal.get(0));
                        }
                    }
                });
            }
        };
    }();
    //Color space convertions
    var hexToRgb = function (hex) {
        var hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
        return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF)};
    };
    var hexToHsb = function (hex) {
        return rgbToHsb(hexToRgb(hex));
    };
    var rgbToHsb = function (rgb) {
        var hsb = {h: 0, s: 0, b: 0};
        var min = Math.min(rgb.r, rgb.g, rgb.b);
        var max = Math.max(rgb.r, rgb.g, rgb.b);
        var delta = max - min;
        hsb.b = max;
        hsb.s = max != 0 ? 255 * delta / max : 0;
        if (hsb.s != 0) {
            if (rgb.r == max)
                hsb.h = (rgb.g - rgb.b) / delta;
            else if (rgb.g == max)
                hsb.h = 2 + (rgb.b - rgb.r) / delta;
            else
                hsb.h = 4 + (rgb.r - rgb.g) / delta;
        } else
            hsb.h = -1;
        hsb.h *= 60;
        if (hsb.h < 0)
            hsb.h += 360;
        hsb.s *= 100 / 255;
        hsb.b *= 100 / 255;
        return hsb;
    };
    var hsbToRgb = function (hsb) {
        var rgb = {};
        var h = hsb.h;
        var s = hsb.s * 255 / 100;
        var v = hsb.b * 255 / 100;
        if (s == 0) {
            rgb.r = rgb.g = rgb.b = v;
        } else {
            var t1 = v;
            var t2 = (255 - s) * v / 255;
            var t3 = (t1 - t2) * (h % 60) / 60;
            if (h == 360)
                h = 0;
            if (h < 60) {
                rgb.r = t1;
                rgb.b = t2;
                rgb.g = t2 + t3
            } else if (h < 120) {
                rgb.g = t1;
                rgb.b = t2;
                rgb.r = t1 - t3
            } else if (h < 180) {
                rgb.g = t1;
                rgb.r = t2;
                rgb.b = t2 + t3
            } else if (h < 240) {
                rgb.b = t1;
                rgb.r = t2;
                rgb.g = t1 - t3
            } else if (h < 300) {
                rgb.b = t1;
                rgb.g = t2;
                rgb.r = t2 + t3
            } else if (h < 360) {
                rgb.r = t1;
                rgb.g = t2;
                rgb.b = t1 - t3
            } else {
                rgb.r = 0;
                rgb.g = 0;
                rgb.b = 0
            }
        }
        return {r: Math.round(rgb.r), g: Math.round(rgb.g), b: Math.round(rgb.b)};
    };
    var rgbToHex = function (rgb) {
        var hex = [
            rgb.r.toString(16),
            rgb.g.toString(16),
            rgb.b.toString(16)
        ];
        $.each(hex, function (nr, val) {
            if (val.length == 1) {
                hex[nr] = '0' + val;
            }
        });
        return hex.join('');
    };
    var hsbToHex = function (hsb) {
        return rgbToHex(hsbToRgb(hsb));
    };
    $.fn.extend({
        colpick: colpick.init,
        colpickHide: colpick.hidePicker,
        colpickShow: colpick.showPicker,
        colpickSetColor: colpick.setColor
    });
    $.extend({
        colpick: {
            rgbToHex: rgbToHex,
            rgbToHsb: rgbToHsb,
            hsbToHex: hsbToHex,
            hsbToRgb: hsbToRgb,
            hexToHsb: hexToHsb,
            hexToRgb: hexToRgb
        }
    });
})(jQuery);
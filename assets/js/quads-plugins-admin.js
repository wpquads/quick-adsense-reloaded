var strict;

jQuery(document).ready(function ($) {

    /**
     * DEACTIVATION FEEDBACK FORM
     */
    // show overlay when clicked on "deactivate"
    quads_deactivate_link = $('.wp-admin.plugins-php tr[data-slug="quick-adsense-reloaded"] .row-actions .deactivate a');
    quads_deactivate_link_url = quads_deactivate_link.attr('href');

    quads_deactivate_link.click(function (e) {
        e.preventDefault();

        // only show feedback form once per day
        var c_value = quads_admin_get_cookie("quads_hide_deactivate_feedback");

        if (c_value === undefined) {
            $('#quick-adsense-reloaded-feedback-overlay').show();
        } else {
            // click on the link
            window.location.href = quads_deactivate_link_url;
        }
    });
    // show text fields
    $('#quick-adsense-reloaded-feedback-content input[type="radio"]').click(function () {
        // show text field if there is one
        $(this).parents('li').next('li').children('input[type="text"], textarea').show();
    });
    // send form or close it
    $('#quick-adsense-reloaded-feedback-content .button').click(function (e) {
        e.preventDefault();
        // set cookie for 1 day
        var exdate = new Date();
        exdate.setSeconds(exdate.getSeconds() + 86400);
        document.cookie = "quads_hide_deactivate_feedback=1; expires=" + exdate.toUTCString() + "; path=/";

        $('#quick-adsense-reloaded-feedback-overlay').hide();
        if ('quick-adsense-reloaded-feedback-submit' === this.id) {
            // Send form data
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'quads_send_feedback',
                    data: $('#quick-adsense-reloaded-feedback-content form').serialize()
                },
                complete: function (MLHttpRequest, textStatus, errorThrown) {
                    // deactivate the plugin and close the popup
                    $('#quick-adsense-reloaded-feedback-overlay').remove();
                    window.location.href = quads_deactivate_link_url;

                }
            });
        } else {
            $('#quick-adsense-reloaded-feedback-overlay').remove();
            window.location.href = quads_deactivate_link_url;
        }
    });
    // close form without doing anything
    $('.quick-adsense-reloaded-feedback-not-deactivate').click(function (e) {
        $('#quick-adsense-reloaded-feedback-overlay').hide();
    });
    
    function quads_admin_get_cookie (name) {
	var i, x, y, quads_cookies = document.cookie.split( ";" );
	for (i = 0; i < quads_cookies.length; i++)
	{
		x = quads_cookies[i].substr( 0, quads_cookies[i].indexOf( "=" ) );
		y = quads_cookies[i].substr( quads_cookies[i].indexOf( "=" ) + 1 );
		x = x.replace( /^\s+|\s+$/g, "" );
		if (x === name)
		{
			return unescape( y );
		}
	}
}

}); // document ready
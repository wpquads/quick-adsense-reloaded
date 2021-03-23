var wpquads_adblocker_check = true;

var wpquads_adblocker_check_2 = true;

if ( window.jQuery ) {
   var quads_widget_fixed =   jQuery('.quads_widget_fixed').length;
    if ( quads_widget_fixed ) {
        jQuery(window).load(quads_widget_fixed_init);
    } else {
        if (document.readyState!='loading'){
            quads_widget_fixed_init();
        }
        else{
            document.addEventListener('DOMContentLoaded', quads_widget_fixed_init);
        }
    }
} else {
    console.log('jQuery is not loaded!');
}
function quads_widget_fixed_init(){
    var quads_widget_fixed =   jQuery('.quads_widget_fixed');
    for ( var i = 0; i < quads_widget_fixed.length; i++ ) {
        quads_sidebar(quads_widget_fixed[i]);
    }
    jQuery(window).on('resize', function(){
        for ( var i = 0; i < quads_widget_fixed.length; i++ ) {
            quads_sidebar(quads_widget_fixed[i]);
        }
    });
}
function quads_sidebar(options){
    var options_current_id = jQuery(options).attr('id');
    var options_current_class ='quads_widget_fixed';
    var trigger_top = jQuery('#'+options_current_id).offset();
    var options_current_id_sel = jQuery('#'+options_current_id);
    if ( jQuery('#wpadminbar').length )  { // WordPress admin bar
        trigger_top.top = trigger_top.top + jQuery('#wpadminbar').height();
    }
    jQuery(window).on('scroll.' + options_current_class, function(event) {
        var scroll = jQuery(this).scrollTop();
        if ( scroll  > trigger_top.top) { // fixed bottom
            options_current_id_sel.css('position', 'fixed');
            options_current_id_sel.css('top', '');
        }
    }).trigger('scroll.' + options_current_class);
}
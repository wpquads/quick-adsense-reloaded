
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
    var window_height = jQuery(window).height();
	var document_height = jQuery(document).height();
	var fixed_margin_top = 10;
    if ( jQuery('#wpadminbar').length )  { // WordPress admin bar
        fixed_margin_top = fixed_margin_top+ jQuery('#wpadminbar').height();
    }
    jQuery('.quads-widget-clone-' + options_current_id).remove();

    function widget() {}
	var widgets = new Array();
    var quads_widget_fixed =   jQuery('.quads_widget_fixed');
    for ( var i = 0; i < quads_widget_fixed.length; i++ ) {
      var  widget_obj = jQuery( quads_widget_fixed[i]).parent();

        widget_obj.css('position',''); 
            widgets[i] = new widget();
            widgets[i].obj = widget_obj;
            widgets[i].clone = widget_obj.clone();
            widgets[i].clone.children().remove();
            widgets[i].clone_id = widget_obj.attr('id') + '_clone';
            widgets[i].clone.addClass('quads-widget-clone-' + options_current_id);
            widgets[i].clone.attr('id', widgets[i].clone_id);
            widgets[i].clone.css('height', widget_obj.height());
            widgets[i].clone.css('visibility', 'hidden');
            widgets[i].offset_top = widget_obj.offset().top;
            widgets[i].fixed_margin_top = fixed_margin_top;
            widgets[i].height = widget_obj.outerHeight(true);

            widgets[i].fixed_margin_bottom = fixed_margin_top + widgets[i].height;
            fixed_margin_top += widgets[i].height;

    }
                var next_widgets_height = 0;
	
                var widget_parent_container;
                    
                for ( var i = widgets.length - 1; i >= 0; i-- ) {
                    if (widgets[i]) {
                        widgets[i].next_widgets_height = next_widgets_height;
                        widgets[i].fixed_margin_bottom += next_widgets_height;
                        next_widgets_height += widgets[i].height;
                        if ( !widget_parent_container ) {
                            widget_parent_container = widget_obj.parent();
                            widget_parent_container.addClass('quads-fixed-widget-container');
                            widget_parent_container.css('height','');
                            widget_parent_container.height(widget_parent_container.height());
                        }
                    }
                }
                jQuery(window).off('scroll.' + options.sidebar); 
	
                for ( var i = 0; i < widgets.length; i++ ) {
                    if (widgets[i]) fixed_widget(widgets[i]);
                }

    function fixed_widget(widget) {		
		var trigger_top = widget.offset_top - widget.fixed_margin_top;
		var trigger_bottom = document_height - options.margin_bottom;

		if ( options.stop_id && jQuery('#' + options.stop_id).length ) {
            trigger_bottom = jQuery('#' + options.stop_id).offset().top - options.margin_bottom;
        }

		var widget_width; if ( options.width_inherit ) widget_width = 'inherit'; else widget_width = widget.obj.css('width');
		
		var style_applied_top = false;
		var style_applied_bottom = false;
		var style_applied_normal = false;
		
		jQuery(window).on('scroll.' + options.sidebar, function(event) {
			if ( jQuery(window).width() <= options.screen_max_width || jQuery(window).height() <= options.screen_max_height ) {
				if ( ! style_applied_normal ) { 
					widget.obj.css('position', '');
					widget.obj.css('top', '');
					widget.obj.css('bottom', '');
					widget.obj.css('width', '');
					widget.obj.css('margin', '');
					widget.obj.css('padding', '');
					widget_obj.parent().css('height','');
					if ( jQuery('#'+widget.clone_id).length > 0 ) jQuery('#'+widget.clone_id).remove();
					style_applied_normal = true;
					style_applied_top = false;
					style_applied_bottom = false;		
				}
			} else {
				var scroll = jQuery(this).scrollTop();
				if ( scroll + widget.fixed_margin_bottom >= trigger_bottom ) { // fixed bottom
					if ( !style_applied_bottom ) {
						widget.obj.css('position', 'fixed');
						widget.obj.css('top', '');
						widget.obj.css('width', widget_width);
						if(jQuery('#'+widget.clone_id).length <= 0) widget.obj.before(widget.clone);
						style_applied_bottom = true;
						style_applied_top = false;
						style_applied_normal = false;
					}
					widget.obj.css('bottom', scroll + window_height + widget.next_widgets_height - trigger_bottom);
				} else if ( scroll >= trigger_top ) { // fixed top
					if ( !style_applied_top ) {
						widget.obj.css('position', 'fixed');
						widget.obj.css('top', widget.fixed_margin_top);
						widget.obj.css('bottom', '');
						widget.obj.css('width', widget_width);
						if(jQuery('#'+widget.clone_id).length <= 0) widget.obj.before(widget.clone);
						style_applied_top = true;
						style_applied_bottom = false;
						style_applied_normal = false;
					}
				} else { // normal
					if ( !style_applied_normal ) {
						widget.obj.css('position', '');
						widget.obj.css('top', '');
						widget.obj.css('bottom', '');
						widget.obj.css('width', '');
						if(jQuery('#'+widget.clone_id).length > 0) jQuery('#'+widget.clone_id).remove();
						style_applied_normal = true;
						style_applied_top = false;
						style_applied_bottom = false;
					}
				}
			}
		}).trigger('scroll.' + options.sidebar);
	}	
}
<?php

/**
 * API Functions allow creation of custom ad positions
 *
 * @package     QUADS
 * @subpackage  Functions/API
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.4
 */

/*
 * Sample function for creating custom ad positions in QUADS admin settings.
 * 
 * Use this in your functions.php and the following code in your template files
 * 
 *     <?php if (function_exists('quads_return_custom_ad'))
            echo quads_return_custom_ad('ad_id')
       ?> 
 * 
 * @since 0.9.3
 */
function quads_custom_quads_locations_sample($content) {  
    
    $args = array (
        'id' => 'header', 
        'name' => __( 'Header', 'quick-adsense-reloaded' ), 
        'desc' => __('at <strong>header position</strong> ', 'quick-adsense-reloaded'));
    
    $args2 = array (
        'id' => 'footer', 
        'name' => __( 'Footer', 'quick-adsense-reloaded' ), 
        'desc' => __('at <strong>footer position</strong> ', 'quick-adsense-reloaded'));
    
  $html  = quads_register_ad( $args1 );
  $html .= quads_register_ad( $args2 );
  
  return $content . $html;
}

/**
 * Register custom ad positions
 * 
 * @param array id, name, desc
 */
function quads_register_ad( $args = array() ){
         global $quads_options;

        $id = $args['id'];
        $name = $args['name'];
        $desc = $args['desc'];
                    
        $html  = QUADS()->html->checkbox(array('name' => 'quads_settings[custom][' . $id . '_status]','current'  => !empty($quads_options['custom'][ $id . '_status']) ? $quads_options['custom'][$id . '_status'] : null,'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= QUADS()->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[custom]['.$id.']','selected' => !empty($quads_options['custom'][$id]) ? $quads_options['custom'][$id] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . $desc . '</br>';

    return apply_filters('quads_filter_register_ad',$html, 10);
}


/**
 * Return custom ads
 * 
 * @global arr $quads_options
 * @param string id of the custom ad position 
 */
function quads_return_custom_ad($id){
    global $quads_options;
        
    $active = !empty($quads_options['custom'][$id . '_status']) ? $quads_options['custom'][$id . '_status'] : false;
    $key = !empty($quads_options['custom'][$id]) ? $quads_options['custom'][$id] : false;
    
    if ($key && $active)
        $code = $quads_options['ad' . $key ]['code'];
    
    if (!empty($code) && quads_ad_is_allowed() )
            echo $code; 
    
}

/**
 * Add a new custom position to admin settings
 * 
 * @param string $content
 * @return string filtered content
 */
/*function quads_return_text($content){
     global $quads_options;
     
        $html  = QUADS()->html->checkbox(array('name' => 'quads_settings[pos10][header_status]','current'  => !empty($quads_options['pos10']['header_status']) ? $quads_options['pos10']['header_status'] : null,'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= QUADS()->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos10][header]','selected' => !empty($quads_options['pos10']['header']) ? $quads_options['pos10']['header'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('to <strong>Header</strong>.','quick-adsense-reloaded') . '</br>';

        if ( !quads_ad_is_allowed() )
                return;
            
    return $content . $html;
}*/
//add_filter('quads_ad_position_callback', 'quads_return_text');


/**
 * Add an ad to a custom postion. 
 * Needs an custom action in your template defined like: do_action( 'quads_custom_head' ); 
 * 
 */
/*function quads_custom_header_action(){
    global $quads_options, $ShownAds;
    $active = !empty($quads_options['pos10']['header_status']) ? $quads_options['pos10']['header_status'] : false;
    $key = !empty($quads_options['pos10']['header']) ? $quads_options['pos10']['header'] : false;
    
    if ($key && $active)
        $code = $quads_options['ad' . $key ]['code'];
    
    if (!empty($code))
            echo $code; 
}
add_action('quads_custom_head', 'quads_custom_header_action', 100);
 * */


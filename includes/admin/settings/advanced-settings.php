<?php

/**
 * Register Settings
 *
 * @package     QUADS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/


//add_filter('quads_advanced_settings', 'quads_advanced_settings', 10, 2);

function quads_advanced_settings($content, $id){
    global $quads_options, $quads;
    
        $html  = '<div class="quads-advanced-ad-box">';
        $html .= '<div>'.__('Use size option <strong>Auto</strong> for automatic detection of your adsense sizes.', 'quick-adsense-reloaded').'</div>';
        $html .=   '<div class="quads-left-box">';
        $html .=        '<div class="quads-advanced-description"><label for="quads_settings['.$id.'][desktop]">' . __('Disable on Desktop ', 'quick-adsense-reloaded') . '</label></div>' .  $quads->html->checkbox(array('name' => 'quads_settings['.$id.'][desktop]', 'current'  => !empty($quads_options[$id]['desktop']) ? $quads_options[$id]['desktop'] : null , 'class' => 'quads-checkbox' )); 
        $html .=        '<div class="quads-advanced-description"><label for="quads_settings['.$id.'][tablet_landscape]">' .__('Disable on Tablet Landscape ', 'quick-adsense-reloaded') . '</label></div>' . $quads->html->checkbox(array('name' => 'quads_settings['.$id.'][tablet_landscape]', 'current'  => !empty($quads_options[$id]['tablet_landscape']) ? $quads_options[$id]['tablet_landscape'] : null , 'class' => 'quads-checkbox' )); 
        $html .=        '<div class="quads-advanced-description"><label for="quads_settings['.$id.'][tablet_portrait]">' .__('Disable on Tablet Portrait ', 'quick-adsense-reloaded') . '</label></div>' . $quads->html->checkbox(array('name' => 'quads_settings['.$id.'][tablet_portrait]', 'current'  => !empty($quads_options[$id]['tablet_portrait']) ? $quads_options[$id]['tablet_portrait'] : null , 'class' => 'quads-checkbox' )); 
        $html .=        '<div class="quads-advanced-description"><label for="quads_settings['.$id.'][phone]">' .__('Disable on Phone  ', 'quick-adsense-reloaded') . '</label></div>' . $quads->html->checkbox(array('name' => 'quads_settings['.$id.'][phone]', 'current'  => !empty($quads_options[$id]['phone']) ? $quads_options[$id]['phone'] : null , 'class' => 'quads-checkbox' )); 
        $html .=   '</div>';
        $html .=    '<div>';
        $html .=        '<span class="adsense-size-title">' . __('AdSense Size: ', 'quick-adsense-reloaded') . '</span>' . quads_render_size_option(array('id' => $id, 'type' => 'desktop_size'));
        $html .=        '<span class="adsense-size-title">' .__('AdSense Size: ', 'quick-adsense-reloaded') . '</span>' . quads_render_size_option(array('id' => $id, 'type' => 'tbl_lands_size'));
        $html .=        '<span class="adsense-size-title">' .__('AdSense Size: ', 'quick-adsense-reloaded') . '</span>' . quads_render_size_option(array('id' => $id, 'type' => 'tbl_portr_size'));
        $html .=        '<span class="adsense-size-title">' .__('AdSense Size: ', 'quick-adsense-reloaded') . '</span>' . quads_render_size_option(array('id' => $id, 'type' => 'phone_size'));
        $html .=    '</div>';
        $html .='</div>';
        $html .='<div style="clear:both;height:1px;"><span>' . sprintf(__('If you get a error while saving <a href="%s1" target="_blank">read this.</a>', 'quick-adsense-reloaded'), 'https://wordpress.org/support/topic/404-error-when-saving-plugin-options-takes-me-to-wp-adminoptionsphp/') . '</span></div>';
    
    return $html;
}


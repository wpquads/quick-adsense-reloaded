<?php
/**
 * Register Settings
 *
 * @package     QUADS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0.0
 * @return mixed
 */
function quads_get_option( $key = '', $default = false ) {
	global $quads_options;
	$value = ! empty( $quads_options[ $key ] ) ? $quads_options[ $key ] : $default;
	$value = apply_filters( 'quads_get_option', $value, $key, $default );
	return apply_filters( 'quads_get_option_' . $key, $value, $key, $default );
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array QUADS settings
 */
function quads_get_settings() {
	$settings = get_option( 'quads_settings' );


	if( empty( $settings ) ) {
		// Update old settings with new single option
		$general_settings = is_array( get_option( 'quads_settings_general' ) )    ? get_option( 'quads_settings_general' )  	: array();
		$ext_settings     = is_array( get_option( 'quads_settings_extensions' ) ) ? get_option( 'quads_settings_extensions' )	: array();
		$license_settings = is_array( get_option( 'quads_settings_licenses' ) )   ? get_option( 'quads_settings_licenses' )   : array();
                $addons_settings = is_array( get_option( 'quads_settings_addons' ) )   ? get_option( 'quads_settings_addons' )   : array();
                $imexport_settings = is_array( get_option( 'quads_settings_imexport' ) )   ? get_option( 'quads_settings_imexport' )   : array();
                $help_settings = is_array( get_option( 'quads_settings_help' ) )   ? get_option( 'quads_settings_help' )   : array();
                
		$settings = array_merge( $general_settings, $ext_settings, $imexport_settings, $help_settings);
                
		update_option( 'quads_settings', $settings);
                
	}
	return apply_filters( 'quads_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since 1.0
 * @return void
*/
function quads_register_settings() {

	if ( false == get_option( 'quads_settings' ) ) {
		add_option( 'quads_settings' );
	}

	foreach( quads_get_registered_settings() as $tab => $settings ) {

		add_settings_section(
			'quads_settings_' . $tab,
			__return_null(),
			'__return_false',
			'quads_settings_' . $tab
		);

		foreach ( $settings as $option ) {

			$name = isset( $option['name'] ) ? $option['name'] : '';

			add_settings_field(
				'quads_settings[' . $option['id'] . ']',
				$name,
				function_exists( 'quads_' . $option['type'] . '_callback' ) ? 'quads_' . $option['type'] . '_callback' : 'quads_missing_callback',
				'quads_settings_' . $tab,
				'quads_settings_' . $tab,
				array(
					'id'      => isset( $option['id'] ) ? $option['id'] : null,
					'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
                                        'desc2'   => ! empty( $option['desc2'] ) ? $option['desc2'] : '',
					'name'    => isset( $option['name'] ) ? $option['name'] : null,
					'section' => $tab,
					'size'    => isset( $option['size'] ) ? $option['size'] : null,
					'options' => isset( $option['options'] ) ? $option['options'] : '',
					'std'     => isset( $option['std'] ) ? $option['std'] : '',
                                        'textarea_rows' => isset( $option['textarea_rows']) ? $option['textarea_rows'] : ''
				)
			);
		}
                // Store adsense values 
                quads_store_adsense_args();

	}

	// Creates our settings in the options table
	register_setting( 'quads_settings', 'quads_settings', 'quads_settings_sanitize' );
        

}
add_action('admin_init', 'quads_register_settings');

/**
 * Retrieve the array of plugin settings
 *
 * @since 1.8
 * @return array
*/
function quads_get_registered_settings() {

	/**
	 * 'Whitelisted' QUADS settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 */
	$quads_settings = array(
		/** General Settings */
		'general' => apply_filters( 'quads_settings_general',
			array(
                                array(
					'id' => 'general_header',
					'name' => '<strong>' . __( 'General Settings', 'quick-adsense-reloaded' ) . '</strong>',
					'desc' => __( ' ', 'quick-adsense-reloaded' ),
					'type' => 'header'
				),
                                array(
					'id' => 'maxads',
					'name' => __( 'Adsense:', 'quick-adsense-reloaded' ),
					'desc' => __( 'Ads on a page. Select up to <strong>3 Ads only</strong> if you are solely using Google Ads.', 'quick-adsense-reloaded' ),
                                        'desc2' => __('(Google allows publishers to place up to 3 Adsense for Content on a page. If you have placed these ads manually in the page, you will need to take those into account. If you are using other Ads services, you may select up to 10 Ads.)','quick-adsense-reloaded'),
                                        'type' => 'select',
                                        'std' => 3,
                                        'options' => array(
                                            1 => '1' ,
                                            2 => '2',
                                            3 => '3',
                                            4 => '4' ,
                                            5 => '5',
                                            6 => '6',
                                            7 => '7',
                                            8 => '8',
                                            9 => '9',
                                            10 => '10',
                                        ),
				),
                                array(
					'id' => 'ad_position',
					'name' => __( 'Position - automatic', 'quick-adsense-reloaded' ),
					'desc' => __( 'Select on which post_types the share buttons appear. This values will be ignored when position is specified "manual".', 'quick-adsense-reloaded' ),
					'type' => 'ad_position'
				),
                                array(
					'id' => 'visibility',
					'name' => __( 'Visibility', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'visibility'
				),
                            
                                array(
					'id' => 'quicktags',
					'name' => __( 'Quicktags', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'quicktags'
				),                     
                                /*'load_scripts_footer' => array(
					'id' => 'load_scripts_footer',
					'name' => __( 'JS Load Order', 'quick-adsense-reloaded' ),
					'desc' => __( 'Enable this to load all *.js files into footer. Make sure your theme uses the wp_footer() template tag in the appropriate place. Default: Disabled', 'quick-adsense-reloaded' ),
					'type' => 'checkbox'
				),*/
                                'adsense_header' => array(
					'id' => 'adsense_header',
					'name' => '<strong>' . __( 'AdSense Code', 'quick-adsense-reloaded' ) . '</strong>',
					'desc' => __( 'Paste up to 10 Ad codes on Post Body as assigned above, and up to 10 Ad codes on Sidebar Widget. Ad codes provided must not be identical, repeated codes may result the Ads not being display correctly. Ads will never displays more than once in a page as long as you use the automatic function. If you are using shortcodes take care not to use the same ad on different locations.', 'quick-adsense-reloaded' ),
					'type' => 'header'
				),
                                array(
					'id' => 'ad1',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="1"] </strong></br>PHP: <strong>echo do_shortcode(\'[quads id="1"]\');</strong>', 'quick-adsense-reloaded' ),
                                        'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
                                        
				),
                            array(
					'id' => 'ad2',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="2"] </strong></br>PHP: <strong>echo do_shortcode(\'[quads id="2"]\');</strong>', 'quick-adsense-reloaded' ),
                                        'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
                                        
				),
                            array(
					'id' => 'ad3',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="3"] </strong></br>PHP: <strong>echo do_shortcode(\'[quads id="3"]\');</strong>', 'quick-adsense-reloaded' ),
					'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
                                        
				),
                                array(
					'id' => 'ad4',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="4"] </strong></br>PHP: <strong>echo do_shortcode(\'[quads id="4"]\');</strong>', 'quick-adsense-reloaded' ),
					'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
                                        
				),
                                array(
					'id' => 'ad5',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="5"] </strong></br>PHP: <strong>echo do_shortcode(\'[quads id="5"]\');</strong>', 'quick-adsense-reloaded' ),
					'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
				),
                                array(
					'id' => 'ad6',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="6"] </strong></br>PHP: <strong>echo do_shortcode(\'[quads id="6"]\');</strong>', 'quick-adsense-reloaded' ),
					'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
				),
                                array(
					'id' => 'ad7',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="7"] </strong></br>PHP: <strong>echo do_shortcode(\'[quads id="7"]\');</strong>', 'quick-adsense-reloaded' ),
					'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
				),
                                array(
					'id' => 'ad8',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="8"] </strong></br>PHP: <strong>echo do_shortcode(\'[quads id="8"]\');</strong>', 'quick-adsense-reloaded' ),
					'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
                                ),
                                array(
					'id' => 'ad9',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="9"] </strong></br>Function: <strong>echo do_shortcode(\'[quads id="9"]\');</strong>', 'quick-adsense-reloaded' ),
					'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
				),
                                array(
					'id' => 'ad10',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( 'Shortcode: <strong>[quads id="10"] </strong></br>PHP <strong>echo do_shortcode(\'[quads id="10"]\');</strong>', 'quick-adsense-reloaded' ),
					'type' => 'adsense_code',
                                        'options' => quads_get_alignment(),
				),
                                'widget_header' => array(
					'id' => 'widget_header',
					'name' => '<strong>' . __( 'Widgets Code', 'quick-adsense-reloaded' ) . '</strong>',
					'desc' => sprintf( __( 'Every code block creates an unique ad widget in the <a href="%s" target="_self">widget section</a> of WordPress', 'quick-adsense-reloaded' ) , admin_url() . 'widgets.php'),
					'type' => 'header'
				),
                                'ad1_widget' => array(
					'id' => 'ad1_widget',
					'name' => __( 'Ad widget 1', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                'ad2_widget' => array(
					'id' => 'ad2_widget',
					'name' => __( 'Ad widget 2', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                'ad3_widget' => array(
					'id' => 'ad3_widget',
					'name' => __( 'Ad widget 3', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                'ad4_widget' => array(
					'id' => 'ad4_widget',
					'name' => __( 'Ad widget 4', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                'ad5_widget' => array(
					'id' => 'ad5_widget',
					'name' => __( 'Ad widget 5', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                'ad6_widget' => array(
					'id' => 'ad6_widget',
					'name' => __( 'Ad widget 6', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                'ad7_widget' => array(
					'id' => 'ad7_widget',
					'name' => __( 'Ad widget 7', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                'ad8_widget' => array(
					'id' => 'ad8_widget',
					'name' => __( 'Ad widget 8', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                'ad9_widget' => array(
					'id' => 'ad9_widget',
					'name' => __( 'Ad widget 9', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                'ad10_widget' => array(
					'id' => 'ad10_widget',
					'name' => __( 'Ad widget 10', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'textarea',
					'size' => 4
				),
                                array(
					'id' => 'plugin_header',
					'name' => '<strong>' . __( 'Plugin Settings', 'quick-adsense-reloaded' ) . '</strong>',
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'header'
				),
                                'priority' => array(
					'id' => 'priority',
					'name' => __( 'Load Priority', 'quick-adsense-reloaded' ),
					'desc' => __( 'A low value loads WP QUADS earlier than other Plugins. Use a higher value for loading WP QUADS later. Try to lower the value to a value less than 10 if not all ads are shown. <strong>Default:</strong> 20', 'quick-adsense-reloaded' ),
					'type' => 'number',
					'size' => 'small',
                                        'std' => 20
				),
                                'create_settings' => array(
					'id' => 'create_settings',
					'name' => __( 'Settings link', 'quick-adsense-reloaded' ),
					'desc' => __( 'Make the WPQUADS settings available from <strong>Settings->WPQUADS</strong> This will remove the primary menu button from the admin sidebar', 'quick-adsense-reloaded' ),
					'type' => 'checkbox',
				),
                                'uninstall_on_delete' => array(
					'id' => 'uninstall_on_delete',
					'name' => __( 'Remove Data on Uninstall?', 'quick-adsense-reloaded' ),
					'desc' => __( 'Check this box if you would like <strong>Settings->WPQUADS</strong> to completely remove all of its data when the plugin is deleted.', 'quick-adsense-reloaded' ),
					'type' => 'checkbox'
				),

    
			)
		),
		'licenses' => apply_filters('quads_settings_licenses',
			array('licenses_header' => array(
					'id' => 'licenses_header',
					'name' => __( 'Activate Your License', 'quick-adsense-reloaded' ),
					'desc' => '',
					'type' => 'header'
				),)
		),
                'extensions' => apply_filters('quads_settings_extension',
			array()
		),
                'addons' => apply_filters('quads_settings_addons',
			array(
                                'addons' => array(
					'id' => 'addons',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'addons'
				),   
                        )
		),
                'imexport' => apply_filters('quads_settings_imexport',
			array(
                                'imexport' => array(
					'id' => 'imexport',
					'name' => __( '', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'imexport'
				)
                        )
		),
                'help' => apply_filters('quads_settings_help',
			array(
                                
                                'help_header' => array(
					'id' => 'help_header',
					'name' => '<strong>' . __( 'Help', 'quick-adsense-reloaded' ) . '</strong>',
					'desc' => sprintf( __( 'Something not working as expected? Visit the WP<strong>QUADS</strong> <a href="%1s" target="_blank">Support Forum</a>', 'quick-adsense-reloaded' ) , 'https://wordpress.org/support/plugin/quick-adsense-reloaded'),
					'type' => 'header'
				),
                                'systeminfo' => array(
					'id' => 'systeminfo',
					'name' => __( 'Systeminfo', 'quick-adsense-reloaded' ),
					'desc' => __( '', 'quick-adsense-reloaded' ),
					'type' => 'systeminfo'
				),
                                'debug_mode' => array(
					'id' => 'debug_mode',
					'name' => __( 'Debug mode', 'quick-adsense-reloaded' ),
					'desc' => __( 'This does not minify javascript and css files. This makes debugging much easier and is recommended setting for inspecting issues on your site', 'quick-adsense-reloaded' ),
					'type' => 'checkbox'
				)
                        )
		)
	);

	return $quads_settings;
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since 0.9.0
 *
 * @param array $input The value input in the field
 *
 * @return string $input Sanitized value
 */
function quads_settings_sanitize( $input = array() ) {

	global $quads_options;

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = quads_get_registered_settings();
	$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

	$input = $input ? $input : array();
	$input = apply_filters( 'quads_settings_' . $tab . '_sanitize', $input );

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {

		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[$tab][$key]['type'] ) ? $settings[$tab][$key]['type'] : false;

		if ( $type ) {
			// Field type specific filter
			$input[$key] = apply_filters( 'quads_settings_sanitize_' . $type, $value, $key );
		}

		// General filter
		$input[$key] = apply_filters( 'quads_settings_sanitize', $value, $key );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved
	if ( ! empty( $settings[$tab] ) ) {
		foreach ( $settings[$tab] as $key => $value ) {
			// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
			if ( is_numeric( $key ) ) {
				$key = $value['id'];
			}
                        
			if ( empty( $input[$key] ) ) {
				unset( $quads_options[$key] );
                                
			}

		}
	}
      

	// Merge our new settings with the existing
	$output = array_merge( $quads_options, $input );


	add_settings_error( 'quads-notices', '', __( 'Settings updated.', 'quick-adsense-reloaded' ), 'updated' );

	return $output;
}


/**
 * Sanitize text fields
 *
 * @since 1.8
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function quads_sanitize_text_field( $input ) {
	return trim( $input );
}
add_filter( 'quads_settings_sanitize_text', 'quads_sanitize_text_field' );

/**
 * Retrieve settings tabs
 *
 * @since 1.8
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function quads_get_settings_tabs() {

	$settings = quads_get_registered_settings();

	$tabs             = array();
	$tabs['general']  = __( 'General', 'quick-adsense-reloaded' );

        if( ! empty( $settings['visual'] ) ) {
		$tabs['visual'] = __( 'Visual', 'quick-adsense-reloaded' );
	}

        if( ! empty( $settings['networks'] ) ) {
		//$tabs['networks'] = __( 'Social Networks', 'quick-adsense-reloaded' );
	}

	if( ! empty( $settings['extensions'] ) ) {
		$tabs['extensions'] = __( 'Add-On Setting', 'quick-adsense-reloaded' );
	}

        
	if( ! empty( $settings['licenses'] ) && quads_is_advanced() ) {
		$tabs['licenses'] = __( 'Licenses', 'quick-adsense-reloaded' );
	}
        
        //$tabs['addons'] = __( 'Add-Ons', 'quick-adsense-reloaded' );
        
        $tabs['imexport'] = __( 'Import/Export', 'quick-adsense-reloaded' );
        
        $tabs['help'] = __( 'Help', 'quick-adsense-reloaded' );

	//$tabs['misc']      = __( 'Misc', 'quick-adsense-reloaded' );

	return apply_filters( 'quads_settings_tabs', $tabs );
}




/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function quads_header_callback( $args ) {
        if ( !empty( $args['desc'] ) ){
		echo $args['desc'];
        }else{
                echo '&nbsp';
        }
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_checkbox_callback( $args ) {
	global $quads_options;

	$checked = isset( $quads_options[ $args[ 'id' ] ] ) ? checked( 1, $quads_options[ $args[ 'id' ] ], false ) : '';
	$html = '<input type="checkbox" id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
	$html .= '<label class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_multicheck_callback( $args ) {
	global $quads_options;

	if ( ! empty( $args['options'] ) ) {
		foreach( $args['options'] as $key => $option ):
			if( isset( $quads_options[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
			echo '<input name="quads_settings[' . $args['id'] . '][' . $key . ']" id="quads_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
			echo '<label for="quads_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
		endforeach;
		echo '<p class="description quads_hidden">' . $args['desc'] . '</p>';
	}
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 1.3.3
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_radio_callback( $args ) {
	global $quads_options;

	foreach ( $args['options'] as $key => $option ) :
		$checked = false;

		if ( isset( $quads_options[ $args['id'] ] ) && $quads_options[ $args['id'] ] == $key )
			$checked = true;
		elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $quads_options[ $args['id'] ] ) )
			$checked = true;

		echo '<input name="quads_settings[' . $args['id'] . ']"" id="quads_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
		echo '<label for="quads_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
	endforeach;

	echo '<p class="description quads_hidden">' . $args['desc'] . '</p>';
}

/**
 * Gateways Callback
 *
 * Renders gateways fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_gateways_callback( $args ) {
	global $quads_options;

	foreach ( $args['options'] as $key => $option ) :
		if ( isset( $quads_options['gateways'][ $key ] ) )
			$enabled = '1';
		else
			$enabled = null;

		echo '<input name="quads_settings[' . $args['id'] . '][' . $key . ']"" id="quads_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="1" ' . checked('1', $enabled, false) . '/>&nbsp;';
		echo '<label for="quads_settings[' . $args['id'] . '][' . $key . ']">' . $option['admin_label'] . '</label><br/>';
	endforeach;
}



/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_text_callback( $args ) {
	global $quads_options;

	if ( isset( $quads_options[ $args['id'] ] ) )
		$value = $quads_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . $size . '-text" id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label class="quads_hidden" class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since 1.9
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_number_callback( $args ) {
	global $quads_options;

	if ( isset( $quads_options[ $args['id'] ] ) )
		$value = $quads_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_textarea_callback( $args ) {
	global $quads_options;

	if ( isset( $quads_options[ $args['id'] ] ) )
		$value = $quads_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : '40';
	$html = '<textarea class="large-text quads-textarea" cols="50" rows="' . $size . '" id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since 1.3
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_password_callback( $args ) {
	global $quads_options;

	if ( isset( $quads_options[ $args['id'] ] ) )
		$value = $quads_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="password" class="' . $size . '-text" id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
	$html .= '<label for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.3.1
 * @param array $args Arguments passed by the setting
 * @return void
 */
function quads_missing_callback($args) {
	printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'quick-adsense-reloaded' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_select_callback($args) {
	global $quads_options;

	if ( isset( $quads_options[ $args['id'] ] ) )
		$value = $quads_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$html = '<select id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']"/>';

	foreach ( $args['options'] as $option => $name ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
        $html .= '<br>' . $args['desc2'];

	echo $html;
}



/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since 2.1.2
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */


function quads_color_select_callback( $args ) {
	global $quads_options;

        if ( isset( $quads_options[ $args['id'] ] ) )
		$value = $quads_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$html = '<strong>#:</strong><input type="text" style="max-width:80px;border:1px solid #' . esc_attr( stripslashes( $value ) ) . ';border-right:20px solid #' . esc_attr( stripslashes( $value ) ) . ';" id="quads_settings[' . $args['id'] . ']" class="medium-text ' . $args['id'] . '" name="quads_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';

	$html .= '</select>';
	$html .= '<label class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @global $wp_version WordPress Version
 */
function quads_rich_editor_callback( $args ) {
	global $quads_options, $wp_version;
	if ( isset( $quads_options[ $args['id'] ] ) )
		$value = $quads_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
		ob_start();
		wp_editor( stripslashes( $value ), 'quads_settings_' . $args['id'], array( 'textarea_name' => 'quads_settings[' . $args['id'] . ']', 'textarea_rows' => $args['textarea_rows'] ) );
		$html = ob_get_clean();
	} else {
		$html = '<textarea class="large-text quads-richeditor" rows="10" id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	}

	$html .= '<br/><label class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_upload_callback( $args ) {
	global $quads_options;

	if ( isset( $quads_options[ $args['id'] ] ) )
		$value = $quads_options[$args['id']];
	else
		$value = isset($args['std']) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . $size . '-text quads_upload_field" id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<span>&nbsp;<input type="button" class="quads_settings_upload_button button-secondary" value="' . __( 'Upload File', 'quick-adsense-reloaded' ) . '"/></span>';
	$html .= '<label class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since 1.6
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_color_callback( $args ) {
	global $quads_options;

	if ( isset( $quads_options[ $args['id'] ] ) )
		$value = $quads_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$default = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="quads-color-picker" id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
	$html .= '<label class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Registers the license field callback
 *
 * @since 3.0.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS options
 * @return void
 */
if ( ! function_exists( 'quads_license_key_callback' ) ) {
    function quads_license_key_callback( $args ) {
        global $quads_options;
        
        $class = '';

        $messages = array();
        $license  = get_option( $args['options']['is_valid_license_option'] );


        if( isset( $quads_options[$args['id']] ) ) {
            $value = $quads_options[$args['id']];
        } else {
            $value = isset( $args['std'] ) ? $args['std'] : '';
        }

        if( ! empty( $license ) && is_object( $license ) ) {

            // activate_license 'invalid' on anything other than valid, so if there was an error capture it
            if ( false === $license->success ) {

                switch( $license->error ) {

                    case 'expired' :

                        $class = 'error';
                        $messages[] = sprintf(
                            __( 'Your license key expired on %s. Please <a href="%s" target="_blank" title="Renew your license key">renew your license key</a>.', 'quick-adsense-reloaded' ),
                            date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
                            'http://wpquads.com/checkout/?edd_license_key=' . $value . '&utm_campaign=notice&utm_source=license_tab&utm_medium=admin&utm_content=license-expired'
                        );

                        $license_status = 'quads-license-' . $class . '-notice';

                        break;

                    case 'missing' :

                        $class = 'error';
                        $messages[] = sprintf(
                            __( 'Invalid license. Please <a href="%s" target="_blank" title="Visit account page">visit your account page</a> and verify it.', 'quick-adsense-reloaded' ),
                            'http://wpquads.com/your-account?utm_source=licenses-tab&utm_medium=admin&utm_content=invalid-license&utm_campaign=notice'
                        );

                        $license_status = 'quads-license-' . $class . '-notice';

                        break;

                    case 'invalid' :
                    case 'site_inactive' :

                        $class = 'error';
                        $messages[] = sprintf(
                            __( 'Your %s is not active for this URL. Please <a href="%s" target="_blank" title="Visit account page">visit your account page</a> to manage your license key URLs.', 'quick-adsense-reloaded' ),
                            $args['name'],
                            'http://wpquads.com/your-account?utm_campaign=notice&utm_source=licenses-tab&utm_medium=admin&utm_content=invalid-license'
                        );

                        $license_status = 'quads-license-' . $class . '-notice';

                        break;

                    case 'item_name_mismatch' :

                        $class = 'error';
                        $messages[] = sprintf( __( 'This is not a %s.', 'quick-adsense-reloaded' ), $args['name'] );

                        $license_status = 'quads-license-' . $class . '-notice';

                        break;

                    case 'no_activations_left':

                        $class = 'error';
                        $messages[] = sprintf( __( 'Your license key has reached its activation limit. <a href="%s" target="_blank">View possible upgrades</a> now.', 'quick-adsense-reloaded' ), 'http://wpquads.com/your-account?utm_campaign=notice&utm_source=licenses-tab&utm_medium=admin&utm_content=invalid-license' );

                        $license_status = 'quads-license-' . $class . '-notice';

                        break;

                }

            } else {

                switch( $license->license ) {

                    case 'valid' :
                    default:

                        $class = 'valid';

                        $now        = current_time( 'timestamp' );
                        $expiration = strtotime( $license->expires, current_time( 'timestamp' ) );

                        if( 'lifetime' === $license->expires ) {

                            $messages[] = __( 'License key never expires.', 'quick-adsense-reloaded' );

                            $license_status = 'quads-license-lifetime-notice';

                        } elseif( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

                            $messages[] = sprintf(
                                __( 'Your license key expires soon! It expires on %s. <a href="%s" target="_blank" title="Renew license">Renew your license key</a>.', 'quick-adsense-reloaded' ),
                                date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
                                'http://wpquads.com/checkout/?edd_license_key=' . $value . '&utm_campaign=notice&utm_source=licenses-tab&utm_medium=admin'
                            );

                            $license_status = 'quads-license-expires-soon-notice';

                        } else {

                            $messages[] = sprintf(
                                __( 'Your license key expires on %s.', 'quick-adsense-reloaded' ),
                                date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
                            );

                            $license_status = 'quads-license-expiration-date-notice';

                        }

                        break;

                }

            }

        } else {
            $license_status = null;
        }

        $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
        $html = '<input type="text" class="' . sanitize_html_class( $size ) . '-text" id="quads_settings[' . quads_sanitize_key( $args['id'] ) . ']" name="quads_settings[' . quads_sanitize_key( $args['id'] ) . ']" value="' . esc_attr( $value ) . '"/>';

        if ( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) {
            $html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . __( 'Deactivate License',  'quick-adsense-reloaded' ) . '"/>';
        }

        $html .= '<label for="quads_settings[' . quads_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

        if ( ! empty( $messages ) ) {
            foreach( $messages as $message ) {

                $html .= '<div class="quads-license-data quads-license-' . $class . '">';
                $html .= '<p>' . $message . '</p>';
                $html .= '</div>';

            }
        }

        wp_nonce_field( quads_sanitize_key( $args['id'] ) . '-nonce', quads_sanitize_key( $args['id'] ) . '-nonce' );

        if ( isset( $license_status ) ) {
            echo '<div class="' . $license_status . '">' . $html . '</div>';
        } else {
            echo '<div class="quads-license-null">' . $html . '</div>';
        }
    }
}



/**
 * Registers the Add-Ons field callback for WPQUADS Add-Ons
 *
 * @since 2.0.5
 * @param array $args Arguments passed by the setting
 * @return html
 */
function quads_addons_callback( $args ) {
	$html = quads_add_ons_page();
	echo $html;
}

/**
 * Registers the im/export callback for WPQUADS
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @return html
 */
function quads_imexport_callback( $args ) {
	$html = quads_tools_import_export_display();
        $html .= quads_import_quick_adsense_settings();
	echo $html;
}

/**
 * Registers the system info for WPQUADS
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @return html
 */
function quads_systeminfo_callback( $args ) {
	$html = quads_tools_sysinfo_display();
	echo $html;
}

/**
 * Registers the image upload field
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */

	function quads_upload_image_callback( $args ) {
		global $quads_options;

		if ( isset( $quads_options[ $args['id'] ] ) )
			$value = $quads_options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text ' . $args['id'] . '" id="quads_settings[' . $args['id'] . ']" name="quads_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';

		$html .= '<input type="submit" class="button-secondary quads_upload_image" name="' . $args['id'] . '_upload" value="' . __( 'Select Image',  'quick-adsense-reloaded' ) . '"/>';

		$html .= '<label class="quads_hidden" for="quads_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}


/*
 * Post Types Callback
 *
 * Adds a multiple choice drop box
 * for selecting where WPQUADS should be enabled
 *
 * @since 2.0.9
 * @param array $args Arguments passed by the setting
 * @return void
 *
 */

function quads_posttypes_callback ($args){
  global $quads_options;
  $posttypes = get_post_types();

  //if ( ! empty( $args['options'] ) ) {
  if ( ! empty( $posttypes ) ) {
		//foreach( $args['options'] as $key => $option ):
                foreach( $posttypes as $key => $option ):
			if( isset( $quads_options[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
			echo '<input name="quads_settings[' . $args['id'] . '][' . $key . ']" id="quads_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
			echo '<label for="quads_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
		endforeach;
		echo '<p class="description quads_hidden">' . $args['desc'] . '</p>';
	}
}



/*
 * Note Callback
 *
 * Show a note
 *
 * @since 2.2.8
 * @param array $args Arguments passed by the setting
 * @return void
 *
 */

function quads_note_callback ($args){
  global $quads_options;
  //$html = !empty($args['desc']) ? $args['desc'] : '';
  $html = '';
  echo $html;
}

/**
 * Additional content Callback
 * Adds several content text boxes selectable via jQuery easytabs()
 *
 * @param array $args
 * @return string $html
 * @scince 2.3.2
 */

function quads_add_content_callback($args){
    	global $quads_options;

        $html = '<div id="quadstabcontainer" class="tabcontent_container"><ul class="quadstabs" style="width:99%;max-width:500px;">';
            foreach ( $args['options'] as $option => $name ) :
                    $html .= '<li class="quadstab" style="float:left;margin-right:4px;"><a href="#'.$name['id'].'">'.$name['name'].'</a></li>';
            endforeach;
        $html .= '</ul>';
        $html .= '<div class="quadstab-container">';
            foreach ( $args['options'] as $option => $name ) :
                    $value = isset($quads_options[$name['id']]) ? $quads_options[ $name['id']] : '';
                    $textarea = '<textarea class="large-text quads-textarea" cols="50" rows="15" id="quads_settings['. $name['id'] .']" name="quads_settings['.$name['id'].']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
                    $html .= '<div id="'.$name['id'].'" style="max-width:500px;"><span style="padding-top:60px;display:block;">' . $name['desc'] . ':</span><br>' . $textarea . '</div>';
            endforeach;
        $html .= '</div>';
        $html .= '</div>';
	echo $html;
}


/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since 1.0.8.2
 * @param array $args Arguments passed by the setting
 * @return void
 */
function quads_hook_callback( $args ) {
	do_action( 'quads_' . $args['id'] );
}

/**
 * Set manage_options as the cap required to save QUADS settings pages
 *
 * @since 1.9
 * @return string capability required
 */
function quads_set_settings_cap() {
	return 'manage_options';
}
add_filter( 'option_page_capability_quads_settings', 'quads_set_settings_cap' );




/* returns Cache Status if enabled or disabled
 *
 * @since 2.0.4
 * @return string
 */

function quads_cache_status(){
    global $quads_options;
    if (isset($quads_options['disable_cache'])){
        return ' <strong style="color:red;">' . __('Transient Cache disabled! Enable it for performance increase.' , 'quick-adsense-reloaded') . '</strong> ';
    }
}

/* Permission check if logfile is writable
 *
 * @since 2.0.6
 * @return string
 */

function quads_log_permissions(){
    global $quads_options;
    if (!$quads->logger->checkDir() ){
        return '<br><strong style="color:red;">' . __('Log file directory not writable! Set FTP permission to 755 or 777 for /wp-content/plugins/quadssharer/logs/', 'quick-adsense-reloaded') . '</strong> <br> Read here more about <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">file permissions</a> ';
    }
}






/**
 * Get number of available ads
 * 
 * @global $quads_options $quads_options
 * @return array
 */

function quads_get_ads(){
    global $quads_options;
    
    $ads = array(
        0 => __('Random Ads','quick-adsense-reloaded'),
        1 => 'ad1',
        2 => 'ad2',
        3 => 'ad3',
        4 => 'ad4',
        5 => 'ad5',
        6 => 'ad6',
        7 => 'ad7',
        8 => 'ad8',
        9 => 'ad9',
        10=> 'ad10'
    );
    return $ads;
}

/**
 * Get array of 1 to 50 for image and paragraph dropdown values
 * 
 * @global $quads_options $quads_options
 * @return array
 */

function quads_get_values(){
    
    $array = array(1); 
    for ($i = 1; $i <= 50; $i++){
        $array[] = $i;
    }
    unset($array[0]); // remove the 0 and start the array with 1
    return $array;
}

/**
 * Visibility Callback
 *
 * Renders fields for ad visibility
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */

function quads_visibility_callback($args){
    	global $quads_options, $quads;
        
        // Posts & Pages
        $html = $quads->html->checkbox(array('name' => 'quads_settings[visibility][AppPost]','current'  => !empty($quads_options['visibility']['AppPost']) ? $quads_options['visibility']['AppPost'] : null,'class' => 'quads-checkbox' ));
        $html .= __('Posts ', 'quick-adsense-reloaded');
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[visibility][AppPage]','current'  => !empty($quads_options['visibility']['AppPage']) ? $quads_options['visibility']['AppPage'] : null,'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Pages', 'quick-adsense-reloaded') . '<br>';
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[visibility][AppHome]','current'  => !empty($quads_options['visibility']['AppHome']) ? $quads_options['visibility']['AppHome'] : null,'class' => 'quads-checkbox' )) . __('Homepage ','quick-adsense-reloaded');
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[visibility][AppCate]','current'  => !empty($quads_options['visibility']['AppCate']) ? $quads_options['visibility']['AppCate'] : null,'class' => 'quads-checkbox' )) . __('Categories ','quick-adsense-reloaded');
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[visibility][AppArch]','current'  => !empty($quads_options['visibility']['AppArch']) ? $quads_options['visibility']['AppArch'] : null,'class' => 'quads-checkbox' )) . __('Archives ','quick-adsense-reloaded');
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[visibility][AppTags]','current'  => !empty($quads_options['visibility']['AppTags']) ? $quads_options['visibility']['AppTags'] : null,'class' => 'quads-checkbox' )) . __('Tags','quick-adsense-reloaded') . '<br>';
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[visibility][AppSide]','current'  => !empty($quads_options['visibility']['AppSide']) ? $quads_options['visibility']['AppSide'] : null,'class' => 'quads-checkbox' )) . __('Hide AdsWidget on Homepage','quick-adsense-reloaded') . '<br>';
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[visibility][AppLogg]','current'  => !empty($quads_options['visibility']['AppLogg']) ? $quads_options['visibility']['AppLogg'] : null,'class' => 'quads-checkbox' )) . __('Hide Ads when user is logged in.','quick-adsense-reloaded') . '<br>';

        echo $html;
}

/**
 * Ad position Callback
 *
 * Renders multioptions fields for ad position
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_ad_position_callback($args) {
	global $quads_options, $quads;
       

        // Pos 1
        $html  = $quads->html->checkbox(array('name' => 'quads_settings[pos1][BegnAds]','current'  => !empty($quads_options['pos1']['BegnAds']) ? $quads_options['pos1']['BegnAds'] : null,'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos1][BegnRnd]','selected' => !empty($quads_options['pos1']['BegnRnd']) ? $quads_options['pos1']['BegnRnd'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('to <strong>Beginning of Post</strong>','quick-adsense-reloaded') . '</br>';
        
        // Pos 2
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos2][MiddAds]', 'current'  => !empty($quads_options['pos2']['MiddAds']) ? $quads_options['pos2']['MiddAds'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos2][MiddRnd]','selected' => !empty($quads_options['pos2']['MiddRnd']) ? $quads_options['pos2']['MiddRnd'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('to <strong>Middle of Post</strong>','quick-adsense-reloaded') . '</br>';
        
        // Pos 3
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos3][EndiAds]', 'current'  => !empty($quads_options['pos3']['EndiAds']) ? $quads_options['pos3']['EndiAds'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos3][EndiRnd]','selected' => !empty($quads_options['pos3']['EndiRnd']) ? $quads_options['pos3']['EndiRnd'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('to <strong>End of Post</strong>','quick-adsense-reloaded') . '</br>';
        
        // Pos 4
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos4][MoreAds]', 'current'  => !empty($quads_options['pos4']['MoreAds']) ? $quads_options['pos4']['MoreAds'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos4][MoreRnd]','selected' => !empty($quads_options['pos4']['MoreRnd']) ? $quads_options['pos4']['MoreRnd'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('right after <strong>the <span style="font-family:Courier New,Courier,Fixed;">&lt;!--more--&gt;</span> tag</strong>','quick-adsense-reloaded') . '</br>';

        // Pos 5
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos5][LapaAds]', 'current'  => !empty($quads_options['pos5']['LapaAds']) ? $quads_options['pos5']['LapaAds'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos5][LapaRnd]','selected' => !empty($quads_options['pos5']['LapaRnd']) ? $quads_options['pos5']['LapaRnd'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('right before <strong>the last Paragraph</strong>','quick-adsense-reloaded') . ' </br>';

        // Pos 6
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos6][Par1Ads]', 'current'  => !empty($quads_options['pos6']['Par1Ads']) ? $quads_options['pos6']['Par1Ads'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos6][Par1Rnd]','selected' => !empty($quads_options['pos6']['Par1Rnd']) ? $quads_options['pos6']['Par1Rnd'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('<strong>After Paragraph</strong>','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_values(),'name' => 'quads_settings[pos6][Par1Nup]','selected' => !empty($quads_options['pos6']['Par1Nup']) ? $quads_options['pos6']['Par1Nup'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('→','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos6][Par1Con]', 'current'  => !empty($quads_options['pos6']['Par1Con']) ? $quads_options['pos6']['Par1Con'] : null , 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('to <strong>End of Post</strong> if fewer paragraphs are found.','quick-adsense-reloaded') . ' </br>';
        
        // Pos 7
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos7][Par2Ads]', 'current'  => !empty($quads_options['pos7']['Par2Ads']) ? $quads_options['pos7']['Par2Ads']: null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos7][Par2Rnd]','selected' => !empty($quads_options['pos7']['Par2Rnd']) ? $quads_options['pos7']['Par2Rnd'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('<strong>After Paragraph</strong>','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_values(),'name' => 'quads_settings[pos7][Par2Nup]','selected' => !empty($quads_options['pos7']['Par2Nup']) ? $quads_options['pos7']['Par2Nup']: null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('→','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos7][Par2Con]', 'current'  => !empty($quads_options['pos7']['Par2Con']) ? $quads_options['pos7']['Par2Con'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('to <strong>End of Post</strong> if fewer paragraphs are found.','quick-adsense-reloaded') . ' </br>';
        
        // Pos 8
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos8][Par3Ads]', 'current'  => !empty($quads_options['pos8']['Par3Ads']) ? $quads_options['pos8']['Par3Ads'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos8][Par3Rnd]','selected' => !empty($quads_options['pos8']['Par3Rnd']) ? $quads_options['pos8']['Par3Rnd'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('<strong>After Paragraph</strong>','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_values(),'name' => 'quads_settings[pos8][Par3Nup]','selected' => !empty($quads_options['pos8']['Par3Nup']) ? $quads_options['pos8']['Par3Nup'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('→','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos8][Par3Con]', 'current'  => !empty($quads_options['pos8']['Par3Con']) ? $quads_options['pos8']['Par3Con'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('to <strong>End of Post</strong> if fewer paragraphs are found.','quick-adsense-reloaded') . ' </br>';
        
        // Pos 9
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos9][Img1Ads]', 'current'  => !empty($quads_options['pos9']['Img1Ads']) ? $quads_options['pos9']['Img1Ads'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_ads(),'name' => 'quads_settings[pos9][Img1Rnd]','selected' => !empty($quads_options['pos9']['Img1Rnd']) ? $quads_options['pos9']['Img1Rnd'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('<strong>After Image</strong>','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->select(array('options' => quads_get_values(),'name' => 'quads_settings[pos9][Img1Nup]','selected' => !empty($quads_options['pos9']['Img1Nup']) ? $quads_options['pos9']['Img1Nup'] : null, 'show_option_all'  => false,'show_option_none' => false));
        $html .= ' ' . __('→','quick-adsense-reloaded') . ' ';
        $html .= $quads->html->checkbox(array('name' => 'quads_settings[pos9][Img1Con]', 'current'  => !empty($quads_options['pos9']['Img1Con']) ? $quads_options['pos9']['Img1Con'] : null, 'class' => 'quads-checkbox' ));
        $html .= ' ' . __('after <b>Image\'s outer</b><b><span style="font-family:Courier New,Courier,Fixed;"> &lt;div&gt; wp-caption</span></b> if any.','quick-adsense-reloaded') . ' </br>';
        
        echo apply_filters('quads_ad_position_callback', $html);

}



/**
 * Quicktags Callback
 *
 * Renders quicktags fields
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */

function quads_quicktags_callback($args){
    	global $quads_options, $quads;
        
        // Quicktags info
        $html  = '<div style="margin-bottom:5px;"><strong>Optional: </strong><a href="#" id="quads_insert_ads_action">' . __(' Insert Ads into a post, on-the-fly','quick-adsense-reloaded') . '</a></br>' . 
                '<ol style="margin-top:5px;display:none;" id="quads_insert_ads_box">
                <li>' . __('Insert <span class="quads-quote-docs">&lt;!--Ads1--&gt;</span>, <span class="quads-quote-docs">&lt;!--Ads2--&gt;</span>, etc. into a post to show the <b>Particular Ads</b> at specific location.','quick-adsense-reloaded') . '</li>
                <li>' . __('Insert <span class="quads-quote-docs">&lt;!--RndAds--&gt;</span> into a post to show the <b>Random Ads</b> at specific location','quick-adsense-reloaded') . '</li>
                </ol></div>';

                $html .= $quads->html->checkbox(array('name' => 'quads_settings[quicktags][QckTags]', 'current'  => !empty($quads_options['quicktags']['QckTags']) ? $quads_options['quicktags']['QckTags'] : null , 'class' => 'quads-checkbox' )); 
                $html .= __('Show Quicktag Buttons on the HTML Edit Post SubPanel','quick-adsense-reloaded') . '</br>';
                $html .= $quads->html->checkbox(array('name' => 'quads_settings[quicktags][QckRnds]', 'current'  => !empty($quads_options['quicktags']['QckRnds']) ? $quads_options['quicktags']['QckRnds'] : null, 'class' => 'quads-checkbox' )); 
                $html .= __('Hide <span class="quads-quote-docs">&lt;!--RndAds--&gt;</span> from Quicktag Buttons ]','quick-adsense-reloaded') . '</br>';
                //$html .= $quads->html->checkbox(array('name' => 'quads_settings[quicktags][QckOffs]', 'current'  => !empty($quads_options['quicktags']['QckOffs']) ? $quads_options['quicktags']['QckOffs'] : null, 'class' => 'quads-checkbox' ));
                //$html .= __('Hide <span class="quads-quote-docs">&lt;!--NoAds--&gt;</span>, <span class="quads-quote-docs">&lt;!--OffDef--&gt;</span>, <span class="quads-quote-docs">&lt;!--OffWidget--&gt;</span> from Quicktag Buttons','quick-adsense-reloaded') . '</br>';
                //$html .= $quads->html->checkbox(array('name' => 'quads_settings[quicktags][QckOfPs]', 'current'  => !empty($quads_options['quicktags']['QckOfPs']) ? $quads_options['quicktags']['QckOfPs'] : null, 'class' => 'quads-checkbox' )); 
                //$html .= __('Hide <span class="quads-quote-docs">&lt;!--OffBegin--&gt;</span>, <span class="quads-quote-docs">&lt;!--OffMiddle--&gt;</span>, <span class="quads-quote-docs">&lt;!--OffEnd--&gt;</span>, <span class="quads-quote-docs">&lt;!--OffAfMore--&gt;</span>, <span class="quads-quote-docs">&lt;!--OffBfLastPara--&gt;</span> from Quicktag Buttons','quick-adsense-reloaded') . '</br>';
                $html .= '<span class="quads-desc">' . __('Tags can be inserted into a post via the additional Quicktag Buttons at the HTML Edit Post SubPanel.','quick-adsense-reloaded') . '</span>';
        echo $html;
}

/**
  * This hook should be removed and the hook function should replace entire "quads_ad_position_callback" function.
  */
 add_filter( 'quads_ad_position_callback', 'quads_render_ad_locations' );
  		  
 /**
  * Return ad locations HTML based on new API.
  *
  * @param $html
  * @return string   Locations HTML
  */
  function quads_render_ad_locations ( $html ) {
 	global $quads_options, $_quads_registered_ad_locations, $quads;

        if ( isset( $_quads_registered_ad_locations ) && is_array( $_quads_registered_ad_locations ) ) {
 		foreach ( $_quads_registered_ad_locations as $location => $location_args ) {
 
 			$location_settings = quads_get_ad_location_settings( $location );
 
 			$html .= $quads->html->checkbox( array(
 				'name'              => 'quads_settings[location_settings][' . $location . '][status]',
 				'current'           => ! empty( $location_settings['status'] ) ? $location_settings['status'] : null,
 				'class'             => 'quads-checkbox'
 			) );
 			$html .= ' ' . __('Assign','quick-adsense-reloaded') . ' ';
 
 			$html .= $quads->html->select( array(
 				'options'           => quads_get_ads(),
 				'name'              => 'quads_settings[location_settings][' . $location . '][ad]',
 				'selected'          => ! empty( $location_settings['ad'] ) ? $location_settings['ad'] : null,
 				'show_option_all'   => false,
 				'show_option_none'  => false
 			) );
 			$html .= ' ' . $location_args['description'] . '</br>';
 		}
 	}
 
 	return $html;
 }

/**
 * AdSense Code Callback
 *
 * Renders adsense code fields
 *
 * @since 0.9.0
 * @param array $args Arguments passed by the setting
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */

function quads_adsense_code_callback($args){
    	global $quads_options;
        
        $code = isset( $quads_options[ $args['id'] ]['code'] ) ?  $quads_options[ $args['id'] ]['code'] : '';
        
        $margin = isset ($quads_options[ $args['id'] ]['margin'] ) ?  esc_attr( stripslashes( $quads_options[$args['id']]['margin'] ) ) : 0;
        
        $align = isset ($quads_options[ $args['id'] ]['align'] ) ?  $quads_options[ $args['id'] ]['align'] : 3; // Default value 3 = none
        
	//$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
        
        //$checked = isset( $quads_options[ $args[ 'id' ] ]['align'] ) ? checked( 1, $quads_options[ $args[ 'id' ] ]['align'], false ) : '';
        
        $id = $args['id'];
        
         $html = '<div class="quads-ad-toggle-header quads-box-close" data-box-id="quads-toggle'.$id.'">';
        $html .=    '<div class="quads-toogle-title">Ad '.str_replace('ad','', $id) . '</div>';
        $html .=    '<a class="quads-toggle" data-box-id="quads-toggle'.$id.'" href="#"><div class="quads-close-open-icon"></div></a>';
        $html .= '</div>';
        $html .= '<div class="quads-ad-toggle-container" id="quads-toggle'.$id.'" style="display:none;">';
        $html .= '<div>';
        $html .=    '<textarea style="vertical-align:top;margin-right:20px;float:left;" class="medium-text quads-textarea" cols="50" rows="4" id="quads_settings[' . $args['id'] . '][code]" name="quads_settings[' . $args['id'] . '][code]">' . esc_textarea( stripslashes( $code ) ) . '</textarea><label for="quads_settings[' . $args['id'] . '][code]">' . $args['desc'].'</label>';
        $html .= '</div>';
        $html .= '<div>';        
        $html .=    '<label for="quads_settings[' . $args['id'] . '][margin]"> '.__('Margin (px): ', 'quick-adsense-reloaded').' </label>';
	$html .=    '<input type="number" step="1" max="" min="" class="small-text" id="quads_settings[' . $args['id'] . '][margin]" name="quads_settings[' . $args['id'] . '][margin]" value="' . esc_attr( stripslashes( $margin ) ) . '"/> ';
        $html .=    '<label for="quads_settings[' . $args['id'] . '][align]"> '.__('Align:', 'quick-adsense-reloaded').' </label>';
        $html .=    '<select class="quads-align-input" id="quads_settings[' . $args['id'] . '][align]" name="quads_settings[' . $args['id'] . '][align]"/>';
                    foreach ( $args['options'] as $option => $name ) :
                            $selected = selected( $option, $align, false );
                            $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
                    endforeach;
	$html .=    '</select>';
        $html .= '</div>';
        $html .= apply_filters('quads_advanced_settings', '', $id);
        $html .= quads_pro_overlay();
        $html .='</div>';
        
        echo apply_filters('quads_adsense_settings', $html);
}

/**
 * If advanced settings are not available load overlay image
 * @return string
 */
function quads_pro_overlay(){
        if (quads_is_advanced()){
            return '';
        }
    
        $html  = '<div class="quads-advanced-ad-box quads-pro-overlay"><a href="http://wpquads.com/?utm_campaign=overlay&utm_source=free-plugin&utm_medium=admin" target="_blank"><img src="'. QUADS_PLUGIN_URL . '/assets/images/get_pro_overlay.png"></a></div>';
        $html .='<div style="clear:both;height:1px;"><span>' . sprintf(__('If you get an error while saving <a href="%s1" target="_blank">read this.</a>', 'quick-adsense-reloaded'), 'https://wordpress.org/support/topic/404-error-when-saving-plugin-options-takes-me-to-wp-adminoptionsphp/') . '</span></div>';
    
    return $html;

}


/**
 * 
 * Return array of alignment options
 * 
 * @return array
 */
function quads_get_alignment() {
    // Do not change the key => value order for compatibility reasons
    return array(
        3 => 'none',
        0 => 'left',
        1 => 'center',
        2 => 'right',
    );
}

/**
 * Check if plugin Clickfraud Monitoring is installed
 * 
 * @return boolean true when it is installed and active
 */
function quads_is_installed_clickfraud(){
    $plugin_file = 'cfmonitor/cfmonitor.php';
    $plugin_file2 = 'clickfraud-monitoring/cfmonitor.php';
    
    if ( is_plugin_active( $plugin_file ) || is_plugin_active( $plugin_file2 ) ) {
            return true;   
    }
    
   return false; 
}
/**
 * 
 * @param array $args array(
 * 'id' => 'string),
 * 'type' => desktop, tablet_landscape, tablet_portrait, phone
 * @return string
 */
function quads_render_size_option( $args ) {
    global $quads_options;
    
    if (!isset($args['id'])){
        return '';
    }
    
    $checked = isset($quads_options[ $args['id'] ][ $args['type'] ]) ? $quads_options[ $args['id'] ][ $args['type'] ] : '';
    $html = '<div class="quads-select-style-overwrite">';
    $html .= '<select class="quads-size-input" id="quads_settings[' . $args['id'] . ']['.$args['type'].']" name="quads_settings[' . $args['id'] . '][' . $args['type'] . ']"/>';
    foreach ( quads_get_adsense_sizes() as $key => $value ) :
        $selected = selected( $key, $checked, false );
        $html .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
    endforeach;
    $html .= '</select>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Get all AdSense Sizes
 * @return array
 */
function quads_get_adsense_sizes() {
    $sizes = array(
        'Auto' => 'Auto',
        '120 x 90' => '120 x 90',
        '120 x 240' => '120 x 240',
        '120 x 600' => '120 x 600',
        '125 x 125' => '125 x 125',
        '160 x 90' => '160 x 90',
        '160 x 600' => '160 x 600',
        '180 x 90' => '180 x 90',
        '180 x 150' => '180 x 150',
        '200 x 90' => '200 x 90',
        '200 x 200' => '200 x 200',
        '234 x 60' => '234 x 60',
        '250 x 250' => '250 x 250',
        '320 x 100' => '320 x 100',
        '300 x 250' => '300 x 250',
        '300 x 600' => '300 x 600',
        '300 x 1050' => '300 x 1050',
        '320 x 50' => '320 x 50',
        '336 x 280' => '336 x 280',
        '360 x 300' => '360 x 300',
        '435 x 300' => '435 x 300',
        '468 x 15' => '468 x 15',
        '468 x 60' => '468 x 60',
        '640 x 165' => '640 x 165',
        '640 x 190' => '640 x 190',
        '640 x 300' => '640 x 300',
        '728 x 15' => '728 x 15',
        '728 x 90' => '728 x 90',
        '970 x 90' => '970 x 90',
        '970 x 250' => '970 x 250',
        '240 x 400' => '240 x 400 - Regional ad sizes',
        '250 x 360' => '250 x 360 - Regional ad sizes',
        '580 x 400' => '580 x 400 - Regional ad sizes',
        '750 x 100' => '750 x 100 - Regional ad sizes',
        '750 x 200' => '750 x 200 - Regional ad sizes',
        '750 x 300' => '750 x 300 - Regional ad sizes',
        '980 x 120' => '980 x 120 - Regional ad sizes',
        '930 x 180' => '930 x 180 - Regional ad sizes',
    );
    
    return $sizes;
}


/**
 * Store AdSense parameters
 * 
 * @return boolean
 */
function quads_store_adsense_args() {
global $quads_options;

foreach ( $quads_options as $id => $values ) {

        if( !empty( $values['code'] ) ) {

            //check to see if it is google ad
            if( preg_match( '/googlesyndication.com/', $values['code'] ) ) {
                $quads_options[$id]['current_ad_type'] = 'google';

                //test to see if if google ad asincron
                if( preg_match( '/data-ad-client=/', $values['code'] ) ) {
                    //*** GOOGLE ASYNCRON *************
                    //get g_data_ad_client
                    $explode_ad_code = explode( 'data-ad-client', $values['code'] );
                    preg_match( '/"([a-zA-Z0-9-\s]+)"/', $explode_ad_code[1], $matches_add_client );
                    $quads_options[$id]['g_data_ad_client'] = str_replace( array('"', ' '), array(''), $matches_add_client[1] );

                    //get g_data_ad_slot
                    $explode_ad_code = explode( 'data-ad-slot', $values['code'] );
                    preg_match( '/"([a-zA-Z0-9\s]+)"/', $explode_ad_code[1], $matches_add_slot );
                    $quads_options[$id]['g_data_ad_slot'] = str_replace( array('"', ' '), array(''), $matches_add_slot[1] );
                } else {

                    //*** GOOGLE SYNCRON *************
                    //get g_data_ad_client
                    $explode_ad_code = explode( 'google_ad_client', $values['code'] );
                    preg_match( '/"([a-zA-Z0-9-\s]+)"/', $explode_ad_code[1], $matches_add_client );
                    $quads_options[$id]['g_data_ad_client'] = str_replace( array('"', ' '), array(''), $matches_add_client[1] );

                    //get g_data_ad_slot
                    $explode_ad_code = explode( 'google_ad_slot', $values['code'] );
                    preg_match( '/"([a-zA-Z0-9\s]+)"/', $explode_ad_code[1], $matches_add_slot );
                    $quads_options[$id]['g_data_ad_slot'] = str_replace( array('"', ' '), array(''), $matches_add_slot[1] );
                }
            } else {
                $quads_options[$id]['current_ad_type'] = 'other';
            }
        }
    }

    update_option('quads_settings', $quads_options);
}
/**
 * Check if advanced settings are available
 * 
 * @return boolean
 */
function quads_is_advanced(){
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $plugin = 'wp-quads-pro/wp-quads-pro.php';
    if ( is_plugin_active($plugin) ){
        return true;
    }
    
    
//    if ( file_exists(QUADS_PLUGIN_DIR . '/includes/admin/settings/advanced-settings.php') ){
//        return true;
//    }
    return false;
}

/**
 * Sanitizes a string key for QUADS Settings
 *
 * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are allowed
 *
 * @since  2.0.0
 * @param  string $key String key
 * @return string Sanitized key
 */
function quads_sanitize_key( $key ) {
    $raw_key = $key;
    $key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
    /**
     * Filter a sanitized key string.
     *
     * @since 2.5.8
     * @param string $key     Sanitized key.
     * @param string $raw_key The key prior to sanitization.
     */
    return apply_filters( 'quads_sanitize_key', $key, $raw_key );
}

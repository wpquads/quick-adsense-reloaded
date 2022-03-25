<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function quads_block_assets() { // phpcs:ignore
	// Register block styles for both frontend + backend.
	wp_register_style(
		'quads-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
		QUADS_VERSION // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);

	// Register block editor script for backend.
	wp_register_script(
		'quads-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element' ), // Dependencies, defined above.
		QUADS_VERSION, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	wp_register_style(
		'quads-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		QUADS_VERSION // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `quadsGlobal` object.
	wp_localize_script(
		'quads-js',
		'quadsGlobal', // Array containing dynamic data for a JS Global.
		array(
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
			'quads_get_ads' => quads_get_ads(), 
			// Add more data here that you want to access from `quads_get_ads	` object.
		)
	);

	register_block_type(
		'quads/adds', array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style'         => 'quads-style-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'quads-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'  => 'quads-editor-css',
		)
	);
}

// Hook: Block assets.
add_action( 'init', 'quads_block_assets' );
$quads_settings = get_option( 'quads_settings' );
if(isset($quads_settings['adsforwp_quads_gutenberg']) && $quads_settings['adsforwp_quads_gutenberg'] && class_exists('Adsforwp_Ads_Gutenberg') ){
	remove_action( 'init', array( Adsforwp_Ads_Gutenberg::get_instance(), 'adsforwp_ads_block' ));
	add_action( 'init', 'adsforwp_to_quads_ads_block',999 );
}

  if(isset($quads_settings['adsforwp_quads_gutenberg']) && $quads_settings['adsforwp_quads_gutenberg'] && !class_exists('Adsforwp_Ads_Gutenberg') ){
	add_action( 'init', 'adsforwp_to_quads_ads_block',999 );
	if(! defined('ADSFORWP_PLUGIN_DIR_URI')){
		add_action( 'enqueue_block_editor_assets',  'adsforwp_to_quads_register_admin_scripts'  );
	}
}

function adsforwp_to_quads_ads_block(){
  if ( !function_exists( 'register_block_type' ) ) {
      // no Gutenberg, Abort
      return;
    }
    
    register_block_type( 'adsforwp/adsblock', array(
      'editor_style'  => 'adsforwp-gb-css-editor',
      'editor_script' => 'adsforwp-gb-ad-js',
      'render_callback' => 'adsforwp_to_quads_ads_render_blocks',
    ) );
}
function adsforwp_to_quads_ads_render_blocks($attributes ){
  ob_start();
    if ( !isset( $attributes ) ) {
      ob_end_clean();                                      
      return '';
    }

    // the item is an ad
    if ( 0 === strpos( $attributes['itemID'], 'ad_' ) ) {
      $id = substr( $attributes['itemID'], 3 );

      echo '[adsforwp id="'.$id.'"]';

    } elseif ( 0 === strpos( $attributes['itemID'], 'group_' ) ) {
      $group_id = substr( $attributes['itemID'], 6 );
      $output_function_obj = new adsforwp_output_functions();
        $group_code =  $output_function_obj->adsforwp_group_ads($atts=null, $group_id, '');     
        echo $group_code;
    }

    return ob_get_clean();
}
function adsforwp_to_quads_register_admin_scripts() {
	    if ( !function_exists( 'register_block_type' ) ) {
	            // no Gutenberg, Abort
	            return;
	    }
	    wp_register_script(
            'adsforwp-gb-ad-js',
            QUADS_PLUGIN_URL . '/includes/gutenberg/src/adsforwp-blocks.js',
            array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' )
        );                                         
	   

	     $all_ads = adsforwp_to_quads_get_ad_ids(); 
	    $all_group_ads =  array();
	    $ads = array();
		$groups = array();

		if (is_array($all_ads) && !empty($all_ads)){
			foreach ( $all_ads as $ad_id ) {
				$ads[] = array( 'id' => $ad_id, 'title' => get_the_title( $ad_id ) );
			}
		}

		if (is_array($all_group_ads) && !empty($all_group_ads) ){
			foreach ( $all_group_ads as $gr_ad_id ) {
				$groups[] = array( 'id' => $gr_ad_id, 'name' => get_the_title($gr_ad_id) );
			}
		}


			$default = array(
			'--empty--' => esc_html__( '--empty--', 'ads-for-wp' ),
			'adsforwp' => esc_html__( 'Adsforwp Ads', 'ads-for-wp' ),
			'ads' => esc_html__( 'Ads', 'ads-for-wp' ),
			'adGroups' => esc_html__( 'Ad Groups', 'ads-for-wp' ),
		);

		$inline_script = wp_json_encode(
			array(
				'ads' => $ads,
				'groups' => $groups,
				'editLinks' => array(
					'group' => admin_url( 'edit.php?post_type=adsforwp-groups' ),
					'ad' => admin_url( 'post.php?post=%ID%&action=edit' ),
				),
				'default' => $default
			)
		);

	    wp_add_inline_script( 'adsforwp-gb-ad-js', 'var adsforwpGutenberg = '.$inline_script,'before');
        wp_enqueue_script( 'adsforwp-gb-ad-js' );               
	}

	function adsforwp_to_quads_get_ad_ids(){
        
    $all_ads_id = json_decode(get_transient('adsforwp_transient_ads_ids'), true);
    if(!$all_ads_id){
      $all_ads_post = get_posts(
            array(
                    'post_type'    => 'adsforwp',
                    'posts_per_page'     => -1,
                    'post_status'        => 'publish',
            )
        ); 
        $ads_post_ids = array();
        if($all_ads_post){

            foreach($all_ads_post as $ads){
                $ads_post_ids[] = $ads->ID;         
           }
        }
     
        if(!empty($ads_post_ids) ){
          return $ads_post_ids;
        }else{
          return false;
        }
    }                  
    return $all_ads_id;
}
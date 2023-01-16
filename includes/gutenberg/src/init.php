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
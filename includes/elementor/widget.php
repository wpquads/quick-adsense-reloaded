<?php
namespace ElementorQuads;

class Plugin {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function include_widgets_files() {
		require_once( __DIR__ . '/quads_elementor.php' );
	}

	public function register() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\Quads_Elementor() );
	}
	public function register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Quads_Elementor() );
	}


	public function __construct() {
		// Register widgets
		if(defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.5.0') >= 0 ) {
			add_action( 'elementor/widgets/register', [ $this, 'register' ] );
		}
		else{
			add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
		}
	}
}

Plugin::instance();
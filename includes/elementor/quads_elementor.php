<?php
namespace ElementorQuads\Widgets;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Quads_Elementor extends Widget_Base {

	public function get_name() {
		return 'wp-quads';
	}

	public function get_title() {
		return __( 'WP QUADS', 'quick-adsense-reloaded' );
	}

	public function get_icon() {
		return 'dashicons dashicons-welcome-widgets-menus';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	public function get_script_depends() {
		return [ 'elementor-wp-quads' ];
	}

	protected function _register_controls() {
		$options =array();
		foreach(quads_get_ads() as $key => $value){
			if($key == 0)
			$options['[quads id=RndAds]'] =$value;
			else
			 $options['[quads id='.$key.']'] =$value;
		}

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'quick-adsense-reloaded' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'seleted_add',
			[
				'label' => __( 'Select add to Display', 'quick-adsense-reloaded' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo  $settings['seleted_add'] ;  //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is handled by Elementor
	}

	protected function _content_template() {
		?>
	 {{ settings.seleted_add }}
		<?php
	}
}
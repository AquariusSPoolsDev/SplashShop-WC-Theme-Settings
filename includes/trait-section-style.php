<?php
/**
 * Shared "Section style" controls for full-width homepage widgets:
 * background (default / white / light grey) and top/bottom borders.
 * Styled by the theme (`.shopchop-section` rules in _06-homepage.css).
 */

if (! defined('ABSPATH')) exit;

trait ShopChop_Section_Style
{
	/**
	 * @param bool $borders_default Whether borders are on by default (matches the widget's original look).
	 */
	protected function register_section_style_controls(bool $borders_default): void
	{
		$this->start_controls_section('section_section_style', [
			'label' => esc_html__('Section Style', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('section_background', [
			'label'   => esc_html__('Background', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => [
				''      => esc_html__('Default', 'shopchop-theme-settings'),
				'white' => esc_html__('White', 'shopchop-theme-settings'),
				'grey'  => esc_html__('Light grey', 'shopchop-theme-settings'),
			],
		]);

		$this->add_control('section_borders', [
			'label'        => esc_html__('Section borders', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => $borders_default ? 'yes' : '',
		]);

		$this->add_control('section_border_top', [
			'label'        => esc_html__('Top border', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => ['section_borders' => 'yes'],
		]);

		$this->add_control('section_border_bottom', [
			'label'        => esc_html__('Bottom border', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => ['section_borders' => 'yes'],
		]);

		$this->end_controls_section();
	}

	/** Classes for the section wrapper, e.g. "shopchop-section section-bg-grey section-border-top". */
	protected function section_style_classes(array $settings): string
	{
		$classes = ['shopchop-section'];
		$bg      = $settings['section_background'] ?? '';

		if (in_array($bg, ['white', 'grey'], true)) {
			$classes[] = 'section-bg-' . $bg;
		}

		if (($settings['section_borders'] ?? '') === 'yes') {
			if (($settings['section_border_top'] ?? '') === 'yes') {
				$classes[] = 'section-border-top';
			}
			if (($settings['section_border_bottom'] ?? '') === 'yes') {
				$classes[] = 'section-border-bottom';
			}
		}

		return implode(' ', $classes);
	}
}

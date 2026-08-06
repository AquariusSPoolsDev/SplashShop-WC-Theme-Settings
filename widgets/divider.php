<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Divider extends \Elementor\Widget_Base
{

	public function get_name(): string
	{
		return 'shopchop-divider';
	}

	public function get_title(): string
	{
		return esc_html__('Divider', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-divider';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['divider', 'separator', 'line', 'hr'];
	}

	public function has_widget_inner_wrapper(): bool
	{
		return false;
	}

	protected function is_dynamic_content(): bool
	{
		return false;
	}

	protected function register_controls(): void
	{

		$this->start_controls_section('section_settings', [
			'label' => esc_html__('Settings', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('divider_width', [
			'label'   => esc_html__('Width', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'full',
			'options' => [
				'full'   => esc_html__('Full Width', 'shopchop-theme-settings'),
				'medium' => esc_html__('Medium (60%)', 'shopchop-theme-settings'),
				'short'  => esc_html__('Short (fixed)', 'shopchop-theme-settings'),
			],
		]);

		$this->add_control('divider_spacing', [
			'label'   => esc_html__('Spacing', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'md',
			'options' => [
				'sm' => esc_html__('Small', 'shopchop-theme-settings'),
				'md' => esc_html__('Medium', 'shopchop-theme-settings'),
				'lg' => esc_html__('Large', 'shopchop-theme-settings'),
			],
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings = $this->get_settings_for_display();
		$width    = $settings['divider_width']   ?? 'full';
		$spacing  = $settings['divider_spacing'] ?? 'md';

		$classes = [
			'shopchop-divider',
			'shopchop-divider-' . $width,
			'shopchop-divider-' . $spacing,
		];
?>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?>"></div>
<?php
	}
}

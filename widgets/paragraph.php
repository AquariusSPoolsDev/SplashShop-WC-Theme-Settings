<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Paragraph extends \Elementor\Widget_Base
{

	public function get_name(): string
	{
		return 'shopchop-paragraph';
	}

	public function get_title(): string
	{
		return esc_html__('Paragraph', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-text-align-left';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['paragraph', 'text', 'body copy'];
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

		$this->start_controls_section('section_content', [
			'label' => esc_html__('Content', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('paragraph_text', [
			'label'   => esc_html__('Text', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::WYSIWYG,
			'default' => esc_html__('Add your body text here. Keep it short and to the point.', 'shopchop-theme-settings'),
		]);

		$this->add_control('paragraph_size', [
			'label'   => esc_html__('Size', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'base',
			'options' => [
				'sm'   => esc_html__('Small', 'shopchop-theme-settings'),
				'base' => esc_html__('Base', 'shopchop-theme-settings'),
				'lg'   => esc_html__('Large', 'shopchop-theme-settings'),
			],
		]);

		$this->add_control('paragraph_color', [
			'label'   => esc_html__('Color', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'muted',
			'options' => [
				'muted'      => esc_html__('Muted (neutral)', 'shopchop-theme-settings'),
				'foreground' => esc_html__('Foreground (solid)', 'shopchop-theme-settings'),
			],
		]);

		$this->add_control('paragraph_align', [
			'label'   => esc_html__('Alignment', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'start',
			'options' => [
				'start'  => esc_html__('Left', 'shopchop-theme-settings'),
				'center' => esc_html__('Center', 'shopchop-theme-settings'),
				'end'    => esc_html__('Right', 'shopchop-theme-settings'),
			],
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings = $this->get_settings_for_display();
		$text     = $settings['paragraph_text']  ?? '';
		$size     = $settings['paragraph_size']  ?? 'base';
		$color    = $settings['paragraph_color'] ?? 'muted';
		$align    = $settings['paragraph_align'] ?? 'start';

		if (empty(trim(wp_strip_all_tags($text)))) return;

		$classes = [
			'shopchop-paragraph',
			'shopchop-paragraph-' . $size,
			'shopchop-paragraph-' . $color,
			'text-' . $align,
		];
?>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
			<?php echo wp_kses_post($text); ?>
		</div>
<?php
	}
}

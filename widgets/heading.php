<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Heading extends \Elementor\Widget_Base
{

	public function get_name(): string
	{
		return 'shopchop-heading';
	}

	public function get_title(): string
	{
		return esc_html__('Heading', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-t-letter';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['heading', 'title', 'text'];
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

		$this->add_control('heading_text', [
			'label'       => esc_html__('Text', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'default'     => esc_html__('Add Your Heading Here', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('Type your heading', 'shopchop-theme-settings'),
			'rows'        => 2,
		]);

		$this->add_control('heading_tag', [
			'label'   => esc_html__('HTML Tag', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
			],
		]);

		$this->add_control('heading_style', [
			'label'       => esc_html__('Style', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'default'     => 'section',
			'options'     => [
				'section' => esc_html__('Section Heading (gradient, uppercase)', 'shopchop-theme-settings'),
				'simple'  => esc_html__('Simple (solid color)', 'shopchop-theme-settings'),
			],
			'description' => esc_html__('Matches the heading treatments already used across ShopChop sections.', 'shopchop-theme-settings'),
		]);

		$this->add_control('heading_align', [
			'label'   => esc_html__('Alignment', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'start',
			'options' => [
				'start'  => esc_html__('Left', 'shopchop-theme-settings'),
				'center' => esc_html__('Center', 'shopchop-theme-settings'),
				'end'    => esc_html__('Right', 'shopchop-theme-settings'),
			],
		]);

		$this->add_control('heading_transform', [
			'label'   => esc_html__('Text Transform', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'normal',
			'options' => [
				'normal'    => esc_html__('Normal', 'shopchop-theme-settings'),
				'uppercase' => esc_html__('Uppercase', 'shopchop-theme-settings'),
			],
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings  = $this->get_settings_for_display();
		$text      = $settings['heading_text']      ?? '';
		$tag       = $settings['heading_tag']       ?? 'h2';
		$style     = $settings['heading_style']     ?? 'section';
		$align     = $settings['heading_align']     ?? 'start';
		$transform = $settings['heading_transform'] ?? 'normal';

		if (empty($text)) return;

		$allowed_tags = ['h1', 'h2', 'h3', 'h4'];
		if (! in_array($tag, $allowed_tags, true)) {
			$tag = 'h2';
		}

		$classes = [
			'shopchop-heading',
			'shopchop-heading-' . $style,
			'text-' . $align,
		];

		if ($transform === 'uppercase') {
			$classes[] = 'shopchop-heading-uppercase';
		}
?>
		<<?php echo esc_html($tag); ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
			<?php echo esc_html($text); ?>
		</<?php echo esc_html($tag); ?>>
<?php
	}
}

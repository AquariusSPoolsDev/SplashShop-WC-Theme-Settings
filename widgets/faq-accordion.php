<?php
if (! defined('ABSPATH')) exit;

class ShopChop_FAQ_Accordion extends \Elementor\Widget_Base
{
	public function get_name(): string
	{
		return 'shopchop-faq-accordion';
	}

	public function get_title(): string
	{
		return esc_html__('FAQ Accordion', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-accordion';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['faq', 'accordion', 'question', 'answer', 'toggle', 'collapse'];
	}

	public function has_widget_inner_wrapper(): bool
	{
		return false;
	}

	protected function register_controls(): void
	{
		$this->start_controls_section('section_items', [
			'label' => esc_html__('FAQ Items', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('question', [
			'label'   => esc_html__('Question', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Add your question here', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('answer', [
			'label'   => esc_html__('Answer', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::WYSIWYG,
			'default' => esc_html__('Add your answer here.', 'shopchop-theme-settings'),
		]);

		$this->add_control('faq_items', [
			'label'       => esc_html__('Items', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'question' => esc_html__('Add your question here', 'shopchop-theme-settings'),
					'answer'   => esc_html__('Add your answer here.', 'shopchop-theme-settings'),
				],
			],
			'title_field' => '{{{ question }}}',
		]);

		$this->end_controls_section();

		$this->start_controls_section('section_behavior', [
			'label' => esc_html__('Behavior', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('open_first', [
			'label'        => esc_html__('Open First Item By Default', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings   = $this->get_settings_for_display();
		$items      = $settings['faq_items'] ?? [];
		$open_first = ($settings['open_first'] ?? '') === 'yes';

		if (empty($items)) return;
?>
		<div class="shopchop-accordion">
			<?php foreach ($items as $index => $item) :
				$question = $item['question'] ?? '';
				$answer   = $item['answer']   ?? '';

				if (! $question && ! trim(wp_strip_all_tags($answer))) continue;
			?>
				<details class="shopchop-accordion-item" <?php echo ($open_first && $index === 0) ? 'open' : ''; ?>>
					<summary class="shopchop-accordion-toggle">
						<span class="shopchop-accordion-toggle-title"><?php echo esc_html($question); ?></span>
						<span class="shopchop-accordion-toggle-icon" aria-hidden="true"></span>
					</summary>
					<div class="shopchop-accordion-panel">
						<?php echo wp_kses_post($answer); ?>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
<?php
	}
}

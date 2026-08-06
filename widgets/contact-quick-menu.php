<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Contact_Quick_Menu extends \Elementor\Widget_Base
{

	public function get_name(): string
	{
		return 'shopchop-contact-quick-menu';
	}

	public function get_title(): string
	{
		return esc_html__('Contact Quick Menu', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-icon-box';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['contact', 'quick menu', 'shortcuts', 'icon grid', 'help'];
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

		// ── Heading ──────────────────────────────────────────────
		$this->start_controls_section('section_heading', [
			'label' => esc_html__('Heading', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('heading_text', [
			'label'       => esc_html__('Heading Text', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => esc_html__('What Do You Need?', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. What Do You Need?', 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();

		// ── Menu Items ───────────────────────────────────────────
		$this->start_controls_section('section_items', [
			'label' => esc_html__('Menu Items', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('item_icon', [
			'label'   => esc_html__('Icon', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::ICONS,
			'default' => [
				'value'   => 'fas fa-arrow-right',
				'library' => 'fa-solid',
			],
		]);

		$repeater->add_control('item_label', [
			'label'       => esc_html__('Label', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => esc_html__('Menu Item', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Track Order', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('item_link', [
			'label'         => esc_html__('Link', 'shopchop-theme-settings'),
			'type'          => \Elementor\Controls_Manager::URL,
			'placeholder'   => esc_html__('https://your-link.com', 'shopchop-theme-settings'),
			'show_external' => true,
			'default'       => [
				'url'         => '',
				'is_external' => false,
				'nofollow'    => false,
			],
		]);

		$this->add_control('menu_items', [
			'label'         => esc_html__('Items (max 4)', 'shopchop-theme-settings'),
			'type'          => \Elementor\Controls_Manager::REPEATER,
			'fields'        => $repeater->get_controls(),
			'max_items'     => 4,
			'prevent_empty' => true,
			'default'       => [
				['item_label' => esc_html__('Track Order', 'shopchop-theme-settings'), 'item_icon' => ['value' => 'fas fa-box', 'library' => 'fa-solid']],
				['item_label' => esc_html__('FAQ', 'shopchop-theme-settings'),          'item_icon' => ['value' => 'fas fa-circle-question', 'library' => 'fa-solid']],
				['item_label' => esc_html__('Returns', 'shopchop-theme-settings'),      'item_icon' => ['value' => 'fas fa-rotate-left', 'library' => 'fa-solid']],
			],
			'title_field'   => '{{{ item_label }}}',
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings = $this->get_settings_for_display();
		$heading  = $settings['heading_text'] ?? '';
		$items    = $settings['menu_items']    ?? [];

		if (empty($items)) return;

		// Hard cap at 4 regardless of saved data (e.g. imported templates).
		$items = array_slice($items, 0, 4);
		$count = count($items);
?>

		<div class="shopchop-contact-quick-menu">
			<div class="">
				<?php if ($heading) : ?>
					<h2 class="quick-menu-heading"><?php echo esc_html($heading); ?></h2>
				<?php endif; ?>

				<div class="quick-menu-grid quick-menu-cols-<?php echo esc_attr($count); ?>">
					<?php foreach ($items as $item) :
						$label    = $item['item_label'] ?? '';
						$icon     = $item['item_icon']  ?? [];
						$has_icon = ! empty($icon['value']);
						$link     = $item['item_link']  ?? [];
						$url      = $link['url'] ?? '';
						$tag      = $url ? 'a' : 'div';
					?>
						<<?php echo esc_html($tag); ?>
							<?php if ($url) : ?>
								href="<?php echo esc_url($url); ?>"
								<?php echo ! empty($link['is_external']) ? 'target="_blank"' : ''; ?>
								<?php echo ! empty($link['nofollow']) ? 'rel="nofollow"' : ''; ?>
							<?php endif; ?>
							class="quick-menu-card"
						>
							<?php if ($has_icon) : ?>
								<span class="quick-menu-icon-wrap">
									<?php \Elementor\Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
								</span>
							<?php endif; ?>

							<?php if ($label) : ?>
								<span class="quick-menu-label"><?php echo esc_html($label); ?></span>
							<?php endif; ?>
						</<?php echo esc_html($tag); ?>>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

<?php
	}
}

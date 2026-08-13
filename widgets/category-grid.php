<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Category_Grid extends \Elementor\Widget_Base
{

	public function get_name(): string
	{
		return 'shopchop-category-grid';
	}

	public function get_title(): string
	{
		return esc_html__('Category Grid', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-gallery-grid';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['category', 'grid', 'shop', 'products'];
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
			'default'     => esc_html__('Shop by Category', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Shop by Category', 'shopchop-theme-settings'),
		]);

		$this->add_control('subheading_text', [
			'label'       => esc_html__('Subheading Text', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'default'     => '',
			'placeholder' => esc_html__('Optional supporting text below the heading.', 'shopchop-theme-settings'),
			'rows'        => 2,
		]);

		$this->end_controls_section();

		// ── Items ────────────────────────────────────────────────
		$this->start_controls_section('section_items', [
			'label' => esc_html__('Category Items', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('item_image', [
			'label'       => esc_html__('Image', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::MEDIA,
			'default'     => ['url' => \Elementor\Utils::get_placeholder_image_src()],
			'description' => esc_html__('Transparent PNG recommended, max 300×300px.', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('item_name', [
			'label'       => esc_html__('Name', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => esc_html__('Category Name', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Chemicals', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('item_subtitle', [
			'label'       => esc_html__('Subtitle', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => esc_html__('Optional, e.g. Chlorine, Alum, etc.', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('item_link', [
			'label'       => esc_html__('Link', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::URL,
			'placeholder' => esc_html__('https://your-link.com', 'shopchop-theme-settings'),
			'default'     => ['url' => ''],
		]);

		$this->add_control('category_items', [
			'label'       => esc_html__('Items (max 4)', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				['item_name' => esc_html__('Category Name', 'shopchop-theme-settings')],
			],
			'title_field' => '{{{ item_name }}}',
			'max_items'   => 4,
		]);

		$this->end_controls_section();

		// ── View All ─────────────────────────────────────────────
		$this->start_controls_section('section_view_all', [
			'label' => esc_html__('View All Link', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('view_all_text', [
			'label'       => esc_html__('Text', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => esc_html__('View All Categories', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. View All Categories', 'shopchop-theme-settings'),
		]);

		$this->add_control('view_all_link', [
			'label'       => esc_html__('Link', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::URL,
			'placeholder' => esc_html__('https://your-link.com', 'shopchop-theme-settings'),
			'default'     => ['url' => ''],
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings   = $this->get_settings_for_display();
		$heading    = $settings['heading_text']    ?? '';
		$subheading = $settings['subheading_text'] ?? '';
		$items      = $settings['category_items']  ?? [];
		$va_text    = $settings['view_all_text']   ?? '';
		$va_link    = $settings['view_all_link']['url'] ?? '';

		$items = array_slice($items, 0, 4);

		if (empty($items)) return;
?>

		<div class="shopchop-category-grid">
			<div class="homepage-container">

				<?php if ($heading || $subheading) : ?>
					<div class="category-grid-heading-wrap">
						<?php if ($heading) : ?>
							<h2 class="category-heading"><?php echo esc_html($heading); ?></h2>
						<?php endif; ?>
						<?php if ($subheading) : ?>
							<p class="category-subheading"><?php echo esc_html($subheading); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="category-grid-wrapper">

					<?php foreach ($items as $item) :
						$img_url  = $item['item_image']['url'] ?? '';
						$name     = $item['item_name']         ?? '';
						$subtitle = $item['item_subtitle']     ?? '';
						$link     = $item['item_link']['url']  ?? '';
						$is_ext   = ! empty($item['item_link']['is_external']);
						$nofollow = ! empty($item['item_link']['nofollow']);
					?>

						<a href="<?php echo esc_url($link ?: '#'); ?>"
							class="category-grid-card"
							<?php echo $is_ext ? ' target="_blank"' : ''; ?>
							<?php echo $nofollow ? ' rel="nofollow"' : ''; ?>>

							<?php if ($img_url) : ?>
								<span class="cat-image-wrapper">
									<img src="<?php echo esc_url($img_url); ?>"
										alt="<?php echo esc_attr($name); ?>"
										loading="lazy"
										class="cat-image">
								</span>
							<?php endif; ?>

							<span class="cat-grid-info">
								<?php if ($name) : ?>
									<span class="cat-name"><?php echo esc_html($name); ?></span>
								<?php endif; ?>

								<?php if ($subtitle) : ?>
									<span class="cat-subtitle"><?php echo esc_html($subtitle); ?></span>
								<?php endif; ?>
							</span>

						</a>

					<?php endforeach; ?>

					<?php if ($va_text) : ?>
						<a href="<?php echo esc_url($va_link ?: '#'); ?>" class="category-grid-view-all">
							<?php echo esc_html($va_text); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"></path></svg>
						</a>
					<?php endif; ?>

				</div>
			</div>
		</div>

<?php
	}
}

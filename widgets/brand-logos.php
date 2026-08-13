<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Brand_Logos extends \Elementor\Widget_Base
{

	public function get_name(): string
	{
		return 'shopchop-brand-logos';
	}

	public function get_title(): string
	{
		return esc_html__('Brand Logos', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-image-box';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['brand', 'logos', 'distributor', 'partners'];
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

		// ── Text ────────────────────────────────────────────────
		$this->start_controls_section('section_text', [
			'label' => esc_html__('Heading', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('heading_text', [
			'label'   => esc_html__('Heading Text', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Authorised Distributor for', 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();

		// ── Brands ──────────────────────────────────────────────
		$this->start_controls_section('section_brands', [
			'label' => esc_html__('Brands', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('brand_name', [
			'label'       => esc_html__('Brand Name', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'placeholder' => esc_html__('e.g. Hayward', 'shopchop-theme-settings'),
			'description' => esc_html__('Used as alt text for the logo image.', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('brand_image', [
			'label'       => esc_html__('Logo Image', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::MEDIA,
			'default'     => ['url' => \Elementor\Utils::get_placeholder_image_src()],
			'description' => esc_html__('Recommended: transparent PNG or SVG, max 200 × 80px.', 'shopchop-theme-settings'),
		]);

		$this->add_control('brands', [
			'label'       => esc_html__('Brands', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				['brand_name' => 'Brand 1'],
				['brand_name' => 'Brand 2'],
			],
			'title_field' => '{{{ brand_name || "Brand" }}}',
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings = $this->get_settings_for_display();
		$brands   = $settings['brands'] ?? [];
		$heading  = $settings['heading_text'] ?? '';

		if (empty($brands)) return;
?>

		<div class="shopchop-brand-logos">
			<div class="homepage-container">
				<div class="brand-logo-content">
					<?php if ($heading) : ?>
						<h2 class="brand-distributor-heading"><?php echo esc_html($heading); ?></h2>
						<span class="brand-logo-divider" aria-hidden="true"></span>
					<?php endif; ?>

					<div class="brand-logo-grid">
						<?php foreach ($brands as $brand) :
							$name = ! empty($brand['brand_name']) ? $brand['brand_name'] : '';
							$url  = $brand['brand_image']['url'] ?? '';
							if (! $url) continue;
						?>
							<div class="brand-logo-item group">
								<img src="<?php echo esc_url($url); ?>"
									alt="<?php echo esc_attr($name); ?>"
									title="<?php echo esc_attr($name); ?>"
									loading="lazy"
									class="brand-logo-img">
							</div>
						<?php endforeach; ?>
					</div>
				</div>

			</div>
		</div>

<?php
	}
}

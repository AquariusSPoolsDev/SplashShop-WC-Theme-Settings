<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Product_Spotlight extends \Elementor\Widget_Base
{
	public function get_name(): string
	{
		return 'shopchop-product-spotlight';
	}

	public function get_title(): string
	{
		return esc_html__('Product Spotlight', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-featured-image';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['product', 'spotlight', 'highlight', 'featured', 'cta', 'banner'];
	}

	public function has_widget_inner_wrapper(): bool
	{
		return false;
	}

	protected function register_controls(): void
	{
		// ── Content ───────────────────────────────────────────────
		$this->start_controls_section('section_content', [
			'label' => esc_html__('Content', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('badge_text', [
			'label'       => esc_html__('Badge (optional)', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'placeholder' => esc_html__('e.g. Best Seller', 'shopchop-theme-settings'),
			'default'     => esc_html__('Best Seller', 'shopchop-theme-settings'),
		]);

		$this->add_control('heading_text', [
			'label'   => esc_html__('Heading', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Product Name Goes Here', 'shopchop-theme-settings'),
		]);

		$this->add_control('description', [
			'label'   => esc_html__('Description', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::WYSIWYG,
			'default' => esc_html__('Add a short compelling description about this product. Highlight its key benefits and why customers should buy it.', 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();

		// ── CTA ───────────────────────────────────────────────────
		$this->start_controls_section('section_cta', [
			'label' => esc_html__('CTA Button', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('cta_label', [
			'label'   => esc_html__('Button Label', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Shop Now', 'shopchop-theme-settings'),
		]);

		$this->add_control('cta_url', [
			'label'       => esc_html__('Button URL', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::URL,
			'placeholder' => 'https://',
		]);

		$this->end_controls_section();

		// ── Image ─────────────────────────────────────────────────
		$this->start_controls_section('section_image', [
			'label' => esc_html__('Image', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('spotlight_image', [
			'label'   => esc_html__('Image', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
		]);

		$this->end_controls_section();

		// ── Style ─────────────────────────────────────────────────
		$this->start_controls_section('section_style', [
			'label' => esc_html__('Style', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('background', [
			'label'   => esc_html__('Background', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'primary',
			'options' => [
				'primary'   => esc_html__('Primary (brand)', 'shopchop-theme-settings'),
				'secondary' => esc_html__('Secondary', 'shopchop-theme-settings'),
				'neutral'   => esc_html__('Neutral', 'shopchop-theme-settings'),
				'white'     => esc_html__('White', 'shopchop-theme-settings'),
			],
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings   = $this->get_settings_for_display();
		$badge      = $settings['badge_text']    ?? '';
		$heading    = $settings['heading_text']  ?? '';
		$desc       = $settings['description']   ?? '';
		$cta_label  = $settings['cta_label']     ?? '';
		$cta_url    = $settings['cta_url']['url'] ?? '';
		$cta_target = ($settings['cta_url']['is_external'] ?? '') === 'on' ? '_blank' : '_self';
		$cta_norel  = ($settings['cta_url']['nofollow']    ?? '') === 'on' ? 'nofollow' : '';
		$img_url    = $settings['spotlight_image']['url']  ?? '';
		$img_alt    = $settings['spotlight_image']['alt']  ?? esc_html($heading);
		$bg         = $settings['background']    ?? 'primary';
?>

		<div class="shopchop-product-spotlight spotlight-bg-<?php echo esc_attr($bg); ?>">
			<div class="homepage-container">
				<div class="spotlight-inner">

					<!-- Content (left on lg) -->
					<div class="spotlight-content">
						<?php if ($badge) : ?>
							<span class="spotlight-badge"><?php echo esc_html($badge); ?></span>
						<?php endif; ?>

						<?php if ($heading) : ?>
							<h2 class="spotlight-heading"><?php echo esc_html($heading); ?></h2>
						<?php endif; ?>

						<?php if ($desc) : ?>
							<div class="spotlight-description"><?php echo wp_kses_post($desc); ?></div>
						<?php endif; ?>

						<?php if ($cta_label && $cta_url) : ?>
							<a
								href="<?php echo esc_url($cta_url); ?>"
								class="spotlight-cta"
								target="<?php echo esc_attr($cta_target); ?>"
								<?php if ($cta_norel) : ?>rel="<?php echo esc_attr($cta_norel); ?>"<?php endif; ?>
							>
								<?php echo esc_html($cta_label); ?>
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M5 12h14" /><path d="m12 5 7 7-7 7" />
								</svg>
							</a>
						<?php endif; ?>
					</div>

					<!-- Image (right on lg, bottom on mobile) -->
					<?php if ($img_url) : ?>
						<div class="spotlight-image-col group">
							<img
								src="<?php echo esc_url($img_url); ?>"
								alt="<?php echo esc_attr($img_alt); ?>"
								title="<?php echo esc_attr($img_alt); ?>"
								loading="lazy"
								class="spotlight-image"
							/>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>

<?php
	}
}

<?php
if (! defined('ABSPATH')) exit;

class ShopChop_USP_Bar extends \Elementor\Widget_Base
{
	use ShopChop_Section_Style;


	public function get_name(): string
	{
		return 'shopchop-usp-bar';
	}

	public function get_title(): string
	{
		return esc_html__('USP Bar', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-bullet-list';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['usp', 'benefits', 'features', 'trust', 'selling points'];
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
			'default'     => esc_html__('Why Shop With Us', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Why Shop With Us', 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();

		// ── USP Points ───────────────────────────────────────────
		$this->start_controls_section('section_points', [
			'label' => esc_html__('USP Points', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('usp_image', [
			'label'       => esc_html__('Image / Icon', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::MEDIA,
			'default'     => ['url' => \Elementor\Utils::get_placeholder_image_src()],
			'description' => esc_html__('Recommended: SVG or PNG icon, square, min 80×80px.', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('usp_title', [
			'label'       => esc_html__('Title', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => esc_html__('USP Title', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Free Delivery', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('usp_description', [
			'label'       => esc_html__('Description', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'default'     => esc_html__('Short benefit description here.', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Free shipping on orders over RM200.', 'shopchop-theme-settings'),
			'rows'        => 3,
		]);

		$this->add_control('usp_points', [
			'label'       => esc_html__('USP Points', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				['usp_title' => esc_html__('Free Delivery', 'shopchop-theme-settings'),         'usp_description' => esc_html__('Free shipping on orders over RM200.', 'shopchop-theme-settings')],
				['usp_title' => esc_html__('Genuine Products', 'shopchop-theme-settings'),      'usp_description' => esc_html__('All products are 100% authentic.', 'shopchop-theme-settings')],
				['usp_title' => esc_html__('Authorised Distributor', 'shopchop-theme-settings'), 'usp_description' => esc_html__('Official distributor for top brands.', 'shopchop-theme-settings')],
				['usp_title' => esc_html__('Expert Support', 'shopchop-theme-settings'),        'usp_description' => esc_html__('Get advice from our pool specialists.', 'shopchop-theme-settings')],
			],
			'title_field' => '{{{ usp_title }}}',
		]);

		$this->end_controls_section();

		$this->register_section_style_controls(true);
	}

	protected function render(): void
	{
		$settings = $this->get_settings_for_display();
		$heading  = $settings['heading_text'] ?? '';
		$points   = $settings['usp_points']   ?? [];

		if (empty($points)) return;
?>

		<div class="shopchop-usp-bar <?php echo esc_attr($this->section_style_classes($settings)); ?>">
			<div class="homepage-container">
				<?php if ($heading) : ?>
					<h2 class="usp-heading"><?php echo esc_html($heading); ?></h2>
				<?php endif; ?>

				<div class="usp-grid">
					<?php foreach ($points as $point) :
						$img_url     = $point['usp_image']['url'] ?? '';
						$img_alt     = $point['usp_title'] ?? '';
						$title       = $point['usp_title'] ?? '';
						$description = $point['usp_description'] ?? '';
					?>
						<div class="usp-card">

							<?php if ($img_url) : ?>
								<div class="usp-image-wrap">
									<img src="<?php echo esc_url($img_url); ?>"
										alt="<?php echo esc_attr($img_alt); ?>"
										title="<?php echo esc_attr($img_alt); ?>"
										loading="lazy" class="usp-image">
								</div>
							<?php endif; ?>

							<div class="usp-content">
								<?php if ($title) : ?>
									<h3 class="usp-title"><?php echo esc_html($title); ?></h3>
								<?php endif; ?>

								<?php if ($description) : ?>
									<p class="usp-description"><?php echo esc_html($description); ?></p>
								<?php endif; ?>
							</div>

						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

<?php
	}
}

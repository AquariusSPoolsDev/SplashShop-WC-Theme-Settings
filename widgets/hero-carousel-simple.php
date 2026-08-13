<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Hero_Carousel_Simple extends \Elementor\Widget_Base
{
	public function get_name(): string
	{
		return 'shopchop-hero-carousel-simple';
	}

	public function get_title(): string
	{
		return esc_html__('Hero Carousel Simple', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-banner';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['hero', 'banner', 'simple', 'cta'];
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
		// ── Content ───────────────────────────────────────────────
		$this->start_controls_section('section_content', [
			'label' => esc_html__('Content', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('eyebrow_text', [
			'label'   => esc_html__('Eyebrow Text', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Your Tagline Here', 'shopchop-theme-settings'),
		]);

		$this->add_control('heading_text', [
			'label'   => esc_html__('Heading', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__('Your Outcome Strategy', 'shopchop-theme-settings'),
		]);

		$this->add_control('subheading_text', [
			'label'   => esc_html__('Subheading', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Your Supporting Outcome. One line Text.', 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();

		// ── Buttons ───────────────────────────────────────────────
		$this->start_controls_section('section_buttons', [
			'label' => esc_html__('Buttons (max 2)', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('button_text', [
			'label'   => esc_html__('Button Text', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Button', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('button_style', [
			'label'   => esc_html__('Style', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'primary',
			'options' => [
				'primary'   => esc_html__('Primary (filled)', 'shopchop-theme-settings'),
				'secondary' => esc_html__('Secondary (outline)', 'shopchop-theme-settings'),
			],
		]);

		$repeater->add_control('button_link', [
			'label'         => esc_html__('Link', 'shopchop-theme-settings'),
			'type'          => \Elementor\Controls_Manager::URL,
			'placeholder'   => 'https://',
			'show_external' => true,
		]);

		$this->add_control('buttons', [
			'label'       => esc_html__('Buttons', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'button_text'  => esc_html__('Button #1', 'shopchop-theme-settings'),
					'button_style' => 'primary',
				],
				[
					'button_text'  => esc_html__('Button #2', 'shopchop-theme-settings'),
					'button_style' => 'secondary',
				],
			],
			'title_field' => '{{{ button_text || "Button" }}}',
			'max_items'   => 2,
		]);

		$this->end_controls_section();

		// ── Background ────────────────────────────────────────────
		$this->start_controls_section('section_background', [
			'label' => esc_html__('Background', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('background_image', [
			'label'       => esc_html__('Background Image', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::MEDIA,
			'default'     => ['url' => \Elementor\Utils::get_placeholder_image_src()],
			'description' => esc_html__('Blur, overlay, and overflow are handled automatically.', 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings   = $this->get_settings_for_display();
		$eyebrow    = $settings['eyebrow_text']    ?? '';
		$heading    = $settings['heading_text']    ?? '';
		$subheading = $settings['subheading_text'] ?? '';
		$buttons    = $settings['buttons']         ?? [];
		$bg_url     = $settings['background_image']['url'] ?? '';
?>

		<div class="shopchop-hero-simple">
			<?php if ($bg_url) : ?>
				<div class="hero-simple-bg" aria-hidden="true">
					<img src="<?php echo esc_url($bg_url); ?>" alt="" class="hero-simple-bg-img" loading="eager">
				</div>
			<?php endif; ?>

			<div class="hero-simple-overlay" aria-hidden="true"></div>

			<div class="homepage-container">
				<div class="hero-simple-inner">

					<?php if ($eyebrow) : ?>
						<span class="hero-simple-eyebrow"><?php echo esc_html($eyebrow); ?></span>
					<?php endif; ?>

					<?php if ($heading) : ?>
						<h1 class="hero-simple-heading"><?php echo esc_html($heading); ?></h1>
					<?php endif; ?>

					<?php if ($subheading) : ?>
						<p class="hero-simple-subheading"><?php echo esc_html($subheading); ?></p>
					<?php endif; ?>

					<?php if (! empty($buttons)) : ?>
						<div class="hero-simple-buttons">
							<?php foreach (array_slice($buttons, 0, 2) as $button) :
								$text  = $button['button_text']  ?? '';
								$style = $button['button_style'] ?? 'primary';
								$link  = $button['button_link']  ?? [];
								$url   = $link['url'] ?? '';
								if (! $text || ! $url) continue;
							?>
								<a
									href="<?php echo esc_url($url); ?>"
									class="hero-simple-button hero-simple-button-<?php echo esc_attr($style); ?>"
									<?php echo ! empty($link['is_external']) ? 'target="_blank"' : ''; ?>
									<?php echo ! empty($link['nofollow']) ? 'rel="nofollow"' : ''; ?>
								>
									<?php echo esc_html($text); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>

<?php
	}
}

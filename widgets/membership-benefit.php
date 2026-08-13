<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Membership_Benefit extends \Elementor\Widget_Base
{
	public function get_name(): string
	{
		return 'shopchop-membership-benefit';
	}

	public function get_title(): string
	{
		return esc_html__('New Membership Benefit', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-badge';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['membership', 'benefit', 'signup', 'offer', 'account'];
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

		$this->add_control('pill_text', [
			'label'   => esc_html__('Top Text Pill', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('New Member Offer', 'shopchop-theme-settings'),
		]);

		$this->add_control('heading_text', [
			'label'   => esc_html__('Heading', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__('Your Heading here', 'shopchop-theme-settings'),
		]);

		$this->add_control('subheading_text', [
			'label'   => esc_html__('Subheading', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__('Your Subheading here', 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();

		// ── Benefit Points ──────────────────────────────────────────
		$this->start_controls_section('section_benefits', [
			'label' => esc_html__('Benefit Points', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('benefit_text', [
			'label'   => esc_html__('Benefit Text', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::WYSIWYG,
			'default' => esc_html__('Benefit point goes here', 'shopchop-theme-settings'),
		]);

		$this->add_control('benefit_points', [
			'label'       => esc_html__('Benefit Points', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				['benefit_text' => esc_html__('Benefit point one', 'shopchop-theme-settings')],
				['benefit_text' => esc_html__('Benefit point two', 'shopchop-theme-settings')],
				['benefit_text' => esc_html__('Benefit point three', 'shopchop-theme-settings')],
			],
			'title_field' => '{{{ benefit_text || "Benefit" }}}',
			'max_items'   => 3,
		]);

		$this->end_controls_section();

		// ── Button ──────────────────────────────────────────────────
		$this->start_controls_section('section_button', [
			'label' => esc_html__('Button', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('button_text', [
			'label'   => esc_html__('Button Text', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Button Text', 'shopchop-theme-settings'),
		]);

		$this->add_control('button_link', [
			'label'       => esc_html__('Button Link', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::URL,
			'placeholder' => 'https://',
		]);

		$this->add_control('button_note', [
			'label'   => esc_html__('Small Text Under Button', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__("It's free. No extra cost.", 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();

		// ── Image ─────────────────────────────────────────────────
		$this->start_controls_section('section_image', [
			'label' => esc_html__('Image', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('benefit_image', [
			'label'   => esc_html__('Image', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings     = $this->get_settings_for_display();
		$pill         = $settings['pill_text']        ?? '';
		$heading      = $settings['heading_text']     ?? '';
		$subheading   = $settings['subheading_text']  ?? '';
		$benefits     = $settings['benefit_points']   ?? [];
		$button_text  = $settings['button_text']      ?? '';
		$button_url   = $settings['button_link']['url'] ?? '';
		$button_target = ($settings['button_link']['is_external'] ?? '') === 'on' ? '_blank' : '_self';
		$button_norel  = ($settings['button_link']['nofollow']    ?? '') === 'on' ? 'nofollow' : '';
		$button_note  = $settings['button_note']      ?? '';
		$img_url      = $settings['benefit_image']['url'] ?? '';
		$img_alt      = $settings['benefit_image']['alt'] ?? esc_html($heading);
?>

		<div class="shopchop-membership-benefit">
			<div class="homepage-container">
				<div class="membership-benefit-inner">
					<div class="membership-benefit-content">
						<div class="membership-above-fold">
							<?php if ($pill) : ?>
								<span class="membership-benefit-pill"><?php echo esc_html($pill); ?></span>
							<?php endif; ?>

							<?php if ($heading) : ?>
								<h2 class="membership-benefit-heading"><?php echo esc_html($heading); ?></h2>
							<?php endif; ?>

							<?php if ($subheading) : ?>
								<p class="membership-benefit-subheading"><?php echo esc_html($subheading); ?></p>
							<?php endif; ?>
						</div>
						
						<?php if (! empty($benefits)) : ?>
							<div class="membership-benefits">
								<ul class="membership-benefit-list">
									<?php foreach (array_slice($benefits, 0, 3) as $benefit) :
										$text = $benefit['benefit_text'] ?? '';
										if (! $text) continue;
									?>
										<li class="membership-benefit-item">
											<svg class="benefit-item-bullet" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor">
												<path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"></path>
											</svg>
											<span class="benefit-item-text"><?php echo wp_kses_post($text); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<div class="membership-button-support">
							<?php if ($button_text && $button_url) : ?>
								<a
									href="<?php echo esc_url($button_url); ?>"
									class="membership-benefit-button"
									target="<?php echo esc_attr($button_target); ?>"
									<?php if ($button_norel) : ?>rel="<?php echo esc_attr($button_norel); ?>"<?php endif; ?>
								>
									<?php echo esc_html($button_text); ?>
								</a>
							<?php endif; ?>

							<?php if ($button_note) : ?>
								<span class="membership-benefit-note"><?php echo esc_html($button_note); ?></span>
							<?php endif; ?>	
						</div>
					</div>

					<?php if ($img_url) : ?>
						<div class="membership-benefit-image-col">
							<img
								src="<?php echo esc_url($img_url); ?>"
								alt="<?php echo esc_attr($img_alt); ?>"
								title="<?php echo esc_attr($img_alt); ?>"
								loading="lazy"
								class="membership-benefit-image"
							/>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>

<?php
	}
}

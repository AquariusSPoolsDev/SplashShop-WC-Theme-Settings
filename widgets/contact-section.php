<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Contact_Section extends \Elementor\Widget_Base
{
	public function get_name(): string
	{
		return 'shopchop-contact-section';
	}

	public function get_title(): string
	{
		return esc_html__('Contact Section', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-call-to-action';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['contact', 'whatsapp', 'email', 'support', 'cta'];
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

		$this->add_control('heading_text', [
			'label'   => esc_html__('Heading', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Got anything to say?', 'shopchop-theme-settings'),
		]);

		$this->add_control('subheading_text', [
			'label'   => esc_html__('Subheading', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Contact us to get your answers', 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();

		// ── Primary Contact ─────────────────────────────────────────
		$this->start_controls_section('section_primary', [
			'label' => esc_html__('Primary Contact', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('primary_icon', [
			'label'   => esc_html__('Icon', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::ICONS,
			'default' => [
				'value'   => 'fab fa-whatsapp',
				'library' => 'fa-brands',
			],
		]);

		$this->add_control('primary_text', [
			'label'   => esc_html__('Button Text', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Primary Contact Option', 'shopchop-theme-settings'),
		]);

		$this->add_control('primary_supporting_text', [
			'label'   => esc_html__('Supporting Text', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Contact Details are required', 'shopchop-theme-settings'),
		]);

		$this->add_control('primary_link', [
			'label'         => esc_html__('Link', 'shopchop-theme-settings'),
			'type'          => \Elementor\Controls_Manager::URL,
			'placeholder'   => 'https://',
			'show_external' => true,
		]);

		$this->end_controls_section();

		// ── Secondary Contacts ──────────────────────────────────────
		$this->start_controls_section('section_secondary', [
			'label' => esc_html__('Secondary Contacts', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('contact_icon', [
			'label'   => esc_html__('Icon', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::ICONS,
			'default' => [
				'value'   => 'fas fa-envelope',
				'library' => 'fa-solid',
			],
		]);

		$repeater->add_control('contact_text', [
			'label'   => esc_html__('Text', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Contact', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('contact_link', [
			'label'         => esc_html__('Link', 'shopchop-theme-settings'),
			'type'          => \Elementor\Controls_Manager::URL,
			'placeholder'   => 'https://',
			'show_external' => true,
		]);

		$this->add_control('secondary_contacts', [
			'label'       => esc_html__('Contacts', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'contact_text' => esc_html__('Contact #1', 'shopchop-theme-settings'),
					'contact_icon' => ['value' => 'fas fa-envelope', 'library' => 'fa-solid'],
				],
				[
					'contact_text' => esc_html__('Contact #2', 'shopchop-theme-settings'),
					'contact_icon' => ['value' => 'fas fa-map-marker-alt', 'library' => 'fa-solid'],
				],
			],
			'title_field' => '{{{ contact_text || "Contact" }}}',
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
			'description' => esc_html__('Optional. Overflow is handled automatically.', 'shopchop-theme-settings'),
		]);

		$this->add_control('background_blur', [
			'label'      => esc_html__('Background Blur', 'shopchop-theme-settings'),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => ['px'],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 40,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 4,
			],
			'condition'  => [
				'background_image[url]!' => '',
			],
		]);

		$this->add_control('background_opacity', [
			'label'     => esc_html__('Background Opacity', 'shopchop-theme-settings'),
			'type'      => \Elementor\Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'default'   => [
				'unit' => 'px',
				'size' => 25,
			],
			'condition' => [
				'background_image[url]!' => '',
			],
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings      = $this->get_settings_for_display();
		$heading       = $settings['heading_text']              ?? '';
		$subheading    = $settings['subheading_text']           ?? '';
		$primary_icon  = $settings['primary_icon']               ?? [];
		$primary_text  = $settings['primary_text']               ?? '';
		$primary_sub   = $settings['primary_supporting_text']    ?? '';
		$primary_link  = $settings['primary_link']               ?? [];
		$primary_url   = $primary_link['url']                    ?? '';
		$contacts      = $settings['secondary_contacts']         ?? [];
		$bg_url        = $settings['background_image']['url']    ?? '';
		$bg_blur       = $settings['background_blur']['size']    ?? 4;
		$bg_opacity    = $settings['background_opacity']['size']  ?? 25;
		$bg_style      = sprintf('filter:blur(%dpx);opacity:%s%%;', (int) $bg_blur, esc_attr($bg_opacity));
?>

		<div class="shopchop-contact-section">
			<?php if ($bg_url) : ?>
				<div class="contact-section-bg" aria-hidden="true">
					<img src="<?php echo esc_url($bg_url); ?>" alt="" class="contact-section-bg-img" loading="lazy" style="<?php echo esc_attr($bg_style); ?>">
				</div>
			<?php endif; ?>

			<div class="homepage-container">
				<div class="contact-section-inner">

					<?php if ($heading) : ?>
						<h2 class="contact-section-heading"><?php echo esc_html($heading); ?></h2>
					<?php endif; ?>

					<?php if ($subheading) : ?>
						<p class="contact-section-subheading"><?php echo esc_html($subheading); ?></p>
					<?php endif; ?>

					<?php if ($primary_text && $primary_url) : ?>
						<a
							href="<?php echo esc_url($primary_url); ?>"
							class="contact-section-primary-button"
							<?php echo ! empty($primary_link['is_external']) ? 'target="_blank"' : ''; ?>
							<?php echo ! empty($primary_link['nofollow']) ? 'rel="nofollow"' : ''; ?>
						>
							<?php if (! empty($primary_icon['value'])) : ?>
								<?php \Elementor\Icons_Manager::render_icon($primary_icon, ['aria-hidden' => 'true']); ?>
							<?php endif; ?>
							<?php echo esc_html($primary_text); ?>
						</a>
					<?php endif; ?>

					<?php if ($primary_sub) : ?>
						<p class="contact-section-primary-note"><?php echo esc_html($primary_sub); ?></p>
					<?php endif; ?>

					<?php if (! empty($contacts)) : ?>
						<div class="contact-section-secondary-grid">
							<?php foreach (array_slice($contacts, 0, 2) as $contact) :
								$text = $contact['contact_text'] ?? '';
								$icon = $contact['contact_icon'] ?? [];
								$link = $contact['contact_link'] ?? [];
								$url  = $link['url'] ?? '';
								if (! $text || ! $url) continue;
							?>
								<a
									href="<?php echo esc_url($url); ?>"
									class="contact-section-secondary-button"
									<?php echo ! empty($link['is_external']) ? 'target="_blank"' : ''; ?>
									<?php echo ! empty($link['nofollow']) ? 'rel="nofollow"' : ''; ?>
								>
									<?php if (! empty($icon['value'])) : ?>
										<?php \Elementor\Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
									<?php endif; ?>
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

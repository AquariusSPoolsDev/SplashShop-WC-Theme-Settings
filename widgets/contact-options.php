<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Contact_Options extends \Elementor\Widget_Base
{

	public function get_name(): string
	{
		return 'shopchop-contact-options';
	}

	public function get_title(): string
	{
		return esc_html__('Contact Options', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-time-line';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['contact', 'whatsapp', 'email', 'cards', 'reply time'];
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
			'default'     => esc_html__('Contact Us Directly', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Contact Us Directly', 'shopchop-theme-settings'),
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

		// ── Options ──────────────────────────────────────────────
		$this->start_controls_section('section_options', [
			'label' => esc_html__('Contact Options (max 2)', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('option_icon', [
			'label'   => esc_html__('Icon', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::ICONS,
			'default' => [
				'value'   => 'fas fa-comment',
				'library' => 'fa-solid',
			],
		]);

		$repeater->add_control('option_color', [
			'label'   => esc_html__('Accent Color', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'primary',
			'options' => [
				'primary'   => esc_html__('Primary (brand)', 'shopchop-theme-settings'),
				'success'   => esc_html__('Success', 'shopchop-theme-settings'),
				'secondary' => esc_html__('Secondary', 'shopchop-theme-settings'),
				'warning'   => esc_html__('Warning', 'shopchop-theme-settings'),
				'neutral'   => esc_html__('Neutral', 'shopchop-theme-settings'),
			],
		]);

		$repeater->add_control('option_badge', [
			'label'       => esc_html__('Reply Time Badge', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => esc_html__('Within 1 hour', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Within 1 hour', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('option_title', [
			'label'       => esc_html__('Title', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => esc_html__('WhatsApp', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. WhatsApp', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('option_description', [
			'label'       => esc_html__('Description', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'default'     => esc_html__('We reply to most questions within an hour during business hours.', 'shopchop-theme-settings'),
			'rows'        => 3,
		]);

		$repeater->add_control('option_link_text', [
			'label'       => esc_html__('Link Text', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => esc_html__('Chat on WhatsApp', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Chat on WhatsApp', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('option_link', [
			'label'         => esc_html__('Link', 'shopchop-theme-settings'),
			'type'          => \Elementor\Controls_Manager::URL,
			'placeholder'   => esc_html__('https://wa.me/60XXXXXXXXX or mailto:hello@yourstore.com', 'shopchop-theme-settings'),
			'show_external' => true,
			'default'       => [
				'url'         => '',
				'is_external' => false,
				'nofollow'    => false,
			],
		]);

		$this->add_control('contact_options', [
			'label'         => esc_html__('Options', 'shopchop-theme-settings'),
			'type'          => \Elementor\Controls_Manager::REPEATER,
			'fields'        => $repeater->get_controls(),
			'max_items'     => 2,
			'prevent_empty' => true,
			'default'       => [
				[
					'option_title'       => esc_html__('WhatsApp', 'shopchop-theme-settings'),
					'option_description' => esc_html__('We reply to most questions within an hour during business hours.', 'shopchop-theme-settings'),
					'option_badge'       => esc_html__('Within 1 hour', 'shopchop-theme-settings'),
					'option_link_text'   => esc_html__('Chat on WhatsApp', 'shopchop-theme-settings'),
					'option_color'       => 'success',
					'option_icon'        => ['value' => 'fab fa-whatsapp', 'library' => 'fa-brands'],
				],
				[
					'option_title'       => esc_html__('Email', 'shopchop-theme-settings'),
					'option_description' => esc_html__("We'll get back to you within one business day.", 'shopchop-theme-settings'),
					'option_badge'       => esc_html__('Within 1 day', 'shopchop-theme-settings'),
					'option_link_text'   => esc_html__('Send an email', 'shopchop-theme-settings'),
					'option_color'       => 'primary',
					'option_icon'        => ['value' => 'fas fa-envelope', 'library' => 'fa-solid'],
				],
			],
			'title_field'   => '{{{ option_title }}}',
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings = $this->get_settings_for_display();
		$heading   = $settings['heading_text']      ?? '';
		$transform = $settings['heading_transform'] ?? 'normal';
		$options   = $settings['contact_options']   ?? [];
		$heading_class = 'contact-options-heading' . ($transform === 'uppercase' ? ' shopchop-heading-uppercase' : '');

		if (empty($options)) return;

		// Hard cap at 2 regardless of saved data (e.g. imported templates).
		$options = array_slice($options, 0, 2);
		$count   = count($options);
?>

		<div class="shopchop-contact-options">
			<div class="">
				<?php if ($heading) : ?>
					<h2 class="<?php echo esc_attr($heading_class); ?>"><?php echo esc_html($heading); ?></h2>
				<?php endif; ?>

				<div class="contact-options-grid contact-options-cols-<?php echo esc_attr($count); ?>">
					<?php foreach ($options as $option) :
						$title    = $option['option_title']       ?? '';
						$desc     = $option['option_description'] ?? '';
						$badge    = $option['option_badge']       ?? '';
						$icon     = $option['option_icon']        ?? [];
						$has_icon = ! empty($icon['value']);
						$color    = $option['option_color']       ?? 'primary';
						$link_text = $option['option_link_text']  ?? '';
						$link     = $option['option_link']        ?? [];
						$url      = $link['url'] ?? '';
					?>
						<div class="contact-options-card contact-options-color-<?php echo esc_attr($color); ?> group">

							<div class="contact-options-card-top">
								<?php if ($has_icon) : ?>
									<span class="contact-options-icon-wrap">
										<?php \Elementor\Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
									</span>
								<?php endif; ?>

								<?php if ($badge) : ?>
									<span class="contact-options-badge"><?php echo esc_html($badge); ?></span>
								<?php endif; ?>
							</div>

							<?php if ($title) : ?>
								<h3 class="contact-options-title">
									<?php if ($url) : ?>
										<a href="<?php echo esc_url($url); ?>"
											<?php echo ! empty($link['is_external']) ? 'target="_blank"' : ''; ?>
											<?php echo ! empty($link['nofollow']) ? 'rel="nofollow"' : ''; ?>
											class="contact-options-title-link"><?php echo esc_html($title); ?></a>
									<?php else : ?>
										<?php echo esc_html($title); ?>
									<?php endif; ?>
								</h3>
							<?php endif; ?>

							<?php if ($desc) : ?>
								<p class="contact-options-description"><?php echo esc_html($desc); ?></p>
							<?php endif; ?>

							<?php if ($url && $link_text) : ?>
								<a href="<?php echo esc_url($url); ?>"
									<?php echo ! empty($link['is_external']) ? 'target="_blank"' : ''; ?>
									<?php echo ! empty($link['nofollow']) ? 'rel="nofollow"' : ''; ?>
									class="contact-options-link">
									<?php echo esc_html($link_text); ?>
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"></path></svg>
								</a>
							<?php endif; ?>

						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

<?php
	}
}

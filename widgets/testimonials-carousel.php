<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Testimonials_Carousel extends \Elementor\Widget_Base
{
	public function get_name(): string
	{
		return 'shopchop-testimonials-carousel';
	}

	public function get_title(): string
	{
		return esc_html__('Testimonials Carousel', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-testimonial-carousel';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['testimonial', 'review', 'carousel', 'slider', 'quote', 'customer'];
	}

	public function has_widget_inner_wrapper(): bool
	{
		return false;
	}

	protected function register_controls(): void
	{
		// ── Header ────────────────────────────────────────────────
		$this->start_controls_section('section_header', [
			'label' => esc_html__('Header', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('heading_text', [
			'label'   => esc_html__('Heading', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Reviews', 'shopchop-theme-settings'),
		]);

		$this->add_control('description', [
			'label'   => esc_html__('Description', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__('What Our Customers Say', 'shopchop-theme-settings'),
			'rows'    => 2,
		]);

		$this->end_controls_section();

		// ── Source ────────────────────────────────────────────────
		$this->start_controls_section('section_source', [
			'label' => esc_html__('Reviews Source', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('source', [
			'label'   => esc_html__('Source', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'manual',
			'options' => [
				'manual' => esc_html__('Manual Input', 'shopchop-theme-settings'),
				'wc'     => esc_html__('WooCommerce Reviews', 'shopchop-theme-settings'),
			],
		]);

		// ── WooCommerce options ───────────────────────────────────
		$this->add_control('wc_max', [
			'label'     => esc_html__('Max Reviews', 'shopchop-theme-settings'),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => 8,
			'min'       => 2,
			'max'       => 24,
			'condition' => ['source' => 'wc'],
		]);

		$this->add_control('wc_min_rating', [
			'label'     => esc_html__('Minimum Rating', 'shopchop-theme-settings'),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => '4',
			'options'   => [
				'1' => esc_html__('1★ and above', 'shopchop-theme-settings'),
				'2' => esc_html__('2★ and above', 'shopchop-theme-settings'),
				'3' => esc_html__('3★ and above', 'shopchop-theme-settings'),
				'4' => esc_html__('4★ and above', 'shopchop-theme-settings'),
				'5' => esc_html__('5★ only', 'shopchop-theme-settings'),
			],
			'condition' => ['source' => 'wc'],
		]);

		// ── Manual repeater ───────────────────────────────────────
		$repeater = new \Elementor\Repeater();

		$repeater->add_control('quote', [
			'label'   => esc_html__('Quote', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 4,
			'default' => esc_html__('Insert some customer\s testimonial here...', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('reviewer_name', [
			'label'   => esc_html__('Name', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Ali Abu', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('reviewer_location', [
			'label'       => esc_html__('Location / Label', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'placeholder' => esc_html__('e.g. Johor Bahru or Verified Buyer', 'shopchop-theme-settings'),
			'default'     => esc_html__('Verified Buyer', 'shopchop-theme-settings'),
		]);

		$repeater->add_control('rating', [
			'label'   => esc_html__('Star Rating', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '5',
			'options' => [
				'5' => '★★★★★ (5)',
				'4' => '★★★★☆ (4)',
				'3' => '★★★☆☆ (3)',
				'2' => '★★☆☆☆ (2)',
				'1' => '★☆☆☆☆ (1)',
			],
		]);

		$repeater->add_control('avatar', [
			'label' => esc_html__('Avatar (optional)', 'shopchop-theme-settings'),
			'type'  => \Elementor\Controls_Manager::MEDIA,
		]);

		$this->add_control('testimonials', [
			'label'       => esc_html__('Testimonials', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'quote'             => esc_html__('Insert some customer\s testimonial here...', 'shopchop-theme-settings'),
					'reviewer_name'     => esc_html__('Ali Abu', 'shopchop-theme-settings'),
					'reviewer_location' => esc_html__('Verified Buyer', 'shopchop-theme-settings'),
					'rating'            => '5',
				],
			],
			'title_field' => '{{{ reviewer_name }}}',
			'condition'   => ['source' => 'manual'],
		]);

		$this->end_controls_section();

		// ── Carousel ─────────────────────────────────────────────
		$this->start_controls_section('section_carousel', [
			'label' => esc_html__('Carousel', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('autoplay', [
			'label'        => esc_html__('Autoplay', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		]);

		$this->add_control('autoplay_delay', [
			'label'     => esc_html__('Autoplay Delay (ms)', 'shopchop-theme-settings'),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => 5000,
			'min'       => 1000,
			'max'       => 10000,
			'step'      => 500,
			'condition' => ['autoplay' => 'yes'],
		]);

		$this->add_control('loop', [
			'label'        => esc_html__('Loop', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		]);

		$this->add_control('show_arrows', [
			'label'        => esc_html__('Show Arrows', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
		]);


		$this->end_controls_section();
	}

	private function render_stars(int $rating): string
	{
		$stars = '';
		for ($i = 1; $i <= 5; $i++) {
			$stars .= $i <= $rating
				? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>'
				: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
		}
		return $stars;
	}

	private function get_wc_reviews(int $max, int $min_rating): array
	{
		$comments = get_comments([
			'type'       => 'review',
			'status'     => 'approve',
			'number'     => $max * 3,
			'meta_query' => [
				[
					'key'     => 'rating',
					'value'   => $min_rating,
					'compare' => '>=',
					'type'    => 'NUMERIC',
				],
			],
		]);

		$reviews = [];
		foreach ($comments as $comment) {
			$rating = (int) get_comment_meta($comment->comment_ID, 'rating', true);
			if ($rating < $min_rating) continue;
			$reviews[] = [
				'quote'             => $comment->comment_content,
				'reviewer_name'     => $comment->comment_author,
				'reviewer_location' => 'Verified Buyer',
				'rating'            => $rating,
				'avatar_url'        => get_avatar_url($comment->comment_author_email, ['size' => 64]),
			];
			if (count($reviews) >= $max) break;
		}

		return $reviews;
	}

	protected function render(): void
	{
		$settings   = $this->get_settings_for_display();
		$heading    = $settings['heading_text'] ?? '';
		$desc       = $settings['description']  ?? '';
		$source     = $settings['source']       ?? 'manual';
		$autoplay   = $settings['autoplay']        === 'yes';
		$loop       = $settings['loop']            === 'yes';
		$arrows     = $settings['show_arrows'] === 'yes';
		$delay      = (int) ($settings['autoplay_delay'] ?? 5000);

		if ($source === 'wc') {
			$max        = (int) ($settings['wc_max'] ?? 8);
			$min_rating = (int) ($settings['wc_min_rating'] ?? 4);
			$reviews    = $this->get_wc_reviews($max, $min_rating);
		} else {
			$raw     = $settings['testimonials'] ?? [];
			$reviews = array_map(fn($item) => [
				'quote'             => $item['quote']             ?? '',
				'reviewer_name'     => $item['reviewer_name']     ?? '',
				'reviewer_location' => $item['reviewer_location'] ?? '',
				'rating'            => (int) ($item['rating']     ?? 5),
				'avatar_url'        => $item['avatar']['url']     ?? '',
			], $raw);
		}

		if (empty($reviews)) return;

		$swiper_config = wp_json_encode([
			'slidesPerView'  => 1,
			'spaceBetween'   => 16,
			'loop'           => $loop,
			'autoplay'       => $autoplay ? ['delay' => $delay, 'disableOnInteraction' => false] : false,
			'pagination'     => false,
			'navigation'     => $arrows ? ['nextEl' => '.swiper-button-next', 'prevEl' => '.swiper-button-prev'] : false,
			'breakpoints'    => [
				768  => ['slidesPerView' => 2, 'spaceBetween' => 20],
				1024 => ['slidesPerView' => 3, 'spaceBetween' => 24],
				1280 => ['slidesPerView' => 4, 'spaceBetween' => 24],
			],
		]);
?>

		<div class="shopchop-testimonials-carousel">
			<div class="homepage-container">

				<?php if ($heading || $desc) : ?>
					<div class="testimonials-header">
						<?php if ($heading) : ?>
							<h2 class="testimonials-heading"><?php echo esc_html($heading); ?></h2>
						<?php endif; ?>
						<?php if ($desc) : ?>
							<p class="testimonials-description"><?php echo esc_html($desc); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="swiper shopchop-testimonials-swiper" data-swiper="<?php echo esc_attr($swiper_config); ?>">
					<div class="swiper-wrapper">

						<?php foreach ($reviews as $review) :
							$rating     = min(5, max(1, (int) $review['rating']));
							$avatar_url = $review['avatar_url'] ?? '';
						?>
							<div class="swiper-slide">
								<div class="testimonial-card">
									<div class="testimonial-content">
										<div class="testimonial-stars">
											<?php echo $this->render_stars($rating); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										</div>

										<blockquote class="testimonial-quote">
											<?php echo esc_html($review['quote']); ?>
										</blockquote>
									</div>
									

									<div class="testimonial-author">
										<?php if ($avatar_url) : ?>
											<img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($review['reviewer_name']); ?>" loading="lazy" class="testimonial-avatar" />
										<?php else : ?>
											<div class="testimonial-avatar-placeholder">
												<?php echo esc_html(mb_substr($review['reviewer_name'], 0, 1)); ?>
											</div>
										<?php endif; ?>
										<div class="testimonial-author-info">
											<span class="testimonial-name"><?php echo esc_html($review['reviewer_name']); ?></span>
											<?php if (! empty($review['reviewer_location'])) : ?>
												<span class="testimonial-location"><?php echo esc_html($review['reviewer_location']); ?></span>
											<?php endif; ?>
										</div>
									</div>

								</div>
							</div>
						<?php endforeach; ?>

					</div><!-- .swiper-wrapper -->

	
					<?php if ($arrows) : ?>
						<div class="swiper-button-prev" role="button" title="<?php esc_attr_e('Previous testimonial', 'shopchop-theme-settings'); ?>" aria-label="<?php esc_attr_e('Previous testimonial', 'shopchop-theme-settings'); ?>"></div>
						<div class="swiper-button-next" role="button" title="<?php esc_attr_e('Next testimonial', 'shopchop-theme-settings'); ?>" aria-label="<?php esc_attr_e('Next testimonial', 'shopchop-theme-settings'); ?>"></div>
					<?php endif; ?>

				</div><!-- .swiper -->

			</div>
		</div>

<?php
	}
}

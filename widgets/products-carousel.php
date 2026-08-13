<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Products_Carousel extends \Elementor\Widget_Base
{

	public function get_name(): string
	{
		return 'shopchop-products-carousel';
	}

	public function get_title(): string
	{
		return esc_html__('Products Carousel', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-products';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['products', 'carousel', 'slider', 'woocommerce', 'featured', 'sale'];
	}

	public function has_widget_inner_wrapper(): bool
	{
		return false;
	}

	protected function is_dynamic_content(): bool
	{
		return true;
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
			'default'     => esc_html__('Featured Products', 'shopchop-theme-settings'),
			'placeholder' => esc_html__('e.g. Featured Products', 'shopchop-theme-settings'),
		]);

		$this->add_control('description', [
			'label'   => esc_html__('Description', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => '',
			'rows'    => 2,
		]);

		$this->end_controls_section();

		// ── Query ────────────────────────────────────────────────
		$this->start_controls_section('section_query', [
			'label' => esc_html__('Query', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('query_type', [
			'label'   => esc_html__('Show Products', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'featured',
			'options' => [
				'featured'   => esc_html__('Featured Products', 'shopchop-theme-settings'),
				'on_sale'    => esc_html__('On Sale', 'shopchop-theme-settings'),
				'new'        => esc_html__('New Arrivals (by date)', 'shopchop-theme-settings'),
				'category'   => esc_html__('By Category', 'shopchop-theme-settings'),
				'tag'        => esc_html__('By Tag', 'shopchop-theme-settings'),
				'handpicked' => esc_html__('Hand-picked', 'shopchop-theme-settings'),
			],
		]);

		// Category picker
		$categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
		$cat_options = ['' => esc_html__('— Select Category —', 'shopchop-theme-settings')];
		if (! is_wp_error($categories)) {
			foreach ($categories as $cat) {
				$cat_options[$cat->slug] = $cat->name;
			}
		}

		$this->add_control('product_category', [
			'label'     => esc_html__('Category', 'shopchop-theme-settings'),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'options'   => $cat_options,
			'condition' => ['query_type' => 'category'],
		]);

		$this->add_control('product_tag', [
			'label'       => esc_html__('Tag Slug', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'placeholder' => esc_html__('e.g. pool-pump', 'shopchop-theme-settings'),
			'condition'   => ['query_type' => 'tag'],
		]);

		$this->add_control('handpicked_ids', [
			'label'       => esc_html__('Product IDs', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'placeholder' => esc_html__('e.g. 12,34,56', 'shopchop-theme-settings'),
			'description' => esc_html__('Comma-separated product IDs. Find IDs in WP Admin → Products.', 'shopchop-theme-settings'),
			'condition'   => ['query_type' => 'handpicked'],
		]);

		$this->add_control('product_limit', [
			'label'   => esc_html__('Max Products', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 8,
			'min'     => 2,
			'max'     => 24,
		]);

		$this->add_control('hide_out_of_stock', [
			'label'        => esc_html__('Hide Out of Stock', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
		]);

		$this->end_controls_section();

		// ── Show All Link ─────────────────────────────────────────
		$this->start_controls_section('section_show_all', [
			'label' => esc_html__('Show All Link', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('show_all_label', [
			'label'   => esc_html__('Button Label', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('View All', 'shopchop-theme-settings'),
		]);

		$this->add_control('show_all_url_mode', [
			'label'   => esc_html__('Link URL', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'auto',
			'options' => [
				'auto'   => esc_html__('Auto (based on query type)', 'shopchop-theme-settings'),
				'custom' => esc_html__('Custom URL', 'shopchop-theme-settings'),
				'none'   => esc_html__('Hide button', 'shopchop-theme-settings'),
			],
		]);

		$this->add_control('show_all_custom_url', [
			'label'       => esc_html__('Custom URL', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::URL,
			'placeholder' => 'https://',
			'condition'   => ['show_all_url_mode' => 'custom'],
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
			'default'      => '',
		]);

		$this->add_control('autoplay_delay', [
			'label'     => esc_html__('Autoplay Delay (ms)', 'shopchop-theme-settings'),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => 4000,
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
			'default'      => 'yes',
		]);

		$this->add_control('show_pagination', [
			'label'        => esc_html__('Show Dots', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
		]);

		$this->end_controls_section();
	}

	private function get_show_all_url(array $settings): string
	{
		$mode       = $settings['show_all_url_mode'] ?? 'auto';
		$query_type = $settings['query_type'] ?? 'featured';

		if ($mode === 'none') return '';

		if ($mode === 'custom') {
			return $settings['show_all_custom_url']['url'] ?? '';
		}

		// auto
		switch ($query_type) {
			case 'featured':
				return add_query_arg('filter_featured', '1', wc_get_page_permalink('shop'));
			case 'on_sale':
				return add_query_arg('on_sale', '1', wc_get_page_permalink('shop'));
			case 'new':
				return add_query_arg('orderby', 'date', wc_get_page_permalink('shop'));
			case 'category':
				$slug = $settings['product_category'] ?? '';
				if ($slug) {
					$term = get_term_by('slug', $slug, 'product_cat');
					if ($term) return get_term_link($term);
				}
				return wc_get_page_permalink('shop');
			case 'tag':
				$slug = $settings['product_tag'] ?? '';
				if ($slug) {
					$term = get_term_by('slug', $slug, 'product_tag');
					if ($term) return get_term_link($term);
				}
				return wc_get_page_permalink('shop');
			default:
				return wc_get_page_permalink('shop');
		}
	}

	private function build_query(array $settings): \WP_Query
	{
		$query_type      = $settings['query_type']       ?? 'featured';
		$limit           = (int) ($settings['product_limit'] ?? 8);
		$hide_oos        = $settings['hide_out_of_stock'] === 'yes';

		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		if ($hide_oos) {
			$args['meta_query'][] = [
				'key'     => '_stock_status',
				'value'   => 'instock',
				'compare' => '=',
			];
		}

		switch ($query_type) {
			case 'featured':
				$args['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
				];
				break;

			case 'on_sale':
				$args['post__in'] = array_merge([0], wc_get_product_ids_on_sale());
				$args['orderby']  = 'rand';
				break;

			case 'new':
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;

			case 'category':
				$slug = $settings['product_category'] ?? '';
				if ($slug) {
					$args['tax_query'][] = [
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $slug,
					];
				}
				break;

			case 'tag':
				$slug = $settings['product_tag'] ?? '';
				if ($slug) {
					$args['tax_query'][] = [
						'taxonomy' => 'product_tag',
						'field'    => 'slug',
						'terms'    => $slug,
					];
				}
				break;

			case 'handpicked':
				$raw = $settings['handpicked_ids'] ?? '';
				$ids = array_filter(array_map('intval', explode(',', $raw)));
				if (! empty($ids)) {
					$args['post__in'] = $ids;
					$args['orderby']  = 'post__in';
				}
				break;
		}

		return new \WP_Query($args);
	}

	protected function render(): void
	{
		$settings   = $this->get_settings_for_display();
		$heading    = $settings['heading_text'] ?? '';
		$desc       = $settings['description']  ?? '';
		$autoplay   = $settings['autoplay']        === 'yes';
		$loop       = $settings['loop']            === 'yes';
		$arrows     = $settings['show_arrows']     === 'yes';
		$pagination = $settings['show_pagination'] === 'yes';
		$delay      = (int) ($settings['autoplay_delay'] ?? 4000);
		$show_all   = $settings['show_all_url_mode'] !== 'none';
		$label      = $settings['show_all_label'] ?? esc_html__('View All', 'shopchop-theme-settings');
		$all_url    = $this->get_show_all_url($settings);

		$query = $this->build_query($settings);
		if (! $query->have_posts()) return;

		// Swiper loop mode needs roughly 2x the largest breakpoint's slidesPerView (4)
		// worth of real slides, or it misbehaves. Force it off when there aren't enough.
		if ($query->post_count < 4) {
			$loop = false;
		}

		$swiper_config = wp_json_encode([
			'slidesPerView'  => 2,
			'spaceBetween'   => 16,
			'loop'           => $loop,
			'autoplay'       => $autoplay ? ['delay' => $delay, 'disableOnInteraction' => false] : false,
			'pagination'     => $pagination ? ['el' => '.swiper-pagination', 'clickable' => true] : false,
			'navigation'     => $arrows ? ['nextEl' => '.swiper-button-next', 'prevEl' => '.swiper-button-prev'] : false,
			'breakpoints'    => [
				768  => ['slidesPerView' => 3, 'spaceBetween' => 20],
				1024 => ['slidesPerView' => 4, 'spaceBetween' => 24],
			],
		]);
?>

		<div class="shopchop-products-carousel">
			<div class="homepage-container">

				<div class="products-carousel-header">
					<div class="products-carousel-header-text">
						<?php if ($heading) : ?>
							<h2 class="products-carousel-heading"><?php echo esc_html($heading); ?></h2>
						<?php endif; ?>
						<?php if ($desc) : ?>
							<p class="products-carousel-description"><?php echo esc_html($desc); ?></p>
						<?php endif; ?>
					</div>

					<?php if ($show_all && $all_url) : ?>
						<a href="<?php echo esc_url($all_url); ?>" class="products-carousel-view-all">
							<?php echo esc_html($label); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"></path></svg>
						</a>
					<?php endif; ?>
				</div>

				<div class="swiper shopchop-products-swiper" data-swiper="<?php echo esc_attr($swiper_config); ?>">
					<div class="swiper-wrapper">

						<?php
						global $wp_query;
						$original_query = $wp_query;
						$wp_query       = $query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

						wc_setup_loop( [
							'name'    => 'shopchop-products-carousel',
							'columns' => 4,
							'total'   => $query->found_posts,
						] );

						while ( $query->have_posts() ) :
							$query->the_post();
							global $product;
							$product = wc_get_product( get_the_ID() );
							if ( ! $product || ! $product->is_visible() ) continue;
							echo '<div class="swiper-slide">';
							wc_get_template_part( 'content', 'product' );
							echo '</div>';
						endwhile;

						wc_reset_loop();
						$wp_query = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						wp_reset_postdata();
						?>

					</div><!-- .swiper-wrapper -->

				</div><!-- .swiper -->

				<?php if ($pagination || $arrows) : ?>
					<div class="products-carousel-footer">
						<?php if ($pagination) : ?>
							<div class="swiper-pagination"></div>
						<?php endif; ?>

						<?php if ($arrows) : ?>
							<div class="products-carousel-nav">
								<div class="swiper-button-prev" role="button" title="<?php esc_attr_e('Previous products', 'shopchop-theme-settings'); ?>" aria-label="<?php esc_attr_e('Previous products', 'shopchop-theme-settings'); ?>"></div>
								<div class="swiper-button-next" role="button" title="<?php esc_attr_e('Next products', 'shopchop-theme-settings'); ?>" aria-label="<?php esc_attr_e('Next products', 'shopchop-theme-settings'); ?>"></div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

<?php
	}
}

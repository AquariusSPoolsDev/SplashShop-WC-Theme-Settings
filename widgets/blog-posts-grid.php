<?php
if (! defined('ABSPATH')) exit;

class ShopChop_Blog_Posts_Grid extends \Elementor\Widget_Base
{
	public function get_name(): string
	{
		return 'shopchop-blog-posts-grid';
	}

	public function get_title(): string
	{
		return esc_html__('Blog Posts Grid', 'shopchop-theme-settings');
	}

	public function get_icon(): string
	{
		return 'eicon-posts-grid';
	}

	public function get_categories(): array
	{
		return ['shopchop'];
	}

	public function get_keywords(): array
	{
		return ['blog', 'posts', 'grid', 'articles', 'news'];
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
		// ── Header ────────────────────────────────────────────────
		$this->start_controls_section('section_header', [
			'label' => esc_html__('Header', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('heading_text', [
			'label'   => esc_html__('Heading', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('From Our Blog', 'shopchop-theme-settings'),
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

		$this->add_control('description', [
			'label'   => esc_html__('Description', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__('Tips, guides and news for pool and spa owners.', 'shopchop-theme-settings'),
			'rows'    => 2,
		]);

		$this->end_controls_section();

		// ── Query ─────────────────────────────────────────────────
		$this->start_controls_section('section_query', [
			'label' => esc_html__('Query', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('posts_count', [
			'label'   => esc_html__('Number of Posts', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 3,
			'min'     => 2,
			'max'     => 12,
		]);

		$this->add_control('columns', [
			'label'   => esc_html__('Columns', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '3',
			'options' => [
				'2' => esc_html__('2 Columns', 'shopchop-theme-settings'),
				'3' => esc_html__('3 Columns', 'shopchop-theme-settings'),
			],
		]);

		// Category filter
		$categories  = get_categories(['hide_empty' => true]);
		$cat_options = ['' => esc_html__('All Categories', 'shopchop-theme-settings')];
		foreach ($categories as $cat) {
			$cat_options[$cat->term_id] = $cat->name;
		}

		$this->add_control('category_id', [
			'label'   => esc_html__('Category', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'options' => $cat_options,
			'default' => '',
		]);

		$this->end_controls_section();

		// ── Display ───────────────────────────────────────────────
		$this->start_controls_section('section_display', [
			'label' => esc_html__('Display', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('show_category', [
			'label'        => esc_html__('Show Category Tag', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		]);

		$this->add_control('show_excerpt', [
			'label'        => esc_html__('Show Excerpt', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		]);

		$this->add_control('show_date', [
			'label'        => esc_html__('Show Date', 'shopchop-theme-settings'),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		]);

		$this->add_control('read_more_label', [
			'label'   => esc_html__('Read More Label', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('Read More', 'shopchop-theme-settings'),
		]);

		$this->end_controls_section();

		// ── View All Link ─────────────────────────────────────────
		$this->start_controls_section('section_view_all', [
			'label' => esc_html__('View All Link', 'shopchop-theme-settings'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('view_all_label', [
			'label'   => esc_html__('Button Label', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__('View All Posts', 'shopchop-theme-settings'),
		]);

		$this->add_control('view_all_url_mode', [
			'label'   => esc_html__('Link URL', 'shopchop-theme-settings'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'auto',
			'options' => [
				'auto'   => esc_html__('Auto (blog archive)', 'shopchop-theme-settings'),
				'custom' => esc_html__('Custom URL', 'shopchop-theme-settings'),
				'none'   => esc_html__('Hide button', 'shopchop-theme-settings'),
			],
		]);

		$this->add_control('view_all_custom_url', [
			'label'       => esc_html__('Custom URL', 'shopchop-theme-settings'),
			'type'        => \Elementor\Controls_Manager::URL,
			'placeholder' => 'https://',
			'condition'   => ['view_all_url_mode' => 'custom'],
		]);

		$this->end_controls_section();
	}

	protected function render(): void
	{
		$settings       = $this->get_settings_for_display();
		$heading        = $settings['heading_text']  ?? '';
		$transform      = $settings['heading_transform'] ?? 'normal';
		$desc           = $settings['description']   ?? '';
		$count          = (int) ($settings['posts_count'] ?? 3);
		$columns        = $settings['columns']        ?? '3';
		$category_id    = $settings['category_id']   ?? '';
		$show_cat       = $settings['show_category'] === 'yes';
		$show_excerpt   = $settings['show_excerpt']  === 'yes';
		$show_date      = $settings['show_date']     === 'yes';
		$read_more      = $settings['read_more_label'] ?? esc_html__('Read More', 'shopchop-theme-settings');

		$view_all_mode  = $settings['view_all_url_mode'] ?? 'auto';
		$view_all_label = $settings['view_all_label'] ?? esc_html__('View All Posts', 'shopchop-theme-settings');

		if ($view_all_mode === 'none') {
			$view_all_url = '';
		} elseif ($view_all_mode === 'custom') {
			$view_all_url = $settings['view_all_custom_url']['url'] ?? '';
		} else {
			$posts_page = get_option('page_for_posts');
			$view_all_url = $posts_page ? get_permalink($posts_page) : get_bloginfo('url') . '/blog/';
		}

		$args = [
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $count,
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		if ($category_id) {
			$args['cat'] = (int) $category_id;
		}

		$query = new \WP_Query($args);
		if (! $query->have_posts()) return;

		$col_class     = $columns === '2' ? 'blog-grid-cols-2' : 'blog-grid-cols-3';
		$heading_class = 'blog-grid-heading' . ($transform === 'uppercase' ? ' shopchop-heading-uppercase' : '');
?>

		<div class="shopchop-blog-posts-grid">
			<div class="homepage-container">

				<?php if ($heading || $desc) : ?>
					<div class="blog-grid-header">
						<?php if ($heading) : ?>
							<h2 class="<?php echo esc_attr($heading_class); ?>"><?php echo esc_html($heading); ?></h2>
						<?php endif; ?>
						<?php if ($desc) : ?>
							<p class="blog-grid-description"><?php echo esc_html($desc); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="blog-grid-wrapper <?php echo esc_attr($col_class); ?>">
					<?php while ($query->have_posts()) : $query->the_post(); ?>

						<article class="blog-post-card group">

							<?php if (has_post_thumbnail()) : ?>
								<div class="blog-post-thumbnail">
									<?php the_post_thumbnail('medium_large', ['class' => 'blog-post-img']); ?>
								</div>
							<?php else : ?>
								<div class="blog-post-no-image"></div>
							<?php endif; ?>

							<div class="blog-post-body">

								<?php if ($show_cat) :
									$cats = get_the_category();
									if (! empty($cats)) : ?>
										<a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" class="blog-post-category">
											<?php echo esc_html($cats[0]->name); ?>
										</a>
								<?php endif;
								endif; ?>

								<h3 class="blog-post-title">
									<a href="<?php the_permalink(); ?>" class="blog-post-title-link">
										<?php the_title(); ?>
									</a>
								</h3>

								<?php if ($show_excerpt) : ?>
									<p class="blog-post-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20, '…')); ?></p>
								<?php endif; ?>

								<div class="blog-post-footer">
									<?php if ($show_date) : ?>
										<time class="blog-post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
											<?php echo esc_html(get_the_date()); ?>
										</time>
									<?php endif; ?>

									<a href="<?php the_permalink(); ?>" class="blog-post-read-more">
										<?php echo esc_html($read_more); ?>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M5 12h14" />
											<path d="m12 5 7 7-7 7" />
										</svg>
									</a>
								</div>

							</div>
						</article>

					<?php endwhile;
					wp_reset_postdata(); ?>
				</div>

				<?php if ($view_all_url) : ?>
					<div class="blog-grid-footer">
						<a href="<?php echo esc_url($view_all_url); ?>" class="blog-grid-view-all">
							<?php echo esc_html($view_all_label); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M5 12h14" />
								<path d="m12 5 7 7-7 7" />
							</svg>
						</a>
					</div>
				<?php endif; ?>

			</div>
		</div>

<?php
	}
}

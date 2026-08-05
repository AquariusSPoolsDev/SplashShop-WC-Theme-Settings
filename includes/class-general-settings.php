<?php
/**
 * ShopChop General Settings page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ShopChop_General_Settings {

	const OPTION_KEY = 'shopchop_general_settings';
	const PAGE_SLUG  = 'shopchop-general-settings';

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_init', [ $this, 'handle_pool_profile_delete' ] );

		// The Settings API gates form submission separately from the menu —
		// keep it aligned with the same manage_woocommerce gate.
		add_filter( 'option_page_capability_' . self::PAGE_SLUG, function () {
			return 'manage_woocommerce';
		} );
	}

	/**
	 * Capability gate for the whole settings page (both tabs).
	 * 'manage_woocommerce' covers Shop Manager as well as Administrator
	 * (Administrators hold every capability, including this one).
	 *
	 * @return bool
	 */
	private function current_user_can_manage() {
		return current_user_can( 'manage_woocommerce' );
	}

	public function add_settings_page() {
		add_menu_page(
			esc_html__( 'The Splash Shop Settings', 'shopchop-theme-settings' ),
			esc_html__( 'The Splash Shop Settings', 'shopchop-theme-settings' ),
			'manage_woocommerce',
			self::PAGE_SLUG,
			[ $this, 'render_settings_page' ],
			'dashicons-store',
			59
		);
	}

	public function register_settings() {
		register_setting( self::PAGE_SLUG, self::OPTION_KEY, [
			'type'              => 'array',
			'sanitize_callback' => [ $this, 'sanitize' ],
			'default'           => [],
		] );

		add_settings_section(
			'shopchop_general_section',
			esc_html__( 'Shop Details', 'shopchop-theme-settings' ),
			'__return_false',
			self::PAGE_SLUG
		);

		$fields = [
			'shop_name'  => __( 'Shop Name', 'shopchop-theme-settings' ),
			'shop_phone' => __( 'Phone Number', 'shopchop-theme-settings' ),
			'shop_email' => __( 'Email', 'shopchop-theme-settings' ),
		];

		foreach ( $fields as $key => $label ) {
			add_settings_field(
				$key,
				esc_html( $label ),
				[ $this, 'render_field' ],
				self::PAGE_SLUG,
				'shopchop_general_section',
				[ 'key' => $key ]
			);
		}
	}

	public function render_field( $args ) {
		$key      = $args['key'];
		$options  = self::get_settings();
		$value    = isset( $options[ $key ] ) ? $options[ $key ] : '';
		$name     = self::OPTION_KEY . '[' . $key . ']';
		$type     = ( 'shop_email' === $key ) ? 'email' : 'text';
		$desc     = '';

		if ( 'shop_phone' === $key ) {
			$desc = esc_html__( 'Used for WhatsApp and sender details. Include country code, e.g. 60123456789.', 'shopchop-theme-settings' );
		}

		printf(
			'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="regular-text" />',
			esc_attr( $type ),
			esc_attr( $key ),
			esc_attr( $name ),
			esc_attr( $value )
		);

		if ( $desc ) {
			printf( '<p class="description">%s</p>', $desc );
		}
	}

	public function sanitize( $input ) {
		$output = [];

		$output['shop_name']  = isset( $input['shop_name'] ) ? sanitize_text_field( $input['shop_name'] ) : '';
		$output['shop_phone'] = isset( $input['shop_phone'] ) ? preg_replace( '/[^0-9+]/', '', $input['shop_phone'] ) : '';
		$output['shop_email'] = isset( $input['shop_email'] ) ? sanitize_email( $input['shop_email'] ) : '';

		return $output;
	}

	public function render_settings_page() {
		if ( ! $this->current_user_can_manage() ) {
			return;
		}

		$tabs = [
			'general'       => esc_html__( 'General', 'shopchop-theme-settings' ),
			'pool-profiles' => esc_html__( 'Customer Pool Profiles', 'shopchop-theme-settings' ),
		];

		$current_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs )
			? sanitize_key( $_GET['tab'] )
			: 'general';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'The Splash Shop Settings', 'shopchop-theme-settings' ); ?></h1>

			<nav class="nav-tab-wrapper">
				<?php foreach ( $tabs as $slug => $label ) : ?>
					<a href="<?php echo esc_url( add_query_arg( [ 'page' => self::PAGE_SLUG, 'tab' => $slug ], admin_url( 'admin.php' ) ) ); ?>" class="nav-tab <?php echo $current_tab === $slug ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<?php if ( 'pool-profiles' === $current_tab ) : ?>
				<?php $this->render_pool_profiles_list(); ?>
			<?php else : ?>
				<form action="options.php" method="post">
					<?php
					settings_fields( self::PAGE_SLUG );
					do_settings_sections( self::PAGE_SLUG );
					submit_button();
					?>
				</form>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Look up a labelled value from a theme-defined option constant
	 * (e.g. SHOPCHOP_POOL_TYPES), falling back to the raw stored key if the
	 * constant isn't available (defensive — this plugin reads theme data,
	 * it doesn't own the CPT).
	 *
	 * @param string $constant
	 * @param string $key
	 * @return string
	 */
	private function label_from_constant( $constant, $key ) {
		if ( ! $key ) {
			return '';
		}
		if ( defined( $constant ) && isset( constant( $constant )[ $key ] ) ) {
			return constant( $constant )[ $key ];
		}
		return $key;
	}

	/**
	 * Build a " (L × W × H unit)" suffix from a pool profile's saved dimensions.
	 * Returns an empty string when any dimension is missing (calculator is optional).
	 *
	 * @param int $profile_id Pool profile post ID.
	 * @return string
	 */
	private function format_pool_dimensions( $profile_id ) {
		$l = get_post_meta( $profile_id, '_pool_size_l', true );
		$w = get_post_meta( $profile_id, '_pool_size_w', true );
		$h = get_post_meta( $profile_id, '_pool_size_h', true );

		if ( '' === $l || '' === $w || '' === $h ) {
			return '';
		}

		$unit = get_post_meta( $profile_id, '_pool_size_unit', true ) === 'ft' ? 'ft' : 'm';

		return sprintf(
			' (%s × %s × %s %s)',
			rtrim( rtrim( number_format( (float) $l, 2 ), '0' ), '.' ),
			rtrim( rtrim( number_format( (float) $w, 2 ), '0' ), '.' ),
			rtrim( rtrim( number_format( (float) $h, 2 ), '0' ), '.' ),
			$unit
		);
	}

	/**
	 * Render a read-only table of every customer's pool profiles, across
	 * all owners — post_type registered by the theme
	 * (theme/inc/pool-profile.php), just queried here.
	 */
	public function render_pool_profiles_list() {
		$profiles = get_posts( [
			'post_type'      => 'pool_profile',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		] );
		?>
		<div class="shopchop-settings-tab-content">
			<h2><?php esc_html_e( 'Customer Pool Profiles', 'shopchop-theme-settings' ); ?></h2>
			<p class="description"><?php esc_html_e( 'All pool profiles saved by customers via My Account, across every owner.', 'shopchop-theme-settings' ); ?></p>

			<?php if ( ! $profiles ) : ?>
				<p><?php esc_html_e( 'No customer pool profiles have been saved yet.', 'shopchop-theme-settings' ); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th style="width:90px;"><?php esc_html_e( 'Photo', 'shopchop-theme-settings' ); ?></th>
							<th><?php esc_html_e( 'Pool Name', 'shopchop-theme-settings' ); ?></th>
							<th><?php esc_html_e( 'Owner', 'shopchop-theme-settings' ); ?></th>
							<th><?php esc_html_e( 'Volume', 'shopchop-theme-settings' ); ?></th>
							<th><?php esc_html_e( 'Pool Type', 'shopchop-theme-settings' ); ?></th>
							<th><?php esc_html_e( 'Saved', 'shopchop-theme-settings' ); ?></th>
							<th style="width:140px;"><?php esc_html_e( 'Actions', 'shopchop-theme-settings' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $profiles as $profile ) :
							$photo_id     = get_post_meta( $profile->ID, '_pool_photo_id', true );
							$volume       = get_post_meta( $profile->ID, '_pool_volume', true );
							$volume_label = $volume ? number_format_i18n( (int) $volume ) . ' L' . $this->format_pool_dimensions( $profile->ID ) : '';
							$type     = $this->label_from_constant( 'SHOPCHOP_POOL_TYPES', get_post_meta( $profile->ID, '_pool_type', true ) );
							$author   = get_userdata( (int) $profile->post_author );
							$phone    = $author ? get_user_meta( $author->ID, 'billing_phone', true ) : '';

							$delete_url = wp_nonce_url(
								add_query_arg( [
									'page'                    => self::PAGE_SLUG,
									'tab'                     => 'pool-profiles',
									'shopchop_delete_profile' => $profile->ID,
								], admin_url( 'admin.php' ) ),
								'shopchop_delete_pool_profile_' . $profile->ID
							);

							$view_data = [
								'name'      => $profile->post_title,
								'photo'     => $photo_id ? wp_get_attachment_image_url( $photo_id, 'medium' ) : '',
								'photoFull' => $photo_id ? wp_get_attachment_image_url( $photo_id, 'large' ) : '',
								'owner'     => $author ? $author->display_name : '',
								'email'     => $author ? $author->user_email : '',
								'phone'     => $phone,
								'volume'    => $volume_label,
								'type'      => $type,
								'shape'     => $this->label_from_constant( 'SHOPCHOP_POOL_SHAPES', get_post_meta( $profile->ID, '_pool_shape', true ) ),
								'sanitiser' => $this->label_from_constant( 'SHOPCHOP_SANITISER_METHODS', get_post_meta( $profile->ID, '_sanitiser_method', true ) ),
								'skimmer'   => $this->label_from_constant( 'SHOPCHOP_SKIMMER_TYPES', get_post_meta( $profile->ID, '_skimmer_type', true ) ),
								'pump'      => get_post_meta( $profile->ID, '_pump_used', true ),
								'filter'    => $this->label_from_constant( 'SHOPCHOP_FILTER_TYPES', get_post_meta( $profile->ID, '_filter_type', true ) ),
								'brand'     => $this->label_from_constant( 'SHOPCHOP_EQUIPMENT_BRANDS', get_post_meta( $profile->ID, '_equipment_brand', true ) ),
								'service'   => get_post_meta( $profile->ID, '_last_service_date', true ),
								'notes'     => get_post_meta( $profile->ID, '_pool_notes', true ),
							];
							?>
							<tr>
								<td>
									<?php if ( $photo_id ) : ?>
										<?php echo wp_get_attachment_image( $photo_id, [ 80, 80 ], false, [ 'style' => 'border-radius:8px;object-fit:cover;' ] ); ?>
									<?php else : ?>
										&mdash;
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( $profile->post_title ); ?></td>
								<td>
									<?php if ( $author ) : ?>
										<a href="<?php echo esc_url( get_edit_user_link( $author->ID ) ); ?>">
											<?php echo esc_html( $author->display_name ); ?>
										</a>
										<br />
										<span class="description">
											<?php echo esc_html( $author->user_email ); ?>
											<?php if ( $phone ) : ?>
												&nbsp;&middot;&nbsp;<?php echo esc_html( $phone ); ?>
											<?php endif; ?>
										</span>
									<?php else : ?>
										&mdash;
									<?php endif; ?>
								</td>
								<td><?php echo $volume_label ? esc_html( $volume_label ) : '—'; ?></td>
								<td><?php echo esc_html( $type ?: '—' ); ?></td>
								<td><?php echo esc_html( get_the_date( '', $profile ) ); ?></td>
								<td>
									<button type="button" class="button button-small shopchop-view-profile" data-profile="<?php echo esc_attr( wp_json_encode( $view_data ) ); ?>">
										<?php esc_html_e( 'View', 'shopchop-theme-settings' ); ?>
									</button>
									<a href="<?php echo esc_url( $delete_url ); ?>" class="submitdelete" style="margin-left:8px;" onclick="return confirm('<?php echo esc_js( __( 'Delete this pool profile? This cannot be undone.', 'shopchop-theme-settings' ) ); ?>');">
										<?php esc_html_e( 'Delete', 'shopchop-theme-settings' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<div id="shopchop-profile-modal" class="shopchop-profile-modal" aria-hidden="true">
					<div class="shopchop-profile-modal-card">
						<button type="button" class="shopchop-profile-modal-close" aria-label="<?php esc_attr_e( 'Close', 'shopchop-theme-settings' ); ?>">&times;</button>
						<div class="shopchop-profile-modal-body"></div>
					</div>
				</div>

				<div id="shopchop-photo-modal" class="shopchop-photo-modal" aria-hidden="true">
					<button type="button" class="shopchop-photo-modal-close" aria-label="<?php esc_attr_e( 'Close', 'shopchop-theme-settings' ); ?>">&times;</button>
					<img src="" alt="" />
				</div>

				<style>
					.shopchop-profile-modal { display: none; position: fixed; inset: 0; z-index: 100000; align-items: center; justify-content: center; background: rgba(0,0,0,.6); padding: 20px; }
					.shopchop-profile-modal.is-open { display: flex; }
					.shopchop-profile-modal-card { position: relative; width: 100%; max-width: 520px; max-height: 85vh; overflow-y: auto; background: #fff; border-radius: 8px; padding: 28px; }
					.shopchop-profile-modal-close { position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border: 0; background: #f0f0f1; border-radius: 4px; font-size: 20px; line-height: 1; cursor: pointer; }
					.shopchop-profile-modal-body img { display: block; width: 100%; max-width: 240px; aspect-ratio: 4/3; object-fit: cover; border-radius: 8px; margin-bottom: 16px; cursor: zoom-in; }
					.shopchop-profile-modal-body table { width: 100%; border-collapse: collapse; }
					.shopchop-profile-modal-body th { text-align: left; width: 160px; padding: 6px 8px 6px 0; color: #646970; vertical-align: top; }
					.shopchop-profile-modal-body td { padding: 6px 0; vertical-align: top; }

					.shopchop-photo-modal { display: none; position: fixed; inset: 0; z-index: 100001; align-items: center; justify-content: center; background: rgba(0,0,0,.9); padding: 24px; }
					.shopchop-photo-modal.is-open { display: flex; }
					.shopchop-photo-modal img { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px; }
					.shopchop-photo-modal-close { position: absolute; top: 20px; right: 20px; width: 40px; height: 40px; border: 0; border-radius: 50%; background: rgba(255,255,255,.1); color: #fff; font-size: 24px; line-height: 1; cursor: pointer; }
				</style>
				<script>
				(function () {
					var modal = document.getElementById('shopchop-profile-modal');
					if (!modal) return;
					var body = modal.querySelector('.shopchop-profile-modal-body');

					var fields = [
						['owner', '<?php echo esc_js( __( 'Owner', 'shopchop-theme-settings' ) ); ?>'],
						['email', '<?php echo esc_js( __( 'Email', 'shopchop-theme-settings' ) ); ?>'],
						['phone', '<?php echo esc_js( __( 'Phone', 'shopchop-theme-settings' ) ); ?>'],
						['volume', '<?php echo esc_js( __( 'Volume', 'shopchop-theme-settings' ) ); ?>'],
						['type', '<?php echo esc_js( __( 'Pool type', 'shopchop-theme-settings' ) ); ?>'],
						['shape', '<?php echo esc_js( __( 'Pool shape', 'shopchop-theme-settings' ) ); ?>'],
						['sanitiser', '<?php echo esc_js( __( 'Sanitiser method', 'shopchop-theme-settings' ) ); ?>'],
						['skimmer', '<?php echo esc_js( __( 'Skimmer', 'shopchop-theme-settings' ) ); ?>'],
						['pump', '<?php echo esc_js( __( 'Pump used', 'shopchop-theme-settings' ) ); ?>'],
						['filter', '<?php echo esc_js( __( 'Filter type', 'shopchop-theme-settings' ) ); ?>'],
						['brand', '<?php echo esc_js( __( 'Equipment brand', 'shopchop-theme-settings' ) ); ?>'],
						['service', '<?php echo esc_js( __( 'Last service date', 'shopchop-theme-settings' ) ); ?>'],
						['notes', '<?php echo esc_js( __( 'Notes', 'shopchop-theme-settings' ) ); ?>']
					];

					var photoModal = document.getElementById('shopchop-photo-modal');
					var photoModalImg = photoModal.querySelector('img');

					function openModal(data) {
						var html = '<h2>' + escapeHtml(data.name) + '</h2>';
						if (data.photo) {
							html += '<img src="' + escapeHtml(data.photo) + '" data-full="' + escapeHtml(data.photoFull || data.photo) + '" alt="" />';
						}
						html += '<table>';
						fields.forEach(function (f) {
							var value = data[f[0]] || '—';
							html += '<tr><th>' + f[1] + '</th><td>' + escapeHtml(value) + '</td></tr>';
						});
						html += '</table>';
						body.innerHTML = html;
						modal.classList.add('is-open');
						modal.setAttribute('aria-hidden', 'false');
					}

					function closeModal() {
						modal.classList.remove('is-open');
						modal.setAttribute('aria-hidden', 'true');
					}

					function openPhotoModal(src) {
						photoModalImg.setAttribute('src', src);
						photoModal.classList.add('is-open');
						photoModal.setAttribute('aria-hidden', 'false');
					}

					function closePhotoModal() {
						photoModal.classList.remove('is-open');
						photoModal.setAttribute('aria-hidden', 'true');
						photoModalImg.setAttribute('src', '');
					}

					function escapeHtml(str) {
						var div = document.createElement('div');
						div.textContent = str;
						return div.innerHTML;
					}

					document.querySelectorAll('.shopchop-view-profile').forEach(function (btn) {
						btn.addEventListener('click', function () {
							openModal(JSON.parse(btn.getAttribute('data-profile')));
						});
					});

					body.addEventListener('click', function (e) {
						if (e.target.tagName === 'IMG') {
							openPhotoModal(e.target.getAttribute('data-full') || e.target.getAttribute('src'));
						}
					});

					modal.querySelector('.shopchop-profile-modal-close').addEventListener('click', closeModal);
					modal.addEventListener('click', function (e) {
						if (e.target === modal) closeModal();
					});

					photoModal.querySelector('.shopchop-photo-modal-close').addEventListener('click', closePhotoModal);
					photoModal.addEventListener('click', function (e) {
						if (e.target === photoModal) closePhotoModal();
					});

					document.addEventListener('keydown', function (e) {
						if (e.key === 'Escape') {
							closePhotoModal();
							closeModal();
						}
					});
				})();
				</script>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Handle profile deletion triggered from the Customer Pool Profiles tab.
	 */
	public function handle_pool_profile_delete() {
		if ( ! isset( $_GET['shopchop_delete_profile'], $_GET['_wpnonce'], $_GET['page'] ) || self::PAGE_SLUG !== $_GET['page'] ) {
			return;
		}

		if ( ! $this->current_user_can_manage() ) {
			return;
		}

		$profile_id = absint( $_GET['shopchop_delete_profile'] );

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'shopchop_delete_pool_profile_' . $profile_id ) ) {
			return;
		}

		$profile = get_post( $profile_id );

		if ( $profile && 'pool_profile' === $profile->post_type ) {
			$photo_id = get_post_meta( $profile_id, '_pool_photo_id', true );
			if ( $photo_id ) {
				wp_delete_attachment( $photo_id, true );
			}
			wp_delete_post( $profile_id, true );
		}

		wp_safe_redirect( add_query_arg( [ 'page' => self::PAGE_SLUG, 'tab' => 'pool-profiles' ], admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Get all ShopChop general settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$defaults = [
			'shop_name'  => get_bloginfo( 'name' ),
			'shop_phone' => '',
			'shop_email' => get_bloginfo( 'admin_email' ),
		];

		$options = get_option( self::OPTION_KEY, [] );

		return wp_parse_args( $options, $defaults );
	}

	/**
	 * Get a single ShopChop general setting.
	 *
	 * @param string $key
	 * @param mixed  $default
	 * @return mixed
	 */
	public static function get( $key, $default = '' ) {
		$settings = self::get_settings();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}

new ShopChop_General_Settings();

<?php
/**
 * Product Video — admin meta fields.
 *
 * "Video Source" select (Select / YouTube / Vimeo) on the WooCommerce
 * Product Data metabox, switching between three input modes:
 * - Select  → WP Media Library picker (video attachment), stores the
 *             attachment ID + its URL.
 * - YouTube → plain URL text field.
 * - Vimeo   → plain URL text field; oEmbed thumbnail cached on save.
 *
 * Stored as: _product_video_source ('media'|'youtube'|'vimeo'),
 * _product_video_url (canonical playable URL regardless of source),
 * _product_video_attachment_id (media mode only),
 * _product_video_thumbnail (vimeo mode only, cached oEmbed thumbnail).
 *
 * @package ShopChop_Theme_Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_product_data_tabs', 'shopchop_video_product_data_tab' );
function shopchop_video_product_data_tab( $tabs ) {
	$tabs['shopchop_video'] = array(
		'label'    => __( 'Product Video', 'shopchop-theme-settings' ),
		'target'   => 'shopchop_video_product_data',
		'class'    => array(),
		'priority' => 80,
	);

	return $tabs;
}

add_action( 'woocommerce_product_data_panels', 'shopchop_video_product_data_panel' );
function shopchop_video_product_data_panel() {
	global $post;

	$source         = get_post_meta( $post->ID, '_product_video_source', true ) ?: 'media';
	$url            = get_post_meta( $post->ID, '_product_video_url', true );
	$attachment_id  = get_post_meta( $post->ID, '_product_video_attachment_id', true );
	$attachment_url = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
	?>
	<div id="shopchop_video_product_data" class="panel woocommerce_options_panel">
		<div class="options_group">
			<p class="form-field">
				<label for="_product_video_source"><?php esc_html_e( 'Video Source', 'shopchop-theme-settings' ); ?></label>
				<select id="_product_video_source" name="_product_video_source" class="select short">
					<option value="media" <?php selected( $source, 'media' ); ?>><?php esc_html_e( 'Select', 'shopchop-theme-settings' ); ?></option>
					<option value="youtube" <?php selected( $source, 'youtube' ); ?>><?php esc_html_e( 'YouTube', 'shopchop-theme-settings' ); ?></option>
					<option value="vimeo" <?php selected( $source, 'vimeo' ); ?>><?php esc_html_e( 'Vimeo', 'shopchop-theme-settings' ); ?></option>
				</select>
			</p>

			<p class="form-field shopchop-video-field shopchop-video-field--media">
				<label><?php esc_html_e( 'Video File', 'shopchop-theme-settings' ); ?></label>
				<span class="shopchop-video-media-picker">
					<button type="button" class="button shopchop-video-select-media"><?php esc_html_e( 'Select Video', 'shopchop-theme-settings' ); ?></button>
					<button type="button" class="button shopchop-video-remove-media" <?php echo $attachment_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove', 'shopchop-theme-settings' ); ?></button>
					<span class="shopchop-video-media-filename"><?php echo esc_html( $attachment_url ? basename( $attachment_url ) : '' ); ?></span>
					<input type="hidden" name="_product_video_attachment_id" class="shopchop-video-attachment-id" value="<?php echo esc_attr( $attachment_id ); ?>" />
				</span>
			</p>

			<p class="form-field shopchop-video-field shopchop-video-field--youtube">
				<label for="_product_video_url_youtube"><?php esc_html_e( 'YouTube video', 'shopchop-theme-settings' ); ?></label>
				<input type="url" class="short" id="_product_video_url_youtube" name="_product_video_url_youtube"
					placeholder="https://www.youtube.com/watch?v=..."
					value="<?php echo esc_attr( 'youtube' === $source ? $url : '' ); ?>" />
			</p>

			<p class="form-field shopchop-video-field shopchop-video-field--vimeo">
				<label for="_product_video_url_vimeo"><?php esc_html_e( 'Vimeo video', 'shopchop-theme-settings' ); ?></label>
				<input type="url" class="short" id="_product_video_url_vimeo" name="_product_video_url_vimeo"
					placeholder="https://vimeo.com/..."
					value="<?php echo esc_attr( 'vimeo' === $source ? $url : '' ); ?>" />
			</p>
		</div>
	</div>
	<?php
}

add_action( 'admin_enqueue_scripts', 'shopchop_video_admin_assets' );
function shopchop_video_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'product' !== get_post_type() ) {
		return;
	}

	wp_enqueue_media();

	wp_enqueue_script(
		'shopchop-admin-product-video',
		SHOPCHOP_SETTINGS_URL . 'assets/js/admin-product-video.js',
		array( 'jquery' ),
		SHOPCHOP_SETTINGS_VERSION,
		true
	);

	wp_enqueue_style(
		'shopchop-admin-product-video',
		SHOPCHOP_SETTINGS_URL . 'assets/css/admin-product-video.css',
		array(),
		SHOPCHOP_SETTINGS_VERSION
	);
}

add_action( 'woocommerce_process_product_meta', 'shopchop_video_save_product_data' );
function shopchop_video_save_product_data( $post_id ) {
	$source = isset( $_POST['_product_video_source'] ) ? sanitize_key( wp_unslash( $_POST['_product_video_source'] ) ) : 'media';

	if ( ! in_array( $source, array( 'media', 'youtube', 'vimeo' ), true ) ) {
		$source = 'media';
	}

	update_post_meta( $post_id, '_product_video_source', $source );

	$url           = '';
	$attachment_id = 0;

	if ( 'media' === $source ) {
		$attachment_id = isset( $_POST['_product_video_attachment_id'] ) ? absint( $_POST['_product_video_attachment_id'] ) : 0;
		$url           = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
	} elseif ( 'youtube' === $source ) {
		$raw = isset( $_POST['_product_video_url_youtube'] ) ? sanitize_text_field( wp_unslash( $_POST['_product_video_url_youtube'] ) ) : '';
		$url = $raw ? esc_url_raw( $raw ) : '';
	} elseif ( 'vimeo' === $source ) {
		$raw = isset( $_POST['_product_video_url_vimeo'] ) ? sanitize_text_field( wp_unslash( $_POST['_product_video_url_vimeo'] ) ) : '';
		$url = $raw ? esc_url_raw( $raw ) : '';
	}

	update_post_meta( $post_id, '_product_video_url', $url );
	update_post_meta( $post_id, '_product_video_attachment_id', $attachment_id );

	if ( 'vimeo' === $source && $url ) {
		$thumbnail = shopchop_fetch_vimeo_thumbnail( $url );
		if ( $thumbnail ) {
			update_post_meta( $post_id, '_product_video_thumbnail', $thumbnail );
			return;
		}
	}

	delete_post_meta( $post_id, '_product_video_thumbnail' );
}

function shopchop_fetch_vimeo_thumbnail( $url ) {
	$response = wp_remote_get( 'https://vimeo.com/api/oembed.json?url=' . rawurlencode( $url ) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return '';
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return is_array( $body ) && ! empty( $body['thumbnail_url'] ) ? esc_url_raw( $body['thumbnail_url'] ) : '';
}

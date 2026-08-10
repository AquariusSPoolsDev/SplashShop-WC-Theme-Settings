<?php
/**
 * Product Video — frontend injection.
 *
 * Doesn't hook any gallery-specific action (this theme's product-image.php
 * overrides WooCommerce's default gallery template and never fires
 * woocommerce_product_thumbnails — see includes/class-admin-product-columns.php
 * commit history for the same lesson). Instead: hook the generic,
 * always-fired woocommerce_before_single_product_summary action to emit a
 * small inline JSON payload, and let player.js detect whatever gallery
 * library is actually present (Swiper here, could be anything on another
 * theme) and inject the video slide itself. Keeps this plugin
 * theme-independent.
 *
 * @package ShopChop_Theme_Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opts into the theme's "render the thumb strip + nav arrows anyway"
 * filter (see product-image.php) whenever this product has a video — the
 * theme has no idea about this plugin's meta keys, it just asks "is
 * something going to add extra slides?".
 */
add_filter( 'shopchop_gallery_has_extra_slides', 'shopchop_video_has_extra_slides', 10, 2 );
function shopchop_video_has_extra_slides( $has_extra, $product ) {
	if ( $has_extra || ! $product ) {
		return $has_extra;
	}

	$product_id = $product->get_id();

	return (bool) get_post_meta( $product_id, '_product_video_url', true ) && get_post_meta( $product_id, '_product_video_source', true );
}

add_action( 'woocommerce_before_single_product_summary', 'shopchop_video_output_payload', 5 );
function shopchop_video_output_payload() {
	global $product;

	if ( ! $product ) {
		return;
	}

	$url    = get_post_meta( $product->get_id(), '_product_video_url', true );
	$source = get_post_meta( $product->get_id(), '_product_video_source', true );

	if ( ! $url || ! $source ) {
		return;
	}

	// 'media' source (WP Media Library picker) is always an mp4/video file;
	// youtube/vimeo still need their video ID pulled out of the saved URL to
	// build the embed URL.
	$type = 'media' === $source ? 'mp4' : $source;
	$id   = ( 'media' !== $source ) ? shopchop_video_extract_id( $source, $url ) : '';

	if ( 'media' !== $source && ! $id ) {
		return;
	}

	$payload = array(
		'type'             => $type,
		'embedUrl'         => null,
		'videoUrl'         => null,
		'videoThumbnail'   => null,
		'featuredImageUrl' => wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_single' ) ?: wc_placeholder_img_src( 'woocommerce_single' ),
		'featuredThumbUrl' => wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_gallery_thumbnail' ) ?: wc_placeholder_img_src( 'woocommerce_gallery_thumbnail' ),
	);

	switch ( $type ) {
		case 'youtube':
			$payload['embedUrl']       = 'https://www.youtube-nocookie.com/embed/' . $id;
			$payload['videoThumbnail'] = 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg';
			break;

		case 'vimeo':
			$payload['embedUrl']       = 'https://player.vimeo.com/video/' . $id;
			$payload['videoThumbnail'] = get_post_meta( $product->get_id(), '_product_video_thumbnail', true ) ?: null;
			break;

		case 'mp4':
			$payload['videoUrl'] = $url;
			break;
	}

	printf(
		'<script type="application/json" id="shopchop-product-video-data">%s</script>',
		wp_json_encode( $payload )
	);
}

function shopchop_video_extract_id( $source, $url ) {
	if ( 'youtube' === $source && preg_match( '#(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})#', $url, $matches ) ) {
		return $matches[1];
	}

	if ( 'vimeo' === $source && preg_match( '#vimeo\.com/(?:.*/)?(\d+)#', $url, $matches ) ) {
		return $matches[1];
	}

	return '';
}

add_action( 'wp_enqueue_scripts', 'shopchop_video_enqueue_assets' );
function shopchop_video_enqueue_assets() {
	if ( ! is_product() ) {
		return;
	}

	$product_id = get_queried_object_id();

	if ( ! $product_id || ! get_post_meta( $product_id, '_product_video_url', true ) || ! get_post_meta( $product_id, '_product_video_source', true ) ) {
		return;
	}

	wp_enqueue_style(
		'shopchop-product-video-player',
		SHOPCHOP_SETTINGS_URL . 'assets/css/product-video-player.css',
		array(),
		SHOPCHOP_SETTINGS_VERSION
	);

	wp_enqueue_script(
		'shopchop-product-video-player',
		SHOPCHOP_SETTINGS_URL . 'assets/js/product-video-player.js',
		array(),
		SHOPCHOP_SETTINGS_VERSION,
		true
	);
}

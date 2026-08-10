<?php
/**
 * Plugin Name:       The Splash Shop Settings
 * Plugin URI:        https://github.com/AquariusSPoolsDev/SplashShop-WC-Theme-Settings
 * Description:       Custom Elementor widgets and other settings for The Splash Shop.
 * Version:           1.5.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Usoppii
 * Author URI:        https://usoppii.my/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       shopchop-theme-settings
 * Requires Plugins:  elementor, woocommerce, yith-woocommerce-wishlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SHOPCHOP_SETTINGS_VERSION', '1.5.0' );
define( 'SHOPCHOP_SETTINGS_DIR', plugin_dir_path( __FILE__ ) );
define( 'SHOPCHOP_SETTINGS_URL', plugin_dir_url( __FILE__ ) );

require_once SHOPCHOP_SETTINGS_DIR . 'includes/class-general-settings.php';
require_once SHOPCHOP_SETTINGS_DIR . 'includes/class-admin-product-columns.php';
require_once SHOPCHOP_SETTINGS_DIR . 'includes/product-video/meta-fields.php';
require_once SHOPCHOP_SETTINGS_DIR . 'includes/product-video/frontend.php';

/**
 * Register custom Elementor widget category: ShopChop.
 */
add_action( 'elementor/elements/categories_registered', function ( $elements_manager ) {
	$elements_manager->add_category( 'shopchop', [
		'title' => esc_html__( 'ShopChop', 'shopchop-theme-settings' ),
		'icon'  => 'fa fa-plug',
	] );
} );

/**
 * Register all ShopChop Elementor widgets.
 */
add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/hero-carousel.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/brand-logos.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/category-grid.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/usp-bar.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/products-carousel.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/product-spotlight.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/promo-banner.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/testimonials-carousel.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/blog-posts-grid.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/contact-quick-menu.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/contact-options.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/heading.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/paragraph.php';
	require_once SHOPCHOP_SETTINGS_DIR . 'widgets/divider.php';

	$widgets_manager->register( new ShopChop_Hero_Carousel() );
	$widgets_manager->register( new ShopChop_Brand_Logos() );
	$widgets_manager->register( new ShopChop_Category_Grid() );
	$widgets_manager->register( new ShopChop_USP_Bar() );
	$widgets_manager->register( new ShopChop_Products_Carousel() );
	$widgets_manager->register( new ShopChop_Product_Spotlight() );
	$widgets_manager->register( new ShopChop_Promo_Banner() );
	$widgets_manager->register( new ShopChop_Testimonials_Carousel() );
	$widgets_manager->register( new ShopChop_Blog_Posts_Grid() );
	$widgets_manager->register( new ShopChop_Contact_Quick_Menu() );
	$widgets_manager->register( new ShopChop_Contact_Options() );
	$widgets_manager->register( new ShopChop_Heading() );
	$widgets_manager->register( new ShopChop_Paragraph() );
	$widgets_manager->register( new ShopChop_Divider() );
} );

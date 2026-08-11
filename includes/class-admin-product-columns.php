<?php
/**
 * Admin product list customizations.
 *
 * Originally moved from theme §16 "Custom Stock Statuses" (stock html filter +
 * list CSS). Extended with: hiding Tags/Brands/Date columns, a Status pill
 * column, richer Stock column output, and expandable variant rows for
 * variable products.
 *
 * @package ShopChop_Theme_Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Task 1 + Task 2: hide Tags/Brands/Date, register Status column between
 * Categories and Featured.
 */
add_filter( 'manage_edit-product_columns', 'shopchop_admin_manage_product_columns' );
function shopchop_admin_manage_product_columns( $columns ) {
	unset( $columns['tags'], $columns['product_brand'], $columns['date'] );

	$new_columns = array();
	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;
		if ( 'product_cat' === $key ) {
			$new_columns['shopchop_status'] = __( 'Status', 'shopchop' );
		}
	}

	return $new_columns;
}

/**
 * Task 2: render the Status pill.
 * Task 4: append the variant expand button below the product name, without
 * re-rendering WooCommerce's own "name" column output (WordPress concatenates
 * every callback registered on this hook for a given column).
 */
add_action( 'manage_product_posts_custom_column', 'shopchop_admin_render_product_columns', 20, 2 );
function shopchop_admin_render_product_columns( $column, $post_id ) {
	if ( 'shopchop_status' === $column ) {
		shopchop_render_status_pill( $post_id );
		return;
	}

	if ( 'name' === $column ) {
		shopchop_render_variant_toggle( $post_id );
	}
}

function shopchop_render_status_pill( $post_id ) {
	$status = get_post_status( $post_id );

	$map = array(
		'publish' => array( 'label' => __( 'Published', 'shopchop' ), 'class' => 'shopchop-pill--publish' ),
		'pending' => array( 'label' => __( 'Pending review', 'shopchop' ), 'class' => 'shopchop-pill--pending' ),
		'draft'   => array( 'label' => __( 'Draft', 'shopchop' ), 'class' => 'shopchop-pill--draft' ),
		'private' => array( 'label' => __( 'Private', 'shopchop' ), 'class' => 'shopchop-pill--private' ),
	);

	if ( ! isset( $map[ $status ] ) ) {
		return;
	}

	printf(
		'<span class="shopchop-pill %s">%s</span>',
		esc_attr( $map[ $status ]['class'] ),
		esc_html( $map[ $status ]['label'] )
	);
}

function shopchop_render_variant_toggle( $post_id ) {
	$product = wc_get_product( $post_id );

	if ( ! $product || ! $product->is_type( 'variable' ) ) {
		return;
	}

	$variation_ids = $product->get_children();
	$count         = count( $variation_ids );

	if ( ! $count ) {
		return;
	}

	printf(
		'<button type="button" class="shopchop-variant-toggle" data-product-id="%d" aria-expanded="false">%s</button>',
		(int) $post_id,
		esc_html( sprintf( _n( '%d variant', '%d variants', $count, 'shopchop' ), $count ) )
	);
}

/**
 * Task 3: Stock column content.
 *
 * WooCommerce's own render_is_in_stock_column() prints `apply_filters(
 * 'woocommerce_admin_stock_html', $stock_html, $product )` — extending that
 * filter (rather than hooking manage_product_posts_custom_column for
 * is_in_stock) is what avoids double-rendering the column, since Woo's
 * renderer is the only thing that echoes it.
 */
add_filter( 'woocommerce_admin_stock_html', 'shopchop_admin_stock_column_html', 10, 2 );
function shopchop_admin_stock_column_html( $html, $product ) {
	$status = $product->get_stock_status();

	if ( 'pre_order' === $status ) {
		return '<mark class="pre-order">' . __( 'Pre-Order Item', 'shopchop' ) . '</mark>';
	}
	if ( 'coming_soon' === $status ) {
		return '<mark class="coming-soon">' . __( 'Coming Soon', 'shopchop' ) . '</mark>';
	}

	$badge = shopchop_stock_status_mark( $status );

	if ( $product->is_type( 'variable' ) ) {
		return $badge . shopchop_variable_stock_lines( $product );
	}

	$qty = $product->managing_stock() ? (int) $product->get_stock_quantity() : null;

	if ( null === $qty ) {
		return $badge;
	}

	return $badge . sprintf( '<br><span class="shopchop-stock-line">%s</span>', esc_html( sprintf( __( 'Stock: %d', 'shopchop' ), $qty ) ) );
}

function shopchop_stock_status_mark( $status ) {
	if ( 'onbackorder' === $status ) {
		return '<mark class="onbackorder">' . __( 'On backorder', 'shopchop' ) . '</mark>';
	}
	if ( 'outofstock' === $status ) {
		return '<mark class="outofstock">' . __( 'Out of stock', 'shopchop' ) . '</mark>';
	}
	return '<mark class="instock">' . __( 'In stock', 'shopchop' ) . '</mark>';
}

function shopchop_variable_stock_lines( $product ) {
	$total_units = 0;
	$in_stock    = 0;
	$children    = $product->get_children();
	$total       = count( $children );

	foreach ( $children as $variation_id ) {
		$variation = wc_get_product( $variation_id );

		if ( ! $variation ) {
			continue;
		}

		$total_units += (int) $variation->get_stock_quantity();

		if ( $variation->is_in_stock() ) {
			$in_stock++;
		}
	}

	$lines  = sprintf( '<br><span class="shopchop-stock-line">%s</span>', esc_html( sprintf( __( 'Total: %d units', 'shopchop' ), $total_units ) ) );
	$lines .= sprintf( '<br><span class="shopchop-stock-line">%s</span>', esc_html( sprintf( __( '%1$d of %2$d variants in stock', 'shopchop' ), $in_stock, $total ) ) );

	return $lines;
}

/**
 * Inject admin-only CSS to style status badges and tighten the product list table.
 * Scoped to the Products list screen only.
 */
add_action( 'admin_head', 'shopchop_admin_custom_status_styles' );
function shopchop_admin_custom_status_styles() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'edit-product' ) return;
	?>
	<style>
		mark.instock     { background:#d1fae5!important; color:#065f46!important; padding:2px 8px; border-radius:4px; font-weight:600!important; }
		mark.outofstock  { background:#ffe4e6!important; color:#9f1239!important; padding:2px 8px; border-radius:4px; font-weight:600!important; }
		mark.onbackorder { background:#fef3c7!important; color:#92400e!important; padding:2px 8px; border-radius:4px; font-weight:600!important; }
		mark.pre-order   { background:#DBEAFE; color:#1D4ED8; padding:2px 8px; border-radius:4px; font-weight:600; }
		mark.coming-soon { background:#FEF3C7; color:#92400E; padding:2px 8px; border-radius:4px; font-weight:600; }

		.wp-list-table .column-thumb { width:80px!important; }
		.thumb.column-thumb .attachment-thumbnail { max-width:80px; max-height:80px; }

		.wp-list-table .column-name a.row-title {
			display:-webkit-box;
			-webkit-line-clamp:1;
			-webkit-box-orient:vertical;
			overflow:hidden;
			max-width:300px;
		}

		.wp-list-table .column-cogs_value,
		.wp-list-table th#cost { display:none; }

		.wp-list-table .column-is_in_stock,
		.wp-list-table .column-price { width:20ch!important; }

		.wp-list-table .column-price { color:#005A7A; font-weight:700; }

		.wp-list-table .column-price .discount-price { display:flex; align-items:center; gap:4px; flex-wrap:wrap; white-space:nowrap; }
		.wp-list-table .column-price .discount-price del.regular  { color:#999; font-size:.8rem; font-weight:400; }
		.wp-list-table .column-price .discount-price .discount    { background:#ffe4e6; color:#9f1239; font-size:.7rem; font-weight:700; padding:1px 5px; border-radius:4px; }
		.wp-list-table .column-price .discount-price .sale        { font-weight:700; color:#005A7A; }

		.shopchop-stock-line { font-size:.8rem; color:#4b5563; }
	</style>
	<?php
}

/**
 * Task 2 stylesheet + Task 4 script, scoped to the Products list screen only.
 */
add_action( 'admin_enqueue_scripts', 'shopchop_admin_product_columns_assets' );
function shopchop_admin_product_columns_assets( $hook ) {
	if ( 'edit.php' !== $hook || 'product' !== ( $_GET['post_type'] ?? '' ) ) {
		return;
	}

	wp_enqueue_style(
		'shopchop-admin-product-columns',
		SHOPCHOP_SETTINGS_URL . 'assets/css/admin-product-columns.css',
		array(),
		SHOPCHOP_SETTINGS_VERSION
	);

	wp_enqueue_script(
		'shopchop-admin-product-columns',
		SHOPCHOP_SETTINGS_URL . 'assets/js/admin-product-columns.js',
		array(),
		SHOPCHOP_SETTINGS_VERSION,
		true
	);

	wp_localize_script(
		'shopchop-admin-product-columns',
		'shopchopProductColumns',
		array(
			'restUrl'        => esc_url_raw( rest_url( 'wc/v3/products' ) ),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'currencySymbol' => get_woocommerce_currency_symbol(),
		)
	);
}

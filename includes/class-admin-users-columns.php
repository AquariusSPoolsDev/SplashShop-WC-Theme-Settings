<?php
/**
 * Admin Users list customizations: Display Name column, sortable Role
 * column, and a colored Role pill (mirrors the product Status pill in
 * class-admin-product-columns.php).
 *
 * @package ShopChop_Theme_Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a Display Name column and swap the built-in Role column for our own
 * sortable "shopchop_role" column — WP_Users_List_Table renders 'role' via
 * its own column_role() method directly, bypassing the custom-column filter,
 * so overriding its markup requires a replacement column rather than a
 * render hook on the existing key.
 */
add_filter( 'manage_users_columns', 'shopchop_admin_manage_users_columns' );
function shopchop_admin_manage_users_columns( $columns ) {
	unset( $columns['role'] );

	$new_columns = array();
	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;
		if ( 'username' === $key ) {
			$new_columns['shopchop_display_name'] = __( 'Display Name', 'shopchop' );
		}
		if ( 'email' === $key ) {
			$new_columns['shopchop_role'] = __( 'Role', 'shopchop' );
		}
	}

	return $new_columns;
}

add_filter( 'manage_users_custom_column', 'shopchop_admin_render_users_columns', 10, 3 );
function shopchop_admin_render_users_columns( $output, $column, $user_id ) {
	if ( 'shopchop_display_name' === $column ) {
		$user = get_userdata( $user_id );
		return $user ? esc_html( $user->display_name ) : '';
	}

	if ( 'shopchop_role' === $column ) {
		return shopchop_render_role_pill( $user_id );
	}

	return $output;
}

function shopchop_render_role_pill( $user_id ) {
	$user = get_userdata( $user_id );

	if ( ! $user || empty( $user->roles ) ) {
		return '';
	}

	$role_slug  = $user->roles[0];
	$role_names = wp_roles()->get_names();
	$label      = isset( $role_names[ $role_slug ] ) ? translate_user_role( $role_names[ $role_slug ] ) : $role_slug;

	$known_roles = array( 'administrator', 'shop_manager', 'customer', 'editor', 'author', 'contributor', 'subscriber' );
	$class       = in_array( $role_slug, $known_roles, true ) ? $role_slug : 'default';

	return sprintf(
		'<span class="shopchop-role-pill shopchop-role-pill--%s">%s</span>',
		esc_attr( $class ),
		esc_html( $label )
	);
}

/**
 * Make the Role column sortable. Role is stored serialized in the
 * wp_capabilities usermeta, so this only sorts correctly for single-role
 * users — the default WordPress setup — not accounts with multiple roles.
 */
add_filter( 'manage_users_sortable_columns', 'shopchop_admin_users_sortable_columns' );
function shopchop_admin_users_sortable_columns( $columns ) {
	$columns['shopchop_role'] = 'shopchop_role';
	return $columns;
}

/**
 * Force the usermeta JOIN (needed for the ORDER BY below) without letting
 * WP_User_Query's own 'meta_value' orderby handling run — that path collided
 * with the role-filter views ("All" / "Administrator" / ...), which also
 * query against wp_capabilities, and returned zero rows.
 */
add_action( 'pre_get_users', 'shopchop_admin_users_orderby_role' );
function shopchop_admin_users_orderby_role( $query ) {
	if ( ! is_admin() || 'shopchop_role' !== $query->get( 'orderby' ) ) {
		return;
	}

	global $wpdb;
	$query->set( 'meta_key', $wpdb->get_blog_prefix() . 'capabilities' );
}

/**
 * Custom rank order (Administrator > Shop manager > Customer > everyone
 * else) rather than an alphabetical sort on the serialized capabilities
 * value, which wouldn't match that order anyway.
 */
add_action( 'pre_user_query', 'shopchop_admin_users_role_orderby_sql' );
function shopchop_admin_users_role_orderby_sql( $query ) {
	if ( ! is_admin() || ! isset( $_GET['orderby'] ) || 'shopchop_role' !== $_GET['orderby'] ) {
		return;
	}

	global $wpdb;

	$order      = isset( $_GET['order'] ) && 'desc' === strtolower( $_GET['order'] ) ? 'DESC' : 'ASC';
	$rank_field = "{$wpdb->usermeta}.meta_value";

	$query->query_orderby = $wpdb->prepare(
		"ORDER BY CASE
			WHEN {$rank_field} LIKE %s THEN 1
			WHEN {$rank_field} LIKE %s THEN 2
			WHEN {$rank_field} LIKE %s THEN 3
			ELSE 4
		END {$order}",
		'%"administrator"%',
		'%"shop_manager"%',
		'%"customer"%'
	);
}

/**
 * Pill stylesheet, scoped to the Users list screen only.
 */
add_action( 'admin_enqueue_scripts', 'shopchop_admin_users_columns_assets' );
function shopchop_admin_users_columns_assets( $hook ) {
	if ( 'users.php' !== $hook ) {
		return;
	}

	wp_enqueue_style(
		'shopchop-admin-users-columns',
		SHOPCHOP_SETTINGS_URL . 'assets/css/admin-users-columns.css',
		array(),
		SHOPCHOP_SETTINGS_VERSION
	);
}

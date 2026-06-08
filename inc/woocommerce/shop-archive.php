<?php
/**
 * Mosharaf Core — Shop Archive Layout & Filters
 *
 * Standalone, optional piece of the WooCommerce module: replaces the shop/
 * category archive's default top-to-bottom layout with a sidebar-filter +
 * toolbar + grid layout (hero, category filter sidebar, result-count/ordering
 * toolbar, custom pagination, optional flexible-content below the grid).
 *
 * Pure hooks + plain links — no AJAX. Filter clicks (mosharaf-core-shop-
 * archive.js) rewrite the URL with a `filter_category` query argument; the
 * resulting request is turned into a tax_query below. Everything is scoped
 * to `is_shop() || is_product_taxonomy()`, so it only touches archive pages
 * and leaves single-product, cart, checkout, and account pages untouched.
 *
 * Removable independently of the rest of the WooCommerce module: delete this
 * file plus its require below, inc/woocommerce/templates/shop-*.php, and the
 * mosharaf-core-shop-archive CSS/JS pair — the shop falls back to WooCommerce's
 * stock archive-product.php layout (still themed by mosharaf-core-woocommerce.css).
 *
 * @package mosharaf-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Turn a `?filter_category=slug,slug` URL parameter into a tax_query on the
 * main shop/archive product query.
 */
function mosharaf_filter_shop_products_by_category( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}

	if ( empty( $_GET['filter_category'] ) ) {
		return;
	}

	$category_slugs = array_filter( array_map( 'sanitize_title', explode( ',', wp_unslash( $_GET['filter_category'] ) ) ) );

	if ( empty( $category_slugs ) ) {
		return;
	}

	$tax_query   = $query->get( 'tax_query' ) ?: [];
	$tax_query[] = [
		'taxonomy' => 'product_cat',
		'field'    => 'slug',
		'terms'    => $category_slugs,
		'operator' => 'IN',
	];

	$query->set( 'tax_query', $tax_query );
}
add_action( 'pre_get_posts', 'mosharaf_filter_shop_products_by_category', 20 );

/**
 * Suppress the archive template's own `<h1>` (and the default header/
 * description/breadcrumb hooks) on shop/category pages — the shop hero
 * (mosharaf_display_shop_hero) reproduces all of it, so without this they'd
 * print twice.
 */
function mosharaf_hide_default_archive_page_title( $show ) {
	if ( is_shop() || is_product_taxonomy() ) {
		return false;
	}
	return $show;
}
add_filter( 'woocommerce_show_page_title', 'mosharaf_hide_default_archive_page_title' );

function mosharaf_remove_archive_default_header_elements() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}

	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10 );
	remove_all_actions( 'woocommerce_archive_description' );
}
add_action( 'template_redirect', 'mosharaf_remove_archive_default_header_elements' );

/**
 * Hero — title, description, optional image. Sits above the filter/grid layout.
 */
function mosharaf_display_shop_hero() {
	if ( is_shop() || is_product_taxonomy() ) {
		get_template_part( 'inc/woocommerce/templates/shop-hero' );
	}
}
add_action( 'woocommerce_before_main_content', 'mosharaf_display_shop_hero', 12 );

/**
 * Open the page wrapper + two-column grid (header / sidebar / content areas —
 * see mosharaf-core-shop-archive.css for the `.shop-grid` layout).
 */
function mosharaf_shop_archive_wrapper_start() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	echo '<div class="shop-page-wrapper layout-padding"><div class="shop-grid">';
}
add_action( 'woocommerce_before_main_content', 'mosharaf_shop_archive_wrapper_start', 13 );

/**
 * Toolbar — filter toggle, result count, ordering dropdown.
 */
function mosharaf_display_shop_toolbar() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	get_template_part( 'inc/woocommerce/templates/shop-toolbar' );
}
add_action( 'woocommerce_before_main_content', 'mosharaf_display_shop_toolbar', 14 );

/**
 * Sidebar — category filter tree.
 */
function mosharaf_display_shop_sidebar() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	echo '<aside class="shop-sidebar">';
	get_template_part( 'inc/woocommerce/templates/shop-filters' );
	echo '</aside>';
}
add_action( 'woocommerce_before_main_content', 'mosharaf_display_shop_sidebar', 15 );

/**
 * Open the content column. Both this and the sidebar hook above run on
 * `woocommerce_before_main_content` priority 15 — order between same-priority
 * callbacks follows registration order, so the sidebar prints first and this
 * starts the adjacent grid cell, exactly matching `.shop-grid`'s two columns.
 */
function mosharaf_shop_content_start() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	echo '<div class="shop-content">';
}
add_action( 'woocommerce_before_main_content', 'mosharaf_shop_content_start', 15 );

/**
 * Custom pagination — replaces WooCommerce's default (removed below) with the
 * theme's shared `mosharaf_render_pagination()` so the shop matches blog/
 * archive pagination styling.
 */
function mosharaf_display_shop_pagination() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	if ( function_exists( 'mosharaf_render_pagination' ) ) {
		mosharaf_render_pagination();
	}
}
add_action( 'woocommerce_after_shop_loop', 'mosharaf_display_shop_pagination', 20 );

/**
 * Close the content column.
 */
function mosharaf_shop_content_end() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	echo '</div><!-- .shop-content -->';
}
add_action( 'woocommerce_after_main_content', 'mosharaf_shop_content_end', 5 );

/**
 * Close the grid + page wrapper.
 */
function mosharaf_shop_archive_wrapper_end() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	echo '</div><!-- .shop-grid --></div><!-- .shop-page-wrapper -->';
}
add_action( 'woocommerce_after_main_content', 'mosharaf_shop_archive_wrapper_end', 10 );

/**
 * Optional ACF flexible-content sections below the grid — same `cms` field as
 * pages, sourced from the queried category term when it has its own content,
 * falling back to the Shop page. Lets a project add CMS blocks (FAQs, banners,
 * brand strips, etc.) under the product grid without a bespoke template.
 */
function mosharaf_load_shop_flexible_content() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}

	if ( ! function_exists( 'mosharaf_flexible_content' ) ) {
		return;
	}

	$content_source = null;

	if ( is_product_taxonomy() ) {
		$queried_object = get_queried_object();

		if ( $queried_object && isset( $queried_object->taxonomy, $queried_object->term_id )
			&& function_exists( 'have_rows' )
			&& have_rows( 'cms', $queried_object->taxonomy . '_' . $queried_object->term_id )
		) {
			$content_source = $queried_object->taxonomy . '_' . $queried_object->term_id;
		}
	}

	if ( ! $content_source ) {
		$shop_page_id = wc_get_page_id( 'shop' );
		if ( $shop_page_id ) {
			$content_source = $shop_page_id;
		}
	}

	if ( $content_source ) {
		mosharaf_flexible_content( 'cms', $content_source );
	}
}
add_action( 'woocommerce_after_main_content', 'mosharaf_load_shop_flexible_content', 10 );

// Reposition result count + ordering into the custom toolbar (shop-toolbar.php).
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

// Replace default pagination with mosharaf_render_pagination() above.
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

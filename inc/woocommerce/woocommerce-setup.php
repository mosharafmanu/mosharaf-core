<?php
/**
 * Mosharaf Core — WooCommerce Module
 *
 * Everything the theme does for WooCommerce lives in this one file plus its
 * sibling asset folders — all of it gated on `class_exists( 'WooCommerce' )`,
 * so it stays inert on sites where the plugin isn't installed. That makes the
 * whole module a single removable unit for non-ecommerce projects: delete
 *   - inc/woocommerce/        (this file)
 *   - woocommerce/            (template overrides)
 *   - assets/css/woocommerce/ assets/js/woocommerce/  (module-specific assets)
 *   - .ai/WOOCOMMERCE.md      (module docs)
 * and nothing else needs to change — the require below is guarded with
 * file_exists() specifically so deleting this folder is safe.
 *
 * @package mosharaf-core
 */

// Shop archive layout + filters — sidebar/toolbar/grid hooks for the shop and
// category pages. A standalone, optional piece of this module: delete this
// require plus inc/woocommerce/shop-archive.php, inc/woocommerce/templates/
// shop-*.php, and the mosharaf-core-shop-archive CSS/JS pair to fall back to
// WooCommerce's stock archive layout (still themed by mosharaf-core-woocommerce.css).
// Loaded here (rather than functions.php) so it shares this file's removability —
// guarded the same way, by file_exists(), since WooCommerce is already active by
// the time this file is reached.
$mosharaf_shop_archive = get_template_directory() . '/inc/woocommerce/shop-archive.php';
if ( file_exists( $mosharaf_shop_archive ) ) {
	require $mosharaf_shop_archive;
}

add_action( 'after_setup_theme', function() {
	add_theme_support( 'woocommerce', [
		'thumbnail_image_width' => 600,
		'single_image_width'    => 900,
		'product_grid'          => [
			'default_rows'    => 3,
			'min_rows'        => 1,
			'default_columns' => 3,
			'min_columns'     => 1,
			'max_columns'     => 4,
		],
	] );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
} );

// Swap default content wrappers for our own layout classes and remove sidebar
add_action( 'init', function() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper',     10 );
	remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar',             'woocommerce_get_sidebar',                10 );
}, 20 );

add_action( 'woocommerce_before_main_content', function() {
	echo '<div class="wc-main layout-padding">';
} );

add_action( 'woocommerce_after_main_content', function() {
	echo '</div>';
} );

// Checkout: WC outputs the "Your order" heading and the order-review box as
// separate siblings of #customer_details. Our two-column checkout grid
// (mosharaf-core-woocommerce.css) places each as its own auto-placed grid
// item — so the heading lands beside "Billing details" (row 1, sized to that
// tall column) while the actual order box gets pushed to a new row underneath
// it, leaving a large empty gap. Wrapping both in one element makes them a
// single grid item that stacks internally, independent of the billing column's
// height.
add_action( 'woocommerce_checkout_before_order_review_heading', function() {
	echo '<div class="checkout-sidebar">';
} );

add_action( 'woocommerce_checkout_after_order_review', function() {
	echo '</div>';
} );

// My Account login/register: when a visitor isn't logged in, WC renders a
// bare <h2>Login</h2> + <form> (or, with registration enabled, a two-column
// `.u-columns` block) directly inside `.woocommerce` — the very same element
// that, for logged-in users, is a 14rem/1fr sidebar+content grid. Without a
// wrapper, the grid's auto-placement scatters these plain-flow siblings into
// that grid's cells (heading in one column, form in another), producing a
// form crammed in a corner of an otherwise-empty page. Wrapping them in one
// element makes them a single block we can center and card-style on its own
// terms (`.account-auth` in mosharaf-core-woocommerce.css), independent of
// the logged-in layout.
add_action( 'woocommerce_before_customer_login_form', function() {
	echo '<div class="account-auth">';
} );

add_action( 'woocommerce_after_customer_login_form', function() {
	echo '</div>';
} );

// Remove WooCommerce default styles — we provide our own
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

// Enqueue our WooCommerce CSS (only when WC is active, after main styles)
add_action( 'wp_enqueue_scripts', function() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	wp_enqueue_style(
		'mosharaf-core-woocommerce',
		get_template_directory_uri() . '/assets/css/woocommerce/mosharaf-core-woocommerce.css',
		[ 'mosharaf-core-starter-style' ],
		MOSHARAF_CORE_VERSION
	);

	// Notices — a standalone, theme-wide WooCommerce component (prints on
	// single-product, cart, checkout, my-account, anywhere wc_print_notices()
	// runs). Kept independent of mosharaf-core-woocommerce so it stays
	// intact even when other WC templates are themed per project.
	wp_enqueue_style(
		'mosharaf-core-notices',
		get_template_directory_uri() . '/assets/css/woocommerce/mosharaf-core-notices.css',
		[ 'mosharaf-core-starter-style' ],
		MOSHARAF_CORE_VERSION
	);

	// Quantity stepper — a standalone, theme-wide WooCommerce component
	// (renders on single-product, cart, checkout, mini-cart, shop-loop).
	// Kept independent of mosharaf-core-woocommerce so it stays intact
	// even when other WC templates are themed per project.
	wp_enqueue_style(
		'mosharaf-core-quantity',
		get_template_directory_uri() . '/assets/css/woocommerce/mosharaf-core-quantity.css',
		[ 'mosharaf-core-starter-style' ],
		MOSHARAF_CORE_VERSION
	);
	wp_enqueue_script(
		'mosharaf-core-quantity',
		get_template_directory_uri() . '/assets/js/woocommerce/mosharaf-core-quantity.js',
		[ 'jquery' ],
		MOSHARAF_CORE_VERSION,
		true
	);

	// Cart instant update — a standalone, theme-wide WooCommerce component
	// (auto-submits the cart's native "Update Cart" AJAX flow on quantity
	// change, so totals refresh without the shopper pressing the button).
	// Kept independent of mosharaf-core-quantity and mosharaf-core-woocommerce
	// so it stays intact — and is removable on its own, e.g. if a project
	// wants the native "Update Cart" button flow back — even when other WC
	// cart behavior is themed per project.
	wp_enqueue_style(
		'mosharaf-core-cart-instant-update',
		get_template_directory_uri() . '/assets/css/woocommerce/mosharaf-core-cart-instant-update.css',
		[ 'mosharaf-core-starter-style' ],
		MOSHARAF_CORE_VERSION
	);
	wp_enqueue_script(
		'mosharaf-core-cart-instant-update',
		get_template_directory_uri() . '/assets/js/woocommerce/mosharaf-core-cart-instant-update.js',
		[ 'jquery' ],
		MOSHARAF_CORE_VERSION,
		true
	);

	// Shop archive layout + filters — only needed on the shop and category
	// pages that the sidebar/toolbar/grid layout (shop-archive.php) renders.
	// Kept independent so it's removable on its own, leaving the rest of the
	// WooCommerce module (and mosharaf-core-woocommerce.css's stock archive
	// styling) intact for projects that don't need a filter sidebar.
	if ( is_shop() || is_product_taxonomy() ) {
		wp_enqueue_style(
			'mosharaf-core-shop-archive',
			get_template_directory_uri() . '/assets/css/woocommerce/mosharaf-core-shop-archive.css',
			[ 'mosharaf-core-woocommerce' ],
			MOSHARAF_CORE_VERSION
		);
		wp_enqueue_script(
			'mosharaf-core-shop-archive',
			get_template_directory_uri() . '/assets/js/woocommerce/mosharaf-core-shop-archive.js',
			[ 'jquery' ],
			MOSHARAF_CORE_VERSION,
			true
		);
	}
}, 20 );

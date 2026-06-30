<?php
/**
 * @package mosharaf-core
 */

if ( ! defined( 'MOSHARAF_CORE_VERSION' ) ) {
	define( 'MOSHARAF_CORE_VERSION', '1.0.43' );
}


// ─────────────────────────────────────────────────────────────────
// ACF DEPENDENCY CHECK
// This theme requires Advanced Custom Fields (free or pro).
// Without it the dispatcher, section builder, and all settings
// helpers are non-functional. Fail loudly rather than silently.
// ─────────────────────────────────────────────────────────────────

add_action( 'admin_notices', function () {
	if ( class_exists( 'ACF' ) ) {
		return;
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$install_url = admin_url( 'plugin-install.php?s=advanced+custom+fields&tab=search&type=term' );
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				wp_kses(
					/* translators: %s: URL to plugin installer */
					__( '<strong>Mosharaf Core requires Advanced Custom Fields.</strong> The page builder, section templates, and all site settings depend on it. <a href="%s">Install ACF Free &rarr;</a>', 'mosharaf-core' ),
					[
						'strong' => [],
						'a'      => [ 'href' => [] ],
					]
				),
				esc_url( $install_url )
			);
			?>
		</p>
	</div>
	<?php
} );


// ─────────────────────────────────────────────────────────────────
// THEME SETUP
// ─────────────────────────────────────────────────────────────────

function mosharaf_setup() {
	load_theme_textdomain( 'mosharaf-core', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus( array(
		'mainMenu'   => esc_html__( 'Main Menu',   'mosharaf-core' ),
		'footerMenu' => esc_html__( 'Footer Menu', 'mosharaf-core' ),
	) );

	// Controls max width for oEmbed — should match --mc-container-max.
	$GLOBALS['content_width'] = 1440;
}
add_action( 'after_setup_theme', 'mosharaf_setup' );


// ─────────────────────────────────────────────────────────────────
// WIDGET AREAS
// ─────────────────────────────────────────────────────────────────

function mosharaf_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'mosharaf-core' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'mosharaf_widgets_init' );


// ─────────────────────────────────────────────────────────────────
// SCRIPTS & STYLES
// ─────────────────────────────────────────────────────────────────

function mosharaf_scripts() {
	// Slick (carousel) ships its CSS + JS only on pages that actually render a
	// carousel — see mosharaf_page_needs_slick(). Base core renders none, so it
	// is off by default; the assets stay registered so a child theme / filter /
	// render-time enqueue can opt in. scripts.js self-guards when Slick is absent.
	$needs_slick = function_exists( 'mosharaf_page_needs_slick' ) && mosharaf_page_needs_slick();

	// ── Fonts ────────────────────────────────────────────────────
	wp_enqueue_style(
		'mosharaf-core-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap',
		array(),
		null,
		'all'
	);

	// ── Core CSS ─────────────────────────────────────────────────
	wp_enqueue_style( 'mosharaf-core-spacer',         get_template_directory_uri() . '/assets/css/spacer.css',                        array(), MOSHARAF_CORE_VERSION );
	wp_enqueue_style( 'mosharaf-core-utilities',      get_template_directory_uri() . '/assets/css/utilities.css',                     array(), MOSHARAF_CORE_VERSION );
	// Video CSS is registered, not enqueued — mosharaf_render_video() pulls it in at
	// render time (like the video JS below), so pages without a video ship neither.
	wp_register_style( 'mosharaf-core-video',         get_template_directory_uri() . '/assets/css/video-behaviors.css',               array(), MOSHARAF_CORE_VERSION );
	wp_register_style( 'mosharaf-core-video-popup',   get_template_directory_uri() . '/assets/css/video-popup.css',                   array(), MOSHARAF_CORE_VERSION );
	// Registered always so they can be opted in; enqueued only where needed.
	wp_register_style( 'slick-carousel',              get_template_directory_uri() . '/assets/css/slick.css',                         array(), MOSHARAF_CORE_VERSION );
	wp_register_style( 'mosharaf-core-slick-custom',  get_template_directory_uri() . '/assets/css/mosharaf-core-slick-custom.css',    array( 'slick-carousel' ), MOSHARAF_CORE_VERSION );
	if ( $needs_slick ) {
		wp_enqueue_style( 'slick-carousel' );
		wp_enqueue_style( 'mosharaf-core-slick-custom' );
	}
	wp_enqueue_style( 'mosharaf-core-design-style',   get_template_directory_uri() . '/assets/css/mosharaf-core-design-style.css',    array(), MOSHARAF_CORE_VERSION );
	wp_enqueue_style( 'mosharaf-core-form-style',    get_template_directory_uri() . '/assets/css/mosharaf-core-form.css',             array(), MOSHARAF_CORE_VERSION );
	wp_enqueue_style( 'mosharaf-core-starter-style',  get_template_directory_uri() . '/assets/css/mosharaf-core-starter-style.css',   array(), MOSHARAF_CORE_VERSION );
	wp_enqueue_style( 'mosharaf-core-style',          get_stylesheet_uri(),                                                           array(), MOSHARAF_CORE_VERSION );

	// ── Core JS ──────────────────────────────────────────────────
	// Slick JS: registered always, enqueued only where a carousel renders.
	// scripts.js no longer hard-depends on it (its carousel init self-guards when
	// $.fn.slick is absent), so scripts.js loads everywhere while Slick is gated.
	wp_register_script( 'slick-carousel',             get_template_directory_uri() . '/assets/js/slick.js',                       array( 'jquery' ), MOSHARAF_CORE_VERSION, true );
	if ( $needs_slick ) {
		wp_enqueue_script( 'slick-carousel' );
	}
	wp_enqueue_script( 'mosharaf-core-scripts',         get_template_directory_uri() . '/assets/js/scripts.js',                   array( 'jquery' ), MOSHARAF_CORE_VERSION, true );

	// Video JS is registered, not enqueued — mosharaf_render_video() pulls in only
	// what a rendered video needs (behaviors always; popup for onclick-popup; the
	// Vimeo player only for a vimeo source), so video-free pages ship none.
	wp_register_script( 'jquery-vimeo-player',         get_template_directory_uri() . '/assets/js/jquery.mb.vimeo_player.min.js', array( 'jquery' ), MOSHARAF_CORE_VERSION, true );
	wp_register_script( 'mosharaf-core-video-behaviors', get_template_directory_uri() . '/assets/js/video-behaviors.js',           array( 'jquery' ), MOSHARAF_CORE_VERSION, true );
	wp_register_script( 'mosharaf-core-video-popup',     get_template_directory_uri() . '/assets/js/video-popup.js',               array( 'jquery' ), MOSHARAF_CORE_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'mosharaf_scripts' );


// ─────────────────────────────────────────────────────────────────
// Move jQuery to the footer so it stops blocking the first paint.
// WordPress loads jQuery render-blocking in <head> by default; every
// jQuery-dependent script here (scripts.js, slick, CF7) already loads in the
// footer, so dependency order is preserved. Skips admin.
// ─────────────────────────────────────────────────────────────────

function mosharaf_jquery_to_footer() {
	if ( is_admin() ) {
		return;
	}

	$scripts = wp_scripts();
	foreach ( array( 'jquery', 'jquery-core', 'jquery-migrate' ) as $handle ) {
		if ( isset( $scripts->registered[ $handle ] ) ) {
			$scripts->add_data( $handle, 'group', 1 );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'mosharaf_jquery_to_footer', 100 );


// ─────────────────────────────────────────────────────────────────
// CONTACT FORM 7 — load its CSS/JS only where a form renders.
// CF7 enqueues site-wide by default; mosharaf_page_needs_contact_form()
// detects the [contact-form-7] shortcode on the queried object (and is
// filterable), so every other page ships none of CF7's CSS/JS.
// ─────────────────────────────────────────────────────────────────

function mosharaf_cf7_conditional_assets( $load ) {
	if ( ! function_exists( 'mosharaf_page_needs_contact_form' ) ) {
		return $load;
	}
	return mosharaf_page_needs_contact_form() ? $load : false;
}
add_filter( 'wpcf7_load_js',  'mosharaf_cf7_conditional_assets' );
add_filter( 'wpcf7_load_css', 'mosharaf_cf7_conditional_assets' );


// ─────────────────────────────────────────────────────────────────
// EDITOR — Gutenberg disabled; theme uses ACF Flexible Content
// ─────────────────────────────────────────────────────────────────

add_filter( 'use_block_editor_for_post_type', '__return_false' );
add_filter( 'use_block_editor_for_post',      '__return_false' );

add_action( 'after_setup_theme', function() {
	remove_theme_support( 'widgets-block-editor' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}, 100 );

add_action( 'admin_enqueue_scripts', function() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
}, 100 );


// ─────────────────────────────────────────────────────────────────
// ACF JSON SYNC
// ─────────────────────────────────────────────────────────────────

add_filter( 'acf/settings/save_json', function( $path ) {
	return get_stylesheet_directory() . '/acf-json';
} );

add_filter( 'acf/settings/load_json', function( $paths ) {
	unset( $paths[0] );
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
} );


// ─────────────────────────────────────────────────────────────────
// CORE INCLUDES
// ─────────────────────────────────────────────────────────────────

require get_template_directory() . '/inc/image-sizes.php';

foreach ( glob( get_template_directory() . '/inc/components/*/*.php' ) as $file ) {
	require $file;
}

foreach ( glob( get_template_directory() . '/inc/helper-functions/*.php' ) as $file ) {
	require $file;
}


// ─────────────────────────────────────────────────────────────────
// WOOCOMMERCE
// Self-contained, optional module — see inc/woocommerce/woocommerce-setup.php.
// Guarded with file_exists() so projects that don't need WooCommerce can
// delete the whole module (that file, woocommerce/, assets/{css,js}/woocommerce/,
// .ai/WOOCOMMERCE.md) without touching this require.
// ─────────────────────────────────────────────────────────────────

$mosharaf_woocommerce_setup = get_template_directory() . '/inc/woocommerce/woocommerce-setup.php';
if ( file_exists( $mosharaf_woocommerce_setup ) ) {
	require $mosharaf_woocommerce_setup;
}


// ─────────────────────────────────────────────────────────────────
// POST CONTENT CLEANUP
// ─────────────────────────────────────────────────────────────────

add_filter( 'the_content', function( $content ) {
	if ( is_admin() || 'post' !== get_post_type() ) {
		return $content;
	}
	return preg_replace( '/(<[^>]+) style=".*?"/i', '$1', $content );
}, 20 );

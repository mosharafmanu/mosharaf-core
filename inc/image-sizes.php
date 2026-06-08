<?php
/**
 * @package mosharaf-core
 */

add_action( 'after_setup_theme', 'mosharaf_register_image_sizes' );
function mosharaf_register_image_sizes() {
	add_image_size( 'mc-300',  300,  9999, false );
	add_image_size( 'mc-600',  600,  9999, false );
	add_image_size( 'mc-900',  900,  9999, false );
	add_image_size( 'mc-1200', 1200, 9999, false );
	add_image_size( 'mc-1600', 1600, 9999, false );
}

// ─────────────────────────────────────────────────────────────────
// DISABLE WORDPRESS DEFAULT SIZES
// ─────────────────────────────────────────────────────────────────

add_filter( 'intermediate_image_sizes_advanced', function( $sizes ) {
	unset( $sizes['medium'] );
	unset( $sizes['medium_large'] );
	unset( $sizes['large'] );
	unset( $sizes['1536x1536'] );
	unset( $sizes['2048x2048'] );
	return $sizes;
} );

// ─────────────────────────────────────────────────────────────────
// SRCSET + WEBP
// ─────────────────────────────────────────────────────────────────

add_filter( 'max_srcset_image_width', function() {
	return 3840;
} );

add_filter( 'mime_types', function( $mimes ) {
	$mimes['webp'] = 'image/webp';
	return $mimes;
} );

<?php
/**
 * @package mosharaf-core
 */

add_filter( 'walker_nav_menu_start_el', function( $item_output, $item, $depth, $args ) {
	if ( ! in_array( 'menu-item-has-children', $item->classes ) ) {
		return $item_output;
	}
	if ( ! isset( $args->theme_location ) || 'mainMenu' !== $args->theme_location ) {
		return $item_output;
	}
	$chevron = '<span class="submenu-indicator" aria-hidden="true">'
		. '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">'
		. '<path d="M1 1l4 4 4-4"/>'
		. '</svg>'
		. '</span>';
	return str_replace( '</a>', $chevron . '</a>', $item_output );
}, 10, 4 );

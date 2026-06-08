/**
 * Mosharaf Core — Quantity Stepper (WooCommerce)
 *
 * WC's number input only offers the browser's native spin arrows.
 * Inject custom −/+ buttons that respect the input's min/max/step and
 * fire `change` so WC's own scripts (cart totals, variation pricing)
 * react normally. Deliberately global — `.quantity` appears on
 * single-product, cart, checkout, mini-cart, and shop-loop forms, and
 * this control should behave identically everywhere regardless of how
 * other WooCommerce templates are themed per project. WC swaps in
 * fresh quantity markup after several AJAX events, so injection
 * re-runs then — guarded by checking for existing buttons rather than
 * caching state, since the DOM itself may have been replaced wholesale.
 *
 * @package mosharaf-core
 */

( function ( $ ) {
	'use strict';

	function refreshQtyStepperState( $wrapper ) {
		const $input = $wrapper.find( 'input.qty[type="number"]' );
		if ( ! $input.length ) return;

		const value = parseFloat( $input.val() );
		const min   = parseFloat( $input.attr( 'min' ) );
		const max   = parseFloat( $input.attr( 'max' ) );

		$wrapper.find( '.qty-btn--minus' ).prop( 'disabled', ! isNaN( min ) && value <= min );
		$wrapper.find( '.qty-btn--plus' ).prop( 'disabled', ! isNaN( max ) && value >= max );
	}

	function buildQtySteppers() {
		$( '.quantity' ).each( function () {
			const $wrapper = $( this );
			const $input   = $wrapper.find( 'input.qty[type="number"]' );

			if ( ! $input.length || $wrapper.find( '.qty-btn' ).length ) return;

			$input
				.before( '<button type="button" class="qty-btn qty-btn--minus" aria-label="Decrease quantity">&minus;</button>' )
				.after( '<button type="button" class="qty-btn qty-btn--plus" aria-label="Increase quantity">+</button>' );

			refreshQtyStepperState( $wrapper );
		} );
	}

	$( function () {
		buildQtySteppers();

		// WC re-renders quantity inputs after these — rebuild the buttons
		// and re-evaluate disabled state against the fresh min/max/value.
		$( document.body ).on( 'updated_wc_div updated_cart_totals found_variation reset_data', buildQtySteppers );

		$( document ).on( 'click', '.qty-btn--minus, .qty-btn--plus', function ( event ) {
			event.preventDefault();

			const $btn = $( this );
			if ( $btn.prop( 'disabled' ) ) return;

			const $wrapper   = $btn.closest( '.quantity' );
			const $input     = $wrapper.find( 'input.qty[type="number"]' );
			const step       = parseFloat( $input.attr( 'step' ) ) || 1;
			const min        = parseFloat( $input.attr( 'min' ) );
			const max        = parseFloat( $input.attr( 'max' ) );
			const current    = parseFloat( $input.val() ) || 0;
			const direction  = $btn.hasClass( 'qty-btn--plus' ) ? 1 : -1;

			let next = current + ( direction * step );
			if ( ! isNaN( min ) ) next = Math.max( next, min );
			if ( ! isNaN( max ) ) next = Math.min( next, max );

			$input.val( next ).trigger( 'change' );
			refreshQtyStepperState( $wrapper );
		} );

		$( document ).on( 'change input', '.quantity input.qty[type="number"]', function () {
			refreshQtyStepperState( $( this ).closest( '.quantity' ) );
		} );
	} );

} )( jQuery );

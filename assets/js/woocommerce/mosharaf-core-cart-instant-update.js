/**
 * Mosharaf Core — Cart Instant Update (WooCommerce)
 *
 * WC's own wc-cart.js already AJAX-refreshes the cart form and totals
 * when "Update Cart" is clicked — no page reload; see
 * quantity_update()/update_wc_div() in assets/js/frontend/cart.js.
 * Rather than reimplement any of that (stock checks, notices, coupon
 * recalculation, the loading overlay, accessibility scroll-to-notices…),
 * this just clicks that same button automatically, debounced, once the
 * shopper settles on a value — so the existing native flow runs without
 * them having to find and press it themselves. Delegated on `document`
 * (not the form) because WC replaces `.woocommerce-cart-form` wholesale
 * on every update — a handler bound to the old node would go silent
 * after the first refresh.
 *
 * Once this is active the button itself becomes redundant — every change
 * already triggers its native click — so a `body` class
 * (mosharaf-core-cart-instant-update.css hides the button on it) signals
 * "JS auto-update is live, hide the now-pointless control". It's added to
 * `body` rather than the button because WC replaces the button's element
 * wholesale too; an element-level class would vanish on the very first
 * refresh. No-JS visitors never get this class, so the button stays put
 * as their only way to apply a changed quantity.
 *
 * Deliberately standalone — kept independent of mosharaf-core-quantity and
 * mosharaf-core-woocommerce so it stays intact (and is removable on its
 * own, e.g. if a project wants the native "Update Cart" flow back) even
 * when other WC cart behavior is themed per project.
 *
 * @package mosharaf-core
 */

( function ( $ ) {
	'use strict';

	$( function () {
		const $cartForm = $( '.woocommerce-cart-form' );

		if ( $cartForm.length ) {
			$( 'body' ).addClass( 'mc-cart-instant-update' );
		}

		const CART_UPDATE_DEBOUNCE_MS = 600;
		let cartUpdateTimer;

		$( document ).on( 'change input', '.woocommerce-cart-form input.qty[type="number"]', function () {
			const $form = $( this ).closest( '.woocommerce-cart-form' );

			clearTimeout( cartUpdateTimer );
			cartUpdateTimer = setTimeout( function () {
				const $updateBtn = $form.find( ':input[name="update_cart"]' );

				// WC enables this button itself on the same change/input
				// event (see input_changed in cart.js) — if it's still
				// disabled, nothing actually changed yet.
				if ( ! $updateBtn.length || $updateBtn.prop( 'disabled' ) ) {
					return;
				}

				$updateBtn.get( 0 ).click();
			}, CART_UPDATE_DEBOUNCE_MS );
		} );
	} );

} )( jQuery );

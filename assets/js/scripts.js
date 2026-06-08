/**
 * Mosharaf Core — Main Theme Scripts
 * @package mosharaf-core
 */

( function ( $ ) {
	'use strict';

	$( function () {

		// ─────────────────────────────────────────────────────────────
		// HEADER OFFSET
		// Sets --header-offset so sticky-aware sections can position
		// themselves. Re-calculated on resize and after fonts load.
		// ─────────────────────────────────────────────────────────────

		const $header = $( '.site-header' );

		function updateHeaderOffset() {
			if ( ! $header.length ) return;
			document.documentElement.style.setProperty(
				'--header-offset',
				$header.outerHeight() + 'px'
			);
		}

		updateHeaderOffset();

		let resizeTimer;
		$( window ).on( 'resize', function () {
			clearTimeout( resizeTimer );
			resizeTimer = setTimeout( updateHeaderOffset, 100 );
		} );

		$( window ).on( 'load', function () {
			setTimeout( updateHeaderOffset, 200 );
		} );


		// ─────────────────────────────────────────────────────────────
		// HEADER SCROLL STATE
		// Adds .is-scrolled after 30px — CSS uses this for compact mode.
		// ─────────────────────────────────────────────────────────────

		if ( $header.length ) {
			const SCROLL_THRESHOLD = 30;

			function handleScroll() {
				$header.toggleClass( 'is-scrolled', $( window ).scrollTop() > SCROLL_THRESHOLD );
			}

			handleScroll();
			$( window ).on( 'scroll', handleScroll );
		}


		// ─────────────────────────────────────────────────────────────
		// MOBILE MENU
		// ─────────────────────────────────────────────────────────────

		const $toggle  = $( '.mobile-menu-toggle' );
		const $mobileNav = $( '.mobile-navigation' );
		const $overlay = $( '.mobile-menu-overlay' );
		const $close   = $( '.mobile-menu-close' );
		const $body    = $( 'body' );

		const FOCUSABLE = 'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])';

		function openMenu() {
			$mobileNav.addClass( 'is-active' );
			$overlay.addClass( 'is-active' );
			$toggle.addClass( 'is-open' ).attr( 'aria-expanded', 'true' );
			$mobileNav.attr( 'aria-hidden', 'false' );
			$body.addClass( 'no-scroll' );
			// Move focus to first focusable element inside panel
			$mobileNav.find( FOCUSABLE ).first().trigger( 'focus' );
		}

		function closeMenu() {
			$mobileNav.removeClass( 'is-active' );
			$overlay.removeClass( 'is-active' );
			$toggle.removeClass( 'is-open' ).attr( 'aria-expanded', 'false' );
			$mobileNav.attr( 'aria-hidden', 'true' );
			$body.removeClass( 'no-scroll' );
			$toggle.trigger( 'focus' );
		}

		function isMenuOpen() {
			return $mobileNav.hasClass( 'is-active' );
		}

		$toggle.on( 'click', function () {
			isMenuOpen() ? closeMenu() : openMenu();
		} );

		$close.on( 'click', closeMenu );
		$overlay.on( 'click', closeMenu );

		// Close when resizing back to desktop
		$( window ).on( 'resize', function () {
			if ( isMenuOpen() && $( window ).width() > 1199 ) {
				closeMenu();
			}
		} );

		// Keyboard: Escape closes; Tab traps focus inside panel
		$( document ).on( 'keydown', function ( e ) {
			if ( ! isMenuOpen() ) return;

			if ( e.key === 'Escape' ) {
				closeMenu();
				return;
			}

			if ( e.key === 'Tab' ) {
				const $focusable = $mobileNav.find( FOCUSABLE ).filter( ':visible' );
				const $first = $focusable.first();
				const $last  = $focusable.last();

				if ( e.shiftKey && $( document.activeElement ).is( $first ) ) {
					e.preventDefault();
					$last.trigger( 'focus' );
				} else if ( ! e.shiftKey && $( document.activeElement ).is( $last ) ) {
					e.preventDefault();
					$first.trigger( 'focus' );
				}
			}
		} );


		// ─────────────────────────────────────────────────────────────
		// MOBILE SUBMENU TOGGLES
		// Injects a chevron button next to each parent link and
		// handles expand / collapse with aria state.
		// ─────────────────────────────────────────────────────────────

		const chevronSVG = '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

		$( '.mobile-menu li.menu-item-has-children' ).each( function () {
			const $li      = $( this );
			const $submenu = $li.children( '.sub-menu' ).hide();
			const $btn     = $( '<button>', {
				class:           'submenu-toggle',
				'aria-expanded': 'false',
				'aria-label':    'Expand submenu',
				html:            chevronSVG,
			} );

			$li.children( 'a' ).after( $btn );

			$btn.on( 'click', function () {
				const isExpanded = $btn.attr( 'aria-expanded' ) === 'true';

				// Close all other open submenus
				$( '.mobile-menu li.menu-item-has-children' ).not( $li ).each( function () {
					$( this ).children( '.sub-menu' ).slideUp( 300 );
					$( this ).find( '.submenu-toggle' ).attr( 'aria-expanded', 'false' );
				} );

				$btn.attr( 'aria-expanded', String( ! isExpanded ) );
				$submenu.slideToggle( 300 );
			} );
		} );


		// ─────────────────────────────────────────────────────────────
		// DESKTOP NAV — KEYBOARD ACCESSIBILITY
		// Arrow keys navigate items; Escape closes open dropdowns.
		// ─────────────────────────────────────────────────────────────

		$( '.main-menu > li.menu-item-has-children > a' ).on( 'keydown', function ( e ) {
			if ( e.key !== 'ArrowDown' && e.key !== 'Enter' ) return;
			if ( e.key === 'Enter' && $( this ).attr( 'href' ) !== '#' ) return;

			e.preventDefault();
			const $submenu = $( this ).siblings( '.sub-menu' );
			$submenu.find( 'a' ).first().trigger( 'focus' );
		} );

		$( '.main-menu .sub-menu a' ).on( 'keydown', function ( e ) {
			const $links  = $( this ).closest( '.sub-menu' ).find( 'a' );
			const index   = $links.index( this );

			if ( e.key === 'ArrowDown' ) {
				e.preventDefault();
				$links.eq( index + 1 ).trigger( 'focus' );
			} else if ( e.key === 'ArrowUp' ) {
				e.preventDefault();
				if ( index === 0 ) {
					$( this ).closest( '.sub-menu' ).siblings( 'a' ).trigger( 'focus' );
				} else {
					$links.eq( index - 1 ).trigger( 'focus' );
				}
			} else if ( e.key === 'Escape' ) {
				$( this ).closest( '.sub-menu' ).siblings( 'a' ).trigger( 'focus' );
			}
		} );


		// ─────────────────────────────────────────────────────────────
		// BACK TO TOP
		// Shows after 400px scroll; hides when back at top.
		// ─────────────────────────────────────────────────────────────

		const $backToTop = $( '.back-to-top' );

		if ( $backToTop.length ) {
			$( window ).on( 'scroll.backToTop', function () {
				const visible = $( this ).scrollTop() > 400;
				$backToTop
					.toggleClass( 'is-visible', visible )
					.attr( 'aria-hidden', String( ! visible ) );
			} );

			$backToTop.on( 'click', function () {
				$( 'html, body' ).animate( { scrollTop: 0 }, 500, 'swing' );
				$( this ).trigger( 'blur' );
			} );
		}


		// ─────────────────────────────────────────────────────────────
		// SMOOTH SCROLL TO ANCHOR
		// Offset accounts for sticky header height.
		// ─────────────────────────────────────────────────────────────

		$( 'a[href^="#"]' ).on( 'click', function ( event ) {
			// Ignore programmatic triggers (e.g. WooCommerce activating its
			// description/reviews tabs via $(...).trigger('click') on load) —
			// only react to genuine user clicks on in-page nav links.
			if ( event.isTrigger ) return;

			const href = $( this ).attr( 'href' );
			if ( ! href || href === '#' || href === '#!' ) return;

			const $target = $( href );
			if ( ! $target.length ) return;

			event.preventDefault();

			const offset = $header.outerHeight() + 20 || 20;
			$( 'html, body' ).animate(
				{ scrollTop: $target.offset().top - offset },
				600,
				'swing'
			);
		} );

	} ); // end document.ready

} )( jQuery );


// ─────────────────────────────────────────────────────────────────
// VIDEO AUTOPLAY ON SCROLL
// Plays/pauses .autoplay-video containers on intersection.
// Kept outside jQuery wrapper — no jQuery dependency needed.
// ─────────────────────────────────────────────────────────────────

document.addEventListener( 'DOMContentLoaded', function () {
	const containers = document.querySelectorAll( '.autoplay-video' );
	if ( ! containers.length ) return;

	const observer = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			const video = entry.target.querySelector( 'video' );
			if ( ! video ) return;
			if ( entry.isIntersecting ) {
				video.currentTime = 0;
				video.play().catch( function () {} );
			} else {
				video.pause();
			}
		} );
	}, { threshold: 0.5 } );

	containers.forEach( function ( el ) {
		observer.observe( el );
	} );
} );

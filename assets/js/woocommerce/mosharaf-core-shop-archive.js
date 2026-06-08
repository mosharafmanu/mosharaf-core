/**
 * Mosharaf Core — Shop Archive Filters & Layout
 *
 * Drives the shop/category archive's filter sidebar and toolbar:
 *   - Desktop: toggles the sidebar column via `.shop-grid.filters-hidden`
 *   - Mobile (<=1199px): opens the sidebar as a fixed overlay via
 *     `.shop-grid.filters-visible` + `body.no-scroll`
 *   - Filter clicks rewrite the URL with a `filter_category` query argument
 *     and navigate — the resulting request is turned into a tax_query by
 *     mosharaf_filter_shop_products_by_category() in shop-archive.php.
 *     Plain links/buttons all the way down, no AJAX.
 *   - Moves `.woocommerce-result-count` between the desktop and mobile
 *     toolbar wrappers as the viewport crosses the 1199px breakpoint.
 *
 * Standalone — only runs when `.filter-toggle-btn`/`.shop-grid` are present,
 * so it stays inert on any page that doesn't use the shop archive layout.
 *
 * @package mosharaf-core
 */

( function ( $ ) {
	'use strict';

	var FILTER_BREAKPOINT = 1199;

	function getUrlParams() {
		var params = {};
		var searchParams = new URLSearchParams( window.location.search );

		searchParams.forEach( function ( value, key ) {
			params[ key ] = value;
		} );

		return params;
	}

	function buildFilterUrl( taxonomy, term ) {
		var params  = getUrlParams();
		var termStr = String( term ).trim();

		if ( termStr ) {
			params[ taxonomy ] = termStr;
		} else {
			delete params[ taxonomy ];
		}

		var baseUrl = window.location.pathname.replace( /\/page\/\d+\/?/, '/' );
		delete params.paged;

		var queryString = Object.keys( params )
			.map( function ( key ) {
				return encodeURIComponent( key ) + '=' + encodeURIComponent( params[ key ] );
			} )
			.join( '&' );

		return queryString ? baseUrl + '?' + queryString : baseUrl;
	}

	function initFilterToggle() {
		var $toggleBtn  = $( '.filter-toggle-btn' );
		var $closeBtn   = $( '.filters-close-btn' );
		var $shopGrid   = $( '.shop-grid' );
		var $btnText    = $toggleBtn.find( '.filter-toggle-text' );
		var $labelText  = $toggleBtn.find( '.filters-label span:last-child' );
		var $iconClose  = $toggleBtn.find( '.filter-icon-close' );
		var $iconShow   = $toggleBtn.find( '.filter-icon-show' );
		var $body       = $( 'body' );

		if ( ! $toggleBtn.length || ! $shopGrid.length ) {
			return;
		}

		function updateLabelForScreenSize() {
			$labelText.text( $( window ).width() <= FILTER_BREAKPOINT ? 'Show Filters' : 'Filters' );
		}

		updateLabelForScreenSize();

		$toggleBtn.on( 'click', function () {
			if ( $( window ).width() > FILTER_BREAKPOINT ) {
				$shopGrid.toggleClass( 'filters-hidden' );

				if ( $shopGrid.hasClass( 'filters-hidden' ) ) {
					$btnText.text( 'Show filters' );
					$iconClose.hide();
					$iconShow.show();
				} else {
					$btnText.text( 'Hide filters' );
					$iconClose.show();
					$iconShow.hide();
				}
			} else {
				$shopGrid.addClass( 'filters-visible' );
				$body.addClass( 'no-scroll' );
			}
		} );

		$closeBtn.on( 'click', function () {
			$shopGrid.removeClass( 'filters-visible' );
			$body.removeClass( 'no-scroll' );
		} );

		$shopGrid.on( 'click', function ( e ) {
			if ( $( e.target ).is( '.shop-grid.filters-visible' ) ) {
				$shopGrid.removeClass( 'filters-visible' );
				$body.removeClass( 'no-scroll' );
			}
		} );

		$( window ).on( 'resize', function () {
			var windowWidth = $( window ).width();
			updateLabelForScreenSize();

			if ( windowWidth > FILTER_BREAKPOINT ) {
				$shopGrid.removeClass( 'filters-visible' );
				$body.removeClass( 'no-scroll' );

				if ( ! $shopGrid.hasClass( 'filters-hidden' ) ) {
					$btnText.text( 'Hide filters' );
					$iconClose.show();
					$iconShow.hide();
				}
			} else {
				$shopGrid.removeClass( 'filters-hidden' );
				$body.removeClass( 'no-scroll' );
				$btnText.text( 'Hide filters' );
				$iconClose.show();
				$iconShow.hide();
			}
		} );
	}

	function initFilterLinks() {
		$( '.filter-link' ).on( 'click', function ( e ) {
			var $link = $( this );

			if ( $link.is( ':disabled' ) ) {
				return false;
			}

			var taxonomy = $link.data( 'taxonomy' );
			var term     = $link.data( 'term' );

			// "All Products" / "Clear Filters" links carry no taxonomy/term
			// data — let them follow their own href.
			if ( ! taxonomy || ! term ) {
				return true;
			}

			e.preventDefault();
			window.location.href = buildFilterUrl( taxonomy, term );
			return false;
		} );
	}

	function initMobileResultCount() {
		var $resultCount    = $( '.woocommerce-result-count' );
		var $desktopWrapper = $( '.result-count-wrapper' );
		var $mobileWrapper  = $( '.result-count-wrapper-mobile' );

		if ( ! $resultCount.length || ! $desktopWrapper.length || ! $mobileWrapper.length ) {
			return;
		}

		function moveResultCount() {
			if ( $( window ).width() <= FILTER_BREAKPOINT ) {
				if ( $resultCount.parent().is( $desktopWrapper ) ) {
					$resultCount.appendTo( $mobileWrapper );
				}
			} else if ( $resultCount.parent().is( $mobileWrapper ) ) {
				$resultCount.appendTo( $desktopWrapper );
			}
		}

		moveResultCount();

		var resizeTimer;
		$( window ).on( 'resize', function () {
			clearTimeout( resizeTimer );
			resizeTimer = setTimeout( moveResultCount, 250 );
		} );
	}

	$( function () {
		initFilterToggle();
		initFilterLinks();
		initMobileResultCount();
	} );

} )( jQuery );

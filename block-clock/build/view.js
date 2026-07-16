/**
 * Front-end tick for the "Bangla Clock" block.
 *
 * Dependency-free by design (auto-enqueued via block.json's "viewScript").
 * Ticks using the visitor's own browser clock/timezone rather than the
 * server-rendered value, which reflects the site timezone at page-load time
 * — the immediate first tick below replaces it before it is ever seen.
 */
( function () {
	'use strict';

	var BN_DIGITS = [ '০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯' ];

	function toBanglaDigits( str ) {
		return String( str ).replace( /[0-9]/g, function ( d ) {
			return BN_DIGITS[ d ];
		} );
	}

	function pad( n ) {
		return n < 10 ? '0' + n : '' + n;
	}

	function render( el, showSeconds ) {
		var now = new Date();
		var parts = [ pad( now.getHours() ), pad( now.getMinutes() ) ];
		if ( showSeconds ) {
			parts.push( pad( now.getSeconds() ) );
		}
		el.textContent = toBanglaDigits( parts.join( ':' ) );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var clocks = document.querySelectorAll( '.ntb-clock' );
		if ( ! clocks.length ) {
			return;
		}

		clocks.forEach( function ( el ) {
			var showSeconds = el.getAttribute( 'data-show-seconds' ) === '1';
			render( el, showSeconds );
			setInterval( function () {
				render( el, showSeconds );
			}, 1000 );
		} );
	} );
} )();

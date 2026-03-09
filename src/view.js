
/**
 * Business Hours Block — Front-End View Script
 *
 * Ensures the current day is highlighted in the weekly schedule
 * and provides any client-side interactivity.
 *
 * @package TelexHoursBlock
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

( function () {
	'use strict';

	/**
	 * Day key map indexed by Date.getDay() value.
	 *
	 * @type {string[]}
	 */
	var dayKeys = [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ];

	/**
	 * Initializes all Business Hours Block instances on the page.
	 *
	 * Finds each block wrapper, determines today's day of the week,
	 * and applies the '--today' modifier class to the corresponding
	 * list item for visual highlighting.
	 *
	 * @return {void}
	 */
	function init() {
		var blocks = document.querySelectorAll( '.wp-block-telex-block-telex-hours-block' );
		var todayIndex = new Date().getDay();
		var todayKey = dayKeys[ todayIndex ];

		blocks.forEach( function ( block ) {
			var items = block.querySelectorAll( '.telex-hours-block__list-item' );

			items.forEach( function ( item ) {
				var dayAttr = item.getAttribute( 'data-day' );
				if ( dayAttr === todayKey ) {
					item.classList.add( 'telex-hours-block__list-item--today' );
				}
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

/**
 * Business Hours Block — Front-End View (Interactivity API)
 *
 * Re-computes the current day key from the visitor's browser clock
 * and toggles "--today" modifier classes on the correct <dt>/<dd>
 * elements. This ensures the today highlight is always accurate,
 * even when the page is served from an HTML cache.
 *
 * The server already renders "--today" classes as the initial state
 * (using the server's clock), so there is no flash on non-cached
 * page loads. On cached pages the Interactivity API corrects the
 * highlight during hydration.
 *
 * @package
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/interactivity-api/
 */

import { store, getContext } from '@wordpress/interactivity';

/**
 * Maps JS Date.getDay() index to the day key used in the block markup.
 */
const DAY_KEYS = [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ];

/**
 * Returns the current day key based on the visitor's local clock.
 *
 * @return {string} Day key string (e.g. 'mon').
 */
function getClientDayKey() {
	return DAY_KEYS[ new Date().getDay() ];
}

store( 'telex/hours-block', {
	state: {
		/**
		 * The current day key, re-evaluated client-side on each access
		 * so that cached pages self-correct.
		 *
		 * @return {string} The current day key.
		 */
		get currentDayKey() {
			return getClientDayKey();
		},

		/**
		 * Whether the current element's day context matches the current day.
		 * Used by data-wp-class directives on both <dt> and <dd>.
		 *
		 * Reads the dayKey from the element's data-wp-context and compares
		 * it against the browser's current day.
		 *
		 * @return {boolean} True if this element represents today.
		 */
		get isDayToday() {
			const ctx = getContext();
			return ctx.dayKey === getClientDayKey();
		},

		/**
		 * Alias for isDayToday — used on <dd> elements.
		 * Both <dt> and <dd> share the same context-based logic.
		 *
		 * @return {boolean} True if this element represents today.
		 */
		get isHoursToday() {
			const ctx = getContext();
			return ctx.dayKey === getClientDayKey();
		},
	},
} );

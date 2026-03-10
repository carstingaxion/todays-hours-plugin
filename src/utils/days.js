/**
 * Day key and label utilities.
 *
 * @package
 */

import { dateI18n } from '@wordpress/date';

/**
 * All day keys in standard order starting from Sunday (index 0).
 *
 * @type {string[]}
 */
export const ALL_DAY_KEYS = [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ];

/**
 * Weekend day keys.
 *
 * @type {string[]}
 */
export const WEEKEND_KEYS = [ 'sun', 'sat' ];

/**
 * Returns day keys and localized labels ordered according to start_of_week.
 *
 * Uses WordPress dateI18n to get properly localized day names that respect
 * the site's language settings.
 *
 * @param {number} startOfWeek The start of week (0=Sunday, 1=Monday, etc.).
 * @return {Array<{key: string, label: string}>} Ordered array of day objects.
 */
export function getOrderedDays( startOfWeek ) {
	const days = [];
	const baseSunday = new Date( '2024-01-07T12:00:00' );

	for ( let i = 0; i < 7; i++ ) {
		const dayOfWeek = ( startOfWeek + i ) % 7;
		const dayDate = new Date( baseSunday );
		dayDate.setDate( baseSunday.getDate() + dayOfWeek );

		days.push( {
			key: ALL_DAY_KEYS[ dayOfWeek ],
			label: dateI18n( 'l', dayDate ),
		} );
	}

	return days;
}

/**
 * Creates a default hours object with all days having one empty slot.
 *
 * @return {Object} Hours object keyed by day key.
 */
export function getDefaultHours() {
	const hours = {};
	ALL_DAY_KEYS.forEach( ( key ) => {
		hours[ key ] = [ { open: '', close: '' } ];
	} );
	return hours;
}

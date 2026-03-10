/**
 * Date parsing and comparison utilities.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';

/**
 * Parses a date string to a Date object at midnight.
 *
 * @param {string} dateStr Date string in YYYY-MM-DD format.
 * @return {Date|null} Parsed date or null if invalid.
 */
export function parseDate( dateStr ) {
	if ( ! dateStr ) {
		return null;
	}
	const parts = dateStr.split( '-' );
	if ( parts.length !== 3 ) {
		return null;
	}
	return new Date(
		parseInt( parts[ 0 ], 10 ),
		parseInt( parts[ 1 ], 10 ) - 1,
		parseInt( parts[ 2 ], 10 )
	);
}

/**
 * Checks if a date falls within a range (inclusive).
 *
 * @param {Date} testDate  The date to test.
 * @param {Date} beginDate The start of the range.
 * @param {Date} endDate   The end of the range.
 * @return {boolean} True if testDate is within the range.
 */
export function isDateInRange( testDate, beginDate, endDate ) {
	const t =
		testDate.getFullYear() * 10000 +
		( testDate.getMonth() + 1 ) * 100 +
		testDate.getDate();
	const b =
		beginDate.getFullYear() * 10000 +
		( beginDate.getMonth() + 1 ) * 100 +
		beginDate.getDate();
	const e =
		endDate.getFullYear() * 10000 +
		( endDate.getMonth() + 1 ) * 100 +
		endDate.getDate();
	return t >= b && t <= e;
}

/**
 * Determines whether a holiday date string has a year.
 *
 * @param {string} dateStr Date string to check.
 * @return {boolean} True if the date string includes a year.
 */
export function dateHasYear( dateStr ) {
	if ( ! dateStr ) {
		return false;
	}
	return dateStr.length > 5;
}

/**
 * Parses a date value into month and day strings.
 *
 * @param {string} dateStr Date string in YYYY-MM-DD or MM-DD format.
 * @return {{month: string, day: string}} Parsed month and day.
 */
export function parseMonthDay( dateStr ) {
	if ( ! dateStr ) {
		return { month: '01', day: '01' };
	}
	const parts = dateStr.split( '-' );
	if ( parts.length === 3 ) {
		return { month: parts[ 1 ], day: parts[ 2 ] };
	}
	if ( parts.length === 2 ) {
		return { month: parts[ 0 ], day: parts[ 1 ] };
	}
	return { month: '01', day: '01' };
}

/**
 * Generates month options with localized labels.
 *
 * @return {Array<{value: string, label: string}>} Month options.
 */
export function getMonthOptions() {
	return [
		{ value: '01', label: __( 'January' ) },
		{ value: '02', label: __( 'February' ) },
		{ value: '03', label: __( 'March' ) },
		{ value: '04', label: __( 'April' ) },
		{ value: '05', label: __( 'May' ) },
		{ value: '06', label: __( 'June' ) },
		{ value: '07', label: __( 'July' ) },
		{ value: '08', label: __( 'August' ) },
		{ value: '09', label: __( 'September' ) },
		{ value: '10', label: __( 'October' ) },
		{ value: '11', label: __( 'November' ) },
		{ value: '12', label: __( 'December' ) },
	];
}

/**
 * Generates day options for a given month.
 *
 * @param {string} month Two-digit month string (01-12).
 * @return {Array<{value: string, label: string}>} Day options.
 */
export function getDayOptions( month ) {
	const daysInMonth = {
		'01': 31,
		'02': 29,
		'03': 31,
		'04': 30,
		'05': 31,
		'06': 30,
		'07': 31,
		'08': 31,
		'09': 30,
		10: 31,
		11: 30,
		12: 31,
	};
	const count = daysInMonth[ month ] || 31;
	const options = [];
	for ( let d = 1; d <= count; d++ ) {
		const val = String( d ).padStart( 2, '0' );
		options.push( { value: val, label: String( d ) } );
	}
	return options;
}

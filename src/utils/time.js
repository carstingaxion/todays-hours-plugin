/**
 * Time formatting utilities.
 *
 * @package TelexHoursBlock
 */

import { __ } from '@wordpress/i18n';

/**
 * Applies friendly twelve labels to a time string.
 *
 * @param {string}  time            The time string.
 * @param {boolean} friendlyTwelves Whether to apply friendly labels.
 * @return {string} The processed time string.
 */
export function applyFriendlyTwelves( time, friendlyTwelves ) {
	if ( ! friendlyTwelves || ! time ) {
		return time;
	}
	const lower = time.toLowerCase().replace( /\s/g, '' );
	if ( lower === '12:00am' ) {
		return __( 'Midnight', 'telex-hours-block' );
	}
	if ( lower === '12:00pm' ) {
		return __( 'Noon', 'telex-hours-block' );
	}
	return time;
}

/**
 * Formats a time string using the WordPress time_format setting.
 *
 * @param {string} timeStr    The input time string (e.g. "8:00 AM").
 * @param {string} timeFormat The PHP-style time format string.
 * @return {string} The formatted time string.
 */
export function formatTimeWithSiteFormat( timeStr, timeFormat ) {
	if ( ! timeStr || ! timeFormat ) {
		return timeStr;
	}

	const parsed = new Date( '2000-01-01 ' + timeStr );
	if ( isNaN( parsed.getTime() ) ) {
		return timeStr;
	}

	const hours = parsed.getHours();
	const minutes = parsed.getMinutes();
	const seconds = parsed.getSeconds();

	let result = '';
	let i = 0;
	while ( i < timeFormat.length ) {
		const ch = timeFormat[ i ];

		if ( ch === '\\' && i + 1 < timeFormat.length ) {
			result += timeFormat[ i + 1 ];
			i += 2;
			continue;
		}

		switch ( ch ) {
			case 'g':
				result += ( hours % 12 ) || 12;
				break;
			case 'G':
				result += hours;
				break;
			case 'h':
				result += String( ( hours % 12 ) || 12 ).padStart( 2, '0' );
				break;
			case 'H':
				result += String( hours ).padStart( 2, '0' );
				break;
			case 'i':
				result += String( minutes ).padStart( 2, '0' );
				break;
			case 's':
				result += String( seconds ).padStart( 2, '0' );
				break;
			case 'a':
				result += hours >= 12 ? 'pm' : 'am';
				break;
			case 'A':
				result += hours >= 12 ? 'PM' : 'AM';
				break;
			default:
				result += ch;
				break;
		}
		i++;
	}

	return result;
}

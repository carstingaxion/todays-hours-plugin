/**
 * Time formatting utilities.
 *
 * Handles time strings in both 24-hour (HH:MM) format from native
 * <input type="time"> elements and legacy 12-hour (g:i A) format.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';

/**
 * Parses a time string into hours and minutes.
 *
 * Accepts:
 * - 24-hour format: "08:00", "17:30", "00:00"
 * - 12-hour format: "8:00 AM", "5:30 PM", "12:00 AM"
 *
 * @param {string} timeStr The input time string.
 * @return {{ hours: number, minutes: number }|null} Parsed time or null if invalid.
 */
export function parseTime( timeStr ) {
	if ( ! timeStr || typeof timeStr !== 'string' ) {
		return null;
	}

	const trimmed = timeStr.trim();

	// Try 24-hour format first: HH:MM or H:MM.
	const match24 = trimmed.match( /^(\d{1,2}):(\d{2})$/ );
	if ( match24 ) {
		const h = parseInt( match24[ 1 ], 10 );
		const m = parseInt( match24[ 2 ], 10 );
		if ( h >= 0 && h <= 23 && m >= 0 && m <= 59 ) {
			return { hours: h, minutes: m };
		}
	}

	// Try 12-hour format: H:MM AM/PM or HH:MM AM/PM.
	const match12 = trimmed.match( /^(\d{1,2}):(\d{2})\s*(am|pm)$/i );
	if ( match12 ) {
		let h = parseInt( match12[ 1 ], 10 );
		const m = parseInt( match12[ 2 ], 10 );
		const period = match12[ 3 ].toLowerCase();

		if ( h < 1 || h > 12 || m < 0 || m > 59 ) {
			return null;
		}

		if ( period === 'am' ) {
			if ( h === 12 ) {
				h = 0;
			}
		} else if ( h !== 12 ) {
			h += 12;
		}

		return { hours: h, minutes: m };
	}

	return null;
}

/**
 * Applies friendly twelve labels to a time string.
 *
 * Handles both 24-hour format ("00:00", "12:00") and
 * 12-hour format ("12:00 AM", "12:00 PM").
 *
 * @param {string}  time            The time string.
 * @param {boolean} friendlyTwelves Whether to apply friendly labels.
 * @return {string} The processed time string.
 */
export function applyFriendlyTwelves( time, friendlyTwelves ) {
	if ( ! friendlyTwelves || ! time ) {
		return time;
	}

	const parsed = parseTime( time );
	if ( ! parsed ) {
		return time;
	}

	if ( parsed.hours === 0 && parsed.minutes === 0 ) {
		return __( 'Midnight', 'telex-hours-block' );
	}
	if ( parsed.hours === 12 && parsed.minutes === 0 ) {
		return __( 'Noon', 'telex-hours-block' );
	}

	return time;
}

/**
 * Formats a time string using the WordPress time_format setting.
 *
 * Accepts both 24-hour (HH:MM) and 12-hour (g:i A) input formats.
 *
 * @param {string} timeStr    The input time string (e.g. "08:00" or "8:00 AM").
 * @param {string} timeFormat The PHP-style time format string.
 * @return {string} The formatted time string.
 */
export function formatTimeWithSiteFormat( timeStr, timeFormat ) {
	if ( ! timeStr || ! timeFormat ) {
		return timeStr;
	}

	const parsed = parseTime( timeStr );
	if ( ! parsed ) {
		return timeStr;
	}

	const { hours, minutes } = parsed;
	const seconds = 0;

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
				result += hours % 12 || 12;
				break;
			case 'G':
				result += hours;
				break;
			case 'h':
				result += String( hours % 12 || 12 ).padStart( 2, '0' );
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

/**
 * Converts a time string to 24-hour HH:MM format.
 *
 * Accepts both 24-hour and 12-hour input. If the input is already
 * in HH:MM format, validates and returns it normalized.
 *
 * @param {string} timeStr The input time string.
 * @return {string} Time in HH:MM format, or empty string if invalid.
 */
export function to24h( timeStr ) {
	if ( ! timeStr ) {
		return '';
	}

	const parsed = parseTime( timeStr );
	if ( ! parsed ) {
		return '';
	}

	return (
		String( parsed.hours ).padStart( 2, '0' ) +
		':' +
		String( parsed.minutes ).padStart( 2, '0' )
	);
}

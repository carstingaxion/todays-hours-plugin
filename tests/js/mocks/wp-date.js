/**
 * Mock for @wordpress/date.
 *
 * Returns localized-style day names from dateI18n when format is 'l'.
 *
 * @package TelexHoursBlock
 */

const DAY_NAMES = [
	'Sunday', 'Monday', 'Tuesday', 'Wednesday',
	'Thursday', 'Friday', 'Saturday',
];

function dateI18n( format, dateValue ) {
	const d = dateValue instanceof Date ? dateValue : new Date( dateValue );
	if ( format === 'l' ) {
		return DAY_NAMES[ d.getDay() ];
	}
	if ( format === 'F j, Y' ) {
		return d.toLocaleDateString( 'en-US', {
			year: 'numeric',
			month: 'long',
			day: 'numeric',
		} );
	}
	return d.toISOString();
}

module.exports = { dateI18n };

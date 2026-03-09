/**
 * Sorting utilities for seasons and holidays.
 *
 * @package TelexHoursBlock
 */

/**
 * Converts a beginDate string into a sortable key.
 *
 * Handles three formats:
 * - "YYYY-MM-DD" (year-specific) — returned as-is.
 * - "MM-DD" (recurring/yearless) — prefixed with "9999-" so recurring items sort after year-specific ones.
 * - Empty or missing — returns "zzzz" to sort last.
 *
 * @param {string} beginDate The begin date string.
 * @return {string} A string suitable for lexicographic sorting.
 */
export function getSortableBeginDate( beginDate ) {
	if ( ! beginDate ) {
		return 'zzzz';
	}
	// Year-specific: "YYYY-MM-DD" (length > 5).
	if ( beginDate.length > 5 ) {
		return beginDate;
	}
	// Recurring: "MM-DD" — prefix with 9999 so they sort after dated entries.
	return '9999-' + beginDate;
}

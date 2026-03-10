/**
 * Time slot normalization utilities.
 *
 * @package
 */

/**
 * Normalizes day data to always be an array of slots.
 * Handles legacy { open, close } format.
 *
 * @param {*} dayData Raw day data from season hours.
 * @return {Array<{open: string, close: string}>} Normalized array of slots.
 */
export function normalizeSlots( dayData ) {
	if ( ! dayData ) {
		return [ { open: '', close: '' } ];
	}
	if ( Array.isArray( dayData ) ) {
		if ( dayData.length === 0 ) {
			return [ { open: '', close: '' } ];
		}
		if (
			typeof dayData[ 0 ] === 'object' &&
			dayData[ 0 ] !== null &&
			( 'open' in dayData[ 0 ] || 'close' in dayData[ 0 ] )
		) {
			return dayData;
		}
	}
	if (
		typeof dayData === 'object' &&
		( 'open' in dayData || 'close' in dayData )
	) {
		return [ { open: dayData.open || '', close: dayData.close || '' } ];
	}
	return [ { open: '', close: '' } ];
}

/**
 * Normalizes holiday data to use the 'slots' array format.
 * Handles legacy 'openTime'/'closeTime' fields.
 *
 * @param {Object} holiday Holiday data object.
 * @return {Array<{open: string, close: string}>} Normalized array of slots.
 */
export function normalizeHolidaySlots( holiday ) {
	if ( holiday.slots && Array.isArray( holiday.slots ) ) {
		return holiday.slots.length > 0 ? holiday.slots : [];
	}
	if ( holiday.openTime ) {
		return [
			{ open: holiday.openTime || '', close: holiday.closeTime || '' },
		];
	}
	return [];
}

/**
 * Checks if any slot has a non-empty open time.
 *
 * @param {Array<{open: string, close: string}>} slots Array of slot objects.
 * @return {boolean} True if at least one slot has a non-empty open time.
 */
export function slotsHaveOpen( slots ) {
	return slots.some( ( s ) => s.open && s.open.trim() !== '' );
}

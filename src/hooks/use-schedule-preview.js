/**
 * Custom hook that computes the schedule preview for the editor.
 *
 * @package
 */

import { useMemo } from '@wordpress/element';
import { ALL_DAY_KEYS } from '../utils/days';
import { parseDate, isDateInRange } from '../utils/dates';
import { normalizeSlots, normalizeHolidaySlots } from '../utils/slots';

/**
 * Finds a matching holiday for a given Date object.
 *
 * @param {Array} holidays Array of holiday objects.
 * @param {Date}  dateObj  The date to check.
 * @return {Object|null} The matching holiday or null.
 */
export function findHolidayForDate( holidays, dateObj ) {
	const md =
		String( dateObj.getMonth() + 1 ).padStart( 2, '0' ) +
		'-' +
		String( dateObj.getDate() ).padStart( 2, '0' );

	for ( const h of holidays ) {
		const beginStr = h.beginDate || '';
		const endStr = h.endDate || '';
		if ( ! beginStr || ! endStr ) {
			continue;
		}

		const beginHasYear = beginStr.length > 5;
		const endHasYear = endStr.length > 5;

		if ( beginHasYear && endHasYear ) {
			const begin = parseDate( beginStr );
			const end = parseDate( endStr );
			if ( begin && end && isDateInRange( dateObj, begin, end ) ) {
				return h;
			}
		} else if ( ! beginHasYear && ! endHasYear ) {
			if ( beginStr <= endStr ) {
				if ( md >= beginStr && md <= endStr ) {
					return h;
				}
			} else if ( md >= beginStr || md <= endStr ) {
				return h;
			}
		}
	}
	return null;
}

/**
 * Computes a preview of today's schedule based on current seasons and holidays.
 *
 * @param {Array} seasons  Array of season objects.
 * @param {Array} holidays Array of holiday objects.
 * @return {Object} Preview data with currentSeason, currentHoliday, slots, holidayName, dayKey, today, and holidays.
 */
export function useSchedulePreview( seasons, holidays ) {
	return useMemo( () => {
		const now = new Date();
		const today = new Date(
			now.getFullYear(),
			now.getMonth(),
			now.getDate()
		);
		const dayKey = ALL_DAY_KEYS[ today.getDay() ];

		const currentHoliday = findHolidayForDate( holidays, today );

		let currentSeason = null;
		for ( const s of seasons ) {
			const begin = parseDate( s.beginDate );
			const end = parseDate( s.endDate );
			if ( begin && end && isDateInRange( today, begin, end ) ) {
				currentSeason = s;
				break;
			}
		}

		let slots = [];
		let holidayName = '';

		if ( currentHoliday ) {
			slots = normalizeHolidaySlots( currentHoliday );
			holidayName = currentHoliday.name || '';
		} else if ( currentSeason && currentSeason.hours?.[ dayKey ] ) {
			slots = normalizeSlots( currentSeason.hours[ dayKey ] );
		}

		return {
			currentSeason,
			currentHoliday,
			slots,
			holidayName,
			dayKey,
			today,
			holidays,
		};
	}, [ seasons, holidays ] );
}

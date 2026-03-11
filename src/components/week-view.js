/**
 * WeekView component — Renders the full weekly schedule preview in the editor.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import { Spinner } from '@wordpress/components';
import {
	normalizeSlots,
	normalizeHolidaySlots,
	slotsHaveOpen,
} from '../utils/slots';
import { WEEKEND_KEYS, ALL_DAY_KEYS } from '../utils/days';
import { findHolidayForDate } from '../hooks/use-schedule-preview';

/**
 * Renders the full week schedule in the editor preview.
 *
 * @param {Object}   props                  Component props.
 * @param {boolean}  props.hasLoaded        Whether settings have loaded.
 * @param {Object}   props.preview          Preview data from useSchedulePreview.
 * @param {Array}    props.orderedDays      Ordered day objects.
 * @param {boolean}  props.hideWeekends     Whether to hide weekend days.
 * @param {boolean}  props.showReasonClosed Whether to show closed reason.
 * @param {Function} props.renderSlots      Function to render slots as JSX.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */
export default function WeekView( {
	hasLoaded,
	preview,
	orderedDays,
	hideWeekends,
	showReasonClosed,
	renderSlots,
} ) {
	if ( ! hasLoaded ) {
		return <Spinner />;
	}

	if ( ! preview.currentSeason ) {
		return (
			<p className="telex-hours-block__message">
				{ __( 'No active season for today.', 'telex-hours-block' ) }
			</p>
		);
	}

	const todayDate = preview.today;
	const todayDayIndex = ALL_DAY_KEYS.indexOf( preview.dayKey );
	const holidays = preview.holidays || [];

	/**
	 * Computes the Date object for a given day key relative to today.
	 *
	 * @param {string} dk Day key (e.g. 'mon').
	 * @return {Date} Date object for that day of the current week.
	 */
	function getDateForDay( dk ) {
		const targetIndex = ALL_DAY_KEYS.indexOf( dk );
		const diff = targetIndex - todayDayIndex;
		const d = new Date( todayDate );
		d.setDate( d.getDate() + diff );
		return d;
	}

	return (
		<dl className="telex-hours-block__list">
			{ orderedDays
				.filter(
					( { key: dk } ) =>
						! hideWeekends || ! WEEKEND_KEYS.includes( dk )
				)
				.map( ( { key: dk, label: dayLabel } ) => {
					const isToday = dk === preview.dayKey;
					const dayDate = getDateForDay( dk );
					const dayHoliday = findHolidayForDate( holidays, dayDate );

					let daySlots = [];
					if ( dayHoliday ) {
						daySlots = normalizeHolidaySlots( dayHoliday );
					} else if ( preview.currentSeason.hours?.[ dk ] ) {
						daySlots = normalizeSlots(
							preview.currentSeason.hours[ dk ]
						);
					}

					const hasOpen = slotsHaveOpen( daySlots );
					const isClosed = ! hasOpen;

					const dtClasses = [
						'telex-hours-block__day',
						isToday ? 'telex-hours-block__day--today' : '',
					]
						.filter( Boolean )
						.join( ' ' );

					const ddClasses = [
						'telex-hours-block__hours',
						isToday ? 'telex-hours-block__hours--today' : '',
						isClosed ? 'telex-hours-block__hours--closed' : '',
					]
						.filter( Boolean )
						.join( ' ' );

					let closedLabel = __( 'Closed', 'telex-hours-block' );
					if (
						isClosed &&
						showReasonClosed &&
						dayHoliday &&
						dayHoliday.name
					) {
						closedLabel =
							__( 'Closed for', 'telex-hours-block' ) + ' ' +
							dayHoliday.name;
					}

					return (
						<div key={ dk } className="telex-hours-block__list-row">
							<dt className={ dtClasses } data-day={ dk }>
								{ dayLabel }
							</dt>
							<dd className={ ddClasses } data-day={ dk }>
								{ isClosed
									? closedLabel
									: renderSlots( daySlots ) }
							</dd>
						</div>
					);
				} ) }
		</dl>
	);
}

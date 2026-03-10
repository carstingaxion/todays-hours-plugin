/**
 * DayView component — Renders today's hours only in the editor preview.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import { Spinner } from '@wordpress/components';
import { slotsHaveOpen } from '../utils/slots';
import { WEEKEND_KEYS } from '../utils/days';

/**
 * Renders the "today only" view in the editor preview.
 *
 * @param {Object}   props                  Component props.
 * @param {boolean}  props.hasLoaded        Whether settings have loaded.
 * @param {Object}   props.preview          Preview data from useSchedulePreview.
 * @param {boolean}  props.hideWeekends     Whether to hide weekend days.
 * @param {boolean}  props.showReasonClosed Whether to show closed reason.
 * @param {Function} props.renderSlots      Function to render slots as JSX.
 * @return {import('@wordpress/element').WPElement|null} Rendered component.
 */
export default function DayView( {
	hasLoaded,
	preview,
	hideWeekends,
	showReasonClosed,
	renderSlots,
} ) {
	if ( ! hasLoaded ) {
		return <Spinner />;
	}

	const { slots, holidayName, dayKey } = preview;

	if ( hideWeekends && WEEKEND_KEYS.includes( dayKey ) ) {
		return null;
	}

	const hasOpen = slotsHaveOpen( slots );

	if ( ! hasOpen ) {
		let closedText = __( 'Closed Today', 'telex-hours-block' );
		if ( showReasonClosed && holidayName ) {
			closedText = __( 'Closed for', 'telex-hours-block' ) + holidayName;
		}
		return (
			<p className="telex-hours-block__today-hours telex-hours-block__today-hours--closed">
				{ closedText }
			</p>
		);
	}

	return (
		<p className="telex-hours-block__today-hours">
			{ renderSlots( slots ) }
		</p>
	);
}

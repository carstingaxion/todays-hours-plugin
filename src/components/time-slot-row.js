/**
 * TimeSlotRow component — Editable row for a single time slot.
 *
 * Uses native HTML5 time inputs that render according to the browser's
 * locale and the operating system's time format preferences.
 *
 * @package TelexHoursBlock
 */

import { __ } from '@wordpress/i18n';
import {
	Button,
	Flex,
	FlexBlock,
	FlexItem,
	BaseControl,
} from '@wordpress/components';

/**
 * Renders an editable row for a single time slot (open/close pair).
 *
 * Uses native <input type="time"> elements which store values in HH:MM
 * (24-hour) format and render in the user's locale on the browser side.
 *
 * @param {Object}   props           Component props.
 * @param {Object}   props.slot      The slot object with open and close strings.
 * @param {number}   props.slotIndex Index of this slot within its parent array.
 * @param {Function} props.onUpdate  Callback: (slotIndex, timeKey, value) => void.
 * @param {Function} props.onRemove  Callback: (slotIndex) => void.
 * @param {boolean}  props.canRemove Whether the remove button should be shown.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */
export default function TimeSlotRow( { slot, slotIndex, onUpdate, onRemove, canRemove } ) {
	const openId = `time-slot-open-${ slotIndex }`;
	const closeId = `time-slot-close-${ slotIndex }`;

	return (
		<Flex align="flex-end" gap={ 2 }>
			<FlexBlock>
				<BaseControl
					id={ openId }
					label={ slotIndex === 0 ? __( 'Open', 'telex-hours-block' ) : '' }
					__nextHasNoMarginBottom
				>
					<input
						id={ openId }
						type="time"
						className="components-text-control__input"
						value={ slot.open || '' }
						onChange={ ( e ) => onUpdate( slotIndex, 'open', e.target.value ) }
					/>
				</BaseControl>
			</FlexBlock>
			<FlexBlock>
				<BaseControl
					id={ closeId }
					label={ slotIndex === 0 ? __( 'Close', 'telex-hours-block' ) : '' }
					__nextHasNoMarginBottom
				>
					<input
						id={ closeId }
						type="time"
						className="components-text-control__input"
						value={ slot.close || '' }
						onChange={ ( e ) => onUpdate( slotIndex, 'close', e.target.value ) }
					/>
				</BaseControl>
			</FlexBlock>
			{ canRemove && (
				<FlexItem>
					<Button
						icon="minus"
						isDestructive
						size="small"
						label={ __( 'Remove time slot', 'telex-hours-block' ) }
						onClick={ () => onRemove( slotIndex ) }
					/>
				</FlexItem>
			) }
		</Flex>
	);
}

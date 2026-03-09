/**
 * TimeSlotRow component — Editable row for a single time slot.
 *
 * @package TelexHoursBlock
 */

import { __ } from '@wordpress/i18n';
import {
	TextControl,
	Button,
	Flex,
	FlexBlock,
	FlexItem,
} from '@wordpress/components';

/**
 * Renders an editable row for a single time slot (open/close pair).
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
	return (
		<Flex align="flex-end" gap={ 2 }>
			<FlexBlock>
				<TextControl
					label={ slotIndex === 0 ? __( 'Open', 'telex-hours-block' ) : '' }
					placeholder="8:00 AM"
					value={ slot.open || '' }
					onChange={ ( val ) => onUpdate( slotIndex, 'open', val ) }
					__nextHasNoMarginBottom
				/>
			</FlexBlock>
			<FlexBlock>
				<TextControl
					label={ slotIndex === 0 ? __( 'Close', 'telex-hours-block' ) : '' }
					placeholder="5:00 PM"
					value={ slot.close || '' }
					onChange={ ( val ) => onUpdate( slotIndex, 'close', val ) }
					__nextHasNoMarginBottom
				/>
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

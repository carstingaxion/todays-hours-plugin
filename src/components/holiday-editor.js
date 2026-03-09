/**
 * HolidayEditor component — Inspector panel editor for a single holiday.
 *
 * @package TelexHoursBlock
 */

import { __ } from '@wordpress/i18n';
import {
	TextControl,
	ToggleControl,
	Button,
	Flex,
	FlexBlock,
	FlexItem,
	Icon,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalText as Text,
} from '@wordpress/components';
import { useState, useCallback, useRef } from '@wordpress/element';
import { dateHasYear, parseMonthDay } from '../utils/dates';
import { normalizeHolidaySlots } from '../utils/slots';
import TimeSlotRow from './time-slot-row';
import MonthDayPicker from './month-day-picker';

/**
 * Renders the editor for a single holiday in the inspector panel.
 *
 * @param {Object}   props           Component props.
 * @param {Object}   props.holiday   Holiday data object.
 * @param {number}   props.index     Index of this holiday in the array.
 * @param {Function} props.onChange  Callback: (index, updatedHoliday) => void.
 * @param {Function} props.onRemove Callback: (index) => void.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */
export default function HolidayEditor( { holiday, index, onChange, onRemove } ) {
	const [ isOpen, setIsOpen ] = useState( false );

	const isRecurring = ! dateHasYear( holiday.beginDate ) && ! dateHasYear( holiday.endDate );

	const updateField = useCallback(
		( field, value ) => {
			onChange( index, { ...holiday, [ field ]: value } );
		},
		[ index, holiday, onChange ]
	);

	const toggleRecurring = useCallback(
		( recurring ) => {
			const updated = { ...holiday };
			if ( recurring ) {
				if ( dateHasYear( updated.beginDate ) ) {
					const { month, day: d } = parseMonthDay( updated.beginDate );
					updated.beginDate = month + '-' + d;
				}
				if ( dateHasYear( updated.endDate ) ) {
					const { month, day: d } = parseMonthDay( updated.endDate );
					updated.endDate = month + '-' + d;
				}
			} else {
				const year = new Date().getFullYear();
				if ( ! dateHasYear( updated.beginDate ) && updated.beginDate ) {
					updated.beginDate = year + '-' + updated.beginDate;
				}
				if ( ! dateHasYear( updated.endDate ) && updated.endDate ) {
					updated.endDate = year + '-' + updated.endDate;
				}
			}
			onChange( index, updated );
		},
		[ index, holiday, onChange ]
	);

	const holidaySlots = normalizeHolidaySlots( holiday );
	const currentSlots = holidaySlots.length > 0 ? holidaySlots : [ { open: '', close: '' } ];

	const updateSlotTime = useCallback(
		( slotIndex, timeKey, value ) => {
			const updated = currentSlots.map( ( s, si ) =>
				si === slotIndex ? { ...s, [ timeKey ]: value } : s
			);
			onChange( index, { ...holiday, slots: updated, openTime: undefined, closeTime: undefined } );
		},
		[ index, holiday, currentSlots, onChange ]
	);

	const addSlot = useCallback( () => {
		const updated = [ ...currentSlots, { open: '', close: '' } ];
		onChange( index, { ...holiday, slots: updated, openTime: undefined, closeTime: undefined } );
	}, [ index, holiday, currentSlots, onChange ] );

	const removeSlot = useCallback(
		( slotIndex ) => {
			const updated = currentSlots.filter( ( _, si ) => si !== slotIndex );
			onChange( index, {
				...holiday,
				slots: updated.length > 0 ? updated : [ { open: '', close: '' } ],
				openTime: undefined,
				closeTime: undefined,
			} );
		},
		[ index, holiday, currentSlots, onChange ]
	);

	return (
		<div className="telex-hours-block-inspector__holiday">
			<Flex align="center" justify="space-between">
				<FlexBlock>
					<Button
						variant="link"
						onClick={ () => setIsOpen( ! isOpen ) }
						className="telex-hours-block-inspector__holiday-toggle"
					>
						<Icon icon={ isOpen ? 'arrow-up-alt2' : 'arrow-down-alt2' } />
						<span>
							{ holiday.name ||
								( __( 'Holiday', 'telex-hours-block' ) + ' ' + ( index + 1 ) ) }
						</span>
					</Button>
				</FlexBlock>
				<FlexItem>
					<Button
						variant="tertiary"
						isDestructive
						size="small"
						onClick={ () => onRemove( index ) }
						label={ __( 'Remove holiday', 'telex-hours-block' ) }
						icon="trash"
					/>
				</FlexItem>
			</Flex>
			{ isOpen && (
				<div className="telex-hours-block-inspector__holiday-details">
					<TextControl
						label={ __( 'Name', 'telex-hours-block' ) }
						value={ holiday.name }
						onChange={ ( val ) => updateField( 'name', val ) }
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __( 'Repeats every year', 'telex-hours-block' ) }
						checked={ isRecurring }
						onChange={ toggleRecurring }
						__nextHasNoMarginBottom
					/>
					{ isRecurring ? (
						<Flex>
							<FlexBlock>
								<MonthDayPicker
									label={ __( 'Begin Date', 'telex-hours-block' ) }
									value={ holiday.beginDate || '01-01' }
									onChange={ ( val ) => updateField( 'beginDate', val ) }
								/>
							</FlexBlock>
							<FlexBlock>
								<MonthDayPicker
									label={ __( 'End Date', 'telex-hours-block' ) }
									value={ holiday.endDate || '01-01' }
									onChange={ ( val ) => updateField( 'endDate', val ) }
								/>
							</FlexBlock>
						</Flex>
					) : (
						<Flex>
							<FlexBlock>
								<TextControl
									label={ __( 'Begin Date', 'telex-hours-block' ) }
									type="date"
									value={ holiday.beginDate }
									onChange={ ( val ) => updateField( 'beginDate', val ) }
									__nextHasNoMarginBottom
								/>
							</FlexBlock>
							<FlexBlock>
								<TextControl
									label={ __( 'End Date', 'telex-hours-block' ) }
									type="date"
									value={ holiday.endDate }
									onChange={ ( val ) => updateField( 'endDate', val ) }
									__nextHasNoMarginBottom
								/>
							</FlexBlock>
						</Flex>
					) }
					<Text
						variant="muted"
						size="11"
						upperCase
						weight={ 500 }
					>
						{ __( 'Leave all slots blank if closed all day', 'telex-hours-block' ) }
					</Text>
					{ currentSlots.map( ( slot, si ) => (
						<TimeSlotRow
							key={ si }
							slot={ slot }
							slotIndex={ si }
							onUpdate={ updateSlotTime }
							onRemove={ removeSlot }
							canRemove={ currentSlots.length > 1 }
						/>
					) ) }
					<Button
						variant="link"
						size="small"
						onClick={ addSlot }
						className="telex-hours-block-inspector__add-slot"
					>
						{ __( '+ Add time slot', 'telex-hours-block' ) }
					</Button>
				</div>
			) }
		</div>
	);
}

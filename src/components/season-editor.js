/**
 * SeasonEditor component — Inspector panel editor for a single season.
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
	Icon,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalText as Text,
} from '@wordpress/components';
import { useState, useCallback } from '@wordpress/element';
import { normalizeSlots } from '../utils/slots';
import TimeSlotRow from './time-slot-row';

/**
 * Renders the editor for a single season in the inspector panel.
 *
 * @param {Object}   props             Component props.
 * @param {Object}   props.season      Season data object.
 * @param {number}   props.index       Index of this season in the array.
 * @param {Array}    props.orderedDays Ordered array of {key, label} day objects.
 * @param {Function} props.onChange    Callback: (index, updatedSeason) => void.
 * @param {Function} props.onRemove   Callback: (index) => void.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */
export default function SeasonEditor( { season, index, orderedDays, onChange, onRemove } ) {
	const [ isOpen, setIsOpen ] = useState( false );

	const updateField = useCallback(
		( field, value ) => {
			onChange( index, { ...season, [ field ]: value } );
		},
		[ index, season, onChange ]
	);

	const updateSlotTime = useCallback(
		( dayKey, slotIndex, timeKey, value ) => {
			const currentSlots = normalizeSlots( season.hours?.[ dayKey ] );
			const updatedSlots = currentSlots.map( ( s, si ) =>
				si === slotIndex ? { ...s, [ timeKey ]: value } : s
			);
			const updatedHours = {
				...season.hours,
				[ dayKey ]: updatedSlots,
			};
			onChange( index, { ...season, hours: updatedHours } );
		},
		[ index, season, onChange ]
	);

	const addSlot = useCallback(
		( dayKey ) => {
			const currentSlots = normalizeSlots( season.hours?.[ dayKey ] );
			const updatedSlots = [ ...currentSlots, { open: '', close: '' } ];
			const updatedHours = {
				...season.hours,
				[ dayKey ]: updatedSlots,
			};
			onChange( index, { ...season, hours: updatedHours } );
		},
		[ index, season, onChange ]
	);

	const removeSlot = useCallback(
		( dayKey, slotIndex ) => {
			const currentSlots = normalizeSlots( season.hours?.[ dayKey ] );
			const updatedSlots = currentSlots.filter( ( _, si ) => si !== slotIndex );
			const updatedHours = {
				...season.hours,
				[ dayKey ]: updatedSlots.length > 0 ? updatedSlots : [ { open: '', close: '' } ],
			};
			onChange( index, { ...season, hours: updatedHours } );
		},
		[ index, season, onChange ]
	);

	return (
		<div className="telex-hours-block-inspector__season">
			<Flex align="center" justify="space-between">
				<FlexBlock>
					<Button
						variant="link"
						onClick={ () => setIsOpen( ! isOpen ) }
						className="telex-hours-block-inspector__season-toggle"
					>
						<Icon icon={ isOpen ? 'arrow-up-alt2' : 'arrow-down-alt2' } />
						<span>
							{ season.name ||
								( __( 'Season', 'telex-hours-block' ) + ' ' + ( index + 1 ) ) }
						</span>
					</Button>
				</FlexBlock>
				<FlexItem>
					<Button
						variant="tertiary"
						isDestructive
						size="small"
						onClick={ () => onRemove( index ) }
						label={ __( 'Remove season', 'telex-hours-block' ) }
						icon="trash"
					/>
				</FlexItem>
			</Flex>
			{ isOpen && (
				<div className="telex-hours-block-inspector__season-details">
					<TextControl
						label={ __( 'Name', 'telex-hours-block' ) }
						value={ season.name }
						onChange={ ( val ) => updateField( 'name', val ) }
						__nextHasNoMarginBottom
					/>
					<Flex>
						<FlexBlock>
							<TextControl
								label={ __( 'Begin Date', 'telex-hours-block' ) }
								type="date"
								value={ season.beginDate }
								onChange={ ( val ) => updateField( 'beginDate', val ) }
								__nextHasNoMarginBottom
							/>
						</FlexBlock>
						<FlexBlock>
							<TextControl
								label={ __( 'End Date', 'telex-hours-block' ) }
								type="date"
								value={ season.endDate }
								onChange={ ( val ) => updateField( 'endDate', val ) }
								__nextHasNoMarginBottom
							/>
						</FlexBlock>
					</Flex>
					<div className="telex-hours-block-inspector__day-grid">
						<Text
							variant="muted"
							size="11"
							upperCase
							weight={ 500 }
							className="telex-hours-block-inspector__day-grid-hint"
						>
							{ __( 'Leave blank for closed', 'telex-hours-block' ) }
						</Text>
						{ orderedDays.map( ( { key, label } ) => {
							const slots = normalizeSlots( season.hours?.[ key ] );
							return (
								<div key={ key } className="telex-hours-block-inspector__day-row">
									<div className="telex-hours-block-inspector__day-label">
										{ label }
									</div>
									<div>
										{ slots.map( ( slot, si ) => (
											<TimeSlotRow
												key={ si }
												slot={ slot }
												slotIndex={ si }
												onUpdate={ ( slotIdx, timeKey, val ) =>
													updateSlotTime( key, slotIdx, timeKey, val )
												}
												onRemove={ ( slotIdx ) =>
													removeSlot( key, slotIdx )
												}
												canRemove={ slots.length > 1 }
											/>
										) ) }
										<Button
											variant="link"
											size="small"
											onClick={ () => addSlot( key ) }
											className="telex-hours-block-inspector__add-slot"
										>
											{ __( '+ Add time slot', 'telex-hours-block' ) }
										</Button>
									</div>
								</div>
							);
						} ) }
					</div>
				</div>
			) }
		</div>
	);
}

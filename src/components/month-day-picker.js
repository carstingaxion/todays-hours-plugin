/**
 * MonthDayPicker component — Selects a month and day without year.
 *
 * @package
 */

import {
	Flex,
	FlexBlock,
	FlexItem,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalText as Text,
} from '@wordpress/components';
import { parseMonthDay, getMonthOptions, getDayOptions } from '../utils/dates';

/**
 * Renders a month/day picker for recurring (yearless) holiday dates.
 *
 * @param {Object}   props          Component props.
 * @param {string}   props.label    The label text.
 * @param {string}   props.value    Current value in MM-DD format.
 * @param {Function} props.onChange Callback with the new MM-DD string.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */
export default function MonthDayPicker( { label, value, onChange } ) {
	const { month, day } = parseMonthDay( value );
	const monthOptions = getMonthOptions();
	const dayOptions = getDayOptions( month );

	return (
		<div>
			<Text
				as="label"
				size="11"
				upperCase
				weight={ 500 }
				className="components-base-control__label"
			>
				{ label }
			</Text>
			<Flex gap={ 2 }>
				<FlexBlock>
					{ /* eslint-disable-next-line jsx-a11y/no-onchange */ }
					<select
						className="components-select-control__input"
						value={ month }
						onChange={ ( e ) => {
							const newMonth = e.target.value;
							const newDayOpts = getDayOptions( newMonth );
							const clampedDay =
								parseInt( day, 10 ) > newDayOpts.length
									? String( newDayOpts.length ).padStart(
											2,
											'0'
									  )
									: day;
							onChange( newMonth + '-' + clampedDay );
						} }
						style={ { width: '100%', minHeight: '36px' } }
					>
						{ monthOptions.map( ( opt ) => (
							<option key={ opt.value } value={ opt.value }>
								{ opt.label }
							</option>
						) ) }
					</select>
				</FlexBlock>
				<FlexItem>
					{ /* eslint-disable-next-line jsx-a11y/no-onchange */ }
					<select
						className="components-select-control__input"
						value={ day }
						onChange={ ( e ) => {
							onChange( month + '-' + e.target.value );
						} }
						style={ { minHeight: '36px' } }
					>
						{ dayOptions.map( ( opt ) => (
							<option key={ opt.value } value={ opt.value }>
								{ opt.label }
							</option>
						) ) }
					</select>
				</FlexItem>
			</Flex>
		</div>
	);
}

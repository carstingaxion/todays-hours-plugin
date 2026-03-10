/**
 * Tests for src/utils/slots.js
 *
 * @package TelexHoursBlock
 */

import {
	normalizeSlots,
	normalizeHolidaySlots,
	slotsHaveOpen,
} from '../../../src/utils/slots';

describe( 'normalizeSlots', () => {
	it( 'returns array-of-slots format unchanged', () => {
		const input = [ { open: '8:00 AM', close: '5:00 PM' } ];
		expect( normalizeSlots( input ) ).toEqual( input );
	} );

	it( 'converts legacy single object to array', () => {
		const input = { open: '9:00 AM', close: '5:00 PM' };
		expect( normalizeSlots( input ) ).toEqual( [
			{ open: '9:00 AM', close: '5:00 PM' },
		] );
	} );

	it( 'returns default empty slot for null', () => {
		expect( normalizeSlots( null ) ).toEqual( [
			{ open: '', close: '' },
		] );
	} );

	it( 'returns default empty slot for undefined', () => {
		expect( normalizeSlots( undefined ) ).toEqual( [
			{ open: '', close: '' },
		] );
	} );

	it( 'returns default empty slot for empty array', () => {
		expect( normalizeSlots( [] ) ).toEqual( [
			{ open: '', close: '' },
		] );
	} );

	it( 'handles multiple slots', () => {
		const input = [
			{ open: '8:00 AM', close: '11:00 AM' },
			{ open: '1:00 PM', close: '5:00 PM' },
		];
		expect( normalizeSlots( input ) ).toEqual( input );
	} );

	it( 'handles legacy object with only open key', () => {
		const input = { open: '9:00 AM' };
		const result = normalizeSlots( input );
		expect( result ).toHaveLength( 1 );
		expect( result[ 0 ].open ).toBe( '9:00 AM' );
		expect( result[ 0 ].close ).toBe( '' );
	} );
} );

describe( 'normalizeHolidaySlots', () => {
	it( 'returns slots from the slots property', () => {
		const holiday = {
			slots: [ { open: '10:00 AM', close: '2:00 PM' } ],
		};
		expect( normalizeHolidaySlots( holiday ) ).toEqual( [
			{ open: '10:00 AM', close: '2:00 PM' },
		] );
	} );

	it( 'converts legacy openTime/closeTime', () => {
		const holiday = {
			openTime: '9:00 AM',
			closeTime: '1:00 PM',
		};
		expect( normalizeHolidaySlots( holiday ) ).toEqual( [
			{ open: '9:00 AM', close: '1:00 PM' },
		] );
	} );

	it( 'returns empty array for empty slots', () => {
		const holiday = { slots: [] };
		expect( normalizeHolidaySlots( holiday ) ).toEqual( [] );
	} );

	it( 'returns empty array for no slots or openTime', () => {
		const holiday = { name: 'Closed Day' };
		expect( normalizeHolidaySlots( holiday ) ).toEqual( [] );
	} );

	it( 'handles multiple slots', () => {
		const holiday = {
			slots: [
				{ open: '8:00 AM', close: '11:00 AM' },
				{ open: '1:00 PM', close: '4:00 PM' },
			],
		};
		const result = normalizeHolidaySlots( holiday );
		expect( result ).toHaveLength( 2 );
	} );
} );

describe( 'slotsHaveOpen', () => {
	it( 'returns true when a slot has open time', () => {
		const slots = [ { open: '8:00 AM', close: '5:00 PM' } ];
		expect( slotsHaveOpen( slots ) ).toBe( true );
	} );

	it( 'returns false when all slots are empty', () => {
		const slots = [ { open: '', close: '' } ];
		expect( slotsHaveOpen( slots ) ).toBe( false );
	} );

	it( 'returns false for empty array', () => {
		expect( slotsHaveOpen( [] ) ).toBe( false );
	} );

	it( 'returns true when at least one slot has open time', () => {
		const slots = [
			{ open: '', close: '' },
			{ open: '1:00 PM', close: '5:00 PM' },
		];
		expect( slotsHaveOpen( slots ) ).toBe( true );
	} );

	it( 'returns false for whitespace-only open', () => {
		const slots = [ { open: '   ', close: '' } ];
		expect( slotsHaveOpen( slots ) ).toBe( false );
	} );
} );

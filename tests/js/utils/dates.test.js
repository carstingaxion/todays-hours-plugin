/**
 * Tests for src/utils/dates.js
 *
 * @package TelexHoursBlock
 */

import {
	parseDate,
	isDateInRange,
	dateHasYear,
	parseMonthDay,
	getMonthOptions,
	getDayOptions,
} from '../../../src/utils/dates';

describe( 'parseDate', () => {
	it( 'parses a valid YYYY-MM-DD string', () => {
		const d = parseDate( '2025-07-04' );
		expect( d ).toBeInstanceOf( Date );
		expect( d.getFullYear() ).toBe( 2025 );
		expect( d.getMonth() ).toBe( 6 ); // July is 0-indexed.
		expect( d.getDate() ).toBe( 4 );
	} );

	it( 'returns null for empty string', () => {
		expect( parseDate( '' ) ).toBeNull();
	} );

	it( 'returns null for null', () => {
		expect( parseDate( null ) ).toBeNull();
	} );

	it( 'returns null for MM-DD format (only 2 parts)', () => {
		expect( parseDate( '07-04' ) ).toBeNull();
	} );

	it( 'returns null for undefined', () => {
		expect( parseDate( undefined ) ).toBeNull();
	} );
} );

describe( 'isDateInRange', () => {
	it( 'returns true when date is within range', () => {
		const test = new Date( 2025, 5, 15 ); // June 15
		const begin = new Date( 2025, 0, 1 ); // Jan 1
		const end = new Date( 2025, 11, 31 ); // Dec 31
		expect( isDateInRange( test, begin, end ) ).toBe( true );
	} );

	it( 'returns true on begin date', () => {
		const d = new Date( 2025, 0, 1 );
		expect( isDateInRange( d, d, new Date( 2025, 11, 31 ) ) ).toBe( true );
	} );

	it( 'returns true on end date', () => {
		const d = new Date( 2025, 11, 31 );
		expect( isDateInRange( d, new Date( 2025, 0, 1 ), d ) ).toBe( true );
	} );

	it( 'returns false when date is before range', () => {
		const test = new Date( 2024, 11, 31 );
		const begin = new Date( 2025, 0, 1 );
		const end = new Date( 2025, 11, 31 );
		expect( isDateInRange( test, begin, end ) ).toBe( false );
	} );

	it( 'returns false when date is after range', () => {
		const test = new Date( 2026, 0, 1 );
		const begin = new Date( 2025, 0, 1 );
		const end = new Date( 2025, 11, 31 );
		expect( isDateInRange( test, begin, end ) ).toBe( false );
	} );

	it( 'works for single-day range', () => {
		const d = new Date( 2025, 6, 4 );
		expect( isDateInRange( d, d, d ) ).toBe( true );
	} );
} );

describe( 'dateHasYear', () => {
	it( 'returns true for YYYY-MM-DD', () => {
		expect( dateHasYear( '2025-07-04' ) ).toBe( true );
	} );

	it( 'returns false for MM-DD', () => {
		expect( dateHasYear( '07-04' ) ).toBe( false );
	} );

	it( 'returns false for empty string', () => {
		expect( dateHasYear( '' ) ).toBe( false );
	} );

	it( 'returns false for null', () => {
		expect( dateHasYear( null ) ).toBe( false );
	} );
} );

describe( 'parseMonthDay', () => {
	it( 'parses YYYY-MM-DD format', () => {
		expect( parseMonthDay( '2025-07-04' ) ).toEqual( {
			month: '07',
			day: '04',
		} );
	} );

	it( 'parses MM-DD format', () => {
		expect( parseMonthDay( '12-25' ) ).toEqual( {
			month: '12',
			day: '25',
		} );
	} );

	it( 'returns defaults for empty string', () => {
		expect( parseMonthDay( '' ) ).toEqual( {
			month: '01',
			day: '01',
		} );
	} );

	it( 'returns defaults for null', () => {
		expect( parseMonthDay( null ) ).toEqual( {
			month: '01',
			day: '01',
		} );
	} );
} );

describe( 'getMonthOptions', () => {
	it( 'returns 12 month options', () => {
		const options = getMonthOptions();
		expect( options ).toHaveLength( 12 );
		expect( options[ 0 ].value ).toBe( '01' );
		expect( options[ 0 ].label ).toBe( 'January' );
		expect( options[ 11 ].value ).toBe( '12' );
		expect( options[ 11 ].label ).toBe( 'December' );
	} );
} );

describe( 'getDayOptions', () => {
	it( 'returns 31 days for January', () => {
		expect( getDayOptions( '01' ) ).toHaveLength( 31 );
	} );

	it( 'returns 29 days for February', () => {
		expect( getDayOptions( '02' ) ).toHaveLength( 29 );
	} );

	it( 'returns 30 days for April', () => {
		expect( getDayOptions( '04' ) ).toHaveLength( 30 );
	} );

	it( 'day values are zero-padded', () => {
		const days = getDayOptions( '01' );
		expect( days[ 0 ].value ).toBe( '01' );
		expect( days[ 0 ].label ).toBe( '1' );
		expect( days[ 8 ].value ).toBe( '09' );
	} );
} );

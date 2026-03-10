/**
 * Tests for src/utils/days.js
 *
 * @package
 */

import {
	ALL_DAY_KEYS,
	WEEKEND_KEYS,
	getOrderedDays,
	getDefaultHours,
} from '../../../src/utils/days';

describe( 'ALL_DAY_KEYS', () => {
	it( 'contains 7 day keys starting with sun', () => {
		expect( ALL_DAY_KEYS ).toHaveLength( 7 );
		expect( ALL_DAY_KEYS[ 0 ] ).toBe( 'sun' );
		expect( ALL_DAY_KEYS[ 6 ] ).toBe( 'sat' );
	} );
} );

describe( 'WEEKEND_KEYS', () => {
	it( 'contains sun and sat', () => {
		expect( WEEKEND_KEYS ).toContain( 'sun' );
		expect( WEEKEND_KEYS ).toContain( 'sat' );
		expect( WEEKEND_KEYS ).toHaveLength( 2 );
	} );
} );

describe( 'getOrderedDays', () => {
	it( 'starts with Sunday when startOfWeek is 0', () => {
		const days = getOrderedDays( 0 );
		expect( days ).toHaveLength( 7 );
		expect( days[ 0 ].key ).toBe( 'sun' );
		expect( days[ 0 ].label ).toBe( 'Sunday' );
		expect( days[ 6 ].key ).toBe( 'sat' );
	} );

	it( 'starts with Monday when startOfWeek is 1', () => {
		const days = getOrderedDays( 1 );
		expect( days[ 0 ].key ).toBe( 'mon' );
		expect( days[ 0 ].label ).toBe( 'Monday' );
		expect( days[ 6 ].key ).toBe( 'sun' );
		expect( days[ 6 ].label ).toBe( 'Sunday' );
	} );

	it( 'starts with Wednesday when startOfWeek is 3', () => {
		const days = getOrderedDays( 3 );
		expect( days[ 0 ].key ).toBe( 'wed' );
		expect( days[ 6 ].key ).toBe( 'tue' );
	} );

	it( 'returns all 7 days with labels', () => {
		const days = getOrderedDays( 0 );
		days.forEach( ( day ) => {
			expect( day ).toHaveProperty( 'key' );
			expect( day ).toHaveProperty( 'label' );
			expect( typeof day.key ).toBe( 'string' );
			expect( typeof day.label ).toBe( 'string' );
			expect( day.label.length ).toBeGreaterThan( 0 );
		} );
	} );

	it( 'contains all day keys regardless of start', () => {
		for ( let s = 0; s < 7; s++ ) {
			const days = getOrderedDays( s );
			const keys = days.map( ( d ) => d.key ).sort();
			expect( keys ).toEqual( [ ...ALL_DAY_KEYS ].sort() );
		}
	} );
} );

describe( 'getDefaultHours', () => {
	it( 'returns an object with all 7 day keys', () => {
		const hours = getDefaultHours();
		ALL_DAY_KEYS.forEach( ( key ) => {
			expect( hours ).toHaveProperty( key );
		} );
	} );

	it( 'each day has one empty slot', () => {
		const hours = getDefaultHours();
		ALL_DAY_KEYS.forEach( ( key ) => {
			expect( hours[ key ] ).toHaveLength( 1 );
			expect( hours[ key ][ 0 ] ).toEqual( { open: '', close: '' } );
		} );
	} );
} );

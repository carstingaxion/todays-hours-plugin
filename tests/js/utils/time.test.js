/**
 * Tests for src/utils/time.js
 *
 * @package
 */

import {
	parseTime,
	applyFriendlyTwelves,
	formatTimeWithSiteFormat,
	to24h,
} from '../../../src/utils/time';

describe( 'parseTime', () => {
	it( 'parses 24-hour HH:MM format', () => {
		expect( parseTime( '08:00' ) ).toEqual( { hours: 8, minutes: 0 } );
		expect( parseTime( '17:30' ) ).toEqual( { hours: 17, minutes: 30 } );
		expect( parseTime( '00:00' ) ).toEqual( { hours: 0, minutes: 0 } );
		expect( parseTime( '23:59' ) ).toEqual( { hours: 23, minutes: 59 } );
	} );

	it( 'parses single-digit hours in 24h format', () => {
		expect( parseTime( '8:00' ) ).toEqual( { hours: 8, minutes: 0 } );
		expect( parseTime( '9:30' ) ).toEqual( { hours: 9, minutes: 30 } );
	} );

	it( 'parses 12-hour format with AM/PM', () => {
		expect( parseTime( '8:00 AM' ) ).toEqual( { hours: 8, minutes: 0 } );
		expect( parseTime( '5:00 PM' ) ).toEqual( { hours: 17, minutes: 0 } );
		expect( parseTime( '12:00 PM' ) ).toEqual( { hours: 12, minutes: 0 } );
		expect( parseTime( '12:00 AM' ) ).toEqual( { hours: 0, minutes: 0 } );
	} );

	it( 'handles case-insensitive AM/PM', () => {
		expect( parseTime( '8:00 am' ) ).toEqual( { hours: 8, minutes: 0 } );
		expect( parseTime( '5:00 pm' ) ).toEqual( { hours: 17, minutes: 0 } );
		expect( parseTime( '8:00AM' ) ).toEqual( { hours: 8, minutes: 0 } );
	} );

	it( 'returns null for empty or invalid input', () => {
		expect( parseTime( '' ) ).toBeNull();
		expect( parseTime( null ) ).toBeNull();
		expect( parseTime( undefined ) ).toBeNull();
		expect( parseTime( 'not-a-time' ) ).toBeNull();
	} );

	it( 'returns null for out-of-range values', () => {
		expect( parseTime( '25:00' ) ).toBeNull();
		expect( parseTime( '08:60' ) ).toBeNull();
	} );
} );

describe( 'applyFriendlyTwelves', () => {
	it( 'replaces midnight in 24h format', () => {
		expect( applyFriendlyTwelves( '00:00', true ) ).toBe( 'Midnight' );
	} );

	it( 'replaces noon in 24h format', () => {
		expect( applyFriendlyTwelves( '12:00', true ) ).toBe( 'Noon' );
	} );

	it( 'replaces 12:00 AM with Midnight', () => {
		expect( applyFriendlyTwelves( '12:00 AM', true ) ).toBe( 'Midnight' );
	} );

	it( 'replaces 12:00 PM with Noon', () => {
		expect( applyFriendlyTwelves( '12:00 PM', true ) ).toBe( 'Noon' );
	} );

	it( 'handles case-insensitive input', () => {
		expect( applyFriendlyTwelves( '12:00 am', true ) ).toBe( 'Midnight' );
		expect( applyFriendlyTwelves( '12:00 pm', true ) ).toBe( 'Noon' );
		expect( applyFriendlyTwelves( '12:00AM', true ) ).toBe( 'Midnight' );
	} );

	it( 'does not replace other times', () => {
		expect( applyFriendlyTwelves( '15:00', true ) ).toBe( '15:00' );
		expect( applyFriendlyTwelves( '08:00', true ) ).toBe( '08:00' );
		expect( applyFriendlyTwelves( '3:00 PM', true ) ).toBe( '3:00 PM' );
		expect( applyFriendlyTwelves( '12:30', true ) ).toBe( '12:30' );
	} );

	it( 'returns original when disabled', () => {
		expect( applyFriendlyTwelves( '00:00', false ) ).toBe( '00:00' );
		expect( applyFriendlyTwelves( '12:00', false ) ).toBe( '12:00' );
		expect( applyFriendlyTwelves( '12:00 AM', false ) ).toBe( '12:00 AM' );
	} );

	it( 'returns empty string for empty input', () => {
		expect( applyFriendlyTwelves( '', true ) ).toBe( '' );
	} );

	it( 'returns falsy input as-is', () => {
		expect( applyFriendlyTwelves( null, true ) ).toBeNull();
		expect( applyFriendlyTwelves( undefined, true ) ).toBeUndefined();
	} );
} );

describe( 'formatTimeWithSiteFormat', () => {
	it( 'formats 24h input with g:i a (12-hour lowercase)', () => {
		expect( formatTimeWithSiteFormat( '08:00', 'g:i a' ) ).toBe(
			'8:00 am'
		);
		expect( formatTimeWithSiteFormat( '17:00', 'g:i a' ) ).toBe(
			'5:00 pm'
		);
	} );

	it( 'formats 24h input with g:i A (12-hour uppercase)', () => {
		expect( formatTimeWithSiteFormat( '08:00', 'g:i A' ) ).toBe(
			'8:00 AM'
		);
		expect( formatTimeWithSiteFormat( '17:00', 'g:i A' ) ).toBe(
			'5:00 PM'
		);
	} );

	it( 'formats 24h input with H:i (24-hour)', () => {
		expect( formatTimeWithSiteFormat( '08:00', 'H:i' ) ).toBe( '08:00' );
		expect( formatTimeWithSiteFormat( '17:00', 'H:i' ) ).toBe( '17:00' );
		expect( formatTimeWithSiteFormat( '12:00', 'H:i' ) ).toBe( '12:00' );
		expect( formatTimeWithSiteFormat( '00:00', 'H:i' ) ).toBe( '00:00' );
	} );

	it( 'formats 24h input with G:i (24-hour no leading zero)', () => {
		expect( formatTimeWithSiteFormat( '08:00', 'G:i' ) ).toBe( '8:00' );
		expect( formatTimeWithSiteFormat( '17:00', 'G:i' ) ).toBe( '17:00' );
	} );

	it( 'formats 24h input with h:i A (12-hour with leading zero)', () => {
		expect( formatTimeWithSiteFormat( '08:00', 'h:i A' ) ).toBe(
			'08:00 AM'
		);
		expect( formatTimeWithSiteFormat( '12:00', 'h:i A' ) ).toBe(
			'12:00 PM'
		);
	} );

	it( 'still handles legacy 12h input', () => {
		expect( formatTimeWithSiteFormat( '8:00 AM', 'g:i a' ) ).toBe(
			'8:00 am'
		);
		expect( formatTimeWithSiteFormat( '5:00 PM', 'g:i a' ) ).toBe(
			'5:00 pm'
		);
		expect( formatTimeWithSiteFormat( '8:00 AM', 'H:i' ) ).toBe( '08:00' );
		expect( formatTimeWithSiteFormat( '5:00 PM', 'H:i' ) ).toBe( '17:00' );
	} );

	it( 'handles escaped characters', () => {
		expect( formatTimeWithSiteFormat( '08:00', 'g:i \\U\\h\\r' ) ).toBe(
			'8:00 Uhr'
		);
	} );

	it( 'returns original for empty input', () => {
		expect( formatTimeWithSiteFormat( '', 'g:i a' ) ).toBe( '' );
	} );

	it( 'returns original when format is empty', () => {
		expect( formatTimeWithSiteFormat( '08:00', '' ) ).toBe( '08:00' );
	} );

	it( 'returns original for unparseable time', () => {
		expect( formatTimeWithSiteFormat( 'not-a-time', 'g:i a' ) ).toBe(
			'not-a-time'
		);
	} );
} );

describe( 'to24h', () => {
	it( 'returns already-valid 24h format normalized', () => {
		expect( to24h( '08:00' ) ).toBe( '08:00' );
		expect( to24h( '17:30' ) ).toBe( '17:30' );
		expect( to24h( '00:00' ) ).toBe( '00:00' );
	} );

	it( 'normalizes single-digit hours', () => {
		expect( to24h( '8:00' ) ).toBe( '08:00' );
		expect( to24h( '9:30' ) ).toBe( '09:30' );
	} );

	it( 'converts 12-hour AM times', () => {
		expect( to24h( '8:00 AM' ) ).toBe( '08:00' );
		expect( to24h( '9:30 AM' ) ).toBe( '09:30' );
	} );

	it( 'converts 12-hour PM times', () => {
		expect( to24h( '5:00 PM' ) ).toBe( '17:00' );
		expect( to24h( '1:00 PM' ) ).toBe( '13:00' );
		expect( to24h( '11:00 PM' ) ).toBe( '23:00' );
	} );

	it( 'handles noon and midnight in 12h format', () => {
		expect( to24h( '12:00 PM' ) ).toBe( '12:00' );
		expect( to24h( '12:00 AM' ) ).toBe( '00:00' );
	} );

	it( 'returns empty for empty input', () => {
		expect( to24h( '' ) ).toBe( '' );
	} );

	it( 'returns empty for invalid input', () => {
		expect( to24h( 'not-a-time' ) ).toBe( '' );
	} );
} );

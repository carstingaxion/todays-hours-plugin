/**
 * Tests for src/utils/time.js
 *
 * @package TelexHoursBlock
 */

import { applyFriendlyTwelves, formatTimeWithSiteFormat } from '../../../src/utils/time';

describe( 'applyFriendlyTwelves', () => {
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
		expect( applyFriendlyTwelves( '12:00PM', true ) ).toBe( 'Noon' );
	} );

	it( 'does not replace other times', () => {
		expect( applyFriendlyTwelves( '3:00 PM', true ) ).toBe( '3:00 PM' );
		expect( applyFriendlyTwelves( '8:00 AM', true ) ).toBe( '8:00 AM' );
		expect( applyFriendlyTwelves( '12:30 PM', true ) ).toBe( '12:30 PM' );
	} );

	it( 'returns original when disabled', () => {
		expect( applyFriendlyTwelves( '12:00 AM', false ) ).toBe( '12:00 AM' );
		expect( applyFriendlyTwelves( '12:00 PM', false ) ).toBe( '12:00 PM' );
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
	it( 'formats with g:i a (12-hour lowercase)', () => {
		expect( formatTimeWithSiteFormat( '8:00 AM', 'g:i a' ) ).toBe(
			'8:00 am'
		);
		expect( formatTimeWithSiteFormat( '5:00 PM', 'g:i a' ) ).toBe(
			'5:00 pm'
		);
	} );

	it( 'formats with g:i A (12-hour uppercase)', () => {
		expect( formatTimeWithSiteFormat( '8:00 AM', 'g:i A' ) ).toBe(
			'8:00 AM'
		);
		expect( formatTimeWithSiteFormat( '5:00 PM', 'g:i A' ) ).toBe(
			'5:00 PM'
		);
	} );

	it( 'formats with H:i (24-hour)', () => {
		expect( formatTimeWithSiteFormat( '8:00 AM', 'H:i' ) ).toBe( '08:00' );
		expect( formatTimeWithSiteFormat( '5:00 PM', 'H:i' ) ).toBe( '17:00' );
		expect( formatTimeWithSiteFormat( '12:00 PM', 'H:i' ) ).toBe(
			'12:00'
		);
		expect( formatTimeWithSiteFormat( '12:00 AM', 'H:i' ) ).toBe(
			'00:00'
		);
	} );

	it( 'formats with G:i (24-hour no leading zero)', () => {
		expect( formatTimeWithSiteFormat( '8:00 AM', 'G:i' ) ).toBe( '8:00' );
		expect( formatTimeWithSiteFormat( '5:00 PM', 'G:i' ) ).toBe( '17:00' );
	} );

	it( 'formats with h:i A (12-hour with leading zero)', () => {
		expect( formatTimeWithSiteFormat( '8:00 AM', 'h:i A' ) ).toBe(
			'08:00 AM'
		);
		expect( formatTimeWithSiteFormat( '12:00 PM', 'h:i A' ) ).toBe(
			'12:00 PM'
		);
	} );

	it( 'handles escaped characters', () => {
		expect( formatTimeWithSiteFormat( '8:00 AM', 'g:i \\U\\h\\r' ) ).toBe(
			'8:00 Uhr'
		);
	} );

	it( 'returns original for empty input', () => {
		expect( formatTimeWithSiteFormat( '', 'g:i a' ) ).toBe( '' );
	} );

	it( 'returns original when format is empty', () => {
		expect( formatTimeWithSiteFormat( '8:00 AM', '' ) ).toBe( '8:00 AM' );
	} );

	it( 'returns original for unparseable time', () => {
		expect( formatTimeWithSiteFormat( 'not-a-time', 'g:i a' ) ).toBe(
			'not-a-time'
		);
	} );
} );

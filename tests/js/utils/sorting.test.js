/**
 * Tests for src/utils/sorting.js
 *
 * @package
 */

import { getSortableBeginDate } from '../../../src/utils/sorting';

describe( 'getSortableBeginDate', () => {
	it( 'returns YYYY-MM-DD as-is', () => {
		expect( getSortableBeginDate( '2025-07-04' ) ).toBe( '2025-07-04' );
	} );

	it( 'prefixes MM-DD with 9999-', () => {
		expect( getSortableBeginDate( '07-04' ) ).toBe( '9999-07-04' );
	} );

	it( 'returns zzzz for empty string', () => {
		expect( getSortableBeginDate( '' ) ).toBe( 'zzzz' );
	} );

	it( 'returns zzzz for null', () => {
		expect( getSortableBeginDate( null ) ).toBe( 'zzzz' );
	} );

	it( 'returns zzzz for undefined', () => {
		expect( getSortableBeginDate( undefined ) ).toBe( 'zzzz' );
	} );

	it( 'sorts year-specific before recurring', () => {
		const a = getSortableBeginDate( '2025-01-01' );
		const b = getSortableBeginDate( '01-01' );
		expect( a.localeCompare( b ) ).toBeLessThan( 0 );
	} );

	it( 'sorts empty dates last', () => {
		const a = getSortableBeginDate( '9999-12-31' );
		const b = getSortableBeginDate( '' );
		expect( a.localeCompare( b ) ).toBeLessThan( 0 );
	} );

	it( 'sorts year-specific dates chronologically', () => {
		const dates = [ '2025-12-01', '2025-01-15', '2025-06-30' ].map(
			getSortableBeginDate
		);

		const sorted = [ ...dates ].sort();
		expect( sorted ).toEqual( [
			'2025-01-15',
			'2025-06-30',
			'2025-12-01',
		] );
	} );
} );

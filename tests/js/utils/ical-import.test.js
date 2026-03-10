/**
 * Tests for src/utils/ical-import.js
 *
 * @package TelexHoursBlock
 */

import apiFetch from '@wordpress/api-fetch';
import { importIcalFile } from '../../../src/utils/ical-import';

// apiFetch is auto-mocked via the mock file.

describe( 'importIcalFile', () => {
	beforeEach( () => {
		apiFetch.mockClear();
	} );

	it( 'reads file and calls apiFetch with the content', async () => {
		const mockHolidays = [
			{
				name: 'Test Holiday',
				beginDate: '2025-07-04',
				endDate: '2025-07-04',
				slots: [],
			},
		];

		apiFetch.mockResolvedValueOnce( { holidays: mockHolidays } );

		// Create a mock File.
		const fileContent = 'BEGIN:VCALENDAR\nEND:VCALENDAR';
		const file = new File( [ fileContent ], 'test.ics', {
			type: 'text/calendar',
		} );

		const result = await importIcalFile( file );

		expect( apiFetch ).toHaveBeenCalledTimes( 1 );
		expect( apiFetch ).toHaveBeenCalledWith( {
			path: '/telex-hours-block/v1/import-ical',
			method: 'POST',
			data: { ical_text: fileContent },
		} );
		expect( result ).toEqual( mockHolidays );
	} );

	it( 'returns empty array when response has no holidays', async () => {
		apiFetch.mockResolvedValueOnce( { holidays: [] } );

		const file = new File( [ 'BEGIN:VCALENDAR\nEND:VCALENDAR' ], 'empty.ics', {
			type: 'text/calendar',
		} );

		const result = await importIcalFile( file );
		expect( result ).toEqual( [] );
	} );

	it( 'propagates errors from apiFetch', async () => {
		apiFetch.mockRejectedValueOnce( new Error( 'Network error' ) );

		const file = new File( [ 'invalid' ], 'bad.ics', {
			type: 'text/calendar',
		} );

		await expect( importIcalFile( file ) ).rejects.toThrow(
			'Network error'
		);
	} );
} );

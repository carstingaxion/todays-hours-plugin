/**
 * Tests for the schedule preview logic (findHolidayForDate).
 *
 * The useSchedulePreview hook itself uses useMemo and depends on the current date,
 * so we test the exported findHolidayForDate function directly.
 *
 * @package
 */

import { findHolidayForDate } from '../../../src/hooks/use-schedule-preview';

describe( 'findHolidayForDate', () => {
	it( 'matches a year-specific single-day holiday', () => {
		const holidays = [
			{
				name: 'Christmas',
				beginDate: '2025-12-25',
				endDate: '2025-12-25',
				slots: [],
			},
		];

		const result = findHolidayForDate( holidays, new Date( 2025, 11, 25 ) );
		expect( result ).not.toBeNull();
		expect( result.name ).toBe( 'Christmas' );
	} );

	it( 'matches a year-specific multi-day holiday', () => {
		const holidays = [
			{
				name: 'Winter Break',
				beginDate: '2025-12-20',
				endDate: '2026-01-05',
				slots: [],
			},
		];

		expect(
			findHolidayForDate( holidays, new Date( 2025, 11, 28 ) )
		).not.toBeNull();
		expect(
			findHolidayForDate( holidays, new Date( 2026, 0, 3 ) )
		).not.toBeNull();
		expect(
			findHolidayForDate( holidays, new Date( 2025, 11, 19 ) )
		).toBeNull();
	} );

	it( 'matches a recurring (yearless) holiday', () => {
		const holidays = [
			{
				name: 'New Year',
				beginDate: '01-01',
				endDate: '01-01',
				slots: [],
			},
		];

		expect(
			findHolidayForDate( holidays, new Date( 2025, 0, 1 ) )
		).not.toBeNull();
		expect(
			findHolidayForDate( holidays, new Date( 2030, 0, 1 ) )
		).not.toBeNull();
		expect(
			findHolidayForDate( holidays, new Date( 2025, 0, 2 ) )
		).toBeNull();
	} );

	it( 'matches a recurring holiday with date range', () => {
		const holidays = [
			{
				name: 'Thanksgiving',
				beginDate: '11-27',
				endDate: '11-28',
				slots: [],
			},
		];

		expect(
			findHolidayForDate( holidays, new Date( 2025, 10, 27 ) )
		).not.toBeNull();
		expect(
			findHolidayForDate( holidays, new Date( 2025, 10, 28 ) )
		).not.toBeNull();
		expect(
			findHolidayForDate( holidays, new Date( 2025, 10, 26 ) )
		).toBeNull();
	} );

	it( 'handles recurring wrap-around (e.g. 12-20 to 01-05)', () => {
		const holidays = [
			{
				name: 'Holiday Break',
				beginDate: '12-20',
				endDate: '01-05',
				slots: [],
			},
		];

		expect(
			findHolidayForDate( holidays, new Date( 2025, 11, 25 ) )
		).not.toBeNull();
		expect(
			findHolidayForDate( holidays, new Date( 2026, 0, 3 ) )
		).not.toBeNull();
		expect(
			findHolidayForDate( holidays, new Date( 2025, 5, 15 ) )
		).toBeNull();
	} );

	it( 'returns null for empty holidays array', () => {
		expect( findHolidayForDate( [], new Date( 2025, 6, 4 ) ) ).toBeNull();
	} );

	it( 'skips holidays with empty dates', () => {
		const holidays = [
			{
				name: 'Incomplete',
				beginDate: '',
				endDate: '',
				slots: [],
			},
		];

		expect(
			findHolidayForDate( holidays, new Date( 2025, 0, 1 ) )
		).toBeNull();
	} );

	it( 'returns the first matching holiday', () => {
		const holidays = [
			{
				name: 'First',
				beginDate: '07-04',
				endDate: '07-04',
				slots: [],
			},
			{
				name: 'Second',
				beginDate: '07-04',
				endDate: '07-04',
				slots: [],
			},
		];

		const result = findHolidayForDate( holidays, new Date( 2025, 6, 4 ) );
		expect( result.name ).toBe( 'First' );
	} );

	it( 'does not match when year-specific holiday is in wrong year', () => {
		const holidays = [
			{
				name: 'One-Time Event',
				beginDate: '2025-07-04',
				endDate: '2025-07-04',
				slots: [],
			},
		];

		expect(
			findHolidayForDate( holidays, new Date( 2026, 6, 4 ) )
		).toBeNull();
	} );
} );

<?php
/**
 * Tests the logic for matching a date against seasons and holidays.
 *
 * @package TelexHoursBlock\Tests
 */

/**
 * Tests for Telex_Hours_Season_Finder.
 */
class SeasonFinderTest extends PHPUnit\Framework\TestCase {

	/**
	 * Season finder instance.
	 *
	 * @var Telex_Hours_Season_Finder
	 */
	private Telex_Hours_Season_Finder $finder;

	/**
	 * Set up the test fixture.
	 */
	protected function setUp(): void {
		$this->finder = Telex_Hours_Season_Finder::get_instance();
	}

	/**
	 * Test finding a season that matches today.
	 */
	public function test_find_season_match(): void {
		$seasons = array(
			array(
				'name'      => 'Fall 2025',
				'beginDate' => '2025-09-01',
				'endDate'   => '2025-12-31',
				'hours'     => array(),
			),
		);

		$today  = new DateTime( '2025-10-15' );
		$result = $this->finder->find_season( $seasons, $today );

		$this->assertNotNull( $result );
		$this->assertSame( 'Fall 2025', $result['name'] );
	}

	/**
	 * Test finding a season on the exact begin date.
	 */
	public function test_find_season_on_begin_date(): void {
		$seasons = array(
			array(
				'name'      => 'Spring',
				'beginDate' => '2025-03-01',
				'endDate'   => '2025-05-31',
				'hours'     => array(),
			),
		);

		$today  = new DateTime( '2025-03-01' );
		$result = $this->finder->find_season( $seasons, $today );

		$this->assertNotNull( $result );
		$this->assertSame( 'Spring', $result['name'] );
	}

	/**
	 * Test finding a season on the exact end date.
	 */
	public function test_find_season_on_end_date(): void {
		$seasons = array(
			array(
				'name'      => 'Spring',
				'beginDate' => '2025-03-01',
				'endDate'   => '2025-05-31',
				'hours'     => array(),
			),
		);

		$today  = new DateTime( '2025-05-31' );
		$result = $this->finder->find_season( $seasons, $today );

		$this->assertNotNull( $result );
	}

	/**
	 * Test no season matches when date is outside all ranges.
	 */
	public function test_find_season_no_match(): void {
		$seasons = array(
			array(
				'name'      => 'Fall',
				'beginDate' => '2025-09-01',
				'endDate'   => '2025-12-31',
				'hours'     => array(),
			),
		);

		$today  = new DateTime( '2025-06-15' );
		$result = $this->finder->find_season( $seasons, $today );

		$this->assertNull( $result );
	}

	/**
	 * Test that the first matching season is returned.
	 */
	public function test_find_season_returns_first_match(): void {
		$seasons = array(
			array(
				'name'      => 'First',
				'beginDate' => '2025-01-01',
				'endDate'   => '2025-12-31',
				'hours'     => array(),
			),
			array(
				'name'      => 'Second',
				'beginDate' => '2025-06-01',
				'endDate'   => '2025-08-31',
				'hours'     => array(),
			),
		);

		$today  = new DateTime( '2025-07-15' );
		$result = $this->finder->find_season( $seasons, $today );

		$this->assertSame( 'First', $result['name'] );
	}

	/**
	 * Test seasons with empty dates are skipped.
	 */
	public function test_find_season_skips_empty_dates(): void {
		$seasons = array(
			array(
				'name'      => 'No Dates',
				'beginDate' => '',
				'endDate'   => '',
				'hours'     => array(),
			),
		);

		$today  = new DateTime( '2025-06-15' );
		$result = $this->finder->find_season( $seasons, $today );

		$this->assertNull( $result );
	}

	/**
	 * Test finding a year-specific holiday.
	 */
	public function test_find_holiday_year_specific(): void {
		$holidays = array(
			array(
				'name'      => 'Christmas',
				'beginDate' => '2025-12-25',
				'endDate'   => '2025-12-25',
				'slots'     => array(),
			),
		);

		$today  = new DateTime( '2025-12-25' );
		$result = $this->finder->find_holiday( $holidays, $today );

		$this->assertNotNull( $result );
		$this->assertSame( 'Christmas', $result['name'] );
	}

	/**
	 * Test finding a multi-day year-specific holiday.
	 */
	public function test_find_holiday_multi_day(): void {
		$holidays = array(
			array(
				'name'      => 'Winter Break',
				'beginDate' => '2025-12-20',
				'endDate'   => '2026-01-05',
				'slots'     => array(),
			),
		);

		$today  = new DateTime( '2025-12-28' );
		$result = $this->finder->find_holiday( $holidays, $today );

		$this->assertNotNull( $result );
		$this->assertSame( 'Winter Break', $result['name'] );
	}

	/**
	 * Test finding a recurring (yearless) holiday.
	 */
	public function test_find_holiday_recurring(): void {
		$holidays = array(
			array(
				'name'      => 'New Year',
				'beginDate' => '01-01',
				'endDate'   => '01-01',
				'slots'     => array(),
			),
		);

		$today  = new DateTime( '2025-01-01' );
		$result = $this->finder->find_holiday( $holidays, $today );

		$this->assertNotNull( $result );
		$this->assertSame( 'New Year', $result['name'] );
	}

	/**
	 * Test recurring holiday matching across different years.
	 */
	public function test_find_holiday_recurring_different_year(): void {
		$holidays = array(
			array(
				'name'      => 'Independence Day',
				'beginDate' => '07-04',
				'endDate'   => '07-04',
				'slots'     => array(),
			),
		);

		$today_2025 = new DateTime( '2025-07-04' );
		$today_2030 = new DateTime( '2030-07-04' );

		$this->assertNotNull( $this->finder->find_holiday( $holidays, $today_2025 ) );
		$this->assertNotNull( $this->finder->find_holiday( $holidays, $today_2030 ) );
	}

	/**
	 * Test recurring holiday with year-boundary wrap-around (e.g. 12-20 to 01-05).
	 */
	public function test_find_holiday_recurring_wrap_around(): void {
		$holidays = array(
			array(
				'name'      => 'Holiday Break',
				'beginDate' => '12-20',
				'endDate'   => '01-05',
				'slots'     => array(),
			),
		);

		$dec_25 = new DateTime( '2025-12-25' );
		$jan_03 = new DateTime( '2026-01-03' );
		$jun_15 = new DateTime( '2025-06-15' );

		$this->assertNotNull( $this->finder->find_holiday( $holidays, $dec_25 ) );
		$this->assertNotNull( $this->finder->find_holiday( $holidays, $jan_03 ) );
		$this->assertNull( $this->finder->find_holiday( $holidays, $jun_15 ) );
	}

	/**
	 * Test no holiday match when date is outside range.
	 */
	public function test_find_holiday_no_match(): void {
		$holidays = array(
			array(
				'name'      => 'Thanksgiving',
				'beginDate' => '2025-11-27',
				'endDate'   => '2025-11-27',
				'slots'     => array(),
			),
		);

		$today  = new DateTime( '2025-11-28' );
		$result = $this->finder->find_holiday( $holidays, $today );

		$this->assertNull( $result );
	}

	/**
	 * Test holidays with empty dates are skipped.
	 */
	public function test_find_holiday_skips_empty_dates(): void {
		$holidays = array(
			array(
				'name'      => 'Incomplete',
				'beginDate' => '',
				'endDate'   => '',
				'slots'     => array(),
			),
		);

		$today  = new DateTime( '2025-06-15' );
		$result = $this->finder->find_holiday( $holidays, $today );

		$this->assertNull( $result );
	}

	/**
	 * Test empty seasons and holidays arrays.
	 */
	public function test_empty_arrays(): void {
		$today = new DateTime( '2025-06-15' );

		$this->assertNull( $this->finder->find_season( array(), $today ) );
		$this->assertNull( $this->finder->find_holiday( array(), $today ) );
	}
}

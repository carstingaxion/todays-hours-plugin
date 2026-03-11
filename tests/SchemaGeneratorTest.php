<?php
/**
 * Tests for Telex_Hours_Schema_Generator.
 *
 * @package TelexHoursBlock\Tests
 */

class SchemaGeneratorTest extends WP_UnitTestCase {

	/**
	 * Schema generator instance.
	 *
	 * @var Telex_Hours_Schema_Generator
	 */
	private Telex_Hours_Schema_Generator $generator;

	/**
	 * Day helpers instance.
	 *
	 * @var Telex_Hours_Day_Helpers
	 */
	private Telex_Hours_Day_Helpers $day_helpers;

	/**
	 * Time formatter instance.
	 *
	 * @var Telex_Hours_Time_Formatter
	 */
	private Telex_Hours_Time_Formatter $time_fmt;

	/**
	 * Set up the test fixture.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->generator   = Telex_Hours_Schema_Generator::get_instance();
		$this->day_helpers = Telex_Hours_Day_Helpers::get_instance();
		$this->time_fmt    = Telex_Hours_Time_Formatter::get_instance();
	}

	/**
	 * Test building specs for a season with open days.
	 */
	public function test_build_specs_season_hours(): void {
		$season = array(
			'name'      => 'Normal',
			'beginDate' => '2025-01-01',
			'endDate'   => '2025-12-31',
			'hours'     => array(
				'sun' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'mon' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '5:00 PM',
					),
				),
				'tue' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '5:00 PM',
					),
				),
				'wed' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'thu' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '5:00 PM',
					),
				),
				'fri' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '5:00 PM',
					),
				),
				'sat' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
			),
		);

		$today = new DateTime( '2025-06-15' );
		$specs = $this->generator->build_opening_hours_specs(
			$season,
			array(),
			$today,
			false,
			$this->day_helpers,
			$this->time_fmt
		);

		// 4 open days: mon, tue, thu, fri.
		$this->assertCount( 4, $specs );

		// Check first spec structure.
		$this->assertSame( 'OpeningHoursSpecification', $specs[0]['@type'] );
		$this->assertArrayHasKey( 'dayOfWeek', $specs[0] );
		$this->assertArrayHasKey( 'opens', $specs[0] );
		$this->assertArrayHasKey( 'closes', $specs[0] );
		$this->assertSame( '2025-01-01', $specs[0]['validFrom'] );
		$this->assertSame( '2025-12-31', $specs[0]['validThrough'] );
	}

	/**
	 * Test building specs hides weekend days when requested.
	 */
	public function test_build_specs_hide_weekends(): void {
		$season = array(
			'name'      => 'All Open',
			'beginDate' => '2025-01-01',
			'endDate'   => '2025-12-31',
			'hours'     => array(
				'sun' => array(
					array(
						'open'  => '10:00 AM',
						'close' => '4:00 PM',
					),
				),
				'mon' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '5:00 PM',
					),
				),
				'tue' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '5:00 PM',
					),
				),
				'wed' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '5:00 PM',
					),
				),
				'thu' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '5:00 PM',
					),
				),
				'fri' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '5:00 PM',
					),
				),
				'sat' => array(
					array(
						'open'  => '10:00 AM',
						'close' => '4:00 PM',
					),
				),
			),
		);

		$today          = new DateTime( '2025-06-15' );
		$specs_all      = $this->generator->build_opening_hours_specs(
			$season,
			array(),
			$today,
			false,
			$this->day_helpers,
			$this->time_fmt
		);
		$specs_no_wkend = $this->generator->build_opening_hours_specs(
			$season,
			array(),
			$today,
			true,
			$this->day_helpers,
			$this->time_fmt
		);

		$this->assertCount( 7, $specs_all );
		$this->assertCount( 5, $specs_no_wkend );
	}

	/**
	 * Test building specs with a closed holiday.
	 */
	public function test_build_specs_closed_holiday(): void {
		$holidays = array(
			array(
				'name'      => 'Christmas',
				'beginDate' => '2025-12-25',
				'endDate'   => '2025-12-25',
				'slots'     => array(),
			),
		);

		$today = new DateTime( '2025-06-15' );
		$specs = $this->generator->build_opening_hours_specs(
			null,
			$holidays,
			$today,
			false,
			$this->day_helpers,
			$this->time_fmt
		);

		$this->assertCount( 1, $specs );
		$this->assertSame( 'OpeningHoursSpecification', $specs[0]['@type'] );
		$this->assertSame( '2025-12-25', $specs[0]['validFrom'] );
		$this->assertSame( '2025-12-25', $specs[0]['validThrough'] );
		$this->assertArrayNotHasKey( 'opens', $specs[0] );
	}

	/**
	 * Test building specs with a holiday that has open hours.
	 */
	public function test_build_specs_holiday_with_hours(): void {
		$holidays = array(
			array(
				'name'      => 'Holiday',
				'beginDate' => '2025-07-04',
				'endDate'   => '2025-07-04',
				'slots'     => array(
					array(
						'open'  => '10:00 AM',
						'close' => '2:00 PM',
					),
				),
			),
		);

		$today = new DateTime( '2025-06-15' );
		$specs = $this->generator->build_opening_hours_specs(
			null,
			$holidays,
			$today,
			false,
			$this->day_helpers,
			$this->time_fmt
		);

		$this->assertCount( 1, $specs );
		$this->assertArrayHasKey( 'opens', $specs[0] );
		$this->assertSame( '10:00', $specs[0]['opens'] );
		$this->assertSame( '14:00', $specs[0]['closes'] );
	}

	/**
	 * Test recurring (yearless) holidays use the current year.
	 */
	public function test_build_specs_recurring_holiday_uses_current_year(): void {
		$holidays = array(
			array(
				'name'      => 'New Year',
				'beginDate' => '01-01',
				'endDate'   => '01-01',
				'slots'     => array(),
			),
		);

		$today = new DateTime( '2025-06-15' );
		$specs = $this->generator->build_opening_hours_specs(
			null,
			$holidays,
			$today,
			false,
			$this->day_helpers,
			$this->time_fmt
		);

		$this->assertCount( 1, $specs );
		$this->assertSame( '2025-01-01', $specs[0]['validFrom'] );
		$this->assertSame( '2025-01-01', $specs[0]['validThrough'] );
	}

	/**
	 * Test render_json_ld produces a valid script tag.
	 */
	public function test_render_json_ld(): void {
		$season = array(
			'name'      => 'Test',
			'beginDate' => '2025-01-01',
			'endDate'   => '2025-12-31',
			'hours'     => array(
				'sun' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'mon' => array(
					array(
						'open'  => '9:00 AM',
						'close' => '5:00 PM',
					),
				),
				'tue' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'wed' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'thu' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'fri' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'sat' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
			),
		);

		$today = new DateTime( '2025-06-15' );
		$html  = $this->generator->render_json_ld(
			$season,
			array(),
			$today,
			false,
			$this->day_helpers,
			$this->time_fmt
		);

		$this->assertStringStartsWith( '<script type="application/ld+json">', $html );
		$this->assertStringEndsWith( '</script>', $html );
		$this->assertStringContainsString( '"@context":"https://schema.org"', $html );
		$this->assertStringContainsString( '"@type":"Place"', $html );
		$this->assertStringContainsString( 'OpeningHoursSpecification', $html );
	}

	/**
	 * Test render_json_ld returns empty string when no specs.
	 */
	public function test_render_json_ld_empty(): void {
		$today = new DateTime( '2025-06-15' );
		$html  = $this->generator->render_json_ld(
			null,
			array(),
			$today,
			false,
			$this->day_helpers,
			$this->time_fmt
		);

		$this->assertSame( '', $html );
	}

	/**
	 * Test multiple time slots per day produce multiple specs.
	 */
	public function test_build_specs_multiple_slots_per_day(): void {
		$season = array(
			'name'      => 'Split',
			'beginDate' => '2025-01-01',
			'endDate'   => '2025-12-31',
			'hours'     => array(
				'sun' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'mon' => array(
					array(
						'open'  => '8:00 AM',
						'close' => '11:00 AM',
					),
					array(
						'open'  => '1:00 PM',
						'close' => '5:00 PM',
					),
				),
				'tue' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'wed' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'thu' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'fri' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
				'sat' => array(
					array(
						'open'  => '',
						'close' => '',
					),
				),
			),
		);

		$today = new DateTime( '2025-06-15' );
		$specs = $this->generator->build_opening_hours_specs(
			$season,
			array(),
			$today,
			false,
			$this->day_helpers,
			$this->time_fmt
		);

		// Monday has 2 slots, so 2 specs.
		$this->assertCount( 2, $specs );
		$this->assertSame( '08:00', $specs[0]['opens'] );
		$this->assertSame( '13:00', $specs[1]['opens'] );
	}
}

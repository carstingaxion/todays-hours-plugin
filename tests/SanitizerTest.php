<?php
/**
 * Tests for Telex_Hours_Sanitizer.
 *
 * @package TelexHoursBlock\Tests
 */

class SanitizerTest extends WP_UnitTestCase {

	/**
	 * Sanitizer instance.
	 *
	 * @var Telex_Hours_Sanitizer
	 */
	private Telex_Hours_Sanitizer $sanitizer;

	/**
	 * Set up the test fixture.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->sanitizer = Telex_Hours_Sanitizer::get_instance();
	}

	/**
	 * Test sanitize_seasons with valid data.
	 */
	public function test_sanitize_seasons_valid(): void {
		$input = array(
			array(
				'name'      => 'Fall 2025',
				'beginDate' => '2025-09-01',
				'endDate'   => '2025-12-31',
				'hours'     => array(
					'mon' => array(
						array(
							'open'  => '8:00 AM',
							'close' => '5:00 PM',
						),
					),
				),
			),
		);

		$result = $this->sanitizer->sanitize_seasons( $input );

		$this->assertCount( 1, $result );
		$this->assertSame( 'Fall 2025', $result[0]['name'] );
		$this->assertSame( '2025-09-01', $result[0]['beginDate'] );
		$this->assertArrayHasKey( 'mon', $result[0]['hours'] );
		$this->assertSame( '8:00 AM', $result[0]['hours']['mon'][0]['open'] );
	}

	/**
	 * Test sanitize_seasons with non-array input.
	 */
	public function test_sanitize_seasons_non_array(): void {
		$result = $this->sanitizer->sanitize_seasons( 'not-an-array' );
		$this->assertSame( array(), $result );
	}

	/**
	 * Test sanitize_seasons fills missing day keys.
	 */
	public function test_sanitize_seasons_fills_missing_days(): void {
		$input = array(
			array(
				'name'      => 'Test',
				'beginDate' => '2025-01-01',
				'endDate'   => '2025-12-31',
				'hours'     => array(),
			),
		);

		$result = $this->sanitizer->sanitize_seasons( $input );

		$day_keys = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );
		foreach ( $day_keys as $dk ) {
			$this->assertArrayHasKey( $dk, $result[0]['hours'] );
			$this->assertCount( 1, $result[0]['hours'][ $dk ] );
			$this->assertSame( '', $result[0]['hours'][ $dk ][0]['open'] );
		}
	}

	/**
	 * Test sanitize_slots with legacy single-object format.
	 */
	public function test_sanitize_slots_legacy_format(): void {
		$input = array(
			'open'  => '9:00 AM',
			'close' => '5:00 PM',
		);

		$result = $this->sanitizer->sanitize_slots( $input );

		$this->assertCount( 1, $result );
		$this->assertSame( '9:00 AM', $result[0]['open'] );
		$this->assertSame( '5:00 PM', $result[0]['close'] );
	}

	/**
	 * Test sanitize_slots with array-of-slots format.
	 */
	public function test_sanitize_slots_array_format(): void {
		$input = array(
			array(
				'open'  => '8:00 AM',
				'close' => '11:00 AM',
			),
			array(
				'open'  => '1:00 PM',
				'close' => '5:00 PM',
			),
		);

		$result = $this->sanitizer->sanitize_slots( $input );

		$this->assertCount( 2, $result );
		$this->assertSame( '8:00 AM', $result[0]['open'] );
		$this->assertSame( '1:00 PM', $result[1]['open'] );
	}

	/**
	 * Test sanitize_slots with non-array input.
	 */
	public function test_sanitize_slots_non_array(): void {
		$result = $this->sanitizer->sanitize_slots( 'invalid' );

		$this->assertCount( 1, $result );
		$this->assertSame( '', $result[0]['open'] );
	}

	/**
	 * Test sanitize_slots with empty array.
	 */
	public function test_sanitize_slots_empty_array(): void {
		$result = $this->sanitizer->sanitize_slots( array() );

		$this->assertCount( 1, $result );
		$this->assertSame( '', $result[0]['open'] );
	}

	/**
	 * Test sanitize_holidays with valid data.
	 */
	public function test_sanitize_holidays_valid(): void {
		$input = array(
			array(
				'name'      => 'Christmas',
				'beginDate' => '2025-12-25',
				'endDate'   => '2025-12-25',
				'slots'     => array(
					array(
						'open'  => '10:00 AM',
						'close' => '2:00 PM',
					),
				),
			),
		);

		$result = $this->sanitizer->sanitize_holidays( $input );

		$this->assertCount( 1, $result );
		$this->assertSame( 'Christmas', $result[0]['name'] );
		$this->assertCount( 1, $result[0]['slots'] );
		$this->assertSame( '10:00 AM', $result[0]['slots'][0]['open'] );
	}

	/**
	 * Test sanitize_holidays with legacy openTime/closeTime.
	 */
	public function test_sanitize_holidays_legacy_format(): void {
		$input = array(
			array(
				'name'      => 'Legacy Holiday',
				'beginDate' => '2025-07-04',
				'endDate'   => '2025-07-04',
				'openTime'  => '9:00 AM',
				'closeTime' => '1:00 PM',
			),
		);

		$result = $this->sanitizer->sanitize_holidays( $input );

		$this->assertCount( 1, $result );
		$this->assertSame( '9:00 AM', $result[0]['slots'][0]['open'] );
		$this->assertSame( '1:00 PM', $result[0]['slots'][0]['close'] );
	}

	/**
	 * Test sanitize_holidays with empty slots (closed).
	 */
	public function test_sanitize_holidays_closed(): void {
		$input = array(
			array(
				'name'      => 'Closed Day',
				'beginDate' => '2025-12-25',
				'endDate'   => '2025-12-25',
				'slots'     => array(),
			),
		);

		$result = $this->sanitizer->sanitize_holidays( $input );

		$this->assertCount( 1, $result );
		$this->assertSame( array(), $result[0]['slots'] );
	}

	/**
	 * Test sanitize_holidays with non-array input.
	 */
	public function test_sanitize_holidays_non_array(): void {
		$result = $this->sanitizer->sanitize_holidays( 'not-an-array' );
		$this->assertSame( array(), $result );
	}

	/**
	 * Test sanitize_holidays skips non-array entries.
	 */
	public function test_sanitize_holidays_skips_invalid_entries(): void {
		$input = array(
			'not-an-array',
			array(
				'name'      => 'Valid',
				'beginDate' => '2025-01-01',
				'endDate'   => '2025-01-01',
				'slots'     => array(),
			),
		);

		$result = $this->sanitizer->sanitize_holidays( $input );
		$this->assertCount( 1, $result );
		$this->assertSame( 'Valid', $result[0]['name'] );
	}
}

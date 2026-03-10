<?php
/**
 * Tests for Telex_Hours_Day_Helpers.
 *
 * @package TelexHoursBlock\Tests
 */

class DayHelpersTest extends PHPUnit\Framework\TestCase {

	/**
	 * Day helpers instance.
	 *
	 * @var Telex_Hours_Day_Helpers
	 */
	private Telex_Hours_Day_Helpers $helpers;

	/**
	 * Set up the test fixture.
	 */
	protected function setUp(): void {
		$this->helpers = Telex_Hours_Day_Helpers::get_instance();
	}

	/**
	 * Test all day keys are returned in Sunday-first order.
	 */
	public function test_get_all_day_keys(): void {
		$keys = $this->helpers->get_all_day_keys();

		$this->assertCount( 7, $keys );
		$this->assertSame( 'sun', $keys[0] );
		$this->assertSame( 'sat', $keys[6] );
	}

	/**
	 * Test weekend keys.
	 */
	public function test_get_weekend_keys(): void {
		$keys = $this->helpers->get_weekend_keys();

		$this->assertCount( 2, $keys );
		$this->assertContains( 'sun', $keys );
		$this->assertContains( 'sat', $keys );
	}

	/**
	 * Test is_weekend for weekend days.
	 */
	public function test_is_weekend_true(): void {
		$this->assertTrue( $this->helpers->is_weekend( 'sun' ) );
		$this->assertTrue( $this->helpers->is_weekend( 'sat' ) );
	}

	/**
	 * Test is_weekend for weekdays.
	 */
	public function test_is_weekend_false(): void {
		$this->assertFalse( $this->helpers->is_weekend( 'mon' ) );
		$this->assertFalse( $this->helpers->is_weekend( 'wed' ) );
		$this->assertFalse( $this->helpers->is_weekend( 'fri' ) );
	}

	/**
	 * Test ordered day keys respect start_of_week = 0 (Sunday).
	 */
	public function test_get_ordered_day_keys_sunday_start(): void {
		global $telex_test_options;
		$telex_test_options['start_of_week'] = 0;

		$keys = $this->helpers->get_ordered_day_keys();

		$this->assertSame( 'sun', $keys[0] );
		$this->assertSame( 'sat', $keys[6] );
	}

	/**
	 * Test ordered day keys respect start_of_week = 1 (Monday).
	 */
	public function test_get_ordered_day_keys_monday_start(): void {
		global $telex_test_options;
		$telex_test_options['start_of_week'] = 1;

		$keys = $this->helpers->get_ordered_day_keys();

		$this->assertSame( 'mon', $keys[0] );
		$this->assertSame( 'sun', $keys[6] );

		// Reset.
		$telex_test_options['start_of_week'] = 0;
	}

	/**
	 * Test get_day_labels returns all 7 labels.
	 */
	public function test_get_day_labels(): void {
		$labels = $this->helpers->get_day_labels();

		$this->assertCount( 7, $labels );
		$this->assertSame( 'Sunday', $labels['sun'] );
		$this->assertSame( 'Monday', $labels['mon'] );
		$this->assertSame( 'Saturday', $labels['sat'] );
	}

	/**
	 * Test normalize_slots with array-of-slots format.
	 */
	public function test_normalize_slots_array_format(): void {
		$input = array(
			array(
				'open'  => '8:00 AM',
				'close' => '5:00 PM',
			),
		);

		$result = $this->helpers->normalize_slots( $input );

		$this->assertCount( 1, $result );
		$this->assertSame( '8:00 AM', $result[0]['open'] );
		$this->assertSame( '5:00 PM', $result[0]['close'] );
	}

	/**
	 * Test normalize_slots with legacy single object format.
	 */
	public function test_normalize_slots_legacy_format(): void {
		$input = array(
			'open'  => '9:00 AM',
			'close' => '5:00 PM',
		);

		$result = $this->helpers->normalize_slots( $input );

		$this->assertCount( 1, $result );
		$this->assertSame( '9:00 AM', $result[0]['open'] );
	}

	/**
	 * Test normalize_slots with null input.
	 */
	public function test_normalize_slots_null(): void {
		$result = $this->helpers->normalize_slots( null );
		$this->assertSame( array(), $result );
	}

	/**
	 * Test normalize_slots with non-array input.
	 */
	public function test_normalize_slots_string(): void {
		$result = $this->helpers->normalize_slots( 'invalid' );
		$this->assertSame( array(), $result );
	}

	/**
	 * Test normalize_slots with multiple slots.
	 */
	public function test_normalize_slots_multiple(): void {
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

		$result = $this->helpers->normalize_slots( $input );
		$this->assertCount( 2, $result );
	}

	/**
	 * Test normalize_holiday_slots with the 'slots' format.
	 */
	public function test_normalize_holiday_slots_new_format(): void {
		$holiday = array(
			'name'      => 'Test',
			'beginDate' => '2025-12-25',
			'endDate'   => '2025-12-25',
			'slots'     => array(
				array(
					'open'  => '10:00 AM',
					'close' => '2:00 PM',
				),
			),
		);

		$result = $this->helpers->normalize_holiday_slots( $holiday );

		$this->assertCount( 1, $result );
		$this->assertSame( '10:00 AM', $result[0]['open'] );
	}

	/**
	 * Test normalize_holiday_slots with legacy openTime/closeTime format.
	 */
	public function test_normalize_holiday_slots_legacy(): void {
		$holiday = array(
			'name'      => 'Legacy',
			'beginDate' => '2025-12-25',
			'endDate'   => '2025-12-25',
			'openTime'  => '9:00 AM',
			'closeTime' => '12:00 PM',
		);

		$result = $this->helpers->normalize_holiday_slots( $holiday );

		$this->assertCount( 1, $result );
		$this->assertSame( '9:00 AM', $result[0]['open'] );
	}

	/**
	 * Test normalize_holiday_slots with no slots (closed).
	 */
	public function test_normalize_holiday_slots_closed(): void {
		$holiday = array(
			'name'      => 'Closed Day',
			'beginDate' => '2025-12-25',
			'endDate'   => '2025-12-25',
			'slots'     => array(),
		);

		$result = $this->helpers->normalize_holiday_slots( $holiday );
		$this->assertSame( array(), $result );
	}

	/**
	 * Test slots_have_open with open slots.
	 */
	public function test_slots_have_open_true(): void {
		$slots = array(
			array(
				'open'  => '8:00 AM',
				'close' => '5:00 PM',
			),
		);
		$this->assertTrue( $this->helpers->slots_have_open( $slots ) );
	}

	/**
	 * Test slots_have_open with all empty slots.
	 */
	public function test_slots_have_open_false(): void {
		$slots = array(
			array(
				'open'  => '',
				'close' => '',
			),
		);
		$this->assertFalse( $this->helpers->slots_have_open( $slots ) );
	}

	/**
	 * Test slots_have_open with empty array.
	 */
	public function test_slots_have_open_empty_array(): void {
		$this->assertFalse( $this->helpers->slots_have_open( array() ) );
	}

	/**
	 * Test slots_have_open with mixed slots.
	 */
	public function test_slots_have_open_mixed(): void {
		$slots = array(
			array(
				'open'  => '',
				'close' => '',
			),
			array(
				'open'  => '1:00 PM',
				'close' => '5:00 PM',
			),
		);
		$this->assertTrue( $this->helpers->slots_have_open( $slots ) );
	}
}

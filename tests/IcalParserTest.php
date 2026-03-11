<?php
/**
 * Tests for Telex_Hours_Ical_Parser.
 *
 * @package TelexHoursBlock\Tests
 */

class IcalParserTest extends WP_UnitTestCase {

	/**
	 * iCal parser instance.
	 *
	 * @var Telex_Hours_Ical_Parser
	 */
	private Telex_Hours_Ical_Parser $parser;

	/**
	 * Set up the test fixture.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->parser = Telex_Hours_Ical_Parser::get_instance();
	}

	/**
	 * Test parsing an all-day event.
	 */
	public function test_parse_all_day_event(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART;VALUE=DATE:20250704\r\n"
			. "DTEND;VALUE=DATE:20250705\r\n"
			. "SUMMARY:Independence Day\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );

		$this->assertCount( 1, $holidays );
		$this->assertSame( 'Independence Day', $holidays[0]['name'] );
		$this->assertSame( '2025-07-04', $holidays[0]['beginDate'] );
		$this->assertSame( '2025-07-04', $holidays[0]['endDate'] );
		$this->assertSame( array(), $holidays[0]['slots'] );
	}

	/**
	 * Test parsing a multi-day all-day event.
	 */
	public function test_parse_multi_day_event(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART;VALUE=DATE:20251224\r\n"
			. "DTEND;VALUE=DATE:20251227\r\n"
			. "SUMMARY:Christmas Break\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );

		$this->assertCount( 1, $holidays );
		$this->assertSame( '2025-12-24', $holidays[0]['beginDate'] );
		// DTEND is exclusive, so 20251227 minus 1 day = 20251226.
		$this->assertSame( '2025-12-26', $holidays[0]['endDate'] );
	}

	/**
	 * Test parsing a timed event.
	 */
	public function test_parse_timed_event(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART:20250704T100000\r\n"
			. "DTEND:20250704T140000\r\n"
			. "SUMMARY:Half Day\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );

		$this->assertCount( 1, $holidays );
		$this->assertSame( 'Half Day', $holidays[0]['name'] );
		$this->assertSame( '2025-07-04', $holidays[0]['beginDate'] );
		$this->assertCount( 1, $holidays[0]['slots'] );
		$this->assertSame( '10:00 AM', $holidays[0]['slots'][0]['open'] );
		$this->assertSame( '2:00 PM', $holidays[0]['slots'][0]['close'] );
	}

	/**
	 * Test parsing a timed event with UTC (Z) suffix.
	 */
	public function test_parse_timed_event_utc(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART:20250704T150000Z\r\n"
			. "DTEND:20250704T200000Z\r\n"
			. "SUMMARY:UTC Event\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );

		$this->assertCount( 1, $holidays );
		$this->assertSame( '2025-07-04', $holidays[0]['beginDate'] );
		$this->assertSame( '3:00 PM', $holidays[0]['slots'][0]['open'] );
	}

	/**
	 * Test parsing multiple events.
	 */
	public function test_parse_multiple_events(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART;VALUE=DATE:20250704\r\n"
			. "DTEND;VALUE=DATE:20250705\r\n"
			. "SUMMARY:July 4th\r\n"
			. "END:VEVENT\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART;VALUE=DATE:20250901\r\n"
			. "DTEND;VALUE=DATE:20250902\r\n"
			. "SUMMARY:Labor Day\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );

		$this->assertCount( 2, $holidays );
		$this->assertSame( 'July 4th', $holidays[0]['name'] );
		$this->assertSame( 'Labor Day', $holidays[1]['name'] );
	}

	/**
	 * Test parsing with no events returns empty array.
	 */
	public function test_parse_no_events(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "VERSION:2.0\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );
		$this->assertSame( array(), $holidays );
	}

	/**
	 * Test parsing empty string.
	 */
	public function test_parse_empty_string(): void {
		$holidays = $this->parser->parse( '' );
		$this->assertSame( array(), $holidays );
	}

	/**
	 * Test parsing event without DTSTART is skipped.
	 */
	public function test_parse_event_without_dtstart(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "SUMMARY:No Date\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );
		$this->assertSame( array(), $holidays );
	}

	/**
	 * Test parsing handles line folding (RFC 5545).
	 */
	public function test_parse_line_folding(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART;VALUE=DATE:20250704\r\n"
			. "DTEND;VALUE=DATE:20250705\r\n"
			. "SUMMARY:Independence\r\n"
			. " Day Holiday\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );

		$this->assertCount( 1, $holidays );
		$this->assertSame( 'IndependenceDay Holiday', $holidays[0]['name'] );
	}

	/**
	 * Test parsing single-day all-day event where DTEND equals DTSTART.
	 */
	public function test_parse_single_day_dtend_equals_dtstart(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART;VALUE=DATE:20251225\r\n"
			. "DTEND;VALUE=DATE:20251225\r\n"
			. "SUMMARY:Christmas\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );

		$this->assertCount( 1, $holidays );
		$this->assertSame( '2025-12-25', $holidays[0]['beginDate'] );
		$this->assertSame( '2025-12-25', $holidays[0]['endDate'] );
	}

	/**
	 * Test parsing event without SUMMARY uses empty name.
	 */
	public function test_parse_event_without_summary(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART;VALUE=DATE:20250704\r\n"
			. "DTEND;VALUE=DATE:20250705\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );

		$this->assertCount( 1, $holidays );
		$this->assertSame( '', $holidays[0]['name'] );
	}

	/**
	 * Test parsing event without DTEND uses DTSTART as end date.
	 */
	public function test_parse_event_without_dtend(): void {
		$ical = "BEGIN:VCALENDAR\r\n"
			. "BEGIN:VEVENT\r\n"
			. "DTSTART;VALUE=DATE:20250704\r\n"
			. "SUMMARY:No End\r\n"
			. "END:VEVENT\r\n"
			. "END:VCALENDAR\r\n";

		$holidays = $this->parser->parse( $ical );

		$this->assertCount( 1, $holidays );
		$this->assertSame( '2025-07-04', $holidays[0]['beginDate'] );
		$this->assertSame( '2025-07-04', $holidays[0]['endDate'] );
	}
}

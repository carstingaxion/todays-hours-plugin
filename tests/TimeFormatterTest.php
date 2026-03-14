<?php
/**
 * Tests formatting utilities.
 *
 * @package TelexHoursBlock\Tests
 */

/**
 * Tests for Telex_Hours_Time_Formatter.
 */
class TimeFormatterTest extends PHPUnit\Framework\TestCase {

	/**
	 * Time formatter instance.
	 *
	 * @var Telex_Hours_Time_Formatter
	 */
	private Telex_Hours_Time_Formatter $formatter;

	/**
	 * Set up the test fixture.
	 */
	protected function setUp(): void {
		$this->formatter = Telex_Hours_Time_Formatter::get_instance();
	}

	/**
	 * Test friendly twelves replaces 12:00 AM with Midnight.
	 */
	public function test_friendly_twelves_midnight(): void {
		$result = $this->formatter->friendly_twelves( '12:00 AM', true );
		$this->assertSame( 'Midnight', $result );
	}

	/**
	 * Test friendly twelves replaces 12:00 PM with Noon.
	 */
	public function test_friendly_twelves_noon(): void {
		$result = $this->formatter->friendly_twelves( '12:00 PM', true );
		$this->assertSame( 'Noon', $result );
	}

	/**
	 * Test friendly twelves does not replace other times.
	 */
	public function test_friendly_twelves_regular_time(): void {
		$result = $this->formatter->friendly_twelves( '3:00 PM', true );
		$this->assertSame( '3:00 PM', $result );
	}

	/**
	 * Test friendly twelves disabled returns original.
	 */
	public function test_friendly_twelves_disabled(): void {
		$result = $this->formatter->friendly_twelves( '12:00 AM', false );
		$this->assertSame( '12:00 AM', $result );
	}

	/**
	 * Test friendly twelves with empty string.
	 */
	public function test_friendly_twelves_empty(): void {
		$result = $this->formatter->friendly_twelves( '', true );
		$this->assertSame( '', $result );
	}

	/**
	 * Test friendly twelves handles case-insensitive input.
	 */
	public function test_friendly_twelves_case_insensitive(): void {
		$this->assertSame( 'Midnight', $this->formatter->friendly_twelves( '12:00 am', true ) );
		$this->assertSame( 'Noon', $this->formatter->friendly_twelves( '12:00 pm', true ) );
		$this->assertSame( 'Midnight', $this->formatter->friendly_twelves( '12:00AM', true ) );
	}

	/**
	 * Test to_24h converts AM times.
	 */
	public function test_to_24h_am(): void {
		$this->assertSame( '08:00', $this->formatter->to_24h( '8:00 AM' ) );
		$this->assertSame( '09:30', $this->formatter->to_24h( '9:30 AM' ) );
	}

	/**
	 * Test to_24h converts PM times.
	 */
	public function test_to_24h_pm(): void {
		$this->assertSame( '17:00', $this->formatter->to_24h( '5:00 PM' ) );
		$this->assertSame( '13:00', $this->formatter->to_24h( '1:00 PM' ) );
		$this->assertSame( '23:00', $this->formatter->to_24h( '11:00 PM' ) );
	}

	/**
	 * Test to_24h handles noon and midnight.
	 */
	public function test_to_24h_noon_midnight(): void {
		$this->assertSame( '12:00', $this->formatter->to_24h( '12:00 PM' ) );
		$this->assertSame( '00:00', $this->formatter->to_24h( '12:00 AM' ) );
	}

	/**
	 * Test to_24h with empty string.
	 */
	public function test_to_24h_empty(): void {
		$this->assertSame( '', $this->formatter->to_24h( '' ) );
	}

	/**
	 * Test to_24h with unparseable string.
	 */
	public function test_to_24h_invalid(): void {
		$this->assertSame( '', $this->formatter->to_24h( 'not-a-time' ) );
	}

	/**
	 * Test format_time uses the site time_format option.
	 */
	public function test_format_time(): void {
		// The stub get_option returns 'g:i a' for time_format.
		$result = $this->formatter->format_time( '8:00 AM' );
		$this->assertSame( '8:00 am', $result );
	}

	/**
	 * Test format_time with PM time.
	 */
	public function test_format_time_pm(): void {
		$result = $this->formatter->format_time( '5:00 PM' );
		$this->assertSame( '5:00 pm', $result );
	}

	/**
	 * Test format_time with empty string.
	 */
	public function test_format_time_empty(): void {
		$result = $this->formatter->format_time( '' );
		$this->assertSame( '', $result );
	}

	/**
	 * Test render_slots_html with a single slot.
	 */
	public function test_render_slots_html_single(): void {
		$slots = array(
			array(
				'open'  => '8:00 AM',
				'close' => '5:00 PM',
			),
		);

		$html = $this->formatter->render_slots_html( $slots, false );

		$this->assertStringContainsString( '<time datetime="08:00">', $html );
		$this->assertStringContainsString( '<time datetime="17:00">', $html );
		$this->assertStringContainsString( 'telex-hours-block__slot', $html );
		$this->assertStringContainsString( 'telex-hours-block__separator', $html );
	}

	/**
	 * Test render_slots_html with multiple slots produces <br> separator.
	 */
	public function test_render_slots_html_multiple(): void {
		$slots = array(
			array(
				'open'  => '8:00 AM',
				'close' => '11:00 AM',
			),
			array(
				'open'  => '1:00 PM',
				'close' => '5:00 PM',
			),
		);

		$html = $this->formatter->render_slots_html( $slots, false );

		$this->assertStringContainsString( '<br>', $html );
		$this->assertStringContainsString( 'datetime="08:00"', $html );
		$this->assertStringContainsString( 'datetime="13:00"', $html );
	}

	/**
	 * Test render_slots_html skips empty slots.
	 */
	public function test_render_slots_html_skips_empty(): void {
		$slots = array(
			array(
				'open'  => '',
				'close' => '',
			),
			array(
				'open'  => '9:00 AM',
				'close' => '5:00 PM',
			),
		);

		$html = $this->formatter->render_slots_html( $slots, false );

		$this->assertStringNotContainsString( '<br>', $html );
		$this->assertStringContainsString( 'datetime="09:00"', $html );
	}

	/**
	 * Test render_slots_html with all empty slots returns empty string.
	 */
	public function test_render_slots_html_all_empty(): void {
		$slots = array(
			array(
				'open'  => '',
				'close' => '',
			),
		);

		$html = $this->formatter->render_slots_html( $slots, false );

		$this->assertSame( '', $html );
	}

	/**
	 * Test render_slots_html with friendly twelves.
	 */
	public function test_render_slots_html_friendly(): void {
		$slots = array(
			array(
				'open'  => '12:00 AM',
				'close' => '12:00 PM',
			),
		);

		$html = $this->formatter->render_slots_html( $slots, true );

		$this->assertStringContainsString( 'Midnight', $html );
		$this->assertStringContainsString( 'Noon', $html );
	}
}

<?php
/**
 * The iCal parser and REST route for Business Hours Block.
 *
 * Parses VEVENT components from iCal text and provides a REST endpoint
 * for importing holidays from .ics files.
 *
 * @package TelexHoursBlock
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Telex_Hours_Ical_Parser' ) ) {
	/**
	 * Parses iCal data and registers a REST import endpoint.
	 *
	 * @since 0.1.0
	 */
	class Telex_Hours_Ical_Parser {

		/**
		 * The single instance of this class.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Ical_Parser|null
		 */
		private static ?Telex_Hours_Ical_Parser $instance = null;

		/**
		 * Retrieves the single instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @return Telex_Hours_Ical_Parser The singleton instance.
		 */
		public static function get_instance(): Telex_Hours_Ical_Parser {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 0.1.0
		 */
		private function __construct() {}

		/**
		 * Registers the iCal import REST route.
		 *
		 * @since 0.1.0
		 *
		 * @return void
		 */
		public function register_routes(): void {
			register_rest_route(
				'telex-hours-block/v1',
				'/import-ical',
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'handle_import' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(
						'ical_text' => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_textarea_field',
						),
					),
				)
			);
		}

		/**
		 * Handles the iCal import REST request.
		 *
		 * @since 0.1.0
		 *
		 * @param WP_REST_Request $request The REST request object.
		 * @return WP_REST_Response The parsed holidays.
		 */
		public function handle_import( $request ) {
			$ical_text_raw = $request->get_param( 'ical_text' );
			$ical_text     = is_string( $ical_text_raw ) ? $ical_text_raw : '';
			$holidays      = $this->parse( $ical_text );

			return rest_ensure_response(
				array(
					'holidays' => $holidays,
					'count'    => count( $holidays ),
				) 
			);
		}

		/**
		 * Parses iCal text and extracts VEVENT components as holiday objects.
		 *
		 * Each VEVENT is converted to a holiday with name, beginDate, endDate,
		 * and slots. All-day events produce a holiday with empty slots (closed).
		 * Timed events produce a holiday with one slot containing the times.
		 *
		 * @since 0.1.0
		 *
		 * @param string $ical_text Raw iCal file content.
		 * @return array<int, array{name: string, beginDate: string, endDate: string, slots: array<int, array{open: string, close: string}>}> Array of holiday data arrays.
		 */
		public function parse( string $ical_text ): array {
			$holidays = array();

			// Unfold lines per RFC 5545 (continuation lines start with space or tab).
			$unfolded = preg_replace( '/\r\n[ \t]/', '', $ical_text );
			if ( ! is_string( $unfolded ) ) {
				$unfolded = $ical_text;
			}
			$normalized = preg_replace( '/\r/', "\n", $unfolded );
			if ( ! is_string( $normalized ) ) {
				$normalized = $unfolded;
			}
			$lines = explode( "\n", $normalized );

			$in_event = false;
			/**
			 * Temporary storage for VEVENT properties while parsing.
			 *
			 * @var array<string, string> $event_data
			 */
			$event_data = array();

			foreach ( $lines as $line ) {
				$line = trim( $line );

				if ( 'BEGIN:VEVENT' === strtoupper( $line ) ) {
					$in_event   = true;
					$event_data = array();
					continue;
				}

				if ( 'END:VEVENT' === strtoupper( $line ) ) {
					$in_event = false;
					$holiday  = $this->vevent_to_holiday( $event_data );
					if ( null !== $holiday ) {
						$holidays[] = $holiday;
					}
					continue;
				}

				if ( $in_event ) {
					$colon_pos = strpos( $line, ':' );
					if ( false !== $colon_pos ) {
						$key                = strtoupper( substr( $line, 0, $colon_pos ) );
						$value              = substr( $line, $colon_pos + 1 );
						$event_data[ $key ] = $value;
					}
				}
			}

			return $holidays;
		}

		/**
		 * Converts parsed VEVENT data into a holiday array.
		 *
		 * @since 0.1.0
		 *
		 * @param array<string, string> $event_data Associative array of VEVENT properties.
		 * @return array{name: string, beginDate: string, endDate: string, slots: array<int, array{open: string, close: string}>}|null Holiday data array or null if insufficient data.
		 */
		private function vevent_to_holiday( array $event_data ): ?array {
			$summary     = $this->extract_summary( $event_data );
			$dtstart_raw = $this->extract_property_value( $event_data, 'DTSTART' );
			$dtend_raw   = $this->extract_property_value( $event_data, 'DTEND' );

			if ( '' === $dtstart_raw ) {
				return null;
			}

			$begin_date = $this->parse_datetime( $dtstart_raw );
			if ( '' === $begin_date ) {
				return null;
			}

			$end_date   = '' !== $dtend_raw ? $this->parse_datetime( $dtend_raw ) : $begin_date;
			$is_all_day = $this->is_all_day_event( $event_data, $dtstart_raw );

			$end_date = $this->adjust_end_date( $begin_date, $end_date, $is_all_day );
			$slots    = $this->build_slots( $is_all_day, $dtstart_raw, $dtend_raw );

			return array(
				'name'      => sanitize_text_field( $summary ),
				'beginDate' => $begin_date,
				'endDate'   => $end_date,
				'slots'     => $slots,
			);
		}

		/**
		 * Extracts the SUMMARY value from VEVENT data.
		 *
		 * Handles both plain "SUMMARY" keys and parameterized "SUMMARY;..." keys.
		 *
		 * @since 0.1.0
		 *
		 * @param array<string, string> $event_data VEVENT properties.
		 * @return string The summary value, or empty string if not found.
		 */
		private function extract_summary( array $event_data ): string {
			foreach ( $event_data as $key => $value ) {
				if ( 'SUMMARY' === $key || 0 === strpos( $key, 'SUMMARY;' ) ) {
					return $value;
				}
			}
			return '';
		}

		/**
		 * Extracts the value of a named iCal property from VEVENT data.
		 *
		 * Handles both plain keys (e.g. "DTSTART") and parameterized keys
		 * (e.g. "DTSTART;VALUE=DATE").
		 *
		 * @since 0.1.0
		 *
		 * @param array<string, string> $event_data   VEVENT properties.
		 * @param string                $property_name The property name to search for (e.g. "DTSTART").
		 * @return string The property value, or empty string if not found.
		 */
		private function extract_property_value( array $event_data, string $property_name ): string {
			$prefix = strtoupper( $property_name );
			foreach ( $event_data as $key => $value ) {
				$key_upper = strtoupper( $key );
				if ( $prefix === $key_upper || 0 === strpos( $key_upper, $prefix . ';' ) ) {
					return $value;
				}
			}
			return '';
		}

		/**
		 * Determines whether a VEVENT represents an all-day event.
		 *
		 * Checks for VALUE=DATE in the DTSTART property key and for
		 * an 8-digit date-only value (YYYYMMDD format).
		 *
		 * @since 0.1.0
		 *
		 * @param array<string, string> $event_data  VEVENT properties.
		 * @param string                $dtstart_raw The raw DTSTART value.
		 * @return bool True if the event is all-day.
		 */
		private function is_all_day_event( array $event_data, string $dtstart_raw ): bool {
			if ( 1 === preg_match( '/^\d{8}$/', $dtstart_raw ) ) {
				return true;
			}

			foreach ( $event_data as $key => $value ) {
				if ( 0 === strpos( strtoupper( $key ), 'DTSTART' ) ) {
					return ( false !== strpos( strtoupper( $key ), 'VALUE=DATE' ) );
				}
			}

			return false;
		}

		/**
		 * Adjusts the end date for all-day events.
		 *
		 * In iCal, DTEND for all-day events is the day after the last day
		 * of the event, so this subtracts one day when applicable.
		 *
		 * @since 0.1.0
		 *
		 * @param string $begin_date  The begin date in Y-m-d format.
		 * @param string $end_date    The end date in Y-m-d format.
		 * @param bool   $is_all_day  Whether the event is all-day.
		 * @return string The adjusted end date in Y-m-d format.
		 */
		private function adjust_end_date( string $begin_date, string $end_date, bool $is_all_day ): string {
			if ( ! $is_all_day || '' === $end_date || $end_date === $begin_date ) {
				return $end_date;
			}

			$end_dt = new DateTime( $end_date );
			$end_dt->modify( '-1 day' );
			return $end_dt->format( 'Y-m-d' );
		}

		/**
		 * Builds the time slots array for a holiday from VEVENT data.
		 *
		 * All-day events produce an empty array (closed). Timed events
		 * produce a single slot with the parsed start and end times.
		 *
		 * @since 0.1.0
		 *
		 * @param bool   $is_all_day  Whether the event is all-day.
		 * @param string $dtstart_raw The raw DTSTART value.
		 * @param string $dtend_raw   The raw DTEND value.
		 * @return array<int, array{open: string, close: string}> Array of time slots.
		 */
		private function build_slots( bool $is_all_day, string $dtstart_raw, string $dtend_raw ): array {
			if ( $is_all_day ) {
				return array();
			}

			$start_time = $this->parse_time( $dtstart_raw );
			if ( '' === $start_time ) {
				return array();
			}

			$end_time = $this->parse_time( $dtend_raw );
			return array(
				array(
					'open'  => $start_time,
					'close' => $end_time,
				),
			);
		}

		/**
		 * Parses an iCal datetime value into a Y-m-d date string.
		 *
		 * Handles formats: YYYYMMDD, YYYYMMDDTHHmmss, YYYYMMDDTHHmmssZ.
		 *
		 * @since 0.1.0
		 *
		 * @param string $value iCal datetime value.
		 * @return string Date in Y-m-d format, or empty string.
		 */
		private function parse_datetime( string $value ): string {
			$value = trim( $value );
			// Remove trailing Z.
			$value = rtrim( $value, 'Z' );
			// Take only the date part (first 8 chars).
			$date_part = substr( $value, 0, 8 );
			if ( 1 !== preg_match( '/^\d{8}$/', $date_part ) ) {
				return '';
			}
			$year  = substr( $date_part, 0, 4 );
			$month = substr( $date_part, 4, 2 );
			$day   = substr( $date_part, 6, 2 );

			if ( ! checkdate( (int) $month, (int) $day, (int) $year ) ) {
				return '';
			}

			return $year . '-' . $month . '-' . $day;
		}

		/**
		 * Parses an iCal datetime value into a human-readable time string.
		 *
		 * @since 0.1.0
		 *
		 * @param string $value iCal datetime value.
		 * @return string Time in "g:i A" format, or empty string.
		 */
		private function parse_time( string $value ): string {
			$value = trim( $value );
			$value = rtrim( $value, 'Z' );
			// Check for T separator indicating time component.
			$t_pos = strpos( $value, 'T' );
			if ( false === $t_pos ) {
				return '';
			}
			$time_part = substr( $value, $t_pos + 1 );
			if ( strlen( $time_part ) < 4 ) {
				return '';
			}
			$hours   = (int) substr( $time_part, 0, 2 );
			$minutes = (int) substr( $time_part, 2, 2 );

			$timestamp = mktime( $hours, $minutes, 0, 1, 1, 2000 );
			if ( false === $timestamp ) {
				return '';
			}
			return (string) wp_date( 'g:i A', $timestamp );
		}
	}
}

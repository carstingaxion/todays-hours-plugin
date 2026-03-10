<?php
/**
 * iCal parser and REST route for Business Hours Block.
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
			$ical_text = $request->get_param( 'ical_text' );
			$holidays  = $this->parse( $ical_text );

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
		 * @return array Array of holiday data arrays.
		 */
		public function parse( string $ical_text ): array {
			$holidays = array();

			// Unfold lines per RFC 5545 (continuation lines start with space or tab).
			$ical_text = preg_replace( '/\r\n[ \t]/', '', $ical_text );
			$ical_text = preg_replace( '/\r/', "\n", $ical_text );
			$lines     = explode( "\n", $ical_text );

			$in_event   = false;
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
		 * @param array $event_data Associative array of VEVENT properties.
		 * @return array|null Holiday data array or null if insufficient data.
		 */
		private function vevent_to_holiday( array $event_data ): ?array {
			$summary = '';
			foreach ( $event_data as $key => $value ) {
				if ( 'SUMMARY' === $key || 0 === strpos( $key, 'SUMMARY;' ) ) {
					$summary = $value;
					break;
				}
			}

			$dtstart_raw = '';
			$dtend_raw   = '';
			foreach ( $event_data as $key => $value ) {
				$key_upper = strtoupper( $key );
				if ( 'DTSTART' === $key_upper || 0 === strpos( $key_upper, 'DTSTART;' ) ) {
					$dtstart_raw = $value;
				}
				if ( 'DTEND' === $key_upper || 0 === strpos( $key_upper, 'DTEND;' ) ) {
					$dtend_raw = $value;
				}
			}

			if ( empty( $dtstart_raw ) ) {
				return null;
			}

			$is_all_day  = false;
			$dtstart_key = '';
			foreach ( $event_data as $key => $value ) {
				if ( 0 === strpos( strtoupper( $key ), 'DTSTART' ) ) {
					$dtstart_key = $key;
					break;
				}
			}
			if ( false !== strpos( strtoupper( $dtstart_key ), 'VALUE=DATE' ) ) {
				$is_all_day = true;
			}
			// Also check if the value is exactly 8 digits (YYYYMMDD format = all-day).
			if ( preg_match( '/^\d{8}$/', $dtstart_raw ) ) {
				$is_all_day = true;
			}

			$begin_date = $this->parse_datetime( $dtstart_raw );
			$end_date   = ! empty( $dtend_raw ) ? $this->parse_datetime( $dtend_raw ) : $begin_date;

			if ( empty( $begin_date ) ) {
				return null;
			}

			$slots = array();

			if ( $is_all_day ) {
				// For all-day events, DTEND is the day after the last day.
				// Adjust end date back by one day.
				if ( ! empty( $end_date ) && $end_date !== $begin_date ) {
					$end_dt = new DateTime( $end_date );
					$end_dt->modify( '-1 day' );
					$end_date = $end_dt->format( 'Y-m-d' );
				}
				// All-day: no slots means closed.
			} else {
				// Timed event: extract times.
				$start_time = $this->parse_time( $dtstart_raw );
				$end_time   = $this->parse_time( $dtend_raw );
				if ( ! empty( $start_time ) ) {
					$slots = array(
						array(
							'open'  => $start_time,
							'close' => ! empty( $end_time ) ? $end_time : '',
						),
					);
				}
			}

			return array(
				'name'      => sanitize_text_field( $summary ),
				'beginDate' => $begin_date,
				'endDate'   => $end_date,
				'slots'     => $slots,
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
			if ( ! preg_match( '/^\d{8}$/', $date_part ) ) {
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
			return date( 'g:i A', $timestamp );
		}
	}
}

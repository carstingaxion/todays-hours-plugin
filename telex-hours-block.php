<?php
/**
 * Plugin Name:       Today's Hours
 * Description:       Displays the current day's business hours or a full weekly schedule. Seasons and holidays can be customized. Ideal for institutions with variable yearly schedules.
 * Version:           2.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            WordPress Telex
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       telex-hours-block
 *
 * @package TelexHoursBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class using Singleton pattern.
 *
 * Encapsulates block registration, settings registration, and sanitization
 * logic. Ensures only one instance of the plugin is loaded at any time.
 *
 * @since 0.1.0
 */
class Telex_Hours_Block {

	/**
	 * The single instance of this class.
	 *
	 * @since 0.1.0
	 * @var Telex_Hours_Block|null
	 */
	private static ?Telex_Hours_Block $instance = null;

	/**
	 * Default season data used when no seasons have been configured.
	 *
	 * @since 0.1.0
	 * @var array
	 */
	private array $default_seasons;

	/**
	 * Retrieves the single instance of this class.
	 *
	 * @since 0.1.0
	 *
	 * @return Telex_Hours_Block The singleton instance.
	 */
	public static function get_instance(): Telex_Hours_Block {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor. Sets up defaults and registers hooks.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
		$this->default_seasons = array(
			array(
				'name'      => 'Normal Schedule',
				'beginDate' => '2024-01-01',
				'endDate'   => '2026-12-31',
				'hours'     => array(
					'sun' => array( array( 'open' => '', 'close' => '' ) ),
					'mon' => array( array( 'open' => '8:00 AM', 'close' => '11:00 PM' ) ),
					'tue' => array( array( 'open' => '8:00 AM', 'close' => '11:00 PM' ) ),
					'wed' => array( array( 'open' => '8:00 AM', 'close' => '11:00 PM' ) ),
					'thu' => array( array( 'open' => '8:00 AM', 'close' => '11:00 PM' ) ),
					'fri' => array( array( 'open' => '8:00 AM', 'close' => '9:00 PM' ) ),
					'sat' => array( array( 'open' => '', 'close' => '' ) ),
				),
			),
		);

		$this->register_hooks();
	}

	/**
	 * Prevents cloning of the singleton instance.
	 *
	 * @since 0.1.0
	 */
	private function __clone() {}

	/**
	 * Prevents unserializing of the singleton instance.
	 *
	 * @since 0.1.0
	 *
	 * @throws \RuntimeException Always.
	 */
	public function __wakeup() {
		throw new \RuntimeException( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Registers all WordPress hooks used by this plugin.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function register_hooks(): void {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'init', array( $this, 'register_settings' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Registers the block type using metadata from block.json.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_block(): void {
		register_block_type( __DIR__ . '/build/' );
	}

	/**
	 * Registers the site-wide options for seasons and holidays.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_settings(): void {
		$slot_schema = array(
			'type'       => 'object',
			'properties' => array(
				'open'  => array( 'type' => 'string' ),
				'close' => array( 'type' => 'string' ),
			),
			'additionalProperties' => false,
		);

		$day_slots_schema = array(
			'type'  => 'array',
			'items' => $slot_schema,
		);

		register_setting(
			'telex_hours_block',
			'telex_hours_seasons',
			array(
				'type'              => 'array',
				'description'       => __( 'Business hours seasons/semesters schedule data.', 'telex-hours-block' ),
				'default'           => $this->default_seasons,
				'show_in_rest'      => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'name'      => array( 'type' => 'string' ),
								'beginDate' => array( 'type' => 'string' ),
								'endDate'   => array( 'type' => 'string' ),
								'hours'     => array(
									'type'       => 'object',
									'properties' => array(
										'sun' => $day_slots_schema,
										'mon' => $day_slots_schema,
										'tue' => $day_slots_schema,
										'wed' => $day_slots_schema,
										'thu' => $day_slots_schema,
										'fri' => $day_slots_schema,
										'sat' => $day_slots_schema,
									),
									'additionalProperties' => false,
								),
							),
							'additionalProperties' => false,
						),
					),
				),
				'sanitize_callback' => array( $this, 'sanitize_seasons' ),
			)
		);

		register_setting(
			'telex_hours_block',
			'telex_hours_holidays',
			array(
				'type'              => 'array',
				'description'       => __( 'Business hours holidays/exceptions data.', 'telex-hours-block' ),
				'default'           => array(),
				'show_in_rest'      => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'name'      => array( 'type' => 'string' ),
								'beginDate' => array( 'type' => 'string' ),
								'endDate'   => array( 'type' => 'string' ),
								'slots'     => array(
									'type'  => 'array',
									'items' => $slot_schema,
								),
							),
							'additionalProperties' => false,
						),
					),
				),
				'sanitize_callback' => array( $this, 'sanitize_holidays' ),
			)
		);
	}

	/**
	 * Registers custom REST API routes for the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_rest_routes(): void {
		register_rest_route(
			'telex-hours-block/v1',
			'/import-ical',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_ical_import' ),
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
	 * Parses VEVENT components from the provided iCal text and returns
	 * an array of holiday objects suitable for merging into the holidays setting.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response The parsed holidays.
	 */
	public function handle_ical_import( $request ) {
		$ical_text = $request->get_param( 'ical_text' );
		$holidays  = $this->parse_ical( $ical_text );

		return rest_ensure_response( array(
			'holidays' => $holidays,
			'count'    => count( $holidays ),
		) );
	}

	/**
	 * Parses iCal text and extracts VEVENT components as holiday objects.
	 *
	 * Each VEVENT is converted to a holiday with name, beginDate, endDate,
	 * and slots. All-day events (DATE values or DTEND equals DTSTART + 1 day)
	 * produce a single-day holiday with empty slots (closed). Timed events
	 * produce a holiday with one slot containing the start and end times.
	 *
	 * @since 0.1.0
	 *
	 * @param string $ical_text Raw iCal file content.
	 * @return array Array of holiday data arrays.
	 */
	private function parse_ical( string $ical_text ): array {
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
					$key   = strtoupper( substr( $line, 0, $colon_pos ) );
					$value = substr( $line, $colon_pos + 1 );
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

		$is_all_day = false;
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

		$begin_date = $this->parse_ical_datetime( $dtstart_raw );
		$end_date   = ! empty( $dtend_raw ) ? $this->parse_ical_datetime( $dtend_raw ) : $begin_date;

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
			$start_time = $this->parse_ical_time( $dtstart_raw );
			$end_time   = $this->parse_ical_time( $dtend_raw );
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
	private function parse_ical_datetime( string $value ): string {
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
	private function parse_ical_time( string $value ): string {
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

	/**
	 * Sanitizes the seasons option value.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value The raw option value.
	 * @return array Sanitized seasons array.
	 */
	public function sanitize_seasons( $value ): array {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$sanitized = array();
		$day_keys  = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );

		foreach ( $value as $season ) {
			if ( ! is_array( $season ) ) {
				continue;
			}

			$clean = array(
				'name'      => isset( $season['name'] ) ? sanitize_text_field( $season['name'] ) : '',
				'beginDate' => isset( $season['beginDate'] ) ? sanitize_text_field( $season['beginDate'] ) : '',
				'endDate'   => isset( $season['endDate'] ) ? sanitize_text_field( $season['endDate'] ) : '',
				'hours'     => array(),
			);

			$hours = isset( $season['hours'] ) && is_array( $season['hours'] ) ? $season['hours'] : array();

			foreach ( $day_keys as $dk ) {
				$day_data           = isset( $hours[ $dk ] ) ? $hours[ $dk ] : array();
				$clean['hours'][ $dk ] = $this->sanitize_slots( $day_data );
			}

			$sanitized[] = $clean;
		}

		return $sanitized;
	}

	/**
	 * Sanitizes an array of time slots.
	 *
	 * Handles both the new array-of-slots format and the legacy single
	 * {open, close} object format for backward compatibility.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $slots Raw slots data.
	 * @return array Sanitized array of slot objects.
	 */
	private function sanitize_slots( $slots ): array {
		if ( ! is_array( $slots ) ) {
			return array( array( 'open' => '', 'close' => '' ) );
		}

		// Legacy format: { open: '...', close: '...' } — convert to array of one slot.
		if ( isset( $slots['open'] ) || isset( $slots['close'] ) ) {
			return array(
				array(
					'open'  => isset( $slots['open'] ) ? sanitize_text_field( $slots['open'] ) : '',
					'close' => isset( $slots['close'] ) ? sanitize_text_field( $slots['close'] ) : '',
				),
			);
		}

		$sanitized = array();
		foreach ( $slots as $slot ) {
			if ( ! is_array( $slot ) ) {
				continue;
			}
			$sanitized[] = array(
				'open'  => isset( $slot['open'] ) ? sanitize_text_field( $slot['open'] ) : '',
				'close' => isset( $slot['close'] ) ? sanitize_text_field( $slot['close'] ) : '',
			);
		}

		if ( empty( $sanitized ) ) {
			return array( array( 'open' => '', 'close' => '' ) );
		}

		return $sanitized;
	}

	/**
	 * Sanitizes the holidays option value.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value The raw option value.
	 * @return array Sanitized holidays array.
	 */
	public function sanitize_holidays( $value ): array {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$sanitized = array();
		foreach ( $value as $holiday ) {
			if ( ! is_array( $holiday ) ) {
				continue;
			}

			$slots_raw = array();
			if ( isset( $holiday['slots'] ) && is_array( $holiday['slots'] ) ) {
				$slots_raw = $holiday['slots'];
			} elseif ( isset( $holiday['openTime'] ) || isset( $holiday['closeTime'] ) ) {
				// Legacy format migration.
				$slots_raw = array(
					array(
						'open'  => isset( $holiday['openTime'] ) ? $holiday['openTime'] : '',
						'close' => isset( $holiday['closeTime'] ) ? $holiday['closeTime'] : '',
					),
				);
			}

			$clean_slots = array();
			foreach ( $slots_raw as $slot ) {
				if ( ! is_array( $slot ) ) {
					continue;
				}
				$clean_slots[] = array(
					'open'  => isset( $slot['open'] ) ? sanitize_text_field( $slot['open'] ) : '',
					'close' => isset( $slot['close'] ) ? sanitize_text_field( $slot['close'] ) : '',
				);
			}

			$sanitized[] = array(
				'name'      => isset( $holiday['name'] ) ? sanitize_text_field( $holiday['name'] ) : '',
				'beginDate' => isset( $holiday['beginDate'] ) ? sanitize_text_field( $holiday['beginDate'] ) : '',
				'endDate'   => isset( $holiday['endDate'] ) ? sanitize_text_field( $holiday['endDate'] ) : '',
				'slots'     => $clean_slots,
			);
		}

		return $sanitized;
	}
}

Telex_Hours_Block::get_instance();

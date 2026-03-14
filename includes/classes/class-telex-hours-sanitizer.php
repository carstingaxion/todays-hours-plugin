<?php
/**
 * Sanitization callbacks for Business Hours Block settings.
 *
 * Handles sanitization of seasons, holidays, and time slots,
 * including backward compatibility with legacy data formats.
 *
 * @package TelexHoursBlock
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Telex_Hours_Sanitizer' ) ) {
	/**
	 * Sanitizes seasons and holidays data for safe storage.
	 *
	 * @since 0.1.0
	 */
	class Telex_Hours_Sanitizer {

		/**
		 * The single instance of this class.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Sanitizer|null
		 */
		private static ?Telex_Hours_Sanitizer $instance = null;

		/**
		 * Retrieves the single instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @return Telex_Hours_Sanitizer The singleton instance.
		 */
		public static function get_instance(): Telex_Hours_Sanitizer {
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
		 * Safely casts a value to string for sanitization.
		 *
		 * @since 0.1.0
		 *
		 * @param mixed $value The value to cast.
		 * @return string The value as a string, or empty string if not scalar.
		 */
		private function to_string( $value ): string {
			if ( is_string( $value ) ) {
				return $value;
			}
			if ( is_scalar( $value ) ) {
				return (string) $value;
			}
			return '';
		}

		/**
		 * Sanitizes the seasons option value.
		 *
		 * @since 0.1.0
		 *
		 * @param mixed $value The raw option value.
		 * @return array<int, array{name: string, beginDate: string, endDate: string, hours: array<string, array<int, array{open: string, close: string}>>}> Sanitized seasons array.
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
					'name'      => sanitize_text_field( $this->to_string( $season['name'] ?? '' ) ),
					'beginDate' => sanitize_text_field( $this->to_string( $season['beginDate'] ?? '' ) ),
					'endDate'   => sanitize_text_field( $this->to_string( $season['endDate'] ?? '' ) ),
					'hours'     => array(),
				);

				$hours = isset( $season['hours'] ) && is_array( $season['hours'] ) ? $season['hours'] : array();

				foreach ( $day_keys as $dk ) {
					$day_data              = isset( $hours[ $dk ] ) ? $hours[ $dk ] : array();
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
		 * @return array<int, array{open: string, close: string}> Sanitized array of slot objects.
		 */
		public function sanitize_slots( $slots ): array {
			if ( ! is_array( $slots ) ) {
				return array(
					array(
						'open'  => '',
						'close' => '',
					),
				);
			}

			// Legacy format: { open: '...', close: '...' } — convert to array of one slot.
			if ( isset( $slots['open'] ) || isset( $slots['close'] ) ) {
				return array(
					array(
						'open'  => sanitize_text_field( $this->to_string( $slots['open'] ?? '' ) ),
						'close' => sanitize_text_field( $this->to_string( $slots['close'] ?? '' ) ),
					),
				);
			}

			$sanitized = array();
			foreach ( $slots as $slot ) {
				if ( ! is_array( $slot ) ) {
					continue;
				}
				$sanitized[] = array(
					'open'  => sanitize_text_field( $this->to_string( $slot['open'] ?? '' ) ),
					'close' => sanitize_text_field( $this->to_string( $slot['close'] ?? '' ) ),
				);
			}

			if ( empty( $sanitized ) ) {
				return array(
					array(
						'open'  => '',
						'close' => '',
					),
				);
			}

			return $sanitized;
		}

		/**
		 * Sanitizes the holidays option value.
		 *
		 * @since 0.1.0
		 *
		 * @param mixed $value The raw option value.
		 * @return array<int, array{name: string, beginDate: string, endDate: string, slots: array<int, array{open: string, close: string}>}> Sanitized holidays array.
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
							'open'  => $this->to_string( $holiday['openTime'] ?? '' ),
							'close' => $this->to_string( $holiday['closeTime'] ?? '' ),
						),
					);
				}

				$clean_slots = array();
				foreach ( $slots_raw as $slot ) {
					if ( ! is_array( $slot ) ) {
						continue;
					}
					$clean_slots[] = array(
						'open'  => sanitize_text_field( $this->to_string( $slot['open'] ?? '' ) ),
						'close' => sanitize_text_field( $this->to_string( $slot['close'] ?? '' ) ),
					);
				}

				$sanitized[] = array(
					'name'      => sanitize_text_field( $this->to_string( $holiday['name'] ?? '' ) ),
					'beginDate' => sanitize_text_field( $this->to_string( $holiday['beginDate'] ?? '' ) ),
					'endDate'   => sanitize_text_field( $this->to_string( $holiday['endDate'] ?? '' ) ),
					'slots'     => $clean_slots,
				);
			}

			return $sanitized;
		}
	}
}

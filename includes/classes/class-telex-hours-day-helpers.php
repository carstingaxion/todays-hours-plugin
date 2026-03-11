<?php
/**
 * Day ordering, labels, and slot normalization helpers.
 *
 * @package TelexHoursBlock
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Telex_Hours_Day_Helpers' ) ) {
	/**
	 * Provides day-related utilities for the renderer.
	 *
	 * @since 0.1.0
	 */
	class Telex_Hours_Day_Helpers {

		/**
		 * The single instance of this class.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Day_Helpers|null
		 */
		private static ?Telex_Hours_Day_Helpers $instance = null;

		/**
		 * All day keys in standard order starting from Sunday.
		 *
		 * @since 0.1.0
		 * @var array<int, string>
		 */
		private array $all_day_keys = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );

		/**
		 * Weekend day keys.
		 *
		 * @since 0.1.0
		 * @var array<int, string>
		 */
		private array $weekend_keys = array( 'sun', 'sat' );

		/**
		 * Retrieves the single instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @return Telex_Hours_Day_Helpers The singleton instance.
		 */
		public static function get_instance(): Telex_Hours_Day_Helpers {
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
		 * Returns all day keys in standard Sunday-first order.
		 *
		 * @since 0.1.0
		 *
		 * @return array<int, string> Array of day key strings.
		 */
		public function get_all_day_keys(): array {
			return $this->all_day_keys;
		}

		/**
		 * Returns weekend day keys.
		 *
		 * @since 0.1.0
		 *
		 * @return array<int, string> Array of weekend day key strings.
		 */
		public function get_weekend_keys(): array {
			return $this->weekend_keys;
		}

		/**
		 * Returns the day keys ordered according to the WordPress start_of_week setting.
		 *
		 * @since 0.1.0
		 *
		 * @return array<int, string> Ordered array of day key strings.
		 */
		public function get_ordered_day_keys(): array {
			$start_of_week = get_option( 'start_of_week', 0 );
			$start_index   = is_numeric( $start_of_week ) ? (int) $start_of_week : 0;

			return array_merge(
				array_slice( $this->all_day_keys, $start_index ),
				array_slice( $this->all_day_keys, 0, $start_index )
			);
		}

		/**
		 * Returns localized day labels using the default textdomain.
		 *
		 * @since 0.1.0
		 *
		 * @return array<string, string> Associative array of day keys to localized day names.
		 */
		public function get_day_labels(): array {
			return array(
				'sun' => __( 'Sunday' ),
				'mon' => __( 'Monday' ),
				'tue' => __( 'Tuesday' ),
				'wed' => __( 'Wednesday' ),
				'thu' => __( 'Thursday' ),
				'fri' => __( 'Friday' ),
				'sat' => __( 'Saturday' ),
			);
		}

		/**
		 * Checks whether a day key is a weekend day.
		 *
		 * @since 0.1.0
		 *
		 * @param string $day_key The day key to check.
		 * @return bool True if the day key is a weekend day.
		 */
		public function is_weekend( string $day_key ): bool {
			return in_array( $day_key, $this->weekend_keys, true );
		}

		/**
		 * Normalizes day data into an array of time slots.
		 *
		 * Handles both the new array-of-slots format and the legacy
		 * single {open, close} object format.
		 *
		 * @since 0.1.0
		 *
		 * @param mixed $day_data Raw day data from season hours.
		 * @return array<int, array{open: string, close: string}> Array of slot arrays with 'open' and 'close' keys.
		 */
		public function normalize_slots( $day_data ): array {
			if ( ! is_array( $day_data ) ) {
				return array();
			}

			// Legacy format: { open: '...', close: '...' }.
			if ( isset( $day_data['open'] ) || isset( $day_data['close'] ) ) {
				$open_val  = isset( $day_data['open'] ) ? $day_data['open'] : '';
				$close_val = isset( $day_data['close'] ) ? $day_data['close'] : '';
				return array(
					array(
						'open'  => is_string( $open_val ) ? $open_val : '',
						'close' => is_string( $close_val ) ? $close_val : '',
					),
				);
			}

			// New format: array of { open, close } objects.
			$slots = array();
			foreach ( $day_data as $slot ) {
				if ( is_array( $slot ) && ( isset( $slot['open'] ) || isset( $slot['close'] ) ) ) {
					$slot_open  = isset( $slot['open'] ) ? $slot['open'] : '';
					$slot_close = isset( $slot['close'] ) ? $slot['close'] : '';
					$slots[]    = array(
						'open'  => is_string( $slot_open ) ? $slot_open : '',
						'close' => is_string( $slot_close ) ? $slot_close : '',
					);
				}
			}

			return $slots;
		}

		/**
		 * Normalizes holiday data into an array of time slots.
		 *
		 * Handles both the new 'slots' format and legacy 'openTime'/'closeTime'.
		 *
		 * @since 0.1.0
		 *
		 * @param array<string, mixed> $holiday Holiday data array.
		 * @return array<int, array{open: string, close: string}> Array of slot arrays with 'open' and 'close' keys.
		 */
		public function normalize_holiday_slots( array $holiday ): array {
			if ( isset( $holiday['slots'] ) && is_array( $holiday['slots'] ) ) {
				$slots = array();
				foreach ( $holiday['slots'] as $slot ) {
					if ( is_array( $slot ) ) {
						$slot_open  = isset( $slot['open'] ) ? $slot['open'] : '';
						$slot_close = isset( $slot['close'] ) ? $slot['close'] : '';
						$slots[]    = array(
							'open'  => is_string( $slot_open ) ? $slot_open : '',
							'close' => is_string( $slot_close ) ? $slot_close : '',
						);
					}
				}
				return $slots;
			}

			// Legacy format.
			$open_raw  = isset( $holiday['openTime'] ) ? $holiday['openTime'] : '';
			$close_raw = isset( $holiday['closeTime'] ) ? $holiday['closeTime'] : '';
			$open      = is_string( $open_raw ) ? $open_raw : '';
			$close     = is_string( $close_raw ) ? $close_raw : '';
			if ( '' !== $open ) {
				return array(
					array(
						'open'  => $open,
						'close' => $close,
					),
				);
			}

			return array();
		}

		/**
		 * Checks whether any slot in the array has a non-empty open time.
		 *
		 * @since 0.1.0
		 *
		 * @param array<int, array{open: string, close: string}> $slots Array of time slot arrays.
		 * @return bool True if at least one slot has a non-empty 'open' value.
		 */
		public function slots_have_open( array $slots ): bool {
			foreach ( $slots as $slot ) {
				if ( ! empty( $slot['open'] ) ) {
					return true;
				}
			}
			return false;
		}
	}
}

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
		 * @var array
		 */
		private array $all_day_keys = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );

		/**
		 * Weekend day keys.
		 *
		 * @since 0.1.0
		 * @var array
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
		 * @return array Array of day key strings.
		 */
		public function get_all_day_keys(): array {
			return $this->all_day_keys;
		}

		/**
		 * Returns weekend day keys.
		 *
		 * @since 0.1.0
		 *
		 * @return array Array of weekend day key strings.
		 */
		public function get_weekend_keys(): array {
			return $this->weekend_keys;
		}

		/**
		 * Returns the day keys ordered according to the WordPress start_of_week setting.
		 *
		 * @since 0.1.0
		 *
		 * @return array Ordered array of day key strings.
		 */
		public function get_ordered_day_keys(): array {
			$start_index = (int) get_option( 'start_of_week', 0 );

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
		 * @return array Associative array of day keys to localized day names.
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
		 * @return array Array of slot arrays with 'open' and 'close' keys.
		 */
		public function normalize_slots( $day_data ): array {
			if ( ! is_array( $day_data ) ) {
				return array();
			}

			// Legacy format: { open: '...', close: '...' }.
			if ( isset( $day_data['open'] ) || isset( $day_data['close'] ) ) {
				return array(
					array(
						'open'  => isset( $day_data['open'] ) ? $day_data['open'] : '',
						'close' => isset( $day_data['close'] ) ? $day_data['close'] : '',
					),
				);
			}

			// New format: array of { open, close } objects.
			$slots = array();
			foreach ( $day_data as $slot ) {
				if ( is_array( $slot ) && ( isset( $slot['open'] ) || isset( $slot['close'] ) ) ) {
					$slots[] = array(
						'open'  => isset( $slot['open'] ) ? $slot['open'] : '',
						'close' => isset( $slot['close'] ) ? $slot['close'] : '',
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
		 * @param array $holiday Holiday data array.
		 * @return array Array of slot arrays with 'open' and 'close' keys.
		 */
		public function normalize_holiday_slots( array $holiday ): array {
			if ( isset( $holiday['slots'] ) && is_array( $holiday['slots'] ) ) {
				$slots = array();
				foreach ( $holiday['slots'] as $slot ) {
					if ( is_array( $slot ) ) {
						$slots[] = array(
							'open'  => isset( $slot['open'] ) ? $slot['open'] : '',
							'close' => isset( $slot['close'] ) ? $slot['close'] : '',
						);
					}
				}
				return $slots;
			}

			// Legacy format.
			$open  = isset( $holiday['openTime'] ) ? $holiday['openTime'] : '';
			$close = isset( $holiday['closeTime'] ) ? $holiday['closeTime'] : '';
			if ( ! empty( $open ) ) {
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
		 * @param array $slots Array of time slot arrays.
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

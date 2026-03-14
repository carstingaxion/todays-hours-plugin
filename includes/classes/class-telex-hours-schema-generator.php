<?php
/**
 * Schema.org JSON-LD structured data generator.
 *
 * Builds OpeningHoursSpecification entries for seasons and holidays.
 *
 * @package TelexHoursBlock
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Telex_Hours_Schema_Generator' ) ) {
	/**
	 * Generates schema.org JSON-LD structured data for business hours.
	 *
	 * @since 0.1.0
	 */
	class Telex_Hours_Schema_Generator {

		/**
		 * The single instance of this class.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Schema_Generator|null
		 */
		private static ?Telex_Hours_Schema_Generator $instance = null;

		/**
		 * Schema.org full DayOfWeek IRIs.
		 *
		 * @since 0.1.0
		 * @var array<string, string>
		 */
		private array $schema_day_of_week = array(
			'sun' => 'https://schema.org/Sunday',
			'mon' => 'https://schema.org/Monday',
			'tue' => 'https://schema.org/Tuesday',
			'wed' => 'https://schema.org/Wednesday',
			'thu' => 'https://schema.org/Thursday',
			'fri' => 'https://schema.org/Friday',
			'sat' => 'https://schema.org/Saturday',
		);

		/**
		 * Retrieves the single instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @return Telex_Hours_Schema_Generator The singleton instance.
		 */
		public static function get_instance(): Telex_Hours_Schema_Generator {
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
		 * Builds an array of OpeningHoursSpecification objects.
		 *
		 * Produces specs for the active season's regular weekly hours, plus
		 * separate specs for each holiday. Per schema.org, a specification
		 * without `opens` indicates the place is closed for that period.
		 *
		 * @since 0.1.0
		 *
		 * @param array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}|null $season Active season data or null.
		 * @param array<int, array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}>         $holidays All holidays.
		 * @param DateTime                                                                                                                               $today         Today's date object.
		 * @param bool                                                                                                                                   $hide_weekends Whether weekend days are hidden.
		 * @param Telex_Hours_Day_Helpers                                                                                                                $day_helpers   Day helpers instance.
		 * @param Telex_Hours_Time_Formatter                                                                                                             $time_fmt     Time formatter instance.
		 * @return array<int, array<string, string>> Array of OpeningHoursSpecification associative arrays.
		 */
		public function build_opening_hours_specs(
			?array $season,
			array $holidays,
			DateTime $today,
			bool $hide_weekends,
			Telex_Hours_Day_Helpers $day_helpers,
			Telex_Hours_Time_Formatter $time_fmt
		): array {
			$specs = $this->build_season_specs( $season, $hide_weekends, $day_helpers, $time_fmt );

			$holiday_specs = $this->build_holiday_specs( $holidays, $today, $day_helpers, $time_fmt );
			foreach ( $holiday_specs as $spec ) {
				$specs[] = $spec;
			}

			return $specs;
		}

		/**
		 * Builds OpeningHoursSpecification entries for a season's regular weekly hours.
		 *
		 * @since 0.1.0
		 *
		 * @param array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}|null $season Season data or null.
		 * @param bool                                                                                                                                   $hide_weekends Whether to skip weekend days.
		 * @param Telex_Hours_Day_Helpers                                                                                                                $day_helpers   Day helpers instance.
		 * @param Telex_Hours_Time_Formatter                                                                                                             $time_fmt      Time formatter instance.
		 * @return array<int, array<string, string>> Array of specification arrays.
		 */
		private function build_season_specs(
			?array $season,
			bool $hide_weekends,
			Telex_Hours_Day_Helpers $day_helpers,
			Telex_Hours_Time_Formatter $time_fmt
		): array {
			if ( null === $season ) {
				return array();
			}

			$season_begin = isset( $season['beginDate'] ) ? $season['beginDate'] : '';
			$season_end   = isset( $season['endDate'] ) ? $season['endDate'] : '';
			$season_hours = isset( $season['hours'] ) ? $season['hours'] : array();
			$all_day_keys = $day_helpers->get_all_day_keys();
			$specs        = array();

			foreach ( $all_day_keys as $dk ) {
				if ( $hide_weekends && $day_helpers->is_weekend( $dk ) ) {
					continue;
				}

				if ( ! isset( $season_hours[ $dk ] ) ) {
					continue;
				}

				$day_specs = $this->build_day_slot_specs(
					$dk,
					$day_helpers->normalize_slots( $season_hours[ $dk ] ),
					$season_begin,
					$season_end,
					$time_fmt
				);

				foreach ( $day_specs as $spec ) {
					$specs[] = $spec;
				}
			}

			return $specs;
		}

		/**
		 * Builds OpeningHoursSpecification entries for a single day's time slots.
		 *
		 * @since 0.1.0
		 *
		 * @param string                                         $day_key      Day key (e.g. 'mon').
		 * @param array<int, array{open: string, close: string}> $slots      Normalized time slots.
		 * @param string                                         $valid_from   Season begin date.
		 * @param string                                         $valid_through Season end date.
		 * @param Telex_Hours_Time_Formatter                     $time_fmt     Time formatter instance.
		 * @return array<int, array<string, string>> Array of specification arrays.
		 */
		private function build_day_slot_specs(
			string $day_key,
			array $slots,
			string $valid_from,
			string $valid_through,
			Telex_Hours_Time_Formatter $time_fmt
		): array {
			$specs = array();

			foreach ( $slots as $slot ) {
				$spec = $this->build_slot_spec( $slot, $time_fmt );
				if ( null === $spec ) {
					continue;
				}

				$spec['dayOfWeek'] = $this->schema_day_of_week[ $day_key ];

				if ( ! empty( $valid_from ) ) {
					$spec['validFrom'] = $valid_from;
				}
				if ( ! empty( $valid_through ) ) {
					$spec['validThrough'] = $valid_through;
				}

				$specs[] = $spec;
			}

			return $specs;
		}

		/**
		 * Builds a single OpeningHoursSpecification from a time slot.
		 *
		 * Returns null if the slot has no valid open/close times.
		 *
		 * @since 0.1.0
		 *
		 * @param array{open: string, close: string} $slot     A time slot.
		 * @param Telex_Hours_Time_Formatter         $time_fmt Time formatter instance.
		 * @return array<string, string>|null A specification array or null.
		 */
		private function build_slot_spec( array $slot, Telex_Hours_Time_Formatter $time_fmt ): ?array {
			$open  = $slot['open'];
			$close = $slot['close'];

			if ( empty( $open ) ) {
				return null;
			}

			$open_24  = $time_fmt->to_24h( $open );
			$close_24 = $time_fmt->to_24h( $close );

			if ( empty( $open_24 ) || empty( $close_24 ) ) {
				return null;
			}

			return array(
				'@type'  => 'OpeningHoursSpecification',
				'opens'  => $open_24,
				'closes' => $close_24,
			);
		}

		/**
		 * Builds OpeningHoursSpecification entries for all holidays.
		 *
		 * @since 0.1.0
		 *
		 * @param array<int, array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}> $holidays All holidays.
		 * @param DateTime                                                                                                                       $today      Today's date object (for yearless date expansion).
		 * @param Telex_Hours_Day_Helpers                                                                                                        $day_helpers Day helpers instance.
		 * @param Telex_Hours_Time_Formatter                                                                                                     $time_fmt   Time formatter instance.
		 * @return array<int, array<string, string>> Array of specification arrays.
		 */
		private function build_holiday_specs(
			array $holidays,
			DateTime $today,
			Telex_Hours_Day_Helpers $day_helpers,
			Telex_Hours_Time_Formatter $time_fmt
		): array {
			$specs        = array();
			$current_year = $today->format( 'Y' );

			foreach ( $holidays as $holiday ) {
				$dates = $this->resolve_holiday_dates( $holiday, $current_year );
				if ( null === $dates ) {
					continue;
				}

				$holiday_specs = $this->build_single_holiday_specs(
					$holiday,
					$dates['begin'],
					$dates['end'],
					$day_helpers,
					$time_fmt
				);

				foreach ( $holiday_specs as $spec ) {
					$specs[] = $spec;
				}
			}

			return $specs;
		}

		/**
		 * Resolves a holiday's begin and end dates, expanding yearless dates with the current year.
		 *
		 * Returns null if the holiday has empty dates.
		 *
		 * @since 0.1.0
		 *
		 * @param array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>} $holiday Holiday data.
		 * @param string                                                                                                             $current_year The current year string (e.g. '2025').
		 * @return array{begin: string, end: string}|null Resolved date pair or null.
		 */
		private function resolve_holiday_dates( array $holiday, string $current_year ): ?array {
			$begin = isset( $holiday['beginDate'] ) ? trim( (string) $holiday['beginDate'] ) : '';
			$end   = isset( $holiday['endDate'] ) ? trim( (string) $holiday['endDate'] ) : '';

			if ( empty( $begin ) || empty( $end ) ) {
				return null;
			}

			if ( strlen( $begin ) <= 5 ) {
				$begin = $current_year . '-' . $begin;
			}
			if ( strlen( $end ) <= 5 ) {
				$end = $current_year . '-' . $end;
			}

			return array(
				'begin' => $begin,
				'end'   => $end,
			);
		}

		/**
		 * Builds OpeningHoursSpecification entries for a single holiday.
		 *
		 * Closed holidays (no open slots) produce a single spec without opens/closes.
		 * Holidays with time slots produce one spec per slot.
		 *
		 * @since 0.1.0
		 *
		 * @param array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>} $holiday Holiday data.
		 * @param string                                                                                                             $begin     Resolved begin date.
		 * @param string                                                                                                             $end       Resolved end date.
		 * @param Telex_Hours_Day_Helpers                                                                                            $day_helpers Day helpers instance.
		 * @param Telex_Hours_Time_Formatter                                                                                         $time_fmt   Time formatter instance.
		 * @return array<int, array<string, string>> Array of specification arrays.
		 */
		private function build_single_holiday_specs(
			array $holiday,
			string $begin,
			string $end,
			Telex_Hours_Day_Helpers $day_helpers,
			Telex_Hours_Time_Formatter $time_fmt
		): array {
			$holiday_slots = $day_helpers->normalize_holiday_slots( $holiday );
			$has_open      = $day_helpers->slots_have_open( $holiday_slots );

			if ( ! $has_open ) {
				return array(
					array(
						'@type'        => 'OpeningHoursSpecification',
						'validFrom'    => $begin,
						'validThrough' => $end,
					),
				);
			}

			return $this->build_holiday_slot_specs( $holiday_slots, $begin, $end, $time_fmt );
		}

		/**
		 * Builds OpeningHoursSpecification entries for a holiday's open time slots.
		 *
		 * @since 0.1.0
		 *
		 * @param array<int, array{open: string, close: string}> $slots    Holiday time slots.
		 * @param string                                         $begin    Resolved begin date.
		 * @param string                                         $end      Resolved end date.
		 * @param Telex_Hours_Time_Formatter                     $time_fmt Time formatter instance.
		 * @return array<int, array<string, string>> Array of specification arrays.
		 */
		private function build_holiday_slot_specs(
			array $slots,
			string $begin,
			string $end,
			Telex_Hours_Time_Formatter $time_fmt
		): array {
			$specs = array();

			foreach ( $slots as $slot ) {
				$spec = $this->build_slot_spec( $slot, $time_fmt );
				if ( null === $spec ) {
					continue;
				}

				$spec['validFrom']    = $begin;
				$spec['validThrough'] = $end;
				$specs[]              = $spec;
			}

			return $specs;
		}

		/**
		 * Builds the complete JSON-LD script tag for schema.org structured data.
		 *
		 * @since 0.1.0
		 *
		 * @param array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}|null $season Active season data or null.
		 * @param array<int, array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}>         $holidays All holidays.
		 * @param DateTime                                                                                                                               $today         Today's date object.
		 * @param bool                                                                                                                                   $hide_weekends Whether weekend days are hidden.
		 * @param Telex_Hours_Day_Helpers                                                                                                                $day_helpers   Day helpers instance.
		 * @param Telex_Hours_Time_Formatter                                                                                                             $time_fmt     Time formatter instance.
		 * @return string JSON-LD script tag HTML, or empty string.
		 */
		public function render_json_ld(
			?array $season,
			array $holidays,
			DateTime $today,
			bool $hide_weekends,
			Telex_Hours_Day_Helpers $day_helpers,
			Telex_Hours_Time_Formatter $time_fmt
		): string {
			$specs = $this->build_opening_hours_specs( $season, $holidays, $today, $hide_weekends, $day_helpers, $time_fmt );
			if ( empty( $specs ) ) {
				return '';
			}

			$ld = array(
				'@context'                  => 'https://schema.org',
				'@type'                     => 'Place',
				'name'                      => get_bloginfo( 'name' ),
				'openingHoursSpecification' => $specs,
			);

			$json = wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
			if ( false === $json ) {
				return '';
			}

			return '<script type="application/ld+json">' . $json . '</script>';
		}
	}
}

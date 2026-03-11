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
			$specs        = array();
			$all_day_keys = $day_helpers->get_all_day_keys();

			// Regular season hours.
			if ( null !== $season ) {
				$season_begin = isset( $season['beginDate'] ) ? $season['beginDate'] : '';
				$season_end   = isset( $season['endDate'] ) ? $season['endDate'] : '';
				// @phpstan-ignore-next-line - hours may be missing or not an array, but the method handles that.
				$season_hours = isset( $season['hours'] ) && is_array( $season['hours'] ) ? $season['hours'] : array();

				foreach ( $all_day_keys as $dk ) {
					if ( $hide_weekends && $day_helpers->is_weekend( $dk ) ) {
						continue;
					}

					if ( ! isset( $season_hours[ $dk ] ) ) {
						continue;
					}

					$slots = $day_helpers->normalize_slots( $season_hours[ $dk ] );

					foreach ( $slots as $slot ) {
						$open  = $slot['open'];
						$close = $slot['close'];
						if ( empty( $open ) ) {
							continue;
						}

						$open_24  = $time_fmt->to_24h( $open );
						$close_24 = $time_fmt->to_24h( $close );
						if ( empty( $open_24 ) || empty( $close_24 ) ) {
							continue;
						}

						$spec = array(
							'@type'     => 'OpeningHoursSpecification',
							'dayOfWeek' => $this->schema_day_of_week[ $dk ],
							'opens'     => $open_24,
							'closes'    => $close_24,
						);

						if ( ! empty( $season_begin ) ) {
							$spec['validFrom'] = $season_begin;
						}
						if ( ! empty( $season_end ) ) {
							$spec['validThrough'] = $season_end;
						}

						$specs[] = $spec;
					}
				}
			}

			// Holiday / exception specs.
			$current_year = $today->format( 'Y' );

			foreach ( $holidays as $holiday ) {
				$begin = isset( $holiday['beginDate'] ) ? trim( (string) $holiday['beginDate'] ) : '';
				$end   = isset( $holiday['endDate'] ) ? trim( (string) $holiday['endDate'] ) : '';
				if ( empty( $begin ) || empty( $end ) ) {
					continue;
				}

				$begin_has_year = ( strlen( $begin ) > 5 );
				$end_has_year   = ( strlen( $end ) > 5 );

				if ( ! $begin_has_year ) {
					$begin = $current_year . '-' . $begin;
				}
				if ( ! $end_has_year ) {
					$end = $current_year . '-' . $end;
				}

				$holiday_slots = $day_helpers->normalize_holiday_slots( $holiday );
				$has_open      = $day_helpers->slots_have_open( $holiday_slots );

				if ( ! $has_open ) {
					// Closed holiday: no opens/closes means the place is closed.
					$specs[] = array(
						'@type'        => 'OpeningHoursSpecification',
						'validFrom'    => $begin,
						'validThrough' => $end,
					);
				} else {
					// Holiday with specific hours.
					foreach ( $holiday_slots as $slot ) {
						$open  = $slot['open'];
						$close = $slot['close'];
						if ( empty( $open ) ) {
							continue;
						}

						$open_24  = $time_fmt->to_24h( $open );
						$close_24 = $time_fmt->to_24h( $close );
						if ( empty( $open_24 ) || empty( $close_24 ) ) {
							continue;
						}

						$specs[] = array(
							'@type'        => 'OpeningHoursSpecification',
							'validFrom'    => $begin,
							'validThrough' => $end,
							'opens'        => $open_24,
							'closes'       => $close_24,
						);
					}
				}
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

<?php
/**
 * Season and holiday finder.
 *
 * Encapsulates the logic for matching a date against seasons and holidays.
 *
 * @package TelexHoursBlock
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Telex_Hours_Season_Finder' ) ) {
	/**
	 * Finds active seasons and holidays for a given date.
	 *
	 * @since 0.1.0
	 */
	class Telex_Hours_Season_Finder {

		/**
		 * The single instance of this class.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Season_Finder|null
		 */
		private static ?Telex_Hours_Season_Finder $instance = null;

		/**
		 * Retrieves the single instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @return Telex_Hours_Season_Finder The singleton instance.
		 */
		public static function get_instance(): Telex_Hours_Season_Finder {
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
		 * Finds the active season for a given date.
		 *
		 * Iterates through the seasons array and returns the first season
		 * whose date range includes the given date.
		 *
		 * @since 0.1.0
		 *
		 * @param array<int, array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}> $seasons Array of season data.
		 * @param DateTime                                                                                                                                      $today The date to check.
		 * @return array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}|null The matching season or null.
		 */
		public function find_season( array $seasons, DateTime $today ): ?array {
			$today_str = $today->format( 'Y-m-d' );
			foreach ( $seasons as $season ) {
				$begin = $season['beginDate'] ?? '';
				$end   = $season['endDate'] ?? '';
				if ( '' === $begin || '' === $end ) {
					continue;
				}
				if ( $today_str >= $begin && $today_str <= $end ) {
					return $season;
				}
			}
			return null;
		}

		/**
		 * Finds the active holiday for a given date.
		 *
		 * Supports both year-specific (YYYY-MM-DD) and recurring (MM-DD) dates.
		 * For recurring holidays that span a year boundary (e.g. 12-20 to 01-05),
		 * uses an OR comparison to handle the wrap-around.
		 *
		 * @since 0.1.0
		 *
		 * @param array<int, array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}> $holidays Array of holiday data.
		 * @param DateTime                                                                                                                       $today The date to check.
		 * @return array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}|null The matching holiday or null.
		 */
		public function find_holiday( array $holidays, DateTime $today ): ?array {
			$today_full = $today->format( 'Y-m-d' );
			$today_md   = $today->format( 'm-d' );

			foreach ( $holidays as $holiday ) {
				$begin = trim( $holiday['beginDate'] ?? '' );
				$end   = trim( $holiday['endDate'] ?? '' );
				if ( '' === $begin || '' === $end ) {
					continue;
				}

				$begin_has_year = ( strlen( $begin ) > 5 );
				$end_has_year   = ( strlen( $end ) > 5 );

				if ( $begin_has_year && $end_has_year ) {
					if ( $today_full >= $begin && $today_full <= $end ) {
						return $holiday;
					}
				} elseif ( ! $begin_has_year && ! $end_has_year ) {
					if ( $begin <= $end ) {
						if ( $today_md >= $begin && $today_md <= $end ) {
							return $holiday;
						}
					} elseif ( $today_md >= $begin || $today_md <= $end ) {
							return $holiday;
					}
				}
			}
			return null;
		}
	}
}

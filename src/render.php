<?php
/**
 * Server-side rendering for the Business Hours Block.
 *
 * Dynamically computes the current day's hours based on seasons and holidays
 * stored as site-wide options, then outputs semantic HTML with schema.org
 * JSON-LD structured data.
 *
 * @package TelexHoursBlock
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content (empty for dynamic blocks).
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Telex_Hours_Block_Renderer' ) ) {
	/**
	 * Renderer class using Singleton pattern.
	 *
	 * Encapsulates all rendering logic for the Business Hours Block, including
	 * season/holiday resolution, time formatting, and HTML output generation.
	 *
	 * @since 0.1.0
	 */
	class Telex_Hours_Block_Renderer {

		/**
		 * The single instance of this class.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Block_Renderer|null
		 */
		private static ?Telex_Hours_Block_Renderer $instance = null;

		/**
		 * All day keys in standard order starting from Sunday.
		 *
		 * @since 0.1.0
		 * @var array
		 */
		private array $all_day_keys = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );

		/**
		 * Schema.org two-letter day abbreviations.
		 *
		 * @since 0.1.0
		 * @var array
		 */
		private array $schema_day_map = array(
			'sun' => 'Su',
			'mon' => 'Mo',
			'tue' => 'Tu',
			'wed' => 'We',
			'thu' => 'Th',
			'fri' => 'Fr',
			'sat' => 'Sa',
		);

		/**
		 * Schema.org full DayOfWeek IRIs.
		 *
		 * @since 0.1.0
		 * @var array
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
		 * Weekend day keys.
		 *
		 * @since 0.1.0
		 * @var array
		 */
		private array $weekend_keys = array( 'sun', 'sat' );

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
		 * @return Telex_Hours_Block_Renderer The singleton instance.
		 */
		public static function get_instance(): Telex_Hours_Block_Renderer {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor. Initializes default seasons.
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
		 * Finds the active season for a given date.
		 *
		 * @since 0.1.0
		 *
		 * @param array    $seasons Array of season data.
		 * @param DateTime $today   The date to check.
		 * @return array|null The matching season or null.
		 */
		public function find_season( array $seasons, DateTime $today ): ?array {
			$today_str = $today->format( 'Y-m-d' );
			foreach ( $seasons as $season ) {
				$begin = isset( $season['beginDate'] ) ? $season['beginDate'] : '';
				$end   = isset( $season['endDate'] ) ? $season['endDate'] : '';
				if ( empty( $begin ) || empty( $end ) ) {
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
		 *
		 * @since 0.1.0
		 *
		 * @param array    $holidays Array of holiday data.
		 * @param DateTime $today    The date to check.
		 * @return array|null The matching holiday or null.
		 */
		public function find_holiday( array $holidays, DateTime $today ): ?array {
			$today_full = $today->format( 'Y-m-d' );
			$today_md   = $today->format( 'm-d' );

			foreach ( $holidays as $holiday ) {
				$begin = isset( $holiday['beginDate'] ) ? trim( $holiday['beginDate'] ) : '';
				$end   = isset( $holiday['endDate'] ) ? trim( $holiday['endDate'] ) : '';
				if ( empty( $begin ) || empty( $end ) ) {
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
					} else {
						if ( $today_md >= $begin || $today_md <= $end ) {
							return $holiday;
						}
					}
				}
			}
			return null;
		}

		/**
		 * Replaces 12:00 AM/PM with Midnight/Noon labels.
		 *
		 * @since 0.1.0
		 *
		 * @param string $time             The time string to process.
		 * @param bool   $friendly_twelves Whether to apply friendly labels.
		 * @return string The processed time string.
		 */
		public function friendly_twelves( string $time, bool $friendly_twelves ): string {
			if ( ! $friendly_twelves || empty( $time ) ) {
				return $time;
			}
			$normalized = strtolower( preg_replace( '/\s+/', '', $time ) );
			if ( '12:00am' === $normalized ) {
				return __( 'Midnight', 'telex-hours-block' );
			}
			if ( '12:00pm' === $normalized ) {
				return __( 'Noon', 'telex-hours-block' );
			}
			return $time;
		}

		/**
		 * Formats a time string using the WordPress time_format setting.
		 *
		 * @since 0.1.0
		 *
		 * @param string $time_str The input time string.
		 * @return string The formatted time string.
		 */
		public function format_time( string $time_str ): string {
			if ( empty( $time_str ) ) {
				return $time_str;
			}

			$timestamp = strtotime( $time_str );
			if ( false === $timestamp ) {
				return $time_str;
			}

			$time_format = get_option( 'time_format', 'g:i a' );
			return date_i18n( $time_format, $timestamp );
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
				return array( array( 'open' => $open, 'close' => $close ) );
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

		/**
		 * Converts a time string to 24-hour HH:MM format for datetime attributes.
		 *
		 * @since 0.1.0
		 *
		 * @param string $time_str The input time string.
		 * @return string Time in HH:MM format, or empty string.
		 */
		public function to_24h( string $time_str ): string {
			if ( empty( $time_str ) ) {
				return '';
			}
			$timestamp = strtotime( $time_str );
			if ( false === $timestamp ) {
				return '';
			}
			return gmdate( 'H:i', $timestamp );
		}

		/**
		 * Renders multiple time slots as HTML with <time> elements, separated by <br>.
		 *
		 * @since 0.1.0
		 *
		 * @param array $slots             Array of slot arrays.
		 * @param bool  $friendly_twelves  Whether to apply friendly labels.
		 * @return string Rendered HTML for all open slots.
		 */
		public function render_slots_html( array $slots, bool $friendly_twelves ): string {
			$parts = array();
			foreach ( $slots as $slot ) {
				$open  = isset( $slot['open'] ) ? $slot['open'] : '';
				$close = isset( $slot['close'] ) ? $slot['close'] : '';

				if ( empty( $open ) ) {
					continue;
				}

				$display_open  = $this->friendly_twelves( $open, $friendly_twelves );
				$display_close = $this->friendly_twelves( $close, $friendly_twelves );

				if ( $display_open === $open ) {
					$display_open = $this->format_time( $open );
				}
				if ( $display_close === $close ) {
					$display_close = $this->format_time( $close );
				}

				$open_24  = $this->to_24h( $open );
				$close_24 = $this->to_24h( $close );

				$html = '<span class="telex-hours-block__slot">';
				$html .= '<time datetime="' . esc_attr( $open_24 ) . '">' . esc_html( $display_open ) . '</time>';
				$html .= '<span class="telex-hours-block__separator">' . "\xE2\x80\x93" . '</span>';
				$html .= '<time datetime="' . esc_attr( $close_24 ) . '">' . esc_html( $display_close ) . '</time>';
				$html .= '</span>';

				$parts[] = $html;
			}

			return implode( '<br>', $parts );
		}

		/**
		 * Builds an array of OpeningHoursSpecification objects for JSON-LD.
		 *
		 * Produces specs for the active season's regular weekly hours, plus
		 * separate specs for each holiday. Per schema.org, a specification
		 * without `opens` indicates the place is closed for that period.
		 *
		 * @since 0.1.0
		 *
		 * @param array|null $season        Active season data or null.
		 * @param array      $holidays      All holidays.
		 * @param DateTime   $today         Today's date object.
		 * @param bool       $hide_weekends Whether weekend days are hidden.
		 * @return array Array of OpeningHoursSpecification associative arrays.
		 */
		public function build_opening_hours_specs( ?array $season, array $holidays, DateTime $today, bool $hide_weekends ): array {
			$specs = array();

			// Regular season hours.
			if ( null !== $season ) {
				$season_begin = isset( $season['beginDate'] ) ? $season['beginDate'] : '';
				$season_end   = isset( $season['endDate'] ) ? $season['endDate'] : '';

				foreach ( $this->all_day_keys as $dk ) {
					if ( $hide_weekends && in_array( $dk, $this->weekend_keys, true ) ) {
						continue;
					}

					if ( ! isset( $season['hours'][ $dk ] ) ) {
						continue;
					}

					$slots = $this->normalize_slots( $season['hours'][ $dk ] );

					foreach ( $slots as $slot ) {
						$open  = isset( $slot['open'] ) ? $slot['open'] : '';
						$close = isset( $slot['close'] ) ? $slot['close'] : '';
						if ( empty( $open ) ) {
							continue;
						}

						$open_24  = $this->to_24h( $open );
						$close_24 = $this->to_24h( $close );
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
			foreach ( $holidays as $holiday ) {
				$begin = isset( $holiday['beginDate'] ) ? trim( $holiday['beginDate'] ) : '';
				$end   = isset( $holiday['endDate'] ) ? trim( $holiday['endDate'] ) : '';
				if ( empty( $begin ) || empty( $end ) ) {
					continue;
				}

				// schema.org validFrom/validThrough require full dates.
				// For recurring (yearless) holidays, expand with the current year.
				$begin_has_year = ( strlen( $begin ) > 5 );
				$end_has_year   = ( strlen( $end ) > 5 );
				$current_year   = $today->format( 'Y' );

				if ( ! $begin_has_year ) {
					$begin = $current_year . '-' . $begin;
				}
				if ( ! $end_has_year ) {
					$end = $current_year . '-' . $end;
				}

				$holiday_slots = $this->normalize_holiday_slots( $holiday );
				$has_open      = $this->slots_have_open( $holiday_slots );

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
						$open  = isset( $slot['open'] ) ? $slot['open'] : '';
						$close = isset( $slot['close'] ) ? $slot['close'] : '';
						if ( empty( $open ) ) {
							continue;
						}

						$open_24  = $this->to_24h( $open );
						$close_24 = $this->to_24h( $close );
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
		 * Uses a Place type with openingHoursSpecification to describe the
		 * business hours in a machine-readable format. Includes both regular
		 * season hours and holiday overrides/closures.
		 *
		 * @since 0.1.0
		 *
		 * @param array|null $season        Active season data or null.
		 * @param array      $holidays      All holidays.
		 * @param DateTime   $today         Today's date object.
		 * @param bool       $hide_weekends Whether weekend days are hidden.
		 * @return string JSON-LD script tag HTML, or empty string.
		 */
		public function render_json_ld( ?array $season, array $holidays, DateTime $today, bool $hide_weekends ): string {
			$specs = $this->build_opening_hours_specs( $season, $holidays, $today, $hide_weekends );
			if ( empty( $specs ) ) {
				return '';
			}

			$ld = array(
				'@context'                   => 'https://schema.org',
				'@type'                      => 'Place',
				'name'                       => get_bloginfo( 'name' ),
				'openingHoursSpecification'  => $specs,
			);

			$json = wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
			if ( false === $json ) {
				return '';
			}

			return '<script type="application/ld+json">' . $json . '</script>';
		}

		/**
		 * Renders the "day" display mode showing only today's hours.
		 *
		 * @since 0.1.0
		 *
		 * @param array|null $season           Active season data or null.
		 * @param array|null $holiday          Active holiday data or null.
		 * @param string     $day_key          Day key (e.g., 'mon').
		 * @param bool       $show_reason      Whether to show the closed reason.
		 * @param bool       $friendly_twelves Whether to use friendly twelve labels.
		 * @return string Rendered HTML.
		 */
		public function render_day( ?array $season, ?array $holiday, string $day_key, bool $show_reason, bool $friendly_twelves ): string {
			$slots = array();

			if ( null !== $holiday ) {
				$slots = $this->normalize_holiday_slots( $holiday );
			} elseif ( null !== $season && isset( $season['hours'][ $day_key ] ) ) {
				$slots = $this->normalize_slots( $season['hours'][ $day_key ] );
			}

			$has_open = $this->slots_have_open( $slots );

			if ( ! $has_open ) {
				$closed_text = __( 'Closed Today', 'telex-hours-block' );
				if ( $show_reason && null !== $holiday && ! empty( $holiday['name'] ) ) {
					/* translators: %s: holiday/exception name */
					$closed_text = sprintf( __( 'Closed for %s', 'telex-hours-block' ), $holiday['name'] );
				}
				return '<p class="telex-hours-block__today-hours telex-hours-block__today-hours--closed">'
					. esc_html( $closed_text )
					. '</p>';
			}

			$html  = '<p class="telex-hours-block__today-hours">';
			$html .= $this->render_slots_html( $slots, $friendly_twelves );
			$html .= '</p>';

			return $html;
		}

		/**
		 * Renders the "week" display mode showing the full weekly schedule.
		 *
		 * Checks holidays per-day so that holidays on any day of the current
		 * week are displayed, not just today's holiday.
		 *
		 * @since 0.1.0
		 *
		 * @param array|null $season           Active season data or null.
		 * @param array      $holidays         All holidays for per-day checking.
		 * @param string     $today_key        Today's day key.
		 * @param bool       $friendly_twelves Whether to use friendly twelve labels.
		 * @param DateTime   $today            Today's date object.
		 * @param bool       $show_reason      Whether to show the closed reason.
		 * @param bool       $hide_weekends    Whether to hide weekend days.
		 * @return string Rendered HTML.
		 */
		public function render_week( ?array $season, array $holidays, string $today_key, bool $friendly_twelves, DateTime $today, bool $show_reason, bool $hide_weekends = false ): string {
			if ( null === $season ) {
				return '<p class="telex-hours-block__message">'
					. esc_html__( 'No active season for today.', 'telex-hours-block' )
					. '</p>';
			}

			$day_keys   = $this->get_ordered_day_keys();
			$day_labels = $this->get_day_labels();

			$today_index = array_search( $today_key, $this->all_day_keys, true );

			$html = '<dl class="telex-hours-block__list">';

			foreach ( $day_keys as $dk ) {
				if ( $hide_weekends && in_array( $dk, $this->weekend_keys, true ) ) {
					continue;
				}

				$is_today = ( $dk === $today_key );

				$dk_index = array_search( $dk, $this->all_day_keys, true );
				$diff     = $dk_index - $today_index;
				$day_date = clone $today;
				$day_date->modify( sprintf( '%+d days', $diff ) );

				$day_holiday = $this->find_holiday( $holidays, $day_date );

				$slots = array();
				if ( null !== $day_holiday ) {
					$slots = $this->normalize_holiday_slots( $day_holiday );
				} elseif ( isset( $season['hours'][ $dk ] ) ) {
					$slots = $this->normalize_slots( $season['hours'][ $dk ] );
				}

				$has_open  = $this->slots_have_open( $slots );
				$is_closed = ! $has_open;

				$dt_classes = array( 'telex-hours-block__day' );
				$dd_classes = array( 'telex-hours-block__hours' );
				if ( $is_today ) {
					$dt_classes[] = 'telex-hours-block__day--today';
					$dd_classes[] = 'telex-hours-block__hours--today';
				}
				if ( $is_closed ) {
					$dd_classes[] = 'telex-hours-block__hours--closed';
				}

				$html .= '<dt class="' . esc_attr( implode( ' ', $dt_classes ) ) . '" data-day="' . esc_attr( $dk ) . '">';
				$html .= esc_html( $day_labels[ $dk ] );
				$html .= '</dt>';

				$html .= '<dd class="' . esc_attr( implode( ' ', $dd_classes ) ) . '" data-day="' . esc_attr( $dk ) . '">';
				if ( $is_closed ) {
					$closed_label = esc_html__( 'Closed', 'telex-hours-block' );
					if ( $show_reason && null !== $day_holiday && ! empty( $day_holiday['name'] ) ) {
						/* translators: %s: holiday/exception name */
						$closed_label = esc_html( sprintf( __( 'Closed for %s', 'telex-hours-block' ), $day_holiday['name'] ) );
					}
					$html .= $closed_label;
				} else {
					$html .= $this->render_slots_html( $slots, $friendly_twelves );
				}
				$html .= '</dd>';
			}

			$html .= '</dl>';

			return $html;
		}

		/**
		 * Renders the complete Business Hours Block output.
		 *
		 * This is the main entry point called by the block rendering system.
		 *
		 * @since 0.1.0
		 *
		 * @param array $attributes Block attributes containing display options.
		 * @return string The rendered HTML output.
		 */
		public function render( array $attributes ): string {
			$display_mode      = isset( $attributes['displayMode'] ) ? $attributes['displayMode'] : 'week';
			$show_todays_date  = ! empty( $attributes['showTodaysDate'] );
			$show_reason       = ! empty( $attributes['showReasonClosed'] );
			$friendly_twelves  = ! empty( $attributes['friendlyTwelves'] );
			$hide_weekends     = ! empty( $attributes['hideWeekends'] );

			$seasons  = get_option( 'telex_hours_seasons', $this->default_seasons );
			$holidays = get_option( 'telex_hours_holidays', array() );

			$timezone_string = wp_timezone_string();
			$timezone        = new DateTimeZone( $timezone_string );
			$now             = new DateTime( 'now', $timezone );
			$today           = new DateTime( $now->format( 'Y-m-d' ), $timezone );

			$day_key = $this->all_day_keys[ (int) $today->format( 'w' ) ];

			$current_holiday = $this->find_holiday( $holidays, $today );
			$current_season  = $this->find_season( $seasons, $today );

			$output = '';

			if ( $show_todays_date ) {
				$output .= '<p class="telex-hours-block__date">';
				$output .= esc_html( wp_date( get_option( 'date_format' ), $today->getTimestamp(), $timezone ) );
				$output .= '</p>';
			}

			if ( 'day' === $display_mode ) {
				if ( $hide_weekends && in_array( $day_key, $this->weekend_keys, true ) ) {
					$output .= '';
				} else {
					$output .= $this->render_day(
						$current_season,
						$current_holiday,
						$day_key,
						$show_reason,
						$friendly_twelves
					);
				}
			} else {
				$output .= $this->render_week(
					$current_season,
					$holidays,
					$day_key,
					$friendly_twelves,
					$today,
					$show_reason,
					$hide_weekends
				);
			}

			$output .= $this->render_json_ld( $current_season, $holidays, $today, $hide_weekends );

			return $output;
		}
	}
}

$renderer        = Telex_Hours_Block_Renderer::get_instance();
$rendered_output = $renderer->render( $attributes );

printf(
	'<div %s>%s</div>',
	get_block_wrapper_attributes( array( 'class' => 'telex-hours-block' ) ),
	$rendered_output // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped within render methods.
);

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
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Block content (empty for dynamic blocks).
 * @var WP_Block             $block      Block instance.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; // @codeCoverageIgnore

// Load helper classes.
require_once TODAYS_HOURS_CORE_PATH . '/includes/classes/class-telex-hours-season-finder.php';
require_once TODAYS_HOURS_CORE_PATH . '/includes/classes/class-telex-hours-time-formatter.php';
require_once TODAYS_HOURS_CORE_PATH . '/includes/classes/class-telex-hours-day-helpers.php';
require_once TODAYS_HOURS_CORE_PATH . '/includes/classes/class-telex-hours-schema-generator.php';

if ( ! class_exists( 'Telex_Hours_Block_Renderer' ) ) {
	/**
	 * Renderer class using Singleton pattern.
	 *
	 * Orchestrates the helper classes to produce the block's HTML output.
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
		 * Season finder instance.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Season_Finder
		 */
		private Telex_Hours_Season_Finder $season_finder;

		/**
		 * Time formatter instance.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Time_Formatter
		 */
		private Telex_Hours_Time_Formatter $time_formatter;

		/**
		 * Day helpers instance.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Day_Helpers
		 */
		private Telex_Hours_Day_Helpers $day_helpers;

		/**
		 * Schema generator instance.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Schema_Generator
		 */
		private Telex_Hours_Schema_Generator $schema_generator;

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
		 * Constructor. Initializes helper instances.
		 *
		 * @since 0.1.0
		 */
		private function __construct() {
			$this->season_finder    = Telex_Hours_Season_Finder::get_instance();
			$this->time_formatter   = Telex_Hours_Time_Formatter::get_instance();
			$this->day_helpers      = Telex_Hours_Day_Helpers::get_instance();
			$this->schema_generator = Telex_Hours_Schema_Generator::get_instance();
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
		 * Renders the "day" display mode showing only today's hours.
		 *
		 * @since 0.1.0
		 *
		 * @param array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}|null $season Active season data or null.
		 * @param array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}|null                $holiday Active holiday data or null.
		 * @param string                                                                                                                                 $day_key          Day key (e.g., 'mon').
		 * @param bool                                                                                                                                   $show_reason      Whether to show the closed reason.
		 * @param bool                                                                                                                                   $friendly_twelves Whether to use friendly twelve labels.
		 * @return string Rendered HTML.
		 */
		public function render_day( ?array $season, ?array $holiday, string $day_key, bool $show_reason, bool $friendly_twelves ): string {
			$slots = array();

			if ( null !== $holiday ) {
				$slots = $this->day_helpers->normalize_holiday_slots( $holiday );
			} elseif ( null !== $season && isset( $season['hours'][ $day_key ] ) ) {
				$slots = $this->day_helpers->normalize_slots( $season['hours'][ $day_key ] );
			}

			$has_open = $this->day_helpers->slots_have_open( $slots );

			if ( ! $has_open ) {
				$closed_text = __( 'Closed', 'telex-hours-block' );
				if ( $show_reason && null !== $holiday && ! empty( $holiday['name'] ) ) {
					$holiday_name = (string) $holiday['name'];
					/* translators: %s: holiday/exception name */
					$closed_text = sprintf( __( 'Closed for %s', 'telex-hours-block' ), $holiday_name );
				}
				return '<p class="telex-hours-block__today-hours telex-hours-block__today-hours--closed">'
					. esc_html( $closed_text )
					. '</p>';
			}

			$html  = '<p class="telex-hours-block__today-hours">';
			$html .= $this->time_formatter->render_slots_html( $slots, $friendly_twelves );
			$html .= '</p>';

			return $html;
		}

		/**
		 * Renders the "week" display mode showing the full weekly schedule.
		 *
		 * Checks holidays per-day so that holidays on any day of the current
		 * week are displayed, not just today's holiday.
		 *
		 * Each <dt> and <dd> is annotated with Interactivity API directives
		 * so the client-side store can re-evaluate "--today" modifier classes
		 * after hydration, which is essential for bypassing HTML page caching.
		 *
		 * @since 0.1.0
		 *
		 * @param array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}|null $season Active season data or null.
		 * @param array<int, array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}>         $holidays All holidays for per-day checking.
		 * @param string                                                                                                                                 $today_key        Today's day key.
		 * @param bool                                                                                                                                   $friendly_twelves Whether to use friendly twelve labels.
		 * @param DateTime                                                                                                                               $today            Today's date object.
		 * @param bool                                                                                                                                   $show_reason      Whether to show the closed reason.
		 * @param bool                                                                                                                                   $hide_weekends    Whether to hide weekend days.
		 * @return string Rendered HTML.
		 */
		public function render_week( ?array $season, array $holidays, string $today_key, bool $friendly_twelves, DateTime $today, bool $show_reason, bool $hide_weekends = false ): string {
			if ( null === $season ) {
				return '<p class="telex-hours-block__message">'
					. esc_html__( 'No active season for today.', 'telex-hours-block' )
					. '</p>';
			}

			$day_keys     = $this->day_helpers->get_ordered_day_keys();
			$today_index  = array_search( $today_key, $day_keys, true );
			$season_hours = $season['hours'] ?? array();

			$html = '<dl class="telex-hours-block__list">';

			foreach ( $day_keys as $dk ) {
				if ( $hide_weekends && $this->day_helpers->is_weekend( $dk ) ) {
					continue;
				}

				$dk_index = array_search( $dk, $day_keys, true );
				$diff     = (int) $dk_index - (int) $today_index;
				$day_date = clone $today;
				$day_date->modify( sprintf( '%+d days', $diff ) );

				$html .= $this->render_week_row(
					$dk,
					$day_date,
					$holidays,
					$season_hours,
					$today_key,
					$friendly_twelves,
					$show_reason
				);
			}

			$html .= '</dl>';

			return $html;
		}

		/**
		 * Renders a single day row (<dt> + <dd>) within the week schedule.
		 *
		 * @since 0.1.0
		 *
		 * @param string                                                                                                                         $dk               Day key (e.g. 'mon').
		 * @param DateTime                                                                                                                       $day_date         Date object for this day.
		 * @param array<int, array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}> $holidays         All holidays.
		 * @param array<string, array<int, array{open: string, close: string}>>                                                                  $season_hours     Season hours keyed by day key.
		 * @param string                                                                                                                         $today_key        Today's day key.
		 * @param bool                                                                                                                           $friendly_twelves Whether to use friendly twelve labels.
		 * @param bool                                                                                                                           $show_reason      Whether to show the closed reason.
		 * @return string Rendered HTML for one <dt>/<dd> pair.
		 */
		private function render_week_row( string $dk, DateTime $day_date, array $holidays, array $season_hours, string $today_key, bool $friendly_twelves, bool $show_reason ): string {
			$day_holiday = $this->season_finder->find_holiday( $holidays, $day_date );
			$slots       = $this->resolve_day_slots( $dk, $day_holiday, $season_hours );
			$is_closed   = ! $this->day_helpers->slots_have_open( $slots );
			$is_today    = ( $dk === $today_key );
			$day_labels  = $this->day_helpers->get_day_labels();
			$day_context = wp_json_encode( array( 'dayKey' => $dk ), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP );

			$dt_classes = 'telex-hours-block__day' . ( $is_today ? ' telex-hours-block__day--today' : '' );
			$dd_classes = 'telex-hours-block__hours'
				. ( $is_closed ? ' telex-hours-block__hours--closed' : '' )
				. ( $is_today ? ' telex-hours-block__hours--today' : '' );

			$html  = '<dt'
				. ' class="' . esc_attr( $dt_classes ) . '"'
				. ' data-day="' . esc_attr( $dk ) . '"'
				. ' data-wp-context=\'' . $day_context . '\''
				. ' data-wp-class--telex-hours-block__day--today="state.isDayToday"'
				. '>';
			$html .= esc_html( $day_labels[ $dk ] );
			$html .= '</dt>';

			$html .= '<dd'
				. ' class="' . esc_attr( $dd_classes ) . '"'
				. ' data-day="' . esc_attr( $dk ) . '"'
				. ' data-wp-context=\'' . $day_context . '\''
				. ' data-wp-class--telex-hours-block__hours--today="state.isHoursToday"'
				. '>';
			$html .= $is_closed
				? $this->render_closed_label( $day_holiday, $show_reason )
				: $this->time_formatter->render_slots_html( $slots, $friendly_twelves );
			$html .= '</dd>';

			return $html;
		}

		/**
		 * Resolves the time slots for a given day, preferring holiday slots over season hours.
		 *
		 * @since 0.1.0
		 *
		 * @param string                                                                                                                  $dk            Day key (e.g. 'mon').
		 * @param array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}|null $day_holiday Active holiday for this day, or null.
		 * @param array<string, array<int, array{open: string, close: string}>>                                                           $season_hours  Season hours keyed by day key.
		 * @return array<int, array{open: string, close: string}> Resolved time slots.
		 */
		private function resolve_day_slots( string $dk, ?array $day_holiday, array $season_hours ): array {
			if ( null !== $day_holiday ) {
				return $this->day_helpers->normalize_holiday_slots( $day_holiday );
			}

			if ( isset( $season_hours[ $dk ] ) ) {
				return $this->day_helpers->normalize_slots( $season_hours[ $dk ] );
			}

			return array();
		}

		/**
		 * Renders the "Closed" or "Closed for <holiday>" label.
		 *
		 * @since 0.1.0
		 *
		 * @param array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}|null $day_holiday  Active holiday or null.
		 * @param bool                                                                                                                    $show_reason  Whether to show the closed reason.
		 * @return string Escaped HTML for the closed label.
		 */
		private function render_closed_label( ?array $day_holiday, bool $show_reason ): string {
			if ( $show_reason && null !== $day_holiday && ! empty( $day_holiday['name'] ) ) {
				$holiday_name = is_string( $day_holiday['name'] ) ? $day_holiday['name'] : '';
				/* translators: %s: holiday/exception name */
				return esc_html( sprintf( __( 'Closed for %s', 'telex-hours-block' ), $holiday_name ) );
			}

			return esc_html__( 'Closed', 'telex-hours-block' );
		}

		/**
		 * Renders the complete Business Hours Block output (inner content only).
		 *
		 * Returns the schedule HTML, date heading, and JSON-LD structured data
		 * without the outer wrapper <div>.
		 *
		 * @since 0.1.0
		 *
		 * @param array<string, mixed> $attributes Block attributes containing display options.
		 * @return string The rendered inner HTML output.
		 */
		public function render( array $attributes ): string {
			$display_mode     = isset( $attributes['displayMode'] ) && is_string( $attributes['displayMode'] ) ? $attributes['displayMode'] : 'week';
			$show_todays_date = ! empty( $attributes['showTodaysDate'] );
			$show_reason      = ! empty( $attributes['showReasonClosed'] );
			$friendly_twelves = ! empty( $attributes['friendlyTwelves'] );
			$hide_weekends    = ! empty( $attributes['hideWeekends'] );

			$raw_seasons  = get_option( 'telex_hours_seasons', array() );
			$raw_holidays = get_option( 'telex_hours_holidays', array() );

			/**
			 * Type safety, the shape is defined by the registered setting schema.
			 *
			 * @var array<int, array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}> $seasons
			 */
			$seasons = is_array( $raw_seasons ) ? $raw_seasons : array();

			/**
			 * Type safety, the shape is defined by the registered setting schema.
			 *
			 * @var array<int, array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}> $holidays
			 */
			$holidays = is_array( $raw_holidays ) ? $raw_holidays : array();

			// If no seasons are configured at all, render nothing.
			if ( empty( $seasons ) ) {
				return '';
			}

			$timezone = new DateTimeZone( wp_timezone_string() );
			$now      = new DateTime( 'now', $timezone );
			$today    = new DateTime( $now->format( 'Y-m-d' ), $timezone );

			$all_day_keys    = $this->day_helpers->get_all_day_keys();
			$day_key         = $all_day_keys[ (int) $today->format( 'w' ) ];
			$current_holiday = $this->season_finder->find_holiday( $holidays, $today );
			$current_season  = $this->season_finder->find_season( $seasons, $today );

			$output  = $this->render_date_heading( $show_todays_date, $today, $timezone );
			$output .= $this->render_schedule( $display_mode, $current_season, $current_holiday, $holidays, $day_key, $today, $show_reason, $friendly_twelves, $hide_weekends );
			$output .= $this->schema_generator->render_json_ld( $current_season, $holidays, $today, $hide_weekends, $this->day_helpers, $this->time_formatter );

			return $output;
		}

		/**
		 * Renders the full block output including the interactive wrapper.
		 *
		 * Wraps the inner content in a <div> with block wrapper attributes,
		 * Interactivity API namespace, and server-computed context for the
		 * current day key. The client re-computes the day key from the
		 * browser's clock so the today highlight stays correct even on
		 * cached pages.
		 *
		 * @since 0.1.0
		 *
		 * @param array<string, mixed> $attributes Block attributes containing display options.
		 * @return string The complete block HTML output with interactive wrapper.
		 */
		public function render_block( array $attributes ): string {
			$inner_html = $this->render( $attributes );

			// If render() returned nothing (no seasons configured), output nothing.
			if ( '' === $inner_html ) {
				return '';
			}

			$all_day_keys   = $this->day_helpers->get_all_day_keys();
			$timezone       = new DateTimeZone( wp_timezone_string() );
			$now            = new DateTime( 'now', $timezone );
			$server_day_key = $all_day_keys[ (int) $now->format( 'w' ) ];

			$context = array(
				'serverDayKey' => $server_day_key,
			);

			return sprintf(
				'<div %s data-wp-interactive="telex/hours-block" data-wp-context=\'%s\'>%s</div>',
				get_block_wrapper_attributes( array( 'class' => 'telex-hours-block' ) ),
				wp_json_encode( $context, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP ),
				$inner_html
			);
		}

		/**
		 * Renders the date heading paragraph if enabled.
		 *
		 * @since 0.1.0
		 *
		 * @param bool         $show     Whether to show the date heading.
		 * @param DateTime     $today    Today's date object.
		 * @param DateTimeZone $timezone The site timezone.
		 * @return string HTML for the date heading, or empty string.
		 */
		private function render_date_heading( bool $show, DateTime $today, DateTimeZone $timezone ): string {
			if ( ! $show ) {
				return '';
			}

			$date_format_raw = get_option( 'date_format', 'F j, Y' );
			$date_format     = is_string( $date_format_raw ) ? $date_format_raw : 'F j, Y';
			$formatted_date  = wp_date( $date_format, $today->getTimestamp(), $timezone );

			return '<p class="telex-hours-block__date">'
				. esc_html( is_string( $formatted_date ) ? $formatted_date : '' )
				. '</p>';
		}

		/**
		 * Dispatches rendering to the appropriate display mode method.
		 *
		 * @since 0.1.0
		 *
		 * @param string                                                                                                                                 $display_mode     Display mode ('day' or 'week').
		 * @param array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}|null $current_season   Active season or null.
		 * @param array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}|null                $current_holiday  Active holiday or null.
		 * @param array<int, array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}>         $holidays         All holidays.
		 * @param string                                                                                                                                 $day_key          Today's day key.
		 * @param DateTime                                                                                                                               $today            Today's date object.
		 * @param bool                                                                                                                                   $show_reason      Whether to show the closed reason.
		 * @param bool                                                                                                                                   $friendly_twelves Whether to use friendly twelve labels.
		 * @param bool                                                                                                                                   $hide_weekends    Whether to hide weekend days.
		 * @return string Rendered HTML for the schedule.
		 */
		private function render_schedule( string $display_mode, ?array $current_season, ?array $current_holiday, array $holidays, string $day_key, DateTime $today, bool $show_reason, bool $friendly_twelves, bool $hide_weekends ): string {
			if ( 'day' === $display_mode ) {
				if ( $hide_weekends && $this->day_helpers->is_weekend( $day_key ) ) {
					return '';
				}
				return $this->render_day( $current_season, $current_holiday, $day_key, $show_reason, $friendly_twelves );
			}

			return $this->render_week( $current_season, $holidays, $day_key, $friendly_twelves, $today, $show_reason, $hide_weekends );
		}
	}
}

$todays_hours_block_renderer = Telex_Hours_Block_Renderer::get_instance();
echo $todays_hours_block_renderer->render_block( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

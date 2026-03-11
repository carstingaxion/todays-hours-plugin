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
		 * Default season data used when no seasons have been configured.
		 *
		 * @since 0.1.0
		 * @var array<int, array{name: string, beginDate: string, endDate: string, hours: array<string, array<int, array{open: string, close: string}>>}>
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
		 * Constructor. Initializes helper instances and default seasons.
		 *
		 * @since 0.1.0
		 */
		private function __construct() {
			$this->season_finder    = Telex_Hours_Season_Finder::get_instance();
			$this->time_formatter   = Telex_Hours_Time_Formatter::get_instance();
			$this->day_helpers      = Telex_Hours_Day_Helpers::get_instance();
			$this->schema_generator = Telex_Hours_Schema_Generator::get_instance();

			$this->default_seasons = array(
				array(
					'name'      => 'Normal Schedule',
					'beginDate' => '2024-01-01',
					'endDate'   => '2026-12-31',
					'hours'     => array(
						'sun' => array(
							array(
								'open'  => '',
								'close' => '',
							),
						),
						'mon' => array(
							array(
								'open'  => '8:00 AM',
								'close' => '11:00 PM',
							),
						),
						'tue' => array(
							array(
								'open'  => '8:00 AM',
								'close' => '11:00 PM',
							),
						),
						'wed' => array(
							array(
								'open'  => '8:00 AM',
								'close' => '11:00 PM',
							),
						),
						'thu' => array(
							array(
								'open'  => '8:00 AM',
								'close' => '11:00 PM',
							),
						),
						'fri' => array(
							array(
								'open'  => '8:00 AM',
								'close' => '9:00 PM',
							),
						),
						'sat' => array(
							array(
								'open'  => '',
								'close' => '',
							),
						),
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
				$closed_text = __( 'Closed Today', 'telex-hours-block' );
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

			$all_day_keys = $this->day_helpers->get_all_day_keys();
			$day_keys     = $this->day_helpers->get_ordered_day_keys();
			$day_labels   = $this->day_helpers->get_day_labels();

			$today_index = array_search( $today_key, $all_day_keys, true );

			$season_hours = $season['hours'] ?? array();

			$html = '<dl class="telex-hours-block__list">';

			foreach ( $day_keys as $dk ) {
				if ( $hide_weekends && $this->day_helpers->is_weekend( $dk ) ) {
					continue;
				}

				$dk_index = array_search( $dk, $all_day_keys, true );
				$diff     = (int) $dk_index - (int) $today_index;
				$day_date = clone $today;
				$day_date->modify( sprintf( '%+d days', $diff ) );

				$day_holiday = $this->season_finder->find_holiday( $holidays, $day_date );

				$slots = array();
				if ( null !== $day_holiday ) {
					$slots = $this->day_helpers->normalize_holiday_slots( $day_holiday );
				} elseif ( isset( $season_hours[ $dk ] ) ) {
					$slots = $this->day_helpers->normalize_slots( $season_hours[ $dk ] );
				}

				$has_open  = $this->day_helpers->slots_have_open( $slots );
				$is_closed = ! $has_open;

				$is_today = ( $dk === $today_key );

				// Base classes without --today modifier (Interactivity API adds it client-side).
				$dt_base_classes = 'telex-hours-block__day';
				$dd_base_classes = 'telex-hours-block__hours';
				if ( $is_closed ) {
					$dd_base_classes .= ' telex-hours-block__hours--closed';
				}

				// Server renders --today classes as initial state; Interactivity API
				// directives override them client-side to handle cached pages.
				$dt_classes = $dt_base_classes;
				$dd_classes = $dd_base_classes;
				if ( $is_today ) {
					$dt_classes .= ' telex-hours-block__day--today';
					$dd_classes .= ' telex-hours-block__hours--today';
				}

				$day_context = wp_json_encode( array( 'dayKey' => $dk ), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP );

				$html .= '<dt'
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
				if ( $is_closed ) {
					$closed_label = esc_html__( 'Closed', 'telex-hours-block' );
					if ( $show_reason && null !== $day_holiday && ! empty( $day_holiday['name'] ) ) {
						$holiday_name = is_string( $day_holiday['name'] ) ? $day_holiday['name'] : '';
						/* translators: %s: holiday/exception name */
						$closed_label = esc_html( sprintf( __( 'Closed for %s', 'telex-hours-block' ), $holiday_name ) );
					}
					$html .= $closed_label;
				} else {
					$html .= $this->time_formatter->render_slots_html( $slots, $friendly_twelves );
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
		 * @param array<string, mixed> $attributes Block attributes containing display options.
		 * @return string The rendered HTML output.
		 */
		public function render( array $attributes ): string {
			$display_mode     = isset( $attributes['displayMode'] ) && is_string( $attributes['displayMode'] ) ? $attributes['displayMode'] : 'week';
			$show_todays_date = ! empty( $attributes['showTodaysDate'] );
			$show_reason      = ! empty( $attributes['showReasonClosed'] );
			$friendly_twelves = ! empty( $attributes['friendlyTwelves'] );
			$hide_weekends    = ! empty( $attributes['hideWeekends'] );

			$raw_seasons  = get_option( 'telex_hours_seasons', $this->default_seasons );
			$raw_holidays = get_option( 'telex_hours_holidays', array() );

			/** @var array<int, array{name?: string, beginDate?: string, endDate?: string, hours?: array<string, array<int, array{open: string, close: string}>>}> $seasons */
			$seasons = is_array( $raw_seasons ) ? $raw_seasons : $this->default_seasons;

			/** @var array<int, array{name?: string, beginDate?: string, endDate?: string, slots?: array<int, array{open: string, close: string}>}> $holidays */
			$holidays = $raw_holidays;

			$timezone_string = wp_timezone_string();
			$timezone        = new DateTimeZone( $timezone_string );
			$now             = new DateTime( 'now', $timezone );
			$today           = new DateTime( $now->format( 'Y-m-d' ), $timezone );

			$all_day_keys = $this->day_helpers->get_all_day_keys();
			$day_key      = $all_day_keys[ (int) $today->format( 'w' ) ];

			$current_holiday = $this->season_finder->find_holiday( $holidays, $today );
			$current_season  = $this->season_finder->find_season( $seasons, $today );

			$output = '';

			if ( $show_todays_date ) {
				$date_format_raw = get_option( 'date_format', 'F j, Y' );
				$date_format     = is_string( $date_format_raw ) ? $date_format_raw : 'F j, Y';
				$formatted_date  = wp_date( $date_format, $today->getTimestamp(), $timezone );

				$output .= '<p class="telex-hours-block__date">';
				$output .= esc_html( is_string( $formatted_date ) ? $formatted_date : '' );
				$output .= '</p>';
			}

			if ( 'day' === $display_mode ) {
				if ( $hide_weekends && $this->day_helpers->is_weekend( $day_key ) ) {
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

			$output .= $this->schema_generator->render_json_ld(
				$current_season,
				$holidays,
				$today,
				$hide_weekends,
				$this->day_helpers,
				$this->time_formatter
			);

			return $output;
		}
	}
}

$renderer        = Telex_Hours_Block_Renderer::get_instance();
$rendered_output = $renderer->render( $attributes );

// Provide initial server state to the Interactivity API store.
// The client re-computes the current day key from the browser's clock,
// so the today highlight stays correct even on cached pages.
$all_day_keys_list = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );
$tz_string         = wp_timezone_string();
$tz_obj            = new DateTimeZone( $tz_string );
$now_obj           = new DateTime( 'now', $tz_obj );
$server_day_key    = $all_day_keys_list[ (int) $now_obj->format( 'w' ) ];

$context = array(
	'serverDayKey' => $server_day_key,
);

printf(
	'<div %s data-wp-interactive="telex/hours-block" data-wp-context=\'%s\'>%s</div>',
	get_block_wrapper_attributes( array( 'class' => 'telex-hours-block' ) ),
	wp_json_encode( $context, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP ),
	$rendered_output // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);

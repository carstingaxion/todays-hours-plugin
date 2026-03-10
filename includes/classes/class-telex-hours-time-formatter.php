<?php
/**
 * Time formatting utilities.
 *
 * Handles time string formatting, friendly twelve labels,
 * 24-hour conversion, and HTML slot rendering.
 *
 * @package TelexHoursBlock
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Telex_Hours_Time_Formatter' ) ) {
	/**
	 * Formats time strings for display and structured data.
	 *
	 * @since 0.1.0
	 */
	class Telex_Hours_Time_Formatter {

		/**
		 * The single instance of this class.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Time_Formatter|null
		 */
		private static ?Telex_Hours_Time_Formatter $instance = null;

		/**
		 * Retrieves the single instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @return Telex_Hours_Time_Formatter The singleton instance.
		 */
		public static function get_instance(): Telex_Hours_Time_Formatter {
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
		 * Each non-empty slot produces a <span> containing two <time> elements
		 * (open and close) with an en-dash separator. Multiple slots are joined
		 * by <br> tags for line-by-line display.
		 *
		 * @since 0.1.0
		 *
		 * @param array $slots             Array of slot arrays with 'open' and 'close' keys.
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

				$html  = '<span class="telex-hours-block__slot">';
				$html .= '<time datetime="' . esc_attr( $open_24 ) . '">' . esc_html( $display_open ) . '</time>';
				$html .= '<span class="telex-hours-block__separator">' . "\xE2\x80\x93" . '</span>';
				$html .= '<time datetime="' . esc_attr( $close_24 ) . '">' . esc_html( $display_close ) . '</time>';
				$html .= '</span>';

				$parts[] = $html;
			}

			return implode( '<br>', $parts );
		}
	}
}

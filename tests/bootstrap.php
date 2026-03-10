<?php
/**
 * PHPUnit bootstrap for Business Hours Block tests.
 *
 * Provides minimal WordPress stubs so the plugin's PHP classes
 * can be tested without a full WordPress installation.
 *
 * @package TelexHoursBlock\Tests
 */

// Prevent "already defined" notices.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/tmp/' );
}

// --- WordPress function stubs ---

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * Stub for sanitize_text_field.
	 *
	 * @param string $str Input string.
	 * @return string Trimmed string.
	 */
	function sanitize_text_field( $str ) {
		return trim( (string) $str );
	}
}

if ( ! function_exists( '__' ) ) {
	/**
	 * Stub for __ (translate).
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Text domain.
	 * @return string The input text unchanged.
	 */
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	/**
	 * Stub for esc_html__.
	 *
	 * @param string $text   Text.
	 * @param string $domain Text domain.
	 * @return string The input text unchanged.
	 */
	function esc_html__( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	/**
	 * Stub for esc_html.
	 *
	 * @param string $text Text.
	 * @return string The input text with basic HTML entity encoding.
	 */
	function esc_html( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	/**
	 * Stub for esc_attr.
	 *
	 * @param string $text Text.
	 * @return string The input text with basic HTML entity encoding.
	 */
	function esc_attr( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	/**
	 * Stub for wp_json_encode.
	 *
	 * @param mixed $data    Data to encode.
	 * @param int   $options JSON encode options.
	 * @param int   $depth   Maximum depth.
	 * @return string|false JSON string or false.
	 */
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'get_bloginfo' ) ) {
	/**
	 * Stub for get_bloginfo.
	 *
	 * @param string $show What to retrieve.
	 * @return string Stub value.
	 */
	function get_bloginfo( $show = '' ) {
		if ( 'name' === $show ) {
			return 'Test Site';
		}
		return '';
	}
}

/**
 * In-memory options store for get_option / update_option stubs.
 *
 * @var array
 */
global $telex_test_options;
$telex_test_options = array(
	'time_format'   => 'g:i a',
	'date_format'   => 'F j, Y',
	'start_of_week' => 0,
);

if ( ! function_exists( 'get_option' ) ) {
	/**
	 * Stub for get_option.
	 *
	 * @param string $option  Option name.
	 * @param mixed  $default Default value.
	 * @return mixed Option value.
	 */
	function get_option( $option, $default = false ) {
		global $telex_test_options;
		if ( array_key_exists( $option, $telex_test_options ) ) {
			return $telex_test_options[ $option ];
		}
		return $default;
	}
}

if ( ! function_exists( 'date_i18n' ) ) {
	/**
	 * Stub for date_i18n.
	 *
	 * @param string $format    PHP date format.
	 * @param int    $timestamp Unix timestamp.
	 * @return string Formatted date.
	 */
	function date_i18n( $format, $timestamp = 0 ) {
		if ( 0 === $timestamp ) {
			$timestamp = time();
		}
		return date( $format, $timestamp );
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	/**
	 * Stub for sanitize_textarea_field.
	 *
	 * @param string $str Input string.
	 * @return string Trimmed string.
	 */
	function sanitize_textarea_field( $str ) {
		return trim( (string) $str );
	}
}

// --- Load plugin classes ---

$plugin_root = dirname( __DIR__ );

require_once $plugin_root . '/includes/classes/class-telex-hours-season-finder.php';
require_once $plugin_root . '/includes/classes/class-telex-hours-time-formatter.php';
require_once $plugin_root . '/includes/classes/class-telex-hours-day-helpers.php';
require_once $plugin_root . '/includes/classes/class-telex-hours-schema-generator.php';
require_once $plugin_root . '/includes/classes/class-telex-hours-sanitizer.php';
require_once $plugin_root . '/includes/classes/class-telex-hours-ical-parser.php';

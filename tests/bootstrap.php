<?php
/**
 * PHPUnit bootstrap for Business Hours Block tests.
 *
 * Loads the WordPress test suite via wp-phpunit and then
 * requires the plugin's PHP classes.
 *
 * @package TelexHoursBlock\Tests
 */

// Composer autoloader.
$composer_autoload = dirname( __DIR__ ) . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
}

// Determine the path to the WordPress test library.
// 1. WP_TESTS_DIR environment variable (set by CI or local config).
// 2. Composer-installed wp-phpunit package.
$wp_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $wp_tests_dir ) {
	$wp_tests_dir = dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit';
}

// Ensure the WordPress test suite exists.
if ( ! file_exists( $wp_tests_dir . '/includes/functions.php' ) ) {
	echo 'Could not find the WordPress test suite.' . PHP_EOL;
	echo 'Looked in: ' . $wp_tests_dir . PHP_EOL;
	echo PHP_EOL;
	echo 'Set WP_TESTS_DIR or run `composer install` to install wp-phpunit.' . PHP_EOL;
	exit( 1 );
}

// Load the wp-tests-config.php.
// 1. WP_TESTS_CONFIG_FILE_PATH environment variable.
// 2. tests/wp-tests-config.php in the plugin root.
$wp_tests_config = getenv( 'WP_TESTS_CONFIG_FILE_PATH' );
if ( ! $wp_tests_config || ! file_exists( $wp_tests_config ) ) {
	$wp_tests_config = __DIR__ . '/wp-tests-config.php';
}

if ( ! file_exists( $wp_tests_config ) ) {
	echo 'Could not find wp-tests-config.php.' . PHP_EOL;
	echo 'Expected at: ' . $wp_tests_config . PHP_EOL;
	echo PHP_EOL;
	echo 'Copy tests/wp-tests-config-sample.php to tests/wp-tests-config.php and update the database credentials.' . PHP_EOL;
	exit( 1 );
}

// Provide the config path to the WordPress test suite.
if ( ! defined( 'WP_TESTS_CONFIG_FILE_PATH' ) ) {
	define( 'WP_TESTS_CONFIG_FILE_PATH', $wp_tests_config );
}

// Give access to tests_add_filter() function.
require_once $wp_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin's PHP classes.
 *
 * Hooked into muplugins_loaded so they're available before tests run,
 * but after WordPress core is loaded.
 */
tests_add_filter(
	'muplugins_loaded',
	function () {
		$plugin_root = dirname( __DIR__ );

		require_once $plugin_root . '/includes/classes/class-telex-hours-season-finder.php';
		require_once $plugin_root . '/includes/classes/class-telex-hours-time-formatter.php';
		require_once $plugin_root . '/includes/classes/class-telex-hours-day-helpers.php';
		require_once $plugin_root . '/includes/classes/class-telex-hours-schema-generator.php';
		require_once $plugin_root . '/includes/classes/class-telex-hours-sanitizer.php';
		require_once $plugin_root . '/includes/classes/class-telex-hours-ical-parser.php';
	} 
);

// Start up the WP testing environment.
require $wp_tests_dir . '/includes/bootstrap.php';

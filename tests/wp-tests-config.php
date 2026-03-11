<?php
/**
 * Sample WordPress test configuration.
 *
 * Copy this file to wp-tests-config.php and update the values
 * to match your local test database.
 *
 * WARNING: The test database is wiped clean on every test run.
 * Use a dedicated test database — never your production database.
 *
 * @package TelexHoursBlock\Tests
 */

// Path to the WordPress codebase (not required when using wp-phpunit).
// If you have a local WordPress installation, point this there.
// Otherwise, wp-phpunit provides its own minimal WordPress installation.
// define( 'ABSPATH', dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit/' );

// Test database settings.
define( 'DB_NAME', 'wordpress_test' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_HOST', 'mysql' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', 'utf8mb4_unicode_520_ci' );

// WordPress test suite table prefix.
$table_prefix = 'wptests_';

// WordPress debug mode.
define( 'WP_DEBUG', true );

// Use the filesystem for test installations.
define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );
define( 'WP_PHP_BINARY', 'php' );

// WordPress locale.
define( 'WPLANG', '' );

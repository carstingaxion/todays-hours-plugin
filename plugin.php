<?php
/**
 * Plugin Name:       Today's Hours
 * Description:       Displays the current day's business hours or a full weekly schedule. Seasons and holidays can be customized. Ideal for institutions with variable yearly schedules.
 * Version:           2.0.2
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Carsten Bach
 * Author URI:        https://carsten-bach.de
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       telex-hours-block
 * Domain Path:       /languages
 *
 * @package TelexHoursBlock
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; // @codeCoverageIgnore

// Constants.
define( 'TODAYS_HOURS_VERSION', current( get_file_data( __FILE__, array( 'Version' ), 'plugin' ) ) );
define( 'TODAYS_HOURS_CORE_PATH', __DIR__ );

// Load helper classes used by the main plugin.
require_once TODAYS_HOURS_CORE_PATH . '/includes/classes/class-telex-hours-sanitizer.php';
require_once TODAYS_HOURS_CORE_PATH . '/includes/classes/class-telex-hours-settings.php';
require_once TODAYS_HOURS_CORE_PATH . '/includes/classes/class-telex-hours-ical-parser.php';

/**
 * Main plugin class using Singleton pattern.
 *
 * Thin orchestrator that loads and wires together dedicated classes
 * for settings registration, sanitization, and iCal import.
 *
 * @since 0.1.0
 */
class Telex_Hours_Block {

	/**
	 * The single instance of this class.
	 *
	 * @since 0.1.0
	 * @var Telex_Hours_Block|null
	 */
	private static ?Telex_Hours_Block $instance = null;

	/**
	 * Sanitizer instance.
	 *
	 * @since 0.1.0
	 * @var Telex_Hours_Sanitizer
	 */
	private Telex_Hours_Sanitizer $sanitizer;

	/**
	 * Settings registration instance.
	 *
	 * @since 0.1.0
	 * @var Telex_Hours_Settings
	 */
	private Telex_Hours_Settings $settings;

	/**
	 * The iCal parser instance.
	 *
	 * @since 0.1.0
	 * @var Telex_Hours_Ical_Parser
	 */
	private Telex_Hours_Ical_Parser $ical_parser;

	/**
	 * Retrieves the single instance of this class.
	 *
	 * @since 0.1.0
	 *
	 * @return Telex_Hours_Block The singleton instance.
	 */
	public static function get_instance(): Telex_Hours_Block {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor. Initializes helper instances and registers hooks.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
		$this->sanitizer   = Telex_Hours_Sanitizer::get_instance();
		$this->settings    = Telex_Hours_Settings::get_instance( $this->sanitizer );
		$this->ical_parser = Telex_Hours_Ical_Parser::get_instance();

		$this->register_hooks();
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
	 * Registers all WordPress hooks used by this plugin.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function register_hooks(): void {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this->settings, 'register' ) );
		add_action( 'rest_api_init', array( $this->ical_parser, 'register_routes' ) );
	}

	/**
	 * Registers the block type using metadata from block.json.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_block(): void {
		register_block_type( __DIR__ . '/build/' );
	}

	/**
	 * Loads the plugin text domain for translations.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'telex-hours-block',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Returns the settings instance.
	 *
	 * Useful for accessing default seasons from other components.
	 *
	 * @since 0.1.0
	 *
	 * @return Telex_Hours_Settings The settings instance.
	 */
	public function get_settings(): Telex_Hours_Settings {
		return $this->settings;
	}

	/**
	 * Returns the sanitizer instance.
	 *
	 * @since 0.1.0
	 *
	 * @return Telex_Hours_Sanitizer The sanitizer instance.
	 */
	public function get_sanitizer(): Telex_Hours_Sanitizer {
		return $this->sanitizer;
	}

	/**
	 * Returns the iCal parser instance.
	 *
	 * @since 0.1.0
	 *
	 * @return Telex_Hours_Ical_Parser The iCal parser instance.
	 */
	public function get_ical_parser(): Telex_Hours_Ical_Parser {
		return $this->ical_parser;
	}
}

Telex_Hours_Block::get_instance();

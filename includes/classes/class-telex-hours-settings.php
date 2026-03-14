<?php
/**
 * Settings registration for Business Hours Block.
 *
 * Registers the site-wide options for seasons and holidays with
 * proper REST API schemas.
 *
 * @package TelexHoursBlock
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Telex_Hours_Settings' ) ) {
	/**
	 * Registers site-level settings for seasons and holidays.
	 *
	 * @since 0.1.0
	 */
	class Telex_Hours_Settings {

		/**
		 * The single instance of this class.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Settings|null
		 */
		private static ?Telex_Hours_Settings $instance = null;

		/**
		 * Sanitizer instance.
		 *
		 * @since 0.1.0
		 * @var Telex_Hours_Sanitizer
		 */
		private Telex_Hours_Sanitizer $sanitizer;

		/**
		 * Retrieves the single instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @param Telex_Hours_Sanitizer $sanitizer Sanitizer instance.
		 * @return Telex_Hours_Settings The singleton instance.
		 */
		public static function get_instance( Telex_Hours_Sanitizer $sanitizer ): Telex_Hours_Settings {
			if ( null === self::$instance ) {
				self::$instance = new self( $sanitizer );
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 0.1.0
		 *
		 * @param Telex_Hours_Sanitizer $sanitizer Sanitizer instance.
		 */
		private function __construct( Telex_Hours_Sanitizer $sanitizer ) {
			$this->sanitizer = $sanitizer;
		}

		/**
		 * Registers the site-wide options for seasons and holidays.
		 *
		 * @since 0.1.0
		 *
		 * @return void
		 */
		public function register(): void {
			$slot_schema = array(
				'type'                 => 'object',
				'properties'           => array(
					'open'  => array( 'type' => 'string' ),
					'close' => array( 'type' => 'string' ),
				),
				'additionalProperties' => false,
			);

			$day_slots_schema = array(
				'type'  => 'array',
				'items' => $slot_schema,
			);

			register_setting(
				'telex_hours_block',
				'telex_hours_seasons',
				array(
					'type'              => 'array',
					'description'       => __( 'Business hours seasons/semesters schedule data.', 'telex-hours-block' ),
					'default'           => array(),
					'show_in_rest'      => array(
						'schema' => array(
							'type'  => 'array',
							'items' => array(
								'type'                 => 'object',
								'properties'           => array(
									'name'      => array( 'type' => 'string' ),
									'beginDate' => array( 'type' => 'string' ),
									'endDate'   => array( 'type' => 'string' ),
									'hours'     => array(
										'type'       => 'object',
										'properties' => array(
											'sun' => $day_slots_schema,
											'mon' => $day_slots_schema,
											'tue' => $day_slots_schema,
											'wed' => $day_slots_schema,
											'thu' => $day_slots_schema,
											'fri' => $day_slots_schema,
											'sat' => $day_slots_schema,
										),
										'additionalProperties' => false,
									),
								),
								'additionalProperties' => false,
							),
						),
					),
					'sanitize_callback' => array( $this->sanitizer, 'sanitize_seasons' ),
				)
			);

			register_setting(
				'telex_hours_block',
				'telex_hours_holidays',
				array(
					'type'              => 'array',
					'description'       => __( 'Business hours holidays/exceptions data.', 'telex-hours-block' ),
					'default'           => array(),
					'show_in_rest'      => array(
						'schema' => array(
							'type'  => 'array',
							'items' => array(
								'type'                 => 'object',
								'properties'           => array(
									'name'      => array( 'type' => 'string' ),
									'beginDate' => array( 'type' => 'string' ),
									'endDate'   => array( 'type' => 'string' ),
									'slots'     => array(
										'type'  => 'array',
										'items' => $slot_schema,
									),
								),
								'additionalProperties' => false,
							),
						),
					),
					'sanitize_callback' => array( $this->sanitizer, 'sanitize_holidays' ),
				)
			);
		}
	}
}

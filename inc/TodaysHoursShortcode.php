<?php


class TodaysHoursShortcode {

	private $the_settings;

	/* the schedule */
	private $the_seasons;
	private $the_holidays;

	private $todays_date;
	private $current_season;      /* NULL if day isn't in any season */
	private $current_holiday;     /* NULL if day isn't in any holiday */

	private $current_season_hour_list;

	private $today_open_time;
	private $today_close_time;

	/* the shortcode output */
	private $shortcode_output_show_todays_date;
	private $shortcode_output_todays_hours;
	private $shortcode_output_current_season_hours_list;

	/* some user options */
	private $show_todays_date;
	private $show_reason_closed;
	private $use_friendly_twelves;
	private $time_format;
	private $timezone_string;

	/**
	 * Shortcode name
	 *
	 * @type   string
	 */
	protected $shortcode = 'TodaysHours';

	private $shortcode_args;



	public function __construct()
	{
		//Hook up to the init action
		add_action( 'plugins_loaded', array( &$this, 'init' ) );
#		add_action( 'init', array( &$this, 'init' ) );

		// Register a UI for the Shortcode with Shortcake-Plugin
		add_action( 'init', array( $this, 'shortcode_ui' ) );
#		add_action( 'plugins_loaded', array( $this, 'shortcode_ui' ) );
	}

	public function init ()
	{

		$this->load_and_set_settings();
		$this->set_current_season();
		$this->set_current_holiday();


		$this->set_todays_hours();

		//
		add_shortcode( $this->shortcode, array( $this, 'shortcode') );



	}


	public function shortcode( $attr )
	{
		// reset for every instance of the shortcode used 
#		$this->shortcode_args = '';

		$this->shortcode_args = wp_parse_args( $attr, array(
			'week_or_day' => 'week',
			'show_todays_date' => ( $this->show_todays_date ) ? 'true' : 'false',
			'show_reason_closed' => ( $this->show_reason_closed ) ? 'true' : 'false',
			'friendly12' => ( $this->use_friendly_twelves ) ? 'true' : 'false',
#			'' => '',
#			'' => '',
		) );

#		$this->shortcode_args = $attr;

		$this->set_current_season_hours_list();

		ob_start(); ?>

		<pre><?php #var_export( $this->shortcode_args ) ?></pre>

		<div class='TodaysHoursShortcode'>
			<?php #echo $this->shortcode_output_show_todays_date ?>
			<?php echo $this->set_show_todays_date_output(); ?>
			<?php echo $this->set_todays_hours_output(); ?>
			<?php echo $this->set_current_season_hours_list_output(); ?>
		</div>
	
		<pre><?php #var_export( $this->current_season ) ?></pre>
		<pre><?php var_export( $this->current_holiday ) ?></pre>
		<pre><?php var_export( $this->the_holidays ) ?></pre>
		<pre><?php #var_export( $this->current_season_hour_list ) ?></pre>
		<pre><?php var_export( $this->shortcode_args ) ?></pre>

		<?php
		return ob_get_clean();

	}


	private function load_and_set_settings()
	{
		// Get Time format per language
		if ( function_exists( 'pll__' ) ) {
			$this->time_format = pll__('g:i a');
		} else {
			$this->time_format = get_option( 'time_format' );
		}

		// Set timezone for coming date functions
		$this->timezone_string = get_option( 'timezone_string' );
		date_default_timezone_set( $this->timezone_string );

		$this->todays_date = new DateTime( date('Y-m-d',time()), new DateTimeZone( $this->timezone_string ) ); /* sets time to 00:00:00 */


		$this->the_settings = get_option('todayshours_settings');
		$this->the_seasons = json_decode($this->the_settings['seasons']);
		$this->the_holidays = json_decode($this->the_settings['holidays']);     

		$this->show_todays_date = $this->the_settings['showdate'];
		$this->show_reason_closed = $this->the_settings['showreason'];
		$this->use_friendly_twelves = $this->the_settings['friendly12'];
	}


	/* date arguments should have times set to 00:00:00 for accurate comparisons */
	private function is_date_in_range($begin_date, $end_date, $test_date)
	{
		if ($test_date == $begin_date) return true;
		if ($test_date == $end_date) return true;
		if ($test_date < $begin_date) return false;
		if ($test_date > $end_date) return false;
		return true;
	}


	private function set_current_season()
	{
		for ($i = 0; $i < count($this->the_seasons); $i++) {
			$season_begin_date = new DateTime($this->the_seasons[$i]->begin_date);
			$season_end_date = new DateTime($this->the_seasons[$i]->end_date);
			if ( $this->is_date_in_range($season_begin_date, $season_end_date, $this->todays_date) ) {
				$this->current_season = $this->the_seasons[$i];
				break;
			}
		}
	}


	private function set_current_holiday()
	{
		for ($i = 0; $i < count($this->the_holidays); $i++) {
			$holiday_begin_date = new DateTime($this->the_holidays[$i]->begin_date);
			$holiday_end_date = new DateTime($this->the_holidays[$i]->end_date);
			if ( $this->is_date_in_range($holiday_begin_date, $holiday_end_date, $this->todays_date) ) {
				$this->current_holiday = $this->the_holidays[$i];
			  break;
			}
		}
	}


	private function set_todays_hours()
	{
		if ($this->current_holiday) {
			$this->today_open_time = $this->current_holiday->open_time;
			$this->today_close_time = $this->current_holiday->close_time;     
		}
		else {
			switch ($this->todays_date->format('D')) {
				case 'Sun':
					$this->today_open_time = $this->current_season->su_open;
					$this->today_close_time = $this->current_season->su_close;
					break;
				case 'Mon':
					$this->today_open_time = $this->current_season->mo_open;
					$this->today_close_time = $this->current_season->mo_close; 
					break;
				case 'Tue':
					$this->today_open_time = $this->current_season->tu_open;
					$this->today_close_time = $this->current_season->tu_close; 
					break;
				case 'Wed':
					$this->today_open_time = $this->current_season->we_open;
					$this->today_close_time = $this->current_season->we_close; 
					break;
				case 'Thu':
					$this->today_open_time = $this->current_season->th_open;
					$this->today_close_time = $this->current_season->th_close; 
					break;
				case 'Fri':
					$this->today_open_time = $this->current_season->fr_open;
					$this->today_close_time = $this->current_season->fr_close; 
					break;
				case 'Sat':
					$this->today_open_time = $this->current_season->sa_open;
					$this->today_close_time = $this->current_season->sa_close; 
					break;
				default:
					break;
			}
		}
	}


	private function set_todays_hours_output()
	{
#error_log( '$this->shortcode_args: ' . var_export( $this->shortcode_args ) );
		if ( $this->shortcode_args['week_or_day'] !== 'day' )
			return;

		// reset // TODO
#		$this->shortcode_output_todays_hours = '';

#wp_die( '<pre>' . var_export( $this->shortcode_args ) . '</pre>' );

		/* check if closed */
		$this->set_closed_and_reason_output();

		if ($this->today_open_time == '') {
			/* option - show reason closed (holiday name) */
			if ($this->current_holiday && $this->shortcode_args['show_reason_closed'] ) {
				$__output = sprintf( _x('Closed for %$1', 'The holiday-name used as \'closed\' reason.', 'todays-hours-plugin' ), $this->current_holiday->name );
			}
			else {
				$__output = __('Closed Today', 'todays-hours-plugin' );
			}
		}
		else {

			/* option - use 'noon' and 'midnight' */
			if ( $this->shortcode_args['friendly12'] === 'true' ) {
				$__today_open = $this->friendly_twelves($this->today_open_time);
				$__today_close = $this->friendly_twelves($this->today_close_time);
			} else {
				// i18n output
#				$__today_open = date_create_from_format( 'g:i a', $this->today_open_time )->format( $this->time_format );
				$__today_open = date_i18n( $this->time_format, strtotime( $this->today_open_time ) );
#				$__today_close = date_create_from_format( 'g:i a', $this->today_close_time )->format( $this->time_format );
				$__today_close = date_i18n( $this->time_format, strtotime( $this->today_close_time ) );
			}

			$__output = $__today_open . ' - ' . $__today_close;

		}

		$__output = '<p class="TH-todays-hours">' . $__output . '</p>';

		return $__output;
	}




	private function set_current_season_hours_list()
	{

			$__days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
			$this->current_season_hour_list = array();

			foreach ( $__days as $__day ) {

				// get two-chars weekday "Mo, Tu, ..."
				$__day_two_char = strtolower( substr( $__day, 0, 2 ) );
				//
				$__day_open  = $this->current_season->{$__day_two_char.'_open'};
				$__day_close = $this->current_season->{$__day_two_char.'_close'};

				if ($this->current_holiday) {
					$__day_open = $this->current_holiday->open_time;
					$__day_close = $this->current_holiday->close_time;
				}

				// setup sub-array per day
				$this->current_season_hour_list[$__day] = array();
				// i18n name of weekday
				$this->current_season_hour_list[$__day][] = date_i18n( 'l', strtotime( $__day ) );

				/* option - use 'noon' and 'midnight' */
				if ( $this->shortcode_args['friendly12'] === 'true' ) {
					$__today_open = $this->friendly_twelves( $__day_open );
					$__today_close = $this->friendly_twelves( $__day_close );
				} else {
					// i18n output
#					$__today_open = date_create_from_format( 'g:i a', $__day_open )->format( $this->time_format );
					$__today_open = date_i18n( $this->time_format, strtotime( $__day_open ) );
#					$__today_close = date_create_from_format( 'g:i a', $__day_close )->format( $this->time_format );
					$__today_close = date_i18n( $this->time_format, strtotime( $__day_close ) );
				}

				// check for the existence of a time value
				if ( $__day_open != '' ) 
				{
					// date object of opening
					$this->current_season_hour_list[$__day][] = 
						$__today_open;
					// date object of closing
					$this->current_season_hour_list[$__day][] = 
						$__today_close;

					// schema.org content-string
					$this->current_season_hour_list[$__day][] = 
						// get two-chars weekday "Mo, Tu, ..."
						ucfirst( $__day_two_char ) . ' ' .
						// get open time range in 24hrs format
						date_create_from_format( 'g:i a', $__day_open )->format('G:i') .'-' .
						date_create_from_format( 'g:i a', $__day_close )->format('G:i');

				}

				
			}

	}



	private function set_current_season_hours_list_output()
	{
		if ( $this->shortcode_args['week_or_day'] !== 'week' )
			return;

		// reset // TODO
#		$this->shortcode_output_current_season_hours_list = '';

#		$__output = '';

		/* check if closed */
		$__output = $this->set_closed_and_reason_output();

/*
		if ($this->today_open_time == '') {
			# option - show reason closed (holiday name)
			if ($this->current_holiday && $this->show_reason_closed) {
				$__output = sprintf( _x('Closed for %$1', 'The holiday-name used as \'closed\' reason.', 'todays-hours-plugin' ), $this->current_holiday->name );
			}
			else {
				$__output = __('Closed Today', 'todays-hours-plugin' );
			}

		}
		else {
*/
			$__output .= '<ol class="TH-opening-hours-list">';

			foreach ($this->current_season_hour_list as $__day => $__data) {

				// reset
				$__output_el = '';
				$__classes = array();

				// markup todays weekday
#				if( date('l') == $__data[0] )
				if( date_i18n( 'l', strtotime( $__day ) ) == date_i18n( 'l' ) )
					$__classes[] = 'TH-today';

				if( count( $__data ) == 1 )
				{

					$__classes[] = 'TH-closed';
					$__output_el .= 	'<data>' .
										'<span class="TH-weekdays">'.
										$__data[0] .
										'</span>' .
										'<span class="TH-hours">' .
											__( 'Closed', 'todays-hours-plugin' ) .
										'</span>' .
									'</data>';
				}
				else
				{

					$__classes[] = 'TH-open';
					$__output_el .= 	'<data itemprop="openingHours" value="' . $__data[3] . '">' .
										'<span class="TH-weekdays">'.
										$__data[0] .
										'</span>' .
										'<span class="TH-hours">' .
											'<span class="TH-hour TH-hour-opening">'.
												$__data[1] .
											'</span>' .
											'<span class="TH-hyphen"> - </span>' .
											'<span class="TH-hour TH-hour-closing">'.
												$__data[2] .
#											_x( '', 'german UHR behind opening time', 'todays-hours-plugin' ) .
											'</span>' .
										'</span>' .
									'</data>';
				}
				$__output .= '<li class="' . join( ' ', $__classes ). '">'.
								$__output_el . '</li>';

			}

			$__output .= '</ol>';



#		} // if open today

		return $__output;
	}


	private function set_show_todays_date_output()
	{
		if ( $this->shortcode_args['show_todays_date'] !== 'true' )
			return;

		// i18n todays date
		$__output = date_i18n( 
				_x('l F j, Y','today\'s date format in shortcode output', 'todays-hours-plugin' ),
				$this->todays_date->format( 'U' )
				);
		// todays date markup
		$__output = '<p class="TH-todays-date">' . $__output . '</p>';

		return $__output;

	}





	private function set_closed_and_reason_output()
	{
		// check if closed

		// Check if we want to show the closed-reason
		if ( $this->shortcode_args['show_reason_closed'] !== 'true' )
			return;

		$__reasons = array();
		foreach ($this->current_holiday as $__holiday) {

			$__validFrom_content = '2014-01-01';
			$__validFrom_text = '1st January 2014';
			$__validThrough_content = '2014-01-01';
			$__validThrough_text = '1st January 2014'; // not used
			$__opens_content = '12:00';
			$__opens_text = 'Noon';
			$__closes_content = '14:00';
			$__closes_text = '2014-01-01';

			$__reasons[] = '<li itemprop="openingHoursSpecification" itemscope itemtype="http://schema.org/OpeningHoursSpecification">' .
				'<span itemprop="validFrom" content="' . $__validFrom_content . '">' . $__validThrough_content . '</span>' .
				'<span itemprop="validThrough" content="' . $__validThrough_content . '"></span>:' .
				'<span itemprop="opens" content="' . $__opens_content . '">' . $__opens_text . '</span>-<span itemprop="closes" content="' . $__closes_content . '">' . $__closes_text . '</span></li>';
			}
/**/
		// add closed reasons as list elements
		$__output = '<ol class="TH-closed-reasons">' . join( '', $__reasons ) . '</ol>';
		return $__output;

	}


	/**
	 * Register a UI for the Shortcode 
	 * 
	 * Using "Shortcake"-Plugin.
	 * Pass the shortcode tag (string)
	 * and an array or args.
	 */
	public function shortcode_ui ( ) {

		if ( function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			shortcode_ui_register_for_shortcode(
				$this->shortcode,
				array(

					// Display label. String. Required.
					'label' => _x( 'Todays Hours', 'Label of the shortcode UI', 'todays-hours-plugin' ),

					// Icon/image for shortcode. Optional. src or dashicons-$icon. Defaults to carrot.
					'listItemImage' => 'dashicons-clock',

					// Available shortcode attributes and default values. Required. Array.
					// Attribute model expects 'attr', 'type' and 'label'
					// Supported field types: text, checkbox, textarea, radio, select, email, url, number, and date.
					'attrs' => array(
						array(
							'label'   => __( 'Week or day?', 'todays-hours-plugin' ),
							'description'   => __( 'Show opening hours of current season or only todays\' hours', 'todays-hours-plugin' ),
							'attr'    => 'week_or_day',
							'type'    => 'radio',
							'options' => array(
								'week' => __( 'Show hours of full season', 'todays-hours-plugin' ),
								'day' => __( 'Show only todays\' hours', 'todays-hours-plugin' )
							),
							'value' => 'week'
						),
						array(
							'label' => __( 'Show today\'s date before the hours', 'todays-hours-plugin' ),
							'attr'  => 'show_todays_date',
							'type'  => 'radio',
							'options'  => array( 'true' => __('Yes'), 'false' => __('No') ),
							'value' => ( $this->show_todays_date ) ? 'true' : 'false' // overwrites user options
						),
						array(
							'label' => __( 'Show reason that day is closed (uses Holiday \'Name\' field)', 'todays-hours-plugin' ),
							'attr'  => 'show_reason_closed',
							'type'  => 'radio',
							'options'  => array( 'true' => __('Yes'), 'false' => __('No') ),
							'value' => ( $this->show_reason_closed ) ? 'true' : 'false' // overwrites user options
						),
						array(
							'label' => __( 'Use \'Midnight\' and \'Noon\' in place of 12:00am and 12:00pm', 'todays-hours-plugin' ),
							'attr'  => 'friendly12',
							'type'  => 'radio',
							'options'  => array( 'true' => __('Yes'), 'false' => __('No') ),
							'value' => ( $this->use_friendly_twelves ) ? 'true' : 'false' // overwrites user options
						),
						array(
							'label' => __( 'Show season-title as headline', 'todays-hours-plugin' ),
							'attr'  => '',
							'type'  => 'checkbox',
						),

					),
				)
			);
		}
	}



	private function friendly_twelves($the_time)
	{
		if (strtotime($the_time) == strtotime('midnight') ) {
			$the_time = __('Midnight', 'todays-hours-plugin' );
		}
		else if (strtotime($the_time) == strtotime('noon') ) {
			$the_time = __('Noon', 'todays-hours-plugin' );
		}
		return $the_time;
	}


} /* END TodaysHoursShortcode class */

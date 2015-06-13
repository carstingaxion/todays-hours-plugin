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
	private $shortcode_output_todays_hours;
	private $shortcode_output_current_season_hours_list;

	/* some user options */
	private $show_todays_date;
	private $show_reason_closed;
	private $use_friendly_twelves;
	private $time_format;
	private $timezone_string;


	public function __construct()
	{
		//Hook up to the init action
		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}

	public function init ()
	{
		$this->todays_date = new DateTime( date('Y-m-d',time()), new DateTimeZone(get_option('timezone_string')) ); /* sets time to 00:00:00 */
		$this->load_and_set_settings();
		$this->set_current_season();
		$this->set_current_holiday();

		$this->set_current_season_hours_list();
		$this->set_current_season_hours_list_output();

		$this->set_todays_hours();
		$this->set_todays_hours_output();

		add_shortcode( 'TodaysHours', array( $this, 'shortcode') );
	}


	public function shortcode( $attr )
	{

		$attr = wp_parse_args( $attr, array(
			'show_all' => false,
		) );

		ob_start(); ?>

		<div class='TodaysHoursShortcode'>
			<?php #echo $this->shortcode_output_todays_hours ?>
			<?php echo $this->shortcode_output_current_season_hours_list ?>
		</div>
	
		<pre><?php #var_export( $this->current_season ) ?></pre>
		<pre><?php var_export( $this->current_holiday ) ?></pre>
		<pre><?php #var_export( $this->current_season_hour_list ) ?></pre>

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
		 /* option - use 'noon' and 'midnight' */
		if ($this->use_friendly_twelves) {
			$this->today_open_time = $this->friendly_twelves($this->today_open_time);
			$this->today_close_time = $this->friendly_twelves($this->today_close_time);
		}
		
		/* check if closed */
		if ($this->today_open_time == '') {
			/* option - show reason closed (holiday name) */
			if ($this->current_holiday && $this->show_reason_closed) {
				$the_text = sprintf( _x('Closed for %$1', 'The holiday-name used as \'closed\' reason.', 'todays-hours-plugin' ), $this->current_holiday->name );
			}
			else {
				$the_text = __('Closed Today', 'todays-hours-plugin' );
			}
		}
		else {

			// i18n output
			$_i18n_today_open = date_create_from_format( 'g:i a', $this->today_open_time )->format( $this->time_format );
			$_i18n_today_close = date_create_from_format( 'g:i a', $this->today_close_time )->format( $this->time_format );


			$the_text = $_i18n_today_open . ' - ' . $_i18n_today_close;

		}

		/* option - show today's date */
/*
		if ($this->show_todays_date) {
			//$the_text = date('l F j, Y') . '<br>' . $the_text;
			$the_text = $this->todays_date->format( _x('l F j, Y','today\'s date format in widget output', 'todays-hours-plugin' ) ) . '<br>' . $the_text;
		}
*/
		$this->shortcode_output_todays_hours = $the_text;
	}




	private function set_current_season_hours_list()
	{

		if ($this->current_holiday) {
			$this->today_open_time = $this->current_holiday->open_time;
			$this->today_close_time = $this->current_holiday->close_time;     
		}
		else {

			$__days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
			$this->current_season_hour_list = array();

			foreach ( $__days as $__day ) {

				// get two-chars weekday "Mo, Tu, ..."
				$__day_two_char = strtolower( substr( $__day, 0, 2 ) );
				//
				$__day_open  = $this->current_season->{$__day_two_char.'_open'};
				$__day_close = $this->current_season->{$__day_two_char.'_close'};



				$this->current_season_hour_list[$__day] = array();
				// i18n name of weekday
				$this->current_season_hour_list[$__day][] = date_i18n( 'l', strtotime( $__day ) );

				// check for the existence of a time value
				if ( $__day_open != '' ) 
				{
					// date object of opening
					$this->current_season_hour_list[$__day][] = 
						date_create_from_format( 'g:i a', $__day_open )->format( $this->time_format );
					// date object of closing
					$this->current_season_hour_list[$__day][] = 
						date_create_from_format( 'g:i a', $__day_close )->format( $this->time_format );

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


	}



	private function set_current_season_hours_list_output()
	{
		$__output = '';

		/* option - use 'noon' and 'midnight' */
		if ($this->use_friendly_twelves) {
			$this->today_open_time = $this->friendly_twelves($this->today_open_time);
			$this->today_close_time = $this->friendly_twelves($this->today_close_time);
		}
		
		/* check if closed */
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
			$__output = '<ol class="TH-opening-hours-list">';

			foreach ($this->current_season_hour_list as $__day => $__data) {

				// reset
				$__output_el = '';
				$__classes = array();

				// markup todays weekday
				if( date('l') == $__data[0] )
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
											$__data[1] .
											' - ' .
											$__data[2] .
											_x( '', 'german UHR behind opening time', 'todays-hours-plugin' ) .
										'</span>' .
									'</data>';
				}
				$__output .= '<li class="' . join( ' ', $__classes ). '">'.
								$__output_el . '</li>';

			}

			$__output .= '</ol>';



#		} // if open today



		/* option - show today's date */
/*
		if ($this->show_todays_date) {
			//$the_text = date('l F j, Y') . '<br>' . $the_text;
			$the_text = $this->todays_date->format( _x('l F j, Y','today\'s date format in widget output', 'todays-hours-plugin' ) ) . '<br>' . $the_text;
		}
*/
		$this->shortcode_output_current_season_hours_list = $__output;
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

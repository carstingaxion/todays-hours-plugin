<?php
/**
* Contains the Widget class
* @author David Baker
* @copyright 2014-2015 Milligan College
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU Public License v2
* @since 1.0
*/

namespace PHWelshimer\TodaysHours;

/**
* Widget class
* Handles registering and display of widget
* @since 1.0
*/
class Widget extends \WP_Widget {
	private $the_settings;

	/* the schedule */
	private $the_seasons;
	private $the_holidays;
	
	private $todays_date;
	private $current_season;      /* NULL if day isn't in any season */
	private $current_holiday;     /* NULL if day isn't in any holiday */
	
	private $today_open_time;
	private $today_close_time;
	
	private $widget_heading;
	private $widget_text;
	
	/* some user options */
	private $show_todays_date;
	private $show_reason_closed;
	private $use_friendly_twelves;
	private $multiple_schedules;
	private $schedules;
	private $show_link;
	private $hours_link;
	
	function __construct() {
		parent::__construct(
					'todays_hours_widget',
					_x('Todays Hours', 'Admin Title', 'todays-hours-plugin' ),
					array(
						'description' => _x('Displays Todays Business Hours', 'Admin Widget Description', 'todays-hours-plugin' ),
					)
		);
	}

	public function form( $instance ) {}

	public function update( $new_instance, $old_instance ) {}
	
	public function widget( $args, $instance ) {
		$this->todays_date = new \DateTime( date('Y-m-d',time()), new \DateTimeZone(get_option('timezone_string')) ); /* sets time to 00:00:00 */
		$this->load_and_set_settings();

		if ($this->multiple_schedules) {
			foreach ($this->schedules as $schedule) { 
				$schedule = trim($schedule);
				$this->set_current_season($schedule);
				$this->set_current_holiday($schedule);
				$this->set_todays_hours();	
				$this->set_widget_text($schedule); 
			}
		}
		else {
			$this->set_current_season();
			$this->set_current_holiday();
			$this->set_todays_hours();
			$this->set_widget_text();	
		}

		$title = apply_filters( 'widget_title', $this->widget_heading );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo $this->widget_text;
		
		if ($this->show_link) { 
			echo "<div class='todays-hours-link'><a href='" . $this->hours_link . "'>" . __('View all business hours', 'todays-hours-plugin') . "</a></div>";
		}

		echo $args['after_widget'];
	}
	

	private function load_and_set_settings() {
		$this->the_settings = get_option('todayshours_settings');
		$this->the_seasons = json_decode($this->the_settings['seasons']);
		$this->the_holidays = json_decode($this->the_settings['holidays']);     

		$this->widget_heading = $this->the_settings['widgettext'];
		$this->show_todays_date = $this->the_settings['showdate'];
		$this->show_reason_closed = $this->the_settings['showreason'];
		$this->use_friendly_twelves = $this->the_settings['friendly12'];
		$this->multiple_schedules = $this->the_settings['multisched'];
		$this->schedules = str_getcsv($this->the_settings['schedules']);
		$this->show_link = $this->the_settings['showlink'];
		$this->hours_link = $this->the_settings['hourslink'];
	}

	
	/* date arguments should have times set to 00:00:00 for accurate comparisons */
	private function is_date_in_range($begin_date, $end_date, $test_date) {
		if ($test_date == $begin_date) return true;
		if ($test_date == $end_date) return true;
		if ($test_date < $begin_date) return false;
		if ($test_date > $end_date) return false;
		return true;
	}

	
	private function set_current_season($schedule = '') {
		for ($i = 0; $i < count($this->the_seasons); $i++) {
			$season_begin_date = new \DateTime($this->the_seasons[$i]->begin_date);
			$season_end_date = new \DateTime($this->the_seasons[$i]->end_date);
			if ( $this->is_date_in_range($season_begin_date, $season_end_date, $this->todays_date) 
				 && $this->the_seasons[$i]->schedule == $schedule ) { 
					$this->current_season = $this->the_seasons[$i];
					break;
			}
			else {
				$this->current_season = '';
			}
		}
	}

	
	private function set_current_holiday($schedule = '') {
		for ($i = 0; $i < count($this->the_holidays); $i++) {
			$holiday_begin_date = new \DateTime($this->the_holidays[$i]->begin_date);
			$holiday_end_date = new \DateTime($this->the_holidays[$i]->end_date);
			if ( $this->is_date_in_range($holiday_begin_date, $holiday_end_date, $this->todays_date) 
				 && $this->the_holidays[$i]->schedule == $schedule ) {
					$this->current_holiday = $this->the_holidays[$i];
			  		break;
			}
			else {
				$this->current_holiday = '';
			}
		}
	}
  
	
	private function set_todays_hours() {
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

	
	private function set_widget_text($schedule = '') {
		if ($this->use_friendly_twelves) {
			$this->today_open_time = $this->friendly_twelves($this->today_open_time);
			$this->today_close_time = $this->friendly_twelves($this->today_close_time);
		}
	
		if ($this->multiple_schedules) {
			$the_text = $this->widget_text . "<div class='schedname'>{$schedule}</div>";
		}	
		
		if ($this->today_open_time == '') { 
			if ($this->current_holiday && $this->show_reason_closed) { 
				$the_text .= sprintf( _x('Closed for %s', 'The holiday-name used as \'closed\' reason.', 'todays-hours-plugin' ), $this->current_holiday->name );
			}
			else {
				$the_text .= '<div>' . __('Closed Today', 'todays-hours-plugin' ) . '</div>';
			}
		}
		else {
			$the_text .= '<div>' . $this->today_open_time . ' - ' . $this->today_close_time . '</div>';  
		}
		
		if ($this->show_todays_date) {
			$date_text = "<div class='todaysdate'>" . $this->todays_date->format( _x('l F j, Y','today\'s date format in widget output', 'todays-hours-plugin' ) ) . "</div>";
			if (strpos($the_text, $date_text) === false) {
				$the_text = $date_text . $the_text;	
			}
		}

		$this->widget_text = $the_text;
	}
	
	
	private function friendly_twelves($the_time) {
		if (strtotime($the_time) == strtotime('midnight') ) {
			$the_time = __('Midnight', 'todays-hours-plugin' );
		}
		else if (strtotime($the_time) == strtotime('noon') ) {
			$the_time = __('Noon', 'todays-hours-plugin' );
		}
		return $the_time;
	}
	
} 

add_action('widgets_init', create_function('', 'return register_widget("PHWelshimer\TodaysHours\Widget");'));

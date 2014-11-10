<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
   Today's Hours Plugin - Widget
   David Baker, Milligan College 2014
*/

class TodaysHoursWidget extends WP_Widget {
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
   
   function __construct() {
      parent::__construct('todays_hours_widget',
                          'Todays Hours Widget',
                          array('classname' => 'TodaysHoursWidget',
                                'description' => 'Displays Todays Business Hours')
      );
      
      $this->todays_date = new DateTime( date('Y-m-d',time()) ); /* sets time to 00:00:00 */
      $this->load_and_set_settings();
      $this->set_current_season();
      $this->set_current_holiday();
      $this->set_todays_hours();
      $this->set_widget_text();
   }

   public function form( $instance ) {}

	public function update( $new_instance, $old_instance ) {}
   
   public function widget( $args, $instance ) {
		echo $args['before_widget'];
      echo "<h1 class='clock-icon'>" . $this->widget_heading . "</h1>";
      echo "<div id='todaysHours' class='textwidget'>";
         echo "<p>" . $this->widget_text . "</p>";
         echo "<a href='http://library.milligan.edu/faq/#hours'>View all business hours</a>";
      echo "</div>";
      echo $args['after_widget'];
	}
   

   private function load_and_set_settings() {
      $this->the_settings = get_option('todayshours_settings');
      $this->the_seasons = json_decode($this->the_settings['seasons']);
      $this->the_holidays = json_decode($this->the_settings['holidays']);     

      $this->widget_heading = get_option('todayshours_settings')['widgettext'];
      $this->show_todays_date = $this->the_settings['showdate'];
      $this->show_reason_closed = $this->the_settings['showreason'];
      $this->use_friendly_twelves = $this->the_settings['friendly12'];
   }

   
   /* date arguments should have times set to 00:00:00 for accurate comparisons */
   private function is_date_in_range($begin_date, $end_date, $test_date) {
      if ($test_date == $begin_date) return true;
      if ($test_date == $end_date) return true;
      if ($test_date < $begin_date) return false;
      if ($test_date > $end_date) return false;
      return true;
   }

   
   private function set_current_season() {
      for ($i = 0; $i < count($this->the_seasons); $i++) {
         $season_begin_date = new DateTime($this->the_seasons[$i]->begin_date);
         $season_end_date = new DateTime($this->the_seasons[$i]->end_date);
         if ( $this->is_date_in_range($season_begin_date, $season_end_date, $this->todays_date) ) {
            $this->current_season = $this->the_seasons[$i];
            break;
         }
      }
   }

   
   private function set_current_holiday() {
      for ($i = 0; $i < count($this->the_holidays); $i++) {
         $holiday_begin_date = new DateTime($this->the_holidays[$i]->begin_date);
         $holiday_end_date = new DateTime($this->the_holidays[$i]->end_date);
         if ( $this->is_date_in_range($holiday_begin_date, $holiday_end_date, $this->todays_date) ) {
            $this->current_holiday = $this->the_holidays[$i];
           break;
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

   
   private function set_widget_text() {
       /* option - use 'noon' and 'midnight' */
      if ($this->use_friendly_twelves) {
         $this->today_open_time = $this->friendly_twelves($this->today_open_time);
         $this->today_close_time = $this->friendly_twelves($this->today_close_time);
      }
      
      /* check if closed */
      if ($this->today_open_time == '') {
         /* option - show reason closed (holiday name) */
         if ($this->current_holiday && $this->show_reason_closed) {
            $the_text = 'Closed for ' . $this->current_holiday->name;
         }
         else {
            $the_text = 'Closed Today';
         }
      }
      else {
         $the_text = $this->today_open_time . ' - ' . $this->today_close_time;  
      }
      
      /* option - show today's date */
      if ($this->show_todays_date) {
         $the_text = date('l F j, Y') . '<br>' . $the_text;
      }
      
      $this->widget_text = $the_text;
   }
   
   
   private function friendly_twelves($the_time) {
      if (strtotime($the_time) == strtotime('midnight') ) {
         $the_time = 'Midnight';
      }
      else if (strtotime($the_time) == strtotime('noon') ) {
         $the_time = 'Noon';
      }
      return $the_time;
   }
   
} /* END TodaysHoursWidget class */

add_action('widgets_init', create_function('', 'return register_widget("TodaysHoursWidget");'));
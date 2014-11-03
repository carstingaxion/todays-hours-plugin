<?php

class TodaysHoursWidget extends WP_Widget {


   function __construct() {
      parent::__construct('todays_hours_widget',
                          'Todays Hours Widget',
                          array('classname' => 'TodaysHoursWidget',
                                'description' => 'Displays Todays Business Hours')
      );
   }

   public function widget( $args, $instance ) {
		echo $args['before_widget'];
      
      echo "<h1 class='clock-icon'>Today's Hours</h1>";
      echo "<div id='todaysHours' class='textwidget'>";
         /* FIGURE OUT THE BUSINESS HOURS */
         echo "<p>" . $this->getTheHours() . "</p>";
         echo "<a href='http://library.milligan.edu/faq/#hours'>View all business hours</a>";
      echo "</div>";
      
      echo $args['after_widget'];
	}
   
   public function form( $instance ) {}

	public function update( $new_instance, $old_instance ) {}

   private function getTheHours() {

      $settings = get_option('todayshours_settings');
      $seasons = json_decode($settings['seasons']);
      $holidays = json_decode($settings['holidays']);
      
      $isHoliday = false;

      /* some options */
      $showTodaysDate = $settings['showdate'];
      $showReasonClosed = $settings['showreason'];
      $useFriendlyTwelves = $settings['friendly12'];
      
      $nowTimestamp = time();
      $nowDayOfWeek = date('D', $nowTimestamp);
      
      /* used to pad a day to it's full number of seconds */
      $oneDayMinusOneSecond = 86399;
      
      /* Find what season we're in now */
      for ($i = 0; $i < count($seasons); $i++) {
         $beginDate = strtotime($seasons[$i]->begin_date);
         $endDate = strtotime($seasons[$i]->end_date);
         if ($nowTimestamp > $beginDate && $nowTimestamp < $endDate) {
            $currentSeason = $seasons[$i];
            break;
         }
      }
      
      /* Check if today is on a holiday */
      for ($i = 0; $i < count($holidays); $i++) {
         $beginDate = strtotime($holidays[$i]->begin_date);
         $endDate = strtotime($holidays[$i]->end_date);
         if ($nowTimestamp > $beginDate && $nowTimestamp < ($endDate + $oneDayMinusOneSecond)) {
            $currentHoliday = $holidays[$i];
            $openTime = $currentHoliday->open_time;
            $closeTime = $currentHoliday->close_time;
            $isHoliday = true;
            break;
         }
      }
      
      /* if not a holiday get normal season hours */
      if (!$isHoliday) {
         switch ($nowDayOfWeek) {
            case 'Sun':
               $openTime = $currentSeason->su_open;
               $closeTime = $currentSeason->su_close;
               break;
            case 'Mon':
               $openTime = $currentSeason->mo_open;
               $closeTime = $currentSeason->mo_close; 
               break;
            case 'Tue':
               $openTime = $currentSeason->tu_open;
               $closeTime = $currentSeason->tu_close; 
               break;
            case 'Wed':
               $openTime = $currentSeason->we_open;
               $closeTime = $currentSeason->we_close; 
               break;
            case 'Thu':
               $openTime = $currentSeason->th_open;
               $closeTime = $currentSeason->th_close; 
               break;
            case 'Fri':
               $openTime = $currentSeason->fr_open;
               $closeTime = $currentSeason->fr_close; 
               break;
            case 'Sat':
               $openTime = $currentSeason->sa_open;
               $closeTime = $currentSeason->sa_close; 
               break;
            default:
               break;
         }
      }
      
      /* option - use 'noon' and 'midnight' */
      if ($useFriendlyTwelves) {
         $openTime = $this->friendlyTwelves($openTime);
         $closeTime = $this->friendlyTwelves($closeTime);
      }
      
      /* check if closed */
      if ($openTime == '') {
         /* option - show reason closed (holiday name) */
         if ($isHoliday && $showReasonClosed) {
            $theHours = 'Closed for ' . $currentHoliday->name;
         }
         else {
            $theHours = 'Closed Today';
         }
      }
      else {
         $theHours = $openTime . ' - ' . $closeTime;  
      }
      
      /* option - show today's date */
      if ($showTodaysDate) {
         $theHours = date('l F j, Y') . '<br />' . $theHours;
      }
      
      return $theHours;
   }

   
   private function friendlyTwelves($theTime) {
      if (strtotime($theTime) == strtotime('midnight') ) {
         $theTime = 'Midnight';
      }
      else if (strtotime($theTime) == strtotime('noon') ) {
         $theTime = 'Noon';
      }
      return $theTime;
   }
   
} /* END TodaysHoursWidget class */

add_action('widgets_init', create_function('', 'return register_widget("TodaysHoursWidget");'));
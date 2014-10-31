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
      $theHours = '8:00am-Midnight';
      
      return $theHours;
   }
   
}

add_action('widgets_init', create_function('', 'return register_widget("TodaysHoursWidget");'));
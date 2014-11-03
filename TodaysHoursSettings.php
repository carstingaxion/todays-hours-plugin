<?php

class TodaysHoursSettings {

   private $option_name = 'todayshours_settings';
   private $option_page = 'todayshours_settings_page';
   
   private $settings;
   
   public function __construct() {
      /* Get plugin options. Returns false if not created yet */
      $this->settings = get_option($this->option_name);
      
      /* If not created, create option & set defaults */
      if (!$this->settings) {
         add_option($this->option_name);
         
         $this->settings = array(
            'seasons'   => '', /* JSONify array and store as string! */
            'holidays'  => '',
            'showdate' => true,
            'showreason' => true,
            'friendly12' => true
         );
         
         $seasons_array = array();
         $holidays_array = array();
         
         /* Sample data */
         $s1 = new Season;
         $s1->name = "Normal Schedule";
         $s1->begin_date = "1/1/2015";
         $s1->end_date = "12/31/2025";
         $s1->mo_open = "8:00am";
         $s1->mo_close = "11:00pm";
         $s1->tu_open = "8:00am";
         $s1->tu_close = "11:00pm";
         $s1->we_open = "8:00am";
         $s1->we_close = "11:00pm";
         $s1->th_open = "8:00am";
         $s1->th_close = "11:00pm";
         $s1->fr_open = "8:00am";
         $s1->fr_close = "9:00pm";
         
         $h1 = new Holiday;
         $h1->name = "Thanksgiving";
         $h1->begin_date = "11/26/15";
         $h1->end_date = "11/27/15";
       
         array_push($seasons_array, $s1);
         array_push($holidays_array, $h1);
         
         /* json_encode so data can be stored in hidden form input */
         $this->settings['seasons'] = json_encode($seasons_array);
         $this->settings['holidays'] = json_encode($holidays_array);
         
         $this->save_settings();
      }
      
      if (is_admin()) {
         add_action('admin_menu', array($this, 'register_todays_hours_settings_page'));
         add_action('admin_init', array($this, 'register_todays_hours_settings'));
      }
      
   }
   
   public function getSettings() {
      return $this->settings;
   }
   
   public function register_todays_hours_settings() {
      
      add_settings_section(
         'todays_hours_main_section',
         'General Settings',
         array($this, 'todays_hours_main_section_callback'),
         $this->option_page
      );
      
      add_settings_section(
         'todays_hours_schedule_section',
         'Schedule Settings',
         array($this, 'todays_hours_schedule_section_callback'),
         $this->option_page
      );
      
      add_settings_field(
         'showdate',
         'Show Today\'s Date',
         array($this, 'todays_hours_showdate_callback'),
         $this->option_page,
         'todays_hours_main_section'
      );
      
      add_settings_field(
         'showreason',
         'Show Reason Closed',
         array($this, 'todays_hours_showreason_callback'),
         $this->option_page,
         'todays_hours_main_section'
      );
      
      add_settings_field(
         'friendly12',
         'Show Noon/Midnight',
         array($this, 'todays_hours_friendly12_callback'),
         $this->option_page,
         'todays_hours_main_section'
      );
      
      add_settings_field(
         'seasons',
         'Seasons/Semesters',
         array($this, 'todays_hours_seasons_callback'),
         $this->option_page,
         'todays_hours_schedule_section'
      );
      
      add_settings_field(
         'holidays',
         'Holidays/Exceptions',
         array($this, 'todays_hours_holidays_callback'),
         $this->option_page,
         'todays_hours_schedule_section'
      );
      
      register_setting($this->option_page, $this->option_name);
   }
   
   
   public function todays_hours_main_section_callback($args) {/*User doc here*/}
   public function todays_hours_schedule_section_callback($args) {}
   
   
   public function todays_hours_showdate_callback($args) {
      $html .= "<input type='checkbox' name='todayshours_settings[showdate]' id='showdate' " . ($this->settings['showdate'] ? 'checked' : '') . " ><label for='showdate'>Show today's date before the hours</label></input>";
      echo $html;
   }
   
   
   public function todays_hours_showreason_callback($args) {
      $html .= "<input type='checkbox' name='todayshours_settings[showreason]' id='showreason' " . ($this->settings['showreason'] ? 'checked' : '') . " ><label for='showreason'>Show reason that day is closed (uses Holiday 'Name' field)</label</input>";
      echo $html;
   }
   
   
   public function todays_hours_friendly12_callback($args) {
      $html .= "<input type='checkbox' name='todayshours_settings[friendly12]' id='friendly12' " . ($this->settings['friendly12'] ? 'checked' : '') . " ><label for='friendly12'>Use 'Midnight' and 'Noon' in place of 12:00am and 12:00pm</label></input>";
      echo $html;
   }
   
   
   public function todays_hours_seasons_callback($args) {
      $seasons_array = json_decode($this->settings['seasons']);
      
      $html .= "<div><p>A Season is a period of days. They could be used to define a year, a semester, or any block of time.</p>";
      $html .= "<p>In order for the widget to display a day's hours, the day must fall within the date range of a Season.</p>";
      $html .= "<p>If you schedule does not change from season to season, you should use only one season. An institution such as a college or university would probably define a season for each semester that business hours were different. For example, our library is only open on weekdays during the summer. However, during the Fall and Spring semesters have weekend hours and stay open until midnight on most nights. Therefore, we define a season for the Summer, Fall Semester, and Spring Semester.<p>";
      $html .= "<p><strong>Blank open times are regarded as closed for the day.</strong></p></div>";

      $html .= "<div class='thForm'>";

      $season_counter = 0;
      foreach ($seasons_array as $s) {
         $html .= "<div id='season" . $season_counter . "'>";
         $html .= "<h3>Season " . ($season_counter + 1) . "</h3>";
         $html .= "<table>";
         $html .= "<tr><td><input type='checkbox' name='seasonDelete_" . $season_counter . "' value=''>Delete this Season</input></td></tr>";
         $html .= "<tr><td>Name: <input type='text' name='seasonName_" . $season_counter . "' value='" . $s->name . "' ></input></td>";
         $html .= "<td>Begin Date: <input type='text' onfocus='blur()' class='datepicker' name='seasonBegin_" . $season_counter . "' value='" . $s->begin_date . "' maxlength='10' size='10'></input></td>";
         $html .= "<td>End Date: <input type='text' onfocus='blur()' class='datepicker' name='seasonEnd_" . $season_counter . "' value='" . $s->end_date . "' maxlength='10' size='10'></input></td></tr></table>";
         $html .= "<table><tr><td>Sunday </td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonSuOpen_" . $season_counter . "' value='" . $s->su_open . "' maxlength='8' size='8'></input></td>";
         $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonSuClose_" . $season_counter . "' value='" . $s->su_close . "' maxlength='8' size='8'></input></td></tr>";
         $html .= "<tr><td>Monday </td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonMoOpen_" . $season_counter . "' value='" . $s->mo_open . "' maxlength='8' size='8'></input></td>";
         $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonMoClose_" . $season_counter . "' value='" . $s->mo_close . "' maxlength='8' size='8'></input></td></tr>";
         $html .= "<tr><td>Tuesday </td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonTuOpen_" . $season_counter . "' value='" . $s->tu_open . "' maxlength='8' size='8'></input></td>";
         $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonTuClose_" . $season_counter . "' value='" . $s->tu_close . "' maxlength='8' size='8'></input></td></tr>";
         $html .= "<tr><td>Wednesday </td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonWeOpen_" . $season_counter . "' value='" . $s->we_open . "' maxlength='8' size='8'></input></td>";
         $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonWeClose_" . $season_counter . "' value='" . $s->we_close . "' maxlength='8' size='8'></input></td></tr>";
         $html .= "<tr><td>Thursday </td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonThOpen_" . $season_counter . "' value='" . $s->th_open . "' maxlength='8' size='8'></input></td>";
         $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonThClose_" . $season_counter . "' value='" . $s->th_close . "' maxlength='8' size='8'></input></td></tr>";
         $html .= "<tr><td>Friday </td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonFrOpen_" . $season_counter . "' value='" . $s->fr_open . "' maxlength='8' size='8'></input></td>";
         $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonFrClose_" . $season_counter . "' value='" . $s->fr_close . "' maxlength='8' size='8'></input></td></tr>";
         $html .= "<tr><td>Saturday </td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonSaOpen_" . $season_counter . "' value='" . $s->sa_open . "' maxlength='8' size='8'></input></td>";
         $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonSaClose_" . $season_counter . "' value='" . $s->sa_close . "' maxlength='8' size='8'></input></td></tr>";
         $html .= "</table>";
         $html .= "</div>";
         
         $season_counter++;
      }
  
      /* Fields to add another Season */
      $html .= "<div id='showNewSeason' class='button' >Add new Season</div>";

      $html .= "<div id='addNewSeason' class='hidden'>";
      $html .= "<h3>Fill out the following fields to add a Season</h3>";
      $html .= "<table>";
      $html .= "<tr><td>Name: <input type='text' name='seasonName_new' value=''></input></td>";
      $html .= "<td>Begin Date: <input type='text' onfocus='blur()' class='datepicker' name='seasonBegin_new' value='' maxlength='10' size='10'></input></td>";
      $html .= "<td>End Date: <input type='text' onfocus='blur()'  class='datepicker' name='seasonEnd_new' value='' maxlength='10' size='10'></input></td></tr></table>";
      $html .= "<table><tr><td>Sunday </td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonSuOpen_new' value='' maxlength='8' size='8'></input></td>";
      $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonSuClose_new' value='' maxlength='8' size='8'></input></td></tr>";
      $html .= "<tr><td>Monday</td><td>Open:<input type='text' onfocus='blur()' class='timepicker' name='seasonMoOpen_new' value='' maxlength='8' size='8'></input></td>";
      $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonMoClose_new' value='' maxlength='8' size='8'></input></td></tr>";
      $html .= "<tr><td>Tuesday</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonTuOpen_new' value='' maxlength='8' size='8'></input></td>";
      $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonTuClose_new' value='' maxlength='8' size='8'></input></td></tr>";
      $html .= "<tr><td>Wednesday</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonWeOpen_new' value='' maxlength='8' size='8'></input></td>";
      $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonWeClose_new' value='' maxlength='8' size='8'></input></td></tr>";
      $html .= "<tr><td>Thursday</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonThOpen_new' value='' maxlength='8' size='8'></input></td>";
      $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonThClose_new' value='' maxlength='8' size='8'></input></td></tr>";
      $html .= "<tr><td>Friday</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonFrOpen_new' value='' maxlength='8' size='8'></input></td>";
      $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonFrClose_new' value='' maxlength='8' size='8'></input></td></tr>";
      $html .= "<tr><td>Saturday</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='seasonSaOpen_new' value='' maxlength='8' size='8'></input></td>";
      $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='seasonSaClose_new' value='' maxlength='8' size='8'></input></td></tr>";
      $html .= "</table>";
      $html .= "</div>";
  
      /* Current JSON encoded settings */
      $html .= "<input type='hidden' name='todayshours_settings[seasons]' id='seasons' value='" . $this->settings['seasons'] . "'></input>";
      $html .= "</div>";
      
      
      echo $html;
   }
   

   public function todays_hours_holidays_callback($args) {
      $holidays_array = json_decode($this->settings['holidays']);

      $html .= "<div><p>Holidays are used when there is a deviation or exception to the rules defined in the Seasons. For example, your office is closed on the Thanksgiving holiday. The hours apply to each day within the date range chosen.</p>";
      $html .= "<p><strong>Blank open times are regarded as closed for the day.</strong></p></div>";
      
      $html .= "<div class='thForm'>";
      
      $holiday_counter = 0;
      foreach ($holidays_array as $h) {
         $html .= "<div id='holiday" . $holiday_counter . "'>";
         $html .= "<h3>Holiday " . ($holiday_counter + 1) . "</h3>";
         $html .= "<table>";
         $html .= "<tr><td><input type='checkbox' name='holidayDelete_" . $holiday_counter . "' value=''>Delete this Holiday</input></td></tr>";
         $html .= "<tr><td>Name: <input type='text' name='holidayName_" . $holiday_counter . "' value='" . $h->name ."' ></input></td>";
         $html .= "<td>Begin Date: <input type='text' onfocus='blur()' class='datepicker' name='holidayBegin_" . $holiday_counter . "' value='" . $h->begin_date . "' maxlength='10' size='10'></input></td>";
         $html .= "<td>End Date: <input type='text' onfocus='blur()' class='datepicker' name='holidayEnd_" . $holiday_counter . "' value='" . $h->end_date . "' maxlength='10' size='10'></input></td></tr></table>";
         $html .= "<table><tr><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='holidayOpen_" . $holiday_counter . "' value='" . $h->open_time . "' maxlength='8' size='8'></input></td>";
         $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='holidayClose_" . $holiday_counter . "' value='" . $h->close_time . "' maxlength='8' size='8'></input></td></tr>";        
         $html .= "</table>";
         $html .= "</div>";
         
         $holiday_counter++;
      }
      
      /* Fields to add another Holiday */
      $html .= "<div id='showNewHoliday' class='button' >Add new Holiday</div>";

      $html .= "<div id='addNewHoliday' class='hidden'>";
      $html .= "<h3>Fill out the following fields to add a Holiday</h3>";
      $html .= "<table>";
      $html .= "<tr><td>Name: <input type='text' name='holidayName_" . $holiday_counter . "' value=''></input></td>";
      $html .= "<td>Begin Date: <input type='text' onfocus='blur()' class='datepicker' name='holidayBegin_" . $holiday_counter . "' value='' maxlength='10' size='10'></input></td>";
      $html .= "<td>End Date: <input type='text' onfocus='blur()' class='datepicker' name='holidayEnd_" . $holiday_counter . "' value='' maxlength='10' size='10'></input></td></tr></table>";
      $html .= "<table><tr><td>Open: <input type='text' onfocus='blur()' class='timepicker' name='holidayOpen_" . $holiday_counter . "' value='' maxlength='8' size='8'></input></td>";
      $html .= "<td>Close: <input type='text' onfocus='blur()' class='timepicker' name='holidayClose_" . $holiday_counter . "' value='' maxlength='8' size='8'></input></td></tr>";        
      $html .= "</table>";
      $html .= "</div>";
      
      /* Current JSON encoded settings */
      $html .= "<input type='hidden' name='todayshours_settings[holidays]' id='holidays' value='" . $this->settings['holidays'] . "'></input>";
      $html .= "</div>";   

      echo $html;
   }
   

   public function register_todays_hours_settings_page() {
      add_options_page(
         'Today\'s Hours',
         'Today\'s Hours',
         'administrator',
         'todays_hours_settings_page',
         array($this, 'todays_hours_settings_page_callback')
      );
   }
 

 public function todays_hours_settings_page_callback() { ?>
      <div class="wrap">
         <div id="icon-tools" class="icon32">&nbsp;</div>
         <h2>Today's Hours</h2>
         
         <form method="post" action="options.php">
            <?php settings_fields($this->option_page);?>
            <?php do_settings_sections($this->option_page);?>
            <?php submit_button(); ?> 

            <?php wp_enqueue_style('todayshoursettingsstyle', plugins_url('todaysHoursSettings.css', __FILE__)); ?>
            <?php wp_enqueue_script('todayshourssettings', plugins_url('todaysHoursSettings.js', __FILE__), array('jquery'), '1.0', true); ?>
            <?php wp_enqueue_script('jquerytimepicker', plugins_url('jquery.ui.timepicker.js', __FILE__), array('jquery'), '0.3.3', true); ?>
            <?php wp_enqueue_script('jquery-ui-datepicker');?>
            
            
         
         </form>
         
      </div>
      
   <?php
   }

   
   public function save_settings() {
      update_option($this->option_name, $this->settings);
   }
} /* END TodaysHoursSettings class


/* All days within the year should fall within a season. Some institutions may only have 1 season that consists of the entire year. 
   The school year will consist of breaks and semesters */
class Season {
   public $name;        /* i.e. Fall Semester, Summer Break, etc. */
   public $begin_date;
   public $end_date;
   public $su_open;     /* Sunday open */
   public $su_close;    /* Sunday close */
   public $mo_open;     /* etc. */
   public $mo_close;
   public $tu_open;
   public $tu_close;
   public $we_open;
   public $we_close;
   public $th_open;
   public $th_close;
   public $fr_open;
   public $fr_close;
   public $sa_open;
   public $sa_close;
}

/* Deviations from the current season */
class Holiday {
   public $name;
   public $begin_date;
   public $end_date;
   public $open_time;
   public $close_time;
}
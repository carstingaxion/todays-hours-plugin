<?php
/*
   Today's Hours Plugin - Settings
   David Baker, Milligan College 2014
*/

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
            'friendly12' => true,
            'widgettext' => 'Today\'s Hours'
         );
         
         $seasons_array = array();
         $holidays_array = array();
         
         /* Sample data */
         $s1 = new Season;
         $s1->name = "Normal Schedule";
         $s1->begin_date = "1/1/2014";
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
         __( 'General Settings', 'todays-hours-plugin' ),
         array($this, 'todays_hours_main_section_callback'),
         $this->option_page
      );
      
      add_settings_section(
         'todays_hours_schedule_section',
         __( 'Schedule Settings', 'todays-hours-plugin' ),
         array($this, 'todays_hours_schedule_section_callback'),
         $this->option_page
      );
      
      add_settings_field(
         'showdate',
         __( 'Show Today\'s Date', 'todays-hours-plugin' ),
         array($this, 'todays_hours_showdate_callback'),
         $this->option_page,
         'todays_hours_main_section'
      );
      
      add_settings_field(
         'showreason',
         __( 'Show Reason Closed', 'todays-hours-plugin' ),
         array($this, 'todays_hours_showreason_callback'),
         $this->option_page,
         'todays_hours_main_section'
      );
      
      add_settings_field(
         'friendly12',
         __( 'Show Noon/Midnight', 'todays-hours-plugin' ),
         array($this, 'todays_hours_friendly12_callback'),
         $this->option_page,
         'todays_hours_main_section'
      );
      
      add_settings_field(
         'widgettext',
         __( 'Widget Title', 'todays-hours-plugin' ),
         array($this, 'todays_hours_widgettext_callback'),
         $this->option_page,
         'todays_hours_main_section'
      );
      
      add_settings_field(
         'seasons',
         __( 'Seasons/Semesters', 'todays-hours-plugin' ),
         array($this, 'todays_hours_seasons_callback'),
         $this->option_page,
         'todays_hours_schedule_section'
      );
      
      add_settings_field(
         'holidays',
         __( 'Holidays/Exceptions', 'todays-hours-plugin' ),
         array($this, 'todays_hours_holidays_callback'),
         $this->option_page,
         'todays_hours_schedule_section'
      );
    
      register_setting($this->option_page, $this->option_name, array($this, 'todays_hours_sanitize_callback'));
   }
   
   
   public function todays_hours_sanitize_callback($input) {

      $input['widgettext'] = htmlentities(sanitize_text_field($input['widgettext']));
      
      $seasons_array = json_decode($input['seasons']);
      $holidays_array = json_decode($input['holidays']);
      
      foreach ($seasons_array as $s) {
         $s->name = htmlentities(sanitize_text_field($s->name));
      }
      
      foreach ($holidays_array as $h) {
         $h->name = htmlentities(sanitize_text_field($h->name));
      }
      
      $input['seasons'] = json_encode($seasons_array);
      $input['holidays'] = json_encode($holidays_array);

      //print_r($input);
      
      //exit;
      
      return $input;
   }
   
   
   public function todays_hours_main_section_callback($args) {/*User doc here*/}
   public function todays_hours_schedule_section_callback($args) {}
   
   
   public function todays_hours_showdate_callback($args) {
      $html = "<input type='checkbox' name='todayshours_settings[showdate]' id='showdate' " . ($this->settings['showdate'] ? 'checked' : '') . " >
      <label for='showdate'>" . __( 'Show today\'s date before the hours', 'todays-hours-plugin' ) . "</label>";
      echo $html;
   }
   
   
   public function todays_hours_showreason_callback($args) {
      $html = "<input type='checkbox' name='todayshours_settings[showreason]' id='showreason' " . ($this->settings['showreason'] ? 'checked' : '') . " >
      <label for='showreason'>" . __( 'Show reason that day is closed (uses Holiday \'Name\' field)', 'todays-hours-plugin' ) . "</label>";
      echo $html;
   }
   
   
   public function todays_hours_friendly12_callback($args) {
      $html = "<input type='checkbox' name='todayshours_settings[friendly12]' id='friendly12' " . ($this->settings['friendly12'] ? 'checked' : '') . " >
      <label for='friendly12'>" . __( 'Use \'Midnight\' and \'Noon\' in place of 12:00am and 12:00pm', 'todays-hours-plugin' ) . "</label>";
      echo $html;
   }
   
   
   public function todays_hours_widgettext_callback($args) {
      $html = "<input type='text' name='todayshours_settings[widgettext]' id='widgettext' value='" . $this->settings['widgettext'] . "' >
      <label for='widgettext'>" . __( 'Heading text for the widget', 'todays-hours-plugin' ) . "</label>";
      echo $html;
   }
   
   
   public function todays_hours_seasons_callback($args) {
      $seasons_array = json_decode($this->settings['seasons']);
      
      $html = "<div><p>" . __( 'A Season is a period of days. They could be used to define a year, a semester, or any block of time.', 'todays-hours-plugin' ) . "</p>";
      $html .= "<p>" . __( 'In order for the widget to display a day\'s hours, the day must fall within the date range of a Season.', 'todays-hours-plugin' ) . "</p>";
      $html .= "<p>" . __( 'If you schedule does not change from season to season, you should use only one season. An institution such as a college or university would probably define a season for each semester that business hours were different. For example, our library is only open on weekdays during the summer. However, during the Fall and Spring semesters have weekend hours and stay open until midnight on most nights. Therefore, we define a season for the Summer, Fall Semester, and Spring Semester.', 'todays-hours-plugin' ) . "<p>";
      $html .= "<p><strong>" . __( 'Blank open times are regarded as closed for the day.', 'todays-hours-plugin' ) . "</strong></p></div>";

      $html .= "<div class='thForm'>";

      $season_counter = 0;
      foreach ($seasons_array as $s) {
         $html .= "<div id='season" . $season_counter . "'>";
         $html .= "<h3>" . __( 'Season ', 'mention the empty space at the end', 'todays-hours-plugin' ) . ($season_counter + 1) . "</h3>";
         $html .= "<table>";
         $html .= "<tr><td><input type='checkbox' id='seasonDelete_" . $season_counter . "' value=''><label>" . __( 'Delete this Season', 'todays-hours-plugin' ) . "</label></td></tr>";
         $html .= "<tr><td>" . _x( 'Name: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' id='seasonName_" . $season_counter . "' value='" . $s->name . "' ></td>";
         $html .= "<td>" . _x( 'Begin Date: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='datepicker' id='seasonBegin_" . $season_counter . "' value='" . $s->begin_date . "' maxlength='10' size='10'></td>";
         $html .= "<td>" . _x( 'End Date: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='datepicker' id='seasonEnd_" . $season_counter . "' value='" . $s->end_date . "' maxlength='10' size='10'></td></tr></table>";
         $html .= "<table><tr><td>" . _x( 'Sunday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonSuOpen_" . $season_counter . "' value='" . $s->su_open . "' maxlength='8' size='8'></td>";
         $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonSuClose_" . $season_counter . "' value='" . $s->su_close . "' maxlength='8' size='8'></td></tr>";
         $html .= "<tr><td>" . _x( 'Monday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonMoOpen_" . $season_counter . "' value='" . $s->mo_open . "' maxlength='8' size='8'></td>";
         $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonMoClose_" . $season_counter . "' value='" . $s->mo_close . "' maxlength='8' size='8'></td></tr>";
         $html .= "<tr><td>" . _x( 'Tuesday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonTuOpen_" . $season_counter . "' value='" . $s->tu_open . "' maxlength='8' size='8'></td>";
         $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonTuClose_" . $season_counter . "' value='" . $s->tu_close . "' maxlength='8' size='8'></td></tr>";
         $html .= "<tr><td>" . _x( 'Wednesday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonWeOpen_" . $season_counter . "' value='" . $s->we_open . "' maxlength='8' size='8'></td>";
         $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonWeClose_" . $season_counter . "' value='" . $s->we_close . "' maxlength='8' size='8'></td></tr>";
         $html .= "<tr><td>" . _x( 'Thursday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonThOpen_" . $season_counter . "' value='" . $s->th_open . "' maxlength='8' size='8'></td>";
         $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonThClose_" . $season_counter . "' value='" . $s->th_close . "' maxlength='8' size='8'></td></tr>";
         $html .= "<tr><td>" . _x( 'Friday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonFrOpen_" . $season_counter . "' value='" . $s->fr_open . "' maxlength='8' size='8'></td>";
         $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonFrClose_" . $season_counter . "' value='" . $s->fr_close . "' maxlength='8' size='8'></td></tr>";
         $html .= "<tr><td>" . _x( 'Saturday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonSaOpen_" . $season_counter . "' value='" . $s->sa_open . "' maxlength='8' size='8'></td>";
         $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonSaClose_" . $season_counter . "' value='" . $s->sa_close . "' maxlength='8' size='8'></td></tr>";
         $html .= "</table>";
         $html .= "</div>";
         
         $season_counter++;
      }
  
      /* Fields to add another Season */
      $html .= "<div id='showNewSeason' class='button' >" . __( 'Add new Season', 'todays-hours-plugin' ) . "</div>";

      $html .= "<div id='addNewSeason' class='hidden'>";
      $html .= "<h3>" . __( 'Fill out the following fields to add a Season', 'todays-hours-plugin' ) . "</h3>";
      $html .= "<table>";
      $html .= "<tr><td>" . _x( 'Name: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' id='seasonName_new' value=''></td>";
      $html .= "<td>" . _x( 'Begin Date: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='datepicker' id='seasonBegin_new' value='' maxlength='10' size='10'></td>";
      $html .= "<td>" . _x( 'End Date: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()'  class='datepicker' id='seasonEnd_new' value='' maxlength='10' size='10'></td></tr></table>";
      $html .= "<table><tr><td>" . _x( 'Sunday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonSuOpen_new' value='' maxlength='8' size='8'></td>";
      $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonSuClose_new' value='' maxlength='8' size='8'></td></tr>";
      $html .= "<tr><td>" . _x( 'Monday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open:<input type='text' onfocus='blur()' class='timepicker' id='seasonMoOpen_new' value='' maxlength='8' size='8'></td>";
      $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonMoClose_new' value='' maxlength='8' size='8'></td></tr>";
      $html .= "<tr><td>" . _x( 'Tuesday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonTuOpen_new' value='' maxlength='8' size='8'></td>";
      $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonTuClose_new' value='' maxlength='8' size='8'></td></tr>";
      $html .= "<tr><td>" . _x( 'Wednesday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonWeOpen_new' value='' maxlength='8' size='8'></td>";
      $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonWeClose_new' value='' maxlength='8' size='8'></td></tr>";
      $html .= "<tr><td>" . _x( 'Thursday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonThOpen_new' value='' maxlength='8' size='8'></td>";
      $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonThClose_new' value='' maxlength='8' size='8'></td></tr>";
      $html .= "<tr><td>" . _x( 'Friday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonFrOpen_new' value='' maxlength='8' size='8'></td>";
      $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonFrClose_new' value='' maxlength='8' size='8'></td></tr>";
      $html .= "<tr><td>" . _x( 'Saturday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "</td><td>Open: <input type='text' onfocus='blur()' class='timepicker' id='seasonSaOpen_new' value='' maxlength='8' size='8'></td>";
      $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='seasonSaClose_new' value='' maxlength='8' size='8'></td></tr>";
      $html .= "</table>";
      $html .= "</div>";
  
      /* Current JSON encoded settings */
      $html .= "<input type='hidden' name='todayshours_settings[seasons]' id='seasons' value='" . $this->settings['seasons'] . "'>";
      $html .= "</div>";
      
      
      echo $html;
   }
   

   public function todays_hours_holidays_callback($args) {
      $holidays_array = json_decode($this->settings['holidays']);

      $html = "<div><p>" . __( 'Holidays are used when there is a deviation or exception to the rules defined in the Seasons. For example, your office is closed on the Thanksgiving holiday. The hours apply to each day within the date range chosen.', 'todays-hours-plugin' ) . "</p>";
      $html .= "<p><strong>" . __( 'Blank open times are regarded as closed for the day.', 'todays-hours-plugin' ) . "</strong></p></div>";
      
      $html .= "<div class='thForm'>";
      
      $holiday_counter = 0;
      foreach ($holidays_array as $h) {
         $html .= "<div id='holiday" . $holiday_counter . "'>";
         $html .= "<h3>" . _x( 'Holiday ', 'mention the empty space at the end', 'todays-hours-plugin' ) . ($holiday_counter + 1) . "</h3>";
         $html .= "<table>";
         $html .= "<tr><td><input type='checkbox' id='holidayDelete_" . $holiday_counter . "' value=''><label>Delete this Holiday</label></td></tr>";
         $html .= "<tr><td>" . _x( 'Name: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' id='holidayName_' . $holiday_counter . '' value='" . $h->name ."' ></td>";
         $html .= "<td>" . _x( 'Begin Date: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='datepicker' id='holidayBegin_" . $holiday_counter . "' value='" . $h->begin_date . "' maxlength='10' size='10'></td>";
         $html .= "<td>" . _x( 'End Date: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='datepicker' id='holidayEnd_" . $holiday_counter . "' value='" . $h->end_date . "' maxlength='10' size='10'></td></tr></table>";
         $html .= "<table><tr><td>" . _x( 'Open: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='holidayOpen_" . $holiday_counter . "' value='" . $h->open_time . "' maxlength='8' size='8'></td>";
         $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='holidayClose_" . $holiday_counter . "' value='" . $h->close_time . "' maxlength='8' size='8'></td></tr>";        
         $html .= "</table>";
         $html .= "</div>";
         
         $holiday_counter++;
      }
      
      /* Fields to add another Holiday */
      $html .= "<div id='showNewHoliday' class='button' >" . __( 'Add new Holiday', 'todays-hours-plugin' ) . "</div>";

      $html .= "<div id='addNewHoliday' class='hidden'>";
      $html .= "<h3>" . __( 'Fill out the following fields to add a Holiday', 'todays-hours-plugin' ) . "</h3>";
      $html .= "<table>";
      $html .= "<tr><td>" . _x( 'Name: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' id='holidayName_new' value=''></td>";
      $html .= "<td>" . _x( 'Begin Date: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='datepicker' id='holidayBegin_new' value='' maxlength='10' size='10'></td>";
      $html .= "<td>" . _x( 'End Date: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='datepicker' id='holidayEnd_new' value='' maxlength='10' size='10'></td></tr></table>";
      $html .= "<table><tr><td>" . _x( 'Open: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='holidayOpen_new' value='' maxlength='8' size='8'></td>";
      $html .= "<td>" . _x( 'Close: ', 'mention the empty space at the end', 'todays-hours-plugin' ) . "<input type='text' onfocus='blur()' class='timepicker' id='holidayClose_new' value='' maxlength='8' size='8'></td></tr>";        
      $html .= "</table>";
      $html .= "</div>";
      
      /* Current JSON encoded settings */
      $html .= "<input type='hidden' name='todayshours_settings[holidays]' id='holidays' value='" . $this->settings['holidays'] . "'>";
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

            <?php wp_enqueue_style('todayshoursettingsstyle', plugins_url('../css/todaysHoursSettings.css', __FILE__), array(), filemtime(dirname(dirname(__FILE__)) . '/css/todaysHoursSettings.css'), 'all'); ?>
            <?php wp_enqueue_script('todayshourssettings', plugins_url('../js/todaysHoursSettings.js', __FILE__), array('jquery'), filemtime(dirname(dirname(__FILE__)) . '/js/todaysHoursSettings.js'), true); ?>
            <?php wp_enqueue_script('jquerytimepicker', plugins_url('../timepicker/jquery.ui.timepicker.js', __FILE__), array('jquery'), '0.3.3', true); ?>
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
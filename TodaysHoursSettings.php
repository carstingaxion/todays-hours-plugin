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
         );
         
         $seasons_array = array();
         $holidays_array = array();
         
         /* test data */
         $s1 = new Season;
         $s1->name = "Fall Semester";
         $s2 = new Season;
         $s2->name = "Spring Semester";
         array_push($seasons_array, $s1, $s2);
         
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
   
   
   public function register_todays_hours_settings() {
      
      add_settings_section(
         'todays_hours_main_section',
         'General Settings',
         array($this, 'todays_hours_main_section_callback'),
         $this->option_page
      );
      
      add_settings_field(
         'seasons',
         'Seasons',
         array($this, 'todays_hours_seasons_callback'),
         $this->option_page,
         'todays_hours_main_section'
      );
      
      add_settings_field(
         'holidays',
         'Holidays',
         array($this, 'todays_hours_holidays_callback'),
         $this->option_page,
         'todays_hours_main_section'
      );
      
      register_setting($this->option_page, $this->option_name);
   }
   
   
   public function todays_hours_main_section_callback($args) {/*User doc here*/}
   
   
   public function todays_hours_seasons_callback($args) {
      $seasons_array = json_decode($this->settings['seasons']);

      $html .= "<div style='border-bottom:1px solid #bbb; padding:25px;'>";
      $html .= "<style type='text/css'> tr td {padding:5px !important;}</style>";

      $season_counter = 0;
      foreach ($seasons_array as $s) {
         $html .= "<div id='season" . $season_counter . "'>";
         $html .= "<h3>Season " . ($season_counter + 1) . "</h3>";
         $html .= "<table>";
         $html .= "<tr><td>Name: <input type='text' name='seasonName_" . $season_counter . "' value='" . $s->name . "' ></input></td>";
         $html .= "<td>Begin Date: <input type='text' name='seasonBegin_" . $season_counter . "' value='" . $s->begin_date . "' maxlength='10' size='10'></input></td>";
         $html .= "<td>End Date: <input type='text' name='seasonEnd_" . $season_counter . "' value='" . $s->end_date . "' maxlength='10' size='10'></input></td></tr></table>";
         $html .= "<table><tr><td>Sunday </td><td>Open: <input type='text' name='seasonSuOpen_" . $season_counter . "' value='" . $s->su_open . "' maxlength='4' size='4'></input></td>";
         $html .= "<td>Close: <input type='text' name='seasonSuClose_" . $season_counter . "' value='" . $s->su_close . "' maxlength='4' size='4'></input></td></tr>";
         $html .= "<tr><td>Monday </td><td>Open: <input type='text' name='seasonMoOpen_" . $season_counter . "' value='" . $s->mo_open . "' maxlength='4' size='4'></input></td>";
         $html .= "<td>Close: <input type='text' name='seasonMoClose_" . $season_counter . "' value='" . $s->mo_close . "' maxlength='4' size='4'></input></td></tr>";
         $html .= "<tr><td>Tuesday </td><td>Open: <input type='text' name='seasonTuOpen_" . $season_counter . "' value='" . $s->tu_open . "' maxlength='4' size='4'></input></td>";
         $html .= "<td>Close: <input type='text' name='seasonTuClose_" . $season_counter . "' value='" . $s->tu_close . "' maxlength='4' size='4'></input></td></tr>";
         $html .= "<tr><td>Wednesday </td><td>Open: <input type='text' name='seasonWeOpen_" . $season_counter . "' value='" . $s->we_open . "' maxlength='4' size='4'></input></td>";
         $html .= "<td>Close: <input type='text' name='seasonWeClose_" . $season_counter . "' value='" . $s->we_close . "' maxlength='4' size='4'></input></td></tr>";
         $html .= "<tr><td>Thursday </td><td>Open: <input type='text' name='seasonThOpen_" . $season_counter . "' value='" . $s->th_open . "' maxlength='4' size='4'></input></td>";
         $html .= "<td>Close: <input type='text' name='seasonThClose_" . $season_counter . "' value='" . $s->th_close . "' maxlength='4' size='4'></input></td></tr>";
         $html .= "<tr><td>Friday </td><td>Open: <input type='text' name='seasonFrOpen_" . $season_counter . "' value='" . $s->fr_open . "' maxlength='4' size='4'></input></td>";
         $html .= "<td>Close: <input type='text' name='seasonFrClose_" . $season_counter . "' value='" . $s->fr_close . "' maxlength='4' size='4'></input></td></tr>";
         $html .= "<tr><td>Saturday </td><td>Open: <input type='text' name='seasonSaOpen_" . $season_counter . "' value='" . $s->sa_open . "' maxlength='4' size='4'></input></td>";
         $html .= "<td>Close: <input type='text' name='seasonSaClose_" . $season_counter . "' value='" . $s->sa_close . "' maxlength='4' size='4'></input></td>";
         $html .= "<td><input type='button' name='seasonDelete_" . $season_counter . "' value='Delete Season " . ($season_counter + 1) . "'></input></td></tr>";
         $html .= "</table>";
         $html .= "</div>";
         
         $season_counter++;
      }
  
      /* Fields to add another Season */
      $html .= "<h3>Fill out the following fields to add another Season</h3>";
      $html .= "<table>";
      $html .= "<tr><td>Name: <input type='text' name='seasonName_new' value=''></input></td>";
      $html .= "<td>Begin Date: <input type='text' name='seasonBegin_new' value='' maxlength='10' size='10'></input></td>";
      $html .= "<td>End Date: <input type='text' name='seasonEnd_new' value='' maxlength='10' size='10'></input></td></tr></table>";
      $html .= "<table><tr><td>Sunday </td><td>Open: <input type='text' name='seasonSuOpen_new' value='' maxlength='4' size='4'></input></td>";
      $html .= "<td>Close: <input type='text' name='seasonSuClose_new' value='' maxlength='4' size='4'></input></td></tr>";
      $html .= "<tr><td>Monday</td><td>Open:<input type='text' name='seasonMoOpen_new' value='' maxlength='4' size='4'></input></td>";
      $html .= "<td>Close: <input type='text' name='seasonMoClose_new' value='' maxlength='4' size='4'></input></td></tr>";
      $html .= "<tr><td>Tuesday</td><td>Open: <input type='text' name='seasonTuOpen_new' value='' maxlength='4' size='4'></input></td>";
      $html .= "<td>Close: <input type='text' name='seasonTuClose_new' value='' maxlength='4' size='4'></input></td></tr>";
      $html .= "<tr><td>Wednesday</td><td>Open: <input type='text' name='seasonWeOpen_new' value='' maxlength='4' size='4'></input></td>";
      $html .= "<td>Close: <input type='text' name='seasonWeClose_new' value='' maxlength='4' size='4'></input></td></tr>";
      $html .= "<tr><td>Thursday</td><td>Open: <input type='text' name='seasonThOpen_new' value='' maxlength='4' size='4'></input></td>";
      $html .= "<td>Close: <input type='text' name='seasonThClose_new' value='' maxlength='4' size='4'></input></td></tr>";
      $html .= "<tr><td>Friday</td><td>Open: <input type='text' name='seasonFrOpen_new' value='' maxlength='4' size='4'></input></td>";
      $html .= "<td>Close: <input type='text' name='seasonFrClose_new' value='' maxlength='4' size='4'></input></td></tr>";
      $html .= "<tr><td>Saturday</td><td>Open: <input type='text' name='seasonSaOpen_new' value='' maxlength='4' size='4'></input></td>";
      $html .= "<td>Close: <input type='text' name='seasonSaClose_new' value='' maxlength='4' size='4'></input></td></tr>";
      $html .= "</table>";
  
      /* Current JSON encoded settings */
      $html .= "<input type='hidden' name='todayshours_settings[seasons]' id='seasons' value='" . $this->settings['seasons'] . "'></input>";
      $html .= "</div>";
      
      echo $html;
   }
   

   public function todays_hours_holidays_callback($args) {
      $holidays_array = json_decode($this->settings['holidays']);
      
      $html .= "<div style='border-bottom:1px solid #bbb; padding:25px;'>";
      
      $holiday_counter = 0;
      foreach ($holidays_array as $h) {
         $html .= "<div id='holiday" . $holiday_counter . "'>";
         $html .= "<h3>Holiday " . ($holiday_counter + 1) . "</h3>";
         $html .= "<table>";
         $html .= "<tr><td>Name: <input type='text' name='holidayName_" . $holiday_counter . "' value='" . $h->name ."' ></input></td>";
         $html .= "<td>Begin Date: <input type='text' name='holidayBegin_" . $holiday_counter . "' value='" . $h->begin_date . "' maxlength='10' size='10'></input></td>";
         $html .= "<td>End Date: <input type='text' name='holidayEnd_" . $holiday_counter . "' value='" . $h->end_date . "' maxlength='10' size='10'></input></td></tr></table>";
         $html .= "<table><tr><td>Open: <input type='text' name='holidayOpen_" . $holiday_counter . "' value='" . $h->open_time . "' maxlength='4' size='4'></input></td>";
         $html .= "<td>Close: <input type='text' name='holidayClose_" . $holiday_counter . "' value='" . $h->close_time . "' maxlength='4' size='4'></input></td>";        
         $html .= "<td><input type='button' name='holidayDelete_" . $holiday_counter . "' value='Delete Holiday " . ($holiday_counter + 1) . "'></input></td></tr>";
         $html .= "</table>";
         $html .= "</div>";
         
         $holiday_counter++;
      }
      
      /* Fields to add another Holiday */
      $html .= "<h3>Fill out the following fields to add another Holiday</h3>";
      $html .= "<table>";
      $html .= "<tr><td>Name: <input type='text' name='holidayName_" . $holiday_counter . "' value=''></input></td>";
      $html .= "<td>Begin Date: <input type='text' name='holidayBegin_" . $holiday_counter . "' value='' maxlength='10' size='10'></input></td>";
      $html .= "<td>End Date: <input type='text' name='holidayEnd_" . $holiday_counter . "' value='' maxlength='10' size='10'></input></td></tr></table>";
      $html .= "<table><tr><td>Open: <input type='text' name='holidayOpen_" . $holiday_counter . "' value='' maxlength='4' size='4'></input></td>";
      $html .= "<td>Close: <input type='text' name='holidayClose_" . $holiday_counter . "' value='' maxlength='4' size='4'></input></td></tr>";        
      $html .= "</table>";
      
      /* Current JSON encoded settings */
      $html .= "<input type='hidden' name='todayshours_settings[holidays]' id='holidays' value='" . $this->settings['holidays'] . "'></input>";
      $html .= "</div>";   

      echo $html;
   }
   

   public function register_todays_hours_settings_page() {
      add_options_page(
         'Todays Hours',
         'Todays Hours',
         'administrator',
         'todays_hours_settings_page',
         array($this, 'todays_hours_settings_page_callback')
      );
   }
 

 public function todays_hours_settings_page_callback() { ?>
      <div class="wrap">
         <div id="icon-tools" class="icon32">&nbsp;</div>
         <h2>Todays Hours</h2>
         
         <form method="post" action="options.php">
            <?php settings_fields($this->option_page);?>
            <?php do_settings_sections($this->option_page);?>
            <?php submit_button(); ?> 

            <?php wp_enqueue_script('todayshourssettings', plugins_url('todaysHoursSettings.js', __FILE__), array('jquery'), '1.0', true); ?>
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
<?php
/**
* Contains the Season class
* @author David Baker
* @copyright 2014-2015 Milligan College
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU Public License v2
* @since 1.2
*/

namespace PHWelshimer\TodaysHours;

/**
* Season class
* All days within the year should fall within a season. 
* Some institutions may only have 1 season that consists of the entire year. 
* The school year will consist of breaks and semesters
* 
* @since 1.0
*/
class Season {
   public $schedule;
   public $name;
   public $begin_date;
   public $end_date;
   public $su_open;
   public $su_close;
   public $mo_open;
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
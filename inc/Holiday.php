<?php
/**
* Contains the Holiday class
* @author David Baker
* @copyright 2014-2015 Milligan College
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU Public License v2
* @since 1.2
*/

namespace PHWelshimer\TodaysHours;

/**
* Holiday/Exception class
* Deviations from the current season
* 
* @since 1.0
*/
class Holiday {
   public $name;
   public $begin_date;
   public $end_date;
   public $open_time;
   public $close_time;
}

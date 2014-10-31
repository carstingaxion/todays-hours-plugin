<?php 
/*
Plugin Name: Today's Hours
Description: Displays the open hours for the current day.
Version: 1.0
Plugin URI: https://github.com/dbaker3/todays-hours-plugin
Author: David Baker - Milligan College
Author URI: https://github.com/dbaker3
*/

include 'TodaysHoursSettings.php';
include 'TodaysHoursWidget.php';

$settingsObject = new TodaysHoursSettings();
$widgetObject = new TodaysHoursWidget();


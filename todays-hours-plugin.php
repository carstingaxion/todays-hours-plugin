<?php 
/*
Plugin Name: 	Today's Hours
Description: 	Displays the open hours for the current day.
Version: 		1.1
Plugin URI: 	https://github.com/dbaker3/todays-hours-plugin
Author: 		David Baker - Milligan College
Author URI: 	https://github.com/dbaker3
*/

// Setup localization
load_plugin_textdomain( 'todays-hours-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

include 'inc/TodaysHoursSettings.php';
include 'inc/TodaysHoursWidget.php';
include 'inc/TodaysHoursShortcode.php';

$settingsObject = new TodaysHoursSettings();
$widgetObject = new TodaysHoursWidget();
$shortcodeObject = new TodaysHoursShortcode();


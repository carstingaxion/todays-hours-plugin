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

include 'inc/Settings.php';
include 'inc/Season.php';
include 'inc/Holiday.php';
include 'inc/Widget.php';

$settingsObject = new PHWelshimer\TodaysHours\Settings;
$widgetObject = new PHWelshimer\TodaysHours\Widget;


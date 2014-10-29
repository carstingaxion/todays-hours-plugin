<?php 
/*
Plugin Name: Today's Hours
Description: Displays the open hours for the current day.
Version: 1.0
Plugin URI: https://github.com/dbaker3/todays-hours-plugin
Author: David Baker
Author URI: https://github.com/dbaker3
*/

include 'TodaysHoursSettings.php';
include 'TodaysHoursWidget.php';

$settings = new TodaysHoursSettings();
$widget = new TodaysHoursWidget($settings);

/* add_action('admin_enqueue_scripts', 'load_my_scripts');

function load_my_scripts() {
   wp_enqueue_script('todayshourssettings', plugins_url('todaysHoursSettings.js', __FILE__), array('jquery'));
} */
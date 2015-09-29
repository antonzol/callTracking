<?php
/*
Plugin Name: CallTracking
Plugin URI: http://it4u.ua/
Description: CallTracking use Google Analitycs and virtual ATC for create statistic to calls.
Version: 1.0 beta
Author: IT4U
Author URI: http://it4u.ua/
*/
require_once "Calltracking.class.php";
require_once 'admin/admin.php';

register_activation_hook(__FILE__, array('CallTracking', 'install__plugin')); 
register_deactivation_hook(__FILE__, array('CallTracking', 'uninstall__plugin'));

add_filter('widget_text', 'do_shortcode');

function initFunc () {
	$callTracking = new CallTracking ();
}

add_action('init', 'initFunc');

function ajax_get_number ( ) {
	$callTracking = new CallTracking ();
	echo $callTracking->createNumber();
	die;
}

add_action('wp_ajax_nopriv_get_dnumber', 'ajax_get_number');
add_action('wp_ajax_get_dnumber', 'ajax_get_number');


function changeViewChart () {
	echo json_encode(getIssuetDataArray($_POST['data']));
	die;
}

add_action('wp_ajax_nopriv_changeViewChart', 'changeViewChart');
add_action('wp_ajax_changeViewChart', 'changeViewChart');

function count_busy_number () {
	global $wpdb;
	$numbers = $wpdb->get_row("SELECT COUNT(id) as count_number FROM " . $wpdb->prefix . "calltracking_telephone WHERE id_analytic <> '' AND time_expectation > NOW()");
	$wpdb->query("INSERT INTO " . $wpdb->prefix . "busy_number (date_report, count_number) VALUES 
				 (NOW(), '{$numbers->count_number}')");
}
if(isset($_GET['doing_wp_cron']) && $_GET['doing_wp_cron'] == 'count_busy_number') {
	count_busy_number();
};


<?php
/*
Plugin Name: CallTracking
Plugin URI: http://it4u.ua/
Description: CallTracking use Google Analitycs and virtual ATC for create statistic to calls.
Version: 1.0 beta
Author: IT4U
Author URI: http://it4u.ua/
*/

require_once 'admin/admin.php';
require_once "Calltracking.class.php";



register_activation_hook(__FILE__, array('CallTracking', 'install__plugin')); 
register_deactivation_hook(__FILE__, array('CallTracking', 'uninstall__plugin'));

$callTracking = new CallTracking ();

add_filter('widget_text', 'do_shortcode');
/*
require_once 'admin/issued_number.php';
require_once 'admin/busy_number.php';
require_once 'admin/waiting_time.php';
require_once 'push_call.php';


function call_tracking_install () {
	global $wpdb;
	$database_name = $wpdb->prefix . "calltracking_telephone";
	$query = "CREATE TABLE IF NOT EXISTS {$database_name} (
														  id int PRIMARY KEY auto_increment,
														  number_telephone varchar(20),
														  extension_number varchar(20), 
														  id_analytic varchar(255) NOT NULL,
														  time_active datetime NOT NULL,
														  time_expectation datetime NOT NULL)";
	$wpdb->query($query);
	$wpdb->query("CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "ip_ignore (id int PRIMARY KEY auto_increment, ip varchar(100))");
	$wpdb->query("CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "busy_number (id int PRIMARY KEY auto_increment, date_report datetime, count_number int)");
	$wpdb->query("CREATE TABLE IF NOT EXISTS `wp_issued_number` (`id` int(11) NOT NULL AUTO_INCREMENT,
																 `cookie` varchar(255),
  																 `called_did` varchar(20) NOT NULL,
  																 `caller_id` varchar(20) NOT NULL,
  																 `date_report` date NOT NULL,
  																 `issued_dynamic_number` int(1) NOT NULL,
  																 `issued_default_number` int(1) NOT NULL,
 																 `elapsed_time` varchar(20) NOT NULL,
 																 `status` int(1) NOT NULL,
  																 PRIMARY KEY (`id`))");
	
	add_option('default_number', '');
	add_option('secret', '');
	add_option('id_analytic', '');
	add_option('time_active', '00:00');
	add_option('time_expectation', '00:00');
	add_option('event', '');
	add_option('event_label', '');
	add_option('type_event', '');
	add_option('context', '');
	add_option('cost', '');
	add_option('last_parcing', date('20ymdHis', time()));
}

function call_tracking_uninstall () {
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "calltracking_telephone");
	$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "ip_ignore");
	$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "busy_number");
	$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "issued_number");

	delete_option('default_number');
	delete_option('secret');
	delete_option('id_analytic');
	delete_option('time_active');
	delete_option('time_expectation');	
	delete_option('event');
	delete_option('event_label', '');
	delete_option('type_event');
	delete_option('context');
	delete_option('cost');
	delete_option('last_parcing');
}

function liberation_phone() {
	global $wpdb;
	$wpdb->query("UPDATE " . $wpdb->prefix . "calltracking_telephone SET id_analytic = '' WHERE time_active < NOW()");
}

function check_ip($ip) {
	global $wpdb;
	$all_ip = $wpdb->get_col("SELECT ip FROM " . $wpdb->prefix . "ip_ignore");

	if(in_array($ip, $all_ip)) {
		return true;
	} 
	return false;
}

function check_cookie ( ) {
	return ($_COOKIE['_ga']) ? true : false;
}

function get_cookie ( $cookie ) {
	if(isset($cookie)) {
		return substr($cookie, 6);
	}
}

function select_number ($cookie = null) {
	global $wpdb;

	$cookie = get_cookie(($cookie != null) ? $cookie : $_COOKIE['_ga']);

	$numbers = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "calltracking_telephone WHERE id_analytic = {$cookie} AND time_active > NOW()");
	$number = ($numbers) ? $numbers->number_telephone : '';

	if(empty($number)) {
		$numbers = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "calltracking_telephone WHERE id_analytic <> {$cookie} AND time_expectation < NOW() LIMIT 1");
		$number = ($numbers) ? $numbers->number_telephone : '';
		$number_id = (!empty($number)) ? $numbers->id : '';
	} else {
		$number_id = $numbers->id;
	}
	
	if(!empty($number)) {
		$timestamp = time();
		$time = getdate($timestamp);
		$year = $time['year'];
		$month = $time['mon'];
		$day = $time['mday'];
		$hours = $time['hours'];
		$minutes = $time['minutes'];
		$seconds = $time['seconds'];
		
		$temp = mktime($hours + 3, $minutes + get_option('time_active'), $seconds, $month, $day, $year);
		$time_active = date("y-m-d H:i:s", $temp); 
		$temp = mktime($hours + 3, $minutes + get_option('time_active') + get_option('time_expectation'), $seconds, $month, $day, $year);
		$time_expectation = date("Y-m-d H:i:s", $temp);
		$wpdb->update($wpdb->prefix . 'calltracking_telephone', array('time_active' 	 => $time_active, 
														  			  'time_expectation' => $time_expectation,
														  			  'id_analytic' 	 => $cookie),
												    			array('id' 				 => $number_id));
		
			$wpdb->query("INSERT INTO " . $wpdb->prefix . "issued_number (called_did, cookie, date_report, issued_dynamic_number, status) 
																  VALUES ('{$number}', '{$cookie}', '{$time_active}', 1, 0)");

	} else {
		$number = get_option('default_number');
		$wpdb->query("INSERT INTO " . $wpdb->prefix . "issued_number (called_did, cookie, date_report, issued_default_number, status) VALUES 
				  ('{$number}', '{$cookie}', NOW(), 1, 0)");
	}
	return $number; 
}

function get_number () {
	liberation_phone();
	$ip = check_ip($_SERVER["REMOTE_ADDR"]);
	
	if(!check_cookie()) {
		wp_register_script("get_dynamic_number", plugins_url() . "/callTracking/js/get_number.js", array(), false, true);
		wp_enqueue_script("get_dynamic_number");
	}

	if($ip == false) {
		$number = select_number();
	} else 
		$number = get_option('default_number');
	
	$tmp = '+' . substr($number, 0, 1) . ' (' . substr($number, 1, 3) . ') ' . substr($number, 4, 3) . '-' . substr($number, 7, 2) . '-' . substr($number, 9, 2);
	return $tmp;
}

function create_shortcode (){
	add_shortcode('call_tracking_number', 'get_number');
}	

add_action('init', 'create_shortcode');

add_filter('widget_text', 'do_shortcode');
register_activation_hook(__FILE__, 'call_tracking_install');
register_deactivation_hook(__FILE__, 'call_tracking_uninstall');



function ajax_get_number ( ) {
	liberation_phone();
	$ip = check_ip($_SERVER["REMOTE_ADDR"]);
	
	if($ip == false) {
		$number = select_number($_POST['clientId']);
	} else 
		$number = get_option('default_number');
	
	$tmp = '+' . substr($number, 0, 1) . ' (' . substr($number, 1, 3) . ') ' . substr($number, 4, 3) . '-' . substr($number, 7, 2) . '-' . substr($number, 9, 2);
	echo $tmp;
	die;
}

add_action('wp_ajax_nopriv_get_dnumber', 'ajax_get_number');
add_action('wp_ajax_get_dnumber', 'ajax_get_number');
*/

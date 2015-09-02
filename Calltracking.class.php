<?php 

class CallTracking {
	
	public $ip = "";
	public $number = get_option('default_number');
	public $cookie = null;

public function __construct () {
	$this->update_phone_table();
}

private function update_phone_table () {
	global $wpdb;
	$wpdb->query("UPDATE " . $wpdb->prefix . "calltracking_telephone SET id_analytic = '' WHERE time_active < NOW()");
}

private function get_ip_address () {
	return $_SERVER["REMOTE_ADDR"];
}

private function check_ip_address ($ip_address) {
	global $wpdb;
	$array_ip = $wpdb->get_col("SELECT ip FROM " . $wpdb->prefix . "ip_ignore");
	return (in_array($ip_address, $array_ip)) ? true : false;
}

private function check_cookie ( ) {
	return ($_COOKIE['_ga']) ? true : false;
}

private function get_client_id ($cookie) {
	return substr($cookie, 6);
}

private function search_number_by_client_id ($client_id) {
	global $wpdb;
	$rezult = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "calltracking_telephone WHERE id_analytic = {$client_id} AND time_active > NOW()");
	return ($rezult) ? array("number" => $rezult->number, "number_id" => $rezult->id) : false;
}

private function search_free_phones () {
	global $wpdb;
	$rezult = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "calltracking_telephone WHERE id_analytic <> {$cookie} AND time_expectation < NOW() LIMIT 1");
	return ($rezult) ? array("number" => $rezult->number, "number_id" => $rezult->id) : false;
}

private function save_log_dynamic_number ($cookie, $number_id) {

}

private function save_log_default_number ($cookie, $number_id) {
	global $wpdb;
	$wpdb->query("INSERT INTO " . $wpdb->prefix . "issued_number (called_did, cookie, date_report, issued_default_number, status) VALUES 
				  ('{$number}', '{$cookie}', NOW(), 1, 0)");
}

}
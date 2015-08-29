<?php

function count_busy_number () {
	global $wpdb;
	$numbers = $wpdb->get_row("SELECT COUNT(id) as count_number FROM " . $wpdb->prefix . "calltracking_telephone WHERE id_analytic <> '' AND time_expectation > NOW()");
	$wpdb->query("INSERT INTO " . $wpdb->prefix . "busy_number (date_report, count_number) VALUES 
				 (NOW(), '{$numbers->count_number}')");
}

function push_call_ () {
	global $wpdb;
	$telephones = $wpdb->get_results("SELECT * FROM wp_calltracking_telephone");
	
	$caller_id = $_POST['caller_id'];
	$called_did = $_POST['called_did'];
	$callstart = $_POST['callstart'];

	$body = "Начало звонка: " . $callstart . " Номер звонящего: " . $caller_id . "Номер на который звонят:" . $called_did;

	$v = "v=1";
	$tid = "&tid=" . get_option('id_analytic');
	$t = "&t=" . get_option('type_event');
	$ec = "&ec=" . get_option('context');
	$ea = "&ea=" . get_option('event');
	$el = "&el=" . get_option('event_label');
	$ev = "&ev=" . get_option('cost');

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

	foreach ($telephones as $value) {
		if($value->number_telephone == $called_did && !empty($value->id_analytic)) {
			$wpdb->query("UPDATE " . $wpdb->prefix . "calltracking_telephone SET time_active = '{$time_active}', time_expectation = '{$time_expectation}' WHERE id_analytic = '{$value->id_analytic}'");
			$google_url = "http://www.google-analytics.com/collect?" . $v . $tid . '&cid=' . $value->id_analytic . $t . $ec . $ea . $el . $ev;			
		
			$temp = mktime($hours + 3, $minutes, $seconds, $month, $day, $year);
			$current_time = date("y-m-d H:i:s", $temp);

			$timestamp = strtotime($value->time_active);
			$time = getdate($timestamp);
			$year = $time['year'];
			$month = $time['mon'];
			$day = $time['mday'];
			$hours = $time['hours'];
			$minutes = $time['minutes'];
			$seconds = $time['seconds'];
			$temp = mktime($hours, $minutes - get_option('time_active'), $seconds, $month, $day, $year);
			$start_hit = date("y-m-d H:i:s", $temp);

			$elapsed_time = date("H:i:s", strtotime($current_time) - strtotime($start_hit));
			$wpdb->query("UPDATE " . $wpdb->prefix . "issued_number SET caller_id = '{$caller_id}', elapsed_time = '{$elapsed_time}', status = 1 WHERE called_did = '{$value->number_telephone}'");
			$body .= " " . $google_url;
			file_get_contents($google_url);
			break;
		}
	}

	mail("a@it4u.ua", "sv@computers.net.ua", "callTracking", $body);
}

if(isset($_POST['caller_id'])) {
	push_call_();
};

if(isset($_GET['doing_wp_cron']) && $_GET['doing_wp_cron'] == 'count_busy_number') {
	count_busy_number();
};

?>
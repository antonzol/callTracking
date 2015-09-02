<?
function add_page_waiting () {
	add_submenu_page('call_tracking', 'Статистика по времени звонка после выдачи', 'Когда звонят?', 'manage_options', 'calltracking_waiting_time', 'create_waiting_time');
}
function create_waiting_time () {
	global $wpdb;
	
	$waiting_time = $wpdb->get_results("SELECT date_report, elapsed_time FROM `wp_issued_number` WHERE DATE( date_report ) > DATE( NOW( ) - INTERVAL 30 DAY ) AND status = 1");

	$timestamp = time();
	$time = getdate($timestamp);
	$year = $time['year'];
	$month = $time['mon'];
	$day = $time['mday'];
	$hours = $time['hours'];
	$minutes = $time['minutes'];
	$seconds = $time['seconds'];

	echo date("d.m", strtotime("2015-08-10"));

	$array_result = array();

	for($i = 0; $i < 30; $i++){
		$temp = mktime($hours, $minutes, $seconds, $month, $day, $year);
		$array_result[date("d.m", $temp)]['avg'] = 0;
		$array_result[date("d.m", $temp)]['count'] = 0;
		$array_result[date("d.m", $temp)]['max'] = 0;
		$day--;
	}

	foreach ($waiting_time as $key => $value) {
		$k = date("d.m", strtotime($value->date_report));
		preg_match_all("/\d{2}/", $value->elapsed_time, $temp);
		
		$hours = (int)($temp[0][0]);
		$min = (int)($temp[0][1]);
		$sec = (int)($temp[0][2]);
		
		$time = $hours * 3600 + $min * 60 + $sec;
		
		$array_result[$k]['avg'] += $time;
		$array_result[$k]['count']++;
		if ($time > $array_result[$k]['max']) {
			$array_result[$k]['max'] = $time;
		}
		
	}
	
?>

<style>
	.table_reports {
		margin-bottom: 30px;
		text-align: center;
	}
</style>
<div class="wrap">
	<h2>Статистика времени за последние 30 дней :</h2>
</div>
<div>
<?php if($waiting_time) : ?>
	<table class="wp-list-table widefat striped pages table_reports" style="width: 500px;">
		<tr>
			<th style="text-align:center;">Дата</th>
			<th style="text-align:center;">Среднее</th>
			<th style="text-align:center;">MAX</th>				
		</tr>
		<?php foreach ($array_result as $key => $value) : ?>
		<tr>
			<td><?php echo $key; ?></td>

			<?php 
				if($value['avg'] != 0 && $value['count'] != 0) {
					$tmp_avg = $value['avg'] / $value['count'];
					$h = 0;
					$m = 0;
					$s = 0;
				}
			?>
			<td><?php echo ($value['avg'] != 0 && $value['count'] != 0) ? $value['avg'] / $value['count'] : "00:00:00"; ?></td>
			

			<td><?php echo $value['max']; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
<?php 

}

add_action('admin_menu', 'add_page_waiting');

<?
function add_page_report () {
	add_options_page('Статистика по Call Tracking','Статистика', 8, 'call_tracking_reports', 'create_tracking_reports');
}
function create_tracking_reports () {
	global $wpdb;
	$issued_numbers_default = $wpdb->get_row("SELECT COUNT(DISTINCT(cookie)) as c FROM " . $wpdb->prefix . "issued_number WHERE issued_default_number = 1 AND DATE(date_report) = DATE(NOW() - INTERVAL 1 DAY)"); 
	$issued_numbers_dynamic = $wpdb->get_row("SELECT COUNT(DISTINCT(cookie)) as c FROM " . $wpdb->prefix . "issued_number WHERE issued_dynamic_number = 1 AND DATE(date_report) = DATE(NOW() - INTERVAL 1 DAY)"); 
	$busy_number = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "busy_number ORDER BY date_report DESC LIMIT 100");
	$calls = $wpdb->get_results("SELECT DISTINCT cookie, caller_id, called_did, date_report, elapsed_time
								 FROM " . $wpdb->prefix . "issued_number WHERE status = 1
								 ORDER BY date_report DESC 
								 LIMIT 100");
?>
	<style>
		.table_reports {
			margin-bottom: 30px;
			text-align: center;
		}
	</style>
	<div class="wrap">
		<h2>Статистика по Call Tracking</h2>
	</div>
	<div class="">
		<?php if(!empty($issued_numbers_default) || !empty($issued_numbers_dynamic)) : ?>
		<p style="font-size: 18px; margin: 10px 0;">Выдача номеров за прошедшые сутки</p>
		<table class="wp-list-table widefat fixed striped pages table_reports" style="width: 500px;">
			<tr>
				<th style="text-align:center;">Динамических номеров выдано:</th>
				<th style="text-align:center;">Номер по умолчанию:</th>
			</tr>
			<tr>
				<td><?php echo $issued_numbers_dynamic->c; ?></td>
				<td><?php echo $issued_numbers_default->c; ?></td>
			</tr>
		</table>
		<?php endif; ?>

		<?php if($busy_number) : ?>
		<p style="font-size: 18px; margin: 10px 0;">Количество занятых номеров: </p>
		<table class="wp-list-table widefat fixed striped pages table_reports" style="width: 500px;">
			<tr>
				<th style="text-align:center;">Время</th>
				<th style="text-align:center;">Количество:</th>
			</tr>
			<?php foreach ($busy_number as $key => $value) : ?>	
			<?php 
				$current_time = explode(' ', $value->date_report);
			?>
			<tr>
				<td><?php echo $current_time[1]; ?></td>
				<td><?php echo $value->count_number; ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php endif; ?>

		<p style="font-size: 18px;">Информация о последних звонках:</p>
		<?php if($calls) : ?>
		<table class="wp-list-table widefat fixed striped pages" style="width: 90%;">
			<tr>
				<th>Исходящий номер</th>
				<th>Входящий номер</th>
				<th>Cookie</th>
				<th>Время после первого хита</th>
				<th>Дата</th>
			</tr>
			<?php foreach ($calls as $key => $value) : ?>	
			<tr>
				<td><?php echo $value->caller_id; ?></td>
				<td><?php echo $value->called_did; ?></td>
				<td><?php echo $value->cookie; ?></td>
				<td><?php echo $value->elapsed_time; ?></td>
				<td><?php echo $value->date_report; ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php endif; ?>
		<?php if(!$calls) : ?>
			<p style="font-size: 18px;">Звонков нет:</p>
		<?php endif; ?>

	</div>
<?php
}

add_action('admin_menu', 'add_page_report');

<?php 

function add_admin_page () {
	add_options_page('Настройки Call Tracking','Call Tracking', 8, 'call_tracking', 'create_options_page');
	add_options_page('Фильтр по IP','Фильтр по IP', 8, 'filter_ip', 'create_options_ip');

	add_option('default_number', 'Не задано');
	add_option('id_analytics', 'Не задано');
	add_option('time_active', '0');
	add_option('time_expectation', '0');
}

function create_options_ip() {
	global $wpdb;
	if(isset($_POST['submit_add_ip'])) {
		$wpdb->insert($wpdb->prefix . 'ip_ignore', array('ip' => trim($_POST['ignor_ip'])));
	}
	if(isset($_POST['delete_ip'])) {
		$wpdb->delete($wpdb->prefix . 'ip_ignore', array( 'id' => trim($_POST['id_ip'])));
	}
	$data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ip_ignore");
?>
<div class="wrap">
	<h2>Добавление IP в фильтр</h2>
	<div class="main_setting" style='max-width: 600px; padding: 10px 20px 20px; border: 1px solid #DADADA; margin-top: 20px;'>
		<form method='POST' action='<?php echo $_SERVER['PHP-SELF']; ?>?page=filter_ip&amp;update=true'>
			<table class="form-table">
				<tr>
					<td><label for="ignor_ip">IP:</label></td>
					<td><input type="text" name="ignor_ip" id="ignor_ip" class="regular-text"></td>
					<td><input type="submit" name="submit_add_ip" id="submit" class="button button-primary" value="Добавить"></td>
				</tr>	
			</table>
		</form>
	</div>
	<div class="main_setting" style='max-width: 600px; padding: 10px 20px 20px; border: 1px solid #DADADA; margin-top: 20px;'>
		<table class="wp-list-table widefat fixed striped pages">
			<tr>
				<td>IP</td>
				<td></td>
			</tr>
			<?php foreach ($data as $value) { ?>	
			<tr>
				<td>
					<?php echo $value->ip; ?>
				</td>
				<td>
					<form method='POST' action='<?php echo $_SERVER['PHP-SELF']; ?>?page=filter_ip&amp;update=true'>
					<input type="hidden" name='id_ip' value='<?php echo $value->id; ?>'>
					<input type='submit' value='Удалить' name='delete_ip' class='button button-primary'>
					</form>
				</td>
			</tr>
			<?php } ?>
		</table>
		</form>
	</div>
<?php 
}

function create_options_page() {
if(isset($_POST['submit_default_setting'])) {

	$default_number = $_POST['default_number'];
	$secret = $_POST['secret'];
	$id_analytic = $_POST['id_analytic'];
	$time_active = $_POST['time_active'];
	$time_expectation = $_POST['time_expectation'];
	$event = $_POST['event'];
	$type_event = $_POST['type_event'];
	$context = $_POST['context'];
	$cost = $_POST['cost'];
	$event_label = $_POST['event_label'];

	update_option('default_number', $default_number);
	update_option('secret', $secret);
	update_option('id_analytic', $id_analytic);
	update_option('time_active', $time_active);
	update_option('time_expectation', $time_expectation);
	update_option('event', $event);
	update_option('type_event', $type_event);
	update_option('context', $context);
	update_option('cost', $cost);
	update_option('event_label', $event_label);
}
?>
	<div class="wrap">
		<h2>Настройки Call Tracking</h2>
		<div class="main_setting" style='max-width: 500px; padding: 10px 20px 20px; border: 1px solid #DADADA; margin-top: 20px; float:left;'>
			<p style="font-size: 15px;">Настройки общих параметров:</p>
			<form method='POST' action='<?php echo $_SERVER['PHP-SELF']; ?>?page=call_tracking&amp;update=true'>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="default_number">Номер по умолчанию</label></th>
					<td>
						<input type="text" name="default_number" id="default_number" class="regular-text" 
							   value="<?php echo get_option('default_number');?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="secret">Секрет:</label></th>
					<td>
						<input type="text" name="secret" id="secret" class="regular-text" 
							   value="<?php echo get_option('secret');?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="secret">ID аналитики:</label></th>
					<td>
						<input type="text" name="id_analytic" id="id_analytic" class="regular-text" 
							   value="<?php echo get_option('id_analytic');?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="time_active">Время активности номеров (минуты):</label></th>
					<td>
						<input type="text" name="time_active" id="time_active" class="regular-text"
							   value="<?php echo get_option('time_active');?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="time_expectation">Время ожидания номеров (минуты):</label></th>
					<td>
						<input type="text" name="time_expectation" id="time_expectation" class="regular-text"
						       value="<?php echo get_option('time_expectation');?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="type_event">Тип события:</label></th>
					<td>
						<input type="text" name="type_event" id="type_event" class="regular-text" 
							   value="<?php echo get_option('type_event');?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="context">Контекст:</label></th>
					<td>
						<input type="text" name="context" id="context" class="regular-text" 
							   value="<?php echo get_option('context');?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="event">Событие:</label></th>
					<td>
						<input type="text" name="event" id="event" class="regular-text" 
							   value="<?php echo get_option('event');?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="event_label">Метка событие:</label></th>
					<td>
						<input type="text" name="event_label" id="event_label" class="regular-text" 
							   value="<?php echo get_option('event_label');?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cost">Стоимость:</label></th>
					<td>
						<input type="text" name="cost" id="cost" class="regular-text" 
							   value="<?php echo get_option('cost');?>">
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit_default_setting" id="submit" class="button button-primary" value="Сохранить">
			</p>
			</form>
		</div>
<?php 	

global $wpdb;  	
if(isset($_POST['submit_telephone'])){
	$wpdb->insert($wpdb->prefix . 'calltracking_telephone', array('number_telephone' => trim($_POST['number_telephone'])));
}

?>
		<div class="setting_talephone" style='max-width: 600px; padding: 10px 20px 20px; border: 1px solid #DADADA; margin: 20px 0 0 20px; float:left;'>
			<p style="font-size: 15px;">Добавление новых телефонов:</p>
			<form method='POST' action='<?php echo $_SERVER['PHP-SELF']; ?>?page=call_tracking&amp;update=true'>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="number_telephone">Номер:</label></th>
					<td>
						<input type="text" name="number_telephone" id="number_telephone" class="regular-text" 
							   value="">
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit_telephone" id="submit" class="button button-primary" value="Добавить">
			</p>
			</form>
		</div>
		
		<div style='max-width: 600px; padding: 10px 20px 20px; margin: 20px 0 0 20px; float:left;'>
			<h3 class="title">Описание</h3>
			<p>Для того что бы вывести телефоны в шаблон. Вставте следеющую строку - [call_tracking_number].</p>
		</div>

		<div style='clear:both;'></div>
<?php 
	if(isset($_POST['delete_phone'])) {
		$wpdb->delete($wpdb->prefix . 'calltracking_telephone', array('id' => trim($_POST['number_id'])));
	}
	$data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "calltracking_telephone");
?>
		<div class="number_table" style='padding: 10px 20px 20px; border: 1px solid #DADADA; margin-top: 20px;'>
			<p style="font-size: 15px; text-align: center;">Список активности:</p>
			
			<table class="wp-list-table widefat fixed striped pages">
				<thead>
					<th>ID</th>
					<th>Номер</th>
					<th>Номер сессии</th>
					<th>Время сессии до</th>
					<th>Время ожидания до</th>
					<th></th>
				</thead>
				<tbody>
					<?php foreach ($data as $value) { ?>
					<tr>
						<td>
							<?php echo $value->id; ?>
						</td>
						<td><?php echo $value->number_telephone; ?></td>
						<td><?php echo $value->id_analytic ?></td>
						<td><?php echo $value->time_active ?></td>
						<td><?php echo $value->time_expectation ?></td>
						<td>
							<form method='POST' action='<?php echo $_SERVER['PHP-SELF']; ?>?page=call_tracking&amp;update=true'>
							<input type="hidden" name="number_id" value="<?php echo $value->id; ?>">
							<input type='submit' value='Удалить' name='delete_phone' class='button button-primary'>
							</form>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			</form>
		</div>
	</div>
<?php
}
	
add_action('admin_menu', 'add_admin_page');

?>
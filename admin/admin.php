<?php 

function add_admin_page () {
	add_menu_page('Настройки Call Tracking','Call Tracking', 'manage_options', 'call_tracking', 'create_options_page', 'dashicons-phone', 80);
	add_submenu_page('call_tracking','Фильтр по IP', 'Фильтр IP', 'manage_options', 'filter_ip', 'create_options_ip');
	add_submenu_page('call_tracking','Статистика', 'Статистика', 'manage_options', 'statistic', 'create_options_statistic');

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

	if(isset($_POST['submit_add_my_ip'])) {
		$wpdb->insert($wpdb->prefix . 'ip_ignore', array('ip' => trim($_SERVER["REMOTE_ADDR"])));
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
					<td>
						<input type="submit" name="submit_add_ip" id="submit" class="button button-primary" value="Добавить">
					</td>
				</tr>	
			</table>
		</form>
		<form method='POST' action='<?php echo $_SERVER['PHP-SELF']; ?>?page=filter_ip&amp;update=true'><input type="submit" name="submit_add_my_ip" id="submit" class="button button-primary" value="Добавить свой"></form>
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
			<p>Для того что бы вывести телефоны в шаблон. Вставте следеющую строку - [call_tracking_number], также даний код должен быть обернут в тег с атрибутом itemprop="telephone".</p>
		</div>

		<div style='clear:both;'></div>
<?php 
	if(isset($_POST['delete_phone'])) {
		$wpdb->delete($wpdb->prefix . 'calltracking_telephone', array('id' => trim($_POST['number_id'])));
	}
	$data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "calltracking_telephone");
?>
		<div class="number_table" style='padding: 10px 20px 20px; border: 1px solid #DADADA; margin-top: 20px;'>
			<p style="font-size: 15px;">Список активности:</p>
			
			<table class="wp-list-table widefat striped pages" style="width: 800px">
				<thead>
					<th width="30px">ID</th>
					<th width="100px">Номер</th>
					<th width="160px">Номер сессии</th>
					<th width="140px">Время сессии до</th>
					<th width="140px">Время ожидания до</th>
					<th width="80px"></th>
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

function getIssuetDataArray ($daysAgo) {
	$countDay = ($daysAgo != 1) ? $daysAgo - 1 : 1;
	global $wpdb;
	$numbers = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "issued_number WHERE DATE( date_report ) > DATE( NOW( ) - INTERVAL 30 DAY )", OBJECT_K);

	foreach ($numbers as $key => $number) {
		$number->date_report = date("d.m", strtotime($number->date_report));
	}

	for($i = $countDay; $i >= 0; $i--) {
		$tempTime = date("d.m", time() - 86400 * $i);
		$static[$tempTime]['dinamic'] = 0;
		$static[$tempTime]['default'] = 0;
	}

	foreach ($static as $key => $n) {
		foreach ($numbers as $k => $v) {
			if ($key === $v->date_report && $v->issued_dynamic_number === '1') {
				$static[$key]['dinamic'] += 1;  
			}
			if ($key === $v->date_report && $v->issued_dynamic_number === '0') {
				$static[$key]['default'] += 1;  
			}
		}
	}

	return $static;
}

function getBusyNumbersDataArray () {
	global $wpdb;
	$busy_number = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "busy_number WHERE DATE( date_report ) > DATE( NOW( ) - INTERVAL 30 DAY ) ORDER BY date_report ASC", OBJECT_K);
	
	$result = array();
	for ($i = 0; $i < 144; $i++) {
		$tempTime = date("H:i", strtotime("2015-09-29 00:00:00") + (60 * $i * 10));
		$result[$tempTime]['count'] = 0;
		$result[$tempTime]['summa'] = 0;	
		$result[$tempTime]['max'] = 0;
	}

	foreach ($busy_number as $key => $value) {
		$tmp = date("H:i", strtotime($value->date_report));
		$result[$tmp]['count'] += 1;
		$result[$tmp]['summa'] += $value->count_number;
		$result[$tmp]['max'] = ($result[$tmp]['max'] < $value->count_number) ? $value->count_number : $result[$tmp]['max'];
	}

	return $result;
}

function getWaitingTimeDataArray () {
	global $wpdb;
	$waitingTime = $wpdb->get_results("SELECT date_report, elapsed_time FROM `" . $wpdb->prefix . "issued_number` WHERE DATE( date_report ) > DATE( NOW( ) - INTERVAL 30 DAY ) AND status = 1");
	$result = array();

	foreach ($waitingTime as $key => $val) {
		$val->date_report = date("d.m", strtotime($val->date_report));
	}

	for($i = 29; $i >= 0; $i--) {
		$tempTime = date("d.m", time() - 86400 * $i);
		$result[$tempTime]['avg'] = 0;
		$result[$tempTime]['max'] = 0;
		$result[$tempTime]['count'] = 0;
	}
	
	foreach ($waitingTime as $key => $value) {
		$tmp = $value->date_report;
		preg_match_all("/\d{2}/", $value->elapsed_time, $temp);
		$hours = (int)($temp[0][0]);
		$min = (int)($temp[0][1]);
		$sec = (int)($temp[0][2]);
		$time = $hours * 3600 + $min * 60 + $sec;

		$result[$tmp]['avg'] += $time;
		$result[$tmp]['count'] += 1;
		if ($time > $result[$tmp]['max']) {
			$result[$tmp]['max'] = $time;
		}

	}

	return $result;
}

function create_options_statistic () {
?>
	<script src="<?php echo plugins_url(); ?>/callTracking/js/Chart.min.js"></script>
	<?php $issuedData = getIssuetDataArray(30); ?>
	<?php $busyNumber = getBusyNumbersDataArray(); ?>
	<?php $waitingTime = getWaitingTimeDataArray(); ?>

	<ul class="tabs-menu">
		<?php if($issuedData) : ?><li><a href="#" class="menu__item menu__item--active" data="0">Выдача номеров</a></li><?php endif; ?>
		<?php if($busyNumber) : ?><li><a href="#" class="menu__item" data="1">Занятые номера</a></li><?php endif; ?>
		<?php if($waitingTime) : ?><li><a href="#" class="menu__item" data="2">Когда звонят ?</a></li><?php endif; ?>
	</ul>
	
	<?php if($issuedData) : ?>
	<div class="tabs-container">
		<div class="tabs-container__item tabs-container__item--active">
			<div class="wrap">
				<h2>Статистика по выдаче номеров:</h2>
				<canvas id="issuedNumber" style="width:100%; height:500px;"></canvas>
				<p>Показать за</p>
				<div class="buttons__switch">
					<span class="buttons__item buttons__item--active" data-item="30">Месяц</span>
					<span class="buttons__item" data-item="14">14 дней</span>
					<span class="buttons__item" data-item="7">7 дней</span>
					<span class="buttons__item" data-item="1">2 дня</span>
				</div>
				<script>
					var options = {
	    				scaleShowGridLines : true,
						scaleGridLineColor : "rgba(0,0,0,.05)",
    					scaleGridLineWidth : 1,
    					scaleShowHorizontalLines: true,
    					scaleShowVerticalLines: true,
    					bezierCurve : true,
    					bezierCurveTension : 0.4,
    					pointDot : true,
    					pointDotRadius : 4,
    					pointDotStrokeWidth : 1,
    					pointHitDetectionRadius : 20,
    					datasetStroke : true,
    					datasetStrokeWidth : 2,
    					datasetFill : true,
    					legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
					};
		
					var ctx = document.getElementById("issuedNumber").getContext("2d");
					var data = {
    					labels: [],
    					datasets: [
        				{
        			    	label: "Статические номера",
        			    	fillColor: "rgba(220,220,220,0.2)",
        			    	strokeColor: "rgba(220,220,220,1)",
        			    	pointColor: "rgba(220,220,220,1)",
        			    	pointStrokeColor: "#fff",
        			    	pointHighlightFill: "#fff",
        			    	pointHighlightStroke: "rgba(220,220,220,1)",
        			    	data: []
        				},
        				{
        			    	label: "Динамические номера",
        			    	fillColor: "rgba(151,187,205,0.2)",
        			    	strokeColor: "rgba(151,187,205,1)",
        			    	pointColor: "rgba(151,187,205,1)",
        			    	pointStrokeColor: "#fff",
        			    	pointHighlightFill: "#fff",
        			    	pointHighlightStroke: "rgba(151,187,205,1)",
        			    	data: []
        				}
    					]
					};

					var dataNumber = <?php echo json_encode($issuedData); ?>,
						labelsDate = [],
						dataDinamic = [],
						dataDefault = [];

					for (key in dataNumber) {
						labelsDate.push(key);
						dataDinamic.push(dataNumber[key]['dinamic']);
						dataDefault.push(dataNumber[key]['default']);
					}

					data.labels = labelsDate;
					data.datasets[0]['data'] = dataDinamic;
					data.datasets[1]['data'] = dataDefault;
					
					var myLineChart = new Chart(ctx).Line(data, options);
		
					var changeChart = function (dataChart) {
						labelsDate = [],
						dataDinamic = [],
						dataDefault = [];
						for (key in dataChart) {
							labelsDate.push(key);
							dataDinamic.push(dataChart[key]['dinamic']);
							dataDefault.push(dataChart[key]['default']);
						}
						data.labels = labelsDate;
						data.datasets[0]['data'] = dataDinamic;
						data.datasets[1]['data'] = dataDefault;
						myLineChart.destroy();
						myLineChart = new Chart(ctx).Line(data, options);
					}

					jQuery(".buttons__switch").on('click',function(e){
						if(jQuery(e.target).hasClass('buttons__item--active')) 
							return;
						jQuery(e.target).toggleClass('buttons__item--active').siblings().removeClass("buttons__item--active");
						var d = e.target.getAttribute('data-item');
						jQuery.ajax({
       						url: '/wp-admin/admin-ajax.php',
	    					data: {
	    						data: d,
	    						action: 'changeViewChart'
	    					},
	    					type: 'POST',
     						success:function(data){
        						if(data){
									changeChart(JSON.parse(data));
								}
        						else {
        							alert("Нажмите еще раз");
        						}	 
       						}	
    					});
					});
				</script>
				
				<table class="wp-list-table widefat striped pages table_reports">
				<tr>
					<td>
						<table>
							<tr><th>Дата:</th></tr>
							<tr><th>Динамические:</th></tr>
							<tr><th>Статические:</th></tr>
						</table>
					</td>
					<?php foreach ($issuedData as $key => $data) : ?>
					<td>
						<table>
							<tr>
								<td><?php echo $key;?></td>
							</tr>
							<tr style="background-color: rgba(151,187,205,0.2);">
								<td><?php echo $data['dinamic'];?></td>
							</tr>
							<tr style="background-color: rgba(220,220,220,0.2);">
								<td><?php echo $data['default'];?></td>
							</tr>   
						</table>
					</td>
					<?php endforeach; ?>
				</tr>
				</table>
			</div>		
		</div>
		<?php endif; ?>
		<?php if($busyNumber) : ?>
		<div class="tabs-container__item">
			<div class="wrap">
				<h2>Статистика занаятости номеров с групировкой каждые (10 мин) за последие 30 дней:</h2>
			</div>
			<canvas id="busyNumber" style="width:100%; height:500px;"></canvas>
			<script>
					var busyArray = <?php echo json_encode($busyNumber); ?>,
						busyArrayLabels = [],
						busyArrayData0 = [],
						busyArrayData1 = [];

					var i = 0;
					for (key in busyArray) {
						if (i % 6 == 0)
							busyArrayLabels.push(key);
						busyArrayData0.push(busyArray[key]['max']);
						busyArrayData1.push((busyArray[key]['summa'] != 0 && busyArray[key]['count']) ? busyArray[key]['summa'] / busyArray[key]['count'] : 0);
						i++;
					}
					var ctxBusyNumber = document.getElementById("busyNumber").getContext("2d");
					var dataBusyNumber = {
					    labels: busyArrayLabels,
					    datasets: [
					        {
					            label: "Максимум",
					            fillColor: "rgba(151,187,205,0.2)",
					            strokeColor: "rgba(151,187,205,1)",
					            pointColor: "rgba(151,187,205,1)",
					            pointStrokeColor: "#fff",
					            pointHighlightFill: "#fff",
					            pointHighlightStroke: "rgba(151,187,205,1)",
					            data: busyArrayData0
					        },
					        {
        			    		label: "Среднее",
        			    		fillColor: "rgba(255,0,0,0.2)",
        			    		strokeColor: "rgba(255,0,0,0.5)",
        			    		pointColor: "rgba(220,220,220,1)",
        			    		pointStrokeColor: "#fff",
        			    		pointHighlightFill: "#fff",
        			    		pointHighlightStroke: "rgba(220,220,220,1)",
        			    		data: busyArrayData1
        					},
					    ]
					};

					var optionsBusy = options;
					optionsBusy.showTooltips = false;
					optionsBusy.scaleGridLineWidth = 0;
					optionsBusy.pointDot = false;
					var busyNumberChart = new Chart(ctxBusyNumber).Line(dataBusyNumber, optionsBusy);
			</script>
			<p style="font-size: 18px; margin: 10px 0;">Количество занятых номеров: </p>
			<table class="wp-list-table widefat striped pages table_reports" style="width: 12000px;">
				<tr>
					<td>
						<table>
							<tr><th>Время:</th></tr>
							<tr><th>Среднее:</th></tr>
							<tr><th>MAX:</th></tr>
						</table>
					</td>
					<?php foreach ($busyNumber as $key => $value) : ?>
					<td>
						<table>
							<tr>
								<td><?php echo  $key; ?></td>
							</tr>
							<tr>
								<td style="background-color:rgba(255,0,0,0.5);"><?php echo ($value['summa'] != 0 && $value['count'] != 0) ? $value['summa'] / $value['count'] : 0; ?></td>
							</tr>
							<tr>
								<td style="background-color:rgba(151,187,205,1);"><?php echo $value['max']; ?></td>
							</tr>
						</table>
					</td>
					<?php endforeach; ?>
				</tr>
			</table>
		</div>
		<?php endif; ?>
		<?php if($waitingTime) : ?>
		<div class="tabs-container__item">
			<div class="wrap">
				<h2>Статистика времени за последние 30 дней :</h2>
			</div>
			<table class="wp-list-table widefat striped pages table_reports" style="width: 500px;">
				<tr>
					<th style="text-align:center;">Дата</th>
					<th style="text-align:center;">Среднее</th>
					<th style="text-align:center;">MAX</th>				
				</tr>
				<?php foreach ($waitingTime as $key => $value) : ?>
				<tr>
					<td><?php echo $key; ?></td>
					<?php 
					if($value['avg'] != 0 && $value['count'] != 0) {
						$avg =  date("H:i:s", mktime(0, 0, $value['avg'] / $value['count']));
					} else {
						$avg = "00:00";
					}
					?>
					<td><?php echo $avg; ?></td>
					<td><?php echo date("H:i:s", mktime(0, 0, $value['max'])); ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>
		<?php endif;?>
	</div>
	<style>
		.tabs-menu {
			display: inline-block;
			background-color: #23282D;
		}
		.tabs-menu > li {
			display: inline-block;
			color: #fff;
			margin: 0;
		}
		.menu__item {
			display: inline-block;
			padding: 10px 20px;
			font-size: 16px;
			text-transform: uppercase;
			color: #fff;
			text-decoration: none;
		}
		.menu__item--active {
			background-color: #0073AA;
		}
		.tabs-container__item {
			display: none;
		}
		.tabs-container__item--active {
			display: block;
		}
	</style>
	<script>
		(function(){
			var tabsMenu = document.querySelectorAll('.tabs-menu')[0],
				tabsItem = document.querySelectorAll('.tabs-container__item'),
				tabsMenuItem = tabsMenu.querySelectorAll('.menu__item');
			
			tabsMenu.addEventListener("click", function(e){
				e.preventDefault();
				var current = e.target,
					numberItem = current.getAttribute('data');
				if(numberItem) {
					for(var i = 0; i < tabsItem.length; i++) {
						tabsItem[i].classList.remove('tabs-container__item--active');
					}
					for(var i = 0; i < tabsMenuItem.length; i++) {
						tabsMenuItem[i].classList.remove('menu__item--active');
					}
					tabsItem[numberItem].classList.add('tabs-container__item--active');
					tabsMenuItem[numberItem].classList.add('menu__item--active');
				}
			}, false);
		})();
	</script>
<?php
}

	
add_action('admin_menu', 'add_admin_page');


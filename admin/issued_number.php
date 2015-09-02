<?
function add_page_issued () {
	add_submenu_page('call_tracking', 'Статистика по выданим номерам', 'Выдача номеров', 'manage_options', 'calltracking_issued_number', 'create_issued_page');
}
function create_issued_page () {
	global $wpdb;
	
	$issued_dynamic_number = $wpdb->get_results("SELECT date_report, COUNT( DISTINCT (cookie) ) AS c
												FROM wp_issued_number
												WHERE issued_dynamic_number = 1
												AND DATE( date_report ) > DATE( NOW( ) - INTERVAL 30 DAY ) 
												GROUP BY date_report, issued_dynamic_number", OBJECT_K);

	$issued_numbers_default = $wpdb->get_results("SELECT date_report, COUNT( DISTINCT (cookie) ) AS c
												  FROM wp_issued_number
												  WHERE issued_default_number = 1
												  AND DATE( date_report ) > DATE( NOW( ) - INTERVAL 30 DAY ) 
											      GROUP BY date_report, issued_dynamic_number", OBJECT_K);

	$timestamp = time();
	$time = getdate($timestamp);
	$year = $time['year'];
	$month = $time['mon'];
	$day = $time['mday'];
	$hours = $time['hours'];
	$minutes = $time['minutes'];
	$seconds = $time['seconds'];
	
	$label_date = array();
	$cound_dynamic = array();
	$cound_default = array();

	for($i = 0; $i < 25; $i++){
		$temp = mktime($hours, $minutes, $seconds, $month, $day, $year);
		$label_date[] = date("d.m", $temp);
		$temp_date = date("Y-m-d", $temp);
		$cound_dynamic[] = ($issued_dynamic_number[$temp_date]) ? $issued_dynamic_number[$temp_date]->c : 0 ;
		$cound_default[] = ($issued_numbers_default[$temp_date]) ? $issued_numbers_default[$temp_date]->c : 0 ;
		$day--;
	}
?>
	<style>
		.table_reports {
			margin-bottom: 30px;
			text-align: center;
		}
	</style>
	<div class="wrap">
		<h2>Статистика по выдаче номеров:</h2>
	</div>
	<div style="width: 90%; padding: 0 50px;">
			
		<script src="<?php echo plugins_url(); ?>/callTracking/js/Chart.min.js"></script>
		<canvas id="myChart" style="width:100%; height:500px;"></canvas>
		<script>
			var ctx = document.getElementById("myChart").getContext("2d");
var data = {
    labels: ['<?php echo join("', '", array_reverse($label_date)); ?>'],
    datasets: [
        {
            label: "Статические номера",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: ['<?php echo join("', '", array_reverse($cound_default)); ?>',]
        },
        {
            label: "Динамические номера",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: ['<?php echo join("', '", array_reverse($cound_dynamic)); ?>']
        }
    ]
};

var options = {

    ///Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines : true,

    //String - Colour of the grid lines
    scaleGridLineColor : "rgba(0,0,0,.05)",

    //Number - Width of the grid lines
    scaleGridLineWidth : 1,

    //Boolean - Whether to show horizontal lines (except X axis)
    scaleShowHorizontalLines: true,

    //Boolean - Whether to show vertical lines (except Y axis)
    scaleShowVerticalLines: true,

    //Boolean - Whether the line is curved between points
    bezierCurve : true,

    //Number - Tension of the bezier curve between points
    bezierCurveTension : 0.4,

    //Boolean - Whether to show a dot for each point
    pointDot : true,

    //Number - Radius of each point dot in pixels
    pointDotRadius : 4,

    //Number - Pixel width of point dot stroke
    pointDotStrokeWidth : 1,

    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
    pointHitDetectionRadius : 20,

    //Boolean - Whether to show a stroke for datasets
    datasetStroke : true,

    //Number - Pixel width of dataset stroke
    datasetStrokeWidth : 2,

    //Boolean - Whether to fill the dataset with a colour
    datasetFill : true,

    //String - A legend template
    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"

};
	var myLineChart = new Chart(ctx).Line(data, options);
</script>

		<?php if(!empty($issued_dynamic_number)) : ?>
		<p style="font-size: 18px; margin: 10px 0;">Выдача номеров за последние 25 дней</p>
		<table class="wp-list-table widefat striped pages table_reports">
			<tr>
				<td>
					<table>
						<tr><th>Дата:</th></tr>
						<tr><th>Динамические:</th></tr>
						<tr><th>Статические:</th></tr>
					</table>
				</td>
				<td>
					<table>
						<tr>
							<?php foreach (array_reverse($label_date) as $value) : ?>
								<td><?php echo $value;?></td>
							<?php endforeach; ?>
						</tr>
						<tr style="background-color: rgba(151,187,205,0.2);">
							<?php foreach (array_reverse($cound_dynamic) as $value) : ?>
								<td><?php echo $value;?></td>
							<?php endforeach; ?>
						</tr>
						<tr style="background-color: rgba(220,220,220,0.2);">
							<?php foreach (array_reverse($cound_default) as $value) : ?>
								<td><?php echo $value;?></td>
							<?php endforeach; ?>
						</tr>   
					</table>
				</td>
			</tr>
		</table>
		<?php endif; ?>

	</div>
<?php

}

add_action('admin_menu', 'add_page_issued');

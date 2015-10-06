<?
function add_page_busy () {
	add_submenu_page('call_tracking', 'Статистика занятости номеров', 'Занятые номера', 'manage_options', 'calltracking_busy', 'create_tracking_busy');
}
function create_tracking_busy () {
	global $wpdb;
	$busy_number = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "busy_number WHERE DATE( date_report ) > DATE( NOW( ) - INTERVAL 30 DAY ) ORDER BY date_report ASC", OBJECT_K);
	
	$timestamp = time();
	$time = getdate($timestamp);
	$year = $time['year'];
	$month = $time['mon'];
	$day = $time['mday'];
	$hours = 0;
	$minutes = 0;
	$seconds = 0;
	
	$label_time = array();
	$array_bn = array();

	for ($i = 0; $i < 144; $i++) {
		$temp = mktime($hours, $minutes, $seconds, $month, $day, $year);
		if($minutes % 60 == 0) {
			$label_time[] = date('H:i', $temp);
		} else {
			$label_time[] = "";
		}
		
		$array_bn[date('H:i', $temp)] = 0;
		$minutes = $minutes + 10;
	}

	foreach ($busy_number as $value) {
		$tmp = date("H:i", strtotime($value->date_report));
	
		if($value->count_number > $array_bn[$tmp]) {
			$array_bn[$tmp] = $value->count_number;
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
		<h2>Статистика занаятости номеров с групировкой каждые (10 мин) за последие 30 дней:</h2>
	</div>
	<div style="overflow: auto; padding: 0 50px;">
			
		<script src="<?php echo plugins_url(); ?>/callTracking/js/Chart.min.js"></script>
		<canvas id="myChart" style="width:100%; height:500px;"></canvas>
		<script>

var ctx = document.getElementById("myChart").getContext("2d");
var data = {
    labels: ['<?php echo join("', '", $label_time); ?>'],
    datasets: [
        {
            label: "Динамические номера",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: ['<?php echo join("', '", $array_bn); ?>']
        }
    ]
};

var options = {

    ///Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines : true,

    //String - Colour of the grid lines
    scaleGridLineColor : "rgba(0,0,0,.05)",

    //Number - Width of the grid lines
    scaleGridLineWidth : 0,

    //Boolean - Whether to show horizontal lines (except X axis)
    scaleShowHorizontalLines: true,

    //Boolean - Whether to show vertical lines (except Y axis)
    scaleShowVerticalLines: true,

    //Boolean - Whether the line is curved between points
    bezierCurve : true,

    //Number - Tension of the bezier curve between points
    bezierCurveTension : 0.4,

    //Boolean - Whether to show a dot for each point
    pointDot : false,

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

    showTooltips : false,

    //String - A legend template
    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"

};
	var myLineChart = new Chart(ctx).Line(data, options);
</script>

		<?php if($busy_number) : ?>
		<p style="font-size: 18px; margin: 10px 0;">Количество занятых номеров: </p>
		<table class="wp-list-table widefat striped pages table_reports" style="width: 12000px;">
			<tr>
				<td>
					<table>
						<tr><th>Время:</th></tr>
						<tr><th>Количество:</th></tr>
					</table>
				</td>
				<?php foreach ($array_bn as $key => $value) : ?>
				<td>
				<table>
					<tr>
						<td><?php echo  $key; ?></td>
					</tr>
					<tr>
						<td><?php echo $value; ?></td>
					</tr>
				</table>
				</td>
				<?php endforeach; ?>
			</tr>
		</table>
		<?php endif; ?>
	</div>
<?php
}

add_action('admin_menu', 'add_page_busy');

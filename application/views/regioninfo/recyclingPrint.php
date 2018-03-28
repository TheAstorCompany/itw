<?php 
include("application/views/admin/common/header_print.php");

echo '
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">

	google.load("visualization", "1", {packages: ["corechart"]});

	function drawChart1() 
	{
		var data = google.visualization.arrayToDataTable([
			["Waste Type",	"Percentage"],
			["Aluminum",	' . $recycling['sum']['aluminum'] . '],
			["Cardboard",	' . $recycling['sum']['cardboard'] . '],
			["Film",		' . $recycling['sum']['film'] . '],
			["Plastic",		' . $recycling['sum']['plastic'] . '],
			["Other",		' . $recycling['sum']['trees'] . '],
		]);

		var options = {
			title:		"Recycling",
			isStacked:	true
		};

		var chart = new google.visualization.PieChart(document.getElementById("chart1"));
		chart.draw(data, options);
	}

	google.setOnLoadCallback(drawChart1);


	function drawChart2() 
	{
		var data = google.visualization.arrayToDataTable([
			["Month", "Waste", "Cardboard", "Film", "Plastic", "Aluminum"],
';

while (list($k,$v) = each($recycling['by_months']))
{
	echo json_encode(array_values($v)) . ", \r\n";
}

echo '
		]);

		var options = {
			title		: "",
			width		: 600,
			height		: 300,
			vAxis		: {title: "Tons"},
			hAxis		: {title: "Month"}
		};

		var chart = new google.visualization.AreaChart(document.getElementById("chart2"));
		chart.draw(data, options);
	}

	google.setOnLoadCallback(drawChart2);
</script>


<div class="row" style="padding-top: 50px">
	<div class="sixteen columns">

		<h1>Waste Report</h1>

		<h5 style="float:left;padding-right:10px">
			<span>Name: </span>' . $name . '
		</h5>

		<h5 style="float:left;padding-right:10px">
			<span>Start Date: </span>' . $from . '
		</h5>

		<h5 style="float:left;padding-right:10px">
			<span>End Date: </span>' . $to . '
		</h5>

		<hr />';


if(!empty($recycling['rows']))
{
	// Display charts only if there is data
	echo '
					<div id="chart1" style="width: 350px; height: 250px; float:left"></div>
					<div id="chart2" style="width: 400px; height: 300px; float:left"></div>';

	echo '
					<table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
						<thead>
							<tr>
								<th>Region</th>
								<th>District</th>
								<th>Location</th>
								<th>24H</th>
								<th>SqFt</th>
								<th>Cardboard</th>
								<th>Aluminum</th>
								<th>Film</th>
								<th>Plastic</th>
								<th>Trees</th>
								<th>Landfill</th>
								<th>Energy (KWh)</th>
								<th>Co2 (Tons)</th>
								<th>Rebate</th>
							</tr>
						</thead>
						<tbody>';

	$i = 1;
	while (list($k,$v) = each($recycling['rows']))
	{
		echo '
							<tr class="' . (($i%2) ? 'even' : 'odd') . ' gradeA">
								<td>' . $v['region'] . '</td>
								<td>' . $v['district'] . '</td>
								<td>' . $v['location'] . '</td>
								<td>' . $v['24H'] . '</td>
								<td>' . $v['sqft'] . '</td>
								<td>' . $v['cardboard'] . '</td>
								<td>' . $v['aluminum'] . '</td>
								<td>' . $v['film'] . '</td>
								<td>' . $v['plastic'] . '</td>
								<td>' . $v['trees'] . '</td>
								<td>' . $v['landfill'] . '</td>
								<td>' . $v['kwh'] . '</td>
								<td>' . $v['co2'] . '</td>
								<td>' . $v['rebate'] . '</td>
							</tr>';
		$i++;
	}

	echo '
						</tbody>
						<tfoot>
							<tr>
								<th>1 Region</th>
								<th>' . $tblData['sum']['districts_count'] . ' Districts</th>
								<th>' . $tblData['sum']['locations_count'] . ' Locations</th>
								<th>&nbsp;</th>
								<th>' . $recycling['sum']['sqft'] . ' SqFt</th>
								<th>' . $recycling['sum']['cardboard'] . '</th>
								<th>' . $recycling['sum']['aluminum'] . '</th>
								<th>' . $recycling['sum']['film'] . '</th>
								<th>' . $recycling['sum']['plastic'] . '</th>
								<th>' . $recycling['sum']['trees'] . '</th>
								<th>' . $recycling['sum']['landfill'] . '</th>
								<th>' . $recycling['sum']['kwh'] . '</th>
								<th>' . $recycling['sum']['co2'] . '</th>
								<th>' . $recycling['sum']['rebate'] . '</th>
							</tr>
						</tfoot>
					</table>';
}



include("application/views/admin/common/footer.php");


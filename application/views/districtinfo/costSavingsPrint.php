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
			["Waste Service",	' . $tblData['sum']['waste_service'] . '],
			["Equipment",		' . $tblData['sum']['waste_equipment_fee'] . '],
			["Haul",			' . $tblData['sum']['waste_haul_fee'] . '],
			["Disposal",		' . $tblData['sum']['waste_disposal_fee'] . '],
			["Other",			' . $tblData['sum']['other_fee'] . ']
		]);

		var options = {
			title:		"",
			isStacked:	true
		};

		var chart = new google.visualization.PieChart(document.getElementById("chart1"));
		chart.draw(data, options);
	}

	google.setOnLoadCallback(drawChart1);


	function drawChart2() 
	{
		var data = google.visualization.arrayToDataTable([
			["Net", "Savings", "Cost"], ' 
			. json_encode(array_values($tblData['chart_data']['net'])) . ", \r\n"
			. json_encode(array_values($tblData['chart_data']['savings'])) . ", \r\n"
			. json_encode(array_values($tblData['chart_data']['cost'])) . " \r\n"
		.']);

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


	function drawChart3() 
	{
		var data = google.visualization.arrayToDataTable([
			["recycling rebates",	' . $tblData['sum']['recycling_rebate'] . '],
			["SAMS",				' . $SAMS . '],
			["waived fees",			' . $tblData['sum']['waived_fee'] . ']
		]);

		var options = {
			title:		"",
			isStacked:	true
		};

		var chart = new google.visualization.PieChart(document.getElementById("chart3"));
		chart.draw(data, options);
	}

	google.setOnLoadCallback(drawChart3);
</script>


<div class="row" style="padding-top: 50px">
	<div class="sixteen columns">

		<h1>Waste Report</h1>

		<h5 style="float:left;padding-right:10px">
			<span>District: </span>' . $name . '
		</h5>

		<h5 style="float:left;padding-right:10px">
			<span>Start Date: </span>' . $from . '
		</h5>

		<h5 style="float:left;padding-right:10px">
			<span>End Date: </span>' . $to . '
		</h5>

		<hr />';


if(!empty($tblData['rows']))
{
	// Display charts only if there is data
	echo '
					<div id="chart1" style="width: 350px; height: 250px; float:left"></div>
					<div id="chart2" style="width: 400px; height: 300px; float:left"></div>
					<div id="chart3" style="width: 400px; height: 300px; float:left"></div>';

	echo '
					<table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
						 <thead>
							<tr>
								<th>Region</th>
								<th>District</th>
								<th>Location</th>
								<th>24H</th>
								<th>SqFt</th>
								<th>Period</th>
								<th>Total Tonnage</th>
								<th> Waste<br>Service</th>
								<th>Waste <br>Equipment Fee</th>
								<th>Waste<br>Haul Fee</th>
								<th>Waste <br> Disposal Fee</th>
								<th>Recycling Rebate</th>
								<th>Other Fee</th>
								<th>Net</th>
							</tr>
						</thead>
						<tbody>';

	$i = 1;
	while (list($k,$v) = each($tblData['rows']))
	{
		echo '
						<tr class="' . (($i%2) ? 'even' : 'odd') . ' gradeA">
								<td>' . $v['region'] . '</td>
								<td>' . $v['district'] . '</td>
								<td>' . $v['location'] . '</td>
								<td>' . $v['24H'] . '</td>
								<td>' . $v['sqft'] . '</td>
								<td>' . $v['period'] . '</td>
								<td>' . $v['total_tonage'] . '</td>
								<td>$' . $v['waste_service'] . '</td>
								<td>$' . $v['waste_equipment_fee'] . '</td>
								<td>$' . $v['waste_haul_fee'] . '</td>
								<td>$' . $v['waste_disposal_fee'] . '</td>
								<td>$' . $v['recycling_rebate'] . '</td>
								<td>$' . $v['other_fee'] . '</td>
								<td>$' . $v['net'] . '</td>
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
								<th>' . $tblData['sum']['sqft'] . ' SqFt</th>
								<th>Period</th>
								<th>' . $tblData['sum']['total_tonage'] . '</th>
								<th>$' . $tblData['sum']['waste_service'] . '</th>
								<th>$' . $tblData['sum']['waste_equipment_fee'] . '</th>
								<th>$' . $tblData['sum']['waste_haul_fee'] . '</th>
								<th>$' . $tblData['sum']['waste_disposal_fee'] . '</th>
								<th>$' . $tblData['sum']['recycling_rebate'] . '</th>
								<th>$' . $tblData['sum']['other_fee'] . '</th>
								<th>$' . $tblData['sum']['net'] . '</th>
							</tr>
						</tfoot>
					</table>';
}



include("application/views/admin/common/footer.php");


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
			["Waste",		' . $tblData['sum']['waste'] . '],
			["Hazardous",	' . $tblData['sum']['hazardous'] . '],
			["Other",		' . $tblData['sum']['other'] . ']
		]);

		var options = {
			title:		"Waste Types",
			isStacked:	true
		};

		var chart = new google.visualization.PieChart(document.getElementById("chart1"));
		chart.draw(data, options);
	}

	google.setOnLoadCallback(drawChart1);


	function drawChart2() 
	{
		var countries = ["Waste", "Hazardous", "Other"];
		var months = ' . json_encode($tblData['chart_data']['dates']) . ';

		var productionByCountry = [
			' . json_encode($tblData['chart_data']['waste']) . ',
			' . json_encode($tblData['chart_data']['hazardous']) . ',
			' . json_encode($tblData['chart_data']['other']) . '
		];

		// Create and populate the data table.
		var data = new google.visualization.DataTable();

		data.addColumn("string", "Month");

		for (var i = 0; i < countries.length; ++i) {
		  data.addColumn("number", countries[i]);
		}

		data.addRows(months.length);

		for (var i = 0; i < months.length; ++i) {
		  data.setCell(i, 0, months[i]);
		}

		for (var i = 0; i < countries.length; ++i) {
		  var country = productionByCountry[i];
		  for (var month = 0; month < months.length; ++month) {
			data.setCell(month, i + 1, country[month]);
		  }
		}

		// Create and draw the visualization.
		var ac = new google.visualization.AreaChart(document.getElementById("chart2"));
		ac.draw(data, {
			title		: "",
			width		: 600,
			height		: 300,
			vAxis		: {title: "Tons"},
			hAxis		: {title: "Month"}
		});
	}

	google.setOnLoadCallback(drawChart2);
</script>


<div class="row" style="padding-top: 50px">
	<div class="sixteen columns">

		<h1>Waste Report</h1>

		<h5 style="float:left;padding-right:10px">
			<span>Store Center: </span>' . $name . '
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
								<th>Period</th>
								<th>Waste (Tons)</th>
								<th>Hazardous (Tons)</th>
								<th>Other (Tons)</th>
								<th>Cost</th>
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
								<td>' . number_format($v['sqft'], 2) . '</td>
								<td>' . $v['period'] . '</td>
								<td>' . number_format($v['waste'], 2) . '</td>
								<td>' . number_format($v['hazardous'], 2) . '</td>
								<td>' . number_format($v['other'], 2) . '</td>
								<td>' . number_format($v['cost'], 2) . '</td>
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
								<th>&nbsp;</th>
								<th>' . $tblData['sum']['waste'] . ' Waste (Tons)</th>
								<th>' . $tblData['sum']['hazardous'] . ' Hazardous (Tons)</th>
								<th>' . $tblData['sum']['other'] . ' Other (Tons)</th>
								<th>$' . $tblData['sum']['cost'] . ' Cost</th>
							</tr>
						</tfoot>
					</table>';
}



include("application/views/admin/common/footer.php");


<?php
include("application/views/admin/common/header.php");
include("application/views/admin/common/top_menu.php");


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


	$(function() {

		$("#tbl").dataTable( {
					"bPaginate": false,
					"bFilter": false
		});

		$(".submit").click(function(){
			$(this).parent().submit();
			return false;
		});

		var dates = $("#from, #to").datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "from" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
</script>

<div class="content">
	<div class="row">
		<div class="two columns">
			<a href="javascript:javascript:history.go(-1)" class="button">&lt;- Go back</a>
		</div>
	</div>
	<div class="row">
		<div class="four columns">
			<h1>' . $name . ' Region</h1>
		</div>
		<div class="two columns">
			Square Footage<h5>' . $storesSqft . ' sqft</h5>
		</div>
		<div class="two columns">
			Diversion Rate<h5>' . $DiversionRate . '%</h5>
		</div>
		<div class="eight columns omega">
		</div>
	</div>

	<div class="row">
		<div class="sixteen columns">
			<div id="tabs" style="border:0px;">
				<ul>
					<li><a href="' . base_url() . 'Regioninfo/Waste/' . $name . '">Waste</a></li>
					<li id="selected_tab"><a href="' . base_url() . 'StateInfo/Recycling/' . $name . '">Recycling</a></li>
					<li><a href="' . base_url() . 'Regioninfo/CostSavings/' . $name . '">Cost/Savings</a></li>
					<li><a href="' . base_url() . 'Regioninfo/Invoices/' . $name . '">Invoices</a></li>
					<li><a href="' . base_url() . 'Regioninfo/SupportRequests/' . $name . '">Support Requests</a></li>
					<li><a href="' . base_url() . 'Regioninfo/Services/' . $name . '">Services</a></li>
					<li><a href="' . base_url() . 'Regioninfo/Locations/' . $name . '">Locations</a></li>
				</ul>

				<div style="border:2px solid #7ABF53; padding: 10px;">';

if(!empty($recycling['rows']))
{
	echo '
					<span style="float:right">
						<a href="' . base_url() . 'Regioninfo/Recycling/' . $name . '?print=1&from=' . urlencode($from) . '&to=' . urlencode($to) . '" class="button" target="_blank">Printer Friendly</a> 
						<a href="' . base_url() . 'Regioninfo/Recycling/' . $name . '?export=1&from=' . urlencode($from) . '&to=' . urlencode($to) . '" class="button">Export CSV</a>
					</span>';
}

echo
					form_open('StoreInfo/Recycling/' . $name, array('method'=>'get')) . '
						<span style="float:left;padding-right:10px">
							<label for="from">Start Date</label>
							<input name="from" type="text" id="from" value="' . set_value('from', $from) . '" style="width:100px" />
						</span>
						<span style="float:left;padding-right:10px">
							<label for="to">End Date</label>
							<input name="to" type="text" id="to" value="' . set_value('to', $to) . '" style="width:100px" />
						</span>
						<label for="for">&nbsp;</label>
						<input type="submit" value="UPDATE" class="submit" />
					</form>

					<br style="clear:both;" />';

if(!empty($recycling['rows']))
{
	// Display charts only if there is data
	echo '
					<div id="chart1" style="width: 350px; height: 250px; float:left"></div>
					<div id="chart2" style="width: 400px; height: 300px; float:left"></div>';

	echo '
					<table cellpadding="0" cellspacing="0" border="0" class="display" id="tbl" width="100%">
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
								<td><a href="' . base_url() . 'Districtinfo/Waste/' . $v['district_id'] . '">' . $v['district'] . '</a></td>
								<td><a href="' . base_url() . 'Storeinfo/Waste/' . $v['location_id'] . '">' . $v['location'] . '</a></td>
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

echo '
					<br style="clear: both;" />
				</div>
			</div>
		</div>
	</div>
</div>';

include("application/views/admin/common/footer.php");



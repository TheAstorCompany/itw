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

	$(function() {

		$("#wastelist").dataTable({
			"bPaginate"	: false,
			"bFilter"	: false
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
					<li><a href="' . base_url() . 'Regioninfo/Recycling/' . $name . '">Recycling</a></li>
					<li id="selected_tab"><a href="' . base_url() . 'Regioninfo/CostSavings/' . $name . '">Cost/Savings</a></li>
					<li><a href="' . base_url() . 'Regioninfo/Invoices/' . $name . '">Invoices</a></li>
					<li><a href="' . base_url() . 'Regioninfo/SupportRequests/' . $name . '">Support Requests</a></li>
					<li><a href="' . base_url() . 'Regioninfo/Services/' . $name . '">Services</a></li>
					<li><a href="' . base_url() . 'Regioninfo/Locations/' . $name . '">Locations</a></li>
				</ul>

				<div style="border:2px solid #7ABF53; padding: 10px;">';

if(!empty($tblData['rows']))
{
	echo '
					<span style="float:right">
						<a href="' . base_url() . 'Regioninfo/CostSavings/' . $name . '?print=1&from=' . urlencode($from) . '&to=' . urlencode($to) . '" class="button" target="_blank">Printer Friendly</a> 
						<a href="' . base_url() . 'Regioninfo/CostSavings/' . $name . '?export=1&from=' . urlencode($from) . '&to=' . urlencode($to) . '" class="button">Export CSV</a>
					</span>';
}

echo
					form_open('StoreInfo/CostSavings/' . $name, array('method'=>'get')) . '
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

if(!empty($tblData['rows']))
{
	// Display charts only if there is data
	echo '
					<div id="chart1" style="width: 350px; height: 250px; float:left"></div>
					<div id="chart2" style="width: 400px; height: 300px; float:left"></div>
					<div id="chart3" style="width: 400px; height: 300px; float:left"></div>


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
								<td><a href="' . base_url() . 'Districtinfo/Waste/' . $v['district_id'] . '">' . $v['district'] . '</a></td>
								<td><a href="' . base_url() . 'Storeinfo/Waste/' . $v['location_id'] . '">' . $v['location'] . '</a></td>
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



























echo '
					<br style="clear: both;" />
				</div>
			</div>
		</div>
	</div>
</div>';

include("application/views/admin/common/footer.php");



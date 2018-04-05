<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>


<script type="text/javascript">
	$(function() {
		$('#tabs').tabs();
		$('#storeslist').dataTable( {
					"sPaginationType": "full_numbers",
					"iDisplayLength": 25,
					 "oLanguage": {
						"sLengthMenu": 'Display <select>'+
						  '<option value="25">25</option>'+
						  '<option value="50">50</option>'+
						  '<option value="100">100</option>'+
						  '<option value="-1">All</option>'+
						  '</select> records'
					  }
				} );
		
		
	});
</script>

<script type="text/javascript" src="http://www.google.com/jsapi"></script>

<script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
</script>

<script type="text/javascript">
	
	/*
	function drawVisualization() {
		// Some raw data (not necessarily accurate)
		var countries =
		  ['Aluminum', 'Plastic',
		   'LDPE Film', 'Cardboard', 'Waste'];
		var months = ['09/2011','10/2011', '11/2011', '12/2011', '01/2012', 'Current'];
		var productionByCountry = [[200, 365, 135, 357, 239, 236],
								   [800, 938, 1120, 1167, 1110, 691],
								   [400, 522, 599, 587, 615, 629],
								   [1100, 998, 1268, 807, 968, 1026],
								   [400, 450, 288, 397, 215, 366]];

		// Create and populate the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Month');
		for (var i = 0; i < countries.length; ++i) {
		  data.addColumn('number', countries[i]);
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
		var ac = new google.visualization.AreaChart(document.getElementById('visualization'));
		ac.draw(data, {
		  title : 'Waste/Recycling Production',
		  isStacked: true,
		  width: 600,
		  height: 300,
		  vAxis: {title: "Tons"},
		  hAxis: {title: "Month"}
		});
	}
	google.setOnLoadCallback(drawVisualization);
	*/
	function drawVisualization2() {
		// Create and populate the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Task');
		data.addColumn('number', 'Hours per Day');
		<?php 
		if(isset($SRChartInfo)) {
			printf("data.addRows(%d);\n", count($SRChartInfo));
			foreach($SRChartInfo as $k=>$_item) {
				printf("data.setValue(%d, 0, '%s');\n", $k, $_item->Name);
				printf("data.setValue(%d, 1, %d)\n", $k, $_item->value);
			}
		}       
		?>      
		// Create and draw the visualization.
		new google.visualization.PieChart(document.getElementById('servicechart')).
			draw(data, {title:"", chartArea:{left:0,top:0,width:"100%",height:"100%"}});
	}
	google.setOnLoadCallback(drawVisualization2);	  
	
	
	
	function drawVisualization3() {
		// Create and populate the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Task');
		data.addColumn('number', 'Hours per Day');
		<?php 
		if(isset($CoSChartInfo)) {        	
			printf("data.addRows(%d);\n", count($CoSChartInfo));
			foreach($CoSChartInfo as $k=>$_item) {
				printf("data.setValue(%d, 0, '%s');\n", $k, $_item->Name);
				printf("data.setValue(%d, 1, %d);\n", $k, $_item->value);
			}
		}       
		?>
		// Create and draw the visualization.
		new google.visualization.PieChart(document.getElementById('invoicechart')).
			draw(data, {title: "", chartArea:{left:0,top:0,width:"100%",height:"100%"}});//, backgroundColor: "#aaffaa"
		
	}
	google.setOnLoadCallback(drawVisualization3);
	
	 
	
	function drawVisualization4() {
		// Create and populate the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Week');
		data.addColumn('number', 'Open Calls');
		data.addColumn({type: 'string', role: 'tooltip'});
		data.addColumn('number', 'Resolved Calls');		
		data.addColumn({type: 'string', role: 'tooltip'});

		<?php 
		if(isset($calls)) {        	
			printf("data.addRows(%d);\n", count($calls));
			$i = 0;
			foreach($calls as $k=>$_item) {
				printf("data.setValue(%d, 0, 'Week of %s');\n", $i, $k);
				printf("data.setValue(%d, 1, %d);\n", $i, $_item['open']);
				printf("data.setValue(%d, 2, 'Week of %s Open Calls: %d Total: %d');\n", $i, $k, $_item['open'], $_item['total']);
				printf("data.setValue(%d, 3, %d);\n", $i, $_item['closed']);
				printf("data.setValue(%d, 4, 'Week of %s Resolved Calls: %d Total: %d');\n", $i, $k, $_item['closed'], $_item['total']);
				$i++;
			}
		}       
		?>		

		// Create and draw the visualization.
		var chart = new google.visualization.ColumnChart(document.getElementById('callschart'));    
		chart.draw(data, {chartArea: {left: 50, top: 20, width: '50%', height: '80%'}, is3D: false, title: '', colors: ['#90BC53', '#588915'], isStacked: true, legend: 'right', legendBackgroundColor: '#ccc'}); 

	}
	google.setOnLoadCallback(drawVisualization4);	  
	
</script>
    
    
<div class="row">
	<div class="ten columns">
		<h1>Dashboard</h1>
	   <!-- <?php //var_dump( $allStores) ?>  -->
	</div>
    <div class="six columns" style="text-align: right;">
    	<form action="" method="get">
        <span style="float:right">
            <select name="store_id" id="store_id" style="float:left;">
                <option value="0">All</option>
                <?php
                foreach($allStores as $temp) {
                    if($store_id == $temp["id"]) {
                        echo "<option selected=\"selected\" value=\"{$temp["id"]}\">{$temp["location"]}</option>";
                    } else {
                        echo "<option value=\"{$temp["id"]}\">{$temp["location"]}</option>";
                    }
                }
                ?>
            </select>
            <div style="float: left; padding-left: 5px; margin-top: -5px">
                <button type="submit" style="margin: 0px;">Update</button>
            </div>
        </span>
        </form>
        <!-- <fieldset style="width: 420px; border: 1px solid #ddd; margin-left: 10px;">
            <legend>Reports</legend>
            <select id="savingsYear" name="savingsYear" style="width: 70px; display: inline;">
                <?php
                for($y=2013; $y<=2030; $y++) {
                    echo '<option value="'.$y.'">'.$y.'</option>'."\n";
                }
                ?>
            </select>
            &nbsp;&nbsp;<a href="#" onclick="this.href = '<?php echo base_url(); ?>admin/Reports/getExpenses?year=' + $('#savingsYear').val();">Expenses</a>
            &nbsp;/&nbsp;<a href="#" onclick="this.href = '<?php echo base_url(); ?>admin/WasteInvoice/SavingsAll?year=' + $('#savingsYear').val();">Savings All</a>
            &nbsp;/&nbsp;<a href="#" onclick="this.href = '<?php echo base_url(); ?>admin/WasteInvoice/SavingsContract?year=' + $('#savingsYear').val();">Savings Contract</a>
            &nbsp;/&nbsp;<a href="<?php echo base_url(); ?>admin/Reports/getDiversion">Diversion</a>
            &nbsp;&nbsp;
        </fieldset>
 -->    
 	</div>
</div>
<div class="row" style="border-bottom:1px solid #ddd">
	<div class="seven columns">
		<table width="100%" border="0" cellspacing="10" cellpadding="20">
            <tr>
                <td colspan="3"><h5><?php echo date('F Y', mktime(0, 0, 0, date('m')-1, 1, date('Y'))); ?></h5></td>
            </tr>
			<tr style="border-bottom:1px solid #ddd">
				<td>Waste
					<h4><?php echo number_format($periodPriorMonthData->waste, 0); ?> tons</h4></td>
				<td>Recycling
					<h4><?php echo number_format($periodPriorMonthData->recycling, 0); ?> tons</h4></td>
				<!-- <td>Diversion&nbsp;rate
					<h4><?php echo number_format($periodPriorMonthData->diversion, 0); ?>%</h4></td>
					<td>Baseline&nbsp;Cost -->				
 				<td>Monthly Baseline&nbsp;
					<h4>$<?php echo number_format($periodPriorMonthData->baseline, 0); ?></h4></td>
			</tr>
			<tr>
				<td>Cost
					<h4>$<?php echo number_format($periodPriorMonthData->cost, 0); ?></h4></td>
				<td>Rebate
					<h4>$<?php echo  number_format($periodPriorMonthData->rebate, 0); ?></h4></td>
				<!-- <td>Cost&nbsp;per&nbsp;sqft
					<h4>$<?php echo  number_format($periodPriorMonthData->costPerSqft, 3); ?></h4></td> -->
				<td>Cost&nbsp;to&nbsp;this&nbsp;month
					<h4>$<?php echo  number_format($periodPriorMonthData->costPerSqft, 3); ?></h4></td>
				<td>Savings
					<h4>$<?php echo  number_format($periodPriorMonthData->savings, 0); ?></h4></td>
			</tr>
        </table>
        <br /><br /><br /><br />
        <table width="100%" border="0" cellspacing="10" cellpadding="20">
			<tr>
			    <td colspan="3"><h5><?php echo date('F Y', mktime(0, 0, 0, date('m')-2, 1, date('Y'))); ?></h5></td>
			</tr>
			<tr style="border-bottom:1px solid #ddd">
				<td>Waste
					<h4><?php echo number_format($period2MonthsBackData->waste, 0); ?> tons</h4></td>
				<td>Recycling
					<h4><?php echo number_format($period2MonthsBackData->recycling, 0); ?> tons</h4></td>
				<!-- <td>Diversion&nbsp;rate
					<h4><?php echo number_format($period2MonthsBackData->diversion, 0); ?>%</h4></td> 
				<td>Baseline&nbsp;Cost-->
				<td>Monthly Baseline
					<h4>$<?php echo number_format($period2MonthsBackData->baseline, 0); ?></h4></td>					
			</tr>
			<tr>
				<td>Cost
					<h4>$<?php echo number_format($period2MonthsBackData->cost, 0); ?></h4></td>
				<td>Rebate
					<h4>$<?php echo  number_format($period2MonthsBackData->rebate, 0); ?></h4></td>
				<!-- <td>Cost&nbsp;per&nbsp;sqft
					<h4>$<?php echo  number_format($period2MonthsBackData->costPerSqft, 3); ?></h4></td> -->
				<td>Cost&nbsp;to&nbsp;this&nbsp;month
					<h4>$<?php echo  number_format($period2MonthsBackData->costPerSqft, 3); ?></h4></td>
				<td>Savings
					<h4>$<?php echo  number_format($period2MonthsBackData->savings, 0); ?></h4></td>				
			</tr>			
		</table>
	</div>
	<div class="five columns omega">
		<h5>30-day Summary</h5>
		Total Service Requests: <strong><?php echo $total_service_requests; ?></strong><br />
		Open Service Requests: <strong><?php echo $open_service_requests; ?></strong><br />
		<div id="callschart" style="width: 600px; height: 300px;"></div>
	</div>	
</div>
<div class="row">
	<div class="columns">
		<h5>Service Requests</h5>
		<div id="servicechart" style="width: 250px; height: 250px;"></div>
	</div>
	<div class="columns omega">
		<h5>Cost of Services</h5>
		<div id="invoicechart" style="width: 250px; height: 250px;"></div>
	</div>
	<div class="columns" style="width: 650px;">
	<!-- gmaps -->
	<?php
	$map_url = 'http://maps.google.com/maps?q='.base_url().'Maps/STORE/';
	/*<iframe width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q=<?php echo base_url()?>Maps/DC/month&amp;output=embed&iwloc=near&amp"></iframe>*/
	if(set_value('period', 1) == 1) {
		$map_url .= 'month&amp;output=embed&iwloc=near&amp';
	} else if (set_value('period', 1) == 2) {				
		$map_url .= 'quarter&amp;output=embed&iwloc=near&amp';
	} else if (set_value('period', 1) == 3) {				
		$map_url .= 'six&amp;output=embed&iwloc=near&amp';
	} else {				
		$map_url .= 'year&amp;output=embed&iwloc=near&amp';
	}
	?>
	<iframe id="ismallmap" width="100%" height="300" frameborder="0" scrolling="no"	marginheight="0" marginwidth="0" src="<?php echo $map_url; ?>"></iframe>
	<a href="javascript:void(0);" onclick="showBigMap();">Show Big Map</a>
	</div>	
</div>
<div id="bigmap" title="Big Map">
	<div id="bigmap_content" style="width: 1070px; height: 740px;">Loading...</div>
</div>

<script type="text/javascript">

	$(document).ready(function() {
		$('#bigmap').dialog({autoOpen: false, width: 1100, height: 800});
	});

	function showBigMap() {
		$('#bigmap').dialog('open');
		$('#bigmap_content').html('<iframe id="ibigmap" width="1070" height="740" style="width: 1070px; height: 740px;" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $map_url; ?>"></iframe>');
	}
</script>
<?php include("application/views/admin/common/footer.php");?>

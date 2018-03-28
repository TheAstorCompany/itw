<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

<script type="text/javascript">
	$(function() {
		$('#tabs').tabs();
		$('#servicelist').dataTable( {
				"bPaginate": false,
				"bFilter": false
		} );
		var dates = $("input[name='from'], input[name='to']").datepicker({
			 dateFormat: "mm/dd/yy",
			 weekHeader: "W",
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

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
</script>
<script type="text/javascript">
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
          title : '',
          isStacked: true,
          width: 600,
          height: 300,
          vAxis: {title: "Tons"},
          hAxis: {title: "Month"}
        });
      }
      

      google.setOnLoadCallback(drawVisualization);

      function drawVisualization2() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        <?php if(isset($chart1)) {
        	printf("data.addRows(%d);\n", count($chart1));
        	foreach($chart1 as $k=>$_item) {
        		printf("data.setValue(%d, 0, '%s');\n", $k, $_item->Name);
        		printf("data.setValue(%d, 1, %d)\n", $k, $_item->value);
        	}
        }
      	?>
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('servicechart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization2);
	  
	  
      function drawVisualization3() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        <?php if(isset($chart2)) {
        	printf("data.addRows(%d);\n", count($chart2));
        	foreach($chart2 as $k=>$_item) {
        		printf("data.setValue(%d, 0, '%s');\n", $k, $_item->Name);
        		printf("data.setValue(%d, 1, %d)\n", $k, $_item->value);
        	}
        }
      	?>      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('timechart')).
            draw(data, {title:""});
      }
      google.setOnLoadCallback(drawVisualization3);

      
	        function drawVisualization4() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        <?php if(isset($chart3)) {
        	printf("data.addRows(%d);\n", count($chart3));
        	foreach($chart3 as $k=>$_item) {
        		printf("data.setValue(%d, 0, '%s');\n", $k, $_item->Name);
        		printf("data.setValue(%d, 1, %d)\n", $k, $_item->value);
        	}
        }
      	?>
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('frequencychart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization4);
</script>

<div class="row">
    <div class="sixteen columns">
	<span style="float:right">
	    <a href="<?php echo base_url();?>Stores/Services?<?php echo http_build_query(array_merge($_GET, array('print'=>1))); ?>" class="button" target="_blank">Printer Friendly</a> 
	    <a href="<?php echo base_url();?>Stores/Services?<?php echo http_build_query(array_merge($_GET, array('export'=>1))); ?>" class="button">Export CSV</a>
	</span>        
	<h1>Services</h1>
	<form action="" method="get">
	<span style="float: left; padding-right: 10px; width: 220px;">
	    <label for="for">Display</label>
	    <?php echo form_dropdown('display', $data->forOptions, set_value('display', $data->display));?>
	</span>
	<span style="float: left; padding-right: 10px; width: 220px;">
	    <label for="for">State</label>
	    <?php echo form_dropdown('bystate', $data->byState, set_value('bystate', $data->bystate));?>
	</span>
	<div style="float:left; margin-top: 15px;">
	    <button type="submit">Update</button>
	</div>
	</form>
	<hr />
    </div>
</div>
<div class="row">
    <div class="five columns">
	<h5>Type of Containers</h5>
	<div id="servicechart" style="width: 380px; height: 200px;"></div>
    </div>
    <div class="five columns">
	<h5>Materials of Service</h5>
	<div id="timechart" style="width: 380px; height: 200px;"></div>
    </div>
    <div class="five columns">
	<h5>Frequency of Service</h5>
	<div id="frequencychart" style="width: 380px; height: 200px;"></div>
    </div>
</div>
<div class="row">
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="servicelist" width="100%">
	<thead>
	    <tr>
		<th>Location</th>
		<th>District</th>
		<th>State</th>
		<th>SqFt</th>
		<th>24H</th>
		<th>Container</th>            
		<th>Frequency</th>
		<th>Cost</th>
	    </tr>
	</thead>
	<tbody>
	<?php foreach($table as $k=>$item) { 
            	@$total['region'][$item->region] = $item->region;
            	@$total['district'][$item->district] = $item->district;
            	@$total['location'][$item->location] = $item->location;
		@$total['state'][$item->state] = $item->state;
            	@$total['squareFootage'] += $item->squareFootage;
            	?>
                <tr class="<?php echo $k%2?'even':'odd';?> gradeA">
				<td><a href="<?php echo base_url()?>StoreInfo/Invoices/<?php echo $item->id?>"><?php echo $item->location?></a></td>
    				<td><?php echo $item->district; ?></td>
    				<td><?php echo $item->state; ?></td>   
				<td><?php echo number_format($item->squareFootage, 0); ?></td>
    				<td><?php echo $item->open24hours ? 'Y' : 'N'; ?></td>
    				<td><?php echo $item->container; ?></td>    				
    				<td><?php echo $item->frequency; ?></td>
    				<td>$<?php echo number_format($item->cost, 2); ?></td>
    			</tr>
    		<?php } ?>  
	</tbody>
	<tfoot>
	    <tr>
		<th><?php echo count(@$total['location']); ?> Location</th>
		<th><?php echo count(@$total['district']); ?> Districts</th>
		<th><?php echo count(@$total['state']); ?> States</th>
		<th>SqFt</th>
		<th>24H</th>
		<th>Container</th>            
		<th>Frequency</th>
		<th>Cost</th>
	    </tr>
	</tfoot>
    </table>
</div>
        
<?php include("application/views/admin/common/footer.php");?>
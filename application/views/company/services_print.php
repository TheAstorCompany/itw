<?php include("application/views/admin/common/header_print.php");?>

<script>
	$(function() {
		$('#servicelist').dataTable( {
			"bPaginate": false,
					"bFilter": false
		});

	});
	</script>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
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
    
    
      <div class="content">
      <div class="row">&nbsp;</div>
    <div class="row">
          <div class="sixteen columns">     
       	 <h1>Services Report</h1>
      	<h5 style="float:left;padding-right:10px">
      		<span>Start Date: </span><?php echo set_value('from', date('m') . '/01/' . date('Y'));?>
		</h5>
		<h5 style="float:left;padding-right:10px">
			<span>End Date: </span><?php echo set_value('to', date('m/d/Y'));?>
		</h5>
		<h5 style="float:left;padding-right:10px">
        	<span>Show: </span><?php echo $data->forOptions[set_value('for', $data->for)];?>
        </h5>
      </div>
        </div>
    <div class="row">
          <div class="five columns">
        <h5>Type of Containers</h5>
        <div id="servicechart" style="width: 380px; height: 200px;"></div>
      </div>
          <div class="five columns">
        <h5>Duration of Service</h5>
        <div id="timechart" style="width: 380px; height: 200px;"></div>
      </div>
          <div class="five columns">
        <h5>Frequency of Service</h5>
        <div id="frequencychart" style="width: 380px; height: 200px;"></div>
      </div>
        </div>
    <div class="row">
          <div class="sixteen columns">
        <div> 
              <!--To style tr, use class="odd even gradeA gradeB gradeC gradeU gradeX"-->
              <table cellpadding="0" cellspacing="0" border="0" class="display" id="servicelist" width="100%">
            <thead>
                  <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Container</th>
                <th>Duration</th>
                <th>Frequency</th>
                <th>Cost</th>
                <th>Last Updated</th>
              </tr>
                </thead>
            <tbody>
			<?php foreach($table as $k=>$item) { 
            	@$total['region'][$item->region] = $item->region;
            	@$total['district'][$item->district] = $item->district;
            	@$total['location'][$item->location] = $item->location;
            	@$total['squareFootage'] += $item->squareFootage;
            	//@$total['waste'] += $item->EnergySaves;
            	//@$total['hazardous'] += $item->CO2Saves;
            	//@$total['other'] += $item->CO2Saves;
            	//@$total['cost'] += $item->cost;
            	?>
                <tr class="gradeA">
                	<td><?php
                		if ($item->locationType == 'DC') {
                			echo 'DC';
                		} else {
                			echo 'Region';
                		}
                	?></td>
                	<td><?php echo $item->name; ?></td>
                	<td><?php echo $item->container; ?></td>
                	<td><?php echo $item->duration; ?></td>
                	<td><?php echo $item->frequency; ?></td>
                	<td><?php echo number_format($item->cost,2);?></td> 				
       				<td><?php echo $item->lastUpdated?></td>
    			</tr>
    		<?php } ?> 
                </tbody>
            <tfoot>
                  <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Container</th>
                <th>Duration</th>
                <th>Frequency</th>
                <th>Cost</th>
                <th>Last Updated</th>
                </tfoot>
          </table>
            </div>
      </div>
        </div>
  </div>
<?php include("application/views/admin/common/footer.php");?>
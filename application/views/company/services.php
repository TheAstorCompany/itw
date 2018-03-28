<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

<script>
	$(function() {
		$('#servicelist').dataTable( {
			"bPaginate": false,
					"bFilter": false
		});
		
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
    <div class="row">
          <div class="sixteen columns"> <span style="float:right">
        <a href="<?php echo base_url();?>Company/Services?<?php echo http_build_query(array_merge($_GET, array('print'=>1))); ?>" class="button" target="_blank">Printer Friendly</a> 
        <a href="<?php echo base_url();?>Company/Services?<?php echo http_build_query(array_merge($_GET, array('export'=>1))); ?>" class="button">Export CSV</a></span>        
        <h1>Services Report</h1>
        <form action="" method="get">
        <span style="float:left;padding-right:10px;width:106px;"><label for="from">Start Date</label>
		<input name="from" type="text" id="from" value="<?php echo set_value('from', date('m') . '/01/' . date('Y'));?>" style="width:100px"/></span><span style="float:left;padding-right:10px;width:106px;">
		<label for="to">End Date</label>
		<input name="to" type="text" id="to" value="<?php echo set_value('to', date('m/d/Y'));?>" style="width:100px"/></span><span style="float:left;padding-right:10px;width:220px;">
        <label for="for">Display</label>
        	<?php echo form_dropdown('display', $data->forOptions, set_value('display', $data->display));?>
        </span>
		<div style="float: left; margin-top: 15px;">
			<button type="submit">Update</button>
		</div>
		</form>
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

            	?>
                <tr class="gradeA">
                	<td><?php
                		if ($item->locationType == 'DC') {
                			echo 'DC';
                		} else {
                			echo 'Region';
                		}
                	?></td>
                	<td><a href="<?php echo (($item->locationType == 'DC') ? base_url() . 'Dcinfo/Waste/' . $item->id : base_url() . 'Regioninfo/Waste/' . $item->name) ?>"><?php echo $item->name; ?></a></a></td>
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
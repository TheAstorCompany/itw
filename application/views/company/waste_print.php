<?php include("application/views/admin/common/header_print.php");?>
 <script>
	$(function() {
		$('#servicelist').dataTable({});

		$('#wastelist').dataTable({
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
      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var countries =
          ['Distribution Centers', 'Stores (West)',
           'Stores (Southwest)', 'Stores (MidWest)', 'Stores (East)'];
        var months = <?php echo json_encode($wasteTrends->dates); ?>;// ['09/2011','10/2011', '11/2011', '12/2011', '01/2012', 'Current'];
        var productionByCountry = [<?php echo json_encode($wasteTrends->dc, JSON_NUMERIC_CHECK); ?>,
        						   <?php echo json_encode($wasteTrends->West, JSON_NUMERIC_CHECK); ?>,
        						   <?php echo json_encode($wasteTrends->Southeast, JSON_NUMERIC_CHECK); ?>,
        						   <?php echo json_encode($wasteTrends->MidWest, JSON_NUMERIC_CHECK); ?>,
        						   <?php echo json_encode($wasteTrends->East, JSON_NUMERIC_CHECK); ?>
        						];
      
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
        var ac = new google.visualization.LineChart(document.getElementById('visualization'));
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
        data.addColumn('string', 'Location');
        data.addColumn('number', 'Total waste cost');
        <?php
        	if (is_array($result['costChart']->dc)) {
        		echo 'data.addRows(' . count($result['costChart']->dc) . ");\n";
        		foreach ($result['costChart']->dc as $k=>$value) {
        			echo 'data.setValue('.$k.',0,\''.$value[1]."');\n";
        			echo 'data.setValue('.$k.',1,'.$value[0].");\n";
        		}	
        	} else {
        ?>
        data.addRows(5);
        data.setValue(0, 0, 'DC');
        data.setValue(0, 1, <?php echo $result['costChart']->dc; ?>);
        data.setValue(1, 0, 'Stores (West)');
        data.setValue(1, 1, <?php echo $result['costChart']->West; ?>);
        data.setValue(2, 0, 'Stores (Southeast)');
        data.setValue(2, 1, <?php echo $result['costChart']->Southeast; ?>);
        data.setValue(3, 0, 'Stores (Midwest)');
        data.setValue(3, 1, <?php echo $result['costChart']->MidWest; ?>);
        data.setValue(4, 0, 'Stores (East)');
        data.setValue(4, 1, <?php echo $result['costChart']->East; ?>);
      <?php
      		}
      ?>
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('costs')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization2);
	  
	  
      function drawVisualization3() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Location');
        data.addColumn('number', 'Total waste tons');
        <?php
            	if (is_array($result['wasteChart']->dc)) {
            		echo 'data.addRows(' . count($result['wasteChart']->dc) . ");\n";
            		foreach ($result['wasteChart']->dc as $k=>$value) {
            			echo 'data.setValue('.$k.',0,\''.$value[1]."');\n";
            			echo 'data.setValue('.$k.',1,'.$value[0].");\n";
            		}	
            	} else {
        ?>
        data.addRows(5);
        data.setValue(0, 0, 'DC');
        data.setValue(0, 1, <?php echo $result['wasteChart']->dc; ?>);
        data.setValue(1, 0, 'Stores (West)');
        data.setValue(1, 1, <?php echo $result['wasteChart']->West; ?>);
        data.setValue(2, 0, 'Stores (Southeast)');
        data.setValue(2, 1, <?php echo $result['wasteChart']->Southeast; ?>);
        data.setValue(3, 0, 'Stores (Midwest)');
        data.setValue(3, 1, <?php echo $result['wasteChart']->MidWest; ?>);
        data.setValue(4, 0, 'Stores (East)');
        data.setValue(4, 1, <?php echo $result['wasteChart']->East; ?>);
      <?php
      	 		} 
      ?>
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('wastes')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization3);
    </script>
    
      <div class="content">
      <div class="row">&nbsp;</div>
    <div class="row">
          <div class="sixteen columns">
        <h1>Waste Report</h1>
      	<h5 style="float:left;padding-right:10px">
      		<span>Start Date: </span><?php echo set_value('from', date('m') . '/01/' . date('Y'));?>
		</h5>
		<h5 style="float:left;padding-right:10px">
			<span>End Date: </span><?php echo set_value('to', date('m/d/Y'));?>
		</h5>
		<h5 style="float:left;padding-right:10px">
        	<span>Show: </span><?php echo $data->forOptions[set_value('for', $data->for)];?>
        </h5>
        <hr>
        <div class="row">
        	<div class="seven columns">
            	<h5>Costs</h5>
            	<?php
            		if (!$result['costChart']->hasData()) {
            			echo '<span>No data available for selected period</span>';
            		} 
            	?>
            	<div id="costs" style="width: 350px; height: 200px;"></div>
          	</div>
       		<div class="seven columns">
            	<h5>Waste</h5>
            	<?php
            		if (!$result['wasteChart']->hasData()) {
            			echo '<span>No data available for selected period</span>';
            		} 
            	?>
            	<div id="wastes" style="width: 350px; height: 200px;"></div>
          	</div>
      	</div>
      	<div class="row">
            <div class="seven columns">
	            <h5>Waste Trends</h5>
    	        <div id="visualization" style="width: 400px; height: 200px;">
    	       		<br>
                </div>
          	</div>
      	</div>
      </div>
        </div>
    <div class="row">
          <div class="sixteen columns">
        <div><br>
              <br>
              <!--To style tr, use class="odd even gradeA gradeB gradeC gradeU gradeX"-->
              <table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
            <thead>
              <tr>
                    <th>Type</th>
                    <th> Name</th>
		    <th>State</th>
                    <th>SqFt</th>
                    <th>Waste (Tons)</th>
                    <th>Recycling (Tons)</th>
                    <th>Total Tonnage</th>
                    <th>Cost</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($result['list'] as $row):?>
              <tr class="odd gradeA">
                <td><?php echo $row->type; ?></td>
                <td><?php echo $row->name; ?></td>
		<td><?php echo $row->state; ?></td>
                <td><?php echo number_format($row->squareFootage, 2); ?></td>
                <td><?php echo number_format($row->waste, 2); ?></td>
                <td><?php echo number_format($row->recycling, 2); ?></td>
                <td><?php echo number_format(($row->recycling + $row->waste), 2); ?></td>
                <td><?php echo number_format($row->cost, 2); ?></td>
              </tr>
            <?php endforeach;?>
            </tbody>
            <tfoot>
                  <tr>
                    <th>Type</th>
                    <th>Locations</th>
		    <th>State</th>
                    <th>SqFt</th>
                    <th>Waste (Tons)</th>
                    <th>Recycling (Tons)</th>
                    <th>Total Tonnage</th>
                    <th>Cost</th>
              </tr>
                </tfoot>
          </table>
            </div>
      </div>
        </div>
  </div>
<?php include("application/views/admin/common/footer.php");?>
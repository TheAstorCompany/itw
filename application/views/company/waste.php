<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
 <script>
	$(function() {
		$('#servicelist').dataTable({});

		$('#wastelist').dataTable({
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
      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var countries =
          ['Distribution Centers', 'Stores (West)',
           'Stores (Southwest)', 'Stores (MidWest)', 'Stores (East)'];
        var months = <?php echo json_encode($wasteTrends->dates); ?>;
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
	    $flagCost = true;
	    if(isset($result['costChart']->stateCost)) {
			echo 'data.addRows(' . count($result['costChart']->stateCost) . ");\n";
			foreach($result['costChart']->stateCost as $k=>$value) {
				echo 'data.setValue('.$k.',0,\''.$value[1]."');\n";
				echo 'data.setValue('.$k.',1,'.$value[0].");\n";
			}
			$flagCost = false;
	    } else {
		
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
	    $flagWaste = true;
	    if(isset($result['wasteChart']->stateWaste)) {
			echo 'data.addRows(' . count($result['wasteChart']->stateWaste) . ");\n";
			foreach($result['wasteChart']->stateWaste as $k=>$value) {
				echo 'data.setValue('.$k.',0,\''.$value[1]."');\n";
				echo 'data.setValue('.$k.',1,'.$value[0].");\n";
			}
			$flagWaste = false;
	    } else {
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
	    }
      ?>
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('wastes')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization3);
    </script>
    
      <div class="content">
    <div class="row">
          <div class="sixteen columns"> <span style="float:right">
          <a href="<?php echo base_url(); ?>Company/Waste?<?php $_GET['print'] = 1; echo http_build_query($_GET);?>" class="button" target="_blank">Printer Friendly</a>
          <a href="<?php echo base_url(); ?>Company/Waste?<?php unset($_GET['print']); $_GET['export'] = 1; echo http_build_query($_GET);?>" class="button">Export CSV</a></span>
        <h1>Waste Report</h1>
        <form action="" method="get">
	      	<span style="float:left;padding-right:10px;width:106px;">
	      		<label for="from">Start Date</label>
				<input name="from" type="text" id="from" value="<?php echo set_value('from', date('m') . '/01/' . date('Y'));?>" style="width:100px"/>
			</span>
			<span style="float:left;padding-right:10px;width:106px;">
				<label for="to">End Date</label>
				<input name="to" type="text" id="to" value="<?php echo set_value('to', date('m/d/Y'));?>" style="width:100px"/>
			</span>
			<span style="float:left;padding-right:10px;width:220px;">
	        <label for="for">Show for</label>
	        <?php echo form_dropdown('for', $data->forOptions, set_value('for', $data->for));?>
	        </span>
		<span style="float:left;padding-right:10px;width:220px;">
			<label for="for">By State</label>
			<?php echo form_dropdown('bystate', $data->byState, set_value('bystate', $data->bystate));?>
		</span>
	        <div style="float:left; margin-top: 15px;">
	        	<button type="submit">Update</button>
	        </div>
        </form>
        <hr>
        <div class="row">
        	<div class="four columns">
            	<h5>Costs</h5>
            	<?php
            		if (!$result['costChart']->hasData() && $flagCost) {
            			echo '<span>No data available for selected period</span>';
            		} 
            	?>
            	<div id="costs" style="width: 300px; height: 200px;"></div>
          	</div>
       		<div class="four columns">
            	<h5>Waste</h5>
            	<?php
            		if (!$result['wasteChart']->hasData() && $flagWaste) {
            			echo '<span>No data available for selected period</span>';
            		} 
            	?>
            	<div id="wastes" style="width: 300px; height: 200px;"></div>
          	</div>
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
                <td><a href="<?php echo (($row->type == 'DC') ? base_url() . 'Dcinfo/Waste/' . $row->id : base_url() . 'Regioninfo/Waste/' . $row->name) ?>"><?php echo $row->name; ?></a></td>
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
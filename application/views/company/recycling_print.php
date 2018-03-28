<?php include("application/views/admin/common/header_print.php");?>
<script>
	$(function() {
		$('#recyclelist').dataTable( {
			"bPaginate": false,
					"bFilter": false
				} );
	});
	</script>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      function drawVisualization() {
        var countries =
          ['Distribution Centers', 'Stores (West)',
           'Stores (Southwest)', 'Stores (MidWest)', 'Stores (East)'];
        var months = <?php echo json_encode($recyclingTrends->dates); ?>;
        var productionByCountry = [<?php echo json_encode($recyclingTrends->dc, JSON_NUMERIC_CHECK); ?>,
        						   <?php echo json_encode($recyclingTrends->West, JSON_NUMERIC_CHECK); ?>,
        						   <?php echo json_encode($recyclingTrends->Southeast, JSON_NUMERIC_CHECK); ?>,
        						   <?php echo json_encode($recyclingTrends->MidWest, JSON_NUMERIC_CHECK); ?>,
        						   <?php echo json_encode($recyclingTrends->East, JSON_NUMERIC_CHECK); ?>
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
        data.addColumn('number', 'Rebate');
        data.addRows(<?php echo count($result['rebatesChart']); ?>);

        <?php
        	$i = 0;
        	foreach ($result['rebatesChart'] as $name=>$value) {
        		echo 'data.setValue('.$i.', 0, "'.$name .'");'.PHP_EOL;
        		echo 'data.setValue('.$i.', 1, '.$value .');'.PHP_EOL;
        		$i++;
        	}
        ?>
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('savingschart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization2);
	  
	  
      function drawVisualization3() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Material');
        data.addColumn('number', 'Price');
        <?php
        	echo "data.addRows(".count($result['recyclingChart']).");\n";
        	
        	foreach ($result['recyclingChart'] as $k=>$item) {
        		echo "data.setValue($k,0, '{$item->name}');\n";
        		echo "data.setValue($k,1, {$item->materialPrice});\n";	
        	}
        ?>
        
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('invoicechart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization3);
    </script>


      <div class="content">
      <div class="row">&nbsp;</div>
    <div class="row">
          <div class="sixteen columns">
        <h1>Recycling Report</h1>
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
          <div class="four columns">
        	<h5>Rebates</h5>
	      	<?php
	      		$temp = 0;
	      		
	      		foreach ($result['rebatesChart'] as $i) {
	      			$temp += $i;
	      		}
	      		
	       		if (!$temp) {
	       			echo '<span>No data available for selected period</span>';
	       		} 
	       	?>
        	<div id="savingschart" style="width: 350px; height: 200px;"></div>
      	  </div>
	      <div class="four columns">
	        <h5>Recycling</h5>
	      	<?php
	       		if (empty($result['recyclingChart'])) {
	       			echo '<span>No data available for selected period</span>';
	       		}
	       	?>
	        <div id="invoicechart" style="width: 350px; height: 200px;"></div>
	      </div>
      </div>
      <div class="row">
        <div class="seven columns">
        	<h5>Recycling Trends</h5>
        	<div id="visualization" style="width: 400px; height: 200px;"></div>
      	</div>
      </div>
    <div class="row">
          <div class="sixteen columns"><br>
        <br>
        <br>
        <br>
        <!--To style tr, use class="odd even gradeA gradeB gradeC gradeU gradeX"-->
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="recyclelist" width="100%">
              <thead>
            <tr>
                  <th>Type</th>
                  <th>Name</th>
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
              <tbody>
			<?php foreach($result['list'] as $item) :?>
            <tr class="odd gradeA">
              	  <td><?php echo $item->type; ?></td>
                  <td><?php echo $item->name; ?></td>
                  <td><?php echo $item->sqft; ?></td>
                  <td><?php echo $item->cardboard; ?></td>
                  <td><?php echo $item->aluminum; ?></td>
                  <td><?php echo $item->film; ?></td>
                  <td><?php echo $item->plastic; ?></td>
                  <td><?php echo $item->trees; ?></td>
                  <td>-</td>
                  <td><?php echo $item->kwh; ?></td>
                  <td><?php echo $item->co2; ?></td>
                  <td>$<?php echo $item->rebate; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
              <tfoot>
            <tr>
                  <th>#</th>
                  <th>Locations</th>
                  <th>SqFt</th>
                  <th>390,800 Cardboard</th>
                  <th>Aluminum</th>
                  <th>Film</th>
                  <th>Plastic</th>
                  <th>Trees</th>
                  <th>Landfill</th>
                  <th>Energy (KWh)</th>
                  <th>Co2 (Tons)</th>
                  <th>Rebate</th>
                </tr>
          </tfoot>
            </table>
      </div>
        </div>
  </div>
<?php include("application/views/admin/common/footer.php");?>
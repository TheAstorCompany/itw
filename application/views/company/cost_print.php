<?php include("application/views/admin/common/header_print.php");?>

<script>
	$(function() {
		$('#costlist').dataTable( {
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
    <?php
    	if (isset($chart3['regions']) && !empty($chart3['regions'])) { 
    ?>
      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var countries = <?php echo $chart3['regions']?>;
        var months = <?php echo $chart3['months']?>;
        var productionByCountry = <?php echo $chart3['data']?>;
      
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
          vAxis: {title: "Net Cost"},
          hAxis: {title: "Month"}
        });
      }
      

      google.setOnLoadCallback(drawVisualization);
      <?php
      }
      ?>

      function drawVisualization2() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        <?php if(isset($chart2)) {
        	//saving
        	printf("data.addRows(%d);\n", count($chart2));
        	foreach($chart2 as $k=>$_item) {
        		printf("data.setValue(%d, 0, '%s');\n", $k, $_item->Name);
        		printf("data.setValue(%d, 1, %d)\n", $k, $_item->value);
        	}
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
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
       	<?php if(isset($chart1)) {
        	//cost
       		printf("data.addRows(%d);\n", count($chart1));
        	foreach($chart1 as $k=>$_item) {
        		printf("data.setValue(%d, 0, '%s');\n", $k, $_item->Name);
        		printf("data.setValue(%d, 1, %d)\n", $k, $_item->value);
        	}
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
    <div class="row">
          <div class="four columns">
        <h5>Cost</h5>
        <div id="invoicechart" style="width: 350px; height: 200px;"></div>
      </div>
          <div class="four columns">
        <h5>Savings</h5>
        <div id="savingschart" style="width: 350px; height: 200px;"></div>
      </div>
          <div class="seven columns">
        <h5>Net Trends</h5>
        <div id="visualization" style="width: 400px; height: 200px;"><br>
            <?php
		    	if (isset($chart3['regions']) && !empty($chart3['regions'])) {
		    		echo '<strong>No data available<strong>';
		    	} 
	    	?>
            </div>
      </div>
        </div>
    <div class="row">
          <div class="sixteen columns">
        <div><br>
              <br>
              <br>
              <!--To style tr, use class="odd even gradeA gradeB gradeC gradeU gradeX"-->
              <table cellpadding="0" cellspacing="0" border="0" class="display" id="costlist" width="100%">
            <thead>
                  <tr>
                <th>Type</th>
                <th>Name</th>
                <th>SqFt</th>
                <th>Total Tonnage</th>
                <th> Waste<br>
                  Service</th>
                <th>Waste <br>
                  Equipment Fee</th>
                <th>Waste<br>
                  Haul Fee</th>
                <th>Waste <br>
                  Disposal Fee</th>
                <th>Recycling Rebate</th>
                <th>Other  Fee</th>
                <th>Net</th>
                </tr>
                </thead>
            <tbody>
            <?php foreach($table as $k=>$item) { 
            	@$total['region'][$item->region] = $item->region;
            	@$total['district'][$item->district] = $item->district;
            	@$total['location'][$item->location] = $item->location;
            	@$total['squareFootage'] += $item->squareFootage;
            	@$total['TotalTonnage'] += $item->TotalTonnage;
            	@$total['WasteService'] += $item->WasteService;
            	@$total['WasteEquipmentFee'] += $item->WasteEquipmentFee;
            	@$total['WasteHaulFee'] += $item->WasteHaulFee;
            	@$total['WasteDisposalFee'] += $item->WasteDisposalFee;
            	@$total['RecyclingRebate'] += $item->RecyclingRebate;
            	@$total['OtherFee'] += $item->OtherFee;
            	@$total['net'] += $item->net;            	
            	?>
                <tr class="gradeA">
                    <td><?php echo $item->type;?></td>
              		<td><?php echo $item->region ?></td>
              		<td><?php echo number_format($item->squareFootage,2);?></td>
              		<td><?php echo number_format($item->TotalTonnage,2);?></td>               	
                	<td><?php echo number_format($item->WasteService,2);?></td>
                	<td><?php echo number_format($item->WasteEquipmentFee,2);?></td>
                	<td><?php echo number_format($item->WasteHaulFee,2);?></td>
                	<td><?php echo number_format($item->WasteDisposalFee,2);?></td>
                	<td><?php echo number_format($item->RecyclingRebate,2);?></td>
                	<td><?php echo number_format($item->OtherFee,2);?></td>
                	<td><?php echo number_format($item->net,2);?></td>                	
              	</tr>
              	<?php } ?>   
            </tbody>
            <tfoot>
                  <tr>
                  	<th>Type</th>
                    <th><?php echo count(@$total['location']); ?> Name</th>
                    <th><?php echo number_format(@$total['squareFootage'],2); ?> SqFt</th>
                    <th><?php echo number_format(@$total['TotalTonnage'],2); ?> Total Tonnage</th>
                  	<th><?php echo number_format(@$total['WasteService'],2); ?> Waste<br>
                    Service</th>
                  	<th><?php echo number_format(@$total['WasteEquipmentFee'],2); ?> Waste <br>
                    Equipment Fee</th>
                  	<th><?php echo number_format(@$total['WasteHaulFee'],2); ?> Waste<br>
                    Haul Fee</th>
                  	<th><?php echo number_format(@$total['WasteDisposalFee'],2); ?> Waste <br>
                    Disposal Fee</th>
                  	<th><?php echo number_format(@$total['RecyclingRebate'],2); ?> Recycling Rebate</th>
                  	<th><?php echo number_format(@$total['OtherFee'],2); ?> Other Fee</th>
                    
                    <th>$<?php echo number_format(@$total['net'],2);?> Net</th>
                </tr>
                </tfoot>
          </table>
            </div>
      </div>
        </div>
  </div>
<?php include("application/views/admin/common/footer.php");?>
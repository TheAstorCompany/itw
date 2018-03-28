<?php include("application/views/admin/common/header_print.php");?>	
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var countries = <?php echo $chart3['countries']?>;
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
          height: 200,
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
        new google.visualization.PieChart(document.getElementById('costschart')).
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
        new google.visualization.PieChart(document.getElementById('wastechart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization3);
    </script>

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
        		<span>Display: </span><?php echo @$data->forOptions[$data->display]?>
        	</h5>          	           	
        <hr>
		</div>		
		</div>
                <div class="row">
                <div class="row"><div class="four columns">
            <h5>Costs</h5>
            <div id="costschart" style="width: 350px; height: 200px;"></div>
          </div>
              <div class="four columns">
            <h5>Waste</h5>
            <div id="wastechart" style="width: 350px; height: 180px;"></div></div>
            <div class="eight columns omega">
             <h5>Waste Trends</h5><div id="visualization" style="width: 400px; height: 200px;">
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
                    	<th>Region</th>
                		<th>District</th>
                		<th>Location</th>
				<th>State</th>
                		<th>24H</th>
                		<th>SqFt</th>
                		<th>Waste (Tons)</th>
                		<th>Hazardous (Tons)</th>
                		<th>Other (Tons)</th>
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
            	@$total['waste'] += $item->EnergySaves;
            	@$total['hazardous'] += $item->CO2Saves;
            	@$total['other'] += $item->CO2Saves;
            	@$total['cost'] += $item->cost;
            	?>
                <tr class="<?php echo $k%2?'even':'odd';?> gradeA">
                    <td><?php echo $item->region?></td>
              		<td><?php echo $item->district?></td>
              		<td><?php echo $item->location?></td>
			<td><?php echo $item->state; ?></td>  
              		<td><?php echo $item->open24hours?'Y':'N';?></td>                	
                	<td><?php echo number_format($item->squareFootage,2);?></td>
                	<td><?php echo number_format(@$item->items['Waste'],2);?></td>
                	<td><?php echo number_format(@$item->items['Hazardous'],2);?></td>
                	<td><?php echo number_format(@$item->items['Other'],2);?></td>
                	<td><?php echo number_format($item->cost,2);?></td>
              	</tr>
              	<?php } ?>                
            </tbody>
            <tfoot>
 				<tr>
                    <th><?php echo count(@$total['region']); ?> Regions</th>
                    <th><?php echo count(@$total['district']); ?> Districts</th>
                    <th><?php echo count(@$total['location']); ?> Locations</th>
		    <th><?php echo count(@$total['state']); ?> States</th>
                    <th>&nbsp;</th>
                    <th><?php echo number_format(@$total['squareFootage'],2); ?> SqFt</th>
                    <th>1000 Waste (Tons)</th>
                    <th>2000 Hazardous (Tons)</th>
                    <th>3000 Other (Tons)</th>
                    <th>$<?php echo number_format(@$total['cost'],2);?> Cost</th>
                </tr>
           	</tfoot>
          	</table>                 
            </div>
      		</div>
        </div>
	</div>
</div>
        
<?php include("application/views/admin/common/footer.php");?>
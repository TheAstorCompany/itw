<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

 	<script>
	$(function() {
		$('#tabs').tabs();
		/*
		$('#recyclelist').dataTable( {
					"bPaginate": false,
					"bFilter": false
		} );
		*/
		var dates = $( "#from, #to" ).datepicker({
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
        new google.visualization.PieChart(document.getElementById('rebateschart')).
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
        new google.visualization.PieChart(document.getElementById('recyclingchart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization3);
    </script>

   <div class="row"><div class="sixteen columns">
          <span style="float:right">          	
          	<a href="<?php echo base_url();?>Stores/Recycling?<?php echo http_build_query(array_merge($_GET, array('print'=>1))); ?>" class="button" target="_blank">Printer Friendly</a> 
          	<a href="<?php echo base_url();?>Stores/Recycling?<?php echo http_build_query(array_merge($_GET, array('export'=>1))); ?>" class="button">Export CSV</a></span><h1>Stores Recycling</h1>
          	<form action="" method="get">
          	<span style="float:left;padding-right:10px;width:106px;"><label for="from">Start Date</label>
			<input name="from" type="text" id="from" value="<?php echo set_value('from', date('m') . '/01/' . date('Y'));?>" style="width:100px"/></span><span style="float:left;padding-right:10px;width:106px;">
			<label for="to">End Date</label>
			<input name="to" type="text" id="to" value="<?php echo set_value('to', date('m/d/Y'));?>" style="width:100px"/></span><span style="float:left;padding-right:10px;width:220px;">
            <label for="for">Display</label>            
        	<?php echo form_dropdown('display', $data->displayOptions, set_value('display', $data->display));?>
            </span><span style="float:left;padding-right:10px;width:220px;"><label for="report">Chart for</label>
            <?php echo form_dropdown('for', $data->forOptions, set_value('for', $data->for));?>            
        	</span>
		<span style="float:left;padding-right:10px;width:220px;">
		    <label for="for">State</label>
		    <?php echo form_dropdown('bystate', $data->byState, set_value('bystate', $data->bystate));?>
		</span>
        	 <div style="float:left; margin-top: 15px;">
                        <button type="submit">Update</button>
                </div>
        	</form>          	
          <hr>
</div></div>         
           <div class="row">
           <div class="four columns">
        <h5>Rebates</h5>
        <div id="rebateschart" style="width: 350px; height: 200px;"></div>
      </div><div class="four columns">
                      <h5>All Recycling</h5>
                      <div id="recyclingchart" style="width: 350px; height: 180px;"></div>
                    </div>
          <div class="eight columns omega"><h5>All Recycling Trend</h5><div id="visualization" style="width: 400px; height: 300px;"></div></div>
		  
		   </div> 
        <div class="row">
          <div class="sixteen columns">
			  <!--
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="recyclelist" width="100%">
              <thead>
            <tr>
              <th>Region</th>
                  <th>District</th>
                  <th>Location</th>
		  <th>State</th>
                  <th>24H</th>
                  <th>SqFt</th>
                  <?php foreach($mainMaterials as $mat) {?>
                  <th><?php echo $mat; ?></th>
                  <?php } ?>
                  <th>Energy (KWh)</th>
                  <th>Co2 (Tons)</th>
                  <th>Rebate</th>
                </tr>
          </thead>
            <tbody>
            <?php foreach($table as $k=>$item) {
      			
            	@$total['region'][$item->region] = $item->region;
            	@$total['district'][$item->district] = $item->district;
            	@$total['location'][$item->location] = $item->location;
				@$total['state'][$item->state] = $item->state;
            	@$total['squareFootage'] += $item->squareFootage;
            	@$total['EnergySaves'] += $item->EnergySaves;
            	@$total['CO2Saves'] += $item->CO2Saves;
            	@$total['rebate'] += $item->rebate;
            ?>
            <tr class="<?php echo $k%2?'even':'odd';?> gradeA">
              <td><a href="<?php echo base_url()?>Regioninfo/Recycling/<?php echo $item->region?>"><?php echo $item->region?></a></td>
              <td><a href="<?php echo base_url()?>Districtinfo/Recycling/<?php echo $item->district?>"><?php echo $item->district?></a></td>
              <td><a href="<?php echo base_url()?>StoreInfo/Recycling/<?php echo $item->id?>"><?php echo $item->location?></a></td>
	      <td><?php echo $item->state; ?></td> 
              <td><?php echo $item->open24hours?'Y':'N';?></td>
              <td><?php echo number_format($item->squareFootage,2);?></td>
                  <?php foreach($mainMaterials as $matId=>$mat) { 
                  @$total['materials'][$mat] += $item->materials[$matId];
                  ?>
                  <td><?php echo isset($item->materials[$matId])?$item->materials[$matId]:'-'; ?></td>
                  <?php } ?>
                  <td><?php echo number_format($item->EnergySaves,2);?></td>
                  <td><?php echo number_format($item->CO2Saves,2);?></td>
                  <td>$<?php echo number_format($item->rebate,2);?></td>
            </tr>
            <?php }  ?>                        
          	</tbody>
          <tfoot>
            <tr>
              <th><?php echo count(@$total['region']); ?> Regions</th>
                  <th><?php echo count(@$total['district']); ?> Districts</th>
                  <th><?php echo count(@$total['location']); ?> Locations</th>
					<th><?php echo count(@$total['state']); ?> States</th>
                  <th>&nbsp;</th>
                  <th><p><?php echo number_format(@$total['squareFootage'],2); ?> SqFt</p></th>
                  <?php foreach($mainMaterials as $matId=>$mat) {?>
                  <th><?php echo number_format(@$total['materials'][$mat], 2); echo " " .$mat;?></th>
                  <?php } ?>
                  <th><?php echo number_format(@$total['EnergySaves'],2); ?> Energy (KWh)</th>
                  <th><?php echo number_format(@$total['CO2Saves'],2); ?> Co2 (Tons)</th>
                  <th>$<?php echo number_format(@$total['rebate'],2); ?> Rebate</th>
			</tr>
          </tfoot>
        </table>
			  -->
			</div>
		</div>
	</div>
<?php include("application/views/admin/common/footer.php");?>
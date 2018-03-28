<?php include("application/views/admin/common/header_print.php");?>

 <script>
	$(function() {
		$('#tabs').tabs();
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
        // Some raw data (not necessarily accurate)
        var countries = <?php echo json_encode($DCNames) ?>

        var months = <?php echo json_encode($RecyclingMonth) ?>;
        var productionByCountry = <?php echo json_encode($OrderedTrend)?>;
      
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
        data.addRows(<?php echo count($DCList)?>);
        <?php
        	 $i = 0;
        	 foreach($DCList as $temp) {?>
        	data.setValue(<?php echo $i?>, 0, '<?php echo $temp["name"]?>');
        	data.setValue(<?php echo $i?>, 1, <?php echo $temp["total"]?>);
        <?php
        	$i++;
		}?>

        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('savingschart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization2);
	  
	  
      function drawVisualization3() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Recycling Materials');
        data.addRows(<?php echo count($AllRecyclingMaterials)?>);

        <?php
        	$i = 0; 
        	foreach ($AllRecyclingMaterials as $temp) {
        ?>
	        data.setValue(<?php echo $i?>, 0, '<?php echo $temp["name"]?>');
    	    data.setValue(<?php echo $i?>, 1, <?php echo $temp["quantity"]?>);
		<?php $i++; }?>

      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('invoicechart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization3);




      //Recycling
		$(function() {
			var dates = $( "#from, #to" ).datepicker({
				defaultDate: "+1w",
				dateFormat: "mm/dd/yy",
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
    
 	<div class="row" style="padding-top: 50px">
          <div class="sixteen columns">
        <h1>Recycling Report</h1>
      	<h5 style="float:left;padding-right:10px">
      		<span>Start Date: </span><?php echo set_value('from', date('m') . '/01/' . date('Y'));?>
		</h5>
		<h5 style="float:left;padding-right:10px">
			<span>End Date: </span><?php echo set_value('to', date('m/d/Y'));?>
		</h5>
		<h5 style="float:left;padding-right:10px">
        	<span>Distribution Centers: </span><?php if($DC) echo $AllDC[$DC]["name"]; else echo "All"?>
        </h5>
        <h5 style="float:left;padding-right:10px">
        	<span>Material: </span><?php if ($Materials) echo $AllRecyclingMaterials[$Materials]["name"]; else echo "All Recycling";?>
        </h5>
        <hr>
				<h5>All Recycling</h5>
                <div id="invoicechart" style="width: 350px; height: 180px;"></div>
                <h5>Rebates</h5>
        		<div id="savingschart" style="width: 350px; height: 200px;"></div>
				<h5>All Recycling Trend</h5><div id="visualization" style="width: 400px; height: 300px;"></div></div>        		
        </div>

           <div class="row">
           
          
        <div class="row">
          <div class="sixteen columns">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="recyclelist" width="100%">
              <thead>
            <tr>
                  <th>Location</th>
                  <th>SqFt</th>
                  <?php
                  foreach($AllRecyclingMaterials as $temp) {
                  	echo "<th>{$temp["name"]}</th>";
                  }?>
                  <th>Energy (KWh)</th>
                  <th>Co2 (Tons)</th>
                  <th>Rebate</th>
                </tr>
          </thead>
              <tbody>
            <?php $i = 0;
            foreach($DCList as $temp) {?>
            <tr class=" <?php if($i%2):?>odd<?php else: ?>even<?php endif;?> gradeA">
                  <td><a href="<?=base_url()?>Dcexample/DistributionCenters/Recycling/<?php echo $temp["id"];?>#tabs-2"><?php echo $temp["name"]; ?> </a></td>
                  <td><?php echo $temp["sqft"];?></td>
                  <?php foreach($AllRecyclingMaterials as $tmp) {
                  	if(isset($temp["material_".$tmp["id"]])) {
                  		echo "<td>{$temp["quantity_".$tmp["id"]]}</td>"; 
                  	} else {
                  		echo "<td> - </td>";	
                  	}
                  }
                  $i++;
                  ?>                  
                  <td><?php echo $temp["energy"];?></td>
                  <td><?php echo $temp["co"];?></td>
                  <td><?php echo $temp["total"]; ?></td>
                </tr>
             <?php }?>
          </tbody>
              <tfoot>
            <tr>
                  <th><?php echo $i;?> DCs</th>
                  <th><?php echo $allData["sqft"]; ?> SqFt</th>
                  <?php foreach($AllRecyclingMaterials as $tmp) { 
                  	echo "<th>{$tmp["quantity"]} {$tmp["name"]}</th>";
                  	
                  }?>
                  <th><?php echo $allData["energy"]; ?> (KWh)</th>
                  <th><?php echo $allData["co"]; ?> Co2 (Tons)</th>
                  <th>$<?php echo $allData["rebate"]; ?> Rebate</th>
                </tr>
          </tfoot>
          </table></div></div>
        
        
        </div>
        
<?php include("application/views/admin/common/footer.php");?>
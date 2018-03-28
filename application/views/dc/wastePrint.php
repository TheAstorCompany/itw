<?php include("application/views/admin/common/header_print.php");?>



 <script>
	$(function() {
		$('#tabs').tabs();
		$('#wastelist').dataTable( {
					"bPaginate": false,
					"bFilter": false
				} );
	});
	</script>
        <script>
	$(function() {
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
        var countries = <?php echo json_encode($DCNames) ?>;
        var months = <?php echo json_encode($WasteMonth) ?>;
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
        var ac = new google.visualization.LineChart(document.getElementById('visualization'));
        ac.draw(data, {
          title : '',
          isStacked: true,
          width: 600,
          height: 200,
          vAxis: {title: "Tons"},
          hAxis: {title: "DC"}
        });
      }
      

      google.setOnLoadCallback(drawVisualization);

      function drawVisualization2() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows(<?php echo count($dataList)?>);
        <?php
        $i = 0; 
        foreach ($dataList as $temp) {?>
	        data.setValue(<?php echo $i?>, 0, '<?php echo $temp["locationName"]?>');
	        data.setValue(<?php echo $i?>, 1, <?php echo $temp["cost"]?>);
        <?php $i++; }?>
      
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
        data.addRows(3);
        data.setValue(0, 0, 'Waste');
        data.setValue(0, 1, <?php echo $sumData["waste"]?>);
        data.setValue(1, 0, 'Hazardous');
        data.setValue(1, 1, <?php echo $sumData["hazardous"]?>);
        data.setValue(2, 0, 'Other');
        data.setValue(2, 1, <?php echo $sumData["waste"] - $sumData["hazardous"]?>);
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('wastechart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization3);
    </script>
    
    
    
    
<div class="row" style="padding-top: 50px">
          <div class="sixteen columns">
        <h1>Waste Report</h1>
      	<h5 style="float:left;padding-right:10px">
      		<span>Start Date: </span><?php echo set_value('from', date('m') . '/01/' . date('Y'));?>
		</h5>
		<h5 style="float:left;padding-right:10px">
			<span>End Date: </span><?php echo set_value('to', date('m/d/Y'));?>
		</h5>
		<h5 style="float:left;padding-right:10px">
        	<span>Distribution Centers: </span><?php if($DC) echo $AllDC[$DC]["name"]; else echo "All"?>
        </h5>
        <hr>
            <h5>Costs</h5>
            <div id="costschart" style="width: 350px; height: 200px;"></div>
            <h5>Waste Types</h5>
            <div id="wastechart" style="width: 350px; height: 180px;"></div></div>
				<h5>Waste Trends</h5><div id="visualization" style="width: 400px; height: 160px;">        		
        </div>    
    
    
        
    
                <div class="row">
        <div class="row">
          <div class="sixteen columns">
        <div><br>
              <br>
              <!--To style tr, use class="odd even gradeA gradeB gradeC gradeU gradeX"-->
              <table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
            <thead>
                  <tr>
                <th>Location</th>
                <th>SqFt</th>
                <th>Waste (Tons)</th>
                <th>Hazardous (Tons)</th>
                <th> Other (Tons)</th>
                <th>Cost</th>
                </tr>
                </thead>
            <tbody>
            <?php foreach($dataList as $temp) {?>
                <tr class="gradeA">
	                <td><a href="<?php echo base_url()?>Dcexample/DistributionCenters/Waste#tabs-1"><?php echo $temp["locationName"]?> </a></td>
	                <td><?php echo $temp["sqft"]?></td>
	                <td><?php echo $temp["waste"]?></td>
	                <td><?php echo $temp["hazardous"]?></td>
	                <td><?php echo $temp["waste"] - $temp["hazardous"]?></td>
	                <td><?php echo $temp["cost"]?></td>
                </tr>
             <?php }?>
                </tbody>
            <tfoot>
                  <tr>
                    <th><?php echo count($dataList)?> DCs</th>
                    <th><?php echo $sumData["sqft"]?> SqFt</th>
                    <th><?php echo $sumData["waste"]?> Waste (Tons)</th>
                    <th><?php echo $sumData["hazardous"]?> Hazardous (Tons)</th>
                    <th><?php echo $sumData["waste"] - $sumData["hazardous"]?> Other (Tons)</th>
                    <th><?php echo $sumData["cost"] ?> Cost</th>
                  </tr>
                </tfoot>
          </table>
            </div>
      </div>
        </div>
       </div>
        
<?php include("application/views/admin/common/footer.php");?>
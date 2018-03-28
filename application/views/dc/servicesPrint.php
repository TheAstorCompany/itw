<?php include("application/views/admin/common/header_print.php");?>


   <script>
	$(function() {
		$('#servicelist').dataTable( {
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
     
      function drawVisualization2() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows(<?php echo count($allSum["ct"])?>);
        <?php
        	$i = 0; 
        	foreach($allSum["ct"] as $key=>$temp) {?>
        	data.setValue(<?php echo $i?>, 0, '<?php echo $key?>');
        	data.setValue(<?php echo $i?>, 1, <?php echo $temp?>);
        <?php $i++; }?>

      
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
        data.addRows(<?php echo count($allSum["duration"])?>);
        <?php 
        	$i = 0;
        	foreach ($allSum["duration"] as $key=>$temp) {
        ?>
	        data.setValue(<?php echo $i?>, 0, '<?php echo $key?>');
    	    data.setValue(<?php echo $i?>, 1, <?php echo $temp?>);
        <?php $i++; }?>
      
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
        data.addRows(<?php echo count($allSum["frequency"])?>);
        <?php
        	$i = 0; 
        foreach($allSum["frequency"] as $key=>$temp) {?>
        data.setValue(<?php echo $i?>, 0, '<?php echo $key?>');
        data.setValue(<?php echo $i?>, 1, <?php echo $temp?>);
        <?php $i++;}?>

      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('frequencychart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization4);
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
        <hr>
				<h5>Type of Containers</h5>
                <div id="servicechart" style="width: 380px; height: 200px;"></div>
                <h5>Duration of Service</h5>
        		<div id="timechart" style="width: 380px; height: 200px;"></div>
				<h5>Frequency of Service</h5>        	
				<div id="frequencychart" style="width: 380px; height: 200px;"></div>	
        </div>
        
       <div class="row"><div class="sixteen columns"><div>
       <!--To style tr, use class="odd even gradeA gradeB gradeC gradeU gradeX"-->
       <table cellpadding="0" cellspacing="0" border="0" class="display" id="servicelist" width="100%">
	<thead>
		<tr>
			<th>Location</th>
            <th>SqFt</th>
            <th>Vendor</th>
            <th>Service Type</th>
            <th>Container</th>
            <th>Duration</th>
			<th>Frequency</th>
            <th>Cost</th>
			<th>Last Updated</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($data as $temp) {?>
  	<tr class="gradeA">
	    <td><a href="<?=base_url()?>Dcexample/DistributionCenters/Services#tabs-5"><?php echo $temp["name"]?></a></td>
	    <td><?php echo $temp["sqft"]?></td>
	    <td><?php echo $temp["vendorName"]?></td>
	    <td>Waste</td>
	    <td><?php echo $temp["containerType"]?></td>
	    <td><?php echo $temp["duration"]?></td>
	    <td><?php echo $temp["days"]?></td>
	    <td><?php echo $temp["total"]?></td>
	    <td><?php echo $temp["lu"]?></td>
    </tr>
    <?php }?>

	</tbody>
	<tfoot>
		<tr>
			<th>Location</th>
            <th><?php echo $allSum["sqft"]?> SqFt</th>
            <th><?php echo count($allSum["vendors"])?> Vendor</th>
            <th>1 Types</th>
            <th><?php echo count($allSum["ct"])?> Container Type</th>
            <th><?php echo count($allSum["duration"])?> Duration</th>
			<th><?php echo $allSum["fr"]?> Frequency</th>
            <th><?php echo $allSum["cost"]?> Cost</th>
			<th>Last Updated</td>
			  </tr>
	</tfoot>
</table></div></div></div>  </div>
        
        <footer>
        &nbsp;&copy; 2012 All rights reserved. The Astor Company. <a href="privacy.html">Privacy</a> & <a href="terms.html">Terms</a></footer>

	</article><!-- container -->

	<!-- JS
	================================================== -->
	<script src="javascripts/tabs.js"></script>

<!-- End Document
================================================== -->
</body>
</html>
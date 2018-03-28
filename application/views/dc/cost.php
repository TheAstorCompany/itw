<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

 <script>
	$(function() {
		$('#tabs').tabs();
		$('#costlist').dataTable( {
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
		var countries = <?php echo json_encode($DCNames) ?>

        var months = <?php echo json_encode($CostMonth) ?>;
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
        data.addRows(<?php echo count($data) ?>);

        <?php 
        	$i = 0;
        	foreach ($data as $temp) {
        ?>
        	
        data.setValue(<?php echo $i?>, 0, '<?php echo $temp["locationName"]?>');
        data.setValue(<?php echo $i?>, 1, <?php echo ($temp["rr"] + $temp["WTY"] - $temp["WLY"])?>);
        <?php $i ++; } ?>

      
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
        data.addRows(3);
        data.setValue(0, 0, 'WasteInvoice');
        data.setValue(0, 1, <?php echo $costGraph["wi"]?>);
        data.setValue(1, 0, 'RecyclingInvoice');
        data.setValue(1, 1, <?php echo $costGraph["rp"]?>);
        data.setValue(2, 0, 'RecyclingCharges');
        data.setValue(2, 1, <?php echo $costGraph["rc"]?$costGraph["rc"]:0 ?>);
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('invoicechart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization3);
    </script>

	<form action="" method="GET">
    <div class="row">
          <div class="sixteen columns"> <span style="float:right">
          	<a href="<?php echo base_url()?>DistributionCenters/Cost?<?php $_GET['print'] = 1; echo http_build_query($_GET);?>" class="button" target="_blank">Printer Friendly</a>
       		   <a href="<?php echo base_url()?>DistributionCenters/Cost?<?php unset($_GET['print']); $_GET['export'] = 1; echo http_build_query($_GET);?>" class="button">Export CSV</a></span>
        <h1>DC Cost/Savings</h1>
        <span style="float:left;padding-right:10px">
            <label for="from">Start Date</label>
            <input name="from" type="text" id="from" value="<?php if(isset($from)) echo $from;?>" style="width:100px"/>
            </span><span style="float:left;padding-right:10px">
            <label for="to">End Date</label>
            <input name="to" type="text" id="to" value="<?php if(isset($to)) echo $to;?>" style="width:100px"/>
            </span><span style="float:left;padding-right:10px">
            <label for="for">Distribution Centers</label>
            <select name="allDC" id="allDC"  style="float:left">
	         	<option value="">All</option>
	         	<?php foreach($AllDC as $temp) {
	         			if($DC == $temp["id"]) {
	         				echo "<option selected=\"selected\" value=\"{$temp["id"]}\">{$temp["name"]}</option>";
	         			} else {
	          				echo "<option value=\"{$temp["id"]}\">{$temp["name"]}</option>";
	         			}
	         		}
	          	?>
	          </select>
	            <div style="float:left; padding-left: 5px; margin-top: -5px">
                        <button type="submit">Update</button>
                </div>
            </span>
        <hr>
      </div>
        </div>
        </form>
        
        
    <div class="row">
          <div class="four columns">
        <h5>Cost</h5>
        <div id="invoicechart" style="width: 350px; height: 180px;"></div>
      </div>
          <div class="four columns">
        <h5>Savings</h5>
        <div id="savingschart" style="width: 350px; height: 180px;"></div>
      </div>
          <div class="eight columns omega">
        <h5>Net Trends</h5>
        <div id="visualization" style="width: 700px; height: 300px;"></div>
      </div>
        </div>
    <div class="row">
          <div class="sixteen columns">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="costlist" width="100%">
              <thead>
            <tr>
                  <th>Location</th>
                  <th>SqFt</th>
                  <th>Period</th>
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
              <?php foreach($data as $temp) {?>
            <tr class="gradeA">
                  <td><a href="<?php echo base_url()?>Dcinfo/CostSavings/<?php echo $temp["locationId"] ?>"><?php echo $temp["locationName"]?> </a></td>
                  <td><?php echo $temp["sqft"] ?></td>
                  <td><?php echo $to?></td>
                  <td><?php echo $temp["t"]?></td>
                  <td>$ <?php echo $temp["ws"]?></td>
                  <td>$ <?php echo $temp["we"]?></td>
                  <td>$ <?php echo $temp["wh"]?></td>
                  <td>$ <?php echo $temp["wd"]?></td>
                  <td>$ <?php echo isset($temp["rr"])?$temp["rr"]:0?></td>
                  <td>$ <?php echo $temp["o"]?></td>
                  <td>$ <?php if(isset($temp["rr"])) {$s = $temp["rr"]-$temp["ws"]-$temp["rrf"]; if($s > 0){ echo $s; $allSum["n"]+=$s;} else echo "0";} else {echo "0"; }?></td>
                </tr>
                <?php }?>
          </tbody>
              <tfoot>
            <tr>
                  <th><?php echo count($data)?> Dcs</th>
                  <th><?php echo $allSum["sqft"]?> SqFt</th>
                  <th>Period</th>
                  <th>Total Tonnage</th>
                  <th>$<?php echo $allSum["ws"]?> Service Fee</th>
                  <th><?php echo $allSum["we"]?><br>Equipment Fee</th>
                  <th>$<?php echo $allSum["wh"]?> Haul Fee</th>
                  <th>$<?php echo $allSum["wd"]?> Disposal Fee</th>
                  <th>$20,000 Rebate</th>
                  <th><?php echo $allSum["o"]?><br>
                  Other Fee</th>
                  <th>$<?php echo $allSum["n"]?> Net</th>
                </tr>
          </tfoot>
            </table>
      </div>
        </div>
  </div>
<?php include("application/views/admin/common/footer.php");?>
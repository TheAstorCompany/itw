<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>


 <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var countries = <?php echo json_encode($trend->materials);?>;
        var months = <?php echo json_encode($trend->dates);?>;
        var productionByCountry = <?php echo json_encode($trend->data, JSON_NUMERIC_CHECK);?>;
   
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
        data.addColumn('string', 'Service Name');
        data.addColumn('number', 'Requests');
        <?php
        	echo 'data.addRows('. count($requestServices) . ');' . PHP_EOL;
        	foreach ($requestServices as $i=>$item) {
        		echo 'data.setValue('.$i.', 0, "'.$item->name.'");' . PHP_EOL;
        		echo 'data.setValue('.$i.', 1, '.($item->c ? $item->c : '0').');' . PHP_EOL;
        	}
        ?>
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('servicechart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization2);
	  
	  
      function drawVisualization3() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Service');
        data.addColumn('number', 'Cost of service');
        <?php
            	echo 'data.addRows('. count($costOfServices) . ');' . PHP_EOL;
            	foreach ($costOfServices as $i=>$item) {
            		echo 'data.setValue('.$i.', 0, "'.$item->Name.'");' . PHP_EOL;
            		echo 'data.setValue('.$i.', 1, '.($item->value ? $item->value : '0').');' . PHP_EOL;
            	}
        ?>
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('invoicechart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization3);
    </script>



      <div class="content">
    <div class="row">
          <div class="sixteen columns">
        <h1>Walgreens at a Glance</h1></div>
        </div>
    <div class="row" style="border-bottom:1px solid #ddd">
          <div class="seven columns">
        <h2>Current Month</h2>
        <table width="100%" border="0" cellspacing="10" cellpadding="20">
              <tr style="border-bottom:1px solid #ddd">
            <td>Waste
                  <h4><?php echo $monthData->waste; ?> tons</h4></td>
            <td>Recycling
                  <h4><?php echo $monthData->recycling; ?> tons</h4></td>
            <td>Diversion rate
                  <h4><?php echo $monthData->diversion; ?>%</h4></td>
          </tr>
              <tr>
            <td>Cost
                  <h4>$<?php echo $monthData->cost; ?></h4></td>
            <td>Rebate
                  <h4>$<?php echo $monthData->rebate; ?></h4></td>
            <td>Cost per sqft
                  <h4>$<?php echo $monthData->costPerSqft;?></h4></td>
          </tr>
            </table>
        <br>
        <h2>
        	<form name="period_form" action="<?php echo base_url();?>Company" method="post">
        		<input type="hidden" id="id_performance" name="performance" value="<?php echo set_value('performance', 1)?>" />
        		<?php echo form_dropdown('period', $data->periodOptions, set_value('period', 1));?>
	        </form>
        </h2>
        <table width="100%" border="0" cellspacing="10" cellpadding="20">
              <tr style="border-bottom:1px solid #ddd">
            <td>Waste
                  <h4><?php echo $periodData->waste; ?> tons</h4></td>
            <td>Recycling
                  <h4><?php echo $periodData->recycling; ?> tons</h4></td>
            <td>Diversion rate
                  <h4><?php echo $periodData->diversion; ?>%</h4></td>
          </tr>
              <tr>
            <td>Cost
                  <h4>$<?php echo $periodData->cost; ?></h4></td>
            <td>Savings
                  <h4><?php if ($periodData->savings < 0):?>-<?php endif;?>$<?php echo number_format(abs($periodData->savings), 3); ?></h4></td>
            <td>Cost per sqft
                  <h4>$<?php echo $periodData->costPerSqft;?></h4></td>
          </tr>
            </table>
      </div>
          <div class="nine columns omega">
        <h5>Waste/Recycling</h5>
        <div id="visualization" style="width: 600px; height: 300px;"></div>
      </div>
        </div>
    <div class="row">
          <div class="seven columns">
        <h5>Performance</h5>
        <label for="performance"></label>
        <?php echo form_dropdown('performance', $data->performanceOptions, set_value('performance', 1), 'id="performance"');?>
        <table width="100%" border="0" cellspacing="10" cellpadding="20">
              <tr style="border-bottom:1px solid #ddd">
            <td>Distribution Center</td>
            <td>Stores</td>
          </tr>
              <tr style="border-bottom:1px solid #ddd">
            <td>Highest
                  <h4>
                  <?php
                  	$temp = '';
                  	
                  	foreach ($performanceData->DCHigh as $item) {
                  		$temp .= '<a href="'.base_url().'Dcinfo/Waste/'.$item->id.'">'.$item->name.'</a>, ';
                  	}
                  	
                  	$temp = rtrim($temp, ', ');
                  	echo $temp;
                  ?>
                  </h4>
                  </td>
            <td>Highest
                  <h4>
                  <?php
                  	$temp = '';
                  	
                  	foreach ($performanceData->StoreHigh as $item) {
                  		//$temp .= '<a href="'.base_url().'Storeinfo/Waste/'.$item->id.'">'.$item->name.'</a>, ';
                  		$temp .= '<a href="'.base_url().'StoreInfo/Waste/'.$item->id.'">'.$item->name.'</a>, ';
                  	}
                  	
                  	$temp = rtrim($temp, ', ');
                  	echo $temp;
                  ?>
                  </td>
          </tr>
              <tr  style="border-bottom:1px solid #ddd">
            <td>Lowest
                  <h4>
                  <?php
                  	$temp = '';
                  	
                  	foreach ($performanceData->DCLow as $item) {
                  		$temp .= '<a href="'.base_url().'Dcinfo/Waste/'.$item->id.'">'.$item->name.'</a>, ';
                  	}
                  	
                  	$temp = rtrim($temp, ', ');
                  	echo $temp;
                  ?>
                  </h4></td>
            <td>Lowest
                  <h4>
                  <?php
                  	$temp = '';
                  	
                  	foreach ($performanceData->StoreLow as $item) {
                  		//$temp .= '<a href="'.base_url().'Storeinfo/Waste/'.$item->id.'">'.$item->name.'</a>, ';
                  		$temp .= '<a href="'.base_url().'StoreInfo/Waste/'.$item->id.'">'.$item->name.'</a>, ';
                  	}
                  	
                  	$temp = rtrim($temp, ', ');
                  	echo $temp;
                  ?>
                  </h4></td>
          </tr>
            </table>
      </div>
          <div class="four columns">
        <h5>Service Requests</h5>
        <div id="servicechart" style="width: 350px; height: 200px;"></div>
      </div>
          <div class="five columns omega">
        <h5>Cost of Services</h5>
        <div id="invoicechart" style="width: 350px; height: 200px;"></div>
      </div>
        </div>
  </div>
  <script type="text/javascript">
<!--
	$(document).ready(function() {
		$('select[name="period"]').change(function() {
			document.forms["period_form"].submit();
		});

		$('#performance').change(function() {
			$('#id_performance').val($(this).val());
			document.forms["period_form"].submit();
		});
	});
//-->
</script>
<?php include("application/views/admin/common/footer.php");?>
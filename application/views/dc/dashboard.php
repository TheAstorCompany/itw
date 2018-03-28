<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

<script type="text/javascript">
	$(function() {
		$('#tabs').tabs();
	});
</script>
<script	type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
</script>
<script type="text/javascript">
    function drawVisualization2() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');

        <?php
        if(isset($ServiceRequests)) {
        	printf("data.addRows(%d);\n", count($ServiceRequests));
        	foreach($ServiceRequests as $k=>$_item) {
        		printf("data.setValue(%d, 0, '%s');\n", $k, $_item->Name);
        		printf("data.setValue(%d, 1, %d)\n", $k, $_item->value);
        	}
        }
        ?>

        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('servicechart')).
            draw(data, {title: "", chartArea:{left:0,top:0,width:"100%",height:"100%"}});
    }
    google.setOnLoadCallback(drawVisualization2);


    function drawVisualization3() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        <?php if(isset($CostOfService)) {
        	printf("data.addRows(%d);\n", count($CostOfService));
        	foreach($CostOfService as $k=>$_item) {
        		printf("data.setValue(%d, 0, '%s');\n", $k, $_item->Name);
        		printf("data.setValue(%d, 1, %d)\n", $k, $_item->value);
        	}
        } ?>

        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('invoicechart')).
            draw(data, {title: "", chartArea:{left:0,top:0,width:"100%",height:"100%"}});
    }
    google.setOnLoadCallback(drawVisualization3);

    function drawVisualization6() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Week');
        data.addColumn('number', 'Open Calls');
        data.addColumn({type: 'string', role: 'tooltip'});
        data.addColumn('number', 'Resolved Calls');
        data.addColumn({type: 'string', role: 'tooltip'});

        <?php
        if(isset($calls)) {
            printf("data.addRows(%d);\n", count($calls));
            $i = 0;
            foreach($calls as $k=>$_item) {
                printf("data.setValue(%d, 0, 'Week of %s');\n", $i, $k);
                printf("data.setValue(%d, 1, %d);\n", $i, $_item['open']);
                printf("data.setValue(%d, 2, 'Week of %s Open Calls: %d Total: %d');\n", $i, $k, $_item['open'], $_item['total']);
                printf("data.setValue(%d, 3, %d);\n", $i, $_item['closed']);
                printf("data.setValue(%d, 4, 'Week of %s Resolved Calls: %d Total: %d');\n", $i, $k, $_item['closed'], $_item['total']);
                $i++;
            }
        }
        ?>

        // Create and draw the visualization.
        var chart = new google.visualization.ColumnChart(document.getElementById('callschart'));
        chart.draw(data, {chartArea: {left: 50, top: 20, width: '50%', height: '80%'}, is3D: false, title: '', colors: ['#90BC53', '#588915'], isStacked: true, legend: 'right', legendBackgroundColor: '#ccc'});

    }
    google.setOnLoadCallback(drawVisualization6);


</script>
<div class="row">
	<div class="sixteen columns">
		<h1>Penn Medicine Dashboard</h1>
	</div>
</div>
<div class="row" style="border-bottom: 1px solid #ddd;">
    <div class="sixteen columns">
        <form action="" method="get">
        <span style="float:right">
            <select name="distributioncenter_id" id="distributioncenter_id" style="float:left;">
                <option value="0">All</option>
                <?php
                foreach($AllDC as $temp) {
                    if($distributioncenter_id == $temp["id"]) {
                        echo "<option selected=\"selected\" value=\"{$temp["id"]}\">{$temp["name"]}</option>";
                    } else {
                        echo "<option value=\"{$temp["id"]}\">{$temp["name"]}</option>";
                    }
                }
                ?>
            </select>
            <div style="float: left; padding-left: 5px; margin-top: -5px">
                <button type="submit" style="margin: 0px;">Update</button>
            </div>
        </span>
        </form>
    </div>
	<div class="seven columns">
        <table width="100%" border="0" cellspacing="10" cellpadding="20">
            <tr>
                <td colspan="3"><h5><?php echo date('F Y', mktime(0, 0, 0, date('m')-2, 1, date('Y'))); ?></h5></td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd">
                <td>Waste
                    <h4>
                        <?php echo number_format($PriorMonthData->waste, 0); ?>
                        tons
                    </h4>
                </td>
                <td>Recycling
                    <h4><?php echo number_format($PriorMonthData->recycling, 0); ?> tons</h4>
                </td>
                <td>Diversion rate
                    <h4><?php echo number_format($PriorMonthData->diversion, 0); ?>%</h4>
                </td>
            </tr>
            <tr>
                <td>Cost
                    <h4>$<?php echo number_format($PriorMonthData->cost, 0); ?></h4>
                </td>
                <td>Rebate
                    <h4>$<?php echo number_format($PriorMonthData->rebate, 0); ?></h4>
                </td>
                <td>Waste Cost/sq ft&nbsp;|&nbsp;Recycle Cost/sq ft
                    <h4>$<?php echo number_format($PriorMonthData->wasteCostSqFt, 3); ?>&nbsp;|&nbsp;$<?php echo number_format($PriorMonthData->recyclingCostSqFt, 3); ?></h4>
                </td>
            </tr>
        </table>
        <br /><br /><br /><br />
        <table width="100%" border="0" cellspacing="10" cellpadding="20">
            <tr>
                <td colspan="3"><h5><?php echo date('F Y', mktime(0, 0, 0, date('m')-3, 1, date('Y'))); ?></h5></td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd">
                <td>Waste
                    <h4>
                        <?php echo number_format($Prior2MonthsBack->waste, 0); ?>
                        tons
                    </h4>
                </td>
                <td>Recycling
                    <h4><?php echo number_format($Prior2MonthsBack->recycling, 0); ?> tons</h4>
                </td>
                <td>Diversion rate
                    <h4><?php echo number_format($Prior2MonthsBack->diversion, 0); ?>%</h4>
                </td>
            </tr>
            <tr>
                <td>Cost
                    <h4>$<?php echo number_format($Prior2MonthsBack->cost, 0); ?></h4>
                </td>
                <td>Rebate
                    <h4>$<?php echo number_format($Prior2MonthsBack->rebate, 0); ?></h4>
                </td>
                <td>Waste Cost/sq ft&nbsp;|&nbsp;Recycle Cost/sq ft
                    <h4>$<?php echo number_format($Prior2MonthsBack->wasteCostSqFt, 3); ?>&nbsp;|&nbsp;$<?php echo number_format($Prior2MonthsBack->recyclingCostSqFt, 3); ?></h4>
                </td>
            </tr>
        </table>
	</div>
	<div class="nine columns omega">
        <h5>30-day Summary</h5>
        Total Service Requests: <strong><?php echo $total_service_requests; ?></strong><br />
        Open Service Requests: <strong><?php echo $open_service_requests; ?></strong><br />
        <div id="callschart" style="width: 600px; height: 300px;"></div>
	</div>
</div>

<div class="row">
	<div class="columns">
		<h5>Service Requests</h5>
        <h5><?php echo date('F Y', mktime(0, 0, 0, date('m')-2, 1, date('Y'))); ?></h5>
		<div id="servicechart" style="width: 250px; height: 250px;"></div>
	</div>
	<div class="columns omega">
		<h5>Cost of Services</h5>
        <h5><?php echo date('F Y', mktime(0, 0, 0, date('m')-2, 1, date('Y'))); ?></h5>
		<div id="invoicechart" style="width: 250px; height: 250px;"></div>
	</div>
    <div class="columns" id="mapsFrame" style="width: 650px;">
        <iframe width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q=<?php echo base_url()?>Maps/DC/month&amp;output=embed&iwloc=near&amp"></iframe>
    </div>
</div>
<?php include("application/views/admin/common/footer.php");?>

<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

<style type="text/css">
    #dialogServiceDescriptions table {
        border: 1px solid #000000;
    }

    #dialogServiceDescriptions table td {
        padding: 3px;
        border: 1px solid #000000;
    }

    #dialogServiceDescriptions table th {
        padding: 3px;
        border: 1px solid #000000;
        font-weight: bold;
    }
</style>
<script type="text/javascript">
    $(function () {
        $('#tabs').tabs();

        $('#wastelist').dataTable({
            "bPaginate": false,
            "bFilter": false
        });

        $('#dialogServiceDescriptions').dialog({ modal: true, autoOpen: false, title: 'Service Descriptions', width: 800, buttons: { 'OK': function() { $(this).dialog('close'); } } });

        var dates = $("#from, #to").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            onSelect: function (selectedDate) {
                var option = this.id == "from" ? "minDate" : "maxDate",
                    instance = $(this).data("datepicker"),
                    date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                            $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings);
                dates.not(this).datepicker("option", option, date);
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
        var data = google.visualization.arrayToDataTable([
            ['Year', 'Tons'],
            <?php
            foreach ($WasteTrend as $row) {
                echo '["'.$row->my.'", '.$row->wasteTons.'],'."\n";
            }
            ?>
        ]);

        // Create and draw the visualization.
        var ac = new google.visualization.LineChart(document.getElementById('visualization'));
        ac.draw(data, {
            title: '',
            legend: 'none',
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
        data.addRows(<?php echo count($CostChartInfo)?>);
        <?php
        $i = 0;
        foreach ($CostChartInfo as $row) {?>
        data.setValue(<?php echo $i?>, 0, '<?php echo $row->feeType; ?>');
        data.setValue(<?php echo $i?>, 1, <?php echo $row->Cost; ?>);
        <?php $i++; }?>

        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('costschart')).
            draw(data, {title: "", chartArea: {left: 0, top: 0, width: "100%", height: "100%"}, sliceVisibilityThreshold: 0});
    }
    google.setOnLoadCallback(drawVisualization2);


</script>


<form action="" method="GET">
    <div class="row">
        <div class="sixteen columns">
            <span style="float:right">
                 <a href="<?php echo base_url()?>DistributionCenters/Waste?<?php echo http_build_query($_GET);?>&export=1" class="button">Export CSV</a>
            </span>
            <h1>North Mississippi Waste</h1>
            <span style="float:left;padding-right:10px">
                <label for="from">Start Date</label>
                <input name="from" type="text" id="from" value="<?php if(isset($from)) echo $from;?>" style="width:100px"/>
            </span>
            <span style="float:left;padding-right:10px">
                <label for="to">End Date</label>
                <input name="to" type="text" id="to" value="<?php if(isset($to)) echo $to;?>" style="width:100px"/>
            </span>
            <label for="for">Sites</label>
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
            <div style="float:left;padding-left: 5px; margin-top: -5px">
                <button type="submit">Update</button>
            </div>
            <hr />
        </div>
    </div>
</form>
<div class="row">
    <div class="row">
        <div class="columns" style="width: 350px;">
            <h5 style="display: inline; margin-right: 150px;">Costs</h5><a href="javascript: void(0);" onclick="$('#dialogServiceDescriptions').dialog('open');">Service Descriptions</a>
            <div id="costschart" style="width: 350px; height: 200px;"></div>
        </div>
        <div class="eight columns omega">
            <h5>Waste Trends</h5>
            <div id="visualization" style="width: 400px; height: 160px;">Loading...</div>
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
                        <th>DC Name</th>
                        <th>Sq Ft</th>
                        <th>Scheduled (Tons)</th>
                        <th>Scheduled (Cost)</th>
                        <th>On Call (Tons)</th>
                        <th>On Call (Cost)</th>
                        <th>Other (Cost)</th>
                        <th>Cost (Tons)</th>
                        <th>Cost</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dataList as $row) { ?>
                        <tr class="gradeA">
                            <!--td><a href="<?php echo base_url() ?>Dcinfo/Waste/<?php echo $row->id; ?>"><?php echo $row->name; ?></a></td-->
                            <td><?php echo $row->name; ?></td>
                            <td align="center"><?php echo round($row->squareFootage, 0); ?></td>
                            <td align="center"><?php echo round($row->ScheduledTons, 2); ?></td>
                            <td align="center">$<?php echo round($row->ScheduledCost, 2); ?></td>
                            <td align="center"><?php echo round($row->OnCallTons, 2); ?></td>
                            <td align="center">$<?php echo round($row->OnCallCost, 2); ?></td>
                            <td align="center">$<?php echo round($row->OtherCost, 2); ?></td>
                            <td align="center"><?php echo round($row->TotalTons, 2); ?></td>
                            <td align="center">$<?php echo round($row->TotalCost, 2); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>DC Name</th>
                        <th>Sq Ft</th>
                        <th>Scheduled (Tons)</th>
                        <th>Scheduled (Cost)</th>
                        <th>On Call (Tons)</th>
                        <th>On Call (Cost)</th>
                        <th>Other (Cost)</th>
                        <th>Cost (Tons)</th>
                        <th>Cost</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="dialogServiceDescriptions">
    <table cellpadding="5" border="1">
        <tr><th>Name</th><th>Description</th></tr>
        <tr><td>Trash Rate</td><td>The Price Walgreens Pays for the Regular Monthly Trash Service</td></tr>
        <tr><td>Rolloff Trash Haul</td><td>(Compactor Trash Haul)Compactors are “On Call” This is the price Walgreens Pays for each haul </td></tr>
        <tr><td>Rolloff Trash Disposal</td><td>(Compactor Trash Disposal) The price Walgreens Pays for each Disposal</td></tr>
        <tr><td>Rec Rate</td><td>The Price Walgreen’s Pays for Recycling</td></tr>
        <tr><td>Temp Roll Off Haul</td><td>Hauling Fee for a Temp Roll-Off </td></tr>
        <tr><td>Temp Rolloff Disposal</td><td>Disposal Fee for a Temp Roll-Off </td></tr>
        <tr><td>Extra/Bulk P/U</td><td>Extra pickup Fee </td></tr>
        <tr><td>Rental</td><td>Rental Fee </td></tr>
        <tr><td>Fuel</td><td>Fuel Fee</td></tr>
    </table>
</div>
        
<?php include("application/views/admin/common/footer.php");?>

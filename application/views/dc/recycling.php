<?php include("application/views/admin/common/header.php"); ?>
<?php include("application/views/admin/common/top_menu.php"); ?>

    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">

        function drawVisualization2() {
            // Create and populate the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'materialName');
            data.addColumn('number', 'quantity');
            data.addRows(<?php echo count($ChartsInfo)?>);
            <?php
                $i = 0;
                foreach($ChartsInfo as $temp) {
            ?>
            data.setValue(<?php echo $i?>, 0, '<?php echo $temp->materialName; ?>');
            data.setValue(<?php echo $i?>, 1, <?php echo floatval($temp->quantity); ?>);
            <?php
                    $i++;
                }
            ?>

            // Create and draw the visualization.
            new google.visualization.PieChart(document.getElementById('totaltonnagebymaterial')).
                draw(data, {title: "", sliceVisibilityThreshold: 0});
        }
        google.setOnLoadCallback(drawVisualization2);

        function drawVisualization3() {
            // Create and populate the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'materialName');
            //data.addColumn('number', 'tons');
            data.addColumn('number', 'PricePOUnit');
            data.addRows(<?php echo count($ChartsInfo)?>);

            <?php
                $i = 0;
                foreach ($ChartsInfo as $temp) {
            ?>
            data.setValue(<?php echo $i?>, 0, '<?php echo $temp->materialName; ?>');
            //data.setValue(<?php echo $i?>, 1, <?php echo floatval($temp->pricePOUnit)*floatval($temp->quantity); ?>);
            data.setValue(<?php echo $i?>, 1, <?php echo $temp->pricePOUnit; ?>);
            <?php
                    $i++;
                }
            ?>


            // Create and draw the visualization.
            new google.visualization.PieChart(document.getElementById('totalpopricebymaterial')).
                draw(data, {title: "", sliceVisibilityThreshold: 0});
        }
        google.setOnLoadCallback(drawVisualization3);


        //Recycling
        $(function () {
            var dates = $("#from, #to").datepicker({
                defaultDate: "+1w",
                dateFormat: "mm/dd/yy",
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
/*
            $('#recyclinglist').dataTable({
                "bPaginate": false,
                "bFilter": false
            });
            */
        });

        <?php /*echo number_format(round($row->Rebate, 2), 2, '.', '');*/ ?>
    </script>

    <style type="text/css">
        .cellFixed {
            width: 65px;
            white-space: nowrap;
        }
        .cellFixedName {
            width: 100px;
            white-space: nowrap;
        }
        .tdRight {
            padding: 3px 10px 3px 0;
            text-align: right;
        }

        .tdLeft {
            padding: 3px 0 3px 10px;
        }

        table#recyclinglist td {
            /*padding: 3px 0;*/
        }
    </style>

    <form action="" method="GET">
        <div class="row">
            <div class="sixteen columns">
	            <span style="float:right">
       		        <a href="<?php echo base_url() ?>DistributionCenters/Recycling?<?php unset($_GET['print']); $_GET['export'] = 1; echo http_build_query($_GET); ?>" class="button">Export CSV</a>
                </span>
	            <span style="float:left;padding-right:10px"><label for="from">Start Date</label>
				    <input name="from" type="text" id="from" value="<?php if (isset($from)) echo $from; ?>" style="width:100px"/></span><span style="float:left;padding-right:10px" />
				    <label for="to">End Date</label>
				    <input name="to" type="text" id="to" value="<?php if (isset($to)) echo $to; ?>" style="width:100px"/>
		        </span>
                <span style="float:left;padding-right:10px">
		            <label for="for">Sites</label>
                    <select name="distributioncenter_id" id="distributioncenter_id">
                    <option value="">All</option>
                    <?php
                        foreach ($AllDC as $temp) {
                            if ($distributioncenter_id == $temp["id"]) {
                                echo "<option selected=\"selected\" value=\"{$temp["id"]}\">{$temp["name"]}</option>";
                            } else {
                                echo "<option value=\"{$temp["id"]}\">{$temp["name"]}</option>";
                            }
                        }
                    ?>
                    </select>
	            </span>
                <div style="float:left; padding-left: 5px; margin-top: -5px">
                    <button type="submit">Update</button>
                </div>
                <hr />
            </div>
        </div>
    </form>

    <div class="row">
        <div class="columns">
            <h5>Total Tonnage By Material</h5>

            <div id="totaltonnagebymaterial" style="width: 500px; height: 200px;"></div>
        </div>
        <div class="columns">
            <h5>Total PO Price By Material</h5>

            <div id="totalpopricebymaterial" style="width: 500px; height: 180px;"></div>
        </div>
    </div>
    <div class="row">
        <div class="sixteen columns">
            <div style="width: 1180px; overflow-x: scroll;">
                <table cellpadding="0" cellspacing="0" border="0" class="display" id="recyclinglist" width="100%">
                    <thead>
                        <tr>
                            <?php
                                foreach($DatagridInfoColums as $row) {
                                    echo '<th nowrap>'.$row.'</th>'."\n";
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $i = 0;
                        foreach($DatagridInfo as $row) {
                            echo '<tr class="gradeA '.($i%2==0 ? 'odd' : 'even').'">';
                            foreach($row as $k=>$v) {
                                echo '<td>'.$v.'</td>'."\n";
                            }
                            echo '</tr>';
                            $i++;
                        }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <?php
                            foreach($DatagridInfoColums as $row) {
                                echo '<th nowrap>'.$row.'</th>'."\n";
                            }
                            ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

<?php include("application/views/admin/common/footer.php"); ?>

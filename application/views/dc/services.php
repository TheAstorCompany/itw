<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>


    <script type="text/javascript">
        $(function() {
            $('#servicelist').dataTable( {
                "bPaginate": false,
                        "bFilter": false
                    } );
        });

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
            data.addRows(<?php echo count($TypeOfContainers)?>);
            <?php
                $i = 0;
                foreach($TypeOfContainers as $row) {?>
            data.setValue(<?php echo $i?>, 0, '<?php echo $row->Name; ?>');
            data.setValue(<?php echo $i?>, 1, <?php echo $row->value; ?>);
            <?php $i++; }?>


            // Create and draw the visualization.
            new google.visualization.PieChart(document.getElementById('servicechart')).
                draw(data, {title: ""});
        }


        google.setOnLoadCallback(drawVisualization2);


        function drawVisualization4() {
            // Create and populate the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Task');
            data.addColumn('number', 'Hours per Day');
            data.addRows(<?php echo count($FrequencyOfService)?>);
            <?php
                $i = 0;
            foreach($FrequencyOfService as $row) {?>
            data.setValue(<?php echo $i?>, 0, '<?php echo $row->FrequencyType; ?>');
            data.setValue(<?php echo $i?>, 1, <?php echo $row->value; ?>);
            <?php $i++;}?>


            // Create and draw the visualization.
            new google.visualization.PieChart(document.getElementById('frequencychart')).
                draw(data, {title: ""});
        }


        google.setOnLoadCallback(drawVisualization4);
    </script>

	<form action="" method="GET">
        <div class="row">
            <div class="sixteen columns">
                <span style="float:right">
                    <a href="<?php echo base_url()?>DistributionCenters/Services?<?php echo http_build_query($_GET);?>&export=1" class="button">Export CSV</a>
                </span>
                <h1>DC Services</h1>
                <span style="float:left;padding-right:10px">
                    <label for="from">Start Date</label>
                    <input name="from" type="text" id="from" value="<?php if(isset($from)) echo $from;?>" style="width:100px"/>
                </span>
                <span style="float:left;padding-right:10px">
                    <label for="to">End Date</label>
                    <input name="to" type="text" id="to" value="<?php if(isset($to)) echo $to;?>" style="width:100px"/>
                </span>
                <span style="float:left;padding-right:10px">
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
                </span>
                <div style="float:left; margin-top: 15px;">
                    <button type="submit">Update</button>
                </div>
                <hr />
            </div>
        </div>
	</form>         
    <div class="row">
        <div class="five columns">
            <h5>Type of Containers</h5>
            <div id="servicechart" style="width: 380px; height: 200px;"></div>
        </div>
        <div class="five columns">
            <h5>Frequency of Service</h5>
            <div id="frequencychart" style="width: 380px; height: 200px;"></div>
        </div>
    </div>
    <div class="row">
        <div class="sixteen columns">
            <div>
                <table cellpadding="0" cellspacing="0" border="0" class="display" id="servicelist" width="100%">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>SqFt</th>
                            <th>Vendor</th>
                            <th>Service Type</th>
                            <th>Container</th>
                            <th>Frequency</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($DatagridInfo as $row) {?>
                        <tr class="gradeA">
                            <!--td><a href="<?php echo base_url()?>Dcinfo/Waste/<?php echo $row->id; ?>"><?php echo $row->dcName; ?></a></td-->
                            <td><?php echo $row->dcName; ?></td>
                            <td ><?php echo round($row->squareFootage, 0); ?></td>
                            <td><?php echo $row->name; ?></td>
                            <td><?php echo $row->ServiceType; ?></td>
                            <td><?php echo $row->container; ?></td>
                            <td><?php echo $row->Frequency; ?></td>
                            <td><?php echo $row->cost; ?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Location</th>
                            <th>SqFt</th>
                            <th>Vendor</th>
                            <th>Service Type</th>
                            <th>Container</th>
                            <th>Frequency</th>
                            <th>Cost</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

<?php include("application/views/admin/common/footer.php");?>

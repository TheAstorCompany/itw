<script type="text/javascript">
	$(function() {
		var dates = $("input[name='from'], input[name='to']").datepicker({
			 dateFormat: "mm/dd/yy",
			 weekHeader: "W",
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

		$('#display').change(function() {
			if($(this).val()==8) {
				$('#spanBystate').show();
			} else {
				$('#bystate').val(0);
				$('#spanBystate').hide();
			}
		});

		if($('#display').val()==8) {
			$('#spanBystate').show();
		}

		$('#btnDiversionReport').click(function() {
			$(this).attr('href', '<?php echo base_url();?>Stores/Waste?diversion_report=1&from=' + $('#from').val() + '&to=' + $('#to').val());
		});
	});
</script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('visualization', '1', {packages: ['corechart']});
</script>
<script type="text/javascript">
	function drawVisualization() {
	    var chart = new google.visualization.LineChart(document.getElementById('visualization'));
	    $.post('<?php echo base_url();?>Stores/WasteTrend', function(data) {

		var trends = [];
		trends.push(['Month', 'Waste', 'Cardboard', 'Commingle', 'Other']);
		for (var key in data.trend) {
		    var val = data.trend[key];
		    var trend = [key, val['Waste'], val['Cardboard'], val['Commingle'], val['Other']];
		    trends.push(trend);
		}
		
    	var datadraw = google.visualization.arrayToDataTable(trends);

		chart.draw(datadraw,  {
			    title : '',
			    isStacked: true,
			    width: 650,
			    height: 200,
			    vAxis: {title: "Tons"},
			    hAxis: {title: "Month"}
		    });
	    });
	}       

	function drawVisualization3() {
		// Create and populate the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Task');
		data.addColumn('number', 'Hours per Day');
		<?php if(isset($chart2)) {
			printf("data.addRows(%d);\n", count($chart2));
			foreach($chart2 as $k=>$_item) {
				printf("data.setValue(%d, 0, '%s');\n", $k, $_item['name']);
				printf("data.setValue(%d, 1, %d)\n", $k, $_item['value']);
			}
		}
		?>      

		new google.visualization.PieChart(document.getElementById('wastechart')).draw(data, {title: ""});
	}
	google.setOnLoadCallback(drawVisualization3);
</script>

<div class="row">
	<div class="sixteen columns">
		<?php
			if(!$is_storeinfo) {
		?>		
        <span style="float:right">
			<a href="<?php echo base_url();?>Stores/Waste?<?php echo http_build_query(array_merge($_GET, array('print'=>1))); ?>" class="button" target="_blank">Printer Friendly</a> 
			<a href="<?php echo base_url();?>Stores/Waste?<?php echo http_build_query(array_merge($_GET, array('diversion_report'=>1))); ?>" id="btnDiversionReport" class="button">Diversion Report</a>
			<a href="<?php echo base_url();?>Stores/Waste?<?php echo http_build_query(array_merge($_GET, array('export'=>1))); ?>" class="button">Export CSV</a>
		</span>
		<h1>Waste</h1>
		<?php
			}
		?>
        <form action="" method="get">
        <span style="float:left;padding-right:10px;width:106px;"><label for="from">Start Date</label>
		<input name="from" type="text" id="from" value="<?php echo set_value('from', date('m') . '/01/' . date('Y'));?>" style="width:100px"/></span><span style="float:left;padding-right:10px;width:106px;">
		<label for="to">End Date</label>
		<input name="to" type="text" id="to" value="<?php echo set_value('to', date('m/d/Y'));?>" style="width:100px"/></span>
		<?php
			if(!$is_storeinfo) {
		?>	
		<span style="float:left;padding-right:10px;width:220px;">
			<label for="for">Display</label>
				<?php echo form_dropdown('display', $data->forOptions, set_value('display', $data->display), 'id="display"');?>
		</span>
		<span style="float:left;padding-right:10px;width:220px;">
			<label for="for">State</label>
			<?php echo form_dropdown('bystate', $data->byState, set_value('bystate', $data->bystate), 'id="bystate"');?>
		</span>
		<?php
			}
		?>			
        <div style="float:left; margin-top: 15px;">
			<button type="submit">Update</button>
        </div>		
        </form>          	
        <hr />
	</div>		
</div>
<div class="row">
	<div class="row">
		<div class="five columns">
			<h5>Waste vs Recycling</h5>
			<div id="wastechart"></div>
		</div>
		<div class="eleven columns omega">
			<h5>Waste Trends</h5>
			<div id="visualization">Loading...</div>
		</div>
	</div>
	<div class="row">
		<div class="sixteen columns" style="width: <?php echo ($is_storeinfo ? '1100px;' : '100%'); ?>">
			<br />
			<br />

			<table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
				<thead>
					<tr>
						<th>Location</th>
						<th>District</th>
						<th>State</th>
						<th>24H</th>
						<th>SqFt</th>
						<th>Waste (Tons)</th>
						<th>Cardboard (Tons)</th>
						<th>Commingle (Tons)</th>
						<th>Other (Tons)</th>
						<th>Cost</th>
					</tr>
				</thead>
				<tbody>
           
				</tbody>
				<tfoot>
					<tr>
						<th>Location</th>
						<th>District</th>
						<th>State</th>
						<th>24H</th>
						<th>SqFt</th>
						<th>Waste (Tons)</th>
						<th>Cardboard (Tons)</th>
						<th>Commingle (Tons)</th>
						<th>Other (Tons)</th>
						<th>Cost</th>
					</tr>
				</tfoot>
			</table>                 
		</div>
	</div>
</div>
<script type="text/javascript">
    var oTable = null;
    var filterData = null;
    var oTotalRecords = 0;
    $(function() {
        drawVisualization();

        oTable = $('#wastelist').dataTable({
            "sPaginationType": "full_numbers",
            "iDisplayLength" : 10,
            "bFilter": false,
            "bLengthChange": false,
            "bProcessing": true,
            "bServerSide": true,
            "bPaginate": true,
            "bStateSave": false,
            "oSearch": {"sSearch": ""},
            "sAjaxSource": '<?php echo base_url();?>Stores/WasteTable',
            "fnServerData": function ( sSource, aoData, fnCallback ) {

            aoData.push({ "name": "from", "value": $.trim($('#from').val())});
            aoData.push({ "name": "to", "value": $.trim($('#to').val())});
            aoData.push({ "name": "bystate", "value": $.trim($('#bystate').val())});
            aoData.push({ "name": "display", "value": $.trim($('#display').val())});

            filterData = aoData;

            $.getJSON( sSource, aoData, function (json) {
                if(json.error == 'expired') {
                    alert('You session has timed out, click OK to return to the login screen');
                    document.location.href='<?php echo base_url();?>admin/Auth';
                } else {
                    oTotalRecords = json.iTotalRecords;
                    fnCallback(json);
                }
            });
            }
        });
    });	
</script>	

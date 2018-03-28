<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<script type="text/javascript">

function submitForm(val) {
    $('#export').val('true');
    $('#reportForm').submit();
}

</script>

<span style="color:red;"><?php echo validation_errors(); ?></span>
<?php echo form_open('admin/Reports/MissingInvoices', array("id"=>"reportForm"));?>

<input type="hidden" id="export" name="export" value="false" />

<div class="row">
	<div class="sixteen columns">
		<h1>Report - Missing Invoices</h1>
	</div>
</div>
<div class="row">
	<div class="sixteen columns">
		<label for="type">Month / Year</label>
		<div>
			<select style="width: 60px; float: left;" name="reportMonth" id="reportMonth" style="float: right;">
				<?php
					for($m=1; $m<=12; $m++) {
						$m_str = strftime('%b', mktime(0, 0, 0, $m, 1, 2012));
						echo '<option value="'.strftime('%m', mktime(0, 0, 0, $m, 1, 2012)).'" '.(set_value('reportMonth', $data->reportMonth)==$m ? 'selected="selected"' : '').'>'.$m_str.'</option>'."\n";
					}
				?>						
			</select>
			<span style="float: left; padding: 1px;">/</span>
			<select style="width: 60px; float: left;" name="reportYear" id="reportYear">
				<?php
					for($y=2012; $y<=(date('Y')); $y++) {
						echo '<option value="'.$y.'"'.(set_value('reportYear', $data->reportYear)==$y ? 'selected="selected"' : '').'>'.$y.'</option>'."\n";
					}
				?>						
			</select>
			<span style="float: right;"><button onclick="submitForm(1); return false;">Get Report</button></span>
		</div>
		</div>
		<div>
		    <table cellpadding="0" cellspacing="0" border="0" class="display" id="invoiceslist">
			<thead>
			    <tr>
				<th>Store#</th>
				<th>Vendor#</th>
				<th>Vendor Name</th>
				<th>Month/Year</th>
			    </tr>
			</thead>
			<tbody>

			</tbody>
			<tfoot>
			    <tr>
				<th>Store#</th>
				<th>Vendor#</th>
				<th>Vendor Name</th>
				<th>Month/Year</th>
			    </tr>
			</tfoot>
		    </table>
		</div>
	    <br /><br />
	</div>
</div>

<?php echo form_close()?>

<script>
	function ExportToCSV() {
		var q = '';
		for(var i=0; i<filterData.length; i++) {
			var o = filterData[i];
			var v = o.value;
			if(o.name=='iDisplayStart') {
				v = 0;
			}
			if(o.name=='iDisplayLength') {
				v = iTotalRecords;
			}
			q += '<input type="hidden" name="' + o.name + '" value="' + v + '" />';
		}
		$('#fExport').html(q);
		$('#fExport').submit();
	}
	
	var stack_id = 0;
	$(function() {	
		var table = $('#invoiceslist').dataTable({
			"sPaginationType": "full_numbers",
			"bProcessing": true,
			"bServerSide": true,
			"bStateSave": false,
			"iDisplayLength": 50,
			"bLengthChange": false,
			"bFilter": false,
			"sAjaxSource": '<?php echo base_url();?>admin/Reports/MissingInvoices',
			"fnServerData": function ( sSource, aoData, fnCallback ) {
			    
			    aoData.push(
				{ "name": "export", "value": 'false' }
			    );		    
	
			    aoData.push(
				{ "name": "reportMonth", "value": $('#reportMonth').val() }
			    );

			    aoData.push(
				{ "name": "reportYear", "value": $('#reportYear').val() }
			    );

			    filterData = aoData;
			    stack_id++;
			    $.ajax({
				type: "POST",
				dataType: "json",
				url: sSource,
				data: aoData,
				lstack_id: stack_id,
				success: function (json) {		
				    if(json.error == 'expired') {
					alert('You session has timed out, click OK to return to the login screen');
					document.location.href = '<?php echo base_url();?>admin/Auth';
				    } else {
					if(this.lstack_id==stack_id) {
					    iTotalRecords = json.iTotalRecords;
					    fnCallback(json);
					}
				    }
				}
			    });
			}
		});

		$('#reportMonth, #reportYear').change(
		    function() {
			table.fnDraw();
		    }
		);
	});
</script>  
 
<?php include("application/views/admin/common/footer.php");?>
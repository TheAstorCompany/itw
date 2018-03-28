<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
        <div class="content">
        <div class="row">
        	<div class="sixteen columns">
          		<h1>Invoice History</h1>
          	</div>
        </div>
        <div class="row">
        	<div class="sixteen columns">
        		<table cellpadding="0" cellspacing="0" border="0" class="display" id="callslist" width="100%">
					<thead>
						<tr>
							<th>Invoice#</th>
				            <th>Site Name</th>
				            <th>Vendor</th>
				            <th>Inv Date</th>
                            <th>Hauler Inv#</th>
				            <th>Material</th>
				            <th>Cost</th>
				            <th>Tons</th>
							<th>Total Cost</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Invoice#</th>
				            <th>Site Name</th>
				            <th>Vendor</th>
				            <th>Inv Date</th>
                            <th>Hauler Inv#</th>
				            <th>Material</th>
				            <th>Cost</th>
				            <th>Tons</th>
							<th>Total Cost</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
<script>
	$(function() {		
		$('#callslist').dataTable({
			"sPaginationType": "full_numbers",
	        "bProcessing": true,
	        "bServerSide": true,
	        "bStateSave": true,
			"aoColumnDefs": [{ aTargets: [4, 5, 6, 7], bSortable: false }],
	        "oSearch": {"sSearch": "incomplete"},
	        "sAjaxSource": '<?php echo base_url();?>admin/DCInvoice/ajaxList',
	        "fnServerData": function ( sSource, aoData, fnCallback ) {
	            var filter_id = $('#storedFilter_id').val();

	            aoData.push(
	                { "name": "filter_id", "value": $.trim(filter_id) }
	             );
	             
	             $.getJSON( sSource, aoData, function (json) {
	                if(json.error == 'expired') {
                      	alert('You session has timed out, click OK to return to the login screen');
                      	document.location.href='<?php echo base_url();?>admin/Auth';
	                } else {
	                    fnCallback(json)
	                }
	             });
	        }
		});
	});
</script>
<?php include("application/views/admin/common/footer.php");?>

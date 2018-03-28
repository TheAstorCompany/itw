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
			<th>Invoice Date</th>
			<th>Sent Date</th>
			<th>Location</th>
			<th>Vendor</th>
			<th>Material</th>
			<th>Qty</th>
			<th>Trash Rate</th>
			<th>Fees</th>
			<th>Tax</th>
			<th>Total</th>
			<th>Complete?</th>
		    </tr>
		</thead>
		<tfoot>
		    <tr>
			<th>Invoice#</th>
			<th>Invoice Date</th>
			<th>Sent Date</th>
			<th>Location</th>
			<th>Vendor</th>
			<th>Material</th>
			<th>Qty</th>
			<th>Trash Rate</th>
			<th>Fees</th>
			<th>Tax</th>
			<th>Total</th>
			<th>Complete?</th>
		    </tr>
		</tfoot>
	    </table>
	</div>
    </div>
    <script type="text/javascript">

        var stack_id = 0;
        $(function() {
            $('#callslist').dataTable({
                "sPaginationType": "full_numbers",
                "bProcessing": true,
                "bServerSide": true,
                "bStateSave": true,
                "oSearch": {"sSearch": "incomplete"},
                "sAjaxSource": '<?php echo base_url();?>admin/WasteInvoice/ajaxList',
                "fnServerData": function ( sSource, aoData, fnCallback ) {
                    stack_id++;
                    $.ajax({
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
                                    fnCallback(json);
                                }
                            }
                        }
                    });
                }
            });
        });
    </script>
</div>    
<?php include("application/views/admin/common/footer.php");?>
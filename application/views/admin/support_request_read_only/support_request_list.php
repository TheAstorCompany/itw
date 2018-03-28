<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
        <div class="content">
        <div class="row">
        	<div class="sixteen columns">
          		<h1>Support History</h1>
          	</div>
        </div>
        <div class="row">
        	<div class="sixteen columns">
        		<table cellpadding="0" cellspacing="0" border="0" class="display" id="callslist" width="100%">
					<thead>
						<tr>
							<th>Service#</th>
				            <th>Date</th>
				            <th>Location</th>
				            <th>Contact</th>
				            <th>Phone#</th>
				            <th>Comment</th>
				            <th>Complete?</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
						  <th>Service#</th>
						  <th> Date</th>
						  <th>Location</th>
						  <th>Contact</th>
						  <th>Phone#</th>
						  <th>Comment</th>
						  <th>Complete?</th>
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
	        "bStateSave": false,
	        "oSearch": {"sSearch": "incomplete"},
	        "sAjaxSource": '<?php echo base_url();?>admin/SupportRequest/ajaxList'
		});
	});
</script>
<?php include("application/views/admin/common/footer.php");?>
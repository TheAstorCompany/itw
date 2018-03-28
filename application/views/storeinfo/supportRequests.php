<?php
	$selected_tab_id = 'SupportRequests';
	include 'header.php';
?>

<?php
	if(!empty($support_requests['rows'])) {
?>
	<span style="float:right">
		<a href="<?php echo base_url() . 'StoreInfo/SupportRequests/' . $id; ?>?print=1" class="button" target="_blank">Printer Friendly</a> 
		<a href="<?php echo base_url() . 'StoreInfo/SupportRequests/' . $id; ?>?export=1" class="button">Export CSV</a>
	</span>

	<table cellpadding="0" cellspacing="0" border="0" class="display" id="data_tbl" width="100%">
		<thead>
			<tr>
				<th>Location</th>
				<th>Service#</th>
				<th>Date</th>
				<th>Contact</th>
				<th>Phone#</th>
				<th>Description</th>
				<th>Resolved</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Location</th>
				<th>Service#</th>
				<th>Date</th>
				<th>Contact</th>
				<th>Phone#</th>
				<th>Description</th>
				<th>Resolved</th>
			</tr>
		</tfoot>
	</table>

	<script type="text/javascript">
		$(function() {
			$("#data_tbl").dataTable({
				"sPaginationType"	: "full_numbers",
				"bProcessing"		: true,
				"bServerSide"		: true,
				"bStateSave"		: false,
				"oSearch"			: {"sSearch": "incomplete"},
				"bFilter"			: false,
				"bLengthChange"		: false,
				"sAjaxSource"		: "<?php echo base_url() . 'StoreInfo/ajax_SupportRequests/' . $id; ?>"
			});
		});
	</script>
<?php
	}
?>

<?php
	include 'footer.php';
?>



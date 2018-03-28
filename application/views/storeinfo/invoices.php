<?php
	$selected_tab_id = 'Invoices';
	include 'header.php';
?>

<?php
	if(!empty($inv['rows'])) {
?>
	<span style="float:right">
		<a href="<?php echo base_url() . 'StoreInfo/Invoices/' . $id; ?>'?print=1" class="button" target="_blank">Printer Friendly</a> 
		<a href="<?php echo base_url() . 'StoreInfo/Invoices/' . $id; ?>'?export=" class="button">Export CSV</a>
	</span>
	<table cellpadding="0" cellspacing="0" border="0" class="display" id="invoices_tbl" width="100%">
		<thead>
			<tr>
				<th>Inv</th>
				<th>PO#</th>
				<th>Invoice Date</th>
				<?php echo (($_isAdmin == 1) ? '<th>Sent Date</th>' : ''); ?>
				<th>Location</th>
				<th>Vendor</th>
				<th>Material</th>
				<th>Qty</th>
				<th>Total Rebate</th>
				<th>Total</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Inv</th>
				<th>PO#</th>
				<th>Invoice Date</th>
				<?php echo (($_isAdmin == 1) ? '<th>Sent Date</th>' : ''); ?>
				<th>Location</th>
				<th>Vendor</th>
				<th>Material</th>
				<th>Qty</th>
				<th>Total Rebate</th>
				<th>Total</th>
			</tr>
		</tfoot>
	</table>

	<script type="text/javascript">
		$(function() {
			$("#invoices_tbl").dataTable({
				"sPaginationType"	: "full_numbers",
				"bProcessing"		: true,
				"bServerSide"		: true,
				"bStateSave"		: false,
				"oSearch"			: {"sSearch": "incomplete"},
				"bFilter"			: false,
				"bLengthChange"		: false,
				"sAjaxSource"		: "<?php echo base_url() . 'StoreInfo/ajax_Invoices/' . $id; ?>"
			});
		});
	</script>

<?php
	}
?>

<?php
	include 'footer.php';
?>



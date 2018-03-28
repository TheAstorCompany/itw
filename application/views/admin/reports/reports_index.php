<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<script type="text/javascript">

$(function() {
	var dates = $( "#from, #to" ).datepicker({
		defaultDate: "+1w",
		dateFormat: "mm/dd/yy",
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

	function submitForm(val) {
		document.getElementById("export_type").value = val; 
		document.getElementById("reportForm").submit();
	}

</script>

<span style="color:red;"><?php echo validation_errors(); ?></span>
<?php echo form_open('admin/Reports/getReports', array("id"=>"reportForm"));?>

<input type="hidden" id="export_type" name="export_type" value="" />

<div class="row">
	<div class="sixteen columns">
		<h1>Reports</h1>
	</div>
</div>
<div class="row">
	<div class="thirteen columns"><label for="type">Type</label>
		<?php echo form_dropdown('type', $data->typeOptions, set_value('type', $data->type));?>

		<label for="type">Period</label>
		<span style="float:left;padding-right:10px"><label for="from">Start Date</label>
			<input name="from" type="text" id="from" value="<?php echo set_value('from', $data->from);?>" style="width:100px"/>
		</span>
		<span style="float:left;padding-right:10px">
			<label for="to">End Date</label>
			<input name="to" type="text" id="to" value="<?php echo set_value('to', $data->to);?>" style="width:100px"/>
		</span>
	</div>
    <div class="three column">
        <span style="float: right;"><a href="<?php echo base_url();?>admin/TrackingUserChanges">Tracking User Changes</a></span>
        <br />
        <span style="float: right;"><a href="<?php echo base_url();?>admin/Reports/MissingInvoices">Missing Invoices</a></span>
        <br />
        <span style="float: right;"><a href="<?php echo base_url();?>admin/Reports/getServices">Services</a></span>
    </div>
</div>
<div class="row">
	<div class="sixteen columns">
		<button onclick="submitForm(1);">Get Quickbooks Report</button>
		<button onclick="submitForm(2);">Get Hauler Invoice Submission Report</button> 
		<button onclick="submitForm(3);">Get Recyling Invoice Submission Report</button>
	</div>
</div>

<?php echo form_close()?>        
        
<?php include("application/views/admin/common/footer.php");?>
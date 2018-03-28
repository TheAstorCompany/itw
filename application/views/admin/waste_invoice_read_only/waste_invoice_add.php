<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

<div class="content">
	<div class="row remove-bottom">
		<div class="sixteen columns">
			<h1><?php if ($data->id):?>Edit Hauler Invoice #<?php echo $data->id; ?><?php else:?>Enter Hauler Invoice<?php endif;?></h1>
		</div>
    </div>
    <div class="row">
        <div class="sixteen columns">
			<a href="javascript: window.history.back();" class="button">&lt;- Go back</a>
			<div id="tabs" style="border:0px;">
			<?php if (!$data->id) {
				include("application/views/admin/common/tabs.php"); 
			}?>
				<div id="ui-tabs-2" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px;">
					<span style="color:red;" id="id_errors"></span>
					<span style="color:red;"><?php echo validation_errors(); ?></span>

					<h5 class="dataentry">General Info</h5>
					<fieldset class="dataentry">
						<label for='locationName'>Location Name or ID</label>
						<input name="locationName" readonly="readonly" id="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>">

						<label for='vendorName'>Vendor</label>
						<input name="vendorName" readonly="readonly" id="vendorName" type="text" value="<?php echo set_value('vendorName', $data->vendorName);?>">

						<label for='invoiceNumber'>Invoice #</label>
						<input name="invoiceNumber" readonly="readonly" type="text" value="<?php echo set_value('invoiceNumber', $data->invoiceNumber);?>" style="width:100px">

						<label for='invoiceDate'>Invoice Date</label>
						<input name="invoiceDate" disabled="disabled" autocomplete="off" type="text" value="<?php echo set_value('invoiceDate', $data->invoiceDate);?>" style="width:100px">

						<label for='invoiceMonth'>Invoice Month</label>
						<input name="invoiceMonth" disabled="disabled" autocomplete="off" type="text" value="<?php echo set_value('invoiceMonth', $data->invoiceMonth);?>" style="width:100px">
					</fieldset>					

					<hr />

					<h5 class="dataentry">Description</h5>
					<fieldset class="dataentry">
						<div class="dataentry" id="services">
						&nbsp;
						<?php $inc = true; include "application/views/admin/waste_invoice_read_only/waste_invoice_services.php";?>
						</div>
						<br />
					</fieldset>
					<hr />
				</div>
			</div>
		</div>
	</div>
</div>	

<?php include("application/views/admin/common/footer.php");?>
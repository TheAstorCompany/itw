<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
      <div class="content">
    <div class="row remove-bottom" style="margin-bottom:0;">
          <div class="sixteen columns">
        <h1><?php if ($data->id):?>Edit Recycling charges #<?php echo $data->id; ?><?php else:?>Enter Recycling charges<?php endif;?></h1>
      </div>
        </div>
    <div class="row" style="margin-bottom:0;">
        <div class="sixteen columns" style="width:100%;">
        <?php if ($data->id) :?><a href="<?php echo base_url();?>admin/RecyclingCharges/history" class="button">&lt;- Go back</a>
        <a href="<?php echo base_url();?>admin/RecyclingCharges/Delete/<?php echo $data->id;?>" class="button">Delete</a><?php endif;?>
        <div id="tabs" style="border:0px;">
		<?php if (!$data->id) {
			include("application/views/admin/common/tabs.php"); 
		}?>            
		<span style="color:red;"><?php echo validation_errors(); ?></span>

	<div class="tab_enter_recycling_charges" id="tabs-4" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
		<div class="sixteen columns alpha" style="width:100%;">
			<?php echo form_open('admin/RecyclingCharges/AddEdit/' . $data->id, 'id="formAdd"');?>
			<?php echo form_hidden('submit_form',1);?>
			<input type="hidden" name="dateSent" value="<?php echo set_value('dateSent', date('m/d/Y')) ?>" id="id_dateSent" />
			<input type="hidden" name="internalNotes" value="<?php echo set_value('internalNotes', $data->internalNotes) ?>" id="id_internalNotes" />
			<h6 class="dataentry">General Info</h6>
			<fieldset class="dataentry">
				<label for='location'>Location Name or ID*</label>
				<input type="hidden" name="locationId" value="<?php echo set_value('locationId', $data->locationId) ?>" id="locationId" />
				<?php echo form_hidden('locationType', set_value('locationType', $data->locationType));?>
				<input name="locationName" onchange="document.getElementById('locationId').value=''" id="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>" />
				<input type="hidden" name="vendorId" value="<?php echo set_value('vendorId', $data->vendorId) ?>" id="vendorId" />
				<label for='vendorNumber'>Vendor*</label>
				<input name="vendorNumber" onchange="document.getElementById('vendorId').value=''" id="vendorNumber" type="text" value="<?php echo set_value('vendorNumber', $data->vendorNumber);?>" />
				<label for='invoiceNumber'>Invoice #*</label>
				<input name="invoiceNumber" type="text" value="<?php echo set_value('invoiceNumber', $data->invoiceNumber);?>" />
				<label for='date'>Invoice Date*</label>
				<input name="date" type="text" value="<?php echo set_value('date', $data->date);?>" style="width:100px" />
				<label for='invoiceMonth'>Invoice Month</label>
				<input name="invoiceMonth" type="text" value="<?php echo set_value('date', $data->invoiceMonth);?>" style="width:100px" />				
			</fieldset>
			<h6 class="dataentry">Description</h6>
			<fieldset class="dataentry">
				<div class="dataentry" id="dataentry">
					<?php include("charges_and_fees_ajax.php"); ?>
				</div>
				<div style="color:red;" id="error"></div>
			</fieldset>
			<?php echo form_close();?>
		<div class="column2">
			<fieldset class="dataentry">
				<!-- Charges form  -->
				<?php echo form_open('admin/RecyclingCharges/addCharge/' . $data->id, 'id="formRecycling"'); ?>
					<h6 class="dataentry">Charge</h6>
					<label for='materialDate'>Date</label>
					<input name="materialDate" type="text" style="width:100px" />
					<label for='materialId'>Material*</label>
					<?php echo form_dropdown('materialId', $data->materialOptions, null, "style='width:150px' size=1");?>
					<label for='quantity'>Quantity*</label>
					<input name="quantity" type="text" style="width:100px;" value="" />
					<label for='unitId'>Unit*</label>
					<?php echo form_dropdown('unitId', $data->unitOptions,null, 'style="width:150px"');?>
					<label for='pricePerTon'>Price Per Unit*</label>
					<input name="pricePerTon" type="text" style="width:100px;" value="" />
					<label for='description'>Description</label>
					<input name="description" type="text" style="width:100px;" value="" />
					<label for='releaseNumber'>Release #</label>
					<input name="releaseNumber" type="text" style="width:100px;" />
					<label for='CBRENumber'>CBRE #</label>
					<input name="CBRENumber" type="text" value="" style="width:100px;" />
					<button form="formRecycling">Add</button>
				<?php echo form_close();?>
				<script>
					$(document).ready(function(){
						$('#formRecycling').submit(function() {
                            var $form = $(this);

                            $('input[name^="existing_charges\["]', $form).remove();
                            $('input[name^="existing_fees\["]', $form).remove();

                            $('input[name^="existing_charges\["]').each(function(){
                                $form.append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />');
                            });
                            $('input[name^="existing_fees\["]').each(function(){
                                $form.append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />');
                            });

							$.post($('#formRecycling').attr('action'), $('#formRecycling').serialize(),
								function(data) {
									if (!data.error) {
										document.getElementById('formRecycling').reset();
										$('#error').html('');
									} else {
										$('#error').html(data.error);
									}
									$('#dataentry').html(data.html);
								}
							);
							return false;
						})
					});
					function deleteCharge(index) {
                        $('li[charge_k="' + index + '"]').remove();

                        return false;
					}
				</script>
				<!-- Fees form  -->
				<div style="color:red;" id="errorFees"></div>
				<?php echo form_open('admin/RecyclingCharges/addFee/' . $data->id, 'id="formFees"');?>
				<h6 class="dataentry">Fee</h6>
				<label for='feeType'>Fee Type*</label>
				<?php echo form_dropdown('feeType', $data->feeOptions, null, 'style="width:150px"')?>
				<label for='fee'>Fee*</label>
				<input name="fee" type="text" style="width:100px;" />
				<label for='waived'>Waived/Saved</label>
				<input name="waived" type="checkbox" value="1"><br />
				<button form="formFees">Add</button>
				<?php echo form_close();?>
				<script>
					$(document).ready(function() {
						$('#formFees').submit(function() {
                            var $form = $(this);

                            $('input[name^="existing_charges\["]', $form).remove();
                            $('input[name^="existing_fees\["]', $form).remove();

                            $('input[name^="existing_charges\["]').each(function(){
                                $form.append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />');
                            });
                            $('input[name^="existing_fees\["]').each(function(){
                                $form.append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />');
                            });

							$.post($('#formFees').attr('action'), $('#formFees').serialize(),
								function(data) {
									if (!data.error) {
										document.getElementById('formFees').reset();
										$('#errorFees').html('');
									} else {
										$('#errorFees').html(data.error);
									}
									$('#dataentry').html(data.html);
								}
							);
							return false;
						})
					});
					function deleteFee(index) {
                        $('li[fee_k="' + index + '"]').remove();

                        return false;
					}
				</script>
			</fieldset>
		</div>
			<?php if ($_isAdmin) { ?>
		<div class="column3">
			<fieldset class="dataentry" id="adminPanel">
				<h6 class="dataentry">Astor Only</h6>
				<label for="status">Complete?</label>
				<?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status))?>           
				<label for="dateSent">Date Sent</label>           
				<input name="dateSent" type="text" style="width:140px" value="<?php echo set_value('dateSent', $data->dateSent?$data->dateSent:date('m/d/Y'));?>" />
				<label for="internalNotes">Internal Notes</label>
				<textarea name="internalNotes" style="width:340px"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
			</fieldset>
		</div>
			<?php } ?>
			<hr />
			<button onclick="document.getElementById('formAdd').submit()" style="margin-left:130px;background:#13602E; color:#fff">Save</button>
			<button style="background:#777; color:#fff" onclick="window.location = window.location">Cancel</button>
		</div>
	</div>
      </div>
      </div>
  </div>
<script type="text/javascript">
	$(function() {
		$("input[name='date'], input[name='dateSent'], input[name='materialDate']").datepicker({
			 dateFormat: "mm/dd/yy",
			 weekHeader: "W" 
		});
		$("#locationName").autocomplete({
			source: "<?php echo base_url(); ?>admin/SupportRequest/autocompleteLocation",
			minLength: 2,
			select: function(e, ui) {
				$("input[name='locationId']").val(ui.item.id);
				$("input[name='locationType']").val(ui.item.type);
			}
		});
		//towa ne se znae znae li se ? 		
		$("#vendorLocation").autocomplete({
			source: "<?php echo base_url(); ?>admin/SupportRequest/autocompleteLocation",
			minLength: 2,
			select: function(e, ui) {
				$("input[name='locationId']").val(ui.item.id);
				$("input[name='locationType']").val(ui.item.type);
			}
		});

<?php if ($_isAdmin): ?>
		$('#adminPanel input,select,textarea').change(function() {			
			$('#id_'+$(this).attr('name')).val($(this).val());
		});
<?php endif; ?>

		$("#vendorNumber").autocomplete({
			source: "<?php echo base_url(); ?>admin/RecyclingCharges/autocompleteVendor",
			minLength: 2,
			select: function(e, ui) {
				$('#vendorId').val(ui.item.id);
				//$('#vendorLocation').val(ui.item.addressLine1);
			}
		});

		$("#vendorLocation").autocomplete({
			source: "<?php echo base_url(); ?>admin/RecyclingCharges/autocompleteVendorByLocation",
			minLength: 2,
			select: function(e, ui) {
				$('#vendorId').val(ui.item.id);
				$('#vendorNumber').val(ui.item.number);
			}
		});
	});
</script>
<?php include("application/views/admin/common/footer.php");?>
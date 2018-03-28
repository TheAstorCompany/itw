<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
      <div class="content">
    <div class="row remove-bottom">
          <div class="sixteen columns">
        <h1><?php if ($data->id):?>Edit Recycling charges #<?php echo $data->id; ?><?php else:?>Enter Recycling charges<?php endif;?></h1>
      </div>
        </div>
    <div class="row">
        <div class="sixteen columns">
        <?php if ($data->id) :?><a href="<?php echo base_url();?>admin/RecyclingCharges/history" class="button">&lt;- Go back</a>
        <a href="<?php echo base_url();?>admin/RecyclingCharges/Delete/<?php echo $data->id;?>" class="button">Delete</a><?php endif;?>
        <div id="tabs" style="border:0px;">
		<?php if (!$data->id) {
			include("application/views/admin/common/tabs.php"); 
		}?>            
		<span style="color:red;"><?php echo validation_errors(); ?></span>

		<div id="tabs-4" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
        <div class="sixteen columns alpha">
	        <?php echo form_open('admin/RecyclingCharges/AddEdit/' . $data->id, 'id="formAdd"');?>
	        <?php echo form_hidden('submit_form',1);?>
	        <input type="hidden" name="status" value="<?php echo set_value('status', $data->status) ?>" id="id_status"/>
	        <input type="hidden" name="dateSent" value="<?php echo set_value('dateSent', date('m/d/Y')) ?>" id="id_dateSent"/>
	        <input type="hidden" name="internalNotes" value="<?php echo set_value('internalNotes', $data->internalNotes) ?>" id="id_internalNotes"/>
        	<h5 class="dataentry">General Info</h5>
            <fieldset class="dataentry">
	            <label for='location'>Location Name or ID*</label>
	            <input type="hidden" name="locationId" value="<?php echo set_value('locationId', $data->locationId) ?>" id="locationId"/>
            	<?php echo form_hidden('locationType', set_value('locationType', $data->locationType));?>
            	<input name="locationName" readonly="readonly" onchange="document.getElementById('locationId').value=''" id="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>">
	            <input type="hidden" name="vendorId" value="<?php echo set_value('vendorId', $data->vendorId) ?>" id="vendorId"/>
	            <label for='vendorNumber'>Vendor*</label>
	            <input name="vendorNumber" readonly="readonly" onchange="document.getElementById('vendorId').value=''" id="vendorNumber" type="text" value="<?php echo set_value('vendorNumber', $data->vendorNumber);?>">
	            <label for='invoiceNumber'>Invoice #*</label>
	            <input name="invoiceNumber" readonly="readonly" type="text" value="<?php echo set_value('invoiceNumber', $data->invoiceNumber);?>">
	            <label for='date'>Invoice Date*</label>
	            <input name="date" type="text" disabled="disabled" value="<?php echo set_value('date', $data->date);?>" style="width:100px">            
          	</fieldset>
          	<?php echo form_close();?>
            <hr>            
            <h5 class="dataentry">Description</h5>
            <fieldset class="dataentry">
            	<div class="dataentry" id="dataentry">
            	<?php include("charges_and_fees_ajax.php"); ?>
            	</div>            	
            	<br>
            	<div style="color:red;" id="error"></div>
            	<!-- Charges form  -->
            	<?php echo form_open('admin/RecyclingCharges/addCharge/' . $data->id, 'id="formRecycling"'); ?>
            	<h6 class="dataentry">Charge</h6>
            	<label for='materialDate'>Date</label>
            	<input name="materialDate" disabled="disabled" type="text" style="width:100px">            
            	<label for='materialId'>Material*</label>
                <?php echo form_dropdown('materialId', $data->materialOptions, null, "style='width:150px' size=1 disabled='disabled'");?>            		
            	<label for='quantity'>Quantity*</label>
            	<input name="quantity" readonly="readonly" type="text" style="width:100px;" value="">
            	<label for='unitId'>Unit*</label>
            	<?php echo form_dropdown('unitId', $data->unitOptions,null, 'style="width:150px" disabled="disabled"');?>
            	<label for='pricePerTon'>Price Per Unit*</label>
            	<input name="pricePerTon" readonly="readonly" type="text" style="width:100px;" value="">
            	<label for='description'>Description</label>
            	<input name="description" readonly="readonly" type="text" style="width:100px;" value="">
            	<label for='releaseNumber'>Release #</label>
            	<input name="releaseNumber" readonly="readonly" type="text" style="width:100px;">
            	<label for='CBRENumber'>CBRE #</label>
            	<input name="CBRENumber" readonly="readonly" type="text" value="" style="width:100px;">
            	<button form="formRecycling">Add</button>
            	<?php echo form_close();?>
            	<script>
	            	$(document).ready(function() {            		
	            		$('#formRecycling').submit(function() {
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
	            		$.post($('#formRecycling').attr('action'), {action: 'delete', index: index},
							function(data) {								
								$('#dataentry').html(data.html);								   								
							}
						);
	            	}
            	</script>            	
            	<!-- Fees form  -->
            	<div style="color:red;" id="errorFees"></div>
            	<?php echo form_open('admin/RecyclingCharges/addFee/' . $data->id, 'id="formFees"');?>
            	<h6 class="dataentry">Fee</h6>
            	<label for='feeType'>Fee Type*</label>
            	<?php echo form_dropdown('feeType', $data->feeOptions, null, 'style="width:150px"  disabled="disabled"')?>
            	<label for='fee'>Fee*</label>
            	<input name="fee" readonly="readonly" type="text" style="width:100px;">
            	<label for='waived'>Waived/Saved</label>
            	<input name="waived" disabled="disabled" type="checkbox" value="1"><br />
            	<button form="formFees">Add</button>            	
            	<?php echo form_close();?>
            	<script>
	            	$(document).ready(function() {            		
	            		$('#formFees').submit(function() {
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
	            		$.post($('#formFees').attr('action'), {action: 'delete', index: index},
							function(data) {								
								$('#dataentry').html(data.html);								   								
							}
						);
	            	}
            	</script>
          </fieldset>                    
          <?php if ($_isAdmin) { ?>
          <hr>
          <h5 class="dataentry">Astor Only</h5>
          <fieldset class="dataentry" id="adminPanel">
          <label for="status">Complete?</label>
          <?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status), 'disabled="disabled"')?>           
          <label for="dateSent">Date Sent</label>           
          <input name="dateSent" disabled="disabled" type="text" style="width:140px" value="<?php echo set_value('dateSent', $data->dateSent?$data->dateSent:date('m/d/Y'));?>">
          <label for="internalNotes">Internal Notes</label>
          <textarea name="internalNotes" readonly="readonly" style="width:45%"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
          </fieldset>
          <hr>
          <?php } ?>
          <button onclick="document.getElementById('formAdd').submit()" style="margin-left:200px;background:#13602E; color:#fff">Save</button>
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
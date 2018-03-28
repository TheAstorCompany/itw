<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<div class="content">
<div class="row remove-bottom">
  <div class="sixteen columns">
    <h1>Admin Home</h1>
  </div>
</div>
<div class="row">
  <div class="sixteen columns">
    <div id="tabs" style="border:0px;">
      <?php include("application/views/admin/common/tabs.php"); ?>
      <div id="ui-tabs-2" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;"> <span style="color:red;" id="id_errors"></span> <span style="color:red;"><?php echo validation_errors(); ?></span> <?php echo form_open('admin/WasteInvoice/AddEdit/' . $data->id, array('id'=>'SupportRequestAddForm'));?> <?php echo form_hidden('submit',1);?>
        <input type="hidden" name="status" value="<?php echo set_value('status', $data->status) ?>" id="id_status"/>
        <input type="hidden" name="dateSent" value="<?php echo set_value('dateSent', $data->dateSent) ?>" id="id_dateSent"/>
        <input type="hidden" name="internalNotes" value="<?php echo set_value('internalNotes', $data->internalNotes) ?>" id="id_internalNotes"/>
        <div class="sixteen columns alpha">
          <h5 class="dataentry">General Info</h5>
          <fieldset class="dataentry">
            <label for='locationName'>Location Name or ID*</label>
            <input id="locationName" name="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>">
            <input id="locationId" type="hidden" name="locationId" value="<?php echo set_value('locationId', $data->locationId);?>">
            <input id="locationType" type="hidden" name="locationType" value="<?php echo set_value('locationType', $data->locationType);?>">
            <label for='vendorName'>Vendor*</label>
            <input id="vendorName" name="vendorName" type="text" value="<?php echo set_value('vendorName', $data->vendorName);?>">
            <input type="hidden" id="vendorId" name="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>">
            <label for="invoiceNumber">Invoice #*</label>
            <input name="invoiceNumber" autocomplete="off" type="text" value="<?php echo set_value('invoiceNumber', $data->invoiceNumber);?>" style="width:100px" />
            <label for='invoiceDate'>Invoice Date*</label>
            <input name="invoiceDate" autocomplete="off" type="text" value="<?php echo set_value('invoiceDate', $data->invoiceDate);?>" style="width:100px">
          </fieldset>
          <h5 class="dataentry">Description</h5>
          <fieldset class="dataentry">
            <div class="dataentry"> 
            <strong>Services</strong>
              <div id="services_container">
                <ol id="servicesList">
                </ol>
                <br>
              </div>
              <strong>Fees</strong>
              <div id="fees_container">
                <ol id="feeList">
                </ol>
                <br/>
              </div>
              Total: $<span id="total"><?php echo set_value('total', empty($data->total) ? '0.00':$data->total);?></span></div>
            <button style="background:#13602E; color:#fff">Save</button>
            <button style="background:#13602E; color:#fff">Save & New</button>
            <?php echo form_close();?> <?php echo form_open('admin/WasteInvoice/addService/' . $data->id, array('id'=>'formServices'))?>
            <h6 class="dataentry">Service</h6>
            <label for='serviceDate'>Date</label>
            <input name="serviceDate" autocomplete="off" type="text" style="width:100px">
            <label for='serviceTypeId'>Service*</label>
            <?php echo form_dropdown('serviceTypeId', $data->serviceTypeOptions, null, "style='width:150px;'");?> 
            <!--label for='serviceTypeId'>Existing Service</label>
                  <!--?php echo form_dropdown('serviceId', $data->ServicesOptions);?--> 
            <!-- <option>County Waste, 8yd compactor, 3xWeek (MoWeFr)</option> -->
            <label for='materialId'>Material</label>
            <?php echo form_dropdown('materialId', $data->materialOptions, null, "id='waste' style='width:150px;'");?>
            <label for='quantity'>Quantity</label>
            <input name="quantity" type="text" style="width:100px;">
            <label for='unitId'>Unit</label>
            <?php echo form_dropdown('unitId', $data->unitOptions,null, 'style="width:100px"');?>
            <label for='trashFee'>Rate*</label>
            <input name="trashFee" type="text" style="width:100px;">
            <!--label for="CBRENumber">CBRE #</label>
                  <input name="CBRENumber" type="text" style="width:100px;" /-->
            <button>Add</button>
            <?php echo form_close();?>
            <h6 class="dataentry">Fee</h6>
            <?php echo form_open('admin/WasteInvoice/addFee/' . $data->id, array('id'=>'formFees'))?>
            <label for='feeType'>Fee Type</label>
            <?php echo form_dropdown('feeType', $data->feeOptions, null, 'style="width:150px"')?>
            <label for='feeAmount'>Fee</label>
            <input name="feeAmount" type="text" style="width:100px;">
            <button>Add</button>
          </fieldset>
          <?php echo form_close();?>
          <?php if ($_isAdmin): ?>
          <hr /> <div id="adminPanel">
          <h5 class="dataentry">Astor Only</h5>
          <fieldset class="dataentry">
            <label for="status">Complete?</label>
            <select name="status" id="type">
              <option selected>No</option>
              <option>Yes</option>
            </select>
            <label for="dateSent">Date Sent</label>
            <input name="dateSent"  autocomplete="off" type="text" style="width:140px">
            <label for="internalNotes">Internal Notes</label>
            <textarea name="internalNotes" style="width:45%"></textarea>
          </fieldset>
          <?php endif;?>
        </div>
        <?php form_close();?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	$(function() {
		$("input[name='invoiceDate'], input[name='serviceDate'], input[name='dateSent']").datepicker({
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

		$("#vendorName").autocomplete({
			source: "<?php echo base_url(); ?>admin/WasteInvoice/autocompleteVendor",
			minLength: 2,
			select: function(e, ui) {
				$('#vendorId').val(ui.item.id);
			}
		});

		<?php if ($_isAdmin): ?>
		$('#adminPanel input,select,textarea').each(function(i, item) {
			$('#id_'+$(item).attr('name')).val($(item).val());
		});
		
		$('#adminPanel input,select,textarea').change(function() {
			$('#id_'+$(this).attr('name')).val($(this).val());
		});
		<?php endif; ?>

		$('#formFees').submit(function() {
			$.post(
				$('#formFees').attr('action'),
				$('#formFees').serialize(),
				function (result) {
					if (result.error == '') {
						document.getElementById('formFees').reset();
					}
					
					setFeesHTML(result);
				}
			);
				
			return false;
		});

		$('#formServices').submit(function() {
			$.post(
				$('#formServices').attr('action'),
				$('#formServices').serialize(),
				function (result) {
					if (result.error == '') {
						document.getElementById('formServices').reset();
					}
					
					setServicesHTML(result);
				}
			);
				
			return false;
		});

		updateFees();
		updateServices();
	});

	var fees = <?php echo json_encode($data->feeOptions);?>;
	var services = <?php echo json_encode($data->ServicesOptions);?>;

	function setFeesHTML(data, skipTotal) {
		$('#id_errors').html(data.error);

		$('#feeList li').remove();
		
		$.each(data.result, function(i, item) {

			if (!item.feeAmount) {
				item.feeAmount = '0.00';
			}
			
			if (item.id) {
				i = item.id;
			}
			
			$('#feeList').append('<li>$'+item.feeAmount + ' ' + fees[item.feeType] +' (<a href="#" onclick="return deleteFee('+i+');">Delete</a>)</li>');
		});

		if (skipTotal != true) {
			$('#total').html(data.total);
		}
	}

	function updateFees() {
		$.get($('#formFees').attr('action'), function(data) {
			setFeesHTML(data, true);
		});
	}

	function deleteFee(i) {
		$.post(
			'<?php echo base_url();?>admin/WasteInvoice/deleteFee/<?php echo $data->id;?>',
			{feeId:i},
			function (result) {
				if (result.error != '') {
					document.getElementById('formFees').reset();
				}
				
				setFeesHTML(result);
			}
		);

		return false;
	}
//services
	function setServicesHTML(data, skipTotal) {
		$('#id_errors').html(data.error);

		$('#servicesList li').remove();
		
		$.each(data.result, function(i, item) {
			
			if (item.id) {
				i = item.id;
			}
			
			$('#servicesList').append('<li>$'+(parseInt(item.quantity) * parseFloat(item.trashFee))+ ', ' +item.serviceDate + ' ' + services[item.serviceId] +' (<a href="#" onclick="return deleteService('+i+');">Delete</a>)</li>');
		});

		if (skipTotal != true) {
			$('#total').html(data.total);
		}
	}

	function updateServices() {
		$.get($('#formServices').attr('action'), function(data) {
			setServicesHTML(data, true);
		});
	}

	function deleteService(i) {
		$.post(
			'<?php echo base_url();?>admin/WasteInvoice/deleteService/<?php echo $data->id;?>',
			{serviceId:i},
			function (result) {
				if (result.error != '') {
					document.getElementById('formServices').reset();
				}
				
				setServicesHTML(result);
			}
		);

		return false;
	}
</script>
<?php include("application/views/admin/common/footer.php");?>

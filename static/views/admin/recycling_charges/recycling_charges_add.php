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
      <div id="ui-tabs-4" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px"> 
        <!-- start --> 
        <span style="color:red;" id="id_errors"></span> <span style="color:red;"><?php echo validation_errors(); ?></span> <?php echo form_open('admin/RecyclingCharges/AddEdit/' . $data->id, 'formAdd');?> <?php echo form_hidden('submit',1);?>
        <input type="hidden" name="status" value="<?php echo set_value('status', $data->status) ?>" id="id_status"/>
        <input type="hidden" name="dateSent" value="<?php echo set_value('dateSent', date('m/d/Y')) ?>" id="id_dateSent"/>
        <input type="hidden" name="internalNotes" value="<?php echo set_value('internalNotes', $data->internalNotes) ?>" id="id_internalNotes"/>
        <div class="sixteen columns alpha">
          <h5 class="dataentry">General Info</h5>
          <fieldset class="dataentry">
          <label for='vendorLocation'>Location Name or ID*</label>
            <input id="vendorLocation" name="vendorLocation" type="text" value="<?php echo set_value('vendorLocation', $data->vendorLocation);?>">
            <input type="hidden" name="vendorId" value="<?php echo set_value('vendorId', $data->vendorId) ?>" id="vendorId"/>
            <label for='vendorNumber'>Vendor</label>
            <input id="vendorNumber" name="vendorNumber" type="text" value="<?php echo set_value('vendorNumber', $data->vendorNumber);?>">
            <label for='date'>Invoice Date</label>
            <input name="date" type="text" value="<?php echo set_value('date', $data->date);?>" style="width:100px">
          </fieldset>
          <hr />
          <h5 class="dataentry">Description</h5>
          <fieldset class="dataentry">
            <div class="dataentry"> <strong>Charges</strong>
              <ol id="recyclingList">
              </ol>
              <strong>Fees</strong>
              <ol id="feeList">
              </ol>
              <br>
              Total: $<span id="total">0.00</span></div>
            <input type="hidden" name="action" value="1" />
            <button style="background:#13602E; color:#fff">Save</button>
            <button style="background:#13602E; color:#fff" onclick="document.forms[0].elements['action'].value=2;">Save & New</button>
            <?php echo form_close();?>
            <h6 class="dataentry">Charge</h6>
            <?php echo form_open('admin/RecyclingCharges/AddRecycling/' . $data->id, 'id="formRecycling"'); ?>
            <label for='materialDate'>Date</label>
            <input name="materialDate" type="text" style="width:100px" value="">
            
            <label for='materialId'>Material</label>
            <?php echo form_dropdown('materialId', $data->materialOptions, null, "style='width:150px' size=1");?>
            <label for='quantity'>Quantity</label>
            <input name="quantity" type="text" style="width:100px;" value="">
            <label for='unitId'>Unit</label>
            <?php echo form_dropdown('unitId', $data->unitOptions,null, 'style="width:100px"');?>
            <label for='pricePerTon'>Price Per Ton</label>
            <input name="pricePerTon" type="text" style="width:100px;" value="">
            <label for='description'>Description</label>
            <input name="description" type="text" style="width:100px;" value="">
            <label for='releaseNumber'>Release #</label>
            <input name="releaseNumber" type="text" style="width:100px;" value="">
            <label for='CBRENumber'>CBRE #</label>
            <input name="CBRENumber" type="text" style="width:100px;" value="" />
            <button>Add</button>
            <?php echo form_close();?>
            <h6 class="dataentry">Fee</h6>
            <?php echo form_open('admin/RecyclingCharges/addFee/' . $data->id, 'id="formFees"');?>
            <label for='feeType'>Fee Type</label>
            <?php echo form_dropdown('feeType', $data->feeOptions, null, 'style="width:150px"')?>
            <label for='fee'>Fee</label>
            <input name="fee" type="text" style="width:100px;">
            <button>Add</button>
          </fieldset>
          <?php echo form_close();?>
          <?php if ($_isAdmin): ?>
          <hr />
          <div id="adminPanel">
            <h5 class="dataentry">Astor Only</h5>
            <fieldset class="dataentry">
              <label for="type">Complete?</label>
              <?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status))?>
              <label for="dateSent">Date Sent</label>
              <input name="dateSent" type="text" style="width:140px" value="<?php echo set_value('dateSent', date('m/d/Y'));?>">
              <label for="internalNotes">Internal Notes</label>
              <textarea name="internalNotes" style="width:45%"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
            </fieldset>
          </div>
          <?php endif;?>
        </div>
        <!-- end --> 
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
		$("#location").autocomplete({
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
			source: "<?php echo base_url(); ?>admin/RecyclingCharges/autocompleteVendorByNumber",
			minLength: 2,
			select: function(e, ui) {
				$('#vendorId').val(ui.item.id);
				$('#vendorLocation').val(ui.item.addressLine1);
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

		$('#formRecycling').submit(function() {
			$.post(
				$('#formRecycling').attr('action'),
				$('#formRecycling').serialize(),
				function (result) {
					if (result.error == '') {
						document.getElementById('formRecycling').reset();
					}
					
					setRecyclingHTML(result);
				}
			);
			
			return false;
		});

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

		updateRecyclings();
		updateFees();
	});

	var total = 0;
	var materials = <?php echo json_encode($data->materialOptions); ?>;
	var fees = <?php echo json_encode($data->feeOptions);?>;
	
	function setRecyclingHTML(data) {
		$('#id_errors').html(data.error);

		$('#recyclingList li').remove();
		
		$.each(data.result, function(i, item) {

			if (!item.pricePerTon) {
				item.pricePerTon = '0.00';
			}
			
			$('#recyclingList').append('<li>$'+item.pricePerTon+ ' ' + materials[item.materialId] +' (<a href="#" onclick="return deleteRecycling('+i+');">Delete</a>)</li>');
		});

		$('#total').html(data.total);
	}

	function setFeesHTML(data) {
		$('#id_errors').html(data.error);

		$('#feeList li').remove();
		
		$.each(data.result, function(i, item) {

			if (!item.fee) {
				item.fee = '0.00';
			}
			
			$('#feeList').append('<li>$'+item.fee+ ' ' + fees[item.feeType] +' (<a href="#" onclick="return deleteFee('+i+');">Delete</a>)</li>');
		});

		$('#total').html(data.total);
	}
	
	function updateRecyclings() {
		$.get($('#formRecycling').attr('action'), function(data) {
			setRecyclingHTML(data);
		});
	}

	function updateFees() {
		$.get($('#formFees').attr('action'), function(data) {
			setFeesHTML(data);
		});
	}

	function deleteRecycling(i) {
		$.post(
			'<?php echo base_url();?>admin/RecyclingCharges/deleteRecycleItem/<?php echo $data->id;?>',
			{recyclingItemId:i},
			function (result) {
				if (result.error != '') {
					document.getElementById('formRecycling').reset();
				}
				
				setRecyclingHTML(result);
			}
		);

		return false;
	}

	function deleteFee(i) {
		$.post(
			'<?php echo base_url();?>admin/RecyclingCharges/deleteFee/<?php echo $data->id;?>',
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
</script>
<?php include("application/views/admin/common/footer.php");?>

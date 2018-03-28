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
      <div id="ui-tabs-2" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;"> 
        <!-- start --> 
        <span style="color:red;" id="id_errors"></span> <span style="color:red;"><?php echo validation_errors(); ?></span> <?php echo form_open('admin/RecyclingInvoice/AddEdit/' . $data->id);?> <?php echo form_hidden('submit',1);?>
        <input type="hidden" name="status" value="<?php echo set_value('status', $data->status) ?>" id="id_status"/>
        <input type="hidden" name="dateSent" value="<?php echo set_value('dateSent', $data->dateSent) ?>" id="id_dateSent"/>
        <input type="hidden" name="internalNotes" value="<?php echo set_value('internalNotes', $data->internalNotes) ?>" id="id_internalNotes"/>
        <div class="sixteen columns alpha">
          <h5 class="dataentry">General Info</h5>
          <fieldset class="dataentry">
            <label for='locationName'>Location Name or ID*</label>
            <input id="locationName" name="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>">
            <?php echo form_hidden('locationId', set_value('locationId', $data->locationId));?> <?php echo form_hidden('locationType', set_value('locationType', $data->locationType));?>
            <label for='vendor'>Vendor*</label>
            <input id="vendor" name="vendor" type="text" value="<?php echo set_value('vendor', $data->vendor);?>">
            <input type="hidden" name="vendorId" id="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>">
            <label for='poDate'>PO Date</label>
            <input name="poDate"  autocomplete="off" type="text" style="width:100px" value="<?php echo set_value('poDate', $data->poDate);?>">
            <label for='poNumber'>PO #</label>
            <input name="poNumber" type="text" style="width:100px;" value="<?php echo set_value('poNumber', $data->poNumber);?>">
            <label for='trailerNumber'>Trailer #</label>
            <input name="trailerNumber" type="text" style="width:100px;" value="<?php echo set_value('trailerNumber', $data->trailerNumber);?>">
            <label for='BOLNumber'>BOL #</label>
            <input name="BOLNumber" type="text" style="width:100px;" value="<?php echo set_value('BOLNumber', $data->BOLNumber);?>">
          </fieldset>
          <hr />
          <h5 class="dataentry">Purchase Order<br />
            to Company</h5>
          <fieldset class="dataentry">
            <div class="dataentry">
            <strong>Materials</strong>  
              <ol id="recyclingList">
              </ol>
              
<strong>Fees from Invoice</strong>
              <ol id="feeList">
              </ol>
              
              Total: $<span id="total">0.00</span>            </div>
            <input type="hidden" name="action" value="1" />
            <button style="background:#13602E; color:#fff">Save</button>
            <button style="background:#13602E; color:#fff" onclick="document.forms[0].elements['action'].value=2;">Save & New</button>
            <?php echo form_close();?>
            <h6 class="dataentry">Material</h6>
            <?php echo form_open('admin/RecyclingInvoice/AddMaterial/' . $data->id, 'id="formRecycling"');?>
            <label for='materialId'>Material</label>
            <?php echo form_dropdown('materialId', $data->materialOptions, null, array('id'=>'waste', 'style'=>'width:150px;'));?>
            <label for='quantity'>Quantity</label>
            <input name="quantity" type="text" style="width:100px;">
            <label for='unit'>Unit</label>
            <?php echo form_dropdown('unit', $data->unitOptions,null, 'style="width:100px"');?>
            <label for='pricePerUnit'>Market Price Per Unit</label>
            <input name="pricePerUnit" type="text" style="width:100px;">
            <label for='CBRENumber'>CBRE #</label>
            <input name="CBRENumber" type="text" style="width:100px;" />
            <button>Add</button>
            <?php echo form_close();?>
            <h6 class="dataentry"> Fee</h6>
            <?php echo form_open('admin/RecyclingInvoice/addFee/' . $data->id, 'id="formFees"');?>
            <label for='waste2'>Fee Type</label>
            <?php echo form_dropdown('feeType', $data->feeOptions, null, 'style="width:150px"')?>
            <label for='feeAmount'>Fee</label>
            <input name="feeAmount" type="text" style="width:100px;">
            <button>Add</button>
          </fieldset>
          <?php echo form_close();?>
          <hr />
          <h5 class="dataentry">Invoice to Vendor</h5>
          <fieldset class="dataentry">
            <div class="dataentry"><strong>Materials
              from PO</strong>              
              <ol id="invoiceList">
              </ol>
                  <strong>Fees</strong>
                  <ol>
                <li><strong>Recycling&nbsp;&nbsp;-&nbsp;&nbsp;$100</strong> &nbsp;<em>&nbsp;</em><a href="#">Delete</a></li>
                <li><strong>Repair &nbsp;-&nbsp;&nbsp;$100</strong> &nbsp;&nbsp; <a href="#">Delete</a> &nbsp;&nbsp;</li>
              </ol>
                  <h6 class="highlight">Total $4,200.00 </h6>
                </div> 
            <?php echo form_open('admin/RecyclingInvoice/addInvoice/' . $data->id, 'id="formInvoices"');?>
            <label for='invoiceNumber'>Invoice  #</label>
            <input name="invoiceNumber" type="text" style="width:100px;">
            <label for='invoiceDate'>Invoice  Date</label>
            <input name="invoiceDate" autocomplete="off" type="text" style="width:100px">
            <label for='pricePerTon'>Price Per Ton</label>
            <input name="pricePerTon" type="text" style="width:100px;">
            <button>Add</button>
          </fieldset>
          <?php echo form_close();?>
          <?php if ($_isAdmin): ?>
          <hr />
          <div id="adminPanel">
            <h5 class="dataentry">Astor Only</h5>
            <fieldset class="dataentry">
              <span style="float:left;padding-right:10px">
              <label for="type">Complete?</label>
              <?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status))?> </span>
              <label for="dateSent">Date Sent</label>
              <input name="dateSent" type="text" style="width:140px" value="<?php echo set_value('dateSent', $data->dateSent);?>">
              <label for="internalNotes">Internal Notes</label>
              <textarea name="internalNotes" style="width:45%"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
            </fieldset>
            <?php endif;?>
          </div>
        </div>
        <!-- end --> 
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(function() {
	$("input[name='invoiceDate'], input[name='dateSent'], input[name='poDate']").datepicker({
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

	$("#vendor").autocomplete({
		source: "<?php echo base_url(); ?>admin/RecyclingInvoice/autocompleteVendor",
		minLength: 2,
		select: function(e, ui) {
			$('#vendorId').val(ui.item.id);
		}
	});

<?php if ($_isAdmin): ?>
	$('#adminPanel input,select,textarea').change(function() {
		$('#id_'+$(this).attr('name')).val($(this).val());
	});
<?php endif; ?>

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

	$('#formInvoices').submit(function() {
		$.post(
			$('#formInvoices').attr('action'),
			$('#formInvoices').serialize(),
			function (result) {
				if (result.error == '') {
					document.getElementById('formInvoices').reset();
				}
				
				setInvoicesHTML(result);
			}
		);
			
		return false;
	});

	updateRecyclings();
	updateFees();
	updateInvoices();
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

		
		if (item.id) {
			i = item.id;
		}
		
		$('#recyclingList').append('<li>$'+item.pricePerUnit+ ' x '+ item.quantity + ' ' + materials[item.materialId] +' (<a href="#" onclick="return deleteRecycling('+i+');">Delete</a>)</li>');
	});

	$('#total').html(data.total);
}

function setFeesHTML(data) {
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

	$('#total').html(data.total);
}

function setInvoicesHTML(data) {
	$('#id_errors').html(data.error);

	$('#invoiceList li').remove();
	
	$.each(data.result, function(i, item) {

		if (!item.fee) {
			item.fee = '0.00';
		}

		if (item.id) {
			i = item.id;
		}
		
		$('#invoiceList').append('<li>'+item.invoiceNumber +', '+ item.invoiceDate +' (<a href="#" onclick="return deleteInvoice('+i+');">Delete</a>)</li>');
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

function updateInvoices() {
	$.get($('#formInvoices').attr('action'), function(data) {
		setInvoicesHTML(data);
	});
}

function deleteRecycling(i) {
	$.post(
		'<?php echo base_url();?>admin/RecyclingCharges/deleteRecycleItem/<?php echo $data->id;?>',
		{materialId:i},
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
		'<?php echo base_url();?>admin/RecyclingInvoice/deleteFee/<?php echo $data->id;?>',
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

function deleteInvoice(i) {
	$.post(
		'<?php echo base_url();?>admin/RecyclingInvoice/deleteInvoice/<?php echo $data->id;?>',
		{invoiceId:i},
		function (result) {
			if (result.error != '') {
				document.getElementById('formInvoices').reset();
			}
			
			setInvoicesHTML(result);
		}
	);

	return false;
}
</script>
<?php include("application/views/admin/common/footer.php");?>

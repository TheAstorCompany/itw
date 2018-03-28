<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
      <div class="content">
    <div class="row remove-bottom">
          <div class="sixteen columns">
        <h1><?php if ($data->id):?>Edit Recycling purchase order #<?php echo $data->id; ?><?php else:?>Enter Recycling purchase order<?php endif;?></h1>
      </div>
        </div>
    <div class="row">
          <div class="sixteen columns">
        <?php if ($data->id) :?><a href="<?php echo base_url();?>admin/RecyclingInvoice/history" class="button">&lt;- Go back</a>
        <a href="<?php echo base_url();?>admin/RecyclingInvoice/Delete/<?php echo $data->id;?>" class="button">Delete</a><?php endif;?>
        <div id="tabs" style="border:0px;">
<?php if (!$data->id) {
	include("application/views/admin/common/tabs.php"); 
}?>
            <div id="ui-tabs-2" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;">
<!-- start -->
            
        <br />
         <span style="color:red;" id="id_errors"></span>
         <span style="color:red;"><?php echo validation_errors(); ?></span>
         <div class="sixteen columns alpha">
         <?php echo form_open('admin/RecyclingInvoice/AddEdit/' . $data->id, "id=\"generalForm\"");?>
              <h5 class="dataentry">General Info</h5>
              <fieldset class="dataentry">
            <label for='locationName'>Location Name or ID*</label>
            <input id="locationName" readonly="readonly" name="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>">
            <?php echo form_hidden('locationId', set_value('locationId', $data->locationId));?>
            <?php echo form_hidden('locationType', set_value('locationType', $data->locationType));?>
            <label for='vendor'>Vendor*</label>
            <input id="vendor" name="vendor" readonly="readonly" type="text" value="<?php echo set_value('vendor', $data->vendor);?>">
            <input type="hidden" name="vendorId" id="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>">
            <label for='trailerNumber'>Trailer #</label>
            <input id ="trailerNumber" readonly="readonly" name="trailerNumber" type="text" style="width:100px;" value="<?php echo set_value('trailerNumber', $data->trailerNumber);?>">
            <label for='releaseNumber'>Release #</label>
            <input id = "poNumber" readonly="readonly" name="poNumber" type="text" style="width:100px;" value="<?php echo set_value('poNumber', $data->poNumber);?>">
            <label for='BOLNumber'>Bill Of Lading #</label>
            <input id="BOLNumber" readonly="readonly" name="BOLNumber" type="text" style="width:100px;" value="<?php echo set_value('BOLNumber', $data->BOLNumber);?>">
            <label for='CBRENumber'>CBRE #</label>
            <input id="CBRENumber" readonly="readonly" name="CBRENumber" type="text" style="width:100px;" value="<?php echo set_value('CBRENumber', $data->CBRENumber);?>">
            <label for='invoiceDate'>Date</label>
            <input id="invoiceDate" disabled="disabled" name="invoiceDate" type="text" style="width:100px" autocomplete="off" value="<?php echo set_value('invoiceDate', $data->invoiceDate);?>">
            <?php echo form_close();?>
            
			<hr>
			<label for='waste'>Material</label>
			<?php echo form_dropdown('materialId', $data->materialOptions, null, "id='waste' style='width:150px;' disabled='disabled'");?>
                <label for='type2'>Unit</label>
				<?php echo form_dropdown('unit', $data->unitOptions,null, 'id="unit" style="width:150px" disabled="disabled"');?>
				<?php if($data ->id) {?>
				<input type="button" value="Add Material" onclick="addMaterial()">
				<?php } else {?>
				<input type="button" value="Add Material" onclick="noSaveWarning();">
				<?php }?>
                
          </fieldset><hr>
          <button style="margin-left:200px;background:#13602E; color:#fff" onclick="document.getElementById('generalForm').submit()">Save</button>
              <button style="background:#777; color:#fff" onclick="window.location = window.location" >Cancel</button>
           <hr>
              <h5 class="dataentry">Invoice to Vendor </h5>
              <fieldset class="dataentry">
            
            <div id="invoiceFeeList">
            	<?php include("application/views/admin/recycling_invoice_read_only/feesList.php");?>
            </div>

            <br>
            <label for='location2'>Release/Invoice  #</label>
            <input id ="releaseNumber" readonly="readonly" name="releaseNumber" type="text" style="width:100px;" value="<?php echo set_value('releaseNumber', $data->releaseNumber);?>">
            <label for='iDate'>Invoice  Date</label>
            <input id="iDate" name="iDate" disabled="disabled" type="text" style="width:100px" autocomplete="off" value="<?php echo set_value('iDate', $data->iDate);?>">
            
            
            
            <h6 class="dataentry">Material</h6>
            <label for='waste'>Materials</label>
			<?php echo form_dropdown('invoiceMaterial', $data->allMaterials, null, "id=invoiceMaterial style='width:300px' size='1' disabled='disabled'");?>            

            <label for='quantity'>Quantity</label>
            <input name="quantity" readonly="readonly" id="quantity" type="text" style="width:100px;">
            
            <label for='pricePerUnit'>Market Price Per Unit</label>
            <input name="pricePerUnit" readonly="readonly" id="pricePerUnit" type="text" style="width:100px;">
            <button onclick="addInvoiceMaterial()">Add</button>
            
            
            <h6 class="dataentry"> Fee</h6>
            <label for='feeTpye'>Fee Type</label>
			<?php echo form_dropdown('feeType', $data->feeOptions, null, "id='feeType' style='width:150px' disabled='disabled'");?>            
            <label for='fee'>Fee</label>
            <input name="fee" readonly="readonly" id="fee" type="text" style="width:100px;">
            <label for='waived'>Waived/Saved</label>
            <input name="wived" id="waived" disabled="disabled" type="checkbox" ><br />
            <button onclick="addInvoiceFee()">Add</button>
          </fieldset>
              <hr>
              <h5 class="dataentry">Purchase Order <br>
            to Company</h5>
              <fieldset class="dataentry">
            

              
              
            <div id="orderFeeList">
            	<?php include("application/views/admin/recycling_invoice_read_only/orderList.php");?>
            </div>
              
            <br>
            <label for='rPNumber'>Release/PO #</label>
            <input id ="rPNumber" name="rPNumber" readonly="readonly" type="text" style="width:100px;" value="<?php echo set_value('rPNumber', $data->poNumber);?>">
            <label for='poDate'>PO Date</label>
            <input name="poDate" id="poDate" disabled="disabled" type="text" style="width:100px" value="">
            
            <h6 class="dataentry">Material</h6>
            <label for='orderMaterial'>Material</label>
			<?php echo form_dropdown('orderMaterial', $data->allMaterials, null, "id=orderMaterial style='width:300px' size='1' disabled='disabled' ");?>
            <label for='orderPricePerUnit'>Market Price Per Unit</label>
            <input name="orderPricePerUnit" id="orderPricePerUnit" readonly="readonly" type="text" style="width:100px;">
            <button onclick="addOrderMaterial()">Add</button>
            
            
            </fieldset>

             
              <hr>
              
           <?php echo form_open('admin/RecyclingInvoice/SaveData/' . $data->id, "id=\"SaveDataForm\"");?>
           <input type="hidden" name="action" id="dataFormAction" value="" />
           <?php if ($_isAdmin): ?>
            <h5 class="dataentry">Astor Only</h5>
              <fieldset class="dataentry">
            <label for="status">Complete?</label>
			<?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status), "disabled=\"disabled\"")?>
            <label for="dateSent">Date Sent</label>
            <input name="dateSent" disabled="disabled" id="dateSent" type="text" style="width:140px"  value="<?php echo set_value('dateSent', $data->dateSent);?>">
            <label for="internalNotes">Internal Notes</label>
            <textarea name="internalNotes" readonly="readonly" id="internalNotes" style="width:45%"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
	          </fieldset>
              <hr>
              <?php endif; ?>
              <?php echo form_close();?>
              <button style="margin-left:200px;background:#13602E; color:#fff" onclick="saveDataForm('save')">Save</button>
           	  <button style="background:#777; color:#fff" onclick="saveDataForm('cancel')" >Cancel</button>
            </div>
	       
	       

      
      		<script type="text/javascript">

				function saveDataForm(action) {
					document.getElementById("dataFormAction").value = action;
					document.getElementById("SaveDataForm").submit();
				}
      		</script>
      
      
      
      
      
      
      
      
      
                  
            
            
            
            
            
            
            
            
            
            
            
            
             
             
             
             
             
             
             
             
             
             
             
<!-- end -->            
          </div>
      </div>
      </div>
  </div>
  
  
<div id="dialog-message" title="Warning">
	<p id="popUpMessage">
		Please, first save the invoice
	</p>
</div>

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
<script type="text/javascript">
$(function() {
	$("input[name='invoiceDate'], input[name='iDate'], input[name='poDate'], input[name='dateSent']").datepicker({
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

	$('#locationName').change(function() {
		$("input[name='locationId']").val('');
	});

	$("#vendor").autocomplete({
		source: "<?php echo base_url(); ?>admin/RecyclingInvoice/autocompleteVendor",
		minLength: 2,
		select: function(e, ui) {
			$('#vendorId').val(ui.item.id);
		}
	});

	$('#vendor').change(function() {
		$('#vendorId').val('');
	});

<?php if ($_isAdmin): ?>
	$('#adminPanel input,select,textarea').change(function() {
		$('#id_'+$(this).attr('name')).val($(this).val());
	});
<?php endif; ?>
/*
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
*/	
	updateMaterialSelect();
});

var total = 0;
var materials = <?php echo json_encode($data->materialOptions); ?>;
var fees = <?php echo json_encode($data->feeOptions);?>;

function updateMaterialSelect() {
	var usedMaterials = JSON.parse($('#id_usedMaterials').val());
	var dis = false;
	
	$('#invoiceMaterial option').removeAttr('disabled');
	$('#orderMaterial option').removeAttr('disabled');
	$('#orderMaterial option').eq(0).attr('selected','selected');
	
	$('#invoiceMaterial option').each(function(idx, item){
		if (idx > 0) {
			$.each(usedMaterials, function(jidx, jitem) {

				if (jitem == $(item).val()) {
					$(item).attr('disabled','disabled');
					dis = true;
					
					return false;
				}
			});

			//alert('dis:'+dis+' idx:'+idx);
	
			if (!dis) {
				$('#orderMaterial option').eq(idx).attr('disabled', 'disabled');
			} else {
				$('#orderMaterial option').eq(idx).removeAttr('disabled');
			}
	
			dis = false;
		}
	});
}

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


//-------------------------------------------------------------------------------------------------

	$(function() {
		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
	
		$( "#dialog-message" ).dialog({
			modal: true,
			autoOpen: false,
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
	
function noSaveWarning() {
	document.getElementById("popUpMessage").innerHTML = "Please, first save the invoice!";
	$("#dialog-message").dialog("open");

}
function addMaterial() {
	var material = document.getElementById("waste").value;
	var unit = document.getElementById("unit").value;
	 
	if((unit == 0) || (material == 0)) {
		document.getElementById("popUpMessage").innerHTML = "Please, select material and unit!";
		$("#dialog-message").dialog("open");
		return;
	} 
	$.post(
			'<?php echo base_url();?>admin/RecyclingInvoice/addMaterialUnit/<?php echo $data->id;?>',
			{materialId:material, unitId:unit},
			function (result) {
				if (result.error != '') {
					window.location = window.location
				}
			}
	);
	
}

function addInvoiceMaterial() {
	invoiceID = "<?php echo $data->id ?>";
	releaseNumber = document.getElementById("releaseNumber").value;
	iDate = document.getElementById("iDate").value;
	material = document.getElementById("invoiceMaterial").value;
	
	quantity = document.getElementById("quantity").value;
	pricePerUnit = document.getElementById("pricePerUnit").value;

	if(!material || !quantity || !pricePerUnit || (material == 0)) {
		document.getElementById("popUpMessage").innerHTML = "Please, select Materials, Quantity and Market Price Per Unit!";
		$("#dialog-message").dialog("open");
		return;
	}
	
	if(!invoiceID) {
		noSaveWarning();
		return;
	}


	$('#invoiceMaterial option:selected').attr('disabled','disabled');//.find('option:first').attr('selected', 'selected');
	$('#invoiceMaterial option:first').attr('selected', 'selected');

	$.post(
			'<?php echo base_url();?>admin/RecyclingInvoice/addInvoiceMaterial/<?php echo $data->id;?>',
			{materialId:material, qVal:quantity, ppu:pricePerUnit, poNumber:releaseNumber, date:iDate},
			function (result) {
				if (result.error != '') {
					document.getElementById("invoiceFeeList").innerHTML = result;
					document.getElementById("quantity").value = "";
					document.getElementById("pricePerUnit").value = "";	

					updateMaterialSelect();		
				}
			}
	);

	
	
}

function addInvoiceFee() {
	invoiceID = "<?php echo $data->id ?>";
	releaseNumber = document.getElementById("releaseNumber").value;
	iDate = document.getElementById("iDate").value;
	feeTypeData = document.getElementById("feeType").value;
	feeData = document.getElementById("fee").value;
	waivedData = document.getElementById("waived").checked?1:0;


	if(!invoiceID) {
		noSaveWarning();
		return;
	}
	
	if(!feeData || !feeTypeData) {
		document.getElementById("popUpMessage").innerHTML = "Please, select FeeType and Fee!";
		$("#dialog-message").dialog("open");
		return;
	}

	$.post(
			'<?php echo base_url();?>admin/RecyclingInvoice/addInvoiceFee/<?php echo $data->id;?>',
			{fee:feeData, feeType:feeTypeData, waived:waivedData, poNumber:releaseNumber, date:iDate},
			function (result) {
				if (result.error != '') {
					document.getElementById("invoiceFeeList").innerHTML = result;
					//$('#feesOrderList').html($('#feesInvoiceList').html());
					//$('#feesOrderList span').remove();
					updateOrder(invoiceID);
					document.getElementById("fee").value = "";
				}
			}
	);
}

function deleteInvoicePart(ID, Part) {
	$.post(
			'<?php echo base_url();?>admin/RecyclingInvoice/deleteInvoicePart/<?php echo $data->id;?>',
			{id:ID, part:Part},
			function (result) {
				if (result.error != '') {
					document.getElementById("invoiceFeeList").innerHTML = result;
					updateMaterialSelect();

					if (Part == 'fees') {
						updateOrder(ID);
					}
				}
			}
	);
}

function deleteOrderPart(ID, Part) {
	$.post(
			'<?php echo base_url();?>admin/RecyclingInvoice/deleteOrderPart/<?php echo $data->id;?>',
			{id:ID, part:Part},
			function (result) {
				if (result.error != '') {
					document.getElementById("orderFeeList").innerHTML = result;
					updateMaterialSelect();
				}
			}
	);
}

function updateOrder(ID) {
	$.post(
			'<?php echo base_url();?>admin/RecyclingInvoice/OrderHtml/<?php echo $data->id;?>',
			{id:ID},
			function (result) {
				if (result.error != '') {
					document.getElementById("orderFeeList").innerHTML = result;
					updateMaterialSelect();
				}
			}
	);
}


function addOrderMaterial() {
	invoiceID = "<?php echo $data->id ?>";
	releaseNumber = document.getElementById("rPNumber").value;
	poDate = document.getElementById("poDate").value;
	material = document.getElementById("orderMaterial").value;
	pricePerUnit = document.getElementById("orderPricePerUnit").value;

	if(!invoiceID) {
		noSaveWarning();
		return;
	}
	if(!material || !pricePerUnit || (material == 0)) {
		document.getElementById("popUpMessage").innerHTML = "Please, select Materials and Market Price Per Unit!";
		$("#dialog-message").dialog("open");
		return;
	}

	$.post(
			'<?php echo base_url();?>admin/RecyclingInvoice/addOrderMaterial/<?php echo $data->id;?>',
			{materialId:material, ppu:pricePerUnit, poNumber:releaseNumber, date:poDate},
			function (result) {
				if (result.error != '') {
					document.getElementById("orderFeeList").innerHTML = result;
					document.getElementById("orderPricePerUnit").value = "";				
				}
			}
	);

	
	
}



</script>


<?php include("application/views/admin/common/footer.php");?>
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
            
        <br />
         <span style="color:red;" id="id_errors"></span>
         <span style="color:red;"><?php echo validation_errors(); ?></span>
         <div class="sixteen columns alpha">
         <?php echo form_open('admin/RecyclingInvoice/AddEdit/' . $data->id, "id=\"generalForm\"");?>
              <h5 class="dataentry">General Info</h5>
              <fieldset class="dataentry">
            <label for='locationName'>Location Name or ID*</label>
            <input id="locationName" name="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>">
            <?php echo form_hidden('locationId', set_value('locationId', $data->locationId));?>
            <?php echo form_hidden('locationType', set_value('locationType', $data->locationType));?>
            <label for='vendor'>Vendor*</label>
            <input id="vendor" name="vendor" type="text" value="<?php echo set_value('vendor', $data->vendor);?>">
            <input type="hidden" name="vendorId" id="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>">
            <label for='trailerNumber'>Trailer #</label>
            <input id ="trailerNumber" name="trailerNumber" type="text" style="width:100px;" value="<?php echo set_value('trailerNumber', $data->trailerNumber);?>">
            <label for='releaseNumber'>Release #</label>
            <input id = "poNumber" name="poNumber" type="text" style="width:100px;" value="<?php echo set_value('poNumber', $data->poNumber);?>">
            <label for='BOLNumber'>Bill Of Lading #</label>
            <input id="BOLNumber" name="BOLNumber" type="text" style="width:100px;" value="<?php echo set_value('BOLNumber', $data->BOLNumber);?>">
            <label for='CBRENumber'>CBRE #</label>
            <input id="CBRENumber" name="CBRENumber" type="text" style="width:100px;" value="<?php echo set_value('CBRENumber', $data->CBRENumber);?>">
            <label for='invoiceDate'>Date</label>
            <input id="invoiceDate" name="invoiceDate" type="text" style="width:100px" autocomplete="off" value="<?php echo set_value('invoiceDate', $data->invoiceDate);?>">
            <?php echo form_close();?>
            
			<hr>
			<label for='waste'>Material</label>
			<?php echo form_dropdown('materialId', $data->materialOptions, null, "id='waste' style='width:150px;'");?>
                <label for='type2'>Unit</label>
				<?php echo form_dropdown('unit', $data->unitOptions,null, 'id="unit" style="width:150px"');?>
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
            
            <div id="invoiceFeeList"></div>

            <br>
            <label for='location2'>Release/Invoice  #</label>
            <input id ="releaseNumber" name="releaseNumber" type="text" style="width:100px;" value="<?php echo set_value('releaseNumber', $data->releaseNumber);?>">
            <label for='iDate'>Invoice  Date</label>
            <input id="iDate" name="iDate" type="text" style="width:100px" autocomplete="off" value="<?php echo set_value('iDate', $data->iDate);?>">
            
            
            
            <h6 class="dataentry">Material</h6>
            <label for='waste'>Materials</label>
			<?php echo form_dropdown('invoiceMaterial', $data->allMaterials, null, "id=invoiceMaterial style='width:300px' size='1'");?>            

            <label for='quantity'>Quantity</label>
            <input name="quantity" id="quantity" type="text" style="width:100px;">
            
            <label for='pricePerUnit'>Market Price Per Unit</label>
            <input name="pricePerUnit" id="pricePerUnit" type="text" style="width:100px;">
            <button onclick="addInvoiceMaterial()">Add</button>
            
            
            <h6 class="dataentry"> Fee</h6>
            <label for='feeTpye'>Fee Type</label>
			<?php echo form_dropdown('feeType', $data->feeOptions, null, "id='feeType' style='width:150px'");?>            
            <label for='fee'>Fee</label>
            <input name="fee" id="fee" type="text" style="width:100px;">
            <label for='waived'>Waived/Saved</label>
            <input name="wived" id="waived" type="checkbox" ><br />
            <button onclick="addInvoiceFee()">Add</button>
          </fieldset>
              <hr>
              <h5 class="dataentry">Purchase Order <br>
            to Company</h5>
              <fieldset class="dataentry">
            

            <div class="dataentry"><strong>Material</strong>
              <ol>
                <li>Cardboard - $4000&nbsp;&nbsp;&#8226;&nbsp;&nbsp;40 Tons @ <strong>$100&nbsp;&nbsp;</strong>&nbsp;&nbsp; <a href="#">Delete</a></li>
                <li>Plastic - $4000&nbsp;&nbsp;&#8226;&nbsp;&nbsp;40 Tons @ <strong>$100&nbsp;&nbsp;</strong>&nbsp;&nbsp; <a href="#">Delete</a></li>
              </ol>
                  <strong>Fees</strong>
                  <ol>
                <li>Recycling&nbsp;&nbsp;-&nbsp;&nbsp;$100</li>
                <li>Repair &nbsp;-&nbsp;&nbsp;$100</li>
                <li>Enviromental - $90 (Waived)</li>
              </ol>
                  <h6 class="highlight">Total $3,800.00 </h6>
            </div>
            
            
            <br>
            <label for='location4'>Release/PO #</label>
            <input name="location4" type="text" style="width:100px;" value="444444">
            <label for='location18'>PO Date</label>
            <input name="poDate" id="poDate" type="text" style="width:100px" value="">
            
            <h6 class="dataentry">Material</h6>
            <label for='waste'>Material</label>
			<?php echo form_dropdown('orderMaterial', $data->allMaterials, null, "id=orderMaterial style='width:300px' size='1'");?>
            <label for='location4'>Market Price Per Unit</label>
            <input name="location5" type="text" style="width:100px;">
            <button>Add</button>
            
            
            </fieldset>

             
              <hr>
              
              
              <h5 class="dataentry">Astor Only</h5>
              <fieldset class="dataentry">
            <label for="type">Complete?</label>
            <select name="type" id="type">
                  <option selected>No</option>
                  <option>Yes</option>
                </select>
            <label for="type">Date Sent</label>
            <input name="date" type="text" style="width:140px" value="5/22/12">
            <label for="message">Internal Notes</label>
            <textarea name="message" style="width:45%"></textarea>
          </fieldset>
              <hr>
              <button style="margin-left:200px;background:#13602E; color:#fff">Save</button>
              <button style="background:#777; color:#fff">Cancel</button>
            </div>
      
      
      
      
      
      
      
      
      
      
      
      
      
                  
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            <span style="color:red;" id="id_errors"></span>
            <span style="color:red;"><?php echo validation_errors(); ?></span>
            
            <?php echo form_hidden('submit',1);?>
            <input type="hidden" name="status" value="<?php echo set_value('status', $data->status) ?>" id="id_status"/>
            <input type="hidden" name="dateSent" value="<?php echo set_value('dateSent', $data->dateSent) ?>" id="id_dateSent"/>
            <input type="hidden" name="internalNotes" value="<?php echo set_value('internalNotes', $data->internalNotes) ?>" id="id_internalNotes"/>
            <div class="five columns alpha">
            <?php /*
            	<h5  style="color:#7ABF53">General Info</h5>
            		<label for='vendor'>Vendor</label>
            		<input id="vendor" name="vendor" type="text" value="<?php echo set_value('vendor', $data->vendor);?>">
            		<input type="hidden" name="vendorId" id="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>">
            		<label for='locationName'>Location</label>
                  <input id="locationName" name="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>">
                  <?php echo form_hidden('locationId', set_value('locationId', $data->locationId));?>
                  <?php echo form_hidden('locationType', set_value('locationType', $data->locationType));?>
                   <label for='poDate'>PO Date</label>
                    <input name="poDate"  autocomplete="off" type="text" style="width:100px" value="<?php echo set_value('poDate', $data->poDate);?>">
                    <label for='poNumber'>PO #</label>
                  <input name="poNumber" type="text" style="width:100px;" value="<?php echo set_value('poNumber', $data->poNumber);?>">
                  
                  <label for='trailerNumber'>Trailer #</label>
                  <input name="trailerNumber" type="text" style="width:100px;" value="<?php echo set_value('trailerNumber', $data->trailerNumber);?>">
                    
                  <label for='BOLNumber'>BOL #</label>
                  <input name="BOLNumber" type="text" style="width:100px;" value="<?php echo set_value('BOLNumber', $data->BOLNumber);?>">
             */?>     
                  <h5 style="color:#7ABF53">PO Description</h5>
                  	<ol id="recyclingList">
	            	</ol>
                    <br>
              		<h5 style="color:#7ABF53">Fee</h5>
	            	<ol id="feeList">
	            	</ol>
                    <br>
              		<h5 style="color:#7ABF53">Invoices</h5>
	            	<ol id="invoiceList">
	            	</ol>
                  <br>
                Total: $<span id="total">0.00</span>
                <br><br>
                <input type="hidden" name="action" value="1" />
                <button style="background:#13602E; color:#fff">Save</button>
                <button style="background:#13602E; color:#fff" onclick="document.forms[0].elements['action'].value=2;">Save & New</button>
                </div>
             	
                <div class="nine columns omega">
                  <h5  style="color:#7ABF53">Material Info</h5>
                  <?php echo form_open('admin/RecyclingInvoice/AddMaterial/' . $data->id, 'id="formRecycling"');?>
                  <table border="0" cellspacing="10" cellpadding="10" width="100%">
                   <tr>
                      <td><label for='materialId'>Material</label>
                      	<?php echo form_dropdown('materialId', $data->materialOptions, null, array('id'=>'waste', 'style'=>'width:150px;'));?>
					  </td>
                      
                      <td><label for='quantity'>Quantity</label>
                    <input name="quantity" type="text" style="width:100px;"></td>
                      <td><label for='unit'>Unit</label>
                    	<?php echo form_dropdown('unit', $data->unitOptions,null, 'style="width:100px"');?>
                      </td>
                      <td>
                      	<label for='pricePerUnit'>Price Per Unit</label>
                        <input name="pricePerUnit" type="text" style="width:100px;">
                      </td>
                      
                      <td>
                      	<br><button>Add</button>
                      </td>
                    </tr>
                    <tr>
                        <td>
                            <label for='CBRENumber'>CBRE #</label>
                            <input name="CBRENumber" type="text" style="width:100px;" />
                        </td>
                    </tr>
                </table>
                <?php echo form_close();?>
                <h5  style="color:#7ABF53"> Fee</h5>
                <?php echo form_open('admin/RecyclingInvoice/addFee/' . $data->id, 'id="formFees"');?>
                  <table border="0" cellpadding="10" cellspacing="10" width='60%'>
                    <tr>
                      <td>
                      	<label for='waste2'>Fee Type</label>
                      	<?php echo form_dropdown('feeType', $data->feeOptions, null, 'style="width:150px"')?>
                      </td>
                      <td>
                      	<label for='feeAmount'>Fee</label>
                      	<input name="feeAmount" type="text" style="width:100px;">
                      </td>
                      <td>
                      	<br><button>Add</button>
                      </td>
                    </tr>
                </table>
                <?php echo form_close();?>
                <hr>
                <h5  style="color:#7ABF53"> Invoice Info</h5>
                <?php echo form_open('admin/RecyclingInvoice/addInvoice/' . $data->id, 'id="formInvoices"');?>
                <table border="0" cellspacing="10" cellpadding="10" width="100%">
                	<tr>
                	<?php /*
                		<td>
                			<label for='invoiceDate'>Invoice  Date</label>
                    		<input name="invoiceDate" autocomplete="off" type="text" style="width:100px">
                    	</td>
                    */ ?>
                  		<td>
                  			<label for='invoiceNumber'>Invoice  #</label>
                  			<input name="invoiceNumber" type="text" style="width:100px;">
                  		</td>
                  		<td colspan="2">
                  			<label for='pricePerTon'>Price Per Ton</label>
                  			<input name="pricePerTon" type="text" style="width:100px;">
                  		</td>
                  		<td>
                  			<br>
                  			<button>Add</button>
                  		</td>
                    </tr>
                </table>
                <?php echo form_close();?>
                <?php if ($_isAdmin): ?>
                <div style="padding:10px;background:#efefef" id="adminPanel">
	                <h5  style="color:#7ABF53">Admin Info</h5>
	               	<span style="float:left;padding-right:10px">
	                	<label for="type">Complete?</label>
	                	<?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status))?>
	                </span> 
	                <label for="dateSent">Date Sent</label>
	                <input name="dateSent" type="text" style="width:140px" value="<?php echo set_value('dateSent', $data->dateSent);?>">
	                <label for="internalNotes">Internal Notes</label>
	                <textarea name="internalNotes" style="width:95%"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
             	<?php endif;?>
              	</div>
             </div>
             
             
             
             
             
             
             
             
             
             
             
             
             
             
             
             
             
             
             
             
             
             
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
	$("input[name='invoiceDate'], input[name='iDate'], input[name='poDate']").datepicker({
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

	if(!invoiceID) {
		noSaveWarning();
		return;
	}
	if(!material || !quantity || !pricePerUnit) {
		document.getElementById("popUpMessage").innerHTML = "Please, select Materials, Quantity and Market Price Per Unit!";
		$("#dialog-message").dialog("open");
		return;
	}

	$.post(
			'<?php echo base_url();?>admin/RecyclingInvoice/addInvoiceMaterial/<?php echo $data->id;?>',
			{materialId:material, qVal:quantity, ppu:pricePerUnit, poNumber:releaseNumber, date:iDate},
			function (result) {
				if (result.error != '') {
					document.getElementById("invoiceFeeList").innerHTML = result;
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
				}
			}
	);
}



</script>


<?php include("application/views/admin/common/footer.php");?>
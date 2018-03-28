<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<div class="content">
    <div class="row remove-bottom" style="margin-bottom:0;">
        <div class="sixteen columns">
            <h1><?php if ($data->id):?>Edit Recycling purchase order #<?php echo $data->id; ?><?php else:?>Enter Recycling purchase order<?php endif;?></h1>
        </div>
    </div>
    <div class="row" style="margin-bottom:0;">
        <div class="sixteen columns" style="width:100%;">
        <?php if ($data->id) :?><a href="<?php echo base_url();?>admin/RecyclingInvoice/history" class="button">&lt;- Go back</a>
        <a href="<?php echo base_url();?>admin/RecyclingInvoice/Delete/<?php echo $data->id;?>" class="button">Delete</a><?php endif;?>
        <div id="tabs" style="border:0px;">
        <?php if (!$data->id) {
            include("application/views/admin/common/tabs.php");
        }?>
        <div class="tab_enter_recycling_purchase" id="ui-tabs-2" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;">
        <!-- start -->
        <br />
         <span style="color:red;" id="id_errors"></span>
         <span style="color:red;"><?php echo validation_errors(); ?></span>
         <div class="sixteen columns alpha" style="width:100%;">
			<?php echo form_open('admin/RecyclingInvoice/AddEdit/' . $data->id, "id=\"mainForm\"");?>
			<input type="hidden" name="action" id="action" value="1" />
			  <div style="white-space: nowrap;">
			    <div style="width: 420px;vertical-align: top;display: inline-block;float:left;">
				<div style="margin-bottom:10px;"> 
				    <h6 class="dataentry">General Info</h6>
				    <fieldset class="dataentry">
					    <label for='locationName'>Location Name or ID*</label>
					    <input id="locationName" name="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>" />
					    <?php echo form_hidden('locationId', set_value('locationId', $data->locationId));?>
					    <?php echo form_hidden('locationType', set_value('locationType', $data->locationType));?>
					    <label for='vendor'>Vendor*</label>
					    <input id="vendor" name="vendor" type="text" value="<?php echo set_value('vendor', $data->vendor);?>" />
					    <input type="hidden" name="vendorId" id="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>" />
					    <label for='trailerNumber'>Trailer #</label>
					    <input id="trailerNumber" name="trailerNumber" type="text" style="width:100px;" value="<?php echo set_value('trailerNumber', $data->trailerNumber);?>" />
					    <label for='releaseNumber'>Release #</label>
					    <input id="poNumber" name="poNumber" type="text" style="width:100px;" value="<?php echo set_value('poNumber', $data->poNumber);?>" />
					    <label for='BOLNumber'>Bill Of Lading #</label>
					    <input id="BOLNumber" name="BOLNumber" type="text" style="width:100px;" value="<?php echo set_value('BOLNumber', $data->BOLNumber);?>" />
					    <label for='CBRENumber'>WO #</label>
					    <input id="CBRENumber" name="CBRENumber" type="text" style="width:100px;" value="<?php echo set_value('CBRENumber', $data->CBRENumber);?>" />
					    <label for='invoiceDate'>Date*</label>
					    <input id="invoiceDate" name="invoiceDate" type="text" style="width:100px" autocomplete="off" value="<?php echo set_value('invoiceDate', $data->invoiceDate);?>" />
						<label for="invoiceMonth">Invoice Month</label>
						<input name="invoiceMonth" type="text" style="width:100px" value="<?php echo set_value('invoiceMonth', $data->invoiceMonth);?>" />						
				    </fieldset>
				</div>
				<hr />	
				<div style="margin-bottom:10px;margin-top:10px;">
				    <?php if ($_isAdmin): ?>
				    <fieldset class="dataentry">
					    <h6 class="dataentry">Astor Only</h6>
					    <label for="status">Complete?</label>
					    <?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status))?>
					    <label for="dateSent">Date Sent</label>
					    <input name="dateSent" id="dateSent" type="text" style="width:140px"  value="<?php echo set_value('dateSent', $data->dateSent);?>" />
					    <label for="internalNotes">Internal Notes</label>
					    <textarea name="internalNotes" id="internalNotes" style="max-width:340px;width:340px"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
				    </fieldset>
                    <?php
                        if(!empty($data->id)) {
                            widget::run('audit_table', 'recycling_invoice', $data->id);
                        }
                    ?>
                    <?php endif; ?>
				</div>
			    </div>
			    <div style="min-height: 450px;width: 700px;border-left: 1px solid #DDD;padding: 0 10px;margin: 0 5px;vertical-align: top;display: inline-block;float:left;">
				<div>
				    <h6 class="dataentry">Material Charges</h6>
				    <div style="white-space: nowrap;" class="materialCharges">
					<div class="flo-inl">
					    <label for='waste'>Materials</label>
					    <?php echo form_dropdown('invoiceMaterial[]', $data->allMaterials, isset($data->MaterialsLoad[0]->materialId)?$data->MaterialsLoad[0]->materialId:$data->selMaterials[0], "id=invoiceMaterial0 style='width:300px' size='1' class='invoiceMaterial'");?>            
					</div>
					<div class="flo-inl">
					    <label for='quantity' style="width: 100%;">Quantity</label>
					    <input name="quantity[]" id="quantity0" type="text" style="width:100px;" value="<?php echo isset($data->MaterialsLoad[0]->quantity)?$data->MaterialsLoad[0]->quantity:'';?>" />
					</div>
					<div class="flo-inl">
					    <label for='invoicePriceUnit' style="width: 100%;">Invoice Price/Unit</label>
					    <input name="invoicePriceUnit[]" id="invoicePriceUnit0" type="text" style="width:100px;" value="<?php echo isset($data->MaterialsLoad[0]->pricePerUnit)?$data->MaterialsLoad[0]->pricePerUnit:'';?>" />
					</div>
					<div class="flo-inl">
					    <label for='poPriceUnit' style="width: 100%;">PO Price/Unit</label>
					    <input name="poPriceUnit[]" id="poPriceUnit0" type="text" style="width:100px;" value="<?php echo isset($data->MaterialsLoad[0]->pricePOUnit)?$data->MaterialsLoad[0]->pricePOUnit:'';?>" />
					    <input type="hidden" name="unitId[]" id="unitId0" value="<?php echo isset($data->MaterialsLoad[0]->unitId)?$data->MaterialsLoad[0]->unitId:$data->unitId[0];?>" />
					</div>
				    </div>
				    <?php 
					for($i=1; $i<8; $i++) {
					    echo '<div style="white-space: nowrap;" class="materialCharges">
						    <div class="flo-inl">'.
							form_dropdown('invoiceMaterial[]', $data->allMaterials, isset($data->MaterialsLoad[$i]->materialId)?$data->MaterialsLoad[$i]->materialId:$data->selMaterials[$i], "id=invoiceMaterial".$i." style='width:300px' size='1' class='invoiceMaterial'").            
						    '</div>
						    <div class="flo-inl">
							<input name="quantity[]" id="quantity'.$i.'" type="text" style="width:100px;"  value="'.(isset($data->MaterialsLoad[$i]->quantity)?$data->MaterialsLoad[$i]->quantity:'').'" />
						    </div>
						    <div class="flo-inl">
							<input name="invoicePriceUnit[]" id="invoicePriceUnit'.$i.'" type="text" style="width:100px;" value="'.(isset($data->MaterialsLoad[$i]->pricePerUnit)?$data->MaterialsLoad[$i]->pricePerUnit:'').'"  />
						    </div>
						    <div class="flo-inl">
							<input name="poPriceUnit[]" id="poPriceUnit'.$i.'" type="text" style="width:100px;"  value="'.(isset($data->MaterialsLoad[$i]->pricePOUnit)?$data->MaterialsLoad[$i]->pricePOUnit:'').'" />
							<input type="hidden" name="unitId[]" id="unitId'.$i.'" value="'.(isset($data->MaterialsLoad[$i]->unitId)?$data->MaterialsLoad[$i]->unitId:$data->unitId[$i]).'" />
						    </div>
						</div>';					
					}		    
				    ?>
				    <div style="white-space: nowrap;">
					<div class="flo-inl" style="width: 300px; text-align: right;">Total&nbsp;</div>
					<div class="flo-inl" style="width: 100px;">&nbsp;</div>
					<div class="flo-inl" style="width: 100px; padding-left: 8px;" id="invoicePriceUnitTotal">0</div>
					<div class="flo-inl" style="width: 100px; padding-left: 8px;" id="poPriceUnitTotal">0</div>
				    </div>
				</div>
				<div style="float:left;">
				    <h6 class="dataentry">Fee Charges</h6>
					<div style="clear:both;"></div>
				    <div style="white-space: nowrap;" class="feeCharges">
					<div class="flo-inl">
					    <label for='feeTpye'>Fee Type</label>
					    <?php echo form_dropdown('feeType[]', $data->feeOptions, isset($data->fees[0]->feeType)?$data->fees[0]->feeType:$data->feeOption[0], " style='width:150px'");?>            
					</div>
					<div class="flo-inl">
					    <label for='fee'>Fee</label>
					    <input name="fee[]" id="fee" type="text" style="width:100px;" value="<?php echo isset($data->fees[0]->feeAmount)?$data->fees[0]->feeAmount:"";?>" />
					</div>
					<div class="flo-inl">
					    <label for='waived'>Waived/Saved</label>
					    <input name="wived[0]" id="waived" type="checkbox" <?php echo (isset($data->fees[0]->waived) && $data->fees[0]->waived==1)?'checked="checked"':'';?> />
					</div>
				    </div>
					<div style="clear:both;"></div>
				    <div style="white-space: nowrap;" class="feeCharges">
					<div class="flo-inl">
					    <?php echo form_dropdown('feeType[]', $data->feeOptions, isset($data->fees[1]->feeType)?$data->fees[1]->feeType:$data->feeOption[1], " style='width:150px'");?>            
					</div>
					<div class="flo-inl">
					    <input name="fee[]" id="fee" type="text" style="width:100px;" value="<?php echo isset($data->fees[1]->feeAmount)?$data->fees[1]->feeAmount:"";?>" />
					</div>
					<div class="flo-inl">
					    <input name="wived[1]" id="waived" type="checkbox" <?php echo (isset($data->fees[1]->waived) && $data->fees[1]->waived==1)?'checked="checked"':'';?>/>
					</div>
				    </div>
					<div style="clear:both;"></div>
				    <div style="white-space: nowrap;" class="feeCharges">
					<div class="flo-inl">
					    <?php echo form_dropdown('feeType[]', $data->feeOptions, isset($data->fees[2]->feeType)?$data->fees[2]->feeType:$data->feeOption[2], " style='width:150px'");?>            
					</div>
					<div class="flo-inl">
					    <input name="fee[]" id="fee" type="text" style="width:100px;" value="<?php echo isset($data->fees[2]->feeAmount)?$data->fees[2]->feeAmount:"";?>" />
					</div>
					<div class="flo-inl">
					    <input name="wived[2]" id="waived" type="checkbox" <?php echo (isset($data->fees[2]->waived) && $data->fees[2]->waived==1)?'checked="checked"':'';?>/>
					</div>
				    </div>
				</div>
			    </div>
			  </div>
			  <hr />
	     
			  <div style="text-align: center;">
			    <button style="background:#13602E; color:#fff" onclick="document.getElementById('mainForm').submit(); return false;">Save</button>
				<button style="background:#13602E; color:#fff" onclick="document.getElementById('action').value = 2; document.getElementById('mainForm').submit(); return false;">Save & New</button>
				<button style="background:#777; color:#fff" onclick="window.location = '<?php echo base_url();?>admin/RecyclingInvoice/history'; return false;" >Cancel</button>
			  </div>
			<?php echo form_close();?>

          </div>
      </div>
      </div>
	</div>
    </div>  
</div>
<div id="releasenumbernotunique_dialog">
    <p>Release number is duplicated.</p>
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

	    $('.invoiceMaterial').change(function() {
		    var materialId = this.id;
		    $.ajax({
			url: "<?php echo base_url(); ?>admin/RecyclingInvoice/loadMaterialInfo",
			type : "POST",
			dataType: "json",
			data: { 
			    materialId: $(this).val(),
			    distributionCenterId: $('input[name="locationId"]').val(),
			    invoiceDate: $("#invoiceDate").val()
			},
			success: function (result) {
			    var id = materialId.replace(/[^0-9]/gim, '');			    
			    $('#quantity' + id).val(1);
			    
			    if(result!=null){
                    $("#invoicePriceUnit" + materialId[materialId.length-1]).val(result.invoiceRate);
                    $("#poPriceUnit" + materialId[materialId.length-1]).val(result.poRate);
                    $("#unitId" + materialId[materialId.length-1]).val(result.unitId);
			    }
			    
			    CalcTotalMaterialCharges();
			}
		    });
	    });


	    $('#invoiceDate').change(function() {
		for(var i=0;i<8;i++) {
		    var materialId = "invoiceMaterial" + i;
		    if($("#" + materialId).val()!=0) {
                $.ajax({
                    url: "<?php echo base_url(); ?>admin/RecyclingInvoice/loadMaterialInfo",
                    async: false,
                    type : "POST",
                    dataType: "json",
                    data: {
                    materialId: $("#" + materialId).val(),
                    invoiceDate: $("#invoiceDate").val()
                    },
                    success: function (result) {
                    if(result!=null){
                        $("#invoicePriceUnit" + i).val(result.invoiceRate);
                        $("#poPriceUnit" + i).val(result.poRate);
                        $("#unitId" + i).val(result.unitId);

                        CalcTotalMaterialCharges();
                    }
                    }
                });
		    }
		}
	    });
    });
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

        $('#mainForm').change(function(){
            CalcTotalMaterialCharges();
        });

        $('#releasenumbernotunique_dialog').dialog({ modal: true, autoOpen: false, title: 'Alert', buttons: { 'OK': function() { $(this).dialog('close'); } } });

        $('#poNumber').blur(function() {
            $.ajax({
                url: "<?php echo base_url(); ?>admin/RecyclingInvoice/IsUniqueReleaseNumber",
                async: false,
                type : "POST",
                data: {
                    poNumber: $('#poNumber').val(),
                    invoiceId: <?php echo (!empty($data->id) ? $data->id : 0); ?>
                },
                success: function (result) {
                    if(result!=null && result=='NO') {
                        $('#releasenumbernotunique_dialog').dialog('open');
                    }
                }
            });
        });

        CalcTotalMaterialCharges();
    });

    function noSaveWarning() {
        document.getElementById("popUpMessage").innerHTML = "Please, first save the invoice!";
        $("#dialog-message").dialog("open");
    }
    
    function CalcTotalMaterialCharges() {
        var invoicePriceUnitTotal = 0.0;
        var poPriceUnitTotal = 0.0;
        var fees = 0.0;
        $('div.materialCharges').each(function(){
            var quantity = toFloat($(this).find('input[name^="quantity"]').val());
            var invoicePriceUnit = toFloat($(this).find('input[name^="invoicePriceUnit"]').val());
            var poPriceUnit = toFloat($(this).find('input[name^="poPriceUnit"]').val());

            invoicePriceUnitTotal += quantity*invoicePriceUnit;
            poPriceUnitTotal += quantity*poPriceUnit;
        });

        $('div.feeCharges').each(function(){
            var fee = toFloat($(this).find('input[name^="fee"]').val());
            fees += fee;
        });

        $('#invoicePriceUnitTotal').html((invoicePriceUnitTotal - fees).toFixed(2));
        $('#poPriceUnitTotal').html((poPriceUnitTotal - fees).toFixed(2));
    }
    
    function toFloat(v) {
        var tmp = parseFloat(v);
        if (isNaN(tmp)) {
            tmp = 0.0;
        }
        return tmp;
    }
</script>


<?php include("application/views/admin/common/footer.php");?>

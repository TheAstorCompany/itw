<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

<div class="content">
    <div class="row remove-bottom" style="margin-bottom:0;">
		<div class="sixteen columns">
			<h1><?php echo ($data->invoiceId > 0 ? 'DC Invoice #'.$data->invoiceId : 'Enter DC Invoice');?></h1>
		</div>
        <?php if($data->invoiceId > 0) { ?>
        <div class="row">
            <div class="sixteen columns">
                <a href="javascript: window.history.back();" class="button">&lt;- Go back</a>
                <?php
                if(!$data->is_readonly) {
                ?>
                <a href="<?php echo base_url();?>admin/DCInvoice/Delete/<?php echo $data->id;?>" class="button">Delete</a>
                <?php
                }
                ?>
            </div>
        </div>
        <?php }?>
    </div>
    <div class="row" style="margin-bottom:0;">
		<div class="sixteen columns" style="width:100%;">
			<div id="tabs" style="border:0px;">
				<?php 
					if($data->invoiceId==0) {
						include("application/views/admin/common/tabs.php"); 
					}
				?>
				<div class="tab_enter_recycling_purchase" id="ui-tabs-2" style="border-top: 2px solid #7ABF53; padding-left: 0px; padding-right:0px;">
					<br />
					<span style="color:red;" id="id_errors"></span>
					<span style="color:red;"><?php echo validation_errors(); ?></span>
					<div class="sixteen columns alpha" style="width:100%;">
						<?php echo form_open('', 'id="mainForm"');?>
						<div style="white-space: nowrap;">
							<div style="width: 490px; vertical-align: top; display: inline-block; float:left;">
								<div style="margin-bottom:10px;"> 
									<h6 class="dataentry">General Info</h6>
									<fieldset class="dataentry">
										<label for='locationName'>Site Name*</label>
										<select name="distributionCenterId" id="distributionCenterId" onchange="populateMaterialCharges();">
											<option value="">- Please select -</option>
											<?php 
												foreach($data->distributionCenterIdOptions as $dc) {
													echo '<option value="'.$dc['id'].'" '.((isset($data->distributionCenterId) && $data->distributionCenterId==$dc['id']) ? 'selected="selected"' : '').'>'.$dc['name'].'</option>';
												}
											?>
										</select>
										<input type="hidden" id="save_type" name="save_type" value="1" />
										
										<label for='vendor'>Vendor*</label>
										<input id="vendorName" name="vendorName" type="text" value="<?php echo set_value('vendorName', $data->vendorName);?>" />
										<input type="hidden" name="vendorId" id="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>" />

										<label for='invoiceDate'>Date*</label>
										<input id="invoiceDate" name="invoiceDate" type="text" style="width:100px" autocomplete="off" value="<?php echo set_value('invoiceDate', $data->invoiceDate);?>" />
										
										<label for='haulerInvNumber'>Hauler Inv#*</label>
										<input id="haulerInvNumber" name="haulerInvNumber" type="text" style="width:100px;" value="<?php echo set_value('haulerInvNumber', $data->haulerInvNumber);?>" />
										
										<label for='remitTo'>Remit To</label>
										<input id="remitTo" name="remitTo" type="text" style="width:100px;" value="<?php echo set_value('remitTo', $data->remitTo);?>" />
										
										<label for='monthlyServicePeriod'>Monthly Service Period*</label>
	
										<select style="width: 60px; float: left;" name="monthlyServicePeriodM" style="float: right;">
											<option value=""></option>
											<?php
												for($m=1; $m<=12; $m++) {
													$m_str = strftime('%b', mktime(0, 0, 0, $m, 1, 2012));
													echo '<option value="'.$m_str.'" '.(set_value('monthlyServicePeriodM', $data->monthlyServicePeriodM)==$m_str ? 'selected="selected"' : '').'>'.$m_str.'</option>'."\n";
												}
											?>						
										</select>
										<span style="float: left; padding: 1px;">/</span>
										<select style="width: 60px; float: left;" name="monthlyServicePeriodY">
											<option value=""></option>
											<?php
												for($y=2012; $y<=(date('Y')+1); $y++) {
													echo '<option value="'.$y.'"'.(set_value('monthlyServicePeriodY', $data->monthlyServicePeriodY)==$y ? 'selected="selected"' : '').'>'.$y.'</option>'."\n";
												}
											?>						
										</select>										
										
									</fieldset>
								</div>
								<hr />	
								<div style="margin-bottom: 10px; margin-top: 10px;">
									<input type="hidden" name="action" id="dataFormAction" value="" />
									<fieldset class="dataentry">
										<h6 class="dataentry">Astor Only</h6>
										<label for="status">Complete?</label>
										<?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status))?>
										<label for="dateSent">Date Sent</label>
										<input name="dateSent" id="dateSent" type="text" style="width:140px" value="<?php echo set_value('dateSent', $data->dateSent);?>" />
										<label for="internalNotes">Internal Notes</label>
										<textarea name="internalNotes" id="internalNotes" style="max-width:340px;width:340px"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
									</fieldset>
                                    <?php
                                        if(!empty($data->id)) {
                                            widget::run('audit_table', 'dc_invoice', $data->id);
                                        }
                                    ?>
									<br />
									<hr />
									<br />
                                    <?php
                                        if(!$data->is_readonly) {
                                    ?>
									<div style="text-align: center;">
										<button style="background:#13602E; color:#fff" onclick="$('#save_type').val(1); submitDCInvoice(); return false;">Save</button>
										<button style="background:#13602E; color:#fff" onclick="$('#save_type').val(2); submitDCInvoice(); return false;">Save and New</button>
										<button style="background:#13602E; color:#fff" onclick="$('#save_type').val(3); submitDCInvoice(); return false;">Save Same Vendor</button>
										<button style="background:#777; color:#fff" onclick="window.location = window.location; return false;">Cancel</button>
									</div>
                                    <?php
                                        }
                                    ?>
								</div>
							</div>
							<div style="min-height: 450px; width: 630px; border-left: 1px solid #DDD; padding: 0 10px; margin: 0 5px; vertical-align: top; display: inline-block;float:left;">
								<div>
									<h6 class="dataentry">Material Charges</h6>
									<table class="dc-mc-table">
										<tr>
											<th><label>Fee Type</label></th>
											<th><label>Amount</label></th>
											<th><label>Tons</label></th>
										</tr>
										<?php 
										for($i=0; $i<20; $i++) {
										    echo '
											    <tr>
												    <td>'.form_dropdown('invoiceFees[]', $data->feeTypeOptions, isset($data->invoiceFees[$i]) ? $data->invoiceFees[$i] : 0, 'id=invoiceFees_'.$i.'').'</td>
												    <td><input type="text" name="invoiceFeesAmount[]" id="invoiceFeesAmount_'.$i.'" value="'.(isset($data->invoiceFeesAmount[$i]) ? $data->invoiceFeesAmount[$i] : '').'" /></td>
												    <td><input type="text" name="invoiceFeesTons[]" id="invoiceFeesTons_'.$i.'" value="'.(isset($data->invoiceFeesTons[$i]) ? $data->invoiceFeesTons[$i] : '').'" /></td>
											    </tr>';
										}	
										?>
										<tr>
											<th align="right">Total:</th>
											<th id="thTotalAmount"></th>
											<th id="thTotalTons"></th>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>  
</div>  
	  
<script type="text/javascript">
    var isDuplicateChecked = true;

    function submitDCInvoice(){
        if(isDuplicateChecked)
            document.getElementById('mainForm').submit();
        else
            checkInvoiceIsNotDuplicate(true);
    }

    $(function() {
        $("input[name='invoiceDate'], input[name='dateSent']").datepicker({
            dateFormat: "mm/dd/yy",
            weekHeader: "W"
        });

        $("#vendorName").autocomplete({
            source: "<?php echo base_url(); ?>admin/WasteInvoice/autocompleteVendor",
            minLength: 2,
            select: function(e, ui) {
            $('#vendorId').val(ui.item.id);
                $('#remitTo').val(ui.item.remitTo);
                populateMaterialCharges();
            }
        });

        $('#vendorName').change(function() {
            $('#vendorId').val('');
        });

        calcTotals();

        $('input[name^="invoiceFeesAmount"],input[name^="invoiceFeesTons"]').keyup(function(){
            calcTotals();
        });
        $('input[name^="invoiceFeesAmount"],input[name^="invoiceFeesTons"]').change(function(){
            calcTotals();
        });
    });

    function calcTotals() {
        var totalAmount = 0.0;
        var totalTons = 0.0;
        //debugger;
        $('input[name^="invoiceFeesAmount"]').each(function() {
            var v = $(this).val();
            var tmp = parseFloat(v);
            if (isNaN(tmp)) {
                tmp = 0.0;
            }
            totalAmount += tmp;
        });
        $('input[name^="invoiceFeesTons"]').each(function() {
            var v = $(this).val();
            var tmp = parseFloat(v);
            if (isNaN(tmp)) {
                tmp = 0.0;
            }
            totalTons += tmp;
        });

        $('#thTotalAmount').html(totalAmount.toFixed(2));
        $('#thTotalTons').html(totalTons.toFixed(2));
    }
    $("#haulerInvNumber").change(function(){checkInvoiceIsNotDuplicate(false);});

    function checkInvoiceIsNotDuplicate(submitAfter) {
        isDuplicateChecked = false;
        var PostData = {
            "haulerInvNumber": $("#haulerInvNumber").val(),
            "distributionCenterId" : $("select#distributionCenterId option:selected").val(),
            "vendorId": $("input#vendorId").val()
        };
        if(parseInt(PostData.distributionCenterId) > 0 && parseInt(PostData.vendorId) > 0 && PostData.haulerInvNumber.length>0){
            $.post("/admin/DCInvoice/checkInvoiceIsNotDuplicate", PostData,function(data){
                isDuplicateChecked = true;
                if(data!="0") {
                    $("div#popupmessage_dialog").remove();
                    $("body").append($("<div></div>").attr("id","popupmessage_dialog").html("There is already an invoice entered for this vendor, please verify this is not a duplicate"));
                    $('#popupmessage_dialog').dialog(
                        {
                            modal: true,
                            autoOpen: true,
                            title: 'Message',
                            buttons: {
                                'OK': function () {
                                    $(this).dialog('close');
                                    if(submitAfter===true)
                                        submitDCInvoice();
                                }
                            }
                        }
                    );


                } else if(submitAfter===true){
                    submitDCInvoice();
                }
            });
        }
    }
    
    function populateMaterialCharges() {
        if($('#distributionCenterId').val()!='' && $('#vendorId').val()!='') {
            $.post("<?php echo base_url(); ?>admin/DCInvoice/PopulateMaterialCharges", {locationId: $('#distributionCenterId').val(), vendorId: $('#vendorId').val()},
                function(data) {
                    var n = 0;

                    if(data.waste.cost!=0 || data.waste.tonnage!=0) {
                        $('#invoiceFees_'+n).val(1);
                        $('#invoiceFeesAmount_'+n).val(data.waste.cost);
                        $('#invoiceFeesTons_'+n).val(data.waste.tonnage);
                        n++;
                    }

                    if(data.recycling.cost!=0 || data.recycling.tonnage!=0) {
                        $('#invoiceFees_'+n).val(4);
                        $('#invoiceFeesAmount_'+n).val(data.recycling.cost);
                        $('#invoiceFeesTons_'+n).val(data.recycling.tonnage);
                        n++;
                    }

                    for(var i in data.fees){
                        if(!data.fees.hasOwnProperty(i))
                            continue;

                        var existingFee = $("#invoiceFees_"+n+" option").filter(function(){return this.value===data.fees[i].feeType;});
                        $("#invoiceFees_"+n+" option").removeAttr("selected");
                        existingFee.attr("selected","selected");
                        $("#invoiceFeesAmount_"+n).val(data.fees[i].feeAmount);
                        n++;
                    }
                    calcTotals();
                    checkInvoiceIsNotDuplicate();
                }
            );
        }
    }
</script>


<?php include("application/views/admin/common/footer.php");?>

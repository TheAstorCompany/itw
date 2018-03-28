<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

<div class="content">
	<div class="row remove-bottom">
		<div class="sixteen columns">
			<h1><?php if ($data->id):?>Edit Hauler Invoice #<?php echo $data->id; ?><?php else:?>Enter Hauler Invoice<?php endif;?></h1>
		</div>
    </div>
	<div class="row">
		<div class="sixteen columns" style="width:100%;">
			<?php if ($data->id) :?><a href="<?php echo base_url();?>admin/WasteInvoice/history" class="button">&lt;- Go back</a>
			<a href="<?php echo base_url();?>admin/WasteInvoice/Delete/<?php echo $data->id;?>" class="button">Delete</a><?php endif;?>
			<div id="tabs" style="border:0px;">
			<?php if (!$data->id) {
				include("application/views/admin/common/tabs.php"); 
			}?>
				<div id="ui-tabs-2" class="tab_enter_hauler_invoice" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px;">
					<span style="color:red;" id="id_errors"></span>
					<span style="color:red;"><?php echo validation_errors(); ?></span>
					<?php echo form_open('admin/WasteInvoice/AddEdit/' . $data->id, array('id'=>'EnterWasteInvoice'));?>
					<?php echo form_hidden('submit_form', 1);?>
					<input type="hidden" id="save_type" name="save_type" value="1" />
                    <input type="hidden" name="fromOCR" value="<?php echo set_value('fromOCR', (isset($data->fromOCR) ? 'true' : 'false')) ?>" id="fromOCR" />
					<input type="hidden" name="status" value="<?php echo set_value('status', $data->status) ?>" id="id_status" />
					<input type="hidden" name="dateSent" value="<?php echo set_value('dateSent', $data->dateSent) ?>" id="id_dateSent" />
					<input type="hidden" name="internalNotes" value="<?php echo set_value('internalNotes', $data->internalNotes) ?>" id="id_internalNotes" />
					<h6 class="dataentry">General Info</h6>
					<fieldset class="dataentry">
						<label for='vendorName'>Vendor*</label>
						<input name="vendorName" id="vendorName" type="text" value="<?php echo set_value('vendorName', $data->vendorName);?>" />
						<input type="hidden" id="vendorId" name="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>" />

						<label for='invoiceDate'>Invoice Date*</label>
						<input name="invoiceDate" id="invoiceDate" autocomplete="off" type="text" value="<?php echo set_value('invoiceDate', $data->invoiceDate);?>" style="width:100px" />

						<label for='locationName'>Location Name or ID*</label>
						<input name="locationName" id="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>" />
						<input id="locationId" type="hidden" name="locationId" value="<?php echo set_value('locationId', $data->locationId);?>" />
						<input id="locationType" type="hidden" name="locationType" value="<?php echo set_value('locationType', $data->locationType);?>" />

						<label for='invoiceNumber'>Invoice #*</label>
						<input name="invoiceNumber" type="text" value="<?php echo set_value('invoiceNumber', $data->invoiceNumber);?>" style="width:100px" />

						<label for='invoiceMonth'>Invoice Month/Year*</label>
						<div>
							<select style="width: 60px; float: left;" name="invoiceMonth" style="float: right;">
								<option value=""></option>
								<?php
									for($m=1; $m<=12; $m++) {
										$m_str = strftime('%b', mktime(0, 0, 0, $m, 1, 2012));
										echo '<option value="'.$m_str.'" '.(set_value('invoiceMonth', $data->invoiceMonth)==$m_str ? 'selected="selected"' : '').'>'.$m_str.'</option>'."\n";
									}
								?>						
							</select>
							<span style="float: left; padding: 1px;">/</span>
							<select style="width: 60px; float: left;" name="invoiceYear">
								<option value=""></option>
								<?php
									for($y=2012; $y<=(date('Y')+1); $y++) {
										echo '<option value="'.$y.'"'.(set_value('invoiceYear', $data->invoiceYear)==$y ? 'selected="selected"' : '').'>'.$y.'</option>'."\n";
									}
								?>						
							</select>
						</div>
					</fieldset>
					<h6 class="dataentry">Description</h6>
					<fieldset class="dataentry">
						<div class="dataentry" id="services">
							<?php $inc = true; include "application/views/admin/waste_invoice/waste_invoice_services.php";?>
						</div>
					</fieldset>
					<?php echo form_close();?>

					<fieldset class="dataentry column2">
						<form id="service_form" action="<?php echo base_url();?>admin/WasteInvoice/services?type=service&metod=add" method="post">
							<h6 class="dataentry">Service</h6>
							<label for='location18'><em>Scheduled Services</em></label>
							<div class="minibutton">
								  <input onclick="openScheduledServices();" name="service1" type="button" value="Add Scheduled Services from Location &amp; Vendor" style="width:300px" />
							</div>
							<label for='serviceDate'>Date</label>
							<input name="serviceDate" autocomplete="off" type="text" style="width:100px" />
							<span>
								<label for='serviceTypeId'>Service*</label>
								<?php echo form_dropdown('serviceTypeId', $data->serviceTypeOptions, null, "");?>
							</span>
							&nbsp; <strong>For*</strong>
							<input name="category" type="radio" value="0" />
							<strong>Waste</strong>
							<input name="category" type="radio" value="1" />
							<strong>Recycling</strong><br>

							<label for="containerId">Container*</label>
							<?php echo form_dropdown('containerId', $data->containerOptions);?>

							<label for='waste'>Material*</label>
							<?php echo form_dropdown('materialId', $data->materialOptions, null, "id='waste'");?>

							<label for='quantity'>Quantity</label>
							<input name="quantity" type="text" style="width:100px;" />
							<label for="unitId">Unit</label>
							<?php echo form_dropdown('unitId', $data->unitOptions,null, '');?>

							<label for='rate'>Rate* ($)</label>
							<input name="rate" type="text" style="width:100px;" value="" />
							<button onclick="$('#service_form').submit(); return false;">Add</button>
						</form>
					</fieldset>

					<fieldset class="dataentry column3" id="adminPanel">
						<h6 class="dataentry">Fee</h6>
						<form id="fee_form" action="<?php echo base_url();?>admin/WasteInvoice/services?type=fee" method="post">
                            <label>Pay Fees</label><input type="text" id="payFeesYesNo" value="<?php echo $data->payFeesYesNo; ?>" readonly="readonly" style="width: 30px; border: 0px solid #ffffff;" />
							<label for='feeType'>Fee Type</label>
							<?php echo form_dropdown('feeType', $data->feeOptions, null, 'style="width:150px"')?>
							<label for='feeAmount'>Fee</label>
							<input name="feeAmount" type="text" style="width:100px;" />
							<label for='waived'>Waived/Saved</label>
							<input name="waived" type="checkbox" value="1" ><br/>
							<button onclick="$('#fee_form').submit(); return false;">Add</button>
						</form>
				<?php if ($_isAdmin): ?>
						<h6 class="dataentry">Astor Only</h6>
						<label for="type">Complete?</label>
						<?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status))?>
						<label for="dateSent">Date Sent</label>
						<input name="dateSent" autocomplete="off" value="<?php echo set_value('dateSent', $data->dateSent);?>" type="text" style="width:140px" />
						<label for="internalNotes">Internal Notes</label>
						<textarea name="internalNotes" style="width:340px"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
                        <?php
                            if(!empty($data->id)) {
                                widget::run('audit_table', 'waste_invoice', $data->id);
                            }
                        ?>
				<?php endif; ?>

					</fieldset>
					<hr />
					<button onclick="$('#save_type').val(1); submitWasteInvoice();" style="margin-left:250px; background:#13602E; color:#fff;">Save</button>
					<button onclick="$('#save_type').val(2); submitWasteInvoice();" style="margin-left:20px; background:#13602E; color:#fff;">Save and New</button>
					<button onclick="$('#save_type').val(3); submitWasteInvoice();" style="margin-left:20px; background:#13602E; color:#fff;">Save - Same Vendor</button>
					<button style="margin-left:20px; background:#777; color:#fff;" onclick="window.location='<?php echo base_url();?>admin/WasteInvoice/Add'">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="franchise_dialog">
	<p>Franchise Location</p>
</div>
<script type="text/javascript">
	var servicesDlg;
    var isDuplicateChecked=true;

    function submitWasteInvoice(){
        if(isDuplicateChecked)
            document.getElementById('EnterWasteInvoice').submit();
        else
            checkInvoiceIsNotDuplicate(true);
    }
	
	$(function() {
		$('#franchise_dialog').dialog({ modal: true, autoOpen: false, title: 'Message', buttons: { 'OK': function() { $(this).dialog('close'); } } });

        <?php if(isset($data->fromOCR) && isset($data->locationFranchise) && $data->locationFranchise && !$data->storeInactive) { ?>
            $('#franchise_dialog').dialog('open');
        <?php } ?>
		
		$('#service_form, #fee_form').submit(function() {
            var $form = $(this);
            return addServiceOrFee($form);
		});

        $('#EnterWasteInvoice').submit(function() {
            var $form = $(this);

            $('input[name^="existing_services\["]', $form).remove();
            $('input[name^="existing_fees\["]', $form).remove();

            $('input[name^="existing_services\["]').each(function(){
                $form.append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />');
            });

            $('input[name^="existing_fees\["]').each(function(){
                $form.append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />');
            });

            return true;
        });

        $("input[name='invoiceNumber']").change(function(){checkInvoiceIsNotDuplicate();});

		$('#scheduled_services form').submit(function() {
			$.post(
				$(this).attr('action'),
				$(this).serialize(),
				function (result) {
					$('#services').html(result.html);

					if (!result.error) {
						if (result.form) {
							document.getElementById(result.form).reset();
						}
					}
				}
			);

			return false;
		});
		
		$("input[name='serviceDate'], input[name='dateSent'], input[name='serviceDate']").datepicker({
			 dateFormat: "mm/dd/yy",
			 weekHeader: "W"
		});

        $("input[name='invoiceDate']").datepicker({
            dateFormat: "mm/dd/yy",
            weekHeader: "W",
            onSelect: function(selectedDate) {
                autopopulateScheduledServices();
            }
        });
		
		$("#locationName").autocomplete({
			source: "<?php echo base_url(); ?>admin/SupportRequest/autocompleteLocation",
			minLength: 2,
			select: function(e, ui) {
				$("input[name='locationId']").val(ui.item.id);
				$("input[name='locationType']").val(ui.item.type);
				if(ui.item.franchise) {
					$('#franchise_dialog').dialog('open');	
				}
                if(ui.item.payFees) {
                    $('#payFeesYesNo').val(ui.item.payFees);
                } else {
                    $('#payFeesYesNo').val('');
                }
                checkInvoiceIsNotDuplicate();
				autopopulateScheduledServices();
			}
		});

		$('#locationName').change(function() {
			$("input[name='locationId']").val('');
		});

		$("#vendorName").autocomplete({
			source: "<?php echo base_url(); ?>admin/WasteInvoice/autocompleteVendor",
			minLength: 2,
			select: function(e, ui) {
				$('#vendorId').val(ui.item.id);
				checkInvoiceIsNotDuplicate();
				autopopulateScheduledServices();
			}
		});

		$('#vendorName').change(function() {
			$('#vendorId').val('');
		});

		<?php if ($_isAdmin): ?>
		$('#adminPanel input,select,textarea').each(function(i, item) {
			$('#id_'+$(item).attr('name')).val($(item).val());
		});
		
		$('#adminPanel input,select,textarea').change(function() {
			$('#id_'+$(this).attr('name')).val($(this).val());
		});
		<?php endif; ?>
	});


    function addServiceOrFee($form) {
        $('#service_form input[name^="existing_services\["]').remove();
        $('#service_form input[name^="existing_fees\["]').remove();

        $('#fee_form input[name^="existing_services\["]').remove();
        $('#fee_form input[name^="existing_fees\["]').remove();

        $('input[name^="existing_services\["]').each(function(){
            $form.append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />');
        });
        $('input[name^="existing_fees\["]').each(function(){
            $form.append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />');
        });

        $.post(
            $form.attr('action'),
            $form.serialize(),
            function (result) {
                $('#services').html(result.html);

                if (!result.error) {
                    if (result.form) {
                        document.getElementById(result.form).reset();
                    }
                }
            }
        );

        return false;
    }

	function deleteFee(idx) {
        $('li[fee_k="' + idx + '"]').remove();
        calcTotal();
		return false;
	}

	function deleteService(idx) {
		$('li[service_k="' + idx + '"]').remove();
		calcTotal();
		return false;
	}

    function calcTotal() {
        var total = 0.0;
        for (var i = 0; i < $("#services li").length; i++) {
            var li = $($("#services li")[i]);

            if (li.find("input[name^='existing_fees']").length > 0) {
                if (li.find("input[name$='[waived]']").val() != "1") {
                    total += parseFloat(li.find("input[name$='[feeAmount]']").val());
                }
            } else {
                total += parseFloat(li.find("input[name$='[rate]']").val());
            }
        }
        $("#services h6").html("Total $" + total);
    }

    function checkInvoiceIsNotDuplicate(submitAfter) {
        isDuplicateChecked = false;
        var PostData = {
                        "invoiceNumber" : $("input[name='invoiceNumber']").val(),
                        "locationId": $("input#locationId").val(),
                        "vendorId": $("input#vendorId").val()
                       };
        if(parseInt(PostData.locationId) > 0 && parseInt(PostData.vendorId) > 0 && PostData.invoiceNumber.length>0){
            $.post("/admin/WasteInvoice/checkInvoiceIsNotDuplicate", PostData,function(data){
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
                                        submitWasteInvoice();
                                }
                            }
                        }
                    );
                } else if(submitAfter===true) {
                    submitWasteInvoice();
                }
            });
        } else if(submitAfter){
            var errorInfo = parseInt(PostData.vendorId) > 0 ?  "" : "<p>The Vendor field must be autocompleted!</p>";
            errorInfo += parseInt(PostData.locationId) > 0 ? "" : "<p>The Location field must be autocompleted!</p>"  ;
            errorInfo += PostData.invoiceNumber.length == 0 ? "<p>The Invoice number field is required!</p>" : "" ;
            $("span#id_errors").next().empty();
            $("span#id_errors").html(errorInfo);

        }
    }

	function autopopulateScheduledServices() {
		var locationId = $('#locationId').val();
		var locationType = $('#locationType').val();
		var vendorId = $('#vendorId').val();
        var invoiceDate = $('#invoiceDate').val();

		if (locationId >= 1 && vendorId >= 1 && invoiceDate!='') {
			$.get('<?php echo base_url();?>admin/WasteInvoice/getScheduledServices',
			'locationId=' + locationId + '&vendorId=' + vendorId + '&locationType=' + locationType + '&invoiceDate=' + invoiceDate + '&data_only=true',
			function(data) {
				if(data.length>0) {
                    $('#service_form input[name^="existing_services\["]').remove();
                    $('#service_form input[name^="existing_fees\["]').remove();

                    $('#fee_form input[name^="existing_services\["]').remove();
                    $('#fee_form input[name^="existing_fees\["]').remove();

                    for(var i=0; i<data.length; i++) {
                        var post_data = data[i];

                        post_data.existing_services = [];
                        var iii = [];
                        $('input[name^="existing_services\["]').each(function(){
                            var re = /existing_services\[(\d+)\]\[(.+?)\]/i;
                            var found = $(this).attr('name').match(re);
                            var k = parseInt(found[1]);
                            var name = found[2];

                            var j = -1;
                            for (var key in iii) {
                                var val = iii[key];
                                if(val == k) {
                                    j = key;
                                    break;
                                }
                            }
                            if(j==-1) {
                                iii.push(k);
                                j = (iii.length - 1);
                                post_data.existing_services[j] = {};
                            }

                            post_data.existing_services[j][name] = $(this).val();
                        });

                        post_data.existing_fees = [];
                        iii = [];
                        $('input[name^="existing_fees\["]').each(function(){
                            var re = /existing_fees\[(\d+)\]\[(.+?)\]/i;
                            var found = $(this).attr('name').match(re);
                            var k = parseInt(found[1]);
                            var name = found[2];

                            var j = -1;
                            for (var key in iii) {
                                var val = iii[key];
                                if(val == k) {
                                    j = key;
                                    break;
                                }
                            }
                            if(j==-1) {
                                iii.push(k);
                                j = (iii.length - 1);
                                post_data.existing_fees[j] = {};
                            }

                            post_data.existing_fees[j][name] = $(this).val();
                        });
						post_data.serviceTypeId = post_data.durationId;
						$.ajax({
							type: 'POST',
							async: false,
							url: '<?php echo base_url();?>admin/WasteInvoice/services?type=service',
							data: post_data,
							success: function (result) {
								$('#services').html(result.html);

								if (!result.error) {
									if (result.form) {
										document.getElementById(result.form).reset();
									}

                                    var arrayOfInvoiceDate = invoiceDate.split('/');
                                    var dtInvoicedate = new Date((arrayOfInvoiceDate[2]), parseInt(arrayOfInvoiceDate[0])-1, parseInt(arrayOfInvoiceDate[1]));
                                    var within30days = false;

                                    for(var i=0; i<result.data.services.length; i++) {
                                        var endDate = result.data.services[i].endDate;
                                        var arrayOfEndDate = endDate.split('-');
                                        var dtEndDate = new Date((arrayOfEndDate[0]), parseInt(arrayOfEndDate[1])-1, parseInt(arrayOfEndDate[2]));
                                        if( ((dtEndDate - dtInvoicedate)/60/60/24/1000) <=30 && (new Date()).getTime() - dtEndDate.getTime() < 0) {
                                            within30days = true;
                                        }
                                    }

                                    if(within30days) {
                                        alert('Services ending within 30days');
                                    }
								}
							}
						});
					}
				}

			}, 'json');
		}		
	}

	function openScheduledServices() {
		var locationId = $('#locationId').val();
		var locationType = $('#locationType').val();
		var vendorId = $('#vendorId').val();
        var invoiceDate = $('#invoiceDate').val();

		if (locationId < 1) {
			alert('"Location Name" field is required');
			$('input[name="locationName"]').focus();
			return false;
		}

		if (vendorId < 1) {
			alert('"Vendor" field is required');
			$('input[name="vendorName"]').focus();
			return false;
		}

        if (invoiceDate == '') {
            alert('"Invoice Date" field is required');
            $('input[name="invoiceDate"]').focus();
            return false;
        }

		$.get(
			'<?php echo base_url();?>admin/WasteInvoice/getScheduledServices',
			'locationId=' + locationId + '&vendorId=' + vendorId + '&locationType=' + locationType + '&invoiceDate=' + invoiceDate,
			function(data) {
				$(data).dialog({
					autoOpen: true,
					title: 'Select existing scheduled service(s)',
					minWidth: 600,
					buttons: {
						'Close': function() {
							$(this).remove();
						}
					}
				});

				$('#scheduled_services form').submit(function() {
                    var $form = $(this);

                    addServiceOrFee($form);

                    $form.parents('li').remove();

					if (!$("#scheduled_services li").size()) {
						$('#scheduled_services').html('<h1>Sorry, no scheduled services left.</h1>');
					}

					return false;
				});
			}
		);	

		return;
	}
</script>
<?php
    if(isset($data->fromOCR) && !empty($data->vendorId)) {
?>
        <script type="text/javascript">
            $(function() {
                autopopulateScheduledServices();
            });
        </script>
<?php
    }
?>

<?php
    if(isset($data->fromOCR) && empty($data->vendorId) && !$data->storeInactive) {
?>
        <div id="fromOCR_dialog">
            <p>Cannot find vendor with name: <?php echo $_GET['vendor_name']; ?> and address: <?php echo $_GET['vendor_address']; ?></p>
        </div>
        <script type="text/javascript">
            $(function() {
                $('#fromOCR_dialog').dialog({
                    modal: true,
                    autoOpen: true,
                    title: 'Vendor not found',
                    buttons: {
                        'Close': function() {
                            $(this).dialog('close');
                        }
                    }
                });
            });
        </script>
<?php
    }
?>

<?php
    if(isset($data->fromOCR) && $data->storeInactive) {
?>
        <div id="fromOCR_StoreInactive_dialog">
            <p>Store Inactive</p>
        </div>
        <script type="text/javascript">
            $(function() {
                $('#fromOCR_StoreInactive_dialog').dialog({
                    modal: true,
                    autoOpen: true,
                    title: 'Store Inactive',
                    buttons: {
                        'Close': function() {
                            $(this).dialog('close');
                        }
                    }
                });
            });
        </script>
<?php
    }
?>

<?php include("application/views/admin/common/footer.php");?>
<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>        
        <script type="text/javascript" src="<?php echo base_url();?>js/json2form.js"></script>
        <script>
        $(document).ready(function() {
        	$('#tabs').tabs({selected: 1});
        	$("input[name='renewalDate']").datepicker({
				dateFormat: "mm/dd/yy",
			 	weekHeader: "W" 
			});
        	$('#vendors_tabs').tabs();
			$("#ServicesLocation").autocomplete({
				source: "<?php echo base_url(); ?>admin/Vendors/autocompleteLocation",
				minLength: 2,
				select: function(e, ui) {
					if (ui.item.id) {
						$.post("<?php echo base_url(); ?>admin/Vendors/autocompleteLocationFillForm", {vendorServiceId: ui.item.id },
   							function(data) {     								
     							$('#form_tab_3').json2form(data);     								
     						}
   						);
					}
				}
			});
			$('#vendorslist').dataTable( {
					"sPaginationType": "full_numbers"
				} );
						$('#dclist').dataTable( {
					"sPaginationType": "full_numbers"
				} );
						$('#storeslist').dataTable( {
					"sPaginationType": "full_numbers"
				} );
						$('#userslist').dataTable( {
					"sPaginationType": "full_numbers"
			} );	
			$('#delete').click(function() {
				// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
				$( "#dialog:ui-dialog" ).dialog( "destroy" );	
				$( "#dialog-confirm" ).dialog({
					resizable: false,
					height:160,
					modal: true,
					buttons: {
						"Delete": function() {
							$(this).dialog( "close" );
							document.location = '<?php echo base_url();?>admin/Vendors/Delete/<?php echo $vendorId;?>';
						},
						Cancel: function() {
							$( this ).dialog( "close" );
						}
					}
				});
			});
		});
		</script>
		<div class="row">
		    <?php if ($vendorId) { ?>
        	<div  id="dialog-confirm" title="Confirmation to delete" style="display:none">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
				Do you want to delete vendor '<?php echo $data->name; ?>' ?</p>
			</div>
			<?php } ?>
            <div class="sixteen columns"><a href="<?php echo base_url();?>admin/ManageCompany/Vendors" class="button">&lt;- Go back</a>
                <?php if ($vendorId && !$is_read_only) { ?>
                    <button id="delete">Delete</button>
                <?php } ?>
            </div>
        </div>
        <div class="row"><div class="eight columns">
          	<h1>Vendor</h1>
			<fieldset class="dataentry">
				<span style="color:red;"><?php echo validation_errors(); ?></span>
				<?php echo form_open('admin/Vendors/AddEdit/' . $vendorId, 'id="form_tab_1"'); ?>
				<?php echo form_hidden('addupdate', 1); ?>				
				<table>
					<tr>
						<td style="width: 240px;">
							<label style="float:left;padding-right:10px">Last Updated</label>
							&nbsp;
						</td>
						<td>
							<?php if ($vendorId) { ?>
							<?php echo date("m/d/Y h:ia", strtotime($data->lastUpdated));?><br /><br />
							<?php } ?>						
						</td>
					</tr>
					<tr>
						<td>
							<label for="number">Vendor#*</label>
							<input name="number" type="text" value="<?php echo set_value('number', $data->number);?>">								
						</td>
						<td>
                            <label for="number">Quickbooks Name</label>
                            <input name="quickbooks" type="text" value="<?php echo set_value('quickbooks', $data->quickbooks);?>">
						</td>
					</tr>					
					<tr>
						<td>
                            <label for="name">Vendor Name*</label>
                            <input name="name" type="text" value="<?php echo set_value('name', $data->name);?>">
                        </td>
						<td style=" vertical-align: top;">
                            <label for="cityVendor" style="float: none;">City Vendor</label>
                            <input name="cityVendor" type="checkbox" value="1" <?php echo set_value('cityVendor', $data->cityVendor)=='1' ? 'checked="checked"' : ''; ?> style="float: left; height: 21px;"  />
						</td>
					</tr>
                    <tr>
                        <td>
                            <label for="addressLine1">Address</label>
                            <input name="addressLine1" type="text" value="<?php echo set_value('addressLine1', $data->addressLine1);?>">
                        </td>
                        <td>
                            <label for="remitTo">Remit To</label>
                            <input name="remitTo" type="text" value="<?php echo set_value('remitTo', $data->remitTo);?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="addressLine2">Address #2</label>
                            <input name="addressLine2" type="text" value="<?php echo set_value('addressLine2', $data->addressLine2);?>">
                        </td>
                        <td>
                            <label for="remitToAddress">Remit to Address</label>
                            <input name="remitToAddress" type="text" value="<?php echo set_value('remitToAddress', $data->remitToAddress);?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="city">City</label>
                            <input name="city" type="text" value="<?php echo set_value('city', $data->city);?>">
                        </td>
                        <td>
                            <label for="remitToCity">Remit to City</label>
                            <input name="remitToCity" type="text" value="<?php echo set_value('remitToCity', $data->remitToCity);?>">
                        </td>
                    </tr>
					<tr>
						<td>
                            <label for="phone">State</label>
                            <?php echo form_dropdown('stateId', $statesOptions, set_value('stateId', $data->stateId));?>
						</td>
						<td>
                            <label for="remitToStateId">Remit to State</label>
                            <?php echo form_dropdown('remitToStateId', $statesOptions, set_value('remitToStateId', $data->remitToStateId));?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="email">Zip</label>
							<input type="text" name="zip" value="<?php echo set_value('zip', $data->zip);?>" />							
						</td>
						<td>
                            <label for="remitToZip">Remit to Zip</label>
                            <input name="remitToZip" type="text" value="<?php echo set_value('remitToZip', $data->remitToZip);?>">
						</td>
					</tr>
                    <tr>
                        <td>
                            <label for="email">Phone</label>
                            <input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">
                        </td>
                        <td style=" vertical-align: top;">
                            <label for="astorBilled" style="float: none;">Astor Billed</label>
                            <input name="astorBilled" type="checkbox" value="1" <?php echo set_value('astorBilled', $data->astorBilled)=='1' ? 'checked="checked"' : ''; ?> style="float: left; height: 21px;"  />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="fax">Fax</label>
                            <input name="fax" type="text" value="<?php echo set_value('fax', $data->fax);?>">
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="email">Email</label>
                            <input name="email" type="text" value="<?php echo set_value('email', $data->email);?>">
                        </td>
                        <td>
                            <label for="website">Website</label>
                            <input name="website" type="text" value="<?php echo set_value('website', $data->website);?>">
                        </td>
                    </tr>
				</table>
			
      			<?php if ($_isAdmin) { ?>
      			<hr>
      			<h5 style="color:#7ABF53;">Astor Only</h5>
        		<label for="type">status</label>
        		<?php echo form_dropdown('status', $data->activeOptions, set_value('status', $data->status))?>        
        		<label for="notes">Internal Notes</label>
        		<textarea name="notes"><?php echo set_value('notes', $data->notes);?></textarea>
        		<?php } ?>
                <?php if (!$is_read_only) { ?>
                <button type="submit"><?php if ($vendorId) {?>Update<?php } else { ?>Add<?php } ?></button>
                <?php } ?>
      			<?php echo form_close() ?>
      		</fieldset>
          </div>

        <div class="seven columns">
            <div id="tabs" style="border:0px;">
                <ul>
                    <li><a href="#tabs-1">Contacts</a></li>
                    <li><a href="#tabs-2">Services</a></li>
                    <li><a href="#tabs-3">Service History</a></li>
                </ul>
                <div id="tabs-1" style="border:2px solid #7ABF53;">
                    <div class="dataentry" id="dataentry">
                    <?php include('vendors_contacts_ajax.php'); ?>
                    </div>
                    <?php echo form_open('admin/Vendors/AddContact', 'id="form_tab_2"'); ?>
                    <div style="color:red;" id="error"></div>
                    <input name="vendorId" id="vendorId" type="hidden" value="<?php echo set_value('vendorId', $vendorId); ?>" />
                    <span style="float:left;padding-right:10px">
                        <label for="firstName">First Name*</label><input name="firstName" type="text" value="">
                    </span>
                    <label for="lastName">Last Name*</label><input name="lastName" type="text" value="">
                    <label for="title">Title</label><input name="title" type="text" value="">
                    <label for="email">Email</label><input name="email" type="text" value="">
                    <label for="phone">Phone</label><input name="phone" type="text" value="">
                    <button>Add</button>
                    <?php echo form_close(); ?>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $('#form_tab_2').submit(function() {
                                $.post($('#form_tab_2').attr('action'), $('#form_tab_2').serialize(),
                                    function(data) {
                                        if (!data.error) {
                                            document.getElementById('form_tab_2').reset();
                                            $('#error').html('');
                                        } else {
                                            $('#error').html(data.error);
                                        }
                                        $('#dataentry').html(data.html);
                                    }
                                );
                                return false;
                            });
                        });

                        function deleteContact(contact_id) {
                            $.post('<?php echo base_url();?>admin/Vendors/DeleteContact', { contact_id: contact_id },
                                function(data) {
                                    $('li[contact_id="' + contact_id + '"]').remove();
                                }
                            );

                            return false;
                        }
                    </script>
                </div>
                <div id="tabs-2" style="border:2px solid #7ABF53;">
                    <div class="dataentry">
                        <strong>Current Services</strong>
                        <ol>
                        <?php
                            if (is_array($data->vendorServices)) {
                                foreach($data->vendorServices as $service) {
                                    if(($service->endDate=="0000-00-00") || ($service->endDate>=date('Y-m-d'))){
                        ?>
                                        <li><a href="<?php echo base_url(); echo ($service->locationType == 'DC')?"admin/DistributionCenters":"admin/Stores"?>/AddEdit/<?php echo $service->locationId?>"><?php echo $service->locationName;?></a>, <?php echo $service->title;?>
                        <?php
                                    }
                                }
                            }
                        ?>
                        </ol>
                    </div>
                </div>
	            <div id="tabs-3" style="border:2px solid #7ABF53;">
	                <div class="dataentry">
		                <strong>Current Services</strong>
		                <ol>
		                <?php
                            if (is_array($data->vendorServices)) {
		                        foreach($data->vendorServices as $service) {
			                        if(($service->endDate!="0000-00-00") && ($service->endDate<date('Y-m-d'))){
                        ?>
		                                <li><a href="<?php echo base_url(); echo ($service->locationType == 'DC')?"admin/DistributionCenters":"admin/Stores"?>/AddEdit/<?php echo $service->locationId?>"><?php echo $service->locationName;?></a>, <?php echo $service->title;?>
			            <?php
                                    }
			                    }
		                    }
                        ?>
		                </ol>
                    </div>
                </div>
            </div>
        </div>
<?php include("application/views/admin/common/footer.php");?>
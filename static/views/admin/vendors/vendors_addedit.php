<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>        
        <script type="text/javascript" src="<?php echo base_url();?>js/json2form.js"></script>
        <script>
        $(document).ready(function() {
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
        <div class="content">
        	<?php if ($vendorId) { ?>
        	<div  id="dialog-confirm" title="Confirmation to delete" style="display:none">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
				Do you want to delete vendor '<?php echo $data->name; ?>' ?</p>
			</div>
			<?php } ?>			           	
    		<div class="row">
    			<div class="sixteen columns">
    				<a href="<?php echo base_url()?>admin/ManageCompany/Vendors/" class="button">&lt;- Go back</a>
    				<?php if ($vendorId) { ?>    				
    				<button id="delete">Delete</button>    				
    				<?php } ?>
    			</div>
    		</div>    		
        	<div class="row">
        		<span style="color:red;"><?php echo validation_errors(); ?></span>
        		
        		<div class="eight columns"><?php if ($vendorId) { ?>
          			<h1>Vendor</h1><fieldset class="dataentry"><label for="type">Last Updated</label>
          	   <?php echo date("m/d/Y h:ia", strtotime($data->lastUpdated));?>		<?php } ?><br /><br />  	
                <?php echo form_open('admin/Vendors/AddEdit/' . $vendorId, 'id="form_tab_1"'); ?> <!--this no longer applies since we're moving this form out of a tab-->
    					<?php echo form_hidden('addupdate',1); ?>
          				
          				<label for="name">Vendor Name</label>
          				<input name="name" type="text" value="<?php echo set_value('name', $data->name);?>">
      					
      					<label for="number">Vendor#</label>
      					<input name="number" type="text" value="<?php echo set_value('number', $data->number);?>">
      					
      					<label for="remitTo">Remit To</label>
      					<input name="remitTo" type="text" value="<?php echo set_value('remitTo', $data->remitTo);?>">
      					
      					<label for="addressLine1">Address</label>
      					<input name="addressLine1" type="text" value="<?php echo set_value('addressLine1', $data->addressLine1);?>">
      					<label for="addressLine2">Address #2</label>
      					<input name="addressLine2" type="text" value="<?php echo set_value('addressLine2', $data->addressLine2);?>">
      					
      					<label for="city">City</label>
      					<input name="city" type="text" value="<?php echo set_value('city', $data->city);?>">
      					<label for="state">State</label>
      					<?php echo form_dropdown('stateId', $statesOptions, set_value('stateId', $data->stateId));?>
      					
      					<label for="phone">Phone</label>
      					<input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">
      					<label for="fax">Fax</label>
      					<input name="fax" type="text" value="<?php echo set_value('fax', $data->fax);?>">
      					
      					<label for="website">Website</label>
      					<input name="website" type="text" value="<?php echo set_value('website', $data->website);?>">
      					<label for="email">Email</label>
      					<input name="email" type="text" value="<?php echo set_value('email', $data->email);?>"></fieldset>
                        <?php if ($_isAdmin) { ?>
          				<hr />
          					<h5 style="color:#7ABF53;">Astor Only</h5><fieldset class="dataentry">
         					<label for="status">Status</label>
				        	<?php echo form_dropdown('status', $data->activeOptions, set_value('status', $data->status))?>				        	        
         					<label for="notes">Internal Notes</label>
         					<textarea name="notes" style="width:45%"><?php echo set_value('notes', $data->notes);?></textarea>
         				</fieldset>
         				<?php } ?>
      					<button type="submit"><?php if ($vendorId) {?>Update<?php } else { ?>Add<?php } ?></button>
      					<?php echo form_close() ?>   	
          		</div>
          		       		
          	      
          		<div class="seven columns omega">
          		<div id="vendors_tabs" style="border:0px;">
					<ul>
						<li><a href="#tabs-1">Contacts</a></li>
                		<li><a href="#tabs-2">Services</a></li>
					</ul>          			
       				<div id="tabs-1" style="border:2px solid #7ABF53;">       					
       					<script>       				
       					function vendorsAddContact() {
       						$.post($('#form_tab_2').attr('action'), $('#form_tab_2').serialize(),
   								function(data) {     								
     								if (data.error != "") {
     									$('#vendorContactsErrorMessage').html(data.error);
     								} else {
     									$('#vendorContactsErrorMessage').html('');
     									if (data.result) {
     										$('#form_tab_3 input').val('');     										
     										$('#rightContainer>h6').remove();
     										for (a in data.result) {    
     											var contactInfo = data.result[a].firstName.replace("'", '`') + ' ' + data.result[a].lastName.replace("'", '`') + ', ' + data.result[a].title +  ', ' + data.result[a].email + ', Ph ' + 	data.result[a].phone;
     											var event = "vendorsDeleteContact('" +data.result[a].id +"', '" + contactInfo + "');";
     											$('#rightContainer').append('<h6><a href="JavaScript:void(0)" onclick="' + event + '">Delete</a> ' + contactInfo +'</h6>');     											
     										}
     									}
     								}     								
     							}
   							);
       					}
       					function vendorsDeleteContact(contactId, name) {
       						$( "<div>" ).dialog({
								resizable: false,
								height:160,
								title: 'Confirmation to delete',
								modal: true,
								buttons: {
									"Delete": function() {
										$(this).dialog( "close" );
										$.post('<?php echo base_url()?>admin/Vendors/DeleteContact/<?php echo $vendorId?>',
			       							{contactId : contactId},
			   								function(data) {
			     								$('#vendorContactsErrorMessage').html('');
			     								if (data.result) {
			     									$('#form_tab_2 input').val('');			     									
			     									$('#rightContainer>h6').remove();
			     									for (a in data.result) {			     										
			     										var contactInfo = data.result[a].firstName.replace("'", '`') + ' ' + data.result[a].lastName.replace("'", '`') + ', ' + data.result[a].title +  ', ' + data.result[a].email + ', Ph ' + 	data.result[a].phone;  											
		     											var event = "vendorsDeleteContact('" +data.result[a].id +"', '" + contactInfo + "');";
		     											$('#rightContainer').append('<h6><a href="JavaScript:void(0)" onclick="' + event + '">Delete</a> ' + contactInfo +'</h6>');     											
			     									}
			     								}    								
			     							}
			   							);
									},
									Cancel: function() {
										$( this ).dialog( "close" );
									}
								}
							}).html('<p>Do you want to delete vendor contact " ' + name + '"?</p>');       						
       					}
       					</script>
       					<div id="vendorContactsErrorMessage" style="color:red;"></div>
       					<div class="dataentry" id="rightContainer">
       					<strong>Current Contacts</strong>      
      					<?php if (is_array($data->vendorContacts)) foreach($data->vendorContacts as $vendorContact) {
	      						$contactInfo = $vendorContact->firstName . ' ' . $vendorContact->lastName . ', ' . $vendorContact->title . ', ' . $vendorContact->email . ', Ph ' . 	$vendorContact->phone;  											
      					?>
      						<h6><a href="JavaScript:void(0)" onclick="vendorsDeleteContact(<?php echo $vendorContact->id;?>, '<?php echo addslashes($vendorContact->firstName);?> <?php echo addslashes($vendorContact->lastName);?>');">Delete</a> <?php echo $contactInfo; ?></h6>
      					<?php } ?>
      					</div>      					
      					<?php echo form_open('admin/Vendors/Contacts/' . $vendorId, 'id="form_tab_1"'); ?>
       					<?php echo form_hidden('addupdate',1); ?>
      					<span style="float:left;padding-right:10px">
      					<label for="firstName">First Name</label>
      					<input name="firstName" type="text" value="" autocomplete="off"></span>
      					<label for="lastName">Last Name</label><input name="lastName" type="text" value="" autocomplete="off">
      					<label for="title">Title</label><input name="title" type="text" value="" autocomplete="off">
      					<label for="email">Email</label><input name="email" type="text" autocomplete="off">
      					<label for="phone">Phone</label><input name="phone" type="text" autocomplete="off">      					
      					<?php echo form_close() ?>
      					<br><button onclick="vendorsAddContact()">Add</button>
 					</div>
 					<div id="tabs-2" style="border:2px solid #7ABF53;">
 					<script>
       					function vendorsAddService() {
       						$.post($('#form_tab_3').attr('action'), $('#form_tab_3').serialize(),
   								function(data) {     								
     								if (data.error != "") {
     									$('#vendorServicesErrorMessage').html(data.error);
     								} else {
     									$('#vendorServicesErrorMessage').html('');
     									if (data.result) {
     										$('#form_tab_3 input').val('');
     										$('#form_tab_3 input[type="checkbox"]').attr('checked', false);     										
     										$('#rightContainerServices>h6').remove();
     										for (a in data.result) {     											
     											var event = "vendorsDeleteService('" +data.result[a].id +"', '" + data.result[a].name.replace("'", '`') + "')";
     											$('#rightContainerServices').append('<h6><a href="JavaScript:void(0)" onclick="' + event + '">Delete</a> ' + data.result[a].title +'</h6>');     											
     										}
     									}
     								}     								
     							}
   							);
       					}
       					function vendorsDeleteService(serviceId, name) {
       						$( "<div>" ).dialog({
								resizable: false,
								height:160,
								title: 'Confirmation to delete',
								modal: true,
								buttons: {
									"Delete": function() {
										$(this).dialog( "close" );
										$.post('<?php echo base_url()?>admin/Vendors/DeleteService/<?php echo $vendorId?>',
			       							{serviceId : serviceId},
			   								function(data) {
			     								$('#vendorContactsErrorMessage').html('');
			     								if (data.result) {
			     									$('#form_tab_3 input').val('');			     									
			     									$('#rightContainerServices>h6').remove();			     									
			     									for (a in data.result) {			     										
			     										var event = "vendorsDeleteService('" +data.result[a].id +"', '" + data.result[a].name.replace("'", '`') + "')";
     													$('#rightContainerServices').append('<h6><a href="JavaScript:void(0)" onclick="' + event + '">Delete</a> ' + data.result[a].title + '</h6>');     											
			     									}
			     								}    								
			     							}
			   							);
									},
									Cancel: function() {
										$( this ).dialog( "close" );
									}
								}
							}).html('<p>Do you want to delete vendor service " ' + name + '"?</p>');       						
       					}
       					</script>
       					<div id="vendorServicesErrorMessage" style="color:red;"></div>      
           				<?php echo form_open('admin/Vendors/Services/' . $vendorId, 'id="form_tab_2"'); ?>
       					<?php echo form_hidden('addupdate',1); ?>
           				<div class="dataentry" id="rightContainerServices">
           				<strong>Current Services</strong>      
      					<?php if (is_array($data->vendorServices)) foreach($data->vendorServices as $vendorService) {?>
      						<h6><a href="JavaScript:void(0)" onclick="vendorsDeleteService(<?php echo $vendorService->id; ?>, '<?php echo addslashes($vendorService->name); ?>')">Delete</a> <?php echo $vendorService->title?></h6>
      					<?php } ?>
      					</div>
      					
                        <label for="durationId">Vendor</label>
      					<input name="name" id="ServicesLocation" type="text" value="" autocomplete="off">
      					<label for="durationId">Duration</label>
      					<?php echo form_dropdown("durationId", $vendorServiceDurations) ?>      					
      					<label for="purposeId">Purpose</label>
      					<?php echo form_dropdown("purposeId", $vendorServicePurposes, 2) ?>
         				<label for="quantity">QTY</label>
         				<input name="quantity" type="text" value="" autocomplete="off">
         				<label for="containerId">Container</label>
         				<?php echo form_dropdown("containerId", $containers) ?>         				
         				<label for="schedule">Schedule</label>
         				<?php echo form_dropdown("schedule", $schedule) ?>				        
         				<input name="days[]" type="checkbox" value="2">
         				Sun
         				<input name="days[]" type="checkbox" value="4">
         				Mon 
         				<input name="days[]" type="checkbox" value="8">
         				Tue
         				<input name="days[]" type="checkbox" value="16">
         				Wed
         				<input name="days[]" type="checkbox" value="32">
         				Thu
         				<input name="days[]" type="checkbox" value="64">
         				Fri
         				<input name="days[]" type="checkbox" value="128">
         				Sat<br>
         				<label for="rate">Rate</label>
         				<input name="rate" type="text" value="" autocomplete="off">
         				<label for="date">Renewal Date</label>
         				<input name="renewalDate" type="text" readonly="readonly">        				
         				<?php echo form_close() ?>
         				<br><button onclick="vendorsAddService()">Add</button>
         			</div>
       		</div>      </div></div>  
<?php include("application/views/admin/common/footer.php");?>
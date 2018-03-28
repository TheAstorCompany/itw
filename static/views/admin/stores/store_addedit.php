<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
		<script type="text/javascript" src="<?php echo base_url();?>js/json2form.js"></script>    
        <script>
	        $(document).ready(function() {
                $("input[name='endDate']").datepicker({
					dateFormat: "mm/dd/yy",
				 	weekHeader: "W" 
				});
                $("input[name='startDate']").datepicker({
					dateFormat: "mm/dd/yy",
				 	weekHeader: "W" 
				});
                
                
                
	        	$('#vendors_tabs').tabs();
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
								document.location = '<?php echo base_url();?>admin/Stores/Delete/<?php echo $data->id;?>';
							},
							Cancel: function() {
								$( this ).dialog( "close" );
							}
						}
					});
				});
	
				getContacts();
				getServices();

				$("#service_name").autocomplete({
					source: "<?php echo base_url(); ?>admin/Stores/autocompleteServices",
					minLength: 2,
					select: function(e, ui) {
						$.get('<? echo base_url();?>admin/Stores/getService/'+ ui.item.id, function(data) {
							$('#form_tab_3').json2form(data);			
						});
					}
				});
			});
	
	
			function setContactsHTML(data) {
				var it = 0;				
				for (a in data.result) {
					var contactInfo = data.result[a].firstName.replace("'", '`') + ' ' + data.result[a].lastName.replace("'", '`') + ', ' + data.result[a].title +  ', ' + data.result[a].email + ', Ph ' + 	data.result[a].phone;
					if (data.result[a].id) {
						it = data.result[a].id; 
					}					
					$('#rightContainer').append('<h6><a href="JavaScript:void(0)" onclick="deleteContact('+it+', \''+data.result[a].firstName+ ' ' +data.result[a].lastName+'\')">Delete</a> ' + contactInfo + '</h6>');
					if (!data.result[a].id) {
						it++; 
					}
					     											
				}
			}

			function setServicesHTML(data) {
				var it = 0;				
				for (a in data.result) {
					if (data.result[a].id) {
						it = data.result[a].id; 
					}
					$('#servicesContainer').append('<h6><a href="JavaScript:void(0)" onclick="deleteService('+it+', \''+data.result[a].title+'\')">Delete</a> ' + data.result[a].title + '</h6>');
					if (!data.result[a].id) {
						it++; 
					}     											
				}				
			}
				
			function getContacts() {
				$.get($('#form_tab_2').attr('action'), function(data) {
					setContactsHTML(data);
				});
			}

			function getServices() {
				$.get($('#form_tab_3').attr('action'), function(data) {
					setServicesHTML(data);
				});
			}

			function addService() {
				$.post($('#form_tab_3').attr('action'), $('#form_tab_3').serialize(),
					function(data) {     								
						if (data.error != "") {
							$('#servicesErrorMessage').html(data.error);
						} else {
							$('#servicesErrorMessage').html('');
							if (data.result) {
								document.getElementById('form_tab_3').reset();
								//remove old records in list
								$('#servicesContainer>h6').remove();
								setServicesHTML(data);
							}
						}     								
					}
				);

			}
				
			function addContact() {
				$.post($('#form_tab_2').attr('action'), $('#form_tab_2').serialize(),
					function(data) {     								
						if (data.error != "") {
							$('#contactsErrorMessage').html(data.error);
						} else {
							$('#contactsErrorMessage').html('');
							if (data.result) {
								document.getElementById('form_tab_2').reset();
								//remove old records in list
								$('#rightContainer>h6').remove();
								setContactsHTML(data);
							}
						}     								
					}
				);
			}

			function deleteService(serviceId, name) {
				$( "<div>" ).dialog({
				resizable: false,
				height:160,
				title: 'Confirmation to delete',
				modal: true,
				buttons: {
					"Delete": function() {
						$(this).dialog( "close" );
	       				$.post(
	       					'<?php echo base_url();?>admin/Stores/DeleteService/<?php echo $data->id;?>',
	           				{'serviceId' : serviceId},
	       					function(data) {
	         					$('#vendorContactsErrorMessage').html('');
	         					if (data.result) {	         						
	         						document.getElementById('form_tab_3').reset();
	         						//remove old records in list
	         						$('#servicesContainer>h6').remove();
	         						setServicesHTML(data);
	         					}    								
	         				}
	       				);
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
				
				}).html('<p>Do you want to delete service " ' + name + '"?</p>');
			};
				
			function deleteContact(contactId, name) {
				$( "<div>" ).dialog({
				resizable: false,
				height:160,
				title: 'Confirmation to delete',
				modal: true,
				buttons: {
					"Delete": function() {
						$(this).dialog( "close" );
	       				$.post(
	       					'<?php echo base_url();?>admin/Stores/DeleteContact/<?php echo $data->id;?>',
	           				{contactId : contactId},
	       					function(data) {
	         					$('#vendorContactsErrorMessage').html('');
	         					if (data.result) {
	         						document.getElementById('form_tab_2').reset();
	         						//remove old records in list
	         						$('#rightContainer>h6').remove();
	         						setContactsHTML(data);
	         					}    								
	         				}
	       				);
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
				
				}).html('<p>Do you want to delete contact " ' + name + '"?</p>');
			};
		</script>
        <div class="content">
        	<?php if ($data->id) : ?>
        	<div  id="dialog-confirm" title="Confirmation to delete" style="display:none">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
				Do you want to delete '<?php echo $data->location; ?>' ?</p>
			</div>
			<?php endif; ?>			           	
    		<div class="row">
    			<div class="sixteen columns">
    				<a href="<?php echo base_url()?>admin/ManageCompany/Stores" class="button">&lt;- Go back</a>
    				<?php if ($data->id) : ?>    				
    				<button id="delete">Delete</button>    				
    				<?php endif; ?>
    			</div>
    		</div>    		
        	<div class="row">
        		<span style="color:red;"><?php echo validation_errors(); ?></span>
        		<div class="eight columns alpha">
				<?php if ($data->id) : ?>
        		
          			<h1>Store</h1><label for="type">Last Updated</label><fieldset class="dataentry">
          			<?php echo date("m/d/Y h:ia", strtotime($data->lastUpdated));?><br /><br />		 		
          		
          		<?php else:?>
        		
          			<h1>New Store</h1><fieldset class="dataentry"> 		
          		
          		<?php endif; ?>       
                <?php echo form_open('admin/Stores/AddEdit/' . $data->id, 'id="form_tab_1"'); ?><!--this is no longer relevant because it's not a tab-->
					<?php echo form_hidden('addupdate',1); ?>
						<label for="location">Location#</label>
						<input name="location" type="text" value="<?php echo set_value('location', $data->location);?>">
						
							<label for="squareFootage">Square Footage</label>
							<input name="squareFootage" type="text" value="<?php echo set_value('squareFootage', $data->squareFootage);?>">
						
						<label for="open24hours">24Hours?</label>
						<input name="open24hours" type="radio" value="0"  <?php if (set_value('open24hours') == '0'):?>checked<?php endif; ?>>N
						<input name="open24hours" type="radio" value="1"  <?php if (set_value('open24hours') == '1'):?>checked<?php endif; ?>>Y
						<br><br><br>
						
							<label for="district">District#</label>
							<input name="district" type="text" value="<?php echo set_value('district', $data->district);?>">
						
						<label for="districtName">District Name</label>
						<input name="districtName" type="text" value="<?php echo set_value('districtName', $data->districtName);?>">
						
							<label for="addressLine1">Address</label>
							<input name="addressLine1" type="text" value="<?php echo set_value('addressLine1', $data->addressLine1);?>">
						
						<label for="addressLine2">Address #2</label>
						<input name="addressLine2" type="text" value="<?php echo set_value('addressLine2', $data->addressLine2);?>">
						
							<label for="city">City</label>
							<input name="city" type="text" value="<?php echo set_value('city', $data->city);?>">
						
						<label for="state">State</label>
						<?php echo form_dropdown('stateId', $data->statesOptions, set_value('stateId', $data->stateId));?>
						
							<label for="phone">Phone</label>
							<input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">
						
						<label for="fax">Fax</label>
						<input name="fax" type="text" value="<?php echo set_value('fax', $data->fax);?>">
                        <?php if ($_isAdmin) :?>					
						<hr />
							<h5 style="color: #7ABF53;">Astor Only</h5>
							<label for="status">Status</label>
							<?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status));?>
							<label for="notes">Internal Notes</label>
							<textarea name="notes"><?php echo set_value('notes', $data->notes);?></textarea>
						
						<?php endif; ?>
						<button><?php if ($data->id):?>Save<?php else: ?>Add<?php endif; ?></button></fieldset>
						<?php echo form_close();?> 		
          	</div>
        	         
          		<div class="seven columns">
          		<div id="vendors_tabs" style="border:0px;">
					<ul>
						<li><a href="#tabs-1">Contacts</a></li>
		                <li><a href="#tabs-2">Services</a></li>
					</ul>    			
       				<div id="tabs-1" style="border:2px solid #7ABF53;">       					
       					<div id="contactsErrorMessage" style="color:red;"></div>
       					<div class="dataentry" id="rightContainer">
       					<strong>Current Contacts</strong>
      					</div>      					
      					<?php echo form_open('admin/Stores/Contacts/' . $data->id, 'id="form_tab_1"'); ?>
       					<?php echo form_hidden('addupdate',1); ?>
      					<span style="float:left;padding-right:10px">
      					<label for="firstName">First Name</label>
      					<input name="firstName" type="text" value="" autocomplete="off"></span>
      					<label for="lastName">Last Name</label><input name="lastName" type="text" value="" autocomplete="off">
      					<label for="title">Title</label><input name="title" type="text" value="" autocomplete="off">
      					<label for="email">Email</label><input name="email" type="text" autocomplete="off">
      					<label for="phone">Phone</label><input name="phone" type="text" autocomplete="off">      					
      					<?php echo form_close() ?>
      					<br><button onclick="addContact();">Add</button>
 					</div>
 					<div id="tabs-2" style="border:2px solid #7ABF53;">     
 						<div id="servicesErrorMessage" style="color:red;"></div> 
           				<?php echo form_open('admin/Stores/Services/' . $data->id, 'id="form_tab_2"'); ?>
       					<?php echo form_hidden('update',1); ?>
           				<div class="dataentry" id="servicesContainer">
           					<strong>Current Services</strong>   
							<h6></h6>
      					</div>
      					<input name="name" id="service_name" type="text" value="">
      					<label for="durationId">Duration</label>
      					<?php echo form_dropdown('durationId', $data->durationOptions);?>
      					<label for="puposeId">Purpose</label>
      					<?php echo form_dropdown('puposeId', $data->purposeOptions);?>
         				<label for="quantity">QTY</label>
         				<input name="quantity" type="text">
         				<label for="containerId">Container</label>
         				<?php echo form_dropdown('containerId', $data->containerOptions);?>
         				<label for="schedule">Schedule</label>
         				<?php echo form_dropdown('schedule', $data->scheduleOptions);?>
         				
                        AM
         				<input name="days[]" type="checkbox" value="128">
         				Sun
         				<input name="days[]" type="checkbox" value="2">
         				Mon 
         				<input name="days[]" type="checkbox" value="4">
         				Tue
         				<input name="days[]" type="checkbox" value="8">
         				Wed
         				<input name="days[]" type="checkbox" value="16">
         				Thu
         				<input name="days[]" type="checkbox" value="32">
         				Fri
         				<input name="days[]" type="checkbox" value="64">
         				Sat<br />
                        
                        PM
         				<input name="days[]" type="checkbox" value="16384">
         				Sun 
         				<input name="days[]" type="checkbox" value="256">
         				Mon 
         				<input name="days[]" type="checkbox" value="512">
         				Tue
         				<input name="days[]" type="checkbox" value="1024">
         				Wed
         				<input name="days[]" type="checkbox" value="2048">
         				Thu
         				<input name="days[]" type="checkbox" value="4096">
         				Fri
         				<input name="days[]" type="checkbox" value="8192">
         				Sat<br />
                        
                        
         				<label for="rate">Rate</label>
         				<input name="rate" type="text" value="">
                        
                        <label for="startDate">Start Date</label>
         				<input name="startDate" id="startDate" type="text" value="">
                        
         				<label for="endDate">End Date</label>
         				<input name="endDate" type="text" value="">
         				<br>
         				<?php echo form_close() ?>
         				<button  onclick="addService();">Add</button>
         			</div>
       		</div></div>
      </div>
<?php include("application/views/admin/common/footer.php");?>
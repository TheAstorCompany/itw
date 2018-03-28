<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
		<script type="text/javascript" src="<?php echo base_url();?>js/json2form.js"></script>    
        <script>
	        $(document).ready(function() {
	        	$('#tabs').tabs({selected: 1});
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
					$( "#dialog:ui-dialog" ).dialog( "destroy" );
					$( "#dialog-confirm" ).dialog({
						resizable: false,
						height:160,
						modal: true,
						buttons: {
							"Delete": function() {
								$(this).dialog( "close" );
								document.location = '<?php echo base_url();?>admin/DistributionCenters/Delete/<?php echo $data->id;?>';
							},
							Cancel: function() {
								$( this ).dialog( "close" );
							}
						}
					});
				});
	
				getContacts();
				getServices();
				getServicesHistory();
                getServiceContacts();

				$("#vendorName").autocomplete({
					source: "<?php echo base_url(); ?>admin/DistributionCenters/autocompleteServices",
					minLength: 2,
					select: function(e, ui) {
						$('#vendorId').val(ui.item.id);
						$('#vendorPhone').html("<a href='<?php echo base_url();?>admin/Vendors/AddEdit/" + ui.item.id + "'>" + ui.item.phone + "</a>");
						$('#vendorEmail').html("<a href='mailto:" + ui.item.email + "'>" + ui.item.email + "</a>");
					}
				});
			});

            //Contacts
            function getContacts() {
                $.get($('#form_tab_2').attr('action') + "?rand=" + Math.random(), function(data) {
                    setContactsHTML(data);
                });
            }
	
			function setContactsHTML(data) {				 
				var it = 0;
				
				for (a in data.result) {
					var contactInfo = data.result[a].firstName.replace("'", '`') + ' ' + data.result[a].lastName.replace("'", '`') + ', ' + data.result[a].title +  ', ' + data.result[a].email + ', Ph ' + 	data.result[a].phone;

					if (data.result[a].id) {
						it = data.result[a].id; 
					}

                    var html = '';
                    html += '<li>' + contactInfo + '<br/>';
                    html += '<a href="JavaScript:void(0)" onclick="editContact(' + it + ')">Edit</a>';
                    html += '&nbsp;|&nbsp;';
                    html += '<a href="JavaScript:void(0)" onclick="deleteContact(' + it + ', \'' + data.result[a].firstName.replace("'", "`") + ' ' + data.result[a].lastName.replace("'", "`")+'\')">Delete</a>';
                    html += '</li>';

                    $('#rightContainer ol').append(html);

					if (!data.result[a].id) {
						it++; 
					}
				}
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
                                $('#rightContainer li').remove();
                                setContactsHTML(data);
                            }
                        }
                        $('#form_tab_2 input[name="contactId"]').val(0);
                        $('#btnSubmitFormTab2').html('Add');
                    }
                );
            }

            function editContact(contactId) {
                $.post(
                    '<?php echo base_url();?>admin/DistributionCenters/Contact/' + contactId,
                    {},
                    function(data) {
                        $('#btnSubmitFormTab2').html('Save');

                        var $form_tab_2 = $('#form_tab_2');

                        $('input[name="contactId"]', $form_tab_2).val(contactId);
                        $('input[name="firstName"]', $form_tab_2).val(data.firstName);
                        $('input[name="lastName"]', $form_tab_2).val(data.lastName);
                        $('input[name="title"]', $form_tab_2).val(data.title);
                        $('input[name="email"]', $form_tab_2).val(data.email);
                        $('input[name="phone"]', $form_tab_2).val(data.phone);
                        $('textarea[name="notes"]', $form_tab_2).val(data.notes);
                    }
                );
            }

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
                                '<?php echo base_url();?>admin/DistributionCenters/DeleteContact/<?php echo $data->id;?>',
                                {contactId : contactId},
                                function(data) {
                                    $('#vendorContactsErrorMessage').html('');
                                    if (data.result) {
                                        document.getElementById('form_tab_2').reset();
                                        //remove old records in list
                                        $('#rightContainer li').remove();
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

            //Services
            function getServices() {
                $.get($('#form_tab_3').attr('action') + "?rand=" + Math.random(), function(data) {
                    setServicesHTML(data, false);
                });
            }

			function setServicesHTML(data, isHistory) {
				var it = 0;

				for (a in data.result) {
					if (data.result[a].id) {
	                    it = data.result[a].id; 
	            	}

					var title = data.result[a].title.replace(new RegExp('^' + data.result[a].vendorName + '', 'i'), '<a href="<?php echo base_url();?>admin/Vendors/AddEdit/' + data.result[a].vendorId + '">' + data.result[a].vendorName.toUpperCase() + '</a>');
					var html = '';
					html += '<li>' + title + '<br/>';
                    html += '<strong>Fees</strong><ul>';
                    for(var aa in data.result[a].fees) {
                        var fee = data.result[a].fees[aa];
                        html += '<li fee_k="0">' + $('#feeType option[value="' + fee.feeType + '"]').text() + ' - $' + fee.feeAmount + '</li>';
                    }
                    html += '</ul>';
					if(!isHistory){
					    html += '<a href="JavaScript:void(0)" onclick="editService(' + data.result[a].id + ')">Edit</a>';
					    html += '&nbsp;|&nbsp;';
					    html += '<a href="JavaScript:void(0)" onclick="deleteService(' + it + ', \'' + data.result[a].vendorName.replace("'", "`") + '\')">Delete</a>';
					}
					html += '</li>';
					
					if(isHistory)
					    $('#servicesContainerHistory').append(html);
					else
					    $('#servicesContainer').append(html);

					if (!data.result[a].id) {
                         it++; 
                 	}    											
				}
			}

            function addService() {
                $.post($('#form_tab_3').attr('action'), $('#form_tab_3').serialize(),
                    function(data) {
                        if (data.error != "") {
                            $('#servicesErrorMessage').html(data.error);
                        } else {
                            $('#servicesErrorMessage').html('');
                            if (data.result) {
                                resetFormTab3();
                                //remove old records in list
                                $('#servicesContainer li').remove();
                                setServicesHTML(data, false);
                                getServicesHistory();
                            }
                        }

                        $('#btnSubmitFormTab3').html('Add');
                    }
                );
            }

            function editService(serviceId) {
                $.post(
                    '<?php echo base_url();?>admin/DistributionCenters/Service/' + serviceId,
                    {},
                    function(data) {
                        $('#btnSubmitFormTab3').html('Save');
                        $('input[name="serviceId"]').val(serviceId);
                        $.get('<?php echo base_url(); ?>admin/Stores/Vendor/' + data.vendorId, function(ac_data) {
                            $('input[name="vendorName"]').val('#' + ac_data.number + ', ' + ac_data.name + ', ' + ac_data.addressLine1);
                            $('input[name="vendorId"]').val(ac_data.id);
                        });
                        $('input[name="category"][value="' + data.category + '"]').attr('checked', true);
                        $('select[name="materialId"]').val(data.materialId);
                        $('input[name="quantity"]').val(data.quantity);
                        $('select[name="unitId"]').val(data.unitId);
                        $('select[name="containerId"]').val(data.containerId);
                        $('select[name="schedule"]').val(data.schedule);
                        $('input[name="rate"]').val(data.rate);
                        $('input[name="equipmentDate"]').val(data.equipmentDate);
                        $('input[name="startDate"]').val(data.startDate);
                        $('input[name="endDate"]').val(data.endDate);
                        $('input[name="days[]"]').each(function() {
                            var k = $(this).val();
                            if(data.days[k]) {
                                $(this).attr('checked', true);
                            } else {
                                $(this).attr('checked', false);
                            }
                        });

                        $('#vendorPhone').html('');
                        $('#vendorEmail').html('');

                        $('ul.newserviceFees', $('#form_tab_3')).empty();
                        for(var aa in data.fees) {
                            var fee = data.fees[aa];
                            fee.feeTypeTitle = $('#feeType option[value="' + fee.feeType + '"]').text();
                            drawFee(fee, $('#form_tab_3'));
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
                                '<?php echo base_url();?>admin/DistributionCenters/DeleteService/<?php echo $data->id;?>',
                                {'serviceId' : serviceId},
                                function(data) {
                                    $('#vendorContactsErrorMessage').html('');
                                    if (data.result) {
                                        document.getElementById('form_tab_3').reset();
                                        //remove old records in list
                                        $('#servicesContainer li').remove();
                                        $('#vendorPhone').html('');
                                        $('#vendorEmail').html('');

                                        setServicesHTML(data, false);
                                        getServicesHistory();
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

            //ServiceContacts
            function getServiceContacts() {
                $.get($('#form_tab_5').attr('action') + "?rand=" + Math.random(), function(data) {
                    setServiceContactsHTML(data);
                });
            }

            function setServiceContactsHTML(data) {
                var it = 0;

                for (a in data.result) {
                    var contactInfo = data.result[a].firstName.replace("'", '`') + ' ' + data.result[a].lastName.replace("'", '`') + ', ' + data.result[a].title +  ', ' + data.result[a].email + ', Ph ' + data.result[a].phone + ' &laquo;' + data.result[a].companyName + '&raquo;';

                    if (data.result[a].id) {
                        it = data.result[a].id;
                    }

                    var html = '';
                    html += '<li>' + contactInfo + '<br/>';
                    html += '<a href="JavaScript:void(0)" onclick="editServiceContact(' + it + ')">Edit</a>';
                    html += '&nbsp;|&nbsp;';
                    html += '<a href="JavaScript:void(0)" onclick="deleteServiceContact(' + it + ', \'' + data.result[a].firstName.replace("'", "`") + ' ' + data.result[a].lastName.replace("'", "`")+'\')">Delete</a>';
                    html += '</li>';

                    $('#rightServiceContactsContainer ol').append(html);

                    if (!data.result[a].id) {
                        it++;
                    }
                }
            }

            function addServiceContact() {
                $.post($('#form_tab_5').attr('action'), $('#form_tab_5').serialize(),
                    function(data) {
                        if (data.error != "") {
                            $('#serviceContactsErrorMessage').html(data.error);
                        } else {
                            $('#serviceContactsErrorMessage').html('');
                            if (data.result) {
                                document.getElementById('form_tab_5').reset();
                                $('#rightServiceContactsContainer li').remove();
                                setServiceContactsHTML(data);
                            }
                        }
                        $('#form_tab_5 input[name="serviceContactId"]').val(0);
                        $('#btnSubmitFormTab5').html('Add');
                    }
                );
            }

            function editServiceContact(serviceContactId) {
                $.post(
                    '<?php echo base_url();?>admin/DistributionCenters/ServiceContact/' + serviceContactId,
                    {},
                    function(data) {
                        $('#btnSubmitFormTab5').html('Save');

                        var $form_tab_5 = $('#form_tab_5');

                        $('input[name="serviceContactId"]', $form_tab_5).val(serviceContactId);

                        $('input[name="firstName"]', $form_tab_5).val(data.firstName);
                        $('input[name="lastName"]', $form_tab_5).val(data.lastName);
                        $('input[name="title"]', $form_tab_5).val(data.title);
                        $('input[name="email"]', $form_tab_5).val(data.email);
                        $('input[name="phone"]', $form_tab_5).val(data.phone);
                        $('input[name="companyName"]', $form_tab_5).val(data.companyName);
                        $('textarea[name="notes"]', $form_tab_5).val(data.notes);
                    }
                );
            }

            function deleteServiceContact(contactId, name) {

                $( "<div>" ).dialog({
                    resizable: false,
                    height:160,
                    title: 'Confirmation to delete',
                    modal: true,
                    buttons: {
                        "Delete": function() {
                            $(this).dialog( "close" );
                            $.post(
                                '<?php echo base_url();?>admin/DistributionCenters/DeleteServiceContact/<?php echo $data->id;?>',
                                {contactId : contactId},
                                function(data) {
                                    $('#vendorContactsErrorMessage').html('');
                                    if (data.result) {
                                        document.getElementById('form_tab_2').reset();
                                        $('#rightServiceContactsContainer li').remove();
                                        setServiceContactsHTML(data);
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

            //ServicesHistory
			function getServicesHistory() {
				$.get($('#form_tab_4').attr('action') + "?rand=" + Math.random(), function(data) {
					$('#servicesContainerHistory').html('');
					setServicesHTML(data, true);
				});
			}

			function resetFormTab3() {
				$('#form_tab_3').get(0).reset();
				
				$('input[name="serviceId"]').val('');
				$('input[name="vendorId"]').val('');
				
				$('input[name="category"]').each(function() {
					$(this).attr('checked', false);
				});
				$('input[name="days[]"]').each(function() {
					$(this).attr('checked', false);
				});
				
				$('#vendorPhone').html('');
				$('#vendorEmail').html('');				
				
				$('#btnSubmitFormTab3').html('Add');
			}

            function addFee(form) {
                $form = $('#' + form);
                var isValid = true;
                $('div.newserviceFeesMsgsError').empty();
                if($('select[name="feeType"]', $form).val()==0) {
                    isValid = false;
                    $('div.newserviceFeesMsgsError', $form).append('<p>The Fee type field is required.</p>');
                }
                if($('input[name="feeAmount"]', $form).val()=='') {
                    isValid = false;
                    $('div.newserviceFeesMsgsError', $form).append('<p>The Fee field is required.</p>');
                }

                if(isValid) {
                    var feeAmount = parseFloat($('input[name="feeAmount"]', $form).val());
                    feeAmount = isNaN(feeAmount) ? 0.0 : feeAmount;
                    var waived = $('input[name="feeWaived"]', $form).is(':checked') ? 1 : 0;

                    var fee = {feeType: $('select[name="feeType"]', $form).val(), feeAmount: feeAmount, waived: waived, feeTypeTitle: $('select[name="feeType"] option:selected', $form).text()};
                    drawFee(fee, $form);

                    $('select[name="feeType"]', $form).val(0);
                    $('input[name="feeAmount"]', $form).val('');
                }
            }

            function drawFee(fee, $form) {
                drawFee.fee_id = drawFee.fee_id||1;
                fee.id = drawFee.fee_id;

                var li = '';
                li += '<li fee_id="' + fee.id + '">';
                li += '<input type="hidden" name="fees[' + fee.id + '][feeType]" value="' + fee.feeType + '" />';
                li += '<input type="hidden" name="fees[' + fee.id + '][feeAmount]" value="' + fee.feeAmount + '" />';
                li += '<input type="hidden" name="fees[' + fee.id + '][waived]" value="' + fee.waived + '" />';
                li += '<strong>' + fee.feeTypeTitle + '</strong> - $' + fee.feeAmount + '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="deleteFee(' + fee.id + ');">Delete</a>';
                li += '</li>';
                $('ul.newserviceFees', $form).append(li);

                drawFee.fee_id++;
            }

            function deleteFee(fee_id) {
                $('ul.newserviceFees li[fee_id="' + fee_id + '"]').remove();
            }
		</script>
<div class="content">
	<div class="row">
		<div class="sixteen columns">
			<a href="<?php echo base_url()?>admin/ManageCompany/DC" class="button">&lt;-
				Go back</a>
			<a href="<?php echo base_url()?>admin/DistributionCenters/AddEdit" class="button">Add</a>
    		<?php if ($data->id) : ?>    				
    		<a href="<?php echo base_url()?>admin/DistributionCenters/Delete/<?php echo $data->id;?>" class="button">Delete</a>    				
    		<?php endif; ?>
		</div>
	</div>
	<div class="row">
		<div class="eight columns alpha">
			<span style="color:red;"><?php echo validation_errors(); ?></span>
			<h1>DC</h1>
			<fieldset class="dataentry">
				<?php echo form_open('admin/DistributionCenters/AddEdit/' . $data->id, 'id="form_tab_1"'); ?>
				<?php echo form_hidden('addupdate',1); ?>
				<?php if ($data->id) : ?><label for="type">Last Updated</label><?php echo date("m/d/Y h:ia", strtotime($data->lastUpdated));?><br> <br><?php endif;?>
				<label for="name">DC Name *</label>
				<input name="name" type="text" value="<?php echo set_value('name', $data->name);?>">
				<label for="quickbooks">Quickbooks Name</label>
				<input name="quickbooks" type="text" value="<?php echo set_value('quickbooks', $data->quickbooks);?>">
				<label for="number">DC # *</label>
				<input name="number" type="text" value="<?php echo set_value('number', $data->number);?>">
				<label for="accountNumber">Account #</label>
				<input name="accountNumber" type="text" value="<?php echo set_value('accountNumber', $data->accountNumber);?>">
				<label for="addressLine1">Address *</label>
				<input name="addressLine1" type="text" value="<?php echo set_value('addressLine1', $data->addressLine1);?>">
				<label for="addressLine2">Address#2</label>
				<input name="addressLine2" type="text" value="<?php echo set_value('addressLine2', $data->addressLine2);?>">
				<label for="squareFootage">DC square footage</label>
				<input name="squareFootage" type="text" value="<?php echo set_value('squareFootage', $data->squareFootage);?>">
				<span style="float: left; padding-right: 10px">
					<label for="city">City *</label>
					<input name="city" type="text" value="<?php echo set_value('city', $data->city);?>">
				</span>
				<label for="state">State</label>
				<?php echo form_dropdown('stateId', $data->statesOptions, set_value('stateId', $data->stateId));?>

				<label for="zip">Zip *</label>
				<input name="zip" type="text" value="<?php echo set_value('zip', $data->zip);?>">
				<span style="float: left; padding-right: 10px">
					<label for="phone">Phone *</label>
					<input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">
				</span>
				<label for="fax">Fax</label>
				<input name="fax" type="text" value="<?php echo set_value('fax', $data->fax);?>">
				<hr>
				<?php if ($_isAdmin) :?>
				<h5 style="color: #7ABF53">Astor Only</h5>
				<label for="status">Status</label>
				<?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status));?>
				<label for="notes">Internal Notes</label>
				<textarea name="notes"><?php echo set_value('notes', $data->notes);?></textarea>
				<?php endif; ?>
				<input type="submit" value = "Save" class="button"/>
				<?php echo form_close();?>
			</fieldset>
		</div>
		<div class="seven columns omega">

			<div id="tabs" style="border: 0px;">
				<ul>
					<li><a href="#tabs-1">Contacts</a></li>
					<li><a href="#tabs-2">Services</a></li>
					<li><a href="#tabs-3">Service History</a></li>
                    <li><a href="#tabs-4">Service Contacts</a></li>
				</ul>
				<div id="tabs-1" style="border: 2px solid #7ABF53;">
					<div id="contactsErrorMessage" style="color:red;"></div>
					<div class="dataentry" id="rightContainer">
						<strong>Current Contacts</strong>
						<ol>
						</ol>
					</div>
					<?php echo form_open('admin/DistributionCenters/Contacts/' . $data->id, 'id="form_tab_2"'); ?>
       				<?php echo form_hidden('addupdate',1); ?>
                    <input name="contactId" type="hidden" value="0" />
					<span style="float: left; padding-right: 10px">
						<label for="firstName">First Name</label>
						<input name="firstName" type="text" value="">
                        <label for="title">Title</label>
                        <input name="title" type="text" value="">
                        <label for="email">Email</label>
                        <input name="email" type="text">
                        <label for="phone">Phone</label>
                        <input name="phone" type="text">
					</span>
					<label for="lastName">Last Name</label>
					<input name="lastName" type="text" value="">
                    <label for="notes">Notes</label>
                    <textarea name="notes" style="min-height: 120px;"></textarea>
					<?php echo form_close();?>
					<div class="minibutton">
						<button id="btnSubmitFormTab2" onclick="addContact();">Add</button>
					</div>
				</div>
				<div id="tabs-2" style="border: 2px solid #7ABF53;">
 					<div id="servicesErrorMessage" style="color:red;"></div>    
           			<?php echo form_open('admin/DistributionCenters/Services/' . $data->id, 'id="form_tab_3"'); ?>
       				<?php echo form_hidden('update', 1); ?>
					<?php echo form_hidden('serviceId', 0); ?>
					<div class="dataentry" id="servicesContainer">
						<strong>Current Services</strong>
						<ol>
						<!-- 
							<li><a href="addvendor.html#tabs-3">ABC Hauler</a>, Waste -
								$200.00<strong>&nbsp;</strong> <br> 1 - Compactor
								40yd  •  Weekly,  Mon AM - Wed PM - Fri AM<br> 05/14/12 -
								05/12/13 <br> <a href="#">Delete</a>
							</li>
						 -->
						</ol>
					</div>
					
					<label for="vendorName">Vendor</label>
					<input name="vendorName" id="vendorName" type="text" value="">
					<input type="hidden" name="vendorId" id="vendorId"/>
					
					<label for="type">Phone</label>
					<span id="vendorPhone"></span><br /><br />					
					<label for="type">Email</label>
					<span id="vendorEmail"></span><br /><br />
					<label for="type">Duration</label>
					<input type="hidden" name="durationId" value="1"/>
					Scheduled<br><br>
					<label for="category">Category</label>
					<input name="category" type="radio" value="0">
					<strong>Waste</strong>
					<input name="category" type="radio" value="1">
					<strong>Recycling</strong>
					<br>
					<br>
					<label for='materialId'>Material*</label>
	            	<?php echo form_dropdown('materialId', $data->materialOptions);?>
					<label for="quantity">QTY</label>
					<input name="quantity" type="text">
					<label for="containerId">Container</label>
					<?php echo form_dropdown('containerId', $data->containerOptions);?>
					<label for="schedule">Schedule</label>
					<?php echo form_dropdown('schedule', $data->scheduleOptions);?>
					AM <input name="days[]" type="checkbox" value="128">
					Sun <input name="days[]" type="checkbox" value="2">
					Mon <input name="days[]" type="checkbox" value="4">
					Tue <input name="days[]" type="checkbox" value="8">
					Wed <input name="days[]" type="checkbox" value="16">
					Thu <input name="days[]" type="checkbox" value="32">
					Fri <input name="days[]" type="checkbox" value="64">
					Sat<br>
					PM <input name="days[]" type="checkbox" value="16384">
					Sun <input name="days[]" type="checkbox" value="256">
					Mon <input name="days[]" type="checkbox" value="512">
					Tue <input name="days[]" type="checkbox" value="1024">
					Wed <input name="days[]" type="checkbox" value="2048">
					Thu <input name="days[]" type="checkbox" value="4096">
					Fri <input name="days[]" type="checkbox" value="8192">
					Sat<br> <br>
					<label for="rate">Rate</label>
					<input name="rate" type="text">
					<label for="startDate">Start Date</label>
					<input name="startDate" type="text">
					<label for="endDate">End Date</label>
					<input name="endDate" type="text">
                    <div>
                        <fieldset class="dataentry column3">
                            <h6 class="dataentry">Fees</h6>
                            <div class="dataentry">
                                <ul class="newserviceFees"></ul>
                                <div style="color: red;" class="newserviceFeesMsgsError"></div>
                            </div>
                            <label for="feeType">Fee Type</label>
                            <?php echo form_dropdown('feeType', $data->feeOptions, null, 'id="feeType" style="width:150px"')?>
                            <label for="feeAmount">Fee</label>
                            <input type="text" style="width:100px;" name="feeAmount" />
                            <label for="feeWaived">Waived/Saved</label>
                            <input type="checkbox" value="1" name="feeWaived" /><br />
                            <div style="padding-left: 200px;"><a href="javascript: void(0);" onclick="addFee('form_tab_3');">Add</a></div>
                        </fieldset>
                    </div>
					<?php echo form_close();?>
					<div class="minibutton">
						<button id="btnSubmitFormTab3" onclick="addService();">Add</button>&nbsp;&nbsp;<a href="javascript: void(0);" onclick="resetFormTab3();">Reset</a>
					</div>
				</div>
				<div id="tabs-3" style="border: 2px solid #7ABF53;">
 					<div id="servicesErrorMessage" style="color:red;"></div>    
           			<?php echo form_open('admin/DistributionCenters/Services/' . $data->id .'/true', 'id="form_tab_4"'); ?>
       				<?php echo form_hidden('update',1); ?>
					<div class="dataentry" id="servicesContainerHistory">
						<strong>Current Services</strong>
						<ol>
						<!-- 
							<li><a href="addvendor.html#tabs-3">ABC Hauler</a>, Waste -
								$200.00<strong>&nbsp;</strong> <br> 1 - Compactor
								40yd  •  Weekly,  Mon AM - Wed PM - Fri AM<br> 05/14/12 -
								05/12/13 <br> <a href="#">Delete</a>
							</li>
						 -->
						</ol>
					</div>
                    <?php echo form_close();?>
				</div>
                <div id="tabs-4" style="border: 2px solid #7ABF53;">
                    <div id="serviceContactsErrorMessage" style="color:red;"></div>
                    <div class="dataentry" id="rightServiceContactsContainer">
                        <strong>Current Contacts</strong>
                        <ol>
                        </ol>
                    </div>
                    <?php echo form_open('admin/DistributionCenters/ServiceContacts/' . $data->id, 'id="form_tab_5"'); ?>
                    <?php echo form_hidden('addupdate',1); ?>
                    <input name="serviceContactId" type="hidden" value="0" />
                    <span style="float: left; padding-right: 10px">
						<label for="firstName">First Name</label>
						<input name="firstName" type="text" value="">
                        <label for="title">Title</label>
                        <input name="title" type="text" value="">
                        <label for="email">Email</label>
                        <input name="email" type="text">
                        <label for="phone">Phone</label>
                        <input name="phone" type="text">
					</span>
                    <label for="lastName">Last Name</label>
                    <input name="lastName" type="text" value="">
                    <label for="companyName">Company Name</label>
                    <input name="companyName" type="text" value="">

                    <label for="notes">Notes</label>
                    <textarea name="notes"></textarea>
                    <?php echo form_close();?>
                    <div class="minibutton">
                        <button id="btnSubmitFormTab5" onclick="addServiceContact();">Add</button>
                    </div>
                </div>
			</div>
		</div>
	</div>

	<?php include("application/views/admin/common/footer.php");?>
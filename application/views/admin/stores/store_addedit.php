<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<script type="text/javascript" src="<?php echo base_url();?>js/json2form.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#tabs').tabs({ selected: 1 });

        $("input[name='endDate']").datepicker({
            dateFormat: "mm/dd/yy",
            weekHeader: "W"
        });
        $("input[name='startDate']").datepicker({
            dateFormat: "mm/dd/yy",
            weekHeader: "W"
        });
        $("input[name='endDateS']").datepicker({
            dateFormat: "mm/dd/yy",
            weekHeader: "W"
        });
        $("input[name='startDateS']").datepicker({
            dateFormat: "mm/dd/yy",
            weekHeader: "W"
        });
        $("input[name='equipmentDate']").datepicker({
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
        getServicesHistory();

        $("input[name='vendorName']").autocomplete({
            source: "<?php echo base_url(); ?>admin/Stores/autocompleteServices",
            minLength: 2,
            select: function(e, ui) {
                $(e.target).parents('form').find('input[name="vendorId"]').val(ui.item.id);
            }
        });
    });

    $(function() {
        $("#district").autocomplete({
            source: "<?php echo base_url(); ?>admin/Stores/autocompleteDistrict",
            minLength: 1,
            select: function(e, ui) {
                $("input[name='district']").val(ui.item.id);
                $("input[name='districtId']").val(ui.item.id);
                $("input[name='districtName']").val(ui.item.name);

            }
        });

        $("#district").change(function() {
            $("input[name='districtId']").val('');
        });

    });


    function setContactsHTML(data) {
        var it = 0;
        for (a in data.result) {
            var contactInfo = data.result[a].firstName.replace("'", '`') + ' ' + data.result[a].lastName.replace("'", '`') + ', ' + data.result[a].title +  ', ' + data.result[a].email + ', Ph ' + 	data.result[a].phone;
            if (data.result[a].id) {
                it = data.result[a].id;
            }

            $('#rightContainer ol').append('<li>'+contactInfo+'<br/><a href="JavaScript:void(0)" onclick="deleteContact('+it+', \''+data.result[a].firstName.replace("'", "`")+ ' ' +data.result[a].lastName.replace("'", "`")+'\')">Delete</a></li>');

            if (!data.result[a].id) {
                it++;
            }

        }
    }

    function setServicesHTML(data, isHistory) {
        var isSA = <?php echo ($is_read_only ? 'false' : 'true'); ?>;
        var it = 0;
        for (a in data.result) {
            if (data.result[a].id) {
                it = data.result[a].id;
            }

            var title = data.result[a].title.replace(data.result[a].vendorName.toUpperCase(), '<a href="<?php echo base_url();?>admin/Vendors/AddEdit/' + data.result[a].vendorId + '">' + data.result[a].vendorName.toUpperCase() + '</a>');
            var html = '';
            html += '<li>';
            html += title + '<br />';
            html += '<strong>Fees</strong><ul>';
            for(var aa in data.result[a].fees) {
                var fee = data.result[a].fees[aa];
                html += '<li fee_k="0">' + $('#feeType option[value="' + fee.feeType + '"]').text() + ' - $' + fee.feeAmount + '</li>';
            }
            html += '</ul>';
            if(!isHistory || isSA){
                html += '<a href="JavaScript:void(0)" onclick="editService(' + it + ', ' + isHistory.toString() + ')">Edit</a>';
                html += '&nbsp;|&nbsp;';
                html += '<a href="JavaScript:void(0)" onclick="deleteService(' + it + ', \'' + data.result[a].title.replace("'", "`") + '\'' + ', ' + isHistory.toString() + ')">Delete</a>';
            }
            html += '</li>';

            if(isHistory) {
                $('#servicesContainerHistory ol').append(html);
            } else {
                $('#servicesContainer ol').append(html);
            }


            if (!data.result[a].id) {
                it++;
            }
        }
    }

    function getContacts() {
        $.get($('#form_tab_2').attr('action') + "?rand=" + Math.random(), function(data) {
            setContactsHTML(data);
        });
    }

    function getServices() {
        $.get($('#form_tab_3').attr('action') + "?rand=" + Math.random(), function(data) {
            setServicesHTML(data, false);
        });
    }

    function getServicesHistory() {
        $.get($('#form_tab_4').attr('action') + "?rand=" + Math.random(), function(data) {
            $('#servicesContainerHistory ol').html('');
            setServicesHTML(data, true);
        });
    }

    function addService(isHistory) {
        $form = null;
        if(!isHistory) {
            $form = $('#form_tab_3');
        } else {
            $form = $('#form_tab_4');
            if($('input[name="serviceId"]', $form).val()==0) {
                alert('Edit only');
                return;
            }
        }

        $.post($form.attr('action'), $form.serialize(),
            function(data) {
                if (data.error != "") {
                    $('#servicesErrorMessage').html(data.error);
                } else {
                    $('#servicesErrorMessage').html('');
                    if (data.result) {
                        if(!isHistory) {
                            resetFormTab3();
                            $('#servicesContainer li').remove();
                        } else {
                            resetFormTab4();
                            $('#servicesContainerHistory li').remove();
                        }

                        setServicesHTML(data, isHistory);
                        getServicesHistory();
                    }
                }
                if(!isHistory) {
                    $('#btnSubmitFormTab3').html('Add');
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
                        $('#rightContainer li').remove();
                        setContactsHTML(data);
                    }
                }
            }
        );
    }

    function editService(serviceId, isHistory) {
        $.post(
            '<?php echo base_url();?>admin/Stores/Service/' + serviceId,
            {},
            function(data) {
                $form = null;
                if(!isHistory) {
                    $('#btnSubmitFormTab3').html('Save');
                    $form = $('#form_tab_3');
                } else {
                    $form = $('#form_tab_4');
                }

                $('input[name="serviceId"]', $form).val(serviceId);
                $.get('<?php echo base_url(); ?>admin/Stores/Vendor/' + data.vendorId, function(ac_data) {
                    $('input[name="vendorName"]', $form).val('#' + ac_data.number + ', ' + ac_data.name + ', ' + ac_data.addressLine1);
                    $('input[name="vendorId"]', $form).val(ac_data.id);
                });
                $('input[name="category"][value="' + data.category + '"]', $form).attr('checked', true);
                $('select[name="materialId"]', $form).val(data.materialId);
                $('input[name="quantity"]', $form).val(data.quantity);
                $('select[name="unitId"]', $form).val(data.unitId);
                $('select[name="containerId"]', $form).val(data.containerId);
                $('select[name="schedule"]', $form).val(data.schedule);
                $('input[name="rate"]', $form).val(data.rate);
                $('input[name="equipmentDate"]', $form).val(data.equipmentDate);
                $('input[name="startDate"]', $form).val(data.startDate);
                $('input[name="endDate"]', $form).val(data.endDate);
                $('input[name="days[]"]', $form).each(function() {
                    var k = $(this).val();
                    if(data.days[k]) {
                        $(this).attr('checked', true);
                    } else {
                        $(this).attr('checked', false);
                    }
                });
                $('ul.newserviceFees', $form).empty();
                for(var aa in data.fees) {
                    var fee = data.fees[aa];
                    fee.feeTypeTitle = $('#feeType option[value="' + fee.feeType + '"]').text();
                    drawFee(fee, $form);
                }
            }
        );
    }

    function deleteService(serviceId, name, isHistory) {
        $( "<div>" ).dialog({
        resizable: false,
        height: 160,
        title: 'Confirmation to delete',
        modal: true,
        buttons: {
            "Delete": function() {
                $(this).dialog( "close" );
                $.post(
                    '<?php echo base_url();?>admin/Stores/DeleteService/<?php echo $data->id;?>',
                    {'serviceId' : serviceId},
                    function(data) {
                        if (data.result) {
                            if(!isHistory) {
                                resetFormTab3();
                                $('#servicesContainer li').remove();
                            } else {
                                resetFormTab4();
                                $('#servicesContainerHistory li').remove();
                            }

                            setServicesHTML(data, isHistory);
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

    function resetFormTab3() {
        $form = $('#form_tab_3');

        $form.get(0).reset();

        $('input[name="serviceId"]', $form).val('');
        $('input[name="vendorId"]', $form).val('');

        $('input[name="category"]', $form).each(function() {
            $(this).attr('checked', false);
        });
        $('input[name="days[]"]', $form).each(function() {
            $(this).attr('checked', false);
        });

        $('ul.newserviceFees', $form).empty();

        $('#btnSubmitFormTab3').html('Add');
    }

    function resetFormTab4() {
        $form = $('#form_tab_4');

        $form.get(0).reset();

        $('input[name="serviceId"]', $form).val('');
        $('input[name="vendorId"]', $form).val('');

        $('input[name="category"]', $form).each(function() {
            $(this).attr('checked', false);
        });
        $('input[name="days[]"]', $form).each(function() {
            $(this).attr('checked', false);
        });

        $('ul.newserviceFees', $form).empty();
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
                    '<?php echo base_url();?>admin/Stores/DeleteContact/<?php echo $data->id;?>',
                    {contactId : contactId},
                    function(data) {
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
    <?php if ($data->id) : ?>
    <div  id="dialog-confirm" title="Confirmation to delete" style="display:none">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
        Do you want to delete '<?php echo $data->location; ?>' ?</p>
    </div>
    <?php endif; ?>
    <div class="row">
        <div class="sixteen columns">
            <a href="<?php echo base_url()?>admin/ManageCompany/Stores" class="button">&lt;- Go back</a>
            <?php if ($data->id && !$is_read_only) : ?>
            <button id="delete">Delete</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <span style="color:red;"><?php echo validation_errors(); ?></span>
    </div>
	<div class="row">
		<div class="eight columns">
			<h1>Store</h1>
			<fieldset class="dataentry">
				<?php if ($data->id) : ?>
				<label for="type">Last Updated</label>
				<?php echo date("m/d/Y h:ia", strtotime($data->lastUpdated));?><br>
				<br>
				<?php endif; ?> 
				<?php echo form_open('admin/Stores/AddEdit/' . $data->id, 'id="form_tab_1"'); ?>
				<?php echo form_hidden('addupdate',1); ?>
				<label for="location">Location# *</label>
				<input name="location" type="text" value="<?php echo set_value('location', $data->location);?>">
				<label for="squareFootage">Square Footage</label>
				<input name="squareFootage" type="text" value="<?php echo set_value('squareFootage', $data->squareFootage);?>">
				<label for="open24hours">24Hours?</label>
				<input name="open24hours" type="radio" value="0" <?php if (set_value('open24hours', $data->open24hours) == '0'):?>checked="checked"<?php endif; ?>>
				N
				<input name="open24hours" type="radio" value="1" <?php if (set_value('open24hours', $data->open24hours) == '1'):?>checked="checked"<?php endif; ?>>
				Y<br />
				<br />

				<label for="officeLocation">Office Location?</label>
				<input name="officeLocation" type="radio" value="0" <?php if (set_value('officeLocation', $data->officeLocation) == '0'):?>checked="checked"<?php endif; ?>>
				N
				<input name="officeLocation" type="radio" value="1" <?php if (set_value('officeLocation', $data->officeLocation) == '1'):?>checked="checked"<?php endif; ?>>
				Y<br />
				<br />
				
				<label for="district">District# *</label>
				<input type="hidden" name="districtId" value="<?php echo set_value('districtId', $data->districtId)?>" />
				<input name="district" id="district" type="text" value="<?php echo set_value('district', $data->district);?>">

				<label for="districtName">District Name</label>
				<input name="districtName" id="districtName" type="text" readonly="readonly" value="<?php echo set_value('districtName', $data->districtName);?>">

				<label for="franchise">Franchise?</label>
				<input name="franchise" type="radio" value="0" <?php if (set_value('franchise', $data->franchise) == '0'):?>checked="checked"<?php endif; ?>>
				N
				<input name="franchise" type="radio" value="1" <?php if (set_value('franchise', $data->franchise) == '1'):?>checked="checked"<?php endif; ?>>
				Y<br />
				<br />

				<label for="salesRanking">Sales Ranking</label>
				<input name="salesRanking" type="text" value="<?php echo set_value('salesRanking', $data->salesRanking);?>">

				<label for="addressLine1">Address *</label>
				<input name="addressLine1" type="text" value="<?php echo set_value('addressLine1', $data->addressLine1);?>">

				<label for="addressLine2">Address #2</label>
				<input name="addressLine2" type="text" value="<?php echo set_value('addressLine2', $data->addressLine2);?>">

				<label for="county">County</label>
				<input name="county" type="text" value="<?php echo set_value('county', $data->county);?>">

				<label for="city">City *</label>
				<input name="city" type="text" value="<?php echo set_value('city', $data->city);?>">

				<label for="stateId">State</label>
				<?php echo form_dropdown('stateId', $data->statesOptions, set_value('stateId', $data->stateId));?>
				<label for="postCode">Zip Code</label>
				<input type="text" name="postCode" value="<?php echo set_value('postCode', $data->postCode);?>" />

				<label for="phone">Phone *</label>
				<input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">

				<label for="fax">Fax</label>
				<input name="fax" type="text" value="<?php echo set_value('phone', $data->phone);?>">

                <label for="payFees">Pay Fees?</label>
                <input name="payFees" type="radio" value="0" <?php if (set_value('payFees', $data->payFees) == '0'):?>checked="checked"<?php endif; ?>>
                N
                <input name="payFees" type="radio" value="1" <?php if (set_value('payFees', $data->payFees) == '1'):?>checked="checked"<?php endif; ?>>
                Y<br />
                <br />

                <label for="startDateS">Start Date</label>
                <input name="startDateS" type="text" value="<?php echo set_value('startDateS', $data->startDateS);?>" />

                <label for="endDateS">End Date</label>
                <input name="endDateS" type="text" value="<?php echo set_value('endDateS', $data->endDateS);?>" />

				<hr>
				<?php if ($_isAdmin) :?>
				<h5 style="color:#7ABF53;">Astor Only</h5>
				<label for="type">Status</label>
				<?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status));?>
				<label for="type">Service Type</label>
				<?php echo form_dropdown('serviceType', $data->serviceTypeOptions, set_value('serviceType', $data->serviceType));?>			  
				<label for="notes">Internal Notes</label>
				<textarea name="notes"><?php echo set_value('notes', $data->notes);?></textarea>
				<?php endif;?>
                <?php if (!$is_read_only) { ?>
				<button>Save</button>
                <?php } ?>
				<?php echo form_close();?>
			</fieldset>
		</div>
        <div class="seven columns">
            <div id="tabs" style="border:0px;">
                <ul>
                    <li><a href="#tabs-1">Contacts</a></li>
                    <li><a href="#tabs-2">Services</a></li>
                    <li><a href="#tabs-3">Service History</a></li>
                </ul>
                <div id="tabs-1" style="border: 2px solid #7ABF53;">
                    <div id="contactsErrorMessage" style="color:red;"></div>
                    <div class="dataentry" id="rightContainer">
                        <strong>Current Contacts</strong>
                        <ol>
                        </ol>
                    </div>
                    <?php echo form_open('admin/Stores/Contacts/' . $data->id, 'id="form_tab_2"'); ?>
                    <?php echo form_hidden('addupdate',1); ?>
                    <span style="float: left; padding-right: 10px">
                        <label for="firstName">First Name</label>
                        <input name="firstName" type="text" value="">
                    </span>
                    <label for="lastName">Last Name</label>
                    <input name="lastName" type="text" value="">
                    <label for="title">Title</label>
                    <input name="title" type="text" value="">
                    <label for="email">Email</label>
                    <input name="email" type="text">
                    <label for="phone">Phone</label>
                    <input name="phone" type="text">
                    <?php echo form_close();?>
                    <div class="minibutton">
                        <button onclick="addContact();">Add</button>
                    </div>
                </div>
                <div id="tabs-2" style="border: 2px solid #7ABF53;">
                    <div id="servicesErrorMessage" style="color:red;"></div>
                    <?php echo form_open('admin/Stores/Services/' . $data->id, 'id="form_tab_3"'); ?>
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
                    <input name="vendorName" type="text" value="" />
                    <input type="hidden" name="vendorId" />

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
                    <label for="unitId">Unit</label>
                    <?php echo form_dropdown('unitId', $data->unitOptions, null, '');?>
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
                    <label for="equipmentDate">Equipment Date</label>
                    <input name="equipmentDate" type="text">
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
                        <button onclick="addService(false);" id="btnSubmitFormTab3">Add</button>&nbsp;&nbsp;<a href="javascript: void(0);" onclick="resetFormTab3();">Reset</a>
                    </div>
                </div>
                <div id="tabs-3" style="border: 2px solid #7ABF53;">
                    <div id="servicesErrorMessage" style="color:red;"></div>
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
                    <div <?php echo ($is_read_only ? 'style="display: none;"' : ''); ?>>
                        <?php echo form_open('admin/Stores/Services/' . $data->id .'/true', 'id="form_tab_4"'); ?>
                        <?php echo form_hidden('serviceId', 0); ?>
                        <label for="vendorName">Vendor</label>
                        <input name="vendorName" type="text" value="" />
                        <input type="hidden" name="vendorId" />

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
                        <label for="unitId">Unit</label>
                        <?php echo form_dropdown('unitId', $data->unitOptions, null, '');?>
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
                        <label for="equipmentDate">Equipment Date</label>
                        <input name="equipmentDate" type="text">
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
                                <?php echo form_dropdown('feeType', $data->feeOptions, null, 'style="width:150px"')?>
                                <label for="feeAmount">Fee</label>
                                <input type="text" style="width:100px;" name="feeAmount" />
                                <label for="feeWaived">Waived/Saved</label>
                                <input type="checkbox" value="1" name="feeWaived" /><br />
                                <div style="padding-left: 200px;"><a href="javascript: void(0);" onclick="addFee('form_tab_4');">Add</a></div>
                            </fieldset>
                        </div>
                        <?php echo form_close();?>
                        <div class="minibutton">
                            <button onclick="addService(true);" id="btnSubmitFormTab4">Save</button>&nbsp;&nbsp;<a href="javascript: void(0);" onclick="resetFormTab4();">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
<?php include("application/views/admin/common/footer.php");?>
<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
    <div class="content">
        <div class="row remove-bottom">
            <div class="sixteen columns">
                <h1>Support Request #<?php echo $data->id;?></h1>
            </div>
        </div>
        <div class="row">
            <div class="sixteen columns">
                <a href="javascript: window.history.back();" class="button">&lt;- Go back</a>
                <a href="<?php echo base_url();?>admin/SupportRequest/Delete/<?php echo $data->id;?>" class="button">Delete</a>
            </div>
        </div>
        <div id="tabs" style="border:0px;">
            <?php //include("application/views/admin/common/tabs.php"); ?>
            <div id="tabs-1" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
                <span style="color:red;"><?php echo validation_errors(); ?></span>
                <div class="sixteen columns alpha" style="width:100%;">
                    <div style="width:38%;float: left;">
                        <?php echo form_open("admin/SupportRequest/edit/{$data->id}", array('id'=>'SupportRequestAddForm'));?>
                        <?php echo form_hidden('submit_form',1);?>
                        <h5 class="dataentry" style="">General Info</h5><br /><br />
                        <fieldset class="dataentry">
                            <label for="location">Location Name or ID*</label>
                            <?php echo form_hidden('locationId', set_value('locationId', $data->locationId));?>
                            <?php echo form_hidden('locationType', set_value('locationType', $data->locationType));?>
                            <input name="locationName" id="location" type="text" value="<?php echo set_value('locationName', $data->locationName);?>">
                            <label for="vendorName">Vendor</label>
                            <input name="vendorName" id="vendorName" type="text" value="<?php echo set_value('vendorName', $data->vendorName);?>" />
                            <input type="hidden" id="vendorId" name="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>" />
                            <input type="hidden" id="prevVendorId" name="prevVendorId" value="<?php echo set_value('prevVendorId', $data->vendorId);?>" />
                            <label for="firstName">First Name*</label>
                            <input name="firstName" type="text" value="<?php echo set_value('firstName', $data->firstName);?>">
                            <label for="lastName">Last Name</label>
                            <input name="lastName" type="text" value="<?php echo set_value('lastName', $data->lastName);?>">
                            <label for="phone">Phone*</label>
                            <input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">
                            <label for="email">Email</label>
                            <input name="email" type="text" value="<?php echo set_value('email', $data->email);?>">
                            <label for="po">PO #</label>
                            <input name="po" type="text" value="<?php echo set_value('po', $data->po);?>">
                            <label for="cbre">WO #*</label>
                            <input name="cbre" type="text" value="<?php echo set_value('cbre', $data->cbre);?>" />
                            <?php if ($_isAdmin) { ?>
                                <input type="hidden" id="id_complete" name="complete" value="<?php echo set_value('complete',0); ?>"/>
                                <input type="hidden" name="complete_old" value="<?php echo $data->complete; ?>" />
                                <input type="hidden" id="id_lastUpdated" name="lastUpdated" value="<?php echo set_value('lastUpdated',date('m/d/Y'));?>">
                                <input type="hidden" id="id_notes" name="notes" value="<?php echo set_value('notes', nl2br(addslashes($data->notes))); ?>"/>
                            <?php } ?>
                            <input type="hidden" id="description_for_rask" name="description" value="" />
                        </fieldset>
                        <?php echo form_close();?>

                        <?php if ($_isAdmin) { ?>

                        <div style="border-top: 1px solid #DDD">
                            <h5  style="color:#7ABF53;width:200px;">Astor Only</h5><br />
                            <fieldset class="dataentry" id="adminPanel">
                                <label for="type">Complete?</label>
                                <?php echo form_dropdown('complete', $data->resolvedOptions, set_value('complete', $data->complete)); ?>
                                <label for="type">Last Updated</label>
                                <input name="lastUpdated" type="text" style="width:140px" value="<?php echo set_value('lastUpdated', date('m/d/Y'));?>">
                                <label for="message">Internal Notes</label>
                                <textarea name="notes" style="width:45%"><?php echo set_value('notes', $data->notes); ?></textarea>
                            </fieldset>
                            <?php
                                if(!empty($data->id)) {
                                    (new widget)->run('audit_table', 'support_request', $data->id);
                                }
                            ?>
                        </div>
                        <?php } ?>

                    </div>
                    <div style="border-left: 1px solid #DDD;margin-left: 39%;padding: 0 15px;">
                        <?php echo form_open('admin/SupportRequest/task', array('id'=>'SupportRequestTaskForm'));?>
                        <h5 class="dataentry" style="margin-bottom: 10px;">Description</h5><br /><br />
                        <fieldset class="dataentry">
                            <div class="dataentry" id="dataentry">
                            <?php include('tasks_ajax.php') ?>
                            </div>
                            <div style="color:red;" id="error"></div>
                            <span style='float:left;padding-right:10px'>
                            <label for="type">Purpose*</label>
                            <?php echo form_dropdown('purposeId', $data->SupportRequestServiceTypes, set_value('purposeId', 0))?>
                            </span> <strong>&nbsp;For*</strong>
                                <input name="purposeType" type="radio" value="0" <?php if (set_value('purposeType') == '0'):?>checked<?php endif; ?>>
                                <strong>for Waste</strong>
                                <input name="purposeType" type="radio" value="1" <?php if (set_value('purposeType') == '1'):?>checked<?php endif; ?>>
                                <strong>for Recycling</strong><br style="clear:both;">
                            <label for="quantity">QTY</label>
                            <input name="quantity" type="text" style="width:25px" value="<?php echo set_value('quantity', '');?>">
                            <label for="containerId">Container</label>
                            <?php echo form_dropdown('containerId', $data->SupportRequestContainers, set_value('containerId', 0));?>
                            <label for="serviceDate">Service Date*</label>
                            <input name="serviceDate" type="text" style="width:100px" value="<?php echo set_value('serviceDate','');?>">
                            <label for="deliveryDate">Delivery Date</label>
                            <input name="deliveryDate" id="deliveryDate" type="text" style="width:100px" value="<?php echo set_value('deliveryDate', '');?>">
                            <label for="removalDate">Removal Date</label>
                            <input name="removalDate" type="text" style="width:100px" value="<?php echo set_value('removalDate', '');?>">
                            <label for="description">Description</label>
                            <textarea name="description" id="task_description" style="width:45%"><?php echo set_value('description', (!empty($_POST['description']) ? $_POST['description'] : ''));?></textarea>
                            <input name="supportRequestId" id="supportRequestId" type="hidden" value="<?php echo set_value('supportRequestId', $data->id); ?>" />
                            <button>Add</button>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $('#SupportRequestTaskForm').submit(function() {
                                        $.post($('#SupportRequestTaskForm').attr('action'), $('#SupportRequestTaskForm').serialize(),
                                            function(data) {
                                                if (!data.error) {
                                                    document.getElementById('SupportRequestTaskForm').reset();
                                                    $('#error').html('');
                                                } else {
                                                    $('#error').html(data.error);
                                                }
                                                $('#dataentry').html(data.html);
                                                injectExistingTasks();
                                            }
                                        );
                                        return false;
                                    });

                                    $('#SupportRequestAddForm').submit(function(){
                                        $('#description_for_rask').val($('#task_description').val());
                                    });

                                    injectExistingTasks();
                                });

                                function deleteTask(task_id) {
                                    $.post('<?php echo base_url();?>admin/SupportRequest/deleteTask', { task_id: task_id },
                                        function(data) {
                                            $('li[task_k="' + task_id + '"]').remove();
                                        }
                                    );

                                    return false;
                                }

                                function injectExistingTasks() {
                                    var $form = $('#SupportRequestAddForm');

                                    $('input[name^="existing_tasks\["]', $form).remove();

                                    $('input[name^="existing_tasks\["]').each(function(){
                                        $form.append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />');
                                    });
                                }
                            </script>
                            <?php echo form_close();?>
                        </fieldset>
                    </div>
                    <hr>
                    <button style="background:#13602E; color:#fff; margin-left:200px;" onclick="document.getElementById('SupportRequestAddForm').submit();" form="SupportRequestAddForm" >Save Request</button>
                    <button style="background:#777777; color:#fff" onclick="window.location=window.location">Cancel</button>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
	$(function() {
		$("input[name='serviceDate'], input[name='deliveryDate'], input[name='removalDate'], input[name='lastUpdated']").datepicker({
			 dateFormat: "mm/dd/yy",
			 weekHeader: "W" 
		});
		$("#location").autocomplete({
			source: "<?php echo base_url(); ?>admin/SupportRequest/autocompleteLocation",
			minLength: 2,
			select: function(e, ui) {
				$("input[name='locationId']").val(ui.item.id);
				$("input[name='locationType']").val(ui.item.type);
			}
		});
        $("#vendorName").autocomplete({
            source: "<?php echo base_url(); ?>admin/WasteInvoice/autocompleteVendor",
            minLength: 2,
            select: function(e, ui) {
                $('#vendorId').val(ui.item.id);
                if($('#prevVendorId').val()=='') {
                    autopopulateScheduledServices();
                }
            }
        });
		<?php if ($_isAdmin) { ?>
			$('#adminPanel input,select,textarea').each(function(i, item) {
                $('#id_'+$(item).attr('name')).val($(item).val());
            });
            $('#adminPanel input,select,textarea').change(function() {
                $('#id_'+$(this).attr('name')).val($(this).val());
            });
        <?php } ?>
	});

    function autopopulateScheduledServices() {
        var locationId = $('input[name="locationId"]').val();
        var locationType = $('input[name="locationType"]').val();
        var vendorId = $('#vendorId').val();

        if (locationId >= 1 && vendorId >= 1) {
            $.get('<?php echo base_url();?>admin/WasteInvoice/getScheduledServices',
                'locationId='+locationId+'&vendorId='+vendorId + '&locationType='+locationType+'&data_only=true',
                function(data) {
                    if(data.length>0) {
                        var services = [];
                        for(i=0; i<data.length; i++) {
                            var post_data = data[i];
                            post_data.serviceTypeId = post_data.durationId;
                            $.ajax({
                                type: 'POST',
                                async: false,
                                url: '<?php echo base_url();?>admin/WasteInvoice/services?type=service&typefrom=true',
                                data: post_data,
                                success: function (result) {
                                    for(var k=0; k<result.data.services.length; k++) {
                                        var s = result.data.services[k];
                                        var is_exists = false;
                                        for(var j=0; j<services.length; j++) {
                                            var st = services[j];
                                            if(s.id==st.id) {
                                                is_exists = true;
                                                break;
                                            }
                                        }

                                        if(!is_exists) {
                                            services.push(s);
                                        }
                                    }
                                }
                            });
                        }
                        var content = '';
                        var servicesTotal = 0;
                        for(var j=0; j<services.length; j++) {
                            var st = services[j];
                            st.rate = parseFloat(st.rate);
                            st.durationId = parseInt(st.durationId)

                            content += (j + 1) + '.';
                            content += ' ';
                            content += (st.category == '0' ? 'Waste' : 'Recycling');
                            content += ' ~ ';
                            content += '$' + st.rate.toFixed(2);
                            content += ' ';
                            content += st.quantity + ' - ' + st.containerName;
                            if(st.durationId==1) {
                                content += ' ~ ';
                                content += 'Scheduled';
                            }
                            content += "\n";
                            servicesTotal += st.rate;
                        }
                        content += "\n";
                        content += 'Total $' + servicesTotal.toFixed(2);

                        $('#task_description').val(content);
                    }

                }, 'json');
        }
    }
</script>
<?php include("application/views/admin/common/footer.php");?>

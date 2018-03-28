<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<script type="text/javascript">

</script>
      <div class="content">
    <div class="row remove-bottom" style="margin-bottom:0;">
          <div class="sixteen columns">
        <h1>Admin Home</h1>
      </div>
        </div>
    <div class="row" style="margin-bottom:0;">
          <div class="sixteen columns" style="width:100%;">
        <div id="tabs" style="border:0px;">
			<?php include("application/views/admin/common/tabs.php"); ?>
            <?php /*
            <div id="ui-tabs-1" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;">
            <span style="color:red;"><?php echo validation_errors(); ?></span>
            <?php echo form_open('admin/SupportRequest/index', array('id'=>'SupportRequestAddForm'));?>
            <?php echo form_hidden('submit',1);?>
            <div class="five columns alpha" style="width:200px; padding-right:30px;margin-bottom:20px">
                  <h5 style="color:#7ABF53">Contact Info</h5>
                  <label for="firstName">First Name</label>
                  <input name="firstName" type="text" value="<?php echo set_value('firstName', $data->firstName);?>">
                  <label for="lastName">Last Name</label>
                  <input name="lastName" type="text" value="<?php echo set_value('lastName', $data->lastName);?>">
                  <label for="email">Email</label>
                  <input name="email" type="text" value="<?php echo set_value('email', $data->email);?>">
                  <label for="phone">Phone</label>
                  <input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">
            </div>
            
            <div class="eight columns"><h5 style="color:#7ABF53">Request Info</h5>
                  <span style='float:left;padding-right:10px'>
                  <label for="location">Location Name or ID</label>
                  <input name="location" id="location" type="text" value="<?php echo set_value('location');?>"></span>
                  <?php echo form_hidden('locationId', set_value('locationId'));?>
                  <?php echo form_hidden('locationType', set_value('locationType'));?>
                 <br>
              <h6 id="locationText">&nbsp;</h6><br><br>
                  
               	<span style='float:left;padding-right:10px'><label for="type">Purpose</label>
                <?php echo form_dropdown('serviceTypeId', $data->SupportRequestServiceTypes, set_value('serviceTypeId', $data->serviceTypeId))?>
              	</span><br><span style='float:left;padding-right:10px'>
              	  <input name="wasteRecycle" type="radio" value="0" <?php if (set_value('wasteRecycle') == '0'):?>checked<?php endif; ?>>
                  <strong>for Waste</strong>
                  <input name="wasteRecycle" type="radio" value="1" <?php if (set_value('wasteRecycle') == '1'):?>checked<?php endif; ?>>
                  <strong>for Recycling</strong></span><br style="clear:both;">
                  <span style="float:left; padding-right:10px">
              <label for="quantity">QTY</label>
              <input name="quantity" type="text" style="width:25px" value="<?php echo set_value('quantity', $data->quantity);?>">
              </span><span style="float:left; padding-right:10px">
              	  <label for="containerId">Container</label>
                  <?php echo form_dropdown('containerId', $data->SupportRequestContainers, set_value('containerId', $data->containerId));?>
                </span>
                  <span style="float:left;  padding-right:10px">
              <label for="deliveryDate">Delivery Date</label>
              <input name="deliveryDate" type="text" style="width:100px" value="<?php echo set_value('deliveryDate', $data->deliveryDate);?>">
              </span> <span style="float:left">
              <label for="removalDate">(Removal Date)</label>
              <input name="removalDate" type="text" style="width:100px" value="<?php echo set_value('removalDate', $data->removalDate);?>">
              </span><br style="clear:both;">
                  <label for="description">Description</label>
                  <textarea name="description" style="width:95%"><?php echo set_value('description', $data->description);?></textarea>
              </div>
             <?php if ($_isAdmin): ?>
             <div class="four columns omega" style="background:#efefef; padding:10px">
             	<h5  style="color:#7ABF53">Admin Info</h5>
                <label for="resolved">Complete?</label>
                <?php echo form_dropdown('resolved', $data->resolvedOptions, set_value('resolved',0));?>
                <span style="float:left">
              	<label for="lastUpdated">Last Updated</label>
              	<input name="lastUpdated" type="text" style="width:140px" value="<?php echo set_value('lastUpdated',date('m/d/Y'));?>">
              	</span>
              	<br class="clear">
                <label for="internalNotes">Internal Notes</label>
                <textarea name="internalNotes" style="width:95%"><?php echo set_value('internalNotes', $data->internalNotes); ?></textarea>
             </div>
             <?php endif;?>
             <?php form_close();?>
			<div class="clear"></div>
	      	<button form="SupportRequestAddForm" style="background:#13602E; color:#fff;" type="submit">Save Request</button>
          </div>
			*/?>			
	<div class="tab_enter_support_request" id="tabs-1" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
		<span style="color:red;"><?php echo validation_errors(); ?></span>
		<div class="sixteen columns alpha" style="width:100%;">
			<?php echo form_open('admin/SupportRequest/index', array('id'=>'SupportRequestAddForm', 'name'=>'SupportRequestAddForm', 'style'=>'width:780px;'));?>
			<div class="sra_col1" style="">
			    <h6 class="dataentry">General Info</h6>
			    <?php echo form_hidden('form_submit',1);?>
			    <fieldset class="dataentry">
				    <label for="location">Location Name or ID*</label>
				    <?php echo form_hidden('locationId', set_value('locationId'));?>
				    <?php echo form_hidden('locationType', set_value('locationType'));?>
				    <input name="locationName" id="locationName" type="text" value="<?php echo set_value('locationName');?>" />
				    <label for='vendorName'>Vendor*</label>
				    <input name="vendorName" id="vendorName" type="text" value="<?php echo set_value('vendorName', $data->vendorName);?>" />
				    <input type="hidden" id="vendorId" name="vendorId" value="<?php echo set_value('vendorId', $data->vendorId);?>" />
				    <label for="firstName">First Name*</label>
				    <input name="firstName" type="text" value="<?php echo set_value('firstName', $data->firstName);?>" />
				    <label for="lastName">Last Name</label>
				    <input name="lastName" type="text" value="<?php echo set_value('lastName', $data->lastName);?>" />
				    <label for="phone">Phone*</label>
				    <input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>" />
				    <label for="email">Email</label>
				    <input name="email" type="text" value="<?php echo set_value('email', $data->email);?>" />
				    <label for="po">PO #</label>
				    <input name="po" type="text" value="<?php echo set_value('po', $data->po);?>" />
				    <label for="cbre">WO #*</label>
				    <input name="cbre" type="text" value="<?php echo set_value('cbre', $data->cbre);?>" />
				    <?php if ($_isAdmin) { ?>
				    <input type="hidden" id="id_complete" name="complete" value="<?php echo set_value('complete',0); ?>" />
				    <input type="hidden" id="id_lastUpdated" name="lastUpdated" value="<?php echo set_value('lastUpdated',date('m/d/Y'));?>" />
				    <input type="hidden" id="id_notes" name="notes" value="<?php echo set_value('notes', nl2br(addslashes($data->notes))); ?>" />
				    <?php } ?>
			    </fieldset>
			    <?php //echo form_close();?>
			</div><div class="column2 sra_col2" style="">
				<?php //echo form_open('admin/SupportRequest/task', array('id'=>'SupportRequestTaskForm'));?>
				<h6 class="dataentry">Description</h6>
				<fieldset class="dataentry">
					<span class="dataentry" id="dataentry">
						<?php include('tasks_ajax.php') ?>
					</span>
					<div style="color:red;" id="error"></div>
					<span style='display:block:left;padding-right:10px'>
						<label for="type">Purpose*</label>
						<?php echo form_dropdown('purposeId',$data->SupportRequestServiceTypes,set_value('purposeId', $data->purposeId)) ?>;
						<!-- <?php echo form_dropdown('purposeId', $data->SupportRequestServiceTypes, set_value('purposeId', $data->purposeId), array('id'=>'sltPurposeId'))?> -->
					</span>
					<strong>&nbsp;For*</strong>
					<input name="purposeType" type="radio" value="0" <?php if (set_value('purposeType') == '0'):?>checked<?php endif; ?> />
					<strong>for Waste</strong>
					<input name="purposeType" type="radio" value="1" <?php if (set_value('purposeType') == '1'):?>checked<?php endif; ?> />
					<strong>for Recycling</strong>
					<br style="clear:both;" />
					<label for="quantity">QTY</label>
					<input name="quantity" id="quantity" type="text" style="width:25px" value="<?php echo set_value('quantity', $data->quantity);?>" />
					<label for="containerId">Container</label>
					<?php echo form_dropdown('containerId', $data->SupportRequestContainers, set_value('containerId', $data->containerId));?>
					<label for="serviceDate">Service Date*</label>
					<input name="serviceDate" id="serviceDate" type="text" style="width:100px" value="<?php echo set_value('serviceDate', $data->serviceDate);?>" />
					<label for="deliveryDate">Delivery Date</label>
					<input name="deliveryDate" id="deliveryDate" type="text" style="width:100px" value="<?php echo set_value('deliveryDate', $data->deliveryDate);?>" />
					<label for="removalDate">Removal Date</label>
					<input name="removalDate" id="removalDate" type="text" style="width:100px" value="<?php echo set_value('removalDate', $data->removalDate);?>" />
					<label for="description">Description</label>            
					<textarea name="description" style="width:340px"><?php echo set_value('description', $data->description);?></textarea>
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
				}
			);
			return false;
		});
		$('#SupportRequestAddFormSubmit').click(function() {
			document.getElementById('SupportRequestAddForm').submit();
		});
		$("#vendorName").autocomplete({
			source: "<?php echo base_url(); ?>admin/WasteInvoice/autocompleteVendor",
			minLength: 2,
			select: function(e, ui) {
				$('#vendorId').val(ui.item.id);

				autopopulateScheduledServices();
			}
		});

		$('#vendorName').change(function() {
			$('#vendorId').val('');
		});
	});
	
	function deleteTask(index) {
		$.post($('#SupportRequestTaskForm').attr('action'), {action: 'delete', index: index},
			function(data) {								
				$('#dataentry').html(data.html);								   								
			}
		);
	}
	
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
					
					$('textarea[name="description"]').val(content);
				}

			}, 'json');
		}		
	}				
</script>
				</fieldset>
				<?php echo form_close();?>
				
			</div>
			<?php if ($_isAdmin) { ?>
			<div class="column3 sra-col3">
				<h6 class="dataentry">Astor Only</h6>
				<fieldset class="dataentry" id="adminPanel">
					<label for="type">Complete?</label>
					<?php echo form_dropdown('complete', $data->resolvedOptions, set_value('complete',0));?>
					<label for="type">Last Updated</label>
					<input name="lastUpdated" type="text" style="width:140px" value="<?php echo set_value('lastUpdated',date('m/d/Y'));?>" />
					<label for="message">Internal Notes</label>            
					<textarea name="notes" style="width:340px"><?php echo set_value('notes', $data->notes); ?></textarea>
				</fieldset>
				
			</div>
			<hr />
			<button style="background:#13602E; color:#fff; margin-left:80px;" id="SupportRequestAddFormSubmit">Save Request</button>
			<button style="background:#777777; color:#fff" onclick="window.location=window.location">Cancel</button>
			<?php } ?>
		</div>
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
		$("#locationName").autocomplete({
			source: "<?php echo base_url(); ?>admin/SupportRequest/autocompleteLocation",
			minLength: 2,
			select: function(e, ui) {
				$("input[name='locationId']").val(ui.item.id);
				$("input[name='locationType']").val(ui.item.type);
				
				autopopulateScheduledServices();
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
</script>
<?php include("application/views/admin/common/footer.php");?>

<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<script type="text/javascript">

</script>
      <div class="content">
    <div class="row remove-bottom">
          <div class="sixteen columns">
        <h1>Admin Home</h1>
      </div>
        </div>
    <div class="row">
          <div class="sixteen columns">
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
			<div id="tabs-1" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
        	<span style="color:red;"><?php echo validation_errors(); ?></span>
        	<div class="sixteen columns alpha">
            <h5 class="dataentry">General Info</h5>
            <?php echo form_open('admin/SupportRequest/index', array('id'=>'SupportRequestAddForm', 'name'=>'SupportRequestAddForm'));?>
            <?php echo form_hidden('form_submit',1);?>
            <fieldset class="dataentry">
            <label for="location">Location Name or ID*</label>
            <?php echo form_hidden('locationId', set_value('locationId'));?>
            <?php echo form_hidden('locationType', set_value('locationType'));?>
            <input name="locationName" readonly="readonly" id="locationName" type="text" value="<?php echo set_value('locationName');?>">
            <label for="firstName">First Name</label>
            <input name="firstName" readonly="readonly" type="text" value="<?php echo set_value('firstName', $data->firstName);?>">
            <label for="lastName">Last Name</label>
            <input name="lastName" readonly="readonly" type="text" value="<?php echo set_value('lastName', $data->lastName);?>">
            <label for="phone">Phone</label>
            <input name="phone" readonly="readonly" type="text" value="<?php echo set_value('phone', $data->phone);?>">
            <label for="email">Email</label>
            <input name="email" readonly="readonly" type="text" value="<?php echo set_value('email', $data->email);?>">
            <label for="po">PO #</label>
            <input name="po" readonly="readonly" type="text" value="<?php echo set_value('po', $data->po);?>">
            <label for="cbre">CBRE #</label>
            <input name="cbre" readonly="readonly" type="text" value="<?php echo set_value('po', $data->cbre);?>">
         	<?php if ($_isAdmin) { ?>
         	<input type="hidden" id="id_complete" name="complete" value="<?php echo set_value('complete',0); ?>"/>
         	<input type="hidden" id="id_lastUpdated" name="lastUpdated" value="<?php echo set_value('lastUpdated',date('m/d/Y'));?>">
         	<input type="hidden" id="id_notes" name="notes" value="<?php echo set_value('notes', nl2br(addslashes($data->notes))); ?>"/>         	
         	<?php } ?>
         	</fieldset>
          	<?php echo form_close();?>
              <hr>
              <?php echo form_open('admin/SupportRequest/task', array('id'=>'SupportRequestTaskForm'));?>
              <h5 class="dataentry">Description</h5>
              <fieldset class="dataentry">
            	<div class="dataentry" id="dataentry">
            	<?php include('tasks_ajax.php') ?>
                </div>
                <div style="color:red;" id="error"></div>
            	<span style='float:left;padding-right:10px'>
                <label for="type">Purpose*</label>
                <?php echo form_dropdown('purposeId', $data->SupportRequestServiceTypes, set_value('purposeId', $data->purposeId), "disabled=\"disabled\"")?>
                </span> <strong>&nbsp;For*</strong>
                	<input name="purposeType" readonly="readonly" type="radio" value="0" <?php if (set_value('purposeType') == '0'):?>checked<?php endif; ?>>
                  	<strong>for Waste</strong>
                  	<input name="purposeType" readonly="readonly" type="radio" value="1" <?php if (set_value('purposeType') == '1'):?>checked<?php endif; ?>>
                  	<strong>for Recycling</strong><br style="clear:both;">
            <label for="quantity">QTY</label>
            <input name="quantity" readonly="readonly" type="text" style="width:25px" value="<?php echo set_value('quantity', $data->quantity);?>">
            <label for="containerId">Container</label>
            <?php echo form_dropdown('containerId', $data->SupportRequestContainers, set_value('containerId', $data->containerId), "disabled=\"disabled\"");?>
            <label for="serviceDate">Service Date</label>
            <input name="serviceDate" type="text" disabled="disabled" style="width:100px" value="<?php echo set_value('serviceDate', $data->serviceDate);?>">
            <label for="deliveryDate">Delivery Date</label>
            <input name="deliveryDate" id="deliveryDate" disabled="disabled" type="text" style="width:100px" value="<?php echo set_value('deliveryDate', $data->deliveryDate);?>">
            <label for="removalDate">Removal Date</label>
            <input name="removalDate" disabled="disabled" type="text" style="width:100px" value="<?php echo set_value('removalDate', $data->removalDate);?>">
            <label for="description">Description</label>            
            <textarea name="description" readonly="readonly" "width:45%"><?php echo set_value('description', $data->description);?></textarea>
            <button>Add</button>
            <script>
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
            		})
            	});
            	function deleteTask(index) {
            		$.post($('#SupportRequestTaskForm').attr('action'), {action: 'delete', index: index},
						function(data) {								
							$('#dataentry').html(data.html);								   								
						}
					);
            	}
            </script>
            <?php echo form_close();?>
          </fieldset>
           	<?php if ($_isAdmin) { ?>
          	<hr>
              <h5  style="color:#7ABF53;float:left;width:200px;">Astor Only</h5>
            <fieldset class="dataentry" id="adminPanel">
            <label for="type">Complete?</label>
            <?php echo form_dropdown('complete', $data->resolvedOptions, set_value('complete',0), "disabled=\"disabled\"");?>
            <label for="type">Last Updated</label>
            <input name="lastUpdated" type="text" disabled="disabled" style="width:140px" value="<?php echo set_value('lastUpdated',date('m/d/Y'));?>">
            <label for="message">Internal Notes</label>            
            <textarea readonly="readonly" name="notes" style="width:45%"><?php echo set_value('notes', $data->notes); ?></textarea>
          	</fieldset>
          	<?php } ?>
            <hr>
            <button style="background:#13602E; color:#fff; margin-left:200px;" onclick="document.getElementById('SupportRequestAddForm').submit()" >Save Request</button>
            <button style="background:#777777; color:#fff" onclick="window.location=window.location">Cancel</button>
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
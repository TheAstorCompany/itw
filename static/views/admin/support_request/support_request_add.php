<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
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
      <div id="ui-tabs-1" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;"> <span style="color:red;"><?php echo validation_errors(); ?></span> <?php echo form_open('admin/SupportRequest/index', array('id'=>'SupportRequestAddForm'));?> <?php echo form_hidden('submit',1);?>
        <div class="sixteen columns alpha">
          <h5 class="dataentry">General Info</h5>
          <fieldset class="dataentry">
            <label for="location">Location Name or ID*</label>
            <input name="location" id="location" type="text" value="<?php echo set_value('location');?>">
            <?php echo form_hidden('locationId', set_value('locationId'));?> <?php echo form_hidden('locationType', set_value('locationType'));?> <br>
            <h6 id="locationText">&nbsp;</h6>
            <label for="firstName">First Name</label>
            <input name="firstName" type="text" value="<?php echo set_value('firstName', $data->firstName);?>">
            <label for="lastName">Last Name</label>
            <input name="lastName" type="text" value="<?php echo set_value('lastName', $data->lastName);?>">
            <label for="phone">Phone</label>
            <input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">
            <label for="email">Email</label>
            <input name="email" type="text" value="<?php echo set_value('email', $data->email);?>">
          </fieldset>
          <hr />
          <h5 class="dataentry">Description</h5>
          <fieldset class="dataentry">
            <!--div class="dataentry"><strong>Tasks</strong>
                  <ol>
                <li><strong>Pickup</strong> for <strong>Waste</strong> &nbsp;&nbsp;&#8226;&nbsp;&nbsp; <strong>1</strong> - <strong>Compactor 40yd</strong> &nbsp;&nbsp;&#8226;&nbsp;&nbsp; (Service<strong> 05/18/12</strong> - Delivery <strong>05/21/12</strong> - Removal <strong>05/24/12</strong>) &nbsp;&nbsp;<a href="#">Delete</a><br>
                      Description for the entry goes here</li>
                <li><strong>Pickup</strong> for <strong>Waste</strong> &nbsp;&nbsp;&#8226;&nbsp;&nbsp; <strong>1</strong> - <strong>Compactor 40yd</strong> &nbsp;&nbsp;&#8226;&nbsp;&nbsp; (Removal <strong>05/24/12</strong>) &nbsp;&nbsp; <a href="#">Delete</a><br>
                      Description for the entry goes here</li>
                <li><strong>Relamping </strong>for<strong> Recycling</strong>&nbsp;&nbsp;&#8226;&nbsp; (Removal <strong>05/24/12</strong>) &nbsp;&nbsp; <a href="#">Delete</a><br>
                      10ft lamps </li>
                <li><strong> Setup New Service</strong> for <strong>Waste</strong> &nbsp;&nbsp;&#8226;&nbsp;&nbsp; <strong>1</strong> - <strong>Compactor 40yd</strong> &nbsp;&nbsp;&#8226;&nbsp;&nbsp; (Delivery <strong>05/24/12</strong>) &nbsp;&nbsp; <a href="#">Delete</a><br>
                      David called about a temporary service for construction waste</li>
              </ol>
                </div--> 
            <span style='float:left;padding-right:10px'>
            <label for="type">Purpose*</label>
            <?php echo form_dropdown('serviceTypeId', $data->SupportRequestServiceTypes, set_value('serviceTypeId', $data->serviceTypeId))?></span> For*
            <input name="wasteRecycle" type="radio" value="0" <?php if (set_value('wasteRecycle') == '0'):?>checked<?php endif; ?>>
            <strong>Waste</strong>
            <input name="wasteRecycle" type="radio" value="1" <?php if (set_value('wasteRecycle') == '1'):?>checked<?php endif; ?>>
            <strong>Recycling</strong><br style="clear:both;">
            <label for="quantity">QTY</label>
            <input name="quantity" type="text" style="width:25px" value="<?php echo set_value('quantity', $data->quantity);?>">
            <label for="containerId">Container</label>
            <?php echo form_dropdown('containerId', $data->SupportRequestContainers, set_value('containerId', $data->containerId));?>
            <label for="deliveryDate">Delivery Date</label>
            <input name="deliveryDate" type="text" style="width:100px" value="<?php echo set_value('deliveryDate', $data->deliveryDate);?>">
            <label for="removalDate">Removal Date</label>
            <input name="removalDate" type="text" style="width:100px" value="<?php echo set_value('removalDate', $data->removalDate);?>">
            <label for="description">Description</label>
            <textarea name="description" style="width:45%"><?php echo set_value('description', $data->description);?></textarea>
          </fieldset>
          <?php if ($_isAdmin): ?>
          <hr />
          <h5 class="dataentry">Astor Only</h5>
          <fieldset class="dataentry">
            <label for="resolved">Complete?</label>
            <?php echo form_dropdown('resolved', $data->resolvedOptions, set_value('resolved',0));?>
            <label for="lastUpdated">Last Updated</label>
            <input name="lastUpdated" type="text" style="width:140px" value="<?php echo set_value('lastUpdated',date('m/d/Y'));?>">
            <label for="internalNotes">Internal Notes</label>
            <textarea name="internalNotes" style="width:45%"><?php echo set_value('internalNotes', $data->internalNotes); ?></textarea>
          </fieldset>
        </div>
        <?php endif;?>
        <?php form_close();?>
      </div>
      <div class="clear"></div>
      <button form="SupportRequestAddForm" style="background:#13602E; color:#fff; margin-left:200px;" type="submit">Save Request</button>
    </div>
  </div>
</div>
<script type="text/javascript">
	$(function() {
		$("input[name='deliveryDate'], input[name='removalDate'], input[name='lastUpdated']").datepicker({
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
	});
</script>
<?php include("application/views/admin/common/footer.php");?>

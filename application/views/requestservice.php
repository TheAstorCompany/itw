<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
        <div class="content">
        	<div class="row"><div class="sixteen columns">
          	<h1>Support Center</h1>
          	Call 800-276-6726, email
          	<a href="mailto:info@astorrecycling.com">info@astorrecycling.com</a>
           	or use the form below...           
          	</div>
        </div>
        <div class="row">
			<div class="four columns">
				<h5 style="color:#7ABF53">Support Request Form</h5>
				<span style="color:red;"><?php echo validation_errors(); ?></span>
			</div>
		</div>
        <?php echo form_open('RequestService');?>
        <?php echo form_hidden('submit',1);?>
        <div class="row">
        	<div class="four columns">
          		<label for="fname">First Name</label><input name="firstName" type="text" value="<?php echo set_value('firstName', $data->firstName);?>"></div>
            <div class="four columns">
       			<label for="lname">Last Name</label><input name="lastName" type="text" value="<?php echo set_value('lastName', $data->lastName);?>">
           	</div>
           	<div class="four columns">
           		<label for="email">Email</label><input name="email" type="text" value="<?php echo set_value('email', $data->email);?>">      
       		</div>
       		<div class="four columns">
       			<label for="phone">Phone</label><input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">
           	</div></div>
           	<div class="row">
			<div class="four columns"><label for="location">Location</label>
           	<input name="location" id="location" type="text" value="<?php echo set_value('location', $data->location);?>">
           	<input type="hidden" name="locationId" type="text" value="<?php echo set_value('locationId', $data->locationId);?>">
           	</div>
           	<script>
           	$("#location").autocomplete({
				source: "<?php echo base_url(); ?>RequestService/autocompleteLocation",
				minLength: 2,
				select: function(e, ui) {
					$("input[name='locationId']").val(ui.item.id);
					$("#autocompletePH").html(ui.item.label);
				}
			});
           	</script>
           	<div class="twelve columns"><br>
           	<h6 id="autocompletePH"></h6></div></div>
           	<div class="row"><div class="sixteen columns">                                       
         	<label for="message">Message</label>
         	<textarea name="message" style="width:75%"><?php echo set_value('message', $data->message);?></textarea>       
       		<button type="submit">Send Request</button>                                 
           </div>
       </div>
       <?php echo form_close();?>        
<?php include("application/views/admin/common/footer.php");?>
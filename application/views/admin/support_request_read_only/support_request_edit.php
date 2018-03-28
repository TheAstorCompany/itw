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
        </div>
	</div>
	<div id="tabs" style="border:0px;">
		<?php //include("application/views/admin/common/tabs.php"); ?>            			
		<div id="tabs-1" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
			<span style="color:red;"><?php echo validation_errors(); ?></span>
			<div class="sixteen columns alpha">
				<h5 class="dataentry">General Info</h5>
				<fieldset class="dataentry">
					<label for="location">Location Name or ID</label>
					<input name="locationName" id="location" type="text" value="<?php echo set_value('locationName', $data->locationName);?>">

					<label for="firstName">First Name</label>
					<input name="firstName" type="text" value="<?php echo set_value('firstName', $data->firstName);?>">

					<label for="lastName">Last Name</label>
					<input name="lastName" type="text" value="<?php echo set_value('lastName', $data->lastName);?>">

					<label for="phone">Phone</label>
					<input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">

					<label for="email">Email</label>
					<input name="email" type="text" value="<?php echo set_value('email', $data->email);?>">

					<label for="po">PO #</label>
					<input name="po" type="text" value="<?php echo set_value('po', $data->po);?>">

					<label for="cbre">CBRE #</label>
					<input name="cbre" type="text" value="<?php echo set_value('po', $data->cbre);?>">
				</fieldset>
				
				<hr />
		  
				<h5 class="dataentry">Description</h5>
				
				<fieldset class="dataentry">
					<div class="dataentry" id="dataentry">
					<?php include('tasks_ajax.php') ?>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
</div>

<?php include("application/views/admin/common/footer.php");?>
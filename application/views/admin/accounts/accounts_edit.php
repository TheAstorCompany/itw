<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
        <div class="content">
        <div class="row"><div class="sixteen columns">
          <h1>Account</h1>
          </div></div>
                         <div class="row"><div class="four columns">Company<h5><?php echo $company_data->name;?><br>
                         <?php echo $company_data->addressLine;?></h5><br>
                         Have a question?<h5>
                         <a href="<?php echo base_url(); ?>admin/Help">Get help</a></h5>
           </div>
           <div class="five columns">
           <span style="color:red;"><?php echo validation_errors(); ?></span>
           <?php echo form_open('admin/Accounts/Update'); ?>
           <?php echo form_hidden('update',1); ?>
               <label for="firstName">First Name</label>
               <input name="firstName" type="text" value="<?php echo set_value('firstName', $data->firstName); ?>">
               <label for="lastName">Last Name</label>
               <input name="lastName" type="text" value="<?php echo set_value('lastName', $data->lastName); ?>">
               <label for="title">Title</label>
               <input name="title" type="text" value="<?php echo set_value('title', $data->title); ?>">
               <label for="phone">Phone</label>
               <input name="phone" type="text" value="<?php echo set_value('phone', $data->phone); ?>">
			</div>
			<div class="five columns">
				<label for="email">Email</label>
				<input name="email" type="text" value="<?php echo set_value('email', $data->email); ?>">
				<label for="password">Password</label>
           		<input name="password" type="password" >
           		<label for="password2">Password (Confirm)</label>
           		<input name="password2" type="password">
           		<button type="submit">Save</button>
           	</div>
           <?php echo form_close();?>
           </div>
        </div>
<?php include("application/views/admin/common/footer.php");?>
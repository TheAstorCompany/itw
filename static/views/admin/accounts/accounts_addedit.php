<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>        
        <script>
        $(document).ready(function(){	
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
							document.location = '<?php echo base_url();?>admin/Accounts/Delete/<?php echo $userId;?>';
						},
						Cancel: function() {
							$( this ).dialog( "close" );
						}
					}
				});
			});
		});
		</script>
        <div class="content">
        	<?php if ($userId) { ?>
        	<div  id="dialog-confirm" title="Confirmation to delete" style="display:none">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
				Do you want to delete user <?php echo $data->firstName . ' ' . $data->lastName; ?> ?</p>
			</div>
			<?php } ?>
			<?php echo form_open('admin/Accounts/AddEdit/' . $userId); ?>
    		<?php echo form_hidden('update',1); ?>           	
    		<div class="row">
    			<div class="sixteen columns"><a href="<?php echo base_url()?>admin/ManageCompany/Peoples" class="button">&lt;- Go back</a><button type="submit"><?php if ($userId) { ?>Save<?php } else {?>Add<?php } ?></button><?php if ($userId) { ?><button id="delete" onclick="return false">Delete</button><?php } ?></div>
    		</div>
        	<div class="row">
        		<span style="color:red;"><?php echo validation_errors(); ?></span>
        		<?php if ($data->lastUpdated) { ?>
        		<div class="sixteen columns">
          			<h1>User</h1><label for="type">Last Updated</label>
          			<?php echo date("m/d/Y h:ia", strtotime($data->lastUpdated));?>		 		
          		</div>
          		<?php } ?>
          	</div>
        	<div class="row">
          		<div class="eight columns">
       				<span style="float:left;padding-right:10px"><label for="username">Username</label>
    				<input name="username" type="text" value="<?php echo set_value('username', $data->username); ?>">
    				</span><label for="password">Password</label>
    				<input name="password" type="password" value="" autocomplete="off">    
      				<span style="float:left;padding-right:10px">
      				<label for="firstName">First Name</label><input name="firstName" type="text" value="<?php echo set_value('firstName', $data->firstName); ?>"></span>
      				<label for="lastName">Last Name</label><input name="lastName" type="text" value="<?php echo set_value('lastName', $data->lastName); ?>">
      				<label for="title">Title</label><input name="title" value="<?php echo set_value('title', $data->title); ?>" type="text" style="width:76%">
      				<span style="float:left;padding-right:10px"><label for="email">Email</label><input name="email" type="text" value="<?php echo set_value('email', $data->email); ?>"></span>
      				<label for="phone">Phone</label><input name="phone" type="text" value="<?php echo set_value('phone', $data->phone); ?>">
          		</div>            
				<div class="eight columns omega">
					<div  style="background:#efefef;padding:10px">
             			<h5  style="color:#900;">Admin Info</h5>
         				<span style="float:left;padding-right:10px"><label for="active">Status</label>
     					<?php echo form_dropdown('active', $data->activeOptions, set_value('active', $data->active))?>    				
         				</span>
   						<label for="accessLevel">Access Level</label>
         				<?php echo form_dropdown('accessLevel', $data->accessLevelOptions, set_value('accessLevel', $data->accessLevel))?>         				        
         				<label for="internalNotes">Internal Notes</label>
         				<textarea name="internalNotes" style="width:100%"><?php echo set_value('internalNotes', $data->internalNotes); ?></textarea>
         			</div>
         		</div>         		
       		</div>
       		<?php echo form_close(); ?> 
       	</div>
<?php include("application/views/admin/common/footer.php");?>
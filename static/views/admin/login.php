<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<div class="content">
	<div style="margin:0 auto;width:300px;">
		<?php if (!empty($errors)):?>
		<ul style="color:red;list-style-type:circle;">
			<?php foreach ($errors as $error):?>
			<li><?php echo $error?></li>
			<?php endforeach;?>
		</ul>
		<?php endif;?>
		<?php echo form_open(empty($_controller) ? 'admin/Auth' : $_controller);?>
		<?php echo form_hidden('login', 1);?>
	                  <label for="username">Username</label>
	                  <input name="username" type="text">
	                  <label for="password">Password</label>
	                  <input name="password" type="password">
	                  <button type="submit" style="background:#13602E; color:#fff">Log in</button>
	    <?php echo form_close();?>
    </div>
</div>
<?php include("application/views/admin/common/footer.php");?>
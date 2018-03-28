<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<div class="row">
	<div class="sixteen columns">
		<h1>Help</h1>
	</div>
</div>
<div class="row">
	<div class="four columns">Write to
		<h5>The Astor Company<br>123 Walgreens St,<br>New York, NY 90210</h5><br>
		Call 24-Hours
		<h5>800-278-6726</h5><br>
		Email
		<h5>Kevin Flood<br>
		<a href="mailto:info@astorrecycling.com">info@astorrecycling.com</a></h5>
    </div>
	<div class="ten columns">
	<span style="color:red;"><?php echo validation_errors(); ?></span>
	<?php echo form_open('admin/Help');?>
		<?php echo form_hidden('submit',1);?>
		<label for="subject">Subject</label>
		<input name="subject" type="text" value="<?php echo set_value('subject', $data->subject);?>">
		<label for="message">Message</label>
		<textarea name="message" style="width: 100%"><?php echo set_value('message', $data->message);?></textarea>
		<button type="submit">Send Request</button>
	<?php echo form_close();?>
	</div>
</div>
<?php include("application/views/admin/common/footer.php");?>

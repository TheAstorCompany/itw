<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

    <head>

    <!-- Basic Page Needs
  ================================================== -->
    <meta charset="utf-8">
    <title>Home - Astor</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <!-- Mobile Specific Metas
  ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
  ================================================== -->
    <link rel="stylesheet" href="<?php echo base_url();?>stylesheets/demo_table.css">
    <link rel="stylesheet" href="<?php echo base_url();?>stylesheets/base.css">
    <link rel="stylesheet" href="<?php echo base_url();?>stylesheets/astor/jquery-ui-1.8.17.custom.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>stylesheets/skeleton.css">
    <link rel="stylesheet" href="<?php echo base_url();?>stylesheets/layout.css">

    <!-- Favicons
	================================================== -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,600,700' rel='stylesheet' type='text/css'>

    <!-- Head Scripts
	================================================== -->
    <script src="<?php echo base_url();?>js/modernizr-2.5.1.js"></script>
    <script src="<?php echo base_url();?>js/jquery-1.7.1.js"></script>
    <script src="<?php echo base_url();?>js/jquery-ui-1.8.17.custom.min.js"></script>
    <script src="<?php echo base_url();?>js/jquery.dataTables.js"></script>
    <script src='<?php echo base_url();?>js/jquery.color-RGBa-patch.js'></script>
    <script src="<?php echo base_url();?>js/custom.js"></script>
    </head>
    <body>
	<?php if($this->session->flashdata('info')){ ?>
	<div style="position:absolute;top:0;left:0;z-index:10;width:100%;background-color:yellow;text-align:center;font-weight:bold;height:auto;"><?php echo $this->session->flashdata('info');?></div>
	<?php } ?>
	<?php if($this->session->flashdata('popupmessage')){ ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#popupmessage_dialog').dialog({ modal: true, autoOpen: true, title: 'Message', buttons: { 'OK': function() { $(this).dialog('close'); } } });
		});
	</script>
	<div id="popupmessage_dialog">
		<p><?php echo $this->session->flashdata('popupmessage');?></p>
	</div>
	<?php } ?>	
<!-- Primary Page Layout
	================================================== -->
<div id="article" class="container">
      <div class="row remove-bottom">
	<div class="header">
		<a href="<?php echo base_url();?>" style="position:relative;"><img src="<?php echo base_url();?>images/logo.png" width="250" height="112" alt="Astor Home"></a>
          <?php if (!isset($hideAdminMenu)):?><div style="position:absolute;right:44px;top:10px;margin-top:10px; text-align:right; font-size:90%;">Welcome <?php echo $_loggedUser;?> &nbsp;&nbsp; <a href="<?php echo base_url();?>admin/Accounts/Update">Account</a> &nbsp;&nbsp;&nbsp; <a href="<?php echo  base_url();?>admin/Help">Help</a> &nbsp;&nbsp;&nbsp; <a href="<?php echo base_url();?>admin/Auth/logOut">Logout</a>
          <br/>Call 800-278-6726 or <a href="<?php echo base_url();?>RequestService">Get Support</a>
          <?php else:?>
          <div style="position:absolute;right:44px;top:10px;margin-top:10px;text-align:right; font-size:90%;">Welcome Guest! Please <a href="<?php echo base_url() . (empty($_controller) ? 'admin/Auth' : 'Front');?>">Login here</a>.
          <?php endif;?>
            <?php if (isset($hideAdminMenu) && (!isset($hideAdminMenu))):?>
            <select name="account">
	            <?php foreach ($companiesList as $company):?>
	            <option value="<?php echo $company->id;?>"><?php echo $company->name;?></option>
	            <?php endforeach;?>
            </select>
            <?php else: ?>
            <?php endif;?>
      </div>
        </div>
  </div>
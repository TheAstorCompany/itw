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
	<div style="position:absolute;top:0;left:0;z-index:10;width:100%;background-color:yellow;text-align:center;font-weight:bold;"><?php echo $this->session->flashdata('info');?></div>
<!-- Primary Page Layout
	================================================== -->
<article class="container">
      <div class="row remove-bottom">
	    <header>
	    	<a href="<?php echo base_url();?>">
	    		<img src="<?php echo base_url();?>images/logo.png" width="250" height="112" alt="Astor Home" />
	    	</a>
	    </header>
  	  </div>
  	  <div class="clear"></div>
<?php
	include("application/views/admin/common/header.php");
	include("application/views/admin/common/top_menu.php");
?>
<div class="content">
	<div class="row">
		<div class="two columns">
			<a href="javascript:javascript:history.go(-1)" class="button">&lt;- Go back</a>
		</div>
	</div>
	<div class="row">
		<div class="four columns">
			<h1><?php echo $DCData['name']; ?></h1>
		</div>
		<div class="two columns">
			Square Footage<h5><?php echo $DCData['squareFootage']; ?> sqft</h5>
		</div>
		<div class="two columns">
			Diversion Rate<h5><?php echo number_format($DiversionRate, 2); ?>%</h5>
		</div>
		<div class="eight columns omega">
		<?php
			include("contacts.php");
		?>
		</div>
	</div>

	<div class="row">
		<div class="sixteen columns">
			<div id="tabs" style="border:0px;">
				<ul>
					<!--<li <?php echo ($selected_tab_id=='Waste' ? 'id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'StoreInfo/Waste/' . $id; ?>">Waste</a></li>-->
					<!--<li <?php echo ($selected_tab_id=='Recycling' ? 'id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'StoreInfo/Recycling/' . $id; ?>">Recycling</a></li>-->
					<!--<li <?php echo ($selected_tab_id=='CostSavings' ? 'id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'StoreInfo/CostSavings/' . $id; ?>">Cost/Savings</a></li>-->
					<li <?php echo ($selected_tab_id=='Invoices' ? 'id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'StoreInfo/Invoices/' . $id; ?>">Invoices</a></li>
					<li <?php echo ($selected_tab_id=='SupportRequests' ? 'id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'StoreInfo/SupportRequests/' . $id; ?>">Support Requests</a></li>
					<li <?php echo ($selected_tab_id=='SiteInfo' ? 'id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'StoreInfo/SiteInfo/' . $id; ?>">Site Info</a></li>
				</ul>

				<div style="border:2px solid #7ABF53; padding: 10px;">
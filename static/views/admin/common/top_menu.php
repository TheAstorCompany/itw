      <div class="<?php if (!isset($hideAdminMenu) && $_main_controller != "RequestService"): ?>tall<?php endif;?>nav-wrap">
	    <ul class="group" id="mainnavbar">
	          <?php if (!isset($hideAdminMenu)): ?><li <?php if (!$_main_controller):?> class="current_page_item"<?php endif;?>><a rel="#80C65A" href="<?php echo base_url();?>admin/SupportRequest/index">Admin</a></li>
	          <li <?php if ($_main_controller == "Company"): ?>class="current_page_item"<?php endif; ?>><a rel="#80C65A" href="<?php echo base_url()?>Company">Company</a></li>
	          <li <?php if ($_main_controller == "DistributionCenters"): ?>class="current_page_item"<?php endif; ?>><a rel="#1C4D25" href="<?php echo base_url()?>DistributionCenters">Distribution Centers</a></li>
	          <li <?php if ($_main_controller == "Stores"): ?>class="current_page_item"<?php endif; ?>><a rel="#1C4D25" href="<?php echo base_url()?>Stores">Stores</a></li>
	          <li <?php if ($_main_controller == "RequestService"): ?>class="current_page_item"<?php endif; ?>><a rel="#1C4D25" href="<?php echo base_url();?>RequestService">Support Center</a></li>
	          <?php endif; ?>
	    </ul>


	    <?php if($_main_controller == "RequestService"): ?>


	    <?php elseif($_main_controller):?>
    <ul class="group" id="subnavbar">
          <li <?php if ((!$_controller || ($_controller == "Dashboard"))):?> class="current_subpage_item"<?php endif;?>><a href="<?php echo base_url()?><?php echo $_main_controller;?>/Dashboard">Dashboard</a></li>
          <li <?php if ($_controller == "Waste"):?> class="current_subpage_item"<?php endif;?>><a href="<?php echo base_url()?><?php echo $_main_controller;?>/Waste">Waste</a></li>
          <li <?php if ($_controller == "Recycling"):?> class="current_subpage_item"<?php endif;?>><a href="<?php echo base_url()?><?php echo $_main_controller;?>/Recycling">Recycling</a></li>
          <li <?php if ($_controller == "Cost"):?> class="current_subpage_item"<?php endif;?>><a href="<?php echo base_url()?><?php echo $_main_controller;?>/Cost">Cost/Savings</a></li>
          <li <?php if ($_controller == "Services"):?> class="current_subpage_item"<?php endif;?>><a href="<?php echo base_url()?><?php echo $_main_controller;?>/Services">Services</a></li>
          <?php if($_main_controller == "DistributionCenters" || $_main_controller == "Stores"): ?>
		  <li <?php if ($_controller == "Lists"):?> class="current_subpage_item"<?php endif;?>><a href="<?php echo base_url()?><?php echo $_main_controller;?>/Lists">List</a></li>
          <li <?php if (($_controller == 'WasteInvoice') && ($_action == 'history')) :?>class="current_subpage_item"<?php endif; ?>><a href="<?php echo base_url();?>admin/WasteInvoice/history">Waste History</a></li>
          <li <?php if (($_controller == 'SupportRequest') && ($_action == 'history')) :?>class="current_subpage_item"<?php endif; ?>><a href="<?php echo base_url();?>admin/SupportRequest/history">Support History</a></li>
          <?php endif; ?>
        </ul>


	    <?php elseif (!isset($hideAdminMenu)): ?>
	    <ul class="group" id="subnavbar">
	          <li <?php if ($_action != 'history' && (( ($_controller == 'RecyclingInvoice')) || ($_controller == 'WasteInvoice') || ($_controller == 'SupportRequest') && (($_action == 'index') || ($_action == 'edit')))) :?>class="current_subpage_item"<?php endif; ?>><a href="<?php echo base_url();?>admin/SupportRequest/index">Admin Home</a></li>
	          <li <?php if (($_controller == 'DistributionCenters') || ($_controller == 'Accounts') || ($_controller == 'ManageCompany') || ($_controller == 'Vendors')) :?>class="current_subpage_item"<?php endif; ?>><a href="<?php echo base_url();?>admin/ManageCompany/Peoples" >Manage Company</a></li>
	          <li <?php if (($_controller == 'SupportRequest') && ($_action == 'history')) :?>class="current_subpage_item"<?php endif; ?>><a href="<?php echo base_url();?>admin/SupportRequest/history">Support History</a></li>
	          <li <?php if (($_controller == 'WasteInvoice') && ($_action == 'history')) :?>class="current_subpage_item"<?php endif; ?>><a href="<?php echo base_url();?>admin/WasteInvoice/history">Waste History</a></li>
	          <li <?php if (($_controller == 'RecyclingInvoice') && ($_action == 'history')):?>class="current_subpage_item"<?php endif;?>><a href="<?php echo base_url();?>admin/RecyclingInvoice/history">Recycling History</a></li>
	          <li><a href="<?php echo base_url()?>admin/Reports">Reports</a></li>
	    </ul>
	    <?php endif; ?>
  	</div>
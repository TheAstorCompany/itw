<ul>
    <li <?php if ($_action == 'Peoples'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/ManageCompany/Peoples">People</a></li>
    <li <?php if ($_action == 'Vendors'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/ManageCompany/Vendors">Vendors</a></li>
    <li <?php if ($_action == 'Stores'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/ManageCompany/Stores">North Mississippi Health Services Sites</a></li>
	<!-- <li <?php if ($_action == 'DC'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/ManageCompany/DC">Penn Medicine Sites</a></li> -->
</ul>

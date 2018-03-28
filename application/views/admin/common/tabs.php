<ul>
    <li <?php if ($_controller == 'SupportRequest'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/SupportRequest/index">Enter Support Request</a></li>
    <li <?php if ($_controller == 'WasteInvoice'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/WasteInvoice/Add">Enter N. Miss. Invoice</a></li>
	<!-- <li <?php if ($_controller == 'DCInvoice'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/DCInvoice/Add">Enter Penn Medicine Invoice</a></li>
 -->    
 	<li <?php if ($_controller == 'RecyclingInvoice'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/RecyclingInvoice/AddEdit">Enter Recycling PO</a></li>
</ul>

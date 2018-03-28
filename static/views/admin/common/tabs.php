<ul>
    <li <?php if ($_controller == 'SupportRequest'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/SupportRequest/index">Enter Support Request</a></li>
    <li <?php if ($_controller == 'WasteInvoice'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/WasteInvoice/Add">Enter Waste Invoice</a></li>
    <li <?php if ($_controller == 'RecyclingInvoice'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/RecyclingInvoice/AddEdit">Enter Recycling Purchase Order</a></li>
	<li <?php if ($_controller == 'RecyclingCharges'):?>id="selected_tab"<?php endif; ?>><a href="<?php echo base_url();?>admin/RecyclingCharges/AddEdit">Enter Recycling Charges</a></li>
</ul>
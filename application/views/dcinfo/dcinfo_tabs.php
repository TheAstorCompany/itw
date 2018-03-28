<ul>
    <!--
    <li<?php echo ($current_tab=='Waste' ? ' id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'Dcinfo/Waste/' . $id; ?>'">Waste</a></li>
    <li<?php echo ($current_tab=='Recycling' ? ' id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'Dcinfo/Recycling/' . $id; ?>">Recycling</a></li>
    <li<?php echo ($current_tab=='CostSavings' ? ' id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'Dcinfo/CostSavings/' . $id; ?>">Cost/Savings</a></li>
    -->
    <li<?php echo ($current_tab=='Invoices' ? ' id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'Dcinfo/Invoices/' . $id; ?>">Invoices</a></li>
    <li<?php echo ($current_tab=='Rebates' ? ' id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'Dcinfo/Rebates/' . $id; ?>">Rebates</a></li>
    <li<?php echo ($current_tab=='SupportRequests' ? ' id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'Dcinfo/SupportRequests/' . $id; ?>">Support Requests</a></li>
    <li<?php echo ($current_tab=='SiteInfo' ? ' id="selected_tab"' : ''); ?>><a href="<?php echo base_url() . 'Dcinfo/SiteInfo/' . $id; ?>">Site Info</a></li>
</ul>
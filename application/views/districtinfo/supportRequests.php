<?php
include("application/views/admin/common/header.php");
include("application/views/admin/common/top_menu.php");


echo '
<div class="content">
	<div class="row">
		<div class="two columns">
			<a href="javascript:javascript:history.go(-1)" class="button">&lt;- Go back</a>
		</div>
	</div>
	<div class="row">
		<div class="four columns">
			<h1>' . $name . '</h1>
		</div>
		<div class="two columns">
			Square Footage<h5>' . $storesSqft . ' sqft</h5>
		</div>
		<div class="two columns">
			Diversion Rate<h5>' . $DiversionRate . '%</h5>
		</div>
		<div class="eight columns omega">
		</div>
	</div>

	<div class="row">
		<div class="sixteen columns">
			<div id="tabs" style="border:0px;">
				<ul>
					<li><a href="' . base_url() . 'Districtinfo/Waste/' . $id . '">Waste</a></li>
					<li><a href="' . base_url() . 'Districtinfo/Recycling/' . $id . '">Recycling</a></li>
					<li><a href="' . base_url() . 'Districtinfo/CostSavings/' . $id . '">Cost/Savings</a></li>
					<li><a href="' . base_url() . 'Districtinfo/Invoices/' . $id . '">Invoices</a></li>
					<li id="selected_tab"><a href="' . base_url() . 'Districtinfo/SupportRequests/' . $id . '">Support Requests</a></li>
					<li><a href="' . base_url() . 'Districtinfo/Services/' . $id . '">Services</a></li>
					<li><a href="' . base_url() . 'Districtinfo/Locations/' . $id . '">Locations</a></li>
				</ul>

				<div style="border:2px solid #7ABF53; padding: 10px;">';

if(!empty($support_requests['rows']))
{
	echo '
					<span style="float:right">
						<a href="' . base_url() . 'Districtinfo/SupportRequests/' . $id . '?print=1" class="button" target="_blank">Printer Friendly</a> 
						<a href="' . base_url() . 'Districtinfo/SupportRequests/' . $id . '?export=1" class="button">Export CSV</a>
					</span>

					<table cellpadding="0" cellspacing="0" border="0" class="display" id="data_tbl" width="100%">
						<thead>
							<tr>
								<th>Region</th>
								<th>District</th>
								<th>Location</th>
								<th>Service#</th>
								<th>Date</th>
								<th>Contact</th>
								<th>Phone#</th>
								<th>Description</th>
								<th>Resolved</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Region</th>
								<th>District</th>
								<th>Location</th>
								<th>Service#</th>
								<th>Date</th>
								<th>Contact</th>
								<th>Phone#</th>
								<th>Description</th>
								<th>Resolved</th>
							</tr>
						</tfoot>
					</table>

					<script type="text/javascript">
						$(function() {
							$("#data_tbl").dataTable({
								"sPaginationType"	: "full_numbers",
								"bProcessing"		: true,
								"bServerSide"		: true,
								"bStateSave"		: false,
								"oSearch"			: {"sSearch": "incomplete"},
								"bFilter"			: false,
								"bLengthChange"		: false,
								"sAjaxSource"		: "' . base_url() . 'Districtinfo/ajax_SupportRequests/' . $id . '"
							});
						});
					</script>

					<br style="clear: both;" />';
}

echo '

				</div>
			</div>
		</div>
	</div>
</div>';

include("application/views/admin/common/footer.php");



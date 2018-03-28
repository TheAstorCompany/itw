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
			<h1>' . $name . ' Region</h1>
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
	</div>

	<div class="row">
		<div class="sixteen columns">
			<div id="tabs" style="border:0px;">
				<ul>
					<li><a href="' . base_url() . 'Regioninfo/Waste/' . $name . '">Waste</a></li>
					<li><a href="' . base_url() . 'Regioninfo/Recycling/' . $name . '">Recycling</a></li>
					<li><a href="' . base_url() . 'Regioninfo/CostSavings/' . $name . '">Cost/Savings</a></li>
					<li><a href="' . base_url() . 'Regioninfo/Invoices/' . $name . '">Invoices</a></li>
					<li><a href="' . base_url() . 'Regioninfo/SupportRequests/' . $name . '">Support Requests</a></li>
					<li><a href="' . base_url() . 'Regioninfo/Services/' . $name . '">Services</a></li>
					<li id="selected_tab"><a href="' . base_url() . 'Regioninfo/Locations/' . $name . '">Locations</a></li>
				</ul>

				<div style="border:2px solid #7ABF53; padding: 10px;">';


	echo '
					<span style="float:right">
						<a href="' . base_url() . 'Regioninfo/Locations/' . $name . '?print=1" class="button" target="_blank">Printer Friendly</a> 
						<a href="' . base_url() . 'Regioninfo/Locations/' . $name . '?export=1" class="button">Export CSV</a>
					</span>

					<table cellpadding="0" cellspacing="0" border="0" class="display" id="invoices_tbl" width="100%">
						<thead>
							<tr>
								<th>Region</th>
								<th>District</th>
								<th>Location</th>
								<th>Address</th>
								<th>City</th>
								<th>State</th>
								<th>Zip</th>
								<th>24H</th>
								<th>SqFt</th>
								<th>Diversion</th>
								<th>Cost/Sqft</th>
								<th>Last Updated</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Region</th>
								<th>District</th>
								<th>Location</th>
								<th>Address</th>
								<th>City</th>
								<th>State</th>
								<th>Zip</th>
								<th>24H</th>
								<th>SqFt</th>
								<th>Diversion</th>
								<th>Cost/Sqft</th>
								<th>Last Updated</th>
							</tr>
						</tfoot>
					</table>

					<script type="text/javascript">
						$(function() {
							$("#invoices_tbl").dataTable({
								"sPaginationType"	: "full_numbers",
								"bProcessing"		: true,
								"bServerSide"		: true,
								"bStateSave"		: false,
								"oSearch"			: {"sSearch": "incomplete"},
								"bFilter"			: false,
								"bLengthChange"		: false,
								"sAjaxSource"		: "' . base_url() . 'Regioninfo/ajax_Locations/' . $name . '"
							});
						});
					</script>

					<br style="clear: both;" />';


echo '

				</div>
			</div>
		</div>
	</div>
</div>';

include("application/views/admin/common/footer.php");



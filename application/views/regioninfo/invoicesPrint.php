<?php 
include("application/views/admin/common/header_print.php");

echo '
<div class="row" style="padding-top: 50px">
	<div class="sixteen columns">

		<h1>Invoices</h1>

		<h5 style="float:left;padding-right:10px">
			<span>Store Name: </span>' . $name . '
		</h5>

		<hr />';


if(!empty($inv['rows']))
{
	echo '
					<table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
					  <thead>
						<tr>
							<th>Region</th>
							<th>District</th>
							<th>Location</th>
							<th>PO#</th>
							<th>Invoice Date</th>
							' . (($_isAdmin == 1) ? '<th>Sent Date</th>' : '') . '
							<th>Vendor</th>
							<th>Material</th>
							<th>Qty</th>
							<th>Total Rebate</th>
							<th>Total</th>
						</tr>
					  </thead>
					  <tbody>';

	$i = 1;
	while (list($k,$v) = each($inv['rows']))
	{
		echo '
						<tr class="' . (($i%2) ? 'even' : 'odd') . ' gradeA">
							<td>' . $v['region'] . '</td>
							<td>' . $v['district'] . '</td>
							<td>' . $v['location'] . '</td>
							<td>' . $v['invoice_num'] . '</td>
							<td>' . $v['invoice_date'] . '</td>
							' . (($_isAdmin == 1) ? '<td>' . $v['sent_date'] . '</td>' : '') . '
							<td>' . $v['vendor'] . '</td>
							<td>' . $v['material'] . '</td>
							<td>' . $v['quantity'] . '</td>
							<td>' . $v['total_rebate'] . '</td>
							<td>' . $v['total'] . '</td>
						</tr>';
		$i++;
	}

	echo '
					  </tbody>
					  <tfoot>
						<tr>
							<th>Region</th>
							<th>District</th>
							<th>Location</th>
							<th>PO#</th>
							<th>Invoice Date</th>
							' . (($_isAdmin == 1) ? '<th>Sent Date</th>' : '') . '
							<th>Vendor</th>
							<th>Material</th>
							<th>Qty</th>
							<th>Total Rebate</th>
							<th>Total</th>
						</tr>
					  </tfoot>
					</table>';
}



include("application/views/admin/common/footer.php");


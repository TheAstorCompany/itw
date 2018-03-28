<?php 
include("application/views/admin/common/header_print.php");

echo '
<div class="row" style="padding-top: 50px">
	<div class="sixteen columns">

		<h1>Support Requests</h1>

		<h5 style="float:left;padding-right:10px">
			<span>Store Name: </span>' . $DCData['name'] . '
		</h5>

		<hr />';


if(!empty($support_requests['rows']))
{
	echo '
					<table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
					  <thead>
						<tr>
							<th>Location</th>
							<th>Service</th>
							<th>Date</th>
							<th>Contact</th>
							<th>Phone</th>
							<th>Description</th>
							<th>Resolved</th>
						</tr>
					  </thead>
					  <tbody>';

	$i = 1;
	while (list($k,$v) = each($support_requests['rows']))
	{
		echo '
						<tr class="' . (($i%2) ? 'even' : 'odd') . ' ' . (($v['complete'] == 1) ? 'gradeA' : 'gradeX' ) . '">
							<td>' . $v['location'] . '</td>
							<td>' . $v['service_id'] . '</td>
							<td>' . $v['r_date'] . '</td>
							<td>' . $v['contact'] . '</td>
							<td>' . $v['phone'] . '</td>
							<td>' . $v['description'] . '</td>
							<td>' . $v['complete_word'] . '</td>
						</tr>';
		$i++;
	}

	echo '
					  </tbody>
					  <tfoot>
						<tr>
							<th>Location</th>
							<th>Service</th>
							<th>Date</th>
							<th>Contact</th>
							<th>Phone</th>
							<th>Description</th>
							<th>Resolved</th>
						</tr>
					  </tfoot>
					</table>';
}



include("application/views/admin/common/footer.php");


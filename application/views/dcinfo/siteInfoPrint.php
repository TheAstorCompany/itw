<?php 
include("application/views/admin/common/header_print.php");

echo '
<div class="row" style="padding-top: 50px">
	<div class="sixteen columns">

		<h1>Site Info</h1>

		<h5 style="float:left;padding-right:10px">
			<span>Distribution Center: </span>' . $DCData['name'] . '
		</h5>

		<hr />';


if(!empty($vendors['rows']))
{
	echo '
					<table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
					  <thead>
						<tr>
							<th>Location</th>
							<th>SqFt</th>
							<th>Vendor</th>
							<th>Service Type</th>
							<th>Container Type</th>
							<th>Container Size</th>
							<th>Duration</th>
							<th>Frequency</th>
							<th>Cost</th>
							<th>Last Updated</th>
						</tr>
					  </thead>
					  <tbody>';

	$i = 1;
	while (list($k,$v) = each($vendors['rows']))
	{
		echo '
						<tr class="' . (($i%2) ? 'even' : 'odd') . ' gradeA">
							<td>' . $v['location'] . '</td>
							<td>' . $v['DC_squareFootage'] . '</td>
							<td>' . $v['vendor_name'] . '</td>
							<td>' . $v['service_type'] . '</td>
							<td>' . $v['container_type'] . '</td>
							<td>' . $v['container_size'] . '</td>
							<td>' . $v['duration'] . '</td>
							<td>' . $v['frequency'] . '</td>
							<td>' . $v['cost'] . '</td>
							<td>' . $v['last_updated'] . '</td>
						</tr>';
		$i++;
	}

	echo '
					  </tbody>
					  <tfoot>
						<tr>
							<th>Location</th>
							<th>SqFt</th>
							<th>Vendor</th>
							<th>Service Type</th>
							<th>Container Type</th>
							<th>Container Size</th>
							<th>Duration</th>
							<th>Frequency</th>
							<th>Cost</th>
							<th>Last Updated</th>
						</tr>
					  </tfoot>
					</table>';
}



include("application/views/admin/common/footer.php");


<?php 
include("application/views/admin/common/header_print.php");

echo '
<div class="row" style="padding-top: 50px">
	<div class="sixteen columns">

		<h1>Site Info</h1>

		<h5 style="float:left;padding-right:10px">
			<span>District: </span>' . $name . '
		</h5>

		<hr />';


	echo '
					<table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
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
						<tbody>';

	$i = 1;
	while (list($k,$v) = each($locations['rows']))
	{
		echo '
						<tr class="' . (($i%2) ? 'even' : 'odd') . ' gradeA">
							<td>' . $v['region'] . '</td>
							<td>' . $v['district'] . '</td>
							<td>' . $v['location'] . '</td>
							<td>' . $v['address'] . '</td>
							<td>' . $v['city'] . '</td>
							<td>' . $v['state'] . '</td>
							<td>' . $v['zip'] . '</td>
							<td>' . $v['24H'] . '</td>
							<td>' . $v['squareFootage'] . '</td>
							<td>' . $v['diversion'] . '%</td>
							<td>$' . $v['CostSqft'] . '</td>
							<td>' . $v['last_updated'] . '</td>
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
					</table>';




include("application/views/admin/common/footer.php");


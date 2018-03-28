<?php 
include("application/views/admin/common/header_print.php");

echo '
<div class="row" style="padding-top: 50px">
	<div class="sixteen columns">

		<h1>Site Info</h1>

		<h5 style="float:left;padding-right:10px">
			<span>Name: </span>' . $name . '
		</h5>

		<hr />';


if(!empty($services['rows']))
{
	echo '
					<table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
					  <thead>
						<tr>
								<th>Region</th>
								<th>District</th>
								<th>Location</th>
								<th>SqFt</th>
								<th>24H</th>
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
	while (list($k,$v) = each($services['rows']))
	{
		echo '
						<tr class="' . (($i%2) ? 'even' : 'odd') . ' gradeA">
							<td>' . $v['region'] . '</td>
							<td>' . $v['district'] . '</td>
							<td>' . $v['location'] . '</td>
							<td>' . $v['squareFootage'] . '</td>
							<td>' . $v['24H'] . '</td>
							<td>' . $v['container_type'] . '</td>
							<td>' . $v['container_size'] . '</td>
							<td>' . $v['duration'] . '</td>
							<td>' . $v['frequency'] . '</td>
							<td>' . $v['total'] . '</td>
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
								<th>SqFt</th>
								<th>24H</th>
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


<?php
	if(!empty($Contacts)) {
?>
			<table width="100%" border="0" cellspacing="10" cellpadding="20">
				<tr>
					<td>Contact</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
<?php
	while (list($k,$v) = each($Contacts)) {
		echo '
				<tr style="border-bottom:1px solid #ddd">
					<td><h5>' . $v['firstName'] . ' ' . $v['lastName'] . '</h5></td>
					<td><h5>' . $v['title'] . '</h5></td>
					<td><h5>' . $v['phone'] . '</h5></td>
					<td><h5>' . $v['email_lnk'] . '</h5></td>
				</tr>';
	}
?>
			</table>
<?php
	}
?>

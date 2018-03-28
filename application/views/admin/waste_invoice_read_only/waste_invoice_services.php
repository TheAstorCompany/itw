<?php if (!isset($inc)): ?>
<div style="color:red">
<?php echo validation_errors(); ?>
</div>
<?php endif; ?>
<br/>
<strong>Services</strong>
<ol>
<?php
	$servicesTotal = 0;
	
	if (isset($data->items->services) && is_array($data->items->services)) {
		foreach ($data->items->services as $k=>$service) {
			echo '<li><strong>';
			echo ($service->category == '0' ? 'Waste':'Recycling');
			echo '&nbsp;$' . number_format($service->quantity * $service->rate, 2) . '&nbsp;&nbsp;&#8226;&nbsp;';
			echo '</strong>';
			echo $service->quantity . ' - ';
			echo $data->containerOptions[$service->containerId] . '&nbsp;&#8226;&nbsp;';
			if ($service->durationId == 1) {
				echo 'Scheduled&nbsp;';
			}
			echo '</li>';
			
			$servicesTotal += $service->quantity * $service->rate;
		}
	}
?>
</ol>
<strong>Fees</strong>
<ol>
<?php
	$feesTotal = 0;
	
	if (isset($data->items->fees) && is_array($data->items->fees)) {
		foreach ($data->items->fees as $k=>$fee) {
			echo '<li><strong>';
			echo $data->feeOptions[$fee->feeType];
			echo '</strong>';
			echo ' - $' . $fee->feeAmount;
			echo ($fee->waived ? ' (Waived) ':'');
			echo '&nbsp;&nbsp;';
			echo '</li>';
			
			if (!$fee->waived) {
				$feesTotal += $fee->feeAmount;
			}
		}
	}
?>
</ol>
<h6 class="highlight">Total $<?php echo number_format($servicesTotal + $feesTotal, 2);?></h6>
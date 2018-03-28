<?php if (!isset($inc)): ?>
<div style="color:red">
<?php echo validation_errors(); ?>
</div>
<?php endif; ?>
<strong>Services</strong>
<?php

    if(!is_array($data->items->fees)) {
        $data->items->fees = array();
    }

    $servicesTotal = 0;

    if(count($data->items->fees)>0)
        foreach($data->items->fees as $k=>$fee) {
            if(isset($fee->fromService)) {
                unset($data->items->fees[$k]);
            }
        }


	if (isset($data->items->services) && is_array($data->items->services)) {
		echo '<ol>';

    foreach ($data->items->services as $k=>$service) {
			echo '<li service_k="'.$k.'">';
            foreach($service as $prop=>$val) {
                if(!is_array($val)) {
                    echo '<input type="hidden" name="existing_services['.$k.']['.$prop.']" value="'.$val.'" />'."\n";
                }
            }
            echo '<strong>';
			echo ($service->category == '0' ? 'Waste':'Recycling');
			echo '&nbsp;$' . number_format($service->rate, 2) . '&nbsp;&nbsp;&#8226;&nbsp;';
			echo '</strong>';
			echo $service->quantity . ' - ';
			if(isset($data->containerOptions[$service->containerId]))
			    echo $data->containerOptions[$service->containerId] . '&nbsp;&#8226;&nbsp;';
			if ($service->durationId == 1) {
				echo 'Scheduled&nbsp;';
			}

            if(count($service->fees)>0) {
                foreach($service->fees as $fee) {
                    $objFee = new stdClass();
                    $objFee->feeType = $fee->feeType;
                    $objFee->feeAmount = $fee->feeAmount;
                    $objFee->waived = $fee->waived;
                    $objFee->fromService = true;
                    $data->items->fees[] = $objFee;
                }
            }

			$servicesTotal += $service->rate;
            echo '<a href="javascript:void(0)" onclick="deleteService('.$k.');">Delete</a>';
            echo '</li>';
		}
		echo '</ol>';
	}
?>

<strong>Fees</strong>

<?php
	$feesTotal = 0;

	if (count($data->items->fees)>0) {
		echo '<ol>';
		foreach ($data->items->fees as $k=>$fee) {
            echo '<li fee_k="'.$k.'">';
            foreach($fee as $prop=>$val) {
                echo '<input type="hidden" name="existing_fees['.$k.']['.$prop.']" value="'.$val.'" />'."\n";
            }
            echo '<strong>';
			echo $data->feeOptions[$fee->feeType];
			echo '</strong>';
			echo ' - $' . $fee->feeAmount;
			echo ($fee->waived ? ' (Waived) ':'');
			echo '&nbsp;&nbsp;';
			echo '<a href="javascript:void(0)" onclick="deleteFee('.$k.');">Delete</a>';
			echo '</li>';
			
			if (!$fee->waived) {
				$feesTotal += $fee->feeAmount;
			}
		}
		echo '</ol>';
	}
?>

<h6 class="highlight">Total $<?php echo number_format($servicesTotal + $feesTotal, 2);?></h6>
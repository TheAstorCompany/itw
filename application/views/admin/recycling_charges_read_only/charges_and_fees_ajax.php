				<?php if ((isset($charges) && count($charges) > 0) ||  (isset($fees) && count($fees) > 0 )) {?>				
					<?php if (isset($charges) && count($charges) > 0) { ?>
					<strong>Charges</strong>                	
                	<ol>
                		<?php foreach($charges as $index=>$charge) { ?>
                		<li><strong><?php 
                		if (isset($data->materialOptions[$charge->materialId])) {
                			echo $data->materialOptions[$charge->materialId];
                		} 
                		?> - $<?php echo number_format($charge->pricePerTon);?>&nbsp;&nbsp;&#8226;&nbsp;&nbsp;<?php echo number_format($charge->quantity);?> <?php echo $data->unitOptions[$charge->unitId]; ?></strong><strong>&nbsp;&nbsp;-&nbsp;</strong><strong>&nbsp;&nbsp;</strong><?php echo ($charge->releaseNumber)?'Release: '. $charge->releaseNumber . '&nbsp;&nbsp;':'';?><?php echo ($charge->CBRENumber)?'CBRE: '. $charge->CBRENumber . '&nbsp;&nbsp;':'';?><a href="JavaScript:void(0)" onclick="deleteCharge(<?php echo $index; ?>)">Delete</a><br><?php echo $charge->materialDate;?><?php echo ($charge->description)?', '. $charge->description:'';?></li>
                      	<?php } ?>
              		</ol>
              		<?php }
					if (isset($fees) && count($fees) > 0) { ?>
                	<strong>Fees</strong>
                	<ol>
                		<?php foreach($fees as $index=>$fee) {?>                		                		
                		<li><strong><?php 
                		if (isset($data->feeOptions[$fee->feeType])) {
                			echo $data->feeOptions[$fee->feeType];
                		} ?>&nbsp;&nbsp;-&nbsp;&nbsp;<strong>$<?php echo number_format($fee->fee);?></strong></strong> &nbsp;&nbsp; <a href="JavaScript:void(0)" onclick="deleteFee('<?php echo $index;?>')">Delete</a></li>                		
                		<?php } ?>
              		</ol>
                  	<?php } ?>
                  	<?php if (isset($total)) {?><h6 class="highlight">Total $<?php echo number_format($total) ?> </h6><?php } ?>
                <?php } ?>
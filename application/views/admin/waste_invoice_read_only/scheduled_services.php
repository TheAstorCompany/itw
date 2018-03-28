<div style="display:none;" id="scheduled_services">
	<?php 
		if (!property_exists($data, '0')) {
			echo '<h1>Sorry, no scheduled services available.</h1>';
		}
	?>
	<ol style="padding-top: 20px;">
	<?php
		$this->load->helper('dates');
		
		foreach ($data as $k=>$item):
			$hideDates = false;
			
			if ( ($item->startDate == '0000-00-00') && ($item->endDate == '0000-00-00')) {
				$hideDates = true;
			}
	?>
		<li id="id_li_<?php echo $item->id; ?>" style="height:60px;">
			<table width="80%" style="margin-top: -26px;">
				<tr>
					<td width="50%">
						<?php
							echo '<strong>';
							if ($item->category == 0) {
								echo 'Waste';
							} else {
								echo 'Recycling';
							}
							echo '</strong>';
							$total = number_format($item->quantity * $item->rate, 2);
							echo '&nbsp;$'.$total . ', '.$item->quantity;
							echo '&nbsp;' . $containers[$item->containerId];
							
							if (!$hideDates) {
								echo '&nbsp;,' . SQLToUSDate($item->startDate) . ' - ' . SQLToUSDate($item->endDate);
							} 
						?>
					</td>
					<td width="10%">
						<form action="<?php echo base_url();?>admin/WasteInvoice/services?type=service" method="post">
							<?php
								foreach ($item as $name=>$value) {
									echo '<input type="hidden" name="'.$name.'" value="'.$value.'" />'.PHP_EOL;
								} 
							?>
							<input type="hidden" name="serviceTypeId" value="<?php echo $item->durationId; ?>" />
							<button>Select</button>
						</form>
					</td>
				</tr>
			</table>
		</li>
	<?php endforeach;?>
	</ol>
</div>
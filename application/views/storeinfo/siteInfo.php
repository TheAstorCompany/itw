<?php
	$selected_tab_id = 'SiteInfo';
	include 'header.php';
?>

<div class="row">
	<div class="eight columns">
		<fieldset class="dataentry">
			<label for="type">Last Updated</label>
			<?php echo date("m/d/Y h:ia", strtotime($data->lastUpdated));?><br>
			<br />

			<label for="location">Location#</label>
			<input name="location" type="text" value="<?php echo set_value('location', $data->location);?>">
			
			<label for="squareFootage">Square Footage</label>
			<input name="squareFootage" type="text" value="<?php echo set_value('squareFootage', $data->squareFootage);?>">
			
			<label for="open24hours">24Hours?</label>
			<input name="open24hours" type="radio" value="0" <?php if (set_value('open24hours', $data->open24hours) == '0'):?>checked="checked"<?php endif; ?>>
			N
			<input name="open24hours" type="radio" value="1" <?php if (set_value('open24hours', $data->open24hours) == '1'):?>checked="checked"<?php endif; ?>>
			Y<br />
			<br />

			<label for="officeLocation">Office Location?</label>
			<input name="officeLocation" type="radio" value="0" <?php if (set_value('officeLocation', $data->officeLocation) == '0'):?>checked="checked"<?php endif; ?>>
			N
			<input name="officeLocation" type="radio" value="1" <?php if (set_value('officeLocation', $data->officeLocation) == '1'):?>checked="checked"<?php endif; ?>>
			Y<br />
			<br />

			<label for="district">District#</label>
			<input name="district" id="district" type="text" value="<?php echo set_value('district', $data->district);?>">

			<label for="districtName">District Name</label>
			<input name="districtName" id="districtName" type="text" readonly="readonly" value="<?php echo set_value('districtName', $data->districtName);?>">

			<label for="franchise">Franchise?</label>
			<input name="franchise" type="radio" value="0" <?php if (set_value('franchise', $data->franchise) == '0'):?>checked="checked"<?php endif; ?>>
			N
			<input name="franchise" type="radio" value="1" <?php if (set_value('franchise', $data->franchise) == '1'):?>checked="checked"<?php endif; ?>>
			Y<br />
			<br />

			<label for="salesRanking">Sales Ranking</label>
			<input name="salesRanking" type="text" value="<?php echo set_value('salesRanking', $data->salesRanking);?>">

			<label for="addressLine1">Address</label>
			<input name="addressLine1" type="text" value="<?php echo set_value('addressLine1', $data->addressLine1);?>">

			<label for="addressLine2">Address #2</label>
			<input name="addressLine2" type="text" value="<?php echo set_value('addressLine2', $data->addressLine2);?>">

			<label for="county">County</label>
			<input name="county" type="text" value="<?php echo set_value('county', $data->county);?>">

			<label for="city">City</label>
			<input name="city" type="text" value="<?php echo set_value('city', $data->city);?>">

			<label for="stateId">State</label>
			<?php echo form_dropdown('stateId', $data->statesOptions, set_value('stateId', $data->stateId));?>
			<label for="postCode">Zip Code</label>
			<input type="text" name="postCode" value="<?php echo set_value('postCode', $data->postCode);?>" />

			<label for="phone">Phone</label>
			<input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>">

			<label for="fax">Fax</label>
			<input name="fax" type="text" value="<?php echo set_value('phone', $data->phone);?>">
		</fieldset>
	</div>
	<div class="seven columns">
	    <div id="tabs" style="border:0px;">
			<div class="dataentry" id="servicesContainer">
				<strong>Services</strong>
				<ol>
				<?php
				foreach($data->services as $item) {
					echo '<li>';
					echo sprintf("%s %s • %d x %s • %s • %s for %s $%s", 
							strtoupper($item->vendorName),
							$item->vendorPhone,
							$item->quantity,
							isset($data->containerOptions[$item->containerId]) ? $data->containerOptions[$item->containerId] : '&nbsp;',
							$data->scheduleOptions[$item->schedule],
							$item->daysname,
							$item->category==0 ? 'Watse' : 'Recycling',
							$item->rate
						);
					echo '</li>';
				}
				?>
				</ol>
			</div>
			<div class="dataentry" id="servicesContainerHistory">
				<strong>Service history</strong>
				<ol>
				<?php
				foreach($data->serviceHistory as $item) {
				  echo '<li>';
				  echo sprintf("%s %s • %d x %s • %s • %s for $%s", 
						  strtoupper($item->vendorName),
						  $item->vendorPhone,
						  $item->quantity,
						  isset($data->containerOptions[$item->containerId]) ? $data->containerOptions[$item->containerId] : '&nbsp;',
						  $data->scheduleOptions[$item->schedule],
						  $item->daysname,
						  $item->rate
					  );
				  echo '</li>';
				}
				?>
				</ol>
			</div>
		</div>
	</div>
</div>

<?php
	include 'footer.php';
?>



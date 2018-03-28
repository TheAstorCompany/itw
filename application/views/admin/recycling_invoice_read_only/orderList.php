<?php if(isset($data->orderSum["materials"]) && count($data->orderSum["materials"])) {?>
<div class="dataentry">
		<strong>Materials</strong>
              <ol>
              <?php foreach($data->orderSum["materials"] as $key=>$temp) {?>
              
                <li> <?php
	                $qty = !empty($temp['quantity']) ? $temp['quantity'] : '1';
	                 
	                if(isset($data->allMaterials[$temp["materialId"]])) { 
	                	echo $data->allMaterials[$temp["materialId"]]?> - $<?php 
	                } 
	                echo number_format($temp["pricePerUnit"] * $qty, 2);
	                echo '&nbsp;&#8226;&nbsp;<strong>' . $qty . ' Tons @ $' . number_format($temp["pricePerUnit"], 2) . '</strong>';
                ?>&nbsp; </li>
               <?php }?>
              </ol>
    <?php if(isset($data->invoiceSum["fees"]) && count($data->invoiceSum["fees"])) {?>
                  <strong>Fees</strong>
                  <ol id="feesOrderList">
                  <?php foreach($data->invoiceSum["fees"] as $key=>$temp) {?>
                <li><strong><?php echo $data->allFees[$temp["feeType"]] ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $temp["fee"]?> </strong> <?php if ($temp["waived"]) echo "(Waived)";?>    &nbsp;<em>&nbsp;</em></li>
                <?php }?>
              </ol>
    <?php }?>
     <h6 class="highlight">Total $<?php echo number_format($data->orderSum["allPrice"],2); ?> </h6>
</div>
<?php }?>                  
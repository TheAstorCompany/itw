<?php if((isset($data->invoiceSum["materials"]) && count($data->invoiceSum["materials"])) || isset($data->invoiceSum["fees"]) && count($data->invoiceSum["fees"])) { ?>
<div class="dataentry">
	<?php if(isset($data->invoiceSum["materials"]) && count($data->invoiceSum["materials"])) {?>
		<strong>Materials</strong>
              <ol>
              <?php foreach($data->invoiceSum["materials"] as $key=>$temp) {?>
                <li> <?php
                if(isset($data->allMaterials[$temp["materialId"]])) 
                {echo $data->allMaterials[$temp["materialId"]];?> -$<?php } echo number_format($temp["quantity"]*$temp["pricePerUnit"], 2)?>&nbsp;&nbsp;&#8226;&nbsp;<strong>&nbsp;<?php echo $temp["quantity"]?> Tons</strong> @ <strong>$<?php echo number_format($temp["pricePerUnit"], 2)?></strong>&nbsp; <span style="cursor: pointer;" onclick="deleteInvoicePart(<?php echo $key?>, 'materials')">Delete</span></li>
               <?php }?>
              </ol>
    <?php }?>
    <?php if(isset($data->invoiceSum["fees"]) && count($data->invoiceSum["fees"])) {?>
                  <strong>Fees</strong>
                  <ol id="feesInvoiceList">
                  <?php foreach($data->invoiceSum["fees"] as $key=>$temp) {?>
                <li><strong><?php echo $data->allFees[$temp["feeType"]] ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $temp["fee"]?> </strong> <?php if ($temp["waived"]) echo "(Waived)";?>    &nbsp;<em>&nbsp;</em><span style="cursor: pointer;" onclick="deleteInvoicePart(<?php echo $key?>, 'fees')">Delete</span></li>
                <?php }?>
              </ol>
    <?php }?>
                  <h6 class="highlight">Total $<?php echo number_format($data->invoiceSum["allPrice"],2); ?> </h6>
</div>
<?php }?>
<input type="hidden" id="id_usedMaterials" value='<?php $usedMaterials = array();if (isset($data->invoiceSum["materials"])) { foreach($data->invoiceSum["materials"] as $key=>$temp) {	$usedMaterials[] = $temp["materialId"]; }} echo json_encode($usedMaterials); ?>' />

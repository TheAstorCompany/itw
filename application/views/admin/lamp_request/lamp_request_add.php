<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<?php
if (!function_exists('echoMaterialCharges')) {
    function echoMaterialCharges($itemOptions, $id,$isBlank=true, $invoiceItem = 0, $invoiceItemLampQuantity = '', $invoiceItemBoxQuantity = '')
    {
        $blank = $isBlank ? ' class="blankMaterial"' : '';
        echo '
        <tr'.$blank.'>
		    <td>' . form_dropdown('invoiceItems[]', $itemOptions, $invoiceItem, 'id=invoiceItems_' . $id . '') . '</td>
			<td><input type="text" placeholder="Lamp Quantity" name="invoiceItemsLampQuantity[]" id="invoiceItemsLampQuantity_' . $id . '" value="' . $invoiceItemLampQuantity . '" /></td>
			<td><input type="text" placeholder="Box Quantity" name="invoiceItemsBoxQuantity[]" id="invoiceItemsBoxQuantity_' . $id . '" value="' . $invoiceItemBoxQuantity . '" /></td>
		</tr>';
    }
}
?>
<script type="text/javascript">
$("body").on("change keydown",".dc-mc-table select, .dc-mc-table input",function(){
    $(this).closest('tr').removeClass('blankMaterial');
    if(! $(".dc-mc-table").find("tr").last().hasClass('blankMaterial') ){
        var num = $(".dc-mc-table select").length;
        var tr = $("<tr></tr>").addClass("blankMaterial");
        var select = $("<select></select>").attr("id","invoiceItems_"+num).attr("name","invoiceItems[]");
        var optionData = <?php echo json_encode($data->ItemOptions);?>;
        for(var i in optionData){
            if (!optionData.hasOwnProperty(i))
                continue;
            select.append($("<option></option>").val(i).html(optionData[i]));
        }
        tr.append($("<td></td>").append(select));

        tr.append($("<td></td>").append(
            $("<input>").attr("id","invoiceItemsLampQuantity_"+num).attr("name","invoiceItemsLampQuantity[]").attr("placeholder","Lamp Quantity").attr("type","text")
        ));
        tr.append($("<td></td>").append(
            $("<input>").attr("id","invoiceItemsBoxQuantity_"+num).attr("name","invoiceItemsBoxQuantity[]").attr("placeholder","Box Quantity").attr("type","text")
        ));

        $(this).closest('table').append(tr);
        $("#article").scrollTop($("#article")[0].scrollHeight);
    }
});
</script>
    <div class="content">
        <div class="row remove-bottom" style="margin-bottom:0;">
            <div class="sixteen columns">
                <h1><?php echo ($data->invoiceId > 0 ? 'Lamp Request #'.$data->invoiceId : 'Enter Lamp Request');?></h1>
            </div>
            <?php if($data->invoiceId > 0) { ?>
                <div class="row">
                    <div class="sixteen columns">
                        <a href="javascript: window.history.back();" class="button">&lt;- Go back</a>
                        <?php
                        if(!$data->is_readonly) {
                            ?>
                            <a href="<?php echo base_url();?>admin/LampRequest/Delete/<?php echo $data->id;?>" class="button">Delete</a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            <?php }?>
        </div>
        <div class="row" style="margin-bottom:0;">
            <div class="sixteen columns" style="width:100%;">
                <div id="tabs" style="border:0px;">
                    <?php
                    if($data->invoiceId==0) {
                        include("application/views/admin/common/tabs.php");
                    }
                    ?>
                    <div class="tab_enter_recycling_purchase" id="ui-tabs-2" style="border-top: 2px solid #7ABF53; padding-left: 0px; padding-right:0px;">
                        <br />
                        <span style="color:red;" id="id_errors"></span>
                        <span style="color:red;"><?php echo validation_errors(); ?></span>
                        <div class="sixteen columns alpha" style="width:100%;">
                            <?php echo form_open('', 'id="mainForm"');?>
                            <input type="hidden" id="save_type" name="save_type" value="1" />
                            <div style="white-space: nowrap;">
                                <div style="width: 630px; vertical-align: top; display: inline-block; float:left;">
                                    <div style="margin-bottom:10px;">
                                        <h6 class="dataentry">General Info</h6>
                                        <fieldset class="dataentry">
                                            <label for="locationName">Location Name or ID*</label>
                                            <input type="hidden" name="locationId" value="<?php echo set_value('locationId', $data->locationId) ?>" id="locationId" />
                                            <?php echo form_hidden('locationType', set_value('locationType', $data->locationType));?>
                                            <input name="locationName" id="locationName" type="text" value="<?php echo set_value('locationName', $data->locationName);?>" />

                                            <label for="address">Address</label>
                                            <input name="address" type="text" value="<?php echo set_value('address', $data->address);?>" />

                                            <label for="phone">Phone</label>
                                            <input name="phone" type="text" value="<?php echo set_value('phone', $data->phone);?>" />

                                            <label for="requestDate">Request Date</label>
                                            <input name="requestDate" type="text" value="<?php echo set_value('requestDate', $data->requestDate);?>" style="width:100px" />

                                            <label for="cbreNumber">CBRE#*</label>
                                            <input name="cbreNumber" type="text" value="<?php echo set_value('cbreNumber', $data->cbreNumber);?>" />

                                            <label for="invoiceNumber">Invoice#</label>
                                            <input name="invoiceNumber" type="text" value="<?php echo set_value('invoiceNumber', $data->invoiceNumber);?>" />

                                            <label for="bolDate">BOL Date</label>
                                            <input name="bolDate" type="text" value="<?php echo set_value('bolDate', $data->bolDate);?>" style="width:100px" />

                                            <label for="invoiceDate">Invoice Date</label>
                                            <input name="invoiceDate" type="text" value="<?php echo set_value('invoiceDate', $data->invoiceDate);?>" style="width:100px" />

                                        </fieldset>
                                    </div>
                                    <hr />
                                    <div style="margin-bottom: 10px; margin-top: 10px;">
                                        <h6 class="dataentry">Material Charges</h6>
                                        <table class="dc-mc-table">
                                            <tr>
                                                <th><label>Item</label></th>
                                                <th><label>Lamp Quantity</label></th>
                                                <th><label>Box Quantity</label></th>
                                            </tr>
                                            <?php
                                            $lastId=0;
                                            if(count($data->invoiceItems)>0)
                                                for($lastId=0;$lastId < count($data->invoiceItems);$lastId++)
                                                    echoMaterialCharges($data->ItemOptions,$lastId, false, $data->invoiceItems[$lastId],$data->invoiceItemsLampQuantity[$lastId],$data->invoiceItemsBoxQuantity[$lastId]);
                                            //adding blank one at the end
                                            echoMaterialCharges($data->ItemOptions,$lastId);
                                            ?>
                                        </table>

                                        <?php
                                            if(!empty($data->id)) {
                                                widget::run('audit_table', 'lamp_request', $data->id);
                                            }
                                        ?>
                                        <br />
                                        <hr />
                                        <br />
                                        <?php
                                        if(!$data->is_readonly) {
                                            ?>
                                            <div style="text-align: center;">
                                                <button style="background:#13602E; color:#fff" onclick="$('#save_type').val(1); document.getElementById('mainForm').submit();">Save</button>
                                                <button style="background:#13602E; color:#fff" onclick="$('#save_type').val(2); document.getElementById('mainForm').submit();">Save and New</button>
                                                <button style="background:#777; color:#fff" onclick="window.location = window.location; return false;">Cancel</button>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div style="min-height: 450px; width: 490px; border-left: 1px solid #DDD; padding: 0 10px; margin: 0 5px; vertical-align: top; display: inline-block;float:left;">
                                    <div>
                                        <input type="hidden" name="action" id="dataFormAction" value="" />
                                        <fieldset class="dataentry">
                                            <h6 class="dataentry">Astor Only</h6>
                                            <label for="status">Status</label>
                                            <?php echo form_dropdown('status', $data->statusOptions, set_value('status', $data->status))?>
                                            <label for="dateSent">Date Sent to Everlights</label>
                                            <input name="dateSent" id="dateSent" type="text" style="width:140px" value="<?php echo set_value('dateSent', $data->dateSent);?>" />
                                            <label for="internalNotes">Internal Notes</label>
                                            <textarea name="internalNotes" id="internalNotes" style="max-width:410px;width:410px;min-height:290px"><?php echo set_value('internalNotes', $data->internalNotes);?></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {
            $("input[name='invoiceDate'], input[name='requestDate'], input[name='bolDate'], input[name='dateSent']").datepicker({
                dateFormat: "mm/dd/yy",
                weekHeader: "W"
            });

            $("#locationName").autocomplete({
                source: "<?php echo base_url(); ?>admin/SupportRequest/autocompleteLocation",
                minLength: 2,
                select: function(e, ui) {
                    $("input[name='locationId']").val(ui.item.id);
                    $("input[name='locationType']").val(ui.item.type);
                    $("input[name='address']").val(ui.item.address);
                    $("input[name='phone']").val(ui.item.phone);
                    $.post("/admin/SupportRequest/checkLocation",{id:ui.item.id},function(data){
                            if(data!="0")
                                alert("This store has another lamp request within the last year");
                        }
                    );
                }
            });

            $('#locationName').change(function() {
                $("input[name='locationId']").val('');
            });
        });
    </script>


<?php include("application/views/admin/common/footer.php");?>
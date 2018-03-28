<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<div class="content">
    <div class="row">
        <div class="sixteen columns">
            <h1>Recycling PO History</h1>
        </div>
    </div>
    <div class="row">
        <div>
            <span style="float:right">
				<form id="fExport" action="<?php echo base_url();?>admin/RecyclingInvoice/csvList" method="get"></form>
			    <a class="button" href="javascript: void(0);" onclick="ExportToCSV();">Export to CSV</a>
		    </span>
        </div>
        <div style="display: none;" id="additionalFilter">
            Start Date<input type="text" id="invoiceDateStart" value="" style="width: 100px; display: inline-block;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            End Date<input type="text" id="invoiceDateEnd" value="" style="width: 100px; display: inline-block;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            Distribution Center<select name="distributionCenterId" id="distributionCenterId" style="display: inline-block;">
            <option value="">- All -</option>
            <?php
                foreach($data->distributionCenterIdOptions as $dc) {
                    echo '<option value="'.$dc['id'].'">'.$dc['name'].'</option>';
                }
            ?>
            </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            Status<select name="status" id="status" style="width: 100px; display: inline-block;">
                <option value="">- All -</option>
                <option value="YES">Complete</option>
                <option value="NO">Incomplete</option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        <div class="sixteen columns">
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="callslist" width="100%">
            <thead>
                <tr>
                    <th>ID#</th>
                    <th>PO#</th>
                    <th>Invoice Date</th>
                    <th>Location</th>
                    <th>Vendor</th>
                    <th>Material</th>
                    <th>Qty</th>
                    <th>PO Price</th>
                    <th>PO PPT</th>
                    <th>Invoice Price</th>
                    <th>Invoice PPT</th>
                    <th>Complete?</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>ID#</th>
                    <th>PO#</th>
                    <th>Invoice Date</th>
                    <th>Location</th>
                    <th>Vendor</th>
                    <th>Material</th>
                    <th>Qty</th>
                    <th>PO Price</th>
                    <th>PO PPT</th>
                    <th>Invoice Price</th>
                    <th>Invoice PPT</th>
                    <th>Complete?</th>
                </tr>
            </tfoot>
            </table>
        </div>
    </div>
</div>    
<script type="text/javascript">
    var oTable = null;
    var filterData = null;
    var oTotalRecords = 0;
    var filter_distributionCenterId = 0;
    var filter_invoiceDateStart = '';
    var filter_invoiceDateEnd = '';
    var filter_status = 0;
    $(function() {		
        oTable = $('#callslist').dataTable({
            "sDom": '<"#toolbar1">r<"#toolbar2"f>tp',
            "sPaginationType": "full_numbers",
            "iDisplayLength" : 100,
            "bProcessing": true,
            "bServerSide": true,
            "bPaginate": true,
            "bStateSave": true,
            "oSearch": {"sSearch": ""},
            "sAjaxSource": '<?php echo base_url();?>admin/RecyclingInvoice/ajaxList',
            "fnStateLoaded": function (oSettings, oData) {
                if(oSettings.oPreviousSearch.filter_distributionCenterId) {
                    filter_distributionCenterId = oSettings.oPreviousSearch.filter_distributionCenterId;
                }
                if(oSettings.oPreviousSearch.filter_invoiceDateStart) {
                    filter_invoiceDateStart = oSettings.oPreviousSearch.filter_invoiceDateStart;
                }
                if(oSettings.oPreviousSearch.filter_invoiceDateEnd) {
                    filter_invoiceDateEnd = oSettings.oPreviousSearch.filter_invoiceDateEnd;
                }
                if(oSettings.oPreviousSearch.filter_status) {
                    filter_status = oSettings.oPreviousSearch.filter_status;
                }
            },
            "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {

                aoData.push({ "name": "distributionCenterId", "value": filter_distributionCenterId});
                aoData.push({ "name": "invoiceDateStart", "value": filter_invoiceDateStart});
                aoData.push({ "name": "invoiceDateEnd", "value": filter_invoiceDateEnd});
                aoData.push({ "name": "status", "value": filter_status});

                oSettings.oPreviousSearch.filter_distributionCenterId = filter_distributionCenterId;
                oSettings.oPreviousSearch.filter_invoiceDateStart = filter_invoiceDateStart;
                oSettings.oPreviousSearch.filter_invoiceDateEnd = filter_invoiceDateEnd;
                oSettings.oPreviousSearch.filter_status = filter_status;

                filterData = aoData;

                $.getJSON( sSource, aoData, function (json) {
                    if(json.error == 'expired') {
                        alert('You session has timed out, click OK to return to the login screen');
                        document.location.href='<?php echo base_url();?>admin/Auth';
                    } else {
                        oTotalRecords = json.iTotalRecords;
                        fnCallback(json);
                    }
                });
            }
        });
        $('#callslist_filter').css({'width': '100%'});
        $('#callslist_filter').prepend($('#additionalFilter').html());
        $('#additionalFilter').remove();

        $('#invoiceDateStart').val(filter_invoiceDateStart);
        $('#invoiceDateEnd').val(filter_invoiceDateEnd);
        $('#distributionCenterId').val(filter_distributionCenterId);
        $('#status').val(filter_status);

        $('#invoiceDateStart,#invoiceDateEnd').datepicker({
            dateFormat: "mm/dd/yy",
            weekHeader: "W",
            onSelect: function( selectedDate ) {
                filter_invoiceDateStart = $('#invoiceDateStart').val();
                filter_invoiceDateEnd = $('#invoiceDateEnd').val();
                oTable.fnDraw();
            }
        });
        $('#distributionCenterId').change (
            function () {
                filter_distributionCenterId = $('#distributionCenterId').val();
                oTable.fnDraw();
            }
        );
        $('#status').change (
            function () {
                filter_status = $('#status').val();
                oTable.fnDraw();
            }
        );
    });
    
    function ExportToCSV() {
        var q = '';
        for(var i=0; i<filterData.length; i++) {
            var o = filterData[i];
            var v = o.value;
            if(o.name=='iDisplayStart') {
                v = 0;
            }
            if(o.name=='iDisplayLength') {
                v = oTotalRecords;
            }
            q += '<input type="hidden" name="' + o.name + '" value="' + v + '" />';
        }
        $('#fExport').html(q);
        $('#fExport').submit();
    }    
</script>
<?php include("application/views/admin/common/footer.php");?>
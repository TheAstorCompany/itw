<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

<div class="sixteen columns">
    <form method="post" action="" id="fExport">
        <input type="hidden" id="action" name="action" value="html" />
        <span style="float: left; padding-right: 10px;">
            <label>Start Date</label>
            <input type="text" style="width: 100px;" value="<?php echo set_value('startDate', date('m/d/Y', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")))); ?>" id="startDate" name="startDate" />
        </span>
        <span style="float: left; padding-right: 10px;">
            <label>End Date</label>
            <input type="text" style="width: 100px;" value="<?php echo set_value('endDate', date('m/d/Y')); ?>" id="endDate" name="endDate" />
        </span>
        <span style="float: left; padding-right: 10px;">
            <label>&nbsp;</label>
            <a onclick="ExportTo('html');" href="javascript: void(0);" class="button">Update</a>
        </span>        
        <span style="float:right">
            <a onclick="ExportTo('csv');" href="javascript: void(0);" class="button">Export CSV</a>
        </span>
    </form>
</div>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
    <thead>
        <tr>
            <th>User</th>
            <th>Type</th>
            <th style="text-align: right;">Adds</th>
            <th style="text-align: right;">Changes</th>
            <th style="text-align: right;">Unique Changes</th>
            <th style="text-align: right;">Errors</th>
            <th style="text-align: right;">Closed</th>
            <th style="text-align: right;">Deleted</th>
            <th style="text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
    <?php
	$total = array('add'=>0, 'change'=>0, 'unique_change'=>0, 'delete' => 0, 'errors' => 0, 'close'=>0, 'total'=>0);
        foreach($data->tracks as $track) {
            foreach($track as $k=>$v) {
                if(!isset($data->object_types[$k])) {
                    continue;
                } else {
                    $total['add'] += $v['add'];
                    $total['change'] += $v['change'];
                    $total['unique_change'] += $v['unique_change'];
                    $total['close'] += $v['close'];
					$total['delete'] += $v['delete'];
					$total['errors'] += $v['errors'];
                    $total['total'] += ($v['add'] + $v['unique_change'] + $v['close'] + $v['delete']);
		        }
    ?>
            <tr class="gradeX">
                <td><?php echo $track['username']; ?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $data->object_types[$k]; ?></td>
                <td style="text-align: right;"><?php echo $v['add']; ?></td>
                <td style="text-align: right;"><?php echo $v['change']; ?></td>
                <td style="text-align: right;"><?php echo $v['unique_change']; ?></td>
                <td style="text-align: right;"><?php echo $v['errors']; ?></td>
                <td style="text-align: right;"><?php echo $v['close']; ?></td>
                <td style="text-align: right;"><?php echo $v['delete']; ?></td>
                <td style="text-align: right;"><?php echo ($v['add'] + $v['unique_change'] + $v['close'] + $v['delete']); ?></td>
            </tr>    
    <?php
            }
        }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th>&nbsp;</th>
            <th style="text-align: right;">Total</th>
            <th style="text-align: right; padding: 3px 10px;"><?php echo $total['add']; ?></th>
            <th style="text-align: right; padding: 3px 10px;"><?php echo $total['change']; ?></th>
            <th style="text-align: right; padding: 3px 10px;"><?php echo $total['unique_change']; ?></th>
            <th style="text-align: right; padding: 3px 10px;"><?php echo $total['errors']; ?></th>
			<th style="text-align: right; padding: 3px 10px;"><?php echo $total['close']; ?></th>
			<th style="text-align: right; padding: 3px 10px;"><?php echo $total['delete']; ?></th>
            <th style="text-align: right; padding: 3px 10px;"><?php echo $total['total']; ?></th>
        </tr>	
    </tfoot>
</table>
<script>
    var oTable = null;
    $(document).ready(function() {
	var dates = $('#startDate, #endDate').datepicker({
            dateFormat: "mm/dd/yy",
            changeMonth: true,
            numberOfMonths: 1,
            onSelect: function( selectedDate ) {
                var option = this.id == "startDate" ? "minDate" : "maxDate",
                        instance = $( this ).data( "datepicker" ),
                        date = $.datepicker.parseDate(
                                instance.settings.dateFormat ||
                                $.datepicker._defaults.dateFormat,
                                selectedDate, instance.settings );
                dates.not( this ).datepicker( "option", option, date );
            }
	});
        
	oTable = $('#example').dataTable({
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": false,
            "bInfo": false,
            "fnDrawCallback": function ( oSettings ) {
                if ( oSettings.aiDisplay.length == 0 ) {
                    return;
                }

                var nTrs = $('#example tbody tr');
                var iColspan = nTrs[0].getElementsByTagName('td').length;
                var sLastGroup = "";
                for ( var i=0 ; i<nTrs.length ; i++ ) {
                    var iDisplayIndex = oSettings._iDisplayStart + i;
                    var sGroup = oSettings.aoData[ oSettings.aiDisplay[iDisplayIndex] ]._aData[0];
                    if ( sGroup != sLastGroup ) {
                        var nGroup = document.createElement( 'tr' );
                        var nCell = document.createElement( 'td' );
                        nCell.colSpan = iColspan;
                        nCell.className = "group";
                        nCell.innerHTML = sGroup;
                        nGroup.appendChild( nCell );
                        nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );
                        sLastGroup = sGroup;
                    }
                }
            },
            "aoColumnDefs": [
                { "bVisible": false, "aTargets": [ 0 ] }
            ],
            "aaSortingFixed": [[ 0, 'asc' ]],
            "aaSorting": [[ 1, 'asc' ]],
            "sDom": 'lfr<"giveHeight"t>ip'
	});
    } );
    
    function ExportTo(action) {
        $('#action').val(action);
        $('#fExport').submit();
    }    
</script> 
<?php include("application/views/admin/common/footer.php");?>
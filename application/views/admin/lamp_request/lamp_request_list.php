<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<style type="text/css">
    .statusPending {
        background-color: #ADD8E6!important;
    }
    .statusSentToEverlights {
        background-color: #FFDDDD!important;
    }
    .statusComplete {
        background-color: #DDFFDD!important;
    }

    .statusPending td.sorting_1 {
        background-color: #ADD8E6!important;
    }
    .statusSentToEverlights td.sorting_1 {
        background-color: #FFDDDD!important;
    }
    .statusComplete td.sorting_1 {
        background-color: #DDFFDD!important;
    }

    .statusPickUp td.sorting_1 {
        background-color: lightgrey!important;
    }
    .statusPickUp {
        background-color: lightgrey!important;
    }

    #mailBody table {
        border: 1px solid #193e1d;
    }

    #mailBody table th{
        border: 1px solid #193e1d;
        font-weight: bold;
    }

    #mailBody table td{
        border: 1px solid #193e1d;
    }

</style>
    <div class="content">
        <div class="row">
        	<div class="sixteen columns">
          		<h1>Lamp Request History</h1>
          	</div>
        </div>
        <div class="row">
            <div>
                <a href="javascript:void(0);" id="btnSendRequestsToEverLights">Send requests to EverLights</a>
                <span style="float:right">
                    <form id="fExport" action="<?php echo base_url();?>admin/LampRequest/csvList" method="get"></form>
                    <a class="button" href="javascript: void(0);" onclick="ExportToCSV();">Export to CSV</a>
                </span>
            </div>
        	<div class="sixteen columns">
        		<table cellpadding="0" cellspacing="0" border="0" class="display" id="callslist" width="100%">
					<thead>
						<tr>
						  	<th>Request#</th>
							<th>Location</th>
				            <th>Request Date</th>
				            <th>CBRE#</th>
				            <th>Invoice#</th>
				            <th>Invoice Date</th>
							<th>BOL Date</th>
							<th>Status</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
                            <th>Request#</th>
                            <th>Location</th>
                            <th>Request Date</th>
                            <th>CBRE#</th>
                            <th>Invoice#</th>
                            <th>Invoice Date</th>
                            <th>BOL Date</th>
                            <th>Status</th>
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
    var filter_complete = 'All';
    var filter_requestDateStart = '';
    var filter_requestDateEnd = '';

	$(function() {
        oTable = $('#callslist').dataTable({
			"sPaginationType": "full_numbers",
	        "bProcessing": true,
	        "bServerSide": true,
	        "bStateSave": false,
            "oSearch": {"sSearch": "", "filter_complete": 'All'},
	        "sAjaxSource": '<?php echo base_url();?>admin/LampRequest/ajaxList',
	        "fnServerData": function ( sSource, aoData, fnCallback ) {
	            var filter_id = $('#storedFilter_id').val();

                if($('#filter_complete').length==1) {
                    filter_complete = $('#filter_complete').val();
                }

	            aoData.push(
	                { "name": "filter_id", "value": $.trim(filter_id) },
                    { "name": "filter_requestDateStart", "value": filter_requestDateStart },
                    { "name": "filter_requestDateEnd", "value": filter_requestDateEnd },
                    { "name": "filter_complete", "value": $.trim(filter_complete) }
	            );

                filterData = aoData;

                $.getJSON( sSource, aoData, function (json) {
                    if(json.error == 'expired') {
                        alert('You session has timed out, click OK to return to the login screen');
                        document.location.href='<?php echo base_url();?>admin/Auth';
                    } else {
                        oTotalRecords = json.iTotalRecords;
                        fnCallback(json)
                    }
                });
	        },
            "sDom": 'lf<"completeBox">rtip'
		});

        $('div.completeBox').html(
            'Start Date<input type="text" id="requestDateStart" value="" style="width: 100px; display: inline-block;" />' +
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
            'End Date<input type="text" id="requestDateEnd" value="" style="width: 100px; display: inline-block;" />' +
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
            '<label style="display: inline-block;">' +
                'Complete?:<select id="filter_complete">' +
                '<option value="All">All</option>' +
                '<?php
                    foreach($data->filterOptions as $key => $option)
                        echo "<option value=\"{$key}\">{$option}</option>";
                ?>' +
                '</select>' +
            '</label>');
        $('#callslist_length').css({'width': '250px'});
        $('div.completeBox').css({'width': '650px'});
        $('#requestDateStart,#requestDateEnd').datepicker({
            dateFormat: "mm/dd/yy",
            weekHeader: "W",
            onSelect: function( selectedDate ) {
                filter_requestDateStart = $('#requestDateStart').val();
                filter_requestDateEnd = $('#requestDateEnd').val();
                oTable.fnDraw();
            }
        });        
        
        var newSearch = $('#callslist_filter input').clone(true);
        newSearch.attr('id', 'callListFilterSearch');
        $('#callslist_filter').html('');
        $('#callslist_filter').append('<label for="callListFilterSearch">Search: </label>');
        $('#callslist_filter').append(newSearch);

        $('#filter_complete').val(filter_complete);

        $('#filter_complete').change (
            function () {
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

    $(function() {
        $('#dialogSendRequestsToEverLights').dialog({
            autoOpen: false,
            height: 500,
            width: 600,
            modal: true,
            buttons: {
                'Send': function() {
                    var $dialog = $(this);
                    $.post('<?php echo base_url();?>admin/LampRequest/SendRequestsToEverLights', {action: 'send', mailTo: $('#mailTo').val(), mailSubject: $('#mailSubject').val()}, function() {
                        $dialog.dialog('close');
                        oTable.fnDraw();
                        $('#mailBody').html('');
                    });
                },
                Cancel: function() {
                    $(this).dialog('close');
                }
            },
            close: function() {
                $('#mailBody').html('Loading...');
            }
        });

        $('#btnSendRequestsToEverLights').click(function() {
            $.post('<?php echo base_url();?>admin/LampRequest/SendRequestsToEverLights', {mailTo: 'test@test.com'}, function(html) {
                $('#mailBody').html(html);
            });
            $('#dialogSendRequestsToEverLights').dialog('open');
        });

    });

    function SaveMailTo() {
        $.post('<?php echo base_url();?>admin/LampRequest/SaveMailTo', {mailTo: $('#mailTo').val()}, function(html) {
        });
    }
</script>
<div id="dialogSendRequestsToEverLights" title="Send Requests to EverLights">
    <div>To<br /><input type="text" id="mailTo" name="mailTo" value="<?php echo $data->mailTo; ?>" style="display: inline-block;" />&nbsp;&nbsp;&nbsp;<a href="#" onclick="SaveMailTo(); return false;">Save</a></div>
    <div>Subject<br /><input type="text" id="mailSubject" name="mailSubject" value="Relamps" disabled="disabled" readonly="readonly" style="display: inline-block;" /></div>
    <div>Body
        <div id="mailBody" style="border: 1px solid #CCCCCC; border-radius: 2px; width: 570px; height: 250px; overflow: scroll; padding: 3px;"></div>
    </div>
</div>
<?php include("application/views/admin/common/footer.php");?>
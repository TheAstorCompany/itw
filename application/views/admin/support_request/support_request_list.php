<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
        <div class="content">
        <div class="row">
        	<div class="sixteen columns">
				<span style="float:right">
					<form id="fExport" action="<?php echo base_url();?>admin/SupportRequest/csvList" method="get"></form>
					<a class="button" href="javascript: void(0);" onclick="ExportToCSV();">Export CSV</a>
				</span>
          		<h1>Support History</h1>
          	</div>
        </div>
        <div class="row">
        	<div class="sixteen columns">
        		<table cellpadding="0" cellspacing="0" border="0" class="display" id="callslist" width="100%">
					<thead>
						<tr>
							<th>Service#</th>
							<th>Date</th>
							<th>Location</th>
                            <th>WO #</th>
							<th>Contact</th>
							<th>Phone#</th>
							<th>Purpose</th>
							<th>Description</th>
							<th>Container</th>
							<th>Service date</th>
							<th>Complete?</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Service#</th>
							<th>Date</th>
							<th>Location</th>
                            <th>WO #</th>
							<th>Contact</th>
							<th>Phone#</th>
							<th>Purpose</th>
							<th>Description</th>
							<th>Container</th>
							<th>Service date</th>
							<th>Complete?</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
<script type="text/javascript">
    function ExportToCSV() {
		var q = '';
		for(var i=0; i<filterData.length; i++) {
			var o = filterData[i];
			var v = o.value;
			if(o.name=='iDisplayStart') {
				v = 0;
			}
			if(o.name=='iDisplayLength') {
				v = iTotalRecords;
			}
			q += '<input type="hidden" name="' + o.name + '" value="' + v + '" />';
		}
		$('#fExport').html(q);
		$('#fExport').submit();
	}

    var filterData = null;
    var iTotalRecords = 100000;
    var oTable = null;
    var filter_complete = 'All';
    var filter_requestDateStart = '';
    var filter_requestDateEnd = '';

    $(function() {
	    oTable = $('#callslist').dataTable({
		    "bStateSave": true,
		    "aaSorting": [[ 1, "asc" ]],
			"sPaginationType": "full_numbers",
	        "bProcessing": true,
	        "bServerSide": true,
	        "oSearch": {"sSearch": "", "filter_complete": 1},
	        "sAjaxSource": '<?php echo base_url();?>admin/SupportRequest/ajaxList',
            "fnStateLoaded": function (oSettings, oData) {
                if(oSettings.oPreviousSearch.filter_complete) {
                    filter_complete = oSettings.oPreviousSearch.filter_complete;
                }
                if(oSettings.oPreviousSearch.filter_requestDateStart) {
                    filter_requestDateStart = oSettings.oPreviousSearch.filter_requestDateStart;
                }
                if(oSettings.oPreviousSearch.filter_requestDateEnd) {
                    filter_requestDateEnd = oSettings.oPreviousSearch.filter_requestDateEnd;
                }
            },
	        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {

				if($('#filter_complete').length==1) {
					filter_complete = $('#filter_complete').val();
				}

				aoData.push(
					{ "name": "filter_complete", "value": $.trim(filter_complete) },
                    { "name": "filter_requestDateStart", "value": $.trim(filter_requestDateStart) },
                    { "name": "filter_requestDateEnd", "value": $.trim(filter_requestDateEnd) }
				);

                oSettings.oPreviousSearch.filter_complete = filter_complete;

				filterData = aoData; 
	            
				$.getJSON( sSource, aoData, function (json) {
					if(json.error == 'expired') {
                      	alert('You session has timed out, click OK to return to the login screen');
                      	document.location.href='<?php echo base_url();?>admin/Auth';
	                } else {
						iTotalRecords = json.iTotalRecords;
	                    fnCallback(json)
	                }
	            });
	        },
			"sDom": 'f<"completeBox">rtip'
		});
		
		$('div.completeBox').html('Start Date<input type="text" id="filter_requestDateStart" value="" style="width: 100px; padding: 0px; display: inline-block;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;End Date<input type="text" id="filter_requestDateEnd" value="" style="width: 100px; padding: 0px; display: inline-block;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Complete?:<select id="filter_complete"><option value="All">All</option><option value="0">Incomplete</option><option value="1">Complete</option></select>');
		var newSearch = $('#callslist_filter input').clone(true);
		newSearch.attr('id', 'callListFilterSearch');
		$('#callslist_filter').html('');
		$('#callslist_filter').append('<label for="callListFilterSearch">Search: </label>');
		$('#callslist_filter').append(newSearch);

        $('div.completeBox').css({'width': '570px'});

        $('#filter_requestDateStart,#filter_requestDateEnd').datepicker({
            dateFormat: "mm/dd/yy",
            weekHeader: "W",
            onSelect: function( selectedDate ) {
                filter_requestDateStart = $('#filter_requestDateStart').val();
                filter_requestDateEnd = $('#filter_requestDateEnd').val();
                oTable.fnDraw();
            }
        });

        $('#filter_complete').val(filter_complete);
        $('#filter_requestDateStart').val(filter_requestDateStart);
        $('#filter_requestDateEnd').val(filter_requestDateEnd);

		$('#filter_complete').change (
			function () {
                oTable.fnDraw();
			}
		);
	});
</script>

<?php include("application/views/admin/common/footer.php");?>

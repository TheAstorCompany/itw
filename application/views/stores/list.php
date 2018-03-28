<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
	<script>
	$(function() {
		$('#tabs').tabs();
	});
	</script>
    <div class="row">
	    <div class="sixteen columns">
			    <span style="float:right">
				    <form id="fExport" action="<?php echo base_url();?>Stores/csvList" method="get"></form>
				    <a class="button" href="javascript: void(0);" onclick="ExportToCSV();">Export CSV</a>
			    </span>
		    <h1>Penn Medicine List</h1>
	    </div>
    </div>
    <!--To style tr, use class="odd even gradeA gradeB gradeC gradeU gradeX"-->
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="storeslist" width="100%">
	<thead>
		<tr>
			<th>Location</th>
			<th>District</th>
			<th>Address</th>
			<th>City</th>
			<th>State</th>
			<th>Zip</th>
			<th>24-Hour</th>
			<th>Office Location</th>
			<th>SqFt</th>
			<th>Diversion</th>
			<th>Cost/Sqft</th>
		</tr>
	</thead>
	<tbody>  
	</tbody>
	<tfoot>
		<tr>
			<th>Location</th>
			<th>District</th>
			<th>Address</th>
			<th>City</th>
			<th>State</th>
			<th>Zip</th>
			<th>24-Hour</th>
			<th>Office Location</th>
			<th>SqFt</th>
			<th>Diversion</th>
			<th>Cost/Sqft</th>
		</tr>
	</tfoot>
	</table>
	</div>
</div>
</div>
</div>
<?php echo form_dropdown("bystate", $data->byState, set_value("bystate", $data->bystate), "id='filter_complete'");?>
<script>
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
	
	var stack_id = 0;
	$(function() {	
		var table = $('#storeslist').dataTable({
			"sPaginationType": "full_numbers",
			"bProcessing": true,
			"bServerSide": true,
			"bStateSave": false,
			//"oSearch": {"sSearch": ""},
			"sAjaxSource": '<?php echo base_url();?>Stores/ajaxList',
			"fnServerData": function ( sSource, aoData, fnCallback ) {
			    var filter_id = $('#storedFilter_id').val();
			    
			    aoData.push(
				{ "name": "anticache", "value": Math.random() }
			    );

			    aoData.push(
				{ "name": "filter_id", "value": $.trim(filter_id) }
			    );
				
			    var filter_complete = 1;
			    if($('#filter_complete').length==1) {
				filter_complete = $('#filter_complete').val();
			    }

			    aoData.push(
				{ "name": "filter_complete", "value": $.trim(filter_complete) }
			    );

			    filterData = aoData;
			    stack_id++;
			    $.ajax({
				dataType: "json",
				url: sSource,
				data: aoData,
				lstack_id: stack_id,
				success: function (json) {		
				    if(json.error == 'expired') {
					alert('You session has timed out, click OK to return to the login screen');
					document.location.href = '<?php echo base_url();?>admin/Auth';
				    } else {
					if(this.lstack_id==stack_id) {
					    iTotalRecords = json.iTotalRecords;
					    fnCallback(json);
					}
				    }
				}
			    });
			},
			"sDom": 'lf<"completeBox">rtip'
		});
		
		$('div.completeBox').html("<label style='float: left;'>State:</label>");
		$('#filter_complete').appendTo('div.completeBox');
		var newSearch=$('#callslist_filter input').clone(true);
		newSearch.attr('id', 'callListFilterSearch');
		$('#callslist_filter').html('');
		$('#callslist_filter').append("<label for='callListFilterSearch'>Search: </label>");
		$('#callslist_filter').append(newSearch);
		
		$('#storeslist_filter').css('left','-15px');
		$('#storeslist_filter').css('position','relative');
		
		/*
		$('#callslist_filter input').unbind();
		$('#callslist_filter input').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.fnFilter(this.value);   
			}
		});*/
		$('#filter_complete').change (
			function () { 
				table.fnDraw();
			}
		);
	});
</script>        
<?php include("application/views/admin/common/footer.php");?>

<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>  
        <div class="content">
        	<div class="row">
        		<div class="sixteen columns">
					<h1>Manage Company</h1>
          		</div>
          	</div>
        	<div class="row">
        		<div class="sixteen columns">
         			<div id="tabs" style="border:0px;">
						<?php include("application/views/admin/common/manage_company_tabs.php"); ?>
                        <div id="tabs-1" style="border:2px solid #7ABF53; padding: 30px;">
                                <span style="float:right">
                                    <form id="fExport" action="<?php echo base_url();?>admin/Stores/csvList" method="get"></form>
                                    <a class="button" href="javascript: void(0);" onclick="ExportToCSV();">Export to CSV</a>
                                </span>
                                <a href="<?php echo base_url()?>/admin/Stores/AddEdit" class="button">Add Site</a>
                                <table cellpadding="0" cellspacing="0" border="0" class="display" id="userslist" width="100%">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Site#</th>
                                        <th>District#</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Zip</th>
                                        <th>24-Hour</th>
                                        <th>Office Location</th>
                                        <th>Scheduled Services</th>
                                        <th>Store Phone</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Status</th>
                                        <th>Site#</th>
                                        <th>District#</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Zip</th>
                                        <th>24-Hour</th>
                                        <th>Office Location</th>
                                        <th>Scheduled Services</th>
                                        <th>Store Phone</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </tfoot>
                            </table>
                                <style type="text/css">
                                    #userslist_wrapper label{float:none;display:inline-block;white-space:nowrap;padding:3px 5px 3px 0;}
                                    #userslist_wrapper input,#userslist_wrapper select{margin:0;float:none;display:inline-block;position:relative;width:200px;}
                                    #userslist_wrapper .filterGroup,#userslist_wrapper .dataTables_length,#userslist_wrapper .dataTables_filter{
                                        display:inline-block;margin:0;
                                        float:none;
                                        width:auto;
                                        padding-right:20px;
                                    }
                                    #userslist_wrapper .dataTables_length label{width:160px;}
                                    #userslist_wrapper .dataTables_status label{width:120px;}
                                    #userslist_wrapper .dataTables_filter label{width:250px;}
                                    #userslist_wrapper .dataTables_status select,#userslist_wrapper .dataTables_length select{width:80px;}

                                    #userslist_wrapper .dataTables_status{margin-left:80px;}
                                    #userslist_wrapper .dataTables_filter{float:right;padding-right:0;}
                                    .container .columns.sixteen{width:100%;}
                                </style>
                            <div id="custom_filter">
                                <div class="filterGroup dataTables_status">
                                    <label>
                                    Status:
                                    <?php echo form_dropdown('filter_status', $data->filter_statusOptions, 2, 'id="filter_status"');?>
                                    </label>
                                </div>
                                <div class="filterGroup">
                                    <label style="width: 250px;">
                                    Container:
                                    <?php echo form_dropdown('filter_container', $data->filter_containerOptions, 0, 'id="filter_container"');?>
                                    </label>
                                    <label style="width: 150px;">
                                    &nbsp;&nbsp;&nbsp;Search by:
                                    <select id="filter_field" style="width:80px;">
                                        <option value="0">All</option>
                                        <option value="1" selected="selected">Site #</option>
                                        <option value="2">District #</option>
                                        <option value="3">Address</option>
                                        <option value="4">City</option>
                                        <option value="5">State</option>
                                        <option value="6">Zip</option>
                                        <option value="7">Phone</option>
                                    </select>
                                    </label>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>				
<script>
	var filterData = null;
	var iTotalRecords = 100000;
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
		var table = $('#userslist').dataTable({
			"sPaginationType": "full_numbers",
	        "bProcessing": true,
	        "bServerSide": true,
	        "bStateSave": true,
	        "sAjaxSource": '<?php echo base_url();?>admin/Stores/ajaxList',
	        "fnServerData": function ( sSource, aoData, fnCallback ) {
                aoData.push(
                    { "name": "filter_status", "value": $.trim($('#filter_status').val()) }
                );
                aoData.push(
                    { "name": "filter_container", "value": $.trim($('#filter_container').val()) }
                );
                aoData.push(
                    { "name": "filter_field", "value": $.trim($('#filter_field').val()) }
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
	        }
		});
		
		var custom_filter = $('#custom_filter').html();
		$('#custom_filter').empty();
		
		$('#userslist_length').after(custom_filter);
		$('#filter_status, #filter_container, #filter_field').change (
			function () { 
				table.fnDraw();
			}
		);
	});
</script>
<?php include("application/views/admin/common/footer.php");?>

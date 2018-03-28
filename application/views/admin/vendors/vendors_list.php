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
            				<a href="<?php echo base_url()?>admin/Vendors/AddEdit" class="button">Add Vendor</a>
					        <table cellpadding="0" cellspacing="0" border="0" class="display" id="vendorsList" width="100%">
								<thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Vendor#</th>
                                        <th>Vendor Name</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Zip</th>
                                        <th>Last Updated</th>
                                    </tr>
								</thead>
								<tbody>  
								<tfoot>
                                    <tr>
                                        <th>Status</th>
                                        <th>Vendor#</th>
                                        <th>Vendor Name</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Zip</th>
                                        <th>Last Updated</th>
                                    </tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
            </div>
        </div>
<script>
    var table = null;

    $(function() {
        table = $('#vendorsList').dataTable({
            "sPaginationType": "full_numbers",
            "bProcessing": true,
            "bServerSide": true,
            "bStateSave": true,
            "sAjaxSource": '<?php echo base_url();?>admin/Vendors/ajaxList',
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                var filter_id = $('#storedFilter_id').val();
                var filter_active = 1;
                if($('#filter_active').length==1) {
                    filter_active = $('#filter_active').val();
                }

                aoData.push(
                    { "name": "filter_id", "value": $.trim(filter_id) },
                    { "name": "filter_active", "value": filter_active }
                );

                $.getJSON( sSource, aoData, function (json) {
                    if(json.error == 'expired') {
                        alert('You session has timed out, click OK to return to the login screen');
                        document.location.href='<?php echo base_url();?>admin/Auth';
                    } else {
                        fnCallback(json)
                    }
                });
            },
            "sDom": 'lf<"completeBox">rtip'
        });

        $('div.completeBox').html('<label>Status:<select id="filter_active"><option value="-1">All</option><option value="1">Active</option><option value="0">Inactive</option></select></label>');

        $('#filter_active').val(1);

        $('#filter_active').change (
            function () {
                table.fnDraw();
            }
        );
    });
</script>
<?php include("application/views/admin/common/footer.php");?>
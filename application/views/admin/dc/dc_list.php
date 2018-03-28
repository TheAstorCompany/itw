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
            				<a href="<?php echo base_url()?>admin/DistributionCenters/AddEdit" class="button">Add Site</a>
       						<table cellpadding="0" cellspacing="0" border="0" class="display" id="dclist" width="100%">
								<thead>
									<tr>
									  	<th>Status</th>
										<th>Site Name</th>
							            <th>Address</th>
							            <th>City</th>
							            <th>State</th>
							            <th>Zip</th>
										<th>Waste</th>
										<th>Recycling</th>
										<th>Diversion</th>
										<th>Cost/Sqft</th>
										<th>Last Updated</th>
									</tr>
								</thead>	
								<tfoot>
									<tr>
									  	<th>Status</th>
										<th>Site Name</th>
							            <th>Address</th>
							            <th>City</th>
							            <th>State</th>
							            <th>Zip</th>
										<th>Waste</th>
										<th>Recycling</th>
										<th>Diversion</th>
										<th>Cost/Sqft</th>
										<th>Last Updated</th>
									</tr>
								</tfoot>
							</table>
					</div>
					</div></div>
<script>
	$(function() {		
		$('#dclist').dataTable({
			"sPaginationType": "full_numbers",
	        "bProcessing": true,
	        "bServerSide": true,
	        "bStateSave": true,
	        "sAjaxSource": '<?php echo base_url();?>admin/DistributionCenters/ajaxList',
	        "fnServerData": function ( sSource, aoData, fnCallback ) {
	            var filter_id = $('#storedFilter_id').val();

	            aoData.push(
	                { "name": "filter_id", "value": $.trim(filter_id) }
	             );
	             
	             $.getJSON( sSource, aoData, function (json) {
	                if(json.error == 'expired') {
                      	alert('You session has timed out, click OK to return to the login screen');
                      	document.location.href='<?php echo base_url();?>admin/Auth';
	                } else {
	                    fnCallback(json)
	                }
	             });
	        }
		});
	});
</script>
<?php include("application/views/admin/common/footer.php");?>

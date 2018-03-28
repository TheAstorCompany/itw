<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

    <script type="text/javascript">
	$(function() {
		$('#tabs').tabs();
		$('#dclist').dataTable( {
					"bPaginate": false,
					"bFilter": false
				} );
	});
	</script>


    <div class="row">
        <div class="sixteen columns">
           <h1>North Mississippi List</h1>
            <div>
                <span style="float:right">
                    <a href="<?php echo base_url()?>DistributionCenters/Lists?export=1" class="button">Export CSV</a>
                </span>
                <table cellpadding="0" cellspacing="0" border="0" class="display" id="dclist" width="100%">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Zip</th>
                            <th>SqFt</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $temp) {?>
                        <tr class="gradeA">
                            <!--td><a href="<?php echo base_url()?>Dcinfo/Invoices/<?php echo $temp["id"] ?>"><?php echo $temp["name"]?> </a></td-->
                            <td><?php echo $temp["name"]?> </td>
                            <td><?php echo $temp["addressLine1"]; ?></td>
                            <td><?php echo $temp["city"]; ?> </td>
                            <td><?php echo $temp["sname"]; ?></td>
                            <td nowrap><?php echo $temp["zip"]; ?></td>
                            <td><?php echo round($temp["squareFootage"], 0); ?></td>
                            <td><?php echo $temp["lu"]; ?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Location</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Zip</th>
                            <th>SqFt</th>
                            <th>Last Updated</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
        
<?php include("application/views/admin/common/footer.php");?>

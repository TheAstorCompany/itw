<?php
    include("application/views/admin/common/header.php");
    include("application/views/admin/common/top_menu.php");
?>

<div class="content">
	<div class="row">
		<div class="two columns">
			<a href="javascript:javascript:history.go(-1)" class="button">&lt;- Go back</a>
		</div>
	</div>
	<div class="row">
		<div class="four columns">
			<h1><?php echo $DCData['name']; ?></h1>
		</div>
		<div class="two columns">
			Square Footage<h5><?php echo $DCData['squareFootage']; ?> sqft</h5>
		</div>
		<div class="two columns">
			Diversion Rate<h5><?php echo $DiversionRate; ?>%</h5>
		</div>
		<div class="eight columns omega">

<?php
    if(!empty($Contacts)) {
?>
			<table width="100%" border="0" cellspacing="10" cellpadding="20">
				<tr>
					<td>Contact</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
    <?php
        while (list($k,$v) = each($Contacts)) {
    ?>
                <tr style="border-bottom:1px solid #ddd">
                    <td><h5><?php echo $v['firstName'] . ' ' . $v['lastName']; ?></h5></td>
                    <td><h5><?php echo $v['title']; ?></h5></td>
                    <td><h5><?php echo $v['phone']; ?></h5></td>
                    <td><h5><?php echo $v['email_lnk']; ?></h5></td>
                </tr>
    <?php
        }
    ?>
			</table>
    <?php
    }
?>
		</div>
	</div>

	<div class="row">
		<div class="sixteen columns">
			<div id="tabs" style="border:0px;">
                <?php
                    include("dcinfo_tabs.php");
                ?>
				<div style="border:2px solid #7ABF53; padding: 10px;">

					<span style="float:right">
						<a href="<?php echo base_url() . 'Dcinfo/Invoices/' . $id; ?>'?export=1" class="button">Export CSV</a>
					</span>
                    <style type="text/css">
                        .alignright {
                            text-align: right;
                        }
                    </style>
                    <table cellpadding="0" cellspacing="0" border="0" class="display" id="invoices_tbl" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Date</th>
                                <th style="width: 100px;">Inv #</th>
                                <th style="width: 80px;">Date Sent</th>
                                <th>Vendor</th>
                                <th style="width: 150px;">Scheduled (Tons)</th>
                                <th style="width: 120px;">On Call (Tons)</th>
                                <th style="width: 120px;">Cost</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Date</th>
                                <th>Inv #</th>
                                <th>Date Sent</th>
                                <th>Vendor</th>
                                <th>Scheduled (Tons)</th>
                                <th>On Call (Tons)</th>
                                <th>Cost</th>
                            </tr>
                        </tfoot>
				    </table>

                    <script type="text/javascript">
                        $(function() {
                            $("#invoices_tbl").dataTable({
                                "sPaginationType"	: "full_numbers",
                                "bProcessing"		: true,
                                "bPaginate" 		: false,
                                "bServerSide"		: true,
                                "bStateSave"		: false,
                                "oSearch"			: {"sSearch": "incomplete"},
                                "bFilter"			: false,
                                "bLengthChange"		: false,
                                "sAjaxSource"		: "<?php echo base_url() . 'Dcinfo/ajax_Invoices/' . $id; ?>",
                                "aoColumnDefs"      : [{"sClass": "alignright", "aTargets": [6]}]
                            });
                        });
                    </script>

				    <br style="clear: both;" />

				</div>
			</div>
		</div>
	</div>
</div>

<?php
    include("application/views/admin/common/footer.php");
?>
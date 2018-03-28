<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>
<script type="text/javascript">
 
    $(document).ready(function() {
	    getContainerContent();
	    getMaterialContent();
	    getPurposeContent();
	    getFeetypeContent();
	    getMarketRatesContent();

	    $('#setup_tabs').tabs();

	    $('#container_dialog').dialog({ modal: true, autoOpen: false, title: 'Container', buttons: { 'Save': function() { saveContainer(); $(this).dialog('close'); }, Cancel: function() { $(this).dialog('close'); } } });
	    $('#material_dialog').dialog({ modal: true, autoOpen: false, title: 'Material', buttons: { 'Save': function() { saveMaterial(); $(this).dialog('close'); }, Cancel: function() { $(this).dialog('close'); } } });
	    $('#purpose_dialog').dialog({ modal: true, autoOpen: false, title: 'Purpose', buttons: { 'Save': function() { savePurpose(); $(this).dialog('close'); }, Cancel: function() { $(this).dialog('close'); } } });
	    $('#feetype_dialog').dialog({ modal: true, autoOpen: false, title: 'Fee Type', buttons: { 'Save': function() { saveFeetype(); $(this).dialog('close'); }, Cancel: function() { $(this).dialog('close'); } } });
	    $('#marketrates_dialog').dialog({ modal: true, autoOpen: false, title: 'Market Rates', buttons: { 'Save': function() { saveMarketRates(); $(this).dialog('close'); }, Cancel: function() { $(this).dialog('close'); } } });

	    $('#delete_dialog').dialog({
		    resizable: false,
		    autoOpen: false,
		    height: 160,
		    modal: true,
		    deleteCallback: null,
		    buttons: {
			    'Delete': function() {
				    var o = $(this).dialog('option');
				    var $this = $(this);
				    if(o.deleteCallback!=null) {
					    o.deleteCallback(function(){
						    $this.dialog('close');
					    });
				    } else {
					    $this.dialog('close');
				    }
			    },
			    Cancel: function() {
				    $(this).dialog('close');
			    }
		    }
	    });

	    $("input[name='marketratesMonthValidity'], input[name='marketratesStartDate'], input[name='marketratesStopDate']").datepicker({
		    dateFormat: "mm/dd/yy",
		    weekHeader: "W" 
	    });
    });

    function getContainerContent() {
	    $.post('<?php echo base_url();?>admin/Setup/getContainerContent', function(data) {
		    $('#divContainer_content').html(data);
	    });
    }

    function getMaterialContent() {
	    $.post('<?php echo base_url();?>admin/Setup/getMaterialContent', function(data) {
		    $('#divMaterial_content').html(data);
	    });
    }

    function getPurposeContent() {
	    $.post('<?php echo base_url();?>admin/Setup/getPurposeContent', function(data) {
		    $('#divPurpose_content').html(data);
	    });
    }

    function getFeetypeContent() {
	    $.post('<?php echo base_url();?>admin/Setup/getFeetypeContent', function(data) {
		    $('#divFeetype_content').html(data);
	    });
    }

    function getMarketRatesContent() {
	/*
	    $.post('<?php echo base_url();?>admin/Setup/getMarketRatesContent', function(data) {
		    $('#divMarketRates_content').html(data);
	    });
	    */
    }

    function editContainer(id) {
	    if(id!=0) {
		    $tr = $('#containerId_' + id);

		    $('#containerId').val(id);
		    $('#containerName').val($tr.find('.containerName').html());
		    $('#containerType').val($tr.find('.containerType').html());
		    $('#containerWeightInLbs').val($tr.find('.containerWeightInLbs').html());
            if($tr.find('.containerOver8Yards').html()=='N') {
                $('#containerOver8YardsNo').attr('checked', true);
            } else {
                $('#containerOver8YardsYes').attr('checked', true);
            }
	    } else {
		    $('#containerId').val(0);
		    $('#containerName').val('');
		    $('#containerWeightInLbs').val('');
            $('#containerOver8YardsNo').attr('checked', true);
	    }

	    $('#container_dialog').dialog('open');	
    }

    function editMaterial(id) {
	    if(id!=0) {
		    $tr = $('#materialId_' + id);

		    $('#materialId').val(id);
		    $('#materialName').val($tr.find('.materialName').html());
		    $('#materialEnergySaves').val($tr.find('.materialEnergySaves').html());
		    $('#materialCO2Saves').val($tr.find('.materialCO2Saves').html());
		    $('#materialIsHazardous').val($tr.find('.hidmaterialIsHazardous').val());
		    $('#materialUnit').val($tr.find('.hidmaterialUnit').val());
		    $('#materialQuickbooks').val($tr.find('.materialQuickbooks').html());
	    } else {
		    $('#materialId').val(0);
		    $('#materialName').val('');
		    $('#materialEnergySaves').val('');
		    $('#materialCO2Saves').val('');
		    $('#materialIsHazardous').val('0');
		    $('#materialUnit').val('0');
		    $('#materialQuickbooks').val('');
	    }

	    $('#material_dialog').dialog('open');	
    }

    function editPurpose(id) {
	    if(id!=0) {//Purpose
		    $tr = $('#purposeId_' + id);

		    $('#purposeId').val(id);
		    $('#purposeName').val($tr.find('.purposeName').html());
	    } else {
		    $('#purposeId').val(0);
		    $('#purposeName').val('');
	    }

	    $('#purpose_dialog').dialog('open');	
    }

    function editFeetype(id) {
	    if(id!=0) {//Purpose
		    $tr = $('#feetypeId_' + id);

		    $('#feetypeId').val(id);
		    $('#feetypeName').val($tr.find('.feetypeName').html());
		    $('#feetypeQuickbooks').val($tr.find('.feetypeQuickbooks').html());
	    } else {
		    $('#feetypeId').val(0);
		    $('#feetypeName').val('');
		    $('#feetypeQuickbooks').val('');
	    }

	    $('#feetype_dialog').dialog('open');	
    }

    function editMarketRates(id) {
	    if(id!=0) {
		    $tr = $('#marketratesId_' + id);

		    $('#marketratesId').val(id);
		    $('#marketratesMaterial').val($tr.find('.hidmarketratesMaterial').val());
		    $('#marketratesDistributionCenter').val($tr.find('.hidmarketratesDistributionCenter').val());
		    $('#marketratesCompany').val($tr.find('.hidmarketratesCompany').html());
		    $('#marketratesStartDate').val($tr.find('.marketratesStartDate').html());
		    $('#marketratesStopDate').val($tr.find('.marketratesStopDate').html());
		    $('#marketratesInvoiceRate').val($tr.find('.marketratesInvoiceRate').html());
		    $('#marketratesPORate').val($tr.find('.marketratesPORate').html());
	    } else {
		    $('#marketratesId').val(0);
		    $('#marketratesMaterial').val(0);
		    $('#marketratesDistributionCenterId').val(0);
		    $('#marketratesCompany').val(1);
		    $('#marketratesStartDate').val('');
		    $('#marketratesStopDate').val('');
		    $('#marketratesInvoiceRate').val('');
		    $('#marketratesPORate').val('');
	    }

	    $('#marketrates_dialog').dialog('open');	
    }

    function saveContainer() {
	    $.post('<?php echo base_url();?>admin/Setup/saveContainer', 
		    { 
			    containerId: $('#containerId').val(), 
			    name: $('#containerName').val(), 
			    containerType: $('#containerType').val(),
			    weightInLbs: $('#containerWeightInLbs').val(),
                over8Yards: $('input[name="containerOver8Yards"]:checked').val()
		    },
		    function(data) {
			    getContainerContent();
		    }
	    );
    }

    function saveMaterial() {    
	    $.post('<?php echo base_url();?>admin/Setup/saveMaterial', 
		    { 
			    materialId: $('#materialId').val(), 
			    name: $('#materialName').val(), 
			    EnergySaves: $('#materialEnergySaves').val(), 
			    CO2Saves: $('#materialCO2Saves').val(),
			    isHazardous: $('#materialIsHazardous').val(),
			    unit: $('#materialUnit').val(),
			    quickbooks: $('#materialQuickbooks').val()
		    },
		    function(data) {
			    getMaterialContent();
		    }
	    );
    }

    function savePurpose() {
	    $.post('<?php echo base_url();?>admin/Setup/savePurpose', 
		    { 
			    purposeId: $('#purposeId').val(), 
			    name: $('#purposeName').val() 
		    },
		    function(data) {
			    getPurposeContent();
		    }
	    );
    }

    function saveFeetype() {
	    $.post('<?php echo base_url();?>admin/Setup/saveFeetype', 
		    { 
			    feetypeId: $('#feetypeId').val(), 
			    name: $('#feetypeName').val(),
			    quickbooks: $('#feetypeQuickbooks').val()
		    },
		    function(data) {
			    getFeetypeContent();
		    }
	    );
    }

    function saveMarketRates() {
	    $.post('<?php echo base_url();?>admin/Setup/saveMarketRates', 
		    {
			    marketratesId: $('#marketratesId').val(), 
			    distributionCenterId: $('#marketratesDistributionCenter').val(),
			    materialId: $('#marketratesMaterial').val(),
			    companyId: $('#marketratesCompany').val(),
			    startDate: $('#marketratesStartDate').val(),
			    stopDate: $('#marketratesStopDate').val(),
			    invoiceRate: $('#marketratesInvoiceRate').val(),
			    poRate: $('#marketratesPORate').val()
		    },
		    function(data) {
			    //getMarketRatesContent();
			    oTable.fnDraw();
		    }
	    );
    }

    function deleteContainer(id) {
	    $('#delete_dialog').dialog('option', 'deleteCallback', function(callback) {
		    $.post('<?php echo base_url();?>admin/Setup/deleteContainer', 
			    { 
				    containerId: id
			    },
			    function(data) {
				    if(callback) {
					    callback();
				    }
				    getContainerContent();
			    }
		    );
	    });

	    $('#delete_dialog').dialog('open');
    }

    function deleteMaterial(id) {
	    $('#delete_dialog').dialog('option', 'deleteCallback', function(callback) {
		    $.post('<?php echo base_url();?>admin/Setup/deleteMaterial', 
			    { 
				    materialId: id
			    },
			    function(data) {
				    if(callback) {
					    callback();
				    }
				    getMaterialContent();
			    }
		    );
	    });

	    $('#delete_dialog').dialog('open');
    }

    function deletePurpose(id) {
	    $('#delete_dialog').dialog('option', 'deleteCallback', function(callback) {
		    $.post('<?php echo base_url();?>admin/Setup/deletePurpose', 
			    { 
				    purposeId: id
			    },
			    function(data) {
				    if(callback) {
					    callback();
				    }
				    getPurposeContent();
			    }
		    );
	    });

	    $('#delete_dialog').dialog('open');
    }

    function deleteFeetype(id) {
	    $('#delete_dialog').dialog('option', 'deleteCallback', function(callback) {
		    $.post('<?php echo base_url();?>admin/Setup/deleteFeetype', 
			    { 
				    feetypeId: id
			    },
			    function(data) {
				    if(callback) {
					    callback();
				    }
				    getFeetypeContent();
			    }
		    );
	    });

	    $('#delete_dialog').dialog('open');
    }

    function deleteMarketRates(id) {
	    $('#delete_dialog').dialog('option', 'deleteCallback', function(callback) {
		    $.post('<?php echo base_url();?>admin/Setup/deleteMarketRates', 
			    { 
				    marketratesId: id
			    },
			    function(data) {
				    if(callback) {
					    callback();
				    }
				    getMarketRatesContent();
			    }
		    );
	    });

	    $('#delete_dialog').dialog('open');
    }

    var oTable = null;
    var filterData = null;
    var oTotalRecords = 0;
    
    $(function() {		
	oTable = $('#callslist').dataTable({
	    "sDom": 'p<"#toolbar1">r<"#toolbar2">',
	    "sPaginationType": "full_numbers",
	    "iDisplayLength": 50,
	    "bPaginate": true,
	    "bProcessing": true,
	    "bServerSide": true,
	    "bStateSave": false,
	    "bSort": false,
	    "sAjaxSource": '<?php echo base_url();?>admin/Setup/getMarketRatesContent',
	    "fnServerData": function ( sSource, aoData, fnCallback ) {

		aoData.push({ "name": "filterDistributionCenterId", "value": $.trim($('#filterDistributionCenterId').val())});
		aoData.push({ "name": "filterStartDate", "value": $.trim($('#filterStartDate').val())});	
	
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

	$('#toolbar2').prepend($('#additionalFilter').html());
	$('#additionalFilter').remove();
	
	$('#filterStartDate').datepicker({
	    dateFormat: "mm/dd/yy",
	    weekHeader: "W",
	    onSelect: function( selectedDate ) {
                oTable.fnDraw();
            }
	});	
	$('#filterDistributionCenterId').change (
	    function () { 
		oTable.fnDraw();
	    }
	);
    });
</script>

<span style="color:red;"><?php echo validation_errors(); ?></span>
<h2>Setup</h2>        
<div id="setup_tabs" style="border:0px;">
	<ul>
		<li><a href="#tabs-1">Container</a></li>
		<li><a href="#tabs-2">Material</a></li>
		<li><a href="#tabs-3">Purpose</a></li>
		<li><a href="#tabs-4">Fee Type</a></li>
		<li><a href="#tabs-5">Market Rates</a></li>
	</ul>      			
	<div id="tabs-1" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
		<a href="javascript: void(0);" onclick="editContainer(0);">New</a>
		<div id="divContainer_content"></div>
	</div>
	<div id="tabs-2" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
		<a href="javascript: void(0);" onclick="editMaterial(0);">New</a>
		<div id="divMaterial_content"></div>
	</div>
	<div id="tabs-3" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
		<a class="ui-button" href="javascript: void(0);" onclick="editPurpose(0);">New</a>
		<div id="divPurpose_content"></div>
	</div>
	<div id="tabs-4" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
		<a class="ui-button" href="javascript: void(0);" onclick="editFeetype(0);">New</a>
		<div id="divFeetype_content"></div>
	</div>	
	<div id="tabs-5" style="border-top:2px solid #7ABF53;padding-left:0px;padding-right:0px;padding-top:14px">
		<a class="ui-button" href="javascript: void(0);" onclick="editMarketRates(0);">New</a>
		<div id="divMarketRates_content">
		    <div style="display: none;" id="additionalFilter">
			Start Date&nbsp;<input type="text" id="filterStartDate" value="" style="width: 100px; display: inline-block;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			Distribution Center&nbsp;<select name="filterDistributionCenterId" id="filterDistributionCenterId" style="display: inline-block;">
			    <option value="">- All -</option>
			    <?php 
				foreach($data->distributionCenterIdOptions as $dc) {
				    echo '<option value="'.$dc['id'].'">'.$dc['name'].'</option>';
				}
			    ?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    </div>
		    <table cellpadding="0" cellspacing="0" border="0" class="display" id="callslist" width="100%">
			<thead>
			    <tr>
				<th>Material</th>
                                <th>Distribution Center</th>
				<th>Company</th>
				<th>Start Date</th>
				<th>Stop Date</th>
				<th>Invoice Rate</th>
				<th>PO Rate</th>
				<th class="centered">-</th>
			    </tr>
			</thead>
			<tfoot>
			    <tr>
				<th>Material</th>
                                <th>Distribution Center</th>
				<th>Company</th>
				<th>Start Date</th>
				<th>Stop Date</th>
				<th>Invoice Rate</th>
				<th>PO Rate</th>
				<th class="centered">-</th>
			    </tr>
			</tfoot>
		    </table>		    
		</div>
	</div>	
</div>
<div id="container_dialog">
	<input type="hidden" value="" name="containerId" id="containerId" value="0" />
	<div>
		Name<br />
		<input type="text" value="" name="containerName" id="containerName" />
	</div>
	<div>
		Type<br />
		<?php echo form_dropdown('containerType', $containerTypeOptions, null, 'id="containerType"');?>
	</div>
	<div>
		weightInLbs<br />
		<input type="text" value="" name="containerWeightInLbs" id="containerWeightInLbs" />
	</div>
    <div>
        Over 8 Yards<br />
        Yes&nbsp;<input type="radio" value="1" name="containerOver8Yards" id="containerOver8YardsYes" />&nbsp;&nbsp;&nbsp;No&nbsp;<input type="radio" value="0" name="containerOver8Yards" id="containerOver8YardsNo" />
    </div>
</div>
<div id="material_dialog">
	<input type="hidden" value="" name="materialId" id="materialId" value="0" />
	<div>
		Name<br />
		<input type="text" value="" name="materialName" id="materialName" />
	</div>
	<div>
		Energy Saves<br />
		<input type="text" value="" name="materialEnergySaves" id="materialEnergySaves" size="8" />
	</div>
	<div>
		CO2 Saves<br />
		<input type="text" value="" name="materialCO2Saves" id="materialCO2Saves" size="8" />
	</div>	
	<div>
		Quickbooks<br />
		<input type="text" value="" name="materialQuickbooks" id="materialQuickbooks" />
	</div>	
	<div>
		Hazardous<br />
		<select name="materialIsHazardous" id="materialIsHazardous">
		    <option value="1">Yes</option>
		    <option value="0">No</option>
		</select>
	</div>	
	<div>
		Unit of measure<br />
		<?php echo form_dropdown('materialUnit', $data->unitOptions, "0", "id='materialUnit'");?>
	</div>
</div>
<div id="purpose_dialog">
	<input type="hidden" value="" name="purposeId" id="purposeId" value="0" />
	<div>
		Name<br />
		<input type="text" value="" name="purposeName" id="purposeName" />
	</div>
</div>
<div id="feetype_dialog">
	<input type="hidden" value="" name="feetypeId" id="feetypeId" value="0" />
	<div>
		Name<br />
		<input type="text" value="" name="feetypeName" id="feetypeName" />
	</div>
	<div>
		Quickbooks<br />
		<input type="text" value="" name="feetypeQuickbooks" id="feetypeQuickbooks" />
	</div>	
</div>
<div id="marketrates_dialog">
	<input type="hidden" value="" name="marketratesId" id="marketratesId" value="0" />
	<div>
		Materials<br />
		<?php echo form_dropdown('marketratesMaterial', $data->MaterialsList, "0", "id='marketratesMaterial'");?>
	</div>
	<div>
		Distribution Center<br />
		<?php echo form_dropdown('marketratesDistributionCenter', $data->DistributionCentersList, "0", "id='marketratesDistributionCenter'");?>
	</div>
        <div>
		Company<br />
		<?php echo form_dropdown('marketratesCompany', $data->CompaniesList, "0", "id='marketratesCompany'");?>
	</div>	
	<div>
		Start Date<br />
		<input type="text" value="" name="marketratesStartDate" id="marketratesStartDate" />
	</div>
	<div>
		Stop Date<br />
		<input type="text" value="" name="marketratesStopDate" id="marketratesStopDate" />
	</div>
	<div>
		Invoice Rate<br />
		<input type="text" value="" name="marketratesInvoiceRate" id="marketratesInvoiceRate" />
	</div>
	<div>
		PO Rate<br />
		<input type="text" value="" name="marketratesPORate" id="marketratesPORate" />
	</div>
</div>
<div id="delete_dialog" title="Delete the item?">
	<p>Are you sure?</p>
</div>
<?php include("application/views/admin/common/footer.php");?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class Setup extends Auth {

	private $unitOptions = array(
			0 => '- Please select -',
			1 => 'Tons',
			2 => 'Lbs',
			3 => 'Bales',
			4 => 'Bulbs',
			5 => 'Boxes',
			6 => 'Units',
		);
    
	public function index() {
		$this->load->model('admin/SetupModel');
		$this->load->model('MaterialsModel');
		$this->load->model('Companies');
		$this->load->model('RegionsModel');
                $this->load->model('DistributioncentersModel');
		
		$this->assigns['data'] = new stdClass();
		$this->assigns['containerTypeOptions'] = $this->SetupModel->getContainerTypeOptions();
		$this->assigns['data']->unitOptions = $this->unitOptions;
				
		$this->assigns['data']->MaterialsList = $this->MaterialsModel->getListForSelect();
		$this->assigns['data']->CompaniesList = $this->Companies->getListForSelect();
                $this->assigns['data']->DistributionCentersList = $this->DistributioncentersModel->getListForSelect();
		$this->assigns['data']->distributionCenterIdOptions = $this->DistributioncentersModel->getAllDC();		
		
		$this->load->view('admin/setup', $this->assigns);
	}
	
	public function getContainerContent() {
		$this->load->model('admin/SetupModel');
		
		$dataContainerTable = $this->SetupModel->getContainerList();
		$content = '
		<table class="display inTabs">
			<thead><tr>
				<th>Name</th>
				<th>Type</th>
				<th>weightInLbs</th>
				<th>Over 8 Yards</th>
				<th class="centered">-</th>
			</tr></thead>';
		foreach($dataContainerTable as $item) {
			$content .= '
			<tr id="containerId_'.$item->id.'">
				<td class="containerName">'.$item->name.'</td>
				<td class="containerType">'.$item->containerType.'</td>
				<td class="containerWeightInLbs">'.$item->weightInLbs.'</td>
				<td class="containerOver8Yards">'.($item->over8Yards==1 ? 'Y' : 'N').'</td>
				<td style="text-align: center;"><a href="javascript: void(0);" onclick="editContainer('.$item->id.');">Edit</a>&nbsp;|&nbsp;<a href="javascript: void(0);" onclick="deleteContainer('.$item->id.');">Delete</a></td>
			</tr>';		
		}
		
		$content .= '</table>';
		
		echo $content;
	}
	
	public function getMaterialContent() {
		$this->load->model('admin/SetupModel');
				
		$dataMaterialTable = $this->SetupModel->getMaterialList();
		$content = '
		<table class="display inTabs">
			<thead><tr>
				<th>Name</th>
				<th>Energy Saves</th>
				<th>CO2 Saves</th>
				<th>Quickbooks</th>
				<th>Hazardous</th>
				<th>Unit of measure</th>
				<th class="centered">-</th>
			</tr></thead>';
		foreach($dataMaterialTable as $item) {
			$content .= '
			<tr id="materialId_'.$item->id.'">
				<td class="materialName">'.$item->name.'</td>
				<td class="materialEnergySaves">'.$item->EnergySaves.'</td>
				<td class="materialCO2Saves">'.$item->CO2Saves.'</td>
				<td class="materialQuickbooks">'.($item->quickbooks==null?'':$item->quickbooks).'</td>
				<td class="materialIsHazardous">'.($item->isHazardous?'Yes':'No').'<input type="hidden" class="hidmaterialIsHazardous" value="'.$item->isHazardous.'"></td> 
				<td class="materialUnit">'.(($item->unit==null || $item->unit==0)?'':$this->unitOptions[$item->unit]).'<input type="hidden" class="hidmaterialUnit" value="'.(($item->unit==null)?'0':$item->unit).'"></td> 
				<td style="text-align: center;"><a href="javascript: void(0);" onclick="editMaterial('.$item->id.');">Edit</a>&nbsp;|&nbsp;<a href="javascript: void(0);" onclick="deleteMaterial('.$item->id.');">Delete</a></td>
			</tr>';		
		}
		
		$content .= '</table>';
		
		echo $content;
	}	
	
	public function getPurposeContent() {
		$this->load->model('admin/SetupModel');
		
		$dataPurposeTable = $this->SetupModel->getPurposeList();
		$content = '
		<table class="display inTabs">
			<thead><tr>
				<th>Name</th>
				<th class="centered">-</th>
			</tr></thead>';
		foreach($dataPurposeTable as $item) {
			$content .= '
			<tr id="purposeId_'.$item->id.'">
				<td class="purposeName">'.$item->name.'</td>
				<td style="text-align: center;"><a href="javascript: void(0);" onclick="editPurpose('.$item->id.');">Edit</a>&nbsp;|&nbsp;<a href="javascript: void(0);" onclick="deletePurpose('.$item->id.');">Delete</a></td>
			</tr>';		
		}
		
		$content .= '</table>';
		
		echo $content;
	}	

	public function getFeetypeContent() {
		$this->load->model('admin/SetupModel');
		
		$dataPurposeTable = $this->SetupModel->getFeetypeList();
		$content = '
		<table class="display inTabs">
			<thead><tr>
				<th>Name</th>
				<th>Quickbooks</th>
				<th class="centered">-</th>
			</tr></thead>';
		foreach($dataPurposeTable as $item) {
			$content .= '
			<tr id="feetypeId_'.$item->id.'">
				<td class="feetypeName">'.$item->name.'</td>
				<td class="feetypeQuickbooks">'.$item->quickbooks.'</td>
				<td style="text-align: center;"><a href="javascript: void(0);" onclick="editFeetype('.$item->id.');">Edit</a>&nbsp;|&nbsp;<a href="javascript: void(0);" onclick="deleteFeetype('.$item->id.');">Delete</a></td>
			</tr>';		
		}
		
		$content .= '</table>';
		
		echo $content;
	}
	
	public function getMarketRatesContent() {
		$this->load->model('admin/SetupModel');
		$this->load->model('MaterialsModel');
		$this->load->model('Companies');
		$this->load->model('RegionsModel');
                $this->load->model('DistributioncentersModel');
		$this->load->helper('dates');
				
		$MaterialsList = $this->MaterialsModel->getListForSelect();
		$CompaniesList = $this->Companies->getListForSelect();
                $DistributionCentersList = $this->DistributioncentersModel->getListForSelect();

		$startDate = null;
		$distributionCenterId = null;
		if($this->input->get('filterStartDate')!='') {
		    $startDate = USToSQLDate($this->input->get('filterStartDate'));
		}
		if($this->input->get('filterDistributionCenterId')!='') {
		    $distributionCenterId = intval($this->input->get('filterDistributionCenterId'));
		}
		$iDisplayLength = intval($this->input->get('iDisplayLength'));
		$iDisplayStart = intval($this->input->get('iDisplayStart'));

		$dataPurposeTable = $this->SetupModel->getMarketRatesList($iDisplayStart, $iDisplayLength, $startDate, $distributionCenterId);

		$ajaxData = array();
		
		foreach ($dataPurposeTable['data'] as $item) {
			$ajaxData[] = array(
				'DT_RowId' => 'marketratesId_'.$item->id,
				'DT_RowClass' => 'gradeA',			
				'<span class="marketratesMaterial">'.((empty($item->materialId)) ? '' : $MaterialsList[$item->materialId]).'<input type="hidden" class="hidmarketratesMaterial" value="'.(empty($item->materialId)?'0':$item->materialId).'"></span>',
				'<span class="marketratesDistributionCenter">'.((!isset($DistributionCentersList[$item->distributionCenterId])) ? '' : $DistributionCentersList[$item->distributionCenterId]).'<input type="hidden" class="hidmarketratesDistributionCenter" value="'.(empty($item->distributionCenterId) ? '0' : $item->distributionCenterId).'"></span>',
				'<span class="marketratesCompany">'.(empty($item->companyId) ? '' : $CompaniesList[$item->companyId]).'<input type="hidden" class="hidmarketratesCompany" value="'.(empty($item->companyId)?'1':$item->companyId).'"></span>',
				'<span class="marketratesStartDate">'.(empty($item->startDate) ? '' : SQLToUSDate($item->startDate)).'</span>',
				'<span class="marketratesStopDate">'.(empty($item->stopDate) ? '' : SQLToUSDate($item->stopDate)).'</span>',
				'<span class="marketratesInvoiceRate">'.(empty($item->invoiceRate) ? '' : $item->invoiceRate).'</span>',
				'<span class="marketratesPORate">'.(empty($item->poRate) ? '' : $item->poRate).'</span>',
				'<a href="javascript: void(0);" onclick="editMarketRates('.$item->id.');">Edit</a>&nbsp;|&nbsp;<a href="javascript: void(0);" onclick="deleteMarketRates('.$item->id.');">Delete</a>'
			);
		}
		
		header('Content-type: application/json');
		echo json_encode(array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $dataPurposeTable['records'],//$data['records']
			'iTotalDisplayRecords' => $dataPurposeTable['records']//$data['records']
		));		
	}
	
	public function saveContainer() {
		$this->load->model('admin/SetupModel');
		
		if (intval($this->input->post('containerId'))>0) {
			$this->SetupModel->updateContainer($_POST);
		} else {
			$this->SetupModel->addContainer($_POST);
		}
	}
	
	public function saveMaterial() {
		$this->load->model('admin/SetupModel');
				
		if (intval($this->input->post('materialId'))>0) {
			$this->SetupModel->updateMaterial($_POST);
		} else {
			$this->SetupModel->addMaterial($_POST);
		}
	}
	
	public function savePurpose() {
		$this->load->model('admin/SetupModel');
		
		if (intval($this->input->post('purposeId'))>0) {
			$this->SetupModel->updatePurpose($_POST);
		} else {
			$this->SetupModel->addPurpose($_POST);
		}
	}	
	
	public function saveFeetype() {
		$this->load->model('admin/SetupModel');
		
		if (intval($this->input->post('feetypeId'))>0) {
			$this->SetupModel->updateFeetype($_POST);
		} else {
			$this->SetupModel->addFeetype($_POST);
		}
	}
	
	public function saveMarketRates() {
		$this->load->model('admin/SetupModel');
		$this->load->helper('dates');
		
		$_POST['startDate'] = USToSQLDate($_POST['startDate']);
		$_POST['stopDate'] = USToSQLDate($_POST['stopDate']);
		
		if (intval($this->input->post('marketratesId'))>0) {
			$this->SetupModel->updateMarketRates($_POST);
		} else {
			$this->SetupModel->addMarketRates($_POST);
		}
	}	
	
	public function deleteContainer() {
		$this->load->model('admin/SetupModel');
		
		$containerId = intval($this->input->post('containerId'));
		if ($containerId>0) {
			$this->SetupModel->deleteContainer($containerId);
		} 
	}
	
	public function deleteMaterial() {
		$this->load->model('admin/SetupModel');
		
		$materialId = intval($this->input->post('materialId'));
		if ($materialId>0) {
			$this->SetupModel->deleteMaterial($materialId);
		} 
	}
	
	public function deletePurpose() {
		$this->load->model('admin/SetupModel');
		
		$purposeId = intval($this->input->post('purposeId'));
		if ($purposeId>0) {
			$this->SetupModel->deletePurpose($purposeId);
		} 
	}
	
	public function deleteFeetype() {
		$this->load->model('admin/SetupModel');
		
		$feetypeId = intval($this->input->post('feetypeId'));
		if ($feetypeId>0) {
			$this->SetupModel->deleteFeetype($feetypeId);
		} 
	}	
	
	public function deleteMarketRates() {
		$this->load->model('admin/SetupModel');
		
		$marketratesId = intval($this->input->post('marketratesId'));
		if ($marketratesId>0) {
			$this->SetupModel->deleteMarketRates($marketratesId);
		} 
	}	
}

/* End of file Reports.php */
/* Location: ./application/controllers/admin/Setup.php */
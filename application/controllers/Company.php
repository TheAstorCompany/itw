<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/Front.php';

class Company extends Front {
	private $forOptions = array(
		1 => 'All DCs and Store Regions',
		2 => 'Only DCs',
		3 => 'Only Store Regions',
		4 => 'State',
	);
	
	public function __construct() {
	    parent::__construct();
	    redirect('/Stores');
	}
			
	public function DashBoard() {
		$this->index();
	}
	
	public function index() {
		$this->load->model('CompanyModel');
		
		$monthData = $this->CompanyModel->getCurrentMonthData();
		$priorDate = $this->CompanyModel->getLastYearData();
		
		$currentPeriod = 1;
		$currentPerformance = 1;
		
		if ($this->input->post('period')) {
			$currentPeriod = (int)$this->input->post('period');
		}
		
		if ($this->input->post('performance')) {
			$currentPerformance = (int)$this->input->post('performance');
		}
		
		switch ($currentPeriod) {
			case 1:
				$periodData = $this->CompanyModel->getPriorMonthData();
				break;
			case 2:
				$periodData = $this->CompanyModel->getPriorQuarterData();
				break;
			case 3:
				$periodData = $this->CompanyModel->getSixMonthsData();
				break;
			case 4:
				$periodData = $this->CompanyModel->getLastYearData();
				break;
			default:
				die('error!');
				break;				
		}
		
		
		switch ($currentPerformance) {
			case 1:
				$performanceData = $this->CompanyModel->wasteDiversion();
				break;
			case 2:
				$performanceData = $this->CompanyModel->recyclingRebates();
				break;
			case 3:
				$performanceData = $this->CompanyModel->netCost();
				break;
			case 4:
				$performanceData = $this->CompanyModel->services();
				break;
			default:
				die('not implemented yet');
				break;
		}
		

		//////////////////////////////////////
		$this->assigns['monthData'] = $monthData;
		$this->assigns['periodData'] = $periodData;
		$this->assigns['requestServices'] = $this->CompanyModel->getServiceRequests('','');
		$this->assigns['costOfServices'] = $this->CompanyModel->getCostOfServicesChartInfo();
		
		$this->assigns['performanceData'] = $performanceData;
		
		$this->assigns['trend'] = $this->CompanyModel->wasteRecyclingTrendsChart();
		
		$this->assigns['data'] = new Placeholder();
		$this->assigns['data']->periodOptions = array(
			1 => 'See Prior Month',
			2 => 'See Prior Quarter',
			3 => 'See Last Six Months',
			4 => 'See Last Year',
		);
		
		$this->assigns['data']->performanceOptions = array(
			1 => 'Waste Diversion',
			2 => 'Recycling Rebates',
			3 => 'Net Cost',
			4 => 'Services',
		);

		$this->load->view("company/dashboard", $this->assigns);
	}
	
	public function Waste() {
		$_POST = $_GET;
		$this->load->helper('dates');
		$this->load->model('CompanyModel');
		$this->load->model('States');
		$this->assigns['data'] = new Placeholder();
		$this->assigns['data']->forOptions = $this->forOptions;
					
		$byStateArray = $this->States->getListForSelect();
		$this->assigns['data']->byState = array(0=>"All States")+$byStateArray;
				
		$startDate = $this->input->get('from');
		$endDate = $this->input->get('to');
		$for = $this->input->get('for');
		$bystate = $this->input->get('bystate');
		
		if (!($startDate && $endDate)) {
			$startDate =  date('m') . '/01/' . date('Y');
			$endDate = date('m/d/Y');
		}
		
		if (!$for) {
			$for = 1;
		}
				
		if (!$bystate) {
			$bystate = 0;
		}
		
		$_POST['for'] = $for;
		
		$this->assigns['result'] = $this->CompanyModel->wasteReportByDateRange($startDate, $endDate, $for, $bystate);
		
		if ($this->input->get('export') == false) {
			$this->assigns['wasteTrends'] = $this->CompanyModel->wasteTrendsChart();
						
			if ($this->input->get('print') == false) {
				$this->load->view("company/waste", $this->assigns);
			} else {
				$this->load->view("company/waste_print", $this->assigns);
			}
		} else {
			//export
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
			fputcsv($file, array(
				'Type',
				'Name',
				'State',
				'SqFt',
				'Waste (Tons)',
				'Recycling (Tons)',
				'Total Tonage',
				'Cost',
			));
			
			foreach ($this->assigns['result']['list'] as $row) {
				fputcsv($file, array(
					$row->type,
					$row->name,
					$row->state,
					number_format($row->squareFootage, 2),
					number_format($row->waste, 2),
					number_format($row->recycling, 2),
					number_format(($row->recycling + $row->waste), 2),
					number_format($row->cost, 2),
				));
			}
			
      		rewind($file);
      		$csv = stream_get_contents($file);
			fclose($file);
			
			force_download('CompanyWaste-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
	}
	
	public function Recycling() {
	
		$_POST = $_GET;
		$this->load->helper('dates');
		$this->load->model('CompanyModel');
		
		$this->assigns['data'] = new Placeholder();
		$this->assigns['data']->forOptions = $this->forOptions;
		
		$startDate = $this->input->get('from');
		$endDate = $this->input->get('to');
		$for = $this->input->get('for');
		
		if (!($startDate && $endDate)) {
			$startDate =  date('m') . '/01/' . date('Y');
			$endDate = date('m/d/Y');
		}
		
		if (!$for) {
			$for = 1;
		}
		
		$_POST['for'] = $for;
		
		$this->assigns['result'] = $this->CompanyModel->recyclingReportByDateRange($startDate, $endDate, $for);
		
		if ($this->input->get('export') == false) {
			$this->assigns['recyclingTrends'] = $this->CompanyModel->recyclingTrendsChart();
			
			if ($this->input->get('print') == false) {
				$this->load->view("company/recycling", $this->assigns);
			} else {
				$this->load->view("company/recycling_print", $this->assigns);
			}
		} else {
			//export
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
			fputcsv($file, array(
				'Type',
				'Name',
				'SqFt',
				'Cardboard',
				'Aluminum',
				'Film',
				'Plastic',
				'Trees',
				'Landfill',
				'Energy (KWh)',
				'Co2 (Tons)',
				'Rebate'
			));
			
			foreach ($this->assigns['result']['list'] as $row) {
				fputcsv($file, array(
					$row->type,
					$row->name,
					$row->sqft,
					$row->cardboard,
					$row->aluminum,
					$row->film,
					$row->plastic,
					$row->trees,
					'-',
					$row->kwh,
					$row->co2,
					$row->rebate,
				));
			}
			
      		rewind($file);
      		$csv = stream_get_contents($file);
			fclose($file);
			
			force_download('CompanyRecycling-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
	}
	
	public function Cost() {
		$this->load->view("empty", $this->assigns);
		return;
	
		$_POST = $_GET;
		$this->load->helper('dates');
		$this->load->model('CompanyModel');
		
		$this->assigns['data'] = new Placeholder();
		$this->assigns['data']->forOptions = $this->forOptions;
		
		$startDate = $this->input->get('from');
		$endDate = $this->input->get('to');

		$for = $this->input->get('for');
		if (!$for) {
			$for = 1;
		}
		
		if (!($startDate && $endDate)) {
			$startDate =  date('m') . '/01/' . date('Y');
			$endDate = date('m/d/Y');
		}
		
		
		
		$_POST['for'] = $for;
		$result = $this->CompanyModel->getCostSavingData($startDate, $endDate, $for);

		if ($this->input->get('export') == false) {
			
			$this->assigns['chart1'] = $result['cost'];
			$this->assigns['chart2'] = $result['savings'];
			$this->assigns['chart3'] = $result['trend'];		
			$this->assigns['table'] = $result['table'];
	
			
			if ($this->input->get('print') == false) {
				$this->load->view("company/cost", $this->assigns);
			} else {
				$this->load->view("company/cost_print", $this->assigns);
			}
			
		} else {
			//export
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			fputcsv($file, array(
				'Type',
				'Name',
				'SqFt',
				'Total Tonnage',
				'Waste Service',
				'Waste Equipment Fee',
				'Waste Haul Fee',	
				'Waste Disposal Fee',
				'Recycling Rebate',
				'Other Fee',
				'Net'
			));
			
			foreach ($result['table'] as $item) {
				$data = array(
					$item->type,
            		$item->region,
            		number_format($item->squareFootage,2),
            		number_format($item->TotalTonnage,2),
            		number_format($item->WasteService,2),
            		number_format($item->WasteEquipmentFee,2),
            		number_format($item->WasteHaulFee,2),
            		number_format($item->WasteDisposalFee,2),
            		number_format($item->RecyclingRebate,2),
            		number_format($item->OtherFee,2),
            		number_format($item->net,2)
            	);
                            	
            	fputcsv($file, $data);            	
				
			}
			
      		rewind($file);
      		$csv = stream_get_contents($file);
			fclose($file);
			
			force_download('CompanyCostsSavings-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}

	}
	
	public function Services() {
		$_POST = $_GET;
		$this->load->helper('dates');
		$this->load->model('CompanyModel');
		
		$this->assigns['data'] = new Placeholder();
		$this->assigns['data']->forOptions = $this->forOptions;
		
		$startDate = $this->input->get('from');
		$endDate = $this->input->get('to');
		$for = $this->input->get('for');
		
		if (!($startDate && $endDate)) {
			$startDate =  date('m') . '/01/' . date('Y');
			$endDate = date('m/d/Y');
		}
		
		if (!$for) {
			$for = 1;
		}
		
		$_POST['for'] = $for;
		
		$display = $this->input->get('display')?$this->input->get('display'):1; // By region
		$this->assigns['data']->display = $display;
		
		$result = $this->CompanyModel->getServicesData($startDate, $endDate, $display);
		$this->assigns['table'] = $result['table'];	
		
		if ($this->input->get('export') == false) {
			
		
			$this->assigns['chart1'] = $result['containers'];
			$this->assigns['chart2'] = $result['services'];				
			$this->assigns['chart3'] = $result['frequency'];
			
			if ($this->input->get('print') == false) {
				$this->load->view("company/services", $this->assigns);
			} else {
				$this->load->view("company/services_print", $this->assigns);
			}
		} else {
			//export
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			fputcsv($file, array(
				'Type',
				'Name',
				'Container',
				'Duration',
				'Frequency',
				'Cost',	
				'Last Updated',
			));
			
			foreach ($this->assigns['table'] as $item) {
				$data = array(
            		($item->locationType == 'DC') ? 'DC':'Store',
            		$item->name,
            		$item->container,
            		$item->duration,
            		$item->frequency,
            		number_format($item->cost,2),
            		$item->lastUpdated
            	);
                            	
            	fputcsv($file, $data);            	
				
			}
			
      		rewind($file);
      		$csv = stream_get_contents($file);
			fclose($file);
			
			force_download('CompanyServices-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
	}
	
	
	public function ReportsAllDCWaste() {
		$this->load->view("company/reports_alldcwaste.php", $this->assigns);
	}
	public function reportsAllDCWasteCSV() {
		header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=reports_alldcwaste.csv");
        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: binary");
        echo file_get_contents('application/html/reports_alldcwaste.csv');
	}
	
}

/* End of file front.php */
/* Location: ./application/controllers/front.php */

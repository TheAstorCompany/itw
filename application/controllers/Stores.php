<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/Front.php';

class Stores extends Front {

	
	public function DashBoard() {
		$this->index();
	}
	
	public function index() {
		$this->load->model('StoreModel');
		$this->load->model('admin/SupportRequestModel');
		$this->load->model('CompanyModel');
		$this->load->library('GMapsHelper');	

		$store_id = 0;
        if(isset($_GET['store_id'])) {
            $store_id = intval($_GET['store_id']);
        }	

		$this->assigns['periodPriorMonthData'] = $this->StoreModel->getPriorMonthData($store_id);
		$this->assigns['period2MonthsBackData'] = $this->StoreModel->getPrior2MonthsBackData($store_id);		

		$this->assigns['SRChartInfo'] = $this->StoreModel->getServiceRequestChartInfo();
		$this->assigns['CoSChartInfo'] = $this->CompanyModel->getCostOfServicesChartInfo();

		$qqq = $this->SupportRequestModel->getLast4Weeks('STORE');
		$calls = array();
		$open_service_requests = 0;
		$total_service_requests = 0;
		
		foreach($qqq as $week=>$qq) {
			$k = date('m/d', $week);
			$calls[$k] = array('open'=>0, 'closed'=>0, 'total'=>0);
			foreach($qq as $q) {
				$calls[$k]['total']++;
				$total_service_requests++;
				if($q->complete != '1') {
					$calls[$k]['open']++;
					$open_service_requests++;
				} else {
					$calls[$k]['closed']++;
				}
			}
		}
		
		$this->assigns['calls'] = $calls;
		$this->assigns['open_service_requests'] = $open_service_requests;
		$this->assigns['total_service_requests'] = $total_service_requests;

		$this->assigns["allStores"] = $this->StoreModel->getAllStores();
		$this->assigns['store_id'] = $store_id;
		
		$this->load->view("stores/dashboard", $this->assigns);
	}
	
	public function Waste() {
		$_POST = $_GET;
		$this->load->helper('dates');
		$this->load->model('StoreModel');
		$this->load->model('States');
		$this->assigns['data'] = new Placeholder();
		$startDate = $this->input->get('from');
		$endDate = $this->input->get('to');
		if (!($startDate && $endDate)) {
			$startDate = date('m') . '/01/' . date('Y');
			$endDate = date('m/d/Y');
		}
		
		if ($this->input->get('diversion_report') == 1) {
			//export
			$this->load->helper('download');
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			fputcsv($file, array(
				'Store #',
				'Trash Bin Size',
				'Trash Frequency',
				'Trash Weight in Tons',
				'Cardboard Bin Size',
				'Cardboard Frequency',
				'Cardboard Weight in Tons',	
				'Recycling Material',
				'Recycling Bin Size',
				'Recycling Frequency',
				'Recycling Weight in Tons',
				'Total Garbage Weight in Tons',
				'Total Recycling Weight in Tons',
				'Diversion %'
			));
			
			$startDate = USToSQLDate($startDate);
			$endDate = USToSQLDate($endDate);
			
			$rows = $this->StoreModel->getDiversionReportData($startDate, $endDate);
			foreach ($rows as $item) {
				$total_garbage = floatval($item['weightInTonsTrash']);
				$total_recycling = floatval($item['weightInTonsCardboard'] + $item['weightInTonsRecycling']);

				$data = array(
					$item['locationName'],
					$item['containerNameTrash'],
					$item['scheduleNameTrash'],
					$item['weightInTonsTrash']=='' ? '' : number_format($item['weightInTonsTrash'], 2),
					$item['containerNameCardboard'],
					$item['scheduleNameCardboard'],
					$item['weightInTonsCardboard']=='' ? '' : number_format($item['weightInTonsCardboard'], 2),
					$item['materialNameRecycling'],
					$item['containerNameRecycling'],
					$item['scheduleNameRecycling'],
					$item['weightInTonsRecycling']=='' ? '' : number_format($item['weightInTonsRecycling'], 2),					
					number_format($total_garbage, 2),
					number_format($total_recycling, 2),
					($total_recycling + $total_garbage)==0 ? '0%' : floor(($total_recycling / ($total_recycling + $total_garbage)*100)).'%'
				);

				fputcsv($file, $data);            					
			}
			
			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			
			force_download('DiversionReport_' . $startDate . '_to_' . $endDate . '.csv', $csv);		
		} elseif ($this->input->get('export') == false) {
						
			$this->assigns['data']->forOptions = array(
			    1 => 'By Regions',
			    'By District',
			    'By District (East Region)',
			    'By District (Southeast Region)',
			    'By District (Midwest Region)',
			    'By District (South Region)',
			    'By District (West Region)',
			    'By State'
			);
			
			$byStateArray = $this->States->getListForSelect();								
			$this->assigns['data']->byState = array(0=>"All States") + $byStateArray;
		
			$display = $this->input->get('display') ? $this->input->get('display') : 1; // By region
			$this->assigns['data']->display = $display;
			$bystate = $this->input->get('bystate') ? $this->input->get('bystate') : 0; // All State
			$this->assigns['data']->bystate = $bystate;

			$this->assigns['chart2'] = $this->StoreModel->getWasteData($startDate, $endDate, $display, $bystate);
			
			if ($this->input->get('print') == false) {				
				$this->load->view("stores/waste", $this->assigns);
			} else {
				$this->load->view("stores/wastePrint", $this->assigns);
			}
		} else {
			//export
			$this->load->helper('download');
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
			fputcsv($file, array(
                'Location',
				'District',
				'State',
				'24H',
				'SqFt',
				'Waste (Tons)',	
				'Cardboard (Tons)',
				'Commingle (Tons)',
                'Other (Tons)',
				'Cost'
			));

            $result = $this->WasteTable(true);
			foreach ($result['data'] as $item) {
			    $data = array(
                    $item->location,
                    $item->district,
                    $item->state,
                    $item->open24hours ? 'Y' : 'N',
                    number_format($item->squareFootage, 0),
                    number_format($item->WasteTons, 2),
                    number_format($item->CardboardTons, 2),
                    number_format($item->CommingleTons, 2),
                    number_format($item->OtherTons, 2),
                    '$'.number_format($item->cost, 2)
			    );

			    fputcsv($file, $data);
			}

			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			
			force_download('StoreWaste-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
	}
	
	public function WasteTrend() {
	    $_POST = $_GET;
	    $this->load->helper('dates');
	    $this->load->model('StoreModel');
	    $this->load->model('States');
	    $this->assigns['data'] = new Placeholder();
	    $startDate = $this->input->get('from');
	    $endDate = $this->input->get('to');
	    if (!($startDate && $endDate)) {
		    $startDate = date('m') . '/01/' . date('Y');
		    $endDate = date('m/d/Y');
	    }	    
	    $display = $this->input->get('display') ? $this->input->get('display') : 1; // By region
	    $this->assigns['data']->display = $display;
	    $bystate = $this->input->get('bystate') ? $this->input->get('bystate') : 0; // All State
	    $this->assigns['data']->bystate = $bystate;

	    $result = $this->StoreModel->getWasteTrendData2($startDate, $endDate, $display, $bystate);
	    
	    $data = array();
	    $data[] = $result['months'];

	    foreach($result['trend'] as $month=>$v) {
            $r = array();
            array_push($r, $month);

            array_push($r, $v['Waste']);
            array_push($r, $v['Cardboard']);
            array_push($r, $v['Commingle']);
            array_push($r, $v['Other']);

            array_push($data, $r);
	    }
	    
	    header('Content-type: application/json');
	    echo json_encode($result);	    
	}
	
	public function WasteTable($is_export=false) {
	    $_POST = $_GET;
	    $this->load->helper('dates');
	    $this->load->model('StoreModel');
	    $this->load->model('States');
	    $this->assigns['data'] = new Placeholder();
	    $startDate = $this->input->get('from');
	    $endDate = $this->input->get('to');
	    if (!($startDate && $endDate)) {
		    $startDate = date('m') . '/01/' . date('Y');
		    $endDate = date('m/d/Y');
	    }	    
	    $display = $this->input->get('display') ? $this->input->get('display') : 1; // By region
	    $this->assigns['data']->display = $display;
	    $bystate = $this->input->get('bystate') ? $this->input->get('bystate') : 0; // All State
	    $this->assigns['data']->bystate = $bystate;

	    $sortColumn = 'CAST(`location` AS UNSIGNED)';
	    $sortDir = 'DESC';
		
	    if ($this->input->get('iSortingCols') > 0) {
		    if ($this->input->get('bSortable_0')) {
			    switch ($this->input->get('iSortCol_0')) {
				    case 0:
					    $sortColumn = 'location';
					    break;
				    case 1:
					    $sortColumn = 'district';
					    break;
				    case 2:
					    $sortColumn = 'state';
					    break;
				    case 3:
					    $sortColumn = 'open24hours';
					    break;
				    case 4:
					    $sortColumn = 'squareFootage';
					    break;
				    case 5:
					    $sortColumn = 'WasteTons';
					    break;
				    case 6:
					    $sortColumn = 'CardboardTons';
					    break;
				    case 7:
					    $sortColumn = 'CommingleTons';
					    break;					
				    case 8:
					    $sortColumn = 'OtherTons';
					    break;
				    case 9:
					    $sortColumn = 'cost';
					    break;

			    }

			    if ($this->input->get('sSortDir_0') == 'asc') {
				    $sortDir = 'ASC';
			    }
		    }		
	    }

        if($is_export) {
            $result = $this->StoreModel->getWasteTableData2($startDate, $endDate, $display, $bystate, 0, 999999, $sortColumn, $sortDir);
            return $result;
        }

        $result = $this->StoreModel->getWasteTableData2($startDate, $endDate, $display, $bystate, $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $sortColumn, $sortDir);
	    $ajaxData = array();
	    
	    foreach ($result['data'] as $item) {
            $ajaxData[] = array(
                'DT_RowClass' => 'gradeA',
                sprintf("<a href='%sStoreInfo/Invoices/%d'>%s</a>", base_url(), $item->id, $item->location),
                $item->district,
                $item->state,
                $item->open24hours ? 'Y' : 'N',
                number_format($item->squareFootage, 0),
                number_format($item->WasteTons, 2),
                number_format($item->CardboardTons, 2),
                number_format($item->CommingleTons, 2),
                number_format($item->OtherTons, 2),
                '$'.number_format($item->cost, 2)
            );
	    }	    

	    echo json_encode(array(
            'logs' => $result['logs'],
		    'aaData' => $ajaxData,
		    'iTotalRecords' => $result['records'],
		    'iTotalDisplayRecords' => $result['records']
		));	    
	}	
	
	public function Recycling() {
		$_POST = &$_GET;
		$this->load->helper('dates');
		$this->load->model('StoreModel');
		$this->load->model('States');
		$startDate = null;
		$endDate = null;		
		$material = null;
		$this->assigns['data'] = new Placeholder();
		
		$byStateArray = $this->States->getListForSelect();								
		$this->assigns['data']->byState = array(0=>"All States") + $byStateArray;
		
		$this->assigns['data']->displayOptions = array(
			1 => 'By Regions',
	      	'By District',
          	'By District (East Region)',
          	'By District (Southeast Region)',
          	'By District (Midwest Region)',
          	'By District (South Region)',
          	'By District (West Region)',
		    'By State'
		);
		$this->assigns['data']->forOptions = array(
		    1=>'Cardboard',
		    'Film',
		    'Aluminum',
		    'Plastic',
		    //'Rebates',
		    'Trees Saved',
		    7=>'KWh Saved',
		    'CO2 Reduced',
		    //'Landfill Reduced'
		); 

		$startDate = $this->input->get('from');
		$endDate = $this->input->get('to');
		
		$display = $this->input->get('display')?$this->input->get('display'):1; // By region
		$this->assigns['data']->display = $display;
		$chartFor = $this->input->get('for')?$this->input->get('for'):1; //Cardboard
		$bystate = $this->input->get('bystate')?$this->input->get('bystate'):0; // All State
		$this->assigns['data']->bystate = $bystate;
		 
		
		if (!($startDate && $endDate)) {
			$startDate =  date('m') . '/01/' . date('Y');
			$endDate = date('m/d/Y');
		}
		$this->assigns['data']->from = $startDate;
		$this->assigns['data']->to = $endDate;
		if($for = $this->input->get("for")) {			
			$this->assigns['data']->for = $for;
		} else {
			$this->assigns['data']->for = 1;
		}
		if($report = $this->input->get("report")) {			
			$this->assigns['data']->report = $report;
		} else {
			$this->assigns['data']->report = 1;
		}		
		$result = $this->StoreModel->getRecyclingData($startDate, $endDate, $display, $chartFor, $bystate);		
		$this->assigns['mainMaterials'] = Array(
			1=>'Cardboard',
			3=>'Aluminum',
			8=>'Film',
			5=>'Plastic',
			7=>'Trees',
			1000=>'Landfill'
		);
		
		$this->assigns['chart1'] = $result['rebates'];
		$this->assigns['chart2'] = $result['recycling'];
		$this->assigns['chart3'] = $result['trend'];
		$this->assigns['table'] = $result['table'];
		if($this->input->get("print")) {
			$this->load->view("stores/recyclingPrint", $this->assigns);
		} else if ($this->input->get("export")) {
			
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
			
			$colums = Array('Region', 'District', 'Location', '24H', 'SqFt');
			foreach($this->assigns['mainMaterials'] as $temp) {
				$colums[] = $temp;
			}
			$colums[] = "Energy (KWh)";
			$colums[] = "Co2 (Tons)";
			$colums[] = "Rebate";

			fputcsv($file, $colums);
				
			$data = array();
			
            foreach($this->assigns['table'] as $item) {
            	
            	$data = array(
            		$item->region,
            		$item->district,
            		$item->location,
            		$item->open24hours?'Y':'N',
            		number_format($item->squareFootage,2)
            	);
            	
            	foreach($this->assigns['mainMaterials'] as $matId=>$mat) {
            		$data[]  = isset($item->materials[$matId])?$item->materials[$matId]:'';            		
            	}
            	
            	$data[] = number_format($item->EnergySaves,2);
            	$data[] = number_format($item->CO2Saves,2);
            	$data[] = number_format($item->rebate,2);
            	fputcsv($file, $data);
            }
			
			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('Stores-Recycling-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		} else {
			$this->load->view("stores/recycling", $this->assigns);
		}
		
	}
	/*
	 * Cost/Saving
	 */
	public function Cost() {
		$this->load->view("empty", $this->assigns);
		return;
		
		$_POST = $_GET;
		$this->load->helper('dates');
		$this->load->model('StoreModel');
		$this->load->model('States');
		$this->assigns['data'] = new Placeholder();
		$startDate = $this->input->get('from');
		$endDate = $this->input->get('to');
		$for = (int)$this->input->get('for') >= 1?(int)$this->input->get('for'):1;
				
		if (!($startDate && $endDate)) {
			$startDate =  date('m') . '/01/' . date('Y');
			$endDate = date('m/d/Y');
		}
		
		$display = $this->input->get('display')?$this->input->get('display'):1; // By region
		$this->assigns['data']->display = $display;
		$bystate = $this->input->get('bystate')?$this->input->get('bystate'):0; // All State
		$this->assigns['data']->bystate = $bystate;
		
		//$this->assigns['result'] = $this->StoreModel->wasteReportByDateRange($startDate, $endDate, $for);
		
		$result = $this->StoreModel->getCostSavingData($startDate, $endDate, $display, $bystate);			
		$this->assigns['chart1'] = $result['cost'];
		$this->assigns['chart2'] = $result['savings'];
		$this->assigns['chart3'] = $result['trend'];		
		$this->assigns['table'] = $result['table'];
			
		
		if ($this->input->get('export') == false) {
			//$this->assigns['wasteTrends'] = $this->CompanyModel->wasteTrendsChart();
			
			$byStateArray = $this->States->getListForSelect();								
			$this->assigns['data']->byState = array(0=>"All States") + $byStateArray;
			
			$this->assigns['data']->forOptions = array(
				1 => 'By Regions',
		      	'By District',
          		'By District (East Region)',
          		'By District (Southeast Region)',
          		'By District (Midwest Region)',
          		'By District (South Region)',
          		'By District (West Region)',
			    'By State'
			);
			
			if ($this->input->get('print') == false) {				
				$this->load->view("stores/cost", $this->assigns);
			} else {
				$this->load->view("stores/costPrint", $this->assigns);
			}
		} else {
			//export
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
			fputcsv($file, array(
				'Region',
				'District',
				'Location',
				'24H',
				'SqFt',
				'Total Tonnage',
				'Waste Service',
				'Waste Equipment Fee',
				'Waste Haul Fee	Waste',
				'Disposal Fee',
			 	'Recycling Rebate',
				'Other Fee',
				'Net'
			));
			
			foreach ($this->assigns['table'] as $item) {
				$data = array(
            		$item->region,
            		$item->district,
            		$item->location,
            		$item->open24hours?'Y':'N',
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
			
			force_download('StoreCost-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
	}
	/*
	 * Services
	 */
	public function Services() {
		$_POST = $_GET;
		$this->load->helper('dates');
		$this->load->model('StoreModel');
		$this->load->model('States');
		$this->assigns['data'] = new Placeholder();
		$for = (int)$this->input->get('for') >= 1?(int)$this->input->get('for'):1;
				
		
		$display = $this->input->get('display') ? $this->input->get('display') : 1; // By region
		$this->assigns['data']->display = $display;
		$bystate = $this->input->get('bystate') ? $this->input->get('bystate') : 0; // All State
		$this->assigns['data']->bystate = $bystate;
		
		$result = $this->StoreModel->getServicesData($display, $bystate);
		//echo "<pre>";	print_r($result); die();			
		$this->assigns['chart1'] = $result['containers'];
		$this->assigns['chart2'] = $result['services'];				
		$this->assigns['chart3'] = $result['frequency'];
		//echo "<pre>"; print_r($this->assigns['chart3']);echo "</pre>";
		$this->assigns['table'] = $result['table'];		
		
		if ($this->input->get('export') == false) {
			//$this->assigns['wasteTrends'] = $this->CompanyModel->wasteTrendsChart();
			
			$byStateArray = $this->States->getListForSelect();								
			$this->assigns['data']->byState = array(0=>"All States") + $byStateArray;
			
			$this->assigns['data']->forOptions = array(
				1 => 'By Regions',
		      	'By District',
          		'By District (East Region)',
          		'By District (Southeast Region)',
          		'By District (Midwest Region)',
          		'By District (South Region)',
          		'By District (West Region)',
			    'By State'
			);
			
			if ($this->input->get('print') == false) {				
				$this->load->view("stores/services", $this->assigns);
			} else {
				$this->load->view("stores/servicesPrint", $this->assigns);
			}
		} else {
			//export
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');			 	
			fputcsv($file, array(
				'Region',
				'District',
				'Location',
				'SqFt',
				'24H',				
				'Container',	
				'Frequency',
				'Cost'
			));
			
			foreach ($this->assigns['table'] as $item) {
				$data = array(
			    $item->region,
			    $item->district,
			    $item->location,
			    $item->squareFootage,
			    $item->open24hours?'Y':'N',
			    $item->container,
			    $item->frequency,
			    number_format($item->cost, 2)		           		            		
            		
            	);
            	fputcsv($file, $data);           	
				
			}
			
      		rewind($file);
      		$csv = stream_get_contents($file);
			fclose($file);
			
			force_download('StoreServices.csv', $csv);
		}		
	}
	/*
	 * List
	 */
	public function Lists() {
		$this->load->model('States');
		$this->assigns['data'] = new Placeholder();
	    		
		$byStateArray = $this->States->getListForSelect();								
		$this->assigns['data']->byState = array(0=>"All States") + $byStateArray;
	    
		$this->load->view("stores/list", $this->assigns);
	}
	
	public function ajaxList($type=null) {
	    	    
		$this->load->model('StoreModel', 'StoresModel');
		header('Content-type: application/json');
		
		$sortColumn = null;
		$sortDir = null;
		
		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'CAST(`location` AS UNSIGNED)';
						break;
					case 1:
						$sortColumn = 'CAST(`district` AS UNSIGNED)';
						break;
					case 2:
						$sortColumn = 'addressLine1';
						break;
					case 3:
						$sortColumn = 'city';
						break;
					case 4:
						$sortColumn = 'state';
						break;
					case 5:
						$sortColumn = 'postCode';
						break;
					case 6:
						$sortColumn = 'open24hours';
						break;
					case 7:
						$sortColumn = 'officeLocation';
						break;					
					case 8:
						$sortColumn = 'squareFootage';
						break;
					case 9:
						$sortColumn = 'diversion';
						break;
					case 10:
						$sortColumn = 'cost';
						break;
								
				}
				
				if ($this->input->get('sSortDir_0') == 'asc') {
					$sortDir = 'ASC';
				} else {
					$sortDir = 'DESC';
				}
			}		
		}
		
		$bystate = $this->input->get('filter_complete')?$this->input->get('filter_complete'):0;
				
		$data = $this->StoresModel->getList($this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $this->input->get('sSearch'), $sortColumn, $sortDir, $bystate);
		$ajaxData = array();
		
		foreach ($data['data'] as $item) {
			$ajaxData[] = array(
				'DT_RowClass' => 'gradeA',
				sprintf("<a href='%sStoreInfo/Invoices/%d'>%s</a>", base_url(), $item->id, $item->location),
				$item->district,
				$item->addressLine1,
				$item->city,
				$item->state,
				$item->postCode,
				$item->open24hours ? 'Y' : 'N',
				$item->officeLocation ? 'Y' : 'N',
				number_format($item->squareFootage, 0),
				number_format($item->diversion, 2) . "%",
				'$'.number_format($item->cost, 2)
			);
		}
		
		if($type=='csv')
		{
		    return array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		    );
		} else {
		    echo json_encode(array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		    ));
		}
	}
	
	public function csvList() {
		//export
		$this->load->helper('download');
		$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
		fputcsv($file, array(
			'Location',		    
			'District',
			'Address',
			'City',
			'State',
			'Zip',
			'24H',
			'Office Location',
			'SqFt',
			'Diversion',
			'Cost/Sqft'
		));
		$data = $this->ajaxList('csv');
		
		foreach ($data['aaData'] as $row) {
			fputcsv($file, array(
				strip_tags($row[0]),
				strip_tags($row[1]),
				strip_tags($row[2]),
				strip_tags($row[3]),
				strip_tags($row[4]),
				strip_tags($row[5]),
				strip_tags($row[6]),
				strip_tags($row[7]),
				strip_tags($row[8]),
				strip_tags($row[9]),
				'$'.strip_tags($row[10])	
			));
		}

		rewind($file);
		$csv = stream_get_contents($file);
		fclose($file);

		force_download('StoreList.csv', $csv);
	}
	
	public function Example() {
		$this->load->view("stores/example", $this->assigns);
	}
	
}

/* End of file front.php */
/* Location: ./application/controllers/front.php */
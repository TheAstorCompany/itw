<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/Front.php';

class DistributionCenters extends Front {

	/**
	 * 
	 * @var DistributioncentersModel
	 */
	public $distributioncentersmodel;
	
	
	public function __construct() {
		parent::__construct();
		$this->load->model("distributioncentersmodel");
	}
	
	public function DashBoard() {
		$this->index();
	}
	
	public function index() {
        $this->load->model('CompanyModel');
        $this->load->model('admin/SupportRequestModel');

        $distributioncenter_id = 0;
        if(isset($_GET['distributioncenter_id'])) {
            $distributioncenter_id = intval($_GET['distributioncenter_id']);
        }
                            
		$this->assigns["PriorMonthData"] = $this->distributioncentersmodel->getPrior2MonthsBackData($distributioncenter_id);
        $this->assigns["Prior2MonthsBack"] = $this->distributioncentersmodel->getPrior3MonthsBackData($distributioncenter_id);

		$this->assigns["ServiceRequests"] = $this->distributioncentersmodel->getServiceRequestChartInfo('2MonthsBack', $distributioncenter_id);
		$this->assigns["CostOfService"] = $this->distributioncentersmodel->getCostOfServicesChartInfo('2MonthsBack', $distributioncenter_id);

        $qqq = $this->SupportRequestModel->getLast4Weeks('DC', $distributioncenter_id);
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

        $this->assigns['AllDC'] = $this->distributioncentersmodel->getAllDC();
        $this->assigns['distributioncenter_id'] = $distributioncenter_id;

		$this->load->view("dc/dashboard", $this->assigns);
	}
	
	public function GetDashBoardData($data) {
		if($data == 2) {
			$this->assigns["CurrentMonthData"] = $this->distributioncentersmodel->getPriorQuarterData();
		} else if ($data == 3) {
			$this->assigns["CurrentMonthData"] = $this->distributioncentersmodel->getSixMonthsData();
		} else if ($data == 4) {
			$this->assigns["CurrentMonthData"] = $this->distributioncentersmodel->getLastYearData();
		} else {
			$this->assigns["CurrentMonthData"] = $this->distributioncentersmodel->getPriorMonthData();
		}
		$this->load->view("dc/common/dashboardData", $this->assigns);

	}
	
	public function Waste() {
		
		$this->load->helper('dates');
		
		$startDate = null;
		$endDate = null;

		if($this->input->get("from")) {
			$startDate = USToSQLDate($this->input->get("from"));
			$this->assigns["from"] = $this->input->get("from");
		} else {
            $sUSDate = date('m/d/Y', mktime(0, 0, 0, date("m")-1, 1, date("Y")));
            $startDate = USToSQLDate( $sUSDate);
            $this->assigns["from"] =  $sUSDate;
		}
		if($this->input->get("to")) {
			$endDate = USToSQLDate($this->input->get("to"));
			$this->assigns["to"] = $this->input->get("to");
		} else {
            $eUSDate = date('m/d/Y', mktime(0, 0, 0, date("m"), 0, date("Y")));
            $endDate = USToSQLDate($eUSDate);
            $this->assigns["to"] = $eUSDate;
		}
		$distributioncenter_id = 0;
		if($this->input->get("distributioncenter_id")) {
            $distributioncenter_id = $this->input->get("distributioncenter_id");
		}
        $this->assigns["distributioncenter_id"] = $distributioncenter_id;
		
		$this->assigns["AllDC"] = $this->distributioncentersmodel->getAllDC();
		
		$this->assigns["dataList"] = $this->distributioncentersmodel->getDatagridInfo($startDate, $endDate, $distributioncenter_id);
        $this->assigns["CostChartInfo"] = $this->distributioncentersmodel->getCostChartInfo($startDate, $endDate, $distributioncenter_id);
        $this->assigns["WasteTrend"] = $this->distributioncentersmodel->getWasteTrends($distributioncenter_id);

		if ($this->input->get("export")) {
			
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			fputcsv($file, array("DC Name", "Sq Ft", "Scheduled (Tons)", "Scheduled (Cost)", "On Call (Tons)", "On Call (Cost)", "TotalTons", "TotalCost"));

            foreach($this->assigns["dataList"] as $row) {
            	$data = array(
                    $row->name,
                    $row->squareFootage,
                    $row->ScheduledTons,
                    $row->ScheduledCost,
                    $row->OnCallTons,
                    $row->OnCallCost,
                    $row->TotalTons,
                    $row->TotalCost);

            	fputcsv($file, $data);
            }
			
			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('DistributionCentersWaste-' . $startDate . '_to_' . $endDate . '.csv', $csv);			
		} else {
			$this->load->view("dc/waste", $this->assigns);
		}
	}
	
	public function Recycling() {

		$this->load->helper('dates');
		
		$startDate = null;
		$endDate = null;
		$distributioncenter_id = null;

		if($this->input->get("from")) {
			$startDate = USToSQLDate($this->input->get("from"));
			$this->assigns["from"] = $this->input->get("from");
		} else {
            $sUSDate = date('m/d/Y', mktime(0, 0, 0, date("m")-1, 1, date("Y")));
			$startDate = USToSQLDate( $sUSDate);
			$this->assigns["from"] =  $sUSDate;
		}
		if($this->input->get("to")) {
			$endDate = USToSQLDate($this->input->get("to"));
			$this->assigns["to"] = $this->input->get("to");
		} else {
            $eUSDate = date('m/d/Y', mktime(0, 0, 0, date("m"), 0, date("Y")));
			$endDate = USToSQLDate($eUSDate);
			$this->assigns["to"] = $eUSDate;
		}
		
		if($this->input->get("distributioncenter_id")) {
            $distributioncenter_id = intval($this->input->get("distributioncenter_id"));
		}
        $this->assigns["distributioncenter_id"] = $distributioncenter_id;

		$this->assigns["AllDC"] = $this->distributioncentersmodel->getAllDC();

        $this->assigns["ChartsInfo"] = $this->distributioncentersmodel->getDCDashboardRecyclingTotalTonnageTotalPOPriceChartsInfo($startDate, $endDate, $distributioncenter_id);
        $this->assigns["DatagridInfo"] = $this->distributioncentersmodel->getDCDashboardRecyclingDatagridInfo($startDate, $endDate, $distributioncenter_id);

        $colums = array('DC Name');
        $data = array();
        foreach($this->assigns["DatagridInfo"] as $row) {
            if(!in_array($row->name, $colums)) {
                array_push($colums, $row->name, $row->cost);
            }
            if(!isset($data[$row->locationId])) {
                $data[$row->locationId] = array_fill(0, count($colums), 0.0);
            }
        }

        $pr = array();
        foreach($colums as $col) {
            $pr[$col] = 0.0;
        }

        foreach($data as $k=>$v) {
            $data[$k] = $pr;
        }

        foreach($this->assigns["DatagridInfo"] as $row) {
            $data[$row->locationId]['DC Name'] = $row->dcName;
            $data[$row->locationId][$row->name] = number_format(round($row->quantity, 0), 0, '.', ',');
            $data[$row->locationId][$row->cost] = '$'.number_format(round($row->pricePOUnit, 0), 0, '.', ',');
        }

        $this->assigns["DatagridInfo"] = $data;
        $this->assigns["DatagridInfoColums"] = $colums;

		if ($this->input->get("export")) {
			
			$this->load->helper('download');
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			fputcsv($file, $colums);

            foreach($this->assigns["DatagridInfo"] as $temp) {
            	fputcsv($file, $temp);
            }
			
			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('DistributonCentersRecycling-' . $startDate . '_to_' . $endDate . '.csv', $csv);			
			
		} else {
			$this->load->view("dc/recycling", $this->assigns);
		}
	}
	
	public function Cost() {
		
		$this->load->helper('dates');
		
		$startDate = null;
		$endDate = null;
		$DC = null;
		$material = null;
		
		if($this->input->get("from")) {
			$startDate = USToSQLDate($this->input->get("from"));
			$this->assigns["from"] = $this->input->get("from");
		} else {
			$startDate = USToSQLDate(date('m') . '/01/' . date('Y'));
			$this->assigns["from"] = date('m') . '/01/' . date('Y');
		}
		if($this->input->get("to")) {
			$endDate = USToSQLDate($this->input->get("to"));
			$this->assigns["to"] = $this->input->get("to");
		} else {
			$endDate = USToSQLDate(date('m/d/Y'));
			$this->assigns["to"] = date('m/d/Y');
		}
		
		if($this->input->get("allDC")) {
			$DC = $this->input->get("allDC");
			$this->assigns["DC"] = $this->input->get("allDC");
		} else {
			$this->assigns["DC"] = "";
		}		
		
		
		$allDC = $this->distributioncentersmodel->getAllDC();

		$this->assigns["AllDC"] = $allDC;
		
		$data = $this->distributioncentersmodel->getCost($startDate, $endDate, $DC);
		
		$this->assigns["data"] = $data[0];
		$this->assigns["allSum"] = $data[1];
		$this->assigns["costGraph"] = $data[2];
		
		$costTrend = $this->distributioncentersmodel->CostTrends($allDC);
		$this->assigns["CostTrend"] = $costTrend[0];
		$this->assigns["CostMonth"] = $costTrend[1];
		$this->assigns["OrderedTrend"] = $costTrend[2];
		$this->assigns["DCNames"] = $costTrend[3];
		
		
		
		if($this->input->get("print")) {
			$this->load->view("dc/costPrint", $this->assigns);
				
		} else if ($this->input->get("export")) {
				
				
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
				
				
			$colums = array("Location", "SqFt", "Period", "Total Tonnage", "Waste Service", "Waste Equipment Fee", "Waste Haul Fee", "Waste Disposal Fee", "Recycling Rebate", "Other Fee", "Net");
			fputcsv($file, $colums);
		
			$data = array();
				
			foreach($this->assigns["data"] as $temp) {

				$rr = isset($temp["rr"])?$temp["rr"]:0;
				
				
				if(isset($temp["rr"])) {
					$net = $temp["rr"]-$temp["ws"]-$temp["rrf"]; 
					if($net < 0){
						$net = 0;
					}
				} else {
					$net = 0;
				}				

				$data = array(
						$temp["locationName"],
						$temp["sqft"],
						$temp["t"],
						$temp["ws"],
						$temp["we"],
						$temp["wh"],
						$temp["wd"],
						$rr,
						$temp["o"],
						$net);
				fputcsv($file, $data);
			}
				
			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('DistributionCentersWaste-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		} else {		
			$this->load->view("dc/cost", $this->assigns);
		}
	}
	
	public function Services() {
		
		$this->load->helper('dates');
		
		$startDate = null;
		$endDate = null;
		$DC = null;
		$material = null;
		
		if($this->input->get("from")) {
			$startDate = USToSQLDate($this->input->get("from"));
			$this->assigns["from"] = $this->input->get("from");
		} else {
			$startDate = USToSQLDate(date('m') . '/01/' . date('Y'));
			$this->assigns["from"] = date('m') . '/01/' . date('Y');
		}
		if($this->input->get("to")) {
			$endDate = USToSQLDate($this->input->get("to"));
			$this->assigns["to"] = $this->input->get("to");
		} else {
			$endDate = USToSQLDate(date('m/d/Y'));
			$this->assigns["to"] = date('m/d/Y');
		}

        $distributioncenter_id = 0;
        if($this->input->get("distributioncenter_id")) {
            $distributioncenter_id = $this->input->get("distributioncenter_id");
        }
        $this->assigns["distributioncenter_id"] = $distributioncenter_id;

        $this->assigns["AllDC"] = $this->distributioncentersmodel->getAllDC();
		
        $this->assigns["FrequencyOfService"] = $this->distributioncentersmodel->getFrequencyOfService($startDate, $endDate, $distributioncenter_id);
        $this->assigns["TypeOfContainers"] = $this->distributioncentersmodel->getTypeOfContainers($startDate, $endDate, $distributioncenter_id);
        $this->assigns["DatagridInfo"] = $this->distributioncentersmodel->getServicesDatagridInfo($startDate, $endDate, $distributioncenter_id);
		
        if ($this->input->get("export")) {

			$this->load->helper('download');

			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			fputcsv($file, array("Location", "SqFt", "Vendor", "Service Type", "Container", "Frequency", "Cost"));
		
			foreach($this->assigns["DatagridInfo"] as $row) {
				$data = array(
                    $row->dcName,
                    $row->squareFootage,
                    $row->name,
                    $row->ServiceType,
                    $row->container,
                    $row->Frequency,
                    $row->cost
				);
				fputcsv($file, $data);
			}
				
			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('DistributonCentersServices-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		} else {
			$this->load->view("dc/services", $this->assigns);
		}		

	}
	
	public function Lists() {
		$this->assigns["data"] = $this->distributioncentersmodel->getAllList();

        if($this->input->get("export")){
            $this->load->helper('download');

            $file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

            fputcsv($file, array("Location", "Address", "City", "State", "Zip", "SqFt", "Last Updated"));

            foreach($this->assigns["data"] as $temp) {
                $data = array(
                    $temp["name"],
                    $temp["addressLine1"],
                    $temp["city"],
                    $temp["sname"],
                    $temp["zip"],
                    $temp["squareFootage"],
                    $temp["lu"]
                );
                fputcsv($file, $data);
            }

            rewind($file);
            $csv = stream_get_contents($file);
            fclose($file);
            force_download('DistributonCentersList.csv', $csv);
        } else {
            $this->load->view("dc/list", $this->assigns);
        }
	}
}

/* End of file front.php */
/* Location: ./application/controllers/front.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/Front.php';

class StoreInfo extends Front 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("storemodel");
		$this->assigns['_main_controller'] = 'Stores';
	}



	/**
	 * @param integer id `Stores`.`id`
	 */
	public function Waste($id=0)
	{
		$id = (int)$id;

		if($id === 0) {
			log_message('error', 'Store id is empty');
			return;
		}

		$this->assigns['DCData'] = $this->storemodel->getStore($id);
		if(empty($this->assigns['DCData'])) {
			$msg = 'Row with id ' . $id . ' not found';
			log_message('error', $msg);
			echo $msg;
			return;
		}

		$this->assigns['Contacts'] = $this->storemodel->getContacts($id);
		$this->assigns['id'] = $id;
		
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
		
		$this->assigns['tblData'] = $this->storemodel->getWasteTable($startDate, $endDate, $id);
		$this->assigns['recycling'] = $this->storemodel->getRecycleInvoices($startDate, $endDate, $id);
		$this->assigns['DiversionRate']	= $this->storemodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['tblData']['sum']['waste']
		);		

		$result = $this->StoreModel->getWasteData($startDate, $endDate, 1, 0, $id);			
		$this->assigns['chart2'] = $result['waste'];
		$this->assigns['chart3'] = $result['trend'];
		$this->assigns['table'] = $result['table'];

		$byStateArray = $this->States->getListForSelect();								
		$this->assigns['data']->byState = array(0=>"All States") + $byStateArray;

		$this->load->view("storeinfo/waste", $this->assigns);
	}


	/**
	 * @param integer id `distributioncenters`.`id`
	 */
	public function Recycling($id=0)
	{
		$id = (int)$id;

		if($id === 0)
		{
			log_message('error', 'Store id is empty');
			return;
		}

		$this->assigns['DCData']		= $this->storemodel->getStore($id);
		if(empty($this->assigns['DCData']))
		{
			$msg = 'Row with id ' . $id . ' not found';
			log_message('error', $msg);
			echo $msg;
			return;
		}

		$this->assigns['Contacts']		= $this->storemodel->getContacts($id);
		$this->assigns["id"]			= $id;


		$this->load->helper('dates');

		if($this->input->get("from")) 
		{
			$startDate = USToSQLDate($this->input->get("from"));
			$this->assigns["from"] = $this->input->get("from");
		}
		else
		{
			$startDate = USToSQLDate('01/01/' . date('Y'));
			$this->assigns["from"] = '01/01/' . date('Y');
		}

		if($this->input->get("to"))
		{
			$endDate = USToSQLDate($this->input->get("to"));
			$this->assigns["to"] = $this->input->get("to");
		}
		else
		{
			$endDate = USToSQLDate(date('m/d/Y'));
			$this->assigns["to"] = date('m/d/Y');
		}

		$this->assigns['tblData']		= $this->storemodel->getWasteTable($startDate, $endDate, $id);
		$this->assigns['recycling']		= $this->storemodel->getRecycleInvoices($startDate, $endDate, $id);
		$this->assigns['DiversionRate']	= $this->storemodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['tblData']['sum']['waste']
		);

		//echo '<pre>'; dump($this->assigns['tblData']);

		if($this->input->get("print")) 
		{
			$this->load->view("storeinfo/recyclingPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				"Store", 
				"SqFt", 
				"Cardboard", 
				"Aluminum", 
				"Film", 
				"Plastic", 
				"Trees", 
				"Landfill", 
				"Energy", 
				"Co2", 
				"Rebate",
			);

			fputcsv($file, $colums);

			$data = array();

			foreach($this->assigns['recycling']['rows'] as $temp) 
			{
				$data = array(
					$temp["DC_name"],
					$temp["sqft"],
					$temp["cardboard"],
					$temp["aluminum"],
					$temp["film"],
					$temp["plastic"],
					$temp["trees"],
					$temp["landfill"],
					$temp["kwh"],
					$temp["co2"],
					$temp["rebate"],
				);

				fputcsv($file, $data);
			}

			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('Store_Recycling-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
		else
		{
			$this->load->view("storeinfo/recycling", $this->assigns);
		}
	}



	/**
	 * @param integer id `Stores`.`id`
	 *
	 *
	 *	Diagram 2
	 *	NET			= $v['recycling_rebate'] - $v['waste_service'] - ($v['rif'] * $v['quantity']);
	 *	Savings		= recycling_rebate
	 *	COST		= Waste Equipment Fee + Waste Haul Fee + Waste Disposal Fee + Other fee = waste_service
	 *
	 *	Diagram 3
	 *	recycling rebates	= $v['recycling_rebate']
	 *	SAMS				= waste_service WHERE WasteInvoiceServices.schedule >= 1 for choosen year 
	 *						  -(minus) 
	 *						  waste_service WHERE WasteInvoiceServices.schedule >= 1 for (choosen year - 1)
	 *	waived fees			= WasteInvoiceFees && RecyclingInvoicesFees
	 *
	 */
	public function CostSavings($id=0)
	{
		$id = (int)$id;

		if($id === 0)
		{
			log_message('error', 'Store id is empty');
			return;
		}

		$this->assigns['DCData']		= $this->storemodel->getStore($id);
		if(empty($this->assigns['DCData']))
		{
			$msg = 'Row with id ' . $id . ' not found';
			log_message('error', $msg);
			echo $msg;
			return;
		}

		$this->assigns['Contacts']		= $this->storemodel->getContacts($id);
		$this->assigns["id"]			= $id;


		$this->load->helper('dates');

		if($this->input->get("from")) 
		{
			$startDate = USToSQLDate($this->input->get("from"));
			$this->assigns["from"] = $this->input->get("from");
		}
		else
		{
			$startDate = USToSQLDate('01/01/' . date('Y'));
			$this->assigns["from"] = '01/01/' . date('Y');
		}

		if($this->input->get("to"))
		{
			$endDate = USToSQLDate($this->input->get("to"));
			$this->assigns["to"] = $this->input->get("to");
		}
		else
		{
			$endDate = USToSQLDate(date('m/d/Y'));
			$this->assigns["to"] = date('m/d/Y');
		}

		$this->assigns['waste']			= $this->storemodel->getWasteTable($startDate, $endDate, $id);
		$this->assigns['recycling']		= $this->storemodel->getRecycleInvoices($startDate, $endDate, $id);
		$this->assigns['DiversionRate']	= $this->storemodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['waste']['sum']['waste']
		);

		$this->assigns["tblData"] = $this->storemodel->getSingleDCCost($startDate, $endDate, $id);

		$one_year_ago = $this->storemodel->getSingleDCCost(
			date('Y-m-d', strtotime('-1 year', strtotime($startDate))), 
			date('Y-m-d', strtotime('-1 year', strtotime($endDate))), 
			$id
		);
									   // current year
		$this->assigns["SAMS"] = (int) $this->assigns["tblData"]['sum']['waste_service_shedule'] - $one_year_ago['sum']['waste_service_shedule'];


		if($this->input->get("print")) 
		{
			$this->load->view("storeinfo/costSavingsPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				"Store", 
				"SqFt", 
				"Period", 
				"Total Tonnage", 
				"Waste Service", 
				"Waste Equipment Fee", 
				"Waste Haul Fee", 
				"Waste Disposal Fee", 
				"Recycling Rebate", 
				"Other Fee", 
				"Net",
			);

			fputcsv($file, $colums);

			$data = array();

			foreach($this->assigns['tblData']['rows'] as $temp) 
			{
				$data = array(
					$temp["dc_name"],
					$temp["sqft"],
					$temp["period"],
					$temp["total_tonage"],
					$temp["waste_service"],
					$temp["waste_equipment_fee"],
					$temp["waste_haul_fee"],
					$temp["waste_disposal_fee"],
					$temp["recycling_rebate"],
					$temp["other_fee"],
					$temp["net"],
				);

				fputcsv($file, $data);
			}

			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('Store_Cost_Savings-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
		else
		{
			$this->load->view("storeinfo/costSavings", $this->assigns);
		}
	}


	/**
	 * @param integer id `Stores`.`id`
	 *
	 * LATEST UPDATE FROM THE CUSTOMER (14.06.2012):
	 *
	 * (http://dev.astordashboard.com/static/dcexample.html#tabs-4) should aggregate all hauler invoices, 
	 * all recycling purchase orders (only  and all recycling charge invoices tied to the specific DC being viewed. 
	 * Rather than using the old fields , please use: Sent Date, Location, Invoice, Invoice Date, Invoice Number, 
	 * Vendor, Material, Total Charge, Total Rebate.... just like the table on this 
	 * http://dev.astordashboard.com/admin/RecyclingInvoice/history. Note that when an invoice link is clicked on, 
	 * users only see info and cannot edit. For recycling purchase order, users can only see "General Info" and 
	 * "Purchase Order to Company" info. 
	 * An admin should see and be able to edit everything just as they would through the Admin links. 
	 */
	public function Invoices($id=0)
	{
		$id = (int)$id;

		if($id === 0)
		{
			log_message('error', 'Store id is empty');
			return;
		}

		$this->assigns['DCData']		= $this->storemodel->getStore($id);
		if(empty($this->assigns['DCData']))
		{
			$msg = 'Row with id ' . $id . ' not found';
			log_message('error', $msg);
			echo $msg;
			return;
		}

		$this->assigns['Contacts'] = $this->storemodel->getContacts($id);
		$this->assigns["id"] = $id;

		$this->assigns['waste'] = $this->storemodel->getWasteTable('200-01-01', date('Y-m-d'), $id);
		$this->assigns['recycling'] = $this->storemodel->getRecycleInvoices('200-01-01', date('Y-m-d'), $id);
		$this->assigns['DiversionRate']	= $this->storemodel->getDiversionRate($id);

		$this->assigns['inv'] = $this->storemodel->getInvoices($id);

		if($this->input->get("print")) 
		{
			$this->load->view("storeinfo/invoicesPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				"PO", 
				"InvoiceDate", 
				"SentDate", 
				"Location", 
				"Vendor", 
				"Material", 
				"Qty", 
				"TotalRebate", 
				"Total"
			);

			if($this->assigns['_isAdmin'] != 1)
			{
				unset($colums[2]);
			}

			fputcsv($file, $colums);

			$data = array();

			foreach($this->assigns['inv']['rows'] as $temp) 
			{
				$data = array(
					$temp["invoice_num"],
					$temp["invoice_date"],
					$temp["sent_date"],
					$temp["location"],
					$temp["vendor"],
					$temp["material"],
					$temp["quantity"],
					$temp["total_rebate"],
					$temp["total"],
				);

				if($this->assigns['_isAdmin'] != 1)
				{
					unset($temp[2]);
				}

				fputcsv($file, $data);
			}

			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('Store_Invoices.csv', $csv);
		}
		else
		{
			$this->load->view("storeinfo/invoices", $this->assigns);
		}
	}


	/**
	 * @param integer id `distributioncenters`.`id`
	 */
	public function ajax_Invoices($id=0)
	{
		$id = (int)$id;

		header('Content-type: application/json');

		if ($this->input->get('iSortingCols') > 0) 
		{
			if ($this->input->get('bSortable_0')) 
			{
				$sortColumn_arr = array();
				$sortColumn_arr[] = 'id';
				$sortColumn_arr[] = 'invoice_num';
				$sortColumn_arr[] = 'invoice_date';

				// This column is visible only for admin
				if($this->assigns['_isAdmin'] == 1)
				{
					$sortColumn_arr[] = 'sent_date';
				}

				$sortColumn_arr[] = 'location';
				$sortColumn_arr[] = 'vendor';
				$sortColumn_arr[] = 'material';
				$sortColumn_arr[] = 'quantity';
				$sortColumn_arr[] = 'total_rebate';
				$sortColumn_arr[] = 'total';

				$sortColumn = $sortColumn_arr[$this->input->get('iSortCol_0')];

				if ($this->input->get('sSortDir_0') == 'asc')
				{
					$sortDir = 'ASC';
				}
				else
				{
					$sortDir = 'DESC';
				}
			}
		}

		$data = $this->storemodel->getInvoices(
			$id,
			$this->input->get('iDisplayStart'), 
			$this->input->get('iDisplayLength'), 
			$sortColumn, 
			$sortDir
		);

		$ajaxData = array();
		while (list($k,$v) = each($data['rows']))
		{
			$tmp = array();

			//////////////////////////////////////////////////////
			// Table row class decoration
			//$tmp['DT_RowClass'] = 'gradeA ' . ($k+1)%2 ? 'even' : 'odd';
			$tmp['DT_RowClass'] = 'gradeA';

			//////////////////////////////////////////////////////
			// Location
			// for admin - link to edit form
			// for others - link to "details" read-only
			if($this->assigns['_isAdmin'] == 1)
			{
				if($v['row_type'] == 'recycle')
				{
					$lnk = base_url() . 'admin/RecyclingInvoice/AddEdit/' . $v['id'];
				}
				elseif($v['row_type'] == 'waste')
				{
					$lnk = base_url() . 'admin/WasteInvoice/AddEdit/' . $v['id'];
				}

				$tmp[] = '<a title="Edit invoice #' . $v['id'] . '" href="' . $lnk . '">Edit</a>';
			}
			else
			{
				if($v['row_type'] == 'recycle')
				{
					$lnk = base_url() . 'admin/RecyclingInvoiceReadOnly/AddEdit/' . $v['id'];
				}
				elseif($v['row_type'] == 'waste')
				{
					$lnk = base_url() . 'admin/WasteInvoiceReadOnly/AddEdit/' . $v['id'];
				}

				$tmp[] = '<a title="More about invoice #' . $v['id'] . '" href="' . $lnk . '">View</a>';
			}

			//////////////////////////////////////////////////////
			// Other data
			$tmp[] = $v['invoice_num'];
			$tmp[] = $v['invoice_date'];

			// This column is visible only for admin
			if($this->assigns['_isAdmin'] == 1)
			{
				$tmp[] = $v['sent_date'];
			}

			$tmp[] = $v['location'];
			$tmp[] = $v['vendor'];
			$tmp[] = $v['material'];
			$tmp[] = $v['quantity'];
			$tmp[] = $v['total_rebate'];
			$tmp[] = $v['total'];

			$ajaxData[] = $tmp;
			unset($tmp);
		}

		echo json_encode(
			array(
				'aaData'				=> $ajaxData,
				'iTotalRecords'			=> $data['rows_count'],
				'iTotalDisplayRecords'	=> $data['rows_count']
			)
		);

		return;
	}



	/**
	 * @param integer id `distributioncenters`.`id`
	 */
	public function SupportRequests($id=0)
	{
		$id = (int)$id;

		if($id === 0)
		{
			log_message('error', 'Store id is empty');
			return;
		}

		$this->assigns['DCData']		= $this->storemodel->getStore($id);
		if(empty($this->assigns['DCData']))
		{
			$msg = 'Row with id ' . $id . ' not found';
			log_message('error', $msg);
			echo $msg;
			return;
		}

		$this->assigns['Contacts']		= $this->storemodel->getContacts($id);
		$this->assigns["id"]			= $id;

		$this->assigns['waste']			= $this->storemodel->getWasteTable('200-01-01', date('Y-m-d'), $id);
		$this->assigns['recycling']		= $this->storemodel->getRecycleInvoices('200-01-01', date('Y-m-d'), $id);
		$this->assigns['DiversionRate']	= $this->storemodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['waste']['sum']['waste']
		);

		$this->assigns['support_requests'] = $this->storemodel->getSupportRequests($id);

		//echo '<pre>'; dump($this->assigns['support_requests']);

		if($this->input->get("print")) 
		{
			$this->load->view("storeinfo/supportRequestsPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				"Location", 
				"Service", 
				"Date", 
				"Contact", 
				"Phone", 
				"Description", 
				"Resolved",
			);

			if($this->assigns['_isAdmin'] != 1)
			{
				unset($colums[2]);
			}

			fputcsv($file, $colums);

			$data = array();

			foreach($this->assigns['support_requests']['rows'] as $temp) 
			{
				$data = array(
					$temp["location"],
					$temp["service_id"],
					$temp["r_date"],
					$temp["contact"],
					$temp["phone"],
					$temp["description"],
					$temp["complete_word"],
				);

				fputcsv($file, $data);
			}

			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('Store_Support_Requests.csv', $csv);
		}
		else
		{
			$this->load->view("storeinfo/supportRequests", $this->assigns);
		}
	}


	/**
	 * @param integer id `distributioncenters`.`id`
	 */
	public function ajax_SupportRequests($id=0)
	{
		$id = (int)$id;

		header('Content-type: application/json');

		if ($this->input->get('iSortingCols') > 0) 
		{
			if ($this->input->get('bSortable_0')) 
			{
				$sortColumn_arr = array();
				$sortColumn_arr[] = 'location';
				$sortColumn_arr[] = 'service_id';
				$sortColumn_arr[] = 'r_date';
				$sortColumn_arr[] = 'contact';
				$sortColumn_arr[] = 'phone';
				$sortColumn_arr[] = 'description';
				$sortColumn_arr[] = 'complete';

				$sortColumn = $sortColumn_arr[$this->input->get('iSortCol_0')];

				if ($this->input->get('sSortDir_0') == 'asc')
				{
					$sortDir = 'ASC';
				}
				else
				{
					$sortDir = 'DESC';
				}
			}
		}

		$data = $this->storemodel->getSupportRequests(
			$id,
			$this->input->get('iDisplayStart'), 
			$this->input->get('iDisplayLength'), 
			$sortColumn, 
			$sortDir
		);


		$ajaxData = array();
		while (list($k,$v) = each($data['rows']))
		{
			$tmp = array();

			//////////////////////////////////////////////////////
			// Table row class decoration
			$tmp['DT_RowClass'] = ($v['complete'] == 1) ? 'gradeA' : 'gradeX';

			//////////////////////////////////////////////////////
			// Lnk
			if($this->assigns['_isAdmin'] == 1)
			{
				$tmp[] = '<a href="' . base_url() . 'admin/SupportRequest/edit/' . $v['service_id'] . '">' . $v['location'] . '</a>';
			}
			else
			{
				$tmp[] = '<a href="' . base_url() . 'admin/SupportRequestReadOnly/edit/' . $v['service_id'] . '">' . $v['location'] . '</a>';
			}

			//////////////////////////////////////////////////////
			// Other data
			$tmp[] = $v['service_id'];
			$tmp[] = $v['r_date'];
			$tmp[] = $v['contact'];
			$tmp[] = $v['phone'];
			$tmp[] = $v['description'];
			$tmp[] = $v['complete_word'];

			$ajaxData[] = $tmp;
			unset($tmp);
		}

		echo json_encode(
			array(
				'aaData'				=> $ajaxData,
				'iTotalRecords'			=> $data['rows_count'],
				'iTotalDisplayRecords'	=> $data['rows_count']
			)
		);

		return;
	}


	/**
	 * @param integer id `distributioncenters`.`id`
	 */
	public function SiteInfo($id=0) {
		$id = (int)$id;

		if($id === 0) {
			log_message('error', 'Store id is empty');
			return;
		}

		$this->assigns['DCData'] = $this->storemodel->getStore($id);
		if(empty($this->assigns['DCData'])) {
			$msg = 'Row with id ' . $id . ' not found';
			log_message('error', $msg);
			echo $msg;
			return;
		}

		$this->assigns['Contacts'] = $this->storemodel->getContacts($id);
		$this->assigns['id'] = $id;

		$this->assigns['waste'] = $this->storemodel->getWasteTable('200-01-01', date('Y-m-d'), $id);
		$this->assigns['recycling'] = $this->storemodel->getRecycleInvoices('200-01-01', date('Y-m-d'), $id);
		$this->assigns['DiversionRate']	= $this->storemodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['waste']['sum']['waste']
		);
		
		$this->load->model('admin/StoresModel');
		$this->load->model('States');
		$this->load->model('admin/VendorServices');
		$this->load->model('admin/Containers');
		$this->load->model('admin/ScheduleModel');
		
		$this->assigns['data'] = $this->StoresModel->getById($this->assigns['_companyId'], $id);
		$this->assigns['data']->statesOptions = $this->States->getListForSelect();
		
		$this->assigns['data']->services = $this->VendorServices->getByLocation($id, 'STORE', false);
		foreach($this->assigns['data']->services as &$item) {
			$item->daysname = $this->day2name($item->days);
		}		
		
		$this->assigns['data']->serviceHistory = $this->VendorServices->getByLocation($id, 'STORE', true);
		foreach($this->assigns['data']->serviceHistory as &$item) {
			$item->daysname = $this->day2name($item->days);
		}		
		
		$this->assigns['data']->containerOptions = $this->Containers->getListForSelect($this->assigns['_companyId']);
		$this->assigns['data']->scheduleOptions = $this->ScheduleModel->getListForSelect($this->assigns['_companyId']);
		$this->assigns['data']->scheduleOptions[0] = '';
		
		$this->load->view("storeinfo/siteInfo", $this->assigns);
	}



	/**
	 * @param integer id `distributioncenters`.`id`
	 */
	public function ajax_SiteInfo($id=0)
	{
		$id = (int)$id;

		header('Content-type: application/json');

		if ($this->input->get('iSortingCols') > 0) 
		{
			if ($this->input->get('bSortable_0')) 
			{
				$sortColumn_arr = array();
				$sortColumn_arr[] = 'location';
				$sortColumn_arr[] = 'DC_squareFootage';
				$sortColumn_arr[] = 'vendor_name';
				$sortColumn_arr[] = 'service_type';
				$sortColumn_arr[] = 'container_type';
				$sortColumn_arr[] = 'container_size';
				$sortColumn_arr[] = 'duration';
				$sortColumn_arr[] = 'frequency';
				$sortColumn_arr[] = 'cost';
				$sortColumn_arr[] = 'last_updated';

				$sortColumn = $sortColumn_arr[$this->input->get('iSortCol_0')];

				if ($this->input->get('sSortDir_0') == 'asc')
				{
					$sortDir = 'ASC';
				}
				else
				{
					$sortDir = 'DESC';
				}
			}
		}

		$data = $this->storemodel->getVendorsByInvoices(
			$id,
			$this->input->get('iDisplayStart'), 
			$this->input->get('iDisplayLength'), 
			$sortColumn, 
			$sortDir
		);


		$ajaxData = array();
		while (list($k,$v) = each($data['rows']))
		{
			$tmp = array();

			$tmp['DT_RowClass']	= 'gradeA';
			$tmp[]				= $v['location'];
			$tmp[]				= $v['DC_squareFootage'];
			$tmp[]				= $v['vendor_name'];
			$tmp[]				= $v['service_type'];
			$tmp[]				= $v['container_type'];
			$tmp[]				= $v['container_size'];
			$tmp[]				= $v['duration'];
			$tmp[]				= $v['frequency'];
			$tmp[]				= $v['cost'];
			$tmp[]				= $v['last_updated'];

			$ajaxData[] = $tmp;
			unset($tmp);
		}

		echo json_encode(
			array(
				'aaData'				=> $ajaxData,
				'iTotalRecords'			=> $data['rows_count'],
				'iTotalDisplayRecords'	=> $data['rows_count']
			)
		);

		return;
	}





}

/* End of file StoreInfo.php */
/* Location: ./application/controllers/StoreInfo.php */
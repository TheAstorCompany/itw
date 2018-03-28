<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/Front.php';

class Regioninfo extends Front 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("regionsmodel");
		$this->assigns['_main_controller'] = 'Stores';
	}



	/**
	 * @param string region region name (Southeast, West, East...)
	 */
	public function Waste($regionName='')
	{
		$regionName = trim($regionName);

		if($regionName == '')
		{
			log_message('error', 'Region name is empty');
			return;
		}


		$this->assigns['name']			= $regionName;
		$this->assigns['storesCount']	= $this->regionsmodel->getStoresCount($regionName);
		$this->assigns['storesSqft']	= $this->regionsmodel->getStoresSqft($regionName);
		$this->assigns['DiversionRate']	= 'TODO';

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

		$this->assigns['tblData']		= $this->regionsmodel->getWasteTable($startDate, $endDate, $regionName);
		$this->assigns['recycling']		= $this->regionsmodel->getRecycleInvoices($startDate, $endDate, $regionName);
		$this->assigns['DiversionRate']	= $this->regionsmodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['tblData']['sum']['waste']
		);

		//echo '<pre>'; dump($this->assigns['DCData']);

		if($this->input->get("print")) 
		{
			$this->load->view("regioninfo/wastePrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				'Region', 
				'District', 
				'Location', 
				'24H', 
				'SqFt', 
				'Period', 
				'Waste (Tons)', 
				'Hazardous (Tons)', 
				'Other (Tons)', 
				'Cost', 
			);

			fputcsv($file, $colums);

			$data = array();

			foreach($this->assigns['tblData']['rows'] as $temp) 
			{
				$data = array(
					$temp["region"],
					$temp["district"],
					$temp["location"],
					$temp["24H"],
					$temp["sqft"],
					$temp["period"],
					$temp["waste"],
					$temp["hazardous"],
					$temp["other"],
					$temp["cost"],
				);

				fputcsv($file, $data);
			}

			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('Region_Waste-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
		else
		{
			$this->load->view("regioninfo/waste", $this->assigns);
		}
	}


	/**
	 * @param string region region name (Southeast, West, East...)
	 */
	public function Recycling($regionName='')
	{
		$regionName = trim($regionName);

		if($regionName == '')
		{
			log_message('error', 'Region name is empty');
			return;
		}
		$this->assigns['name']			= $regionName;
		$this->assigns['storesCount']	= $this->regionsmodel->getStoresCount($regionName);
		$this->assigns['storesSqft']	= $this->regionsmodel->getStoresSqft($regionName);

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

		$this->assigns['tblData']		= $this->regionsmodel->getWasteTable($startDate, $endDate, $regionName);
		$this->assigns['recycling']		= $this->regionsmodel->getRecycleInvoices($startDate, $endDate, $regionName);
		$this->assigns['DiversionRate']	= $this->regionsmodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['tblData']['sum']['waste']
		);

		//echo '<pre>'; dump($this->assigns['tblData']);

		if($this->input->get("print")) 
		{
			$this->load->view("regioninfo/recyclingPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				'Region', 
				'District', 
				'Location', 
				'24H', 
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
					$temp["region"],
					$temp["district"],
					$temp["location"],
					$temp["24H"],
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
			force_download('Region_Recycling-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
		else
		{
			$this->load->view("regioninfo/recycling", $this->assigns);
		}
	}



	/**
	 * @param string region region name (Southeast, West, East...)
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
	public function CostSavings($regionName='')
	{
		$regionName = trim($regionName);

		if($regionName == '')
		{
			log_message('error', 'Region name is empty');
			return;
		}
		$this->assigns['name']			= $regionName;
		$this->assigns['storesCount']	= $this->regionsmodel->getStoresCount($regionName);
		$this->assigns['storesSqft']	= $this->regionsmodel->getStoresSqft($regionName);

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

		$this->assigns['waste']			= $this->regionsmodel->getWasteTable($startDate, $endDate, $regionName);
		$this->assigns['recycling']		= $this->regionsmodel->getRecycleInvoices($startDate, $endDate, $regionName);
		$this->assigns['DiversionRate']	= $this->regionsmodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['waste']['sum']['waste']
		);

		$this->assigns["tblData"] = $this->regionsmodel->getSingleDCCost($startDate, $endDate, $regionName);

		$one_year_ago = $this->regionsmodel->getSingleDCCost(
			date('Y-m-d', strtotime('-1 year', strtotime($startDate))), 
			date('Y-m-d', strtotime('-1 year', strtotime($endDate))), 
			$regionName
		);
										  // current year
		$this->assigns["SAMS"] = (double) $this->assigns["tblData"]['sum']['waste_service_shedule'] - $one_year_ago['sum']['waste_service_shedule'];


		if($this->input->get("print")) 
		{
			$this->load->view("regioninfo/costSavingsPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				'Region', 
				'District', 
				'Location', 
				'24H', 
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
					$temp["region"],
					$temp["district"],
					$temp["location"],
					$temp["24H"],
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
			force_download('Region_Cost_Savings-' . $startDate . '_to_' . $endDate . '.csv', $csv);
		}
		else
		{
			$this->load->view("regioninfo/costSavings", $this->assigns);
		}
	}


	/**
	 * @param string regionName region name (Southeast, West, East...)
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
	public function Invoices($regionName='')
	{
		$regionName = trim($regionName);
		if($regionName == '')
		{
			log_message('error', 'Region name is empty');
			return;
		}
		$this->assigns['name']			= $regionName;
		$this->assigns['storesCount']	= $this->regionsmodel->getStoresCount($regionName);
		$this->assigns['storesSqft']	= $this->regionsmodel->getStoresSqft($regionName);

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

		$this->assigns['waste']			= $this->regionsmodel->getWasteTable($startDate, $endDate, $regionName);
		$this->assigns['recycling']		= $this->regionsmodel->getRecycleInvoices($startDate, $endDate, $regionName);
		$this->assigns['DiversionRate']	= $this->regionsmodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['waste']['sum']['waste']
		);


		$this->assigns['inv'] = $this->regionsmodel->getInvoices($regionName);

		if($this->input->get("print")) 
		{
			$this->load->view("regioninfo/invoicesPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				'Region', 
				'District', 
				'Location',
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
				unset($colums[5]);
			}

			fputcsv($file, $colums);

			$data = array();

			foreach($this->assigns['inv']['rows'] as $temp) 
			{
				$data = array(
					$temp["region"],
					$temp["district"],
					$temp["location"],
					$temp["invoice_num"],
					$temp["invoice_date"],
					$temp["sent_date"],
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
			force_download('Region_Invoices.csv', $csv);
		}
		else
		{
			$this->load->view("regioninfo/invoices", $this->assigns);
		}
	}


	/**
	 * @param string regionName region name (Southeast, West, East...)
	 */
	public function ajax_Invoices($regionName='')
	{
		$regionName = trim($regionName);

		header('Content-type: application/json');

		if ($this->input->get('iSortingCols') > 0) 
		{
			if ($this->input->get('bSortable_0')) 
			{
				$sortColumn_arr = array();

				$sortColumn_arr[] = 'region';
				$sortColumn_arr[] = 'district';
				$sortColumn_arr[] = 'location';
				$sortColumn_arr[] = 'id';
				$sortColumn_arr[] = 'invoice_num';
				$sortColumn_arr[] = 'invoice_date';

				// This column is visible only for admin
				if($this->assigns['_isAdmin'] == 1)
				{
					$sortColumn_arr[] = 'sent_date';
				}

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

		$data = $this->regionsmodel->getInvoices(
			$regionName,
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

				$tmp[] = '
				<a 
					title="Edit invoice #' . $v['id'] . '" 
					href="' . $lnk . '">Edit</a>';
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

				$tmp[] = '
				<a 
					title="More about invoice #' . $v['id'] . '" 
					href="' . $lnk . '">More</a>';
			}



			//////////////////////////////////////////////////////
			// Other data
			$tmp[] = $v['region'];
			$tmp[] = '<a href="' . base_url() . 'Districtinfo/Waste/' . $v['district_id'] . '">' . $v['district'] . '</a>';
			$tmp[] = '<a href="' . base_url() . 'Storeinfo/Waste/' . $v['location_id'] . '">' . $v['location'] . '</a>';
			$tmp[] = $v['invoice_num'];
			$tmp[] = $v['invoice_date'];

			// This column is visible only for admin
			if($this->assigns['_isAdmin'] == 1)
			{
				$tmp[] = $v['sent_date'];
			}

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
	 * @param string region region name (Southeast, West, East...)
	 */
	public function SupportRequests($regionName='')
	{
		$regionName = trim($regionName);

		if($regionName == '')
		{
			log_message('error', 'Region name is empty');
			return;
		}
		$this->assigns['name']			= $regionName;
		$this->assigns['storesCount']	= $this->regionsmodel->getStoresCount($regionName);
		$this->assigns['storesSqft']	= $this->regionsmodel->getStoresSqft($regionName);

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

		$this->assigns['waste']			= $this->regionsmodel->getWasteTable($startDate, $endDate, $regionName);
		$this->assigns['recycling']		= $this->regionsmodel->getRecycleInvoices($startDate, $endDate, $regionName);
		$this->assigns['DiversionRate']	= $this->regionsmodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['waste']['sum']['waste']
		);

		$this->assigns['support_requests'] = $this->regionsmodel->getSupportRequests($regionName);

		//echo '<pre>'; dump($this->assigns['support_requests']);

		if($this->input->get("print")) 
		{
			$this->load->view("regioninfo/supportRequestsPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				'Region', 
				'District', 
				'Location',
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
					$temp["region"],
					$temp["district"],
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
			force_download('Region_Support_Requests.csv', $csv);
		}
		else
		{
			$this->load->view("regioninfo/supportRequests", $this->assigns);
		}
	}


	/**
	 * @param string region region name (Southeast, West, East...)
	 */
	public function ajax_SupportRequests($regionName='')
	{
		$regionName = trim($regionName);

		header('Content-type: application/json');

		if ($this->input->get('iSortingCols') > 0) 
		{
			if ($this->input->get('bSortable_0')) 
			{
				$sortColumn_arr = array();
				$sortColumn_arr[] = 'region';
				$sortColumn_arr[] = 'district';
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

		$data = $this->regionsmodel->getSupportRequests(
			$regionName,
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
			// Other data
			$tmp[] = $v['region'];
			$tmp[] = $v['district'];

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
	 * @param string region region name (Southeast, West, East...)
	 */
	public function Services($regionName='')
	{
		//echo '<pre>'; dump($this->assigns);
		$regionName = trim($regionName);

		if($regionName == '')
		{
			log_message('error', 'Region name is empty');
			return;
		}
		$this->assigns['name']			= $regionName;
		$this->assigns['storesCount']	= $this->regionsmodel->getStoresCount($regionName);
		$this->assigns['storesSqft']	= $this->regionsmodel->getStoresSqft($regionName);

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

		$this->assigns['waste']			= $this->regionsmodel->getWasteTable($startDate, $endDate, $regionName);
		$this->assigns['recycling']		= $this->regionsmodel->getRecycleInvoices($startDate, $endDate, $regionName);
		$this->assigns['DiversionRate']	= $this->regionsmodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['waste']['sum']['waste']
		);

		$this->assigns['services'] = $this->regionsmodel->getServices($regionName);

		if($this->input->get("print")) 
		{
			$this->load->view("regioninfo/servicesPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				'Region', 
				'District', 
				'Location',
				"SqFt", 
				"24H", 
				"Container Type", 
				"Container Size", 
				"Duration", 
				"Frequency",
				"Cost",
				"Last Updated",
			);

			fputcsv($file, $colums);

			$data = array();

			foreach($this->assigns['services']['rows'] as $temp) 
			{
				$data = array(
					$temp["region"],
					$temp["district"],
					$temp["location"],
					$temp["squareFootage"],
					$temp["24H"],
					$temp["container_type"],
					$temp["container_size"],
					$temp["duration"],
					$temp["frequency"],
					$temp["total"],
					$temp["last_updated"],
				);

				fputcsv($file, $data);
			}

			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('Services_Requests.csv', $csv);
		}
		else
		{
			$this->load->view("regioninfo/services", $this->assigns);
		}
	}


	/**
	 * @param string region region name (Southeast, West, East...)
	 */
	public function ajax_Services($regionName='')
	{
		$regionName = trim($regionName);

		header('Content-type: application/json');

		if ($this->input->get('iSortingCols') > 0) 
		{
			if ($this->input->get('bSortable_0')) 
			{
				$sortColumn_arr = array();
				$sortColumn_arr[] = 'region';
				$sortColumn_arr[] = 'district';
				$sortColumn_arr[] = 'location';
				$sortColumn_arr[] = 'squareFootage';
				$sortColumn_arr[] = '24H';
				$sortColumn_arr[] = 'container_type';
				$sortColumn_arr[] = 'container_size';
				$sortColumn_arr[] = 'duration';
				$sortColumn_arr[] = 'frequency';
				$sortColumn_arr[] = 'total';
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

		$data = $this->regionsmodel->getServices(
			$regionName,
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
			//$tmp['DT_RowClass'] = ($v['complete'] == 1) ? 'gradeA' : 'gradeX';
			$tmp['DT_RowClass'] = 'gradeA';

			//////////////////////////////////////////////////////
			// Other data
			$tmp[] = $v['region'];
			$tmp[] = $v['district'];
			$tmp[] = $v['location'];
			$tmp[] = $v['squareFootage'];
			$tmp[] = $v['24H'];
			$tmp[] = $v['container_type'];
			$tmp[] = $v['container_size'];
			$tmp[] = $v['duration'];
			$tmp[] = $v['frequency'];
			$tmp[] = $v['total'];
			$tmp[] = $v['last_updated'];

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
	 * @param string region region name (Southeast, West, East...)
	 */
	public function Locations($regionName='')
	{
		$regionName = trim($regionName);

		if($regionName == '')
		{
			log_message('error', 'Region name is empty');
			return;
		}
		$this->assigns['name']			= $regionName;
		$this->assigns['storesCount']	= $this->regionsmodel->getStoresCount($regionName);
		$this->assigns['storesSqft']	= $this->regionsmodel->getStoresSqft($regionName);

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

		$this->assigns['waste']			= $this->regionsmodel->getWasteTable($startDate, $endDate, $regionName);
		$this->assigns['recycling']		= $this->regionsmodel->getRecycleInvoices($startDate, $endDate, $regionName);
		$this->assigns['DiversionRate']	= $this->regionsmodel->getDiversionRate(
			$this->assigns['recycling']['materialTons'],
			$this->assigns['waste']['sum']['waste']
		);

		$this->assigns['locations'] = $this->regionsmodel->getLocations($regionName);

		//echo '<pre>'; dump($this->assigns['support_requests']);

		if($this->input->get("print")) 
		{
			$this->load->view("regioninfo/locationsPrint", $this->assigns);
		}
		elseif ($this->input->get("export")) 
		{
			$this->load->helper('download');
			$csv = '';
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');

			$colums = array(
				'Region', 
				'District', 
				'Location',
				"Address", 
				"City", 
				"State", 
				"Zip", 
				"24H", 
				"SqFt",
				'Diversion',
				'Cost/Sqft',
				'Last Updated',
			);

			fputcsv($file, $colums);

			$data = array();

			foreach($this->assigns['locations']['rows'] as $temp) 
			{
				$data = array(
					$temp["region"],
					$temp["district"],
					$temp["location"],
					$temp["address"],
					$temp["city"],
					$temp["state"],
					$temp["zip"],
					$temp["24H"],
					$temp["squareFootage"],
					$temp["diversion"],
					$temp["CostSqft"],
					$temp["last_updated"],
				);

				fputcsv($file, $data);
			}

			rewind($file);
			$csv = stream_get_contents($file);
			fclose($file);
			force_download('Locations.csv', $csv);
		}
		else
		{
			$this->load->view("regioninfo/locations", $this->assigns);
		}
	}


	/**
	 * @param string region region name (Southeast, West, East...)
	 */
	public function ajax_Locations($regionName='')
	{
		$regionName = trim($regionName);

		header('Content-type: application/json');

		if ($this->input->get('iSortingCols') > 0) 
		{
			if ($this->input->get('bSortable_0')) 
			{
				$sortColumn_arr = array();
				$sortColumn_arr[] = 'region';
				$sortColumn_arr[] = 'district';
				$sortColumn_arr[] = 'location';
				$sortColumn_arr[] = 'address';
				$sortColumn_arr[] = 'city';
				$sortColumn_arr[] = 'state';
				$sortColumn_arr[] = 'zip';
				$sortColumn_arr[] = '24H';
				$sortColumn_arr[] = 'squareFootage';
				$sortColumn_arr[] = 'diversion';
				$sortColumn_arr[] = 'CostSqft';
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

		$data = $this->regionsmodel->getLocations(
			$regionName,
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
			//$tmp['DT_RowClass'] = ($v['complete'] == 1) ? 'gradeA' : 'gradeX';
			$tmp['DT_RowClass'] = 'gradeA';

			//////////////////////////////////////////////////////
			// Other data
			$tmp[] = $v['region'];
			$tmp[] = $v['district'];
			$tmp[] = $v['location'];
			$tmp[] = $v['address'];
			$tmp[] = $v['city'];
			$tmp[] = $v['state'];
			$tmp[] = $v['zip'];
			$tmp[] = $v['24H'];
			$tmp[] = $v['squareFootage'];
			$tmp[] = $v['diversion'] . '%';
			$tmp[] = '$' . $v['CostSqft'];
			$tmp[] = $v['last_updated'];

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

/* End of file Regioninfo.php */
/* Location: ./application/controllers/Regioninfo.php */
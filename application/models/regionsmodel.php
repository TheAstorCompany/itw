<?php

class RegionsModel extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('dates');
	}


	/**
	 * @param string region region name (Southeast, West, East...) States.region
	 * @return integer count of the Stores belonging to this district
	 */
	public function getStoresCount($region)
	{
		$s = "
		SELECT
			COUNT(`s`.`squareFootage`) AS stores_count
		FROM
			`Stores` AS s
		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)
		WHERE 
			`st`.`region` = '" . $region . "'";
		$q = $this->db->query($s);
		$result = $q->row();
		return (int)$result->stores_count;
	}


	/**
	 * @param string region region name (Southeast, West, East...) States.region
	 * @return double sum of the Square Footages of the Stores belonging to this region (state)
	 */
	public function getStoresSqft($region)
	{
		$s = "
		SELECT
			SUM(`s`.`squareFootage`) AS sqft
		FROM
			`Stores` AS s
		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)
		WHERE 
			`st`.`region` = '" . $region . "'";
		$q = $this->db->query($s);
		$result = $q->row();
		return (double)$result->sqft;
	}


	/**
	 * @param string startDate date in format YYYY-MM-DD
	 * @param string endDate date in format YYYY-MM-DD
	 * @param string region region name (Southeast, West, East...) States.region
	 * @return array mixed
	 */
	public function getWasteTable($startDate, $endDate, $region)
	{
		$s = "
		SELECT 
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			IF(
				`s`.`open24hours` = 1,
				'Y', 
				'N'
			) AS 24H,
			`s`.`squareFootage` AS DC_squareFootage,
			DATE_FORMAT(`wi`.`invoiceDate`, '%Y') AS invoice_y,
			DATE_FORMAT(`wi`.`invoiceDate`, '%m') AS invoice_m,
			`ws`.`quantity`, 
			`m`.`name`,
			`wi`.`total` AS cost

		FROM 
			`WasteInvoices` AS wi

		INNER JOIN
			`Stores` AS s ON (`wi`.`locationId` = `s`.`id`)

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		LEFT OUTER JOIN 
			`WasteInvoiceServices` AS ws ON (`wi`.`id` = `ws`.`invoiceId`)

		LEFT OUTER JOIN 
			`Materials` AS m ON (`m`.`id` = `materialId`)

		WHERE 
			`wi`.`locationType` = 'STORE' 
			AND `ws`.`unitID` = 1
			AND `wi`.`invoiceDate` >= '" . $startDate . "'
			AND `wi`.`invoiceDate` <= '" . $endDate . "'
			AND `wi`.`locationId` = `s`.`id`
			AND `st`.`region` = '" . $region . "'

		ORDER BY
			`wi`.`invoiceDate` ASC";
		$r = $this->db->query($s);
		$data = $r->result("array");

		$tmp = array();
		while (list($k,$v) = each($data))
		{
			$d = $v['invoice_y'] . $v['invoice_m'];

			if(isset($tmp[$d]))
			{
				$tmp[$d]['waste']		+= $v['quantity'];
				$tmp[$d]['hazardous']	+= ($v['name'] == 'Hazardous') ? $v['quantity'] : 0;
				$tmp[$d]['cost']		+= $v['cost'];
			}
			else
			{
				$tmp[$d] = array(
					'region'		=> $v['region'],
					'district'		=> $v['district'],
					'district_id'	=> $v['district_id'],
					'location'		=> $v['location'],
					'location_id'	=> $v['location_id'],
					'24H'			=> $v['24H'],
					'sqft'			=> $v['DC_squareFootage'],
					'period'		=> $v['invoice_m'] . '/' . $v['invoice_y'],
					'waste'			=> $v['quantity'],
					'hazardous'		=> ($v['name'] == 'Hazardous') ? $v['quantity'] : 0,
					'cost'			=> $v['cost'],
				);
			}
		}

		$sum = array(
			'districts_count'	=> 0,
			'locations_count'	=> 0,
			'sqft'				=> 0,
			'sqft'				=> 0,
			'waste'				=> 0,
			'hazardous'			=> 0,
			'cost'				=> 0,
			'other'				=> 0,
		);

		$rows = array();

		$chart_data = array(
			'dates'		=> array(),
			'waste'		=> array(),
			'hazardous'	=> array(),
			'other'		=> array(),
		);

		$tmp_districts = array();
		$tmp_locations = array();

		while (list($k,$v) = each($tmp))
		{
			$rows[$k] = $v;
			$rows[$k]['other'] = $v['waste'] - $v['hazardous'];

			$sum['sqft']		+= $v['sqft']; // why?
			$sum['waste']		+= $v['waste'];
			$sum['hazardous']	+= $v['hazardous'];
			$sum['other']		+= $rows[$k]['other'];
			$sum['cost']		+= $v['cost'];

			$chart_data['dates'][]		= $v['period'];
			$chart_data['waste'][]		= (int)$v['waste'];
			$chart_data['hazardous'][]	= (int)$v['hazardous'];
			$chart_data['other'][]		= (int)$rows[$k]['other'];

			$tmp_districts[] = $v['district_id'];
			$tmp_locations[] = $v['location_id'];
		}

		$sum['districts_count'] = count(array_unique($tmp_districts));
		$sum['locations_count'] = count(array_unique($tmp_locations));

		return array(
			'rows'			=> $rows, 
			'sum'			=> $sum,
			'chart_data'	=> $chart_data,
		);
	}


	/**
	 * @param string startDate date in format YYYY-MM-DD
	 * @param string endDate date in format YYYY-MM-DD
	 * @param string region region name (Southeast, West, East...) States.region
	 * @return array mixed
	 */
	public function getRecycleInvoices($startDate, $endDate, $region)
	{
		$query = $this->db->query("
			SELECT * FROM Materials 
		");
		
		$tempMaterials = $query->result();
		$materials = array();
		
		foreach ($tempMaterials as $v) 
		{
			$materials[$v->id]['co2'] = $v->CO2Saves;
			$materials[$v->id]['kwh'] = $v->EnergySaves;
		}

		// Trees - from Dropbox\nikolay\cleanData\HowToComputeSustainabilityGains.xlsx
		// 1 ton CardBoard	= 17 Trees (35 foot)
		// 1 ton Newsprint	= 15 Trees (35 foot)
		// 1 ton Paper		= 17 Trees (35 foot)
		// ???

		// landfill - from Dropbox\nikolay\cleanData\HowToComputeSustainabilityGains.xlsx
		// CardBoard	= 9
		// Newsprint	= 4.6
		// Paper		= 3.3
		$s = "
		SELECT 
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			IF(
				`s`.`open24hours` = 1,
				'Y', 
				'N'
			) AS 24H,
			`s`.`squareFootage` AS DC_squareFootage,
			`ri`.`id`,
			DATE_FORMAT(`ri`.`invoiceDate`, '%Y') AS invoice_y,
			DATE_FORMAT(`ri`.`invoiceDate`, '%m') AS invoice_m,
			SUM(IF(`rim`.`materialId` = 1, `rim`.`quantity`, 0)) AS cardboard,
			SUM(IF(`rim`.`materialId` = 3, `rim`.`quantity`, 0)) AS aluminum,
			SUM(IF(`rim`.`materialId` = 8, `rim`.`quantity`, 0)) AS film,
			SUM(IF(`rim`.`materialId` = 5, `rim`.`quantity`, 0)) AS plastic,
			SUM(IF(`rim`.`materialId` = 7, `rim`.`quantity`, 0)) AS trees,
			SUM(`rim`.`quantity` * `rim`.`pricePerUnit`) AS rebate,
			SUM(
				CASE `rim`.`materialId`
					WHEN 1 THEN 
						`rim`.`quantity` * 9
					ELSE
						0
				END
			) AS landfill

		FROM
			`RecyclingInvoices` AS ri

		INNER JOIN `Stores` AS s 
			ON (`ri`.`locationId` = `s`.`id`)

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		LEFT OUTER JOIN `RecyclingInvoicesMaterials` AS rim 
			ON (`rim`.`invoiceId` = `ri`.`id` AND `rim`.`unit` = 1)

		WHERE
			`ri`.`locationType` = 'STORE' 
			AND `ri`.`locationId` = `s`.`id`
			AND `st`.`region` = '" . $region . "'
			AND (`ri`.`invoiceDate` BETWEEN '" . $startDate . "' AND ' " . $endDate . "')

		GROUP BY
			`ri`.`id`

		ORDER BY
			`ri`.`invoiceDate` ASC";
		$query = $this->db->query($s);
		$temp = $query->result('array');
		//echo '<pre>'; dump($temp);

		$invoices	= array();
		$invoices_sum	= array(
			'districts_count'	=> 0,
			'locations_count'	=> 0,
			'sqft'		=> 0,
			'cardboard'	=> 0,
			'aluminum'	=> 0,
			'film'		=> 0,
			'plastic'	=> 0,
			'trees'		=> 0,
			'landfill'	=> 0,
			'kwh'		=> 0,
			'co2'		=> 0,
			'rebate'	=> 0,
		);
		$by_months = array();

		$materialTons = 0;

		$tmp_districts = array();
		$tmp_locations = array();

		while (list($k,$v) = each($temp))
		{
			// Every invoice
			$invoices[$v['id']] = array(
				'region'		=> $v['region'],
				'district'		=> $v['district'],
				'district_id'	=> $v['district_id'],
				'location'		=> $v['location'],
				'location_id'	=> $v['location_id'],
				'24H'			=> $v['24H'],
				'sqft'			=> $v['DC_squareFootage'],
				'cardboard'		=> $v['cardboard'],
				'aluminum'		=> $v['aluminum'],
				'film'			=> $v['film'],
				'plastic'		=> $v['plastic'],
				'trees'			=> $v['trees'],
				'landfill'		=> $v['landfill'],
				'kwh'			=>	($v['cardboard']	* $materials[1]['kwh']
									+ $v['aluminum']	* $materials[3]['kwh']
									+ $v['film']		* $materials[8]['kwh']
									+ $v['plastic']		* $materials[5]['kwh']
									+ $v['trees']		* $materials[7]['kwh']),

				'co2'			=>	($v['cardboard']	* $materials[1]['co2']
									+ $v['aluminum']	* $materials[3]['co2']
									+ $v['film']		* $materials[8]['co2']
									+ $v['plastic']		* $materials[5]['co2']
									+ $v['trees']		* $materials[7]['co2']),

				'rebate'		=> (double)$v['rebate'],
			);


			// Invoices summary
			$invoices_sum['sqft']		+= $invoices[$v['id']]['sqft'];
			$invoices_sum['cardboard']	+= $invoices[$v['id']]['cardboard'];
			$invoices_sum['aluminum']	+= $invoices[$v['id']]['aluminum'];
			$invoices_sum['film']		+= $invoices[$v['id']]['film'];
			$invoices_sum['plastic']	+= $invoices[$v['id']]['plastic'];
			$invoices_sum['trees']		+= $invoices[$v['id']]['trees'];
			$invoices_sum['landfill']	+= $invoices[$v['id']]['landfill'];
			$invoices_sum['kwh']		+= $invoices[$v['id']]['kwh'];
			$invoices_sum['co2']		+= $invoices[$v['id']]['co2'];
			$invoices_sum['rebate']		+= $invoices[$v['id']]['rebate'];


			// All recycled materials in tons
			$materialTons +=  $invoices[$v['id']]['cardboard']
							+ $invoices[$v['id']]['aluminum']
							+ $invoices[$v['id']]['film']
							+ $invoices[$v['id']]['plastic']
							+ $invoices[$v['id']]['trees'];


			// Tonage by months
			$d = $v['invoice_y'] . $v['invoice_m'];

			if(!isset($by_months[$d]))
			{
				$by_months[$d] = array(
					'period'	=> $v['invoice_m'] . '/' . $v['invoice_y'],
					'waste'		=> 0,
					'cardboard'	=> 0,
					'aluminum'	=> 0,
					'film'		=> 0,
					'plastic'	=> 0,
				);
			}

			$by_months[$d]['waste']		+=   $invoices[$v['id']]['cardboard']
										   + $invoices[$v['id']]['aluminum']
										   + $invoices[$v['id']]['film']
										   + $invoices[$v['id']]['plastic'];
			$by_months[$d]['cardboard']	+= $invoices[$v['id']]['cardboard'];
			$by_months[$d]['aluminum']	+= $invoices[$v['id']]['aluminum'];
			$by_months[$d]['film']		+= $invoices[$v['id']]['film'];
			$by_months[$d]['plastic']	+= $invoices[$v['id']]['plastic'];

			$tmp_districts[] = $v['district_id'];
			$tmp_locations[] = $v['location_id'];
		}

		$invoices_sum['districts_count'] = count(array_unique($tmp_districts));
		$invoices_sum['locations_count'] = count(array_unique($tmp_locations));


		// chart data
		$chart_data = array();
		foreach($by_months AS $v)
		{
			$chart_data['period'][]		= $v['period'];
			$chart_data['waste'][]		= $v['waste'];
			$chart_data['cardboard'][]	= $v['cardboard'];
			$chart_data['aluminum'][]	= $v['aluminum'];
			$chart_data['film'][]		= $v['film'];
			$chart_data['plastic'][]	= $v['plastic'];
		}


		return array(
			'rows'			=> $invoices, 
			'sum'			=> $invoices_sum,
			'by_months'		=> $by_months,
			'chart_data'	=> $chart_data,
			'materialTons'	=> $materialTons,
		);
	}


	/**
	 * @param double recycled recycled materials tons
	 * @param double waste waste materials tons
	 * @retrun double diversion rate
	 */
	public function getDiversionRate($recycled, $waste)
	{
		if($recycled == 0)
		{
			return 0;
		}
		else
		{
			return (double) round(($recycled / ($waste + $recycled)), 2);
		}
	}


	/**
	 * @param string startDate date in format YYYY-MM-DD
	 * @param string endDate date in format YYYY-MM-DD
	 * @param string region region name (Southeast, West, East...) States.region
	 * @return array mixed
	 */
	function getSingleDCCost($startDate, $endDate, $region) 
	{
/*
		$fees = array(
			1 => 'Freight Charge',
			2 => 'Fuel Charge',
			3 => 'Stop Charge',
			4 => 'Tax',
			5 => 'Other',
			6 => 'Repair',
		);
*/
		/////////////////////////////////////////////////
		// Waste
		$s = "
		SELECT 
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			IF(
				`s`.`open24hours` = 1,
				'Y', 
				'N'
			) AS 24H,
			`s`.`squareFootage` AS DC_squareFootage,
			`wi`.`id`,
			DATE_FORMAT(`wi`.`invoiceDate`, '%Y') AS invoice_y,
			DATE_FORMAT(`wi`.`invoiceDate`, '%m') AS invoice_m,
			`ws`.`quantity`, 
			`wf`.`feeType`, 
			`wf`.`feeAmount`,
			`wf`.`waived`,
			`ws`.`rate`,
			`ws`.`schedule`,
			`srst`.`name` AS sname

		FROM 
			`WasteInvoices` AS wi

		INNER JOIN `Stores` AS s 
			ON (`wi`.`locationId` = `s`.`id`)

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		LEFT OUTER JOIN 
			`WasteInvoiceServices` AS ws ON `wi`.`id` = `ws`.`invoiceId`

		LEFT OUTER JOIN 
			`SupportRequestServiceTypes` AS srst ON `ws`.`serviceId` = `srst`.`id`

		LEFT OUTER JOIN 
			`WasteInvoiceFees` AS wf ON `wi`.`id` = `wf`.`invoiceId`

		WHERE 
			`wi`.`locationType` = 'STORE' 
			AND `ws`.`unitID` = 1
			AND `wi`.`locationId` = `s`.`id`
			AND `st`.`region` = '" . $region . "'
			AND (`wi`.`invoiceDate` BETWEEN '" . $startDate . "' AND '" . $endDate . "')

		ORDER BY
			`wi`.`invoiceDate` ASC";

		$q = $this->db->query($s);
		$waste_data = $q->result('array');

		$tmp_districts = array();
		$tmp_locations = array();

		$tmp = array();
		while (list($k,$v) = each($waste_data))
		{
			$d = $v['invoice_y'] . $v['invoice_m'];

			if(!isset($tmp[$d]))
			{
				$tmp[$d]['region']					= $v['region'];
				$tmp[$d]['district']				= $v['district'];
				$tmp[$d]['district_id']				= $v['district_id'];
				$tmp[$d]['location']				= $v['location'];
				$tmp[$d]['location_id']				= $v['location_id'];
				$tmp[$d]['24H']						= $v['24H'];
				$tmp[$d]['districts_count']			= 0;
				$tmp[$d]['locations_count']			= 0;

				$tmp[$d]['sqft']					= $v['DC_squareFootage'];
				$tmp[$d]['period']					= $v['invoice_m'] . '/' . $v['invoice_y'];
				$tmp[$d]['total_tonage']			= $v['quantity'];
				$tmp[$d]['waste_service']			= 0;
				$tmp[$d]['waste_service_shedule']	= 0;
				$tmp[$d]['waste_equipment_fee']		= 0;
				$tmp[$d]['waste_haul_fee']			= 0;
				$tmp[$d]['waste_disposal_fee']		= 0;
				$tmp[$d]['recycling_rebate']		= 0;
				$tmp[$d]['other_fee']				= 0;
				$tmp[$d]['net']						= 0;
				$tmp[$d]['cost']					= 0;
				$tmp[$d]['waived_fee']				= 0;
			}

			$tmp[$d]['waste_service'] += $v['feeAmount'];

			if($v['schedule'] > 0)
			{
				$tmp[$d]['waste_service_shedule']	+= $v['feeAmount'];
			}

			if($v['feeType'] == 6)
			{
				$tmp[$d]['waste_equipment_fee'] += $v['feeAmount'];
			}
			elseif($v['feeType'] == 1)
			{
				$tmp[$d]['waste_haul_fee'] += $v['feeAmount'];
			}
			elseif($v['feeType'] == 3)
			{
				$tmp[$d]['waste_disposal_fee'] += $v['feeAmount'];
			}
			else
			{
				$tmp[$d]['other_fee'] += $v['feeAmount'];
			}

			$tmp[$d]['recycling_rebate'] += $v['quantity'] * $v['rate'];
			$tmp[$d]['cost'] += $v['quantity'] * $v['rate'];

			if($v['waived'] == 1)
			{
				$tmp[$d]['waived_fee'] += $v['feeAmount'];
			}

			$tmp_districts[] = $v['district_id'];
			$tmp_locations[] = $v['location_id'];
		}


		/////////////////////////////////////////////////
		// Recycling
		$s = "
		SELECT 
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			IF(
				`s`.`open24hours` = 1,
				'Y', 
				'N'
			) AS 24H,
			`s`.`squareFootage` AS DC_squareFootage,
			`ri`.`id`,
			DATE_FORMAT(`ri`.`invoiceDate`, '%Y') AS invoice_y,
			DATE_FORMAT(`ri`.`invoiceDate`, '%m') AS invoice_m,
			`rim`.`quantity`,
			`rim`.`pricePerUnit`,
			ri.*,
			(
				SELECT 
					SUM(`feeAmount`)
				FROM 
					`RecyclingInvoicesFees` AS rif 
				WHERE 
					`ri`.`id` = `rif`.`invoiceId`
			) AS rif,

			(
				SELECT 
					SUM(`feeAmount`)
				FROM 
					`RecyclingInvoicesFees` AS rif 
				WHERE 
					`ri`.`id` = `rif`.`invoiceId`
					AND `rif`.`waived` = 1
			) AS waived_fee

		FROM 
			`RecyclingInvoices` AS ri

		INNER JOIN `Stores` AS s 
			ON (`ri`.`locationId` = `s`.`id`)

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		LEFT OUTER JOIN 
			`RecyclingInvoicesMaterials` AS rim ON (`rim`.`invoiceId` = `ri`.`id`)

		LEFT OUTER JOIN 
			`DistributionCenters` AS dc ON (`dc`.`id` = `ri`.`locationId`)

		WHERE 
			`ri`.`locationType` = 'STORE' 
			AND `ri`.`locationId` = `s`.`id`
			AND `st`.`region` = '" . $region . "'
			AND (`ri`.`invoiceDate` BETWEEN '" . $startDate . "' AND ' " . $endDate . "')

		ORDER BY
			`ri`.`invoiceDate` ASC";

		$q = $this->db->query($s);
		$recycle_data = $q->result('array');

		while (list($k,$v) = each($waste_data))
		{
			$d = $v['invoice_y'] . $v['invoice_m'];

			if(!isset($tmp[$d]))
			{
				$tmp[$d]['region']					= $v['region'];
				$tmp[$d]['district']				= $v['district'];
				$tmp[$d]['district_id']				= $v['district_id'];
				$tmp[$d]['location']				= $v['location'];
				$tmp[$d]['location_id']				= $v['location_id'];
				$tmp[$d]['24H']						= $v['24H'];
				$tmp[$d]['districts_count']			= 0;
				$tmp[$d]['locations_count']			= 0;

				$tmp[$d]['sqft']				= $v['DC_squareFootage'];
				$tmp[$d]['period']				= $v['invoice_m'] . '/' . $v['invoice_y'];
				$tmp[$d]['total_tonage']		= $v['quantity'];
				$tmp[$d]['waste_service']		= 0;
				$tmp[$d]['waste_equipment_fee']	= 0;
				$tmp[$d]['waste_haul_fee']		= 0;
				$tmp[$d]['waste_disposal_fee']	= 0;
				$tmp[$d]['recycling_rebate']	= 0;
				$tmp[$d]['other_fee']			= 0;
				$tmp[$d]['net']					= 0;
				$tmp[$d]['cost']				= 0;
				$tmp[$d]['waived_fee']			= $v['waived_fee'];
			}

			$tmp[$d]['recycling_rebate']	+= $v['pricePerUnit'] * $v['quantity'];

			$net =	  $v['recycling_rebate'] 
					- $v['waste_service'] 
					- ($v['rif'] * $v['quantity']);
			$tmp[$d]['net'] = ($net > 0) ? $net : 0;

			$tmp_districts[] = $v['district_id'];
			$tmp_locations[] = $v['location_id'];
		}

		$sum = array(
			'sqft'					=> 0,
			'total_tonage'			=> 0,
			'waste_service'			=> 0,
			'waste_service_shedule'	=> 0,
			'waste_equipment_fee'	=> 0,
			'waste_haul_fee'		=> 0,
			'waste_disposal_fee'	=> 0,
			'recycling_rebate'		=> 0,
			'other_fee'				=> 0,
			'net'					=> 0,
			'waived_fee'			=> 0,
		);

		$chart_data = array(
			'months'	=> array(),
			'net'		=> array(),
			'savings'	=> array(),
			'cost'		=> array(),
		);

		foreach($tmp AS $v)
		{
			$sum['sqft']					+= $v['sqft'];
			$sum['total_tonage']			+= $v['total_tonage'];
			$sum['waste_service']			+= $v['waste_service'];
			$sum['waste_service_shedule']	+= $v['waste_service_shedule'];
			$sum['waste_equipment_fee']		+= $v['waste_equipment_fee'];
			$sum['waste_haul_fee']			+= $v['waste_haul_fee'];
			$sum['waste_disposal_fee']		+= $v['waste_disposal_fee'];
			$sum['recycling_rebate']		+= $v['recycling_rebate'];
			$sum['other_fee']				+= $v['other_fee'];
			$sum['net']						+= $v['net'];
			$sum['waived_fee']				+= $v['waived_fee'];

			$chart_data['months'][]			= $v['period'];
			$chart_data['net'][]			= $v['net'];
			$chart_data['savings'][]		= $v['recycling_rebate'];
			$chart_data['cost'][]			= $v['waste_service'];
		}

		$sum['districts_count'] = count(array_unique($tmp_districts));
		$sum['locations_count'] = count(array_unique($tmp_locations));

		return array(
			'rows'			=> $tmp, 
			'sum'			=> $sum,
			'chart_data'	=> $chart_data,
		);
	}


	/**
	 * @param string region region name (Southeast, West, East...) States.region
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	public function getInvoices($region, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn='invoice_date', $sortDir='DESC')
	{
		$s = "
		SELECT
			SQL_CALC_FOUND_ROWS
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			`wi`.`id` AS id,
			`wi`.`invoiceNumber` AS invoice_num,
			DATE_FORMAT(`wi`.`invoiceDate`, '%m/%d/%Y') AS invoice_date,
			DATE_FORMAT(`wi`.`dateSent`, '%m/%d/%Y') AS sent_date,
			`s`.`city` AS location,
			`v`.`name` AS vendor,
			`m`.`name` AS material,
			`wis`.`quantity`,
			(`wis`.`quantity` * `wis`.`rate`) AS total_rebate,
			`wi`.`total`,
			'waste' AS row_type

		FROM
			WasteInvoiceServices AS wis

		INNER JOIN `WasteInvoices` AS wi 
			ON (`wi`.`id` = `wis`.`invoiceId`)

		INNER JOIN `Materials` AS m
			ON (`m`.`id` = `wis`.`materialId`)

		INNER JOIN `Stores` AS s 
			ON (`wi`.`locationId` = `s`.`id`)

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		INNER JOIN `Vendors` AS v 
			ON (`v`.`id` = `wi`.`vendorId`)

		WHERE 
			`wi`.`locationType` = 'STORE'
			AND `wi`.`locationId` = `s`.`id`
			AND `st`.`region` = '" . $region . "'

		UNION


		SELECT
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			`ri`.`id` AS id,
			`ri`.`poNumber` AS invoice_num,
			DATE_FORMAT(`ri`.`invoiceDate`, '%m/%d/%Y') AS invoice_date,
			DATE_FORMAT(`ri`.`dateSent`, '%m/%d/%Y') AS sent_date,
			`s`.`city` AS location,
			`v`.`name` AS vendor,
			`m`.`name` AS material,
			`rim`.`quantity` AS quantity,
			(`rim`.`quantity` * `rim`.`pricePerUnit`) AS total_rebate,
			`ri`.`total`,
			'recycle' AS row_type

		FROM
			`RecyclingInvoicesMaterials` AS rim

		INNER JOIN `RecyclingInvoices` AS ri 
			ON (`ri`.`id` = `rim`.`invoiceId`)

		INNER JOIN `Materials` AS m
			ON (`m`.`id` = `rim`.`materialId`)

		INNER JOIN `Stores` AS s 
			ON (`ri`.`locationId` = `s`.`id`)

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		INNER JOIN `Vendors` AS v 
			ON (`v`.`id` = `ri`.`vendorId`)

		WHERE 
			`ri`.`locationType` = 'STORE'
			AND `st`.`region` = '" . $region . "'

		ORDER BY
			" . $sortColumn . " " . $sortDir . "

		LIMIT 
			" . $iDisplayStart . ", " . $iDisplayLength;

/*
		$s = "
		SELECT
			SQL_CALC_FOUND_ROWS
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			`ri`.`id` AS id,
			`ri`.`poNumber` AS invoice_num,
			DATE_FORMAT(`ri`.`invoiceDate`, '%m/%d/%Y') AS invoice_date,
			DATE_FORMAT(`ri`.`dateSent`, '%m/%d/%Y') AS sent_date,
			`s`.`location`,
			`v`.`name` AS vendor,
			(
				SELECT m.name 
				FROM Materials as m
				LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.materialId = m.id
				WHERE
				rim.id = (
					SELECT rimm.id FROM RecyclingInvoicesMaterials as rimm
					WHERE
						rimm.invoiceId = ri.id
					ORDER BY rimm.quantity DESC
					LIMIT 1 
				)
			) AS material,
			(
				SELECT rimm.quantity FROM RecyclingInvoicesMaterials as rimm
				WHERE
					rimm.invoiceId = ri.id
				ORDER BY rimm.quantity DESC
				LIMIT 1 
			) AS quantity,
			'-' AS total_rebate,
			`ri`.`total`

		FROM
			`RecyclingInvoices` AS ri

		INNER JOIN `Stores` AS s 
			ON (`ri`.`locationId` = `s`.`id`)

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		INNER JOIN `Vendors` AS v 
			ON (`v`.`id` = `ri`.`vendorId`)

		WHERE 
			`ri`.`locationType` = 'STORE' 
			AND `ri`.`locationId` = `s`.`id`
			AND `st`.`region` = '" . $region . "'

		ORDER BY
			" . $sortColumn . " " . $sortDir . "

		LIMIT 
			" . $iDisplayStart . ", " . $iDisplayLength;
*/
/*
		$s = "
		UNION
		SELECT

			DATE_FORMAT(`wi`.`invoiceDate`, '%m/%d/%Y') AS invoice_date,

			`wi`.`invoiceNumber` AS invoice_num,

			DATE_FORMAT(`wi`.`dateSent`, '%m/%d/%Y') AS sent_date,

			`dc`.`name` AS location,

			`v`.`name` AS vendor,

			'-' AS material,

			'-' AS quantity,

			'-' AS total_rebate,

			`wi`.`total`

		FROM
			`WasteInvoices` AS wi


		RIGHT JOIN `DistributionCenters` AS dc 
			ON 
			(
				`dc`.`id` = `wi`.`locationId` 
				AND `wi`.`locationType` = 'DC'
			)

		RIGHT JOIN `Vendors` AS v 
			ON (`v`.`id` = `wi`.`vendorId`)

		WHERE 
			`dc`.`id` = " . $DC . "
		";
*/
		$q = $this->db->query($s);
		$rows = $q->result('array');

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();
		$row = $query->row();

		return array(
			'rows_count'	=> $row->i,
			'rows'			=> $rows
		);
	}


	/**
	 * @param string region region name (Southeast, West, East...) States.region
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	function getSupportRequests($region, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn='`sr`.`timeStamp`', $sortDir='DESC')
	{
		$s = "
		SELECT 
			SQL_CALC_FOUND_ROWS
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			`sr`.`id` AS service_id,
			DATE_FORMAT(`sr`.`timeStamp`, '%m/%d/%Y') AS r_date,
			CONCAT(`sr`.`firstName`, ' ', `sr`.`lastName`) AS contact,
			`sr`.`phone`,
			`sr`.`notes` AS description,
			`sr`.`complete`,
			IF(`sr`.`complete` = 1, 'Y', 'N') AS complete_word

		FROM 
			`SupportRequests` AS sr

		INNER JOIN `Stores` AS s 
			ON (`sr`.`locationId` = `s`.`id`)

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		WHERE
			`sr`.`locationId` = `s`.`id`
			AND `st`.`region` = '" . $region . "'

		ORDER BY
			" . $sortColumn . " " . $sortDir . "

		LIMIT 
			" . $iDisplayStart . ", " . $iDisplayLength;

		$q = $this->db->query($s);
		$rows = $q->result('array');

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();
		$row = $query->row();

		return array(
			'rows_count'	=> $row->i,
			'rows'			=> $rows
		);
	}


	/**
	 * @param string region region name (Southeast, West, East...) States.region
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	function getServices($region, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn='last_updated', $sortDir='DESC')
	{
		$s = "
		SELECT 
			SQL_CALC_FOUND_ROWS
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			DATE_FORMAT(`s`.`lastUpdated`, '%m/%d/%Y') AS r_date,
			`s`.`squareFootage` AS squareFootage,
			IF(
				`s`.`open24hours` = 1,
				'Y', 
				'N'
			) AS 24H,
			`c`.`containerType` AS container_type,
			`c`.`name` AS container_size,
			`ssd`.`name` AS duration,
			(
				CASE `ss`.`schedule`
					WHEN 1 THEN 'Weekly'
					WHEN 2 THEN 'Biweekly'
					WHEN 3 THEN 'Monthly'
					ELSE
						'On Call'
				END
			) AS frequency,
			(`ss`.`quantity` * `ss`.`rate`) AS total,
			DATE_FORMAT(`s`.`lastUpdated`, '%m/%d/%Y') AS last_updated

		FROM
			`StoreServices` ss

		INNER JOIN `Stores` AS s 
			ON (`ss`.`storeId` = `s`.`id`)

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		INNER JOIN `Containers` AS c 
			ON (`c`.`id` = `ss`.`containerId`)

		INNER JOIN `StoreServiceDurations` AS ssd 
			ON (`ssd`.`id` = `ss`.`durationId`)


		WHERE
			`st`.`region` = '" . $region . "'

		ORDER BY
			" . $sortColumn . " " . $sortDir . "

		LIMIT 
			" . $iDisplayStart . ", " . $iDisplayLength;

		$q = $this->db->query($s);
		$rows = $q->result('array');

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();
		$row = $query->row();

		return array(
			'rows_count'	=> $row->i,
			'rows'			=> $rows
		);
	}




	/**
	 * @param string region region name (Southeast, West, East...) States.region
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	function getLocations($region, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn='last_updated', $sortDir='DESC')
	{
		$s = "
		SELECT
			SQL_CALC_FOUND_ROWS
			'" . $region . "' AS region,
			`d`.`name` AS district,
			`d`.`number` AS district_id,
			`s`.`location`,
			`s`.`id` AS location_id,
			CONCAT(`s`.`addressLine1`, ' ', `s`.`addressLine2`) AS address,
			`s`.`city`,
			`st`.`name` AS state,
			`s`.`postCode` AS zip,
			IF(
				`s`.`open24hours` = 1,
				'Y', 
				'N'
			) AS 24H,
			`s`.`squareFootage` AS squareFootage,
			ROUND((SUM(`rim`.`quantity`) / (SUM(`wis`.`quantity`) + SUM(`rim`.`quantity`))), 2) AS diversion,
			ROUND(((SUM(`wi`.`total`) + SUM(`ri`.`total`))  / `s`.`squareFootage`), 2) AS CostSqft,
			DATE_FORMAT(`s`.`lastUpdated`, '%m/%d/%Y') AS last_updated

		FROM
			`Stores` AS s

		INNER JOIN `States` AS st
			ON (`st`.`id` = `s`.`stateId`)

		INNER JOIN `District` AS d
			ON (`d`.`number` = `s`.`districtId`)

		LEFT JOIN `WasteInvoices` AS wi
			ON (
				`wi`.`locationId` = `s`.`id` 
				AND `wi`.`locationType` = 'STORE'
			)

		LEFT JOIN `RecyclingInvoices` AS ri
			ON (
				`ri`.`locationId` = `s`.`id` 
				AND `ri`.`locationType` = 'STORE'
			)

		LEFT JOIN `WasteInvoiceServices` AS wis
			ON (
				`wis`.`unitId` = 1
				AND `wis`.`invoiceId` IN (
					SELECT 
						`wi2`.`id` 
					FROM 
						`WasteInvoices` AS wi2
					WHERE 
						`wi2`.`locationId` = `s`.`id` 
						AND `wi2`.`locationType` = 'STORE'
					)
			)

		LEFT JOIN `RecyclingInvoicesMaterials` AS rim
			ON (
				`rim`.`unit` = 1
				AND `rim`.`invoiceId` IN (
					SELECT 
						`ri2`.`id` 
					FROM 
						`RecyclingInvoices` AS ri2
					WHERE 
						`ri2`.`locationId` = `s`.`id` 
						AND `ri2`.`locationType` = 'STORE'
					)
			)

		WHERE 
			`st`.`region` = '" . $region . "'

		GROUP BY 
			`s`.`id`

		ORDER BY
			" . $sortColumn . " " . $sortDir . "

		LIMIT 
			" . $iDisplayStart . ", " . $iDisplayLength;

		$q = $this->db->query($s);
		$rows = $q->result('array');

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();
		$row = $query->row();

		return array(
			'rows_count'	=> $row->i,
			'rows'			=> $rows
		);
	}

	public function getListForSelect() {
	    $this->db->order_by("name", "asc"); 
	    $query = $this->db->get("Regions");

	    $data = $query->result();

	    $result = array();

	    foreach ($data as $item) {
		    $result[$item->id] = $item->name;
	    }

	    return $result;
	}
}



<?php

class Mapsmodel extends CI_Model {
	
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('GMapsHelper');
	}
	
	
	function calculateData($type, $period) {
		
		$xml = '<?xml version="1.0" encoding="utf-8"?>
				<kml xmlns="http://earth.google.com/kml/2.0">
				<Document>
				<!--Style Definitions (START)-->
				<Style id="0">
				<IconStyle>
				<Icon>
				<href>' .base_url() . "pushpins/60.png" .'</href>
				</Icon>
				</IconStyle>
				</Style>
				<Style id="1">
				<IconStyle>
				<Icon>
				<href>' .base_url() . "pushpins/60-80.png" .'</href>
				</Icon>
				</IconStyle>
				</Style>
				<Style id="2">
				<IconStyle>
				<Icon>
				<href>' .base_url() . "pushpins/80-85.png" .'</href>
				</Icon>
				</IconStyle>
				</Style>
				<Style id="3">
				<IconStyle>
				<Icon>
				<href>' .base_url() . "pushpins/85.png" .'</href>
				</Icon>
				</IconStyle>
				</Style>
				<Style id="4">
				<IconStyle>
				<Icon>
				<href>' .base_url() . "pushpins/100.png" .'</href>
				</Icon>
				</IconStyle>
				</Style>
				<!--Style Definitions (END)-->';
		
		/*
		//current month waste in tons
		$query = $this->db->query("
				SELECT wis.quantity AS waste, (wis.quantity * wis.rate) as sum, wi.* FROM WasteInvoices as wi
				LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
				WHERE wi.locationType = '".$type."' AND
				(wi.invoiceDate BETWEEN date_sub(now(), INTERVAL 6 MONTH) AND now())
				AND wis.unitId = 1
				");
				$waste = $query->row();
				//end of current month waste in tons
		
		//current month recycling in tons
		$query = $this->db->query("
				SELECT rim.quantity as recycling, (rim.quantity * rim.pricePerUnit) as sum, ri.* FROM RecyclingInvoices as ri
				LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
				WHERE ri.locationType = '".$type."' AND
				(ri.invoiceDate BETWEEN date_sub(now(), INTERVAL 6 MONTH) AND now())
				AND rim.unit = 1
				");
				$recycling = $query->row();
		*/
		
		if($type == "DC") {
			$queryTable = "DistributionCenters";
		} else {
			$queryTable = "Stores";
		}
		
		if($period == "month") {
			$periodString = " 1 MONTH ";
		} elseif ($period == "quarter") {
			$periodString = " 3 MONTH ";
		} elseif ($period == "six") {
			$periodString = " 6 MONTH ";
		} else {
			$periodString = " 1 YEAR ";
		}
		
		$query = $this->db->query("SELECT 
									V.*,
							    (SELECT SUM(wis.quantity * wis.rate) FROM WasteInvoices as wi
											LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
											WHERE wi.locationType = '".$type."' AND
											(wi.invoiceDate BETWEEN date_sub(now(), INTERVAL ".$periodString.") AND now())
											AND wis.unitId = 1 AND V.id = wi.locationId) AS w,

    
							    (SELECT SUM(rim.quantity * rim.pricePerUnit) FROM RecyclingInvoices as ri
													LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
													WHERE ri.locationType = '".$type."' AND
													(ri.invoiceDate BETWEEN date_sub(now(), INTERVAL ".$periodString.") AND now())
													AND rim.unit = 1 AND V.id = ri.locationId) AS r

								FROM ".$queryTable." AS V LIMIT 1000");
				
		foreach ($query->result("array") as $temp) {
			
			if($temp["r"] > 0) {
				$diversion = number_format(($temp["r"] / ($temp["r"] + $temp["w"])) * 100, 2);
				if($diversion < 60) {
					$diversionStyle = "#0";
				} elseif ($diversion < 80) {
					$diversionStyle = "#1";
				} elseif ($diversion < 85) {
					$diversionStyle = "#2";
				} elseif ($diversion < 99) {
					$diversionStyle = "#3";
				} else {
					$diversionStyle = "#4";
				}
			} else {
				$diversionStyle = "#0";
			}
			if($type == "DC") {
				$name = $temp["name"];
				$zip = $temp["zip"];
			} else {
				$name = $temp["location"];
				$zip = $temp["postCode"];
			}

			$coordinatesString = "";
			
			if($temp["lat"] && $temp["lng"]) {
				$coordinatesString = $temp["lng"] . ", " . $temp["lat"];
			} else {
				$coordinates = $this->gmapshelper->getLatLng($temp["addressLine1"] . " " . $temp["city"] . " " . $zip);
				
				if($coordinates) {
					$coordinatesString = $coordinates["lng"] . "," . $coordinates["lat"];
					
					$this->db->query(sprintf("UPDATE %s SET lng = %f, lat = %f WHERE id = %d", 
														$queryTable, $coordinates["lng"], $coordinates["lat"], $temp["id"]));
				} else {
					$coordinatesString = "";	
				}
			}
			if(! $coordinatesString) {
				continue;
			}			
			$xml .= sprintf("<Placemark>
								<name>
									<![CDATA[%s]]>
								</name>
								<description>
									<![CDATA[%s]]>
								</description>
								<styleUrl>
									%s
								</styleUrl>
								<Point>
									<coordinates>%s</coordinates>
								</Point>
							</Placemark>",
					$name,
					$name . "<br />" . $temp["city"] . "<br />" . $temp["addressLine1"],
					$diversionStyle,
					$coordinatesString
					);
		}

		$xml .= "</Document></kml>";
		
		//header("Content-Type: application/vnd.google-earth.kml+xml");
		header("Content-type: text/xml");
		echo $xml;
	}
}
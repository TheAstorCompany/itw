<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class GMapsHelper {
	public function getLatLng($address) {
		$apikey = '';	
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?key='.$apikey.'&userip='.$_SERVER['REMOTE_ADDR'].'&sensor=false&address=';
		$result = null;
		if ($res = file_get_contents($url . urlencode($address))) {			
			if ($jsonResult = (json_decode($res)->results)) {
				$point = array_pop($jsonResult)->geometry->location;
				$result = array();
				$result['lat'] = $point->lat;
				$result['lng'] = $point->lng;				
			}
		}
		return $result;
	}
	public function getPushPin($rate) {
		$rate = (int)$rate;
		if ($rate == 100) {
			return base_url() . "pushpins/100.png"; 	
		} elseif ($rate == 85) {
			return base_url() . "pushpins/85.png";
		} elseif ($rate > 80 && $rate < 85) {
			return base_url() . "pushpins/80-85.png";
		} elseif ($rate > 60 && $rate <= 80) {
			return base_url() . "pushpins/60-80.png";
		} else {
			return base_url() . "pushpins/60.png";
		}
	}
}
?>
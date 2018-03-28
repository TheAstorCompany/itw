<?php
include_once dirname(__FILE__).'/../basemodel.php';

class ScheduleModel extends BaseModel {
		
	public function getListForSelect($companyId) {
		return $this->getScheduleOptions();
	}
}
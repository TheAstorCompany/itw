<?php
include_once dirname(__FILE__).'/../basemodel.php';

class SupportRequestModel extends BaseModel {
	private $table = 'SupportRequests';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();	
	}
	
	public function getById($companyId, $supportRequestId) {
		$companyId = (int)$companyId;
		$supportRequestId = (int)$supportRequestId;
		
		$this->db->select('IF(locationType = "DC", (SELECT name FROM DistributionCenters WHERE id = locationId), (SELECT location FROM Stores WHERE id = locationId) ) as locationName', false);
		$this->db->select($this->table . '.*, (SELECT name FROM Vendors WHERE Vendors.id=SupportRequests.vendorId) as vendorName');
				
		$query = $this->db->get_where($this->table,
			array(
				'companyId' => $companyId,
				'id' => $supportRequestId
			),
			1
		);
		
		$result = $query->row();
		$result->tasks = array();
		//read tasks
        $this->db->order_by('serviceDate', 'asc');
        $query = $this->db->get_where('SupportRequestTasks', array('supportRequestId' => $supportRequestId));
		foreach($query->result() as $task) {			
			if(isset($task->removalDate)) {
				$task->removalDate = $this->SQLToUSDate($task->removalDate);
			}
			if(isset($task->deliveryDate)) {
				$task->deliveryDate = $this->SQLToUSDate($task->deliveryDate);
			}
			if(isset($task->serviceDate)) {
				$task->serviceDate = $this->SQLToUSDate($task->serviceDate);
			}
			$result->tasks[] = $task;
		}				
		/*if (isset($result->removalDate)) {
			$result->removalDate = $this->SQLToUSDate($result->removalDate);
		}
		if (isset($result->deliveryDate)) {
			$result->deliveryDate = $this->SQLToUSDate($result->deliveryDate);
		} 
		if (isset($result->deliveryDate)) {
			$result->deliveryDate = $this->SQLToUSDate($result->deliveryDate);
		}*/
		return $result;
	}
	
	public function getList($companyId, $start, $length, $searchFilter = array(), $orderColumn = null, $orderDir = null) {
		$companyId = (int)$companyId;
		
		$this->db->select('SQL_CALC_FOUND_ROWS '.$this->table.'.*, '.$this->table.'.id,
                    concat(\'• \', group_concat(SupportRequestTasks.description SEPARATOR \'   <br />• \')) as description, 
		    group_concat(SupportRequestTasks.serviceDate SEPARATOR \'   <br />• \') as serviceDate, 
		    SupportRequestTasks.containerId as containerId, 
		    SupportRequestTasks.purposeId as purposeId, 
		    concat(\'• \', group_concat((select Containers.name from Containers where Containers.id = containerId) SEPARATOR \'   <br />• \')) as containername,
		    concat(\'• \', group_concat((select SupportRequestServiceTypes.name from SupportRequestServiceTypes where SupportRequestServiceTypes.id = purposeId) SEPARATOR \'   <br />• \')) as purposename' , false);
        // ' . 'IF(locationType = "DC", (SELECT name FROM DistributionCenters WHERE DistributionCenters.id = locationId), (SELECT location FROM Stores WHERE Stores.id = locationId) ) as locationName
                
		$this->db->join('SupportRequestTasks', 'SupportRequestTasks.supportRequestId = '.$this->table.'.id', 'left');
                
        $this->db->group_by("SupportRequests.id");

        if(isset($searchFilter['requestDateStart'])) {
            $this->db->where($this->table.'.lastUpdated >=', $searchFilter['requestDateStart']);
        }
        if(isset($searchFilter['requestDateEnd'])) {
            $this->db->where($this->table.'.lastUpdated <=', $searchFilter['requestDateEnd']);
        }
        if(isset($searchFilter['complete'])) {
            $this->db->where($this->table.'.complete', $searchFilter['complete']);
        }
        if(!empty($searchFilter['searchToken'])) {
            $searchToken = trim($searchFilter['searchToken']);
            $this->db->where(
                '('.$this->table.'.id LIKE "%'.$searchToken.'%" OR '.
                $this->table.'.lastUpdated LIKE "%'.$searchToken.'%" OR '.
                $this->table.'.firstName LIKE "%'.$searchToken.'%" OR '.
                $this->table.'.lastName LIKE "%'.$searchToken.'%" OR '.
                $this->table.'.lastUpdated LIKE "%'.$searchToken.'%" OR '.
                $this->table.'.cbre LIKE "%'.$searchToken.'%" OR '.
                'SupportRequestTasks.description LIKE "%'.$searchToken.'%" OR '.
                'SupportRequestTasks.serviceDate LIKE "%'.$searchToken.'%" OR '.
                '(select Containers.name from Containers where Containers.id = SupportRequestTasks.containerId) LIKE "%'.$searchToken.'%" OR '.
                '(select SupportRequestServiceTypes.name from SupportRequestServiceTypes where SupportRequestServiceTypes.id = SupportRequestTasks.purposeId) LIKE "%'.$searchToken.'%" OR '.
                'IF(locationType = "DC", (SELECT name FROM DistributionCenters WHERE DistributionCenters.id = locationId), (SELECT location FROM Stores WHERE Stores.id = locationId) ) LIKE "%'.$searchToken.'%")');
        }
		
		if ($orderColumn) {
			$this->db->order_by($orderColumn, $orderDir);
		}
		
		$query = $this->db->get_where($this->table,
			array(
				$this->table.'.companyId' => $companyId
			),
			$length,
			$start
		);
		
		$result = $query->result();
        //echo $this->db->last_query();

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();
		
		$row = $query->row();

		return array(
			'records' => $row->i,
			'data' => $result
		);
	}
	
	public function getLast4Weeks($locationType, $locationId = 0) {
		$result = array();
		$current_sunday  = mktime(0, 0, 0, date('m'), (date('d') - date('w')), date('Y'));
		$one = 60*60*24;
		
		$first_sunday = $current_sunday - (3 * 7 * $one); 
		
		for($i=0; $i<4; $i++) {
			$this->db->select('*', false);

            $w = array(
                'locationType =' => $locationType,
                'lastUpdated >=' => date('Y-m-d', ($first_sunday + (($i + 0) * 7 * $one))),
                'lastUpdated <=' => date('Y-m-d', ($first_sunday + (($i + 1) * 7 * $one)))
            );
            if($locationId>0) {
                $w['locationId ='] = $locationId;
            }

			$query = $this->db->get_where($this->table, $w);

			$result[($first_sunday + (($i + 0) * 7 * $one))] = $query->result();
		}
		
		return $result;
	}
	
	public function add($companyId, $userId, $data, $tasks) {
		$companyId = (int)$companyId;
		$userId = (int)$userId;		
		if (!isset($data['lastUpdated'])) {
			$data['lastUpdated'] = date('m/d/Y');
		}
		$data['userId'] = $userId;
		$data['companyId'] = $companyId;				
		$data['lastUpdated'] = $this->USToSQLDate($data['lastUpdated']);		
		$data = $this->cleanFields($data);
		$this->db->insert($this->table, $data);
		if (is_array($tasks)) {
			if ($id = $this->db->insert_id()) {
                $this->addEvent('support_request', $id, 'add', $userId);
				foreach($tasks as $task) {
					$task = (array)$task;
					$task['supportRequestId'] = $id;
					if(isset($task['removalDate'])) {
						$task['removalDate'] = $this->USToSQLDate($task['removalDate']);
					} else {
						$task['removalDate'] = null;
					}
					if(isset($task['deliveryDate'])) {
						$task['deliveryDate'] = $this->USToSQLDate($task['deliveryDate']);
					} else {
						$task['deliveryDate'] = null;
					}
					if(isset($task['serviceDate'])) {
						$task['serviceDate'] = $this->USToSQLDate($task['serviceDate']);
					} else {
						$task['serviceDate'] = null;
					}
					$this->db->insert('SupportRequestTasks', $task);
				}
			}
		}
		return !!$this->db->affected_rows();
	}
	
	public function edit($companyId, $userId, $supportRequestId, $data, $tasks) {
		$companyId = (int)$companyId;
		$userId = (int)$userId;		
		if (!isset($data['lastUpdated'])) {
			$data['lastUpdated'] = date('m/d/Y');
		}
		$data['userId'] = $userId;
		$data['companyId'] = $companyId;				
		$data['lastUpdated'] = $this->USToSQLDate($data['lastUpdated']);
		
		$is_closing = (isset($data['complete_old']) && $data['complete_old']==0 && $data['complete']==1);
		
		$data = $this->cleanFields($data);
		
		unset($data['companyId']);
		unset($data['userId']);
		
		$this->db->where(
			array(
				'companyId' => $companyId,
				'id' =>	$supportRequestId
			)
		);
		
		$this->db->update($this->table, $data);
		$this->addEvent('support_request', $supportRequestId, ($is_closing ? 'close' : 'change'));
		
		//tasks		
		if (is_array($tasks)) {
			$notForDelete = array();
			foreach($tasks as $task) {
				$task = (array)$task;
				$task['supportRequestId'] = $supportRequestId;
				if(isset($task['removalDate'])) {
				    if($task['removalDate']=='') {
					    $task['removalDate']='0000-00-00';
				    } else {
					    $task['removalDate'] = $this->USToSQLDate($task['removalDate']);
				    }
				} else {
					$task['removalDate']='0000-00-00';
				}
				if(isset($task['deliveryDate'])) {
				    if($task['deliveryDate']=='') {
					    $task['deliveryDate']='0000-00-00';
				    } else {
					    $task['deliveryDate'] = $this->USToSQLDate($task['deliveryDate']);
				    }
				} else {
					$task['deliveryDate']='0000-00-00';
				}
				if(isset($task['serviceDate'])) {
				    if($task['serviceDate']=='') {
					    $task['serviceDate']='0000-00-00';
				    } else {
					    $task['serviceDate'] = $this->USToSQLDate($task['serviceDate']);
				    }
				} else {
					$task['serviceDate']='0000-00-00';
				}				
				if (!isset($task['id'])) {
					//add
					$this->db->insert('SupportRequestTasks', $task);
					$notForDelete[] = $this->db->insert_id();
				} else if ($taskId = (int)$task['id']) {
					//update
					$notForDelete[] = $taskId;
					$this->db->where(array('id' => $taskId));
					$this->db->update('SupportRequestTasks', $task);
				}
			}
			if (count($notForDelete) > 0) {
				//remove old					
				$this->db->query('DELETE FROM SupportRequestTasks WHERE id NOT IN (' . implode(',', $notForDelete) . ') AND supportRequestId = ' . $supportRequestId);
			} else {
				//remove all
				$this->db->query('DELETE FROM SupportRequestTasks WHERE supportRequestId = ' . $supportRequestId);
			}			
		}	
		
		return !!$this->db->affected_rows();
	}

    public function addTask($task) {
        $this->db->insert('SupportRequestTasks', $task);
        return $this->db->insert_id();
    }

    public function deleteTask($taskId) {
        $this->db->delete('SupportRequestTasks', array('id' => $taskId));
    }
	
	public function deleteById($id) {
            $id = (int)$id;

            $this->db->query("
                    DELETE FROM {$this->table} WHERE
                            id = $id
                    LIMIT 1
            ");

            $r = !!$this->db->affected_rows();

            $this->addEvent('support_request', $id, 'delete');

            return $r;
	}

    public function getLocationTypes() {
        $query = $this->db->query('
            SELECT * FROM
              (
            SELECT s.id, s.location AS locationID, "STORE" AS locationType FROM Stores s
            UNION
            SELECT dc.id, dc.number AS locationID, "DC" AS locationType FROM DistributionCenters dc
              ) t
              ORDER BY locationID');

        $rows = $query->result();
        $result = array();
        foreach($rows as $row){
            $result[$row->locationID] = array('locationType' => $row->locationType, 'id' => $row->id);
        }

        return $result;
    }

    public function addEmailParsing($data) {
        $this->db->insert('EmailParsing', $data);
    }

    public function IsCBREExists($cbre) {
        $sql = 'SELECT COUNT(*) AS cbre_count FROM `SupportRequests` WHERE `cbre` = "'.$cbre.'"';
        $query = $this->db->query($sql);
        $result = $query->row();

        return $result->cbre_count > 0;
    }

	private function USToSQLDate($date) {
		//US date format mm/dd/YYYY
		//SQL format YYYY-mm-dd
		if (!empty($date)) {
			$temp = explode('/', $date);
			
			if (count($temp) != 3) {
				return $date;
			}
			
			$date = $temp[2] .'-'. $temp[0] . '-' . $temp[1];
		}
		
		return $date;
	}
	
	private function SQLToUSDate($date) {
		//US date format mm/dd/YYYY
		//SQL format YYYY-mm-dd
		if (!empty($date)) {
			$temp = explode('-', $date);
			
			if (count($temp) != 3) {
				return $date;
			}
			
			$isZero = true;
			
			foreach ($temp as $num) {
				if ($num != 0) {
					$isZero = false;
					break;
				}	
			}
			
			if ($isZero) {
				return '';
			}
			
			$date = $temp[1] .'/'. $temp[2] . '/' . $temp[0];
		}
		
		return $date;
	}
	
	
	private function cleanFields($data) {
		$tableFields = $this->db->list_fields($this->table);
		$result = array();
		
		foreach ($tableFields as $field) {
			if (array_key_exists($field, $data)) {
				$result[$field] = $data[$field];
			}
		}

		return $result;
	}
}
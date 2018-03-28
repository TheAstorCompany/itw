<?php
class Autocomplete extends CI_Model 
{
    // Properties defined here
    private $maxItemsPerAutocomplete = 10000;

    // Constructor
    public function __construct() {
            parent::__construct();
            $this->load->database();	
    }

    // Methods defined here
    ////////////////////////////////////////////////////////////////////////////
    public function vendor($token, $companyId) {
	$companyId = (int)$companyId;

	if($token!=rtrim($token)) {
		$like_token = rtrim($token);			
	} else {
		$like_token = $token.'%';
	}

	$query = $this->db->query('SELECT `Vendors`.*, `States`.`name` AS statename FROM `Vendors` JOIN `States` ON `Vendors`.stateId = `States`.id WHERE `Vendors`.`companyId` = '.$companyId.' AND `Vendors`.`status` = 1 AND (`Vendors`.addressLine1 like "%'.$like_token.'%" OR `Vendors`.`number` like "%'.$like_token.'%" OR `Vendors`.`name` like "%'.$like_token.'%")');

	$result = array();
	$data = $query->result();

	foreach ($data as $k => $v) {
	    $result[] = array(
		    'id' => $v->id,
		    'label' => '#'. $v->number . ', ' . $v->name . ', '.$v->addressLine1 . ', '.$v->city.', '.$v->statename,
		    'value' => $v->name,
		    'phone' => $v->phone,
		    'email' => $v->email,
            'remitTo' => $v->remitTo
	    );
	}


	return $result;
    }
    ////////////////////////////////////////////////////////////////////////////
    public function vendorByLocation($token, $companyId) {
            $companyId = (int)$companyId;

            $this->db->select();
            $this->db->like(
                    array(
                            'addressLine1' => $token
                    )
            );

            $query = $this->db->get_where('Vendors',
                    array(
                            'companyId' => $companyId
                    ),
                    $this->maxItemsPerAutocomplete
            );

            $result = array();
            $data = $query->result();

            foreach ($data as $k=>$v) {
                    $result[] = array(
                            'id' => $v->id,
                            'label' => $v->addressLine1,
                            'value' => $v->addressLine1,
                            'number' => $v->number
                    );
            }


            return $result;
    }

    public function vendorByNumber($token, $companyId) {
            $companyId = (int)$companyId;

            $this->db->select();
            $this->db->like(
                    array(
                            'number' => $token
                    )
            );

            $query = $this->db->get_where('Vendors',
                    array(
                            'companyId' => $companyId
                    ),
                    $this->maxItemsPerAutocomplete
            );

            $result = array();
            $data = $query->result();

            foreach ($data as $k=>$v) {
                    $result[] = array(
                            'id' => $v->id,
                            'label' => $v->number,
                            'value' => $v->number,
                            'addressLine1' => $v->addressLine1
                    );
            }


            return $result;
    }
    public function vendorBySomething($token, $companyId) {
            $companyId = (int)$companyId;

            $this->db->select();
            $this->db->like(
                    array(
                            'number' => $token,
                    		
                    )
            );
            $this->db->or_like(
                    array(
                            'name' => $token
                    )
            );

            $query = $this->db->get_where('Vendors',
                    array(
                            'companyId' => $companyId
                    ),
                    $this->maxItemsPerAutocomplete
            );

            $result = array();
            $data = $query->result();

            foreach ($data as $k=>$v) {
                    $result[] = array(
                            'id' => $v->id,
                            'label' => $v->name . ', ' .$v->number,
                            'value' => $v->name,
                            'addressLine1' => $v->addressLine1
                    );
            }


            return $result;
    }    
    ////////////////////////////////////////////////////////////////////////////
    public function supportRequestLocation($token, $companyId) {
        $companyId = (int)$companyId;
        $itemsPerTable = ceil($this->maxItemsPerAutocomplete / 2);

        /*
		$this->db->or_like(
            array(
                'location' => $token,
            )
        );
		*/
		if($token!=rtrim($token)) {
			$like_token = rtrim($token);			
		} else {
			$like_token = $token.'%';
		}

        $this->db->select('Stores.*', true);
        $this->db->select('States.name as state');
        $this->db->join('States', 'States.id = Stores.stateId');

        $query = $this->db->get_where('Stores',
            array(
                'companyId' => $companyId,
				'status' => 'YES',
				'location like' => $like_token
            ),
            $itemsPerTable
        );

        $storeData = $query->result();


        $this->db->select('DistributionCenters.*', true);
        $this->db->select('DistributionCenters.name as name', false);
        $this->db->select('States.name as state');
        $this->db->join('States', 'States.id = DistributionCenters.stateId');
		/*
        $this->db->or_like(
            array(
                'DistributionCenters.name' => $token,
                'DistributionCenters.number'=> $token,
            )
        );
		*/
		$this->db->or_where(array('DistributionCenters.name like' => $like_token, 'DistributionCenters.number like'=> $like_token));

		$query = $this->db->get_where('DistributionCenters',
            array(
                'companyId' => $companyId
            ),
            $itemsPerTable
        );

        $dcData = $query->result();

        $result = array();

        foreach ($storeData as $item) {
            $result[] = array(
                'id' => $item->id,
                'label' => 'Store ' . $item->location . ' ' . $item->city . ' ' . $item->state . ' ' . $item->postCode,
                'value' => $item->location,
				'franchise' => ($item->franchise==1),
                'payFees' => ($item->payFees=="1" ? 'Yes' : ($item->payFees=="0" ? 'No' : '')),
                'type' => 'STORE',
                'address' => $item->addressLine1,
                'phone' => $item->phone
            );	
        }

        foreach ($dcData as $item) {
            $result[] = array(
                'id' => $item->id,
                'label' => 'DC '.$item->name . ' ' . $item->city . ' ' . $item->state . ' ' . $item->zip,
                'value' => $item->name,
                'type' => 'DC',
                'address' => $item->addressLine1,
                'phone' => $item->phone
            );			
        }

        return $result;
    }
    ////////////////////////////////////////////////////////////////////////////
    public function VendorServicesLocation($token, $companyId) {
            $result = array();
            $companyId = (int)$companyId;		
            $this->db->select("VendorServices.id as id, VendorServices.name as name", false);
            $this->db->or_like(
                    array(
                            'VendorServices.name' => $token,
                    )
            );
            $this->db->join("Vendors", "Vendors.id = vendorId");
            $query = $this->db->get_where('VendorServices',
                    array(
                            'companyId' => $companyId
                    ),
                    $this->maxItemsPerAutocomplete
            );

            $items = $query->result();

            foreach ($items as $item) {
                    $result[] = array(
                            'id' => $item->id,
                            'label' => $item->name,
                            'value' => $item->name
                    );			
            }

            return $result;

    }


    public function distributionCenterServices($token, $companyId) {
            $companyId = (int)$companyId;

            $this->db->select();
            $this->db->select('DistributionCenterServices.name as name', false);

            $this->db->like(
                    array(
                            'DistributionCenterServices.name' => $token
                    ) 
            );

            $this->db->join('DistributionCenters', 'DistributionCenters.id = DistributionCenterServices.distributionCenterId');

            $query = $this->db->get_where('DistributionCenterServices',
                    array(
                            'DistributionCenters.companyId' => $companyId
                    ),
                    $this->maxItemsPerAutocomplete
            );

            $data = $query->result();
            $result = array();

            foreach ($data as $item) {
                    $result[] = array(
                            'id' => $item->id,
                            'label' => $item->name,
                            'value' => $item->name
                    );
            }

            return $result;
    }
    public function storeServices($token, $companyId) {
		$companyId = (int)$companyId;

		$this->db->select();
		$this->db->select('StoreServices.name as name', false);
		
		$this->db->like(
			array(
				'StoreServices.name' => $token
			) 
		);
		
		$this->db->join('Stores', 'Stores.id = StoreServices.storeId');
		
		$query = $this->db->get_where('StoreServices',
			array(
				'Stores.companyId' => $companyId
			),
			$this->maxItemsPerAutocomplete
		);
		
		$data = $query->result();
		$result = array();
		
		foreach ($data as $item) {
			$result[] = array(
				'id' => $item->id,
				'label' => $item->name,
				'value' => $item->name
			);
		}
		
		return $result;
	}
	////////////////////////////////////////////////////////////////////////////
	public function district($token) {
	
		$this->db->select();
		$this->db->or_like(
				array(
						'number' => $token
				)
		);
	
		$query = $this->db->get('District',
				$this->maxItemsPerAutocomplete
		);
	
		$result = array();
		$data = $query->result();
	
		foreach ($data as $k => $v) {
			$result[] = array(
					'id' => $v->number,
					'label' => '#'. $v->number . ', ' . $v->name,
					'value' => $v->number,
					'name' => $v->name
			);
		}
	
	
		return $result;
	}	
}
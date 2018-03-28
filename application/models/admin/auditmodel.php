<?php
    class AuditModel extends CI_Model {
        private $table = 'Audit';

        public function __construct() {
            parent::__construct();
            $this->load->database();
        }

        public function getObjectLog($object_type, $object_id) {
            $this->db->select('Audit.*, CompanyUsers.username', false);
            $this->db->select('DATE_FORMAT(Audit.dt, "%c/%e/%Y %l:%i%p") AS dt_us', false);
            $this->db->join('CompanyUsers', 'Audit.user_id = CompanyUsers.id');
            $this->db->order_by('Audit.dt', 'asc');
            $query = $this->db->get_where('Audit',
                array(
                    'object_type =' => $object_type,
                    'object_id =' => $object_id
                )
            );

            return $query->result();
        }
    }
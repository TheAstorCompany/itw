<?php

class Audit_table extends Widget {
    function run($object_type, $object_id) {

        $auditmodel = $this->load_model('admin/AuditModel');

        $rows = $auditmodel->getObjectLog($object_type, $object_id);

        $this->render('audit_table', array('rows'=>$rows));
    }
}
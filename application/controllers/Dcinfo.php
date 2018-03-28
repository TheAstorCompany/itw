<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/Front.php';

class Dcinfo extends Front
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("distributioncentersmodel");
        $this->assigns['_main_controller'] = 'Stores';
    }


    /**
     * @param integer id `distributioncenters`.`id`
     */
    public function Waste($id = 0)
    {
        $id = (int)$id;

        if ($id === 0) {
            log_message('error', 'DC id is empty');
            return;
        }

        $this->assigns['DCData'] = $this->distributioncentersmodel->getDC($id);
        if (empty($this->assigns['DCData'])) {
            $msg = 'Row with id ' . $id . ' not found in distributioncenters table';
            log_message('error', $msg);
            echo $msg;
            return;
        }

        $this->assigns['Contacts'] = $this->distributioncentersmodel->getContacts($id);
        $this->assigns["id"] = $id;

        $this->load->helper('dates');

        if ($this->input->get("from")) {
            $startDate = USToSQLDate($this->input->get("from"));
            $this->assigns["from"] = $this->input->get("from");
        } else {
            $startDate = USToSQLDate('01/01/' . date('Y'));
            $this->assigns["from"] = '01/01/' . date('Y');
        }

        if ($this->input->get("to")) {
            $endDate = USToSQLDate($this->input->get("to"));
            $this->assigns["to"] = $this->input->get("to");
        } else {
            $endDate = USToSQLDate(date('m/d/Y'));
            $this->assigns["to"] = date('m/d/Y');
        }

        $this->assigns['tblData'] = $this->distributioncentersmodel->getWasteTable($startDate, $endDate, $id);
        $this->assigns['recycling'] = $this->distributioncentersmodel->getRecycleInvoices($startDate, $endDate, $id);
        $this->assigns['DiversionRate'] = $this->distributioncentersmodel->getDiversionRate(
            $this->assigns['recycling']['materialTons'],
            $this->assigns['tblData']['sum']['waste']
        );

        //echo '<pre>'; dump($this->assigns['DCData']);

        if ($this->input->get("print")) {
            $this->load->view("dcinfo/wastePrint", $this->assigns);
        } elseif ($this->input->get("export")) {
            $this->load->helper('download');
            $csv = '';
            $file = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

            $colums = array("DC Name", "SqFt", "Period", "Waste", "Hazardous", "Other", "Cost");
            fputcsv($file, $colums);

            $data = array();

            foreach ($this->assigns['tblData']['rows'] as $temp) {
                $data = array(
                    $temp["dc_name"],
                    $temp["sqft"],
                    $temp["period"],
                    $temp["waste"],
                    $temp["hazardous"],
                    $temp["other"],
                    $temp["cost"]
                );

                fputcsv($file, $data);
            }

            rewind($file);
            $csv = stream_get_contents($file);
            fclose($file);
            force_download('Distribution_Center_Waste-' . $startDate . '_to_' . $endDate . '.csv', $csv);
        } else {
            $this->load->view("dcinfo/waste", $this->assigns);
        }
    }


    /**
     * @param integer id `distributioncenters`.`id`
     */
    public function Recycling($id = 0)
    {
        $id = (int)$id;

        if ($id === 0) {
            log_message('error', 'DC id is empty');
            return;
        }

        $this->assigns['DCData'] = $this->distributioncentersmodel->getDC($id);
        if (empty($this->assigns['DCData'])) {
            $msg = 'Row with id ' . $id . ' not found in distributioncenters table';
            log_message('error', $msg);
            echo $msg;
            return;
        }

        $this->assigns['Contacts'] = $this->distributioncentersmodel->getContacts($id);
        $this->assigns["id"] = $id;


        $this->load->helper('dates');

        if ($this->input->get("from")) {
            $startDate = USToSQLDate($this->input->get("from"));
            $this->assigns["from"] = $this->input->get("from");
        } else {
            $startDate = USToSQLDate('01/01/' . date('Y'));
            $this->assigns["from"] = '01/01/' . date('Y');
        }

        if ($this->input->get("to")) {
            $endDate = USToSQLDate($this->input->get("to"));
            $this->assigns["to"] = $this->input->get("to");
        } else {
            $endDate = USToSQLDate(date('m/d/Y'));
            $this->assigns["to"] = date('m/d/Y');
        }

        $this->assigns['tblData'] = $this->distributioncentersmodel->getWasteTable($startDate, $endDate, $id);
        $this->assigns['recycling'] = $this->distributioncentersmodel->getRecycleInvoices($startDate, $endDate, $id);
        $this->assigns['DiversionRate'] = $this->distributioncentersmodel->getDiversionRate(
            $this->assigns['recycling']['materialTons'],
            $this->assigns['tblData']['sum']['waste']
        );

        //echo '<pre>'; dump($this->assigns['tblData']);

        if ($this->input->get("print")) {
            $this->load->view("dcinfo/recyclingPrint", $this->assigns);
        } elseif ($this->input->get("export")) {
            $this->load->helper('download');
            $csv = '';
            $file = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

            $colums = array(
                "DC Name",
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

            foreach ($this->assigns['recycling']['rows'] as $temp) {
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
            force_download('Distribution_Center_Recycling-' . $startDate . '_to_' . $endDate . '.csv', $csv);
        } else {
            $this->load->view("dcinfo/recycling", $this->assigns);
        }
    }


    /**
     * @param integer id `distributioncenters`.`id`
     *
     *
     *    Diagram 2
     *    NET            = $v['recycling_rebate'] - $v['waste_service'] - ($v['rif'] * $v['quantity']);
     *    Savings        = recycling_rebate
     *    COST        = Waste Equipment Fee + Waste Haul Fee + Waste Disposal Fee + Other fee = waste_service
     *
     *    Diagram 3
     *    recycling rebates    = $v['recycling_rebate']
     *    SAMS                = waste_service WHERE WasteInvoiceServices.schedule >= 1 for choosen year
     *                          -(minus)
     *                          waste_service WHERE WasteInvoiceServices.schedule >= 1 for (choosen year - 1)
     *    waived fees            = WasteInvoiceFees && RecyclingInvoicesFees
     *
     */
    public function CostSavings($id = 0)
    {
        $id = (int)$id;

        if ($id === 0) {
            log_message('error', 'DC id is empty');
            return;
        }

        $this->assigns['DCData'] = $this->distributioncentersmodel->getDC($id);
        if (empty($this->assigns['DCData'])) {
            $msg = 'Row with id ' . $id . ' not found in distributioncenters table';
            log_message('error', $msg);
            echo $msg;
            return;
        }

        $this->assigns['Contacts'] = $this->distributioncentersmodel->getContacts($id);
        $this->assigns["id"] = $id;


        $this->load->helper('dates');

        if ($this->input->get("from")) {
            $startDate = USToSQLDate($this->input->get("from"));
            $this->assigns["from"] = $this->input->get("from");
        } else {
            $startDate = USToSQLDate('01/01/' . date('Y'));
            $this->assigns["from"] = '01/01/' . date('Y');
        }

        if ($this->input->get("to")) {
            $endDate = USToSQLDate($this->input->get("to"));
            $this->assigns["to"] = $this->input->get("to");
        } else {
            $endDate = USToSQLDate(date('m/d/Y'));
            $this->assigns["to"] = date('m/d/Y');
        }

        $this->assigns['waste'] = $this->distributioncentersmodel->getWasteTable($startDate, $endDate, $id);
        $this->assigns['recycling'] = $this->distributioncentersmodel->getRecycleInvoices($startDate, $endDate, $id);
        $this->assigns['DiversionRate'] = $this->distributioncentersmodel->getDiversionRate(
            $this->assigns['recycling']['materialTons'],
            $this->assigns['waste']['sum']['waste']
        );

        $this->assigns["tblData"] = $this->distributioncentersmodel->getSingleDCCost($startDate, $endDate, $id);

        $one_year_ago = $this->distributioncentersmodel->getSingleDCCost(
            date('Y-m-d', strtotime('-1 year', strtotime($startDate))),
            date('Y-m-d', strtotime('-1 year', strtotime($endDate))),
            $id
        );
        // current year
        $this->assigns["SAMS"] = (double)$this->assigns["tblData"]['sum']['waste_service_shedule'] - $one_year_ago['sum']['waste_service_shedule'];


        if ($this->input->get("print")) {
            $this->load->view("dcinfo/costSavingsPrint", $this->assigns);
        } elseif ($this->input->get("export")) {
            $this->load->helper('download');
            $csv = '';
            $file = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

            $colums = array(
                "DC Name",
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

            foreach ($this->assigns['tblData']['rows'] as $temp) {
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
            force_download('Distribution_Center_Cost_Savings-' . $startDate . '_to_' . $endDate . '.csv', $csv);
        } else {
            $this->load->view("dcinfo/costSavings", $this->assigns);
        }
    }


    /**
     * @param integer id `distributioncenters`.`id`
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
    public function Invoices($id = 0) {
        $id = (int)$id;

        if ($id === 0) {
            log_message('error', 'DC id is empty');
            return;
        }

        $this->assigns['DCData'] = $this->distributioncentersmodel->getDC($id);
        if (empty($this->assigns['DCData'])) {
            $msg = 'Row with id ' . $id . ' not found in distributioncenters table';
            log_message('error', $msg);
            echo $msg;
            return;
        }

        $this->assigns['id'] = $id;
        $this->assigns['Contacts'] = $this->distributioncentersmodel->getContacts($id);

        $this->assigns['waste'] = $this->distributioncentersmodel->getWasteTable('200-01-01', date('Y-m-d'), $id);
        $this->assigns['recycling'] = $this->distributioncentersmodel->getRecycleInvoices('200-01-01', date('Y-m-d'), $id);
        $this->assigns['DiversionRate'] = $this->distributioncentersmodel->getDiversionRate($this->assigns['recycling']['materialTons'], $this->assigns['waste']['sum']['waste']);

        if ($this->input->get("export")) {
            $this->load->helper('download');
            $file = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

            $colums = array(
                "Date",
                "Inv #",
                "Date Sent",
                "Vendor",
                "Scheduled (Tons)",
                "On Call (Tons)",
                "Cost",
            );

            fputcsv($file, $colums);

            $data = array();

            $rows = $this->distributioncentersmodel->getInvoices(
                $id,
                0,
                -1,
                'invoiceDate',
                'ASC'
            );

            foreach ($rows['rows'] as $temp) {
                $data = array(
                    $temp['invoiceDate'],
                    $temp['haulerInvNumber'],
                    $temp['dateSent'],
                    $temp['vendorName'],
                    $temp['scheduledTons'],
                    $temp['oncallTons'],
                    $temp['cost'],
                );

                fputcsv($file, $data);
            }

            rewind($file);
            $csv = stream_get_contents($file);
            fclose($file);
            force_download('Distribution_Center_Invoices.csv', $csv);
        } else {
            $this->assigns['current_tab'] = 'Invoices';
            $this->load->view("dcinfo/invoices", $this->assigns);
        }
    }

    public function ajax_Invoices($id = 0) {
        $id = (int)$id;

        header('Content-type: application/json');

        $sortColumn = 'invoiceDate';
        $sortDir = 'ASC';
        if ($this->input->get('iSortingCols') > 0) {
            if ($this->input->get('bSortable_0')) {
                $sortColumn_arr = array();
                $sortColumn_arr[] = 'invoiceDate';
                $sortColumn_arr[] = 'haulerInvNumber';
                $sortColumn_arr[] = 'dateSent';
                $sortColumn_arr[] = 'vendorName';
                $sortColumn_arr[] = 'scheduledTons';
                $sortColumn_arr[] = 'oncallTons';
                $sortColumn_arr[] = 'cost';

                $sortColumn = $sortColumn_arr[$this->input->get('iSortCol_0')];

                if ($this->input->get('sSortDir_0') == 'asc') {
                    $sortDir = 'ASC';
                } else {
                    $sortDir = 'DESC';
                }
            }
        }

        $data = $this->distributioncentersmodel->getInvoices(
            $id,
            $this->input->get('iDisplayStart'),
            $this->input->get('iDisplayLength'),
            $sortColumn,
            $sortDir
        );

        $ajaxData = array();
        while (list($k, $v) = each($data['rows'])) {
            $tmp = array();

            $tmp['DT_RowClass'] = 'gradeA';

            $tmp[] = '<a title="More about invoice #' . $v['id'] . '" href="' . base_url() . 'admin/DCInvoice/view/' . $v['id'] . '">' . $v['invoiceDate'] . '</a>';
            $tmp[] = $v['haulerInvNumber'];
            $tmp[] = $v['dateSent'];
            $tmp[] = $v['vendorName'];
            $tmp[] = round($v['scheduledTons'], 2);
            $tmp[] = round($v['oncallTons'], 2);
            $tmp[] = number_format(round($v['cost'], 2), 2, '.', '');

            $ajaxData[] = $tmp;
            unset($tmp);
        }

        echo json_encode(
            array(
                'aaData' => $ajaxData,
                'iTotalRecords' => $data['rows_count'],
                'iTotalDisplayRecords' => $data['rows_count']
            )
        );

        return;
    }

    public function Rebates($id = 0) {
        $id = (int)$id;

        if ($id === 0) {
            log_message('error', 'DC id is empty');
            return;
        }

        $this->assigns['DCData'] = $this->distributioncentersmodel->getDC($id);
        if (empty($this->assigns['DCData'])) {
            $msg = 'Row with id ' . $id . ' not found in distributioncenters table';
            log_message('error', $msg);
            echo $msg;
            return;
        }

        $this->assigns['id'] = $id;
        $this->assigns['Contacts'] = $this->distributioncentersmodel->getContacts($id);

        $this->assigns['waste'] = $this->distributioncentersmodel->getWasteTable('200-01-01', date('Y-m-d'), $id);
        $this->assigns['recycling'] = $this->distributioncentersmodel->getRecycleInvoices('200-01-01', date('Y-m-d'), $id);
        $this->assigns['DiversionRate'] = $this->distributioncentersmodel->getDiversionRate($this->assigns['recycling']['materialTons'], $this->assigns['waste']['sum']['waste']);

        if ($this->input->get("export")) {
            $this->load->helper('download');

            $file = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

            $colums = array(
                "Rebate Date",
                "Vendor",
                "Material",
                "Tons",
                "Rebate"
            );

            fputcsv($file, $colums);

            $data = array();

            $rows = $this->distributioncentersmodel->getRebates(
                $id,
                0,
                -1,
                'invoiceDate',
                'ASC'
            );

            foreach ($rows['rows'] as $temp) {
                $data = array(
                    $temp['invoiceDate'],
                    $temp['vendorName'],
                    $temp['materialName'],
                    $temp['tons'],
                    $temp['rebate']
                );

                fputcsv($file, $data);
            }

            rewind($file);
            $csv = stream_get_contents($file);
            fclose($file);
            force_download('Distribution_Center_Rebates.csv', $csv);
        } else {
            $this->assigns['current_tab'] = 'Rebates';
            $this->load->view("dcinfo/rebates", $this->assigns);
        }
    }

    public function ajax_Rebates($id = 0) {
        $id = (int)$id;

        header('Content-type: application/json');

        $sortColumn = 'invoiceDate';
        $sortDir = 'ASC';
        if ($this->input->get('iSortingCols') > 0) {
            if ($this->input->get('bSortable_0')) {
                $sortColumn_arr = array();
                $sortColumn_arr[] = 'invoiceDate';
                $sortColumn_arr[] = 'vendorName';
                $sortColumn_arr[] = 'materialName';
                $sortColumn_arr[] = 'tons';
                $sortColumn_arr[] = 'rebate';

                $sortColumn = $sortColumn_arr[$this->input->get('iSortCol_0')];

                if ($this->input->get('sSortDir_0') == 'asc') {
                    $sortDir = 'ASC';
                } else {
                    $sortDir = 'DESC';
                }
            }
        }

        $data = $this->distributioncentersmodel->getRebates(
            $id,
            $this->input->get('iDisplayStart'),
            $this->input->get('iDisplayLength'),
            $sortColumn,
            $sortDir
        );

        $ajaxData = array();
        while (list($k, $v) = each($data['rows'])) {
            $tmp = array();

            $tmp['DT_RowClass'] = 'gradeA';

            $tmp[] = $v['invoiceDate'];
            $tmp[] = $v['vendorName'];
            $tmp[] = $v['materialName'];
            $tmp[] = $v['tons'];
            $tmp[] = number_format(round($v['rebate'], 2), 2, '.', '');

            $ajaxData[] = $tmp;
            unset($tmp);
        }

        echo json_encode(
            array(
                'aaData' => $ajaxData,
                'iTotalRecords' => $data['rows_count'],
                'iTotalDisplayRecords' => $data['rows_count']
            )
        );

        return;
    }

    /**
     * @param integer id `distributioncenters`.`id`
     */
    public function SupportRequests($id = 0) {
        $id = (int)$id;

        if ($id === 0) {
            log_message('error', 'DC id is empty');
            return;
        }

        $this->assigns['DCData'] = $this->distributioncentersmodel->getDC($id);
        if (empty($this->assigns['DCData'])) {
            $msg = 'Row with id ' . $id . ' not found in distributioncenters table';
            log_message('error', $msg);
            echo $msg;
            return;
        }

        $this->assigns['Contacts'] = $this->distributioncentersmodel->getContacts($id);
        $this->assigns["id"] = $id;

        $this->assigns['waste'] = $this->distributioncentersmodel->getWasteTable('200-01-01', date('Y-m-d'), $id);
        $this->assigns['recycling'] = $this->distributioncentersmodel->getRecycleInvoices('200-01-01', date('Y-m-d'), $id);
        $this->assigns['DiversionRate'] = $this->distributioncentersmodel->getDiversionRate(
            $this->assigns['recycling']['materialTons'],
            $this->assigns['waste']['sum']['waste']
        );

        if ($this->input->get("export")) {
            $this->load->helper('download');
            $csv = '';
            $file = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

            $colums = array(
                "Location",
                "Service#",
                "Date",
                "Contact",
                "Phone#",
                "Description",
                "Resolved"
            );

            fputcsv($file, $colums);

            $data = array();

            $rows = $this->distributioncentersmodel->getSupportRequests(
                $id,
                $this->input->get('iDisplayStart'),
                -1,
                'location',
                'ASC'
            );

            foreach ($rows['rows'] as $temp) {
                $data = array(
                    $temp['location'],
                    $temp['service_id'],
                    $temp['r_date'],
                    $temp['contact'],
                    $temp['phone'],
                    $temp['description'],
                    $temp['complete_word']
                );

                fputcsv($file, $data);
            }

            rewind($file);
            $csv = stream_get_contents($file);
            fclose($file);
            force_download('Distribution_Center_Support_Requests.csv', $csv);
        } else {
            $this->assigns['current_tab'] = 'SupportRequests';
            $this->load->view("dcinfo/supportRequests", $this->assigns);
        }
    }


    /**
     * @param integer id `distributioncenters`.`id`
     */
    public function ajax_SupportRequests($id = 0) {
        $id = (int)$id;

        header('Content-type: application/json');

        if ($this->input->get('iSortingCols') > 0) {
            if ($this->input->get('bSortable_0')) {
                $sortColumn_arr = array();
                $sortColumn_arr[] = 'location';
                $sortColumn_arr[] = 'service_id';
                $sortColumn_arr[] = 'r_date';
                $sortColumn_arr[] = 'contact';
                $sortColumn_arr[] = 'phone';
                $sortColumn_arr[] = 'description';
                $sortColumn_arr[] = 'complete';

                $sortColumn = $sortColumn_arr[$this->input->get('iSortCol_0')];

                if ($this->input->get('sSortDir_0') == 'asc') {
                    $sortDir = 'ASC';
                } else {
                    $sortDir = 'DESC';
                }
            }
        }

        $data = $this->distributioncentersmodel->getSupportRequests(
            $id,
            $this->input->get('iDisplayStart'),
            $this->input->get('iDisplayLength'),
            $sortColumn,
            $sortDir
        );


        $ajaxData = array();
        while (list($k, $v) = each($data['rows'])) {
            $tmp = array();

            //////////////////////////////////////////////////////
            // Table row class decoration
            $tmp['DT_RowClass'] = ($v['complete'] == 1) ? 'gradeA' : 'gradeX';

            //////////////////////////////////////////////////////
            // Lnk
            if ($this->assigns['_isAdmin'] == 1) {
                $tmp[] = '<a href="' . base_url() . 'admin/SupportRequest/edit/' . $v['service_id'] . '">' . $v['location'] . '</a>';
            } else {
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
                'aaData' => $ajaxData,
                'iTotalRecords' => $data['rows_count'],
                'iTotalDisplayRecords' => $data['rows_count']
            )
        );

        return;
    }


    /**
     * @param integer id `distributioncenters`.`id`
     */
    public function SiteInfo($id = 0) {
        $id = (int)$id;

        if ($id === 0) {
            log_message('error', 'DC id is empty');
            return;
        }

        $this->assigns['DCData'] = $this->distributioncentersmodel->getDC($id);
        if (empty($this->assigns['DCData'])) {
            $msg = 'Row with id ' . $id . ' not found in distributioncenters table';
            log_message('error', $msg);
            echo $msg;
            return;
        }

        $this->assigns['Contacts'] = $this->distributioncentersmodel->getContacts($id);
        $this->assigns["id"] = $id;

        $this->assigns['waste'] = $this->distributioncentersmodel->getWasteTable('200-01-01', date('Y-m-d'), $id);
        $this->assigns['recycling'] = $this->distributioncentersmodel->getRecycleInvoices('200-01-01', date('Y-m-d'), $id);
        $this->assigns['DiversionRate'] = $this->distributioncentersmodel->getDiversionRate(
            $this->assigns['recycling']['materialTons'],
            $this->assigns['waste']['sum']['waste']
        );

        $this->assigns['vendors'] = $this->distributioncentersmodel->getVendorsByInvoices($id);

        if ($this->input->get("export")) {
            $this->load->helper('download');
            $file = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

            $colums = array(
                "Location",
                "SqFt",
                "Vendor",
                "ServiceType",
                "Container",
                "Frequency",
                "Cost",
            );

            fputcsv($file, $colums);

            $data = array();

            $rows = $this->distributioncentersmodel->getSiteInfo(
                $id,
                $this->input->get('iDisplayStart'),
                -1,
                'locationName',
                'ASC'
            );

            foreach ($rows['rows'] as $temp) {
                $data = array(
                    $temp['locationName'],
                    $temp['squareFootage'],
                    $temp['vendorName'],
                    $temp['serviceType'],
                    $temp['containerType'],
                    $temp['frequency'],
                    $temp['cost']
                );

                fputcsv($file, $data);
            }

            rewind($file);
            $csv = stream_get_contents($file);
            fclose($file);
            force_download('Distribution_Center_Site_Info.csv', $csv);
        } else {
            $this->assigns['current_tab'] = 'SiteInfo';
            $this->load->view("dcinfo/siteInfo", $this->assigns);
        }
    }


    /**
     * @param integer id `distributioncenters`.`id`
     */
    public function ajax_SiteInfo($id = 0) {
        $id = (int)$id;

        header('Content-type: application/json');

        if ($this->input->get('iSortingCols') > 0) {
            if ($this->input->get('bSortable_0')) {
                $sortColumn_arr = array();
                $sortColumn_arr[] = 'locationName';
                $sortColumn_arr[] = 'squareFootage';
                $sortColumn_arr[] = 'vendorName';
                $sortColumn_arr[] = 'serviceType';
                $sortColumn_arr[] = 'containerType';
                $sortColumn_arr[] = 'frequency';
                $sortColumn_arr[] = 'cost';

                $sortColumn = $sortColumn_arr[$this->input->get('iSortCol_0')];

                if ($this->input->get('sSortDir_0') == 'asc') {
                    $sortDir = 'ASC';
                } else {
                    $sortDir = 'DESC';
                }
            }
        }

        $data = $this->distributioncentersmodel->getSiteInfo(
            $id,
            $this->input->get('iDisplayStart'),
            $this->input->get('iDisplayLength'),
            $sortColumn,
            $sortDir
        );


        $ajaxData = array();
        while (list($k, $v) = each($data['rows'])) {
            $tmp = array();

            $tmp['DT_RowClass'] = 'gradeA';
            $tmp[] = $v['locationName'];
            $tmp[] = $v['squareFootage'];
            $tmp[] = $v['vendorName'];
            $tmp[] = $v['serviceType'];
            $tmp[] = $v['containerType'];
            $tmp[] = $v['frequency'];
            $tmp[] = $v['cost'];

            $ajaxData[] = $tmp;
            unset($tmp);
        }

        echo json_encode(
            array(
                'aaData' => $ajaxData,
                'iTotalRecords' => $data['rows_count'],
                'iTotalDisplayRecords' => $data['rows_count']
            )
        );

        return;
    }


}

/* End of file Dcinfo.php */
/* Location: ./application/controllers/Dcinfo.php */
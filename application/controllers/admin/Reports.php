<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class Reports extends Auth {

	public function index() {
		$this->load->model('Companies');

		$company = $this->Companies->getById($this->assigns['_companyId']);
		$this->assigns['company'] = $company;
		
		if ($this->input->post('submit')) {
			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('subject', 'Subject', 'required|trim');
			$this->form_validation->set_rules('message', 'Message', 'required|trim');
			
			if ($this->form_validation->run() == true) {
				
				$this->load->library('email');
				$user = $this->session->userdata('USER');
				
				$this->email->from(ASTOR_EMAIL, ASTOR_NAME);
				$this->email->reply_to($user->email, $user->firstName . ' ' . $user->lastName);
				$this->email->to(ASTOR_EMAIL);
			
				$message = <<<EOD
Help request from  {$user->firstName}  {$user->lastName}\n
Subject: {$this->input->post('subject')}\n
Message:\n
\t{$this->input->post('message')}
EOD;
				
				$this->email->subject('Help request - ' . $this->input->post('subject'));
				$this->email->message($message);				
				$this->email->send();

				$this->session->set_flashdata('info', 'Your request has been sent.');
			
				redirect('admin/Help');
				return;
			}
			
			
		}
		
		$this->assigns['data'] = new Placeholder();
		
		$this->assigns['data']->typeOptions = array(
				1 => "Distribution Centers",
				2 => "Stores",
                3 => "Construction"
		);		
		
		$this->load->view('admin/reports/reports_index', $this->assigns);
	}
	
	public function MissingInvoices() {
	    $this->load->model('admin/reportsmodel');
	    
	    $reportMonth = date('m');
	    $reportYear = date('Y');
	    
	    if($this->input->post('export')!='') {
            if($this->input->post('reportMonth')!='') {
                $reportMonth = $this->input->post('reportMonth');
            }
            if($this->input->post('reportYear')!='') {
                $reportYear = $this->input->post('reportYear');
            }

            $ym = $reportYear.$reportMonth;
            $my = $reportMonth.'/'.$reportYear;

            $filter = null;

            if($this->input->post('export')=='false') {
                $filter = array();
                if ($this->input->post('bSortable_0')) {
                    switch ($this->input->post('iSortCol_0')) {
                        case 0:
                            $filter['sortColumn'] = 'location';
                            break;
                        case 1:
                            $filter['sortColumn'] = 'vendorNumber';
                            break;
                        case 2:
                            $filter['sortColumn'] = 'vendorName';
                            break;
                        case 3:
                            $filter['sortColumn'] = 'location';
                            break;
                    }
                    if ($this->input->post('sSortDir_0') == 'asc') {
                        $filter['sortDir'] = 'ASC';
                    } else {
                        $filter['sortDir'] = 'DESC';
                    }
                }
                $filter['start'] = $this->input->post('iDisplayStart');
                $filter['length'] = $this->input->post('iDisplayLength');
            }

            $data = $this->reportsmodel->getMissingInvoices($ym, $filter);

            if($this->input->post('export')=='true') {

                $endData = array();
                $endData[] = array(
                    'Store#',
                    'Vendor#',
                    'Vendor Name',
                    'Month/Year'
                );


                foreach($data['data'] as $row) {
                    $endData[] = array(
                        $row->location,
                        $row->vendorNumber,
                        $row->vendorName,
                        $my
                    );
                }

                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=missing_invoices_".$this->input->post('reportMonth').$this->input->post('reportYear').".csv");
                header("Content-Type: application/octet-stream");
                header("Content-Transfer-Encoding: binary");

                $fp = fopen('php://output', 'w');

                if (!empty($endData)) {
                    foreach ($endData as $temp) {
                        fputcsv($fp, $temp, ",", '"');
                    }
                }

                return;
            } elseif($this->input->post('export')=='false') {
                $ajaxData = array();
                $i = 0;
                foreach ($data['data'] as $row) {
                    $ajaxData[] = array(
                        'DT_RowClass' => 'gradeA' . ($i%2 == 0 ? ' odd' : ' even'),
                        $row->location,
                        $row->vendorNumber,
                        $row->vendorName,
                        $my
                    );
                    $i++;
                }

                echo json_encode(array(
                'aaData' => $ajaxData,
                'iTotalRecords' => $data['records'],
                'iTotalDisplayRecords' => $data['records']
                ));
            }
	    } else {
	    
            $this->assigns['data'] = new Placeholder();
            $this->assigns['data']->reportMonth = $reportMonth;
            $this->assigns['data']->reportYear = $reportYear;

            $this->load->view('admin/reports/reports_missinginvoices', $this->assigns);
	    }
	}

    public function getDiversion() {
        $this->load->model('admin/reportsmodel');

        require_once dirname(__FILE__).'/../../classes/PHPExcel.php';

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Astor")
            ->setLastModifiedBy("Astor")
            ->setTitle("Diversion")
            ->setSubject("Diversion")
            ->setDescription("Diversion")
            ->setKeywords("Diversion")
            ->setCategory("Diversion");

        $sheets = array('Stores', 'DCs', 'Campus');

        //$objPHPExcel->createSheet();
        for($s=0; $s<count($sheets); $s++) {
            $objPHPExcel->createSheet();

            $objPHPExcel->setActiveSheetIndex($s);
            $objPHPExcel->getActiveSheet()->setTitle($sheets[$s]);

            $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'ASTOR')
                ->setCellValue('B1', '')
                ->setCellValue('C1', 'MSW')
                ->setCellValue('D1', 'CARDBOARD')
                ->setCellValue('E1', 'Shrinkwrap')
                ->setCellValue('F1', '*SINGLE STREAM w/ OCC')
                ->setCellValue('G1', 'SS - OCC')
                ->setCellValue('H1', 'SS - Plastic')
                ->setCellValue('I1', 'SS - metals')
                ->setCellValue('J1', 'SS - glass')
                ->setCellValue('K1', 'SS - paper')
                ->setCellValue('L1', '*SINGLE STREAM - No OCC in mix')
                ->setCellValue('M1', 'SS - Plastic')
                ->setCellValue('N1', 'SS - metals')
                ->setCellValue('O1', 'SS - glass')
                ->setCellValue('P1', 'SS - paper')
                ->setCellValue('Q1', 'COMPOST')
                ->setCellValue('R1', 'TOTALS')
                ->setCellValue('S1', '')
                ->setCellValue('T1', '')
                ->setCellValue('U1', '')
                ->setCellValue('V1', '');

            $objPHPExcel->getActiveSheet()
                ->setCellValue('A2', 'Month')
                ->setCellValue('B2', '# of locations')
                ->setCellValue('C2', 'Weight in Pounds')
                ->setCellValue('D2', 'Weight in Pounds')
                ->setCellValue('E2', 'Weight in Pounds')
                ->setCellValue('F2', 'Weight in Pounds')
                ->setCellValue('G2', 0.6)
                ->setCellValue('H2', '5%')
                ->setCellValue('I2', '10%')
                ->setCellValue('J2', '10%')
                ->setCellValue('K2', '15%')
                ->setCellValue('L2', 'Weight in Pounds')
                ->setCellValue('M2', '20%')
                ->setCellValue('N2', '25%')
                ->setCellValue('O2', '25%')
                ->setCellValue('P2', '30%')
                ->setCellValue('Q2', 'Weight in Tons')
                ->setCellValue('R2', 'Total MSW in Pounds')
                ->setCellValue('S2', 'Total OCC Weight in Pounds')
                ->setCellValue('T2', 'Total Shrinkwrap Weight in Pounds')
                ->setCellValue('U2', 'Total Recycling Weight in Pounds (incl. SS)')
                ->setCellValue('V2', 'Diversion  %');

            $objPHPExcel->getActiveSheet()->getStyle('G2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $objPHPExcel->getActiveSheet()->getStyle('H2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $objPHPExcel->getActiveSheet()->getStyle('I2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $objPHPExcel->getActiveSheet()->getStyle('J2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $objPHPExcel->getActiveSheet()->getStyle('K2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $objPHPExcel->getActiveSheet()->getStyle('M2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $objPHPExcel->getActiveSheet()->getStyle('N2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $objPHPExcel->getActiveSheet()->getStyle('O2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $objPHPExcel->getActiveSheet()->getStyle('P2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


            $j = $j0 = 3;

            $ys = 2012;
            $ms = 8;
            $ye = date('Y');
            $me = date('m');

            $months = array();

            for($i=0; ; $i++) {
                $y = date('Y', mktime(0, 0, 0, $ms + $i, 1, $ys));
                $m = date('m', mktime(0, 0, 0, $ms + $i, 1, $ys));

                $rows = array();
                if($s==0) {
                    $rows = $this->reportsmodel->getDiversionStores($y, $m);
                } elseif($s==1) {
                    $rows = $this->reportsmodel->getDiversionDCs($y, $m);
                } elseif($s==2) {
                    $rows = $this->reportsmodel->getDiversionCampus($y, $m);
                }


                $cols = array('B'=>0, 'C'=>0, 'D'=>0, 'E'=>0, 'F'=>0, 'L'=>0);
                if($s==0 || $s==2) {
                    foreach($rows as $row) {
                        if($row->name=='ColumnB') {
                            $cols['B'] += $row->value;
                        } else {
                            if($row->name=='MSW') {
                                $cols['C'] += $row->value;
                            } elseif($row->name=='Cardboard') {
                                $cols['D'] += $row->value;
                            } elseif($row->name=='Single Stream') {
                                $cols['F'] += $row->value;
                            } elseif($row->name=='Commingle') {
                                $cols['L'] += $row->value;
                            } else {
                                $cols['C'] += $row->value;
                            }
                        }
                    }
                } elseif($s==1) {
                    foreach($rows as $row) {
                        if($row->name=='ColumnB') {
                            $cols['B'] += $row->value;
                        } else {
                            if($row->name=='ColumnC') {
                                $cols['C'] += $row->value;
                            } elseif($row->name=='Cardboard') {
                                $cols['D'] += $row->value;
                            } elseif($row->name=='Plastic' || $row->name=='Totes ') {
                                $cols['E'] += $row->value;
                            } else {
                                $cols['C'] += $row->value;
                            }
                        }
                    }
                }

                $objPHPExcel->getActiveSheet()->setCellValue('A'.$j, $m.'/'.$y);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$j, round($cols['B'], 0));
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$j, round($cols['C'], 0));
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$j, round($cols['D'], 0));
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$j, round($cols['E'], 0));
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$j, round($cols['F'], 0));
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$j, '=G$2*F'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$j, '=H$2*F'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$j, '=I$2*F'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('J'.$j, '=J$2*F'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('K'.$j, '=K$2*F'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('L'.$j, round($cols['L'], 0));
                $objPHPExcel->getActiveSheet()->setCellValue('M'.$j, '=M$2*L'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('N'.$j, '=N$2*L'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('O'.$j, '=O$2*L'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('P'.$j, '=P$2*L'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('Q'.$j, 0);
                $objPHPExcel->getActiveSheet()->setCellValue('R'.$j, '=C'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('S'.$j, '=D'.$j.'+G'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('T'.$j, '=+E'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('U'.$j, '=D'.$j.'+E'.$j.'+F'.$j.'+L'.$j.'+Q'.$j);
                $objPHPExcel->getActiveSheet()->setCellValue('V'.$j, '=(U'.$j.')/(R'.$j.'+U'.$j.')');
                for($ii=ord('C'); $ii<=ord('U'); $ii++) {
                    $objPHPExcel->getActiveSheet()->getStyle(chr($ii).$j)->getNumberFormat()->setFormatCode('#,##0');
                }
                $objPHPExcel->getActiveSheet()->getStyle('V'.$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

                if(!isset($months[$m.'/'.$y])) {
                    $months[$m.'/'.$y] = 0;
                }

                if($m==12 || ($m==$me && $y==$ye)) {
                    if(!isset($months[$y])) {
                        $months[$y] = $y;
                    }

                    $j++;
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$j, 'TOTAL '.$y);
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setBold(true);
                    for($ii = 67; $ii<=85; $ii++) {
                        $objPHPExcel->getActiveSheet()->setCellValue(chr($ii).$j, '=SUM('.chr($ii).$j0.':'.chr($ii).($j-1).')');
                        $objPHPExcel->getActiveSheet()->getStyle(chr($ii).$j)->getNumberFormat()->setFormatCode('#,##0');
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$j, '=(U'.$j.')/(R'.$j.'+U'.$j.')');
                    $objPHPExcel->getActiveSheet()->getStyle('V'.$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

                    $j++;
                    $j0 = $j + 1;
                }


                if($y>=$ye && $m>=$me) {
                    break;
                }

                $j++;
            }

            //Style
            for($ii=65; $ii<=86; $ii++) {
                for($jj=1; $jj<$j; $jj++) {
                    $objPHPExcel->getActiveSheet()->getStyle(chr($ii).$jj)->applyFromArray(
                        array(
                            'borders' => array(
                                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                            )
                        )
                    );
                }
            }

            $objPHPExcel->getActiveSheet()->mergeCells('R1:V1');
            $objPHPExcel->getActiveSheet()->getStyle('R1:V1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

            $objPHPExcel->getActiveSheet()->getStyle('A1:V2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1:V2')->getFont()->setSize(8);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);

            $objPHPExcel->getActiveSheet()->getStyle('C1:C2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00D9D9D9');
            $objPHPExcel->getActiveSheet()->getStyle('D1:D2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00FFFF99');
            $objPHPExcel->getActiveSheet()->getStyle('E1:E2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00CCFFFF');
            $objPHPExcel->getActiveSheet()->getStyle('F1:K2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00F2DCDB');
            $objPHPExcel->getActiveSheet()->getStyle('L1:P2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00E4DFEC');
            $objPHPExcel->getActiveSheet()->getStyle('Q1:Q2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00FCD5B4');
            $objPHPExcel->getActiveSheet()->getStyle('R1:V2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00C4D79B');

            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(45.75);
            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(56.25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);

            //$objPHPExcel->createSheet();
        }

        $objPHPExcel->setActiveSheetIndex($s);
        $objPHPExcel->getActiveSheet()->setTitle('TOTALS');

        $objPHPExcel->getActiveSheet()
            ->setCellValue('A2', 'Month')
            ->setCellValue('B2', 'Total MSW in Pounds')
            ->setCellValue('C2', 'Total OCC Weight in Pounds')
            ->setCellValue('D2', 'Total Shrinkwrap Weight in Pounds')
            ->setCellValue('E2', 'Total Recycling Weight in Pounds (incl. SS)')
            ->setCellValue('F2', 'Diversion  %');

        $objPHPExcel->getActiveSheet()
            ->setCellValue('B1', 'Month');

        $j = 3;
        foreach($months as $month=>$v) {

            $objPHPExcel->getActiveSheet()->setCellValue('B'.$j, '=Stores!R'.$j.'+DCs!R'.$j.'+Campus!R'.$j.'');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$j, '=Stores!S'.$j.'+DCs!S'.$j.'+Campus!S'.$j.'');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$j, '=Stores!T'.$j.'+DCs!T'.$j.'+Campus!T'.$j.'');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$j, '=Stores!U'.$j.'+DCs!U'.$j.'+Campus!U'.$j.'');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$j, '=E'.$j.'/(B'.$j.'+E'.$j.')');
            $objPHPExcel->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $objPHPExcel->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getNumberFormat()->setFormatCode('#,##0');

            if($v!=0) {
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$j, 'TOTAL '.$y);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setBold(true);
                $j++;
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$j, $month);
            }
            $j++;
        }

        //Style
        for($ii=ord('A'); $ii<=ord('F'); $ii++) {
            for($jj=1; $jj<$j; $jj++) {
                $objPHPExcel->getActiveSheet()->getStyle(chr($ii).$jj)->applyFromArray(
                    array(
                        'borders' => array(
                            'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                        )
                    )
                );
            }
        }

        $objPHPExcel->getActiveSheet()->mergeCells('B1:F1');

        $objPHPExcel->getActiveSheet()->getStyle('B1:F2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00C4D79B');
        $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->getFont()->setSize(8);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(56.25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Diversion.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        exit();
    }

    public function getExpenses() {
        $this->load->model('admin/reportsmodel');
        $this->load->model('admin/wasteinvoicesmodel');
        $this->load->model('admin/wasteinvoicefeesmodel');
        $this->load->model('admin/wasteinvoiceservicesmodel');
        $this->load->model('distributioncentersmodel');
        $this->load->model('materialsmodel');
        $this->load->model('States');
        $this->load->helper('dates');

        $year = 2012;
        if(isset($_GET['year'])) {
            $year = intval($_GET['year']);
        }

        $states = $this->States->getList();

        /** Include PHPExcel */
        require_once dirname(__FILE__).'/../../classes/PHPExcel.php';

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Astor")
            ->setLastModifiedBy("Astor")
            ->setTitle("Expenses ".$year)
            ->setSubject("Expenses ".$year)
            ->setDescription("Expenses ".$year)
            ->setKeywords("Expenses ".$year)
            ->setCategory("Expenses ".$year);

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()
            ->setCellValue('D1', 'RECURRING CHARGES')
            ->setCellValue('Q1', 'NON-RECURRING CHARGES');
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('Q1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('Q1')->getFont()->setSize(14);

        $objPHPExcel->getActiveSheet()
            ->setCellValue('A2', 'Month Processed')
            ->setCellValue('B2', 'State')
            ->setCellValue('C2', 'Store Count')
            ->setCellValue('D2', 'Trash Rate')
            ->setCellValue('E2', 'Cardboard Rate')
            ->setCellValue('F2', 'Commingle Rate (Bottles & Cans Only)')
            ->setCellValue('G2', 'Single Stream Rate')
            ->setCellValue('H2', 'Rent')
            ->setCellValue('I2', 'Compactor Pull/Ton')
            ->setCellValue('J2', 'C/B On-Call Total')
            ->setCellValue('K2', 'Fuel Surcharge/Enviro Fees')
            ->setCellValue('L2', 'Late fees/  Finance Charges')
            ->setCellValue('M2', 'GMC Mgmt Fee (Recurring Services)')
            ->setCellValue('N2', 'Adjustments')
            ->setCellValue('O2', 'Other (Organics, Glass, Pallets)')
            ->setCellValue('P2', 'TOTAL RECURRING')
            ->setCellValue('Q2', 'Extra Trash')
            ->setCellValue('R2', 'Extra C/B')
            ->setCellValue('S2', 'Extra Commingle')
            ->setCellValue('T2', 'Extra Single Stream')
            ->setCellValue('U2', 'Extra Other (Organics, Glass, Pallets)')
            ->setCellValue('V2', 'Bulk Pickups')
            ->setCellValue('W2', 'Temp Service')
            ->setCellValue('X2', 'GMC Fee (Non-recurring services)')
            ->setCellValue('Y2', 'TOTAL NON-RECURRING')
            ->setCellValue('Z2', 'GRAND TOTAL = RECURRING AND NON-RECURRING');

        $objPHPExcel->getActiveSheet()->getStyle('A2:Z2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(79);

        for($i = 68; $i<=90; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setWidth(13.14);
        }

        $waste_invoices = $this->wasteinvoicesmodel->getByDatesForExpenses(1, ($year-1).'-09-01', $year.'-08-31');

        $waste_invoice_services = array();
        $waste_invoice_fees = array();

        if(count($waste_invoices)>0) {
            $invoice_ids = array_keys($waste_invoices);
            $waste_invoice_services = $this->wasteinvoiceservicesmodel->getByInvoiceIdArray($invoice_ids);
            $waste_invoice_fees = $this->wasteinvoicefeesmodel->getByInvoiceIdArray($invoice_ids);
        }

        $data = array();
        foreach ($waste_invoices as $invoice_id => $invoice) {
            if(!isset($data[$invoice['monthProcessed']])) {
                $data[$invoice['monthProcessed']] = array();
            }
            if(!isset($data[$invoice['monthProcessed']][$invoice['stateId']])) {
                $data[$invoice['monthProcessed']][$invoice['stateId']] = array(
                    'A2' => '',//Month Processed
                    'B2' => '',//State
                    'C2' => $this->wasteinvoicesmodel->getStoreCount($invoice['monthProcessed'], $invoice['stateId']),//Store Count
                    'D2' => 0,//Trash Rate
                    'E2' => 0,//Cardboard Rate
                    'F2' => 0,//Commingle Rate (Bottles & Cans Only)
                    'G2' => 0,//Single Stream Rate
                    'H2' => 0,//Rent  ???
                    'I2' => 0,//Compactor Pull/Ton~
                    'J2' => 0,//C/B On-Call Total
                    'K2' => 0,//Fuel Surcharge/Enviro Fees
                    'L2' => 0,//Late fees/ Finance Charges
                    'M2' => 0,//GMC Mgmt Fee (Recurring Services)  ???
                    'N2' => 0,//Adjustments
                    'O2' => 0,//Other (Organics, Glass, Pallets)
                    'P2' => 0,//TOTAL RECURRING
                    'Q2' => 0,//Extra Trash
                    'R2' => 0,//Extra C/B
                    'S2' => 0,//Extra Commingle
                    'T2' => 0,//Extra Single Stream
                    'U2' => 0,//Extra Other (Organics, Glass, Pallets) ???
                    'V2' => 0,//Bulk Pickups
                    'W2' => 0,//Temp Service
                    'X2' => 0,//GMC Fee (Non-recurring services)
                    'Y2' => 0,//TOTAL NON-RECURRING
                    'Z2' => 0,//GRAND TOTAL = RECURRING AND NON-RECURRING
                    'AA2' => 0,//DEBUG
                    'AB2' => 0//DEBUG
                );
            }

            $is_temporary_exists = false;

            if (isset($waste_invoice_services[$invoice_id])) {
                foreach ($waste_invoice_services[$invoice_id] as $wis) {

                    $serviceTypeId = $wis['serviceTypeId'];
                    $rate = $wis['rate'] ;
                    $data[$invoice['monthProcessed']][$invoice['stateId']]['AA2'] += $rate;

                    if ($serviceTypeId == 2) {
                        $is_temporary_exists = true;
                    }

                    if ($wis['category'] == 0) { # Waste
                        $ast202 = array(45, 47, 49, 50, 52, 71, 72, 74, 77, 89, 90, 95);
						
			            if($wis['containerId']==66) {// waste and container = Bulk Items
                            $data[$invoice['monthProcessed']][$invoice['stateId']]['V2'] += $rate;
                        } elseif(in_array($wis['containerId'], $ast202)) {
                            $data[$invoice['monthProcessed']][$invoice['stateId']]['I2'] += $rate;
                        } elseif ($this->check_service_type($serviceTypeId, 'Extra')) {
                            $data[$invoice['monthProcessed']][$invoice['stateId']]['Q2'] += $rate;
                        } else {
                             $data[$invoice['monthProcessed']][$invoice['stateId']]['D2'] += $rate;                            
			            }
		            } elseif ($wis['category'] == 1) { # Recycling
                        if($wis['material_name'] == 'Single Stream') {
                            if($serviceTypeId == 1) {
                                $data[$invoice['monthProcessed']][$invoice['stateId']]['G2'] += $rate;
                            } else {
                                $data[$invoice['monthProcessed']][$invoice['stateId']]['T2'] += $rate;
                            }
                        } elseif ($wis['material_name'] == 'Cardboard') {
                            if ($this->check_schedule($wis['schedule'], 'On Call')) {
                                $data[$invoice['monthProcessed']][$invoice['stateId']]['J2'] += $rate;
                            } else {
                                if ($this->check_service_type($serviceTypeId, 'Temporary')) {
                                    	$data[$invoice['monthProcessed']][$invoice['stateId']]['R2'] += $rate;
                                } else {
					                $data[$invoice['monthProcessed']][$invoice['stateId']]['E2'] += $rate;
				                }
                            }
                        } elseif ($wis['material_name'] == 'Commingle') {
                            if ($this->check_service_type($serviceTypeId, 'Extra')) {
                                $data[$invoice['monthProcessed']][$invoice['stateId']]['S2'] += $rate;
                            } else {
                                $data[$invoice['monthProcessed']][$invoice['stateId']]['F2'] += $rate;
                            }
                        } else {
                             $data[$invoice['monthProcessed']][$invoice['stateId']]['O2'] += $rate;
                        }
                    }
                }
            }
            $fees = array('Fuel Charge' => 0,
                'Environmental' => 0,
                'Rental' => 0,
                'Tax' => 0,
                'Frieght Charge' => 0,
                'notes' => array());

            if (isset($waste_invoice_fees[$invoice_id])) {
                foreach ($waste_invoice_fees[$invoice_id] as $wif) {
                    $data[$invoice['monthProcessed']][$invoice['stateId']]['AB2'] += $wif['feeAmount'];
                    switch ($wif['feeType']) {
                        case 10:
                            $data[$invoice['monthProcessed']][$invoice['stateId']]['W2'] += $wif['feeAmount'];
                            break;
                        case 2:
                            $data[$invoice['monthProcessed']][$invoice['stateId']]['K2'] += $wif['feeAmount'];
                            break;
                        case 8:
                            $data[$invoice['monthProcessed']][$invoice['stateId']]['K2'] += $wif['feeAmount'];
                            break;
                        //case 9:
                            //$lock_gate_sweep_fee += $wif['feeAmount'];
                            //break;
                        //case 12:
                        //    $franchise_fee += $wif['feeAmount'];
                        //    break;
                        //case 14:
                        //    $rebate += $wif['feeAmount'];
                        //    break;
                        case 15:
                          $data[$invoice['monthProcessed']][$invoice['stateId']]['L2'] += $wif['feeAmount'];
                          break;
                        //case 4:
                        //    $tax += $wif['feeAmount'];
                        //    break;
                        case 17:
                            if ($is_temporary_exists) {
                                $data[$invoice['monthProcessed']][$invoice['stateId']]['W2'] += $wif['feeAmount'];
                            }
                            else {
                                $data[$invoice['monthProcessed']][$invoice['stateId']]['I2'] += $wif['feeAmount'];
                            }
                            break;
                        case 16:
                            if ($is_temporary_exists) {
                                $data[$invoice['monthProcessed']][$invoice['stateId']]['W2'] += $wif['feeAmount'];
                            }
                            else {
                                $data[$invoice['monthProcessed']][$invoice['stateId']]['I2'] += $wif['feeAmount'];
                            }
                            break;
                        case 7:
 				$data[$invoice['monthProcessed']][$invoice['stateId']]['H2'] += $wif['feeAmount']; 
                            break;
                        default :
                            $data[$invoice['monthProcessed']][$invoice['stateId']]['N2'] += $wif['feeAmount'];
                    }

/*
                    if (!$wif['waived']) {
                        $fee_name = $wif['fee_type_name'];
                        $fee_amount = $wif['feeAmount'];

                        if($fee_name=='Rental') {
                            $data[$invoice['monthProcessed']][$invoice['stateId']]['H2'] += $fee_amount;
                        }

                        if($fee_name=='Finance charge') {
                            $data[$invoice['monthProcessed']][$invoice['stateId']]['L2'] += $fee_amount;
                        }

                        if (isset($fees[$fee_name])) {
                            $fees[$fee_name] += $fee_amount;
                        } else {
                            $fees[$fee_name] = $fee_amount;
                        }

                        if (in_array($fee_name, array('Credit', 'Stop Charge', 'Sweeping', 'Repair', 'Other', 'Tax'))) {
                            $fees['notes'] = $fee_name;
                        }
                    }
*/
                }
            }

        }

        $j = $j0 = 3;
        foreach($data as $monthProcessed => $v1) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$j, $monthProcessed);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setItalic(true);

            foreach($v1 as $stateId => $v2) {
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$j, (isset($states[$stateId]) ? $states[$stateId]->code : $stateId));
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$j, $v2['C2']);

                for($i = 68; $i<=79; $i++) {
                    $objPHPExcel->getActiveSheet()->setCellValue(chr($i).$j, $v2[chr($i).'2']);
                }
                $objPHPExcel->getActiveSheet()->setCellValue('P'.$j, '=SUM(D'.$j.':O'.$j.')');
                $objPHPExcel->getActiveSheet()->getStyle('P'.$j)->getFont()->setSize(12);

                for($i = 81; $i<=88; $i++) {
                    $objPHPExcel->getActiveSheet()->setCellValue(chr($i).$j, $v2[chr($i).'2']);
                }
                $objPHPExcel->getActiveSheet()->setCellValue('Y'.$j, '=SUM(Q'.$j.':X'.$j.')');
                $objPHPExcel->getActiveSheet()->setCellValue('Z'.$j, '=P'.$j.'+Y'.$j.'');

                $objPHPExcel->getActiveSheet()->getStyle('Y'.$j)->getFont()->setSize(12);
                $objPHPExcel->getActiveSheet()->getStyle('Z'.$j)->getFont()->setSize(12);

                $j++;
            }

            $objPHPExcel->getActiveSheet()->setCellValue('B'.$j, 'TOTAL');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$j, '=SUM(C'.$j0.':C'.($j-1).')');

            for($i = 68; $i<=90; $i++) {
                $objPHPExcel->getActiveSheet()->setCellValue(chr($i).$j, '=SUM('.chr($i).$j0.':'.chr($i).($j-1).')');
            }

            $objPHPExcel->getActiveSheet()->getStyle('B'.$j.':Z'.$j)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$j.':Z'.$j)->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setARGB('00D9D9D9');

            $objPHPExcel->getActiveSheet()->getStyle('D'.$j0.':Z'.$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $j += 2;
            $j0 = $j;
        }

        $styleBottomArray = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('A2:Z2')->applyFromArray($styleBottomArray);

        $styleRightArray = array(
            'borders' => array(
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => '00000000'),
                ),
            ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('C1:C'.$j)->applyFromArray($styleRightArray);
        $objPHPExcel->getActiveSheet()->getStyle('P1:P'.$j)->applyFromArray($styleRightArray);
        $objPHPExcel->getActiveSheet()->getStyle('Y1:Y'.$j)->applyFromArray($styleRightArray);

        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Expenses'.$year.'.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        exit();
    }

	public function getReports() {
		$endData = "";
		$this->load->model('admin/reportsmodel');
		$this->load->model('admin/wasteinvoicesmodel');
		$this->load->model('admin/wasteinvoicefeesmodel');
		$this->load->model('admin/wasteinvoiceservicesmodel');
		$this->load->model('distributioncentersmodel');
		$this->load->model('materialsmodel');
		$this->load->helper('dates');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules("from", "Start Date", "required|date|trim");
		$this->form_validation->set_rules("to", "End Date", "required|date|trim");
		$this->form_validation->set_rules("type", "", "trim");
		$company_id = 1;
		$type = $this->input->post('type');

		if ($this->form_validation->run() == true) {
			if($this->input->post("export_type") == 1) { # Quickbooks
				$endData = $this->reportsmodel->getQuickBooksReport(1, USToSQLDate($this->input->post('from')), USToSQLDate($this->input->post('to')), $this->input->post('type'));
			} else if ($this->input->post("export_type") == 2) {
			    # Hauler Invoice Submission Report
			    if ($this->input->post('type')== 1) { // Distribution Centers
                    $this->load->model('admin/DistributionCenterInvoicesModel');
                    $invoices = $this->DistributionCenterInvoicesModel->getHaulerInvoiceSubmissionReport(USToSQLDate($this->input->post('from')), USToSQLDate($this->input->post('to')));
                    $endData = $this->get_dcinvoice_report($invoices);
			    } elseif ($this->input->post('type')==2) { // Stores
                    $waste_invoices = $this->wasteinvoicesmodel->getByDates($company_id, USToSQLDate($this->input->post('from')), USToSQLDate($this->input->post('to')), $type);

                    if(count($waste_invoices)>0) {
                        $invoice_ids = array_keys($waste_invoices);
                        $waste_invoice_services = $this->wasteinvoiceservicesmodel->getByInvoiceIdArray($invoice_ids);
                        $waste_invoice_fees = $this->wasteinvoicefeesmodel->getByInvoiceIdArray($invoice_ids);
                    } else {
                        $waste_invoice_services = array();
                        $waste_invoice_fees = array();
                    }
                    //var_dump($waste_invoices);
                    $endData = $this->get_store_report($waste_invoices, $waste_invoice_services, $waste_invoice_fees);
			    } elseif ($this->input->post('type')==3) { // Construction
                    $this->load->model('admin/ConstructionInvoicesModel');
                    $invoices = $this->ConstructionInvoicesModel->getHaulerInvoiceSubmissionReport(USToSQLDate($this->input->post('from')), USToSQLDate($this->input->post('to')));
                    $endData = $this->get_constructioninvoice_report($invoices);
                }
				
			} else if ($this->input->post("export_type") == 3) { # Recyling invoice submission report
				if ($this->input->post('type') == 1) { #Recycling invoices for DC
					$result = $this->reportsmodel->getRecyclingInvoicesForDC(1, USToSQLDate($this->input->post('from')), USToSQLDate($this->input->post('to')));
					//8 columns
					if (!empty($result)) {
						foreach ($result as $dc) {
							$endData[] = array('DC Name', 'Type', 'Date', 'Num', 'Memo','Qty','Sales Price','Amount');
							$endData[] = array($dc->name, '', '', '', '','','','');
							$total = 0;
							
							foreach ($dc->orders as $order) {
								foreach ($order as $items) {
									//
									$purchaseOrders = $items->po;
									$fees = $items->fees;
									
									foreach ($purchaseOrders as $po) {
										$poTotal = $po->pricePerUnit * $po->quantity;
										$total += $poTotal;
										
										$endData[] = array('', 'Purchase Order', SQLToUSDate($po->PODate), $items->poNumber, $po->materialName,$po->quantity,$po->pricePerUnit, $poTotal);
									}
									
									foreach ($fees as $fee) {
										$endData[] = array('', 'Fee', SQLToUSDate($items->invoiceDate), '', $feeNames[$fee->feeType],'1',$fee->feeAmount,$fee->feeAmount);
										$total += $fee->feeAmount;
									}
	
								}
							}
							
							$endData[] = array('', '', '', '', '', '', '', number_format($total, 2));
							$endData[] = array('', '', '', '', '', '', '', '');
							
						}
					}

				} else { # recycling invoices for stores
					$endData = $this->reportsmodel->getRecyclingInvoices(1, USToSQLDate($this->input->post('from')), USToSQLDate($this->input->post('to')), $this->input->post('type'));
				}				
			}

			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename=export.csv");
			header("Content-Type: application/octet-stream");
			header("Content-Transfer-Encoding: binary");			
			
			$fp = fopen('php://output', 'w');
			
			if (!empty($endData)) {
				foreach ($endData as $temp) {
					fputcsv($fp, $temp, ",", '"');
				}
			} else {
				echo '"Sorry, no results found.";';
			}

			return;
		} else {
			
 
		}
		
		$this->assigns['data'] = new Placeholder();
		$this->assigns['data']->typeOptions = array(
		    1 => "Distribution Centers",
		    2 => "Stores"
		);		
		
		$this->load->view('admin/reports/reports_index', $this->assigns);
	}

    public function getServices(){

        $query = $this->db->query("
            select
              v.name AS \"vendor\",
              s.location AS \"Store\",
              s.addressline1 as \"address\",
              s.city as \"city\",
              s.postcode as \"zip\",
              st.name as \"state\",
              s.districtId as \"DistrictNumber\",
              case s.serviceType when 0 then 'None' when 1 then 'Waste' when 2 then 'Lamp' when 3 then 'Waste/Lamp' else 'Unknown' end  as \"ServiceType\",
              c.name as \"ContainersName\",
              vs.quantity,
              vs.rate,
              m.name,
              case vs.category when 0 then 'Waste' when 1 then 'Recycling' else 'Unknown' end as \"Category\",
              mp.name AS \"frequency\",
              (SELECT GROUP_CONCAT(d2n.name SEPARATOR \", \")  FROM `day2name` d2n  WHERE (d2n.day & vs.days) > 1) AS PickupDays,
              vs.startDate,
              vs.endDate
            FROM VendorServices vs
              LEFT JOIN Stores s
                ON s.id = vs.locationId
              LEFT JOIN Vendors v
                ON v.id = vs.vendorId
              LEFT JOIN Containers c
                ON c.id = vs.containerId
              LEFT JOIN Materials m
                ON m.id = vs.materialId
              LEFT OUTER JOIN monthlyPickups mp
                ON mp.id = vs.schedule
              Left Outer join States st on st.id=s.stateid
            where s.status='yes' and (vs.enddate = '0000-00-00' or vs.enddate > '".date("Y-m-d")."') and locationtype='STORE'
		");

        $endData = array();
        $endData[] = array(
            'vendor',
            'Store',
            'address',
            'city',
            'zip',
            'state',
            'DistrictNumber',
            'ServiceType',
            'ContainersName',
            'name',
            'Category',
            'quantity',
            'frequency',
            'PickupDays',
            'startDate',
            'endDate',
            'rate'
        );


        foreach($query->result() as $row) {
            $endData[] = array(
                $row->vendor,
                $row->Store,
                $row->address,
                $row->city,
                $row->zip,
                $row->state,
                $row->DistrictNumber,
                $row->ServiceType,
                $row->ContainersName,
                $row->name,
                $row->Category,
                $row->quantity,
                $row->frequency,
                $row->PickupDays,
                $row->startDate,
                $row->endDate,
                $row->rate
            );
        }

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=services_report_".date("Y-m-d").".csv");
        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: binary");


        $fp = fopen('php://output', 'w');


        if (!empty($endData)) {
            foreach ($endData as $temp) {
                fputcsv($fp, $temp, ",", '"');
            }
        }

        return;
    }
	
	private function get_dcinvoice_report($invoices) {
        $this->load->helper('dates');

        $data = array();
        $th = array(
            'Date',
            'Hauler Inv #', # wasteinvoices.invoiceNumber
            'Invoice Date', # wasteinvoices.invoiceDate
            'AU #', # distributioncenters.number
            'Account #', # 303220
            'Hauler', # wasteinvoices.vendorName
            'Hauler Vendor #', # vendors.number
            'Remit To #'); # wasteinvoices.total

        $feesTypes = $this->DistributionCenterInvoicesModel->getFeesTypes();

        foreach ($feesTypes as $feeTypeId => $feeType) {
            if (count($th) == 12) {
                array_push($th, 'Invoice Month');
            }
            array_push($th, $feeType);
        }
        array_push($th, 'Notes');
        array_push($th, 'Total');

        $data[] = $th;

        foreach ($invoices as $invoice) {
            $invoice_total = 0;
            $invoiceFees = $invoice->fees;

            $row = array(
                SQLToUSDate(date('Y-m-d')),
                $this->toExcelLeadingZeroes($invoice->haulerInvNumber), //Hauler Inv #
                SQLToUSDate($invoice->invoiceDate), //Invoice Date
                $invoice->distributioncenterNumber, //AU #
                $invoice->accountNumber, //Account #
                $invoice->vendorName, //Hauler
                $invoice->vendorNumber, //Hauler Vendor #
                $invoice->remitTo //Remit To #
            );

            foreach ($feesTypes as $feeTypeId => $feeType) {
                $fee = '';
                if (isset($invoiceFees[$feeTypeId])) {
                    $fee = $invoiceFees[$feeTypeId];
                    $invoice_total += $invoiceFees[$feeTypeId];
                }
                if (count($row) == 12) {
                    array_push($row, $invoice->monthlyServicePeriodM . '/' . $invoice->monthlyServicePeriodY);
                }
                array_push($row, $fee);
            }
            array_push($row, $invoice->internalNotes);
            array_push($row, $invoice_total);

            $data[] = $row;
        }

        return $data;
	}
	
	private function get_store_report($waste_invoices, $waste_invoice_services, $waste_invoice_fees) {
        $data[] = array(
            'Date sent',
            'Hauler Inv #', # wasteinvoices.invoiceNumber
            'Invoice Date', # wasteinvoices.invoiceDate
            'Store',
            'Hauler', # wasteinvoices.vendorName
            'Hauler Vendor #', # vendors.number
            'Remit to', # vendors.remitTo
            'Trash Rate Total', # wasteinvoiceservices.category=0
            'Card-board Rate Total', # wasteinvoices.category=1 and wasteinvoice.material=? and service != "On Call"
            'Single Stream Total', #
            'Commingle Rate Total', # wasteinvoices.category=1 and wasteinvoice.material=?
            'Rent Total', # wasteinvoicefees.feetype=?
            'Monthly Sub Total', # formula is "Trash Rate Total" + "Card-board Rate Total" + "Commingle Rate Total" + "Rent Total"
            'Monthly Service Period', # leave empty
            'Compactor Pull/Ton Total', #
            'C/B On-Call Total', # schedule=1, service call
            'Extra C/B Total', # scheduled Recycling amount when material is "Cardboard" and service is "On Call"
            'Extra Single Stream Total', #
            'Extra Commingle Total', # wasteinvoices.category=1 and wasteinvoice.material=?
            'Extra Trash Total', # wasteinvoices.category=1 and wasteinvoice.material=?
            'Temp Service Date(s)', # wasteinvoiceServices.startddate and enddate
            'Temp Service Charge Total', # wasteinvoicesservices.rate x qty
            'Fuel Sur-charge | Enviro Fees Total', # wasteinvoicefees.feetype=?
            'Finance charge',
            'Tax', # wasteinvoicefees.feetype=4
            'Lock/Gate/sweep Fee', #
            'Rebate', #
            'Franchise Fee', #
            'Notes', # wasteinvoicefees.feetype=?
            'Adjustment', # wasteinvoicefees.feeAmount=?
            'Total',
            'Unknown Services',
            'Unknown Fees'
        );

        // Build report
        foreach ($waste_invoices as $invoice_id => $invoice) {

            $columnH = $columnI = $columnJ = $columnK = $columnP = $columnAF = $columnT = $columnQ = $columnR = $columnS = $columnV = $columnL = $columnO = $columnW = $columnX = $columnY = $columnZ = $columnAA = $columnAB = $columnAD = $columnAG = 0.0;

            if (isset($waste_invoice_services[$invoice_id])) {
                foreach ($waste_invoice_services[$invoice_id] as $wis) {
                    $serviceTypeId = $wis['serviceTypeId'];
                    $rate = $wis['rate'];

                    if ($this->check_service_type($serviceTypeId, 'Normal')) {
                        
			//if ($wis['category'] == 0 && $this->is_over8Yards($wis['containerId']) && $this->check_schedule($wis['schedule'], 'On Call')) {
			if ($wis['category'] == 0 && $this->is_over8Yards($wis['containerId']) ) {
				$columnO += $rate;
			} elseif ($wis['category'] == 0) { # Waste
                            $columnH += $rate;
                        } elseif($wis['material_name'] == 'Cardboard' && !$this->is_over8Yards($wis['containerId']) && !$this->check_schedule($wis['schedule'], 'On Call')) {
                            $columnI += $rate;
                        } elseif($wis['material_name'] == 'Single Stream') {
                            $columnJ += $rate;
                        } elseif($wis['material_name'] == 'Commingle') {
                            $columnK += $rate;
                        } elseif($wis['material_name'] == 'Cardboard' && ($this->is_over8Yards($wis['containerId']) || $wis['containerId']==58) && $this->check_schedule($wis['schedule'], 'On Call')) {
                            $columnP += $rate;
                        } else {
                            $columnAF += $rate;
                        }
                    } elseif($this->check_service_type($serviceTypeId, 'Extra')) {
                        if ($wis['category'] == 0) { # Waste
                            $columnT += $rate;
                        } elseif($wis['material_name'] == 'Cardboard') {
                            $columnQ += $rate;
                        } elseif($wis['material_name'] == 'Single Stream') {
                            $columnR += $rate;
                        } elseif($wis['material_name'] == 'Commingle') {
                            $columnS += $rate;
                        } else {
                            $columnAF += $rate;
                        }
                    } else {
                        $columnV += $rate;
                    }
                }
            }

            if (isset($waste_invoice_fees[$invoice_id])) {
                foreach ($waste_invoice_fees[$invoice_id] as $wif) {
                    $amount = floatval($wif['feeAmount']);
                    switch ($wif['feeType']) {
                        case 7://Rental
                            $columnL += $amount;
                            break;
                        case 16://Haul
                        case 17://Disposal
                            $columnO += $amount;
                            break;
                        case 2://Fuel Charge
                        case 8://Enviromental
                            $columnW += $amount;
                            break;
                        case 4://Tax
                            $columnY += $amount;
                            break;
                        case 25://Casters Fee
                        case 20://Temp Rental
                        case 21://Temp Haul Fee
			case 22://Temp Delivery/Removal
			    $columnV += $amount;
			    break;
                        case 23://Gate Fee
                        case 13://City Container Fee
                        case 9://Lock
                        case 19://Lock Install
                        case 24://Roll Out Fee
                        case 26://Sweeping Fee
                            $columnZ += $amount;
                            break;
                        case 14://Rebate
                            $columnAA += $amount;
                            break;
                        case 12://Franchise Fee
                            $columnAB += $amount;
                            break;
                        case 5://Other
                        case 11://Credit
                            $columnAD += $amount;
                            break;
                        case 15://Rental
                            $columnX += $amount;
                            break;
                        default :
                            $columnAG += $amount;
                    }
                }
            }

            $columnM = $columnH + $columnI + $columnJ + $columnK + $columnL;
            $columnU = 0.0;
            $columnAE = $columnAD + $columnAB + $columnAA + $columnZ + $columnY + $columnX + $columnW + $columnV + $columnU + $columnT + $columnS + $columnR + $columnQ + $columnP + $columnO +$columnM;
            $invoiceNumber = $this->toExcelLeadingZeroes($invoice['invoiceNumber']);
            $data[] = array(
                date('Y-m-d'),
                $invoiceNumber, # wasteinvoices.invoiceNumber
                $invoice['invoiceDate'], # wasteinvoices.invoiceDate
                $invoice['store_location'], # store number
                $invoice['vendorName'], # wasteinvoices.vendorName
                $invoice['vendor_number'], # vendors.number
                $invoice['remitTo'], # vendors.remitTo
                $columnH,
                $columnI,
                $columnJ,
                $columnK,
                $columnL,
                $columnM,
                $invoice['invoiceMonth'].'/'.$invoice['invoiceYear'],
                $columnO,
                $columnP,
                $columnQ,
                $columnR,
                $columnS,
                $columnT,
                '',
                $columnV,
                $columnW,
                $columnX,
                $columnY,
                $columnZ,
                $columnAA,
                $columnAB,
                $invoice['internalNotes'],
                $columnAD,
                $columnAE,
                $columnAF,
                $columnAG);

            $this->wasteinvoicesmodel->updateToYes($invoice_id);
        }

        return $data;
	}

    private function get_constructioninvoice_report($invoices) {
        $this->load->helper('dates');

        $data = array();

        $th = array(
            'Date Sent',
            'Store #',
            'Vendor #',
            'Invoice #',
            'Total Amount',
            'Budget#/Lawson#');

        $feesTypes = $this->ConstructionInvoicesModel->getFeesTypes();

        $data[] = $th;

        foreach ($invoices as $invoice) {
            $invoice_total = 0;

            $invoiceFees = $invoice->fees;
            foreach ($feesTypes as $feeTypeId => $feeType) {
                if (isset($invoiceFees[$feeTypeId])) {
                    $invoice_total += $invoiceFees[$feeTypeId];
                }
            }

            $row = array(
                SQLToUSDate(date('Y-m-d')),
                $invoice->locationName,
                $invoice->vendorNumber,
                $invoice->haulerInvNumber,
                $invoice_total,
                $invoice->budgetNumber.'/'.$invoice->lawsonNumber
            );

            $data[] = $row;
        }

        return $data;
    }
	
	private function toExcelLeadingZeroes($number) {
		$excelNumber = '=';
		for($i=0; $i<strlen($number); $i++) {
			$ch = $number[$i];
			if($i!=0) {
				$excelNumber .= '&';
			}
			$excelNumber .= 'CHAR('.ord($ch).')';
		}
		
		return $excelNumber;
	}
	
	private function check_schedule($id, $name) {
	    $schedule_id = $this->get_schedule_id($name);

	    if ( $id == $schedule_id ) {
	        return true;
	    }
	    return false;
	}
	
	private function get_schedule_id($name) {
	    $services = array(
	        'On Call'=>1,
            'ONCALL' =>1, 
            'Monthly'=>2,	
            'Biweekly'=>3,	
            'Weekly'=>4,	
            '6x/week'=>5,	
            '5x/week'=>6,	
            '4x/week'=>7,	
            '3x/Week'=>8,	
            '2x/Week'=>9,
            'Daily'=>10,	
            'EOW'=>11,
            '2x/Month'=>12);
        if ( isset( $services[$name] ) ) {
            return $services[$name];
        }
        return '0';
	}
	
	private function check_service_type($id, $service_name) {
	    $service_id = $this->get_service_id( $service_name );
        if ( $id == $service_id ) {
            return true;
        }
        return false;
	}
	
	private function get_service_id($name) {
	    $services = array('Normal' => 1,
	                      'Temporary' => 2,
	                      'Extra' => 3);
	    if ( isset( $services[$name] ) ) {
	        return $services[$name];
	    }
	    return false;
	}

    private function is_over8Yards($containerId) {
        static $containers = null;
        if($containers == null) {
            $this->load->model('admin/Containers');
            $containers = $this->Containers->getOver8YardsList();
        }

        if(isset($containers[$containerId])) {
            return $containers[$containerId];
        }

        return false;
    }

}

/* End of file Reports.php */
/* Location: ./application/controllers/admin/Reports.php */

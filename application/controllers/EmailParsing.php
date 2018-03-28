<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EmailParsing extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
	echo("don't do this");
	exit(1);
        set_time_limit(0);

        $this->load->model('admin/SupportRequestModel');
        $this->load->library('Mailbox');

        $mailbox = $this->mailbox->getMailbox();

        $mailsIds = $mailbox->searchMailBox('ALL');
        if(!$mailsIds) {
            echo 'Mailbox is empty';
        } else {
            echo 'FOUND '.count($mailsIds)."<br />\n";

            $locationTypes = $this->SupportRequestModel->getLocationTypes();

            foreach($mailsIds as $mailId) {
                $mail = $mailbox->getMail($mailId);

                echo $mail->subject.' ~ ';

                $ep = array();
                $ep['subject'] = $mail->subject;
                $ep['body'] = ($mail->textPlain!=null ? $mail->textPlain : '~NULL~');
                $ep['status'] = 'NEW';
                $ep['errors'] = '';
                $ep['dt'] = date('Y-m-d H:i:s');
                if(strpos($mail->subject, 'Dispatch of Work Order')!==false) {
                    $cbre = trim(str_ireplace('Dispatch of Work Order', '', $mail->subject));
                    if(!$this->SupportRequestModel->IsCBREExists($cbre)) {
                        if($mail->textPlain!=null) {
                            //$mail->textPlain = iconv(mb_detect_encoding($mail->textPlain), 'ISO-8859-1//TRANSLIT', $mail->textPlain);
                            $data = array();
                            $data['firstName'] = '';
                            $data['lastName'] = '';
                            $data['phone'] = '';
                            $data['locationId'] = '';
                            $data['locationName'] = '';
                            $data['locationType'] = '';
                            $data['cbre'] = $cbre;

                            preg_match('/Requestor Name:(.+)\n/', $mail->textPlain, $matches);
                            if(count($matches)==2) {
                                $requestorName = trim($matches[1]);
                                list($data['firstName'], $data['lastName']) = explode(' ', $requestorName);
                            }
                            preg_match('/Requestor Phone:(.+)\n/', $mail->textPlain, $matches);
                            if(count($matches)==2) {
                                $data['phone'] = trim($matches[1]);
                            }
                            preg_match('/Building:.+?#(.+)\n/', $mail->textPlain, $matches);
                            if(count($matches)==2) {
                                $locationId = trim($matches[1]);
                                $data['locationId'] = $locationId;
                                $data['locationName'] = $locationId;
                                if(isset($locationTypes[$locationId])) {
                                    $data['locationType'] = $locationTypes[$locationId]['locationType'];
                                    $data['locationId'] = $locationTypes[$locationId]['id'];
                                    $data['locationName'] = $locationId;
                                } else {
                                    echo 'ERROR #003'."<br />\n";
                                    $ep['errors'] .= '#003';
                                }
                            }

                            $task = array();
                            $task['purposeId'] = '5';
                            $task['purposeType'] = '0';
                            $task['serviceDate'] = null;
                            $task['description'] = '';
                            preg_match('/Date Entered:(.+)\n/', $mail->textPlain, $matches);
                            if(count($matches)==2) {
                                $task['serviceDate'] = date('Y-m-d', strtotime(trim($matches[1])));
                            }
                            preg_match('/Problem Description:(.+?)Order Status:/s', $mail->textPlain, $matches);
                            if(count($matches)==2) {
                                $task['description'] = iconv('utf-8', 'windows-1251//IGNORE', trim($matches[1]));
                            }

                            $tasks = array();
                            $tasks[] = $task;
                            if(!empty($data['locationId'])) {
                                if (!$this->SupportRequestModel->add(1, 24, $data, $tasks)) {
                                    echo 'ERROR'."<br />\n";
                                    $ep['status'] = 'ERROR';
                                } else {
                                    $ep['status'] = 'OK';
                                }
                            } else {
                                echo 'ERROR #002'."<br />\n";
                                $ep['errors'] .= '#002';
                            }
                        } else {
                            echo 'ERROR #004'."<br />\n";
                            $ep['errors'] .= '#004';
                        }
                    } else {
                        echo 'CBRE '.$cbre.' is exists'."<br />\n";
                        $ep['status'] = 'CBRE is exists';
                    }
                } else {
                    echo 'SKIP'."<br />\n";
                    $ep['status'] = 'SKIP';
                }
                $this->SupportRequestModel->addEmailParsing($ep);

                $mailbox->deleteMail($mailId);
            }
        }
    }
}

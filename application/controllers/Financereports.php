<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Financereports extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->time               = strtotime(date('d-m-Y H:i:s'));
        $this->payment_mode       = $this->customlib->payment_mode();
        $this->search_type        = $this->customlib->get_searchtype();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model('Leads_management_model');
        $this->load->library('media_storage');
        $this->load->model('feeenquiry_model');
        $this->load->model('leadstudents_model');
        $this->load->model('feesreceipt_model');
        $this->load->model('late_entries_model');
        $this->load->model('feetype_model');
    }

    // public function finance()
    // {
    //     $this->session->set_userdata('top_menu', 'Financereports');
    //     $this->session->set_userdata('sub_menu', 'Financereports/finance');
    //     $this->session->set_userdata('subsub_menu', '');
    //     $this->load->view('layout/header');
    //     $this->load->view('financereports/finance');
    //     $this->load->view('layout/footer');
    // }
    
    
    public function finance()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // Prepare response data
        $data = [
            'page'      => 'Finance Reports',
            'menu'      => 'Financereports',
            'sub_menu'  => 'Financereports/finance'
        ];
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }

    public function reportduefees()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_statement', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/reportduefees');
        $data                = array();
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $date               = date('Y-m-d');
            $class_id           = $this->input->post('class_id');
            $section_id         = $this->input->post('section_id');
            $data['class_id']   = $class_id;
            $data['section_id'] = $section_id;
            $fees_dues          = $this->studentfeemaster_model->getStudentDueFeeTypesByDate($date, $class_id, $section_id);
            $students_list      = array();

            if (!empty($fees_dues)) {
                foreach ($fees_dues as $fee_due_key => $fee_due_value) {
                    $amount_paid = 0;

                    if (isJSON($fee_due_value->amount_detail)) {
                        $student_fees_array = json_decode($fee_due_value->amount_detail);
                        foreach ($student_fees_array as $fee_paid_key => $fee_paid_value) {
                            $amount_paid += ($fee_paid_value->amount + $fee_paid_value->amount_discount);
                        }
                    }
                    if ($amount_paid < $fee_due_value->fee_amount || ($amount_paid < $fee_due_value->amount && $fee_due_value->is_system)) {
                        $students_list[$fee_due_value->student_session_id]['admission_no']             = $fee_due_value->admission_no;
                        $students_list[$fee_due_value->student_session_id]['roll_no']                  = $fee_due_value->roll_no;
                        $students_list[$fee_due_value->student_session_id]['admission_date']           = $fee_due_value->admission_date;
                        $students_list[$fee_due_value->student_session_id]['firstname']                = $fee_due_value->firstname;
                        $students_list[$fee_due_value->student_session_id]['middlename']               = $fee_due_value->middlename;
                        $students_list[$fee_due_value->student_session_id]['lastname']                 = $fee_due_value->lastname;
                        $students_list[$fee_due_value->student_session_id]['father_name']              = $fee_due_value->father_name;
                        $students_list[$fee_due_value->student_session_id]['image']                    = $fee_due_value->image;
                        $students_list[$fee_due_value->student_session_id]['mobileno']                 = $fee_due_value->mobileno;
                        $students_list[$fee_due_value->student_session_id]['email']                    = $fee_due_value->email;
                        $students_list[$fee_due_value->student_session_id]['state']                    = $fee_due_value->state;
                        $students_list[$fee_due_value->student_session_id]['city']                     = $fee_due_value->city;
                        $students_list[$fee_due_value->student_session_id]['pincode']                  = $fee_due_value->pincode;
                        $students_list[$fee_due_value->student_session_id]['class']                    = $fee_due_value->class;
                        $students_list[$fee_due_value->student_session_id]['section']                  = $fee_due_value->section;
                        $students_list[$fee_due_value->student_session_id]['fee_groups_feetype_ids'][] = $fee_due_value->fee_groups_feetype_id;
                    }
                }
            }

            if (!empty($students_list)) {
                foreach ($students_list as $student_key => $student_value) {
                    $students_list[$student_key]['fees_list'] = $this->studentfeemaster_model->studentDepositByFeeGroupFeeTypeArray($student_key, $student_value['fee_groups_feetype_ids']);
                }
            }

            $data['student_due_fee'] = $students_list;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/reportduefees', $data);
        $this->load->view('layout/footer', $data);
    }

    public function printreportduefees()
    {
        $data                = array();
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        $date                = date('Y-m-d');
        $class_id            = $this->input->post('class_id');
        $section_id          = $this->input->post('section_id');
        $data['class_id']    = $class_id;
        $data['section_id']  = $section_id;
        $fees_dues           = $this->studentfeemaster_model->getStudentDueFeeTypesByDate($date, $class_id, $section_id);
        $students_list       = array();

        if (!empty($fees_dues)) {
            foreach ($fees_dues as $fee_due_key => $fee_due_value) {
                $amount_paid = 0;

                if (isJSON($fee_due_value->amount_detail)) {
                    $student_fees_array = json_decode($fee_due_value->amount_detail);
                    foreach ($student_fees_array as $fee_paid_key => $fee_paid_value) {
                        $amount_paid += ($fee_paid_value->amount + $fee_paid_value->amount_discount);
                    }
                }
                // if ($amount_paid < $fee_due_value->fee_amount) {
                if ($amount_paid < $fee_due_value->fee_amount || ($amount_paid < $fee_due_value->amount && $fee_due_value->is_system)) {
                    $students_list[$fee_due_value->student_session_id]['admission_no']             = $fee_due_value->admission_no;
                    $students_list[$fee_due_value->student_session_id]['roll_no']                  = $fee_due_value->roll_no;
                    $students_list[$fee_due_value->student_session_id]['admission_date']           = $fee_due_value->admission_date;
                    $students_list[$fee_due_value->student_session_id]['firstname']                = $fee_due_value->firstname;
                    $students_list[$fee_due_value->student_session_id]['middlename']               = $fee_due_value->middlename;
                    $students_list[$fee_due_value->student_session_id]['lastname']                 = $fee_due_value->lastname;
                    $students_list[$fee_due_value->student_session_id]['father_name']              = $fee_due_value->father_name;
                    $students_list[$fee_due_value->student_session_id]['image']                    = $fee_due_value->image;
                    $students_list[$fee_due_value->student_session_id]['mobileno']                 = $fee_due_value->mobileno;
                    $students_list[$fee_due_value->student_session_id]['email']                    = $fee_due_value->email;
                    $students_list[$fee_due_value->student_session_id]['state']                    = $fee_due_value->state;
                    $students_list[$fee_due_value->student_session_id]['city']                     = $fee_due_value->city;
                    $students_list[$fee_due_value->student_session_id]['pincode']                  = $fee_due_value->pincode;
                    $students_list[$fee_due_value->student_session_id]['class']                    = $fee_due_value->class;
                    $students_list[$fee_due_value->student_session_id]['section']                  = $fee_due_value->section;
                    $students_list[$fee_due_value->student_session_id]['fee_groups_feetype_ids'][] = $fee_due_value->fee_groups_feetype_id;
                }
            }
        }

        if (!empty($students_list)) {
            foreach ($students_list as $student_key => $student_value) {
                $students_list[$student_key]['fees_list'] = $this->studentfeemaster_model->studentDepositByFeeGroupFeeTypeArray($student_key, $student_value['fee_groups_feetype_ids']);
            }
        }
        $data['student_due_fee'] = $students_list;
        $page                    = $this->load->view('financereports/_printreportduefees', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    // public function reportdailycollection()
    // {
    //     if (!$this->rbac->hasPrivilege('daily_collection_report', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/reportdailycollection');
    //     $data          = array();
    //     $data['title'] = 'Daily Collection Report';
    //     $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == true) {

    //         $date_from          = $this->input->post('date_from');
    //         $date_to            = $this->input->post('date_to');
    //         $formated_date_from = strtotime($this->customlib->dateFormatToYYYYMMDD($date_from));
    //         $formated_date_to   = strtotime($this->customlib->dateFormatToYYYYMMDD($date_to));
    //         $st_fees            = $this->studentfeemaster_model->getCurrentSessionStudentFees();
    //         $fees_data          = array();

    //         for ($i = $formated_date_from; $i <= $formated_date_to; $i += 86400) {
    //             $fees_data[$i]['amt']                       = 0;
    //             $fees_data[$i]['count']                     = 0;
    //             $fees_data[$i]['student_fees_deposite_ids'] = array();
    //         }

    //         if (!empty($st_fees)) {
    //             foreach ($st_fees as $fee_key => $fee_value) {
    //                 if (isJSON($fee_value->amount_detail)) {
    //                     $fees_details = (json_decode($fee_value->amount_detail));
    //                     if (!empty($fees_details)) {
    //                         foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
    //                             $date = strtotime($fees_detail_value->date);
    //                             if ($date >= $formated_date_from && $date <= $formated_date_to) {
    //                                 if (array_key_exists($date, $fees_data)) {
    //                                     $fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
    //                                     $fees_data[$date]['count'] += 1;
    //                                     $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
    //                                 } else {
    //                                     $fees_data[$date]['amt']                         = $fees_detail_value->amount + $fees_detail_value->amount_fine;
    //                                     $fees_data[$date]['count']                       = 1;
    //                                     $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //         $data['fees_data'] = $fees_data;
    //     }

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/reportdailycollection', $data);
    //     $this->load->view('layout/footer', $data);
    // }
    
    
    public function getreportdailycollection()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // RBAC check
        // if (!$this->rbac->hasPrivilege('daily_collection_report', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status'  => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'title' => 'Daily Collection Report'
                ]
            ]));
    }
    
    
    public function reportdailycollection()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // RBAC check
        // if (!$this->rbac->hasPrivilege('daily_collection_report', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status'  => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        // Read JSON input
        $input = json_decode(file_get_contents('php://input'), true);
    
        // Validation
        if (empty($input['date_from']) || empty($input['date_to'])) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'date_from' => empty($input['date_from']) ? 'Date From is required' : null,
                        'date_to'   => empty($input['date_to']) ? 'Date To is required' : null
                    ]
                ]));
        }
    
        $date_from          = $input['date_from'];
        $date_to            = $input['date_to'];
        $formated_date_from = strtotime($this->dateFormatToYYYYMMDD($date_from));
        $formated_date_to   = strtotime($this->dateFormatToYYYYMMDD($date_to));
    
        $st_fees   = $this->studentfeemaster_model->getCurrentSessionStudentFees();
        $fees_data = [];
    
        // Initialize date range
        for ($i = $formated_date_from; $i <= $formated_date_to; $i += 86400) {
            $fees_data[$i] = [
                'amount' => 0,
                'count'  => 0,
                'student_fees_deposite_ids' => []
            ];
        }
    
        if (!empty($st_fees)) {
            foreach ($st_fees as $fee_value) {
                if (isJSON($fee_value->amount_detail)) {
                    $details = json_decode($fee_value->amount_detail);
                    foreach ($details as $d) {
                        $date = strtotime($d->date);
                        if ($date >= $formated_date_from && $date <= $formated_date_to) {
                            $fees_data[$date]['amount'] += ($d->amount + $d->amount_fine);
                            $fees_data[$date]['count']++;
                            $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                        }
                    }
                }
            }
        }
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'date_from' => $date_from,
                    'date_to'   => $date_to,
                    'fees_data' => $fees_data
                ]
            ]));
    }

    public function dateFormatToYYYYMMDD($date = null)
    {

        if ($date == "") {
            return null;
        }
        $format = $this->getSchoolDateFormat();

        $date_formated = date_parse_from_format($format, $date);
        $year          = $date_formated['year'];
        $month         = str_pad($date_formated['month'], 2, "0", STR_PAD_LEFT);
        $day           = str_pad($date_formated['day'], 2, "0", STR_PAD_LEFT);
        $hour          = $date_formated['hour'];
        $minute        = $date_formated['minute'];
        $second        = $date_formated['second'];
        $date          = $year . "-" . $month . "-" . $day;

        return $date;
    }
    
    public function getSchoolDateFormat($date_only = true, $time = false)
    {
        $setting_result     = $this->setting_model->get();
        return $date_format = $setting_result[0]['date_format'];
    }

    // public function reportdaycollection()
    // {
    //     if (!$this->rbac->hasPrivilege('day_collection', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/reportdaycollection');
    //     $data          = array();
    //     $data['title'] = 'Day Collection Report';
    //     $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
    //     $data['sch_setting'] = $this->sch_setting_detail;


    //     if ($this->form_validation->run() == true) {

    //         $date_from          = $this->input->post('date_from');
    //         $date_to            = $this->input->post('date_to');
    //         // $formated_date_from = strtotime($this->customlib->dateFormatToYYYYMMDD($date_from));
    //         // $formated_date_to   = strtotime($this->customlib->dateFormatToYYYYMMDD($date_to));

    //         $start_date         = date('Y-m-d', $this->customlib->datetostrtotime($date_from));

    //         $end_date    = date('Y-m-d', $this->customlib->datetostrtotime($date_to));

    //         $data['search_type'] = $this->input->post('search_type');

    //         $st_fees = $this->feesreceipt_model->get24feesbetweendate($start_date, $end_date);



    //         foreach ($st_fees as $key => $value) {
    //             $collection[$value->mode][] = $value;
    //         }

    //         // echo "<pre>";
    //         // print_r($collection);exit;


    //         // $st_fees            = $this->studentfeemaster_model->getFeeBetweenDate($start_date, $end_date);



    //         $data['fees_data'] = $collection;
    //     }



    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/reportdaycollection', $data);
    //     $this->load->view('layout/footer', $data);
    // }

    
    public function get_reportdaycollection()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        // if (!$this->rbac->hasPrivilege('day_collection', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access denied'
        //         ]));
        // }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'      => true,
                'title'       => 'Day Collection Report',
                'sch_setting' => $this->sch_setting_detail
            ]));
    }

    
    public function reportdaycollection()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        // if (!$this->rbac->hasPrivilege('day_collection', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access denied'
        //         ]));
        // }
    
        $input = json_decode(file_get_contents("php://input"), true);
    
        // Validation
        if (empty($input['date_from']) || empty($input['date_to'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'date_from' => empty($input['date_from']) ? 'Date From is required' : null,
                        'date_to'   => empty($input['date_to']) ? 'Date To is required' : null
                    ]
                ]));
        }
    
        // Date conversion
        $start_date = date(
            'Y-m-d',
            $this->customlib->datetostrtotime($input['date_from'])
        );
    
        $end_date = date(
            'Y-m-d',
            $this->customlib->datetostrtotime($input['date_to'])
        );
    
        // Fetch fees
        $st_fees = $this->feesreceipt_model
            ->get24feesbetweendate($start_date, $end_date);
    
        $collection = [];
    
        if (!empty($st_fees)) {
            foreach ($st_fees as $value) {
                $collection[$value->mode][] = $value;
            }
        }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'     => true,
                'from_date'  => $start_date,
                'to_date'    => $end_date,
                'fees_data'  => $collection,
                'sch_setting'=> $this->sch_setting_detail
            ]));
    }
    

    // public function feeCollectionStudentDeposit()
    // {
    //     $data                 = array();
    //     $date                 = $this->input->post('date');
    //     $fees_id              = $this->input->post('fees_id');
    //     $fees_id_array        = explode(',', $fees_id);
    //     $fees_list            = $this->studentfeemaster_model->getFeesDepositeByIdArray($fees_id_array);
    //     $data['student_list'] = $fees_list;
    //     $data['date']         = $date;
    //     $data['sch_setting']  = $this->sch_setting_detail;
    //     $page                 = $this->load->view('financereports/_feeCollectionStudentDeposit', $data, true);
    //     echo json_encode(array('status' => 1, 'page' => $page));
    // }
    
    
    public function feeCollectionStudentDeposit()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // Read raw JSON input
        $input = json_decode(file_get_contents('php://input'), true);
    
        $date    = $input['date']    ?? null;
        $fees_id = $input['fees_id'] ?? null;
    
        // Validation
        if (empty($date) || empty($fees_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'date'    => empty($date) ? 'Date is required' : null,
                        'fees_id' => empty($fees_id) ? 'Fees ID is required' : null
                    ]
                ]));
        }
    
        // Convert comma-separated IDs to array
        $fees_id_array = array_filter(array_map('trim', explode(',', $fees_id)));
    
        // Fetch fees deposit data
        $fees_list = $this->studentfeemaster_model
            ->getFeesDepositeByIdArray($fees_id_array);
    
        $data = [
            'student_list' => $fees_list,
            'date'         => $date,
            'sch_setting'  => $this->sch_setting_detail
        ];
    
        // Render partial view (HTML)
        $page = $this->load->view(
            'financereports/_feeCollectionStudentDeposit',
            $data,
            true
        );
    
        // Success response
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data,
                'page'   => $page
            ]));
    }

    public function reportbyname()
    {
        if (!$this->rbac->hasPrivilege('fees_statement', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/reportbyname');
        $data['title']       = 'student fees';
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;

        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('financereports/reportByName', $data);
            $this->load->view('layout/footer', $data);
        } else { {
                $data['student_due_fee'] = array();
                $class_id                = $this->input->post('class_id');
                $section_id              = $this->input->post('section_id');
                $student_id              = $this->input->post('student_id');
                $student_due_fee         = $this->studentfeemaster_model->getStudentFeesByClassSectionStudent($class_id, $section_id, $student_id);
                $data['student_due_fee'] = $student_due_fee;
                $data['class_id']        = $class_id;
                $data['section_id']      = $section_id;
                $data['student_id']      = $student_id;
                $category                = $this->category_model->get();
                $data['categorylist']    = $category;
                $this->load->view('layout/header', $data);
                $this->load->view('financereports/reportByName', $data);
                $this->load->view('layout/footer', $data);
            }
        }
    }

    // public function studentacademicreport()
    // {
    //     if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/studentacademicreport');
    //     $data['title']           = 'student fee';
    //     $data['payment_type']    = $this->customlib->getPaymenttype();
    //     $class                   = $this->class_model->get();
    //     $data['classlist']       = $class;

    //     $data['sch_setting']     = $this->sch_setting_detail;
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;

    //     $feesessiongroup     = $this->feesessiongroup_model->getFeesByGroup();


    //     $feeTypes  = $this->feetype_model->get();
    //     $data['feeTypes'] = $feeTypes;



    //     // $data['feesessiongrouplist'] = $feesessiongroup;
    //     // $data['fees_group']          = "";
    //     // if (isset($_POST['feegroup_id']) && $_POST['feegroup_id'] != '') {
    //     //     $data['fees_group'] = $_POST['feegroup_id'];
    //     // }

    //     // if (isset($_POST['select_all']) && $_POST['select_all'] != '') {
    //     //     $data['select_all'] = $_POST['select_all'];
    //     // }
    //     $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
    //     $jsonstaffrole       = $this->customlib->getStaffRole();
    //     $staffrole          = json_decode($jsonstaffrole);
    //     $staff_id           = $this->customlib->getStaffID();

    //     if ($this->form_validation->run() == false) {
    //         $data['student_due_fee'] = array();
    //         $data['resultarray']     = array();
    //         // $data['feetype']     = "";
    //         $data['feetype_arr'] = array();

    //     } else {
    //         $student_Array = array();
    //         $search_type   = $this->input->post('search_type');
    //         $class_id   = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');
    //         $feegroupIDs = $this->input->post('feegroup');




    //         // echo "<pre>";
    //         // print_r($feegroupIDs);exit;

    //         if (isset($class_id)) {
    //             $studentlist = $this->student_model->searchByClassSectionWithSession($class_id, $section_id);
    //         } else {
    //             $studentlist = $this->student_model->getStudents();
    //         }

    //         $student_Array = array();
    //         if (!empty($studentlist)) {
    //             foreach ($studentlist as $key => $eachstudent) {
    //                 $obj                = new stdClass();
    //                 $obj->name          = $this->customlib->getFullName($eachstudent['firstname'], $eachstudent['middlename'], $eachstudent['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
    //                 $obj->id            = $eachstudent['id'];
    //                 $obj->class         = $eachstudent['class'];
    //                 $obj->section       = $eachstudent['section'];
    //                 $obj->admission_no  = $eachstudent['admission_no'];
    //                 $obj->roll_no       = $eachstudent['roll_no'];
    //                 $obj->father_name   = $eachstudent['father_name'];
    //                 $student_session_id = $eachstudent['student_session_id'];
    //                 [$fee_enquiry_data]  = $this->feeenquiry_model->get(null,  $eachstudent['id']);
    //                 if (isset($fee_enquiry_data) && !empty($fee_enquiry_data)) {
    //                     $obj->status   = $fee_enquiry_data['status'];
    //                 } else {
    //                     $obj->status   = 'Active';
    //                 }
    //                 $student_total_fees = $this->studentfeemaster_model->getStudentFees($student_session_id);

    //                 // echo "<pre>";
    //                 // print_r($student_total_fees);exit;

    //                 if (!empty($student_total_fees)) {
    //                     $totalfee = 0;
    //                     $deposit  = 0;
    //                     $discount = 0;
    //                     $balance  = 0;
    //                     $fine     = 0;
    //                     $feetypePaidAmount = [];
    //                     $feetypeBalances = [];

    //                     foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {

    //                         if (!empty($student_total_fees_value->fees)) {

    //                             // echo "<pre>";
    //                             // print_r($student_total_fees_value->fees);exit;


    //                             foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {

    //                                 // echo "<pre>";
    //                                 // print_r($each_fee_value);exit;



    //                                 if(in_array($each_fee_values->feetype_id, $feegroupIDs)) { 




    //                                 $totalfee = $totalfee + $each_fee_value->amount;
    //                                 $feetype = $each_fee_value->type;
    //                                 $fee_amount = $each_fee_value->amount;

    //                                 if (!isset($feetypePaidAmount[$feetype])) {
    //                                     $feetypePaidAmount[$feetype] = 0;
    //                                 }

    //                                 // febalance 

    //                                 $feetypeBalances[$feetype] = $fee_amount;


    //                                 $amount_detail = json_decode($each_fee_value->amount_detail);

    //                                 if (is_object($amount_detail)) {
    //                                     foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
    //                                         $feetypePaidAmount[$feetype] += $amount_detail_value->amount;
    //                                         $deposit  = $deposit + $amount_detail_value->amount;
    //                                         $fine     = $fine + $amount_detail_value->amount_fine;
    //                                         $discount = $discount + $amount_detail_value->amount_discount;
    //                                         $feetypeBalances[$feetype] = $fee_amount - $amount_detail_value->amount;

    //                                     }
    //                                 }

    //                                 // foreach ($feetypePaidAmount as $type => $paidAmount) {
    //                                 //     $feetypeBalances[$type] = $fee_amount - $paidAmount;
    //                                 // }


    //                             }

    //                             }
    //                         }
    //                     }


    //                     $obj->totalfee     = $totalfee;
    //                     $obj->payment_mode = "N/A";
    //                     $obj->deposit      = $deposit;
    //                     $obj->fine         = $fine;
    //                     $obj->discount     = $discount;
    //                     $obj->balance      = $totalfee - ($deposit + $discount);
    //                     $obj->feetypePaidAmount = $feetypePaidAmount;
    //                     $obj->feetypeBalances = $feetypeBalances;
    //                 } else {

    //                     $obj->totalfee     = 0;
    //                     $obj->payment_mode = 0;
    //                     $obj->deposit      = 0;
    //                     $obj->fine         = 0;
    //                     $obj->balance      = 0;
    //                     $obj->discount     = 0;
    //                 }

    //                 if ($search_type == 'all') {
    //                     $student_Array[$obj->class ." ( ". $obj->section." ) "][] = $obj;   //
    //                 } elseif ($search_type == 'balance') {
    //                     if ($obj->balance > 0) {
    //                         $student_Array[$obj->class ." ( ". $obj->section." )"][] = $obj;  //
    //                     }
    //                 } elseif ($search_type == 'paid') {
    //                     if ($obj->balance <= 0) {
    //                         $student_Array[$obj->class ." (" . $obj->section." )"][] = $obj;  //
    //                     }
    //                 }
    //             }
    //         }


    //         $classlistdata[]         = array('result' => $student_Array);
    //         $data['student_due_fee'] = $student_Array;
    //         $data['resultarray']     = $classlistdata;
    //         $data['feeTypes'] = $feetypeBalances;

    //         // echo "<pre>";
    //         // print_r($student_Array);exit;
    //     }

    //     $data['staff_id'] =  $staff_id;
    //     $data['role_id'] = $staffrole->id;

    //         //         echo "<pre>";
    //         // print_r($data);exit;

    //         // $fee_typeList = $this->feetype_model->get();

    //         // $data['feetype_list'] = $fee_typeList;


    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/studentAcademicReport', $data);
    //     $this->load->view('layout/footer', $data);
    // }

    // public function studentacademicreport()
    // {
    //     if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/studentacademicreport');
    //     $data['title'] = 'student balance fee report';
    //     $data['payment_type'] = $this->customlib->getPaymenttype();
    //     $class = $this->class_model->get();
    //     $data['classlist'] = $class;

    //     $data['sch_setting'] = $this->sch_setting_detail;
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;

    //     // $feesessiongroup = $this->feesessiongroup_model->getFeesByGroup();
    //     $fee_typeList = $this->feetype_model->get();
    //     $data['fee_typeList'] = $fee_typeList;

    //     $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

    //     $this->form_validation->set_rules('feegroup[]', $this->lang->line('fee_group'), 'trim|required|xss_clean');

    //     $jsonstaffrole = $this->customlib->getStaffRole();
    //     $staffrole = json_decode($jsonstaffrole);
    //     $staff_id = $this->customlib->getStaffID();

    //     if ($this->form_validation->run() == false) {
    //         $data['student_due_fee'] = array();
    //         $data['resultarray'] = array();
    //         $data['feetype_arr'] = array();
    //     } else {
    //         $student_Array = array();
    //         $search_type = $this->input->post('search_type');
    //         $class_id = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');
    //         $feegroupIDs = $this->input->post('feegroup');


    //         if (isset($class_id)) {
    //             $studentlist = $this->student_model->searchByClassSectionWithSession($class_id, $section_id);
    //         } else {
    //             $studentlist = $this->student_model->getStudents();
    //         }

    //         $student_Array = array();
    //         $feesTypes = array();
    //         if (!empty($studentlist)) {
    //             foreach ($studentlist as $key => $eachstudent) {
    //                 $obj = new stdClass();
    //                 $obj->name = $this->customlib->getFullName($eachstudent['firstname'], $eachstudent['middlename'], $eachstudent['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
    //                 $obj->id = $eachstudent['id'];
    //                 $obj->class = $eachstudent['class'];
    //                 $obj->section = $eachstudent['section'];
    //                 $obj->admission_no = $eachstudent['admission_no'];
    //                 $obj->roll_no = $eachstudent['roll_no'];
    //                 $obj->father_name = $eachstudent['father_name'];
    //                 $obj->father_phone = $eachstudent['father_phone'];
    //                 $student_session_id = $eachstudent['student_session_id'];
    //                 $obj->student_session_id = $eachstudent['student_session_id'];
    //                 $fee_enquiry_data = $this->feeenquiry_model->get(null, $eachstudent['id']);
    //                 if (isset($fee_enquiry_data) && !empty($fee_enquiry_data)) {
    //                     $obj->status = $fee_enquiry_data['status'];
    //                 } else {
    //                     $obj->status = 'Active';
    //                 }
    //                 $student_total_fees = $this->studentfeemaster_model->getStudentFees($student_session_id);

    //                 if (!empty($student_total_fees)) {
    //                     $totalfee = 0;
    //                     $deposit = 0;
    //                     $discount = 0;
    //                     $balance = 0;
    //                     $fine = 0;
    //                     $feetypePaidAmount = [];
    //                     $feetypeBalances = [];

    //                     foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {
    //                         if (!empty($student_total_fees_value->fees)) {
    //                             foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {
    //                                 // Check if fee_groups_id is in feegroupIDs
    //                                 if (in_array($each_fee_value->feetype_id, $feegroupIDs)) {
    //                                     $totalfee += $each_fee_value->amount;
    //                                     $feetype = $each_fee_value->type;
    //                                     $fee_amount = $each_fee_value->amount;
    //                                     $feePaidAmount = 0;

    //                                     if (!in_array($feetype, $feesTypes)) {

    //                                         array_push($feesTypes, $feetype);
    //                                     }

    //                                     if (!isset($feetypePaidAmount[$feetype])) {
    //                                         $feetypePaidAmount[$feetype] = 0;
    //                                     }

    //                                     $feetypeBalances[$feetype] = $fee_amount;

    //                                     $amount_detail = json_decode($each_fee_value->amount_detail);

    //                                     if (is_object($amount_detail)) {
    //                                         foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
    //                                             $feetypePaidAmount[$feetype] += $amount_detail_value->amount;
    //                                             $deposit += $amount_detail_value->amount;
    //                                             $fine += $amount_detail_value->amount_fine;
    //                                             $discount += $amount_detail_value->amount_discount;
    //                                             $feePaidAmount  +=  $amount_detail_value->amount;
    //                                             // remove disount for balance amount
    //                                             $fee_amount -= $amount_detail_value->amount_discount;
    //                                         }


    //                                         $feetypeBalances[$feetype] = $fee_amount - $feePaidAmount;
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }

    //                     $obj->totalfee = $totalfee;
    //                     $obj->payment_mode = "N/A";
    //                     $obj->deposit = $deposit;
    //                     $obj->fine = $fine;
    //                     $obj->discount = $discount;
    //                     $obj->balance = $totalfee - ($deposit + $discount);
    //                     $obj->feetypePaidAmount = $feetypePaidAmount;
    //                     $obj->feetypeBalances = $feetypeBalances;
    //                 } else {
    //                     $obj->totalfee = 0;
    //                     $obj->payment_mode = 0;
    //                     $obj->deposit = 0;
    //                     $obj->fine = 0;
    //                     $obj->balance = 0;
    //                     $obj->discount = 0;
    //                 }

    //                 if ($search_type == 'all') {
    //                     $student_Array[$obj->class . " ( " . $obj->section . " ) "][] = $obj;
    //                 } elseif ($search_type == 'balance') {
    //                     if ($obj->balance > 0) {
    //                         $student_Array[$obj->class . " ( " . $obj->section . " )"][] = $obj;
    //                     }
    //                 } elseif ($search_type == 'paid') {
    //                     if ($obj->balance <= 0) {
    //                         $student_Array[$obj->class . " (" . $obj->section . " )"][] = $obj;
    //                     }
    //                 }
    //             }
    //         }

    //         // exit;


    //         $classlistdata[] = array('result' => $student_Array);
    //         $data['student_due_fee'] = $student_Array;
    //         $data['resultarray'] = $classlistdata;
    //         $data['feeTypes'] = $feesTypes;
    //         $data['feegroupIDs'] = $feegroupIDs;
    //     }

    //     $data['staff_id'] = $staff_id;
    //     $data['role_id'] = $staffrole->id;

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/studentAcademicReport', $data);
    //     $this->load->view('layout/footer', $data);
    // }
    
    
    public function get_studentacademicreport()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // RBAC
        // if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        $data = [
            'title'           => 'student balance fee report',
            'payment_type'    => $this->customlib->getPaymenttype(),
            'classlist'       => $this->class_model->get(),
            'fee_typeList'    => $this->feetype_model->get(),
            'sch_setting'     => $this->sch_setting_detail,
            'adm_auto_insert' => $this->sch_setting_detail->adm_auto_insert,
        ];
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }
    
    
    public function studentacademicreport()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // RBAC
        // if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        // Read JSON
        $input = json_decode(file_get_contents('php://input'), true);
    
        $search_type = $input['search_type'] ?? null;
        $class_id    = $input['class_id'] ?? null;
        $section_id  = $input['section_id'] ?? null;
        $feegroupIDs = $input['feegroup'] ?? [];
    
        // Validation
        if (empty($search_type) || empty($feegroupIDs)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'search_type' => empty($search_type) ? 'Search type is required' : null,
                        'feegroup'    => empty($feegroupIDs) ? 'Fee group is required' : null
                    ]
                ]));
        }
    
        // Fetch students
        if (!empty($class_id)) {
            $studentlist = $this->student_model
                ->searchByClassSectionWithSession($class_id, $section_id);
        } else {
            $studentlist = $this->student_model->getStudents();
        }
    
        $student_Array = [];
        $feesTypes     = [];
    
        if (!empty($studentlist)) {
            foreach ($studentlist as $eachstudent) {
    
                $obj = new stdClass();
                $obj->id            = $eachstudent['id'];
                $obj->name          = $this->customlib->getFullName(
                    $eachstudent['firstname'],
                    $eachstudent['middlename'],
                    $eachstudent['lastname'],
                    $this->sch_setting_detail->middlename,
                    $this->sch_setting_detail->lastname
                );
                $obj->class         = $eachstudent['class'];
                $obj->section       = $eachstudent['section'];
                $obj->admission_no  = $eachstudent['admission_no'];
                $obj->roll_no       = $eachstudent['roll_no'];
                $obj->father_name   = $eachstudent['father_name'];
                $obj->father_phone  = $eachstudent['father_phone'];
    
                $student_session_id = $eachstudent['student_session_id'];
                $student_fees       = $this->studentfeemaster_model
                    ->getStudentFees($student_session_id);
    
                $totalfee = $deposit = $discount = $fine = 0;
                $feetypePaidAmount = [];
                $feetypeBalances   = [];
    
                if (!empty($student_fees)) {
                    foreach ($student_fees as $feeRow) {
                        if (!empty($feeRow->fees)) {
                            foreach ($feeRow->fees as $fee) {
    
                                if (!in_array($fee->feetype_id, $feegroupIDs)) {
                                    continue;
                                }
    
                                $totalfee += $fee->amount;
                                $feetype   = $fee->type;
    
                                if (!in_array($feetype, $feesTypes)) {
                                    $feesTypes[] = $feetype;
                                }
    
                                $amount_detail = json_decode($fee->amount_detail);
                                $paid = 0;
    
                                if (is_object($amount_detail)) {
                                    foreach ($amount_detail as $detail) {
                                        $paid     += $detail->amount;
                                        $deposit  += $detail->amount;
                                        $fine     += $detail->amount_fine;
                                        $discount += $detail->amount_discount;
                                    }
                                }
    
                                $feetypePaidAmount[$feetype] = ($feetypePaidAmount[$feetype] ?? 0) + $paid;
                                $feetypeBalances[$feetype]   = $fee->amount - ($paid + $discount);
                            }
                        }
                    }
                }
    
                $obj->totalfee = $totalfee;
                $obj->deposit  = $deposit;
                $obj->discount = $discount;
                $obj->fine     = $fine;
                $obj->balance  = $totalfee - ($deposit + $discount);
    
                if (
                    $search_type === 'all' ||
                    ($search_type === 'balance' && $obj->balance > 0) ||
                    ($search_type === 'paid' && $obj->balance <= 0)
                ) {
                    $student_Array[$obj->class . " ( " . $obj->section . " )"][] = $obj;
                }
            }
        }
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'students'     => $student_Array,
                    'feeTypes'     => $feesTypes,
                    'feegroupIDs'  => $feegroupIDs
                ]
            ]));
    }


    public function assignleadsstudent()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/studentacademicreport');
        $data['title']           = 'student fee';
        $data['payment_type']    = $this->customlib->getPaymenttype();

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $jsonstaffrole       = $this->customlib->getStaffRole();
        $staffrole          = json_decode($jsonstaffrole);
        $staff_id           = $this->customlib->getStaffID();
        $feetype_ids = $this->leads_management_model->getFeeTypeByStaffId($staff_id);
        $feetype             = $this->feetype_model->get(null, $feetype_ids);
        $data['feetypeList'] = $feetype;


        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data['student_due_fee'] = array();
            $data['resultarray']     = array();
            $data['feetype']     = "";
            $data['feetype_arr'] = array();
        } else {
            $student_Array = array();
            $search_type   = $this->input->post('search_type');
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $feetype_id = $this->input->post('feetype_id');

            $data['feetype_id'] = $feetype_id;

            $lead_assign = $this->leads_management_model->getLeadAssignid($class_id,  $section_id, $feetype_id, $staff_id);
            $student_ids = [];
            if ($lead_assign && $lead_assign->id) {
                $student_ids = $this->leadstudents_model->getStudentsByLead($lead_assign->id);
            }

            $feesessiongroup     = $this->feesessiongroup_model->getFeesByGroup();
            $data['feesessiongroup'] = $feesessiongroup;
            $feegroups = [];
            $fee_groups_feetypes = [];

            foreach ($feesessiongroup as $f_s_g) {
                if ($f_s_g->class_id == $class_id) {
                    foreach ($f_s_g->feetypes as $feetype) {
                        if ($feetype->feetype_id == $feetype_id) {
                            $feegroups[] =  $f_s_g->id;
                            $fee_groups_feetypes[] = $feetype->id;
                        }
                    }
                }
            }

            $data['feegroups'] = $feegroups;
            $data['fee_groups_feetypes'] = $fee_groups_feetypes;

            $fee_group_comma = implode(', ', array_map(function ($val) {
                return sprintf("'%s'", $val);
            }, array_unique($feegroups)));
            $fee_groups_feetype_comma = implode(', ', array_map(function ($val) {
                return sprintf("'%s'", $val);
            }, array_unique($fee_groups_feetypes)));
            $student_due_fee = [];
            if (!empty($feegroups) && !empty($fee_groups_feetypes) && !empty($student_ids)) {
                $student_due_fee = $this->studentfee_model->getMultipleDueFees($fee_group_comma, $fee_groups_feetype_comma, $class_id, $section_id, $student_ids);
            }

            if (!empty($student_due_fee)) {
                foreach ($student_due_fee as $student_due_fee_key => $student_due_fee_value) {


                    $amt_due = ($student_due_fee_value['is_system']) ? $student_due_fee_value['fee_master_amount'] : $student_due_fee_value['amount'];

                    $a = json_decode($student_due_fee_value['amount_detail']);
                    if (!empty($a)) {
                        $amount          = 0;
                        $amount_discount = 0;
                        $amount_fine     = 0;

                        foreach ($a as $a_key => $a_value) {
                            $amount          = $amount + $a_value->amount;
                            $amount_discount = $amount_discount + $a_value->amount_discount;
                            $amount_fine     = $amount_fine + $a_value->amount_fine;
                        }
                        if ($amt_due <= ($amount + $amount_discount)) {
                            unset($student_due_fee[$student_due_fee_key]);
                        } else {

                            if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                                $stu = $this->add_new_student($student_due_fee_value);
                                [$fee_enquiry_data]  = $this->feeenquiry_model->get(null,  $stu['id'], $feetype_id);
                                if (isset($fee_enquiry_data) && !empty($fee_enquiry_data)) {
                                    $stu['status']   = $fee_enquiry_data['status'];
                                } else {
                                    $stu['status']   = 'Active';
                                }
                                $students[$student_due_fee_value['student_session_id']] = $stu;
                            }

                            $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                                'is_system' => $student_due_fee_value['is_system'],
                                'amount'          => $amt_due,
                                'amount_deposite' => $amount,
                                'amount_discount' => $amount_discount,
                                'amount_fine'     => $amount_fine,
                                'fee_group'       => $student_due_fee_value['fee_group'],
                                'fee_type'        => $student_due_fee_value['fee_type'],
                                'fee_code'        => $student_due_fee_value['fee_code'],
                            );
                        }
                    } else {

                        if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                            $stu = $this->add_new_student($student_due_fee_value);
                            [$fee_enquiry_data]  = $this->feeenquiry_model->get(null,  $stu['id'], $feetype_id);
                            if (isset($fee_enquiry_data) && !empty($fee_enquiry_data)) {
                                $stu['status']   = $fee_enquiry_data['status'];
                            } else {
                                $stu['status']   = 'Active';
                            }
                            $students[$student_due_fee_value['student_session_id']] = $stu;
                        }
                        $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                            'is_system' => $student_due_fee_value['is_system'],
                            'amount'          => $student_due_fee_value['amount'],
                            'amount_deposite' => 0,
                            'amount_discount' => 0,
                            'amount_fine'     => 0,
                            'fee_group'       => $student_due_fee_value['fee_group'],
                            'fee_type'        => $student_due_fee_value['fee_type'],
                            'fee_code'        => $student_due_fee_value['fee_code'],
                        );
                    }
                }
            }

            $data['student_remain_fees'] = $students;
        }

        $data['staff_id'] =  $staff_id;
        $data['role_id'] = $staffrole->id;

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/studentsfeeslead', $data);
        $this->load->view('layout/footer', $data);
    }


    public function leadreport()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Leads');
        $this->session->set_userdata('sub_menu', 'Leads/status');

        $data['title']           = 'student fee';
        $data['payment_type']    = $this->customlib->getPaymenttype();
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['student_due_fee'] = array();
            $data['resultarray']     = array();
            $data['feetype']     = "";
            $data['feetype_arr'] = array();
        } else {
            $student_Array = array();
            $search_type   = $this->input->post('search_type');
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            if (isset($class_id)) {
                $studentlist = $this->student_model->searchByClassSectionWithSession($class_id, $section_id);
            } else {
                $studentlist = $this->student_model->getStudents();
            }

            $student_Array = array();
            if (!empty($studentlist)) {
                foreach ($studentlist as $key => $eachstudent) {
                    $obj                = new stdClass();
                    $obj->name          = $this->customlib->getFullName($eachstudent['firstname'], $eachstudent['middlename'], $eachstudent['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
                    $obj->id            = $eachstudent['id'];
                    $obj->class         = $eachstudent['class'];
                    $obj->section       = $eachstudent['section'];
                    $obj->admission_no  = $eachstudent['admission_no'];
                    $obj->roll_no       = $eachstudent['roll_no'];
                    $obj->father_name   = $eachstudent['father_name'];
                    $student_session_id = $eachstudent['student_session_id'];
                    $student_total_fees = $this->studentfeemaster_model->getStudentFees($student_session_id);

                    if (!empty($student_total_fees)) {
                        $totalfee = 0;
                        $deposit  = 0;
                        $discount = 0;
                        $balance  = 0;
                        $fine     = 0;
                        foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {

                            if (!empty($student_total_fees_value->fees)) {
                                foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {
                                    $totalfee = $totalfee + $each_fee_value->amount;

                                    $amount_detail = json_decode($each_fee_value->amount_detail);

                                    if (is_object($amount_detail)) {
                                        foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                                            $deposit  = $deposit + $amount_detail_value->amount;
                                            $fine     = $fine + $amount_detail_value->amount_fine;
                                            $discount = $discount + $amount_detail_value->amount_discount;
                                        }
                                    }
                                }
                            }
                        }

                        $obj->totalfee     = $totalfee;
                        $obj->payment_mode = "N/A";
                        $obj->deposit      = $deposit;
                        $obj->fine         = $fine;
                        $obj->discount     = $discount;
                        $obj->balance      = $totalfee - ($deposit + $discount);
                    } else {

                        $obj->totalfee     = 0;
                        $obj->payment_mode = 0;
                        $obj->deposit      = 0;
                        $obj->fine         = 0;
                        $obj->balance      = 0;
                        $obj->discount     = 0;
                    }

                    if ($search_type == 'all') {
                        $student_Array[] = $obj;
                    } elseif ($search_type == 'balance') {
                        if ($obj->balance > 0) {
                            $student_Array[] = $obj;
                        }
                    } elseif ($search_type == 'paid') {
                        if ($obj->balance <= 0) {
                            $student_Array[] = $obj;
                        }
                    }
                }
            }

            $classlistdata[]         = array('result' => $student_Array);
            $data['student_due_fee'] = $student_Array;
            $data['resultarray']     = $classlistdata;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/leadReport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function collectfee_report()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', $this->lang->line('Fees Collection'));
        $this->session->set_userdata('sub_menu', 'financereports/collectfee_report');

        $data['title']           = 'student fee';
        $data['payment_type']    = $this->customlib->getPaymenttype();
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $feetype             = $this->feetype_model->get();
        $data['feetypeList'] = $feetype;
        $data['collect_by']  = $this->studentfeemaster_model->get_feesreceived_by();
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['student_ids'] = [];
        $data['assign_by'] = '';
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['student_due_fee'] = array();
            $data['resultarray']     = array();
            $data['feetype']     = "";
            $data['feetype_arr'] = array();
        } else {
            $student_Array = array();
            $search_type   = 'balance';
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $assign_by = $this->input->post('assign_by');
            $feetype_id = $this->input->post('feetype_id');

            $lead_assign = $this->leads_management_model->getLeadAssignid($class_id,  $section_id, $feetype_id, $assign_by);
            if ($lead_assign && $lead_assign->id) {
                $student_ids = $this->leadstudents_model->getStudentsByLead($lead_assign->id);
                $data['student_ids'] = $student_ids;
            }

            $feesessiongroup     = $this->feesessiongroup_model->getFeesByGroup();
            $data['feesessiongroup'] = $feesessiongroup;
            $feegroups = [];
            $fee_groups_feetypes = [];

            foreach ($feesessiongroup as $f_s_g) {
                if ($f_s_g->class_id == $class_id) {
                    foreach ($f_s_g->feetypes as $feetype) {
                        if ($feetype->feetype_id == $feetype_id) {
                            $feegroups[] =  $f_s_g->id;
                            $fee_groups_feetypes[] = $feetype->id;
                        }
                    }
                }
            }

            $data['feegroups'] = $feegroups;
            $data['fee_groups_feetypes'] = $fee_groups_feetypes;

            $fee_group_comma = implode(', ', array_map(function ($val) {
                return sprintf("'%s'", $val);
            }, array_unique($feegroups)));
            $fee_groups_feetype_comma = implode(', ', array_map(function ($val) {
                return sprintf("'%s'", $val);
            }, array_unique($fee_groups_feetypes)));
            $student_due_fee = [];
            if (!empty($feegroups) && !empty($fee_groups_feetypes)) {
                $student_due_fee = $this->studentfee_model->getMultipleDueFees($fee_group_comma, $fee_groups_feetype_comma, $class_id, $section_id);
            }

            $data['assign_by']       = $assign_by;

            $students = array();

            if (!empty($student_due_fee)) {
                foreach ($student_due_fee as $student_due_fee_key => $student_due_fee_value) {


                    $amt_due = ($student_due_fee_value['is_system']) ? $student_due_fee_value['fee_master_amount'] : $student_due_fee_value['amount'];

                    $a = json_decode($student_due_fee_value['amount_detail']);
                    if (!empty($a)) {
                        $amount          = 0;
                        $amount_discount = 0;
                        $amount_fine     = 0;

                        foreach ($a as $a_key => $a_value) {
                            $amount          = $amount + $a_value->amount;
                            $amount_discount = $amount_discount + $a_value->amount_discount;
                            $amount_fine     = $amount_fine + $a_value->amount_fine;
                        }
                        if ($amt_due <= ($amount + $amount_discount)) {
                            unset($student_due_fee[$student_due_fee_key]);
                        } else {

                            if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {

                                $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                            }

                            $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                                'is_system' => $student_due_fee_value['is_system'],
                                'amount'          => $amt_due,
                                'amount_deposite' => $amount,
                                'amount_discount' => $amount_discount,
                                'amount_fine'     => $amount_fine,
                                'fee_group'       => $student_due_fee_value['fee_group'],
                                'fee_type'        => $student_due_fee_value['fee_type'],
                                'fee_code'        => $student_due_fee_value['fee_code'],
                            );
                        }
                    } else {

                        if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                            $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                        }
                        $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                            'is_system' => $student_due_fee_value['is_system'],
                            'amount'          => $student_due_fee_value['amount'],
                            'amount_deposite' => 0,
                            'amount_discount' => 0,
                            'amount_fine'     => 0,
                            'fee_group'       => $student_due_fee_value['fee_group'],
                            'fee_type'        => $student_due_fee_value['fee_type'],
                            'fee_code'        => $student_due_fee_value['fee_code'],
                        );
                    }
                }
            }

            $data['student_remain_fees'] = $students;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/collectfee_reports', $data);
        $this->load->view('layout/footer', $data);
    }


    // public function collection_report()
    // {
    //     if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
    //         access_denied();
    //     }

    //     $data['collect_by']  = $this->studentfeemaster_model->get_feesreceived_by();
    //     $data['searchlist']  = $this->customlib->get_searchtype();
    //     $data['group_by']    = $this->customlib->get_groupby();
    //     $feetype             = $this->feetype_model->get();
    //     $data['feetypeList'] = $feetype;
    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/collection_report');
    //     $subtotal = false;

    //     if (isset($_POST['search_type']) && $_POST['search_type'] != '') {
    //         $dates               = $this->customlib->get_betweendate($_POST['search_type']);
    //         $data['search_type'] = $_POST['search_type'];
    //     } else {
    //         $dates               = $this->customlib->get_betweendate('this_year');
    //         $data['search_type'] = '';
    //     }

    //     if (isset($_POST['collect_by']) && $_POST['collect_by'] != '') {
    //         $data['received_by'] = $received_by = $_POST['collect_by'];
    //     } else {
    //         $data['received_by'] = $received_by = '';
    //     }

    //     if (isset($_POST['feetype_id']) && $_POST['feetype_id'] != '') {
    //         $feetype_id = $_POST['feetype_id'];
    //     } else {
    //         $feetype_id = "";
    //     }

    //     if (isset($_POST['group']) && $_POST['group'] != '') {
    //         $data['group_byid'] = $group = $_POST['group'];
    //         $subtotal           = true;
    //     } else {
    //         $data['group_byid'] = $group = '';
    //     }

    //     $collect_by = array();
    //     $collection = array();
    //     $start_date = date('Y-m-d', strtotime($dates['from_date']));
    //     $end_date   = date('Y-m-d', strtotime($dates['to_date']));

    //     $this->form_validation->set_rules('search_type', $this->lang->line('search_duration'), 'trim|required|xss_clean');

    //     $data['classlist']        = $this->class_model->get();
    //     $data['selected_section'] = '';

    //     if ($this->form_validation->run() == false) {
    //         $data['results'] = array();
    //     } else {

    //         $class_id   = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');

    //         $data['selected_section'] = $section_id;

    //         $data['results'] = $this->studentfeemaster_model->getFeeCollectionReport($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id);

    //         if ($group != '') {

    //             if ($group == 'class') {
    //                 $group_by = 'class_id';
    //             } elseif ($group == 'collection') {
    //                 $group_by = 'received_by';
    //             } elseif ($group == 'mode') {
    //                 $group_by = 'payment_mode';
    //             }

    //             foreach ($data['results'] as $key => $value) {
    //                 $collection[$value[$group_by]][] = $value;
    //             }
    //         } else {

    //             $s = 0;
    //             foreach ($data['results'] as $key => $value) {
    //                 $collection[$s++] = array($value);
    //             }
    //         }

    //         $data['results'] = $collection;
    //     }
    //     $data['subtotal']    = $subtotal;

    //     $data['sch_setting'] = $this->sch_setting_detail;
    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/collection_report', $data);
    //     $this->load->view('layout/footer', $data);
    // }
    
    
    public function get_collection_report()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        // if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        $data = [
            'status'       => true,
            'collect_by'   => $this->studentfeemaster_model->get_feesreceived_by(),
            'searchlist'   => $this->customlib->get_searchtype(),
            'group_by'     => $this->customlib->get_groupby(),
            'feetypeList'  => $this->feetype_model->get(),
            'classlist'    => $this->class_model->get(),
            'sch_setting'  => $this->sch_setting_detail
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    
    public function collection_report()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            return $this->output
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Access Denied'
                ]));
        }
    
        $input = json_decode(file_get_contents("php://input"), true);
    
        if (empty($input['search_type'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'search_type is required'
                ]));
        }
    
        // Dates
        $dates      = $this->customlib->get_betweendate($input['search_type']);
        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));
    
        $received_by = $input['collect_by'] ?? '';
        $feetype_id  = $input['feetype_id'] ?? '';
        $group       = $input['group'] ?? '';
        $class_id    = $input['class_id'] ?? '';
        $section_id  = $input['section_id'] ?? '';
    
        $subtotal = ($group !== '');
    
        // Fetch report
        $results = $this->studentfeemaster_model->getFeeCollectionReport(
            $start_date,
            $end_date,
            $feetype_id,
            $received_by,
            $group,
            $class_id,
            $section_id
        );
    
        // Grouping logic
        $collection = [];
    
        if ($group !== '') {
    
            if ($group === 'class') {
                $group_by = 'class_id';
            } elseif ($group === 'collection') {
                $group_by = 'received_by';
            } elseif ($group === 'mode') {
                $group_by = 'payment_mode';
            }
    
            foreach ($results as $row) {
                $collection[$row[$group_by]][] = $row;
            }
    
        } else {
            $i = 0;
            foreach ($results as $row) {
                $collection[$i++][] = $row;
            }
        }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'     => true,
                'search_type'=> $input['search_type'],
                'subtotal'   => $subtotal,
                'result'     => $collection
            ]));
    }

    // public function onlinefees_report()
    // {
    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/onlinefees_report');
    //     $data['searchlist'] = $this->customlib->get_searchtype();
    //     $data['group_by']   = $this->customlib->get_groupby();

    //     if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

    //         $dates               = $this->customlib->get_betweendate($_POST['search_type']);
    //         $data['search_type'] = $_POST['search_type'];
    //     } else {

    //         $dates               = $this->customlib->get_betweendate('this_year');
    //         $data['search_type'] = '';
    //     }

    //     $collection = array();
    //     $start_date = date('Y-m-d', strtotime($dates['from_date']));
    //     $end_date   = date('Y-m-d', strtotime($dates['to_date']));
    //     $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {
    //         $data['collectlist'] = array();
    //     } else {
    //         $data['collectlist'] = $this->studentfeemaster_model->getOnlineFeeCollectionReport($start_date, $end_date);
    //     }

    //     $data['sch_setting'] = $this->sch_setting_detail;
    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/onlineFeesReport', $data);
    //     $this->load->view('layout/footer', $data);
    // }
    
    
    public function get_onlinefees_report()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        $data = [
            'status'     => true,
            'searchlist' => $this->customlib->get_searchtype(),
            'group_by'   => $this->customlib->get_groupby(),
            'sch_setting'=> $this->sch_setting_detail
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    
    
    public function onlinefees_report()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        $input = json_decode(file_get_contents("php://input"), true);
    
        if (empty($input['search_type'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'search_type is required'
                ]));
        }
    
        // Get date range
        $dates      = $this->customlib->get_betweendate($input['search_type']);
        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));
    
        // Fetch online fee collection
        $collectlist = $this->studentfeemaster_model
            ->getOnlineFeeCollectionReport($start_date, $end_date);
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'      => true,
                'search_type' => $input['search_type'],
                'from_date'   => $start_date,
                'to_date'     => $end_date,
                'data'        => $collectlist
            ]));
    }
    

    public function duefeesremark()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_report_with_remark', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/duefeesremark');
        $data                = array();
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {
            $date               = date('Y-m-d');
            $class_id           = $this->input->post('class_id');
            $section_id         = $this->input->post('section_id');
            $data['class_id']   = $class_id;
            $data['section_id'] = $section_id;
            $date               = date('Y-m-d');
            $student_due_fee    = $this->studentfee_model->getDueStudentFeesByDateClassSection($class_id, $section_id, $date);

            $students = array();

            if (!empty($student_due_fee)) {
                foreach ($student_due_fee as $student_due_fee_key => $student_due_fee_value) {

                    $amt_due = ($student_due_fee_value['is_system']) ? $student_due_fee_value['previous_balance_amount'] : $student_due_fee_value['amount'];

                    $a = json_decode($student_due_fee_value['amount_detail']);
                    if (!empty($a)) {
                        $amount          = 0;
                        $amount_discount = 0;
                        $amount_fine     = 0;

                        foreach ($a as $a_key => $a_value) {
                            $amount          = $amount + $a_value->amount;
                            $amount_discount = $amount_discount + $a_value->amount_discount;
                            $amount_fine     = $amount_fine + $a_value->amount_fine;
                        }
                        if ($amt_due <= ($amount + $amount_discount)) {
                            unset($student_due_fee[$student_due_fee_key]);
                        } else {

                            if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                                $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                            }

                            $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                                'is_system' => $student_due_fee_value['is_system'],
                                'amount'          => $amt_due,
                                'amount_deposite' => $amount,
                                'amount_discount' => $amount_discount,
                                'amount_fine'     => $amount_fine,
                                'fee_group'       => $student_due_fee_value['fee_group'],
                                'fee_type'        => $student_due_fee_value['fee_type'],
                                'fee_code'        => $student_due_fee_value['fee_code'],

                            );
                        }
                    } else {
                        $amount          = 0;
                        $amount_discount = 0;

                        if ($amt_due <= ($amount + $amount_discount)) {
                            unset($student_due_fee[$student_due_fee_key]);
                        } else {
                            if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {

                                $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                            }
                            $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                                'is_system' => $student_due_fee_value['is_system'],
                                'amount'          => $amt_due,
                                'amount_deposite' => 0,
                                'amount_discount' => 0,
                                'amount_fine'     => 0,
                                'fee_group'       => $student_due_fee_value['fee_group'],
                                'fee_type'        => $student_due_fee_value['fee_type'],
                                'fee_code'        => $student_due_fee_value['fee_code'],
                            );
                        }
                    }
                }
            }

            $data['student_remain_fees'] = $students;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/duefeesremark', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add_new_student($student)
    {
        $new_student = array(
            'id'                 => $student['id'],
            'student_session_id' => $student['student_session_id'],
            'class'              => $student['class'],
            'section_id'         => $student['section_id'],
            'section'            => $student['section'],
            'admission_no'       => $student['admission_no'],
            'roll_no'            => $student['roll_no'],
            'admission_date'     => $student['admission_date'],
            'firstname'          => $student['firstname'],
            'middlename'         => $student['middlename'],
            'lastname'           => $student['lastname'],
            'image'              => $student['image'],
            'mobileno'           => $student['mobileno'],
            'email'              => $student['email'],
            'state'              => $student['state'],
            'city'               => $student['city'],
            'pincode'            => $student['pincode'],
            'religion'           => $student['religion'],
            'dob'                => $student['dob'],
            'current_address'    => $student['current_address'],
            'permanent_address'  => $student['permanent_address'],
            'category_id'        => $student['category_id'],
            'category'           => $student['category'],
            'adhar_no'           => $student['adhar_no'],
            'samagra_id'         => $student['samagra_id'],
            'bank_account_no'    => $student['bank_account_no'],
            'bank_name'          => $student['bank_name'],
            'ifsc_code'          => $student['ifsc_code'],
            'guardian_name'      => $student['guardian_name'],
            'guardian_relation'  => $student['guardian_relation'],
            'guardian_phone'     => $student['guardian_phone'],
            'guardian_address'   => $student['guardian_address'],
            'is_active'          => $student['is_active'],
            'father_name'        => $student['father_name'],
            'rte'                => $student['rte'],
            'gender'             => $student['gender'],

        );
        return $new_student;
    }

    public function printduefeesremark()
    {
        if (!$this->rbac->hasPrivilege('fees_statement', 'can_view')) {
            access_denied();
        }

        $date                = date('Y-m-d');
        $class_id            = $this->input->post('class_id');
        $section_id          = $this->input->post('section_id');
        $data['class_id']    = $class_id;
        $data['section_id']  = $section_id;
        $data['class']       = $this->class_model->get($class_id);
        $data['section']     = $this->section_model->get($section_id);
        $date                = date('Y-m-d');
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_due_fee     = $this->studentfee_model->getDueStudentFeesByDateClassSection($class_id, $section_id, $date);

        $students = array();

        if (!empty($student_due_fee)) {
            foreach ($student_due_fee as $student_due_fee_key => $student_due_fee_value) {



                $amt_due = ($student_due_fee_value['is_system']) ? $student_due_fee_value['previous_balance_amount'] : $student_due_fee_value['amount'];

                $a = json_decode($student_due_fee_value['amount_detail']);
                if (!empty($a)) {
                    $amount          = 0;
                    $amount_discount = 0;
                    $amount_fine     = 0;

                    foreach ($a as $a_key => $a_value) {
                        $amount          = $amount + $a_value->amount;
                        $amount_discount = $amount_discount + $a_value->amount_discount;
                        $amount_fine     = $amount_fine + $a_value->amount_fine;
                    }
                    if ($amt_due <= ($amount + $amount_discount)) {
                        unset($student_due_fee[$student_due_fee_key]);
                    } else {

                        if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                            $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                        }

                        $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                            'is_system' => $student_due_fee_value['is_system'],
                            'amount'          => $amt_due,
                            'amount_deposite' => $amount,
                            'amount_discount' => $amount_discount,
                            'amount_fine'     => $amount_fine,
                            'fee_group'       => $student_due_fee_value['fee_group'],
                            'fee_type'        => $student_due_fee_value['fee_type'],
                            'fee_code'        => $student_due_fee_value['fee_code'],
                        );
                    }
                } else {
                    $amount          = 0;
                    $amount_discount = 0;

                    if ($amt_due <= ($amount + $amount_discount)) {
                        unset($student_due_fee[$student_due_fee_key]);
                    } else {
                        if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                            $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                        }
                        $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                            'is_system' => $student_due_fee_value['is_system'],
                            'amount'          => $amt_due,
                            'amount_deposite' => 0,
                            'amount_discount' => 0,
                            'amount_fine'     => 0,
                            'fee_group'       => $student_due_fee_value['fee_group'],
                            'fee_type'        => $student_due_fee_value['fee_type'],
                            'fee_code'        => $student_due_fee_value['fee_code'],
                        );
                    }
                }
            }
        }

        $data['student_remain_fees'] = $students;
        $page = $this->load->view('financereports/_printduefeesremark', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function income()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/income');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/income', $data);
        $this->load->view('layout/footer', $data);
    }

    public function searchreportvalidation()
    {

        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $error = array();

            $error['search_type'] = form_error('search_type');

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {
            $search_type = $this->input->post('search_type');
            $date_from   = "";
            $date_to     = "";
            if ($search_type == 'period') {

                $date_from = $this->input->post('date_from');
                $date_to   = $this->input->post('date_to');
            }

            $params = array('search_type' => $search_type, 'date_from' => $date_from, 'date_to' => $date_to);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    public function getincomelistbydt()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        if ($search_type == "") {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        } else {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));

        $incomeList = $this->income_model->search("", $start_date, $end_date);

        $incomeList      = json_decode($incomeList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($incomeList->data)) {
            foreach ($incomeList->data as $key => $value) {
                $grand_total += $value->amount;

                $row   = array();
                $row[] = $value->name;
                $row[] = $value->invoice_no;
                $row[] = $value->income_category;
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[] = $currency_symbol . amountFormat($value->amount);
                $dt_data[] = $row;
            }
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('grand_total') . "</b>";
            $footer_row[] = $currency_symbol . amountFormat($grand_total);
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($incomeList->draw),
            "recordsTotal"    => intval($incomeList->recordsTotal),
            "recordsFiltered" => intval($incomeList->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function expense()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/expense');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';

        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        } else {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/expense', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getexpenselistbydt()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        if ($search_type == "") {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        } else {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $expenseList   = $this->expense_model->search('', $start_date, $end_date);

        $m               = json_decode($expenseList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $grand_total += $value->amount;

                $row       = array();
                $row[]     = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[]     = $value->exp_category;
                $row[]     = $value->name;
                $row[]     = $value->invoice_no;
                $row[]     = $currency_symbol . amountFormat($value->amount);
                $dt_data[] = $row;
            }
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('grand_total') . "</b>";
            $footer_row[] = "<b>" . $currency_symbol . amountFormat($grand_total) . "</b>";
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function payroll()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/payroll');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label']        = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $data['payment_mode'] = $this->payment_mode;

        $result              = $this->payroll_model->getbetweenpayrollReport($start_date, $end_date);
        $data['payrollList'] = $result;
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/payroll', $data);
        $this->load->view('layout/footer', $data);
    }

    public function incomegroup()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/incomegroup');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';
        $data['headlist']    = $this->incomehead_model->get();
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/incomegroup', $data);
        $this->load->view('layout/footer', $data);
    }

    public function dtincomegroupreport()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');
        $head        = $this->input->post('head');

        if (isset($search_type) && $search_type != '') {

            $dates               = $this->customlib->get_betweendate($search_type);
            $data['search_type'] = $_POST['search_type'];
        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }
        $data['head_id'] = $head_id = "";
        if (isset($_POST['head']) && $_POST['head'] != '') {
            $data['head_id'] = $head_id = $_POST['head'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label']   = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $incomeList      = $this->income_model->searchincomegroup($start_date, $end_date, $head_id);
        $m               = json_decode($incomeList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;

        if (!empty($m->data)) {
            $grd_total  = 0;
            $inchead_id = 0;
            $count      = 0;
            foreach ($m->data as $key => $value) {
                $income_head[$value->head_id][] = $value;
            }

            foreach ($m->data as $key => $value) {
                $inc_head_id  = $value->head_id;
                $total_amount = "<b>" . $value->amount . "</b>";
                $grd_total += $value->amount;
                $row = array();
                if ($inchead_id == $inc_head_id) {
                    $row[] = "";
                    $count++;
                } else {
                    $row[] = $value->income_category;
                    $count = 0;
                }
                $row[]      = $value->id;
                $row[]      = $value->name;
                $row[]      = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[]      = $value->invoice_no;
                $row[]      = amountFormat($value->amount);
                $dt_data[]  = $row;
                $inchead_id = $value->head_id;
                $sub_total  = 0;
                if ($count == (count($income_head[$value->head_id]) - 1)) {
                    foreach ($income_head[$value->head_id] as $inc_headkey => $inc_headvalue) {
                        $sub_total += $inc_headvalue->amount;
                    }
                    $amount_row   = array();
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "<b>" . $this->lang->line('sub_total') . "</b>";
                    $amount_row[] = "<b>" . $currency_symbol . amountFormat($sub_total) . "</b>";
                    $dt_data[]    = $amount_row;
                }
            }

            $grand_total  = "<b>" . $currency_symbol . amountFormat($grd_total) . "</b>";
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total') . "</b>";
            $footer_row[] = $grand_total;
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getgroupreportparam()
    {
        $search_type = $this->input->post('search_type');
        $head        = $this->input->post('head');
        $date_from = "";
        $date_to   = "";
        if ($search_type == 'period') {

            $date_from = $this->input->post('date_from');
            $date_to   = $this->input->post('date_to');
        }

        $params = array('search_type' => $search_type, 'head' => $head, 'date_from' => $date_from, 'date_to' => $date_to);
        $array  = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);
    }

    public function expensegroup()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/expensegroup');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';
        $data['headlist']    = $this->expensehead_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/expensegroup', $data);
        $this->load->view('layout/footer', $data);
    }

    public function dtexpensegroupreport()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');
        $head        = $this->input->post('head');

        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        $data['head_id'] = $head_id = "";
        if (isset($_POST['head']) && $_POST['head'] != '') {
            $data['head_id'] = $head_id = $_POST['head'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $result        = $this->expensehead_model->searchexpensegroup($start_date, $end_date, $head_id);

        $m               = json_decode($result);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $expense_head[$value->exp_head_id][] = $value;
            }

            $grd_total  = 0;
            $exphead_id = 0;
            $count      = 0;
            foreach ($m->data as $key => $value) {

                $exp_head_id  = $value->exp_head_id;
                $total_amount = "<b>" . $value->total_amount . "</b>";
                $grd_total += $value->total_amount;
                $row = array();

                if ($exphead_id == $exp_head_id) {
                    $row[] = "";
                    $count++;
                } else {
                    $row[] = $value->exp_category;
                    $count = 0;
                }

                $row[]      = $value->id;
                $row[]      = $value->name;
                $row[]      = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[]      = $value->invoice_no;
                $row[]      = amountFormat($value->amount);
                $dt_data[]  = $row;
                $exphead_id = $value->exp_head_id;
                $sub_total  = 0;
                if ($count == (count($expense_head[$value->exp_head_id]) - 1)) {
                    foreach ($expense_head[$value->exp_head_id] as $exp_headkey => $exp_headvalue) {
                        $sub_total += $exp_headvalue->amount;
                    }
                    $amount_row   = array();
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "<b>" . $this->lang->line('sub_total') . "</b>";
                    $amount_row[] = "<b>" . $currency_symbol . amountFormat($sub_total) . "</b>";
                    $dt_data[]    = $amount_row;
                }
            }

            $grand_total  = "<b>" . $currency_symbol . amountFormat($grd_total) . "</b>";
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total') . "</b>";
            $footer_row[] = $grand_total;
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function onlineadmission()
    {
        if (!$this->rbac->hasPrivilege('online_admission', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/onlineadmission');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $data['group_by']   = $this->customlib->get_groupby();

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        $collection = array();
        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $data['collectlist'] = array();
        } else {

            $data['collectlist'] = $this->onlinestudent_model->getOnlineAdmissionFeeCollectionReport($start_date, $end_date);
        }
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/onlineadmission', $data);
        $this->load->view('layout/footer', $data);
    }


    public function assignagentstudents()
    {
        $class_id = $this->input->post('form_class_id');
        $section_id = $this->input->post('form_section_id');
        $assign_by = $this->input->post('form_assign_id');
        $feetype_id = $this->input->post('form_feetype_id');
        $student_session_id = $this->input->post('student_session_id[]');

        $lead_assign_id = '';
        $lead_assign = $this->leads_management_model->getLeadAssignid($class_id,  $section_id, $feetype_id, $assign_by);
        if ($lead_assign) {
            $lead_assign_id = $lead_assign->id;
        }


        if ($lead_assign_id && !empty($student_session_id)) {
            $this->leadstudents_model->delete($lead_assign_id);
            foreach ($student_session_id as $student_id) {
                $insert_data[] = [
                    'student_session_id' => $student_id,
                    'lead_id' => $lead_assign_id
                ];
            }
            if (!empty($insert_data)) {
                $this->leadstudents_model->add($insert_data);
            }
        } else {
            $lead_assign_id = $this->leads_management_model->add([
                'class_id' => $class_id,
                'section_id' => $section_id,
                'feetype_id' => $feetype_id,
                'staff_id' => $assign_by,
            ]);
            $insert_data = [];
            if ($lead_assign_id && !empty($student_session_id)) {
                foreach ($student_session_id as $student_id) {
                    $insert_data[] = [
                        'student_session_id' => $student_id,
                        'lead_id' => $lead_assign_id
                    ];
                }
                if (!empty($insert_data)) {
                    $this->leadstudents_model->add($insert_data);
                }
            }
        }

        echo json_encode([
            'status' => 200,
            'message' => 'Records inserted succesfully',

        ]);
    }


    // student day academic report

    public function studentdayacademicreportnew()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/studentdayacademicreport');
        $data['title'] = 'Student Day Academic Report';
        $data['payment_type'] = $this->customlib->getPaymenttype();
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');


        $jsonstaffrole = $this->customlib->getStaffRole();
        $staffrole = json_decode($jsonstaffrole);
        $staff_id = $this->customlib->getStaffID();

        if ($this->form_validation->run() == false) {
            $data['student_due_fee'] = array();
            $data['resultarray'] = array();
            $data['feetype'] = "";
            $data['feetype_arr'] = array();
        } else {
            $student_Array = array();
            $search_type = $this->input->post('search_type');
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $date_from = $this->input->post('date_from');
            $date_to = $this->input->post('date_to');
            $acadamicType = $this->input->post('acadamic_type');

            $start_date = "";
            $end_date = "";

            if (!empty($date_from)) {
                $start_date = date('Y-m-d', $this->customlib->datetostrtotime($date_from));
            }

            if (!empty($date_to)) {
                $end_date = date('Y-m-d', $this->customlib->datetostrtotime($date_to));
            }

            // echo $acadamicType;exit;

            // if (isset($acadamicType) == 1) {
            //     $acadamicType = 1;
            //     $feetypes = $this->feetype_model->getAcadamicFeeTypes();


            // }



            if (isset($class_id)) {
                $studentlist = $this->student_model->payemntsearchByClassSectionWithSession($class_id, $section_id, null, $start_date, $end_date);
            } else {
                $studentlist = $this->student_model->paymentgetStudents($start_date, $end_date);
            }

            // echo "<pre>";
            // print_r($studentlist);exit;

            $student_Array = array();
            $all_feetypeBalances = array();

            if (!empty($studentlist)) {
                foreach ($studentlist as $key => $eachstudent) {
                    $obj = new stdClass();
                    $obj->name = $this->customlib->getFullName($eachstudent['firstname'], $eachstudent['middlename'], $eachstudent['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
                    $obj->id = $eachstudent['id'];
                    $obj->class = $eachstudent['class'];
                    $obj->section = $eachstudent['section'];
                    $obj->admission_no = $eachstudent['admission_no'];
                    $obj->roll_no = $eachstudent['roll_no'];
                    $obj->father_name = $eachstudent['father_name'];
                    $obj->receipt_no = $eachstudent['receipt_no'];
                    $obj->mode = $eachstudent['payment_mode'];
                    $obj->collected_by = $eachstudent['collected_by'];
                    $student_session_id = $eachstudent['student_session_id'];
                    [$fee_enquiry_data] = $this->feeenquiry_model->get(null, $eachstudent['id']);
                    if (isset($fee_enquiry_data) && !empty($fee_enquiry_data)) {
                        $obj->status = $fee_enquiry_data['status'];
                    } else {
                        $obj->status = 'Active';
                    }

                    $student_total_fees = $this->studentfeemaster_model->getStudentAcadamicFees($student_session_id, $start_date, $end_date, $acadamicType);
                    // echo "<pre>";
                    // print_r($student_total_fees);exit;

                    if (!empty($student_total_fees)) {
                        $totalfee = 0;
                        $deposit = 0;
                        $discount = 0;
                        $fine = 0;
                        $feetypePaidAmount = [];
                        $feetypeAmounts = [];

                        foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {

                            // echo "<pre>";
                            // print_r($student_total_fees_value);exit;
                            if (!empty($student_total_fees_value->fees)) {
                                foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {


                                    $amount_detail = json_decode($each_fee_value->amount_detail);

                                    // echo "<pre>";
                                    // print_r($amount_detail);exit;

                                    if (is_object($amount_detail)) {

                                        $totalfee += $each_fee_value->amount;
                                        $feetype = $each_fee_value->type;

                                        if (!isset($feetypePaidAmount[$feetype])) {
                                            $feetypePaidAmount[$feetype] = 0;
                                            $feetypeAmounts[$feetype] = 0;
                                        }

                                        $feetypeAmounts[$feetype] += $each_fee_value->amount;
                                        foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {

                                            if ($amount_detail_value->date >= $start_date && $amount_detail_value->date <= $end_date) {

                                                // if($feetype == "SPL Fee-I" || $feetype == "SPL Fee-II" || $feetype == "SPL Fee-III")

                                                $feetypePaidAmount[$feetype] += $amount_detail_value->amount;
                                                $deposit += $amount_detail_value->amount;
                                                $fine += $amount_detail_value->amount_fine;
                                                $discount += $amount_detail_value->amount_discount;
                                            } else {
                                                $feetypePaidAmount[$feetype] = 0;
                                                $deposit = 0;
                                                $fine = 0;
                                                $discount = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $feetypeBalances = [];
                        foreach ($feetypePaidAmount as $type => $paidAmount) {
                            $feetypeBalances[$type] = $feetypeAmounts[$type] - $paidAmount;
                        }

                        $obj->totalfee = $totalfee;
                        $obj->payment_mode = "N/A";
                        $obj->deposit = $deposit;
                        $obj->fine = $fine;
                        $obj->discount = $discount;
                        $obj->balance = $totalfee - ($deposit + $discount);
                        $obj->feetypePaidAmount = $feetypePaidAmount;
                        $obj->feetypeBalances = $feetypeBalances;

                        // Add the fee type balances to the main array
                        $all_feetypeBalances = array_merge_recursive($all_feetypeBalances, $feetypePaidAmount);
                    } else {
                        $obj->totalfee = 0;
                        $obj->payment_mode = "N/A";
                        $obj->deposit = 0;
                        $obj->fine = 0;
                        $obj->balance = 0;
                        $obj->discount = 0;
                        $obj->feetypePaidAmount = [];
                        $obj->feetypeBalances = [];
                    }

                    if ($search_type == 'all') {
                        $student_Array[] = $obj;
                    } elseif ($search_type == 'balance') {
                        if ($obj->balance > 0) {
                            $student_Array[] = $obj;
                        }
                    } elseif ($search_type == 'paid') {
                        if ($obj->balance <= 0) {
                            $student_Array[] = $obj;
                        }
                    }
                }

                $data['date_from'] = $date_from;
                $data['date_to'] = $date_to;
                $data['acadamic_type'] = $acadamicType;
            }


            // echo "<pre>";
            // print_r($student_Array);exit;


            $classlistdata[] = array('result' => $student_Array);
            $data['student_due_fee'] = $student_Array;
            $data['resultarray'] = $classlistdata;
            // $data['feeTypes'] = $feetypes;
            $data['feeTypes'] = $all_feetypeBalances;
        }

        //    echo "<pre>";
        // print_r($data);exit;

        $data['staff_id'] = $staff_id;
        $data['role_id'] = $staffrole->id;

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/studentdayacademicreport', $data);
        $this->load->view('layout/footer', $data);
    }

    // public function studentdayacademicreport()
    // {
    //     if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/studentdayacademicreport');
    //     $data['title'] = 'Student Day Academic Report';
    //     $data['payment_type'] = $this->customlib->getPaymenttype();
    //     $class = $this->class_model->get();
    //     $data['classlist'] = $class;
    //     $data['sch_setting'] = $this->sch_setting_detail;
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
    //     // $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

    //     $jsonstaffrole = $this->customlib->getStaffRole();
    //     $staffrole = json_decode($jsonstaffrole);
    //     $staff_id = $this->customlib->getStaffID();

    //     if ($this->form_validation->run() == false) {
    //         $data['student_due_fee'] = array();
    //         $data['resultarray'] = array();
    //         $data['feetype'] = "";
    //         $data['feetype_arr'] = array();
    //     } else {
    //         $student_Array = array();
    //         // $search_type = $this->input->post('search_type');
    //         $class_id = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');
    //         $date_from = $this->input->post('date_from');
    //         $date_to = $this->input->post('date_to');
    //         // $acadamicType = $this->input->post('acadamic_type');

    //         $start_date = "";
    //         $end_date = "";

    //         if (!empty($date_from)) {
    //             $start_date = date('Y-m-d', $this->customlib->datetostrtotime($date_from));
    //         }

    //         if (!empty($date_to)) {
    //             $end_date = date('Y-m-d', $this->customlib->datetostrtotime($date_to));
    //         }

    //         $studentFeeData  = $this->studentfeemaster_model->getFeeCollectionReportByDate($start_date, $end_date, null, null, null, $class_id, $section_id);

    //         $sortedData = array();

    //         // echo "<pre>";
    //         // print_r($studentFeeData);exit;


    //         foreach ($studentFeeData as $entry) {
    //             $id = $entry['id'];
    //             $type = $entry['type'];
    //             $inv_no = $entry['inv_no'];
    //             $code = $entry['code'];

    //             // if ($type == "SPL Fee-I" || $type == "SPL Fee-II" || $type == "SPL Fee-III") {
    //             //     $type = "SPL Fee";
    //             // } else  if ($type == "Transport Term-1" || $type == "Transport Term-2" || $type == "Transport Term-3") {
    //             //     $type = "Transport";

    //             // } else  if ($type == "Term-I" || $type == "Term-II" || $type == "Term-III" || $type == "Term-IV") {
    //             //     $type  = "Term";
    //             // } else  if ($type == "Uniform Fee 1" || $type == "Uniform Fee 2") {
    //             //     $type = "Unifrom Fee";
    //             // }

    //             $feetypePaidAmount = array();




    //             if (!isset($sortedData[$id . "/" . $inv_no][$code])) {

    //                 $feetypePaidAmount = array();


    //                 $feetypePaidAmount[$type] = $entry['amount'];
    //                 $entry['feetypePaidAmount'] = $feetypePaidAmount;
    //                 $sortedData[$id . "/" . $inv_no] = $entry;
    //             } else {
    //                 $feetypePaidAmount[$type] += $entry['amount'];
    //                 $entry['feetypePaidAmount'] = $feetypePaidAmount;

    //                 $sortedData[$id . "/" . $inv_no] = $entry;
    //             }
    //         }


    //         // echo "<pre>";
    //         // print_r($sortedData);exit;

    //         $feeTypes = array();

    //         foreach ($sortedData as $fees) {

    //             $code = $fees['code'];
    //             $type = $fees['type'];



    //             // if ($code == "SPL-I" || $code == "SPL-II" || $code == "SPL-III") {
    //             //     $code = "SPL Fee";
    //             // } else  if ($code == "T-IV" || $code == "T-III" || $code == "T-II" || $code == "T-I") {
    //             //     $code = "Term";

    //             // }  else  if ($code == "UF 1" || $code == "UF 2" || $code == "UF 2") {
    //             //     $code= "Unifrom Fee";
    //             // }

    //             // else  if ($code == "TT-1" || $code == "TT-2" || $code == "TT-3") {
    //             //     $code= "Transport";
    //             // }

    //             // else  if ($code == "SUGF") {
    //             //     $code= "Sports Uniform Girls Fee";
    //             // }
    //             // else  if ($code == "SUBF") {
    //             //     $code= "Sports Uniform Boys Fee";
    //             // }
    //             // else  if ($code == "BF") {
    //             //     $code= "Book Fee";
    //             // }



    //             if (!in_array($type, $feeTypes)) {
    //                 $feeTypes[] = $type;
    //             }
    //         }




    //         $classlistdata[] = array('result' => $sortedData);
    //         $data['student_due_fee'] = $sortedData;
    //         $data['resultarray'] = $classlistdata;
    //         $data['feeTypes'] = $feeTypes;
    //         $data['date_from'] = $date_from;
    //         $data['date_to'] = $date_to;
    //     }


    //     // echo "<pre>";
    //     // print_r($data);exit;
    //     $data['staff_id'] = $staff_id;
    //     $data['role_id'] = $staffrole->id;

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/studentdayacademicreport', $data);
    //     $this->load->view('layout/footer', $data);
    // }
    
    
    public function get_studentdayacademicreport()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        // if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access denied'
        //         ]));
        // }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'        => true,
                'title'         => 'Student Day Academic Report',
                'payment_type'  => $this->customlib->getPaymenttype(),
                'classlist'     => $this->class_model->get(),
                'sch_setting'   => $this->sch_setting_detail,
                'adm_auto_insert' => $this->sch_setting_detail->adm_auto_insert
            ]));
    }


    public function studentdayacademicreport()
    {
        
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        // if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access denied'
        //         ]));
        // }
    
        $input = json_decode(file_get_contents("php://input"), true);
    
        // Validation
        if (empty($input['date_from']) || empty($input['date_to'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'date_from' => empty($input['date_from']) ? 'Date From is required' : null,
                        'date_to'   => empty($input['date_to']) ? 'Date To is required' : null
                    ]
                ]));
        }
    
        $class_id   = $input['class_id']   ?? null;
        $section_id = $input['section_id'] ?? null;
    
        $start_date = date(
            'Y-m-d',
            $this->customlib->datetostrtotime($input['date_from'])
        );
    
        $end_date = date(
            'Y-m-d',
            $this->customlib->datetostrtotime($input['date_to'])
        );
    
        // Fetch fee data
        $studentFeeData = $this->studentfeemaster_model
            ->getFeeCollectionReportByDate(
                $start_date,
                $end_date,
                null,
                null,
                null,
                $class_id,
                $section_id
            );
    
        $sortedData = [];
        $feeTypes   = [];
    
        foreach ($studentFeeData as $entry) {
            $key  = $entry['id'] . '/' . $entry['inv_no'];
            $type = $entry['type'];
    
            if (!isset($sortedData[$key])) {
                $entry['feetypePaidAmount'] = [];
                $sortedData[$key] = $entry;
            }
    
            if (!isset($sortedData[$key]['feetypePaidAmount'][$type])) {
                $sortedData[$key]['feetypePaidAmount'][$type] = 0;
            }
    
            $sortedData[$key]['feetypePaidAmount'][$type] += $entry['amount'];
    
            if (!in_array($type, $feeTypes)) {
                $feeTypes[] = $type;
            }
        }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'      => true,
                'date_from'  => $input['date_from'],
                'date_to'    => $input['date_to'],
                'feeTypes'   => $feeTypes,
                'data'       => $sortedData,
                'sch_setting'=> $this->sch_setting_detail
            ]));
    }


    public function send_reminders()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/studentacademicreport');
        $data['title'] = 'student fee';
        $data['payment_type'] = $this->customlib->getPaymenttype();
        $class = $this->class_model->get();
        $data['classlist'] = $class;

        $data['sch_setting'] = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;

        $feesessiongroup = $this->feesessiongroup_model->getFeesByGroup();
        $fee_typeList = $this->feetype_model->get();
        $data['fee_typeList'] = $fee_typeList;

        $jsonstaffrole = $this->customlib->getStaffRole();
        $staffrole = json_decode($jsonstaffrole);
        $staff_id = $this->customlib->getStaffID();

        $current_date = date('Y-m-d'); // Get current date for due date comparison

        if ($this->form_validation->run() == true) {
            //echo "hi 1";exit;
            $data['student_due_fee'] = array();
            $data['resultarray'] = array();
            $data['feetype_arr'] = array();
        } else {
            //echo "hi 2";exit;
            $student_Array = array();
            $search_type = 'balance';
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            if (isset($class_id)) {
                $studentlist = $this->student_model->searchByClassSectionWithSession($class_id, $section_id);
            } else {
                $studentlist = $this->student_model->getStudents();
            }



            $student_Array = array();
            if (!empty($studentlist)) {
                foreach ($studentlist as $key => $eachstudent) {
                    $obj = new stdClass();
                    $obj->name = $this->customlib->getFullName($eachstudent['firstname'], $eachstudent['middlename'], $eachstudent['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
                    $obj->id = $eachstudent['id'];
                    $obj->class = $eachstudent['class'];
                    $obj->section = $eachstudent['section'];
                    $obj->admission_no = $eachstudent['admission_no'];
                    $obj->roll_no = $eachstudent['roll_no'];
                    $obj->father_name = $eachstudent['father_name'];
                    $obj->father_phone = $eachstudent['father_phone'];
                    $obj->student_session_id = $eachstudent['student_session_id'];

                    $student_session_id = $eachstudent['student_session_id'];
                    $fee_enquiry_data = $this->feeenquiry_model->get(null, $eachstudent['id']);
                    $obj->status = isset($fee_enquiry_data) && !empty($fee_enquiry_data) ? $fee_enquiry_data['status'] : 'Active';

                    $student_total_fees = $this->studentfeemaster_model->getStudentFees($student_session_id);

                    //echo '<pre>'; print_r($student_total_fees);exit;

                    if (!empty($student_total_fees)) {
                        $totalfee = 0;
                        $deposit = 0;
                        $discount = 0;
                        $balance = 0;
                        $fine = 0;
                        $feetypePaidAmount = [];
                        $feetypeBalances = [];

                        foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {
                            if (!empty($student_total_fees_value->fees)) {
                                foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {

                                    // Filter by due_date
                                    if (strtotime($each_fee_value->due_date) <= strtotime($current_date)) {
                                        // Check if fee_groups_id is in feegroupIDs
                                        //if (in_array($each_fee_value->feetype_id, $feegroupIDs)) {
                                        $totalfee += $each_fee_value->amount;
                                        $feetype = $each_fee_value->type;
                                        $fee_amount = $each_fee_value->amount;
                                        $feePaidAmount = 0;

                                        if (!isset($feetypePaidAmount[$feetype])) {
                                            $feetypePaidAmount[$feetype] = 0;
                                        }

                                        $feetypeBalances[$feetype] = $fee_amount;
                                        $amount_detail = json_decode($each_fee_value->amount_detail);

                                        if (is_object($amount_detail)) {
                                            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                                                $feetypePaidAmount[$feetype] += $amount_detail_value->amount;
                                                $deposit += $amount_detail_value->amount;
                                                $fine += $amount_detail_value->amount_fine;
                                                $discount += $amount_detail_value->amount_discount;
                                                $feePaidAmount += $amount_detail_value->amount;
                                                $fee_amount -= $amount_detail_value->amount_discount;
                                            }

                                            $feetypeBalances[$feetype] = $fee_amount - $feePaidAmount;
                                        }
                                        //}
                                    }
                                }
                            }
                        }

                        $obj->totalfee = $totalfee;
                        $obj->payment_mode = "N/A";
                        $obj->deposit = $deposit;
                        $obj->fine = $fine;
                        $obj->discount = $discount;
                        $obj->balance = $totalfee - ($deposit + $discount);
                        $obj->feetypePaidAmount = $feetypePaidAmount;
                        $obj->feetypeBalances = $feetypeBalances;
                    } else {
                        $obj->totalfee = 0;
                        $obj->payment_mode = 0;
                        $obj->deposit = 0;
                        $obj->fine = 0;
                        $obj->balance = 0;
                        $obj->discount = 0;
                    }

                    if ($search_type == 'balance') {
                        if ($obj->balance > 0) {
                            $student_Array[$obj->class . " ( " . $obj->section . " )"][] = $obj;
                        }
                    }
                }
            }

            $classlistdata[] = array('result' => $student_Array);
            $data['student_due_fee'] = $student_Array;
            $data['resultarray'] = $classlistdata;
            $data['feeTypes'] = $feetypeBalances;
            //$data['feegroupIDs'] = $feegroupIDs;
        }

        $data['staff_id'] = $staff_id;
        $data['role_id'] = $staffrole->id;

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/sendreminders', $data);
        $this->load->view('layout/footer', $data);
    }

    public function sendReminder()
    {
        $stu_session_ids = $this->input->post('students');
        //echo '<pre>'; print_r($stu_session_ids);exit;

        if (!empty($stu_session_ids)) {
            foreach ($stu_session_ids as $student) {
                $student_session_id = $student['stu_session_id'];
                $name = $student['name'];
                $adno = $student['adno'];
                $father_phone = $student['father_phone'];
                //$father_phone = '7097091226';
                $balance = $student['balance'];
                $class = $student['class'];

                // Send SMS Reminder
                $this->sendSMSReminder($father_phone, $balance, $name, $adno, $class);
            }

            $this->session->set_flashdata('msg', 'SMS reminders sent successfully.');
        } else {
            $this->session->set_flashdata('msg', 'Please select at least one student.');
        }


        redirect('financereports/send_reminders');
    }


    private function sendSMSReminder($mobileno, $balance, $name, $adno, $class)
    {
        //echo $mobileno; exit;
        // Your credentials
        $user = "PRAGATIVHS";
        $password = "Pragati@3233";
        $senderid = "PRGEDS";
        $messagetype = "N"; // Normal text message
        $DReports = "Y"; // Delivery report required

        // SMS API URL
        $url = "http://www.smscountry.com/SMSCwebservice_Bulk.aspx";

        // Create the message, properly encoding special characters
        //$message = "Dear Parent, Please pay your Total Fee Due Amount: $balance before the due date. For details, contact the office. - Pragati Vidyaniketan High School.";
        $message = "Dear Parent, please pay fees due Rs." . $balance . " of " . $name . " " . $class . " Adm-No " . $adno . ", Timing 9 to 11 AM and 2 to 4 PM, (please ignore if paid) - Pragati";

        //echo $message; exit;

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL peer verification (make sure this is safe in production)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'User' => $user,
            'passwd' => $password,
            'mobilenumber' => $mobileno,
            'message' => $message,
            'sid' => $senderid,
            'mtype' => $messagetype,
            'DR' => $DReports
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return response instead of printing it

        // Execute the request and capture the response
        $curlresponse = curl_exec($ch);

        // Error handling
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            // Log the error or take appropriate action
            log_message('error', 'SMS sending failed: ' . $error_msg);
            return false; // Return false to indicate failure
        }

        curl_close($ch);

        log_message('info', 'SMS Response: ' . $curlresponse);

        return $curlresponse;
    }


    // public function studentacademicfeereceipt()
    // {
    //     if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/studentacademicfeereceipt');
    //     $data['title'] = 'student balance fee report';
    //     $data['payment_type'] = $this->customlib->getPaymenttype();
    //     $class = $this->class_model->get();
    //     $data['classlist'] = $class;

    //     $data['sch_setting'] = $this->sch_setting_detail;
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;

    //     // $feesessiongroup = $this->feesessiongroup_model->getFeesByGroup();
    //     $fee_typeList = $this->feetype_model->get();
    //     $data['fee_typeList'] = $fee_typeList;

    //     $this->form_validation->set_rules('class_id', $this->lang->line('classs'), 'trim|required|xss_clean');

    //     // $this->form_validation->set_rules('feegroup[]', $this->lang->line('fee_group'), 'trim|required|xss_clean');

    //     $jsonstaffrole = $this->customlib->getStaffRole();
    //     $staffrole = json_decode($jsonstaffrole);
    //     $staff_id = $this->customlib->getStaffID();

    //     if ($this->form_validation->run() == false) {
    //         $data['student_due_fee'] = array();
    //         $data['resultarray'] = array();
    //         $data['feetype_arr'] = array();
    //     } else {
    //         $student_Array = array();
    //         $search_type = "all";
    //         $class_id = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');
    //         $due_date = $this->input->post('due_date');
    //         // $feegroupIDs = $this->input->post('feegroup');


    //         if (isset($class_id)) {
    //             $studentlist = $this->student_model->searchByClassSectionWithSession($class_id, $section_id);
    //         } else {
    //             $studentlist = $this->student_model->getStudents();
    //         }

    //         $student_Array = array();
    //         $feesTypes = array();
    //         if (!empty($studentlist)) {
    //             foreach ($studentlist as $key => $eachstudent) {
    //                 $obj = new stdClass();
    //                 $obj->name = $this->customlib->getFullName($eachstudent['firstname'], $eachstudent['middlename'], $eachstudent['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
    //                 $obj->id = $eachstudent['id'];
    //                 $obj->class = $eachstudent['class'];
    //                 $obj->section = $eachstudent['section'];
    //                 $obj->admission_no = $eachstudent['admission_no'];
    //                 $obj->roll_no = $eachstudent['roll_no'];
    //                 $obj->father_name = $eachstudent['father_name'];
    //                 $obj->father_phone = $eachstudent['father_phone'];
    //                 $obj->student_session_id = $eachstudent['student_session_id'];

    //                 $student_session_id = $eachstudent['student_session_id'];
    //                 $fee_enquiry_data = $this->feeenquiry_model->get(null, $eachstudent['id']);
    //                 if (isset($fee_enquiry_data) && !empty($fee_enquiry_data)) {
    //                     $obj->status = $fee_enquiry_data['status'];
    //                 } else {
    //                     $obj->status = 'Active';
    //                 }
    //                 $student_total_fees = $this->studentfeemaster_model->getStudentFees($student_session_id);

    //                 // echo  "<pre>";
    //                 // print_r($student_total_fees);



    //                 if (!empty($student_total_fees)) {
    //                     $totalfee = 0;
    //                     $deposit = 0;
    //                     $discount = 0;
    //                     $balance = 0;
    //                     $fine = 0;
    //                     $feetypePaidAmount = [];
    //                     $feetypeBalances = [];

    //                     foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {
    //                         if (!empty($student_total_fees_value->fees)) {
    //                             foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {
    //                                 // echo "<pre>";
    //                                 // print_r($each_fee_value);
    //                                 // Check if fee_groups_id is in feegroupIDs

    //                                 $totalfee += $each_fee_value->amount;
    //                                 $feetype = $each_fee_value->type;
    //                                 $fee_amount = $each_fee_value->amount;
    //                                 $feePaidAmount = 0;

    //                                 if (!in_array($feetype, $feesTypes)) {

    //                                     array_push($feesTypes, $feetype);
    //                                 }

    //                                 if (!isset($feetypePaidAmount[$feetype])) {
    //                                     $feetypePaidAmount[$feetype] = 0;
    //                                 }

    //                                 $feetypeBalances[$feetype] = $fee_amount;

    //                                 $amount_detail = json_decode($each_fee_value->amount_detail);

    //                                 if (is_object($amount_detail)) {
    //                                     foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
    //                                         $feetypePaidAmount[$feetype] += $amount_detail_value->amount;
    //                                         $deposit += $amount_detail_value->amount;
    //                                         $fine += $amount_detail_value->amount_fine;
    //                                         $discount += $amount_detail_value->amount_discount;
    //                                         $feePaidAmount  +=  $amount_detail_value->amount;
    //                                         // remove disount for balance amount
    //                                         $fee_amount -= $amount_detail_value->amount_discount;
    //                                     }


    //                                     $feetypeBalances[$feetype] = $fee_amount - $feePaidAmount;
    //                                 }
    //                             }
    //                         }
    //                     }

    //                     $obj->totalfee = $totalfee;
    //                     $obj->payment_mode = "N/A";
    //                     $obj->deposit = $deposit;
    //                     $obj->fine = $fine;
    //                     $obj->discount = $discount;
    //                     $obj->balance = $totalfee - ($deposit + $discount);
    //                     $obj->feetypePaidAmount = $feetypePaidAmount;
    //                     $obj->feetypeBalances = $feetypeBalances;
    //                 } else {
    //                     $obj->totalfee = 0;
    //                     $obj->payment_mode = 0;
    //                     $obj->deposit = 0;
    //                     $obj->fine = 0;
    //                     $obj->balance = 0;
    //                     $obj->discount = 0;
    //                 }

    //                 if ($search_type == 'all') {
    //                     if ($obj->balance  == 0) {
    //                         $student_Array[$obj->class . " ( " . $obj->section . " ) "][] = $obj;
    //                     }
    //                 }
    //             }
    //         }

    //         // exit;


    //         $classlistdata[] = array('result' => $student_Array);
    //         $data['student_due_fee'] = $student_Array;
    //         $data['resultarray'] = $classlistdata;
    //         $data['feeTypes'] = $feesTypes;
    //         $data['due_date'] = $due_date;
    //     }

    //     // echo "<pre>";
    //     // print_r($data);exit;

    //     $data['staff_id'] = $staff_id;
    //     $data['role_id'] = $staffrole->id;

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/studentacademicfeereceipt', $data);
    //     $this->load->view('layout/footer', $data);
    // }
    
    
    public function get_studentacademicfeereceipt()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        // if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        $data = [
            'status'           => true,
            'title'            => 'Student Balance Fee Report',
            'payment_type'     => $this->customlib->getPaymenttype(),
            'classlist'        => $this->class_model->get(),
            'fee_typeList'     => $this->feetype_model->get(),
            'sch_setting'      => $this->sch_setting_detail,
            'adm_auto_insert'  => $this->sch_setting_detail->adm_auto_insert
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    
    
    public function studentacademicfeereceipt()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        
        // if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        $input = json_decode(file_get_contents("php://input"), true);
    
        $class_id   = $input['class_id'] ?? null;
        $section_id = $input['section_id'] ?? null;
        $due_date   = $input['due_date'] ?? null;
    
        if (empty($class_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'class_id is required'
                ]));
        }
    
        $studentlist = $this->student_model
            ->searchByClassSectionWithSession($class_id, $section_id);
    
        $student_Array = [];
        $feesTypes = [];
    
        foreach ($studentlist as $eachstudent) {
    
            $obj = new stdClass();
            $obj->id             = $eachstudent['id'];
            $obj->name           = $this->customlib->getFullName(
                                        $eachstudent['firstname'],
                                        $eachstudent['middlename'],
                                        $eachstudent['lastname'],
                                        $this->sch_setting_detail->middlename,
                                        $this->sch_setting_detail->lastname
                                    );
            $obj->class          = $eachstudent['class'];
            $obj->section        = $eachstudent['section'];
            $obj->admission_no   = $eachstudent['admission_no'];
            $obj->roll_no        = $eachstudent['roll_no'];
            $obj->father_name    = $eachstudent['father_name'];
            $obj->father_phone   = $eachstudent['father_phone'];
    
            $student_session_id = $eachstudent['student_session_id'];
    
            $student_fees = $this->studentfeemaster_model
                ->getStudentFees($student_session_id);
    
            $total = $deposit = $fine = $discount = 0;
            $feetypePaidAmount = [];
            $feetypeBalances   = [];
    
            foreach ($student_fees as $fee_group) {
                foreach ($fee_group->fees as $fee) {
    
                    $total += $fee->amount;
                    $feetype = $fee->type;
    
                    if (!in_array($feetype, $feesTypes)) {
                        $feesTypes[] = $feetype;
                    }
    
                    $paid = 0;
                    $balanceFee = $fee->amount;
    
                    $amount_detail = json_decode($fee->amount_detail);
    
                    if (is_object($amount_detail)) {
                        foreach ($amount_detail as $amt) {
                            $paid      += $amt->amount;
                            $deposit   += $amt->amount;
                            $fine      += $amt->amount_fine;
                            $discount  += $amt->amount_discount;
                            $balanceFee -= $amt->amount_discount;
                        }
                    }
    
                    $feetypePaidAmount[$feetype] = ($feetypePaidAmount[$feetype] ?? 0) + $paid;
                    $feetypeBalances[$feetype]   = $balanceFee - $paid;
                }
            }
    
            $obj->totalfee = $total;
            $obj->deposit  = $deposit;
            $obj->fine     = $fine;
            $obj->discount = $discount;
            $obj->balance  = $total - ($deposit + $discount);
    
            // Only PAID students
            if ($obj->balance == 0) {
                $student_Array[$obj->class . " ( " . $obj->section . " )"][] = $obj;
            }
        }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'     => true,
                'due_date'   => $due_date,
                'feeTypes'   => $feesTypes,
                'result'     => $student_Array
            ]));
    }
    

    public function printbalancefeereceipt()
    {
        $due_date             = $this->input->post('due_date');
        $student_session_ids  = $this->input->post('student_session_id');

        $student_Array = array();

        foreach ($student_session_ids as $student) {
            // Initialize a new object for each student
            $obj = new stdClass();

            $std = $this->student_model->getByStudentSession($student);

            $obj->name = $this->customlib->getFullName(
                $std['firstname'],
                $std['middlename'],
                $std['lastname'],
                $this->sch_setting_detail->middlename,
                $this->sch_setting_detail->lastname
            );

            $obj->id = $std['id'];
            $obj->class = $std['class'];
            $obj->section = $std['section'];
            $obj->admission_no = $std['admission_no'];
            $obj->roll_no = $std['roll_no'];
            $obj->father_name = $std['father_name'];
            $obj->father_phone = $std['father_phone'];
            $obj->gender = $std['gender'];
            $obj->student_session_id = $std['student_session_id'];

            $student_total_fees = $this->studentfeemaster_model->getStudentFees($student);

            if (!empty($student_total_fees)) {
                $totalfee = 0;
                $deposit = 0;
                $discount = 0;
                $fine = 0;

                foreach ($student_total_fees as $student_total_fees_value) {
                    if (!empty($student_total_fees_value->fees)) {
                        foreach ($student_total_fees_value->fees as $each_fee_value) {
                            $totalfee += $each_fee_value->amount;
                            $amount_detail = json_decode($each_fee_value->amount_detail);

                            if (is_object($amount_detail)) {
                                foreach ($amount_detail as $amount_detail_value) {
                                    $deposit += $amount_detail_value->amount;
                                    $fine += $amount_detail_value->amount_fine;
                                    $discount += $amount_detail_value->amount_discount;
                                }
                            }
                        }
                    }
                }

                $obj->totalfee = $totalfee;
                $obj->deposit = $deposit;
                $obj->fine = $fine;
                $obj->discount = $discount;
                $obj->balance = $totalfee - ($deposit + $discount);
            } else {
                $obj->totalfee = 0;
                $obj->deposit = 0;
                $obj->fine = 0;
                $obj->discount = 0;
                $obj->balance = 0;
            }

            // Add the new object to the array
            $student_Array[] = $obj;
        }

        $data['student_details'] = $student_Array;
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['due_date'] = $due_date;

        // echo "<pre>";
        // print_r($data);exit;

        $student_balance_receipts     = $this->load->view('financereports/_printbalancefeereceipt', $data, true);
        $array                   = array('status' => '1', 'error' => '', 'page' => $student_balance_receipts);
        echo json_encode($array);
    }
}

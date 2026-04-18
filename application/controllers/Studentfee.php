<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentfee extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->library('mailsmsconf');
        $this->load->library('customlib');
        $this->load->library('media_storage');
        $this->load->model("module_model");
        $this->load->model("transportfee_model");
        $this->load->model('feesreceipt_model');
        $this->load->model('feetype_model');
        $this->load->model('bank_model');
        $this->load->library('encoding_lib');
        $this->search_type        = $this->config->item('search_type');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input;
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $class             = $this->class_model->get();
        $start_date        = date('Y-m-d');
        $end_date          = date('Y-m-d');
        $fee_Data          = $this->feesreceipt_model->getTransctionData($start_date, $end_date);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => true,
                'sch_setting' => $this->sch_setting_detail,
                'classlist'   => $class,
                'fees_data'   => $fee_Data
            ]));
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $search_type = $input['search_type'] ?? '';

        if ($search_type == "class_search") {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');
        } elseif ($search_type == "keyword_search") {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('search_text', $this->lang->line('keyword'), 'required|trim|xss_clean');
        }

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $params = array(
                'class_id'    => $input['class_id'] ?? null,
                'section_id'  => $input['section_id'] ?? null,
                'search_type' => $search_type,
                'search_text' => $input['search_text'] ?? null
            );
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => true,
                    'params' => $params
                ]));
        }
    }

    public function ajaxSearch()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input       = $this->_get_input();
        $class       = $input['class_id'] ?? null;
        $section     = $input['section_id'] ?? null;
        $search_text = $input['search_text'] ?? null;
        $search_type = $input['search_type'] ?? '';

        if ($search_type == "class_search") {
            $students = $this->student_model->getDatatableByClassSection($class, $section);
        } elseif ($search_type == "keyword_search") {
            $students = $this->student_model->getDatatableByFullTextSearch($search_text);
        } else {
            $students = json_encode(['data' => []]);
        }

        $sch_setting = $this->sch_setting_detail;
        $students    = json_decode($students);
        $dt_data     = array();
        if (!empty($students->data)) {
            foreach ($students->data as $student_key => $student) {
                $row         = array();
                $row['id']   = $student->id;
                $row['student_session_id'] = $student->student_session_id;
                $row['class'] = $student->class;
                $row['section'] = $student->section;
                $row['admission_no'] = $student->admission_no;
                $row['full_name'] = $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname);
                if ($sch_setting->father_name) {
                    $row['father_name'] = $student->father_name;
                }
                $row['dob'] = $this->customlib->dateformat($student->dob);
                $row['father_phone'] = $student->father_phone;
                $dt_data[] = $row;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw"            => intval($students->draw ?? 0),
                "recordsTotal"    => intval($students->recordsTotal ?? 0),
                "recordsFiltered" => intval($students->recordsFiltered ?? 0),
                "data"            => $dt_data,
            ]));
    }

    public function feesearch()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $class               = $this->class_model->get();
        $feesessiongroup     = $this->feesessiongroup_model->getFeesByGroup();
        $feeTypes            = $this->feetype_model->get();
        $module              = $this->module_model->getPermissionByModulename('transport');

        $currentsessiontransportfee = $this->transportfee_model->getSessionFees($this->current_session);
        if (!empty($currentsessiontransportfee)) {
            if ($module['is_active']) {
                $month_list = $this->customlib->getMonthDropdown($this->sch_setting_detail->start_month);
                $transportfesstype = array();
                foreach ($month_list as $key => $value) {
                    $transportfesstype[] = $this->transportfee_model->transportfesstype($this->current_session, $value);
                }
                $feesessiongroup[count($feesessiongroup)] = (object)array('id' => 'Transport', 'group_name' => 'Transport Fees', 'is_system' => 0, 'feetypes' => $transportfesstype);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('feegroup[]', $this->lang->line('fee_group'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'error'  => $this->form_validation->error_array(),
                        'data'   => [
                            'classlist'           => $class,
                            'feesessiongrouplist' => $feesessiongroup,
                            'feeTypes'            => $feeTypes
                        ]
                    ]));
            } else {
                $feegroups = $input['feegroup'];
                $fee_group_array          = array();
                $fee_groups_feetype_array = array();
                $transport_groups_feetype_array = array();
                foreach ($feegroups as $fee_grp_key => $fee_grp_value) {
                    $feegroup                   = explode("-", $fee_grp_value);
                    if ($feegroup[0] == "Transport") {
                        $transport_groups_feetype_array[] = $feegroup[1];
                    } else {
                        $fee_group_array[]          = $feegroup[0];
                        $fee_groups_feetype_array[] = $feegroup[1];
                    }
                }

                $fee_group_comma = implode(', ', array_map(function ($val) {
                    return sprintf("'%s'", $val);
                }, array_unique($fee_group_array)));
                $fee_groups_feetype_comma = implode(', ', array_map(function ($val) {
                    return sprintf("'%s'", $val);
                }, array_unique($fee_groups_feetype_array)));

                $class_id   = $input['class_id'] ?? null;
                $section_id = $input['section_id'] ?? null;

                $student_due_fee = $this->studentfee_model->getMultipleDueFees($fee_group_comma, $fee_groups_feetype_comma, $transport_groups_feetype_array, $class_id, $section_id);
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
                                continue;
                            } else {
                                if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                                    $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                                }
                                $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                                    'is_system'       => $student_due_fee_value['is_system'],
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
                                'is_system'       => $student_due_fee_value['is_system'],
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
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'               => true,
                        'student_remain_fees'  => array_values($students)
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'              => true,
                'classlist'           => $class,
                'feesessiongrouplist' => $feesessiongroup,
                'feeTypes'            => $feeTypes,
                'sch_setting'         => $this->sch_setting_detail
            ]));
    }

    public function deleteFee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = $this->_get_input();
        $invoice_id  = $input['main_invoice'] ?? null;
        $sub_invoice = $input['sub_invoice'] ?? null;
        if (!empty($invoice_id)) {
            $this->studentfee_model->remove($invoice_id, $sub_invoice);
        }
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['status' => 'success', 'result' => 'success']));
    }

    public function getcollectfee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $record              = $input['data'] ?? '[]';
        $record_array        = is_array($record) ? $record : json_decode($record);

        $fees_array = array();
        if (!empty($record_array)) {
            foreach ($record_array as $key => $value) {
                $fee_groups_feetype_id = $value->fee_groups_feetype_id ?? null;
                $fee_master_id         = $value->fee_master_id ?? null;
                $fee_session_group_id  = $value->fee_session_group_id ?? null;
                $fee_category          = $value->fee_category ?? '';
                $trans_fee_id          = $value->trans_fee_id ?? null;

                if ($fee_category == "transport") {
                    $feeList               = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
                    $feeList->fee_category = $fee_category;
                } else {
                    $feeList               = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
                    $feeList->fee_category = $fee_category;
                    if (isset($value->totalbalanceAmount)) {
                        $feeList->totalbalanceAmount = $value->totalbalanceAmount;
                    }
                    if (isset($value->fee_remaing_amount)) {
                        $feeList->fee_remaing_amount = $value->fee_remaing_amount;
                        $feeList->fee_reaming_id = $value->fee_groups_feetype_id;
                    }
                }
                $fees_array[] = $feeList;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'   => true,
                'feearray' => $fees_array
            ]));
    }


    // public function update_invoice()
    // {

    //     if ($this->input->server('REQUEST_METHOD') === 'POST') {
    //         $receipt_id = $this->input->post('receipt_id');
    //         $status = $this->input->post('status');
    //         $description = $this->input->post('description');

    //         // Update invoice via model
    //         $update_data = array(
    //             'status' => $status,
    //             'description' => $description,
    //             'cancel_date' => date('Y-m-d', $this->datetostrtotime($this->input->post('cancelled_date'))),
    //         );

    //         $updated = $this->feesreceipt_model->update_invoice($receipt_id, $update_data);

    //         if ($updated) {
    //             echo json_encode(['status' => 'success', 'message' => 'Invoice updated successfully']);
    //         } else {
    //             echo json_encode(['status' => 'error', 'message' => 'Failed to update invoice']);
    //         }
    //     }
    // }


    public function update_invoice()
    {
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

        // Read raw JSON body
        $input = json_decode(trim(file_get_contents('php://input')), true);

        if (empty($input)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Invalid JSON body'
                ]));
        }

        $receipt_id    = $input['receipt_id'] ?? null;
        $status        = $input['status'] ?? null;
        $description   = $input['description'] ?? null;
        $cancelled_date = $input['cancelled_date'] ?? null;

        if (empty($receipt_id) || empty($status)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'receipt_id and status are required'
                ]));
        }

        $update_data = [
            'status'      => $status,
            'description' => $description,
            'cancel_date' => !empty($cancelled_date)
                ? date('Y-m-d', $this->datetostrtotime($cancelled_date))
                : null,
        ];

        $updated = $this->feesreceipt_model->update_invoice($receipt_id, $update_data);

        if ($updated) {
            return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => 'Invoice updated successfully'
                ]));
        } else {
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Failed to update invoice'
                ]));
        }
    }


    public function datetostrtotime($date)
    {
        if ($date == "") {
            return "";
        }
        $format = $this->getSchoolDateFormat();
        if ($format == 'd-m-Y') {
            list($day, $month, $year) = explode('-', $date);
        }

        if ($format == 'd/m/Y') {
            list($day, $month, $year) = explode('/', $date);
        }

        if ($format == 'd-M-Y') {
            list($day, $month, $year) = explode('-', $date);
        }

        if ($format == 'd.m.Y') {
            list($day, $month, $year) = explode('.', $date);
        }

        if ($format == 'm-d-Y') {
            list($month, $day, $year) = explode('-', $date);
        }

        if ($format == 'm/d/Y') {
            list($month, $day, $year) = explode('/', $date);
        }

        if ($format == 'm.d.Y') {
            list($month, $day, $year) = explode('.', $date);
        }

        if ($format == 'Y/m/d') {
            list($year, $month, $day) = explode('/', $date);
        }

        $date = $year . "-" . $month . "-" . $day;

        return strtotime($date);
    }


    public function getSchoolDateFormat($date_only = true, $time = false)
    {
        $setting_result     = $this->setting_model->get();
        return $date_format = $setting_result[0]['date_format'];
    }


    public function getchallanfee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input               = $this->_get_input();
        $student_id          = $input['student_id'] ?? null;
        $due_dates           = $this->studentfeemaster_model->getstudentduedates($student_id);
        $due_date            = $input['due_date'] ?? null;
        $bank_data           = $this->bank_model->get();
        $resultlist          = $this->studentfeemaster_model->getStudentFeesbyduedate($student_id, $due_date);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'           => '1',
                'error'            => '',
                'student_duedates' => $due_dates,
                'bank_data'        => $bank_data,
                'resultlist_data'  => $resultlist,
                'due_date'         => $due_date
            ]));
    }

    public function save_challan_fees()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input        = $this->_get_input();
        $student_id   = $input['student_id'] ?? null;
        $amount       = $input['amount'] ?? null;
        $fee_groups   = $input['fee_groups'] ?? null;
        $fee_types    = $input['fee_types'] ?? null;
        $generated_by = $input['generated_by'] ?? null;
        $due_date     = $input['due_date'] ?? null;
        $date         = $input['date'] ?? null;
        $bank         = $input['bank'] ?? null;

        if (!empty($student_id) && !empty($fee_groups) && !empty($fee_types)) {
            $data = array(
                'student_session_id' => $student_id,
                'amount'             => $amount,
                'fee_groups'         => $fee_groups,
                'fee_types'          => $fee_types,
                'generated_by'       => $generated_by,
                'due_date'           => $due_date,
                'date'               => $date,
                'bank'               => $bank
            );

            $this->db->insert('student_challan', $data);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 'success']));
        } else {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Missing required fields']));
        }
    }



    public function getcollectfee1()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $record              = $input['data'] ?? '[]';
        $record_array        = is_array($record) ? $record : json_decode($record);

        $fees_array = array();
        if (!empty($record_array)) {
            foreach ($record_array as $key => $value) {
                $fee_groups_feetype_id = $value->fee_groups_feetype_id ?? null;
                $fee_master_id         = $value->fee_master_id ?? null;
                $fee_session_group_id  = $value->fee_session_group_id ?? null;
                $fee_category          = $value->fee_category ?? '';
                $trans_fee_id          = $value->trans_fee_id ?? null;

                if ($fee_category == "transport") {
                    $feeList               = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
                    $feeList->fee_category = $fee_category;
                } else {
                    $feeList               = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
                    $feeList->fee_category = $fee_category;
                    if (isset($value->fee_remaing_amount)) {
                        $feeList->fee_remaing_amount =   $value->fee_remaing_amount;
                        $feeList->fee_reaming_id = $value->fee_groups_feetype_id;
                    }
                }
                $fees_array[] = $feeList;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'   => true,
                'feearray' => $fees_array
            ]));
    }

    public function addfee($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $data['sch_setting']        = $this->sch_setting_detail;
        $student                    = $this->student_model->getByStudentSessionDetails($id);
        $get_academics             = $this->student_model->getstudentAcademics($student['id']);
        $data['student_academics']  = $get_academics;
        $route_pickup_point_id      = $student['route_pickup_point_id'];
        $student_session_id         = $student['student_session_id'];
        $transport_fees             = [];

        $module = $this->module_model->getPermissionByModulename('transport');
        if ($module['is_active']) {
            $transport_fees         = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);
        }

        $data['student']            = $student;
        $admission_no               = $student['admission_no'];
        $studentData               = $this->student_model->getOldStudentDataByAdmissionNo($admission_no);

        if (!empty($studentData)) {
            $oldStudentSession      = $studentData->student_session_id;
            $student_old_due_fee    = $this->studentfeemaster_model->getStudentFees($oldStudentSession);
            $total_amount           = 0;
            $total_deposite_amount = 0;
            $total_discount_amount = 0;
            $total_fine_amount     = 0;
            $total_balance_amount  = 0;

            foreach ($student_old_due_fee as $key => $fee) {
                foreach ($fee->fees as $fee_key => $fee_value) {
                    $fee_paid     = 0;
                    $fee_discount = 0;
                    $fee_fine     = 0;
                    if (!empty($fee_value->amount_detail)) {
                        $fee_deposits = json_decode(($fee_value->amount_detail));
                        foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                            $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                            $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                            $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                        }
                    }
                    $total_amount          += $fee_value->amount;
                    $total_discount_amount += $fee_discount;
                    $total_deposite_amount += $fee_paid;
                    $total_fine_amount     += $fee_fine;
                    $feetype_balance        = $fee_value->amount - ($fee_paid + $fee_discount);
                    $total_balance_amount  += $feetype_balance;
                }
            }
            $data['oldFee'] = array('total_balence_amount' => $total_balance_amount, 'total_amount' => $total_amount, 'total_paid_balence' => $total_deposite_amount, 'total_fee_discount' => $total_discount_amount);
        }

        $student_due_fee            = $this->studentfeemaster_model->getStudentFees($id);
        $student_discount_fee       = $this->feediscount_model->getStudentFeesDiscount($id);
        $data['transport_fees']     = $transport_fees;
        $data['student_discount_fee'] = $student_discount_fee;
        $data['student_due_fee']    = $student_due_fee;
        $category                   = $this->category_model->get();
        $data['categorylist']       = $category;
        $class_section              = $this->student_model->getClassSection($student["class_id"]);
        $data["class_section"]      = $class_section;
        $session                    = $this->setting_model->getCurrentSession();
        $studentlistbysection       = $this->student_model->getStudentClassSection($student["class_id"], $session);
        $data["studentlistbysection"] = $studentlistbysection;
        $student_processing_fee     = $this->studentfeemaster_model->getStudentProcessingFees($id);
        $data['student_processing_fee'] = false;

        foreach ($student_processing_fee as $key => $processing_value) {
            if (!empty($processing_value->fees)) {
                $data['student_processing_fee'] = true;
            }
        }

        $fee_payments             = $this->feesreceipt_model->get24feesBySudent_session_id($student_session_id);
        $data['fee_payments']     = $fee_payments;

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }

    public function getProcessingfees($id)
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_add')) {
            access_denied();
        }

        $student               = $this->student_model->getByStudentSession($id);
        $route_pickup_point_id = $student['route_pickup_point_id'];
        $student_session_id    = $student['student_session_id'];
        $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);
        $data['student']       = $student;
        $student_due_fee       = $this->studentfeemaster_model->getStudentProcessingFees($id);
        $data['transport_fees']  = $transport_fees;
        $data['student_due_fee'] = $student_due_fee;

        $result = array(
            'view' => $this->load->view('user/student/getProcessingfees', $data, true),
        );
        $this->output->set_output(json_encode($result));
    }

    public function deleteTransportFee()
    {
        $id = $this->input->post('feeid');
        $this->studenttransportfee_model->remove($id);
        $array = array('status' => 'success', 'result' => 'success');
        echo json_encode($array);
    }

    public function delete($id)
    {
        $data['title'] = 'studentfee List';
        $this->studentfee_model->remove($id);
        redirect('studentfee/index');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }
        $data['title'] = 'Add studentfee';
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/studentfeeCreate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'category' => $this->input->post('category'),
            );
            $this->studentfee_model->add($data);
            $this->session->set_flashdata('msg', '<div studentfee="alert alert-success text-center">' . $this->lang->line('success_message') . '</div>');
            redirect('studentfee/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_edit')) {
            access_denied();
        }
        $data['title']      = 'Edit studentfees';
        $data['id']         = $id;
        $studentfee         = $this->studentfee_model->get($id);
        $data['studentfee'] = $studentfee;
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/studentfeeEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'       => $id,
                'category' => $this->input->post('category'),
            );
            $this->studentfee_model->add($data);
            $this->session->set_flashdata('msg', '<div studentfee="alert alert-success text-center">' . $this->lang->line('update_message') . '</div>');
            redirect('studentfee/index');
        }
    }

    public function addstudentfee()
    {
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric|callback_check_deposit');
        $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('amount_fine', $this->lang->line('fine'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'amount'                 => form_error('amount'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'amount_discount'        => form_error('amount_discount'),
                'amount_fine'            => form_error('amount_fine'),
                'payment_mode'           => form_error('payment_mode'),
                'date'           => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $staff_record = $this->staff_model->get($this->customlib->getStaffID());

            $collected_by             = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";
            $student_fees_discount_id = $this->input->post('student_fees_discount_id');
            $json_array               = array(
                'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'amount_discount' => convertCurrencyFormatToBaseAmount($this->input->post('amount_discount')),
                'amount_fine'     => convertCurrencyFormatToBaseAmount($this->input->post('amount_fine')),
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description'     => $this->input->post('description'),
                'collected_by'    => $collected_by,
                'payment_mode'    => $this->input->post('payment_mode'),
                'received_by'     => $staff_record['id'],
            );

            $student_fees_master_id = $this->input->post('student_fees_master_id');
            $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
            $transport_fees_id      = $this->input->post('transport_fees_id');
            $fee_category           = $this->input->post('fee_category');

            $data = array(
                'fee_category'           => $fee_category,
                'student_fees_master_id' => $this->input->post('student_fees_master_id'),
                'fee_groups_feetype_id'  => $this->input->post('fee_groups_feetype_id'),
                'amount_detail'          => $json_array,
            );

            if ($transport_fees_id != 0 && $fee_category == "transport") {
                $mailsms_array                    = new stdClass();
                $data['student_fees_master_id']   = null;
                $data['fee_groups_feetype_id']    = null;
                $data['student_transport_fee_id'] = $transport_fees_id;

                $mailsms_array                 = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($transport_fees_id);
                $mailsms_array->fee_group_name = $this->lang->line("transport_fees");
                $mailsms_array->type           = $mailsms_array->month;
                $mailsms_array->code           = "";
            } else {

                $mailsms_array = $this->feegrouptype_model->getFeeGroupByIDAndStudentSessionID($this->input->post('fee_groups_feetype_id'), $this->input->post('student_session_id'));

                if ($mailsms_array->is_system) {
                    $mailsms_array->amount = $mailsms_array->balance_fee_master_amount;
                }
            }

            $action             = $this->input->post('action');
            $send_to            = $this->input->post('guardian_phone');
            $email              = $this->input->post('guardian_email');
            $parent_app_key     = $this->input->post('parent_app_key');
            $student_session_id = $this->input->post('student_session_id');
            $inserted_id        = $this->studentfeemaster_model->fee_deposit($data, $send_to, $student_fees_discount_id);

            $print_record = array();
            if ($action == "print") {
                $receipt_data           = json_decode($inserted_id);
                $data['sch_setting']    = $this->sch_setting_detail;

                $student                = $this->studentsession_model->searchStudentsBySession($student_session_id);
                $data['student']        = $student;
                $data['sub_invoice_id'] = $receipt_data->sub_invoice_id;

                $setting_result         = $this->setting_model->get();
                $data['settinglist']    = $setting_result;

                if ($transport_fees_id != 0 && $fee_category == "transport") {

                    $fee_record = $this->studentfeemaster_model->getTransportFeeByInvoice($receipt_data->invoice_id, $receipt_data->sub_invoice_id);
                    $data['feeList']        = $fee_record;
                    $print_record = $this->load->view('print/printTransportFeesByName', $data, true);
                } else {

                    $fee_record             = $this->studentfeemaster_model->getFeeByInvoice($receipt_data->invoice_id, $receipt_data->sub_invoice_id);
                    $data['feeList']        = $fee_record;
                    $print_record = $this->load->view('print/printFeesByName', $data, true);
                }
            }

            $mailsms_array->invoice            = $inserted_id;
            $mailsms_array->student_session_id = $student_session_id;
            $mailsms_array->contact_no         = $send_to;
            $mailsms_array->email              = $email;
            $mailsms_array->parent_app_key     = $parent_app_key;
            $mailsms_array->fee_category       = $fee_category;

            $this->mailsmsconf->mailsms('fee_submission', $mailsms_array);

            $array = array('status' => 'success', 'error' => '', 'print' => $print_record);
            echo json_encode($array);
        }
    }

    public function printFeesByName()
    {
        $data                   = array('payment' => "0");
        $record                 = $this->input->post('data');
        $fee_category           = $this->input->post('fee_category');
        $invoice_id             = $this->input->post('main_invoice');
        $sub_invoice_id         = $this->input->post('sub_invoice');
        $student_session_id     = $this->input->post('student_session_id');
        $setting_result         = $this->setting_model->get();
        $data['settinglist']    = $setting_result;
        $student                = $this->studentsession_model->searchStudentsBySession($student_session_id);
        $data['student']        = $student;
        $data['sub_invoice_id'] = $sub_invoice_id;
        $data['sch_setting']    = $this->sch_setting_detail;

        $data['superadmin_rest'] = $this->customlib->superadmin_visible();

        if ($fee_category == "transport") {
            $fee_record      = $this->studentfeemaster_model->getTransportFeeByInvoice($invoice_id, $sub_invoice_id);
            $data['feeList'] = $fee_record;
            $page            = $this->load->view('print/printTransportFeesByName', $data, true);
        } else {
            $fee_record      = $this->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
            $data['feeList'] = $fee_record;
            $page = $this->load->view('print/printFeesByName', $data, true);
        }

        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printFeesByGroup()
    {
        $fee_category        = $this->input->post('fee_category');
        $trans_fee_id        = $this->input->post('trans_fee_id');
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $data['sch_setting'] = $this->sch_setting_detail;

        if ($fee_category == "transport") {
            $data['feeList'] = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
            $page = $this->load->view('print/printTransportFeesByGroup', $data, true);
        } else {

            $fee_groups_feetype_id = $this->input->post('fee_groups_feetype_id');
            $fee_master_id         = $this->input->post('fee_master_id');
            $fee_session_group_id  = $this->input->post('fee_session_group_id');
            $data['feeList']       = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
            $page                  = $this->load->view('print/printFeesByGroup', $data, true);
        }

        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printFeesByGroupArray()
    {
        $data['sch_setting'] = $this->sch_setting_detail;
        $record              = $this->input->post('data');
        $record_array        = json_decode($record);
        $fees_array          = array();
        foreach ($record_array as $key => $value) {
            $fee_groups_feetype_id = $value->fee_groups_feetype_id;
            $fee_master_id         = $value->fee_master_id;
            $fee_session_group_id  = $value->fee_session_group_id;
            $fee_category          = $value->fee_category;
            $trans_fee_id          = $value->trans_fee_id;

            if ($fee_category == "transport") {
                $feeList               = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
                $feeList->fee_category = $fee_category;
            } else {
                $feeList               = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
                $feeList->fee_category = $fee_category;
            }

            $fees_array[] = $feeList;
        }

        $data['feearray'] = $fees_array;
        $this->load->view('print/printFeesByGroupArray', $data);
    }

    public function printFeesByChallan()
    {
        $data['sch_setting'] = $this->sch_setting_detail;
        $record              = $this->input->post('data');
        $record_array        = json_decode($record);
        $fees_array          = array();
        foreach ($record_array as $key => $value) {
            $fee_groups_feetype_id = $value->fee_groups_feetype_id;
            $fee_master_id         = $value->fee_master_id;
            $fee_session_group_id  = $value->fee_session_group_id;
            $fee_category          = $value->fee_category;
            $trans_fee_id          = $value->trans_fee_id;

            if ($fee_category == "transport") {
                $feeList               = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
                $feeList->fee_category = $fee_category;
            } else {
                $feeList               = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
                $feeList->fee_category = $fee_category;
            }

            $fees_array[] = $feeList;
        }

        $data['feearray'] = $fees_array;
        $this->load->view('print/printChallan', $data);
    }

    public function searchpayment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }
        $this->form_validation->set_rules('paymentid', $this->lang->line('payment_id'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $paymentid = $input['paymentid'];
            $invoice   = explode("/", $paymentid);

            if (array_key_exists(0, $invoice) && array_key_exists(1, $invoice)) {
                $invoice_id             = $invoice[0];
                $sub_invoice_id         = $invoice[1];
                $feeList                = $this->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'         => true,
                        'feeList'        => $feeList,
                        'sub_invoice_id' => $sub_invoice_id
                    ]));
            } else {
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => true,
                        'feeList' => array()
                    ]));
            }
        }
    }

    public function addfeegroup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('fee_session_groups', $this->lang->line('fee_group'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $student_session_id     = $input['student_session_id'] ?? array();
            $fee_session_groups     = $input['fee_session_groups'];
            $student_sesssion_array = is_array($student_session_id) ? $student_session_id : array($student_session_id);
            $student_ids            = $input['student_ids'] ?? array();
            $delete_student         = array_diff($student_ids, $student_sesssion_array);

            $preserve_record = array();
            if (!empty($student_sesssion_array)) {
                foreach ($student_sesssion_array as $key => $value) {
                    $insert_array = array(
                        'student_session_id'   => $value,
                        'fee_session_group_id' => $fee_session_groups,
                    );
                    $inserted_id = $this->studentfeemaster_model->add($insert_array);
                    $preserve_record[] = $inserted_id;
                }
            }
            if (!empty($delete_student)) {
                $this->studentfeemaster_model->delete($fee_session_groups, $delete_student);
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message')
                ]));
        }
    }

    public function geBalanceFee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('fee_groups_feetype_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('student_fees_master_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_session_id', $this->lang->line('student_session_id'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $student_session_id   = $input['student_session_id'];
            $discount_not_applied = $this->getNotAppliedDiscount($student_session_id);
            $fee_category         = $input['fee_category'] ?? '';

            if ($fee_category == "transport") {
                $trans_fee_id         = $input['trans_fee_id'];
                $remain_amount_object = $this->getStudentTransportFeetypeBalance($trans_fee_id);
                $remain_amount        = (float) json_decode($remain_amount_object)->balance;
                $remain_amount_fine   = json_decode($remain_amount_object)->fine_amount;
            } else {
                $fee_groups_feetype_id  = $input['fee_groups_feetype_id'];
                $student_fees_master_id = $input['student_fees_master_id'];
                $remain_amount_object   = $this->getStuFeetypeBalance($fee_groups_feetype_id, $student_fees_master_id);
                $remain_amount          = json_decode($remain_amount_object)->balance;
                $remain_amount_fine     = json_decode($remain_amount_object)->fine_amount;
            }

            $remain_amount = number_format($remain_amount, 2, ".", "");

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'               => 'success',
                    'balance'              => convertBaseAmountCurrencyFormat($remain_amount),
                    'discount_not_applied' => $discount_not_applied,
                    'remain_amount_fine'   => convertBaseAmountCurrencyFormat($remain_amount_fine),
                    'student_fees'         => convertBaseAmountCurrencyFormat(json_decode($remain_amount_object)->student_fees)
                ]));
        }
    }

    public function getStudentTransportFeetypeBalance($trans_fee_id)
    {
        $data = array();

        $result          = $this->studentfeemaster_model->studentTransportDeposit($trans_fee_id);
        $amount_balance  = 0;
        $amount          = 0;
        $amount_fine     = 0;
        $amount_discount = 0;
        $fine_amount     = 0;
        $fee_fine_amount = 0;

        $due_amt = $result->fees;
        if (strtotime($result->due_date) < strtotime(date('Y-m-d'))) {
            $fee_fine_amount = is_null($result->fine_percentage) ? $result->fine_amount : percentageAmount($result->fees, $result->fine_percentage);
        }

        $amount_detail = json_decode($result->amount_detail);
        if (is_object($amount_detail)) {

            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                $amount          = $amount + $amount_detail_value->amount;
                $amount_discount = $amount_discount + $amount_detail_value->amount_discount;
                $amount_fine     = $amount_fine + $amount_detail_value->amount_fine;
            }
        }

        $amount_balance = $due_amt - ($amount + $amount_discount);
        $fine_amount    = abs($amount_fine - $fee_fine_amount);
        $array          = array('status' => 'success', 'error' => '', 'student_fees' => $due_amt, 'balance' => $amount_balance, 'fine_amount' => $fine_amount);
        return json_encode($array);
    }

    public function getStuFeetypeBalance($fee_groups_feetype_id, $student_fees_master_id)
    {
        $data                           = array();
        $data['fee_groups_feetype_id']  = $fee_groups_feetype_id;
        $data['student_fees_master_id'] = $student_fees_master_id;
        $result                         = $this->studentfeemaster_model->studentDeposit($data);

        $amount_balance  = 0;
        $amount          = 0;
        $amount_fine     = 0;
        $amount_discount = 0;
        $fine_amount     = 0;
        $fee_fine_amount = 0;
        $due_amt         = $result->amount;
        if (strtotime($result->due_date) < strtotime(date('Y-m-d'))) {
            $fee_fine_amount = $result->fine_amount;
        }

        if ($result->is_system) {
            $due_amt = $result->student_fees_master_amount;
        }

        $amount_detail = json_decode($result->amount_detail);
        if (is_object($amount_detail)) {

            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                $amount          = $amount + $amount_detail_value->amount;
                $amount_discount = $amount_discount + $amount_detail_value->amount_discount;
                $amount_fine     = $amount_fine + $amount_detail_value->amount_fine;
            }
        }

        $amount_balance = $due_amt - ($amount + $amount_discount);
        $fine_amount    = ($fee_fine_amount > 0) ? ($fee_fine_amount - $amount_fine) : 0;

        $array          = array('status' => 'success', 'error' => '', 'student_fees' => $due_amt, 'balance' => $amount_balance, 'fine_amount' => $fine_amount);
        return json_encode($array);
    }

    public function check_deposit($amount)
    {
        if (is_numeric($this->input->post('amount')) && is_numeric($this->input->post('amount_discount'))) {
            if ($this->input->post('amount') != "" && $this->input->post('amount_discount') != "") {
                if ($this->input->post('amount') < 0) {
                    $this->form_validation->set_message('check_deposit', $this->lang->line('deposit_amount_can_not_be_less_than_zero'));
                    return false;
                } else {
                    $transport_fees_id      = $this->input->post('transport_fees_id');
                    $student_fees_master_id = $this->input->post('student_fees_master_id');
                    $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
                    $deposit_amount         = $this->input->post('amount') + $this->input->post('amount_discount');
                    if ($transport_fees_id != 0) {
                        $remain_amount = $this->getStudentTransportFeetypeBalance($transport_fees_id);
                    } else {
                        $remain_amount = $this->getStuFeetypeBalance($fee_groups_feetype_id, $student_fees_master_id);
                    }
                    $remain_amount = json_decode($remain_amount)->balance;
                    if (convertBaseAmountCurrencyFormat($remain_amount) < $deposit_amount) {
                        $this->form_validation->set_message('check_deposit', $this->lang->line('deposit_amount_can_not_be_greater_than_remaining'));
                        return false;
                    } else {
                        return true;
                    }
                }
                return true;
            }
        } elseif (!is_numeric($this->input->post('amount'))) {
            $this->form_validation->set_message('check_deposit', $this->lang->line('amount_field_must_contain_only_numbers'));
            return false;
        } elseif (!is_numeric($this->input->post('amount_discount'))) {
            return true;
        }

        return true;
    }

    public function getNotAppliedDiscount($student_session_id)
    {
        $discounts_array = $this->feediscount_model->getDiscountNotApplied($student_session_id);
        foreach ($discounts_array as $discount_key => $discount_value) {
            $discounts_array[$discount_key]->{"amount"} = convertBaseAmountCurrencyFormat($discount_value->amount);
        }
        return $discounts_array;
    }

    public function addfeegrp()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('row_counter[]', $this->lang->line('fees_list'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('collected_date', $this->lang->line('date'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 0,
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $collected_array = array();
            $staff_id = $this->customlib->getStaffID();
            if (empty($staff_id)) {
                $staff_id = 1;
            }
            $staff_record    = $this->staff_model->get($staff_id);
            $collected_by    = ($this->customlib->getAdminSessionUserName() ?: 'Admin') . "(" . ($staff_record['employee_id'] ?? '1') . ")";

            $student_session_id = $input['student_session_id'];
            $totalbalanceAmount = $input['totalbalanceAmount'];
            $student            = $this->student_model->getByStudentSession($student_session_id);
            $total_row          = $input['row_counter'];

            foreach ($total_row as $total_row_key => $total_row_value) {
                $fee_category             = $input['fee_category_' . $total_row_value];
                $student_transport_fee_id = $input['trans_fee_id_' . $total_row_value];

                $json_array = array(
                    'amount'          => $input['fee_amount_' . $total_row_value],
                    'date'            => $this->safeDate($input['collected_date']),
                    'description'     => $input['fee_gupcollected_note'] ?? '',
                    'amount_discount' => 0,
                    'collected_by'    => $collected_by,
                    'amount_fine'     => $input['fee_groups_feetype_fine_amount_' . $total_row_value] ?? 0,
                    'payment_mode'    => $input['payment_mode_fee'],
                    'received_by'     => $staff_id,
                );
                $collected_array[] = array(
                    'fee_category'             => $fee_category,
                    'student_transport_fee_id' => $student_transport_fee_id,
                    'student_fees_master_id'   => $input['student_fees_master_id_' . $total_row_value],
                    'fee_groups_feetype_id'    => $input['fee_groups_feetype_id_' . $total_row_value],
                    'amount_detail'            => $json_array,
                );
            }

            $deposited_fees = $this->studentfeemaster_model->fee_deposit_collections($collected_array);

            $t = $input['total_amount'];
            $balance = (int) $totalbalanceAmount - (int)$t;

            $student_print = array(
                'student_session_id'    => $student_session_id,
                'amount'                => $input['total_amount'],
                'fee_types'             => $input['fee_type'],
                'collected_by'          => $collected_by,
                'mode'                  => $input['payment_mode_fee'],
                'reference_no'          => $input['reference_no'] ?? '',
                'balanceAmount'         => $balance,
                'created_at'            => $this->safeDate($input['collected_date']),
                'status'                => 0
            );

            $academic_session = $this->customlib->getCurrentSession();
            if (!empty($academic_session)) {
                $asession = $this->customlib->getAcademicSession($academic_session['session']);
                $end_year = $asession['end_year'];
                $start_year = $asession['start_year'];
            } else {
                $start_year = "24";
                $end_year = "24";
            }

            $table_name = 'student_fees_print_' . $start_year;
            $this->db->insert($table_name, $student_print);

            if ($deposited_fees && is_array($deposited_fees)) {
                $invoice = array();
                foreach ($deposited_fees as $deposited_fees_key => $deposited_fees_value) {
                    $fee_category = $deposited_fees_value['fee_category'];
                    $invoice[]   = array(
                        'invoice_id'     => $deposited_fees_value['invoice_id'],
                        'sub_invoice_id' => $deposited_fees_value['sub_invoice_id'],
                        'fee_category'   => $fee_category,
                    );
                }

                // SMS/Mail Logic (keep but ensure it doesn't crash)
                try {
                    $obj_mail                     = [];
                    $obj_mail['student_id']       = $student['id'];
                    $obj_mail['student_session_id'] = $student_session_id;
                    $obj_mail['invoice']         = $invoice;
                    $obj_mail['contact_no']      = $student['guardian_phone'];
                    $obj_mail['email']           = $student['email'];
                    $obj_mail['parent_app_key']  = $student['parent_app_key'];
                    $obj_mail['amount']          = $t;
                    $obj_mail['fee_category']    = $fee_category;
                    $obj_mail['send_type']       = 'group';
                    $this->mailsmsconf->mailsms('fee_submission', $obj_mail);
                } catch (Exception $e) {
                }
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 1, 'error' => '']));
        }
    }


    private function safeDate($date)
    {
        if (empty($date)) {
            return null;
        }

        $ts = strtotime($date);
        return $ts ? date('Y-m-d', $ts) : null;
    }


    public function addstudentchallan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('row_counter[]', $this->lang->line('fees_list'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('collected_date', $this->lang->line('date'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 0,
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $staff_id = $this->customlib->getStaffID() ?: 1;
            $staff_record    = $this->staff_model->get($staff_id);
            $collected_by    = ($this->customlib->getAdminSessionUserName() ?: 'Admin') . "(" . ($staff_record['employee_id'] ?? '1') . ")";

            $student_session_id = $input['student_session_id'];
            $totalbalanceAmount = $input['totalbalanceAmount'];

            $t = $input['total_amount'];
            $balance = (float)$totalbalanceAmount - (float)$t;

            $student_print = array(
                'student_session_id'    => $student_session_id,
                'amount'                => $t,
                'fee_types'             => $input['fee_type'],
                'generated_by'          => $collected_by,
                'date'                  => date('Y-m-d', $this->customlib->datetostrtotime($input['collected_date'])),
                'bank'                  => $input['bankdetails'] ?? '',
            );

            $this->db->insert('student_challan', $student_print);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 1, 'error' => '']));
        }
    }


    public function addfeegrp1()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('row_counter[]', $this->lang->line('fees_list'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('collected_date', $this->lang->line('date'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 0,
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $collected_array = array();
            $staff_id = $this->customlib->getStaffID() ?: 1;
            $staff_record    = $this->staff_model->get($staff_id);
            $collected_by    = ($this->customlib->getAdminSessionUserName() ?: 'Admin') . "(" . ($staff_record['employee_id'] ?? '1') . ")";

            $student_session_id = $input['student_session_id'];
            $student = $this->student_model->getByStudentSession($student_session_id);
            $total_row          = $input['row_counter'];

            foreach ($total_row as $total_row_key => $total_row_value) {
                $fee_category             = $input['fee_category_' . $total_row_value];
                $student_transport_fee_id = $input['trans_fee_id_' . $total_row_value];

                $json_array = array(
                    'amount'          => 0,
                    'date'            => date('Y-m-d', $this->customlib->datetostrtotime($input['collected_date'])),
                    'description'     => $input['fee_gupcollected_note'] ?? '',
                    'amount_discount' => $input['fee_amount_' . $total_row_value],
                    'collected_by'    => $collected_by,
                    'amount_fine'     => $input['fee_groups_feetype_fine_amount_' . $total_row_value] ?? 0,
                    'payment_mode'    => $input['payment_mode_fee'],
                    'received_by'     => $staff_id,
                );
                $collected_array[] = array(
                    'fee_category'             => $fee_category,
                    'student_transport_fee_id' => $student_transport_fee_id,
                    'student_fees_master_id'   => $input['student_fees_master_id_' . $total_row_value],
                    'fee_groups_feetype_id'    => $input['fee_groups_feetype_id_' . $total_row_value],
                    'amount_detail'            => $json_array,
                );
            }

            $deposited_fees = $this->studentfeemaster_model->fee_deposit_collections($collected_array);

            if ($deposited_fees && is_array($deposited_fees)) {
                $invoice = array();
                foreach ($deposited_fees as $deposited_fees_key => $deposited_fees_value) {
                    $fee_category = $deposited_fees_value['fee_category'];
                    $invoice[]   = array(
                        'invoice_id'     => $deposited_fees_value['invoice_id'],
                        'sub_invoice_id' => $deposited_fees_value['sub_invoice_id'],
                        'fee_category'   => $fee_category,
                    );
                }

                try {
                    $obj_mail                     = [];
                    $obj_mail['student_id']       = $student['id'];
                    $obj_mail['student_session_id'] = $student_session_id;
                    $obj_mail['invoice']         = $invoice;
                    $obj_mail['contact_no']      = $student['guardian_phone'];
                    $obj_mail['email']           = $student['email'];
                    $obj_mail['parent_app_key']  = $student['parent_app_key'];
                    $obj_mail['amount']          = $input['total_amount'] ?? 0;
                    $obj_mail['fee_category']    = $fee_category;
                    $obj_mail['send_type']       = 'group';
                    $this->mailsmsconf->mailsms('fee_submission', $obj_mail);
                } catch (Exception $e) {
                }
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 1, 'error' => '']));
        }
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

    public function fees_analysis_report()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $start_date         = date('Y-m-d');
        $end_date           = date('Y-m-d');
        $fee_Data           = $this->feesreceipt_model->getTransctionData($start_date, $end_date);

        $studentlist = $this->student_model->getStudents();
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

                $student_session_id = $eachstudent['student_session_id'];
                $student_total_fees = $this->studentfeemaster_model->getStudentFees($student_session_id);

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
                                        $feePaidAmount  +=  $amount_detail_value->amount;
                                        $fee_amount -= $amount_detail_value->amount_discount;
                                    }
                                    $feetypeBalances[$feetype] = $fee_amount - $feePaidAmount;
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
                $student_Array[] = $obj;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'        => true,
                'fees_data'     => $fee_Data,
                'student_Array' => $student_Array
            ]));
    }


    // export bulk payment collection 

    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_students_payment_file.csv";


        $data     = file_get_contents($filepath);
        $name     = 'import_student_payment_file.csv';

        force_download($name, $data);
    }

    // import payments 

    public function importpaymentsold()
    {
        if (!$this->rbac->hasPrivilege('import_payments', 'can_view')) {
            access_denied();
        }
        $data['title']      = $this->lang->line('import_payments');
        $data['title_list'] = $this->lang->line('recently_added_student');
        $session            = $this->setting_model->getCurrentSession();

        $userdata           = $this->customlib->getUserData();

        $fields = array('admission_no', 'fee_type', 'fee_category', 'date', 'payment_mode', 'amount', 'description', 'amount_discount', 'collected_by', 'amount_fine');
        $data["fields"]       = $fields;

        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/studentfeeSearch', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $session = $this->setting_model->getCurrentSession();
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];
                    $this->load->library('CSVReader');
                    $result = $this->csvreader->parse_file($file);

                    if (!empty($result)) {
                        $rowcount = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $student_data[$i] = array();
                            $n                = 0;
                            foreach ($result[$i] as $key => $value) {

                                $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);

                                $student_data[$i]['is_active'] = 'yes';

                                $datetime = DateTime::createFromFormat('m/d/Y', $result[$i]['date']);
                                if ($datetime !== false) {
                                    $formatted_date = $datetime->format('Y-m-d');
                                } else {
                                    // Handle the error or assign a default date value

                                    $formatted_date = $student_data[$i]['date']; // or another appropriate default value
                                }
                                $student_data[$i]['date'] = $formatted_date;

                                $n++;
                            }

                            $student_details = $this->student_model->findByAdmission($student_data[$i]["admission_no"]);
                            $student_session_id = $student_details->student_session_id;

                            $feetypes = $student_data[$i]['fee_type'];

                            $fee_array = explode(',', $feetypes);
                            $amount_array = explode('+', $student_data[$i]['amount']);
                            $discount_array = explode('+', $student_data[$i]['amount_discount']);
                            $fine_array = explode(',', $student_data[$i]['amount_fine']);
                            $staff_record    = $this->staff_model->get($this->customlib->getStaffID());


                            $fee_category =  $student_data[$i]['fee_category'];
                            $fee_date = $student_data[$i]['date'];
                            $description = $student_data[$i]['description'];
                            $payment_mode = $student_data[$i]['payment_mode'];
                            $collected_by = $student_data[$i]['collected_by'];

                            $fees_data = array();
                            $selected_fee_types = array();

                            $receipt_amount = 0;

                            for ($j = 0; $j < count($fee_array); $j++) {

                                $res['fee_category'] = $fee_category;
                                $res['student_transport_fee_id'] = 0;

                                $fee = $fee_array[$j];
                                $paidamount = $amount_array[$j];

                                $receipt_amount += $amount_array[$j];
                                $paiddiscount = $discount_array[$j];
                                $fineamount = 0;
                                $feedetails = $this->studentfeemaster_model->getfeedetails($fee, $student_session_id);

                                if (!empty($feedetails)) {
                                    $res['student_fees_master_id'] = $feedetails['fee_master_id'];
                                    $res['fee_groups_feetype_id'] = $feedetails['fee_group_feetype_id'];

                                    if ($paidamount <= $feedetails['fee_amount'] && $paiddiscount <= $feedetails['fee_amount']) {
                                        $selected_fee_types[] = $feedetails['feestype'];


                                        if ($res['fee_category'] == "fees") {

                                            $res['amount_detail'] = array(
                                                'amount' => $paidamount,
                                                'date' => $fee_date,
                                                'description' => $description,
                                                'amount_discount' => 0,
                                                'collected_by' => $collected_by,
                                                'amount_fine' => $fineamount,
                                                'payment_mode' => $payment_mode,
                                                'received_by'     => $staff_record['id'],

                                            );
                                        } else if ($res['fee_category'] == "discount") {
                                            $res['amount_detail'] = array(
                                                'amount' => 0,
                                                'date' => $fee_date,
                                                'description' => $description,
                                                'amount_discount' => $paiddiscount,
                                                'collected_by' => $collected_by,
                                                'amount_fine' => $fineamount,
                                                'payment_mode' => $payment_mode,
                                                'received_by'     => $staff_record['id'],

                                            );
                                        }

                                        $fees_data[] = $res;
                                        $selected_fee_types_string = implode(',', $selected_fee_types);
                                    }
                                }
                            }

                            // echo "<pre>";
                            // print_r($fees_data); echo "<br>";



                            // print_r($student_print);

                            if (!empty($fees_data)) {

                                if ($fee_category == "fees") {

                                    // echo $table_name;exit;

                                    // New Fee Receipts Table ()
                                    $deposited_fees = $this->studentfeemaster_model->fee_deposit_collections($fees_data);

                                    $student_print = array(
                                        'student_session_id' => $student_session_id,
                                        'amount' => $receipt_amount,
                                        'fee_types' => $selected_fee_types_string,
                                        'collected_by' => $collected_by,
                                        'mode' => $payment_mode,
                                        'created_at' => $fee_date
                                    );



                                    $this->db->insert('student_fees_print_24', $student_print);
                                } else if ($fee_category == "discount") {
                                    $deposited_fees = $this->studentfeemaster_model->fee_discount_payments($fees_data);
                                }



                                $data['csvData'] = $result;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('students_imported_successfully') . '</div>');

                                $rowcount++;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');
                            }
                        }
                        // exit;
                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
                    }
                } else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
                }
            }

            redirect('studentfee/index');
        }
    }

    // new 

    public function importpayments()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if ($ext == 'csv') {
                $file = $_FILES['file']['tmp_name'];
                $this->load->library('CSVReader');
                $result = $this->csvreader->parse_file($file);


                // echo "<pre>";print_r($result);exit;

                if (!empty($result)) {
                    $rowcount = 0;
                    for ($i = 1; $i <= count($result); $i++) {
                        $student_data[$i] = array();
                        $fees = [];

                        foreach ($result[$i] as $key => $value) {
                            $nofeeTypes = ['admission_no', 'date', 'payment_mode', 'collected_by', 'description', 'class', 'fee_category', 'reference_no'];
                            if (!in_array($key, $nofeeTypes) && !in_array($key, $fees)) {
                                if ($value != 0) {
                                    array_push($fees, array($key => $value));
                                }
                            }

                            $datetime = DateTime::createFromFormat('m/d/Y', $result[$i]['date']);
                            $formatted_date = ($datetime !== false) ? $datetime->format('Y-m-d') : $result[$i]['date'];

                            $student_data[$i]['admission_no']  = $result[$i]['admission_no'];
                            $student_data[$i]['date']          = $formatted_date;
                            $student_data[$i]['payment_mode']  = $result[$i]['payment_mode'];
                            $student_data[$i]['collected_by']  = $result[$i]['collected_by'];
                            $student_data[$i]['description']   = $result[$i]['description'];
                            $student_data[$i]['reference_no']   = $result[$i]['reference_no'];
                            $student_data[$i]['fee_category']   = $result[$i]['fee_category'] ?: 'fees';
                        }

                        $resultdata = [];
                        foreach ($fees as $fee) {
                            foreach ($fee as $feeType => $amount) {
                                $resultdata[$feeType] = $amount;
                            }
                        }

                        $student_details = $this->student_model->findByAdmission($student_data[$i]["admission_no"]);
                        if (empty($student_details)) continue;

                        $student_session_id = $student_details->student_session_id;
                        $fee_array          = $resultdata;
                        $staff_id           = $this->customlib->getStaffID() ?: 1;
                        $staff_record       = $this->staff_model->get($staff_id);
                        $fee_category       = $student_data[$i]['fee_category'] == 'discount' ? 'discount' : 'fees';
                        $fee_date           = $student_data[$i]['date'];
                        $description        = $student_data[$i]['description'];
                        $payment_mode       = $student_data[$i]['payment_mode'];
                        $collected_by       = $student_data[$i]['collected_by'];
                        $reference_no       = $student_data[$i]['reference_no'];

                        $fees_data          = array();
                        $selected_fee_types = array();
                        $receipt_amount     = 0;

                        foreach ($fee_array as $key => $value) {
                            $res = array('fee_category' => $fee_category, 'student_transport_fee_id' => 0);
                            $fee = $key;

                            $paidamount = ($fee_category == 'fees') ? $value : 0;
                            $paiddiscount = ($fee_category == 'discount') ? $value : 0;
                            if ($fee_category == 'fees') $receipt_amount += $value;

                            $feedetails = $this->studentfeemaster_model->getfeedetails($fee, $student_session_id);

                            if (!empty($feedetails)) {
                                $res['student_fees_master_id'] = $feedetails['fee_master_id'];
                                $res['fee_groups_feetype_id']  = $feedetails['fee_group_feetype_id'];

                                if ($paidamount <= $feedetails['fee_amount'] || $feedetails['feestype'] == "Previous Session Balance") {
                                    $selected_fee_types[] = $feedetails['feestype'];
                                    $res['amount_detail'] = array(
                                        'amount'          => $paidamount,
                                        'date'            => $fee_date,
                                        'description'     => $description,
                                        'amount_discount' => $paiddiscount,
                                        'collected_by'    => $collected_by,
                                        'amount_fine'     => 0,
                                        'payment_mode'    => $payment_mode,
                                        'received_by'     => $staff_id,
                                    );
                                    $fees_data[] = $res;
                                }
                            }
                        }



                        if (!empty($fees_data)) {
                            if ($fee_category == "fees") {

                                // echo "<pre>";print_r($fees_data);
                                 $deposited_fees = $this->studentfeemaster_model->fee_deposit_collections($fees_data);
                                // $academic_session = $this->customlib->getCurrentSession();
                                // $start_year = (!empty($academic_session)) ? $this->customlib->getAcademicSession($academic_session['session'])['start_year'] : "24";



                                $session = $this->sch_setting_detail->session;

                               
                                $start_year = explode('-', $session)[1] -1;

                                // echo $start_year; echo "<br>";

                              
                                // echo 'student_fees_print_' . $start_year;



                                $student_print = array(
                                    'student_session_id' => $student_session_id,
                                    'amount'             => $receipt_amount,
                                    'fee_types'          => implode(',', $selected_fee_types),
                                    'collected_by'       => $collected_by,
                                    'mode'               => $payment_mode,
                                    'created_at'         => $fee_date,
                                    'reference_no'       => $reference_no
                                );

                                // echo "<pre>";print_r($student_print);
                                 $this->db->insert('student_fees_print_' . $start_year, $student_print);
                            } else {
                                $this->studentfeemaster_model->fee_discount_payments($fees_data);
                            }
                            $rowcount++;
                        }

                        // exit;

                        
                    }
                    return $this->output->set_status_header(200)->set_output(json_encode(['status' => 'success', 'imported' => $rowcount]));
                } else {
                    return $this->output->set_status_header(200)->set_output(json_encode(['status' => 'error', 'message' => 'No record found']));
                }
            } else {
                return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Invalid file extension']));
            }
        }
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'No file uploaded']));
    }

    public function handle_csv_upload()
    {
        $error = "";
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('csv');
            $mimes       = array(
                'text/csv',
                'text/plain',
                'application/csv',
                'text/comma-separated-values',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'text/anytext',
                'application/octet-stream',
                'application/txt'
            );
            $temp      = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if (!in_array($_FILES['file']['type'], $mimes)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            $this->form_validation->set_message('handle_csv_upload', $this->lang->line('please_select_file'));
            return false;
        }
    }


    public function assignDiscountFee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $staff_id = $this->customlib->getStaffID() ?: 1;
            $staff_record = $this->staff_model->get($staff_id);
            $role_array     = $this->customlib->getStaffRole();
            $role           = json_decode($role_array);
            $staff_role     = $role->name ?? 'Admin';
            $status = ($staff_role == 'Super Admin') ? 1 : 0;

            $collected_by             = ($this->customlib->getAdminSessionUserName() ?: 'Admin') . "(" . ($staff_record['employee_id'] ?? '1') . ")";
            $student_fees_discount_id = $input['student_fees_discount_id'] ?? null;
            $json_array               = array(
                'amount'          => 0,
                'amount_discount' => convertCurrencyFormatToBaseAmount($input['amount_discount']),
                'amount_fine'     => 0,
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($input['date'])),
                'description'     => '',
                'collected_by'    => $collected_by,
                'payment_mode'    => 'discount',
                'received_by'     => $staff_id,
                'status'          => $status,
                'discount_type'   => $input['discount_type'] ?? ''
            );

            $data = array(
                'fee_category'           => $input['fee_category'],
                'student_fees_master_id' => $input['student_fees_master_id'],
                'fee_groups_feetype_id'  => $input['fee_groups_feetype_id'],
                'amount_detail'          => $json_array,
            );

            if (($input['transport_fees_id'] ?? 0) != 0 && $input['fee_category'] == "transport") {
                $data['student_fees_master_id']   = null;
                $data['fee_groups_feetype_id']    = null;
                $data['student_transport_fee_id'] = $input['transport_fees_id'];
            }

            $this->studentfeemaster_model->fee_deposit($data, null, $student_fees_discount_id);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 'success', 'message' => 'Discount assigned successfully']));
        }
    }

    public function approveDiscountFee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $data = array(
                'student_fees_master_id' => $input['student_fees_master_id'],
                'fee_groups_feetype_id'  => $input['fee_groups_feetype_id'],
                'id'                     => $input['student_fees_deposite_id'],
            );

            $res = $this->studentfeemaster_model->updateDiscountStatus($data, $input['invoice_id']);

            if ($res) {
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode(['status' => 'success', 'message' => 'Discount Approved Successfully']));
            } else {
                return $this->output
                    ->set_status_header(400)
                    ->set_output(json_encode(['status' => 'fail', 'message' => 'Something went wrong']));
            }
        }
    }

    public function assignstudentdiscountfee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $staff_id = $this->customlib->getStaffID() ?: 1;
            $staff_record = $this->staff_model->get($staff_id);
            $collected_by = ($this->customlib->getAdminSessionUserName() ?: 'Admin') . "(" . ($staff_record['employee_id'] ?? '1') . ")";

            $json_array = array(
                'amount'          => 0,
                'amount_discount' => convertCurrencyFormatToBaseAmount($input['amount_discount']),
                'amount_fine'     => 0,
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($input['date'])),
                'description'     => $input['description'] ?? '',
                'collected_by'    => $collected_by,
                'payment_mode'    => $input['payment_mode'],
                'received_by'     => $staff_id,
                'discount_type'   => $input['discount_type'] ?? '',
            );

            $role_array     = $this->customlib->getStaffRole();
            $role           = json_decode($role_array);
            $staff_role     = $role->name ?? 'Admin';
            $paymode        = $input['payment_mode'];

            $data = array(
                'fee_category'           => $input['fee_category'],
                'student_fees_master_id' => $input['student_fees_master_id'],
                'fee_groups_feetype_id'  => $input['fee_groups_feetype_id'],
                'amount_detail'          => $json_array,
            );

            if (($input['transport_fees_id'] ?? 0) != 0 && $input['fee_category'] == "transport") {
                $data['student_fees_master_id']   = null;
                $data['fee_groups_feetype_id']    = null;
                $data['student_transport_fee_id'] = $input['transport_fees_id'];
            }

            if ($staff_role == 'Super Admin' && $paymode == "discount") {
                $this->studentfeemaster_model->fee_deposit($data, null, $input['student_fees_discount_id'] ?? null);
            } else {
                $this->studentfeemaster_model->fee_discount_approve($data, null, $input['student_fees_discount_id'] ?? null);
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 'success', 'message' => $this->lang->line('success_message')]));
        }
    }

    public function deleteDiscountFee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $invoice_id  = $input['main_invoice'] ?? null;
        $sub_invoice = $input['sub_invoice'] ?? null;
        if (!empty($invoice_id)) {
            $this->studentfee_model->discountremove($invoice_id, $sub_invoice);
        }
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['status' => 'success', 'result' => 'success']));
    }
}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentfee extends Admin_Controller
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
        $this->load->library('encoding_lib');
        $this->search_type        = $this->config->item('search_type');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', $this->lang->line('fees_collection'));
        $this->session->set_userdata('sub_menu', 'studentfee/index');
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;

        $start_date         = date('Y-m-d');
        $end_date         = date('Y-m-d');
        $fee_Data = $this->feesreceipt_model->getTransctionData($start_date, $end_date);
        $data['fees_data'] = $fee_Data;

        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/studentfeeSearch', $data);
        $this->load->view('layout/footer', $data);
    }



    public function pdf()
    {
        $this->load->helper('pdf_helper');
    }

    public function search()
    {
        $search_type = $this->input->post('search_type');
        if ($search_type == "class_search") {
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');
        } elseif ($search_type == "keyword_search") {
            $this->form_validation->set_rules('search_text', $this->lang->line('keyword'), 'required|trim|xss_clean');
            $data = array('search_text' => 'dummy');
            $this->form_validation->set_data($data);
        }
        if ($this->form_validation->run() == false) {
            $error = array();
            if ($search_type == "class_search") {
                $error['class_id'] = form_error('class_id');
            } elseif ($search_type == "keyword_search") {
                $error['search_text'] = form_error('search_text');
            }

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {
            $search_type = $this->input->post('search_type');
            $search_text = $this->input->post('search_text');
            $class_id    = $this->input->post('class_id');
            $section_id  = $this->input->post('section_id');
            $params      = array('class_id' => $class_id, 'section_id' => $section_id, 'search_type' => $search_type, 'search_text' => $search_text);
            $array       = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    public function ajaxSearch()
    {
        $class       = $this->input->post('class_id');
        $section     = $this->input->post('section_id');
        $search_text = $this->input->post('search_text');
        $search_type = $this->input->post('search_type');
        if ($search_type == "class_search") {
            $students = $this->student_model->getDatatableByClassSection($class, $section);
        } elseif ($search_type == "keyword_search") {
            $students = $this->student_model->getDatatableByFullTextSearch($search_text);
        }
        $sch_setting = $this->sch_setting_detail;
        $students    = json_decode($students);
        $dt_data     = array();
        if (!empty($students->data)) {
            foreach ($students->data as $student_key => $student) {
                $row         = array();
                $row[]       = $student->class;
                $row[]       = $student->section;
                $row[]       = $student->admission_no;
                $row[]       = "<a href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                $sch_setting = $this->sch_setting_detail;
                if ($sch_setting->father_name) {
                    $row[] = $student->father_name;
                }
                $row[] = $this->customlib->dateformat($student->dob);
                // $row[] = $student->guardian_phone;
                $row[]       = $student->father_phone;
                $row[] = "<a href=" . site_url('studentfee/addfee/' . $student->student_session_id) . "  class='btn btn-info btn-xs'>" . $this->lang->line('collect_fees') . "</a>";

                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($students->draw),
            "recordsTotal"    => intval($students->recordsTotal),
            "recordsFiltered" => intval($students->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function feesearch()
    {
        if (!$this->rbac->hasPrivilege('search_due_fees', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'studentfee/feesearch');
        $data['title']       = $this->lang->line('student_fees');
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        $feesessiongroup     = $this->feesessiongroup_model->getFeesByGroup();

        $feeTypes  = $this->feetype_model->get();
        $data['feeTypes'] = $feeTypes;
        $module = $this->module_model->getPermissionByModulename('transport');

        $currentsessiontransportfee = $this->transportfee_model->getSessionFees($this->current_session);
        if (!empty($currentsessiontransportfee)) {
            if ($module['is_active']) {
                $month_list = $this->customlib->getMonthDropdown($this->sch_setting_detail->start_month);
                foreach ($month_list as $key => $value) {
                    $transportfesstype[] = $this->transportfee_model->transportfesstype($this->current_session, $value);
                }
                $feesessiongroup[count($feesessiongroup)] = (object)array('id' => 'Transport', 'group_name' => 'Transport Fees', 'is_system' => 0, 'feetypes' => $transportfesstype);
            }
        }

        $feetypeids = array();



        $data['feesessiongrouplist'] = $feesessiongroup;
        $data['fees_group']          = "";
        if (isset($_POST['feegroup_id']) && $_POST['feegroup_id'] != '') {
            $data['fees_group'] = $_POST['feegroup_id'];
        }

        if (isset($_POST['select_all']) && $_POST['select_all'] != '') {
            $data['select_all'] = $_POST['select_all'];
        }

        $this->form_validation->set_rules('feegroup[]', $this->lang->line('fee_group'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/studentSearchFee', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $feegroups = $this->input->post('feegroup');

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

            $data['student_due_fee'] = array();

            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

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

            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/studentSearchFee', $data);
            $this->load->view('layout/footer', $data);
        }
    }



    public function reportbyclass()
    {
        $data['title']     = 'student fees';
        $data['title']     = 'student fees';
        $class             = $this->class_model->get();
        $data['classlist'] = $class;
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/reportByClass', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $student_fees_array      = array();
            $class_id                = $this->input->post('class_id');
            $section_id              = $this->input->post('section_id');
            $student_result          = $this->student_model->searchByClassSection($class_id, $section_id);
            $data['student_due_fee'] = array();
            if (!empty($student_result)) {
                foreach ($student_result as $key => $student) {
                    $student_array                      = array();
                    $student_array['student_detail']    = $student;
                    $student_session_id                 = $student['student_session_id'];
                    $student_id                         = $student['id'];
                    $student_due_fee                    = $this->studentfee_model->getDueFeeBystudentSection($class_id, $section_id, $student_session_id);
                    $student_array['fee_detail']        = $student_due_fee;
                    $student_fees_array[$student['id']] = $student_array;
                }
            }
            $data['class_id']           = $class_id;
            $data['section_id']         = $section_id;
            $data['student_fees_array'] = $student_fees_array;
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/reportByClass', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }
        $data['title']      = 'studentfee List';
        $studentfee         = $this->studentfee_model->get($id);
        $data['studentfee'] = $studentfee;
        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/studentfeeShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function deleteFee()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_delete')) {
            access_denied();
        }
        $invoice_id  = $this->input->post('main_invoice');
        $sub_invoice = $this->input->post('sub_invoice');
        if (!empty($invoice_id)) {
            $this->studentfee_model->remove($invoice_id, $sub_invoice);
        }
        $array = array('status' => 'success', 'result' => 'success');
        echo json_encode($array);
    }

    public function deleteStudentDiscount()
    {
        $discount_id = $this->input->post('discount_id');
        if (!empty($discount_id)) {
            $data = array('id' => $discount_id, 'status' => 'assigned', 'payment_id' => "");
            $this->feediscount_model->updateStudentDiscount($data);
        }
        $array = array('status' => 'success', 'result' => 'success');
        echo json_encode($array);
    }

    public function getcollectfee()
    {
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $record              = $this->input->post('data');
        $record_array        = json_decode($record);

        $fees_array = array();
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

                if ($value->totalbalanceAmount) {
                    $feeList->totalbalanceAmount = $value->totalbalanceAmount;
                }

                if (isset($value->fee_remaing_amount)) {
                    $feeList->fee_remaing_amount =   $value->fee_remaing_amount;
                    $feeList->fee_reaming_id = $value->fee_groups_feetype_id;
                }
            }

            $fees_array[] = $feeList;
        }

        $data['feearray'] = $fees_array;
        $result           = array(
            'view' => $this->load->view('studentfee/getcollectfee', $data, true),
        );

        $this->output->set_output(json_encode($result));
    }


    public function update_invoice()
    {

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $receipt_id = $this->input->post('receipt_id');
            $status = $this->input->post('status');
            $description = $this->input->post('description');

            // Update invoice via model
            $update_data = array(
                'status' => $status,
                'description' => $description,
                'cancel_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('cancelled_date'))),
            );

            $updated = $this->feesreceipt_model->update_invoice($receipt_id, $update_data);

            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Invoice updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update invoice']);
            }
        }
    }


    public function getchallanfee()
    {
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $id                  = $this->input->post('student_id');



        $due_dates             = $this->studentfeemaster_model->getstudentduedates($id);


        $data['student_duedates']       = $due_dates;

        $data['student_session_id'] = $this->input->post('student_id');

        $due_date = $this->input->post('due_date');
        $data['bankdetails'] = $this->input->post('bankdetails');


        $bank_data          = $this->bank_model->get();
        $data['bank_data']  = $bank_data;

        $resultlist = $this->studentfeemaster_model->getStudentFeesbyduedate($data['student_session_id'], $due_date);


        // 		if (!empty($from_date) && !empty($to_date)) {
        //         foreach ($resultlist as &$student) {
        //             $student_session_id = $student['student_session_id'];
        //             $attendance = $this->cbseexam_exam_model->get_student_attendance($student_session_id, $from_date, $to_date);
        //             // $lateEntries  = $this->cbseexam_exam_model->get_late_entries_beetweenDate($from_date,$to_date,$student_session_id);
        //             // echo "<pre>";print_r($lateEntries);exit;
        //             $student['total_present_days'] = $attendance['total_present_days'];
        //             // $student['total_late_days'] = $lateEntries[0]['total_late_days'];
        // 			//echo '<pre>'; print_r($student);exit;
        // 			}
        // 		}

        $data['resultlist_data'] = $resultlist;

        // echo '<pre>'; print_r($resultlist);exit;
        $result           = array(
            'status' => '1',
            'error' => '',
            'view' => $this->load->view('studentfee/generatechallan', $data, true),
            'due_date' => $due_date,
        );

        $this->output->set_output(json_encode($result));
    }


    public function save_challan_fees()
    {
        $student_id  = $this->input->post('student_id');
        $amount      = $this->input->post('amount');
        $fee_groups  = $this->input->post('fee_groups');
        $fee_types   = $this->input->post('fee_types');
        $generated_by = $this->input->post('generated_by');
        $due_date    = $this->input->post('due_date');
        $date        = $this->input->post('date');
        $bank        = $this->input->post('bank');

        if (!empty($student_id) && !empty($fee_groups) && !empty($fee_types)) {
            $data = array(
                'student_session_id' => $student_id,
                'amount'             => $amount,
                'fee_groups'         => $fee_groups,  // Single fee group
                'fee_types'          => $fee_types,   // Concatenated types
                'generated_by'       => $generated_by,
                'due_date'           => $due_date,
                'date'               => $date,
                'bank'               => $bank
            );

            $this->db->insert('student_challan', $data);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }



    public function getcollectfee1()
    {
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $record              = $this->input->post('data');
        $record_array        = json_decode($record);



        $fees_array = array();
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
                if (isset($value->fee_remaing_amount)) {
                    $feeList->fee_remaing_amount =   $value->fee_remaing_amount;
                    $feeList->fee_reaming_id = $value->fee_groups_feetype_id;
                }
            }

            $fees_array[] = $feeList;
        }



        $data['feearray'] = $fees_array;
        $result           = array(
            'view' => $this->load->view('studentfee/getGropcollectfee', $data, true),
        );

        $this->output->set_output(json_encode($result));
    }


    public function addfee($id)
    {

        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $data['sch_setting']   = $this->sch_setting_detail;
        $data['title']         = 'Student Detail';
        // $student               = $this->student_model->getByStudentSession($id);
        $student               = $this->student_model->getByStudentSessionDetails($id);



        $get_academics = $this->student_model->getstudentAcademics($student['id']);

        $data['student_academics'] = $get_academics;
        $route_pickup_point_id = $student['route_pickup_point_id'];
        $student_session_id    = $student['student_session_id'];
        $transport_fees = [];

        $module = $this->module_model->getPermissionByModulename('transport');
        if ($module['is_active']) {

            $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);
        }



        $data['student']       = $student;

        $admission_no = $student['admission_no'];

        $studentData = $this->student_model->getOldStudentDataByAdmissionNo($admission_no);

        if (!empty($studentData)) {

            $oldStudentSession = $studentData->student_session_id;

            $student_old_due_fee  = $this->studentfeemaster_model->getStudentFees($oldStudentSession);

            $total_amount = 0;
            $total_deposite_amount = 0;
            $total_discount_amount = 0;
            $total_fine_amount = 0;
            $total_balance_amount = 0;
            $total_fees_fine_amount  = 0;

            foreach ($student_old_due_fee as $key => $fee) {



                foreach ($fee->fees as $fee_key => $fee_value) {
                    $fee_paid         = 0;
                    $fee_discount     = 0;
                    $fee_fine         = 0;
                    $fees_fine_amount = 0;
                    $feetype_balance  = 0;
                    if (!empty($fee_value->amount_detail)) {
                        $fee_deposits = json_decode(($fee_value->amount_detail));

                        foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                            $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                            $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                            $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                        }
                    }
                    if (($fee_value->due_date != "0000-00-00" && $fee_value->due_date != null) && (strtotime($fee_value->due_date) < strtotime(date('Y-m-d')))) {
                        $fees_fine_amount       = $fee_value->fine_amount;
                        $total_fees_fine_amount = $total_fees_fine_amount + $fee_value->fine_amount;
                    }

                    $total_amount += $fee_value->amount;
                    $total_discount_amount += $fee_discount;
                    $total_deposite_amount += $fee_paid;
                    $total_fine_amount += $fee_fine;
                    $feetype_balance = $fee_value->amount - ($fee_paid + $fee_discount);
                    $total_balance_amount += $feetype_balance;
                }
            }

            // echo "<pre>";
            // print_r($student_old_due_fee);exit;

            $oldFee = array('total_balence_amount' => $total_balance_amount, 'total_amount' => $total_amount, 'total_paid_balence' => $total_deposite_amount, 'total_fee_discount' => $total_discount_amount);

            $data['oldFee'] = $oldFee;
        }

        // echo "<pre>";
        // print_r($data['oldFee']);exit;
        $student_due_fee       = $this->studentfeemaster_model->getStudentFees($id);
        $student_discount_fee  = $this->feediscount_model->getStudentFeesDiscount($id);

        $data['transport_fees']         = $transport_fees;
        $data['student_discount_fee']   = $student_discount_fee;
        $data['student_due_fee']        = $student_due_fee;
        $category                       = $this->category_model->get();
        $data['categorylist']           = $category;
        $class_section                  = $this->student_model->getClassSection($student["class_id"]);
        $data["class_section"]          = $class_section;
        $session                        = $this->setting_model->getCurrentSession();
        $studentlistbysection           = $this->student_model->getStudentClassSection($student["class_id"], $session);
        $data["studentlistbysection"]   = $studentlistbysection;
        $student_processing_fee         = $this->studentfeemaster_model->getStudentProcessingFees($id);
        $data['student_processing_fee'] = false;

        foreach ($student_processing_fee as $key => $processing_value) {
            if (!empty($processing_value->fees)) {
                $data['student_processing_fee'] = true;
            }
        }

        $fee_payments             = $this->feesreceipt_model->get24feesBySudent_session_id($student_session_id);
        $data['fee_payments']     = $fee_payments;


        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/studentAddfee', $data);
        $this->load->view('layout/footer', $data);
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
        if (!$this->rbac->hasPrivilege('search_fees_payment', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'studentfee/searchpayment');
        $data['title'] = $this->lang->line('fees_collection');

        $this->form_validation->set_rules('paymentid', $this->lang->line('payment_id'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
        } else {
            $paymentid = $this->input->post('paymentid');
            $invoice   = explode("/", $paymentid);

            if (array_key_exists(0, $invoice) && array_key_exists(1, $invoice)) {
                $invoice_id             = $invoice[0];
                $sub_invoice_id         = $invoice[1];
                $feeList                = $this->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
                $data['feeList']        = $feeList;
                $data['sub_invoice_id'] = $sub_invoice_id;
            } else {
                $data['feeList'] = array();
            }
        }
        $data['sch_setting'] = $this->sch_setting_detail;

        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/searchpayment', $data);
        $this->load->view('layout/footer', $data);
    }

    public function addfeegroup()
    {
        $this->form_validation->set_rules('fee_session_groups', $this->lang->line('fee_group'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'fee_session_groups' => form_error('fee_session_groups'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $student_session_id     = $this->input->post('student_session_id');
            $fee_session_groups     = $this->input->post('fee_session_groups');
            $student_sesssion_array = isset($student_session_id) ? $student_session_id : array();
            $student_ids            = $this->input->post('student_ids');
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

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function geBalanceFee()
    {
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('fee_groups_feetype_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('student_fees_master_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_session_id', $this->lang->line('student_session_id'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'student_session_id'     => form_error('student_session_id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $data                 = array();
            $student_session_id   = $this->input->post('student_session_id');
            $discount_not_applied = $this->getNotAppliedDiscount($student_session_id);

            $fee_category = $this->input->post('fee_category');
            if ($fee_category == "transport") {
                $trans_fee_id         = $this->input->post('trans_fee_id');
                $remain_amount_object = $this->getStudentTransportFeetypeBalance($trans_fee_id);
                $remain_amount        = (float) json_decode($remain_amount_object)->balance;
                $remain_amount_fine   = json_decode($remain_amount_object)->fine_amount;
            } else {
                $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
                $student_fees_master_id = $this->input->post('student_fees_master_id');
                $remain_amount_object   = $this->getStuFeetypeBalance($fee_groups_feetype_id, $student_fees_master_id);
                $remain_amount          = json_decode($remain_amount_object)->balance;
                $remain_amount_fine     = json_decode($remain_amount_object)->fine_amount;
            }

            $remain_amount = number_format($remain_amount, 2, ".", "");

            $array = array('status' => 'success', 'error' => '', 'balance' => convertBaseAmountCurrencyFormat($remain_amount), 'discount_not_applied' => $discount_not_applied, 'remain_amount_fine' => convertBaseAmountCurrencyFormat($remain_amount_fine), 'student_fees' => convertBaseAmountCurrencyFormat(json_decode($remain_amount_object)->student_fees));
            echo json_encode($array);
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
        $staff_record = $this->staff_model->get($this->customlib->getStaffID());
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('row_counter[]', $this->lang->line('fees_list'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('collected_date', $this->lang->line('date'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'row_counter'    => form_error('row_counter'),
                'collected_date' => form_error('collected_date'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            $collected_array = array();
            $staff_record    = $this->staff_model->get($this->customlib->getStaffID());
            $collected_by    = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";

            $send_to            = $this->input->post('guardian_phone');
            $email              = $this->input->post('guardian_email');
            $parent_app_key     = $this->input->post('parent_app_key');
            $student_session_id = $this->input->post('student_session_id');
            $totalbalanceAmount = $this->input->post('totalbalanceAmount');
            $student = $this->student_model->getByStudentSession($student_session_id);
            $total_row          = $this->input->post('row_counter');


            foreach ($total_row as $total_row_key => $total_row_value) {


                $fee_category             = $this->input->post('fee_category_' . $total_row_value);
                $student_transport_fee_id = $this->input->post('trans_fee_id_' . $total_row_value);

                $json_array = array(
                    'amount'          => $this->input->post('fee_amount_' . $total_row_value),
                    'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('collected_date'))),
                    'description'     => $this->input->post('fee_gupcollected_note'),
                    'amount_discount' => 0,
                    'collected_by'    => $collected_by,
                    'amount_fine'     => $this->input->post('fee_groups_feetype_fine_amount_' . $total_row_value),
                    'payment_mode'    => $this->input->post('payment_mode_fee'),
                    'received_by'     => $staff_record['id'],
                );
                $collected_array[] = array(
                    'fee_category'             => $fee_category,
                    'student_transport_fee_id' => $student_transport_fee_id,
                    'student_fees_master_id'   => $this->input->post('student_fees_master_id_' . $total_row_value),
                    'fee_groups_feetype_id'    => $this->input->post('fee_groups_feetype_id_' . $total_row_value),
                    'amount_detail'            => $json_array,
                );
            }

            $deposited_fees = $this->studentfeemaster_model->fee_deposit_collections($collected_array);

            $t = $this->input->post('total_amount');
            $balance = (int) $totalbalanceAmount - (int)$t;

            $student_print = array(
                'student_session_id'    => $student_session_id,
                'amount'                => $this->input->post('total_amount'),
                'fee_types'             => $this->input->post('fee_type'),
                'collected_by'          => $collected_by,
                'mode'    => $this->input->post('payment_mode_fee'),
                'reference_no'   => $this->input->post('reference_no'),
                'balanceAmount' => $balance,
                'created_at'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('collected_date'))),
                'status'                => 0




            );

            // print_r($student_print);exit;




            $academic_session = $this->customlib->getCurrentSession();

            if (!empty($academic_session)) {
                $asession = $this->customlib->getAcademicSession($academic_session['session']);
                // echo "<pre>";
                // print_r($asession);exit;
                $end_year = $asession['end_year'];
                $start_year = $asession['start_year'];
            } else {


                $start_year = "24";
                $end_year = "24";
            }

            $table_name = 'student_fees_print_' . $start_year;

            // echo $table_name;exit;



            // New Fee Receipts Table ()
            $this->db->insert($table_name, $student_print);


            if ($deposited_fees && is_array($deposited_fees)) {
                foreach ($deposited_fees as $deposited_fees_key => $deposited_fees_value) {
                    $fee_category = $deposited_fees_value['fee_category'];
                    $invoice[]   = array(
                        'invoice_id'     => $deposited_fees_value['invoice_id'],
                        'sub_invoice_id' => $deposited_fees_value['sub_invoice_id'],
                        'fee_category' => $fee_category,
                    );


                    if ($deposited_fees_value['student_transport_fee_id'] != 0 && $deposited_fees_value['fee_category'] == "transport") {

                        $data['student_fees_master_id']   = null;
                        $data['fee_groups_feetype_id']    = null;
                        $data['student_transport_fee_id'] = $deposited_fees_value['student_transport_fee_id'];

                        $mailsms_array     = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($deposited_fees_value['student_transport_fee_id']);
                        $fee_group_name[]  = $this->lang->line("transport_fees");
                        $type[]            = $mailsms_array->month;
                        $code[]            = "-";
                        $fine_type[]       = $mailsms_array->fine_type;
                        $due_date[]        = $mailsms_array->due_date;
                        $fine_percentage[] = $mailsms_array->fine_percentage;
                        $fine_amount[]     = amountFormat($mailsms_array->fine_amount);
                        $amount[]          = amountFormat($mailsms_array->amount);
                    } else {

                        $mailsms_array = $this->feegrouptype_model->getFeeGroupByIDAndStudentSessionID($deposited_fees_value['fee_groups_feetype_id'], $student_session_id);

                        $fee_group_name[]  = $mailsms_array->fee_group_name;
                        $type[]            = $mailsms_array->type;
                        $code[]            = $mailsms_array->code;
                        $fine_type[]       = $mailsms_array->fine_type;
                        $due_date[]        = $mailsms_array->due_date;
                        $fine_percentage[] = $mailsms_array->fine_percentage;
                        $fine_amount[]     = amountFormat($mailsms_array->fine_amount);

                        if ($mailsms_array->is_system) {
                            $amount[] = amountFormat($mailsms_array->balance_fee_master_amount);
                        } else {
                            $amount[] = amountFormat($mailsms_array->amount);
                        }
                    }
                }
                $obj_mail                     = [];
                $obj_mail['student_id']  = $student['id'];
                $obj_mail['student_session_id'] = $student_session_id;

                $obj_mail['invoice']         = $invoice;
                $obj_mail['contact_no']      = $student['guardian_phone'];
                $obj_mail['email']           = $student['email'];
                $obj_mail['parent_app_key']  = $student['parent_app_key'];
                // $obj_mail['amount']          = "(" . implode(',', $amount) . ")";
                $obj_mail['amount']          = $t;
                $obj_mail['fine_type']       = "(" . implode(',', $fine_type) . ")";
                $obj_mail['due_date']        = "(" . implode(',', $due_date) . ")";
                $obj_mail['fine_percentage'] = "(" . implode(',', $fine_percentage) . ")";
                $obj_mail['fine_amount']     = "(" . implode(',', $fine_amount) . ")";
                $obj_mail['fee_group_name']  = "(" . implode(',', $fee_group_name) . ")";
                $obj_mail['type']            = "(" . implode(',', $type) . ")";
                $obj_mail['code']            = "(" . implode(',', $code) . ")";
                $obj_mail['fee_category']    = $fee_category;
                $obj_mail['send_type']    = 'group';

                $this->mailsmsconf->mailsms('fee_submission', $obj_mail);
            }


            $array = array('status' => 1, 'error' => '');
            echo json_encode($array);
        }
    }


    public function addstudentchallan()
    {
        $staff_record = $this->staff_model->get($this->customlib->getStaffID());
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('row_counter[]', $this->lang->line('fees_list'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('collected_date', $this->lang->line('date'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'row_counter'    => form_error('row_counter'),
                'collected_date' => form_error('collected_date'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            $collected_array = array();
            $staff_record    = $this->staff_model->get($this->customlib->getStaffID());
            $collected_by    = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";

            $send_to            = $this->input->post('guardian_phone');
            $email              = $this->input->post('guardian_email');
            $parent_app_key     = $this->input->post('parent_app_key');
            $student_session_id = $this->input->post('student_session_id');
            $totalbalanceAmount = $this->input->post('totalbalanceAmount');
            $student = $this->student_model->getByStudentSession($student_session_id);
            $total_row          = $this->input->post('row_counter');




            $t = $this->input->post('total_amount');
            $balance = (int) $totalbalanceAmount - (int)$t;

            $student_print = array(
                'student_session_id'    => $student_session_id,
                'amount'                => $this->input->post('total_amount'),
                'fee_types'             => $this->input->post('fee_type'),
                'generated_by'          => $collected_by,
                'date'                  => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('collected_date'))),
                'bank'                  => $this->input->post('bankdetails'),




            );


            $this->db->insert('student_challan', $student_print);





            $array = array('status' => 1, 'error' => '');
            echo json_encode($array);
        }
    }


    public function addfeegrp1()
    {
        $staff_record = $this->staff_model->get($this->customlib->getStaffID());
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('row_counter[]', $this->lang->line('fees_list'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('collected_date', $this->lang->line('date'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'row_counter'    => form_error('row_counter'),
                'collected_date' => form_error('collected_date'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            $collected_array = array();
            $staff_record    = $this->staff_model->get($this->customlib->getStaffID());
            $collected_by    = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";

            $send_to            = $this->input->post('guardian_phone');
            $email              = $this->input->post('guardian_email');
            $parent_app_key     = $this->input->post('parent_app_key');
            $student_session_id = $this->input->post('student_session_id');
            $student = $this->student_model->getByStudentSession($student_session_id);
            $total_row          = $this->input->post('row_counter');
            foreach ($total_row as $total_row_key => $total_row_value) {

                $fee_category             = $this->input->post('fee_category_' . $total_row_value);
                $student_transport_fee_id = $this->input->post('trans_fee_id_' . $total_row_value);

                $json_array = array(
                    'amount'          => 0,
                    'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('collected_date'))),
                    'description'     => $this->input->post('fee_gupcollected_note'),
                    'amount_discount' => $this->input->post('fee_amount_' . $total_row_value),
                    'collected_by'    => $collected_by,
                    'amount_fine'     => $this->input->post('fee_groups_feetype_fine_amount_' . $total_row_value),
                    'payment_mode'    => $this->input->post('payment_mode_fee'),
                    'received_by'     => $staff_record['id'],
                );
                $collected_array[] = array(
                    'fee_category'             => $fee_category,
                    'student_transport_fee_id' => $student_transport_fee_id,
                    'student_fees_master_id'   => $this->input->post('student_fees_master_id_' . $total_row_value),
                    'fee_groups_feetype_id'    => $this->input->post('fee_groups_feetype_id_' . $total_row_value),
                    'amount_detail'            => $json_array,
                );
            }

            $deposited_fees = $this->studentfeemaster_model->fee_deposit_collections($collected_array);

            // $student_print = array(
            //     'student_session_id'    => $student_session_id,
            //     'amount'                => $this->input->post('total_amount'),
            //     'fee_types'             => $this->input->post('fee_type'),
            // );

            // Fee Receipts Table (2024-25)
            // $this->db->insert('student_fees_print_24', $student_print);


            if ($deposited_fees && is_array($deposited_fees)) {
                foreach ($deposited_fees as $deposited_fees_key => $deposited_fees_value) {
                    $fee_category = $deposited_fees_value['fee_category'];
                    $invoice[]   = array(
                        'invoice_id'     => $deposited_fees_value['invoice_id'],
                        'sub_invoice_id' => $deposited_fees_value['sub_invoice_id'],
                        'fee_category' => $fee_category,
                    );


                    if ($deposited_fees_value['student_transport_fee_id'] != 0 && $deposited_fees_value['fee_category'] == "transport") {

                        $data['student_fees_master_id']   = null;
                        $data['fee_groups_feetype_id']    = null;
                        $data['student_transport_fee_id'] = $deposited_fees_value['student_transport_fee_id'];

                        $mailsms_array     = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($deposited_fees_value['student_transport_fee_id']);
                        $fee_group_name[]  = $this->lang->line("transport_fees");
                        $type[]            = $mailsms_array->month;
                        $code[]            = "-";
                        $fine_type[]       = $mailsms_array->fine_type;
                        $due_date[]        = $mailsms_array->due_date;
                        $fine_percentage[] = $mailsms_array->fine_percentage;
                        $fine_amount[]     = amountFormat($mailsms_array->fine_amount);
                        $amount[]          = amountFormat($mailsms_array->amount);
                    } else {

                        $mailsms_array = $this->feegrouptype_model->getFeeGroupByIDAndStudentSessionID($deposited_fees_value['fee_groups_feetype_id'], $student_session_id);

                        $fee_group_name[]  = $mailsms_array->fee_group_name;
                        $type[]            = $mailsms_array->type;
                        $code[]            = $mailsms_array->code;
                        $fine_type[]       = $mailsms_array->fine_type;
                        $due_date[]        = $mailsms_array->due_date;
                        $fine_percentage[] = $mailsms_array->fine_percentage;
                        $fine_amount[]     = amountFormat($mailsms_array->fine_amount);

                        if ($mailsms_array->is_system) {
                            $amount[] = amountFormat($mailsms_array->balance_fee_master_amount);
                        } else {
                            $amount[] = amountFormat($mailsms_array->amount);
                        }
                    }
                }
                $obj_mail                     = [];
                $obj_mail['student_id']  = $student['id'];
                $obj_mail['student_session_id'] = $student_session_id;

                $obj_mail['invoice']         = $invoice;
                $obj_mail['contact_no']      = $student['guardian_phone'];
                $obj_mail['email']           = $student['email'];
                $obj_mail['parent_app_key']  = $student['parent_app_key'];
                $obj_mail['amount']          = "(" . implode(',', $amount) . ")";
                $obj_mail['fine_type']       = "(" . implode(',', $fine_type) . ")";
                $obj_mail['due_date']        = "(" . implode(',', $due_date) . ")";
                $obj_mail['fine_percentage'] = "(" . implode(',', $fine_percentage) . ")";
                $obj_mail['fine_amount']     = "(" . implode(',', $fine_amount) . ")";
                $obj_mail['fee_group_name']  = "(" . implode(',', $fee_group_name) . ")";
                $obj_mail['type']            = "(" . implode(',', $type) . ")";
                $obj_mail['code']            = "(" . implode(',', $code) . ")";
                $obj_mail['fee_category']    = $fee_category;
                $obj_mail['send_type']    = 'group';


                $this->mailsmsconf->mailsms('fee_submission', $obj_mail);
            }


            $array = array('status' => 1, 'error' => '');
            echo json_encode($array);
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
        $data['title']      = 'studentfee Analysis';

        $start_date         = date('Y-m-d');
        $end_date           = date('Y-m-d');
        $fee_Data           = $this->feesreceipt_model->getTransctionData($start_date, $end_date);
        $data['fees_data']  = $fee_Data;

        $feesummary = array();
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
                                        // remove disount for balance amount
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


        $totalfeelabel = 0;
        $depositfeelabel = 0;
        $discountlabel = 0;
        $finelabel = 0;
        $balancelabel = 0;
        $commitedlabel = 0;

        if (!empty($student_Array)) {

            foreach ($student_Array as $students) {
                $totalfeelabel += $students->totalfee;
                $depositfeelabel += $students->deposit;
                $discountlabel += $students->discount;
                $finelabel += $students->fine;
                $balancelabel += $students->balance;
                $commitedlabel += $students->totalfee - $students->discount;
            }

            $feesummary['totalfee'] = $commitedlabel;
            $feesummary['deposit'] = $depositfeelabel;
            $feesummary['balance'] = $balancelabel;
        }


        $data['feesummarData'] = $feesummary;

        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/studentfeeanalysis', $data);
        $this->load->view('layout/footer', $data);
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



                    // echo "<pre>";
                    // print_r($result);exit;

                    if (!empty($result)) {
                        $rowcount = 0;
                        for ($i = 1; $i <= count($result); $i++) {



                            $student_data[$i] = array();
                            $n                = 0;
                            $fees = [];

                            foreach ($result[$i] as $key => $value) {

                                $nofeeTypes = ['admission_no', 'date', 'payment_mode', 'collected_by', 'description', 'class', 'fee_category', 'reference_no']; // List all fee types here

                                // echo "<pre>";
                                // print_r($value);


                                if (!in_array($key, $nofeeTypes) && !in_array($key, $fees)) {

                                    if ($value != 0) {
                                        array_push($fees, array($key => $value));
                                    }
                                }


                                // $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);


                                $datetime = DateTime::createFromFormat('m/d/Y', $result[$i]['date']);
                                if ($datetime !== false) {
                                    $formatted_date = $datetime->format('Y-m-d');
                                } else {
                                    // Handle the error or assign a default date value

                                    $formatted_date = $result[$i]['date']; // or another appropriate default value

                                }



                                $student_data[$i]['admission_no']  =   $result[$i]['admission_no'];
                                $student_data[$i]['date'] = $formatted_date;
                                $student_data[$i]['payment_mode']  =   $result[$i]['payment_mode'];
                                $student_data[$i]['collected_by']  =   $result[$i]['collected_by'];
                                $student_data[$i]['description']  =   $result[$i]['description'];
                                $student_data[$i]['reference_no']  =   $result[$i]['reference_no'];
                                $category =  $result[$i]['fee_category'];
                                if ($category != "") {
                                    $student_data[$i]['fee_category']  =   $result[$i]['fee_category'];
                                }



                                $n++;
                            }

                            // echo "<br>";
                            // echo "<pre>";
                            // print_r($fees);exit;


                            $resultdata = []; // Initialize an empty array

                            foreach ($fees as $fee) {
                                foreach ($fee as $feeType => $amount) {
                                    $resultdata[$feeType] = $amount; // Add fee type and amount to the result array
                                }
                            }

                            $student_details = $this->student_model->findByAdmission($student_data[$i]["admission_no"]);
                            $student_session_id = $student_details->student_session_id;

                            // $feetypes = $student_data[$i]['fee_type'];

                            $fee_array = $resultdata;

                            // $fee_array = explode(',', $feetypes);
                            // $amount_array = explode('+', $student_data[$i]['amount']);
                            // $discount_array = explode('+', $student_data[$i]['amount_discount']);
                            // $fine_array = explode(',', $student_data[$i]['amount_fine']);
                            $staff_record    = $this->staff_model->get($this->customlib->getStaffID());


                            $fee_category = $student_data[$i]['fee_category'];

                            // echo $fee_category;exit;

                            if ($fee_category != "" && $fee_category == "discount") {
                                $fee_category = "discount";
                            } else if ($fee_category != "" && $fee_category == "oldfee") {
                                $fee_category = "fees";
                            } else {

                                $fee_category =  "fees";
                            }




                            $fee_date = $student_data[$i]['date'];
                            $description = $student_data[$i]['description'];
                            // $description = '';

                            $payment_mode = $student_data[$i]['payment_mode'];
                            $collected_by = $student_data[$i]['collected_by'];
                            $reference_no = $student_data[$i]['reference_no'];


                            $fees_data = array();
                            $selected_fee_types = array();

                            $receipt_amount = 0;

                            // for ($j = 0; $j < count($fee_array); $j++) 

                            // echo "<pre>";
                            // print_r($student_data[$i]);exit;



                            foreach ($fee_array as $key => $value) {

                                $res['fee_category'] = $fee_category;
                                $res['student_transport_fee_id'] = 0;

                                $fee = $key;

                                if ($fee_category == "fees") {
                                    $paidamount = $value;
                                    $receipt_amount += $value;
                                    $paiddiscount = 0;
                                } else if ($fee_category == "discount") {

                                    $paidamount = 0;
                                    // $receipt_amount += $value;
                                    $paiddiscount = $value;
                                }

                                $fineamount = 0;

                                if ($paidamount != 0) {
                                    $feedetails = $this->studentfeemaster_model->getfeedetails($fee, $student_session_id);
                                } else if ($paiddiscount != 0) {
                                    $feedetails = $this->studentfeemaster_model->getfeedetails($fee, $student_session_id);
                                }


                                // echo "<pre>";
                                // print_r($feedetails);exit;

                                if (!empty($feedetails)) {
                                    $res['student_fees_master_id'] = $feedetails['fee_master_id'];
                                    $res['fee_groups_feetype_id'] = $feedetails['fee_group_feetype_id'];

                                    if ($paidamount <= $feedetails['fee_amount'] || $feedetails['feestype'] == "Previous Session Balance") {
                                        $selected_fee_types[] = $feedetails['feestype'];

                                        // echo $res['fee_category'];exit;


                                        if ($res['fee_category'] == "fees" || $res['fee_category'] == "oldfee") {

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
                            // print_r($fees_data);




                            if (!empty($fees_data)) {

                                if ($fee_category == "fees") {


                                    $academic_session = $this->customlib->getCurrentSession();

                                    if (!empty($academic_session)) {
                                        $asession = $this->customlib->getAcademicSession($academic_session['session']);
                                        // echo "<pre>";
                                        // print_r($asession);exit;
                                        $end_year = $asession['end_year'];
                                        $start_year = $asession['start_year'];
                                    } else {


                                        $start_year = "24";
                                        $end_year = "24";
                                    }

                                    $table_name = 'student_fees_print_' . $start_year;

                                    // echo $table_name;exit;


                                    // New Fee Receipts Table ()
                                    $deposited_fees = $this->studentfeemaster_model->fee_deposit_collections($fees_data);

                                    $student_print = array(
                                        'student_session_id' => $student_session_id,
                                        'amount' => $receipt_amount,
                                        'fee_types' => $selected_fee_types_string,
                                        'collected_by' => $collected_by,
                                        'mode' => $payment_mode,
                                        'created_at' => $fee_date,
                                        'reference_no' => $reference_no
                                    );

                                    //print_r($student_print);
                                    $this->db->insert($table_name, $student_print);
                                } else {

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
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');

        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');
        // $this->form_validation->set_rules('amount_fine', $this->lang->line('fine'), 'required|trim|numeric|xss_clean');
        // $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                // 'amount'                 => form_error('amount'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'amount_discount'        => form_error('amount_discount'),



            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {


            $staff_record = $this->staff_model->get($this->customlib->getStaffID());

            $role_array     = $this->customlib->getStaffRole();
            $role           = json_decode($role_array);
            $staff_role     = $role->name;
            if ($staff_role == 'Super Admin') {
                $status = 1;
            } else {
                $status = 0;
            }


            $collected_by             = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";
            $student_fees_discount_id = $this->input->post('student_fees_discount_id');
            $json_array               = array(
                'amount'          => 0,
                'amount_discount' => convertCurrencyFormatToBaseAmount($this->input->post('amount_discount')),
                'amount_fine'     => 0,
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description'     => '',
                'collected_by'    => $collected_by,
                'payment_mode'    => 'discount',
                'received_by'     => $staff_record['id'],
                'status'          =>   $status,
                'discount_type'  =>    $this->input->post('discount_type')
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


            // echo "<pre>";
            // print_r($data);exit;

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

            // $action             = $this->input->post('action');
            // $send_to            = $this->input->post('guardian_phone');
            // $email              = $this->input->post('guardian_email');
            // $parent_app_key     = $this->input->post('parent_app_key');
            $student_session_id = $this->input->post('student_session_id');
            $inserted_id        = $this->studentfeemaster_model->fee_deposit($data, $send_to, $student_fees_discount_id);
            $receipt_data           = json_decode($inserted_id);

            $new_inserted_id = null;  //&& $json_array['payment_mode'] !== 'discount'
            if ($inserted_id && $json_array['payment_mode'] !== 'discount') {
                $return_data = json_decode($inserted_id);

                //  print_r($return_data);exit;
            }



            $print_record = array();
            if ($action == "print") {
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

                    //    $data['paymentID'] = $receipt_data->new_invoice_id;

                    if ($new_inserted_id) {
                        $data['new_insert_id'] = $new_inserted_id;
                    }

                    //    print_r($data['feeList']);exit;
                    // $print_record = $this->load->view('print/printFeesByName', $data, true);
                }
            }

            $mailsms_array->invoice            = $inserted_id;
            $mailsms_array->student_session_id = $student_session_id;
            $mailsms_array->contact_no         = $send_to;
            $mailsms_array->email              = $email;
            $mailsms_array->parent_app_key     = $parent_app_key;
            $mailsms_array->fee_category       = $fee_category;

            // $this->mailsmsconf->mailsms('fee_submission', $mailsms_array);

            $array = array('status' => 'success', 'error' => '', 'print' => $print_record);
            echo json_encode($array);
        }
    }

    // approved discount fee

    public function approveDiscountFee()
    {
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');

        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        // $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric|callback_check_deposit');
        // $this->form_validation->set_rules('amount_fine', $this->lang->line('fine'), 'required|trim|numeric|xss_clean');
        // $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                // 'amount'                 => form_error('amount'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),

            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            // echo "<pre>";
            // print_r($this->input->post());exit;

            $student_fees_deposite_id = $this->input->post('student_fees_deposite_id');
            $student_fees_master_id = $this->input->post('student_fees_master_id');
            $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
            $transport_fees_id      = $this->input->post('transport_fees_id');
            $invoice_id           = $this->input->post('invoice_id');

            $data = array(
                'student_fees_master_id' => $this->input->post('student_fees_master_id'),
                'fee_groups_feetype_id'  => $this->input->post('fee_groups_feetype_id'),
                'id'          => $student_fees_deposite_id,
            );




            $student_session_id = $this->input->post('student_session_id');
            $res        = $this->studentfeemaster_model->updateDiscountStatus($data, $invoice_id);

            if ($res) {

                $array = array('status' => 'success', 'error' => '', 'message' => 'Discount Approved Sucessfully');
            } else {

                $array = array('status' => 'fail', 'error' => '', 'message' => 'Some thing went wrong');
            }

            echo json_encode($array);
        }
    }

    // assgin discount fee 

    public function assignstudentdiscountfee()
    {
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        // $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric|callback_check_deposit');
        $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');
        // $this->form_validation->set_rules('amount_fine', $this->lang->line('fine'), 'required|trim|numeric|xss_clean');
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
                'amount'          => convertCurrencyFormatToBaseAmount(0),
                'amount_discount' => convertCurrencyFormatToBaseAmount($this->input->post('amount_discount')),
                'amount_fine'     => convertCurrencyFormatToBaseAmount(0),
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description'     => $this->input->post('description'),
                'collected_by'    => $collected_by,
                'payment_mode'    => $this->input->post('payment_mode'),
                'received_by'     => $staff_record['id'],
                'discount_type'     => $this->input->post('discount_type'),

            );

            $role_array     = $this->customlib->getStaffRole();
            $role           = json_decode($role_array);
            $staff_role     = $role->name;
            $paymode =  $this->input->post('payment_mode');


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


            // echo "<pre>";
            // print_r($data);exit;

            if ($transport_fees_id != 0 && $fee_category == "transport") {
                $mailsms_array                    = new stdClass();
                $data['student_fees_master_id']   = null;
                $data['fee_groups_feetype_id']    = null;
                $data['student_transport_fee_id'] = $transport_fees_id;

                $mailsms_array                 = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($transport_fees_id);
                $mailsms_array->fee_group_name = $this->lang->line("transport_fees");
                $mailsms_array->type           = $mailsms_array->month;
                $mailsms_array->code           = "";
            }

            $send_to            = $this->input->post('guardian_phone');
            $student_session_id = $this->input->post('student_session_id');
            if ($staff_role == 'Super Admin' && $paymode == "discount") {

                // $json_array['status'] = 1;
                $inserted_id        = $this->studentfeemaster_model->fee_deposit($data, $send_to, $student_fees_discount_id);
            } else {
                $inserted_id        = $this->studentfeemaster_model->fee_discount_approve($data, $send_to, $student_fees_discount_id);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');




            $array = array('status' => 'success', 'error' => '', 'print' => '', 'msg' =>  $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }


    public function deleteDiscountFee()
    {
        if (!$this->rbac->hasPrivilege('feediscount_assign', 'can_delete')) {
            access_denied();
        }
        $invoice_id  = $this->input->post('main_invoice');
        $sub_invoice = $this->input->post('sub_invoice');
        if (!empty($invoice_id)) {
            $this->studentfee_model->discountremove($invoice_id, $sub_invoice);
        }
        $array = array('status' => 'success', 'result' => 'success');
        echo json_encode($array);
    }
}

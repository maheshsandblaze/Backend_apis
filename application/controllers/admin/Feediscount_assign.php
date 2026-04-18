<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feediscount_assign extends Admin_Controller
{

    protected $balance_group;
    protected $balance_type;
    protected $setting_result;

    public function __construct()
    {
        parent::__construct();
        $this->load->config('ci-blog');
        $this->balance_group = $this->config->item('ci_balance_group');
        $this->balance_type  = $this->config->item('ci_balance_type');

        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('feediscount_assign', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'Feediscount_assign/index');
        $class                   = $this->class_model->get();
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['classlist']       = $class;
        $action                  = $this->input->post('action');
        $class_id                = $this->input->post('class_id');
        $section_id              = $this->input->post('section_id');
 


            //========================

            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required');
            // $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'required');
            if ($this->form_validation->run() == true) {
                $data['student_due_fee'] = array();

                $data['sch_setting']            = $this->sch_setting_detail;

                $start_month = sprintf("%02d", $data['sch_setting']->start_month);

                $academic_session = $data['sch_setting']->session;

                // $academic_session = "2024-25";
                $years  = explode('-', $academic_session);

                $start_year = $years[0];
                $end_year = $years[1];

                $end_month = $start_month - 1;


                $start_date  = date($start_year . '-04-01');

                // $end_date = date($end_year.'-'.$end_month.'-t');
                $end_date = date('Y-m-d');


                // $student_Array = $this->student_model->getStudentfeesSearchByclassesection($class_id,$section_id);
                // $fee_discount_payments   = $this->studentfeemaster_model->getFeeDisountBetweenDate($start_date, $end_date,$class_id,$section_id);


                if (isset($class_id) && $class_id !=  0) {

                    $studentlist = $this->student_model->searchByClassSectionWithSession($class_id, $section_id);
                } else {


                    $studentlist = $this->student_model->getStudents();
                }

                // echo "<pre>";
                // print_r($studentlist);exit;

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


                        // $obj->father_phone = $eachstudent['father_phone'];
                        $student_session_id = $eachstudent['student_session_id'];

                        // if (isset($fee_enquiry_data) && !empty($fee_enquiry_data)) {
                        //     $obj->status   = $fee_enquiry_data['status'];
                        // } else {
                        //     $obj->status   = 'Active';
                        // }
                        $student_total_fees = $this->studentfeemaster_model->getStudentFees($student_session_id);
                        // echo "<pre>";
                        // print_r($student_total_fees);

                        if (!empty($student_total_fees)) {
                            $totalfee = 0;
                            $deposit  = 0;
                            $discount = 0;
                            $balance  = 0;
                            $fine     = 0;
                            $fee_tobe_pay = 0;
                            $feetypePaidAmount = [];
                            $feetypeAmounts = [];
                            foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {

                                // $obj->fees = $student_total_fees_value->fees;

                                // print_r($student_total_fees_value);


                                if (!empty($student_total_fees_value->fees)) {
                                    foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {

                                        // echo "<pre>";
                                        // print_r($each_fee_value);
                                        $totalfee = $totalfee + $each_fee_value->amount;
                                        $assign_descounts = $this->studentfeemaster_model->getstudentassigndiscount($each_fee_value->id, $each_fee_value->fee_groups_feetype_id);

                                        if (!empty($assign_descounts)) {
                                            $each_fee_value->discountstatus = $assign_descounts->is_active;
                                            $each_fee_value->feediscountID = $assign_descounts->id;
                                        } else {
                                            $each_fee_value->discountstatus = '';
                                            $each_fee_value->feediscountID = 0;
                                        }

                                        // echo "<pre>";
                                        // print_r($assign_descounts);

                                        $obj->fees[] = $each_fee_value;


                                        $feetype = $each_fee_value->code;

                                        // if ($feetype != "Previous Session Balance") {

                                        //     $fee_tobe_pay += $each_fee_value->amount;
                                        // }




                                        if (!isset($feetypePaidAmount[$feetype])) {
                                            $feetypePaidAmount[$feetype] = 0;

                                            // if ($feetype != "Previous Session Balance") {
                                            //     $feetypePaidAmount["Previous Session Balance"] = 0;
                                            // }
                                            $feetypeAmounts[$feetype] = 0;
                                        }


                                        $feetypeAmounts[$feetype] += $each_fee_value->amount;


                                        $amount_detail = json_decode($each_fee_value->amount_detail);

                                        if (is_object($amount_detail)) {
                                            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                                                $feetypePaidAmount[$feetype] += $amount_detail_value->amount;
                                                $deposit  = $deposit + $amount_detail_value->amount;
                                                $fine     = $fine + $amount_detail_value->amount_fine;
                                                $discount = $discount + $amount_detail_value->amount_discount;
                                            }
                                        }
                                    }
                                    // exit;
                                } else {
                                    $obj->fees = array();
                                }
                            }




                            $obj->totalfee     = $totalfee; // remove discount for feetobepay
                            $obj->fee_tobe_pay = $fee_tobe_pay - $discount; // remove discount for totalfeetobepay
                            $obj->payment_mode = "N/A";
                            $obj->deposit      = $deposit;
                            $obj->fine         = $fine;
                            $obj->discount     = $discount;
                            $obj->balance      = $totalfee - ($deposit + $discount);
                            $obj->feetypePaidAmount = $feetypePaidAmount;
                        } else {

                            $obj->totalfee     = 0;
                            $obj->fee_tobe_pay = 0;

                            $obj->payment_mode = 0;
                            $obj->deposit      = 0;
                            $obj->fine         = 0;
                            $obj->balance      = 0;
                            $obj->discount     = 0;
                            $obj->fees     = array();
                        }


                        $student_Array[] = $obj;
                    }
                }
                // exit;

                $data['student_due_fee'] = $student_Array;
                // echo "<pre>";
                // print_r($student_Array);exit;





                // $data['fee_discounts'] = $fee_discount_payments;
                // $data['is_update']       = $student_Array->is_update;
            } else {

                $studentlist = $this->student_model->getdiscountassignstudents();

                // echo "<pre>";
                // print_r($studentlist);exit;


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



                        $obj->father_phone = $eachstudent['father_phone'];
                        $student_session_id = $eachstudent['student_session_id'];

                        if (isset($fee_enquiry_data) && !empty($fee_enquiry_data)) {
                            $obj->status   = $fee_enquiry_data['status'];
                        } else {
                            $obj->status   = 'Active';
                        }
                        $student_total_fees = $this->studentfeemaster_model->getStudentDiscounts($student_session_id); // get assing descounts
                        // echo "<pre>";
                        // print_r($student_total_fees);

                        if (!empty($student_total_fees)) {
                            $totalfee = 0;
                            $deposit  = 0;
                            $discount = 0;
                            $balance  = 0;
                            $fine     = 0;
                            $fee_tobe_pay = 0;
                            $feetypePaidAmount = [];
                            $feetypeAmounts = [];
                            $discount_type = "";
                            foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {

                                // $obj->fees = $student_total_fees_value->fees;

                                // print_r($student_total_fees_value);


                                if (!empty($student_total_fees_value->fees)) {
                                    foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {

                                        // echo "<pre>";
                                        // print_r($each_fee_value);
                                        $totalfee = $totalfee + $each_fee_value->amount;
                                        // $assign_descounts = $this->studentfeemaster_model->getstudentassigndiscount($each_fee_value->id,$each_fee_value->fee_groups_feetype_id);

                                        // if(!empty($assign_descounts))
                                        // {
                                        //     $each_fee_value->discountstatus = $assign_descounts->is_active;
                                        //     $each_fee_value->feediscountID = $assign_descounts->id;
                                        // }
                                        // else {
                                        //     $each_fee_value->discountstatus = '';
                                        //     $each_fee_value->feediscountID = 0;

                                        // }

                                        // echo "<pre>";
                                        // print_r($assign_descounts);

                                        $obj->fees[] = $each_fee_value;


                                        $feetype = $each_fee_value->code;

                                        // if ($feetype != "Previous Session Balance") {

                                        //     $fee_tobe_pay += $each_fee_value->amount;
                                        // }




                                        if (!isset($feetypePaidAmount[$feetype])) {
                                            $feetypePaidAmount[$feetype] = 0;

                                            // if ($feetype != "Previous Session Balance") {
                                            //     $feetypePaidAmount["Previous Session Balance"] = 0;
                                            // }
                                            $feetypeAmounts[$feetype] = 0;
                                        }


                                        $feetypeAmounts[$feetype] += $each_fee_value->amount;


                                        $amount_detail = json_decode($each_fee_value->amount_detail);

                                        if (is_object($amount_detail)) {
                                            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                                                $feetypePaidAmount[$feetype] += $amount_detail_value->amount;
                                                $deposit  = $deposit + $amount_detail_value->amount;
                                                $fine     = $fine + $amount_detail_value->amount_fine;
                                                $discount = $discount + $amount_detail_value->amount_discount;
                                                $discount_type = $amount_detail_value->discount_type;
                                            }
                                        }
                                    }
                                    // exit;
                                } else {
                                    $obj->fees = array();
                                }
                            }




                            $obj->totalfee     = $totalfee; // remove discount for feetobepay
                            $obj->fee_tobe_pay = $fee_tobe_pay - $discount; // remove discount for totalfeetobepay
                            $obj->payment_mode = "N/A";
                            $obj->deposit      = $deposit;
                            $obj->fine         = $fine;
                            $obj->discount     = $discount;
                            $obj->balance      = $totalfee - ($deposit + $discount);
                            $obj->feetypePaidAmount = $feetypePaidAmount;

                            $obj->discount_type = $discount_type;
                        } else {

                            $obj->totalfee     = 0;
                            $obj->fee_tobe_pay = 0;

                            $obj->payment_mode = 0;
                            $obj->deposit      = 0;
                            $obj->fine         = 0;
                            $obj->balance      = 0;
                            $obj->discount     = 0;
                            $obj->fees     = array();
                            $obj->discount_type = "";
                        }


                        $student_Array[] = $obj;
                    }
                }

                $data['discount_assign_students'] = $student_Array;
            }
        


        // echo "<pre>";
        // print_r($data);exit;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/feediscountassign/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function findPreviousBalanceFees($session_id, $class_id, $section_id, $current_session)
    {
        $studentlist = $this->student_model->getPreviousSessionStudent($session_id, $class_id, $section_id);

        $is_update     = false;
        $student_Array = array();
        if (!empty($studentlist)) {
            $student_comma_seprate = array();

            foreach ($studentlist as $student_list_key => $student_list_value) {

                $obj                              = new stdClass();
                $obj->name                        = $this->customlib->getFullName($student_list_value->firstname, $student_list_value->middlename, $student_list_value->lastname, $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
                $obj->admission_no                = $student_list_value->admission_no;
                $obj->roll_no                     = $student_list_value->roll_no;
                $obj->father_name                 = $student_list_value->father_name;
                $obj->student_session_id          = $student_list_value->current_student_session_id;
                $obj->student_previous_session_id = $student_list_value->previous_student_session_id;
                $obj->admission_date              = $this->customlib->dateformat($student_list_value->admission_date);
                $student_Array[]                  = $obj;
                $student_comma_seprate[]          = $student_list_value->current_student_session_id;
            }

            $student_session_array = "(" . implode(",", $student_comma_seprate) . ")";
            $record_exists         = $this->studentfeemaster_model->getBalanceMasterRecord($this->balance_group, $student_session_array);

            if (!empty($record_exists)) {
                $is_update = true;
                foreach ($student_Array as $stkey => $eachstudent) {

                    $eachstudent->balance = $this->findValueExists($record_exists, $eachstudent->student_session_id);
                }
            } else {
                foreach ($student_Array as $stkey => $eachstudent) {

                    //==========================
                    $student_total_fees = array();
                    if ($eachstudent->student_previous_session_id != "") {

                        $student_total_fees = $this->studentfeemaster_model->getPreviousStudentFees($eachstudent->student_previous_session_id);
                    }

                    if (!empty($student_total_fees)) {
                        $totalfee = 0;
                        $deposit  = 0;
                        $discount = 0;
                        $balance  = 0;
                        foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {
                            if (!empty($student_total_fees_value->fees)) {
                                foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {
                                    $totalfee = $totalfee + $each_fee_value->amount;

                                    $amount_detail = json_decode($each_fee_value->amount_detail);
                                    if ($amount_detail != null) {
                                        foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                                            $deposit  = $deposit + $amount_detail_value->amount;
                                            $discount = $discount + $amount_detail_value->amount_discount;
                                        }
                                    }
                                }
                            }
                        }

                        $eachstudent->balance = $totalfee - ($deposit + $discount);
                    } else {
                        $eachstudent->balance = "0";
                    }
                    //===================
                }
            }
        }

        return json_encode(array('student_Array' => $student_Array, 'is_update' => $is_update));
    }

    public function findValueExists($array, $find)
    {
        $amount = 0;
        foreach ($array as $x => $x_value) {
            if ($x_value->student_session_id == $find) {
                return $x_value->amount;
            }
        }
        return $amount;
    }

    // approvediscount

    public function approvediscount()
    {
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(

                'student_fees_master_id' => form_error('student_fees_master_id'),
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'amount_discount'        => form_error('amount_discount'),
                'date'           => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {


            $staff_record = $this->staff_model->get($this->customlib->getStaffID());

            $collected_by             = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";
            $student_fees_discount_id = $this->input->post('student_fees_discount_id');
            $approve_id = $this->input->post('discount_approve_id');
            $json_array               = array(
                'amount'          => convertCurrencyFormatToBaseAmount(0),
                'amount_discount' => convertCurrencyFormatToBaseAmount($this->input->post('amount_discount')),
                'amount_fine'     => convertCurrencyFormatToBaseAmount(0),
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description'     => $this->input->post('description'),
                'collected_by'    => $collected_by,
                'payment_mode'    => 'discount',
                'received_by'     => $staff_record['id'],
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





            $send_to            = $this->input->post('guardian_phone');
            $student_session_id = $this->input->post('student_session_id');

            $updedata = array('id' => $approve_id, 'student_fees_master_id' => $student_fees_master_id, 'fee_groups_feetype_id' => $fee_groups_feetype_id);

            $res = $this->studentfeemaster_model->updatediscount($updedata);

            $inserted_id        = $this->studentfeemaster_model->fee_deposit($data, $send_to, $student_fees_discount_id);



            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');




            $array = array('status' => 'success', 'error' => '', 'print' => '', 'msg' =>  $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }
}

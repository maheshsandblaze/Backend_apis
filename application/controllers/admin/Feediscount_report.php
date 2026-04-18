<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feediscount_report extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->load->model(array('feesreceipt_model', 'setting_model','studentfeemaster_model'));
        $this->sch_setting_detail = $this->setting_model->getSetting();

    }

    public function index()
    {

        // $this->session->set_userdata('top_menu', 'Fees Receipt');
        // $this->session->set_userdata('sub_menu', 'feesreceipt/index');
        
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'feediscount_report/index');

        $data['sch_setting']            = $this->sch_setting_detail;
      
        $start_month =sprintf("%02d", $data['sch_setting']->start_month) ;

        $academic_session =$data['sch_setting']->session;

        $academic_session = "2024-25";
        $years  = explode('-', $academic_session);

        $start_year = $years[0];
        $end_year = $years[1];

        $end_month = $start_month -1;

        // $start_date  = date('Y-'.$start_month.'-01');
        // // echo $start_date;exit;
        // $end_date = date('y-m-t');
        $start_date  = date($start_year.'-'.$start_month.'-01');
        
        $end_date = date('y-m-d');
        
  
        // $start_date           = date('Y-m-01');
        // $end_date             = date('Y-m-t');
        // $start_date  = date('Y-'.$start_month.'-01');
        // echo $start_date;exit;
        // $end_date = date('y-m-t');
        // $fee_payments             = $this->feesreceipt_model->get();
        $fee_discount_payments   = $this->studentfeemaster_model->getFeeDisountBetweenDate($start_date,$end_date);
        // echo "<pre>";
        // print_r($fee_discount_payments);exit;
        // $data['fee_payments'] = $fee_payments;
        $data['fee_disount_payments']     = $fee_discount_payments;


        $this->load->view('layout/header', $data);
        $this->load->view('admin/feediscountreport/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function printStudentGroupFees()
    {
        $class_id               = $this->input->post('class_id');
        $section_id             = $this->input->post('section_id');
        $student_id             = $this->input->post('student_id');
        $receipt_id             = $this->input->post('receipt_id');
        
        $setting_result         = $this->setting_model->get();
        $data['settinglist']    = $setting_result;
        $student                = $this->feesreceipt_model->printStudentGroupFees($receipt_id);
        $data['student']        = $student;
        
        $data['sch_setting']    = $this->setting_model->getSetting();
        
        $page = $this->load->view('admin/feesreceipt/printStudentGroupFees', $data, true);

        echo json_encode(array('status' => 1, 'page' => $page));

    }
    
    public function feesreceipt_24()
    {

        // $this->session->set_userdata('top_menu', 'Fees Receipt');
        // $this->session->set_userdata('sub_menu', 'feesreceipt/index');
        
        $this->session->set_userdata('top_menu', 'Fees Receipt');
        $this->session->set_userdata('sub_menu', 'feesreceipt/feesreceipt_24');
        $fee_payments             = $this->feesreceipt_model->get24fees();
        $data['fee_payments']     = $fee_payments;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/feesreceipt/feesreceipt24', $data);
        $this->load->view('layout/footer', $data);
    }
    
    public function printStudentGroupFees24()
    {
        $class_id               = $this->input->post('class_id');
        $section_id             = $this->input->post('section_id');
        $student_id             = $this->input->post('student_id');
        $receipt_id             = $this->input->post('receipt_id');
        
        $setting_result         = $this->setting_model->get();
        $data['settinglist']    = $setting_result;
        $student                = $this->feesreceipt_model->printStudentGroupFees24($receipt_id);
        $data['student']        = $student;
        
        $data['sch_setting']    = $this->setting_model->getSetting();
        
        $page = $this->load->view('admin/feesreceipt/printStudentGroupFees24', $data, true);

        echo json_encode(array('status' => 1, 'page' => $page));

    }

}

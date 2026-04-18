<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feesreceipt extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->load->model(array('feesreceipt_model', 'setting_model'));
        $this->load->library('Customlib');

        
        $this->sch_setting_detail = $this->setting_model->getSetting();

    }

    public function index()
    {

        // $this->session->set_userdata('top_menu', 'Fees Receipt');
        // $this->session->set_userdata('sub_menu', 'feesreceipt/index');
        
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'feesreceipt/index');
        $fee_payments             = $this->feesreceipt_model->get();
        $data['fee_payments']     = $fee_payments;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/feesreceipt/index', $data);
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
    
    public function studentfee_challan()
    {
        
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'feesreceipt/feechallan');
        $fee_payments             = $this->feesreceipt_model->getstudentchallan();
        $data['fee_payments']     = $fee_payments;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/feesreceipt/studentfee_challan', $data);
        $this->load->view('layout/footer', $data);
    }
    
    public function feesreceipt_24()
    {

        // $this->session->set_userdata('top_menu', 'Fees Receipt');
        // $this->session->set_userdata('sub_menu', 'feesreceipt/index');
        
        $this->session->set_userdata('top_menu', 'Fees Receipt');
        $this->session->set_userdata('sub_menu', 'feesreceipt/feesreceipt_24');
        $fee_payments             = $this->feesreceipt_model->get24fees();
        $data['fee_payments']     = $fee_payments;
        $data['sch_setting'] = $this->sch_setting_detail;

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
    
    public function printStudentchallan()
    {
        $student_id             = $this->input->post('student_id');
        $receipt_id             = $this->input->post('receipt_id');
        
        $setting_result         = $this->setting_model->get();
        $data['settinglist']    = $setting_result;
        $student                = $this->feesreceipt_model->printStudentchallan($receipt_id);
        $data['student']        = $student;
        
        $data['sch_setting']    = $this->setting_model->getSetting();
        
        $page = $this->load->view('admin/feesreceipt/printStudentChallan', $data, true);

        echo json_encode(array('status' => 1, 'page' => $page));

    }

}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feesreceipt extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->load->model(array('feesreceipt_model', 'setting_model'));
        $this->load->library('Customlib');
        
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    protected function _get_input()
    {
        $input = $this->input->post();
        if (empty($input)) {
            $input = json_decode($this->input->raw_input_stream, true);
        }
        return $input ?: [];
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $fee_payments = $this->feesreceipt_model->get();
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'       => 'success',
                'fee_payments' => $fee_payments
            ]));
    }

    public function printStudentGroupFees()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input      = $this->_get_input();
        $receipt_id = $input['receipt_id'] ?? null;
        
        $setting_result = $this->setting_model->get();
        $student        = $this->feesreceipt_model->printStudentGroupFees($receipt_id);
        $sch_setting    = $this->setting_model->getSetting();

        $data = [
            'settinglist' => $setting_result,
            'student'     => $student,
            'sch_setting' => $sch_setting
        ];

        $page = $this->load->view('admin/feesreceipt/printStudentGroupFees', $data, true);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 1,
                'page'        => $page,
                'data'        => $data
            ]));
    }
    
    public function studentfee_challan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $fee_payments = $this->feesreceipt_model->getstudentchallan();
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'       => 'success',
                'fee_payments' => $fee_payments
            ]));
    }
    
    public function feesreceipt_24()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $fee_payments = $this->feesreceipt_model->get24fees();
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'       => 'success',
                'fee_payments' => $fee_payments,
                'sch_setting' => $this->sch_setting_detail
            ]));
    }
    
    public function printStudentGroupFees24()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input      = $this->_get_input();
        $receipt_id = $input['receipt_id'] ?? null;
        
        $setting_result = $this->setting_model->get();
        $student        = $this->feesreceipt_model->printStudentGroupFees24($receipt_id);
        $sch_setting    = $this->setting_model->getSetting();

        $data = [
            'settinglist' => $setting_result,
            'student'     => $student,
            'sch_setting' => $sch_setting
        ];
        
        $page = $this->load->view('admin/feesreceipt/printStudentGroupFees24', $data, true);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 1,
                'page'        => $page,
                'data'        => $data
            ]));
    }
    
    public function printStudentchallan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input      = $this->_get_input();
        $receipt_id = $input['receipt_id'] ?? null;
        
        $setting_result = $this->setting_model->get();
        $student        = $this->feesreceipt_model->printStudentchallan($receipt_id);
        $sch_setting    = $this->setting_model->getSetting();

        $data = [
            'settinglist' => $setting_result,
            'student'     => $student,
            'sch_setting' => $sch_setting
        ];
        
        $page = $this->load->view('admin/feesreceipt/printStudentChallan', $data, true);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 1,
                'page'        => $page,
                'data'        => $data
            ]));
    }

}

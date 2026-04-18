<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Leavetypes extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->config->load("payroll");
        $this->load->model('leavetypes_model');
        $this->load->model('staff_model');
        $this->load->library('form_validation'); // Ensure loaded
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input ?: [];
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $LeaveTypes = $this->leavetypes_model->getLeaveType();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'    => 'success',
                'leavetype' => $LeaveTypes
            ]));
    }

    public function createleavetype()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules(
            'type', $this->lang->line('name'), array('required',
                array('check_exists', array($this->leavetypes_model, 'valid_leave_type')),
            )
        );
        
        $leavetypeid = $input['leavetypeid'] ?? null;
        
        // Privilege Check logic adapted from original
        // if (empty($leavetypeid)) {
        //     if (!$this->rbac->hasPrivilege('leave_types', 'can_add')) ...
        // } else {
        //     if (!$this->rbac->hasPrivilege('leave_types', 'can_edit')) ...
        // }

        if ($this->form_validation->run()) {

            $type = $input['type'];
            
            if (!empty($leavetypeid)) {
                $data = array('type' => $type, 'is_active' => 'yes', 'id' => $leavetypeid);
            } else {
                $data = array('type' => $type, 'is_active' => 'yes');
            }

            $insert_id = $this->leavetypes_model->addLeaveType($data);
            
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message'),
                    'id'      => $insert_id ?: $leavetypeid
                ]));

        } else {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        }
    }

    public function leaveedit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $result = $this->staff_model->getLeaveType($id);
        
        if (!$result) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Leave Type not found']));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'result' => $result
            ]));
    }

    public function leavedelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $this->leavetypes_model->deleteLeaveType($id);
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('delete_message')
            ]));
    }

}

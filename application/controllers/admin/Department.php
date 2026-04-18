<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Department extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->config->load("payroll");
        $this->load->model('department_model');
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

    public function department()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        // Handle POST (Add/Update)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            // Check privilege
            $departmenttypeid = $input['departmenttypeid'] ?? null;
            // if (empty($departmenttypeid)) {
            //     if (!$this->rbac->hasPrivilege('department', 'can_add')) ...
            // } else {
            //     if (!$this->rbac->hasPrivilege('department', 'can_edit')) ...
            // }
            
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules(
                'type', $this->lang->line('name'), array('required',
                array('check_exists', array($this->department_model, 'valid_department'))
                )
            );

            if ($this->form_validation->run()) {
                $type = $input['type'];

                if (!empty($departmenttypeid)) {
                    $data = array('department_name' => $type, 'is_active' => 'yes', 'id' => $departmenttypeid);
                } else {
                    $data = array('department_name' => $type, 'is_active' => 'yes');
                }

                $insert_id = $this->department_model->addDepartmentType($data);
                
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('success_message'),
                        'id'      => $insert_id
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

        // GET behavior: List all
        $DepartmentTypes = $this->department_model->getDepartmentType();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'         => 'success',
                'departmenttype' => $DepartmentTypes
            ]));
    }

    public function departmentedit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $result = $this->department_model->getDepartmentType($id);
        
        if (!$result) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Department not found']));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'result' => $result
            ]));
    }

    public function departmentdelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $this->department_model->deleteDepartment($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('delete_message')
            ]));
    }

}
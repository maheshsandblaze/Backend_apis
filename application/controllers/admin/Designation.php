<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Designation extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->config->load("payroll");
        $this->load->model('designation_model');
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

    public function designation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        // Handle POST (Add/Update)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules(
                'type', $this->lang->line('name'), array('required',
                array('check_exists', array($this->designation_model, 'valid_designation'))
                )
            );

            if ($this->form_validation->run()) {
                $type          = $input['type'];
                $designationid = $input['designationid'] ?? null;

                if (!empty($designationid)) {
                    $data = array('designation' => $type, 'is_active' => 'yes', 'id' => $designationid);
                } else {
                    $data = array('designation' => $type, 'is_active' => 'yes');
                }

                $insert_id = $this->designation_model->addDesignation($data);
                
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
        $designation = $this->designation_model->get();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 'success',
                'designation' => $designation
            ]));
    }

    public function designationedit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $result = $this->designation_model->get($id);
        
        if (!$result) {
             return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Designation not found']));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'result' => $result
            ]));
    }

    public function designationdelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $this->designation_model->deleteDesignation($id);
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('delete_message')
            ]));
    }

}
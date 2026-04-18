<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Subject extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('form_validation');
        $this->load->model('subject_model');
        $this->load->model('teachersubject_model');
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

        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('name', $this->lang->line('subject_name'), 'trim|required|xss_clean|callback__check_name_exists');
            $this->form_validation->set_rules('type', $this->lang->line('type'), 'trim|required|xss_clean');
            
            if (isset($input['code']) && $input['code'] != "") {
                $this->form_validation->set_rules('code', $this->lang->line('code'), 'trim|required|callback__check_code_exists');
            }

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $data = array(
                    'name' => $input['name'],
                    'code' => $input['code'] ?? '',
                    'type' => $input['type'],
                );
                $insert_id = $this->subject_model->add($data);
                
                if ($insert_id) {
                    return $this->output
                        ->set_status_header(200)
                        ->set_output(json_encode([
                            'status'  => 'success',
                            'message' => $this->lang->line('success_message'),
                            'id'      => $insert_id
                        ]));
                } else {
                    return $this->output
                        ->set_status_header(500)
                        ->set_output(json_encode([
                            'status'  => 'fail',
                            'message' => 'Failed to add subject'
                        ]));
                }
            }
        }

        // GET behavior: List all
        $subject_result = $this->subject_model->get();
        $subject_types  = $this->customlib->subjectType();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'        => 'success',
                'subjectlist'   => $subject_result,
                'subject_types' => $subject_types
            ]));
    }

    public function view($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
        }

        $subject = $this->subject_model->get($id);
        
        if (!$subject) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Subject not found']));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'subject' => $subject
            ]));
    }

    public function delete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!$id) {
            $input = $this->_get_input();
            $id = $input['id'] ?? null;
        }

        if ($id) {
            $result = $this->subject_model->remove($id);
            if ($result) {
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('delete_message')
                    ]));
            } else {
                 return $this->output
                    ->set_status_header(500)
                    ->set_output(json_encode([
                        'status'  => 'fail',
                        'message' => 'Failed to delete subject'
                    ]));
            }
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
    }

    public function _check_name_exists($name)
    {
        $input = $this->_get_input();
        $data = array('name' => $name);
        if (isset($input['id'])) {
            $data['id'] = $input['id'];
        }
        
        if ($this->subject_model->check_data_exists($data)) {
            $this->form_validation->set_message('_check_name_exists', $this->lang->line('name_already_exists'));
            return false;
        } else {
            return true;
        }
    }

    public function _check_code_exists($code)
    {
        $input = $this->_get_input();
        $data = array('code' => $code);
        if (isset($input['id'])) {
            $data['id'] = $input['id'];
        }

        if ($this->subject_model->check_code_exists($data)) {
            $this->form_validation->set_message('_check_code_exists', $this->lang->line('code_already_exists'));
            return false;
        } else {
            return true;
        }
    }
    
    
    public function get_subjectDetails($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        // Allow only GET request
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Only GET method is allowed"
                ]));
        }
    
        // Validate ID
        if (empty($id)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Subject ID is required"
                ]));
        }
    
        // Fetch subject details
        $subject = $this->subject_model->get($id);
    
        // Check if subject exists
        if (!$subject) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Subject not found"
                ]));
        }
    
        // Fetch subject list (optional)
        $subjectlist = $this->subject_model->get();
    
        // Fetch subject types
        $subject_types = $this->customlib->subjectType();
    
        // API Response
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => true,
                "message" => "Subject Details Fetched Successfully",
                "data" => [
                    "subject"       => $subject,
                    "subjectlist"   => $subjectlist,
                    "subject_types" => $subject_types
                ]
            ]));
    }

    

    public function edit($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $id = $id ?: ($input['id'] ?? null);

        if (!$id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
        }

        $subject = $this->subject_model->get($id);
        if (!$subject) {
             return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Subject not found']));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('name', $this->lang->line('subject'), 'trim|required|xss_clean|callback__check_name_exists');
            $this->form_validation->set_rules('type', $this->lang->line('type'), 'trim|required|xss_clean');
            
            if (isset($input['code']) && $input['code'] != "") {
                $this->form_validation->set_rules('code', $this->lang->line('code'), 'trim|required|callback__check_code_exists');
            }

            if ($this->form_validation->run() == false) {
                 return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $data = array(
                    'id'   => $id,
                    'name' => $input['name'],
                    'code' => $input['code'] ?? '',
                    'type' => $input['type'],
                );
                $this->subject_model->add($data);
                
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('update_message')
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'        => 'success',
                'subject'       => $subject,
                'subject_types' => $this->customlib->subjectType()
            ]));
    }

    public function getSubjctByClassandSection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input      = $this->_get_input();
        $class_id   = $input['class_id'] ?? null;
        $section_id = $input['section_id'] ?? null;

        if (!$class_id || !$section_id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'class_id and section_id are required']));
        }

        $data = $this->teachersubject_model->getSubjectByClsandSection($class_id, $section_id);
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }

}

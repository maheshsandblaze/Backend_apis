<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Classes extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->model('classsection_model');
        $this->load->model('student_model');
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
            $this->form_validation->set_rules(
                'class', $this->lang->line('class'), array(
                    'required',
                    array('class_exists', array($this->class_model, 'class_exists')),
                )
            );
            $this->form_validation->set_rules('sections[]', $this->lang->line('section'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $class_array = array(
                    'class' => $input['class'],
                );
                $sections = $input['sections'];
                $insert_id = $this->classsection_model->add($class_array, $sections);
                
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
                            'message' => 'Failed to add class'
                        ]));
                }
            }
        }

        // GET behavior: List all
        $section_result      = $this->section_model->get();
        $vehroute_result      = $this->classsection_model->getByID();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'         => 'success',
                'sectionlist'    => $section_result,
                'classsectionlist' => $vehroute_result
            ]));
    }
    
    
    public function classIndexApi()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    
        /* =========================
           HANDLE GET (LIST)
        ========================== */
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
            $sections      = $this->section_model->get();
            $classSections = $this->classsection_model->getByID();
    
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'title'  => 'Class List',
                    'data'   => [
                        'sections'       => $sections,
                        'class_sections' => $classSections
                    ]
                ]));
        }
    
        /* =========================
           HANDLE POST (ADD CLASS)
        ========================== */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
            $input = json_decode(file_get_contents("php://input"), true);
            if (empty($input)) {
                $input = $this->input->post();
            }
    
            $this->form_validation->set_data($input);
    
            $this->form_validation->set_rules(
                'class',
                $this->lang->line('class'),
                [
                    'required',
                    ['class_exists', [$this->class_model, 'class_exists']]
                ]
            );
    
            $this->form_validation->set_rules(
                'sections',
                $this->lang->line('section'),
                'required'
            );
    
            if ($this->form_validation->run() === false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => $this->form_validation->error_array()
                    ]));
            }
    
            $class_array = [
                'class' => $input['class']
            ];
    
            $sections = $input['sections'];
    
            $this->classsection_model->add($class_array, $sections);
    
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $this->lang->line('success_message')
                ]));
        }
    
        /* =========================
           INVALID METHOD
        ========================== */
        return $this->output
            ->set_status_header(405)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => false,
                'message' => 'Method Not Allowed'
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
            $result = $this->class_model->remove($id);
            if ($result) {
                // Secondary cleanup logic from original controller
                $student_delete = $this->student_model->getUndefinedStudent();
                if (!empty($student_delete)) {
                    $delete_student_array = array();
                    foreach ($student_delete as $student_value) {
                        $delete_student_array[] = $student_value->id;
                    }
                    $this->student_model->bulkdelete($delete_student_array);
                }

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
                        'message' => 'Failed to delete class'
                    ]));
            }
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
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

        $class_data = $this->classsection_model->getByID($id);
        if (empty($class_data)) {
             return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Class not found']));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules(
                'class', $this->lang->line('class'), array(
                    'required',
                    array('class_exists', array($this->class_model, 'class_exists')),
                )
            );
            $this->form_validation->set_rules('sections[]', $this->lang->line('sections'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                 return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $sections      = $input['sections'];
                $prev_sections = $input['prev_sections'] ?? [];
                
                // Original logic for differential update
                $add_result    = array_diff($sections, $prev_sections);
                $delete_result = array_diff($prev_sections, $sections);
                
                $class_array = array(
                    'id'    => $id,
                    'class' => $input['class'],
                );

                if (!empty($add_result)) {
                    $this->classsection_model->add($class_array, $add_result);
                } else {
                    $this->classsection_model->update($class_array);
                }

                if (!empty($delete_result)) {
                    $this->classsection_model->remove($id, $delete_result);
                }

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('update_message')
                    ]));
            }
        }

        // GET behavior: Return single class details
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'class'  => $class_data[0] ?? null,
                'sectionlist' => $this->section_model->get()
            ]));
    }

    public function get_section($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!$id) {
            $input = $this->_get_input();
            $id = $input['id'] ?? null;
        }

        if (!$id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Class ID is required']));
        }

        $sections = $this->class_model->get_section($id);
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'   => 'success',
                'sections' => $sections
            ]));
    }

}

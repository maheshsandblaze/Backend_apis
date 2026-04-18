<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Subjectgroup extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('subjectgroup_model');
        $this->load->model('class_model');
        $this->load->model('subject_model');
        $this->load->model('studentsubjectgroup_model');
        $this->load->model('subjecttimetable_model');
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
                'name', $this->lang->line('name'), array(
                    'required',
                    array('class_exists', array($this->subjectgroup_model, 'class_exists')),
                )
            );
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('subject[]', $this->lang->line('subject'), 'trim|required|xss_clean');
            $this->form_validation->set_rules(
                'sections[]', $this->lang->line('section'), array(
                    'required',
                    array('check_section_exists', array($this->subjectgroup_model, 'check_section_exists')),
                )
            );

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $session     = $this->setting_model->getCurrentSession();
                $class_array = array(
                    'name'        => $input['name'],
                    'session_id'  => $session,
                    'description' => $input['description'] ?? '',
                );
                $subject  = $input['subject'];
                $sections = $input['sections'];

                $insert_id = $this->subjectgroup_model->add($class_array, $subject, $sections);
                
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
                            'message' => 'Failed to add subject group'
                        ]));
                }
            }
        }

        // GET behavior: List all
        $subjectgroupList = $this->subjectgroup_model->getByID();
        $classlist        = $this->class_model->get();
        $subjectlist      = $this->subject_model->get();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'           => 'success',
                'subjectgroupList' => $subjectgroupList,
                'classlist'        => $classlist,
                'subjectlist'      => $subjectlist
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
            $result = $this->subjectgroup_model->remove($id);
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
                        'message' => 'Failed to delete subject group'
                    ]));
            }
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
    }
    
    
    public function subjectGroupDetails_get($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        // Check request method
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
                    "message" => "Subject Group ID is required"
                ]));
        }
    
        // Fetch subject group details
        $subjectgroup = $this->subjectgroup_model->getByID($id);
    
        if (!$subjectgroup) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Subject Group not found"
                ]));
        }
    
        // Fetch required lists
        $classlist   = $this->class_model->get();
        $subjectlist = $this->subject_model->get();
    
        // Final API Response
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => true,
                "message" => "Subject Group Details Fetched Successfully",
                "data" => [
                    "subjectgroup" => $subjectgroup,
                    "classlist"    => $classlist,
                    "subjectlist"  => $subjectlist
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

        $subjectgroup = $this->subjectgroup_model->getByID($id);
        if (empty($subjectgroup)) {
             return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Subject group not found']));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules(
                'name', $this->lang->line('name'), array(
                    'required',
                    array('class_exists', array($this->subjectgroup_model, 'class_exists')),
                )
            );
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('sections[]', $this->lang->line('section'), array(
                    'required',
                    array('check_section_exists', array($this->subjectgroup_model, 'check_section_exists')),
                )
            );
            $this->form_validation->set_rules('subject[]', $this->lang->line('subject'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                 return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $old_sections = array();
                $old_subjects = array();
                
                if (!empty($subjectgroup[0]->sections)) {
                    foreach ($subjectgroup[0]->sections as $value) {
                        $old_sections[] = $value->class_section_id;
                    }
                }
                if (!empty($subjectgroup[0]->group_subject)) {
                    foreach ($subjectgroup[0]->group_subject as $value) {
                        $old_subjects[] = $value->subject_id;
                    }
                }

                $class_array = array(
                    'id'          => $id,
                    'name'        => $input['name'],
                    'description' => $input['description'] ?? '',
                );
                
                $subject         = $input['subject'];
                $sections        = $input['sections'];
                $delete_sections = array_diff($old_sections, $sections);
                $add_sections    = array_diff($sections, $old_sections);
                $delete_subjects = array_diff($old_subjects, $subject);
                $add_subjects    = array_diff($subject, $old_subjects);
                
                $result = $this->subjectgroup_model->edit($class_array, $delete_sections, $add_sections, $delete_subjects, $add_subjects);
                
                if ($result) {
                    return $this->output
                        ->set_status_header(200)
                        ->set_output(json_encode([
                            'status'  => 'success',
                            'message' => $this->lang->line('update_message')
                        ]));
                } else {
                     return $this->output
                        ->set_status_header(500)
                        ->set_output(json_encode([
                            'status'  => 'fail',
                            'message' => 'Failed to update subject group'
                        ]));
                }
            }
        }

        // GET behavior: Return single subject group details
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'       => 'success',
                'subjectgroup' => $subjectgroup[0]
            ]));
    }

    public function addsubjectgroup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('subject_group_id', $this->lang->line('subject_group'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_ids[]', 'Students', 'required');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {
            $student_session_id     = $input['student_session_id'] ?? [];
            $subject_group_id       = $input['subject_group_id'];
            $student_ids            = $input['student_ids'] ?? [];
            $delete_student         = array_diff($student_ids, $student_session_id);

            if (!empty($student_session_id)) {
                foreach ($student_session_id as $value) {
                    $insert_array = array(
                        'student_session_id' => $value,
                        'subject_group_id'   => $subject_group_id,
                    );
                    $this->studentsubjectgroup_model->add($insert_array);
                }
            }

            if (!empty($delete_student)) {
                $this->studentsubjectgroup_model->delete($subject_group_id, $delete_student);
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message')
                ]));
        }
    }

    public function getGroupByClassandSection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input      = $this->_get_input();
        $class_id   = $input['class_id'] ?? null;
        $section_id = $input['section_id'] ?? null;
        $session_id = $input['session_id'] ?? null;

        if (!$class_id || !$section_id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'class_id and section_id are required']));
        }

        $data = $this->subjectgroup_model->getGroupByClassandSection($class_id, $section_id, $session_id);
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }

    public function getSubjectByClassandSectionDate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input      = $this->_get_input();
        $date_str   = $input['date'] ?? null;
        $class_id   = $input['class_id'] ?? null;
        $section_id = $input['section_id'] ?? null;

        if (!$date_str || !$class_id || !$section_id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'date, class_id, and section_id are required']));
        }

        $date = date('Y-m-d', $this->customlib->datetostrtotime($date_str));
        $day  = date('l', strtotime($date));
        $data = $this->subjecttimetable_model->getSubjectByClassandSectionDay($class_id, $section_id, $day);
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }

    public function getAllSubjectByClassandSection()
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

        $data = $this->subjectgroup_model->getAllsubjectByClassSection($class_id, $section_id);
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }

    public function getSubjectByClassandSection()
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

        $data = $this->subjecttimetable_model->getSubjectByClassandSection($class_id, $section_id);
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }

    public function getGroupsubjects()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input            = $this->_get_input();
        $subject_group_id = $input['subject_group_id'] ?? null;
        $session_id       = $input['session_id'] ?? null;

        if (!$subject_group_id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'subject_group_id is required']));
        }

        $data = $this->subjectgroup_model->getGroupsubjects($subject_group_id, $session_id);      
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }
}

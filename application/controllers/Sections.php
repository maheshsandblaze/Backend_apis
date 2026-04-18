<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sections extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('section_model');
        $this->load->model('class_model');
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
                'section',
                $this->lang->line('section'),
                'trim|required|xss_clean'
            );

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            }

            $section_name = trim($input['section']);
            $section_id   = $input['id'] ?? null;

            /* =========================
           ✅ DUPLICATE CHECK (UPDATE SAFE)
        ========================== */
            $this->db->where('section', $section_name);

            if (!empty($section_id)) {
                $this->db->where('id !=', $section_id); // exclude current record
            }

            $exists = $this->db->get('sections')->row(); // 🔁 check your table name

            if ($exists) {
                return $this->output
                    ->set_status_header(409)
                    ->set_output(json_encode([
                        'status'  => 'fail',
                        'message' => 'Section already exists'
                    ]));
            }

            /* =========================
           INSERT / UPDATE
        ========================== */
            $data = array(
                'id'      => $section_id,
                'section' => $section_name,
            );

            $insert_id = $this->section_model->add($data);

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
                        'message' => 'Failed to add section'
                    ]));
            }
        }

        // GET → LIST
        $section_result = $this->section_model->get();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 'success',
                'sectionlist' => $section_result
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

        $section = $this->section_model->get($id);

        if (!$section) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Section not found']));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'section' => $section
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
            $result = $this->section_model->remove($id);
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
                        'message' => 'Failed to delete section'
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

        $section = $this->section_model->get($id);
        if (!$section) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Section not found']));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('section', $this->lang->line('section'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $data = array(
                    'id'      => $id,
                    'section' => $input['section'],
                );
                $this->section_model->add($data);

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
                'status'  => 'success',
                'section' => $section
            ]));
    }

    // public function getByClass()
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }

    //     $input      = $this->_get_input();
    //     $class_id   = $input['class_id'] ?? $this->input->get('class_id');

    //     if (!$class_id) {
    //          return $this->output
    //             ->set_status_header(400)
    //             ->set_output(json_encode(['status' => 'fail', 'message' => 'class_id is required']));
    //     }

    //     $data = $this->section_model->getClassBySection($class_id);

    //     return $this->output
    //         ->set_status_header(200)
    //         ->set_output(json_encode([
    //             'status' => 'success',
    //             'data'   => $data
    //         ]));
    // }


    public function getByClass($class_id = null)
    {
        // Handle Preflight OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only GET requests
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Only GET method is allowed'
                ]));
        }

        // Validate class_id
        if (empty($class_id)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'class_id is required in URL'
                ]));
        }

        // Fetch Sections by Class ID
        $data = $this->section_model->getClassBySection($class_id);

        // Return Response
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }



    public function getClassTeacherSection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input      = $this->_get_input();
        $class_id   = $input['class_id'] ?? $this->input->get('class_id');

        if (!$class_id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'class_id is required']));
        }

        $userdata = $this->customlib->getUserData();
        $role_id  = $userdata["role_id"] ?? null;

        if (isset($role_id) && ($role_id == 2) && (isset($userdata["class_teacher"]) && $userdata["class_teacher"] == "yes")) {
            $id    = $userdata["id"];
            // Original logic for checking if class teacher or subject teacher
            $query = $this->db->where("staff_id", $id)->where("class_id", $class_id)->get("class_teacher");

            if ($query->num_rows() > 0) {
                $data = $this->section_model->getClassTeacherSection($class_id);
            } else {
                $data = $this->section_model->getSubjectTeacherSection($class_id, $id);
            }
        } else {
            $data = $this->section_model->getClassBySection($class_id);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }
}

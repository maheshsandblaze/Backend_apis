<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Stdtransfer extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('classteacher_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->model('session_model');
        $this->load->model('student_model');
        $this->load->model('studentsession_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
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
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('class_promote_id', $this->lang->line('class'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('section_promote_id', $this->lang->line('section'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('session_id', $this->lang->line('promote_in_session'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $class           = $input['class_id'];
                $section         = $input['section_id'];
                $session         = $input['session_id'];
                $class_promote   = $input['class_promote_id'];
                $section_promote = $input['section_promote_id'];

                $resultlist = $this->student_model->searchNonPromotedStudents(
                    $class, 
                    $section, 
                    $session, 
                    $class_promote, 
                    $section_promote
                );

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'           => 'success',
                        'resultlist'       => $resultlist,
                        'class_id'         => $class,
                        'section_id'       => $section,
                        'class_promote_id' => $class_promote,
                        'section_promote_id' => $section_promote,
                        'session_id'       => $session
                    ]));
            }
        }

        // GET behavior: Return initial data
        $class          = $this->class_model->get('', $classteacher = 'yes');
        $session_result = $this->session_model->get();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 'success',
                'classlist'   => $class,
                'sessionlist' => $session_result,
                'sch_setting' => $this->sch_setting_detail
            ]));
    }

    public function promote()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('session_id', $this->lang->line('session'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('class_promote_id', $this->lang->line('class'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('section_promote_id', $this->lang->line('section'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_list[]', $this->lang->line('student'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {
            $student_list    = $input['student_list'];
            $current_session = $this->setting_model->getCurrentSession();
            $class_post      = $input['class_post'] ?? null;
            $section_post    = $input['section_post'] ?? null;

            // echo    "<pre>";
            // print_r($input);exit;

            if (!empty($student_list) && isset($student_list)) {
                foreach ($student_list as $value) {
                    $student_id     = $value;
                    $result         = $input['result_' . $value] ?? null;
                    $session_status = $input['next_working_' . $value] ?? null;

                    if ($result == "pass" && $session_status == "countinue") {
                        // Promote to next class
                        $promoted_class   = $input['class_promote_id'];
                        $promoted_section = $input['section_promote_id'];
                        $promoted_session = $input['session_id'];
                        
                        $data_new = array(
                            'student_id'     => $student_id,
                            'class_id'       => $promoted_class,
                            'section_id'     => $promoted_section,
                            'session_id'     => $promoted_session,
                            'transport_fees' => 0,
                            'fees_discount'  => 0,
                        );
                        $this->student_model->add_student_session($data_new);
                        
                    } elseif ($result == "fail" && $session_status == "countinue") {
                        // Keep in same class
                        $promoted_session = $input['session_id'];
                        
                        $data_new = array(
                            'student_id'     => $student_id,
                            'class_id'       => $class_post,
                            'section_id'     => $section_post,
                            'session_id'     => $promoted_session,
                            'transport_fees' => 0,
                            'fees_discount'  => 0,
                        );
                        $this->student_model->add_student_session($data_new);
                        
                    } elseif ($session_status == "leave") {
                        // Mark as alumni
                        $leave_student = array(
                            'is_leave'   => 1,
                            'session_id' => $current_session,
                            'student_id' => $student_id,
                            'class_id'   => $class_post,
                            'section_id' => $section_post,
                        );
                        $this->studentsession_model->updatePromote($leave_student);

                        $alumni_data = array(
                            'student_id' => $student_id,
                            'is_alumni'  => 1,
                        );
                        $this->student_model->alumni_student_status($alumni_data);
                    }
                }
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message')
                ]));
        }
    }

}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Common extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function parents()
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        $sql        = "SELECT * FROM `users` WHERE role='parent'";
        $query      = $this->db->query($sql);
        $par_result = $query->result();
        foreach ($par_result as $res_key => $res_value) {
            $ids = explode(",", $res_value->childs);
            $this->db->where_in('id', $ids);
            $this->db->update('students', array('parent_id' => $res_value->id));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function getSudentSessions()
    {
        $student_id          = $this->customlib->getStudentSessionUserID();
        $session             = $this->session_model->getStudentAcademicSession($student_id);
        $data                = array();
        $session_array       = $this->session->has_userdata('session_array');
        $data['sessionData'] = array('session_id' => 0);
        if ($session_array) {
            $data['sessionData'] = $this->session->userdata('session_array');
        } else {
            $setting             = $this->setting_model->get();
            $data['sessionData'] = array('session_id' => $setting[0]['session_id']);
        }
        $data['sessionList'] = $session;
        $this->load->view('partial/_session', $data);
    }

    public function getStudentSessionClasses()
    {
        // ===============================
        // CORS
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ===============================
        // Only GET
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ===============================
        // TOKEN VALIDATION
        // ===============================
        $auth = $this->auth->validate_user();

        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ]));
        }

        $login_id = $auth->login_id;
        $role     = $auth->role; // assuming role stored in token

        $studentclasses = [];


        $student = $this->student_model->get($login_id);

        // echo "<pre>";print_r($student);exit;

        $parent_id = $auth->parent_id;

        // ===============================
        // FETCH DATA BASED ON ROLE
        // ===============================

        // echo "<pre>";print_r($role);exit;
        if ($role == "student") {

            $studentclasses = $this->studentsession_model
                ->searchMultiClsSectionByStudent($login_id);
        } elseif ($role == "parent") {

            $studentclasses = $this->student_model
                ->getParentChilds($parent_id);
        } else {

            return $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Access denied'
                ]));
        }

        // ===============================
        // SCHOOL SETTINGS
        // ===============================
        $sch_setting = $this->sch_setting_detail;

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'role'            => $role,
                    'studentclasses'  => $studentclasses ?? [],
                    'school_settings' => $sch_setting
                ]
            ]));
    }

    public function getStudentClass()
    {
        // Handle CORS preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Get JSON input
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['student_session_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'student_session_id is required'
                ]));
        }

        $student_session_id = $input['student_session_id'];

        // Fetch student
        $student = $this->student_model->getByStudentSession($student_session_id);

        if (!$student) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Student not found'
                ]));
        }

        // Get logged user
        $logged_In_User = $this->customlib->getLoggedInUserData();

        // echo "<pre>";print_r($logged_In_User);exit;

        $logged_In_User['student_id'] = $student['id'];


         $headers = getallheaders();
        $token   = $headers['Authorization'] ?? '';

        //    print_r($token);exit;

        if (!empty($token)) {
            // Remove "Bearer " if exists
            $token = str_replace('Bearer ', '', $token);

            // Delete token from database
            $this->db->where('token', $token);
            $this->db->update('admin_authentication',['login_id' => $student['id']]);

            // echo $this->db->last_query();exit;
        }


        // Remove old session
        if ($this->session->has_userdata('current_class')) {
            $this->session->unset_userdata('current_class');
        }

        // Set student session
        $this->session->set_userdata('student', $logged_In_User);

        $student_current_class = [
            'class_id'           => $student['class_id'],
            'section_id'         => $student['section_id'],
            'student_session_id' => $student['student_session_id']
        ];

        $this->session->set_userdata('current_class', $student_current_class);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Class changed successfully',
                'data' => $student_current_class
            ]));


    }

    public function getStudentClassNew()
    {
        // ===============================
        // CORS
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        /* =========================
           ALLOW ONLY POST
        ========================== */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        /* =========================
           TOKEN AUTH
        ========================== */
        $auth = $this->auth->validate_user();

        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthorized'
                ]));
        }

        /* =========================
           GET JSON INPUT
        ========================== */
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['student_session_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'student_session_id is required'
                ]));
        }

        $student_session_id = $input['student_session_id'];

        /* =========================
           FETCH STUDENT
        ========================== */
        $student = $this->student_model
            ->getByStudentSession($student_session_id);

        if (!$student) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Student not found'
                ]));
        }

        /* =========================
           OPTIONAL: SAVE SELECTED CLASS IN DB
           (Recommended instead of session)
        ========================== */
        // Example:
        // $this->user_model->updateSelectedClass(
        //     $auth->staff_id,
        //     $student_session_id
        // );

        /* =========================
           RETURN RESPONSE
        ========================== */
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Class changed successfully',
                'data' => [
                    'student_id'         => $student['id'],
                    'class_id'           => $student['class_id'],
                    'section_id'         => $student['section_id'],
                    'student_session_id' => $student['student_session_id']
                ]
            ]));
    }

    public function getAllSession()
    {
        $session             = $this->session_model->getAllSession();
        $data                = array();
        $session_array       = $this->session->has_userdata('session_array');
        $data['sessionData'] = array('session_id' => 0);
        if ($session_array) {
            $data['sessionData'] = $this->session->userdata('session_array');
        } else {
            $setting             = $this->setting_model->get();
            $data['sessionData'] = array('session_id' => $setting[0]['session_id']);
        }
        $data['sessionList'] = $session;
        $this->load->view('partial/_session', $data);
    }

    public function updateSession()
    {
        $role = $this->customlib->getUserRole();
        // print_r($this->session->userdata('student'));
        $redirect_url = site_url('site/userlogin');
        if ($role == "teacher") {
            $redirect_url = site_url('teacher/teacher/dashboard');
        } elseif ($role == 'accountant') {
            $redirect_url = site_url('accountant/accountant/dashboard');
        }

        $session       = $this->input->post('popup_session');
        $session_array = $this->session->has_userdata('session_array');
        if ($session_array) {
            $this->session->unset_userdata('session_array');
        }
        $session = $this->session_model->get($session);

        $session_array = array('session_id' => $session['id'], 'session' => $session['session']);
        $this->session->set_userdata('session_array', $session_array);

        if ($role == "student"  || $role == "parent") {
            $session                 = $this->input->post('popup_session');
            $session_Array           = $this->session->userdata('student');
            $student_id              = $session_Array['student_id'];
            $student_display_session = $this->studentsession_model->searchActiveClassSectionStudent($student_id, $session);
            $student_current_class   = array('student_session_id' => $student_display_session->id, 'class_id' => $student_display_session->class_id, 'section_id' => $student_display_session->section_id);
            $this->session->unset_userdata('current_class');
            $this->session->set_userdata('current_class', $student_current_class);
        }
        // exit();
        echo json_encode(array('status' => 1, 'message' => $this->lang->line('session_changed_successfully'), 'redirect_url' => $redirect_url));
    }
}

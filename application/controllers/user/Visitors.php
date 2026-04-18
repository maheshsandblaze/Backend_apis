<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Visitors extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
    }

    public function index()
    {
        // ===============================
        // HANDLE PREFLIGHT
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ===============================
        // ONLY GET METHOD
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
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ]));
        }

        $student_id = $auth->login_id;

        // ===============================
        // STUDENT DETAILS
        // ===============================
        $student = $this->student_model->get($student_id);

        if (!$student) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student not found'
                ]));
        }

        $student_session_id = $student['student_session_id'];

        // ===============================
        // FETCH VISITOR LIST
        // ===============================
        $visitor_list = $this->visitors_model
            ->visitorbystudentid($student_session_id);

            // echo "<pre>";print_r($visitor_list);exit;

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'student_id'        => $student_id,
                    'student_session_id' => $student_session_id,
                    'visitor_list'      => $visitor_list ?? []
                ]
            ]));
    }
    public function download($id)
    {
        $visitorlist = $this->visitors_model->visitors_list($id);
        $this->media_storage->filedownload($visitorlist['image'], "./uploads/front_office/visitors");
    }
}

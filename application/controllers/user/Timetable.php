<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Timetable extends Public_Controller {

    public function __construct() {
        parent::__construct();
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
            ->set_content_type('application/json')
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

    $class_id   = $student['class_id'];
    $section_id = $student['section_id'];

    // ===============================
    // GET TIMETABLE
    // ===============================
    $days = $this->customlib->getDaysname();
    $days_record = [];

    foreach ($days as $day_key => $day_value) {
        $days_record[$day_value] =
            $this->subjecttimetable_model
            ->getparentSubjectByClassandSectionDay(
                $class_id,
                $section_id,
                $day_key
            );
    }

    // ===============================
    // FINAL RESPONSE
    // ===============================
    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status' => true,
            'data'   => [
                'student_id' => $student_id,
                'class_id'   => $class_id,
                'section_id' => $section_id,
                'timetable'  => $days_record
            ]
        ]));
}
 
}
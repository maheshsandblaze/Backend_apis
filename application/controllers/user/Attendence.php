<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Attendence extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getdaysubattendence()
    {
        $date = $this->input->post('date');
        $date = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date')));

        $attendencetypes = $this->attendencetype_model->get();
        $timestamp       = strtotime($date);
        $day             = date('l', $timestamp);

        $student_id                    = $this->customlib->getStudentSessionUserID();
        $student                       = $this->student_model->get($student_id);
        $student_current_class         = $this->customlib->getStudentCurrentClsSection();
        $student_session_id            = $student_current_class->student_session_id;
        $class_id                      = $student_current_class->class_id;
        $section_id                    = $student_current_class->section_id;
        $result['attendencetypeslist'] = $attendencetypes;
        $result['attendence']          = $this->studentsubjectattendence_model->studentAttendanceByDate($class_id, $section_id, $day, $date, $student_session_id);
        $result_page                   = $this->load->view('user/attendence/_getdaysubattendence', $result, true);
        echo json_encode(array('status' => 1, 'result_page' => $result_page));
    }

    public function Index()
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
        // Token validation
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

        // ===============================
        // SETTINGS
        // ===============================
        $setting_result = $this->setting_model->get();

        if (empty($setting_result)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Settings not found'
                ]));
        }

        $setting = $setting_result[0];

        // ===============================
        // STUDENT DATA
        // ===============================
        $student_id = $auth->login_id;

        $student_current_class = $this->student_model->get($student_id);

        if (!$student_current_class) {


            $student_lists = $this->student_model
                ->getParentChilds($auth->login_id);

            //  echo '<pre>';print_r($student_lists);exit;
            $student_current_class = $this->student_model->get($student_lists[0]->id);
        }

        // ===============================
        // LANGUAGE
        // ===============================
        // $student = $this->student_model->get($student_id);
        $language = [];

        if (!empty($student['language_id'])) {
            $language = $this->language_model->get($student_current_class['language_id']);
        }

        // ===============================
        // FINAL DATA STRUCTURE
        // ===============================
        $attendance_data = [
            'title'              => 'Attendance List',
            'attendance_type'    => $setting['attendence_type'], // 0 = Daily, 1 = Subject
            'attendance_view'    => $setting['attendence_type'] ? 'subject' : 'daily',
            'language_shortcode' => $language,
            'resultList'         => [] // empty initially like original
        ];

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $attendance_data
            ]));
    }
    public function getAttendance()
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
        // Token validation
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

        // ===============================
        // GET PARAMETERS
        // ===============================
        $start = $this->input->get('start');
        $end   = $this->input->get('end');

        if (empty($start) || empty($end)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Start and End date required'
                ]));
        }

        // ===============================
        // STUDENT DATA
        // ===============================
        $student_id = $auth->login_id;

        $student_current_class = $this->student_model->get($student_id);

        if (!$student_current_class) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student class not found'
                ]));
        }

        $student_session_id = $student_current_class['student_session_id'];

        // ===============================
        // ATTENDANCE DATA
        // ===============================
        $date = [
            'start' => $start,
            'end'   => $end
        ];

        $attendance_result =
            $this->attendencetype_model
            ->getStudentAttendenceRange($date, $student_session_id);

        $eventdata = [];

        if (!empty($attendance_result)) {

            foreach ($attendance_result as $attendance) {

                $type  = $attendance->type;
                $color = '';
                $event_type = $type;

                switch ($type) {
                    case 'Present':
                        $color = '#27ab00';
                        break;

                    case 'Absent':
                        $color = '#fa2601';
                        break;

                    case 'Late':
                    case 'Late with excuse':
                        $color = '#ffeb00';
                        break;

                    case 'Holiday':
                        $color = '#a7a7a7';
                        break;

                    case 'Half Day':
                        $color = '#fa8a00';
                        break;
                }

                $eventdata[] = [
                    'title'           => $type,
                    'start'           => $attendance->date,
                    'end'             => $attendance->date,
                    'description'     => $attendance->remark,
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
                    'event_type'      => $event_type
                ];
            }
        }

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $eventdata
            ]));
    }

    public function getevents()
    {
        $userdata = $this->customlib->getUserData();
        $result   = $this->calendar_model->getEvents();
        if (!empty($result)) {

            foreach ($result as $key => $value) {

                $event_type = $value["event_type"];

                if ($event_type == 'private') {

                    $event_for = $userdata["id"];
                } else if ($event_type == 'sameforall') {

                    $event_for = $userdata["role_id"];
                } else if ($event_type == 'public') {

                    $event_for = "0";
                } else if ($event_type == 'task') {

                    $event_for = $userdata["id"];
                }
                if ($event_type == 'task') {

                    if (($event_for == $value["event_for"]) && ($value["role_id"] == $userdata["role_id"])) {
                        $eventdata[] = array(
                            'title' => $value["event_title"],
                            'start'                      => $value["start_date"],
                            'end'                        => $value["end_date"],
                            'description'                => $value["event_description"],
                            'id'                         => $value["id"],
                            'backgroundColor'            => $value["event_color"],
                            'borderColor'                => $value["event_color"],
                            'event_type'                 => $value["event_type"],
                        );
                    }
                } else {
                    if ($event_for == $value["event_for"]) {
                        $eventdata[] = array(
                            'title' => $value["event_title"],
                            'start'                      => $value["start_date"],
                            'end'                        => $value["end_date"],
                            'description'                => $value["event_description"],
                            'id'                         => $value["id"],
                            'backgroundColor'            => $value["event_color"],
                            'borderColor'                => $value["event_color"],
                            'event_type'                 => $value["event_type"],
                        );
                    } elseif ($event_type == 'protected') {
                    }
                }
            }

            echo json_encode($eventdata);
        }
    }
}

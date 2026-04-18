<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class approve_leave extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    // public function index()
    // {

    //     if (!$this->rbac->hasPrivilege('approve_leave', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Attendance');
    //     $this->session->set_userdata('sub_menu', 'Attendance/approve_leave');
    //     $class               = $this->class_model->get();
    //     $data['classlist']   = $class;
    //     $data['class_id']    = $class_id    = '';
    //     $data['section_id']  = $section_id  = '';
    //     $data['sch_setting'] = $this->setting_model->getSetting();
    //     $data['results']     = array();

    //     if (isset($_POST['class_id']) && $_POST['class_id'] != '') {
    //         $data['class_id'] = $class_id = $_POST['class_id'];
    //     } else {
    //         $listaudit = $this->apply_leave_model->get(null, null, null);
    //     }

    //     if (isset($_POST['section_id']) && $_POST['section_id'] != '') {
    //         $data['section_id'] = $section_id = $_POST['section_id'];
    //     }
    //     $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
    //     if ($this->form_validation->run() == false) {

    //     } else {
    //         $listaudit = $this->apply_leave_model->get(null, $class_id, $section_id);
    //     }

    //     $data['results'] = $listaudit;

    //     $this->load->view('layout/header');
    //     $this->load->view('admin/approve_leave/index', $data);
    //     $this->load->view('layout/footer');
    // }


    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // =========================
        // METHOD CHECK
        // =========================
        $method = $_SERVER['REQUEST_METHOD'];

        if (!in_array($method, ['GET', 'POST'])) {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // =========================
        // PRIVILEGE CHECK
        // =========================
        // if (!$this->rbac->hasPrivilege('approve_leave', 'can_view')) {
        //     echo json_encode([
        //         'status'  => false,
        //         'message' => 'Access denied'
        //     ]);
        //     return;
        // }

        // =========================
        // GET PARAMETERS
        // =========================
        if ($method == 'GET') {
            $class_id   = $this->input->get('class_id');
            $section_id = $this->input->get('section_id');
        } else {
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
        }

        // =========================
        // GET CLASS LIST
        // =========================
        $classlist = $this->class_model->get();
        $sch_setting = $this->setting_model->getSetting();

        // =========================
        // FETCH LEAVE DATA
        // =========================
        if ($class_id != '' && $section_id != '') {
            $results = $this->apply_leave_model->get(null, $class_id, $section_id);
        } else {
            // echo "comming";exit;
            $results = $this->apply_leave_model->get(null, null, null);
        }

        // =========================
        // RETURN JSON
        // =========================
        echo json_encode([
            'status'       => true,
            'classlist'    => $classlist,
            'class_id'     => $class_id,
            'section_id'   => $section_id,
            'sch_setting'  => $sch_setting,
            'results'      => $results
        ]);
    }


    // public function get_details()
    // {
    //     $userdata = $this->customlib->getUserData();
    //     $role_id  = $userdata["role_id"];
    //     $can_edit = 1;

    //     if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
    //         $myclasssubjects = $this->apply_leave_model->canApproveLeave($userdata["id"], $this->input->post('class_id'), $this->input->post('section_id'));
    //         $can_edit        = $myclasssubjects;
    //     }

    //     if ($can_edit == 0) {

    //         $data = array('status' => 'fail', 'error' => $this->lang->line('not_authoried'));
    //     } else {
    //         $data                 = $this->apply_leave_model->get($_POST['id'], null, null);

    //         $data['leave_status'] = $data['status'];
    //         $data['from_date']    = date($this->customlib->getSchoolDateFormat(), strtotime($data['from_date']));
    //         $data['to_date']      = date($this->customlib->getSchoolDateFormat(), strtotime($data['to_date']));
    //         $data['apply_date']   = date($this->customlib->getSchoolDateFormat(), strtotime($data['apply_date']));
    //     }
    //     echo json_encode($data);
    // }


    public function get_details()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $json = file_get_contents("php://input");
        $request = json_decode($json, true);

        $id = $request['id'] ?? '';
        $class_id = $request['class_id'] ?? '';
        $section_id = $request['section_id'] ?? '';

        $userdata = $this->customlib->getUserData();
        $role_id  = $userdata["role_id"];
        $can_edit = 1;

        if ($role_id == 2 && $userdata["class_teacher"] == "yes") {
            $can_edit = $this->apply_leave_model->canApproveLeave(
                $userdata["id"],
                $class_id,
                $section_id
            );
        }

        if ($can_edit == 0) {

            $response = [
                'status' => 'fail',
                'message' => 'Not authorized'
            ];
        } else {

            $data = $this->apply_leave_model->get($id, null, null);

            $data['leave_status'] = $data['status'];
            $data['from_date']  = date('d-m-Y', strtotime($data['from_date']));
            $data['to_date']    = date('d-m-Y', strtotime($data['to_date']));
            $data['apply_date'] = date('d-m-Y', strtotime($data['apply_date']));

            $response = $data;
        }

        echo json_encode($response);
    }


    // public function add()
    // {
    //     $student_id = '';
    //     $this->form_validation->set_rules('class', $this->lang->line('class'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('section', $this->lang->line('section'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('apply_date', $this->lang->line('apply_date'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('from_date', $this->lang->line('from_date'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('to_date', $this->lang->line('to_date'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('student', $this->lang->line('student'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('leave_status', $this->lang->line('leave_status'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('userfile', $this->lang->line('file'), 'callback_handle_upload[userfile]');
    //     if ($this->form_validation->run() == false) {

    //         $msg = array(
    //             'class'        => form_error('class'),
    //             'section'      => form_error('section'),
    //             'student'      => form_error('student'),
    //             'apply_date'   => form_error('apply_date'),
    //             'from_date'    => form_error('from_date'),
    //             'to_date'      => form_error('to_date'),
    //             'userfile'     => form_error('userfile'),
    //             'leave_status' => form_error('leave_status'),
    //         );

    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {

    //         $student_session_id = $this->apply_leave_model->get_studentsessionId($_POST['class'], $_POST['section'], $_POST['student']);

    //         $img_name = $this->media_storage->fileupload("userfile", "./uploads/student_leavedocuments/");

    //         $data = array(
    //             'apply_date'         => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('apply_date'))),
    //             'from_date'          => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('from_date'))),
    //             'to_date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('to_date'))),
    //             'student_session_id' => $student_session_id['id'],
    //             'reason'             => $this->input->post('message'),
    //             'request_type'       => '1',
    //             'status'             => $this->input->post('leave_status'),
    //         );

    //         if ($data['status'] != 0) {
    //             $data['approve_by'] = $this->customlib->getStaffID();
    //             $data['approve_date'] = date('Y-m-d');
    //         } 

    //         if ($this->input->post('leave_id') == '') {
    //             $data['docs'] = $img_name;
    //             $leave_id     = $this->apply_leave_model->add($data);
    //             $data['id']   = $leave_id;
    //         } else {
    //             $data['id'] = $this->input->post('leave_id');

    //             $leave_list = $this->apply_leave_model->get($this->input->post('leave_id'));

    //             if (isset($_FILES["userfile"]) && $_FILES['userfile']['name'] != '' && (!empty($_FILES['userfile']['name']))) {
    //                 $img_name = $img_name;
    //             } else {
    //                 $img_name = $leave_list['docs'];
    //             }

    //             $data['docs'] = $img_name;

    //             if (isset($_FILES["userfile"]) && $_FILES['userfile']['name'] != '' && (!empty($_FILES['userfile']['name']))) {
    //                 if ($leave_list['docs'] != '') {
    //                     $this->media_storage->filedelete($leave_list['docs'], "uploads/student_leavedocuments");
    //                 }
    //             }

    //             $this->apply_leave_model->add($data);
    //         }

    //         $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
    //     }

    //     echo json_encode($array);
    // }


    public function addold()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $this->form_validation->set_rules('class', 'Class', 'required');
        $this->form_validation->set_rules('section', 'Section', 'required');
        $this->form_validation->set_rules('student', 'Student', 'required');
        $this->form_validation->set_rules('apply_date', 'Apply Date', 'required');
        $this->form_validation->set_rules('from_date', 'From Date', 'required');
        $this->form_validation->set_rules('to_date', 'To Date', 'required');
        $this->form_validation->set_rules('leave_status', 'Leave Status', 'required');

        if ($this->form_validation->run() == false) {

            echo json_encode([
                "status" => "fail",
                "error" => validation_errors()
            ]);
            return;
        }

        $student_session_id = $this->apply_leave_model->get_studentsessionId(
            $this->input->post('class'),
            $this->input->post('section'),
            $this->input->post('student')
        );

        $img_name = $this->media_storage->fileupload("userfile", "../uploads/student_leavedocuments/");

        $data = array(
            'apply_date' => date('Y-m-d', strtotime($this->input->post('apply_date'))),
            'from_date' => date('Y-m-d', strtotime($this->input->post('from_date'))),
            'to_date' => date('Y-m-d', strtotime($this->input->post('to_date'))),
            'student_session_id' => $student_session_id['id'],
            'reason' => $this->input->post('message'),
            'request_type' => '1',
            'status' => $this->input->post('leave_status'),
            'docs' => $img_name
        );

        $leave_id = $this->apply_leave_model->add($data);

        echo json_encode([
            "status" => "success",
            "message" => "Leave added successfully",
            "leave_id" => $leave_id,
            "data"      => $data
        ]);
    }


    public function add()
    {
        /* ======================
       CORS
    ====================== */
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        /* ======================
       ALLOW ONLY POST
    ====================== */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        /* ======================
       VALIDATION RULES
    ====================== */

        $this->form_validation->set_rules('class', 'Class', 'required');
        $this->form_validation->set_rules('section', 'Section', 'required');
        $this->form_validation->set_rules('student', 'Student', 'required');
        $this->form_validation->set_rules('apply_date', 'Apply Date', 'required');
        $this->form_validation->set_rules('from_date', 'From Date', 'required');
        $this->form_validation->set_rules('to_date', 'To Date', 'required');
        $this->form_validation->set_rules('leave_status', 'Leave Status', 'required');
        $this->form_validation->set_rules('userfile', 'File', 'callback_handle_upload[userfile]');

        if ($this->form_validation->run() == false) {

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error' => [
                        'class' => form_error('class'),
                        'section' => form_error('section'),
                        'student' => form_error('student'),
                        'apply_date' => form_error('apply_date'),
                        'from_date' => form_error('from_date'),
                        'to_date' => form_error('to_date'),
                        'leave_status' => form_error('leave_status'),
                        'userfile' => form_error('userfile')
                    ]
                ]));
        }

        /* ======================
       GET STUDENT SESSION
    ====================== */

        $student_session_id = $this->apply_leave_model->get_studentsessionId(
            $this->input->post('class'),
            $this->input->post('section'),
            $this->input->post('student')
        );

        /* ======================
       FILE UPLOAD
    ====================== */

        $img_name = '';
        if (isset($_FILES['userfile']) && !empty($_FILES['userfile']['name'])) {
            $img_name = $this->media_storage->fileupload("userfile", "../uploads/student_leavedocuments/");
        }

        /* ======================
       PREPARE DATA
    ====================== */

        $data = [
            'apply_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('apply_date'))),
            'from_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('from_date'))),
            'to_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('to_date'))),
            'student_session_id' => $student_session_id['id'],
            'reason' => $this->input->post('message'),
            'request_type' => '1',
            'status' => $this->input->post('leave_status')
        ];

        /* ======================
       APPROVE INFO
    ====================== */

        if ($data['status'] != 0) {
            $data['approve_by'] = $this->input->post('approve_by') ?? $this->customlib->getStaffID();
            $data['approve_date'] = date('Y-m-d');
        }

        /* ======================
       ADD / UPDATE
    ====================== */

        if ($this->input->post('leave_id') == '') {

            $data['docs'] = $img_name;
            $leave_id = $this->apply_leave_model->add($data);
        } else {

            $leave_id = $this->input->post('leave_id');
            $data['id'] = $leave_id;

            $leave_list = $this->apply_leave_model->get($leave_id);

            if ($img_name != '') {

                if ($leave_list['docs'] != '') {
                    $this->media_storage->filedelete($leave_list['docs'], "../uploads/student_leavedocuments");
                }

                $data['docs'] = $img_name;
            } else {

                $data['docs'] = $leave_list['docs'];
            }

            // echo "<pre>";print_r($data);echo "</pre>";exit;

            $this->apply_leave_model->add($data);
        }

        /* ======================
       RESPONSE
    ====================== */

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Leave saved successfully',
                'leave_id' => $leave_id
            ]));
    }

    public function searchByClassSection($class_id, $student_id)
    {
        $section_id          = $_REQUEST['section_id'];
        $resultlist          = $this->student_model->searchByClassSection($class_id, $section_id);
        $data['resultlist']  = $resultlist;
        $data['select_id']   = $student_id;
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('admin/approve_leave/_student_list', $data);
    }

    public function status()
    {
        $userdata = $this->customlib->getUserData();
        $role_id  = $userdata["role_id"];
        $can_edit = 1;

        if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
            $myclasssubjects = $this->apply_leave_model->canApproveLeave($userdata["id"], $this->input->post('class_id'), $this->input->post('section_id'));
            $can_edit        = $myclasssubjects;
        }

        if ($can_edit == 0) {
            $msg   = array('leave' => $this->lang->line('not_authoried'));
            $array = array('status' => 0, 'error' => $this->lang->line('not_authoried'));
        } else {
            if ($_POST['status'] == 1) {
                $data['approve_by'] = $this->customlib->getStaffID();
            } else {
                $data['approve_by'] = 0;
            }

            $data['status'] = $_POST['status'];
            $this->db->where('id', $_POST['id']);
            $this->db->update('student_applyleave', $data);
            $msg   = array('leave' => $this->lang->line('success_message'));
            $array = array('status' => 1, 'success' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    // public function remove_leave()
    // {
    //     $userdata = $this->customlib->getUserData();
    //     $role_id  = $userdata["role_id"];
    //     $can_edit = 1;

    //     if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
    //         $myclasssubjects = $this->apply_leave_model->canApproveLeave($userdata["id"], $this->input->post('class_id'), $this->input->post('section_id'));
    //         $can_edit        = $myclasssubjects;
    //     }

    //     if ($can_edit == 0) {
    //         $array = array('status' => 0, 'error' => $this->lang->line('not_authoried'));
    //     } else {
    //         $row = $this->apply_leave_model->get($_POST['id']);
    //         if ($row['docs'] != '') {
    //             $this->media_storage->filedelete($row['docs'], "uploads/student_leavedocuments/");
    //         }

    //         $this->apply_leave_model->remove_leave($_POST['id']);
    //         $array = array('status' => 1, 'success' => $this->lang->line('delete_message'));
    //     }
    //     echo json_encode($array);
    // }


    public function remove_leave()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read JSON body
        $json = file_get_contents("php://input");
        $request = json_decode($json, true);

        $id = $request['id'] ?? '';
        $class_id = $request['class_id'] ?? '';
        $section_id = $request['section_id'] ?? '';

        $userdata = $this->customlib->getUserData();
        $role_id  = $userdata["role_id"];
        $can_edit = 1;

        // Check permission
        if (isset($role_id) && $role_id == 2 && $userdata["class_teacher"] == "yes") {
            $can_edit = $this->apply_leave_model->canApproveLeave(
                $userdata["id"],
                $class_id,
                $section_id
            );
        }

        if ($can_edit == 0) {

            $response = [
                "status" => 0,
                "error" => $this->lang->line('not_authoried')
            ];
        } else {

            $row = $this->apply_leave_model->get($id);

            if (!empty($row['docs'])) {
                $this->media_storage->filedelete($row['docs'], "uploads/student_leavedocuments/");
            }

            $this->apply_leave_model->remove_leave($id);

            $response = [
                "status" => 1,
                "success" => $this->lang->line('delete_message')
            ];
        }

        echo json_encode($response);
    }


    public function download($id)
    {
        $approve_leave = $this->apply_leave_model->get($id);
        $this->media_storage->filedownload($approve_leave['docs'], "../uploads/student_leavedocuments");
    }

    public function handle_upload($str, $var)
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }

                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_extension_error_uploading_image'));
                return false;
            }

            return true;
        }
        return true;
    }
}

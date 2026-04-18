<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentdairy extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->load->library("customlib");
        $this->load->model("studentdairy_model");
        $this->load->model("staff_model");
        $this->load->model("student_model");
        $this->load->model("filetype_model");
    }

    public function index()
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
        // STUDENT DATA
        // ===============================
        $student_id = $auth->login_id;

        $student = $this->student_model->get($student_id);

        if (!$student) {
            // return $this->output
            //     ->set_status_header(404)
            //     ->set_output(json_encode([
            //         'status'  => false,
            //         'message' => 'Student not found'
            //     ]));

            $student_lists = $this->student_model
                ->getParentChilds($auth->login_id);

            //  echo '<pre>';print_r($student_lists);exit;
            $student = $this->student_model->get($student_lists[0]->id);
        }

        $class_id   = $student['class_id'];
        $section_id = $student['section_id'];
        $student_session_id = $student['student_session_id'];

        // ===============================
        // FETCH STUDENT DAIRY
        // ===============================
        $studentdairylist = $this->studentdairy_model
            ->getStudentstudentdairyWithStatus($class_id, $section_id, $student_session_id);

        // ===============================
        // FORMAT DATE (Optional - Recommended)
        // ===============================
        if (!empty($studentdairylist)) {
            foreach ($studentdairylist as &$row) {
                if (!empty($row['date'])) {
                    $row['date'] = date('d-m-Y', strtotime($row['date']));
                }
            }
        }

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'title' => 'Student Diary List',
                    'student_id' => $student_id,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'studentdairylist' => $studentdairylist ?? []
                ]
            ]));
    }

    public function upload_docs()
    {
        $studentdairy_id         = $_REQUEST['studentdairy_id'];
        $userdata            = $this->customlib->getLoggedInUserData();
        $student_id          = $userdata["student_id"];
        $data['studentdairy_id'] = $studentdairy_id;
        $data['student_id']  = $student_id;
        $is_required         = $this->studentdairy_model->check_assignment($studentdairy_id, $student_id);
        $this->form_validation->set_rules('message', $this->lang->line('message'), 'trim|required|xss_clean');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $this->form_validation->set_rules('file', $this->lang->line('attach_document'), 'trim|xss_clean|callback_handle_upload[' . $is_required . ']');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'message' => form_error('message'),
                'file'    => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data['message'] = $this->input->post('message');

            $img_name = $this->media_storage->fileupload("file", "./uploads/studentdairy/assignment/");
            if ($img_name != '') {
                $data['docs'] = $img_name;
            }

            $this->studentdairy_model->upload_docs($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function studentdairy_detail($id = null)
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

        if (empty($id)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student diary ID is required'
                ]));
        }

        $student_id = $auth->login_id;

        // ===============================
        // FETCH RECORD
        // ===============================
        $result = $this->studentdairy_model->getRecord($id);

        if (!$result) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Record not found'
                ]));
        }

        // ===============================
        // OPTIONAL: DATE FORMAT
        // ===============================
        if (!empty($result['date'])) {
            $result['date'] = date('d-m-Y', strtotime($result['date']));
        }

        // ===============================
        // SETTINGS (If Needed)
        // ===============================
        $settingresult = $this->setting_model->getSetting();
        $superadmin_restriction = $settingresult->superadmin_restriction ?? 0;

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'title'                    => 'Student Assessment',
                    'studentdairy_id'          => $id,
                    'superadmin_restriction'   => $superadmin_restriction,
                    'result'                   => $result
                ]
            ]));
    }

    public function download($id)
    {
        $studentdairy = $this->studentdairy_model->getRecord($id);
        $this->media_storage->filedownload($studentdairy['document'], "./uploads/homework");
    }

    public function assigmnetDownload($id)
    {
        $userdata     = $this->customlib->getLoggedInUserData();
        $student_id   = $userdata["student_id"];
        $studentdairydocs = $this->studentdairy_model->get_studentdairyDocByIdStdid($id, $student_id);
        $this->media_storage->filedownload($studentdairydocs[0]['docs'], "./uploads/studentdairy/assignment");
    }

    public function handle_upload($str, $is_required)
    {

        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name']) && $_FILES["file"]["size"] > 0) {

            $file_type = $_FILES["file"]['type'];
            $file_size = $_FILES["file"]["size"];
            $file_name = $_FILES["file"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', 'File Type Not Allowed');
                return false;
            }

            if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', 'Extension Not Allowed');
                return false;
            }

            if ($file_size > $result->file_size) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                return false;
            }

            return true;
        } else {
            if ($is_required == 0) {
                $this->form_validation->set_message('handle_upload', 'Please choose a file to upload.');
                return false;
            } else {
                return true;
            }
        }
    }

    public function assignment_handle_upload($str, $is_required)
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();

        if (isset($_FILES["file"]) && !empty($_FILES['file']['name']) && $_FILES["file"]["size"] > 0) {

            $file_type = $_FILES["file"]['type'];
            $file_size = $_FILES["file"]["size"];
            $file_name = $_FILES["file"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $_FILES['file']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mtype, $allowed_mime_type)) {
                $this->form_validation->set_message('assignment_handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('assignment_handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }

            if ($file_size > $result->file_size) {
                $this->form_validation->set_message('assignment_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                return false;
            }

            return true;
        } else {
            return true;
        }
    }

    public function dailyassignment()
    {
        $this->session->set_userdata('top_menu', 'studentdairy');
        $student_id                  = $this->customlib->getStudentSessionUserID();
        $student_current_session     = $this->customlib->getStudentCurrentClsSection();
        $dailyassignmentlist         = $this->studentdairy_model->getdailyassignment($student_id, $student_current_session->student_session_id);
        $data["dailyassignmentlist"] = $dailyassignmentlist;
        $class_id                    = $student_current_session->class_id;
        $section_id                  = $student_current_session->section_id;

        $data['subjectlist'] = $this->subjectgroup_model->getAllsubjectByClassSection($class_id, $section_id);
        $this->load->view("layout/student/header");
        $this->load->view("user/studentdairy/dailyassignmentlist", $data);
        $this->load->view("layout/student/footer");
    }

    public function createdailyassignment()
    {
        $student_current_session = $this->customlib->getStudentCurrentClsSection();
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject', $this->lang->line('subject'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_assignment_handle_upload');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'title'   => form_error('title'),
                'subject' => form_error('subject'),
                'file'    => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $img_name = $this->media_storage->fileupload("file", "./uploads/studentdairy/daily_assignment/");

            $data = array(
                'title'                    => $this->input->post('title'),
                'student_session_id'       => $student_current_session->student_session_id,
                'description'              => $this->input->post('description'),
                'subject_group_subject_id' => $this->input->post('subject'),
                'date'                     => date('Y-m-d'),
                'attachment'               => $img_name,
                'evaluated_by'             => NULL,
            );

            $id = $this->studentdairy_model->adddailyassignment($data);

            $msg   = $this->lang->line('success_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }

        echo json_encode($array);
    }

    public function editdailyassignment()
    {
        $assignment_id               = $this->input->post('assignment_id');
        $singledailyassignmentlist   = $this->studentdairy_model->getsingledailyassignment($assignment_id);
        $data["dailyassignmentlist"] = $singledailyassignmentlist;
        $student_current_class       = $this->customlib->getStudentCurrentClsSection();
        $class_id                    = $student_current_class->class_id;
        $section_id                  = $student_current_class->section_id;
        $data['subjectlist']         = $this->subjectgroup_model->getAllsubjectByClassSection($class_id, $section_id);
        $page                        = $this->load->view("user/studentdairy/_editdailyassignment", $data, true);
        echo json_encode(array('page' => $page));
    }

    public function updatedailyassignment()
    {
        $student_current_session = $this->customlib->getStudentCurrentClsSection();
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject', $this->lang->line('subject'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_assignment_handle_upload');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'title'   => form_error('title'),
                'subject' => form_error('subject'),
                'file'    => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $data = array(
                'id'                       => $this->input->post('assigment_id'),
                'title'                    => $this->input->post('title'),
                'student_session_id'       => $student_current_session->student_session_id,
                'subject_group_subject_id' => $this->input->post('subject'),
                'description'              => $this->input->post('description'),
                'date'                     => date('Y-m-d'),
            );

            $assignment_list = $this->studentdairy_model->getsingledailyassignment($this->input->post('assigment_id'));
            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {

                $img_name = $this->media_storage->fileupload("file", "./uploads/studentdairy/daily_assignment/");
            } else {
                $img_name = $assignment_list['attachment'];
            }

            $data['attachment'] = $img_name;

            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
                if ($assignment_list['attachment'] != '') {
                    $this->media_storage->filedelete($assignment_list['attachment'], "uploads/studentdairy/daily_assignment");
                }
            }

            $id = $this->studentdairy_model->adddailyassignment($data);

            $msg   = $this->lang->line('success_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }

        echo json_encode($array);
    }

    public function deletedailyassignment($id)
    {
        $row = $this->studentdairy_model->getsingledailyassignment($id);
        if ($row['attachment'] != '') {
            $this->media_storage->filedelete($row['attachment'], "uploads/studentdairy/daily_assignment/");
        }

        $this->studentdairy_model->deletedailyassignment($id);
        redirect('user/studentdairy/dailyassignment');
    }

    public function dailyassigmnetdownload($id)
    {
        $dailyassigmnetlist = $this->studentdairy_model->getsingledailyassignment($id);
        $this->media_storage->filedownload($dailyassigmnetlist['attachment'], "./uploads/studentdairy/daily_assignment");
    }
}

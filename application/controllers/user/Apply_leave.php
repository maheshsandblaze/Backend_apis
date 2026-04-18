<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Apply_leave extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->load->model("filetype_model");
        $this->load->library('mailsmsconf');
    }

    public function index()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

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


        $leave_records = $this->apply_leave_model
            ->get_student($student_session_id);


        $studentclasses = $this->studentsession_model
            ->searchMultiClsSectionByStudent($student_id);


        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'student_id'      => $student_id,
                    'student_session_id' => $student_session_id,
                    'leave_records'   => $leave_records ?? [],
                    'student_classes' => $studentclasses ?? []
                ]
            ]));
    }

    public function get_details($id = null)
    {
        // Handle preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Leave ID is required'
                ]));
        }

        $leave = $this->apply_leave_model->getstudentleave($id, null, null);

        if (empty($leave)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Leave record not found'
                ]));
        }

        // Format dates
        $leave['from_date']  = date($this->customlib->getSchoolDateFormat(), strtotime($leave['from_date']));
        $leave['to_date']    = date($this->customlib->getSchoolDateFormat(), strtotime($leave['to_date']));
        $leave['apply_date'] = date($this->customlib->getSchoolDateFormat(), strtotime($leave['apply_date']));

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data' => $leave
            ]));
    }

    public function add()
    {
        // Handle preflight
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

        $student_session_id = $this->input->post('student_session_id');

        $this->form_validation->set_rules('apply_date', 'Apply Date', 'required');
        $this->form_validation->set_rules('from_date', 'From Date', 'required');
        $this->form_validation->set_rules('to_date', 'To Date', 'required');

        if ($this->form_validation->run() == false) {

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        $data = [
            'apply_date'         => $this->customlib->dateFormatToYYYYMMDD($this->input->post('apply_date')),
            'from_date'          => $this->customlib->dateFormatToYYYYMMDD($this->input->post('from_date')),
            'to_date'            => $this->customlib->dateFormatToYYYYMMDD($this->input->post('to_date')),
            'student_session_id' => $student_session_id,
            'reason'             => $this->input->post('message')
        ];

        if ($this->input->post('leave_id')  == "") {

            $leave_id = $this->apply_leave_model->add($data);
        } else {
            $data['id'] = $this->input->post('leave_id');
            $leave_id   = $data['id'];
            $this->apply_leave_model->add($data);
        }


        /* ======================
       FILE UPLOAD
    ====================== */

    // echo "<pre>";
    // print_r($_FILES);exit;




        if (!empty($_FILES['files']['name'])) {
            // echo "comming";
            $img_name = $this->media_storage->fileupload("files", "./../uploads/student_leavedocuments/");

                // echo "<pre>";
                // print_r($img_name);exit;




            $file_data = [
                'id'   => $leave_id,
                'docs' => $img_name
            ];

            // echo $file_data['docs'];

            // echo "<pre>";
            // print_r($file_data);exit;

            $this->apply_leave_model->add($file_data);
        }

        // exit;

        // if (!empty($_FILES['files']['name'][0])) {

        //     foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {

        //         if (!empty($tmpName)) {

        //             $img_name = time() . "-" . uniqid() . "-" . $_FILES['files']['name'][$index];

        //             move_uploaded_file(
        //                 $tmpName,
        //                 "./uploads/student_leavedocuments/" . $img_name
        //             );

        //             $file_data = [
        //                 'id'   => $leave_id,
        //                 'docs' => $img_name
        //             ];

        //             $this->apply_leave_model->add($file_data);
        //         }
        //     }
        // }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Leave applied successfully',
                'leave_id' => $leave_id
            ]));
    }

    public function remove_leave($id = null)
    {
        // Handle preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Leave ID is required'
                ]));
        }

        $row = $this->apply_leave_model->get($id, null, null);

        if (empty($row)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Leave record not found'
                ]));
        }

        // Delete document if exists
        if (!empty($row['docs'])) {
            $this->media_storage->filedelete($row['docs'], "uploads/student_leavedocuments/");
        }

        // Delete leave record
        $this->apply_leave_model->remove_leave($id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Leave deleted successfully'
            ]));
    }

    public function download($id)
    {
        $leavelist = $this->apply_leave_model->get($id, null, null);
        $this->media_storage->filedownload($leavelist['docs'], "./uploads/student_leavedocuments");
    }

    public function handle_upload($str, $var1)
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();

        if (isset($_FILES["files"]["name"][0]) && !empty($_FILES["files"]["name"][0])) {

            $file_type         = $_FILES["files"]["type"][0];
            $file_size         = $_FILES["files"]["size"][0];
            $file_name         = $_FILES["files"]["name"][0];
            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES["files"]['tmp_name'][0])) {

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
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_extension_error_uploading'));
                return false;
            }

            return true;
        }

        return true;
    }
}

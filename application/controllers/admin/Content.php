<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Content extends Public_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
    }

    public function index()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }



        // POST only
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }


        // $userdata  = $this->customlib->getUserData();
        // $user_role = json_decode($this->customlib->getStaffRole());

        // -----------------------------
        // MASTER DATA (Always returned)
        // -----------------------------
        $response = [
            // 'content_available' => $this->customlib->contentAvailabelFor(),
            // 'content_type'      => $this->customlib->getcontenttype(),
            'classlist'         => $this->class_model->get(),
            'list'              => $this->content_model
                ->getContentByRole()
        ];

        // -----------------------------
        // READ INPUT (JSON / FORM DATA)
        // -----------------------------
        $input = $this->input->post();
        if (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // -----------------------------
        // IF NO SUBMISSION → RETURN DATA
        // -----------------------------
        if (empty($input)) {
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => true,
                    'data'   => $response
                ]));
        }

        // -----------------------------
        // VALIDATION
        // -----------------------------
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('content_title', 'Content Title', 'required');
        $this->form_validation->set_rules('content_type', 'Content Type', 'required');
        $this->form_validation->set_rules('upload_date', 'Upload Date', 'required');

        if (
            isset($input['content_available']) &&
            in_array('student', (array)$input['content_available']) &&
            empty($input['visibility'])
        ) {
            $this->form_validation->set_rules('class_id', 'Class', 'required');
            $this->form_validation->set_rules('section', 'Section', 'required');
            $this->form_validation->set_rules('created_by', 'staff', 'required');
        }

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array(),
                    'data'   => $response
                ]));
        }

        // echo "<pre>";print_r($_POST);exit;

        // -----------------------------
        // PREPARE DATA
        // -----------------------------
        $session_id = $this->setting_model->getCurrentSession();

        $content_available = (array)$input['content_available'];
        $content_for = [];

        foreach ($content_available as $role) {
            $content_for[] = ['role' => $role];
        }

        $visibility = (!empty($input['visibility'])) ? $input['visibility'] : 'No';
        $classes    = $input['class_id'] ?? '';
        $sections   = isset($input['section']) ? implode(',', (array)$input['section']) : '';

        $data = [
            'title'       => $input['content_title'],
            'type'        => $input['content_type'],
            'note'        => $input['note'] ?? '',
            'class_id'    => $classes,
            'cls_sec_id'  => $sections,
            'created_by'  => $input['created_by'],
            'is_public'   => $visibility,
            'session_id'  => $session_id,
            'date'        => date('Y-m-d', strtotime($input['upload_date']))

        ];

        // echo "<pre>";print_r($data);exit;

        // -----------------------------
        // FILE SIZE CHECK (2MB)
        // -----------------------------
        if (!empty($_FILES['file']['name'])) {
            if ($_FILES['file']['size'] > 2 * 1024 * 1024) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'File size must not exceed 2 MB'
                    ]));
            }
        }

        // echo "<pre>";print_r($data);exit;

        // -----------------------------
        // INSERT CONTENT
        // -----------------------------
        $insert_id = $this->content_model->add($data, $content_for);

        // echo $insert_id;
        // exit;

        // -----------------------------
        // FILE UPLOAD
        // -----------------------------
        if (!empty($_FILES['file']['name'])) {


            $attachment = null;
            if (!empty($_FILES['file']['name'])) {
                $attachment = $this->media_storage->fileupload(
                    "file",
                    "../uploads/school_content/"
                );

                $this->content_model->add([
                    'id'   => $insert_id,
                    'file' => 'uploads/school_content/material/' . $attachment
                ]);
            }
        }

        // -----------------------------
        // AUDIT LOG
        // -----------------------------


        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Content uploaded successfully'
            ]));
    }




    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        $result = $this->filetype_model->get();
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES['file']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                // if ($file_size > $result->file_size) {
                //     $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                //     return false;
                // }
            } else {
                $this->form_validation->set_message('handle_upload', 'File size is too small');
                return false;
            }

            return true;
        } else {
            $this->form_validation->set_message('handle_upload', $this->lang->line('the_file_field_is_required'));
            return false;
        }
    }

    public function download()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


        $input = $this->input->post();
        if (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        $id  = $input['id'];



        $res = $this->content_model->get($id);





        if (empty($res) || empty($res['file'])) {
            show_error('File not found', 404);
        }

        $this->load->helper('download');

        $filepath = FCPATH . $res['file'];

        if (!file_exists($filepath)) {
            show_error('File not found on server', 404);
        }

        $filename = basename($filepath);
        force_download($filename, file_get_contents($filepath));



        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'File download successfully'
            ]));
    }


    public function editold()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }


        // Read form-data or JSON
        $input = $this->input->post();
        if (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // -----------------------------
        // VALIDATION
        // -----------------------------
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('id', 'Content ID', 'required');
        $this->form_validation->set_rules('content_title', 'Content Title', 'required');
        $this->form_validation->set_rules('content_type', 'Content Type', 'required');
        $this->form_validation->set_rules('class_id', 'Class', 'required');
        $this->form_validation->set_rules('upload_date', 'Upload Date', 'required');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        // -----------------------------
        // PREPARE DATA
        // -----------------------------
        $content_available = (array)($input['content_available'] ?? []);
        $content_for = [];

        foreach ($content_available as $role) {
            $content_for[] = ['role' => $role];
        }

        $sections = isset($input['section'])
            ? implode(',', (array)$input['section'])
            : '';

        $data = [
            'id'          => $input['id'],
            'title'       => $input['content_title'],
            'type'        => $input['content_type'],
            'note'        => $input['note'] ?? '',
            'class_id'    => $input['class_id'],
            'cls_sec_id'  => $sections,
            'created_by'  => $this->customlib->getStaffID(),
            'date'        => date(
                'Y-m-d',
                $this->customlib->datetostrtotime($input['upload_date'])
            )
        ];

        // -----------------------------
        // FILE UPLOAD (OPTIONAL)
        // -----------------------------
        if (!empty($_FILES['file']['name'])) {

            if ($_FILES['file']['size'] > 2 * 1024 * 1024) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'File size must not exceed 2 MB'
                    ]));
            }

            $fileInfo = pathinfo($_FILES['file']['name']);
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $fileInfo['basename']);

            $upload_dir = FCPATH . '../uploads/content_files/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $filename);
            $data['file'] = 'uploads/content_files/' . $filename;
        } else {
            // Keep old file
            $data['file'] = $input['old_file'] ?? '';
        }

        // -----------------------------
        // UPDATE CONTENT
        // -----------------------------
        $this->content_model->add($data, $content_for);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Content updated successfully'
            ]));
    }

    public function edit($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

        // -----------------------------
        // ✅ GET → FETCH DATA
        // -----------------------------
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if (empty($id)) {
                return $this->output
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'ID is required'
                    ]));
            }

            $content = $this->content_model->get($id);

            if (!$content) {
                return $this->output
                    ->set_status_header(404)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Content not found'
                    ]));
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => true,
                    'data'   => $content
                ]));
        }

        // -----------------------------
        // ❌ NOT POST
        // -----------------------------
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // -----------------------------
        // ✅ READ INPUT (FORM-DATA / JSON)
        // -----------------------------
        $input = $this->input->post();
        if (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // -----------------------------
        // ✅ VALIDATION
        // -----------------------------
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('id', 'Content ID', 'required');
        $this->form_validation->set_rules('content_title', 'Content Title', 'required');
        $this->form_validation->set_rules('content_type', 'Content Type', 'required');
        $this->form_validation->set_rules('class_id', 'Class', 'required');
        $this->form_validation->set_rules('upload_date', 'Upload Date', 'required');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        // -----------------------------
        // ✅ PREPARE DATA
        // -----------------------------
        $content_available = (array)($input['content_available'] ?? []);
        $content_for = [];

        foreach ($content_available as $role) {
            $content_for[] = ['role' => $role];
        }

        $sections = isset($input['section'])
            ? implode(',', (array)$input['section'])
            : '';

        $data = [
            'id'          => $input['id'],
            'title'       => $input['content_title'],
            'type'        => $input['content_type'],
            'note'        => $input['note'] ?? '',
            'class_id'    => $input['class_id'],
            'cls_sec_id'  => $sections,
            'created_by'  => $input['created_by'],
            'date'        => date('Y-m-d', strtotime($input['upload_date']))
        ];

        // -----------------------------
        // ✅ FILE UPLOAD
        // -----------------------------
        if (!empty($_FILES['file']['name'])) {

            if ($_FILES['file']['size'] > 2 * 1024 * 1024) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'File size must not exceed 2 MB'
                    ]));
            }

            $fileInfo = pathinfo($_FILES['file']['name']);
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $fileInfo['basename']);

            $upload_dir = FCPATH . '../uploads/content_files/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $filename);
            $data['file'] = 'uploads/content_files/' . $filename;
        } else {
            $data['file'] = $input['old_file'] ?? '';
        }

        // -----------------------------
        // ✅ UPDATE
        // -----------------------------
        $this->content_model->add($data, $content_for);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Content updated successfully'
            ]));
    }


    // function search() {
    //     $text = $_GET['content'];
    //     $data['title'] = 'Fees Master List';
    //     $contentlist = $this->content_model->search_by_content_type($text);
    //     $data['contentlist'] = $contentlist;
    //     $this->load->view('layout/header');
    //     $this->load->view('admin/content/search', $data);
    //     $this->load->view('layout/footer');
    // }

    function delete()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


        $input = $this->input->post();
        if (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        $id  = $input['id'];



        $data = $this->content_model->get($id);
        $file = $data['file'];
        unlink($file);
        $this->content_model->remove($id);


        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Content deleted successfully'
            ]));
    }



    public function deleteassignment()
    {


        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Optional RBAC (enable if needed)
        // if (!$this->rbac->hasPrivilege('upload_content', 'can_delete')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode(['status' => false, 'message' => 'Access Denied']));
        // }

        $input = json_decode(file_get_contents('php://input'), true) ?? $this->input->post();

        if (empty($input['id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Content ID is required'
                ]));
        }

        $this->content_model->remove($input['id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Assignment deleted successfully'
            ]));
    }





    public function weeklyschedule()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $list = $this->content_model->getListByCategory('weekly_schedule');

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $list
            ]));
    }


    public function syllabus()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
        $list = $this->content_model->getListByCategory("syllabus");
        $data['list'] = $list;
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $list
            ]));
    }



    public function worksheets()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $list = $this->content_model->getListByCategory("work_sheets");
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $list
            ]));
    }
}

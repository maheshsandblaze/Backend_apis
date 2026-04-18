<?php

class Timeline extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('media_storage');
        $this->load->model('timeline_model');
    }

    // public function add()
    // {
    //     $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('timeline_doc', $this->lang->line('image'), 'callback_doc_handle_upload[timeline_doc]');

    //     $title = $this->input->post("timeline_title");

    //     if ($this->form_validation->run() == false) {

    //         $msg = array(
    //             'timeline_title' => form_error('timeline_title'),
    //             'timeline_date'  => form_error('timeline_date'),
    //             'timeline_doc'   => form_error('timeline_doc'),
    //         );

    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {
    //         $visible_check = $this->input->post('visible_check');
    //         $timeline_date = $this->input->post('timeline_date');
    //         if (empty($visible_check)) {
    //             $visible = '';
    //         } else {
    //             $visible = 'yes';
    //         }              

    //         if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {                    
    //             $img_name = $this->media_storage->fileupload("timeline_doc", "./uploads/student_timeline/");              
    //         } else {
    //             $img_name = '';
    //         }

    //         $timeline = array(
    //             'title'         => $this->input->post('timeline_title'),
    //             'description'   => $this->input->post('timeline_desc'),
    //             'timeline_date' => date('Y-m-d', $this->customlib->datetostrtotime($timeline_date)),
    //             'status'        => $visible,
    //             'date'          => date('Y-m-d'),
    //             'student_id'    => $this->input->post('student_id'),
    //             'created_student_id'    => '',
    //             'document'    => $img_name
    //         );

    //         $this->timeline_model->add($timeline);

    //         $msg   = $this->lang->line('success_message');
    //         $array = array('status' => 'success', 'error' => '', 'message' => $msg);
    //     }
    //     echo json_encode($array);
    // }


    public function add()
    {
        // =========================
        // METHOD CHECK
        // =========================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Validation Rules
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $errors = [
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
            ];

            echo json_encode([
                'status'  => 'fail',
                'error'   => $errors,
                'message' => ''
            ]);
            return;
        }

        // Visible check
        $visible = $this->input->post('visible_check') ? 'yes' : '';

        // Format date
        // $timeline_date = date(
        //     'Y-m-d',
        //     $this->datetostrtotime($this->input->post('timeline_date'))
        // );

        $input_date = $this->input->post('timeline_date');
        $format = $this->getSchoolDateFormat();

        $date = DateTime::createFromFormat($format, $input_date);

        if ($date && $date->format($format) === $input_date) {
            $timeline_date = $date->format('Y-m-d');
        } else {
            echo json_encode([
                'status' => 'fail',
                'error'  => ['timeline_date' => 'Invalid date format. Expected: ' . $format],
                'message' => ''
            ]);
            return;
        }

        // File Upload (if exists)
        $attachment = null;
        if (!empty($_FILES['timeline_doc']['name'])) {
            $attachment = $this->media_storage->fileupload(
                "timeline_doc",
                "../uploads/student_timeline/"
            );
        }
        // if (!empty($_FILES['timeline_doc']['name'])) {

        //     $config['upload_path']   = './uploads/student_timeline/';
        //     $config['allowed_types'] = 'jpg|jpeg|png|pdf';
        //     $config['max_size']      = 2048; // 2MB
        //     $config['encrypt_name']  = TRUE;

        //     $this->load->library('upload', $config);

        //     if ($this->upload->do_upload('timeline_doc')) {
        //         $fileData = $this->upload->data();
        //         $img_name = $fileData['file_name'];
        //     } else {
        //         echo json_encode([
        //             'status'  => 'fail',
        //             'error'   => ['timeline_doc' => $this->upload->display_errors('', '')],
        //             'message' => ''
        //         ]);
        //         return;
        //     }
        // }

        // Prepare Data
        $timeline = [
            'title'              => $this->input->post('timeline_title'),
            'description'        => $this->input->post('timeline_desc'),
            'timeline_date'      => $timeline_date,
            'status'             => $visible,
            'date'               => date('Y-m-d'),
            'student_id'         => $this->input->post('student_id'),
            'created_student_id' => '',
            'document'           => $attachment
        ];

        $this->timeline_model->add($timeline);

        echo json_encode([
            'status'  => 'success',
            'error'   => '',
            'message' => 'Timeline added successfully',
            'data'    => $timeline
        ]);
    }

    public function datetostrtotime($date)
    {
        if ($date == "") {
            return "";
        }
        $format = $this->getSchoolDateFormat();
        if ($format == 'd-m-Y') {
            list($day, $month, $year) = explode('-', $date);
        }

        if ($format == 'd/m/Y') {
            list($day, $month, $year) = explode('/', $date);
        }

        if ($format == 'd-M-Y') {
            list($day, $month, $year) = explode('-', $date);
        }

        if ($format == 'd.m.Y') {
            list($day, $month, $year) = explode('.', $date);
        }

        if ($format == 'm-d-Y') {
            list($month, $day, $year) = explode('-', $date);
        }

        if ($format == 'm/d/Y') {
            list($month, $day, $year) = explode('/', $date);
        }

        if ($format == 'm.d.Y') {
            list($month, $day, $year) = explode('.', $date);
        }

        if ($format == 'Y/m/d') {
            list($year, $month, $day) = explode('/', $date);
        }

        $date = $year . "-" . $month . "-" . $day;

        return strtotime($date);
    }


    public function getSchoolDateFormat($date_only = true, $time = false)
    {
        $setting_result     = $this->setting_model->get();
        return $date_format = $setting_result[0]['date_format'];
    }

    public function add_staff_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('image'), 'callback_doc_handle_upload[timeline_doc]');
        $title = $this->input->post("timeline_title");

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $visible_check = $this->input->post('visible_check');
            $timeline_date = $this->input->post('timeline_date');
            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }
            $timeline = array(
                'title'         => $this->input->post('timeline_title'),
                'timeline_date' => date('Y-m-d', $this->customlib->datetostrtotime($timeline_date)),
                'description'   => $this->input->post('timeline_desc'),
                'status'        => $visible,
                'date'          => date('Y-m-d'),
                'staff_id'      => $this->input->post('staff_id')
            );

            $id = $this->timeline_model->add_staff_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/staff_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }

                $img_name = $this->media_storage->fileupload("timeline_doc", $uploaddir);
            } else {

                $document = "";
                $img_name = "";
            }

            $upload_data = array('id' => $id, 'document' => $img_name);
            $this->timeline_model->add_staff_timeline($upload_data);
            $msg   = $this->lang->line('success_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function download($timeline_id)
    {
        $doc_details = $this->timeline_model->getstudentsingletimeline($timeline_id);
        $this->media_storage->filedownload($doc_details['document'], "./uploads/student_timeline/");
    }

    public function download_staff_timeline($timeline_id)
    {
        $doc_details = $this->timeline_model->getstaffsingletimeline($timeline_id);
        $this->media_storage->filedownload($doc_details['document'], "./uploads/staff_timeline/");
    }

    // public function delete_timeline()
    // {
    //     $id = $this->input->post('id');
    //     $this->timeline_model->delete_timeline($id);
    //     echo json_encode(array('status' => 'success', 'message' => $this->lang->line('delete_message')));
    // }

    public function delete_timeline()
    {
        // =========================
        // METHOD CHECK
        // =========================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // =========================
        // GET RAW JSON INPUT
        // =========================
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        if (!$data || empty($data['id'])) {
            echo json_encode([
                'status'  => false,
                'message' => 'ID is required'
            ]);
            return;
        }

        $id = $data['id'];

        // =========================
        // DELETE RECORD
        // =========================
        $this->timeline_model->delete_timeline($id);

        echo json_encode([
            'status'  => true,
            'message' => $this->lang->line('delete_message')
        ]);
    }

    public function delete_staff_timeline($id)
    {
        if (!empty($id)) {
            $this->timeline_model->delete_staff_timeline($id);
        }
    }

    public function staff_timeline($id = 77)
    {
        $userdata = $this->customlib->getUserData();
        $userid   = $userdata['id'];
        $status   = '';
        if ($userid == $id) {
            $status = 'yes';
        }

        $result = $this->timeline_model->getStaffTimeline($id, $status);
        $data["result"] = $result;
        $this->load->view("admin/staff_timeline", $data);
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

            if ($files = @getimagesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }

                if ($file_size > $result->image_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->image_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed_or_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    // public function getstudentsingletimeline()
    // {
    //     $id                         = $this->input->post('id');
    //     $data['singletimelinelist'] = $this->timeline_model->getstudentsingletimeline($id);
    //     $page                       = $this->load->view("admin/_edit_student_timeline", $data, true);
    //     echo json_encode(array('page' => $page));
    // }

    public function getstudentsingletimeline()
    {
        // =========================
        // METHOD CHECK
        // =========================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // =========================
        // GET RAW JSON INPUT
        // =========================
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        if (!$data || empty($data['id'])) {
            echo json_encode([
                'status'  => false,
                'message' => 'ID is required'
            ]);
            return;
        }

        $id = $data['id'];

        // =========================
        // FETCH DATA
        // =========================
        $timelineData = $this->timeline_model->getstudentsingletimeline($id);

        if (!$timelineData) {
            echo json_encode([
                'status'  => false,
                'message' => 'No record found'
            ]);
            return;
        }

        // If you still need view HTML
        $viewData['singletimelinelist'] = $timelineData;
        $page = $this->load->view("admin/_edit_student_timeline", $viewData, true);

        echo json_encode([
            'status' => true,
            'data'   => $timelineData,
            'page'   => $page
        ]);
    }

    // public function editstudenttimeline()
    // {
    //     $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('timeline_doc', $this->lang->line('image'), 'callback_doc_handle_upload[timeline_doc]');
    //     $title = $this->input->post("timeline_title");

    //     if ($this->form_validation->run() == false) {

    //         $msg = array(
    //             'timeline_title' => form_error('timeline_title'),
    //             'timeline_date'  => form_error('timeline_date'),
    //             'timeline_doc'   => form_error('timeline_doc'),
    //         );

    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {

    //         $timeline_date = $this->input->post('timeline_date');
    //         $visible_check = $this->input->post('visible_check');

    //         if (empty($visible_check)) {
    //             $visible = '';
    //         } else {
    //             $visible = 'yes';
    //         }

    //         $timeline = array(
    //             'id'            => $this->input->post('id'),
    //             'title'         => $this->input->post('timeline_title'),
    //             'description'   => $this->input->post('timeline_desc'),
    //             'timeline_date' => date('Y-m-d', $this->customlib->datetostrtotime($timeline_date)),
    //             'status'        => $visible,
    //             'date'          => date('Y-m-d'),
    //             'student_id'    => $this->input->post('student_id'),
    //             'created_student_id'    => ''
    //             );                

    //         $this->timeline_model->add($timeline);

    //         if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
    //             $uploaddir = './uploads/student_timeline/';
    //             if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
    //                 die("Error creating folder $uploaddir");
    //             }
    //             $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
    //             $document = 'uploads/student_timeline/' . basename($_FILES['timeline_doc']['name']);
    //             $img_name = $this->input->post('id') . '.' . $fileInfo['extension'];
    //             move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);
    //         } else {

    //             $gettimelinedata = $this->timeline_model->getstudentsingletimeline($this->input->post('id'));
    //             $img_name        = $gettimelinedata['document'];
    //         }

    //         $upload_data = array('id' => $this->input->post('id'), 'document' => $img_name);
    //         $this->timeline_model->add($upload_data);
    //         $msg   = $this->lang->line('success_message');
    //         $array = array('status' => 'success', 'error' => '', 'message' => $msg);
    //     }
    //     echo json_encode($array);
    // }


    public function editstudenttimelineold()
    {
        // =========================
        // METHOD CHECK
        // =========================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // =========================
        // GET FORM DATA
        // =========================
        $data = $this->input->post();

        if (empty($data)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'message' => 'No input data received'
                ]));
        }

        // =========================
        // VALIDATION
        // =========================
        $errors = [];

        if (empty($data['id'])) {
            $errors['id'] = 'ID is required';
        }

        if (empty($data['timeline_title'])) {
            $errors['timeline_title'] = 'Title is required';
        }

        if (empty($data['timeline_date'])) {
            $errors['timeline_date'] = 'Date is required';
        }

        if (!empty($errors)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $errors
                ]));
        }

        // =========================
        // DATE FORMAT
        // =========================
        $format = $this->getSchoolDateFormat();
        $date   = DateTime::createFromFormat($format, $data['timeline_date']);

        if (!$date || $date->format($format) !== $data['timeline_date']) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => [
                        'timeline_date' => 'Invalid date format. Expected: ' . $format
                    ]
                ]));
        }

        $timeline_date = $date->format('Y-m-d');

        $visible = (!empty($data['visible_check'])) ? 'yes' : 'no';

        // =========================
        // BASE DATA
        // =========================
        $timeline = [
            'id'                 => $data['id'],
            'title'              => trim($data['timeline_title']),
            'description'        => $data['timeline_desc'] ?? '',
            'timeline_date'      => $timeline_date,
            'status'             => $visible,
            'date'               => date('Y-m-d'),
            'student_id'         => $data['student_id'] ?? null,
            'created_student_id' => ''
        ];

        // =========================
        // FILE UPLOAD
        // =========================
        if (!empty($_FILES['timeline_doc']['name'])) {

            $uploadPath = '../uploads/student_timeline/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $config['upload_path']   = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf';
            $config['max_size']      = 2048; // 2MB
            $config['file_name']     = time() . '_' . $_FILES['timeline_doc']['name'];

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('timeline_doc')) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'message' => $this->upload->display_errors('', '')
                    ]));
            }

            $fileData = $this->upload->data();
            $timeline['document'] = $fileData['file_name'];
        }

        // =========================
        // SAVE (SINGLE CALL)
        // =========================
        $this->timeline_model->add($timeline);

        // =========================
        // RESPONSE
        // =========================
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => 'Timeline updated successfully'
            ]));
    }

    public function editstudenttimeline()
    {
        // =========================
        // METHOD CHECK
        // =========================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // =========================
        // GET FORM DATA
        // =========================
        $data = $this->input->post();

        if (empty($data)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'message' => 'No input data received'
                ]));
        }

        // =========================
        // VALIDATION
        // =========================
        $errors = [];

        if (empty($data['id'])) {
            $errors['id'] = 'ID is required';
        }

        if (empty($data['timeline_title'])) {
            $errors['timeline_title'] = 'Title is required';
        }

        if (empty($data['timeline_date'])) {
            $errors['timeline_date'] = 'Date is required';
        }

        if (!empty($errors)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $errors
                ]));
        }

        // =========================
        // DATE FORMAT
        // =========================
        $format = $this->getSchoolDateFormat();
        $date   = DateTime::createFromFormat($format, $data['timeline_date']);

        if (!$date || $date->format($format) !== $data['timeline_date']) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => [
                        'timeline_date' => 'Invalid date format. Expected: ' . $format
                    ]
                ]));
        }

        $timeline_date = $date->format('Y-m-d');

        $visible = (!empty($data['visible_check'])) ? 'yes' : 'no';

        // =========================
        // BASE DATA
        // =========================
        $timeline = [
            'id'                 => $data['id'],
            'title'              => trim($data['timeline_title']),
            'description'        => $data['timeline_desc'] ?? '',
            'timeline_date'      => $timeline_date,
            'status'             => $visible,
            'date'               => date('Y-m-d'),
            'student_id'         => $data['student_id'] ?? null,
            'created_student_id' => ''
        ];


        // echo "<pre>";
        // print_r($_FILES);
        // exit;
        // =========================
        // FILE UPLOAD
        // =========================
        // if (!empty($_FILES['timeline_doc']['name'])) {

        //     $uploadPath = '../uploads/student_timeline/';

        //     if (!is_dir($uploadPath)) {
        //         mkdir($uploadPath, 0777, true);
        //     }

        //     $config['upload_path']   = $uploadPath;
        //     $config['allowed_types'] = 'jpg|jpeg|png|pdf';
        //     $config['max_size']      = 2048; // 2MB
        //     $config['file_name']     = time() . '_' . $_FILES['timeline_doc']['name'];

        //     $this->load->library('upload', $config);

        //     if (!$this->upload->do_upload('timeline_doc')) {
        //         return $this->output
        //             ->set_status_header(422)
        //             ->set_output(json_encode([
        //                 'status' => 'fail',
        //                 'message' => $this->upload->display_errors('', '')
        //             ]));
        //     }

        //     $fileData = $this->upload->data();
        //     $timeline['document'] = $fileData['file_name'];
        // }

        // File Upload (if exists)
        $attachment = null;
        if (!empty($_FILES['timeline_doc']['name'])) {
            $attachment = $this->media_storage->fileupload(
                "timeline_doc",
                "../uploads/student_timeline/"
            );

          $timeline['document'] = $attachment;

        }

        // =========================
        // SAVE (SINGLE CALL)
        // =========================
        $this->timeline_model->add($timeline);

        // =========================
        // RESPONSE
        // =========================
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => 'Timeline updated successfully'
            ]));
    }

    public function getstaffsingletimeline()
    {
        $id                         = $this->input->post('id');
        $data['singletimelinelist'] = $this->timeline_model->getstaffsingletimeline($id);
        $page                       = $this->load->view("admin/_edit_staff_timeline", $data, true);
        echo json_encode(array('page' => $page));
    }

    public function editstafftimeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('image'), 'callback_doc_handle_upload[timeline_doc]');
        $title = $this->input->post("timeline_title");

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $timeline_date = $this->input->post('timeline_date');
            $visible_check = $this->input->post('visible_check');

            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = 'yes';
            }

            $timeline = array(
                'id'            => $this->input->post('id'),
                'title'         => $this->input->post('timeline_title'),
                'description'   => $this->input->post('timeline_desc'),
                'timeline_date' => date('Y-m-d', $this->customlib->datetostrtotime($timeline_date)),
                'status'        => $visible,
                'date'          => date('Y-m-d'),
                'staff_id'      => $this->input->post('edit_staff_id')
            );

            $this->timeline_model->add_staff_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = '../uploads/staff_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = 'uploads/staff_timeline/' . basename($_FILES['timeline_doc']['name']);
                $img_name = $this->input->post('id') . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);
                $upload_data = array('id' => $this->input->post('id'), 'document' => $img_name);
                $this->timeline_model->add_staff_timeline($upload_data);
            }
            $msg   = $this->lang->line('success_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function doc_handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();

        if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {

            $file_type = $_FILES["timeline_doc"]['type'];
            $file_size = $_FILES["timeline_doc"]["size"];
            $file_name = $_FILES["timeline_doc"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES['timeline_doc']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('doc_handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('doc_handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('doc_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('doc_handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }
}

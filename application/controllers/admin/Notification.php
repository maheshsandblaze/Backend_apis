<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notification extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->load->model('notification_model');
        $this->load->model('role_model');
        $this->load->model('notificationsetting_model');
        $this->load->model('staff_model');
        $this->load->model('exam_model');
        $this->load->library('form_validation');
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input ?: [];
    }

    public function index($role_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $notificationlist = $this->notification_model->get($id = null, $role_id);
        $userdata         = $this->customlib->getUserData();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'           => 'success',
                'notificationlist' => $notificationlist,
                'user_id'          => $role_id,
                'roles'            => $this->role_model->get()
            ]));
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ✅ Handle both JSON & form-data
        $input = $this->input->post();

        if (empty($input)) {
            // fallback for raw JSON
            $input = json_decode(file_get_contents("php://input"), true);
        }

        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        // ✅ Validation
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('message', 'Message', 'trim|required');
        $this->form_validation->set_rules('date', 'Notice Date', 'trim|required');
        $this->form_validation->set_rules('publish_date', 'Publish Date', 'trim|required');
        $this->form_validation->set_rules('visible[]', 'Message To', 'required');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array(),
                    'roles'  => $this->role_model->get()
                ]));
        }

        // ✅ File Upload
        $attachment = null;
        if (!empty($_FILES['file']['name'])) {
            $attachment = $this->media_storage->fileupload(
                "file",
                "../uploads/notice_board_images/"
            );
        }

        // ✅ User data
        $student     = "No";
        $staff       = "No";
        $parent      = "No";
        $staff_roles = [];

        $visible = $input['visible'] ?? [];

        // Ensure array format
        if (!is_array($visible)) {
            $visible = [$visible];
        }

        if (!in_array(7, $visible)) {
            $staff_roles[] = ['role_id' => 7, 'send_notification_id' => ''];
            $staff = "Yes";
        }

        foreach ($visible as $value) {
            if ($value == "student") {
                $student = "Yes";
            } elseif ($value == "parent") {
                $parent = "Yes";
            } elseif (is_numeric($value)) {
                $staff_roles[] = ['role_id' => $value, 'send_notification_id' => ''];
                $staff = "Yes";
            }
        }


        $staff_data = $this->staff_model->get($input['created_by']);

    

        if(!empty($staff_data)){
            $input['created_by'] = $staff_data['name'];
            $input['created_id'] = $staff_data['id'];
        } else {
            $input['created_by'] = 'admin';
            $input['created_id'] = '';
        }

        // echo "<pre>";
        // print_r($staff_data);
        // exit;

        // ✅ Insert data
        $data = [
            'message'         => $input['message'],
            'title'           => $input['title'],
            'date'            => $input['date'],
            'created_by'      => $input['created_by'],
            'created_id'      => $input['created_id'],
            'visible_student' => $student,
            'visible_staff'   => $staff,
            'visible_parent'  => $parent,
            'publish_date'    => $input['publish_date'],
            'attachment'      => $attachment,
        ];


        // echo "<Pre>";
        // print_r($data);exit;

        $id = $this->notification_model->insertBatch($data, $staff_roles);

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => 'Notice added successfully',
                'id'      => $id
            ]));
    }

    // public function edit($id)
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }

    //     $notification = $this->notification_model->get($id);
    //     if (!$notification) {
    //          return $this->output
    //             ->set_status_header(404)
    //             ->set_output(json_encode(['status' => 'fail', 'message' => 'Notification not found']));
    //     }

    //     $input = $this->_get_input();
    //     if (!empty($input)) {
    //         $this->form_validation->set_data($input);
    //     }

    //     $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('message', $this->lang->line('message'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date', $this->lang->line('notice_date'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('publish_date', $this->lang->line('publish_on'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('visible[]', $this->lang->line('message_to'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {
    //          return $this->output
    //             ->set_status_header(422)
    //             ->set_output(json_encode([
    //                 'status'       => 'fail',
    //                 'errors'       => $this->form_validation->error_array(),
    //                 'notification' => $notification,
    //                 'roles'        => $this->role_model->get()
    //             ]));
    //     } else {
    //         $userdata    = $this->customlib->getUserData();
    //         $student     = "No";
    //         $staff       = "No";
    //         $parent      = "No";
    //         $prev_roles  = $input['prev_roles'] ?? [];
    //         $visible     = $input['visible'] ?? [];
    //         $staff_roles = array();
    //         $inst_staff  = array();

    //         if (!in_array(7, $visible)) {
    //             $staff_roles[] = array('role_id' => 7, 'send_notification_id' => '');
    //             $staff         = "Yes";
    //         }

    //         foreach ($visible as $key => $value) {
    //             if ($value == "student") {
    //                 $student = "Yes";
    //             } else if ($value == "parent") {
    //                 $parent = "Yes";
    //             } else if (is_numeric($value)) {
    //                 $inst_staff[]  = $value;
    //                 $staff_roles[] = array('role_id' => $value, 'send_notification_id' => '');
    //                 $staff         = "Yes";
    //             }
    //         }

    //         $to_be_del    = array_diff($prev_roles, $inst_staff);
    //         $to_be_insert = array_diff($inst_staff, $prev_roles);
    //         $insert       = array();

    //         if (!empty($to_be_insert)) {
    //             foreach ($to_be_insert as $to_insert_key => $to_insert_value) {
    //                 $insert[] = array('role_id' => $to_insert_value, 'send_notification_id' => '');
    //             }
    //         }      

    //         $data = array(
    //             'id'              => $id,
    //             'message'         => $input['message'],
    //             'title'           => $input['title'],
    //             'date'            => date('Y-m-d', $this->customlib->datetostrtotime($input['date'])),
    //             'created_by'      => $userdata["user_type"] ?? 'admin',
    //             'created_id'      => $this->customlib->getStaffID(),
    //             'visible_student' => $student,
    //             'visible_staff'   => $staff,
    //             'visible_parent'  => $parent,
    //             'publish_date'    => date('Y-m-d', $this->customlib->datetostrtotime($input['publish_date'])),    
    //         );

    //         $img_name = $notification['attachment'];
    //         if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
    //             $img_name = $this->media_storage->fileupload("file", "./uploads/notice_board_images/");
    //             if ($notification['attachment'] != '') {
    //                 $this->media_storage->filedelete($notification['attachment'], "uploads/school_income");
    //             }
    //         } 

    //         $data['attachment'] = $img_name;

    //         $this->notification_model->insertBatch($data, $insert, $to_be_del);            

    //         return $this->output
    //             ->set_status_header(200)
    //             ->set_output(json_encode([
    //                 'status'  => 'success',
    //                 'message' => $this->lang->line('update_message')
    //             ]));
    //     }
    // }


    public function edit($id = null, $role_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (empty($id)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'message' => 'Notification ID is required'
                ]));
        }

        // Fetch notification
        $notification = $this->notification_model->get($id, $role_id);

        if (!$notification) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'message' => 'Notification not found'
                ]));
        }

        // ==========================
        // ✅ 1. HANDLE GET REQUEST
        // ==========================
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'       => 'success',
                    'notification' => $notification,
                    'roles'        => $this->role_model->get()
                ]));
        }

        // ==========================
        // ✅ 2. HANDLE POST REQUEST
        // ==========================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $input = $this->_get_input();

            if (!empty($input)) {
                $this->form_validation->set_data($input);
            }

            $this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
            $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');
            $this->form_validation->set_rules('date', 'Notice Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules('publish_date', 'Publish Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules('visible[]', 'Visible To', 'required');

            if ($this->form_validation->run() == false) {

                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            }

            // ================= BUSINESS LOGIC =================

            $userdata = $this->customlib->getUserData();

            $student = "No";
            $staff   = "No";
            $parent  = "No";

            $prev_roles  = $input['prev_roles'] ?? [];
            $visible     = $input['visible'] ?? [];

            $staff_roles = [];
            $inst_staff  = [];

            if (!in_array(7, $visible)) {
                $staff_roles[] = ['role_id' => 7, 'send_notification_id' => ''];
                $staff = "Yes";
            }

            foreach ($visible as $value) {
                if ($value == "student") {
                    $student = "Yes";
                } elseif ($value == "parent") {
                    $parent = "Yes";
                } elseif (is_numeric($value)) {
                    $inst_staff[] = $value;
                    $staff_roles[] = ['role_id' => $value, 'send_notification_id' => ''];
                    $staff = "Yes";
                }
            }

            $to_be_del    = array_diff($prev_roles, $inst_staff);
            $to_be_insert = array_diff($inst_staff, $prev_roles);

            $insert = [];
            foreach ($to_be_insert as $value) {
                $insert[] = ['role_id' => $value, 'send_notification_id' => ''];
            }

            $data = [
                'id'              => $id,
                'message'         => $input['message'],
                'title'           => $input['title'],
                'date'            => date('Y-m-d', strtotime($input['date'])),
                'created_by'      => $userdata["user_type"] ?? 'admin',
                'visible_student' => $student,
                'visible_staff'   => $staff,
                'visible_parent'  => $parent,
                'publish_date'    => date('Y-m-d', strtotime($input['publish_date']))
            ];

            // File Upload
            $img_name = $notification['attachment'];

            if (!empty($_FILES["file"]['name'])) {
                $img_name = $this->media_storage->fileupload("file", "../uploads/notice_board_images/");
            }

            $data['attachment'] = $img_name;

            $this->notification_model->insertBatch($data, $insert, $to_be_del);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => 'Notification updated successfully',
                    'data'    => $data
                ]));
        }

        // ==========================
        // METHOD NOT ALLOWED
        // ==========================
        return $this->output
            ->set_status_header(405)
            ->set_output(json_encode([
                'status' => 'fail',
                'message' => 'Method Not Allowed'
            ]));
    }


    // public function delete($id)
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }

    //     $usernotification = $this->notification_model->get($id);
    //     if (!$usernotification) {
    //          return $this->output
    //             ->set_status_header(404)
    //             ->set_output(json_encode(['status' => 'fail', 'message' => 'Notification not found']));
    //     }

    //     $this->notification_model->remove($id);
    //     if ($usernotification['attachment']) {
    //         @unlink("./uploads/notice_board_images/" . $usernotification['attachment']);
    //     }

    //     return $this->output
    //         ->set_status_header(200)
    //         ->set_output(json_encode([
    //             'status'  => 'success',
    //             'message' => $this->lang->line('delete_message')
    //         ]));
    // }


    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Get input (JSON or form-data)
        $input = $this->_get_input();

        $id = $input['id'] ?? null;
        $role_id = $input['role_id'] ?? null;

        if (empty($id)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Notification ID is required'
                ]));
        }

        // Check if notification exists
        $usernotification = $this->notification_model->get($id, $role_id);

        if (!$usernotification) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Notification not found'
                ]));
        }

        // Delete from database
        $this->notification_model->remove($id);

        // Delete attachment if exists
        if (!empty($usernotification['attachment'])) {
            @unlink("./uploads/notice_board_images/" . $usernotification['attachment']);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('delete_message')
            ]));
    }

    public function setting()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $ids          = $input['ids'] ?? [];
            $update_array = array();
            foreach ($ids as $id_key => $id_value) {
                $array = array(
                    'id'                    => $id_value,
                    'is_mail'               => $input['mail_' . $id_value] ?? 0,
                    'is_sms'                => $input['sms_' . $id_value] ?? 0,
                    'is_notification'       => $input['notification_' . $id_value] ?? 0,
                    'is_student_recipient'  => $input['student_recipient_' . $id_value] ?? 0,
                    'is_guardian_recipient' => $input['guardian_recipient_' . $id_value] ?? 0,
                    'is_staff_recipient'    => $input['staff_recipient_' . $id_value] ?? 0,
                );
                $update_array[] = $array;
            }

            $this->notificationsetting_model->updatebatch($update_array);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('update_message')
                ]));
        }

        $notificationlist = $this->notificationsetting_model->get();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'           => 'success',
                'notificationlist' => $notificationlist
            ]));
    }

    public function read()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input           = $this->_get_input();
        $notification_id = $input['notice'] ?? null;

        if ($notification_id != "") {
            $staffid = $this->customlib->getStaffID();
            $data    = $this->notification_model->updateStatusforStaff($notification_id, $staffid);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 'success',
                    'data'   => $data,
                    'msg'    => $this->lang->line('update_message')
                ]));
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode([
                'status' => 'fail',
                'msg'    => $this->lang->line('something_went_wrong')
            ]));
    }

    public function gettemplate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $id    = $input['id'] ?? null;

        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 0, 'message' => 'ID is required']));
        }

        $record = $this->notificationsetting_model->get($id);

        // If they want the HTML template rendered
        $data['record'] = $record;
        $template = $this->load->view('admin/notification/gettemplate', $data, true);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'   => 1,
                'record'   => $record,
                'template' => $template
            ]));
    }

    public function savetemplate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('temp_id', $this->lang->line('template_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('template_message', $this->lang->line('template_message'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('template_subject', $this->lang->line('subject'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 0,
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {
            $data_update = array(
                'id'          => $input['temp_id'],
                'template_id' => $input['template_id'] ?? null,
                'template'    => $input['template_message'],
                'subject'     => $input['template_subject'],
            );

            $this->notificationsetting_model->update($data_update);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 1,
                    'message' => $this->lang->line('update_message')
                ]));
        }
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();

        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type = $_FILES["file"]['type'];
            $file_size = $_FILES["file"]["size"];
            $file_name = $_FILES["file"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = @filesize($_FILES['file']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function download($id)
    {
        $notification = $this->notification_model->notification($id);
        $this->media_storage->filedownload($notification['attachment'], "uploads/notice_board_images");
    }

    public function notification()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input      = $this->_get_input();
        $message_id = $input['message_id'] ?? null;

        if (!$message_id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 0, 'message' => 'Message ID is required']));
        }

        $notificationlist = $this->notification_model->get($message_id);
        if (!$notificationlist) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 0, 'message' => 'Notification not found']));
        }

        $data['notificationlist'] = $notificationlist;
        $created_by_name          = $this->staff_model->get($notificationlist["created_id"]);
        $roles                    = $notificationlist["roles"];

        $staff_id = '';
        if ($created_by_name["employee_id"] != '') {
            $staff_id = ' (' . $created_by_name["employee_id"] . ')';
        }

        $arr = explode(",", $roles);
        $data['notificationlist']["role_name"] = $this->notification_model->getRole($arr);

        if (!empty($created_by_name["name"])) {
            $data['notificationlist']["createdby_name"] =  "<li><i class='fa fa-user pr-1'></i>" . $this->lang->line('created_by') . ':' . $created_by_name["name"] . " " . $created_by_name["surname"] . $staff_id . "</li>";
        } else {
            $data['notificationlist']["createdby_name"] = '';
        }

        $page = $this->load->view('admin/notification/_notification', $data, true);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'           => 1,
                'notificationlist' => $notificationlist,
                'page'             => $page
            ]));
    }
}

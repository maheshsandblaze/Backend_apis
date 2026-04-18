<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notification_class extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
    }

    // public function index()
    // {
    //     if (!$this->rbac->hasPrivilege('notice_board', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Communicate');
    //     $this->session->set_userdata('sub_menu', 'notification/index');
    //     $data['title']            = 'Notifications';
    //     $data['notificationlist'] = $this->notification_class_model->get();

    //     $userdata           = $this->customlib->getUserData();     
    //     $data['user_id']    = $userdata["id"];
    //     $this->load->view('layout/header', $data);
    //     $this->load->view('admin/notification_class/notificationList', $data);
    //     $this->load->view('layout/footer', $data);
    // }

    public function index()
    {
        // ===============================
        // CORS HANDLING
        // ===============================
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

        // ===============================
        // FETCH DATA
        // ===============================
        $notifications = $this->notification_class_model->get();
        $userdata      = $this->customlib->getUserData();
        
        $class                         = $this->class_model->get('', $classteacher = 'yes');

        // ===============================
        // RESPONSE DATA
        // ===============================
        $data = [
            'title'             => 'Notifications',
            'user_id'           => $userdata['id'] ?? null,
            'notification_list' => $notifications,
            'classlist'         => $class
        ];

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }


    // public function add()
    // {
    //     if (!$this->rbac->hasPrivilege('notice_board', 'can_add')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'Communicate');
    //     $this->session->set_userdata('sub_menu', 'notification_class/index');
    //     $data['title']      = 'Add Notification';
    //     $data['title_list'] = 'Notification List';
    //     $data['roles']      = $this->role_model->get();

    //     $class                   = $this->class_model->get();
    //     $data['classlist']       = $class;

    //     $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('message', $this->lang->line('message'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date', $this->lang->line('circular_date'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('publish_date', $this->lang->line('publish_on'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');

    //     $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');

    //     if ($this->form_validation->run() == false) {

    //     } else {
    //         $img_name = $this->media_storage->fileupload("file", "./uploads/notice_board_images/");

    //         $userdata    = $this->customlib->getUserData();

    //         $data = array(
    //             'message'         => $this->input->post('message'),
    //             'title'           => $this->input->post('title'),
    //             'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
    //             'created_by'      => $userdata["user_type"],
    //             'created_id'      => $this->customlib->getStaffID(),
    //             'class_id'        => $this->input->post('class_id'),
    //             'section_id'      => $this->input->post('section_id'),
    //             'publish_date'    => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('publish_date'))),
    //             'attachment'      => $img_name,
    //         );

    //         $id = $this->notification_class_model->insertBatch($data);

    //         $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
    //         redirect('admin/notification_class/index');
    //     }
    //     $exam_result                    = $this->exam_model->get();
    //     $data['examlist']               = $exam_result;
    //     $data['superadmin_restriction'] = $this->customlib->superadmin_visible();
    //     $this->load->view('layout/header', $data);
    //     $this->load->view('admin/notification_class/notificationAdd', $data);
    //     $this->load->view('layout/footer', $data);
    // }


    public function addold()
    {
        // ===============================
        // CORS HANDLING
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ===============================
        // VALIDATION RULES
        // ===============================
        $this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
        $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
        $this->form_validation->set_rules('publish_date', 'Publish Date', 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');

        if ($this->form_validation->run() === false) {

            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'title'        => form_error('title'),
                        'message'      => form_error('message'),
                        'date'         => form_error('date'),
                        'publish_date' => form_error('publish_date'),
                        'class_id'     => form_error('class_id'),
                        'section_id'   => form_error('section_id'),
                    ]
                ]));
        }

        // ===============================
        // FILE UPLOAD (OPTIONAL)
        // ===============================
        $attachment = null;
        if (!empty($_FILES['file']['name'])) {
            $attachment = $this->media_storage->fileupload(
                "file",
                "../uploads/notice_board_images/"
            );
        }

        // ===============================
        // USER DATA
        // ===============================
        $userdata = $this->customlib->getUserData();

        // ===============================
        // INSERT DATA
        // ===============================
        $data = [
            'title'        => $this->input->post('title'),
            'message'      => $this->input->post('message'),
            'date'         => $this->safeDate($this->input->post('date')),
            'publish_date' => $this->safeDate($this->input->post('publish_date')),
            'class_id'     => $this->input->post('class_id'),
            'section_id'   => $this->input->post('section_id'),
            'created_by'   => $userdata['user_type'],
            'created_id'   => $this->customlib->getStaffID(),
            'attachment'   => $attachment
        ];

        $insert_id = $this->notification_class_model->insertBatch($data);

        // ===============================
        // RESPONSE
        // ===============================
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message'),
                'id'      => $insert_id
            ]));
    }


    public function add()
    {
        // ===============================
        // CORS HANDLING
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ===============================
        // VALIDATION
        // ===============================
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('message', 'Message', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('publish_date', 'Publish Date', 'required');
        $this->form_validation->set_rules('class_id', 'Class', 'required');
        $this->form_validation->set_rules('section_id', 'Section', 'required');

        if ($this->form_validation->run() === false) {

            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }


        // echo '<pre>';print_r($_POST);exit;

        // ===============================
        // FILE UPLOAD
        // ===============================

        // $userdata = $this->customlib->getUserData();


        // echo '<pre>';
        // print_r($_FILES);
        // exit;




        // if (!empty($_FILES['files']['name'])) {

        //     foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {

        //         if (!empty($tmpName)) {

        //             $attachment = time() . $_FILES['files']['name'][$index];

        //             move_uploaded_file(
        //                 $tmpName,
        //                 "./uploads/notice_board_images/" . $attachment
        //             );
        //         }
        //     }
        // }

                $attachment = null;
        if (!empty($_FILES['files']['name'])) {
            $attachment = $this->media_storage->fileupload(
                "files",
                "../uploads/notice_board_images/"
            );
        }


        $data = [
            'title'        => $this->input->post('title'),
            'message'      => $this->input->post('message'),
            'date'         => $this->safeDate($this->input->post('date')),
            'publish_date' => $this->safeDate($this->input->post('publish_date')),
            'class_id'     => $this->input->post('class_id'),
            'section_id'   => $this->input->post('section_id'),
            // 'created_by'   => $userdata['user_type'],
            'created_by'   => '',

            'created_id'   => $this->customlib->getStaffID(),
            'attachment'   => $attachment
        ];

        // echo "<pre>";
        // print_r($data);
        // exit;

        $insert_id = $this->notification_class_model->insertBatch($data);

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Notice created successfully',
                'id'      => $insert_id,
                'file'    => $attachment
            ]));
    }


    // public function edit($id)
    // {
    //     $userdata         = $this->customlib->getUserData();
    //     $user_id          = $userdata["id"];
    //     $usernotification = $this->notification_class_model->get($id);
    //     if ((!$this->rbac->hasPrivilege('notice_board', 'can_edit'))) {
    //         if ($usernotification['created_id'] != $user_id) {

    //             access_denied();
    //         }
    //     }
    //     $data['id']   = $id;
    //     $notification = $this->notification_class_model->get($id);

    //     $class              = $this->class_model->get();
    //     $data['classlist']  = $class;

    //     $data['notification'] = $notification;
    //     $data['roles']        = $this->role_model->get();
    //     $data['title']        = 'Edit Notification';
    //     $data['title_list']   = 'Notification List';
    //     $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('message', $this->lang->line('message'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date', $this->lang->line('circular_date'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('publish_date', $this->lang->line('publish_on'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
    //     $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {

    //     } else {

    //         $userdata    = $this->customlib->getUserData();


    //         $data = array(
    //             'id'              => $id,
    //             'message'         => $this->input->post('message'),
    //             'title'           => $this->input->post('title'),
    //             'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
    //             'created_by'      => $userdata["user_type"],
    //             'created_id'      => $this->customlib->getStaffID(),
    //             'class_id'        => $this->input->post('class_id'),
    //             'section_id'      => $this->input->post('section_id'),
    //             'publish_date'    => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('publish_date'))),    

    //         );

    //          if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {

    //             $img_name = $this->media_storage->fileupload("file", "./uploads/notice_board_images/");
    //         } else {
    //             $img_name = $notification['attachment'];
    //         }

    //         $data['attachment'] = $img_name;

    //         if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
    //             if ($notification['attachment'] != '') {
    //                 $this->media_storage->filedelete($notification['attachment'], "uploads/school_income");
    //             }
    //         }

    //         $this->notification_class_model->insertBatch($data);            

    //         $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
    //         redirect('admin/notification_class/index');
    //     }
    //     $exam_result      = $this->exam_model->get();
    //     $data['examlist'] = $exam_result;
    //     $this->load->view('layout/header', $data);
    //     $this->load->view('admin/notification_class/notificationEdit', $data);
    //     $this->load->view('layout/footer', $data);
    // }


    public function getNotification($id = null)
    {
        // ===============================
        // CORS HANDLING
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        // Allow only GET
        if ($this->input->method() !== 'get') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // Validate ID
        if (empty($id)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Notification ID is required'
                ]));
        }
    
        // Fetch notification
        $notification = $this->notification_class_model->get($id);
    
        if (!$notification) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Notification not found'
                ]));
        }
    
        // Return only details
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $notification
            ]));
    }

    public function edit($id)
    {
        // Handle preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return;
        }

        // Allow only POST / PUT
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // $userdata = $this->customlib->getUserData();
        // $user_id  = $userdata['id'];

        $notification = $this->notification_class_model->get($id);
        if (!$notification) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Notification not found'
                ]));
        }

        // RBAC + ownership check
        // if (!$this->rbac->hasPrivilege('notice_board', 'can_edit')) {
        //     if ($notification['created_id'] != $user_id) {
        //         return $this->output
        //             ->set_status_header(403)
        //             ->set_content_type('application/json')
        //             ->set_output(json_encode([
        //                 'status' => false,
        //                 'message' => 'Access denied'
        //             ]));
        //     }
        // }

        // Validation
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('title', 'Title', 'required|trim|xss_clean');
        $this->form_validation->set_rules('message', 'Message', 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', 'Date', 'required|trim');
        $this->form_validation->set_rules('publish_date', 'Publish Date', 'required|trim');
        $this->form_validation->set_rules('class_id', 'Class', 'required|trim');
        $this->form_validation->set_rules('section_id', 'Section', 'required|trim');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        // Prepare data
        $data = [
            'id'           => $id,
            'title'        => $this->input->post('title'),
            'message'      => $this->input->post('message'),
            'date'         => $this->safeDate($this->input->post('date')),
            'publish_date' => $this->safeDate($this->input->post('publish_date')),
            'class_id'     => $this->input->post('class_id'),
            'section_id'   => $this->input->post('section_id'),
            'created_by'   => $userdata['user_type'],
            'created_id'   => $this->customlib->getStaffID(),
        ];

        // File upload
        if (!empty($_FILES['file']['name'])) {
            $img_name = $this->media_storage->fileupload(
                "file",
                "../uploads/notice_board_images/"
            );

            if (!empty($notification['attachment'])) {
                $this->media_storage->filedelete(
                    $notification['attachment'],
                    "uploads/notice_board_images"
                );
            }

            $data['attachment'] = $img_name;
        } else {
            $data['attachment'] = $notification['attachment'];
        }

        // Save
        $result = $this->notification_class_model->insertBatch($data);

        if (!$result) {
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Failed to update notification'
                ]));
        }

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Notification updated successfully',
                'id' => $id
            ]));
    }



    // public function delete($id)
    // {
    //     $userdata         = $this->customlib->getUserData();
    //     $user_id          = $userdata["id"];
    //     $usernotification = $this->notification_class_model->get($id);
    //     if ((!$this->rbac->hasPrivilege('notice_board', 'can_edit'))) {
    //         if ($usernotification['created_id'] != $user_id) {
    //             access_denied();
    //         }
    //     }
    //     $this->notification_class_model->remove($id);
    //     unlink("./uploads/notice_board_images/" . $usernotification['attachment']);
    //     redirect('admin/notification_class');
    // }


    public function delete($id)
    {
        // Handle preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return;
        }

        // Allow only DELETE or POST
        if (!in_array($_SERVER['REQUEST_METHOD'], ['DELETE', 'POST'])) {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // $userdata = $this->customlib->getUserData();
        // $user_id  = $userdata['id'];

        $notification = $this->notification_class_model->get($id);
        if (!$notification) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Notification not found'
                ]));
        }

        // RBAC + ownership check
        // if (!$this->rbac->hasPrivilege('notice_board', 'can_edit')) {
        //     if ($notification['created_id'] != $user_id) {
        //         return $this->output
        //             ->set_status_header(403)
        //             ->set_content_type('application/json')
        //             ->set_output(json_encode([
        //                 'status'  => false,
        //                 'message' => 'Access denied'
        //             ]));
        //     }
        // }

        // Remove DB record
        $result = $this->notification_class_model->remove($id);
        if (!$result) {
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Failed to delete notification'
                ]));
        }

        // Remove attachment safely
        if (!empty($notification['attachment'])) {
            $file_path = FCPATH . 'uploads/notice_board_images/' . $notification['attachment'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Notification deleted successfully',
                'id'      => $id
            ]));
    }



    private function safeDate($date)
    {
        if (empty($date)) {
            return null;
        }

        $ts = strtotime($date);
        return $ts ? date('Y-m-d', $ts) : null;
    }

    public function setting()
    {
        if (!$this->rbac->hasPrivilege('notification_setting', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'notification/setting');
        $data                     = array();
        $data['title']            = 'Email Config List';
        $notificationlist         = $this->notificationsetting_model->get();
        $data['notificationlist'] = $notificationlist;
        $this->form_validation->set_rules('email_type', $this->lang->line('email_type'), 'required');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $ids          = $this->input->post('ids');
            $update_array = array();
            foreach ($ids as $id_key => $id_value) {
                $array = array(
                    'id'                    => $id_value,
                    'is_mail'               => 0,
                    'is_sms'                => 0,
                    'is_student_recipient'  => 0,
                    'is_guardian_recipient' => 0,
                    'is_staff_recipient'    => 0,
                );
                $mail               = $this->input->post('mail_' . $id_value);
                $sms                = $this->input->post('sms_' . $id_value);
                $notification       = $this->input->post('notification_' . $id_value);
                $student_recipient  = $this->input->post('student_recipient_' . $id_value);
                $guardian_recipient = $this->input->post('guardian_recipient_' . $id_value);
                $staff_recipient    = $this->input->post('staff_recipient_' . $id_value);
                if (isset($mail)) {
                    $array['is_mail'] = $mail;
                }
                if (isset($sms)) {
                    $array['is_sms'] = $sms;
                }
                if (isset($notification)) {
                    $array['is_notification'] = $notification;
                } else {
                    $array['is_notification'] = 0;
                }
                if (isset($student_recipient)) {
                    $array['is_student_recipient'] = $student_recipient;
                }
                if (isset($guardian_recipient)) {
                    $array['is_guardian_recipient'] = $guardian_recipient;
                }
                if (isset($staff_recipient)) {
                    $array['is_staff_recipient'] = $staff_recipient;
                }

                $update_array[] = $array;
            }

            $this->notificationsetting_model->updatebatch($update_array);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/notification_class/setting');
        }

        $data['title'] = 'Email Config List';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/notification_class/setting', $data);
        $this->load->view('layout/footer', $data);
    }

    public function read()
    {
        $array           = array('status' => "fail", 'msg' => $this->lang->line('something_went_wrong'));
        $notification_id = $this->input->post('notice');
        if ($notification_id != "") {
            $staffid = $this->customlib->getStaffID();
            $data    = $this->notification_class_model->updateStatusforStaff($notification_id, $staffid);
            $array   = array('status' => "success", 'data' => $data, 'msg' => $this->lang->line('update_message'));
        }

        echo json_encode($array);
    }

    public function gettemplate()
    {
        $id             = $this->input->post('id');
        $data['record'] = $this->notificationsetting_model->get($id);

        $template = $this->load->view('admin/notification_class/gettemplate', $data, true);
        $response = array('status' => 1, 'template' => $template);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function savetemplate()
    {
        $response = array();
        $this->form_validation->set_rules('temp_id', $this->lang->line('template_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('template_message', $this->lang->line('template_message'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('template_subject', $this->lang->line('subject'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'temp_id'          => form_error('temp_id'),
                'template_message' => form_error('template_message'),
                'template_subject' => form_error('template_subject'),
            );
            $response = array('status' => 0, 'error' => $data);
        } else {

            $data_update = array(
                'id'          => $this->input->post('temp_id'),
                'template_id' => $this->input->post('template_id'),
                'template'    => $this->input->post('template_message'),
                'subject'     => $this->input->post('template_subject'),
            );

            $this->notificationsetting_model->update($data_update);
            $response = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
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

            if ($files = filesize($_FILES['file']['tmp_name'])) {

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
        $notification = $this->notification_class_model->notification($id);
        $this->media_storage->filedownload($notification['attachment'], "uploads/notice_board_images");
    }

    public function notification()
    {
        $message_id       = $this->input->post('message_id');
        $notificationlist = $this->notification_class_model->get($message_id);
        $data['notificationlist'] = $notificationlist;

        $userdata        = $this->customlib->getUserData();
        $role_id         = $userdata["role_id"];
        $user_id         = $userdata["id"];

        $created_by_name = $this->staff_model->get($notificationlist["created_id"]);
        $roles           = $notificationlist["roles"];

        $staff_id = '';
        if ($created_by_name["employee_id"] != '') {
            $staff_id = ' (' . $created_by_name["employee_id"] . ')';
        }

        $arr = explode(",", $roles);

        $data['notificationlist']["role_name"]      = $this->notification_class_model->getRole($arr);

        if (!empty($created_by_name["name"])) {
            $data['notificationlist']["createdby_name"] =  "<li><i class='fa fa-user pr-1'></i>" . $this->lang->line('created_by') . ':' . $created_by_name["name"] . " " . $created_by_name["surname"] . $staff_id . "</li>";
        } else {
            $data['notificationlist']["createdby_name"] = '';
        }

        $page = $this->load->view('admin/notification_class/_notification', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }
}

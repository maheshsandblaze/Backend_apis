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

    public function index()
    {
        // =======================
        // HANDLE PREFLIGHT REQUEST
        // =======================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


    
        $user_role = "parent";
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
        // STUDENT CORE DATA
        // ===============================
        $student_id = $auth->login_id;

        $student_current_class = $this->student_model->get($student_id);

     

        if (empty($student_current_class)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Class details not found'
            ]);
            return;
        }

        $class_id   = $student_current_class['class_id'];
        $section_id =   $student_current_class['section_id'];

                 
          

        /* =======================
       FETCH NOTIFICATIONS
    ======================== */
        if ($user_role == 'student') {
            $notifications = $this->notification_class_model
                ->getNotificationForStudent($class_id, $section_id);
        } elseif ($user_role == 'parent') {
            $notifications = $this->notification_class_model
                ->getNotificationForParent($class_id, $section_id);
        } else {
            echo json_encode([
                'status'  => false,
                'message' => 'Invalid user role'
            ]);
            return;
        }

        // echo "<pre>";
        // print_r($notifications);
        // exit;

        /* =======================
       FILTER BY PUBLISH DATE
    ======================== */
        $notification_bydate = [];
        $today = date('Y-m-d');

        foreach ($notifications as $value) {
            if (strtotime($today) >= strtotime($value['publish_date'])) {
                $notification_bydate[] = $value;
            }
        }

        /* =======================
       API RESPONSE
    ======================== */
        echo json_encode([
            'status'  => true,
            'message' => 'Notifications fetched successfully',
            'data'    => [
                'notifications' => $notification_bydate
            ]
        ]);
    }
    public function updatestatus()
    {
        $notification_id = $this->input->post('notification_id');

        $user_role = $this->customlib->getUserRole();
        if ($user_role == 'student') {
            $student_id = $this->customlib->getStudentSessionUserID();
            $data       = $this->notification_class_model->updateStatus($notification_id, $student_id);
        } elseif ($user_role == 'parent') {
            $parent_id = $this->customlib->getUsersID();
            $data      = $this->notification_class_model->updateStatusforParent($notification_id, $parent_id);
        }

        $array = array('status' => "success", 'data' => $data);
        echo json_encode($array);
    }

    public function read()
    {
        $array           = array('status' => "fail", 'msg' => $this->lang->line('something_went_wrong'));
        $notification_id = $this->input->post('notice');
        if ($notification_id != "") {
            $student_id = $this->customlib->getStudentSessionUserID();
            $data       = $this->notification_class_model->updateStatusforStudent($notification_id, $student_id);
            $array      = array('status' => "success", 'data' => $data, 'msg' => $this->lang->line('delete_message'));
        }

        echo json_encode($array);
    }

    public function download($id)
    {
        $notification = $this->notification_class_model->notification($id);
        $this->media_storage->filedownload($notification['attachment'], "uploads/notice_board_images");
    }

    public function notification()
    {
        $settingresult           = $this->setting_model->getSetting();
        $superadmin_restriction  = $settingresult->superadmin_restriction;
        //------------------------------------------------        

        $message_id               = $this->input->post('message_id');
        $notificationlist         = $this->notification_class_model->notification($message_id);

        if ($superadmin_restriction == 'disabled') {
            $staff = $this->staff_model->get($notificationlist['staff_id']);
            if ($staff['role_id'] != 7) {
                $notificationlist['created_by'] = ($notificationlist['surname'] != "") ? $notificationlist["name"] . " " . $notificationlist["surname"] . "  (" . $notificationlist["employee_id"] . ")" : $notificationlist["name"] . " (" . $notificationlist['employee_id'] . ")";
            } else {
                $notificationlist['created_by'] = '';
            }
        } else {
            $notificationlist['created_by'] = ($notificationlist['surname'] != "") ? $notificationlist["name"] . " " . $notificationlist["surname"] . "  (" . $notificationlist["employee_id"] . ")" : $notificationlist["name"] . " (" . $notificationlist['employee_id'] . ")";
        }

        $data['notificationlist'] = $notificationlist;

        $page                     = $this->load->view('user/notification_class/_notification', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }
}

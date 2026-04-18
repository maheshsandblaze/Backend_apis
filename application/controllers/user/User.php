<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends Public_Controller
{
    public $school_name;
    public $school_setting;
    public $setting;
    public $payment_method;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->payment_method     = $this->paymentsetting_model->getActiveMethod();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model(array("student_edit_field_model", 'marksdivision_model', 'offlinePayment_model', 'module_model'));
        $this->load->model('behavioural_model');


        $this->config->load('mailsms');
        $this->load->helper('custom_helper');
        $this->result = $this->customlib->getLoggedInUserData();
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/student/header');
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/student/footer');
    }

    public function choose()
    {
        // ===============================
        // HANDLE PREFLIGHT
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ===============================
        // TOKEN VALIDATION
        // ===============================
        $auth = $this->auth->validate_user();

        // echo "<pre>";print_r($auth);exit;

        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthorized'
                ]));
        }

        $login_id = $auth->login_id;
        $role     = $auth->role;

        $student = $this->student_model->get($login_id);

        // echo "<pre>";print_r($student);exit;

        $parent_id = $student['parent_id'];


        // =====================================
        // GET METHOD → RETURN CLASS LIST
        // =====================================
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if ($role == "student") {

                $student_lists = $this->studentsession_model
                    ->searchMultiClsSectionByStudent($login_id);

                if (empty($student_lists)) {
                    $student_lists = $this->studentsession_model
                        ->getMultiClsSectionByStudentOldSession($login_id);
                }
            } elseif ($role == "parent") {



                $student_lists = $this->student_model
                    ->getParentChilds($login_id);
            } else {
                return $this->output
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Access denied'
                    ]));
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'data' => [
                        'role' => $role,
                        'student_lists' => $student_lists ?? []
                    ]
                ]));
        }

        // =====================================
        // POST METHOD → SWITCH CLASS
        // =====================================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $input = json_decode(file_get_contents("php://input"), true);
            $student_session_id = $input['student_session_id'] ?? '';

            if (empty($student_session_id)) {
                return $this->output
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'student_session_id is required'
                    ]));
            }

            $student = $this->student_model
                ->getByStudentSession($student_session_id);

            if (!$student) {
                return $this->output
                    ->set_status_header(404)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Student session not found'
                    ]));
            }

            // Update default login
            $this->studentsession_model->updateById([
                'id' => $student_session_id,
                'default_login' => 1
            ]);

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'message' => 'Class switched successfully',
                    'data' => [
                        'class_id' => $student['class_id'],
                        'section_id' => $student['section_id'],
                        'student_session_id' => $student['student_session_id']
                    ]
                ]));
        }

        // ===============================
        // METHOD NOT ALLOWED
        // ===============================
        return $this->output
            ->set_status_header(405)
            ->set_output(json_encode([
                'status' => false,
                'message' => 'Method Not Allowed'
            ]));
    }

    public function fees()
    {
        $data['sch_setting']   = $this->sch_setting_detail;
        $id                    = $this->customlib->getStudentSessionUserID();
        $student_current_class = $this->customlib->getStudentCurrentClsSection();

        if ($this->sch_setting_detail->is_student_feature_lock) {
            $lock_grace_period = $this->sch_setting_detail->lock_grace_period;
            $date              = date('Y-m-d', strtotime(date("Y-m-d")) - (86400 * $lock_grace_period));
            $this->session->set_userdata('top_menu', 'fees');
            $this->session->set_userdata('sub_menu', 'student/getFees');
            $category                = $this->category_model->get();
            $data['categorylist']    = $category;
            $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
            $paymentoption           = $this->customlib->checkPaypalDisplay();
            $data['paymentoption']   = $paymentoption;
            $data['payment_method']  = false;
            if (!empty($this->payment_method)) {
                $data['payment_method'] = true;
            }

            $student_id      = $id;
            $student         = $this->student_model->getStudentByClassSectionID($student_current_class->class_id, $student_current_class->section_id, $student_id);
            $class_id        = $student_current_class->class_id;
            $section_id      = $student_current_class->section_id;
            $data['title']   = 'Student Details';
            $student_due_fee = $this->studentfeemaster_model->getDueFeesByStudent($student_current_class->student_session_id, $date);

            if (!empty($student_due_fee)) {
                foreach ($student_due_fee as $result_key => $result_value) {

                    if ($result_value->is_system == 0) {
                        $student_due_fee[$result_key]->{'amount'} = $result_value->fee_amount;
                    }
                    if ($result_value->amount > 0) {

                        if (isJSON($result_value->amount_detail)) {

                            $total_balance = 0;
                            $fee_paid      = 0;
                            $fee_discount  = 0;
                            $fee_fine      = 0;
                            $fee_deposits  = json_decode(($result_value->amount_detail));

                            foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                $fee_paid += $fee_deposits_value->amount;
                                $fee_discount += $fee_deposits_value->amount_discount;
                                $fee_fine += $fee_deposits_value->amount_fine;
                            }
                            $total_balance = ($result_value->amount + $result_value->fine_amount) - ($fee_paid + $fee_fine + $fee_discount);

                            if ($total_balance <= 0) {

                                unset($student_due_fee[$result_key]);
                            }
                        }
                    } else {
                        unset($student_due_fee[$result_key]);
                    }
                }
            }

            $data['student_due_fee'] = $student_due_fee;

            $data['student'] = $student;

            $transport_fees = [];

            $module = $this->module_model->getPermissionByModulename('transport');
            if ($module['is_active']) {

                $transport_fees  = $this->studentfeemaster_model->getDueTransportFeeByStudent($student['student_session_id'], $student['route_pickup_point_id'], $date);
            }


            if (!empty($transport_fees)) {
                foreach ($transport_fees as $trans_fee_key => $trans_fee_value) {

                    if (isJSON($trans_fee_value->amount_detail)) {
                        $total_balance      = 0;
                        $fee_paid           = 0;
                        $fee_discount       = 0;
                        $fee_fine           = 0;
                        $trans_fee_deposits = json_decode(($trans_fee_value->amount_detail));

                        foreach ($trans_fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                            $fee_paid += $fee_deposits_value->amount;
                            $fee_discount += $fee_deposits_value->amount_discount;
                            $fee_fine += $fee_deposits_value->amount_fine;
                        }
                        $total_balance = ($trans_fee_value->fees + $trans_fee_value->fine_amount) - ($fee_paid + $fee_fine + $fee_discount);

                        if ($total_balance <= 0) {

                            unset($transport_fees[$trans_fee_key]);
                        }
                    }
                }
            }

            $data['transport_fees'] = $transport_fees;
            $this->load->view('layout/student/header', $data);
            $this->load->view('user/student/fees', $data);
            $this->load->view('layout/student/footer', $data);
        } else {
            redirect('user/user/dashboard');
        }
    }

    // public function profile()
    // {

    //     $this->session->set_userdata('top_menu', 'my_profile');
    //     $student_id            = $this->customlib->getStudentSessionUserID();
    //     $student_current_class = $this->customlib->getStudentCurrentClsSection();
    //     $marks_division        = $this->marksdivision_model->get();
    //     $student               = $this->student_model->getStudentByClassSectionID($student_current_class->class_id, $student_current_class->section_id, $student_id);

    //     $superadmin_visible =    $this->Setting_model->get();      

    //     $data                   = array();
    //     $data['superadmin_restriction'] =   $superadmin_visible[0]['superadmin_restriction'];
    //     $data['marks_division'] = $marks_division;
    //     if (!empty($student)) {
    //         $transport_fees=[];

    //         $student_session_id           = $student_current_class->student_session_id;
    //         $gradeList                    = $this->grade_model->get();
    //         $student_due_fee              = $this->studentfeemaster_model->getStudentFees($student_session_id);
    //         $student_discount_fee         = $this->feediscount_model->getStudentFeesDiscount($student_session_id);
    //         $data['student_discount_fee'] = $student_discount_fee;
    //         $data['student_due_fee']      = $student_due_fee;
    //         $timeline                     = $this->timeline_model->getStudentTimeline($student["id"], $status = 'yes');
    //         $data["timeline_list"]        = $timeline;
    //         $data['sch_setting']          = $this->sch_setting_detail;
    //         $data['adm_auto_insert']      = $this->sch_setting_detail->adm_auto_insert;
    //         $data['examSchedule']         = array();
    //         $data['exam_result']          = $this->examgroupstudent_model->searchStudentExams($student['student_session_id'], true, true);
    //         $ss                           = $this->grade_model->getGradeDetails();
    //         $data['exam_grade']           = $this->grade_model->getGradeDetails();
    //         $student_doc                  = $this->student_model->getstudentdoc($student_id);
    //         $data['student_doc']          = $student_doc;
    //         $data['student_doc_id']       = $student_id;
    //         $category_list                = $this->category_model->get();
    //         $data['category_list']        = $category_list;
    //         $data['gradeList']            = $gradeList;
    //         $data['student']              = $student;

    //         $startmonth = $this->setting_model->getStartMonth();

    //         $monthlist         = $this->customlib->getMonthNoDropdown($startmonth);
    //         $data["monthlist"] = $monthlist;

    //         $attendencetypes = $this->attendencetype_model->getAttType();

    //         $data['attendencetypeslist'] = $attendencetypes;

    //         $year = date("Y");

    //         $input       = $this->setting_model->getCurrentSessionName();
    //         list($a, $b) = explode('-', $input);
    //         $start_year  = $a;
    //         if (strlen($b) == 2) {
    //             $Next_year = substr($a, 0, 2) . $b;
    //         } else {
    //             $Next_year = $b;
    //         }

    //         $start_end_month = $this->startmonthandend();

    //         $session_year_start = date("Y-m-01", strtotime($start_year . '-' . $start_end_month[0] . '-01'));
    //         $session_year_end   = date("Y-m-t", strtotime($Next_year . '-' . $start_end_month[1] . '-01'));

    //         $countAttendance = $this->countAttendance($session_year_start, $student['student_session_id']);
    //         $data['behavioural_note'] = $this->behavioural_model->getstudentbehaviouralnote($student['student_session_id']);

    //         $attendences     = $this->stuattendence_model->student_attendence_bw_date($session_year_start, $session_year_end, $student['student_session_id']);

    //         if (!empty($attendences)) {
    //             foreach ($attendences as $att_key => $att_value) {
    //                 $res[$att_value->date] = $attendences[$att_key];
    //             }
    //         }
    //         $st = $start_year;

    //         foreach ($monthlist as $key => $value) {

    //             $datemonth = $key;

    //             if ($datemonth < $this->sch_setting_detail->start_month) {
    //                 $st = $Next_year;
    //             }

    //             $date_each_month = date($st . '-' . $datemonth . '-01');
    //             $date_end        = date('t', strtotime($date_each_month));
    //             for ($n = 1; $n <= $date_end; $n++) {
    //                 $att_date           = sprintf("%02d", $n);
    //                 $attendence_array[] = $att_date;

    //                 $att_dates = $st . "-" . $datemonth . "-" . sprintf("%02d", $n);

    //                 $date_array[]    = $att_dates;
    //                 $res[$att_dates] = $this->stuattendence_model->studentattendance($att_dates, $student['student_session_id']);
    //             }
    //         }

    //         $data["session_year_start"] = $session_year_start;
    //         $data["session_year_end"]   = $session_year_end;
    //         $data["countAttendance"]    = $countAttendance;
    //         $data["resultlist"]         = $res;
    //         $data["start_year"]         = $start_year;
    //         $data["Next_year"]          = $Next_year;
    //         $transport_fees=[];
    //         $module=$this->module_model->getPermissionByModulename('transport');
    //             if($module['is_active']){

    //                   $transport_fees         = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $student['route_pickup_point_id']);

    //             }

    //              $data['transport_fees'] = $transport_fees;

    //     //------- Behaviour Report Start--------
    //         if($this->module_lib->hasModule('behaviour_records') && $this->studentmodule_lib->hasActive('behaviour_records') ){ 

    //             $this->load->model("studentincidents_model");
    //             // This is used to get total points of student by student id 
    //             $total_points = $this->studentincidents_model->totalpoints($student_id);
    //             $student['total_points'] = $total_points['totalpoints'];

    //         }
    //     //------- Behaviour Report End----------

    //         $data['student']              = $student;

    //     }else{
    //          redirect('site/logout');
    //     }        

    //     //------- Behaviour Report Start--------
    //     if ($this->module_lib->hasModule('behaviour_records')) {

    //         $this->load->model("studentincidents_model");

    //         // This is used to get assign incident record of student by student id
    //         $data['assignstudent'] = $this->studentincidents_model->studentbehaviour($student_id);

    //         $this->load->model('studentbehaviour_model');
    //         $data['behavioursetting'] = $this->studentbehaviour_model->getsettings();
    //         $data['role']    = $this->customlib->getUserRole(); 
    //     }               
    //     //------- Behaviour Report End----------       

    //     $unread_notifications = $this->notification_model->getUnreadStudentNotification();
    //     $notification_bydate  = array();

    //     foreach ($unread_notifications as $unread_notifications_key => $unread_notifications_value) {
    //         if (date($this->customlib->getSchoolDateFormat()) >= date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($unread_notifications_value->publish_date))) {
    //             $notification_bydate[] = $unread_notifications_value;
    //         }
    //     }

    //     $setting_data                 = $this->setting_model->get();
    //     $data['student_timeline']     = $setting_data[0]['student_timeline'];
    //     $data['unread_notifications'] = $notification_bydate;

    //     $login_id           = $this->customlib->getStudentSessionUserID();
    //     $data['student_id'] = $login_id;

    //     $this->load->view('layout/student/header', $data);
    //     $this->load->view('user/profile', $data);
    //     $this->load->view('layout/student/footer', $data);
    // }


    public function profile()
    {
        // CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Token validation
        $auth = $this->auth->validate_user();

        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthorized'
                ]));
        }

        // ===============================
        // STUDENT CORE DATA
        // ===============================
        $student_id = $auth->login_id;

        $student = $this->student_model->get($student_id);

        // echo "<pre>";print_r($student);exit;

        if (!$student) {
            $student_lists = $this->student_model
                ->getParentChilds($auth->parent_id);

            //
            $student_current_class = $this->student_model->get($student_lists[0]->id);
        }

        else{

        $student_current_class = $student;

        }



        $student = $this->student_model->getStudentByClassSectionID(
            $student_current_class['class_id'],
            $student_current_class['section_id'],
            $student_current_class['id']
        );

        // echo '<pre>';
        // print_r($student_current_class);
        // exit;



        // ===============================
        // FEES & ACADEMICS
        // ===============================
        $student_session_id = $student_current_class['student_session_id'];

        $profile = [
            'student'              => $student,
            'marks_division'       => $this->marksdivision_model->get(),
            'grade_list'           => $this->grade_model->get(),
            'student_due_fee'      => $this->studentfeemaster_model->getStudentFees($student_session_id),
            'student_discount_fee' => $this->feediscount_model->getStudentFeesDiscount($student_session_id),
            'exam_result'          => $this->examgroupstudent_model->searchStudentExams($student_session_id, true, true),
            'student_docs'         => $this->student_model->getstudentdoc($student_id),
            'timeline'             => $this->timeline_model->getStudentTimeline($student_current_class['id'], 'yes'),
            'categories'           => $this->category_model->get(),
            'attendance_types'     => $this->attendencetype_model->getAttType(),
            'behavioural_notes'    => $this->behavioural_model->getstudentbehaviouralnote($student_session_id)
        ];

        // ===============================
        // TRANSPORT FEES (MODULE SAFE)
        // ===============================
        $profile['transport_fees'] = [];

        $module = $this->module_model->getPermissionByModulename('transport');
        if ($module['is_active']) {
            $profile['transport_fees'] =
                $this->studentfeemaster_model
                ->getStudentTransportFees($student_session_id, $student_current_class['route_pickup_point_id']);
        }

        // ===============================
        // BEHAVIOUR RECORDS (OPTIONAL)
        // ===============================
        if (
            $this->module_lib->hasModule('behaviour_records') &&
            $this->studentmodule_lib->hasActive('behaviour_records')
        ) {
            $this->load->model('studentincidents_model');

            $profile['behaviour'] = [
                'total_points' => $this->studentincidents_model->totalpoints($student_id)['totalpoints'],
                'records'      => $this->studentincidents_model->studentbehaviour($student_id)
            ];
        }

        // ===============================
        // NOTIFICATIONS
        // ===============================
        $profile['unread_notifications'] =
            $this->notification_model->getUnreadStudentNotification($student_session_id);

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $profile
            ]));
    }


    // public function dashboard()
    // {
    //     $student_current_class = $this->customlib->getStudentCurrentClsSection();
    //     $session_year_detail   = sessionYearDetails($this->sch_setting_detail->session, $this->sch_setting_detail->start_month);

    //     $attendance_date           = ['start' => $session_year_detail['month_start'], 'end' => $session_year_detail['month_end']];
    //     $student_total_attendances = $this->attendencetype_model->getStudentAttendenceRange($attendance_date, $student_current_class->student_session_id);

    //     $attendence_percentage = -1;

    //     if (!empty($student_total_attendances)) {
    //         $total_attendance_count=count($student_total_attendances);
    //         $absents = 0;
    //         foreach ($student_total_attendances as $attend_key => $attend_value) {
    //             ($attend_value->attendence_type_id == 4) ? $absents++ : "";
    //         }
    //         $total_presents= $total_attendance_count-$absents;
    //         $attendence_percentage= two_digit_float(($total_presents*100)/$total_attendance_count);        
    //     }

    //     $this->session->set_userdata('top_menu', 'dashboard');
    //     $data          = array();
    //     $student_id    = $this->customlib->getStudentSessionUserID();
    //     $member_type   = "student";
    //     $checkIsMember = $this->librarymember_model->checkIsMember($member_type, $student_id);

    //     $data['bookList'] = $checkIsMember;


    //     $class_id     = $student_current_class->class_id;
    //     $section_id   = $student_current_class->section_id;
    //     $homeworklist = $this->homework_model->getStudentHomeworkWithStatus($class_id, $section_id, $student_current_class->student_session_id);
    //     foreach ($homeworklist as $key => $homeworklist_value) {
    //         $homeworklist[$key]['status'] = '';
    //         $checkstatus                  = $this->homework_model->checkstatus($homeworklist_value['id'], $student_id);
    //         if ($checkstatus['record_count'] != 0) {
    //             $homeworklist[$key]['status'] = 'submitted';
    //         }
    //     }

    //     $data["homeworklist"] = $homeworklist;

    //     $student               = $this->student_model->getStudentByClassSectionID($class_id, $section_id, $student_id);
    //     $data['student']       = $student;

    //     // notification list start
    //     $user_role = $this->customlib->getUserRole();
    //     if ($user_role == 'student') {
    //         $student_id    = $this->customlib->getStudentSessionUserID();
    //         $notifications = $this->notification_model->getNotificationForStudent($student_id);
    //     } elseif ($user_role == 'parent') {
    //         $student_id    = $this->customlib->getUsersID();
    //         $notifications = $this->notification_model->getNotificationForParent($student_id);
    //     }
    //     $notification_bydate = array();
    //     foreach ($notifications as $key => $value) {

    //         if (strtotime(date('Y-m-d')) >= strtotime($value['publish_date'])) {
    //             $notification_bydate[] = $value;
    //         }
    //     }

    //     $data['notificationlist'] = $notification_bydate;
    //     // end

    //     // your progress start
    //     $subjects = $this->syllabus_model->getmysubjects($student_current_class->class_id, $student_current_class->section_id);

    //     foreach ($subjects as $key => $value) {
    //         $show_status     = 0;
    //         $teacher_summary = array();
    //         $lesson_result   = array();
    //         $complete        = 0;
    //         $incomplete      = 0;
    //         $array[]         = $value;
    //         $subject_details = $this->syllabus_model->get_subjectstatus($value->subject_group_subjects_id, $value->subject_group_class_sections_id);
    //         if ($subject_details[0]->total != 0) {

    //             $complete   = ($subject_details[0]->complete / $subject_details[0]->total) * 100;
    //             $incomplete = ($subject_details[0]->incomplete / $subject_details[0]->total) * 100;
    //             if ($value->code == '') {
    //                 $lebel = $value->name;
    //             } else {
    //                 $lebel = $value->name . ' (' . $value->code . ')';
    //             }
    //             $data['subjects_data'][$value->subject_group_subjects_id] = array(
    //                 'lebel'      => $lebel,
    //                 'complete'   => round($complete),
    //                 'incomplete' => round($incomplete),
    //                 'id'         => $value->subject_group_subjects_id . '_' . $value->code,
    //                 'total'      => $subject_details[0]->total,
    //                 'name'       => $value->name,
    //                 'graph_id'   => $value->subject_group_subjects_id . time(),
    //             );
    //         } else {

    //             $data['subjects_data'][$value->subject_group_subjects_id] = array(
    //                 'lebel'      => $value->name . ' (' . $value->code . ')',
    //                 'complete'   => 0,
    //                 'incomplete' => 0,
    //                 'id'         => $value->subject_group_subjects_id . '_' . $value->code,
    //                 'total'      => 0,
    //                 'name'       => $value->name,
    //                 'graph_id'   => $value->subject_group_subjects_id . time(),
    //             );
    //         }
    //     }
    //     // end

    //     // upcomming class start
    //     $days        = $this->customlib->getDaysname();
    //     $days_record = array();
    //     foreach ($days as $day_key => $day_value) {
    //         $days_record[$day_key] = $this->subjecttimetable_model->getparentSubjectByClassandSectionDay($student_current_class->class_id, $student_current_class->section_id, $day_key);
    //     }
    //     $data['timetable'] = $days_record;
    //     $data['attendence_percentage'] = $attendence_percentage;
    //     // end

    //     $data['visitor_list'] = $this->visitors_model->visitorbystudentid($student_current_class->student_session_id);

    //     $data['studentsession_username'] = $this->customlib->getStudentSessionUserName();
    //     $data['student_data'] = $this->customlib->getLoggedInUserData();

    //     $setting_data                 = $this->setting_model->get();
    //     $data['low_attendance_limit']     = $setting_data[0]['low_attendance_limit'];


    //     $data['teachers']   = $teachers   = array();
    //     $student_teacher = $this->subjecttimetable_model->getTeacherByClassandSection($student_current_class->class_id, $student_current_class->section_id); 

    //     foreach ($student_teacher as $value) {
    //         $teachers[$value->staff_id][] = $value;
    //     }

    //     $data['teacherlist']         = $teachers;        

    //     $this->load->view('layout/student/header', $data);
    //     $this->load->view('user/dashboard', $data);
    //     $this->load->view('layout/student/footer', $data);
    // }


    public function dashboardold()
    {
        // CORS error
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ✅ AUTH USING TOKEN
        $auth = $this->auth->validate_user();

        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthorized'
                ]));
        }

        // ✅ BUILD SESSION DATA FROM DB
        $student = $this->student_model->get($auth->login_id);

        // $student_current_class =
        //     $this->customlib->getStudentClassByStudentId($student->id);

        // Attendance
        $session_year_detail = sessionYearDetails(
            $this->sch_setting_detail->session,
            $this->sch_setting_detail->start_month
        );

        $attendance_date = [
            'start' => $session_year_detail['month_start'],
            'end'   => $session_year_detail['month_end']
        ];

        $attendances = $this->attendencetype_model
            ->getStudentAttendenceRange(
                $attendance_date,
                $student['student_session_id']
            );

        $percentage = -1;
        if (!empty($attendances)) {
            $total = count($attendances);
            $absent = 0;
            foreach ($attendances as $row) {
                if ($row->attendence_type_id == 4) {
                    $absent++;
                }
            }
            $percentage = two_digit_float((($total - $absent) * 100) / $total);
        }



        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data' => [
                    'student' => [
                        'id' => $student['id'],
                        'name' => $student['firstname'] . ' ' . $student['lastname'],
                        'admission_no' => $student['admission_no']
                    ],
                    'class_id' => $student['class_id'],
                    'section_id' => $student['section_id'],
                    'attendance_percentage' => $percentage,
                    'sch_setting'   => $this->sch_setting_detail
                ]
            ]));
    }

    public function dashboard()
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




        // echo '<pre>';print_r($auth);exit;

        $student_id = $auth->login_id;



        // ===============================
        // STUDENT DETAILS
        // ===============================


        $student = $this->student_model->get($student_id);


        if (!$student) {
            $student_lists = $this->student_model
                ->getParentChilds($auth->parent_id);

            //  echo '<pre>';print_r($student_lists);exit;
            $student = $this->student_model->get($student_lists[0]->id);
        }
        $class_id   = $student['class_id'];
        $section_id = $student['section_id'];
        $student_session_id = $student['student_session_id'];

        // ===============================
        // ATTENDANCE PERCENTAGE
        // ===============================
        $setting_data = $this->setting_model->get();
        $session_year_detail = sessionYearDetails(
            $setting_data[0]['session'],
            $setting_data[0]['start_month']
        );

        $attendance_date = [
            'start' => $session_year_detail['month_start'],
            'end'   => $session_year_detail['month_end']
        ];

        $attendance_records = $this->attendencetype_model
            ->getStudentAttendenceRange($attendance_date, $student_session_id);

        $attendance_percentage = 0;

        if (!empty($attendance_records)) {
            $total = count($attendance_records);
            $absent = 0;

            foreach ($attendance_records as $row) {
                if ($row->attendence_type_id == 4) {
                    $absent++;
                }
            }

            $present = $total - $absent;
            $attendance_percentage = round(($present * 100) / $total, 2);
        }

        // ===============================
        // HOMEWORK
        // ===============================
        $homeworklist = $this->homework_model
            ->getStudentHomeworkWithStatus($class_id, $section_id, $student_session_id);

        foreach ($homeworklist as $key => $value) {
            $checkstatus = $this->homework_model
                ->checkstatus($value['id'], $student_id);

            $homeworklist[$key]['status'] =
                ($checkstatus['record_count'] != 0) ? 'submitted' : 'pending';
        }

        // ===============================
        // NOTIFICATIONS
        // ===============================
        $notifications = $this->notification_model
            ->getNotificationForStudent($student_id);

        $notification_bydate = [];
        $today = date('Y-m-d');

        foreach ($notifications as $value) {
            if (strtotime($today) >= strtotime($value['publish_date'])) {
                $notification_bydate[] = $value;
            }
        }

        // ===============================
        // SUBJECT PROGRESS
        // ===============================
        $subjects = $this->syllabus_model
            ->getmysubjects($class_id, $section_id);

        $subjects_data = [];

        foreach ($subjects as $value) {

            $subject_details = $this->syllabus_model
                ->get_subjectstatus(
                    $value->subject_group_subjects_id,
                    $value->subject_group_class_sections_id
                );

            if ($subject_details[0]->total != 0) {

                $complete = round(
                    ($subject_details[0]->complete / $subject_details[0]->total) * 100
                );

                $incomplete = round(
                    ($subject_details[0]->incomplete / $subject_details[0]->total) * 100
                );
            } else {
                $complete = 0;
                $incomplete = 0;
            }

            $subjects_data[] = [
                'subject_name' => $value->name,
                'code'         => $value->code,
                'complete'     => $complete,
                'incomplete'   => $incomplete,
                'total_topics' => $subject_details[0]->total
            ];
        }

        // ===============================
        // TIMETABLE
        // ===============================
        $days = $this->customlib->getDaysname();
        $timetable = [];

        foreach ($days as $day_key => $day_value) {
            $timetable[$day_key] =
                $this->subjecttimetable_model
                ->getparentSubjectByClassandSectionDay(
                    $class_id,
                    $section_id,
                    $day_key
                );
        }

        // ===============================
        // VISITORS
        // ===============================
        $visitors = $this->visitors_model
            ->visitorbystudentid($student_session_id);

        // ===============================
        // TEACHERS
        // ===============================
        $teacher_raw = $this->subjecttimetable_model
            ->getTeacherByClassandSection($class_id, $section_id);

        $teachers = [];
        foreach ($teacher_raw as $value) {
            $teachers[$value->staff_id][] = $value;
        }

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'student'               => $student,
                    'attendance_percentage' => $attendance_percentage,
                    'low_attendance_limit'  => $setting_data[0]['low_attendance_limit'],
                    'homework'              => $homeworklist,
                    'notifications'         => $notification_bydate,
                    'subjects_progress'     => $subjects_data,
                    'timetable'             => $timetable,
                    'visitors'              => $visitors,
                    'teachers'              => $teachers
                ]
            ]));
    }


    public function changepass()
    {
        $role = $this->result["role"];
        if ($role == 'guest') {
            redirect('user/studentcourse/changeguestpass');
        } else {

            $data['title'] = 'Change Password';
            $this->form_validation->set_rules('current_pass', 'Current password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('new_pass', 'New password', 'trim|required|xss_clean|matches[confirm_pass]');
            $this->form_validation->set_rules('confirm_pass', 'Confirm password', 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                $sessionData            = $this->session->userdata('loggedIn');
                $this->data['id']       = $sessionData['id'];
                $this->data['username'] = $sessionData['username'];
                $this->load->view('layout/student/header', $data);
                $this->load->view('user/change_password', $data);
                $this->load->view('layout/student/footer', $data);
            } else {
                $sessionData = $this->session->userdata('student');
                $data_array  = array(
                    'current_pass' => ($this->input->post('current_pass')),
                    'new_pass'     => ($this->input->post('new_pass')),
                    'user_id'      => $sessionData['id'],
                    'user_name'    => $sessionData['username'],
                );
                $newdata = array(
                    'id'       => $sessionData['id'],
                    'password' => $this->input->post('new_pass'),
                );
                $query1 = $this->user_model->checkOldPass($data_array);
                if ($query1) {
                    $query2 = $this->user_model->saveNewPass($newdata);
                    if ($query2) {

                        $this->session->set_flashdata('success_msg', 'Password changed successfully');
                        $this->load->view('layout/student/header', $data);
                        $this->load->view('user/change_password', $data);
                        $this->load->view('layout/student/footer', $data);
                    }
                } else {

                    $this->session->set_flashdata('error_msg', 'Invalid current password');
                    $this->load->view('layout/student/header', $data);
                    $this->load->view('user/change_password', $data);
                    $this->load->view('layout/student/footer', $data);
                }
            }
        }
    }

    public function changeusername()
    {
        $sessionData   = $this->customlib->getLoggedInUserData();
        $data['title'] = 'Change Username';
        $this->form_validation->set_rules('current_username', $this->lang->line('current_password'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_username', $this->lang->line('current_password'), 'trim|required|xss_clean|matches[confirm_username]');
        $this->form_validation->set_rules('confirm_username', $this->lang->line('confirm_username'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
        } else {

            $data_array = array(
                'username'     => $this->input->post('current_username'),
                'new_username' => $this->input->post('new_username'),
                'role'         => $sessionData['role'],
                'user_id'      => $sessionData['id'],
            );
            $newdata = array(
                'id'       => $sessionData['id'],
                'username' => $this->input->post('new_username'),
            );
            $is_valid = $this->user_model->checkOldUsername($data_array);

            if ($is_valid) {
                $is_exists = $this->user_model->checkUserNameExist($data_array);
                if (!$is_exists) {
                    $is_updated = $this->user_model->saveNewUsername($newdata);
                    if ($is_updated) {
                        $this->session->set_flashdata('success_msg', $this->lang->line('username_changed_successfully'));
                        redirect('user/user/changeusername');
                    }
                } else {
                    $this->session->set_flashdata('error_msg', $this->lang->line('username_already_exists_please_choose_other'));
                }
            } else {
                $this->session->set_flashdata('error_msg', $this->lang->line('invalid_current_username'));
            }
        }
        $this->data['id']       = $sessionData['id'];
        $this->data['username'] = $sessionData['username'];
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/change_username', $data);
        $this->load->view('layout/student/footer', $data);
    }

    public function download($student_id, $id)
    {
        $student_doc = $this->student_model->studentdocbyid($id);
        $this->media_storage->filedownload($student_doc['doc'], "./uploads/student_documents/$student_id/");
    }

    public function user_language($lang_id)
    {
        $language_name = $this->db->select('languages.language,languages.is_rtl')->from('languages')->where('id', $lang_id)->get()->row_array();
        $student       = $this->session->userdata('student');

        if (!empty($student)) {
            $this->session->unset_userdata('student');
        }
        $language_array      = array('lang_id' => $lang_id, 'language' => $language_name['language']);
        $student['language'] = $language_array;

        if ($language_name['is_rtl'] == 1) {
            $student['is_rtl'] = 'enabled';
        } else {
            $student['is_rtl'] = 'disabled';
        }

        $this->session->set_userdata('student', $student);

        $session = $this->session->userdata('student');
        if ($session['role'] == 'student') {
            $id = $session['student_id'];
        } elseif ($session['role'] == 'guest') {
            $id = $session['guest_id'];
        }

        $data['lang_id'] = $lang_id;
        $language_result = $this->language_model->set_studentlang($id, $data);
    }

    public function change_currency()
    {

        $currency_id = $this->input->post('currency_id');
        //================
        $currency       = $this->currency_model->get($currency_id);
        $logged_session = $this->session->userdata('student');
        if ($logged_session['role'] == "guest") {
            $user_id = $this->customlib->getUsersID();
            $this->load->model('guest_model');
            $update_data = array('id' => $user_id, 'currency_id' => $currency_id);
            $this->guest_model->add($update_data);
        } else {

            $user_id     = $this->customlib->getUsersID();
            $update_data = array('id' => $user_id, 'currency_id' => $currency_id);
            $this->user_model->add($update_data);
        }

        $this->session->userdata['student']['currency_base_price'] = $currency->base_price;
        $this->session->userdata['student']['currency_symbol']     = $currency->symbol;
        $this->session->userdata['student']['currency']            = $currency_id;
        $this->session->userdata['student']['currency_name']       = $currency->short_name;
        echo json_encode(['status' => 1, 'message' => $this->lang->line('currency_changed_successfully')]);
    }

    // public function change_currency()
    // {
    //     // $session         = $this->session->userdata('student');
    //     // $id              = $session['id'];
    //     $currency_id=$this->input->post('currency_id');
    //     //================
    //     $currency=$this->currency_model->get($currency_id);

    //     $user_id= $this->customlib->getUsersID();
    //     $update_data=array('id'=>$user_id,'currency_id'=>$currency_id);
    //     $this->user_model->add($update_data);
    //     $this->session->userdata['student']['currency_base_price'] = $currency->base_price;
    //     $this->session->userdata['student']['currency_symbol'] = $currency->symbol;
    //     $this->session->userdata['student']['currency'] = $currency_id;
    //     $this->session->userdata['student']['currency_name'] = $currency->short_name;
    //     echo json_encode(['status' => 1, 'message' => $this->lang->line('currency_changed_successfully')]);

    // }

    public function timeline_download($timeline_id, $doc)
    {
        $this->media_storage->filedownload($this->uri->segment(4), "./uploads/student_timeline");
    }

    public function view($id)
    {
        $data['title']           = 'Student Details';
        $student                 = $this->student_model->get($id);
        $student_due_fee         = $this->studentfee_model->getDueFeeBystudent($student['class_id'], $student['section_id'], $id);
        $data['student_due_fee'] = $student_due_fee;
        $transport_fee           = $this->studenttransportfee_model->getTransportFeeByStudent($student['student_session_id']);
        $data['transport_fee']   = $transport_fee;
        $examList                = $this->examschedule_model->getExamByClassandSection($student['class_id'], $student['section_id']);
        $data['examSchedule']    = array();
        if (!empty($examList)) {
            $new_array = array();
            foreach ($examList as $ex_key => $ex_value) {
                $array         = array();
                $x             = array();
                $exam_id       = $ex_value['exam_id'];
                $exam_subjects = $this->examschedule_model->getresultByStudentandExam($exam_id, $student['id']);
                foreach ($exam_subjects as $key => $value) {
                    $exam_array                     = array();
                    $exam_array['exam_schedule_id'] = $value['exam_schedule_id'];
                    $exam_array['exam_id']          = $value['exam_id'];
                    $exam_array['full_marks']       = $value['full_marks'];
                    $exam_array['passing_marks']    = $value['passing_marks'];
                    $exam_array['exam_name']        = $value['name'];
                    $exam_array['exam_type']        = $value['type'];
                    $exam_array['attendence']       = $value['attendence'];
                    $exam_array['get_marks']        = $value['get_marks'];
                    $x[]                            = $exam_array;
                }
                $array['exam_name']   = $ex_value['exam_name'];
                $array['exam_result'] = $x;
                $new_array[]          = $array;
            }
            $data['examSchedule'] = $new_array;
        }
        return $data['student'] = $student;
    }

    public function getfees()
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
        // STUDENT CORE DATA
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

        $student = $this->student_model->getStudentByClassSectionID(
            $student_current_class['class_id'],
            $student_current_class['section_id'],
            $student_id
        );

        if (!$student) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student not found'
                ]));
        }

        $student_session_id = $student_current_class['student_session_id'];

        // ===============================
        // FEES DATA
        // ===============================
        $fees_data = [
            'student'              => $student,
            'categorylist'         => $this->category_model->get(),
            'sch_setting'          => $this->sch_setting_detail,
            'adm_auto_insert'      => $this->sch_setting_detail->adm_auto_insert,
            'paymentoption'        => $this->customlib->checkPaypalDisplay(),
            'payment_method'       => !empty($this->payment_method) ? true : false,
            'student_due_fee'      => $this->studentfeemaster_model->getStudentFees($student_session_id),
            'student_discount_fee' => $this->feediscount_model->getStudentFeesDiscount($student_session_id),
            'transport_fees'       => [],
            'student_processing_fee' => false
        ];

        // ===============================
        // TRANSPORT FEES (MODULE SAFE)
        // ===============================
        $module = $this->module_model->getPermissionByModulename('transport');
        if (!empty($module) && $module['is_active']) {
            $fees_data['transport_fees'] =
                $this->studentfeemaster_model
                ->getStudentTransportFees(
                    $student_session_id,
                    $student['route_pickup_point_id']
                );
        }

        // ===============================
        // PROCESSING FEES CHECK
        // ===============================
        $student_processing_fee =
            $this->studentfeemaster_model
            ->getStudentProcessingFees($student_session_id);

        foreach ($student_processing_fee as $processing_value) {
            if (!empty($processing_value->fees)) {
                $fees_data['student_processing_fee'] = true;
                break;
            }
        }

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $fees_data
            ]));
    }

    public function getProcessingfees()
    {
        $id                    = $this->customlib->getStudentSessionUserID();
        $student_current_class = $this->customlib->getStudentCurrentClsSection();

        $this->session->set_userdata('top_menu', 'fees');
        $this->session->set_userdata('sub_menu', 'student/getFees');
        $category                = $this->category_model->get();
        $data['categorylist']    = $category;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $paymentoption           = $this->customlib->checkPaypalDisplay();
        $data['paymentoption']   = $paymentoption;
        $data['payment_method']  = false;
        if (!empty($this->payment_method)) {
            $data['payment_method'] = true;
        }

        $student_id                   = $id;
        $student                      = $this->student_model->getStudentByClassSectionID($student_current_class->class_id, $student_current_class->section_id, $student_id);
        $class_id                     = $student_current_class->class_id;
        $section_id                   = $student_current_class->section_id;
        $data['title']                = 'Student Details';
        $student_due_fee              = $this->studentfeemaster_model->getStudentProcessingFees($student_current_class->student_session_id);
        $student_discount_fee         = $this->feediscount_model->getStudentFeesDiscount($student_current_class->student_session_id);
        $data['student_discount_fee'] = $student_discount_fee;
        $data['student_due_fee']      = $student_due_fee;
        $data['student']              = $student;
        $transport_fees               = $this->studentfeemaster_model->getStudentTransportFees($student_current_class->student_session_id, $student['route_pickup_point_id']);

        $data['transport_fees'] = $transport_fees;
        $result                 = array(
            'view' => $this->load->view('user/student/getProcessingfees', $data, true),
        );
        $this->output->set_output(json_encode($result));
    }

    public function printFeesByGroupArray()
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

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || empty($input['fees'])) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Invalid payload'
                ]));
        }

        $fees_array = [];

        foreach ($input['fees'] as $value) {

            $fee_groups_feetype_id = $value['fee_groups_feetype_id'] ?? null;
            $fee_master_id         = $value['fee_master_id'] ?? null;
            $fee_session_group_id  = $value['fee_session_group_id'] ?? null;
            $fee_category          = $value['fee_category'] ?? null;
            $trans_fee_id          = $value['trans_fee_id'] ?? null;

            if ($fee_category == "transport") {

                $feeList = $this->studentfeemaster_model
                    ->getTransportFeeByID($trans_fee_id);

                if ($feeList) {
                    $feeList->fee_category = "transport";
                }
            } else {

                $feeList = $this->studentfeemaster_model
                    ->getDueFeeByFeeSessionGroupFeetype(
                        $fee_session_group_id,
                        $fee_master_id,
                        $fee_groups_feetype_id
                    );

                if ($feeList) {
                    $feeList->fee_category = "fees";
                }
            }

            if ($feeList) {
                $fees_array[] = $feeList;
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $fees_array
            ]));
    }
    public function getcollectfee()
    {
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $record              = $this->input->post('data');
        $record_array        = json_decode($record);
        $fees_array          = array();
        foreach ($record_array as $key => $value) {
            $fee_groups_feetype_id = $value->fee_groups_feetype_id;
            $fee_master_id         = $value->fee_master_id;
            $fee_session_group_id  = $value->fee_session_group_id;
            $fee_category          = $value->fee_category;
            $trans_fee_id          = $value->trans_fee_id;

            if ($fee_category == "transport") {
                $feeList               = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
                $feeList->fee_category = $fee_category;
            } else {
                $feeList               = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
                $feeList->fee_category = $fee_category;
            }

            $fees_array[] = $feeList;
        }
        $data['feearray'] = $fees_array;
        $result           = array(
            'view' => $this->load->view('user/student/getcollectfee', $data, true),
        );
        $this->output->set_output(json_encode($result));
    }

    public function create_doc()
    {
        $this->form_validation->set_rules('first_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('first_doc', $this->lang->line('documents'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'first_title' => form_error('first_title'),
                'first_doc'   => form_error('first_doc'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $student_id = $this->input->post('student_id');

            if (isset($_FILES["first_doc"]) && !empty($_FILES['first_doc']['name'])) {
                $uploaddir = './uploads/student_documents/' . $student_id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }

                $img_name    = $this->media_storage->fileupload("first_doc", $uploaddir);
                $first_title = $this->input->post('first_title');
                $data_img    = array('student_id' => $student_id, 'title' => $first_title, 'doc' => $img_name);
                $this->student_model->adddoc($data_img);
            }

            $msg   = $this->lang->line('success_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["first_doc"]) && !empty($_FILES['first_doc']['name'])) {

            $file_type = $_FILES["first_doc"]['type'];
            $file_size = $_FILES["first_doc"]["size"];
            $file_name = $_FILES["first_doc"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $_FILES['first_doc']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mtype, $allowed_mime_type)) {
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

            return true;
        } else {
            $this->form_validation->set_message('handle_upload', $this->lang->line('the_file_field_is_required'));
            return false;
        }
        return true;
    }

    public function edit()
    {
        $data['title']              = 'Edit Student';
        $id                         = $this->customlib->getStudentSessionUserID();
        $data['id']                 = $id;
        $student                    = $this->student_model->get($id);
        $genderList                 = $this->customlib->getGender();
        $data['student']            = $student;
        $data['genderList']         = $genderList;
        $session                    = $this->setting_model->getCurrentSession();
        $vehroute_result            = $this->vehroute_model->get();
        $data['vehroutelist']       = $vehroute_result;
        $category                   = $this->category_model->get();
        $data['categorylist']       = $category;
        $data["bloodgroup"]         = $this->config->item('bloodgroup');
        $data['inserted_fields']    = $this->student_edit_field_model->get();
        $data['sch_setting_detail'] = $this->sch_setting_detail;

        if ($this->findSelected($data['inserted_fields'], 'firstname')) {
            $this->form_validation->set_rules('firstname', $this->lang->line('first_name'), 'trim|required|xss_clean');
        }
        if ($this->findSelected($data['inserted_fields'], 'guardian_is')) {
            $this->form_validation->set_rules('guardian_is', $this->lang->line('guardian'), 'trim|required|xss_clean');
        }
        if ($this->findSelected($data['inserted_fields'], 'dob')) {
            $this->form_validation->set_rules('dob', $this->lang->line('date_of_birth'), 'trim|required|xss_clean');
        }
        if ($this->findSelected($data['inserted_fields'], 'gender')) {
            $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        }
        if ($this->findSelected($data['inserted_fields'], 'guardian_name')) {
            $this->form_validation->set_rules('guardian_name', $this->lang->line('guardian_name'), 'trim|required|xss_clean');
        }
        if ($this->findSelected($data['inserted_fields'], 'guardian_phone')) {
            $this->form_validation->set_rules('guardian_phone', $this->lang->line('guardian_phone'), 'trim|required|xss_clean');
        }

        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_edit_handle_upload[file]');
        $this->form_validation->set_rules('father_pic', $this->lang->line('image'), 'callback_edit_handle_upload[father_pic]');
        $this->form_validation->set_rules('mother_pic', $this->lang->line('image'), 'callback_edit_handle_upload[mother_pic]');
        $this->form_validation->set_rules('guardian_pic', $this->lang->line('image'), 'callback_edit_handle_upload[guardian_pic]');

        $custom_fields              = $this->customfield_model->getByBelong('students');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[students][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/student/header', $data);
            $this->load->view('user/edit', $data);
            $this->load->view('layout/student/footer', $data);
        } else {

            $student_id = $id;
            $data       = array(
                'id' => $id,
            );

            $firstname = $this->input->post('firstname');
            if (isset($firstname)) {
                $data['firstname'] = $this->input->post('firstname');
            }
            $rte = $this->input->post('rte');
            if (isset($rte)) {
                $data['rte'] = $this->input->post('rte');
            }
            $pincode = $this->input->post('pincode');
            if (isset($pincode)) {
                $data['pincode'] = $this->input->post('pincode');
            }
            $cast = $this->input->post('cast');
            if (isset($cast)) {
                $data['cast'] = $this->input->post('cast');
            }
            $guardian_is = $this->input->post('guardian_is');
            if (isset($guardian_is)) {
                $data['guardian_is'] = $this->input->post('guardian_is');
            }
            $previous_school = $this->input->post('previous_school');
            if (isset($previous_school)) {
                $data['previous_school'] = $this->input->post('previous_school');
            }
            $dob = $this->input->post('dob');
            if (isset($dob)) {
                $data['dob'] = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('dob')));
            }
            $current_address = $this->input->post('current_address');
            if (isset($current_address)) {
                $data['current_address'] = $this->input->post('current_address');
            }
            $permanent_address = $this->input->post('permanent_address');
            if (isset($permanent_address)) {
                $data['permanent_address'] = $this->input->post('permanent_address');
            }
            $bank_account_no = $this->input->post('bank_account_no');
            if (isset($bank_account_no)) {
                $data['bank_account_no'] = $this->input->post('bank_account_no');
            }
            $bank_name = $this->input->post('bank_name');
            if (isset($bank_name)) {
                $data['bank_name'] = $this->input->post('bank_name');
            }
            $ifsc_code = $this->input->post('ifsc_code');
            if (isset($ifsc_code)) {
                $data['ifsc_code'] = $this->input->post('ifsc_code');
            }
            $guardian_occupation = $this->input->post('guardian_occupation');
            if (isset($guardian_occupation)) {
                $data['guardian_occupation'] = $this->input->post('guardian_occupation');
            }
            $guardian_email = $this->input->post('guardian_email');
            if (isset($guardian_email)) {
                $data['guardian_email'] = $this->input->post('guardian_email');
            }
            $gender = $this->input->post('gender');
            if (isset($gender)) {
                $data['gender'] = $this->input->post('gender');
            }
            $guardian_name = $this->input->post('guardian_name');
            if (isset($guardian_name)) {
                $data['guardian_name'] = $this->input->post('guardian_name');
            }
            $guardian_relation = $this->input->post('guardian_relation');
            if (isset($guardian_relation)) {
                $data['guardian_relation'] = $this->input->post('guardian_relation');
            }
            $guardian_phone = $this->input->post('guardian_phone');
            if (isset($guardian_phone)) {
                $data['guardian_phone'] = $this->input->post('guardian_phone');
            }
            $guardian_address = $this->input->post('guardian_address');
            if (isset($guardian_address)) {
                $data['guardian_address'] = $this->input->post('guardian_address');
            }
            $adhar_no = $this->input->post('adhar_no');
            if (isset($adhar_no)) {
                $data['adhar_no'] = $this->input->post('adhar_no');
            }
            $samagra_id = $this->input->post('samagra_id');
            if (isset($samagra_id)) {
                $data['samagra_id'] = $this->input->post('samagra_id');
            }

            $house             = $this->input->post('house');
            $blood_group       = $this->input->post('blood_group');
            $measurement_date  = $this->input->post('measure_date');
            $roll_no           = $this->input->post('roll_no');
            $middlename        = $this->input->post('middlename');
            $lastname          = $this->input->post('lastname');
            $category_id       = $this->input->post('category_id');
            $religion          = $this->input->post('religion');
            $mobileno          = $this->input->post('mobileno');
            $email             = $this->input->post('email');
            $admission_date    = $this->input->post('admission_date');
            $height            = $this->input->post('height');
            $weight            = $this->input->post('weight');
            $father_name       = $this->input->post('father_name');
            $father_phone      = $this->input->post('father_phone');
            $father_occupation = $this->input->post('father_occupation');
            $mother_name       = $this->input->post('mother_name');
            $mother_phone      = $this->input->post('mother_phone');
            $mother_occupation = $this->input->post('mother_occupation');

            if (isset($measurement_date)) {
                $data['measurement_date'] = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('measure_date')));
            }

            if (isset($house)) {
                $data['school_house_id'] = $this->input->post('house');
            }
            if (isset($blood_group)) {

                $data['blood_group'] = $this->input->post('blood_group');
            }

            if (isset($middlename)) {

                $data['middlename'] = $this->input->post('middlename');
            }

            if (isset($lastname)) {

                $data['lastname'] = $this->input->post('lastname');
            }

            if (isset($category_id)) {

                $data['category_id'] = $this->input->post('category_id');
            }

            if (isset($religion)) {

                $data['religion'] = $this->input->post('religion');
            }

            if (isset($mobileno)) {

                $data['mobileno'] = $this->input->post('mobileno');
            }

            if (isset($email)) {

                $data['email'] = $this->input->post('email');
            }

            if (isset($admission_date)) {

                $data['admission_date'] = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('admission_date')));
            }

            if (isset($height)) {

                $data['height'] = $this->input->post('height');
            }

            if (isset($weight)) {

                $data['weight'] = $this->input->post('weight');
            }

            if (isset($father_name)) {

                $data['father_name'] = $this->input->post('father_name');
            }

            if (isset($father_phone)) {

                $data['father_phone'] = $this->input->post('father_phone');
            }

            if (isset($father_occupation)) {

                $data['father_occupation'] = $this->input->post('father_occupation');
            }

            if (isset($mother_name)) {

                $data['mother_name'] = $this->input->post('mother_name');
            }

            if (isset($mother_phone)) {

                $data['mother_phone'] = $this->input->post('mother_phone');
            }

            if (isset($mother_occupation)) {

                $data['mother_occupation'] = $this->input->post('mother_occupation');
            }

            $custom_field_post = $this->input->post("custom_fields[students]");
            if (isset($custom_field_post)) {
                $custom_value_array = array();
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[students][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'students');
            }

            $this->student_model->add($data);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/student_images/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/student_images/' . $img_name);
                $this->student_model->add($data_img);
            }

            if (isset($_FILES["father_pic"]) && !empty($_FILES['father_pic']['name'])) {
                $fileInfo = pathinfo($_FILES["father_pic"]["name"]);
                $img_name = $id . "father" . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["father_pic"]["tmp_name"], "./uploads/student_images/" . $img_name);
                $data_img = array('id' => $id, 'father_pic' => 'uploads/student_images/' . $img_name);
                $this->student_model->add($data_img);
            }

            if (isset($_FILES["mother_pic"]) && !empty($_FILES['mother_pic']['name'])) {
                $fileInfo = pathinfo($_FILES["mother_pic"]["name"]);
                $img_name = $id . "mother" . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["mother_pic"]["tmp_name"], "./uploads/student_images/" . $img_name);
                $data_img = array('id' => $id, 'mother_pic' => 'uploads/student_images/' . $img_name);
                $this->student_model->add($data_img);
            }

            if (isset($_FILES["guardian_pic"]) && !empty($_FILES['guardian_pic']['name'])) {
                $fileInfo = pathinfo($_FILES["guardian_pic"]["name"]);
                $img_name = $id . "guardian" . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["guardian_pic"]["tmp_name"], "./uploads/student_images/" . $img_name);
                $data_img = array('id' => $id, 'guardian_pic' => 'uploads/student_images/' . $img_name);
                $this->student_model->add($data_img);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('user/user/edit');
        }
    }

    public function findSelected($inserted_fields, $find)
    {
        foreach ($inserted_fields as $inserted_key => $inserted_value) {
            if ($find == $inserted_value->name && $inserted_value->status) {
                return true;
            }
        }
        return false;
    }

    public function edit_handle_upload($value, $field_name)
    {
        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES[$field_name]) && !empty($_FILES[$field_name]['name'])) {

            $file_type         = $_FILES[$field_name]['type'];
            $file_size         = $_FILES[$field_name]["size"];
            $file_name         = $_FILES[$field_name]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES[$field_name]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('edit_handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('edit_handle_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('edit_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('edit_handle_upload', $this->lang->line('file_type_extension_error_uploading_image'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function printFeesByName()
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

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Invalid payload'
                ]));
        }

        $fee_category       = $input['fee_category'] ?? '';
        $invoice_id         = $input['main_invoice'] ?? '';
        $sub_invoice_id     = $input['sub_invoice'] ?? '';
        $student_session_id = $input['student_session_id'] ?? '';

        if (!$student_session_id || !$invoice_id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Required fields missing'
                ]));
        }

        $setting_result = $this->setting_model->get();
        $student        = $this->studentsession_model
            ->searchStudentsBySession($student_session_id);

        if ($fee_category == "transport") {

            $fee_record = $this->studentfeemaster_model
                ->getTransportFeeByInvoice($invoice_id, $sub_invoice_id);
        } else {

            $fee_record = $this->studentfeemaster_model
                ->getFeeByInvoice($invoice_id, $sub_invoice_id);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data' => [
                    'student' => $student,
                    'fee_category' => $fee_category,
                    'invoice_id' => $invoice_id,
                    'sub_invoice_id' => $sub_invoice_id,
                    'fees' => $fee_record,
                    'settings' => $setting_result
                ]
            ]));
    }
    public function countAttendance($session_year_start, $student_session_id)
    {
        $attendencetypes = $this->attendencetype_model->getAttType();

        $record = array();
        foreach ($attendencetypes as $type_key => $type_value) {
            $record[$type_value['id']] = 0;
        }

        for ($i = 1; $i <= 12; $i++) {
            $start_month = date('Y-m-d', strtotime($session_year_start));

            $end_month = date('Y-m-t', strtotime($session_year_start));

            $session_year_start = date('Y-m-d', strtotime('+1 month', strtotime($session_year_start)));

            $attendences = $this->stuattendence_model->student_attendence_bw_date($start_month, $end_month, $student_session_id);
            if (!empty($attendences)) {
                foreach ($attendences as $attendence_key => $attendence_value) {

                    $record[$attendence_value->attendence_type_id] += 1;
                }
            }
        }

        return $record;
    }

    public function startmonthandend()
    {
        $startmonth = $this->setting_model->getStartMonth();
        if ($startmonth == 1) {
            $endmonth = 12;
        } else {
            $endmonth = $startmonth - 1;
        }
        return array($startmonth, $endmonth);
    }
}

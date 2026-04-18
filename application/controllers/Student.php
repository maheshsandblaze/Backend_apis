<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Student extends Public_Controller
{

    public $sch_setting_detail = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->config->load('app-config');
        $this->config->load("payroll");
        $this->load->library('smsgateway');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->model("classteacher_model");
        $this->load->model("class_model");
        $this->load->model("section_model");
        $this->load->model('behavioural_model');

        $this->load->model(array("timeline_model", "student_edit_field_model", 'transportfee_model', 'marksdivision_model', 'module_model'));
        $this->blood_group        = $this->config->item('bloodgroup');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->role;
        $this->staff_attendance = $this->config->item('staffattendance');
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function index()
    {
        $data['title']       = 'Student List';
        $student_result      = $this->student_model->get();
        $data['studentlist'] = $student_result;
        $this->load->view('layout/header', $data);
        $this->load->view('student/studentList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function multiclass()
    {
        if (!$this->rbac->hasPrivilege('multi_class_student', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/multiclass');
        $data['title']       = 'student fees';
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;

        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
        } else {
            $class                   = $this->class_model->get();
            $data['classlist']       = $class;
            $data['student_due_fee'] = array();
            $class_id                = $this->input->post('class_id');
            $section_id              = $this->input->post('section_id');
            $data['classes']         = $this->classsection_model->allClassSections();
            $students                = $this->studentsession_model->searchMultiStudentByClassSection($class_id, $section_id);
            $data['students']        = $students;
        }
        $this->load->view('layout/header', $data);
        $this->load->view('student/multiclass', $data);
        $this->load->view('layout/footer', $data);
    }

    public function download($student_id, $doc_id)
    {
        $this->load->helper('download');
        $doc_details = $this->student_model->studentdocbyid($doc_id);
        $this->media_storage->filedownload($doc_details['doc'], "uploads/student_documents/" . $student_id);
    }

    // public function view($id)
    // {
    //     if (!$this->rbac->hasPrivilege('student', 'can_view')) {
    //         access_denied();
    //     }

    //     $userdata        = $this->customlib->getUserData();
    //     $data['role_id'] = $userdata["role_id"];

    //     $data['marks_division'] = $this->marksdivision_model->get();

    //     $data['title']     = $this->lang->line('student_details');
    //     $student           = $this->student_model->get($id);
    //     $data['gradeList'] = $this->grade_model->get();
    //     $studentSession    = $this->student_model->getStudentSession($id);

    //     $data["timeline_list"] = $this->timeline_model->getStudentTimeline($id, $status = '');

    //     $data['sch_setting'] = $this->sch_setting_detail;

    //     $data['adm_auto_insert']      = $this->sch_setting_detail->adm_auto_insert;
    //     $data['student_timeline']     = $this->sch_setting_detail->student_timeline;
    //     $data["session"]              = $studentSession["session"];
    //     $student_due_fee              = $this->studentfeemaster_model->getStudentFees($student['student_session_id']);
    //     $student_discount_fee         = $this->feediscount_model->getStudentFeesDiscount($student['student_session_id']);
    //     $data['student_discount_fee'] = $student_discount_fee;
    //     $data['student_due_fee']      = $student_due_fee;
    //     $data['siblings']             = $this->student_model->getMySiblings($student['parent_id'], $student['id']);

    //     $data['student_doc'] = $this->student_model->getstudentdoc($id);

    //     $transport_fees = [];

    //     $data['superadmin_visible'] = $this->customlib->superadmin_visible();

    //     $getStaffRole       = $this->customlib->getStaffRole();
    //     $data['staffrole']         = json_decode($getStaffRole);

    //     if ($this->module_lib->hasModule('behaviour_records')) {
    //         //------- Behaviour Report Start--------

    //         $this->load->model("studentincidents_model");

    //         // This is used to get assign incident record of student by student id
    //         $data['assignstudent'] = $this->studentincidents_model->studentbehaviour($id);

    //         // This is used to get total points of student by student id
    //         $total_points            = $this->studentincidents_model->totalpoints($id);
    //         $student['total_points'] = $total_points['totalpoints'];

    //         //------- Behaviour Report End----------
    //     }


    //     // ------------- CBSE Exam Start ---------------------
    //     if ($this->module_lib->hasModule('cbseexam')) {

    //         $this->load->model("cbseexam/cbseexam_exam_model");
    //         $this->load->model("cbseexam/cbseexam_grade_model");
    //         $this->load->model("cbseexam/cbseexam_assessment_model");


    //         $exam_list = $this->cbseexam_exam_model->getStudentExamByStudentSession($student['student_session_id']);

    //         $student_exams = [];
    //         if (!empty($exam_list)) {
    //             foreach ($exam_list as $exam_key => $exam_value) {


    //                 $exam_subjects = $this->cbseexam_exam_model->getexamsubjects($exam_value->cbse_exam_id);
    //                 $exam_value->{"subjects"} = $exam_subjects;
    //                 $exam_value->{"grades"} = $this->cbseexam_grade_model->getGraderangebyGradeID($exam_value->cbse_exam_grade_id);
    //                 $exam_value->{"exam_assessments"} = $this->cbseexam_assessment_model->getWithAssessmentTypeByAssessmentID($exam_value->cbse_exam_assessment_id);

    //                 $exam_value->{"exam_subject_assessments"} = $this->cbseexam_assessment_model->getSubjectAssessmentsByExam($exam_subjects);

    //                 $cbse_exam_result = $this->cbseexam_exam_model->getStudentResultByExamsByExamID($exam_value->cbse_exam_id, [$exam_value->student_session_id]);

    //                 // echo "<pre>";
    //                 // print_r($cbse_exam_result);exit;

    //                 $students = [];
    //                 $student_rank = "";

    //                 if (!empty($cbse_exam_result)) {

    //                     foreach ($cbse_exam_result as $student_key => $student_value) {
    //                         // $student_rank= $student_value->rank;

    //                         if (!empty($students)) {

    //                             $student_rank = $student_value->rank;

    //                             if (!array_key_exists($student_value->subject_id, $students['subjects'])) {

    //                                 $new_subject = [
    //                                     'subject_id' => $student_value->subject_id,
    //                                     'subject_name' => $student_value->subject_name,
    //                                     'subject_code' => $student_value->subject_code,
    //                                     'exam_assessments' => [
    //                                         $student_value->cbse_exam_assessment_type_id => [
    //                                             'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
    //                                             'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
    //                                             'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
    //                                             'maximum_marks' => $student_value->maximum_marks,
    //                                             'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
    //                                             'marks' => $student_value->marks,
    //                                             'note' => $student_value->note,
    //                                             'is_absent' => $student_value->is_absent,
    //                                         ],
    //                                     ],
    //                                 ];

    //                                 $students['subjects'][$student_value->subject_id] = $new_subject;
    //                             } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students['subjects'][$student_value->subject_id]['exam_assessments'])) {

    //                                 $new_assesment = [
    //                                     'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
    //                                     'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
    //                                     'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
    //                                     'maximum_marks' => $student_value->maximum_marks,
    //                                     'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
    //                                     'marks' => $student_value->marks,
    //                                     'note' => $student_value->note,
    //                                     'is_absent' => $student_value->is_absent,
    //                                 ];

    //                                 $students['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
    //                             }
    //                         } else {

    //                             $students['subjects'] = [
    //                                 $student_value->subject_id => [
    //                                     'subject_id' => $student_value->subject_id,
    //                                     'subject_name' => $student_value->subject_name,
    //                                     'subject_code' => $student_value->subject_code,
    //                                     'exam_assessments' => [
    //                                         $student_value->cbse_exam_assessment_type_id => [
    //                                             'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
    //                                             'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
    //                                             'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
    //                                             'maximum_marks' => $student_value->maximum_marks,
    //                                             'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
    //                                             'marks' => $student_value->marks,
    //                                             'note' => $student_value->note,
    //                                             'is_absent' => $student_value->is_absent,

    //                                         ],

    //                                     ],
    //                                 ],

    //                             ];
    //                         }
    //                     }
    //                 }

    //                 $exam_value->{"exam_data"} = $students;
    //                 $exam_value->{"rank"} = $student_rank;
    //             }
    //         }

    //         $data['exams'] = $exam_list;
    //     }

    //     // echo "<pre>";
    //     // print_r($exam_list);exit;
    //     // ------------- CBSE Exam End---------------------

    //     $module = $this->module_model->getPermissionByModulename('transport');
    //     if ($module['is_active']) {

    //         $transport_fees = $this->studentfeemaster_model->getStudentTransportFees($student['student_session_id'], $student['route_pickup_point_id']);
    //     }

    //     $data['transport_fees'] = $transport_fees;

    //     $data['student_doc_id'] = $id;
    //     $data['category_list']  = $this->category_model->get();

    //     $data['student'] = $student;

    //     $data["class_section"] = $this->student_model->getClassSection($student["class_id"]);
    //     $session               = $this->setting_model->getCurrentSession();

    //     $data["studentlistbysection"] = $this->student_model->getStudentClassSection($student["class_id"], $session);

    //     $data['guardian_credential'] = $this->student_model->guardian_credential($student['parent_id']);

    //     $data['reason'] = $this->disable_reason_model->get();

    //     if ($student['is_active'] = 'no') {
    //         $data['reason_data'] = $this->disable_reason_model->get($student['dis_reason']);
    //     }

    //     $data['exam_result'] = $this->examgroupstudent_model->searchStudentExams($student['student_session_id'], true, true);
    //     $data['exam_grade']  = $this->grade_model->getGradeDetails();

    //     $data['yearlist'] = $this->stuattendence_model->attendanceYearCount();

    //     $startmonth        = $this->setting_model->getStartMonth();
    //     $monthlist         = $this->customlib->getMonthDropdown($startmonth);
    //     $data["monthlist"] = $monthlist;

    //     $data['attendencetypeslist'] = $this->attendencetype_model->getAttType();

    //     $year = date("Y");

    //     $input       = $this->setting_model->getCurrentSessionName();
    //     list($a, $b) = explode('-', $input);
    //     $start_year  = $a;
    //     if (strlen($b) == 2) {
    //         $Next_year = substr($a, 0, 2) . $b;
    //     } else {
    //         $Next_year = $b;
    //     }

    //     $start_end_month = $this->startmonthandend();

    //     $session_year_start = date("Y-m-01", strtotime($start_year . '-' . $start_end_month[0] . '-01'));
    //     $session_year_end   = date("Y-m-t", strtotime($Next_year . '-' . $start_end_month[1] . '-01'));

    //     $data["countAttendance"] = $this->countAttendance($session_year_start, $student['student_session_id']);

    //     foreach ($monthlist as $key => $value) {

    //         $datemonth       = date("m", strtotime($key));
    //         $date_each_month = date('Y-' . $datemonth . '-01');
    //         $date_end        = date('t', strtotime($date_each_month));
    //         for ($n = 1; $n <= $date_end; $n++) {
    //             $att_date  = sprintf("%02d", $n);
    //             $att_dates = $start_year . "-" . $datemonth . "-" . $att_date;

    //             if ($datemonth == '02' && $att_date == '29') {
    //                 if (!($start_year % 4 == 0 && ($start_year % 100 != 0 || $start_year % 400 == 0))) {
    //                     continue;
    //                 }
    //             }

    //             $attendence_array[] = $att_date;
    //             $date_array[]       = $att_dates;

    //             $res[$att_dates] = $this->stuattendence_model->studentattendance($att_dates, $student['student_session_id']);
    //         }

    //         $start_year = ($datemonth == 12) ? $Next_year : $start_year;
    //     }

    //     $data["session_year_start"] = $session_year_start;
    //     $data["session_year_end"]   = $session_year_end;
    //     $data["resultlist"]         = $res;
    //     $data['behavioural_note'] = $this->behavioural_model->getstudentbehaviouralnote($student['student_session_id']);


    //     $this->load->view('layout/header', $data);
    //     $this->load->view('student/studentShow', $data);
    //     $this->load->view('layout/footer', $data);
    // }



    public function studentViewApi($id)
    {


        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        /* =========================
           BASIC DATA
        ========================== */
        $userdata = $this->customlib->getUserData();

        $student = $this->student_model->get($id);

        // echo "<Pre>";print_r($student);exit;
        if (empty($student)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Student not found'
                ]));
        }

        $studentSession = $this->student_model->getStudentSession($id);
        $startmonth        = $this->setting_model->getStartMonth();

        $monthlist         = $this->customlib->getMonthDropdown($startmonth);


        $year = date("Y");

        $input       = $this->setting_model->getCurrentSessionName();
        list($a, $b) = explode('-', $input);
        $start_year  = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }

        $start_end_month = $this->startmonthandend();

        $session_year_start = date("Y-m-01", strtotime($start_year . '-' . $start_end_month[0] . '-01'));
        $session_year_end   = date("Y-m-t", strtotime($Next_year . '-' . $start_end_month[1] . '-01'));

        /* =========================
           STUDENT RELATED DATA
        ========================== */
        $data = [
            'student'              => $student,
            'role_id'              => $userdata['role_id'],
            'session'              => $studentSession['session'] ?? null,
            'sch_setting'          => $this->sch_setting_detail,
            'marks_division'       => $this->marksdivision_model->get(),
            'grade_list'           => $this->grade_model->get(),
            'timeline'             => $this->timeline_model->getStudentTimeline($id),
            'siblings'             => $this->student_model->getMySiblings($student['parent_id'], $student['id']),
            'student_documents'    => $this->student_model->getstudentdoc($id),
            'guardian_credential'  => $this->student_model->guardian_credential($student['parent_id']),
            'category_list'        => $this->category_model->get(),
            'class_section'        => $this->student_model->getClassSection($student['class_id']),
            'student_list_section' => $this->student_model->getStudentClassSection(
                $student['class_id'],
                $this->setting_model->getCurrentSession()
            ),
            'superadmin_visible'   => $this->customlib->superadmin_visible()
        ];

        /* =========================
           FEES
        ========================== */
        $data['student_due_fee'] =
            $this->studentfeemaster_model->getStudentFees($student['student_session_id']);

        $data['student_discount_fee'] =
            $this->feediscount_model->getStudentFeesDiscount($student['student_session_id']);

        /* =========================
           TRANSPORT FEES
        ========================== */
        $module = $this->module_model->getPermissionByModulename('transport');
        $data['transport_fees'] = $module['is_active']
            ? $this->studentfeemaster_model->getStudentTransportFees(
                $student['student_session_id'],
                $student['route_pickup_point_id']
            )
            : [];

        /* =========================
           EXAM & ATTENDANCE
        ========================== */
        $data['exam_result'] = $this->examgroupstudent_model
            ->searchStudentExams($student['student_session_id'], true, true);

        $data['exam_grade'] = $this->grade_model->getGradeDetails();
        $data['attendance_years'] = $this->stuattendence_model->attendanceYearCount();
        $data['attendance_types'] = $this->attendencetype_model->getAttType();

        $data["countAttendance"]    = $this->countAttendance($session_year_start, $student['student_session_id']);

        foreach ($monthlist as $key => $value) {

            $datemonth       = date("m", strtotime($key));
            $date_each_month = date('Y-' . $datemonth . '-01');
            $date_end        = date('t', strtotime($date_each_month));
            for ($n = 1; $n <= $date_end; $n++) {
                $att_date  = sprintf("%02d", $n);
                $att_dates = $start_year . "-" . $datemonth . "-" . $att_date;

                if ($datemonth == '02' && $att_date == '29') {
                    if (!($start_year % 4 == 0 && ($start_year % 100 != 0 || $start_year % 400 == 0))) {
                        continue;
                    }
                }

                $attendence_array[] = $att_date;
                $date_array[]       = $att_dates;

                $res[$att_dates] = $this->stuattendence_model->studentattendance($att_dates, $student['student_session_id']);
            }

            $start_year = ($datemonth == 12) ? $Next_year : $start_year;
        }

        $data["monthlist"]          = $monthlist;

        $data["date_wise_attendance_data"]    = $res;

        /* =========================
           BEHAVIOUR MODULE
        ========================== */
        // if ($this->module_lib->hasModule('behaviour_records')) {
            // $this->load->model('studentincidents_model');

            // $data['behaviour'] = [
            //     'records'      => $this->studentincidents_model->studentbehaviour($student['id']),
            //     'total_points' => $this->studentincidents_model->totalpoints($student['id'])['totalpoints'] ?? 0
            // ];
        // }

        /* =========================
           CBSE MODULE
        ========================== */
        // if ($this->module_lib->hasModule('cbseexam')) {
            $this->load->model("cbseexam/cbseexam_exam_model");

            $data['cbse_exams'] =
                $this->cbseexam_exam_model
                ->getStudentExamByStudentSession($student['student_session_id']);
        // }

        /* =========================
           RESPONSE
        ========================== */
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }


    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_student_sample_file.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_student_sample_file.csv';

        force_download($name, $data);
    }



    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('student', 'can_delete')) {
            access_denied();
        }
        $this->student_model->remove($id);
        $this->session->set_flashdata('msg', '<i class="fa fa-check-square-o" aria-hidden="true"></i> ' . $this->lang->line('delete_message') . '');
        redirect('student/search');
    }

    public function doc_delete($id, $student_id)
    {
        $this->student_model->doc_delete($id);
        $this->session->set_flashdata('msg', $this->lang->line('delete_message'));
        redirect('student/view/' . $student_id);
    }

    // public function create()
    // {
    //     if (!$this->rbac->hasPrivilege('student', 'can_add')) {
    //         access_denied();
    //     }

    //     $data["month"] = $this->customlib->getMonthDropdown();
    //     $this->session->set_userdata('top_menu', 'Student Information');
    //     $this->session->set_userdata('sub_menu', 'student/create');
    //     $genderList                    = $this->customlib->getGender();
    //     $data['genderList']            = $genderList;
    //     $data['sch_setting']           = $this->sch_setting_detail;
    //     $data['title']                 = 'Add Student';
    //     $data['title_list']            = 'Recently Added Student';
    //     $data['adm_auto_insert']       = $this->sch_setting_detail->adm_auto_insert;
    //     $data["student_categorize"]    = 'class';
    //     $session                       = $this->setting_model->getCurrentSession();
    //     $data['feesessiongroup_model'] = $this->feesessiongroup_model->getFeesByGroup();
    //     $data['transport_fees']        = $this->transportfee_model->getSessionFees($session);
    //     $student_result                = $this->student_model->getRecentRecord();
    //     $data['studentlist']           = $student_result;
    //     $class                         = $this->class_model->get('', $classteacher = 'yes');

    //     $data['classlist']    = $class;
    //     $userdata             = $this->customlib->getUserData();
    //     $category             = $this->category_model->get();
    //     $data['categorylist'] = $category;
    //     $houses               = $this->student_model->gethouselist();
    //     $data['houses']       = $houses;
    //     $data["bloodgroup"]   = $this->blood_group;
    //     $hostelList           = $this->hostel_model->get();
    //     $data['hostelList']   = $hostelList;
    //     $vehroute_result      = $this->vehroute_model->getRouteVehiclesList();

    //     $data['vehroutelist'] = $vehroute_result;
    //     $custom_fields        = $this->customfield_model->getByBelong('students');

    //     foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
    //         if ($custom_fields_value['validation']) {
    //             $custom_fields_id   = $custom_fields_value['id'];
    //             $custom_fields_name = $custom_fields_value['name'];
    //             $this->form_validation->set_rules("custom_fields[students][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
    //         }
    //     }

    //     $this->form_validation->set_rules('first_doc', $this->lang->line('image'), 'callback_handle_uploadfordoc[first_doc]');
    //     $this->form_validation->set_rules('second_doc', $this->lang->line('image'), 'callback_handle_uploadfordoc[second_doc]');
    //     $this->form_validation->set_rules('fourth_doc', $this->lang->line('image'), 'callback_handle_uploadfordoc[fourth_doc]');
    //     $this->form_validation->set_rules('fifth_doc', $this->lang->line('image'), 'callback_handle_uploadfordoc[fifth_doc]');
    //     $this->form_validation->set_rules('firstname', $this->lang->line('first_name'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('dob', $this->lang->line('date_of_birth'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('child_id', 'Child ID', 'trim|required|xss_clean');

    //     if ($this->sch_setting_detail->guardian_name) {
    //         $this->form_validation->set_rules('guardian_name', $this->lang->line('guardian_name'), 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('guardian_is', $this->lang->line('guardian'), 'trim|required|xss_clean');
    //     }

    //     if ($this->sch_setting_detail->guardian_phone) {
    //         $this->form_validation->set_rules('guardian_phone', $this->lang->line('guardian_phone'), 'trim|required|xss_clean');
    //     };
    //     $this->form_validation->set_rules(
    //         'email',
    //         $this->lang->line('email'),
    //         array(
    //             'valid_email',
    //             array('check_student_email_exists', array($this->student_model, 'check_student_email_exists')),
    //         )
    //     );


    //     $this->form_validation->set_rules(
    //         'mobileno',
    //         $this->lang->line('mobileno'),
    //         array(
    //             'xss_clean',
    //             array('check_student_mobile_exists', array($this->student_model, 'check_student_mobile_no_exists')),
    //         )
    //     );

    //     $sibling_id         = $this->input->post('sibling_id');
    //     if ($sibling_id > 0) {
    //     } else {
    //         $this->form_validation->set_rules(
    //             'guardian_email',
    //             $this->lang->line('guardian_email'),
    //             array(
    //                 'valid_email',
    //                 array('check_guardian_email_exists', array($this->student_model, 'check_guardian_email_exists')),
    //             )
    //         );
    //     }

    //     if (!$this->sch_setting_detail->adm_auto_insert) {
    //         $this->form_validation->set_rules('admission_no', $this->lang->line('admission_no'), 'trim|required|xss_clean|is_unique[students.admission_no]');
    //     }

    //     $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload[file]');

    //     $transport_feemaster_id = $this->input->post('transport_feemaster_id');
    //     if (!empty($transport_feemaster_id)) {
    //         $this->form_validation->set_rules('vehroute_id', $this->lang->line('route_list'), 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('route_pickup_point_id', $this->lang->line('pickup_point'), 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('transport_feemaster_id[]', $this->lang->line('fees_month'), 'trim|required|xss_clean');
    //     }

    //     if ($this->form_validation->run() == false) {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('student/studentCreate', $data);
    //         $this->load->view('layout/footer', $data);
    //     } else {

    //         $custom_field_post  = $this->input->post("custom_fields[students]");
    //         $custom_value_array = array();
    //         if (!empty($custom_field_post)) {

    //             foreach ($custom_field_post as $key => $value) {
    //                 $check_field_type = $this->input->post("custom_fields[students][" . $key . "]");
    //                 $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
    //                 $array_custom     = array(
    //                     'belong_table_id' => 0,
    //                     'custom_field_id' => $key,
    //                     'field_value'     => $field_value,
    //                 );
    //                 $custom_value_array[] = $array_custom;
    //             }
    //         }

    //         $class_id              = $this->input->post('class_id');
    //         $section_id            = $this->input->post('section_id');
    //         $fees_discount         = $this->input->post('fees_discount');
    //         $route_pickup_point_id = $this->input->post('route_pickup_point_id');
    //         $vehroute_id           = $this->input->post('vehroute_id');
    //         if (empty($vehroute_id)) {
    //             $vehroute_id = null;
    //         }
    //         $hostel_room_id = $this->input->post('hostel_room_id');

    //         if (empty($route_pickup_point_id)) {
    //             $route_pickup_point_id = null;
    //         }

    //         if (empty($hostel_room_id)) {
    //             $hostel_room_id = 0;
    //         }

    //         $data_insert = array(
    //             'firstname'         => $this->input->post('firstname'),
    //             'rte'               => $this->input->post('rte'),
    //             'state'             => $this->input->post('state'),
    //             'city'              => $this->input->post('city'),
    //             'pincode'           => $this->input->post('pincode'),
    //             'cast'              => $this->input->post('cast'),
    //             'previous_school'   => $this->input->post('previous_school'),
    //             'dob'               => $this->customlib->dateFormatToYYYYMMDD($this->input->post('dob')),
    //             'current_address'   => $this->input->post('current_address'),
    //             'permanent_address' => $this->input->post('permanent_address'),
    //             'adhar_no'          => $this->input->post('adhar_no'),
    //             'samagra_id'        => $this->input->post('samagra_id'),
    //             'bank_account_no'   => $this->input->post('bank_account_no'),
    //             'bank_name'         => $this->input->post('bank_name'),
    //             'ifsc_code'         => $this->input->post('ifsc_code'),
    //             'guardian_email'    => $this->input->post('guardian_email'),
    //             'gender'            => $this->input->post('gender'),
    //             'guardian_name'     => $this->input->post('guardian_name'),
    //             'guardian_relation' => $this->input->post('guardian_relation'),
    //             'guardian_phone'    => $this->input->post('guardian_phone'),
    //             'guardian_address'  => $this->input->post('guardian_address'),
    //             'hostel_room_id'    => $hostel_room_id,
    //             'note'              => $this->input->post('note'),
    //             'is_active'         => 'yes',
    //             'child_id'          => $this->input->post('child_id'),
    //             'class_of_admission'    => $this->input->post('class_of_admission'),
    //         );

    //         if ($this->sch_setting_detail->guardian_occupation) {
    //             $data_insert['guardian_occupation'] = $this->input->post('guardian_occupation');
    //         }

    //         $house             = $this->input->post('house');
    //         $blood_group       = $this->input->post('blood_group');
    //         $measurement_date  = $this->input->post('measure_date');
    //         $roll_no           = $this->input->post('roll_no');
    //         $lastname          = $this->input->post('lastname');
    //         $middlename        = $this->input->post('middlename');
    //         $category_id       = $this->input->post('category_id');
    //         $religion          = $this->input->post('religion');
    //         $mobileno          = $this->input->post('mobileno');
    //         $email             = $this->input->post('email');
    //         $admission_date    = $this->input->post('admission_date');
    //         $height            = $this->input->post('height');
    //         $weight            = $this->input->post('weight');
    //         $father_name       = $this->input->post('father_name');
    //         $father_phone      = $this->input->post('father_phone');
    //         $father_occupation = $this->input->post('father_occupation');
    //         $mother_name       = $this->input->post('mother_name');
    //         $mother_phone      = $this->input->post('mother_phone');
    //         $mother_occupation = $this->input->post('mother_occupation');

    //         if ($this->sch_setting_detail->guardian_name) {
    //             $data_insert['guardian_is'] = $this->input->post('guardian_is');
    //         }
    //         if (isset($measurement_date)) {
    //             $data_insert['measurement_date'] = $this->customlib->dateFormatToYYYYMMDD($this->input->post('measure_date'));
    //         }
    //         if (isset($house)) {
    //             $data_insert['school_house_id'] = $this->input->post('house');
    //         }
    //         if (isset($blood_group)) {
    //             $data_insert['blood_group'] = $this->input->post('blood_group');
    //         }
    //         if (isset($roll_no)) {
    //             $data_insert['roll_no'] = $this->input->post('roll_no');
    //         }
    //         if (isset($lastname)) {
    //             $data_insert['lastname'] = $this->input->post('lastname');
    //         }
    //         if (isset($middlename)) {
    //             $data_insert['middlename'] = $this->input->post('middlename');
    //         }
    //         if (isset($category_id)) {
    //             $data_insert['category_id'] = $this->input->post('category_id');
    //         }
    //         if (isset($religion)) {
    //             $data_insert['religion'] = $this->input->post('religion');
    //         }
    //         if (isset($mobileno)) {
    //             $data_insert['mobileno'] = $this->input->post('mobileno');
    //         }
    //         if (isset($email)) {
    //             $data_insert['email'] = $this->input->post('email');
    //         }
    //         if (isset($admission_date)) {
    //             $data_insert['admission_date'] = $this->customlib->dateFormatToYYYYMMDD($this->input->post('admission_date'));
    //         }
    //         if (isset($height)) {
    //             $data_insert['height'] = $this->input->post('height');
    //         }
    //         if (isset($weight)) {
    //             $data_insert['weight'] = $this->input->post('weight');
    //         }
    //         if (isset($father_name)) {
    //             $data_insert['father_name'] = $this->input->post('father_name');
    //         }
    //         if (isset($father_phone)) {
    //             $data_insert['father_phone'] = $this->input->post('father_phone');
    //         }
    //         if (isset($father_occupation)) {
    //             $data_insert['father_occupation'] = $this->input->post('father_occupation');
    //         }
    //         if (isset($mother_name)) {
    //             $data_insert['mother_name'] = $this->input->post('mother_name');
    //         }
    //         if (isset($mother_phone)) {
    //             $data_insert['mother_phone'] = $this->input->post('mother_phone');
    //         }
    //         if (isset($mother_occupation)) {
    //             $data_insert['mother_occupation'] = $this->input->post('mother_occupation');
    //         }

    //         $fee_session_group_id = $this->input->post('fee_session_group_id');

    //         $insert                            = true;
    //         $data_setting                      = array();
    //         $data_setting['id']                = $this->sch_setting_detail->id;
    //         $data_setting['adm_auto_insert']   = $this->sch_setting_detail->adm_auto_insert;
    //         $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;
    //         $admission_no                      = 0;

    //         if ($this->sch_setting_detail->adm_auto_insert) {
    //             if ($this->sch_setting_detail->adm_update_status) {

    //                 $admission_no = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;

    //                 $last_student = $this->student_model->lastRecord();
    //                 if (!empty($last_student)) {

    //                     $last_admission_digit = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);

    //                     $admission_no                = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
    //                     $data_insert['admission_no'] = $admission_no;
    //                 } else {
    //                     $admission_no                = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
    //                     $data_insert['admission_no'] = $admission_no;
    //                 }
    //             } else {
    //                 $admission_no                = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
    //                 $data_insert['admission_no'] = $admission_no;
    //             }

    //             $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
    //             if ($admission_no_exists) {
    //                 $insert = false;
    //             }
    //         } else {
    //             $data_insert['admission_no'] = $this->input->post('admission_no');
    //         }

    //         if (empty($_FILES["file"])) {
    //             if ($this->input->post('gender') == 'Female') {
    //                 $data_insert['image'] = 'uploads/student_images/default_female.jpg';
    //             } else {
    //                 $data_insert['image'] = 'uploads/student_images/default_male.jpg';
    //             }
    //         }

    //         if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
    //             $img_name             = $this->media_storage->fileupload("file", "./uploads/student_images/");
    //             $data_insert['image'] = "uploads/student_images/" . $img_name;
    //         }

    //         if (isset($_FILES["father_pic"]) && !empty($_FILES['father_pic']['name'])) {
    //             $img_name                  = $this->media_storage->fileupload("father_pic", "./uploads/student_images/");
    //             $data_insert['father_pic'] = "uploads/student_images/" . $img_name;
    //         }

    //         if (isset($_FILES["mother_pic"]) && !empty($_FILES['mother_pic']['name'])) {
    //             $img_name                  = $this->media_storage->fileupload("mother_pic", "./uploads/student_images/");
    //             $data_insert['mother_pic'] = "uploads/student_images/" . $img_name;
    //         }

    //         if (isset($_FILES["guardian_pic"]) && !empty($_FILES['guardian_pic']['name'])) {
    //             $img_name                    = $this->media_storage->fileupload("guardian_pic", "./uploads/student_images/");
    //             $data_insert['guardian_pic'] = "uploads/student_images/" . $img_name;
    //         }

    //         if ($insert) {
    //             $insert_id = $this->student_model->add($data_insert, $data_setting);
    //             if (!empty($custom_value_array)) {
    //                 $this->customfield_model->insertRecord($custom_value_array, $insert_id);
    //             }

    //             $data_new = array(
    //                 'student_id'            => $insert_id,
    //                 'class_id'              => $class_id,
    //                 'section_id'            => $section_id,
    //                 'session_id'            => $session,
    //                 'fees_discount'         => $fees_discount,
    //                 'route_pickup_point_id' => $route_pickup_point_id,
    //                 'vehroute_id'           => $vehroute_id,
    //             );
    //             $student_session_id     = $this->student_model->add_student_session($data_new);
    //             $transport_feemaster_id = $this->input->post('transport_feemaster_id');

    //             if ($fee_session_group_id) {
    //                 $this->studentfeemaster_model->assign_bulk_fees($fee_session_group_id, $student_session_id, array());
    //             }

    //             if (!empty($transport_feemaster_id)) {
    //                 $trns_data_insert = array();
    //                 foreach ($transport_feemaster_id as $transport_feemaster_key => $transport_feemaster_value) {
    //                     $trns_data_insert[] = array(
    //                         'student_session_id'     => $student_session_id,
    //                         'route_pickup_point_id'  => $route_pickup_point_id,
    //                         'transport_feemaster_id' => $transport_feemaster_value,
    //                     );
    //                 }

    //                 $student_session_is = $this->studenttransportfee_model->add($trns_data_insert, $student_session_id, array(), $route_pickup_point_id);
    //             }

    //             $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

    //             $sibling_id         = $this->input->post('sibling_id');
    //             $data_student_login = array(
    //                 'username' => $this->student_login_prefix . $insert_id,
    //                 'password' => $user_password,
    //                 'user_id'  => $insert_id,
    //                 'role'     => 'student',
    //                 'lang_id'  => $this->sch_setting_detail->lang_id,
    //             );

    //             $this->user_model->add($data_student_login);

    //             if ($sibling_id > 0) {
    //                 $student_sibling = $this->student_model->get($sibling_id);
    //                 $update_student  = array(
    //                     'id'        => $insert_id,
    //                     'parent_id' => $student_sibling['parent_id'],
    //                 );
    //                 $student_sibling = $this->student_model->add($update_student);
    //             } else {
    //                 $parent_password   = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
    //                 $temp              = $insert_id;
    //                 $data_parent_login = array(
    //                     'username' => $this->parent_login_prefix . $insert_id,
    //                     'password' => $parent_password,
    //                     'user_id'  => 0,
    //                     'role'     => 'parent',
    //                     'childs'   => $temp,
    //                 );
    //                 $ins_parent_id  = $this->user_model->add($data_parent_login);
    //                 $update_student = array(
    //                     'id'        => $insert_id,
    //                     'parent_id' => $ins_parent_id,
    //                 );
    //                 $this->student_model->add($update_student);
    //             }

    //             $upload_dir_path  = $this->customlib->getFolderPath() . './uploads/student_documents/' . $insert_id . '/';
    //             $upload_directory = './uploads/student_documents/' . $insert_id . '/';
    //             if (!is_dir($upload_dir_path) && !mkdir($upload_dir_path)) {
    //                 die("Error creating folder $upload_dir_path");
    //             }

    //             if (isset($_FILES["first_doc"]) && !empty($_FILES['first_doc']['name'])) {

    //                 $first_title = $this->input->post('first_title');
    //                 $imp         = $this->media_storage->fileupload("first_doc", $upload_directory);
    //                 $data_img    = array('student_id' => $insert_id, 'title' => $first_title, 'doc' => $imp);
    //                 $this->student_model->adddoc($data_img);
    //             }

    //             if (isset($_FILES["second_doc"]) && !empty($_FILES['second_doc']['name'])) {
    //                 $second_title = $this->input->post('second_title');
    //                 $imp          = $this->media_storage->fileupload("second_doc", $upload_directory);
    //                 $data_img     = array('student_id' => $insert_id, 'title' => $second_title, 'doc' => $imp);
    //                 $this->student_model->adddoc($data_img);
    //             }

    //             if (isset($_FILES["fourth_doc"]) && !empty($_FILES['fourth_doc']['name'])) {
    //                 $fourth_title = $this->input->post('fourth_title');
    //                 $imp          = $this->media_storage->fileupload("fourth_doc", $upload_directory);
    //                 $data_img     = array('student_id' => $insert_id, 'title' => $fourth_title, 'doc' => $imp);
    //                 $this->student_model->adddoc($data_img);
    //             }

    //             if (isset($_FILES["fifth_doc"]) && !empty($_FILES['fifth_doc']['name'])) {
    //                 $fifth_title = $this->input->post('fifth_title');
    //                 $imp         = $this->media_storage->fileupload("fifth_doc", $upload_directory);
    //                 $data_img    = array('student_id' => $insert_id, 'title' => $fifth_title, 'doc' => $imp);
    //                 $this->student_model->adddoc($data_img);
    //             }

    //             $sender_details = array('student_id' => $insert_id, 'student_phone' => $this->input->post('mobileno'), 'guardian_phone' => $this->input->post('guardian_phone'), 'student_email' => $this->input->post('email'), 'guardian_email' => $this->input->post('guardian_email'), 'student_session_id' => $student_session_id);

    //             $this->mailsmsconf->mailsms('student_admission', $sender_details);

    //             $student_login_detail = array('id' => $insert_id, 'credential_for' => 'student', 'first_name' => $this->input->post('firstname'), 'last_name' => $this->input->post('lastname'), 'username' => $this->student_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $this->input->post('mobileno'), 'email' => $this->input->post('email'), 'admission_no' => $data_insert['admission_no'], 'student_session_id' => $student_session_id);

    //             $this->mailsmsconf->mailsms('student_login_credential', $student_login_detail);

    //             if ($sibling_id > 0) {
    //             } else {
    //                 $parent_login_detail = array('id' => $insert_id, 'credential_for' => 'parent', 'username' => $this->parent_login_prefix . $insert_id, 'password' => $parent_password, 'contact_no' => $this->input->post('guardian_phone'), 'email' => $this->input->post('guardian_email'), 'admission_no' => $data_insert['admission_no'], 'student_session_id' => $student_session_id);
    //                 $this->mailsmsconf->mailsms('student_login_credential', $parent_login_detail);
    //             }

    //             $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
    //             redirect('student/create');
    //         } else {

    //             $data['error_message'] = $this->lang->line('admission_no') . ' ' . $admission_no . ' ' . $this->lang->line('already_exists');
    //             $this->load->view('layout/header', $data);
    //             $this->load->view('student/studentCreate', $data);
    //             $this->load->view('layout/footer', $data);
    //         }
    //     }
    // }


    public function create()
    {
        // Handle CORS preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Only GET method is allowed'
                ]));
        }

        // Current academic session
        $session = $this->setting_model->getCurrentSession();

        $category             = $this->category_model->get();

        // Prepare response data
        $data = [
            'title'                  => 'Add Student',
            'title_list'             => 'Recently Added Student',
            'classlist'              => $this->class_model->get('', 'yes'),
            'month'                  => $this->customlib->getMonthDropdown(),
            'genderList'             => $this->customlib->getGender(),
            'sch_setting'            => $this->sch_setting_detail,
            'adm_auto_insert'        => $this->sch_setting_detail->adm_auto_insert,
            'student_categorize'     => 'class',
            'feesessiongroup_model'  => $this->feesessiongroup_model->getFeesByGroup(),
            'transport_fees'         => $this->transportfee_model->getSessionFees($session),
            'studentlist'            => $this->student_model->getRecentRecord(),
            'categorylist'   => $category,
            'houseList'      => $this->student_model->gethouselist(),
            'bloodgroupList' => $this->blood_group,
            'hostelList'     => $this->hostel_model->get(),
            'vehroutelist'   => $this->vehroute_model->getRouteVehiclesList(),
        ];




        // Return API response
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }


    public function createStudent_Api()
    {
        /* =======================
           ALLOW ONLY POST
        ======================== */

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
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

        /* =======================
           READ JSON INPUT
        ======================== */
        $input = json_decode(file_get_contents('php://input'), true);

        // Fallback for form-data
        if (empty($input)) {
            $input = $this->input->post();
        }

        $_POST = $input; // for form_validation

        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // die;



        /* =======================
           VALIDATION RULES
        ======================== */
        $this->form_validation->set_rules('firstname', $this->lang->line('first_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', $this->lang->line('date_of_birth'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('child_id', 'Child ID', 'trim|required|xss_clean');

        // School setting validations
        if ($this->sch_setting_detail->guardian_name) {
            $this->form_validation->set_rules('guardian_name', $this->lang->line('guardian_name'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('guardian_is', $this->lang->line('guardian'), 'trim|required|xss_clean');
        }

        if ($this->sch_setting_detail->guardian_phone) {
            $this->form_validation->set_rules('guardian_phone', $this->lang->line('guardian_phone'), 'trim|required|xss_clean');
        }

        // Email validation
        $this->form_validation->set_rules(
            'email',
            $this->lang->line('email'),
            array(
                'valid_email',
                array('check_student_email_exists', array($this->student_model, 'check_student_email_exists')),
            )
        );

        // Mobile validation
        $this->form_validation->set_rules(
            'mobileno',
            $this->lang->line('mobileno'),
            array(
                'xss_clean',
                array('check_student_mobile_exists', array($this->student_model, 'check_student_mobile_no_exists')),
            )
        );

        // Guardian email validation (only if no sibling)
        $sibling_id = isset($input['sibling_id']) ? $input['sibling_id'] : 0;
        if ($sibling_id <= 0) {
            $this->form_validation->set_rules(
                'guardian_email',
                $this->lang->line('guardian_email'),
                array(
                    'valid_email',
                    array('check_guardian_email_exists', array($this->student_model, 'check_guardian_email_exists')),
                )
            );
        }

        // Admission number validation
        if (!$this->sch_setting_detail->adm_auto_insert) {
            $this->form_validation->set_rules('admission_no', $this->lang->line('admission_no'), 'trim|required|xss_clean|is_unique[students.admission_no]');
        }

        // Transport validation
        $transport_feemaster_id = isset($input['transport_feemaster_id']) ? $input['transport_feemaster_id'] : null;
        if (!empty($transport_feemaster_id)) {
            $this->form_validation->set_rules('vehroute_id', $this->lang->line('route_list'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('route_pickup_point_id', $this->lang->line('pickup_point'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('transport_feemaster_id[]', $this->lang->line('fees_month'), 'trim|required|xss_clean');
        }

        /* =======================
           RUN VALIDATION
        ======================== */
        if ($this->form_validation->run() == false) {

            // echo "<pre>";print_r($this->form_validation->error_array());echo "</pre>";die;    
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        try {
            /* =======================
               PROCESS CUSTOM FIELDS
            ======================== */
            $custom_value_array = array();
            if (isset($input['custom_fields']['students']) && !empty($input['custom_fields']['students'])) {
                foreach ($input['custom_fields']['students'] as $key => $value) {
                    $field_value = is_array($value) ? implode(",", $value) : $value;
                    $custom_value_array[] = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                }
            }

            /* =======================
               PREPARE STUDENT DATA
            ======================== */
            $class_id = $input['class_id'];
            $section_id = $input['section_id'];
            $session = $this->setting_model->getCurrentSession();
            $fees_discount = $input['fees_discount'];
            $route_pickup_point_id = $input['route_pickup_point_id'];
            $vehroute_id           = $input['vehroute_id'];
            if (empty($vehroute_id)) {
                $vehroute_id = null;
            }
            $hostel_room_id = $input['hostel_room_id'];

            if (empty($route_pickup_point_id)) {
                $route_pickup_point_id = null;
            }

            if (empty($hostel_room_id)) {
                $hostel_room_id = 0;
            }

            $data_insert = array(
                'firstname'         => $input['firstname'],
                'rte'               => $input['rte'] ?? null,
                'state'             => $input['state'] ?? null,
                'city'              => $input['city'] ?? null,
                'pincode'           => $input['pincode'] ?? null,
                'cast'              => $input['cast'] ?? null,
                'previous_school'   => $input['previous_school'] ?? null,
                'dob'               => $this->customlib->dateFormatToYYYYMMDD($input['dob']),
                'current_address'   => $input['current_address'] ?? null,
                'permanent_address' => $input['permanent_address'] ?? null,
                'adhar_no'          => $input['adhar_no'] ?? null,
                'samagra_id'        => $input['samagra_id'] ?? null,
                'bank_account_no'   => $input['bank_account_no'] ?? null,
                'bank_name'         => $input['bank_name'] ?? null,
                'ifsc_code'         => $input['ifsc_code'] ?? null,
                'guardian_email'    => $input['guardian_email'] ?? null,
                'gender'            => $input['gender'],
                'guardian_name'     => $input['guardian_name'] ?? null,
                'guardian_relation' => $input['guardian_relation'] ?? null,
                'guardian_phone'    => $input['guardian_phone'] ?? null,
                'guardian_address'  => $input['guardian_address'] ?? null,
                'note'              => $input['note'] ?? null,
                'is_active'         => 'yes',
                'child_id'          => $input['child_id'],
                'class_of_admission' => $input['class_of_admission'] ?? null,
                'hostel_room_id'    => $hostel_room_id,

            );

            // Optional fields with null coalescing
            $optional_fields = [
                'lastname',
                'middlename',
                'category_id',
                'religion',
                'mobileno',
                'email',
                'admission_date',
                'height',
                'weight',
                'father_name',
                'father_phone',
                'father_occupation',
                'mother_name',
                'mother_phone',
                'mother_occupation',
                'roll_no',
                'house',
                'blood_group',
                'measurement_date'
            ];

            foreach ($optional_fields as $field) {
                if (isset($input[$field]) && !empty($input[$field])) {
                    if ($field === 'admission_date' || $field === 'measurement_date') {
                        $data_insert[$field] = $this->customlib->dateFormatToYYYYMMDD($input[$field]);
                    } elseif ($field === 'house') {
                        $data_insert['school_house_id'] = $input[$field];
                    } elseif ($field === 'blood_group') {
                        $data_insert['blood_group'] = $input[$field];
                    } else {
                        $data_insert[$field] = $input[$field];
                    }
                }
            }

            if ($this->sch_setting_detail->guardian_occupation && isset($input['guardian_occupation'])) {
                $data_insert['guardian_occupation'] = $input['guardian_occupation'];
            }

            if ($this->sch_setting_detail->guardian_name && isset($input['guardian_is'])) {
                $data_insert['guardian_is'] = $input['guardian_is'];
            }

            /* =======================
               HANDLE ADMISSION NUMBER
            ======================== */
            $admission_no = null;
            $insert = true;

            if ($this->sch_setting_detail->adm_auto_insert) {
                if ($this->sch_setting_detail->adm_update_status) {
                    $admission_no = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
                    $last_student = $this->student_model->lastRecord();

                    if (!empty($last_student)) {
                        $last_admission_digit = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);
                        $admission_no = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
                    }
                } else {
                    $admission_no = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
                }

                // Check if admission number exists
                $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
                if ($admission_no_exists) {
                    $insert = false;
                } else {
                    $data_insert['admission_no'] = $admission_no;
                }
            } else {
                $data_insert['admission_no'] = $input['admission_no'];
            }

            if (!$insert) {
                return $this->output
                    ->set_status_header(409)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'  => false,
                        'message' => 'Admission number ' . $admission_no . ' already exists'
                    ]));
            }

            /* =======================
               HANDLE FILE UPLOADS (Optional for API)
            ======================== */
            // Set default image based on gender
            // if ($input['gender'] == 'Female') {
            //     $data_insert['image'] = 'uploads/student_images/default_female.jpg';
            // } else {
            //     $data_insert['image'] = 'uploads/student_images/default_male.jpg';
            // }


            // student image upload (if provided) - Note: For API, consider accepting base64 encoded images or multipart/form-data

            // echo "<pre>";
            // print_r($_FILES);exit;

            $img1 = $img2 = $img3 = $img4 = null;

            if (!empty($_FILES['image']['name'])) {
                $img1 = $this->media_storage->fileupload(
                    "image",
                    "../uploads/student_images/"
                );

                // echo $img1;exit;

                $data_insert['image'] = "uploads/student_images/" . $img1;
            } else {

                if ($this->input->post('gender') == 'Female') {
                    $data_insert['image'] = 'uploads/student_images/default_female.jpg';
                } else {
                    $data_insert['image'] = 'uploads/student_images/default_male.jpg';
                }
            }
            /* =======================
            FATHER PIC
            ======================= */
            if (!empty($_FILES['father_pic']['name'])) {

                $img2 = $this->media_storage->fileupload(
                    "father_pic",
                    "../uploads/student_images/"
                );

                if (!$img2) {
                    return $this->output
                        ->set_status_header(400)
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => false,
                            'message' => 'Father photo upload failed'
                        ]));
                }

                $data_insert['father_pic'] = "uploads/student_images/" . $img2;
            }

            /* =======================
                MOTHER PIC
                ======================= */
            if (!empty($_FILES['mother_pic']['name'])) {

                $img3 = $this->media_storage->fileupload(
                    "mother_pic",
                    "../uploads/student_images/"
                );

                if (!$img3) {
                    return $this->output
                        ->set_status_header(400)
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => false,
                            'message' => 'Mother photo upload failed'
                        ]));
                }

                $data_insert['mother_pic'] = "uploads/student_images/" . $img3;
            }

            /* =======================
                GUARDIAN PIC
                ======================= */
            if (!empty($_FILES['guardian_pic']['name'])) {

                $img4 = $this->media_storage->fileupload(
                    "guardian_pic",
                    "../uploads/student_images/"
                );

                if (!$img4) {
                    return $this->output
                        ->set_status_header(400)
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => false,
                            'message' => 'Guardian photo upload failed'
                        ]));
                }

                $data_insert['guardian_pic'] = "uploads/student_images/" . $img4;
            }







            // echo "<pre>";
            // print_r($data_insert);
            // exit;






            // Note: For API, file uploads would need to be handled differently
            // You might want to accept base64 encoded images or use multipart/form-data

            // echo "<pre>";
            // print_r($data_insert);
            // echo "</pre>";
            // exit;

            /* =======================
               SAVE STUDENT RECORD
            ======================== */
            $insert_id = $this->student_model->add($data_insert, [
                'id' => $this->sch_setting_detail->id,
                'adm_auto_insert' => $this->sch_setting_detail->adm_auto_insert,
                'adm_update_status' => $this->sch_setting_detail->adm_update_status
            ]);


            if (!$insert_id) {
                throw new Exception('Failed to insert student record');
            }


            // student document uploads (if provided) - Note: For API, consider accepting base64 encoded files or multipart/form-data   


            $upload_dir_path  = $this->customlib->getFolderPath() . '../uploads/student_documents/' . $insert_id . '/';
            $upload_directory = '../uploads/student_documents/' . $insert_id . '/';
            if (!is_dir($upload_dir_path) && !mkdir($upload_dir_path)) {
                die("Error creating folder $upload_dir_path");
            }

            if (isset($_FILES["first_doc"]) && !empty($_FILES['first_doc']['name'])) {

                $first_title = $this->input->post('first_title');
                $imp         = $this->media_storage->fileupload("first_doc", $upload_directory);
                $data_img    = array('student_id' => $insert_id, 'title' => $first_title, 'doc' => $imp);
                $this->student_model->adddoc($data_img);
            }

            if (isset($_FILES["second_doc"]) && !empty($_FILES['second_doc']['name'])) {
                $second_title = $this->input->post('second_title');
                $imp          = $this->media_storage->fileupload("second_doc", $upload_directory);
                $data_img     = array('student_id' => $insert_id, 'title' => $second_title, 'doc' => $imp);
                $this->student_model->adddoc($data_img);
            }

            if (isset($_FILES["fourth_doc"]) && !empty($_FILES['fourth_doc']['name'])) {
                $fourth_title = $this->input->post('fourth_title');
                $imp          = $this->media_storage->fileupload("fourth_doc", $upload_directory);
                $data_img     = array('student_id' => $insert_id, 'title' => $fourth_title, 'doc' => $imp);
                $this->student_model->adddoc($data_img);
            }

            if (isset($_FILES["fifth_doc"]) && !empty($_FILES['fifth_doc']['name'])) {
                $fifth_title = $this->input->post('fifth_title');
                $imp         = $this->media_storage->fileupload("fifth_doc", $upload_directory);
                $data_img    = array('student_id' => $insert_id, 'title' => $fifth_title, 'doc' => $imp);
                $this->student_model->adddoc($data_img);
            }


            /* =======================
               SAVE CUSTOM FIELDS
            ======================== */
            if (!empty($custom_value_array)) {
                foreach ($custom_value_array as &$custom) {
                    $custom['belong_table_id'] = $insert_id;
                }
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }

            /* =======================
               CREATE STUDENT SESSION
            ======================== */
            $student_session_data = array(
                'student_id'            => $insert_id,
                'class_id'              => $class_id,
                'section_id'            => $section_id,
                'session_id'            => $session,
                'fees_discount'         => $fees_discount,
                'route_pickup_point_id' => $route_pickup_point_id,
                'vehroute_id'           => $vehroute_id,
            );

            // echo "<pre>";
            // print_r($student_session_data);exit;

            $student_session_id = $this->student_model->add_student_session($student_session_data);

            /* =======================
               HANDLE FEES
            ======================== */
            if (isset($input['fee_session_group_id']) && !empty($input['fee_session_group_id'])) {
                $this->studentfeemaster_model->assign_bulk_fees(
                    $input['fee_session_group_id'],
                    $student_session_id,
                    array()
                );
            }

            /* =======================
               HANDLE TRANSPORT FEES
            ======================== */
            if (!empty($transport_feemaster_id) && is_array($transport_feemaster_id)) {
                $trns_data_insert = array();
                foreach ($transport_feemaster_id as $transport_feemaster_value) {
                    $trns_data_insert[] = array(
                        'student_session_id'     => $student_session_id,
                        'route_pickup_point_id'  => $input['route_pickup_point_id'],
                        'transport_feemaster_id' => $transport_feemaster_value,
                    );
                }
                $this->studenttransportfee_model->add($trns_data_insert, $student_session_id, array(), $input['route_pickup_point_id']);
            }

            /* =======================
               CREATE LOGIN CREDENTIALS
            ======================== */
            $student_password = $this->role->get_random_password(6, 6, false, true, false);

            $student_login_data = array(
                'username' => $this->student_login_prefix . $insert_id,
                'password' => $student_password,
                'user_id'  => $insert_id,
                'role'     => 'student',
                'lang_id'  => $this->sch_setting_detail->lang_id,
            );
            $this->user_model->add($student_login_data);

            /* =======================
               HANDLE SIBLING/PARENT
            ======================== */
            if ($sibling_id > 0) {
                $student_sibling = $this->student_model->get($sibling_id);
                $this->student_model->add(array(
                    'id'        => $insert_id,
                    'parent_id' => $student_sibling['parent_id'],
                ));
            } else {
                $parent_password = $this->role->get_random_password(6, 6, false, true, false);
                $parent_login_data = array(
                    'username' => $this->parent_login_prefix . $insert_id,
                    'password' => $parent_password,
                    'user_id'  => 0,
                    'role'     => 'parent',
                    'childs'   => $insert_id,
                );
                $ins_parent_id = $this->user_model->add($parent_login_data);

                $this->student_model->add(array(
                    'id'        => $insert_id,
                    'parent_id' => $ins_parent_id,
                ));
            }

            /* =======================
               PREPARE RESPONSE
            ======================== */
            $response_data = array(
                'student_id'        => $insert_id,
                'student_session_id' => $student_session_id,
                'admission_no'      => $data_insert['admission_no'],
                'login_credentials' => array(
                    'student' => array(
                        'username' => $this->student_login_prefix . $insert_id,
                        'password' => $student_password
                    )
                )
            );

            // Add parent credentials if not sibling
            if ($sibling_id <= 0) {
                $response_data['login_credentials']['parent'] = array(
                    'username' => $this->parent_login_prefix . $insert_id,
                    'password' => $parent_password
                );
            }

            /* =======================
               SEND SUCCESS RESPONSE
            ======================== */
            return $this->output
                ->set_status_header(201)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $this->lang->line('success_message'),
                    'data'    => $response_data
                ]));
        } catch (Exception $e) {
            /* =======================
               ERROR HANDLING
            ======================== */
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Server Error: ' . $e->getMessage()
                ]));
        }
    }

    /* =======================
       HELPER FUNCTION FOR API TOKEN (Optional)
    ======================== */
    private function checkApiToken()
    {
        $headers = $this->input->request_headers();

        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            // Validate token against your authentication system
            return $this->auth_model->validateApiToken($token);
        }

        return false;
    }


    public function create_doc()
    {
        // ===============================
        // HANDLE PREFLIGHT
        // ===============================
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

        // ===============================
        // VALIDATION
        // ===============================
        $this->form_validation->set_rules('student_id', 'Student ID', 'required');
        $this->form_validation->set_rules('first_title', 'Title', 'required');

        if ($this->form_validation->run() === false) {

            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        $student_id  = $this->input->post('student_id');
        $first_title = $this->input->post('first_title');

        // echo "<pre>";
        // print_r($_FILES);exit;

        // ===============================
        // FILE UPLOAD
        // ===============================
        if (empty($_FILES['first_doc']['name'])) {

            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Document file is required'
                ]));
        }
        $attachment = null;
        if (!empty($_FILES['first_doc']['name'])) {
            $attachment = $this->media_storage->fileupload(
                "first_doc",
                "../uploads/student_documents/" . $student_id . "/"
            );
        }
        $file_name  = $attachment;

        // ===============================
        // SAVE DOCUMENT
        // ===============================
        $data_img = [
            'student_id' => $student_id,
            'title'      => $first_title,
            'doc'        => $file_name
        ];

        $this->student_model->adddoc($data_img);

        // ===============================
        // RESPONSE
        // ===============================
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Document uploaded successfully',
                'file'    => $file_name
            ]));
    }
    public function handle_uploadcreate_doc()
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
                $this->form_validation->set_message('handle_uploadcreate_doc', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_uploadcreate_doc', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($file_size > $result->file_size) {
                $this->form_validation->set_message('handle_uploadcreate_doc', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                return false;
            }

            return true;
        } else {
            $this->form_validation->set_message('handle_uploadcreate_doc', $this->lang->line('the_document_field_is_required'));
            return false;
        }
        return true;
    }

    public function handle_father_upload()
    {
        $image_validate = $this->config->item('image_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["father_pic"]) && !empty($_FILES['father_pic']['name'])) {

            $file_type = $_FILES["father_pic"]['type'];
            $file_size = $_FILES["father_pic"]["size"];
            $file_name = $_FILES["father_pic"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = @getimagesize($_FILES['father_pic']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_father_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_father_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }
                if ($file_size > $result->image_size) {
                    $this->form_validation->set_message('handle_father_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->image_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_father_upload', $this->lang->line('file_type_extension_error_uploading_image'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function handle_mother_upload()
    {
        $image_validate = $this->config->item('image_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["mother_pic"]) && !empty($_FILES['mother_pic']['name'])) {

            $file_type = $_FILES["mother_pic"]['type'];
            $file_size = $_FILES["mother_pic"]["size"];
            $file_name = $_FILES["mother_pic"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = @getimagesize($_FILES['mother_pic']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_mother_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_mother_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }
                if ($file_size > $result->image_size) {
                    $this->form_validation->set_message('handle_mother_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->image_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_mother_upload', $this->lang->line('file_type_extension_error_uploading_image'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function handle_guardian_upload()
    {

        $image_validate = $this->config->item('image_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["guardian_pic"]) && !empty($_FILES['guardian_pic']['name'])) {

            $file_type = $_FILES["guardian_pic"]['type'];
            $file_size = $_FILES["guardian_pic"]["size"];
            $file_name = $_FILES["guardian_pic"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = @getimagesize($_FILES['guardian_pic']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_guardian_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_guardian_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }
                if ($file_size > $result->image_size) {
                    $this->form_validation->set_message('handle_guardian_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->image_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_guardian_upload', $this->lang->line('file_type_extension_error_uploading_image'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function sendpassword()
    {
        $student_login_detail = array('id' => $this->input->post('student_id'), 'credential_for' => 'student', 'username' => $this->input->post('username'), 'password' => $this->input->post('password'), 'contact_no' => $this->input->post('contact_no'), 'email' => $this->input->post('email'), 'admission_no' => $this->input->post('admission_no'), 'student_session_id' => $this->input->post('student_session_id'));

        $msg = $this->mailsmsconf->mailsms('student_login_credential', $student_login_detail);
    }

    public function send_parent_password()
    {
        $parent_login_detail = array('id' => $this->input->post('student_id'), 'credential_for' => 'parent', 'username' => $this->input->post('username'), 'password' => $this->input->post('password'), 'contact_no' => $this->input->post('contact_no'), 'email' => $this->input->post('email'), 'admission_no' => $this->input->post('admission_no'), 'student_session_id' => $this->input->post('student_session_id'));

        $msg = $this->mailsmsconf->mailsms('student_login_credential', $parent_login_detail);
    }

    public function import()
    {
        /* =========================
       CORS
    ========================== */
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }


        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $class = $this->class_model->get('', $classteacher = 'yes');
            $category = $this->category_model->get();

            $fields = array(
                'admission_no',
                'class',
                'section',
                'roll_no',
                'firstname',
                'middlename',
                'lastname',
                'gender',
                'dob',
                'category_id',
                'religion',
                'cast',
                'mobileno',
                'email',
                'admission_date',
                'blood_group',
                'school_house_id',
                'height',
                'weight',
                'measurement_date',
                'father_name',
                'father_phone',
                'father_occupation',
                'mother_name',
                'mother_phone',
                'mother_occupation',
                'guardian_is',
                'guardian_name',
                'guardian_relation',
                'guardian_email',
                'guardian_phone',
                'guardian_occupation',
                'guardian_address',
                'current_address',
                'permanent_address',
                'bank_account_no',
                'bank_name',
                'ifsc_code',
                'adhar_no',
                'samagra_id',
                'rte',
                'previous_school',
                'note',
                'child_id',
                'class_of_admission'
            );

            return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'message' => 'Student master data fetched successfully',
                    'data' => [
                        'classlist' => $class,
                        'categorylist' => $category,
                        'fields' => $fields,
                    ]
                ]));
        }

        /* =========================
       CHECK FILE
    ========================== */
        if (!isset($_FILES['file']) || empty($_FILES['file']['name'])) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'CSV file is required'
                ]));
        }

        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if ($ext !== 'csv') {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Only CSV file allowed'
                ]));
        }

        /* =========================
       READ CSV
    ========================== */
        $file = $_FILES['file']['tmp_name'];

        $this->load->library('CSVReader');
        $result = $this->csvreader->parse_file($file);

        if (empty($result)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'CSV is empty or invalid format'
                ]));
        }

        /* =========================
       REQUIRED FIELDS
    ========================== */
        $fields = [
            'admission_no',
            'class',
            'section',
            'roll_no',
            'firstname',
            'middlename',
            'lastname',
            'gender',
            'dob',
            'category_id',
            'religion',
            'cast',
            'mobileno',
            'email',
            'admission_date',
            'blood_group',
            'school_house_id',
            'height',
            'weight',
            'father_name',
            'father_phone',
            'father_occupation',
            'mother_name',
            'mother_phone',
            'mother_occupation',
            'guardian_is',
            'guardian_name',
            'guardian_relation',
            'guardian_email',
            'guardian_phone',
            'guardian_occupation',
            'guardian_address',
            'current_address',
            'permanent_address',
            'bank_account_no',
            'bank_name',
            'ifsc_code',
            'adhar_no',
            'samagra_id',
            'rte',
            'previous_school',
            'note',
            'child_id',
            'class_of_admission'
        ];

        $session  = $this->setting_model->getCurrentSession();
        $rowcount = 0;
        $errors   = [];

        /* =========================
       PROCESS CSV
    ========================== */
        foreach ($result as $index => $row) {

            try {

                $student_data = [];

                /* ===== MAP CSV BY HEADER NAME ===== */
                foreach ($fields as $field) {
                    $student_data[$field] = isset($row[$field])
                        ? $this->encoding_lib->toUTF8(trim($row[$field]))
                        : null;
                }

                $student_data['is_active'] = 'yes';

                // fallback admission date
                if (empty($student_data['admission_date'])) {
                    $student_data['admission_date'] = date('Y-m-d');
                }

                /* ===== INSERT STUDENT ===== */
                $data_setting = [];
                $data_setting['id']                = $this->sch_setting_detail->id;
                $data_setting['adm_auto_insert']   = $this->sch_setting_detail->adm_auto_insert;
                $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;

                // echo "<pre>";
                // print_r($student_data);exit;



                /* =========================
               CLASS & SECTION
            ========================== */
                $class_id   = null;
                $section_id = null;

                if (!empty($student_data['class'])) {
                    $classData = $this->class_model->getClassIdByName($student_data['class']);
                    $class_id  = $classData['id'] ?? null;
                }

                if (!empty($student_data['section'])) {
                    $sectionData = $this->section_model->getSectionIdByName($student_data['section']);
                    $section_id  = $sectionData['id'] ?? null;
                }

                unset($student_data['class'], $student_data['section']);

                // echo "<pre>";
                // print_r($student_data);exit;

                $insert_id = $this->student_model->add($student_data, $data_setting);

                // echo $insert_id;
                // exit;

                if (!$insert_id) {
                    $errors[] = "Row " . ($index + 1) . " insert failed";
                    continue;
                }

                /* =========================
               STUDENT SESSION
            ========================== */
                $this->student_model->add_student_session([
                    'student_id' => $insert_id,
                    'class_id'   => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session,
                ]);

                /* =========================
               STUDENT LOGIN
            ========================== */
                $password = $this->role->get_random_password(6, 6, false, true, false);

                $this->user_model->add([
                    'username' => $this->student_login_prefix . $insert_id,
                    'password' => $password,
                    'user_id'  => $insert_id,
                    'role'     => 'student',
                ]);

                /* =========================
               PARENT LOGIN
            ========================== */
                $parent_password = $this->role->get_random_password(6, 6, false, true, false);

                $parent_id = $this->user_model->add([
                    'username' => $this->parent_login_prefix . $insert_id,
                    'password' => $parent_password,
                    'user_id'  => $insert_id,
                    'role'     => 'parent',
                    'childs'   => $insert_id,
                ]);

                $this->student_model->add([
                    'id'        => $insert_id,
                    'parent_id' => $parent_id,
                ]);

                $rowcount++;
            } catch (Exception $e) {
                $errors[] = "Row " . ($index + 1) . " error: " . $e->getMessage();
            }
        }

        /* =========================
       FINAL RESPONSE
        ========================== */
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Import completed',
                'total_records' => count($result),
                'imported_records' => $rowcount,
                'failed_records' => count($errors),
                'errors' => $errors
            ]));
    }

    public function handle_csv_upload()
    {
        $error = "";
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('csv');
            $mimes       = array(
                'text/csv',
                'text/plain',
                'application/csv',
                'text/comma-separated-values',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'text/anytext',
                'application/octet-stream',
                'application/txt'
            );
            $temp      = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if (!in_array($_FILES['file']['type'], $mimes)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            $this->form_validation->set_message('handle_csv_upload', $this->lang->line('please_select_file'));
            return false;
        }
    }



    // public function edit()
    // {

    //     // echo "comming";exit;
    //     $method = $this->input->server('REQUEST_METHOD');

    //     if ($method != 'POST') {
    //         json_output(400, array(
    //             'status' => 400,
    //             'message' => 'Bad request.'
    //         ));
    //     } else {

    //         // $check_auth_client = $this->auth_model->check_auth_client();

    //         // echo "<pre>";print_r($check_auth_client);exit;
    //         // if ($check_auth_client === true) {

    //         // $response = $this->auth_model->auth();
    //         // if ($response['status'] == 200) {

    //         $data = $this->input->POST();

    //         $data = json_decode(file_get_contents('php://input'), true);


    //         // echo "<pre>";
    //         // print_r($params);
    //         // exit;

    //         $this->form_validation->set_data($data);
    //         $this->form_validation->set_error_delimiters('', '');

    //         $this->form_validation->set_rules('student_id', 'Student ID', 'required|trim');
    //         $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
    //         $this->form_validation->set_rules('dob', 'Date of Birth', 'required|trim');
    //         $this->form_validation->set_rules('class_id', 'Class', 'required|trim');
    //         $this->form_validation->set_rules('section_id', 'Section', 'required|trim');
    //         $this->form_validation->set_rules('gender', 'Gender', 'required|trim');
    //         $this->form_validation->set_rules('child_id', 'Child ID', 'required|trim');


    //         if ($this->form_validation->run() == false) {

    //             $errors = array(
    //                 'student_id' => form_error('student_id'),
    //                 'firstname'  => form_error('firstname'),
    //                 'dob'        => form_error('dob'),
    //                 'class_id'   => form_error('class_id'),
    //                 'section_id' => form_error('section_id'),
    //                 'gender'     => form_error('gender'),
    //                 'child_id'   => form_error('child_id'),
    //             );

    //             $array = array(
    //                 'status' => '0',
    //                 'error'  => $errors
    //             );
    //         } else {

    //             $student = $this->student_model->get($data['student_id']);

    //             // echo '<pre>';
    //             // print_r($student);
    //             // exit;

    //             if (empty($student)) {
    //                 json_output(200, array(
    //                     'status' => '0',
    //                     'message' => 'Student not found'
    //                 ));
    //                 return;
    //             }

    //             $hostel_room_id = $data['hostel_room_id'];

    //             if (empty($hostel_room_id)) {
    //                 $hostel_room_id = 0;
    //             }

    //             $update_data = array(
    //                 'id'              => $data['student_id'],
    //                 'firstname'       => $data['firstname'],
    //                 'middlename'      => $data['middlename'],
    //                 'lastname'        => $data['lastname'],
    //                 'gender'          => $data['gender'],
    //                 'dob'             => $this->customlib->dateFormatToYYYYMMDD($data['dob']),
    //                 'mobileno'        => $data['mobileno'],
    //                 'email'           => $data['email'],
    //                 'guardian_name'   => $data['guardian_name'],
    //                 'guardian_phone'  => $data['guardian_phone'],
    //                 'child_id'        => $data['child_id'],
    //                 'is_active'       => 'yes',





    //             );

    //             $update_data['hostel_room_id'] = $hostel_room_id;

    //             $optional_fields = array(
    //                 'admission_no',
    //                 'roll_no',
    //                 'rte',
    //                 'state',
    //                 'city',
    //                 'pincode',
    //                 'cast',
    //                 'previous_school',
    //                 'current_address',
    //                 'permanent_address',
    //                 'adhar_no',
    //                 'samagra_id',
    //                 'bank_account_no',
    //                 'bank_name',
    //                 'ifsc_code',
    //                 'guardian_email',
    //                 'guardian_relation',
    //                 'guardian_address',
    //                 'note',
    //                 'class_of_admission',
    //                 'religion',
    //                 'permanent_address',
    //                 'category_id',
    //                 'school_house_id',
    //                 'blood_group',
    //                 'guardian_occupation',
    //                 'height',
    //                 'weight'

    //             );

    //             foreach ($optional_fields as $field) {
    //                 if (isset($data[$field]) && $data[$field] !== '') {
    //                     $update_data[$field] = $data[$field];
    //                 }
    //             }


    //             // echo "<pre>";
    //             // Print_r($update_data);
    //             // exit;

    //             $this->student_model->add($update_data);

    //             $session_data = array(
    //                 'student_id' => $data['student_id'],
    //                 'class_id'   => $data['class_id'],
    //                 'section_id' => $data['section_id'],
    //                 'session_id' => $this->setting_model->getCurrentSession(),
    //             );

    //             $this->student_model->add_student_session($session_data);

    //             $array = array(
    //                 'status' => '1',
    //                 'msg'    => 'Student updated successfully'
    //             );
    //         }

    //         // json_output(200, $array);

    //         return $this->jsonResponse(true, 'Student updated successfully');

    //         // }
    //         // }
    //     }
    // }


    public function editold()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        /* =======================
           JWT AUTH
        ======================== */
        // $user = validate_jwt_token();
        // if (!$user) {
        //     return $this->output
        //         ->set_status_header(401)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Unauthorized'
        //         ]));
        // }

        /* =======================
           READ JSON / FORM DATA
        ======================== */
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        $_POST = $input; // required for form_validation

        /* =======================
           PERMISSION
        ======================== */
        // if (!$this->rbac->hasPrivilege('student', 'can_edit')) {
        //     return $this->output->set_status_header(403)->set_output(json_encode([
        //         'status' => false,
        //         'message' => 'Permission denied'
        //     ]));
        // }

        /* =======================
           VALIDATION
        ======================== */
        $this->form_validation->set_rules('id', 'Student ID', 'required|numeric');
        $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
        $this->form_validation->set_rules('dob', 'DOB', 'required');
        $this->form_validation->set_rules('class_id', 'Class', 'required');
        $this->form_validation->set_rules('section_id', 'Section', 'required');
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('child_id', 'Child ID', 'required');

        if ($this->form_validation->run() === false) {
            return $this->output->set_output(json_encode([
                'status' => false,
                'errors' => $this->form_validation->error_array()
            ]));
        }

        /* =======================
           PREPARE DATA
        ======================== */
        $student_id = $input['id'];

        $student_data = [
            'id'                => $student_id,
            'firstname'         => $input['firstname'],
            'lastname'          => $input['lastname'] ?? null,
            'middlename'        => $input['middlename'] ?? null,
            'dob'               => $this->customlib->dateFormatToYYYYMMDD($input['dob']),
            'gender'            => $input['gender'],
            'mobileno'          => $input['mobileno'] ?? null,
            'email'             => $input['email'] ?? null,
            'guardian_name'     => $input['guardian_name'] ?? null,
            'guardian_phone'    => $input['guardian_phone'] ?? null,
            'guardian_relation' => $input['guardian_relation'] ?? null,
            'current_address'   => $input['current_address'] ?? null,
            'permanent_address' => $input['permanent_address'] ?? null,
            'child_id'          => $input['child_id'],
            'is_active'         => 'yes'
        ];

        /* =======================
           FILE UPLOADS (OPTIONAL)
        ======================== */
        if (!empty($_FILES['file']['name'])) {
            $img = $this->media_storage->fileupload('file', './uploads/student_images/');
            $student_data['image'] = 'uploads/student_images/' . $img;
        }

        if (!empty($_FILES['father_pic']['name'])) {
            $img = $this->media_storage->fileupload('father_pic', './uploads/student_images/');
            $student_data['father_pic'] = 'uploads/student_images/' . $img;
        }

        /* =======================
           UPDATE STUDENT
        ======================== */
        $this->student_model->add($student_data);

        /* =======================
           UPDATE STUDENT SESSION
        ======================== */
        $session_data = [
            'student_id'            => $student_id,
            'class_id'              => $input['class_id'],
            'section_id'            => $input['section_id'],
            'session_id'            => $this->setting_model->getCurrentSession(),
            'fees_discount'         => $input['fees_discount'] ?? 0,
            'route_pickup_point_id' => $input['route_pickup_point_id'] ?? null,
            'vehroute_id'           => $input['vehroute_id'] ?? null
        ];

        $this->student_model->add_student_session($session_data);

        /* =======================
           SUCCESS RESPONSE
        ======================== */
        return $this->output->set_output(json_encode([
            'status' => true,
            'message' => 'Student updated successfully'
        ]));
    }

    public function edit($id)
    {
        /* =========================
       CORS
        ========================== */
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
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

        /* =========================
       GET EXISTING STUDENT
    ========================== */

        $student = $this->student_model->get($id);

        if (!$student) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Student not found'
                ]));
        }

        /* =========================
       GET INPUT
    ========================== */

        $input = $this->input->post();

        if (empty($input)) {
            $input = json_decode(file_get_contents("php://input"), true);
        }

        /* =========================
       BASIC VALIDATION
    ========================== */

        if (empty($input['firstname']) || empty($input['class_id']) || empty($input['section_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Required fields missing'
                ]));
        }

        /* =========================
       PREPARE DATA
    ========================== */


        $sibling_id            = $input['sibling_id'];
        $siblings_counts       = $input['siblings_counts'];
        $siblings              = $this->student_model->getMySiblings($student['parent_id'], $student['id']);
        $total_siblings        = count($siblings);
        $class_id              = $input['class_id'];
        $section_id            = $input['section_id'];
        $hostel_room_id        = $input['hostel_room_id'] ? $input['hostel_room_id'] : $student['hostel_room_id'];
        $fees_discount         = $input['fees_discount'];
        $route_pickup_point_id = $input['route_pickup_point_id'];

        if (empty($route_pickup_point_id)) {
            $route_pickup_point_id = null;
        }

        if (empty($hostel_room_id)) {
            $hostel_room_id = 0;
        }
        $vehroute_id = $input['vehroute_id'];
        if (empty($vehroute_id)) {
            $vehroute_id = null;
        }

        $data = [
            'id' => $id,
            'firstname' => $input['firstname'],
            'lastname' => $input['lastname'] ?? $student['lastname'],
            'middlename' => $input['middlename'] ?? $student['middlename'],
            'gender' => $input['gender'] ?? $student['gender'],
            'dob' => $input['dob'] ?? $student['dob'],
            'admission_date' => $input['admission_date'] ?? $student['admission_date'],
            'measurement_date' => $input['measure_date'] ?? $student['measure_date'],

            'mobileno' => $input['mobileno'] ?? $student['mobileno'],
            'email' => $input['email'] ?? $student['email'],
            'category_id' => $input['category_id'] ?? $student['category_id'],
            'religion' => $input['religion'] ?? $student['religion'],
            'cast' => $input['cast'] ?? $student['cast'],
            'guardian_name' => $input['guardian_name'] ?? $student['guardian_name'],
            'guardian_phone' => $input['guardian_phone'] ?? $student['guardian_phone'],
            'guardian_relation' => $input['guardian_relation'] ?? $student['guardian_relation'],
            'guardian_occupation' => $input['guardian_occupation'] ?? $student['guardian_occupation'],

            'school_house_id' => $input['house'] ?? $student['school_house_id'],
            'blood_group' => $input['blood_group'] ?? $student['blood_group'],

            'guardian_email' => $input['guardian_email'] ?? $student['guardian_email'],
            'guardian_address' => $input['guardian_address'] ?? $student['guardian_address'],
            'father_name' => $input['father_name'] ?? $student['father_name'],
            'father_phone' => $input['father_phone'] ?? $student['father_phone'],
            'father_occupation' => $input['father_occupation'] ?? $student['father_occupation'],
            'mother_name' => $input['mother_name'] ?? $student['mother_name'],
            'mother_phone' => $input['mother_phone'] ?? $student['mother_phone'],
            'mother_occupation' => $input['mother_occupation'] ?? $student['mother_occupation'],
            'current_address' => $input['current_address'] ?? $student['current_address'],
            'permanent_address' => $input['permanent_address'] ?? $student['permanent_address'],
            'bank_account_no' => $input['bank_account_no'] ?? $student['bank_account_no'],
            'bank_name' => $input['bank_name'] ?? $student['bank_name'],
            'ifsc_code' => $input['ifsc_code'] ?? $student['ifsc_code'],
            'adhar_no' => $input['adhar_no'] ?? $student['adhar_no'],
            'samagra_id' => $input['samagra_id'] ?? $student['samagra_id'],
            'note' => $input['note'] ?? $student['note'],
            'child_id' => $input['child_id'] ?? $student['child_id'],
            'class_of_admission' => $input['class_of_admission'] ?? $student['class_of_admission'],
            'previous_school' => $input['previous_school'] ?? $student['previous_school'],

            'height' => $input['height'] ?? $student['height'],
            'weight' => $input['weight'] ?? $student['weight'],
            'roll_no' => $input['roll_no'] ?? $student['roll_no'],
            'hostel_room_id'    => $hostel_room_id,


        ];

        /* =========================
       FILE UPLOADS
    ========================== */

        // echo "<pre>";
        // print_r($_FILES);exit;

        $img = $img2 = $img3 = $img4 = null;

        // Student Image
        if (!empty($_FILES['image']['name'])) {
            $img = $this->media_storage->fileupload("image", "../uploads/student_images/");

            // echo "Image: ";
            // print_r($img);exit;
            $data['image'] = "uploads/student_images/" . $img;
        } else {
            $data['image'] = $student['image'];
        }

        // Father Image
        if (!empty($_FILES['father_pic']['name'])) {
            $img2 = $this->media_storage->fileupload("father_pic", "../uploads/student_images/");
            $data['father_pic'] = "uploads/student_images/" . $img2;
        } else {
            $data['father_pic'] = $student['father_pic'];
        }

        // Mother Image
        if (!empty($_FILES['mother_pic']['name'])) {
            $img3 = $this->media_storage->fileupload("mother_pic", "../uploads/student_images/");
            $data['mother_pic'] = "uploads/student_images/" . $img3;
        } else {
            $data['mother_pic'] = $student['mother_pic'];
        }

        // Guardian Image
        if (!empty($_FILES['guardian_pic']['name'])) {
            $img4 = $this->media_storage->fileupload("guardian_pic", "../uploads/student_images/");
            $data['guardian_pic'] = "uploads/student_images/" . $img4;
        } else {
            $data['guardian_pic'] = $student['guardian_pic'];
        }

        /* =========================
       UPDATE STUDENT
    ========================== */

        // echo "<pre>";
        // print_r($data);exit;

        $this->student_model->add($data);

        /* =========================
       UPDATE CLASS SESSION
    ========================== */

        $session = $this->setting_model->getCurrentSession();

        $session_data = [
            'student_id' => $id,
            'class_id' => $input['class_id'],
            'section_id' => $input['section_id'],
            'session_id' => $session,
            'fees_discount'         => $fees_discount,
            'route_pickup_point_id' => $route_pickup_point_id,
            'vehroute_id'           => $vehroute_id,
        ];

        $this->student_model->add_student_session($session_data);

        /* =========================
       RESPONSE
    ========================== */

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Student updated successfully',
                'student_id' => $id
            ]));
    }



    public function edit_student_details($student_id)
    {
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

        /* =======================
           READ JSON / FORM DATA
        ======================== */
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        $_POST = $input; // required for form_validation

        $stu_data = $this->student_model->get($student_id);

        if (empty($stu_data)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Student not found'
                ]));
        }


        $category             = $this->category_model->get();
        $session = $this->setting_model->getCurrentSession();

        $siblings                   = $this->student_model->getMySiblings($stu_data['parent_id'], $stu_data['id']);

        $student = [

            'classlist'              => $this->class_model->get('', 'yes'),
            'month'                  => $this->customlib->getMonthDropdown(),
            'genderList'             => $this->customlib->getGender(),
            'sch_setting'            => $this->sch_setting_detail,
            'adm_auto_insert'        => $this->sch_setting_detail->adm_auto_insert,
            'student_categorize'     => 'class',
            'feesessiongroup_model'  => $this->feesessiongroup_model->getFeesByGroup(),
            'transport_fees'         => $this->transportfee_model->getSessionFees($session),
            'studentlist'            => $this->student_model->getRecentRecord(),
            'siblings'               => $siblings,
            'categorylist'           => $category,

            'houseList'      => $this->student_model->gethouselist(),
            'bloodgroupList' => $this->blood_group,
            'hostelList'     => $this->hostel_model->get(),
            'vehroutelist'   => $this->vehroute_model->getRouteVehiclesList(),
            'student'                => $stu_data,

        ];




        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'        => true,
                'student_data'  => $student
            ]));
    }

    public function bulkdelete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // -----------------------------
        // ✅ GET → RETURN CLASS LIST
        // -----------------------------
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $classlist = $this->class_model->get();

            return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'data'   => [
                        'classlist' => $classlist
                    ]
                ]));
        }

        // -----------------------------
        // ❌ NOT POST
        // -----------------------------
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
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

        $class_id   = $input['class_id'] ?? '';
        $section_id = $input['section_id'] ?? '';
        $search     = $input['search'] ?? '';

        // -----------------------------
        // ✅ GET STUDENTS
        // -----------------------------
        $resultlist = $this->student_model->searchByClassSection($class_id, $section_id);

        // -----------------------------
        // ✅ RESPONSE
        // -----------------------------
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'class_id'    => $class_id,
                    'section_id'  => $section_id,
                    'students'    => $resultlist
                ]
            ]));
    }

    // public function search()
    // {
    //     if (!$this->rbac->hasPrivilege('student', 'can_view')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'Student Information');
    //     $this->session->set_userdata('sub_menu', 'student/search');
    //     $data['title']           = 'Student Search';
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
    //     $data['sch_setting']     = $this->sch_setting_detail;
    //     $data['fields']          = $this->customfield_model->get_custom_fields('students', 1);
    //     $class                   = $this->class_model->get();
    //     $data['classlist']       = $class;

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('student/studentSearch', $data);
    //     $this->load->view('layout/footer', $data);
    // }


    public function search()
    {
        // Allow preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Only GET method is allowed'
                ]));
        }

        // Prepare response data
        $data = [
            'title'            => 'Student Search',
            'classlist'        => $this->class_model->get(),
            'adm_auto_insert'  => $this->sch_setting_detail->adm_auto_insert,
            'sch_setting'      => $this->sch_setting_detail,
            'fields'           => $this->customfield_model->get_custom_fields('students', 1)
        ];

        // Return JSON response
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }


    public function ajaxsearch()
    {
        $search_type = $this->input->post('search_type');
        if ($search_type == "search_filter") {
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        }

        if ($this->form_validation->run() == false && $search_type == "search_filter") {
            $error = array();
            if ($search_type == "search_filter") {
                $error['class_id'] = form_error('class_id');
            }

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {
            $search_type = $this->input->post('search_type');
            $search_text = $this->input->post('search_text');
            $class_id    = $this->input->post('class_id');
            $section_id  = $this->input->post('section_id');
            $params      = array('class_id' => $class_id, 'section_id' => $section_id, 'search_type' => $search_type, 'search_text' => $search_text);
            $array       = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    // public function getByClassAndSection()
    // {
    //     $class      = $this->input->get('class_id');
    //     $section    = $this->input->get('section_id');
    //     $resultlist = $this->student_model->searchByClassSection($class, $section);
    //     foreach ($resultlist as $key => $value) {
    //         $resultlist[$key]['full_name'] = $this->customlib->getFullName($value['firstname'], $value['middlename'], $value['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
    //         # code...
    //     }
    //     echo json_encode($resultlist);
    // }


    public function getByClassAndSection()
    {
        // Handle preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        $class   = $input['class_id'] ?? '';
        $section = $input['section_id'] ?? '';

        $resultlist = $this->student_model->searchByClassSection($class, $section);

        foreach ($resultlist as $key => $value) {
            $resultlist[$key]['full_name'] = $this->customlib->getFullName(
                $value['firstname'],
                $value['middlename'],
                $value['lastname'],
                $this->sch_setting_detail->middlename,
                $this->sch_setting_detail->lastname
            );
        }

        // JSON response
        echo json_encode([
            "status" => "success",
            "data"   => $resultlist
        ]);
    }


    public function getByClassAndSectionExcludeMe()
    {
        $class      = $this->input->get('class_id');
        $section    = $this->input->get('section_id');
        $student_id = $this->input->get('current_student_id');
        $resultlist = $this->student_model->searchByClassSectionWithoutCurrent($class, $section, $student_id);

        foreach ($resultlist as $key => $value) {
            $resultlist[$key]['full_name'] = $this->customlib->getFullName($value['firstname'], $value['middlename'], $value['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
            # code...
        }

        echo json_encode($resultlist);
    }

    // public function getStudentRecordByID()
    // {
    //     $student_id = $this->input->get('student_id');
    //     $resultlist = $this->student_model->get($student_id);

    //     foreach ($resultlist as $key => $value) {

    //         $resultlist['full_name'] = $this->customlib->getFullName($resultlist['firstname'], $resultlist['middlename'], $resultlist['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
    //     }

    //     echo json_encode($resultlist);
    // }


    public function sibling_details($student_id = null)
    {
        // Handle preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        if (empty($student_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student ID is required'
                ]));
        }

        // Fetch student
        $student = $this->student_model->get($student_id);

        if (empty($student)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student not found'
                ]));
        }

        // Full name
        $student['full_name'] = $this->customlib->getFullName(
            $student['firstname'],
            $student['middlename'],
            $student['lastname'],
            $this->sch_setting_detail->middlename,
            $this->sch_setting_detail->lastname
        );

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $student
            ]));
    }



    public function uploadimage($id)
    {
        $data['title'] = 'Add Image';
        $data['id']    = $id;
        $this->load->view('layout/header', $data);
        $this->load->view('student/uploadimage', $data);
        $this->load->view('layout/footer', $data);
    }

    public function doupload($id)
    {
        $config = array(
            'upload_path'   => "./uploads/student_images/",
            'allowed_types' => "gif|jpg|png|jpeg|df",
            'overwrite'     => true,
        );
        $config['file_name'] = $id . ".jpg";
        $this->upload->initialize($config);
        $this->load->library('upload', $config);
        if ($this->upload->do_upload()) {
            $data        = array('upload_data' => $this->upload->data());
            $upload_data = $this->upload->data();
            $data_record = array('id' => $id, 'image' => $upload_data['file_name']);
            $this->setting_model->add($data_record);
            $this->load->view('upload_success', $data);
        } else {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('file_view', $error);
        }
    }

    public function getlogindetail()
    {
        if (!$this->rbac->hasPrivilege('student_login_credential_report', 'can_view')) {
            access_denied();
        }
        $student_id   = $this->input->post('student_id');
        $examSchedule = $this->user_model->getStudentLoginDetails($student_id);
        echo json_encode($examSchedule);
    }

    // public function getUserLoginDetails()
    // {
    //     $studentid = $this->input->post("student_id");
    //     $result    = $this->user_model->getUserLoginDetails($studentid);
    //     echo json_encode($result);
    // }


    public function getUserLoginDetails()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Method Not Allowed'
                ]));
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $studentid = $input['student_id'];

        if (empty($studentid)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Student ID is required'
                ]));
        }

        $result = $this->user_model->getUserLoginDetails($studentid);

        if (!$result) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'No login details found'
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $result
            ]));
    }


    // public function disablestudentslist()
    // {
    //     if (!$this->rbac->hasPrivilege('disable_student', 'can_view')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'Student Information');
    //     $this->session->set_userdata('sub_menu', 'student/disablestudentslist');
    //     $class                   = $this->class_model->get();
    //     $data['classlist']       = $class;
    //     $data["resultlist"]      = array();
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
    //     $data['sch_setting']     = $this->sch_setting_detail;
    //     $userdata                = $this->customlib->getUserData();
    //     $carray                  = array();
    //     $reason_list             = array();
    //     if (!empty($data["classlist"])) {
    //         foreach ($data["classlist"] as $ckey => $cvalue) {

    //             $carray[] = $cvalue["id"];
    //         }
    //     }

    //     $button = $this->input->post('search');
    //     if ($this->input->server('REQUEST_METHOD') == "GET") {
    //     } else {
    //         $class       = $this->input->post('class_id');
    //         $section     = $this->input->post('section_id');
    //         $search      = $this->input->post('search');
    //         $search_text = $this->input->post('search_text');
    //         if (isset($search)) {
    //             if ($search == 'search_filter') {
    //                 $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //                 if ($this->form_validation->run() == false) {
    //                 } else {
    //                     $data['searchby']   = "filter";
    //                     $data['class_id']   = $this->input->post('class_id');
    //                     $data['section_id'] = $this->input->post('section_id');

    //                     $data['search_text'] = $this->input->post('search_text');
    //                     $resultlist          = $this->student_model->disablestudentByClassSection($class, $section);
    //                     $data['resultlist']  = $resultlist;
    //                 }
    //             } else if ($search == 'search_full') {
    //                 $data['searchby'] = "text";

    //                 $data['search_text'] = trim($this->input->post('search_text'));
    //                 $resultlist          = $this->student_model->disablestudentFullText($search_text);
    //                 $data['resultlist']  = $resultlist;
    //             }
    //         }
    //     }

    //     $disable_reason = $this->disable_reason_model->get();

    //     foreach ($disable_reason as $key => $value) {
    //         $id               = $value['id'];
    //         $reason_list[$id] = $value;
    //     }

    //     $data['disable_reason'] = $reason_list;

    //     $this->load->view("layout/header", $data);
    //     $this->load->view("student/disablestudents", $data);
    //     $this->load->view("layout/footer", $data);
    // }


    public function get_disablestudentslist()
    {
        // Handle CORS preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Only GET method is allowed'
                ]));
        }

        // Fetch data
        $classlist       = $this->class_model->get();
        $adm_auto_insert = $this->sch_setting_detail->adm_auto_insert;
        $sch_setting     = $this->sch_setting_detail;
        $userdata        = $this->customlib->getUserData();

        // Default empty arrays (same as UI method)
        $resultlist  = [];
        $reason_list = [];
        $carray      = [];

        // API response
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'classlist'       => $classlist,
                    'resultlist'      => $resultlist,
                    'adm_auto_insert' => $adm_auto_insert,
                    'sch_setting'     => $sch_setting,
                    'user'            => $userdata,
                    'reason_list'     => $reason_list,
                    'carray'          => $carray
                ]
            ]));
    }


    public function disablestudentslist()
    {
        /* =========================
           CORS
        ========================== */
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

        /* =========================
           INPUT (JSON / FORM)
        ========================== */
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $search      = $input['search'] ?? null;
        $class_id    = $input['class_id'] ?? null;
        $section_id  = $input['section_id'] ?? null;
        $search_text = trim($input['search_text'] ?? '');

        /* =========================
           BASE DATA
        ========================== */
        $classlist = $this->class_model->get();
        $sch_setting = $this->sch_setting_detail;

        $resultlist = [];

        /* =========================
           SEARCH LOGIC
        ========================== */
        if ($search === 'search_filter') {

            if (empty($class_id)) {
                return $this->output
                    ->set_status_header(422)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'error'  => [
                            'class_id' => $this->lang->line('class') . ' is required'
                        ]
                    ]));
            }

            $resultlist = $this->student_model
                ->disablestudentByClassSection($class_id, $section_id);
        } elseif ($search === 'search_full') {


            $resultlist = $this->student_model
                ->disablestudentFullText($search_text);
        }

        /* =========================
           DISABLE REASONS
        ========================== */
        $disable_reason_raw = $this->disable_reason_model->get();
        $disable_reason = [];

        foreach ($disable_reason_raw as $row) {
            $disable_reason[$row['id']] = $row;
        }

        /* =========================
           RESPONSE
        ========================== */
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'            => true,
                'total'             => count($resultlist),
                'classlist'         => $classlist,
                'adm_auto_insert'   => $sch_setting->adm_auto_insert,
                'sch_setting'       => $sch_setting,
                'disable_reason'    => $disable_reason,
                'data'              => $resultlist
            ]));
    }



    public function disablestudent($id)
    {
        $data = array('is_active' => "no", 'disable_at' => date("Y-m-d"));
        $this->student_model->disableStudent($id, $data);
        redirect("student/view/" . $id);
    }


    // public function enablestudent($id)
    // {
    //     $data = array('is_active' => "yes");
    //     $this->student_model->disableStudent($id, $data);
    //     echo "0";
    // }


    public function enablestudent()
    {
        /* =========================
           CORS
        ========================== */
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Method Not Allowed'
                ]));
        }

        /* =========================
           INPUT (JSON / FORM)
        ========================== */
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $id = $input['student_id'];

        if (empty($id)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'Student ID is required'
                ]));
        }

        $data = array('is_active' => "yes");

        $this->student_model->disableStudent($id, $data);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => 'Student enabled successfully'
            ]));
    }

    public function savemulticlass()
    {
        $student_id       = '';
        $message          = "";
        $duplicate_record = 0;
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('student_id', $this->lang->line('student_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('row_count[]', 'row_count[]', 'trim|required|xss_clean');

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $total_rows = $this->input->post('row_count[]');
            if (!empty($total_rows)) {

                foreach ($total_rows as $key_rowcount => $row_count) {

                    $this->form_validation->set_rules('class_id_' . $row_count, $this->lang->line('class'), 'trim|required|xss_clean');

                    $this->form_validation->set_rules('section_id_' . $row_count, $this->lang->line('section'), 'trim|required|xss_clean');
                }
            }
        }

        if ($this->form_validation->run() == false) {

            $msg = array(
                'student_id'  => form_error('student_id'),
                'row_count[]' => form_error('row_count[]'),
            );

            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                if (!empty($total_rows)) {

                    $total_rows = $this->input->post('row_count[]');
                    foreach ($total_rows as $key_rowcount => $row_count) {

                        $msg['class_id_' . $row_count]   = form_error('class_id_' . $row_count);
                        $msg['section_id_' . $row_count] = form_error('section_id_' . $row_count);
                    }
                }
            }
            if (!empty($msg)) {
                $message = $this->lang->line('something_went_wrong');
            }

            $array = array('status' => '0', 'error' => $msg, 'message' => $message);
        } else {

            $rowcount            = $this->input->post('row_count[]');
            $class_section_array = array();
            $duplicate_array     = array();
            foreach ($rowcount as $key_rowcount => $value_rowcount) {

                $array = array(
                    'class_id'   => $this->input->post('class_id_' . $value_rowcount),
                    'session_id' => $this->setting_model->getCurrentSession(),
                    'student_id' => $this->input->post('student_id'),
                    'section_id' => $this->input->post('section_id_' . $value_rowcount),
                );

                $class_section_array[] = $array;
                $duplicate_array[]     = $this->input->post('class_id_' . $value_rowcount) . "-" . $this->input->post('section_id_' . $value_rowcount);
            }

            foreach (array_count_values($duplicate_array) as $val => $c) {

                if ($c > 1) {
                    $duplicate_record = 1;
                    break;
                }
            }
            if ($duplicate_record) {

                $array = array('status' => 0, 'error' => '', 'message' => $this->lang->line('duplicate_entry'));
            } else {
                $this->studentsession_model->add($class_section_array, $this->input->post('student_id'));

                $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
            }
        }
        echo json_encode($array);
    }

    // public function disable_reason()
    // {
    //     // $student_id = '';
    //     $this->form_validation->set_rules('reason', $this->lang->line('reason'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('disable_date', $this->lang->line('date'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {

    //         $msg = array(
    //             'reason'       => form_error('reason'),
    //             'disable_date' => form_error('disable_date'),
    //         );

    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {

    //         $data = array(
    //             'dis_reason' => $this->input->post('reason'),
    //             'dis_note'   => $this->input->post('note'),
    //             'id'         => $this->input->post('student_id'),
    //             'disable_at' => $this->customlib->dateFormatToYYYYMMDD($this->input->post('disable_date')),
    //             'is_active'  => 'no',
    //         );

    //         $this->student_model->add($data);

    //         $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
    //     }
    //     echo json_encode($array);
    // }


    public function disableReasonApi()
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
        // INPUT (JSON / FORM)
        // =========================
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $reason       = trim($input['reason'] ?? '');
        $disable_date = trim($input['disable_date'] ?? '');
        $note         = $input['note'] ?? '';
        $student_id   = $input['student_id'] ?? '';

        // =========================
        // VALIDATION
        // =========================
        $errors = [];

        if ($reason === '') {
            $errors['reason'] = 'Reason is required';
        }

        if ($disable_date === '') {
            $errors['disable_date'] = 'Disable date is required';
        }

        if (!empty($errors)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'error'   => $errors,
                    'message' => ''
                ]));
        }

        // =========================
        // DATA SAVE
        // =========================
        $data = [
            'id'         => $student_id,
            'dis_reason' => $reason,
            'dis_note'   => $note,
            'disable_at' => $this->customlib->dateFormatToYYYYMMDD($disable_date),
            'is_active'  => 'no'
        ];

        $this->student_model->add($data);

        // =========================
        // SUCCESS RESPONSE
        // =========================
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'error'   => '',
                'message' => $this->lang->line('success_message')
            ]));
    }


    public function ajax_delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ❌ Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
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

        $students = $input['student'] ?? [];

        // Ensure array
        if (!is_array($students)) {
            $students = [$students];
        }

        // -----------------------------
        // ❌ VALIDATION
        // -----------------------------
        if (empty($students)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'student' => 'Student field is required'
                    ]
                ]));
        }

        // -----------------------------
        // ✅ DELETE
        // -----------------------------
        $this->student_model->bulkdelete($students);

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Students deleted successfully'
            ]));
    }

    public function profilesetting()
    {
        if (!$this->rbac->hasPrivilege('student_profile_update', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'System Settings/profilesetting');
        $data                    = array();
        $data['result']          = $this->setting_model->getSetting();
        $data['fields']          = get_student_editable_fields();
        $data['inserted_fields'] = $this->student_edit_field_model->get();
        $data['result']          = $this->setting_model->getSetting();
        $this->form_validation->set_rules('student_profile_edit', $this->lang->line('student_profile_update'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == true) {
            $data_record = array(
                'id'                   => $this->input->post('sch_id'),
                'student_profile_edit' => $this->input->post('student_profile_edit'),
            );
            $this->setting_model->add($data_record);
            $this->session->set_flashdata('msg', '<div class="alert alert-left">' . $this->lang->line('update_message') . '</div>');
            redirect('student/profilesetting');
        }
        $data['sch_setting_detail'] = $this->sch_setting_detail;
        $data['custom_fields']      = $this->onlinestudent_model->getcustomfields();
        $this->load->view("layout/header");
        $this->load->view("student/profilesetting", $data);
        $this->load->view("layout/footer");
    }

    public function changeprofilesetting()
    {
        $this->form_validation->set_rules('name', $this->lang->line('student'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('status', $this->lang->line('status'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'status' => form_error('status'),
                'name'   => form_error('name'),
            );

            $array = array('status' => '0', 'error' => $msg, 'msg' => $this->lang->line('something_went_wrong'));
        } else {
            $insert = array(
                'name'   => $this->input->post('name'),
                'status' => $this->input->post('status'),
            );
            $this->student_edit_field_model->add($insert);
            $array = array('status' => '1', 'error' => '', 'msg' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    /**
     * This function is used to view bulk mail page
     */
    public function bulkmail()
    {
        if (!$this->rbac->hasPrivilege('login_credentials_send', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Communicate');
        $this->session->set_userdata('sub_menu', 'bulk_mail');
        $class                    = $this->class_model->get();
        $data['classlist']        = $class;
        $data['sch_setting']      = $this->sch_setting_detail;
        $data['bulkmailto']       = $this->customlib->bulkmailto();
        $data['notificationtype'] = $this->customlib->bulkmailnotificationtype();
        $data['fields']           = $this->customfield_model->get_custom_fields('students', 1);
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('student/bulkmail', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');

            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
            } else {
                $data['class_id']   = $this->input->post('class_id');
                $data['section_id'] = $this->input->post('section_id');
                $resultlist         = $this->student_model->searchByClassSection($class, $section);
                $data['resultlist'] = $resultlist;
            }

            $this->load->view('layout/header', $data);
            $this->load->view('student/bulkmail', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    /**
     * This function is used to send bulk mail to student and Parent
     */
    public function sendbulkmail()
    {
        $this->form_validation->set_rules('student[]', $this->lang->line('student'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('message_to', $this->lang->line('message_to'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('notification_type', $this->lang->line('notification_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'student[]'         => form_error('student[]'),
                'message_to'        => form_error('message_to'),
                'notification_type' => form_error('notification_type'),
            );
            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $students          = $this->input->post('student');
            $message_to        = $this->input->post('message_to');
            $notification_type = $this->input->post('notification_type');

            foreach ($students as $students_value) {

                $student_detail = $this->user_model->student_information($students_value);

                if (($message_to == 1 && $notification_type == 1) || ($message_to == 1 && $notification_type == 3) || ($message_to == 3 && $notification_type == 3)) {

                    $sender_details = array('student_id' => $students_value, 'contact_no' => $student_detail[0]->mobileno, 'email' => $student_detail[0]->email, 'student_session_id' => $student_detail[0]->student_session_id);

                    $this->mailsmsconf->bulkmailsms('student_admission', $sender_details);
                }

                if (($message_to == 1 && $notification_type == 2) || ($message_to == 1 && $notification_type == 3) || ($message_to == 3 && $notification_type == 3) || ($message_to == 3 && $notification_type == 2)) {

                    $student_login_detail = array('display_name' => $student_detail[0]->firstname . ' ' . $student_detail[0]->lastname, 'id' => $students_value, 'credential_for' => 'student', 'username' => $student_detail[0]->username, 'password' => $student_detail[0]->password, 'contact_no' => $student_detail[0]->mobileno, 'email' => $student_detail[0]->email, 'student_session_id' => $student_detail[0]->student_session_id, 'admission_no' => $student_detail[0]->admission_no);

                    $this->mailsmsconf->bulkmailsms('student_login_credential', $student_login_detail);
                }

                if (($message_to == 2 && $notification_type == 1) || ($message_to == 2 && $notification_type == 3) || ($message_to == 3 && $notification_type == 3) || ($message_to == 3 && $notification_type == 1)) {

                    $sender_details = array('student_id' => $students_value, 'contact_no' => $student_detail[0]->guardian_phone, 'email' => $student_detail[0]->guardian_email, 'student_session_id' => $student_detail[0]->student_session_id);

                    $this->mailsmsconf->bulkmailsms('student_admission', $sender_details);
                }

                if (($message_to == 2 && $notification_type == 2) || ($message_to == 2 && $notification_type == 3) || ($message_to == 3 && $notification_type == 3) || ($message_to == 3 && $notification_type == 2)) {

                    $parent_detail = $this->user_model->read_single_child($student_detail[0]->parent_id);

                    $parent_login_detail = array('display_name' => $student_detail[0]->firstname . ' ' . $student_detail[0]->lastname, 'id' => $students_value, 'credential_for' => 'parent', 'username' => $parent_detail->username, 'password' => $parent_detail->password, 'contact_no' => $student_detail[0]->guardian_phone, 'email' => $student_detail[0]->guardian_email, 'student_session_id' => $student_detail[0]->student_session_id, 'admission_no' => $student_detail[0]->admission_no);

                    $this->mailsmsconf->bulkmailsms('student_login_credential', $parent_login_detail);
                }
            }

            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('message_sent_successfully'));
        }
        echo json_encode($array);
    }

    public function dtstudentlist()
    {
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $class           = $this->input->post('class_id');
        $section         = $this->input->post('section_id');
        $search_text     = $this->input->post('search_text');
        $search_type     = $this->input->post('srch_type');
        $classlist       = $this->class_model->get();
        $classlist       = $classlist;
        $carray          = array();
        if (!empty($classlist)) {
            foreach ($classlist as $ckey => $cvalue) {
                $carray[] = $cvalue["id"];
            }
        }

        $sch_setting = $this->sch_setting_detail;

        if ($search_type == "search_filter") {

            $resultlist = $this->student_model->searchdtByClassSection($class, $section);
        } elseif ($search_type == "search_full") {

            $resultlist = $this->student_model->searchFullText($search_text, $carray);
        }

        $students = array();
        $students = json_decode($resultlist);

        $dt_data = array();
        $fields  = $this->customfield_model->get_custom_fields('students', 1);

        if (!empty($students->data)) {
            foreach ($students->data as $student_key => $student) {

                $editbtn    = '';
                $deletebtn  = '';
                $viewbtn    = '';
                $collectbtn = '';

                $viewbtn = "<a href='" . base_url() . "student/view/" . $student->id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('view') . "'><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('student', 'can_edit')) {
                    $editbtn = "<a href='" . base_url() . "student/edit/" . $student->id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->module_lib->hasActive('fees_collection') && $this->rbac->hasPrivilege('collect_fees', 'can_add')) {

                    $collectbtn = "<a href='" . base_url() . "studentfee/addfee/" . $student->student_session_id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('add_fees') . "'><span >" . $currency_symbol . "</a>";
                }

                $row   = array();
                $row[] = $student->admission_no;
                $row[] = "<a href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                $row[] = $student->class . "(" . $student->section . ")";
                if ($sch_setting->father_name) {
                    $row[] = $student->father_name;
                }

                $row[] = $this->customlib->dateformat($student->dob);

                if (!empty($student->gender)) {
                    $row[] = $this->lang->line(strtolower($student->gender));
                } else {
                    $row[] = '';
                }

                if ($sch_setting->category) {
                    $row[] = $student->category;
                }
                if ($sch_setting->mobile_no) {
                    $row[] = $student->mobileno;
                }

                foreach ($fields as $fields_key => $fields_value) {

                    $custom_name   = $fields_value->name;
                    $display_field = $student->$custom_name;
                    if ($fields_value->type == "link") {
                        $display_field = "<a href=" . $student->$custom_name . " target='_blank'>" . $student->$custom_name . "</a>";
                    }
                    $row[] = $display_field;
                }

                $row[] = $viewbtn . '' . $editbtn . '' . $collectbtn;

                $dt_data[] = $row;
            }
        }
        $sch_setting         = $this->sch_setting_detail;
        $student_detail_view = $this->load->view('student/_searchDetailView', array('sch_setting' => $sch_setting, 'students' => $students), true);
        $json_data           = array(
            "draw"                => intval($students->draw),
            "recordsTotal"        => intval($students->recordsTotal),
            "recordsFiltered"     => intval($students->recordsFiltered),
            "data"                => $dt_data,
            "student_detail_view" => $student_detail_view,
        );

        echo json_encode($json_data);
    }

    //datatable function to check search parameter validation
    public function searchvalidation()
    {
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');

        $srch_type   = $this->input->post('search_type');
        $search_text = $this->input->post('search_text');

        if ($srch_type == 'search_filter') {

            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == true) {

                $params = array('srch_type' => $srch_type, 'class_id' => $class_id, 'section_id' => $section_id);
                $array  = array('status' => 1, 'error' => '', 'params' => $params);
                echo json_encode($array);
            } else {

                $error             = array();
                $error['class_id'] = form_error('class_id');
                $array             = array('status' => 0, 'error' => $error);
                echo json_encode($array);
            }
        } else {
            $params = array('srch_type' => 'search_full', 'class_id' => $class_id, 'section_id' => $section_id, 'search_text' => $search_text);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    // public function getStudentByClassSection()
    // {
    //     $data                 = array();
    //     $cls_section_id       = $this->input->post('cls_section_id');
    //     $data['fields']       = $this->customfield_model->get_custom_fields('students', 1);
    //     $student_list         = $this->student_model->getStudentBy_class_section_id($cls_section_id);
    //     $data['student_list'] = $student_list;
    //     $data['sch_setting']  = $this->sch_setting_detail;
    //     $page                 = $this->load->view('reports/_getStudentByClassSection', $data, true);
    //     echo json_encode(array('status' => 1, 'page' => $page));
    // }


    public function getStudentByClassSection()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read raw JSON input
        $rawInput = file_get_contents("php://input");
        $request  = json_decode($rawInput, true);

        // Get cls_section_id from JSON
        $cls_section_id = $request['cls_section_id'] ?? null;

        if (empty($cls_section_id)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'cls_section_id is required'
                ]));
        }

        // Fetch data
        $data = [];
        $data['fields']       = $this->customfield_model->get_custom_fields('students', 1);
        $data['student_list'] = $this->student_model->getStudentBy_class_section_id($cls_section_id);
        $data['sch_setting']  = $this->sch_setting_detail;

        // Success response
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }

    public function handle_upload($str, $var)
    {
        $image_validate = $this->config->item('image_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
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

    public function handle_uploadfordoc($str, $var)
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

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $_FILES[$var]['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mtype, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_uploadfordoc', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_uploadfordoc', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($file_size > $result->file_size) {
                $this->form_validation->set_message('handle_uploadfordoc', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                return false;
            }

            return true;
        }
    }

    public function countAttendance($session_year_start, $student_session_id)
    {
        $attendencetypes = $this->attendencetype_model->getAttType();

        $record = array();
        foreach ($attendencetypes as $type_key => $type_value) {
            $record[$type_value['id']] = 0;
        }

        for ($i = 1; $i <= 12; $i++) {
            $start_month        = date('Y-m-d', strtotime($session_year_start));
            $end_month          = date('Y-m-t', strtotime($session_year_start));
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

    public function getAdmissionNoByGuardianEmail()
    {
        $student_id =   $_POST['student_id'];
        $guardian_email =   $_POST['guardian_email'];

        $student_admission_no = $this->student_model->getAdmissionNoByGuardianEmail($student_id, $guardian_email);

        if ($student_admission_no['guardian_email']) {

            echo "This Guardian Email is already exists due to " . $student_admission_no['firstname'] . " " . $student_admission_no['middlename'] . " " . $student_admission_no['lastname'] . " (" . $student_admission_no['admission_no'] . ") and their siblings guardian email, if this student is also sibling then add as sibling";
        } else {
            echo "";
        }
    }

    public function getAdmissionNoByGuardianPhone()
    {
        $student_id =   $_POST['student_id'];
        $guardian_phone =   $_POST['guardian_phone'];

        $student_admission_no = $this->student_model->getAdmissionNoByGuardianPhone($student_id, $guardian_phone);

        if ($student_admission_no['guardian_phone']) {

            echo "This Guardian Phone is already exists due to " . $student_admission_no['firstname'] . " " . $student_admission_no['middlename'] . " " . $student_admission_no['lastname'] . " (" . $student_admission_no['admission_no'] . ") and their siblings guardian phone, if this student is also sibling then add as sibling";
        } else {
            echo "";
        }
    }

    public function student_analysis()
    {
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        foreach ($data['classlist'] as $key => $value) {
            $carray[] = $value['id'];
        }

        $data['resultlist'] = $this->student_model->student_ratio();
        $total_boys         = $total_girls         = 0;
        foreach ($data['resultlist'] as $key => $value) {

            $total_boys += $value['male'];
            $total_girls += $value['female'];

            $data['result'][] = array('total_student' => $value['total_student'], 'male' => $value['male'], 'female' => $value['female'], 'class' => $value['class'], 'section' => $value['section'], 'class_id' => $value['class_id'], 'section_id' => $value['section_id']);
        }

        $this->load->view('layout/header', $data);
        $this->load->view('student/studentanalysis', $data);
        $this->load->view('layout/footer', $data);
    }

    // exportsubjectstudentmarks
    public function exportsubjectstudentmarks()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_studentsubjectmarks_sample_file.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_student_exam_marks_file.csv';

        force_download($name, $data);
    }

    // bulk detain students

    public function bulkdetain()
    {
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'bulkdetain');
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['fields']          = $this->customfield_model->get_custom_fields('students', 1);
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search  = $this->input->post('search');

            $data['searchby']    = "filter";
            $data['class_id']    = $this->input->post('class_id');
            $data['section_id']  = $this->input->post('section_id');
            $data['search_text'] = $this->input->post('search_text');
            $resultlist          = $this->student_model->searchByClassSection($class, $section);
            $data['resultlist']  = $resultlist;
        }
        $this->load->view('layout/header', $data);
        $this->load->view('student/bulkdetain', $data);
        $this->load->view('layout/footer', $data);
    }

    public function ajax_detain()
    {

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('student[]', $this->lang->line('student'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'student[]' => form_error('student[]'),
            );
            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $students = $this->input->post('student');

            foreach ($students as $student_key => $student_value) {
            }

            $this->student_model->bulkdetain($students);

            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('detain_message'));
        }
        echo json_encode($array);
    }

    function json_output($statusHeader, $response)
    {
        $ci = &get_instance();
        $ci->output->set_content_type('application/json');
        $ci->output->set_status_header($statusHeader);
        $ci->output->set_output(json_encode($response));
        $ci->output->_display();
        exit;
    }

    public function studentListApi()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
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

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $class_id   = $input['class_id']   ?? null;
        $section_id = $input['section_id'] ?? null;
        $search     = $input['search']     ?? '';

        $students = $this->student_model->getStudentsApi(
            $class_id,
            $section_id,
            $search
        );

        // echo "<pre>";print_r($students);exit;

        $fields      = $this->customfield_model->get_custom_fields('students', 1);
        $sch_setting = $this->sch_setting_detail;
        $data        = [];

        foreach ($students as $student) {

            $custom_fields = [];
            foreach ($fields as $field) {
                $custom_fields[] = [
                    'name'  => $field->name,
                    'type'  => $field->type,
                    'value' => $student->{$field->name} ?? null
                ];
            }

            $data[] = [
                'id'                 => $student->id,
                'student_session_id' => $student->student_session_id,
                'admission_no'       => $student->admission_no,
                'full_name'          => $this->customlib->getFullName(
                    $student->firstname,
                    $student->middlename,
                    $student->lastname,
                    $sch_setting->middlename,
                    $sch_setting->lastname
                ),
                'class_section' => $student->class . ' (' . $student->section . ')',
                'father_name'   => $sch_setting->father_name ? $student->father_name : null,
                'dob'           => $this->customlib->dateformat($student->dob),
                'gender'        => $student->gender,
                'mobile_no'     => $sch_setting->mobile_no ? $student->mobileno : null,
                'custom_fields' => $custom_fields,
                'image'         => $student->image
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'total'  => count($data),
                'data'   => $data
            ]));
    }




    private function jsonResponse($status, $message, $code = 200, $data = [])
    {
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => $status,
                'message' => $message,
                'data' => $data
            ]));
    }
}

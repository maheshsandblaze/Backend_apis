<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staff extends Public_Controller
{

    public $sch_setting_detail = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->config->load("payroll");
        $this->config->load("app-config");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->model("staff_model");
        $this->load->library('encoding_lib');
        $this->load->model("leaverequest_model");
        $this->load->model("setting_model");
        $this->load->model("staffattendancemodel");
        $this->load->model("payroll_model");
        $this->load->model("attendencetype_model");
        $this->load->model("role_model");
        $this->load->model("timeline_model");
        $this->load->model("admin_model");

        $this->contract_type      = $this->config->item('contracttype');
        $this->marital_status     = $this->config->item('marital_status');
        $this->staff_attendance   = $this->config->item('staffattendance');
        $this->payroll_status     = $this->config->item('payroll_status');
        $this->payment_mode       = $this->config->item('payment_mode');
        $this->status             = $this->config->item('status');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input;
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input() ?: $this->input->get(NULL);
        $search = $input['search'] ?? null;
        $role = $input['role'] ?? null;
        $search_text = $input['search_text'] ?? "";

        if (isset($search)) {
            if ($search == 'search_filter') {
                $resultlist = $this->staff_model->getEmployee($role, 1);
            } else if ($search == 'search_full') {
                $resultlist = $this->staff_model->searchFullText($search_text, 1);
            } else {
                $resultlist = $this->staff_model->searchFullText("", 1);
            }
        } else {
            $resultlist = $this->staff_model->searchFullText("", 1);
        }

        $data = [
            'resultlist' => $resultlist,
            'staff_role' => $this->staff_model->getStaffRole(),
            'fields'     => $this->customfield_model->get_custom_fields('staff', 1),
            'title'      => $this->lang->line('staff_list'),
        ];

        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $data]));
    }

    public function disablestafflist()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input() ?: $this->input->get(NULL);
        $search = $input['search'] ?? null;
        $role = $input['role'] ?? null;
        $search_text = $input['search_text'] ?? "";

        if (isset($search)) {
            if ($search == 'search_filter') {
                $resultlist = $this->staff_model->getEmployee($role, 0);
            } else if ($search == 'search_full') {
                $resultlist = $this->staff_model->searchFullText($search_text, 0);
            } else {
                $resultlist = $this->staff_model->searchFullText($search_text, 0);
            }
        } else {
            $resultlist = $this->staff_model->searchFullText($search_text, 0);
        }

        $data = [
            'resultlist' => $resultlist,
            'role'       => $this->staff_model->getStaffRole(),
            'title'      => 'Disabled Staff List',
        ];

        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $data]));
    }

    public function profile($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $staff_info      = $this->staff_model->getProfile($id);
        $timeline_list   = $this->timeline_model->getStaffTimeline($id, '');
        $staff_payroll   = $this->staff_model->getStaffPayroll($id);
        $staff_leaves    = $this->leaverequest_model->staff_leave_request($id);
        $alloted_leavetype = $this->staff_model->allotedLeaveType($id);
        $salary          = $this->payroll_model->getSalaryDetails($id);
        $attendencetypes = $this->staffattendancemodel->getStaffAttendanceType();

        $i = 0;
        $leaveDetail = array();
        foreach ($alloted_leavetype as $key => $value) {
            $count_leaves[]                   = $this->leaverequest_model->countLeavesData($id, $value["leave_type_id"]);
            $leaveDetail[$i]['type']          = $value["type"];
            $leaveDetail[$i]['alloted_leave'] = $value["alloted_leave"];
            $leaveDetail[$i]['approve_leave'] = $count_leaves[$i]['approve_leave'];
            $i++;
        }

        $monthlist  = $this->customlib->getMonthDropdown();
        $startMonth = $this->setting_model->getStartMonth();
        $yearlist   = $this->staffattendancemodel->attendanceYearCount();
        $year       = date("Y");

        $attendence_count = array();
        foreach ($attendencetypes as $att_value) {
            $attendence_count[$att_value['type']] = array();
        }

        $date_array = array();
        $res = array();
        foreach ($monthlist as $key => $value) {
            $datemonth       = date("m", strtotime($key));
            $date_each_month = date('Y-' . $datemonth . '-01');
            $date_start = date('01', strtotime($date_each_month));
            $date_end   = date('t', strtotime($date_each_month));
            for ($n = $date_start; $n <= $date_end; $n++) {
                $att_dates        = $year . "-" . $datemonth . "-" . sprintf("%02d", $n);
                $date_array[]     = $att_dates;
                $staff_attendence = $this->staffattendancemodel->searchStaffattendance($att_dates, $id, false);
                if (!empty($staff_attendence)) {
                    if ($staff_attendence['att_type'] != "") {
                        $attendence_count[$staff_attendence['att_type']][] = 1;
                    }
                }
                $res[$att_dates] = $staff_attendence;
            }
        }

        $data = [
            'staff'            => $staff_info,
            'timeline_list'    => $timeline_list,
            'staff_payroll'    => $staff_payroll,
            'leavedetails'     => $leaveDetail,
            'staff_leaves'     => $staff_leaves,
            'salary'           => $salary,
            'attendencetypeslist' => $attendencetypes,
            'monthlist'        => $monthlist,
            'yearlist'         => $yearlist,
            'countAttendance'  => $attendence_count,
            'resultlist'       => $res,
            'date_array'       => $date_array,
            'payroll_status'   => $this->payroll_status,
            'payment_mode'     => $this->payment_mode,
            'contract_type'    => $this->contract_type,
            'status'           => $this->status,
            'roles'            => $this->role_model->get(),
            'sch_setting'      => $this->sch_setting_detail,
        ];

        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $data]));
    }

    public function countAttendance($year, $emp)
    {
        $record = array();
        foreach ($this->staff_attendance as $att_key => $att_value) {
            $s           = $this->staff_model->count_attendance($year, $emp, $att_value);
            $r[$att_key] = $s;
        }
        $record[$year] = $r;
        return $record;
    }

    public function ajax_attendance()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $id    = $this->input->post("id");
        $year  = $this->input->post("year");
        if (!empty($year)) {
            $monthlist         = $this->customlib->getMonthDropdown();
            $res = array();
            $attendence_array = array();
            $date_array = array();
            foreach ($monthlist as $key => $value) {
                $datemonth       = date("m", strtotime($key));
                $date_each_month = date('Y-' . $datemonth . '-01');
                $date_end        = date('t', strtotime($date_each_month));
                for ($n = 1; $n <= $date_end; $n++) {
                    $att_date           = sprintf("%02d", $n);
                    $attendence_array[] = $att_date;
                    $att_dates          = $year . "-" . $datemonth . "-" . sprintf("%02d", $n);
                    $date_array[]    = $att_dates;
                    $res[$att_dates] = $this->staffattendancemodel->searchStaffattendance($att_dates, $id);
                }
            }
            $countAttendance = $this->countAttendance($year, $id);
            return $this->output->set_status_header(200)->set_output(json_encode([
                'status' => 1,
                'countAttendance' => $countAttendance[$year],
                'resultlist' => $res,
                'attendence_array' => $attendence_array,
                'date_array' => $date_array
            ]));
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('role', $this->lang->line('role'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', $this->lang->line('date_of_birth'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), array('required', 'valid_email', array('check_exists', array($this->staff_model, 'valid_email_id'))));

        if ($this->form_validation->run() == false) {
            $errors = array(
                'name'   => form_error('name'),
                'role'   => form_error('role'),
                'gender' => form_error('gender'),
                'dob'    => form_error('dob'),
                'email'  => form_error('email'),
            );
            return $this->output->set_status_header(422)->set_output(json_encode(['status' => false, 'errors' => $errors]));
        } else {
            $password = $this->role->get_random_password(6, 6, false, true, false);
            $data_insert = array(
                'password'        => $this->enc_lib->passHashEnc($password),
                'employee_id'     => $input['employee_id'] ?? '',
                'name'            => $input['name'],
                'surname'         => $input['surname'] ?? '',
                'email'           => $input['email'],
                'dob'             => date('Y-m-d', $this->customlib->datetostrtotime($input['dob'])),
                'gender'          => $input['gender'],
                'is_active'       => 1,
                'contact_no'      => $input['contactno'] ?? '',
                'emergency_contact_no' => $input['emergency_no'] ?? '',
                'marital_status'  => $input['marital_status'] ?? '',
                'local_address'   => $input['address'] ?? '',
                'permanent_address' => $input['permanent_address'] ?? '',
                'qualification'   => $input['qualification'] ?? '',
                'work_exp'        => $input['work_exp'] ?? '',
                'note'            => $input['note'] ?? '',
                'epf_no'          => $input['epf_no'] ?? '',
                'basic_salary'    => $input['basic_salary'] ?? 0,
                'contract_type'   => $input['contract_type'] ?? '',
                'shift'           => $input['shift'] ?? '',
                'location'        => $input['location'] ?? '',
                'bank_account_no' => $input['bank_account_no'] ?? '',
                'bank_name'       => $input['bank_name'] ?? '',
                'account_title'   => $input['account_title'] ?? '',
                'ifsc_code'       => $input['ifsc_code'] ?? '',
                'bank_branch'     => $input['bank_branch'] ?? '',
                'facebook'        => $input['facebook'] ?? '',
                'twitter'         => $input['twitter'] ?? '',
                'linkedin'        => $input['linkedin'] ?? '',
                'instagram'       => $input['instagram'] ?? '',
                'mother_name'     => $input['mother_name'] ?? '',
                'father_name'     => $input['father_name'] ?? '',
            );

            if ($input['date_of_joining'] != "") {
                $data_insert['date_of_joining'] = date('Y-m-d', $this->customlib->datetostrtotime($input['date_of_joining']));
            }

            $department  = $input['department'] ?? null;
            $designation = $input['designation'] ?? null;

            if ($department != '') {
                $data_insert['department'] = $department;
            }

            if ($designation != '') {
                $data_insert['designation'] = $designation;
            }

            // Custom fields processing
            $custom_value_array = array();
            if (isset($input['custom_fields'])) {
                foreach ($input['custom_fields'] as $cf_key => $cf_value) {
                    $custom_value_array[] = array('belong_table_id' => 0, 'custom_field_id' => $cf_key, 'field_value' => $cf_value);
                }
            }

            // Handle image upload if any
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $img_name             = $this->media_storage->fileupload("file", "../uploads/staff_images/");
                $data_insert['image'] = $img_name;
            }

            $role_array = array('role_id' => $input['role'], 'staff_id' => 0);
            $leave_type = $input['leave_type'] ?? [];
            $leave_array = array();
            foreach ($leave_type as $lt_id) {
                $leave_array[] = array('staff_id' => 0, 'leave_type_id' => $lt_id, 'alloted_leave' => $input['alloted_leave_' . $lt_id] ?? 0);
            }

            // echo "<pre>";    
            //  print_r($data_insert);

            //  die;

            $insert_id = $this->staff_model->batchInsert($data_insert, $role_array, $leave_array, array('id' => $this->sch_setting_detail->id, 'staffid_auto_insert' => $this->sch_setting_detail->staffid_auto_insert, 'staffid_update_status' => $this->sch_setting_detail->staffid_update_status));

            if ($insert_id) {
                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }

                // Handle documents
                $upload_dir = '../uploads/staff_documents/' . $insert_id . '/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $resume = (isset($_FILES["first_doc"]) && !empty($_FILES['first_doc']['name'])) ? $this->media_storage->fileupload("first_doc", $upload_dir) : "";
                $joining_letter = (isset($_FILES["second_doc"]) && !empty($_FILES['second_doc']['name'])) ? $this->media_storage->fileupload("second_doc", $upload_dir) : "";
                $resignation_letter = (isset($_FILES["third_doc"]) && !empty($_FILES['third_doc']['name'])) ? $this->media_storage->fileupload("third_doc", $upload_dir) : "";
                $fourth_doc = (isset($_FILES["fourth_doc"]) && !empty($_FILES['fourth_doc']['name'])) ? $this->media_storage->fileupload("fourth_doc", $upload_dir) : "";

                $data_doc = array('id' => $insert_id, 'resume' => $resume, 'joining_letter' => $joining_letter, 'resignation_letter' => $resignation_letter, 'other_document_name' => 'Other Folder', 'other_document_file' => $fourth_doc);
                $this->staff_model->add($data_doc);

                return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => $this->lang->line('success_message'), 'id' => $insert_id]));
            }
            return $this->output->set_status_header(500)->set_output(json_encode(['status' => false, 'message' => 'Failed to create staff']));
        }
    }


    public function create_meta()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $roles        = $this->role_model->get();
        $designation  = $this->staff_model->getStaffDesignation();
        $department   = $this->staff_model->getDepartment();
        $leavetype    = $this->staff_model->getLeaveType();
        $payscale     = $this->staff_model->getPayroll();
        $genderList   = $this->customlib->getGender();

        $data = [
            'roles'        => $roles,
            'designation'  => $designation,
            'department'   => $department,
            'leave_types'  => $leavetype,
            'payscale'     => $payscale,
            'gender'       => $genderList,
            'contract_type' => $this->contract_type,
            'marital_status' => $this->marital_status
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data' => $data
            ]));
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        /* =====================================
       ✅ GET → RETURN STAFF + MASTER DATA
    ===================================== */
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $staff = $this->staff_model->get($id);

            if (!$staff) {
                return $this->output
                    ->set_status_header(404)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'  => false,
                        'message' => 'Staff not found'
                    ]));
            }

            // 🔹 Master Data
            $genderList     = $this->customlib->getGender();
            $payscaleList   = $this->staff_model->getPayroll();
            $leavetypeList  = $this->staff_model->getLeaveType();
            $staffRole      = $this->staff_model->getStaffRole();
            $designation    = $this->staff_model->getStaffDesignation();
            $department     = $this->staff_model->getDepartment();
            $marital_status = $this->marital_status;

            // 🔹 Staff Related
            $staffLeaveDetails = $this->staff_model->getLeaveDetails($id);
            $custom_fields     = $this->customfield_model->getByBelong('staff', $id);

            return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'data'   => [
                        'staff'               => $staff,
                        'leave_details'       => $staffLeaveDetails,
                        'custom_fields'       => $custom_fields,

                        // Dropdown Data
                        'gender_list'         => $genderList,
                        'payscale_list'       => $payscaleList,
                        'leave_type_list'     => $leavetypeList,
                        'role_list'           => $staffRole,
                        'designation_list'    => $designation,
                        'department_list'     => $department,
                        'marital_status_list' => $marital_status
                    ]
                ]));
        }

        /* =====================================
       ❌ BLOCK OTHER METHODS
    ===================================== */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        /* =====================================
       ✅ POST → UPDATE STAFF
    ===================================== */

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('role', $this->lang->line('role'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', $this->lang->line('date_of_birth'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $errors = array(
                'name'   => form_error('name'),
                'role'   => form_error('role'),
                'gender' => form_error('gender'),
                'dob'    => form_error('dob'),
            );
            return $this->output->set_status_header(422)->set_output(json_encode([
                'status' => false,
                'errors' => $errors
            ]));
        }

        $staff = $this->staff_model->get($id);

        if (!$staff) {
            return $this->output->set_status_header(404)->set_output(json_encode([
                'status' => false,
                'message' => 'Staff not found'
            ]));
        }

        // 🔹 Update Data
        $data_update = array(
            'id'              => $id,
            'employee_id'     => $input['employee_id'] ?? '',
            'name'            => $input['name'],
            'surname'         => $input['surname'] ?? '',
            'email'           => $input['email'] ?? '',
            'dob'             => date('Y-m-d', $this->customlib->datetostrtotime($input['dob'])),
            'gender'          => $input['gender'],
            'contact_no'      => $input['contactno'] ?? '',
            'emergency_contact_no' => $input['emergency_no'] ?? '',
            'marital_status'  => $input['marital_status'] ?? '',
            'local_address'   => $input['address'] ?? '',
            'permanent_address' => $input['permanent_address'] ?? '',
            'qualification'   => $input['qualification'] ?? '',
            'work_exp'        => $input['work_exp'] ?? '',
            'note'            => $input['note'] ?? '',
            'epf_no'          => $input['epf_no'] ?? '',
            'basic_salary'    => $input['basic_salary'] ?? 0,
            'contract_type'   => $input['contract_type'] ?? '',
            'shift'           => $input['shift'] ?? '',
            'location'        => $input['location'] ?? '',
            'bank_account_no' => $input['bank_account_no'] ?? '',
            'bank_name'       => $input['bank_name'] ?? '',
            'account_title'   => $input['account_title'] ?? '',
            'ifsc_code'       => $input['ifsc_code'] ?? '',
            'bank_branch'     => $input['bank_branch'] ?? '',
            'facebook'        => $input['facebook'] ?? '',
            'twitter'         => $input['twitter'] ?? '',
            'linkedin'        => $input['linkedin'] ?? '',
            'instagram'       => $input['instagram'] ?? '',
            'mother_name'     => $input['mother_name'] ?? '',
            'father_name'     => $input['father_name'] ?? '',
            'is_invisibles_user' => $input['is_invisible_user'] ?? 0
        );

        // Dates
        if (!empty($input['date_of_joining'])) {
            $data_update['date_of_joining'] = date('Y-m-d', $this->customlib->datetostrtotime($input['date_of_joining']));
        }
        if (!empty($input['date_of_leaving'])) {
            $data_update['date_of_leaving'] = date('Y-m-d', $this->customlib->datetostrtotime($input['date_of_leaving']));
        }

        // Department & Designation (FIXED BUG)
        if (!empty($input['department'])) {
            $data_update['department'] = $input['department'];
        }
        if (!empty($input['designation'])) {
            $data_update['designation'] = $input['designation'];
        }

        // 🔹 Image Upload
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $img_name = $this->media_storage->fileupload("file", "../uploads/staff_images/");
            $data_update['image'] = $img_name;

            if (!empty($staff['image'])) {
                $this->media_storage->filedelete($staff['image'], "uploads/staff_images");
            }
        }

        // 🔹 Update Staff
        $this->staff_model->add($data_update);

        // 🔹 Update Role
        $this->staff_model->update_role([
            'staff_id' => $id,
            'role_id'  => $input['role']
        ]);

        // 🔹 Leave Update
        $leave_type     = $input['leave_type_id'] ?? [];
        $alloted_leave  = $input['alloted_leave'] ?? [];
        $altid          = $input['altid'] ?? [];

        foreach ($leave_type as $k => $lt_id) {
            $l_data = [
                'staff_id'      => $id,
                'leave_type_id' => $lt_id,
                'alloted_leave' => $alloted_leave[$k] ?? 0
            ];
            if (!empty($altid[$k])) {
                $l_data['id'] = $altid[$k];
            }
            $this->staff_model->add_staff_leave_details($l_data);
        }

        // 🔹 Custom Fields
        if (isset($input['custom_fields'])) {
            $cf_update = [];
            foreach ($input['custom_fields'] as $cf_id => $cf_val) {
                $cf_update[] = [
                    'belong_table_id' => $id,
                    'custom_field_id' => $cf_id,
                    'field_value'     => $cf_val
                ];
            }
            $this->customfield_model->updateRecord($cf_update, $id, 'staff');
        }

        // 🔹 Documents Upload
        $upload_dir = '../uploads/staff_documents/' . $id . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $doc_update = ['id' => $id];

        if (!empty($_FILES['first_doc']['name'])) {
            $doc_update['resume'] = $this->media_storage->fileupload("first_doc", $upload_dir);
        }
        if (!empty($_FILES['second_doc']['name'])) {
            $doc_update['joining_letter'] = $this->media_storage->fileupload("second_doc", $upload_dir);
        }
        if (!empty($_FILES['third_doc']['name'])) {
            $doc_update['resignation_letter'] = $this->media_storage->fileupload("third_doc", $upload_dir);
        }
        if (!empty($_FILES['fourth_doc']['name'])) {
            $doc_update['other_document_file'] = $this->media_storage->fileupload("fourth_doc", $upload_dir);
        }

        if (count($doc_update) > 1) {
            $this->staff_model->add($doc_update);
        }

        return $this->output->set_status_header(200)->set_output(json_encode([
            'status'  => true,
            'message' => $this->lang->line('update_message')
        ]));
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->staff_model->remove($id);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Staff deleted successfully']));
    }

    public function disablestaff($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = $this->_get_input();
        $date = $input['date'] ?? date('Y-m-d');
        $this->staff_model->disablestaff(array('id' => $id, 'disable_at' => date('Y-m-d', $this->customlib->datetostrtotime($date)), 'is_active' => 0));
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Staff disabled successfully']));
    }

    public function enablestaff($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->staff_model->enablestaff($id);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Staff enabled successfully']));
    }

    public function staffLeaveSummary()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $resultdata = $this->staff_model->getLeaveSummary();
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $resultdata]));
    }

    public function getEmployeeByRole()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = $this->_get_input();
        $role = $input['role'] ?? null;
        $data = $this->staff_model->getEmployee($role);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $data]));
    }

    public function leaverequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $leave_request = $this->leaverequest_model->staff_leave_request();
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $leave_request]));
    }

    public function change_password($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = $this->_get_input();
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('new_pass', $this->lang->line('new_password'), 'trim|required|xss_clean|min_length[6]');
        $this->form_validation->set_rules('confirm_pass', $this->lang->line('confirm_password'), 'trim|required|xss_clean|matches[new_pass]');

        if ($this->form_validation->run() == false) {
            $errors = array('new_pass' => form_error('new_pass'), 'confirm_pass' => form_error('confirm_pass'));
            return $this->output->set_status_header(422)->set_output(json_encode(['status' => false, 'errors' => $errors]));
        } else {
            $this->admin_model->saveNewPass(array('id' => $id, 'password' => $this->enc_lib->passHashEnc($input['new_pass'])));
            return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => $this->lang->line('password_changed_successfully')]));
        }
    }

    public function rating()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $all_rating = $this->staff_model->all_rating();
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $all_rating]));
    }

    public function ratingapr($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->staff_model->ratingapr($id, ['status' => '1']);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Rating approved']));
    }

    public function delete_rateing($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->staff_model->rating_remove($id);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Rating deleted']));
    }

    public function recruitment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = $this->_get_input();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array(
                'name' => $input['name'] ?? '',
                'position' => $input['position'] ?? '',
                'openings' => $input['no_of_openings'] ?? '',
                'description' => $input['description'] ?? '',
                'status' => $input['status'] ?? '',
            );
            $this->staff_model->insert_recruitment($data);
            return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Recruitment added successfully']));
        }
        $recruitmentresult = $this->staff_model->getstaffrecruitment();
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $recruitmentresult]));
    }

    public function recruitment_delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->staff_model->delete_recruitment($id);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Recruitment deleted']));
    }

    public function gallery()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = $this->_get_input();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_name = $input['category_name'] ?? '';
            if (!empty($category_name)) {
                $this->admin_model->add_category(['category_name' => $category_name]);
                return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Category added']));
            }
        }
        $g_categories = $this->admin_model->get_categories();
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $g_categories]));
    }

    public function g_category_update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = $this->_get_input();
        $this->admin_model->update_category($input['category_id'], ['category_name' => $input['category_name']]);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Category updated']));
    }

    public function g_category_delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->admin_model->delete_g_category($id);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Category deleted']));
    }

    public function image_delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->admin_model->delete_image($id);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Image deleted']));
    }

    public function gallery_list($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $gallery_list = $this->admin_model->get_gallery_by_category($id);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => ['category_id' => $id, 'gallery_list' => $gallery_list]]));
    }

    public function add_gallery_image()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $category_id = $this->input->post('category_id');
        $name = $this->input->post('name');
        $upload_dir = './uploads/gallery/' . $category_id . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $image_path = (isset($_FILES["category_image"]) && !empty($_FILES['category_image']['name'])) ? $this->media_storage->fileupload("category_image", $upload_dir) : '';
        if (!empty($image_path)) {
            $this->admin_model->insert_gallery_image(['category_id' => $category_id, 'name' => $name, 'image' => $image_path, 'created_at' => date('Y-m-d H:i:s')]);
            return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Image uploaded']));
        }
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => false, 'message' => 'Upload failed']));
    }

    // Callback helpers for validation (needed to stay as public methods if used by form_validation, though we set data manually)
    public function handle_upload()
    {
        return true;
    }
    public function handle_first_upload()
    {
        return true;
    }
    public function handle_second_upload()
    {
        return true;
    }
    public function handle_third_upload()
    {
        return true;
    }
    public function handle_fourth_upload()
    {
        return true;
    }
    public function username_check($str)
    {
        return true;
    }

    /*
     * Legacy methods kept for completeness as per user request (not removing anything).
     * These are now commented out or minimally refactored to prevent execution if they use views.
     */

    public function permission($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = $this->_get_input();
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $staff_id   = $input['staff_id'];
            $prev_array = $input['prev_array'] ?? [];
            $module_perm = $input['module_perm'] ?? [];
            $delete_array = array_diff($prev_array, $module_perm);
            $insert_diff  = array_diff($module_perm, $prev_array);
            $insert_array = [];
            foreach ($insert_diff as $value) {
                $insert_array[] = array('staff_id' => $staff_id, 'permission_id' => $value);
            }
            $this->userpermission_model->getInsertBatch($insert_array, $staff_id, $delete_array);
            return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => 'Permissions updated']));
        }
        $userpermission = $this->userpermission_model->getUserPermission($id);
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $userpermission]));
    }

    public function import()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // -----------------------------
        // ✅ GET → RETURN MASTER DATA
        // -----------------------------
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $roles       = $this->role_model->get();
            $designation = $this->staff_model->getStaffDesignation();
            $department  = $this->staff_model->getDepartment();

            return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'data'   => [
                        'roles'       => $roles,
                        'designation' => $designation,
                        'department'  => $department
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
        // ✅ POST → IMPORT CSV
        // -----------------------------
        $role        = $this->input->post('role');
        $designation = $this->input->post('designation');
        $department  = $this->input->post('department');

        if (empty($role)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => ['role' => 'Role is required']
                ]));
        }

        if (empty($_FILES['file']['name'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => ['file' => 'CSV file is required']
                ]));
        }

        // -----------------------------
        // ✅ FILE CHECK
        // -----------------------------
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if ($ext !== 'csv') {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Only CSV file allowed'
                ]));
        }

        // -----------------------------
        // ✅ READ CSV
        // -----------------------------
        $file = $_FILES['file']['tmp_name'];
        $this->load->library('CSVReader');
        $result = $this->csvreader->parse_file($file);

        // echo "<Pre>";print_r($result);exit;

        $imported = 0;
        $skipped  = 0;

        if (!empty($result)) {

            foreach ($result as $row) {

                $check_exists = $this->staff_model->import_check_data_exists($row['name'], $row['employee_id']);
                $check_email  = $this->staff_model->import_check_email_exists($row['name'], $row['email']);

                if ($check_exists == 0 && $check_email == 0) {

                    foreach ($row as $key => $value) {
                        $row[$key] = $this->encoding_lib->toUTF8($value);
                    }

                    $row['user_id']    = $role;
                    $row['designation'] = $designation;
                    $row['department'] = $department;
                    $row['is_active']  = 1;

                    $password = $this->role->get_random_password(6, 6, false, true, false);
                    $row['password'] = $this->enc_lib->passHashEnc($password);

                    $role_array = [
                        'role_id'  => $role,
                        'staff_id' => 0
                    ];

                    // echo "<pre>";
                    // print_r($row);exit;

                    $insert_id = $this->staff_model->batchInsert($row, $role_array);

                    if ($insert_id) {
                        $login = [
                            'id'             => $insert_id,
                            'credential_for' => 'staff',
                            'username'       => $row['email'],
                            'password'       => $password,
                            'contact_no'     => $row['contact_no'],
                            'email'          => $row['email']
                        ];

                        // $this->mailsmsconf->mailsms('login_credential', $login);
                        $imported++;
                    }
                } else {
                    $skipped++;
                }
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Import completed successfully',
                'summary' => [
                    'total'     => count($result),
                    'imported'  => $imported,
                    'skipped'   => $skipped
                ]
            ]));
    }

    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/staff_csvfile.csv";
        $data = file_get_contents($filepath);
        force_download('staff_csvfile.csv', $data);
    }
}

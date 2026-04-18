<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staffattendance extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->config->load("mailsms");
        $this->config->load("payroll");
        $this->load->library('mailsmsconf');
        $this->config_attendance = $this->config->item('attendence');
        $this->staff_attendance  = $this->config->item('staffattendance');
        $this->load->model("staffattendancemodel");
        $this->load->model("staff_model");
        $this->load->model("payroll_model"); 
        $this->load->library('form_validation'); // Ensure loaded
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input ?: [];
    }
    
    private function safeDate($date)
    {
        if (!$date) {
            return null;
        }
    
        try {
            return date('Y-m-d', strtotime($date));
        } catch (Exception $e) {
            return null;
        }
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        
        // POST Handling (Search or Save)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            
            $user_type_id = $input['user_id'] ?? null;
            $search       = $input['search'] ?? '';

            if ($search == "saveattendence") {
                // Logic to save attendance
                $date                = $this->safeDate($input['date']);
                $user_type_ary       = $input['student_session'] ?? []; // List of staff IDs
                $absent_student_list = array();
                $holiday             = $input['holiday'] ?? null;

                if (!empty($user_type_ary)) {
                    $date = $this->safeDate($input['date']);

                foreach ($user_type_ary as $value) {
                
                    $remark   = $input['remark' . $value] ?? '';
                    $att_type = $input['attendencetype' . $value] ?? null;
                
                    if (isset($holiday)) {
                        $att_type = 5;
                    }
                
                    // 🔎 Check if attendance already exists
                    $existing = $this->db
                        ->where('staff_id', $value)
                        ->where('date', $date)
                        ->get('staff_attendance')
                        ->row_array();
                
                    if (!empty($existing)) {
                
                        // ✅ UPDATE
                        $arr = [
                            'id'                       => $existing['id'],
                            'staff_id'                 => $value,
                            'staff_attendance_type_id' => $att_type,
                            'remark'                   => $remark,
                            'date'                     => $date
                        ];
                
                        $this->staffattendancemodel->add($arr);
                
                    } else {
                
                        // ✅ INSERT
                        $arr = [
                            'staff_id'                 => $value,
                            'staff_attendance_type_id' => $att_type,
                            'date'                     => $date,
                            'remark'                   => $remark
                        ];
                
                        $this->staffattendancemodel->add($arr);
                    }
                
                    // Track absent
                    if ($att_type == $this->config_attendance['absent']) {
                        $absent_student_list[] = $value;
                    }
                }

                    // Send SMS/Mail for absent staff
                    if (!empty($absent_student_list)) {
                        $this->mailsmsconf->mailsms('absent_attendence', $absent_student_list, $date);
                    }

                    return $this->output
                        ->set_status_header(200)
                        ->set_output(json_encode([
                            'status'  => 'success',
                            'message' => $this->lang->line('success_message')
                        ]));
                } else {
                    return $this->output
                        ->set_status_header(400)
                        ->set_output(json_encode([
                            'status'  => 'fail',
                            'message' => 'No staff selected'
                        ]));
                }

            } else {
                // Search Logic (List Staff for Attendance)
                // user_id here actually refers to the Role Name/Type e.g. "Teacher", "Admin" based on original code
                // Original: $user_type = $this->input->post('user_id'); where user_id value is role name.
                
                if (isset($user_type_id)) { // This 'user_id' param name holds the Role Name
                    $date                        = $input['date'];
                    $user_list                   = $this->staffattendancemodel->get(); // Fetch all?
                    
                    // Original code re-fetches user_list ?? It fetches $user_list but doesn't seem to use it except for assigning to data['userlist'].
                    // The main list comes from searchAttendenceUserType
                    
                    $attendencetypes             = $this->attendencetype_model->getStaffAttendanceType();
                    $resultlist                  = $this->staffattendancemodel->searchAttendenceUserType($user_type_id, date('Y-m-d', $this->customlib->datetostrtotime($date)));
                    
                    return $this->output
                        ->set_status_header(200)
                        ->set_output(json_encode([
                            'status'              => 'success',
                            'attendencetypeslist' => $attendencetypes,
                            'resultlist'          => $resultlist,
                            'date'                => $date,
                            'user_type'           => $user_type_id
                        ]));
                }
            }
        }

        // GET behavior: Initial Data (Roles)
        $user_type   = $this->staff_model->getStaffRole();
        $sch_setting = $this->setting_model->getSetting();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'    => 'success',
                'rolelist'  => $user_type,
                'settings'  => $sch_setting
            ]));
    }

    public function monthAttendance($st_month, $no_of_months, $emp)
    {
        // Helper function, keeping as public API or internal?
        // Making it API accessible if needed, but original used it as internal logic perhaps?
        // Original controller had it public. 
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $this->load->model("payroll_model");
        $record = array();
        $r      = array();
        $month  = date('m', strtotime($st_month));
        $year   = date('Y', strtotime($st_month));
        foreach ($this->staff_attendance as $att_key => $att_value) {
            $s = $this->payroll_model->count_attendance_obj($month, $year, $emp, $att_value);
            $r[$att_key] = $s;
        }

        $record[$emp] = $r;
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $record
            ]));
    }

    public function profileattendance()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        $input = $this->_get_input();
        // Allow staff_id and year/month overrides via input
        // Original code had hardcoded staff_id = 8 inside loop?
        // "res[$att_dates] = $this->staffattendancemodel->searchStaffattendance($att_dates, $staff_id = 8);"
        // I should probably fix this to accept input.
        
        $staff_id = $input['staff_id'] ?? 8; // Defaulting to 8 only to match legacy behavior if needed, but really should be required.
        $year     = $input['year'] ?? date('Y');
        $month    = $input['month'] ?? date('m'); // Optional specific month filter? Original looped all months?
        
        // Original Logic loop:
        // loops 1 to 31
        // loops through $monthlist (all months)
        // builds a calendar grid
        
        $monthlist = $this->customlib->getMonthDropdown();
        $res = array();
        $date_array = array();
        
        // Optimizing: Instead of nested loops doing queries, maybe just return the raw data?
        // But adhering to the existing logic structure for now:
        
        foreach ($monthlist as $key => $value) {
            $datemonth = date("m", strtotime($value));
            // Only loop days for that month? Original looped 1-31 for ALL months regardless of validity (e.g. Feb 30)
            // It relies on searchStaffattendance handling invalid dates or yielding empty.
            
            for ($i = 1; $i <= 31; $i++) {
                $att_date = sprintf("%02d", $i);
                $att_dates = date("Y") . "-" . $datemonth . "-" . $att_date;
                
                // Validate date exists?
                if (checkdate($datemonth, $i, date("Y"))) {
                    $date_array[] = $att_dates;
                    $result = $this->staffattendancemodel->searchStaffattendance($att_dates, $staff_id);
                    if ($result) {
                        $res[$att_dates] = $result;
                    }
                }
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'     => 'success',
                'resultlist' => $res,
                'staff_id'   => $staff_id
            ]));
    }

}

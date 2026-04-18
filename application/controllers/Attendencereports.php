<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Attendencereports extends Public_Controller
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
        $this->load->model('late_entries_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->search_type        = $this->customlib->get_searchtype();
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input ?: [];
    }

    public function classattendencereport()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $attendencetypes = $this->attendencetype_model->getAttType();
        $setting_data    = $this->setting_model->get();
        $class           = $this->class_model->get();
        $userdata        = $this->customlib->getUserData();

        $role_id = $userdata["role_id"] ?? null;

        if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
            if ($userdata["class_teacher"] == 'yes') {
                $class = $this->teacher_model->get_daywiseattendanceclass($userdata["id"]);
            }
        }

        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('month', $this->lang->line('month'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(empty($input) ? 200 : 422)
                ->set_output(json_encode([
                    'status'                => empty($input) ? 'success' : 'fail',
                    'errors'                => $this->form_validation->error_array(),
                    'attendencetypeslist'   => $attendencetypes,
                    'low_attendance_limit'  => $setting_data[0]['low_attendance_limit'] ?? null,
                    'classlist'             => $class,
                    'monthlist'             => $this->customlib->getMonthDropdown(),
                    'yearlist'              => $this->stuattendence_model->attendanceYearCount(),
                ]));
        } else {
            $class_id       = $input['class_id'];
            $section_id     = $input['section_id'];
            $month          = $input['month'];
            $year           = $input['year'] ?? null;

            $studentlist     = $this->student_model->searchByClassSection($class_id, $section_id);
            $session_current = $this->setting_model->getCurrentSessionName();
            $startMonth      = $this->setting_model->getStartMonth();
            $centenary       = substr($session_current, 0, 2);
            $year_first_substring  = substr($session_current, 2, 2);
            $year_second_substring = substr($session_current, 5, 2);
            $month_number    = date("m", strtotime($month));

            if (!empty($year)) {
                $year = $input["year"];
            } else {
                if ($month_number >= $startMonth && $month_number <= 12) {
                    $year = $centenary . $year_first_substring;
                } else {
                    $year = $centenary . $year_second_substring;
                }
            }

            $num_of_days      = cal_days_in_month(CAL_GREGORIAN, $month_number, $year);
            $attendence_array = array();
            $student_result   = array();
            $date_result      = array();

            for ($i = 1; $i <= $num_of_days; $i++) {
                $att_date           = $year . "-" . $month_number . "-" . sprintf("%02d", $i);
                $attendence_array[] = $att_date;

                $res            = $this->stuattendence_model->searchAttendenceReport($class_id, $section_id, $att_date);
                $student_result = $res;
                $s              = array();
                foreach ($res as $result_k => $result_v) {
                    $s[$result_v['student_session_id']] = $result_v;
                }
                $date_result[$att_date] = $s;
            }

            $monthAttendance = array();
            foreach ($res as $result_k => $result_v) {
                $date              = $year . "-" . $month;
                $newdate           = date('Y-m-d', strtotime($date));
                $monthAttendance[] = $this->stuMonthAttendance($newdate, 1, $result_v['student_session_id']);
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'            => 'success',
                    'class_id'          => $class_id,
                    'section_id'        => $section_id,
                    'month_selected'    => $month,
                    'year_selected'     => $year,
                    'no_of_days'        => $num_of_days,
                    'monthAttendance'   => $monthAttendance,
                    'resultlist'        => $date_result,
                    'attendence_array'  => $attendence_array,
                    'student_array'     => $student_result,
                ]));
        }
    }

    public function stuMonthAttendance($st_month, $no_of_months, $student_id)
    {
        $record = array();
        $r      = array();
        $month  = date('m', strtotime($st_month));
        $year   = date('Y', strtotime($st_month));
        foreach ($this->config_attendance as $att_key => $att_value) {
            $s = $this->stuattendence_model->count_attendance_obj($month, $year, $student_id, $att_value);
            $attendance_key = $att_key;
            $r[$attendance_key] = $s;
        }

        $record[$student_id] = $r;
        return $record;
    }

    public function attendancereport()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $class      = $input['class_id'] ?? null;
        $section    = $input['section_id'] ?? null;
        $classlist  = $this->class_model->get();

        $search_type = $input['search_type'] ?? 'this_week';
        $between_date = $this->customlib->get_betweendate($search_type);

        $from_date = date('Y-m-d', strtotime($between_date['from_date']));
        $to_date   = date('Y-m-d', strtotime($between_date['to_date']));
        $dates     = array();
        $off_date  = array();
        $current   = strtotime($from_date);
        $last      = strtotime($to_date);

        while ($current <= $last) {
            $date    = date('Y-m-d', $current);
            $day     = date("D", strtotime($date));
            $holiday = $this->stuattendence_model->checkholidatbydate($date);

            if ($day == 'Sun' || $holiday > 0) {
                $off_date[] = $date;
            } else {
                $dates[] = $date;
            }

            $current = strtotime('+1 day', $current);
        }

        $filter = date($this->customlib->getSchoolDateFormat(), strtotime($from_date)) . " To " . date($this->customlib->getSchoolDateFormat(), strtotime($to_date));
        $attendance_type = $this->attendencetype_model->getstdAttType('2');

        $this->form_validation->set_rules('attendance_type', $this->lang->line('attendance_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(empty($input) ? 200 : 422)
                ->set_output(json_encode([
                    'status'          => empty($input) ? 'success' : 'fail',
                    'errors'          => $this->form_validation->error_array(),
                    'searchlist'      => $this->search_type,
                    'classlist'       => $classlist,
                    'attendance_type' => $attendance_type,
                    'filter'          => $filter,
                ]));
        } else {
            $attendance_type_id = $input['attendance_type'];
            $condition = " and `student_attendences`.`attendence_type_id`=" . $attendance_type_id;

            if ($class != '') {
                $condition .= ' and class_id=' . $class;
            }
            $condition .= " and date_format(student_attendences.date,'%Y-%m-%d') between '" . $from_date . "' and '" . $to_date . "'";
            if ($section != '') {
                $condition .= ' and section_id=' . $section;
            }

            $student_attendences = $this->stuattendence_model->student_attendences($condition, "");

            $attd = array();
            foreach ($student_attendences as $value) {
                $std_id          = $value['id'];
                $attd[$std_id][] = $value;
            }

            $fdata = [];
            foreach ($attd as $key => $att_value) {
                $all_week = 1;
                foreach ($att_value as $value) {
                    if (in_array($value['date'], $off_date)) {
                        // Skip
                    } else {
                        if (in_array($value['date'], $dates)) {
                            // Match found
                        } else {
                            $all_week = 0;
                        }
                    }
                }
                if ($all_week == 1) {
                    $fdata[] = $att_value[0];
                }
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'              => 'success',
                    'class_id'            => $class,
                    'section_id'          => $section,
                    'attendance_type_id'  => $attendance_type_id,
                    'student_attendences' => $student_attendences,
                    'filter'              => $filter,
                    'filtered_data'       => $fdata,
                ]));
        }
    }

    public function daily_attendance_report()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $date = " and student_attendences.date='" . date('Y-m-d') . "'";
            $display_date = date($this->customlib->getSchoolDateFormat());
        } else {
            $date = " and student_attendences.date='" . date('Y-m-d', $this->customlib->datetostrtotime($input['date'])) . "'";
            $display_date = date($this->customlib->getSchoolDateFormat(), $this->customlib->datetostrtotime($input['date']));
        }

        $result = $this->stuattendence_model->get_attendancebydate($date);
        $resultlist = [];
        $all_student = $all_present = $all_absent = 0;

        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $total_present = $value->present + $value->excuse + $value->late + $value->half_day;
                $total_student = $total_present + $value->absent;
                $presnt_percent = $total_present > 0 ? round(($total_present / $total_student) * 100) : 0;
                $presnt_absent = $value->absent > 0 ? round(($value->absent / $total_student) * 100) : 0;
                
                $all_student += $total_student;
                $all_present += $total_present;
                $all_absent += $value->absent;

                $resultlist[] = array(
                    'class_section'   => $value->class_name . " (" . $value->section_name . ")",
                    'total_present'   => $total_present,
                    'total_absent'    => $value->absent,
                    'present_percent' => $presnt_percent . "%",
                    'absent_persent'  => $presnt_absent . "%"
                );
            }
        }

        $all_present_percent = $all_student > 0 ? round(($all_present / $all_student) * 100) . "%" : "0%";
        $all_absent_percent  = $all_student > 0 ? round(($all_absent / $all_student) * 100) . "%" : "0%";

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'              => 'success',
                'date'                => $display_date,
                'resultlist'          => $resultlist,
                'all_student'         => $all_student,
                'all_present'         => $all_present,
                'all_absent'          => $all_absent,
                'all_present_percent' => $all_present_percent,
                'all_absent_percent'  => $all_absent_percent,
            ]));
    }

    public function staffattendancereport()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $attendencetypes = $this->staffattendancemodel->getStaffAttendanceType();
        $staffRole       = $this->staff_model->getStaffRole();

        $this->form_validation->set_rules('month', $this->lang->line('month'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(empty($input) ? 200 : 422)
                ->set_output(json_encode([
                    'status'              => empty($input) ? 'success' : 'fail',
                    'errors'              => $this->form_validation->error_array(),
                    'attendencetypeslist' => $attendencetypes,
                    'role'                => $staffRole,
                    'monthlist'           => $this->customlib->getMonthDropdown(),
                    'yearlist'            => $this->staffattendancemodel->attendanceYearCount(),
                ]));
        } else {
            $month      = $input['month'];
            $searchyear = $input['year'] ?? null;
            $role       = $input["role"] ?? null;

            $stafflist       = $this->staff_model->getEmployee($role);
            $session_current = $this->setting_model->getCurrentSessionName();
            $startMonth      = $this->setting_model->getStartMonth();
            $centenary       = substr($session_current, 0, 2);
            $year_first_substring  = substr($session_current, 2, 2);
            $year_second_substring = substr($session_current, 5, 2);
            $month_number    = date("m", strtotime($month));

            if ($month_number >= $startMonth && $month_number <= 12) {
                $year = $centenary . $year_first_substring;
            } else {
                $year = $centenary . $year_second_substring;
            }

            $num_of_days      = cal_days_in_month(CAL_GREGORIAN, $month_number, $searchyear);
            $attendence_array = array();
            $student_result   = array();
            $date_result      = array();
            $monthAttendance  = array();

            for ($i = 1; $i <= $num_of_days; $i++) {
                $att_date           = $searchyear . "-" . $month_number . "-" . sprintf("%02d", $i);
                $attendence_array[] = $att_date;

                $res = $this->staffattendancemodel->searchAttendanceReport($role, $att_date);
                $student_result = $res;
                $s = array();

                foreach ($res as $result_k => $result_v) {
                    $s[$result_v['id']] = $result_v;
                }

                $date_result[$att_date] = $s;
            }

            foreach ($res as $result_k => $result_v) {
                $date              = $searchyear . "-" . $month;
                $newdate           = date('Y-m-d', strtotime($date));
                $monthAttendance[] = $this->monthAttendance($newdate, 1, $result_v['id']);
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'           => 'success',
                    'month_selected'   => $month,
                    'role_selected'    => $role,
                    'no_of_days'       => $num_of_days,
                    'monthAttendance'  => $monthAttendance,
                    'resultlist'       => $date_result,
                    'attendence_array' => !empty($searchyear) ? $attendence_array : [],
                    'student_array'    => !empty($searchyear) ? $student_result : [],
                ]));
        }
    }

    public function monthAttendance($st_month, $no_of_months, $emp)
    {
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
        return $record;
    }

    public function late_entries_report()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $class = $this->class_model->get();

        $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(empty($input) ? 200 : 422)
                ->set_output(json_encode([
                    'status'     => empty($input) ? 'success' : 'fail',
                    'errors'     => $this->form_validation->error_array(),
                    'classlist'  => $class,
                ]));
        } else {
            $date_from  = $input['date_from'];
            $date_to    = $input['date_to'];
            $class_id   = $input['class_id'] ?? null;
            $section_id = $input['section_id'] ?? null;
            $student_id = $input['student_id'] ?? null;

            $start_date = date('Y-m-d', $this->customlib->datetostrtotime($date_from));
            $end_date   = date('Y-m-d', $this->customlib->datetostrtotime($date_to));

            $late_entries = $this->late_entries_model->get_late_entries_beetweenDate($start_date, $end_date, $class_id, $section_id, $student_id);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'       => 'success',
                    'class_id'     => $class_id,
                    'section_id'   => $section_id,
                    'student_id'   => $student_id,
                    'date_from'    => $date_from,
                    'date_to'      => $date_to,
                    'late_entries' => $late_entries,
                ]));
        }
    }

    public function late_entries_analysis()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $total_late_students = 0;
        $tot_late = $this->late_entries_model->getTotalLateStudents();
        
        if (!empty($tot_late)) {
            $total_late_students = $tot_late->total_late_students;
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'              => 'success',
                'total_late_students' => $total_late_students,
            ]));
    }
}

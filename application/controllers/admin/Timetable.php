<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Timetable extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("staff_model");
        $this->load->model("classteacher_model");
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

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        $class             = $this->class_model->get();
        $data['classlist'] = $class;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('group_id', $this->lang->line('subject_group'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $class_id           = $input['class_id'];
                $section_id         = $input['section_id'];
                $group_id           = $input['group_id'];
                $result_subjects    = $this->teachersubject_model->getSubjectByClsandSection($class_id, $section_id);

                $getDaysnameList         = $this->customlib->getDaysname();
                $final_array             = array();
                if (!empty($result_subjects)) {
                    foreach ($result_subjects as $subject_k => $subject_v) {
                        $result_array = array();
                        foreach ($getDaysnameList as $day_key => $day_value) {
                            $where_array = array(
                                'teacher_subject_id' => $subject_v['id'],
                                'day_name'           => $day_value,
                            );
                            $result = $this->timetable_model->get($where_array);
                            if (!empty($result)) {
                                $obj                      = new stdClass();
                                $obj->status              = "Yes";
                                $obj->start_time          = $result[0]['start_time'];
                                $obj->end_time            = $result[0]['end_time'];
                                $obj->room_no             = $result[0]['room_no'];
                                $result_array[$day_value] = $obj;
                            } else {
                                $obj                      = new stdClass();
                                $obj->status              = "No";
                                $obj->start_time          = "N/A";
                                $obj->end_time            = "N/A";
                                $obj->room_no             = "N/A";
                                $result_array[$day_value] = $obj;
                            }
                        }
                        $final_array[$subject_v['name']] = $result_array;
                    }
                }

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'          => 'success',
                        'getDaysnameList' => $getDaysnameList,
                        'result_array'    => $final_array
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'    => 'success',
                'classlist' => $class
            ]));
    }

    public function mytimetable()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $my_role  = $this->customlib->getStaffRole();
        $role     = json_decode($my_role);
        $is_admin = false;

        $staff_list = [];
        if ($role->id != "2") {
            $staff_list = $this->staff_model->getEmployee('2');
            $is_admin   = true;
        }

        $staff_id          = $this->customlib->getStaffID();
        $timetable         = array();
        $days              = $this->customlib->getDaysname();

        foreach ($days as $day_key => $day_value) {
            $timetable[$day_value] = $this->subjecttimetable_model->getByStaffandDay($staff_id, $day_key);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'     => 'success',
                'is_admin'   => $is_admin,
                'staff_list' => $staff_list,
                'timetable'  => $timetable
            ]));
    }

    public function view($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
        }

        $mark = $this->mark_model->get($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'mark'   => $mark
            ]));
    }

    public function delete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!$id) {
            $input = $this->_get_input();
            $id = $input['id'] ?? null;
        }

        if ($id) {
            $this->mark_model->remove($id);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('delete_message')
                ]));
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        
        $exam              = $this->exam_model->get();
        $class             = $this->class_model->get('', $classteacher = 'yes');
        $staff             = $this->staff_model->getStaffbyrole(2);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('subject_group_id', $this->lang->line('subject_group'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $subject_group_id = $input['subject_group_id'];
                $subject          = $this->subjectgroup_model->getGroupsubjects($subject_group_id);
                $getDaysnameList  = $this->customlib->getDaysname();
                
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'          => 'success',
                        'getDaysnameList' => $getDaysnameList,
                        'subject'         => $subject
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'    => 'success',
                'examlist'  => $exam,
                'classlist' => $class,
                'staff'     => $staff
            ]));
    }
    
    
    
    public function get_classreport()
    {
        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow Only GET Request
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    "status"  => false,
                    "message" => "Method Not Allowed"
                ]));
        }
    
        // ---------------- BASIC DATA ----------------
    
        $session = $this->setting_model->getCurrentSession();
    
        // Exam List
        $examlist = $this->exam_model->get();
    
        // Class List (Only class teacher allowed)
        $classlist = $this->class_model->get('', $classteacher = 'yes');
    
        // Staff List (Role = 2 → Teacher)
        $stafflist = $this->staff_model->getStaffbyrole(2);
    
        // Empty Subject Array
        $subject = [];
    
        // ---------------- FINAL API RESPONSE ----------------
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => true,
                "data"   => [
                    "session"    => $session,
                    "examlist"   => $examlist,
                    "classlist"  => $classlist,
                    "stafflist"  => $stafflist,
                    "subject"    => $subject
                ]
            ]));
    }

    

    public function classreport()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $class = $this->class_model->get('', $classteacher = 'yes');
        $exam  = $this->exam_model->get();
        $staff = $this->staff_model->getStaffbyrole(2);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == true) {
                $class_id    = $input['class_id'];
                $section_id  = $input['section_id'];
                $days        = $this->customlib->getDaysname();
                $days_record = array();
                foreach ($days as $day_key => $day_value) {
                    $days_record[$day_key] = $this->subjecttimetable_model->getSubjectByClassandSectionDay($class_id, $section_id, $day_key);
                }

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'    => 'success',
                        'timetable' => $days_record
                    ]));
            } else {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'    => 'success',
                'classlist' => $class,
                'examlist'  => $exam,
                'staff'     => $staff
            ]));
    }

    public function edit($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $id = $id ?: ($input['id'] ?? null);

        if (!$id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
        }

        $mark = $this->mark_model->get($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('name', $this->lang->line('mark'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                 return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $data = array(
                    'id'   => $id,
                    'name' => $input['name'],
                    'note' => $input['note'] ?? '',
                );
                $this->mark_model->add($data);
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('success_message')
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'mark'   => $mark
            ]));
    }

    public function getBydategroupclasssection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input            = $this->_get_input();
        $day              = $input['day'] ?? null;
        $class_id         = $input['class_id'] ?? null;
        $section_id       = $input['section_id'] ?? null;
        $subject_group_id = $input['subject_group_id'] ?? null;

        if (!$day || !$class_id || !$section_id || !$subject_group_id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Required parameters missing']));
        }

        $subject     = $this->subjectgroup_model->getGroupsubjects($subject_group_id);
        $prev_record = $this->subjecttimetable_model->getBySubjectGroupDayClassSection($subject_group_id, $day, $class_id, $section_id);
        $staff       = $this->staff_model->getStaffbyrole(2);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'           => 'success',
                'staff'            => $staff,
                'subject'          => $subject,
                'prev_record'      => $prev_record,
                'total_count'      => count($prev_record) ?: 1,
                'day'              => $day,
                'class_id'         => $class_id,
                'section_id'       => $section_id,
                'subject_group_id' => $subject_group_id
            ]));
    }

    public function savegroup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('subject_group_id', $this->lang->line('subject_group'), 'trim|required');
        $this->form_validation->set_rules('day', $this->lang->line('day'), 'trim|required');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required');
        
        $total_rows = $input['total_row'] ?? null;
        if (!isset($total_rows)) {
            $this->form_validation->set_rules('rows', 'class_timetable', 'trim|required', array('required' => $this->lang->line('you_have_not_selected_any_row')));
        }
        
        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $key => $value) {
                $this->form_validation->set_rules('subject_' . $value, 'Subject', 'trim|required');
                $this->form_validation->set_rules('staff_' . $value, 'Staff', 'trim|required');
                $this->form_validation->set_rules('time_from_' . $value, 'Time From', 'trim|required');
                $this->form_validation->set_rules('time_to_' . $value, 'Time To', 'trim|required');
                $this->form_validation->set_rules('room_no_' . $value, 'Room No', 'trim|required');
            }
        }

        if (!$this->form_validation->run()) {
            $errors = $this->form_validation->error_array();
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $errors
                ]));
        } else {
            $day              = $input['day'];
            $class_id         = $input['class_id'];
            $section_id       = $input['section_id'];
            $subject_group_id = $input['subject_group_id'];
            $total_row        = $input['total_row'];
            $session          = $this->setting_model->getCurrentSession();
            $insert_array     = array();
            $update_array     = array();
            $old_input        = array();
            $prev_array       = $input['prev_array'] ?? [];
            
            foreach ($prev_array as $prev_arr_key => $prev_arr_value) {
                $old_input[] = $prev_arr_value;
            }
            
            $preserve_array = array();
            if (isset($total_row)) {
                foreach ($total_row as $total_key => $total_value) {
                    $prev_id = $input['prev_id_' . $total_value] ?? 0;

                    if ($prev_id == 0) {
                        $insert_array[] = array(
                            'day'                      => $day,
                            'class_id'                 => $class_id,
                            'section_id'               => $section_id,
                            'subject_group_id'         => $subject_group_id,
                            'subject_group_subject_id' => $input['subject_' . $total_value],
                            'staff_id'                 => $input['staff_' . $total_value],
                            'time_from'                => $input['time_from_' . $total_value],
                            'time_to'                  => $input['time_to_' . $total_value],
                            'start_time'               => $this->customlib->timeFormat($input['time_from_' . $total_value], true),
                            'end_time'                 => $this->customlib->timeFormat($input['time_to_' . $total_value], true),
                            'room_no'                  => $input['room_no_' . $total_value],
                            'session_id'               => $session,
                        );
                    } else {
                        $preserve_array[] = $prev_id;
                        $update_array[]   = array(
                            'id'                       => $prev_id,
                            'day'                      => $day,
                            'class_id'                 => $class_id,
                            'section_id'               => $section_id,
                            'subject_group_id'         => $subject_group_id,
                            'subject_group_subject_id' => $input['subject_' . $total_value],
                            'staff_id'                 => $input['staff_' . $total_value],
                            'time_from'                => $input['time_from_' . $total_value],
                            'time_to'                  => $input['time_to_' . $total_value],
                            'start_time'               => $this->customlib->timeFormat($input['time_from_' . $total_value], true),
                            'end_time'                 => $this->customlib->timeFormat($input['time_to_' . $total_value], true),
                            'room_no'                  => $input['room_no_' . $total_value],
                            'session_id'               => $session,
                        );
                    }
                }
            }

            $delete_array = array_diff($old_input, $preserve_array);
            $result       = $this->subjecttimetable_model->add($delete_array, $insert_array, $update_array);
            if ($result) {
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('success_message')
                    ]));
            } else {
                return $this->output
                    ->set_status_header(500)
                    ->set_output(json_encode([
                        'status'  => 'fail',
                        'message' => $this->lang->line('something_went_wrong')
                    ]));
            }
        }
    }

    public function getteachertimetable()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }
        
        $this->form_validation->set_rules('teacher', $this->lang->line('teacher'), 'trim|required');

        if (!$this->form_validation->run()) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {
            $staff_id          = $input['teacher'];
            $timetable         = array();
            $days              = $this->customlib->getDaysname();

            foreach ($days as $day_key => $day_value) {
                $timetable[$day_value] = $this->subjecttimetable_model->getByStaffandDay($staff_id, $day_key);
            }

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'    => 'success',
                    'timetable' => $timetable
                ]));
        }
    }
    
    
    public function subjectgroups()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Method Not Allowed"
                ]));
        }
    
        $input = json_decode(file_get_contents("php://input"), true);
    
        if (empty($input)) {
            $input = $this->input->post();
        }
    
        $class_id   = $input['class_id'] ?? null;
        $section_id = $input['section_id'] ?? null;
    
        if (empty($class_id) || empty($section_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "class_id and section_id are required"
                ]));
        }
    
        $groups = $this->subjectgroup_model
            ->getGroupByClassandSection($class_id, $section_id);
    
        return $this->output
            ->set_content_type("application/json")
            ->set_output(json_encode([
                "status" => true,
                "data"   => $groups
            ]));
    }

}

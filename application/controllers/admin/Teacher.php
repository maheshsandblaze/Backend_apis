<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Teacher extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mailsmsconf');
        $this->load->library('form_validation');
        $this->load->model("classteacher_model");
        $this->load->model("staff_model");
        $this->load->model("teacher_model");
        $this->load->model("teachersubject_model");
        $this->load->model("subject_model");
        $this->load->model("class_model");
        $this->load->model("section_model");
        $this->load->model("classsection_model");
        $this->load->model("user_model");

        $this->current_session = $this->setting_model->getCurrentSession();
        $this->teacher_login_prefix = "teach"; // Default prefix if not defined elsewhere
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

        $teacherlist = $this->teacher_model->get();
        $genderList  = $this->customlib->getGender();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 'success',
                'teacherlist' => $teacherlist,
                'genderList'  => $genderList
            ]));
    }

    public function getSubjctByClassandSection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $class_id   = $input['class_id'] ?? null;
        $section_id = $input['section_id'] ?? null;

        if (!$class_id || !$section_id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'class_id and section_id are required']));
        }

        $data = $this->teachersubject_model->getSubjectByClsandSection($class_id, $section_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }

    public function assignteacher()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $loop = $input['i'] ?? [];
            if (empty($loop)) {
                return $this->output
                    ->set_status_header(400)
                    ->set_output(json_encode(['status' => 'fail', 'message' => 'No assignments provided (i array is empty)']));
            }

            $array = array();
            $class_id   = $input['class_id'] ?? null;
            $section_id = $input['section_id'] ?? null;

            if (!$class_id || !$section_id) {
                return $this->output
                    ->set_status_header(400)
                    ->set_output(json_encode(['status' => 'fail', 'message' => 'class_id and section_id are required']));
            }

            $dt = $this->classsection_model->getDetailbyClassSection($class_id, $section_id);
            if (!$dt) {
                return $this->output
                    ->set_status_header(404)
                    ->set_output(json_encode(['status' => 'fail', 'message' => 'Class section details not found']));
            }

            foreach ($loop as $key => $value) {
                $s = array(
                    'session_id'       => $this->current_session,
                    'class_section_id' => $dt['id'],
                    'teacher_id'       => $input['teacher_id_' . $value] ?? null,
                    'subject_id'       => $input['subject_id_' . $value] ?? null,
                );

                $row_id = $input['row_id_' . $value] ?? 0;
                if ($row_id == 0) {
                    $insert_id = $this->teachersubject_model->add($s);
                    $array[]   = $insert_id;
                } else {
                    $s['id'] = $row_id;
                    $array[] = $row_id;
                    $this->teachersubject_model->add($s);
                }
            }

            $ids              = $array;
            $class_section_id = $dt['id'];
            $this->teachersubject_model->deleteBatch($ids, $class_section_id);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message')
                ]));
        }

        // GET behavior
        $teacherlist = $this->staff_model->getStaffbyrole(2);
        $subjectlist = $this->subject_model->get();
        $classlist   = $this->class_model->get();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 'success',
                'teacherlist' => $teacherlist,
                'subjectlist' => $subjectlist,
                'classlist'   => $classlist
            ]));
    }

    public function getSubjectTeachers()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {
            $class_id   = $input['class_id'];
            $section_id = $input['section_id'];
            $dt         = $this->classsection_model->getDetailbyClassSection($class_id, $section_id);
            if (!$dt) {
                return $this->output
                    ->set_status_header(404)
                    ->set_output(json_encode(['status' => 'fail', 'message' => 'Class section not found']));
            }
            $data = $this->teachersubject_model->getDetailByclassAndSection($dt['id']);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 'success', 'data' => $data]));
        } else {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        }
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

        $teacher                = $this->teacher_model->get($id);
        $teachersubject         = $this->teachersubject_model->getTeacherClassSubjects($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'         => 'success',
                'teacher'        => $teacher,
                'teachersubject' => $teachersubject
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
            $result = $this->teacher_model->remove($id);
            if ($result) {
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('delete_message')
                    ]));
            } else {
                return $this->output
                    ->set_status_header(500)
                    ->set_output(json_encode([
                        'status'  => 'fail',
                        'message' => 'Failed to delete teacher'
                    ]));
            }
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
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('name', $this->lang->line('teacher'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', $this->lang->line('date_of_birth'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $teacher_result = $this->teacher_model->get();
            $genderList     = $this->customlib->getGender();

            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status'      => 'fail',
                    'errors'      => $this->form_validation->error_array(),
                    'teacherlist' => $teacher_result,
                    'genderList'  => $genderList
                ]));
        } else {
            $data = array(
                'name'     => $input['name'],
                'email'    => $input['email'],
                'password' => $input['password'] ?? '',
                'sex'      => $input['gender'],
                'dob'      => date('Y-m-d', $this->customlib->datetostrtotime($input['dob'])),
                'address'  => $input['address'] ?? '',
                'phone'    => $input['phone'],
                'image'    => 'uploads/student_images/no_image.png',
            );

            $insert_id = $this->teacher_model->add($data);
            if (!$insert_id) {
                return $this->output
                    ->set_status_header(500)
                    ->set_output(json_encode(['status' => 'fail', 'message' => 'Failed to create teacher']));
            }

            $user_password = $this->role->get_random_password(6, 6, false, true, false);
            $data_student_login = array(
                'username' => $this->teacher_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id'  => $insert_id,
                'role'     => 'teacher',
            );
            $this->user_model->add($data_student_login);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/teacher_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/teacher_images/' . $img_name);
                $this->teacher_model->add($data_img);
            }

            $teacher_login_detail = array(
                'id'             => $insert_id,
                'credential_for' => 'teacher',
                'username'       => $this->teacher_login_prefix . $insert_id,
                'password'       => $user_password,
                'contact_no'     => $input['phone']
            );
            $this->mailsmsconf->mailsms('login_credential', $teacher_login_detail);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message'),
                    'id'      => $insert_id
                ]));
        }
    }

    public function edit($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $id = $id ?: ($input['id'] ?? null);
        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
        }

        $teacher = $this->teacher_model->get($id);
        if (!$teacher) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Teacher not found']));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_rules('name', $this->lang->line('teacher'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('dob', $this->lang->line('date_of_birth'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $data = array(
                    'id'       => $id,
                    'name'     => $input['name'],
                    'email'    => $input['email'],
                    'password' => $input['password'] ?? '',
                    'sex'      => $input['gender'],
                    'dob'      => date('Y-m-d', $this->customlib->datetostrtotime($input['dob'])),
                    'address'  => $input['address'] ?? '',
                    'phone'    => $input['phone'],
                );

                $this->teacher_model->add($data);

                if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                    $fileInfo = pathinfo($_FILES["file"]["name"]);
                    $img_name = $id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/teacher_images/" . $img_name);
                    $data_img = array('id' => $id, 'image' => 'uploads/teacher_images/' . $img_name);
                    $this->teacher_model->add($data_img);
                }

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('update_message')
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'teacher' => $teacher,
                'genderList' => $this->customlib->getGender()
            ]));
    }

    public function getlogindetail()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $teacher_id = $input['teacher_id'] ?? null;

        if (!$teacher_id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'teacher_id is required']));
        }

        $loginDetails = $this->user_model->getTeacherLoginDetails($teacher_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $loginDetails
            ]));
    }

    // public function assign_class_teacher()
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }

    //     $input = $this->_get_input();

    //     if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
    //         $this->form_validation->set_data($input);
    //         $this->form_validation->set_rules(
    //             'class', $this->lang->line('class'), array(
    //                 'required',
    //                 array('class_exists', array($this->class_model, 'class_teacher_exists')),
    //             )
    //         );
    //         $this->form_validation->set_rules('section', $this->lang->line('section'), 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('teachers[]', $this->lang->line('class_teacher'), 'trim|required|xss_clean');

    //         if ($this->form_validation->run() == false) {
    //              return $this->output
    //                 ->set_status_header(422)
    //                 ->set_output(json_encode([
    //                     'status' => 'fail',
    //                     'errors' => $this->form_validation->error_array()
    //                 ]));
    //         } else {
    //             $class    = $input["class"];
    //             $section  = $input["section"];
    //             $teachers = $input["teachers"];

    //             $i = 0;
    //             foreach ($teachers as $key => $value) {
    //                 $classteacherid = $input["classteacherid"] ?? null;
    //                 if (isset($classteacherid) && isset($classteacherid[$i])) {
    //                     $data = array(
    //                         'id'         => $classteacherid[$i],
    //                         'class_id'   => $class,
    //                         'section_id' => $section,
    //                         'staff_id'   => $teachers[$i],
    //                         'session_id' => $this->current_session,
    //                     );
    //                 } else {
    //                     $data = array(
    //                         'class_id'   => $class,
    //                         'section_id' => $section,
    //                         'staff_id'   => $teachers[$i],
    //                         'session_id' => $this->current_session,
    //                     );
    //                 }
    //                 $this->classteacher_model->addClassTeacher($data);
    //                 $i++;
    //             }

    //             return $this->output
    //                 ->set_status_header(200)
    //                 ->set_output(json_encode([
    //                     'status'  => 'success',
    //                     'message' => $this->lang->line('success_message')
    //                 ]));
    //         }
    //     }

    //     // GET behavior
    //     $classlist         = $this->class_model->get();
    //     $sectionlist       = $this->section_model->get();
    //     $assignteacherlist = $this->class_model->getClassTeacher();
    //     $teacherlist       = $this->staff_model->getStaffbyrole(2);

    //     $tlist = [];
    //     foreach ($assignteacherlist as $key => $value) {
    //         $tlist[] = $this->classteacher_model->teacherByClassSection($value["class_id"], $value["section_id"]);
    //     }

    //     return $this->output
    //         ->set_status_header(200)
    //         ->set_output(json_encode([
    //             'status'            => 'success',
    //             'classlist'         => $classlist,
    //             'sectionlist'       => $sectionlist,
    //             'assignteacherlist' => $assignteacherlist,
    //             'teacherlist'       => $teacherlist,
    //             'tlist'             => $tlist
    //         ]));
    // }


    public function get_class_teacher_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Only GET Method Allowed"
                ]));
        }


        $classlist = $this->class_model->get();


        $sectionlist = $this->section_model->get();


        $teacherlist = $this->staff_model->getStaffbyrole(2);


        $assignteacherlist = $this->class_model->getClassTeacher();

        $assigned = [];

        foreach ($assignteacherlist as $value) {

            $class_id   = $value["class_id"];
            $section_id = $value["section_id"];

            $teachers = $this->classteacher_model
                ->teacherByClassSection($class_id, $section_id);

            $assigned[] = [
                "class_id"   => $class_id,
                "section_id" => $section_id,
                "teachers"   => $teachers
            ];
        }

        // Final Response
        return $this->output
            ->set_content_type("application/json")
            ->set_output(json_encode([
                "status" => true,
                "data" => [
                    "classes"           => $classlist,
                    "sections"          => $sectionlist,
                    "teachers"          => $teacherlist,
                    "assigned_teachers" => $assigned
                ]
            ]));
    }


    public function assign_class_teacher_old()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Only POST Method Allowed"
                ]));
        }

        $input = json_decode(file_get_contents("php://input"), true);

        $class_id   = $input['class_id'] ?? null;
        $section_id = $input['section_id'] ?? null;
        $teachers   = $input['teachers'] ?? [];

        // Validation
        if (empty($class_id) || empty($section_id) || empty($teachers)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "class_id, section_id, teachers are required"
                ]));
        }

        // Save Teachers
        foreach ($teachers as $teacher_id) {

            $data = [
                "class_id"   => $class_id,
                "section_id" => $section_id,
                "staff_id"   => $teacher_id,
                "session_id" => $this->current_session
            ];

            $this->classteacher_model->addClassTeacher($data);
        }

        return $this->output
            ->set_content_type("application/json")
            ->set_output(json_encode([
                "status" => true,
                "message" => "Class Teacher Assigned Successfully"
            ]));
    }

    public function assign_class_teacher()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Only POST Method Allowed"
                ]));
        }

        $input = json_decode(file_get_contents("php://input"), true);

        $class_id   = $input['class_id'] ?? null;
        $section_id = $input['section_id'] ?? null;
        $teachers   = $input['teachers'] ?? [];

        // ✅ Validation
        if (empty($class_id) || empty($section_id) || empty($teachers)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "class_id, section_id, teachers are required"
                ]));
        }

        // ✅ Remove duplicate teacher IDs from request itself
        $teachers = array_unique($teachers);

        $inserted = 0;
        $skipped  = 0;

        foreach ($teachers as $teacher_id) {

            /* =========================
           ✅ DUPLICATE CHECK (DB)
        ========================== */
            $this->db->where([
                "class_id"   => $class_id,
                "section_id" => $section_id,
                "staff_id"   => $teacher_id,
                "session_id" => $this->current_session
            ]);

            $exists = $this->db->get('class_teacher')->row(); // 🔁 check table name

            if ($exists) {
                $skipped++;
                continue; // skip duplicate
            }

            /* =========================
           INSERT
        ========================== */
            $data = [
                "class_id"   => $class_id,
                "section_id" => $section_id,
                "staff_id"   => $teacher_id,
                "session_id" => $this->current_session
            ];

            $this->classteacher_model->addClassTeacher($data);
            $inserted++;
        }

        return $this->output
            ->set_content_type("application/json")
            ->set_output(json_encode([
                "status"   => true,
                "message"  => "Class Teacher Assignment Completed",
                "inserted" => $inserted,
                "skipped"  => $skipped
            ]));
    }


    public function update_class_teacher()
    {
        // ==============================
        // CORS
        // ==============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ==============================
        // ALLOW ONLY POST
        // ==============================
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ==============================
        // GET JSON INPUT
        // ==============================
        $input = json_decode(file_get_contents("php://input"), true);

        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        // ==============================
        // VALIDATION
        // ==============================
        $this->form_validation->set_rules('class', 'Class', 'required');
        $this->form_validation->set_rules('section', 'Section', 'required');
        $this->form_validation->set_rules('teachers[]', 'Class Teacher', 'required');

        if ($this->form_validation->run() == false) {

            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        // ==============================
        // VARIABLES
        // ==============================
        $class_id     = $input['class'];
        $section      = $input['section'];
        $staff_id     = $input['teachers'] ?? [];
        $prev_teacher = $input['classteacherid'] ?? [];

        $add_result    = array_diff($staff_id, $prev_teacher);
        $delete_result = array_diff($prev_teacher, $staff_id);

        // ==============================
        // ADD TEACHER
        // ==============================
        if (!empty($add_result)) {

            foreach ($add_result as $teacher_add_value) {

                $data = [
                    'class_id'   => $class_id,
                    'section_id' => $section,
                    'staff_id'   => $teacher_add_value,
                    'session_id' => $this->current_session
                ];

                $this->classteacher_model->addClassTeacher($data);
            }
        } else {

            $prev_class_id   = $input['prev_class_id'] ?? null;
            $prev_section_id = $input['prev_section_id'] ?? null;
            $previd          = $input['previd'] ?? [];

            if (!empty($previd)) {

                if ($prev_class_id != $class_id || $prev_section_id != $section) {

                    $this->classteacher_model->updateTeacher(
                        $previd,
                        $class_id,
                        $section
                    );
                }
            }
        }

        // ==============================
        // DELETE TEACHER
        // ==============================
        if (!empty($delete_result)) {

            $this->classteacher_model->delete(
                $class_id,
                $section,
                $delete_result
            );
        }

        // ==============================
        // RESPONSE
        // ==============================
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Class teacher updated successfully'
            ]));
    }

    public function class_teacher_edit($class_id = null, $section_id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Only GET method allowed"
                ]));
        }

        // Validate URI segment values
        if (empty($class_id) || empty($section_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "class_id and section_id are required in URL"
                ]));
        }

        // Assigned Teachers
        $assigned_teachers = $this->classteacher_model
            ->teacherByClassSection($class_id, $section_id);

        // Teacher List
        $teacherlist = $this->staff_model->getStaffbyrole(2);

        return $this->output
            ->set_content_type("application/json")
            ->set_output(json_encode([
                "status" => true,
                "data" => [
                    "class_id"          => $class_id,
                    "section_id"        => $section_id,
                    "assigned_teachers" => $assigned_teachers,
                    "all_teachers"      => $teacherlist
                ]
            ]));
    }


    public function update_class_teacherNew()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Only POST method allowed"
                ]));
        }

        $input = json_decode(file_get_contents("php://input"), true);

        $class_id   = $input["class_id"] ?? null;
        $section_id = $input["section_id"] ?? null;
        $teachers   = $input["teachers"] ?? [];

        if (empty($class_id) || empty($section_id) || empty($teachers)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "class_id, section_id, teachers are required"
                ]));
        }

        // Get Previous Teachers
        $prev_teachers = $this->classteacher_model
            ->teacherByClassSection($class_id, $section_id);

        $prev_ids = array_column($prev_teachers, "staff_id");

        // Find Add + Delete Difference
        $add_result    = array_diff($teachers, $prev_ids);
        $delete_result = array_diff($prev_ids, $teachers);

        // Add New Teachers
        if (!empty($add_result)) {
            foreach ($add_result as $staff_id) {

                $data = [
                    "class_id"   => $class_id,
                    "section_id" => $section_id,
                    "staff_id"   => $staff_id,
                    "session_id" => $this->current_session
                ];

                $this->classteacher_model->addClassTeacher($data);
            }
        }

        // Remove Deleted Teachers
        if (!empty($delete_result)) {
            $this->classteacher_model->delete(
                $class_id,
                $section_id,
                $delete_result
            );
        }

        return $this->output
            ->set_content_type("application/json")
            ->set_output(json_encode([
                "status" => true,
                "message" => "Class Teacher Updated Successfully",
                "added"   => array_values($add_result),
                "deleted" => array_values($delete_result)
            ]));
    }



    // public function classteacherdelete()
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }

    //     $input = $this->_get_input();
    //     $class_id   = $input['class_id'] ?? null;
    //     $section_id = $input['section_id'] ?? null;

    //     if ($class_id && $section_id) {
    //         $this->classteacher_model->delete($class_id, $section_id, null);
    //         return $this->output
    //             ->set_status_header(200)
    //             ->set_output(json_encode([
    //                 'status'  => 'success',
    //                 'message' => $this->lang->line('delete_message')
    //             ]));
    //     }

    //     return $this->output
    //         ->set_status_header(400)
    //         ->set_output(json_encode(['status' => 'fail', 'message' => 'class_id and section_id are required']));
    // }


    public function classteacherdelete()
    {
        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type("application/json")
                ->set_output(json_encode([
                    "status"  => false,
                    "message" => "Only POST method allowed"
                ]));
        }

        // Read JSON body input
        $input = json_decode(file_get_contents("php://input"), true);

        $class_id   = isset($input['class_id']) ? $input['class_id'] : null;
        $section_id = isset($input['section_id']) ? $input['section_id'] : null;

        // Validate required fields
        if (empty($class_id) || empty($section_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type("application/json")
                ->set_output(json_encode([
                    "status"  => false,
                    "message" => "class_id and section_id are required"
                ]));
        }

        // Delete class teacher assignment
        $this->classteacher_model->delete($class_id, $section_id, null);

        // Success response
        return $this->output
            ->set_content_type("application/json")
            ->set_output(json_encode([
                "status"  => true,
                "message" => "Class Teacher Deleted Successfully"
            ]));
    }

    public function assign_subject_teacher()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {

            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('class', $this->lang->line('class'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('section', $this->lang->line('section'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('teacher', $this->lang->line('teacher_list'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            }

            $class_id   = $input["class"];
            $section_id = $input["section"];
            $staff_id   = $input["teacher"];
            $subject_id = $input["subject_id"];
            $session_id = $this->current_session;

            /* =========================
           ✅ DUPLICATE CHECK
        ========================== */
            $this->db->where([
                'class_id'   => $class_id,
                'section_id' => $section_id,
                'staff_id'   => $staff_id,
                'subject_id' => $subject_id,
                'session_id' => $session_id
            ]);

            $exists = $this->db->get('subject_teacher')->row(); // 🔁 check table name

            if ($exists) {
                return $this->output
                    ->set_status_header(409)
                    ->set_output(json_encode([
                        'status'  => 'fail',
                        'message' => 'This teacher is already assigned to this class, section, and subject'
                    ]));
            }

            /* =========================
           INSERT
        ========================== */
            $array_data = array(
                'class_id'   => $class_id,
                'section_id' => $section_id,
                'staff_id'   => $staff_id,
                'subject_id' => $subject_id,
                'session_id' => $session_id,
            );

            $this->classteacher_model->addSubjectTeacher($array_data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message')
                ]));
        }

        /* =========================
       GET DATA
    ========================== */
        $classlist      = $this->class_model->get();
        $batch_subjects = $this->subject_model->get();
        $subject_data   = $this->classteacher_model->getSubjectTeachersList();

        $my_role  = $this->customlib->getStaffRole();
        $role     = json_decode($my_role);
        $staff_list = [];

        if ($role->id != "2") {
            $staff_list = $this->staff_model->getEmployee('2');
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'         => 'success',
                'classlist'      => $classlist,
                'batch_subjects' => $batch_subjects,
                'subject_data'   => $subject_data,
                'staff_list'     => $staff_list
            ]));
    }

    public function subjectteacherdelete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!$id) {
            $input = $this->_get_input();
            $id = $input['id'] ?? null;
        }

        if ($id) {
            $this->classteacher_model->deletesubjectteacher($id);
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

    public function handle_upload()
    {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png');
            $temp        = explode(".", $_FILES["file"]["name"]);
            $extension   = end($temp);

            if ($_FILES["file"]["error"] > 0) {
                $this->form_validation->set_message('handle_upload', 'Error opening the file');
                return false;
            }
            if (
                $_FILES["file"]["type"] != 'image/gif' &&
                $_FILES["file"]["type"] != 'image/jpeg' &&
                $_FILES["file"]["type"] != 'image/png'
            ) {

                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($extension, $allowedExts)) {

                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($_FILES["file"]["size"] > 10240000) {

                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than'));
                return false;
            }
            return true;
        } else {
            return true;
        }
    }
}

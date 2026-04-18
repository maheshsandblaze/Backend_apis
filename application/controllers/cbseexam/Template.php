<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Template extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {


        $data['title'] = 'Category List';
        $category_result = $this->category_model->get();
        $data['categorylist'] = $category_result;
        $subjectgroupList = $this->subjectgroup_model->getByID();
        $data['subjectgroupList'] = $subjectgroupList;
        $data['result'] = $this->cbseexam_template_model->gettemplatelist();
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['marksheet'] = $this->cbseexam_result_model->marksheet_type();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Read input (multipart/form-data)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // $input = json_decode(file_get_contents('php://input'), true);

        // Read input properly for both JSON and FormData
        if (!empty($_POST)) {
            $input = $this->input->post();   // Form-data input
        } else {
            $input = json_decode(file_get_contents("php://input"), true); // Raw JSON
        }


        // -----------------------------
        // VALIDATION
        // -----------------------------
        $errors = [];

        if (empty($input['name'])) {
            $errors['name'] = $this->lang->line('template') . ' required';
        }

        if (empty($input['class_id'])) {
            $errors['class_id'] = $this->lang->line('class') . ' required';
        }

        if (empty($input['section'])) {
            $errors['section'] = $this->lang->line('please_select_atleast_one_section');
        }

        if (!empty($errors)) {
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => $errors,
                    'message' => ''
                ]));
        }

        // -----------------------------
        // FLAGS
        // -----------------------------
        $flags = [
            'is_name'         => isset($input['is_name']) ? 1 : 0,
            'is_father_name'  => isset($input['is_father_name']) ? 1 : 0,
            'is_mother_name'  => isset($input['is_mother_name']) ? 1 : 0,
            'is_admission_no' => isset($input['is_admission_no']) ? 1 : 0,
            'is_roll_no'      => isset($input['is_roll_no']) ? 1 : 0,
            'is_photo'        => isset($input['is_photo']) ? 1 : 0,
            'is_division'     => isset($input['is_division']) ? 1 : 0,
            'is_class'        => isset($input['is_class']) ? 1 : 0,
            'is_section'      => isset($input['is_section']) ? 1 : 0,
            'is_dob'          => isset($input['is_dob']) ? 1 : 0,
            'is_remark'       => isset($input['remark']) ? 1 : 0,
            'exam_session'    => isset($input['exam_session']) ? 1 : 0,
        ];

        // -----------------------------
        // DATA
        // -----------------------------
        $data = [
            'name'            => $input['name'],
            'description'     => $input['description'] ?? '',
            'school_name'     => $input['school_name'] ?? '',
            'exam_center'     => $input['exam_center'] ?? '',
            'date'            => !empty($input['date'])
                ? $this->customlib->dateFormatToYYYYMMDD($input['date'])
                : null,
            'content'         => $input['content'] ?? '',
            'content_footer'  => $input['content_footer'] ?? '',
            'orientation'     => $input['orientation'] ?? 'P',
            'session_id'      => $this->setting_model->getCurrentSession(),
            'created_by'      => $this->customlib->getStaffID(),
        ] + $flags;

        // -----------------------------
        // FILE UPLOAD HANDLER
        // -----------------------------
        $upload_map = [
            'header_image'  => 'header_image',
            'left_sign'     => 'left_sign',
            'middle_sign'   => 'middle_sign',
            'right_sign'    => 'right_sign',
            'background_img' => 'background_img'
        ];

        foreach ($upload_map as $field => $folder) {
            if (!empty($_FILES[$field]['name'])) {
                $time = md5($_FILES[$field]['name'] . microtime());
                $ext  = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                $file = $time . '.' . $ext;
                move_uploaded_file(
                    $_FILES[$field]['tmp_name'],
                    "../uploads/cbseexam/template/{$folder}/{$file}"
                );
                $data[$field] = $file;
            }
        }

        // -----------------------------
        // INSERT TEMPLATE
        // -----------------------------
        $template_id = $this->cbseexam_template_model->add($data);

        // -----------------------------
        // INSERT CLASS SECTIONS
        // -----------------------------
        foreach ($input['section'] as $section_id) {
            $this->cbseexam_template_model->add_class_section([
                'cbse_template_id' => $template_id,
                'class_section_id' => $section_id
            ]);
        }

        // -----------------------------
        // RESPONSE
        // -----------------------------
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }

    public function edit()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Read input (multipart/form-data)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        if (!empty($_POST)) {
            $input = $this->input->post();   // Form-data input
        } else {
            $input = json_decode(file_get_contents("php://input"), true); // Raw JSON
        }

        // -----------------------------
        // VALIDATION
        // -----------------------------
        $errors = [];

        if (empty($input['name'])) {
            $errors['name'] = $this->lang->line('template') . ' required';
        }

        if (empty($input['class_id'])) {
            $errors['class_id'] = $this->lang->line('class') . ' required';
        }

        if (empty($input['section'])) {
            $errors['section'] = $this->lang->line('please_select_atleast_one_section');
        }

        if (empty($input['templateid'])) {
            $errors['templateid'] = 'Template ID is required';
        }

        // File validation - check file types and sizes if needed
        $allowed_types = 'jpg|jpeg|png|gif|bmp|pdf';
        $max_size = 2048; // 2MB in KB

        $file_fields = ['header_image', 'left_logo', 'right_logo', 'background_img', 'left_sign', 'middle_sign', 'right_sign'];
        foreach ($file_fields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                $allowed_exts = explode('|', $allowed_types);

                if (!in_array(strtolower($ext), $allowed_exts)) {
                    $errors[$field] = $this->lang->line('invalid_file_type');
                }

                if ($_FILES[$field]['size'] > $max_size * 1024) {
                    $errors[$field] = sprintf($this->lang->line('file_size_exceeds'), $max_size . 'KB');
                }
            }
        }

        if (!empty($errors)) {
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => $errors,
                    'message' => ''
                ]));
        }


        // echo "<pre>";
        // print_r($input);exit;
        // -----------------------------
        // GET EXISTING TEMPLATE
        // -----------------------------
        $existing_template = $this->cbseexam_template_model->get($input['templateid']);
        if (!$existing_template) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Template not found'
                ]));
        }

        // -----------------------------
        // FLAGS
        // -----------------------------
        $flags = [
            'is_name'         => ($input['is_name'] == 1)  ? 1 : 0,
            'is_father_name'  => ($input['is_father_name'] == 1) ? 1 : 0,
            'is_mother_name'  => ($input['is_mother_name'] == 1) ? 1 : 0,
            'is_admission_no' => ($input['is_admission_no'] == 1) ? 1 : 0,
            'is_roll_no'      => ($input['is_roll_no'] == 1) ? 1 : 0,
            'is_photo'        =>  ($input['is_photo'] == 1) ? 1 : 0,
            'is_division'     => ($input['is_division'] == 1) ? 1 : 0,
            'is_class'        => ($input['is_class'] == 1) ? 1 : 0,
            'is_section'      => ($input['is_section'] == 1) ? 1 : 0,
            'is_dob'          => ($input['is_dob'] == 1) ? 1 : 0,
            'is_remark'       => ($input['is_remark'] == 1) ? 1 : 0,
            'exam_session'    => ($input['exam_session'] == 1) ? 1 : 0,
        ];

        // -----------------------------
        // DATA
        // -----------------------------
        $data = [
            'id'              => $input['templateid'],
            'name'            => $input['name'],
            'description'     => $input['description'] ?? '',
            'school_name'     => $input['school_name'] ?? '',
            'exam_center'     => $input['exam_center'] ?? '',
            'date'            => !empty($input['date'])
                ? $this->customlib->dateFormatToYYYYMMDD($input['date'])
                : null,
            'content'         => $input['content'] ?? '',
            'content_footer'  => $input['content_footer'] ?? '',
            'orientation'     => trim($input['orientation']) ?? 'P',
            'created_by'      => $this->customlib->getStaffID()
            // 'updated_at'      => date('Y-m-d H:i:s')
        ] + $flags;

        // -----------------------------
        // FILE UPLOAD HANDLER
        // -----------------------------
        $upload_map = [
            'header_image'   => 'header_image',
            'left_logo'      => 'left_logo',
            'right_logo'     => 'right_logo',
            'left_sign'      => 'left_sign',
            'middle_sign'    => 'middle_sign',
            'right_sign'     => 'right_sign',
            'background_img' => 'background_img'
        ];

        foreach ($upload_map as $field => $folder) {
            if (!empty($_FILES[$field]['name'])) {
                // Upload new file
                $time = md5($_FILES[$field]['name'] . microtime());
                $ext  = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                $file = $time . '.' . $ext;

                move_uploaded_file(
                    $_FILES[$field]['tmp_name'],
                    "./uploads/cbseexam/template/{$folder}/{$file}"
                );

                // Delete old file if exists
                if (!empty($existing_template[$field])) {
                    $old_file_path = "./uploads/cbseexam/template/{$folder}/{$existing_template[$field]}";
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }

                $data[$field] = $file;
            } else {
                // Keep existing file
                $data[$field] = $existing_template[$field] ?? '';
            }
        }

        // -----------------------------
        // UPDATE TEMPLATE
        // -----------------------------
        // echo "<pre>";
        // print_r($data);exit;
        $this->cbseexam_template_model->add($data); // Assuming add() handles both insert and update based on ID

        // -----------------------------
        // UPDATE CLASS SECTIONS
        // -----------------------------
        // Delete existing sections
        $this->cbseexam_template_model->deleteclasssectionbytemplateid($input['templateid']);

        // Insert new sections
        foreach ($input['section'] as $section_id) {
            $this->cbseexam_template_model->add_class_section([
                'cbse_template_id' => $input['templateid'],
                'class_section_id' => $section_id
            ]);
        }

        // -----------------------------
        // RESPONSE
        // -----------------------------
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }




    public function getdata()
    {
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

        // Support JSON payload
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (empty($input['template_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => [
                        'template_id' => 'Template ID required'
                    ]
                ]));
        }

        $template_id = $input['template_id'];

        $result = $this->cbseexam_template_model->get($template_id);
        $sections = $this->cbseexam_template_model->getclasssection($template_id);

        if (empty($result)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Template not found'
                ]));
        }

        $data = [
            'template'            => $result,
            'classlist'           => $this->class_model->get(),
            'sections'            => $sections,
            'selected_class_id'   => !empty($sections) ? $sections[0]['class_id'] : null,
            'selected_section_id' => $sections
        ];

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }


    public function viewtemplate()
    {
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

        // Support JSON payload
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (empty($input['template_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => [
                        'template_id' => 'Template ID required'
                    ]
                ]));
        }

        $template_id = $input['template_id'];
        $template = $this->cbseexam_template_model->get($template_id);

        if (empty($template)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Template not found'
                ]));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'template' => $template
                ]
            ]));
    }


    public function get_ClassSectionByTermId($termid)
    {
        $result = $this->cbseexam_term_model->gettermbyid($termid);
        $data['class_id'] = $result[0]['class_id'];
        $data['sections'] = $this->section_model->getClassBySection($result[0]['class_id']);
        echo json_encode($data);
    }

    public function remove()
    {
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

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (empty($input['templateid'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => ['templateid' => 'Template ID required']
                ]));
        }

        $this->cbseexam_template_model->remove($input['templateid']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('delete_message')
            ]));
    }


    public function get_examdata()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (empty($input['marksheet_type']) || empty($input['template_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => 'marksheet_type and template_id required'
                ]));
        }

        $result = $this->cbseexam_template_model->all_term($input['template_id']);
        $templatedata = $this->cbseexam_template_model->get_templatedata($input['template_id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data' => [
                    'marksheet_type' => $input['marksheet_type'],
                    'template'       => $templatedata,
                    'exam_data'      => $result
                ]
            ]));
    }


    public function linkexams()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (empty($input['marksheet']) || empty($input['template_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => 'marksheet and template_id required'
                ]));
        }

        $templatedata = [
            'id'             => $input['template_id'],
            'marksheet_type' => $input['marksheet'],
            'gradeexam_id'   => $input['grading'] ?? null,
            'remarkexam_id'  => $input['teacher_remark'] ?? null,
        ];

        $this->cbseexam_template_model->add($templatedata);
        $this->cbseexam_template_model->delete_template_record($input['template_id']);

        // save exams
        if (!empty($input['exam'])) {
            foreach ($input['exam'] as $exam_id) {
                $this->cbseexam_template_model->cbse_template_term_exams([
                    'cbse_template_id' => $input['template_id'],
                    'cbse_exam_id'     => $exam_id,
                    'weightage'        => $input['weightage'][$exam_id] ?? null
                ]);
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }


    public function get()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (empty($input['class_section_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => 'class_section_id required'
                ]));
        }

        $templates = $this->cbseexam_template_model
            ->getTemplateListbyclasssectionid($input['class_section_id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $templates
            ]));
    }


    public function check_exams($field, $exams)
    {
        if ($exams >= 2) {
            // user exists
            return true;
        } else {
            $this->form_validation->set_message('check_exams', $this->lang->line('select_multiple_exams'));
            return false;
        }
    }

    public function check_exam_grading($field, $exams)
    {
        $exams = explode("|", $exams);
        if (in_array($field, $exams)) {
            // user exists
            return true;
        } else {
            $this->form_validation->set_message('check_exam_grading', $this->lang->line('grading_should_be_choose_from_selected_exam'));
            return false;
        }
        return true;
    }

    public function check_teacher_remark($field, $exams)
    {
        $exams = explode("|", $exams);
        if (in_array($field, $exams)) {
            // user exists
            return true;
        } else {
            $this->form_validation->set_message('check_teacher_remark', $this->lang->line('teacher_remark_should_be_choose_from_selected_exam'));
            return false;
        }
        return true;
    }

    public function check_weightage($field, $weightage)
    {
        if ($weightage == 100) {
            // user exists
            return true;
        } else {
            if (strlen($weightage) != 0) {
                if ($weightage == 0) {
                    $this->form_validation->set_message('check_weightage', $this->lang->line('select_term_exam_weightage_should_not_be_zero_or_empty'));
                    return false;
                } elseif ($weightage > 100 || $weightage < 100) {
                    $this->form_validation->set_message('check_weightage', $this->lang->line('select_term_exam_weightage_should_not_be_greater_or_less'));
                    return false;
                }
            }
        }
        return true;
    }

    public function handle_upload($str, $var)
    {
        $result = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

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
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed') . " " . $this->lang->line('or') . " " . $this->lang->line('extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function templatewiserank($template_id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (empty($template_id)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Template ID is required'
                ]));
        }

        $class_section_id = $this->input->post('class_section_id');

        $template = $this->cbseexam_template_model->get($template_id);
        $studentList = $this->cbseexam_result_model->getTemplateStudents($template_id);
        $sch_setting = $this->setting_model->getSetting();

        $response = [
            'status' => true,
            'template_id' => $template_id,
            'school_setting' => $sch_setting,
            'template' => $template,
            'student_list' => $studentList
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }


}

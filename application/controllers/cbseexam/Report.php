<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Report extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->current_session    = $this->setting_model->getCurrentSession();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }




    // public function index()
    // {
    //     // Allow only GET or POST (optional but recommended)
    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         return $this->output
    //             ->set_status_header(405)
    //             ->set_output(json_encode([
    //                 'status'  => false,
    //                 'message' => 'Method Not Allowed'
    //             ]));
    //     }


    //     // Load view as STRING (IMPORTANT)
    //     $page = $this->load->view(
    //         'cbseexam/report/index',
    //         [],          // pass data array if needed
    //         true         // return as string
    //     );

    //     // Return JSON response
    //     return $this->output
    //         ->set_content_type('application/json')
    //         ->set_status_header(200)
    //         ->set_output(json_encode([
    //             'status' => true,
    //             'message'     => 'CBSE Report API Loaded Successfully',

    //             'page'   => $page
    //         ]));
    // }
    
    
    public function index()
    {
        // Preflight CORS support
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only GET method
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // API Response Data
        $response = [
            'top_menu'    => 'cbse_exam',
            'sub_menu'    => 'reports/cbse_report',
            'subsub_menu' => '',
            'message'     => 'CBSE Report API Loaded Successfully'
        ];
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $response
            ]));
    }




    public function indexold()
    {


        // API Response Data
        $response = [
            'top_menu'    => 'cbse_exam',
            'sub_menu'    => 'reports/cbse_report',
            'subsub_menu' => '',
            'message'     => 'CBSE Report API Loaded Successfully'
        ];

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $response
            ]));
    }


    public function get_examsubject()
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
    
        // Fetch Exam List
        $exams = $this->cbseexam_exam_model->getexamlist();
    
        // Return Response
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => true,
                "data"   => $exams
            ]));
    }



    public function examsubject()
    {
        // Handle OPTIONS Request (CORS)
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

        // Accept JSON or form-data
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (empty($input['exam_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => [
                        'exam_id' => 'Exam ID is required'
                    ]
                ]));
        }

        $exam_id = $input['exam_id'];

        // ---------------- BASIC DATA ----------------
        $subjects         = $this->cbseexam_exam_model->getexamsubjects($exam_id);
        $exam             = $this->cbseexam_exam_model->getExamWithGrade($exam_id);
        $exam_assessments = $this->cbseexam_assessment_model
            ->getWithAssessmentTypeByAssessmentID($exam->cbse_exam_assessment_id);

        // ---------------- RESULT DATA ----------------
        $students = [];
        $cbse_exam_result = $this->cbseexam_exam_model->getExamResultByExamId($exam_id);

        if (!empty($cbse_exam_result)) {

            foreach ($cbse_exam_result as $row) {

                // Student not exists
                if (!isset($students[$row->student_session_id])) {

                    $students[$row->student_session_id] = [
                        'student_id'         => $row->student_id,
                        'student_session_id' => $row->student_session_id,
                        'firstname'          => $row->firstname,
                        'middlename'         => $row->middlename,
                        'lastname'           => $row->lastname,
                        'roll_no'            => $row->roll_no,
                        'admission_no'       => $row->admission_no,
                        'gender'             => $row->gender,
                        'dob'                => $row->dob,
                        'mobileno'           => $row->mobileno,
                        'email'              => $row->email,
                        'guardian_name'      => $row->guardian_name,
                        'guardian_phone'     => $row->guardian_phone,
                        'father_name'        => $row->father_name,
                        'mother_name'        => $row->mother_name,
                        'class_id'           => $row->class_id,
                        'class'              => $row->class,
                        'section_id'         => $row->section_id,
                        'section'            => $row->section,
                        'student_image'      => $row->image,
                        'remark'             => $row->remark,
                        'rank'               => $row->rank,
                        'total_present_days' => $row->total_present_days,
                        'total_working_days' => $row->total_working_days,
                        'subjects'           => []
                    ];
                }

                // Subject not exists
                if (!isset($students[$row->student_session_id]['subjects'][$row->subject_id])) {

                    $students[$row->student_session_id]['subjects'][$row->subject_id] = [
                        'subject_id'       => $row->subject_id,
                        'subject_name'     => $row->subject_name,
                        'subject_code'     => $row->subject_code,
                        'exam_assessments' => []
                    ];
                }

                // Assessment
                $students[$row->student_session_id]['subjects'][$row->subject_id]['exam_assessments'][$row->cbse_exam_assessment_type_id] = [
                    'cbse_exam_assessment_type_id'   => $row->cbse_exam_assessment_type_id,
                    'cbse_exam_assessment_type_name' => $row->cbse_exam_assessment_type_name,
                    'cbse_exam_assessment_type_code' => $row->cbse_exam_assessment_type_code,
                    'maximum_marks'                  => $row->maximum_marks,
                    'cbse_student_subject_marks_id'  => $row->cbse_student_subject_marks_id,
                    'marks'                          => $row->marks,
                    'note'                           => $row->note,
                    'is_absent'                      => $row->is_absent,
                ];
            }
        }
        
        
        // ---------------- CALCULATE TOTALS, PERCENTAGE, GRADE ----------------
        foreach ($students as &$student) {
        
            $total_marks = 0;
            $total_max_marks = 0;
        
            // Loop Subjects
            foreach ($student['subjects'] as $subject) {
        
                // Loop Assessments
                foreach ($subject['exam_assessments'] as $assessment) {
        
                    $marks = $assessment['marks'];
        
                    // If absent or null → treat as 0
                    if ($marks === null || $marks === "N/A") {
                        $marks = 0;
                    }
        
                    $total_marks += $marks;
                    $total_max_marks += $assessment['maximum_marks'];
                }
            }
        
            // Percentage
            $percentage = $this->getPercent($total_max_marks, $total_marks);
        
            // Grade
            $grade = $this->getGrade($exam->grades, $percentage);
        
            // Add into student response
            $student['total_marks']     = $total_marks;
            $student['total_max_marks'] = $total_max_marks;
            $student['percentage']      = $percentage;
            $student['grade']           = $grade;
        
            // Rank already exists
            $student['rank']            = $student['rank'];
        }

        

        // ---------------- FINAL RESPONSE ----------------
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'exam'             => $exam,
                    'subjects'         => $subjects,
                    'exam_assessments' => $exam_assessments,
                    'students'         => array_values($students)
                ]
            ]));
    }



    public function templatewise()
    {
        // Preflight CORS support
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

        // if (!$this->rbac->hasPrivilege('template_marks_report', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access denied'
        //         ]));
        // }

        $data = [
            'classlist' => $this->class_model->get(null),
            'templates' => $this->cbseexam_template_model->get(null)
        ];

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }


    public function getTemplatewiseExams()
    {
        $this->form_validation->set_rules('template_id', $this->lang->line('template_id'), 'required|trim|xss_clean');
        $data = array();
        if ($this->form_validation->run() == false) {
            $msg = array(
                'template_id' => form_error('template_id'),
            );
            $array = array('status' => 0, 'error' => $msg);
            echo json_encode($array);
        } else {
            $template_id = $this->input->post('template_id');
        }
    }

    public function getTermTemplateWise()
    {
        // Handle OPTIONS Request (CORS)
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

        if (empty($input['template_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error' => [
                        'template_id' => 'Template ID is required'
                    ]
                ]));
        }

        $template_id = $input['template_id'];

        $template_data = $this->cbseexam_template_model
            ->getTemplateTermsOrExam($template_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $template_data
            ]));
    }


    public function getTemplateWiseResult()
    {
        // Preflight CORS support
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

        $errors = [];

        if (empty($input['class_id'])) {
            $errors['class_id'] = 'Class is required';
        }
        if (empty($input['class_section_id'])) {
            $errors['class_section_id'] = 'Section is required';
        }
        if (empty($input['template_id'])) {
            $errors['template_id'] = 'Template ID is required';
        }

        if (!empty($errors)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => $errors
                ]));
        }

        $template_id      = $input['template_id'];
        $class_section_id = $input['class_section_id'];

        $template = $this->cbseexam_template_model->get($template_id);

        if (empty($template)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Template not found'
                ]));
        }

        // -------- SWITCH BY MARKSHEET TYPE --------
        if ($template['marksheet_type'] === 'without_term') {

            $result = $this->getMultiexam($template_id, $class_section_id);
        } elseif ($template['marksheet_type'] === 'exam_wise') {

            $exam = $this->cbseexam_exam_model
                ->getTemplateSingleExam($template_id);

            $result = $this->getSingleExam(
                $exam->cbse_exam_id,
                $class_section_id,
                $template_id
            );
        } elseif ($template['marksheet_type'] === 'all_term') {

            $result = $this->getMultiTerm($template_id, $class_section_id);
        } elseif ($template['marksheet_type'] === 'term_wise') {

            $result = $this->getSinglTerm($template_id, $class_section_id);
        } else {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Invalid marksheet type'
                ]));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data' => [
                    'template_id'     => $template_id,
                    'marksheet_type'  => $template['marksheet_type'],
                    'result'          => $result
                ]
            ]));
    }


    public function getSinglTerm($cbse_template_id, $class_section_id) //multiple exam in single term
    {
        $data['terms'] = $this->cbseexam_template_model->getTemplateWithTermWithExams($cbse_template_id);
        $cbse_exam_result = $this->cbseexam_exam_model->getResultTermwiseByTemplateId($cbse_template_id, $class_section_id);
        $template_subjects         = $this->cbseexam_exam_model->getTemplateAssessment($cbse_template_id);
        $subject_array             = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id              = "";
        $remarkexam_id             = "";

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name . " (" . $cbse_exam_result_value->subject_code . ")";
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            if ((!array_key_exists($cbse_exam_result_value->cbse_term_id, $exam_term_exam_assessment))) {
                $assessment_array = $this->getExamAssesmentByTerm($template_subjects, $cbse_exam_result_value->cbse_term_id);
                $new_terms = [
                    'cbse_term_id'           => $cbse_exam_result_value->cbse_term_id,
                    'cbse_term_name'         => $cbse_exam_result_value->cbse_term_name,
                    'cbse_term_code'         => $cbse_exam_result_value->cbse_term_code,
                    'term_total_assessments' => $assessment_array,
                    'exams'                  => [],

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->cbse_term_id] = $new_terms;
            }
        }

        foreach ($template_subjects as $sub_key => $sub_value) {
            if (!array_key_exists($sub_value->cbse_exam_id, $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'])) {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id] = [
                    'cbse_exam_id'     => $sub_value->cbse_exam_id,
                    'exam_name'        => $sub_value->exam_name,
                    'weightage'        => $sub_value->weightage,
                    'exam_assessments' => [[

                        'cbse_exam_assessment_id'      => $sub_value->cbse_exam_assessment_id,
                        'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                        'name'                         => $sub_value->name,
                        'code'                         => $sub_value->code,
                        'maximum_marks'                => $sub_value->maximum_marks,
                        'pass_percentage'              => $sub_value->pass_percentage,

                    ]],

                ];
            } else {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id]['exam_assessments'][] = [

                    'cbse_exam_assessment_id'      => $sub_value->cbse_exam_assessment_id,
                    'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                    'name'                         => $sub_value->name,
                    'code'                         => $sub_value->code,
                    'maximum_marks'                => $sub_value->maximum_marks,
                    'pass_percentage'              => $sub_value->pass_percentage,

                ];
            }
        }

        $data['subject_array']             = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;
        $students                          = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $gradeexam_id  = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_session_id, $students)) {

                if (!array_key_exists($student_value->cbse_term_id, $students[$student_value->student_session_id]['terms'])) {

                    $new_cbse_term_id = [

                        'cbse_term_id'           => $student_value->cbse_term_id,
                        'cbse_term_name'         => $student_value->cbse_term_name,
                        'cbse_term_code'         => $student_value->cbse_term_code,
                        'cbse_term_weight'       => $student_value->cbse_template_terms_weightage,
                        'term_total_assessments' => 1,

                        'exams'                  => [
                            $student_value->id => [
                                'name'               => $student_value->name,
                                'total_assessments'  => 1,
                                'total_present_days' => $student_value->total_present_days,
                                'total_working_days' => $student_value->total_working_days,
                                'subjects'           => [
                                    $student_value->subject_id => [
                                        'subject_id'       => $student_value->subject_id,
                                        'subject_name'     => $student_value->subject_name,
                                        'subject_code'     => $student_value->subject_code,
                                        'exam_assessments' => [
                                            $student_value->cbse_exam_assessment_type_id => [
                                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                'maximum_marks'                  => $student_value->maximum_marks,
                                                'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                                'marks'                          => $student_value->marks,
                                                'note'                           => $student_value->note,
                                                'is_absent'                      => $student_value->is_absent,

                                            ],

                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id] = $new_cbse_term_id;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'])) {

                    $new_exam = [
                        'name'               => $student_value->name,
                        'total_assessments'  => 1,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects'           => [
                            $student_value->subject_id => [
                                'subject_id'       => $student_value->subject_id,
                                'subject_name'     => $student_value->subject_name,
                                'subject_code'     => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks'                  => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                        'marks'                          => $student_value->marks,
                                        'note'                           => $student_value->note,
                                        'is_absent'                      => $student_value->is_absent,

                                    ],
                                ],
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id] = $new_exam;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'])) {

                    $new_subject = [
                        'subject_id'       => $student_value->subject_id,
                        'subject_name'     => $student_value->subject_name,
                        'subject_code'     => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks'                  => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                'marks'                          => $student_value->marks,
                                'note'                           => $student_value->note,
                                'is_absent'                      => $student_value->is_absent,
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks'                  => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                        'marks'                          => $student_value->marks,
                        'note'                           => $student_value->note,
                        'is_absent'                      => $student_value->is_absent,
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['total_assessments'] = count($students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments']);
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                }
            } else {
                $students[$student_value->student_session_id] = [
                    'student_id'         => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname'          => $student_value->firstname,
                    'middlename'         => $student_value->middlename,
                    'lastname'           => $student_value->lastname,
                    'mobileno'           => $student_value->mobileno,
                    'email'              => $student_value->email,
                    'religion'           => $student_value->religion,
                    'guardian_name'      => $student_value->guardian_name,
                    'guardian_phone'     => $student_value->guardian_phone,
                    'dob'                => $student_value->dob,
                    'admission_no'       => $student_value->admission_no,
                    'father_name'        => $student_value->father_name,
                    'mother_name'        => $student_value->mother_name,
                    'class_id'           => $student_value->class_id,
                    'class'              => $student_value->class,
                    'section_id'         => $student_value->section_id,
                    'section'            => $student_value->section,
                    'roll_no'            => $student_value->roll_no,
                    'student_image'      => $student_value->image,
                    'gender'             => $student_value->gender,
                    'rank'               => $student_value->rank,
                    'terms'              => [
                        $student_value->cbse_term_id => [

                            'cbse_term_id'           => $student_value->cbse_term_id,
                            'cbse_term_name'         => $student_value->cbse_term_name,
                            'cbse_term_code'         => $student_value->cbse_term_code,
                            'cbse_term_weight'       => $student_value->cbse_template_terms_weightage,
                            'term_total_assessments' => 1,

                            'exams'                  => [
                                $student_value->id => [
                                    'name'               => $student_value->name,
                                    'total_assessments'  => 1,
                                    'total_present_days' => $student_value->total_present_days,
                                    'total_working_days' => $student_value->total_working_days,
                                    'subjects'           => [
                                        $student_value->subject_id => [
                                            'subject_id'       => $student_value->subject_id,
                                            'subject_name'     => $student_value->subject_name,
                                            'subject_code'     => $student_value->subject_code,
                                            'exam_assessments' => [
                                                $student_value->cbse_exam_assessment_type_id => [
                                                    'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                    'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                                    'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                    'maximum_marks'                  => $student_value->maximum_marks,
                                                    'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                                    'marks'                          => $student_value->marks,
                                                    'note'                           => $student_value->note,
                                                    'is_absent'                      => $student_value->is_absent,

                                                ],

                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                }
            }
        }


        $data['result']        = $students;
        $data['gradeexam_id']  = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades'] = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);
        $result_page = $this->load->view('cbseexam/report/_printsingleterm', $data, true);
        return array('pg' => $result_page);
    }

    public function getMultiTerm($cbse_template_id, $class_section_id) //for multiple terms
    {

        $data['terms'] = $this->cbseexam_template_model->getTermByTemplateId($cbse_template_id);


        $cbse_exam_result = $this->cbseexam_exam_model->getResultTermwiseByTemplateIdWithSelectedTerm($cbse_template_id, $class_section_id);


        $template_subjects         = $this->cbseexam_exam_model->getTemplateAssessment($cbse_template_id);
        $subject_array             = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id              = "";
        $remarkexam_id             = "";

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name . " (" . $cbse_exam_result_value->subject_code . ")";
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            if ((!array_key_exists($cbse_exam_result_value->cbse_term_id, $exam_term_exam_assessment))) {

                $assessment_array = $this->getExamAssesmentByTerm($template_subjects, $cbse_exam_result_value->cbse_term_id);

                $new_terms = [

                    'cbse_term_id'           => $cbse_exam_result_value->cbse_term_id,
                    'cbse_term_name'         => $cbse_exam_result_value->cbse_term_name,
                    'cbse_term_code'         => $cbse_exam_result_value->cbse_term_code,
                    'cbse_term_weight'       => $cbse_exam_result_value->weightage,
                    'term_total_assessments' => $assessment_array,
                    'exams'                  => [],

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->cbse_term_id] = $new_terms;
            }
        }

        foreach ($template_subjects as $sub_key => $sub_value) {

            if (!array_key_exists($sub_value->cbse_exam_id, $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'])) {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id] = [
                    'cbse_exam_id'     => $sub_value->cbse_exam_id,
                    'exam_name'        => $sub_value->exam_name,
                    'exam_assessments' => [[
                        'cbse_exam_assessment_id'      => $sub_value->cbse_exam_assessment_id,
                        'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                        'name'                         => $sub_value->name,
                        'code'                         => $sub_value->code,
                        'maximum_marks'                => $sub_value->maximum_marks,
                        'pass_percentage'              => $sub_value->pass_percentage,

                    ]],

                ];
            } else {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id]['exam_assessments'][] = [

                    'cbse_exam_assessment_id'      => $sub_value->cbse_exam_assessment_id,
                    'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                    'name'                         => $sub_value->name,
                    'code'                         => $sub_value->code,
                    'maximum_marks'                => $sub_value->maximum_marks,
                    'pass_percentage'              => $sub_value->pass_percentage,
                ];
            }
        }

        $data['subject_array']             = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;

        $students                          = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {
            $gradeexam_id  = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_session_id, $students)) {

                if (!array_key_exists($student_value->cbse_term_id, $students[$student_value->student_session_id]['terms'])) {

                    $new_cbse_term_id = [

                        'cbse_term_id'           => $student_value->cbse_term_id,
                        'cbse_term_name'         => $student_value->cbse_term_name,
                        'cbse_term_code'         => $student_value->cbse_term_code,
                        'cbse_term_weight'       => $student_value->cbse_template_terms_weightage,
                        'term_total_assessments' => 1,
                        'exams'                  => [
                            $student_value->id => [
                                'name'               => $student_value->name,
                                'total_assessments'  => 1,
                                'total_present_days' => $student_value->total_present_days,
                                'total_working_days' => $student_value->total_working_days,
                                'subjects'           => [
                                    $student_value->subject_id => [
                                        'subject_id'       => $student_value->subject_id,
                                        'subject_name'     => $student_value->subject_name,
                                        'subject_code'     => $student_value->subject_code,
                                        'exam_assessments' => [
                                            $student_value->cbse_exam_assessment_type_id => [
                                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                'maximum_marks'                  => $student_value->maximum_marks,
                                                'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                                'marks'                          => $student_value->marks,
                                                'note'                           => $student_value->note,
                                                'is_absent'                      => $student_value->is_absent,

                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id] = $new_cbse_term_id;
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'])) {

                    $new_exam = [
                        'name'               => $student_value->name,
                        'total_assessments'  => 1,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects'           => [
                            $student_value->subject_id => [
                                'subject_id'       => $student_value->subject_id,
                                'subject_name'     => $student_value->subject_name,
                                'subject_code'     => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks'                  => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                        'marks'                          => $student_value->marks,
                                        'note'                           => $student_value->note,
                                        'is_absent'                      => $student_value->is_absent,
                                    ],
                                ],
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id] = $new_exam;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'])) {

                    $new_subject = [
                        'subject_id'       => $student_value->subject_id,
                        'subject_name'     => $student_value->subject_name,
                        'subject_code'     => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks'                  => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                'marks'                          => $student_value->marks,
                                'note'                           => $student_value->note,
                                'is_absent'                      => $student_value->is_absent
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks'                  => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                        'marks'                          => $student_value->marks,
                        'note'                           => $student_value->note,
                        'is_absent'                      => $student_value->is_absent,

                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['total_assessments'] = count($students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments']);
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                }
            } else {

                $students[$student_value->student_session_id] = [
                    'student_id'         => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname'          => $student_value->firstname,
                    'middlename'         => $student_value->middlename,
                    'lastname'           => $student_value->lastname,
                    'mobileno'           => $student_value->mobileno,
                    'email'              => $student_value->email,
                    'religion'           => $student_value->religion,
                    'guardian_name'      => $student_value->guardian_name,
                    'guardian_phone'     => $student_value->guardian_phone,
                    'dob'                => $student_value->dob,
                    'admission_no'       => $student_value->admission_no,
                    'father_name'        => $student_value->father_name,
                    'mother_name'        => $student_value->mother_name,
                    'class_id'           => $student_value->class_id,
                    'class'              => $student_value->class,
                    'section_id'         => $student_value->section_id,
                    'section'            => $student_value->section,
                    'roll_no'            => $student_value->roll_no,
                    'student_image'      => $student_value->image,
                    'gender'             => $student_value->gender,
                    'rank'               => $student_value->rank,
                    'terms'              => [
                        $student_value->cbse_term_id => [

                            'cbse_term_id'           => $student_value->cbse_term_id,
                            'cbse_term_name'         => $student_value->cbse_term_name,
                            'cbse_term_code'         => $student_value->cbse_term_code,
                            'cbse_term_weight'       => $student_value->cbse_template_terms_weightage,
                            'term_total_assessments' => 1,

                            'exams'                  => [
                                $student_value->id => [
                                    'name'               => $student_value->name,
                                    'total_assessments'  => 1,
                                    'total_present_days' => $student_value->total_present_days,
                                    'total_working_days' => $student_value->total_working_days,
                                    'subjects'           => [
                                        $student_value->subject_id => [
                                            'subject_id'       => $student_value->subject_id,
                                            'subject_name'     => $student_value->subject_name,
                                            'subject_code'     => $student_value->subject_code,
                                            'exam_assessments' => [
                                                $student_value->cbse_exam_assessment_type_id => [
                                                    'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                    'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                                    'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                    'maximum_marks'                  => $student_value->maximum_marks,
                                                    'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                                    'marks'                          => $student_value->marks,
                                                    'note'                           => $student_value->note,
                                                    'is_absent'                      => $student_value->is_absent,

                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result']        = $students;
        $data['gradeexam_id']  = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades']   = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);

        // print_r($students);
        // exit();
        $result_page = $this->load->view('cbseexam/report/_printmultiterm', $data, true);
        return array('pg' => $result_page);
    }

    public function getSingleExam($exam_id, $class_section_id, $cbse_template_id)
    {
        $subjects         = $this->cbseexam_exam_model->getexamsubjects($exam_id);
        $exam             = $this->cbseexam_exam_model->getExamWithGrade($exam_id);
        $exam_assessments = $this->cbseexam_assessment_model->getWithAssessmentTypeByAssessmentID($exam->cbse_exam_assessment_id);
        $data['exam']             = $exam;
        $data['subjects']         = $subjects;
        $data['exam_assessments'] = $exam_assessments;
        // $cbse_template_id = $this->input->post('template_id');

        $students         = [];
        $cbse_exam_result = $this->cbseexam_exam_model->getExamResultByExamIdByTemplate($exam_id, $cbse_template_id, $class_section_id);

        // echo "<pre>";print_r($cbse_exam_result);exit;


        if (!empty($cbse_exam_result)) {

            foreach ($cbse_exam_result as $student_key => $student_value) {

                if (array_key_exists($student_value->student_session_id, $students)) {

                    if (!array_key_exists($student_value->subject_id, $students[$student_value->student_session_id]['subjects'])) {

                        $new_subject = [
                            'subject_id'       => $student_value->subject_id,
                            'subject_name'     => $student_value->subject_name,
                            'subject_code'     => $student_value->subject_code,
                            'exam_assessments' => [
                                $student_value->cbse_exam_assessment_type_id => [
                                    'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                    'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                    'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                    'maximum_marks'                  => $student_value->maximum_marks,
                                    'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                    'marks'                          => $student_value->marks,
                                    'note'                           => $student_value->note,
                                    'is_absent'                      => $student_value->is_absent,
                                ],
                            ],
                        ];

                        $students[$student_value->student_session_id]['subjects'][$student_value->subject_id] = $new_subject;
                    } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_session_id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                        $new_assesment = [
                            'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                            'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                            'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                            'maximum_marks'                  => $student_value->maximum_marks,
                            'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                            'marks'                          => $student_value->marks,
                            'note'                           => $student_value->note,
                            'is_absent'                      => $student_value->is_absent,
                        ];

                        $students[$student_value->student_session_id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    }
                } else {

                    $students[$student_value->student_session_id] = [
                        'student_id'         => $student_value->student_id,
                        'student_session_id' => $student_value->student_session_id,
                        'firstname'          => $student_value->firstname,
                        'middlename'         => $student_value->middlename,
                        'lastname'           => $student_value->lastname,
                        'mobileno'           => $student_value->mobileno,
                        'email'              => $student_value->email,
                        'religion'           => $student_value->religion,
                        'guardian_name'      => $student_value->guardian_name,
                        'guardian_phone'     => $student_value->guardian_phone,
                        'dob'                => $student_value->dob,
                        'remark'             => $student_value->remark,
                        'admission_no'       => $student_value->admission_no,
                        'father_name'        => $student_value->father_name,
                        'mother_name'        => $student_value->mother_name,
                        'class_id'           => $student_value->class_id,
                        'class'              => $student_value->class,
                        'section_id'         => $student_value->section_id,
                        'section'            => $student_value->section,
                        'roll_no'            => $student_value->roll_no,
                        'student_image'      => $student_value->image,
                        'gender'             => $student_value->gender,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'rank' => $student_value->rank,
                        'subjects'           => [
                            $student_value->subject_id => [
                                'subject_id'       => $student_value->subject_id,
                                'subject_name'     => $student_value->subject_name,
                                'subject_code'     => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks'                  => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                        'marks'                          => $student_value->marks,
                                        'note'                           => $student_value->note,
                                        'is_absent'                      => $student_value->is_absent,

                                    ],
                                ],
                            ],
                        ],
                    ];
                }
            }
        }

        $data['students'] = $students;

        // echo "<pre>";print_r($data);exit;

        $result_page = $this->load->view('cbseexam/report/_printexam', $data, true);

        return array('pg' => $result_page);
    }

    public function getMultiexam($template_id, $class_section_id)
    {
        $template_subjects         = $this->cbseexam_exam_model->getTemplateAssessmentWithoutTerm($template_id);
        $cbse_exam_result          = $this->cbseexam_exam_model->getStudentResultByTemplateId($template_id, $class_section_id);
        $subject_array             = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id              = "";
        $remarkexam_id             = "";
        $data['template']          = $this->cbseexam_template_model->getTemplateTermsOrExam($template_id);

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name . " (" . $cbse_exam_result_value->subject_code . ")";
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            if ((!array_key_exists($cbse_exam_result_value->id, $exam_term_exam_assessment))) {

                $assessment_array = $this->getExamAssesment($template_subjects, $cbse_exam_result_value->id);

                $new_terms = [

                    'exam_id'                => $cbse_exam_result_value->id,
                    'exam_name'              => $cbse_exam_result_value->name,
                    'weightage'              => $cbse_exam_result_value->weightage,
                    'exam_total_assessments' => $assessment_array,

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->id] = $new_terms;
            }
        }

        $data['subject_array']             = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;
        $students                          = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $gradeexam_id  = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_session_id, $students)) {

                if (!array_key_exists($student_value->id, $students[$student_value->student_session_id]['exams'])) {

                    $new_exam = [
                        'name'               => $student_value->name,
                        'total_assessments'  => 1,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects'           => [
                            $student_value->subject_id => [
                                'subject_id'       => $student_value->subject_id,
                                'subject_name'     => $student_value->subject_name,
                                'subject_code'     => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks'                  => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                        'marks'                          => $student_value->marks,
                                        'note'                           => $student_value->note,
                                        'is_absent'                      => $student_value->is_absent,

                                    ],
                                ],
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['exams'][$student_value->id] = $new_exam;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_session_id]['exams'][$student_value->id]['subjects'])) {

                    $new_subject = [
                        'subject_id'       => $student_value->subject_id,
                        'subject_name'     => $student_value->subject_name,
                        'subject_code'     => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks'                  => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                'marks'                          => $student_value->marks,
                                'note'                           => $student_value->note,
                                'is_absent'                      => $student_value->is_absent,
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_session_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks'                  => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                        'marks'                          => $student_value->marks,
                        'note'                           => $student_value->note,
                        'is_absent'                      => $student_value->is_absent,
                    ];

                    $students[$student_value->student_session_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                }
            } else {
                $students[$student_value->student_session_id] = [
                    'student_id'         => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname'          => $student_value->firstname,
                    'middlename'         => $student_value->middlename,
                    'lastname'           => $student_value->lastname,
                    'mobileno'           => $student_value->mobileno,
                    'email'              => $student_value->email,
                    'religion'           => $student_value->religion,
                    'guardian_name'      => $student_value->guardian_name,
                    'guardian_phone'     => $student_value->guardian_phone,
                    'dob'                => $student_value->dob,
                    'admission_no'       => $student_value->admission_no,
                    'father_name'        => $student_value->father_name,
                    'mother_name'        => $student_value->mother_name,
                    'class_id'           => $student_value->class_id,
                    'class'              => $student_value->class,
                    'section_id'         => $student_value->section_id,
                    'section'            => $student_value->section,
                    'roll_no'            => $student_value->roll_no,
                    'student_image'      => $student_value->image,
                    'gender'             => $student_value->gender,
                    'rank'               => $student_value->rank,
                    'exams'              => [
                        $student_value->id => [
                            'name'               => $student_value->name,
                            'total_assessments'  => 1,
                            'total_present_days' => $student_value->total_present_days,
                            'total_working_days' => $student_value->total_working_days,
                            'subjects'           => [
                                $student_value->subject_id => [
                                    'subject_id'       => $student_value->subject_id,
                                    'subject_name'     => $student_value->subject_name,
                                    'subject_code'     => $student_value->subject_code,
                                    'exam_assessments' => [
                                        $student_value->cbse_exam_assessment_type_id => [
                                            'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                            'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                            'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                            'maximum_marks'                  => $student_value->maximum_marks,
                                            'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                            'marks'                          => $student_value->marks,
                                            'note'                           => $student_value->note,
                                            'is_absent'                      => $student_value->is_absent,

                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result']        = $students;
        $data['gradeexam_id']  = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades']   = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);
        $result_page = $this->load->view('cbseexam/report/_printmultiexam', $data, true);
        return array('pg' => $result_page);
    }

    public function getExamAssesmentByTerm($array, $find_cbse_term_id)
    {
        $return_array = [];
        foreach ($array as $_arrry_key => $_arrry_value) {
            if ($_arrry_value->cbse_term_id == $find_cbse_term_id) {

                $return_array[] = [
                    'assesment_type_id'              => $_arrry_value->cbse_exam_assessment_type_id,
                    'assesment_type_name'            => $_arrry_value->name,
                    'assesment_type_code'            => $_arrry_value->code,
                    'assesment_type_maximum_marks'   => $_arrry_value->maximum_marks,
                    'assesment_type_pass_percentage' => $_arrry_value->pass_percentage,
                ];
            }
        }

        return $return_array;
    }

    public function getExamAssesment($array, $find_cbse_term_id)
    {
        $return_array = [];
        foreach ($array as $_arrry_key => $_arrry_value) {

            if ($_arrry_value->cbse_exam_id == $find_cbse_term_id) {

                $return_array[] = [
                    'assesment_type_id'              => $_arrry_value->cbse_exam_assessment_type_id,
                    'assesment_type_name'            => $_arrry_value->name,
                    'assesment_type_code'            => $_arrry_value->code,
                    'assesment_type_maximum_marks'   => $_arrry_value->maximum_marks,
                    'assesment_type_pass_percentage' => $_arrry_value->pass_percentage,
                ];
            }
        }

        return $return_array;
    }
    
    
    public function getClassList()
    {
        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow Only GET Request
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // Fetch Class List
        $classlist = $this->class_model->get();
    
        // API Response
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'    => true,
                'classlist' => $classlist
            ]));
    }



    public function getClassSectionExamResults()
    {
        // Handle OPTIONS Request (CORS)
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
        
        $classlist = $this->class_model->get();

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $errors = [];

        if (empty($input['class_id'])) {
            $errors['class_id'] = 'Class is required';
        }

        if (empty($input['section_id'])) {
            $errors['section_id'] = 'Section is required';
        }

        if (!empty($errors)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => $errors
                ]));
        }

        $class_id   = $input['class_id'];
        $section_id = $input['section_id'];

        /* ================= DATA CONTAINERS ================= */
        $subject_array        = [];
        $exam_term_assessment = [];
        $students             = [];

        /* ================= FETCH RESULTS ================= */
        $cbse_exam_results = $this->cbseexam_exam_model
            ->getStudentResultsByClassSection($section_id);

        /* ================= BUILD RESPONSE ================= */
        foreach ($cbse_exam_results as $result) {

            /* ---------- SUBJECT LIST ---------- */
            if (!isset($subject_array[$result->subject_id])) {
                $subject_array[$result->subject_id] = $result->subject_name;
            }

            /* ---------- EXAM / TERM ---------- */
            if (!isset($exam_term_assessment[$result->id])) {
                $exam_term_assessment[$result->id] = [
                    'exam_id'   => $result->id,
                    'exam_name' => $result->name,
                    'term_name' => $result->cbse_term_name,
                    'weightage' => $result->weightage,
                    'subject_assessments' => []
                ];
            }

            /* ---------- SUBJECT ASSESSMENT ---------- */
            if (!isset($exam_term_assessment[$result->id]['subject_assessments'][$result->subject_id])) {
                $exam_term_assessment[$result->id]['subject_assessments'][$result->subject_id] = [
                    'subject_id'   => $result->subject_id,
                    'subject_name' => $result->subject_name,
                    'assessments'  => []
                ];
            }

            $exam_term_assessment[$result->id]['subject_assessments'][$result->subject_id]['assessments'][$result->cbse_exam_assessment_type_id] = [
                'assessment_type_id'   => $result->cbse_exam_assessment_type_id,
                'assessment_type_name' => $result->cbse_exam_assessment_type_name,
                'maximum_marks'        => $result->maximum_marks,
                'marks'                => $result->marks,
                'is_absent'            => $result->is_absent,
                'note'                 => $result->note
            ];

            /* ---------- STUDENT ---------- */
            if (!isset($students[$result->student_session_id])) {
                $students[$result->student_session_id] = [
                    'student_id'   => $result->student_id,
                    'student_session_id' => $result->student_session_id,
                    'admission_no' => $result->admission_no,
                    'firstname'    => $result->firstname,
                    'lastname'     => $result->lastname,
                    'class_id'     => $result->class_id,
                    'section_id'   => $result->section_id,
                    'exams'        => []
                ];
            }

            /* ---------- STUDENT EXAM ---------- */
            if (!isset($students[$result->student_session_id]['exams'][$result->id])) {
                $students[$result->student_session_id]['exams'][$result->id] = [
                    'exam_id'   => $result->id,
                    'exam_name' => $result->name,
                    'term_name' => $result->cbse_term_name,
                    'subjects'  => []
                ];
            }

            /* ---------- STUDENT SUBJECT ---------- */
            if (!isset($students[$result->student_session_id]['exams'][$result->id]['subjects'][$result->subject_id])) {
                $students[$result->student_session_id]['exams'][$result->id]['subjects'][$result->subject_id] = [
                    'subject_id'   => $result->subject_id,
                    'subject_name' => $result->subject_name,
                    'assessments'  => []
                ];
            }

            $students[$result->student_session_id]['exams'][$result->id]['subjects'][$result->subject_id]['assessments'][$result->cbse_exam_assessment_type_id] = [
                'assessment_type_id'   => $result->cbse_exam_assessment_type_id,
                'assessment_type_name' => $result->cbse_exam_assessment_type_name,
                'marks'                => $result->marks,
                'maximum_marks'        => $result->maximum_marks,
                'is_absent'            => $result->is_absent,
                'note'                 => $result->note
            ];
        }

        /* ================= FINAL RESPONSE ================= */
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'classlist' => $classlist,
                'data'   => [
                    'class_id'             => $class_id,
                    'section_id'           => $section_id,
                    'subjects'             => $subject_array,
                    'exam_term_assessment' => array_values($exam_term_assessment),
                    'students'             => array_values($students)
                ]
            ]));
    }
    
    
    private function getPercent($max, $obtained)
    {
        if ($max == 0) return 0;
        return round(($obtained / $max) * 100, 2);
    }
    
    // private function getGrade($grade_array, $percentage)
    // {
    //     if (!empty($grade_array)) {
    //         foreach ($grade_array as $grade) {
    //             if ($percentage >= $grade->minimum_percentage) {
    //                 return $grade->name;
    //             }
    //         }
    //     }
    //     return "-";
    // }
    
    
    private function getGrade($grade_array, $percentage)
    {
        if (!empty($grade_array)) {
    
            foreach ($grade_array as $grade) {
    
                // Swap min/max safely
                $min = min($grade->minimum_percentage, $grade->maximum_percentage);
                $max = max($grade->minimum_percentage, $grade->maximum_percentage);
    
                if ($percentage >= $min && $percentage <= $max) {
                    return $grade->name;
                }
            }
        }
    
        return "-";
    }


}

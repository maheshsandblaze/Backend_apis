<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Exam extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cbse_mail_sms');
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {

        // echo "comming";exit;

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


        $data['result'] = $this->cbseexam_exam_model->getexamlist();
        $data['term_list'] = $this->cbseexam_term_model->get();
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['assessment_result'] = $this->cbseexam_assessment_model->get();
        $data['grade_result'] = $this->cbseexam_grade_model->getgradelist();

        // echo "<pre>";
        // print_r($data);
        // exit;

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'data'    => $data
            ]));
    }

    public function read()
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

        $id =  $input['id'];



        $data['result'] = $this->cbseexam_exam_model->get_exambyId($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'data'    => $data
            ]));
    }


    public function examstudent()
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

        // echo $input['examid'];exit;


        $data['sch_setting'] = $this->setting_model->getSetting();
        $examid = $input['examid'];
        $exam_class_section = $this->cbseexam_exam_model->get_class_sectionbyexamid($examid);
        $resultlist = $this->cbseexam_exam_model->searchExamStudents($exam_class_section, $examid);
        $data['exam_id'] = $examid;
        $data['resultlist'] = $resultlist;

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'data'    => $data
            ]));
        // $student_exam_page = $this->load->view('cbseexam/exam/_partialexamstudent', $data, true);
        // $array = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
        // echo json_encode($array);
    }

    public function getexamSubjects()
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


        $exam_id = $input['exam_id'];
        // $class_batch_id = $this->input->post('class_batch_id');
        // $exam_group_ids = $this->input->post('exam_group_id');
        $data['examDetail'] = $this->cbseexam_exam_model->getexamdetails($exam_id);
        $data['exam_subjects'] = $this->cbseexam_exam_model->getexamsubjects($exam_id);
        $data['batch_subjects'] = $this->subject_model->get();
        $data['exam_id'] = $exam_id;
        $data['exam_subjects_count'] = count($data['exam_subjects']);
        // $data['batch_subject_dropdown'] = $this->load->view('cbseexam/exam/_partialexamSubjectDropdown', $data, true);
        // $data['subject_page'] = $this->load->view('cbseexam/exam/_partialexamSubjects', $data, true);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'data'    => $data
            ]));
    }



    public function exam_rank()
    {


        $data = array();
        $exams = $this->cbseexam_exam_model->getexamlist();

        $data['exams'] = $exams;
        $data['title'] = 'Add Batch';
        $data['title_list'] = 'Recent Batch';

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'data'    => $data
            ]));
    }

    public function exam_ajax_rank()
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

        // Get input
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // -----------------------------
        // CALL VALIDATION FUNCTION
        // -----------------------------
        $required_fields = [
            'exam_id'          => 'Exam ID',
        ];

        $errors = validateRequired($input, $required_fields);

        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }

        // -----------------------------
        // SUCCESS
        // -----------------------------
        $exam_id          = $input['exam_id'];
        $class_section_id = $input['class_section_id'];

        $data['sch_setting'] = $this->sch_setting_detail;
        $data['exam_id']     = $exam_id;
        $data['studentList'] = $this->cbseexam_exam_model->getExamStudents($exam_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }

    public function examrankgenerate()
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


        // -----------------------------
        // CALL VALIDATION FUNCTION
        // -----------------------------
        $required_fields = [
            'exam_id'          => 'Exam ID',
        ];

        $errors = validateRequired($input, $required_fields);

        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }





        $exam_id = $input['exam_id'];
        // $student_session_ids = $input['student_session_id'];
        $this->updateExamRank($exam_id);
        $array = array('status' => 1, 'msg' => $this->lang->line('record_updated_successfully'));

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('record_updated_successfully'),
                'data'    => []
            ]));

        // echo json_encode($array);
    }



    public function updateExamRank($exam_id)
    {

        // echo $exam_id;exit;

        // $exam_id          = $this->input->post('exam_id');
        $exam             = $this->cbseexam_exam_model->getExamWithGrade($exam_id);
        $exam_assessments = $this->cbseexam_assessment_model->getWithAssessmentTypeByAssessmentID($exam->cbse_exam_assessment_id);
        $data['exam_assessments'] = $exam_assessments;
        $students = [];
        $cbse_exam_result = $this->cbseexam_exam_model->getExamResultByExamId($exam_id);

        // echo "<pre>";print_r($cbse_exam_result);exit;

        if (!empty($cbse_exam_result)) {

            foreach ($cbse_exam_result as $student_key => $student_value) {

                $exam_assessments[$student_value->cbse_exam_assessment_type_id] = $student_value->cbse_exam_assessment_type_id;

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

        if (!empty($students)) {
            //===============
            // Rank

            $student_allover_rank = [];
            $subject_rank = [];
            foreach ($students as $student_key => $student_value) {
                $total_max_marks = 0;
                $total_gain_marks = 0;

                foreach ($student_value['subjects'] as $subject_key => $subject_value) {
                    $subject_total = 0;
                    $subject_max_total = 0;

                    foreach ($subject_value['exam_assessments'] as $assessment_key => $assessment_value) {
                        $subject_total += $assessment_value['marks'];
                        $subject_max_total += $assessment_value['maximum_marks'];

                        $total_gain_marks += $assessment_value['marks'];
                        $total_max_marks += $assessment_value['maximum_marks'];
                    }

                    if (!array_key_exists($subject_key, $subject_rank)) {
                        $subject_rank[$subject_key] = [];
                    }

                    $subject_rank[$subject_key][] = [
                        'student_session_id' => $student_value['student_session_id'],
                        'rank_percentage'    => $subject_total,
                        'rank' => 0
                    ];
                }

                $exam_percentage = getPercent($total_max_marks, $total_gain_marks);
                $student_allover_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_exam_id' => $exam_id,
                    'rank_percentage' => $exam_percentage,
                    'rank' => 0,
                ];
            }

            //-=====================start term calculation Rank=============

            $rank_overall_percentage_keys = array_column($student_allover_rank, 'rank_percentage');

            array_multisort($rank_overall_percentage_keys, SORT_DESC, $student_allover_rank);

            $term_rank_allover_list = unique_array($student_allover_rank, "rank_percentage");

            foreach ($student_allover_rank as $term_rank_key => $term_rank_value) {

                $student_allover_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }

            //-=====================end term calculation Rank=============

            //===============

            // echo "<pre>";print_r($student_allover_rank);exit;

            $this->cbseexam_student_rank_model->add_exam_rank($student_allover_rank, $exam_id);
        }
    }



    public function examwiserank()
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


        // -----------------------------
        // CALL VALIDATION FUNCTION
        // -----------------------------
        $required_fields = [
            'exam_id'          => 'Exam ID',
        ];

        $errors = validateRequired($input, $required_fields);

        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }


        $data = array();
        $data['sch_setting'] = $this->sch_setting_detail;
        $exam = $input['exam_id'];
        $data['exam_id'] = $exam;

        $data['exam'] = $this->cbseexam_exam_model->get_exambyId($exam);
        $data['studentList'] = $this->cbseexam_exam_model->getExamStudents($exam);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('record_updated_successfully'),
                'data'    => $data
            ]));
    }

    public function rank()
    {


        $data = array();
        $templates = $this->cbseexam_template_model->gettemplatelist();

        // echo "<pre>";print_r($templates);exit;
        $data['templates'] = $templates;
        $data['title'] = 'Add Batch';
        $data['title_list'] = 'Recent Batch';

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                // 'message' => $this->lang->line('record_updated_successfully'),
                'data'    => $data
            ]));
    }

    public function term_wise($cbse_template_id, $students) //multiple exam in single term
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $cbse_template_subject_term_exam = $this->cbseexam_template_model->getTemplateTermExamWithAssessment($cbse_template_id);
        //==================================       
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultTermwiseByTemplateId($cbse_template_id, $students);
        $template_subjects = $this->cbseexam_exam_model->getTemplateAssessment($cbse_template_id);
        $subject_array = $cbse_template_subject_term_exam['subjects'];
        $exam_term_exam_assessment = $cbse_template_subject_term_exam['terms'];
        $gradeexam_id = "";
        $remarkexam_id = "";
        $data['subject_array'] = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;

        $students = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $gradeexam_id = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_id, $students)) {

                if (!array_key_exists($student_value->cbse_term_id, $students[$student_value->student_id]['terms'])) {
                    $new_cbse_term_id = [

                        'cbse_term_id' => $student_value->cbse_term_id,
                        'cbse_term_name' => $student_value->cbse_term_name,
                        'cbse_term_code' => $student_value->cbse_term_code,
                        'cbse_term_weight' => $student_value->cbse_template_terms_weightage,
                        'term_total_assessments' => 1,

                        'exams' => [
                            $student_value->id => [
                                'name' => $student_value->name,
                                'total_assessments' => 1,
                                'total_present_days' => $student_value->total_present_days,
                                'total_working_days' => $student_value->total_working_days,
                                'subjects' => [
                                    $student_value->subject_id => [
                                        'subject_id' => $student_value->subject_id,
                                        'subject_name' => $student_value->subject_name,
                                        'subject_code' => $student_value->subject_code,
                                        'exam_assessments' => [
                                            $student_value->cbse_exam_assessment_type_id => [
                                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                'maximum_marks' => $student_value->maximum_marks,
                                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                'marks' => $student_value->marks,
                                                'note' => $student_value->note,
                                                'is_absent' => $student_value->is_absent,

                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id] = $new_cbse_term_id;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'])) {

                    $new_exam = [
                        'name' => $student_value->name,
                        'total_assessments' => 1,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects' => [
                            $student_value->subject_id => [
                                'subject_id' => $student_value->subject_id,
                                'subject_name' => $student_value->subject_name,
                                'subject_code' => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks' => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                        'marks' => $student_value->marks,
                                        'note' => $student_value->note,
                                        'is_absent' => $student_value->is_absent,

                                    ]
                                ]
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id] = $new_exam;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'])) {


                    $new_subject = [
                        'subject_id' => $student_value->subject_id,
                        'subject_name' => $student_value->subject_name,
                        'subject_code' => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks' => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                'marks' => $student_value->marks,
                                'note' => $student_value->note,
                                'is_absent' => $student_value->is_absent,
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks' => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                        'marks' => $student_value->marks,
                        'note' => $student_value->note,
                        'is_absent' => $student_value->is_absent,
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['total_assessments'] = count($students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments']);
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                }
            } else {
                $students[$student_value->student_id] = [
                    'student_id' => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname' => $student_value->firstname,
                    'middlename' => $student_value->middlename,
                    'lastname' => $student_value->lastname,
                    'mobileno' => $student_value->mobileno,
                    'email' => $student_value->email,
                    'religion' => $student_value->religion,
                    'guardian_name' => $student_value->guardian_name,
                    'guardian_phone' => $student_value->guardian_phone,
                    'dob' => $student_value->dob,
                    'admission_no' => $student_value->admission_no,
                    'father_name' => $student_value->father_name,
                    'mother_name' => $student_value->mother_name,
                    'class_id' => $student_value->class_id,
                    'class' => $student_value->class,
                    'section_id' => $student_value->section_id,
                    'section' => $student_value->section,
                    'roll_no' => $student_value->roll_no,
                    'student_image' => $student_value->image,
                    'gender' => $student_value->gender,
                    'terms' => [
                        $student_value->cbse_term_id => [

                            'cbse_term_id' => $student_value->cbse_term_id,
                            'cbse_term_name' => $student_value->cbse_term_name,
                            'cbse_term_code' => $student_value->cbse_term_code,
                            'cbse_term_weight' => $student_value->cbse_template_terms_weightage,
                            'term_total_assessments' => 1,

                            'exams' => [
                                $student_value->id => [
                                    'name' => $student_value->name,
                                    'total_assessments' => 1,
                                    'total_present_days' => $student_value->total_present_days,
                                    'total_working_days' => $student_value->total_working_days,
                                    'subjects' => [
                                        $student_value->subject_id => [
                                            'subject_id' => $student_value->subject_id,
                                            'subject_name' => $student_value->subject_name,
                                            'subject_code' => $student_value->subject_code,
                                            'exam_assessments' => [
                                                $student_value->cbse_exam_assessment_type_id => [
                                                    'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                    'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                    'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                    'maximum_marks' => $student_value->maximum_marks,
                                                    'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                    'marks' => $student_value->marks,
                                                    'note' => $student_value->note,
                                                    'is_absent' => $student_value->is_absent,

                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]

                    ]
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result'] = $students;
        $data['gradeexam_id'] = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades'] = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);

        if (!empty($students)) {
            $student_allover_exam_rank = [];
            $subject_term_rank = [];
            foreach ($students as $student_key => $student_value) {

                $grand_total_term_percentage = 0;

                foreach ($subject_array as $subject_array_key => $subject_array_value) {
                    $subject_grand_total = 0;
                    $subject_total_exam_percentage = 0;

                    foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {
                        $subject_grand_total = 0;
                        $subject_total_exam_percentage = 0;

                        foreach ($assess_value['exams'] as $exam_key => $exam_value) {
                            $exam_subject_total = 0;
                            $exam_subject_maximum_total = 0;
                            foreach ($exam_value['exam_assessments'] as $exam_assement_key => $exam_assement_value) {

                                $subject_marks_array = getSubjectDataTerm($student_value['terms'], $assess_value['cbse_term_id'], $exam_key, $subject_array_key, $exam_assement_value['cbse_exam_assessment_type_id']);


                                if (!$subject_marks_array['marks'] <= 0 || $subject_marks_array['marks'] == "N/A") {

                                    $exam_subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
                                    $exam_subject_maximum_total += $subject_marks_array['maximum_marks'];
                                } else {

                                    $exam_subject_total += 0;
                                    $exam_subject_maximum_total += 0;
                                }
                            }
                            $subject_percentage = getPercent($exam_subject_maximum_total, $exam_subject_total);
                            $subject_total_exam_percentage += ($subject_percentage * ($exam_value['exam_weightage'] / 100));
                            $grand_total_term_percentage += ($subject_percentage * ($exam_value['exam_weightage'] / 100));
                        }
                    }

                    //===============
                    if (!array_key_exists($subject_array_key, $subject_term_rank)) {
                        $subject_term_rank[$subject_array_key] = [];
                    }

                    $subject_term_rank[$subject_array_key][] = [
                        'student_session_id' => $student_value['student_session_id'],
                        'rank_percentage' => $subject_total_exam_percentage,
                        'rank' => 0,
                        'cbse_template_id' => $cbse_template_id,

                    ];

                    //==============

                }

                $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_term_percentage);

                $student_allover_exam_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_template_id' => $cbse_template_id,
                    'rank_percentage' => $overall_percentage,
                    'rank' => 0,
                ];
            }

            //-=====================start term calculation Rank=============

            $rank_overall_term_percentage_keys = array_column($student_allover_exam_rank, 'rank_percentage');

            array_multisort($rank_overall_term_percentage_keys, SORT_DESC,  $student_allover_exam_rank);

            $term_rank_allover_list = unique_array($student_allover_exam_rank, "rank_percentage");

            foreach ($student_allover_exam_rank as $term_rank_key => $term_rank_value) {
                $student_allover_exam_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }

            //-=====================end term calculation Rank=============

            //-=====================start subject term calculation Rank=============

            foreach ($subject_term_rank as $subject_term_key => $subject_term_value) {


                $rank_overall_subject = array_column($subject_term_rank[$subject_term_key], 'rank_percentage');

                array_multisort($rank_overall_subject, SORT_DESC, $subject_term_rank[$subject_term_key]);

                $subject_rank_allover_list = unique_array($subject_term_rank[$subject_term_key], "rank_percentage");

                foreach ($subject_term_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

                    $subject_term_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
                }
            }

            $this->cbseexam_student_rank_model->add_rank($student_allover_exam_rank, $cbse_template_id, $subject_term_rank);
        }
    }

    public function all_term($cbse_template_id, $students) //for multiple terms
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $cbse_template_subject_term_exam = $this->cbseexam_template_model->getTemplateTermExamWithAssessment($cbse_template_id);
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultTermwiseByTemplateId($cbse_template_id, $students);
        $template_subjects = $this->cbseexam_exam_model->getTemplateAssessment($cbse_template_id);
        $subject_array = $cbse_template_subject_term_exam['subjects'];
        $exam_term_exam_assessment = $cbse_template_subject_term_exam['terms'];
        $gradeexam_id = "";
        $remarkexam_id = "";

        $data['subject_array'] = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;

        $students = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {
            $gradeexam_id = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_id, $students)) {

                if (!array_key_exists($student_value->cbse_term_id, $students[$student_value->student_id]['terms'])) {

                    $new_cbse_term_id = [

                        'cbse_term_id' => $student_value->cbse_term_id,
                        'cbse_term_name' => $student_value->cbse_term_name,
                        'cbse_term_code' => $student_value->cbse_term_code,
                        'cbse_term_weight' => $student_value->cbse_template_terms_weightage,
                        'term_total_assessments' => 1,

                        'exams' => [
                            $student_value->id => [
                                'name' => $student_value->name,
                                'total_assessments' => 1,
                                'total_present_days' => $student_value->total_present_days,
                                'total_working_days' => $student_value->total_working_days,
                                'subjects' => [
                                    $student_value->subject_id => [
                                        'subject_id' => $student_value->subject_id,
                                        'subject_name' => $student_value->subject_name,
                                        'subject_code' => $student_value->subject_code,
                                        'exam_assessments' => [
                                            $student_value->cbse_exam_assessment_type_id => [
                                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                'maximum_marks' => $student_value->maximum_marks,
                                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                'marks' => $student_value->marks,
                                                'note' => $student_value->note,
                                                'is_absent' => $student_value->is_absent,

                                            ]

                                        ]
                                    ]

                                ]

                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id] = $new_cbse_term_id;
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'])) {

                    $new_exam = [
                        'name' => $student_value->name,
                        'total_assessments' => 1,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects' => [
                            $student_value->subject_id => [
                                'subject_id' => $student_value->subject_id,
                                'subject_name' => $student_value->subject_name,
                                'subject_code' => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks' => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                        'marks' => $student_value->marks,
                                        'note' => $student_value->note,
                                        'is_absent' => $student_value->is_absent,
                                    ]
                                ]
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id] = $new_exam;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'])) {


                    $new_subject = [
                        'subject_id' => $student_value->subject_id,
                        'subject_name' => $student_value->subject_name,
                        'subject_code' => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks' => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                'marks' => $student_value->marks,
                                'note' => $student_value->note,
                                'is_absent' => $student_value->is_absent,
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks' => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                        'marks' => $student_value->marks,
                        'note' => $student_value->note,
                        'is_absent' => $student_value->is_absent,

                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['total_assessments'] = count($students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments']);
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                }
            } else {

                $students[$student_value->student_id] = [
                    'student_id' => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname' => $student_value->firstname,
                    'middlename' => $student_value->middlename,
                    'lastname' => $student_value->lastname,
                    'mobileno' => $student_value->mobileno,
                    'email' => $student_value->email,
                    'religion' => $student_value->religion,
                    'guardian_name' => $student_value->guardian_name,
                    'guardian_phone' => $student_value->guardian_phone,
                    'dob' => $student_value->dob,
                    'admission_no' => $student_value->admission_no,
                    'father_name' => $student_value->father_name,
                    'mother_name' => $student_value->mother_name,
                    'class_id' => $student_value->class_id,
                    'class' => $student_value->class,
                    'section_id' => $student_value->section_id,
                    'section' => $student_value->section,
                    'roll_no' => $student_value->roll_no,
                    'student_image' => $student_value->image,
                    'gender' => $student_value->gender,
                    'terms' => [
                        $student_value->cbse_term_id => [

                            'cbse_term_id' => $student_value->cbse_term_id,
                            'cbse_term_name' => $student_value->cbse_term_name,
                            'cbse_term_code' => $student_value->cbse_term_code,
                            'cbse_term_weight' => $student_value->cbse_template_terms_weightage,
                            'term_total_assessments' => 1,

                            'exams' => [
                                $student_value->id => [
                                    'name' => $student_value->name,
                                    'total_assessments' => 1,
                                    'total_present_days' => $student_value->total_present_days,
                                    'total_working_days' => $student_value->total_working_days,
                                    'subjects' => [
                                        $student_value->subject_id => [
                                            'subject_id' => $student_value->subject_id,
                                            'subject_name' => $student_value->subject_name,
                                            'subject_code' => $student_value->subject_code,
                                            'exam_assessments' => [
                                                $student_value->cbse_exam_assessment_type_id => [
                                                    'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                    'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                    'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                    'maximum_marks' => $student_value->maximum_marks,
                                                    'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                    'marks' => $student_value->marks,
                                                    'note' => $student_value->note,
                                                    'is_absent' => $student_value->is_absent,

                                                ]
                                            ]
                                        ]
                                    ]

                                ]
                            ]
                        ]

                    ]
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result'] = $students;
        $data['gradeexam_id'] = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades'] = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);

        if (!empty($students)) {

            $student_allover_term_rank = [];
            $subject_term_rank = [];
            foreach ($students as $student_key => $student_value) {
                $grand_total_marks = 0;
                $grand_total_term_percentage = 0;
                $grand_total_gain_marks = 0;
                $terms_weight_array = [];

                foreach ($subject_array as $subject_array_key => $subject_array_value) {
                    $subject_grand_total = 0;
                    $subject_total_term_percentage = 0;

                    foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {
                        $subject_total = 0;
                        $subject_maximum_total = 0;

                        foreach ($assess_value['exams'] as $exam_key => $exam_value) {
                            foreach ($exam_value['exam_assessments'] as $exam_assement_key => $exam_assement_value) {
                                $subject_marks_array = getSubjectDataTerm($student_value['terms'], $assess_value['cbse_term_id'], $exam_key, $subject_array_key, $exam_assement_value['cbse_exam_assessment_type_id']);
                                if (!$subject_marks_array['marks'] <= 0 || $subject_marks_array['marks'] == "N/A") {

                                    $subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
                                    $subject_maximum_total += $subject_marks_array['maximum_marks'];
                                } else {

                                    $subject_total += 0;
                                    $subject_maximum_total += 0;
                                }
                            }
                        }

                        if ($subject_maximum_total <= 0 && $subject_total <= 0) {
                            $subject_maximum_total = 100;
                            $subject_total = 100;
                        }

                        $subject_percentage = getPercent($subject_maximum_total, $subject_total);
                        $total_term_ = (($subject_total * 100) / $subject_maximum_total);
                        $subject_total_term_percentage += ($total_term_ * ($assess_value['cbse_term_weight'] / 100));
                        $grand_total_term_percentage += ($total_term_ * ($assess_value['cbse_term_weight'] / 100));
                        $grand_total_gain_marks += $subject_total;
                        $grand_total_marks += $subject_maximum_total;
                    }

                    //===============
                    if (!array_key_exists($subject_array_key, $subject_term_rank)) {
                        $subject_term_rank[$subject_array_key] = [];
                    }

                    $subject_term_rank[$subject_array_key][] = [
                        'student_session_id' => $student_value['student_session_id'],
                        'rank_percentage' => $subject_total_term_percentage,
                        'cbse_template_id' => $cbse_template_id,
                        'rank' => 0

                    ];
                    //==============
                }

                $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_term_percentage);

                $student_allover_term_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_template_id' => $cbse_template_id,
                    'rank_percentage' => $overall_percentage,
                    'rank' => 0,
                ];
            }

            //-=====================start term calculation Rank=============

            $rank_overall_term_percentage_keys = array_column($student_allover_term_rank, 'rank_percentage');

            array_multisort($rank_overall_term_percentage_keys, SORT_DESC, $student_allover_term_rank);

            $term_rank_allover_list = unique_array($student_allover_term_rank, "rank_percentage");

            foreach ($student_allover_term_rank as $term_rank_key => $term_rank_value) {
                $student_allover_term_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }

            //-=====================end term calculation Rank=============
            //-=====================start subject term calculation Rank=============

            foreach ($subject_term_rank as $subject_term_key => $subject_term_value) {

                $rank_overall_subject = array_column($subject_term_rank[$subject_term_key], 'rank_percentage');

                array_multisort($rank_overall_subject, SORT_DESC, $subject_term_rank[$subject_term_key]);

                $subject_rank_allover_list = unique_array($subject_term_rank[$subject_term_key], "rank_percentage");

                foreach ($subject_term_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

                    $subject_term_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
                }
            }

            //-=====================end subject term calculation Rank=============

            $this->cbseexam_student_rank_model->add_rank($student_allover_term_rank, $cbse_template_id, $subject_term_rank);
        }
    }

    public function getExamAssesment($array, $find_cbse_term_id)
    {
        $return_array = [];
        foreach ($array as $_arrry_key => $_arrry_value) {
            if ($_arrry_value->cbse_exam_id == $find_cbse_term_id) {
                $return_array[] = [
                    'assesment_type_id' => $_arrry_value->cbse_exam_assessment_type_id,
                    'assesment_type_name' => $_arrry_value->name,
                    'assesment_type_code' => $_arrry_value->code,
                    'assesment_type_maximum_marks' => $_arrry_value->maximum_marks,
                    'assesment_type_pass_percentage' => $_arrry_value->pass_percentage,
                ];
            }
        }

        return $return_array;
    }

    public function multi_exam_without_term($cbse_template_id, $students) //multiple exam without term wise
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultByTemplateId($cbse_template_id, $students);
        $template_subjects = $this->cbseexam_exam_model->getTemplateAssessmentWithoutTerm($cbse_template_id);
        $subject_array = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id = "";
        $remarkexam_id = "";
        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name . " (" . $cbse_exam_result_value->subject_code . ")";
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            if ((!array_key_exists($cbse_exam_result_value->id, $exam_term_exam_assessment))) {

                $assessment_array = $this->getExamAssesment($template_subjects, $cbse_exam_result_value->id);

                $new_terms = [

                    'exam_id' => $cbse_exam_result_value->id,
                    'exam_name' => $cbse_exam_result_value->name,
                    'weightage' => $cbse_exam_result_value->weightage,
                    'exam_total_assessments' => $assessment_array,

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->id] = $new_terms;
            }
        }

        $data['subject_array'] = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;
        $students = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $gradeexam_id = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_id, $students)) {

                if (!array_key_exists($student_value->id, $students[$student_value->student_id]['exams'])) {

                    $new_exam = [
                        'name' => $student_value->name,
                        'total_assessments' => 1,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects' => [
                            $student_value->subject_id => [
                                'subject_id' => $student_value->subject_id,
                                'subject_name' => $student_value->subject_name,
                                'subject_code' => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks' => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                        'marks' => $student_value->marks,
                                        'note' => $student_value->note,
                                        'is_absent' => $student_value->is_absent,

                                    ]

                                ]
                            ]

                        ]

                    ];

                    $students[$student_value->student_id]['exams'][$student_value->id] = $new_exam;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_id]['exams'][$student_value->id]['subjects'])) {

                    $new_subject = [
                        'subject_id' => $student_value->subject_id,
                        'subject_name' => $student_value->subject_name,
                        'subject_code' => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks' => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                'marks' => $student_value->marks,
                                'note' => $student_value->note,
                                'is_absent' => $student_value->is_absent,
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks' => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                        'marks' => $student_value->marks,
                        'note' => $student_value->note,
                        'is_absent' => $student_value->is_absent,
                    ];

                    $students[$student_value->student_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                }
            } else {
                $students[$student_value->student_id] = [
                    'student_id' => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname' => $student_value->firstname,
                    'middlename' => $student_value->middlename,
                    'lastname' => $student_value->lastname,
                    'mobileno' => $student_value->mobileno,
                    'email' => $student_value->email,
                    'religion' => $student_value->religion,
                    'guardian_name' => $student_value->guardian_name,
                    'guardian_phone' => $student_value->guardian_phone,
                    'dob' => $student_value->dob,
                    'admission_no' => $student_value->admission_no,
                    'father_name' => $student_value->father_name,
                    'mother_name' => $student_value->mother_name,
                    'class_id' => $student_value->class_id,
                    'class' => $student_value->class,
                    'section_id' => $student_value->section_id,
                    'section' => $student_value->section,
                    'roll_no' => $student_value->roll_no,
                    'student_image' => $student_value->image,
                    'gender' => $student_value->gender,
                    'exams' => [
                        $student_value->id => [
                            'name' => $student_value->name,
                            'total_assessments' => 1,
                            'total_present_days' => $student_value->total_present_days,
                            'total_working_days' => $student_value->total_working_days,
                            'subjects' => [
                                $student_value->subject_id => [
                                    'subject_id' => $student_value->subject_id,
                                    'subject_name' => $student_value->subject_name,
                                    'subject_code' => $student_value->subject_code,
                                    'exam_assessments' => [
                                        $student_value->cbse_exam_assessment_type_id => [
                                            'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                            'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                            'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                            'maximum_marks' => $student_value->maximum_marks,
                                            'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                            'marks' => $student_value->marks,
                                            'note' => $student_value->note,
                                            'is_absent' => $student_value->is_absent,

                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result'] = $students;
        $data['gradeexam_id'] = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;

        if (!empty($students)) {
            $student_allover_exam_rank = [];
            $subject_wise_rank = [];
            foreach ($students as $student_key => $student_value) {
                $grand_total_term_percentage = 0;
                $grand_total_exam_weight_percentage = 0;

                foreach ($subject_array as $subject_array_key => $subject_array_value) {
                    $subject_grand_total = 0;

                    $subject_total_weight_percentage = 0;

                    foreach ($exam_term_exam_assessment as $exam_key => $exam_value) {

                        $exam_subject_total = 0;
                        $exam_subject_maximum_total = 0;
                        foreach ($exam_value['exam_total_assessments'] as $exam_assessment_key => $exam_assessment_value) {

                            $subject_marks_array = getSubjectData($student_value, $exam_value['exam_id'], $subject_array_key, $exam_assessment_value['assesment_type_id']);

                            if (!$subject_marks_array['marks'] <= 0 || $subject_marks_array['marks'] == "N/A") {

                                $exam_subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
                                $exam_subject_maximum_total += $subject_marks_array['maximum_marks'];
                            } else {

                                $exam_subject_total += 0;
                                $exam_subject_maximum_total += 0;
                            }
                        }

                        $subject_percentage = getPercent($exam_subject_maximum_total, $exam_subject_total);
                        $subject_total_weight_percentage += ($subject_percentage * ($exam_value['weightage'] / 100));
                    }
                    if (!array_key_exists($subject_array_key, $subject_wise_rank)) {
                        $subject_wise_rank[$subject_array_key] = [];
                    }

                    $subject_wise_rank[$subject_array_key][] = [
                        'student_session_id' => $student_value['student_session_id'],
                        'rank_percentage' => $subject_total_weight_percentage,
                        'cbse_template_id' => $cbse_template_id,
                        'rank' => 0

                    ];

                    $grand_total_exam_weight_percentage += $subject_total_weight_percentage;
                }

                $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_exam_weight_percentage);

                $student_allover_exam_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_template_id' => $cbse_template_id,
                    'rank_percentage' => $overall_percentage,
                    'rank' => 0,
                ];
            }

            // //-=====================start term calculation Rank=============

            $rank_overall_term_percentage_keys = array_column($student_allover_exam_rank, 'rank_percentage');

            array_multisort($rank_overall_term_percentage_keys, SORT_DESC, $student_allover_exam_rank);

            $term_rank_allover_list = unique_array($student_allover_exam_rank, "rank_percentage");

            foreach ($student_allover_exam_rank as $term_rank_key => $term_rank_value) {
                $student_allover_exam_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }

            //-=====================end term calculation Rank=============

            //=====================start subject term calculation Rank=============

            foreach ($subject_wise_rank as $subject_term_key => $subject_term_value) {

                $rank_overall_subject = array_column($subject_wise_rank[$subject_term_key], 'rank_percentage');

                array_multisort($rank_overall_subject, SORT_DESC, $subject_wise_rank[$subject_term_key]);

                $subject_rank_allover_list = unique_array($subject_wise_rank[$subject_term_key], "rank_percentage");

                foreach ($subject_wise_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

                    $subject_wise_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
                }
            }

            $this->cbseexam_student_rank_model->add_rank($student_allover_exam_rank, $cbse_template_id, $subject_wise_rank);
        }
    }

    public function exam_wise_rank($cbse_template_id, $cbse_exam_id, $students)
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['exam'] = $this->cbseexam_exam_model->getExamWithGrade($cbse_exam_id);
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultByExamId($cbse_template_id, $cbse_exam_id, $students);
        $data['cbse_exam_result'] = $cbse_exam_result;
        $exam_assessments = [];
        $students = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $exam_assessments[$student_value->cbse_exam_assessment_type_id] = $student_value->cbse_exam_assessment_type_id;

            if (array_key_exists($student_value->student_id, $students)) {

                if (!array_key_exists($student_value->subject_id, $students[$student_value->student_id]['term']['exams'][$student_value->id]['subjects'])) {

                    $new_subject = [
                        'subject_id' => $student_value->subject_id,
                        'subject_name' => $student_value->subject_name,
                        'subject_code' => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks' => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                'marks' => $student_value->marks,
                                'note' => $student_value->note,
                                'is_absent' => $student_value->is_absent,
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['term']['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_id]['term']['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks' => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                        'marks' => $student_value->marks,
                        'note' => $student_value->note,
                        'is_absent' => $student_value->is_absent,
                    ];
                    $students[$student_value->student_id]['term']['total_assessments'] += 1;
                    $students[$student_value->student_id]['term']['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                }
            } else {
                $students[$student_value->student_id] = [
                    'student_id' => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname' => $student_value->firstname,
                    'middlename' => $student_value->middlename,
                    'lastname' => $student_value->lastname,
                    'mobileno' => $student_value->mobileno,
                    'email' => $student_value->email,
                    'religion' => $student_value->religion,
                    'guardian_name' => $student_value->guardian_name,
                    'guardian_phone' => $student_value->guardian_phone,
                    'dob' => $student_value->dob,
                    'remark' => $student_value->remark,
                    'admission_no' => $student_value->admission_no,
                    'father_name' => $student_value->father_name,
                    'mother_name' => $student_value->mother_name,
                    'class_id' => $student_value->class_id,
                    'class' => $student_value->class,
                    'section_id' => $student_value->section_id,
                    'section' => $student_value->section,
                    'roll_no' => $student_value->roll_no,
                    'student_image' => $student_value->image,
                    'gender' => $student_value->gender,
                    'total_present_days' => $student_value->total_present_days,
                    'total_working_days' => $student_value->total_working_days,
                    'term' => [
                        'cbse_term_id' => $student_value->cbse_term_id,
                        'cbse_term_name' => $student_value->cbse_term_name,
                        'cbse_term_code' => $student_value->cbse_term_code,
                        'total_assessments' => 1,
                        'exams' => [
                            $student_value->id => [
                                'name' => $student_value->name,
                                'subjects' => [
                                    $student_value->subject_id => [
                                        'subject_id' => $student_value->subject_id,
                                        'subject_name' => $student_value->subject_name,
                                        'subject_code' => $student_value->subject_code,
                                        'exam_assessments' => [
                                            $student_value->cbse_exam_assessment_type_id => [
                                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                'maximum_marks' => $student_value->maximum_marks,
                                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                'marks' => $student_value->marks,
                                                'note' => $student_value->note,
                                                'is_absent' => $student_value->is_absent,

                                            ]
                                        ]
                                    ]
                                ]

                            ]
                        ]
                    ]
                ];
            }
        }

        $data['result'] = $students;

        // echo "<pre>";
        // print_r($data['result']);exit;
        $data['exam_assessments'] = $exam_assessments;
        //========================calculate Rank=======================

        if (!empty($students)) {
            $student_allover_rank = [];
            $subject_rank = [];
            foreach ($students as $student_key => $student_value) {
                $total_max_marks = 0;
                $total_gain_marks = 0;

                foreach ($student_value['term']['exams'] as $student_exam_key => $student_exam_value) {
                    foreach ($student_exam_value['subjects'] as $subject_key => $subject_value) {
                        $subject_total = 0;
                        $subject_max_total = 0;

                        foreach ($subject_value['exam_assessments'] as $assessment_key => $assessment_value) {
                            $subject_total += $assessment_value['marks'];
                            $subject_max_total += $assessment_value['maximum_marks'];

                            $total_gain_marks += $assessment_value['marks'];
                            $total_max_marks += $assessment_value['maximum_marks'];
                        }
                        if (!array_key_exists($subject_key, $subject_rank)) {
                            $subject_rank[$subject_key] = [];
                        }

                        $subject_rank[$subject_key][] = [
                            'student_session_id' => $student_value['student_session_id'],
                            'rank_percentage' => $subject_total,
                            'cbse_template_id' => $cbse_template_id,
                            'rank' => 0

                        ];
                    }
                }

                $exam_percentage = getPercent($total_max_marks, $total_gain_marks);

                $student_allover_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_template_id' => $cbse_template_id,
                    'rank_percentage' => $exam_percentage,
                    'rank' => 0,
                ];
            }

            // echo "<pre>";
            // print_r($student_allover_rank);exit;

            //-=====================start term calculation Rank=============

            $rank_overall_percentage_keys = array_column($student_allover_rank, 'rank_percentage');

            array_multisort($rank_overall_percentage_keys, SORT_DESC, $student_allover_rank);

            $term_rank_allover_list = unique_array($student_allover_rank, "rank_percentage");

            foreach ($student_allover_rank as $term_rank_key => $term_rank_value) {

                $student_allover_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }


            // echo "<pre>";
            // print_r($student_allover_rank);exit;



            //-=====================end term calculation Rank=============

            foreach ($subject_rank as $subject_term_key => $subject_term_value) {

                $rank_overall_subject = array_column($subject_rank[$subject_term_key], 'rank_percentage');

                array_multisort($rank_overall_subject, SORT_DESC, $subject_rank[$subject_term_key]);

                $subject_rank_allover_list = unique_array($subject_rank[$subject_term_key], "rank_percentage");

                foreach ($subject_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

                    $subject_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
                }
            }

            $this->cbseexam_student_rank_model->add_rank($student_allover_rank, $cbse_template_id, $subject_rank);
        }
        //===============================================
    }

    public function rank_ajax()
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

        // -----------------------------
        // CALL VALIDATION FUNCTION
        // -----------------------------
        $required_fields = [
            'template'          => 'Template ID',
        ];

        $errors = validateRequired($input, $required_fields);

        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }







        $data['sch_setting'] = $this->sch_setting_detail;
        $template = $input['template'];
        $data['studentList'] = $this->cbseexam_result_model->getTemplateStudents($template);
        $data['cbse_template_id'] = $template;

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'data'    => $data
            ]));
    }

    public function rankgenerate()
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


        // -----------------------------
        // CALL VALIDATION FUNCTION
        // -----------------------------
        $required_fields = [
            'cbse_template_id'          => 'Template ID',
            'student_session_id'        => "Student Session ID"
        ];

        $errors = validateRequired($input, $required_fields);

        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }


        $cbse_template_id = $input['cbse_template_id'];
        $student_session_ids = $input['student_session_id'];
        $template = $this->cbseexam_template_model->get($cbse_template_id);

        // echo '<pre>';
        // print_r($template);exit;

        if ($template['marksheet_type'] == "exam_wise") {
            $cbse_temp_term_exam = $this->cbseexam_exam_model->getTemplateSingleExam($cbse_template_id);

            // echo "<pre>";   
            // print_r($cbse_temp_term_exam);exit;

            $return_page = $this->exam_wise_rank($cbse_template_id, $cbse_temp_term_exam->cbse_exam_id, $student_session_ids);
        } elseif ($template['marksheet_type'] == "without_term") {

            $return_page = $this->multi_exam_without_term($cbse_template_id, $student_session_ids);
        } elseif ($template['marksheet_type'] == "all_term") {

            $return_page = $this->all_term($cbse_template_id, $student_session_ids);
        } elseif ($template['marksheet_type'] == "term_wise") {

            $return_page = $this->term_wise($cbse_template_id, $student_session_ids);
        }


        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('record_updated_successfully'),
            ]));
    }



    public function add()
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

        // Read JSON or POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // -----------------------------
        // REQUIRED FIELD VALIDATION
        // -----------------------------
        $required_fields = [
            'exam_term_id'  => 'Term',
            'class_id'      => 'Class',
            'section'       => 'Section',
            'exam_name'     => 'Exam Name',
            'grade_id'      => 'Grade',
            'assessment_id' => 'Assessment',
            'session_id'    => "Session ID"
        ];

        $errors = validateRequired($input, $required_fields);

        // Extra check: section must be array with values
        if (isset($input['section']) && (!is_array($input['section']) || count($input['section']) === 0)) {
            $errors['section'] = 'Please select at least one section';
        }

        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }

        // -----------------------------
        // DATA PREPARATION
        // -----------------------------
        $is_publish = !empty($input['is_publish']) ? 1 : 0;
        $is_active  = !empty($input['is_active']) ? 1 : 0;

        $data = [
            'cbse_term_id'              => $input['exam_term_id'],
            'cbse_exam_assessment_id'   => $input['assessment_id'],
            'cbse_exam_grade_id'        => $input['grade_id'],
            'name'                      => $input['exam_name'],
            'description'               => $input['exam_description'] ?? null,
            'is_active'                 => $is_active,
            'is_publish'                => $is_publish,
            'created_by'                => $this->customlib->getStaffID(),
            'session_id'                => $input['session_id'],
        ];

        // -----------------------------
        // INSERT EXAM
        // -----------------------------
        $exam_id = $this->cbseexam_exam_model->add($data);

        // Insert class sections
        foreach ($input['section'] as $class_section_id) {
            $exam_class_section = [
                'cbse_exam_id'      => $exam_id,
                'class_section_id'  => $class_section_id,
            ];
            $this->cbseexam_exam_model->add_exam_class_section($exam_class_section);
        }

        // -----------------------------
        // SEND RESULT (IF PUBLISHED)
        // -----------------------------
        if ($is_publish) {
            $exam          = $this->cbseexam_exam_model->get_exambyId($exam_id);
            $exam_students = $this->cbseexam_exam_model->get_examstudents($exam_id);

            $student_exams = [
                'exam'        => $exam,
                'exam_result' => $exam_students
            ];

            // $this->cbse_mail_sms->mailsms('cbse_exam_result', $student_exams);
        }

        // -----------------------------
        // SUCCESS RESPONSE
        // -----------------------------
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message'),
                'exam_id' => $exam_id
            ]));
    }

    public function get_editdetails()

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

        // Read JSON or POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // -----------------------------
        // REQUIRED FIELD VALIDATION
        // -----------------------------
        $required_fields = [
            'id'  => 'ID',

        ];

        $errors = validateRequired($input, $required_fields);

        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }
        $id = $input['id'];

        // echo $id;exit;
        $result = $this->cbseexam_exam_model->get_editdetails($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'data' => $result
            ]));
    }

    public function remove($id = null)
    {



        // -----------------------------
        // REQUIRED FIELD VALIDATION
        // -----------------------------
        if (empty($id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'id' => 'ID is required'
                    ]
                ]));
        }

        // Optional: check record exists
        $exam = $this->cbseexam_exam_model->get_editdetails($id);
        if (!$exam) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Record not found'
                ]));
        }

        // Remove record
        $this->cbseexam_exam_model->remove($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Exam removed successfully',
                'id'      => $id
            ]));
    }


    public function add_exam()
    {

        $data = array();
        $id = $this->input->post('id');
        $result = $this->cbseexam_exam_model->get_exambyId($id);
        $data['result'] = $result;
        $data['delete_string'] = $this->input->post('delete_string');
        echo json_encode($this->load->view("cbseexam/exam/_add_exam", $data, true));
    }

    public function entrystudents()
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

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }


        $required_fields = [
            'exam_id' => 'Exam ID'
        ];

        $errors = validateRequired($input, $required_fields);

        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }

        $exam_id          = $input['exam_id'];
        $student_session  = $input['student_session_id'] ?? [];
        $all_students     = $input['all_students'] ?? 0;
        $visible_students = $input['visible_student'] ?? [];

        $insert_array = [];

        if (!empty($student_session) && is_array($student_session)) {
            foreach ($student_session as $student_value) {
                $insert_array[] = [
                    'cbse_exam_id'                => $exam_id,
                    'student_session_id'          => $student_value,
                    'reportcard_visible_student'  => isset($visible_students[$student_value]) ? 1 : 0
                ];
            }
        }

        $this->cbseexam_exam_model->add_student(
            $insert_array,
            $exam_id,
            $all_students
        );

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }


    public function addexamsubject()
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

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }


        // echo "<pre>";print_r($input);exit;


        $required_fields = [
            'exam_id' => 'Exam ID',
            'rows'    => 'Rows'
        ];

        $errors = validateRequired($input, $required_fields);
        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }

        $rows     = $input['rows'];
        $exam_id  = $input['exam_id'];
        $type_ids = $input['type_ids'] ?? [];

        if (!is_array($rows) || empty($rows)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'rows' => 'Rows must be a non-empty array'
                    ]
                ]));
        }


        $insert_array = [];
        $update_array = [];
        $not_be_del   = [];

        foreach ($rows as $row_value) {

            $subject   = $input['subject_' . $row_value]    ?? null;
            $time_from = $input['time_from' . $row_value]   ?? null;
            $duration  = $input['duration' . $row_value]    ?? null;
            $room_no   = $input['room_no_' . $row_value]    ?? null;
            $date_from = $input['date_from_' . $row_value]  ?? null;
            $update_id = $input['prev_row'][$row_value]   ?? 0;

            // Field-level validation
            if (!$subject || !$time_from || !$duration || !$room_no || !$date_from) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => [
                            'fields' => $this->lang->line('fields_values_required')
                        ]
                    ]));
            }

            $row_data = [
                'cbse_exam_id' => $exam_id,
                'subject_id'   => $subject,
                'date'         => date(
                    'Y-m-d',
                    $this->customlib->datetostrtotime($date_from)
                ),
                'time_from'    => $time_from,
                'duration'     => $duration,
                'room_no'      => $room_no
            ];

            if ((int)$update_id === 0) {
                $insert_array[] = $row_data;
            } else {
                $row_data['id'] = $update_id;
                $update_array[] = $row_data;
                $not_be_del[]   = $update_id;
            }
        }


        $this->cbseexam_exam_model->add_examsubject(
            $insert_array,
            $update_array,
            $not_be_del,
            $exam_id,
            $type_ids
        );

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }


    public function getSubjectByExam()
    {

        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // POST only
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read JSON or POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validation
        if (empty($input['recordid'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'exam_id' => 'Exam ID is required'
                    ]
                ]));
        }

        $exam_id = $input['recordid'];

        $examDetail       = $this->cbseexam_exam_model->getexamdetails($exam_id);
        $exam_subjects    = $this->cbseexam_exam_model->getexamsubjects($exam_id);
        $batch_subjects   = $this->subject_model->get();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'               => true,
                'exam_id'              => $exam_id,
                'examDetail'           => $examDetail,
                'exam_subjects'        => $exam_subjects,
                'batch_subjects'       => $batch_subjects,
                'exam_subjects_count'  => count($exam_subjects)
            ]));
    }


    public function subjectstudent()
    {
        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // POST only
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read JSON or POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validation
        $required_fields = [
            'timetable_id' => 'Timetable ID',
            'subject_id'   => 'Subject ID',
            'exam_id'      => 'Exam ID'
        ];

        $errors = validateRequired($input, $required_fields);
        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }

        $timetable_id = $input['timetable_id'];
        $subject_id   = $input['subject_id'];
        $exam_id      = $input['exam_id'];

        $examdetails = $this->cbseexam_exam_model->get_exambyId($exam_id);
        if (!$examdetails) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Exam not found'
                ]));
        }

        $resultlist = $this->cbseexam_exam_model
            ->get_markexamstudents($timetable_id, $subject_id);

        $exam_assessment_types = $this->cbseexam_exam_model
            ->get_exam_assessment_types($examdetails['cbse_exam_assessment_id']);

        $subject_detail = $this->batchsubject_model
            ->getExamSubject($subject_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'                 => true,
                'exam'                   => $examdetails,
                'subject_detail'         => $subject_detail,
                'resultlist'             => $resultlist,
                'exam_assessment_types'  => $exam_assessment_types,
                'sch_setting'            => $this->sch_setting_detail
            ]));
    }


    public function teacherRemark()
    {
        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // POST only
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }



        // Read JSON or POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validation
        if (empty($input['exam_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'exam_id' => 'Exam ID is required'
                    ]
                ]));
        }

        $exam_id = $input['exam_id'];

        $resultlist = $this->cbseexam_exam_model
            ->get_teacher_remark($exam_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => true,
                'exam_id'     => $exam_id,
                'resultlist'  => $resultlist,
                'sch_setting' => $this->sch_setting_detail
            ]));
    }

    public function entrymarks()
    {

        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // POST only
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }



        // Read JSON or POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validation
        if (empty($input['exam_student_id']) || !is_array($input['exam_student_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'exam_student_id' => 'Student list is required'
                    ]
                ]));
        }

        if (empty($input['cbse_exam_timetable_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'cbse_exam_timetable_id' => 'Timetable ID is required'
                    ]
                ]));
        }

        $cbse_exam_timetable_id = $input['cbse_exam_timetable_id'];
        $insert_array = [];

        foreach ($input['exam_student_id'] as $exam_student_id) {

            $note = $input['exam_student_note'][$exam_student_id] ?? null;

            if (!isset($input['mark'][$exam_student_id])) {
                continue;
            }

            foreach ($input['mark'][$exam_student_id] as $assessment_key => $assessment_value) {

                $absent = 0;
                if (isset($input['absent'][$exam_student_id][$assessment_key])) {
                    $absent = 1;
                    $assessment_value = 0;
                }

                $insert_array[] = [
                    'cbse_exam_timetable_id'          => $cbse_exam_timetable_id,
                    'cbse_exam_student_id'            => $exam_student_id,
                    'cbse_exam_assessment_type_id'    => $assessment_key,
                    'marks'                           => $assessment_value,
                    'is_absent'                       => $absent,
                    'note'                            => $note
                ];
            }
        }

        // Save marks
        $this->cbseexam_exam_model
            ->addresultmark_data($insert_array, $cbse_exam_timetable_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }


    /* public function exam_attendance()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_attendance', 'can_view')) {
            access_denied();
        }

        $data['exam_id'] = $this->input->post('exam_id');
        $data['exam'] = $this->cbseexam_exam_model->get_exambyId($this->input->post('exam_id'));
        $resultlist = $this->cbseexam_exam_model->get_examstudents($data['exam_id']);
        $data['resultlist'] = $resultlist;
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_exam_page = $this->load->view('cbseexam/exam/_exam_attendancestudent', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
        echo json_encode($array);
    }*/

    public function exam_attendance()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read JSON or POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validation
        if (empty($input['exam_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'exam_id' => 'Exam ID is required'
                    ]
                ]));
        }

        $exam_id   = $input['exam_id'];
        $from_date = $input['from_date'] ?? null;
        $to_date   = $input['to_date'] ?? null;

        // Convert dates if provided (d/m/Y → Y-m-d)
        if (!empty($from_date) && !empty($to_date)) {
            try {
                $from_date = DateTime::createFromFormat('d/m/Y', $from_date)->format('Y-m-d');
                $to_date   = DateTime::createFromFormat('d/m/Y', $to_date)->format('Y-m-d');
            } catch (Exception $e) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => [
                            'date' => 'Invalid date format. Use d/m/Y'
                        ]
                    ]));
            }
        }

        // Exam details
        $exam = $this->cbseexam_exam_model->get_exambyId($exam_id);
        if (!$exam) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Exam not found'
                ]));
        }

        // Exam students
        $resultlist = $this->cbseexam_exam_model->get_examstudents($exam_id);

        // Attendance calculation
        if (!empty($from_date) && !empty($to_date)) {
            foreach ($resultlist as &$student) {
                $student_session_id = $student['student_session_id'];

                $attendance = $this->cbseexam_exam_model
                    ->get_student_attendance(
                        $student_session_id,
                        $from_date,
                        $to_date
                    );

                $student['total_present_days'] =
                    $attendance['total_present_days'] ?? 0;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => true,
                'exam_id'     => $exam_id,
                'exam'        => $exam,
                'resultlist'  => $resultlist,
                'from_date'   => $from_date,
                'to_date'     => $to_date,
                'sch_setting' => $this->sch_setting_detail
            ]));
    }



    public function get_observation_parameter()
    {
        $data['exam_id'] = $this->input->post('exam_id');
        $resultlist = $this->cbseexam_exam_model->get_observation_parameter($data['exam_id']);
        $data['resultlist'] = $resultlist;
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_exam_page = $this->load->view('cbseexam/exam/_add_observation_marks', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
        echo json_encode($array);
    }

    public function addattendance()
    {

        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST
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


        if (empty($input['total_working_days']) || !is_numeric($input['total_working_days']) || $input['total_working_days'] <= 0) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'total_working_days' => $this->lang->line('total_attendance_days_should_be_greater_than_zero')
                    ]
                ]));
        }

        if (empty($input['exam_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'exam_id' => 'Exam ID is required'
                    ]
                ]));
        }

        if (empty($input['exam_student_id']) || !is_array($input['exam_student_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'exam_student_id' => 'Student list is required'
                    ]
                ]));
        }



        $exam_id               = $input['exam_id'];
        $total_working_days    = $input['total_working_days'];
        $exam_student_id       = $input['exam_student_id'];
        $total_present_days    = $input['total_present_days'] ?? [];

        $month1                = $input['month1'] ?? null;
        $month1_working_days   = $input['month1_working_days'] ?? null;
        $month1_present_days   = $input['month1_present_days'] ?? [];

        $month2                = $input['month2'] ?? null;
        $month2_working_days   = $input['month2_working_days'] ?? null;
        $month2_present_days   = $input['month2_present_days'] ?? [];

        $month3                = $input['month3'] ?? null;
        $month3_working_days   = $input['month3_working_days'] ?? null;
        $month3_present_days   = $input['month3_present_days'] ?? [];

        $examdata = [
            'id'                    => $exam_id,
            'total_working_days'    => $total_working_days,
            'month1'                => $month1,
            'month1_working_days'   => $month1_working_days,
            'month2'                => $month2,
            'month2_working_days'   => $month2_working_days,
            'month3'                => $month3,
            'month3_working_days'   => $month3_working_days,
        ];

        $this->cbseexam_exam_model->add($examdata);


        foreach ($exam_student_id as $student_id) {

            if (!isset($total_present_days[$student_id])) {
                continue;
            }

            $savedata = [
                'id'                    => $student_id,
                'total_present_days'    => $total_present_days[$student_id],
                'month1_present_days'   => $month1_present_days[$student_id] ?? 0,
                'month2_present_days'   => $month2_present_days[$student_id] ?? 0,
                'month3_present_days'   => $month3_present_days[$student_id] ?? 0
            ];

            $this->cbseexam_exam_model->addexamstudent($savedata);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Attendance saved successfully'
            ]));
    }


    public function check_teacher_remark($field, $marks)
    {
        $total_working_days = $this->input->post('total_working_days');
        if ($marks == "") {
            $this->form_validation->set_message('check_teacher_remark', $this->lang->line('student_attandance_required'));
            return false;
        } elseif ($marks > $total_working_days) {
            $this->form_validation->set_message('check_teacher_remark', $this->lang->line('student_attandance_cant_be_greater_than_total_attendance_days'));
            return false;
        }
        return true;
    }

    public function addteacherremark()
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

        if (empty($input['exam_student_id']) || !is_array($input['exam_student_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'exam_student_id' => 'Student list is required'
                    ]
                ]));
        }

        $exam_student_id = $input['exam_student_id'];
        $teacher_remark  = $input['teacher_remark'] ?? [];

        foreach ($exam_student_id as $value) {
            $savedata = [
                'id'       => $value,
                'staff_id' => $this->customlib->getStaffID(),
                'remark'   => $teacher_remark[$value] ?? null,
            ];
            $this->cbseexam_exam_model->addexamstudent($savedata);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }


    public function get_exam()
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

        if (empty($input['exam_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => ['exam_id' => 'Exam ID is required']
                ]));
        }

        $exam_id = $input['exam_id'];

        $result = $this->cbseexam_exam_model->get_exambyId($exam_id);
        if (!$result) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Exam not found'
                ]));
        }

        $class_section_list = $this->cbseexam_exam_model->get_classsectionbyId($exam_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'              => true,
                'exam'                => $result,
                'term_list'           => $this->cbseexam_term_model->get(),
                'classlist'           => $this->class_model->get(),
                'assessment_result'   => $this->cbseexam_assessment_model->get(),
                'grade_result'        => $this->cbseexam_grade_model->getgradelist(),
                'class_id'            => $class_section_list[0]['class_id'] ?? null,
                'class_section_list'  => $class_section_list,
                'sch_setting'         => $this->sch_setting_detail
            ]));
    }


    public function edit()
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

        $required = ['exam_id', 'exam_term_id', 'class_id', 'exam_name', 'grade_id'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => [$field => ucfirst(str_replace('_', ' ', $field)) . ' is required']
                    ]));
            }
        }

        $data = [
            'id'                         => $input['exam_id'],
            'cbse_term_id'               => $input['exam_term_id'],
            'cbse_exam_assessment_id'    => $input['assessment_id'] ?? null,
            'cbse_exam_grade_id'         => $input['grade_id'],
            'name'                       => $input['exam_name'],
            'description'                => $input['exam_description'] ?? null,
            'is_active'                  => !empty($input['is_active']) ? 1 : 0,
            'is_publish'                 => !empty($input['is_publish']) ? 1 : 0,
            'created_by'                 => $this->customlib->getStaffID(),
            'session_id'                 => $this->current_session,
        ];

        $this->cbseexam_exam_model->add($data);

        if (!empty($input['section'])) {
            $this->cbseexam_exam_model->removeclasssection($input['exam_id']);
            foreach ($input['section'] as $section_id) {
                $this->cbseexam_exam_model->add_exam_class_section([
                    'cbse_exam_id'     => $input['exam_id'],
                    'class_section_id' => $section_id
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

    public function deleteexam()
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

        if (empty($input['exam_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => ['exam_id' => 'Exam ID is required']
                ]));
        }

        $this->cbseexam_exam_model->remove_exam($input['exam_id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('delete_message')
            ]));
    }


    public function generate_rank()
    {
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/generate_rank');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('cbseexam/exam/generate_rank');
        $this->load->view('layout/footer');
    }

    public function examtimetable()
    {




        $exams = $this->cbseexam_exam_model->getExamTimetable();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $exams
            ]));
    }



    //This method is used to generate admit cards
    public function examwiseadmitcard($exam_id)
    {
        $data = array();
        $data['sch_setting'] = $this->sch_setting_detail;
        $exam = $exam_id;
        $data['exam_id'] = $exam;

        $data['exam'] = $this->cbseexam_exam_model->get_exambyId($exam);
        $data['studentList'] = $this->cbseexam_exam_model->getExamStudents($exam);

        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/exam/generateadmitcard', $data);
        $this->load->view('layout/footer', $data);
    }

    //This method is used to print admitcards
    // public function printadmitcard()
    // {
    //     // Handle OPTIONS Request (CORS)
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }

    //     $exam_id                 = $this->input->post('exam_id');
    //     $student_session_ids     = $this->input->post('student_session_id');
    //     $data['exam_subjects']   = $this->cbseexam_exam_model->getexamsubjects($exam_id);
    //     $data['student_details'] = $this->cbseexam_exam_model->getStudentResultByExamId($exam_id, $student_session_ids);
    //     $data['sch_setting']     = $this->sch_setting_detail;
    //     $student_admit_cards     = $this->load->view('cbseexam/exam/_printadmitcard', $data, true);
    //     $array                   = array('status' => '1', 'error' => '', 'page' => $student_admit_cards);
    //     echo json_encode($array);
    // }


    public function printadmitcard()
    {
        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow Only POST Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Accept JSON or Form Data
        $input = json_decode(file_get_contents("php://input"), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validation
        if (empty($input['exam_id']) || empty($input['student_session_id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'error'  => 'exam_id and student_session_id are required'
                ]));
        }

        $exam_id             = $input['exam_id'];
        $student_session_ids = $input['student_session_id'];

        // Fetch Data
        $exam_subjects   = $this->cbseexam_exam_model->getexamsubjects($exam_id);
        $student_details = $this->cbseexam_exam_model->getStudentResultByExamId($exam_id, $student_session_ids);
        $sch_setting     = $this->sch_setting_detail;

        // Final API Response
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'exam_subjects'   => $exam_subjects,
                    'student_details' => $student_details,
                    'school_setting'  => $sch_setting
                ]
            ]));
    }


    // bulk marks uplode
    public function importsubjectmarks()
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
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ==============================
        // FILE VALIDATION
        // ==============================
        if (!isset($_FILES["file"]) || empty($_FILES["file"]["name"])) {

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'CSV file is required'
                ]));
        }

        $fileName = $_FILES["file"]["tmp_name"];

        if ($_FILES["file"]["size"] <= 0) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Uploaded file is empty'
                ]));
        }

        $return_array = [];

        // ==============================
        // READ CSV
        // ==============================
        $file = fopen($fileName, "r");
        $flag = true;

        while (($column = fgetcsv($file, 10000, ",")) !== false) {

            // Skip header
            if ($flag) {
                $flag = false;
                continue;
            }

            // echo "<pre>";print_r($column);exit;

            if (trim($column[0]) != "" && trim($column[1]) != "") {

                $json_data = [
                    'adm_no' => $column[0]
                ];

                // Parameter 1
                if ($column[1] != "" && strtolower($column[1]) != "a") {
                    $json_data['parameter1'] = number_format($column[1], 2, '.', '');
                } elseif (strtolower($column[1]) == "a") {
                    $json_data['attendance1'] = 1;
                    $json_data['parameter1'] = 0;
                }

                // Parameter 2
                if ($column[2] != "" && strtolower($column[2]) != "a") {
                    $json_data['parameter2'] = number_format($column[2], 2, '.', '');
                } elseif (strtolower($column[2]) == "a") {
                    $json_data['attendance2'] = 1;
                    $json_data['parameter2'] = 0;
                }

                // Parameter 3
                if ($column[3] != "" && strtolower($column[3]) != "a") {
                    $json_data['parameter3'] = number_format($column[3], 2, '.', '');
                } elseif (strtolower($column[3]) == "a") {
                    $json_data['attendance3'] = 1;
                    $json_data['parameter3'] = 0;
                }

                // Parameter 4
                if ($column[4] != "" && strtolower($column[4]) != "a") {
                    $json_data['parameter4'] = number_format($column[4], 2, '.', '');
                } elseif (strtolower($column[4]) == "a") {
                    $json_data['attendance4'] = 1;
                    $json_data['parameter4'] = 0;
                }

                // Note
                if (!empty($column[5])) {
                    $json_data['note'] = trim($column[5]);
                }

                $return_array[] = $json_data;
            }
        }
        
        fclose($file);
        // ==============================
        // RESPONSE
        // ==============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'student_marks' => $return_array
            ]));
    }


    public function handle_upload()
    {
        $image_validate = $this->config->item('csv_validate');

        if (isset($_FILES["file"]) && !empty($_FILES['file']['name']) && $_FILES["file"]["size"] > 0) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            $finfo             = finfo_open(FILEINFO_MIME_TYPE);
            $mtype             = finfo_file($finfo, $_FILES['file']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mtype, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($file_size > $image_validate['upload_size']) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                return false;
            }

            return true;
        } else {
            $this->form_validation->set_message('handle_upload', $this->lang->line('the_file_field_is_required'));
            return false;
        }
    }
}

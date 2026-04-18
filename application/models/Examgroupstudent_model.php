<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Examgroupstudent_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function searchExamGroupStudentAttempted1($exam_group_id, $class_id, $batch_id)
    {
        $sql   = "select IFNULL(exam_group_students.id, 0) as `exam_group_student_id`,students.admission_no , students.id as `student_id`, students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,students.dob ,students.current_address,    students.permanent_address,students.category_id, IFNULL(categories.category, '') as `category`,   students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,`classes`.`class`,students.guardian_address,students.is_active,`students`.`father_name`,`students`.`gender`,students.batch_id,batch.name,student_session.* from student_session INNER join students on students.id=student_session.student_id JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` inner join batch on students.batch_id=batch.id INNER JOIN exam_group_students on exam_group_students.exam_group_id=" . $this->db->escape($exam_group_id) . " and exam_group_students.student_id =students.id WHERE student_session.class_id=" . $this->db->escape($class_id) . " and students.batch_id=" . $this->db->escape($batch_id) . " GROUP BY students.id ORDER BY students.id asc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function searchExamGroupStudentAttempted($exam_group_id, $exam_id, $class_id, $section_id, $session_id)
    {
        $sql   = "select IFNULL(exam_group_students.id, 0) as `exam_group_student_id`,students.admission_no , students.id as `student_id`, students.roll_no,students.admission_date,students.firstname,students.middlename, students.lastname,students.image, students.mobileno, students.email ,students.state , students.city , students.pincode , students.religion,students.dob ,students.current_address, students.permanent_address,students.category_id, IFNULL(categories.category, '') as `category`, students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,`classes`.`class`,students.guardian_address,students.is_active,`students`.`father_name`,`students`.`gender`,student_session.* from student_session INNER join students on students.id=student_session.student_id and student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_ids=" . $this->db->escape($section_id) . " and student_session.session_id=" . $this->db->escape($session_id) . " JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` INNER JOIN exam_group_students on exam_group_students.exam_group_id=" . $this->db->escape($exam_group_id) . " and exam_group_students.student_id =students.id ORDER BY students.id asc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function searchExamStudentsByExam($exam_id)
    {
        $sql = "SELECT  exam_group_class_batch_exam_students.id as `exam_group_class_batch_exam_student_id`,exam_group_class_batch_exam_students.rank,exam_group_class_batch_exam_students.roll_no as `exam_roll_no`,exam_group_class_batch_exam_students.teacher_remark,students.admission_no , students.id as `student_id`, students.roll_no,students.admission_date,students.firstname,students.middlename, students.lastname,students.image, students.mobileno, students.email ,students.state , students.city , students.pincode , students.religion,students.dob ,students.current_address, students.permanent_address,students.category_id, IFNULL(categories.category, '') as `category`, students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_email,`classes`.`class`,students.guardian_address,students.is_active,`students`.`father_name`,`students`.`gender`,`students`.`app_key`,`students`.`parent_app_key`,exam_group_class_batch_exam_students.rank,sections.section as student_section FROM `exam_group_class_batch_exam_students` INNER JOIN student_session on student_session.id=exam_group_class_batch_exam_students.student_session_id INNER join students on students.id=student_session.student_id  INNER JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` INNER JOIN `sections` ON `student_session`.`section_id` = `sections`.`id`  LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` WHERE exam_group_class_batch_exam_id=" . $this->db->escape($exam_id) . " AND students.is_active='yes' order by exam_group_class_batch_exam_students.rank asc";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function searchExamStudents($exam_group_id, $exam_id, $class_id, $section_id, $session_id)
    {
        $sql   = "SELECT  exam_group_class_batch_exam_students.id as `exam_group_class_batch_exam_student_id`,exam_group_class_batch_exam_students.rank,exam_group_class_batch_exam_students.roll_no as `exam_roll_no`,students.admission_no , students.id as `student_id`, students.roll_no,students.admission_date,students.firstname,students.middlename, students.lastname,students.image, students.mobileno, students.email ,students.state , students.city , students.pincode , students.religion,students.dob ,students.current_address, students.permanent_address,students.category_id, IFNULL(categories.category, '') as `category`, students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,`classes`.`class`,students.guardian_address,students.is_active,`students`.`father_name`,`students`.`gender` FROM `exam_group_class_batch_exam_students` INNER JOIN student_session on student_session.id=exam_group_class_batch_exam_students.student_session_id INNER join students on students.id=student_session.student_id  INNER JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` WHERE exam_group_class_batch_exam_id=" . $this->db->escape($exam_id) . " AND students.is_active='yes' AND student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_id=" . $this->db->escape($section_id) . " and student_session.session_id=" . $this->db->escape($session_id)."order by students.firstname asc";
        $query = $this->db->query($sql);

        return $query->result();
    }

    public function searchExamGroupStudents($exam_group_id, $class_id, $section_id, $session_id)
    {
        $sql   = "select IFNULL(exam_group_students.id, 0) as `exam_group_student_id`,students.admission_no , students.id as `student_id`, students.roll_no,students.admission_date,students.firstname,students.middlename, students.lastname,students.image, students.mobileno, students.email ,students.state , students.city , students.pincode , students.religion,students.dob ,students.current_address, students.permanent_address,students.category_id, IFNULL(categories.category, '') as `category`, students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,`classes`.`class`,students.guardian_address,students.is_active,`students`.`father_name`,`students`.`gender`,student_session.* from student_session INNER join students on students.id=student_session.student_id and student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_id=" . $this->db->escape($section_id) . " and student_session.session_id=" . $this->db->escape($session_id) . " JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` LEFT JOIN exam_group_students on exam_group_students.exam_group_id=" . $this->db->escape($exam_group_id) . " and exam_group_students.student_id =students.id ORDER BY students.id asc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function add($data_insert, $data_delete, $exam_group_id)
    {
        $this->db->trans_begin();
        if (!empty($data_insert)) {
            foreach ($data_insert as $student_key => $student_value) {
                $this->db->where('exam_group_id', $student_value['exam_group_id']);
                $this->db->where('student_id', $student_value['student_id']);
                $q = $this->db->get('exam_group_students');
                if ($q->num_rows() == 0) {
                    $this->db->insert('exam_group_students', $data_insert[$student_key]);
                }
            }
        }
        if (!empty($data_delete)) {
            $this->db->where('exam_group_id', $exam_group_id);
            $this->db->where_in('student_id', $data_delete);
            $this->db->delete('exam_group_students');
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function examGroupSubjectResult($exam_subject_id, $class_id, $section_id, $session_id)
    {
        $sql = "SELECT IFNULL(exam_group_exam_results.id, 0) as exam_group_exam_result_id,IFNULL(exam_group_exam_results.attendence,'') as `exam_group_exam_result_attendance`,IFNULL(exam_group_exam_results.get_marks,'') as `exam_group_exam_result_get_marks`,IFNULL(exam_group_exam_results.note,'') as `exam_group_exam_result_note`,exam_group_class_batch_exam_students.id as `exam_group_class_batch_exam_students_id`,exam_group_class_batch_exam_students.roll_no as `exam_roll_no`,exam_group_class_batch_exam_subjects.*,subjects.name,subjects.code,subjects.type,students.admission_no , students.roll_no,students.id as `student_id`, students.roll_no,students.admission_date,students.firstname,students.middlename, students.lastname,students.image, students.mobileno, students.email ,students.state , students.city , students.pincode , students.religion,students.dob ,students.current_address, students.permanent_address,students.category_id, IFNULL(categories.category, '') as `category`, students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active,`students`.`father_name`,`students`.`gender`,exam_group_class_batch_exams.use_exam_roll_no FROM `exam_group_class_batch_exam_subjects` INNER JOIN exam_group_class_batch_exams on exam_group_class_batch_exams.id=exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id INNER JOIN subjects on subjects.id=exam_group_class_batch_exam_subjects.subject_id INNER JOIN exam_group_class_batch_exam_students on exam_group_class_batch_exam_students.exam_group_class_batch_exam_id=exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id INNER join student_session on student_session.id=exam_group_class_batch_exam_students.student_session_id LEFT join exam_group_exam_results on exam_group_exam_results.exam_group_class_batch_exam_subject_id=exam_group_class_batch_exam_subjects.id and exam_group_exam_results.exam_group_class_batch_exam_student_id=exam_group_class_batch_exam_students.id  INNER JOIN students on students.id=student_session.student_id LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id`  WHERE students.is_active='yes' AND exam_group_class_batch_exam_subjects.id=" . $this->db->escape($exam_subject_id) . " and  student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_id=" . $this->db->escape($section_id) . " and student_session.session_id=" . $this->db->escape($session_id) . " ORDER BY students.id asc";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function add_result($insert_array)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        if (!empty($insert_array)) {
            foreach ($insert_array as $student_key => $student_value) {
                $student_value['exam_group_class_batch_exam_subject_id'];
                $student_value['exam_group_class_batch_exam_student_id'];
                $this->db->where('exam_group_class_batch_exam_subject_id', $student_value['exam_group_class_batch_exam_subject_id']);
                $this->db->where('exam_group_class_batch_exam_student_id', $student_value['exam_group_class_batch_exam_student_id']);
                $q = $this->db->get('exam_group_exam_results');
                if ($q->num_rows() > 0) {
                    $update_result = $q->row();
                    $this->db->where('id', $update_result->id);
                    $this->db->update('exam_group_exam_results', $student_value);
                } else {
                    $this->db->insert('exam_group_exam_results', $student_value);
                }
            }
        }

        $this->db->trans_complete(); # Completing transaction

        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            # Everything is Perfect.
            # Committing data to the database.
            $this->db->trans_commit();
            return true;
        }
    }

    public function searchStudentByClassSectionSession($class_id, $section_id, $session_id)
    {
        $sql = "SELECT students.admission_no , students.id as `student_id`, students.roll_no,students.admission_date,students.firstname, students.middlename,students.lastname,students.image, students.mobileno, students.email ,students.state , students.city , students.pincode , students.religion,students.dob ,students.current_address, students.permanent_address,students.category_id, IFNULL(categories.category, '') as `category`, students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active,`students`.`father_name`,`students`.`gender` FROM `students` LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` INNER join student_session on students.id=student_session.student_id and student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_id=" . $this->db->escape($section_id) . " and student_session.session_id=" . $this->db->escape($session_id) . " ORDER BY students.id asc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function searchStudentExams($student_session_id, $is_active = false, $is_publish = false)
    {
        $inner_sql = "";
        if ($is_active) {
            $inner_sql = "and exam_group_class_batch_exams.is_active=1 ";
        }
        if ($is_publish) {
            $inner_sql .= "and exam_group_class_batch_exams.is_publish=1 ";
        }
        $sql = "SELECT exam_group_class_batch_exam_students.*,exam_group_class_batch_exams.exam_group_id,exam_group_class_batch_exams.exam,exam_group_class_batch_exams.date_from,exam_group_class_batch_exams.date_to,exam_group_class_batch_exams.description,exam_groups.name,exam_groups.exam_type,exam_group_class_batch_exams.passing_percentage FROM `exam_group_class_batch_exam_students` INNER JOIN exam_group_class_batch_exams on exam_group_class_batch_exams.id=exam_group_class_batch_exam_students.exam_group_class_batch_exam_id  INNER JOIN exam_groups on exam_groups.id=exam_group_class_batch_exams.exam_group_id WHERE student_session_id=" . $this->db->escape($student_session_id) . $inner_sql . " ORDER BY id asc";
        $query        = $this->db->query($sql);
        $student_exam = $query->result();
        if (!empty($student_exam)) {
            foreach ($student_exam as $student_exam_key => $student_exam_value) {
                $student_exam_value->exam_result = $this->examresult_model->getStudentExamResults($student_exam_value->exam_group_class_batch_exam_id, $student_exam_value->exam_group_id, $student_exam_value->id, $student_exam_value->student_id);
            }
        }
        return $student_exam;
    }

    public function studentExams($student_session_id)
    {
        $sql = "SELECT exam_group_class_batch_exam_students.*,exam_group_class_batch_exams.id as `exam_group_class_batch_exam_id`,exam_group_class_batch_exams.exam,exam_group_class_batch_exams.description FROM `exam_group_class_batch_exam_students` INNER JOIN exam_group_class_batch_exams on exam_group_class_batch_exam_students.exam_group_class_batch_exam_id=exam_group_class_batch_exams.id WHERE student_session_id=" . $this->db->escape($student_session_id) . " and exam_group_class_batch_exams.is_active=1";
        $query        = $this->db->query($sql);
        $student_exam = $query->result();
        return $student_exam;
    }
    public function updateExamStudent($data)
    {
        $this->db->update_batch('exam_group_class_batch_exam_students', $data, 'id');
    }

    public function getexamresult($student_session_id, $exam_id, $is_active = false, $is_publish = false)
    {
        $inner_sql = "";
        if ($is_active) {
            $inner_sql = "and exam_group_class_batch_exams.is_active=1 ";
        }
        if ($is_publish) {
            $inner_sql .= "and exam_group_class_batch_exams.is_publish=1 ";
        }
        $sql = "SELECT exam_group_class_batch_exam_students.*,exam_group_class_batch_exams.exam_group_id,exam_group_class_batch_exams.exam,exam_group_class_batch_exams.passing_percentage,exam_group_class_batch_exams.date_from,exam_group_class_batch_exams.date_to,exam_group_class_batch_exams.description,exam_groups.name,exam_groups.exam_type FROM `exam_group_class_batch_exam_students` INNER JOIN exam_group_class_batch_exams on exam_group_class_batch_exams.id=exam_group_class_batch_exam_students.exam_group_class_batch_exam_id  INNER JOIN exam_groups on exam_groups.id=exam_group_class_batch_exams.exam_group_id WHERE exam_group_class_batch_exam_students.exam_group_class_batch_exam_id = " . $this->db->escape($exam_id) . " and  student_session_id=" . $this->db->escape($student_session_id) . $inner_sql . " ORDER BY id asc";

        $query        = $this->db->query($sql);
        $student_exam = $query->result();

        if (!empty($student_exam)) {
            foreach ($student_exam as $student_exam_key => $student_exam_value) {
                $student_exam_value->exam_result = $this->examresult_model->getStudentExamResults($student_exam_value->exam_group_class_batch_exam_id, $student_exam_value->exam_group_id, $student_exam_value->id, $student_exam_value->student_id);
            }
        }
        return $student_exam;
    }

    // serach state exams by student id 

    public function searchStudentstateExams($student_session_id, $is_active = false, $is_publish = false)
    {
        $student_exam =  $this->db->select('cbse_exam_students.*,cbse_template.id as cbse_template_id,cbse_template.name as cbse_template_name,cbse_exams.cbse_exam_assessment_id,cbse_exams.cbse_term_id,cbse_exams.name,cbse_exams.use_exam_roll_no,cbse_exams.is_active,cbse_exams.is_publish,cbse_exams.cbse_term_id,cbse_exams.cbse_exam_grade_id,cbse_exams.total_working_days')
            ->from('cbse_exam_students')
            ->join('student_session', 'student_session.id=cbse_exam_students.student_session_id')
            ->join('students', 'students.id=student_session.student_id')
            
            ->join('cbse_exams', 'cbse_exam_students.cbse_exam_id=cbse_exams.id')
            ->join('cbse_template', 'cbse_template.gradeexam_id=cbse_exams.id ')

            ->where('cbse_exam_students.student_session_id', $student_session_id)
            ->where('cbse_exams.is_publish', '1')
            ->order_by('cbse_exams.created_at', 'asc')
            ->get()->result();

            // echo "<pre>";
            // print_r($student_exam);exit;


        if (!empty($student_exam)) {
            foreach ($student_exam as $student_exam_key => $student_exam_value) {
            //                 echo "<pre>";
            // print_r($student_exam_value);exit;

                $student_exam_value->exam_result = $this->getExamResultByExamId($student_exam_value->cbse_exam_id,$student_exam_value->student_session_id);

                $student_exam_value->subjects = $this->getexamsubjects($student_exam_value->cbse_exam_id);
            }
        }

        // echo "<pre>";
        // print_r($student_exam);exit;
        return $student_exam;
    }

    public function getExamResultByExamId($cbse_exam_id,$student_id)
    {
        $sql   = "SELECT  `cbse_exams`.*,cbse_student_exam_ranks.rank,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_timetable.written_maximum_marks as cbse_max_marks,cbse_exam_timetable.written_maximum_marks as cbse_max_marks,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
        students.guardian_is,students.parent_id,students.admission_no,
        students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id`  FROM `cbse_exams` INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join classes on student_session.class_id = classes.id INNER join sections on sections.id = student_session.section_id left join cbse_student_exam_ranks on cbse_student_exam_ranks.student_session_id = student_session.id and cbse_student_exam_ranks.cbse_exam_id=" . $cbse_exam_id . " WHERE cbse_exams.`id` = " . $this->db->escape($cbse_exam_id) . "  and cbse_exam_timetable.`cbse_exam_id` = " . $this->db->escape($cbse_exam_id) . " and cbse_exams.session_id=" . $this->current_session . " and cbse_exam_students.student_session_id = ".$student_id."  ORDER BY  subjects.id,classes.id,sections.id,CAST(students.roll_no AS UNSIGNED) asc;";
        $query = $this->db->query($sql);
        return $query->result();
    }

    // exam subjects 

    public function getexamsubjects($exam_id)
    {
        return $this->db->select('cbse_exam_timetable.*, subjects.name as subject_name, subjects.code as subject_code')
            ->from('cbse_exam_timetable')
            ->join('subjects', 'subjects.id = cbse_exam_timetable.subject_id')
            ->where('cbse_exam_id', $exam_id)

            ->order_by('cbse_exam_timetable.id')

            ->get()
            ->result();
    }

    public function getExamWithGrade($id)
    {
        $result = $this->db->select('*')->from('cbse_exams')->where('id', $id)->get()->row();
        $result->grades = $this->db->select('*')->from('cbse_exam_grades_range')->where('cbse_exam_grade_id', $result->cbse_exam_grade_id)->get()->result();
        return $result;
    }

    

}

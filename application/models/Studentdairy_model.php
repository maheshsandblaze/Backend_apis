<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentdairy_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"]) && $data["id"] > 0) {
            $this->db->where("id", $data["id"])->update("studentdairy", $data);
            // echo $this->db->last_query();exit;

            // $message   = UPDATE_RECORD_CONSTANT . " On studentdairy id " . $data['id'];
            // $action    = "Update";
            // $record_id = $insert_id = $data['id'];
            // $this->log($message, $record_id, $action);
        } else {

            $this->db->insert("studentdairy", $data);
            // echo $this->db->last_query();exit;
            $insert_id = $this->db->insert_id();
            // $message   = INSERT_RECORD_CONSTANT . " On studentdairy id " . $insert_id;
            // $action    = "Insert";
            // $record_id = $insert_id;
            // $this->log($message, $record_id, $action);
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        } 
    }

    public function get($id = null)
    {
        $class  = $this->class_model->get();
        $carray = array();
        foreach ($class as $key => $value) {
            $carray[] = $value['id'];
            $sections = $this->section_model->getClassBySection($value['id']);

            foreach ($sections as $sec => $secdata) {
                $section_array[] = $secdata['section_id'];
            }
        }

        if (!empty($id)) {
            $this->db->select("`studentdairy`.*");
            $this->db->join("classes", "classes.id = studentdairy.class_id");
            $this->db->join("sections", "sections.id = studentdairy.section_id");

            $this->db->where('studentdairy.session_id', $this->current_session);
            $this->db->where("studentdairy.id", $id);

            $query = $this->db->get("studentdairy");
            return $query->row_array();
        } else {

            $this->db->select("`studentdairy`.*");
            $this->db->join("classes", "classes.id = studentdairy.class_id");
            $this->db->join("sections", "sections.id = studentdairy.section_id");

            $this->db->where('studentdairy.session_id', $this->current_session);
            if (!empty($carray)) {
                $this->db->where_in('classes.id', $carray);
            }
            if (!empty($section_array)) {
                $this->db->where_in('sections.id', $section_array);
            }
            $query = $this->db->get("studentdairy");
            return $query->result_array();
        }
    }

    public function get_studentdairyDocById($studentdairy_id)
    {
        $this->datatables
            ->select('students.*,submit_assignment.docs,submit_assignment.message,submit_assignment.student_id')
            ->join('students', 'students.id=submit_assignment.student_id', 'inner')
            ->searchable('students.firstname,submit_assignment.message," "')
            ->orderable('students.firstname,submit_assignment.message," "')
            ->from('submit_assignment')
            ->where(array('submit_assignment.studentdairy_id' => $studentdairy_id));
        return $this->datatables->generate('json');
    }

    public function get_studentdairyDocByIdStdid($studentdairy_id, $student_id)
    {
        $query = $this->db->select('students.*,submit_assignment.docs,submit_assignment.message')->from('submit_assignment')->join('students', 'students.id=submit_assignment.student_id', 'inner')->where(array('submit_assignment.studentdairy_id' => $studentdairy_id, 'submit_assignment.student_id' => $student_id))->get();
        return $query->result_array();
    }

    public function search_studentdairy($class_id, $section_id, $subject_group_id, $subject_id)
    {
        if ((!empty($class_id)) && (!empty($section_id)) && (!empty($subject_id)) && (!empty($subject_group_id))) {
            $this->db->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id, 'subject_groups.id' => $subject_group_id, 'subject_group_subjects.id' => $subject_id));
        } else if ((!empty($class_id)) && (!empty($section_id)) && (!empty($subject_group_id))) {
            $this->db->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id, 'subject_groups.id' => $subject_group_id));
        } else if ((!empty($class_id)) && (empty($section_id)) && (empty($subject_id))) {
            $this->db->where(array('studentdairy.class_id' => $class_id));
        } else if ((!empty($class_id)) && (!empty($section_id)) && (empty($subject_id))) {
            $this->db->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id));
        }

        $this->db->select("`studentdairy`.*,classes.class,sections.section,subject_group_subjects.subject_id,subject_group_subjects.id as `subject_group_subject_id`,subjects.name as subject_name,subjects.code as subject_code,subject_groups.id as subject_groups_id,subject_groups.name,(select count(*) as total from submit_assignment where submit_assignment.studentdairy_id=studentdairy.id) as assignments");
        $this->db->join("classes", "classes.id = studentdairy.class_id");
        $this->db->join("sections", "sections.id = studentdairy.section_id");
        $this->db->join("subject_group_subjects", "subject_group_subjects.id = studentdairy.subject_group_subject_id");
        $this->db->join("subjects", "subjects.id = subject_group_subjects.subject_id");
        $this->db->join("subject_groups", "subject_group_subjects.subject_group_id=subject_groups.id");
        $this->db->where('subject_groups.session_id', $this->current_session);
        $this->db->order_by('studentdairy.studentdairy_date', 'DESC');
        $query = $this->db->get("studentdairy");
        return $query->result_array();
    }

    public function search_dtstudentdairy($class_id, $section_id)
    {
        if ((!empty($class_id)) && (!empty($section_id)) ) {
            $this->datatables->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id));

        } else if ((!empty($class_id)) && (!empty($section_id)) ) {
            $this->datatables->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id));
        } else if ((!empty($class_id)) && (empty($section_id)) ) {
            $this->datatables->where(array('studentdairy.class_id' => $class_id));
        } else if ((!empty($class_id)) && (!empty($section_id))) {
            $this->datatables->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id));
        }

        $this->datatables->select('`studentdairy`.*,classes.class,sections.section')
            ->searchable('`studentdairy`.*,classes.class,sections.section')
            ->join("classes", "classes.id = studentdairy.class_id")
            ->join("sections", "sections.id = studentdairy.section_id")
            ->orderable('`studentdairy`.*,classes.class,sections.section')
            ->where('studentdairy.session_id', $this->current_session)
            ->where('studentdairy.date >=', date("Y-m-d"))
            ->from('studentdairy');
        return $this->datatables->generate('json');

    }

    public function search_closestudentdairy($class_id, $section_id, $subject_group_id, $subject_id)
    {
        if ((!empty($class_id)) && (!empty($section_id)) && (!empty($subject_id)) && (!empty($subject_group_id))) {
            $this->datatables->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id, 'subject_groups.id' => $subject_group_id, 'subject_group_subjects.id' => $subject_id));
        } else if ((!empty($class_id)) && (!empty($section_id)) && (!empty($subject_group_id))) {
            $this->datatables->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id, 'subject_groups.id' => $subject_group_id));
        } else if ((!empty($class_id)) && (empty($section_id)) && (empty($subject_id))) {
            $this->datatables->where(array('studentdairy.class_id' => $class_id));
        } else if ((!empty($class_id)) && (!empty($section_id)) && (empty($subject_id))) {
            $this->datatables->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id));
        }

        $this->datatables->select('`studentdairy`.*,classes.class,sections.section,subject_group_subjects.subject_id,subject_group_subjects.id as `subject_group_subject_id`,subjects.name as subject_name,subjects.code as subject_code,subject_groups.id as subject_groups_id,subject_groups.name,(select count(*) as total from submit_assignment where submit_assignment.studentdairy_id=studentdairy.id) as assignments,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,staff_roles.role_id')
            ->searchable('classes.class,sections.section,subject_groups.name,subjects.name,studentdairy_date,submit_date,evaluation_date,staff.name')
            ->join("classes", "classes.id = studentdairy.class_id")
            ->join("sections", "sections.id = studentdairy.section_id")
            ->join("subject_group_subjects", "subject_group_subjects.id = studentdairy.subject_group_subject_id")
            ->join("subjects", "subjects.id = subject_group_subjects.subject_id")
            ->join("subject_groups", "subject_group_subjects.subject_group_id=subject_groups.id")
            ->join("staff", "studentdairy.created_by=staff.id")
            ->join("staff_roles", "staff_roles.staff_id=staff.id")
            ->orderable('" ",classes.class,sections.section,subject_groups.name,subjects.name,studentdairy_date,submit_date,evaluation_date,staff.name')
            ->where('subject_groups.session_id', $this->current_session)
            ->where('studentdairy.submit_date <', date("Y-m-d"))
            ->sort('studentdairy.studentdairy_date', 'DESC')
            ->from('studentdairy');
        return $this->datatables->generate('json');
    }

    public function getRecord($id = null)
    {
        $query =
        $this->db->select('`studentdairy`.*,classes.class,sections.section')
            ->join("classes", "classes.id = studentdairy.class_id")
            ->join("sections", "sections.id = studentdairy.section_id")
            ->where("studentdairy.id", $id)
            ->get("studentdairy");
        return $query->row_array();
    }

    public function getStudents($id)
    {
        $sql = "SELECT IFNULL(studentdairy_evaluation.id,0) as studentdairy_evaluation_id,studentdairy_evaluation.note,studentdairy_evaluation.marks,student_session.*,students.firstname,students.middlename,students.lastname,students.admission_no from student_session inner JOIN (SELECT studentdairy.id as studentdairy_id,studentdairy.class_id,studentdairy.section_id,studentdairy.session_id FROM `studentdairy` WHERE id= " . $this->db->escape($id) . " ) as home_work on home_work.class_id=student_session.class_id and home_work.section_id=student_session.section_id and home_work.session_id=student_session.session_id inner join students on students.id=student_session.student_id and students.is_active='yes' left join studentdairy_evaluation on studentdairy_evaluation.student_session_id=student_session.id and students.is_active='yes' and studentdairy_evaluation.studentdairy_id=" . $this->db->escape($id) . "   order by students.id desc";    
        $query = $this->db->query($sql);
        $studentlist = $query->result_array();
        foreach ($studentlist as $key => $student_list_value) {
            $studentlist[$key]['assignmentlist'] = $this->get_studentdairyassignmentById($id, $student_list_value['student_id']);
        }

        return $studentlist;

    }

    public function get_studentdairyassignmentById($studentdairy_id, $student_id)
    {
        $this->db->select('submit_assignment.id as submit_assignment_id ,submit_assignment.docs,submit_assignment.message,submit_assignment.student_id');
        $this->db->join('students', 'students.id=submit_assignment.student_id');
        $this->db->from('submit_assignment');
        $this->db->where('submit_assignment.studentdairy_id', $studentdairy_id);
        $this->db->where('submit_assignment.student_id', $student_id);
        $result = $this->db->get();
        return $result->result_array();
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("studentdairy");

        // echo $this->db->last_query();exit;

        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function addEvaluation($insert_prev, $insert_array, $studentdairy_id, $evaluation_date, $evaluated_by, $update_array)
    {
        
        $studentdairy = array('evaluation_date' => $evaluation_date, 'evaluated_by' => $evaluated_by);
        $this->db->where("id", $studentdairy_id)->update("studentdairy", $studentdairy);

        if (!empty($insert_array)) {

            foreach ($insert_array as $insert_student_key => $insert_student_value) {

                $insert_student = array(
                    'studentdairy_id'        => $studentdairy_id,
                    'student_session_id' => $insert_student_value['student_session_id'],
                    'note'               => $insert_student_value['note'],
                    'marks'              => $insert_student_value['marks'],
                    'student_id'         => $insert_student_value['student_id'],
                    'date'               => $evaluation_date,
                    'status'             => 'Complete',
                );

                $this->db->insert("studentdairy_evaluation", $insert_student);
                $insert_prev[] = $this->db->insert_id();
            }
        }

        if (!empty($update_array)) {
            foreach ($update_array as $parameter_key => $parameter_value) {

                foreach ($parameter_value as $row) {
                    $update_student = array(
                        'note'  => $row['note'],
                        'marks' => $row['marks'],

                    );
                }
                $this->db->where("id", $parameter_key)->update("studentdairy_evaluation", $update_student);
            }
        }
        $this->db->where('studentdairy_id', $studentdairy_id);
        $this->db->where_not_in('id', $insert_prev);
        $this->db->delete('studentdairy_evaluation');
        
    }

    public function searchstudentdairyEvaluation($class_id, $section_id, $subject_id)
    {
        if ((!empty($class_id)) && (!empty($section_id)) && (!empty($subject_id))) {
            $this->db->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id, 'studentdairy.subject_id' => $subject_id));
        } else if ((!empty($class_id)) && (empty($section_id)) && (empty($subject_id))) {
            $this->db->where(array('studentdairy.class_id' => $class_id));
        } else if ((!empty($class_id)) && (!empty($section_id)) && (empty($subject_id))) {
            $this->db->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id));
        }

        $query = $this->db->select('studentdairy.*,classes.class,sections.section,subjects.name')
            ->join('classes', 'classes.id = studentdairy.class_id')
            ->join('sections', 'sections.id = studentdairy.section_id')
            ->join('subjects', 'subjects.id = studentdairy.subject_id')
            ->where_in('studentdairy.id', 'select studentdairy_evaluation.studentdairy_id from studentdairy_evaluation join studentdairy on (studentdairy_evaluation.studentdairy_id = studentdairy.id) group by studentdairy_evaluation.studentdairy_id')
            ->get('studentdairy');
        return $query->result_array();
    }

    public function getEvaluationReport($id)
    {
        $query = $this->db->select("studentdairy.*,studentdairy_evaluation.student_id,studentdairy_evaluation.id as evalid,studentdairy_evaluation.date,studentdairy_evaluation.status,classes.class,subjects.name,sections.section,(select count(*) as total from submit_assignment sa where sa.studentdairy_id=studentdairy.id) as assignments")->join("classes", "classes.id = studentdairy.class_id")->join("sections", "sections.id = studentdairy.section_id")->join("subjects", "subjects.id = studentdairy.subject_id")->join("studentdairy_evaluation", "studentdairy.id = studentdairy_evaluation.studentdairy_id")->where("studentdairy.id", $id)->get("studentdairy");
        return $query->result_array();
    }

    public function getEvaStudents($id, $class_id, $section_id)
    {
        $query = $this->db->select("students.*,studentdairy_evaluation.student_id,studentdairy_evaluation.date,studentdairy_evaluation.status,classes.class,subjects.name,sections.section")->join("classes", "classes.id = studentdairy.class_id")->join("sections", "sections.id = studentdairy.section_id")->join("subjects", "subjects.id = studentdairy.subject_id")->join("studentdairy_evaluation", "studentdairy.id = studentdairy_evaluation.studentdairy_id")->join("students", "students.id = studentdairy_evaluation.student_id", "left")->where("studentdairy.id", $id)->get("studentdairy");
        return $query->result_array();
    }

    public function delete_evaluation($prev_students)
    {
        if (!empty($prev_students)) {
            $this->db->where_in("id", $prev_students)->delete("studentdairy_evaluation");
        }
    }

    public function count_students($class_id, $section_id)
    {
        $query = $this->db->select("student_session.student_id")->join("student_session", "students.id = student_session.student_id")->where(array('student_session.class_id' => $class_id, 'student_session.section_id' => $section_id, 'students.is_active' => "yes", 'student_session.session_id' => $this->current_session))->group_by("student_session.student_id")->get("students");
        return $query->num_rows();
    }

    public function count_evalstudents($id, $class_id, $section_id)
    {
        $array['studentdairy.id']         = $id;
        $array['studentdairy.session_id'] = $this->current_session;
        $array['students.is_active']  = 'yes';
        $query = $this->db->select("count(*) as total")->join("studentdairy_evaluation", "studentdairy_evaluation.studentdairy_id = studentdairy.id")->join('student_session', 'student_session.id=studentdairy_evaluation.student_session_id')->join('students', 'students.id=student_session.student_id')->where($array)->get("studentdairy");
        return $query->row_array();
    }

    public function get_studentdairyDoc($student_id)
    {
        return $this->db->select('*')->from('submit_assignment')->where('student_id', $student_id)->get()->result_array();
    }

    public function get_studentdairyDocBystudentdairy_id($studentdairy_id)
    {
        return $this->db->select('*')->from('submit_assignment')->where('studentdairy_id', $studentdairy_id)->get()->result_array();
    }
    public function getStudentstudentdairyWithStatus($class_id, $section_id, $student_session_id)
    {
        $sql   = "SELECT `studentdairy`.*, `classes`.`class`, `sections`.`section` 
                  FROM `studentdairy` 
                  JOIN `classes` ON `classes`.`id` = `studentdairy`.`class_id` 
                  JOIN `sections` ON `sections`.`id` = `studentdairy`.`section_id` 
                  WHERE `studentdairy`.`class_id` = " . $this->db->escape($class_id) . " 
                  AND `studentdairy`.`section_id` = " . $this->db->escape($section_id) . " 
                  AND `studentdairy`.`session_id` = " . $this->current_session . " 
                  AND `date` >= '" . date('Y-m-d') . "' 
                  ORDER BY `studentdairy`.`date` DESC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    

    public function getstudentclosedstudentdairywithstatus($class_id, $section_id, $student_session_id)
    {
        $sql   = "SELECT `studentdairy`.*,IFNULL(studentdairy_evaluation.id,0) as studentdairy_evaluation_id,studentdairy_evaluation.note,studentdairy_evaluation.marks as evaluation_marks, `classes`.`class`, `sections`.`section`, `subject_group_subjects`.`subject_id`, `subject_group_subjects`.`id` as `subject_group_subject_id`, `subjects`.`name` as `subject_name`, `subjects`.`code` as `subject_code`,  `subject_groups`.`id` as `subject_groups_id`, `subject_groups`.`name` FROM `studentdairy` LEFT JOIN studentdairy_evaluation on studentdairy_evaluation.studentdairy_id=studentdairy.id and studentdairy_evaluation.student_session_id=" . $this->db->escape($student_session_id) . "  JOIN `classes` ON `classes`.`id` = `studentdairy`.`class_id` JOIN `sections` ON `sections`.`id` = `studentdairy`.`section_id` JOIN `subject_group_subjects` ON `subject_group_subjects`.`id` = `studentdairy`.`subject_group_subject_id` JOIN `subjects` ON `subjects`.`id` = `subject_group_subjects`.`subject_id` JOIN `subject_groups` ON `subject_group_subjects`.`subject_group_id`=`subject_groups`.`id` WHERE `studentdairy`.`class_id` = " . $this->db->escape($class_id) . " AND `studentdairy`.`section_id` = " . $this->db->escape($section_id) . " AND `studentdairy`.`session_id` = " . $this->current_session . " and submit_date < '" . date('Y-m-d') . "' order by studentdairy.studentdairy_date desc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getStudentstudentdairy($class_id, $section_id)
    {
        $this->db->select("`studentdairy`.*,classes.class,sections.section,subject_group_subjects.subject_id,subject_group_subjects.id as `subject_group_subject_id`,subjects.name as subject_name,subject_groups.id as subject_groups_id,subject_groups.name,(select count(*) as total from submit_assignment where submit_assignment.studentdairy_id=studentdairy.id) as assignments");
        $this->db->join("classes", "classes.id = studentdairy.class_id");
        $this->db->join("sections", "sections.id = studentdairy.section_id");
        $this->db->join("subject_group_subjects", "subject_group_subjects.id = studentdairy.subject_group_subject_id");
        $this->db->join("subjects", "subjects.id = subject_group_subjects.subject_id");
        $this->db->join("subject_groups", "subject_group_subjects.subject_group_id=subject_groups.id");
        $this->db->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id));
        $this->db->where('studentdairy.session_id', $this->current_session);
        $query = $this->db->get("studentdairy");
        return $query->result_array();       
    }

    public function get_studentdairySubject($subjectgroup_id)
    {
        return $this->db->select('subjects.name as subject,subjects.code')->from('subject_group_subjects')->join('subjects', 'subject_group_subjects.subject_id=subjects.id')->where('subject_group_subjects.subject_group_id', $subjectgroup_id)->get()->result_array();
    }

    public function upload_docs($data)
    {
        $this->db->where('studentdairy_id', $data['studentdairy_id']);
        $this->db->where('student_id', $data['student_id']);
        $q = $this->db->get('submit_assignment');
        if ($q->num_rows() > 0) {
            $this->db->where('studentdairy_id', $data['studentdairy_id']);
            $this->db->where('student_id', $data['student_id']);
            $this->db->update('submit_assignment', $data);
        } else {
            $this->db->insert('submit_assignment', $data);
        }
    }

    public function get_upload_docs($array)
    {
        return $this->db->select('*')->from('submit_assignment')->where($array)->get()->result_array();
    }

    public function getEvaluationReportForStudent($id, $student_id)
    {
        $query = $this->db->select("studentdairy.*,studentdairy_evaluation.student_id,studentdairy_evaluation.id as evalid,studentdairy_evaluation.date,studentdairy_evaluation.status,studentdairy_evaluation.student_id,classes.class,sections.section")->join("classes", "classes.id = studentdairy.class_id")->join("sections", "sections.id = studentdairy.section_id")->join("studentdairy_evaluation", "studentdairy.id = studentdairy_evaluation.studentdairy_id")->where("studentdairy.id", $id)->get("studentdairy");      
        $result = $query->result_array();
        foreach ($result as $key => $value) {
            if ($value["student_id"] == $student_id) {
                return $value;
            } else {
                $data = array('date' => $value["date"], 'status' => 'Incomplete');
                return $data;
            }
        }
    }

    public function get_studentdairyDocBystudentId($studentdairy_id, $student_id)
    {
        $where = array('submit_assignment.studentdairy_id' => $studentdairy_id, 'submit_assignment.student_id' => $student_id);
        $query = $this->db->select('students.*,submit_assignment.docs,submit_assignment.message')->from('submit_assignment')->join('students', 'students.id=submit_assignment.student_id', 'inner')->where($where)->get();
        return $query->result_array();
    }

    public function check_assignment($studentdairy_id, $student_id)
    {
        $status = $this->db->select('*')->from('submit_assignment')->where(array('studentdairy_id' => $studentdairy_id, 'student_id' => $student_id))->get()->num_rows();
        return $status;
    }

    public function adddailyassignment($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"]) && $data["id"] > 0) {
            $this->db->where("id", $data["id"])->update("daily_assignment", $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Daily Assignment id " . $data['id'];
            $action    = "Update";
            $record_id = $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("daily_assignment", $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Daily Assignment id " . $insert_id;
            $action    = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    public function getdailyassignment($student_id, $student_session_id)
    {
        return $this->db->select('daily_assignment.*,subjects.name as subject_name,`subjects`.`code` as `subject_code`')
            ->from('daily_assignment')
            ->join('student_session', 'student_session.student_id=daily_assignment.student_session_id', 'left')
            ->join('subject_group_subjects', 'subject_group_subjects.id=daily_assignment.subject_group_subject_id', 'left')
            ->join('subjects', 'subjects.id=subject_group_subjects.subject_id')
            ->where('daily_assignment.student_session_id', $student_session_id)
            ->or_where('student_session.student_id', $student_id)
            ->order_by('daily_assignment.id', 'desc')
            ->group_by('daily_assignment.id')
            ->get()
            ->result_array();
    }

    public function getsingledailyassignment($assignment_id)
    {
        return $this->db->select('daily_assignment.*')->from('daily_assignment')->where('daily_assignment.id', $assignment_id)->get()->row_array();
    }

    public function deletedailyassignment($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("daily_assignment");
        $message   = DELETE_RECORD_CONSTANT . " On Daily Assignment id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function searchdailyassignment($class_id, $section_id, $subject_group_id, $subject_group_subject_id, $date)
    {
        if ((!empty($class_id)) && (!empty($section_id)) && (!empty($date))) {
            $this->datatables->where(array('student_session.class_id' => $class_id, 'student_session.section_id' => $section_id, 'daily_assignment.date' => $date, 'subject_group_subjects.subject_group_id' => $subject_group_id, 'subject_group_subjects.id' => $subject_group_subject_id));
        }

        $this->datatables->select('daily_assignment.*,staff.name,staff.surname,staff.employee_id,classes.class,sections.section,students.firstname,students.middlename,students.lastname,students.id as student_id,students.admission_no as student_admission_no,subjects.name as subject_name,subjects.code as subject_code')
            ->searchable('students.firstname,classes.class,sections.section,daily_assignment.title,daily_assignment.date,daily_assignment.description')
            ->join("student_session", "student_session.id = daily_assignment.student_session_id")
            ->join("classes", "classes.id = student_session.class_id")
            ->join("sections", "sections.id = student_session.section_id")
            ->join("students", "students.id = student_session.student_id")
            ->join("subject_group_subjects", "subject_group_subjects.id = daily_assignment.subject_group_subject_id")
            ->join("subjects", "subjects.id = subject_group_subjects.subject_id")
            ->join("staff", "staff.id = `daily_assignment`.`evaluated_by`", "left")
            ->orderable('students.firstname,classes.class,sections.section,daily_assignment.description,daily_assignment.remark,daily_assignment.date,daily_assignment.evaluation_date')
            ->sort('students.firstname,classes.class,sections.section,daily_assignment.title,daily_assignment.date,daily_assignment.description', 'DESC')
            ->from('daily_assignment');
        return $this->datatables->generate('json');
    }   

    public function checkstatus($studentdairy_id, $student_id)
    {
        return $this->db->select('count(submit_assignment.id) as record_count')->from('submit_assignment')
            ->where('submit_assignment.studentdairy_id', $studentdairy_id)->where('submit_assignment.student_id', $student_id)->get()->row_array();
    }

    public function getStudentlist($id)
    {
        $sql = "SELECT IFNULL(studentdairy_evaluation.id,0) as studentdairy_evaluation_id,studentdairy_evaluation.note,studentdairy_evaluation.marks,student_session.*,students.firstname,students.middlename,students.lastname,students.admission_no from student_session inner JOIN (SELECT studentdairy.id as studentdairy_id,studentdairy.class_id,studentdairy.section_id,studentdairy.session_id FROM `studentdairy` WHERE id= " . $this->db->escape($id) . " ) as home_work on home_work.class_id=student_session.class_id and home_work.section_id=student_session.section_id and home_work.session_id=student_session.session_id inner join students on students.id=student_session.student_id and students.is_active='yes' left join studentdairy_evaluation on studentdairy_evaluation.student_session_id=student_session.id and students.is_active='yes' and studentdairy_evaluation.studentdairy_id=" . $this->db->escape($id) . "";
        $this->datatables->query($sql)
            ->searchable('firstname,marks,studentdairy_evaluation.note," "')
            ->orderable('firstname,marks,studentdairy_evaluation.note," "')
            ->sort('students.id', 'DESC')
            ->query_where_enable(true);

        return $this->datatables->generate('json');
    }

    public function assignmentrecord($assigment_id)
    {
        $this->db->select('daily_assignment.*,staff.name,staff.surname,staff.employee_id,classes.class,sections.section,students.firstname,students.middlename,students.lastname,students.id as student_id,students.admission_no as student_admission_no,subjects.name as subject_name,subjects.code as subject_code');
        $this->db->join("student_session", "student_session.id = daily_assignment.student_session_id");
        $this->db->join("classes", "classes.id = student_session.class_id");
        $this->db->join("sections", "sections.id = student_session.section_id");
        $this->db->join("students", "students.id = student_session.student_id");
        $this->db->join("subject_group_subjects", "subject_group_subjects.id = daily_assignment.subject_group_subject_id");
        $this->db->join("subjects", "subjects.id = subject_group_subjects.subject_id");
        $this->db->join("staff", "staff.id = `daily_assignment`.`evaluated_by`", "left");
        $this->db->from('daily_assignment');
        $this->db->where('daily_assignment.id', $assigment_id);
        $result = $this->db->get();
        return $result->row_array();
    }

    public function dailyassignmentreport($class_id, $section_id, $subject_group_id, $subject_group_subject_id, $condition = null)
    {
        if ((!empty($class_id)) && (!empty($section_id))) {
            $this->datatables->where(array('student_session.class_id' => $class_id, 'student_session.section_id' => $section_id, 'subject_group_subjects.subject_group_id' => $subject_group_id, 'subject_group_subjects.id' => $subject_group_subject_id));
        }

        if ($condition != null) {
            $this->datatables->where($condition);
        }

        $this->datatables->select('daily_assignment.*,staff.name,staff.surname,staff.employee_id,classes.class,sections.section,students.firstname,students.middlename,students.lastname,students.id as student_id,students.admission_no as student_admission_no,subjects.name as subject_name,count(students.id) as total_student')
            ->searchable('students.firstname,classes.class,sections.section,daily_assignment.title,daily_assignment.date,daily_assignment.description')
            ->join("student_session", "student_session.id = daily_assignment.student_session_id")
            ->join("classes", "classes.id = student_session.class_id")
            ->join("sections", "sections.id = student_session.section_id")
            ->join("students", "students.id = student_session.student_id")
            ->join("subject_group_subjects", "subject_group_subjects.id = daily_assignment.subject_group_subject_id")
            ->join("subjects", "subjects.id = subject_group_subjects.subject_id")
            ->join("staff", "staff.id = `daily_assignment`.`evaluated_by`", "left")
            ->orderable('students.firstname,classes.class,sections.section,daily_assignment.description,daily_assignment.remark,daily_assignment.date,daily_assignment.evaluation_date')
            ->group_by('students.id')
            ->sort('students.firstname,classes.class,sections.section,daily_assignment.title,daily_assignment.date,daily_assignment.description', 'DESC')
            ->from('daily_assignment');

        return $this->datatables->generate('json');
    }

    public function assignmentdetails($student_id, $condition, $subject_group_id)
    {
        $this->db->select('daily_assignment.*,staff.name,staff.surname,staff.employee_id,classes.class,sections.section,students.firstname,students.middlename,students.lastname,students.id as student_id,students.admission_no as student_admission_no,subjects.name as subject_name,subjects.code as subject_code');
        $this->db->join("student_session", "student_session.id = daily_assignment.student_session_id");
        $this->db->join("classes", "classes.id = student_session.class_id");
        $this->db->join("sections", "sections.id = student_session.section_id");
        $this->db->join("students", "students.id = student_session.student_id");
        $this->db->join("subject_group_subjects", "subject_group_subjects.id = daily_assignment.subject_group_subject_id");
        $this->db->join("subjects", "subjects.id = subject_group_subjects.subject_id");
        $this->db->join("staff", "staff.id = `daily_assignment`.`evaluated_by`", "left");
        $this->db->where('students.id', $student_id);
        $this->db->where('daily_assignment.subject_group_subject_id', $subject_group_id);
        $this->db->where($condition);
        $this->db->from('daily_assignment');
        $result = $this->db->get();
        return $result->result_array();
    }

    public function check_valid_marks($str)
    {
        $this->form_validation->set_message('check_valid_marks', $this->lang->line('enter_valid_marks'));
        return false;

    }
    
    public function search_dtstudentdairyreport($class_id, $section_id, $subject_group_id, $subject_id)
    {
        if ((!empty($class_id)) && (!empty($section_id)) && (!empty($subject_id)) && (!empty($subject_group_id))) {
            
            $this->db->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id, 'subject_groups.id' => $subject_group_id, 'subject_group_subjects.id' => $subject_id));
            
        } else if ((!empty($class_id)) && (!empty($section_id)) && (!empty($subject_group_id))) {
            
            $this->db->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id, 'subject_groups.id' => $subject_group_id));
            
        } else if ((!empty($class_id)) && (empty($section_id)) && (empty($subject_id))) {
            
            $this->db->where(array('studentdairy.class_id' => $class_id));
            
        } else if ((!empty($class_id)) && (!empty($section_id)) && (empty($subject_id))) {
            
            $this->db->where(array('studentdairy.class_id' => $class_id, 'studentdairy.section_id' => $section_id));
            
        }

        $this->db->select('`studentdairy`.*,classes.class,sections.section,subject_group_subjects.subject_id,subject_group_subjects.id as `subject_group_subject_id`,subjects.name as subject_name,subjects.code as subject_code,subject_groups.id as subject_groups_id,subject_groups.name,(select count(*) as total from submit_assignment where submit_assignment.studentdairy_id=studentdairy.id) as assignments,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,staff_roles.role_id,       
        (SELECT COUNT(*) FROM student_session INNER JOIN students on students.id=student_session.student_id WHERE student_session.class_id=classes.id and student_session.section_id=sections.id and students.is_active="yes"  and student_session.session_id='.$this->current_session.')  as student_count
        
        ')
            
            ->join("classes", "classes.id = studentdairy.class_id")
            ->join("sections", "sections.id = studentdairy.section_id")
            ->join("subject_group_subjects", "subject_group_subjects.id = studentdairy.subject_group_subject_id")
            ->join("subjects", "subjects.id = subject_group_subjects.subject_id")
            ->join("subject_groups", "subject_group_subjects.subject_group_id=subject_groups.id")
            ->join("staff", "studentdairy.created_by=staff.id")
            ->join("staff_roles", "staff_roles.staff_id=staff.id")            
            ->where('subject_groups.session_id', $this->current_session)             
            ->order_by('studentdairy.studentdairy_date', 'DESC')
            ->from('studentdairy');            
             
        $result = $this->db->get();
        return $result->result_array();      
         
    }
    
    public function get_submitted_studentdairy($studentdairy_id)
    {
        $this->db
            ->select('students.*,submit_assignment.docs,submit_assignment.message,submit_assignment.student_id,classes.class,sections.section')
            ->join('students', 'students.id=submit_assignment.student_id', 'inner')
            ->join("student_session", "student_session.student_id = submit_assignment.student_id")
            ->join("classes", "classes.id = student_session.class_id")
            ->join("sections", "sections.id = student_session.section_id")
            ->from('submit_assignment')
            ->where(array('submit_assignment.studentdairy_id' => $studentdairy_id))
            ->where('student_session.session_id', $this->current_session)
            ->where('students.is_active', 'yes');
            
        $result = $this->db->get();
        return $result->result_array();
    }
    
    public function get_not_submitted_studentdairy($class_id,$section_id,$studentdairy_id)
    {
        $this->db
            ->select('students.*,classes.class,sections.section')             
            ->join("student_session", "student_session.student_id = students.id")
            ->join("classes", "classes.id = student_session.class_id")
            ->join("sections", "sections.id = student_session.section_id")
            ->from('students')
            ->where('student_session.class_id', $class_id)
            ->where('student_session.session_id', $this->current_session)
            ->where('student_session.section_id', $section_id)
            ->where('students.is_active', 'yes');           
            
            $this->db->where("students.id NOT IN (select submit_assignment.student_id from submit_assignment where studentdairy_id = $studentdairy_id and students.id = student_id) ");
            
        $result = $this->db->get();
        return $result->result_array();
    }  

    
}
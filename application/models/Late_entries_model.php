<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Late_entries_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date = $this->setting_model->getDateYmd();
    }


    public function get($id = null)
    {
        $this->db->select()->from('late_entries');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function get_today_entries()
    {
        // Get today's date
        $today = date('Y-m-d');

        // Use CodeIgniter's Query Builder
        $this->db->select('
            late_entries.*, 
            students.admission_no, 
            students.roll_no, 
            students.firstname, 
            students.lastname, 
            students.mobileno, 
            students.email, 
            students.father_name,
            classes.class,
            sections.section


        ');
        $this->db->from('late_entries');
        $this->db->join('students', 'students.admission_no = late_entries.admission_no');
        $this->db->join('student_session', 'students.id = student_session.student_id ');
        // $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id');
        $this->db->join('classes', 'student_session.class_id   = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id ');

        $this->db->where('DATE(late_entries.date)', $today);
        $this->db->where('student_session.session_id', $this->current_session);

        $this->db->group_by('late_entries.id');


        $query = $this->db->get();
        // echo $this->db->last_query();exit;
        return $query->result_array();
    }

    public function get_late_entries_beetweenDate($from_date, $to_date,$classID = null,$sectionID = null,$studentID = null)
    {
        // Get today's date
        $today = date('Y-m-d');

        // Use CodeIgniter's Query Builder
        $this->db->select('
            late_entries.*, 
            students.admission_no, 
            students.roll_no, 
            students.firstname, 
            students.lastname, 
            students.mobileno, 
            students.email, 
            students.father_name,
            classes.class,
            sections.section
            
        ');
        $this->db->from('late_entries');
        $this->db->join('students', 'students.admission_no = late_entries.admission_no');
        $this->db->join('student_session', 'students.id = student_session.student_id ');
        $this->db->join('classes', 'student_session.class_id   = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id ');
        

        $this->db->where('DATE(late_entries.date) >=', $from_date);
        $this->db->where('DATE(late_entries.date) <=', $to_date);
        $this->db->where('student_session.session_id', $this->current_session);

        if ($classID != null) {
            $this->db->where('student_session.class_id', $classID);
        }
        if ($sectionID != null) {
            $this->db->where('student_session.section_id', $sectionID);
        }
        if ($studentID != null) {
            $this->db->where('student_session.session_id', $studentID);
        }
        $this->db->group_by('late_entries.id');

        
        $query = $this->db->get();
        return $query->result_array();
    }
    public function add($insert_array, $update_array)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
    
        if (!empty($insert_array)) {
            $admission_no = $insert_array['admission_no'];
            $date = date('Y-m-d');
    
            $this->db->select('*');
            $this->db->from('late_entries');
            $this->db->where('admission_no', $admission_no);
            $this->db->where('DATE(date)', $date);
            $query = $this->db->get();
    
            if ($query->num_rows() == 0) {
                $this->db->insert('late_entries', $insert_array);
            }
            else{
                return false;
            }
        }
    
        if (!empty($update_array) && isset($update_array['id'])) {
            $this->db->where('id', $update_array['id']);
            $this->db->update('late_entries', $update_array);
        }
        $this->db->trans_complete();
    
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    
    public function getTotalLateStudents()
    {
        $current_date           = date('Y-m-d');
        $query = "SELECT COUNT(*) AS `total_late_students` FROM `late_entries` WHERE DATE(`date`) = ?";
        $result = $this->db->query($query, array($current_date));
        return $result->row();
    }
}

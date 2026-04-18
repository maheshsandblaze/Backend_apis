<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class My_day_today_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date = $this->setting_model->getDateYmd();
    }


    public function get($id = null)
    {
        $this->db->select()->from('visitor_management');
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
            my_day_today.*, 
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
        $this->db->from('my_day_today');
        $this->db->join('students', 'students.admission_no = my_day_today.admission_no');
        $this->db->join('student_session', 'students.id = student_session.student_id ');
        // $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id');
        $this->db->join('classes', 'student_session.class_id   = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id ');

        $this->db->where('my_day_today.date', $today);
        $this->db->where('student_session.session_id', $this->current_session);

        $this->db->group_by('my_day_today.id');


        $query = $this->db->get();
        // echo $this->db->last_query();exit;
        return $query->result_array();
    }
    
    
    public function getRecord($id = null)
    {

        // Use CodeIgniter's Query Builder
        $this->db->select('
            my_day_today.*, 
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
        $this->db->from('my_day_today');
        $this->db->join('students', 'students.admission_no = my_day_today.admission_no');
        $this->db->join('student_session', 'students.id = student_session.student_id ');
        // $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id');
        $this->db->join('classes', 'student_session.class_id   = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id ');

        $this->db->where('my_day_today.id', $id);
        $this->db->where('student_session.session_id', $this->current_session);


        $query = $this->db->get();
        // echo $this->db->last_query();exit;
        return $query->row_array();
    }
    
    
    public function getStudentRecord($id = null)
    {
        // Use CodeIgniter's Query Builder
        $this->db->select('
            my_day_today.*, 
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
        $this->db->from('my_day_today');
        $this->db->join('students', 'students.admission_no = my_day_today.admission_no');
        $this->db->join('student_session', 'students.id = student_session.student_id ');
        // $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id');
        $this->db->join('classes', 'student_session.class_id   = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id ');

        $this->db->where('my_day_today.id', $id);
        $this->db->where('student_session.session_id', $this->current_session);


        $query = $this->db->get();
        // echo $this->db->last_query();exit;
        return $query->row_array();
    }
    
    
    public function get_mydaytoday_student($student_id)
    {
        // Get today's date
        $today = date('Y-m-d');

        // Use CodeIgniter's Query Builder
        $this->db->select('
            my_day_today.*, 
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
        $this->db->from('my_day_today');
        $this->db->join('students', 'students.admission_no = my_day_today.admission_no');
        $this->db->join('student_session', 'students.id = student_session.student_id ');
        // $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id');
        $this->db->join('classes', 'student_session.class_id   = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id ');

        $this->db->where('my_day_today.student_id', $student_id);
        $this->db->where('student_session.session_id', $this->current_session);

        $this->db->group_by('my_day_today.id');


        $query = $this->db->get();
        // echo $this->db->last_query();exit;
        return $query->result_array();
    }
    
    public function get_visitor_management_beetweenDate($from_date, $to_date)
    {
        // Get today's date
        $today = date('Y-m-d');

        // Use CodeIgniter's Query Builder
        $this->db->select('
            visitor_management.*, 
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
        $this->db->from('visitor_management');
        $this->db->join('students', 'students.admission_no = visitor_management.admission_no');
        $this->db->join('student_session', 'students.id = student_session.student_id ');
        $this->db->join('classes', 'student_session.class_id   = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id ');


        $this->db->where('visitor_management.date >=', $from_date);
        $this->db->where('visitor_management.date <=', $to_date);
        $this->db->where('student_session.session_id', $this->current_session);

        
        $this->db->group_by('visitor_management.id');


        $query = $this->db->get();
        return $query->result_array();
    }
    public function add($insert_array)
    {
        $this->db->insert('my_day_today', $insert_array);
        // echo $this->db->last_query();
        // exit;

        $inserid = $this->db->insert_id();

        return $inserid;
    }

    public function getTotalLateStudents()
    {
        $current_date           = date('Y-m-d');
        $query = "SELECT COUNT(*) AS `total_late_students` FROM `visitor_management` WHERE `date` = ?";
        $result = $this->db->query($query, array($current_date));
        return $result->row();
    }
    
    public function remove($id){
    	
    	$this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        $this->db->where('id',$id);
        $this->db->delete('my_day_today');
        $message = DELETE_RECORD_CONSTANT . " On my day today id " . $id;
        $action = "Delete";
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
}

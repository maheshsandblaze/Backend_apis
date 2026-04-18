<?php

class Schoolvacancies_model extends MY_model {

    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();


    }
    public function get($id = null) {
        $this->db->select('school_vacancies.*, classes.class, sections.section,IFNULL(count(student_session.student_id),0) as intakes');
        $this->db->join('classes', 'classes.id = school_vacancies.class_id');
        $this->db->join('sections', 'sections.id = school_vacancies.section_id');
        $this->db->join('student_session', 'student_session.class_id = school_vacancies.class_id AND student_session.section_id = school_vacancies.section_id AND student_session.session_id = school_vacancies.session','left');
        $this->db->where('school_vacancies.session', $this->current_session);
        $this->db->group_by('school_vacancies.id, classes.class, sections.section');
        $this->db->order_by('classes.id');
        $this->db->order_by('sections.id');
    
        if (!empty($id)) {
            $this->db->where('school_vacancies.id', $id);
            $query = $this->db->get('school_vacancies');
            return $query->row_array();
        } else {
            $query = $this->db->get('school_vacancies');

            // echo $this->db->last_query();exit;
            return $query->result_array();
        }
    }
    

    public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

    
        // Check if record exists with the same class_id, section_id, and session_id
        $this->db->where('class_id', $data['class_id']);
        $this->db->where('section_id', $data['section_id']);
        $this->db->where('session', $data['session']);
        $query = $this->db->get('school_vacancies');
    
        if ($query->num_rows() > 0) {
            // Record exists, perform update
            $existing_record = $query->row_array();
            $this->db->where('id', $existing_record['id'])->update('school_vacancies', $data);
            $message = UPDATE_RECORD_CONSTANT . " On school houses id " . $existing_record['id'];
            $action = "Update";
            $record_id = $existing_record['id'];
            // $this->log($message, $record_id, $action);
        } else {
            // Record does not exist, perform insert
            $this->db->insert('school_vacancies', $data);
            $id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On school houses id " . $id;
            $action = "Insert";
            $record_id = $id;
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
            //return $return_value;
        }
    }
    

    public function delete($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("school_vacancies");
        $message = DELETE_RECORD_CONSTANT . " On school houses id " . $id;
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

?>
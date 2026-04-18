<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Leadstudents_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session      = $this->setting_model->getCurrentSession();
        $this->current_session_name = $this->setting_model->getCurrentSessionName();
        $this->start_month          = $this->setting_model->getStartMonth();
    }




    public function add($data)
    {
        $status = 0;
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
    
        $this->db->insert_batch('lead_students', $data);
        $status = 1;

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return 0;
        } else {
            return $status;
        }
    }


    public function getStudentsByLead($lead_id) {
        $this->db->select('student_session_id');
        $this->db->from('lead_students');
        $this->db->where('lead_id', $lead_id);

        $query = $this->db->get();
        $data = [];
        foreach($query->result_array() as $tr) {
            $data[] = $tr['student_session_id'];
        } 

        return $data;
    }




    public function delete($lead_id)
    {
        $this->db->where('lead_id', $lead_id);
        $this->db->delete('lead_students');
        return $this->db->affected_rows();
    }

}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Behavioural_model extends MY_Model
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
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('behavioural', $data);
        $id        = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On  Phone Call Log id " . $id;
        $action    = "Insert";
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

    public function call_list($id = null)
    {
        $this->db->select('behavioural.*,classes.class,sections.section')->from('behavioural');
        if ($id != null) {
            $this->db->where('behavioural.id', $id);
        } else {
            $this->db->order_by('behavioural.id','desc');
        }
        $this->db->join('classes', 'behavioural.class_id = classes.id');
        $this->db->join('sections', 'sections.id = behavioural.section_id');
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getcalllist($id = null)
    {

        if ($id != null) {
            $this->datatables->where('behavioural.id', $id);
        }  
        $this->datatables->sort('behavioural.id','desc');        
        $this->datatables
            ->select('behavioural.*,classes.class,sections.section')
            ->searchable('behavioural.name,behavioural.collected_by,classes.class,sections.section')
            ->orderable('behavioural.name,behavioural.collected_by,classes.class,sections.section')
            ->join('classes', 'behavioural.class_id = classes.id')
            ->join('sections', 'behavioural.section_id = sections.id')
            ->from('behavioural');
   
            return $this->datatables->generate('json');
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('behavioural');
        $message   = DELETE_RECORD_CONSTANT . " On Phone Call Log id " . $id;
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

    public function call_update($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('behavioural', $data);
        $message   = UPDATE_RECORD_CONSTANT . " On Phone Call Log id " . $id;
        $action    = "Update";
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

    // get studnt behavioural note 

    public function getstudentbehaviouralnote($id = null)
    {
        $this->db->select('behavioural.*,classes.class,sections.section')->from('behavioural');
        if ($id != null) {
            $this->db->where('behavioural.student_session_id', $id);
        } else {
            $this->db->order_by('behavioural.id','desc');
        }
        $this->db->join('classes', 'behavioural.class_id = classes.id');
        $this->db->join('sections', 'sections.id = behavioural.section_id');
        $query = $this->db->get();

        // echo $this->db->last_query();exit;

        return $query->result_array();

    }

}

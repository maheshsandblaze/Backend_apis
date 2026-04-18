<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feeenquiry_model extends MY_Model
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
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('fee_enquiry', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  fees discounts id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
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
        } else {
            $this->db->insert('fee_enquiry', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  fees discounts id " . $id;
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
            return $id;
        }
    }


    public function get($id = null , $student_id = null, $feetype_id = null, $return_rows = false)
    {
        $this->db->select('*');
        $this->db->from('fee_enquiry');

        if (!empty($id)) {
            $this->db->where('id', $id);
        }

        if (!empty($student_id)) {
            $this->db->where('student_id', $student_id);
        }

        if (!empty($feetype_id)) {
            $this->db->where('feetype_id', $feetype_id);
        }

        $query = $this->db->get();
        if ($return_rows) {
            return  $query->num_rows();   
        }

        return $query->result_array();
    }


    public function changeStatus($data)
    {
        $this->db->where("id", $data["id"])->update("fee_enquiry", $data);
    }

}
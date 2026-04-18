<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Timetable_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false);
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('timetables');
        $message   = DELETE_RECORD_CONSTANT . " On timetables id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false);
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != 0) {
            $this->db->where('id', $data['id']);
            $this->db->update('timetables', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On timetables id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
        } else {
            $this->db->insert('timetables', $data);
            $record_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On timetables id " . $record_id;
            $action    = "Insert";
        }
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function get($data)
    {
        $query = $this->db->get_where('timetables', $data);
        return $query->result_array();
    }

}

<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subject_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get($id = null) {
        $subject_condition = 0;
        $userdata = $this->customlib->getUserData();
        $role_id = $userdata["role_id"] ?? null;

        if (isset($role_id) && ($role_id == 2) && (isset($userdata["class_teacher"]) && $userdata["class_teacher"] == "yes")) {
            $my_classes = $this->teacher_model->my_classes($userdata['id']);

            if (!empty($my_classes)) {
                $subject_condition = 0;
            } else {
                $subject_condition = 1;
                $my_subjects = $this->teacher_model->get_examsubjects($userdata['id']);
            }
        }
        
        if ($id != null) {
            $this->db->select()->from('subjects');
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if ($subject_condition == 1 && !empty($my_subjects)) {
                $this->db->select()->from('subjects');
                $this->db->where_in('subjects.id', $my_subjects);
                $this->db->order_by('id');
                $query = $this->db->get();
                return $query->result_array(); 
            } elseif ($subject_condition == 1 && empty($my_subjects)) {
                return array();
            } else {
                $this->db->select()->from('subjects');
                $this->db->order_by('id');
                $query = $this->db->get();
                return $query->result_array(); 
            }
        }
    }

    public function remove($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false);
        // =======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('subjects');
        $message   = DELETE_RECORD_CONSTANT . " On subjects id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        // ======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false);
        // =======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('subjects', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On subjects id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
        } else {
            $this->db->insert('subjects', $data);
            $record_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On subjects id " . $record_id;
            $action    = "Insert";
        }
        $this->log($message, $record_id, $action);
        // ======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    function check_data_exists($data) {
        $this->db->where('name', $data['name']);
        if (isset($data['id'])) {
            $this->db->where('id !=', $data['id']);
        }
        $query = $this->db->get('subjects');
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function check_code_exists($data) {
        $this->db->where('code', $data['code']);
        if (isset($data['id'])) {
            $this->db->where('id !=', $data['id']);
        }
        $query = $this->db->get('subjects');
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

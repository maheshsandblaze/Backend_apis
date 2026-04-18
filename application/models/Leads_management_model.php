<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Leads_management_model extends MY_Model
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
        $this->db->insert('leads_assign', $data);
        return $this->db->insert_id();
    }

    public function getLeads()
    {
        $this->db->select('leads_assign.*,classes.class,sections.section,staff.name,staff.surname');
        $this->db->from('leads_assign');
        $this->db->join('classes', 'leads_assign.class_id = classes.id');
        $this->db->join('sections', 'leads_assign.section_id = sections.id');
        $this->db->join('staff', 'leads_assign.staff_id = staff.id');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getClassByStaffId($staff_id)
    {
        $this->db->select('DISTINCT(class_id) as class_id')->from('leads_assign');
        $this->db->where('staff_id', $staff_id);
        $this->db->order_by('id');
        $query = $this->db->get();
        $staff_list = $query->result_array();
        $class_ids = [];
        foreach ($staff_list as $sl) {
            $class_ids[] = $sl['class_id'];
        }
        return $class_ids;
    }

    public function getSectionsByClassId($class_id, $staff_id)
    {
        $this->db->select('id, section_id')->from('leads_assign');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('class_id', $class_id);
        $this->db->order_by('id');
        $query = $this->db->get();
        $staff_list = $query->result_array();
        $section_ids = [];
        foreach ($staff_list as $sl) {
            $section_ids[] = $sl['section_id'];
        }
        return $section_ids;
    }


    public function getFeeTypeByStaffId($staff_id) {
        $this->db->select('DISTINCT(feetype_id) as feetype_id')->from('leads_assign');
        $this->db->where('staff_id', $staff_id);
        $this->db->order_by('id');
        $query = $this->db->get();
        $staff_list = $query->result_array();
        $class_ids = [];
        foreach ($staff_list as $sl) {
            $class_ids[] = $sl['feetype_id'];
        }
        return $class_ids;
    }

    

    public function lead_delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('leads_assign');
        return $this->db->affected_rows();
    }

    public function getLeadAssignid($class_id,  $section_id, $feetype_id, $assign_by) {
        $this->db->select('id')->from('leads_assign');
        $this->db->where('class_id', $class_id);
        if ($section_id) {
            $this->db->where('section_id', $section_id);
        }
        
        $this->db->where('feetype_id', $feetype_id);
        $this->db->where('staff_id', $assign_by);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }
    
    
     public function getLeadsByStaffid($staff_id) {
        $this->db->select('id, class_id,section_id, feetype_id')->from('leads_assign');
        $this->db->where('staff_id', $staff_id);

        $query = $this->db->get();

        return $query->result_array();
    }

}

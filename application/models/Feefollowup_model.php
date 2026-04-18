<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feefollowup_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        
        
    }


    public function add_follow_up($data)
    {
        $this->db->insert('fee_followup', $data);
    }

    public function follow_up_update($enquiry_id, $follow_up_id, $data)
    {
        $this->db->where('id', $follow_up_id);
        $this->db->where('enquiry_id', $enquiry_id);
        $this->db->update('fee_followup', $data);
        redirect('admin/enquiry/follow_up_edit/' . $enquiry_id . '/' . $follow_up_id . '');
    }

    public function next_follow_up_date($enquiry_id)
    {
        $this->db->select('max(`id`) as id');
        $this->db->from('fee_followup');
        $this->db->where('feeenquiry_id', $enquiry_id);
        $query = $this->db->get();
        $data  = $query->row_array();
        $id    = $data['id'];
        $this->db->select('*');
        $this->db->from('fee_followup');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function getfollow_up_list($enquiry_id, $follow_up = null)
    {
        $this->db->select('fee_followup .*, staff.employee_id, staff.name, staff.surname,fee_enquiry.created_by')->from('fee_followup ');
        $this->db->join('fee_enquiry', 'fee_enquiry.id = fee_followup.feeenquiry_id');
        $this->db->join('staff', 'staff.id = fee_followup.followup_by')->join("staff_roles", "staff_roles.staff_id = staff.id", "left");

        if ($this->session->has_userdata('admin')) {
            $getStaffRole       = $this->customlib->getStaffRole();
            $staffrole          = json_decode($getStaffRole);
            $superadmin_visible = $this->customlib->superadmin_visible();
            if ($superadmin_visible == 'disabled' && $staffrole->id != 7) {
                $this->db->where("staff_roles.role_id !=", 7);
            }
        }

        if ($follow_up != null) {
            $this->db->where('fee_followup.id', $follow_up);
            $this->db->where('fee_followup.feeenquiry_id', $enquiry_id);
            $this->db->order_by('fee_followup.id desc');
        } else {
            $this->db->where('fee_followup.feeenquiry_id', $enquiry_id);
            $this->db->order_by('fee_followup.id desc');
        }
        $query = $this->db->get();
        if ($follow_up != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }


    public function delete_follow_up($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('fee_followup');
    }

}
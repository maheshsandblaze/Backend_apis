<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Userlog_model extends CI_Model
{

    private $table = "userlog";

    public function __construct()
    {
        parent::__construct();
        $this->load->library('datatables');
    }

    public function get($id = null)
    {
        $this->db->select()->from('userlog');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('login_datetime', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getByRole($role)
    {
        $this->db->select()->from('userlog');
        $this->db->where('role', $role);
        $this->db->order_by('login_datetime', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getByRoleStaff()
    {
        $this->db->select()->from('userlog');
        $this->db->where('role!=', 'Parent');
        $this->db->where('role!=', 'Student');
        $this->db->order_by('login_datetime', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function add($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('userlog', $data);
            return $data['id'];
        } else {
            $this->db->insert('userlog', $data);
            return $this->db->insert_id();
        }
    }

    public function getAllRecord()
    {
        if ($this->staffrole->id != 7) {
            // $this->datatables->where('staff.is_invisibles_user != ',1);
        }

        $this->datatables
            ->select($this->table . '.*,class_sections.id as `class_section_id`,IFNULL(classes.class, "") as `class_name`,IFNULL(sections.section, "") as `section_name`')
            ->searchable('user,role,ipaddress,classes.class,sections.section')
            ->join('class_sections', 'class_sections.id = ' . $this->table . '.class_section_id', 'left')
            ->join('classes', 'classes.id = class_sections.class_id', 'left')
            ->join('sections', 'sections.id = class_sections.section_id', 'left')
            ->orderable('user, role, ipaddress,login_datetime,user_agent')
            ->sort('login_datetime', 'desc')
            ->from($this->table);
        return $this->datatables->generate('json');
    }

    public function getlogAllRecord()
    {
        $this->datatables
            ->select($this->table . '.*,class_sections.id as `class_section_id`,IFNULL(classes.class, "") as `class_name`,IFNULL(sections.section, "") as `section_name`,IFNULL(staff.is_invisibles_user,"0") as is_invisible_user ')
            ->searchable('user,role,ipaddress,classes.class,sections.section')
            ->join('class_sections', 'class_sections.id = ' . $this->table . '.class_section_id', 'left')
            ->join('classes', 'classes.id = class_sections.class_id', 'left')
            ->join('sections', 'sections.id = class_sections.section_id', 'left')
            ->join('staff', 'staff.email = ' . $this->table . '.user', 'left')
            ->orderable('user, role, ipaddress,login_datetime,user_agent')
            ->sort('login_datetime', 'desc')
            ->from($this->table);
        return $this->datatables->generate('json');
    }

    public function getAllRecordByRole($role)
    {
        $this->datatables
            ->select($this->table . '.*,class_sections.id as `class_section_id`,IFNULL(classes.class, "") as `class_name`,IFNULL(sections.section, "") as `section_name`')
            ->searchable('user,role,ipaddress,classes.class,sections.section,login_datetime')
            ->join('class_sections', 'class_sections.id = ' . $this->table . '.class_section_id', 'left')
            ->join('classes', 'classes.id = class_sections.class_id', 'left')
            ->join('sections', 'sections.id = class_sections.section_id', 'left')
            ->orderable('user, role, ipaddress,login_datetime,user_agent')
            ->where('role', $role)
            ->sort('login_datetime', 'desc')
            ->from($this->table);
        return $this->datatables->generate('json');
    }

    public function getAllRecordByStaff()
    {
        $this->datatables
            ->select($this->table . '.*, IFNULL(staff.is_invisibles_user, 0) AS is_invisible_user')
            ->join('staff', 'staff.email = ' . $this->table . '.user', 'left')
            ->searchable('id,user,role,ipaddress')
            ->orderable('user, role, ipaddress, login_datetime, user_agent')
            ->where('role!=', 'Parent')
            ->where('role!=', 'Student')
            ->sort('login_datetime', 'desc')
            ->from($this->table);

        return $this->datatables->generate('json');
    }

    public function userlog_delete()
    {
        $this->db->trans_start();
        $this->db->truncate('userlog');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}

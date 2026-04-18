<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Section_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        $this->db->select()->from('sections');
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

    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); 
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('sections');
        
        $message   = DELETE_RECORD_CONSTANT . " On sections id " . $id;
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

    public function getClassBySectionAll($classid)
    {
        $this->db->select('class_sections.id,class_sections.section_id,sections.section');
        $this->db->from('class_sections');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->where('class_sections.class_id', $classid);
        $this->db->order_by('class_sections.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getClassBySection($classid)
    {
        $userdata = $this->customlib->getUserData();
        $role_id  = $userdata["role_id"] ?? null;
        $section  = array();

        if (isset($role_id) && ($role_id == 2) && (isset($userdata["class_teacher"]) && $userdata["class_teacher"] == "yes")) {
            $section = $this->teacher_model->get_teacherrestricted_modesections($userdata["id"], $classid);
        } else {
            $this->db->select('class_sections.id,class_sections.section_id,sections.section');
            $this->db->from('class_sections');
            $this->db->join('sections', 'sections.id = class_sections.section_id');
            $this->db->where('class_sections.class_id', $classid);
            $this->db->order_by('class_sections.id');
            $query = $this->db->get();
            $section = $query->result_array();
        }

        return $section;
    }

    public function getClassTeacherSection($classid)
    {
        $userdata = $this->customlib->getUserData();
        if (isset($userdata["role_id"]) && ($userdata["role_id"] == 2)) {
            $id = $userdata["id"];

            $query = $this->db->select("class_teacher.section_id ")
                ->join('sections', 'sections.id = class_teacher.section_id')
                ->join('class_sections', 'sections.id = class_sections.section_id')
                ->where(array('class_teacher.class_id' => $classid, 'class_teacher.staff_id' => $id))
                ->group_by("class_teacher.section_id")
                ->get("class_teacher");
            $result = $query->result_array();

            foreach ($result as $key => $value) {
                $query2 = $this->db->select('class_sections.id,sections.section')
                    ->join('sections', 'sections.id = class_sections.section_id')
                    ->where('sections.id', $value['section_id'])
                    ->get('class_sections');
                $result2 = $query2->row_array();
                if ($result2) {
                    $result[$key]['id'] = $result2['id'];
                    $result[$key]['section'] = $result2['section'];
                }
            }
            return $result;
        }
        return array();
    }

    public function getSubjectTeacherSection($classid, $id)
    {
        $query = $this->db->select("class_sections.id,sections.section,class_sections.section_id")
            ->join("class_sections", "teacher_subjects.class_section_id = class_sections.id")
            ->join('sections', 'sections.id = class_sections.section_id')
            ->where(array('class_sections.class_id' => $classid, 'teacher_subjects.teacher_id' => $id))
            ->get("teacher_subjects");

        return $query->result_array();
    }

    public function getClassNameBySection($classid, $sectionid)
    {
        $this->db->select('class_sections.id,class_sections.section_id,sections.section,classes.class');
        $this->db->from('class_sections');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->join('classes', 'classes.id = class_sections.class_id');
        $this->db->where('class_sections.class_id', $classid);
        $this->db->where('class_sections.section_id', $sectionid);
        $this->db->order_by('class_sections.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function add($data)
    {
        $this->db->trans_start(); 
        $this->db->trans_strict(false);
        
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('sections', $data);
            $record_id = $data['id'];
            $message   = UPDATE_RECORD_CONSTANT . " On sections id " . $record_id;
            $action    = "Update";
        } else {
            $this->db->insert('sections', $data);
            $record_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On sections id " . $record_id;
            $action    = "Insert";
        }
        
        $this->log($message, $record_id, $action);
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getSectionIdByName($section_name)
    {
        $this->db->where('section', $section_name);
        $query = $this->db->get('sections');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }
}

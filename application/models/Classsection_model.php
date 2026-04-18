<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Classsection_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($classid = null)
    {
        $this->db->select('class_sections.id,class_sections.section_id,sections.section');
        $this->db->from('class_sections');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->where('class_sections.class_id', $classid);
        $this->db->order_by('class_sections.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            return $this->db->update('classes', $data);
        }
        return false;
    }

    public function add($data, $sections)
    {
        $this->db->trans_start(); 
        $this->db->trans_strict(false);
        
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('classes', $data);
            $class_id  = $data['id'];
            $message   = UPDATE_RECORD_CONSTANT . " On classes id " . $data['id'];
            $action    = "Update";
        } else {
            $this->db->insert('classes', $data);
            $class_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On classes id " . $class_id;
            $action    = "Insert";
        }
        
        $this->log($message, $class_id, $action);

        if (!empty($sections)) {
            $sections_array = array();
            foreach ($sections as $vec_value) {
                $sections_array[] = array(
                    'class_id'   => $class_id,
                    'section_id' => $vec_value,
                );
            }
            $this->db->insert_batch('class_sections', $sections_array);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $class_id;
        }
    }

    public function getDetailbyClassSection($class_id, $section_id)
    {
        $this->db->select('class_sections.*,classes.class,sections.section')->from('class_sections');
        $this->db->where('class_sections.class_id', $class_id);
        $this->db->where('class_sections.section_id', $section_id);
        $this->db->join('classes', 'classes.id = class_sections.class_id');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function check_data_exists($data)
    {
        $this->db->where('class_id', $data['class_id']);
        $this->db->where('section_id', $data['section_id']);
        $query = $this->db->get('class_sections');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getByID($id = null)
    {
        $this->db->select('classes.*')->from('classes');
        if ($id != null) {
            $this->db->where('classes.id', $id);
        } else {
            $this->db->order_by('classes.id', 'DESC');
        }

        $query = $this->db->get();
        $classes_list = $query->result_array();
        
        $array = array();
        if (!empty($classes_list)) {
            foreach ($classes_list as $value) {
                $item        = new stdClass();
                $item->id    = $value['id'];
                $item->class = $value['class'];
                $item->sections = $this->getVechileByRoute($value['id']);
                $array[]        = $item;
            }
        }
        
        if ($id != null && !empty($array)) {
            return $array;
        }
        return $array;
    }

    public function getVechileByRoute($class_id)
    {
        $this->db->select('class_sections.id as class_section_id,class_sections.class_id,class_sections.section_id,sections.*')->from('class_sections');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->where('class_sections.class_id', $class_id);
        $this->db->order_by('class_sections.id', 'asc');
        $queryValue = $this->db->get();
        return $queryValue->result();
    }

    public function remove($class_id, $array)
    {
        $this->db->trans_start();
        $this->db->where('class_id', $class_id);
        if (!empty($array)) {
            $this->db->where_in('section_id', $array);
        }
        $this->db->delete('class_sections');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function allClassSections()
    {
        $classes = $this->class_model->get();
        if (!empty($classes)) {
            foreach ($classes as $class_key => $class_value) {
                $classes[$class_key]['sections'] = $this->get($class_value['id']);
            }
        }
        return $classes;
    }

    public function getClassSectionStudentCount()
    {
        $class_section_array = $this->customlib->get_myClassSectionQuerystring('class_sections');
        $userdata = $this->customlib->getUserData();
        $query = "SELECT class_sections.*,classes.class,sections.section,(SELECT COUNT(*) FROM student_session INNER JOIN students on students.id=student_session.student_id WHERE student_session.class_id=classes.id and student_session.section_id=sections.id and students.is_active='yes'  and student_session.session_id=" . $this->current_session . " )  as student_count FROM `class_sections` INNER JOIN classes on classes.id=class_sections.class_id INNER JOIN sections on sections.id=class_sections.section_id  where 0=0 " . $class_section_array . " ORDER by classes.class ASC, sections.section asc";
        $queryResult = $this->db->query($query);
        $std_data= $queryResult->result();
        
        if (isset($userdata["role_id"]) && ($userdata["role_id"] == 2) && (isset($userdata["class_teacher"]) && $userdata["class_teacher"] == "yes") && (empty($class_section_array))) {
            $std_data = array();
        }
        return $std_data;
    }

}

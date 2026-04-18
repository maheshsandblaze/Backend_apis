<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Content_model extends MY_Model
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
    public function get($id = null)
    {
        $this->db->select('contents.*,classes.class,sections.section,(select GROUP_CONCAT(role) FROM content_for WHERE content_id=contents.id) as role,class_sections.id as `aa`')->from('contents');
        $this->db->join('class_sections', 'contents.cls_sec_id = class_sections.id', 'left outer');
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left outer');
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left outer');
        if ($id != null) {
            $this->db->where('contents.id', $id);
        }
        $this->db->order_by('contents.id', "desc");
        $this->db->limit(10);
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getContentByRoleold($id = null, $role = null)
    {
        // $userdata = $this->customlib->getUserData();
        // $staff_id = $userdata["id"];
        $session  = $this->setting_model->getCurrentSession();

        $inner_sql = "";
        $teacher_filter = "";

        // Role-specific filters
        if ($role == "student") {
            $inner_sql = " WHERE (role='student' and created_by='" . $id . "' ) 
                       OR (created_by=0 and role='student')";
        }

        // // Restrict to teacher's assigned class-sections
        // if ($role == "Teacher") {
        //     // Subquery to get allowed class_section_ids for this teacher
        //     $teacher_filter = " AND contents.cls_sec_id IN (
        //         SELECT cs.id
        //         FROM class_sections cs
        //         INNER JOIN subject_teacher st ON st.class_id = cs.class_id AND st.section_id = cs.section_id
        //         WHERE st.staff_id = " . $staff_id . "
        //     )";
        // }

        // // Final query
        // $query = "SELECT contents.*,
        //             (SELECT GROUP_CONCAT(role) FROM content_for WHERE content_id=contents.id) as role,
        //             classes.class,
        //             (SELECT GROUP_CONCAT(sections.section) 
        //              FROM sections 
        //              WHERE FIND_IN_SET(sections.id, contents.cls_sec_id)) as section_names
        //         FROM content_for 
        //         INNER JOIN contents ON contents.id = content_for.content_id 
        //         LEFT JOIN class_sections ON class_sections.id = contents.cls_sec_id
        //         LEFT JOIN classes ON classes.id = class_sections.class_id 
        //         LEFT JOIN sections ON sections.id = class_sections.section_id
        //         " . $inner_sql . "
        //         WHERE contents.session_id = " . $session . "
        //         " . $teacher_filter . "
        //         GROUP BY contents.id";

        // $result = $this->db->query($query);
        // return $result->result_array();

        if ($role == "Teacher") {
            // Class teacher - get class_id of classes where teacher is class teacher
            $classTeacherClassIdsQuery = "SELECT ct.class_id
            FROM class_teacher ct ";

            // Build filter for class teacher: all sections of classes where teacher is class teacher
            $classTeacherSectionIdsQuery = "SELECT cs.id
            FROM class_sections cs
            WHERE cs.class_id IN ($classTeacherClassIdsQuery)";

            // Subject teacher: specific class_id and section_id
            $subjectTeacherSectionsQuery = "SELECT cs.id
            FROM class_sections cs
            INNER JOIN subject_teacher st ON st.class_id = cs.class_id AND st.section_id = cs.section_id
            ";

            // Combine both with UNION
            $teacher_filter = " AND contents.cls_sec_id IN (
            $classTeacherSectionIdsQuery
            UNION
            $subjectTeacherSectionsQuery
        )";
        }

        $query = "SELECT contents.*,
                (SELECT GROUP_CONCAT(role) FROM content_for WHERE content_id=contents.id) as role,
                classes.class,
                (SELECT GROUP_CONCAT(sections.section) 
                 FROM sections 
                 WHERE FIND_IN_SET(sections.id, contents.cls_sec_id)) as section_names
            FROM content_for 
            INNER JOIN contents ON contents.id = content_for.content_id 
            LEFT JOIN class_sections ON class_sections.id = contents.cls_sec_id
            LEFT JOIN classes ON classes.id = class_sections.class_id 
            LEFT JOIN sections ON sections.id = class_sections.section_id
            " . $inner_sql . "
            
            
            " . $teacher_filter . "
            GROUP BY contents.id";

        // echo $query;exit;

        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function getContentByRole($id = null, $role = null)
    {
        $session  = $this->setting_model->getCurrentSession();

        $where = "";
        $teacher_filter = "";

        // ✅ Student filter (based only on contents table)
        if ($role == "student") {
            $where = "WHERE (contents.role='student' AND contents.created_by='" . $id . "') 
                  OR (contents.created_by=0 AND contents.role='student')";
        }

        // ✅ Teacher filter
        if ($role == "Teacher") {

            // Class Teacher → all sections of their classes
            $classTeacherSectionIdsQuery = "SELECT cs.id
            FROM class_sections cs
            WHERE cs.class_id IN (
                SELECT ct.class_id 
                FROM class_teacher ct 
                WHERE ct.staff_id = " . $id . "
            )";

            // Subject Teacher → specific class & section
            $subjectTeacherSectionsQuery = "SELECT cs.id
            FROM class_sections cs
            INNER JOIN subject_teacher st 
                ON st.class_id = cs.class_id 
                AND st.section_id = cs.section_id
            WHERE st.staff_id = " . $id;

            // Combine both
            $teacher_filter = "WHERE contents.cls_sec_id IN (
            $classTeacherSectionIdsQuery
            UNION
            $subjectTeacherSectionsQuery
        )";
        }

        // ✅ Final Query (NO content_for)
        $query = "SELECT contents.*,
                classes.class,
                sections.section as section_name
            FROM contents
            LEFT JOIN class_sections 
                ON class_sections.id = contents.cls_sec_id
            LEFT JOIN classes 
                ON classes.id = class_sections.class_id
            LEFT JOIN sections 
                ON sections.id = class_sections.section_id
            " . (!empty($where) ? $where : $teacher_filter) . "
            AND contents.session_id = " . $session . "
            GROUP BY contents.id";

        // echo $query; exit;

        $result = $this->db->query($query);
        return $result->result_array();
    }


    public function getListByCategory($category)
    {
        // $userdata   = $this->customlib->getUserData();
        // $staff_id   = $userdata["id"];
        // $role       = $userdata["user_type"];
        // $session    = $this->setting_model->getCurrentSession();

        $this->db->select('contents.*, classes.class,
        (SELECT GROUP_CONCAT(sections.section) 
         FROM sections 
         WHERE FIND_IN_SET(class_sections.id, contents.cls_sec_id)) as section_names');

        $this->db->from('contents');
        $this->db->join('classes', 'contents.class_id = classes.id', 'left');
        $this->db->join('class_sections', 'contents.cls_sec_id = class_sections.id', 'left');
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left');

        // Join subject_teachers only if the role is teacher
        // if ($role == 'Teacher') {
        //     $this->db->join('subject_teacher st', 'st.class_id = class_sections.class_id AND st.section_id = class_sections.section_id', 'inner');
        //     $this->db->where('st.staff_id', $staff_id);
        // }

        // Apply teacher restriction
        // if ($role == 'Teacher') {

        // Class teacher - class IDs

        // }

        $this->db->where('contents.type', $category);
        // $this->db->where('contents.session_id', $session);
        $this->db->order_by('contents.id');

        $query = $this->db->get();

        return $query->result_array();
    }


    public function getListByCategoryforUser($class_id, $section_id, $category = '')
    {

        $session    = $this->setting_model->getCurrentSession();

        if (empty($class_id)) {

            $class_id = "0";
        }

        if (empty($section_id)) {

            $section_id = "0";
        }

        $this->db->select('id');
        $this->db->from('class_sections');
        $this->db->where('class_id', $class_id);
        $this->db->where('section_id', $section_id);
        $class_section = $this->db->get()->row();

        $class_section_id = isset($class_section->id) ? $class_section->id : 0;

        // $query = "SELECT contents.*,class_sections.id as `class_section_id`,classes.class,sections.section FROM `content_for` INNER JOIN contents on content_for.content_id=contents.id left JOIN class_sections on class_sections.id=contents.cls_sec_id left join classes on classes.id=class_sections.class_id LEFT JOIN sections on sections.id=class_sections.section_id WHERE  (role='student' and contents.type='" . $category . "' and contents.is_public='yes') or (classes.id =" . $class_id . " and sections.id=" . $section_id . " and role='student' and contents.type='" . $category . "') and contents.session_id =" . $session . "";

        $query = "
        SELECT 
            contents.*, 
            classes.class,
            (SELECT GROUP_CONCAT(section) 
             FROM sections 
             WHERE FIND_IN_SET(sections.id, contents.cls_sec_id)) AS section_names
        FROM content_for 
        INNER JOIN contents ON content_for.content_id = contents.id 
        LEFT JOIN classes ON classes.id = contents.class_id 
        WHERE 
            content_for.role = 'student' 
            AND contents.type = " . $this->db->escape($category) . " 
            AND contents.session_id = " . $this->db->escape($session) . " 
            AND (
                contents.is_public = 'yes' 
                OR (classes.id = " . $this->db->escape($class_id) . " 
                AND FIND_IN_SET(" . $this->db->escape($class_section_id) . ", contents.cls_sec_id))
            )
    ";

        $query = $this->db->query($query);
        return $query->result_array();
    }

    public function getListByforUser($class_id, $section_id)
    {

        if (empty($class_id)) {

            $class_id = "0";
        }

        if (empty($section_id)) {

            $section_id = "0";
        }
        $query = "SELECT contents.*,class_sections.id as `class_section_id`,classes.class,sections.section FROM `content_for` INNER JOIN contents on content_for.content_id=contents.id left JOIN class_sections on class_sections.id=contents.cls_sec_id left join classes on classes.id=class_sections.class_id LEFT JOIN sections on sections.id=class_sections.section_id WHERE  (role='student' and contents.is_public='yes') or (classes.id =" . $class_id . " and sections.id=" . $section_id . " and role='student')";
        $query = $this->db->query($query);
        return $query->result_array();
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        // Fetching file path before deleting the record
        $file = $this->db->select('file')->get_where('contents', array('id' => $id))->row()->file;

        // Deleting the file
        if ($file && file_exists($file)) {
            unlink($file);
        }

        $this->db->where('id', $id);
        $this->db->delete('contents');
        $message = DELETE_RECORD_CONSTANT . " On contents id " . $id;
        $action = "Delete";
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

    public function search_by_content_type($text)
    {
        $this->db->select()->from('contents');
        $this->db->or_like('contents.content_type', $text);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */

    public function add($data, $content_role = array())
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('contents', $data);
            $message = UPDATE_RECORD_CONSTANT . " On  contents id " . $data['id'];
            $action = "Update";
            $record_id = $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('contents', $data);
            // echo $this->db->last_query();exit;
            $insert_id = $this->db->insert_id();
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
        // return $insert_id;
    }

    public function getyearplancount($class, $section)
    {
        $this->db->where('class_id', $class);
        $this->db->where('cls_sec_id', $section);
        $this->db->where('type', 'year_plans');
        $query = $this->db->get('contents');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return $query->result();
        }
    }
}

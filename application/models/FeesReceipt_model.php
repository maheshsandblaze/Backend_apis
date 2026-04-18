<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class FeesReceipt_model extends MY_Model
{

    protected $table_name;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Customlib');

        // $this->current_session  = $this->customlib->getCurrentSession();
        // $this->load->model('setting_model');
        // $this->load->config('ci-blog');


        // $this->current_session = $this->setting_model->getCurrentSession();

        // $this->current_session = $this->setting_model->getCurrentSession();

        // $this->start_year = $this->academic_session['start_year'];
        // $this->table_name = 'student_fees_print_' . $this->start_year;
        $this->start_year = "24";
        $this->table_name = 'student_fees_print_' . $this->start_year;
    }


    public function getAcademcisession()
    {
        $academissession = $this->customlib->getCurrentSession();

        return $academissession;

    }

    public function getTableName()
    {

        $academissession = $this->getAcademcisession();


        if (!empty($academissession)) {
            $asession = $this->customlib->getAcademicSession($academissession['session']);
            // echo "<pre>";
            // print_r($asession);exit;
            $end_year = $asession['end_year'];
            $start_year = $asession['start_year'];
        } else {
        
        
            $start_year = "24";
            $end_year = "24";
        }

        $table_name = 'student_fees_print_' . $start_year;

        return $table_name;

    }




    public function get($id = null)
    {
        $this->db->select('student_fees_print.*,classes.id AS `class_id`,student_session.id as student_session_id,students.id as `student_id`,classes.class,sections.id AS `section_id`,sections.section,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`, students.cast,students.father_name')->from('student_fees_print');
        $this->db->join('student_session', 'student_session.id = student_fees_print.student_session_id');
        $this->db->join('students', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        if ($id != null) {
            $this->db->where('student_fees_print.id', $id);
        } else {
            $this->db->order_by('student_fees_print.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    public function printStudentGroupFees($receipt_id)
    {
        $this->db->select($this->table_name . '.*, classes.id AS `class_id`, student_session.id as student_session_id, students.id as `student_id`, classes.class, sections.id AS `section_id`, sections.section, students.admission_no, students.roll_no, students.admission_date, students.firstname, students.middlename, students.lastname, students.image, students.mobileno, students.email, students.state, students.city, students.pincode, students.religion, students.dob, students.current_address, students.permanent_address, IFNULL(students.category_id, 0) as `category_id`, IFNULL(categories.category, "") as `category`, students.cast,students.father_name')
            ->from($this->table_name)
            ->join('student_session', 'student_session.id = ' . $this->table_name . '.student_session_id')
            ->join('students', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            ->where($this->table_name . '.id', $receipt_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function get24fees($id = null)
    {

        $table_name = $this->getTableName();

        // echo "<pre>";
        // // print_r($this->current_session);exit;
        // print_r($this->getAcademcisession());exit;

        $academissession = $this->getAcademcisession();
        $session_id = $academissession['session_id'];

        $this->db->select($table_name . '.*, classes.id AS `class_id`, student_session.id as student_session_id, students.id as `student_id`, classes.class, sections.id AS `section_id`, sections.section, students.admission_no, students.roll_no, students.admission_date, students.firstname, students.middlename, students.lastname, students.image, students.mobileno, students.email, students.state, students.city, students.pincode, students.religion, students.dob, students.current_address, students.permanent_address, IFNULL(students.category_id, 0) as `category_id`, IFNULL(categories.category, "") as `category`, students.cast,students.father_name')
            ->from($table_name)
            ->join('student_session', 'student_session.id = ' . $table_name . '.student_session_id')
            ->join('students', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            // ->where('student_session.session_id', $this->academic_session['session_id']);
            ->where('student_session.session_id', $session_id);


        if ($id != null) {
            $this->db->where($table_name . '.id', $id);
        } else {
            $this->db->order_by($table_name . '.id');
        }


        $query = $this->db->get();
        // echo $this->db->last_query();exit;

        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    public function get24feesbetweendate($from_date, $to_date)
    {

        $table_name = $this->getTableName();

        
        $academissession = $this->getAcademcisession();
        $session_id = $academissession['session_id'];
        $this->db->select($table_name . '.*, classes.id AS class_id, student_session.id AS student_session_id, students.id AS student_id, classes.class, sections.id AS section_id, sections.section, students.admission_no, students.roll_no, students.admission_date, students.firstname, students.middlename, students.lastname, students.image, students.mobileno, students.email, students.state, students.city, students.pincode, students.religion, students.dob, students.current_address, students.permanent_address, IFNULL(students.category_id, 0) AS category_id, IFNULL(categories.category, "") AS category, students.cast, 
        students.cast,students.father_name')
            ->from($table_name)
            ->join('student_session', 'student_session.id = ' . $table_name . '.student_session_id')
            ->join('students', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            ->where('DATE(' . $table_name . '.created_at) >=', $from_date)
            ->where('DATE(' . $table_name . '.created_at) <=', $to_date)
            ->where('student_session.session_id', $session_id)
                        ->where($table_name . '.status !=', 1)


            ->order_by('FIELD(' . $table_name . '.mode, "Cash", "card", "upi")');

        $query = $this->db->get();
        return $query->result();
    }

    public function get24feesBySudent_session_id($student_session_id)
    {

        $table_name = $this->getTableName();

        $this->db->select($table_name . '.*, classes.id AS class_id, student_session.id AS student_session_id, students.id AS student_id, classes.class, sections.id AS section_id, sections.section, students.admission_no, students.roll_no, students.admission_date, students.firstname, students.middlename, students.lastname, students.image, students.mobileno, students.email, students.state, students.city, students.pincode, students.religion, students.dob, students.current_address, students.permanent_address, IFNULL(students.category_id, 0) AS category_id, IFNULL(categories.category, "") AS category, students.cast')
            ->from($table_name)
            ->join('student_session', 'student_session.id = ' . $table_name . '.student_session_id')
            ->join('students', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            ->where($table_name . '.student_session_id', $student_session_id)
            ->order_by('FIELD(' . $table_name . '.mode, "Cash", "card", "upi")');

        $query = $this->db->get();
        return $query->result();
    }

    public function getTransctionData($from_date, $to_date)
    {
        $table_name = $this->getTableName();

        $this->db->select($table_name . '.mode, SUM(' . $table_name . '.amount) AS total_amount')
            ->from($table_name)
                                    ->where($table_name . '.status !=', 1)

            ->where('DATE(' . $table_name . '.created_at) >=', $from_date)
            ->where('DATE(' . $table_name . '.created_at) <=', $to_date)
            ->group_by($table_name . '.mode');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function printStudentGroupFees24($receipt_id)
    {

        $table_name = $this->getTableName();

        $this->db->select($table_name . '.*, classes.id AS `class_id`, student_session.id as student_session_id, students.id as `student_id`, classes.class, sections.id AS `section_id`, sections.section, students.admission_no, students.roll_no, students.admission_date, students.firstname, students.middlename, students.lastname, students.image, students.mobileno, students.email, students.state, students.city, students.pincode, students.religion, students.dob, students.current_address, students.permanent_address, IFNULL(students.category_id, 0) as `category_id`, IFNULL(categories.category, "") as `category`, students.cast, students.father_name')
            ->from($table_name)
            ->join('student_session', 'student_session.id = ' . $table_name . '.student_session_id')
            ->join('students', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            ->where($table_name . '.id', $receipt_id);

        $query = $this->db->get();
        return $query->row();
    }
    
    public function update_invoice($receipt_id, $data) 
    {
        $table_name = $this->getTableName();

        $this->db->where('id', $receipt_id);
        return $this->db->update($table_name, $data);
    }
}

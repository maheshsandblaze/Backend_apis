<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Studentcertificate extends Admin_Controller {

    function __construct() {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->model('certificate_model');
    }

    public function index() {

        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/studentcertificate');
        $this->data = null;
        $admission_id   = $this->input->get('admissionid');
        $this->data['admissionid'] = $admission_id;
        $this->form_validation->set_rules('admissionid', 'Admission No', 'required|trim');
        if ($this->form_validation->run() == false && !$admission_id) {
            $this->load->view('layout/header');
            $this->load->view('admin/certificate/createstudentcertificate', $this->data);
            $this->load->view('layout/footer');
        } else {
            if (!$admission_id) {
                $admission_id   = $this->input->post('admissionid');
            }
            $this->data['admissionid'] = $admission_id;
            if ($admission_id) {
                $student_record = $this->student_model->findByAdmission($admission_id);
                $query = $this->get_bonfide($admission_id);
                $query1 = $this->get_tc($admission_id);
                if ($student_record) {
                    $this->data['student'] = $student_record;
                    $this->data['bonafidetext'] = 'Add';
                    $this->data['tctext'] = 'Add';
                    if ($query->num_rows() > 0) {
                        $this->data['bonafidetext'] = 'Edit';
                        $this->data['bonafide'] = $query->row();
                    }
                    if ($query1->num_rows() > 0) {
                        $this->data['tctext'] = 'Edit';
                        $this->data['tc'] = $query1->row();
                    }
                } else {
                    $this->data['student'] = [];
                }
               
                $this->load->view('layout/header');        
                $this->load->view('admin/certificate/createstudentcertificate', $this->data);
                $this->load->view('layout/footer');
            } else {
                $this->load->view('layout/header');
                $this->load->view('admin/certificate/createstudentcertificate', $this->data);
                $this->load->view('layout/footer');
            }

        }
    }


    public function get_bonfide($admission_id) {
        $this->db->select('*');
        $this->db->from('student_bonafide');
        $this->db->where('admission_id', $admission_id );
        $query = $this->db->get();

        return $query;
    }

    public function get_tc($admission_id) {
        $this->db->select('*');
        $this->db->from('student_tc');
        $this->db->where('admission_id', $admission_id );
        $query = $this->db->get();

        return $query;
    }

    public function create_bonafide()
    {
        $this->data = null;
        $admission_id   = $this->input->get('admission_id');
        $student_record = $this->student_model->findByAdmission($admission_id);

        $query = $this->get_bonfide($admission_id);
        if ($query->num_rows() > 0) {
            $bonafide = $query->row();

            // echo "<pre>";
            // print_r($bonafide);exit;
            $bonafide->bno = $bonafide->id;
            $bonafide->name =  $student_record->firstname . " " . $student_record->lastname;
            $bonafide->father_name = $student_record->father_name;
            $bonafide->class_of_admission = $student_record->class_of_admission;
            $bonafide->admission_date = $student_record->admission_date;
            $bonafide->classname = $student_record->class;

            $bonafide->gender = $student_record->gender;
         

            $this->data['student'] =  $bonafide;
            $this->data['student']->admission_no = $admission_id;
        } else {
            $this->data['student'] = $student_record;
            $this->data['student']->admission_no = $admission_id;
        }

        $this->load->view('layout/header');        
        $this->load->view('admin/certificate/create_bonafide', $this->data);
        $this->load->view('layout/footer');
    }

    public function create_tc()
    {
        $admission_id   = $this->input->get('admission_id');
        $student_record = $this->student_model->findByAdmission($admission_id);

        $query = $this->get_tc($admission_id);
        if ($query->num_rows() > 0) {
            $tc = $query->row();
            $tc->name = $student_record->firstname . " " . $student_record->lastname;
            $tc->father_name = $student_record->father_name;
            $tc->mother_name = $student_record->mother_name;
            $tc->religion = $student_record->religion;
            $tc->category_id = $student_record->category_id;
            $tc->category_id = $student_record->category_id;
            $tc->admission_date = $student_record->admission_date;
            $tc->dob = $student_record->dob;
            $tc->current_address = $student_record->current_address;
            $this->data['student'] =  $tc;
            $this->data['student']->admission_no = $admission_id;
        } else {
            $this->data['student'] = $student_record;
            $this->data['student']->admission_no = $admission_id;
        }

        $this->load->view('layout/header');        
        $this->load->view('admin/certificate/create_tc', $this->data);
        $this->load->view('layout/footer');
    }


    public function submit_bonafide() {
        $post_data = $this->input->post();
        if ($post_data) {
            $student_name = $this->input->post('student_name');
            $father_name = $this->input->post('father_name');
            $grade_from = $this->input->post('grade_from');
            $grade_to = $this->input->post('grade_to');
            $to = $this->input->post('to');
            $from = $this->input->post('from');
            $dob = $this->input->post('dob');
            $dob_words = $this->input->post('dob_words');
            $issued_for = $this->input->post('issued_for');
            $admission_id = $this->input->post('admission_id');
            $gender = $this->input->post('gender');
            $academicyear_from = $this->input->post('academicyear_from');
            $academicyear_to  = $this->input->post('academicyear_to');
            $no = $this->input->post('no');


            $record = [
                'name' => $student_name,
                'admission_id' => $admission_id,
                'father_name' => $father_name,
                'grade_from' => $grade_from,
                'grade_to' => $grade_to,
                'study_from' => $from,
                'study_to' => $to,
                'dob' => $dob,
                'dob_words' => $dob_words,
                'issued_for' => $issued_for,
                'issued_date' => date("d/m/y"),
                'gender' =>$gender,
                'academicyear_from' => $academicyear_from,
                'academicyear_to' =>$academicyear_to,
                'bonafide_no' => $no
            ];

            $sql = "SELECT * from student_bonafide WHERE admission_id=\"".$admission_id."\"";
            $query  = $this->db->query($sql);
            $response = [];
            if ($query->num_rows() > 0) {
                $this->db->where('admission_id', $admission_id);
                $inserted = $this->db->update('student_bonafide', $record);
                // echo $this->db->last_query();exit;
                $response['is_edited'] = true;
            } else {
                
                $inserted = $this->db->insert('student_bonafide', $record);
                // echo $this->db->last_query();exit;

            }
            $inserted_id = $this->db->insert_id();
            if ($inserted) {
                $response['success'] = true;
                $response['id'] = $inserted_id;
                $response['check_this'] = $this->customlib->getUserData();
                echo json_encode($response);
            } else {
                echo json_encode([
                    'error' => true,
                    'val' =>  $record
                ]);
            }
        }
    }

    public function preview_bonafied() {
        
        $this->load->view('layout/header');        
        $this->load->view('admin/certificate/preview_bonafied');
        $this->load->view('layout/footer');
    }
    
    public function submit_tc() {
        $post_data = $this->input->post();
        if ($post_data) {
            $name  = $this->input->post('studentName');
            $father_name  = $this->input->post('fatherName');
            $nationality  = $this->input->post('religion');
            $category  = $this->input->post('categoryId');
            $dob  = $this->input->post('dob');
            $address = $this->input->post('address');
            $school_name  = $this->input->post('school_name');
            $admission_no  = $this->input->post('admission_no');
            $admission_data = $this->input->post('admission_data');
            $class_one  = $this->input->post('class_one');
            $class_one_working_days  = $this->input->post('class_one_working_days');
            $class_one_present_days  = $this->input->post('class_one_present_days');
            $class_two  = $this->input->post('class_two');
            $class_two_working_days  = $this->input->post('class_two_working_days');
            $class_two_present_days  = $this->input->post('class_two_present_days');
            $class_three  = $this->input->post('class_three');
            $class_three_working_days  = $this->input->post('class_three_working_days');
            $class_three_present_days  = $this->input->post('class_three_present_days');
            $class_four  = $this->input->post('class_four');
            $class_four_working_days  = $this->input->post('class_four_working_days');
            $class_four_present_days  = $this->input->post('class_four_present_days');
            $class_five  = $this->input->post('class_five');
            $class_five_working_days  = $this->input->post('class_five_working_days');
            $class_five_present_days  = $this->input->post('class_five_present_days');
            $relieve_date  = $this->input->post('relieve_date');
            $progress  = $this->input->post('progress');
            $conduct  = $this->input->post('conduct');
            $completion_date  = $this->input->post('completion_date');
            $leaving_date = $this->input->post('leaving_date');
            $identification_marks = $this->input->post('identification_marks');
            $admission_date = $this->input->post('admission_date');

            $record = [
                'name' => $name,
                'father_name' => $father_name,
                'religion' => $nationality,
                'category_id' => $category,
                'dob' => $dob,
                'address' => $address,
                'school_name' => $school_name,
                'admission_id' => $admission_no,
                'admission_data' => $admission_data,
                'class_one' => $class_one,
                'class_one_working_days' => $class_one_working_days,
                'class_one_present_days' => $class_one_present_days,
                'class_two' => $class_two,
                'class_two_working_days' => $class_two_working_days,
                'class_two_present_days' => $class_two_present_days,
                'class_three' => $class_three,
                'class_three_working_days' => $class_three_working_days,
                'class_three_present_days' => $class_three_present_days,
                'class_four' => $class_four,
                'class_four_working_days' => $class_four_working_days,
                'class_four_present_days' => $class_four_present_days,
                'class_five' => $class_five,
                'class_five_working_days' => $class_five_working_days,
                'class_five_present_days' => $class_five_present_days,
                'relieve_date' => $relieve_date,
                'progress' => $progress,
                'conduct' => $conduct,
                'completion_date' => $completion_date,
                'leaving_date' => $leaving_date,
                'identification_marks' => $identification_marks,
                'admission_date' => $admission_date
            ];

            $sql = "SELECT * from student_tc WHERE admission_id=\"".$admission_no."\"";
            $query  = $this->db->query($sql);
            $response = [];
            if ($query->num_rows() > 0) {
                $this->db->where('admission_id', $admission_no);
                $inserted = $this->db->update('student_tc', $record);
                $response['is_edited'] = true;
            } else {
                
                $inserted = $this->db->insert('student_tc', $record);
            }
            $inserted_id = $this->db->insert_id();
            if ($inserted) {
                $response['success'] = true;
                $response['id'] = $inserted_id;
                echo json_encode($response);
            } else {
                echo json_encode([
                    'error' => true,
                    'val' =>  $record,
                ]);
            }
        }
    }
    
    public function preview_tc() {
        
        $this->load->view('layout/header');        
        $this->load->view('admin/certificate/preview_tc');
        $this->load->view('layout/footer');
    }

}
?>
<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Generateidcard extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
            $this->load->library('media_storage');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }
    
    
    public function get_search()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // Fetch required data
        $classlist  = $this->class_model->get();
        $idcardlist = $this->Generateidcard_model->getstudentidcard();
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'classlist'       => $classlist,
                    'idcardlist'      => $idcardlist,
                    'adm_auto_insert' => $this->sch_setting_detail->adm_auto_insert,
                    'sch_setting'     => $this->sch_setting_detail
                ]
            ]));
    }

    // public function search()
    // {
    //     if (!$this->rbac->hasPrivilege('generate_id_card', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Certificate');
    //     $this->session->set_userdata('sub_menu', 'admin/generateidcard');

    //     $class                   = $this->class_model->get();
    //     $data['classlist']       = $class;
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
    //     $data['sch_setting']     = $this->sch_setting_detail;
    //     $idcardlist              = $this->Generateidcard_model->getstudentidcard();
    //     $data['idcardlist']      = $idcardlist;
    //     $button                  = $this->input->post('search');
    //     if ($this->input->server('REQUEST_METHOD') == "GET") {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/certificate/generateidcard', $data);
    //         $this->load->view('layout/footer', $data);
    //     } else {
    //         $class   = $this->input->post('class_id');
    //         $section = $this->input->post('section_id');
    //         $search  = $this->input->post('search');
    //         $id_card = $this->input->post('id_card');
    //         if (isset($search)) {
    //             $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');

    //             $this->form_validation->set_rules('id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
    //             if ($this->form_validation->run() == false) {

    //             } else {
    //                 $data['searchby']     = "filter";
    //                 $data['class_id']     = $this->input->post('class_id');
    //                 $data['section_id']   = $this->input->post('section_id');
    //                 $id_card              = $this->input->post('id_card');
    //                 $idcardResult         = $this->Generateidcard_model->getidcardbyid($id_card);
    //                 $data['idcardResult'] = $idcardResult;
    //                 $resultlist           = $this->student_model->searchByClassSection($class, $section);
    //                 $data['resultlist']   = $resultlist;
                     
    //             }
    //         }

    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/certificate/generateidcard', $data);
    //         $this->load->view('layout/footer', $data);
    //     }
    // }
    
    
    public function search()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // Read RAW JSON
        $rawInput = json_decode(file_get_contents('php://input'), true);
    
        if (empty($rawInput)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Invalid or empty JSON body'
                ]));
        }
    
        $class_id   = $rawInput['class_id'] ?? null;
        $section_id = $rawInput['section_id'] ?? null;
        $id_card    = $rawInput['id_card'] ?? null;
    
        // Manual validation (required for raw JSON)
        $errors = [];
    
        if (!$class_id) {
            $errors['class_id'] = 'Class is required';
        }
    
        if (!$id_card) {
            $errors['id_card'] = 'ID Card Template is required';
        }
    
        if (!empty($errors)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }
    
        // Fetch ID Card Template
        $idcardResult = $this->Generateidcard_model->getidcardbyid($id_card);
    
        if (!$idcardResult) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'ID Card template not found'
                ]));
        }
    
        // Fetch Students
        $resultlist = $this->student_model->searchByClassSection(
            $class_id,
            $section_id
        );
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'searchby'     => 'filter',
                    'class_id'     => $class_id,
                    'section_id'   => $section_id,
                    'idcardResult' => $idcardResult,
                    'resultlist'   => $resultlist
                ]
            ]));
    }

    public function generate($student, $class, $idcard)
    {
        $idcardlist         = $this->Generateidcard_model->getidcardbyid($idcard);
        $data['idcardlist'] = $idcardlist;
        $resultlist         = $this->student_model->searchByClassStudent($class, $student);
        $data['resultlist'] = $resultlist;

        $this->load->view('admin/certificate/studentidcard', $data);
    }

    // public function generatemultiple()
    // {
    //     $studentid           = $this->input->post('data');
    //     $student_array       = json_decode($studentid);
    //     $idcard              = $this->input->post('id_card');
    //     $class               = $this->input->post('class_id');
    //     $data                = array();
    //     $results             = array();
    //     $std_arr             = array();
    //     $data['sch_setting'] = $this->setting_model->get();
    //     $data['id_card']     = $this->Generateidcard_model->getidcardbyid($idcard);

    //     foreach ($student_array as $key => $value) {
    //         $std_arr[] = $value->student_id;
    //     }

    //     $students = $this->student_model->getStudentsByArray($std_arr);
    //     foreach ($students as $key => $students_value) {
    //         $students[$key]->barcode = $this->customlib->generatebarcode($students_value->admission_no);
    //     }

    //     $data['students']        = $students;
    //     $data['sch_settingdata'] = $this->sch_setting_detail;

    //     $id_cards = $this->load->view('admin/certificate/generatemultiple', $data, true);
    //     echo json_encode(array('status' => 1, 'page' => $id_cards));
    // }
    
    // public function generatemultiple()
    // {

    //     $studentid           = $this->input->post('data');
    //     $student_array       = json_decode($studentid);
    //     $idcard              = $this->input->post('id_card');
    //     $class               = $this->input->post('class_id');
    //     $data                = array();
    //     $results             = array();
    //     $std_arr             = array();
    //     $data['sch_setting'] = $this->setting_model->get();
    //     $data['id_card']     = $this->Generateidcard_model->getidcardbyid($idcard);

    //     foreach ($student_array as $key => $value) {
    //         $std_arr[] = $value->student_id;
    //     }

    //     $data['students']        = $this->student_model->getStudentsByArray($std_arr);
    //     $data['sch_settingdata'] = $this->sch_setting_detail;

    //     $id_cards = $this->load->view('admin/certificate/generatemultiple', $data, true);
    //     echo $id_cards;
    // }
    
    
    public function generatemultiple()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // Read RAW JSON
        $rawInput = json_decode(file_get_contents('php://input'), true);
    
        if (empty($rawInput)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Invalid or empty JSON body'
                ]));
        }
    
        $students = $rawInput['students'] ?? [];
        $id_card  = $rawInput['id_card'] ?? null;
        $class_id = $rawInput['class_id'] ?? null;
    
        // Validation
        $errors = [];
    
        if (empty($students)) {
            $errors['students'] = 'Students array is required';
        }
    
        if (!$id_card) {
            $errors['id_card'] = 'ID Card template is required';
        }
    
        if (!$class_id) {
            $errors['class_id'] = 'Class ID is required';
        }
    
        if (!empty($errors)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }
    
        // Extract student IDs
        $std_arr = [];
        foreach ($students as $student) {
            if (!empty($student['student_id'])) {
                $std_arr[] = $student['student_id'];
            }
        }
    
        if (empty($std_arr)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'No valid student IDs found'
                ]));
        }
    
        // Fetch required data
        $data = [];
        $data['id_card']         = $this->Generateidcard_model->getidcardbyid($id_card);
        $data['students']        = $this->student_model->getStudentsByArray($std_arr);
        $data['sch_setting']     = $this->setting_model->get();
        $data['sch_settingdata'] = $this->sch_setting_detail;
        
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }

}

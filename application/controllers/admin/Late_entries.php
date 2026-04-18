<?php

use function GuzzleHttp\json_encode;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Late_entries extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->config->load("mailsms");
        $this->load->library('mailsmsconf');
        $this->config_attendance = $this->config->item('attendence');
        $this->load->model(array("classteacher_model",'class_section_time_model','late_entries_model','student_model'));
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('late_entries', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Late Entries');
        $this->session->set_userdata('sub_menu', 'late_entries/index');
        $sch_setting         = $this->setting_model->getSchoolDetail();
        $data['sch_setting'] = $this->sch_setting_detail;
     
        $data['date']       = "";

        $this->form_validation->set_rules('newadmission_no', 'admission_no', 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {

            $lateEntries             = $this->late_entries_model->get_today_entries();
            $data['late_entries'] = $lateEntries;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/late_entries/late_entriesList', $data);
            $this->load->view('layout/footer', $data);
        } else {

        
            $admission_no        = $this->input->post('newadmission_no');
            // $data['admission_no']   = $admission_no;


            $studentData = $this->student_model->findByAdmission($admission_no);

            $update_data = array();


            if(!empty($studentData))
            {

                $student_session_id =      $studentData->student_session_id;


                $insert_data = array(
                   'student_session_id' =>$student_session_id,
                   'admission_no'      => $admission_no
                );

       
               $insert_id     = $this->late_entries_model->add($insert_data,$update_data);

               if($insert_id)
               {
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>'); 
                redirect('admin/late_entries/index','refresh'); 

  
               }
               else {

                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">This was an existing admission number. </div>'); 
                redirect('admin/late_entries/index','refresh'); 
               }

        
            }
            else {

                
               $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left"> Admission number not found </div>'); 
               redirect('admin/late_entries/index','refresh'); 

            }


            
            $lateEntries             = $this->late_entries_model->get_today_entries();
            $data['late_entries'] = $lateEntries;
            // $resultlist                  = $this->stuattendence_model->searchAttendenceClassSection($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)), $type);
            // $data['resultlist']          = $resultlist;
            // echo "<pre>"; print_r($data['late_entries']) ;exit;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/late_entries/late_entriesList', $data);
            $this->load->view('layout/footer', $data);
        }
    }


    public function add()
    {
        if (!$this->rbac->hasPrivilege('late_entries', 'can_view')) {
            access_denied();
        }
    
        $this->session->set_userdata('top_menu', 'Late Entries');
        $this->session->set_userdata('sub_menu', 'late_entries/index');
        $sch_setting = $this->setting_model->getSchoolDetail();
        $data['sch_setting'] = $this->sch_setting_detail;
    
        $this->form_validation->set_rules('newadmission_no', 'Admission Number', 'trim|required|xss_clean');
    
        if ($this->form_validation->run() == false) {
            if ($this->input->is_ajax_request()) {
                $errors = validation_errors();
                echo json_encode(['status' => 0, 'error' => $errors]);
                return;
            } else {
                $lateEntries = $this->late_entries_model->get_today_entries();
                $data['late_entries'] = $lateEntries;
                $this->load->view('layout/header', $data);
                $this->load->view('admin/late_entries/late_entriesList', $data);
                $this->load->view('layout/footer', $data);
            }
        } else {


          
            $admission_no = $this->input->post('newadmission_no');
            $studentData = $this->student_model->findByAdmission($admission_no);
    
            if (!empty($studentData)) {
                $student_session_id = $studentData->student_session_id;
                $insert_data = array(
                    'student_session_id' => $student_session_id,
                    'admission_no' => $admission_no
                );

                // echo "<pre>";
                // print_r($insert_data);exit;
    
                $insert_id = $this->late_entries_model->add($insert_data);
    
                if ($insert_id) {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['status' => 1, 'message' => $this->lang->line('success_message')]);
                        return;
                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
                        redirect('admin/late_entries/index', 'refresh');
                    }
                } else {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['status' => 0, 'message' => 'This was an existing admission number.']);
                        return;
                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">This was an existing admission number.</div>');
                        redirect('admin/late_entries/index', 'refresh');
                    }
                }
            } else {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 0, 'message' => 'Admission number not found.']);
                    return;
                } else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Admission number not found.</div>');
                    redirect('admin/late_entries/index', 'refresh');
                }
            }
        }
    }
    


    public function getStudentData()
    {
        $setting_result = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $admission_no = $this->input->post('admission_no');
    
        $stuData = $this->student_model->findByAdmission($admission_no);
    
        if (!empty($stuData)) {
            $result = array(
                'status' => 'success',
                'studentData' => array(
                    'firstname' => $stuData->firstname,
                    'class' => $stuData->class,
                    'section' => $stuData->section,
                    'admission_no' => $admission_no
                ),
            );
        } else {
            $result = array(
                'status' => 'error',
                'message' => 'No record found',
            );
        }
    
        echo \json_encode($result); // Use PHP's native json_encode function with global namespace
    }
    
    

    public function attendencereport()
    {
        if (!$this->rbac->hasPrivilege('attendance_by_date', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/attendenceReport');
        $data['title']      = 'Add Fees Type';
        $data['title_list'] = 'Fees Type List';
        $class              = $this->class_model->get();
        $userdata           = $this->customlib->getUserData();

        $role_id = $userdata["role_id"];

        if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
            if ($userdata["class_teacher"] == 'yes') {
                $carray = array();
                $class  = array();
                $class  = $this->teacher_model->get_daywiseattendanceclass($userdata["id"]);
            }
        }
        $data['classlist']  = $class;
        $data['class_id']   = "";
        $data['section_id'] = "";
        $data['date']       = "";
        $data['session_type'] = "";
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('session_type', 'Session Type', 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->load->view('layout/header', $data);
            $this->load->view('admin/stuattendence/attendencereport', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class              = $this->input->post('class_id');
            $section            = $this->input->post('section_id');
            $date               = $this->input->post('date');
            $type               = $this->input->post('session_type');
            $data['class_id']   = $class;
            $data['section_id'] = $section;
            $data['date']       = $date;
            $data['session_type'] = $type;
            $search             = $this->input->post('search');
            if ($search == "saveattendence") {
                $session_ary = $this->input->post('student_session');
                foreach ($session_ary as $key => $value) {
                    $checkForUpdate = $this->input->post('attendendence_id' . $value);
                    if ($checkForUpdate != 0) {
                        $arr = array(
                            'id'                 => $checkForUpdate,
                            'student_session_id' => $value,
                            'attendence_type_id' => $this->input->post('attendencetype' . $value),
                            'date'               => date('Y-m-d', $this->customlib->datetostrtotime($date)),
                        );
                        $insert_id = $this->stuattendence_model->add($arr);
                    } else {
                        $arr = array(
                            'student_session_id' => $value,
                            'attendence_type_id' => $this->input->post('attendencetype' . $value),
                            'date'               => date('Y-m-d', $this->customlib->datetostrtotime($date)),
                        );
                        $insert_id = $this->stuattendence_model->add($arr);
                    }
                }
            }
            $attendencetypes             = $this->attendencetype_model->get();
            $data['attendencetypeslist'] = $attendencetypes;
            $resultlist                  = $this->stuattendence_model->searchAttendenceClassSectionPrepare($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)), $type);

            $data['resultlist']  = $resultlist;
            $data['sch_setting'] = $this->sch_setting_detail;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/stuattendence/attendencereport', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    

    public function monthAttendance($st_month, $no_of_months, $student_id)
    {
        $record = array();
        $r     = array();
        $month = date('m', strtotime($st_month));
        $year  = date('Y', strtotime($st_month));
        foreach ($this->config_attendance as $att_key => $att_value) {
            $s = $this->stuattendence_model->count_attendance_obj($month, $year, $student_id, $att_value);

            $attendance_key = $att_key;
            $r[$attendance_key] = $s;
        }

        $record[$student_id] = $r;
        return $record;
    }
    
    public function saveclasstime()
    {
        $this->form_validation->set_rules('row[]', $this->lang->line('section'), 'trim|required|xss_clean');
        $class_sections=$this->input->post('class_section_id');
        $time_valid=true;

       if(!empty($class_sections) && isset($class_sections)){
        foreach ($class_sections as $class_sections_key => $class_sections_value) {
        if($class_sections_value == ""){
             $this->form_validation->set_rules('time', $this->lang->line('time'), 'trim|required|xss_clean');
              $time_valid=false;
                break;
        }
        }
       }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'row' => form_error('row')
            );
            if(!$time_valid){
                $msg['time']= form_error('time');
            }

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {

        $insert_data=array();
        $update_data=array();

         $prev_records=$this->input->post('prev_record_id');
           if(!empty($class_sections) && isset($class_sections)){
            foreach ($class_sections as $class_sections_key => $class_sections_value) {

              if($prev_records[$class_sections_key] > 0){
                 $update_data[]=array(
                        'id'=>$prev_records[$class_sections_key],
                        'class_section_id'=>$class_sections_key,
                        'time'=>$this->customlib->timeFormat($class_sections_value, true),
                    );

              }else{
                 $insert_data[]=array(
                        'class_section_id'=>$class_sections_key,
                        'time'=>$this->customlib->timeFormat($class_sections_value, true),
                    );
              }                  
               
             }
            }

             $this->class_section_time_model->add($insert_data, $update_data);

             $array = array('status' =>1 , 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);

    }
}

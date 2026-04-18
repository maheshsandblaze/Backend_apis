<?php

// use function GuzzleHttp\json_encode;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class My_day_today extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->config->load("mailsms");
        $this->load->library('mailsmsconf');
        $this->load->library('media_storage');
        $this->config->load('front_office');
        $this->config_attendance = $this->config->item('attendence');
        $this->load->model(array("classteacher_model",'class_section_time_model','my_day_today_model','student_model'));
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }
    
    public function handle_upload($str, $var)
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }

            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_extension_error_uploading_image'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/my_day_today');
        $sch_setting         = $this->setting_model->getSchoolDetail();
        $data['sch_setting'] = $this->sch_setting_detail;
     
        $data['date']       = "";

        //$this->form_validation->set_rules('newadmission_no', 'admission_no', 'trim|required|xss_clean');
        $this->form_validation->set_rules('my_snack', 'My Snack', 'trim|xss_clean');
        $this->form_validation->set_rules('my_lunch', 'My Lunch', 'trim|xss_clean');
        // $this->form_validation->set_rules('guardian_name', 'Guardian Name', 'trim|required|xss_clean');
        // $this->form_validation->set_rules('guardian_phone', 'Guardian Phone', 'trim|required|xss_clean|numeric');
        // $this->form_validation->set_rules('visitorpurpose', 'Purpose', 'trim|required|xss_clean');
        // $this->form_validation->set_rules('time', 'Time', 'trim|required|xss_clean');
        // $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
        // $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload[file]');
        // $this->form_validation->set_rules('visitor_relation', 'Visitor Relation', 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {

            $visitorlist             = $this->my_day_today_model->get_today_entries();
            
            $data['mydaytoday_list'] = $visitorlist;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/my_day_today', $data);
            $this->load->view('layout/footer', $data);
        } else {

            
            $admission_no        = $this->input->post('newadmission_no');
            
            $studentData = $this->student_model->findByAdmission($admission_no);


            if(!empty($studentData))
            {

                

                $student_session_id =      $studentData->student_session_id;
                $student_id = $studentData->id;
                
                $insert_data = array(
                    'student_id'            =>  $student_id,
                    'student_name'          =>  $this->input->post('studentname'),
                    'iwas'                  =>  $this->input->post('iwas'),
                    'drank_when1'           =>  $this->input->post('when1'),
                    'drank_when2'           =>  $this->input->post('when2'),
                    'drank_when3'           =>  $this->input->post('when3'),
                    'drank_when4'           =>  $this->input->post('when4'),
                    'drank_howmuch1'        =>  $this->input->post('howmuch1'),
                    'drank_howmuch2'        =>  $this->input->post('howmuch2'),
                    'drank_howmuch3'        =>  $this->input->post('howmuch3'),
                    'drank_howmuch4'        =>  $this->input->post('howmuch4'),
                    'slept_when'            =>  $this->input->post('slept_when'),
                    'slept_howlong'         =>  $this->input->post('slept_howlong'),
                    'my_snack'              =>  $this->input->post('my_snack'),
                    'my_lunch'              =>  $this->input->post('my_lunch'),
                    'we_time'               =>  $this->input->post('we_time'),
                    'gross_motor'           =>  $this->input->post('gross_motor'),
                    'fine_motor'            =>  $this->input->post('fine_motor'),
                    'free_play'             =>  $this->input->post('free_play'),
                    'study_time'            =>  $this->input->post('study_time'),
                    'poo_pee1'              =>  $this->input->post('poo_pee1'),
                    'poo_pee_text1'         =>  $this->input->post('poo_pee_text1'),
                    'poo_pee2'              =>  $this->input->post('poo_pee2'),
                    'poo_pee_text2'         =>  $this->input->post('poo_pee_text2'),
                    'poo_pee3'              =>  $this->input->post('poo_pee3'),
                    'poo_pee_text3'         =>  $this->input->post('poo_pee_text3'),
                    'poo_pee4'              =>  $this->input->post('poo_pee4'),
                    'poo_pee_text4'         =>  $this->input->post('poo_pee_text4'),
                    'need'                  =>  $this->input->post('need1'),
                    'note'                  =>  $this->input->post('note'),
                    'date'                  =>  date("Y-m-d"),
                    'admission_no'          =>  $admission_no,
                );
                

       
               $insert_id     = $this->my_day_today_model->add($insert_data);

            //    echo "comming";

            //    print_r($insert_id);

            //    echo $insert_id;exit;


               if($insert_id)
               {

                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>'); 


                echo json_encode(['status' => 1, 'message' => $this->lang->line('success_message')]);


                redirect('admin/visitor_management/index','refresh'); 

  
               }
               else {

                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">This was an existing admission number. </div>'); 
                redirect('admin/frontoffice/my_day_today','refresh');
               
               }

        
            }
            else {

                
              $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left"> Admission number not found </div>'); 
              redirect('admin/frontoffice/my_day_today','refresh');

            }


            
            $visitorlist             = $this->my_day_today_model->get_today_entries();
        
            $data['mydaytoday_list'] = $visitorlist;
            // $resultlist                  = $this->stuattendence_model->searchAttendenceClassSection($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)), $type);
            // $data['resultlist']          = $resultlist;
            // echo "<pre>"; print_r($data['late_entries']) ;exit;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/my_day_today', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    
    public function mydaytoday_detail($id)
    {
      
        $data["title"]           = "My Day Today";
        
        $result                  = $this->my_day_today_model->getRecord($id);

        
        $data["result"]       = $result;

        

        $this->load->view("admin/frontoffice/mydaytoday_detail", $data);
    }
    

    public function add()
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_view')) {
            access_denied();
        }
    
        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/my_day_today');
        $sch_setting = $this->setting_model->getSchoolDetail();
        $data['sch_setting'] = $this->sch_setting_detail;
    
        $this->form_validation->set_rules('newadmission_no', 'Admission Number', 'trim|required|xss_clean');
    
        if ($this->form_validation->run() == false) {
            if ($this->input->is_ajax_request()) {
                $errors = validation_errors();
                echo json_encode(['status' => 0, 'error' => $errors]);
                return;
            } else {
                $lateEntries = $this->my_day_today_model->get_today_entries();
                $data['late_entries'] = $lateEntries;
                $this->load->view('layout/header', $data);
                $this->load->view('admin/frontoffice/my_day_today', $data);
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
    
                $insert_id = $this->my_day_today_model->add($insert_data);
    
                if ($insert_id) {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['status' => 1, 'message' => $this->lang->line('success_message')]);
                        return;
                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
                        redirect('admin/visitor_management/index', 'refresh');
                    }
                } else {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['status' => 0, 'message' => 'This was an existing admission number.']);
                        return;
                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">This was an existing admission number.</div>');
                        redirect('admin/visitor_management/index', 'refresh');
                    }
                }
            } else {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 0, 'message' => 'Admission number not found.']);
                    return;
                } else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Admission number not found.</div>');
                    redirect('admin/visitor_management/index', 'refresh');
                }
            }
        }
    }
    

    public function remove()
    {
        $mydaytodayid = $this->input->post('mydaytodayid');
        $this->my_day_today_model->remove($mydaytodayid);
        $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
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
                    'admission_no' => $admission_no,
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

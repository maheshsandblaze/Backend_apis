<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class My_day_today extends Student_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->load->model("filetype_model");
        $this->load->model("my_day_today_model");
        $this->load->library('mailsmsconf');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'my_day_today');
        
        $student_id             = $this->customlib->getStudentSessionUserID();
        $student                = $this->student_model->get($student_id);
        $data['results']        = $this->my_day_today_model->get_mydaytoday_student($student_id);
        
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/student/my_day_today', $data);
        $this->load->view('layout/student/footer', $data);
    }
    
    public function mydaytoday_detail($id)
    {
        $data["title"]           = "My Day Today";
        
        $result                  = $this->my_day_today_model->getStudentRecord($id);

        
        $data["result"]       = $result;

        

        $this->load->view("user/student/mydaytoday_detail", $data);
    }

}

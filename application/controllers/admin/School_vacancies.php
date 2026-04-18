<?php

class School_vacancies extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("schoolvacancies_model");
        $this->load->model('class_model');
        $this->load->model('session_model');
        $this->load->model('section_model');
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date    = $this->setting_model->getDateYmd();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('school_vacancies', 'can_view')) {
            access_denied();
        }

  
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'admin/school_vacancies');
        $data['title']       = $this->lang->line('add_school_vacancies');
        $data["house_name"]  = "";
        $data["description"] = "";
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        // $session_result      = $this->session_model->getAllSession();
        // $data['sessionlist'] = $session_result;
        $houselist           = $this->schoolvacancies_model->get();
        // echo "<pre>";
        // print_r($houselist);exit;
        $data["houselist"]   = $houselist;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/schoolvacancies/schoolclasslist', $data);
        $this->load->view('layout/footer', $data);
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('school_vacancies', 'can_add')) {
            access_denied();
        }
        $data['title']       = $this->lang->line('add_school_house');
        $houselist           = $this->schoolvacancies_model->get();
        $data["houselist"]   = $houselist;
        $data["house_name"]  = "";
        $data["description"] = "";
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('vacancies', $this->lang->line('vacancies'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/schoolvacancies/schoolclasslist', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'class_id'  => $this->input->post('class_id'),
                'section_id'   =>  $this->input->post('section_id'),
                'vacancies' => $this->input->post('vacancies'),
                'session' => $this->current_session
            );


            $this->schoolvacancies_model->add($data);

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/school_vacancies/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('school_vacancies', 'can_edit')) {
            access_denied();
        }
        $data['title']       = $this->lang->line('edit_school_house');
        $houselist           = $this->schoolvacancies_model->get();
        $data["houselist"]   = $houselist;
        $data['id']          = $id;
        $house               = $this->schoolvacancies_model->get($id);
        // echo "<pre>";
        // print_r($house);exit;
        $data["house"]       = $house;
        $data["class_id"]  = $house["class_id"];
        $data["section_id"] = $house["section_id"];
        $data["intakes"] = $house["intakes"];
        $data["vacancies"] = $house["vacancies"];

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $sections                   = $this->section_model->getClassBySection($house['class_id']);
        // echo '<pre>';
        // print_r($sections);exit;
        $data['sectionslist']       = $sections;


        $this->form_validation->set_rules('house_name', $this->lang->line('name'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/schoolvacancies/schoolclasslist', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'class_id'  => $this->input->post('class_id'),
                'section_id'   =>  $this->input->post('section_id'),
                'vacancies' => $this->input->post('vacancies'),
                'session' => $this->current_session
            );
            $this->schoolvacancies_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/school_vacancies');
        }
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('school_vacancies', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->schoolvacancies_model->delete($id);
            $this->session->set_flashdata('msgdelete', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        }
        redirect('admin/school_vacancies/');
    }

}

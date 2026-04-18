<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Leadmanagement extends Admin_Controller
{
 
    public function __construct()
    {
        parent::__construct();

        $this->time               = strtotime(date('d-m-Y H:i:s'));
        $this->payment_mode       = $this->customlib->payment_mode();
        $this->search_type        = $this->customlib->get_searchtype();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library('media_storage');
        $this->load->model('Leads_management_model');
    }
    
    public function index()
    {

        $data['stff_list']      = $this->staff_model->get_agents();
        $this->session->set_userdata('top_menu', 'Leads');
        $this->session->set_userdata('sub_menu', 'Leads/assign');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/collection_report');
        $subtotal = false;

        $data['classlist']        = $this->class_model->get();
        $data['assign_list']    = $this->Leads_management_model->getLeads();
        
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/leads/assign_leads', $data);
        $this->load->view('layout/footer', $data);
    }
    
    public function addleads()
    {
        $this->session->set_userdata('top_menu', 'Leads');
        $this->session->set_userdata('sub_menu', 'Leads/assign');
        
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        
        $data = array(
            'class_id'  =>  $this->input->post('class_id'),
            'section_id'    =>  $this->input->post('section_id'),
            'staff_id'  =>  $this->input->post('assigned'),
        );
        
        $insert_data = $this->Leads_management_model->add($data);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
        redirect('admin/leadmanagement');
    }
    
    
    public function delete($id) {
        
        $this->Leads_management_model->lead_delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/leadmanagement');
        
    }
    
}

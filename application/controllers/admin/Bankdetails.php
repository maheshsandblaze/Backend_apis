<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bankdetails extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("bank_model");
        $this->load->library('media_storage');
        $this->load->library('Customlib');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('fees_type', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'bank/index');

        
        $this->form_validation->set_rules('account_name', $this->lang->line('account_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('account_no', $this->lang->line('account_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bank_name', $this->lang->line('bank_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('branch_name', $this->lang->line('branch_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ifsc_code', $this->lang->line('ifsc_code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo'), 'callback_handle_upload[logo_img]');
        
        if ($this->form_validation->run() == false) {
            
            $feegroup_result     = $this->bank_model->get();
            $data['feetypeList'] = $feegroup_result;
    
            $this->load->view('layout/header', $data);
            $this->load->view('admin/bank/bankList', $data);
            $this->load->view('layout/footer', $data);

        } else {
            $data = array(
                'account_name'          => $this->input->post('account_name'),
                'account_number'        => $this->input->post('account_no'),
                'bank_name'             => $this->input->post('bank_name'),
                'branch_name'           => $this->input->post('branch_name'),
                'ifsc_code'             => $this->input->post('ifsc_code'),
            );
            
            if (!empty($_FILES['logo_img']['name'])) {
                $logo_img_name = $this->media_storage->fileupload("logo_img", "./uploads/bankdetails/");
            } else {
                $logo_img_name = '';
            }
            $data['header_image'] = $logo_img_name;
            
            $this->bank_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/bankdetails/index');
        }
        
    }
    
    public function handle_upload($str, $var)
    {
        $image_validate = $this->config->item('image_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = @getimagesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }

                if ($file_size > $result->image_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->image_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed_or_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('fees_type', 'can_delete')) {
            access_denied();
        }

        $this->bank_model->remove($id);
        redirect('admin/bankdetails/index');
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('fees_type', 'can_edit')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'bank/index');
        $data['id']          = $id;
        $feetype             = $this->bank_model->get($id);
        $data['feetype']     = $feetype;
        $feegroup_result     = $this->bank_model->get();
        $data['feetypeList'] = $feegroup_result;
        
        $this->form_validation->set_rules('account_name', $this->lang->line('account_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('account_no', $this->lang->line('account_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bank_name', $this->lang->line('bank_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('branch_name', $this->lang->line('branch_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ifsc_code', $this->lang->line('ifsc_code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo'), 'callback_handle_upload[logo_img]');
        
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/bank/bankEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'          => $id,
                'account_name'          => $this->input->post('account_name'),
                'account_number'        => $this->input->post('account_no'),
                'bank_name'             => $this->input->post('bank_name'),
                'branch_name'           => $this->input->post('branch_name'),
                'ifsc_code'             => $this->input->post('ifsc_code'),
            );
            
            $removelogo_image       = $this->input->post('removelogo_image');
            
            if ($removelogo_image != '') {
                $data['header_image'] = '';
            }
            
            if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
                $logo_img     = $this->media_storage->fileupload("logo_img", "./uploads/bankdetails/");
                $data['header_image'] = $logo_img;
            }

            if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
                $this->media_storage->filedelete($feetype['header_image'], "uploads/bankdetails/");
            }
            
            $this->bank_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/bankdetails/index');
        }
    }

}

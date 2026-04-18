<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Smsconfig extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('smsconfig_model');
        $this->load->library('form_validation');
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input ?: [];
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $sms_result = $sms_result = $this->smsconfig_model->get();
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'     => 'success',
                'smslist'    => $sms_result,
                'statuslist' => $this->customlib->getStatus()
            ]));
    }

    public function smscountry()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('smscountry', $this->lang->line('username'), 'required');
        $this->form_validation->set_rules('smscountrypassword', $this->lang->line('password'), 'required');
        $this->form_validation->set_rules('smscountrysenderid', $this->lang->line('sender_id'), 'required');
        $this->form_validation->set_rules('smscountry_status', $this->lang->line('status'), 'required');
        $this->form_validation->set_rules('smscountryauthKey', $this->lang->line('auth_Key'), 'required');
        $this->form_validation->set_rules('smscountryauthtoken', $this->lang->line('authentication_token'), 'required');

        if ($this->form_validation->run()) {
            $data = array(
                'type'      => 'smscountry',
                'username'  => $input['smscountry'],
                'password'  => $input['smscountrypassword'],
                'senderid'  => $input['smscountrysenderid'],
                'is_active' => $input['smscountry_status'],
                'authkey'   => $input['smscountryauthKey'],
                'api_id'    => $input['smscountryauthtoken'],
            );
            $this->smsconfig_model->add($data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('update_message')
                ]));
        } else {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        }
    }


}
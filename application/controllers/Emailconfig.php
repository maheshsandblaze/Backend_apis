<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Emailconfig extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->library('mailsmsconf');
        $this->load->model('emailconfig_model');
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

        $input = $this->_get_input();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('email_type', $this->lang->line('email_type'), 'required');
            
            if ($input['email_type'] == "smtp") {
                $this->form_validation->set_rules('smtp_server', $this->lang->line('smtp_server'), 'required');
            }

            if ($input['email_type'] == "aws_ses") {
                $this->form_validation->set_rules('aws_email', $this->lang->line('email'), 'required');
                $this->form_validation->set_rules('access_key', $this->lang->line('access_key_id'), 'required');
                $this->form_validation->set_rules('secret_access_key', $this->lang->line('secret_access_key'), 'required');
                $this->form_validation->set_rules('region', $this->lang->line('region'), 'required');
            }

            if ($this->form_validation->run() === false) {
                 return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $email = '';
                if ($input['email_type'] == "aws_ses") {
                    $email = $input["aws_email"];
                } elseif ($input['email_type'] == "smtp") {
                    $email = $input['smtp_username'];
                }

                $data_insert = array(
                    'email_type'    => $input['email_type'],
                    'smtp_username' => $email,
                    'smtp_password' => $input['smtp_password'] ?? null,
                    'smtp_server'   => $input['smtp_server'] ?? null,
                    'smtp_port'     => $input['smtp_port'] ?? null,
                    'ssl_tls'       => $input['smtp_security'] ?? null,
                    'smtp_auth'     => $input['smtp_auth'] ?? 'false',
                    'api_key'       => $input['access_key'] ?? null,
                    'api_secret'    => $input['secret_access_key'] ?? null,
                    'region'        => $input['region'] ?? null,
                    'is_active'     => 'yes',
                );

                $id = $this->emailconfig_model->add($data_insert);
                
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('update_message'),
                        'id'      => $id
                    ]));
            }
        }

        // GET behavior
        $emaillist       = $this->emailconfig_model->get();
        $smtp_auth       = $this->config->item('smtp_auth');
        $smtp_encryption = $this->config->item('smtp_encryption');
        $mailMethods     = $this->customlib->getMailMethod();

        if (empty($emaillist)) {
            $emaillist = [
                'email_type'    => "",
                'smtp_server'   => "",
                'smtp_port'     => "",
                'smtp_username' => "",
                'smtp_password' => "",
                'ssl_tls'       => "",
                'smtp_auth'     => "false"
            ];
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'          => 'success',
                'emaillist'       => $emaillist,
                'mailMethods'     => $mailMethods,
                'smtp_auth'       => $smtp_auth,
                'smtp_encryption' => $smtp_encryption
            ]));
    } 

}

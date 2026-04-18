<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sessions extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
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

        $session_result = $this->session_model->getAllSession();
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 'success',
                'sessionlist' => $session_result
            ]));
    }

    public function view($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $session = $this->session_model->get($id);
        
        if (!$session) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Session not found']));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'session' => $session
            ]));
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $result = $this->session_model->remove($id);
        
        if ($result) {
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('delete_message')
                ]));
        } else {
             return $this->output
                ->set_status_header(500)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Failed to delete session']));
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('session', $this->lang->line('session'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {
            $data = array(
                'session' => $input['session'],
            );
            $insert_id = $this->session_model->add($data);
            
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message'),
                    'id'      => $insert_id
                ]));
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('session', $this->lang->line('session'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $session = $this->session_model->get($id);
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'errors'  => $this->form_validation->error_array(),
                    'session' => $session
                ]));
        } else {
            $data = array(
                'id'      => $id,
                'session' => $input['session'],
            );
            $this->session_model->add($data);
            
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('update_message')
                ]));
        }
    }

}
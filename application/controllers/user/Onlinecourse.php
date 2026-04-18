<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onlinecourse extends Public_Controller
{
    public $sch_setting_detail = array();
    public function __construct()
    {
        parent::__construct();
        $this->config->load('app-config');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library('mailsmsconf');
        $this->load->library('media_storage');
        $this->load->model('onlinecourse_model');
    }

    public function index()
    {
        // ===============================
        // HANDLE PREFLIGHT
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ===============================
        // ONLY GET METHOD
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ===============================
        // TOKEN VALIDATION
        // ===============================
        $auth = $this->auth->validate_user();

        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthorized'
                ]));
        }

        // ===============================
        // GET CATEGORY LIST
        // ===============================
        $category_list = $this->onlinecourse_model->get_categories();

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'category_list' => $category_list ?? []
                ]
            ]));
    }

    public function list($id = null)
    {
        // ===============================
        // HANDLE PREFLIGHT
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ===============================
        // ONLY GET METHOD
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ===============================
        // TOKEN VALIDATION
        // ===============================
        $auth = $this->auth->validate_user();

        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthorized'
                ]));
        }

        if (empty($id)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Category ID is required'
                ]));
        }

        // ===============================
        // GET VIDEO LIST
        // ===============================
        $video_list = $this->onlinecourse_model->getVideosBycatID($id);

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'category_id' => $id,
                    'video_list'  => $video_list ?? []
                ]
            ]));
    }



    public function add()
    {
        $this->form_validation->set_rules('category_name', $this->lang->line('category_title'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'category_name'               => form_error('category_name'),
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $insert_data = array(
                'category_name'               => $this->input->post('category_name'),
                'session_id'                  => $this->setting_model->getCurrentSession(),
            );
            //print_r($insert_data);exit;
            $this->onlinecourse_model->add($insert_data);


            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function add_video()
    {  // echo "1";exit;
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
        // $this->form_validation->set_rules('type', $this->lang->line('type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'title'               => form_error('title'),
                //'type'               => form_error('type'),
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $insert_data = array(
                'category_id'               => $this->input->post('category_id'),
                'title'                       => $this->input->post('title'),
                //'type'               		=> $this->input->post('type'),
                'url'                       => $this->input->post('url'),
            );
            //print_r($insert_data);exit;
            $this->onlinecourse_model->add_video($insert_data);


            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }
}

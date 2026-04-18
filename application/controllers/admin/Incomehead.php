<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Incomehead extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->model('incomehead_model');
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input;
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $category_result = $this->incomehead_model->get();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $category_result
            ]));
    }

    public function fetch($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $category = $this->incomehead_model->get($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $category
            ]));
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!empty($id)) {
            $this->incomehead_model->remove($id);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $this->lang->line('delete_message')
                ]));
        } else {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'ID is required'
                ]));
        }
    }

    public function create()
    {
        /* =========================
       CORS
    ========================== */
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        /* =========================
       INPUT
    ========================== */
        $input = $this->_get_input();

        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        /* =========================
       VALIDATION
    ========================== */
        $this->form_validation->set_rules(
            'incomehead',
            $this->lang->line('income_head'),
            'trim|required|xss_clean'
        );

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }


        $incomehead = trim($input['incomehead']);

        $exists = $this->incomehead_model
            ->checkIncomeHeadExists($incomehead); // create this

        if ($exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Income head already exists'
                ]));
        }

        $data = [
            'income_category' => $incomehead,
            'description'     => $input['description'] ?? '',
        ];

        $insert_id = $this->incomehead_model->add($data);


        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message'),
                'id'      => $insert_id
            ]));
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

        $this->form_validation->set_rules('incomehead', $this->lang->line('income_head'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {

            // $incomehead = trim($input['incomehead']);

            // $exists = $this->incomehead_model
            //     ->checkIncomeHeadExists($incomehead); // create this

            // if ($exists) {
            //     return $this->output
            //         ->set_status_header(409)
            //         ->set_output(json_encode([
            //             'status' => false,
            //             'message' => 'Income head already exists'
            //         ]));
            // }

            $data = array(
                'id'              => $id,
                'income_category' => $input['incomehead'],
                'description'     => $input['description'] ?? '',
            );
            $this->incomehead_model->add($data);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $this->lang->line('update_message')
                ]));
        }
    }
}

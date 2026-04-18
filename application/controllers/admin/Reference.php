<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reference extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("reference_model");
    }

    public function index()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        $list = $this->reference_model->reference_list();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $list
            ]));
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read JSON or POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validation
        $required_fields = [
            'reference' => 'Reference'
        ];

        $errors = validateRequired($input, $required_fields);
        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }

        $data = [
            'reference'   => $input['reference'],
            'description' => $input['description'] ?? null
        ];
        
        // Prevent duplicate entry
        if ($this->reference_model->existsByReference($input['reference'])) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Reference already exists'
                ]));
        }

        $this->reference_model->add($data);

        return $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }


    public function edit($reference_id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }



        if (empty($reference_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'id' => 'Reference ID is required'
                    ]
                ]));
        }

        // Read input
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validation
        $required_fields = [
            'reference' => 'Reference'
        ];

        $errors = validateRequired($input, $required_fields);
        if ($errors) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        }

        // Check exists
        $existing = $this->reference_model->reference_list($reference_id);
        if (!$existing) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Record not found'
                ]));
        }

        $data = [
            'reference'   => $input['reference'],
            'description' => $input['description'] ?? null
        ];

        $this->reference_model->update($reference_id, $data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('update_message'),
                'id'      => $reference_id
            ]));
    }


    public function delete($id = null)
    {
     
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }   

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }


        if (empty($id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'id' => 'Reference ID is required'
                    ]
                ]));
        }

        $existing = $this->reference_model->reference_list($id);
        if (!$existing) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Record not found'
                ]));
        }

        $this->reference_model->delete($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('delete_message'),
                'id'      => $id
            ]));
    }
}

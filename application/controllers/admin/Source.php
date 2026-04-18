<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Source extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');

        $this->load->model("source_model");
    }

    public function index()
    {

        $list = $this->source_model->source_list();

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
            'source' => 'Source'
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
            'source'      => $input['source'],
            'description' => $input['description'] ?? null
        ];
        
        
        // Prevent duplicate
        if ($this->source_model->existsBySource($input['source'])) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Source already exists'
                ]));
        }
        

        $this->source_model->add($data);

        return $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Source added successfully'
            ]));
    }


    public function edit($source_id = null)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (empty($source_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => ['id' => 'Source ID is required']
                ]));
        }

        // Read input
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validation
        $required_fields = [
            'source' => 'Source'
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
        $existing = $this->source_model->source_list($source_id);
        if (!$existing) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Record not found'
                ]));
        }

        $data = [
            'source'      => $input['source'],
            'description' => $input['description'] ?? null
        ];

        $this->source_model->update($source_id, $data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Source updated successfully',
                'id'      => $source_id
            ]));
    }

    public function delete($id = null)
    {
     
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


        if (empty($id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => ['id' => 'Source ID is required']
                ]));
        }

        $existing = $this->source_model->source_list($id);
        if (!$existing) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Record not found'
                ]));
        }

        $this->source_model->delete($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Source deleted successfully',
                'id'      => $id
            ]));
    }
}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Visitorspurpose extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("visitors_purpose_model");
    }

    public function index()
    {




        $data['visitors_purpose_list'] = $this->visitors_purpose_model->visitors_purpose_list();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'data'    => $data
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

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }


        // -----------------------------
        // CALL VALIDATION FUNCTION
        // -----------------------------
        $required_fields = [
            'visitors_purpose'          => 'Visiotor Puspose',
            'description'        => "Description"
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

        $visitors_purpose = array(
            'visitors_purpose' => $input['visitors_purpose'],
            'description'      => $input['description'],
        );
        $res =   $this->visitors_purpose_model->add($visitors_purpose);



        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message'),
            ]));
    }

    public function edit($visitors_purpose_id = null)
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        if (empty($visitors_purpose_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'id' => 'Visitors purpose ID is required'
                    ]
                ]));
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $required_fields = [
            'visitors_purpose' => 'Visitors Purpose'
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

        $existing = $this->visitors_purpose_model
            ->visitors_purpose_list($visitors_purpose_id);

        if (!$existing) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Record not found'
                ]));
        }

        $data = [
            'visitors_purpose' => $input['visitors_purpose'],
            'description'      => $input['description'] ?? null
        ];

        $this->visitors_purpose_model->update($visitors_purpose_id, $data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Visitors purpose updated successfully',
                'id'      => $visitors_purpose_id
            ]));
    }


    public function delete($id = null)
    {


        // if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        //     return $this->output
        //         ->set_status_header(405)
        //         ->set_output(json_encode([
        //             'status'  => false,
        //             'message' => 'Method Not Allowed'
        //         ]));
        // }

        if (empty($id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'id' => 'Visitors purpose ID is required'
                    ]
                ]));
        }


    

        $existing = $this->visitors_purpose_model->visitors_purpose_list($id);
        if (!$existing) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Record not found'
                ]));
        }

        $this->visitors_purpose_model->delete($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Visitors purpose deleted successfully',
                'id'      => $id
            ]));
    }
}

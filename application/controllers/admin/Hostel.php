<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hostel extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
    }

    public function index()
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



        $response = [
            'listhostel' => $this->hostel_model->listhostel(),
            'ght'        => $this->customlib->getHostaltype()
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $response
            ]));
    }


    public function create()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode(['status' => false]));
        }



        $input = json_decode(file_get_contents('php://input'), true) ?? $this->input->post();

        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('hostel_name', 'Hostel Name', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required|trim');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        $data = [
            'hostel_name' => $input['hostel_name'],
            'type'        => $input['type'],
            'address'     => $input['address'] ?? '',
            'intake'      => $input['intake'] ?? 0,
            'description' => $input['description'] ?? ''
        ];

        $this->hostel_model->addhostel($data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Hostel added successfully'
            ]));
    }

    public function edit()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode(['status' => false]));
        }



        $input = json_decode(file_get_contents('php://input'), true) ?? $this->input->post();

        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('id', 'ID', 'required');
        $this->form_validation->set_rules('hostel_name', 'Hostel Name', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required|trim');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        $data = [
            'id'          => $input['id'],
            'hostel_name' => $input['hostel_name'],
            'type'        => $input['type'],
            'address'     => $input['address'] ?? '',
            'intake'      => $input['intake'] ?? 0,
            'description' => $input['description'] ?? ''
        ];

        $this->hostel_model->addhostel($data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Hostel updated successfully'
            ]));
    }


    public function delete()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode(['status' => false]));
        }



        $input = json_decode(file_get_contents('php://input'), true) ?? $this->input->post();

        if (empty($input['id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Hostel ID required'
                ]));
        }

        $this->hostel_model->remove($input['id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Hostel deleted successfully'
            ]));
    }
}

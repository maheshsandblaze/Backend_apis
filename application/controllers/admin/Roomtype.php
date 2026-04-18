<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Roomtype extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }



        $response = [
            'roomtypelist' => $this->roomtype_model->get(),
            'listroomtype' => $this->roomtype_model->lists(),
            'ght'          => $this->customlib->getHostaltype()
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
        /* =========================
       CORS
    ========================== */
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        /* =========================
       INPUT
    ========================== */
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        /* =========================
       VALIDATION
    ========================== */
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('room_type', 'Room Type', 'required|trim');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        /* =========================
       DUPLICATE CHECK 🔥
    ========================== */
        $room_type = trim($input['room_type']);

        $exists = $this->roomtype_model
            ->checkRoomTypeExists($room_type); // create this function

        if ($exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Room type already exists'
                ]));
        }

        /* =========================
       INSERT
    ========================== */
        $data = [
            'room_type'   => $room_type,
            'description' => $input['description'] ?? ''
        ];

        $this->roomtype_model->add($data);

        /* =========================
       RESPONSE
    ========================== */
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Room type added successfully'
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
        $this->form_validation->set_rules('room_type', 'Room Type', 'required|trim');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        /* =========================
       DUPLICATE CHECK 🔥
    ========================== */
        // $room_type = trim($input['room_type']);

        // $exists = $this->roomtype_model
        //     ->checkRoomTypeExists($room_type); // create this function

        // if ($exists) {
        //     return $this->output
        //         ->set_status_header(409)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Room type already exists'
        //         ]));
        // }


        $data = [
            'id'          => $input['id'],
            'room_type'   => $input['room_type'],
            'description' => $input['description'] ?? ''
        ];

        $this->roomtype_model->add($data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Room type updated successfully'
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
                    'message' => 'Room type ID required'
                ]));
        }

        $this->roomtype_model->remove($input['id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Room type deleted successfully'
            ]));
    }
}

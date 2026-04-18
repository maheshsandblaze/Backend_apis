<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hostelroom extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->model("classteacher_model");
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }




        // -----------------------------
        // MASTER DATA (always returned)
        // -----------------------------
        $response = [
            'roomtypelist'   => $this->roomtype_model->get(),
            'hostellist'     => $this->hostel_model->get(),
            'hostelroomlist' => $this->hostelroom_model->lists()
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



        // POST only
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }




        $input = json_decode(file_get_contents('php://input'), true) ?? $this->input->post();

        // Validation
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('hostel_id', 'Hostel', 'required');
        $this->form_validation->set_rules('room_type_id', 'Room Type', 'required');
        $this->form_validation->set_rules('room_no', 'Room No', 'required');
        $this->form_validation->set_rules('no_of_bed', 'Beds', 'required|numeric');
        $this->form_validation->set_rules('cost_per_bed', 'Cost', 'required|numeric');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        $data = [
            'hostel_id'    => $input['hostel_id'],
            'room_type_id' => $input['room_type_id'],
            'room_no'      => $input['room_no'],
            'no_of_bed'    => $input['no_of_bed'],
            'cost_per_bed' => convertCurrencyFormatToBaseAmount($input['cost_per_bed']),
            'description'  => $input['description'] ?? ''
        ];

        $this->hostelroom_model->add($data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Hostel room added successfully'
            ]));
    }


    public function getRoom()
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

        $rooms = $this->hostelroom_model->getRoomByHoselID($input['hostel_id'] ?? 0);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $rooms
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
        $this->form_validation->set_rules('hostel_id', 'Hostel', 'required');
        $this->form_validation->set_rules('room_type_id', 'Room Type', 'required');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        $data = [
            'id'           => $input['id'],
            'hostel_id'    => $input['hostel_id'],
            'room_type_id' => $input['room_type_id'],
            'room_no'      => $input['room_no'],
            'no_of_bed'    => $input['no_of_bed'],
            'cost_per_bed' => convertCurrencyFormatToBaseAmount($input['cost_per_bed']),
            'description'  => $input['description'] ?? ''
        ];

        $this->hostelroom_model->add($data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Hostel room updated successfully'
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

        $this->hostelroom_model->remove($input['id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Hostel room deleted successfully'
            ]));
    }


    public function studenthosteldetails()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output->set_status_header(405);
        }

        $response = [
            'classlist'   => $this->class_model->get(),
            'hostellist'  => $this->hostel_model->get(),
            'sch_setting' => $this->sch_setting_detail
        ];

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $response
            ]));
    }


    public function searchvalidation()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $this->input->post();

        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('class_id', 'Class', 'required');
        $this->form_validation->set_rules('section_id', 'Section', 'required');

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'params' => $input
            ]));
    }


    public function dthostellist()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $this->input->post();

        $resultlist = json_decode(
            $this->hostelroom_model->searchHostelDetails(
                $input['section_id'],
                $input['class_id'],
                $input['hostel_name']
            )
        );

        $data = [];

        foreach ($resultlist->data ?? [] as $student) {
            $data[] = [
                $student->class . ' - ' . $student->section,
                $student->admission_no,
                $student->firstname,
                $student->mobileno,
                $student->guardian_phone,
                $student->hostel_name,
                $student->room_no,
                $student->room_type,
                amountFormat($student->cost_per_bed)
            ];
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'draw'            => intval($resultlist->draw ?? 1),
                'recordsTotal'    => intval($resultlist->recordsTotal ?? 0),
                'recordsFiltered' => intval($resultlist->recordsFiltered ?? 0),
                'data'            => $data
            ]));
    }
}

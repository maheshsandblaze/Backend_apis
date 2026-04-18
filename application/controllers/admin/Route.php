<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Route extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("classteacher_model");
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    protected function _get_input()
    {
        $input = $this->input->post();
        if (empty($input)) {
            $input = json_decode($this->input->raw_input_stream, true);
        }
        return $input ?: [];
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $listroute = $this->route_model->listroute();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'    => 'success',
                'listroute' => $listroute
            ]));
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

        // ✅ Duplicate restriction added
        $this->form_validation->set_rules(
            'route_title',
            $this->lang->line('route_title'),
            'trim|required|xss_clean'
        );

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error'  => $this->form_validation->error_array()
                ]));
        } else {

            $exists = $this->route_model->check_duplicate($input['route_title']);

            if ($exists) {
                return $this->output
                    ->set_status_header(409)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'message' => 'Route title already exists'
                    ]));
            }

            $data = array(
                'route_title'   => $input['route_title'],
                'no_of_vehicle' => $input['no_of_vehicle'] ?? '',
            );

            $this->route_model->add($data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message')
                ]));
        }
    }

    public function get_editdetails($id = null)
    {
        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow Only GET Method
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Validate ID
        if (empty($id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Route ID is required'
                ]));
        }

        // Fetch Route Data
        $editroute = $this->route_model->get($id);

        // If Route Not Found
        if (empty($editroute)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Route not found'
                ]));
        }

        // Success Response
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'id'        => $id,
                    'editroute' => $editroute
                ]
            ]));
    }

    public function edit($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $id = $id ?: ($input['id'] ?? null);

        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Route ID is required']));
        }

        $this->form_validation->set_rules('route_title', $this->lang->line('route_title'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $editroute = $this->route_model->get($id);
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status'    => 'fail',
                    'error'     => $this->form_validation->error_array(),
                    'editroute' => $editroute
                ]));
        } else {

            // $exists = $this->route_model->check_duplicate($input['route_title']);

            // if ($exists) {
            //     return $this->output
            //         ->set_status_header(409)
            //         ->set_output(json_encode([
            //             'status' => 'fail',
            //             'message' => 'Route title already exists'
            //         ]));
            // }
            $data = array(
                'id'            => $id,
                'route_title'   => $input['route_title'],
                'no_of_vehicle' => $input['no_of_vehicle'] ?? '',
            );
            $this->route_model->add($data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('update_message')
                ]));
        }
    }

    public function delete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!$id) {
            $input = $this->_get_input();
            $id = $input['id'] ?? null;
        }

        if ($id) {
            $this->route_model->remove($id);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 'success', 'message' => 'Route deleted successfully']));
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => 'fail', 'message' => 'Route ID is required']));
    }

    public function studenttransportdetails()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $classlist = $this->class_model->get();
        $vehroutelist = $this->route_model->get();

        $section_id         = $input['section_id'] ?? null;
        $class_id           = $input['class_id'] ?? null;
        $transport_route_id = $input['transport_route_id'] ?? null;
        $pickup_point_id    = $input['pickup_point_id'] ?? null;
        $vehicle_id         = $input['vehicle_id'] ?? null;
        $resultlist         = [];

        if (isset($input['search'])) {
            $resultlist = $this->route_model->searchTransportDetails($section_id, $class_id, $transport_route_id, $pickup_point_id, $vehicle_id);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'       => 'success',
                'classlist'    => $classlist,
                'vehroutelist' => $vehroutelist,
                'resultlist'   => $resultlist,
                'sch_setting'  => $this->sch_setting_detail
            ]));
    }

    public function pickup_point()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $listpickup_point = $this->route_model->listpickup_point();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'           => 'success',
                'listpickup_point' => $listpickup_point
            ]));
    }

    public function transport_analysis()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Add logic here if needed, currently empty in original
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => 'Transport analysis data'
            ]));
    }
}

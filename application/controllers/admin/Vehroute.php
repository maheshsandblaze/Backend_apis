<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vehroute extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('vehroute_model');
        $this->load->model('vehicle_model');
        $this->load->model('route_model');
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

        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('route_id', $this->lang->line('route'), array('required', array('route_exists', array($this->vehroute_model, 'route_exists'))));
            $this->form_validation->set_rules('vehicle[]', $this->lang->line('vehicle'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $vehicle             = $input['vehicle'];
                $route_id            = $input['route_id'];
                $vehicle_batch_array = array();
                foreach ($vehicle as $vec_key => $vec_value) {
                    $vehicle_array = array(
                        'route_id'   => $route_id,
                        'vehicle_id' => $vec_value,
                    );
                    $vehicle_batch_array[] = $vehicle_array;
                }

                $result = $this->vehroute_model->add($vehicle_batch_array);
                
                if ($result) {
                    return $this->output
                        ->set_status_header(200)
                        ->set_output(json_encode([
                            'status'  => 'success',
                            'message' => $this->lang->line('success_message')
                        ]));
                } else {
                    return $this->output
                        ->set_status_header(500)
                        ->set_output(json_encode([
                            'status'  => 'fail',
                            'message' => 'Failed to assign vehicles to route'
                        ]));
                }
            }
        }

        // Default GET behavior
        $vehicle_result         = $this->vehicle_model->get();
        $routeList              = $this->route_model->get();
        $vehroute_result        = $this->vehroute_model->getRouteVehiclesList();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'       => 'success',
                'vehiclelist'  => $vehicle_result,
                'routelist'    => $routeList,
                'vehroutelist' => $vehroute_result
            ]));
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
            $result = $this->vehroute_model->removeByroute($id);
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
                    ->set_output(json_encode([
                        'status'  => 'fail',
                        'message' => 'Failed to delete vehicle route'
                    ]));
            }
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode([
                'status'  => 'fail',
                'message' => 'Route ID is required'
            ]));
    }
    
    
    public function get_editvehroute($id = null)
    {
        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow Only GET Request
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
    
        // Fetch Route Vehicle List
        $vehroute = $this->vehroute_model->getRouteVehiclesList($id);
    
        // If No Data Found
        if (empty($vehroute)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'No vehicles found for this route ID'
                ]));
        }
    
        // Success Response
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'route_id'  => $id,
                    'vehroute'  => $vehroute
                ]
            ]));
    }


    public function edit($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $id = $id ?: ($input['id'] ?? null);

        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'ID is required'
                ]));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('route_id', $this->lang->line('route'), array('required'));
            $this->form_validation->set_rules('vehicle[]', $this->lang->line('vehicle'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $vehicle        = $input['vehicle'];
                $prev_vec_route = $input['prev_vec_route'] ?? [];
                $pre_route_id   = $input['pre_route_id'] ?? $id;
                $route_id       = $input['route_id'];

                $add_result    = array_diff($vehicle, $prev_vec_route);
                $delete_result = array_diff($prev_vec_route, $vehicle);

                if ($pre_route_id != $route_id) {
                    $this->vehroute_model->removeByroute($pre_route_id);
                    $vehicle_batch_array = array();
                    foreach ($vehicle as $vec_key => $vec_value) {
                        $vehicle_array = array(
                            'route_id'   => $route_id,
                            'vehicle_id' => $vec_value,
                        );
                        $vehicle_batch_array[] = $vehicle_array;
                    }
                    $this->vehroute_model->add($vehicle_batch_array);
                } else {
                    if (!empty($add_result)) {
                        $vehicle_batch_array = array();
                        foreach ($add_result as $vec_add_key => $vec_add_value) {
                            $vehicle_array = array(
                                'route_id'   => $pre_route_id,
                                'vehicle_id' => $vec_add_value,
                            );
                            $vehicle_batch_array[] = $vehicle_array;
                        }
                        $this->vehroute_model->add($vehicle_batch_array);
                    }

                    if (!empty($delete_result)) {
                        $vehicle_delete_array = array();
                        foreach ($delete_result as $vec_delete_key => $vec_delete_value) {
                            $vehicle_delete_array[] = $vec_delete_value;
                        }
                        $this->vehroute_model->remove($pre_route_id, $vehicle_delete_array);
                    }
                }

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('update_message')
                    ]));
            }
        }

        // GET behavior: return edit data
        $vehroute = $this->vehroute_model->getRouteVehiclesList($id);
        $vehicle_result = $this->vehicle_model->get();
        $routeList = $this->route_model->get();
        $vehroute_result = $this->vehroute_model->getRouteVehiclesList();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'       => 'success',
                'vehroute'     => $vehroute,
                'vehiclelist'  => $vehicle_result,
                'routelist'    => $routeList,
                'vehroutelist' => $vehroute_result
            ]));
    }
}

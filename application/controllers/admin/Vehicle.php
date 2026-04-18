<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vehicle extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
    }

    // public function index()
    // {
    //     if (!$this->rbac->hasPrivilege('vehicle', 'can_view')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'Transport');
    //     $this->session->set_userdata('sub_menu', 'vehicle/index');
    //     $data['title']       = 'Add Vehicle';
    //     $listVehicle         = $this->vehicle_model->get();
    //     $data['listVehicle'] = $listVehicle;
    //     $this->load->view('layout/header');
    //     $this->load->view('admin/vehicle/index', $data);
    //     $this->load->view('layout/footer');
    // }


    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Method Not Allowed"
                ]));
        }

        $listVehicle = $this->vehicle_model->get();

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                "status" => true,
                "data" => $listVehicle
            ]));
    }



    // public function add()
    // {
    //     if (!$this->rbac->hasPrivilege('vehicle', 'can_add')) {
    //         access_denied();
    //     }
    //     $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('vehicle_photo', $this->lang->line('vehicle_photo'), 'callback_handle_upload');

    //     if ($this->form_validation->run() == false) {
    //         $msg = array(
    //             'vehicle_no' => form_error('vehicle_no'),
    //             'vehicle_photo' => form_error('vehicle_photo'),
    //         );

    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {            

    //         $vehicle_photo = $this->media_storage->fileupload("vehicle_photo", "./uploads/vehicle_photo/");

    //         $data = array(
    //             'vehicle_no'           => $this->input->post('vehicle_no'),
    //             'vehicle_model'        => $this->input->post('vehicle_model'),
    //             'driver_name'          => $this->input->post('driver_name'),
    //             'driver_licence'       => $this->input->post('driver_licence'),
    //             'driver_contact'       => $this->input->post('driver_contact'),
    //             'note'                 => $this->input->post('note'),
    //             'registration_number'  => $this->input->post('registration_number'),
    //             'chasis_number'        => $this->input->post('chasis_number'),
    //             'max_seating_capacity' => $this->input->post('max_seating_capacity'),
    //             'manufacture_year'      => $this->input->post('manufacture_year'),
    //             'vehicle_photo'        => $vehicle_photo,
    //         );

    //         $this->vehicle_model->add($data);

    //         $msg   = $this->lang->line('success_message');
    //         $array = array('status' => 'success', 'error' => '', 'message' => $msg);
    //     }
    //     echo json_encode($array);
    // }


    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Method Not Allowed"
                ]));
        }

        $this->form_validation->set_rules(
            'vehicle_no',
            'Vehicle Number',
            'trim|required'
        );

        if ($this->form_validation->run() == false) {

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    "status" => false,
                    "errors" => $this->form_validation->error_array()
                ]));
        }



        $exists = $this->vehicle_model->check_vehicle_no($this->input->post('vehicle_no'));

        if ($exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Vehicle number already exists"
                ]));
        }

        // Upload Photo if available
        $vehicle_photo = "";
        if (!empty($_FILES['vehicle_photo']['name'])) {
            $vehicle_photo = $this->media_storage->fileupload(
                "vehicle_photo",
                "../uploads/vehicle_photo/"
            );
        }

        $data = [
            "vehicle_no"           => $this->input->post("vehicle_no"),
            "vehicle_model"        => $this->input->post("vehicle_model"),
            "driver_name"          => $this->input->post("driver_name"),
            "driver_licence"       => $this->input->post("driver_licence"),
            "driver_contact"       => $this->input->post("driver_contact"),
            "note"                 => $this->input->post("note"),
            "registration_number"  => $this->input->post("registration_number"),
            "chasis_number"        => $this->input->post("chasis_number"),
            "max_seating_capacity" => $this->input->post("max_seating_capacity"),
            "manufacture_year"     => $this->input->post("manufacture_year"),
            "vehicle_photo"        => $vehicle_photo
        ];

        $insert_id = $this->vehicle_model->add($data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => true,
                "message" => "Vehicle Added Successfully",
                "vehicle_id" => $insert_id
            ]));
    }



    // public function getsinglevehicledata()
    // {
    //     $vehicleid           = $this->input->post('vehicleid');
    //     $data['editvehicle'] = $this->vehicle_model->get($vehicleid);
    //     $page                = $this->load->view('admin/vehicle/edit', $data, true);
    //     echo json_encode(array('page' => $page));
    // }


    public function getsinglevehicledata($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Method Not Allowed"
                ]));
        }

        $vehicle = $this->vehicle_model->get($id);

        if (!$vehicle) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Vehicle Not Found"
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => true,
                "data" => $vehicle
            ]));
    }



    // public function edit()
    // {
    //     if (!$this->rbac->hasPrivilege('vehicle', 'can_edit')) {
    //         access_denied();
    //     }

    //     $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('vehicle_photo', $this->lang->line('vehicle_photo'), 'callback_handle_upload');
    //     $id =   $this->input->post('id');

    //     $vehicle              = $this->vehicle_model->get($id);       

    //     if ($this->form_validation->run() == false) {
    //         $msg = array(
    //             'vehicle_no' => form_error('vehicle_no'),
    //             'vehicle_photo' => form_error('vehicle_photo'),
    //         );

    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {           

    //         $data = array(
    //             'id'                   => $this->input->post('id'),
    //             'vehicle_no'           => $this->input->post('vehicle_no'),
    //             'vehicle_model'        => $this->input->post('vehicle_model'),
    //             'driver_name'          => $this->input->post('driver_name'),
    //             'driver_licence'       => $this->input->post('driver_licence'),
    //             'driver_contact'       => $this->input->post('driver_contact'),
    //             'note'                 => $this->input->post('note'),
    //             'registration_number'  => $this->input->post('registration_number'),
    //             'chasis_number'        => $this->input->post('chasis_number'),
    //             'max_seating_capacity' => $this->input->post('max_seating_capacity'),
    //             'manufacture_year' => $this->input->post('manufacture_year'),        

    //         );            

    //         if (isset($_FILES["vehicle_photo"]) && $_FILES['vehicle_photo']['name'] != '' && (!empty($_FILES['vehicle_photo']['name']))) {

    //             $img_name = $this->media_storage->fileupload("vehicle_photo", "./uploads/vehicle_photo/");
    //         } else {
    //             $img_name = $vehicle->vehicle_photo;
    //         }

    //         $data['vehicle_photo'] = $img_name;

    //         if (isset($_FILES["vehicle_photo"]) && $_FILES['vehicle_photo']['name'] != '' && (!empty($_FILES['vehicle_photo']['name']))) {
    //             if ($vehicle->vehicle_photo != '') {
    //                 $this->media_storage->filedelete($vehicle->vehicle_photo, "uploads/school_income");
    //             }
    //         }

    //         $this->vehicle_model->add($data);

    //         $msg   = $this->lang->line('success_message');
    //         $array = array('status' => 'success', 'error' => '', 'message' => $msg);
    //     }
    //     echo json_encode($array);
    // }


    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Method Not Allowed"
                ]));
        }

        $vehicle = $this->vehicle_model->get($id);

        if (!$vehicle) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Vehicle Not Found"
                ]));
        }

        $img_name = $vehicle->vehicle_photo;
        // echo "<pre>";
        // print_r($_FILES);exit;     
        
        // $exists = $this->vehicle_model->check_vehicle_no($this->input->post('vehicle_no'));

        // if ($exists) {
        //     return $this->output
        //         ->set_status_header(409)
        //         ->set_output(json_encode([
        //             "status" => false,
        //             "message" => "Vehicle number already exists"
        //         ]));
        // }




        // Upload new photo if provided
        if (!empty($_FILES['vehicle_photo']['name'])) {
            $img_name = $this->media_storage->fileupload(
                "vehicle_photo",
                "../uploads/vehicle_photo/"
            );
        }

        // echo    "<pre>";
        // print_r($img_name);exit;

        $data = [
            "id"                   => $id,
            "vehicle_no"           => $this->input->post("vehicle_no"),
            "vehicle_model"        => $this->input->post("vehicle_model"),
            "driver_name"          => $this->input->post("driver_name"),
            "driver_licence"       => $this->input->post("driver_licence"),
            "driver_contact"       => $this->input->post("driver_contact"),
            "note"                 => $this->input->post("note"),
            "registration_number"  => $this->input->post("registration_number"),
            "chasis_number"        => $this->input->post("chasis_number"),
            "max_seating_capacity" => $this->input->post("max_seating_capacity"),
            "manufacture_year"     => $this->input->post("manufacture_year"),
            "vehicle_photo"        => $img_name
        ];

        $this->vehicle_model->add($data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => true,
                "message" => "Vehicle Updated Successfully"
            ]));
    }



    // public function delete($id)
    // {
    //     if (!$this->rbac->hasPrivilege('vehicle', 'can_delete')) {
    //         access_denied();
    //     }        
    //     $this->vehicle_model->remove($id);
    //     redirect('admin/vehicle/index');
    // }


    public function delete()
    {
        // Handle OPTIONS Request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    "status"  => false,
                    "message" => "Method Not Allowed"
                ]));
        }

        // Read JSON Input
        $input = json_decode(file_get_contents("php://input"), true);

        // Validate Input
        if (empty($input) || !isset($input['id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    "status"  => false,
                    "message" => "Vehicle ID is required"
                ]));
        }

        $id = $input['id'];

        // Check if Vehicle Exists
        $vehicle = $this->vehicle_model->get($id);

        if (empty($vehicle)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    "status"  => false,
                    "message" => "Vehicle not found"
                ]));
        }

        // Delete Vehicle
        $this->vehicle_model->remove($id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status"  => true,
                "message" => "Vehicle Deleted Successfully"
            ]));
    }




    public function vehicledetails()
    {
        $vehicleid           = $this->input->post('vehicleid');
        $data['editvehicle'] = $this->vehicle_model->get($vehicleid);
        $page                = $this->load->view('admin/vehicle/_vehicledetails', $data, true);
        echo json_encode(array('page' => $page));
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["vehicle_photo"]) && !empty($_FILES['vehicle_photo']['name'])) {

            $file_type = $_FILES["vehicle_photo"]['type'];
            $file_size = $_FILES["vehicle_photo"]["size"];
            $file_name = $_FILES["vehicle_photo"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES['vehicle_photo']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }

                if ($file_size > $result->image_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->image_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_extension_error_uploading_image'));
                return false;
            }

            return true;
        }
        return true;
    }
}

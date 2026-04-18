<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Route extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pickuppoint_model');
    }

    // public function index()
    // {
    //     $this->session->set_userdata('top_menu', 'Transport');
    //     $this->session->set_userdata('sub_menu', 'route/index');
    //     $student_id                  = $this->customlib->getStudentSessionUserID();
    //     $studentList                 = $this->student_model->get($student_id);
    //     $studentList['pickup_point'] = $this->pickuppoint_model->getPickupPointByRouteID($studentList['route_id']);
    //     $data['listroute']           = $studentList;
    //     $this->load->view('layout/student/header');
    //     $this->load->view('user/route/index', $data);
    //     $this->load->view('layout/student/footer');
    // }
    
    
    public function index()
    {
        /* =========================
           CORS
        ========================== */
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        /* =========================
           ALLOW ONLY GET
        ========================== */
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        /* =========================
           TOKEN AUTH
        ========================== */
        $auth = $this->auth->validate_user();
    
        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ]));
        }
    
        /* =========================
           FETCH STUDENT
        ========================== */
        $student_id = $auth->login_id;
    
        $student = $this->student_model->get($student_id);
    
        if (!$student) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student not found'
                ]));
        }
    
        /* =========================
           FETCH ROUTE PICKUP POINTS
        ========================== */
        $pickup_points = [];
    
        if (!empty($student['route_id'])) {
            $pickup_points = $this->pickuppoint_model
                                  ->getPickupPointByRouteID($student['route_id']);
        }
    
        /* =========================
           FINAL RESPONSE
        ========================== */
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'student_id'   => $student['id'],
                    'route_id'     => $student['route_id'],
                    'route_title'  => $student['route_title'] ?? null,
                    'pickup_points'=> $pickup_points
                ]
            ]));
    }

    public function getbusdetail()
    {
        $vehrouteid = $this->input->post('vehrouteid');
        $result     = $this->vehroute_model->getVechileDetailByVecRouteID($vehrouteid);
        echo json_encode($result);
    }
}

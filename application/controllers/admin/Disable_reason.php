<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Disable_reason extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    // public function index()
    // {
    //     if (!$this->rbac->hasPrivilege('disable_reason', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Student Information');
    //     $this->session->set_userdata('sub_menu', 'student/disable_reason');
    //     $data['results'] = $this->disable_reason_model->get();
    //     $this->form_validation->set_rules('name', $this->lang->line('disable_reason'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == true) {



    //         $data = array(
    //             'reason' => $this->input->post('name'),
    //         );

    //         if ($id == '') {
    //             $leave_id = $this->disable_reason_model->add($data);
    //         } else {
    //             $data['id'] = $this->input->post('reason_id');

    //             $this->disable_reason_model->add($data);
    //         }
    //         $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
    //         redirect('admin/disable_reason');
    //     }

    //         $this->load->view('layout/header');
    //         $this->load->view('admin/disable_reason/disable_reason', $data);
    //         $this->load->view('layout/footer');
    // }


    public function get_disable_reason_list()
    {
        // =========================
        // METHOD CHECK
        // =========================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // if (!$this->rbac->hasPrivilege('disable_reason', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }

        $results = $this->disable_reason_model->get();

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $results
            ]));
    }



    // public function disableReasonApi()
    // {
    //     // =========================
    //     // METHOD CHECK
    //     // =========================
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }

    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         return $this->output
    //             ->set_status_header(405)
    //             ->set_output(json_encode([
    //                 'status'  => false,
    //                 'message' => 'Method Not Allowed'
    //             ]));
    //     }



    //     // =========================
    //     // INPUT (JSON / FORM)
    //     // =========================
    //     $input = json_decode(file_get_contents('php://input'), true);
    //     if (empty($input)) {
    //         $input = $this->input->post();
    //     }

    //     $reason     = trim($input['name'] ?? '');
    //     $reason_id  = $input['reason_id'] ?? null;

    //     // =========================
    //     // VALIDATION
    //     // =========================
    //     if ($reason === '') {
    //         return $this->output
    //             ->set_status_header(422)
    //             ->set_output(json_encode([
    //                 'status'  => false,
    //                 'message' => 'Disable reason is required'
    //             ]));
    //     }

    //     // =========================
    //     // SAVE / UPDATE
    //     // =========================
    //     $data = [
    //         'reason' => $reason
    //     ];

    //     if (!empty($reason_id)) {
    //         $data['id'] = $reason_id;
    //     }

    //     $this->disable_reason_model->add($data);

    //     // =========================
    //     // FETCH UPDATED LIST
    //     // =========================
    //     $results = $this->disable_reason_model->get();

    //     return $this->output
    //         ->set_status_header(200)
    //         ->set_output(json_encode([
    //             'status'  => true,
    //             'message' => $this->lang->line('success_message'),
    //             'data'    => $results
    //         ]));
    // }


    public function add()
    {
        // =========================
        //     // METHOD CHECK
        //     // =========================
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

        // if (!$this->rbac->hasPrivilege('disable_reason', 'can_add')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }

        /* READ JSON INPUT */
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        $_POST = $input;

        $this->form_validation->set_rules(
            'name',
            $this->lang->line('disable_reason'),
            'trim|required|xss_clean'
        );

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'name' => form_error('name')
                    ]
                ]));
        }


        $data = [
            'reason' => $this->input->post('name')
        ];

        // Prevent duplicate
        if ($this->disable_reason_model->existsBydisable_reason($this->input->post('name'))) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Disable reason already exists'
                ]));
        }

        $id = $this->disable_reason_model->add($data);

        return $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Disable reason added successfully',
                'id' => $id
            ]));
    }



    // public function edit($id)
    // {
    //     if (!$this->rbac->hasPrivilege('disable_reason', 'can_edit')) {
    //         access_denied();
    //     }
    //     $data['id'] = $id;

    //     $this->session->set_userdata('top_menu', 'Student Information');
    //     $this->session->set_userdata('sub_menu', 'student/disable_reason');
    //     $data['data']    = $this->disable_reason_model->get($id);
    //     $data['results'] = $this->disable_reason_model->get();
    //     $data['name']    = $data['data']['reason'];
    //     $this->form_validation->set_rules('name', $this->lang->line('disable_reason'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {

    //         $this->load->view('layout/header');
    //         $this->load->view('admin/disable_reason/disable_reasonedit', $data);
    //         $this->load->view('layout/footer');
    //     } else {

    //         $data = array(
    //             'reason' => $this->input->post('name'),
    //         );

    //         $data['id'] = $id;

    //         $this->disable_reason_model->add($data);

    //         $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
    //         redirect('admin/disable_reason');
    //     }
    // }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        /* =======================
           PERMISSION CHECK
        ======================== */
        // if (!$this->rbac->hasPrivilege('disable_reason', 'can_edit')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access denied'
        //         ]));
        // }

        /* =======================
           READ JSON INPUT
        ======================== */
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        $_POST = $input;

        /* =======================
           VALIDATION
        ======================== */
        $this->form_validation->set_rules(
            'name',
            $this->lang->line('disable_reason'),
            'trim|required|xss_clean'
        );

        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        /* =======================
           UPDATE DATA
        ======================== */
        $data = [
            'id'     => $id,
            'reason' => $this->input->post('name')
        ];

        $this->disable_reason_model->add($data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'message' => $this->lang->line('update_message')
            ]));
    }


    // public function get_details($id)
    // {
    //     $data = $this->disable_reason_model->get($id);
    //     echo json_encode($data);
    // }

    public function get_details($id)
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

        $data = $this->disable_reason_model->get($id);

        if (empty($data)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Record not found'
                ]));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data' => $data
            ]));
    }


    // public function delete($id)
    // {
    //     if (!$this->rbac->hasPrivilege('disable_reason', 'can_delete')) {
    //         access_denied();
    //     }
    //     $this->disable_reason_model->remove($id);

    //     $this->session->set_flashdata('message', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
    //     redirect('admin/disable_reason');
    // }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        /* =======================
           PERMISSION CHECK
        ======================== */
        // if (!$this->rbac->hasPrivilege('disable_reason', 'can_delete')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access denied'
        //         ]));
        // }

        $this->disable_reason_model->remove($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'message' => $this->lang->line('delete_message')
            ]));
    }
}

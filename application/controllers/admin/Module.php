<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Module extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("module_model");
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

        $permissionList         = $this->module_model->getPermission();
        $studentpermissionList  = $this->module_model->getStudentPermission();
        $parentpermissionList   = $this->module_model->getParentPermission();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'                => 'success',
                'permissionList'        => $permissionList,
                'studentpermissionList' => $studentpermissionList,
                'parentpermissionList'  => $parentpermissionList
            ]));
    }

    public function changeStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input  = $this->_get_input();
        $id     = $input["id"] ?? null;
        $status = $input["status"] ?? null;

        if (!empty($id)) {
            $data   = array('id' => $id, 'is_active' => $status);
            $this->module_model->changeStatus($data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 1,
                    'msg'    => $this->lang->line("status_change_successfully")
                ]));
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode([
                'status' => 0,
                'msg'    => 'ID is required'
            ]));
    }

    public function changeParentStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input  = $this->_get_input();
        $id     = $input["id"] ?? null;
        $status = $input["status"] ?? null;
        $role   = $input['role'] ?? null;


        if (!empty($id) && !empty($role)) {
            $data   = array('id' => $id, $role => $status);
            $this->module_model->changeParentStatus($data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 1,
                    'msg'    => $this->lang->line("status_change_successfully")
                ]));
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode([
                'status' => 0,
                'msg'    => 'ID and Role are required'
            ]));
    }

    public function changeStudentStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input  = $this->_get_input();
        $id     = $input["id"] ?? null;
        $status = $input["status"] ?? null;
        $role   = $input['role'] ?? null;

        if (!empty($id) && !empty($role)) {
            $data   = array('id' => $id, $role => $status);
            $this->module_model->changeStudentStatus($data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 1,
                    'msg'    => $this->lang->line("status_change_successfully")
                ]));
        }

        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode([
                'status' => 0,
                'msg'    => 'ID and Role are required'
            ]));
    }
}

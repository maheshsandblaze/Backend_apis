<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Userlog extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('userlog_model');
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

        $userlogList = $this->userlog_model->get();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'             => 'success',
                'userlogList'        => $userlogList,
                'userlogStaffList'   => $this->userlog_model->getByRoleStaff(),
                'userlogStudentList' => $this->userlog_model->getByRole('student'),
                'userlogParentList'  => $this->userlog_model->getByRole('parent'),
            ]));
    }

    public function getDatatable()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $userlog = $this->userlog_model->getlogAllRecord();
        $userlog = json_decode($userlog);
        $dt_data = array();

        if (!empty($userlog->data)) {
            foreach ($userlog->data as $key => $value) {
                if ($value->is_invisible_user == 1) {
                    continue;
                }
                $row   = array();
                $row[] = $value->user;
                $row[] = ucfirst($value->role);
                $row[] = ($value->class_name != "") ? $value->class_name . "(" . $value->section_name . ")" : "";
                $row[] = $value->ipaddress;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->login_datetime);
                $row[] = $value->user_agent;

                $dt_data[] = $row;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw"            => intval($userlog->draw),
                "recordsTotal"    => intval($userlog->recordsTotal),
                "recordsFiltered" => intval($userlog->recordsFiltered),
                "data"            => $dt_data,
            ]));
    }

    public function getStudentDatatable()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $userlog = $this->userlog_model->getAllRecordByRole('student');
        $userlog = json_decode($userlog);
        $dt_data = array();

        if (!empty($userlog->data)) {
            foreach ($userlog->data as $key => $value) {
                $row   = array();
                $row[] = $value->user;
                $row[] = ucfirst($value->role);
                $row[] = ($value->class_name != "") ? $value->class_name . "(" . $value->section_name . ")" : "";
                $row[] = $value->ipaddress;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->login_datetime);
                $row[] = $value->user_agent;

                $dt_data[] = $row;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw"            => intval($userlog->draw),
                "recordsTotal"    => intval($userlog->recordsTotal),
                "recordsFiltered" => intval($userlog->recordsFiltered),
                "data"            => $dt_data,
            ]));
    }

    public function getParentDatatable()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $userlog = $this->userlog_model->getAllRecordByRole('parent');
        $userlog = json_decode($userlog);
        $dt_data = array();

        if (!empty($userlog->data)) {
            foreach ($userlog->data as $key => $value) {
                $row   = array();
                $row[] = $value->user;
                $row[] = ucfirst($value->role);
                $row[] = $value->ipaddress;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->login_datetime);
                $row[] = $value->user_agent;

                $dt_data[] = $row;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw"            => intval($userlog->draw),
                "recordsTotal"    => intval($userlog->recordsTotal),
                "recordsFiltered" => intval($userlog->recordsFiltered),
                "data"            => $dt_data,
            ]));
    }

    public function getStaffDatatable()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $userlog = $this->userlog_model->getAllRecordByStaff();
        $userlog = json_decode($userlog);
        $dt_data = array();

        if (!empty($userlog->data)) {
            foreach ($userlog->data as $key => $value) {
                if ($value->is_invisible_user == 1) {
                    continue;
                }
                $row   = array();
                $row[] = $value->user;
                $row[] = ucfirst($value->role);
                $row[] = $value->ipaddress;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->login_datetime);
                $row[] = $value->user_agent;

                $dt_data[] = $row;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw"            => intval($userlog->draw),
                "recordsTotal"    => intval($userlog->recordsTotal),
                "recordsFiltered" => intval($userlog->recordsFiltered),
                "data"            => $dt_data,
            ]));
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $status = $this->userlog_model->userlog_delete();

        if ($status) {
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
                    'message' => 'Failed to delete logs'
                ]));
        }
    }
}

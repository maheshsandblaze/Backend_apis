<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Generatestaffidcard extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    // public function index()
    // {
    //     if (!$this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Certificate');
    //     $this->session->set_userdata('sub_menu', 'admin/generatestaffidcard');
    //     $idcardlist            = $this->Generatestaffidcard_model->getstaffidcard();
    //     $data['idcardlist']    = $idcardlist;
    //     $staffRole             = $this->staff_model->getStaffRole();
    //     $data['staffRolelist'] = $staffRole;
    //     $this->load->view('layout/header');
    //     $this->load->view('admin/generatestaffidcard/generatestaffidcardview', $data);
    //     $this->load->view('layout/footer');
    // }


    public function index()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Permission check
        // if (!$this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status'  => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }

        // Fetch data
        $idcardlist  = $this->Generatestaffidcard_model->getstaffidcard();
        $staffRoles  = $this->staff_model->getStaffRole();

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'idcardlist'     => $idcardlist,
                    'staffRolelist'  => $staffRoles
                ]
            ]));
    }


    // public function search()
    // {
    //     $this->session->set_userdata('top_menu', 'Certificate');
    //     $this->session->set_userdata('sub_menu', 'admin/generatestaffidcard');
    //     $staffRole               = $this->staff_model->getStaffRole();
    //     $data['staffRolelist']   = $staffRole;
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
    //     $idcardlist              = $this->Generatestaffidcard_model->getstaffidcard();
    //     $data['idcardlist']      = $idcardlist;
    //     $this->form_validation->set_rules('id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
    //     if ($this->form_validation->run() == true) {
    //         $role                 = $this->input->post('role_id');
    //         $data['role_id']      = $this->input->post('role_id');
    //         $id_card              = $this->input->post('id_card');
    //         $idcardResult         = $this->Generatestaffidcard_model->getidcardbyid($id_card);
    //         $data['idcardResult'] = $idcardResult;
    //         $resultlist           = $this->staff_model->getEmployee($role, 1);
    //         $data['resultlist']   = $resultlist;
    //     }

    //     $this->load->view('layout/header');
    //     $this->load->view('admin/generatestaffidcard/generatestaffidcardview', $data);
    //     $this->load->view('layout/footer');
    // }


    public function search()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read RAW JSON
        $json = json_decode(file_get_contents('php://input'), true);

        if (empty($json['id_card'])) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'id_card is required'
                ]));
        }

        $roleId  = $json['role_id'] ?? null;
        $idCard  = $json['id_card'];

        // Fetch common data
        $staffRoles  = $this->staff_model->getStaffRole();
        $idcardlist  = $this->Generatestaffidcard_model->getstaffidcard();
        $admAuto     = $this->sch_setting_detail->adm_auto_insert;

        // Fetch selected ID card template
        $idcardResult = $this->Generatestaffidcard_model->getidcardbyid($idCard);

        // Fetch staff list (only active staff = 1)
        $resultlist = [];
        if (!empty($roleId)) {
            $resultlist = $this->staff_model->getEmployee($roleId, 1);
        }

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'staffRolelist'   => $staffRoles,
                    'idcardlist'      => $idcardlist,
                    'adm_auto_insert' => $admAuto,
                    'role_id'         => $roleId,
                    'idcardResult'    => $idcardResult,
                    'resultlist'      => $resultlist
                ]
            ]));
    }


    // public function generatemultiple()
    // {
    //     $staffid             = $this->input->post('data');
    //     $staff_array         = json_decode($staffid);
    //     $idcard              = $this->input->post('id_card');
    //     $staffid_arr         = array();
    //     $data['sch_setting'] = $this->setting_model->get();

    //     $data['id_card'] = $this->Generatestaffidcard_model->getidcardbyid($idcard);
    //     foreach ($staff_array as $key => $value) {
    //         $staffid_arr[] = $value->staff_id;
    //     }

    //     $staffs = $this->Generatestaffidcard_model->getEmployee($staffid_arr, 1);

    //     foreach ($staffs as $key => $staffs_value) {
    //         $staffs[$key]->barcode = $this->customlib->generatestaffbarcode($staffs_value->employee_id);
    //     }

    //     $data['staffs'] = $staffs;

    //     $id_cards = $this->load->view('admin/generatestaffidcard/generatemultiplestaffidcard', $data, true);
    //     echo $id_cards;
    // }

    // public function generatemultiple() {
    //     $staffid = $this->input->post('data');
    //     $staff_array = json_decode($staffid);
    //     $idcard = $this->input->post('id_card');
    //     $staffid_arr = array();
    //     $data['sch_setting'] = $this->setting_model->get();
    //     $data['id_card'] = $this->Generatestaffidcard_model->getidcardbyid($idcard);
    //     foreach ($staff_array as $key => $value) {
    //         $staffid_arr[] = $value->staff_id;
    //     }
    //     $data['staffs'] = $this->Generatestaffidcard_model->getEmployee($staffid_arr,1);
    //     $id_cards = $this->load->view('admin/generatestaffidcard/generatemultiplestaffidcard', $data, true);
    //     echo $id_cards;  
    // }


    public function generatemultiple()
    {
        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read RAW JSON input
        $json = json_decode(file_get_contents('php://input'), true);

        if (empty($json['data']) || empty($json['id_card'])) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'data (staff list) and id_card are required'
                ]));
        }

        $staffArray = $json['data'];   // array of staff objects
        $idCardId   = $json['id_card'];

        // Extract staff IDs
        $staffIdArr = [];
        foreach ($staffArray as $item) {
            if (!empty($item['staff_id'])) {
                $staffIdArr[] = $item['staff_id'];
            }
        }

        if (empty($staffIdArr)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'No valid staff_id found'
                ]));
        }

        // Fetch required data
        $schoolSetting = $this->setting_model->get();
        $idCard        = $this->Generatestaffidcard_model->getidcardbyid($idCardId);
        $staffs        = $this->Generatestaffidcard_model->getEmployee($staffIdArr, 1);

        if (empty($idCard)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'ID Card template not found'
                ]));
        }

        // echo "<pre>";print_r($schoolSetting);exit;

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'sch_setting' => $schoolSetting,
                    'id_card'     => $idCard,
                    'staffs'      => $staffs
                ]
            ]));
    }
}

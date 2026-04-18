<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feemaster extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model('pickuppoint_model');
        $this->load->library('form_validation');
        $this->load->model('feegroup_model');
        $this->load->model('feetype_model');
        $this->load->model('feesessiongroup_model');
        $this->load->model('feegrouptype_model');
        $this->load->model('class_model');
        $this->load->model('category_model');
        $this->load->model('studentfeemaster_model');
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input;
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        // If it's a POST request with data, try to add
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('feetype_id', $this->lang->line('fee_type'), 'required');
            $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|numeric');
            $this->form_validation->set_rules(
                'fee_groups_id',
                $this->lang->line('fee_group'),
                array(
                    'required',
                    array('check_exists', array($this->feesessiongroup_model, 'valid_check_exists')),
                )
            );

            if (isset($input['account_type']) && $input['account_type'] == 'fix') {
                $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
                $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
            } elseif (isset($input['account_type']) && ($input['account_type'] == 'percentage')) {
                $this->form_validation->set_rules('fine_percentage', $this->lang->line('percentage'), 'required|numeric');
                $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
                $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
            }

            if ($this->form_validation->run() != false) {
                $fine_amount = (isset($input['fine_amount']) && $input['fine_amount'] != '') ? convertCurrencyFormatToBaseAmount($input['fine_amount']) : '';

                $insert_array = array(
                    'fee_groups_id'   => $input['fee_groups_id'],
                    'feetype_id'      => $input['feetype_id'],
                    'amount'          => convertCurrencyFormatToBaseAmount($input['amount']),
                    'due_date'        => $this->customlib->dateFormatToYYYYMMDD($input['due_date']),
                    'session_id'      => $this->setting_model->getCurrentSession(),
                    'fine_type'       => $input['account_type'] ?? '',
                    'fine_percentage' => $input['fine_percentage'] ?? 0,
                    'fine_amount'     => $fine_amount,
                );

                $this->feesessiongroup_model->add($insert_array);
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => true,
                        'message' => $this->lang->line('success_message')
                    ]));
            } else {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => $this->form_validation->error_array()
                    ]));
            }
        }

        // Default GET or POST without valid run
        $feegroup             = $this->feegroup_model->get();
        $feetype              = $this->feetype_model->get();
        $feegroup_result       = $this->feesessiongroup_model->getFeesByGroup(null, 0);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'feegroupList' => $feegroup,
                'feetypeList' => $feetype,
                'feemasterList' => $feegroup_result
            ]));
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->feegrouptype_model->remove($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('delete_message')
            ]));
    }

    public function deletegrp($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->feesessiongroup_model->remove($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('delete_message')
            ]));
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('feetype_id', $this->lang->line('fee_type'), 'required');
            $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|numeric');
            $this->form_validation->set_rules(
                'fee_groups_id',
                $this->lang->line('fee_group'),
                array(
                    'required',
                    array('check_exists', array($this->feesessiongroup_model, 'valid_check_exists')),
                )
            );

            if (isset($input['account_type']) && $input['account_type'] == 'fix') {
                $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
                $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
            } elseif (isset($input['account_type']) && ($input['account_type'] == 'percentage')) {
                $this->form_validation->set_rules('fine_percentage', $this->lang->line('percentage'), 'required|numeric');
                $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
                $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
            }

            if ($this->form_validation->run() != false) {
                $fine_amount = (isset($input['fine_amount']) && $input['fine_amount'] != '') ? convertCurrencyFormatToBaseAmount($input['fine_amount']) : '';

                $insert_array = array(
                    'id'              => $id,
                    'feetype_id'      => $input['feetype_id'],
                    'due_date'        => $input['due_date'],
                    'amount'          => convertCurrencyFormatToBaseAmount($input['amount']),
                    'fine_type'       => $input['account_type'] ?? '',
                    'fine_percentage' => $input['fine_percentage'] ?? 0,
                    'fine_amount'     => $fine_amount,
                );

                $this->feegrouptype_model->add($insert_array);
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => true,
                        'message' => $this->lang->line('update_message')
                    ]));
            } else {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => $this->form_validation->error_array()
                    ]));
            }
        }

        $feegroup_type         = $this->feegrouptype_model->get($id);
        $feegroup              = $this->feegroup_model->get();
        $feetype               = $this->feetype_model->get();
        $feegroup_result       = $this->feesessiongroup_model->getFeesByGroup(null, 0);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'feegroup_type' => $feegroup_type,
                'feegroupList' => $feegroup,
                'feetypeList' => $feetype,
                'feemasterList' => $feegroup_result
            ]));
    }

    public function assign($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $data = [];
        $data['id']              = $id;
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $feegroup_result         = $this->feesessiongroup_model->getFeesByGroup($id);
        $data['feegroupList']    = $feegroup_result;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;
        $genderList            = $this->customlib->getGender();
        $data['genderList']    = $genderList;
        $RTEstatusList         = $this->customlib->getRteStatus();
        $data['RTEstatusList'] = $RTEstatusList;

        $listpickup_point         = $this->pickuppoint_model->dropdownpickup_point();
        $data['listpickup_pointlist'] = $listpickup_point;

        $category             = $this->category_model->get();
        $data['categorylist'] = $category;

        $input = $this->_get_input();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $class_id    = $input['class_id'] ?? null;
            $section_id  = $input['section_id'] ?? null;
            $category_id = $input['category_id'] ?? null;
            $gender      = $input['gender'] ?? null;
            $rte_status  = $input['rte'] ?? null;
            $pickuppoint_id = $input['pickuppoint_id'] ?? null;

            $resultlist = $this->studentfeemaster_model->searchAssignFeeByClassSection($class_id, $section_id, $id, $category_id, $gender, $rte_status, $pickuppoint_id);
            $data['resultlist'] = $resultlist;
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }


    public function search_assign_students()
    {
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

        /* =======================
           READ JSON / FORM INPUT
        ======================== */
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $fee_group_id  = $input['fee_group_id'] ?? null;
        $class_id      = $input['class_id'] ?? null;
        $section_id    = $input['section_id'] ?? null;
        $category_id   = $input['category_id'] ?? null;
        $gender        = $input['gender'] ?? null;
        $rte_status    = $input['rte'] ?? null;
        $pickuppoint_id = $input['pickuppoint_id'] ?? null;

        if (empty($fee_group_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Fee group is required'
                ]));
        }

        $students = $this->studentfeemaster_model->searchAssignFeeByClassSection(
            $class_id,
            $section_id,
            $fee_group_id,
            $category_id,
            $gender,
            $rte_status,
            $pickuppoint_id
        );

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'count'  => count($students),
                'data'   => $students
            ]));
    }



    public function get_fee_master_fetch($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // if (!$this->rbac->hasPrivilege('fees_master', 'can_edit')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access denied'
        //         ]));
        // }

        $feegroup_type = $this->feegrouptype_model->get($id);

        if (empty($feegroup_type)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Fee master not found'
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data' => [
                    'feemaster'     => $feegroup_type,
                    'feegroupList'  => $this->feegroup_model->get(),
                    'feetypeList'   => $this->feetype_model->get(),
                    'feemasterList' => $this->feesessiongroup_model->getFeesByGroup(null, 0),
                ]
            ]));
    }
}

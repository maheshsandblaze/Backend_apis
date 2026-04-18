<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Expensehead extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('expensehead_model');
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

        $category_result = $this->expensehead_model->get();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $category_result
            ]));
    }

    public function ajaxSearch()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $expense_head = $this->expensehead_model->getDatatableExpenseHead();
        $expense_head = json_decode($expense_head);
        $dt_data      = array();

        if (!empty($expense_head->data)) {
            foreach ($expense_head->data as $exhead_key => $exhead_value) {
                $row = array();
                $row['id'] = $exhead_value->id;
                $row['exp_category'] = $exhead_value->exp_category;
                $row['description'] = $exhead_value->description;
                $dt_data[] = $row;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw"            => intval($expense_head->draw),
                "recordsTotal"    => intval($expense_head->recordsTotal),
                "recordsFiltered" => intval($expense_head->recordsFiltered),
                "data"            => $dt_data,
            ]));
    }

    public function fetch($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $category = $this->expensehead_model->get($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $category
            ]));
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!empty($id)) {
            $this->expensehead_model->remove($id);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $this->lang->line('delete_message')
                ]));
        } else {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'ID is required'
                ]));
        }
    }

    public function create()
    {
        /* =========================
       CORS
    ========================== */
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        /* =========================
       INPUT
    ========================== */
        $input = $this->_get_input();

        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        /* =========================
       VALIDATION
    ========================== */
        $this->form_validation->set_rules(
            'expensehead',
            $this->lang->line('expense_head'),
            'trim|required|xss_clean'
        );

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        /* =========================
       DUPLICATE CHECK 🔥
    ========================== */
        $expensehead = trim($input['expensehead']);

        $exists = $this->expensehead_model
            ->checkExpenseHeadExists($expensehead); // create this

        if ($exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Expense head already exists'
                ]));
        }

        /* =========================
       INSERT
    ========================== */
        $data = [
            'exp_category' => $expensehead,
            'description'  => $input['description'] ?? '',
        ];

        $insert_id = $this->expensehead_model->add($data);

        /* =========================
       RESPONSE
    ========================== */
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message'),
                'id'      => $insert_id
            ]));
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('expensehead', $this->lang->line('expense_head'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {



                        /* =========================
                DUPLICATE CHECK 🔥
                ========================== */
            // $expensehead = trim($input['expensehead']);

            // $exists = $this->expensehead_model
            //     ->checkExpenseHeadExists($expensehead); // create this

            // if ($exists) {
            //     return $this->output
            //         ->set_status_header(409)
            //         ->set_output(json_encode([
            //             'status' => false,
            //             'message' => 'Expense head already exists'
            //         ]));
            // }
            $data = array(
                'id'           => $id,
                'exp_category' => $input['expensehead'],
                'description'  => $input['description'] ?? '',
            );
            $this->expensehead_model->add($data);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $this->lang->line('update_message')
                ]));
        }
    }
}

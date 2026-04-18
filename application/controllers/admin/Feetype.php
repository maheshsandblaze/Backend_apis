<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feetype extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("feetype_model");
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

        $feegroup_result = $this->feetype_model->get();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $feegroup_result
            ]));
    }

    public function addold()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules(
            'code',
            $this->lang->line('fees_code'),
            array(
                'required',
                array('check_exists', array($this->feetype_model, 'check_exists')),
            )
        );
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');

        if ($this->form_validation->run() == false) {
            $errors = array(
                'code' => form_error('code'),
                'name' => form_error('name'),
            );
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        } else {
            $data = array(
                'type'        => $input['name'],
                'code'        => $input['code'],
                'description' => $input['description'] ?? '',
            );
            $insert_id = $this->feetype_model->add($data);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $this->lang->line('success_message'),
                    'id'      => $insert_id
                ]));
        }
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        // ✅ Basic Validation
        $this->form_validation->set_rules('code', $this->lang->line('fees_code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'code' => form_error('code'),
                        'name' => form_error('name'),
                    ]
                ]));
        }

        $id   = $input['id'] ?? null;
        $code = trim($input['code']);
        $name = trim($input['name']);

        /* =========================
       ✅ DUPLICATE CODE CHECK (UPDATE SAFE)
    ========================== */
        $this->db->where('code', $code);

        if (!empty($id)) {
            $this->db->where('id !=', $id);
        }

        $code_exists = $this->db->get('feetype')->row(); // 🔁 check table name

        if ($code_exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Fee code already exists'
                ]));
        }

        /* =========================
       ✅ DUPLICATE NAME CHECK (UPDATE SAFE)
    ========================== */
        $this->db->where('type', $name);

        if (!empty($id)) {
            $this->db->where('id !=', $id);
        }

        $name_exists = $this->db->get('feetype')->row();

        if ($name_exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Fee type name already exists'
                ]));
        }

        /* =========================
       ✅ SAVE DATA
    ========================== */
        $data = [
            'id'          => $id,
            'type'        => $name,
            'code'        => $code,
            'description' => $input['description'] ?? '',
        ];

        $insert_id = $this->feetype_model->add($data);

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

        $this->form_validation->set_rules(
            'name',
            $this->lang->line('name'),
            array(
                'required',
                array('check_exists', array($this->feetype_model, 'check_exists')),
            )
        );
        $this->form_validation->set_rules('code', $this->lang->line('fees_code'), 'required');

        if ($this->form_validation->run() == false) {
            $errors = array(
                'name' => form_error('name'),
                'code' => form_error('code'),
            );
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $errors
                ]));
        } else {
            $data = array(
                'id'          => $id,
                'type'        => $input['name'],
                'code'        => $input['code'],
                'description' => $input['description'] ?? '',
            );
            $this->feetype_model->add($data);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $this->lang->line('update_message')
                ]));
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!empty($id)) {
            $this->feetype_model->remove($id);
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

    public function fetch($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $result = $this->feetype_model->get($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $result
            ]));
    }
}

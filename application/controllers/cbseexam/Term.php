<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Term extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data = [
            'categorylist'     => $this->category_model->get(),
            'subjectgroupList' => $this->subjectgroup_model->getByID(),
            'classlist'        => $this->class_model->get()
        ];

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }


    public function addold()
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

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (empty($input['name']) || empty($input['term_code'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'name'      => empty($input['name']) ? 'Term name required' : null,
                        'term_code' => empty($input['term_code']) ? 'Term code required' : null
                    ]
                ]));
        }

        if ($this->cbseexam_term_model->check_check_duplicate_code(
            $input['term_code'],
            $input['id'] ?? 0
        )) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => $this->lang->line('term_code_already_exists')
                ]));
        }

        $data = [
            'id'          => $input['id'] ?? null,
            'name'        => $input['name'],
            'term_code'   => $input['term_code'],
            'description' => $input['description'] ?? '',
            'created_by'  => $this->customlib->getStaffID()
        ];

        $this->cbseexam_term_model->add($data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }

    public function add()
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

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (empty($input['name']) || empty($input['term_code'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'name'      => empty($input['name']) ? 'Term name required' : null,
                        'term_code' => empty($input['term_code']) ? 'Term code required' : null
                    ]
                ]));
        }

        $term_id   = $input['id'] ?? null;
        $name      = trim($input['name']);
        $term_code = trim($input['term_code']);

        /* =========================
                    ✅ DUPLICATE TERM NAME CHECK (UPDATE SAFE)
                    ========================== */
        $this->db->where('name', $name);

        if (!empty($term_id)) {
            $this->db->where('id !=', $term_id); // exclude current record
        }

        $name_exists = $this->db->get('cbse_terms')->row();

        if ($name_exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Term name already exists'
                ]));
        }

        /* =========================
                ✅ DUPLICATE TERM CODE CHECK (UPDATE SAFE)
                ========================== */
        $this->db->where('term_code', $term_code);

        if (!empty($term_id)) {
            $this->db->where('id !=', $term_id); // exclude current record
        }

        $code_exists = $this->db->get('cbse_terms')->row();

        if ($code_exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Term code already exists'
                ]));
        }

        /* =========================
                SAVE DATA
                ========================== */
        $data = [
            'id'          => $term_id,
            'name'        => $name,
            'term_code'   => $term_code,
            'description' => $input['description'] ?? '',
            'created_by'  => $this->customlib->getStaffID()
        ];

        $this->cbseexam_term_model->add($data);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message')
            ]));
    }

    public function check_duplicate_code()
    {
        $term_code = $this->input->post('term_code');
        $id = $this->input->post('id') ?? 0;

        if ($term_code == '') {
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => true
                ]));
        }

        if ($this->cbseexam_term_model->check_check_duplicate_code($term_code, $id)) {
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => $this->lang->line('term_code_already_exists')
                ]));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true
            ]));
    }


    public function getdata()
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

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }


        if (empty($input['id'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'id'      => empty($input['id']) ? 'ID  required' : null
                    ]
                ]));
        }
        $id = $input['id'];

        $result = $this->cbseexam_term_model->get($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $result
            ]));
    }


    public function get_ClassSectionByTermId($termid)
    {
        $result           = $this->cbseexam_term_model->get($termid);
        $data['class_id'] = $result->class_id;
        $data['sections'] = $this->section_model->getClassBySection($result->class_id);
        echo json_encode($data);
    }

    public function delete($id)
    {


        $this->cbseexam_term_model->remove($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('delete_message')
            ]));
    }


    public function gettermlist()
    {
        $m = json_decode($this->cbseexam_term_model->gettermlist(), true);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'draw' => intval($m['draw'] ?? 0),
                'recordsTotal' => intval($m['recordsTotal'] ?? 0),
                'recordsFiltered' => intval($m['recordsFiltered'] ?? 0),
                'data' => $m['data'] ?? []
            ]));
    }
}

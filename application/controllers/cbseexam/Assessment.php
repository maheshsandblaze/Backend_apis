<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Assessment extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->cbseexam_assessment_model->getassessmentlist();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $result
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
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['name']) || empty($input['types'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Assessment name and types required'
                ]));
        }

        /* =========================
                    MAIN ASSESSMENT
                    ========================== */
        $insert_assessment = [
            'id'          => $input['record_id'] ?? null,
            'name'        => $input['name'],
            'description' => $input['description'] ?? ''
        ];

        $insert_types = [];
        $update_types = [];
        $delete_types = [];
        $existing_ids = [];

        /* =========================
                GET OLD TYPES (FOR DELETE)
                ========================== */
        $old_types = [];
        if (!empty($insert_assessment['id'])) {
            $old_types = $this->cbseexam_assessment_model
                ->getTypesByAssessmentId($insert_assessment['id']);
        }

        $old_ids = !empty($old_types) ? array_column($old_types, 'id') : [];

        /* =========================
                PROCESS TYPES
                ========================== */
        foreach ($input['types'] as $type) {

            // validation
            if ($type['pass_percentage'] > 100) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Pass percentage cannot exceed 100'
                    ]));
            }

            if (!empty($type['id'])) {

                // update
                $existing_ids[] = $type['id'];

                $update_types[] = [
                    'id' => $type['id'],
                    'cbse_exam_assessment_id' => $insert_assessment['id'],
                    'name' => $type['name'],
                    'code' => $type['code'],
                    'maximum_marks' => $type['maximum_marks'],
                    'pass_percentage' => $type['pass_percentage'],
                    'description' => $type['description'] ?? '',
                    'created_by' => $this->customlib->getStaffID(),
                ];
            } else {

                // insert
                $insert_types[] = [
                    'cbse_exam_assessment_id' => $insert_assessment['id'],
                    'name' => $type['name'],
                    'code' => $type['code'],
                    'maximum_marks' => $type['maximum_marks'],
                    'pass_percentage' => $type['pass_percentage'],
                    'description' => $type['description'] ?? '',
                    'created_by' => $this->customlib->getStaffID(),
                ];
            }
        }

        /* =========================
                DELETE LOGIC (FIXED 🔥)
                ========================== */
        if (!empty($old_ids)) {
            $delete_ids = array_diff($old_ids, $existing_ids);

            foreach ($delete_ids as $id) {
                $delete_types[] = ['id' => $id];
            }
        }








        /* =========================
                    SAVE (MODEL CALL)
                    ========================== */
        $this->cbseexam_assessment_model->add_graderange(
            $insert_assessment,
            $insert_types,
            $update_types,
            $delete_types
        );

        /* =========================
                RESPONSE
                ========================== */
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Assessment saved successfully',
                'inserted' => count($insert_types),
                'updated'  => count($update_types),
                'deleted'  => count($delete_types)
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
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['name']) || empty($input['types'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Assessment name and types required'
                ]));
        }

        /* =========================
       ✅ ASSESSMENT DUPLICATE CHECK (UPDATE SAFE)
    ========================== */

    // echo "<Pre>";print_r($input);exit;
        $this->db->where('name', trim($input['name']));

        if (!empty($input['record_id'])) {
            $this->db->where('id !=', $input['record_id']); // exclude current record
        }

        $exists = $this->db->get('cbse_exam_assessments')->row();

        // echo "<Pre>";print_r($exists);exit;

        if ($exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Assessment name already exists'
                ]));
        }

        /* =========================
       MAIN ASSESSMENT
    ========================== */
        $insert_assessment = [
            'id'          => $input['record_id'] ?? null,
            'name'        => trim($input['name']),
            'description' => $input['description'] ?? ''
        ];

        $insert_types = [];
        $update_types = [];
        $delete_types = [];
        $existing_ids = [];

        $type_names = [];
        $type_codes = [];

        /* =========================
       GET OLD TYPES
    ========================== */
        $old_types = [];
        if (!empty($insert_assessment['id'])) {
            $old_types = $this->cbseexam_assessment_model
                ->getTypesByAssessmentId($insert_assessment['id']);
        }

        $old_ids = !empty($old_types) ? array_column($old_types, 'id') : [];

        /* =========================
       PROCESS TYPES
    ========================== */
        foreach ($input['types'] as $type) {

            $name = isset($type['name']) ? trim($type['name']) : '';
            $code = isset($type['code']) ? trim($type['code']) : '';
            $type_id = $type['id'] ?? null;

            if ($name == '' || $code == '') {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Type name and code required'
                    ]));
            }

            // ✅ PASS % VALIDATION
            if (!empty($type['pass_percentage']) && $type['pass_percentage'] > 100) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Pass percentage cannot exceed 100'
                    ]));
            }

            // ✅ DUPLICATE TYPE NAME (REQUEST LEVEL)
            if (in_array($name, $type_names)) {
                return $this->output
                    ->set_status_header(409)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => "Duplicate type name: $name"
                    ]));
            }
            $type_names[] = $name;

            // ✅ DUPLICATE TYPE CODE (REQUEST LEVEL)
            if (in_array($code, $type_codes)) {
                return $this->output
                    ->set_status_header(409)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => "Duplicate type code: $code"
                    ]));
            }
            $type_codes[] = $code;

            // ✅ DB DUPLICATE CHECK (UPDATE SAFE)
            $this->db->group_start()
                ->where('name', $name)
                ->or_where('code', $code)
                ->group_end();

            if (!empty($type_id)) {
                $this->db->where('id !=', $type_id); // exclude current type
            }

            $this->db->where('cbse_exam_assessment_id',$insert_assessment['id']);

            $type_exists = $this->db->get('cbse_exam_assessment_types')->row();

            // echo "<pre>";print_r($type_exists);exit;

            if ($type_exists) {
                return $this->output
                    ->set_status_header(409)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => "Assessment type already exists"
                    ]));
            }

            // ✅ UPDATE OR INSERT
            if (!empty($type_id)) {

                $existing_ids[] = $type_id;

                $update_types[] = [
                    'id' => $type_id,
                    'cbse_exam_assessment_id' => $insert_assessment['id'],
                    'name' => $name,
                    'code' => $code,
                    'maximum_marks' => $type['maximum_marks'] ?? 0,
                    'pass_percentage' => $type['pass_percentage'] ?? 0,
                    'description' => $type['description'] ?? '',
                    'created_by' => $this->customlib->getStaffID(),
                ];
            } else {

                $insert_types[] = [
                    'cbse_exam_assessment_id' => $insert_assessment['id'],
                    'name' => $name,
                    'code' => $code,
                    'maximum_marks' => $type['maximum_marks'] ?? 0,
                    'pass_percentage' => $type['pass_percentage'] ?? 0,
                    'description' => $type['description'] ?? '',
                    'created_by' => $this->customlib->getStaffID(),
                ];
            }
        }

        /* =========================
       DELETE LOGIC
    ========================== */
        if (!empty($old_ids)) {
            $delete_ids = array_diff($old_ids, $existing_ids);

            foreach ($delete_ids as $id) {
                $delete_types[] = ['id' => $id];
            }
        }

        /* =========================
       SAVE
    ========================== */
        $this->cbseexam_assessment_model->add_graderange(
            $insert_assessment,
            $insert_types,
            $update_types,
            $delete_types
        );

        /* =========================
       RESPONSE
    ========================== */
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'   => true,
                'message'  => 'Assessment saved successfully',
                'inserted' => count($insert_types),
                'updated'  => count($update_types),
                'deleted'  => count($delete_types)
            ]));
    }


    function percentage_greater($str, $str2)
    {

        if ($str2 > 0 && $str2 < 100) {
            // return success
            return true;
        } else {
            // set error message
            $this->form_validation->set_message('percentage_greater', $this->lang->line('percentage_should_be_greater_than_or_less'));

            // return fail
            return FALSE;
        }
    }

    public function get_editdetails()
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
                        'id' => 'Assessment ID required'
                    ]
                ]));
        }

        $result = $this->cbseexam_assessment_model->get_editdetails($input['id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $result
            ]));
    }


    public function assessmentform()
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

        $record_id = $input['record_id'] ?? 0;

        $data = [];
        $total_rows = 2;

        if ($record_id > 0) {
            $old_data = $this->cbseexam_assessment_model->getWithAssessmentType($record_id);
            $data = $old_data;
            $total_rows = count($old_data['list']) + 1;
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'     => true,
                'data'       => $data,
                'total_rows' => $total_rows
            ]));
    }


    public function add_type()
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
                        'id' => 'Assessment ID required'
                    ]
                ]));
        }

        $result = $this->cbseexam_assessment_model->get_assessmentTypebyId($input['id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $result
            ]));
    }


    public function remove()
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
                        'id' => 'Assessment ID required'
                    ]
                ]));
        }

        $this->cbseexam_assessment_model->remove($input['id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('delete_message')
            ]));
    }
}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Grade extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function gradelist()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $result = $this->cbseexam_grade_model->getgradelist();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $result
            ]));
    }


    public function addold()
    {

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

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
        if (empty($input)) {
            $input = $this->input->post();
        }


        if (empty($input['name']) || empty($input['row'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'name' => empty($input['name']) ? 'Grade name required' : null,
                        'row'  => empty($input['row']) ? 'Grade ranges required' : null
                    ]
                ]));
        }


        $insert_grade = [
            'id'          => $input['record_id'] ?? null,
            'name'        => $input['name'],
            'description' => $input['description'] ?? ''
        ];

        $cbse_exam_grades_range        = [];
        $cbse_exam_grades_range_update = [];
        $delete_grade_range            = [];
        $existing_ids                  = [];


        $old_ranges = [];

        if (!empty($insert_grade['id'])) {
            $old_ranges = $this->cbseexam_grade_model
                ->getRangesByGradeId($insert_grade['id']);
        }

        $old_ids = !empty($old_ranges) ? array_column($old_ranges, 'id') : [];


        foreach ($input['row'] as $k => $row_value) {

            $update_id = $input['update_id'][$k] ?? null;

            $name        = $input['range_name_' . $row_value] ?? '';
            $min         = $input['minimum_percentage_' . $row_value] ?? 0;
            $max         = $input['maximum_percentage_' . $row_value] ?? 0;
            $description = $input['type_description_' . $row_value] ?? '';

            // Basic validation
            if ($min > $max) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => "Min percentage cannot be greater than max"
                    ]));
            }

            if (!empty($update_id)) {

                // UPDATE
                $existing_ids[] = $update_id;

                $cbse_exam_grades_range_update[] = [
                    'id' => $update_id,
                    'cbse_exam_grade_id' => $insert_grade['id'],
                    'name' => $name,
                    'minimum_percentage' => $min,
                    'maximum_percentage' => $max,
                    'description' => $description,
                    'created_by' => $this->customlib->getStaffID(),
                ];
            } else {

                // INSERT
                $cbse_exam_grades_range[] = [
                    'cbse_exam_grade_id' => $insert_grade['id'], // FIXED
                    'name' => $name,
                    'minimum_percentage' => $min,
                    'maximum_percentage' => $max,
                    'description' => $description,
                    'created_by' => $this->customlib->getStaffID(),
                ];
            }
        }

        /* =========================
       DELETE LOGIC (IMPORTANT 🔥)


       ========================== */

        $delete_ids = [];
        if (!empty($old_ids)) {

            $delete_ids = array_diff($old_ids, $existing_ids);

            // foreach ($delete_ids as $id) {
            //     $delete_grade_range[] = ['id' => $id];
            // }
        }

        // echo "<pre>";
        // print_r($delete_ids);exit;

        /* =========================
            SAVE (MODEL CALL)
            ========================== */
        $this->cbseexam_grade_model->add_graderange(
            $insert_grade,
            $cbse_exam_grades_range,
            $cbse_exam_grades_range_update,
            $delete_ids
        );

        /* =========================
       RESPONSE
          ========================== */
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Grade saved successfully',
                'inserted' => count($cbse_exam_grades_range),
                'updated'  => count($cbse_exam_grades_range_update),
                'deleted'  => count($delete_grade_range)
            ]));
    }

    public function add()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

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
        if (empty($input)) {
            $input = $this->input->post();
        }

        // ✅ BASIC VALIDATION
        if (empty($input['name']) || empty($input['row'])) {
            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'name' => empty($input['name']) ? 'Grade name required' : null,
                        'row'  => empty($input['row']) ? 'Grade ranges required' : null
                    ]
                ]));
        }

        // ✅ DUPLICATE GRADE NAME CHECK (UPDATE SAFE)
        $grade_exists = $this->cbseexam_grade_model->check_grade_name(
            $input['name'],
            $input['record_id'] ?? null
        );



        // echo "<pre>";print_r($grade_exists);exit;

        if ($grade_exists) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Grade name already exists'
                ]));
        }

        $insert_grade = [
            'id'          => $input['record_id'] ?? null,
            'name'        => trim($input['name']),
            'description' => $input['description'] ?? ''
        ];

        $cbse_exam_grades_range        = [];
        $cbse_exam_grades_range_update = [];
        $existing_ids                  = [];

        $range_names = [];
        $ranges      = [];

        // 🔹 OLD DATA (FOR DELETE)
        $old_ranges = [];
        if (!empty($insert_grade['id'])) {
            $old_ranges = $this->cbseexam_grade_model
                ->getRangesByGradeId($insert_grade['id']);
        }

        $old_ids = !empty($old_ranges) ? array_column($old_ranges, 'id') : [];

        foreach ($input['row'] as $k => $row_value) {

            $update_id = $input['update_id'][$k] ?? null;

            $name        = trim($input['range_name_' . $row_value] ?? '');
            $min         = $input['minimum_percentage_' . $row_value] ?? 0;
            $max         = $input['maximum_percentage_' . $row_value] ?? 0;
            $description = $input['type_description_' . $row_value] ?? '';

            // ✅ MIN/MAX VALIDATION
            if ($min > $max) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => "Min percentage cannot be greater than max"
                    ]));
            }

            // ✅ DUPLICATE RANGE NAME CHECK
            if (in_array($name, $range_names)) {
                return $this->output
                    ->set_status_header(409)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => "Duplicate range name: $name"
                    ]));
            }
            $range_names[] = $name;

            // ✅ OVERLAPPING RANGE CHECK
            foreach ($ranges as $r) {
                if (
                    ($min >= $r['min'] && $min <= $r['max']) ||
                    ($max >= $r['min'] && $max <= $r['max']) ||
                    ($min <= $r['min'] && $max >= $r['max'])
                ) {
                    return $this->output
                        ->set_status_header(409)
                        ->set_output(json_encode([
                            'status' => false,
                            'message' => "Percentage ranges overlapping"
                        ]));
                }
            }
            $ranges[] = ['min' => $min, 'max' => $max];

            // ✅ UPDATE OR INSERT
            if (!empty($update_id)) {

                $existing_ids[] = $update_id;

                $cbse_exam_grades_range_update[] = [
                    'id' => $update_id,
                    'cbse_exam_grade_id' => $insert_grade['id'],
                    'name' => $name,
                    'minimum_percentage' => $min,
                    'maximum_percentage' => $max,
                    'description' => $description,
                    'created_by' => $this->customlib->getStaffID(),
                ];
            } else {

                $cbse_exam_grades_range[] = [
                    'cbse_exam_grade_id' => $insert_grade['id'],
                    'name' => $name,
                    'minimum_percentage' => $min,
                    'maximum_percentage' => $max,
                    'description' => $description,
                    'created_by' => $this->customlib->getStaffID(),
                ];
            }
        }

        // ✅ DELETE LOGIC
        $delete_ids = [];
        if (!empty($old_ids)) {
            $delete_ids = array_diff($old_ids, $existing_ids);
        }

        // ✅ SAVE
        $this->cbseexam_grade_model->add_graderange(
            $insert_grade,
            $cbse_exam_grades_range,
            $cbse_exam_grades_range_update,
            $delete_ids
        );

        // ✅ RESPONSE
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'   => true,
                'message'  => 'Grade saved successfully',
                'inserted' => count($cbse_exam_grades_range),
                'updated'  => count($cbse_exam_grades_range_update),
                'deleted'  => count($delete_ids) // ✅ FIXED
            ]));
    }



    // public function get_editdetails()
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }

    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         return $this->output
    //             ->set_status_header(405)
    //             ->set_output(json_encode([
    //                 'status' => false,
    //                 'message' => 'Method Not Allowed'
    //             ]));
    //     }

    //     $input = json_decode(file_get_contents('php://input'), true);
    //     if (empty($input)) {
    //         $input = $this->input->post();
    //     }

    //     if (empty($input['id'])) {
    //         return $this->output
    //             ->set_status_header(422)
    //             ->set_output(json_encode([
    //                 'status' => false,
    //                 'message' => 'ID required'
    //             ]));
    //     }

    //     $result = $this->cbseexam_grade_model->get_editdetails($input['id']);

    //     return $this->output
    //         ->set_status_header(200)
    //         ->set_output(json_encode([
    //             'status' => true,
    //             'data' => $result
    //         ]));
    // }

    public function add_graderange()
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
        if (empty($input)) {
            $input = $this->input->post();
        }

        $result = $this->cbseexam_grade_model->get_graderangebyId($input['id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data' => $result
            ]));
    }

    public function gradeform()
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
        if (empty($input)) {
            $input = $this->input->post();
        }

        $record_id = $input['record_id'] ?? 0;

        $response = [
            'record_id' => $record_id,
            'total_rows' => 2
        ];

        if ($record_id > 0) {
            $old_data = $this->cbseexam_grade_model->getWithRange($record_id);
            $response['ranges'] = $old_data['list'];
            $response['total_rows'] = count($old_data['list']) + 1;
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data' => $response
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
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }



        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $this->cbseexam_grade_model->remove($input['id']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'message' => $this->lang->line('delete_message')
            ]));
    }
}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Roles extends Public_Controller
{
    private $perm_category = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->config('mailsms');
        $this->perm_category = $this->config->item('perm_category');
        $this->load->model('role_model');
        $this->load->library('form_validation');
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

        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules(
                'name',
                $this->lang->line('name'),
                array(
                    'required',
                    array('check_exists', function ($str) use ($input) {
                        return $this->role_model->valid_check_exists($str, $input);
                    }),
                )
            );

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $data = array(
                    'name' => $input['name'],
                );
                $id = $this->role_model->add($data);
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('success_message'),
                        'id'      => $id
                    ]));
            }
        }

        $listroute = $this->role_model->get();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'    => 'success',
                'listroute' => $listroute
            ]));
    }

    public function permissionold($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $role = $this->role_model->get($id);
        if (!$role) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Role not found']));
        }

        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {

            $per_cat_post = $input['per_cat'] ?? [];
            $role_id      = $id; // Using ID from URL for safety
            $to_be_insert = array();
            $to_be_update = array();
            $to_be_delete = array();

            foreach ($per_cat_post as $per_cat_post_key => $per_cat_post_value) {
                $insert_data = array();
                $ar          = array();
                foreach ($this->perm_category as $per_key => $per_value) {
                    $chk_val = $input[$per_value . "-perm_" . $per_cat_post_value] ?? null;

                    if (isset($chk_val) && ($chk_val == 1 || $chk_val == "1" || $chk_val == "on")) {
                        $insert_data[$per_value] = 1;
                    } else {
                        $ar[$per_value] = 0;
                    }
                }

                $prev_id = $input['roles_permissions_id_' . $per_cat_post_value] ?? 0;
                if ($prev_id != 0) {
                    if (!empty($insert_data)) {
                        $insert_data['id'] = $prev_id;
                        $to_be_update[]    = array_merge($ar, $insert_data);
                    } else {
                        $to_be_delete[] = $prev_id;
                    }
                } elseif (!empty($insert_data)) {
                    $insert_data['role_id']     = $role_id;
                    $insert_data['perm_cat_id'] = $per_cat_post_value;
                    $to_be_insert[]             = array_merge($ar, $insert_data);
                }
            }

  
            $this->role_model->getInsertBatch($role_id, $to_be_insert, $to_be_update, $to_be_delete);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('update_message')
                ]));
        }

        $role_permission = $this->role_model->find($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'          => 'success',
                'role'            => $role,
                'role_permission' => $role_permission
            ]));
    }

    public function permission($id = null)
    {


        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }


        if (empty($id)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Role ID is required'
                ]));
        }

        /* =========================
                    PRIVILEGE CHECK
                    ========================== */
        // if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access denied'
        //         ]));
        // }

        /* =========================
                    GET → RETURN DATA
                    ========================== */
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $role = $this->role_model->get($id);

            if (empty($role)) {
                return $this->output
                    ->set_status_header(404)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Role not found'
                    ]));
            }

            $role_permission = $this->role_model->find($role['id']);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'          => 'success',
                    'role'            => $role,
                    'role_permission' => $role_permission
                ]));
        }

        /* =========================
                    POST → UPDATE PERMISSION
                    ========================== */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Accept JSON or form-data
            $input = json_decode(file_get_contents("php://input"), true);

            if (empty($input)) {
                $input = $this->input->post();
            }

            // echo "<pre>";
            // print_r($input);exit;

            if (empty($input['per_cat'])) {
                return $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'per_cat  are required'
                    ]));
            }

            $per_cat_post = $input['per_cat'];
            $role_id      = $id;

            $to_be_insert = [];
            $to_be_update = [];
            $to_be_delete = [];

            foreach ($per_cat_post as $per_cat_post_value) {

                $insert_data = [];
                $ar = [];

                foreach ($this->perm_category as $per_value) {

                    $key = $per_value . "-perm_" . $per_cat_post_value;

                    if (!empty($input[$key])) {
                        $insert_data[$per_value] = 1;
                    } else {
                        $ar[$per_value] = 0;
                    }
                }

                $prev_id_key = 'roles_permissions_id_' . $per_cat_post_value;
                $prev_id = $input[$prev_id_key] ?? 0;

                if ($prev_id != 0) {

                    if (!empty($insert_data)) {
                        $insert_data['id'] = $prev_id;
                        $to_be_update[] = array_merge($ar, $insert_data);
                    } else {
                        $to_be_delete[] = $prev_id;
                    }
                } elseif (!empty($insert_data)) {

                    $insert_data['role_id']     = $role_id;
                    $insert_data['perm_cat_id'] = $per_cat_post_value;

                    $to_be_insert[] = array_merge($ar, $insert_data);
                }
            }

            /* ===== SAVE ===== */
            //           echo "<pre>";
            // print_r($to_be_insert); 
            // echo "<br><br>";            
            // print_r($to_be_update); 
            // echo "<br><br>";
            // print_r($to_be_delete);
            // exit;
            $this->role_model->getInsertBatch($role_id, $to_be_insert, $to_be_update, $to_be_delete);

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'message' => 'Permissions updated successfully',
                    'inserted' => count($to_be_insert),
                    'updated' => count($to_be_update),
                    'deleted' => count($to_be_delete)
                ]));
        }

        /* =========================
                    INVALID METHOD
                    ========================== */
        return $this->output
            ->set_status_header(405)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => false,
                'message' => 'Method not allowed'
            ]));
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $editrole = $this->role_model->get($id);
        if (!$editrole) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Role not found']));
        }

        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $input['id'] = $id; // Ensure ID is in input for validation
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules(
                'name',
                $this->lang->line('name'),
                array(
                    'required',
                    array('check_exists', function ($str) use ($input) {
                        return $this->role_model->valid_check_exists($str, $input);
                    }),
                )
            );

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $data = array(
                    'id'   => $id,
                    'name' => $input['name'],
                );
                $this->role_model->add($data);
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('update_message')
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'   => 'success',
                'editrole' => $editrole
            ]));
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $this->role_model->remove($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('delete_message')
            ]));
    }
}

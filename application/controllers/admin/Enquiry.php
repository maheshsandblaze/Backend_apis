<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Enquiry extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("enquiry_model");
        $this->load->model('feeenquiry_model');
        $this->load->model('feefollowup_model');
        $this->load->model('student_model');
        $this->config->load("payroll");
        $this->enquiry_status = $this->config->item('enquiry_status');
        $this->feeenquiry_status = $this->config->item('fee_enquiry_status');
    }

    // public function index()
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }

    //     $input = json_decode(file_get_contents('php://input'), true);
    //     if (empty($input)) {
    //         $input = $this->input->get_post(NULL);
    //     }

    //     $class      = $input['class'] ?? null;
    //     $source     = $input['source'] ?? null;
    //     $status     = $input['status'] ?? "active";
    //     $from_date  = $input['from_date'] ?? null;
    //     $to_date    = $input['to_date'] ?? null;

    //     if ($from_date && $to_date) {
    //         $date_from    = date("Y-m-d", $this->customlib->datetostrtotime($from_date));
    //         $date_to      = date("Y-m-d", $this->customlib->datetostrtotime($to_date));
    //         $enquiry_list = $this->enquiry_model->searchEnquiry($class, $source, $date_from, $date_to, $status);
    //     } else {
    //         $enquiry_list = $this->enquiry_model->getenquiry_list();
    //     }

    //     foreach ($enquiry_list as $key => $value) {
    //         $follow_up                          = $this->enquiry_model->getFollowByEnquiry($value["id"]);
    //         $enquiry_list[$key]["followupdate"] = isset($follow_up["date"]) ? $follow_up["date"] : '';
    //         $enquiry_list[$key]["next_date"]    = isset($follow_up["next_date"]) ? $follow_up["next_date"] : '';
    //         $enquiry_list[$key]["response"]     = isset($follow_up["response"]) ? $follow_up["response"] : '';
    //         $enquiry_list[$key]["note"]         = isset($follow_up["note"]) ? $follow_up["note"] : '';
    //         $enquiry_list[$key]["followup_by"]  = isset($follow_up["followup_by"]) ? $follow_up["followup_by"] : '';
    //     }

    //     $data = [
    //         'enquiry_list'   => $enquiry_list,
    //         'enquiry_status' => $this->enquiry_status,
    //         'class_list'     => $this->class_model->get(),
    //         'stff_list'      => $this->staff_model->get(),
    //         'Reference'      => $this->enquiry_model->get_reference(),
    //         'sourcelist'     => $this->enquiry_model->getComplaintSource(),
    //     ];

    //     return $this->output
    //         ->set_status_header(200)
    //         ->set_output(json_encode([
    //             'status'  => true,
    //             'data'    => $data
    //         ]));
    // }


    public function index_old()
    {





        // $allowed_origins = [
        //     'http://localhost:4173',
        //     'http://localhost:5173',
        //     'https://newlayout.wisibles.com',
        //     'https://alpha.wisibles.com'
        // ];

        // $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // if (in_array($origin, $allowed_origins)) {
        //     header("Access-Control-Allow-Origin: $origin");
        // } else {
        //     header("Access-Control-Allow-Origin: https://alpha.wisibles.com");
        // }

        // header("Access-Control-Allow-Credentials: true");
        // header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        // header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        // header("Vary: Origin");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }


        /* =========================
           READ JSON OR FORM DATA
        ========================== */
        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            $input = $this->input->get_post(NULL, true);
        }

        $class     = $input['class'] ?? null;
        $source    = $input['source'] ?? null;
        $status    = $input['status'] ?? 'active';
        $from_date = $input['from_date'] ?? null;
        $to_date   = $input['to_date'] ?? null;

        /* =========================
           DATE SAFE CONVERSION
        ========================== */
        if ($from_date && $to_date) {
            $date_from = date('Y-m-d', strtotime($from_date));
            $date_to   = date('Y-m-d', strtotime($to_date));

            $enquiry_list = $this->enquiry_model
                ->searchEnquiry($class, $source, $date_from, $date_to, $status);
        } else {
            $enquiry_list = $this->enquiry_model->getenquiry_list();
        }

        /* =========================
           FOLLOW-UP DATA
        ========================== */
        foreach ($enquiry_list as $key => $value) {
            $follow_up = $this->enquiry_model->getFollowByEnquiry($value['id']);

            $enquiry_list[$key]['followupdate'] = $follow_up['date'] ?? '';
            $enquiry_list[$key]['next_date']    = $follow_up['next_date'] ?? '';
            $enquiry_list[$key]['response']     = $follow_up['response'] ?? '';
            $enquiry_list[$key]['note']         = $follow_up['note'] ?? '';
            $enquiry_list[$key]['followup_by']  = $follow_up['followup_by'] ?? '';
        }

        /* =========================
           RESPONSE DATA
        ========================== */
        $data = [
            'enquiry_list'   => $enquiry_list,
            'enquiry_status' => $this->enquiry_status,
            'class_list'     => $this->class_model->get(),
            'staff_list'     => $this->staff_model->get(),
            'reference'      => $this->enquiry_model->get_reference(),
            'sourcelist'     => $this->enquiry_model->getComplaintSource(),
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }

    public function index()
    {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';

        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Credentials: true");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }

        $input = json_decode(file_get_contents('php://input'), true);





        $class     = $input['class'] ?? "";
        $source    = $input['source'] ?? "";
        $status    = $input['status'] ?? "active";
        $from_date = $input['from_date'] ?? "";
        $to_date   = $input['to_date'] ?? "";

        if (!empty($from_date) && !empty($to_date)) {

            // $date_from = date("Y-m-d", $this->customlib->datetostrtotime($from_date));
            // $date_to   = date("Y-m-d", $this->customlib->datetostrtotime($to_date));

            $date_from = $from_date;
            $date_to   = $to_date;



            $enquiry_list = $this->enquiry_model->searchEnquiry(
                $class,
                $source,
                $date_from,
                $date_to,
                $status
            );
        } else {

            $enquiry_list = $this->enquiry_model->getenquiry_list();
        }

        foreach ($enquiry_list as $key => $value) {

            $follow_up = $this->enquiry_model->getFollowByEnquiry($value["id"]);

            $enquiry_list[$key]["followupdate"] = $follow_up["date"] ?? "";
            $enquiry_list[$key]["next_date"]    = $follow_up["next_date"] ?? "";
            $enquiry_list[$key]["response"]     = $follow_up["response"] ?? "";
            $enquiry_list[$key]["note"]         = $follow_up["note"] ?? "";
            $enquiry_list[$key]["followup_by"]  = $follow_up["followup_by"] ?? "";
        }

        $response = [
            'status'         => true,
            'enquiry_list'   => $enquiry_list,
            'enquiry_status' => $this->enquiry_status,
            'reference'      => $this->enquiry_model->get_reference(),
            'source_list'    => $this->enquiry_model->getComplaintSource(),
            'class_list'     => $this->class_model->get(),
            'staff_list'     => $this->staff_model->get()
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }



    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('source', $this->lang->line('source'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('follow_up_date', $this->lang->line('next_follow_up_date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'    => form_error('name'),
                'contact' => form_error('contact'),
                'source'  => form_error('source'),
                'date'    => form_error('date'),
                'follow_up_date'    => form_error('follow_up_date'),
            );

            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error'  => $msg
                ]));
        } else {
            $created_by = $input['created_by'];

            $enquiry = array(
                'name'           => $input['name'],
                'contact'        => $input['contact'],
                'address'        => $input['address'] ?? '',
                'reference'      => $input['reference'] ?? '',
                'date'           => $this->safeDate($input['date']),
                'description'    => $input['description'] ?? '',
                'follow_up_date' => $this->safeDate($input['follow_up_date']),
                'note'           => $input['note'] ?? '',
                'source'         => $input['source'],
                'email'          => $input['email'] ?? '',
                'assigned'       => IsNullOrEmptyString($input['assigned'] ?? '') ? NULL : $input['assigned'],
                'class_id'       => IsNullOrEmptyString($input['class'] ?? '') ? NULL : $input['class'],
                'no_of_child'    => $input['no_of_child'] ?? 0,
                'status'         => 'active',
                'created_by'     => $created_by,
            );

            $insert_id = $this->enquiry_model->add($enquiry);

            if ($insert_id === false) {
                return $this->output
                    ->set_status_header(500)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'  => 'fail',
                        'message' => 'Database insert failed'
                    ]));
            }

            return $this->output
                ->set_status_header(201)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message'),
                    'id'      => $insert_id
                ]));
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!empty($id)) {
            $this->enquiry_model->enquiry_delete($id);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('delete_message')
                ]));
        }
    }

    private function safeDate($date)
    {
        if (empty($date)) {
            return null;
        }

        $ts = strtotime($date);
        return $ts ? date('Y-m-d', $ts) : null;
    }


    public function feefollow_up($enquiry_id, $status, $created_by)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $data['id'] = $enquiry_id;
        $enquiry_result = $this->feeenquiry_model->get($enquiry_id);
        $data['enquiry_data'] = !empty($enquiry_result) ? $enquiry_result[0] : null;

        if ($data['enquiry_data']) {
            $data['student_data'] = $this->student_model->get($data['enquiry_data']['student_id']);
            if (!empty($data['enquiry_data']['assigned'])) {
                $data['assigned_staff'] = $this->staff_model->get($data['enquiry_data']['assigned']);
            } else {
                $data['assigned_staff'] = '';
            }
        }

        $data['next_date']       = $this->feefollowup_model->next_follow_up_date($enquiry_id);
        $data['created_by_staff'] = $this->staff_model->get($created_by);
        $data['enquiry_status']  = $this->feeenquiry_status;

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['status' => true, 'data' => $data]));
    }

    public function follow_up($enquiry_id, $status, $created_by)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $data['id']              = $enquiry_id;
        $data['enquiry_data']    = $this->enquiry_model->getenquiry_list($enquiry_id, $status);

        if (!empty($data['enquiry_data']['assigned'])) {
            $data['assigned_staff'] = $this->staff_model->get($data['enquiry_data']['assigned']);
        } else {
            $data['assigned_staff'] = '';
        }
        $data['next_date']       = $this->enquiry_model->next_follow_up_date($enquiry_id);
        $data['created_by_staff'] = $this->staff_model->get($created_by);
        $data['enquiry_status']  = $this->enquiry_status;

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }

    public function follow_up_insert()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('response', $this->lang->line('response'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('follow_up_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('follow_up_date', $this->lang->line('next_follow_up_date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'response'       => form_error('response'),
                'follow_up_date' => form_error('follow_up_date'),
                'date'           => form_error('date'),
            );

            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode(['status' => 'fail', 'error' => $msg]));
        } else {
            $staff_id = $input['staff_id'] ?? 1;

            $follow_up = array(
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($input['date'])),
                'next_date'   => date('Y-m-d', $this->customlib->datetostrtotime($input['follow_up_date'])),
                'response'    => $input['response'],
                'note'        => $input['note'] ?? '',
                'followup_by' => $staff_id,
                'enquiry_id'  => $input['enquiry_id'],
            );
            $this->enquiry_model->add_follow_up($follow_up);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message')
                ]));
        }
    }

    public function fee_follow_up_insert()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('response', $this->lang->line('response'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('follow_up_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('follow_up_date', $this->lang->line('next_follow_up_date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'response'       => form_error('response'),
                'follow_up_date' => form_error('follow_up_date'),
                'date'           => form_error('date'),
            );
            return $this->output->set_status_header(422)->set_output(json_encode(['status' => 'fail', 'error' => $msg]));
        } else {
            $staff_id = $input['staff_id'] ?? 1;

            $follow_up = array(
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($input['date'])),
                'next_date'   => date('Y-m-d', $this->customlib->datetostrtotime($input['follow_up_date'])),
                'response'    => $input['response'],
                'note'        => $input['note'] ?? '',
                'followup_by' => $staff_id,
                'feeenquiry_id'  => $input['enquiry_id'],
            );
            $this->feefollowup_model->add_follow_up($follow_up);
            return $this->output->set_status_header(200)->set_output(json_encode(['status' => 'success', 'message' => $this->lang->line('success_message')]));
        }
    }

    public function fee_enquiry($student_id, $agent_id, $feetype_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $row_count = $this->feeenquiry_model->get(null, $student_id, $feetype_id, true);
        $student_data = $this->student_model->get($student_id);

        if ($row_count == 0) {
            $insert_id = $this->feeenquiry_model->add([
                'student_id' => $student_id,
                'feetype_id' => $feetype_id,
                'date' =>  date("d-m-Y"),
                'follow_up_date' => date("d-m-Y"),
                'assigned' => $agent_id,
                'status'  => 'active',
                'created_by' =>  $agent_id
            ]);
            $enquiry_id = $insert_id;
        } else {
            $row_result = $this->feeenquiry_model->get(null, $student_id, $feetype_id);
            $enquiry_id = $row_result[0]['id'];
        }

        $enquiry_result = $this->feeenquiry_model->get($enquiry_id);
        $data = [
            'enquiry_id' => $enquiry_id,
            'enquiry_data' => $enquiry_result[0],
            'student_data' => $student_data,
            'assigned_staff' => (!empty($enquiry_result[0]['assigned'])) ? $this->staff_model->get($enquiry_result[0]['assigned']) : '',
            'created_by_staff' => $this->staff_model->get($agent_id),
            'enquiry_status' => $this->feeenquiry_status
        ];

        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $data]));
    }

    public function follow_up_list($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $data = [
            'id' => $id,
            'follow_up_list' => $this->enquiry_model->getfollow_up_list($id)
        ];
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $data]));
    }

    public function fee_followup_list($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $data = [
            'id' => $id,
            'follow_up_list' => $this->feefollowup_model->getfollow_up_list($id)
        ];
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'data' => $data]));
    }

    public function follow_up_delete($follow_up_id = null, $enquiry_id = null)
    {


        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        /* =========================
       METHOD CHECK
    ========================== */
        if (!in_array($_SERVER['REQUEST_METHOD'], ['DELETE', 'POST'])) {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        /* =========================
       VALIDATION
    ========================== */
        if (empty($follow_up_id) || empty($enquiry_id)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'follow_up_id and enquiry_id are required'
                ]));
        }

        /* =========================
       CHECK RECORD EXISTS (OPTIONAL BUT GOOD)
    ========================== */
        $exists = $this->enquiry_model->getfollow_up_list($enquiry_id);

        if (empty($exists)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'No follow-up records found'
                ]));
        }

        /* =========================
       DELETE
    ========================== */
        $this->enquiry_model->delete_follow_up($follow_up_id);

        /* =========================
       GET UPDATED LIST
    ========================== */
        $follow_up_list = $this->enquiry_model->getfollow_up_list($enquiry_id);

        /* =========================
       RESPONSE
    ========================== */
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Follow-up deleted successfully',
                'data' => [
                    'follow_up_list' => $follow_up_list
                ]
            ]));
    }

    public function feefollow_up_delete($follow_up_id, $enquiry_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $this->feefollowup_model->delete_follow_up($follow_up_id);
        $data = ['follow_up_list' => $this->feefollowup_model->getfollow_up_list($enquiry_id)];
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => true, 'message' => $this->lang->line('delete_message'), 'data' => $data]));
    }

    public function editpost($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('source', $this->lang->line('source'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('follow_up_date', $this->lang->line('next_follow_up_date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'    => form_error('name'),
                'contact' => form_error('contact'),
                'source'  => form_error('source'),
                'date'    => form_error('date'),
                'follow_up_date'    => form_error('follow_up_date'),
            );

            return $this->output
                ->set_status_header(422)
                ->set_output(json_encode(['status' => 'fail', 'error' => $msg]));
        } else {
            $enquiry_update = array(
                'name'           => $input['name'],
                'contact'        => $input['contact'],
                'address'        => $input['address'] ?? '',
                'reference'      => $input['reference'] ?? '',
                'date'           => $this->safeDate($input['date']),
                'description'    => $input['description'] ?? '',
                'follow_up_date' => $this->safeDate($input['follow_up_date']),
                'note'           => $input['note'] ?? '',
                'source'         => $input['source'],
                'email'          => $input['email'] ?? '',
                'assigned'       => empty2null($input['assigned'] ?? ''),
                'class_id'       => empty2null($input['class'] ?? ''),
                'no_of_child'    => $input['no_of_child'] ?? 0,
            );
            $this->enquiry_model->enquiry_update($id, $enquiry_update);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('update_message')
                ]));
        }
    }

    public function change_status()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $id     = $input['id'] ?? null;
        $status = $input['status'] ?? null;

        if (!empty($id)) {
            $data = array('id' => $id, 'status' => $status);
            $this->enquiry_model->changeStatus($data);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message')
                ]));
        } else {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => 'ID is required'
                ]));
        }
    }

    public function fee_change_status()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: $this->input->post();
        if (!empty($input['id'])) {
            $this->feeenquiry_model->changeStatus(['id' => $input['id'], 'status' => $input['status']]);
            return $this->output->set_status_header(200)->set_output(json_encode(['status' => 'success', 'message' => $this->lang->line('success_message')]));
        }
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
    }

    public function check_number()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: $this->input->post();
        $phone_number = $input['phone_number'] ?? '';
        $check_number = $this->enquiry_model->check_number($phone_number);
        if (!empty($check_number)) {
            $msg = $this->lang->line('number_is_already_exists_and_name_is') . ' ' . $check_number['name'];
            return $this->output->set_status_header(200)->set_output(json_encode(['status' => 'success', 'exists' => true, 'message' => $msg]));
        }
        return $this->output->set_status_header(200)->set_output(json_encode(['status' => 'success', 'exists' => false]));
    }

    public function admissions_analysis_report()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $total_leads    = $this->enquiry_model->getTotalLeads()->total_leads ?? 0;
        $total_passive  = $this->enquiry_model->getTotalPassiveLeads()->total_passive ?? 0;
        $total_won      = $this->enquiry_model->getTotalWonLeads()->total_won ?? 0;
        $total_dead     = $this->enquiry_model->getTotalDeadLeads()->total_dead ?? 0;

        $data = [
            'total_leads'   => $total_leads,
            'total_passive' => $total_passive,
            'total_won'     => $total_won,
            'total_dead'    => $total_dead,
        ];

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }

    public function details($id, $status)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $data['source']       = $this->enquiry_model->getComplaintSource();
        $data['enquiry_type'] = $this->enquiry_model->get_enquiry_type();
        $data['Reference']    = $this->enquiry_model->get_reference();
        $data['class_list']   = $this->enquiry_model->getclasses();
        $data['enquiry_data'] = $this->enquiry_model->getenquiry_list($id, $status);
        $data['stff_list']    = $this->staff_model->get();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $data
            ]));
    }
}

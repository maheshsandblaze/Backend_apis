<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Leaverequest extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('media_storage');
        $this->load->model("staff_model");
        $this->load->model("leaverequest_model");
        $this->load->library('mailsmsconf');
        $this->load->library('form_validation');
        
        $this->contract_type    = $this->config->item('contracttype');
        $this->marital_status   = $this->config->item('marital_status');
        $this->staff_attendance = $this->config->item('staffattendance');
        $this->payroll_status   = $this->config->item('payroll_status');
        $this->payment_mode     = $this->config->item('payment_mode');
        $this->status           = $this->config->item('status');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input ?: [];
    }

    public function leaverequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Check privilege - assuming authenticated context
        // if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
        //     return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'fail', 'message' => 'Access Denied']));
        // }

        $leave_request = $this->leaverequest_model->staff_leave_request();
        $LeaveTypes    = $this->staff_model->getLeaveType();
        $staffRole     = $this->staff_model->getStaffRole();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'        => 'success',
                'leave_request' => $leave_request,
                'leavetype'     => $LeaveTypes,
                'staffrole'     => $staffRole,
                'status_list'   => $this->status
            ]));
    }

    public function countLeave($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $lid   = $input['lid'] ?? null;

        $alloted_leavetype = $this->leaverequest_model->allotedLeaveType($id);
        $data              = array();

        foreach ($alloted_leavetype as $key => $value) {
            $count_leaves = $this->leaverequest_model->countLeavesData($id, $value["leave_type_id"]);
            $allotted     = $value["alloted_leave"];
            $approved     = $count_leaves['approve_leave'] ?? 0;
            $available    = ($allotted == "") ? $approved : ($allotted - $approved);

            if ($available > 0) {
                $data[] = array(
                    'type'          => $value["type"],
                    'id'            => $value["leave_type_id"],
                    'alloted_leave' => $allotted,
                    'approve_leave' => $approved,
                    'available'     => $available,
                    'selected'      => ($lid == $value["leave_type_id"]) ? true : false
                );
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $data
            ]));
    }

    public function leaveStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input            = $this->_get_input();
        $leave_request_id = $input["leave_request_id"] ?? null;
        $status           = $input["status"] ?? null;
        $adminRemark      = $input["detailremark"] ?? null;

        if (!$leave_request_id || !$status) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Missing ID or Status']));
        }

        $data = array('status' => $status, 'admin_remark' => $adminRemark);
        $this->leaverequest_model->changeLeaveStatus($data, $leave_request_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('success_message')
            ]));
    }

    public function remove($id, $staff_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
        $row       = $this->leaverequest_model->get_staff_leave($id);
        
        if ($row && $row['document_file'] != '') {
            $this->media_storage->filedelete($row['document_file'], $uploaddir);
        }
        
        $this->leaverequest_model->leave_remove($id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('delete_message')
            ]));
    }

    public function leaveRecord()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $id    = $input["id"] ?? null;

        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'ID is required']));
        }

        $result = $this->staff_model->getLeaveRecord($id);
        
        if ($result) {
            $result->leavefrom    = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($result->leave_from));
            $result->date         = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($result->date));
            $result->leaveto      = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($result->leave_to));
            $result->days         = $this->dateDifference($result->leave_from, $result->leave_to);
            $result->leave_status = $this->lang->line($result->status);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode($result));
    }

    public function dateDifference($date_1, $date_2, $differenceFormat = '%a')
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval  = date_diff($datetime1, $datetime2);
        return $interval->format($differenceFormat) + 1;
    }

    public function addLeave()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('role', $this->lang->line('role'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('empname', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('applieddate', $this->lang->line('applied_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_from_date', $this->lang->line('leave_from_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_to_date', $this->lang->line('leave_to_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_type', $this->lang->line('available_leave'), 'trim|required|xss_clean');
        
        // Handling file upload callback manually or skipping for API if not provided
        if (isset($_FILES['userfile'])) {
             $this->form_validation->set_rules('userfile', $this->lang->line('file'), 'callback_handle_upload[userfile]');
        }

        if ($this->form_validation->run() == false) {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {
            $leavefrom    = date("Y-m-d", $this->customlib->datetostrtotime($input['leave_from_date']));
            $leaveto      = date("Y-m-d", $this->customlib->datetostrtotime($input['leave_to_date']));
            $applied_by   = $this->customlib->getStaffID(); // Caution: Public controller might not have this session data if stateless
            $leave_days   = $this->dateDifference($leavefrom, $leaveto);
            
            $staff_id     = $input["empname"];
            $leavetype    = $input["leave_type"];
            $my_laeve     = $this->leaverequest_model->myallotedLeaveType($staff_id, $leavetype);
            $total_remain = ($my_laeve['alloted_leave'] ?? 0) - ($my_laeve['total_applied'] ?? 0);

            if ($total_remain >= $leave_days) {
                
                $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                     // Handle error silently or log
                }
                
                $document = '';
                if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {
                    $document = $this->media_storage->fileupload("userfile", $uploaddir);
                }

                $request_id   = $input["leaverequestid"] ?? null;
                $applied_date = $input["applieddate"];
                $reason       = $input["reason"] ?? '';
                $status       = $input["addstatus"] ?? 'pending';
                $remark       = $input["remark"] ?? '';

                if (!empty($request_id)) {
                    $data = array(
                        'id'              => $request_id,
                        'staff_id'        => $staff_id,
                        'date'            => date('Y-m-d', $this->customlib->datetostrtotime($applied_date)),
                        'leave_type_id'   => $leavetype,
                        'leave_days'      => $leave_days,
                        'leave_from'      => $leavefrom,
                        'leave_to'        => $leaveto,
                        'employee_remark' => $reason,
                        'status'          => $status,
                        'admin_remark'    => $remark,
                        'applied_by'      => $applied_by,
                    );
                    if ($document) $data['document_file'] = $document;
                } else {
                    $data = array(
                        'staff_id'        => $staff_id,
                        'date'            => date("Y-m-d", $this->customlib->datetostrtotime($applied_date)),
                        'leave_days'      => $leave_days,
                        'leave_type_id'   => $leavetype,
                        'leave_from'      => $leavefrom,
                        'leave_to'        => $leaveto,
                        'employee_remark' => $reason,
                        'status'          => $status,
                        'admin_remark'    => $remark,
                        'applied_by'      => $applied_by,
                        'document_file'   => $document
                    );
                }

                $this->leaverequest_model->addLeaveRequest($data);
                
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
                        'status' => 'fail',
                        'message' => $this->lang->line('selected_leave_days') . " > " . $this->lang->line('available_leaves')
                    ]));
            }
        }
    }

    public function add_staff_leave()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('applieddate', $this->lang->line('applied_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_from_date', $this->lang->line('leave_from_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_to_date', $this->lang->line('leave_to_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_type', $this->lang->line('available_leave'), 'trim|required|xss_clean');
        
        if (isset($_FILES['userfile'])) {
             $this->form_validation->set_rules('userfile', $this->lang->line('file'), 'callback_handle_upload[userfile]');
        }

        if ($this->form_validation->run() == false) {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {
            $leavefrom = date("Y-m-d", $this->customlib->datetostrtotime($input['leave_from_date']));
            $leaveto   = date("Y-m-d", $this->customlib->datetostrtotime($input['leave_to_date']));
            
            $userdata     = $this->customlib->getUserData();
            $staff_id     = $userdata["id"];
            $applied_by   = $this->customlib->getStaffID(); // Assuming session or correctly passed
            
            $leavetype    = $input["leave_type"];
            $leave_days   = $this->dateDifference($leavefrom, $leaveto);
            $my_laeve     = $this->leaverequest_model->myallotedLeaveType($staff_id, $leavetype);
            $total_remain = ($my_laeve['alloted_leave'] ?? 0) - ($my_laeve['total_applied'] ?? 0);

            if ($total_remain >= $leave_days) {
                
                $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    // Handle error
                }
                
                $document = '';
                if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {
                    $document = $this->media_storage->fileupload("userfile", $uploaddir);
                }

                $request_id   = $input["leaverequestid"] ?? null;
                $applied_date = $input["applieddate"];
                $reason       = $input["reason"] ?? '';
                $status       = 'pending';
                $remark       = ''; // Staff cannot add admin remark

                if (!empty($request_id)) {
                    $data = array(
                        'id'              => $request_id,
                        'staff_id'        => $staff_id,
                        'date'            => date('Y-m-d', $this->customlib->datetostrtotime($applied_date)),
                        'leave_type_id'   => $leavetype,
                        'leave_days'      => $leave_days,
                        'leave_from'      => $leavefrom,
                        'leave_to'        => $leaveto,
                        'employee_remark' => $reason,
                        'status'          => $status,
                        'admin_remark'    => $remark,
                        'applied_by'      => $applied_by,
                    );
                    if ($document) $data['document_file'] = $document;
                } else {
                    $data = array(
                        'staff_id'        => $staff_id,
                        'date'            => date("Y-m-d", $this->customlib->datetostrtotime($applied_date)),
                        'leave_days'      => $leave_days,
                        'leave_type_id'   => $leavetype,
                        'leave_from'      => $leavefrom,
                        'leave_to'        => $leaveto,
                        'employee_remark' => $reason,
                        'status'          => $status,
                        'admin_remark'    => $remark,
                        'applied_by'      => $applied_by,
                        'document_file'   => $document
                    );
                }

                $this->leaverequest_model->addLeaveRequest($data);

                // Send email
                $message_title = $this->lang->line('staff_leave');
                $message       = $reason . '<br>' . $this->lang->line('apply_date') . ': ' . $applied_date . '<br>' . $this->lang->line('from_date') . ': ' . $leavefrom . '<br>' . $this->lang->line('to_date') . ': ' . $leaveto;
                
                if (isset($this->sch_setting_detail->staff_notification_email)) {
                    $this->mailer->send_mail($this->sch_setting_detail->staff_notification_email, $message_title, $message, $_FILES, '');
                }

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
                        'message' => $this->lang->line('selected_leave_days') . " > " . $this->lang->line('available_leaves')
                    ]));
            }
        }
    }

    public function handle_upload($str, $var)
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));

            if ($files = filesize($_FILES[$var]['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_extension_error_uploading_image'));
                return false;
            }
            return true;
        }
        return true;
    }
    
    public function downloadleaverequestdoc($staff_id, $id)
    {
        $doc = $this->leaverequest_model->get_staff_leave($id);
        if ($doc) {
            $this->media_storage->filedownload($doc['document_file'], "./uploads/staff_documents/$staff_id");
        } else {
            // Should probably return 404 but filedownload often forces headers. 
            // Just leaving as is for minimal impact on legacy behavior if called directly.
        }
    }

}

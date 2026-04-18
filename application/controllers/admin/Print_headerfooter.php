<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Print_headerfooter extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
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

        $result = $this->setting_model->get_printheader();
        
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'result' => $result
            ]));
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $type = $input['type'] ?? null;
        if (!$type) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Type is required']));
        }

        $is_required = $this->setting_model->check_haederimage($type);
        $this->form_validation->set_rules('header_image', $this->lang->line('header_image'), 'trim|xss_clean|callback_handle_upload[' . $is_required . ']');

        $message_key = "";
        if ($type == 'staff_payslip') {
            $message_key = 'message';
        } else if ($type == 'online_admission_receipt') {
            $message_key = "admission_message";
        } else if ($type == 'online_exam') {
            $message_key = 'online_exam_message';
        } else {
            $message_key = 'message1';
        }
        
        $this->form_validation->set_rules($message_key, $this->lang->line('footer_content'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {
            $img_name = null;
            if (isset($_FILES["header_image"]) && !empty($_FILES['header_image']['name'])) {

                if ($type == 'student_receipt') {
                    $img_name = $this->media_storage->fileupload("header_image", "./uploads/print_headerfooter/student_receipt/");
                    $old_img = $this->setting_model->unlink_receiptheader();
                    if ($old_img != '') {
                        $this->media_storage->filedelete($old_img, "uploads/print_headerfooter/student_receipt/");
                    }
                } else if ($type == 'online_admission_receipt') {
                    $img_name = $this->media_storage->fileupload("header_image", "./uploads/print_headerfooter/online_admission_receipt/");
                    $old_img = $this->setting_model->unlink_onlinereceiptheader();
                    if ($old_img != '') {
                        $this->media_storage->filedelete($old_img, "uploads/print_headerfooter/online_admission_receipt/");
                    }
                } else if ($type == 'online_exam') {
                    $img_name = $this->media_storage->fileupload("header_image", "./uploads/print_headerfooter/online_exam/");
                    $row = $this->setting_model->get_onlineexamheader_raw(); // Added this helper
                    if ($row && $row['header_image'] != '') {
                        $this->media_storage->filedelete($row['header_image'], "uploads/print_headerfooter/online_exam/");
                    }
                } else {
                    $img_name = $this->media_storage->fileupload("header_image", "./uploads/print_headerfooter/staff_payslip/");
                    $old_img = $this->setting_model->unlink_payslipheader();
                    if ($old_img != '') {
                        $this->media_storage->filedelete($old_img, "uploads/print_headerfooter/staff_payslip/");
                    }
                }
            }

            $data = array(
                'print_type'     => $type,
                'footer_content' => $input[$message_key],
                'created_by'     => $this->customlib->getStaffID()
            );
            
            if ($img_name) {
                $data['header_image'] = $img_name;
            }

            $this->setting_model->add_printheader($data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message')
                ]));
        }
    }

    public function handle_upload($str, $is_required)
    {
        $result = $this->filetype_model->get();
        if (isset($_FILES["header_image"]) && !empty($_FILES['header_image']['name']) && $_FILES["header_image"]["size"] > 0) {

            $file_type = $_FILES["header_image"]['type'];
            $file_size = $_FILES["header_image"]["size"];
            $file_name = $_FILES["header_image"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $_FILES['header_image']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mtype, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }

            if ($file_size > $result->image_size) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->image_size / 1048576, 2) . " MB");
                return false;
            }

            return true;
        } else {
            if ($is_required == 0) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('please_choose_a_file_to_upload'));
                return false;
            } else {
                return true;
            }
        }
    }

}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentdairy extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('media_storage');
        $this->load->model("studentdairy_model");
        $this->load->model("staff_model");
        $this->load->model("classteacher_model");
        $this->config->load("app-config");
        $this->load->library('mailsmsconf');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->role;
        $this->search_type        = $this->customlib->get_searchtype();
    }

    // public function index()
    // {
    //     if (!$this->rbac->hasPrivilege('studentdairy', 'can_view')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'studentdairy');
    //     $this->session->set_userdata('sub_menu', 'studentdairy');
    //     $data["title"] = "Create studentdairy";

    //     $data['classlist'] = $this->class_model->get();

    //     $userdata                 = $this->customlib->getUserData();
    //     $carray                   = array();
    //     $data['class_id']         = "";
    //     $data['section_id']       = "";
    //     $data['subject_group_id'] = "";
    //     $data['subject_id']       = "";

    //     $this->load->view("layout/header", $data);
    //     $this->load->view("studentdairy/studentdairylist", $data);
    //     $this->load->view("layout/footer", $data);
    // }

    // public function searchvalidation()
    // {
    //     $class_id         = $this->input->post('class_id');
    //     $section_id       = $this->input->post('section_id');
    //     // $subject_group_id = $this->input->post('subject_group_id');
    //     // $subject_id       = $this->input->post('subject_id');

    //     $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     if ($this->form_validation->run() == false) {
    //         $error = array();

    //         $error['class_id'] = form_error('class_id');
    //         $array             = array('status' => 0, 'error' => $error);
    //         echo json_encode($array);
    //     } else {
    //         $class_id   = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');

    //         $params = array('class_id' => $class_id, 'section_id' => $section_id);
    //         $array  = array('status' => 1, 'error' => '', 'params' => $params);
    //         echo json_encode($array);
    //     }
    // }
    
    public function studentDairySearchApi()
    {
        // CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
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
            $input = $this->input->post(NULL);
        }
    
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('class_id', 'Class', 'required|trim');
    
        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'class_id' => form_error('class_id')
                    ]
                ]));
        }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'params' => [
                    'class_id'   => $input['class_id'],
                    'section_id' => $input['section_id'] ?? null
                ]
            ]));
    }
    
    
    public function studentDairyListApi()
    {
        // CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
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
            $input = $this->input->post(NULL);
        }
    
        $class_id   = $input['class_id'] ?? null;
        $section_id = $input['section_id'] ?? null;
    
        $studentdairylist = $this->studentdairy_model
            ->search_dtstudentdairy($class_id, $section_id);
    
        $studentdairy = json_decode($studentdairylist);
    
        $rows = [];
    
        if (!empty($studentdairy->data)) {
            foreach ($studentdairy->data as $item) {
    
                // $actions = [
                //     'can_view'   => $this->rbac->hasPrivilege('homework_evaluation', 'can_view'),
                //     'can_edit'   => $this->rbac->hasPrivilege('studentdairy', 'can_edit'),
                //     'can_delete' => $this->rbac->hasPrivilege('studentdairy', 'can_delete'),
                // ];
    
                $rows[] = [
                    'id'          => $item->id,
                    'class'       => $item->class,
                    'section'     => $item->section,
                    'date'        => $this->customlib->dateformat($item->date),
                    'assigned_by' => $item->assigned_by
                ];
            }
        }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'draw'   => intval($studentdairy->draw),
                'total'  => intval($studentdairy->recordsTotal),
                'data'   => $rows
            ]));
    }

    


    // public function dtstudentdairylist()
    // {
    //     $currency_symbol  = $this->customlib->getSchoolCurrencyFormat();
    //     $class_id         = $this->input->post('class_id');
    //     $section_id       = $this->input->post('section_id');


    //     $carray       = array();
    //     $studentdairylist = $this->studentdairy_model->search_dtstudentdairy($class_id, $section_id);
    //     $studentdairy = json_decode($studentdairylist);
    //     // echo "<pre>";
    //     // print_r($studentdairy);exit;

    //     $getStaffRole       = $this->customlib->getStaffRole();
    //     $staffrole          = json_decode($getStaffRole);
    //     $superadmin_visible = $this->customlib->superadmin_visible();

    //     $dt_data = array();
    //     if (!empty($studentdairy->data)) {
    //         foreach ($studentdairy->data as $studentdairy_key => $studentdairylist) {

    //             $editbtn    = '';
    //             $deletebtn  = '';
    //             $viewbtn    = '';
    //             $collectbtn = '';

    //             if ($this->rbac->hasPrivilege('homework_evaluation', 'can_view')) {
    //                 $viewbtn = "<a onclick='evaluation(" . '"' . $studentdairylist->id . '"' . ")' title=''  data-toggle='tooltip'  data-original-title=" . $this->lang->line('evaluation') . " class='btn btn-default btn-xs'  title='" . $this->lang->line('evaluation') . "' data-toggle='tooltip'><i class='fa fa-reorder'></i></a>";
    //             }



    //             if ($this->rbac->hasPrivilege('studentdairy', 'can_edit')) {
    //                 $editbtn = "<a  class='btn btn-default btn-xs modal_form'  data-toggle='tooltip'   data-method_call='edit' data-original-title='" . $this->lang->line('edit') . "' data-record_id=" . $studentdairylist->id . " ><i class='fa fa-pencil'></i></a>";
    //             }

    //             if ($this->rbac->hasPrivilege('studentdairy', 'can_delete')) {
    //                 $collectbtn = "<a onclick='return confirm(" . '"' . $this->lang->line('delete_confirm') . '"' . "  )' href='" . base_url() . "studentdairy/delete/" . $studentdairylist->id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip'  title='" . $this->lang->line('delete') . "' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-remove'></i></a>";
    //             }


    //             $row   = array();
    //             $row[] = $studentdairylist->class;
    //             $row[] = $studentdairylist->section;
    //             $row[] = $this->customlib->dateformat($studentdairylist->date);
    //             $row[] = $studentdairylist->assigned_by;


    //             $row[]     = $viewbtn . '' . $editbtn . '' . $collectbtn;
    //             $dt_data[] = $row;
    //         }
    //     }
    //     $json_data = array(
    //         "draw"            => intval($studentdairy->draw),
    //         "recordsTotal"    => intval($studentdairy->recordsTotal),
    //         "recordsFiltered" => intval($studentdairy->recordsFiltered),
    //         "data"            => $dt_data,
    //     );
    //     echo json_encode($json_data);
    // }

    public function closestudentdairylist()
    {
        $currency_symbol  = $this->customlib->getSchoolCurrencyFormat();
        $class_id         = $this->input->post('class_id');
        $section_id       = $this->input->post('section_id');
        $subject_group_id = $this->input->post('subject_group_id');
        $subject_id       = $this->input->post('subject_id');

        $userdata     = $this->customlib->getUserData();
        $carray       = array();
        $studentdairylist = $this->studentdairy_model->search_closestudentdairy($class_id, $section_id, $subject_group_id, $subject_id);

        $studentdairy           = json_decode($studentdairylist);
        $getStaffRole       = $this->customlib->getStaffRole();
        $staffrole          = json_decode($getStaffRole);
        $superadmin_visible = $this->customlib->superadmin_visible();

        $dt_data = array();
        if (!empty($studentdairy->data)) {

            foreach ($studentdairy->data as $studentdairy_key => $studentdairylist) {

                $editbtn    = '';
                $deletebtn  = '';
                $viewbtn    = '';
                $collectbtn = '';

                if ($this->rbac->hasPrivilege('studentdairy_evaluation', 'can_view')) {
                    $viewbtn = "<a onclick='evaluation(" . '"' . $studentdairylist->id . '"' . "  )' title=''  data-toggle='tooltip'  data-original-title=" . $this->lang->line('evaluation') . " class='btn btn-default btn-xs'  title='" . $this->lang->line('evaluation') . "' data-toggle='tooltip'><i class='fa fa-reorder'></i></a>";
                }

                if ($this->rbac->hasPrivilege('studentdairy', 'can_edit')) {
                    $editbtn = "<a  class='btn btn-default btn-xs modal_form'  data-toggle='tooltip'   data-method_call='edit' data-original-title='" . $this->lang->line('edit') . "' data-record_id=" . $studentdairylist->id . " ><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('studentdairy', 'can_delete')) {
                    $collectbtn = "<a onclick='return confirm(" . '"' . $this->lang->line('delete_confirm') . '"' . "  )' href='" . base_url() . "studentdairy/delete/" . $studentdairylist->id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip'  title='" . $this->lang->line('delete') . "' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-remove'></i></a>";
                }

                $subject_code = '';
                if ($studentdairylist->subject_code) {
                    $subject_code = ' (' . $studentdairylist->subject_code . ')';
                }

                $row   = array();
                $row[] = '<input type="checkbox" id="delete_studentdairy" name="delete_studentdairy[]" value="' . $studentdairylist->id . '">';
                $row[] = $studentdairylist->class;
                $row[] = $studentdairylist->section;
                $row[] = $studentdairylist->name;
                $row[] = $studentdairylist->subject_name . ' ' . $subject_code;

                if ($studentdairylist->studentdairy_date != null && $studentdairylist->studentdairy_date != '0000-00-00') {
                    $row[] = $this->customlib->dateformat($studentdairylist->studentdairy_date);
                } else {
                    $row[] = "";
                }
                $row[] = $this->customlib->dateformat($studentdairylist->submit_date);

                $evl_date = "";
                if ($studentdairylist->evaluation_date != "0000-00-00") {

                    $row[] =  $this->customlib->dateformat($studentdairylist->evaluation_date);
                } else {
                    $row[] = "";
                }

                if ($staffrole->id != 7) {
                    if ($superadmin_visible == 'disabled') {

                        if ($studentdairylist->role_id == 7) {
                            $row[] = '';
                        } else {
                            $row[] = $studentdairylist->staff_name . ' ' . $studentdairylist->staff_surname . ' (' . $studentdairylist->staff_employee_id . ')';
                        }
                    } else {
                        $row[] = $studentdairylist->staff_name . ' ' . $studentdairylist->staff_surname . ' (' . $studentdairylist->staff_employee_id . ')';
                    }
                } else {
                    $row[] = $studentdairylist->staff_name . ' ' . $studentdairylist->staff_surname . ' (' . $studentdairylist->staff_employee_id . ')';
                }

                $row[]     = $viewbtn . '' . $editbtn . '' . $collectbtn;
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($studentdairy->draw),
            "recordsTotal"    => intval($studentdairy->recordsTotal),
            "recordsFiltered" => intval($studentdairy->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function studentdairy_docs($id)
    {
        $docs = $this->studentdairy_model->get_studentdairyDocByid($id);
        $docs = json_decode($docs);

        $dt_data = array();
        if (!empty($docs->data)) {

            foreach ($docs->data as $key => $value) {

                if (!empty($value->docs)) {
                    $doc = '<a class="btn btn-default btn-xs" href="' . base_url() . 'studentdairy/assigmnetDownload/' . $value->docs . '"   data-toggle="tooltip" data-original-title=' . $this->lang->line("download") . '>
                <i class="fa fa-download"></i></a>';
                } else {
                    $doc = "";
                }

                if (!empty($value->message)) {
                    $message = $value->message;
                } else {
                    $message = '';
                }

                $row       = array();
                $row[]     = $this->customlib->getFullName($value->firstname, $value->middlename, $value->lastname, $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname) . " (" . $value->admission_no . ")";
                $row[]     = $message;
                $row[]     = $doc;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($docs->draw),
            "recordsTotal"    => intval($docs->recordsTotal),
            "recordsFiltered" => intval($docs->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    // public function create()
    // {
    //     if (!$this->rbac->hasPrivilege('studentdairy', 'can_add')) {
    //         access_denied();
    //     }

    //     $data["title"]      = "Create studentdairy";
    //     $class              = $this->class_model->get();
    //     $data['classlist']  = $class;
    //     $data['class_id']   = "";
    //     $data['section_id'] = "";
    //     $userdata           = $this->customlib->getUserData();
    //     // $this->form_validation->set_rules('record_id', $this->lang->line('record_id'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('modal_class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('modal_section_id', $this->lang->line('section'), 'trim|required|xss_clean');

    //     $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

    //     $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
    //     if ($this->form_validation->run() == false) {

    //         $msg = array(
    //             'modal_class_id'         => form_error('modal_class_id'),
    //             'modal_section_id'       => form_error('modal_section_id'),

    //             'submit_date'            => form_error('submit_date'),
    //             'description'            => form_error('description'),

    //         );

    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {

    //         $record_id  = $this->input->post('record_id');


    //         $session_id = $this->setting_model->getCurrentSession();
    //         $staff_record = $this->staff_model->get($this->customlib->getStaffID());

    //         $collected_by    = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";


    //         // print_r($_POST);exit;

    //         $data       = array(

    //             'class_id'                 => $this->input->post("modal_class_id"),
    //             'section_id'               => $this->input->post("modal_section_id"),
    //             'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
    //             'description'              => $this->input->post("description"),
    //             'assigned_by'               =>  $collected_by,
    //             'session_id' => $session_id



    //         );


    //         if ($record_id > 0) {
    //             $homeworklist = $this->studentdairy_model->get($record_id);

    //             if (isset($_FILES["userfile"]) && $_FILES['userfile']['name'] != '' && (!empty($_FILES['userfile']['name']))) {
    //                 $img_name = $this->media_storage->fileupload("userfile", "./uploads/homework/");
    //             } else {
    //                 $img_name = $homeworklist['document'];
    //             }

    //             $data['document'] = $img_name;

    //             if (isset($_FILES["userfile"]) && $_FILES['userfile']['name'] != '' && (!empty($_FILES['userfile']['name']))) {
    //                 $this->media_storage->filedelete($homeworklist['document'], "uploads/homework");
    //             }
    //         } else {
    //             $img_name         = $this->media_storage->fileupload("userfile", "./uploads/homework/");
    //             $data['document'] = $img_name;
    //         }

    //         if ($record_id > 0) {
    //             $data['id'] = $record_id;
    //         }

    //         // echo "<pre>";
    //         // print_r($data);
    //         // exit;


    //         $id = $this->studentdairy_model->add($data);


    //         $msg   = $this->lang->line('success_message');
    //         $array = array('status' => 'success', 'error' => '', 'message' => $msg);
    //     }

    //     echo json_encode($array);
    // }
    
    
    public function createStudentDairy()
    {
        // CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // Validation rules
        $this->form_validation->set_rules('class_id', 'Class', 'required|trim|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', 'Date', 'required|trim|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'required|trim|xss_clean');
    
        if ($this->form_validation->run() == false) {
    
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'class_id'   => form_error('class_id'),
                        'section_id' => form_error('section_id'),
                        'date'       => form_error('date'),
                        'description'=> form_error('description')
                    ]
                ]));
        }
    
        // Inputs
        $record_id = $this->input->post('record_id');
        $session_id = $this->setting_model->getCurrentSession();
        // $staff = $this->staff_model->get($this->customlib->getStaffID());
    
        // $assigned_by = $this->customlib->getAdminSessionUserName() .
        //     " (" . $staff['employee_id'] . ")";
        
        $assigned_by = $this->input->post('assigned_by');
    
        $data = [
            'class_id'    => $this->input->post('class_id'),
            'section_id'  => $this->input->post('section_id'),
            'date'        => date(
                'Y-m-d',
                strtotime($this->input->post('date'))
            ),
            'description' => $this->input->post('description'),
            'assigned_by' => $assigned_by,
            'session_id'  => $session_id
        ];
    
        // File upload
        if (!empty($_FILES['userfile']['name'])) {
            $file_name = $this->media_storage->fileupload(
                'userfile',
                '../uploads/homework/'
            );
            $data['document'] = $file_name;
    
            // Delete old file on update
            if (!empty($record_id)) {
                $old = $this->studentdairy_model->get($record_id);
                if (!empty($old['document'])) {
                    $this->media_storage->filedelete(
                        $old['document'],
                        'uploads/homework'
                    );
                }
            }
        }
    
        // Update case
        if (!empty($record_id)) {
            $data['id'] = $record_id;
        }
    
        // Save
        $id = $this->studentdairy_model->add($data);
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Student diary saved successfully',
                'id' => $id
            ]));
    }

    

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {

            $file_type = $_FILES["userfile"]['type'];
            $file_size = $_FILES["userfile"]["size"];
            $file_name = $_FILES["userfile"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES['userfile']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    // public function getRecord()
    // {
    //     if (!$this->rbac->hasPrivilege('studentdairy', 'can_edit')) {
    //         access_denied();
    //     }
    //     $id             = $this->input->post('id');
    //     $result         = $this->studentdairy_model->get($id);
    //     $data["result"] = $result;
    //     // echo "<pre>";
    //     // print_r($result);exit;

    //     echo json_encode($result);
    // }

    // public function edit()
    // {
    //     if (!$this->rbac->hasPrivilege('studentdairy', 'can_edit')) {
    //         access_denied();
    //     }
    //     $id            = $this->input->post("studentdairyid");
    //     $data["title"] = "Edit studentdairy";

    //     $class              = $this->class_model->get();
    //     $data['classlist']  = $class;
    //     $result             = $this->studentdairy_model->get($id);
    //     $data["result"]     = $result;
    //     $data['class_id']   = $result["class_id"];
    //     $data['section_id'] = $result["section_id"];

    //     $data["id"]         = $id;
    //     $userdata           = $this->customlib->getUserData();
    //     $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {
    //         $msg = array(
    //             'class_id'      => form_error('class_id'),
    //             'section_id'    => form_error('section_id'),
    //             'date' => form_error('date'),
    //             'description'   => form_error('description'),
    //         );
    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {

    //         $collected_by    = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";




    //         $data = array(
    //             'id'            => $id,
    //             'class_id'      => $this->input->post("class_id"),
    //             'section_id'    => $this->input->post("section_id"),
    //             'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
    //             'description'   => $this->input->post("description"),
    //             'assigned_by' => $collected_by
    //         );

    //         // echo "<pre>";
    //         // print_r($data);exit;

    //         $this->studentdairy_model->add($data);
    //         $msg   = $this->lang->line('update_message');
    //         $array = array('status' => 'success', 'error' => '', 'message' => $msg);
    //     }

    //     echo json_encode($array);
    // }
    
    public function getRecord()
    {
        // CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->apiResponse(false, 'Method Not Allowed', 405);
        }
    
        $input = json_decode(file_get_contents("php://input"), true);
        $id    = $input['id'] ?? null;
    
        if (!$id) {
            return $this->apiResponse(false, 'ID is required', 400);
        }
    
        $result = $this->studentdairy_model->get($id);
    
        if (!$result) {
            return $this->apiResponse(false, 'Record not found', 404);
        }
    
        return $this->apiResponse(true, $result);
    }
    
    
    public function edit()
    {
        // CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->apiResponse(false, 'Method Not Allowed', 405);
        }
    
        $input = json_decode(file_get_contents("php://input"), true);
    
        // Validation
        if (
            empty($input['id']) ||
            empty($input['class_id']) ||
            empty($input['section_id']) ||
            empty($input['date']) ||
            empty($input['description'])
        ) {
            return $this->apiResponse(false, 'All fields are required', 400);
        }
    
        // Date conversion (prevents 1970-01-01 issue)
        $timestamp = strtotime($input['date']);
        if (!$timestamp) {
            return $this->apiResponse(false, 'Invalid date format', 400);
        }
    
        $date = date('Y-m-d', $timestamp);
    
        // Get staff (API-safe)
        // $staff_id = $this->customlib->getStaffID()
        //     ?? $this->input->get_request_header('staff-id');
    
        // $staff = $this->staff_model->get($staff_id);
    
        // $assigned_by = $staff
        //     ? $staff['name'] . ' (' . $staff['employee_id'] . ')'
        //     : 'System';
        
        $assigned_by = $input['assigned_by'];
    
        $data = [
            'id'          => $input['id'],
            'class_id'    => $input['class_id'],
            'section_id'  => $input['section_id'],
            'date'        => $date,
            'description' => $input['description'],
            'assigned_by' => $assigned_by
        ];
    
        $this->studentdairy_model->add($data);
    
        return $this->apiResponse(true, 'Student diary updated successfully');
    }



    // public function delete($id)
    // {
    //     if (!$this->rbac->hasPrivilege('studentdairy', 'can_delete')) {
    //         access_denied();
    //     }

    //     if (!empty($id)) {
    //         $row = $this->studentdairy_model->get($id);


    //         $this->studentdairy_model->delete($id);
    //         redirect("studentdairy");
    //     }
    // }
    
    
    public function delete()
    {
        // CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        $input = json_decode(file_get_contents("php://input"), true);
        $id    = $input['id'] ?? null;
    
        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'ID is required'
                ]));
        }
    
        $row = $this->studentdairy_model->get($id);
    
        if (!$row) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Record not found'
                ]));
        }
    
        $this->studentdairy_model->delete($id);
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Student diary deleted successfully'
            ]));
    }

    
    private function apiResponse($status, $data = [], $code = 200)
    {
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => $status,
                'data'   => $data
            ]));
    }


    public function download($id)
    {
        $studentdairy = $this->studentdairy_model->get($id);
        $this->media_storage->filedownload($studentdairy['document'], "./uploads/homework");
    }

    public function evaluation($id)
    {
        if (!$this->rbac->hasPrivilege('studentdairy', 'can_view')) {
            access_denied();
        }





        $data["title"]        = "studentdairy view";
        $data["evaluated_by"] = "";
        $result               = $this->studentdairy_model->getRecord($id);

        $class_id      = $result["class_id"];
        $section_id    = $result["section_id"];

        // $data["studentlist"] = $this->studentdairy_model->getStudents($id);
        $data["result"]      = $result;
        $data['sch_setting'] = $this->setting_model->getSetting();



        $this->load->view("studentdairy/evaluation_modal", $data);
    }

    public function add_evaluation()
    {
        if (!$this->rbac->hasPrivilege('studentdairy_evaluation', 'can_add')) {
            access_denied();
        }

        $userdata = $this->customlib->getUserData();
        $this->form_validation->set_rules('evaluation_date', $this->lang->line('evaluation_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('student_list[]', $this->lang->line('student_name'), 'trim|required|xss_clean');

        $students       = $this->input->post("student_list");
        $marks          = $this->input->post("marks");

        $studentdairyresult = $this->studentdairy_model->getRecord($this->input->post("studentdairy_id"));

        if (!empty($students)) {
            foreach ($students as $std_key => $std_value) {

                $marks1 = $marks[$std_key];

                if ($studentdairyresult['marks'] < $marks1) {
                    $this->form_validation->set_rules('marks', $this->lang->line('marks'), array('valid_marks', array('check_valid_marks', array($this->studentdairy_model, 'check_valid_marks'))));
                }
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'evaluation_date' => form_error('evaluation_date'),
                'student_list[]'  => form_error('student_list[]'),
                'marks'           => form_error('marks'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $insert_prev  = array();
            $insert_array = array();
            $update_array = array();
            $studentdairy_id  = $this->input->post("studentdairy_id");
            $students     = $this->input->post("student_list");
            $marks        = $this->input->post("marks");
            $note         = $this->input->post("note");
            $student_id   = $this->input->post("student_id");

            foreach ($students as $std_key => $std_value) {

                $newmarks = NULL;
                if ($marks[$std_key]) {
                    $newmarks = $marks[$std_key];
                }

                if ($std_value == 0) {
                    $insert_array[] = array(

                        'student_session_id' => $std_key,
                        'note'               => $note[$std_key],
                        'marks'              => $newmarks,
                        'student_id'         => $student_id[$std_key],
                        'status'             => 'completed',
                    );
                } else {
                    $insert_prev[] = $std_value;

                    $update_array[$std_value][] = array(
                        'note'               => $note[$std_key],
                        'marks'              => $newmarks,
                        'student_session_id' => $std_key,
                    );
                }
            }

            $evaluation_date = $this->customlib->dateFormatToYYYYMMDD($this->input->post('evaluation_date'));
            $evaluated_by    = $this->customlib->getStaffID();
            $this->studentdairy_model->addEvaluation($insert_prev, $insert_array, $studentdairy_id, $evaluation_date, $evaluated_by, $update_array);
            $msg   = $this->lang->line('evaluation_completed_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function evaluation_report()
    {
        if (!$this->rbac->hasPrivilege('homehork_evaluation_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'studentdairy/studentdairyreport');
        $this->session->set_userdata('subsub_menu', 'studentdairy/evaluation_report');

        $class                    = $this->class_model->get();
        $data['classlist']        = $class;
        $userdata                 = $this->customlib->getUserData();
        $carray                   = array();
        $data['class_id']         = $class_id         = "";
        $data['section_id']       = $section_id       = "";
        $data['subject_id']       = $subject_id       = "";
        $data['subject_group_id'] = $subject_group_id = "";

        $class_id                 = $this->input->post("class_id");
        $section_id               = $this->input->post("section_id");
        $subject_group_id         = $this->input->post("subject_group_id");
        $subject_id               = $this->input->post("subject_id");
        $data['class_id']         = $class_id;
        $data['section_id']       = $section_id;
        $data['subject_group_id'] = $subject_group_id;
        $data['subject_id']       = $subject_id;
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_group_id', $this->lang->line('subject_group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['resultlist'] = array();
            $data["report"]     = array();
        } else {
            $data['resultlist'] = $this->studentdairy_model->search_studentdairy($class_id, $section_id, $subject_group_id, $subject_id);

            foreach ($data['resultlist'] as $key => $value) {
                $report                       = $this->count_percentage($value["id"], $value["class_id"], $value["section_id"]);
                $data["report"][$value['id']] = $report;
            }
        }

        $this->load->view("layout/header");
        $this->load->view("studentdairy/studentdairy_evaluation", $data);
        $this->load->view("layout/footer");
    }

    public function getreport($id = 1)
    {

        $result = $this->studentdairy_model->getEvaluationReport($id);
        if (!empty($result)) {
            $data["result"]       = $result;
            $class_id             = $result[0]["class_id"];
            $section_id           = $result[0]["section_id"];
            $create_data          = $this->staff_model->get($result[0]["created_by"]);
            $eval_data            = $this->staff_model->get($result[0]["evaluated_by"]);
            $created_by           = $create_data["name"] . " " . $create_data["surname"];
            $evaluated_by         = $eval_data["name"] . " " . $eval_data["surname"];
            $data["created_by"]   = $created_by;
            $data["evaluated_by"] = $evaluated_by;
            $studentlist          = $this->studentdairy_model->getStudents($class_id, $section_id);
            $data["studentlist"]  = $studentlist;
            $this->load->view("studentdairy/evaluation_report", $data);
        } else {
            echo "<div class='row'><div class='col-md-12'><br/><div class='alert alert-info'>" . $this->lang->line('no_record_found') . "</div></div></div>";
        }
    }

    public function count_percentage($id, $class_id, $section_id)
    {
        $data               = array();
        $count_students     = $this->studentdairy_model->count_students($class_id, $section_id);
        $count_evalstudents = $this->studentdairy_model->count_evalstudents($id, $class_id, $section_id);
        if ($count_students > 0) {
            $total_students     = $count_students;
            $total_evalstudents = $count_evalstudents['total'];
            $count_percentage   = ($total_evalstudents / $total_students) * 100;
            $data["total"]      = $total_students;
            $data["completed"]  = $total_evalstudents;
            $data["percentage"] = round($count_percentage, 2);
        }

        return $data;
    }

    public function getClass()
    {
        $class = $this->class_model->get();
        echo json_encode($class);
    }

    public function assigmnetDownload($id)
    {
        $assigmnetdata['id'] = $id;
        $assigmnetlist       = $this->studentdairy_model->get_upload_docs($assigmnetdata);
        $this->media_storage->filedownload($assigmnetlist[0]['docs'], "./uploads/studentdairy/assignment");
    }

    public function deletestudentdairy()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('delete_studentdairy[]', $this->lang->line('studentdairy'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'delete_studentdairy[]' => form_error('delete_studentdairy[]'),
            );
            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $delete_studentdairy_list = $this->input->post('delete_studentdairy');

            foreach ($delete_studentdairy_list as $_key => $studentdairy_list_value) {
                $this->studentdairy_model->delete($studentdairy_list_value);
            }

            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('delete_message'));
        }
        echo json_encode($array);
    }

    public function dailyassignment()
    {
        if (!$this->rbac->hasPrivilege('daily_assignment', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'studentdairy');
        $this->session->set_userdata('sub_menu', 'dailyassignment');
        $class             = $this->class_model->get();
        $data['classlist'] = $class;
        $data['class_id']  = "";
        $this->load->view("layout/header");
        $this->load->view("studentdairy/dailyassignmentlist", $data);
        $this->load->view("layout/footer");
    }

    public function searchdailyassignment()
    {
        $class_id                 = $this->input->post('class_id');
        $section_id               = $this->input->post('section_id');
        $subject_group_id         = $this->input->post('subject_group_id');
        $subject_group_subject_id = $this->input->post('subject_id');
        $date                     = $this->input->post('date');
        $download                 = "";

        if ($date != '') {
            $date = date('Y-m-d', $this->customlib->datetostrtotime($date));
        }

        $superadmin_rest = $this->session->userdata['admin']['superadmin_restriction'];
        $getStaffRole    = $this->customlib->getStaffRole();
        $staffrole       = json_decode($getStaffRole);
        $userdata        = $this->customlib->getUserData();
        $login_staff_id  = $userdata["id"];

        $dailyassignment = $this->studentdairy_model->searchdailyassignment($class_id, $section_id, $subject_group_id, $subject_group_subject_id, $date);
        $dailyassignment = json_decode($dailyassignment);

        $dt_data = array();
        if (!empty($dailyassignment->data)) {

            foreach ($dailyassignment->data as $key => $value) {

                if ($value->attachment != "") {
                    $download = "<a  href='" . base_url() . "studentdairy/dailyassigmnetdownload/" . $value->id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip'  title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                }

                $assignment = '<a onclick="assignmentdetails(' . $value->id . ')" class="btn btn-default btn-xs" data-target="#assignmentdetails" data-backdrop="static" data-keyboard="false" data-toggle="modal"  title="' . $this->lang->line('view') . '"><i class="fa fa-reorder"></i></a>';

                $evaluated_by = '';
                if ($value->evaluated_by != 0) {
                    if ($staffrole->id == 7) {

                        $evaluated_by = $value->name . ' ' . $value->surname . ' (' . $value->employee_id . ')';
                    } elseif ($superadmin_rest == 'enabled') {

                        $evaluated_by = $value->name . ' ' . $value->surname . ' (' . $value->employee_id . ')';
                    } elseif ($value->evaluated_by == $login_staff_id) {

                        $evaluated_by = $value->name . ' ' . $value->surname . ' (' . $value->employee_id . ')';
                    }
                }

                if ($value->evaluation_date) {
                    $evaluation_date = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->evaluation_date));
                } else {
                    $evaluation_date = "";
                }

                $code = '';
                if ($value->subject_code) {
                    $code = '(' . $value->subject_code . ')';
                }

                $row   = array();
                $row[] = $value->firstname . ' ' . $value->middlename . ' ' . $value->lastname . ' (' . $value->student_admission_no . ')';
                $row[] = $value->class;
                $row[] = $value->section;
                $row[] = $value->subject_name . ' ' . $code;
                $row[] = $value->title;
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[] = $evaluation_date;
                $row[] = $evaluated_by;
                $row[] = $download . ' ' . '<a onclick="assignmentevaluation(' . $value->id . ')" class="btn btn-default btn-xs" data-target="#assignmentevaluation" data-backdrop="static" data-keyboard="false" data-toggle="modal"  title="' . $this->lang->line('evaluate') . '"><i class="fa fa-newspaper-o"></i></a>' . ' ' . $assignment;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($dailyassignment->draw),
            "recordsTotal"    => intval($dailyassignment->recordsTotal),
            "recordsFiltered" => intval($dailyassignment->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function assignmentvalidation()
    {
        $class_id         = $this->input->post('class_id');
        $section_id       = $this->input->post('section_id');
        $subject_group_id = $this->input->post('subject_group_id');
        $subject_id       = $this->input->post('subject_id');
        $date             = $this->input->post('date');

        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_group_id', $this->lang->line('subject_group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $error = array();

            $error['class_id']         = form_error('class_id');
            $error['section_id']       = form_error('section_id');
            $error['subject_group_id'] = form_error('subject_group_id');
            $error['subject_id']       = form_error('subject_id');
            $error['date']             = form_error('date');

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            $params = array('class_id' => $class_id, 'section_id' => $section_id, 'subject_group_id' => $subject_group_id, 'subject_id' => $subject_id, 'date' => $date);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    public function getdailyassignmentdetails()
    {
        $id             = $this->input->post('id');
        $assignmentlist = $this->studentdairy_model->getsingledailyassignment($id);
        if ($assignmentlist['evaluation_date'] != "") {
            $assignmentlist['evaluation_date'] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($assignmentlist['evaluation_date']));
        } else {
            $assignmentlist['evaluation_date'] = "";
        }

        echo json_encode($assignmentlist);
    }

    public function dailyassigmnetdownload($id)
    {
        $dailyassigmnetlist = $this->studentdairy_model->getsingledailyassignment($id);
        $this->media_storage->filedownload($dailyassigmnetlist['attachment'], "./uploads/studentdairy/daily_assignment");
    }

    public function submitassignmentremark()
    {
        $this->form_validation->set_rules('evaluation_date', $this->lang->line('evaluation_date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'evaluation_date' => form_error('evaluation_date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $insert_data = array(
                'id'              => $this->input->post('assigment_id'),
                'evaluation_date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post('evaluation_date')),
                'remark'          => $this->input->post('remark'),
                'evaluated_by'    => $this->customlib->getStaffID(),
            );

            $this->studentdairy_model->adddailyassignment($insert_data);
            $msg   = $this->lang->line('evaluation_completed_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function assignmentdetails()
    {
        $assigment_id           = $this->input->post('assigment_id');
        $data['assignmentlist'] = $this->studentdairy_model->assignmentrecord($assigment_id);
        $data['sch_setting']    = $this->setting_model->getSetting();
        $page                   = $this->load->view("studentdairy/_assignmentdetails", $data, true);
        echo json_encode(array('page' => $page));
    }

    public function studentdairyreport()
    {
        if (!$this->rbac->hasPrivilege('studentdairy', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'studentdairy/studentdairyreport');
        $this->session->set_userdata('subsub_menu', 'studentdairy/studentdairyreport');

        $data['classlist'] = $this->class_model->get();

        $userdata                 = $this->customlib->getUserData();
        $carray                   = array();
        $data['class_id']         = $class_id   =   $this->input->post('class_id');
        $data['section_id']       = $section_id =   $this->input->post('section_id');
        $data['subject_group_id'] = $subject_group_id   =   $this->input->post('subject_group_id');
        $data['subject_id']       = $subject_id =   $this->input->post('subject_id');

        if (isset($_POST["search"])) {
            $studentdairylist = $this->studentdairy_model->search_dtstudentdairyreport($class_id, $section_id, $subject_group_id, $subject_id);
            $data["resultlist"] = $studentdairylist;
        }

        $this->load->view("layout/header", $data);
        $this->load->view("studentdairy/studentdairyreport", $data);
        $this->load->view("layout/footer", $data);
    }

    public function getStudentByClassSection()
    {
        $data               = array();
        $class_id           = $this->input->post('class_id');
        $section_id         = $this->input->post('section_id');
        $studentdairy_id        = $this->input->post('studentdairy_id');
        $type           = $this->input->post('type');

        $class_sections = $this->classsection_model->getDetailbyClassSection($class_id, $section_id);

        if ($type == 'student_count') {
            $student_list         = $this->student_model->getStudentBy_class_section_id($class_sections['id']);
        } elseif ($type == 'studentdairy_submitted') {
            $student_list         = $this->studentdairy_model->get_submitted_studentdairy($studentdairy_id);
        } elseif ($type == 'pending_student') {
            $student_list         = $this->studentdairy_model->get_not_submitted_studentdairy($class_id, $section_id, $studentdairy_id);
        }

        $data['student_list'] = $student_list;
        $data['sch_setting']  = $this->sch_setting_detail;
        $page                 = $this->load->view('studentdairy/_getStudentByClassSection', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function studentdairyordailyassignmentreport()
    {
        $this->session->set_userdata('top_menu', 'report');
        $this->session->set_userdata('sub_menu', 'studentdairy/studentdairyordailyassignmentreport');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('studentdairy/studentdairyordailyassignmentreport');
        $this->load->view('layout/footer');
    }

    public function dailyassignmentreport()
    {
        if (!$this->rbac->hasPrivilege('daily_assignment', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'studentdairy/studentdairyreport');
        $this->session->set_userdata('subsub_menu', 'studentdairy/dailyassignmentreport');

        $data['searchlist'] = $this->search_type;
        $class              = $this->class_model->get();
        $data['classlist']  = $class;
        $this->load->view("layout/header");
        $this->load->view("studentdairy/dailyassignmentreport", $data);
        $this->load->view("layout/footer");
    }

    public function searchdailyassignmentreport()
    {
        $class_id                 = $this->input->post('class_id');
        $section_id               = $this->input->post('section_id');
        $subject_group_id         = $this->input->post('subject_group_id');
        $subject_group_subject_id = $this->input->post('subject_id');

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $between_date        = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $search_type = $_POST['search_type'];
        } else {

            $between_date        = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = $search_type = '';
        }

        $from_date = date('Y-m-d', strtotime($between_date['from_date']));
        $to_date   = date('Y-m-d', strtotime($between_date['to_date']));
        $condition = " date_format(daily_assignment.date,'%Y-%m-%d') between  '" . $from_date . "' and '" . $to_date . "'";

        $superadmin_rest = $this->session->userdata['admin']['superadmin_restriction'];
        $getStaffRole    = $this->customlib->getStaffRole();
        $staffrole       = json_decode($getStaffRole);
        $userdata        = $this->customlib->getUserData();
        $login_staff_id  = $userdata["id"];
        $dailyassignment = $this->studentdairy_model->dailyassignmentreport($class_id, $section_id, $subject_group_id, $subject_group_subject_id, $condition);
        $dailyassignment = json_decode($dailyassignment);

        $dt_data = array();
        if (!empty($dailyassignment->data)) {

            foreach ($dailyassignment->data as $key => $value) {

                $assignment = '<a onclick="dailyassignmentdetails(' . $value->student_id . ')" class="btn btn-default btn-xs" data-target="#dailyassignmentdetails" data-backdrop="static" data-keyboard="false" data-toggle="modal"  title="' . $this->lang->line('view') . '"><i class="fa fa-reorder"></i></a>';

                $evaluated_by = '';
                if ($value->evaluated_by != 0) {
                    if ($staffrole->id == 7) {

                        $evaluated_by = $value->name . ' ' . $value->surname . ' (' . $value->employee_id . ')';
                    } elseif ($superadmin_rest == 'enabled') {

                        $evaluated_by = $value->name . ' ' . $value->surname . ' (' . $value->employee_id . ')';
                    } elseif ($value->evaluated_by == $login_staff_id) {

                        $evaluated_by = $value->name . ' ' . $value->surname . ' (' . $value->employee_id . ')';
                    }
                }

                if ($value->evaluation_date) {
                    $evaluation_date = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->evaluation_date));
                } else {
                    $evaluation_date = "";
                }

                $row   = array();
                $row[] = $value->firstname . ' ' . $value->middlename . ' ' . $value->lastname . ' (' . $value->student_admission_no . ')';
                $row[] = $value->class;
                $row[] = $value->section;
                $row[] = $value->total_student;
                $row[] = $assignment;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($dailyassignment->draw),
            "recordsTotal"    => intval($dailyassignment->recordsTotal),
            "recordsFiltered" => intval($dailyassignment->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function dailyassignmentdetails()
    {
        $student_id  = $this->input->post('student_id');
        $search_type = $this->input->post('search_type');
        $subject_id  = $this->input->post('subject_id');

        $data['superadmin_rest'] = $this->session->userdata['admin']['superadmin_restriction'];
        $getStaffRole            = $this->customlib->getStaffRole();
        $data['staffrole']       = json_decode($getStaffRole);
        $userdata                = $this->customlib->getUserData();
        $data['login_staff_id']  = $userdata["id"];

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $between_date        = $this->customlib->get_betweendate($_POST['search_type']);
            $from_date = date('Y-m-d', strtotime($between_date['from_date']));
            $to_date   = date('Y-m-d', strtotime($between_date['to_date']));
        } else {

            $between_date        = $this->customlib->get_betweendate('this_year');
            $from_date = date('Y-m-d', strtotime($between_date['from_date']));
            $to_date   = date('Y-m-d', strtotime($between_date['to_date']));
        }

        $condition = " date_format(daily_assignment.date,'%Y-%m-%d') between  '" . $from_date . "' and '" . $to_date . "'";

        $data['assignmentlist'] = $this->studentdairy_model->assignmentdetails($student_id, $condition, $subject_id);

        $data['sch_setting'] = $this->setting_model->getSetting();
        $page                = $this->load->view("reports/_dailyassignmentdetails", $data, true);
        echo json_encode(array('page' => $page));
    }

    public function dailyassignmentreportvalidation()
    {
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_group_id', $this->lang->line('subject_group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required|xss_clean');

        $class_id         = $this->input->post('class_id');
        $section_id       = $this->input->post('section_id');
        $subject_group_id = $this->input->post('subject_group_id');
        $subject_id       = $this->input->post('subject_id');

        if ($this->form_validation->run() == false) {
            $error = array();

            $error['search_type'] = form_error('search_type');

            $error['class_id']         = form_error('class_id');
            $error['section_id']       = form_error('section_id');
            $error['subject_group_id'] = form_error('subject_group_id');
            $error['subject_id']       = form_error('subject_id');

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {
            $search_type = $this->input->post('search_type');
            $date_from   = "";
            $date_to     = "";
            if ($search_type == 'period') {
                $date_from = $this->input->post('date_from');
                $date_to   = $this->input->post('date_to');
            }

            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            $params = array('search_type' => $search_type, 'date_from' => $date_from, 'date_to' => $date_to, 'class_id' => $class_id, 'section_id' => $section_id, 'subject_group_id' => $subject_group_id, 'subject_id' => $subject_id);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }
}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Content extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->load->library('enc_lib');
        $this->load->model(array('contenttype_model', 'uploadcontent_model', 'sharecontent_model'));
    }

    public function list()
    {
        // ===============================
        // HANDLE PREFLIGHT
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ===============================
        // ONLY GET METHOD
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ===============================
        // TOKEN VALIDATION
        // ===============================
        $auth = $this->auth->validate_user();

        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ]));
        }

        $login_id = $auth->login_id;
        $role     = $auth->role;

        // ===============================
        // STUDENT DETAILS (For class/section)
        // ===============================
        $student = $this->student_model->get($login_id);

        if (!$student) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student not found'
                ]));
        }

        $class_id   = $student['class_id'];
        $section_id = $student['section_id'];

        // ===============================
        // FETCH SHARE CONTENT
        // ===============================
        if ($role == "student") {

            $results = $this->sharecontent_model
                ->getStudentsharelist_mobile($login_id, $class_id, $section_id);
        } elseif ($role == "parent") {

            $results = $this->sharecontent_model
                ->getParentsharelist_mobile($login_id, $class_id, $section_id);
        } else {
            return $this->output
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Access Denied'
                ]));
        }

        // ===============================
        // SUPERADMIN RESTRICTION
        // ===============================
        $setting = $this->Setting_model->get();
        $superadmin_restriction = $setting[0]['superadmin_restriction'] ?? 0;

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'role'                   => $role,
                    'class_id'               => $class_id,
                    'section_id'             => $section_id,
                    'superadmin_restriction' => $superadmin_restriction,
                    'downloads'              => $results ?? []
                ]
            ]));
    }
    public function getsharelist()
    {
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $role                  = $this->customlib->getUserRole();
        if ($role == "student") {

            $m = $this->sharecontent_model->getStudentsharelist($this->customlib->getStudentSessionUserID(), $student_current_class->class_id, $student_current_class->section_id);
        } elseif ($role == "parent") {

            $m = $this->sharecontent_model->getParentsharelist($this->customlib->getUsersID(), $student_current_class->class_id, $student_current_class->section_id);
        }

        $superadmin_visible =    $this->Setting_model->get();
        $superadmin_restriction =   $superadmin_visible[0]['superadmin_restriction'];

        $m = json_decode($m);

        $dt_data = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $viewbtn   = '';
                $title     = $value->title;
                $row       = array();
                $row[]     = $title;
                $viewbtn   = "<a href='" . site_url('user/content/view/') . $value->id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('view') . "'><i class='fa fa-eye'></i></a>";
                $row[]     = $this->customlib->dateformat($value->share_date);
                $row[]     = $this->customlib->dateformat($value->valid_upto);

                if ($superadmin_restriction == 'disabled' && $value->role_id == 7) {
                    $row[]     =  '';
                } else {
                    $row[]     = $value->name . ' ' . $value->surname . ' (' . $value->employee_id . ')';
                }

                $row[]     = $viewbtn;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function view($id = null)
    {
        // ===============================
        // HANDLE PREFLIGHT
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // ===============================
        // ONLY GET METHOD
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // ===============================
        // TOKEN VALIDATION
        // ===============================
        $auth = $this->auth->validate_user();

        if (!$auth) {
            return $this->output
                ->set_status_header(401)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ]));
        }

        if (empty($id)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Content ID is required'
                ]));
        }

        // ===============================
        // FETCH CONTENT WITH DOCUMENTS
        // ===============================
        $content = $this->sharecontent_model
            ->getShareContentWithDocuments($id);

        if (empty($content)) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Content not found'
                ]));
        }

        // echo "<pre>";print_r($content);exit;

        // ===============================
        // SUPERADMIN RESTRICTION
        // ===============================
        $setting = $this->Setting_model->get();
        $superadmin_restriction = $setting[0]['superadmin_restriction'] ?? 0;

        // ===============================
        // FINAL RESPONSE
        // ===============================
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => [
                    'title'                   => 'Upload Content',
                    'title_list'              => 'Upload Content List',
                    'superadmin_restriction'  => $superadmin_restriction,
                    'content'                 => $content
                ]
            ]));
    }

    public function index()
    {
        $data['title']      = 'Upload Content';
        $data['title_list'] = 'Upload Content List';
        $list               = $this->content_model->get();
        $data['list']       = $list;
        $ght                = $this->customlib->getcontenttype();
        $data['ght']        = $ght;
        $class              = $this->class_model->get();
        $data['classlist']  = $class;
        $this->load->view('layout/student/header');
        $this->load->view('user/content/createcontent', $data);
        $this->load->view('layout/student/footer');
    }

    public function download($file)
    {
        $this->media_storage->filedownload($this->uri->segment(7), "./uploads/school_content/material");
    }

    public function assignment()
    {
        $this->session->set_userdata('top_menu', 'Downloads');
        $this->session->set_userdata('sub_menu', 'content/assignment');
        $student_id            = $this->customlib->getStudentSessionUserID();
        $student               = $this->student_model->get($student_id);
        $data['title_list']    = 'List of Assignment';
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $list                  = $this->content_model->getListByCategoryforUser($student_current_class->class_id, $student_current_class->section_id, "assignments");
        $data['list']          = $list;
        $this->load->view('layout/student/header');
        $this->load->view('user/content/assignment', $data);
        $this->load->view('layout/student/footer');
    }

    public function studymaterial()
    {
        $this->session->set_userdata('top_menu', 'Downloads');
        $this->session->set_userdata('sub_menu', 'content/studymaterial');
        $student_id            = $this->customlib->getStudentSessionUserID();
        $student               = $this->student_model->get($student_id);
        $data['title_list']    = 'List of Assignment';
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $list                  = $this->content_model->getListByCategoryforUser($student_current_class->class_id, $student_current_class->section_id, "study_material");
        $data['list']          = $list;
        $this->load->view('layout/student/header');
        $this->load->view('user/content/studymaterial', $data);
        $this->load->view('layout/student/footer');
    }

    public function syllabus()
    {
        $this->session->set_userdata('top_menu', 'Downloads');
        $this->session->set_userdata('sub_menu', 'content/syllabus');
        $student_id            = $this->customlib->getStudentSessionUserID();
        $student               = $this->student_model->get($student_id);
        $data['title_list']    = 'List of Syllabus';
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $list                  = $this->content_model->getListByCategoryforUser($student_current_class->class_id, $student_current_class->section_id, "syllabus");
        $data['list']          = $list;
        $this->load->view('layout/student/header');
        $this->load->view('user/content/syllabus', $data);
        $this->load->view('layout/student/footer');
    }

    public function other()
    {
        $this->session->set_userdata('top_menu', 'Downloads');
        $this->session->set_userdata('sub_menu', 'content/other');
        $student_id            = $this->customlib->getStudentSessionUserID();
        $student               = $this->student_model->get($student_id);
        $data['title_list']    = 'List of Other Download';
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $list                  = $this->content_model->getListByCategoryforUser($student_current_class->class_id, $student_current_class->section_id, "other_download");
        $data['list']          = $list;
        $this->load->view('layout/student/header');
        $this->load->view('user/content/other', $data);
        $this->load->view('layout/student/footer');
    }

    public function gallery()
    {
        //echo "test";
        $data['title'] = 'Gallery Categories';


        $data['g_categories'] = $this->user_model->get_categories();
        //print_r($data['g_categories']);exit;

        $this->load->view('layout/student/header');
        $this->load->view('user/gallery', $data);
        $this->load->view('layout/student/footer');
    }


    public function gallery_list($id)
    {
        $data['title'] = 'Gallery List';

        //echo "test";exit;


        $data['category_id'] = $id;
        $data['gallery_list'] = $this->user_model->get_gallery_by_category($id);

        //print_r($data['gallery_list']);exit;

        $this->load->view('layout/student/header');
        $this->load->view('user/gallery_list', $data);
        $this->load->view('layout/student/footer');
    }

    
}

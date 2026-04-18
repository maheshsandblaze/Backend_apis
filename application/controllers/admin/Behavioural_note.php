<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Behavioural_note extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("behavioural_model");
        $this->load->model('student_model');
        $this->load->model('class_model');
    }

    public function index()
    {

        if (!$this->rbac->hasPrivilege('behavioural_note', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/behavioural_note');
        $this->form_validation->set_rules('class_id', $this->lang->line('class_id'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('parameter_1', "Handwriting", 'required');
        $this->form_validation->set_rules('parameter_2', "Listening", 'required');
        $this->form_validation->set_rules('parameter_3', "Behaviour In Class Room", 'required');
        $this->form_validation->set_rules('parameter_4', "Behaviour With Teachers", 'required');
        $this->form_validation->set_rules('parameter_5', "Behaviour With Classmates / Elders And Youngers", 'required');
        $this->form_validation->set_rules('parameter_6', "Behaviour In Campus", 'required');
        // $this->form_validation->set_rules('parameter_7', "Bike", 'required');
  


        $class                   = $this->class_model->get();
        $data['classlist']       = $class;

        $students = $this->student_model->getStudentNames();
        $data['studentslist'] = $students;
        // echo "<pre>";
        // print_r($students);exit;
        if ($this->form_validation->run() == false) {
            $data['CallList'] = $this->behavioural_model->call_list();

            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/behaviouralview', $data);
            $this->load->view('layout/footer');
        } else {


            $class_id = $this->input->post('class_id');
            $sectionID = $this->input->post('section_id');
            $bike = $this->input->post('parameter_7');
            $staff_record = $this->staff_model->get($this->customlib->getStaffID());

            $collected_by    = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";

            $student_id = $this->input->post('name');

            $std = $this->student_model->getByStudentSession($student_id);
 
            
            $student_name = $std['firstname'].' '.$std['middlename'].' '.$std['lastname'];


            $calls = array(
            
                'date'           => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'name'    => $student_name,
                'parameter_1'           => $this->input->post('parameter_1'),
                'parameter_2'           => $this->input->post('parameter_2'),
                'parameter_3'           => $this->input->post('parameter_3'),
                'parameter_4'           => $this->input->post('parameter_4'),
                'parameter_5'           => $this->input->post('parameter_5'),
                'parameter_6'           => $this->input->post('parameter_6'),
                'collected_by'          => $collected_by,
                'student_session_id'           => $student_id
              
            );

            if($class_id != "")
            {
                $calls['class_id'] = $class_id;

            }
            if($sectionID != "")
            {
                $calls['section_id'] = $sectionID;
            }

            if($bike != "")
            {
                $calls['parameter_7'] = $this->input->post('parameter_7');
            }
   

           $res =  $this->behavioural_model->add($calls);

           if($res)
           {

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');

           }
            redirect('admin/behavioural_note');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('behavioural_note', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('na,e'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        if ($this->form_validation->run() == false) {
            $data['CallList']  = $this->behavioural_model->call_list();
            $data['Call_data'] = $this->behavioural_model->call_list($id);
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/generalcalleditview', $data);
            $this->load->view('layout/footer');
        } else {

            $sectionID = $this->input->post('section_id');
            $collected_by    = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";

            
            $calls_update = array(
                'name'           => $this->input->post('name'),
                'class_id'        => $this->input->post('contact'),
                'date'           => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'note'           => $this->input->post('note'),
              
            );

            $this->behavioural_model->call_update($id, $calls_update);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/generalcall');
        }
    }

    public function details($id)
    {
        if (!$this->rbac->hasPrivilege('behavioural_note', 'can_view')) {
            access_denied();
        }

        $data['Call_data'] = $this->behavioural_model->call_list($id);
        $this->load->view('admin/frontoffice/behaviouralmodeview', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('behavioural_note', 'can_delete')) {
            access_denied();
        }
        $this->behavioural_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/behavioural_note');
    }

    public function check_default($post_string)
    {
        return $post_string == '' ? false : true;
    }

    public function test()
    {

        $perm = $this->rbac->module_permission('student_information');
        if ($perm['is_active'] == '1') {
            echo "gc_disable()";
        }
    }

    public function getcalllist()
    {
        $callList        = $this->behavioural_model->getcalllist();
        // echo "<pre>";
        // print_r($callList);exit;
        $m               = json_decode($callList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $editbtn   = '';
                $deletebtn = '';
                $viewbtn   = '';

                $viewbtn = "<a onclick='getRecord(" . $value->id . ")' class='btn btn-default btn-xs' data-target='#calldetails' data-toggle='modal'  title='" . $this->lang->line('view') . "'><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('behavioural_note', 'can_edit')) {
                    // $editbtn = "<a href='" . base_url() . "admin/behavioural_note/edit/" . $value->id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('behavioural_note', 'can_delete')) {
                    $deletebtn = '';
                    $deletebtn = "<a onclick='return confirm(" . '"' . $this->lang->line('delete_confirm') . '"' . "  )' href='" . base_url() . "admin/behavioural_note/delete/" . $value->id . "' class='btn btn-default btn-xs' title='" . $this->lang->line('delete') . "' data-toggle='tooltip'><i class='fa fa-trash'></i></a>";
                }
                $row   = array();
                $row[] = $value->class;
                $row[] = $value->section;
                $row[] = $value->name;
                $row[] = $value->collected_by;
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));            

                // $row[] = $value->note;

                $row[]     = $viewbtn . ' ' . $editbtn . ' ' . $deletebtn;
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

    public function getStudentDetails()
    {
        $classID = $this->input->post('classID');
        $sectionID = $this->input->post('sectionID');
        $searchText = $this->input->post('searchText');



        $data = $this->student_model->getStudentSearch($classID, $sectionID, $searchText);
        // echo "<pre>";
        // print_r($data);exit;

        echo json_encode($data);
    }
}

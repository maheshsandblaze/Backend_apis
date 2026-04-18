<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Expense extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Customlib');
        $this->load->library('media_storage');
        $this->config->load('app-config');
        $this->load->library("datatables");
        $this->load->model('expense_model');
        $this->load->model('expensehead_model');
        $this->load->model('filetype_model');
    }

    private function _get_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        return $input;
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('exp_head_id', $this->lang->line('expense_head'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $img_name = $this->media_storage->fileupload("documents", "../uploads/school_expense/");

                $data = array(
                    'exp_head_id' => $input['exp_head_id'],
                    'name'        => $input['name'],
                    'date'        => date('Y-m-d', $this->customlib->datetostrtotime($input['date'])),
                    'amount'      => convertCurrencyFormatToBaseAmount($input['amount']),
                    'invoice_no'  => $input['invoice_no'] ?? '',
                    'note'        => $input['description'] ?? '',
                    'documents'   => $img_name,
                );

                $insert_id = $this->expense_model->add($data);

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => true,
                        'message' => $this->lang->line('success_message'),
                        'id'      => $insert_id
                    ]));
            }
        }

        // Default GET behavior: return setup data
        $expnseHead          = $this->expensehead_model->get();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => true,
                'expheadlist' => $expnseHead,
                'searchlist'  => $this->customlib->get_searchtype()
            ]));
    }

    public function download($id)
    {
        $result = $this->expense_model->get($id);
        if ($result && isset($result['documents'])) {
            $this->media_storage->filedownload($result['documents'], "./uploads/school_expense");
        }
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
            $file_type         = $_FILES["documents"]['type'];
            $file_size         = $_FILES["documents"]["size"];
            $file_name         = $_FILES["documents"]["name"];
            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES['documents']['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'File Type Not Allowed');
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading Image");
                return false;
            }

            return true;
        }
        return true;
    }

    public function fetch($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $expense = $this->expense_model->get($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'expense' => $expense
            ]));
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $row = $this->expense_model->get($id);
        if ($row && $row['documents'] != '') {
            $this->media_storage->filedelete($row['documents'], "uploads/school_expense/");
        }

        $this->expense_model->remove($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('delete_message')
            ]));
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $expense = $this->expense_model->get($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('exp_head_id', $this->lang->line('expense_head'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => $this->form_validation->error_array()
                    ]));
            } else {
                $data = array(
                    'id'          => $id,
                    'exp_head_id' => $input['exp_head_id'],
                    'name'        => $input['name'],
                    'invoice_no'  => $input['invoice_no'] ?? '',
                    'date'        => date('Y-m-d', $this->customlib->datetostrtotime($input['date'])),
                    'amount'      => convertCurrencyFormatToBaseAmount($input['amount']),
                    'note'        => $input['description'] ?? '',
                );

                if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                    $img_name = $this->media_storage->fileupload("documents", "../uploads/school_expense/");
                    if ($expense['documents'] != '') {
                        $this->media_storage->filedelete($expense['documents'], "../uploads/school_expense/");
                    }
                    $data['documents'] = $img_name;
                }

                $this->expense_model->add($data);

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => true,
                        'message' => $this->lang->line('update_message')
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => true,
                'expense' => $expense
            ]));
    }

    public function getexpenselist()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $m               = $this->expense_model->getexpenselist();
        $m               = json_decode($m);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $row   = array();
                $row['id'] = $value->id;
                $row['name'] = $value->name;
                $row['note'] = ($value->note == "") ? $this->lang->line('no_description') : $value->note;
                $row['invoice_no'] = $value->invoice_no;
                $row['date'] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row['exp_category'] = $value->exp_category;
                $row['amount'] = $currency_symbol . amountFormat($value->amount);
                $row['documents'] = $value->documents;
                $dt_data[] = $row;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw"            => intval($m->draw),
                "recordsTotal"    => intval($m->recordsTotal),
                "recordsFiltered" => intval($m->recordsFiltered),
                "data"            => $dt_data,
            ]));
    }

    public function getsearchexpenselist()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $search_type = $input['search_type'] ?? '';
        $button_type = $input['button_type'] ?? '';
        $search_text = $input['search_text'] ?? '';

        if ($button_type == 'search_filter') {
            if ($search_type != "") {
                if ($search_type == 'all') {
                    $dates = $this->customlib->get_betweendate('this_year');
                } else {
                    $dates = $this->customlib->get_betweendate($search_type);
                }
            } else {
                $dates       = $this->customlib->get_betweendate('this_year');
            }

            $date_from         = date('Y-m-d', strtotime($dates['from_date']));
            $date_to           = date('Y-m-d', strtotime($dates['to_date']));
            $date_from         = date('Y-m-d', $this->customlib->dateYYYYMMDDtoStrtotime($date_from));
            $date_to           = date('Y-m-d', $this->customlib->dateYYYYMMDDtoStrtotime($date_to));
            $resultList        = $this->expense_model->search("", $date_from, $date_to);

        } else {
            $resultList  = $this->expense_model->search($search_text, "", "");
        }

        $m               = json_decode($resultList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $grand_total += $value->amount;
                $row   = array();
                $row['id'] = $value->id;
                $row['name'] = $value->name;
                $row['invoice_no'] = $value->invoice_no;
                $row['exp_category'] = $value->exp_category;
                $row['date'] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row['amount'] = $currency_symbol . amountFormat($value->amount);
                $dt_data[] = $row;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw"            => intval($m->draw),
                "recordsTotal"    => intval($m->recordsTotal),
                "recordsFiltered" => intval($m->recordsFiltered),
                "data"            => $dt_data,
                "grand_total"     => $currency_symbol . amountFormat($grand_total)
            ]));
    }
}

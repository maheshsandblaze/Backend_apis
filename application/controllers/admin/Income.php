<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Income extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('media_storage');
        $this->config->load('app-config');
        $this->load->library("datatables");
        $this->load->library('form_validation');
        $this->load->model('income_model');
        $this->load->model('incomehead_model');
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
            $this->form_validation->set_rules('inc_head_id', $this->lang->line('income_head'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload');

            if ($this->form_validation->run() == true) {
                $img_name = $this->media_storage->fileupload("documents", "../uploads/school_income/");

                $data = array(
                    'income_head_id' => $input['inc_head_id'],
                    'name'        => $input['name'],
                    'date'        => date('Y-m-d', $this->customlib->datetostrtotime($input['date'])),
                    'amount'      => convertCurrencyFormatToBaseAmount($input['amount']),
                    'invoice_no'  => $input['invoice_no'] ?? '',
                    'note'        => $input['description'] ?? '',
                    'documents'   => $img_name,
                );
                $insert_id = $this->income_model->add($data);

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => true,
                        'message' => $this->lang->line('success_message'),
                        'id'      => $insert_id
                    ]));
            } else {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => $this->form_validation->error_array()
                    ]));
            }
        }

        $incomeHead          = $this->incomehead_model->get();
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'incheadlist' => $incomeHead
            ]));
    }

    public function fetch($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $income = $this->income_model->get($id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data'   => $income
            ]));
    }

    public function download($id)
    {
        $income = $this->income_model->get($id);
        $this->media_storage->filedownload($income['documents'], "uploads/school_income");
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if (!empty($id)) {
            $row = $this->income_model->get($id);
            if ($row['documents'] != '') {
                $this->media_storage->filedelete($row['documents'], "uploads/school_income/");
            }

            $this->income_model->remove($id);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $this->lang->line('delete_message')
                ]));
        } else {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'ID is required'
                ]));
        }
    }

    public function handle_upload()
    {
        $result = $this->filetype_model->get();
        if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {

            $file_type = $_FILES["documents"]['type'];
            $file_size = $_FILES["documents"]["size"];
            $file_name = $_FILES["documents"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES['documents']['tmp_name'])) {
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

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $income = $this->income_model->get($id);
        $input = $this->_get_input();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules('inc_head_id', $this->lang->line('income_head'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload');

            if ($this->form_validation->run() == true) {
                $data = array(
                    'id'          => $id,
                    'income_head_id' => $input['inc_head_id'],
                    'name'        => $input['name'],
                    'date'        => date('Y-m-d', $this->customlib->datetostrtotime($input['date'])),
                    'amount'      => convertCurrencyFormatToBaseAmount($input['amount']),
                    'invoice_no'  => $input['invoice_no'] ?? '',
                    'note'        => $input['description'] ?? '',
                );

                if (isset($_FILES["documents"]) && $_FILES['documents']['name'] != '' && (!empty($_FILES['documents']['name']))) {
                    $img_name = $this->media_storage->fileupload("documents", "../uploads/school_income/");
                    if ($income['documents'] != '') {
                        $this->media_storage->filedelete($income['documents'], "../uploads/school_income/");
                    }
                } else {
                    $img_name = $income['documents'];
                }

                $data['documents'] = $img_name;
                $this->income_model->add($data);

                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => true,
                        'message' => $this->lang->line('update_message')
                    ]));
            } else {
                return $this->output
                    ->set_status_header(422)
                    ->set_output(json_encode([
                        'status' => false,
                        'errors' => $this->form_validation->error_array()
                    ]));
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'data' => $income,
                'incheadlist' => $this->incomehead_model->get()
            ]));
    }

    public function getincomelist()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $m               = $this->income_model->getincomelist();
        $m               = json_decode($m);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $row   = array();
                $row['id'] = $value->id;
                $row['name'] = $value->name;
                $row['description'] = $value->note == "" ? $this->lang->line('no_description') : $value->note;
                $row['invoice_no'] = $value->invoice_no;
                $row['date'] = $this->customlib->dateformat($value->date);
                $row['income_category'] = $value->income_category;
                $row['amount'] = $value->amount;
                $row['amount_formatted'] = $currency_symbol . amountFormat($value->amount);
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

    public function incomeSearch()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $search_type = $input['search_type'] ?? 'all';
        $button_type = $input['button_type'] ?? 'search_filter';
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

            $date_from = date('Y-m-d', strtotime($dates['from_date']));
            $date_to   = date('Y-m-d', strtotime($dates['to_date']));
            $resultList = $this->income_model->search("", $date_from, $date_to);
        } else {
            $resultList = $this->income_model->search($search_text, "", "");
        }

        $m = json_decode($resultList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data = array();
        $total_amount = 0;

        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $total_amount += $value->amount;
                $row = array();
                $row['name'] = $value->name;
                $row['invoice_no'] = $value->invoice_no;
                $row['income_category'] = $value->income_category;
                $row['date'] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row['amount'] = $value->amount;
                $row['amount_formatted'] = $currency_symbol . amountFormat($value->amount);
                $dt_data[] = $row;
            }
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                "status" => true,
                "data" => $dt_data,
                "total_amount" => $total_amount,
                "total_amount_formatted" => $currency_symbol . amountFormat($total_amount),
                "recordsTotal" => intval($m->recordsTotal ?? 0),
                "recordsFiltered" => intval($m->recordsFiltered ?? 0),
            ]));
    }
}

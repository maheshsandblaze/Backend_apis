<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Paymentsettings extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('paymentsetting_model');
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

        $paymentlist = $this->paymentsetting_model->get();

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'      => 'success',
                'paymentlist' => $paymentlist,
                'statuslist'  => $this->customlib->getStatus()
            ]));
    }

    public function ccavenue()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('ccavenue_secret', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ccavenue_salt', $this->lang->line('salt'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ccavenue_api_publishable_key', $this->lang->line('access_code'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $input['ccavenue_secret'],
                'salt'                => $input['ccavenue_salt'],
                'api_publishable_key' => $input['ccavenue_api_publishable_key'],
                'payment_type'        => 'ccavenue',
            );
            $this->paymentsetting_model->add($data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('update_message')
                ]));
        } else {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        }
    }

    public function razorpay()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('razorpay_keyid', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('razorpay_secretkey', $this->lang->line('key'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $input['razorpay_secretkey'],
                'api_publishable_key' => $input['razorpay_keyid'],
                'payment_type'        => 'razorpay',
            );
            $this->paymentsetting_model->add($data);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('update_message')
                ]));
        } else {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        }
    }

    public function setting()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('payment_setting', $this->lang->line('payment_setting'), array('required',
            array('paymentsetting', function($str) use ($input) {
                return $this->paymentsetting_model->valid_paymentsetting($input);
            }),
        ));

        if ($this->form_validation->run()) {
            $paymentsetting = $input['payment_setting'];
            $other          = false;
            
            if ($paymentsetting == "none") {
                $other = true;
                $data  = array(
                    'is_active' => 'no',
                );
            } else {
                $data = array(
                    'payment_type' => $paymentsetting,
                    'is_active'    => 'yes',
                );
            }
            $this->paymentsetting_model->active($data, $other);

            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('update_message')
                ]));
        } else {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        }
    }


}

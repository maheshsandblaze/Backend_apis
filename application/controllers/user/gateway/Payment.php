<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . 'third_party/omnipay/vendor/autoload.php';

class Payment extends Public_Controller
{

    public $payment_method;
    public $school_name;
    public $school_setting;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Customlib');
        $this->load->library('Paypal_payment');
        $this->load->library('Stripe_payment');
        $this->load->library('smsgateway');
        $this->load->library('mailsmsconf');
        $this->payment_method     = $this->paymentsetting_model->get();
        $this->school_name        = $this->customlib->getAppName();
        $this->school_setting     = $this->setting_model->get();
        $this->setting            = $this->setting_model->get();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }
    public function grouppay()
    {
        // Handle preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || empty($input['fees'])) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Invalid payload'
                ]));
        }

        $fees_master_array = [];
        $total_amount_balance = 0;
        $total_fine_balance = 0;

        foreach ($input['fees'] as $fee) {

            $fee_category = $fee['fee_category'];
            $transport_id = $fee['student_transport_fee_id'] ?? null;
            $fee_groups_feetype_id = $fee['fee_groups_feetype_id'] ?? null;
            $student_fees_master_id = $fee['student_fees_master_id'] ?? null;

            $amount_balance = 0;
            $fine_balance = 0;

            if ($fee_category == "transport") {

                $result = $this->studentfeemaster_model
                    ->studentTRansportDeposit($transport_id);

                $amount_balance = $result->fees;
                $fine_balance   = $result->fine_amount ?? 0;

                $fee_record = [
                    'fee_category' => 'transport',
                    'student_transport_fee_id' => $transport_id,
                    'fee_group_name' => 'Transport Fees',
                    'fee_type_code' => $result->month,
                    'amount_balance' => $amount_balance,
                    'fine_balance' => $fine_balance
                ];
            } else {

                $data = [
                    'fee_groups_feetype_id' => $fee_groups_feetype_id,
                    'student_fees_master_id' => $student_fees_master_id
                ];

                $result = $this->studentfeemaster_model->studentDeposit($data);

                $amount_balance = $result->amount;
                $fine_balance   = $result->fine_amount ?? 0;

                $fee_record = [
                    'fee_category' => 'fees',
                    'student_fees_master_id' => $student_fees_master_id,
                    'fee_groups_feetype_id' => $fee_groups_feetype_id,
                    'fee_group_name' => $result->fee_group_name,
                    'fee_type_code' => $result->fee_type_code,
                    'amount_balance' => $amount_balance,
                    'fine_balance' => $fine_balance
                ];
            }

            $fees_master_array[] = $fee_record;

            $total_amount_balance += $amount_balance;
            $total_fine_balance   += $fine_balance;
        }

        $student_id = $input['student_id'];

        $student = $this->student_model->get($student_id);

        $pay_method = $this->paymentsetting_model->getActiveMethod();

        if (!$pay_method) {
            return $this->output
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Payment gateway not configured'
                ]));
        }

        $params = [
            'gateway' => $pay_method->payment_type,
            'student_id' => $student_id,
            'student_session_id' => $student['student_session_id'],
            'name' => $student['firstname'] . " " . $student['lastname'],
            'email' => $student['email'],
            'phone' => $student['guardian_phone'],
            'amount_total' => $total_amount_balance,
            'fine_total' => $total_fine_balance,
            'fees' => $fees_master_array
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'gateway' => $pay_method->payment_type,
                'payment_data' => $params
            ]));
    }
    public function pay()
    {
        // Handle preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Invalid payload'
                ]));
        }

        $fee_category             = $input['fee_category'] ?? '';
        $student_transport_fee_id = $input['student_transport_fee_id'] ?? null;
        $student_fees_master_id   = $input['student_fees_master_id'] ?? null;
        $fee_groups_feetype_id    = $input['fee_groups_feetype_id'] ?? null;
        $student_id               = $input['student_id'] ?? null;
        $submit_mode              = $input['submit_mode'] ?? '';

        if (!$student_id || !$submit_mode) {
            return $this->output
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Required fields missing'
                ]));
        }

        /* ===============================
       OFFLINE PAYMENT
    =============================== */
        if ($submit_mode == "offline_payment") {

            $fee_record = [
                'fee_category' => $fee_category,
                'student_transport_fee_id' => $student_transport_fee_id,
                'fee_groups_feetype_id' => $fee_groups_feetype_id,
                'student_fees_master_id' => $student_fees_master_id
            ];

            return $this->output
                ->set_output(json_encode([
                    'status' => true,
                    'payment_type' => 'offline',
                    'data' => $fee_record
                ]));
        }

        /* ===============================
       ONLINE PAYMENT
    =============================== */

        if ($submit_mode == "online_payment") {

            $pay_method = $this->paymentsetting_model->getActiveMethod();

            if (!$pay_method) {
                return $this->output
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Payment gateway not configured'
                    ]));
            }

            $student = $this->student_model->get($student_id);

            if (!$student) {
                return $this->output
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Student not found'
                    ]));
            }

            $amount_balance = 0;
            $fine_balance   = 0;

            if ($fee_category == "fees") {

                $data = [
                    'fee_groups_feetype_id'  => $fee_groups_feetype_id,
                    'student_fees_master_id' => $student_fees_master_id
                ];

                $result = $this->studentfeemaster_model->studentDeposit($data);

                $amount_balance = $result->amount;
            }

            if ($fee_category == "transport") {

                $result = $this->studentfeemaster_model
                    ->studentTRansportDeposit($student_transport_fee_id);

                $amount_balance = $result->fees;
            }

            $params = [
                'gateway' => $pay_method->payment_type,
                'amount'  => $amount_balance,
                'student_id' => $student_id,
                'student_session_id' => $student['student_session_id'],
                'name'  => $student['firstname'] . ' ' . $student['lastname'],
                'email' => $student['email'],
                'phone' => $student['guardian_phone']
            ];

            return $this->output
                ->set_output(json_encode([
                    'status' => true,
                    'payment_type' => 'online',
                    'gateway' => $pay_method->payment_type,
                    'params' => $params
                ]));
        }

        return $this->output
            ->set_output(json_encode([
                'status' => false,
                'message' => 'Invalid payment mode'
            ]));
    }

    public function pay1($student_fees_master_id, $fee_groups_feetype_id, $student_id)
    {
        $this->session->unset_userdata("params");
        ///=======================get balance fees

        if (!empty($this->payment_method)) {
            $data                           = array();
            $data['fee_groups_feetype_id']  = $fee_groups_feetype_id;
            $data['student_fees_master_id'] = $student_fees_master_id;
            $result                         = $this->studentfeemaster_model->studentDeposit($data);

            $fee_record                           = array();
            $fee_record['fee_groups_feetype_id']  = $fee_groups_feetype_id;
            $fee_record['student_fees_master_id'] = $student_fees_master_id;
            $fee_record['fee_group_name']         = $result->fee_group_name;
            $fee_record['fee_type_code']          = $result->fee_type_code;

            $fees_master_array = array();

            $amount_balance      = 0;
            $amount              = 0;
            $amount_fine         = 0;
            $amount_discount     = 0;
            $fine_amount_balance = 0;
            $amount_detail       = json_decode($result->amount_detail);

            if (strtotime($result->due_date) < strtotime(date('Y-m-d'))) {
                $fine_amount_balance = $result->fine_amount;
            }

            if (is_object($amount_detail)) {
                foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                    $amount          = $amount + $amount_detail_value->amount;
                    $amount_discount = $amount_discount + $amount_detail_value->amount_discount;
                    $amount_fine     = $amount_fine + $amount_detail_value->amount_fine;
                }
            }

            $amount_balance = $result->amount - ($amount + $amount_discount);
            if ($result->is_system) {
                $amount_balance = $result->student_fees_master_amount - ($amount + $amount_discount);
            }
            $fine_amount_balance = $fine_amount_balance - $amount_fine;

            $student_record               = $this->student_model->get($student_id);
            $pay_method                   = $this->paymentsetting_model->getActiveMethod();
            $fee_record['fine_balance']   = $fine_amount_balance;
            $fee_record['amount_balance'] = $amount_balance;
            $fees_master_array[]          = $fee_record;
            //======================================

            $page                = new stdClass();
            $page->symbol        = $this->setting[0]['currency_symbol'];
            $page->currency_name = $this->setting[0]['currency'];
            $params              = array(
                'key'                       => $pay_method->api_secret_key,
                'api_publishable_key'       => $pay_method->api_publishable_key,
                'invoice'                   => $page,
                'total'                     => ($amount_balance),
                'fine_amount_balance'       => convertBaseAmountCurrencyFormat($fine_amount_balance),
                'student_session_id'        => $student_record['student_session_id'],
                'guardian_phone'            => $student_record['guardian_phone'],
                'name'                      => $this->customlib->getFullName($student_record['firstname'], $student_record['middlename'], $student_record['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname),

                'email'                     => $student_record['email'],
                'guardian_phone'            => $student_record['guardian_phone'],
                'address'                   => $student_record['permanent_address'],
                'student_id'                => $student_id,
                'student_fees_master_array' => $fees_master_array,
            );
            //=====================================
            if ($pay_method->payment_type == "paypal") {
                //==========Start Paypal==========
                if ($pay_method->api_username == "" || $pay_method->api_password == "" || $pay_method->api_signature == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('paypal_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/paypal"));
                }
                //==========End Paypal==========
            } else if ($pay_method->payment_type == "paystack") {
                ///=====================
                if ($pay_method->api_secret_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('paystack_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/paystack"));
                }

                //=======================
            } else if ($pay_method->payment_type == "stripe") {
                ///=====================
                if ($pay_method->api_secret_key == "" || $pay_method->api_publishable_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('stripe_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/stripe"));
                }

                //=======================
            } else if ($pay_method->payment_type == "payu") {

                if ($pay_method->api_secret_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('payu_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/payu"));
                }
            } else if ($pay_method->payment_type == "ccavenue") {
                if ($pay_method->api_secret_key == "" || $pay_method->salt == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('ccavenue_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/ccavenue"));
                }
            } else if ($pay_method->payment_type == "instamojo") {

                if ($pay_method->api_secret_key == "" || $pay_method->api_publishable_key == "" || $pay_method->salt == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('instamojo_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/instamojo"));
                }
            } else if ($pay_method->payment_type == "razorpay") {

                if ($pay_method->api_secret_key == "" || $pay_method->api_publishable_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('razorpay_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/razorpay"));
                }
            } else if ($pay_method->payment_type == "paytm") {
                if ($pay_method->api_secret_key == "" || $pay_method->api_publishable_key == "" || $pay_method->paytm_website == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('paytm_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/paytm"));
                }
            } else if ($pay_method->payment_type == "midtrans") {
                if ($pay_method->api_secret_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('midtrans_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/midtrans"));
                }
            } else if ($pay_method->payment_type == "pesapal") {
                if ($pay_method->api_secret_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('pesapal_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/pesapal"));
                }
            } else if ($pay_method->payment_type == "flutterwave") {
                if ($pay_method->api_publishable_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('flutterwave_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/flutterwave"));
                }
            } else if ($pay_method->payment_type == "ipayafrica") {
                if ($pay_method->api_publishable_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('ipay_africa_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/ipayafrica"));
                }
            } else if ($pay_method->payment_type == "jazzcash") {
                if ($pay_method->api_secret_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('jazzcash_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/jazzcash"));
                }
            } else if ($pay_method->payment_type == "billplz") {
                if ($pay_method->api_secret_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('billplz_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/billplz"));
                }
            } else if ($pay_method->payment_type == "sslcommerz") {
                if ($pay_method->api_publishable_key == "" || $pay_method->api_password == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('sslcommerz_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/sslcommerz"));
                }
            } else if ($pay_method->payment_type == "walkingm") {
                if ($pay_method->api_publishable_key == "" || $pay_method->api_secret_key == "") {
                    $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('walkingm_settings_not_available') . '</div>');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->session->set_userdata("params", $params);
                    redirect(base_url("students/walkingm"));
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('something_went_wrong'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', '<div class="alert alert-danger">' . $this->lang->line('payment_settings_not_available') . '</div>');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function paymentfailed()
    {
        $this->session->set_userdata('top_menu', 'Fees');
        $data['title']       = 'Invoice';
        $data['message']     = "dfsdfds";
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/paymentfailed', $data);
        $this->load->view('layout/student/footer', $data);
    }

    public function paymentprocessing()
    {
        $params         = $this->session->userdata('params');
        $student_record = $this->student_model->getByStudentSession($params['student_session_id']);
        $this->session->set_userdata('top_menu', 'Fees');
        $data['title']                 = 'Invoice';
        $data['message']               = "dfsdfds";
        $setting_result                = $this->setting_model->get();
        $data['settinglist']           = $setting_result;
        $mailsms_array                 = (object) array();
        $mailsms_array->transaction_id = $params['transaction_id'];
        $mailsms_array->guardian_phone = $params['guardian_phone'];
        $mailsms_array->email          = $params['email'];
        $mailsms_array->class          = $student_record['class'];
        $mailsms_array->section        = $student_record['section'];
        $mailsms_array->fee_amount     = $params['total'];
        $mailsms_array->guardian_email = $params['guardian_email'];
        $mailsms_array->mobileno       = $params['mobileno'];
        $mailsms_array->parent_app_key = $student_record['parent_app_key'];
        $mailsms_array->app_key = $student_record['app_key'];
        $mailsms_array->student_name   = $this->customlib->getFullName($student_record['firstname'], $student_record['middlename'], $student_record['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
        $this->mailsmsconf->mailsms('fee_processing', $mailsms_array);
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/paymentprocessing', $data);
        $this->load->view('layout/student/footer', $data);
    }

    public function successinvoice()
    {
        $this->session->set_userdata('top_menu', 'fees');
        $this->session->set_userdata('sub_menu', 'student/getFees');
        $data = array();
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/invoice', $data);
        $this->load->view('layout/student/footer', $data);
    }
}

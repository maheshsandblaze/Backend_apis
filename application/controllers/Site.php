<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Site extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->check_installation();
        if ($this->config->item('installed') == true) {
            $this->db->reconnect();
        }

        $this->load->model(array("staff_model", "sharecontent_model"));
        $this->load->library('Auth');
        $this->load->library('Enc_lib');
        $this->load->library('customlib');
        $this->load->library('captchalib');
        $this->load->library('mailsmsconf');
        $this->load->library('mailer');
        $this->load->library('media_storage');
        $this->load->config('ci-blog');
        $this->mailer;
        $this->sch_setting = $this->setting_model->getSetting();

        // Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
        // Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
        // Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed
    }

    private function check_installation()
    {
        if ($this->uri->segment(1) !== 'install') {
            $this->load->config('migration');
            if ($this->config->item('installed') == false && $this->config->item('migration_enabled') == false) {
                redirect(base_url() . 'install/start');
            } else {
                if (is_dir(APPPATH . 'controllers/install')) {
                    echo '<h3>Delete the install folder from application/controllers/install</h3>';
                    die;
                }
            }
        }
    }

    //     public function login()
    //     {

    //         $app_name = $this->setting_model->get();
    //         $app_name = $app_name[0]['name'];

    //         if ($this->auth->logged_in()) {
    //             $this->auth->is_logged_in(true);
    //         }
    // 		 if ($this->module_lib->hasModule('google_authenticator') 
    //             && $this->module_lib->hasActive('google_authenticator')) {

    //              redirect('gauthenticate/login');

    //         }		 
    //         $data          = array();
    //         $data['title'] = 'Login';
    //         $school        = $this->setting_model->get();

    //         $data['name'] = $app_name;

    //         $notice_content     = $this->config->item('ci_front_notice_content');
    //         $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
    //         $data['notice']     = $notices;
    //         $data['school']     = $school[0];
    //         $is_captcha         = $this->captchalib->is_captcha('login');
    //         $data["is_captcha"] = $is_captcha;
    //         if ($this->captchalib->is_captcha('login')) {
    //             if($this->input->post('captcha')){
    //                 $this->form_validation->set_rules('captcha', $this->lang->line('captcha'), 'trim|required|callback_check_captcha');
    //             }else{
    //                 $this->form_validation->set_rules('captcha', $this->lang->line('captcha'), 'trim|required');
    //             }
    //         }
    //         $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
    //         if ($this->form_validation->run() == false) {
    //             $captcha               = $this->captchalib->generate_captcha();
    //             $data['captcha_image'] = isset($captcha['image']) ? $captcha['image'] : "";
    //             $data['name']          = $app_name;
    //             $this->load->view('admin/login', $data);
    //         } else {
    //             $login_post = array(
    //                 'email'    => $this->input->post('username'),
    //                 'password' => $this->input->post('password'),
    //             );
    //             if ($this->captchalib->is_captcha('login')) {
    //             $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
    //             }
    //             $setting_result        = $this->setting_model->get();

    //             $result                = $this->staff_model->checkLogin($login_post);


    //             if (!empty($result->language_id)) {
    //                 $lang_array = array('lang_id' => $result->language_id, 'language' => $result->language);
    //                 if ($result->is_rtl == 1) {
    //                     $is_rtl = "enabled";
    //                 } else {
    //                     $is_rtl = "disabled";
    //                 }

    //             } else {
    //                 $lang_array = array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']);
    //                 if ($setting_result[0]['is_rtl'] == 1) {
    //                     $is_rtl = "enabled";
    //                 } else {
    //                     $is_rtl = "disabled";
    //                 }
    //             }

    //             if ($result) {
    //                 if ($result->is_active) {
    //                     if ($result->surname != "") {
    //                         $logusername = $result->name . " " . $result->surname;
    //                     } else {
    //                         $logusername = $result->name;
    //                     }


    //                     $session_data = array(
    //                         'id'                     => $result->id,
    //                         'username'               => $logusername,
    //                         'email'                  => $result->email,
    //                         'image'                  =>$result->image,
    //                         'roles'                  => $result->roles,
    //                         'date_format'            => $setting_result[0]['date_format'],                        
    //                         'currency'               => ($result->currency == 0) ? $setting_result[0]['currency']: $result->currency,
    //                         'currency_base_price'    => ($result->base_price == 0) ? $setting_result[0]['base_price']: $result->base_price,
    //                         'currency_format'        => $setting_result[0]['currency_format'],
    //                         'currency_symbol'        => ($result->symbol == "0") ? $setting_result[0]['currency_symbol'] : $result->symbol,
    //                         'currency_place'         => $setting_result[0]['currency_place'],
    //                         'start_month'            => $setting_result[0]['start_month'],
    //                         'start_week'             => date("w", strtotime($setting_result[0]['start_week'])),
    //                         'school_name'            => $setting_result[0]['name'],
    //                         'timezone'               => $setting_result[0]['timezone'],
    //                         'sch_name'               => $setting_result[0]['name'],
    //                         'language'               => $lang_array,
    //                         'is_rtl'                 => $is_rtl,
    //                         'theme'                  => $setting_result[0]['theme'],
    //                         'gender'                 => $result->gender,                     
    //                         'db_array'               => ['base_url'               => $setting_result[0]['base_url'],
    //                                                      'folder_path'            => $setting_result[0]['folder_path'],
    //                                                      'db_group'=>'default'],
    //                         'superadmin_restriction' => $setting_result[0]['superadmin_restriction'],
    //                     );

    //                     $this->session->set_userdata('admin', $session_data);

    //                     $role      = $this->customlib->getStaffRole();
    //                     $role_name = json_decode($role)->name;
    //                     $this->customlib->setUserLog($this->input->post('username'), $role_name);

    //                     if (isset($_SESSION['redirect_to'])) {
    //                         redirect($_SESSION['redirect_to']);
    //                     } else {
    //                         redirect('admin/admin/dashboard');
    //                     }

    //                 } else {
    //                     $data['name']          = $app_name;
    //                     $data['error_message'] = $this->lang->line('your_account_is_disabled_please_contact_to_administrator');

    //                     $this->load->view('admin/login', $data);
    //                 }
    //             } else {
    //                 $data['name']          = $app_name;
    //                 $data['error_message'] = $this->lang->line('invalid_username_or_password');
    //                 $this->load->view('admin/login', $data);
    //             }
    //         }
    //     }


    public function login()
    {
        // Handle preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // $check_auth_client = $this->admin_model->check_auth_client();

        // if ($check_auth_client == true) {

        $input = json_decode(file_get_contents("php://input"), true);

        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        $captcha  = $input['captcha'] ?? '';

        /* =======================
               BASIC VALIDATION
            ======================== */
        if (empty($username) || empty($password)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Username and password are required'
            ]);
            return;
        }

        /* =======================
               CAPTCHA CHECK
            ======================== */
        if ($this->captchalib->is_captcha('login')) {

            if (empty($captcha)) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Captcha is required'
                ]);
                return;
            }

            if (!$this->captchalib->check_captcha($captcha)) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Invalid captcha'
                ]);
                return;
            }
        }

        /* =======================
               LOGIN CHECK
            ======================== */
        $login_post = [
            'email'    => $username,
            'password' => $password
        ];

        $result = $this->staff_model->checkLogin($login_post);

        if (!$result) {
            echo json_encode([
                'status'  => false,
                'message' => $this->lang->line('invalid_username_or_password')
            ]);
            return;
        }

        if (!$result->is_active) {
            echo json_encode([
                'status'  => false,
                'message' => $this->lang->line('your_account_is_disabled_please_contact_to_administrator')
            ]);
            return;
        }

        /* =======================
               SETTINGS & LANGUAGE
            ======================== */
        $setting_result = $this->setting_model->get();

        if (!empty($result->language_id)) {
            $lang_array = [
                'lang_id'  => $result->language_id,
                'language' => $result->language
            ];
            $is_rtl = ($result->is_rtl == 1) ? 'enabled' : 'disabled';
        } else {
            $lang_array = [
                'lang_id'  => $setting_result[0]['lang_id'],
                'language' => $setting_result[0]['language']
            ];
            $is_rtl = ($setting_result[0]['is_rtl'] == 1) ? 'enabled' : 'disabled';
        }

        /* =======================
               USER NAME FORMAT
            ======================== */
        $logusername = trim($result->name . ' ' . $result->surname);



        /* =======================
               SESSION DATA
            ======================== */
        $session_data = [
            'id'        => $result->id,
            'username'  => $logusername,
            'email'     => $result->email,
            'employee_id'   => $result->employee_id,
            'image'     => $result->image,
            'roles'     => $result->roles,
            'gender'    => $result->gender,
            'language'  => $lang_array,
            'is_rtl'    => $is_rtl,
            'timezone'  => $setting_result[0]['timezone'],
            'theme'     => $setting_result[0]['theme'],
            'superadmin_restriction' => $setting_result[0]['superadmin_restriction'],

        ];

        $this->session->set_userdata('admin', $session_data);

        /* =======================
               USER LOG
            ======================== */
        $role      = $this->customlib->getStaffRole();
        $role_name = json_decode($role)->name;
        $this->customlib->setUserLog($username, $role_name);


        // Insert the auth token code snippet start
        $last_login = date('Y-m-d H:i:s');
        $token      = $this->getToken();
        $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));

        $this->db->insert('admin_authentication', array('login_id' => $result->id, 'token' => $token, 'role' => $role_name, 'expired_at' => $expired_at));
        // Insert the auth token code snippet end


        echo json_encode([
            'status'  => true,
            'message' => 'Login successful',
            'data'    => [
                'user'    => $session_data,
                'token'  => $token,
                'redirect' => 'admin/dashboard' // React will handle routing
            ]
        ]);
        // }
    }

    // public function logout()
    // {
    //     $admin_session   = $this->session->userdata('admin');
    //     $student_session = $this->session->userdata('student');
    //     $this->auth->logout();
    //     if ($admin_session) {
    //         redirect('site/login');
    //     } else if ($student_session) {
    //         redirect('site/userlogin');
    //     } else {
    //         redirect('site/userlogin');
    //     }
    // }

    public function logout()
    {
        // =======================
        // HANDLE PREFLIGHT REQUEST
        // =======================
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        /* =======================
       GET AUTH TOKEN (OPTIONAL BUT RECOMMENDED)
    ======================== */
        $headers = getallheaders();
        $token   = $headers['Authorization'] ?? '';

        //    print_r($token);exit;

        if (!empty($token)) {
            // Remove "Bearer " if exists
            $token = str_replace('Bearer ', '', $token);

            // Delete token from database
            $this->db->where('token', $token);
            $this->db->delete('admin_authentication');

            // echo $this->db->last_query();exit;
        }

        

        if ($this->session->userdata('admin') || $this->session->userdata('student')) {
            $this->session->sess_destroy();
        }

        /* =======================
       API RESPONSE
    ======================== */
        echo json_encode([
            'status'  => true,
            'message' => 'Logout successful'
        ]);
    }


    // public function getToken($randomIdLength = 10)
    // {
    //     $token = '';
    //     do {
    //         $bytes = rand(1, $randomIdLength);
    //         $token .= str_replace(
    //             ['.', '/', '='], '', base64_encode($bytes)
    //         );
    //     } while (strlen($token) < $randomIdLength);
    //     return $token;
    // }

    private function getToken()
    {
        return bin2hex(random_bytes(32)); // 64-char secure token
    }



    public function download_content($share_id, $content_id)
    {
        $content_id = $this->enc_lib->dycrypt($content_id);
        $content    = $this->sharecontent_model->checkvalid($share_id, $content_id);
        if ($content) {
            $this->media_storage->filedownload($content->img_name, $content->dir_path);
        } else {
            echo $this->lang->line('invalid_or_expired_link_please_check_it_again');
        }
    }

    public function forgotpassword()
    {

        $app_name     = $this->setting_model->get();
        $data['name'] = $app_name[0]['name'];
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|required|xss_clean');

        $notice_content     = $this->config->item('ci_front_notice_content');
        $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['notice']     = $notices;
        $data['school']     = $app_name[0];

        if ($this->form_validation->run() == false) {
            $this->load->view('admin/forgotpassword', $data);
        } else {
            $email = $this->input->post('email');

            $result = $this->staff_model->getByEmail($email);

            if ($result && $result->email != "") {
                if ($result->is_active == '1') {
                    $verification_code = $this->enc_lib->encrypt(uniqid(mt_rand()));
                    $update_record     = array('id' => $result->id, 'verification_code' => $verification_code);
                    $this->staff_model->add($update_record);
                    $name           = $result->name;
                    $resetPassLink  = site_url('admin/resetpassword') . "/" . $verification_code;
                    $sender_details = array('resetPassLink' => $resetPassLink, 'name' => $name, 'username' => $result->surname, 'staff_email' => $email);
                    $this->mailsmsconf->mailsms('forgot_password', $sender_details);
                    $this->session->set_flashdata('message', $this->lang->line('please_check_your_email_to_recover_your_password'));
                } else {
                    $this->session->set_flashdata('disable_message', $this->lang->line('your_account_is_disabled_please_contact_to_administrator'));
                }

                redirect('site/login', 'refresh');
            } else {

                $data = array(
                    'error_message' => $this->lang->line('incorrect_email'),
                );
            }
            $this->load->view('admin/forgotpassword', $data);
        }
    }

    //reset password - final step for forgotten password
    public function admin_resetpassword($verification_code = null)
    {
        $app_name     = $this->setting_model->get();
        $data['name'] = $app_name[0]['name'];
        $data['admin_login_page_background'] = $app_name[0]['admin_login_page_background'];
        if (!$verification_code) {
            show_404();
        }

        $user = $this->staff_model->getByVerificationCode($verification_code);
        $notice_content     = $this->config->item('ci_front_notice_content');
        $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['notice']     = $notices;

        if ($user) {
            //if the code is valid then display the password reset form
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'required');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|matches[password]');
            if ($this->form_validation->run() == false) {

                $data['verification_code'] = $verification_code;
                //render
                $this->load->view('admin/admin_resetpassword', $data);
            } else {

                // finally change the password
                $password      = $this->input->post('password');
                $update_record = array(
                    'id'                => $user->id,
                    'password'          => $this->enc_lib->passHashEnc($password),
                    'verification_code' => "",
                );

                $change = $this->staff_model->update($update_record);
                if ($change) {
                    //if the password was successfully changed
                    $this->session->set_flashdata('message', $this->lang->line("password_reset_successfully"));
                    redirect('site/login', 'refresh');
                } else {
                    $this->session->set_flashdata('message', $this->lang->line("something_went_wrong"));
                    redirect('admin_resetpassword/' . $verification_code, 'refresh');
                }
            }
        } else {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->lang->line('invalid_link'));
            redirect("site/forgotpassword", 'refresh');
        }
    }
    //reset password - final step for forgotten password
    public function share($key)
    {
        $data               = array();
        $id                 = $this->enc_lib->dycrypt($key);
        $data['share_data'] = $this->sharecontent_model->getShareContentWithDocuments($id);

        $this->load->view('share', $data);
    }
    //reset password - final step for forgotten password
    public function resetpassword($role = null, $verification_code = null)
    {
        $app_name     = $this->setting_model->get();
        $data['app_name'] = $app_name;
        if (!$role || !$verification_code) {
            show_404();
        }

        $notice_content     = $this->config->item('ci_front_notice_content');
        $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['notice']     = $notices;

        $user = $this->user_model->getUserByCodeUsertype($role, $verification_code);

        if ($user) {
            //if the code is valid then display the password reset form
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'required');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|matches[password]');
            if ($this->form_validation->run() == false) {

                $data['role']              = $role;
                $data['verification_code'] = $verification_code;
                //render
                $this->load->view('resetpassword', $data);
            } else {

                // finally change the password

                $update_record = array(
                    'id'                => $user->user_tbl_id,
                    'password'          => $this->input->post('password'),
                    'verification_code' => "",
                );

                $change = $this->user_model->saveNewPass($update_record);
                if ($change) {
                    //if the password was successfully changed
                    $this->session->set_flashdata('message', $this->lang->line('password_reset_successfully'));
                    redirect('site/userlogin', 'refresh');
                } else {
                    $this->session->set_flashdata('message', $this->lang->line("something_went_wrong"));
                    redirect('user/resetpassword/' . $role . '/' . $verification_code, 'refresh');
                }
            }
        } else {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->lang->line('invalid_link'));
            redirect("site/ufpassword", 'refresh');
        }
    }

    public function ufpassword()
    {

        $notice_content     = $this->config->item('ci_front_notice_content');
        $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['notice']     = $notices;

        $this->form_validation->set_rules('username', $this->lang->line('email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('user[]', $this->lang->line('user_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->load->view('ufpassword', $data);
        } else {
            $email    = $this->input->post('username');
            $usertype = $this->input->post('user[]');
            $result   = $this->user_model->forgotPassword($usertype[0], $email);






            if ($result && $result->email != "") {

                $verification_code = $this->enc_lib->encrypt(uniqid(mt_rand()));
                $update_record     = array('id' => $result->user_tbl_id, 'verification_code' => $verification_code);
                $this->user_model->updateVerCode($update_record);

                if ($usertype[0] == "student") {
                    $name     = $this->customlib->getFullName($result->firstname, $result->middlename, $result->lastname, $this->sch_setting->middlename, $this->sch_setting->lastname);
                    $username = $result->username;
                } else {
                    $name     = $result->guardian_name;
                    $username = $result->username;
                }

                $resetPassLink  = site_url('user/resetpassword') . '/' . $usertype[0] . "/" . $verification_code;
                $sender_details = array('resetPassLink' => $resetPassLink, 'name' => $name, 'username' => $username);
                if ($usertype[0] == "student") {
                    $sender_details['email'] = $email;
                } else {
                    $sender_details['guardian_email'] = $email;
                }
                $this->mailsmsconf->mailsms('forgot_password', $sender_details);
                $this->session->set_flashdata('message', $this->lang->line("please_check_your_email_to_recover_your_password"));
                redirect('site/userlogin', 'refresh');
            } else {
                $data = array(

                    'error_message' => $this->lang->line('invalid_email_or_user_type'),
                );
            }

            $data['notice']     = $notices;

            $this->load->view('ufpassword', $data);
        }
    }

    // public function userlogin()
    // {
    //     $school = $this->setting_model->get();

    //     if (!$school[0]['student_panel_login']) {
    //         redirect('site/login', 'refresh');
    //     }

    //     if ($this->auth->user_logged_in()) {
    //         $this->auth->user_redirect();
    //     }

    //     if ($this->module_lib->hasModule('google_authenticator') 
    //         && $this->module_lib->hasActive('google_authenticator')) {             redirect('gauthenticate/userlogin');     
    //     }

    //     $data               = array();
    //     $data['title']      = 'Login';
    //     $data['name']       = $school[0]['name'];
    //     $notice_content     = $this->config->item('ci_front_notice_content');
    //     $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
    //     $data['notice']     = $notices;
    //     $data['school']     = $school[0];
    //     $is_captcha         = $this->captchalib->is_captcha('userlogin');
    //     $data["is_captcha"] = $is_captcha;
    //     if ($is_captcha) {

    //         if($this->input->post('captcha')){
    //             $this->form_validation->set_rules('captcha', $this->lang->line('captcha'), 'trim|required|callback_check_captcha');
    //         }else{
    //             $this->form_validation->set_rules('captcha', $this->lang->line('captcha'), 'trim|required');
    //         }  

    //     }
    //     $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
    //     if ($this->form_validation->run() == false) {
    //         if ($this->captchalib->is_captcha('userlogin')) {
    //             $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
    //         }
    //         $this->load->view('userlogin', $data);
    //     } else {
    //         $login_post = array(
    //             'username' => $this->input->post('username'),
    //             'password' => $this->input->post('password'),
    //         );
    //         $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
    //         $login_details         = $this->user_model->checkLogin($login_post);

    //         // echo "<pre>";
    //         // print_r($login_details);exit;

    //         if (isset($login_details) && !empty($login_details)) {
    //             $user = $login_details[0];

    //             if ($user->is_active == "yes") {
    //                 if ($user->role == "student") {
    //                     $result = $this->user_model->read_user_information($user->id);

    //                 } else if ($user->role == "parent") {
    //                     if ($school[0]['parent_panel_login']) {
    //                         $result = $this->user_model->checkLoginParent($login_post);


    //                     } else {
    //                         $result = false;

    //                     }

    //                 } 


    //                 if ($result != false) {
    //                     $setting_result = $this->setting_model->get();
    //                     if ($result[0]->lang_id == 0) {
    //                         $language = array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']);
    //                         if ($setting_result[0]['is_rtl'] == 1) {
    //                             $is_rtl = "enabled";
    //                         } else {
    //                             $is_rtl = "disabled";
    //                         }
    //                     } else {
    //                         $language = array('lang_id' => $result[0]->lang_id, 'language' => $result[0]->language);
    //                         if ($setting_result[0]['is_rtl'] == 1) {
    //                             $is_rtl = "enabled";
    //                         } else {
    //                             $is_rtl = "disabled";
    //                         }
    //                     }
    //                     $image = '';
    //                     if ($result[0]->role == "parent") {
    //                         $username = $result[0]->guardian_name;
    //                         if ($result[0]->guardian_is == "father") {
    //                             $image = $result[0]->father_pic;
    //                         } else if ($result[0]->guardian_is == "mother") {
    //                             $image = $result[0]->mother_pic;
    //                         } else if ($result[0]->guardian_is == "other") {
    //                             $image = $result[0]->guardian_pic;
    //                         }
    //                     } elseif ($result[0]->role == "student") {
    //                         $image        = $result[0]->image;
    //                         $username     = $this->customlib->getFullName($result[0]->firstname, $result[0]->middlename, $result[0]->lastname, $this->sch_setting->middlename, $this->sch_setting->lastname);
    //                         $defaultclass = $this->user_model->get_studentdefaultClass($result[0]->user_id);
    //                         $this->customlib->setUserLog($result[0]->username, $result[0]->role, $defaultclass['id']);
    //                     }


    //                     $session_data = array(
    //                         'id'                     => $result[0]->id,
    //                         'login_username'         => $result[0]->username,
    //                         'student_id'             => $result[0]->user_id,
    //                         'role'                   => $result[0]->role,
    //                         'username'               => $username,
    //                         'currency'               => ( $result[0]->currency == 0) ? $setting_result[0]['currency_id']:  $result[0]->currency,
    //                         'currency_base_price'    => ( $result[0]->base_price == 0) ? $setting_result[0]['base_price']:  $result[0]->base_price,
    //                         'currency_format'        => $setting_result[0]['currency_format'],

    //                         'currency_symbol'        => ($result[0]->symbol == "0") ? $setting_result[0]['currency_symbol'] : $result[0]->symbol,


    //                         'currency_name'        => ($result[0]->currency_name == "0") ? $setting_result[0]['currency'] : $result[0]->currency_name,
    //                         'currency_place'         => $setting_result[0]['currency_place'],
    //                         'date_format'            => $setting_result[0]['date_format'],
    //                         'start_week'             => date("w", strtotime($setting_result[0]['start_week'])),
    //                         'timezone'               => $setting_result[0]['timezone'],
    //                         'sch_name'               => $setting_result[0]['name'],
    //                         'language'               => $language,
    //                         'is_rtl'                 => $is_rtl,
    //                         'theme'                  => $setting_result[0]['theme'],
    //                         'image'                  => $image,
    //                         'gender'                 => $result[0]->gender,
    //                         'superadmin_restriction' => $setting_result[0]['superadmin_restriction'],

    //                     );

    //                     $this->session->set_userdata('student', $session_data);
    //                     if ($result[0]->role == "parent") {
    //                         $this->customlib->setUserLog($result[0]->username, $result[0]->role);
    //                     }
    //                     redirect('user/user/choose');
    //                 } else {
    //                     $data['error_message'] = $this->lang->line('account_suspended');
    //                     $this->load->view('userlogin', $data);
    //                 }
    //             } else {
    //                 $data['error_message'] = $this->lang->line('your_account_is_disabled_please_contact_to_administrator');
    //                 $this->load->view('userlogin', $data);
    //             }
    //         } else {
    //             $data['error_message'] = $this->lang->line('invalid_username_or_password');
    //             $this->load->view('userlogin', $data);
    //         }
    //     }
    // }



    public function userlogin()
    {
        // Handle preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        // Read raw JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $_POST = array_merge($_POST, (array) $input);

        $school = $this->setting_model->get();

        if (!$school[0]['student_panel_login']) {
            return $this->output->set_output(json_encode([
                'status'  => false,
                'message' => 'Student login disabled'
            ]));
        }

        // Validation
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => $this->form_validation->error_array()
                ]));
        }

        // Login check
        $login_post = [
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')
        ];

        $login_details = $this->user_model->checkLogin($login_post);

        if (empty($login_details)) {
            return $this->output->set_output(json_encode([
                'status'  => false,
                'message' => 'Invalid username or password'
            ]));
        }

        $user = $login_details[0];

        if ($user->is_active !== 'yes') {
            return $this->output->set_output(json_encode([
                'status'  => false,
                'message' => 'Your account is disabled'
            ]));
        }

        // Role based fetch
        if ($user->role === 'student') {
            $result = $this->user_model->read_user_information($user->id);
        } elseif ($user->role === 'parent' && $school[0]['parent_panel_login']) {
            $result = $this->user_model->checkLoginParent($login_post);
        } else {
            $result = false;
        }

        // echo "<pre>";print_r($result);exit;

        if ($result === false) {
            return $this->output->set_output(json_encode([
                'status'  => false,
                'message' => 'Account suspended'
            ]));
        }

        $setting_result = $this->setting_model->get();

        // Language
        if ($result[0]->lang_id == 0) {
            $language = [
                'lang_id'  => $setting_result[0]['lang_id'],
                'language' => $setting_result[0]['language']
            ];
        } else {
            $language = [
                'lang_id'  => $result[0]->lang_id,
                'language' => $result[0]->language
            ];
        }

        $is_rtl = ($setting_result[0]['is_rtl'] == 1) ? 'enabled' : 'disabled';

        // Username & image
        $image = '';
        if ($result[0]->role === 'parent') {
            $username = $result[0]->guardian_name;
        } else {
            $username = $this->customlib->getFullName(
                $result[0]->firstname,
                $result[0]->middlename,
                $result[0]->lastname,
                $this->sch_setting->middlename,
                $this->sch_setting->lastname
            );
            $defaultclass = $this->user_model->get_studentdefaultClass($result[0]->user_id);
            $this->customlib->setUserLog($result[0]->username, 'student', $defaultclass['id']);
        }

        // Session data
        $session_data = [
            'id'          => $result[0]->id,
            'login_username' => $result[0]->username,
            'student_id'  => $result[0]->user_id,
            'role'        => $result[0]->role,
            'username'    => $username,
            'currency'    => $setting_result[0]['currency_id'],
            'currency_symbol' => $setting_result[0]['currency_symbol'],
            'date_format' => $setting_result[0]['date_format'],
            'timezone'    => $setting_result[0]['timezone'],
            'sch_name'    => $setting_result[0]['name'],
            'language'    => $language,
            'is_rtl'      => $is_rtl,
            'theme'       => $setting_result[0]['theme'],
            'image'       => $image
        ];

        // Set CI session
        $this->session->set_userdata('student', $session_data);

        // ✅ Generate token
        $token      = $this->getToken();
        $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));

        // ✅ Prepare insert data

        $user_id = $result[0]->user_id;

        $parent_id = $result[0]->id;

   

        $insert_data = [
            'login_id'   => $user_id,     // 🔥 VERIFY THIS FIELD
            'parent_id' => $parent_id,
            'token'      => $token,
            'role'       => $result[0]->role,
            'expired_at' => $expired_at
        ];

        // echo "<pre>";print_r($insert_data);exit;

        // ✅ Insert token
        $this->db->insert('admin_authentication', $insert_data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Login successful',
                'role'    => $result[0]->role,
                'token' => $token,
                'expires_at' => $expired_at,
                'data'    => $session_data
            ]));
    }


    public function savemulticlass()
    {

        $student_id = '';
        $this->form_validation->set_rules('student_id', $this->lang->line('student'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'student_id' => form_error('student_id'),
            );

            $array = array('status' => '0', 'error' => $msg, 'message' => '');
        } else {

            $data = array(
                'student_id' => date('Y-m-d', strtotime($this->input->post('student_id'))),
            );

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function check_captcha($captcha)
    {
        if ($captcha != $this->session->userdata('captchaCode')):
            $this->form_validation->set_message('check_captcha', $this->lang->line('incorrect_captcha'));
            return false;
        else:
            return true;
        endif;
    }

    public function refreshCaptcha()
    {
        $captcha = $this->captchalib->generate_captcha();
        echo $captcha['image'];
    }
}

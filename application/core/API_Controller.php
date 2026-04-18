<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // 🚫 Disable sessions completely
        ini_set('session.use_cookies', 0);
        ini_set('session.use_only_cookies', 0);
        ini_set('session.use_trans_sid', 0);

        // 🚫 No HTML errors
        error_reporting(0);
        ini_set('display_errors', 0);

        header('Content-Type: application/json; charset=utf-8');
    }

    protected function response($data, $status = 200)
    {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
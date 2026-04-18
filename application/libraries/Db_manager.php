<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Db_manager
{
    public $connections = array();
    public $CI;
    public $db;

    public function __construct()
    {
        $this->CI = &get_instance();

        // Default DB
        $database_group = 'default';

        if ($this->CI->session->has_userdata('admin')) {

            $database_session = $this->CI->session->userdata('admin');

            // ✅ Check db_array safely
            if (
                isset($database_session['db_array']) &&
                isset($database_session['db_array']['db_group']) &&
                !empty($database_session['db_array']['db_group'])
            ) {
                $database_group = $database_session['db_array']['db_group'];
            }
        }

        // Load DB safely
        $this->CI->db = $this->CI->load->database($database_group, TRUE);
    }

    public function get_connection($db_name)
    {
        $this->connections[$db_name] = $this->CI->load->database($db_name, true);
        return $this->connections[$db_name];
    }
}


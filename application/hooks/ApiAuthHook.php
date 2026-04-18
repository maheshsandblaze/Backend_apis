<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiAuthHook
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function checkSession()
    {
        $uri = $this->CI->uri->uri_string();

        // Allow public endpoints
        $excluded_routes = [
            'api_admin/site/login',
            'api_admin/site/logout',
        ];

        foreach ($excluded_routes as $route) {
            if (strpos($uri, $route) !== false) {
                return;
            }
        }

        // Only protect API routes
        if (strpos($uri, 'api_admin/') === false) {
            return;
        }

        // Check session via RBAC (BEST)
        $userdata = $this->CI->customlib->getUserData();

        if (empty($userdata) || empty($userdata['id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'status'  => false,
                'message' => 'Unauthorized. Please login again.'
            ]);
            exit;
        }
    }
}

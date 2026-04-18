<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sidemenu extends Public_Controller
{

    public $custom_fields_list = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sidebarmenu_model');
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

        $menus        = $this->sidebarmenu_model->getMenuwithSubmenus(0);
        $active_menus = $this->sidebarmenu_model->getMenuwithSubmenus(1);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'       => 'success',
                'menus'        => $menus,
                'active_menus' => $active_menus
            ]));
    }

    public function add_menu()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('menu', $this->lang->line('menu'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('lang_key', $this->lang->line('lang_key'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('activate_menu', 'Active Menu Array key', 'required|trim|xss_clean');
        $this->form_validation->set_rules('icon', 'Icon', 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {
            $sidebar = 0;
            $sidebar_view = $input['sidebar_view'] ?? null;
            if (isset($sidebar_view)) {
                $sidebar = 1;
            }

            $menu_id = $input['menu_id'] ?? 0;
            if ($menu_id == "" || $menu_id == 0) {
                $menu_id = 0;
            }

            $insert_array = array(
                'id'                 => $menu_id,
                'lang_key'           => $input['lang_key'],
                'menu'               => $input['menu'],
                'icon'               => $input['icon'],
                'activate_menu'      => $input['activate_menu'],
                'access_permissions' => $input['access_permissions'] ?? null,
                'sidebar_display'    => $sidebar,
                'level'              => 0,
            );

            $id = $this->sidebarmenu_model->add($insert_array);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message'),
                    'id'      => $id
                ]));
        }
    }

    public function add_sub_menu()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }

        $this->form_validation->set_rules('menu', $this->lang->line('menu'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('url', $this->lang->line('url'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('activate_controller', 'controller', 'required|trim|xss_clean');
        $this->form_validation->set_rules('activate_methods', 'methods', 'required|trim|xss_clean');
        $this->form_validation->set_rules('lang_key', 'Language Key', 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
             return $this->output
                ->set_status_header(422)
                ->set_output(json_encode([
                    'status' => 'fail',
                    'errors' => $this->form_validation->error_array()
                ]));
        } else {
            $submenu_id = $input['submenu_id'] ?? 0;
            if ($submenu_id == "" || $submenu_id == 0) {
                $submenu_id = 0;
            }
            
            $insert_array = array(
                'id'                  => $submenu_id,
                'sidebar_menu_id'     => $input['menu_id'],
                'url'                 => $input['url'],
                'lang_key'            => $input['lang_key'],
                'menu'                => $input['menu'],
                'activate_controller' => $input['activate_controller'],
                'activate_methods'    => $input['activate_methods'],
                'access_permissions'  => $input['access_permissions'] ?? null,
                'addon_permission'    => $input['addon_permission'] ?? null,
                'level'               => 1,
            );
            $id = $this->sidebarmenu_model->addSubMenu($insert_array);
            return $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => $this->lang->line('success_message'),
                    'id'      => $id
                ]));
        }
    }

    public function getmenu()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input   = $this->_get_input();
        $menu_id = $input['menu_id'] ?? null;
        
        if (!$menu_id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Menu ID is required']));
        }

        $menu = $this->sidebarmenu_model->get($menu_id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => 'success',
                'menu'   => $menu
            ]));
    }

    public function getsubmenu()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input      = $this->_get_input();
        $submenu_id = $input['submenu_id'] ?? null;

        if (!$submenu_id) {
             return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'fail', 'message' => 'Submenu ID is required']));
        }

        $sub_menu = $this->sidebarmenu_model->getSubmenuById($submenu_id);
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'   => 'success',
                'sub_menu' => $sub_menu
            ]));
    }

    public function menu_updateorder()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        $input = $this->_get_input();
        $items = $input['items'] ?? [];

        if (!empty($items)) {
            $updateorder        = array();
            $i                  = 1;
            $id_not_to_be_reset = array();
            foreach ($items as $item_key => $item_value) {
                $updateorder[]        = array('id' => $item_value, 'level' => $i, 'sidebar_display' => 1);
                $id_not_to_be_reset[] = $item_value;
                $i++;
            }

            $this->sidebarmenu_model->update_menu_order($updateorder, $id_not_to_be_reset);
        } else {
            $this->sidebarmenu_model->update_menu_order(array(), array(0));
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('update_message')
            ]));
    }

    public function submenu_updateorder()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $items = $input['items'] ?? [];

        if (!empty($items)) {
            $updateorder = array();
            $i           = 1;
            foreach ($items as $item_key => $item_value) {
                $updateorder[] = array('id' => $item_value, 'level' => $i);
                $i++;
            }

            $this->sidebarmenu_model->update_submenu_order($updateorder);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('update_message')
            ]));
    }
}

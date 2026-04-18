<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onlinecourse extends Public_Controller
{
    public $sch_setting_detail = array();
    public function __construct()
    {
        parent::__construct();
        $this->config->load('app-config');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library('mailsmsconf');
        $this->load->library('media_storage');
		$this->load->model('onlinecourse_model');
    }

//     public function index()
//     {
//         if (!$this->rbac->hasPrivilege('online_course', 'can_view')) {
//             access_denied();
//         }
//         $data = array();
//         $this->session->set_userdata('top_menu', 'Online_Course');
//         $this->session->set_userdata('sub_menu', 'Online_Course/Onlinecourse');
//         $category_list           = $this->onlinecourse_model->get_categories();
// 		$data['category_list']  = $category_list;
		
// 		//print_r($category_list);exit;
        
//         $this->load->view('layout/header', $data);
//         $this->load->view('admin/onlinecourse/index', $data);
//         $this->load->view('layout/footer', $data);
//     }



    public function index()
    {
        // Handle CORS preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }

        /* ================= BUSINESS LOGIC ================= */
        $category_list = $this->onlinecourse_model->get_categories();

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Online course categories fetched successfully',
                'data' => [
                    'category_list' => $category_list
                ]
            ]));
    }

	
//     public function list($id)
//     {
//         if (!$this->rbac->hasPrivilege('online_course', 'can_view')) {
//             access_denied();
//         }
//         $data = array();
//         $this->session->set_userdata('top_menu', 'Online_Course');
//         $this->session->set_userdata('sub_menu', 'Online_Course/Onlinecourse');
//         $video_list           = $this->onlinecourse_model->getVideosBycatID($id);
		
// 		$data['category_id']  = $id;
// 		$data['video_list']  = $video_list;
		
// 		//echo $data['category_id'];exit;
        
//         $this->load->view('layout/header', $data);
//         $this->load->view('admin/onlinecourse/list', $data);
//         $this->load->view('layout/footer', $data);
//     }


    public function list()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        /* =======================
           READ JSON INPUT
        ======================== */
        $input = json_decode(file_get_contents('php://input'), true);
    
        // Fallback for form-data
        if (empty($input)) {
            $input = $this->input->post();
        }
    
        $category_id = $input['category_id'] ?? null;
    
        /* =======================
           JWT AUTH
        ======================== */
        // $user = validate_jwt_token();
        // if (!$user) {
        //     return $this->output
        //         ->set_status_header(401)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Unauthorized'
        //         ]));
        // }
    
        /* =======================
           RBAC CHECK
        ======================== */
        // if (!$this->rbac->hasPrivilege('online_course', 'can_view')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        /* =======================
           VALIDATION
        ======================== */
        if (empty($category_id) || !is_numeric($category_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Invalid category ID'
                ]));
        }
    
        /* =======================
           FETCH DATA
        ======================== */
        $video_list = $this->onlinecourse_model->getVideosBycatID($category_id);
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'       => true,
                'category_id'  => (int) $category_id,
                'total_videos' => count($video_list),
                'data'         => $video_list
            ]));
    }
    

    

//     public function add()
//     {
//         $this->form_validation->set_rules('category_name', $this->lang->line('category_title'), 'trim|required|xss_clean');
//         if ($this->form_validation->run() == false) {
//             $msg = array(
//                 'category_name'               => form_error('category_name'),
//             );

//             $array = array('status' => 0, 'error' => $msg, 'message' => '');
//         } else {
//             $insert_data = array(
//                 'category_name'               => $this->input->post('category_name'),
// 				'session_id'                  => $this->setting_model->getCurrentSession(),
//             );
// 			//print_r($insert_data);exit;
//             $this->onlinecourse_model->add($insert_data);
            

//             $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
//         }

//         echo json_encode($array);
//     }

    public function add()
    {
        // Preflight (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // /* =======================
        //   JWT AUTHENTICATION
        // ======================== */
        // $user = validate_jwt_token();
        // if (!$user) {
        //     return $this->output
        //         ->set_status_header(401)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Unauthorized'
        //         ]));
        // }
    
        // /* =======================
        //   RBAC CHECK
        // ======================== */
        // if (!$this->rbac->hasPrivilege('online_course', 'can_add')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status' => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        /* =======================
           READ JSON INPUT
        ======================== */
        $input = json_decode(file_get_contents('php://input'), true);
    
        // Fallback for form-data / x-www-form-urlencoded
        if (empty($input)) {
            $input = $this->input->post();
        }
    
        $_POST = $input; // Required for form_validation
    
        /* =======================
           VALIDATION
        ======================== */
        $this->form_validation->set_rules(
            'category_name',
            $this->lang->line('category_title'),
            'trim|required|xss_clean'
        );
    
        if ($this->form_validation->run() === false) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'errors' => [
                        'category_name' => strip_tags(form_error('category_name'))
                    ],
                    'message' => 'Validation failed'
                ]));
        }
    
        /* =======================
           INSERT DATA
        ======================== */
        $insert_data = [
            'category_name' => $input['category_name'],
            'session_id'    => $this->setting_model->getCurrentSession()
        ];
    
        $insert_id = $this->onlinecourse_model->add($insert_data);
    
        return $this->output
            ->set_status_header(201)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => $this->lang->line('success_message'),
                'id'      => $insert_id
            ]));
    }
  
	
//     public function add_video()
//     {  // echo "1";exit;
//         $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
//       // $this->form_validation->set_rules('type', $this->lang->line('type'), 'trim|required|xss_clean');
//         if ($this->form_validation->run() == false) {
//             $msg = array(
//                 'title'               => form_error('title'),
//                 //'type'               => form_error('type'),
//             );

//             $array = array('status' => 0, 'error' => $msg, 'message' => '');
//         } else {
//             $insert_data = array(
//                 'category_id'               => $this->input->post('category_id'),
//                 'title'               		=> $this->input->post('title'),
//                 //'type'               		=> $this->input->post('type'),
// 				'url'                   	=> $this->input->post('url'),
//             );
// 			//print_r($insert_data);exit;
//             $this->onlinecourse_model->add_video($insert_data);
            

//             $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
//         }

//         echo json_encode($array);
//     }

    
    public function add_video()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        /* =======================
           JWT AUTH
        ======================== */
        // $user = validate_jwt_token();
        // if (!$user) {
        //     return $this->output->set_status_header(401)->set_output(json_encode([
        //         'status' => false,
        //         'message' => 'Unauthorized'
        //     ]));
        // }
    
        /* =======================
           READ JSON INPUT
        ======================== */
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        $_POST = $input;
    
        /* =======================
           VALIDATION
        ======================== */
        $this->form_validation->set_rules('category_id', 'Category', 'required|numeric');
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('url', 'URL', 'required|trim');
    
        if ($this->form_validation->run() === false) {
            return $this->output->set_output(json_encode([
                'status' => false,
                'errors' => $this->form_validation->error_array()
            ]));
        }
    
        /* =======================
           INSERT
        ======================== */
        $data = [
            'category_id' => $input['category_id'],
            'title'       => $input['title'],
            'url'         => $input['url'],
        ];
    
        $this->onlinecourse_model->add_video($data);
    
        return $this->output->set_output(json_encode([
            'status' => true,
            'message' => 'Video added successfully'
        ]));
    }

	
// 	public function edit_video($id)
// 	{
// 		$this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
// 		$this->form_validation->set_rules('url', $this->lang->line('url'), 'trim|required|xss_clean');
	
// 		if ($this->form_validation->run() == false) {
// 			$msg = array(
// 				'title' => form_error('title'),
// 				'url'   => form_error('url'),
// 			);
	
// 			$array = array('status' => 0, 'error' => $msg);
// 		} else {
// 			$update_data = array(
// 				'title' => $this->input->post('title'),
// 				'url'   => $this->input->post('url'),
// 			);
	
// 			$this->onlinecourse_model->update_video($id, $update_data);
	
// 			$array = array('status' => 1, 'error' => '');
// 		}
	
// 		echo json_encode($array);
// 	}


    public function edit_video()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        // $user = validate_jwt_token();
        // if (!$user) {
        //     return $this->output->set_status_header(401)->set_output(json_encode([
        //         'status' => false,
        //         'message' => 'Unauthorized'
        //     ]));
        // }
    
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
        $_POST = $input;
    
        $this->form_validation->set_rules('id', 'Video ID', 'required|numeric');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
    
        if ($this->form_validation->run() === false) {
            return $this->output->set_output(json_encode([
                'status' => false,
                'errors' => $this->form_validation->error_array()
            ]));
        }
    
        $this->onlinecourse_model->update_video($input['id'], [
            'title' => $input['title'],
            'url'   => $input['url'],
        ]);
    
        return $this->output->set_output(json_encode([
            'status' => true,
            'message' => 'Video updated successfully'
        ]));
    }

    
	
// 	public function get_video_details_by_id($id)
// 	{
// 		$this->load->model('onlinecourse_model');
// 		$video = $this->onlinecourse_model->get_video_by_id($id);
// 		echo json_encode($video);
// 	}

    
    public function get_video_details()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        // $user = validate_jwt_token();
        // if (!$user) {
        //     return $this->output->set_status_header(401)->set_output(json_encode([
        //         'status' => false,
        //         'message' => 'Unauthorized'
        //     ]));
        // }
    
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
    
        if (empty($input['id'])) {
            return $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Video ID required'
            ]));
        }
    
        $video = $this->onlinecourse_model->get_video_by_id($input['id']);
    
        return $this->output->set_output(json_encode([
            'status' => true,
            'data' => $video
        ]));
    }

    
	
// 	public function delete_video($id)
// 	{
// 		if (!$this->rbac->hasPrivilege('online_course', 'can_delete')) {
// 			$array = array('status' => 0, 'message' => 'Permission Denied');
// 			echo json_encode($array);
// 			return;
// 		}
	
// 		$this->db->where('id', $id);
// 		$this->db->delete('onlinecourse_videos');
	
// 		if ($this->db->affected_rows() > 0) {
// 			$array = array('status' => 1, 'message' => 'Video deleted successfully.');
// 		} else {
// 			$array = array('status' => 0, 'message' => 'Failed to delete video.');
// 		}
	
// 		echo json_encode($array);
// 	}

    
    public function delete_video()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
        
        // $user = validate_jwt_token();
        // if (!$user) {
        //     return $this->output->set_status_header(401)->set_output(json_encode([
        //         'status' => false,
        //         'message' => 'Unauthorized'
        //     ]));
        // }
    
        // if (!$this->rbac->hasPrivilege('online_course', 'can_delete')) {
        //     return $this->output->set_status_header(403)->set_output(json_encode([
        //         'status' => false,
        //         'message' => 'Permission Denied'
        //     ]));
        // }
    
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $this->input->post();
        }
    
        if (empty($input['id'])) {
            return $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Video ID required'
            ]));
        }
    
        $this->db->delete('onlinecourse_videos', ['id' => $input['id']]);
    
        return $this->output->set_output(json_encode([
            'status' => true,
            'message' => 'Video deleted successfully'
        ]));
    }



    

}

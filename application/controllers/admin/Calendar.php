<?php

class Calendar extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('customlib');
        $this->load->model("calendar_model");
        $this->load->library('pagination');
    }

    // public function events()
    // {

    //     if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) {
    //         access_denied();
    //     }
    //     $userdata                  = $this->customlib->getUserData();
    //     $data["role"]              = $userdata["user_type"];
    //     $event_colors              = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
    //     $data["event_colors"]      = $event_colors;
    //     $config['base_url']        = base_url() . 'admin/calendar/events';
    //     $config['total_rows']      = $this->calendar_model->countrows($userdata["id"], $userdata["role_id"]);
    //     $config['per_page']        = 10;
    //     $config["full_tag_open"]   = '<ul class="pagination">';
    //     $config["full_tag_close"]  = '</ul>';
    //     $config["first_link"]      = "&laquo;";
    //     $config["first_tag_open"]  = "<li>";
    //     $config["first_tag_close"] = "</li>";
    //     $config["last_link"]       = "&raquo;";
    //     $config["last_tag_open"]   = "<li>";
    //     $config["last_tag_close"]  = "</li>";
    //     $config['next_link']       = '&gt;';
    //     $config['next_tag_open']   = '<li>';
    //     $config['next_tag_close']  = '<li>';
    //     $config['prev_link']       = '&lt;';
    //     $config['prev_tag_open']   = '<li>';
    //     $config['prev_tag_close']  = '<li>';
    //     $config['cur_tag_open']    = '<li class="active"><a href="#">';
    //     $config['cur_tag_close']   = '</a></li>';
    //     $config['num_tag_open']    = '<li>';
    //     $config['num_tag_close']   = '</li>';
    //     $this->pagination->initialize($config);

    //     $tasklist = $this->calendar_model->getTask($userdata["id"], $userdata["role_id"], 10, $this->uri->segment(4));

    //     $data["tasklist"] = $tasklist;
    //     $data["title"]    = $this->lang->line("event_calendar");
    //     $this->load->view("layout/header.php");
    //     $this->load->view("setting/eventcalendar", $data);
    //     $this->load->view("layout/footer.php");
    // }
    
    
    // public function events()
    // {
    //     /* =========================
    //       CORS
    //     ========================== */
        
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         exit;
    //     }
        
    //     // Allow only GET
    //     if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    //         return $this->output
    //             ->set_status_header(405)
    //             ->set_content_type('application/json')
    //             ->set_output(json_encode([
    //                 'status'  => false,
    //                 'message' => 'Method Not Allowed'
    //             ]));
    //     }
    
    //     // Privilege check
    //     // if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) {
    //     //     return $this->output
    //     //         ->set_status_header(403)
    //     //         ->set_content_type('application/json')
    //     //         ->set_output(json_encode([
    //     //             'status'  => false,
    //     //             'message' => 'Access Denied'
    //     //         ]));
    //     // }
    
    //     $userdata = $this->customlib->getUserData();
    
    //     // GET params
    //     $page  = (int) $this->input->get('page') ?: 1;
    //     $limit = (int) $this->input->get('limit') ?: 10;
    //     $offset = ($page - 1) * $limit;
    
    //     // Colors
    //     $event_colors = [
    //         "#03a9f4", "#c53da9", "#757575", "#8e24aa",
    //         "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b"
    //     ];
    
    //     // Total count
    //     $total_rows = $this->calendar_model->countrows(
    //         $userdata["id"],
    //         $userdata["role_id"]
    //     );
    
    //     // Fetch events
    //     $tasklist = $this->calendar_model->getTask(
    //         $userdata["id"],
    //         $userdata["role_id"],
    //         $limit,
    //         $offset
    //     );
    
    //     return $this->output
    //         ->set_content_type('application/json')
    //         ->set_output(json_encode([
    //             'status'        => true,
    //             'title'         => $this->lang->line("event_calendar"),
    //             'role'          => $userdata["user_type"],
    //             'event_colors'  => $event_colors,
    //             'pagination'    => [
    //                 'page'        => $page,
    //                 'limit'       => $limit,
    //                 'total_rows'  => $total_rows,
    //                 'total_pages' => ceil($total_rows / $limit)
    //             ],
    //             'data' => $tasklist
    //         ]));
    // }
    
    
    public function events($id = null, $role_id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    
        // Allow only GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Method Not Allowed'
                ]));
        }
    
        // Validate URI params
        if (empty($id) || empty($role_id)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'id and role_id are required'
                ]));
        }
    
        // Pagination (optional query params)
        $page   = (int) $this->input->get('page') ?: 1;
        $limit  = (int) $this->input->get('limit') ?: 10;
        $offset = ($page - 1) * $limit;
    
        // Colors
        $event_colors = [
            "#03a9f4", "#c53da9", "#757575", "#8e24aa",
            "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b"
        ];
    
        // Total rows
        $total_rows = $this->calendar_model->countrows($id, $role_id);
    
        // Fetch events
        $tasklist = $this->calendar_model->getTask(
            $id,
            $role_id,
            $limit,
            $offset
        );
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'       => true,
                'event_colors' => $event_colors,
                'pagination'   => [
                    'page'        => $page,
                    'limit'       => $limit,
                    'total_rows'  => $total_rows,
                    'total_pages' => ceil($total_rows / $limit)
                ],
                'data' => $tasklist
            ]));
    }
    

    // public function addtodo()
    // {
    //     if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) {
    //         access_denied();
    //     }

    //     $this->form_validation->set_rules('task_title', $this->lang->line('task_title'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('task_date', $this->lang->line('date'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {

    //         $msg = array(
    //             'task_title' => form_error('task_title'),
    //             'task_date'  => form_error('task_date'),
    //         );
    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {

    //         $userdata          = $this->customlib->getUserData();
    //         $event_title       = $this->input->post("task_title");
    //         $event_description = '';
    //         $event_type        = 'task';
    //         $event_color       = '#000';
    //         $date              = $this->input->post('task_date');
    //         $start_date        = date('Y-m-d H:i:s', $this->customlib->datetostrtotime($this->input->post('task_date')));
    //         $eventid           = $this->input->post("eventid");
    //         if (!empty($eventid)) {

    //             $eventdata = array('event_title' => $event_title,
    //                 'event_description'              => $event_description,
    //                 'start_date'                     => $start_date,
    //                 'end_date'                       => $start_date,
    //                 'event_type'                     => $event_type,
    //                 'event_color'                    => $event_color,
    //                 'event_for'                      => $userdata["id"],
    //                 'id'                             => $eventid,
    //             );
    //             $msg = $this->lang->line('update_message');
    //         } else {
    //             $eventdata = array('event_title' => $event_title,
    //                 'event_description'              => $event_description,
    //                 'start_date'                     => $start_date,
    //                 'end_date'                       => $start_date,
    //                 'event_type'                     => $event_type,
    //                 'event_color'                    => $event_color,
    //                 'is_active'                      => "no",
    //                 'event_for'                      => $userdata["id"],
    //                 'role_id'                        => $userdata["role_id"],
    //             );
    //             $msg = $this->lang->line('success_message');
    //         }

    //         $this->calendar_model->saveEvent($eventdata);
    //         $array = array('status' => 'success', 'error' => '', 'message' => $msg);
    //     }

    //     echo json_encode($array);
    // }
    
    
    public function addtodo()
    {
        /* =========================
           CORS
        ========================== */
        
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
    
        // Privilege check
        // if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status'  => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        // Read JSON or form-data
        $input = json_decode(trim(file_get_contents('php://input')), true);
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }
    
        // Validation
        $this->form_validation->set_rules('task_title', $this->lang->line('task_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('task_date',  $this->lang->line('date'),       'trim|required|xss_clean');
    
        if ($this->form_validation->run() === false) {
    
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error'  => [
                        'task_title' => form_error('task_title'),
                        'task_date'  => form_error('task_date')
                    ],
                    'message' => ''
                ]));
        }
    
        // Business logic
        $userdata    = $this->customlib->getUserData();
        $eventid     = $input['eventid'] ?? null;
        $event_title = $input['task_title'];
        $task_date   = $input['task_date'];
    
        $start_date = date(
            'Y-m-d H:i:s',
            $this->customlib->datetostrtotime($task_date)
        );
    
        $eventdata = [
            'event_title'       => $event_title,
            'event_description' => '',
            'start_date'        => $start_date,
            'end_date'          => $start_date,
            'event_type'        => 'task',
            'event_color'       => '#000',
            'event_for'         => $input['staff_id'],
            // 'event_for'         => $userdata['id'],
        ];
    
        if (!empty($eventid)) {
            // Update
            $eventdata['id'] = $eventid;
            $message = $this->lang->line('update_message');
        } else {
            // Create
            $eventdata['is_active'] = 'no';
            $eventdata['role_id']  = $input['role_id'];
            $message = $this->lang->line('success_message');
        }
    
        $this->calendar_model->saveEvent($eventdata);
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $message
            ]));
    }
    

    // public function saveevent()
    // {
    //     $this->form_validation->set_rules('title', $this->lang->line('event_title'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('event_from', $this->lang->line('event_from'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('event_to', $this->lang->line('event_to'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {

    //         $msg = array(
    //             'title'      => form_error('title'),
    //             'event_from' => form_error('event_from'),
    //             'event_to'   => form_error('event_to'),
    //         );
    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {
    //         $event_title       = $this->input->post("title");
    //         $event_description = $this->input->post("description");
    //         $event_type        = $this->input->post("event_type");
    //         $event_color       = $this->input->post("eventcolor");
    //         if (empty($event_color)) {
    //             $event_color = '#337ab7';
    //         }

    //         $start_date = date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post("event_from")));
    //         $end_date   = date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post("event_to")));

    //         $userdata = $this->customlib->getUserData();
    //         if ($event_type == 'private') {

    //             $event_for = $userdata["id"];
    //         } else if ($event_type == 'sameforall') {

    //             $event_for = $userdata["role_id"];
    //         } else if ($event_type == 'public') {

    //             $event_for = "0";
    //         } else if ($event_type == 'protected') {

    //             $event_for = $userdata["role_id"];
    //         }
    //         $eventdata = array('event_title' => $event_title,
    //             'event_description'              => $event_description,
    //             'start_date'                     => $start_date,
    //             'end_date'                       => $end_date,
    //             'event_type'                     => $event_type,
    //             'event_color'                    => $event_color,
    //             'event_for'                      => $event_for,
    //         );

    //         $this->calendar_model->saveEvent($eventdata);

    //         $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

    //     }
    //     echo json_encode($array);
    // }
    
    
    public function saveevent()
    {
        /* =========================
           CORS
        ========================== */
        
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
    
        // Read JSON input or fallback to form-data
        $input = json_decode(trim(file_get_contents('php://input')), true);
        if (!empty($input)) {
            $this->form_validation->set_data($input);
        }
    
        // Validation rules
        $this->form_validation->set_rules('title', $this->lang->line('event_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('event_from', $this->lang->line('event_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('event_to', $this->lang->line('event_to'), 'trim|required|xss_clean');
    
        if ($this->form_validation->run() === false) {
    
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'error'   => $this->form_validation->error_array(),
                    'message' => ''
                ]));
        }
    
        // Read inputs
        $title       = $input['title'] ?? $this->input->post('title');
        $description = $input['description'] ?? $this->input->post('description');
        $event_type  = $input['event_type'] ?? $this->input->post('event_type');
        $event_color = $input['eventcolor'] ?? $this->input->post('eventcolor');
    
        if (empty($event_color)) {
            $event_color = '#337ab7';
        }
    
        $start_date = date(
            'Y-m-d H:i:s',
            $this->customlib->dateTimeformatTwentyfourhour($input['event_from'] ?? $this->input->post('event_from'))
        );
    
        $end_date = date(
            'Y-m-d H:i:s',
            $this->customlib->dateTimeformatTwentyfourhour($input['event_to'] ?? $this->input->post('event_to'))
        );
    
        // User data
        $userdata = $this->customlib->getUserData();
    
        // Event visibility logic
        switch ($event_type) {
            case 'private':
                $event_for = $input['staff_id'];
                break;
            case 'sameforall':
            case 'protected':
                $event_for = $input['role_id'];
                break;
            case 'public':
            default:
                $event_for = '0';
                break;
        }
    
        // Save event
        $eventdata = [
            'event_title'       => $title,
            'event_description' => $description,
            'start_date'        => $start_date,
            'end_date'          => $end_date,
            'event_type'        => $event_type,
            'event_color'       => $event_color,
            'event_for'         => $event_for,
        ];
    
        $this->calendar_model->saveEvent($eventdata);
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('success_message')
            ]));
    }
    

    // public function updateevent()
    // {
    //     if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) {
    //         access_denied();
    //     }
    //     $this->form_validation->set_rules('title', $this->lang->line('event_title'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('event_from', $this->lang->line('event_from'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('event_to', $this->lang->line('event_to'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {

    //         $msg = array(
    //             'title'      => form_error('title'),
    //             'event_from' => form_error('event_from'),
    //             'event_to'   => form_error('event_to'),
    //         );
    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {
    //         $event_title       = $this->input->post("title");
    //         $event_description = $this->input->post("description");
    //         $event_type        = $this->input->post("eventtype");
    //         $event_color       = $this->input->post("eventcolor");
    //         $id                = $this->input->post("eventid");

    //         $event_for = "";
    //         $userdata  = $this->customlib->getUserData();
    //         if ($event_type == 'private') {

    //             $event_for = $userdata["id"];
    //         } else if ($event_type == 'sameforall') {

    //             $event_for = $userdata["role_id"];
    //         } else if ($event_type == 'public') {

    //             $event_for = "0";
    //         } else if ($event_type == 'protected') {

    //             $event_for = $userdata["role_id"];
    //         }

    //         $start_date = date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post("event_from")));
    //         $end_date   = date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post("event_to")));
    //         // $this->customlib->dateFormatToYYYYMMDD($this->input->post('event_to'));
            
            
    //         $eventdata = array('id' => $id,
    //             'event_title'           => $event_title,
    //             'event_description'     => $event_description,
    //             'start_date'            => $start_date,
    //             'end_date'              => $end_date,
    //             'event_type'            => $event_type,
    //             'event_color'           => $event_color,
    //             'event_for'             => $event_for,
    //         );

    //         $this->calendar_model->saveEvent($eventdata);
    //         $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

    //     }
    //     echo json_encode($array);
    // }
    
    
    public function updateevent()
    {
        /* =========================
           CORS
        ========================== */
        
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
    
        // if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) {
        //     access_denied();
        // }
    
        // Read raw JSON body
        $input = json_decode(trim(file_get_contents('php://input')), true);
    
        // Merge JSON + form-data support
        $title       = $input['title']       ?? $this->input->post('title');
        $event_from  = $input['event_from']  ?? $this->input->post('event_from');
        $event_to    = $input['event_to']    ?? $this->input->post('event_to');
        $description = $input['description'] ?? $this->input->post('description');
        $event_type  = $input['eventtype']   ?? $this->input->post('eventtype');
        $event_color = $input['eventcolor']  ?? $this->input->post('eventcolor');
        $event_id    = $input['eventid']     ?? $this->input->post('eventid');
    
        // Validation
        $errors = [];
        if (empty($title)) {
            $errors['title'] = 'Event title is required';
        }
        if (empty($event_from)) {
            $errors['event_from'] = 'Event from date is required';
        }
        if (empty($event_to)) {
            $errors['event_to'] = 'Event to date is required';
        }
        if (empty($event_id)) {
            $errors['eventid'] = 'Event ID is required';
        }
    
        if (!empty($errors)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'error'  => $errors
                ]));
        }
    
        // Determine event_for
        $userdata = $this->customlib->getUserData();
        switch ($event_type) {
            case 'private':
                $event_for = $input['staff_id'];
                break;
            case 'sameforall':
            case 'protected':
                $event_for = $input['role_id'];
                break;
            case 'public':
                $event_for = "0";
                break;
            default:
                $event_for = $userdata['id'];
        }
    
        // Date formatting
        $start_date = date(
            'Y-m-d H:i:s',
            $this->customlib->dateTimeformatTwentyfourhour($event_from)
        );
    
        $end_date = date(
            'Y-m-d H:i:s',
            $this->customlib->dateTimeformatTwentyfourhour($event_to)
        );
    
        // Update payload
        $eventdata = [
            'id'                => $event_id,
            'event_title'       => $title,
            'event_description' => $description,
            'start_date'        => $start_date,
            'end_date'          => $end_date,
            'event_type'        => $event_type,
            'event_color'       => $event_color,
            'event_for'         => $event_for,
        ];
    
        $this->calendar_model->saveEvent($eventdata);
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('success_message')
            ]));
    }
    

    public function getevents()
    {
        $userdata = $this->customlib->getUserData();
        $result   = $this->calendar_model->getEvents();
        if (!empty($result)) {

            foreach ($result as $key => $value) {

                $event_type = $value["event_type"];

                if ($event_type == 'private') {

                    $event_for = $userdata["id"];
                } else if ($event_type == 'sameforall') {

                    $event_for = $userdata["role_id"];
                } else if ($event_type == 'public') {

                    $event_for = "0";
                } else if ($event_type == 'task') {

                    $event_for = $userdata["id"];
                }

                if ($event_type == 'task') {

                    if (($event_for == $value["event_for"]) && ($value["role_id"] == $userdata["role_id"])) {
                        $eventdata[] = array('title' => $value["event_title"],
                            'start'                      => $value["start_date"],
                            'end'                        => $value["end_date"],
                            'description'                => $value["event_description"],
                            'id'                         => $value["id"],
                            'backgroundColor'            => $value["event_color"],
                            'borderColor'                => $value["event_color"],
                            'event_type'                 => $value["event_type"],
                        );
                    }
                } else {
                    if ($event_for == $value["event_for"]) {
                        $eventdata[] = array('title' => $value["event_title"],
                            'start'                      => $value["start_date"],
                            'end'                        => $value["end_date"],
                            'description'                => $value["event_description"],
                            'id'                         => $value["id"],
                            'backgroundColor'            => $value["event_color"],
                            'borderColor'                => $value["event_color"],
                            'event_type'                 => $value["event_type"],
                        );
                    } elseif ($event_type == 'protected') {
                        $eventdata[] = array('title' => $value["event_title"],
                            'start'                      => $value["start_date"],
                            'end'                        => $value["end_date"],
                            'description'                => $value["event_description"],
                            'id'                         => $value["id"],
                            'backgroundColor'            => $value["event_color"],
                            'borderColor'                => $value["event_color"],
                            'event_type'                 => $value["event_type"],
                        );
                    }

                }
            }

            echo json_encode($eventdata);
        }
    }

    // public function view_event($id)
    // {
    //     if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) {
    //         access_denied();
    //     }
    //     $result              = $this->calendar_model->getEvents($id);
    //     $start_date          = date($this->customlib->getSchoolDateFormat() . " H:i:s", strtotime($result["start_date"]));
    //     $end_date            = date($this->customlib->getSchoolDateFormat() . " H:i:s", strtotime($result["end_date"]));
    //     $colorid             = trim($result["event_color"], "#");
    //     $result["colorid"]   = $colorid;
    //     $result["startdate"] = $start_date;
    //     $result["enddate"]   = $end_date;

    //     echo json_encode($result);
    // }
    
    
    public function view_event()
    {
        /* =========================
           CORS
        ========================== */
        
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
    
        // if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) {
        //     access_denied();
        // }
    
        // Read raw JSON
        $input = json_decode(trim(file_get_contents('php://input')), true);
    
        // Fallback to form-data
        $event_id = $input['event_id'] ?? $this->input->post('event_id');
    
        if (empty($event_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'event_id is required'
                ]));
        }
    
        $result = $this->calendar_model->getEvents($event_id);
    
        if (empty($result)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Event not found'
                ]));
        }
    
        $start_date = date(
            $this->customlib->getSchoolDateFormat() . " H:i:s",
            strtotime($result["start_date"])
        );
    
        $end_date = date(
            $this->customlib->getSchoolDateFormat() . " H:i:s",
            strtotime($result["end_date"])
        );
    
        $result["colorid"]   = trim($result["event_color"], "#");
        $result["startdate"] = $start_date;
        $result["enddate"]   = $end_date;
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
    

    // public function delete_event($id)
    // {
    //     if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) {
    //         access_denied();
    //     }
    //     if (!empty($id)) {

    //         $result = $this->calendar_model->deleteEvent($id);
    //         $array  = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
    //     } else {

    //         $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('cannot_delete_this_event'));
    //     }
    //     echo json_encode($array);
    // }
    
    
    public function delete_event()
    {
        /* =========================
           CORS
        ========================== */
        
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
    
        // Privilege check
        // if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status'  => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        // Read JSON body OR form-data
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $id    = $input['id'] ?? $this->input->post('id');
    
        if (empty($id)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => $this->lang->line('cannot_delete_this_event')
                ]));
        }
    
        // Delete event
        $this->calendar_model->deleteEvent($id);
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('delete_message')
            ]));
    }
    

    // public function gettaskbyid($id)
    // {
    //     if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) {
    //         access_denied();
    //     }

    //     $result = $this->calendar_model->getEvents($id);

    //     echo json_encode($result);
    // }
    
    
    public function gettaskbyid()
    {
        /* =========================
           CORS
        ========================== */
        
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
    
        // Privilege check
        // if (!$this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) {
        //     return $this->output
        //         ->set_status_header(403)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status'  => false,
        //             'message' => 'Access Denied'
        //         ]));
        // }
    
        // Read JSON or form-data
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $task_id = $input['id'] ?? $this->input->post('id');
    
        if (empty($task_id)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Task ID is required'
                ]));
        }
    
        // Fetch task
        $result = $this->calendar_model->getEvents($task_id);
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $result
            ]));
    }
    

    // public function markcomplete($id)
    // {
    //     $status = $this->input->post("active");

    //     $eventdata = array('is_active' => $status, 'id' => $id);
    //     if (!empty($id)) {

    //         $this->calendar_model->saveEvent($eventdata);
    //         $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('mark_completed_successfully'));
    //     } else {

    //         $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('cannot_mark_complete_this_event'));
    //     }
    //     echo json_encode($array);
    // }
    
    
    public function markcomplete()
    {
        /* =========================
           CORS
        ========================== */
        
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
    
        // Read raw JSON body
        $input = json_decode(trim(file_get_contents('php://input')), true);
    
        // Support JSON + form-data
        $id     = $input['id']     ?? $this->input->post('id');
        $status = $input['active'] ?? $this->input->post('active');
    
        // Validation
        if (empty($id)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'fail',
                    'message' => $this->lang->line('cannot_mark_complete_this_event')
                ]));
        }
    
        // Update data
        $eventdata = [
            'id'        => $id,
            'is_active' => $status
        ];
    
        $this->calendar_model->saveEvent($eventdata);
    
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $this->lang->line('mark_completed_successfully')
            ]));
    }

}

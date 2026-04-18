<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feereminder extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function _get_input()
    {
        $input = $this->input->post();
        if (empty($input)) {
            $input = json_decode($this->input->raw_input_stream, true);
        }
        return $input ?: [];
    }

    public function setting()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $input = $this->_get_input();
        $feereminderlist = $this->feereminder_model->get();

        if ($this->input->server('REQUEST_METHOD') == "POST" && !empty($input)) {
            $ids = $input['ids'] ?? [];
            $update_array = array();
            
            foreach ($ids as $id_value) {
                $array = array(
                    'id'        => $id_value,
                    'is_active' => 0,
                    'day'       => $input['days' . $id_value] ?? 0,
                );
                
                $is_active = $input['isactive_' . $id_value] ?? null;

                if (isset($is_active)) {
                    $array['is_active'] = $is_active;
                }

                $update_array[] = $array;
            }

            if (!empty($update_array)) {
                $this->feereminder_model->updatebatch($update_array);
                return $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'status'  => 'success',
                        'message' => $this->lang->line('update_message')
                    ]));
            } else {
                return $this->output
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'status'  => 'error',
                        'message' => 'No data provided for update'
                    ]));
            }
        }

        // Return the list if it's a GET request or if no update was performed
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'status'           => 'success',
                'feereminderlist' => $feereminderlist
            ]));
    }

}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class Calendar extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        //チャットボット
        $this->header['page'] = 'schedule';
        $this->header['title'] = '管理画面【企業用】';
        $this->header['user'] = $this->user;

        // $this->load->model('scenario_model');
        // $this->load->model('bot_model');
        $this->load->model('user_model');
        $this->load->model('attendance_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        
        $cond_date = $this->input->post('cond_date');

        if(empty($cond_date)) {
            $cond_date = date('Y-m-d');
        }
        $this->data['cond_date'] = $cond_date;

        $this->_load_view("calendar/index");
    }

    public function get_members() 
    {
        $this->data = array(
            'today_date' => date("Y-m-d"),
        );
        $attendance_data = $this->attendance_model->get_today_data($this->data);

        $data = array();
        foreach ($attendance_data AS $attendance) {
            $data[] = [
                "staff_id" => $attendance->staff_id,
                "staff_name" => $attendance->staff_name
            ];
        }
        echo json_encode($data);
    }

    public function get_events() 
    {

        echo json_encode([
          [
            "id" => "1",
            "title" => "山田1",
            "start" => "2025-04-10T10:00:00",
            "end" => "2025-04-10T11:00:00",
            "member_id" => "1"
          ],
          [
            "id" => "2",
            "title" => "山田2",
            "start" => "2025-04-10T13:00:00",
            "end" => "2025-04-10T14:00:00",
            "member_id" => "2"
          ],
          [
            "id" => "3",
            "title" => "山田3",
            "start" => "2025-04-10T10:00:00",
            "end" => "2025-04-10T12:00:00",
            "member_id" => "3"
          ],
          [
            "id" => "4",
            "title" => "山田4",
            "start" => "2025-04-10T11:00:00",
            "end" => "2025-04-10T14:00:00",
            "member_id" => "4"
          ]
        ]);
    }

    public function edit()
    {
        $this->data['month'] = $this->input->post('month') ?: date('n');
        $a_date = date('Y-').$this->data['month'].'-01';

        $this->data['days'] = date("t", strtotime($a_date));
        
        $this->_load_view("calendar/edit");
    }

}

?>
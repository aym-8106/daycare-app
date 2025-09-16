<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class Schedule extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        //チャットボット
        $this->header['page'] = 'schedule';
        $this->header['title'] = 'CareNavi訪問看護';
        $this->header['user'] = $this->user;

        // $this->load->model('scenario_model');
        // $this->load->model('bot_model');
        $this->load->model('user_model');
        $this->load->model('attendance_model');
        $this->load->model('Settingstaff_model');
        $this->load->model('patient_model');
        $this->load->model('schedule_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        
        $cond_date = $this->input->post('cond_date');
        if(empty($cond_date)) $cond_date = date('Y-m-d');
        $this->data['cond_date'] = $cond_date;

        $weekday = date('w', strtotime($this->data['cond_date']));
        $this->data['patient'] = $this->schedule_model->get_patient_data_today($this->data['cond_date'], $weekday);
        // print_r($this->data['patient']);die;
        $this->data['schedule_data'] = $this->schedule_model->get_schedules_by_date($this->data['cond_date']);

        $this->_load_view("schedule/index");
    }

    public function get_members() 
    {
        $user_data = $this->session->userdata('staff');
        $this->data = array(
            'today_date' => date("Y-m-d"),
            'staff_id' => $user_data['staff_id']
        );
        $attendance_data = $this->Settingstaff_model->get_today_data($this->data);

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
      $cond['member_id'] = $this->input->get('member_id');
      $cond['cond_date'] = $this->input->get('cond_date');

      $attendance_data = $this->patient_model->get_today_data($cond);
      echo json_encode([
          [
            "id" => "1",
            "title" => "山田1",
            "start" => "2025-04-12T10:00:00",
            "end" => "2025-04-12T11:00:00",
            "member_id" => $member_id
          ]
        ]);
    }

    public function add_event() {
        $rawData = file_get_contents('php://input');

        // Check if we have valid JSON data
        if (empty($rawData)) {
            echo json_encode(['status' => 'error', 'message' => 'No data received']);
            exit;
        }

        $data = json_decode($rawData, true);

        // Check if the required fields are set
        if (empty($data['date']) || empty($data['staff_id']) || empty($data['patient_id']) || empty($data['start_time']) || empty($data['end_time'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        
        $schedule_id = $data['schedule_id'];
        $schedule_date = $data['date'];
        $staff_id = $data['staff_id'];
        $patient_id = $data['patient_id'];
        $schedule_start_time = $data['start_time'];
        $schedule_end_time = $data['end_time'];

        // Check if the event with the same data already exists
        $this->db->where('schedule_date', $schedule_date);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('patient_id', $patient_id);
        $this->db->where('schedule_start_time', $schedule_start_time);
        $this->db->where('schedule_end_time', $schedule_end_time);
        $query = $this->db->get('tbl_schedule');

        // If the record exists, update it
        if ($query->num_rows() > 0) {
            $existing_event = $query->row();

            // You may update the event with any changes needed (e.g., changing the time)
            $updateData = [
                'schedule_date' => $schedule_date,
                'staff_id' => $staff_id,
                'patient_id' => $patient_id,
                'schedule_start_time' => $schedule_start_time,
                'schedule_end_time' => $schedule_end_time
            ];

            // Update the existing event
            $this->db->where('id', $existing_event->id);
            $this->db->update('tbl_schedule', $updateData);

            $updatedId = $existing_event->id;
            echo json_encode(['id' => $updatedId, 'status' => 'updated']);
        } else {
            // If no record exists, insert a new event
            $insertData = [
                'schedule_date' => $schedule_date,
                'staff_id' => $staff_id,
                'patient_id' => $patient_id,
                'schedule_start_time' => $schedule_start_time,
                'schedule_end_time' => $schedule_end_time
            ];

            $this->db->insert('tbl_schedule', $insertData);
            $insertedId = $this->db->insert_id();
            $this->schedule_model->delete_force($schedule_id, 'id');
            echo json_encode(['id' => $insertedId, 'status' => 'inserted']);
        }

        exit;
    }

    public function update_event_time()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        $id = $data['id'] ?? null;
        $staffId = $data['staff_id'] ?? null;
        $startTime = $data['start_time'] ?? null;
        $endTime = $data['end_time'] ?? null;

        if (!$id || !$staffId || !$startTime || !$endTime) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
            exit;
        }

        // Check if event with given ID exists
        $query = $this->db->get_where('tbl_schedule', ['id' => $id]);
        if ($query->num_rows() === 0) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Event not found']);
            exit;
        }

        // Update existing event
        $updateData = [
            'staff_id'            => $staffId,
            'schedule_start_time' => $startTime,
            'schedule_end_time'   => $endTime
        ];

        $this->db->where('id', $id);
        $updated = $this->db->update('tbl_schedule', $updateData);

        if ($updated) {
            echo json_encode(['id' => $id]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }

        exit;
    }

    public function delete_event()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        $id = $data['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false]);
            return;
        }
    
        $this->db->where('id', $id);
        $updated = $this->db->delete('tbl_schedule', ['id' => $id]);
    
        echo json_encode(['success' => true]);
    }

    public function edit()
    {
        $this->data['month'] = $this->input->post('month') ?: date('n');
        $a_date = date('Y-').$this->data['month'].'-01';

        $this->data['days'] = date("t", strtotime($a_date));
        
        $this->_load_view("schedule/edit");
    }

    public function get_schedule_by_date() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
    
        $date = $data['date'] ?? date('Y-m-d');
        $schedule_data = $this->schedule_model->get_schedules_by_date($date);
    
        echo json_encode($schedule_data);
    }

}

?>
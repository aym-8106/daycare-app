<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/CompanyController.php';

class Shift extends CompanyController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_COMPANY);

        //チャットボット
        $this->header['page'] = 'shift';
        $this->header['title'] = 'CareNavi訪問看護';

        $this->load->model('user_model');
        $this->load->model('schedule_model');
        $this->load->model('patient_model');
        $this->load->model('company_model');
        $this->load->model('staff_model');
        $this->load->model('shift_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        $this->data['today'] = date('Y-m-d');
        $this->data['loggedin_user'] = $this->user;

        $this->data['cond_date'] = $this->input->post('cond_date');

        if($this->data['cond_date']) {
            $this->data['cond_date'] = date('Y-m', strtotime($this->data['cond_date']));
        } else {
            $this->data['cond_date'] = date('Y-m', strtotime($this->data['today']));
        }

        $this->data['data_list'] = $this->shift_model->getShiftByCond($this->data['cond_date']);

        $this->_load_view_company("company/shift/index");
    }

    public function saveShift()
    {
        // Check if request is AJAX
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Direct access not allowed']);
            return;
        }
        
        // Get input parameters
        $staff_id = $this->input->post('staff_id');
        $shift_date = $this->input->post('shift_date');
        $shift_option = $this->input->post('shift_option');
        
        // Validate required parameters
        if (empty($staff_id) || empty($shift_date)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
            return;
        }
        
        // Prepare data for database update
        $shift_data = [
            'company_id' => $this->user['company_id'],
            'staff_id' => $staff_id,
            'shift_date' => $shift_date,
            'shift_option' => $shift_option,
        ];
        
        // Check if record already exists
        $existing_record = $this->shift_model->getShiftByStaffAndDate($staff_id, $shift_date);
        
        // Update or insert shift data
        if ($existing_record) {
            // Update existing record
            $result = $this->shift_model->updateShift($existing_record['shift_id'], $shift_data);
        } else {
            // Insert new record
            $result = $this->shift_model->addNewShift($shift_data);
        }
        
        // Return response
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Shift updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update shift']);
        }
    }

    public function saveoncall()
    {
        // Check if request is AJAX
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Direct access not allowed']);
            return;
        }
        // Get input parameters
        $staff_id = $this->input->post('staff_id');
        $shift_date = $this->input->post('shift_date');
        $call_flag = $this->input->post('call_flag');
        
        // Validate required parameters
        if (empty($staff_id) || empty($shift_date) || !isset($call_flag)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
            return;
        }
        
        // Prepare data for database update
        $shift_data = [
            'company_id' => $this->user['company_id'],
            'staff_id' => $staff_id,
            'shift_date' => $shift_date,
            'call_flag' => $call_flag,
        ];
        
        // Check if record already exists
        $existing_record = $this->shift_model->getShiftByStaffAndDate($staff_id, $shift_date);
        // Update or insert shift data
        if ($existing_record) {
            // Update existing record
            $result = $this->shift_model->updateShift($existing_record['shift_id'], $shift_data);
        } else {
            // Insert new record
            // $shift_data['shift_option'] = ''; // Set default shift option if only updating call_flag
            $result = $this->shift_model->addNewShift($shift_data);
        }
        
        // Return response
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'On-call status updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update on-call status']);
        }
    }
}

?>
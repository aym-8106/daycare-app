<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/CompanyController.php';

class Schedule extends CompanyController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_COMPANY);

        //チャットボット
        $this->header['page'] = 'schedule';
        $this->header['title'] = 'CareNavi訪問看護';

        $this->load->model('user_model');
        $this->load->model('schedule_model');
        $this->load->model('patient_model');
        $this->load->model('company_model');
        $this->load->model('staff_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        $this->data['today'] = date('Y-m-d');

        // Check if dates are submitted via POST
        $posted_start_date = $this->input->post('start_date');
        $posted_end_date = $this->input->post('end_date');
        
        if ($posted_start_date && $posted_end_date) {
            // New date range submitted - save to session
            $this->session->set_userdata('schedule_start_date', $posted_start_date);
            $this->session->set_userdata('schedule_end_date', $posted_end_date);
            
            $this->data['start_date'] = date('Y-m-d', strtotime($posted_start_date));
            $this->data['end_date'] = date('Y-m-d', strtotime($posted_end_date));
        } else {
            // No dates submitted, check session
            $session_start_date = $this->session->userdata('schedule_start_date');
            $session_end_date = $this->session->userdata('schedule_end_date');
            
            if ($session_start_date && $session_end_date) {
                // Use dates from session
                $this->data['start_date'] = date('Y-m-d', strtotime($session_start_date));
                $this->data['end_date'] = date('Y-m-d', strtotime($session_end_date));
            } else {
                // No dates in session, use default (current month)
                $this->data['start_date'] = date('Y-m-01', strtotime($this->data['today']));
                $this->data['end_date'] = date('Y-m-t', strtotime($this->data['today'])); 
            }
        }
        

        if ($mode == 'update') {
            $use_flag = $this->input->post('use_flag');
            $id = $this->input->post('company_id');
            $data = array(
                'use_flag' => $use_flag,
                'company_id' => $id,
            );
            $this->schedule_model->saveSetting($data);
        }
        $this->data['search'] = $this->input->post('searchText');
        
        $this->load->library('pagination');
        
        $this->data['list_cnt'] = $this->schedule_model->get_total_List('', $this->data['search'], $this->data['start_date'], $this->data['end_date'], true);
        
        $returns = $this->_paginationCompress("company/schedule/index", $this->data['list_cnt'], 10, 4);

        $this->data['start_page'] = $returns["segment"] + 1;
        $this->data['end_page'] = $returns["segment"] + $returns["page"];
        if ($this->data['end_page'] > $this->data['list_cnt']) $this->data['end_page'] = $this->data['list_cnt'];
        if (!$this->data['start_page']) $this->data['start_page'] = 1;

        $this->data['list'] = $this->schedule_model->get_total_List('', $this->data['search'], $this->data['start_date'], $this->data['end_date'], false, $returns['page'], $returns['segment']);

        $this->_load_view_company("company/schedule/index");
    }

    /**
     * This function is used to load the user list
     */
    function add()
    {
        $mode = $this->input->post('mode');
        $this->data['company'] = $this->company_model->get_all_data();
        $this->data['staff'] = $this->staff_model->get_all_data();
        $this->data['patient'] = $this->patient_model->get_all_data();

        if($mode == 'save') {
            $this->data['staff_id'] = $this->user['staff_id'];
            $this->data['staff_name'] = $this->user['staff_name'];
            $this->data['company_id'] = $this->user['company_id'];
            $company_data = $this->company_model->getSetting($this->data['company_id']);
            $this->data['company_name'] = $company_data['company_name'];
            
            if ($this->form_validation->run() === TRUE) {
                if($this->data['staff_id'] != 0 && $this->data['company_id'] != 0) {
                    if ($mode == 'save') {
                        $result = $this->patient_model->patient_add($this->data['patient']);
                    } else if($mode == 'update') {
                        $id = $this->input->post('patient_id');
                        $result = $this->patient_model->patient_update($id, $this->data['patient']);
                    }
                    redirect('company/schedule');
                } else {
                    
                }
            } else {
            }
        }

        $this->_load_view_company("company/schedule/add");
    }

    public function edit()
    {
        $mode = $this->input->post('mode');
        $this->data['scheduleId'] = $this->input->post('scheduleId');
        $company_id = $this->input->post('companyId');
        $this->data['staff_list'] = $this->staff_model->get_staff($company_id);
        $this->data['company'] = $this->company_model->get_all_data();
        $this->data['staff'] = $this->staff_model->get_all_data();
        $this->data['patient'] = $this->patient_model->get_all_data();

        $schedule = $this->schedule_model->get_form_data($this->data['scheduleId']);

        if (empty($schedule)) {
            redirect('company/schedule');
        }

        if ($mode == 'edit') {
            $this->data['schedule'] = array(
                'id' => $schedule['id'],
                'company_id' => $company_id,
                'staff_id' => $schedule['staff_id'],
                'patient_id' => $schedule['patient_id'],
                'schedule_date' => $schedule['schedule_date'],
                'schedule_start_time' => $schedule['schedule_start_time'],
                'schedule_end_time' => $schedule['schedule_end_time']
            );
        } else {
            redirect('company/schedule');
        }
        $this->_load_view_company("company/schedule/edit");
    }

    /**
     * This function is used to delete the user using userId
     * @return boolean $result : TRUE / FALSE
     */
    function delete()
    {
        $id = $this->input->post('scheduleId');
        $mode = $this->input->post('mode');

        if($mode == 'delete') {
            $result = $this->schedule_model->schedule_delete($id);
            redirect('company/schedule');
        }
    }

    public function get_patient_data() {
        // $id = $this->input->post('id');
        // $patient = $this->patient_model->getFromId($id);
    
        // // Mapping for readable names
        // $weekdays = [1 => '月曜日', 2 => '火曜日', 3 => '水曜日', 4 => '木曜日', 5 => '金曜日', 6 => '土曜日', 7 => '日曜日'];
        // $curetype = [1 => '看護', 2 => 'リハビリ'];
        // $repeat = [1 => '毎日', 2 => '毎週', 3 => '隔週', 4 => '毎月'];
    
        // echo json_encode([
        //     'patient_addr' => $patient['patient_addr'],
        //     'patient_date' => $patient['patient_date'],
        //     'patient_date_name' => $weekdays[$patient['patient_date']] ?? '',
        //     'patient_curetype' => $patient['patient_curetype'],
        //     'patient_curetype_name' => $curetype[$patient['patient_curetype']] ?? '',
        //     'patient_usefrom' => $patient['patient_usefrom'],
        //     'patient_useto' => $patient['patient_useto'],
        //     'patient_repeat' => $patient['patient_repeat'],
        //     'patient_repeat_name' => $repeat[$patient['patient_repeat']] ?? ''
        // ]);
    }

    public function get_staff_data()
    {
        $company_id = $this->input->post('company_id');
        $staff_list = $this->staff_model->get_staff($company_id);

        echo json_encode($staff_list);
    }

    public function schedule_save()
    {
        $mode = $this->input->post('mode');
        $company_id = $this->input->post('companyId');
        $staff_id = $this->input->post('staffId');
        $patient_id = $this->input->post('patientId');
        $schedule_date = $this->input->post('schedule_date');
        $schedule_start_time = $this->input->post('schedule_start_time');
        $schedule_end_time = $this->input->post('schedule_end_time');

        $check_company_id = $this->company_model->getFromId($company_id);
        $check_staff_id = $this->staff_model->getFromId($staff_id);

        if(!empty($check_company_id) && !empty($check_staff_id)) {
            $data = array(
                'staff_id' => $staff_id,
                'patient_id' => $patient_id,
                'schedule_date' => $schedule_date,
                'schedule_start_time' => $schedule_start_time,
                'schedule_end_time' => $schedule_end_time
            );
            
            if ($mode == 'update') {
                $id = $this->input->post('schedule_id');
                $this->schedule_model->schedule_update($id, $data);
            } else if ($mode == 'save') {
                $this->schedule_model->schedule_add($data);
            }
            
            redirect('company/schedule');
        } else {
            $this->session->set_flashdata('error', '情報が不足しています。');
            redirect('company/schedule/add');
        }
    }

}

?>
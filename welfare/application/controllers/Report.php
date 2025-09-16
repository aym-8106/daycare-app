<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class Report extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        //チャットボット
        $this->header['page'] = 'report';
        $this->header['title'] = 'CareNavi訪問看護';
        $this->header['user'] = $this->user;

        // $this->load->model('scenario_model');
        // $this->load->model('bot_model');
        $this->load->model('user_model');
        $this->load->model('Post_model', 'post_model');
        $this->load->model('Patient_model', 'patient_model');
        $this->data['weekdays'] = get_weekdays();
        $this->data['patientrepeat'] = get_patientrepeat();
        $this->data['patientcuretype'] = get_patientcuretype();
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

        $start_date = date('Y-m-01', strtotime($cond_date));
        $end_date = date('Y-m-t', strtotime($cond_date)); 

        $this->data['cond_date'] = $cond_date;

        $staff_id = $this->user['staff_id'];

        $data = array();
        $data['staff_id'] = $staff_id;
        $data['cond_date'] = $this->data['cond_date'];
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $this->data['daily'] = $this->post_model->get_daily($data);

        $this->data['monthly'] = $this->post_model->get_monthly($data);

        $this->_load_view("report/index");
    }

}

?>
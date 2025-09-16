<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class Post extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        //チャットボット
        $this->header['page'] = 'post';
        $this->header['title'] = 'CareNavi訪問看護';
        $this->header['user'] = $this->user;

        // $this->load->model('scenario_model');
        // $this->load->model('bot_model');
        $this->load->model('user_model');
        $this->load->model('Staff_model', 'staff_model');
        $this->load->model('Post_model', 'post_model');
        $this->load->model('Patient_model', 'patient_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {        
        $mode = $this->input->post('mode');
        $id = $this->input->post('hid_id');
        $staff_id = $this->user['staff_id'];
        $data = array();
        $data['post_date'] = $this->input->post('post_date');
        $data['post_staff'] = $this->input->post('post_staff');
        $data['post_content'] = $this->input->post('post_content');

        if ($mode == 'save') {
            $this->form_validation->set_rules('post_date', '企業名', 'required');
            $this->form_validation->set_rules('post_staff', 'メールアドレス', 'required');
            $this->form_validation->set_rules('post_content', 'パスワード', 'required');

            if ($this->form_validation->run() === TRUE) {
                if(!empty($id)) {
                    $this->post_model->save($data);
                } else {
                    $this->post_model->add($data);
                }
            }
        }
        $this->data['search'] = $this->input->post('searchText');
        $this->load->library('pagination');
        $this->data['list_cnt'] = $this->post_model->get_search_data($this->data['search'], $staff_id, $this->user['staff_role']);
        
        $returns = $this->_paginationCompress("post/index", $this->data['list_cnt'], 10);

        $this->data['start_page'] = $returns["segment"] + 1;
        $this->data['end_page'] = $returns["segment"] + $returns["page"];
        if ($this->data['end_page'] > $this->data['list_cnt']) $this->data['end_page'] = $this->data['list_cnt'];
        if (!$this->data['start_page']) $this->data['start_page'] = 1;

        if(!empty($data['post_staff'])) {
            $this->post['post_data'] = $this->post_model->getList('*', array('post_date' => $data['post_date'], 'staff_id' => $data['post_staff'], 'del_flag' => 0));
            if(empty($this->post['post_data'])) {
                $this->data['post_data'] = array();
                $this->data['post_data']['post_date'] = $this->input->post('post_date') ?: date('Y-m-d');
                $this->data['post_data']['post_staff'] = $this->input->post('post_staff');
            }
        } else {
            $this->post['post_data'] = array();
            $this->data['post_data']['post_date'] = $this->input->post('post_date') ?: date('Y-m-d');
        }
        if($this->user['staff_role'] == 1) {
            $this->data['staffs'] = $this->staff_model->getList('*', array('BaseTbl.company_id' => $this->user['company_id'], 'staff_role' => 2, 'BaseTbl.del_flag' => 0));
        } else {
            $this->data['staffs'] = $this->staff_model->getList('*', array('staff_id' => $this->user['staff_id'], 'BaseTbl.del_flag' => 0));
        }

        $user_data = $this->session->userdata('staff');
        
        if($this->user['staff_role'] == 1) {
            $this->data['list'] = $this->post_model->get_all_data($this->user['staff_role'], $staff_id, $this->data['search']);
        } else {
            $this->data['list'] = $this->post_model->get_all_data($this->user['staff_role'], $staff_id, $this->data['search']);
        }
        $this->data['patient'] = $this->post_model->get_patient_all_data($this->data['post_data']['post_date'], $staff_id);

        $this->_load_view("post/index");
    }

    public function postsearch()
    {
        $this->data['patientId'] = $this->input->post('patientId');
        $this->data['post_date'] = $this->input->post('post_date');
        $staff_id = $this->user['staff_id'];

        $data = array();
        $data['patientId'] = $this->data['patientId']; 
        $data['post_date'] = $this->data['post_date'];

        $this->data['post_data']['post_date'] =  $this->data['post_date'];

        if($this->user['staff_id'] == 1) {
            $this->data['list'] = $this->post_model->get_search_all_data($data);
        } else {
            $this->data['list'] = $this->post_model->get_search_data($staff_id, $data);
        }
        
        if($this->data['patientId'] != "") {
            $this->data['patient'] = $this->post_model->get_patient_all_data($this->data['post_data']['post_date'], $staff_id);
            $this->_load_view("post/index");
        } else {
            redirect('post/index');
        }
    }

    public function postadd()
    {
        $staff_id = $this->user['staff_id'];
        $this->data['patient'] = $this->post_model->get_patient_all_data(date('Y-m-d'), $staff_id);
        $this->data['patient_usefrom'] = $this->input->post('patient_usefrom') ?: date('00:00');
        $this->data['patient_useto'] = $this->input->post('patient_useto') ?: date('00:00');

        $this->_load_view("post/add");
    }

    public function postsave()
    {
        $mode = $this->input->post('mode');
        $data = array();
        $data['id'] = $this->input->post('post_content_id');
        $data['staff_id'] = $this->user['staff_id'];
        $data['patient_id'] = $this->input->post('patientId');
        $data['post_date'] = $this->input->post('post_date');
        $data['patient_usefrom'] = $this->input->post('patient_usefrom');
        $data['patient_useto'] = $this->input->post('patient_useto');
        $data['post_content'] = $this->input->post('post_descript');

        if ($mode == 'insert') {
            if(!empty($data)) {
                $this->post_model->save($data);
            }
        } else {
            if(!empty($data)) {
                $id = $this->input->post('post_content_id');
                $this->post_model->post_update($id,$data);
            }
        }
        redirect('post/index');
    }

    public function postedit()
    {
        $mode = $this->input->post('mode');
        $id = $this->input->post('userId');

        $this->data['edit_data'] = $this->post_model->get_post_data($id);
        $this->data['post_data'] = $this->data['edit_data'][0];

        $this->_load_view("post/edit");
    }

    public function confirm()
    {
        $this->data['month'] = $this->input->post('month') ?: date('n');
        $a_date = date('Y-').$this->data['month'].'-01';

        $this->data['days'] = date("t", strtotime($a_date));
        
        $this->_load_view("post/confirm");
    }

    public function get_patient_data() {
        $id = $this->input->post('id');
        $post_date = $this->input->post('post_date');
        $staff_id = $this->user['staff_id'];

        $patient = $this->post_model->get_data($id, $post_date, $staff_id);
        
        if(empty($patient)) {
            $patient_start_time = '';
            $patient_end_time = '';
        } else {
            $patient_start_time = $patient[0]['schedule_start_time'];
            $patient_end_time = $patient[0]['schedule_end_time'];
        }

        echo json_encode([
            'patient_usefrom' => $patient_start_time ?: '00:00',
            'patient_useto' => $patient_end_time ?: '00:00',
        ]);
    }

    function postdelete()
    {
        $post_id = $this->input->post('post_id');
        $mode = $this->input->post('mode');

        if($mode == 'delete') {
            $result = $this->post_model->post_delete($post_id);
            redirect('post/index');
        }
    }

    function patient_today_data()
    {
        $post_date = $this->input->post('post_date');
        $staff_id = $this->user['staff_id'];
        $patient = $this->post_model->get_patient_all_data($post_date, $staff_id);

        echo json_encode($patient);
    }
}

?>
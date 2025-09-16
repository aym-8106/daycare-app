<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class Setting extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        $this->header['user'] = $this->user;

        $this->load->model('user_model');
        $this->load->model('staff_model');
        $this->load->model('Settingstaff_model');
        $this->load->model('patient_model');
        $this->data['weekdays'] = get_weekdays();
        $this->data['patientrepeat'] = get_patientrepeat();
        $this->data['patientcuretype'] = get_patientcuretype();
    }

    /**
     * This function used to load the first screen of the user
     */
    public function patient()
    {
        //チャットボット
        $this->header['page'] = 'patient';
        $this->header['title'] = 'CareNavi訪問看護';

        $mode = $this->input->post('mode');
        if ($mode == 'update') {
            $use_flag = $this->input->post('use_flag');
            $id = $this->input->post('company_id');
            $data = array(
                'use_flag' => $use_flag,
                'company_id' => $id,
            );
            $this->patient_model->saveSetting($data);
        }
        $this->data['search'] = $this->input->post('searchText');

        $this->load->library('pagination');

        $this->data['list_cnt'] = $this->patient_model->getList('*', $this->data['search'], true);
        $returns = $this->_paginationCompress("setting/patient/index", $this->data['list_cnt'], 10);

        $this->data['start_page'] = $returns["segment"] + 1;
        $this->data['end_page'] = $returns["segment"] + $returns["page"];
        if ($this->data['end_page'] > $this->data['list_cnt']) $this->data['end_page'] = $this->data['list_cnt'];
        if (!$this->data['start_page']) $this->data['start_page'] = 1;

        $this->data['list'] = $this->patient_model->getList('*', $this->data['search'], false, $returns['page'], $returns['segment']);

        $this->_load_view("setting/patient/index");
    }

    /**
     * This function is used to load the user list
     */
    function patientadd()
    {
        //チャットボット
        $this->header['page'] = 'patient';
        $this->header['title'] = 'CareNavi訪問看護';

        $mode = $this->input->post('mode');
        $this->data['patient_regdate'] = $this->input->post('patient_regdate') ?: date('Y-m-d');

        $this->data['patient_usefrom'] = $this->input->post('patient_usefrom') ?: date('00:00');
        $this->data['patient_useto'] = $this->input->post('patient_useto') ?: date('00:00');

        $this->data['patient_usefrom2'] = $this->input->post('patient_usefrom2') ?: date('00:00');
        $this->data['patient_useto2'] = $this->input->post('patient_useto2') ?: date('00:00');

        $this->data['patient_usefrom3'] = $this->input->post('patient_usefrom3') ?: date('00:00');
        $this->data['patient_useto3'] = $this->input->post('patient_useto3') ?: date('00:00');

        $this->data['patient_usefrom4'] = $this->input->post('patient_usefrom4') ?: date('00:00');
        $this->data['patient_useto4'] = $this->input->post('patient_useto4') ?: date('00:00');

        $this->data['patient_usefrom5'] = $this->input->post('patient_usefrom5') ?: date('00:00');
        $this->data['patient_useto5'] = $this->input->post('patient_useto5') ?: date('00:00');

        $this->data['patient_usefrom6'] = $this->input->post('patient_usefrom6') ?: date('00:00');
        $this->data['patient_useto6'] = $this->input->post('patient_useto6') ?: date('00:00');

        $this->data['patient_usefrom7'] = $this->input->post('patient_usefrom7') ?: date('00:00');
        $this->data['patient_useto7'] = $this->input->post('patient_useto7') ?: date('00:00');

        

        $this->data['patient'] = array(
            'patient_name' => $this->input->post('patient_name'),
            'patient_addr' => $this->input->post('patient_addr'),
            'patient_regdate' => $this->input->post('patient_regdate'),
            'patient_date' => $this->input->post('patient_date'),
            'patient_curetype' => $this->input->post('patient_curetype'),
            'patient_usefrom' => $this->input->post('patient_usefrom'),
            'patient_useto' => $this->input->post('patient_useto'),
            'patient_repeat' => $this->input->post('patient_repeat'),

            'patient_curetype2' => $this->input->post('patient_curetype2'),
            'patient_usefrom2' => $this->input->post('patient_usefrom2'),
            'patient_useto2' => $this->input->post('patient_useto2'),
            'patient_repeat2' => $this->input->post('patient_repeat2'),

            'patient_curetype3' => $this->input->post('patient_curetype3'),
            'patient_usefrom3' => $this->input->post('patient_usefrom3'),
            'patient_useto3' => $this->input->post('patient_useto3'),
            'patient_repeat3' => $this->input->post('patient_repeat3'),

            'patient_curetype4' => $this->input->post('patient_curetype4'),
            'patient_usefrom4' => $this->input->post('patient_usefrom4'),
            'patient_useto4' => $this->input->post('patient_useto4'),
            'patient_repeat4' => $this->input->post('patient_repeat4'),

            'patient_curetype5' => $this->input->post('patient_curetype5'),
            'patient_usefrom5' => $this->input->post('patient_usefrom5'),
            'patient_useto5' => $this->input->post('patient_useto5'),
            'patient_repeat5' => $this->input->post('patient_repeat5'),

            'patient_curetype6' => $this->input->post('patient_curetype6'),
            'patient_usefrom6' => $this->input->post('patient_usefrom6'),
            'patient_useto6' => $this->input->post('patient_useto6'),
            'patient_repeat6' => $this->input->post('patient_repeat6'),

            'patient_curetype7' => $this->input->post('patient_curetype7'),
            'patient_usefrom7' => $this->input->post('patient_usefrom7'),
            'patient_useto7' => $this->input->post('patient_useto7'),
            'patient_repeat7' => $this->input->post('patient_repeat7'),
        );

        $this->form_validation->set_rules('patient_name', '利用者名', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('patient_addr', 'アドレス', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('patient_regdate', '登録日', 'required');
        $this->form_validation->set_rules('patient_repeat', '頻度', 'required');

        if ($this->form_validation->run() === TRUE) {
            if ($mode == 'save') {
                $result = $this->patient_model->patient_add($this->data['patient']);
            } else if($mode == 'update') {
                $id = $this->input->post('patient_id');
                $result = $this->patient_model->patient_update($id, $this->data['patient']);
            }
            redirect('setting/patient');
        } else {
//                var_dump(validation_errors());;
        }

        $this->_load_view("setting/patient/add");
    }

    public function patientedit()
    {
        //チャットボット
        $this->header['page'] = 'patient';
        $this->header['title'] = 'CareNavi訪問看護';

        $id = $this->input->post('userId');
        $mode = $this->input->post('mode');

        $patient = $this->patient_model->getFromId($id);
        
        if (empty($patient)) {
            redirect('setting/patient');
        }

        if ($mode == 'edit') {
            $this->data['patient'] = array(
                'id' => $patient['id'],
                'patient_name' => $patient['patient_name'],
                'patient_addr' => $patient['patient_addr'],
                'patient_regdate' => $patient['patient_regdate'],
                'patient_date' => $patient['patient_date'],
                'patient_curetype' => $patient['patient_curetype'],
                'patient_usefrom' => $patient['patient_usefrom'],
                'patient_useto' => $patient['patient_useto'],
                'patient_repeat' => $patient['patient_repeat'],

                'patient_curetype2' => $patient['patient_curetype2'],
                'patient_usefrom2' => $patient['patient_usefrom2'],
                'patient_useto2' => $patient['patient_useto2'],
                'patient_repeat2' => $patient['patient_repeat2'],

                'patient_curetype3' => $patient['patient_curetype3'],
                'patient_usefrom3' => $patient['patient_usefrom3'],
                'patient_useto3' => $patient['patient_useto3'],
                'patient_repeat3' => $patient['patient_repeat3'],

                'patient_curetype4' => $patient['patient_curetype4'],
                'patient_usefrom4' => $patient['patient_usefrom4'],
                'patient_useto4' => $patient['patient_useto4'],
                'patient_repeat4' => $patient['patient_repeat4'],

                'patient_curetype5' => $patient['patient_curetype5'],
                'patient_usefrom5' => $patient['patient_usefrom5'],
                'patient_useto5' => $patient['patient_useto5'],
                'patient_repeat5' => $patient['patient_repeat5'],

                'patient_curetype6' => $patient['patient_curetype6'],
                'patient_usefrom6' => $patient['patient_usefrom6'],
                'patient_useto6' => $patient['patient_useto6'],
                'patient_repeat6' => $patient['patient_repeat6'],

                'patient_curetype7' => $patient['patient_curetype7'],
                'patient_usefrom7' => $patient['patient_usefrom7'],
                'patient_useto7' => $patient['patient_useto7'],
                'patient_repeat7' => $patient['patient_repeat7'],
            );
        } else {
            $this->data['company'] = $company;
        }

        $this->_load_view("setting/patient/edit");

    }

    /**
     * This function is used to delete the user using userId
     * @return boolean $result : TRUE / FALSE
     */
    function patientdelete()
    {
        $userId = $this->input->post('userId');
        $mode = $this->input->post('mode');

        if($mode == 'delete') {
            $result = $this->patient_model->patient_delete($userId);
            redirect('setting/patient');
        }
    }

    public function staff()
    {
        //チャットボット
        $this->header['page'] = 'staff';
        $this->header['title'] = 'CareNavi訪問看護';

        $mode = $this->input->post('mode');
        
        $this->data['staff_data'] = $this->staff_model->get_staffList();
        $this->data['employtype_data'] = $this->staff_model->get_employList();

        if ($mode == 'save') {
            $data = $this->input->post('data');
            $id = $this->input->post('hid_id');
            
            $staffs = $this->staff_model->getList("*", array('staff_id' => $this->user['staff_id']));

            $this->data['staff_info'] = $staffs[0];

            $staff_info = array();
            $staff_info['staff_jobtype'] = $this->input->post('jobtypeId');
            $staff_info['staff_employtype'] = $this->input->post('employtypeId');

            $regdata = array();
            if(empty($id)){
                $regdata['id'] = $id;
                $regdata['company_id'] = $this->data['staff_info']['company_id'];
                $regdata['staff_id'] = $this->data['staff_info']['staff_id'];
                $regdata['mon_start'] = $data['mon_start'];
                $regdata['tue_start'] = $data['tue_start'];
                $regdata['wed_start'] = $data['wed_start'];
                $regdata['thu_start'] = $data['thu_start'];
                $regdata['fri_start'] = $data['fri_start'];
                $regdata['sat_start'] = $data['sat_start'];
                $regdata['mon_end'] = $data['mon_end'];
                $regdata['tue_end'] = $data['tue_end'];
                $regdata['wed_end'] = $data['wed_end'];
                $regdata['thu_end'] = $data['thu_end'];
                $regdata['fri_end'] = $data['fri_end'];
                $regdata['sat_end'] = $data['sat_end'];
                $regdata['relax_time'] = $data['relax_time'];
                $regdata['create_date'] = date('Y-m-d H:m:s');
                $regdata['update_date'] = date('Y-m-d H:m:s');
                $this->Settingstaff_model->add($regdata);
            }else{
                $regdata['id'] = $id;
                $regdata['company_id'] = $this->data['staff_info']['company_id'];
                $regdata['staff_id'] = $this->data['staff_info']['staff_id'];
                $regdata['mon_start'] = $data['mon_start'];
                $regdata['tue_start'] = $data['tue_start'];
                $regdata['wed_start'] = $data['wed_start'];
                $regdata['thu_start'] = $data['thu_start'];
                $regdata['fri_start'] = $data['fri_start'];
                $regdata['sat_start'] = $data['sat_start'];
                $regdata['mon_end'] = $data['mon_end'];
                $regdata['tue_end'] = $data['tue_end'];
                $regdata['wed_end'] = $data['wed_end'];
                $regdata['thu_end'] = $data['thu_end'];
                $regdata['fri_end'] = $data['fri_end'];
                $regdata['sat_end'] = $data['sat_end'];
                $regdata['relax_time'] = $data['relax_time'];
                $regdata['update_date'] = date('Y-m-d H:m:s');
                $this->Settingstaff_model->edit($regdata);
                $this->staff_model->staff_edit($regdata, $staff_info);
            }
        }

        $staffs = $this->staff_model->getList("*", array('staff_id' => $this->user['staff_id']));
        $this->data['staff_info'] = $staffs[0];

        $staff_setting = $this->Settingstaff_model->getList("*", array('staff_id' => $this->user['staff_id']));
        if(empty($staff_setting)) {
            $this->data['staff_setting'] = array();
        } else {
            $this->data['staff_setting'] = $staff_setting[0];
        }
        $this->_load_view("setting/staff");
    }

    public function update_patient_field() {
        $id = $this->input->post('id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        $result = $this->patient_model->update_patient_field($id, $field, $value);

        echo $result ? 'success' : 'error';
    }
}

?>
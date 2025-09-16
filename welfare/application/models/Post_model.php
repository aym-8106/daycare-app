<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Post_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_post';
        $this->primary_key = 'id';
    }

    function getPost($key,$company_id){

        $this->db->select('key_value');
        $this->db->from($this->table);
        $this->db->where('key_name', $key);
        $this->db->where('company_id', $company_id);
        $query = $this->db->get()->row_array();
        return !empty($query['key_value'])? $query['key_value']:'';
    }

    function setPost($key,$value,$company_id){

        $this->db->where('key_name', $key);
        $this->db->where('company_id', $company_id);
        $this->db->update($this->table,array('key_value'=>$value));
        return $this->db->affected_rows();
    }

    function registerPost($key,$value,$company_id){
        if($this->getPost($key,$company_id)){
            return $this->setPost($key,$value,$company_id);
        }else{
            $data = array(
                'key_name'=>$key,
                'key_value'=>$value,
                'company_id'=>$company_id,
            );

            return $this->add($data);
        }
    }

    function save($data)
    {
        $this->db->insert($this->table, $data);
    }

    function post_update($id,$data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table,$data);
        
        return $this->db->affected_rows();
    }
    
    function get_all_data($staff_role, $staff_id, $where_data)
    {
        $this->db->select('tbl_schedule.*,tbl_patient.patient_name,tbl_staff.staff_name, tbl_post.id as post_id,tbl_post.post_content');
        $this->db->from('tbl_schedule');
        $this->db->join('tbl_patient', 'tbl_schedule.patient_id = tbl_patient.id', 'left');
        $this->db->join('tbl_staff', 'tbl_schedule.staff_id = tbl_staff.staff_id', 'left');
        $this->db->join('tbl_post', 'tbl_schedule.patient_id = tbl_post.patient_id AND tbl_schedule.schedule_date = tbl_post.post_date', 'left');
        
        // Basic conditions for all queries
        $this->db->where('tbl_post.post_content IS NOT NULL');
        $this->db->where('tbl_schedule.del_flag', 0);
        $this->db->where('tbl_post.del_flag', 0);
        $this->db->where('tbl_patient.del_flag', 0);
        
        // Apply staff-specific filtering if not an admin (assuming staff_role=1 is admin)
        if($staff_role != 1) {
            $this->db->where('tbl_schedule.staff_id', $staff_id);
        }
        
        // Search conditions
        if (!empty($where_data)) {
            $this->db->group_start(); // open bracket
            $this->db->like('tbl_schedule.schedule_date', $where_data);
            $this->db->or_like('tbl_patient.patient_name', $where_data);
            $this->db->or_like('tbl_post.post_content', $where_data);
            $this->db->group_end(); // close bracket
        }
        
        $this->db->order_by('tbl_schedule.schedule_date', 'DESC');
        $this->db->order_by('tbl_schedule.schedule_start_time', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_person_data($staff_id)
    {
        $this->db->select('
            tbl_post.*, 
            (tbl_patient.id) AS patient_id, 
            tbl_patient.patient_name, 
            tbl_staff.staff_name
        ');
        $this->db->from('tbl_post');
        $this->db->where('tbl_post.staff_id', $staff_id);
        $this->db->where('tbl_post.del_flag', 0);
        $this->db->join('tbl_patient', 'tbl_post.patient_id = tbl_patient.id', 'left');
        $this->db->join('tbl_staff', 'tbl_post.staff_id = tbl_staff.staff_id', 'left');
        $this->db->order_by('tbl_post.post_date', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_post_data($id)
    {
        $this->db->select('tbl_schedule.*,tbl_patient.patient_name,tbl_post.post_content,tbl_post.id as post_id');
        $this->db->from('tbl_schedule');
        $this->db->where('tbl_schedule.id', $id);
        $this->db->where('tbl_schedule.del_flag', 0);
        $this->db->where('tbl_post.del_flag', 0);
        $this->db->where('tbl_patient.del_flag', 0);
        $this->db->join('tbl_patient', 'tbl_schedule.patient_id = tbl_patient.id', 'left');
        $this->db->join('tbl_post', 'tbl_schedule.patient_id = tbl_post.patient_id AND tbl_schedule.schedule_date = tbl_post.post_date', 'left');

        $query = $this->db->get();
        return $query->result_array();
    }

    function post_delete($value) {
        $this->db->where('id', $value);
        $this->db->update($this->table, ['del_flag' => 1]);
        return true;
    }

    function get_search_all_data($data)
    {
        $this->db->select('
            tbl_post.*, 
            (tbl_patient.id) AS patient_id, 
            tbl_patient.patient_name, 
            tbl_staff.staff_name
        ');
        $this->db->from('tbl_post');
        if($data['patientId'] != "") {
            $this->db->where('tbl_post.patient_id', $data['patientId']);
            $this->db->where('tbl_post.post_date', $data['post_date']);
        }
        $this->db->where('tbl_post.del_flag', 0);
        $this->db->join('tbl_patient', 'tbl_post.patient_id = tbl_patient.id', 'left');
        $this->db->join('tbl_staff', 'tbl_post.staff_id = tbl_staff.staff_id', 'left');
        $this->db->order_by('tbl_post.post_date', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_search_data($where_data, $staff_id, $role_id)
    {
        $this->db->select('tbl_schedule.*,tbl_patient.patient_name,tbl_post.post_content');
        $this->db->from('tbl_schedule');
        if($role_id != 1) {
            $this->db->where('tbl_schedule.staff_id', $staff_id);
        }
        $this->db->where('tbl_post.post_content IS NOT NULL');
        $this->db->where('tbl_schedule.del_flag', 0);
        $this->db->where('tbl_post.del_flag', 0);
        $this->db->where('tbl_patient.del_flag', 0);

        if (!empty($where_data)) {
            $this->db->group_start(); // open bracket
            $this->db->like('tbl_schedule.schedule_date', $where_data);
            $this->db->or_like('tbl_patient.patient_name', $where_data);
            $this->db->or_like('tbl_post.post_content', $where_data);
            $this->db->group_end(); // close bracket
        }
        $this->db->join('tbl_patient', 'tbl_schedule.patient_id = tbl_patient.id', 'left');
        $this->db->join('tbl_post', 'tbl_schedule.patient_id = tbl_post.patient_id AND tbl_schedule.schedule_date = tbl_post.post_date', 'left');
        $this->db->order_by('tbl_schedule.schedule_start_time', 'ASC');
        
        return $this->db->count_all_results();

    }

    
    public function get_daily($data)
    {
        $this->db->select('
            tbl_post.*, 
            (tbl_patient.id) AS patient_id, 
            tbl_patient.patient_name, 
            tbl_patient.patient_curetype, 
            tbl_staff.staff_name
        ');
        $this->db->from('tbl_post');
        $this->db->where('tbl_post.staff_id', $data['staff_id']);
        $this->db->where('tbl_post.post_date', $data['cond_date']);
        $this->db->where('tbl_post.del_flag', 0);
        
        // Apply staff-specific filtering if not an admin (assuming staff_role=1 is admin)
        if($this->user['staff_role'] != 1) {
            $this->db->where('tbl_post.staff_id', $this->user['staff_id']);
        }
        
        $this->db->join('tbl_patient', 'tbl_post.patient_id = tbl_patient.id', 'left');
        $this->db->join('tbl_staff', 'tbl_post.staff_id = tbl_staff.staff_id', 'left');
        $this->db->order_by('tbl_post.patient_usefrom', 'ASC');
        $this->db->order_by('tbl_patient.patient_curetype', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_monthly($data)
    {
        $this->db->select('
            tbl_post.*, 
            (tbl_patient.id) AS patient_id, 
            tbl_patient.patient_name, 
            tbl_patient.patient_curetype, 
            tbl_staff.staff_name
        ');
        $this->db->from('tbl_post');
        $this->db->where('tbl_post.staff_id', $data['staff_id']);
        $this->db->where('tbl_post.post_date >=', $data['start_date']);
        $this->db->where('tbl_post.post_date <=', $data['end_date']);
        $this->db->where('tbl_post.del_flag', 0);
        
        // Apply staff-specific filtering if not an admin (assuming staff_role=1 is admin)
        if($this->user['staff_role'] != 1) {
            $this->db->where('tbl_post.staff_id', $this->user['staff_id']);
        }
        
        $this->db->join('tbl_patient', 'tbl_post.patient_id = tbl_patient.id', 'left');
        $this->db->join('tbl_staff', 'tbl_post.staff_id = tbl_staff.staff_id', 'left');
        $this->db->order_by('tbl_post.post_date', 'ASC');
        $this->db->order_by('tbl_post.patient_usefrom', 'ASC');
        $this->db->order_by('tbl_patient.patient_curetype', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_data($patient_id, $schedule_date, $staff_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_schedule');
        $this->db->where('tbl_schedule.patient_id', $patient_id);
        $this->db->where('tbl_schedule.schedule_date', $schedule_date);
        $this->db->where('tbl_schedule.staff_id', $staff_id);
        $this->db->where('tbl_schedule.del_flag', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_patient_all_data($date,$staff_id)
    {
        $this->db->select('tbl_schedule.patient_id, tbl_patient.patient_name, tbl_staff.staff_name');
        $this->db->from('tbl_schedule');
        $this->db->where('tbl_schedule.schedule_date', $date);
        $this->db->where('tbl_schedule.staff_id', $staff_id);
        $this->db->where('tbl_schedule.del_flag', 0);
        $this->db->where('tbl_patient.del_flag', 0);
        $this->db->join('tbl_patient', 'tbl_schedule.patient_id = tbl_patient.id', 'left');
        $this->db->join('tbl_staff', 'tbl_schedule.staff_id = tbl_staff.staff_id', 'left');
        $this->db->order_by('tbl_schedule.patient_id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}
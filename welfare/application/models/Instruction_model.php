<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Instruction_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_instruction';
        $this->primary_key = 'id';
    }

    function getList1($select, $staff_id, $cond_start_date, $cond_end_date ,$where_data, $count_flag=false,$page=10, $offset=0,$order_by='')
    {
        if(empty($select)){
            $select = '*';
        }
        $this->db->select($select);
        $this->db->from($this->table);
        if(is_array($where_data)){
            foreach ($where_data as $key => $value){
                $this->db->where($key,$value);
            }
        }else{
            if(!empty($where_data)) {
                $likeCriteria = "(patient_name  LIKE '%".$where_data."%'
                            OR  patient_addr  LIKE '%".$where_data."%' )";
                $this->db->where($likeCriteria);
            }
        }
        $this->db->where('del_flag',0);
        if($order_by && is_array($order_by)){
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        if(!$count_flag){
            if($page){
                $this->db->limit($page, $offset);
            }
            $query = $this->db->get();
            $result = $query->result_array();
            return $result;
        }else{
            return $this->db->count_all_results();
        }
    }

    function getList_rows_instruction($select, $where_data, $cond_start_date, $cond_end_date, $count_flag = false, $page = 10, $offset = 0, $order_by = '')
    {
        if (empty($select)) {
            $select = '*';
        }
        $this->db->select($select);
        $this->db->from($this->table);
        if (is_array($where_data)) {
            foreach ($where_data as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        if (!empty($cond_start_date)) {
            $this->db->where('tbl_instruction.instruction_start >=', $cond_start_date);
        }

        if (!empty($cond_end_date)) {
            $this->db->where('tbl_instruction.instruction_end <=', $cond_end_date);
        }

        $this->db->where('del_flag', 0);
        if ($order_by && is_array($order_by)) {
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        if (!$count_flag) {
            if($page) {
                $this->db->limit($page, $offset);
            }

            $query = $this->db->get();
            $result = $query->result();
            return $result;
        } else {
            return $this->db->count_all_results();
        }
    }

    function get_total_List($select, $where_data, $cond_start_date, $cond_end_date, $count_flag = false, $page = 10, $offset = 0, $order_by = '')
    {
        if (empty($select)) {
            $select = 'tbl_instruction.*, tbl_instruction.id as instruction_id, tbl_patient.id as patient_id, tbl_patient.patient_name, tbl_staff.staff_name, tbl_company.company_name';
        }

        $this->db->select($select);
        $this->db->from('tbl_instruction');

        // Join related tables
        $this->db->join('tbl_patient', 'tbl_patient.id = tbl_instruction.patient_id', 'left');
        $this->db->join('tbl_staff', 'tbl_staff.staff_id = tbl_instruction.staff_id', 'left');
        $this->db->join('tbl_company', 'tbl_company.company_id = tbl_staff.company_id', 'left');

        // Optional keyword search
        if (is_array($where_data)) {
            foreach ($where_data as $key => $value) {
                $this->db->where($key, $value);
            }
        } elseif (!empty($where_data)) {
            $this->db->group_start();
            $this->db->like('tbl_patient.patient_name', $where_data);
            $this->db->or_like('tbl_staff.staff_name', $where_data);
            $this->db->or_like('tbl_company.company_name', $where_data);
            $this->db->or_like('tbl_instruction.instruction_start', $where_data);
            $this->db->or_like('tbl_instruction.instruction_end', $where_data);
            $this->db->group_end();
        }

        // Apply staff-specific filtering if not an admin (assuming staff_role=1 is admin)
        if($this->user['staff_role'] != 1) {
            $this->db->where('tbl_instruction.staff_id', $this->user['staff_id']);
        }
        
        // Logical delete flag
        if (!empty($cond_start_date)) {
            $this->db->where('tbl_instruction.instruction_start >=', $cond_start_date);
        }

        if (!empty($cond_end_date)) {
            $this->db->where('tbl_instruction.instruction_end <=', $cond_end_date);
        }

        $this->db->where('tbl_instruction.del_flag', 0);


        // Optional sorting
        if ($order_by && is_array($order_by)) {
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        $this->db->order_by('tbl_instruction.instruction_start', 'DESC');
        $this->db->order_by('tbl_company.company_name', 'ASC');
        $this->db->order_by('tbl_patient.patient_name', 'ASC');

        
        // Return data

        if (!$count_flag) {
            if($page) {
                $this->db->limit($page, $offset);
            }
            $query = $this->db->get();
            $result = $query->result_array();
            return $result;
        } else {
            return $this->db->count_all_results();
        }
    }

    function getSetting($_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($this->primary_key, $_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function get_form_data($id)
    {
        $this->db->select('*,tbl_patient.patient_name, tbl_staff.staff_id, tbl_company.company_id, tbl_staff.staff_name, tbl_company.company_name');
        $this->db->from($this->table);
        $this->db->where('tbl_instruction.id', $id);
        $this->db->join('tbl_patient', 'tbl_instruction.patient_id = tbl_patient.id', 'left');
        $this->db->join('tbl_staff', 'tbl_instruction.staff_id = tbl_staff.staff_id', 'left');
        $this->db->join('tbl_company', 'tbl_staff.company_id = tbl_company.company_id', 'left');
        $query = $this->db->get();
        return $query->row_array();
    }

    function saveSetting($data){
        if(!empty($data[$this->primary_key])){
            $this->db->where($this->primary_key, $data[$this->primary_key]);
            $this->db->update($this->table,$data);
            return true;
        }
        return false;
    }

    function instruction_add($data) {
        $this->db->insert($this->table, $data);
    }

    function instruction_delete($value) {
        $this->db->where('id', $value);
        $this->db->update($this->table, ['del_flag' => 1]);
        return true;
    }

    function instruction_update($id,$value) {
        $this->db->where('id', $id);
        $this->db->update($this->table,$value);
        return true;
    }

    public function get_instruction_by_id($id)
    {
        $this->db->select('tbl_instruction.*, tbl_patient.patient_name, tbl_staff.staff_name, tbl_company.company_name');
        $this->db->from('tbl_instruction');
        $this->db->join('tbl_patient', 'tbl_patient.id = tbl_instruction.patient_id', 'left');
        $this->db->join('tbl_staff', 'tbl_staff.staff_id = tbl_instruction.staff_id', 'left');
        $this->db->join('tbl_company', 'tbl_company.company_id = tbl_staff.company_id', 'left');
        $this->db->where('tbl_instruction.id', $id);
        $query = $this->db->get();
        
        return $query->row_array();
    }
}
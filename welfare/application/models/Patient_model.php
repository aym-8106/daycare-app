<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Patient_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_patient';
        $this->primary_key = 'id';
    }

    function getList($select, $where_data = [], $count_flag = false, $page = 10, $offset = 0, $order_by = '', $custom_where = '') {
        if (empty($select)) {
            $select = '*';
        }
        $this->db->select($select);
        $this->db->from($this->table);

        // 通常の where 条件
        if (is_array($where_data)) {
            foreach ($where_data as $key => $value) {
                $this->db->where($key, $value);
            }
        } else {
            if (!empty($where_data)) {
                $likeCriteria = "(patient_name LIKE '%" . $where_data . "%'
                                OR patient_addr LIKE '%" . $where_data . "%')";
                $this->db->where($likeCriteria);
            }
        }

        // カスタム where 追加
        if (!empty($custom_where)) {
            $this->db->where($custom_where);
        }

        $this->db->where('del_flag', 0);

        if ($order_by && is_array($order_by)) {
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }

        if (!$count_flag) {
            if ($page) {
                $this->db->limit($page, $offset);
            }
            $query = $this->db->get();
            return $query->result_array();
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

    function saveSetting($data){

        if(!empty($data[$this->primary_key])){
            $this->db->where($this->primary_key, $data[$this->primary_key]);
            $this->db->update($this->table,$data);
            return true;
        }
        return false;
    }

    function patient_add($data) {
        $this->db->insert($this->table, $data);
    }

    function patient_delete($value) {
        $this->db->where('id', $value);
        $this->db->update($this->table, ['del_flag' => 1]);
        return true;
    }

    function patient_update($id,$value) {
        $this->db->where('id', $id);
        $this->db->update($this->table,$value);
        return true;
    }

    function get_all_data()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('del_flag', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    
    public function update_patient_field($id, $field, $value)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_patient', [$field => $value]); // Replace 'patient_table' with your actual table
    }
}
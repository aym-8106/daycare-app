<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Schedule_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_schedule';
        $this->primary_key = 'id';
    }

    public function get_schedules_by_date($cond_date)
    {
        $this->db->select('tbl_schedule.*, tbl_patient.patient_name');
        $this->db->from('tbl_schedule');
        $this->db->join('tbl_patient', 'tbl_schedule.patient_id = tbl_patient.id', 'left');
        $this->db->where('DATE(tbl_schedule.schedule_date) = ', $cond_date);
        $this->db->where('tbl_schedule.del_flag', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_patient_data($cond_date)
    {
        $subQuery = "SELECT patient_id FROM tbl_schedule WHERE schedule_date = " . $this->db->escape($cond_date) . " AND del_flag = 0";
        $this->db->select('*');
        $this->db->from('tbl_patient');
        $this->db->where('del_flag', 0);
        $this->db->where("id NOT IN ($subQuery)", null, false);
        return $this->db->get()->result_array();
    }

    public function get_patient_data_today($cond_date, $weekday)
    {
        // Adjust weekday suffix
        $daySuffix = ($weekday == 0) ? '7' : (($weekday == 1) ? '' : $weekday);

        // Subquery to exclude patients already scheduled
        $subQuery = "SELECT patient_id FROM tbl_schedule WHERE schedule_date = " . $this->db->escape($cond_date) . " AND del_flag = 0";

        // Dynamically build column names
        $curetype = 'patient_curetype' . ($daySuffix !== '' ? $daySuffix : '');
        $usefrom = 'patient_usefrom' . ($daySuffix !== '' ? $daySuffix : '');
        $useto = 'patient_useto' . ($daySuffix !== '' ? $daySuffix : '');
        $repeat = 'patient_repeat' . ($daySuffix !== '' ? $daySuffix : '');

        $this->db->select('id, patient_name, (patient_curetype'.$daySuffix.') as patient_curetype, (patient_usefrom'.$daySuffix.') as patient_usefrom, (patient_useto'.$daySuffix.') as patient_useto, (patient_repeat'.$daySuffix.') as patient_repeat');
        $this->db->from('tbl_patient');
        $this->db->where('del_flag', 0);
        $this->db->where("id NOT IN ($subQuery)", null, false);

        // Add conditions based on weekday columns
        $this->db->where("$curetype !=", 0);
        $this->db->where("$usefrom !=", '');
        $this->db->where("$useto !=", '');
        $this->db->where("$repeat !=", 0);

        return $this->db->get()->result_array();
    }
    
    function get_total_List($select, $where_data, $cond_start_date, $cond_end_date, $count_flag = false, $page = 10, $offset = 0, $order_by = '')
    {
        if (empty($select)) {
            $select = 'tbl_schedule.*, tbl_schedule.id as schedule_id, tbl_patient.id as patient_id, tbl_patient.patient_name, tbl_staff.staff_name, tbl_company.company_id, tbl_company.company_name';
        }

        $this->db->select($select);
        $this->db->from('tbl_schedule');

        // Join related tables
        $this->db->join('tbl_patient', 'tbl_patient.id = tbl_schedule.patient_id', 'left');
        $this->db->join('tbl_staff', 'tbl_staff.staff_id = tbl_schedule.staff_id', 'left');
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
            $this->db->group_end();
        }

        // Logical delete flag
        if (!empty($cond_start_date)) {
            $this->db->where('tbl_schedule.schedule_date >=', $cond_start_date);
        }

        if (!empty($cond_end_date)) {
            $this->db->where('tbl_schedule.schedule_date <=', $cond_end_date);
        }

        $this->db->where('tbl_schedule.del_flag', 0);


        // Optional sorting
        if ($order_by && is_array($order_by)) {
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        $this->db->order_by('tbl_company.company_name', 'ASC');
        $this->db->order_by('tbl_staff.staff_name', 'ASC');
        $this->db->order_by('tbl_patient.patient_name', 'ASC');
        $this->db->order_by('tbl_schedule.schedule_date', 'ASC');

        
        // Return data

        if (!$count_flag) {
            $this->db->limit($page, $offset);

            $query = $this->db->get();
            $result = $query->result_array();
            return $result;
        } else {
            return $this->db->count_all_results();
        }
    }

    function schedule_add($data) {
        $this->db->insert($this->table, $data);
    }

    function schedule_delete($value) {
        $this->db->where('id', $value);
        $this->db->update($this->table, ['del_flag' => 1]);
        return true;
    }

    function schedule_update($id,$value) {
        $this->db->where('id', $id);
        $this->db->update($this->table,$value);
        return true;
    }

    function get_form_data($id)
    {
        if (empty($select)) {
            $select = 'tbl_schedule.*, tbl_schedule.id as schedule_id, tbl_patient.id as patient_id, tbl_patient.patient_name, tbl_staff.staff_name, tbl_company.company_name';
        }

        $this->db->select($select);
        $this->db->from('tbl_schedule');
        $this->db->where('tbl_schedule.id', $id);

        // Join related tables
        $this->db->join('tbl_patient', 'tbl_patient.id = tbl_schedule.patient_id', 'left');
        $this->db->join('tbl_staff', 'tbl_staff.staff_id = tbl_schedule.staff_id', 'left');
        $this->db->join('tbl_company', 'tbl_company.company_id = tbl_staff.company_id', 'left');

        $query = $this->db->get();
        return $query->row_array();
    }

}
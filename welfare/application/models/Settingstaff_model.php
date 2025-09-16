<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Settingstaff_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_setting_staff';
        $this->primary_key = 'id';
    }

    function get_today_data($data) 
    {
        $this->db->select('BaseTbl.*, Staff.staff_name');
        $this->db->from('tbl_setting_staff as BaseTbl');
        $this->db->join('tbl_staff as Staff', 'Staff.staff_id = BaseTbl.staff_id','left');
        if(!empty($data['staff_id'])) {
            if($this->user['staff_role'] != 1) {
                $this->db->where('BaseTbl.staff_id', $data['staff_id']);
            }
        }
        
        $query = $this->db->get();

        $result = $query->result();  
        return $result;
    }

}
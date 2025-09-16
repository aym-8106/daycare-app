<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Setting_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_setting';
        $this->primary_key = 'id';
    }

    function getSetting($key,$company_id){

        $this->db->select('key_value');
        $this->db->from($this->table);
        $this->db->where('key_name', $key);
        $this->db->where('company_id', $company_id);
        $query = $this->db->get()->row_array();
        return !empty($query['key_value'])? $query['key_value']:'';
    }

    function setSetting($key,$value,$company_id){

        $this->db->where('key_name', $key);
        $this->db->where('company_id', $company_id);
        $this->db->update($this->table,array('key_value'=>$value));
        return $this->db->affected_rows();
    }

    function registerSetting($key,$value,$company_id){
        if($this->getSetting($key,$company_id)){
            return $this->setSetting($key,$value,$company_id);
        }else{
            $data = array(
                'key_name'=>$key,
                'key_value'=>$value,
                'company_id'=>$company_id,
            );

            return $this->add($data);
        }
    }

}
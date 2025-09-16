<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Employtype_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_employtype';
        $this->primary_key = 'employtypeId';
    }

    function getAll()
    {
        $this->db->select('*');
        $this->db->from($this->table);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }
}
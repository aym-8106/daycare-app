<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Keyword_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_keyword';
        $this->primary_key = 'id';
    }

    function isExist($keyword, $id, $company_id)
    {

        $this->db->where('company_id', $company_id);
        $this->db->where('keyword', $keyword);
        if (!empty($id)) $this->db->where('id !=', $id);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }
}
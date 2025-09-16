<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Query_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_search';
        $this->primary_key = 'search_id';
    }

    function getList($select,$where_data, $count_flag=false,$page=10, $offset=0,$order_by='')
    {
        if(empty($select)){
            $select = '*,ok_cnt+cancel_cnt as all_cnt, IF(search_cnt>0, 100*ok_cnt/(ok_cnt+cancel_cnt),0) as ok_rate';
        }
        $this->db->select($select, FALSE);
        $this->db->from($this->table);
        if(is_array($where_data)){
            foreach ($where_data as $key => $value){
                if($key=='search_text') {
                    if (!empty($value)) $this->db->like($key, $value);
                }elseif($key=="start_date"){
                    if (!empty($value)) $this->db->where('search_time>=', $value);
                }elseif($key=="end_date"){
                    if (!empty($value)) $this->db->where('search_time<=', $value);
                }else{
                    $this->db->where($key,$value);
                }
            }
        }
        if($order_by && is_array($order_by)){
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
            $this->db->order_by('search_time', 'desc');
        }
        if(!$count_flag){
            $this->db->limit($page, $offset);
            $query = $this->db->get();
            $result = $query->result();
            return $result;
        }else{
            return  $this->db->count_all_results();
        }
    }
}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Base_model extends CI_Model
{
    protected $table = 'tbl_company';
    protected $primary_key = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    function debug()
    {
        $sql = $this->db->last_query();
        var_dump($sql);
        die;
    }

    function getListCount($where = array())
    {

        $this->db->from($this->table);
        if (!empty($where)) $this->db->where($where);
        return $this->db->count_all_results();
    }

    function getList($select, $where_data, $count_flag = false, $page = 10, $offset = 0, $order_by = '')
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

    function getList_rows($select, $where_data, $count_flag = false, $page = 10, $offset = 0, $order_by = '')
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

    public function getDataByParam($param = null)
    {
        if ($param) {
            $this->db->where($param);
        }
        $result = $this->db->get($this->table)->result_array();
        return $result;
    }

    public function getOneByParam($param)
    {
        if ($param) {
            $this->db->where($param);
        }
        $result = $this->db->get($this->table)->result_array();
        if ($result)
            return $result[0];
    }

    function getFromId($_id)
    {

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($this->primary_key, $_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get($id, $key = '')
    {
        if ($key == '') {
            $key = $this->primary_key;
        }
        $this->db->where($key, $id);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }
        return array();
    }

    public function getID($val, $key = '')
    {
        if ($key == '') {
            return 0;
        }
        $this->db->where($key, $val);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row[$this->primary_key];
        }
        return 0;
    }

    /**
     * @param $data
     * @param $key
     */
    function register($data, $key = '')
    {
        if (empty($key)) $key = $this->primary_key;
        if (!isset($data[$key])) return $this->add($data);
        $row = $this->get($data[$key], $key);
        if (!empty($row)) {
            return $this->edit($data, $key);
        } else {
            return $this->add($data);
        }
    }

    /**
     * @param $data
     * @param $key
     * @return bool
     */
    function edit($data, $key = '')
    {
        if (empty($key)) $key = $this->primary_key;
        $query = $this->db->set($data)
            ->where($key, $data[$key])
            ->update($this->table);
        return $data[$key];
    }

    function update($data, $key = '')
    {
        if (empty($key)) $key = $this->primary_key;
        $query = $this->db->set($data)
            ->where($key, $data[$key])
            ->update($this->table);
        return $query;
    }

    function insert_batch($data)
    {
        $this->db->insert_batch($this->table, $data);
        return true;
    }

    function update_batch($data, $key = 'id')
    {
        if (empty($key)) $key = $this->primary_key;
        $this->db->update_batch($this->table, $data, $key);
        return true;
    }

    /**
     * @param $data
     * @return mixed
     */
    function add($data)
    {
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /**
     * @param $value
     * @param $key
     * @return bool
     */
    function delete($value, $key = 'id')
    {
        $this->db->set('del_flag', 1);
        $this->db->where($key, $value);
        $this->db->update($this->table);
        return true;
    }

    function delete_force($value, $key = 'id')
    {
        $this->db->where($key, $value);
        $this->db->delete($this->table);
        return true;
    }

    public function getTwoKey($data, $key1, $key2)
    {
        if (empty($key1) || empty($key2)) return;
        if (empty($data[$key1]) || empty($data[$key2])) return;
        $this->db->where($key1, $data[$key1]);
        $this->db->where($key2, $data[$key2]);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }
        return array();
    }

    function editTwokey($data, $key1, $key2)
    {
        $query = $this->db->set($data)
            ->where($key1, $data[$key1])
            ->where($key2, $data[$key2])
            ->update($this->table);
        return $query;
    }

    function registerTwoKey($data, $key1, $key2)
    {
        if (empty($key1) || empty($key2)) return;
        if (empty($data[$key1]) || empty($data[$key2])) return;
        $row = $this->getTwoKey($data, $key1, $key2);
        if (!empty($row)) {
            return $this->editTwoKey($data, $key1, $key2);
        } else {
            return $this->add($data);
        }
    }

    public function getThreeKey($data, $key1, $key2, $key3)
    {
        if (empty($key1) || empty($key2) || empty($key3)) return;
        if (empty($data[$key1]) || empty($data[$key2]) || empty($data[$key3])) return;
        $this->db->where($key1, $data[$key1]);
        $this->db->where($key2, $data[$key2]);
        $this->db->where($key3, $data[$key3]);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }
        return array();
    }

    function editThreekey($data, $key1, $key2, $key3)
    {
        $query = $this->db->set($data)
            ->where($key1, $data[$key1])
            ->where($key2, $data[$key2])
            ->where($key3, $data[$key3])
            ->update($this->table);
        return $query;
    }

    function registerThreeKey($data, $key1, $key2, $key3)
    {
        if (empty($key1) || empty($key2) || empty($key3)) return;
        if (empty($data[$key1]) || empty($data[$key2]) || empty($data[$key3])) return;
        $row = $this->getThreeKey($data, $key1, $key2, $key3);
        if (!empty($row)) {
            return $this->editThreekey($data, $key1, $key2, $key3);
        } else {
            return $this->add($data);
        }
    }
}

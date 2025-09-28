<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Jobtype_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_jobtype';
        $this->primary_key = 'jobtypeId';
    }

    function getAll()
    {
        $this->db->select('*');
        $this->db->from($this->table);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    /**
     * スタッフ管理用の職種一覧（事務を確実に含める）
     */
    function getStaffJobTypes()
    {
        $this->db->select('*');
        $this->db->from($this->table);

        $query = $this->db->get();
        $result = $query->result_array();

        // 事務が存在しない場合は追加
        $has_jimu = false;
        foreach ($result as $jobtype) {
            if ($jobtype['jobtype'] == '事務') {
                $has_jimu = true;
                break;
            }
        }

        if (!$has_jimu) {
            // 事務を追加（最小限のカラムのみ）
            $jimu_data = array(
                'jobtype' => '事務'
            );
            $this->db->insert($this->table, $jimu_data);
            $jimu_id = $this->db->insert_id();

            // 結果に追加
            $jimu_data['jobtypeId'] = $jimu_id;
            $result[] = $jimu_data;
        }

        return $result;
    }
}
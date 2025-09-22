<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Company_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_company';
        $this->primary_key = 'company_id';
    }

    function getList($select,$where_data, $count_flag=false,$page=10, $offset=0,$order_by='')
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
                $this->db->group_start();
                $this->db->like('company_email', $where_data);
                $this->db->or_like('company_name', $where_data);
                $this->db->group_end();
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

    function loginHistory($search,$count_flag=false, $page=10, $segment=0)
    {
        $this->db->select('*');
        $this->db->from('tbl_last_login');
        if(!empty($search['searchText'])) {
            $likeCriteria = "(sessionData  LIKE '%".$search['searchText']."%')";
            $this->db->where($likeCriteria);
        }
        if(!empty($search['fromDate'])) {
            $likeCriteria = "DATE_FORMAT(createdDtm, '%Y-%m-%d' ) >= '".date('Y-m-d', strtotime($search['fromDate']))."'";
            $this->db->where($likeCriteria);
        }
        if(!empty($search['toDate'])) {
            $likeCriteria = "DATE_FORMAT(createdDtm, '%Y-%m-%d' ) <= '".date('Y-m-d', strtotime($search['toDate']))."'";
            $this->db->where($likeCriteria);
        }
        if(!empty($search['company_id'])) {
            $this->db->where('company_id', $search['company_id']);
        }
        if($count_flag){
            return $this->db->count_all_results();
        }
        $this->db->order_by('id', 'DESC');
        $this->db->limit($page, $segment);
        return $this->db->get()->result();
    }

    function getSetting($_id){

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($this->primary_key, $_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getFromDomain($domain){

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('company_wix_domain', $domain);
        $this->db->where('del_flag',0);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getFromUUID($uuid){

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('uuid', $uuid);
        $this->db->where('del_flag',0);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getIDFromUUID($uuid){

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('uuid', $uuid);
        $this->db->where('del_flag',0);
        $query = $this->db->get();
        $company = $query->row_array();
        if (empty($company) || empty($company['company_wix_domain']) || empty($company['company_wix_key']) || empty($company['company_wix_secret'])) {
            return 0;
        }
        return $company['company_id'];
    }
    function saveSetting($data){

        if(!empty($data[$this->primary_key])){
            $this->db->where($this->primary_key, $data[$this->primary_key]);
            $this->db->update($this->table,$data);
            return true;
        }
        return false;
    }

    function login($data)
    {
        if(empty($data['email']) || empty($data['password'])) {
            return false;
        }
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('company_email',$data['email']);
        $this->db->where('company_password',sha1($data['password']));
        $this->db->where("del_flag", 0);
        return $this->db->get()->row_array();
    }

    function checkEmailExists($email,$_id=0)
    {
        $this->db->select("company_email");
        $this->db->from($this->table);
        $this->db->where("company_email", $email);
        $this->db->where("del_flag", 0);
        if($_id != 0){
            $this->db->where("company_id !=", $_id);
        }
        $result = $this->db->get()->result();
        return $result;
    }
    function resetPasswordUser($data)
    {
        $result = $this->db->insert('tbl_reset_password', $data);

        if($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    /**
     * This function is used to get customer information by email-id for forget password email
     * @param string $email : Email id of customer
     * @return object $result : Information of customer
     */
    function getCustomerInfoByEmail($email)
    {
        $this->db->select('company_id, company_email, company_name');
        $this->db->from('tbl_company');
        $this->db->where('del_flag', 0);
        $this->db->where('company_email', $email);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function used to check correct activation deatails for forget password.
     * @param string $email : Email id of user
     * @param string $activation_id : This is activation string
     */
    function checkActivationDetails($email, $activation_id)
    {
        $this->db->select('id');
        $this->db->from('tbl_reset_password');
        $this->db->where('email', $email);
        $this->db->where('activation_id', $activation_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    // This function used to create new password by reset link
    function createPasswordUser($email, $password)
    {
        $this->db->where('company_email', $email);
        $this->db->where('dle_flag', 0);
        $this->db->update('tbl_company', array('company_password'=>sha1($password)));
        $this->db->delete('tbl_reset_password', array('email'=>$email));
    }

    function lastLogin($loginInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_last_login', $loginInfo);
        $this->db->trans_complete();
    }


    function matchOldPassword($userId, $oldPassword)
    {
        $this->db->select('company_id, company_password');
        $this->db->where('company_id', $userId);
        $this->db->where('company_password', sha1($oldPassword));
        $this->db->where('del_flag', 0);
        $query = $this->db->get($this->table);

        $user = $query->result();

        if(!empty($user)){
            return $user;
        } else {
            return array();
        }
    }
    function changePassword($userId, $userInfo)
    {
        $this->db->where('company_id', $userId);
        $this->db->where('del_flag', 0);
        $this->db->update($this->table, $userInfo);

        return $this->db->affected_rows();
    }

    function get_all_data()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('tbl_company.del_flag', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 事業所番号の重複チェック
     */
    function checkCompanyNumberExists($company_number, $company_id = 0)
    {
        $this->db->select('company_id');
        $this->db->from($this->table);
        $this->db->where('company_number', $company_number);
        $this->db->where('del_flag', 0);

        if ($company_id > 0) {
            $this->db->where('company_id !=', $company_id);
        }

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * 事業所情報を更新
     */
    function updateCompany($data, $company_id)
    {
        $this->db->where('company_id', $company_id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

}
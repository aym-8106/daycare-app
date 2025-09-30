<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_users';
        $this->primary_key = 'userId';
    }

    // function getList($select,$where_data, $count_flag=false,$page=10, $offset=0,$order_by='')
    // {
    //     if(empty($select)){
    //         $select = '*';
    //     }
    //     $this->db->select($select);
    //     $this->db->from($this->table);
    //     if(is_array($where_data)){
    //         foreach ($where_data as $key => $value){
    //             $this->db->where($key,$value);
    //         }
    //     }
    //     $this->db->where('del_flag',0);
    //     if($order_by && is_array($order_by)){
    //         foreach ($order_by as $key => $value) {
    //             $this->db->order_by($key, $value);
    //         }
    //     }
    //     if(!$count_flag){
    //         if($page){
    //             $this->db->limit($page, $offset);
    //         }
    //         $query = $this->db->get();
    //         $result = $query->result_array();
    //         return $result;
    //     }else{
    //         return $this->db->count_all_results();
    //     }
    // }

    function getListCount($where = array())
    {
        // tbl_usersからユーザー数を取得
        $this->db->from('tbl_users');
        if (!empty($where)) {
            // del_flagをisDeletedに変換
            if (isset($where['del_flag'])) {
                $where['isDeleted'] = $where['del_flag'];
                unset($where['del_flag']);
            }
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    function getList($select,$where_data, $count_flag=false,$page=10, $offset=0,$order_by='')
    {
        // クエリキャッシュをクリア
        $this->db->flush_cache();
        // tbl_usersからユーザー一覧を取得
        $this->db->select('Users.userId as staff_id, Users.company_id, Users.name as staff_name, Users.email as staff_mail_address, Users.roleId, Users.jobtype_id, Users.createdDtm as create_date, Users.updatedDtm as update_date, Company.company_name, Role.role, IFNULL(Jobtype.jobtype, "") as jobtype, "" as employtype');
        $this->db->from('tbl_users as Users');
        $this->db->join('tbl_company as Company', 'Company.company_id = Users.company_id','left');
        $this->db->join('tbl_roles as Role', 'Role.roleId = Users.roleId','left');
        $this->db->join('tbl_jobtype as Jobtype', 'Jobtype.jobtypeId = Users.jobtype_id','left');

        if(is_array($where_data)){
            foreach ($where_data as $key => $value){
                if($key == "searchText") {
                    $this->db->group_start();
                    $this->db->like('Users.name', $value);
                    $this->db->or_like('Users.email', $value);
                    $this->db->or_like('Company.company_name', $value);
                    $this->db->group_end();
                } else {
                    $this->db->where($key,$value);
                }
            }
        }else{
            if(!empty($where_data)) {
                $this->db->group_start();
                $this->db->like('Users.name', $where_data);
                $this->db->or_like('Users.email', $where_data);
                $this->db->or_like('Company.company_name', $where_data);
                $this->db->group_end();
            }
        }
        $this->db->where('Users.isDeleted', 0);

        if($order_by && is_array($order_by)){
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        } else {
            // デフォルトのソート順を追加（最新の更新順）
            $this->db->order_by('Users.updatedDtm', 'DESC');
            $this->db->order_by('Users.userId', 'ASC');
        }

        if(!$count_flag){
            if($page){
                $this->db->limit($page, $offset);
            }
            try {
                $query = $this->db->get();
                // 一時的なデバッグ情報 - コメントアウトしておきます
                // error_log('Staff getList SQL: ' . $this->db->last_query());
                $result = $query->result_array();

                // 管理者データの会社名を設定
                foreach ($result as &$user) {
                    if ($user['roleId'] == 1 || empty($user['company_name'])) {
                        $user['company_name'] = '管理者';
                    }
                }

                return $result;
            } catch (Exception $e) {
                log_message('error', 'Database error: ' . $e->getMessage());
                echo 'Error: ' . $e->getMessage();
                return false;
            }
        }else{
            return $this->db->count_all_results();
        }
    }

    function getFromId($_id)
    {
        // クエリキャッシュをクリア
        $this->db->flush_cache();
        // tbl_usersテーブルからuserIdで検索
        $this->db->select('Users.userId as staff_id, Users.company_id, Users.name as staff_name, Users.email as staff_mail_address, Users.password as staff_password, Users.roleId, Users.roleId as staff_role, Users.jobtype_id, Users.jobtype_id as staff_jobtype, Company.company_name');
        $this->db->from('tbl_users as Users');
        $this->db->join('tbl_company as Company', 'Company.company_id = Users.company_id', 'left');
        $this->db->where('Users.userId', $_id);
        $this->db->where('Users.isDeleted', 0);
        $query = $this->db->get();
        $result = $query->row_array();

        if ($result) {
            // Add company name for admin users (roleId = 1)
            if ($result['roleId'] == 1) {
                $result['company_name'] = '管理者';
            }
            // jobtype_idが設定されていない場合はデフォルト値を設定
            if (!isset($result['staff_jobtype']) || $result['staff_jobtype'] === null) {
                $result['staff_jobtype'] = 1; // デフォルトの職種ID
            }
            // 勤務形態は現在未実装
            $result['staff_employtype'] = 0;

            // ビューで期待される必須フィールドの確認と設定
            if (empty($result['staff_name'])) {
                $result['staff_name'] = $result['name'] ?? '';
            }
            if (empty($result['staff_mail_address'])) {
                $result['staff_mail_address'] = $result['email'] ?? '';
            }
            if (empty($result['staff_id'])) {
                $result['staff_id'] = $result['userId'] ?? '';
            }

            return $result;
        }

        return array();
    }

    function get_staffList()
    {
        $this->db->select('*');
        $this->db->from('tbl_jobtype');
        $this->db->order_by('jobtypeId', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_employList()
    {
        $this->db->select('*');
        $this->db->from('tbl_employtype');
        $this->db->order_by('employtypeId', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function loginHistory($search,$count_flag=false, $page=10, $segment=0)
    {
        $this->db->select('*');
        $this->db->from('tbl_last_login');
        if(!empty($search['searchText'])) {
            $this->db->like('sessionData', $search['searchText']);
        }
        if(!empty($search['fromDate'])) {
            $from_date = date('Y-m-d', strtotime($search['fromDate']));
            $this->db->where("DATE(createdDtm) >=", $from_date);
        }
        if(!empty($search['toDate'])) {
            $to_date = date('Y-m-d', strtotime($search['toDate']));
            $this->db->where("DATE(createdDtm) <=", $to_date);
        }
        if(!empty($search['staff_id'])) {
            $this->db->where('staff_id', $search['staff_id']);
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
        return $company['staff_id'];
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

        // Load password library
        $this->load->library('password_lib');

        // Check tbl_users table with company join
        $this->db->select('Users.userId as staff_id, Users.company_id, Users.name as staff_name, Users.email, Users.password as staff_password, Users.roleId, Company.company_name');
        $this->db->from('tbl_users as Users');
        $this->db->join('tbl_company as Company', 'Company.company_id = Users.company_id', 'left');
        $this->db->where('Users.email', $data['email']);
        $this->db->where('Users.isDeleted', 0);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $user_result = $query->row_array();

            // Verify password using new library (supports both bcrypt and sha1)
            if ($this->password_lib->verify($data['password'], $user_result['staff_password'])) {

                // If password needs rehashing, update it
                if ($this->password_lib->needs_rehash($user_result['staff_password'])) {
                    $new_hash = $this->password_lib->hash($data['password']);
                    $this->db->where('userId', $user_result['staff_id']);
                    $this->db->update('tbl_users', array('password' => $new_hash));
                }

                // Add company name for admin users (roleId = 1)
                if ($user_result['roleId'] == 1) {
                    $user_result['company_name'] = '管理者';
                }

                return $user_result;
            }
        }

        return array();
    }

    function checkEmailExists($email,$_id=0)
    {
        // Email functionality disabled for staff_id login system
        return array();
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
        // Email functionality disabled for staff_id login system
        return null;
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
        // Email functionality disabled for staff_id login system
        return false;
    }

    function lastLogin($loginInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_last_login', $loginInfo);
        $this->db->trans_complete();
    }


    function matchOldPassword($userId, $oldPassword)
    {
        $this->db->select('staff_id, staff_password');
        $this->db->where('staff_id', $userId);
        $this->db->where('staff_password', sha1($oldPassword));
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
        $this->db->where('staff_id', $userId);
        $this->db->where('del_flag', 0);
        $this->db->update($this->table, $userInfo);

        return $this->db->affected_rows();
    }

    function get_staff($company_id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->where('del_flag', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    function staff_edit($regdata, $data)
    {
        $this->db->where('userId', $regdata['staff_id']);
        $this->db->where('company_id', $regdata['company_id']);
        $this->db->update('tbl_users', $data);
        return $this->db->affected_rows();
    }

    function get_all_data()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('isDeleted', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 事業所内で次のスタッフ番号を生成
     * @param int $company_id 事業所ID
     * @return string スタッフ番号（例：STAFF-001）
     */
    function generate_next_staff_number($company_id)
    {
        // 該当事業所の最大スタッフ番号を取得
        $this->db->select('staff_number');
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->where('staff_number IS NOT NULL');
        $this->db->where('del_flag', 0);
        $this->db->order_by('staff_number', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $last_number = $query->row()->staff_number;
            // STAFF-001 から 001 部分を抽出
            $number_part = intval(substr($last_number, 6));
            $next_number = $number_part + 1;
        } else {
            $next_number = 1;
        }

        return 'STAFF-' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * スタッフ登録時にスタッフ番号を自動生成
     */
    function insert_with_staff_number($data)
    {
        if (isset($data['company_id']) && empty($data['staff_number'])) {
            $data['staff_number'] = $this->generate_next_staff_number($data['company_id']);
        }

        return $this->db->insert($this->table, $data);
    }

    /**
     * 事業所のスタッフ一覧を取得（ユーザーID順）
     */
    function get_staff_with_numbers($company_id)
    {
        $this->db->select('Users.userId as staff_id, Users.name as staff_name, Users.email, Users.roleId as staff_role, Role.role');
        $this->db->from('tbl_users as Users');
        $this->db->join('tbl_roles as Role', 'Role.roleId = Users.roleId', 'left');
        $this->db->where('Users.company_id', $company_id);
        $this->db->where('Users.isDeleted', 0);
        $this->db->order_by('Users.userId', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

}
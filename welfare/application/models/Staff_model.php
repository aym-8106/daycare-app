<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_staff';
        $this->primary_key = 'staff_id';
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

    function getList($select,$where_data, $count_flag=false,$page=10, $offset=0,$order_by='')
    {
        // スタッフ一覧を取得
        $this->db->select('BaseTbl.staff_id, BaseTbl.company_id, BaseTbl.staff_name, BaseTbl.staff_mail_address, BaseTbl.create_date, BaseTbl.update_date, Company.company_name, Role.role, Jobtype.jobtypeId, Jobtype.jobtype, Employtype.employtypeId, Employtype.employtype');
        $this->db->from('tbl_staff as BaseTbl');
        $this->db->join('tbl_company as Company', 'Company.company_id = BaseTbl.company_id','left');
        $this->db->join('tbl_roles as Role', 'Role.roleId = BaseTbl.staff_role','left');
        $this->db->join('tbl_jobtype as Jobtype', 'Jobtype.jobtypeId = BaseTbl.staff_jobtype','left');
        $this->db->join('tbl_employtype as Employtype', 'Employtype.employtypeId = BaseTbl.staff_employtype','left');
        if(is_array($where_data)){
            foreach ($where_data as $key => $value){
                if($key == "searchText") {
                    $this->db->group_start();
                    $this->db->like('BaseTbl.staff_name', $value);
                    $this->db->or_like('Company.company_name', $value);
                    $this->db->or_like('BaseTbl.staff_mail_address', $value);
                    $this->db->group_end();
                } else {
                    $this->db->where($key,$value);
                }
            }
        }else{
            if(!empty($where_data)) {
                $this->db->group_start();
                $this->db->like('BaseTbl.staff_name', $where_data);
                $this->db->or_like('Company.company_name', $where_data);
                $this->db->or_like('BaseTbl.staff_mail_address', $where_data);
                $this->db->group_end();
            }
        }
        $this->db->where('BaseTbl.del_flag',0);
        if($order_by && is_array($order_by)){
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        if(!$count_flag){
            if($page){
                $this->db->limit($page, $offset);
            }
            try {
                $query = $this->db->get();
                $result = $query->result_array();

                // 管理者データも追加
                $this->db->select('admin_id as staff_id, 0 as company_id, admin_name as staff_name, admin_email as staff_mail_address, create_date, update_date');
                $this->db->from('tbl_admin');
                $this->db->where('del_flag', 0);

                // 検索条件があれば管理者にも適用
                if (is_array($where_data)) {
                    foreach ($where_data as $key => $value) {
                        if ($key == "searchText") {
                            $this->db->group_start();
                            $this->db->like('admin_name', $value);
                            $this->db->or_like('admin_email', $value);
                            $this->db->group_end();
                        }
                    }
                } else {
                    if (!empty($where_data)) {
                        $this->db->group_start();
                        $this->db->like('admin_name', $where_data);
                        $this->db->or_like('admin_email', $where_data);
                        $this->db->group_end();
                    }
                }

                $admin_query = $this->db->get();
                $admin_result = $admin_query->result_array();

                // 管理者データを整形
                foreach ($admin_result as &$admin) {
                    $admin['company_name'] = '管理者';
                    $admin['role'] = '管理者';
                    $admin['jobtypeId'] = null;
                    $admin['jobtype'] = '管理者';
                    $admin['employtypeId'] = null;
                    $admin['employtype'] = '管理者';
                }

                // スタッフと管理者を結合
                $result = array_merge($admin_result, $result);

                return $result;
            } catch (Exception $e) {
                log_message('error', 'Database error: ' . $e->getMessage());
                echo 'Error: ' . $e->getMessage();
                return false;
            }
        }else{
            // カウント時は管理者も含める
            $staff_count = $this->db->count_all_results();

            $this->db->select('admin_id');
            $this->db->from('tbl_admin');
            $this->db->where('del_flag', 0);

            // 検索条件があれば管理者にも適用
            if (is_array($where_data)) {
                foreach ($where_data as $key => $value) {
                    if ($key == "searchText") {
                        $this->db->group_start();
                        $this->db->like('admin_name', $value);
                        $this->db->or_like('admin_email', $value);
                        $this->db->group_end();
                    }
                }
            } else {
                if (!empty($where_data)) {
                    $this->db->group_start();
                    $this->db->like('admin_name', $where_data);
                    $this->db->or_like('admin_email', $where_data);
                    $this->db->group_end();
                }
            }

            $admin_count = $this->db->count_all_results();

            return $staff_count + $admin_count;
        }
    }

    function getFromId($_id)
    {
        // まずスタッフテーブルを確認
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($this->primary_key, $_id);
        $query = $this->db->get();
        $result = $query->row_array();

        if ($result) {
            return $result;
        }

        // スタッフが見つからない場合は管理者テーブルを確認
        $this->db->select('admin_id as staff_id, admin_name as staff_name, admin_email as staff_mail_address, admin_password as staff_password, 0 as company_id, create_date, update_date');
        $this->db->from('tbl_admin');
        $this->db->where('admin_id', $_id);
        $this->db->where('del_flag', 0);
        $admin_query = $this->db->get();
        $admin_result = $admin_query->row_array();

        if ($admin_result) {
            $admin_result['user_type'] = 'admin';
            return $admin_result;
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
        if(empty($data['email']) || empty($data['staff_password'])) {
            return false;
        }

        // Load password library
        $this->load->library('password_lib');

        // 統合されたスタッフテーブルから検索
        $this->db->select('BaseTbl.staff_id, BaseTbl.company_id, BaseTbl.staff_name, BaseTbl.staff_mail_address as staff_email, BaseTbl.staff_password, BaseTbl.staff_role, Company.company_name');
        $this->db->from('tbl_staff as BaseTbl');
        $this->db->join('tbl_company as Company', 'Company.company_id = BaseTbl.company_id', 'left');
        $this->db->where('BaseTbl.staff_mail_address', $data['email']);
        $this->db->where('Company.del_flag', 0);
        $this->db->where('BaseTbl.del_flag', 0);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row_array();

            // Verify password using new library (supports both bcrypt and sha1)
            if ($this->password_lib->verify($data['staff_password'], $result['staff_password'])) {

                // If password needs rehashing, update it
                if ($this->password_lib->needs_rehash($result['staff_password'])) {
                    $new_hash = $this->password_lib->hash($data['staff_password']);
                    $this->db->where('staff_id', $result['staff_id']);
                    $this->db->update('tbl_staff', array('staff_password' => $new_hash));
                }

                // Add user_type based on staff_role
                $result['user_type'] = ($result['staff_role'] == 1) ? 'admin' : 'staff';

                return $result;
            }
        }

        return array();
    }

    function checkEmailExists($email,$_id=0)
    {
        $this->db->select("staff_email");
        $this->db->from($this->table);
        $this->db->where("staff_email", $email);
        $this->db->where("del_flag", 0);
        if($_id != 0){
            $this->db->where("staff_id !=", $_id);
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
        $this->db->select('staff_id, staff_mail_address, company_name');
        $this->db->from('tbl_staff');
        $this->db->where('del_flag', 0);
        $this->db->where('staff_mail_address', $email);
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
        // Load password library
        $this->load->library('password_lib');

        $hashed_password = $this->password_lib->hash($password);

        $this->db->where('staff_mail_address', $email);
        $this->db->where('del_flag', 0);
        $this->db->update('tbl_staff', array('staff_password' => $hashed_password));
        $this->db->delete('tbl_reset_password', array('email' => $email));
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
        $this->db->where('staff_id', $regdata['staff_id']);
        $this->db->where('company_id', $regdata['company_id']);
        $this->db->update('tbl_staff', $data);
        return $this->db->affected_rows();
    }

    function get_all_data()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('tbl_staff.del_flag', 0);
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
     * 事業所のスタッフ一覧を取得（スタッフ番号順）
     */
    function get_staff_with_numbers($company_id)
    {
        $this->db->select('staff_id, staff_name, staff_number, staff_mail_address, staff_role');
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->where('del_flag', 0);
        $this->db->order_by('staff_number', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

}
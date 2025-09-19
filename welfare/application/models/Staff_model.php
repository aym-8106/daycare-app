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
        $this->db->select('BaseTbl.staff_id, BaseTbl.company_id, BaseTbl.staff_name, BaseTbl.staff_mail_address, BaseTbl.create_date, BaseTbl.update_date, Company.company_name, Role.role, Jobtype.jobtypeId, Jobtype.jobtype, Employtype.employtypeId, Employtype.employtype');
        $this->db->from('tbl_staff as BaseTbl');
        $this->db->join('tbl_company as Company', 'Company.company_id = BaseTbl.company_id','left');
        $this->db->join('tbl_roles as Role', 'Role.roleId = BaseTbl.staff_role','left');
        $this->db->join('tbl_jobtype as Jobtype', 'Jobtype.jobtypeId = BaseTbl.staff_jobtype','left');
        $this->db->join('tbl_employtype as Employtype', 'Employtype.employtypeId = BaseTbl.staff_employtype','left');
        if(is_array($where_data)){
            foreach ($where_data as $key => $value){
                if($key == "searchText") {
                    $likeCriteria = "(BaseTbl.staff_name  LIKE '%".$value."%'
                                    OR  Company.company_name  LIKE '%".$value."%'
                                    OR  BaseTbl.staff_mail_address  LIKE '%".$value."%')";
                    $this->db->where($likeCriteria);
                } else {
                    $this->db->where($key,$value);
                }
            }
        }else{
            if(!empty($where_data)) {
                $likeCriteria = "(BaseTbl.staff_name  LIKE '%".$where_data."%'
                                OR  Company.company_name  LIKE '%".$where_data."%'
                                OR  BaseTbl.staff_mail_address  LIKE '%".$where_data."%')";
                $this->db->where($likeCriteria);
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
        $this->db->where('use_flag',1);
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

        // First check admin table
        $this->db->select('admin_id as staff_id, 0 as company_id, admin_name as staff_name, admin_password as staff_password, "管理者" as company_name, "admin" as user_type');
        $this->db->from('tbl_admin');
        $this->db->where('admin_email', $data['email']);
        $this->db->where('admin_password', sha1($data['staff_password']));
        $this->db->where('del_flag', 0);

        $admin_result = $this->db->get()->row_array();
        if($admin_result) {
            return $admin_result;
        }

        // If admin not found, check staff table
        $this->db->select('BaseTbl.staff_id, BaseTbl.company_id, BaseTbl.staff_name, BaseTbl.staff_password, Company.company_name, "staff" as user_type');
        $this->db->from('tbl_staff as BaseTbl');
        $this->db->join('tbl_company as Company', 'Company.company_id = BaseTbl.company_id','left');
        $this->db->where('BaseTbl.staff_mail_address',$data['email']);
        $this->db->where('BaseTbl.staff_password',sha1($data['staff_password']));
        $this->db->where("Company.use_flag", 1);
        $this->db->where("Company.del_flag", 0);
        $this->db->where("Company.payment_date>=", date("Y-m-d H:i:s"));
        $this->db->where("BaseTbl.del_flag", 0);

        return $this->db->get()->row_array();
    }

    function checkEmailExists($email,$_id=0)
    {
        $this->db->select("staff_mail_address");
        $this->db->from($this->table);
        $this->db->where("staff_mail_address", $email);
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
        $this->db->where('staff_mail_address', $email);
        $this->db->where('dle_flag', 0);
        $this->db->update('tbl_staff', array('staff_password'=>sha1($password)));
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

}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Staff extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->load->model('Setting_model', 'setting_model');
        $this->load->model('Staff_model', 'staff_model');
        
        $this->load->model('Role_model', 'role_model');
        $this->load->model('Jobtype_model', 'jobtype_model');
        $this->load->model('Employtype_model', 'employtype_model');

        $this->header['page'] = 'staff';
        $this->header['title'] = 'スタッフ管理';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        // キャッシュを無効化して最新データを取得
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');

        $mode = $this->input->post('mode');
        if ($mode == 'update') {
            // use_flag機能は削除されたため、この処理はコメントアウト
            /*
            $use_flag = $this->input->post('use_flag');
            $id = $this->input->post('company_id');
            $data = array(
                'use_flag' => $use_flag,
                'company_id' => $id,
            );
            $this->company_model->saveSetting($data);
            */
        }
        $this->data['search'] = $this->input->post('searchText');

        $this->load->library('pagination');

        $this->data['list_cnt'] = $this->staff_model->getList('*', $this->data['search'], true);
        $returns = $this->_paginationCompress("admin/staff/index", $this->data['list_cnt'], 10, 4);

        $this->data['start_page'] = $returns["segment"] + 1;
        $this->data['end_page'] = $returns["segment"] + $returns["page"];
        if ($this->data['end_page'] > $this->data['list_cnt']) $this->data['end_page'] = $this->data['list_cnt'];
        if (!$this->data['start_page']) $this->data['start_page'] = 1;

        $this->data['list'] = $this->staff_model->getList('*', $this->data['search'], false, $returns['page'], $returns['segment']);
        $this->_load_view_admin("admin/staff/index");
    }

    public function edit($_id)
    {
        if ($_id == null) {
            redirect('admin/staff');
        }

        $staff = $this->staff_model->getFromId($_id);
        if (empty($staff)) {
            redirect('admin/staff');
        }
        $mode = $this->input->post('mode');
        if ($mode == 'save') {
            // デバッグ用：POSTデータを確認
            error_log('POST company_name: ' . $this->input->post('company_name'));
            error_log('POST staff_name: ' . $this->input->post('staff_name'));
            error_log('POST jobtype: ' . $this->input->post('jobtype'));

            // tbl_usersテーブルのフィールド名に合わせてデータを準備
            $this->data['staff'] = array(
                'userId' => $_id,
                'company_id' => $this->input->post('company_name'),
                'name' => $this->input->post('staff_name'),
                'email' => $this->input->post('staff_mail_address'),
                'password' => $this->input->post('staff_password'),
                'roleId' => $this->input->post('role'),
                'jobtype_id' => $this->input->post('jobtype'),
                // employtypeは現在未実装
            );

            // デバッグ用：保存するデータを確認
            error_log('Save data: ' . print_r($this->data['staff'], true));

            $this->form_validation->set_rules('staff_name', 'スタッフ名', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('staff_mail_address', 'メールアドレス', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('staff_password', 'パスワード', 'max_length[20]');
            $this->form_validation->set_rules('staff_password_confirm', 'パスワード（確認）', 'trim|matches[staff_password]|max_length[20]');

            if ($this->form_validation->run() === TRUE) {
                // ユーザーIDを安全に取得
                $user_id = null;
                if (isset($this->user['userId'])) {
                    $user_id = $this->user['userId'];
                } elseif (isset($this->user['user_id'])) {
                    $user_id = $this->user['user_id'];
                } elseif (isset($this->user['id'])) {
                    $user_id = $this->user['id'];
                } else {
                    $user_id = 1; // デフォルト値として管理者ID
                }

                $this->data['staff']['updatedBy'] = $user_id;
                $this->data['staff']['updatedDtm'] = date('Y-m-d H:i:s');
                if (!empty($this->data['staff']['password'])) {
                    // パスワードライブラリを使用してハッシュ化
                    $this->load->library('password_lib');
                    $this->data['staff']['password'] = $this->password_lib->hash($this->data['staff']['password']);
                } else {
                    unset($this->data['staff']['password']);
                }

                $result = $this->staff_model->edit($this->data['staff'], 'userId');
                if ($result) {
                    $this->session->set_flashdata('success', '正常に更新されました。');
                    $this->session->set_flashdata('error', '');
                    // 保存成功後はスタッフ一覧画面にリダイレクト
                    redirect('admin/staff');
                } else {
                    $this->session->set_flashdata('success', '');
                    $this->session->set_flashdata('error', '更新に失敗しました。');
                    // エラーの場合もデータベースから再取得
                    $this->data['staff'] = $this->staff_model->getFromId($_id);
                }
            } else {
                // バリデーションエラーの場合もデータベースから再取得
                $this->data['staff'] = $this->staff_model->getFromId($_id);
            }
        } else {
            $this->data['staff'] = $staff;
        }

        $this->data['companys'] = $this->company_model->getList('*', array(), false, 0);
        $this->data['roles'] = $this->role_model->getStaffRoles();
        // 職種情報を有効化
        $this->data['jobtypes'] = $this->jobtype_model->getStaffJobTypes();
        // 勤務形態のオプションを直接定義
        $this->data['employtypes'] = array(
            array('employtypeId' => 1, 'employtype' => '常勤'),
            array('employtypeId' => 2, 'employtype' => '非常勤')
        );

        $this->_load_view_admin("admin/staff/edit");

    }

    /**
     * This function is used to load the user list
     */
    function add()
    {
        $mode = $this->input->post('mode');
        if ($mode == 'save') {

            // tbl_usersテーブルのフィールド名に合わせてデータを準備
            $this->data['staff'] = array(
                'company_id' => $this->input->post('company_name'),
                'name' => $this->input->post('staff_name'),
                'email' => $this->input->post('staff_mail_address'),
                'password' => $this->input->post('staff_password'),
                'roleId' => $this->input->post('role'),
                'jobtype_id' => $this->input->post('jobtype'),
                // employtypeは現在未実装
            );

            $this->form_validation->set_rules('staff_name', 'スタッフ名', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('staff_mail_address', 'メールアドレス', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('staff_password', 'パスワード', 'required|max_length[20]');
            $this->form_validation->set_rules('staff_password_confirm', 'パスワード（確認）', 'trim|required|matches[staff_password]|max_length[20]');

            if ($this->form_validation->run() === TRUE) {
                // パスワードライブラリを使用してハッシュ化
                $this->load->library('password_lib');
                $this->data['staff']['password'] = $this->password_lib->hash($this->data['staff']['password']);

                // ユーザーIDを安全に取得
                $user_id = null;
                if (isset($this->user['userId'])) {
                    $user_id = $this->user['userId'];
                } elseif (isset($this->user['user_id'])) {
                    $user_id = $this->user['user_id'];
                } elseif (isset($this->user['id'])) {
                    $user_id = $this->user['id'];
                } else {
                    $user_id = 1; // デフォルト値として管理者ID
                }

                $this->data['staff']['createdBy'] = $user_id;
                $this->data['staff']['createdDtm'] = date('Y-m-d H:i:s');
                $this->data['staff']['updatedBy'] = $user_id;
                $this->data['staff']['updatedDtm'] = date('Y-m-d H:i:s');

                $result = $this->staff_model->add($this->data['staff']);
                if ($result) {
                    $this->session->set_flashdata('success', '正常に登録されました。');
                    $this->session->set_flashdata('error', '');

                    redirect('admin/staff');

                } else {
                    $this->session->set_flashdata('success', '');
                    $this->session->set_flashdata('error', '更新に失敗しました。');
                }
            } else {
//                var_dump(validation_errors());;
            }

        }

        $this->data['companys'] = $this->company_model->getList('*', array(), false, 0);
        $this->data['roles'] = $this->role_model->getStaffRoles();
        // 職種情報を有効化
        $this->data['jobtypes'] = $this->jobtype_model->getStaffJobTypes();
        // 勤務形態のオプションを直接定義
        $this->data['employtypes'] = array(
            array('employtypeId' => 1, 'employtype' => '常勤'),
            array('employtypeId' => 2, 'employtype' => '非常勤')
        );

        $this->_load_view_admin("admin/staff/add");

    }

    public function _uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * This function is used to check whether email already exist or not
     */
    function _check_email($email, $company_id = 0)
    {
        if (empty($company_id)) {
            $result = $this->company_model->checkEmailExists($email);
        } else {
            $result = $this->company_model->checkEmailExists($email, $company_id);
        }

        return empty($result) ? true : false;
    }

    /**
     * This function is used to delete the user using userId
     * @return boolean $result : TRUE / FALSE
     */
    function delete()
    {
        $userId = $this->input->post('userId');

        $result = $this->staff_model->delete($userId, 'staff_id');

        if ($result > 0) {
            echo(json_encode(array('status' => TRUE)));
        } else {
            echo(json_encode(array('status' => FALSE)));
        }
    }


    /**
     * This function used to show login history
     * @param number $userId : This is user id
     */
    function loginHistoy($userId = NULL)
    {
        if ($this->isAdmin() == TRUE) {
            $this->loadThis();
        } else {
            $userId = ($userId == NULL ? 0 : $userId);

            $searchText = $this->input->post('searchText');
            $fromDate = $this->input->post('fromDate');
            $toDate = $this->input->post('toDate');

            $data["userInfo"] = $this->user_model->getUserInfoById($userId);

            $data['searchText'] = $searchText;
            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;

            $this->load->library('pagination');

            $count = $this->user_model->loginHistoryCount($userId, $searchText, $fromDate, $toDate);

            $returns = $this->paginationCompress("login-history/" . $userId . "/", $count, 10, 3);

            $data['userRecords'] = $this->user_model->loginHistory($userId, $searchText, $fromDate, $toDate, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'ユーザー：ログイン履歴';

            $this->loadViews("loginHistory", $this->global, $data, NULL);
        }
    }

    /**
     * This function is used to show users profile
     */
    function profile($active = "details")
    {
        $data["userInfo"] = $this->user_model->getUserInfoWithRole($this->vendorId);
        $data["active"] = $active;

        $this->global['pageTitle'] = $active == "詳細" ? 'パスワード変更' : 'プロフィール編集';
        $this->loadViews("profile", $this->global, $data, NULL);
    }

    /**
     * This function is used to update the user details
     * @param text $active : This is flag to set the active tab
     */
    function profileUpdate($active = "details")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('fname', 'Full Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|min_length[10]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_emailExists');

        if ($this->form_validation->run() == FALSE) {
            $this->profile($active);
        } else {
            $name = strtolower($this->security->xss_clean($this->input->post('fname')));
            $mobile = $this->security->xss_clean($this->input->post('mobile'));
            $email = strtolower($this->security->xss_clean($this->input->post('email')));

            $userInfo = array('name' => $name, 'email' => $email, 'mobile' => $mobile, 'updatedBy' => $this->vendorId, 'updatedDtm' => date('Y-m-d H:i:s'));

            $result = $this->user_model->editUser($userInfo, $this->vendorId);

            if ($result == true) {
                $this->session->set_userdata('name', $name);
                $this->session->set_flashdata('success', 'パスワードの更新に成功しました。');
            } else {
                $this->session->set_flashdata('error', 'パスワードの更新に失敗しました。');
            }

            redirect('profile/' . $active);
        }
    }

    /**
     * This function is used to change the password of the user
     * @param text $active : This is flag to set the active tab
     */
    function changePassword($active = "changepass")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('oldPassword', 'Old password', 'required|max_length[20]');
        $this->form_validation->set_rules('newPassword', 'New password', 'required|max_length[20]');
        $this->form_validation->set_rules('cNewPassword', 'Confirm new password', 'required|matches[newPassword]|max_length[20]');

        if ($this->form_validation->run() == FALSE) {
            $this->profile($active);
        } else {
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');

            $resultPas = $this->user_model->matchOldPassword($this->vendorId, $oldPassword);

            if (empty($resultPas)) {
                $this->session->set_flashdata('nomatch', 'Your old password is not correct');
                redirect('profile/' . $active);
            } else {
                $usersData = array('password' => getHashedPassword($newPassword), 'updatedBy' => $this->vendorId,
                    'updatedDtm' => date('Y-m-d H:i:s'));

                $result = $this->user_model->changePassword($this->vendorId, $usersData);

                if ($result > 0) {
                    $this->session->set_flashdata('success', 'パスワードの更新に成功しました。');
                } else {
                    $this->session->set_flashdata('error', 'パスワードの更新に失敗しました。');
                }

                redirect('profile/' . $active);
            }
        }
    }

    /**
     * This function is used to check whether email already exist or not
     * @param {string} $email : This is users email
     */
    function emailExists($email)
    {
        $userId = $this->vendorId;
        $return = false;

        if (empty($userId)) {
            $result = $this->user_model->checkEmailExists($email);
        } else {
            $result = $this->user_model->checkEmailExists($email, $userId);
        }

        if (empty($result)) {
            $return = true;
        } else {
            $this->form_validation->set_message('emailExists', 'The {field} already taken');
            $return = false;
        }

        return $return;
    }
}

?>

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Profile extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->load->model('Staff_model', 'staff_model');

        $this->header['page'] = 'profile';
        $this->header['title'] = '管理者情報変更';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        // ユーザーIDを安全に取得
        $user_id = null;
        if (isset($this->user['userId'])) {
            $user_id = $this->user['userId'];
        } elseif (isset($this->user['user_id'])) {
            $user_id = $this->user['user_id'];
        } elseif (isset($this->user['id'])) {
            $user_id = $this->user['id'];
        } elseif (isset($this->user['staff_id'])) {
            $user_id = $this->user['staff_id'];
        }

        $mode = $this->input->post('mode');
        if ($mode == 'save') {
            $this->form_validation->set_rules('user_name', '氏名', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('user_email', 'メールアドレス', 'trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('old_password', '現在のパスワード', 'required|max_length[20]');
            $this->form_validation->set_rules('new_password', '新しい パスワード', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('new_password_confirm', '新しい パスワード（確認）', 'trim|required|matches[new_password]|max_length[128]');

            $this->data['user'] = array(
                'userId' => $user_id,
                'name' => $this->input->post('user_name'),
                'email' => $this->input->post('user_email'),
                'old_password' => $this->input->post('old_password'),
                'new_password' => $this->input->post('new_password'),
            );

            if ($this->form_validation->run() === TRUE) {
                $old = $this->staff_model->getFromId($user_id);

                // パスワード検証（ハッシュ化されたパスワードと比較）
                $this->load->library('password_lib');
                if (!empty($old['password']) && $this->password_lib->verify($this->data['user']['old_password'], $old['password'])) {

                    $this->session->set_flashdata('nomatch', '');
                    $user_data = array(
                        'userId' => $user_id,
                        'email' => $this->input->post('user_email'),
                        'name' => $this->input->post('user_name'),
                        'password' => $this->password_lib->hash($this->input->post('new_password')),
                        'updatedBy' => $user_id,
                        'updatedDtm' => date('Y-m-d H:i:s')
                    );
                    $result = $this->staff_model->edit($user_data, 'userId');
                    if ($result) {
                        // セッション情報も更新
                        if (isset($this->user['name'])) {
                            $updated_user = $this->user;
                            $updated_user['name'] = $this->input->post('user_name');
                            $updated_user['email'] = $this->input->post('user_email');
                            if (isset($this->user['user_type']) && $this->user['user_type'] == 'admin') {
                                $this->session->set_userdata('staff', $updated_user);
                            } else {
                                $this->session->set_userdata('admin', $updated_user);
                            }
                        }

                        $this->session->set_flashdata('success', '正常に更新されました。');
                        $this->session->set_flashdata('error', '');
                    } else {
                        $this->session->set_flashdata('success', '');
                        $this->session->set_flashdata('error', '更新に失敗しました。');
                    }
                } else {
                    $this->session->set_flashdata('success', '');
                    $this->session->set_flashdata('error', '');
                    $this->session->set_flashdata('nomatch', '現在のパスワードが正しくありません。');
                }
            }
        } else {
            // ユーザー情報を取得
            if ($user_id) {
                $this->data["user"] = $this->staff_model->getFromId($user_id);
            } else {
                // フォールバック：セッションから直接取得
                $this->data["user"] = $this->user;
            }
        }

        $this->_load_view_admin("admin/profile");
    }
}

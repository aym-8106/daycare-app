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

        $this->load->model('admin_model');

        $this->header['page'] = 'profile';
        $this->header['title'] = '管理者情報変更';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        // 管理者IDを取得（user_typeがadminの場合はstaff_idが実際のadmin_id）
        $admin_id = isset($this->user['admin_id']) ? $this->user['admin_id'] : $this->user['staff_id'];

        $mode = $this->input->post('mode');
        if ($mode == 'save') {
            $this->form_validation->set_rules('admin_name', '管理者', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('admin_email', 'メールアドレス', 'trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('old_password', '現在のパスワード', 'required|max_length[20]');
            $this->form_validation->set_rules('new_password', '新しい パスワード', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('new_password_confirm', '新しい パスワード（確認）', 'trim|required|matches[new_password]|max_length[128]');

            $this->data['admin'] = array(
                'admin_id' => $admin_id,
                'admin_name' => $this->input->post('admin_name'),
                'admin_email' => $this->input->post('admin_email'),
                'old_password' => $this->input->post('old_password'),
                'new_password' => $this->input->post('new_password'),
            );

            if ($this->form_validation->run() === TRUE) {
                $old = $this->admin_model->get($admin_id);
                if (!empty($old['admin_password']) && $old['admin_password'] == sha1($this->data['admin']['old_password'])) {

                    $this->session->set_flashdata('nomatch', '');
                    $admin = array(
                        'admin_id' => $admin_id,
                        'admin_email' => $this->input->post('admin_email'),
                        'admin_name' => $this->input->post('admin_name'),
                        'admin_password' => sha1($this->input->post('new_password')),
                    );
                    $result = $this->admin_model->edit($admin);
                    if ($result) {
                        // セッション情報も更新（管理者がstaffセッションでログインしている場合）
                        if (isset($this->user['user_type']) && $this->user['user_type'] == 'admin') {
                            $updated_user = $this->user;
                            $updated_user['staff_name'] = $this->input->post('admin_name');
                            $this->session->set_userdata('staff', $updated_user);
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
            $this->data["admin"] = $this->admin_model->get($admin_id);
        }

        $this->_load_view_admin("admin/profile");
    }
}

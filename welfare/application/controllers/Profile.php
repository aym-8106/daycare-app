<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class Profile extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index($active = 'details')
    {
        $this->data["staff"] = $this->staff_model->get($this->user['staff_id']);
        $this->data["active"] = $active;

        $this->load->library('form_validation');

        $mode = $this->input->post('mode');
        if ($mode == 'save') {
            $this->form_validation->set_rules('staff_name', '名前', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('staff_mail_address', 'メールアドレス', 'trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('staff_password', 'パスワード', 'required|max_length[20]');
            $this->form_validation->set_rules('staff_password_confirm', 'パスワード（確認）', 'trim|required|matches[staff_password]|max_length[20]');

            $this->data['staff'] = array(
                'staff_id' => $this->user['staff_id'],
                'staff_name' => $this->input->post('staff_name'),
                'staff_mail_address' => $this->input->post('staff_mail_address'),
                'staff_password' => $this->input->post('staff_password'),
            );

            if ($this->form_validation->run() === TRUE) {
                if (!$this->emailExists($this->data['staff']['staff_mail_address'])) {

                    if ($this->data['staff']['staff_password']) {
                        $this->data['staff']['staff_password'] = sha1($this->data['staff']['staff_password']);
                    }

                    $result = $this->staff_model->update($this->data['staff']);
                    if ($result) {
                        $this->session->set_flashdata('success', '正常に更新されました。');
                        $this->session->set_flashdata('error', '');
                    } else {
                        $this->session->set_flashdata('success', '');
                        $this->session->set_flashdata('error', '更新に失敗しました。');
                    }
                } else {
                    $this->session->set_flashdata('error', '既に登録されているメールアドレスです。');
                }
            }
        }

        $this->_load_view("profile");
    }


    function profileUpdate($active = "details")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('staff_name', '名前', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('staff_mail_address', 'メールアドレス', 'trim|required|valid_email|max_length[128]');

        if ($this->form_validation->run() == FALSE) {
            $this->profile($active);
        } else {
            $name = $this->input->post('staff_name');
            $email = $this->input->post('staff_mail_address');

            $userInfo = array(
                'staff_name' => $name,
                'staff_mail_address' => $email,
                'staff_id' => $this->user['staff_id'],
            );

            $result = $this->staff_model->edit($userInfo, 'staff_id');

            if ($result == true) {
                $this->session->set_userdata('name', $name);
                $this->session->set_flashdata('success', 'プロフィールの更新に成功しました。');
            } else {
                $this->session->set_flashdata('error', 'プロフィールの更新に失敗しました。');
            }

            redirect('profile/index/' . $active);
        }
    }

    /**
     * This function is used to change the password of the user
     * @param text $active : This is flag to set the active tab
     */
    function changePassword($active = "changepass")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('oldPassword', '現在のパスワード', 'required|max_length[20]');
        $this->form_validation->set_rules('newPassword', '新しい パスワード', 'required|max_length[20]');
        $this->form_validation->set_rules('cNewPassword', '新しいパスワード（確認）', 'required|matches[newPassword]|max_length[20]');

        if ($this->form_validation->run() == FALSE) {
            $this->index($active);
        } else {
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');

            $resultPas = $this->staff_model->matchOldPassword($this->user['staff_id'], $oldPassword);

            if (empty($resultPas)) {
                $this->session->set_flashdata('nomatch', '現在のパスワードが正しくありません。');
                redirect('profile/index/' . $active);
            } else {
                $usersData = array(
                    'staff_password' => sha1($newPassword),
                    'update_date' => date('Y-m-d H:i:s')
                );

                $result = $this->staff_model->changePassword($this->user['staff_id'], $usersData);

                if ($result > 0) {
                    $this->session->set_flashdata('success', 'パスワードの更新に成功しました。');
                } else {
                    $this->session->set_flashdata('error', 'パスワードの更新に失敗しました。');
                }

                redirect('profile/index/' . $active);
            }
        }
    }


    /**
     * This function is used to check whether email already exist or not
     * @param {string} $email : This is users email
     */
    function emailExists($email)
    {
        $staff_id = $this->user['staff_id'];
        $return = false;

        if (empty($staff_id)) {
            $result = $this->staff_model->checkEmailExists($email);
        } else {
            $result = $this->staff_model->checkEmailExists($email, $staff_id);
        }

        if (empty($result)) {
            $return = true;
        } else {
            $this->form_validation->set_message('emailExists', '{field}はすでに存在します。');
            $return = false;
        }

        return $return;
    }
}

?>
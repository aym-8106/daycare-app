<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/CompanyController.php';

class Profile extends CompanyController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_COMPANY);

        $this->load->model('company_model');

        $this->header['page'] = 'profile';
        $this->header['title'] = '管理者情報変更';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        if ($mode == 'save') {
            $this->data['company'] = array(
                'company_id' => $this->user['company_id'],
                'company_name' => $this->input->post('company_name'),
                'company_email' => $this->input->post('company_email'),
                'old_password' => $this->input->post('old_password'),
                'new_password' => $this->input->post('new_password'),
                'shift_option1' => $this->input->post('shift_option1'),
                'shift_option2' => $this->input->post('shift_option2'),
                'shift_option3' => $this->input->post('shift_option3'),
                'shift_option4' => $this->input->post('shift_option4'),
                'shift_option5' => $this->input->post('shift_option5'),
                'shift_option6' => $this->input->post('shift_option6'),
            );

            $this->form_validation->set_rules('company_name', '管理者', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('company_email', 'メールアドレス', 'trim|required|valid_email|max_length[128]');
            if(!empty($this->input->post('old_password')) || !empty($this->input->post('new_password'))) {
                $this->form_validation->set_rules('old_password', '現在のパスワード', 'required|max_length[20]');
                $this->form_validation->set_rules('new_password', '新しい パスワード', 'trim|required|max_length[128]');
                $this->form_validation->set_rules('new_password_confirm', '新しい パスワード（確認）', 'trim|required|matches[new_password]|max_length[128]');
            }

            if ($this->form_validation->run() === TRUE) {
                $old = $this->company_model->get($this->user['company_id']);
                if (!empty($old['company_password']) && $old['company_password'] == sha1($this->data['company']['old_password'])) {

                    $this->session->set_flashdata('nomatch', '');
                    $company = array(
                        'company_id' => $this->user['company_id'],
                        'company_email' => $this->input->post('company_email'),
                        'company_name' => $this->input->post('company_name'),
                        'company_password' => sha1($this->input->post('new_password')),
                        'shift_option1' => $this->input->post('shift_option1'),
                        'shift_option2' => $this->input->post('shift_option2'),
                        'shift_option3' => $this->input->post('shift_option3'),
                        'shift_option4' => $this->input->post('shift_option4'),
                        'shift_option5' => $this->input->post('shift_option5'),
                        'shift_option6' => $this->input->post('shift_option6'),
                    );
                    $result = $this->company_model->edit($company);
                    if ($result) {
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
            $this->data["company"] = $this->company_model->get($this->user['company_id']);
        }

        $this->_load_view_company("company/profile");
    }
}

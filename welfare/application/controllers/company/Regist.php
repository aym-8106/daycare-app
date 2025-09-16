<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/CompanyController.php';

class Regist extends CompanyController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_GUEST);

        $this->load->model('Company_model', 'company_model');
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        if ($mode == 'save') {

            $this->data['company'] = array(
                'company_id' => NULL,
                'company_name' => $this->input->post('company_name'),
                'company_email' => $this->input->post('company_email'),
                'company_password' => $this->input->post('company_password'),
            );

            $this->form_validation->set_rules('company_name', '企業名', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('company_email', 'メールアドレス', 'trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('company_password', 'パスワード', 'required|max_length[20]');
            $this->form_validation->set_rules('company_password_confirm', 'パスワード（確認）', 'trim|required|matches[company_password]|max_length[20]');

            if ($this->form_validation->run() === TRUE) {

                if ($this->_check_email($this->data['company']['company_email'])) {
                    $this->data['company']['uuid'] = $this->_uuid();
                    $this->data['company']['company_password'] = sha1($this->data['company']['company_password']);
                    $this->data['company']['create_date'] = date('Y-m-d H:i:s');
                    $this->data['company']['update_date'] = date('Y-m-d H:i:s');

                    $this->data['company']['use_flag'] = 1;
                    $this->data['company']['payment_date'] = date('Y-m-d', strtotime(date('Y-m-d'). ' + 3 months'));
                    echo date('Y-m-d');
                    $result = $this->company_model->add($this->data['company']);
                    if ($result) {
                        $this->session->set_flashdata('success', '正常に登録されました。');
                        $this->session->set_flashdata('error', '');

                        $company = $this->company_model->getFromUUID($this->data['company']['uuid']);

                        redirect('company/payment');

                    } else {
                        $this->session->set_flashdata('success', '');
                        $this->session->set_flashdata('error', '更新に失敗しました。');
                    }
                } else {
                    $this->session->set_flashdata('error', '既に登録されているメールアドレスです。');
                }
            } else {
//                var_dump(validation_errors());;
            }

        }
        $this->load->view('company/regist', $this->data);
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
}
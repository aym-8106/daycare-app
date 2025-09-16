<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/CompanyController.php';

class Login extends CompanyController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->_login_check(ROLE_COMPANY)) {
            redirect('/company/staff');
        }
        $this->load->model('company_model');
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->data = array(
            'email' => $this->input->post('email'),
            'password' => $this->input->post('password'),
        );
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[255]|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[255]');

        if ($this->form_validation->run() === TRUE) {
            $company_user = $this->company_model->login($this->data);
            if (!empty($company_user)) {
                // Correctly set the session data
                $this->session->set_userdata('company', $company_user);
                
                // Add a flag to indicate the user is logged in
                $this->session->set_userdata('company_logged_in', TRUE);
                
                // Optionally set the user's role
                $this->session->set_userdata('user_role', ROLE_COMPANY);
                
                // Debug: Print the session data to verify it's being set
                // echo "<pre>"; print_r($this->session->userdata()); echo "</pre>"; exit;
                
                // Set a success flash message
                $this->session->set_flashdata('success', 'ログインに成功しました。');
                
                // Redirect to the intended page
                redirect('/company/payment');
                exit; // Make sure to exit after redirect
            } else {
                $this->session->set_flashdata('error', 'メールアドレスまたはパスワードが正しくありません。');
            }

        }
        $this->load->view('/company/login');
    }

}
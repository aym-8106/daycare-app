<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/UserController.php';

class Login extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Staff_model', 'staff_model');
        
        $this->load->model('Role_model', 'role_model');
        $this->load->model('Jobtype_model', 'jobtype_model');
        $this->load->model('Employtype_model', 'employtype_model');
        
        if ($this->_login_check(ROLE_STAFF)) {
            redirect('dashboard');
        }
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->data = array(
            // 'company_id' => $this->input->post('company_id'),
            // 'staff_id' => $this->input->post('staff_id'),
            'email' => $this->input->post('email'),
            'staff_password' => $this->input->post('password'),
            'remember' => $this->input->post('remember'),
        );

        $this->form_validation->set_rules('email', 'メールアドレス', 'required|max_length[255]');
        $this->form_validation->set_rules('password', 'パスワード', 'required|max_length[255]');

        if ($this->form_validation->run() === TRUE) {
            $user = $this->staff_model->login($this->data);

            if (!empty($user)) {
                // Correctly set the session data
                $this->session->set_userdata('staff', $user);
                
                // Add a flag to indicate the user is logged in
                $this->session->set_userdata('staff_logged_in', TRUE);
                
                // Optionally set the user's role
                $this->session->set_userdata('staff__role', ROLE_STAFF);
                
                // Debug: Print the session data to verify it's being set
                // echo "<pre>"; print_r($this->session->userdata()); echo "</pre>"; exit;
                
                // Set a success flash message
                $this->session->set_flashdata('success', 'ログインに成功しました。');
                
                // $sessionData = [
                //     'company_id' => $user['company_id'],
                //     'staff_id' => $user['staff_id'],
                //     'post' => $this->data,
                // ];
                // $loginInfo = array(
                //     'company_id' => $user['company_id'],
                //     "staff_id" => $user['staff_id'],
                //     "sessionData" => json_encode($sessionData),
                //     "machineIp" => $_SERVER['REMOTE_ADDR'],
                //     "userAgent" => getBrowserAgent(),
                //     "agentString" => $this->agent->agent_string(),
                //     "platform" => $this->agent->platform());

                // $this->staff_model->lastLogin($loginInfo);
                $redirect = $this->input->get("redirect");
                if(!empty($redirect)) {
                    redirect('/'.$redirect);
                }
                redirect('/dashboard');
            } else {
                // $staffs = $this->staff_model->getList('*', array('BaseTbl.company_id' => $this->data['company_id']), false, 0);
                // $this->data['staffs'] = $staffs;

                $this->session->set_flashdata('error', 'メールアドレスまたはパスワードが正しくありません。');
            }

        }
        
        $this->data['companys'] = $this->company_model->getList('*', array('use_flag' => true), false, 0);

        $this->load->view('/login', $this->data);
    }

    public function forgotPassword()
    {
        $company = $this->session->userdata('company');

        if (empty($company)) {
            $this->load->view('forgotPassword');
        } else {
            redirect('/dashboard');
        }
    }

    /**
     * This function used to generate reset password request link
     */
    function resetPasswordUser()
    {
        $status = '';

        $this->load->library('form_validation');

        $this->form_validation->set_rules('login_email', 'メールアドレス', 'trim|required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->forgotPassword();
        } else {
            $email = strtolower($this->security->xss_clean($this->input->post('login_email')));

            if ($this->company_model->checkEmailExists($email)) {
                $encoded_email = urlencode($email);

                $this->load->helper('string');
                $data['email'] = $email;
                $data['activation_id'] = random_string('alnum', 15);
                $data['createdDtm'] = date('Y-m-d H:i:s');
                $data['agent'] = getBrowserAgent();
                $data['client_ip'] = $this->input->ip_address();

                $save = $this->company_model->resetPasswordUser($data);

                if ($save) {
                    $data1['reset_link'] = base_url() . "resetPasswordConfirmUser/" . $data['activation_id'] . "/" . $encoded_email;
                    $userInfo = $this->company_model->getCustomerInfoByEmail($email);

                    if (!empty($userInfo)) {
                        $data1["name"] = $userInfo->name;
                        $data1["email"] = $userInfo->email;
                        $data1["message"] = "パスワードをリセット";
                    }

                    $sendStatus = resetPasswordEmail($data1);

                    if ($sendStatus) {
                        $status = "send";
                        setFlashData($status, "パスワードリセット用リンクのメールを送信しました。");
                    } else {
                        $status = "notsend";
                        setFlashData($status, "メールの送信に失敗しました。");
                    }
                } else {
                    $status = 'unable';
                    setFlashData($status, "送信中にエラーが発生しました。もう一度お試しください。");
                }
            } else {
                $status = 'invalid';
                setFlashData($status, "このメールは登録されていません。");
            }
            //登録されていない、または仮登録のメールアドレスです
            //仮登録の場合は別途登録メールにお送りしている「本登録のお願い」より、本登録の完了をお願いいたします
            redirect('/forgotPassword');
        }
    }

    /**
     * This function used to reset the password
     * @param string $activation_id : This is unique id
     * @param string $email : This is user email
     */
    function resetPasswordConfirmUser($activation_id, $email)
    {
        // Get email and activation code from URL values at index 3-4
        $email = urldecode($email);

        // Check activation id in database
        $is_correct = $this->login_model->checkActivationDetails($email, $activation_id);

        $data['email'] = $email;
        $data['activation_code'] = $activation_id;

        if ($is_correct == 1) {
            $this->load->view('newPassword', $data);
        } else {
            redirect('/login');
        }
    }

    /**
     * This function used to create new password for user
     */
    function createPasswordUser()
    {
        $status = '';
        $message = '';
        $email = strtolower($this->input->post("email"));
        $activation_id = $this->input->post("activation_code");

        $this->load->library('form_validation');

        $this->form_validation->set_rules('password', 'パスワード', 'required|max_length[20]');
        $this->form_validation->set_rules('cpassword', 'パスワード（確認）', 'trim|required|matches[password]|max_length[20]');

        if ($this->form_validation->run() == FALSE) {
            $this->resetPasswordConfirmUser($activation_id, urlencode($email));
        } else {
            $password = $this->input->post('password');
            $cpassword = $this->input->post('cpassword');

            // Check activation id in database
            $is_correct = $this->login_model->checkActivationDetails($email, $activation_id);

            if ($is_correct == 1) {
                $this->login_model->createPasswordUser($email, $password);

                $status = 'success';
                $message = 'パスワードが正常にリセットされました';
            } else {
                $status = 'error';
                $message = 'パスワードのリセットに失敗しました';
            }

            setFlashData($status, $message);

            redirect("/login");
        }
    }

    function getstaff($_id)
    {
        $staffs = $this->staff_model->getList('*', array('BaseTbl.company_id' => $_id), false, 0);

        $res = array(
            'ok' => true,
            'staffs' => $staffs,
            'cnt' => count($staffs),
        );

        return $this->output->set_output(json_encode($res));
    }
}
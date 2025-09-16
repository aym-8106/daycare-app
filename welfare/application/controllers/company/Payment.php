<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/CompanyController.php';

class Payment extends CompanyController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_COMPANY);

        $this->load->config('stripe');

        $this->load->model('setting_model');
        $this->load->model('pay_model');

        $this->header['page'] = 'payment';
        $this->header['title'] = '決済管理';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->data["company"] = $this->company_model->get($this->user['company_id']);
        
        $this->_load_view_company("company/payment");
    }
    
    public function confirm($checkout_session_id)
    {
        $url = "https://api.stripe.com/v1/checkout/sessions/" . $checkout_session_id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->config->item('stripe')['secret_key'],
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $session = json_decode($response);

            // Check the payment status
            $payment_status = $session->payment_status ?? 'unknown';
            $amount_total = $session->amount_total ?? 0;

            if($payment_status == 'paid') {
                $this->_save_pay($amount_total);
            }
            
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => '支払い過程で問題が発生しました。<br>もう一度お試しください。',
                'payment_status' => '',
                'amount_total' => 0
            ]);
        }
        redirect('/company/payment');
    }

    public function _save_pay($amount) 
    {
        $payData = array(
            'company_id' => $this->user['company_id'],
            'pay_amount' => $amount,
            'pay_date' => date('Y-m-d H:i:s'),
            'create_date' => date('Y-m-d H:i:s'),
            'update_date' => date('Y-m-d H:i:s'),
        );
        $result = $this->pay_model->add($payData);

        $result1 = false;
        if ($result) {
            $current_date = date("Y-m-d");
            $payment_date = date("Y-m-d", strtotime($this->user['payment_date']));
            $payment_date = $current_date > $payment_date ? $current_date : $payment_date;

            if($amount == 9800 || $amount == 10780) {
                $payment_date = date("Y-m-d", strtotime($payment_date." 1 months"));
            } else if($amount == 117600 || $amount == 129360) {
                $payment_date = date("Y-m-d", strtotime($payment_date." 12 months"));
            }
            $company = array(
                'company_id' => $this->user['company_id'],
                'payment_date' => $payment_date,
                'update_date' => date('Y-m-d H:i:s'),
            );
            $result1 = $this->company_model->edit($company);
        }

        return $result1;
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
}
?>
